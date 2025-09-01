@extends('layouts.page')
@section('content')
    
  <!-- Hero -->
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
    <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16 lg:py-20">
      <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">
        
        <!-- Left: Search Panel -->
        <div class="bg-white/95 rounded-2xl shadow-xl p-5 sm:p-6 md:p-8">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-maroon tracking-tight">
            SIGAP DOKUMEN - Temukan Dokumen Resmi BRIDA
          </h1>
          <p class="mt-2 text-sm sm:text-base text-gray-600">
            Akses arsip terpusat: surat, SK, laporan, hingga dokumen privasi (dengan kontrol & log akses).
          </p>

          <form class="mt-6 grid grid-cols-1 gap-4" action="{{ route('home.show') }}" method="GET">
            @csrf
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Kata Kunci</span>
              <div class="mt-1.5 relative">
                <input type="search" name="q" placeholder="Contoh: SK Sekretariat A, Laporan 2024, 'KTP pegawai'…"
                  class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pe-10" />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">
                  <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="7" stroke-width="2"></circle>
                    <path d="M21 21l-4.3-4.3" stroke-width="2" stroke-linecap="round"></path>
                  </svg>
                </span>
              </div>
            </label>

            <div class="grid sm:grid-cols-3 gap-3">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Kategori</span>
                <select class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option>Semua</option>
                  <option>Surat Keputusan</option>
                  <option>Laporan</option>
                  <option>Formulir</option>
                  <option>Privasi (KK/KTP)</option>
                </select>
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Pihak Terkait</span>
                <input type="text" placeholder="Sekretariat A, Bidang X…"
                  class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Tahun</span>
                <select class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option>Semua</option>
                  <option>2025</option>
                  <option>2024</option>
                  <option>2023</option>
                  <option>2022</option>
                </select>
              </label>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
              <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
                Cari Dokumen
              </button>
              <button type="reset" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
                Reset
              </button>
            </div>

            <!-- Nota Privasi -->
            <div class="mt-4 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
              <svg class="w-5 h-5 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <p class="text-[13px] leading-5 text-amber-800">
                Dokumen privasi (KK/KTP, dsb.) hanya dapat diunduh oleh pengguna berwenang.
                Setiap akses dan unduhan dicatat lengkap (user, waktu, alasan).
              </p>
            </div>
          </form>
        </div>

        <!-- Right: System Card -->
        <div class="relative">
          <div class="h-full rounded-2xl bg-white/10 border border-white/20 backdrop-blur p-1">
            <div class="h-full rounded-xl bg-white shadow-2xl p-6 sm:p-8 flex flex-col">
              <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-maroon text-white font-bold">SB</span>
                <div>
                  <h2 class="text-xl sm:text-2xl font-extrabold text-maroon leading-tight">SIGAP BRIDA</h2>
                  <p class="text-xs text-gray-500 -mt-0.5">Portal Arsip Digital Terpadu</p>
                </div>
              </div>

              <ul class="mt-6 space-y-4 text-sm">
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/><path d="M8 7v10" stroke-width="2"/></svg></span>
                  <p><span class="font-semibold">Arsip Terpusat:</span> Dokumen fix, metadata jelas, versi terkontrol.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg></span>
                  <p><span class="font-semibold">Pencarian Cepat:</span> Filter kategori, pihak terkait, tahun, alias.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg></span>
                  <p><span class="font-semibold">Keamanan & Log:</span> Hak akses bertingkat, semua view/unduh tercatat.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 6h18M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6"/></svg></span>
                  <p><span class="font-semibold">Preview & Unduh:</span> Thumbnail, deskripsi, tombol view/download.</p>
                </li>
              </ul>

              <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="#fitur" class="inline-flex items-center justify-center rounded-lg border border-maroon text-maroon px-4 py-2.5 hover:bg-maroon hover:text-white transition">Lihat Fitur</a>
                <a href="#" class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-4 py-2.5 hover:bg-maroon-800 transition">SIGAP Pegawai</a>
              </div>

              <!-- mini "activity" preview -->
              <div class="mt-6 rounded-lg border border-gray-200">
                <div class="px-4 py-2.5 bg-gray-50 text-xs font-semibold text-gray-600">Aktivitas Terkini (contoh)</div>
                <ul class="divide-y text-xs">
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span>Unduh: SK Sekretariat A</span><span class="text-gray-500">10 Aug 2025 • 14:02</span>
                  </li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span>Lihat: KTP Pegawai (terkendali)</span><span class="text-gray-500">10 Aug 2025 • 13:47</span>
                  </li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span>Unggah: Laporan Kinerja 2024</span><span class="text-gray-500">09 Aug 2025 • 09:21</span>
                  </li>
                </ul>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- Video Penjelasan -->
