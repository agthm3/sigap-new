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

    <a href="{{ route('sigap-agenda.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" d="M12 5v14M5 12h14"/>
      </svg>
      Tambahkan Agenda
    </a>
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

@push('scripts')
<script>
// ====== DATA DARI SERVER ======
const AGENDA_DATA = @json($agendas ?? []);

// ====== INIT ======
document.addEventListener('DOMContentLoaded', () => renderTable(AGENDA_DATA));

// ====== TABEL ======
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
        <a href="{{ route('sigap-agenda.edit') }}?id=${ag.id}"
           class="px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-sm">Edit</a>
        <button type="button" onclick="confirmHapus(${ag.id}, \`${(ag.unit_title||'').replace(/`/g,'\\`')}\`, \`${ag.date}\`)"
                class="px-3 py-1.5 rounded border border-red-300 text-red-600 hover:bg-red-50 text-sm">Hapus</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// ====== FILTER ======
function filterAgenda(e){
  e.preventDefault();
  const val = document.getElementById('filterDate').value;
  if(!val){ renderTable(AGENDA_DATA); return; }
  const filtered = AGENDA_DATA.filter(a => a.date === val);
  renderTable(filtered);
}
function resetFilter(){
  document.getElementById('filterForm').reset();
  renderTable(AGENDA_DATA);
}

// ====================================================================
// =====================  SHARE: LONG IMAGE FIRST  ====================
// ====================================================================

async function shareWA(id){
  const ag = AGENDA_DATA.find(x => x.id === id);
  if(!ag) return;

  const link = `https://sigap.brida.makassarkota.go.id/agenda/${id}`;

  // 1) coba render satu gambar panjang
  let blob = await renderAgendaLongImage(ag, link);

  // 2) kalau gagal (null) atau terlalu tinggi untuk device → fallback ke slide
  if(!blob){
    const blobs = await renderAgendaImagesPaginated(ag, link);
    const files = blobs.map((b,i)=> new File([b], `agenda-${id}-${i+1}.png`, {type:'image/png'}));
    const text = shareText(ag, link);
    if(navigator.canShare && navigator.canShare({ files: files })){
      await navigator.share({ files, text });
    }else{
      files.forEach((f,i)=> downloadBlob(blobs[i], f.name));
      window.open('https://wa.me/?text='+encodeURIComponent(text), '_blank');
    }
    return;
  }

  const file = new File([blob], `agenda-${id}.png`, {type:'image/png'});
  const text = shareText(ag, link);

  if(navigator.canShare && navigator.canShare({ files:[file] })){
    await navigator.share({ files:[file], text });
  }else{
    downloadBlob(blob, file.name);
    window.open('https://wa.me/?text='+encodeURIComponent(text), '_blank');
  }
}

function shareText(ag, link){
  return `AGENDA ${(ag.unit_title||'').toUpperCase()}
${formatDateID(ag.date)}
${link}`;
}

