@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Edit SK <span class="text-maroon">KGB Pegawai</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Perbarui data ketetapan SK KGB untuk pegawai <b>{{ $riwayat->user->name ?? '' }}</b>.
    </p>
  </div>

  <a href="{{ route('sigap-kgb.index') }}"
     class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
    &larr; Kembali ke Monitoring
  </a>
</section>

<form action="{{ route('sigap-kgb.update', $riwayat->id) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-5">
  @csrf
  @method('PUT')

  {{-- IDENTITAS PEGAWAI --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <h2 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
      <span class="w-6 h-6 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center text-xs font-extrabold">1</span>
      Identitas Pegawai
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pegawai</label>
        <input type="text" disabled value="{{ $riwayat->user->name ?? '-' }} (NIP: {{ $riwayat->user->nip ?? '-' }})"
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border bg-gray-50 text-gray-500 cursor-not-allowed">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Pangkat / Golongan <span class="text-red-500">*</span></label>
        <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan', $riwayat->pangkat_golongan) }}" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div class="md:col-span-2">
        <label class="block text-xs font-semibold text-gray-700 mb-1">Jabatan</label>
        <input type="text" name="jabatan" value="{{ old('jabatan', $riwayat->jabatan) }}"
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>
    </div>
  </div>

  {{-- DATA SK LAMA --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
    <h2 class="text-sm font-bold text-gray-900 mb-4 pb-2 border-b border-gray-100 flex items-center gap-2">
      <span class="w-6 h-6 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center text-xs font-extrabold">2</span>
      Data Surat Keputusan (SK)
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor SK <span class="text-red-500">*</span></label>
        <input type="text" name="nomor_sk_lama" value="{{ old('nomor_sk_lama', $riwayat->nomor_sk_lama) }}" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal SK <span class="text-red-500">*</span></label>
        <input type="date" name="tanggal_sk_lama" value="{{ old('tanggal_sk_lama', $riwayat->tanggal_sk_lama ? $riwayat->tanggal_sk_lama->format('Y-m-d') : '') }}" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">TMT SK <span class="text-red-500">*</span></label>
        <input type="date" name="tmt_lama" value="{{ old('tmt_lama', $riwayat->tmt_lama ? $riwayat->tmt_lama->format('Y-m-d') : '') }}" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Masa Kerja (Tahun) <span class="text-red-500">*</span></label>
        <input type="number" name="mkg_tahun_lama" value="{{ old('mkg_tahun_lama', $riwayat->mkg_tahun_lama) }}" min="0" max="40" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Masa Kerja (Bulan)</label>
        <input type="number" name="mkg_bulan_lama" value="{{ old('mkg_bulan_lama', $riwayat->mkg_bulan_lama) }}" min="0" max="11" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 mb-1">Gaji Pokok Lama (Rp) <span class="text-red-500">*</span></label>
        <input type="number" name="gaji_pokok_lama" value="{{ old('gaji_pokok_lama', $riwayat->gaji_pokok_lama) }}" min="0" required
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div class="md:col-span-3">
        <label class="block text-xs font-semibold text-gray-700 mb-1">Pejabat Penetap</label>
        <input type="text" name="pejabat_penetap" value="{{ old('pejabat_penetap', $riwayat->pejabat_penetap) }}"
               class="w-full rounded-xl text-xs px-3.5 py-2.5 border">
      </div>

      <div class="md:col-span-3">
        <label class="block text-xs font-semibold text-gray-700 mb-1">Ganti Berkas Scan SK (Opsional)</label>
        <input type="file" name="file_sk_lama" accept=".pdf,.jpg,.jpeg,.png"
               class="w-full rounded-xl text-xs px-3.5 py-2 border file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-maroon-50 file:text-maroon">
      </div>
    </div>
  </div>

  <div class="flex justify-end gap-2">
    <a href="{{ route('sigap-kgb.index') }}"
       class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
      Batal
    </a>
    <button type="submit"
            class="px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 shadow-sm transition">
      Perbarui Data & Hitung Ulang
    </button>
  </div>
</form>
@endsection