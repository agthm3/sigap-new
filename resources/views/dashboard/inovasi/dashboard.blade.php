@extends('layouts.app')

@section('content')
        <!-- Heading & Quick Actions -->
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Dashboard <span class="text-maroon">SIGAP Inovasi</span></h1>
            <p class="text-sm text-gray-600 mt-0.5">Ringkasan portofolio inovasi daerah, progres tahapan, dan aktivitas terbaru.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <a href="inovasi-daerah.html" class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm transition">Tambah Inovasi</a>
            <a href="#" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm transition">Impor (Excel)</a>
            <a href="inovasi-konfigurasi.html" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Konfigurasi</a>
          </div>
        </section>

        <!-- KPI Cards -->
        <section class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Total Inovasi</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon" id="kpiTotal">124</p>
            <p class="text-xs text-emerald-600 mt-1">+8 inovasi bulan ini</p>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">OPD Terlibat</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon" id="kpiOpd">32</p>
            <p class="text-xs text-gray-500 mt-1">5 OPD aktif minggu ini</p>
          </div>
        </section>

        <!-- Charts -->
        <!-- <section class="grid lg:grid-cols-3 gap-4">
          <div class="lg:col-span-2 rounded-xl border bg-white p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold text-gray-800">Inovasi Baru per Bulan</h3>
              <select id="range" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
                <option>12 bulan</option>
                <option>6 bulan</option>
                <option>3 bulan</option>
              </select>
            </div>
            <canvas id="chartMonthly" height="110"></canvas>
          </div>

          <div class="rounded-xl border bg-white p-4">
            <h3 class="font-semibold text-gray-800 mb-3">Distribusi Tahapan</h3>
            <canvas id="chartStages" height="110"></canvas>
            <div class="mt-3 text-xs text-gray-600">
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Inisiatif</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Uji Coba</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Penerapan</div>
            </div>
          </div>
        </section> -->

        <!-- Urusan & Leaderboard -->
        <section class="grid xl:grid-cols-3 gap-4">
          <!-- Leaderboard OPD -->
          <div class="rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Leaderboard OPD (Jumlah Inovasi)</div>
            <ul class="divide-y text-sm">
              <li class="p-4 flex items-center justify-between">
                <span>BRIDA</span><span class="font-semibold text-maroon">14</span>
              </li>
              <li class="p-4 flex items-center justify-between">
                <span>Dinas Kesehatan</span><span class="font-semibold text-maroon">12</span>
              </li>
              <li class="p-4 flex items-center justify-between">
                <span>Dinas Pendidikan</span><span class="font-semibold text-maroon">11</span>
              </li>
              <li class="p-4 flex items-center justify-between">
                <span>Dishub</span><span class="font-semibold text-maroon">9</span>
              </li>
              <li class="p-4 flex items-center justify-between">
                <span>DPKM</span><span class="font-semibold text-maroon">7</span>
              </li>
            </ul>
          </div>
        </section>

        <!-- Activity & Pending -->
        <section class="grid xl:grid-cols-3 gap-4">
          <!-- Aktivitas terbaru -->
          <div class="xl:col-span-2 rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700 flex items-center justify-between">
              <span>Pengajuan Terbaru</span>
              <a href="inovasi-daerah.html" class="text-maroon hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="text-left border-b">
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">Inovasi</th>
                    <th class="px-4 py-2">OPD</th>
                    <th class="px-4 py-2">Tahapan</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <tr>
                    <td class="px-4 py-2 text-gray-600">04 Sep 2025 • 10:12</td>
                    <td class="px-4 py-2">SIM Air Bersih Terintegrasi</td>
                    <td class="px-4 py-2">BRIDA</td>
                    <td class="px-4 py-2">Uji Coba</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">Menunggu Verifikasi</span></td>
                    <td class="px-4 py-2">
                      <div class="flex gap-2">
                        <a href="inovasi-detail.html" class="px-2.5 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white">Review</a>
                        <button class="px-2.5 py-1.5 rounded-md border hover:bg-gray-50">Tolak</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">03 Sep 2025 • 16:40</td>
                    <td class="px-4 py-2">Clinic Mobile EduHealth</td>
                    <td class="px-4 py-2">Dinkes</td>
                    <td class="px-4 py-2">Penerapan</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Disetujui</span></td>
                    <td class="px-4 py-2">
                      <div class="flex gap-2">
                        <a href="inovasi-detail.html" class="px-2.5 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white">Lihat</a>
                        <button class="px-2.5 py-1.5 rounded-md border hover:bg-gray-50">Log</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">02 Sep 2025 • 09:05</td>
                    <td class="px-4 py-2">SIM Transport Cerdas</td>
                    <td class="px-4 py-2">Dishub</td>
                    <td class="px-4 py-2">Inisiatif</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-rose-50 text-rose-700">Dikembalikan</span></td>
                    <td class="px-4 py-2">
                      <div class="flex gap-2">
                        <a href="inovasi-detail.html" class="px-2.5 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white">Perbaiki</a>
                        <button class="px-2.5 py-1.5 rounded-md border hover:bg-gray-50">Catatan</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Butuh tindak lanjut -->
          <div class="rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Butuh Tindak Lanjut</div>
            <ul class="divide-y text-sm">
              <li class="p-4">
                <div class="flex items-start gap-3">
                  <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                  <div class="flex-1">
                    <p class="font-semibold">Verifikasi Evidence – SIM Air Bersih</p>
                    <p class="text-gray-600 text-xs mt-0.5">Deadline: 06 Sep 2025 • PIC: admin.inovasi</p>
                    <div class="mt-2 flex gap-2">
                      <button class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Verifikasi</button>
                      <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Lihat Evidence</button>
                    </div>
                  </div>
                </div>
              </li>
              <li class="p-4">
                <div class="flex items-start gap-3">
                  <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                  <div class="flex-1">
                    <p class="font-semibold">Lengkapi indikator dampak – EduHealth</p>
                    <p class="text-gray-600 text-xs mt-0.5">Deadline: 07 Sep 2025 • PIC: dinkes.opd</p>
                    <div class="mt-2 flex gap-2">
                      <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Kirim Pengingat</button>
                      <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Catatan</button>
                    </div>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>
@endsection