@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Edit Agenda</h1>
      <p class="text-sm text-gray-600 mt-1">Ubah data agenda lalu simpan.</p>
    </div>
    <a href="{{ route('sigap-agenda.index') }}"
       class="text-sm px-3 py-1.5 rounded-lg border hover:bg-gray-50">Kembali</a>
  </div>

  <form method="POST"
        action="{{ route('sigap-agenda.update') }}"
        enctype="multipart/form-data"
        class="mt-5 bg-white border rounded-2xl p-4 space-y-4">
    @csrf
    <input type="hidden" name="id" value="{{ $agenda->id }}">

    {{-- Header --}}
    <label class="block">
      <span class="text-sm font-semibold">Tanggal</span>
      <input type="date" name="date"
             value="{{ substr($agenda->date,0,10) }}"
             required class="mt-1 w-full rounded border p-2">
    </label>

    <label class="block">
      <span class="text-sm font-semibold">Unit / Pejabat</span>
      <input type="text" name="unit_title"
             value="{{ $agenda->unit_title }}"
             required class="mt-1 w-full rounded border p-2">
    </label>

    <label class="flex items-center gap-2">
      <input type="checkbox" name="is_public" value="1"
             {{ $agenda->is_public ? 'checked' : '' }}>
      <span class="text-sm">Publik</span>
    </label>

    <hr>

    {{-- ITEMS --}}
    <div class="flex justify-between items-center">
      <h2 class="font-semibold">Daftar Kegiatan</h2>
      <button type="button" onclick="addItem()"
              class="px-3 py-1.5 rounded bg-maroon text-white text-sm">
        + Tambah
      </button>
    </div>

    <div id="itemsWrap" class="space-y-3">
      @php $i=0; @endphp
      @foreach($agenda->items as $it)
      @php $i++; @endphp

      <div class="border rounded-xl bg-gray-50 p-3" data-item="{{ $i }}">
        <div class="flex justify-between items-center">
          <div class="font-semibold text-sm item-title">Kegiatan #{{ $i }}</div>
          <div class="flex gap-1">
            <button type="button" onclick="moveItem(this,'up')" class="text-xs border px-2 py-1">↑</button>
            <button type="button" onclick="moveItem(this,'down')" class="text-xs border px-2 py-1">↓</button>
            <button type="button" onclick="dupItem({{ $i }})" class="text-xs border px-2 py-1">Duplikat</button>
            <button type="button" onclick="delItem({{ $i }})"
                    class="text-xs border px-2 py-1 text-red-600">Hapus</button>
          </div>
        </div>

        <div class="mt-3 grid gap-3">
          <input type="hidden" name="items[{{ $i }}][id]" value="{{ $it->id }}">
          <input type="hidden" name="items[{{ $i }}][order_no]" value="{{ $it->order_no ?? $i }}">

          <label>
            <span class="text-sm">Mode</span>
            <select name="items[{{ $i }}][mode]" class="w-full border rounded p-2">
              <option value="kepala" {{ $it->mode=='kepala'?'selected':'' }}>Kepala</option>
              <option value="menugaskan" {{ $it->mode=='menugaskan'?'selected':'' }}>Menugaskan</option>
              <option value="custom" {{ $it->mode=='custom'?'selected':'' }}>Custom</option>
            </select>
          </label>

          {{-- ASSIGNEE --}}
          <div class="assignee-wrap">
            <span class="text-sm">Yang Ditugaskan</span>

            <div class="assignee-chips flex flex-wrap gap-1 mt-1"></div>

            <input type="text" class="assignee-search w-full border rounded p-2 mt-2"
                   placeholder="Cari nama pegawai...">

            <div class="assignee-results border bg-white mt-1 hidden max-h-40 overflow-y-auto"></div>

            <div class="flex gap-2 mt-2">
              <input type="text"
                    class="assignee-manual flex-1 border rounded p-2"
                    placeholder="Tambah manual">

              <button type="button"
                      class="assignee-add px-3 rounded bg-maroon text-white text-sm">
                +
              </button>
            </div>


            <input type="hidden"
                   name="items[{{ $i }}][assignees]"
                   class="assignee-json"
                   value='{{ $it->assignees ?: '{"users":[],"manual":[]}' }}'>
          </div>

          <label>
            <span class="text-sm">Deskripsi</span>
            <textarea name="items[{{ $i }}][description]"
                      class="w-full border rounded p-2"
                      required>{{ $it->description }}</textarea>
          </label>

          {{-- ===== DOKUMEN ===== --}}
          <div class="grid sm:grid-cols-2 gap-3">
            {{-- Upload baru --}}
            <div>
              <label class="block">
                <span class="text-sm font-semibold">Ganti Dokumen (opsional)</span>
                <input type="file"
                      name="items[{{ $i }}][file]"
                      accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                      class="mt-1 w-full text-sm rounded border p-2">
                <p class="text-xs text-gray-500 mt-1">
                  Jika diisi, dokumen lama akan diganti
                </p>
              </label>
            </div>

            {{-- Dokumen saat ini --}}
            <div>
              <span class="text-sm font-semibold">Dokumen Saat Ini</span>

              @if($it->file_path)
                <div class="mt-1 flex flex-col gap-2">
                  <a href="{{ asset('storage/'.$it->file_path) }}"
                    target="_blank"
                    class="inline-flex items-center gap-1 text-sm text-maroon hover:underline">
                    Lihat Dokumen
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-width="2"
                            d="M14 3h7v7M10 14L21 3M21 10v11H3V3h11"/>
                    </svg>
                  </a>

                  <label class="inline-flex items-center gap-2 text-sm text-red-600">
                    <input type="checkbox"
                          name="items[{{ $i }}][file_delete]"
                          value="1"
                          class="rounded border">
                    Hapus dokumen
                  </label>
                </div>
              @else
                <p class="text-xs text-gray-500 mt-1 italic">
                  — Tidak ada dokumen —
                </p>
              @endif
            </div>
          </div>
          {{-- ===== /DOKUMEN ===== --}}


          <div class="grid grid-cols-2 gap-2">
            <input name="items[{{ $i }}][time_text]"
                   value="{{ $it->time_text }}"
                   class="border rounded p-2" placeholder="Waktu">
            <input name="items[{{ $i }}][place]"
                   value="{{ $it->place }}"
                   class="border rounded p-2" placeholder="Tempat">
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="flex justify-end gap-2 pt-4">
      <button class="px-4 py-2 bg-maroon text-white rounded">Simpan</button>
    </div>
  </form>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.assignee-wrap').forEach(initAssignee);
});

function initAssignee(block){
  const hidden = block.querySelector('.assignee-json');
  let state = { users: [], manual: [] };

  try {
    const parsed = JSON.parse(hidden.value || '{}');
    state.users  = [...(parsed.users || [])];
    state.manual= [...(parsed.manual || [])];
  } catch(e){}

  block._state = state;
  renderChips(block);
  bindSearch(block);
  bindManual(block);
}

function renderChips(block){
  const chips = block.querySelector('.assignee-chips');
  const hidden = block.querySelector('.assignee-json');
  const s = block._state;

  chips.innerHTML = '';

  s.users.forEach((u,i)=>{
    const c=document.createElement('span');
    c.className='px-2 py-1 bg-maroon text-white text-xs rounded-full';
    c.innerHTML=`${u.name} <button type="button">&times;</button>`;
    c.querySelector('button').onclick=()=>{
      s.users.splice(i,1);
      hidden.value=JSON.stringify(s);
      renderChips(block);
    };
    chips.appendChild(c);
  });

  s.manual.forEach((m,i)=>{
    const c=document.createElement('span');
    c.className='px-2 py-1 bg-gray-600 text-white text-xs rounded-full';
    c.innerHTML=`${m} <button type="button">&times;</button>`;
    c.querySelector('button').onclick=()=>{
      s.manual.splice(i,1);
      hidden.value=JSON.stringify(s);
      renderChips(block);
    };
    chips.appendChild(c);
  });
}

function bindSearch(block){
  const input = block.querySelector('.assignee-search');
  const box   = block.querySelector('.assignee-results');
  const hidden= block.querySelector('.assignee-json');
  const s     = block._state;

  input.addEventListener('input', async()=>{
    const q=input.value.trim();
    if(q.length<2){ box.classList.add('hidden'); return; }

    const res=await fetch(`/api/users/search?q=${encodeURIComponent(q)}`);
    const data=await res.json();

    box.innerHTML='';
    data.forEach(u=>{
      if(s.users.some(x=>x.id===u.id)) return;
      const d=document.createElement('div');
      d.className='px-3 py-2 hover:bg-gray-100 cursor-pointer';
      d.textContent=u.name;
      d.onclick=()=>{
        s.users.push({id:u.id,name:u.name});
        hidden.value=JSON.stringify(s);
        input.value='';
        box.classList.add('hidden');
        renderChips(block);
      };
      box.appendChild(d);
    });
    box.classList.remove('hidden');
  });
}

function bindManual(block){
  const input  = block.querySelector('.assignee-manual');
  const btn    = block.querySelector('.assignee-add');
  const hidden = block.querySelector('.assignee-json');
  const s      = block._state;

  function addManual(){
    const val = input.value.trim();
    if(!val) return;

    s.manual.push(val);
    hidden.value = JSON.stringify(s);
    input.value = '';
    renderChips(block);
  }

  // Enter (desktop)
  input.addEventListener('keydown', e => {
    if(e.key === 'Enter'){
      e.preventDefault();
      addManual();
    }
  });

  // Klik tombol + (mobile friendly)
  if(btn){
    btn.addEventListener('click', addManual);
  }
}


