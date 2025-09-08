@extends('layouts.app')

@section('content')
  <style>
    body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}
    .scrollbar-thin::-webkit-scrollbar{height:8px;width:8px}
    .scrollbar-thin::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}
    .chip{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .5rem;border-radius:.5rem;border:1px solid #e5e7eb;background:#fff}
    .chip button{opacity:.7}
    .chip button:hover{opacity:1}
    .tab-btn[aria-selected="true"]{background:#7a2222;color:#fff;border-color:#7a2222}
    .tab-panel{display:none}
    .tab-panel.active{display:block}
    .toast{position:fixed;right:1rem;bottom:1rem;z-index:50;background:#111827;color:#fff;padding:.5rem .75rem;border-radius:.5rem;font-size:.875rem;opacity:.95}
    @media print{ .no-print{display:none!important} }
    .role-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .5rem;border-radius:9999px;background:#f3f4f6;border:1px solid #e5e7eb}
  </style>
     <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Konfigurasi — SIGAP Inovasi</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola master data OPD/Unit, kamus inovasi, parameter evidence, dan pengguna.</p>
      </div>
      <div class="no-print flex flex-wrap gap-2">
        <label class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm cursor-pointer">
          Import JSON
          <input id="importFile" type="file" accept=".json" class="hidden">
        </label>
        <button id="btnExport" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Export JSON</button>
        <button id="btnReset" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50">Reset Default</button>
      </div>
    </div>
  </section>

  <!-- Tabs -->
  <section class="max-w-7xl mx-auto px-4 pb-10">
    <div class="rounded-2xl border bg-white">
      <div class="p-3 border-b flex flex-wrap gap-2">
        <button class="tab-btn px-3 py-2 rounded-md border text-sm" data-tab="opd" aria-selected="true">OPD & Unit</button>
        <button class="tab-btn px-3 py-2 rounded-md border text-sm" data-tab="master">Master Data Inovasi</button>
        <button class="tab-btn px-3 py-2 rounded-md border text-sm" data-tab="param">Parameter Evidence (20)</button>
        <button class="tab-btn px-3 py-2 rounded-md border text-sm" data-tab="users">Pengguna & Akses</button>
      </div>

      <!-- Panel: OPD -->
      <div id="tab-opd" class="tab-panel active p-4 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <div>
            <h2 class="font-semibold text-gray-800">Daftar OPD & Unit</h2>
            <p class="text-xs text-gray-500">Kelola struktur OPD dan unit kerja. Data ini muncul di formulir inovasi.</p>
          </div>
          <div class="flex items-center gap-2">
            <div class="relative">
              <input id="opdSearch" class="pl-9 pr-3 py-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon w-64" placeholder="Cari OPD/Unit…">
              <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
            <button id="btnAddOPD" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah OPD/Unit</button>
          </div>
        </div>

        <div class="overflow-x-auto border rounded-xl">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left border-b bg-gray-50">
                <th class="px-4 py-3 w-12">#</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Alias</th>
                <th class="px-4 py-3">Jenis</th>
                <th class="px-4 py-3">Induk</th>
                <th class="px-4 py-3 w-40">Aksi</th>
              </tr>
            </thead>
            <tbody id="tbodyOPD" class="divide-y"></tbody>
          </table>
        </div>
      </div>

      <!-- Panel: Master Data -->
      <div id="tab-master" class="tab-panel p-4 space-y-5">
        <div class="grid lg:grid-cols-2 gap-5">
          <!-- Bentuk -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Bentuk Inovasi</h3>
              <div class="flex items-center gap-2">
                <input id="inBentuk" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Tambah bentuk…">
                <button data-add="bentuk" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listBentuk" class="mt-3 flex flex-wrap gap-2"></div>
          </div>

          <!-- Urusan -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Jenis Urusan</h3>
              <div class="flex items-center gap-2">
                <input id="inUrusan" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Tambah urusan…">
                <button data-add="urusan" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listUrusan" class="mt-3 flex flex-wrap gap-2"></div>
          </div>

          <!-- Inisiator -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Inisiator</h3>
              <div class="flex items-center gap-2">
                <input id="inInisiator" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Tambah inisiator…">
                <button data-add="inisiator" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listInisiator" class="mt-3 flex flex-wrap gap-2"></div>
          </div>

          <!-- Klasifikasi -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Klasifikasi Inovasi</h3>
              <div class="flex items-center gap-2">
                <input id="inKlasifikasi" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Tambah klasifikasi…">
                <button data-add="klasifikasi" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listKlasifikasi" class="mt-3 flex flex-wrap gap-2"></div>
          </div>

          <!-- Asta Cipta -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Asta Cipta</h3>
              <div class="flex items-center gap-2">
                <input id="inAsta" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Tambah Asta…">
                <button data-add="asta" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listAsta" class="mt-3 flex flex-wrap gap-2"></div>
          </div>

          <!-- Program Prioritas -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Program Prioritas Walikota</h3>
              <div class="flex items-center gap-2">
                <input id="inPrioritas" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Tambah program…">
                <button data-add="prioritas" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listPrioritas" class="mt-3 flex flex-wrap gap-2"></div>
          </div>

          <!-- Jenis Inovasi -->
          <div class="rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold text-gray-800">Jenis Inovasi</h3>
              <div class="flex items-center gap-2">
                <input id="inJenis" class="px-3 py-1.5 rounded-md border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Digital / Non Digital">
                <button data-add="jenis" class="px-3 py-1.5 rounded-md bg-maroon text-white text-sm">Tambah</button>
              </div>
            </div>
            <div id="listJenis" class="mt-3 flex flex-wrap gap-2"></div>
          </div>
        </div>
      </div>

      <!-- Panel: Parameter Evidence -->
      <div id="tab-param" class="tab-panel p-4">
        <div class="grid lg:grid-cols-3 gap-4">
          <!-- Indikator list -->
          <aside class="rounded-xl border p-3 lg:sticky lg:top-24 h-fit">
            <div class="flex items-center justify-between mb-2">
              <h3 class="font-semibold text-gray-800">Indikator (20)</h3>
              <button id="btnCopyParams" class="text-xs px-3 py-1.5 rounded-md border hover:bg-gray-50">Copy dari…</button>
            </div>
            <ul id="indikatorList" class="space-y-1 text-sm max-h-[60vh] overflow-auto scrollbar-thin"></ul>
          </aside>

          <!-- Editor parameter -->
          <div class="lg:col-span-2 rounded-xl border p-4">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs text-gray-500">Indikator terpilih</p>
                <h3 id="indikatorTitle" class="font-semibold text-gray-800">—</h3>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Total Bobot:</span>
                <span id="totalBobot" class="text-base font-extrabold text-maroon">0</span>
              </div>
            </div>

            <div class="mt-3">
              <label class="text-sm font-semibold text-gray-700">Tambah Parameter</label>
              <div class="mt-1.5 grid sm:grid-cols-5 gap-2">
                <input id="paramLabel" class="sm:col-span-3 px-3 py-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="cth: SK Kepala Daerah">
                <input id="paramBobot" type="number" min="0" class="px-3 py-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon text-sm" placeholder="Bobot">
                <button id="btnAddParam" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah</button>
              </div>
              <p class="text-[11px] text-gray-500 mt-1">Parameter adalah pilihan teks yang akan dipilih user saat isi evidence; bobot opsional untuk perhitungan skor.</p>
            </div>

            <div id="paramList" class="mt-4 divide-y rounded-lg border"></div>
          </div>
        </div>
      </div>

      <!-- Panel: Pengguna & Akses -->
      <div id="tab-users" class="tab-panel p-4 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
          <div>
            <h2 class="font-semibold text-gray-800">Pengguna & Akses</h2>
            <p class="text-xs text-gray-500">Kelola akun pengguna, peran, status, dan keterkaitan OPD/Unit.</p>
          </div>
          <div class="flex items-center gap-2">
            <div class="relative">
              <input id="userSearch" class="pl-9 pr-3 py-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon w-64" placeholder="Cari nama / username…">
              <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
            <select id="userFilter" class="px-3 py-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon text-sm">
              <option value="">Semua Status</option>
              <option>Aktif</option>
              <option>Nonaktif</option>
            </select>
            <button id="btnAddUser" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pengguna</button>
          </div>
        </div>

        <div class="overflow-x-auto border rounded-xl">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left border-b bg-gray-50">
                <th class="px-4 py-3 w-12">#</th>
                <th class="px-4 py-3">Nama</th>
                <th class="px-4 py-3">Username</th>
                <th class="px-4 py-3">OPD/Unit</th>
                <th class="px-4 py-3">Peran</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3 w-56">Aksi</th>
              </tr>
            </thead>
            <tbody id="tbodyUsers" class="divide-y"></tbody>
          </table>
        </div>
      </div>

    </div>
  </section>

  <!-- Modal: OPD -->
  <div id="modalOPD" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeOPD()"></div>
    <div class="relative z-10 mx-auto max-w-xl px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-3 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 text-white">
          <h2 id="opdModalTitle" class="text-lg font-bold">Tambah OPD/Unit</h2>
          <p class="text-white/80 text-xs mt-0.5">Masukkan data OPD atau Unit Kerja.</p>
        </div>
        <form id="formOPD" class="p-5 grid sm:grid-cols-2 gap-4" onsubmit="event.preventDefault(); saveOPD();">
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Nama</span>
            <input id="opdNama" required class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2" placeholder="Dinas Kesehatan">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alias/Singkatan</span>
            <input id="opdAlias" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2" placeholder="Dinkes">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Jenis</span>
            <select id="opdJenis" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              <option>OPD</option>
              <option>Unit Kerja</option>
            </select>
          </label>
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Induk (opsional)</span>
            <select id="opdInduk" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              <option value="">—</option>
            </select>
          </label>
          <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-lg border hover:bg-gray-50" onclick="closeOPD()">Batal</button>
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: User -->
  <div id="modalUser" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeUser()"></div>
    <div class="relative z-10 mx-auto max-w-xl px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-3 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 text-white">
          <h2 id="userModalTitle" class="text-lg font-bold">Tambah Pengguna</h2>
          <p class="text-white/80 text-xs mt-0.5">Kelola identitas dan akses pengguna.</p>
        </div>
        <form id="formUser" class="p-5 grid sm:grid-cols-2 gap-4" onsubmit="event.preventDefault(); saveUser();">
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Nama Lengkap</span>
            <input id="uNama" required class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2" placeholder="Nama pengguna">
          </label>
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Username / Email</span>
            <input id="uUser" required class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2" placeholder="user@makassarkota.go.id">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">OPD/Unit</span>
            <select id="uOPD" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"></select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select id="uStatus" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              <option>Aktif</option>
              <option>Nonaktif</option>
            </select>
          </label>

          <div class="sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Peran</span>
            <div id="uRolesWrap" class="mt-1.5 flex flex-wrap gap-2"></div>
            <p class="text-[11px] text-gray-500 mt-1">Pilih satu atau lebih peran.</p>
          </div>

          <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-lg border hover:bg-gray-50" onclick="closeUser()">Batal</button>
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Toast -->
  <div id="toast" class="toast hidden"></div>

@endsection

@push('scripts')
      <script>
    // ========= Storage Keys =========
    const KEY_OPD   = 'sigap:konfig:opd';
    const KEY_DICT  = 'sigap:konfig:dict';
    const KEY_PARAM = 'sigap:konfig:params';
    const KEY_USERS = 'sigap:konfig:users';

    // ========= Default Seeds =========
    const DEFAULT_OPD = [
      {id:1, nama:'Badan Riset dan Inovasi Daerah', alias:'BRIDA', jenis:'OPD', induk:''},
      {id:2, nama:'Dinas Kesehatan', alias:'Dinkes', jenis:'OPD', induk:''},
      {id:3, nama:'Bidang Riset', alias:'Bid. Riset', jenis:'Unit Kerja', induk:'BRIDA'},
    ];
    const DEFAULT_DICT = {
      bentuk: ['Inovasi Tata Kelola','Pelayanan Publik','Inovasi Daerah Lainnya'],
      urusan: ['Kesehatan','Pendidikan','Air Bersih','Transportasi','Sosial'],
      inisiator: ['OPD','Unit Kerja','Kolaborasi'],
      klasifikasi: ['Inovasi Perangkat Daerah','Inovasi Desa dan Kelurahan','Inovasi Masyarakat'],
      asta: ['Ekonomi','Sosial','Infrastruktur','Pemerintahan','Lingkungan'],
      prioritas: ['Jagai Anakta','Makassar Recover','Smart City'],
      jenis: ['Digital','Non Digital']
    };
    const INDICATOR_TITLES = [
      'Regulasi Inovasi Daerah *','Ketersediaan SDM *','Dukungan Anggaran','Alat Kerja','Bimtek Inovasi',
      'Integrasi Program & Kegiatan (RKPD)','Keterlibatan Aktor Inovasi','Pelaksana Inovasi Daerah','Jejaring Inovasi','Sosialisasi Inovasi',
      'Pedoman Teknis','Kemudahan Informasi Layanan','Kemudahan Proses Layanan','Penyelesaian Layanan Pengaduan','Layanan Terintegrasi',
      'Replikasi','Kecepatan Penciptaan Inovasi *','Kemanfaatan Inovasi *','Monitoring & Evaluasi','Kualitas Inovasi *'
    ];
    function defaultParams(){
      const map = {};
      INDICATOR_TITLES.forEach((t,i)=>{ map[i+1] = []; });
      map[1] = [
        {label:'Peraturan Daerah (Perda)', weight:10},
        {label:'Peraturan Kepala Daerah (Perkada)', weight:8},
        {label:'SK Kepala Daerah', weight:6},
        {label:'SK Kepala Perangkat Daerah', weight:4}
      ];
      map[2] = [
        {label:'Ada SK Tim & Uraian Tugas', weight:8},
        {label:'Ada SK Tim (tanpa uraian)', weight:6},
        {label:'Surat Penugasan/Perintah', weight:4}
      ];
      return map;
    }
    const ROLE_OPTIONS = ['Superadmin','Admin Inovasi','Admin Evidence','OPD Editor','Viewer'];
    const DEFAULT_USERS = [
      {id:1, name:'Admin Sekretariat', username:'admin.sekretariat@makassarkota.go.id', opd:'BRIDA', roles:['Superadmin'], status:'Aktif', created_at:new Date().toISOString(), last_pw_changed_at:new Date().toISOString()},
      {id:2, name:'User Riset 1', username:'user.riset1@makassarkota.go.id', opd:'Bid. Riset', roles:['OPD Editor'], status:'Aktif', created_at:new Date().toISOString(), last_pw_changed_at:new Date().toISOString()},
    ];

    // ========= State =========
    let OPDS = JSON.parse(localStorage.getItem(KEY_OPD) || 'null') || DEFAULT_OPD;
    let DICT = JSON.parse(localStorage.getItem(KEY_DICT) || 'null') || DEFAULT_DICT;
    let PARAMS = JSON.parse(localStorage.getItem(KEY_PARAM) || 'null') || defaultParams();
    let USERS = JSON.parse(localStorage.getItem(KEY_USERS) || 'null') || DEFAULT_USERS;

    // ========= Helpers =========
    const $ = sel => document.querySelector(sel);
    const $$ = sel => [...document.querySelectorAll(sel)];
    function saveAll(){
      localStorage.setItem(KEY_OPD, JSON.stringify(OPDS));
      localStorage.setItem(KEY_DICT, JSON.stringify(DICT));
      localStorage.setItem(KEY_PARAM, JSON.stringify(PARAMS));
      localStorage.setItem(KEY_USERS, JSON.stringify(USERS));
    }
    function toast(msg){
      const el = $('#toast');
      el.textContent = msg;
      el.classList.remove('hidden');
      clearTimeout(el._t);
      el._t = setTimeout(()=> el.classList.add('hidden'), 1600);
    }
    function nextId(arr){ return (arr.reduce((m,x)=>Math.max(m,x.id||0),0) || 0) + 1; }

    // ========= Tabs =========
    $$('.tab-btn').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        $$('.tab-btn').forEach(b=> b.setAttribute('aria-selected','false'));
        btn.setAttribute('aria-selected','true');
        const t = btn.dataset.tab;
        $$('.tab-panel').forEach(p=> p.classList.remove('active'));
        $('#tab-'+t).classList.add('active');
      });
    });

    // ========= OPD UI =========
    const tbodyOPD = $('#tbodyOPD');
    function renderOPD(){
      const q = ($('#opdSearch').value||'').toLowerCase();
      const rows = OPDS.filter(o=>{
        const str = `${o.nama} ${o.alias} ${o.jenis} ${o.induk}`.toLowerCase();
        return str.includes(q);
      });
      tbodyOPD.innerHTML = rows.map((o,i)=>`
        <tr>
          <td class="px-4 py-3 text-gray-600">${i+1}</td>
          <td class="px-4 py-3 font-medium text-gray-900">${o.nama}</td>
          <td class="px-4 py-3">${o.alias||'—'}</td>
          <td class="px-4 py-3">${o.jenis}</td>
          <td class="px-4 py-3">${o.induk||'—'}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="editOPD(${o.id})">Edit</button>
              <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="removeOPD(${o.id})">Hapus</button>
            </div>
          </td>
        </tr>
      `).join('');
    }
    $('#opdSearch').addEventListener('input', renderOPD);
    $('#btnAddOPD').addEventListener('click', ()=> openOPD());

    // Modal OPD
    let currOPDId = null;
    function openOPD(id=null){
      currOPDId = id;
      $('#opdModalTitle').textContent = id ? 'Edit OPD/Unit' : 'Tambah OPD/Unit';
      // isi dropdown induk dari semua OPD
      const sel = $('#opdInduk');
      sel.innerHTML = `<option value="">—</option>` + OPDS.filter(x=>x.jenis==='OPD').map(x=>`<option>${x.alias || x.nama}</option>`).join('');
      if(id){
        const o = OPDS.find(x=>x.id===id);
        $('#opdNama').value = o.nama;
        $('#opdAlias').value = o.alias||'';
        $('#opdJenis').value = o.jenis;
        $('#opdInduk').value = o.induk||'';
      }else{
        $('#formOPD').reset();
      }
      $('#modalOPD').classList.remove('hidden');
    }
    function closeOPD(){ $('#modalOPD').classList.add('hidden'); }
    function saveOPD(){
      const nama = $('#opdNama').value.trim();
      if(!nama){ alert('Nama wajib diisi'); return; }
      const data = {
        nama,
        alias: $('#opdAlias').value.trim(),
        jenis: $('#opdJenis').value,
        induk: $('#opdInduk').value
      };
      if(currOPDId){
        const idx = OPDS.findIndex(x=>x.id===currOPDId);
        OPDS[idx] = {...OPDS[idx], ...data};
        toast('OPD diperbarui');
      }else{
        OPDS.push({id: nextId(OPDS), ...data});
        toast('OPD ditambahkan');
      }
      saveAll(); renderOPD(); closeOPD();
      // refresh opsi OPD di modal user bila terbuka
      if(!$('#modalUser').classList.contains('hidden')) fillUserOPDOptions();
    }
    function editOPD(id){ openOPD(id); }
    function removeOPD(id){
      const o = OPDS.find(x=>x.id===id);
      if(!confirm(`Hapus "${o.nama}"?`)) return;
      OPDS = OPDS.filter(x=>x.id!==id);
      saveAll(); renderOPD(); toast('OPD dihapus');
    }
    window.editOPD = editOPD;
    window.removeOPD = removeOPD;

    // ========= Master Data Chips =========
    const dictMap = [
      {key:'bentuk', el:'#listBentuk', input:'#inBentuk', label:'Bentuk'},
      {key:'urusan', el:'#listUrusan', input:'#inUrusan', label:'Urusan'},
      {key:'inisiator', el:'#listInisiator', input:'#inInisiator', label:'Inisiator'},
      {key:'klasifikasi', el:'#listKlasifikasi', input:'#inKlasifikasi', label:'Klasifikasi'},
      {key:'asta', el:'#listAsta', input:'#inAsta', label:'Asta'},
      {key:'prioritas', el:'#listPrioritas', input:'#inPrioritas', label:'Program'},
      {key:'jenis', el:'#listJenis', input:'#inJenis', label:'Jenis'},
    ];
    function renderDict(key){
      const cfg = dictMap.find(d=>d.key===key);
      const wrap = $(cfg.el);
      wrap.innerHTML = (DICT[key]||[]).map((val,idx)=>`
        <span class="chip text-sm">
          ${val}
          <button title="Ubah" onclick="editChip('${key}',${idx})">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 20h9"/><path stroke-width="2" d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
          </button>
          <button title="Hapus" onclick="delChip('${key}',${idx})">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 6h18M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path stroke-width="2" d="M10 11v6M14 11v6"/></svg>
          </button>
        </span>
      `).join('') || '<p class="text-sm text-gray-500">Belum ada data.</p>';
    }
    function addDict(key, value){
      value = value.trim(); if(!value) return;
      DICT[key] = DICT[key] || [];
      if(DICT[key].includes(value)){ toast('Sudah ada'); return; }
      DICT[key].push(value); saveAll(); renderDict(key); toast('Ditambahkan');
    }
    function editChip(key, idx){
      const curr = DICT[key][idx];
      const val = prompt('Ubah:', curr);
      if(val===null) return;
      const v = val.trim();
      if(!v) return;
      DICT[key][idx] = v; saveAll(); renderDict(key); toast('Diperbarui');
    }
    function delChip(key, idx){
      const curr = DICT[key][idx];
      if(!confirm(`Hapus "${curr}"?`)) return;
      DICT[key].splice(idx,1); saveAll(); renderDict(key); toast('Dihapus');
    }
    window.editChip = editChip;
    window.delChip  = delChip;

    dictMap.forEach(d=>{
      renderDict(d.key);
      const btn = document.querySelector(`[data-add="${d.key}"]`);
      btn.addEventListener('click', ()=>{
        const val = $(d.input).value;
        addDict(d.key, val);
        $(d.input).value = '';
      });
      $(d.input).addEventListener('keydown', e=>{
        if(e.key==='Enter'){ e.preventDefault(); btn.click(); }
      });
    });

    // ========= Parameter Evidence =========
    const indikatorList = $('#indikatorList');
    const paramList = $('#paramList');
    let currIndeks = 1;

    function renderIndikatorList(){
      indikatorList.innerHTML = INDICATOR_TITLES.map((t,i)=>`
        <li>
          <button class="w-full text-left px-3 py-2 rounded-md hover:bg-gray-50 ${i+1===currIndeks?'bg-maroon/5 border border-maroon/20':''}"
                  onclick="selectIndikator(${i+1})">
            <span class="font-medium">#${i+1}</span> — ${t}
          </button>
        </li>
      `).join('');
    }
    function selectIndikator(no){
      currIndeks = no;
      $('#indikatorTitle').textContent = `#${no} — ${INDICATOR_TITLES[no-1]}`;
      renderParams();
      renderIndikatorList();
    }
    window.selectIndikator = selectIndikator;

    function renderParams(){
      const arr = PARAMS[currIndeks] || [];
      const total = arr.reduce((s,x)=> s + (parseInt(x.weight||0,10)||0), 0);
      $('#totalBobot').textContent = total;

      if(arr.length===0){
        paramList.innerHTML = `<div class="p-4 text-sm text-gray-500">Belum ada parameter. Tambahkan di atas.</div>`;
        return;
      }
      paramList.innerHTML = arr.map((p,idx)=>`
        <div class="flex items-center gap-3 px-3 py-2">
          <div class="flex-1 min-w-0">
            <p class="font-medium text-gray-900 truncate">${p.label}</p>
            <p class="text-xs text-gray-500">Bobot: <span class="font-semibold">${p.weight||0}</span></p>
          </div>
          <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="editParam(${idx})">Ubah</button>
          <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="delParam(${idx})">Hapus</button>
        </div>
      `).join('');
    }
    function addParam(){
      const label = $('#paramLabel').value.trim();
      const bobot = parseInt($('#paramBobot').value||'0',10)||0;
      if(!label){ alert('Label parameter wajib diisi'); return; }
      PARAMS[currIndeks] = PARAMS[currIndeks] || [];
      if(PARAMS[currIndeks].some(x=> x.label.toLowerCase()===label.toLowerCase())){
        alert('Parameter dengan label sama sudah ada'); return;
      }
      PARAMS[currIndeks].push({label, weight:bobot});
      saveAll(); $('#paramLabel').value=''; $('#paramBobot').value=''; renderParams(); toast('Parameter ditambahkan');
    }
    function editParam(idx){
      const item = PARAMS[currIndeks][idx];
      const label = prompt('Ubah label:', item.label);
      if(label===null) return;
      const weight = prompt('Ubah bobot:', item.weight||0);
      if(weight===null) return;
      PARAMS[currIndeks][idx] = {label: label.trim(), weight: parseInt(weight||'0',10)||0};
      saveAll(); renderParams(); toast('Parameter diperbarui');
    }
    function delParam(idx){
      const item = PARAMS[currIndeks][idx];
      if(!confirm(`Hapus parameter "${item.label}"?`)) return;
      PARAMS[currIndeks].splice(idx,1);
      saveAll(); renderParams(); toast('Parameter dihapus');
    }
    window.editParam = editParam;
    window.delParam = delParam;
    $('#btnAddParam').addEventListener('click', addParam);
    $('#paramLabel').addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); addParam(); }});
    $('#paramBobot').addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); addParam(); }});

    // Copy params dari indikator lain
    $('#btnCopyParams').addEventListener('click', ()=>{
      const from = prompt(`Salin parameter dari indikator nomor berapa? (1–${INDICATOR_TITLES.length})`);
      if(!from) return;
      const no = parseInt(from,10);
      if(!(no>=1 && no<=INDICATOR_TITLES.length)){ alert('Nomor tidak valid'); return; }
      if(no===currIndeks){ alert('Tidak bisa menyalin ke indikator yang sama'); return; }
      PARAMS[currIndeks] = JSON.parse(JSON.stringify(PARAMS[no]||[]));
      saveAll(); renderParams(); toast(`Disalin dari #${no}`);
    });

    // ========= USERS =========
    const tbodyUsers = $('#tbodyUsers');
    const ROLE_BADGE_MAP = {
      'Superadmin':'bg-rose-50 text-rose-700 border-rose-100',
      'Admin Inovasi':'bg-blue-50 text-blue-700 border-blue-100',
      'Admin Evidence':'bg-amber-50 text-amber-700 border-amber-100',
      'OPD Editor':'bg-emerald-50 text-emerald-700 border-emerald-100',
      'Viewer':'bg-gray-100 text-gray-700 border-gray-200'
    };
    function renderUsers(){
      const q = ($('#userSearch').value||'').toLowerCase();
      const f = $('#userFilter').value;
      const rows = USERS.filter(u=>{
        const hay = `${u.name} ${u.username} ${u.opd} ${(u.roles||[]).join(' ')}`.toLowerCase();
        const hit = hay.includes(q);
        const ok = !f || u.status===f;
        return hit && ok;
      });
      tbodyUsers.innerHTML = rows.map((u,i)=>`
        <tr>
          <td class="px-4 py-3 text-gray-600">${i+1}</td>
          <td class="px-4 py-3 font-medium text-gray-900">${u.name}</td>
          <td class="px-4 py-3">${u.username}</td>
          <td class="px-4 py-3">${u.opd||'—'}</td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-1">
              ${(u.roles||[]).map(r=>`<span class="role-pill ${ROLE_BADGE_MAP[r]||''}">${r}</span>`).join('') || '<span class="text-gray-500">—</span>'}
            </div>
          </td>
          <td class="px-4 py-3">
            <span class="px-2 py-0.5 rounded text-xs ${u.status==='Aktif'?'bg-emerald-50 text-emerald-700':'bg-gray-100 text-gray-700'}">${u.status}</span>
          </td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-2">
              <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="editUser(${u.id})">Edit</button>
              <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="resetPw(${u.id})">Reset Password</button>
              <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="toggleStatus(${u.id})">${u.status==='Aktif'?'Nonaktifkan':'Aktifkan'}</button>
              <button class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50" onclick="removeUser(${u.id})">Hapus</button>
            </div>
          </td>
        </tr>
      `).join('') || `<tr><td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada pengguna.</td></tr>`;
    }
    $('#userSearch').addEventListener('input', renderUsers);
    $('#userFilter').addEventListener('change', renderUsers);
    $('#btnAddUser').addEventListener('click', ()=> openUser());

    // Modal User
    let currUserId = null;
    function fillUserOPDOptions(){
      const sel = $('#uOPD');
      const list = ['', ...OPDS.map(o=> o.alias || o.nama)];
      sel.innerHTML = list.map(v=> `<option value="${v}">${v || '—'}</option>`).join('');
    }
    function fillRoleChips(selected=[]){
      const wrap = $('#uRolesWrap');
      wrap.innerHTML = ROLE_OPTIONS.map(r=>{
        const id = `role-${r.replace(/\s+/g,'-').toLowerCase()}`;
        const checked = selected.includes(r) ? 'checked' : '';
        return `
          <label for="${id}" class="cursor-pointer inline-flex items-center gap-2 px-3 py-1.5 rounded-full border hover:bg-gray-50">
            <input id="${id}" type="checkbox" class="accent-maroon" value="${r}" ${checked}>
            <span>${r}</span>
          </label>
        `;
      }).join('');
    }
    function openUser(id=null){
      currUserId = id;
      $('#userModalTitle').textContent = id ? 'Edit Pengguna' : 'Tambah Pengguna';
      fillUserOPDOptions();

      if(id){
        const u = USERS.find(x=>x.id===id);
        $('#uNama').value = u.name;
        $('#uUser').value = u.username;
        $('#uOPD').value = u.opd || '';
        $('#uStatus').value = u.status || 'Aktif';
        fillRoleChips(u.roles||[]);
      }else{
        $('#formUser').reset();
        fillRoleChips([]);
      }
      $('#modalUser').classList.remove('hidden');
    }
    function closeUser(){ $('#modalUser').classList.add('hidden'); }

    function saveUser(){
      const name = $('#uNama').value.trim();
      const username = $('#uUser').value.trim();
      if(!name || !username){ alert('Nama dan Username wajib diisi'); return; }
      const opd = $('#uOPD').value;
      const status = $('#uStatus').value;
      const roles = [...$('#uRolesWrap').querySelectorAll('input[type="checkbox"]:checked')].map(i=> i.value);

      if(currUserId){
        const idx = USERS.findIndex(x=>x.id===currUserId);
        USERS[idx] = {...USERS[idx], name, username, opd, status, roles};
        toast('Pengguna diperbarui');
      }else{
        USERS.push({
          id: nextId(USERS),
          name, username, opd, status, roles,
          created_at: new Date().toISOString(),
          last_pw_changed_at: null
        });
        toast('Pengguna ditambahkan');
      }
      saveAll(); renderUsers(); closeUser();
    }
    function editUser(id){ openUser(id); }
    function removeUser(id){
      const u = USERS.find(x=>x.id===id);
      if(!confirm(`Hapus pengguna "${u.name}"?`)) return;
      USERS = USERS.filter(x=>x.id!==id);
      saveAll(); renderUsers(); toast('Pengguna dihapus');
    }
    function toggleStatus(id){
      const idx = USERS.findIndex(x=>x.id===id);
      USERS[idx].status = USERS[idx].status==='Aktif' ? 'Nonaktif' : 'Aktif';
      saveAll(); renderUsers(); toast('Status diperbarui');
    }
    function resetPw(id){
      const u = USERS.find(x=>x.id===id);
      if(!confirm(`Reset password untuk "${u.name}"?`)) return;
      // Demo: tidak menyimpan password; hanya timestamp sebagai penanda reset
      u.last_pw_changed_at = new Date().toISOString();
      saveAll(); toast('Password di-reset (demo)');
    }
    window.editUser = editUser;
    window.removeUser = removeUser;
    window.toggleStatus = toggleStatus;
    window.resetPw = resetPw;

    // ========= Import / Export / Reset =========
    $('#btnExport').addEventListener('click', ()=>{
      const data = {
        exported_at: new Date().toISOString(),
        opd: OPDS,
        dict: DICT,
        params: PARAMS,
        users: USERS
      };
      const blob = new Blob([JSON.stringify(data,null,2)], {type:'application/json'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `sigap-konfigurasi-${new Date().toISOString().slice(0,10)}.json`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    });

    $('#importFile').addEventListener('change', (e)=>{
      const f = e.target.files?.[0];
      if(!f) return;
      const reader = new FileReader();
      reader.onload = ()=>{
        try{
          const obj = JSON.parse(reader.result);
          if(!confirm('Import akan menimpa konfigurasi saat ini. Lanjutkan?')) return;
          if(obj.opd)   OPDS = obj.opd;
          if(obj.dict)  DICT = obj.dict;
          if(obj.params)PARAMS = obj.params;
          if(obj.users) USERS = obj.users;
          saveAll();
          // rerender semua
          renderOPD();
          dictMap.forEach(d=> renderDict(d.key));
          renderIndikatorList(); selectIndikator(1);
          renderUsers();
          toast('Import berhasil');
        }catch(err){
          alert('File JSON tidak valid');
        }
      };
      reader.readAsText(f);
      e.target.value = '';
    });

    $('#btnReset').addEventListener('click', ()=>{
      if(!confirm('Kembalikan ke default pabrik? Semua perubahan akan hilang.')) return;
      OPDS = DEFAULT_OPD.slice();
      DICT = JSON.parse(JSON.stringify(DEFAULT_DICT));
      PARAMS = defaultParams();
      USERS = DEFAULT_USERS.slice();
      saveAll();
      renderOPD();
      dictMap.forEach(d=> renderDict(d.key));
      renderIndikatorList(); selectIndikator(1);
      renderUsers();
      toast('Konfigurasi direset');
    });

    // ========= Init =========
    renderOPD();
    renderIndikatorList();
    selectIndikator(1);
    renderUsers();
  </script>
@endpush