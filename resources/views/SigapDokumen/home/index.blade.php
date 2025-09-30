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
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Kata Kunci</span>
              <div class="mt-1.5 relative">
                <input type="search" name="q" value="{{ request('q') }}"
                  placeholder="Contoh: SK Sekretariat A, Laporan 2024, 'KTP pegawai'…"
                  class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pe-10" />
                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">…</span>
              </div>
            </label>

            <div class="grid sm:grid-cols-3 gap-3">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Kategori</span>
                <select name="category" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option value="">Semua</option>
                  <option value="Surat Keputusan" @selected(request('category')==='Surat Keputusan')>Surat Keputusan</option>
                  <option value="Laporan" @selected(request('category')==='Laporan')>Laporan</option>
                  <option value="Formulir" @selected(request('category')==='Formulir')>Formulir</option>
                  <option value="Privasi" @selected(request('category')==='Privasi')>Privasi (KK/KTP)</option>
                </select>
              </label>

              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Pihak Terkait</span>
                <input name="stakeholder" value="{{ request('stakeholder') }}" type="text" placeholder="Sekretariat A, Bidang X…"
                  class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              </label>

              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Tahun</span>
                <select name="year" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                  <option value="">Semua</option>
                  @for($y = now()->year; $y >= now()->year-5; $y--)
                    <option value="{{ $y }}" @selected(request('year')==$y)>{{ $y }}</option>
                  @endfor
                </select>
              </label>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-2">
              <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">… Cari Dokumen</button>
              <a href="{{ route('home.show') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</a>
            </div>

            {{-- kamu bisa tambahkan sort --}}
            <input type="hidden" name="sort" value="{{ request('sort','latest') }}">
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
                <a href="{{ route('home.pegawai') }}" class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-4 py-2.5 hover:bg-maroon-800 transition">SIGAP Pegawai</a>
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
        <a href="{{ route('home.pegawai') }}">
                <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
                  <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
                  </div>
                  <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">Pegawai</strong></span></h4>
               <p class="mt-1 text-sm text-gray-600">Akses data pegawai (seperti KTP, KK) dengan filter canggih. Setiap download dicatat log-nya untuk keamanan.</p>
            </div>
        </a>
        <a href="{{ route('sigap-auto.index') }}">
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">Auto (Coming Soon)</strong></span></h4>
          <p class="mt-1 text-sm text-gray-600">Automasi tugas repetitif. Sistem yang bekerja otomatis, pegawai hanya konfirmasi atau monitor hasilnya.</p>
        </div>
        </a>
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
        <a href="{{ route('sigap-format.index') }}">
          <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
            </div>
            <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">FORMAT </strong></span></h4>
            <p class="mt-1 text-sm text-gray-600">Kumpulan format dokumen standar (contoh: surat, laporan). Download template dalam bentuk Word untuk kemudahan kerja.</p>
          </div>
        </a>
        <a href="{{ route('sigap-inovasi.home') }}">
          <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
            </div>
            <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">INOVASI</strong></span></h4>
            <p class="mt-1 text-sm text-gray-600">
              Direktori inovasi daerah: menampilkan daftar inovasi dari tiap OPD, status tahapan, dan leaderboard kontribusi. Bisa dilacak dan dievaluasi secara real-time.
            </p>
          </div>
        </a>
        <a href="{{ route('sigap-riset.index') }}">
          <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
            </div>
            <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">RISET</strong></span></h4>
            <p class="mt-1 text-sm text-gray-600">
              Kumpulan hasil riset BRIDA dan laporan kajian strategis. Bisa ditelusuri berdasarkan topik, OPD terkait, atau tahun. Tersedia juga fitur preview & unduh.
            </p>
          </div>
        </a>
        <a href="{{ route('sigap-kinerja.index') }}">
          <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
            <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
            </div>
            <h4 class="mt-4 font-semibold">SIGAP <span><strong class="text-maroon">KINERJA</strong></span></h4>
            <p class="mt-1 text-sm text-gray-600">
              Kumpulan hasil penelitian BRIDA yang bisa ditelusuri publik berdasarkan topik, OPD, atau tahun, lengkap dengan preview & unduh.
            </p>
          </div>
        </a>


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

  <!-- ================= SIGAP Swimlane Roadmap (Sep 2025 - Jan 2026) ================ -->
