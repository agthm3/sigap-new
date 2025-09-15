@extends('layouts.app')

@section('content')

  <!-- Topbar -->
   

      <!-- Content -->
      <main class="p-4 lg:p-6 space-y-6">

        <!-- Greeting + Quick Actions -->
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Selamat datang, <span class="text-maroon">{{ Auth::user()->name }}</span> 👋</h1>
            <p class="text-sm text-gray-600 mt-0.5">Ringkasan aktivitas sistem SIGAP BRIDA hari ini.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <a href="pegawai-detail.html" class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm transition">Tambah Pegawai</a>
            <a href="hasil.html" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm transition">Unggah Dokumen</a>
            <a href="#" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Buat Kategori</a>
          </div>
        </section>

        <!-- KPI Cards -->
        <section class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Total Dokumen</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">8.214</p>
            <p class="text-xs text-emerald-600 mt-1">+3.2% minggu ini</p>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Dokumen Privasi</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">1.982</p>
            <p class="text-xs text-amber-600 mt-1">Akses Terkendali</p>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Permintaan Akses Pending</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">5</p>
            <div class="mt-1 flex -space-x-2">
              <span class="w-6 h-6 rounded-full bg-amber-100 border border-white"></span>
              <span class="w-6 h-6 rounded-full bg-amber-100 border border-white"></span>
              <span class="w-6 h-6 rounded-full bg-amber-100 border border-white"></span>
            </div>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Unduhan 7 hari</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">642</p>
            <p class="text-xs text-gray-500 mt-1">+124 unduhan</p>
          </div>
        </section>

        <!-- Charts -->
        {{-- <section class="grid lg:grid-cols-3 gap-4">
          <div class="lg:col-span-2 rounded-xl border bg-white p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold text-gray-800">Aktivitas Unduh & Lihat (30 hari)</h3>
              <select id="range" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
                <option>30 hari</option>
                <option>7 hari</option>
                <option>24 jam</option>
              </select>
            </div>
            <canvas id="lineChart" height="110"></canvas>
          </div>

          <div class="rounded-xl border bg-white p-4">
            <h3 class="font-semibold text-gray-800 mb-3">Komposisi Dokumen</h3>
            <canvas id="pieChart" height="110"></canvas>
            <div class="mt-3 text-xs text-gray-600">
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Laporan</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> SK</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Formulir</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Privasi</div>
            </div>
          </div>
        </section> --}}

        <!-- Two columns: Activity & Requests -->
        <section class="grid xl:grid-cols-3 gap-4">
          <!-- Activity -->
          <div class="xl:col-span-2 rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700 flex items-center justify-between">
              <span>Aktivitas Terbaru</span>
              <a href="#" class="text-maroon hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="text-left border-b">
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">Pengguna</th>
                    <th class="px-4 py-2">Aksi</th>
                    <th class="px-4 py-2">Objek</th>
                    <th class="px-4 py-2">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <tr>
                    <td class="px-4 py-2 text-gray-600">10 Aug 2025 • 14:02</td>
                    <td class="px-4 py-2">admin.sekretariat</td>
                    <td class="px-4 py-2">Unduh</td>
                    <td class="px-4 py-2">SK Sekretariat A</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Sukses</span></td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">10 Aug 2025 • 13:47</td>
                    <td class="px-4 py-2">user.riset1</td>
                    <td class="px-4 py-2">View</td>
                    <td class="px-4 py-2">KTP Pegawai (Terkendali)</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">Menunggu Kode</span></td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">09 Aug 2025 • 09:21</td>
                    <td class="px-4 py-2">admin.sekretariat</td>
                    <td class="px-4 py-2">Unggah</td>
                    <td class="px-4 py-2">Laporan Kinerja 2024</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Sukses</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Requests -->
          <div class="rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Permintaan Akses</div>
            <ul class="divide-y text-sm">
              <li class="p-4 flex items-start gap-3">
                <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                <div class="flex-1">
                  <p class="font-semibold">user.riset1 meminta akses</p>
                  <p class="text-gray-600 text-xs mt-0.5">KTP Andi Rahman • Alasan: Verifikasi identitas internal</p>
                  <div class="mt-2 flex gap-2">
                    <button class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Setujui</button>
                    <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Tolak</button>
                  </div>
                </div>
              </li>
              <li class="p-4 flex items-start gap-3">
                <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                <div class="flex-1">
                  <p class="font-semibold">user.persuratan meminta akses</p>
                  <p class="text-gray-600 text-xs mt-0.5">KK Sitti Aulia • Alasan: Administrasi tunjangan</p>
                  <div class="mt-2 flex gap-2">
                    <button class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Setujui</button>
                    <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Tolak</button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>

      </main>

      <!-- Footer -->
      <footer class="px-4 lg:px-6 py-4 text-sm text-gray-600 border-t bg-white">
        © 2025 SIGAP BRIDA • BRIDA Kota Makassar
      </footer>

@endsection