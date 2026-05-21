@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
  <div class="mb-4">
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Buat <span class="text-maroon">Kegiatan Daftar Hadir</span>
    </h1>
    <p class="text-sm text-gray-600 mt-1">Setelah disimpan, QR code akan muncul di halaman detail kegiatan.</p>
  </div>

  <form action="{{ route('sigap-daftar-hadir.store') }}" method="POST" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan</label>
      <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}"
             class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
      @error('nama_kegiatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Hari/Tanggal</label>
        <input type="text" name="hari_tanggal" value="{{ old('hari_tanggal') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        @error('hari_tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
        <input type="text" name="tempat" value="{{ old('tempat') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        @error('tempat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
        <input type="text" name="waktu" value="{{ old('waktu') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        @error('waktu') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white font-semibold hover:bg-maroon-800">
        Simpan
      </button>
      <a href="{{ route('sigap-daftar-hadir.index') }}" class="px-4 py-2 rounded-xl border text-gray-700 hover:bg-gray-50">
        Kembali
      </a>
    </div>
  </form>
</div>
@endsection