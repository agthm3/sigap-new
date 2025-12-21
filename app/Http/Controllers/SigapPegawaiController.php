<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class SigapPegawaiController extends Controller
{
    public function __construct(private UserRepository $repo) {}

    public function index(Request $request)
    {
        $filters = $request->only(['q','unit','role','status','sort']);
        $perPage = (int) $request->input('per_page', 25);
        $users   = $this->repo->paginateWithFilters($filters, $perPage);
        $roles   = Role::where('guard_name','web')->pluck('name')->all();

        return view('dashboard.pegawai.index', compact('users','filters','roles'));
    }

    public function create()
    {
        // Form create tetap submit ke route('register'), tapi kita kirim daftar roles untuk checkbox
        $roles = Role::where('guard_name','web')->pluck('name')->all();
        return view('dashboard.pegawai.create', compact('roles'));
    }

    public function edit(User $user)
    {
        $roles         = Role::where('guard_name','web')->pluck('name')->all();
        $userRoleNames = $user->getRoleNames()->all();
        return view('dashboard.pegawai.edit', compact('user','roles','userRoleNames'));
    }

    public function update(Request $request, User $user)
    {
        $validRoleNames = Role::where('guard_name','web')->pluck('name')->all();

        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255',"unique:users,email,{$user->id}"],
            'username' => ['required','string','max:50',"unique:users,username,{$user->id}"],
            'nip'      => ['nullable','string','max:50'],
            'unit'     => ['nullable','string','max:100'],
            'status'   => ['nullable','in:active,inactive'],
            'password' => ['nullable','confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'roles'    => ['nullable','array'],
            'roles.*'  => ['string', Rule::in($validRoleNames)],
            // ⬇️ foto profil
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // upload avatar baru (hapus lama jika ada)
        if ($request->hasFile('avatar')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('avatar')->store('avatars','public');
        }

        $roles = $request->input('roles', []);
        unset($data['roles']);

        $this->repo->update($user, $data, $roles);

        return back()->with('success','Perubahan disimpan.');
    }

    /** opsional: tombol hapus foto */
    public function destroyAvatar(User $user)
    {
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update(['profile_photo_path' => null]);
        }
        return back()->with('success','Foto profil dihapus.');
    }
    public function destroy(User $user)
    {
        $this->repo->delete($user);
        return redirect()->route('sigap-pegawai.index')->with('success','User dihapus.');
    }
}
