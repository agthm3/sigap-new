<?php

namespace App\Http\Controllers;

use App\Models\Evidence;
use App\Models\EvidenceFile;
use App\Models\EvidenceGuide;
use App\Models\Inovasi;
use App\Models\InovasiReviewItem;
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
    public function index(Request $request, EvidenceRepository $evidenceRepo)
    {
        $user = Auth::user();

        $filters = [
            'q'            => $request->get('q'),
            'tahap_status' => $request->get('f_tahap_status'),
            'sort'         => $request->get('sort','terbaru'),
            'tahap' => $request->get('tahap'),
            'urusan' => $request->get('urusan'),
            'inisiator' => $request->get('inisiator'),
            'asistensi_status' => $request->get('asistensi_status'),
        ];  
        

        $evidenceNoteTemplate = $evidenceRepo->evidenceChecklistText();
        $items = $this->repo->paginateForUser($user, $filters, 25);

        if (!empty($filters['asistensi_status'])) {
        $items->setCollection(
            $items->getCollection()->filter(function ($inv) use ($filters) {

                $reviewItems = $inv->reviewItems ?? collect();

                if ($reviewItems->contains('status', 'tolak')) {
                    $status = 'Ditolak';
                } elseif ($reviewItems->contains('status', 'revisi')) {
                    $status = 'Revisi';
                } elseif ($reviewItems->isNotEmpty() && $reviewItems->every(fn($r) => $r->status === 'accept')) {
                    $status = 'Disetujui';
                } else {
                    $status = $inv->asistensi_status ?? 'Menunggu Verifikasi';
                }

                return $status === $filters['asistensi_status'];
            })
        );
    }

        // Eager load reviewItems untuk ditampilkan di kolom inovator
        $items->load(['reviewItems', 'evidenceReviewItems']);
        // dd($filters);
        return view('dashboard.inovasi.index', compact('filters', 'items', 'evidenceNoteTemplate'));
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
                'misi_walikota' => ['required','integer','between:1,7'],
                'videos' => ['required','array','min:3','max:5'],
                'videos.*.judul' => ['required','string','max:255'],
                'videos.*.url' => ['required','url','max:500'],
                'videos.*.deskripsi' => ['nullable','string'],
            ]);

            // set pemilik
            $data['user_id'] = Auth::id();

            $inovasi = $this->repo->create(
                $data,
                $r->file('anggaran'),
                $r->file('profil_bisnis'),
                $r->file('haki'),
                $r->file('penghargaan')
            );

            foreach ($r->videos as $video) {
                $inovasi->referensiVideos()->create([
                    'judul' => $video['judul'],
                    'deskripsi' => $video['deskripsi'] ?? null,
                    'video_url' => $video['url'],
            ]);
    }

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

        $evMax = $evidenceRepo->maxTotalWeight();

        // Indikator Warna
        if ($evTotal <= 30) {
            $statusTeks = 'Kurang';
            $badgeColor = 'bg-red-50  text-red-700 inset-ring inset-ring-red-600/10';
        } elseif ($evTotal > 30 && $evTotal <= 60) {
            $statusTeks = 'Cukup';
            $badgeColor = 'bg-yellow-50  text-yellow-700 inset-ring inset-ring-yellow-600/20'; // Kuning/Oranye
        } elseif ($evTotal > 60 && $evTotal <= 80) {
            $statusTeks = 'Baik';
            $badgeColor = 'bg-blue-50  text-blue-700 inset-ring inset-ring-blue-700/20'; // Biru
        } else {
            $statusTeks = 'Sangat Baik';
            $badgeColor = 'bg-green-50  text-green-700 inset-ring inset-ring-green-600/20'; // Hijau
        }

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
        $referensiVideos = $inovasi->referensiVideos()->get();

        return view('dashboard.inovasi.show', compact(
            'inovasi','tInis','tUji','tTerap','progressPct',
            'evItems','evTotal','evFilled','evFiles','mainFiles','referensiVideos'
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

        // ── Ambil semua review evidence untuk inovasi ini ──
        $evReviews = \App\Models\EvidenceReviewItem::where('inovasi_id', $inovasi->id)
            ->with('reviewer')
            ->get()
            ->groupBy('no');  // key: no (1–20), value: collection of reviews

        return view('dashboard.inovasi.evidence', compact(
            'inovasi','items','totalWeight','doneCount',
            'evReviews'   // ← baru
        ));
    }

    /**
     * Simpan Evidence (pemilik/admin).
     */
    public function evidenceSave(Request $r, Inovasi $inovasi, EvidenceRepository $evidenceRepo)
    {
        $this->authorizeOwnerOrAdmin($inovasi);

        /**
         * 1. SIMPAN DATA EVIDENCE (PARAMETER)
         */
        $paramIds   = $r->input('param_id', []);
        $labels     = $r->input('parameter_label', []);
        $weights    = $r->input('parameter_weight', []);
        $deskripsis = $r->input('deskripsi', []);
        $linkUrls   = $r->input('link_url', []);

        $rows = [];
        for ($no = 1; $no <= 20; $no++) {
            $rows[] = [
                'no'               => $no,
                'param_id'         => $paramIds[$no] ?? null,
                'parameter_label'  => $labels[$no] ?? null,
                'parameter_weight' => $weights[$no] ?? null,
                'deskripsi'        => $deskripsis[$no] ?? null,
                'link_url'         => $linkUrls[$no] ?? null,
            ];
        }

        // ✅ SIMPAN EVIDENCE SAJA
        $evidenceRepo->upsertBulk($inovasi->id, $rows);

        /**
         * 2. HAPUS FILE YANG DITANDAI
         */
        $deleteFiles = $r->input('delete_files', []);
        $evidenceRepo->deleteMarkedFiles($inovasi->id, $deleteFiles);

        /**
         * 3. SIMPAN DOKUMEN (docs)
         */
        $docs      = $r->input('docs', []);
        $docFiles = $r->file('docs', []);

        foreach ($docs as $no => $items) {

            $evidence = Evidence::where('inovasi_id', $inovasi->id)
                ->where('no', $no)
                ->first();

            if (!$evidence) continue;

            foreach ($items as $idx => $meta) {

                if (!isset($docFiles[$no][$idx]['file'])) continue;

                $file = $docFiles[$no][$idx]['file'];

                $path = $file->store(
                    "inovasi/{$inovasi->id}/evidence/no-{$no}",
                    'public'
                );

                EvidenceFile::create([
                    'evidence_id'   => $evidence->id,
                    'file_path'     => $path,
                    'file_name'     => $file->getClientOriginalName(),
                    'file_mime'     => $file->getClientMimeType(),
                    'file_size'     => $file->getSize(),

                    // METADATA
                    'nomor_surat'   => $meta['nomor']   ?? null,
                    'tanggal_surat' => $meta['tanggal'] ?? null,
                    'tentang'       => $meta['tentang'] ?? null,
                ]);
            }
        }

        return redirect()
            ->route('evidence.form', $inovasi->id)
            ->with('success', 'Evidence berhasil disimpan.');
    }



    /**
     * Edit metadata inovasi (pemilik/admin).
     */
    public function edit(int $id)
    {
        $inovasi = $this->repo->find($id);
        $this->authorizeOwnerOrAdmin($inovasi);
    // ambil semua review
        $reviews = InovasiReviewItem::where('inovasi_id', $id)
            ->with('reviewer')
            ->get();

        // group by field
        $reviewByField = $reviews->groupBy('field');

        // group reviewer
        $reviewers = $reviews->groupBy('reviewer_id');

        return view('dashboard.inovasi.edit', compact(
            'inovasi',
            'reviewByField',
            'reviewers'
        ));
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

            'refs' => ['nullable','array','min:3','max:5'],
            'refs.*.id' => ['nullable','integer'],
            'refs.*.judul' => ['required','string','max:255'],
            'refs.*.url' => ['required','url','max:500'],
            'refs.*.deskripsi' => ['nullable','string'],

        ]);

        $this->repo->update(
            $id,
            $data,
            $r->file('anggaran'),
            $r->file('profil_bisnis'),
            $r->file('haki'),
            $r->file('penghargaan')
        );
        $idsKeep = [];

        foreach ($r->input('refs', []) as $ref) {
            $video = $inovasi->referensiVideos()->updateOrCreate(
                ['id' => $ref['id'] ?? null],
                [
                    'judul' => $ref['judul'],
                    'deskripsi' => $ref['deskripsi'] ?? null,
                    'video_url' => $ref['url'],
                ]
            );
            $idsKeep[] = $video->id;
        }

        // hapus referensi yang tidak dikirim
        $inovasi->referensiVideos()
            ->whereNotIn('id', $idsKeep)
            ->delete();

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

    public function pedomanEvidence()
    {
        // ── Evidence (20 indikator) ──
        $guides = EvidenceGuide::all()->keyBy('no');

        $evidenceItems = collect(range(1, 20))->map(function ($no) use ($guides) {
            $g = $guides->get($no);
            return [
                'id'        => $g?->id,
                'no'        => $no,
                'indikator' => $g?->indikator ?? "Evidence {$no}",
                'deskripsi' => $g?->deskripsi ?? null,
                'file_url'  => $g && $g->file_path
                    ? Storage::disk('public')->url($g->file_path)
                    : null,
                'file_name' => $g?->file_name,
                'video_url' => $g?->video_url ?? null,  // kolom baru (nullable)
            ];
        });

        // ── Metadata (field-field inovasi) ──
        $metadataItems = [
            ['key'=>'judul',               'label'=>'Judul Inovasi',               'deskripsi'=>'Tuliskan nama inovasi secara singkat, jelas, dan deskriptif.'],
            ['key'=>'opd_unit',            'label'=>'OPD/Unit',                    'deskripsi'=>'Nama Organisasi Perangkat Daerah atau unit kerja pengusul inovasi.'],
            ['key'=>'inisiator_daerah',    'label'=>'Inisiator (Daerah)',           'deskripsi'=>'Pilih jenis inisiator: OPD, Unit Kerja, atau Kolaborasi.'],
            ['key'=>'inisiator_nama',      'label'=>'Nama Inisiator',              'deskripsi'=>'Nama lengkap individu atau tim yang menjadi penggagas inovasi.'],
            ['key'=>'koordinat',           'label'=>'Koordinat',                   'deskripsi'=>'Koordinat lokasi penerapan inovasi dalam format latitude,longitude.'],
            ['key'=>'klasifikasi',         'label'=>'Klasifikasi Inovasi',         'deskripsi'=>'Pilih klasifikasi: Inovasi Perangkat Daerah, Desa/Kelurahan, atau Masyarakat.'],
            ['key'=>'jenis_inovasi',       'label'=>'Jenis Inovasi',               'deskripsi'=>'Pilih jenis inovasi: Digital atau Non Digital.'],
            ['key'=>'bentuk_inovasi_daerah','label'=>'Bentuk Inovasi Daerah',      'deskripsi'=>'Pilih bentuk: Pelayanan Publik, Tata Kelola Pemerintahan, atau lainnya.'],
            ['key'=>'asta_cipta',          'label'=>'Asta Cipta',                  'deskripsi'=>'Pilih program Asta Cipta yang relevan dengan inovasi.'],
            ['key'=>'program_prioritas',   'label'=>'Program Prioritas Walikota',  'deskripsi'=>'Pilih program prioritas Walikota yang didukung inovasi ini.'],
            ['key'=>'misi_walikota',       'label'=>'Misi Walikota',               'deskripsi'=>'Pilih misi Walikota (1-7) yang paling relevan dengan inovasi.'],
            ['key'=>'urusan_pemerintah',   'label'=>'Urusan Pemerintah',           'deskripsi'=>'Pilih urusan pemerintahan yang menjadi lingkup inovasi.'],
            ['key'=>'waktu_uji_coba',      'label'=>'Waktu Uji Coba',             'deskripsi'=>'Tanggal mulai uji coba inovasi dilakukan.'],
            ['key'=>'waktu_penerapan',     'label'=>'Waktu Penerapan',             'deskripsi'=>'Tanggal mulai inovasi diterapkan secara resmi.'],
            ['key'=>'perkembangan_inovasi','label'=>'Perkembangan Inovasi',        'deskripsi'=>'Apakah sudah ada perkembangan nyata dari inovasi ini?'],
            ['key'=>'rancang_bangun',      'label'=>'Rancang Bangun',              'deskripsi'=>'Jelaskan desain dan arsitektur inovasi secara detail (min. 300 karakter).'],
            ['key'=>'tujuan',              'label'=>'Tujuan Inovasi',              'deskripsi'=>'Uraikan tujuan utama yang ingin dicapai melalui inovasi ini.'],
            ['key'=>'manfaat',             'label'=>'Manfaat Inovasi',             'deskripsi'=>'Jelaskan manfaat konkret bagi masyarakat dan pemerintah.'],
            ['key'=>'hasil_inovasi',       'label'=>'Hasil Inovasi',               'deskripsi'=>'Deskripsikan output dan outcome nyata dari penerapan inovasi.'],
            ['key'=>'videos',              'label'=>'Referensi Penelitian/Video',   'deskripsi'=>'Cantumkan minimal 3 dan maksimal 5 referensi penelitian atau video terkait inovasi.'],
            ['key'=>'anggaran',            'label'=>'Dokumen Anggaran',            'deskripsi'=>'Upload dokumen anggaran dalam format PDF (maks. 10MB).'],
            ['key'=>'profil_bisnis',       'label'=>'Profil Bisnis',               'deskripsi'=>'Upload profil bisnis dalam format PPT atau PDF (maks. 20MB).'],
            ['key'=>'haki',                'label'=>'Dokumen HAKI',                'deskripsi'=>'Upload dokumen Hak Atas Kekayaan Intelektual jika ada.'],
            ['key'=>'penghargaan',         'label'=>'Penghargaan',                 'deskripsi'=>'Upload dokumen penghargaan yang pernah diterima inovasi ini.'],
        ];

        // Ambil video_url dari tabel pedoman_metadata (buat tabel baru atau gunakan config)
        // Untuk sementara bisa simpan di config atau session
        // Kita pakai model baru: InovasiPedomanMeta
        $pedomanMeta = \App\Models\InovasiPedomanMeta::all()->keyBy('field_key');

        return view('dashboard.inovasi.evidence-pedoman', compact(
            'evidenceItems',
            'metadataItems',
            'pedomanMeta',
        ));
    }

    public function pedomanMetaSave(Request $r)
    {
        $this->authorizeAdmin();

        foreach ($r->input('meta', []) as $key => $data) {
            \App\Models\InovasiPedomanMeta::updateOrCreate(
                ['field_key' => $key],
                [
                    'deskripsi' => $data['deskripsi'] ?? null,
                    'video_url' => $data['video_url'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Pedoman metadata berhasil disimpan.');
    }

    public function pedomanEvidenceSave(Request $r)
    {
        $this->authorizeAdmin();

        foreach ($r->input('no', []) as $idx => $no) {
            $guide = EvidenceGuide::firstOrNew(['no' => $no]);
            $guide->indikator = $r->indikator[$idx] ?? $guide->indikator;
            $guide->deskripsi = $r->deskripsi[$idx] ?? $guide->deskripsi;
            $guide->video_url = $r->video_url[$idx] ?? $guide->video_url; // ← tambah ini

            if ($r->hasFile("file.$idx")) {
                if ($guide->file_path) {
                    Storage::disk('public')->delete($guide->file_path);
                }
                $file = $r->file("file.$idx");
                $path = $file->store('evidence-pedoman', 'public');
                $guide->file_path = $path;
                $guide->file_name = $file->getClientOriginalName();
            }

            $guide->save();
        }

        return back()->with('success', 'Pedoman evidence berhasil disimpan.');
    }

    public function pedomanEvidenceDelete(EvidenceGuide $guide)
    {
        $this->authorizeAdmin();

        if ($guide->file_path) {
            Storage::disk('public')->delete($guide->file_path);
        }

        $guide->update([
            'file_path' => null,
            'file_name' => null,
        ]);

        return back()->with('success','File pedoman berhasil dihapus.');
    }

}
