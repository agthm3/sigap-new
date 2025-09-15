<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PegawaiProfilController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        $roleNames = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->toArray()
            : (array) ($user->role ?? []);

        // Dummy berkas (ganti ke query milik user)
        $docs = [
            ['label'=>'KTP','filename'=>"ktp_{$user->id}.pdf",'status'=>'Terverifikasi','uploaded_at'=>now()->subDays(10)->toDateString(),'url'=>'#'],
            ['label'=>'Kartu Keluarga','filename'=>"kk_{$user->id}.pdf",'status'=>'Menunggu verifikasi','uploaded_at'=>now()->subDays(2)->toDateString(),'url'=>'#'],
            ['label'=>'Pas Foto','filename'=>"foto_{$user->id}.jpg",'status'=>'Terverifikasi','uploaded_at'=>now()->subDay()->toDateString(),'url'=>'#'],
        ];

        return view('dashboard.pegawai.profil', compact('user','roleNames','docs'));
    }

    public function edit(Request $request): View
    {
        $user = $request->user();
        return view('dashboard.pegawai.profil_edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255',"unique:users,email,{$user->id}"],
            'nomor_hp' => ['nullable','string','max:20'],
            'password' => ['nullable','confirmed', Rules\Password::defaults()],
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        // email change → (opsional) reset verifikasi
        if ($user->email !== $data['email']) {
            $user->email_verified_at = null; // kalau pakai verifikasi email
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['profile_photo_path'] = $path;
        }

        $user->update($data);

        return back()->with('success','Profil berhasil diperbarui.');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }

        return back()->with('success','Foto profil dihapus.');
    }
}
