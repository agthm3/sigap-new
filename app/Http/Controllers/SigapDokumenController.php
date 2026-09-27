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
   /**
     * Tampilan 1: DOKUMEN UMUM (Katalog Terbuka & Internal Pegawai)
     */
    public function index(Request $request)
    {
        $filters = $request->only(['q', 'category', 'year']);
        $hasFilter = !empty($filters['q']) || !empty($filters['category']) || !empty($filters['year']);

        // Hak akses visibilitas: Tamu hanya 'public', Pegawai & Admin bisa 'public' + 'internal'
        $allowedVisibilities = (Auth::check() && Auth::user()->hasAnyRole(['employee', 'admin'])) 
            ? ['public', 'internal'] 
            : ['public'];

        // 1. QUERY FOLDER ROOT (Hanya kolom yang ada di tabel folders)
        $foldersQuery = Folder::whereIn('visibility', $allowedVisibilities)
            ->whereNull('parent_id')
            ->withCount([
                'documents' => function ($q) use ($allowedVisibilities) {
                    $q->whereIn('sensitivity', $allowedVisibilities);
                },
                'subfolders' => function ($q) use ($allowedVisibilities) {
                    $q->whereIn('visibility', $allowedVisibilities);
                }
            ]);

        if (!empty($filters['q'])) {
            $kw = trim($filters['q']);
            $cleanKw = ltrim($kw, '#');

            $foldersQuery->where(function ($q) use ($kw, $cleanKw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('name', 'like', "%{$cleanKw}%")
                  ->orWhere('classification_code', 'like', "%{$kw}%");
            });
        }

        $folders = $foldersQuery->latest()->get();

        // 2. QUERY DOKUMEN
        // Jika ada filter/pencarian, telusuri SEMUA dokumen (termasuk di dalam subfolder terdalam)
        // Jika tanpa filter, tampilkan dokumen lepas saja (whereNull folder_id)
        $docsQuery = ModelsDocument::with('folder')
            ->whereIn('sensitivity', $allowedVisibilities);

        if (!$hasFilter) {
            $docsQuery->whereNull('folder_id');
        }

        if (!empty($filters['q'])) {
            $rawKw = trim($filters['q']);
            $cleanKw = ltrim($rawKw, '#');

            $docsQuery->where(function ($q) use ($rawKw, $cleanKw) {
                $q->where('title', 'like', "%{$rawKw}%")
                  ->orWhere('title', 'like', "%{$cleanKw}%")
                  ->orWhere('alias', 'like', "%{$rawKw}%")
                  ->orWhere('number', 'like', "%{$rawKw}%")
                  ->orWhere('description', 'like', "%{$rawKw}%")
                  ->orWhere('stakeholder', 'like', "%{$rawKw}%")
                  // Pencarian Tag (String match & MySQL JSON Array Contains)
                  ->orWhere('tags', 'like', "%{$cleanKw}%")
                  ->orWhereJsonContains('tags', $cleanKw)
                  ->orWhereJsonContains('tags', strtoupper($cleanKw))
                  ->orWhereJsonContains('tags', strtolower($cleanKw))
                  ->orWhereJsonContains('tags', ucfirst(strtolower($cleanKw)));
            });
        }

        if (!empty($filters['category'])) {
            $docsQuery->where('category', $filters['category']);
        }

        if (!empty($filters['year'])) {
            $docsQuery->where('year', $filters['year']);
        }

        $docs = $docsQuery->latest()->paginate(10)->withQueryString();

        // 3. DAFTAR FOLDER UNTUK MODAL "PINDAHKAN FOLDER"
        $allFolders = Folder::whereIn('visibility', $allowedVisibilities)
            ->select('id', 'name', 'visibility')
            ->get();

        return view('dashboard.dokumen.index', compact('folders', 'docs', 'allFolders', 'hasFilter'));
    }

    /**
     * Tampilan 2: DOKUMEN SAYA (Folder, Subfolder, Dokumen Pribadi & Terkunci)
     */
    public function saya(Request $request)
    {
        $user = Auth::user();
        $filters = $request->only(['q', 'category', 'year']);
        $hasFilter = !empty($filters['q']) || !empty($filters['category']) || !empty($filters['year']);

        // 1. QUERY FOLDER MILIK USER LOGIN
        $foldersQuery = Folder::where('user_id', $user->id)
            ->whereNull('parent_id')
            ->withCount('documents', 'subfolders');

        if (!empty($filters['q'])) {
            $kw = trim($filters['q']);
            $cleanKw = ltrim($kw, '#');

            $foldersQuery->where(function ($q) use ($kw, $cleanKw) {
                $q->where('name', 'like', "%{$kw}%")
                  ->orWhere('name', 'like', "%{$cleanKw}%")
                  ->orWhere('classification_code', 'like', "%{$kw}%");
            });
        }

        $folders = $foldersQuery->latest()->get();

        // 2. QUERY DOKUMEN MILIK USER LOGIN
        // Jika ada filter/pencarian, telusuri SEMUA dokumen milik user (termasuk di dalam subfolder)
        $docsQuery = ModelsDocument::with('folder')
            ->where('created_by', $user->id);

        if (!$hasFilter) {
            $docsQuery->whereNull('folder_id');
        }

        if (!empty($filters['q'])) {
            $rawKw = trim($filters['q']);
            $cleanKw = ltrim($rawKw, '#');

            $docsQuery->where(function ($q) use ($rawKw, $cleanKw) {
                $q->where('title', 'like', "%{$rawKw}%")
                  ->orWhere('title', 'like', "%{$cleanKw}%")
                  ->orWhere('alias', 'like', "%{$rawKw}%")
                  ->orWhere('number', 'like', "%{$rawKw}%")
                  ->orWhere('description', 'like', "%{$rawKw}%")
                  ->orWhere('stakeholder', 'like', "%{$rawKw}%")
                  // Pencarian Tag (String match & MySQL JSON Array Contains)
                  ->orWhere('tags', 'like', "%{$cleanKw}%")
                  ->orWhereJsonContains('tags', $cleanKw)
                  ->orWhereJsonContains('tags', strtoupper($cleanKw))
                  ->orWhereJsonContains('tags', strtolower($cleanKw))
                  ->orWhereJsonContains('tags', ucfirst(strtolower($cleanKw)));
            });
        }

        if (!empty($filters['category'])) {
            $docsQuery->where('category', $filters['category']);
        }

        if (!empty($filters['year'])) {
            $docsQuery->where('year', $filters['year']);
        }

        $docs = $docsQuery->latest()->paginate(12)->withQueryString();

        // 3. DAFTAR FOLDER TUJUAN MILIK USER
        $allFolders = Folder::where('user_id', $user->id)
            ->select('id', 'name', 'visibility')
            ->get();

        return view('dashboard.dokumen.saya', compact('folders', 'docs', 'allFolders', 'hasFilter'));
    }

    /**
     * FITUR BARU: Endpoint Pindahkan Folder
     */
    /**
     * FITUR: Endpoint Pindahkan Folder (Kebal Circular Dependency & Rekursif Penuh)
     */
    public function moveFolder(Request $request, int $id)
    {
        $folder = Folder::findOrFail($id);

        // 1. Validasi Hak Akses Pemilik
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk memindahkan folder ini.');
        }

        $destId = $request->input('dest_id');
        $visAction = $request->input('vis_action'); // 'adapt' atau 'keep'

        // 2. KASUS 1: Pindah ke Root (Luar Folder)
        if (empty($destId)) {
            $folder->parent_id = null;
            $folder->save();
            return back()->with('success', "Folder '{$folder->name}' berhasil dipindahkan ke direktori utama.");
        }

        // 3. KASUS 2: Pindah ke dalam Folder Tujuan
        $destFolder = Folder::findOrFail($destId);

        // Mencegah Folder Masuk ke Dirinya Sendiri
        if ($destFolder->id === $folder->id) {
            return back()->with('error', 'Gagal: Folder tidak dapat dipindahkan ke dalam dirinya sendiri.');
        }

        // Mencegah Circular Dependency: Folder Tujuan tidak boleh merupakan turunan/anak dari Folder ini
        if ($this->isDescendantOf($destFolder->id, $folder->id)) {
            return back()->with('error', 'Gagal: Folder tidak dapat dipindahkan ke dalam subfoldernya sendiri.');
        }

        // Update relasi induk
        $folder->parent_id = $destFolder->id;

        // 4. Sinkronisasi Hak Akses Rekursif (Seluruh Subfolder & Dokumen di dalamnya)
        if ($visAction === 'adapt' && $folder->visibility !== $destFolder->visibility) {
            $newVisibility = $destFolder->visibility;
            $this->cascadeVisibilityChange($folder, $newVisibility);
        } else {
            $folder->save();
        }

        return back()->with('success', "Folder '{$folder->name}' berhasil dipindahkan ke folder '{$destFolder->name}'.");
    }

    /**
     * Helper: Cek apakah folder tujuan merupakan turunan (subfolder) dari folder saat ini
     */
    private function isDescendantOf(int $destId, int $folderId): bool
    {
        $current = Folder::find($destId);
        while ($current && $current->parent_id) {
            if ($current->parent_id === $folderId) {
                return true;
            }
            $current = Folder::find($current->parent_id);
        }
        return false;
    }

   /**
     * Helper: Ubah hak akses folder dan seluruh keturunannya, 
     * SEKALIGUS memindahkan file fisiknya antar-disk storage (public <-> private)
     */
    private function cascadeVisibilityChange(Folder $folder, string $newVisibility): void
    {
        // 1. Kumpulkan semua ID folder dan subfolder di bawahnya secara rekursif
        $folderIds = [$folder->id];
        $queue = [$folder->id];

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $childIds = Folder::where('parent_id', $currentId)->pluck('id')->toArray();
            if (!empty($childIds)) {
                $folderIds = array_merge($folderIds, $childIds);
                $queue = array_merge($queue, $childIds);
            }
        }

        // 2. Update visibility semua folder terkait di database
        Folder::whereIn('id', $folderIds)->update(['visibility' => $newVisibility]);

        // 3. Pindahkan berkas fisik sesuai aturan keamanan:
        //    - 'public'  => disk 'public'
        //    - 'internal' / 'private' => disk 'private' (brankas terlindungi)
        $docs = ModelsDocument::whereIn('folder_id', $folderIds)->get();
        $targetDisk = ($newVisibility === 'public') ? 'public' : 'private';

        foreach ($docs as $doc) {
            if (!empty($doc->file_path)) {
                // Tentukan disk sumber saat ini
                $currentDisk = Storage::disk('private')->exists($doc->file_path) ? 'private' : 'public';

                // Pindahkan fisik file jika berada di disk yang salah
                if ($currentDisk !== $targetDisk && Storage::disk($currentDisk)->exists($doc->file_path)) {
                    try {
                        $fileContent = Storage::disk($currentDisk)->get($doc->file_path);
                        Storage::disk($targetDisk)->put($doc->file_path, $fileContent);
                        Storage::disk($currentDisk)->delete($doc->file_path);
                    } catch (\Throwable $e) {
                        \Log::warning("Gagal memindahkan file fisik dokumen ID {$doc->id}: " . $e->getMessage());
                    }
                }
            }

            // Perbarui status sensitivitas dokumen di database
            $doc->sensitivity = $newVisibility;
            $doc->save();
        }
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

    public function tempUpload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'],
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension() ?: 'bin';
        $filename = 'tmp_' . Str::random(25) . '.' . $extension;

        $path = $file->storeAs('temp_uploads', $filename, 'public');

        return response()->json([
            'success'   => true,
            'temp_path' => $path,
            'name'      => $file->getClientOriginalName(),
            'size'      => $file->getSize(),
        ]);
    }

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

        if (!empty($validated['folder_id'])) {
            $parentFolder = Folder::find($validated['folder_id']);
            if ($parentFolder) {
                $validated['sensitivity'] = $parentFolder->visibility;
            }
        }

        if (!empty($validated['files']) && is_array($validated['files'])) {
            $createdCount = 0;
            foreach ($validated['files'] as $index => $tempFilePath) {
                $docData = $validated;
                $docData['temp_file_path'] = $tempFilePath;

                if ($index > 0 && empty($validated['tags'])) {
                    $docData['alias'] = null; 
                }

                $this->repo->create($docData);
                $createdCount++;
            }

            $redirectRoute = !empty($validated['folder_id'])
                ? route('sigap-dokumen.folder.show', $validated['folder_id'])
                : ($validated['sensitivity'] === 'private' ? route('sigap-dokumen.saya') : route('sigap-dokumen.index'));

            return redirect($redirectRoute)->with('success', "{$createdCount} Dokumen berhasil diunggah!");
        }

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

    public function show(ModelsDocument $document)
    {
        $this->authorizeDocumentAccess($document);

        ActivityLogger::log('dokumen', 'view', $document);

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

    public function preview(ModelsDocument $document)
    {
        $this->authorizeDocumentAccess($document);

        $disk = Storage::disk('private')->exists($document->file_path) ? 'private' : 'public';
        if (!Storage::disk($disk)->exists($document->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan.');
        }

        $content = Storage::disk($disk)->get($document->file_path);
        $mime = Storage::disk($disk)->mimeType($document->file_path);

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . ($document->alias ?? $document->title) . '"');
    }

    public function download(ModelsDocument $document)
    {
        $this->authorizeDocumentAccess($document);

        $disk = Storage::disk('private')->exists($document->file_path) ? 'private' : 'public';
        if (!Storage::disk($disk)->exists($document->file_path)) {
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

        $updated = $this->repo->update($id, $validated, $request->file('file'), $request->file('thumb'));

        return redirect()->route('sigap-dokumen.edit', $updated->id)->with('success', "Dokumen '{$updated->title}' berhasil diperbarui!");
    }

    private function authorizeDocumentAccess(ModelsDocument $document): void
    {
        $user = Auth::user();

        if ($document->sensitivity === 'private') {
            if (!$user || ($document->created_by !== $user->id && !$user->hasRole('admin'))) {
                abort(403, 'Akses Ditolak: Dokumen ini berstatus PRIVAT.');
            }
        }
        if ($document->sensitivity === 'internal') {
            if (!$user || !$user->hasAnyRole(['employee', 'admin'])) {
                abort(403, 'Akses Terbatas: Dokumen ini hanya diperuntukkan bagi internal pegawai BRIDA.');
            }
        }
    }
}