// ================= LONG IMAGE RENDERER (tinggi dinamis) =================
async function renderAgendaLongImage(ag, link){
  const BASE_W   = 1080;  // portrait lebar standar (jangan diubah ke 1:1)
  const P        = 64;    // padding
  const HDR_BAR  = 110;
  const HDR_GAP  = 50;
  const HEADER_H = HDR_BAR + HDR_GAP; // area header konten
  const FOOTER_H = 90;

  // batas aman tinggi beda-beda per device; kita coba besar dulu
  // jika toBlob gagal, kita fallback ke slide.
  const MAX_SAFE = 12000; // naikin kalau perlu, tapi di beberapa device di hard-cap 8192/10000

  // untuk ukur teks
  const mCanvas = document.createElement('canvas');
  mCanvas.width = 10; mCanvas.height = 10;
  const mctx = mCanvas.getContext('2d');

  // hitung tinggi per item
  function measureItem(it){
    let h = 0;
    if(it.assignees && String(it.assignees).trim()){ h += 48; }
    mctx.font = '500 28px sans-serif';
    h += measureWrapHeight(mctx, composeDesc(it), BASE_W - P*2 - 56, 34) + 6;
    mctx.font = '700 22px sans-serif';
    h += measureWrapHeight(mctx, `Waktu : ${it.time_text}`, BASE_W - P*2 - 56, 28);
    h += measureWrapHeight(mctx, `Tempat : ${it.place}`,   BASE_W - P*2 - 56, 28);
    h += 18 + 22; // separator + gap
    return h;
  }
  const items = (ag.items || []);
  const totalItemsH = items.reduce((s,it)=> s + measureItem(it), 0);

  let H = P*2 + HEADER_H + FOOTER_H + totalItemsH;
  if (H > MAX_SAFE) H = MAX_SAFE; // batasi agar tidak crash; sisa item tetap digambar sampai batas

  // retina scale
  const SCALE = Math.min(Math.max(window.devicePixelRatio || 1, 2), 3);
  const c = document.createElement('canvas');
  c.width  = Math.floor(BASE_W * SCALE);
  c.height = Math.floor(H * SCALE);
  const ctx = c.getContext('2d');
  ctx.scale(SCALE, SCALE);

  // === background
  const grad = ctx.createLinearGradient(0,0,0,H);
  grad.addColorStop(0,'#ffffff'); grad.addColorStop(1,'#f3f4f6');
  ctx.fillStyle = grad; ctx.fillRect(0,0,BASE_W,H);
  drawWatermark(ctx, BASE_W, H);

  // === header
  ctx.fillStyle='#7a2222';
  ctx.fillRect(P, P, BASE_W - P*2, HDR_BAR);

  ctx.fillStyle='#fff'; ctx.textAlign='left';
  ctx.font='800 44px sans-serif';
  ctx.fillText('AGENDA', P+28, P+62);
  ctx.font='700 28px sans-serif';
  ctx.fillText(ag.unit_title, P+28, P+102);

  // tanggal
  ctx.fillStyle='#111'; ctx.font='700 30px sans-serif';
  ctx.fillText(formatDateID(ag.date), P, P + HDR_BAR + 54);

  // === body
  let y = P + HEADER_H + 36;
  for (let i=0; i<items.length; i++){
    const it = items[i];
    // kalau sudah mendekati batas tinggi aman → hentikan biar tidak kepotong footer
    if (y > H - FOOTER_H - 40) break;

    // nomor
    ctx.font='800 28px sans-serif'; ctx.fillStyle='#111';
    ctx.fillText(`${i+1}.`, P, y+28);

    const x = P + 52;

    // badge assignees
    if(it.assignees && String(it.assignees).trim()){
      const names = String(it.assignees);
      const maxW  = BASE_W - P*2 - 56;
      const badgeW = Math.min(ctx.measureText(names).width + 40, maxW);
      ctx.fillStyle='rgba(122,34,34,0.10)';
      ctx.fillRect(x, y, badgeW, 40);
      ctx.strokeStyle='#7a2222'; ctx.strokeRect(x, y, badgeW, 40);
      ctx.fillStyle='#7a2222'; ctx.font='800 22px sans-serif';
      ctx.fillText(fitText(ctx, names, badgeW - 16), x+14, y+26);
      y += 52;
    }

    // deskripsi
    ctx.fillStyle='#111'; ctx.font='500 28px sans-serif';
    y = drawTextWrap(ctx, composeDesc(it), x, y+34, BASE_W - P*2 - 56, 34) + 6;

    // waktu & tempat
    ctx.font='700 22px sans-serif'; ctx.fillStyle='#444';
    y = drawTextWrap(ctx, `Waktu : ${it.time_text}`, x, y+26, BASE_W - P*2 - 56, 28);
    y = drawTextWrap(ctx, `Tempat : ${it.place}`,   x, y+26, BASE_W - P*2 - 56, 28);

    // separator
    y += 18;
    ctx.strokeStyle='#e5e7eb'; ctx.beginPath(); ctx.moveTo(P,y); ctx.lineTo(BASE_W - P, y); ctx.stroke();
    y += 22;
  }

  // === footer
  ctx.fillStyle='#6b7280'; ctx.font='700 22px sans-serif'; ctx.textAlign='left';
  ctx.fillText('Diverifikasi melalui SIGAP AGENDA', P, H - 64);
  ctx.fillText(link, P, H - 36);

  // export
  try{
    const blob = await new Promise(res => c.toBlob(b => res(b), 'image/png', 0.95));
    return blob;
  }catch(e){
    console.warn('toBlob failed for long image, fallback to slides', e);
    return null;
  }
}

