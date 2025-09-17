@extends('layouts.page')

@section('content')
      <!-- Hero: kiri form pencarian pegawai, kanan kartu penjelasan -->
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
    <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16 lg:py-20">
      <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">

        <!-- LEFT: Form Pencarian Data Pegawai -->
        <div class="bg-white/95 rounded-2xl shadow-xl p-5 sm:p-6 md:p-8">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-maroon tracking-tight">
            SIGAP PEGAWAI — Arsip Data Pegawai
          </h1>
          <p class="mt-2 text-sm sm:text-base text-gray-600">
            Telusuri dokumen kepegawaian (KTP, KK, NPWP, BPJS, ijazah, dsb.) dengan kontrol akses & audit ketat.
          </p>

          <form class="mt-6 grid grid-cols-1 gap-4" onsubmit="event.preventDefault(); window.location.href='pegawai-hasil.html';">
            <!-- Tab pencarian sederhana (Nama/NIP) -->
            <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
              <button type="button" class="px-3 py-2 rounded-lg bg-maroon text-white">Nama/NIP</button>
              <button type="button" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700"
                onclick="document.getElementById('advanced').classList.remove('hidden'); this.classList.add('bg-gray-100');">
                Pencarian Lanjutan
              </button>
            </div>

            <div class="grid sm:grid-cols-2 gap-3">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Nama Pegawai</span>
                <input type="text" placeholder="Contoh: Andi Rahman"
                  class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">NIP</span>
                <input type="text" placeholder="18xxxxxxxxxxxxxxx"
                  class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" inputmode="numeric">
              </label>
            </div>

            <div class="grid sm:grid-cols-3 gap-3">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Jenis Dokumen</span>
                <select class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option>Semua</option>
                  <option>KTP</option>
                  <option>KK</option>
                  <option>NPWP</option>
                  <option>BPJS</option>
                  <option>Ijazah</option>
                  <option>Pas Foto</option>
                </select>
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Unit/Bagian</span>
                <input type="text" placeholder="Kepegawaian, Sekretariat A, Bidang X…"
                  class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Tahun Berlaku</span>
                <select class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option>Semua</option>
                  <option>2025</option>
                  <option>2024</option>
                  <option>2023</option>
                  <option>2022</option>
                </select>
              </label>
            </div>

            <!-- Advanced filters -->
            <div id="advanced" class="hidden grid sm:grid-cols-3 gap-3">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Status Pegawai</span>
                <select class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option>Semua</option>
                  <option>PNS</option>
                  <option>PPPK</option>
                  <option>Non-ASN</option>
                </select>
              </label>
              <label class="block sm:col-span-2">
                <span class="text-sm font-semibold text-gray-700">Alasan Akses (Wajib untuk Privasi)</span>
                <select class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option value="">Pilih alasan…</option>
                  <option>Administrasi gaji/tunjangan</option>
                  <option>Penugasan/keperluan dinas</option>
                  <option>Verifikasi identitas internal</option>
                  <option>Lainnya (sesuai SOP)</option>
                </select>
              </label>
            </div>

            <!-- Info privasi -->
            <div class="mt-1 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
              <svg class="w-5 h-5 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <p class="text-[13px] leading-5 text-amber-800">
                Dokumen pegawai adalah data pribadi. Akses mengikuti SOP & role (RBAC).
                Setiap <span class="font-semibold">view/unduh</span> dicatat lengkap (user, waktu, alasan).
              </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
              <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
                Cari Data
              </button>
              <button type="reset" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                Reset
              </button>
            </div>
          </form>
        </div>

        <!-- RIGHT: Kartu Penjelasan Sistem -->
        <div class="relative">
          <div class="h-full rounded-2xl bg-white/10 border border-white/20 backdrop-blur p-1">
            <div class="h-full rounded-xl bg-white shadow-2xl p-6 sm:p-8 flex flex-col">
              <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-maroon text-white font-bold">SP</span>
                <div>
                  <h2 class="text-xl sm:text-2xl font-extrabold text-maroon leading-tight">SIGAP PEGAWAI</h2>
                  <p class="text-xs text-gray-500 -mt-0.5">Portal Arsip Data Pegawai</p>
                </div>
              </div>

              <ul class="mt-6 space-y-4 text-sm">
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg></span>
                  <p><span class="font-semibold">Kontrol Akses:</span> Hanya role berwenang yang dapat melihat/unduh dokumen privasi.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg></span>
                  <p><span class="font-semibold">Pencarian Tepat:</span> Nama/NIP, jenis dokumen, unit, tahun berlaku.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 6h18M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6"/></svg></span>
                  <p><span class="font-semibold">Preview & Unduh:</span> Tampilkan thumbnail/preview sebelum unduhan.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 4h16v6H4zM6 20h12"/></svg></span>
                  <p><span class="font-semibold">Catatan Audit:</span> Semua aktivitas terekam (siapa/kapan/apa/alasan).</p>
                </li>
              </ul>

              <!-- mini statistik contoh -->
              <div class="mt-6 grid grid-cols-3 gap-3 text-center">
                <div class="rounded-lg border p-3">
                  <p class="text-xs text-gray-500">Total Pegawai</p>
                  <p class="text-lg font-extrabold text-maroon">214</p>
                </div>
                <div class="rounded-lg border p-3">
                  <p class="text-xs text-gray-500">Dokumen</p>
                  <p class="text-lg font-extrabold text-maroon">1.982</p>
                </div>
                <div class="rounded-lg border p-3">
                  <p class="text-xs text-gray-500">Akses 30 hari</p>
                  <p class="text-lg font-extrabold text-maroon">356</p>
                </div>
              </div>

              <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="index.html" class="inline-flex items-center justify-center rounded-lg border border-maroon text-maroon px-4 py-2.5 hover:bg-maroon hover:text-white transition">Ke SIGAP Dokumen</a>
                <a href="#!" class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-4 py-2.5 hover:bg-maroon-800 transition">Masuk Admin</a>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Konten tambahan: FAQ singkat & kebijakan -->
  <section class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid lg:grid-cols-3 gap-8">
        <div>
          <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Kebijakan & SOP</h3>
          <p class="mt-3 text-gray-600">SIGAP PEGAWAI menerapkan standar perlindungan data pribadi sesuai SOP internal dan peraturan perundangan.</p>
        </div>
        <div class="lg:col-span-2 grid sm:grid-cols-2 gap-6">
          <div class="p-5 bg-white rounded-xl border">
            <p class="text-sm font-semibold text-maroon">Siapa yang dapat mengakses?</p>
            <p class="mt-1 text-sm text-gray-600">Role/Unit berwenang (mis. Kepegawaian). Permintaan akses dicatat dan dapat diminta alasan.</p>
          </div>
          <div class="p-5 bg-white rounded-xl border">
            <p class="text-sm font-semibold text-maroon">Apa saja yang dicatat?</p>
            <p class="mt-1 text-sm text-gray-600">User, waktu, dokumen, aksi (view/unduh), serta alasan akses (bila privasi).</p>
          </div>
          <div class="p-5 bg-white rounded-xl border">
            <p class="text-sm font-semibold text-maroon">Format yang didukung</p>
            <p class="mt-1 text-sm text-gray-600">PDF (utama), JPG/PNG (scan), dan metadata standar (NIP, unit, masa berlaku).</p>
          </div>
          <div class="p-5 bg-white rounded-xl border">
            <p class="text-sm font-semibold text-maroon">Integrasi</p>
            <p class="mt-1 text-sm text-gray-600">Terhubung dengan SIGAP Dokumen untuk konsistensi antarmuka & audit terpadu.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-14">
    <div class="max-w-7xl mx-auto px-4">
      <div class="rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 sm:px-10 py-10 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div>
            <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Cari data pegawai secara aman & cepat</h3>
            <p class="text-white/80 mt-1 text-sm">Gunakan kata kunci Nama/NIP dan pilih jenis dokumen yang dibutuhkan.</p>
          </div>
          <div class="flex gap-3">
            <a href="#top" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">Mulai Cari</a>
            <a href="index.html" class="px-5 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">Ke SIGAP Dokumen</a>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection