@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="flex items-start justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Edit Agenda</h1>
      <p class="text-sm text-gray-600 mt-1">Ubah data agenda lalu simpan. (Mobile-first)</p>
    </div>
    <a href="{{ route('sigap-agenda.index') }}" class="text-sm px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50">Kembali</a>
  </div>

  <form method="POST"
        action="{{ route('sigap-agenda.update') }}"
        enctype="multipart/form-data"
        class="mt-5 bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 space-y-4">
    @csrf
    <input type="hidden" name="id" value="{{ $agenda->id }}">

    <label class="block">
      <span class="text-sm font-semibold text-gray-800">Tanggal</span>
      <input name="date" type="date" required value="{{ \Illuminate\Support\Str::of($agenda->date)->substr(0,10) }}"
             class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
    </label>

    <label class="block">
      <span class="text-sm font-semibold text-gray-800">Unit / Pejabat</span>
      <input name="unit_title" required value="{{ $agenda->unit_title }}"
             class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
    </label>

    <label class="flex items-center gap-2 select-none">
      <input type="checkbox" name="is_public" value="1" class="w-4 h-4 rounded text-maroon focus:ring-maroon border-gray-300" {{ $agenda->is_public ? 'checked' : '' }}>
      <span class="text-sm text-gray-800">Tandai sebagai <b>Publik</b></span>
    </label>

    <hr>

    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-gray-900">Daftar Kegiatan</h2>
      <button type="button" onclick="addItem()" class="px-3 py-1.5 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">+ Tambah</button>
    </div>

    <div id="itemsWrap" class="space-y-3">
      @php $i = 0; @endphp
      @foreach($agenda->items as $it)
        @php $i++; @endphp
        <div class="border border-gray-200 rounded-xl bg-gray-50 p-3" data-item="{{ $i }}">
        <div class="flex justify-between items-center">
          <div class="font-semibold text-sm text-gray-800 item-title">
            Kegiatan #{{ $i }}
          </div>
          <div class="flex flex-wrap gap-2">
            <button type="button"
                    onclick="moveItem(this, 'up')"
                    class="text-xs px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-100">
              ↑ Atas
            </button>
            <button type="button"
                    onclick="moveItem(this, 'down')"
                    class="text-xs px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-100">
              ↓ Bawah
            </button>
            <button type="button"
                    onclick="dupItem({{ $i }})"
                    class="text-xs px-2 py-1 rounded-lg border hover:bg-gray-100">
              Duplikat
            </button>
            <button type="button"
                    onclick="delItem({{ $i }})"
                    class="text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50">
              Hapus
            </button>
          </div>
        </div>


          <div class="mt-3 grid gap-3">
            <input type="hidden" name="items[{{ $i }}][id]" value="{{ $it->id }}">
            <input type="hidden" name="items[{{ $i }}][order_no]" value="{{ $it->order_no ?? $i }}">

            <label class="block">
              <span class="text-sm text-gray-800">Mode Kalimat</span>
              <select name="items[{{ $i }}][mode]" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
                <option value="kepala" {{ $it->mode === 'kepala' ? 'selected' : '' }}>Kepala Brida</option>
                <option value="menugaskan" {{ $it->mode === 'menugaskan' ? 'selected' : '' }}>Menugaskan</option>
                <option value="custom" {{ $it->mode === 'custom' ? 'selected' : '' }}>Custom</option>
              </select>
            </label>

            <label class="block">
              <span class="text-sm text-gray-800">Yang Ditugaskan (opsional)</span>
              <input type="text" name="items[{{ $i }}][assignees]" value="{{ $it->assignees }}"
                     class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Pisahkan dengan koma">
            </label>

            <label class="block">
              <span class="text-sm text-gray-800">Deskripsi</span>
              <textarea name="items[{{ $i }}][description]" rows="2" required
                        class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">{{ $it->description }}</textarea>
            </label>

            <div class="grid sm:grid-cols-2 gap-3">
              <label class="block">
                <span class="text-sm text-gray-800">Waktu</span>
                <input type="text" name="items[{{ $i }}][time_text]" required value="{{ $it->time_text }}"
                       class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
              </label>
              <label class="block">
                <span class="text-sm text-gray-800">Tempat</span>
                <input type="text" name="items[{{ $i }}][place]" required value="{{ $it->place }}"
                       class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
              </label>
            </div>

            {{-- ====== File dokumen (lihat/ganti/hapus) ====== --}}
            <div class="grid sm:grid-cols-2 gap-3">
              <div class="block">
                <span class="text-sm text-gray-800">Berkas (opsional)</span>
                <input type="file"
                       name="items[{{ $i }}][file]"
                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                       class="mt-1 block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border file:border-gray-300 file:bg-white hover:file:bg-gray-50">
                <p class="text-xs text-gray-500 mt-1">PDF/DOC/DOCX/JPG/PNG</p>
              </div>

              <div class="block">
                @if($it->file_path)
                  <span class="text-sm text-gray-800">Berkas saat ini</span>
                  <div class="mt-1 flex items-center gap-2">
                    <a href="{{ asset('storage/'.$it->file_path) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1 text-sm text-maroon hover:underline">
                      Lihat berkas
                      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" d="M14 3h7v7M10 14L21 3M21 10v11H3V3h11"/>
                      </svg>
                    </a>
                    <label class="inline-flex items-center gap-2 text-sm text-red-700">
                      <input type="checkbox" name="items[{{ $i }}][file_delete]" value="1"
                             class="rounded border-gray-300 text-red-700 focus:ring-red-700">
                      Hapus berkas
                    </label>
                  </div>
                @else
                  <span class="text-sm text-gray-800">Berkas saat ini</span>
                  <p class="text-xs text-gray-500 mt-1">— belum ada berkas —</p>
                @endif
              </div>
            </div>
            {{-- ====== /File ====== --}}
          </div>
        </div>
      @endforeach
    </div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3">
      <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-center">
        Simpan Perubahan
      </button>
      <button type="button"
              onclick="confirmHapus({{ $agenda->id }}, @js($agenda->unit_title), @js($agenda->date))"
              class="px-4 py-2 rounded-lg border border-red-300 text-red-700 hover:bg-red-50 text-center">
        Hapus Agenda
      </button>
    </div>
  </form>
