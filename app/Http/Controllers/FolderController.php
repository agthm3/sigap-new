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
            'visibility'          => ['required', 'in:public,private'],
            'parent_id'           => ['nullable', 'exists:folders,id'],
        ]);

        $validated['user_id'] = Auth::id();

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

        $redirectRoute = $folder->visibility === 'public' 
            ? route('sigap-dokumen.index') 
            : route('sigap-dokumen.saya');

        return redirect($redirectRoute)->with('success', 'Folder baru berhasil dibuat!');
    }

    public function show(Folder $folder)
    {
        if ($folder->visibility === 'private') {
            if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
                abort(403, 'Akses ditolak. Folder ini bersifat privat.');
            }
        }

        $subfoldersQuery = $folder->subfolders()->latest();
        if ($folder->visibility === 'public') {
            $subfoldersQuery->where('visibility', 'public');
        }
        $subfolders = $subfoldersQuery->get();

        $docQuery = $folder->documents()->latest();
        if ($folder->visibility === 'public') {
            $docQuery->where('sensitivity', 'public');
        }
        $documents = $docQuery->paginate(12);

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
            'visibility'          => ['required', 'in:public,private'],
        ]);

        $folder->update($validated);

        return redirect()->route('sigap-dokumen.saya')->with('success', 'Folder berhasil diperbarui!');
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
}