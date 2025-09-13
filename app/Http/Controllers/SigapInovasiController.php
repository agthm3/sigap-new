<?php

namespace App\Http\Controllers;

use App\Models\Inovasi;
use App\Repositories\EvidenceRepository;
use App\Repositories\InovasiRepository;
use Illuminate\Http\Request;

class SigapInovasiController extends Controller
{
    public function __construct(private InovasiRepository $repo)
    {
        
    }
    public function index(Request $request)
    {
        $filters = [
                'q'            => $request->get('q'),
                'inisiator'    => $request->get('f_inisiator'),
                'tahap'        => $request->get('f_tahap_inovasi'),
                'tahap_status' => $request->get('f_tahap_status'),
                'sort'         => $request->get('sort','terbaru'),
            ];

        $items = $this->repo->paginate($filters, 25);
        return view('dashboard.inovasi.index', compact('filters', 'items'));
    }

    public function konfigurasi()
    {
        return view('dashboard.inovasi.konfigurasi');
    }

    public function dashboard()
    {
        return view('dashboard.inovasi.dashboard');
    }

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
            'tahap_inovasi'       => ['nullable','string','max:50'],
            'rancang_bangun'        => ['nullable','string'],
            'tujuan'                => ['nullable','string'],
            'manfaat'               => ['nullable','string'],
            'hasil_inovasi'         => ['nullable','string'],
            'perkembangan_inovasi'  => ['nullable','string','max:255'],
        ]);

        $this->repo->create(
            $data,
            $r->file('anggaran'),
            $r->file('profil_bisnis'),
            $r->file('haki'),
            $r->file('penghargaan')
        );

        return redirect()->route('sigap-inovasi.index')->with('success', 'Inovasi berhasil ditambahkan.');

    }

    public function destroy($id)
    {
        $this->repo->delete($id);
        return redirect()->route('sigap-inovasi.index')->with('success', 'Inovasi berhasil dihapus.');
    }

    public function show(int $id, EvidenceRepository $evidenceRepo)
    {
        $inovasi = $this->repo->find($id);

        // Ambil status tahapan (fallback 'Belum')
        $tInis  = $inovasi->t_inisiatif   ?? $inovasi->t_inisiatif_status   ?? 'Belum';
        $tUji   = $inovasi->t_uji_coba    ?? $inovasi->t_uji_status         ?? 'Belum';
        $tTerap = $inovasi->t_penerapan   ?? $inovasi->t_penerapan_status   ?? 'Belum';

        // Progress (versi tahapan, seperti sebelumnya)
        $steps = collect([$tInis,$tUji,$tTerap])->filter(fn($s)=> strcasecmp((string)$s,'Belum') !== 0 && !empty($s))->count();
        $progressPct = (int) round($steps / 3 * 100);

        // Evidence (langsung dari repo)
        $evItems     = collect($evidenceRepo->listForInovasi($inovasi->id));   // array -> collect untuk gampang ngitung
        $evTotal     = $evidenceRepo->totalWeight($inovasi->id);
        $evFilled    = $evItems->where('selected_weight','>',0)->count();
        $evFiles     = $evItems->filter(fn($r)=> !empty($r['file_name']))->count();

        return view('dashboard.inovasi.show', compact(
            'inovasi','tInis','tUji','tTerap','progressPct',
            'evItems','evTotal','evFilled','evFiles'
        ));
    }
    public function evidenceForm(Inovasi $inovasi, EvidenceRepository $evidenceRepo)
    {
        $items       = $evidenceRepo->listForInovasi($inovasi->id);   // <- array murni
        $totalWeight = $evidenceRepo->totalWeight($inovasi->id);
        $doneCount   = collect($items)->filter(fn($i) =>
                        !empty($i['selected_label']) || (($i['selected_weight'] ?? 0) > 0)
                    )->count();

        return view('dashboard.inovasi.evidence', compact('inovasi','items','totalWeight','doneCount'));
    }

    public function evidenceSave(Request $r, Inovasi $inovasi, EvidenceRepository $evidenceRepo)
    {
        // Ambil array input dari form (keyed by nomor indikator)
        $paramIds   = $r->input('param_id', []);           // [no => param_id]
        $labels     = $r->input('parameter_label', []);    // opsional (kalau mau manual)
        $weights    = $r->input('parameter_weight', []);   // opsional (kalau mau manual)
        $deskripsis = $r->input('deskripsi', []);          // [no => text]
        $linkUrls   = $r->input('link_url', []);           // [no => url]

        // Susun payload untuk repository
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

        // Map file input: file_1..file_20
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

    public function edit(int $id)
    {
        $inovasi = $this->repo->find($id);
        return view('dashboard.inovasi.edit', compact('inovasi'));
    }

    public function update(Request $r, int $id)
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

            'rancang_bangun'        => ['nullable','string'],
            'tujuan'                => ['nullable','string'],
            'manfaat'               => ['nullable','string'],
            'hasil_inovasi'         => ['nullable','string'],

            'anggaran'              => ['nullable','file','mimes:pdf','max:10240'],
            'profil_bisnis'         => ['nullable','file','mimes:ppt,pptx,pdf','max:20480'],
            'haki'                  => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:10240'],
            'penghargaan'           => ['nullable','file','mimes:pdf,jpg,jpeg,png','max:10240'],
        ]);

        // lewat repository (konsisten dengan store())
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
}
