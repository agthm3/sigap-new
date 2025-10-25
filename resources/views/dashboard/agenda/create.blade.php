@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-extrabold text-gray-900">Tambah Agenda</h1>
  <p class="text-sm text-gray-600 mt-1">Isi singkat, rapi, siap dibagikan.</p>

  <form method="POST" action="{{ route('sigap-agenda.store') }}" class="mt-5 bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 space-y-4"  enctype="multipart/form-data">
    @csrf
    <label class="block">
      <span class="text-sm font-semibold text-gray-800">Tanggal</span>
      <input name="date" type="date" required value="{{ now()->format('Y-m-d') }}"
             class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
    </label>

    <label class="block">
      <span class="text-sm font-semibold text-gray-800">Unit / Pejabat</span>
      <input name="unit_title" required placeholder="Contoh: Kepala BRIDA Kota Makassar"
             class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
    </label>

    <label class="flex items-center gap-2 select-none">
      <input type="checkbox" name="is_public" value="1" class="w-4 h-4 rounded text-maroon focus:ring-maroon border-gray-300">
      <span class="text-sm text-gray-800">Tandai sebagai <b>Publik</b></span>
    </label>

    <hr>

    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-gray-900">Daftar Kegiatan</h2>
      <button type="button" onclick="addItem()" class="px-3 py-1.5 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">+ Tambah</button>
    </div>

    <div id="itemsWrap" class="space-y-3"></div>

    <div class="flex flex-col sm:flex-row justify-end gap-2 pt-3">
      <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-center">Simpan Agenda</button>
    </div>
  </form>
</section>
@endsection

@push('scripts')
<script>
let itemCount = 0;
document.addEventListener('DOMContentLoaded', ()=>addItem());

function addItem(pref={}){
  itemCount++;
  const wrap=document.getElementById('itemsWrap');
  const div=document.createElement('div');
  div.className='border border-gray-200 rounded-xl bg-gray-50 p-3';
  div.dataset.item=itemCount;
  div.innerHTML=`
    <div class="flex justify-between items-center">
      <div class="font-semibold text-sm text-gray-800">Kegiatan #${itemCount}</div>
      <button type="button" onclick="this.closest('[data-item]').remove()" class="text-xs text-red-600 hover:underline">Hapus</button>
    </div>
    <div class="mt-3 grid gap-3">
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
        <input type="text" name="items[${itemCount}][assignees]" placeholder="Pisahkan dengan koma"
               class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
      </label>
      <label class="block">
        <span class="text-sm text-gray-800">Deskripsi</span>
        <textarea name="items[${itemCount}][description]" rows="2" required
                  class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon"></textarea>
      </label>
      <div class="grid sm:grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm text-gray-800">Waktu</span>
          <input type="text" name="items[${itemCount}][time_text]" required
                 placeholder="Contoh: 08.30 Wita"
                 class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>
        <label class="block">
          <span class="text-sm text-gray-800">Tempat</span>
          <input type="text" name="items[${itemCount}][place]" required
                 placeholder="Contoh: Ruang Rapat BRIDA Lt.6"
                 class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>
        <label class="block">
          <span class="text-sm text-gray-800">Upload Dokumen (opsional)</span>
          <input type="file" name="items[${itemCount}][file]" accept=".pdf,.doc,.docx,.jpg,.png"
                class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>
      </div>
    </div>`;
  wrap.appendChild(div);
}
</script>
@endpush
