<?php

namespace App\Http\Controllers;

use App\Models\SigapDaftarHadirKegiatan;
use App\Models\SigapNarasumberKesediaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SigapNarasumberController extends Controller
{
    // === DASHBOARD OPERATOR ===
    
    public function index()
    {
        $narasumbers = SigapNarasumberKesediaan::with('kegiatan')->latest()->paginate(10);
        return view('dashboard.narasumber.index', compact('narasumbers'));
    }

    // Menampilkan list kegiatan untuk dibuatkan QR-nya
    public function pilihKegiatan(Request $request)
    {
        $q = $request->get('q');
        $kegiatans = SigapDaftarHadirKegiatan::query()
            ->when($q, fn($query) => $query->where('nama_kegiatan', 'like', "%{$q}%"))
            ->latest()
            ->paginate(10);
            
        return view('dashboard.narasumber.pilih-kegiatan', compact('kegiatans', 'q'));
    }

    // Menampilkan halaman QR Code untuk dibagikan ke narasumber
    public function showQr(SigapDaftarHadirKegiatan $kegiatan)
    {
        $qrUrl = route('sigap-narasumber.public', $kegiatan->uuid);
        return view('dashboard.narasumber.show-qr', compact('kegiatan', 'qrUrl'));
    }

    // Hapus data narasumber
    public function destroy(SigapNarasumberKesediaan $kesediaan)
    {
        if ($kesediaan->ttd_path && Storage::disk('public')->exists($kesediaan->ttd_path)) {
            Storage::disk('public')->delete($kesediaan->ttd_path);
        }
        $kesediaan->delete();
        return back()->with('success', 'Data kesediaan narasumber berhasil dihapus.');
    }

    // Export PDF 2 Lembar
    public function exportPdf(SigapNarasumberKesediaan $kesediaan)
    {
        $kesediaan->load('kegiatan');
        
        $logoBrida = $this->loadLogoBase64('logo-brida.png'); // Menggunakan helper dari SigapDaftarHadirController jika tersedia

        $pdf = Pdf::loadView('dashboard.narasumber.pdf', [
            'data' => $kesediaan,
            'kegiatan' => $kesediaan->kegiatan,
            'logoBrida' => $logoBrida,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Kesediaan_Narasumber_' . Str::slug($kesediaan->nama_lengkap) . '.pdf');
    }

    // === PUBLIC FORM ===

    public function publicForm(SigapDaftarHadirKegiatan $kegiatan)
    {
        return view('dashboard.narasumber.public-form', compact('kegiatan'));
    }

    public function storePublic(Request $request, SigapDaftarHadirKegiatan $kegiatan)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'ttd_data' => 'required|string',
        ]);

        $ttdPath = $this->saveSignatureBase64($request->ttd_data, 'sigap/narasumber/ttd/' . $kegiatan->id);

        if (!$ttdPath) {
            return back()->with('error', 'Gagal memproses tanda tangan.');
        }

        SigapNarasumberKesediaan::create(array_merge(
            $request->except(['_token', 'ttd_data']),
            [
                'uuid' => (string) Str::uuid(),
                'kegiatan_id' => $kegiatan->id,
                'ttd_path' => $ttdPath,
                'signed_at' => now(),
            ]
        ));

        return redirect()->route('sigap-narasumber.public', $kegiatan->uuid)
            ->with('success_name', $request->nama_lengkap)
            ->with('success_kegiatan', $kegiatan->nama_kegiatan);
    }

    // Helper simpan canvas
    private function saveSignatureBase64(string $data, string $folder): string
    {
        if (!Str::startsWith($data, 'data:image/')) return '';
        [$meta, $content] = explode(',', $data, 2);
        $binary = base64_decode($content);
        $fileName = Str::uuid() . '.png';
        $path = trim($folder, '/') . '/' . $fileName;
        Storage::disk('public')->put($path, $binary);
        return $path;
    }

    // Helper konversi logo (Salin dari controller daftar hadir Anda)
    private function loadLogoBase64(string $filename): ?string
    {
        $path = public_path('images/' . $filename);
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $mime = mime_content_type($path);
            return 'data:' . $mime . ';base64,' . base64_encode($content);
        }
        return null;
    }
}