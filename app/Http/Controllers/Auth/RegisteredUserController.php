<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {

        // dd($request->all());
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nip' => ['nullable', 'string', 'max:50'],
            'unit' => ['nullable', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:50', 'unique:'.User::class],
            'profile_photo_path' => ['nullable', 'string', 'max:2048'],
            'status' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'in:admin,user,employee,verificator,inovator,pending'],
            'nomor_hp' => ['nullable', 'string', 'max:15'],
            
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'unit' => $request->unit,
            'password' => Hash::make($request->password),
            'username' => $request->username,
            'profile_photo_path' => $request->profile_photo_path,
            'status' => $request->status ?? 'active',
            'role' => 'pending',
            'nomor_hp' => $request->nomor_hp,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }

    public function adminStore(Request $request): RedirectResponse
    {
        $validRoles = Role::where('guard_name','web')->pluck('name')->all();

        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','string','lowercase','email','max:255','unique:users,email'],
            'username' => ['nullable','string','max:50','unique:users,username'],
            'nip'      => ['required','string','max:50'],
            'unit'     => ['nullable','string','max:100'],
            'status'   => ['nullable','in:active,inactive'],
            'password' => ['nullable','confirmed', Rules\Password::defaults()],
            'roles'    => ['nullable','array'],
            'roles.*'  => ['string', Rule::in($validRoles)],
            'nomor_hp' => ['nullable','string','max:15'],
            // ⬇️ foto profil
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        if (empty($data['username'])) {
            $base = Str::of($data['name'])->lower()->replaceMatches('/[^a-z0-9]+/','.')->trim('.')->substr(0,30);
            $data['username'] = $base.'-'.random_int(10,99);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = Hash::make(Str::password(10));
        }

        $data['status'] = $data['status'] ?? 'active';

        // handle avatar upload
        if ($request->hasFile('avatar')) {
            $data['profile_photo_path'] = $request->file('avatar')->store('avatars','public');
        }

        // jangan bawa 'roles' ke mass-assign
        $roles = $data['roles'] ?? [];
        unset($data['roles']);

        $user = User::create($data);
        $user->syncRoles($roles);

        return redirect()->route('sigap-pegawai.index')->with('success','User berhasil dibuat.');
    }
}
