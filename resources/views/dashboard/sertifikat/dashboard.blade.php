@extends('layouts.app')

@section('content')
<style>
[x-cloak] { display:none !important; }
</style>

{{-- Wrapper utama dengan state Alpine.js --}}
<div x-data="{ 
  openModal: false,
  deleteModal: false,
  deleteId: null,
  deleteNama: '',
  deleteCount: 0,
  typedConfirmation: '',
  
  openDeleteModal(id, nama, count) {
    this.deleteId = id;
    this.deleteNama = nama;
    this.deleteCount = count;
    this.typedConfirmation = '';
    this.deleteModal = true;
  },
  closeDeleteModal() {
    this.deleteModal = false;
    this.deleteId = null;
    this.deleteNama = '';
    this.deleteCount = 0;
    this.typedConfirmation = '';
  }
}">

  {{-- Alert Success --}}
  @if(session('success'))
  <div class="max-w-7xl mx-auto px-4 pt-4">
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center justify-between">
      <span>{{ session('success') }}</span>
    </div>
  </div>
  @endif

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
              <th class="px-4 py-3 text-right">Aksi</th>
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
                  <td class="px-4 py-3 text-right">
                      <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('sertifikat.show', $item->id) }}" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition whitespace-nowrap">
                            Kelola
                        </a>
                        
                        <!-- Tombol Hapus Trigger Modal -->
                        <button 
                          type="button"
                          @click="openDeleteModal({{ $item->id }}, '{{ addslashes($item->nama_kegiatan) }}', {{ $item->sertifikat_count }})"
                          class="px-3 py-1.5 rounded-md bg-red-50 border border-red-200 text-red-600 hover:bg-red-600 hover:text-white transition">
                            Hapus
                        </button>
                      </div>
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

  <!-- ================= MODAL TAMBAH KEGIATAN (DIPERBAIKI) ================= -->
  <div
    x-show="openModal"
    x-cloak
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center p-4">

    <div class="absolute inset-0 bg-black/40" @click="openModal = false"></div>

    <div class="relative bg-white w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden z-10">
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

      <!-- Form Tambah Kegiatan yang Benar -->
      <form action="{{ route('sertifikat-kegiatan.store') }}" method="POST" class="p-5 space-y-4">
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

  <!-- ================= MODAL KONFIRMASI HAPUS BERLAPIS ================= -->
  <div
    x-show="deleteModal"
    x-cloak
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center p-4">

    <!-- Backdrop Overlay -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeDeleteModal()"></div>

    <!-- Modal Box -->
    <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl overflow-hidden z-10 border border-red-100">
      
      <!-- Modal Header -->
      <div class="p-5 bg-red-50 border-b border-red-100 flex items-start gap-3">
        <div class="p-2 bg-red-100 text-red-600 rounded-full shrink-0">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-bold text-red-900">Konfirmasi Penghapusan</h3>
          <p class="text-xs text-red-600 mt-0.5">Tindakan ini permanen dan tidak dapat dibatalkan!</p>
        </div>
      </div>

      <!-- Modal Body (Form Delete yang Benar) -->
      <form :action="'{{ url('sertifikat-kegiatan') }}/' + deleteId" method="POST" class="p-5 space-y-4">
        @csrf
        @method('DELETE')

        <div class="text-sm text-gray-700 space-y-2">
          <p>Anda akan menghapus kegiatan:</p>
          <div class="p-3 bg-gray-50 border rounded-lg font-semibold text-gray-900 break-words" x-text="deleteNama"></div>
          
          <div x-show="deleteCount > 0" class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs leading-relaxed">
            ⚠️ <strong>Peringatan Tambahan:</strong> Terdapat <span class="font-bold underline" x-text="deleteCount"></span> data peserta/sertifikat yang terhubung ke kegiatan ini. Semua data tersebut juga akan ikut <strong>terhapus secara permanen</strong>.
          </div>
        </div>

        <!-- Konfirmasi Teks -->
        <div class="pt-2 border-t border-gray-100">
          <label class="block text-xs font-medium text-gray-700 mb-1">
            Untuk mengonfirmasi, ketik nama kegiatan secara persis di bawah ini:
          </label>
          <input 
            type="text" 
            x-model="typedConfirmation"
            placeholder="Ketik nama kegiatan persis sama..."
            class="w-full text-sm p-2.5 rounded-lg border border-gray-300 focus:border-red-500 focus:ring-red-500"
          >
        </div>

        <!-- Footer Buttons -->
        <div class="flex items-center justify-end gap-2 pt-3">
          <button 
            type="button" 
            @click="closeDeleteModal()" 
            class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">
            Batal
          </button>
          
          <button 
            type="submit" 
            :disabled="typedConfirmation !== deleteNama"
            :class="typedConfirmation === deleteNama ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-red-300 cursor-not-allowed'"
            class="px-4 py-2 rounded-lg text-white text-sm font-medium transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hapus Permanen
          </button>
        </div>

      </form>

    </div>
  </div>

</div>
@endsection