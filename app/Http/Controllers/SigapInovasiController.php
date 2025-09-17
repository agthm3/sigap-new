<?php

namespace App\Http\Controllers;

use App\Models\Inovasi;
use App\Repositories\EvidenceRepository;
use App\Repositories\InovasiRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SigapInovasiController extends Controller
{
    public function __construct(private InovasiRepository $repo)
    {
//
    }

    /**
     * Landing / non-dashboard (company profile).
     */
    public function home()
    {
        return view('SigapInovasi.home.index');
    }

    /**
     * Daftar inovasi (terfilter per user kecuali admin).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $filters = [
            'q'            => $request->get('q'),
            'inisiator'    => $request->get('f_inisiator'),
            'tahap'        => $request->get('f_tahap_inovasi'),
            'tahap_status' => $request->get('f_tahap_status'),
            'urusan'       => $request->get('f_urusan'),
            'sort'         => $request->get('sort','terbaru'),
        ];

        $items = $this->repo->paginateForUser($user, $filters, 25);
        return view('dashboard.inovasi.index', compact('filters', 'items'));
    }

    public function konfigurasi()
    {
        $this->authorizeAdmin();
        return view('dashboard.inovasi.konfigurasi');
    }

    /**
     * Dashboard (KPI & ringkasan) – data terfilter per role.
     */
    public function dashboard()
    {
        $user = Auth::user();

        $base = Inovasi::query();
        if (!$user->hasRole('admin')) {
            $base->where('user_id', $user->id);
        }

        // KPI ringkas
        $total      = (clone $base)->count();
        $opdCount   = (clone $base)->whereNotNull('opd_unit')->distinct('opd_unit')->count('opd_unit');
        $ujiCount   = (clone $base)->where('tahap_inovasi','Uji Coba')->count();
        $terapCount = (clone $base)->where('tahap_inovasi','Penerapan')->count();

        // Leaderboard OPD (Jumlah Inovasi / opd_unit)
        $leaderboard = (clone $base)
            ->select('opd_unit', DB::raw('COUNT(*) as total'))
            ->whereNotNull('opd_unit')
            ->groupBy('opd_unit')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Pengajuan terbaru (5)
        $recent = (clone $base)->latest('created_at')
            ->limit(5)
            ->get(['id','judul','opd_unit','tahap_inovasi','asistensi_status','created_at']);

        // Butuh tindak lanjut: yang baru "Dikembalikan" / "Revisi" / "Ditolak"
        $needsFollowup = (clone $base)
            ->whereIn('asistensi_status', ['Dikembalikan','Revisi','Ditolak'])
            ->orderByDesc('asistensi_at')
            ->limit(8)
            ->get(['id','judul','opd_unit','asistensi_status','asistensi_note','asistensi_at']);

        // (opsional) distribusi & chart bulanan tetap
        $stages = (clone $base)
            ->select('tahap_inovasi', DB::raw('COUNT(*) as total'))
            ->groupBy('tahap_inovasi')
            ->pluck('total','tahap_inovasi');

        $since = now()->startOfMonth()->subMonths(11);
        $monthlyRaw = (clone $base)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as c')
            ->where('created_at','>=',$since)
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        $labels = [];
        $dataMonthly = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $labels[] = now()->subMonths($i)->isoFormat('MMM YYYY');
            $dataMonthly[] = (int) ($monthlyRaw->firstWhere('ym', $m)->c ?? 0);
        }

        // Delta sederhana
        $addedThisMonth    = (clone $base)->whereBetween('created_at',[now()->startOfMonth(), now()])->count();
        $activeOpdThisWeek = (clone $base)->whereBetween('created_at',[now()->startOfWeek(), now()])
                                ->distinct('opd_unit')->count('opd_unit');
        $ujiDeltaWeek      = (clone $base)->where('tahap_inovasi','Uji Coba')
                                ->whereBetween('created_at',[now()->startOfWeek(), now()])->count();

        return view('dashboard.inovasi.dashboard', [
            'kpi' => [
                'total'          => $total,
                'opd'            => $opdCount,
                'uji'            => $ujiCount,
                'terap'          => $terapCount,
                'addedThisMonth' => $addedThisMonth,
                'activeOpdWeek'  => $activeOpdThisWeek,
                'ujiDeltaWeek'   => $ujiDeltaWeek,
            ],
            'leaderboard'   => $leaderboard,
            'recent'        => $recent,
            'stages'        => $stages,
            'chartMonthly'  => ['labels'=>$labels, 'data'=>$dataMonthly],
            'needsFollowup' => $needsFollowup,
        ]);
    }

    /**
     * Simpan inovasi baru.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'judul'                 => ['required','string','max:255'],
            'opd_unit'              => ['nullable','string','max:255'],
            'inisiator_daerah'      => ['nullable','string','max:255'],
            'inisiator_nama'        => ['nullable','string','max:255'],
            'koordinat'             => ['nullable','string','max:255'],
            'klasifikasi'           => ['nullable','string','max:255'],
            'jenis_inovasi'         => ['nullable','string','max:255'],
            'bentuk_inovasi_daerah' => ['nullable','string','max:255'],
            'asta_cipta'            => ['nullable','string','max:255'],
            'program_prioritas'     => ['nullable','string','max:255'],
            'urusan_pemerintah'     => ['nullable','string','max:255'],
            'waktu_uji_coba'        => ['nullable','date'],
            'waktu_penerapan'       => ['nullable','date'],
            'tahap_inovasi'         => ['nullable','string','max:50'],
            'rancang_bangun'        => ['nullable','string'],
            'tujuan'                => ['nullable','string'],
            'manfaat'               => ['nullable','string'],
            'hasil_inovasi'         => ['nullable','string'],
            'perkembangan_inovasi'  => ['nullable','string','max:255'],
        ]);

        // set pemilik
        $data['user_id'] = Auth::id();

        $this->repo->create(
            $data,
            $r->file('anggaran'),
            $r->file('profil_bisnis'),
            $r->file('haki'),
            $r->file('penghargaan')
        );

        return redirect()->route('sigap-inovasi.index')->with('success', 'Inovasi berhasil ditambahkan.');
    }

    /**
     * Hapus inovasi (pemilik/admin).
     */
    public function destroy($id)
    {
        $inovasi = $this->repo->find($id);
        $this->authorizeOwnerOrAdmin($inovasi);

        $this->repo->delete($id);
        return redirect()->route('sigap-inovasi.index')->with('success', 'Inovasi berhasil dihapus.');
    }

    /**
     * Detail inovasi (hydrate evidence file_url/file_name + lampiran utama).
     */
    public function show(int $id, EvidenceRepository $evidenceRepo)
    {
        $inovasi = $this->repo->find($id);
        $this->authorizeOwnerOrAdmin($inovasi);

        // Status tahapan sederhana
        $tInis  = $inovasi->t_inisiatif   ?? $inovasi->t_inisiatif_status   ?? 'Belum';
        $tUji   = $inovasi->t_uji_coba    ?? $inovasi->t_uji_status         ?? 'Belum';
        $tTerap = $inovasi->t_penerapan   ?? $inovasi->t_penerapan_status   ?? 'Belum';

        $steps = collect([$tInis,$tUji,$tTerap])->filter(fn($s)=> strcasecmp((string)$s,'Belum') !== 0 && !empty($s))->count();
        $progressPct = (int) round($steps / 3 * 100);

        // Evidence mentah
        $evItemsRaw = collect($evidenceRepo->listForInovasi($inovasi->id));

        // Hydrate: buat file_url & file_name
        $evItems = $evItemsRaw->map(function($r){
            $path = $r['file_path'] ?? $r['path'] ?? $r['file'] ?? $r['storage_path'] ?? null;

            $url  = $r['file_url'] ?? null;
            if (!$url && $path) {
                $url = Storage::disk('public')->exists($path)
                    ? Storage::disk('public')->url($path)
                    : (preg_match('#^https?://#i', $path) ? $path : null);
            }

            $name = $r['file_name'] ?? ($path ? basename($path) : null);

            $r['file_url']  = $url;
            $r['file_name'] = $name;
            return $r;
        });

        $evTotal  = $evidenceRepo->totalWeight($inovasi->id);
        $evFilled = $evItems->where('selected_weight','>',0)->count();
        $evFiles  = $evItems->filter(fn($r)=> !empty($r['file_url']))->count();

        // Lampiran utama entity Inovasi
        $mainFiles = collect([
            ['label' => 'Anggaran',       'path' => $inovasi->anggaran_file],
            ['label' => 'Profil Bisnis',  'path' => $inovasi->profil_bisnis_file],
            ['label' => 'HAKI',           'path' => $inovasi->haki_file],
            ['label' => 'Penghargaan',    'path' => $inovasi->penghargaan_file],
        ])->map(function($f){
            if (empty($f['path'])) return null;
            $exists = Storage::disk('public')->exists($f['path']);
            return [
                'label' => $f['label'],
                'path'  => $f['path'],
                'url'   => $exists ? Storage::disk('public')->url($f['path']) : null,
                'name'  => basename($f['path']),
            ];
        })->filter();

        return view('dashboard.inovasi.show', compact(
            'inovasi','tInis','tUji','tTerap','progressPct',
            'evItems','evTotal','evFilled','evFiles','mainFiles'
        ));
    }

    /**
     * Form Evidence (pemilik/admin).
     */
    public function evidenceForm(Inovasi $inovasi, EvidenceRepository $evidenceRepo)
    {
        $this->authorizeOwnerOrAdmin($inovasi);

        $items       = $evidenceRepo->listForInovasi($inovasi->id);
        $totalWeight = $evidenceRepo->totalWeight($inovasi->id);
        $doneCount   = collect($items)->filter(fn($i) =>
            !empty($i['selected_label']) || (($i['selected_weight'] ?? 0) > 0)
        )->count();

        return view('dashboard.inovasi.evidence', compact('inovasi','items','totalWeight','doneCount'));
    }

    /**
     * Simpan Evidence (pemilik/admin).
     */
    public function evidenceSave(Request $r, Inovasi $inovasi, EvidenceRepository $evidenceRepo)
    {
        $this->authorizeOwnerOrAdmin($inovasi);

        $paramIds   = $r->input('param_id', []);
        $labels     = $r->input('parameter_label', []);
        $weights    = $r->input('parameter_weight', []);
        $deskripsis = $r->input('deskripsi', []);
        $linkUrls   = $r->input('link_url', []);

        $rows = [];
        for ($no = 1; $no <= 20; $no++) {
            $rows[] = [
                'no'                => $no,
                'param_id'          => $paramIds[$no] ?? null,
                'parameter_label'   => $labels[$no] ?? null,
                'parameter_weight'  => $weights[$no] ?? null,
                'deskripsi'         => $deskripsis[$no] ?? null,
                'link_url'          => $linkUrls[$no] ?? null,
            ];
        }

        $files = [];
        for ($no = 1; $no <= 20; $no++) {
            if ($r->hasFile("file_{$no}")) {
                $files[$no] = $r->file("file_{$no}");
            }
        }

        $evidenceRepo->upsertBulk($inovasi->id, $rows, $files);

        return redirect()
            ->route('evidence.form', $inovasi->id)
            ->with('success','Evidence berhasil disimpan.');
    }

    /**
     * Edit metadata inovasi (pemilik/admin).
     */
    public function edit(int $id)
    {
        $inovasi = $this->repo->find($id);
        $this->authorizeOwnerOrAdmin($inovasi);

        return view('dashboard.inovasi.edit', compact('inovasi'));
    }

    /**
     * Update metadata inovasi + file (pemilik/admin).
     */
    public function update(Request $r, int $id)
    {
        $inovasi = $this->repo->find($id);
        $this->authorizeOwnerOrAdmin($inovasi);

        $data = $r->validate([
            'judul'                 => ['required','string','max:255'],
            'opd_unit'              => ['nullable','string','max:255'],
            'inisiator_daerah'      => ['nullable','string','max:255'],
            'inisiator_nama'        => ['nullable','string','max:255'],
            'koordinat'             => ['nullable','string','max:255'],
            'klasifikasi'           => ['nullable','string','max:255'],
            'jenis_inovasi'         => ['nullable','string','max:255'],
            'bentuk_inovasi_daerah' => ['nullable','string','max:255'],
            'asta_cipta'            => ['nullable','string','max:255'],
            'program_prioritas'     => ['nullable','string','max:255'],
            'urusan_pemerintah'     => ['nullable','string','max:255'],
            'waktu_uji_coba'        => ['nullable','date'],
            'waktu_penerapan'       => ['nullable','date'],

            'rancang_bangun'        => ['nullable','string'],
            'tujuan'                => ['nullable','string'],
            'manfaat'               => ['nullable','string'],
            'hasil_inovasi'         => ['nullable','string'],

            'anggaran'              => ['nullable','file','mimes:pdf','max:10240'],
            'profil_bisnis'         => ['nullable','file','mimes:ppt,pptx,pdf','max:20480'],
            'haki'                  => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:10240'],
            'penghargaan'           => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:10240'],
        ]);

        $this->repo->update(
            $id,
            $data,
            $r->file('anggaran'),
            $r->file('profil_bisnis'),
            $r->file('haki'),
            $r->file('penghargaan')
        );

        return redirect()->route('sigap-inovasi.show',$id)->with('success','Metadata inovasi berhasil diperbarui.');
    }

      /**
     * Aksi: Update Asistensi (admin/verifikator saja)
     */
    public function asistensiUpdate(Request $r, int $id)
    {
        $this->authorizeAdmin(); // atau buat gate khusus "verifikator" jika ada

        $data = $r->validate([
            'status' => ['required','in:Menunggu Verifikasi,Disetujui,Dikembalikan,Revisi,Ditolak'],
            'note'   => ['nullable','string','max:5000'],
        ]);

        // Wajib isi note jika bukan Disetujui/Menunggu
        if (in_array($data['status'], ['Dikembalikan','Revisi','Ditolak']) && empty(trim($data['note'] ?? ''))) {
            return back()->withErrors(['note'=>'Catatan wajib diisi untuk status '.$data['status']])->withInput();
        }

        $this->repo->updateAsistensi($id, $data['status'], $data['note'] ?? null, Auth::id());
        return back()->with('success', 'Status asistensi diperbarui.');
    }

    // ===== Helpers
    private function authorizeOwnerOrAdmin(Inovasi $inv): void
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) return;
        if ($inv->user_id === $user->id) return;
        abort(403, 'Anda tidak berhak mengakses inovasi ini.');
    }
    private function authorizeAdmin(): void
    {
        if (!Auth::user()->hasRole('admin')) abort(403, 'Akses khusus admin/verifikator.');
    }

 
    
}
