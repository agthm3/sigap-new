<?php

namespace App\Http\Controllers;

use App\Models\PegawaiKompetensi;
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
        $user->load(['profile','kompetensis']);

        $roles         = Role::where('guard_name','web')->pluck('name')->all();
        
        // ini untuk checklist (role yang dimiliki user)
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
            'avatar'   => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER
        |--------------------------------------------------------------------------
        */
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // upload avatar baru
        if ($request->hasFile('avatar')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $data['profile_photo_path'] = $request->file('avatar')->store('avatars','public');
        }

        $roles = $request->input('roles', []);
        unset($data['roles']);

        $this->repo->update($user, $data, $roles);

        /*
        |--------------------------------------------------------------------------
        | UPDATE PROFILE
        |--------------------------------------------------------------------------
        */
        $profileData = $request->only([
            'nik',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'agama',
            'status_perkawinan',
            'golongan_darah',
            'nip_baru',
            'nip_lama',
            'keterangan',

            'status_pegawai',
            'jabatan',
            'golongan',
            'tmt_pns',
            'atasan_langsung',
            'golongan_ruang',
            'tmt_golongan',
            'masa_kerja_tahun',
            'masa_kerja_bulan',
            'tmt_jabatan',
            'eselon',
            'jabatan_struktural',
            'jabatan_fungsional',
            'jabatan_teknis',
            'unor',

            'alamat_ktp',
            'alamat_domisili',
            'npwp',
            'bpjs_kesehatan',
            'bpjs_ketenagakerjaan',
            'bank_nama',
            'nomor_rekening',
            'nama_rekening',

            'nama_pasangan',
            'pekerjaan_pasangan',
            'jumlah_anak',
            'kontak_darurat',

            'pendidikan_terakhir',
            'jurusan',
            'tahun_lulus'
        ]);

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KOMPETENSI / SERTIFIKAT (FIX FILE TIDAK HILANG)
        |--------------------------------------------------------------------------
        */
        if ($request->has('nama_sertifikat')) {

            $files = $request->file('file_sertifikat', []);

            foreach ($request->nama_sertifikat as $index => $nama) {

                if (!$nama) continue;

                $id = $request->kompetensi_id[$index] ?? null;

                // fallback file lama dari hidden field
                $filePath = $request->existing_file_path[$index] ?? null;
                $fileName = $request->existing_file_name[$index] ?? null;
                $fileMime = $request->existing_file_mime[$index] ?? null;

                // kalau upload file baru → replace
                if (isset($files[$index])) {

                    $file = $files[$index];

                    if ($filePath) {
                        Storage::disk('public')->delete($filePath);
                    }

                    $filePath = $file->store('kompetensi','public');
                    $fileName = $file->getClientOriginalName();
                    $fileMime = $file->getMimeType();
                }

                // update atau create manual (AMAN)
                $kompetensi = $id
                    ? PegawaiKompetensi::find($id)
                    : new PegawaiKompetensi();

                $kompetensi->user_id           = $user->id;
                $kompetensi->nama_sertifikat   = $nama;
                $kompetensi->bidang_sertifikat = $request->bidang_sertifikat[$index] ?? null;
                $kompetensi->tahun_sertifikat  = $request->tahun_sertifikat[$index] ?? null;
                $kompetensi->file_path         = $filePath;
                $kompetensi->file_name         = $fileName;
                $kompetensi->file_mime         = $fileMime;

                $kompetensi->save();
            }
        }

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