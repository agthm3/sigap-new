@extends('layouts.app')

<style>
.input{
    width:100%;
    border:1px solid #d1d5db;
    border-radius:8px;
    padding:8px;
    margin-top:6px;
}
.section-title{
    font-weight:bold;
    font-size:18px;
    margin-bottom:12px;
}
</style>

@section('title','Edit Profil — SIGAP BRIDA')

@section('content')

@php
    $profile = $user->profile;
@endphp

<nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex gap-1 text-gray-600">
        <li>
            <a href="{{ route('pegawai.profil') }}" class="hover:text-maroon">
                Profil Pegawai
            </a>
        </li>
        <li>›</li>
        <li class="font-semibold text-gray-900">Edit Profil</li>
    </ol>
</nav>

<section class="max-w-7xl mx-auto px-4">
<div class="bg-white border rounded-2xl p-6">

<form method="POST" action="{{ route('pegawai.profil.update') }}" enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- ================= AKUN ================= --}}
<div>
<h3 class="section-title">Informasi Akun</h3>

<div class="grid sm:grid-cols-2 gap-4">

<label>
<span>Nama Lengkap</span>
<input name="name" class="input" value="{{ old('name',$user->name) }}">
</label>

<label>
<span>Email</span>
<input name="email" class="input" value="{{ old('email',$user->email) }}">
</label>

<label>
<span>Nomor HP</span>
<input name="nomor_hp" class="input" value="{{ old('nomor_hp',$user->nomor_hp) }}">
</label>

<label>
<span>Password Baru (opsional)</span>
<input type="password" name="password" class="input">
</label>

</div>
</div>

<hr>

{{-- ================= TAB NAV ================= --}}
<div>
<h3 class="section-title">Detail Profil Pegawai</h3>

<div class="border-b mt-4">
<nav class="flex gap-6 text-sm font-semibold">

<button type="button" class="tab-btn border-b-2 border-maroon pb-2" data-tab="identitas">Identitas</button>
<button type="button" class="tab-btn pb-2" data-tab="kepegawaian">Kepegawaian</button>
<button type="button" class="tab-btn pb-2" data-tab="alamat">Alamat & Administrasi</button>
<button type="button" class="tab-btn pb-2" data-tab="keluarga">Keluarga</button>
<button type="button" class="tab-btn pb-2" data-tab="pendidikan">Pendidikan</button>
<button type="button" class="tab-btn pb-2" data-tab="kompetensi">Kompetensi</button>

</nav>
</div>
</div>

{{-- ================= IDENTITAS ================= --}}
<div id="tab-identitas" class="tab-content mt-5">
<div class="grid sm:grid-cols-2 gap-4">

<label>
<span>NIK</span>
<input name="nik" class="input" value="{{ old('nik',$profile->nik ?? '') }}">
</label>

<label>
<span>Tempat Lahir</span>
<input name="tempat_lahir" class="input" value="{{ old('tempat_lahir',$profile->tempat_lahir ?? '') }}">
</label>

<label>
<span>Tanggal Lahir</span>
<input type="date" name="tanggal_lahir" class="input" value="{{ old('tanggal_lahir',$profile->tanggal_lahir ?? '') }}">
</label>

<label>
<span>Jenis Kelamin</span>
<select name="jenis_kelamin" class="input">
<option value="">-</option>
<option value="Laki-laki" @selected(old('jenis_kelamin',$profile->jenis_kelamin ?? '')=='Laki-laki')>Laki-laki</option>
<option value="Perempuan" @selected(old('jenis_kelamin',$profile->jenis_kelamin ?? '')=='Perempuan')>Perempuan</option>
</select>
</label>

<label>
<span>Agama</span>
<input name="agama" class="input" value="{{ old('agama',$profile->agama ?? '') }}">
</label>

<label>
<span>Status Perkawinan</span>
<select name="status_perkawinan" class="input">
<option value="Belum Kawin" @selected(old('status_perkawinan',$profile->status_perkawinan ?? '')=='Belum Kawin')>Belum Kawin</option>
<option value="Kawin" @selected(old('status_perkawinan',$profile->status_perkawinan ?? '')=='Kawin')>Kawin</option>
<option value="Cerai" @selected(old('status_perkawinan',$profile->status_perkawinan ?? '')=='Cerai')>Cerai</option>
</select>
</label>

<label>
<span>Golongan Darah</span>
<select name="golongan_darah" class="input">
<option value="">-</option>
<option value="A" @selected(old('golongan_darah',$profile->golongan_darah ?? '')=='A')>A</option>
<option value="B" @selected(old('golongan_darah',$profile->golongan_darah ?? '')=='B')>B</option>
<option value="AB" @selected(old('golongan_darah',$profile->golongan_darah ?? '')=='AB')>AB</option>
<option value="O" @selected(old('golongan_darah',$profile->golongan_darah ?? '')=='O')>O</option>
</select>
</label>

