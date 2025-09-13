<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Detail Inovasi — SIGAP BRIDA</title>

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
    .scrollbar-thin::-webkit-scrollbar{height:8px;width:8px}
    .scrollbar-thin::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}
    /* Print */
    @media print{
      .no-print{ display:none !important; }
      header, footer{ display:none !important; }
      .card{ box-shadow:none !important; border-color:#e5e7eb !important; }
      body{ background:#fff; }
    }
    /* Clamp for description previews (if needed) */
    .ql-content :is(h1,h2,h3){ margin: .25rem 0 .5rem; }
    .ql-content p{ margin: .25rem 0 .5rem; }

    /* Watermark saat Cetak */
@media print {
  /* yang sudah ada — boleh tetap */
  .no-print{ display:none !important; }
  header, footer{ display:none !important; }
  .card{ box-shadow:none !important; border-color:#e5e7eb !important; }
  body{ background:#fff; }

  /* WATERMARK */
  body::before{
    content: "Dicetak via SIGAP INOVASI - BRIDA MKS";
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-weight: 800;
    font-size: 30px;           /* boleh sesuaikan */
    letter-spacing: .06em;
    color: #7a2222;            /* maroon kamu */
    opacity: .08;              /* cukup samar, tetap terbaca */
    z-index: 9999;             /* di atas konten */
    pointer-events: none;
    text-transform: uppercase; /* biar tegas */
    white-space: nowrap;
  }

  /* opsional: bikin kartu transparan agar watermark lebih terlihat */
  .card, section, .rounded-2xl, .border, .bg-white { background: transparent !important; }
}

  </style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <header class="border-b border-maroon/10 bg-white sticky top-0 z-40 no-print">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="index.html" class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500 -mt-0.5">Sistem Informasi Gabungan Arsip & Privasi</p>
        </div>
      </a>
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="sigap-inovasi.html" class="hover:text-maroon">Inovasi</a>
        <a href="sigap-dokumen.html" class="hover:text-maroon">Dokumen</a>
        <a href="pegawai.html" class="hover:text-maroon">Pegawai</a>
        <a href="admin-dashboard.html" class="hover:text-maroon">Admin</a>
        <a href="login.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Login</a>
      </nav>
    </div>
  </header>

  <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
          <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-maroon text-white">
            <!-- ikon pemerintahan -->
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
            </svg>
          </span>
          <p class="text-xs text-gray-600">Detail Inovasi</p>
        </div>
        <h1 id="title" class="mt-1 text-2xl font-extrabold text-gray-900">Judul Inovasi</h1>
        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
          <span id="badgeKlas" class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">Klasifikasi: —</span>
          <span id="badgeJenis" class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">Jenis: —</span>
          <span id="badgeUrusan" class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">Urusan: —</span>
          <span id="badgeInisiator" class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">Inisiator: —</span>
          <span id="badgeOPD" class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">OPD: —</span>
        </div>
      </div>
      <div class="no-print flex flex-wrap gap-2">
        <a href="sigap-evidence.edit.html" class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm">Edit Metadata</a>
        <a href="sigap-inovasi.evidence.html" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Isi Evidence</a>
        <button onclick="window.print()" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Cetak</button>
      </div>
    </div>
  </section>

<!-- Summary cards -->
<section class="max-w-7xl mx-auto px-4">
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Skor Evidence</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $evTotal }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $evFilled }}/20 indikator terisi</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">
        {{ $inovasi->updated_at? $inovasi->updated_at->timezone('Asia/Makassar')->format('d M Y • H:i') : '—' }}
      </p>
      <p class="text-xs text-gray-500 mt-1">Metadata</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Berkas Evidence</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $evFiles }}</p>
      <p class="text-xs text-gray-500 mt-1">File terunggah</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Koordinat</p>
      <p class="mt-1 text-lg font-semibold text-gray-900">{{ $inovasi->koordinat ?? '—' }}</p>
      <p class="text-xs text-gray-500 mt-1">Lokasi (jika diisi)</p>
    </div>
  </div>
