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

  <!-- Chart.js -->
  {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
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
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-maroon text-white">
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
        <a href="login.html" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M10 17l5-5-5-5"/><path stroke-width="2" d="M4 12h11"/></svg>
          Keluar
        </a>
      </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-w-0">

      <!-- Topbar -->
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

      <!-- Content -->
      <main class="p-4 lg:p-6 space-y-6">

        <!-- Greeting + Quick Actions -->
        <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Selamat datang, <span class="text-maroon">Admin</span> 👋</h1>
            <p class="text-sm text-gray-600 mt-0.5">Ringkasan aktivitas sistem SIGAP BRIDA hari ini.</p>
          </div>
          <div class="flex flex-wrap gap-2">
            <a href="pegawai-detail.html" class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm transition">Tambah Pegawai</a>
            <a href="hasil.html" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm transition">Unggah Dokumen</a>
            <a href="#" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Buat Kategori</a>
          </div>
        </section>

        <!-- KPI Cards -->
        <section class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Total Dokumen</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">8.214</p>
            <p class="text-xs text-emerald-600 mt-1">+3.2% minggu ini</p>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Dokumen Privasi</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">1.982</p>
            <p class="text-xs text-amber-600 mt-1">Akses Terkendali</p>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Permintaan Akses Pending</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">5</p>
            <div class="mt-1 flex -space-x-2">
              <span class="w-6 h-6 rounded-full bg-amber-100 border border-white"></span>
              <span class="w-6 h-6 rounded-full bg-amber-100 border border-white"></span>
              <span class="w-6 h-6 rounded-full bg-amber-100 border border-white"></span>
            </div>
          </div>
          <div class="rounded-xl border bg-white p-4">
            <p class="text-xs text-gray-500">Unduhan 7 hari</p>
            <p class="mt-1 text-2xl font-extrabold text-maroon">642</p>
            <p class="text-xs text-gray-500 mt-1">+124 unduhan</p>
          </div>
        </section>

        <!-- Charts -->
        {{-- <section class="grid lg:grid-cols-3 gap-4">
          <div class="lg:col-span-2 rounded-xl border bg-white p-4">
            <div class="flex items-center justify-between mb-3">
              <h3 class="font-semibold text-gray-800">Aktivitas Unduh & Lihat (30 hari)</h3>
              <select id="range" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
                <option>30 hari</option>
                <option>7 hari</option>
                <option>24 jam</option>
              </select>
            </div>
            <canvas id="lineChart" height="110"></canvas>
          </div>

          <div class="rounded-xl border bg-white p-4">
            <h3 class="font-semibold text-gray-800 mb-3">Komposisi Dokumen</h3>
            <canvas id="pieChart" height="110"></canvas>
            <div class="mt-3 text-xs text-gray-600">
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Laporan</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> SK</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Formulir</div>
              <div class="flex items-center gap-2"><span class="inline-block w-3 h-3 rounded bg-gray-300"></span> Privasi</div>
            </div>
          </div>
        </section> --}}

        <!-- Two columns: Activity & Requests -->
        <section class="grid xl:grid-cols-3 gap-4">
          <!-- Activity -->
          <div class="xl:col-span-2 rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700 flex items-center justify-between">
              <span>Aktivitas Terbaru</span>
              <a href="#" class="text-maroon hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
              <table class="min-w-full text-sm">
                <thead>
                  <tr class="text-left border-b">
                    <th class="px-4 py-2">Waktu</th>
                    <th class="px-4 py-2">Pengguna</th>
                    <th class="px-4 py-2">Aksi</th>
                    <th class="px-4 py-2">Objek</th>
                    <th class="px-4 py-2">Status</th>
                  </tr>
                </thead>
                <tbody class="divide-y">
                  <tr>
                    <td class="px-4 py-2 text-gray-600">10 Aug 2025 • 14:02</td>
                    <td class="px-4 py-2">admin.sekretariat</td>
                    <td class="px-4 py-2">Unduh</td>
                    <td class="px-4 py-2">SK Sekretariat A</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Sukses</span></td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">10 Aug 2025 • 13:47</td>
                    <td class="px-4 py-2">user.riset1</td>
                    <td class="px-4 py-2">View</td>
                    <td class="px-4 py-2">KTP Pegawai (Terkendali)</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">Menunggu Kode</span></td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2 text-gray-600">09 Aug 2025 • 09:21</td>
                    <td class="px-4 py-2">admin.sekretariat</td>
                    <td class="px-4 py-2">Unggah</td>
                    <td class="px-4 py-2">Laporan Kinerja 2024</td>
                    <td class="px-4 py-2"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Sukses</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Requests -->
          <div class="rounded-xl border bg-white overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Permintaan Akses</div>
            <ul class="divide-y text-sm">
              <li class="p-4 flex items-start gap-3">
                <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                <div class="flex-1">
                  <p class="font-semibold">user.riset1 meminta akses</p>
                  <p class="text-gray-600 text-xs mt-0.5">KTP Andi Rahman • Alasan: Verifikasi identitas internal</p>
                  <div class="mt-2 flex gap-2">
                    <button class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Setujui</button>
                    <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Tolak</button>
                  </div>
                </div>
              </li>
              <li class="p-4 flex items-start gap-3">
                <span class="inline-flex w-8 h-8 items-center justify-center rounded-full bg-amber-100 text-amber-700">!</span>
                <div class="flex-1">
                  <p class="font-semibold">user.persuratan meminta akses</p>
                  <p class="text-gray-600 text-xs mt-0.5">KK Sitti Aulia • Alasan: Administrasi tunjangan</p>
                  <div class="mt-2 flex gap-2">
                    <button class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Setujui</button>
                    <button class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Tolak</button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>

      </main>

      <!-- Footer -->
      <footer class="px-4 lg:px-6 py-4 text-sm text-gray-600 border-t bg-white">
        © 2025 SIGAP BRIDA • BRIDA Kota Makassar
      </footer>
    </div>
  </div>

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
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    const lineChart = new Chart(lineCtx, {
      type: 'line',
      data: {
        labels: Array.from({length: 12}, (_,i)=>`D${i+1}`),
        datasets: [
          {label:'Unduh', data:[10,22,18,30,28,35,40,38,42,31,29,45], borderWidth:2, tension:.3},
          {label:'View',   data:[14,28,21,36,32,40,48,44,51,40,38,60], borderWidth:2, tension:.3}
        ]
      },
      options: {
        responsive:true,
        maintainAspectRatio:false,
        scales: {
          y: { beginAtZero:true, ticks:{ stepSize:10 } }
        },
        plugins: { legend:{ labels:{ boxWidth:10 } } }
      }
    });

    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
      type: 'doughnut',
      data: {
        labels:['Laporan','SK','Formulir','Privasi'],
        datasets:[{ data:[38,27,19,16] }]
      },
      options: { responsive:true, maintainAspectRatio:false, cutout:'55%' }
    });
  </script>
</body>
</html>