<label>
<span>NIP Baru</span>
<input name="nip_baru" class="input" value="{{ old('nip_baru',$profile->nip_baru ?? '') }}">
</label>

<label>
<span>NIP Lama</span>
<input name="nip_lama" class="input" value="{{ old('nip_lama',$profile->nip_lama ?? '') }}">
</label>

<label class="sm:col-span-2">
<span>Keterangan</span>
<input name="keterangan" class="input" value="{{ old('keterangan',$profile->keterangan ?? '') }}">
</label>

</div>
</div>

{{-- ================= KEPEGAWAIAN ================= --}}
<div id="tab-kepegawaian" class="tab-content hidden mt-5">
<div class="grid sm:grid-cols-2 gap-4">

<label>
<span>Status Pegawai</span>
<select name="status_pegawai" class="input">
<option value="">-</option>
<option value="PNS" @selected(old('status_pegawai',$profile->status_pegawai ?? '')=='PNS')>PNS</option>
<option value="PPPK" @selected(old('status_pegawai',$profile->status_pegawai ?? '')=='PPPK')>PPPK</option>
<option value="Non ASN" @selected(old('status_pegawai',$profile->status_pegawai ?? '')=='Non ASN')>Non ASN</option>
</select>
</label>

<label>
<span>Jabatan</span>
<input name="jabatan" class="input" value="{{ old('jabatan',$profile->jabatan ?? '') }}">
</label>

<label>
<span>Golongan</span>
<input name="golongan" class="input" value="{{ old('golongan',$profile->golongan ?? '') }}">
</label>

<label>
<span>TMT PNS</span>
<input type="date" name="tmt_pns" class="input" value="{{ old('tmt_pns',$profile->tmt_pns ?? '') }}">
</label>

<label>
<span>Atasan Langsung</span>
<input name="atasan_langsung" class="input" value="{{ old('atasan_langsung',$profile->atasan_langsung ?? '') }}">
</label>

<label>
<span>Golongan Ruang</span>
<input name="golongan_ruang" class="input" value="{{ old('golongan_ruang',$profile->golongan_ruang ?? '') }}">
</label>

<label>
<span>TMT Golongan</span>
<input type="date" name="tmt_golongan" class="input" value="{{ old('tmt_golongan',$profile->tmt_golongan ?? '') }}">
</label>

<label>
<span>Masa Kerja (Tahun)</span>
<input type="number" name="masa_kerja_tahun" class="input" value="{{ old('masa_kerja_tahun',$profile->masa_kerja_tahun ?? '') }}">
</label>

<label>
<span>Masa Kerja (Bulan)</span>
<input type="number" name="masa_kerja_bulan" class="input" value="{{ old('masa_kerja_bulan',$profile->masa_kerja_bulan ?? '') }}">
</label>

<label>
<span>TMT Jabatan</span>
<input type="date" name="tmt_jabatan" class="input" value="{{ old('tmt_jabatan',$profile->tmt_jabatan ?? '') }}">
</label>

<label>
<span>Eselon</span>
<input name="eselon" class="input" value="{{ old('eselon',$profile->eselon ?? '') }}">
</label>

<label>
<span>Jabatan Struktural</span>
<input name="jabatan_struktural" class="input" value="{{ old('jabatan_struktural',$profile->jabatan_struktural ?? '') }}">
</label>

<label>
<span>Jabatan Fungsional</span>
<input name="jabatan_fungsional" class="input" value="{{ old('jabatan_fungsional',$profile->jabatan_fungsional ?? '') }}">
</label>

<label>
<span>Jabatan Teknis</span>
<input name="jabatan_teknis" class="input" value="{{ old('jabatan_teknis',$profile->jabatan_teknis ?? '') }}">
</label>

<label>
<span>Unit Organisasi (Unor)</span>
<input name="unor" class="input" value="{{ old('unor',$profile->unor ?? '') }}">
</label>

</div>
</div>

{{-- ================= ALAMAT ================= --}}
<div id="tab-alamat" class="tab-content hidden mt-5">
<div class="grid sm:grid-cols-2 gap-4">

<label class="sm:col-span-2">
<span>Alamat KTP</span>
<textarea name="alamat_ktp" class="input">{{ old('alamat_ktp',$profile->alamat_ktp ?? '') }}</textarea>
</label>

<label class="sm:col-span-2">
<span>Alamat Domisili</span>
<textarea name="alamat_domisili" class="input">{{ old('alamat_domisili',$profile->alamat_domisili ?? '') }}</textarea>
</label>