</section>
@endsection

@push('scripts')
<script>
let itemCount = {{ max(1, $agenda->items->count()) }};

function addItem(){
  itemCount++;
  const wrap=document.getElementById('itemsWrap');
  const el=document.createElement('div');
  el.className='border border-gray-200 rounded-xl bg-gray-50 p-3';
  el.dataset.item=itemCount;
  el.innerHTML=`
  <div class="flex justify-between items-center">
    <div class="font-semibold text-sm text-gray-800 item-title">
      Kegiatan #${itemCount}
    </div>
    <div class="flex flex-wrap gap-2">
      <button type="button"
              onclick="moveItem(this, 'up')"
              class="text-xs px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-100">
        ↑ Atas
      </button>
      <button type="button"
              onclick="moveItem(this, 'down')"
              class="text-xs px-2 py-1 rounded-lg border border-gray-300 hover:bg-gray-100">
        ↓ Bawah
      </button>
      <button type="button"
              onclick="dupItem(${itemCount})"
              class="text-xs px-2 py-1 rounded-lg border hover:bg-gray-100">
        Duplikat
      </button>
      <button type="button"
              onclick="delItem(${itemCount})"
              class="text-xs px-2 py-1 rounded-lg border border-red-300 text-red-700 hover:bg-red-50">
        Hapus
      </button>
    </div>
  </div>

    <div class="mt-3 grid gap-3">
      <input type="hidden" name="items[${itemCount}][id]" value="">
      <input type="hidden" name="items[${itemCount}][order_no]" value="${itemCount}">

      <label class="block">
        <span class="text-sm text-gray-800">Mode Kalimat</span>
        <select name="items[${itemCount}][mode]" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
          <option value="kepala">Kepala Brida</option>
          <option value="menugaskan">Menugaskan</option>
          <option value="custom">Custom</option>
        </select>
      </label>

      <label class="block">
        <span class="text-sm text-gray-800">Yang Ditugaskan (opsional)</span>
        <input type="text" name="items[${itemCount}][assignees]" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Pisahkan dengan koma">
      </label>

      <label class="block">
        <span class="text-sm text-gray-800">Deskripsi</span>
        <textarea name="items[${itemCount}][description]" rows="2" required
                  class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon"></textarea>
      </label>

      <div class="grid sm:grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm text-gray-800">Waktu</span>
          <input type="text" name="items[${itemCount}][time_text]" required placeholder="08.30 Wita"
                 class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>
        <label class="block">
          <span class="text-sm text-gray-800">Tempat</span>
          <input type="text" name="items[${itemCount}][place]" required placeholder="Ruang Rapat BRIDA Lt.6"
                 class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>
      </div>

      <!-- File untuk item baru -->
      <div class="grid sm:grid-cols-2 gap-3">
        <div class="block">
          <span class="text-sm text-gray-800">Berkas (opsional)</span>
          <input type="file"
                 name="items[${itemCount}][file]"
                 accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                 class="mt-1 block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border file:border-gray-300 file:bg-white hover:file:bg-gray-50">
          <p class="text-xs text-gray-500 mt-1">PDF/DOC/DOCX/JPG/PNG</p>
        </div>
        <div class="block">
          <span class="text-sm text-gray-800">Berkas saat ini</span>
          <p class="text-xs text-gray-500 mt-1">— belum ada berkas —</p>
        </div>
      </div>
    </div>`;
  wrap.appendChild(el);
   renumberItems();
}