<section id="sigap-swimlane" class="py-12 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Roadmap SIGAP (Swimlane)</h2>
        <p class="text-sm text-gray-600 mt-1">Tiap baris adalah layanan, kolom adalah bulan. Hover untuk detail.</p>
      </div>
      <!-- Legend -->
      <div class="flex flex-wrap gap-2 text-xs">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-green-50 text-green-700 border border-green-200">
          <span class="h-2 w-2 rounded-full bg-green-500"></span> Done
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
          <span class="h-2 w-2 rounded-full bg-blue-500"></span> On Track
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
          <span class="h-2 w-2 rounded-full bg-amber-500"></span> At Risk
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-gray-100 text-gray-700 border border-gray-200">
          <span class="h-2 w-2 rounded-full bg-gray-400"></span> Planned
        </span>
      </div>
    </div>

    <!-- Header months -->
    <div class="mt-6 overflow-x-auto">
      <div class="min-w-[900px]">
        <div class="grid grid-cols-[220px_repeat(5,1fr)] text-xs">
          <div></div>
          <div class="px-3 py-2 font-bold text-gray-700 border-b">Sep 2025</div>
          <div class="px-3 py-2 font-bold text-gray-700 border-b">Okt 2025</div>
          <div class="px-3 py-2 font-bold text-gray-700 border-b">Nov 2025</div>
          <div class="px-3 py-2 font-bold text-gray-700 border-b">Des 2025</div>
          <div class="px-3 py-2 font-bold text-gray-700 border-b">Jan 2026</div>
        </div>

        <!-- Row builder helper (use same 6-col grid) -->
        <!-- ============ Dokumen ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)]">
          <div class="sticky left-0 bg-white z-10 px-3 py-3 border-b font-semibold text-gray-900">Dokumen</div>

          <div class="px-3 py-3 border-b">
            <div class="chip ontrack" data-tip="Perbaikan relevansi & caching hasil cari">
              Search & Filter Stabil
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip done" data-tip="Iframe viewer PDF + ikon fallback">
              Preview PDF & Thumb
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
        </div>

        <!-- ============ Pegawai ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)] bg-gray-50">
          <div class="sticky left-0 bg-gray-50 z-10 px-3 py-3 border-b font-semibold text-gray-900">Pegawai</div>

          <div class="px-3 py-3 border-b">
            <div class="chip ontrack" data-tip="Catat siapa/kapan/alasan unduh">
              Log Akses Download
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip ontrack" data-tip="Kode akses + hint + audit trail">
              Akses Kode (Private)
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
        </div>

        <!-- ============ Auto ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)]">
          <div class="sticky left-0 bg-white z-10 px-3 py-3 border-b font-semibold text-gray-900">Auto</div>

          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip atrisk" data-tip="Trigger upload → validasi → notif">
              Prototipe Bot
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip atrisk" data-tip="Routing multi-level + WA/Email">
              Workflow Pengesahan
            </div>
          </div>
        </div>

        <!-- ============ SKPRD ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)] bg-gray-50">
          <div class="sticky left-0 bg-gray-50 z-10 px-3 py-3 border-b font-semibold text-gray-900">SKPRD</div>

          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip planned" data-tip="Schema kasus & pipeline kajian">
              Form Input Masalah
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip planned" data-tip="Ringkasan status & rekomendasi">
              Dasbor Analitik
            </div>
          </div>
        </div>

        <!-- ============ OPEN DATA ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)]">
          <div class="sticky left-0 bg-white z-10 px-3 py-3 border-b font-semibold text-gray-900">OPEN DATA</div>

          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip ontrack" data-tip="Metadata + lisensi + unduh">
              Katalog Dataset
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip planned" data-tip="Katalog + halaman dataset + log">
              Rilis Beta Portal
            </div>
          </div>
        </div>

        <!-- ============ Format ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)] bg-gray-50">
          <div class="sticky left-0 bg-gray-50 z-10 px-3 py-3 border-b font-semibold text-gray-900">Format</div>

          <div class="px-3 py-3 border-b">
            <div class="chip done" data-tip="Katalog + modal alasan unduh">
              Index & Detail
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip ontrack" data-tip="Bundle DOCX/PDF akhir tahun">
              Paket Template
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
        </div>

        <!-- ============ Inovasi ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)]">
          <div class="sticky left-0 bg-white z-10 px-3 py-3 border-b font-semibold text-gray-900">Inovasi</div>

          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip ontrack" data-tip="Top OPD & status tahapan">
              Dashboard & Leaderboard
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
        </div>

        <!-- ============ Riset ============ -->
        <div class="grid grid-cols-[220px_repeat(5,1fr)] bg-gray-50">
          <div class="sticky left-0 bg-gray-50 z-10 px-3 py-3 border-b font-semibold text-gray-900">Riset</div>

          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b">
            <div class="chip planned" data-tip="Index + filter topik/OPD/tahun">
              Index & Preview Publik
            </div>
          </div>
          <div class="px-3 py-3 border-b">
            <div class="chip planned" data-tip="Migrasi PDF & metadata">
              Import Arsip
            </div>
          </div>
          <div class="px-3 py-3 border-b"></div>
          <div class="px-3 py-3 border-b"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Styles kecil untuk chip & tooltip -->
