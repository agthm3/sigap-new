@extends('layouts.app')

@section('content')
      <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Inovasi</h1>
        <p class="text-sm text-gray-600 mt-1">Direktori inovasi daerah: cari, pantau tahapan, dan kelola unggahan tiap OPD.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <button id="btnTambah" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 5v14M5 12h14"/></svg>
          Tambah Inovasi
        </button>
      </div>
    </div>
  </section>

  <!-- Filter & Search -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-6 gap-3" onsubmit="event.preventDefault();">
        <!-- Kolom 1-3: dropdown dummy -->
        <div>
          <label class="text-sm font-semibold text-gray-700">Bentuk Inovasi</label>
          <select id="f_bentuk" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>Inovasi Tata Kelola</option>
            <option>Inovasi Pelayanan Publik</option>
            <option>Inovasi Daerah Lainnya</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Jenis Urusan</label>
          <select id="f_urusan" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>Kesehatan</option>
            <option>Pendidikan</option>
            <option>Air Bersih</option>
            <option>Transportasi</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Inisiator</label>
          <select id="f_inisiator" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>OPD</option>
            <option>Unit Kerja</option>
            <option>Kolaborasi</option>
          </select>
        </div>

        <!-- Kolom 4-6: Tahapan Inovasi (3 dropdown) -->
        <div>
          <label class="text-sm font-semibold text-gray-700">Tahap Inovasi</label>
          <select id="f_t_terap" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>Uji Coba</option>
            <option>Penerapan</option>
            <option>Inisiatif</option>
          </select>
        </div>

        <!-- Search + Actions full width -->
        <div class="lg:col-span-6 grid md:grid-cols-3 gap-3 pt-1">
          <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-700">Pencarian</label>
            <div class="relative mt-1.5">
              <input id="q" type="search" class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pl-9" placeholder="Judul / OPD / Kata kunci…">
              <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
          </div>
          <div class="flex items-end gap-3">
            <button id="btnCari" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition w-full md:w-auto">Cari</button>
            <button type="reset" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 w-full md:w-auto" onclick="resetFilter()">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span id="countInfo">Menampilkan 3 inovasi (contoh)</span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Urutkan</label>
          <select id="sort" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="terbaru">Terbaru</option>
            <option value="judul">Judul (A-Z)</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3">Inovasi</th>
              <th class="px-4 py-3">Bentuk</th>
              <th class="px-4 py-3">Jenis Urusan</th>
              <th class="px-4 py-3">Inisiator</th>
              <th class="px-4 py-3">Tahap</th>
              <th class="px-4 py-3">OPD/Unit</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody" class="divide-y">
            <!-- Row 1 -->
            <tr>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded bg-maroon/10 flex items-center justify-center text-maroon">
                    <!-- ikon pemerintahan -->
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">SIM Air Bersih Terintegrasi</p>
                    <p class="text-xs text-gray-600 line-clamp-1">Dashboard pemantauan kebocoran & kualitas air PDAM.</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">Inovasi Tata Kelola</td>
              <td class="px-4 py-3">Air Bersih</td>
              <td class="px-4 py-3">OPD</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                  <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Inisiatif: Selesai</span>
                  <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">Uji Coba: Berjalan</span>
                  <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">Penerapan: Belum</span>
                </div>
              </td>
              <td class="px-4 py-3">Bidang Riset</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <a href="inovasi-detail.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
           
                 <div class="relative group inline-block">
                  <a href="evidence.html">   <button
                    class="px-3 py-1.5 rounded-md border hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-maroon/30"
                    aria-describedby="tt-evidence-1"
                    aria-label="Bukti Evidence"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" d="M3 7a2 2 0 0 1 2-2h4l2 3h8a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>
                    </svg>
                </button></a>

                <!-- Tooltip -->
                <div
                    id="tt-evidence-1"
                    role="tooltip"
                    class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                        whitespace-nowrap rounded-md bg-gray-900 text-white text-xs px-2.5 py-1
                        opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0
                        group-focus-within:opacity-100 group-focus-within:translate-y-0
                        transition duration-150 z-20"
                >
                    Bukti Evidence
                    <!-- arrow -->
                    <span class="absolute left-1/2 top-full -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></span>
                </div>
                </div>
       <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</button>
                  <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Hapus</button>
                </div>
              </td>
            </tr>
            <!-- Row 2 -->
            <tr>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded bg-maroon/10 flex items-center justify-center text-maroon">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">Clinic Mobile EduHealth</p>
                    <p class="text-xs text-gray-600 line-clamp-1">Layanan keliling edukasi gizi dan vaksinasi.</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">Pelayanan Publik</td>
              <td class="px-4 py-3">Kesehatan</td>
              <td class="px-4 py-3">Kolaborasi</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                  <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Inisiatif: Selesai</span>
                  <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Uji Coba: Selesai</span>
                  <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">Penerapan: Berjalan</span>
                </div>
              </td>
              <td class="px-4 py-3">Dinkes</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <a href="inovasi-detail.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
                  <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</button>
                  <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Hapus</button>
                </div>
              </td>
            </tr>
            <!-- Row 3 -->
            <tr>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 rounded bg-maroon/10 flex items-center justify-center text-maroon">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
                    </svg>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">SIM Transport Cerdas</p>
                    <p class="text-xs text-gray-600 line-clamp-1">Optimasi trayek angkutan berbasis data mobilitas.</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">Inovasi Tata Kelola</td>
              <td class="px-4 py-3">Transportasi</td>
              <td class="px-4 py-3">OPD</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                  <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">Inisiatif: Berjalan</span>
                  <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">Uji Coba: Belum</span>
                  <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700">Penerapan: Belum</span>
                </div>
              </td>
              <td class="px-4 py-3">Dishub</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <a href="inovasi-detail.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
                  <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</button>
                  <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Hapus</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 flex items-center justify-between">
        <p class="text-sm text-gray-600">Menampilkan 1–3 dari 3</p>
        <nav class="inline-flex overflow-hidden rounded-md border border-gray-200">
          <a href="#prev" class="px-3 py-2 text-sm hover:bg-gray-50">Sebelumnya</a>
          <span class="px-3 py-2 text-sm bg-maroon text-white">1</span>
          <a href="#next" class="px-3 py-2 text-sm hover:bg-gray-50">Berikutnya</a>
        </nav>
      </div>
    </div>
  </section>

  <!-- Modal Tambah Inovasi -->
  <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/40" onclick="closeModal()"></div>
    <div class="relative z-10 mx-auto max-w-3xl px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
          <h2 class="text-white text-lg font-bold">Tambah Inovasi</h2>
          <p class="text-white/80 text-xs mt-0.5">Lengkapi metadata inovasi dan unggah dokumen pendukung.</p>
        </div>

        <form id="formTambah" class="p-5 grid sm:grid-cols-2 gap-4" onsubmit="event.preventDefault(); tambahInovasi();">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul Inovasi</span>
            <input id="i_judul" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama inovasi">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">OPD/Unit</span>
            <input id="i_opd" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama OPD/Unit">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tahapan Inovasi</span>
            <select id="i_bentuk" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih…</option>
              <option>Inovasi Tata Kelola</option>
              <option>Pelayanan Publik</option>
              <option>Inovasi Daerah Lainnya</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Inisiator</span>
            <select id="i_inisiator" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option>OPD</option>
              <option>Unit Kerja</option>
              <option>Kolaborasi</option>
            </select>
          </label>
           <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama Inisiator</span>
            <input id="i_opd" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama inisiator">
          </label>
           <label class="block">
            <span class="text-sm font-semibold text-gray-700">Koordinat</span>
            <input id="i_opd" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Koordinat">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Klasifikasi Inovasi</span>
            <select id="i_tahun" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="" default>--</option>
              <hr>
              <option>Inovasi Perangkat Daerah</option>
              <option>Inovasi Desa dan Kelurahan</option>
              <option>Inovasi Masyarakat</option>
            </select>
          </label>

          <!-- Tahapan -->
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Jenis Inovasi</span>
            <select id="i_t_inisiatif" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option>--</option>
              <hr>
              <option>Digital</option>
              <option>Non Digital</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Bentuk Inovasi Daerah</span>
            <select id="i_t_uji" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Daerah lainnya sesuai dengan Urusan Pemerintahan yang menjadi kewenangan Daerah</option>
              <option>Inovasi Pelayanan Publik</option>
              <option>Inovasi Tata Kelolah Pemerintah Daerah</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Asta Cipta</span>
            <select id="i_t_uji" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Daerah lainnya sesuai dengan Urusan Pemerintahan yang menjadi kewenangan Daerah</option>
              <option>Inovasi Pelayanan Publik</option>
              <option>Inovasi Tata Kelolah Pemerintah Daerah</option>
            </select>
          </label>
          <label class="block  sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Program Prioritas Walikota Makassar</span>
            <select id="i_t_uji" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Daerah lainnya sesuai dengan Urusan Pemerintahan yang menjadi kewenangan Daerah</option>
              <option>Inovasi Pelayanan Publik</option>
              <option>Inovasi Tata Kelolah Pemerintah Daerah</option>
            </select>
          </label>
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Urusan Pemerintah</span>
                <select id="i_t_uji" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">--</option>
                <option>Inovasi Daerah lainnya sesuai dengan Urusan Pemerintahan yang menjadi kewenangan Daerah</option>
                <option>Inovasi Pelayanan Publik</option>
                <option>Inovasi Tata Kelolah Pemerintah Daerah</option>
                </select>
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Waktu Uji Coba Inovasi Daerah</span>
               <input id="i_opd" type="date" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama OPD/Unit">
            </label>
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Waktu Penerapan Inovasi Daerah</span>
               <input id="i_opd" type="date" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama OPD/Unit">
            </label>

            <label class="block sm:col-span-2">
              <span class="text-sm font-semibold text-gray-700">Rancang bangun (Min 300 karakter)</span>
              <textarea name="rancang_bangun"
                        class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"
                        data-minlength="300" data-short="true"
                        placeholder="Ringkasan singkat inovasi…"></textarea>
            </label>

            <label class="block sm:col-span-2">
              <span class="text-sm font-semibold text-gray-700">Tujuan inovasi daerah</span>
              <textarea name="tujuan"
                        class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"
                        placeholder="Tujuan…"></textarea>
            </label>

            <label class="block sm:col-span-2">
              <span class="text-sm font-semibold text-gray-700">Manfaat yang diperoleh</span>
              <textarea name="manfaat"
                        class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"
                        placeholder="Manfaat…"></textarea>
            </label>

            <label class="block sm:col-span-2">
              <span class="text-sm font-semibold text-gray-700">Hasil Inovasi</span>
              <textarea name="hasil_inovasi"
                        class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"
                        placeholder="Hasil…"></textarea>
            </label>

          <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Anggaran (Jika diperlukan)</span>
              <input id="i_file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <p class="text-[12px] text-gray-500 mt-1">Utamakan PDF untuk ringkasan / TOR.</p>
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Profil Bisnis (.ppt) (Jika ada)</span>
              <input id="i_file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <p class="text-[12px] text-gray-500 mt-1">Utamakan PDF untuk ringkasan / TOR.</p>
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Dokumen HAKI</span>
              <input id="i_file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <p class="text-[12px] text-gray-500 mt-1">Utamakan PDF untuk ringkasan / TOR.</p>
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Penghargaan</span>
              <input id="i_file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <p class="text-[12px] text-gray-500 mt-1">Utamakan PDF untuk ringkasan / TOR.</p>
            </label>
          </div>

          <div class="sm:col-span-2 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Data inovasi dapat diverifikasi dan diperbaharui oleh admin.</span>
            </div>
            <a href="#sop" class="text-maroon hover:underline">Lihat SOP</a>
          </div>

          <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeModal()">Batal</button>
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
    <script>
  // Inisialisasi semua textarea.richtext → Quill
  const QUILL_EDITORS = []; // [{ta, quill, min, label}]
  function initQuillEditors(){
    document.querySelectorAll('textarea.richtext').forEach((ta, idx) => {
      // bungkus editor
      const wrapper = document.createElement('div');
      wrapper.className = 'mt-1.5';
      const editor = document.createElement('div');
      wrapper.appendChild(editor);

      // sisipkan setelah textarea
      ta.insertAdjacentElement('afterend', wrapper);
      ta.style.display = 'none'; // sembunyikan textarea

      // inisialisasi quill
      const quill = new Quill(editor, {
        theme: 'snow',
        modules: {
          toolbar: [
            ['bold','italic','underline'],
            [{ 'list':'ordered' }, { 'list':'bullet' }],
            ['link','clean']
          ]
        }
      });
      // isi awal dari textarea (jika ada)
      if (ta.value && ta.value.trim() !== '') quill.root.innerHTML = ta.value;

      // simpan referensi
      QUILL_EDITORS.push({
        ta,
        quill,
        min: parseInt(ta.dataset.minlength || '0', 10) || 0,
        label: ta.previousElementSibling?.querySelector('span')?.innerText || ta.name || `Field ${idx+1}`
      });
    });
  }

  // Salin konten Quill → textarea & validasi
  function syncQuillToTextareas(){
    for (const {ta, quill, min, label} of QUILL_EDITORS) {
      const html = quill.root.innerHTML.trim();
      const plainLen = quill.getText().trim().length;
      if (min && plainLen < min) {
        alert(`${label}: minimal ${min} karakter. Saat ini ${plainLen} karakter.`);
        throw new Error('VALIDATION_FAIL');
      }
      ta.value = html; // penting: biar ikut terkirim saat submit
    }
  }

  // Panggil saat awal load
  initQuillEditors();

  // Jika editor ada di dalam modal yang awalnya hidden,
  // repaint ringan saat modal dibuka agar ukuran tepat
  const btnTambah = document.getElementById('btnTambah');
  btnTambah?.addEventListener('click', () => {
    setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 50);
  });

  // ===== Integrasi dengan fungsi submit kamu =====
  // Kalau kamu pakai onsubmit="tambahInovasi()",
  // cukup panggil syncQuillToTextareas() di awal fungsi itu.
  const _tambahInovasi = window.tambahInovasi; // simpan referensi lama
  window.tambahInovasi = function(){
    try {
      syncQuillToTextareas();
    } catch(e){
      // batal submit kalau validasi gagal
      return;
    }
    // ambil preview singkat untuk tabel dari field bertanda data-short
    const shortTA = document.querySelector('textarea.richtext[data-short="true"]');
    if (shortTA) {
      // contoh: jika di fungsi lama kamu pakai 'desc', kita set di global sementara
      window.__INOVASI_DESC_PREVIEW__ = shortTA.value.replace(/<[^>]+>/g,' ').trim().slice(0,140);
    }
    // lanjutkan fungsi lama
    if (typeof _tambahInovasi === 'function') return _tambahInovasi();
  };
