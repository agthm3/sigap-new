<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\RisetRepository;
use Illuminate\Validation\Rule;

class RisetController extends Controller
{
    public function __construct(private RisetRepository $repo) {}

    public function index()
    {
        $risets = $this->repo->paginateLatest(10);
        return view('dashboard.riset.index', compact('risets'));
    }

    public function dashboard()
    {
        return view('dashboard.riset.dashboard');
    }

    public function show()
    {
        return view('dashboard.riset.create');
    }

    public function create()
    {
        return view('dashboard.riset.create');
    }

    public function store(Request $request)
    {
        // VALIDASI
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'year'      => 'required|integer|min:1900|max:'.(now()->year+1),
            'type'      => 'nullable|in:internal,kolaborasi,eksternal',
            'abstract'  => 'required|string',
            'method'    => 'nullable|string|max:1000',

            // arrays
            'authors'                 => 'required|array|min:1',
            'authors.*.name'          => 'required|string|max:255',
            'authors.*.affiliation'   => 'nullable|string|max:255',
            'authors.*.role'          => 'nullable|string|max:100',
            'authors.*.orcid'         => 'nullable|string|max:30',

            'corresponding'           => 'nullable|array',
            'corresponding.name'      => 'nullable|string|max:255',
            'corresponding.email'     => 'nullable|email|max:255',
            'corresponding.phone'     => 'nullable|string|max:50',

            'tags'                    => 'nullable|array',
            'tags.*'                  => 'nullable|string|max:60',
            'stakeholders'            => 'nullable|array',
            'stakeholders.*'          => 'nullable|string|max:120',

            'doi'       => 'nullable|string|max:100',
            'ojs_url'   => 'nullable|url|max:255',
            'funding'   => 'nullable|string|max:255',
            'ethics'    => 'nullable|string|max:500',

            'version'       => 'nullable|string|max:20',
            'release_note'  => 'nullable|string|max:255',

            'access'        => 'required|in:Public,Restricted',
            'access_reason' => 'required_if:access,Restricted|nullable|string|max:500',
            'license'       => 'nullable|string|max:60',

            'pdf_file'  => 'required|file|mimes:pdf|max:20480',
            'thumbnail' => 'nullable|image|max:2048',

            'datasets'                  => 'nullable|array',
            'datasets.*.label'          => 'nullable|string|max:255',
            'datasets.*.file'           => 'nullable|file|max:20480', // 20MB masing-masing
        ], [
            'authors.required' => 'Minimal satu penulis.',
            'authors.*.name.required' => 'Nama penulis wajib diisi.',
            'access_reason.required_if' => 'Alasan akses wajib diisi jika Restricted.',
        ]);

        // SIMPAN FILE UTAMA
        $pdf = $request->file('pdf_file');
        $pdfPath = $pdf->store('research/pdf', 'public');

        $thumbPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('research/thumb', 'public')
            : null;

        // DATASET/LAMPIRAN
        $datasets = [];
        if ($request->has('datasets')) {
            foreach ($request->input('datasets', []) as $idx => $d) {
                $file = $request->file("datasets.$idx.file");
                if ($file) {
                    $stored = $file->store('research/datasets', 'public');
                    $datasets[] = [
                        'label'         => $d['label'] ?? $file->getClientOriginalName(),
                        'path'          => $stored,
                        'original_name' => $file->getClientOriginalName(),
                        'size'          => $this->formatBytes($file->getSize()),
                    ];
                } elseif (!empty($d['label'])) {
                    // label tanpa file—tetap simpan sebagai catatan
                    $datasets[] = [
                        'label' => $d['label'],
                        'path'  => null,
                        'original_name' => null,
                        'size'  => null,
                    ];
                }
            }
        }

        // PERSIAPKAN PAYLOAD
        $payload = [
            'title'     => $data['title'],
            'year'      => $data['year'],
            'type'      => $data['type'] ?? null,
            'abstract'  => $data['abstract'],
            'method'    => $data['method'] ?? null,

            'authors'       => $data['authors'],
            'corresponding' => $data['corresponding'] ?? null,
            'tags'          => $data['tags'] ?? [],
            'stakeholders'  => $data['stakeholders'] ?? [],

            'doi'       => $data['doi'] ?? null,
            'ojs_url'   => $data['ojs_url'] ?? null,
            'funding'   => $data['funding'] ?? null,
            'ethics'    => $data['ethics'] ?? null,

            'version'       => $data['version'] ?? null,
            'release_note'  => $data['release_note'] ?? null,

            'access'        => $data['access'],
            'access_reason' => $data['access_reason'] ?? null,
            'license'       => $data['license'] ?? null,

            'file_path'     => $pdfPath,
            'file_name'     => $pdf->getClientOriginalName(),
            'file_size'     => $this->formatBytes($pdf->getSize()),
            'thumbnail_path'=> $thumbPath,

            'datasets'      => $datasets,
            'created_by'    => $request->user()?->id,
        ];

        $this->repo->create($payload);

        return redirect()->route('riset.index')->with('success', 'Riset berhasil disimpan.');
    }


    private function formatBytes($bytes, $precision = 1): string
    {
        $units = ['B','KB','MB','GB','TB'];
        $bytes = max($bytes, 0);
        $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }


    
    public function edit(int $id)
    {
        $r = $this->repo->findOrFail($id);

        // Normalisasi fallback untuk form
        $r->authors       = is_array($r->authors) ? $r->authors : [];
        $r->corresponding = is_array($r->corresponding) ? $r->corresponding : [];
        $r->tags          = is_array($r->tags) ? $r->tags : [];
        $r->stakeholders  = is_array($r->stakeholders) ? $r->stakeholders : [];
        $r->datasets      = is_array($r->datasets) ? $r->datasets : [];

        return view('dashboard.riset.edit', ['r' => $r]);
    }

    public function update(Request $request, int $id)
    {
        $existing = $this->repo->findOrFail($id);

        // VALIDASI (pdf & thumbnail opsional saat update)
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'year'      => 'required|integer|min:1900|max:'.(now()->year+1),
            'type'      => ['nullable', Rule::in(['internal','kolaborasi','eksternal'])],
            'abstract'  => 'required|string',
            'method'    => 'nullable|string|max:1000',

            'authors'               => 'required|array|min:1',
            'authors.*.name'        => 'required|string|max:255',
            'authors.*.affiliation' => 'nullable|string|max:255',
            'authors.*.role'        => 'nullable|string|max:100',
            'authors.*.orcid'       => 'nullable|string|max:30',

            'corresponding'           => 'nullable|array',
            'corresponding.name'      => 'nullable|string|max:255',
            'corresponding.email'     => 'nullable|email|max:255',
            'corresponding.phone'     => 'nullable|string|max:50',

            'tags'           => 'nullable|array',
            'tags.*'         => 'nullable|string|max:60',
            'stakeholders'   => 'nullable|array',
            'stakeholders.*' => 'nullable|string|max:120',

            'doi'       => 'nullable|string|max:100',
            'ojs_url'   => 'nullable|url|max:255',
            'funding'   => 'nullable|string|max:255',
            'ethics'    => 'nullable|string|max:500',

            'version'       => 'nullable|string|max:20',
            'release_note'  => 'nullable|string|max:255',

            'access'        => ['required', Rule::in(['Public','Restricted'])],
            'access_reason' => 'required_if:access,Restricted|nullable|string|max:500',
            'license'       => 'nullable|string|max:60',

            'pdf_file'  => 'nullable|file|mimes:pdf|max:20480',
            'thumbnail' => 'nullable|image|max:2048',

            // datasets: boleh hapus yg lama + tambah baru
            'datasets_delete'       => 'nullable|array',
            'datasets_delete.*'     => 'nullable|integer', // index array lama yang dihapus

            'datasets_existing'                 => 'nullable|array', // label edit yg lama (opsional)
            'datasets_existing.*.label'         => 'nullable|string|max:255',

            'datasets_new'                      => 'nullable|array',
            'datasets_new.*.label'              => 'nullable|string|max:255',
            'datasets_new.*.file'               => 'nullable|file|max:20480',
        ], [
            'authors.required' => 'Minimal satu penulis.',
            'authors.*.name.required' => 'Nama penulis wajib diisi.',
            'access_reason.required_if' => 'Alasan akses wajib diisi jika Restricted.',
        ]);

        // REPLACE PDF jika ada
        $pdfPath   = $existing->file_path;
        $fileName  = $existing->file_name;
        $fileSize  = $existing->file_size;

        if ($request->hasFile('pdf_file')) {
            $pdf = $request->file('pdf_file');
            $pdfPath  = $pdf->store('research/pdf', 'public');
            $fileName = $pdf->getClientOriginalName();
            $fileSize = $this->formatBytes($pdf->getSize());

            // (opsional) hapus file lama
            if ($existing->file_path && \Storage::disk('public')->exists($existing->file_path)) {
                \Storage::disk('public')->delete($existing->file_path);
            }
        }

        // REPLACE thumbnail jika ada
        $thumbPath = $existing->thumbnail_path;
        if ($request->hasFile('thumbnail')) {
            $thumb = $request->file('thumbnail');
            $thumbPath = $thumb->store('research/thumb','public');

            if ($existing->thumbnail_path && \Storage::disk('public')->exists($existing->thumbnail_path)) {
                \Storage::disk('public')->delete($existing->thumbnail_path);
            }
        }

        // DATASETS: start dari yang lama
        $currentDatasets = is_array($existing->datasets) ? $existing->datasets : [];

        // Hapus yang ditandai
        $toDeleteIdx = collect($request->input('datasets_delete', []))->filter(fn($v)=>$v!==null)->map(fn($v)=>(int)$v)->values()->all();
        if (!empty($toDeleteIdx)) {
            foreach ($toDeleteIdx as $idx) {
                if (isset($currentDatasets[$idx])) {
                    $item = $currentDatasets[$idx];
                    if (!empty($item['path']) && Storage::disk('public')->exists($item['path'])) {
                        Storage::disk('public')->delete($item['path']);
                    }
                    unset($currentDatasets[$idx]);
                }
            }
            $currentDatasets = array_values($currentDatasets);
        }

        // Edit label yang lama (opsional)
        foreach ($request->input('datasets_existing', []) as $idx => $ex) {
            if (isset($currentDatasets[$idx]) && array_key_exists('label', $ex)) {
                $currentDatasets[$idx]['label'] = $ex['label'] ?: ($currentDatasets[$idx]['label'] ?? null);
            }
        }

        // Tambah baru
        foreach ($request->input('datasets_new', []) as $nIdx => $n) {
            $file = $request->file("datasets_new.$nIdx.file");
            if ($file) {
                $stored = $file->store('research/datasets', 'public');
                $currentDatasets[] = [
                    'label'         => $n['label'] ?? $file->getClientOriginalName(),
                    'path'          => $stored,
                    'original_name' => $file->getClientOriginalName(),
                    'size'          => $this->formatBytes($file->getSize()),
                ];
            } elseif (!empty($n['label'])) {
                $currentDatasets[] = [
                    'label' => $n['label'],
                    'path'  => null,
                    'original_name' => null,
                    'size'  => null,
                ];
            }
        }

        $payload = [
            'title'     => $data['title'],
            'year'      => $data['year'],
            'type'      => $data['type'] ?? null,
            'abstract'  => $data['abstract'],
            'method'    => $data['method'] ?? null,

            'authors'       => $data['authors'],
            'corresponding' => $data['corresponding'] ?? null,
            'tags'          => $data['tags'] ?? [],
            'stakeholders'  => $data['stakeholders'] ?? [],

            'doi'       => $data['doi'] ?? null,
            'ojs_url'   => $data['ojs_url'] ?? null,
            'funding'   => $data['funding'] ?? null,
            'ethics'    => $data['ethics'] ?? null,

            'version'       => $data['version'] ?? null,
            'release_note'  => $data['release_note'] ?? null,

            'access'        => $data['access'],
            'access_reason' => $data['access_reason'] ?? null,
            'license'       => $data['license'] ?? null,

            'file_path'     => $pdfPath,
            'file_name'     => $fileName,
            'file_size'     => $fileSize,
            'thumbnail_path'=> $thumbPath,

            'datasets'      => $currentDatasets,
        ];

        $this->repo->update($id, $payload);

        return redirect()->route('riset.index')->with('success', 'Riset berhasil diperbarui.');
    }
}
