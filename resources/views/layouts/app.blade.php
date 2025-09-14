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
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
    .scrollbar-thin::-webkit-scrollbar{height:6px;width:6px}
    .scrollbar-thin::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}
  </style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Chart.js -->
  {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
@stack('head')
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Layout -->
  <div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed lg:sticky top-0 h-screen w-72 translate-x-[-100%] lg:translate-x-0 bg-white border-r border-gray-200 z-40 transition-transform">
      <div class="h-16 px-4 border-b border-gray-200 flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500">Admin Panel</p>
        </div>
      </div>

      <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] scrollbar-thin">
        <a href="{{ route('home.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-maroon text-white">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/></svg>
          Dashboard
        </a>
        <a href="{{ route('sigap-dokumen.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
          SIGAP Dokumen
        </a>
        <a href="{{ route('sigap-pegawai.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          SIGAP Pegawai
        </a>
        <a href="pegawai-profil.html" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M4 21v-3.5A4.5 4.5 0 0 1 8.5 13h7A4.5 4.5 0 0 1 20 17.5V21"/><circle cx="12" cy="7" r="4"/></svg>
          Profil Pegawai
        </a>
        <a href="permintaan-akses.html" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M4 6h16M4 10h16M4 14h10"/></svg>
          Permintaan Akses
          <span class="ml-auto text-[11px] px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">5</span>
        </a>
        <a href="log-aktivitas.html" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2a10 10 0 0 0-7 17l-1 4 4-1A10 10 0 1 0 12 2z"/></svg>
          Log Aktivitas
        </a>

        <div class="pt-3 mt-3 border-t border-gray-200 text-xs text-gray-500 px-3">PENGATURAN</div>
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
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 10l9-7 9 7v8a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/></svg>
            Dashboard 
          </a>
          <a href="{{ route('sigap-inovasi.index') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
          </svg>
          Inovasi Daerah
          </a>
          <a href="{{ route('sigap-inovasi.konfigurasi') }}"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 text-sm">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M10.325 4.317l.387-1.934a1 1 0 0 1 .976-.8h1.624a1 1 0 0 1 .976.8l.387 1.934a1 1 0 0 0 .725.725l1.934.387a1 1 0 0 1 .8.976v1.624a1 1 0 0 1-.8.976l-1.934.387a1 1 0 0 0-.725.725l-.387 1.934a1 1 0 0 1-.976.8h-1.624a1 1 0 0 1-.976-.8l-.387-1.934a1 1 0 0 0-.725-.725l-1.934-.387a1 1 0 0 1-.8-.976V6.005a1 1 0 0 1 .8-.976l1.934-.387a1 1 0 0 0 .725-.325z"/>
              <circle cx="12" cy="12" r="3" stroke-width="2"/>
            </svg>
            Konfigurasi
          </a>
        </div>
      @endhasanyrole
      </nav>
    </aside>

    {{-- main --}}
      <div class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 justify-between sticky top-0 z-30">
            <div class="flex items-center gap-2">
            <button id="sidebarToggle" class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-300">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="relative">
                <input class="pl-9 pr-3 py-2 rounded-lg border border-gray-300 focus:ring-maroon focus:border-maroon w-[56vw] max-w-md" placeholder="Cari cepat (dokumen, pegawai, alias…)" />
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
            </div>

            <div class="flex items-center gap-3">
            <button class="relative inline-flex items-center justify-center w-9 h-9 rounded-full border border-gray-300">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M15 17h5l-1.4-1.4A8 8 0 1 0 5 12"/></svg>
                <span class="absolute -top-1 -right-1 text-[10px] bg-rose-500 text-white rounded-full px-1.5">3</span>
            </button>
            <div class="h-8 w-px bg-gray-200"></div>
            <button class="inline-flex items-center gap-2">
                <img class="w-9 h-9 rounded-full object-cover ring-2 ring-maroon/20" src="https://images.unsplash.com/photo-1544005311-94ddf0286df2?q=80&w=200&auto=format&fit=crop" alt="">
                <span class="text-sm font-semibold">admin.sekretariat</span>
            </button>
            </div>
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
    // Sidebar toggle (mobile)
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (toggle) {
      toggle.addEventListener('click', () => {
        const opened = !sidebar.classList.contains('translate-x-0');
        sidebar.classList.toggle('-translate-x-[100%]', opened);
        sidebar.classList.toggle('translate-x-0', !opened);
      });
    }

    // Charts

  </script>
    @stack('scripts')
</body>
</html>
