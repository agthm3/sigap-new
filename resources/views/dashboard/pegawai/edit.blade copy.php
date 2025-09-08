@extends('layouts.app')

@section('content')
    
  <!-- Header Profile -->
  <section class="max-w-7xl mx-auto px-4">
    <div id="notfound" class="hidden rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
      Pegawai tidak ditemukan. <a href="sigap-pegawai.html" class="underline">Kembali ke daftar</a>.
    </div>

    <div id="profileHead" class="hidden bg-white border border-gray-200 rounded-2xl p-5">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
          <div id="avatar" class="w-16 h-16 rounded-full bg-maroon/10 text-maroon flex items-center justify-center font-extrabold text-lg">?</div>
          <div>
            <h1 id="name" class="text-xl font-extrabold text-gray-900">Nama</h1>
            <p class="text-sm text-gray-600"><span id="username">@username</span> • <span id="unit">Unit</span> • <span id="role">Role</span></p>
            <p id="status" class="text-xs mt-1"></p>
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <a id="btnEdit" href="#" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50">Edit</a>
          <a id="btnAddDoc" href="#" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Dokumen</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Tabs -->
  <section id="tabsWrap" class="max-w-7xl mx-auto px-4 mt-6 hidden">
    <div class="bg-white border border-gray-200 rounded-2xl">
      <div class="px-4 py-3 border-b flex items-center gap-2">
        <button data-tab="ringkasan" class="tab px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Ringkasan</button>
        <button data-tab="dokumen" class="tab px-3 py-1.5 rounded-md hover:bg-gray-100 text-sm">Dokumen Privasi</button>
        <button data-tab="riwayat" class="tab px-3 py-1.5 rounded-md hover:bg-gray-100 text-sm">Riwayat Akses</button>
      </div>

      <!-- Ringkasan -->
      <div id="tab-ringkasan" class="p-5 grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
          <h3 class="text-sm font-semibold text-gray-700">Data Diri</h3>
          <div class="mt-3 grid sm:grid-cols-2 gap-4 text-sm">
            <div class="border rounded-lg p-3">
              <div class="text-gray-500">NIP</div>
              <div id="nip" class="font-medium text-gray-900">—</div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-gray-500">Email</div>
              <div id="email" class="font-medium text-gray-900">—</div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-gray-500">Telepon</div>
              <div id="phone" class="font-medium text-gray-900">—</div>
            </div>
            <div class="border rounded-lg p-3">
              <div class="text-gray-500">Status</div>
              <div id="status2" class="font-medium text-gray-900">—</div>
            </div>
          </div>

          <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-700">Catatan</h3>
            <p class="mt-2 text-sm text-gray-700">Dokumen privasi (KK/KTP, dsb.) memerlukan kode akses yang valid. Semua upaya akses dicatat.</p>
          </div>
        </div>
        <div>
          <h3 class="text-sm font-semibold text-gray-700">Tindakan Cepat</h3>
          <div class="mt-3 grid gap-2">
            <a href="permintaan-akses.html" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50">Buka Permintaan Akses</a>
            <a href="kode-akses.html" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50">Kelola Kode Akses</a>
            <a href="log-aktivitas.html" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50">Lihat Log Aktivitas</a>
          </div>
        </div>
      </div>

      <!-- Dokumen Privasi -->
      <div id="tab-dokumen" class="p-5 hidden">
        <div class="mb-4 text-sm text-gray-600">
          Masukkan <span class="font-semibold">Kode Akses</span> saat menekan Lihat/Unduh. Kode dapat bersifat <span class="italic">Universal</span> atau <span class="italic">Per-User</span>.
        </div>

        <div id="docList" class="grid md:grid-cols-2 gap-4">
          <!-- kartu dokumen via JS -->
        </div>
      </div>

      <!-- Riwayat Akses -->
      <div id="tab-riwayat" class="p-5 hidden">
        <div class="overflow-x-auto border rounded-lg">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
              <tr class="text-left">
                <th class="px-4 py-2">Waktu</th>
                <th class="px-4 py-2">Aksi</th>
                <th class="px-4 py-2">Dokumen</th>
                <th class="px-4 py-2">Kode</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Catatan</th>
              </tr>
            </thead>
            <tbody id="histBody" class="divide-y">
              <!-- rows via JS -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>

  <!-- Preview Modal (demo) -->
  <div id="previewModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closePreview()"></div>
    <div class="relative z-10 mx-auto max-w-4xl px-4 py-8">
      <div class="bg-white rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-5 py-3 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 text-white">
          <h3 id="pvTitle" class="text-lg font-bold">Pratinjau Dokumen</h3>
        </div>
        <div class="p-5">
          <div class="rounded border bg-gray-50 aspect-[4/3] flex items-center justify-center text-gray-500">
            (Demo) Pratinjau PDF akan ditampilkan di sini.
          </div>
        </div>
        <div class="px-5 py-3 bg-gray-50 flex justify-end gap-2">
          <button class="px-3 py-2 rounded-lg border hover:bg-gray-100" onclick="closePreview()">Tutup</button>
        </div>
      </div>
    </div>
  </div>
@endsection