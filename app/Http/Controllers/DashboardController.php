<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Skp;
use App\Models\SkpKumpulan;
use App\Models\Inovasi;
use App\Models\Riset;
use App\Models\PpdKegiatan;
use App\Models\PegawaiProfile;
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
        $hasFilledSkp = true;
        $pendingPpdCount = 0;

        if ($user->hasRole('employee')) {
            $currentMonth = date('Y-m');
            $hasFilledSkp = SkpKumpulan::where('user_id', $user->id)
                                       ->where('bulan_tahun', $currentMonth)
                                       ->exists();

            $pendingPpdCount = PpdKegiatan::whereHas('pegawai', function ($q) use ($user) {
                                    $q->where('users.id', $user->id);
                                })
                                ->whereIn('status', ['draft', 'proses'])
                                ->count();
        }

        // ==========================================
        // 3. LOGIKA REMINDER & POPUP ULANG TAHUN (H-3 s/d Hari H)
        // ==========================================
        $today = Carbon::today();
        $todayMonthDay = $today->format('m-d');

        // Ambil semua pegawai yang punya data tanggal lahir
        $profiles = PegawaiProfile::with('user')
            ->whereNotNull('tanggal_lahir')
            ->get();

        $upcomingBirthdays = collect();
        $todayBirthdays = collect();

        foreach ($profiles as $profile) {
            if (!$profile->tanggal_lahir || !$profile->user) continue;

            $birthdate = Carbon::parse($profile->tanggal_lahir);
            
            // Hitung tanggal ultah di tahun berjalan
            $birthdayThisYear = Carbon::createFromDate($today->year, $birthdate->month, $birthdate->day)->startOfDay();

            // Jika ultah tahun ini sudah lewat (misal kemarin), hitung ultah tahun depan
            if ($birthdayThisYear->isPast() && !$birthdayThisYear->isToday()) {
                $birthdayThisYear->addYear();
            }

            // Hitung selisih hari dari hari ini ke tanggal ultah
            $diffDays = (int) $today->diffInDays($birthdayThisYear, false);

            // Hitung umur yang akan/sedang dicapai
            $age = $birthdate->diffInYears($birthdayThisYear);

            // Jika ultah dalam kurun H-0 s.d. H-3 (0, 1, 2, atau 3 hari ke depan)
            if ($diffDays >= 0 && $diffDays <= 3) {
                $item = (object) [
                    'user_id'       => $profile->user_id,
                    'name'          => $profile->user->name,
                    'nip'           => $profile->user->nip,
                    'jabatan'       => $profile->jabatan ?: ($profile->user->unit ?: 'Pegawai BRIDA'),
                    'photo'         => $profile->user->profile_photo_path ? asset('storage/'.$profile->user->profile_photo_path) : asset('images/avatar-placeholder.png'),
                    'tanggal_lahir' => $birthdate->translatedFormat('d F Y'),
                    'birth_day_month' => $birthdate->translatedFormat('d F'),
                    'age'           => $age,
                    'diff_days'     => $diffDays, // 0 = Hari H, 1 = Besok, dst.
                ];

                if ($diffDays === 0) {
                    $todayBirthdays->push($item);
                }
                
                $upcomingBirthdays->push($item);
            }
        }

        // Urutkan berdasarkan yang paling dekat ulang tahunnya
        $upcomingBirthdays = $upcomingBirthdays->sortBy('diff_days')->values();

        // ==========================================
        // 4. DATA GRAFIK (Chart.js)
        // ==========================================
        $trendLabels = [];
        $trendInovasi = [];
        $trendDokumen = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $trendLabels[] = $date->translatedFormat('M Y');
            
            $trendInovasi[] = Inovasi::whereYear('created_at', $date->year)
                                     ->whereMonth('created_at', $date->month)
                                     ->count();
                                     
            $trendDokumen[] = Document::whereYear('created_at', $date->year)
                                      ->whereMonth('created_at', $date->month)
                                      ->count();
        }

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
            'stageLabels', 'stageData',
            'upcomingBirthdays', 'todayBirthdays'
        ));
    }
}