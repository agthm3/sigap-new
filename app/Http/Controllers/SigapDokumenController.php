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
     * Tampilan 1: DOKUMEN UMUM (Katalog Terbuka & Internal Pegawai)
     */
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'category', 'year']);

        // Ambil folder root dengan status public dan internal
        $foldersQuery = Folder::whereIn('visibility', ['public', 'internal'])
            ->whereNull('parent_id')
            ->withCount([
                'documents' => function ($q) {
                    $q->whereIn('sensitivity', ['public', 'internal']);
                },
                'subfolders' => function ($q) {
                    $q->whereIn('visibility', ['public', 'internal']);
                }
            ]);

        if (!empty($filters['q'])) {
            $kw = trim($filters['q']);
            $foldersQuery->where(function ($q) use ($kw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('classification_code', 'like', "%{$kw}%");
            });
        }

        $folders = $foldersQuery->latest()->get();

        // Dokumen lepas (root) berstatus public & internal
        $docs = $this->repo->paginate(
            filters: array_merge($filters, ['root_only' => true]),
            perPage: 10,
            mode: 'public'
        );

        return view('dashboard.dokumen.index', compact('folders', 'docs'));
    }

    /**
     * Tampilan 2: DOKUMEN SAYA (Folder, Subfolder, Dokumen Pribadi & Terkunci)
     */
    public function saya(Request $request)
    {
        $user = Auth::user();

        // Ambil folder tingkat atas milik user yang sedang login
        $folders = Folder::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->withCount('documents', 'subfolders')
            ->latest()
            ->get();

        // Dokumen milik user yang belum masuk ke folder mana pun
        $docs = $this->repo->paginate(
            filters: array_merge($request->only(['q', 'category', 'year']), ['root_only' => true]),
            perPage: 12,
            userId: $user->id,
            mode: 'saya'
        );

        return view('dashboard.dokumen.saya', compact('folders', 'docs'));
    }

    /**
     * Form Unggah Dokumen Baru
     */
    public function create(Request $request)
    {
        $folderId = $request->query('folder_id');
        $folder = null;

        if ($folderId) {
            $folderQuery = Folder::where('id', $folderId);
            // Jika bukan admin, pastikan user hanya bisa memilih folder miliknya atau folder non-private
            if (!Auth::user()->hasRole('admin')) {
                $folderQuery->where(function ($q) {
                    $q->where('user_id', Auth::id())
                      ->orWhereIn('visibility', ['public', 'internal']);
                });
            }
            $folder = $folderQuery->first();
        }

        $existingTags = $this->repo->getExistingTags();

        return view('dashboard.dokumen.upload', compact('folder', 'folderId', 'existingTags'));
    }

    /**
     * Endpoint Asinkron Temporary Upload
     */
    public function tempUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // Maksimal 20MB
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $filename = 'tmp_' . Str::random(25) . '.' . $extension;

        // Simpan sementara di storage
        $path = $file->storeAs('temp_uploads', $filename, 'public');

        return response()->json([
            'success'   => true,
            'temp_path' => $path,
            'name'      => $file->getClientOriginalName(),
            'size'      => $file->getSize(),
        ]);
    }

    /**
     * Simpan Dokumen Permanen via Form Payload
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
            'sensitivity'     => ['required', 'in:public,internal,private'],
            'files'           => ['nullable', 'array'],
            'files.*'         => ['string'],
            'file'            => ['nullable', 'file', 'max:20480'],
        ]);

        $userId = Auth::id() ?? 1;
        $validated['created_by'] = $validated['updated_by'] = $userId;
        $validated['folder_id'] = $validated['folder_id'] ?? null;

        // ATURAN REVISI 1: Jika di dalam folder, sensitivity MUTLAK mengikuti visibility folder
        if (!empty($validated['folder_id'])) {
            $parentFolder = Folder::find($validated['folder_id']);
            if ($parentFolder) {
                $validated['sensitivity'] = $parentFolder->visibility;
            }
        }

        // Simpan dokumen jika melalui antarmuka multi-upload asinkron
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

            // Arahkan kembali ke halaman folder terkait jika ada, atau ke katalog sesuai statusnya
            $redirectRoute = !empty($validated['folder_id'])
                ? route('sigap-dokumen.folder.show', $validated['folder_id'])
                : ($validated['sensitivity'] === 'private' ? route('sigap-dokumen.saya') : route('sigap-dokumen.index'));

            return redirect($redirectRoute)->with('success', "{$createdCount} Dokumen berhasil diunggah dan diindeks!");
        }

        // Fallback untuk single upload reguler
        $doc = $this->repo->create(
            $validated,
            $request->file('file'),
            $request->file('thumb')
        );

        $target = !empty($validated['folder_id'])
            ? route('sigap-dokumen.folder.show', $validated['folder_id'])
            : ($doc->sensitivity === 'private' ? route('sigap-dokumen.saya') : route('sigap-dokumen.index'));

        return redirect($target)->with('success', "Dokumen '{$doc->title}' berhasil disimpan!");
    }

    /**
     * Pratinjau & Detail Dokumen
     */
    public function show(ModelsDocument $document)
    {
        $this->authorizeDocumentAccess($document);

        ActivityLogger::log(
            module: 'dokumen',
            action: 'view',
            object: $document
        );

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
     * Streaming inline preview (PDF / Foto)
     */
    public function preview(ModelsDocument $document)
    {
        $this->authorizeDocumentAccess($document);

        // Cari letak disk penyimpanan berkas (private storage vault vs public storage)
        $disk = 'public';
        if (Storage::disk('private')->exists($document->file_path)) {
            $disk = 'private';
        }

        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di penyimpanan server.');
        }

        $content = Storage::disk($disk)->get($document->file_path);
        $mime = Storage::disk($disk)->mimeType($document->file_path);

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . ($document->alias ?? $document->title) . '"');
    }

    /**
     * Unduh Berkas
     */
    public function download(ModelsDocument $document)
    {
        $this->authorizeDocumentAccess($document);

        $disk = 'public';
        if (Storage::disk('private')->exists($document->file_path)) {
            $disk = 'private';
        }

        if (!Storage::disk($disk)->exists($document->file_path)) {
            ActivityLogger::log('dokumen', 'access_denied', $document, [
                'success' => false,
                'reason' => 'file_missing',
            ]);
            abort(404, 'Berkas fisik tidak ditemukan.');
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

        if ($doc->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        $fileUrl = route('sigap-dokumen.preview', $doc);
        $thumbUrl = $doc->thumb_path ? asset('storage/' . $doc->thumb_path) : null;
        return view('dashboard.dokumen.edit', compact('doc', 'fileUrl', 'thumbUrl'));
    }

    public function update(Request $request, int $id)
    {
        $doc = $this->repo->find($id);

        if ($doc->created_by !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

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
            'sensitivity'   => ['required', 'in:public,internal,private'],
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

    /**
     * Helper proteksi akses dokumen berdasarkan 3 Level Sensitivitas
     */
    private function authorizeDocumentAccess(ModelsDocument $document): void
    {
        $user = Auth::user();

        // 1. Dokumen PRIVATE: Hanya pemilik dan admin
        if ($document->sensitivity === 'private') {
            if (!$user || ($document->created_by !== $user->id && !$user->hasRole('admin'))) {
                abort(403, 'Akses Ditolak: Dokumen ini berstatus PRIVAT.');
            }
        }

        // 2. Dokumen INTERNAL: Wajib memiliki role employee atau admin
        if ($document->sensitivity === 'internal') {
            if (!$user || !$user->hasAnyRole(['employee', 'admin'])) {
                abort(403, 'Akses Terbatas: Dokumen ini hanya diperuntukkan bagi internal pegawai BRIDA.');
            }
        }
    }
}