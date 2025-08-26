<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>SIGAP Pegawai — SIGAP BRIDA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme:{ extend:{ colors:{ maroon:{
      50:'#fdf7f7',100:'#faeeee',200:'#f0d1d1',300:'#e2a8a8',400:'#c86f6f',
      500:'#a64040',600:'#8f2f2f',700:'#7a2222',800:'#661b1b',900:'#4a1313',DEFAULT:'#7a2222'} } } } }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}</style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <header class="border-b border-maroon/10 bg-white sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="index.html" class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500 -mt-0.5">Sistem Informasi Gabungan Arsip & Privasi</p>
        </div>
      </a>
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="admin-dashboard.html" class="hover:text-maroon">Dashboard</a>
        <a href="sigap-dokumen.html" class="hover:text-maroon">Dokumen</a>
        <a href="permintaan-akses.html" class="hover:text-maroon">Permintaan Akses</a>
        <a href="kode-akses.html" class="hover:text-maroon">Kode Akses</a>
        <a href="log-aktivitas.html" class="hover:text-maroon">Log</a>
        <a href="login.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Logout</a>
      </nav>
    </div>
  </header>

  <!-- Breadcrumb -->
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="admin-dashboard.html" class="hover:text-maroon">Admin</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">SIGAP Pegawai</li>
    </ol>
  </nav>

  <!-- Header -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Daftar Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola data pegawai untuk akses dokumen dan arsip privasi.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="pegawai-tambah.html" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
        <button id="btnExport" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Export CSV</button>
      </div>
    </div>
  </section>

  <!-- Filters -->
  <section class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-7 gap-3" onsubmit="event.preventDefault(); page=1; render();">
        <div class="lg:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Cari</label>
          <input id="f_q" type="search" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama / Username / NIP / Unit">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Unit</label>
          <select id="f_unit" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>Sekretariat A</option>
            <option>Bidang Riset</option>
            <option>TI</option>
            <option>Keuangan</option>
            <option>Humas</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Role</label>
          <select id="f_role" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>admin</option>
            <option>verifikator</option>
            <option>pegawai</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select id="f_status" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Urutkan</label>
          <select id="sort" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="latest">Terbaru</option>
            <option value="name">Nama (A-Z)</option>
            <option value="unit">Unit (A-Z)</option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Terapkan</button>
          <button type="reset" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="resetFilters()">Reset</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span id="countInfo">Menampilkan 0 pegawai</span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Tampilkan</label>
          <select id="pageSize" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option>10</option>
            <option selected>25</option>
            <option>50</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white">
            <tr class="text-left border-b">
              <th class="px-4 py-3">Pegawai</th>
              <th class="px-4 py-3">NIP</th>
              <th class="px-4 py-3">Unit</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3">Kontak</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody" class="divide-y">
            <!-- rows via JS -->
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 flex items-center justify-between">
        <p id="pageInfo" class="text-sm text-gray-600">Halaman 1</p>
        <div class="inline-flex overflow-hidden rounded-md border border-gray-200">
          <button id="prevBtn" class="px-3 py-2 text-sm hover:bg-gray-50">Sebelumnya</button>
          <button id="nextBtn" class="px-3 py-2 text-sm hover:bg-gray-50">Berikutnya</button>
        </div>
      </div>
    </div>

    <!-- Empty -->
    <div id="empty" class="mt-6 hidden">
      <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
        <p class="text-sm text-gray-700">Belum ada data pegawai.</p>
        <a href="pegawai-profile.html" class="inline-flex mt-3 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-8 text-sm text-gray-600">
      © 2025 SIGAP BRIDA • BRIDA Kota Makassar
    </div>
  </footer>

