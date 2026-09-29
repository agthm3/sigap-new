<?php

namespace App\Http\Controllers;

use App\Models\Notulensi;
use App\Models\NotulensiPeserta;
use App\Models\SigapDaftarHadirKegiatan;
use App\Repositories\KinerjaRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use iio\libmergepdf\Merger;

class NotulensiController extends Controller
{
    public function __construct(private ?KinerjaRepository $kinerjaRepo = null) {}

    /**
     * Tampilkan daftar dokumen notulensi
     */
    public function index()
    {
        $notulensis = Notulensi::with('creator')
            ->latest()
            ->paginate(10);

        return view('dashboard.notulensi.index', compact('notulensis'));
    }

    /**
     * Form buat notulensi baru
     */
    public function create()
    {
        // 1. Ambil daftar kegiatan presensi dari SIGAP DAFTAR HADIR
        $kegiatanDaftarHadir = SigapDaftarHadirKegiatan::select('id', 'nama_kegiatan', 'hari_tanggal', 'tempat', 'waktu')
            ->latest()
            ->get();

        // 2. Ambil bukti kinerja untuk modal pemilihan foto
        $buktiKinerjaList = [];
        if ($this->kinerjaRepo) {
            $items = $this->kinerjaRepo->paginateForIndex([], 50);
            $buktiKinerjaList = collect($items->items())->map(function ($m) {
                $images = [];
                if (method_exists($m, 'media')) {
                    foreach ($m->media()->where('is_image', true)->get() as $media) {
                        $images[] = [
                            'path' => $media->path,
                            'url'  => $this->kinerjaRepo->fileUrl($media->path)
                        ];
                    }
                }
                if (empty($images) && $m->thumb_path) {
                    $images[] = [
                        'path' => $m->thumb_path,
                        'url'  => $this->kinerjaRepo->fileUrl($m->thumb_path)
                    ];
                }

                return [
                    'id'          => $m->id,
                    'title'       => $m->title,
                    'description' => $m->description,
                    'date'        => optional($m->activity_date)->toDateString(),
                    'images'      => $images,
                ];
            })->all();
        }

        return view('dashboard.notulensi.create', compact('kegiatanDaftarHadir', 'buktiKinerjaList'));
    }

    /**
     * API: Ambil data kegiatan & peserta dari SIGAP DAFTAR HADIR
     */
    public function getDaftarHadirData($kegiatanId)
    {
        $kegiatan = SigapDaftarHadirKegiatan::with(['peserta' => fn($q) => $q->orderBy('urutan_absen')])
            ->findOrFail($kegiatanId);

        $penandatangan = $kegiatan->penandatangan;

        return response()->json([
            'status'           => 'success',
            'judul'            => $kegiatan->nama_kegiatan,
            'hari_tanggal'     => $kegiatan->hari_tanggal,
            'tempat'           => $kegiatan->tempat,
            'waktu'            => $kegiatan->waktu,
            'nomor_surat'      => $kegiatan->nomor_surat,
            'pimpinan_nama'    => $penandatangan?->nama_lengkap ?? '',
            'pimpinan_jabatan' => $penandatangan?->jabatan ?? '',
            'pimpinan_pangkat' => $penandatangan?->pangkat ?? '',
            'pimpinan_nip'     => $penandatangan?->nip ?? '',
            'pesertas'         => $kegiatan->peserta->map(function ($p) {
                return [
                    'nama'        => $p->nama,
                    'instansi'    => $p->instansi,
                    'gender'      => $p->gender ?? 'L',
                    'nip_nohp'    => $p->no_hp ?? '-',
                    'email'       => $p->email ?? '-',
                    'paraf_image' => $p->ttd_path ? asset('storage/' . $p->ttd_path) : null,
                ];
            })
        ]);
    }

