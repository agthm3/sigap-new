<?php

namespace App\Http\Controllers;

use App\Models\PjlpPeriode;
use App\Models\PjlpLogbook;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use iio\libmergepdf\Merger;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PjlpLogbookController extends Controller
{
    /**
     * Halaman Utama Pengisian Logbook PJLP & Kelola Logbook (Admin/Verif)
     */
    public function index(Request $request)
    {
        $authUser = Auth::user();
        $isAdminOrVerif = $authUser->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);
        $bulanTahun = $request->get('bulan_tahun', Carbon::now()->format('Y-m'));

        $pjlpUsers = collect();
        $targetUser = $authUser;

        if ($isAdminOrVerif) {
            $pjlpUsers = User::role('pjlp')->orderBy('name', 'asc')->get();

            if ($request->filled('user_id')) {
                $targetUser = $pjlpUsers->where('id', $request->user_id)->first() ?? $pjlpUsers->first() ?? $authUser;
            } else {
                $targetUser = $pjlpUsers->first() ?? $authUser;
            }
        }

        // 1. Ambil atau Buat Header Periode untuk Target User
        $periode = PjlpPeriode::firstOrCreate(
            ['user_id' => $targetUser->id, 'bulan_tahun' => $bulanTahun],
            ['status_laporan' => 'draft']
        );

        // 2. Generate Hari Kerja (Senin-Jumat) Jika Belum Dibuat
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

    /**
     * Upload / Ganti Dokumen Daftar Gaji (PDF)
     */
    public function uploadDaftarGaji(Request $request, $periodeId)
    {
        $request->validate([
            'file_daftar_gaji' => 'required|file|mimes:pdf|max:5120',
        ]);

        $authUser = Auth::user();
        $isAdminOrVerif = $authUser->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);

        $periodeQuery = PjlpPeriode::query();
        if (!$isAdminOrVerif) {
            $periodeQuery->where('user_id', $authUser->id);
        }
        $periode = $periodeQuery->findOrFail($periodeId);

        if ($periode->file_daftar_gaji && Storage::disk('public')->exists($periode->file_daftar_gaji)) {
            Storage::disk('public')->delete($periode->file_daftar_gaji);
        }

        $path = $request->file('file_daftar_gaji')->store('pjlp/daftar_gaji', 'public');
        $periode->update(['file_daftar_gaji' => $path]);

        return back()->with('success', 'Dokumen Daftar Gaji berhasil diunggah.');
    }

    /**
     * Update Logbook & Multi-Evidence Harian (Maks 3 Foto)
     */
    public function updateLogbook(Request $request, $id)
    {
        $request->validate([
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'foto_evidences' => 'nullable|array|max:3',
            'foto_evidences.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $authUser = Auth::user();
        $isAdminOrVerif = $authUser->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);

        $logbook = PjlpLogbook::with('periode')->findOrFail($id);

        if (!$isAdminOrVerif && $logbook->periode->user_id !== $authUser->id) {
            abort(403, 'Anda tidak memiliki hak akses ke logbook ini.');
        }

        $data = [
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'status' => $isAdminOrVerif ? 'terverifikasi' : 'diajukan',
            'updated_by_user_id' => $authUser->id,
        ];

        if ($request->hasFile('foto_evidences')) {
            // Hapus file lama jika ada
            $oldFiles = is_array($logbook->foto_evidences) ? $logbook->foto_evidences : ($logbook->foto_evidence ? [$logbook->foto_evidence] : []);
            foreach ($oldFiles as $oldFile) {
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            // Simpan file baru (maks 3 foto)
            $paths = [];
            foreach ($request->file('foto_evidences') as $file) {
                $paths[] = $file->store('pjlp/evidence', 'public');
            }
            $data['foto_evidences'] = $paths;
            $data['foto_evidence'] = null;
        }

        $logbook->update($data);

        return back()->with('success', 'Evidence tanggal ' . $logbook->tanggal->format('d/m/Y') . ' berhasil disimpan.');
    }

    /**
     * Halaman Monitoring Seluruh PJLP (Role: Admin, Superadmin, Verif PJLP)
     */
    public function monitoring(Request $request)
    {
        $bulanTahun = $request->get('bulan_tahun', Carbon::now()->format('Y-m'));
        $search = $request->get('search');

        $pjlpUsersQuery = User::role('pjlp');

        if ($search) {
            $pjlpUsersQuery->where('name', 'like', "%{$search}%");
        }

        $pjlpUsers = $pjlpUsersQuery->orderBy('name', 'asc')->get();

        // Hitung struktur hari kerja
        $startDate = Carbon::createFromFormat('Y-m', $bulanTahun)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        $totalHariKerja = 0;
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            if ($date->isWeekday()) {
                $totalHariKerja++;
            }
        }

        $periodes = PjlpPeriode::with('logbooks')
            ->where('bulan_tahun', $bulanTahun)
            ->whereIn('user_id', $pjlpUsers->pluck('id'))
            ->get()
            ->keyBy('user_id');

        $monitoringData = $pjlpUsers->map(function ($user) use ($periodes, $totalHariKerja) {
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
     * Detail Logbook PJLP Tertentu untuk Verifikator
     */
    public function showUserLogbook(Request $request, $userId)
    {
        $targetUser = User::role('pjlp')->findOrFail($userId);
        $bulanTahun = $request->get('bulan_tahun', Carbon::now()->format('Y-m'));

        $periode = PjlpPeriode::firstOrCreate(
            ['user_id' => $targetUser->id, 'bulan_tahun' => $bulanTahun],
            ['status_laporan' => 'draft']
        );

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
     * Admin/Verifikator Mengisikan / Mengedit Evidence Atas Nama PJLP
     */
    public function updateByAdmin(Request $request, $id)
    {
        $request->validate([
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'foto_evidences' => 'nullable|array|max:3',
            'foto_evidences.*' => 'image|mimes:jpg,jpeg,png|max:4096',
        ]);

        $logbook = PjlpLogbook::findOrFail($id);

        $data = [
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
            'status' => 'terverifikasi',
            'updated_by_user_id' => Auth::id(),
        ];

        if ($request->hasFile('foto_evidences')) {
            $oldFiles = is_array($logbook->foto_evidences) ? $logbook->foto_evidences : ($logbook->foto_evidence ? [$logbook->foto_evidence] : []);
            foreach ($oldFiles as $oldFile) {
                if (Storage::disk('public')->exists($oldFile)) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            $paths = [];
            foreach ($request->file('foto_evidences') as $file) {
                $paths[] = $file->store('pjlp/evidence', 'public');
            }
            $data['foto_evidences'] = $paths;
            $data['foto_evidence'] = null;
        }

        $logbook->update($data);

        return back()->with('success', 'Evidence berhasil diisikan atas nama PJLP.');
    }

    /**
     * History & Arsip Laporan Bulanan PJLP
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $tahun = $request->get('tahun', Carbon::now()->format('Y'));
        $filterUser = $request->get('user_id');

        $isAdminOrVerif = $user->hasAnyRole(['admin', 'superadmin', 'verif_pjlp']);

        $periodesQuery = PjlpPeriode::with(['user', 'logbooks'])
            ->where('bulan_tahun', 'like', "{$tahun}-%")
            ->orderBy('bulan_tahun', 'desc');

        if ($isAdminOrVerif) {
            if ($filterUser) {
                $periodesQuery->where('user_id', $filterUser);
            }
            $pjlpUsers = User::role('pjlp')->orderBy('name', 'asc')->get();
        } else {
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

    /**
     * Export Laporan Bulanan PJLP & Penggabungan PDF Daftar Gaji
     */
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

        // 1. Convert Foto Profil User ke Base64 (Untuk DomPDF)
        $fotoProfilBase64 = null;
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            $path = Storage::disk('public')->path($user->profile_photo_path);
            if (file_exists($path)) {
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $fotoProfilBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        // 2. Convert Seluruh Foto Evidence ke Base64 (Mendukung Multi-Foto tanpa error append property)
        $logbooksProcessed = $logbooks->map(function ($item) {
            $tempFotosBase64 = [];
            $fotos = is_array($item->foto_evidences) ? $item->foto_evidences : ($item->foto_evidence ? [$item->foto_evidence] : []);

            foreach ($fotos as $foto) {
                if (Storage::disk('public')->exists($foto)) {
                    $path = Storage::disk('public')->path($foto);
                    if (file_exists($path)) {
                        $type = pathinfo($path, PATHINFO_EXTENSION);
                        $data = file_get_contents($path);
                        $tempFotosBase64[] = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                }
            }

            // Assign langsung array utuh ke property model
            $item->fotos_base64 = $tempFotosBase64;
            return $item;
        });

        $logbookPages = $logbooksProcessed->chunk(6);

        // 3. Generate QR Code Verifikasi (Base64)
        $verifyUrl = url("/verifikasi-pjlp/{$periode->id}?hash=" . md5($periode->id . $user->id . $periode->bulan_tahun));
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode(
            QrCode::format('svg')->size(75)->errorCorrection('H')->generate($verifyUrl)
        );

        $firstDate = $logbooks->first() ? $logbooks->first()->tanggal->translatedFormat('d F Y') : '-';
        $lastDate = $logbooks->last() ? $logbooks->last()->tanggal->translatedFormat('d F Y') : '-';
        $namaBulanTahun = Carbon::createFromFormat('Y-m', $periode->bulan_tahun)->translatedFormat('F Y');

        $totalHariKerja = $logbooks->count();
        $totalTerverifikasi = $logbooks->where('status', 'terverifikasi')->count();

        // 4. Render Cover & Evidence PDF via DomPDF
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

        $pdfEvidence = Pdf::loadView('dashboard.pjlp.pdf_evidence', compact(
            'periode',
            'user',
            'logbookPages',
            'qrCodeBase64',
            'firstDate',
            'lastDate',
            'namaBulanTahun'
        ))->setPaper('letter', 'portrait')->output();

        // 5. Gabungkan dengan Dokumen PDF Daftar Gaji via Merger
        $cleanUserName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $user->name);
        $fileName = 'Laporan_Bulanan_PJLP_' . $cleanUserName . '_' . $periode->bulan_tahun . '.pdf';

        $merger = new Merger();

        $tempCoverPath = tempnam(sys_get_temp_dir(), 'sigap_cov_');
        file_put_contents($tempCoverPath, $pdfCover);
        $merger->addFile($tempCoverPath);

        if ($periode->file_daftar_gaji && Storage::disk('public')->exists($periode->file_daftar_gaji)) {
            $pathDaftarGaji = Storage::disk('public')->path($periode->file_daftar_gaji);
            try {
                $merger->addFile($pathDaftarGaji);
            } catch (\Exception $e) {
                // Lewati jika file lampiran gaji corrupt
            }
        }

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

    /**
     * Halaman Publikasi Logbook PJLP untuk Portal Publik
     */
    public function publicIndex(Request $request)
    {
        $q = $request->get('q');
        $kategoriBulan = $request->get('bulan_tahun');

        $query = PjlpPeriode::with(['user.profile', 'logbooks' => function ($q) {
            $q->where('status', 'terverifikasi')
              ->where(function($sub) {
                  $sub->whereNotNull('foto_evidence')
                      ->orWhereNotNull('foto_evidences');
              })
              ->orderBy('tanggal', 'asc');
        }])->whereHas('logbooks', function ($q) {
            $q->where('status', 'terverifikasi')
              ->where(function($sub) {
                  $sub->whereNotNull('foto_evidence')
                      ->orWhereNotNull('foto_evidences');
              });
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

        // Map data foto ke dalam format slide sederhana untuk rotasi otomatis
        $periodes->getCollection()->transform(function ($periode) {
            $periode->slides = $periode->logbooks->flatMap(function ($log) {
                $fotos = is_array($log->foto_evidences) ? $log->foto_evidences : ($log->foto_evidence ? [$log->foto_evidence] : []);
                $slides = [];
                foreach ($fotos as $foto) {
                    $slides[] = [
                        'foto' => asset('storage/' . $foto),
                        'tanggal' => $log->tanggal->format('d/m/Y'),
                        'deskripsi' => $log->deskripsi_pekerjaan,
                        'hari' => $log->hari
                    ];
                }
                return $slides;
            })->values();
            return $periode;
        });

        $totalPjlp = User::role('pjlp')->count();
        $totalEvidence = PjlpLogbook::where('status', 'terverifikasi')
            ->where(function($sub) {
                $sub->whereNotNull('foto_evidence')
                    ->orWhereNotNull('foto_evidences');
            })->count();
        $totalPeriode = PjlpPeriode::count();

        return view('dashboard.pjlp.public', compact(
            'periodes',
            'q',
            'kategoriBulan',
            'totalPjlp',
            'totalEvidence',
            'totalPeriode'
        ));
    }
}