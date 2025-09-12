<?php

namespace App\Http\Controllers;

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
                'bentuk'       => $request->get('f_bentuk'),
                'urusan'       => $request->get('f_urusan'),
                'inisiator'    => $request->get('f_inisiator'),
                'tahap'        => $request->get('f_tahap'),
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
            'bentuk'                => ['nullable','string','max:255'],
            'urusan'                => ['nullable','string','max:255'],
            'inisiator'             => ['nullable','string','max:255'],
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
            'tahap_inisiatif'       => ['nullable','string','max:50'],
            'tahap_uji_coba'        => ['nullable','string','max:50'],
            'tahap_penerapan'       => ['nullable','string','max:50'],
            'rancang_bangun'        => ['nullable','string'],
            'tujuan'                => ['nullable','string'],
            'manfaat'               => ['nullable','string'],
            'hasil_inovasi'         => ['nullable','string'],
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

 public function show(int $id)
    {
        $inovasi = $this->repo->find($id);

        // Ambil status tahapan dari kolom yang ada (fallback ke 'Belum' bila null)
        $tInis  = $inovasi->t_inisiatif   ?? $inovasi->t_inisiatif_status   ?? 'Belum';
        $tUji   = $inovasi->t_uji_coba    ?? $inovasi->t_uji_status         ?? 'Belum';
        $tTerap = $inovasi->t_penerapan   ?? $inovasi->t_penerapan_status   ?? 'Belum';

        // Hitung progres sederhana (tanpa evidence): setiap tahap != 'Belum' dihitung 1 langkah
        $steps = collect([$tInis,$tUji,$tTerap])->filter(fn($s)=> strcasecmp((string)$s,'Belum') !== 0 && !empty($s))->count();
        $progressPct = (int) round($steps / 3 * 100);

        return view('dashboard.inovasi.show', [
            'inovasi'     => $inovasi,
            'tInis'       => $tInis,
            'tUji'        => $tUji,
            'tTerap'      => $tTerap,
            'progressPct' => $progressPct,
        ]);
    }
}
