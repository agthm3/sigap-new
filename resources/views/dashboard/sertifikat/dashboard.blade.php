@extends('layouts.app')

@section('content')
<style>
[x-cloak] { display:none !important; }
</style>
<div x-data="{ openModal:false }">

<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Sertifikat</h1>
      <p class="text-sm text-gray-600 mt-1">
        Daftar kegiatan / event penerbitan sertifikat resmi BRIDA.
      </p>
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-3">
      <form action="{{ route('sigap-sertifikat.dashboard') }}" method="GET" class="w-full sm:w-auto relative">
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="Cari kegiatan, tanggal, tempat..."
          class="w-full sm:w-64 pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon text-sm"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
        </div>
      </form>

      <button
        @click="openModal=true"
        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 5v14M5 12h14"/>
        </svg>
        Tambah Kegiatan
      </button>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 pb-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

    <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 font-semibold border-b border-gray-200">
      Daftar Kegiatan Sertifikat
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b bg-white">
          <tr class="text-left">
            <th class="px-4 py-3">Nama Kegiatan</th>
            <th class="px-4 py-3">Jenis</th>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Tempat</th>
            <th class="px-4 py-3">Jumlah Sertifikat</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($kegiatan as $item)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold text-gray-900">{{ $item->nama_kegiatan }}</td>
                <td class="px-4 py-3">{{ $item->jenis }}</td>
                <td class="px-4 py-3">{{ $item->tanggal }}</td>
                <td class="px-4 py-3">{{ $item->tempat }}</td>
                <td class="px-4 py-3">{{ $item->sertifikat_count }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs {{ $item->status == 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('sertifikat.show', $item->id) }}" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition whitespace-nowrap">
                        Kelola Sertifikat
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                    Tidak ada kegiatan yang ditemukan.
                </td>
            </tr>
            @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
      {{ $kegiatan->links() }}
    </div>

  </div>
</section>
@endsection