<label>
<span>NPWP</span>
<input name="npwp" class="input" value="{{ old('npwp',$profile->npwp ?? '') }}">
</label>

<label>
<span>BPJS Kesehatan</span>
<input name="bpjs_kesehatan" class="input" value="{{ old('bpjs_kesehatan',$profile->bpjs_kesehatan ?? '') }}">
</label>

<label>
<span>BPJS Ketenagakerjaan</span>
<input name="bpjs_ketenagakerjaan" class="input" value="{{ old('bpjs_ketenagakerjaan',$profile->bpjs_ketenagakerjaan ?? '') }}">
</label>

<label>
<span>Nama Bank</span>
<input name="bank_nama" class="input" value="{{ old('bank_nama',$profile->bank_nama ?? 'Bank Sulselbar') }}">
</label>

<label>
<span>Nomor Rekening</span>
<input name="nomor_rekening" class="input" value="{{ old('nomor_rekening',$profile->nomor_rekening ?? '') }}">
</label>

<label>
<span>Atas Nama Rekening</span>
<input name="nama_rekening" class="input" value="{{ old('nama_rekening',$profile->nama_rekening ?? '') }}">
</label>

</div>
</div>

{{-- ================= KELUARGA ================= --}}
<div id="tab-keluarga" class="tab-content hidden mt-5">
<div class="grid sm:grid-cols-2 gap-4">

<label>
<span>Nama Pasangan</span>
<input name="nama_pasangan" class="input" value="{{ old('nama_pasangan',$profile->nama_pasangan ?? '') }}">
</label>

<label>
<span>Pekerjaan Pasangan</span>
<input name="pekerjaan_pasangan" class="input" value="{{ old('pekerjaan_pasangan',$profile->pekerjaan_pasangan ?? '') }}">
</label>

<label>
<span>Jumlah Anak</span>
<input type="number" name="jumlah_anak" class="input" value="{{ old('jumlah_anak',$profile->jumlah_anak ?? '') }}">
</label>

<label>
<span>Kontak Darurat</span>
<input name="kontak_darurat" class="input" value="{{ old('kontak_darurat',$profile->kontak_darurat ?? '') }}">
</label>

</div>
</div>

{{-- ================= PENDIDIKAN ================= --}}
<div id="tab-pendidikan" class="tab-content hidden mt-5">
<div class="grid sm:grid-cols-2 gap-4">

<label>
<span>Pendidikan Terakhir</span>
<select name="pendidikan_terakhir" class="input">
<option value="SMA" @selected(old('pendidikan_terakhir',$profile->pendidikan_terakhir ?? '')=='SMA')>SMA</option>
<option value="D3" @selected(old('pendidikan_terakhir',$profile->pendidikan_terakhir ?? '')=='D3')>D3</option>
<option value="S1" @selected(old('pendidikan_terakhir',$profile->pendidikan_terakhir ?? '')=='S1')>S1</option>
<option value="S2" @selected(old('pendidikan_terakhir',$profile->pendidikan_terakhir ?? '')=='S2')>S2</option>
<option value="S3" @selected(old('pendidikan_terakhir',$profile->pendidikan_terakhir ?? '')=='S3')>S3</option>
</select>
</label>

<label>
<span>Jurusan</span>
<input name="jurusan" class="input" value="{{ old('jurusan',$profile->jurusan ?? '') }}">
</label>

<label>
<span>Tahun Lulus</span>
<input type="number" name="tahun_lulus" class="input" value="{{ old('tahun_lulus',$profile->tahun_lulus ?? '') }}">
</label>

</div>
</div>
{{-- ================= KOMPETENSI / SERTIFIKAT ================= --}}
<div id="tab-kompetensi" class="tab-content hidden mt-5">

<div id="kompetensi-wrapper" class="space-y-6">

@foreach($user->kompetensis as $k)
<div class="border p-4 rounded-xl bg-gray-50">

<input type="hidden" name="kompetensi_id[]" value="{{ $k->id }}">
<input type="hidden" name="existing_file_path[]" value="{{ $k->file_path }}">
<input type="hidden" name="existing_file_name[]" value="{{ $k->file_name }}">
<input type="hidden" name="existing_file_mime[]" value="{{ $k->file_mime }}">

<div class="grid sm:grid-cols-2 gap-4">

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
class="mt-4 px-4 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-800">
+ Tambah Sertifikat
</button>

</div>
<hr>

<button class="px-6 py-2 bg-maroon text-white rounded-lg">
Simpan Perubahan
</button>

</form>
</div>
</section>

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
<span>Upload File Sertifikat</span>
<input type="file" name="file_sertifikat[]" class="input">
</label>

</div>
</div>
`);
}
</script>
@endsection