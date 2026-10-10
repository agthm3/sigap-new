<?php

namespace App\Http\Controllers;

use App\Models\SiipidPrestasi;
use App\Models\SiipidReviewLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiipidReviewController extends Controller
{
    /**
     * Meja Kerja / Monitoring Reviewer SIIPID (Role: verif_siipid / admin)
     */
    public function index(Request $request)
    {
        $statusFilter = $request->input('status');
        $query = SiipidPrestasi::with(['user', 'innovable', 'reviewer']);

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Pencarian
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_inovator', 'like', "%{$search}%")
                  ->orWhere('nama_prestasi', 'like', "%{$search}%")
                  ->orWhere('ajang_kompetisi', 'like', "%{$search}%")
                  ->orWhere('opd_instansi', 'like', "%{$search}%");
            });
        }

        $prestasis = $query->latest()->paginate(15)->withQueryString();

        // Statistik Review
        $countDiajukan = SiipidPrestasi::where('status', 'diajukan')->count();
        $countPerbaikan = SiipidPrestasi::where('status', 'dikembalikan_perbaikan')->count();
        $countDirekomendasikan = SiipidPrestasi::where('status', 'direkomendasikan')->count();
        $countDitolak = SiipidPrestasi::where('status', 'ditolak')->count();
        $countDitetapkan = SiipidPrestasi::where('status', 'ditetapkan_sk')->count();

        return view('dashboard.siipid.verifikator.index', compact(
            'prestasis',
            'statusFilter',
            'countDiajukan',
            'countPerbaikan',
            'countDirekomendasikan',
            'countDitolak',
            'countDitetapkan'
        ));
    }

    /**
     * Halaman Telaah Berkas & Form Aksi Reviewer
     */
    public function show($uuid)
    {
        $prestasi = SiipidPrestasi::with(['user', 'innovable', 'reviewer', 'reviewLogs.reviewer'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return view('dashboard.siipid.verifikator.review', compact('prestasi'));
    }

    /**
     * Eksekusi Aksi Reviewer: Rekomendasikan / Kembalikan Perbaikan / Tolak
     */
    public function submitReview(Request $request, $id)
    {
        $reviewer = Auth::user();
        $prestasi = SiipidPrestasi::findOrFail($id);

        $request->validate([
            'action' => 'required|in:dikembalikan_perbaikan,ditolak,direkomendasikan',
            'catatan' => 'required|string|min:5',
            'is_published_public' => 'nullable|boolean',
        ]);

        $statusLama = $prestasi->status;
        $statusBaru = $request->action;

        // Update Usulan
        $prestasi->status = $statusBaru;
        $prestasi->catatan_review_terakhir = $request->catatan;
        $prestasi->reviewer_id = $reviewer->id;
        $prestasi->reviewed_at = now();

        // Jika direkomendasikan, tentukan visibilitas publik (default publish)
        if ($statusBaru === 'direkomendasikan') {
            $prestasi->is_published_public = $request->has('is_published_public') ? true : true;
        }

        $prestasi->save();

        // Catat ke log review
        SiipidReviewLog::create([
            'prestasi_id' => $prestasi->id,
            'reviewer_id' => $reviewer->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => $statusBaru,
            'catatan' => $request->catatan,
        ]);

        ActivityLogger::log('review', 'SIGAP SIIPID', "Reviewer {$reviewer->name} mengubah status usulan [{$prestasi->nama_prestasi}] menjadi: {$statusBaru}");

        $pesan = match ($statusBaru) {
            'dikembalikan_perbaikan' => 'Usulan berhasil dikembalikan kepada peserta untuk diperbaiki.',
            'ditolak' => 'Usulan berhasil ditolak.',
            'direkomendasikan' => 'Usulan berhasil direkomendasikan untuk penetapan penghargaan.',
        };

        return redirect()->route('sigap-siipid.verifikator.index')->with('success', $pesan);
    }

    /**
     * Penetapan SK Wali Kota (Khusus Admin)
     */
    public function penetapanSk(Request $request, $id)
    {
        $prestasi = SiipidPrestasi::findOrFail($id);

        $request->validate([
            'nomor_sk_walikota' => 'required|string|max:255',
            'tanggal_sk_walikota' => 'required|date',
            'file_sk_walikota' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $filePath = $prestasi->file_sk_walikota;
        if ($request->hasFile('file_sk_walikota')) {
            $filePath = $request->file('file_sk_walikota')->store('siipid/sk_walikota', 'public');
        }

        $prestasi->update([
            'status' => 'ditetapkan_sk',
            'nomor_sk_walikota' => $request->nomor_sk_walikota,
            'tanggal_sk_walikota' => $request->tanggal_sk_walikota,
            'file_sk_walikota' => $filePath,
            'is_published_public' => true,
        ]);

        ActivityLogger::log('update', 'SIGAP SIIPID', "Menetapkan SK Wali Kota untuk usulan: {$prestasi->nama_prestasi}");

        return back()->with('success', 'SK Wali Kota berhasil ditetapkan untuk usulan ini.');
    }
}
