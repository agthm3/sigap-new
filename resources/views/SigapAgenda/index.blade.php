@extends('layouts.page')
@section('content')

<!-- HERO -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-7xl mx-auto px-4 py-14 sm:py-16 lg:py-20">
    <div class="text-center max-w-3xl mx-auto">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs mb-3">
        <span class="h-2 w-2 rounded-full bg-green-400"></span> Modul Terverifikasi
      </div>
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
        SIGAP <span class="text-white/90">AGENDA</span>
      </h1>
      <p class="mt-3 text-white/80 text-sm sm:text-base">
        Modul untuk menyusun, mempublikasikan, dan membagikan <b>agenda resmi</b> unit/pejabat. Agenda yang dipublikasikan
        dilabeli <em>“telah diverifikasi melalui SIGAP AGENDA”</em> agar mudah dipercaya. ✅
      </p>

      <div class="mt-6 flex justify-center">
        <a href="{{ route('sigap-agenda.index') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-maroon font-semibold hover:bg-white/90">
          Buka Dashboard
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" d="M9 5l7 7-7 7"/>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- FITUR UTAMA -->
<section class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center max-w-2xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Apa yang bisa dilakukan?</h2>
      <p class="mt-2 text-gray-600 text-sm">Ringkas, terstruktur, siap dibagikan.</p>
    </div>

    <div class="mt-8 grid md:grid-cols-3 gap-6">
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
          📅
        </div>
        <h3 class="mt-3 font-semibold text-gray-900">Kelola Agenda</h3>
        <p class="text-sm text-gray-600 mt-1">Buat, edit, urutkan kegiatan (waktu, tempat, penugasan) dalam sekali halaman.</p>
      </div>
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
          ✅
        </div>
        <h3 class="mt-3 font-semibold text-gray-900">Verifikasi Publik</h3>
        <p class="text-sm text-gray-600 mt-1">Tandai sebagai publik—pengunjung melihat label “Agenda telah diverifikasi melalui SIGAP AGENDA”.</p>
      </div>
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
          📲
        </div>
        <h3 class="mt-3 font-semibold text-gray-900">Share Gampang</h3>
        <p class="text-sm text-gray-600 mt-1">Satu klik untuk salin ringkasan, salin tautan, atau kirim gambar agenda ke WA.</p>
      </div>
    </div>
  </div>
</section>

<!-- CARA KERJA (singkat) -->
<section class="py-14 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center max-w-2xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Cara Kerja</h2>
      <p class="mt-2 text-gray-600 text-sm">Tiga langkah mudah untuk menyiapkan agenda resmi.</p>
    </div>

    <div class="mt-8 grid md:grid-cols-3 gap-4">
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">1. Susun</p>
        <h4 class="mt-1 font-semibold">Tambah Kegiatan</h4>
        <p class="text-sm text-gray-600 mt-1">Isi deskripsi, waktu, tempat, dan (opsional) penugasan.</p>
      </div>
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">2. Verifikasi</p>
        <h4 class="mt-1 font-semibold">Tandai Publik</h4>
        <p class="text-sm text-gray-600 mt-1">Aktifkan publik untuk memunculkan label verifikasi.</p>
      </div>
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">3. Bagikan</p>
        <h4 class="mt-1 font-semibold">Salin / Share</h4>
        <p class="text-sm text-gray-600 mt-1">Bagikan tautan atau gambar panjang ke kanal komunikasi.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="rounded-2xl overflow-hidden">
      <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 sm:px-10 py-10 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
          <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Mulai kelola agenda sekarang</h3>
          <p class="text-white/80 mt-1 text-sm">Masuk ke dashboard untuk membuat dan membagikan agenda.</p>
        </div>
        <div class="flex gap-3">
          <a href="{{ route('sigap-agenda.index') }}" class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">
            Buka Dashboard
          </a>
          <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">
            Masuk
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
