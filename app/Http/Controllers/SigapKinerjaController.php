<?php

namespace App\Http\Controllers;

use App\Repositories\KinerjaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SigapKinerjaController extends Controller
{
    public function __construct(private KinerjaRepository $repo) {}

    /**
     * INDEX — menampilkan data tersimpan dgn filter bulanan (mode default)
     * - Menerima filter: q, category (kode), rhk (kode), month (YYYY-MM)
     * - Mengirim ke view: items (sudah di-map label), categoryOptions (kode+label), rhksByCategory (untuk dependent dropdown)
     */
    public function index(Request $request)
    {
        $cats = config('kinerja.categories', []);
        $categoryOrder = config('kinerja.category_order', array_keys($cats));

        // Opsi kategori terurut (kode + label panjang)
        $categoryOptions = collect($categoryOrder)
            ->filter(fn($code) => isset($cats[$code]))
            ->map(fn($code) => [
                'code'  => $code,
                'label' => $cats[$code]['label'],
            ])->values()->all();

        // Map kategori -> { code, label, rhks: [kode => label] }
        $rhksByCategory = collect($cats)->map(function($v, $k){
            return [
                'code'  => $k,
                'label' => $v['label'] ?? $k,
                'rhks'  => $v['rhks']  ?? [],
            ];
        })->values()->all();

        $filters = $request->only(['q','category','rhk','month']);
        $itemsPage = $this->repo->paginateForIndex($filters, 24);

        // mapping ke struktur untuk grid (kode → label panjang)
        $items = collect($itemsPage->items())->map(function($m) use ($cats){
            $catCode  = $m->category;
            $rhkCode  = $m->rhk;
            $catLabel = $cats[$catCode]['label'] ?? $catCode;
            $rhkLabel = $cats[$catCode]['rhks'][$rhkCode] ?? $rhkCode;

            return [
                'id'          => $m->id,
                'title'       => $m->title,
                'category'    => $catLabel,
                'rhk'         => $rhkLabel,
                'description' => $m->description,
                'date'        => optional($m->activity_date)->toDateString(),
                'thumb_url'   => $this->repo->fileUrl($m->thumb_path), // null → blade fallback dummy
            ];
        })->all();

        // role admin? (sesuaikan dengan spatie/roles yg kamu pakai)
        $isAdminDemo = auth()->check() && method_exists(auth()->user(), 'hasRole')
            ? auth()->user()->hasRole('admin')
            : true;

        return view('kinerja.index', [
            'items'           => $items,
            'isAdminDemo'     => $isAdminDemo,
            'itemsPage'       => $itemsPage,      // untuk pagination links()
            'categoryOptions' => $categoryOptions,
            'rhksByCategory'  => $rhksByCategory,
        ]);
    }

    /**
     * STORE — simpan bukti; jika file image → auto jadi thumb; jika bukan → thumb opsional
     * - Menyimpan "kode" kategori & rhk ke DB
     */
    public function store(Request $request)
    {
        $cats = config('kinerja.categories', []);
        $catCodes = array_keys($cats);

        $data = $request->validate([
            'category'    => ['required', 'in:'.implode(',', $catCodes)], // kode kategori
            'rhk'         => ['nullable', 'string'],                      // kode rhk (nanti divalidasi cross-field)
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date'        => ['required', 'date'],        // Y-m-d
            'file'        => ['required', 'file', 'max:10240'], // 10MB
            'thumb'       => ['nullable', 'image', 'max:4096'], // 4MB
        ]);

        // Validasi silang: rhk (jika ada) harus terdaftar di kategori terpilih
        if (!empty($data['rhk'])) {
            $rhkCodes = array_keys($cats[$data['category']]['rhks'] ?? []);
            abort_unless(in_array($data['rhk'], $rhkCodes, true), 422, 'RHK tidak valid untuk kategori terpilih.');
        }

        $file  = $request->file('file');   /** @var \Illuminate\Http\UploadedFile $file */
        $thumb = $request->file('thumb');  /** @var \Illuminate\Http\UploadedFile|null $thumb */

        $this->repo->create($data, $file, $thumb);

        return back()->with('success', 'Bukti kinerja berhasil diunggah.');
    }

    /**
     * SHOW PUBLIK — preview per item (dipakai tombol “Lihat” & untuk share)
     */
    public function publicShow(int $id)
    {
        $cats = config('kinerja.categories', []);
        $m = $this->repo->findOrFail($id);

        $catLabel = $cats[$m->category]['label'] ?? $m->category;
        $rhkLabel = $cats[$m->category]['rhks'][$m->rhk] ?? $m->rhk;

        $item = [
            'id'          => $m->id,
            'title'       => $m->title,
            'category'    => $catLabel,
            'rhk'         => $rhkLabel,
            'description' => $m->description,
            'date'        => optional($m->activity_date)->toDateString(),
            'file_url'    => $this->repo->fileUrl($m->file_path),
            'thumb_url'   => $this->repo->fileUrl($m->thumb_path),
            'public_url'  => route('sigap-kinerja.public', $m->id),
            'file_mime'   => $m->file_mime,
        ];

        return view('kinerja.show', compact('item'));
    }

    /**
     * ANNUAL PUBLIC — 1 tautan berisi semua bukti dlm satu tahun (filter opsional: category, rhk, q)
     * route: /kinerja/y/{year}
     */
    public function annualPublic(int $year, Request $request)
    {
        abort_unless($year >= 2000 && $year <= 2100, 404);

        $cats = config('kinerja.categories', []);
        $filters = [
            'category' => $request->query('category'), // kode
            'rhk'      => $request->query('rhk'),      // kode
            'q'        => $request->query('q'),        // search
        ];

        // Validasi opsional: jika category/rhk ada, pastikan valid
        if (!empty($filters['category']) && !isset($cats[$filters['category']])) {
            abort(422, 'Kategori tidak dikenal.');
        }
        if (!empty($filters['rhk'])) {
            $catCode = $filters['category'];
            // jika rhk ada tapi category kosong → boleh? Kita izinkan, tapi cari di semua kategori (opsional).
            if ($catCode && !isset($cats[$catCode]['rhks'][$filters['rhk']])) {
                abort(422, 'RHK tidak sesuai kategori.');
            }
        }

        // Ambil semua item tahun tsb (tanpa pagination) via repository
        $rows = $this->repo->listForAnnual($year, $filters);

        // Map untuk tabel annual_public: tampilkan label panjang
        $items = collect($rows)->map(function($m) use ($cats) {
            $catLabel = $cats[$m->category]['label'] ?? $m->category;
            $rhkLabel = $cats[$m->category]['rhks'][$m->rhk] ?? $m->rhk;
            return [
                'id'        => $m->id,
                'date'      => optional($m->activity_date)->toDateString(),
                'category'  => $catLabel,
                'rhk'       => $rhkLabel,
                'title'     => $m->title,
                'link'      => route('sigap-kinerja.public', $m->id),
            ];
        })->values()->all();

        // Info untuk header/crumbs
        $meta = [
            'year'     => $year,
            'category' => !empty($filters['category']) ? ($cats[$filters['category']]['label'] ?? $filters['category']) : null,
            'rhk'      => null,
            'q'        => $filters['q'] ?? null,
        ];
        if (!empty($filters['rhk'])) {
            if (!empty($filters['category'])) {
                $meta['rhk'] = $cats[$filters['category']]['rhks'][$filters['rhk']] ?? $filters['rhk'];
            } else {
                // jika rhk disuplai tanpa kategori, coba cari label di semua kategori
                foreach ($cats as $c) {
                    if (isset($c['rhks'][$filters['rhk']])) {
                        $meta['rhk'] = $c['rhks'][$filters['rhk']];
                        break;
                    }
                }
                $meta['rhk'] = $meta['rhk'] ?? $filters['rhk'];
            }
        }

        return view('kinerja.annual_public', compact('items', 'meta', 'year'));
    }
}