// ================= FALLBACK: PAGINATED SLIDES ======================
async function renderAgendaImagesPaginated(ag, link){
  const W = 1080, P = 64;
  const HEADER_BAR = 110, HEADER_GAP = 50, HEADER_H = HEADER_BAR + HEADER_GAP;
  const FOOTER_H = 90, MAX_H = 1800;
  const BODY_MAX = MAX_H - (P*2) - HEADER_H - FOOTER_H;

  const mCanvas = document.createElement('canvas');
  mCanvas.width = 10; mCanvas.height = 10;
  const mctx = mCanvas.getContext('2d');

  function measureItem(it){
    let h = 0;
    if(it.assignees && String(it.assignees).trim()){ h += 48; }
    mctx.font = '500 28px sans-serif';
    h += measureWrapHeight(mctx, composeDesc(it), W - P*2 - 56, 34) + 6;
    mctx.font = '700 22px sans-serif';
    h += measureWrapHeight(mctx, `Waktu : ${it.time_text}`, W - P*2 - 56, 28);
    h += measureWrapHeight(mctx, `Tempat : ${it.place}`,   W - P*2 - 56, 28);
    h += 18 + 22;
    return h;
  }

  const items = (ag.items || []);
  const heights = items.map(measureItem);

  // chunk
  const pages = [];
  let page=[], used=0;
  for(let i=0;i<items.length;i++){
    const h = heights[i];
    if(page.length && used + h > BODY_MAX){ pages.push(page); page=[]; used=0; }
    page.push({item:items[i], index:i});
    used += h;
  }
  if(page.length) pages.push(page);
  if(pages.length === 0) pages.push([]);

  const blobs = [];
  for(let p=0;p<pages.length;p++){
    const contentH = Math.min(BODY_MAX, pages[p].reduce((s,x)=> s+measureItem(x.item), 0));
    const H = P*2 + HEADER_H + FOOTER_H + contentH;

    const SCALE = Math.min(Math.max(window.devicePixelRatio || 1, 2), 3);
    const c = document.createElement('canvas');
    c.width  = Math.floor(W * SCALE);
    c.height = Math.floor(H * SCALE);
    const ctx = c.getContext('2d');
    ctx.scale(SCALE, SCALE);

    // background
    const grad = ctx.createLinearGradient(0,0,0,H);
    grad.addColorStop(0,'#ffffff'); grad.addColorStop(1,'#f3f4f6');
    ctx.fillStyle = grad; ctx.fillRect(0,0,W,H);
    drawWatermark(ctx,W,H);

    // header
    ctx.fillStyle='#7a2222';
    ctx.fillRect(P,P,W-P*2,HEADER_BAR);
    ctx.fillStyle='#fff'; ctx.textAlign='left';
    ctx.font='800 44px sans-serif'; ctx.fillText('AGENDA',P+28,P+62);
    ctx.font='700 28px sans-serif'; ctx.fillText(ag.unit_title,P+28,P+102);

    // tanggal + nomor halaman
    ctx.fillStyle='#111'; ctx.font='700 30px sans-serif';
    ctx.fillText(formatDateID(ag.date), P, P+HEADER_BAR+54);
    if(pages.length>1){
      ctx.textAlign='right'; ctx.fillStyle='#6b7280'; ctx.font='600 20px sans-serif';
      ctx.fillText(`Hal. ${p+1}/${pages.length}`, W-P, P+HEADER_BAR+54);
      ctx.textAlign='left';
    }

    // body
    let y = P + HEADER_H + 36;
    pages[p].forEach(({item,index})=>{
      ctx.font='800 28px sans-serif'; ctx.fillStyle='#111';
      ctx.fillText(`${index+1}.`, P, y+28);

      const x = P + 52;

      if(item.assignees && String(item.assignees).trim()){
        const names = String(item.assignees);
        const maxW  = W - P*2 - 56;
        const badgeW = Math.min(ctx.measureText(names).width + 40, maxW);
        ctx.fillStyle='rgba(122,34,34,0.10)';
        ctx.fillRect(x, y, badgeW, 40);
        ctx.strokeStyle='#7a2222'; ctx.strokeRect(x, y, badgeW, 40);
        ctx.fillStyle='#7a2222'; ctx.font='800 22px sans-serif';
        ctx.fillText(fitText(ctx, names, badgeW - 16), x+14, y+26);
        y += 52;
      }

      ctx.fillStyle='#111'; ctx.font='500 28px sans-serif';
      y = drawTextWrap(ctx, composeDesc(item), x, y+34, W - P*2 - 56, 34) + 6;

      ctx.font='700 22px sans-serif'; ctx.fillStyle='#444';
      y = drawTextWrap(ctx, `Waktu : ${item.time_text}`, x, y+26, W - P*2 - 56, 28);
      y = drawTextWrap(ctx, `Tempat : ${item.place}`,   x, y+26, W - P*2 - 56, 28);

      y += 18;
      ctx.strokeStyle='#e5e7eb'; ctx.beginPath(); ctx.moveTo(P,y); ctx.lineTo(W-P,y); ctx.stroke();
      y += 22;
    });

    // footer
    ctx.fillStyle='#6b7280'; ctx.font='700 22px sans-serif'; ctx.textAlign='left';
    ctx.fillText('Diverifikasi melalui SIGAP AGENDA', P, H-64);
    ctx.fillText(link, P, H-36);

    const blob = await new Promise(res=> c.toBlob(b=>res(b), 'image/png', 0.95));
    blobs.push(blob);
  }
  return blobs;
}