<script>
  // ====== DEMO DATA (pakai yang sama dgn kode-akses-form) ======
  const USERS_KEY = 'sigap_users_demo';
  if(!localStorage.getItem(USERS_KEY)){
    const seedUsers = [
      {username:'admin.sekretariat', name:'Admin Sekretariat', unit:'Sekretariat A', role:'admin',    nip:'19790101 200501 1 001', email:'admin@brida.go.id', phone:'0411-123456', status:'active', avatar:''},
      {username:'user.riset1',       name:'Andi Rahman',       unit:'Bidang Riset',  role:'pegawai',  nip:'19910505 201501 1 010', email:'andi@brida.go.id',  phone:'0411-111111', status:'active', avatar:''},
      {username:'user.persuratan',   name:'Budi Santoso',      unit:'Sekretariat A', role:'verifikator', nip:'19880202 201201 1 007', email:'budi@brida.go.id', phone:'0411-222222', status:'active', avatar:''},
      {username:'user.it',           name:'Muh. Yusuf',        unit:'TI',            role:'admin',    nip:'19850505 201003 1 005', email:'yusuf@brida.go.id', phone:'0411-333333', status:'inactive', avatar:''},
      {username:'user.keu2',         name:'Sitti Aulia',       unit:'Keuangan',      role:'pegawai',  nip:'19921212 201703 2 003', email:'aulia@brida.go.id', phone:'0411-444444', status:'active', avatar:''},
      {username:'user.humas',        name:'Nur Aksara',        unit:'Humas',         role:'pegawai',  nip:'19940606 201903 1 002', email:'nur@brida.go.id',   phone:'0411-555555', status:'active', avatar:''},
    ];
    localStorage.setItem(USERS_KEY, JSON.stringify(seedUsers));
  }

  // ====== HELPERS ======
  const $ = (s)=>document.querySelector(s);
  const tbody   = $('#tbody');
  const empty   = $('#empty');
  const countEl = $('#countInfo');
  const pageInfo= $('#pageInfo');
  const sortSel = $('#sort');
  const pageSizeSel = $('#pageSize');

  let page = 1;
  function loadUsers(){ try{ return JSON.parse(localStorage.getItem(USERS_KEY) || '[]'); }catch{ return []; } }
  function saveUsers(list){ localStorage.setItem(USERS_KEY, JSON.stringify(list)); }
  function esc(s=''){ return String(s).replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;' }[m])); }
  function badgeRole(r){
    const map = {admin:'bg-purple-50 text-purple-700', verifikator:'bg-amber-50 text-amber-700', pegawai:'bg-gray-100 text-gray-700'};
    return `<span class="px-2 py-0.5 rounded text-xs ${map[r]||'bg-gray-100 text-gray-700'}">${esc(r)}</span>`;
  }
  function badgeStatus(s){
    return s==='active'
      ? '<span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Aktif</span>'
      : '<span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">Nonaktif</span>';
  }
  function avatarHtml(u){
    if(u.avatar){ return `<img src="${esc(u.avatar)}" class="w-10 h-10 rounded-full object-cover" alt="">`; }
    const ini = (u.name||'?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
    return `<span class="w-10 h-10 rounded-full bg-maroon/10 text-maroon flex items-center justify-center text-xs font-bold">${ini}</span>`;
  }

  // ====== FILTERS ======
  function getFilters(){
    return {
      q: $('#f_q').value.toLowerCase().trim(),
      unit: $('#f_unit').value,
      role: $('#f_role').value,
      status: $('#f_status').value,
      sort: sortSel.value,
      size: parseInt(pageSizeSel.value,10)||25
    };
  }
  function resetFilters(){
    $('#f_q').value=''; $('#f_unit').value=''; $('#f_role').value=''; $('#f_status').value=''; sortSel.value='latest';
    page=1; render();
  }
  function getFiltered(){
    const f = getFilters();
    let arr = loadUsers();

    if(f.q){
      arr = arr.filter(u => (u.name+' '+u.username+' '+(u.nip||'')+' '+(u.unit||'')).toLowerCase().includes(f.q));
    }
    if(f.unit){ arr = arr.filter(u => u.unit===f.unit); }
    if(f.role){ arr = arr.filter(u => u.role===f.role); }
    if(f.status){ arr = arr.filter(u => (u.status||'active')===f.status); }

    if(f.sort==='name'){ arr.sort((a,b)=> a.name.localeCompare(b.name)); }
    else if(f.sort==='unit'){ arr.sort((a,b)=> (a.unit||'').localeCompare(b.unit||'')); }
    else { arr.sort((a,b)=> (b.updatedAt||0)-(a.updatedAt||0)); }

    return arr;
  }

  // ====== RENDER ======
  function render(){
    const data = getFiltered();
    const size = getFilters().size;
    const total = data.length;
    const totalPages = Math.max(1, Math.ceil(total/size));
    if(page>totalPages) page = totalPages;

    const start = (page-1)*size;
    const slice = data.slice(start, start+size);

    tbody.innerHTML = '';
    if(slice.length===0){
      empty.classList.remove('hidden');
      countEl.textContent = 'Menampilkan 0 pegawai';
    } else {
      empty.classList.add('hidden');
      slice.forEach(u=>{
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50';
        tr.innerHTML = `
          <td class="px-4 py-3">
            <div class="flex items-center gap-3">
              ${avatarHtml(u)}
              <div>
                <div class="font-semibold text-gray-900">${esc(u.name)}</div>
                <div class="text-xs text-gray-600">${esc(u.username)}</div>
              </div>
            </div>
          </td>
          <td class="px-4 py-3">${esc(u.nip||'-')}</td>
          <td class="px-4 py-3">${esc(u.unit||'-')}</td>
          <td class="px-4 py-3">${badgeRole(u.role||'pegawai')}</td>
          <td class="px-4 py-3">
            <div class="text-gray-700">${esc(u.email||'-')}</div>
            <div class="text-xs text-gray-500">${esc(u.phone||'')}</div>
          </td>
          <td class="px-4 py-3">${badgeStatus(u.status||'active')}</td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap gap-2">
              <a href="pegawai-single.html?u=${encodeURIComponent(u.username)}" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-xs">View</a>
              <a href="pegawai-profile.html?u=${encodeURIComponent(u.username)}" class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Edit</a>
            </div>
          </td>
        `;
        tbody.appendChild(tr);
      });
      countEl.textContent = `Menampilkan ${slice.length} dari ${total} pegawai`;
    }

    pageInfo.textContent = `Halaman ${page} dari ${Math.max(1, Math.ceil(total/size))}`;
    $('#prevBtn').disabled = page<=1;
    $('#nextBtn').disabled = page>=Math.max(1, Math.ceil(total/size));
  }

  // ====== PAGINATION & SORT EVENTS ======
  $('#prevBtn').addEventListener('click', ()=>{ if(page>1){ page--; render(); }});
  $('#nextBtn').addEventListener('click', ()=>{ page++; render(); });
  pageSizeSel.addEventListener('change', ()=>{ page=1; render(); });
  $('#sort').addEventListener('change', ()=>{ page=1; render(); });

  // ====== EXPORT CSV ======
  document.getElementById('btnExport').addEventListener('click', ()=>{
    const data = getFiltered();
    const rows = [['Username','Nama','NIP','Unit','Role','Email','Telepon','Status']];
    data.forEach(u=>{
      rows.push([u.username,u.name,u.nip||'',u.unit||'',u.role||'',u.email||'',u.phone||'',u.status||'active']);
    });
    const csv = rows.map(r=> r.map(x=>`"${String(x).replace(/"/g,'""')}"`).join(',')).join('\n');
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href=url; a.download='sigap-pegawai.csv'; a.click(); URL.revokeObjectURL(url);
  });

  // ====== INIT ======
  render();
</script>

<noscript>
  <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md mt-4">
    JavaScript dinonaktifkan. Halaman ini memerlukan JavaScript untuk memuat tabel pegawai.
  </div>
</noscript>

</body>
</html>