<section class="py-14 bg-gray-50">
  <div class="max-w-4xl mx-auto px-4">
    <div class="text-center mb-6">
      <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Video Penjelasan SIGAP BRIDA</h3>
      <p class="mt-2 text-gray-600">Tonton untuk memahami cara kerja dan manfaat sistem ini.</p>
    </div>
    <div class="aspect-w-16 aspect-h-9">
      <iframe class="w-full h-[315px] sm:h-[450px] rounded-xl shadow-lg"
        src="https://www.youtube.com/embed/VIDEO_ID" 
        title="Penjelasan SIGAP BRIDA" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
        allowfullscreen>
      </iframe>
    </div>
  </div>
</section>

 <!-- Fitur -->
<section id="jenisSigap" class="py-14">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Jenis Layanan SIGAP</h3>
        <p class="mt-3 text-gray-600">Hadir untuk menjadi solusi nyata: Simpel, Cepat,Terintegrasi,  Mudah dipahami.</p>
      </div>

      <div class="mt-10 grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">Dokumen</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Pusat semua dokumen tetap BRIDA. Cari dan download dokumen dengan metadata lengkap (judul, deskripsi, versi).</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">Pegawai</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Akses data pegawai (seperti KTP, KK) dengan filter canggih. Setiap download dicatat log-nya untuk keamanan.</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">Auto (Coming Soon)</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Automasi tugas repetitif. Sistem yang bekerja otomatis, pegawai hanya konfirmasi atau monitor hasilnya.</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">SKPRD (Coming Soon)</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Smart Research & Policy Dashboard. Tempat input masalah kota (contoh: masalah PDAM) untuk dikaji dan dicarikan solusi oleh BRIDA.</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">OPEN DATA (Coming Soon)</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Portal data terbuka BRIDA. Berisi data non-sensitive yang bisa diakses publik dengan tetap tercatat log-nya.</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">FORMAT (Coming Soon)</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Kumpulan format dokumen standar (contoh: surat, laporan). Download template dalam bentuk Word untuk kemudahan kerja.</p>
        </div>
      </div>
    </div>
</section>
<section id="fitur" class="py-14">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Fitur Utama</h3>
        <p class="mt-3 text-gray-600">Didesain seperti perpustakaan digital: cepat, jelas, dan aman.</p>
      </div>

      <div class="mt-10 grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">Metadata & Versi</h4>
          <p class="mt-1 text-sm text-gray-600">Judul, deskripsi, alias, pihak terkait, versi dokumen yang terkontrol.</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">Filter Pencarian</h4>
          <p class="mt-1 text-sm text-gray-600">Cari berdasarkan kategori, tahun, pihak terkait, label privasi.</p>
        </div>
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">Akses & Audit</h4>
          <p class="mt-1 text-sm text-gray-600">RBAC, persetujuan untuk dokumen sensitif, log lengkap view/unduh.</p>
        </div>
      </div>
    </div>
</section>

 

  <!-- How it works -->
  <section id="bagaimana" class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid lg:grid-cols-3 gap-8">
        <div>
          <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Bagaimana Cara Kerjanya?</h3>
          <p class="mt-3 text-gray-600">Alur sederhana dari unggah hingga distribusi dokumen yang terkendali.</p>
        </div>
        <ol class="lg:col-span-2 grid sm:grid-cols-2 gap-6">
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">1) Unggah & Klasifikasi</p>
            <p class="mt-1 text-sm text-gray-600">Petugas mengunggah file, mengisi metadata, dan menandai sebagai publik/privasi.</p>
          </li>
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">2) Telusur & Preview</p>
            <p class="mt-1 text-sm text-gray-600">Pengguna mencari dengan filter, melihat thumbnail/preview sebelum unduh.</p>
          </li>
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">3) Kontrol Akses</p>
            <p class="mt-1 text-sm text-gray-600">Hanya role tertentu bisa mengakses data sensitif, bisa disertai alasan akses.</p>
          </li>
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">4) Audit & Transparansi</p>
            <p class="mt-1 text-sm text-gray-600">Semua aktivitas tercatat (siapa/kapan/apa) untuk kebutuhan audit internal.</p>
          </li>
        </ol>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-14">
    <div class="max-w-7xl mx-auto px-4">
      <div class="rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 sm:px-10 py-10 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div>
            <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Siap mulai mengarsipkan dengan rapi?</h3>
            <p class="text-white/80 mt-1 text-sm">Buat akun petugas/pegawai dan mulai unggah dokumen fix ke SIGAP BRIDA.</p>
          </div>
          <div class="flex gap-3">
            <a href="#" class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">Masuk</a>
            <a href="#" class="px-5 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">Daftar</a>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection