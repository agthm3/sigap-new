@extends('layouts.app')

@section('content')
<style>
[x-cloak] { display:none !important; }
</style>

{{-- Wrapper utama dengan state Alpine.js --}}
<div x-data="{ openModal: false }">

  {{-- Header --}}
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

        <!-- Tombol untuk Membuka Modal -->
        <button
          @click="openModal = true"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 5v14M5 12h14"/>
          </svg>
          Tambah Kegiatan
        </button>
      </div>
    </div>
  </section>

  {{-- Content Table --}}
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

  <!-- ================= MODAL TAMBAH KEGIATAN ================= -->
  <div
    x-show="openModal"
    x-cloak
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center p-4">

    <!-- Backdrop Overlay -->
    <div class="absolute inset-0 bg-black/40" @click="openModal = false"></div>

    <!-- Modal Content -->
    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden z-10">
      
      <!-- Modal Header -->
      <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 flex justify-between items-center">
        <div>
          <h2 class="text-white text-lg font-bold">Tambah Kegiatan Baru</h2>
          <p class="text-white/80 text-xs">Buat event/kegiatan penerbitan sertifikat baru.</p>
        </div>
        <button @click="openModal = false" class="text-white/80 hover:text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <!-- Modal Body Form -->
      <form method="POST" action="{{ route('sertifikat-kegiatan.store') }}" class="p-5 space-y-4">
        @csrf

        <div>
          <label class="block text-sm font-semibold text-gray-700">Nama Kegiatan</label>
          <input type="text" name="nama_kegiatan" required placeholder="Contoh: Bimbingan Teknis Inovasi..."
            class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700">Jenis Kegiatan</label>
            <input type="text" name="jenis" required placeholder="Contoh: Workshop / Webinar"
              class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Tanggal</label>
            <input type="date" name="tanggal" required
              class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700">Tempat</label>
            <input type="text" name="tempat" required placeholder="Contoh: Aula BRIDA / Zoom"
              class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Status</label>
            <select name="status" required class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
              <option value="Aktif">Aktif</option>
              <option value="Non-Aktif">Non-Aktif</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700">Keterangan (Opsional)</label>
          <textarea name="keterangan" rows="3" placeholder="Tambahkan catatan atau deskripsi ringkas..."
            class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon"></textarea>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
          <button type="button" @click="openModal = false" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50">
            Batal
          </button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-medium hover:bg-maroon-800 transition">
            Simpan Kegiatan
          </button>
        </div>

      </form>

    </div>
  </div>

</div>
@endsection