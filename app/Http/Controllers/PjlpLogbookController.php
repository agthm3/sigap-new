<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PjlpLogbook;
use App\Models\PjlpPeriode;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PjlpLogbookController extends Controller
{
    public function index(Request $request)
    {
        $authUser = Auth::user();
        // Cek apakah yang login adalah Admin / Superadmin / Verifikator
        $isAdminOrVerif = $authUser->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);
        $bulanTahun = $request->get('bulan_tahun', Carbon::now()->format('Y-m'));

        $pjlpUsers = collect();
        $targetUser = $authUser;

        if ($isAdminOrVerif) {
            // Ambil semua user yang memiliki role 'pjlp'
            $pjlpUsers = User::role('pjlp')->orderBy('name', 'asc')->get();

            // Jika ada request user_id terpilih dari dropdown
            if ($request->filled('user_id')) {
                $targetUser = $pjlpUsers->where('id', $request->user_id)->first() ?? $pjlpUsers->first() ?? $authUser;
            } else {
                // Default pilih PJLP pertama jika ada, jika belum ada PJLP pakai auth user
                $targetUser = $pjlpUsers->first() ?? $authUser;
            }
        }

        // 1. Ambil atau Buat Header Periode untuk TARGET USER (bukan auth user jika admin yang login)
        $periode = PjlpPeriode::firstOrCreate(
            ['user_id' => $targetUser->id, 'bulan_tahun' => $bulanTahun],
            ['status_laporan' => 'draft']
        );

        // 2. Generate Hari Kerja (Senin-Jumat) Jika Belum Ada
        if ($periode->logbooks()->count() === 0) {
            $startDate = Carbon::createFromFormat('Y-m', $bulanTahun)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $period = CarbonPeriod::create($startDate, $endDate);

            $namaHari = [
                0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa',
                3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'
            ];

            foreach ($period as $date) {
                if ($date->isWeekday()) {
                    PjlpLogbook::create([
                        'pjlp_periode_id' => $periode->id,
                        'tanggal' => $date->format('Y-m-d'),
                        'hari' => $namaHari[$date->dayOfWeek],
                        'status' => 'belum_diisi',
                        'created_by_user_id' => $authUser->id,
                    ]);
                }
            }
        }

        $logbooks = $periode->logbooks()->with(['createdBy', 'updatedBy'])->orderBy('tanggal', 'asc')->get();

        // 3. Kalkulasi Statistik Pengisian
        $totalHariKerja = $logbooks->count();
        $totalTerisi = $logbooks->whereNotIn('status', ['belum_diisi'])->count();
        $totalTerverifikasi = $logbooks->where('status', 'terverifikasi')->count();
        $hasDaftarGaji = !empty($periode->file_daftar_gaji);
        $isSiapExport = ($totalHariKerja > 0 && $totalTerisi === $totalHariKerja && $hasDaftarGaji);

        return view('dashboard.pjlp.index', compact(
            'targetUser',
            'pjlpUsers',
            'periode',
            'logbooks',
            'bulanTahun',
            'totalHariKerja',
            'totalTerisi',
            'totalTerverifikasi',
            'hasDaftarGaji',
            'isSiapExport',
            'isAdminOrVerif'
        ));
    }

    public function uploadDaftarGaji(Request $request, $periodeId)
    {
        $request->validate([
            'file_daftar_gaji' => 'required|file|mimes:pdf|max:5120', // Maksimal 5MB
        ]);

        $periode = PjlpPeriode::where('user_id', Auth::id())->findOrFail($periodeId);

        if ($periode->file_daftar_gaji && Storage::disk('public')->exists($periode->file_daftar_gaji)) {
            Storage::disk('public')->delete($periode->file_daftar_gaji);
        }

        $path = $request->file('file_daftar_gaji')->store('pjlp/daftar_gaji', 'public');
        $periode->update(['file_daftar_gaji' => $path]);

        return back()->with('success', 'Dokumen Daftar Gaji berhasil diunggah.');
    }

    public function updateLogbook(Request $request, $id)
    {
        $request->validate([
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'foto_evidence' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $authUser = Auth::user();
        $isAdminOrVerif = $authUser->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);

        // Cari logbook berdasarkan ID
        $logbook = PjlpLogbook::with('periode')->findOrFail($id);

        // Keamanan: Jika bukan admin/verif, pastikan logbook ini adalah milik user yang sedang login
        if (!$isAdminOrVerif && $logbook->periode->user_id !== $authUser->id) {
            abort(403, 'Anda tidak memiliki akses ke logbook ini.');
        }

        $data = [
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            // Jika PJLP yang mengisi -> 'diajukan', jika admin/verifikator yang mengisikan -> 'terverifikasi'
            'status' => $isAdminOrVerif ? 'terverifikasi' : 'diajukan',
            'updated_by_user_id' => $authUser->id,
        ];

        if ($request->hasFile('foto_evidence')) {
            if ($logbook->foto_evidence && Storage::disk('public')->exists($logbook->foto_evidence)) {
                Storage::disk('public')->delete($logbook->foto_evidence);
            }
            $data['foto_evidence'] = $request->file('foto_evidence')->store('pjlp/evidence', 'public');
        }

        $logbook->update($data);

        return back()->with('success', 'Evidence tanggal ' . $logbook->tanggal->format('d/m/Y') . ' berhasil disimpan.');
    }
        public function monitoring(Request $request)
    {
        $bulanTahun = $request->get('bulan_tahun', Carbon::now()->format('Y-m'));
        $search = $request->get('search');

        // Ambil seluruh user dengan role 'pjlp'
        $pjlpUsersQuery = User::role('pjlp');

        if ($search) {
            $pjlpUsersQuery->where('name', 'like', "%{$search}%");
        }

        $pjlpUsers = $pjlpUsersQuery->orderBy('name', 'asc')->get();

        // Hitung struktur hari kerja standar (Senin-Jumat) pada bulan tersebut
        $startDate = Carbon::createFromFormat('Y-m', $bulanTahun)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $totalHariKerja = 0;
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if ($date->isWeekday()) {
                $totalHariKerja++;
            }
        }

        // Ambil seluruh data periode & logbook terkait bulan ini
        $periodes = PjlpPeriode::with('logbooks')
            ->where('bulan_tahun', $bulanTahun)
            ->whereIn('user_id', $pjlpUsers->pluck('id'))
            ->get()
            ->keyBy('user_id');

        // Olah data matriks & statistik untuk monitoring
        $monitoringData = $pjlpUsers->map(function ($user) use ($periodes, $totalHariKerja, $bulanTahun) {
            $periode = $periodes->get($user->id);
            $logbooks = $periode ? $periode->logbooks : collect();

            $terisi = $logbooks->whereNotIn('status', ['belum_diisi'])->count();
            $terverifikasi = $logbooks->where('status', 'terverifikasi')->count();
            $menunggu = $logbooks->where('status', 'diajukan')->count();
            $ditolak = $logbooks->where('status', 'ditolak')->count();
            $hasGaji = $periode && !empty($periode->file_daftar_gaji);

            $persenProgress = $totalHariKerja > 0 ? round(($terisi / $totalHariKerja) * 100) : 0;
            $isLengkap = ($totalHariKerja > 0 && $terisi === $totalHariKerja && $hasGaji);

            return (object) [
                'user' => $user,
                'periode' => $periode,
                'total_hari' => $totalHariKerja,
                'terisi' => $terisi,
                'terverifikasi' => $terverifikasi,
                'menunggu' => $menunggu,
                'ditolak' => $ditolak,
                'has_gaji' => $hasGaji,
                'persen_progress' => $persenProgress,
                'is_lengkap' => $isLengkap,
            ];
        });

        // Statistik Ringkasan Atas
        $summary = [
            'total_pjlp' => $pjlpUsers->count(),
            'lengkap' => $monitoringData->where('is_lengkap', true)->count(),
            'belum_lengkap' => $monitoringData->where('is_lengkap', false)->count(),
            'total_menunggu' => $monitoringData->sum('menunggu'),
            'total_ditolak' => $monitoringData->sum('ditolak'),
            'total_terverifikasi' => $monitoringData->sum('terverifikasi'),
        ];

        return view('dashboard.pjlp.monitoring', compact(
            'monitoringData',
            'summary',
            'bulanTahun',
            'search',
            'totalHariKerja'
        ));
    }

    /**
     * Detail Logbook PJLP Tertentu (Verifikator/Admin dapat verifikasi/tolak/isikan atas nama)
     */
    public function showUserLogbook(Request $request, $userId)
    {
        $targetUser = User::role('pjlp')->findOrFail($userId);
        $bulanTahun = $request->get('bulan_tahun', Carbon::now()->format('Y-m'));

        // Buat atau ambil periode untuk PJLP target
        $periode = PjlpPeriode::firstOrCreate(
            ['user_id' => $targetUser->id, 'bulan_tahun' => $bulanTahun],
            ['status_laporan' => 'draft']
        );

        // Inisialisasi hari kerja jika belum terbentuk
        if ($periode->logbooks()->count() === 0) {
            $startDate = Carbon::createFromFormat('Y-m', $bulanTahun)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();
            $period = CarbonPeriod::create($startDate, $endDate);

            $namaHari = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];

            foreach ($period as $date) {
                if ($date->isWeekday()) {
                    PjlpLogbook::create([
                        'pjlp_periode_id' => $periode->id,
                        'tanggal' => $date->format('Y-m-d'),
                        'hari' => $namaHari[$date->dayOfWeek],
                        'status' => 'belum_diisi',
                        'created_by_user_id' => Auth::id(),
                    ]);
                }
            }
        }

        $logbooks = $periode->logbooks()->with(['createdBy', 'updatedBy'])->orderBy('tanggal', 'asc')->get();

        $totalHariKerja = $logbooks->count();
        $totalTerisi = $logbooks->whereNotIn('status', ['belum_diisi'])->count();
        $totalTerverifikasi = $logbooks->where('status', 'terverifikasi')->count();
        $hasDaftarGaji = !empty($periode->file_daftar_gaji);
        $isSiapExport = ($totalHariKerja > 0 && $totalTerisi === $totalHariKerja && $hasDaftarGaji);

        return view('dashboard.pjlp.show', compact(
            'targetUser',
            'periode',
            'logbooks',
            'bulanTahun',
            'totalHariKerja',
            'totalTerisi',
            'totalTerverifikasi',
            'hasDaftarGaji',
            'isSiapExport'
        ));
    }

    /**
     * Verifikasi atau Tolak Evidence Logbook Harian
     */
    public function verifyLogbook(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:terverifikasi,ditolak',
            'catatan_verifikator' => 'nullable|string|max:500',
        ]);

        if ($request->status === 'ditolak' && empty($request->catatan_verifikator)) {
            return back()->with('error', 'Wajib menyertakan alasan/catatan jika menolak evidence.');
        }

        $logbook = PjlpLogbook::findOrFail($id);
        $logbook->update([
            'status' => $request->status,
            'catatan_verifikator' => $request->status === 'terverifikasi' ? null : $request->catatan_verifikator,
            'updated_by_user_id' => Auth::id(),
        ]);

        $pesan = $request->status === 'terverifikasi' 
            ? 'Logbook tanggal ' . $logbook->tanggal->format('d/m/Y') . ' berhasil disetujui.' 
            : 'Logbook tanggal ' . $logbook->tanggal->format('d/m/Y') . ' ditolak.';

        return back()->with('success', $pesan);
    }

    /**
     * Admin / Verifikator Mengisikan atau Mengedit Logbook Atas Nama PJLP
     */
    public function updateByAdmin(Request $request, $id)
    {
        $request->validate([
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'foto_evidence' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $logbook = PjlpLogbook::findOrFail($id);

        $data = [
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'status' => 'terverifikasi', // Otomatis terverifikasi jika diinput oleh admin/verifikator
            'updated_by_user_id' => Auth::id(),
        ];

        if ($request->hasFile('foto_evidence')) {
            if ($logbook->foto_evidence && Storage::disk('public')->exists($logbook->foto_evidence)) {
                Storage::disk('public')->delete($logbook->foto_evidence);
            }
            $data['foto_evidence'] = $request->file('foto_evidence')->store('pjlp/evidence', 'public');
        }

        $logbook->update($data);

        return back()->with('success', 'Evidence berhasil diisikan atas nama PJLP.');
    }

    public function history(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', Carbon::now()->format('Y'));
        $filterUser = $request->get('user_id');

        $isAdminOrVerif = $user->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);

        // Query dasar periode dengan relasi user dan logbooks
        $periodesQuery = PjlpPeriode::with(['user', 'logbooks'])
            ->where('bulan_tahun', 'like', "{$tahun}-%")
            ->orderBy('bulan_tahun', 'desc');

        if ($isAdminOrVerif) {
            if ($filterUser) {
                $periodesQuery->where('user_id', $filterUser);
            }
            $pjlpUsers = User::role('pjlp')->orderBy('name', 'asc')->get();
        } else {
            // Jika login sebagai PJLP, hanya tampilkan histori miliknya
            $periodesQuery->where('user_id', $user->id);
            $pjlpUsers = collect();
        }

        $periodes = $periodesQuery->get()->map(function ($item) {
            $logbooks = $item->logbooks;
            $totalHari = $logbooks->count();
            $terisi = $logbooks->whereNotIn('status', ['belum_diisi'])->count();
            $terverifikasi = $logbooks->where('status', 'terverifikasi')->count();
            $ditolak = $logbooks->where('status', 'ditolak')->count();
            $hasGaji = !empty($item->file_daftar_gaji);
            $isLengkap = ($totalHari > 0 && $terisi === $totalHari && $hasGaji);

            return (object) [
                'id' => $item->id,
                'user' => $item->user,
                'bulan_tahun' => $item->bulan_tahun,
                'file_daftar_gaji' => $item->file_daftar_gaji,
                'total_hari' => $totalHari,
                'terisi' => $terisi,
                'terverifikasi' => $terverifikasi,
                'ditolak' => $ditolak,
                'has_gaji' => $hasGaji,
                'is_lengkap' => $isLengkap,
                'persen' => $totalHari > 0 ? round(($terisi / $totalHari) * 100) : 0,
            ];
        });

        return view('dashboard.pjlp.history', compact(
            'periodes',
            'pjlpUsers',
            'tahun',
            'filterUser',
            'isAdminOrVerif'
        ));
    }


    public function exportPdf($periodeId)
    {
        $authUser = Auth::user();
        $isAdminOrVerif = $authUser->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);

        $periodeQuery = PjlpPeriode::with(['user.profile', 'logbooks']);
        if (!$isAdminOrVerif) {
            $periodeQuery->where('user_id', $authUser->id);
        }

        $periode = $periodeQuery->findOrFail($periodeId);
        $user = $periode->user;
        $profile = $user->profile;
        $logbooks = $periode->logbooks()->orderBy('tanggal', 'asc')->get();

        // 1. Convert Pas Foto Profil User ke Base64
        $fotoProfilBase64 = null;
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            $path = Storage::disk('public')->path($user->profile_photo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $fotoProfilBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // 2. Convert Evidence Foto ke Base64 & Chunk per 6 item (3 Kiri, 3 Kanan per Halaman Letter)
        $logbooksProcessed = $logbooks->map(function ($item) {
            $item->foto_base64 = null;
            if ($item->foto_evidence && Storage::disk('public')->exists($item->foto_evidence)) {
                $path = Storage::disk('public')->path($item->foto_evidence);
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $item->foto_base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                }
            }
            return $item;
        });

        $logbookPages = $logbooksProcessed->chunk(6);

        // 3. Generate QR Code Verifikasi (Base64)
        $verifyUrl = url("/verifikasi-pjlp/{$periode->id}?hash=" . md5($periode->id . $user->id . $periode->bulan_tahun));
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode(
            QrCode::format('svg')->size(75)->errorCorrection('H')->generate($verifyUrl)
        );

        // Metadata Tanggal
        $firstDate = $logbooks->first() ? $logbooks->first()->tanggal->translatedFormat('d F Y') : '-';
        $lastDate = $logbooks->last() ? $logbooks->last()->tanggal->translatedFormat('d F Y') : '-';
        $namaBulanTahun = Carbon::createFromFormat('Y-m', $periode->bulan_tahun)->translatedFormat('F Y');

        // Total statistik
        $totalHariKerja = $logbooks->count();
        $totalTerverifikasi = $logbooks->where('status', 'terverifikasi')->count();

        // 4. Render Bagian 1: Cover & Data Diri Dasar Pegawai
        $pdfCover = Pdf::loadView('dashboard.pjlp.pdf_cover', compact(
            'periode',
            'user',
            'profile',
            'fotoProfilBase64',
            'qrCodeBase64',
            'firstDate',
            'lastDate',
            'namaBulanTahun',
            'totalHariKerja',
            'totalTerverifikasi'
        ))->setPaper('letter', 'portrait')->output();

        // 5. Render Bagian 3: Halaman Lampiran Evidence Logbook dengan Judul Pemisah
        $pdfEvidence = Pdf::loadView('dashboard.pjlp.pdf_evidence', compact(
            'periode',
            'user',
            'logbookPages',
            'qrCodeBase64',
            'firstDate',
            'lastDate',
            'namaBulanTahun'
        ))->setPaper('letter', 'portrait')->output();

        // 6. Satukan dengan PDF Daftar Gaji (Bagian 2) via Merger
        $cleanUserName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name);
        $fileName = 'Laporan_Bulanan_PJLP_' . $cleanUserName . '_' . $periode->bulan_tahun . '.pdf';

        $merger = new Merger();

        // Temp file Cover
        $tempCoverPath = tempnam(sys_get_temp_dir(), 'sigap_cov_');
        file_put_contents($tempCoverPath, $pdfCover);
        $merger->addFile($tempCoverPath);

        // Sisipkan PDF Daftar Gaji di tengah jika ada
        if ($periode->file_daftar_gaji && Storage::disk('public')->exists($periode->file_daftar_gaji)) {
            $pathDaftarGaji = Storage::disk('public')->path($periode->file_daftar_gaji);
            try {
                $merger->addFile($pathDaftarGaji);
            } catch (\Exception $e) {
                // Lanjut jika PDF gaji corrupt
            }
        }

        // Temp file Evidence Logbook
        $tempEvidencePath = tempnam(sys_get_temp_dir(), 'sigap_evi_');
        file_put_contents($tempEvidencePath, $pdfEvidence);
        $merger->addFile($tempEvidencePath);

        try {
            $mergedPdfContent = $merger->merge();

            @unlink($tempCoverPath);
            @unlink($tempEvidencePath);

            return response($mergedPdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        } catch (\Exception $e) {
            @unlink($tempCoverPath);
            @unlink($tempEvidencePath);

            return response($pdfCover . $pdfEvidence, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            ]);
        }
    }

    public function publicIndex(Request $request)
    {
        $q = $request->get('q');
        $kategoriBulan = $request->get('bulan_tahun');

        // Ambil periode yang memiliki logbook terverifikasi dengan foto
        $query = PjlpPeriode::with(['user.profile', 'logbooks' => function ($q) {
            $q->where('status', 'terverifikasi')
              ->whereNotNull('foto_evidence')
              ->orderBy('tanggal', 'asc');
        }])->whereHas('logbooks', function ($q) {
            $q->where('status', 'terverifikasi')
              ->whereNotNull('foto_evidence');
        });

        if ($q) {
            $query->whereHas('user', function ($u) use ($q) {
                $u->where('name', 'like', "%{$q}%");
            });
        }

        if ($kategoriBulan) {
            $query->where('bulan_tahun', $kategoriBulan);
        }

        $periodes = $query->orderBy('bulan_tahun', 'desc')->paginate(9);

        // Map data logbooks menjadi array sederhana untuk Alpine.js
        $periodes->getCollection()->transform(function ($periode) {
            $periode->slides = $periode->logbooks->map(function ($log) {
                return [
                    'foto' => asset('storage/' . $log->foto_evidence),
                    'tanggal' => $log->tanggal->format('d/m/Y'),
                    'deskripsi' => $log->deskripsi_pekerjaan,
                    'hari' => $log->hari
                ];
            })->values();
            return $periode;
        });

        // Statistik
        $totalPjlp = \App\Models\User::role('pjlp')->count();
        $totalEvidence = PjlpLogbook::where('status', 'terverifikasi')->whereNotNull('foto_evidence')->count();
        $totalPeriode = PjlpPeriode::count();

        return view('dashboard.pjlp.public', compact('periodes', 'q', 'kategoriBulan', 'totalPjlp', 'totalEvidence', 'totalPeriode'));
    }
}