    /**
     * Simpan notulensi baru
     */
    public function store(Request $request)
    {
        // 1. Validasi input: Cukup judul_acara yang wajib agar notulis dapat langsung menyimpan draft cepat di lokasi
        $request->validate([
            'judul_acara'        => ['required', 'string', 'max:500'],
            'hari_tanggal'       => ['nullable', 'string', 'max:255'],
            'waktu'              => ['nullable', 'string', 'max:255'],
            'tempat'             => ['nullable', 'string', 'max:255'],
            'tanggal_notula'     => ['nullable', 'date'],
            'tanggal_surat'      => ['nullable', 'date'],
            'undangan_pdf'       => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Maks 5MB
            'daftar_hadir_pdf'   => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // Maks 5MB
            'pimpinan_ttd'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'dokumentasi.*'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        // Ambil input form selain field array dan file khusus
        $data = $request->except([
            'peserta', 
            'dokumentasi', 
            'kinerja_photos', 
            'notulis_ttd_base64', 
            'undangan_pdf', 
            'daftar_hadir_pdf', 
            'pimpinan_ttd'
        ]);

        $data['user_id'] = Auth::id();
        $data['status']  = $request->input('status', 'draft');

        // 2. Simpan Lampiran File PDF Surat Undangan (jika diunggah)
        if ($request->hasFile('undangan_pdf')) {
            $data['undangan_file_path'] = $request->file('undangan_pdf')->store('notulensi/undangan', 'public');
        }

        // 3. Simpan Lampiran File PDF Daftar Hadir Fisik (jika diunggah manual)
        if ($request->hasFile('daftar_hadir_pdf')) {
            $data['daftar_hadir_file_path'] = $request->file('daftar_hadir_pdf')->store('notulensi/daftar-hadir', 'public');
        }

        // 4. Penanganan Tanda Tangan Digital Notulis (Canvas / Lengket ke Profil User)
        if ($request->filled('notulis_ttd_base64')) {
            $base64 = $request->notulis_ttd_base64;

            // Jika tanda tangan merupakan goresan canvas baru (Base64 Data URL)
            if (str_starts_with($base64, 'data:image')) {
                $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
                $image = str_replace(' ', '+', $image);
                $fileName = 'signatures/user_' . Auth::id() . '_' . time() . '.png';

                Storage::disk('public')->put($fileName, base64_decode($image));
                
                // Simpan ke record notulensi ini
                $data['notulis_ttd_image'] = $fileName;

                // Simpan permanen ke akun profil pegawai yang sedang login
                Auth::user()->update([
                    'signature_pad' => $fileName
                ]);
            } else {
                // Jika memakai path TTD yang tersimpan sebelumnya
                $data['notulis_ttd_image'] = Auth::user()->signature_pad;
            }
        } elseif (Auth::user()->signature_pad) {
            // Fallback: Jika tidak diubah, gunakan TTD profil yang sudah ada
            $data['notulis_ttd_image'] = Auth::user()->signature_pad;
        }

        // 5. Simpan Scan TTD / Stempel Pimpinan (jika ada file pengganti custom)
        if ($request->hasFile('pimpinan_ttd')) {
            $data['pimpinan_ttd_image'] = $request->file('pimpinan_ttd')->store('notulensi/ttd-pimpinan', 'public');
        }

        // 6. Penanganan Foto Dokumentasi: Gabungan dari Pilihan SIGAP Kinerja + Upload Manual
        $semuaFoto = [];

        // Ambil foto yang dipilih dari galeri SIGAP Bukti Kinerja (berupa JSON array path)
        if ($request->filled('kinerja_photos')) {
            $kinerjaPaths = json_decode($request->kinerja_photos, true);
            if (is_array($kinerjaPaths)) {
                $semuaFoto = array_merge($semuaFoto, $kinerjaPaths);
            }
        }

        // Ambil foto hasil upload manual dari perangkat
        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $photo) {
                $semuaFoto[] = $photo->store('notulensi/dokumentasi', 'public');
            }
        }

        // Simpan array path foto (maksimal array unik) ke kolom JSON dokumentasi_foto
        $data['dokumentasi_foto'] = !empty($semuaFoto) ? array_values(array_unique($semuaFoto)) : null;

        // 7. Simpan Dokumen Notulensi ke Database
        $notulensi = Notulensi::create($data);

        // 8. Simpan Rincian Peserta Presensi (baik hasil kloning yang diedit maupun input manual)
        if ($request->has('peserta') && is_array($request->peserta)) {
            foreach ($request->peserta as $p) {
                if (!empty($p['nama'])) {
                    $notulensi->pesertas()->create([
                        'nama'        => $p['nama'],
                        'instansi'    => $p['instansi'] ?? null,
                        'gender'      => $p['gender'] ?? 'L',
                        'nip_nohp'    => $p['nip_nohp'] ?? null,
                        'email'       => $p['email'] ?? null,
                        'paraf_image' => $p['paraf_image'] ?? null,
                    ]);
                }
            }
        }

        return redirect()
            ->route('sigap-notulensi.show', $notulensi->id)
            ->with('success', 'Dokumen notulensi berhasil disimpan.');
    }

    /**
     * Tampilkan detail notulensi
     */
    public function show($id)
    {
        $notulensi = Notulensi::with(['pesertas', 'creator'])->findOrFail($id);
        return view('dashboard.notulensi.show', compact('notulensi'));
    }