// ================= DRAW/MEASURE HELPERS =================
function composeDesc(it){
  const mode = it.mode || 'kepala';
  if(mode==='kepala') return `Kepala Brida, ${it.description}`;
  if(mode==='menugaskan'){
    const who = String(it.assignees||'').trim();
    return `Menugaskan ${who ? (who + ', ') : ''}${it.description}`;
  }
  return it.description;
}
function measureWrapHeight(ctx,text,maxW,lineH){
  const words = String(text||'').split(/\s+/); let line='', h=0;
  for(const w of words){
    const t = line ? line+' '+w : w;
    if(ctx.measureText(t).width < maxW){ line = t; }
    else { h += lineH; line = w; }
  }
  h += lineH;
  return h;
}
function drawTextWrap(ctx,text,x,y,maxW,lineH){
  const words = String(text||'').split(/\s+/); let line='', h=y;
  for(const w of words){
    const t = line ? line+' '+w : w;
    if(ctx.measureText(t).width < maxW){ line = t; }
    else { ctx.fillText(line, x, h); line = w; h += lineH; }
  }
  ctx.fillText(line, x, h);
  return h + lineH;
}
function fitText(ctx,text,maxW){
  let out = String(text||'');
  while(ctx.measureText(out).width > maxW && out.length > 3){ out = out.slice(0,-2); }
  if(out !== text) out = out.trim() + '…';
  return out;
}
function drawWatermark(ctx,W,H){
  ctx.save();ctx.translate(W/2,H/2);ctx.rotate(-Math.PI/5);
  ctx.globalAlpha=0.08;ctx.fillStyle='#111';ctx.font='900 90px sans-serif';
  ctx.textAlign='center';ctx.fillText('Diverifikasi melalui SIGAP AGENDA',0,0);ctx.restore();
}

// ====== UTIL ======
function downloadBlob(b,n){const u=URL.createObjectURL(b);const a=document.createElement('a');a.href=u;a.download=n;a.click();setTimeout(()=>URL.revokeObjectURL(u),1000);}
function formatDateID(d){return new Date(d).toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric'});}

// ====================================================================
// ===================  SWEETALERT DELETE UTILITIES  ==================
// ====================================================================
(function ensureDeleteForm(){
  if(document.getElementById('formDeleteAgenda')) return;
  const f = document.createElement('form');
  f.id = 'formDeleteAgenda';
  f.method = 'POST';
  f.action = "{{ route('sigap-agenda.delete') }}";
  f.className = 'hidden';
  f.innerHTML = `@csrf <input type="hidden" name="id" value="">`;
  document.body.appendChild(f);
})();
function confirmHapus(id, unitTitle = '', dateStr = ''){
  if(!window.Swal){
    if(confirm(`Hapus agenda ini?\n${unitTitle}\n${dateStr}`)){ submitDelete(id); }
    return;
  }
  const html = `<div class="text-left">
    <div class="font-semibold text-gray-800">${unitTitle || 'Agenda'}</div>
    <div class="text-xs text-gray-500 mt-1">${formatDateID(dateStr)}</div>
    <div class="text-xs text-gray-500 mt-2">Tindakan ini tidak bisa dibatalkan.</div>
  </div>`;
  Swal.fire({
    icon:'warning', title:'Hapus agenda ini?', html,
    showCancelButton:true, confirmButtonText:'Ya, hapus', cancelButtonText:'Batal',
    confirmButtonColor:'#7a2222', focusCancel:true
  }).then(res=>{ if(res.isConfirmed){ submitDelete(id); } });
}
function submitDelete(id){
  const form = document.getElementById('formDeleteAgenda');
  form.querySelector('input[name="id"]').value = id;
  form.submit();
}
</script>

@if(session('success'))
<script> if(window.Swal){ Swal.fire({icon:'success',title:'Berhasil',text:@json(session('success')),timer:2000,showConfirmButton:false}); } </script>
@endif
@if($errors->any())
<script> if(window.Swal){ Swal.fire({icon:'error',title:'Gagal',text:@json($errors->first())}); } </script>
@endif
@endpush
