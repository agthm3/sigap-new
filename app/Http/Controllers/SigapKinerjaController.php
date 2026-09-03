<?php

namespace App\Http\Controllers;

use App\Repositories\KinerjaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use ZipArchive; 

class SigapKinerjaController extends Controller
{
    public function __construct(private KinerjaRepository $repo) {}

    /**
     * INDEX — menampilkan data tersimpan dgn filter bulanan (mode default)
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

        // mapping ke struktur untuk grid
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
                'thumb_url'   => $this->repo->fileUrl($m->thumb_path),
            ];
        })->all();

        $isAdminDemo = auth()->check() && method_exists(auth()->user(), 'hasAnyRole')
            ? auth()->user()->hasAnyRole(['admin', 'verif_kinerja'])
            : false;

        return view('kinerja.index', [
            'items'           => $items,
            'isAdminDemo'     => $isAdminDemo,
            'itemsPage'       => $itemsPage,
            'categoryOptions' => $categoryOptions,
            'rhksByCategory'  => $rhksByCategory,
        ]);
    }

    /**
     * API ENDPOINT: UPLOAD MEDIA (ASYNC)
     * Menerima 1 file per request, aman dari post_max_size 2MB
     */
    public function uploadMedia(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $file = $request->file('file');
        // Simpan sementara di disk public/kinerja-temp
        $path = $file->store('kinerja-temp', 'public');

        return response()->json([
            'status'   => 'success',
            'path'     => $path,
            'filename' => $file->getClientOriginalName(),
            'mime'     => $file->getClientMimeType(),
        ]);
    }

    /**
     * STORE — simpan data teks dan proses file-file temporary
     * Dipanggil via AJAX JSON setelah semua file beres di-upload
     */
    public function store(Request $request)
    {
        $cats = config('kinerja.categories', []);
        $catCodes = array_keys($cats);

        $rules = [
            'category'        => ['required', 'in:'.implode(',', $catCodes)],
            'rhk'             => ['nullable', 'string'],
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'date'            => ['required', 'date'],
            'uploaded_paths'  => ['required', 'array', 'min:1', 'max:30'],
            'uploaded_paths.*'=> ['required', 'string'],
        ];

        $data = $request->validate($rules);

        // Validasi silang RHK
        if (!empty($data['rhk'])) {
            $rhkCodes = array_keys($cats[$data['category']]['rhks'] ?? []);
            abort_unless(in_array($data['rhk'], $rhkCodes, true), 422, 'RHK tidak valid untuk kategori terpilih.');
        }

        // Konversi path temporary kembali menjadi instance UploadedFile
        $files = [];
        foreach ($data['uploaded_paths'] as $tempPath) {
            $fullPath = Storage::disk('public')->path($tempPath);
            
            if (file_exists($fullPath)) {
                $files[] = new UploadedFile(
                    $fullPath,
                    basename($tempPath),
                    Storage::disk('public')->mimeType($tempPath),
                    null,
                    true // Parameter $test = true agar mem-bypass fungsi is_uploaded_file() bawaan PHP
                );
            }
        }

        // Jalankan logic simpan bawaan repository
        $this->repo->create($data, $files, null);

        // Hapus file temporary yang sudah dicopy/diproses oleh repo
        foreach ($data['uploaded_paths'] as $tempPath) {
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->delete($tempPath);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Bukti kinerja berhasil diunggah.',
            'redirect'=> route('sigap-kinerja.index')
        ]);
    }

    /**
     * SHOW PUBLIK — preview per item
     */
    public function publicShow(int $id)
    {
        $cats = config('kinerja.categories', []);
        $m = $this->repo->findOrFail($id);

        $catLabel = $cats[$m->category]['label'] ?? $m->category;
        $rhkLabel = $cats[$m->category]['rhks'][$m->rhk] ?? $m->rhk;

        $media = [];
        if (method_exists($m, 'media')) {
            $media = $m->media()
                ->orderByDesc('is_primary')
                ->oldest()
                ->get()
                ->map(function($mm){
                    $url = $this->repo->fileUrl($mm->path);
                    return [
                        'url'        => $url,
                        'mime'       => $mm->mime,
                        'is_image'   => (bool) $mm->is_image,
                        'is_primary' => (bool) $mm->is_primary,
                        'filename'   => basename($mm->path),
                    ];
                })->values()->all();
        }

        $item = [
            'id'          => $m->id,
            'title'       => $m->title,
            'category'    => $catLabel,
            'rhk'         => $rhkLabel,
            'description' => $m->description,
            'date'        => optional($m->activity_date)->toDateString(),
            'file_url'    => $this->repo->fileUrl($m->file_path),
            'file_mime'   => $m->file_mime,
            'thumb_url'   => $this->repo->fileUrl($m->thumb_path),
            'media'       => $media,
            'public_url'  => route('sigap-kinerja.public', $m->id),
        ];

        return view('kinerja.show', compact('item'));
    }

    public function downloadImages(int $id)
    {
        $m = $this->repo->findOrFail($id);

        $images = [];

        if (method_exists($m, 'media')) {
            foreach ($m->media()->where('is_image', true)->get() as $mm) {
                $images[] = $mm->path;
            }
        }

        if (empty($images) && $m->file_mime && str_starts_with(strtolower($m->file_mime), 'image/')) {
            $images[] = $m->file_path;
        }

        if (empty($images)) {
            return back()->with('error', 'Tidak ada gambar untuk diunduh.');
        }

        $safeTitle = Str::slug($m->title ?: 'kinerja');
        $zipName   = $safeTitle.'-images-'.now()->format('Ymd_His').'.zip';
        $tmpDir    = storage_path('app/tmp');
        if (!is_dir($tmpDir)) @mkdir($tmpDir, 0775, true);
        $zipPath   = $tmpDir.DIRECTORY_SEPARATOR.$zipName;

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat arsip ZIP.');
        }

        foreach ($images as $index => $relPath) {
            $absPath = Storage::disk('public')->path($relPath);
            if (!is_file($absPath)) continue;

            $basename = basename($relPath);
            $entryName = sprintf('%02d-%s', $index+1, $basename);

            $zip->addFile($absPath, $entryName);
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    public function annualPublic(int $year, Request $request)
    {
        abort_unless($year >= 2000 && $year <= 2100, 404);

        $cats = config('kinerja.categories', []);
        $filters = [
            'category' => $request->query('category'),
            'rhk'      => $request->query('rhk'),
            'q'        => $request->query('q'),
        ];

        if (!empty($filters['category']) && !isset($cats[$filters['category']])) {
            abort(422, 'Kategori tidak dikenal.');
        }
        if (!empty($filters['rhk'])) {
            $catCode = $filters['category'];
            if ($catCode && !isset($cats[$catCode]['rhks'][$filters['rhk']])) {
                abort(422, 'RHK tidak sesuai kategori.');
            }
        }

        $rows = $this->repo->listForAnnual($year, $filters);

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

    public function destroy(int $id, Request $request)
    {
        $u = auth()->user();
        
        $isAuthorized = $u && method_exists($u, 'hasAnyRole') 
            ? $u->hasAnyRole(['admin', 'verif_kinerja']) 
            : false;
            
        abort_unless($isAuthorized, 403, 'Unauthorized');

        $this->repo->delete($id);

        return back()->with('success', 'Bukti kinerja berhasil dihapus.');
    }
}