function delItem(id){
  const el = document.querySelector(`[data-item="${id}"]`);
  if (!el) return;

  const all = document.querySelectorAll('#itemsWrap [data-item]');
  if (all.length === 1) {
    // kalau cuma satu, jangan dihapus, cukup kosongkan saja
    el.querySelectorAll('input[type="text"], textarea').forEach(i => i.value = '');
    const sel = el.querySelector('select'); if (sel) sel.value = 'kepala';
    const idInput = el.querySelector('input[name$="[id]"]'); if (idInput) idInput.value = '';
    const delChk  = el.querySelector('input[name$="[file_delete]"]'); if (delChk) delChk.checked = false;
    const orderInput = el.querySelector('input[type="hidden"][name$="[order_no]"]');
    if (orderInput) orderInput.value = 1;
    const title = el.querySelector('.item-title');
    if (title) title.textContent = 'Kegiatan #1';
    return;
  }

  el.remove();
  renumberItems();
}


function dupItem(id){
  const el=document.querySelector(`[data-item="${id}"]`);
  if(!el) return;
  addItem();
  const dst=document.querySelector(`[data-item="${itemCount}"]`);
  const sSrc=el.querySelector('select'); const sDst=dst.querySelector('select');
  const fSrc=el.querySelectorAll('input[type="text"], textarea');
  const fDst=dst.querySelectorAll('input[type="text"], textarea');
  sDst.value = sSrc.value;
  // urutan: assignees, description, time_text, place
  fDst[0].value = fSrc[0].value || ''; // assignees
  fDst[1].value = fSrc[1].value || ''; // description
  fDst[2].value = fSrc[2].value || ''; // time_text
  fDst[3].value = fSrc[3].value || ''; // place
  // file tidak ikut diduplikasi (demi keamanan browser)
  renumberItems();
}

</script>
@endpush

@push('scripts')
<script>
  function moveItem(btn, direction){
  const card = btn.closest('[data-item]');
  if (!card) return;

  const wrap = document.getElementById('itemsWrap');

  if (direction === 'up') {
    const prev = card.previousElementSibling;
    if (prev) {
      wrap.insertBefore(card, prev);
    }
  } else if (direction === 'down') {
    const next = card.nextElementSibling;
    if (next) {
      wrap.insertBefore(next, card);
    }
  }

  renumberItems();
}

function renumberItems(){
  const cards = document.querySelectorAll('#itemsWrap [data-item]');
  let idx = 0;

  cards.forEach(card => {
    idx++;

    // update dataset
    card.dataset.item = idx;

    // update label "Kegiatan #X"
    const title = card.querySelector('.item-title');
    if (title) {
      title.textContent = 'Kegiatan #' + idx;
    }

    // update hidden order_no
    const orderInput = card.querySelector('input[type="hidden"][name$="[order_no]"]');
    if (orderInput) {
      orderInput.value = idx;
    }
  });
}

</script>
<script>
// inject form delete jika belum ada (reuse dari index)
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
  Swal.fire({
    icon:'warning',
    title:'Hapus agenda ini?',
    html:`<div class="text-left"><div class="font-semibold">${unitTitle||'Agenda'}</div><div class="text-xs text-gray-500 mt-1">${dateStr}</div><div class="text-xs text-gray-500 mt-2">Tindakan ini tidak bisa dibatalkan.</div></div>`,
    showCancelButton:true,
    confirmButtonText:'Ya, hapus',
    cancelButtonText:'Batal',
    confirmButtonColor:'#7a2222',
    focusCancel:true
  }).then(res=>{ if(res.isConfirmed) submitDelete(id); });
}
function submitDelete(id){
  const form=document.getElementById('formDeleteAgenda');
  form.querySelector('input[name="id"]').value=id;
  form.submit();
}
</script>
@endpush
