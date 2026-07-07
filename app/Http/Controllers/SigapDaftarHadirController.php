<?php

namespace App\Http\Controllers;

use App\Models\SigapDaftarHadirKegiatan;
use App\Models\SigapDaftarHadirPejabat;
use App\Models\SigapDaftarHadirPenandatangan;
use App\Models\SigapDaftarHadirPeserta;
use App\Models\SertifikatKegiatan;
use App\Models\SertifikatPeserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use iio\libmergepdf\Merger;

class SigapDaftarHadirController extends Controller
{
    // =========================================================================
    // DASHBOARD — KEGIATAN
    // =========================================================================

    public function index(Request $request)     
    {
        $user = Auth::user();
        $base = SigapDaftarHadirKegiatan::query();

        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            $base->where('created_by', $user->id);
        }

        if ($request->filled('q')) {
            $keyword = trim($request->get('q'));
            $base->where('nama_kegiatan', 'like', "%{$keyword}%");
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            $base->where('status', $status);
        }

        $totalKegiatan = (clone $base)->count();
        $totalDraft    = (clone $base)->where('status', 'draft')->count();
        $totalProses   = (clone $base)->where('status', 'proses')->count();
        $totalSelesai  = (clone $base)->where('status', 'selesai')->count();

        $kegiatans = (clone $base)
            ->with(['creator'])
            ->withCount('peserta')
            ->latest()
            ->paginate(10);

