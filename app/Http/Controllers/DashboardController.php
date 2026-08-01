<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Skp;
use App\Models\SkpKumpulan;
use App\Models\Inovasi;
use App\Models\Riset;
use App\Models\PpdKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ==========================================
        // 1. METRIK GLOBAL (Untuk Semua Pengguna)
        // ==========================================
        $totalPegawai = User::role('employee')->count();
        $totalDokumen = Document::count();
        $totalSkp     = Skp::count();
        $totalInovasi = Inovasi::count();
        $totalRiset   = Riset::count(); 
        $totalPpd     = PpdKegiatan::count();

        // ==========================================
        // 2. SMART ALERTS (Khusus Role 'employee')
        // ==========================================
        $hasFilledSkp = true; // Default true (agar admin tidak kena alert)
        $pendingPpdCount = 0;

        if ($user->hasRole('employee')) {
            // A. Alert SKP (Wajib Bulanan)
            $currentMonth = date('Y-m'); // Format: YYYY-MM
            $hasFilledSkp = SkpKumpulan::where('user_id', $user->id)
                                       ->where('bulan_tahun', $currentMonth)
                                       ->exists();

            // B. Alert PPD (Jika nama pegawai ada di tugas yang belum 'selesai')
            $pendingPpdCount = PpdKegiatan::whereHas('pegawai', function ($q) use ($user) {
                                    $q->where('users.id', $user->id);
                                })
                                ->whereIn('status', ['draft', 'proses'])
                                ->count();
        }

        // ==========================================
        // 3. DATA GRAFIK (Chart.js)
        // ==========================================
        
        // Grafik 1: Tren Unggahan (Dokumen & Inovasi) 6 Bulan Terakhir
        $trendLabels = [];
        $trendInovasi = [];
        $trendDokumen = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $trendLabels[] = $date->translatedFormat('M Y'); // Contoh: Jul 2026
            
            $trendInovasi[] = Inovasi::whereYear('created_at', $date->year)
                                     ->whereMonth('created_at', $date->month)
                                     ->count();
                                     
            $trendDokumen[] = Document::whereYear('created_at', $date->year)
                                      ->whereMonth('created_at', $date->month)
                                      ->count();
        }

        // Grafik 2: Komposisi Tahapan Inovasi
        $inovasiStagesRaw = Inovasi::select('tahap_inovasi', DB::raw('count(*) as total'))
                                   ->groupBy('tahap_inovasi')
                                   ->pluck('total', 'tahap_inovasi')
                                   ->toArray();
        
        $stageLabels = [];
        $stageData = [];
        foreach ($inovasiStagesRaw as $stage => $count) {
            $stageLabels[] = $stage ?: 'Belum Ditentukan';
            $stageData[] = $count;
        }

        return view('dashboard.index', compact(
            'totalPegawai', 'totalDokumen', 'totalSkp', 
            'totalInovasi', 'totalRiset', 'totalPpd',
            'hasFilledSkp', 'pendingPpdCount',
            'trendLabels', 'trendInovasi', 'trendDokumen',
            'stageLabels', 'stageData'
        ));
    }
}