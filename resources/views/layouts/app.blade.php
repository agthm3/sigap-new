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
    <aside id="sidebar"  class="fixed lg:sticky top-0 h-screen w-72 translate-x-[-100%] lg:translate-x-0 bg-white border-r border-gray-200 z-40 transition-transform duration-200">
      <div class="h-16 px-4 border-b border-gray-200 flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500">Admin Panel</p>
        </div>
      </div>

      <nav class="p-3 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] scrollbar-thin">
        @hasrole('admin')
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
        @hasrole('admin')
            <a href="{{ route('sigap-pegawai.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg {{ request()->routeIs('sigap-pegawai.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              SIGAP Pegawai
            </a>
        @endhasrole
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
        <a href="{{ route('sigap-kinerja.index') }}"
          class="flex items-center gap-3 px-3 py-2 rounded-lg
                {{ request()->routeIs('sigap-kinerja.*') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-width="2" d="M3 7h18M3 12h18M3 17h10"/>
          </svg>
          Kinerja
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


    @stack('scripts')
</body>
</html>
