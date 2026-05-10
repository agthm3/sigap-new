<?php

namespace App\Http\Controllers;

use App\Models\SigapPicAssignment;
use App\Models\SigapPicCredential;
use App\Models\SigapPicLog;
use App\Models\SigapPicSystem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SigapPicController extends Controller
{
    private function normalizeUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        if (!Str::startsWith($url, ['http://', 'https://'])) {
            $url = 'https://' . $url;
        }

        return $url;
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        $systemsQuery = SigapPicSystem::query()
            ->with(['assignments.user', 'credentials'])
            ->withCount([
                'assignments as pic_count',
                'credentials as account_count',
            ])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_sistem', 'like', "%{$q}%")
                        ->orWhere('kategori', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%")
                        ->orWhereHas('assignments.user', function ($a) use ($q) {
                            $a->where('name', 'like', "%{$q}%")
                              ->orWhere('nip', 'like', "%{$q}%")
                              ->orWhere('unit', 'like', "%{$q}%");
                        })
                        ->orWhereHas('credentials', function ($c) use ($q) {
                            $c->where('nama_akun', 'like', "%{$q}%")
                              ->orWhere('username', 'like', "%{$q}%")
                              ->orWhere('email', 'like', "%{$q}%");
                        });
                });
            })
            ->when($kategori, fn ($query) => $query->where('kategori', $kategori))
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest();

        $systems = $systemsQuery->paginate(8)->withQueryString();

        return view('dashboard.pic.index', [
            'systems'       => $systems,
            'totalSystems'  => SigapPicSystem::count(),
            'activeSystems' => SigapPicSystem::where('status', 'aktif')->count(),
            'totalPic'      => SigapPicAssignment::count(),
            'totalAccounts' => SigapPicCredential::count(),
        ]);
    }

    public function create()
    {
        $employees = User::role('employee')
            ->orderBy('name')
            ->get(['id', 'name', 'nip', 'unit', 'username', 'email']);

        return view('dashboard.pic.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sistem' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'thumbnail_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,maintenance,nonaktif'],
            'level_kritis' => ['nullable', 'string', 'max:50'],

            'pic_user_ids' => ['required', 'array', 'min:1'],
            'pic_user_ids.*' => ['required', 'exists:users,id', 'distinct'],

            'credentials' => ['nullable', 'array'],
            'credentials.*.nama_akun' => ['required', 'string', 'max:150'],
            'credentials.*.username' => ['nullable', 'string', 'max:150'],
            'credentials.*.password_encrypted' => ['nullable', 'string', 'max:255'],
            'credentials.*.email' => ['nullable', 'email', 'max:255'],
            'credentials.*.url_login' => ['nullable', 'string', 'max:255'],
            'credentials.*.access_level' => ['nullable', 'string', 'max:150'],
            'credentials.*.is_sensitive' => ['nullable'],
            'credentials.*.catatan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $system = SigapPicSystem::create([
                'nama_sistem' => $validated['nama_sistem'],
                'slug' => Str::slug($validated['nama_sistem']) . '-' . Str::lower(Str::random(6)),
                'kategori' => $validated['kategori'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'url' => $this->normalizeUrl($validated['url'] ?? null),
                'youtube_url' => $this->normalizeUrl($validated['youtube_url'] ?? null),
                'thumbnail_path' => $validated['thumbnail_path'] ?? null,
                'status' => $validated['status'],
                'level_kritis' => $validated['level_kritis'] ?? null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            $system->update([
                'slug' => Str::slug($system->nama_sistem) . '-' . $system->id,
            ]);

            $userIds = collect($validated['pic_user_ids'])->unique()->values();

            foreach ($userIds as $index => $userId) {
                $user = User::find($userId);

                if (!$user) {
                    continue;
                }

                SigapPicAssignment::create([
                    'system_id' => $system->id,
                    'user_id' => $user->id,
                    'pegawai_nik' => $user->nip,
                    'nama_pic' => $user->name,
                    'jabatan_pic' => null,
                    'bidang' => $user->unit,
                    'tanggung_jawab' => null,
                    'is_primary' => $index === 0,
                    'urutan' => $index + 1,
                    'catatan' => null,
                ]);
            }

            $credentials = collect($validated['credentials'] ?? [])
                ->filter(fn ($row) => filled($row['nama_akun'] ?? null))
                ->values();

            foreach ($credentials as $row) {
                SigapPicCredential::create([
                    'system_id' => $system->id,
                    'nama_akun' => $row['nama_akun'],
                    'username' => $row['username'] ?? null,
                    'password_encrypted' => $row['password_encrypted'] ?? null,
                    'email' => $row['email'] ?? null,
                    'url_login' => $row['url_login'] ?? null,
                    'access_level' => $row['access_level'] ?? null,
                    'is_sensitive' => !empty($row['is_sensitive']),
                    'catatan' => $row['catatan'] ?? null,
                ]);
            }

            SigapPicLog::create([
                'user_id' => auth()->id(),
                'system_id' => $system->id,
                'aksi' => 'create_system',
                'detail' => 'Membuat sistem PIC: ' . $system->nama_sistem,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('sigap-pic.index')->with('success', 'Sistem PIC berhasil ditambahkan.');
    }

    public function edit(SigapPicSystem $system)
    {
        $system->load(['assignments.user', 'credentials']);

        $employees = User::role('employee')
            ->orderBy('name')
            ->get(['id', 'name', 'nip', 'unit', 'username', 'email']);

        return view('dashboard.pic.edit', compact('system', 'employees'));
    }

    public function update(Request $request, SigapPicSystem $system)
    {
        $validated = $request->validate([
            'nama_sistem' => ['required', 'string', 'max:255'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'url' => ['nullable', 'string', 'max:255'],
            'youtube_url' => ['nullable', 'string', 'max:255'],
            'thumbnail_path' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,maintenance,nonaktif'],
            'level_kritis' => ['nullable', 'string', 'max:50'],

            'pic_user_ids' => ['required', 'array', 'min:1'],
            'pic_user_ids.*' => ['required', 'exists:users,id', 'distinct'],

            'credentials' => ['nullable', 'array'],
            'credentials.*.nama_akun' => ['required', 'string', 'max:150'],
            'credentials.*.username' => ['nullable', 'string', 'max:150'],
            'credentials.*.password_encrypted' => ['nullable', 'string', 'max:255'],
            'credentials.*.email' => ['nullable', 'email', 'max:255'],
            'credentials.*.url_login' => ['nullable', 'string', 'max:255'],
            'credentials.*.access_level' => ['nullable', 'string', 'max:150'],
            'credentials.*.is_sensitive' => ['nullable'],
            'credentials.*.catatan' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $system) {
            $system->update([
                'nama_sistem' => $validated['nama_sistem'],
                'slug' => Str::slug($validated['nama_sistem']) . '-' . $system->id,
                'kategori' => $validated['kategori'] ?? null,
                'deskripsi' => $validated['deskripsi'] ?? null,
                'url' => $this->normalizeUrl($validated['url'] ?? null),
                'youtube_url' => $this->normalizeUrl($validated['youtube_url'] ?? null),
                'thumbnail_path' => $validated['thumbnail_path'] ?? null,
                'status' => $validated['status'],
                'level_kritis' => $validated['level_kritis'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            $system->assignments()->delete();
            $system->credentials()->delete();

            $userIds = collect($validated['pic_user_ids'])->unique()->values();

            foreach ($userIds as $index => $userId) {
                $user = User::find($userId);

                if (!$user) {
                    continue;
                }

                SigapPicAssignment::create([
                    'system_id' => $system->id,
                    'user_id' => $user->id,
                    'pegawai_nik' => $user->nip,
                    'nama_pic' => $user->name,
                    'jabatan_pic' => null,
                    'bidang' => $user->unit,
                    'tanggung_jawab' => null,
                    'is_primary' => $index === 0,
                    'urutan' => $index + 1,
                    'catatan' => null,
                ]);
            }

            $credentials = collect($validated['credentials'] ?? [])
                ->filter(fn ($row) => filled($row['nama_akun'] ?? null))
                ->values();

            foreach ($credentials as $row) {
                SigapPicCredential::create([
                    'system_id' => $system->id,
                    'nama_akun' => $row['nama_akun'],
                    'username' => $row['username'] ?? null,
                    'password_encrypted' => $row['password_encrypted'] ?? null,
                    'email' => $row['email'] ?? null,
                    'url_login' => $row['url_login'] ?? null,
                    'access_level' => $row['access_level'] ?? null,
                    'is_sensitive' => !empty($row['is_sensitive']),
                    'catatan' => $row['catatan'] ?? null,
                ]);
            }

            SigapPicLog::create([
                'user_id' => auth()->id(),
                'system_id' => $system->id,
                'aksi' => 'update_system',
                'detail' => 'Mengubah sistem PIC: ' . $system->nama_sistem,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        return redirect()->route('sigap-pic.index')->with('success', 'Sistem PIC berhasil diperbarui.');
    }

    public function show(SigapPicSystem $system)
    {
        $system->load(['assignments.user', 'credentials', 'logs.user']);

        SigapPicLog::create([
            'user_id' => auth()->id(),
            'system_id' => $system->id,
            'aksi' => 'view_system',
            'detail' => 'Membuka detail sistem PIC: ' . $system->nama_sistem,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return view('dashboard.pic.show', compact('system'));
    }

    public function destroy(SigapPicSystem $system)
    {
        $nama = $system->nama_sistem;

        SigapPicLog::create([
            'user_id' => auth()->id(),
            'system_id' => $system->id,
            'aksi' => 'delete_system',
            'detail' => 'Menghapus sistem PIC: ' . $nama,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $system->delete();

        return redirect()->route('sigap-pic.index')->with('success', 'Sistem PIC berhasil dihapus.');
    }

    public function publicIndex(Request $request)
    {
        $q        = trim((string) $request->get('q'));
        $kategori = $request->get('kategori');
    
        $systems = SigapPicSystem::query()
            ->with(['assignments.user'])           // tidak load credentials
            ->withCount('assignments as pic_count')
            ->where('status', '!=', 'nonaktif')    // sembunyikan sistem nonaktif dari publik
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_sistem', 'like', "%{$q}%")
                        ->orWhere('deskripsi', 'like', "%{$q}%")
                        ->orWhereHas('assignments.user', function ($a) use ($q) {
                            $a->where('name', 'like', "%{$q}%")
                            ->orWhere('unit', 'like', "%{$q}%");
                        });
                });
            })
            ->when($kategori, fn ($query) => $query->where('kategori', $kategori))
            ->latest()
            ->paginate(9)
            ->withQueryString();
    
        return view('SigapPic.public.index', [   // sesuaikan path view kamu
            'systems'       => $systems,
            'q'             => $q,
            'kategori'      => $kategori,
            'totalSystems'  => SigapPicSystem::where('status', '!=', 'nonaktif')->count(),
            'activeSystems' => SigapPicSystem::where('status', 'aktif')->count(),
            'totalPic'      => SigapPicAssignment::count(),
            'totalKategori' => SigapPicSystem::whereNotNull('kategori')
                                ->where('status', '!=', 'nonaktif')
                                ->distinct('kategori')
                                ->count('kategori'),
        ]);
    }
 
}