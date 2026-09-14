<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Document as ModelsDocument;
use App\Models\Folder;
use App\Repositories\DocumentRepository;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SigapDokumenController extends Controller
{
    public function __construct(private DocumentRepository $repo)
    {
    }

    /**
     * Tampilan 1: DOKUMEN UMUM (Publik)
     */
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'category', 'year']);

        // Ambil folder publik root
        $folders = Folder::where('visibility', 'public')
            ->whereNull('parent_id')
            ->withCount(['documents' => function ($q) {
                $q->where('sensitivity', 'public');
            }, 'subfolders' => function ($q) {
                $q->where('visibility', 'public');
            }])
            ->latest()
            ->get();

        // Dokumen umum lepas (root) yang belum masuk ke folder mana pun
        $docs = $this->repo->paginate(
            filters: array_merge($filters, ['root_only' => true]),
            perPage: 10,
            mode: 'public'
        );

        return view('dashboard.dokumen.index', compact('folders', 'docs'));
    }

    /**
     * Tampilan 2: DOKUMEN SAYA (Folder, Subfolder, Dokumen Pribadi)
     */
    public function saya(Request $request)
    {
        $user = Auth::user();

        // Ambil folder tingkat atas (root/tanpa parent_id)
        $folders = Folder::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->withCount('documents', 'subfolders')
            ->latest()
            ->get();

        // Dokumen milik user yang belum masuk ke folder mana pun (root files)
        $docs = $this->repo->paginate(
            filters: array_merge($request->only(['q', 'category', 'year']), ['root_only' => true]),
            perPage: 12,
            userId: $user->id,
            mode: 'saya'
        );

        return view('dashboard.dokumen.saya', compact('folders', 'docs'));
    }

    /**
     * Form Baru: Upload Dokumen (Blade tersendiri, bukan modal pop-up)
     */
    public function create(Request $request)
    {
        $folderId = $request->query('folder_id');
        $folder = $folderId ? Folder::where('id', $folderId)->where('user_id', Auth::id())->first() : null;
        $existingTags = $this->repo->getExistingTags();

        return view('dashboard.dokumen.upload', compact('folder', 'folderId', 'existingTags'));
    }

    /**
     * Endpoint Async Temporary Upload (Per-file kebal limit 2MB via Client-side compression)
     */
    public function tempUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // Validasi per-request file
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $filename = 'tmp_' . Str::random(25) . '.' . $extension;

        // Simpan ke direktori sementara di public disk
        $path = $file->storeAs('temp_uploads', $filename, 'public');

        return response()->json([
            'success'   => true,
            'temp_path' => $path,
            'name'      => $file->getClientOriginalName(),
            'size'      => $file->getSize(),
        ]);
    }

    /**
     * Simpan Dokumen Permanen via Payload Metadata Form (bisa menerima multi-file dari async upload)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number'          => ['nullable', 'string', 'max:255'],
            'doc_date'        => ['nullable', 'date'],
            'title'           => ['required', 'string', 'max:255'],
            'alias'           => ['nullable', 'string', 'max:255', 'unique:documents,alias'],
            'year'            => ['required', 'integer', 'between:1900,' . ((int)date('Y') + 1)],
            'category'        => ['required', 'string', 'max:100'],
            'stakeholder'     => ['nullable', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'folder_id'       => ['nullable', 'exists:folders,id'],
            'physical_rack'   => ['nullable', 'string', 'max:100'],
            'physical_row'    => ['nullable', 'string', 'max:100'],
            'tags'            => ['nullable', 'string'],
            'sensitivity'     => ['required', 'in:public,private'],
            'files'           => ['nullable', 'array'],
            'files.*'         => ['string'],
            'file'            => ['nullable', 'file', 'max:20480'],
        ]);

        $userId = Auth::id() ?? 1;
        $validated['created_by'] = $validated['updated_by'] = $userId;
        
        // Pastikan key folder_id selalu ada meski null
        $validated['folder_id'] = $validated['folder_id'] ?? null;

        // Jika upload melalui antarmuka asinkron (mengirim path temp)
        if (!empty($validated['files']) && is_array($validated['files'])) {
            $createdCount = 0;
            foreach ($validated['files'] as $index => $tempFilePath) {
                $docData = $validated;
                $docData['temp_file_path'] = $tempFilePath;

                if ($index > 0) {
                    $docData['title'] = $validated['title'] . ' (Bagian ' . ($index + 1) . ')';
                    $docData['alias'] = null;
                }

                $this->repo->create($docData);
                $createdCount++;
            }

            // Redirect aman: jika tidak ada folder_id, arahkan ke dokumen umum atau dokumen saya
            $redirectRoute = !empty($validated['folder_id'])
                ? route('sigap-dokumen.folder.show', $validated['folder_id'])
                : (($validated['sensitivity'] ?? 'public') === 'private' ? route('sigap-dokumen.saya') : route('sigap-dokumen.index'));

            return redirect($redirectRoute)->with('success', "{$createdCount} Dokumen berhasil diunggah!");
        }

        // Fallback upload reguler
        $doc = $this->repo->create(
            $validated,
            $request->file('file'),
            $request->file('thumb')
        );

        $target = $doc->sensitivity === 'private' ? route('sigap-dokumen.saya') : route('sigap-dokumen.index');
        return redirect($target)->with('success', "Dokumen '{$doc->title}' berhasil disimpan!");
    }
    /**
     * Show Detail Dokumen (Dengan proteksi otorisasi jika berstatus private)
     */
    public function show(ModelsDocument $document)
    {
        if ($document->sensitivity === 'private') {
            if (!Auth::check() || ($document->created_by !== Auth::id() && !Auth::user()->hasRole('admin'))) {
                abort(403, 'Dokumen ini berstatus PRIVAT dan terkunci. Hanya pemilik yang dapat mengakses.');
            }
        }

        ActivityLogger::log(
            module: 'dokumen',
            action: 'view',
            object: $document
        );

        // Menggunakan streaming preview controller agar berkas di folder private tetap bisa tampil
        $fileUrl = route('sigap-dokumen.preview', $document);
        $thumbUrl = $document->thumb_path ? asset('storage/' . $document->thumb_path) : null;

        $ext = strtolower(pathinfo($document->file_path, PATHINFO_EXTENSION));
        $isPdf = $ext === 'pdf';
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        $logs = ActivityLog::where('module', 'dokumen')
            ->where('object_type', ModelsDocument::class)
            ->where('object_id', $document->id)
            ->latest()
            ->paginate(10);

        return view('dashboard.dokumen.show', compact('document', 'fileUrl', 'thumbUrl', 'isPdf', 'isImage', 'logs'));
    }

    /**
     * Streaming in-line preview (PDF / Gambar) dengan cek otorisasi aman
     */
    public function preview(ModelsDocument $document)
    {
        if ($document->sensitivity === 'private') {
            if (!Auth::check() || ($document->created_by !== Auth::id() && !Auth::user()->hasRole('admin'))) {
                abort(403, 'Akses Ditolak: Dokumen berstatus privat.');
            }
        }

        // Deteksi disk penyimpanan (private atau public)
        $disk = 'public';
        if ($document->sensitivity === 'private' && Storage::disk('private')->exists($document->file_path)) {
            $disk = 'private';
        } elseif (!Storage::disk('public')->exists($document->file_path) && Storage::disk('private')->exists($document->file_path)) {
            $disk = 'private';
        }

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan.');
        }

        $content = Storage::disk($disk)->get($document->file_path);
        $mime = Storage::disk($disk)->mimeType($document->file_path);

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . ($document->alias ?? $document->title) . '"');
    }

    /**
     * Download Dokumen (Membaca dinamis dari disk private maupun public)
     */
    public function download(ModelsDocument $document)
    {
        if ($document->sensitivity === 'private') {
            if (!Auth::check() || ($document->created_by !== Auth::id() && !Auth::user()->hasRole('admin'))) {
                ActivityLogger::log('dokumen', 'access_denied', $document, [
                    'success' => false,
                    'reason' => 'unauthorized_private_download',
                ]);
                abort(403, 'Akses unduh ditolak. Dokumen terkunci.');
            }
        }

        // Deteksi disk penyimpanan
        $disk = 'public';
        if ($document->sensitivity === 'private' && Storage::disk('private')->exists($document->file_path)) {
            $disk = 'private';
        } elseif (!Storage::disk('public')->exists($document->file_path) && Storage::disk('private')->exists($document->file_path)) {
            $disk = 'private';
        }

        if (!Storage::disk($disk)->exists($document->file_path)) {
            ActivityLogger::log('dokumen', 'access_denied', $document, [
                'success' => false,
                'reason' => 'file_missing',
            ]);
            abort(404, 'File tidak ditemukan di penyimpanan server.');
        }

        ActivityLogger::log('dokumen', 'download', $document);

        $ext = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $filename = ($document->alias ?? $document->title) . '.' . $ext;

        return Storage::disk($disk)->download($document->file_path, $filename);
    }

    public function destroy(int $id)
    {
        $doc = $this->repo->find($id);

        if ($doc->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus dokumen ini.');
        }

        $this->repo->delete($id);
        return back()->with('success', 'Dokumen berhasil dihapus!');
    }

    public function edit(int $id)
    {
        $doc = $this->repo->find($id);
        $fileUrl = route('sigap-dokumen.preview', $doc);
        $thumbUrl = $doc->thumb_path ? asset('storage/' . $doc->thumb_path) : null;
        return view('dashboard.dokumen.edit', compact('doc', 'fileUrl', 'thumbUrl'));
    }

    public function update(Request $request, int $id)
    {
        $doc = $this->repo->find($id);

        $validated = $request->validate([
            'number'        => ['nullable', 'string', 'max:255'],
            'title'         => ['required', 'string', 'max:255'],
            'alias'         => ['nullable', 'string', 'max:255', 'unique:documents,alias,' . $doc->id],
            'year'          => ['required', 'integer', 'between:1900,' . ((int)date('Y') + 1)],
            'category'      => ['required', 'string', 'max:100'],
            'description'   => ['nullable', 'string'],
            'physical_rack' => ['nullable', 'string', 'max:100'],
            'physical_row'  => ['nullable', 'string', 'max:100'],
            'tags'          => ['nullable', 'string'],
            'sensitivity'   => ['required', 'in:public,private'],
            'file'          => ['nullable', 'file', 'max:20480'],
            'thumb'         => ['nullable', 'image', 'max:4096'],
            'stakeholder'   => ['nullable', 'string', 'max:255'],
        ]);

        $validated['updated_by'] = Auth::id() ?? 1;

        $updated = $this->repo->update(
            $id,
            $validated,
            $request->file('file'),
            $request->file('thumb')
        );

        return redirect()
            ->route('sigap-dokumen.edit', $updated->id)
            ->with('success', "Dokumen '{$updated->title}' berhasil diperbarui!");
    }
}