    /**
     * Form edit notulensi
     */
    public function edit($id)
    {
        $notulensi = Notulensi::with('pesertas')->findOrFail($id);

        $kegiatanDaftarHadir = SigapDaftarHadirKegiatan::select('id', 'nama_kegiatan', 'hari_tanggal', 'tempat', 'waktu')
            ->latest()
            ->get();

        $buktiKinerjaList = [];
        if ($this->kinerjaRepo) {
            $items = $this->kinerjaRepo->paginateForIndex([], 50);
            $buktiKinerjaList = collect($items->items())->map(function ($m) {
                $images = [];
                if (method_exists($m, 'media')) {
                    foreach ($m->media()->where('is_image', true)->get() as $media) {
                        $images[] = [
                            'path' => $media->path,
                            'url'  => $this->kinerjaRepo->fileUrl($media->path)
                        ];
                    }
                }
                if (empty($images) && $m->thumb_path) {
                    $images[] = [
                        'path' => $m->thumb_path,
                        'url'  => $this->kinerjaRepo->fileUrl($m->thumb_path)
                    ];
                }

                return [
                    'id'          => $m->id,
                    'title'       => $m->title,
                    'description' => $m->description,
                    'date'        => optional($m->activity_date)->toDateString(),
                    'images'      => $images,
                ];
            })->all();
        }

        return view('dashboard.notulensi.edit', compact('notulensi', 'kegiatanDaftarHadir', 'buktiKinerjaList'));
    }