/* === ITEM ORDER === */
function moveItem(btn,dir){
  const c=btn.closest('[data-item]');
  const w=document.getElementById('itemsWrap');
  if(dir==='up' && c.previousElementSibling) w.insertBefore(c,c.previousElementSibling);
  if(dir==='down' && c.nextElementSibling) w.insertBefore(c.nextElementSibling,c);
  renumber();
}
function renumber(){
  document.querySelectorAll('[data-item]').forEach((c,i)=>{
    c.dataset.item=i+1;
    c.querySelector('.item-title').textContent='Kegiatan #'+(i+1);
    c.querySelector('input[name$="[order_no]"]').value=i+1;
  });
}
</script>

<script>
let itemCount = document.querySelectorAll('#itemsWrap [data-item]').length;

/* === ADD ITEM === */
function addItem(){
  itemCount++;

  const wrap = document.getElementById('itemsWrap');
  const div  = document.createElement('div');

  div.className = 'border rounded-xl bg-gray-50 p-3';
  div.dataset.item = itemCount;

  div.innerHTML = `
    <div class="flex justify-between items-center">
      <div class="font-semibold text-sm item-title">Kegiatan #${itemCount}</div>
      <div class="flex gap-1">
        <button type="button" onclick="moveItem(this,'up')" class="text-xs border px-2 py-1">↑</button>
        <button type="button" onclick="moveItem(this,'down')" class="text-xs border px-2 py-1">↓</button>
        <button type="button" onclick="dupItem(${itemCount})" class="text-xs border px-2 py-1">Duplikat</button>
        <button type="button" onclick="delItem(${itemCount})"
                class="text-xs border px-2 py-1 text-red-600">Hapus</button>
      </div>
    </div>

    <div class="mt-3 grid gap-3">
      <input type="hidden" name="items[${itemCount}][id]" value="">
      <input type="hidden" name="items[${itemCount}][order_no]" value="${itemCount}">

      <label>
        <span class="text-sm">Mode</span>
        <select name="items[${itemCount}][mode]" class="w-full border rounded p-2">
          <option value="kepala">Kepala</option>
          <option value="menugaskan">Menugaskan</option>
          <option value="custom">Custom</option>
        </select>
      </label>

      <div class="assignee-wrap">
        <span class="text-sm">Yang Ditugaskan</span>

        <div class="assignee-chips flex flex-wrap gap-1 mt-1"></div>

        <input type="text" class="assignee-search w-full border rounded p-2 mt-2"
               placeholder="Cari nama pegawai...">

        <div class="assignee-results border bg-white mt-1 hidden max-h-40 overflow-y-auto"></div>

        <div class="flex gap-2 mt-2">
          <input type="text"
                class="assignee-manual flex-1 border rounded p-2"
                placeholder="Tambah manual">

          <button type="button"
                  class="assignee-add px-3 rounded bg-maroon text-white text-sm">
            +
          </button>
        </div>


        <input type="hidden"
               name="items[${itemCount}][assignees]"
               class="assignee-json"
               value='{"users":[],"manual":[]}'>
      </div>

      <label>
        <span class="text-sm">Deskripsi</span>
        <textarea name="items[${itemCount}][description]"
                  class="w-full border rounded p-2" required></textarea>
      </label>

      <div class="grid grid-cols-2 gap-2">
        <input name="items[${itemCount}][time_text]" class="border rounded p-2" placeholder="Waktu">
        <input name="items[${itemCount}][place]" class="border rounded p-2" placeholder="Tempat">
      </div>
    </div>
  `;

  wrap.appendChild(div);
  initAssignee(div.querySelector('.assignee-wrap'));
  renumber();
}

/* === DELETE ITEM === */
function delItem(id){
  const el = document.querySelector(`[data-item="${id}"]`);
  if(!el) return;

  const all = document.querySelectorAll('#itemsWrap [data-item]');
  if(all.length === 1){
    alert('Minimal harus ada satu kegiatan.');
    return;
  }

  el.remove();
  renumber();
}

/* === DUPLICATE ITEM === */
function dupItem(id){
  const src = document.querySelector(`[data-item="${id}"]`);
  if(!src) return;

  addItem();
  const dst = document.querySelector(`[data-item="${itemCount}"]`);

  // copy basic fields
  dst.querySelector('select').value =
    src.querySelector('select').value;

  dst.querySelector('textarea').value =
    src.querySelector('textarea').value;

  const srcTime = src.querySelector('input[name$="[time_text]"]');
  const srcPlace= src.querySelector('input[name$="[place]"]');

  dst.querySelector('input[name$="[time_text]"]').value = srcTime.value;
  dst.querySelector('input[name$="[place]"]').value    = srcPlace.value;

  // copy assignees
  const srcAss = src.querySelector('.assignee-json').value;
  const dstAss = dst.querySelector('.assignee-json');
  dstAss.value = srcAss;

  initAssignee(dst.querySelector('.assignee-wrap'));
}
</script>

@endpush
