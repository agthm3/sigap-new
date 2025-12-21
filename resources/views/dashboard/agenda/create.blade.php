@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-extrabold text-gray-900">Tambah Agenda</h1>
  <p class="text-sm text-gray-600 mt-1">Isi singkat, rapi, siap dibagikan.</p>

  <form method="POST"
        action="{{ route('sigap-agenda.store') }}"
        enctype="multipart/form-data"
        class="mt-5 bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 space-y-4">
    @csrf

    <label class="block">
      <span class="text-sm font-semibold text-gray-800">Tanggal</span>
      <input type="date" name="date" required
             value="{{ now()->format('Y-m-d') }}"
             class="mt-1.5 w-full rounded-xl border p-3">
    </label>

    <label class="block">
      <span class="text-sm font-semibold text-gray-800">Unit / Pejabat</span>
      <input type="text" name="unit_title" required
             value="KEPALA BADAN RISET DAN INOVASI DAERAH KOTA MAKASSAR"
             class="mt-1.5 w-full rounded-xl border p-3">
    </label>

    <label class="flex items-center gap-2">
      <input type="checkbox" name="is_public" value="1">
      <span class="text-sm">Tandai sebagai <b>Publik</b></span>
    </label>

    <hr>

    <div class="flex items-center justify-between">
      <h2 class="font-semibold">Daftar Kegiatan</h2>
      <button type="button" onclick="addItem()"
              class="px-3 py-1.5 rounded-lg bg-maroon text-white text-sm">
        + Tambah
      </button>
    </div>

    <div id="itemsWrap" class="space-y-3"></div>

    <div class="flex justify-end pt-3">
      <button type="submit"
              class="px-4 py-2 rounded-lg bg-maroon text-white">
        Simpan Agenda
      </button>
    </div>
  </form>
</section>
@endsection


@push('scripts')
<script>
let itemCount = 0;

document.addEventListener('DOMContentLoaded', () => addItem());

function addItem() {
  itemCount++;

  const wrap = document.getElementById('itemsWrap');
  const div  = document.createElement('div');
  div.className = 'border rounded-xl bg-gray-50 p-3';
  div.dataset.item = itemCount;

  div.innerHTML = `
    <div class="flex justify-between items-center">
      <div class="font-semibold text-sm">Kegiatan #${itemCount}</div>
      <button type="button"
              onclick="this.closest('[data-item]').remove()"
              class="text-xs text-red-600">Hapus</button>
    </div>

    <div class="mt-3 grid gap-3">
      <input type="hidden" name="items[${itemCount}][order_no]" value="${itemCount}">

      <label class="block">
        <span class="text-sm">Mode Kalimat</span>
        <select name="items[${itemCount}][mode]"
                class="mode-select mt-1 w-full rounded border p-2">
          <option value="kepala">Kepala Brida</option>
          <option value="menugaskan">Menugaskan</option>
          <option value="custom">Custom</option>
        </select>
      </label>

      <label class="block assignee-wrap hidden">
        <span class="text-sm">Yang Ditugaskan</span>

        <input type="text"
               class="assignee-search mt-1 w-full rounded border p-2"
               placeholder="Cari nama pegawai...">

        <div class="assignee-results border rounded mt-1 bg-white max-h-40 overflow-y-auto hidden"></div>
        <div class="assignee-chips flex flex-wrap gap-1 mt-2"></div>
        <div class="flex gap-2 mt-2">
        <input type="text"
              class="assignee-manual flex-1 rounded-lg border p-2"
              placeholder="Tambah manual">
        <button type="button"
                class="px-3 rounded bg-maroon text-white text-sm add-manual">
          +
        </button>
      </div>


        <input type="hidden"
               name="items[${itemCount}][assignees]"
               class="assignee-json">
      </label>
      <label class="block">
        <span class="text-sm">Deskripsi</span>
        <textarea name="items[${itemCount}][description]"
                  rows="2" required
                  class="mt-1 w-full rounded border p-2"></textarea>
      </label>
      <div class="grid sm:grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm">Waktu</span>
          <input type="text"
                 name="items[${itemCount}][time_text]"
                 required
                 class="mt-1 w-full rounded border p-2">
        </label>

        <label class="block">
          <span class="text-sm">Tempat</span>
          <input type="text"
                 name="items[${itemCount}][place]"
                 required
                 class="mt-1 w-full rounded border p-2">
        </label>

        <label class="block">
          <span class="text-sm">Upload Dokumen</span>
          <input type="file"
                 name="items[${itemCount}][file]"
                 class="mt-1 w-full rounded border p-2">
        </label>
      </div>
    </div>
  `;

  wrap.appendChild(div);

  const modeSelect   = div.querySelector('.mode-select');
  const assigneeWrap = div.querySelector('.assignee-wrap');

  modeSelect.addEventListener('change', () => {
    assigneeWrap.classList.toggle('hidden', modeSelect.value !== 'menugaskan');
  });

  modeSelect.dispatchEvent(new Event('change'));

  initAssignee(assigneeWrap);
}

function initAssignee(block) {
  const search  = block.querySelector('.assignee-search');
  const results = block.querySelector('.assignee-results');
  const manual  = block.querySelector('.assignee-manual');
  const btn     = block.querySelector('.add-manual'); 
  const hidden  = block.querySelector('.assignee-json');
  const chips   = block.querySelector('.assignee-chips');


  const state = { users: [], manual: [] };
  block._state = state;
  function sync() {
    hidden.value = JSON.stringify(state);
    renderChips();
  }

  function renderChips() {
    chips.innerHTML = '';

    state.users.forEach((u, idx) => {
      const chip = document.createElement('span');
      chip.className =
        'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-maroon text-white';

      chip.innerHTML = `
        ${u.name}
        <button type="button" class="ml-1 hover:text-gray-200">&times;</button>
      `;

      chip.querySelector('button').onclick = () => {
        state.users.splice(idx, 1);
        sync();
      };

      chips.appendChild(chip);
    });

    state.manual.forEach((m, idx) => {
      const chip = document.createElement('span');
      chip.className =
        'inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs bg-gray-600 text-white';

      chip.innerHTML = `
        ${m}
        <button type="button" class="ml-1 hover:text-gray-200">&times;</button>
      `;

      chip.querySelector('button').onclick = () => {
        state.manual.splice(idx, 1);
        sync();
      };

      chips.appendChild(chip);
    });
  }

  // ===== SEARCH USER =====
  search.addEventListener('input', async () => {
    const q = search.value.trim();
    if (q.length < 2) {
      results.classList.add('hidden');
      return;
    }

    const res  = await fetch(`/api/users/search?q=${encodeURIComponent(q)}`);
    const data = await res.json();

    results.innerHTML = '';
    data.forEach(u => {
      if (state.users.some(x => x.id == u.id)) return;

      const item = document.createElement('div');
      item.textContent = u.name;
      item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer';

      item.onclick = () => {
        state.users.push({ id: u.id, name: u.name });
        search.value = '';
        results.classList.add('hidden');
        sync();
      };

      results.appendChild(item);
    });

    results.classList.remove('hidden');
  });

 btn.addEventListener('click', () => {
    const val = manual.value.trim();
    if (!val) return;

    state.manual.push(val);
    manual.value = '';
    sync();
  });
}




</script>
