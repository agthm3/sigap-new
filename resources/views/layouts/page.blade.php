<!DOCTYPE html>
<html lang="id">
<head>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>SIGAP BRIDA — Sistem Informasi Gabungan Arsip & Privasi</title>
  
  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50:  '#fdf7f7',
              100: '#faeeee',
              200: '#f0d1d1',
              300: '#e2a8a8',
              400: '#c86f6f',
              500: '#a64040',
              600: '#8f2f2f',
              700: '#7a2222',
              800: '#661b1b',
              900: '#4a1313',
              DEFAULT: '#7a2222'
            }
          }
        }
      }
    }
  </script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}</style>
  
  {{-- Tambahan: biar child view bisa push head --}}
  @stack('head')

  {{-- Tambahan: SweetAlert2 untuk semua halaman publik --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-white text-gray-800">

<!-- Top Bar -->
<header x-data="{ mobileOpen:false }" class="border-b border-maroon/10 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/70 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="{{ route('home') }}">
      <div class="flex items-center gap-3">
        {{-- Logo --}}
        <img 
          src="{{ asset('images/logo-sigap.png') }}" 
          alt="Logo SIGAP BRIDA"
          class="h-10 w-auto sm:h-11 md:h-12 object-contain"
        >

        {{-- Text --}}
        <div class="leading-tight">
          <p class="text-sm sm:text-base font-semibold text-maroon">
            SIGAP
          </p>
          <p class="text-[10px] sm:text-[11px] text-gray-500">
            Sistem Informasi Gabungan Arsip & Pegawai
          </p>
        </div>
      </div>
    </a>

    {{-- DESKTOP NAV --}}
    <nav class="hidden md:flex items-center gap-6 text-sm">
      
      {{-- DESKTOP NAV: Jenis Layanan (Mega Menu 2 Kolom) --}}
      <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false" class="relative py-2">
        <button
          @click="open = !open"
          :aria-expanded="open"
          class="inline-flex items-center gap-1 hover:text-maroon focus:outline-none font-medium"
        >
          Jenis Layanan
          <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Container Pembungkus Dropdown dengan Invisible Hover Bridge -->
        <div
          x-show="open"
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 translate-y-1"
          x-transition:enter-end="opacity-100 translate-y-0"
          class="absolute top-full -left-4 pt-2 z-50"
        >
          <div class="w-[480px] rounded-xl bg-white border border-gray-100 shadow-xl p-4">
            <div class="grid grid-cols-2 gap-4">
              
              <!-- Kategori 1: Dokumen & Pegawai -->
              <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-maroon mb-1">Dokumen & Pegawai</p>
                <ul class="space-y-0.5 text-xs text-gray-700">
                  <li><a href="{{ route('home.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Dokumen</a></li>
                  <li><a href="{{ route('home.pegawai') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Pegawai</a></li>
                  <li><a href="{{ route('sigap-pjlp.public') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition font-medium">SIGAP PJLP</a></li>
                  <li><a href="{{ route('sigap-absensi.home') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Absensi</a></li>
                  <li><a href="{{ route('sigap-kinerja.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Kinerja</a></li>
                  <li><a href="{{ route('sigap-agenda.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Agenda</a></li>
                  <li><a href="{{ route('sigap-pic.public') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP PIC</a></li>
                </ul>
              </div>

              <!-- Kategori 2: Riset & Inovasi -->
              <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-maroon mb-1">Riset & Inovasi</p>
                <ul class="space-y-0.5 text-xs text-gray-700">
                  <li><a href="{{ route('sigap-inovasi.home') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Inovasi</a></li>
                  <li><a href="{{ route('sigap-riset.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Riset</a></li>
                  <li><a href="{{ route('sigap-inkubatorma.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Inkubatorma</a></li>
                  <li><a href="{{ route('sigap-ppd.public') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP PPD</a></li>
                </ul>
              </div>

              <!-- Kategori 3: Layanan Publik & Utility -->
              <div class="col-span-2 border-t border-gray-100 pt-3">
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-maroon mb-1">Layanan Publik & Utility</p>
                <div class="grid grid-cols-2 gap-x-4 text-xs text-gray-700">
                  <ul class="space-y-0.5">
                    <li>
                      <a href="{{ route('sigap-pdf.landing') }}" class="block px-3 py-1.5 rounded-md bg-maroon-50 font-bold text-maroon hover:bg-maroon-100 transition flex items-center justify-between">
                        <span>SIGAP PDF</span>
                        <span class="text-[9px] bg-maroon text-white px-1.5 py-0.2 rounded">Privacy First</span>
                      </a>
                    </li>
                    <li><a href="{{ route('sigap-auto.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Auto</a></li>
                    <li><a href="{{ route('sigap-format.index') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Format</a></li>
                  </ul>
                  <ul class="space-y-0.5">
                    <li><a href="#" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP SKPRD</a></li>
                    <li><a href="#" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Open Data</a></li>
                    <li><a href="{{ route('sigap-daftar-hadir.public.riwayat-peserta') }}" class="block px-3 py-1.5 rounded-md hover:bg-maroon-50 hover:text-maroon transition">SIGAP Riwayat Peserta</a></li>
                  </ul>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      {{-- Dropdown Profil BRIDA --}}
      <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @keydown.escape.window="open = false" class="relative py-2">
        <button
          @click="open = !open"
          class="inline-flex items-center gap-1 hover:text-maroon focus:outline-none"
        >
          Profil BRIDA
          <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div
          x-show="open"
          x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 translate-y-1"
          x-transition:enter-end="opacity-100 translate-y-0"
          class="absolute top-full left-0 pt-2 z-50"
        >
          <div class="w-56 rounded-lg bg-white border border-gray-200 shadow-lg">
            <ul class="py-2 text-sm text-gray-700">
              <li><a href="{{ route('profil.struktur') }}" class="block px-4 py-2 hover:bg-gray-100">Struktur Organisasi</a></li>
              <li><a href="{{ route('profil.visimisi') }}" class="block px-4 py-2 hover:bg-gray-100">Visi & Misi</a></li>
              <li><a href="{{ route('profil.berita') }}" class="block px-4 py-2 hover:bg-gray-100">Berita BRIDA</a></li>
              <li><a href="{{ route('profil.tentang') }}" class="block px-4 py-2 hover:bg-gray-100">Tentang BRIDA</a></li>
              <li><a href="{{ route('profil.kontak') }}" class="block px-4 py-2 hover:bg-gray-100">Kontak</a></li>
            </ul>
          </div>
        </div>
      </div>

      {{-- Link Navigasi --}}
      <a href="#fitur" class="hover:text-maroon">Fitur</a>
      <a href="#bagaimana" class="hover:text-maroon">Cara Kerja</a>
      <a href="#kontak" class="hover:text-maroon">Kontak</a>

      {{-- Auth / Best Practice Dashboard Route Evaluator --}}
      @guest
        <a href="{{ route('login') }}" class="px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">
          Masuk
        </a>
      @endguest

      @auth
        @php
          $u = auth()->user();
          // Hierarki penentuan route dashboard berdasarkan role tertinggi
          if ($u->hasRole('admin')) {
              $dashboardRoute = route('home.index');
          } elseif ($u->hasRole('inovator')) {
              $dashboardRoute = route('sigap-inovasi.dashboard');
          } elseif ($u->hasAnyRole(['magang', 'verif_magang'])) {
              $dashboardRoute = route('magang.index');
          } else {
              $dashboardRoute = route('pegawai.profil');
          }
        @endphp

        <a href="{{ $dashboardRoute }}" class="px-4 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 transition font-medium text-xs">
          Dashboard Panel
        </a>
      @endauth
    </nav>

    {{-- Mobile Burger Button --}}
    <button @click="mobileOpen=!mobileOpen" class="md:hidden inline-flex items-center px-3 py-2 rounded-md border border-maroon text-maroon" aria-label="Menu">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>

  {{-- MOBILE PANEL --}}
  <div
    x-show="mobileOpen"
    x-transition.opacity
    class="md:hidden border-t border-gray-200 bg-white"
    @keydown.escape.window="mobileOpen=false"
  >
    <div class="max-w-7xl mx-auto px-4 py-4 space-y-3 text-sm">

      {{-- Accordion Jenis Layanan (mobile - Terkategori) --}}
      <div x-data="{ open:false }" class="border rounded-lg">
        <button @click="open=!open" class="w-full flex items-center justify-between px-4 py-3">
          <span class="font-semibold text-gray-800">Jenis Layanan</span>
          <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        
        <div x-show="open" x-transition x-collapse class="border-t bg-gray-50 p-3 space-y-3">
          
          <!-- Kelompok 1 -->
          <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-maroon px-1 mb-1">Dokumen & Pegawai</p>
            <div class="space-y-1 text-sm bg-white rounded-md p-1 border border-gray-100">
              <a href="{{ route('home.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Dokumen</a>
              <a href="{{ route('home.pegawai') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Pegawai</a>
              <a href="{{ route('sigap-pjlp.public') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded font-medium">SIGAP PJLP</a>
              <a href="{{ route('sigap-absensi.home') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Absensi</a>
              <a href="{{ route('sigap-kinerja.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Kinerja</a>
              <a href="{{ route('sigap-agenda.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Agenda</a>
              <a href="{{ route('sigap-pic.public') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP PIC</a>
            </div>
          </div>

          <!-- Kelompok 2 -->
          <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-maroon px-1 mb-1">Riset & Inovasi</p>
            <div class="space-y-1 text-sm bg-white rounded-md p-1 border border-gray-100">
              <a href="{{ route('sigap-inovasi.home') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Inovasi</a>
              <a href="{{ route('sigap-riset.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Riset</a>
              <a href="{{ route('sigap-inkubatorma.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Inkubatorma</a>
              <a href="{{ route('sigap-ppd.public') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP PPD</a>
            </div>
          </div>

          <!-- Kelompok 3 -->
          <div>
            <p class="text-[11px] font-bold uppercase tracking-wider text-maroon px-1 mb-1">Layanan Publik & Utility</p>
            <div class="space-y-1 text-sm bg-white rounded-md p-1 border border-gray-100">
              <a href="{{ route('sigap-pdf.landing') }}" class="block px-3 py-1.5 bg-maroon-50 text-maroon font-bold rounded flex items-center justify-between">
                <span>SIGAP PDF</span>
                <span class="text-[9px] bg-maroon text-white px-1.5 py-0.2 rounded">Privacy First</span>
              </a>
              <a href="{{ route('sigap-auto.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Auto</a>
              <a href="{{ route('sigap-format.index') }}" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Format</a>
              <a href="#" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP SKPRD</a>
              <a href="#" class="block px-3 py-1.5 hover:bg-gray-50 rounded">SIGAP Open Data</a>
              <a href="{{ route('sigap-daftar-hadir.public.riwayat-peserta') }}" class="block px-3 py-1.5 text-maroon font-semibold hover:bg-gray-50 rounded">SIGAP Riwayat Peserta</a>
            </div>
          </div>

        </div>
      </div>

      {{-- Accordion Profil BRIDA --}}
      <div x-data="{ open:false }" class="border rounded-lg">
        <button @click="open=!open" class="w-full flex items-center justify-between px-4 py-3">
          <span class="font-semibold text-gray-800">Profil BRIDA</span>
          <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div x-show="open" x-transition x-collapse class="border-t">
          <ul class="py-2 text-gray-700">
            <li><a href="{{ route('profil.struktur') }}" class="block px-4 py-2 hover:bg-gray-50">Struktur Organisasi</a></li>
            <li><a href="{{ route('profil.visimisi') }}" class="block px-4 py-2 hover:bg-gray-50">Visi & Misi</a></li>
            <li><a href="{{ route('profil.berita') }}" class="block px-4 py-2 hover:bg-gray-50">Berita BRIDA</a></li>
            <li><a href="{{ route('profil.tentang') }}" class="block px-4 py-2 hover:bg-gray-50">Tentang BRIDA</a></li>
            <li><a href="{{ route('profil.kontak') }}" class="block px-4 py-2 hover:bg-gray-50">Kontak</a></li>
          </ul>
        </div>
      </div>

      {{-- Link Biasa --}}
      <a href="#fitur" class="block px-4 py-2 rounded-md hover:bg-gray-50">Fitur</a>
      <a href="#bagaimana" class="block px-4 py-2 rounded-md hover:bg-gray-50">Cara Kerja</a>
      <a href="#kontak" class="block px-4 py-2 rounded-md hover:bg-gray-50">Kontak</a>

      {{-- Auth Button (Mobile) --}}
      @guest
        <a href="{{ route('login') }}" class="block text-center px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">
          Masuk
        </a>
      @endguest

      @auth
        <a href="{{ $dashboardRoute }}" class="block text-center px-4 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 transition font-medium">
          Dashboard Panel
        </a>
      @endauth
    </div>
  </div>
</header>

<!-- Main Content -->
<main>
  @yield('content')
</main>

<!-- Footer -->
<footer id="kontak" class="border-t border-gray-200">
  <div class="max-w-7xl mx-auto px-4 py-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-8 text-sm">
    <div>
      <p class="font-extrabold text-maroon">SIGAP BRIDA</p>
      <p class="mt-2 text-gray-600">BRIDA Kota Makassar</p>
      <p class="text-gray-500 mt-1">© {{ date('Y') }}. All rights reserved.</p>
    </div>
    <div>
      <p class="font-semibold">Navigasi</p>
      <ul class="mt-2 space-y-1 text-gray-600">
        <li><a href="#fitur" class="hover:text-maroon">Fitur</a></li>
        <li><a href="#bagaimana" class="hover:text-maroon">Cara Kerja</a></li>
        <li><a href="#" class="hover:text-maroon">Kebijakan Privasi</a></li>
      </ul>
    </div>
    <div>
      <p class="font-semibold">Bantuan</p>
      <ul class="mt-2 space-y-1 text-gray-600">
        <li><a href="#" class="hover:text-maroon">FAQ</a></li>
        <li><a href="#" class="hover:text-maroon">Panduan Pengguna</a></li>
        <li><a href="#" class="hover:text-maroon">Hubungi Admin</a></li>
        <li><a href="{{ route('reward.index') }}" class="hover:text-maroon"><strong>Reward ⭐⭐⭐⭐</strong></a></li>
        <li><a href="{{ route('about') }}" class="hover:text-maroon"><strong>Klik untuk kejutan🎉</strong></a></li>
      </ul>
    </div>
    <div>
      <p class="font-semibold">Kontak</p>
      <p class="mt-2 text-gray-600">Jl. Ahmad Yani No 2 Kecamatan Ujung Pandang, Kota Makassar, Sulawesi Selatan.</p>
      <p class="text-gray-600">balitbangdamks@gmail.com</p>
    </div>
  </div>
</footer>

@stack('scripts')
</body>
</html>