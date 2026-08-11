@extends('layouts.app')

<style>
.input{
    width:100%;
    border:1px solid #d1d5db;
    border-radius:8px;
    padding:8px;
    margin-top:6px;
}
</style>

@section('title', 'Edit Pegawai — SIGAP BRIDA')

@section('content')
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('sigap-pegawai.index') }}" class="hover:text-maroon">SIGAP Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Edit Pegawai</li>
    </ol>
  </nav>

  <section class="max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Ubah data dasar, role, foto profil, dan dokumen pribadi pegawai.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('sigap-pegawai.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Kembali</a>
        <button form="fEdit" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan Profil</button>
      </div>
    </div>
  </section>

  {{-- Flash Session Alerts --}}
  @if(session('success'))
  <div class="max-w-7xl mx-auto px-4 mt-4">
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
  </div>
  @endif

  <main class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-3 gap-6">
    
    {{-- BAGIAN KIRI (Form Utama Edit Profil) --}}
    <section class="lg:col-span-2 space-y-6">
      <form id="fEdit"
            class="bg-white border border-gray-200 rounded-2xl p-5 space-y-5 shadow-sm"
            method="POST"
            action="{{ route('sigap-pegawai.update', $user) }}"
            enctype="multipart/form-data">
        @csrf @method('PUT')

        @if ($errors->any())
          <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama <span class="text-red-500">*</span></span>
            <input name="name" type="text" required
                   value="{{ old('name',$user->name) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Username <span class="text-red-500">*</span></span>
            <input name="username" type="text" required
                   value="{{ old('username',$user->username) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email <span class="text-red-500">*</span></span>
            <input name="email" type="email" required
                   value="{{ old('email',$user->email) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></span>
            <select name="unit" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih kategori</option>
              @foreach ($unitCategories as $kategori)
                <option value="{{ $kategori }}" @selected(old('unit', $user->unit) === $kategori)>
                  {{ $kategori }}
                </option>
              @endforeach
            </select>
          </label>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">NIP</span>
            <input name="nip" type="text"
                   value="{{ old('nip',$user->nip) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="active"  @selected(old('status',$user->status)==='active')>Aktif</option>
              <option value="inactive" @selected(old('status',$user->status)==='inactive')>Nonaktif</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Telepon</span>
            <input name="nomor_hp" type="text"
                   value="{{ old('nomor_hp',$user->nomor_hp) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Password Baru</span>
            <input name="password" type="password" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Kosongkan jika tidak diganti">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Konfirmasi</span>
            <input name="password_confirmation" type="password" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div>
          <span class="text-sm font-semibold text-gray-700">Role Sistem</span>
          <div class="mt-2 grid sm:grid-cols-3 gap-2">
            @foreach($roles as $r)
              <label class="inline-flex items-center gap-2 text-sm bg-gray-50 px-3 py-2 rounded-lg border cursor-pointer hover:bg-gray-100">
                <input type="checkbox" name="roles[]" value="{{ $r }}"
                       class="rounded border-gray-300 text-maroon focus:ring-maroon"
                       @checked(in_array($r, old('roles',$userRoleNames)))>
                <span>{{ $r }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <hr class="my-6">

        <h3 class="text-lg font-bold text-gray-900">Detail Profil Pegawai</h3>

        <!-- TAB NAV -->
        <div class="mt-4 border-b overflow-x-auto whitespace-nowrap scrollbar-hide">
          <nav class="flex gap-4 sm:gap-6 text-sm font-semibold pb-1">
            <button type="button" class="tab-btn border-b-2 border-maroon text-maroon pb-2 px-1" data-tab="identitas">Identitas</button>
            <button type="button" class="tab-btn text-gray-500 pb-2 px-1" data-tab="kepegawaian">Kepegawaian</button>
            <button type="button" class="tab-btn text-gray-500 pb-2 px-1" data-tab="alamat">Alamat & Administrasi</button>
            <button type="button" class="tab-btn text-gray-500 pb-2 px-1" data-tab="keluarga">Keluarga</button>
            <button type="button" class="tab-btn text-gray-500 pb-2 px-1" data-tab="pendidikan">Pendidikan</button>
            <button type="button" class="tab-btn text-gray-500 pb-2 px-1" data-tab="sertifikat">Sertifikat</button>
          </nav>
        </div>

        <!-- ================= IDENTITAS ================= -->
        <div class="tab-content mt-5" id="tab-identitas">
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="block"><span>NIK</span><input name="nik" class="input" value="{{ old('nik',$user->profile->nik ?? '') }}"></label>
            <label class="block"><span>Tempat Lahir</span><input name="tempat_lahir" class="input" value="{{ old('tempat_lahir',$user->profile->tempat_lahir ?? '') }}"></label>
            <label><span>Tanggal Lahir</span><input type="date" name="tanggal_lahir" class="input" value="{{ old('tanggal_lahir',$user->profile->tanggal_lahir ?? '') }}"></label>
            <label><span>Jenis Kelamin</span>
              <select name="jenis_kelamin" class="input bg-white">
                <option value="">-</option>
                <option value="Laki-laki" @selected(old('jenis_kelamin',$user->profile->jenis_kelamin ?? '')=='Laki-laki')>Laki-laki</option>
                <option value="Perempuan" @selected(old('jenis_kelamin',$user->profile->jenis_kelamin ?? '')=='Perempuan')>Perempuan</option>
              </select>
            </label>
            <label><span>Agama</span><input name="agama" class="input" value="{{ old('agama',$user->profile->agama ?? '') }}"></label>
            <label><span>Status Perkawinan</span>
              <select name="status_perkawinan" class="input bg-white">
                <option value="">-</option>
                <option value="Belum Kawin" @selected(old('status_perkawinan',$user->profile->status_perkawinan ?? '')=='Belum Kawin')>Belum Kawin</option>
                <option value="Kawin" @selected(old('status_perkawinan',$user->profile->status_perkawinan ?? '')=='Kawin')>Kawin</option>
                <option value="Cerai" @selected(old('status_perkawinan',$user->profile->status_perkawinan ?? '')=='Cerai')>Cerai</option>
              </select>
            </label>
            <label><span>Golongan Darah</span>
              <select name="golongan_darah" class="input bg-white">
                <option value="">-</option>
                @foreach(['A','B','AB','O'] as $g) <option value="{{ $g }}" @selected(old('golongan_darah',$user->profile->golongan_darah ?? '')==$g)>{{ $g }}</option> @endforeach
              </select>
            </label>
            <label><span>NIP Baru</span><input name="nip_baru" class="input" value="{{ old('nip_baru',$user->profile->nip_baru ?? '') }}"></label>
            <label><span>NIP Lama</span><input name="nip_lama" class="input" value="{{ old('nip_lama',$user->profile->nip_lama ?? '') }}"></label>
            <label class="sm:col-span-2"><span>Keterangan</span><input name="keterangan" class="input" value="{{ old('keterangan',$user->profile->keterangan ?? '') }}"></label>
          </div>
        </div>

        <!-- ================= KEPEGAWAIAN ================= -->
        <div class="tab-content hidden mt-5" id="tab-kepegawaian">
          <div class="grid sm:grid-cols-2 gap-4">
            <label><span>Status Pegawai</span>
              <select name="status_pegawai" class="input bg-white">
                <option value="">-</option>
                <option value="PNS" @selected(old('status_pegawai',$user->profile->status_pegawai ?? '')=='PNS')>PNS</option>
                <option value="PPPK" @selected(old('status_pegawai',$user->profile->status_pegawai ?? '')=='PPPK')>PPPK</option>
                <option value="Non ASN" @selected(old('status_pegawai',$user->profile->status_pegawai ?? '')=='Non ASN')>Non ASN</option>
              </select>
            </label>
            <label><span>Jabatan</span><input name="jabatan" class="input" value="{{ old('jabatan',$user->profile->jabatan ?? '') }}"></label>
            <label><span>Golongan</span><input name="golongan" class="input" value="{{ old('golongan',$user->profile->golongan ?? '') }}"></label>
            <label><span>TMT PNS</span><input type="date" name="tmt_pns" class="input" value="{{ old('tmt_pns',$user->profile->tmt_pns ?? '') }}"></label>
            <label><span>Atasan Langsung</span><input name="atasan_langsung" class="input" value="{{ old('atasan_langsung',$user->profile->atasan_langsung ?? '') }}"></label>
            <label><span>Golongan Ruang</span><input name="golongan_ruang" class="input" value="{{ old('golongan_ruang',$user->profile->golongan_ruang ?? '') }}"></label>
            <label><span>TMT Golongan</span><input type="date" name="tmt_golongan" class="input" value="{{ old('tmt_golongan',$user->profile->tmt_golongan ?? '') }}"></label>
            <label><span>Masa Kerja (Tahun)</span><input type="number" name="masa_kerja_tahun" class="input" value="{{ old('masa_kerja_tahun',$user->profile->masa_kerja_tahun ?? '') }}"></label>
            <label><span>Masa Kerja (Bulan)</span><input type="number" name="masa_kerja_bulan" class="input" value="{{ old('masa_kerja_bulan',$user->profile->masa_kerja_bulan ?? '') }}"></label>
            <label><span>TMT Jabatan</span><input type="date" name="tmt_jabatan" class="input" value="{{ old('tmt_jabatan',$user->profile->tmt_jabatan ?? '') }}"></label>
            <label><span>Eselon</span><input name="eselon" class="input" value="{{ old('eselon',$user->profile->eselon ?? '') }}"></label>
            <label><span>Jabatan Struktural</span><input name="jabatan_struktural" class="input" value="{{ old('jabatan_struktural',$user->profile->jabatan_struktural ?? '') }}"></label>
            <label><span>Jabatan Fungsional</span><input name="jabatan_fungsional" class="input" value="{{ old('jabatan_fungsional',$user->profile->jabatan_fungsional ?? '') }}"></label>
            <label><span>Jabatan Teknis</span><input name="jabatan_teknis" class="input" value="{{ old('jabatan_teknis',$user->profile->jabatan_teknis ?? '') }}"></label>
            <label><span>Unit Organisasi (Unor)</span><input name="unor" class="input" value="{{ old('unor',$user->profile->unor ?? '') }}"></label>
          </div>
        </div>

        <!-- ================= ALAMAT ================= -->
        <div class="tab-content hidden mt-5" id="tab-alamat">
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="sm:col-span-2"><span>Alamat KTP</span><textarea name="alamat_ktp" class="input">{{ old('alamat_ktp',$user->profile->alamat_ktp ?? '') }}</textarea></label>
            <label class="sm:col-span-2"><span>Alamat Domisili</span><textarea name="alamat_domisili" class="input">{{ old('alamat_domisili',$user->profile->alamat_domisili ?? '') }}</textarea></label>
            <label><span>NPWP</span><input name="npwp" class="input" value="{{ old('npwp',$user->profile->npwp ?? '') }}"></label>
            <label><span>BPJS Kesehatan</span><input name="bpjs_kesehatan" class="input" value="{{ old('bpjs_kesehatan',$user->profile->bpjs_kesehatan ?? '') }}"></label>
            <label><span>BPJS Ketenagakerjaan</span><input name="bpjs_ketenagakerjaan" class="input" value="{{ old('bpjs_ketenagakerjaan',$user->profile->bpjs_ketenagakerjaan ?? '') }}"></label>
            <label><span>Nama Bank</span><input name="bank_nama" class="input" value="{{ old('bank_nama',$user->profile->bank_nama ?? 'Bank Sulselbar') }}"></label>
            <label><span>Nomor Rekening</span><input name="nomor_rekening" class="input" value="{{ old('nomor_rekening',$user->profile->nomor_rekening ?? '') }}"></label>
            <label><span>Atas Nama Rekening</span><input name="nama_rekening" class="input" value="{{ old('nama_rekening',$user->profile->nama_rekening ?? '') }}"></label>
          </div>
        </div>

        <!-- ================= KELUARGA ================= -->
        <div class="tab-content hidden mt-5" id="tab-keluarga">
          <div class="grid sm:grid-cols-2 gap-4">
            <label><span>Nama Pasangan</span><input name="nama_pasangan" class="input" value="{{ old('nama_pasangan',$user->profile->nama_pasangan ?? '') }}"></label>
            <label><span>Pekerjaan Pasangan</span><input name="pekerjaan_pasangan" class="input" value="{{ old('pekerjaan_pasangan',$user->profile->pekerjaan_pasangan ?? '') }}"></label>
            <label><span>Jumlah Anak</span><input type="number" name="jumlah_anak" class="input" value="{{ old('jumlah_anak',$user->profile->jumlah_anak ?? '') }}"></label>
            <label><span>Kontak Darurat</span><input name="kontak_darurat" class="input" value="{{ old('kontak_darurat',$user->profile->kontak_darurat ?? '') }}"></label>
          </div>
        </div>

        <!-- ================= PENDIDIKAN ================= -->
        <div class="tab-content hidden mt-5" id="tab-pendidikan">
          <div class="grid sm:grid-cols-2 gap-4">
            <label><span>Pendidikan Terakhir</span>
              <select name="pendidikan_terakhir" class="input bg-white">
                <option value="">-</option>
                <option value="SMA" @selected(old('pendidikan_terakhir',$user->profile->pendidikan_terakhir ?? '')=='SMA')>SMA</option>
                <option value="D3" @selected(old('pendidikan_terakhir',$user->profile->pendidikan_terakhir ?? '')=='D3')>D3</option>
                <option value="S1" @selected(old('pendidikan_terakhir',$user->profile->pendidikan_terakhir ?? '')=='S1')>S1</option>
                <option value="S2" @selected(old('pendidikan_terakhir',$user->profile->pendidikan_terakhir ?? '')=='S2')>S2</option>
                <option value="S3" @selected(old('pendidikan_terakhir',$user->profile->pendidikan_terakhir ?? '')=='S3')>S3</option>
              </select>
            </label>
            <label><span>Jurusan</span><input name="jurusan" class="input" value="{{ old('jurusan',$user->profile->jurusan ?? '') }}"></label>
            <label><span>Tahun Lulus</span><input type="number" name="tahun_lulus" class="input" value="{{ old('tahun_lulus',$user->profile->tahun_lulus ?? '') }}"></label>
          </div>
        </div>

        <!-- ================= SERTIFIKAT ================= -->
        <div class="tab-content hidden mt-5" id="tab-sertifikat">
          <div id="kompetensi-wrapper" class="space-y-4">
            @foreach($user->kompetensis as $k)
            <div class="border p-4 rounded-xl bg-gray-50/50">
              <div class="grid sm:grid-cols-2 gap-4">
                <input type="hidden" name="kompetensi_id[]" value="{{ $k->id }}">
                <input type="hidden" name="existing_file_path[]" value="{{ $k->file_path }}">
                <input type="hidden" name="existing_file_name[]" value="{{ $k->file_name }}">
                <input type="hidden" name="existing_file_mime[]" value="{{ $k->file_mime }}">
                <label><span>Nama Sertifikat</span><input name="nama_sertifikat[]" class="input" value="{{ $k->nama_sertifikat }}"></label>
                <label><span>Bidang</span><input name="bidang_sertifikat[]" class="input" value="{{ $k->bidang_sertifikat }}"></label>
                <label><span>Tahun</span><input type="number" name="tahun_sertifikat[]" class="input" value="{{ $k->tahun_sertifikat }}"></label>
                <label><span>Upload File Sertifikat (Ganti)</span>
                  <input type="file" name="file_sertifikat[]" class="input bg-white text-sm">
                  @if($k->file_path)
                    <a href="{{ asset('storage/'.$k->file_path) }}" target="_blank" class="text-xs font-semibold text-maroon underline mt-2 block">Lihat File</a>
                  @endif
                </label>
              </div>
            </div>
            @endforeach
          </div>
          <button type="button" onclick="addKompetensi()" class="mt-4 px-4 py-2 bg-gray-100 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-sm">
            + Tambah Sertifikat
          </button>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 mt-6">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Foto Profil</span>
            <input name="avatar" type="file" accept=".jpg,.jpeg,.png" class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 focus:border-maroon bg-white p-1.5">
            <p class="text-[11px] text-gray-500 mt-1">JPG/PNG, maks 2MB.</p>
          </label>
        </div>

      </form>
    </section>

    {{-- BAGIAN KANAN (ASIDE) --}}
    <aside class="space-y-6">
      
      {{-- Ringkasan & Foto Saat Ini --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col items-center mb-6 border-b pb-4">
          @if ($user->profile_photo_path)
            <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 shadow-sm" alt="Foto">
            <form method="POST" action="{{ route('sigap-pegawai.avatar.destroy', $user) }}" class="mt-3 w-full">
              @csrf @method('DELETE')
              <button class="w-full px-3 py-1.5 rounded-lg border text-red-700 border-red-200 bg-red-50 hover:bg-red-100 text-xs font-semibold transition-colors" onclick="return confirm('Hapus foto profil ini?')">Hapus Foto Utama</button>
            </form>
          @else
            <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 font-bold border-4 border-gray-50 shadow-sm text-2xl">
              {{ strtoupper(substr($user->name,0,1)) }}
            </div>
          @endif
        </div>

        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-3">Ringkasan Akun</h3>
        <dl class="space-y-2 text-sm">
          <div class="flex justify-between"><dt class="text-gray-500">Nama</dt><dd class="font-semibold text-gray-900">{{ $user->name }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Username</dt><dd class="font-semibold text-gray-900">{{ $user->username }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd class="font-semibold text-gray-900">{{ $user->email }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-500">Kategori</dt><dd class="font-semibold text-gray-900">{{ $user->unit ?: '—' }}</dd></div>
        </dl>
      </div>

      {{-- [FITUR BARU] Admin Upload Dokumen Pribadi --}}
      <div x-data="{ openUpload: false }" class="bg-white border-2 border-maroon/10 rounded-2xl p-6 shadow-sm overflow-hidden relative">
          <div class="absolute top-0 left-0 w-1 h-full bg-maroon"></div>
          
          <h3 class="text-base font-bold text-gray-900 mb-1">Arsip Dokumen Pribadi</h3>
          <p class="text-xs text-gray-500 leading-relaxed mb-4">Bantu unggah dokumen penting (KTP, KK) milik pegawai ini secara privat.</p>

          {{-- Tombol Buka Modal Upload --}}
          <button type="button" @click="openUpload = !openUpload" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 text-white font-semibold text-sm hover:bg-black transition-colors shadow-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
              <span x-text="openUpload ? 'Batal Upload' : 'Upload Berkas Pegawai'"></span>
          </button>

          {{-- Form Upload (Toggle) --}}
          <div x-show="openUpload" x-cloak x-transition class="mt-4 pt-4 border-t border-gray-100 space-y-4">
              <form action="{{ route('sigap-pegawai.docs.store', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                  @csrf
                  <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Dokumen <span class="text-red-500">*</span></label>
                      <select name="type" required class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-maroon focus:ring-maroon bg-gray-50">
                          <option value="" disabled selected>Pilih Jenis</option>
                          <option value="ktp">KTP</option>
                          <option value="kk">Kartu Keluarga (KK)</option>
                          <option value="npwp">NPWP</option>
                          <option value="bpjs">BPJS</option>
                          <option value="buku_rekening">Buku Rekening</option>
                          <option value="ijazah">Ijazah</option>
                          <option value="sk">SK Pegawai</option>
                          <option value="other">Lainnya</option>
                      </select>
                  </div>
                  <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Berkas <span class="text-red-500">*</span></label>
                      <input type="text" name="title" required placeholder="Contoh: KTP Atas Nama Budi" class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm focus:border-maroon focus:ring-maroon">
                  </div>
                  
                  {{-- Gembok Khusus Admin Set PIN --}}
                  <div class="p-3 bg-amber-50/50 border border-amber-200/60 rounded-xl space-y-3">
                      <div class="flex items-center gap-1.5 text-amber-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span class="text-[11px] font-bold">Kunci PIN (Opsional)</span>
                      </div>
                      <input type="password" name="access_code" placeholder="Buat PIN Rahasia" class="w-full rounded-lg border-amber-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                      <input type="text" name="access_code_hint" placeholder="Hint PIN (Cth: NIK)" class="w-full rounded-lg border-amber-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500">
                  </div>

                  <div>
                      <label class="block text-xs font-semibold text-gray-700 mb-1">File Dokumen <span class="text-red-500">*</span></label>
                      <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-maroon/10 file:text-maroon hover:file:bg-maroon/20 border border-gray-200 rounded-lg cursor-pointer bg-white">
                  </div>

                  <button type="submit" class="w-full px-4 py-2.5 rounded-xl bg-maroon hover:bg-maroon-800 text-white font-bold text-sm shadow-sm transition-colors">
                      Simpan & Unggah Dokumen
                  </button>
              </form>
          </div>

          {{-- List Dokumen Yang Sudah Diunggah --}}
          <div class="mt-6 border-t border-gray-100 pt-4">
              <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Tersimpan ({{ isset($docs) ? $docs->count() : 0 }})</h4>
              <ul class="space-y-2">
                  @if(isset($docs) && $docs->count() > 0)
                      @foreach($docs as $doc)
                          <li class="flex items-center justify-between p-3 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:shadow-sm transition-all group">
                              <div class="truncate pr-3">
                                  <p class="text-xs font-bold text-gray-900 truncate">{{ $doc->title }}</p>
                                  <div class="flex items-center gap-2 mt-1">
                                      <span class="text-[9px] font-bold text-gray-500 bg-gray-200/60 px-1.5 py-0.5 rounded uppercase">{{ $doc->type }}</span>
                                      @if($doc->access_code_hash)
                                          <span class="flex items-center gap-0.5 text-[9px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">
                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                            PIN
                                          </span>
                                      @endif
                                  </div>
                              </div>
                              <a href="{{ route('pegawai.docs.show', $doc->id) }}" target="_blank" class="shrink-0 p-2 text-gray-400 hover:text-maroon bg-white border border-gray-200 rounded-lg shadow-sm transition-colors" title="Buka Detail">
                                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                              </a>
                          </li>
                      @endforeach
                  @else
                      <li class="text-xs text-gray-400 text-center py-4 bg-gray-50 rounded-xl border border-dashed border-gray-200">Belum ada dokumen yang diunggah.</li>
                  @endif
              </ul>
          </div>

      </div>
    </aside>

  </main>
@endsection

@push('scripts')
<script>
// Script Tab Navigasi
document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.onclick = () => {
        document.querySelectorAll(".tab-btn").forEach(b => {
            b.classList.remove("border-maroon", "text-maroon");
            b.classList.add("text-gray-500");
        });
        document.querySelectorAll(".tab-content").forEach(c => {
            c.classList.add("hidden");
        });
        btn.classList.add("border-maroon", "text-maroon");
        btn.classList.remove("text-gray-500");
        document.getElementById("tab-" + btn.dataset.tab).classList.remove("hidden");
    }
});

// Script Tambah Sertifikat Baru
function addKompetensi(){
    let wrapper = document.getElementById('kompetensi-wrapper');
    wrapper.insertAdjacentHTML('beforeend',`
    <div class="border border-gray-200 p-4 rounded-xl bg-gray-50/50 mt-4">
        <div class="grid sm:grid-cols-2 gap-4">
            <label><span>Nama Sertifikat</span><input name="nama_sertifikat[]" class="input bg-white"></label>
            <label><span>Bidang Sertifikat</span><input name="bidang_sertifikat[]" class="input bg-white"></label>
            <label><span>Tahun</span><input type="number" name="tahun_sertifikat[]" class="input bg-white"></label>
            <label><span>Upload File</span><input type="file" name="file_sertifikat[]" class="input bg-white border border-gray-300 p-1.5 text-sm"></label>
        </div>
    </div>
    `);
}
</script>
@endpush