<?php

namespace App\Http\Controllers;

use App\Models\Inkubatorma;
use App\Models\InkubatormaLog;
use App\Models\InkubatormaRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\InkubatormaPengajuanBaruNotification;
use App\Notifications\InkubatormaStatusUpdateNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SigapInkubatormaController extends Controller
{
    /**
     * LIST LAYANAN 
     * value = yang disimpan di DB (string)
     * label = yang ditampilkan di UI
     */
    private function layananOptions(): array
    {
        return [
            'penjaringan_dan_kurasi_ide'      => 'Penjaringan dan Kurasi ide Inovasi',
            'profil_inovasi'                 => 'Asistensi Penyusunan Profil Inovasi',
            'indikator_daerah'               => 'Pengisian Satuan Indikator Inovasi Daerah',
            'implementasi_dan_pilot_project' => 'Pendampingan Implementasi dan Pilot Project',
            'video_inovasi'                  => 'Pembuatan Video Inovasi',
            'kapasitas_sdm'                  => 'Penguatan Kapasitas SDM dan Inovator Riset',
            'konsultasi_tata_kelola'         => 'Konsultasi Inovasi dan Tata Kelola',
            'pembuatan_haki'                 => 'Pembuatan Hak Kekayaan Intelektual (HAKI) Inovasi',
            'hasil_inovasi_riset'            => 'Pengajuan Hasil Inovasi dalam Pelaksanaan Riset',
            'lainnya'                        => 'Lainnya',
        ];
    }

    private function statusOptions(): array
    {
        return Inkubatorma::statusOptions();
    }

    /**
     * Landing page / index
     */
    public function index()
    {
        $layananOptions = $this->layananOptions();

        // LIST JADWAL (SEMUA STATUS)
        $jadwals = Inkubatorma::query()
        ->orderByRaw("
            CASE status
                WHEN '" . Inkubatorma::STATUS_TERJADWAL . "' THEN 1
                WHEN '" . Inkubatorma::STATUS_SESI_KONSULTASI . "' THEN 2
                WHEN '" . Inkubatorma::STATUS_DIJADWALKAN_ULANG . "' THEN 3
                WHEN '" . Inkubatorma::STATUS_MENUNGGU . "' THEN 4
                WHEN '" . Inkubatorma::STATUS_AKAN_DIJADWALKAN . "' THEN 5
                WHEN '" . Inkubatorma::STATUS_SELESAI . "' THEN 6
                WHEN '" . Inkubatorma::STATUS_DITOLAK . "' THEN 7
                ELSE 8
            END
        ")
        ->orderByRaw("
            CASE 
                WHEN status IN ('" . Inkubatorma::STATUS_TERJADWAL . "','" . Inkubatorma::STATUS_SESI_KONSULTASI . "','" . Inkubatorma::STATUS_DIJADWALKAN_ULANG . "')
                    THEN tanggal_final 
                ELSE tanggal_usulan 
            END
        ")
        ->orderByRaw("
            CASE 
                WHEN status IN ('" . Inkubatorma::STATUS_TERJADWAL . "','" . Inkubatorma::STATUS_SESI_KONSULTASI . "','" . Inkubatorma::STATUS_DIJADWALKAN_ULANG . "')
                    THEN jam_final 
                ELSE jam_usulan 
            END
        ")
        ->get();

        // KHUSUS KALENDER
        $calendarJadwals = Inkubatorma::query()
        ->whereIn('status', [
            Inkubatorma::STATUS_MENUNGGU,
            Inkubatorma::STATUS_AKAN_DIJADWALKAN,
            Inkubatorma::STATUS_TERJADWAL,
            Inkubatorma::STATUS_SESI_KONSULTASI,
            Inkubatorma::STATUS_DIJADWALKAN_ULANG,
        ])
        ->orderByRaw("
            CASE 
                WHEN status IN ('" . Inkubatorma::STATUS_TERJADWAL . "','" . Inkubatorma::STATUS_SESI_KONSULTASI . "','" . Inkubatorma::STATUS_DIJADWALKAN_ULANG . "')
                    THEN tanggal_final 
                ELSE tanggal_usulan 
            END
        ")
        ->orderByRaw("
            CASE 
                WHEN status IN ('" . Inkubatorma::STATUS_TERJADWAL . "','" . Inkubatorma::STATUS_SESI_KONSULTASI . "','" . Inkubatorma::STATUS_DIJADWALKAN_ULANG . "')
                    THEN jam_final 
                ELSE jam_usulan 
            END
        ")
        ->get();

        // AMBIL PEGAWAI
        $employees = User::role('employee')
            ->where('status','active')
            ->select('id','name')
            ->orderBy('name')
            ->get();

        $formData = session('inkubatorma_form');

        return view('SigapInkubatorma.home.index', compact('layananOptions', 'jadwals', 'calendarJadwals', 'employees', 'formData'));
    }

    /**
     * Dashboard
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // =========================
        // 0) ROLE FILTER (single source of truth)
        // =========================
        $base = Inkubatorma::query()->orderBy('created_at', 'desc');

        if ($user && ($user->hasRole('admin') || $user->hasRole('verifikator_inkubatorma'))) {
            // lihat semua
        } else {
            // user biasa: hanya miliknya
            $base->where('created_by', $user->id);
        }

        // =========================
        // 1) INPUT PERIODE (overall | yearly | monthly)
        // =========================
        $period = $request->query('period', 'overall'); // overall, yearly, monthly
        if (!in_array($period, ['overall', 'yearly', 'monthly'], true)) $period = 'overall';

        $now   = Carbon::now('Asia/Makassar');
        $year  = (int) $request->query('year', (int) $now->year);
        $month = (int) $request->query('month', (int) $now->month);

        // kalau period bukan monthly, month tidak terlalu penting tapi tetap aman
        if ($month < 1 || $month > 12) $month = (int) $now->month;

        // =========================
        // 2) DATE RANGE UNTUK "FILTERED DATA"
        // dipakai ringkasan + pie layanan + bar opd
        // =========================
        $filterStart = null;
        $filterEnd   = null;

        if ($period === 'yearly') {
            $filterStart = Carbon::create($year, 1, 1, 0, 0, 0, 'Asia/Makassar')->startOfDay();
            $filterEnd   = (clone $filterStart)->endOfYear()->endOfDay();
        }

        if ($period === 'monthly') {
            $filterStart = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Makassar')->startOfDay();
            $filterEnd   = (clone $filterStart)->endOfMonth()->endOfDay();
        }

        $filtered = (clone $base);
        if ($filterStart && $filterEnd) {
            $filtered->whereBetween('created_at', [$filterStart, $filterEnd]);
        }

        // =========================
        // 3) PAGINATION UNTUK TABLE
        // =========================
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) $perPage = 10;

        $inkubatormas = (clone $base)
            ->paginate($perPage)
            ->withQueryString();

        // =========================
        // 4) OPTIONS LAYANAN
        // =========================
        $layananOptions = $this->layananOptions();

        // =========================
        // 5) RINGKASAN STATUS (dari FILTERED)
        // =========================
        $statusCounts = (clone $filtered)
            ->reorder()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ringkasanStatus = [
            Inkubatorma::STATUS_MENUNGGU  => (int) ($statusCounts[Inkubatorma::STATUS_MENUNGGU] ?? 0),
            Inkubatorma::STATUS_TERJADWAL => (int) ($statusCounts[Inkubatorma::STATUS_TERJADWAL] ?? 0),
            Inkubatorma::STATUS_SELESAI   => (int) ($statusCounts[Inkubatorma::STATUS_SELESAI] ?? 0),
        ];

        // =========================
        // 6) PIE: PERSEBARAN LAYANAN (dari FILTERED)
        // =========================
        $layananRows = (clone $filtered)
        ->reorder()
        ->get(['layanan_id', 'layanan_lainnya']);

        $layananCounter = [];

        foreach ($layananRows as $row) {
        $ids = $row->layanan_id ?? [];

        if (!is_array($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : (empty($ids) ? [] : [$ids]);
        }

        foreach ($ids as $id) {
            $label = $layananOptions[$id] ?? (string) $id;

            if ($id === 'lainnya' && !empty($row->layanan_lainnya)) {
                $label = ($layananOptions['lainnya'] ?? 'Lainnya') . ' • ' . $row->layanan_lainnya;
            }

            if (!isset($layananCounter[$label])) {
                $layananCounter[$label] = 0;
            }

            $layananCounter[$label]++;
        }
        }

        arsort($layananCounter);

        $pieLayanan = collect($layananCounter)
        ->map(function ($total, $label) {
            return [
                'key'   => $label,
                'label' => $label,
                'total' => (int) $total,
            ];
        })
        ->values()
        ->all();

        // =========================
        // 7) BAR: TOP OPD (dari FILTERED)
        // =========================
        $opdCounts = (clone $filtered)
            ->reorder()
            ->selectRaw('opd_unit, COUNT(*) as total')
            ->groupBy('opd_unit')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($r) => ['label' => (string)($r->opd_unit ?? '—'), 'total' => (int)$r->total])
            ->values()
            ->all();

        // =========================
        // 8) LINE: JUMLAH PENGAJUAN
        // overall: YEAR-MONTH across all time (YYYY-MM)
        // yearly : Jan..Dec (bulan) untuk tahun terpilih
        // monthly: per hari dalam bulan terpilih (01..N)
        // =========================
        $line = [
            'labels' => [],
            'values' => [],
            'meta'   => ['period' => $period],
        ];

        if ($period === 'overall') {
            $rows = (clone $base)
                ->reorder()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
                ->groupBy('ym')
                ->orderBy('ym')
                ->get();

            $line['labels'] = $rows->pluck('ym')->all();
            $line['values'] = $rows->pluck('total')->map(fn($v)=>(int)$v)->all();
        }

        if ($period === 'yearly') {
            $yearStart = Carbon::create($year, 1, 1, 0, 0, 0, 'Asia/Makassar')->startOfDay();
            $yearEnd   = (clone $yearStart)->endOfYear()->endOfDay();

            $rows = (clone $base)
                ->reorder()
                ->whereBetween('created_at', [$yearStart, $yearEnd])
                ->selectRaw("MONTH(created_at) as m, COUNT(*) as total")
                ->groupBy('m')
                ->orderBy('m')
                ->get()
                ->keyBy('m');

            $labels = [];
            $values = [];
            for ($m=1; $m<=12; $m++) {
                $labels[] = Carbon::create($year, $m, 1, 0, 0, 0, 'Asia/Makassar')->translatedFormat('M');
                $values[] = (int) ($rows[$m]->total ?? 0);
            }

            $line['labels'] = $labels;
            $line['values'] = $values;
            $line['meta']['year'] = $year;
        }

        if ($period === 'monthly') {
            $monthStart = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Makassar')->startOfDay();
            $monthEnd   = (clone $monthStart)->endOfMonth()->endOfDay();
            $daysInMonth = $monthStart->daysInMonth;

            $rows = (clone $base)
                ->reorder()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->selectRaw("DAY(created_at) as d, COUNT(*) as total")
                ->groupBy('d')
                ->orderBy('d')
                ->get()
                ->keyBy('d');

            $labels = [];
            $values = [];
            for ($d=1; $d<=$daysInMonth; $d++) {
                $labels[] = str_pad((string)$d, 2, '0', STR_PAD_LEFT); // 01..31
                $values[] = (int) ($rows[$d]->total ?? 0);
            }

            $line['labels'] = $labels;
            $line['values'] = $values;
            $line['meta']['year'] = $year;
            $line['meta']['month'] = $month;
        }

        return view('dashboard.inkubatorma.dashboard', [
            'inkubatormas'      => $inkubatormas,
            'layananOptions'    => $layananOptions,

            // state untuk UI
            'period'            => $period,
            'year'              => $year,
            'month'             => $month,

            // datasets
            'ringkasanStatus'   => $ringkasanStatus,
            'pieLayanan'        => $pieLayanan,
            'opdCounts'         => $opdCounts,
            'line'              => $line,
            'updatedAtLabel'    => Carbon::now('Asia/Makassar')->translatedFormat('d M Y H:i') . ' WITA',
        ]);
    }

    public function printLaporan(Request $request)
    {
        $user = Auth::user();
        $tz   = 'Asia/Makassar';
        $now  = Carbon::now($tz);

        // =========================
        // 1) BASE QUERY SESUAI ROLE
        // =========================
        $base = Inkubatorma::query();

        if ($user && ($user->hasRole('admin') || $user->hasRole('verifikator_inkubatorma'))) {
            // lihat semua
        } elseif ($user) {
            $base->where('created_by', $user->id);
        } else {
            $base->whereRaw('1 = 0');
        }

        // =========================
        // 2) PARAMETER PERIODE
        // =========================
        $period = $request->query('period', 'overall');
        if (!in_array($period, ['overall', 'yearly', 'monthly'], true)) {
            $period = 'overall';
        }

        $year  = (int) $request->query('year', $now->year);
        $month = (int) $request->query('month', $now->month);
        $month = ($month >= 1 && $month <= 12) ? $month : (int) $now->month;

        // =========================
        // 3) FILTER RANGE
        // =========================
        [$filterStart, $filterEnd, $periodeLabel] = match ($period) {
            'yearly' => [
                Carbon::create($year, 1, 1, 0, 0, 0, $tz)->startOfDay(),
                Carbon::create($year, 12, 31, 23, 59, 59, $tz)->endOfDay(),
                'Tahun ' . $year,
            ],
            'monthly' => [
                Carbon::create($year, $month, 1, 0, 0, 0, $tz)->startOfDay(),
                Carbon::create($year, $month, 1, 0, 0, 0, $tz)->endOfMonth()->endOfDay(),
                Carbon::create($year, $month, 1, 0, 0, 0, $tz)->translatedFormat('F Y'),
            ],
            default => [null, null, 'Keseluruhan Data'],
        };

        $filtered = clone $base;
        if ($filterStart && $filterEnd) {
            $filtered->whereBetween('created_at', [$filterStart, $filterEnd]);
        }

        $layananOptions = $this->layananOptions();

        // =========================
        // 4) DATA TABEL
        // =========================
        $rows = (clone $filtered)
            ->reorder()
            ->latest('created_at')
            ->get();

        // =========================
        // 5) RINGKASAN STATUS
        // =========================
        $statusCounts = (clone $filtered)
            ->reorder()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $ringkasanStatus = [
            Inkubatorma::STATUS_MENUNGGU  => (int) ($statusCounts[Inkubatorma::STATUS_MENUNGGU] ?? 0),
            Inkubatorma::STATUS_TERJADWAL => (int) ($statusCounts[Inkubatorma::STATUS_TERJADWAL] ?? 0),
            Inkubatorma::STATUS_SELESAI   => (int) ($statusCounts[Inkubatorma::STATUS_SELESAI] ?? 0),
        ];

        // =========================
        // 6) PIE: PERSEBARAN LAYANAN (dari FILTERED)
        // =========================
        $layananRows = (clone $filtered)
        ->reorder()
        ->get(['layanan_id', 'layanan_lainnya']);

        $layananCounter = [];

        foreach ($layananRows as $row) {
        $ids = $row->layanan_id ?? [];

        if (!is_array($ids)) {
            $decoded = json_decode($ids, true);
            $ids = is_array($decoded) ? $decoded : (empty($ids) ? [] : [$ids]);
        }

        foreach ($ids as $id) {
            $label = $layananOptions[$id] ?? (string) $id;

            if ($id === 'lainnya' && !empty($row->layanan_lainnya)) {
                $label = ($layananOptions['lainnya'] ?? 'Lainnya') . ' • ' . $row->layanan_lainnya;
            }

            if (!isset($layananCounter[$label])) {
                $layananCounter[$label] = 0;
            }

            $layananCounter[$label]++;
        }
        }

        arsort($layananCounter);

        $pieLayanan = collect($layananCounter)
        ->map(function ($total, $label) {
            return [
                'key'   => $label,
                'label' => $label,
                'total' => (int) $total,
            ];
        })
        ->values()
        ->all();

        // =========================
        // 7) PERSEBARAN OPD
        // =========================
        $opdCounts = (clone $filtered)
            ->reorder()
            ->selectRaw('opd_unit, COUNT(*) as total')
            ->groupBy('opd_unit')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'label' => (string) ($r->opd_unit ?? '—'),
                'total' => (int) $r->total,
            ])
            ->values()
            ->all();

        // =========================
        // 8) LINE CHART
        // overall : per bulan sepanjang data
        // yearly  : per bulan pada tahun dipilih
        // monthly : per hari pada bulan dipilih
        // =========================
        $line = [
            'labels' => [],
            'values' => [],
            'meta'   => ['period' => $period],
        ];

        if ($period === 'overall') {
            $lineRows = (clone $base)
                ->reorder()
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as periode, COUNT(*) as total")
                ->groupBy('periode')
                ->orderBy('periode')
                ->get();

            $line['labels'] = $lineRows->pluck('periode')->all();
            $line['values'] = $lineRows->pluck('total')->map(fn ($v) => (int) $v)->all();
        }

        if ($period === 'yearly') {
            $lineRows = (clone $base)
                ->reorder()
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as periode, COUNT(*) as total')
                ->groupBy('periode')
                ->orderBy('periode')
                ->get()
                ->keyBy('periode');

            for ($m = 1; $m <= 12; $m++) {
                $line['labels'][] = Carbon::create($year, $m, 1, 0, 0, 0, $tz)->translatedFormat('M');
                $line['values'][] = (int) ($lineRows[$m]->total ?? 0);
            }

            $line['meta']['year'] = $year;
        }

        if ($period === 'monthly') {
            $daysInMonth = Carbon::create($year, $month, 1, 0, 0, 0, $tz)->daysInMonth;

            $lineRows = (clone $base)
                ->reorder()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->selectRaw('DAY(created_at) as periode, COUNT(*) as total')
                ->groupBy('periode')
                ->orderBy('periode')
                ->get()
                ->keyBy('periode');

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $line['labels'][] = str_pad((string) $d, 2, '0', STR_PAD_LEFT);
                $line['values'][] = (int) ($lineRows[$d]->total ?? 0);
            }

            $line['meta']['year']  = $year;
            $line['meta']['month'] = $month;
        }

        return view('dashboard.inkubatorma.print', [
            'rows'            => $rows,
            'layananOptions'  => $layananOptions,
            'period'          => $period,
            'year'            => $year,
            'month'           => $month,
            'periodeLabel'    => $periodeLabel,
            'ringkasanStatus' => $ringkasanStatus,
            'layananCounts'   => $pieLayanan,
            'pieLayanan'      => $pieLayanan,
            'opdCounts'       => $opdCounts,
            'line'            => $line,
            'printedAt'       => $now->translatedFormat('d F Y H:i') . ' WITA',
        ]);
    }

    /**
     * Store (pengajuan baru dari landing/index)
     */
    public function store(Request $request)
    {
        $allowed = implode(',', array_keys($this->layananOptions()));

        $validated = $request->validate([
            'nama_pengaju' => ['required', 'string', 'max:255'],
            'no_hp'        => ['required', 'string', 'max:20'],
            'nama_opd'     => ['required', 'string', 'max:255'],

            'layanan' => ['required', 'array', 'max:2'],
            'layanan_lainnya' => ['nullable','string','max:255', 'required_if:layanan,lainnya'],


            'judul_konsultasi' => ['required', 'string', 'max:255'],
            'keluhan'          => ['required', 'string'],
            'poin_asistensi'   => ['required', 'string'],
            'tanggal'          => ['required', 'date'],
            'jam'              => ['required'], 
            'mode'             => ['required', 'in:online,offline'],

            'pegawai_id'       => ['nullable', 'exists:users,id'],

            'lampiran' => ['required','array','max:3'],
            'lampiran.*' => ['file','mimes:pdf,doc,docx','max:102400'], // 100MB
        ]);

        $layananLainnya = in_array('lainnya', $validated['layanan'])
        ? ($validated['layanan_lainnya'] ?? null)
        : null;

        $lampiranPaths = [];
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                // $path = $file->store('inkubatorma/lampiran', 'public');
                // supaya nama file sesuai yg diupload
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = $file->getClientOriginalExtension();
                $safeName     = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalName);
                $uniqueName   = $safeName . '_' . time() . '_' . uniqid() . '.' . $extension;
                $path = $file->storeAs('inkubatorma/lampiran', $uniqueName, 'public');
                $lampiranPaths[] = $path;
            }
        }

        // ambil nama target personil dari users (kalau dipilih)
        $targetPersonilNama = null;
        if (!empty($validated['pegawai_id'])) {
            $targetPersonilNama = User::where('id', $validated['pegawai_id'])->value('name');
        }

        // Kode unik (AMAN dari duplikat)
        $prefix = 'INK-' . now()->format('Ymd') . '-';

        // ambil kode terakhir untuk hari ini, lalu naikkan 1
        $lastKode = Inkubatorma::where('kode', 'like', $prefix . '%')
            ->orderBy('kode', 'desc')
            ->value('kode');

        $lastNumber = 0;
        if ($lastKode) {
            // ambil 4 digit terakhir
            $lastNumber = (int) substr($lastKode, -4);
        }

        $newNumber = $lastNumber + 1;
        $kode = $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);

        $inkubatorma = Inkubatorma::create([
            'kode' => $kode,

            // simpan string layanan ke layanan_id
            'layanan_id' => $validated['layanan'],
            'layanan_lainnya' => $layananLainnya,

            'judul_konsultasi' => $validated['judul_konsultasi'] ?? null,
            'nama_pengaju'     => $validated['nama_pengaju'],
            'hp_pengaju'       => $validated['no_hp'],
            'opd_unit'         => $validated['nama_opd'],
            'keluhan'          => $validated['keluhan'],
            'poin_asistensi'   => $validated['poin_asistensi'],
            'tanggal_usulan'   => $validated['tanggal'],
            'jam_usulan'       => $validated['jam'],
            'metode_usulan'    => $validated['mode'],
            'status'           => Inkubatorma::STATUS_MENUNGGU,

            // ✅ agar USER bisa lihat list miliknya
            'created_by'       => Auth::id(),

            // ✅ agar VERIFIKATOR bisa lihat list yang ditujukan kepadanya
            // (pakai pegawai_id dari form index)
            'pic_employee_id'  => $validated['pegawai_id'] ?? null,

            'target_personil_usulan' => $targetPersonilNama,
            
            'lampiran' => $lampiranPaths,
        ]);

        $verifikators = User::role('verifikator_inkubatorma')
            ->where('status', 'active')
            ->whereNotNull('email')
            ->select('id', 'name', 'email')
            ->get();

        foreach ($verifikators as $verifikator) {
            $verifikator->notify(new InkubatormaPengajuanBaruNotification($inkubatorma));
        }

        if (!auth()->check()) {
            session()->put('inkubatorma_form', $validated);
            // supaya kembali ke halaman inkubatorma
            redirect()->setIntendedUrl(route('sigap-inkubatorma.index') . '#form');
            return redirect()->route('login');
        }

        session()->forget('inkubatorma_form');

        return redirect()
            ->route('sigap-inkubatorma.dashboard')
            ->with('success', 'Pengajuan berhasil dikirim');
    }

    /**
     * Detail
     */
    public function detail($id)
    {
        $inkubatorma = Inkubatorma::with([
            'picUser',
            'verifikatorUser',
            'logs',
        ])->findOrFail($id);

        $status = $inkubatorma->status ?? Inkubatorma::STATUS_MENUNGGU;
        $layananOptions = $this->layananOptions();

        // ✅ TAMBAHAN: ambil semua verifikator inkubatorma (aktif)
        $verifikators = User::role('verifikator_inkubatorma')
        ->where('status', 'active')
        ->select('id', 'name', 'nomor_hp', 'profile_photo_path')
        ->orderBy('name')
        ->get();

        return view('dashboard.inkubatorma.detail', compact('inkubatorma', 'status', 'layananOptions', 'verifikators'));
    }

    private function canEditByStatus(?string $status): bool
    {
        $status = $status ?? Inkubatorma::STATUS_MENUNGGU;

        return in_array($status, [
            Inkubatorma::STATUS_MENUNGGU,
            Inkubatorma::STATUS_AKAN_DIJADWALKAN,
        ], true);
    }

    /**
     * Edit
     */
    public function edit($id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);

        if (!$this->canEditByStatus($inkubatorma->status)) {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('error', 'Pengajuan tidak dapat diedit pada status saat ini.');
        }

        $layananOptions = $this->layananOptions();

        // AMBIL PEGAWAI
        $employees = User::role('employee')
            ->where('status','active')
            ->select('id','name')
            ->orderBy('name')
            ->get();

        return view('dashboard.inkubatorma.edit', compact('inkubatorma', 'layananOptions', 'employees'));
    }

    /**
     * Update dari halaman edit.blade.php
     */
    public function update(Request $request, $id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);

        if (!$this->canEditByStatus($inkubatorma->status)) {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('error', 'Pengajuan tidak dapat diedit pada status saat ini.');
        }

        $allowed = implode(',', array_keys($this->layananOptions()));

        $validated = $request->validate([
            // Supaya bisa 2 layanan maksimal
            'layanan_id' => ['required','array','max:2'],
            'layanan_id.*' => ["in:$allowed"],
            'layanan_lainnya' => ['nullable','string','max:255', 'required_if:layanan_id,lainnya'],

            'judul_konsultasi' => ['required', 'string', 'max:255'],
            'nama_pengaju' => ['required', 'string', 'max:255'],
            'hp_pengaju'   => ['nullable', 'string', 'max:20'],
            'opd_unit'     => ['required', 'string', 'max:255'],

            'keluhan'        => ['nullable', 'string'],
            'poin_asistensi' => ['nullable', 'string'],

            'tanggal_usulan' => ['nullable', 'date'],
            'jam_usulan'     => ['nullable', 'date_format:H:i'],
            'metode_usulan'  => ['nullable', 'in:online,offline'],
            'target_personil_usulan' => ['nullable', 'string', 'max:255'],
        ]);

        $inkubatorma->layanan_id = $validated['layanan_id'];
        $inkubatorma->layanan_lainnya = in_array('lainnya', $validated['layanan_id'])
            ? ($validated['layanan_lainnya'] ?? null)
            : null;

        // $inkubatorma->save();

        // Lampiran — GABUNG atau REPLACE
        $lampiranLama = $inkubatorma->lampiran ?? [];

        // Cek file yang mau dihapus (dikirim dari form sebagai hidden input)
        $hapusFile = $request->input('hapus_lampiran', []);
        if (!empty($hapusFile)) {
            foreach ($hapusFile as $pathHapus) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pathHapus);
            }
            $lampiranLama = array_values(array_filter(
                $lampiranLama,
                fn($p) => !in_array($p, $hapusFile)
            ));
        }

        // Upload file baru
        if ($request->hasFile('lampiran')) {
            foreach ($request->file('lampiran') as $file) {
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension    = $file->getClientOriginalExtension();
                $safeName     = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $originalName);
                $uniqueName   = $safeName . '_' . time() . '_' . uniqid() . '.' . $extension;
                $lampiranLama[] = $file->storeAs('inkubatorma/lampiran', $uniqueName, 'public');
            }
        }

        // Batasi max 3
        $lampiranPaths = array_slice(array_values($lampiranLama), 0, 3);

        $validated['lampiran'] = $lampiranPaths;

        $inkubatorma->update($validated);

        return redirect()
            ->route('sigap-inkubatorma.detail', $inkubatorma->id)
            ->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    /**
     * Halaman verifikasi
     */
    public function verifikasi($id)
    {
        $inkubatorma = Inkubatorma::with([
            'picUser',
            'verifikatorUser',
            'logs',
        ])->findOrFail($id);

        // untuk dropdown/search PIC: user employee aktif
        $employees = User::role('employee')
            ->where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $status = $inkubatorma->status ?? Inkubatorma::STATUS_MENUNGGU;
        $layananOptions = $this->layananOptions();

        return view('dashboard.inkubatorma.verifikasi', compact('inkubatorma', 'employees', 'status', 'layananOptions'));
    }

    /**
     * Simpan aksi verifikasi
     */
    public function verifikasiUpdate(Request $request, $id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);

        if (($inkubatorma->status ?? '') === Inkubatorma::STATUS_SELESAI) {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('error', 'Pengajuan sudah ditutup. Verifikasi tidak bisa diubah lagi.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', $this->statusOptions())],

            'pic_employee_id'   => ['nullable', 'exists:users,id'],
            'tanggal_final'     => ['nullable', 'date'],
            'jam_final'         => ['nullable', 'date_format:H:i'],
            'metode_final'      => ['nullable', 'in:online,offline'],
            'lokasi_link_final' => ['nullable', 'string', 'max:255'],

            'catatan_verifikator' => ['nullable', 'string'],
        ]);

        $statusLama = $inkubatorma->status ?? Inkubatorma::STATUS_MENUNGGU;
        $statusBaru = $validated['status'];

        if ($statusBaru === Inkubatorma::STATUS_SELESAI) {
            $confirm = strtoupper(trim((string) $request->input('close_confirm', '')));
            if ($confirm !== 'TUTUP') {
                return back()
                    ->withErrors(['status' => 'Konfirmasi penutupan gagal. Ketik TUTUP untuk menutup konsultasi.'])
                    ->withInput();
            }
        }

        $butuhJadwal = in_array($statusBaru, [
            Inkubatorma::STATUS_TERJADWAL,
            Inkubatorma::STATUS_SESI_KONSULTASI,
            Inkubatorma::STATUS_DIJADWALKAN_ULANG,
            Inkubatorma::STATUS_SELESAI,
        ], true);

        if ($butuhJadwal) {
            if (empty($validated['tanggal_final']) || empty($validated['jam_final']) || empty($validated['metode_final'])) {
                return back()
                    ->withErrors(['tanggal_final' => 'Status ini membutuhkan Tanggal/Jam/Metode final.'])
                    ->withInput();
            }
        }

        $inkubatorma->status = $statusBaru;
        $inkubatorma->pic_employee_id   = $validated['pic_employee_id'] ?? null;
        $inkubatorma->tanggal_final     = $validated['tanggal_final'] ?? null;
        $inkubatorma->jam_final         = $validated['jam_final'] ?? null;
        $inkubatorma->metode_final      = $validated['metode_final'] ?? null;
        $inkubatorma->lokasi_link_final = $validated['lokasi_link_final'] ?? null;
        $inkubatorma->catatan_verifikator = $validated['catatan_verifikator'] ?? null;
        $inkubatorma->verifikator_employee_id = $validated['pic_employee_id'] ?? null;
        $inkubatorma->verifikasi_at = now();

        $inkubatorma->save();

        if (!empty($inkubatorma->created_by)) {
            $pengajuUser = User::where('id', $inkubatorma->created_by)
                ->whereNotNull('email')
                ->first();

            if ($pengajuUser) {
                $pengajuUser->notify(new InkubatormaStatusUpdateNotification($inkubatorma));
            }
        }

        $aksi = $this->mapAksiDariStatus($statusLama, $statusBaru);

        InkubatormaLog::create([
            'inkubatorma_id' => $inkubatorma->id,
            'aksi'           => $aksi,
            'status_dari'    => $statusLama,
            'status_ke'      => $statusBaru,
            'catatan'        => $inkubatorma->catatan_verifikator,
            'created_by'     => null,
            'created_at'     => now(),
        ]);

        if ($statusBaru === Inkubatorma::STATUS_SELESAI) {
            return redirect()
                ->route('sigap-inkubatorma.detail', $inkubatorma->id)
                ->with('success', 'Konsultasi ditutup (Selesai).');
        }

        return redirect()
            ->route('sigap-inkubatorma.verifikasi', $inkubatorma->id)
            ->with('success', 'Verifikasi berhasil disimpan.');
    }

    private function mapAksiDariStatus(string $dari, string $ke): string
    {
        if ($ke === Inkubatorma::STATUS_AKAN_DIJADWALKAN) {
            return 'APPROVE';
        }

        if ($ke === Inkubatorma::STATUS_TERJADWAL) {
            return 'SET_SCHEDULE';
        }

        if ($ke === Inkubatorma::STATUS_SESI_KONSULTASI) {
            return 'SESSION_STARTED';
        }

        if ($ke === Inkubatorma::STATUS_DIJADWALKAN_ULANG) {
            return 'RESCHEDULE';
        }

        if ($ke === Inkubatorma::STATUS_DITOLAK) {
            return 'REJECT';
        }

        if ($ke === Inkubatorma::STATUS_SELESAI) {
            return 'CLOSE';
        }

        return 'UPDATE_STATUS';
    }

    /**
     * Halaman record konsultasi
     */
    public function records($id)
    {
        $inkubatorma = Inkubatorma::with([
            'picUser',
            'verifikatorUser',
            'logs',
            'records.creator',
            'records.updater',
        ])->findOrFail($id);

        $user = Auth::user();
        $status = $inkubatorma->status ?? Inkubatorma::STATUS_MENUNGGU;

        // Hak akses:
        // - admin / verifikator: boleh lihat semua
        // - user: hanya miliknya sendiri
        if ($user && !($user->hasRole('admin') || $user->hasRole('verifikator_inkubatorma'))) {
            if ((int) $inkubatorma->created_by !== (int) $user->id) {
                abort(403);
            }
        }

        return view('dashboard.inkubatorma.records', [
            'inkubatorma' => $inkubatorma,
            'status'      => $status,
            'isAdmin'     => $user?->hasRole('admin') ?? false,
            'isVerifikator' => $user?->hasRole('verifikator_inkubatorma') ?? false,
            'isUser'      => $user?->hasRole('user') ?? false,
        ]);
    }

    /**
     * Verifikator tambah record sesi / revisi
     */
    public function storeRecord(Request $request, $id)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);
        $user = Auth::user();

        if (!$user || !($user->hasRole('admin') || $user->hasRole('verifikator_inkubatorma'))) {
            abort(403);
        }

        $validated = $request->validate([
            'meeting_notes'      => ['required', 'string'],
            'revision_notes'     => ['nullable', 'string'],
            'revision_status'    => ['required', 'in:none,needs_revision,resolved'],
            'is_final_confirmation_ready' => ['nullable', 'boolean'],
        ]);

        InkubatormaRecord::create([
            'inkubatorma_id'              => $inkubatorma->id,
            'meeting_notes'               => $validated['meeting_notes'],
            'revision_notes'              => $validated['revision_notes'] ?? null,
            'revision_status'             => $validated['revision_status'],
            'user_revision_file'          => null,
            'user_revision_note'          => null,
            'user_confirmed_finish'       => false,
            'user_confirmed_finish_at'    => null,
            'is_final_confirmation_ready' => (bool) ($validated['is_final_confirmation_ready'] ?? false),
            'created_by'                  => $user->id,
            'updated_by'                  => $user->id,
        ]);

        return redirect()
            ->route('sigap-inkubatorma.records', $inkubatorma->id)
            ->with('success', 'Record konsultasi berhasil ditambahkan.');
    }

    /**
     * Verifikator update record
     */
    public function updateRecord(Request $request, $id, $recordId)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);
        $record = InkubatormaRecord::where('inkubatorma_id', $inkubatorma->id)->findOrFail($recordId);
        $user = Auth::user();

        if (!$user || !($user->hasRole('admin') || $user->hasRole('verifikator_inkubatorma'))) {
            abort(403);
        }

        $validated = $request->validate([
            'meeting_notes'      => ['required', 'string'],
            'revision_notes'     => ['nullable', 'string'],
            'revision_status'    => ['required', 'in:none,needs_revision,resolved'],
            'is_final_confirmation_ready' => ['nullable', 'boolean'],
        ]);

        $record->update([
            'meeting_notes'               => $validated['meeting_notes'],
            'revision_notes'              => $validated['revision_notes'] ?? null,
            'revision_status'             => $validated['revision_status'],
            'is_final_confirmation_ready' => (bool) ($validated['is_final_confirmation_ready'] ?? false),
            'updated_by'                  => $user->id,
        ]);

        return redirect()
            ->route('sigap-inkubatorma.records', $inkubatorma->id)
            ->with('success', 'Record konsultasi berhasil diperbarui.');
    }

    /**
     * User upload file revisi ke record
     */
    public function uploadRecordRevision(Request $request, $id, $recordId)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);
        $record = InkubatormaRecord::where('inkubatorma_id', $inkubatorma->id)->findOrFail($recordId);
        $user = Auth::user();

        if (!$user || (int) $inkubatorma->created_by !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'user_revision_file' => ['required', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,rar,jpg,jpeg,png', 'max:10240'],
            'user_revision_note' => ['nullable', 'string'],
        ]);

        $path = $request->file('user_revision_file')->store('inkubatorma/revisions', 'public');

        // hapus file lama kalau ada
        if (!empty($record->user_revision_file) && Storage::disk('public')->exists($record->user_revision_file)) {
            Storage::disk('public')->delete($record->user_revision_file);
        }

        $record->update([
            'user_revision_file' => $path,
            'user_revision_note' => $validated['user_revision_note'] ?? null,
            'updated_by'         => $user->id,
        ]);

        return redirect()
            ->route('sigap-inkubatorma.records', $inkubatorma->id)
            ->with('success', 'File revisi berhasil diunggah.');
    }

    /**
     * User konfirmasi selesai
     */
    public function confirmRecordFinish(Request $request, $id, $recordId)
    {
        $inkubatorma = Inkubatorma::findOrFail($id);
        $record = InkubatormaRecord::where('inkubatorma_id', $inkubatorma->id)->findOrFail($recordId);
        $user = Auth::user();

        if (!$user || (int) $inkubatorma->created_by !== (int) $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'finish_confirm_code' => ['required', 'string'],
        ]);

        if (strtoupper(trim($validated['finish_confirm_code'])) !== 'SELESAI') {
            return back()
                ->withErrors(['finish_confirm_code' => 'Konfirmasi gagal. Ketik SELESAI untuk melanjutkan.'])
                ->withInput();
        }

        $record->update([
            'user_confirmed_finish'    => true,
            'user_confirmed_finish_at' => now(),
            'updated_by'               => $user->id,
        ]);

        return redirect()
            ->route('sigap-inkubatorma.records', $inkubatorma->id)
            ->with('success', 'Konfirmasi selesai berhasil dikirim ke verifikator.');
    }

    public function destroy($id)
    {
        Inkubatorma::findOrFail($id)->delete();

        return redirect()
            ->route('sigap-inkubatorma.dashboard')
            ->with('success', 'Data berhasil dihapus');
    }
}