        return view('dashboard.daftar_hadir.index', compact(
            'kegiatans',
            'totalKegiatan',
            'totalDraft',
            'totalProses',
            'totalSelesai'
        ));
    }

    public function create()
    {
        return view('dashboard.daftar_hadir.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan'        => ['required', 'string', 'max:500'],
            'hari_tanggal'         => ['required', 'string', 'max:255'],
            'tempat'               => ['required', 'string', 'max:255'],
            'waktu'                => ['required', 'string', 'max:255'],
            'undangan_pdf'  => [
                'nullable', 
                'file', 
                'mimes:pdf', 
                'max:5120', // Max 5MB
                function ($attribute, $value, $fail) {
                    // Buka file PDF dan baca 15 karakter pertama di baris paling atas
                    $handle = fopen($value->getRealPath(), 'r');
                    $firstLine = fgets($handle, 15);
                    fclose($handle);

                    // Baris pertama PDF selalu berisi versinya, contoh: %PDF-1.4 atau %PDF-1.7
                    preg_match('/%PDF-(\d\.\d)/', $firstLine, $matches);
                    
                    if (isset($matches[1])) {
                        $version = (float) $matches[1];
                        
                        // Jika versi PDF di atas 1.4 (Canva/iLovePDF biasanya 1.5 - 1.7)
                        if ($version > 1.4) {
                            $fail('File PDF ditolak (Terdeteksi versi PDF ' . $version . '). Sistem hanya mendukung PDF versi 1.4 kebawah. Silakan buka file tersebut di browser (Chrome/Edge), tekan Ctrl+P, lalu pilih "Save as PDF" sebelum mengunggahnya kembali.');
                        }
                    }
                }
            ],
            'buat_sertifikat'      => ['nullable'],  
            'pejabat.nama_lengkap' => ['nullable', 'string', 'max:255'],
            'pejabat.jabatan'      => ['nullable', 'string', 'max:255'],
            'pejabat.pangkat'      => ['nullable', 'string', 'max:255'],
            'pejabat.golongan'     => ['nullable', 'string', 'max:20'],
            'pejabat.nip'          => ['nullable', 'string', 'max:30'],
            'pejabat.tempat_ttd'   => ['nullable', 'string', 'max:255'],
            'pejabat.tanggal_ttd'  => ['nullable', 'string', 'max:255'],
            'nomor_surat'          => [
                'nullable', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->has('buat_sertifikat')) {
                        $query = SigapDaftarHadirKegiatan::where('nomor_surat', trim($value))
                            ->where('buat_sertifikat', 1);
                        if ($query->exists()) {
                            $fail('Nomor Surat/Undangan ini sudah digunakan pada kegiatan sertifikat lain. Silakan gunakan nomor yang berbeda agar tidak terjadi duplikasi nomor sertifikat.');
                        }
                    }
                }
            ],
        ]);

        $undanganPath = null;
        if ($request->hasFile('undangan_pdf')) {
            $undanganPath = $request->file('undangan_pdf')->store('sigap/daftar-hadir/undangan', 'public');
        }

        DB::transaction(function () use ($request, &$kegiatan, $undanganPath) {
            $kegiatan = SigapDaftarHadirKegiatan::create([
                'uuid'            => (string) Str::uuid(),
                'nama_kegiatan'   => $request->nama_kegiatan,
                'hari_tanggal'    => $request->hari_tanggal,
                'tempat'          => $request->tempat,
                'waktu'           => $request->waktu,
                'status'          => 'draft',
                'created_by'      => Auth::id(),
                'undangan_path'   => $undanganPath,
                'buat_sertifikat' => $request->has('buat_sertifikat') ? 1 : 0,
                'nomor_surat'     => $request->input('nomor_surat'),
            ]);

            $pejabatInput = $request->input('pejabat', []);
            if (!empty($pejabatInput['nama_lengkap'])) {
                $this->upsertPenandatangan($kegiatan, $pejabatInput);
            }
        });

        return redirect()
            ->route('sigap-daftar-hadir.show', $kegiatan->uuid)
            ->with('success', 'Kegiatan daftar hadir berhasil dibuat.');
    }

    public function show(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $kegiatan->load([
            'creator',
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        $qrUrl = route('sigap-daftar-hadir.public', $kegiatan->uuid);
        return view('dashboard.daftar_hadir.show', compact('kegiatan', 'qrUrl'));
    }

    public function edit(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        $kegiatan->load([
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        return view('dashboard.daftar_hadir.edit', compact('kegiatan'));
    }

public function update(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();

        // 1. Validasi Hak Akses (Hanya Admin, Verifikator, atau Pembuat Kegiatan)
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses ke kegiatan ini.');
        }

        // 2. Validasi Input Data Form
        $request->validate([
            'nama_kegiatan'         => ['required', 'string', 'max:500'],
            'hari_tanggal'          => ['required', 'string', 'max:255'],
            'tempat'                => ['required', 'string', 'max:255'],
            'waktu'                 => ['required', 'string', 'max:255'],
            'buat_sertifikat'       => ['nullable'],
            'hapus_undangan'        => ['nullable', 'in:1'],
            
            // VALIDASI FILE: Hanya menerima versi PDF 1.4 ke bawah (Anti-Crash Merger)
            'undangan_pdf'          => [
                'nullable', 
                'file', 
                'mimes:pdf', 
                'max:5120', 
                function ($attribute, $value, $fail) {
                    $handle = fopen($value->getRealPath(), 'r');
                    $firstLine = fgets($handle, 15);
                    fclose($handle);

                    preg_match('/%PDF-(\d\.\d)/', $firstLine, $matches);
                    if (isset($matches[1])) {
                        $version = (float) $matches[1];
                        if ($version > 1.4) {
                            $fail('File PDF ditolak (Terdeteksi versi PDF ' . $version . '). Sistem hanya mendukung PDF versi 1.4 kebawah. Silakan buka file tersebut di browser (Chrome/Edge), tekan Ctrl+P, lalu pilih "Save as PDF" sebelum mengunggahnya kembali.');
                        }
                    }
                }
            ],

            // VALIDASI ANTISIPASI DUPLIKAT NOMOR SURAT
            'nomor_surat'           => [
                'nullable', 
                'string', 
                'max:255',
                function ($attribute, $value, $fail) use ($request, $kegiatan) {
                    if ($request->has('buat_sertifikat')) {
                        $query = SigapDaftarHadirKegiatan::where('nomor_surat', trim($value))
                            ->where('buat_sertifikat', 1)
                            ->where('id', '!=', $kegiatan->id);
                        if ($query->exists()) {
                            $fail('Nomor Surat/Undangan ini sudah digunakan pada kegiatan sertifikat lain. Silakan gunakan nomor yang berbeda agar tidak terjadi duplikasi nomor sertifikat.');
                        }
                    }
                }
            ],
            
            // Validasi baris data peserta (jika ada)
            'peserta'               => ['sometimes', 'array'],
            'peserta.*.nama'        => ['required_with:peserta', 'string', 'max:255'],
            'peserta.*.instansi'    => ['required_with:peserta', 'string', 'max:255'],
            'peserta.*.gender'      => ['required_with:peserta', 'in:L,P'],
            'peserta.*.no_hp'       => ['required_with:peserta', 'string', 'max:30'],
            'peserta.*.email'       => ['nullable', 'email', 'max:255'],
            'peserta.*.urutan_absen'=> ['required_with:peserta', 'integer', 'min:1'],
            
            // Validasi data penandatangan (opsional)
            'pejabat.nama_lengkap'  => ['nullable', 'string', 'max:255'],
            'pejabat.jabatan'       => ['nullable', 'string', 'max:255'],
            'pejabat.pangkat'       => ['nullable', 'string', 'max:255'],
            'pejabat.golongan'      => ['nullable', 'string', 'max:20'],
            'pejabat.nip'           => ['nullable', 'string', 'max:30'],
            'pejabat.tempat_ttd'    => ['nullable', 'string', 'max:255'],
            'pejabat.tanggal_ttd'   => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $kegiatan) {
            
            // 3. Logika Penanganan File Undangan (Simpan / Ganti / Hapus)
            if ($request->has('hapus_undangan') && $request->hapus_undangan == '1') {
                // Proses Hapus: Musnahkan file dari storage dan set null di database
                if ($kegiatan->undangan_path && Storage::disk('public')->exists($kegiatan->undangan_path)) {
                    Storage::disk('public')->delete($kegiatan->undangan_path);
                }
                $kegiatan->undangan_path = null;
            } elseif ($request->hasFile('undangan_pdf')) {
                // Proses Ganti: Hapus yang lama, simpan yang baru
                if ($kegiatan->undangan_path && Storage::disk('public')->exists($kegiatan->undangan_path)) {
                    Storage::disk('public')->delete($kegiatan->undangan_path);
                }
                $kegiatan->undangan_path = $request->file('undangan_pdf')->store('sigap/daftar-hadir/undangan', 'public');
            }

            // 4. Set Nilai Mutasi Checkbox Sertifikat
            $kegiatan->buat_sertifikat = $request->has('buat_sertifikat') ? 1 : 0;

            // 5. Jalankan Perbaruan Informasi Utama Kegiatan
            $kegiatan->update([
                'nama_kegiatan' => $request->nama_kegiatan,
                'hari_tanggal'  => $request->hari_tanggal,
                'tempat'        => $request->tempat,
                'waktu'         => $request->waktu,
                'nomor_surat'   => $request->input('nomor_surat'),
            ]);

            // 6. Proses Update Data Massal Peserta Kegiatan
            $pesertaInput = collect($request->input('peserta', []));
            foreach ($pesertaInput as $id => $row) {
                $peserta = SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)
                    ->where('id', $id)
                    ->firstOrFail();

                $peserta->update([
                    'nama'         => $row['nama'],
                    'instansi'     => $row['instansi'],
                    'gender'       => $row['gender'],
                    'no_hp'        => $row['no_hp'],
                    'email'        => $row['email'] ?? null,
                    'urutan_absen' => (int) $row['urutan_absen'],
                ]);
            }

            // 7. Normalisasi / Re-order Urutan Nomor Absen Peserta
            $sorted = $kegiatan->peserta()->orderBy('urutan_absen')->orderBy('created_at')->get();
            $urut = 1;
            foreach ($sorted as $item) {
                $item->update(['urutan_absen' => $urut++]);
            }

            // 8. Logika Perbaruan / Pembersihan Data Pejabat Penandatangan
            $pejabatInput = $request->input('pejabat', []);
            if (!empty($pejabatInput['nama_lengkap'])) {
                $this->upsertPenandatangan($kegiatan, $pejabatInput);
            } else {
                $existing = $kegiatan->penandatangan;
                if ($existing) {
                    if ($existing->ttd_path && Storage::disk('public')->exists($existing->ttd_path)) {
                        Storage::disk('public')->delete($existing->ttd_path);
                    }
                    $existing->delete();
                }
            }
        });

        return redirect()
            ->route('sigap-daftar-hadir.show', $kegiatan->uuid)
            ->with('success', 'Kegiatan dan data peserta berhasil diperbarui.');
    }

    public function updateStatus(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $request->validate([
            'status' => ['required', 'in:draft,proses,selesai'],
        ]);

        $kegiatan->update(['status' => $request->status]);

        if ($request->status === 'selesai' && $kegiatan->buat_sertifikat == 1) {
            $sertifKegiatan = SertifikatKegiatan::firstOrCreate(
                [
                    'nama_kegiatan' => $kegiatan->nama_kegiatan,
                    'tanggal'       => $kegiatan->hari_tanggal,
                ],
                [
                    'jenis'      => 'Kegiatan Internal',
                    'keterangan' => 'Auto-generate dari Daftar Hadir: ' . $kegiatan->nama_kegiatan,
                    'status'     => 'Aktif'
                ]
            );

            foreach ($kegiatan->peserta as $p) {
                // Sekarang menyertakan $kegiatan->id agar terjamin unik total di database
                $nomorDinamis = $this->formatNomorSertifikat($kegiatan->nomor_surat, $p->urutan_absen, $kegiatan->id);
                
                SertifikatPeserta::updateOrCreate(
                    [
                        'kegiatan_id'   => $sertifKegiatan->id,
                        'nama_penerima' => $p->nama,
                    ],
                    [
                        'nomor_sertifikat' => $nomorDinamis,
                        'instansi'         => $p->instansi,
                    ]
                );
            }
        }

        return back()->with('success', 'Status kegiatan berhasil diperbarui.');
    }

    public function destroy(SigapDaftarHadirKegiatan $kegiatan)
    {
        DB::transaction(function () use ($kegiatan) {
            foreach ($kegiatan->peserta as $peserta) {
                if ($peserta->ttd_path && Storage::disk('public')->exists($peserta->ttd_path)) {
                    Storage::disk('public')->delete($peserta->ttd_path);
                }
            }
            $kegiatan->peserta()->delete();

            $penandatangan = $kegiatan->penandatangan;
            if ($penandatangan) {
                if ($penandatangan->ttd_path && Storage::disk('public')->exists($penandatangan->ttd_path)) {
                    Storage::disk('public')->delete($penandatangan->ttd_path);
                }
                $penandatangan->delete();
            }

            $kegiatan->delete();
        });

        return redirect()->route('sigap-daftar-hadir.index')->with('success', 'Kegiatan berhasil dihapus.');
    }

    // =========================================================================
    // RIWAYAT PESERTA
    // =========================================================================

    public function riwayatPeserta(Request $request)
    {
        $q       = trim((string) $request->get('q', ''));
        $results = collect();

        if ($q !== '') {
            $results = SigapDaftarHadirPeserta::with('kegiatan')
                ->where('nama', 'like', "%{$q}%")
                ->orderBy('nama')
                ->get()
                ->groupBy(fn ($p) => Str::lower(trim($p->nama)));
        }

        return view('dashboard.daftar_hadir.riwayat-peserta', compact('q', 'results'));
    }

    public function riwayatPesertaDetail(Request $request)
    {
        $nama = trim((string) $request->get('nama', ''));
        abort_if($nama === '', 400, 'Parameter nama diperlukan.');

        $pesertaList = SigapDaftarHadirPeserta::with('kegiatan')
            ->whereRaw('LOWER(nama) = ?', [Str::lower($nama)])
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.daftar_hadir.riwayat-peserta-detail', compact('nama', 'pesertaList'));
    }

    // =========================================================================
    // PUBLIC FORM — PESERTA
    // =========================================================================

    public function publicForm(SigapDaftarHadirKegiatan $kegiatan)
    {
        $kegiatan->loadCount('peserta');
        return view('dashboard.daftar_hadir.public-form', compact('kegiatan'));
    }

    public function searchPeserta(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') { return response()->json([]); }

        $items = SigapDaftarHadirPeserta::query()
            ->where('nama', 'like', "%{$q}%")
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->unique(fn ($item) => Str::lower(trim($item->nama)))
            ->values()
            ->map(fn ($item) => [
                'id'       => $item->id,
                'nama'     => $item->nama,
                'instansi' => $item->instansi,
                'gender'   => $item->gender,
                'no_hp'    => $item->no_hp,
                'email'    => $item->email,
                'ttd_path' => $item->ttd_path ? asset('storage/' . $item->ttd_path) : null,
            ]);

        return response()->json($items);
    }

    public function storePublic(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        if ($kegiatan->status === 'selesai') {
            return redirect()
                ->route('sigap-daftar-hadir.public', $kegiatan->uuid)
                ->with('error', 'Maaf, kegiatan ini sudah selesai dan tidak menerima peserta baru.');
        }

        $request->validate([
            'nama'              => ['required', 'string', 'max:255'],
            'instansi'          => ['required', 'string', 'max:255'],
            'gender'            => ['required', 'in:L,P'],
            'no_hp'             => ['required', 'string', 'max:30'],
            'email'             => ['nullable', 'email', 'max:255'],
            'ttd_data'          => ['nullable', 'string'],
            'existing_ttd_path' => ['nullable', 'string'],
        ]);

        $nama = trim($request->nama);
        $duplicate = SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)
            ->whereRaw('LOWER(nama) = ?', [Str::lower($nama)])
            ->exists();

        if ($duplicate) {
            return back()->withInput()->with('error', 'Nama tersebut sudah terdaftar pada kegiatan ini.');
        }

        $ttdPath = null;
        if ($request->filled('ttd_data')) {
            $ttdPath = $this->saveSignatureBase64($request->ttd_data, 'sigap/daftar-hadir/ttd/' . $kegiatan->id);
        } elseif ($request->filled('existing_ttd_path')) {
            $ttdPath = ltrim(str_replace(asset('storage/'), '', $request->existing_ttd_path), '/');
        }

        $nextOrder = (int) (SigapDaftarHadirPeserta::where('kegiatan_id', $kegiatan->id)->max('urutan_absen') ?? 0) + 1;

        SigapDaftarHadirPeserta::create([
            'kegiatan_id'  => $kegiatan->id,
            'nama'         => $nama,
            'instansi'     => $request->instansi,
            'gender'       => $request->gender,
            'no_hp'        => $request->no_hp,
            'email'        => $request->email,
            'ttd_path'     => $ttdPath,
            'urutan_absen' => $nextOrder,
            'created_by'   => null,
        ]);

        return redirect()
            ->route('sigap-daftar-hadir.public', $kegiatan->uuid)
            ->with('success_name', $nama)
            ->with('success_kegiatan', $kegiatan->nama_kegiatan);
    }

    public function publicFormPejabat(SigapDaftarHadirPenandatangan $penandatangan)
    {
        $penandatangan->load('kegiatan');
        return view('dashboard.daftar_hadir.public-form-pejabat', compact('penandatangan'));
    }

    public function storePublicPejabat(Request $request, SigapDaftarHadirPenandatangan $penandatangan)
    {
        if ($penandatangan->sudah_ttd) {
            return redirect()
                ->route('sigap-daftar-hadir.pejabat-form', $penandatangan->uuid)
                ->with('error', 'Tanda tangan sudah tersimpan untuk kegiatan ini.');
        }

        $request->validate([ 'ttd_data' => ['required', 'string'] ]);
        $ttdPath = $this->saveSignatureBase64($request->ttd_data, 'sigap/daftar-hadir/ttd-pejabat/' . $penandatangan->kegiatan_id);

        if (!$ttdPath) { return back()->with('error', 'Data TTD tidak valid.'); }

        if ($penandatangan->ttd_path && Storage::disk('public')->exists($penandatangan->ttd_path)) {
            Storage::disk('public')->delete($penandatangan->ttd_path);
        }

        $penandatangan->update([
            'ttd_path'  => $ttdPath,
            'signed_at' => now(),
        ]);

        if ($penandatangan->nip) {
            SigapDaftarHadirPejabat::updateOrCreate(
                ['nip' => $penandatangan->nip],
                [
                    'nama_lengkap' => $penandatangan->nama_lengkap,
                    'jabatan'      => $penandatangan->jabatan,
                    'pangkat'      => $penandatangan->pangkat,
                    'golongan'     => $penandatangan->golongan,
                ]
            );
        }

        return redirect()
            ->route('sigap-daftar-hadir.pejabat-form', $penandatangan->uuid)
            ->with('success', 'Tanda tangan berhasil disimpan. Terima kasih, ' . $penandatangan->nama_lengkap . '.');
    }

    public function searchPejabat(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if ($q === '') { return response()->json([]); }

        $items = SigapDaftarHadirPejabat::where('nama_lengkap', 'like', "%{$q}%")
            ->orWhere('nip', 'like', "%{$q}%")
            ->orWhere('jabatan', 'like', "%{$q}%")
            ->orderBy('nama_lengkap')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'id'           => $p->id,
                'nama_lengkap' => $p->nama_lengkap,
                'jabatan'      => $p->jabatan,
                'pangkat'      => $p->pangkat,
                'golongan'     => $p->golongan,
                'nip'          => $p->nip,
            ]);

        return response()->json($items);
    }

    public function verifikasi(SigapDaftarHadirKegiatan $kegiatan)
    {
        $kegiatan->load([
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);
        return view('dashboard.daftar_hadir.verifikasi', compact('kegiatan'));
    }

    // =========================================================================
    // EXPORT PDF
    // =========================================================================

    public function exportPdf(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
        }

        abort_unless($kegiatan->status === 'selesai', 403, 'PDF hanya bisa diexport saat kegiatan selesai.');

        $kegiatan->load([
            'penandatangan',
            'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
        ]);

        $logoPemkot = $this->loadLogoBase64('logo-pemkot.png');
        $logoBrida  = $this->loadLogoBase64('logo-brida.png');

        $verifikasiUrl = route('sigap-daftar-hadir.verifikasi', $kegiatan->uuid);
        $qrVerifikasi = base64_encode(
            QrCode::format('svg')->size(120)->margin(1)->generate($verifikasiUrl)
        );

        $pdfUtama = Pdf::loadView('dashboard.daftar_hadir.pdf', [
            'kegiatan'      => $kegiatan,
            'logoPemkot'    => $logoPemkot,
            'logoBrida'     => $logoBrida,
            'qrVerifikasi'  => $qrVerifikasi,
            'verifikasiUrl' => $verifikasiUrl,
        ])->setPaper('letter', 'portrait');

        try {
            $merger = new Merger();
            
            if ($kegiatan->undangan_path && Storage::disk('public')->exists($kegiatan->undangan_path)) {
                $merger->addFile(storage_path('app/public/' . $kegiatan->undangan_path));
            }
            
            $merger->addRaw($pdfUtama->output());

            if ($kegiatan->buat_sertifikat == 1) {
                $portalSertifikatUrl = 'https://sigap.brida.makassarkota.go.id/sertifikat';
                $qrSertifikatSvg = base64_encode(
                    QrCode::format('svg')->size(70)->margin(0)->generate($portalSertifikatUrl)
                );

                // Cari data master kegiatan sertifikat
                $sertifKegiatan = SertifikatKegiatan::where('nama_kegiatan', $kegiatan->nama_kegiatan)
                    ->where('tanggal', $kegiatan->hari_tanggal)
                    ->first();

                // Mapping mengambil nomor sertifikat sah asli yang tersimpan di database
                $kegiatan->peserta->transform(function ($p) use ($sertifKegiatan, $kegiatan) {
                    $nomorSah = null;
                    if ($sertifKegiatan) {
                        $nomorSah = SertifikatPeserta::where('kegiatan_id', $sertifKegiatan->id)
                            ->where('nama_penerima', $p->nama)
                            ->value('nomor_sertifikat');
                    }
                    if (!$nomorSah) {
                        $nomorSah = $this->formatNomorSertifikat($kegiatan->nomor_surat, $p->urutan_absen, $kegiatan->id);
                    }
                    $p->nomor_sertifikat_dinamis = $nomorSah;
                    return $p;
                });

                $pdfSertifikat = Pdf::loadView('dashboard.daftar_hadir.pdf_lampiran_sertifikat', [
                    'kegiatan'     => $kegiatan,
                    'logoPemkot'   => $logoPemkot,
                    'logoBrida'    => $logoBrida,
                    'qrSertifikat' => $qrSertifikatSvg
                ])->setPaper('letter', 'portrait');

                $merger->addRaw($pdfSertifikat->output());
            }

            $mergedPdf = $merger->merge();
            return response($mergedPdf)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="dokumen-lengkap-' . Str::slug($kegiatan->nama_kegiatan) . '.pdf"');
        
        } catch (\Exception $e) {
            return $pdfUtama->download('daftar-hadir-saja-' . Str::slug($kegiatan->nama_kegiatan) . '.pdf');
        }
    }

    public function printQr(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
        }

        $qrUrl        = route('sigap-daftar-hadir.public', $kegiatan->uuid);
        $instagramUrl = 'https://www.instagram.com/bridakotamakassar/';
        return view('dashboard.daftar_hadir.print-qr', compact('kegiatan', 'qrUrl', 'instagramUrl'));
    }

    public function printQrPejabat(SigapDaftarHadirKegiatan $kegiatan)
    {
        $user = Auth::user();
        if (!$user->hasAnyRole(['admin', 'verif_daftarhadir'])) {
            abort_unless((int) $kegiatan->created_by === (int) $user->id, 403, 'Anda tidak memiliki akses.');
        }

        $penandatangan = $kegiatan->penandatangan;
        abort_unless($penandatangan !== null, 404, 'Kegiatan ini tidak memiliki data penandatangan.');

        $qrPejabatUrl = route('sigap-daftar-hadir.pejabat-form', $penandatangan->uuid);
        return view('dashboard.daftar_hadir.print-qr-pejabat', compact('kegiatan', 'penandatangan', 'qrPejabatUrl'));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function upsertPenandatangan(SigapDaftarHadirKegiatan $kegiatan, array $input): void
    {
        $pejabatId = null;
        if (!empty($input['nip'])) {
            $master = SigapDaftarHadirPejabat::firstOrCreate(
                ['nip' => $input['nip']],
                [
                    'nama_lengkap' => $input['nama_lengkap'],
                    'jabatan'      => $input['jabatan']      ?? null,
                    'pangkat'      => $input['pangkat']      ?? null,
                    'golongan'     => $input['golongan']     ?? null,
                    'created_by'   => Auth::id(),
                ]
            );
            $master->update([
                'nama_lengkap' => $input['nama_lengkap'],
                'jabatan'      => $input['jabatan']      ?? $master->jabatan,
                'pangkat'      => $input['pangkat']      ?? $master->pangkat,
                'golongan'     => $input['golongan']     ?? $master->golongan,
            ]);
            $pejabatId = $master->id;
        }

        $existing = $kegiatan->penandatangan;
        if ($existing) {
            $existing->update([
                'pejabat_id'   => $pejabatId ?? $existing->pejabat_id,
                'nama_lengkap' => $input['nama_lengkap'],
                'jabatan'      => $input['jabatan']      ?? null,
                'pangkat'      => $input['pangkat']      ?? null,
                'golongan'     => $input['golongan']     ?? null,
                'nip'          => $input['nip']          ?? null,
                'tempat_ttd'   => $input['tempat_ttd']   ?? null,
                'tanggal_ttd'  => $input['tanggal_ttd']  ?? null,
            ]);
        } else {
            SigapDaftarHadirPenandatangan::create([
                'uuid'         => (string) Str::uuid(),
                'kegiatan_id'  => $kegiatan->id,
                'pejabat_id'   => $pejabatId,
                'nama_lengkap' => $input['nama_lengkap'],
                'jabatan'      => $input['jabatan']      ?? null,
                'pangkat'      => $input['pangkat']      ?? null,
                'golongan'     => $input['golongan']     ?? null,
                'nip'          => $input['nip']          ?? null,
                'tempat_ttd'   => $input['tempat_ttd']   ?? null,
                'tanggal_ttd'  => $input['tanggal_ttd']  ?? null,
            ]);
        }
    }

    private function saveSignatureBase64(string $data, string $folder): string
    {
        if (!Str::startsWith($data, 'data:image/')) { return ''; }
        [$meta, $content] = explode(',', $data, 2);

        $extension = 'png';
        if (Str::contains($meta, 'image/jpeg')) { $extension = 'jpg'; }

        $binary   = base64_decode($content);
        $fileName = Str::uuid() . '.' . $extension;
        $path     = trim($folder, '/') . '/' . $fileName;

        Storage::disk('public')->put($path, $binary);
        return $path;
    }

    private function formatNomorSertifikat(?string $nomorSurat, int $urutan, int $kegiatanId = 0): string
    {
        $paddedUrutan = str_pad($urutan, 2, '0', STR_PAD_LEFT);

        if (empty($nomorSurat)) {
            $suffix = 'SERTIF-' . $paddedUrutan;
            if ($kegiatanId > 0) { $suffix .= '-KG' . $kegiatanId; }
            return '000/' . $suffix . '/BRIDA/' . \Carbon\Carbon::now()->format('Y');
        }

        $nomorSurat = trim($nomorSurat);
        $parts = explode('/', $nomorSurat);
        
        if (count($parts) > 1) {
            $tahun = array_pop($parts);
            $prefix = implode('/', $parts);
            
            $suffixSertif = 'SERTIF-' . $paddedUrutan;
            if ($kegiatanId > 0) {
                $suffixSertif .= '-KG' . $kegiatanId;
            }
            return $prefix . '/' . $suffixSertif . '/' . $tahun;
        }

        return $nomorSurat . '/SERTIF-' . $paddedUrutan . ($kegiatanId > 0 ? '-KG' . $kegiatanId : '');
    }
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
}