    /**
     * Update data notulensi
     */
    public function update(Request $request, $id)
    {
        $notulensi = Notulensi::findOrFail($id);

        $request->validate([
            'judul_acara'  => ['required', 'string', 'max:500'],
            'hari_tanggal' => ['nullable', 'string', 'max:255'],
            'waktu'        => ['nullable', 'string', 'max:255'],
            'tempat'       => ['nullable', 'string', 'max:255'],
        ]);

        $data = $request->except(['peserta', 'dokumentasi', 'kinerja_photos', 'existing_photos', 'notulis_ttd_base64']);

        // 1. TTD Notulis
        if ($request->filled('notulis_ttd_base64')) {
            $base64 = $request->notulis_ttd_base64;
            if (str_starts_with($base64, 'data:image')) {
                $image = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
                $image = str_replace(' ', '+', $image);
                $fileName = 'signatures/user_' . Auth::id() . '_' . time() . '.png';

                Storage::disk('public')->put($fileName, base64_decode($image));
                $data['notulis_ttd_image'] = $fileName;

                Auth::user()->update(['signature_pad' => $fileName]);
            }
        }

        // 2. TTD Pimpinan jika ada file baru
        if ($request->hasFile('pimpinan_ttd')) {
            if ($notulensi->pimpinan_ttd_image && Storage::disk('public')->exists($notulensi->pimpinan_ttd_image)) {
                Storage::disk('public')->delete($notulensi->pimpinan_ttd_image);
            }
            $data['pimpinan_ttd_image'] = $request->file('pimpinan_ttd')->store('notulensi/ttd-pimpinan', 'public');
        }

        // 3. Gabungan Foto
        $activePhotos = [];
        if ($request->filled('existing_photos')) {
            $parsed = json_decode($request->existing_photos, true);
            if (is_array($parsed)) {
                $activePhotos = $parsed;
            }
        }

        if ($request->filled('kinerja_photos')) {
            $parsedKinerja = json_decode($request->kinerja_photos, true);
            if (is_array($parsedKinerja)) {
                $activePhotos = array_merge($activePhotos, $parsedKinerja);
            }
        }

        if ($request->hasFile('dokumentasi')) {
            foreach ($request->file('dokumentasi') as $photo) {
                $activePhotos[] = $photo->store('notulensi/dokumentasi', 'public');
            }
        }
        $data['dokumentasi_foto'] = !empty($activePhotos) ? array_values(array_unique($activePhotos)) : null;

        $notulensi->update($data);

        // 4. Sinkronisasi Peserta Presensi
        if ($request->has('peserta')) {
            $notulensi->pesertas()->delete();
            foreach ($request->peserta as $p) {
                if (!empty($p['nama'])) {
                    $notulensi->pesertas()->create([
                        'nama'        => $p['nama'],
                        'instansi'    => $p['instansi'] ?? null,
                        'gender'      => $p['gender'] ?? 'L',
                        'nip_nohp'    => $p['nip_nohp'] ?? null,
                        'email'       => $p['email'] ?? null,
                        'paraf_image' => $p['paraf_image'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('sigap-notulensi.show', $notulensi->id)->with('success', 'Dokumen notulensi berhasil diperbarui!');
    }

    /**
     * Update status (draft / proses / selesai)
     */
    public function updateStatus(Request $request, $id)
    {
        $notulensi = Notulensi::findOrFail($id);
        $notulensi->update(['status' => $request->status]);

        return back()->with('success', 'Status dokumen berhasil diperbarui!');
    }

    /**
     * Hapus dokumen notulensi
     */
    public function destroy($id)
    {
        $notulensi = Notulensi::findOrFail($id);

        if ($notulensi->dokumentasi_foto) {
            foreach ($notulensi->dokumentasi_foto as $foto) {
                if (Storage::disk('public')->exists($foto)) {
                    Storage::disk('public')->delete($foto);
                }
            }
        }

        if ($notulensi->pimpinan_ttd_image && Storage::disk('public')->exists($notulensi->pimpinan_ttd_image)) {
            Storage::disk('public')->delete($notulensi->pimpinan_ttd_image);
        }

        $notulensi->pesertas()->delete();
        $notulensi->delete();

        return redirect()->route('sigap-notulensi.index')->with('success', 'Dokumen notulensi berhasil dihapus!');
    }

    /**
     * EXPORT PDF: Menggabungkan 4 Komponen dalam 1 Berkas Utuh
     */
    public function exportPdf($id)
    {
        $notulensi = Notulensi::with('pesertas')->findOrFail($id);

        // 1. Muat Logo Base64
        $logoPemkot = $this->loadLogoBase64('logo-pemkot.png');
        $logoBrida  = $this->loadLogoBase64('logo-brida.png');

        // 2. Muat TTD & Stempel Resmi Kaban
        $ttdKabanBase64 = null;
        if ($notulensi->pimpinan_ttd_image && Storage::disk('public')->exists($notulensi->pimpinan_ttd_image)) {
            $path = storage_path('app/public/' . $notulensi->pimpinan_ttd_image);
            $ttdKabanBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        } else {
            $ttdKabanBase64 = $this->loadAsetBase64('images/sertifikat/ttd-kaban.png');
        }

        // 3. Muat TTD Digital Notulis
        $ttdNotulisBase64 = null;
        if ($notulensi->notulis_ttd_image && Storage::disk('public')->exists($notulensi->notulis_ttd_image)) {
            $path = storage_path('app/public/' . $notulensi->notulis_ttd_image);
            $ttdNotulisBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($path));
        }

        // 4. Generate QR Verifikasi Digital yang Mengarah ke Link Export PDF Ini
        $verifikasiUrl = route('sigap-notulensi.export-pdf', $notulensi->id);
        $qrVerifikasi = base64_encode(
            QrCode::format('svg')->size(110)->margin(1)->generate($verifikasiUrl)
        );

        // 5. Cek Ketersediaan File PDF Eksternal yang Diunggah Pengguna
        $hasCustomUndanganPdf = !empty($notulensi->undangan_file_path) && Storage::disk('public')->exists($notulensi->undangan_file_path);
        $hasCustomDaftarHadirPdf = !empty($notulensi->daftar_hadir_file_path) && Storage::disk('public')->exists($notulensi->daftar_hadir_file_path);

        $filename = 'Notula_' . Str::slug($notulensi->judul_acara) . '_' . date('Ymd') . '.pdf';

        $sharedData = [
            'notulensi'        => $notulensi,
            'logoPemkot'       => $logoPemkot,
            'logoBrida'        => $logoBrida,
            'ttdKabanBase64'   => $ttdKabanBase64,
            'ttdNotulisBase64' => $ttdNotulisBase64,
            'qrVerifikasi'     => $qrVerifikasi,
            'verifikasiUrl'    => $verifikasiUrl,
        ];

        // SKENARIO A: Tidak ada satupun file PDF yang diunggah (murni render template default HTML)
        if (!$hasCustomUndanganPdf && !$hasCustomDaftarHadirPdf) {
            $pdf = Pdf::loadView('dashboard.notulensi.pdf', array_merge($sharedData, [
                'renderSection' => 'all'
            ]))->setPaper('letter', 'portrait');

            return $pdf->stream($filename);
        }

        // SKENARIO B: Menggabungkan PDF sesuai posisi urutan dokumen resmi
        try {
            $merger = new \iio\libmergepdf\Merger();

            // 1. URUTAN 1: SAMPUL RESMI (COVER)
            $pdfCover = Pdf::loadView('dashboard.notulensi.pdf', array_merge($sharedData, [
                'renderSection' => 'cover'
            ]))->setPaper('letter', 'portrait')->output();
            $merger->addRaw($pdfCover);

            // 2. URUTAN 2: SURAT PENGANTAR / UNDANGAN
            if ($hasCustomUndanganPdf) {
                $merger->addFile(storage_path('app/public/' . $notulensi->undangan_file_path));
            } else {
                $pdfUndangan = Pdf::loadView('dashboard.notulensi.pdf', array_merge($sharedData, [
                    'renderSection' => 'undangan'
                ]))->setPaper('letter', 'portrait')->output();
                $merger->addRaw($pdfUndangan);
            }

            // 3. URUTAN 3: LEMBAR NOTULA RAPAT
            $pdfNotula = Pdf::loadView('dashboard.notulensi.pdf', array_merge($sharedData, [
                'renderSection' => 'notula'
            ]))->setPaper('letter', 'portrait')->output();
            $merger->addRaw($pdfNotula);

            // 4. URUTAN 4: LEMBAR DAFTAR HADIR
            if ($hasCustomDaftarHadirPdf) {
                $merger->addFile(storage_path('app/public/' . $notulensi->daftar_hadir_file_path));
            } else {
                $pdfDaftarHadir = Pdf::loadView('dashboard.notulensi.pdf', array_merge($sharedData, [
                    'renderSection' => 'hadir'
                ]))->setPaper('letter', 'portrait')->output();
                $merger->addRaw($pdfDaftarHadir);
            }

            // 5. URUTAN 5: DOKUMENTASI KEGIATAN (FOTO GRID 2X2)
            $pdfDokumentasi = Pdf::loadView('dashboard.notulensi.pdf', array_merge($sharedData, [
                'renderSection' => 'dokumentasi'
            ]))->setPaper('letter', 'portrait')->output();
            $merger->addRaw($pdfDokumentasi);

            $mergedPdfContent = $merger->merge();

            return response($mergedPdfContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);

        } catch (\Throwable $e) {
            // Deteksi jika penyebabnya adalah kompresi objek FPDI atau error struktur berkas PDF
            $isFpdiCompression = str_contains($e->getMessage(), 'compression technique') 
                || str_contains($e->getMessage(), 'CrossReference') 
                || str_contains($e->getMessage(), 'parser shipped with FPDI');

            $pesanHtml = '<strong>Format Kompresi PDF Lampiran Tidak Didukung!</strong><br><br>' .
                         'Berkas PDF (Surat Undangan atau Daftar Hadir) yang Anda lampirkan menggunakan format kompresi modern (PDF 1.5+). Sistem arsip membutuhkan berkas dengan standar <b>PDF Versi 1.4</b>.<br><br>' .
                         'Silakan lakukan konversi berkas PDF Anda secara gratis melalui tautan berikut:<br>' .
                         '<a href="https://www.allfiletools.com/pdf-version-converter/" target="_blank" style="color: #0284c7; text-decoration: underline; font-weight: bold; display: inline-block; margin-top: 8px;">🔗 Buka PDF Version Converter (Pilih Versi 1.4)</a><br><br>' .
                         '<span style="font-size: 11px; color: #64748b;">Setelah dikonversi ke PDF 1.4, silakan edit dan unggah ulang berkas tersebut.</span>';

            // Catat log untuk kebutuhan debugging teknis
            \Illuminate\Support\Facades\Log::error('Gagal export PDF Notulensi ID ' . $id . ': ' . $e->getMessage());

            // Arahkan kembali dengan flash message SweetAlert
            return back()->with('swal_error_html', $pesanHtml);
        }
    }

    /**
     * Helper muat logo instansi ke base64 (Aman untuk dompdf)
     */
    private function loadLogoBase64(string $filename): ?string
    {
        $candidates = [
            base_path('../public_html/images/' . $filename),
            '/home/sigap/public_html/images/' . $filename,
            public_path('images/' . $filename),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && is_readable($path)) {
                $content = @file_get_contents($path);
                if ($content !== false && $content !== '') {
                    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $mime = match ($ext) {
                        'jpg', 'jpeg' => 'image/jpeg',
                        'svg'         => 'image/svg+xml',
                        'gif'         => 'image/gif',
                        default       => 'image/png',
                    };
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        }

        return null;
    }

    /**
     * Helper muat file aset lokal publik ke base64
     */
    private function loadAsetBase64(string $relativePath): ?string
    {
        $candidates = [
            public_path($relativePath),
            base_path('../public_html/' . $relativePath),
            '/home/sigap/public_html/' . $relativePath,
        ];

        foreach ($candidates as $path) {
            if (file_exists($path) && is_readable($path)) {
                $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }

        return null;
    }
}