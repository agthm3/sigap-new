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
        <p class="text-sm text-gray-600 mt-1">Ubah data dasar, role, dan foto profil.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('sigap-pegawai.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Kembali</a>
        <button form="fEdit" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan</button>
      </div>
    </div>
  </section>

  <main class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 space-y-6">
      <form id="fEdit"
            class="bg-white border border-gray-200 rounded-2xl p-5 space-y-5"
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
            <span class="text-sm font-semibold text-gray-700">Nama</span>
            <input name="name" type="text" required
                   value="{{ old('name',$user->name) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Username</span>
            <input name="username" type="text" required
                   value="{{ old('username',$user->username) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email</span>
            <input name="email" type="email" required
                   value="{{ old('email',$user->email) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Unit</span>
            <input name="unit" type="text"
                   value="{{ old('unit',$user->unit) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
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
          <span class="text-sm font-semibold text-gray-700">Role</span>
          <div class="mt-2 grid sm:grid-cols-3 gap-2">
            @foreach($roles as $r)
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="roles[]" value="{{ $r }}"
                       class="rounded border-gray-300 text-maroon focus:ring-maroon"
                       @checked(in_array($r, old('roles',$userRoleNames)))>
                <span>{{ $roleLabels[$r] ?? $r }}</span>
                {{-- <span>{{ ucfirst($r) }}</span> --}}
              </label>
            @endforeach
          </div>
        </div>

        <hr class="my-6">
        <hr class="my-8">

          <h3 class="text-lg font-bold text-gray-900">Detail Profil Pegawai</h3>

          <!-- TAB NAV -->
          <div class="mt-4 border-b">
            <nav class="flex gap-6 text-sm font-semibold">
              <button type="button" class="tab-btn border-b-2 border-maroon pb-2" data-tab="identitas">Identitas</button>
              <button type="button" class="tab-btn pb-2" data-tab="kepegawaian">Kepegawaian</button>
              <button type="button" class="tab-btn pb-2" data-tab="alamat">Alamat & Administrasi</button>
              <button type="button" class="tab-btn pb-2" data-tab="keluarga">Keluarga</button>
              <button type="button" class="tab-btn pb-2" data-tab="pendidikan">Pendidikan</button>
              <button type="button" class="tab-btn pb-2" data-tab="sertifikat">Kompetensi</button>
            </nav>
          </div>

          <!-- ================= IDENTITAS ================= -->
          <div class="tab-content mt-5" id="tab-identitas">

          <div class="grid sm:grid-cols-2 gap-4">

          <label class="block">
          <span>NIK</span>
          <input name="nik" class="input"
          value="{{ old('nik',$user->profile->nik ?? '') }}">
          </label>

          <label class="block">
          <span>Tempat Lahir</span>
          <input name="tempat_lahir" class="input"
          value="{{ old('tempat_lahir',$user->profile->tempat_lahir ?? '') }}">
          </label>

          <label>
          <span>Tanggal Lahir</span>
          <input type="date" name="tanggal_lahir" class="input"
          value="{{ old('tanggal_lahir',$user->profile->tanggal_lahir ?? '') }}">
          </label>

          <label>
          <span>Jenis Kelamin</span>
          <select name="jenis_kelamin" class="input">
          <option value="">-</option>
          <option value="Laki-laki">Laki-laki</option>
          <option value="Perempuan">Perempuan</option>
          </select>
          </label>

          <label>
          <span>Agama</span>
          <input name="agama" class="input"
          value="{{ old('agama',$user->profile->agama ?? '') }}">
          </label>

          <label>
          <span>Status Perkawinan</span>
          <select name="status_perkawinan" class="input">
          <option>Belum Kawin</option>
          <option>Kawin</option>
          <option>Cerai</option>
          </select>
          </label>

          <label>
          <span>Golongan Darah</span>
          <select name="golongan_darah" class="input">
          <option>A</option>
          <option>B</option>
          <option>AB</option>
          <option>O</option>
          </select>
          </label>

          <label>
          <span>NIP Baru</span>
          <input name="nip_baru" class="input"
          value="{{ old('nip_baru',$user->profile->nip_baru ?? '') }}">
          </label>

          <label>
          <span>NIP Lama</span>
          <input name="nip_lama" class="input"
          value="{{ old('nip_lama',$user->profile->nip_lama ?? '') }}">
          </label>

          <label>
          <span>Keterangan</span>
          <input name="keterangan" class="input"
          value="{{ old('keterangan',$user->profile->keterangan ?? '') }}">
          </label>

          </div>
          </div>


          <!-- ================= KEPEGAWAIAN ================= -->
          <div class="tab-content hidden mt-5" id="tab-kepegawaian">

          <div class="grid sm:grid-cols-2 gap-4">

          <label>
          <span>Status Pegawai</span>
          <select name="status_pegawai" class="input">
          <option value="">-</option>
          <option>PNS</option>
          <option>PPPK</option>
          <option>Non ASN</option>
          </select>
          </label>

          <label>
          <span>Jabatan</span>
          <input name="jabatan" class="input"
          value="{{ old('jabatan',$user->profile->jabatan ?? '') }}">
          </label>

          <label>
          <span>Golongan</span>
          <input name="golongan" class="input"
          value="{{ old('golongan',$user->profile->golongan ?? '') }}">
          </label>

          <label>
          <span>TMT PNS</span>
          <input type="date" name="tmt_pns" class="input"
          value="{{ old('tmt_pns',$user->profile->tmt_pns ?? '') }}">
          </label>

          <label>
          <span>Atasan Langsung</span>
          <input name="atasan_langsung" class="input"
          value="{{ old('atasan_langsung',$user->profile->atasan_langsung ?? '') }}">
          </label>

          <label>
          <span>Golongan Ruang</span>
          <input name="golongan_ruang" class="input"
          value="{{ old('golongan_ruang',$user->profile->golongan_ruang ?? '') }}">
          </label>

          <label>
          <span>TMT Golongan</span>
          <input type="date" name="tmt_golongan" class="input"
          value="{{ old('tmt_golongan',$user->profile->tmt_golongan ?? '') }}">
          </label>

          <label>
          <span>Masa Kerja (Tahun)</span>
          <input type="number" name="masa_kerja_tahun" class="input">
          </label>

          <label>
          <span>Masa Kerja (Bulan)</span>
          <input type="number" name="masa_kerja_bulan" class="input">
          </label>

          <label>
          <span>TMT Jabatan</span>
          <input type="date" name="tmt_jabatan" class="input">
          </label>

          <label>
          <span>Eselon</span>
          <input name="eselon" class="input">
          </label>

          <label>
          <span>Jabatan Struktural</span>
          <input name="jabatan_struktural" class="input">
          </label>

          <label>
          <span>Jabatan Fungsional</span>
          <input name="jabatan_fungsional" class="input">
          </label>

          <label>
          <span>Jabatan Teknis</span>
          <input name="jabatan_teknis" class="input">
          </label>

          <label>
          <span>Unit Organisasi (Unor)</span>
          <input name="unor" class="input">
          </label>

          </div>
          </div>


          <!-- ================= ALAMAT ================= -->
          <div class="tab-content hidden mt-5" id="tab-alamat">

          <div class="grid sm:grid-cols-2 gap-4">

          <label class="sm:col-span-2">
          <span>Alamat KTP</span>
          <textarea name="alamat_ktp" class="input">{{ old('alamat_ktp',$user->profile->alamat_ktp ?? '') }}</textarea>
          </label>

          <label class="sm:col-span-2">
          <span>Alamat Domisili</span>
          <textarea name="alamat_domisili" class="input">{{ old('alamat_domisili',$user->profile->alamat_domisili ?? '') }}</textarea>
          </label>

          <label>
          <span>NPWP</span>
          <input name="npwp" class="input"
          value="{{ old('npwp',$user->profile->npwp ?? '') }}">
          </label>

          <label>
          <span>BPJS Kesehatan</span>
          <input name="bpjs_kesehatan" class="input"
          value="{{ old('bpjs_kesehatan',$user->profile->bpjs_kesehatan ?? '') }}">
          </label>

          <label>
          <span>BPJS Ketenagakerjaan</span>
          <input name="bpjs_ketenagakerjaan" class="input"
          value="{{ old('bpjs_ketenagakerjaan',$user->profile->bpjs_ketenagakerjaan ?? '') }}">
          </label>
                    <label>
          <span>Nama Bank</span>
          <input name="bank_nama"
          class="input"
          value="{{ old('bank_nama',$user->profile->bank_nama ?? 'Bank Sulselbar') }}">
          </label>

          <label>
          <span>Nomor Rekening</span>
          <input name="nomor_rekening"
          class="input"
          value="{{ old('nomor_rekening',$user->profile->nomor_rekening ?? '') }}">
          </label>

          <label>
          <span>Atas Nama Rekening</span>
          <input name="nama_rekening"
          class="input"
          value="{{ old('nama_rekening',$user->profile->nama_rekening ?? '') }}">
          </label>

          </div>
          </div>


          <!-- ================= KELUARGA ================= -->
          <div class="tab-content hidden mt-5" id="tab-keluarga">

          <div class="grid sm:grid-cols-2 gap-4">

          <label>
          <span>Nama Pasangan</span>
          <input name="nama_pasangan" class="input">
          </label>

          <label>
          <span>Pekerjaan Pasangan</span>
          <input name="pekerjaan_pasangan" class="input">
          </label>

          <label>
          <span>Jumlah Anak</span>
          <input type="number" name="jumlah_anak" class="input">
          </label>

          <label>
          <span>Kontak Darurat</span>
          <input name="kontak_darurat" class="input">
          </label>

          </div>
          </div>

          {{-- PENDIDIKAN --}}
          <div class="tab-content hidden mt-5" id="tab-pendidikan">

          <div class="grid sm:grid-cols-2 gap-4">

          <label>
          <span>Pendidikan Terakhir</span>
          <select name="pendidikan_terakhir" class="input">
          <option>SMA</option>
          <option>D3</option>
          <option>S1</option>
          <option>S2</option>
          <option>S3</option>
          </select>
          </label>

          <label>
          <span>Jurusan</span>
          <input name="jurusan" class="input">
          </label>

          <label>
          <span>Tahun Lulus</span>
          <input type="number" name="tahun_lulus" class="input">
          </label>

          </div>
          </div>

          {{-- sertifikat --}}
          <div class="tab-content hidden mt-5" id="tab-sertifikat">

        <div id="kompetensi-wrapper" class="space-y-6">

        @foreach($user->kompetensis as $k)
        <div class="border p-4 rounded-xl bg-gray-50">
          <div class="grid sm:grid-cols-2 gap-4">

            <input type="hidden" name="kompetensi_id[]" value="{{ $k->id }}">
            <input type="hidden" name="existing_file_path[]" value="{{ $k->file_path }}">
            <input type="hidden" name="existing_file_name[]" value="{{ $k->file_name }}">
            <input type="hidden" name="existing_file_mime[]" value="{{ $k->file_mime }}">

            <label>
              <span>Nama Sertifikat</span>
              <input name="nama_sertifikat[]" class="input"
              value="{{ $k->nama_sertifikat }}">
            </label>

            <label>
              <span>Bidang Sertifikat</span>
              <input name="bidang_sertifikat[]" class="input"
              value="{{ $k->bidang_sertifikat }}">
            </label>

            <label>
              <span>Tahun Sertifikat</span>
              <input type="number" name="tahun_sertifikat[]" class="input"
              value="{{ $k->tahun_sertifikat }}">
            </label>

            <label>
              <span>Upload File Sertifikat</span>
              <input type="file" name="file_sertifikat[]" class="input">
              @if($k->file_path)
              <a href="{{ asset('storage/'.$k->file_path) }}"
                target="_blank"
                class="text-sm text-maroon underline mt-2 block">
                Lihat File
              </a>
              @endif
            </label>

          </div>
        </div>
        @endforeach

        </div>

        <button type="button"
        onclick="addKompetensi()"
        class="mt-4 px-4 py-2 bg-maroon text-white rounded-lg">
        + Tambah Sertifikat
        </button>

        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Foto Profil</span>
            <input name="avatar" type="file" accept=".jpg,.jpeg,.png"
                   class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon">
            <p class="text-[11px] text-gray-500 mt-1">JPG/PNG, maks 2MB.</p>
          </label>
          <div class="flex items-end gap-3">
            @if ($user->profile_photo_path)
              <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="w-16 h-16 rounded-full object-cover" alt="">
            @else
              <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">—</div>
            @endif
          </div>
        </div>
      </form>

        <form method="POST" action="{{ route('sigap-pegawai.avatar.destroy', $user) }}">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 rounded-md border text-red-700 border-red-300 hover:bg-red-50 text-sm"
                        onclick="return confirm('Hapus foto profil?')">
                  Hapus Foto
                </button>
        </form>
    </section>

    <aside class="space-y-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Ringkasan</h3>
        <dl class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between"><dt class="text-gray-600">Nama</dt><dd class="font-medium">{{ $user->name }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Username</dt><dd class="font-medium">{{ $user->username }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Email</dt><dd class="font-medium">{{ $user->email }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Unit</dt><dd class="font-medium">{{ $user->unit ?: '—' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Role</dt><dd class="font-medium">{{ implode(', ', $userRoleNames) ?: '—' }}</dd></div>
        </dl>
      </div>
    </aside>
  </main>
@endsection
@push('scripts')
  <script>
document.querySelectorAll(".tab-btn").forEach(btn=>{
btn.onclick=()=>{
document.querySelectorAll(".tab-btn").forEach(b=>b.classList.remove("border-maroon"))
document.querySelectorAll(".tab-content").forEach(c=>c.classList.add("hidden"))

btn.classList.add("border-maroon")
document.getElementById("tab-"+btn.dataset.tab).classList.remove("hidden")
}
})
</script>
<script>
function addKompetensi(){
let wrapper=document.getElementById('kompetensi-wrapper');

wrapper.insertAdjacentHTML('beforeend',`
<div class="border p-4 rounded-xl bg-gray-50 mt-4">
<div class="grid sm:grid-cols-2 gap-4">

<label>
<span>Nama Sertifikat</span>
<input name="nama_sertifikat[]" class="input">
</label>

<label>
<span>Bidang Sertifikat</span>
<input name="bidang_sertifikat[]" class="input">
</label>

<label>
<span>Tahun Sertifikat</span>
<input type="number" name="tahun_sertifikat[]" class="input">
</label>

<label>
<span>Upload File</span>
<input type="file" name="file_sertifikat[]" class="input">
</label>

</div>
</div>
`);
}
</script>
@endpush
