<?php

namespace App\Http\Controllers;

use App\Models\PegawaiKompetensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PegawaiProfilController extends Controller
{

     public function show()
    {
        $user = auth()->user()->load('profile','kompetensis');

            // AUTO CREATE PROFILE JIKA BELUM ADA
            if (!$user->profile) {
                $user->profile()->create([]);
                $user->load('profile');
            }


        $roleNames = $user->getRoleNames()->all();

        $docs = $user->personalDocuments()->latest()->get()->map(function($d){
            return [
                'id'         => $d->id,
                'label'      => strtoupper($d->type),
                'filename'   => basename($d->path),
                'status'     => $d->status === 'verified' ? 'Terverifikasi' :
                                ($d->status === 'pending' ? 'Menunggu verifikasi' : 'Ditolak'),
                'uploaded_at'=> optional($d->created_at)->format('d M Y H:i'),
            ];
        });

        $sertifikats = $user->kompetensis()->latest()->get();

        return view('dashboard.pegawai.profil',
            compact('user','roleNames','docs','sertifikats')
        );
    }

    public function edit(Request $request): View
    {
        $user = $request->user()->load('profile','kompetensis');

        return view('dashboard.pegawai.profil_edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'nomor_hp'  => 'nullable|string|max:20',
            'password'  => 'nullable|confirmed|min:6',

            // PROFILE FIELDS
            'nik'                   => 'nullable|string|max:50',
            'tempat_lahir'          => 'nullable|string|max:100',
            'tanggal_lahir'         => 'nullable|date',
            'jenis_kelamin'         => 'nullable|string|max:20',
            'agama'                 => 'nullable|string|max:50',
            'status_perkawinan'     => 'nullable|string|max:50',
            'golongan_darah'        => 'nullable|string|max:5',
            'nip_baru'              => 'nullable|string|max:50',
            'nip_lama'              => 'nullable|string|max:50',
            'keterangan'            => 'nullable|string|max:255',

            'status_pegawai'        => 'nullable|string|max:50',
            'jabatan'               => 'nullable|string|max:100',
            'golongan'              => 'nullable|string|max:50',
            'tmt_pns'               => 'nullable|date',
            'atasan_langsung'       => 'nullable|string|max:100',
            'golongan_ruang'        => 'nullable|string|max:50',
            'tmt_golongan'          => 'nullable|date',
            'masa_kerja_tahun'      => 'nullable|integer',
            'masa_kerja_bulan'      => 'nullable|integer',
            'tmt_jabatan'           => 'nullable|date',
            'eselon'                => 'nullable|string|max:50',
            'jabatan_struktural'    => 'nullable|string|max:100',
            'jabatan_fungsional'    => 'nullable|string|max:100',
            'jabatan_teknis'        => 'nullable|string|max:100',
            'unor'                  => 'nullable|string|max:100',

            'alamat_ktp'            => 'nullable|string',
            'alamat_domisili'       => 'nullable|string',
            'npwp'                  => 'nullable|string|max:50',
            'bpjs_kesehatan'        => 'nullable|string|max:50',
            'bpjs_ketenagakerjaan'  => 'nullable|string|max:50',
            'bank_nama'             => 'nullable|string|max:100',
            'nomor_rekening'        => 'nullable|string|max:100',
            'nama_rekening'         => 'nullable|string|max:100',

            'nama_pasangan'         => 'nullable|string|max:100',
            'pekerjaan_pasangan'    => 'nullable|string|max:100',
            'jumlah_anak'           => 'nullable|integer',
            'kontak_darurat'        => 'nullable|string|max:100',

            'pendidikan_terakhir'   => 'nullable|string|max:50',
            'jurusan'               => 'nullable|string|max:100',
            'tahun_lulus'           => 'nullable|integer',
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPDATE USER
        |--------------------------------------------------------------------------
        */

        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->nomor_hp = $validated['nomor_hp'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        /*
        |--------------------------------------------------------------------------
        | UPDATE / CREATE PROFILE
        |--------------------------------------------------------------------------
        */

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            collect($validated)->except(['name','email','password','nomor_hp'])->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | SIMPAN KOMPETENSI (SERTIFIKAT)
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

                // jika upload file baru
                if (isset($files[$index])) {

                    $file = $files[$index];

                    if ($filePath) {
                        Storage::disk('public')->delete($filePath);
                    }

                    $filePath = $file->store('kompetensi', 'public');
                    $fileName = $file->getClientOriginalName();
                    $fileMime = $file->getMimeType();
                }

                if ($id) {
                    $kompetensi = PegawaiKompetensi::find($id);
                } else {
                    $kompetensi = new PegawaiKompetensi();
                }

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

        return back()->with('success', 'Profil berhasil diperbarui.');
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

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        $user = $request->user();

        // hapus lama
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $path
        ]);

        return back()->with('success','Foto profil diperbarui.');
    }
}
