<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SpjSubKegiatan;
use App\Models\SpjKegiatan;
use App\Models\SpjGelombang;
use iio\libmergepdf\Merger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Models\SigapDaftarHadirKegiatan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SpjController extends Controller
{
    // 1. Halaman Index Laporan (Menampilkan semua Sub-Kegiatan)
public function index(Request $request)
    {
        // Ambil semua bidang untuk opsi dropdown filter
        $allBidangs = \App\Models\SpjBidang::orderBy('nama_bidang')->get();

        // Query dasar pencarian Sub-Kegiatan beserta relasinya
        $query = SpjSubKegiatan::with(['bidang', 'kegiatans.gelombangs']);

        // 1. Filter Pencarian Teks (Berdasarkan Nama Sub-Kegiatan)
        if ($request->filled('search')) {
            $query->where('nama_sub_kegiatan', 'like', '%' . $request->search . '%');
        }

        // 2. Filter Berdasarkan ID Bidang
        if ($request->filled('bidang_id')) {
            $query->where('spj_bidang_id', $request->bidang_id);
        }

        // 3. Filter Berdasarkan Status Kelengkapan KAK
        if ($request->filled('status_kak')) {
            if ($request->status_kak === 'terisi') {
                $query->whereNotNull('file_kak');
            } elseif ($request->status_kak === 'kosong') {
                $query->whereNull('file_kak');
            }
        }

        // Ambil data dengan pagination dan pertahankan parameter query string filter di URL
        $subKegiatans = $query->latest()->paginate(10)->withQueryString();

        return view('dashboard.spj.index', compact('subKegiatans', 'allBidangs'));
    }

    // 2. Halaman Detail Sub-Kegiatan (Upload KAK & Lihat Kegiatan)
    public function show($id)
    {
        $subKegiatan = SpjSubKegiatan::with(['bidang', 'kegiatans.gelombangs'])->findOrFail($id);
        return view('dashboard.spj.show', compact('subKegiatan'));
    }

    // 3. Upload File KAK
    public function uploadKak(Request $request, $id)
    {
        $request->validate(['file_kak' => 'required|mimes:pdf|max:5120']); // Maks 5MB
        $sub = SpjSubKegiatan::findOrFail($id);

        if ($request->hasFile('file_kak')) {
            if ($sub->file_kak) Storage::disk('public')->delete($sub->file_kak);
            $path = $request->file('file_kak')->store('spj/kak', 'public');
            $sub->update(['file_kak' => $path]);
        }

        return back()->with('success', 'KAK berhasil diunggah!');
    }

    // 4. Upload File SK Kegiatan
    public function uploadSk(Request $request, $id)
    {
        $request->validate([
            'file_sk_panpel' => 'nullable|mimes:pdf|max:5120',
            'file_sk_tenaga_ahli' => 'nullable|mimes:pdf|max:5120',
        ]);

        $kegiatan = SpjKegiatan::findOrFail($id);

        if ($request->hasFile('file_sk_panpel')) {
            if ($kegiatan->file_sk_panpel) Storage::disk('public')->delete($kegiatan->file_sk_panpel);
            $path = $request->file('file_sk_panpel')->store('spj/sk', 'public');
            $kegiatan->update(['file_sk_panpel' => $path]);
        }

        if ($request->hasFile('file_sk_tenaga_ahli')) {
            if ($kegiatan->file_sk_tenaga_ahli) Storage::disk('public')->delete($kegiatan->file_sk_tenaga_ahli);
            $path = $request->file('file_sk_tenaga_ahli')->store('spj/sk', 'public');
            $kegiatan->update(['file_sk_tenaga_ahli' => $path]);
        }

        return back()->with('success', 'SK berhasil diunggah!');
    }

    // 5. Halaman Kelola 10 Berkas Gelombang
    public function berkasGelombang($id)
    {
        $gelombang = SpjGelombang::with('kegiatan.subKegiatan')->findOrFail($id);
        return view('dashboard.spj.gelombang_berkas', compact('gelombang'));
    }

    // 6. Upload 10 Berkas Gelombang
    public function uploadBerkasGelombang(Request $request, $id)
    {
        $gelombang = SpjGelombang::findOrFail($id);
        $fields = [
            'file_sk_narasumber', 'file_sk_moderator', 'file_sp_narasumber', 
            'file_sp_moderator', 'file_sp_panitia', 'file_surat_undangan', 
            'file_daftar_hadir', 'file_notulensi', 'file_dokumentasi', 'file_materi'
        ];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                $request->validate([$field => 'mimes:pdf|max:5120']);
                if ($gelombang->$field) Storage::disk('public')->delete($gelombang->$field);
                
                $path = $request->file($field)->store('spj/gelombang', 'public');
                $gelombang->update([$field => $path]);
            }
        }

        return back()->with('success', 'Berkas gelombang berhasil diperbarui!');
    }