</script>

  <script>
    // Modal controls
    const modal = document.getElementById('modal');
    document.getElementById('btnTambah').addEventListener('click', () => { modal.classList.remove('hidden'); });
    function closeModal(){ modal.classList.add('hidden'); }

    // Demo: filter reset
    function resetFilter(){
      ['f_bentuk','f_urusan','f_inisiator','f_t_inisiatif','f_t_uji','f_t_terap','q'].forEach(id=>{
        const el = document.getElementById(id);
        if(!el) return;
        if(el.tagName==='SELECT') el.value='';
        else el.value='';
      });
    }

    // Badge helper
    function tahapBadge(label, status){
      const cls = status==='Selesai' ? 'bg-emerald-50 text-emerald-700'
                : status==='Berjalan' ? 'bg-amber-50 text-amber-700'
                : 'bg-gray-100 text-gray-700';
      return `<span class="px-2 py-0.5 rounded text-xs ${cls}">${label}: ${status}</span>`;
    }

    // Demo: tambah baris inovasi (tanpa upload beneran)
    function tambahInovasi(){
      const judul  = document.getElementById('i_judul').value.trim();
      const opd    = document.getElementById('i_opd').value.trim() || '-';
      const bentuk = document.getElementById('i_bentuk').value;
      const urusan = document.getElementById('i_urusan').value;
      const init   = document.getElementById('i_inisiator').value || '-';
      const t1     = document.getElementById('i_t_inisiatif').value;
      const t2     = document.getElementById('i_t_uji').value;
      const t3     = document.getElementById('i_t_terap').value;
      const desc   = document.getElementById('i_desc').value.trim();

      if(!judul || !bentuk || !urusan){ alert('Mohon isi minimal Judul, Bentuk, dan Jenis Urusan.'); return; }

      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td class="px-4 py-3">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded bg-maroon/10 flex items-center justify-center text-maroon">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
              </svg>
            </div>
            <div>
              <p class="font-medium text-gray-900">${escapeHtml(judul)}</p>
              <p class="text-xs text-gray-600 line-clamp-1">${escapeHtml(desc || '—')}</p>
            </div>
          </div>
        </td>
        <td class="px-4 py-3">${escapeHtml(bentuk)}</td>
        <td class="px-4 py-3">${escapeHtml(urusan)}</td>
        <td class="px-4 py-3">${escapeHtml(init)}</td>
        <td class="px-4 py-3">
          <div class="flex flex-wrap gap-1">
            ${tahapBadge('Inisiatif', t1)}
            ${tahapBadge('Uji Coba', t2)}
            ${tahapBadge('Penerapan', t3)}
          </div>
        </td>
        <td class="px-4 py-3">${escapeHtml(opd)}</td>
        <td class="px-4 py-3">
          <div class="flex flex-wrap gap-2">
            <a href="inovasi-detail.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
            <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</button>
            <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50" onclick="this.closest('tr').remove()">Hapus</button>
          </div>
        </td>
      `;
      document.getElementById('tbody').prepend(tr);
      updateCount(+1);
      closeModal();
      document.getElementById('formTambah').reset();
    }

    function updateCount(delta){
      const info = document.getElementById('countInfo');
      const m = info.textContent.match(/Menampilkan (\d+)/);
      const curr = m ? parseInt(m[1],10) : 0;
      info.textContent = `Menampilkan ${curr + delta} inovasi`;
    }

    function escapeHtml(s){ return s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
  </script>

@endpush