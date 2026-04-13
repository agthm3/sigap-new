@extends('layouts.app')

@section('content')
<div x-data="{openModal:false}">
    <!-- ================= HEADER ================= -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col gap-2">
    <h1 class="text-2xl font-extrabold text-gray-900">
      Kelola Sertifikat
    </h1>
    <p class="text-sm text-gray-600">
      Kegiatan: <strong>{{ $kegiatan->nama_kegiatan }}</strong>
    </p>
  </div>
</section>

<!-- ================= ACTION BAR ================= -->
<section class="max-w-7xl mx-auto px-4">
  <div class="bg-white border rounded-xl p-4 flex flex-wrap gap-3 items-center justify-between">

    <div class="flex flex-wrap gap-2">
    <a href="{{ route('sertifikat.template') }}"
    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border hover:bg-gray-50">
    ⬇️ Download Template Excel
    </a>

        <form method="POST"
            action="{{ route('sertifikat.import') }}"
            enctype="multipart/form-data"
            class="inline">

        @csrf

        <input type="hidden"
            name="kegiatan_id"
            value="{{ $kegiatan->id }}">

        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border hover:bg-gray-50 cursor-pointer">

        ⬆️ Upload Excel

        <input type="file"
            name="file"
            accept=".xls,.xlsx"
            class="hidden"
            onchange="this.form.submit()">

        </label>

        </form>
    </div>

    <button
      @click="openModal=true"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">
      ➕ Tambah Sertifikat Manual
    </button>

  </div>
</section>

<!-- ================= TABLE ================= -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

    <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700">
      Daftar Sertifikat
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b">
          <tr>
            <th class="px-4 py-3 text-left">Nomor Sertifikat</th>
            <th class="px-4 py-3 text-left">Nama Penerima</th>
            <th class="px-4 py-3 text-left">Instansi</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Aksi</th>
          </tr>
        </thead>

            <tbody class="divide-y">

            @forelse($sertifikat as $item)

            <tr>

            <td class="px-4 py-3 font-medium text-maroon">
            {{ $item->nomor_sertifikat }}
            </td>

            <td class="px-4 py-3">
            {{ $item->nama_penerima }}
            </td>

            <td class="px-4 py-3">
            {{ $item->instansi }}
            </td>

            <td class="px-4 py-3">

            <span class="px-2 py-1 rounded text-xs
            {{ $item->status == 'Aktif'
            ? 'bg-emerald-50 text-emerald-700'
            : 'bg-gray-100 text-gray-600' }}">

            {{ $item->status }}

            </span>

            </td>

            <td class="px-4 py-3">

            <div class="flex gap-2">

            <a href="#"
            class="px-3 py-1.5 rounded-md border hover:bg-gray-50">
            View
            </a>

            </div>

            </td>

            </tr>

            @empty

            <tr>
            <td colspan="5"
            class="px-4 py-6 text-center text-gray-500">
            Belum ada sertifikat
            </td>
            </tr>

            @endforelse

            </tbody>
      </table>
    </div>

    <div class="px-4 py-3 text-sm text-gray-500">
      Total: {{ $sertifikat->count() }} sertifikat
    </div>

  </div>
</section>

<!-- ================= MODAL TAMBAH MANUAL ================= -->
<div
  x-show="openModal"
  x-transition
  class="fixed inset-0 z-50 flex items-center justify-center">

  <div class="absolute inset-0 bg-black/40" @click="openModal=false"></div>

  <div class="relative bg-white w-full max-w-lg p-3 rounded-2xl shadow-2xl overflow-hidden">
    <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
      <h2 class="text-white text-lg font-bold">Tambah Sertifikat Manual</h2>
      <p class="text-white/80 text-xs">
        Digunakan jika hanya menambahkan satu atau beberapa sertifikat.
      </p>
    </div>

    <form method="POST" action="{{ route('sertifikat.store') }}">
        @csrf

        <input type="hidden" name="kegiatan_id"
        value="{{ $kegiatan->id }}">

      <div>
        <label class="text-sm font-semibold text-gray-700">Nomor Sertifikat</label>
        <input name="nomor_sertifikat" type="text" placeholder="MA-2025-XXX"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Nama Penerima</label>
        <input name="nama_penerima" type="text" placeholder="Nama lengkap"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Instansi</label>
        <input name="instansi" type="text" placeholder="Asal instansi / sekolah / OPD"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Keterangan (Opsional)</label>
        <textarea name="keterangan" rows="3"
          class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon"
          placeholder="Contoh: Juara 1 kategori inovasi..."></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <button type="button"
          @click="openModal=false"
          class="px-4 py-2 rounded-lg border hover:bg-gray-50">
          Batal
        </button>
        <button
          class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">
          Simpan Sertifikat
        </button>
      </div>

    </form>
  </div>
</div>
@endsection