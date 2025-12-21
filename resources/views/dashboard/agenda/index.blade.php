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
      <td class="px-4 py-3 text-center">${ag.items_count ?? 0}</td>
      <td class="px-4 py-3 flex flex-wrap gap-2">

           <a href="{{ route('sigap-agenda.show') }}?id=${ag.id}"
   class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">Lihat</a>
   <button type="button" onclick="copyShowLink(${ag.id})"
        class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">Salin Link</button>
  <button type="button" onclick="shareWhatsAppText(${ag.id})"
    class="px-3 py-1.5 rounded bg-green-600 hover:bg-green-700 text-white text-sm inline-flex items-center gap-2">
    <svg viewBox="0 0 24 24" class="w-4 h-4 fill-current">
      <path d="M20.52 3.48A11.86 11.86 0 0012.04 0C5.41 0 .04 5.37.04 12c0 2.12.55 4.19 1.6 6.02L0 24l6.2-1.62a11.9 11.9 0 005.84 1.49h.01c6.63 0 12-5.37 12-12a11.9 11.9 0 00-3.53-8.39zM12.05 21.5a9.46 9.46 0 01-4.82-1.32l-.35-.2-3.68.96.98-3.58-.23-.37a9.47 9.47 0 0114.69-11.8 9.46 9.46 0 01-6.59 16.31zm5.49-7.1c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.66.15-.2.3-.76.97-.93 1.17-.17.2-.34.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.74-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.34.45-.5.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.66-1.59-.9-2.18-.24-.58-.49-.5-.66-.51l-.57-.01c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48 0 1.45 1.07 2.85 1.22 3.05.15.2 2.1 3.2 5.08 4.49.71.31 1.27.49 1.7.63.71.23 1.36.2 1.87.12.57-.08 1.76-.72 2-1.41.25-.7.25-1.3.17-1.41-.07-.11-.27-.18-.57-.33z"/>
    </svg>
    WhatsApp
  </button>
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
  function parseAssignees(raw){
    if(!raw) return [];
    try {
      const obj = JSON.parse(raw);
      return [
        ...(obj.users || []).map(u => '• ' + u.name),
        ...(obj.manual || []).map(m => '• ' + m),
      ];
    } catch {
      return ['• ' + raw];
    }
  }


  function composeDescClient(it){
    if(it.mode === 'menugaskan'){
      const list = parseAssignees(it.assignees);
      return `Menugaskan:\n${list.join('\n')}\n\n${it.description}`;
    }
    if(it.mode === 'kepala'){
      return `Kepala Brida, ${it.description}`;
    }
    return it.description;
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
  const ag = (AGENDA_DATA || []).find(x => String(x.id) === String(id));
  if (!ag) {
    alert('Data agenda tidak ditemukan.');
    return;
  }

  const url = `{{ route('sigap-agenda.show') }}?id=${id}`;
  const lines = [];

  lines.push(`🗓 *AGENDA ${ag.unit_title}*`);
  lines.push(`📅 ${formatDateID(ag.date)}`);
  lines.push('');

  (ag.items || []).forEach((it, idx) => {
    // ===== DESKRIPSI UTAMA =====
    if (it.mode === 'menugaskan') {
      lines.push(`${idx+1}️⃣ Menugaskan:`);

      try {
        const data = JSON.parse(it.assignees || '{}');
        (data.users || []).forEach(u => lines.push(`* ${u.name}`));
        (data.manual || []).forEach(m => lines.push(`* ${m}`));
      } catch(e){}

      // lines.push('');
      lines.push(it.description);
    }
    else if (it.mode === 'kepala') {
      lines.push(`${idx+1}️⃣ Kepala Brida, ${it.description}`);
    }
    else {
      lines.push(`${idx+1}️⃣ ${it.description}`);
    }

    // ===== META =====
    if (it.time_text?.trim()) {
      lines.push(`⏰ Waktu: ${it.time_text}`);
    }
    if (it.place?.trim()) {
      lines.push(`📍 Tempat: ${it.place}`);
    }

    lines.push('');
  });

  lines.push('✅ *Agenda telah diverifikasi melalui SIGAP AGENDA*');
  lines.push('');
  lines.push('Untuk informasi lebih lengkap silakan kunjungi 👇');
  lines.push(`🔗 ${url}`);

  const text = lines.join('\n');

  // ===== COPY =====
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).then(() => {
      window.Swal
        ? Swal.fire({ icon:'success', title:'Agenda disalin', timer:1500, showConfirmButton:false })
        : alert('Agenda berhasil disalin.');
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
function shareWhatsAppText(id){
  const ag = (AGENDA_DATA || []).find(x => String(x.id) === String(id));
  if (!ag) {
    alert('Data agenda tidak ditemukan.');
    return;
  }

  const url = `{{ route('sigap-agenda.show') }}?id=${id}`;
  const lines = [];

  lines.push(`🗓 *AGENDA ${ag.unit_title}*`);
  lines.push(`📅 ${formatDateID(ag.date)}`);
  lines.push('');

  (ag.items || []).forEach((it, idx) => {
    if (it.mode === 'menugaskan') {
      lines.push(`${idx+1}️⃣ Menugaskan:`);

      try {
        const data = JSON.parse(it.assignees || '{}');
        (data.users || []).forEach(u => lines.push(`* ${u.name}`));
        (data.manual || []).forEach(m => lines.push(`* ${m}`));
      } catch(e){}

      lines.push(it.description);
    }
    else if (it.mode === 'kepala') {
      lines.push(`${idx+1}️⃣ Kepala Brida, ${it.description}`);
    }
    else {
      lines.push(`${idx+1}️⃣ ${it.description}`);
    }

    if (it.time_text?.trim()) lines.push(`⏰ Waktu: ${it.time_text}`);
    if (it.place?.trim()) lines.push(`📍 Tempat: ${it.place}`);
    lines.push('');
  });

  lines.push('✅ *Agenda telah diverifikasi melalui SIGAP AGENDA*');
  lines.push('');
  lines.push('Untuk informasi lebih lengkap silakan kunjungi 👇');
  lines.push(`🔗 ${url}`);

  const text = lines.join('\n');

  // 1. copy ke clipboard
  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text).catch(()=>{});
  } else {
    manualCopy(text);
  }

  // 2. buka WhatsApp
  window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank');
}

</script>

