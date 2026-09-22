<?php

namespace App\Http\Controllers;

use App\Constants\PermendagriClassification;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class FolderController extends Controller
{
    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        $parentFolder = $parentId ? Folder::find($parentId) : null;
        $permendagriList = PermendagriClassification::all();

        return view('dashboard.dokumen.folder.create', compact('parentId', 'parentFolder', 'permendagriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'icon'                => ['nullable', 'string', 'max:50'],
            'color'               => ['nullable', 'string', 'max:20'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'visibility'          => ['required', 'in:public,internal,private'],
            'parent_id'           => ['nullable', 'exists:folders,id'],
        ]);

        $validated['user_id'] = Auth::id();

        // Jika dibuat sebagai subfolder di dalam folder private, paksa visibilitasnya tetap private
        if (!empty($validated['parent_id'])) {
            $parent = Folder::find($validated['parent_id']);
            if ($parent && $parent->visibility === 'private') {
                $validated['visibility'] = 'private';
            }
        }

        $folder = Folder::create($validated);

        if ($folder->parent_id) {
            return redirect()->route('sigap-dokumen.folder.show', $folder->parent_id)
                ->with('success', 'Subfolder berhasil dibuat!');
        }

        // Folder public dan internal masuk ke katalog Dokumen Umum; private masuk ke Dokumen Saya
        $redirectRoute = in_array($folder->visibility, ['public', 'internal'])
            ? route('sigap-dokumen.index') 
            : route('sigap-dokumen.saya');

        return redirect($redirectRoute)->with('success', 'Folder baru berhasil dibuat!');
    }

    public function show(Folder $folder)
    {
        $user = Auth::user();

        // 1. Otorisasi Folder Private: Hanya pemilik dan admin
        if ($folder->visibility === 'private') {
            if ($folder->user_id !== $user->id && !$user->hasRole('admin')) {
                abort(403, 'Akses ditolak. Folder ini bersifat privat.');
            }
        }

        // 2. Otorisasi Folder Internal: Wajib pegawai atau admin
        if ($folder->visibility === 'internal') {
            if (!$user->hasAnyRole(['employee', 'admin'])) {
                abort(403, 'Akses terbatas. Folder ini khusus internal pegawai BRIDA.');
            }
        }

        // Ambil subfolder & dokumen di dalam folder ini
        $subfolders = $folder->subfolders()->latest()->get();
        $documents  = $folder->documents()->latest()->paginate(12);

        return view('dashboard.dokumen.folder.show', compact('folder', 'subfolders', 'documents'));
    }

    

    public function edit(Folder $folder)
    {
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah folder ini.');
        }

        $permendagriList = PermendagriClassification::all();
        return view('dashboard.dokumen.folder.edit', compact('folder', 'permendagriList'));
    }

    public function update(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah folder ini.');
        }

        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'icon'                => ['nullable', 'string', 'max:50'],
            'color'               => ['nullable', 'string', 'max:20'],
            'classification_code' => ['nullable', 'string', 'max:50'],
            'visibility'          => ['required', 'in:public,internal,private'],
        ]);

        $oldVisibility = $folder->visibility;
        $newVisibility = $validated['visibility'];

        $folder->update($validated);

        // Jika visibilitas folder berubah, jalankan kaskade ke isi folder
        if ($oldVisibility !== $newVisibility) {
            $this->cascadeFolderVisibility($folder, $newVisibility);
        }

        $redirectRoute = in_array($newVisibility, ['public', 'internal'])
            ? route('sigap-dokumen.index')
            : route('sigap-dokumen.saya');

        return redirect($redirectRoute)->with('success', 'Folder dan seluruh berkas di dalamnya berhasil diperbarui!');
    }

    /**
     * Kaskade perubahan visibilitas ke seluruh subfolder, dokumen, dan berkas fisik
     */
    private function cascadeFolderVisibility(Folder $folder, string $newVisibility): void
    {
        // 1. Sinkronisasi dokumen langsung di dalam folder ini
        $documents = $folder->documents()->get();
        foreach ($documents as $doc) {
            $this->migrateDocumentStorage($doc, $newVisibility);
        }

        // 2. Sinkronisasi rekursif untuk subfolder dan dokumen di dalamnya
        $subfolders = $folder->subfolders()->get();
        foreach ($subfolders as $sub) {
            $sub->update(['visibility' => $newVisibility]);
            $this->cascadeFolderVisibility($sub, $newVisibility);
        }
    }

    /**
     * Pindahkan file fisik antar disk (public <-> private) dan update field sensitivity
     */
    private function migrateDocumentStorage($doc, string $newSensitivity): void
    {
        $targetDisk = $newSensitivity === 'private' ? 'private' : 'public';
        $currentDisk = $targetDisk === 'private' ? 'public' : 'private';

        // Pindahkan file utama
        if ($doc->file_path && Storage::disk($currentDisk)->exists($doc->file_path)) {
            $fileContent = Storage::disk($currentDisk)->get($doc->file_path);
            Storage::disk($targetDisk)->put($doc->file_path, $fileContent);
            Storage::disk($currentDisk)->delete($doc->file_path);
        }

        // Pindahkan thumbnail jika ada
        if ($doc->thumb_path && Storage::disk($currentDisk)->exists($doc->thumb_path)) {
            $thumbContent = Storage::disk($currentDisk)->get($doc->thumb_path);
            Storage::disk($targetDisk)->put($doc->thumb_path, $thumbContent);
            Storage::disk($currentDisk)->delete($doc->thumb_path);
        }

        $doc->update(['sensitivity' => $newSensitivity]);
    }

    public function share(Folder $folder)
    {
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        return view('dashboard.dokumen.folder.share', compact('folder'));
    }

    public function updateShare(Request $request, Folder $folder)
    {
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        $validated = $request->validate([
            'password'   => ['nullable', 'string', 'min:4'],
            'duration'   => ['required', 'in:1_hour,1_day,7_days,unlimited'],
        ]);

        if (empty($folder->share_token)) {
            $folder->share_token = Str::random(32);
        }

        if (!empty($validated['password'])) {
            $folder->share_password = Hash::make($validated['password']);
        }

        $folder->share_expires_at = match ($validated['duration']) {
            '1_hour'    => now()->addHour(),
            '1_day'     => now()->addDay(),
            '7_days'    => now()->addDays(7),
            'unlimited' => null,
        };

        $folder->save();

        return back()->with('success', 'Tautan berbagi folder berhasil dikonfigurasi!');
    }

    public function revokeShare(Folder $folder)
    {
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Akses ditolak.');
        }

        $folder->update([
            'share_token'      => null,
            'share_password'   => null,
            'share_expires_at' => null,
        ]);

        return back()->with('success', 'Tautan berbagi berhasil dinonaktifkan.');
    }

    public function downloadZip(Folder $folder)
    {
        if ($folder->visibility === 'private') {
            if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
                abort(403, 'Akses ditolak.');
            }
        }

        $zipFileName = 'Folder_' . Str::slug($folder->name) . '_' . now()->format('YmdHis') . '.zip';
        $tempPath = storage_path('app/temp_uploads/' . $zipFileName);

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat berkas arsip ZIP.');
        }

        $documents = $folder->documents()->get();
        foreach ($documents as $doc) {
            $disk = Storage::disk('private')->exists($doc->file_path) ? 'private' : 'public';
            if (Storage::disk($disk)->exists($doc->file_path)) {
                $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                $entryName = ($doc->alias ?? Str::slug($doc->title)) . '.' . $ext;
                $zip->addFile(Storage::disk($disk)->path($doc->file_path), $entryName);
            }
        }

        $zip->close();

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    public function accessShared(Request $request, string $token)
    {
        $folder = Folder::where('share_token', $token)->firstOrFail();

        if ($folder->share_expires_at && now()->greaterThan($folder->share_expires_at)) {
            return response()->view('dashboard.dokumen.folder.expired', [], 410);
        }

        if (!empty($folder->share_password)) {
            $unlocked = session()->get('unlocked_folder_' . $folder->id);
            if (!$unlocked) {
                return view('dashboard.dokumen.folder.password', compact('folder', 'token'));
            }
        }

        $documents = $folder->documents()->paginate(15);
        return view('dashboard.dokumen.folder.shared', compact('folder', 'documents', 'token'));
    }

    public function unlockShared(Request $request, string $token)
    {
        $folder = Folder::where('share_token', $token)->firstOrFail();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (Hash::check($request->password, $folder->share_password)) {
            session()->put('unlocked_folder_' . $folder->id, true);
            return redirect()->route('sigap-dokumen.shared.view', $token);
        }

        return back()->withErrors(['password' => 'Kata sandi tidak sesuai. Silakan coba lagi.']);
    }

    /**
     * Tampilkan daftar seluruh tautan folder yang pernah dibagikan
     */
    public function sharedLinksIndex(Request $request)
    {
        $user = Auth::user();
        
        // Admin bisa melihat semua link aktif, employee hanya melihat link miliknya sendiri
        $query = Folder::whereNotNull('share_token');
        
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where('name', 'like', "%{$q}%");
        }

        $sharedFolders = $query->with('user')->latest('updated_at')->paginate(12);

        return view('dashboard.dokumen.shared-links.index', compact('sharedFolders'));
    }

    /**
     * Cabut/Hapus tautan berbagi (Khusus Admin)
     */
    public function sharedLinksRevoke(Folder $folder)
    {
        $folder->update([
            'share_token'      => null,
            'share_password'   => null,
            'share_expires_at' => null,
        ]);

        return back()->with('success', 'Tautan berbagi folder berhasil dinonaktifkan secara permanen.');
    }

    /**
     * Hapus folder beserta seluruh dokumen dan subfolder di dalamnya secara rekursif
     */
    public function destroy(Folder $folder)
    {
        // Validasi hak akses: hanya pemilik folder atau admin
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus folder ini.');
        }

        $parentId = $folder->parent_id;
        $folderVisibility = $folder->visibility;

        // Hapus seluruh isi folder (file fisik, data dokumen, dan subfolder)
        $this->deleteFolderContentsRecursively($folder);

        // Hapus record folder itu sendiri
        $folder->delete();

        // Redirect: jika subfolder, kembali ke induknya; jika root, kembali ke Dokumen Saya / Umum
        if ($parentId) {
            return redirect()->route('sigap-dokumen.folder.show', $parentId)
                ->with('success', 'Folder beserta seluruh isinya berhasil dihapus.');
        }

        $redirectRoute = in_array($folderVisibility, ['public', 'internal'])
            ? route('sigap-dokumen.index')
            : route('sigap-dokumen.saya');

        return redirect($redirectRoute)->with('success', 'Folder beserta seluruh isinya berhasil dihapus.');
    }

    /**
     * Helper rekursif pembersihan berkas fisik & record database
     */
    private function deleteFolderContentsRecursively(Folder $folder): void
    {
        // 1. Hapus berkas fisik dan record dokumen langsung di folder ini
        $documents = $folder->documents()->get();
        foreach ($documents as $doc) {
            // Hapus file utama dari storage (cek disk public dan private)
            if ($doc->file_path) {
                if (Storage::disk('private')->exists($doc->file_path)) {
                    Storage::disk('private')->delete($doc->file_path);
                }
                if (Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
            }

            // Hapus thumbnail jika ada
            if ($doc->thumb_path) {
                if (Storage::disk('private')->exists($doc->thumb_path)) {
                    Storage::disk('private')->delete($doc->thumb_path);
                }
                if (Storage::disk('public')->exists($doc->thumb_path)) {
                    Storage::disk('public')->delete($doc->thumb_path);
                }
            }

            $doc->delete();
        }

        // 2. Telusuri subfolder secara rekursif
        $subfolders = $folder->subfolders()->get();
        foreach ($subfolders as $sub) {
            $this->deleteFolderContentsRecursively($sub);
            $sub->delete();
        }
    }
}