<style>
  #sigap-swimlane .chip{
    display:inline-block;
    font-size:12px; line-height:1.1;
    padding:8px 10px; border-radius:10px;
    border:1px solid transparent; position:relative; cursor:default;
    white-space:nowrap;
  }
  #sigap-swimlane .chip.done{ background:#ecfdf5; color:#065f46; border-color:#a7f3d0; }
  #sigap-swimlane .chip.ontrack{ background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
  #sigap-swimlane .chip.atrisk{ background:#fffbeb; color:#b45309; border-color:#fde68a; }
  #sigap-swimlane .chip.planned{ background:#f5f5f5; color:#374151; border-color:#e5e7eb; }
  #sigap-swimlane .chip:hover::after{
    content:attr(data-tip);
    position:absolute; left:0; top:calc(100% + 8px);
    background:#111827; color:#fff; font-size:12px;
    padding:6px 8px; border-radius:6px; white-space:nowrap;
    box-shadow:0 8px 20px rgba(0,0,0,.15);
    transform:translateX(-10%); z-index:20;
  }
</style>
<!-- ================= END Swimlane Roadmap ================= -->

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
            <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">Masuk</a>
            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">Daftar</a>
          </div>
        </div>
      </div>
    </div>
  </section>


<!-- Modal Pop-up -->
<div id="popup-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
  <div id="popup-content" class="bg-white max-w-md w-full mx-4 rounded-xl p-6 shadow-xl relative animate-fade-in">
    <button id="close-modal" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
    
    <h3 class="text-lg font-bold text-maroon mb-2">⚠️ Perhatian Pengguna</h3>
    
    <p class="text-sm text-gray-700 mb-3">
      Website <strong>SIGAP BRIDA</strong> saat ini masih dalam tahap pengembangan. Jika Anda menemukan bug, kesalahan tampilan, atau fitur yang tidak berjalan sebagaimana mestinya, mohon bantu kami dengan melaporkannya melalui WhatsApp ke 
      <a href="https://wa.me/6285173231604" class="text-maroon font-semibold hover:underline" target="_blank">0851-7323-1604</a>.
    </p>

    <p class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-3">
      📱 Untuk sementara, tampilan website ini hanya dioptimalkan untuk perangkat <strong>desktop</strong>. Penggunaan di mobile mungkin tidak tampil sempurna. Mohon pengertiannya.
    </p>
    <p class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-md p-3 mb-3">
      🔐 <strong>Catatan Keamanan:</strong><br>
      Karena <em>SIGAP BRIDA</em> masih dalam tahap pengembangan, bisa saja masih terdapat
      <span class="font-semibold">celah keamanan</span> yang belum terdeteksi.  
      Jika Anda memiliki kemampuan atau menemukan potensi celah, mohon bantu kami dengan
      melaporkannya melalui WhatsApp ke 
      <a href="https://wa.me/6285173231604" class="text-maroon font-semibold hover:underline" target="_blank">0851-7323-1604</a>.
      <br><br>
      🙏 Sebagai bentuk penghargaan, nama Anda akan dimasukkan di halaman <strong><a href="{{ route('reward.index') }}">“Reward”</a></strong>
      sebagai apresiasi kontribusi menjaga keamanan sistem ini.
    </p>
    <hr>

    <p class="mt-4 text-sm italic text-gray-600">
      Terimakasih sudah membaca, Semangat bekerja ya cantik/ganteng 😘
    </p>

  </div>
</div>

<style>
  @keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fade-in {
    animation: fade-in 0.4s ease-out;
  }
</style>

@endsection

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('popup-modal');
    const modalContent = document.getElementById('popup-content');
    const closeBtn = document.getElementById('close-modal');

    // Tampilkan modal
    modal.classList.remove('hidden');

    // Tutup otomatis setelah 15 detik
    const timer = setTimeout(() => {
      modal.classList.add('hidden');
    }, 20000);

    // Klik tombol close
    closeBtn.addEventListener('click', () => {
      modal.classList.add('hidden');
      clearTimeout(timer);
    });

    // Klik di luar modal-content = tutup juga
    modal.addEventListener('click', (e) => {
      if (!modalContent.contains(e.target)) {
        modal.classList.add('hidden');
        clearTimeout(timer);
      }
    });
  });
</script>