// 7. Generate Laporan Gabungan Utama (Tombol di Dashboard)
    public function generateReport($id)
    {
        try {
            // Panggil fungsi pembangun PDF terpadu yang sudah memiliki logic sampul gelombang
            $outputPdf = $this->buildMergedPdf($id);
            
            $subKegiatan = SpjSubKegiatan::findOrFail($id);
            $fileName = 'Laporan_SPJ_' . str_replace(' ', '_', $subKegiatan->nama_sub_kegiatan) . '.pdf';

            // Kembalikan file PDF langsung terunduh ke browser
            return response($outputPdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal menggabungkan PDF. Error: ' . $e->getMessage()
            ], 500);
        }
    }
// 1. Halaman Publik Preview Dokumen (Berdasarkan UUID)
    public function sharePage($uuid)
    {
        $subKegiatan = SpjSubKegiatan::with('bidang')->where('uuid', $uuid)->firstOrFail();
        return view('dashboard.spj.share', compact('subKegiatan'));
    }

    // 2. Fungsi Streaming PDF ke dalam iFrame (Berdasarkan UUID)
    public function streamReport($uuid)
    {
        try {
            $subKegiatan = SpjSubKegiatan::where('uuid', $uuid)->firstOrFail();
            $outputPdf = $this->buildMergedPdf($subKegiatan->id); // Tetap lempar ID Int ke pembangun PDF
            
            return response($outputPdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Preview_SPJ.pdf"'
            ]);
        } catch (\Exception $e) {
            return response('Gagal memuat dokumen: ' . $e->getMessage(), 500);
        }
    }

    // 3. Fungsi Download PDF Publik (Berdasarkan UUID)
    public function downloadPublicReport($uuid)
    {
        try {
            $subKegiatan = SpjSubKegiatan::where('uuid', $uuid)->firstOrFail();
            $outputPdf = $this->buildMergedPdf($subKegiatan->id);
            $fileName = 'Laporan_SPJ_' . str_replace(' ', '_', $subKegiatan->nama_sub_kegiatan) . '.pdf';
            
            return response($outputPdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    // 4. Private Function Pembangun PDF (Digunakan ulang oleh Stream & Download)
   private function buildMergedPdf($id)
    {
        // 1. Tarik data Sub-Kegiatan beserta seluruh relasi anak di bawahnya
        $subKegiatan = SpjSubKegiatan::with(['bidang', 'kegiatans.gelombangs'])->findOrFail($id);
        
        $merger = new Merger();
        $tempFiles = []; // Melacak file temporary (cover) untuk dibersihkan di akhir proses

        // Pastikan folder temp untuk SPJ tersedia di storage
        $spjTempDir = storage_path('app/public/spj');
        if (!is_dir($spjTempDir)) {
            @mkdir($spjTempDir, 0775, true);
        }

        // ==========================================
        // LEVEL 1: SUB-KEGIATAN (SAMPUL UTAMA & KAK)
        // ==========================================
        
        // Buat URL Share publik berbasis UUID Sub-Kegiatan
        $shareUrl = route('spj.public.share', $subKegiatan->uuid);
        
        // Generate QR Code berbentuk PNG Base64 untuk disematkan di cetakan cover utama
        $qrCodeCover = base64_encode(
            QrCode::format('svg')
                  ->size(120)
                  ->margin(1)
                  ->generate($shareUrl)
        );

        // Render HTML view cover_sub menjadi halaman PDF tunggal
        $coverSubPdf = Pdf::loadView('dashboard.spj.pdf.cover_sub', [
            'subKegiatan' => $subKegiatan,
            'qrCodeCover' => $qrCodeCover,
            'shareUrl'    => $shareUrl
        ])->output();
        
        $coverSubPath = storage_path('app/public/spj/temp_cover_sub_' . $id . '.pdf');
        file_put_contents($coverSubPath, $coverSubPdf);
        $merger->addFile($coverSubPath);
        $tempFiles[] = $coverSubPath;

        // Lampirkan file KAK (Jika operator sudah mengunggahnya)
        if ($subKegiatan->file_kak && Storage::disk('public')->exists($subKegiatan->file_kak)) {
            $merger->addFile(storage_path('app/public/' . $subKegiatan->file_kak));
        }

        // ==========================================
        // LEVEL 2: LOOPING KEGIATAN & DATA SK INDUK
        // ==========================================
        foreach ($subKegiatan->kegiatans as $keg) {
            
            // Render halaman sampul penyekat khusus Kegiatan
            $coverKegPdf = Pdf::loadView('dashboard.spj.pdf.cover_keg', compact('keg'))->output();
            $coverKegPath = storage_path('app/public/spj/temp_cover_keg_' . $keg->id . '.pdf');
            file_put_contents($coverKegPath, $coverKegPdf);
            $merger->addFile($coverKegPath);
            $tempFiles[] = $coverKegPath;

            // Lampirkan SK Panitia Pelaksana jika ada
            if ($keg->file_sk_panpel && Storage::disk('public')->exists($keg->file_sk_panpel)) {
                $merger->addFile(storage_path('app/public/' . $keg->file_sk_panpel));
            }
            
            // Lampirkan SK Tenaga Ahli jika ada
            if ($keg->file_sk_tenaga_ahli && Storage::disk('public')->exists($keg->file_sk_tenaga_ahli)) {
                $merger->addFile(storage_path('app/public/' . $keg->file_sk_tenaga_ahli));
            }

            // ==========================================
            // LEVEL 3: LOOPING GELOMBANG / ANGKATAN
            // ==========================================
            foreach ($keg->gelombangs as $gel) {
                
                // Render halaman sampul penyekat Rincian Pelaksanaan Gelombang (Tanggal, Waktu, Tempat)
                $coverGelPdf = Pdf::loadView('dashboard.spj.pdf.cover_gel', compact('gel', 'keg'))->output();
                $coverGelPath = storage_path('app/public/spj/temp_cover_gel_' . $gel->id . '.pdf');
                file_put_contents($coverGelPath, $coverGelPdf);
                $merger->addFile($coverGelPath);
                $tempFiles[] = $coverGelPath;

                // Urutan 10 Berkas Dokumen Gelombang yang wajib digabung
                $fields = [
                    'file_sk_narasumber', 
                    'file_sk_moderator', 
                    'file_sp_narasumber', 
                    'file_sp_moderator', 
                    'file_sp_panitia', 
                    'file_surat_undangan', 
                    'file_daftar_hadir', // Berkas hasil upload fisik / tarikan SIGAP Daftar Hadir
                    'file_notulensi', 
                    'file_dokumentasi', // Berkas hasil upload fisik / lembar F4 tarikan SIGAP Kinerja
                    'file_materi'
                ];
                
                // Iterasi penggabungan dokumen gelombang jika file-nya tersedia di local storage
                foreach ($fields as $field) {
                    if ($gel->$field && Storage::disk('public')->exists($gel->$field)) {
                        $merger->addFile(storage_path('app/public/' . $gel->$field));
                    }
                }
            }
        }

        // ==========================================
        // EKSEKUSI PENGGABUNGAN (MERGING CORE)
        // ==========================================
        $outputPdf = $merger->merge();

        // Bersihkan seluruh file temporary cover yang sempat tercipta agar storage server tidak penuh
        foreach ($tempFiles as $temp) {
            if (file_exists($temp)) {
                @unlink($temp);
            }
        }

        // Kembalikan data string binary PDF yang siap diunduh (attachment) atau di-stream (inline)
        return $outputPdf;
    }
    // ==========================================
    // INTEGRASI SIGAP DAFTAR HADIR
    // ==========================================

    public function searchDaftarHadir(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        
        // Query dasar: Hanya ambil kegiatan yang statusnya sudah 'selesai'
        $query = SigapDaftarHadirKegiatan::where('status', 'selesai');

        if ($q !== '') {
            // Jika ada keyword pencarian
            $query->where('nama_kegiatan', 'like', "%{$q}%");
            $items = $query->latest()->limit(10)->get();
        } else {
            // Jika pencarian kosong, ambil 5 DATA TERBARU otomatis
            $items = $query->latest()->limit(5)->get();
        }

        // Mapping data untuk dikirim ke JavaScript
        $formattedItems = $items->map(function($item) {
            return [
                'uuid' => $item->uuid,
                'nama_kegiatan' => $item->nama_kegiatan,
                'hari_tanggal' => $item->hari_tanggal,
                'peserta_count' => $item->peserta()->count()
            ];
        });

        return response()->json($formattedItems);
    }
    public function importDaftarHadir(Request $request, $id)
    {
        try {
            $gelombang = SpjGelombang::findOrFail($id);
            $dh_uuid = $request->dh_uuid;

            // 1. Ambil data Daftar Hadir yang dipilih
            $kegiatanDH = SigapDaftarHadirKegiatan::with([
                'penandatangan',
                'peserta' => fn ($q) => $q->orderBy('urutan_absen')->orderBy('created_at'),
            ])->where('uuid', $dh_uuid)->firstOrFail();

            // 2. Siapkan aset untuk PDF
            $logoPemkot = $this->loadLogoBase64('logo-pemkot.png');
            $logoBrida  = $this->loadLogoBase64('logo-brida.png');
            
            $verifikasiUrl = route('sigap-daftar-hadir.verifikasi', $kegiatanDH->uuid);
            $qrVerifikasi = base64_encode(
                QrCode::format('svg')->size(120)->margin(1)->generate($verifikasiUrl)
            );

            // 3. Render PDF di memori (menggunakan view bawaan SIGAP Daftar Hadir)
            $pdf = Pdf::loadView('dashboard.daftar_hadir.pdf', [
                'kegiatan'      => $kegiatanDH,
                'logoPemkot'    => $logoPemkot,
                'logoBrida'     => $logoBrida,
                'qrVerifikasi'  => $qrVerifikasi,
                'verifikasiUrl' => $verifikasiUrl,
            ])->setPaper('letter', 'portrait');

            $pdfContent = $pdf->output();

            // 4. Simpan ke Storage SPJ
            $fileName = 'daftar_hadir_imported_' . Str::random(10) . '.pdf';
            $path = 'spj/gelombang/' . $fileName;

            // Hapus file lama jika ada
            if ($gelombang->file_daftar_hadir && Storage::disk('public')->exists($gelombang->file_daftar_hadir)) {
                Storage::disk('public')->delete($gelombang->file_daftar_hadir);
            }

            Storage::disk('public')->put($path, $pdfContent);
            $gelombang->update(['file_daftar_hadir' => $path]);

            return response()->json(['success' => true, 'message' => 'Daftar Hadir berhasil ditarik dan di-generate menjadi PDF!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Helper yang disalin dari SigapDaftarHadirController agar render PDF berhasil
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
    // ==========================================
    // INTEGRASI SIGAP KINERJA (DOKUMENTASI)
    // ==========================================

    public function searchKinerja(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = DB::table('kinerjas');

        if ($q !== '') {
            $query->where('title', 'like', "%{$q}%");
            $items = $query->latest('activity_date')->limit(10)->get();
        } else {
            // Ambil 5 data terbaru jika pencarian kosong
            $items = $query->latest('activity_date')->limit(5)->get();
        }

        $formattedItems = $items->map(function($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'activity_date' => \Carbon\Carbon::parse($item->activity_date)->translatedFormat('d M Y'),
                'category' => $item->category
            ];
        });

        return response()->json($formattedItems);
    }

    public function importKinerja(Request $request, $id)
    {
        try {
            $gelombang = SpjGelombang::with('kegiatan')->findOrFail($id);
            $kinerjaId = $request->kinerja_id;

            $kinerja = DB::table('kinerjas')->where('id', $kinerjaId)->first();
            if (!$kinerja) throw new \Exception("Data kinerja tidak ditemukan.");

            // 1. Kumpulkan gambar dari tabel kinerja_media
            $mediaPaths = DB::table('kinerja_media')
                ->where('kinerja_id', $kinerjaId)
                ->where('is_image', 1)
                ->pluck('path')
                ->toArray();

            // Fallback: Jika media kosong tapi file utama adalah gambar
            if (empty($mediaPaths) && $kinerja->file_mime && str_starts_with(strtolower($kinerja->file_mime), 'image/')) {
                $mediaPaths[] = $kinerja->file_path;
            }

            if (empty($mediaPaths)) throw new \Exception("Tidak ada file foto pada data kinerja ini.");

            // 2. Ubah gambar fisik menjadi Base64 agar bisa dirender ke PDF
            $imagesBase64 = [];
            foreach ($mediaPaths as $path) {
                $fullPath = storage_path('app/public/' . $path);
                if (file_exists($fullPath)) {
                    $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($fullPath);
                    $imagesBase64[] = 'data:image/' . $ext . ';base64,' . base64_encode($data);
                }
            }

            if (empty($imagesBase64)) throw new \Exception("File foto fisik tidak ditemukan di server.");

            // 3. Render PDF Dokumentasi
            // Kertas F4 (Folio) memiliki dimensi sekitar 8.5 x 13 inci (612 x 936 point)
            $pdf = Pdf::loadView('dashboard.spj.pdf.dokumentasi', [
                'kegiatan' => $gelombang->kegiatan,
                'gelombang' => $gelombang,
                'images' => $imagesBase64
            ])->setPaper([0, 0, 612.00, 936.00], 'portrait');

            $pdfContent = $pdf->output();

            // 4. Simpan ke Storage SPJ
            $fileName = 'dokumentasi_imported_' . Str::random(10) . '.pdf';
            $savePath = 'spj/gelombang/' . $fileName;

            // Hapus file lama jika ada
            if ($gelombang->file_dokumentasi && Storage::disk('public')->exists($gelombang->file_dokumentasi)) {
                Storage::disk('public')->delete($gelombang->file_dokumentasi);
            }

            Storage::disk('public')->put($savePath, $pdfContent);
            $gelombang->update(['file_dokumentasi' => $savePath]);

            return response()->json(['success' => true, 'message' => 'Dokumentasi berhasil ditarik dan di-generate menjadi PDF!']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}