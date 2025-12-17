@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Agenda</h1>
      <p class="text-sm text-gray-600 mt-1">
        Gunakan filter tanggal untuk menampilkan agenda. Tekan <b>Share WA</b> untuk membagikan agenda sebagai gambar dengan watermark verifikasi.
      </p>
    </div>

  @hasrole('admin|verificator')
    <a href="{{ route('sigap-agenda.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" d="M12 5v14M5 12h14"/>
      </svg>
      Tambahkan Agenda
    </a>
  @endhasrole
  </div>
</section>

<!-- FILTER -->
<section class="max-w-7xl mx-auto px-4 -mt-3">
  <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6">
    <form id="filterForm" class="flex flex-col sm:flex-row items-start sm:items-end gap-3" onsubmit="filterAgenda(event)">
      <div class="flex-1 w-full">
        <label class="block text-sm font-semibold text-gray-700">Tanggal</label>
        <input id="filterDate" type="date"
               class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
      </div>
      <button type="submit" class="px-5 py-2.5 rounded-xl bg-maroon text-white hover:bg-maroon-800">Terapkan</button>
      <button type="button" onclick="resetFilter()" class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50">Bersihkan</button>
    </form>
  </div>
</section>

<!-- TABEL -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="text-left px-4 py-3">Tanggal</th>
            <th class="text-left px-4 py-3">Unit / Pejabat</th>
            <th class="text-center px-4 py-3">#Kegiatan</th>
            <th class="text-left px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody id="agendaRows" class="divide-y divide-gray-100"></tbody>
      </table>
    </div>
  </div>
</section>
@endsection

<script>
// ====== DATA DARI SERVER ======
const AGENDA_DATA = @json($agendas ?? []);
const CSRF = '{{ csrf_token() }}';
const SHARE_API = "{{ route('sigap-agenda.share-image') }}"; // POST {id}

// ====== INIT ======
document.addEventListener('DOMContentLoaded', () => renderTable(AGENDA_DATA));

