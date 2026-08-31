<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard — SIGAP BRIDA</title>

  <!-- Tailwind -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50:'#fdf7f7',100:'#faeeee',200:'#f0d1d1',300:'#e2a8a8',
              400:'#c86f6f',500:'#a64040',600:'#8f2f2f',700:'#7a2222',
              800:'#661b1b',900:'#4a1313', DEFAULT:'#7a2222'
            }
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
    .scrollbar-thin::-webkit-scrollbar{height:6px;width:6px}
    .scrollbar-thin::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}

    input,
select,
textarea {
  border: 1px solid #d1d5db !important; /* gray-300 */
  background-color: #ffffff !important;
  color: #111827 !important;
}

input:focus,
select:focus,
textarea:focus {
  border-color: #7a2222 !important; /* maroon */
  box-shadow: 0 0 0 2px rgba(122, 34, 34, 0.15) !important;
  outline: none !important;
}

input::placeholder,
textarea::placeholder {
  color: #9ca3af !important;
}
  </style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Chart.js -->
  {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
@stack('head')

<!-- PWA Meta Tags -->
<link rel="manifest" href="https://sigap.brida.makassarkota.go.id/manifest.json?v=3">
<meta name="theme-color" content="#7a2222">

<!-- Apple Touch Icon -->
<link rel="apple-touch-icon" sizes="192x192" href="https://sigap.brida.makassarkota.go.id/images/icon-192.png">
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Layout -->
  <div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="sidebar"  class="fixed lg:sticky top-0 h-screen w-72 translate-x-[-100%] lg:translate-x-0 bg-white border-r border-gray-200 z-40 transition-transform duration-200">
      <div class="h-16 px-4 border-b border-gray-200 flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500">Admin Panel</p>
        </div>
      </div>

      <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] scrollbar-thin">
        @hasrole('admin|employee')
        <a href="{{ route('home.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg    {{ request()->routeIs('home.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/></svg>
          Dashboard
        </a>
        @endhasrole
        @hasrole('employee|admin')
        <a href="{{ route('sigap-dokumen.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg  {{ request()->routeIs('sigap-dokumen.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
          SIGAP Dokumen
        </a>
        @endhasrole
        @hasrole('admin|verif_pegawai')
            <a href="{{ route('sigap-pegawai.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('sigap-pegawai.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              SIGAP Pegawai
            </a>
        @endhasrole
        @hasanyrole('admin|verif_pic')
        <a href="{{ route('sigap-pic.index') }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg
                {{ request()->routeIs('sigap-pic.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">

          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M9 12h6"/>
            <path stroke-width="2" d="M12 9v6"/>
            <path stroke-width="2" d="M4 5h16v14H4z"/>
          </svg>

          SIGAP PIC
        </a>
        @endhasanyrole
        <a href="{{ route('pegawai.profil') }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg
                  {{ request()->routeIs('pegawai.profil') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M4 21v-3.5A4.5 4.5 0 0 1 8.5 13h7A4.5 4.5 0 0 1 20 17.5V21"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          Profil Pegawai
        </a>
        @hasrole('admin|employee')
<div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
          SIGAP KINERJA
        </div>

        {{-- Toggle Dropdown Kinerja --}}
        <button id="kinerjaToggle"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left transition-colors">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M3 7h18M3 12h18M3 17h10"/>
          </svg>
          <span class="font-medium">Kinerja</span>
          <svg id="kinerjaCaret"
               class="w-4 h-4 ml-auto transition-transform duration-200"
               viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M6 9l6 6 6-6"/>
          </svg>
        </button>

        {{-- Dropdown Sub-menu --}}
        <div id="kinerjaMenu" class="ml-3 mt-1 space-y-1 hidden">
          <a href="{{ route('sigap-kinerja.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('sigap-kinerja.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 7h18M3 12h18M3 17h10"/></svg>
            Bukti Kinerja
          </a>
          <a href="{{ route('sigap-story.create') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('sigap-story.create') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            SIGAP Story
          </a>
          <a href="{{ route('sigap-story.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('sigap-story.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Riwayat Story
          </a>
      </div>
        @endhasrole
        @hasanyrole('admin|verif_skp|employee')
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP SKP
      </div>
      
      {{-- Toggle --}}
      <button id="skpToggle"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M12 3v2M12 19v2M19 12h2M3 12h2"/>
          <rect x="5" y="4" width="14" height="16" rx="2" stroke-width="2" />
        </svg>
        <span class="font-medium">SIGAP SKP</span>
        <svg id="skpCaret"
            class="w-4 h-4 ml-auto transition-transform duration-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
      </button>
      
      {{-- Dropdown --}}
      <div id="skpMenu" class="ml-3 mt-1 space-y-1 hidden">
        <a href="{{ route('sigap-skp.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-skp.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4" stroke-width="2"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
          </svg>
          SKP Umum
        </a>
        
        <a href="{{ route('sigap-skp.pribadi') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-skp.pribadi') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4" stroke-width="2"/>
          </svg>
          SKP Pribadi
        </a>

        <a href="{{ route('sigap-skp.monitoring') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-skp.monitoring') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Monitoring SKP
        </a>
      </div>
      @endhasanyrole
        @hasrole('admin|verificator|employee')
            <a href="{{ route('sigap-agenda.index') }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg
                {{ request()->routeIs('sigap-agenda.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
            <path d="M16 2v4M8 2v4" stroke-width="2"/>
            <path d="M3 10h18" stroke-width="2"/>
            </svg>
          Agenda
        </a>
        @endhasrole
        {{-- <a href="permintaan-akses.html" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M4 6h16M4 10h16M4 14h10"/></svg>
          Permintaan Akses
          <span class="ml-auto text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">5</span>
        </a>
        <a href="log-aktivitas.html" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2a10 10 0 0 0-7 17l-1 4 4-1A10 10 0 1 0 12 2z"/></svg>
          Log Aktivitas
        </a> --}}

        

      @hasanyrole('admin|inovator')
        <!-- SIGAP INOVASI -->
          <!-- SECTION BARU: SIGAP INOVASI -->
        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">SIGAP INOVASI</div>

        <!-- Toggle -->
        <button id="inovasiToggle"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 3l3.5 6 6.5 1-4.7 4.7 1.1 6.3L12 18l-6 3.9 1.1-6.3L2.4 10 9 9z"/>
          </svg>
          <span class="font-medium">SIGAP Inovasi</span>
          <svg id="inovasiCaret" class="w-4 h-4 ml-auto transition-transform duration-200"
              viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M6 9l6 6 6-6"/>
          </svg>
        </button>

        <!-- Dropdown Items -->
        <div id="inovasiMenu" class="ml-3 mt-1 space-y-1 hidden">
          <a href="{{ route('sigap-inovasi.dashboard') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
            {{ request()->routeIs('sigap-inovasi.dashboard') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/></svg>
            Dashboard 
          </a>
          <a href="{{ route('sigap-inovasi.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
            {{ request()->routeIs('sigap-inovasi.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
            </svg>
            Inovasi Daerah
          </a>
          
          <a href="{{ route('sigap-iga.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
            {{ request()->routeIs('sigap-iga.*') ? 'bg-[#002B4C] text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            Akun IGA BSKDN
          </a>

          <a href="{{ route('sigap-inovasi.konfigurasi') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
            {{ request()->routeIs('sigap-inovasi.konfigurasi') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M10.325 4.317l.387-1.934a1 1 0 0 1 .976-.8h1.624a1 1 0 0 1 .976.8l.387 1.934a1 1 0 0 0 .725.725l1.934.387a1 1 0 0 1 .8.976v1.624a1 1 0 0 1-.8.976l-1.934.387a1 1 0 0 0-.725.725l-.387 1.934a1 1 0 0 1-.976.8h-1.624a1 1 0 0 1-.976-.8l-.387-1.934a1 1 0 0 0-.725-.725l-1.934-.387a1 1 0 0 1-.8-.976V6.005a1 1 0 0 1 .8-.976l1.934-.387a1 1 0 0 0 .725-.325z"/>
              <circle cx="12" cy="12" r="3" stroke-width="2"/>
            </svg>
            Konfigurasi
          </a>
        </div>
      @endhasanyrole

      @hasanyrole('admin|verifikator_inkubatorma|user')
      <!-- SIGAP INKUBATORMA -->
          <!-- SECTION BARU: SIGAP INKUBATORMA -->
        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">SIGAP INKUBATORMA</div>

        <!-- Toggle -->
        <button id="inkubatormaToggle"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 3l3.5 6 6.5 1-4.7 4.7 1.1 6.3L12 18l-6 3.9 1.1-6.3L2.4 10 9 9z"/>
          </svg>
          <span class="font-medium">SIGAP Inkubatorma</span>
          <svg id="inkubatormaCaret" class="w-4 h-4 ml-auto transition-transform duration-200"
              viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M6 9l6 6 6-6"/>
          </svg>
        </button>

        <!-- Dropdown Items -->
        <div id="inkubatormaMenu" class="ml-3 mt-1 space-y-1 hidden">
          <a href="{{ route('sigap-inkubatorma.dashboard') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
            {{ request()->routeIs('sigap-inkubatorma.dashboard') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/></svg>
            Dashboard 
          </a>
        </div>
      @endhasanyrole

      @hasanyrole('admin|researcher')
        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">SIGAP RISET</div>

        <!-- Toggle -->
        <button id="risetToggle"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left"
                aria-controls="risetMenu" aria-expanded="false">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 3l3.5 6 6.5 1-4.7 4.7 1.1 6.3L12 18l-6 3.9 1.1-6.3L2.4 10 9 9z"/>
          </svg>
          <span class="font-medium">SIGAP Riset</span>
          <svg id="risetCaret" class="w-4 h-4 ml-auto transition-transform duration-200"
              viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M6 9l6 6 6-6"/>
          </svg>
        </button>

        <!-- Dropdown Items -->
        <div id="risetMenu" class="ml-3 mt-1 space-y-1 hidden">
          <a href="{{ route('riset.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                    {{ request()->routeIs('sigap-riset.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/>
            </svg>
            Dashboard Riset 
          </a>
          <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-gray-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M3 7h18M3 12h18M3 17h10"/>
            </svg>
            Draft / Antrian (Coming Soon)
          </a>
          <a href="#" class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm hover:bg-gray-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M10.325 4.317l.387-1.934M6 12h12M6 16h12"/>
            </svg>
            Konfigurasi (Coming Soon)
          </a>
        </div>
      @endhasanyrole
      @hasanyrole('admin|employee')
      <!-- SIGAP SERTIFIKAT -->
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP SERTIFIKAT
      </div>

      <!-- Toggle -->
      <button id="sertifikatToggle"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="2"/>
          <path d="M7 8h10M7 12h10M7 16h6" stroke-width="2"/>
        </svg>

        <span class="font-medium">SIGAP Sertifikat</span>

        <svg id="sertifikatCaret"
            class="w-4 h-4 ml-auto transition-transform duration-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
      </button>

      <!-- Dropdown -->
      <div id="sertifikatMenu" class="ml-3 mt-1 space-y-1 hidden">

        <a href="{{ route('sigap-sertifikat.dashboard') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-sertifikat.dashboard') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">

          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/>
          </svg>

          Dashboard Sertifikat
        </a>

      </div>
      @endhasanyrole
      @hasanyrole('admin|employee')
              <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">SIGAP FORMAT</div>

              <!-- Toggle -->
              <button id="formatToggle"
                      class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left"
                      aria-controls="formatMenu" aria-expanded="false">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path d="M6 3h12a2 2 0 0 1 2 2v16H4V5a2 2 0 0 1 2-2Z" fill="none" stroke="currentColor" stroke-width="2" />
                <path d="M8 8h8M8 12h8M8 16h6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
                <span class="font-medium">SIGAP Format</span>
                <svg id="formatCaret" class="w-4 h-4 ml-auto transition-transform duration-200"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-width="2" d="M6 9l6 6 6-6"/>
                </svg>
              </button>

              <!-- Dropdown Items -->
              <div id="formatMenu" class="ml-3 mt-1 space-y-1 hidden">
                <a href="{{ route('format.index') }}"
                  class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
                          {{ request()->routeIs('sigap-format.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M6 3h8l5 5v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Z" fill="none" stroke="currentColor" stroke-width="2"/>
                    <path d="M8 12h8M8 15h8M8 18h6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M14 3v5h5" fill="none" stroke="currentColor" stroke-width="2"/>
                  </svg>
                  Katalog Template
                </a>
              </div>
            @endhasanyrole

          @hasanyrole('admin|verificator_absensi|employee')
        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
          SIGAP ABSENSI
        </div>

        <button id="absensiToggle"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 8v4l3 3"/>
            <circle cx="12" cy="12" r="9" stroke-width="2"/>
          </svg>

          <span class="font-medium">SIGAP Absensi</span>

          <svg id="absensiCaret"
              class="w-4 h-4 ml-auto transition-transform duration-200"
              viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M6 9l6 6 6-6"/>
          </svg>
        </button>

        <div id="absensiMenu" class="ml-3 mt-1 space-y-1 hidden">
          @hasanyrole('employee|admin')
            <a href="{{ route('sigap-absensi.index') }}"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
              {{ request()->routeIs('sigap-absensi.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M12 8v4l3 3"/>
                <circle cx="12" cy="12" r="9" stroke-width="2"/>
              </svg>
              Absen Saya
            </a>
          @endhasanyrole

          @hasanyrole('admin|verificator_absensi')
            <a href="{{ route('sigap-absensi.dashboard') }}"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
              {{ request()->routeIs('sigap-absensi.dashboard') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M3 12h18M6 7h12M8 17h8"/>
              </svg>
              Dashboard Absensi
            </a>

            <a href="{{ route('sigap-absensi.rekap-harian') }}"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
              {{ request()->routeIs('sigap-absensi.rekap-harian') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M4 6h16M4 10h16M4 14h10"/>
              </svg>
              Rekap Harian
            </a>

            <a href="{{ route('sigap-absensi.rekap-bulanan') }}"
              class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
              {{ request()->routeIs('sigap-absensi.rekap-bulanan') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M7 3v4M17 3v4M4 8h16M5 12h4M5 16h4M13 12h4M13 16h4"/>
              </svg>
              Rekap Bulanan
            </a>
          @endhasanyrole
        </div>
      @endhasanyrole
      @hasanyrole('admin|verif_ppd|employee')
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP PPD
      </div>

      <a href="{{ route('sigap-ppd.index') }}"
        class="flex items-center gap-3 px-3 py-2 rounded-lg
                {{ request()->routeIs('sigap-ppd.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M7 3h10v18H7z"/>
          <path stroke-width="2" d="M9 7h6M9 11h6M9 15h4"/>
        </svg>
        SIGAP PPD
      </a>
      @endhasanyrole
    @hasanyrole('admin|verif_magang|magang')
      <!-- SECTION HEADER: SIGAP MAGANG -->
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP MAGANG
      </div>

      <!-- Toggle Button -->
      <button id="magangToggle"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left transition-colors">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/>
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
        </svg>
        <span class="font-medium">SIGAP Magang</span>
        <svg id="magangCaret"
            class="w-4 h-4 ml-auto transition-transform duration-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
      </button>

      <!-- Dropdown Menu Items -->
      <div id="magangMenu" class="ml-3 mt-1 space-y-1 hidden">
        <!-- Menu Utama Batch Magang (Tampil untuk Semua Role Magang/Verif/Admin) -->
        <a href="{{ route('magang.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('magang.index*') || request()->routeIs('magang.batch*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M4 5h16v14H4z"/>
            <path stroke-width="2" d="M8 9h8M8 13h5"/>
          </svg>
          Batch Magang
        </a>

        <!-- Menu Khusus Mahasiswa / Peserta Magang: Logbook Saya -->
        @role('magang')
        <a href="{{ route('magang.logbook.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('magang.logbook.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path stroke-width="2" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Logbook Saya
        </a>
        @endrole

        <!-- Menu Khusus Admin & Verif Magang: Monitoring Magang -->
        @hasanyrole('admin|verif_magang')
        <a href="{{ route('magang.monitoring-logbook') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('magang.monitoring-logbook*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Monitoring Magang
        </a>

        <!-- MENU BARU: Riwayat Magang -->
        <a href="{{ route('magang.riwayat.index') }}" 
          class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-colors {{ request()->routeIs('magang.riwayat*') ? 'bg-maroon text-white' : 'text-gray-700 hover:bg-gray-100' }}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span>Riwayat Magang</span>
        </a>
        @endhasanyrole
      </div>
    @endhasanyrole
@hasanyrole('admin|superadmin|verif_pjlp|pjlp')
      <!-- SECTION HEADER: SIGAP PJLP -->
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP PJLP
      </div>

      <!-- Toggle Button -->
      <button id="pjlpToggle"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left transition-colors">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        <span class="font-medium">SIGAP PJLP</span>
        <svg id="pjlpCaret"
            class="w-4 h-4 ml-auto transition-transform duration-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
      </button>

      <!-- Dropdown Menu Items -->
      <div id="pjlpMenu" class="ml-3 mt-1 space-y-1 hidden">
        <!-- 1. Monitoring Logbook (Admin, Superadmin & Verifikator) -->
        @hasanyrole('admin|superadmin|verif_pjlp')
        <a href="{{ route('sigap-pjlp.monitoring') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-pjlp.monitoring*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
          Monitoring PJLP
        </a>
        @endhasanyrole

        <!-- 2. Logbook Harian (PJLP isi sendiri / Admin & Verif bisa kelola/isikan) -->
        <a href="{{ route('sigap-pjlp.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-pjlp.index*') || request()->routeIs('sigap-pjlp.show*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
            <path stroke-width="2" d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
          </svg>
          Logbook Pekerjaan
        </a>

        <!-- 3. Riwayat / History Logbook -->
        <a href="{{ route('sigap-pjlp.history') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-pjlp.history*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          History Laporan
        </a>
      </div>
      @endhasanyrole
      @hasanyrole('admin|verif_daftarhadir|employee')
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP DAFTAR HADIR
      </div>
      
      {{-- Toggle --}}
      <button id="daftarHadirToggle"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M4 5h16v14H4z"/>
          <path stroke-width="2" d="M8 9h8"/>
          <path stroke-width="2" d="M8 13h5"/>
        </svg>
        <span class="font-medium">SIGAP Daftar Hadir</span>
        <svg id="daftarHadirCaret"
            class="w-4 h-4 ml-auto transition-transform duration-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M6 9l6 6 6-6"/>
        </svg>
      </button>
      
      {{-- Dropdown --}}
      <div id="daftarHadirMenu" class="ml-3 mt-1 space-y-1 hidden">
      
        {{-- Daftar Kegiatan (semua role) --}}
        <a href="{{ route('sigap-daftar-hadir.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-daftar-hadir.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M4 5h16v14H4z"/>
            <path stroke-width="2" d="M8 9h8M8 13h5"/>
          </svg>
          Daftar Kegiatan
        </a>
      
        {{-- Riwayat Peserta — hanya admin --}}
        @hasrole('admin')
        <a href="{{ route('sigap-daftar-hadir.riwayat-peserta') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-daftar-hadir.riwayat-peserta*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4" stroke-width="2"/>
            <path stroke-width="2" d="M16 11l2 2 4-4" stroke-linecap="round"/>
          </svg>
          Riwayat Peserta
        </a>
        @endhasrole
        <a href="{{ route('sigap-narasumber.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm {{ request()->routeIs('sigap-narasumber.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="8.5" cy="7" r="4" stroke-width="2"/>
            <path stroke-width="2" d="M20 8v6M23 11h-6"/>
          </svg>
          Permintaan Kesediaan
        </a>
      </div>
      @endhasanyrole
      @hasanyrole('admin|operator_spj')
      <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">
        SIGAP SPJ
      </div>
      
      <!-- Toggle -->
      <button id="spjToggle"
              class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 text-left">
        <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
          <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="14 2 14 8 20 8"></polyline>
          <line stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x1="16" y1="13" x2="8" y2="13"></line>
          <line stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x1="16" y1="17" x2="8" y2="17"></line>
          <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="10 9 9 9 8 9"></polyline>
        </svg>
        <span class="font-medium">SIGAP SPJ</span>
        <svg id="spjCaret"
            class="w-4 h-4 ml-auto transition-transform duration-200"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6"/>
        </svg>
      </button>
      
      <!-- Dropdown -->
      <div id="spjMenu" class="ml-3 mt-1 space-y-1 hidden">
      
        <!-- Menu Laporan (Semua Role SPJ) -->
        <a href="{{ route('sigap-spj.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-spj.index') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 5h16v14H4z"/>
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 9h8M8 13h5"/>
          </svg>
          Laporan Sub-Kegiatan
        </a>
      
        <!-- Menu Master Struktur (Hanya Admin) -->
        @hasrole('admin')
        <a href="{{ route('sigap-spj.bidang.index') }}"
          class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
          {{ request()->routeIs('sigap-spj.bidang.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
             <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
             <polyline stroke-width="2" stroke-linecap="round" stroke-linejoin="round" points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
             <line stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x1="12" y1="22.08" x2="12" y2="12"></line>
          </svg>
          Struktur Bidang
        </a>
        @endhasrole
      </div>
      @endhasanyrole
        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">PENGATURAN</div>
        @hasrole('admin')
        <a href="{{ route('roles.index') }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('roles.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          Manajemen Role
        </a>
        @endhasrole
      @hasrole('admin')
        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">SISTEM</div>
        <a href="{{ route('admin.logs') }}" 
           class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('admin.logs') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
          </svg>
          Sistem Log
        </a>
      @endhasrole
        {{-- <a href="{{ route('logout') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M10 17l5-5-5-5"/><path stroke-width="2" d="M4 12h11"/></svg>
          Keluar
        </a> --}}
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="w-full text-left flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M10 17l5-5-5-5"/><path stroke-width="2" d="M4 12h11"/></svg>
            Keluar
          </button> 
        </form>
      </nav>
    </aside>

    {{-- main --}}
      <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 justify-between sticky top-0 z-30">
            <div class="flex items-center gap-2">
            <button id="sidebarToggle" class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
                <!-- SEARCH BAR TRIGGER (EYE-CATCHING & RESPONSIVE) -->
                <div x-data class="w-full sm:w-80 md:w-96 lg:w-[28rem] transition-all">
                  <button type="button" 
                          onclick="window.dispatchEvent(new CustomEvent('open-global-search'))"
                          @click="$dispatch('cmd-k')"
                          class="group w-full flex items-center justify-between px-3.5 py-2 rounded-xl bg-gray-100/80 hover:bg-white border border-gray-200/80 hover:border-maroon/40 shadow-sm hover:shadow-md transition-all duration-200 text-left">
                    
                    <!-- Sisi Kiri: Ikon & Teks Placeholder -->
                    <div class="flex items-center gap-2.5 min-w-0">
                      <div class="w-7 h-7 rounded-lg bg-maroon/10 group-hover:bg-maroon text-maroon group-hover:text-white flex items-center justify-center transition-colors shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
                        </svg>
                      </div>
                      
                      <div class="flex flex-col truncate">
                        <span class="text-xs font-bold text-gray-700 group-hover:text-maroon transition-colors truncate">Pencarian Cepat Menu...</span>
                        <span class="text-[10px] text-gray-400 font-medium truncate">Cari SPJ, Logbook, Magang, SKP...</span>
                      </div>
                    </div>

                    <!-- Sisi Kanan: Kategori Badge & Shortcut Ctrl+K -->
                    <div class="flex items-center gap-1.5 shrink-0 pl-2">
                      <span class="hidden md:inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white group-hover:bg-maroon/10 border border-gray-200 group-hover:border-maroon/20 text-[10px] font-extrabold text-gray-500 group-hover:text-maroon transition-colors shadow-2xs">
                        ⌘ K
                      </span>
                    </div>

                  </button>
                </div>
            </div>

          @auth
            @php
              $u = auth()->user();
              // foto dari storage (public) → simpan path di kolom profile_photo_path, contoh: "avatars/abc.jpg"
              $avatarUrl = $u->profile_photo_path
                ? asset('storage/'.$u->profile_photo_path)
                : asset('images/avatar-placeholder.png'); // fallback lokal (pastikan file ada)
              // tampilkan username kalau ada, kalau tidak pakai name
              $displayName = $u->username ?: $u->name;
            @endphp

            <button class="inline-flex items-center gap-2">
              <img class="w-9 h-9 rounded-full object-cover ring-2 ring-maroon/20"
                  src="{{ $avatarUrl }}"
                  alt="{{ $displayName }}"
                  onerror="this.onerror=null;this.src='{{ asset('images/avatar-placeholder.png') }}';">
              <span class="text-sm font-semibold">{{ $displayName }}</span>
            </button>
          @else
            <a href="{{ route('login') }}"
              class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
              Login
            </a>
          @endauth

      </header>
         <main class="p-4 lg:p-6 space-y-6">
    @yield('content')
  </main>
      </div>
  </div>
  @include('partials.flash')
  <script>
    // Dropdown SIGAP INOVASI
    const inovasiToggle = document.getElementById('inovasiToggle');
    const inovasiMenu   = document.getElementById('inovasiMenu');
    const inovasiCaret  = document.getElementById('inovasiCaret');

    // (opsional) restore state dari localStorage
    const INOVASI_KEY = 'sb_inovasi_open';
    const isOpenSaved = localStorage.getItem(INOVASI_KEY) === '1';
    if (isOpenSaved) {
      inovasiMenu.classList.remove('hidden');
      inovasiCaret.classList.add('rotate-180');
    }

    inovasiToggle.addEventListener('click', () => {
      const willOpen = inovasiMenu.classList.contains('hidden');
      inovasiMenu.classList.toggle('hidden');
      inovasiCaret.classList.toggle('rotate-180', willOpen);
      localStorage.setItem(INOVASI_KEY, willOpen ? '1' : '0');
    });
  </script>

  <script>
    // Dropdown SIGAP INOVASI
    const inkubatormaToggle = document.getElementById('inkubatormaToggle');
    const inkubatormaMenu   = document.getElementById('inkubatormaMenu');
    const inkubatormaCaret  = document.getElementById('inkubatormaCaret');

    // // (opsional) restore state dari localStorage
    // const INOVASI_KEY = 'sb_inovasi_open';
    // const isOpenSaved = localStorage.getItem(INOVASI_KEY) === '1';
    // if (isOpenSaved) {
    //   inovasiMenu.classList.remove('hidden');
    //   inovasiCaret.classList.add('rotate-180');
    // }

    inkubatormaToggle.addEventListener('click', () => {
      const willOpen = inkubatormaMenu.classList.contains('hidden');
      inkubatormaMenu.classList.toggle('hidden');
      inkubatormaCaret.classList.toggle('rotate-180', willOpen);
      localStorage.setItem(INOVASI_KEY, willOpen ? '1' : '0');
    });
  </script>

<script>
  // Sidebar toggle (mobile)
  const sidebar  = document.getElementById('sidebar');
  const toggle   = document.getElementById('sidebarToggle');
  const backdrop = document.getElementById('sidebarBackdrop');
  const body     = document.body;

  function openSidebar() {
    // hapus posisi offscreen, tampilkan
    sidebar.classList.remove('translate-x-[-100%]');
    sidebar.classList.add('translate-x-0');
    backdrop.classList.remove('hidden');
    body.classList.add('overflow-hidden');
    // aksesibilitas
    toggle?.setAttribute('aria-expanded', 'true');
  }

  function closeSidebar() {
    sidebar.classList.add('translate-x-[-100%]');
    sidebar.classList.remove('translate-x-0');
    backdrop.classList.add('hidden');
    body.classList.remove('overflow-hidden');
    toggle?.setAttribute('aria-expanded', 'false');
  }

  function isSidebarOpen() {
    return sidebar.classList.contains('translate-x-0');
  }

  // tombol hamburger
  toggle?.addEventListener('click', () => {
    isSidebarOpen() ? closeSidebar() : openSidebar();
  });

  // klik backdrop menutup
  backdrop?.addEventListener('click', closeSidebar);

  // tekan ESC menutup
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isSidebarOpen()) closeSidebar();
  });

  // kalau di desktop (lg:) pastikan backdrop selalu hidden & body bebas scroll
  const mql = window.matchMedia('(min-width: 1024px)');
  mql.addEventListener('change', (ev) => {
    if (ev.matches) { // masuk desktop
      backdrop.classList.add('hidden');
      body.classList.remove('overflow-hidden');
      // biarkan Tailwind lg:translate-x-0 yang tampilkan sidebar
    } else {
      // kembali ke mobile, sembunyikan default
      closeSidebar();
    }
  });
</script>

  <script>
  // Dropdown SIGAP RISET
  const risetToggle = document.getElementById('risetToggle');
  const risetMenu   = document.getElementById('risetMenu');
  const risetCaret  = document.getElementById('risetCaret');

  const RISET_KEY = 'sb_riset_open';
  const isRisetOpenSaved = localStorage.getItem(RISET_KEY) === '1';
  if (isRisetOpenSaved) {
    risetMenu?.classList.remove('hidden');
    risetCaret?.classList.add('rotate-180');
    risetToggle?.setAttribute('aria-expanded', 'true');
  }

  risetToggle?.addEventListener('click', () => {
    const willOpen = risetMenu.classList.contains('hidden');
    risetMenu.classList.toggle('hidden');
    risetCaret.classList.toggle('rotate-180', willOpen);
    risetToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    localStorage.setItem(RISET_KEY, willOpen ? '1' : '0');
  });
</script>

<script>
// Dropdown SIGAP FORMAT
const formatToggle = document.getElementById('formatToggle');
const formatMenu   = document.getElementById('formatMenu');
const formatCaret  = document.getElementById('formatCaret');

const FORMAT_KEY = 'sb_format_open';
const isFormatOpenSaved = localStorage.getItem(FORMAT_KEY) === '1';
if (isFormatOpenSaved) {
  formatMenu?.classList.remove('hidden');
  formatCaret?.classList.add('rotate-180');
  formatToggle?.setAttribute('aria-expanded', 'true');
}

formatToggle?.addEventListener('click', () => {
  const willOpen = formatMenu.classList.contains('hidden');
  formatMenu.classList.toggle('hidden');
  formatCaret.classList.toggle('rotate-180', willOpen);
  formatToggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
  localStorage.setItem(FORMAT_KEY, willOpen ? '1' : '0');
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function(){

  const sertifikatToggle = document.getElementById('sertifikatToggle');
  const sertifikatMenu   = document.getElementById('sertifikatMenu');
  const sertifikatCaret  = document.getElementById('sertifikatCaret');

  const SERTIFIKAT_KEY = 'sb_sertifikat_open';

  const isOpenSaved = localStorage.getItem(SERTIFIKAT_KEY) === '1';

  if (isOpenSaved && sertifikatMenu && sertifikatCaret) {
    sertifikatMenu.classList.remove('hidden');
    sertifikatCaret.classList.add('rotate-180');
  }

  sertifikatToggle?.addEventListener('click', () => {

    const willOpen = sertifikatMenu.classList.contains('hidden');

    sertifikatMenu.classList.toggle('hidden');
    sertifikatCaret.classList.toggle('rotate-180', willOpen);

    localStorage.setItem(SERTIFIKAT_KEY, willOpen ? '1' : '0');

  });

});
</script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const absensiToggle = document.getElementById('absensiToggle');
    const absensiMenu   = document.getElementById('absensiMenu');
    const absensiCaret  = document.getElementById('absensiCaret');

    const ABSENSI_KEY = 'sb_absensi_open';
    const isOpenSaved = localStorage.getItem(ABSENSI_KEY) === '1';

    if (isOpenSaved && absensiMenu && absensiCaret) {
      absensiMenu.classList.remove('hidden');
      absensiCaret.classList.add('rotate-180');
    }

    absensiToggle?.addEventListener('click', () => {
      const willOpen = absensiMenu.classList.contains('hidden');
      absensiMenu.classList.toggle('hidden');
      absensiCaret.classList.toggle('rotate-180', willOpen);
      localStorage.setItem(ABSENSI_KEY, willOpen ? '1' : '0');
    });
  });
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const daftarHadirToggle = document.getElementById('daftarHadirToggle');
  const daftarHadirMenu   = document.getElementById('daftarHadirMenu');
  const daftarHadirCaret  = document.getElementById('daftarHadirCaret');
 
  if (!daftarHadirToggle) return;
 
  const DH_KEY     = 'sb_daftar_hadir_open';
  const isOpenSaved = localStorage.getItem(DH_KEY) === '1';
 
  // Auto-buka jika sedang di halaman daftar hadir
  const isOnDaftarHadir = window.location.pathname.includes('/sigap-daftar-hadir');
 
  if (isOpenSaved || isOnDaftarHadir) {
    daftarHadirMenu.classList.remove('hidden');
    daftarHadirCaret.classList.add('rotate-180');
  }
 
  daftarHadirToggle.addEventListener('click', () => {
    const willOpen = daftarHadirMenu.classList.contains('hidden');
    daftarHadirMenu.classList.toggle('hidden');
    daftarHadirCaret.classList.toggle('rotate-180', willOpen);
    localStorage.setItem(DH_KEY, willOpen ? '1' : '0');
  });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const spjToggle = document.getElementById('spjToggle');
  const spjMenu   = document.getElementById('spjMenu');
  const spjCaret  = document.getElementById('spjCaret');

  if (!spjToggle) return;

  const SPJ_KEY   = 'sb_spj_open';
  const isOpenSaved = localStorage.getItem(SPJ_KEY) === '1';

  // Auto-buka jika sedang di URL SPJ
  const isOnSPJ = window.location.pathname.includes('/sigap-spj');

  if (isOpenSaved || isOnSPJ) {
    spjMenu.classList.remove('hidden');
    spjCaret.classList.add('rotate-180');
  }

  spjToggle.addEventListener('click', () => {
    const willOpen = spjMenu.classList.contains('hidden');
    spjMenu.classList.toggle('hidden');
    spjCaret.classList.toggle('rotate-180', willOpen);
    localStorage.setItem(SPJ_KEY, willOpen ? '1' : '0');
  });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const skpToggle = document.getElementById('skpToggle');
  const skpMenu   = document.getElementById('skpMenu');
  const skpCaret  = document.getElementById('skpCaret');
 
  if (!skpToggle) return;
 
  const SKP_KEY     = 'sb_skp_open';
  const isOpenSaved = localStorage.getItem(SKP_KEY) === '1';
 
  // Auto-buka jika sedang di halaman SKP
  const isOnSKP = window.location.pathname.includes('/sigap-skp');
 
  if (isOpenSaved || isOnSKP) {
    skpMenu.classList.remove('hidden');
    skpCaret.classList.add('rotate-180');
  }
 
  skpToggle.addEventListener('click', () => {
    const willOpen = skpMenu.classList.contains('hidden');
    skpMenu.classList.toggle('hidden');
    skpCaret.classList.toggle('rotate-180', willOpen);
    localStorage.setItem(SKP_KEY, willOpen ? '1' : '0');
  });
});
</script>
 
    @stack('scripts')

    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('Service Worker terdaftar:', reg))
            .catch(err => console.log('Service Worker gagal:', err));
        });
      }
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const magangToggle = document.getElementById('magangToggle');
  const magangMenu   = document.getElementById('magangMenu');
  const magangCaret  = document.getElementById('magangCaret');

  if (!magangToggle) return;

  const MAGANG_KEY  = 'sb_magang_open';
  const isOpenSaved = localStorage.getItem(MAGANG_KEY) === '1';

  const isOnMagang = window.location.pathname.includes('/dashboard/magang');

  if (isOpenSaved || isOnMagang) {
    magangMenu.classList.remove('hidden');
    magangCaret.classList.add('rotate-180');
  }

  magangToggle.addEventListener('click', () => {
    const willOpen = magangMenu.classList.contains('hidden');
    magangMenu.classList.toggle('hidden');
    magangCaret.classList.toggle('rotate-180', willOpen);
    localStorage.setItem(MAGANG_KEY, willOpen ? '1' : '0');
  });
});
</script>
<div x-data="globalSearch()" 
         @keydown.window.prevent.cmd.k="openSearch()"
         @keydown.window.prevent.ctrl.k="openSearch()"
         x-show="isOpen" 
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-start justify-center pt-16 sm:pt-24 p-4">
      
      <div @click.away="closeSearch()" 
           class="relative w-full max-w-xl rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
        
        <!-- Input Search Bar -->
        <div class="flex items-center px-4 border-b border-gray-100 bg-gray-50/50">
          <template x-if="selectedParent">
            <button type="button" @click="resetToParentList()" class="mr-2 p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition-colors" title="Kembali ke pencarian awal">
              ←
            </button>
          </template>

          <span class="text-gray-400 font-bold text-lg">🔍</span>
          
          <input type="text" 
                 x-ref="searchInput"
                 x-model="searchQuery" 
                 @keydown.arrow-down.prevent="navigateResults('down')"
                 @keydown.arrow-up.prevent="navigateResults('up')"
                 @keydown.enter.prevent="selectCurrentResult()"
                 @keydown.escape="handleEscape()"
                 :placeholder="selectedParent ? 'Pilih sub-menu ' + selectedParent.title + '...' : 'Cari menu (misal: SIGAP SPJ, Logbook, Magang)...'"
                 class="w-full bg-transparent border-0 py-4 pl-3 pr-4 text-sm font-medium text-gray-800 focus:ring-0 focus:outline-none placeholder-gray-400">
                 
          <span class="text-[10px] font-extrabold text-gray-400 bg-gray-200/60 px-2 py-1 rounded-md">ESC</span>
        </div>

        <!-- Breadcrumb Indicator jika Parent Terpilih -->
        <template x-if="selectedParent">
          <div class="px-4 py-2 bg-maroon/5 border-b border-maroon/10 flex items-center justify-between text-xs font-semibold text-maroon">
            <span>Kategori Terpilih: <strong x-text="selectedParent.title"></strong></span>
            <button type="button" @click="resetToParentList()" class="hover:underline text-[11px]">Ganti Menu</button>
          </div>
        </template>

        <!-- Results List -->
        <div class="max-h-80 overflow-y-auto p-2 space-y-1">
          
          <!-- Mode 1: Pencarian Utama -->
          <template x-if="!selectedParent">
            <div>
              <template x-for="(item, index) in filteredResults" :key="item.id">
                <div @click="handleItemClick(item)"
                     @mouseenter="selectedIndex = index"
                     :class="{ 'bg-maroon text-white': selectedIndex === index, 'text-gray-700 hover:bg-gray-100': selectedIndex !== index }"
                     class="flex items-center justify-between px-3.5 py-2.5 rounded-xl cursor-pointer text-xs font-semibold transition-colors">
                  
                  <div class="flex items-center gap-2.5">
                    <span x-text="item.icon" class="text-base"></span>
                    <div>
                      <p x-text="item.title" class="font-bold"></p>
                      <p x-text="item.description" :class="selectedIndex === index ? 'text-white/80' : 'text-gray-400'" class="text-[11px] font-normal"></p>
                    </div>
                  </div>

                  <template x-if="item.isParent">
                    <span :class="selectedIndex === index ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900 border border-amber-200'" 
                          class="px-2 py-0.5 rounded-md text-[10px] font-extrabold flex items-center gap-1">
                      <span x-text="item.subMenus ? item.subMenus.length + ' Sub-Menu' : 'Sub-Menu'"></span>
                      <span>↵</span>
                    </span>
                  </template>
                </div>
              </template>

              <template x-if="filteredResults.length === 0">
                <div class="py-8 text-center text-xs text-gray-400">
                  Menu atau kata kunci tidak ditemukan.
                </div>
              </template>
            </div>
          </template>

          <!-- Mode 2: Tampilan Sub-Menu dari Parent -->
          <template x-if="selectedParent">
            <div>
              <template x-for="(sub, index) in filteredSubMenus" :key="sub.url">
                <div @click="goToUrl(sub.url)"
                     @mouseenter="selectedIndex = index"
                     :class="{ 'bg-maroon text-white': selectedIndex === index, 'text-gray-700 hover:bg-gray-100': selectedIndex !== index }"
                     class="flex items-center justify-between px-3.5 py-2.5 rounded-xl cursor-pointer text-xs font-semibold transition-colors">
                  
                  <div class="flex items-center gap-2.5">
                    <span x-text="sub.icon" class="text-base"></span>
                    <div>
                      <p x-text="sub.title" class="font-bold"></p>
                      <p x-text="sub.description" :class="selectedIndex === index ? 'text-white/80' : 'text-gray-400'" class="text-[11px] font-normal"></p>
                    </div>
                  </div>

                  <span :class="selectedIndex === index ? 'text-white' : 'text-gray-400'" class="text-xs">Buka →</span>
                </div>
              </template>
            </div>
          </template>

        </div>

        <!-- Footer Help Bar -->
        <div class="px-4 py-2.5 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
          <div class="flex items-center gap-3">
            <span><kbd class="font-semibold bg-gray-200 px-1 rounded">↑↓</kbd> Navigasi</span>
            <span><kbd class="font-semibold bg-gray-200 px-1 rounded">↵</kbd> Pilih</span>
          </div>
          <span>SIGAP Navigasi Cepat</span>
        </div>

      </div>
    </div>

<!-- SCRIPT ALPINE JS FOR GLOBAL SEARCH -->
<script>
function globalSearch() {
  return {
    isOpen: false,
    searchQuery: '',
    selectedIndex: 0,
    selectedParent: null,

    init() {
      window.addEventListener('open-global-search', () => this.openSearch());
      window.addEventListener('cmd-k', () => this.openSearch());
    },

    // DAFTAR MENU DENGAN FILTER ROLE DINAMIS
    menuTree: [
      @auth
        @php
          $u = auth()->user();
          $isAdmin = $u->hasRole('admin');
        @endphp

        // 1. SIGAP MAGANG
        @if($isAdmin || $u->hasAnyRole(['verif_magang', 'magang']))
        {
          id: 'magang-group',
          title: 'SIGAP Magang',
          description: 'Pendaftaran, Logbook, Tes Ketik & Monitoring Magang',
          icon: '🎓',
          isParent: true,
          subMenus: [
            { title: 'Batch Magang', description: 'Informasi umum & pendaftaran batch', url: "{{ route('magang.index') }}", icon: '🏠' },
            @if($isAdmin || $u->hasRole('magang'))
            { title: 'Logbook Saya', description: 'Pelaporan harian & evaluasi ketik 10 jari', url: "{{ route('magang.logbook.index') }}", icon: '📝' },
            @endif
            @if($isAdmin || $u->hasRole('verif_magang'))
            { title: 'Monitoring Magang', description: 'Pantau keaktifan harian & izin susulan', url: "{{ route('magang.monitoring-logbook') }}", icon: '📋' },
            { title: 'Riwayat Magang', description: 'Arsip batch & dokumen lulusan magang', url: "{{ route('magang.riwayat.index') }}", icon: '📁' },
            @endif
          ]
        },
        @endif
        // SIGAP PJLP
        @if($isAdmin || $u->hasAnyRole(['superadmin', 'verif_pjlp', 'pjlp']))
        {
          id: 'pjlp-group',
          title: 'SIGAP PJLP',
          description: 'Logbook Harian, Evidence Kebersihan & Monitoring PJLP',
          icon: '🧹',
          isParent: true,
          subMenus: [
            @if($isAdmin || $u->hasAnyRole(['superadmin', 'verif_pjlp']))
            { title: 'Monitoring PJLP', description: 'Pantau progress & verifikasi logbook PJLP', url: "{{ route('sigap-pjlp.monitoring') }}", icon: '📋' },
            @endif
            { title: 'Logbook Pekerjaan', description: 'Input & kelola evidence pekerjaan harian', url: "{{ route('sigap-pjlp.index') }}", icon: '📝' },
            { title: 'History Laporan', description: 'Arsip logbook & dokumen gaji bulanan', url: "{{ route('sigap-pjlp.history') }}", icon: '📁' },
          ]
        },
        @endif

        // 2. SIGAP PPD
        @if($isAdmin || $u->hasAnyRole(['verif_ppd', 'employee']))
        {
          id: 'ppd-single',
          title: 'SIGAP PPD',
          description: 'Perencanaan, Pengendalian, dan Evaluasi Pembangunan Daerah',
          icon: '📑',
          isParent: false,
          url: "{{ route('sigap-ppd.index') }}"
        },
        @endif

        // 3. SIGAP SPJ
        @if($isAdmin || $u->hasRole('operator_spj'))
        {
          id: 'spj-group',
          title: 'SIGAP SPJ',
          description: 'Kelola SPJ, Laporan Sub-Kegiatan & Struktur Bidang',
          icon: '📄',
          isParent: true,
          subMenus: [
            { title: 'Laporan Sub-Kegiatan', description: 'Kelola draf & berkas SPJ kegiatan', url: "{{ route('sigap-spj.index') }}", icon: '📝' },
            @if($isAdmin)
            { title: 'Struktur Bidang', description: 'Pengaturan bidang & master SPJ', url: "{{ route('sigap-spj.bidang.index') }}", icon: '🏗️' },
            @endif
          ]
        },
        @endif

        // 4. SIGAP SKP
        @if($isAdmin || $u->hasAnyRole(['verif_skp', 'employee']))
        {
          id: 'skp-group',
          title: 'SIGAP SKP',
          description: 'Sasaran Kinerja Pegawai Umum, Pribadi & Monitoring',
          icon: '📊',
          isParent: true,
          subMenus: [
            { title: 'SKP Umum', description: 'Daftar sasaran kinerja umum pegawai', url: "{{ route('sigap-skp.index') }}", icon: '👥' },
            { title: 'SKP Pribadi', description: 'Sasaran kinerja individu Anda', url: "{{ route('sigap-skp.pribadi') }}", icon: '👤' },
            { title: 'Monitoring SKP', description: 'Pantau capaian SKP berkala', url: "{{ route('sigap-skp.monitoring') }}", icon: '📈' },
          ]
        },
        @endif

        // 5. SIGAP ABSENSI
        @if($isAdmin || $u->hasAnyRole(['verificator_absensi', 'employee']))
        {
          id: 'absensi-group',
          title: 'SIGAP Absensi',
          description: 'Pencatatan Kehadiran, Rekap Harian & Rekap Bulanan',
          icon: '⏰',
          isParent: true,
          subMenus: [
            @if($isAdmin || $u->hasRole('employee'))
            { title: 'Absen Saya', description: 'Pencatatan hadir & pulang harian', url: "{{ route('sigap-absensi.index') }}", icon: '📌' },
            @endif
            @if($isAdmin || $u->hasRole('verificator_absensi'))
            { title: 'Dashboard Absensi', description: 'Ringkasan presensi pegawai', url: "{{ route('sigap-absensi.dashboard') }}", icon: '🖥️' },
            { title: 'Rekap Harian', description: 'Laporan rekapitulasi harian', url: "{{ route('sigap-absensi.rekap-harian') }}", icon: '📅' },
            { title: 'Rekap Bulanan', description: 'Laporan rekapitulasi bulanan', url: "{{ route('sigap-absensi.rekap-bulanan') }}", icon: '📆' },
            @endif
          ]
        },
        @endif

        // 6. SIGAP INOVASI
        @if($isAdmin || $u->hasRole('inovator'))
        {
          id: 'inovasi-group',
          title: 'SIGAP Inovasi',
          description: 'Dashboard Inovasi, Inovasi Daerah & IGA BSKDN',
          icon: '💡',
          isParent: true,
          subMenus: [
            { title: 'Dashboard Inovasi', description: 'Ringkasan capaian inovasi daerah', url: "{{ route('sigap-inovasi.dashboard') }}", icon: '🏠' },
            { title: 'Inovasi Daerah', description: 'Katalog & pengusulan inovasi daerah', url: "{{ route('sigap-inovasi.index') }}", icon: '🏛️' },
            { title: 'Akun IGA BSKDN', description: 'Pengelolaan akun IGA Kemendagri', url: "{{ route('sigap-iga.index') }}", icon: '🛡️' },
            { title: 'Konfigurasi Inovasi', description: 'Pengaturan kriteria inovasi', url: "{{ route('sigap-inovasi.konfigurasi') }}", icon: '⚙️' },
          ]
        },
        @endif

        // 7. SIGAP DAFTAR HADIR
        @if($isAdmin || $u->hasAnyRole(['verif_daftarhadir', 'employee']))
        {
          id: 'daftar-hadir-group',
          title: 'SIGAP Daftar Hadir',
          description: 'Daftar Kegiatan, Riwayat Peserta & Kesediaan Narasumber',
          icon: '📋',
          isParent: true,
          subMenus: [
            { title: 'Daftar Kegiatan', description: 'Presensi acara & rapat dinas', url: "{{ route('sigap-daftar-hadir.index') }}", icon: '📅' },
            @if($isAdmin)
            { title: 'Riwayat Peserta', description: 'Rekap kehadiran peserta rapat', url: "{{ route('sigap-daftar-hadir.riwayat-peserta') }}", icon: '👥' },
            @endif
            { title: 'Permintaan Kesediaan Narasumber', description: 'Pengelolaan narasumber kegiatan', url: "{{ route('sigap-narasumber.index') }}", icon: '🎙️' },
          ]
        },
        @endif

        // 8. SIGAP INKUBATORMA
        @if($isAdmin || $u->hasAnyRole(['verifikator_inkubatorma', 'user']))
        {
          id: 'inkubatorma-group',
          title: 'SIGAP Inkubatorma',
          description: 'Inkubator Bisnis & Pengembangan Inovasi Masyarakat',
          icon: '🚀',
          isParent: true,
          subMenus: [
            { title: 'Dashboard Inkubatorma', description: 'Ringkasan tenant & inkubasi', url: "{{ route('sigap-inkubatorma.dashboard') }}", icon: '📊' },
          ]
        },
        @endif

        // 9. SIGAP RISET
        @if($isAdmin || $u->hasRole('researcher'))
        {
          id: 'riset-group',
          title: 'SIGAP Riset',
          description: 'Kajian, Penelitian & Kelayakan Daerah',
          icon: '🔬',
          isParent: true,
          subMenus: [
            { title: 'Dashboard Riset', description: 'Portofolio penelitian daerah', url: "{{ route('riset.index') }}", icon: '📚' },
          ]
        },
        @endif

        // 10. SIGAP SERTIFIKAT & FORMAT
        @if($isAdmin || $u->hasRole('employee'))
        {
          id: 'sertifikat-group',
          title: 'SIGAP Sertifikat',
          description: 'Generasi & Verifikasi Sertifikat Digital',
          icon: '📜',
          isParent: true,
          subMenus: [
            { title: 'Dashboard Sertifikat', description: 'Katalog & pembuatan sertifikat', url: "{{ route('sigap-sertifikat.dashboard') }}", icon: '🏆' },
          ]
        },
        {
          id: 'format-group',
          title: 'SIGAP Format',
          description: 'Template Dokumen Resmi & Format Persuratan',
          icon: '📂',
          isParent: true,
          subMenus: [
            { title: 'Katalog Template Format', description: 'Unduh template dokumen resmi', url: "{{ route('format.index') }}", icon: '📁' },
          ]
        },
        {
          id: 'single-dokumen',
          title: 'SIGAP Dokumen',
          description: 'Pencarian & pengelolaan dokumen resmi',
          icon: '🔍',
          isParent: false,
          url: "{{ route('sigap-dokumen.index') }}"
        },
        @endif

        // 11. MENU UMUM (DAPAT DIAKSES SEMUA USER LOGGED IN)
        {
          id: 'single-agenda',
          title: 'Agenda Kegiatan',
          description: 'Jadwal & kalender agenda dinas kantor',
          icon: '📅',
          isParent: false,
          url: "{{ route('sigap-agenda.index') }}"
        },
        {
          id: 'single-profil',
          title: 'Profil Pegawai Saya',
          description: 'Pengaturan data diri & foto profil',
          icon: '👤',
          isParent: false,
          url: "{{ route('pegawai.profil') }}"
        },

        // 12. KHUSUS ADMIN
        @if($isAdmin)
        {
          id: 'single-pegawai',
          title: 'SIGAP Pegawai',
          description: 'Manajemen Data & Informasi Pegawai BRIDA',
          icon: '👥',
          isParent: false,
          url: "{{ route('sigap-pegawai.index') }}"
        },
        {
          id: 'single-pic',
          title: 'SIGAP PIC',
          description: 'Penanggung Jawab & Person in Charge Kegiatan',
          icon: '📌',
          isParent: false,
          url: "{{ route('sigap-pic.index') }}"
        },
        {
          id: 'single-roles',
          title: 'Manajemen Role & Akses',
          description: 'Pengaturan hak akses pengguna',
          icon: '🛡️',
          isParent: false,
          url: "{{ route('roles.index') }}"
        },
        {
          id: 'single-logs',
          title: 'Sistem Log',
          description: 'Catatan aktivitas sistem & audit trail',
          icon: '🖥️',
          isParent: false,
          url: "{{ route('admin.logs') }}"
        },
        @endif
      @endauth
    ],

    get filteredResults() {
      if (!this.searchQuery.trim()) return this.menuTree;
      const q = this.searchQuery.toLowerCase();
      return this.menuTree.filter(item => {
        const matchTitle = item.title.toLowerCase().includes(q);
        const matchDesc = item.description.toLowerCase().includes(q);
        const matchSub = item.subMenus && item.subMenus.some(sub => sub.title.toLowerCase().includes(q));
        return matchTitle || matchDesc || matchSub;
      });
    },

    get filteredSubMenus() {
      if (!this.selectedParent) return [];
      if (!this.searchQuery.trim()) return this.selectedParent.subMenus;
      const q = this.searchQuery.toLowerCase();
      return this.selectedParent.subMenus.filter(sub => 
        sub.title.toLowerCase().includes(q) || sub.description.toLowerCase().includes(q)
      );
    },

    openSearch() {
      this.isOpen = true;
      this.searchQuery = '';
      this.selectedParent = null;
      this.selectedIndex = 0;
      this.$nextTick(() => {
        if (this.$refs.searchInput) this.$refs.searchInput.focus();
      });
    },

    closeSearch() {
      this.isOpen = false;
    },

    handleEscape() {
      if (this.selectedParent) {
        this.resetToParentList();
      } else {
        this.closeSearch();
      }
    },

    handleItemClick(item) {
      if (item.isParent) {
        this.selectedParent = item;
        this.searchQuery = '';
        this.selectedIndex = 0;
        this.$nextTick(() => {
          if (this.$refs.searchInput) this.$refs.searchInput.focus();
        });
      } else {
        this.goToUrl(item.url);
      }
    },

    selectCurrentResult() {
      if (!this.selectedParent) {
        const current = this.filteredResults[this.selectedIndex];
        if (current) this.handleItemClick(current);
      } else {
        const currentSub = this.filteredSubMenus[this.selectedIndex];
        if (currentSub) this.goToUrl(currentSub.url);
      }
    },

    resetToParentList() {
      this.selectedParent = null;
      this.searchQuery = '';
      this.selectedIndex = 0;
      this.$nextTick(() => {
        if (this.$refs.searchInput) this.$refs.searchInput.focus();
      });
    },

    navigateResults(direction) {
      const max = this.selectedParent ? this.filteredSubMenus.length : this.filteredResults.length;
      if (max === 0) return;
      if (direction === 'down') {
        this.selectedIndex = (this.selectedIndex + 1) % max;
      } else if (direction === 'up') {
        this.selectedIndex = (this.selectedIndex - 1 + max) % max;
      }
    },

    goToUrl(url) {
      this.closeSearch();
      if (url && url !== '#') {
        window.location.href = url;
      }
    }
  }
}
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const pjlpToggle = document.getElementById('pjlpToggle');
  const pjlpMenu   = document.getElementById('pjlpMenu');
  const pjlpCaret  = document.getElementById('pjlpCaret');

  if (!pjlpToggle) return;

  const PJLP_KEY  = 'sb_pjlp_open';
  const isOpenSaved = localStorage.getItem(PJLP_KEY) === '1';

  // Auto-buka jika sedang berada di route SIGAP PJLP
  const isOnPjlp = window.location.pathname.includes('/sigap-pjlp');

  if (isOpenSaved || isOnPjlp) {
    pjlpMenu?.classList.remove('hidden');
    pjlpCaret?.classList.add('rotate-180');
  }

  pjlpToggle.addEventListener('click', () => {
    const willOpen = pjlpMenu.classList.contains('hidden');
    pjlpMenu.classList.toggle('hidden');
    pjlpCaret.classList.toggle('rotate-180', willOpen);
    localStorage.setItem(PJLP_KEY, willOpen ? '1' : '0');
  });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const kinerjaToggle = document.getElementById('kinerjaToggle');
  const kinerjaMenu   = document.getElementById('kinerjaMenu');
  const kinerjaCaret  = document.getElementById('kinerjaCaret');

  if (!kinerjaToggle) return;

  const KINERJA_KEY  = 'sb_kinerja_open';
  const isOpenSaved  = localStorage.getItem(KINERJA_KEY) === '1';

  // Auto-buka jika sedang berada di halaman Kinerja atau SIGAP Story
  const isOnKinerja = window.location.pathname.includes('/sigap-kinerja') || window.location.pathname.includes('/sigap-story');

  if (isOpenSaved || isOnKinerja) {
    kinerjaMenu?.classList.remove('hidden');
    kinerjaCaret?.classList.add('rotate-180');
  }

  kinerjaToggle.addEventListener('click', () => {
    const willOpen = kinerjaMenu.classList.contains('hidden');
    kinerjaMenu.classList.toggle('hidden');
    kinerjaCaret.classList.toggle('rotate-180', willOpen);
    localStorage.setItem(KINERJA_KEY, willOpen ? '1' : '0');
  });
});
</script>
</body>
</html>