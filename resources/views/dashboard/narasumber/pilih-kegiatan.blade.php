@extends('layouts.app')
@section('content')
<div class="max-w-4xl">
  <h1 class="text-xl font-extrabold text-gray-900 mb-4">Pilih Kegiatan untuk Narasumber</h1>
  <form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari kegiatan..." class="rounded-xl border-gray-300 w-full md:w-1/2">
    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-xl text-sm">Cari</button>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($kegiatans as $kegiatan)
    <div class="border rounded-2xl p-4 bg-white shadow-sm flex flex-col justify-between">
      <div>
        <h3 class="font-bold text-gray-900 mb-1">{{ $kegiatan->nama_kegiatan }}</h3>
        <p class="text-xs text-gray-500">{{ $kegiatan->hari_tanggal }}</p>
      </div>
      <a href="{{ route('sigap-narasumber.qr', $kegiatan->uuid) }}" class="mt-4 text-center px-4 py-2 bg-maroon text-white text-sm font-semibold rounded-xl hover:bg-maroon-800">
        Buat QR Kesediaan
      </a>
    </div>
    @endforeach
  </div>
  <div class="mt-4">{{ $kegiatans->links() }}</div>
</div>
@endsection