// ====== TABEL (biarkan sesuai punyamu) ======
function renderTable(data){
  const tbody = document.getElementById('agendaRows');
  tbody.innerHTML = '';
  if(!data.length){
    tbody.innerHTML = `<tr><td colspan="4" class="text-center text-gray-600 py-6">Tidak ada agenda.</td></tr>`;
    return;
  }
  data.forEach(ag=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="px-4 py-3 font-medium text-gray-900">${formatDateID(ag.date)}</td>
      <td class="px-4 py-3">${ag.unit_title}</td>
      <td class="px-4 py-3 text-center">${(ag.items?.length ?? ag.items_count ?? 0)}</td>
      <td class="px-4 py-3 flex flex-wrap gap-2">
        <button type="button" onclick="shareWA(${ag.id})"
                class="px-3 py-1.5 rounded bg-maroon text-white hover:bg-maroon-800 text-sm">Share WA</button>

           <a href="{{ route('sigap-agenda.show') }}?id=${ag.id}"
   class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">Lihat</a>
   <button type="button" onclick="copyShowLink(${ag.id})"
        class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">Salin Link</button>

        @hasrole('admin|verificator')
                <a href="{{ route('sigap-agenda.edit') }}?id=${ag.id}"
           class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">Edit</a>
                <button type="button" onclick="confirmHapus(${ag.id}, \`${(ag.unit_title||'').replace(/`/g,'\\`')}\`, \`${ag.date}\`)"
                class="px-3 py-1.5 rounded border border-red-300 text-red-600 hover:bg-red-50 text-sm">Hapus</button>
        @endhasrole
      </td>`;
    tbody.appendChild(tr);
  });
}

// ====== SHARE: server generate 1 gambar panjang ======
async function shareWA(id){
  let res;
  try{
    res = await fetch(SHARE_API, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json'
      },
      body: JSON.stringify({ id })
    });
  }catch(e){
    alert('Gagal menghubungi server.');
    return;
  }
  if(!res.ok){ alert('Gagal membuat gambar agenda.'); return; }
  const data = await res.json();
  if(!data.ok){ alert('Gagal membuat gambar agenda.'); return; }

  const imageUrl = data.image_url;
  const text = data.text;

  // Web Share v2 (share file)
  try{
    const blob = await fetch(imageUrl, {cache:'no-store'}).then(r=>r.blob());
    const file = new File([blob], `agenda-${id}.jpg`, {type:'image/jpeg'});
    if(navigator.canShare && navigator.canShare({ files: [file] })){
      await navigator.share({ files: [file], text });
      return;
    }
  }catch(e){}

  // fallback: buka file + wa.me untuk teks
  window.open(imageUrl, '_blank');
  window.open('https://wa.me/?text='+encodeURIComponent(text), '_blank');
}

// ====== UTIL ======
function formatDateID(d){
  return new Date(d).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric'});
}


</script>
<script>
// ================== DELETE (Fix: ensure form exists before submit) ==================

// Buat form hapus (dipanggil saat DOM siap ATAU saat submit jika belum ada)
function buildDeleteForm(){
  const exist = document.getElementById('formDeleteAgenda');
  if (exist) return exist;

  const f = document.createElement('form');
  f.id = 'formDeleteAgenda';
  f.method = 'POST';
  f.action = "{{ route('sigap-agenda.delete') }}";
  f.style.display = 'none';

  // CSRF + input id
  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = '_token';
  csrf.value = "{{ csrf_token() }}";

  const idInput = document.createElement('input');
  idInput.type = 'hidden';
  idInput.name = 'id';
  idInput.value = '';

  f.appendChild(csrf);
  f.appendChild(idInput);

  // Pastikan <body> sudah ada; kalau belum, tunda sampai DOM siap
  if (document.body) {
    document.body.appendChild(f);
  } else {
    document.addEventListener('DOMContentLoaded', () => document.body.appendChild(f), { once: true });
  }
  return f;
}

// Panggil saat DOM siap untuk mencegah document.body null
document.addEventListener('DOMContentLoaded', buildDeleteForm);

// Konfirmasi hapus (SweetAlert jika ada, fallback confirm)
function confirmHapus(id, unitTitle = '', dateStr = ''){
  const agendaTitle = unitTitle || 'Agenda';
  const dateHuman   = formatDateID(dateStr || '');

  if (window.Swal) {
    const html = `
      <div class="text-left">
        <div class="font-semibold text-gray-800">${agendaTitle}</div>
        <div class="text-xs text-gray-500 mt-1">${dateHuman}</div>
        <div class="text-xs text-gray-500 mt-2">Tindakan ini tidak bisa dibatalkan.</div>
      </div>
    `;
    Swal.fire({
      icon: 'warning',
      title: 'Hapus agenda ini?',
      html,
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#7a2222',
      focusCancel: true
    }).then(res => { if (res.isConfirmed) submitDelete(id); });
    return;
  }

  if (confirm(`Hapus agenda ini?\n${agendaTitle}\n${dateHuman}\n\nTindakan ini tidak bisa dibatalkan.`)){
    submitDelete(id);
  }
}

// Submit form hapus — jika form belum ada (karena skrip dirender di <head>), buat dulu
function submitDelete(id){
  let form = document.getElementById('formDeleteAgenda');
  if (!form) form = buildDeleteForm();

  const idInput = form.querySelector('input[name="id"]');
  if (!idInput) {
    const inp = document.createElement('input');
    inp.type = 'hidden';
    inp.name = 'id';
    form.appendChild(inp);
  }
  form.querySelector('input[name="id"]').value = id;

  // Tambah proteksi double submit
  if (form.dataset.submitting === '1') return;
  form.dataset.submitting = '1';

  form.submit();
}
</script>

<script>
// ===== Helpers untuk teks =====
function composeDescClient(it){
  const mode = (it.mode || 'kepala').toLowerCase();
  if (mode === 'kepala') return `Kepala Brida, ${it.description}`;
  if (mode === 'menugaskan') {
    const who = (it.assignees || '').trim();
    return `Menugaskan ${who ? (who + ', ') : ''}${it.description}`;
  }
  return it.description || '';
}

function buildAgendaShareText(id){
  const ag = (AGENDA_DATA || []).find(x => String(x.id) === String(id));
  const url = `{{ route('sigap-agenda.show') }}?id=${id}`;
  if (!ag) {
    return `Untuk informasi lebih lengkap silakan kunjungi 👇\n🔗 ${url}`;
  }

  const lines = [];
  lines.push(`🗓 *AGENDA ${ag.unit_title || ''}*`);
  lines.push(`📅 ${formatDateID(ag.date || '')}`);
  lines.push('');

  const items = Array.isArray(ag.items) ? ag.items : [];
  items.forEach((it, idx) => {
    lines.push(`${idx+1}️⃣ ${composeDescClient(it)}`);
    if ((it.time_text || '').trim() !== '') {
      lines.push(`⏰ Waktu: ${it.time_text}`);
    }
    if ((it.place || '').trim() !== '') {
      lines.push(`📍 Tempat: ${it.place}`);
    }
    lines.push('');
  });

  lines.push('✅ *Agenda telah diverifikasi melalui SIGAP AGENDA*');
  lines.push('');
  lines.push('Untuk informasi lebih lengkap silakan kunjungi 👇');
  lines.push(`🔗 ${url}`);


  return lines.join('\n');
}


// ====== Salin Link + Ringkasan Kegiatan ======
function copyShowLink(id){
  const text = buildAgendaShareText(id);

  // Clipboard API (aman di HTTPS)
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(()=>{
      if (window.Swal) {
        Swal.fire({ icon:'success', title:'Teks disalin', text:'Ringkasan + tautan sudah disalin.', timer:1500, showConfirmButton:false });
      } else {
        alert('Ringkasan + tautan sudah disalin.');
      }
    }).catch(()=>{
      manualCopy(text);
    });
  } else {
    manualCopy(text);
  }
}

function manualCopy(text){
  const ta = document.createElement('textarea');
  ta.value = text;
  ta.setAttribute('readonly','');
  ta.style.position = 'absolute';
  ta.style.left = '-9999px';
  document.body.appendChild(ta);
  ta.select();
  try { document.execCommand('copy'); } catch(e) {}
  document.body.removeChild(ta);
  if (window.Swal) {
    Swal.fire({ icon:'success', title:'Teks disalin', text:'Ringkasan + tautan sudah disalin.', timer:1500, showConfirmButton:false });
  } else {
    alert('Ringkasan + tautan sudah disalin.');
  }
}
</script>

