@extends('layouts.app')

@section('content')
      <!-- Header -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Daftar Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola data pegawai untuk akses dokumen dan arsip privasi.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="pegawai-tambah.html" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
        <button id="btnExport" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Export CSV</button>
      </div>
    </div>
  </section>

  <!-- Filters -->
  <section class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-7 gap-3" onsubmit="event.preventDefault(); page=1; render();">
        <div class="lg:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Cari</label>
          <input id="f_q" type="search" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama / Username / NIP / Unit">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Unit</label>
          <select id="f_unit" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>Sekretariat A</option>
            <option>Bidang Riset</option>
            <option>TI</option>
            <option>Keuangan</option>
            <option>Humas</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Role</label>
          <select id="f_role" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>admin</option>
            <option>verifikator</option>
            <option>pegawai</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select id="f_status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Urutkan</label>
          <select id="sort" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="latest">Terbaru</option>
            <option value="name">Nama (A-Z)</option>
            <option value="unit">Unit (A-Z)</option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Terapkan</button>
          <button type="reset" class="px-4 py-2 rounded-lg  border border-gray-300 hover:bg-gray-50" onclick="resetFilters()">Reset</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span id="countInfo">Menampilkan 0 pegawai</span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Tampilkan</label>
          <select id="pageSize" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option>10</option>
            <option selected>25</option>
            <option>50</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white">
            <tr class="text-left border-b">
              <th class="px-4 py-3">Pegawai</th>
              <th class="px-4 py-3">NIP</th>
              <th class="px-4 py-3">Unit</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3">Kontak</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody" class="divide-y">
            <!-- rows via JS -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 flex items-center justify-between">
        <p id="pageInfo" class="text-sm text-gray-600">Halaman 1</p>
        <div class="inline-flex overflow-hidden rounded-md border border-gray-200">
          <button id="prevBtn" class="px-3 py-2 text-sm hover:bg-gray-50">Sebelumnya</button>
          <button id="nextBtn" class="px-3 py-2 text-sm hover:bg-gray-50">Berikutnya</button>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div id="empty" class="mt-6 hidden">
      <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
        <p class="text-sm text-gray-700">Belum ada data pegawai.</p>
        <a href="pegawai-profile.html" class="inline-flex mt-3 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
      </div>
    </div>
  </section>
@endsection