@extends('layouts.page')
@section('content')

  <!-- Hero -->
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
    <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16 lg:py-20">
      <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">

        <!-- Left: Intro Panel -->
        <div class="bg-white/95 rounded-2xl shadow-xl p-5 sm:p-6 md:p-8">
          <h1 class="text-2xl sm:text-3xl font-extrabold text-maroon tracking-tight">
            SIGAP INOVASI – Portal Inovasi Daerah Terpadu
          </h1>
          <p class="mt-2 text-sm sm:text-base text-gray-600">
            Satu pintu untuk menghimpun, menilai, & memantau inovasi dari seluruh OPD Kota Makassar —
            lengkap dengan <span class="font-semibold">tahapan</span>, <span class="font-semibold">evidence</span>,
            dan <span class="font-semibold">log aktivitas</span>.
          </p>

          <!-- Mini "why" bullets -->
          <ul class="mt-5 space-y-3 text-sm">
            <li class="flex gap-3">
              <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg></span>
              <p><span class="font-semibold">Standarisasi data inovasi:</span> metadata seragam, lampiran wajib, & indikator terukur.</p>
            </li>
            <li class="flex gap-3">
              <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg></span>
              <p><span class="font-semibold">Telusur cepat:</span> filter berdasarkan OPD, tahapan (Inisiatif/Uji Coba/Penerapan), dan program prioritas.</p>
            </li>
            <li class="flex gap-3">
              <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 6h18M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6"/></svg></span>
              <p><span class="font-semibold">Evidence & audit:</span> unggah bukti, penilaian bobot, dan jejak akses yang tercatat.</p>
            </li>
          </ul>

          <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('sigap-inovasi.dashboard') }}" class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-4 py-2.5 hover:bg-maroon-800 transition">
              Buka Dashboard
            </a>
            <a href="{{ route('sigap-inovasi.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-maroon text-maroon px-4 py-2.5 hover:bg-maroon hover:text-white transition">
              Lihat Aktivitas
            </a>
          </div>

          <!-- Catatan -->
          <div class="mt-5 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-3">
            <svg class="w-5 h-5 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
            <p class="text-[13px] leading-5 text-amber-800">
              Akses pengelolaan inovasi memerlukan akun petugas/OPD. Semua unggah, ubah, unduh, dan verifikasi tercatat otomatis.
            </p>
          </div>
        </div>

        <!-- Right: System Card -->
        <div class="relative">
          <div class="h-full rounded-2xl bg-white/10 border border-white/20 backdrop-blur p-1">
            <div class="h-full rounded-xl bg-white shadow-2xl p-6 sm:p-8 flex flex-col">
              <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-maroon text-white font-bold">SB</span>
                <div>
                  <h2 class="text-xl sm:text-2xl font-extrabold text-maroon leading-tight">SIGAP INOVASI</h2>
                  <p class="text-xs text-gray-500 -mt-0.5">Portofolio Inovasi Daerah</p>
                </div>
              </div>

              <ul class="mt-6 space-y-4 text-sm">
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg></span>
                  <p><span class="font-semibold">Tahapan:</span> Inisiatif → Uji Coba → Penerapan (terukur & transparan).</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg></span>
                  <p><span class="font-semibold">Filter OPD & Program:</span> fokuskan penelusuran sesuai kebutuhan kebijakan.</p>
                </li>
                <li class="flex gap-3">
                  <span class="shrink-0 mt-0.5"><svg class="w-5 h-5 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 6h18M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6"/></svg></span>
                  <p><span class="font-semibold">Evidence:</span> indikator, bobot, lampiran — memudahkan evaluasi.</p>
                </li>
              </ul>

              <!-- mini "activity" preview (dummy) -->
              <div class="mt-6 rounded-lg border border-gray-200">
                <div class="px-4 py-2.5 bg-gray-50 text-xs font-semibold text-gray-600">Aktivitas Terkini (contoh)</div>
                <ul class="divide-y text-xs">
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span>Pengajuan: “SIM Air Bersih Terintegrasi”</span><span class="text-gray-500">04 Sep 2025 • 10:12</span>
                  </li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span>Verifikasi evidence: “EduHealth”</span><span class="text-gray-500">03 Sep 2025 • 16:40</span>
                  </li>
                  <li class="px-4 py-2 flex items-center justify-between">
                    <span>Perbaikan: “Transport Cerdas”</span><span class="text-gray-500">02 Sep 2025 • 09:05</span>
                  </li>
                </ul>
              </div>

              <div class="mt-6 grid grid-cols-2 gap-3">
                <a href="{{ route('sigap-inovasi.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-maroon text-maroon px-4 py-2.5 hover:bg-maroon hover:text-white transition">Lihat Dashboard</a>
                <a href="{{ route('sigap-inovasi.dashboard') }}" class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-4 py-2.5 hover:bg-maroon-800 transition">Mulai</a>
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
        <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Video Penjelasan SIGAP Inovasi</h3>
        <p class="mt-2 text-gray-600">Tonton alur kerja, tahapan, dan cara verifikasi evidence.</p>
      </div>
      <div class="aspect-w-16 aspect-h-9">
        <iframe class="w-full h-[315px] sm:h-[450px] rounded-xl shadow-lg"
          src="https://www.youtube.com/embed/VIDEO_ID"
          title="Penjelasan SIGAP Inovasi"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          allowfullscreen>
        </iframe>
      </div>
    </div>
  </section>

  <!-- Fitur Utama -->
  <section id="fitur" class="py-14">
    <div class="max-w-7xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Fitur Utama</h3>
        <p class="mt-3 text-gray-600">Didesain seperti perpustakaan inovasi: terstruktur, terukur, dan mudah dipantau.</p>
      </div>

      <div class="mt-10 grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">Pencarian & Filter</h4>
          <p class="mt-1 text-sm text-gray-600">Saring berdasarkan OPD, tahapan, program prioritas, dan kata kunci.</p>
        </div>

        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/><path stroke-width="2" d="M9 14l2 2 4-4"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">Tahapan & KPI</h4>
          <p class="mt-1 text-sm text-gray-600">Pantau progres Inisiatif → Uji Coba → Penerapan dan target tiap triwulan.</p>
        </div>

        <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 6h18M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6"/></svg>
          </div>
          <h4 class="mt-4 font-semibold">Evidence & Audit</h4>
          <p class="mt-1 text-sm text-gray-600">20 indikator evidence, bobot penilaian, dan jejak akses untuk transparansi.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Alur Kerja -->
  <section id="bagaimana" class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid lg:grid-cols-3 gap-8">
        <div>
          <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Alur Kerja Singkat</h3>
          <p class="mt-3 text-gray-600">Mulai dari input OPD hingga inovasi siap diterapkan di lapangan.</p>
        </div>
        <ol class="lg:col-span-2 grid sm:grid-cols-2 gap-6">
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">1) Pengajuan OPD</p>
            <p class="mt-1 text-sm text-gray-600">OPD mengisi metadata inovasi & mengunggah lampiran dasar.</p>
          </li>
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">2) Verifikasi Evidence</p>
            <p class="mt-1 text-sm text-gray-600">BRIDA mengecek indikator, kelengkapan, & bobot penilaian.</p>
          </li>
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">3) Uji Coba</p>
            <p class="mt-1 text-sm text-gray-600">Pendampingan & evaluasi dampak pada unit/lingkup kecil.</p>
          </li>
          <li class="p-5 bg-white rounded-xl border border-gray-200">
            <p class="text-sm font-semibold text-maroon">4) Penerapan</p>
            <p class="mt-1 text-sm text-gray-600">Roll-out lebih luas, monitoring kinerja & output kebijakan.</p>
          </li>
        </ol>
      </div>

      <div class="mt-8 text-center">
        <a href="{{ route('sigap-inovasi.dashboard') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
          Buka Dashboard
        </a>
      </div>
    </div>
  </section>

  <!-- FAQ Ringkas -->
  <section class="py-14">
    <div class="max-w-5xl mx-auto px-4">
      <div class="text-center max-w-2xl mx-auto">
        <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Tanya Jawab Singkat</h3>
        <p class="mt-3 text-gray-600">Hal yang paling sering ditanyakan oleh OPD.</p>
      </div>

      <div class="mt-8 grid md:grid-cols-2 gap-5">
        <div class="p-5 rounded-xl border border-gray-200">
          <p class="font-semibold text-gray-900">Siapa yang bisa mengajukan inovasi?</p>
          <p class="mt-1 text-sm text-gray-600">Akun OPD yang ditunjuk. Pengajuan diverifikasi oleh admin BRIDA.</p>
        </div>
        <div class="p-5 rounded-xl border border-gray-200">
          <p class="font-semibold text-gray-900">Apa saja lampiran minimal?</p>
          <p class="mt-1 text-sm text-gray-600">Anggaran/analisis biaya, profil bisnis/konsep, dokumen pendukung (HAKI/penghargaan bila ada).</p>
        </div>
        <div class="p-5 rounded-xl border border-gray-200">
          <p class="font-semibold text-gray-900">Bagaimana penilaian evidence?</p>
          <p class="mt-1 text-sm text-gray-600">Mengacu 20 indikator dengan bobot. Skor muncul di detail inovasi & dashboard.</p>
        </div>
        <div class="p-5 rounded-xl border border-gray-200">
          <p class="font-semibold text-gray-900">Apakah publik bisa melihat?</p>
          <p class="mt-1 text-sm text-gray-600">Ringkasan dapat dipublikasikan; dokumen sensitif tetap terkendali & tercatat log aksesnya.</p>
        </div>
      </div>

      <div class="mt-10 rounded-2xl overflow-hidden">
        <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 sm:px-10 py-10 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
          <div>
            <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Siap kelola portofolio inovasi secara terukur?</h3>
            <p class="text-white/80 mt-1 text-sm">Masuk ke dashboard untuk mulai memantau progres dan evidence OPD.</p>
          </div>
          <div class="flex gap-3">
            <a href="{{ route('sigap-inovasi.dashboard') }}" class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">Buka Dashboard</a>
            <a href="{{ route('sigap-inovasi.dashboard') }}" class="px-5 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">Lihat Aktivitas</a>
          </div>
        </div>
      </div>

    </div>
  </section>

@endsection