</section>

  <!-- Tahapan -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="card rounded-2xl border bg-white p-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-800">Tahapan Inovasi</h3>
        <div class="text-xs text-gray-500">Status ringkas</div>
      </div>
      <div class="grid md:grid-cols-3 gap-3 text-sm">
        <div class="rounded-xl border p-3">
          <p class="text-gray-600">Inisiatif</p>
          <p id="tahapInis" class="mt-1 font-semibold"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">—</span></p>
        </div>
        <div class="rounded-xl border p-3">
          <p class="text-gray-600">Uji Coba</p>
          <p id="tahapUji" class="mt-1 font-semibold"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">—</span></p>
        </div>
        <div class="rounded-xl border p-3">
          <p class="text-gray-600">Penerapan</p>
          <p id="tahapTerap" class="mt-1 font-semibold"><span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">—</span></p>
        </div>
      </div>
      <div class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
          <div id="barTahap" class="h-2 bg-maroon rounded-full" style="width:0%"></div>
        </div>
        <p id="txtTahap" class="text-xs text-gray-500 mt-1">0% selesai</p>
      </div>
    </div>
  </section>

<!-- Ringkasan Evidence -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="card rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
      <span>Ringkasan Evidence (20 indikator)</span>
      <a href="{{ route('evidence.form', $inovasi->id) }}"
         class="ml-3 px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800 no-print">
         Isi / Perbarui Evidence
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b">
            <th class="px-4 py-3 w-12">No</th>
            <th class="px-4 py-3">Indikator</th>
            <th class="px-4 py-3">Parameter</th>
            <th class="px-4 py-3">Bobot</th>
            <th class="px-4 py-3">File</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @php
            $tone = function($w){
              if(($w ?? 0) >= 10) return 'bg-emerald-50 text-emerald-700';
              if(($w ?? 0) >= 6)  return 'bg-blue-50 text-blue-700';
              if(($w ?? 0) >  0)  return 'bg-amber-50 text-amber-700';
              return 'bg-gray-100 text-gray-700';
            };
          @endphp
          @foreach($evItems as $row)
            <tr>
              <td class="px-4 py-3 text-gray-600">{{ $row['no'] }}</td>
              <td class="px-4 py-3">{{ $row['indikator'] }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded {{ $tone($row['selected_weight'] ?? 0) }}">
                  {{ $row['selected_label'] ?: 'Belum dipilih' }}
                </span>
              </td>
              <td class="px-4 py-3 font-semibold {{ ($row['selected_weight']??0)>0 ? 'text-maroon' : '' }}">
                {{ $row['selected_weight'] ?? 0 }}
              </td>
              <td class="px-4 py-3">
                @if(!empty($row['file_url']))
                  <a href="{{ $row['file_url'] }}" target="_blank" class="text-maroon hover:underline">Lihat file</a>
                @else
                  <span class="text-gray-500">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="border-t bg-gray-50">
          <tr>
            <td class="px-4 py-3 font-semibold" colspan="3">Total Bobot</td>
            <td class="px-4 py-3 font-extrabold text-maroon">{{ $evTotal }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</section>


@push('scripts')
<script>
  // Filter baris evidence (client-side) — optional ringan
  const sel = document.getElementById('filterEv');
  const tbody = document.getElementById('tbodyEv');
  sel?.addEventListener('change', () => {
    const val = sel.value;
    [...tbody.querySelectorAll('tr')].forEach(tr => {
      const done = tr.getAttribute('data-done') === '1';
      const hasFile = tr.getAttribute('data-file') === '1';
      let show = true;
      if (val === 'complete')  show = done && hasFile;
      if (val === 'incomplete') show = !(done && hasFile);
      tr.style.display = show ? '' : 'none';
    });
  });
</script>
@endpush


  <!-- Deskripsi detail -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="grid lg:grid-cols-2 gap-4">
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Rancang Bangun</h3>
        <div id="v_rancang" class="ql-content prose prose-sm max-w-none mt-2 text-gray-800"></div>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Tujuan Inovasi</h3>
        <div id="v_tujuan" class="ql-content prose prose-sm max-w-none mt-2 text-gray-800"></div>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Manfaat yang Diperoleh</h3>
        <div id="v_manfaat" class="ql-content prose prose-sm max-w-none mt-2 text-gray-800"></div>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Hasil Inovasi</h3>
        <div id="v_hasil" class="ql-content prose prose-sm max-w-none mt-2 text-gray-800"></div>
      </div>
    </div>
  </section>

  <!-- Ringkasan Evidence -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="card rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span>Ringkasan Evidence (20 indikator)</span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Tampilkan</label>
          <select id="filterEv" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="all">Semua</option>
            <option value="incomplete">Yang belum lengkap</option>
            <option value="complete">Yang sudah lengkap</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3 w-12">No</th>
              <th class="px-4 py-3">Indikator</th>
              <th class="px-4 py-3">Parameter</th>
              <th class="px-4 py-3">Bobot</th>
              <th class="px-4 py-3">File</th>
            </tr>
          </thead>
          <tbody id="tbodyEv" class="divide-y">
            <!-- render via JS -->
          </tbody>
          <tfoot class="border-t bg-gray-50">
            <tr>
              <td class="px-4 py-3 font-semibold" colspan="3">Total Bobot</td>
              <td class="px-4 py-3 font-extrabold text-maroon" id="totalBobot">0</td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- Lampiran terkumpul -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="card rounded-2xl border bg-white p-4">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">Lampiran Terkumpul</h3>
        <a href="#" class="text-sm text-maroon hover:underline">Unduh semua (zip)</a>
      </div>
      <div id="filesWrap" class="mt-3 grid md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
        <!-- file cards via JS -->
      </div>
    </div>
  </section>

  <!-- Log Aktivitas -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="card rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span>Log Aktivitas</span>
        <a href="#" class="text-maroon hover:underline">Lihat semua</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3">Waktu</th>
              <th class="px-4 py-3">Pengguna</th>
              <th class="px-4 py-3">Aksi</th>
              <th class="px-4 py-3">Keterangan</th>
              <th class="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y" id="tbodyLog">
            <tr>
              <td class="px-4 py-3 text-gray-600">04 Sep 2025 • 10:20</td>
              <td class="px-4 py-3">admin.sekretariat</td>
              <td class="px-4 py-3">Perbarui Metadata</td>
              <td class="px-4 py-3">Edit judul & urusan</td>
              <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Sukses</span></td>
            </tr>
            <tr>
              <td class="px-4 py-3 text-gray-600">03 Sep 2025 • 16:02</td>
              <td class="px-4 py-3">user.riset1</td>
              <td class="px-4 py-3">Unggah Evidence</td>
              <td class="px-4 py-3">Indikator #1 (Perkada)</td>
              <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Sukses</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-gray-200 no-print">
    <div class="max-w-7xl mx-auto px-4 py-8 text-sm text-gray-600">
      © 2025 SIGAP BRIDA • BRIDA Kota Makassar
    </div>
  </footer>

  <script>
    // ====== Konstanta key storage (samakan dengan halaman Edit & Evidence) ======
    const KEY_META = 'sigap:inovasi:edit:DRAFT';       // dari sigap-evidence.edit.html
    const KEY_EVID = 'sigap:evidence:INOVASI-001';     // contoh key dari autosave di evidence (opsional)

    // ====== Helper ======
    const $ = (sel) => document.querySelector(sel);
    function text(el, v){ if(el) el.textContent = v; }
    function badge(el, label){
      if(!el) return;
      el.textContent = label || '—';
      el.className = 'px-2 py-0.5 rounded bg-gray-100 text-gray-700';
    }
    function fmtDateISO(d){
      if(!d) return '—';
      try{
        const dt = new Date(d);
        if(isNaN(dt)) return '—';
        const dd = String(dt.getDate()).padStart(2,'0');
        const mo = dt.toLocaleString('id-ID',{month:'short'});
        const yy = dt.getFullYear();
        const hh = String(dt.getHours()).padStart(2,'0');
        const mm = String(dt.getMinutes()).padStart(2,'0');
        return `${dd} ${mo} ${yy}${isNaN(dt.getHours())?'':` • ${hh}:${mm}`}`;
      }catch{ return '—'; }
    }
    function statusBadge(text, tone){
      const map = {
        hijau: 'bg-emerald-50 text-emerald-700',
        amber: 'bg-amber-50 text-amber-700',
        gray:  'bg-gray-100 text-gray-700',
        biru:  'bg-blue-50 text-blue-700'
      };
      return `<span class="px-2 py-0.5 rounded ${map[tone]||map.gray}">${text}</span>`;
    }

    // ====== Load metadata (dari localStorage jika ada) ======
    const meta = (() => {
      const raw = localStorage.getItem(KEY_META);
      if(!raw) return null;
      try{ return JSON.parse(raw); }catch{ return null; }
    })();

    // ====== Render header & badges ======
    (function renderHeader(){
      text($('#title'), meta?.judul || 'SIM Air Bersih Terintegrasi');
      badge($('#badgeKlas'), 'Klasifikasi: ' + (meta?.klasifikasi || '—'));
      badge($('#badgeJenis'), 'Jenis: ' + (meta?.jenis || '—'));
      badge($('#badgeUrusan'), 'Urusan: ' + (meta?.urusan || '—'));
      badge($('#badgeInisiator'), 'Inisiator: ' + (meta?.inisiator || '—'));
      badge($('#badgeOPD'), 'OPD: ' + (meta?.opd || '—'));

      text($('#kpiUpdate'), fmtDateISO(meta?.saved_at));
      text($('#kpiKoord'), meta?.koordinat || '—');
    })();

    // ====== Render deskripsi dari Quill HTML ======
    function safeHTML(s){
      return (s||'').replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi, '');
    }
    $('#v_rancang').innerHTML = safeHTML(meta?.rancang_html) || '<p class="text-gray-500">Belum ada isi.</p>';
    $('#v_tujuan').innerHTML  = safeHTML(meta?.tujuan_html)  || '<p class="text-gray-500">Belum ada isi.</p>';
    $('#v_manfaat').innerHTML = safeHTML(meta?.manfaat_html) || '<p class="text-gray-500">Belum ada isi.</p>';
    $('#v_hasil').innerHTML   = safeHTML(meta?.hasil_html)   || '<p class="text-gray-500">Belum ada isi.</p>';

    // ====== Data indikator (judul singkat untuk ringkasan) ======
    const INDICATOR_TITLES = [
      'Regulasi Inovasi Daerah *','Ketersediaan SDM *','Dukungan Anggaran','Alat Kerja','Bimtek Inovasi',
      'Integrasi RKPD','Keterlibatan Aktor','Pelaksana Inovasi','Jejaring Inovasi','Sosialisasi',
      'Pedoman Teknis','Kemudahan Informasi','Kemudahan Proses','Penyelesaian Pengaduan','Layanan Terintegrasi',
      'Replikasi','Kecepatan Penciptaan *','Kemanfaatan *','Monitoring & Evaluasi','Kualitas Inovasi *'
    ];

    // ====== Load evidence dari localStorage jika ada (opsional) ======
    const evid = (() => {
      const raw = localStorage.getItem(KEY_EVID);
      if(!raw) return null;
      try{ return JSON.parse(raw); }catch{ return null; }
    })();

    // Bentuk data ringkasan evidence: array 20 item
    // Jika tidak ada di storage, pakai dummy kosong
    const evidenceRows = evid && Array.isArray(evid)
      ? evid
      : INDICATOR_TITLES.map((t,i)=>({
          no: i+1,
          indikator: t,
          parameter_label: '',
          parameter_weight: 0,
          file_name: '',
          deskripsi: ''
        }));

    // ====== Render ringkasan evidence ======
    const tbodyEv = $('#tbodyEv');
    function rowHTML(ev){
      const done = !!ev.parameter_label && (ev.parameter_weight||0) > 0;
      const tone = done ? 'hijau' : 'amber';
      const label = done ? ev.parameter_label : 'Belum dipilih';
      const bobot = ev.parameter_weight || 0;
      const fname = ev.file_name || '—';
      return `
        <tr data-done="${done?'1':'0'}">
          <td class="px-4 py-3 text-gray-600">${ev.no}</td>
          <td class="px-4 py-3">${INDICATOR_TITLES[ev.no-1]||ev.indikator||'-'}</td>
          <td class="px-4 py-3">${statusBadge(label, tone)}</td>
          <td class="px-4 py-3 font-semibold ${bobot>0?'text-maroon':''}">${bobot}</td>
          <td class="px-4 py-3">${fname}</td>
        </tr>
      `;
    }
    function renderEvidence(filter='all'){
      tbodyEv.innerHTML = evidenceRows
        .filter(r=>{
          if(filter==='complete') return !!r.parameter_label && (r.parameter_weight||0)>0 && !!r.file_name;
          if(filter==='incomplete') return !(!!r.parameter_label && (r.parameter_weight||0)>0 && !!r.file_name);
          return true;
        })
        .map(rowHTML).join('');
      const total = evidenceRows.reduce((s,r)=> s + (r.parameter_weight||0), 0);
      text($('#totalBobot'), String(total));

      // KPI terisi & files
      const filled = evidenceRows.filter(r=> r.parameter_weight>0).length;
      const files = evidenceRows.filter(r=> r.file_name).length;
      text($('#kpiTerisi'), `${filled}`);
      text($('#kpiFiles'), `${files}`);
      text($('#kpiSkor'), `${total}`);
    }
    renderEvidence();
    $('#filterEv').addEventListener('change', e=> renderEvidence(e.target.value));

    // ====== Lampiran terkumpul (gabung dari evidence yang punya file) ======
    const filesWrap = $('#filesWrap');
    function fileIcon(name){
      const ext = (name.split('.').pop()||'').toLowerCase();
      if(['jpg','jpeg','png','gif','webp','svg'].includes(ext))
        return '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-blue-100 text-blue-700">IMG</span>';
      if(['mp4','mov'].includes(ext))
        return '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-purple-100 text-purple-700">VID</span>';
      if(['pdf'].includes(ext))
        return '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-rose-100 text-rose-700">PDF</span>';
      return '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-gray-100 text-gray-700">FILE</span>';
    }
    function renderFiles(){
      const files = evidenceRows.filter(r=> r.file_name);
      if(files.length===0){
        filesWrap.innerHTML = '<p class="text-sm text-gray-500">Belum ada lampiran.</p>';
        return;
      }
      filesWrap.innerHTML = files.map(f=>`
        <div class="rounded-xl border p-3 flex items-center gap-3">
          ${fileIcon(f.file_name)}
          <div class="min-w-0">
            <p class="font-medium text-gray-800 truncate">${f.file_name}</p>
            <p class="text-xs text-gray-500">Indikator #${f.no} • ${INDICATOR_TITLES[f.no-1]}</p>
          </div>
          <div class="ml-auto flex items-center gap-2">
            <a href="#" class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50">View</a>
            <a href="#" class="px-2.5 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Download</a>
          </div>
        </div>
      `).join('');
    }
    renderFiles();

    // ====== Tahapan progress (dummy dari evidence/metadata) ======
    // Sederhana: jika total bobot >= 50 → Uji Coba Berjalan; >= 100 → Penerapan Berjalan
    (function tahap(){
      const total = evidenceRows.reduce((s,r)=> s + (r.parameter_weight||0), 0);
      const inis = total > 0 ? 'Selesai' : 'Belum';
      const uji  = total >= 50 ? 'Berjalan' : 'Belum';
      const terap= total >= 100 ? 'Berjalan' : 'Belum';

      $('#tahapInis').innerHTML = statusBadge(inis, inis==='Selesai'?'hijau':'gray');
      $('#tahapUji').innerHTML  = statusBadge(uji,  uji==='Berjalan'?'amber':'gray');
      $('#tahapTerap').innerHTML= statusBadge(terap,terap==='Berjalan'?'amber':'gray');

      const steps = (inis==='Selesai') + (uji==='Berjalan') + (terap==='Berjalan');
      const pct = Math.round(steps/3*100);
      $('#barTahap').style.width = `${pct}%`;
      text($('#txtTahap'), `${pct}% selesai`);
    })();
  </script>
</body>
</html>
