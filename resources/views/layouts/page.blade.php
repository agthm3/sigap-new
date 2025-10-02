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
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500 -mt-0.5">Sistem Informasi Gabungan Arsip & Privasi</p>
        </div>
      </div>
    </a>

    {{-- DESKTOP NAV --}}
    <nav class="hidden md:flex items-center gap-6 text-sm">
      {{-- Dropdown Jenis Layanan (desktop) --}}
      <div x-data="{open:false}" @keydown.escape.window="open=false" class="relative">
        <button
          @mouseenter="open=true" @mouseleave="open=false" @click="open=!open"
          :aria-expanded="open"
          class="inline-flex items-center gap-1 hover:text-maroon focus:outline-none"
        >
          Jenis Layanan
          <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <!-- Panel dropdown; bungkus dengan container yang juga memicu open agar tidak hilang saat mouse pindah -->
        <div
          x-show="open"
          @mouseenter="open=true" @mouseleave="open=false"
          x-transition.origin.top.left
          class="absolute top-full left-0 mt-2 w-56 rounded-lg bg-white border border-gray-200 shadow-lg z-50"
        >
          <ul class="py-2 text-sm text-gray-700">
            <li><a href="{{ route('home.index') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Dokumen</a></li>
            <li><a href="{{ route('home.pegawai') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Pegawai</a></li>
            <li><a href="{{ route('sigap-auto.index') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Auto</a></li>
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">SIGAP SKPRD</a></li>
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-100">SIGAP Open Data</a></li>
            <li><a href="{{ route('sigap-format.index') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Format</a></li>
            <li><a href="{{ route('sigap-inovasi.home') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Inovasi</a></li>
            <li><a href="{{ route('sigap-riset.index') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Riset</a></li>
            <li><a href="{{ route('sigap-kinerja.index') }}" class="block px-4 py-2 hover:bg-gray-100">SIGAP Kinerja</a></li>
          </ul>
        </div>
      </div>

      {{-- Link lain --}}
      <a href="#fitur" class="hover:text-maroon">Fitur</a>
      <a href="#bagaimana" class="hover:text-maroon">Cara Kerja</a>
      <a href="#kontak" class="hover:text-maroon">Kontak</a>

      {{-- Auth / Role buttons --}}
      @if(!Auth::check())
        <a href="{{ route('login') }}" class="px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Masuk</a>
      @endif

      @role('admin')
        <a href="{{ route('home.index') }}" class="px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Admin Dashboard</a>
      @endrole
      @role('user')
        <a href="{{ route('pegawai.profil') }}" class="px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">User Dashboard</a>
      @endrole
      @role('employee')
        <a href="{{ route('pegawai.profil') }}" class="px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Dashboard Pegawai</a>
      @endrole
      @role('inovator')
        <a href="{{ route('sigap-inovasi.dashboard') }}" class="px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Inovator Dashboard</a>
      @endrole
    </nav>

    {{-- Mobile burger --}}
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

      {{-- Accordion Jenis Layanan (mobile) --}}
      <div x-data="{ open:false }" class="border rounded-lg">
        <button @click="open=!open" class="w-full flex items-center justify-between px-4 py-3">
          <span class="font-semibold text-gray-800">Jenis Layanan</span>
          <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div x-show="open" x-transition x-collapse class="border-t">
          <ul class="py-2 text-gray-700">
            <li><a href="{{ route('home.index') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Dokumen</a></li>
            <li><a href="{{ route('home.pegawai') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Pegawai</a></li>
            <li><a href="{{ route('sigap-auto.index') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Auto</a></li>
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-50">SIGAP SKPRD</a></li>
            <li><a href="#" class="block px-4 py-2 hover:bg-gray-50">SIGAP Open Data</a></li>
            <li><a href="{{ route('sigap-format.index') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Format</a></li>
            <li><a href="{{ route('sigap-inovasi.home') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Inovasi</a></li>
            <li><a href="{{ route('sigap-riset.index') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Riset</a></li>
            <li><a href="{{ route('sigap-kinerja.index') }}" class="block px-4 py-2 hover:bg-gray-50">SIGAP Kinerja</a></li>
          </ul>
        </div>
      </div>

      {{-- Link biasa --}}
      <a href="#fitur" class="block px-4 py-2 rounded-md hover:bg-gray-50">Fitur</a>
      <a href="#bagaimana" class="block px-4 py-2 rounded-md hover:bg-gray-50">Cara Kerja</a>
      <a href="#kontak" class="block px-4 py-2 rounded-md hover:bg-gray-50">Kontak</a>

      {{-- Auth / Role buttons --}}
      @if(!Auth::check())
        <a href="{{ route('login') }}" class="block text-center px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Masuk</a>
      @endif

      @role('admin')
        <a href="{{ route('home.index') }}" class="block text-center px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Admin Dashboard</a>
      @endrole
      @role('user')
        <a href="{{ route('pegawai.profil') }}" class="block text-center px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">User Dashboard</a>
      @endrole
      @role('employee')
        <a href="{{ route('pegawai.profil') }}" class="block text-center px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Dashboard Pegawai</a>
      @endrole
      @role('inovator')
        <a href="{{ route('sigap-inovasi.dashboard') }}" class="block text-center px-4 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Inovator Dashboard</a>
      @endrole
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
        <p class="text-gray-500 mt-1">© 2025. All rights reserved.</p>
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
