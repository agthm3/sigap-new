@extends('layouts.app')


@section('content')
<style>
[x-cloak] { display:none !important; }
</style>
<div x-data="{ openModal:false }">

<!-- ================= PAGE HEADER ================= -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Sertifikat</h1>
      <p class="text-sm text-gray-600 mt-1">
        Daftar kegiatan / event penerbitan sertifikat resmi BRIDA.
      </p>
    </div>

    <div>
      <button
        @click="openModal=true"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">

        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 5v14M5 12h14"/>
        </svg>

        Tambah Kegiatan / Event
      </button>
    </div>
  </div>
</section>

<!-- ================= TABLE KEGIATAN ================= -->
<section class="max-w-7xl mx-auto px-4 pb-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

    <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700">
      Daftar Kegiatan Sertifikat
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b bg-white">
          <tr class="text-left">
            <th class="px-4 py-3">Nama Kegiatan</th>
            <th class="px-4 py-3">Jenis</th>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Jumlah Sertifikat</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>

            <tbody class="divide-y">

            @forelse($kegiatan as $item)
            <tr class="hover:bg-gray-50">

            <td class="px-4 py-3 font-semibold text-gray-900">
            {{ $item->nama_kegiatan }}
            </td>

            <td class="px-4 py-3">
            {{ $item->jenis }}
            </td>

            <td class="px-4 py-3">
            {{ $item->tanggal }}
            </td>

            <td class="px-4 py-3">
            0
            </td>

            <td class="px-4 py-3">
            <span class="px-2 py-1 rounded text-xs
            {{ $item->status == 'Aktif' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
            {{ $item->status }}
            </span>
            </td>

            <td class="px-4 py-3">

            <a href="{{ route('sertifikat.show',$item->id) }}"
            class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">
            Kelola Sertifikat
            </a>

            </td>

            </tr>

            @empty

            <tr>
            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
            Belum ada kegiatan
            </td>
            </tr>

            @endforelse

            </tbody>
      </table>
    </div>

    <div class="px-4 py-3 text-sm text-gray-500">
      Menampilkan 1–2 dari 2 kegiatan
    </div>

  </div>
</section>

<!-- ================= MODAL TAMBAH KEGIATAN ================= -->
<div
  x-show="openModal"
  x-cloak
  x-transition
  class="fixed inset-0 z-50 flex items-center justify-center">

  <!-- Overlay -->
  <div class="absolute inset-0 bg-black/40" @click="openModal=false"></div>

  <!-- Modal Box -->
  <div class="relative bg-white w-full max-w-xl rounded-2xl shadow-2xl overflow-hidden">
    <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
      <h2 class="text-white text-lg font-bold">Tambah Kegiatan / Event</h2>
      <p class="text-white/80 text-xs mt-0.5">
        Kegiatan ini akan menjadi induk dari sertifikat-sertifikat yang diterbitkan.
      </p>
    </div>

    <form method="POST" action="{{ route('sertifikat-kegiatan.store') }}" class="p-5 space-y-4">
    @csrf

      <div>
        <label class="text-sm font-semibold text-gray-700">Nama Kegiatan</label>
        <input type="text" name="nama_kegiatan" placeholder="Contoh: Makassar Award 2025"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">Jenis Kegiatan</label>
          <select name="jenis" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
            <option value="">Pilih…</option>
            <option>Penghargaan</option>
            <option>Pelatihan</option>
            <option>Magang</option>
            <option>Workshop</option>
            <option>Lainnya</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select name="status" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
            <option value="">Pilih…</option>
            <option>Aktif</option>
            <option>Nonaktif</option>
          </select>
        </div>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Tanggal / Periode</label>
        <input type="text" name="tanggal" placeholder="Contoh: 12 Jan – 6 Feb 2026"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Keterangan (Opsional)</label>
        <textarea name="keterangan"  rows="3"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon"
          placeholder="Deskripsi singkat kegiatan..."></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button"
          @click="openModal=false"
          class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
          Batal
        </button>
        <button
          class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">
          Simpan Kegiatan
        </button>
      </div>

    </form>
  </div>
</div>
@endsection