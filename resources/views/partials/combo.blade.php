{{-- resources/views/partials/combo.blade.php --}}
<div class="relative" x-id="['combo']">
  {{-- hidden input utk submit nilai terpilih --}}
  <input type="hidden" :name="name" :value="selected">

  {{-- Trigger/Display --}}
  <button
    type="button"
    class="w-full flex items-center justify-between rounded-xl border border-gray-300 px-3 py-2.5 text-left
           focus:outline-none focus:ring-2 focus:ring-maroon disabled:opacity-60 disabled:cursor-not-allowed"
    :aria-expanded="open"
    @click="toggle()"
    :disabled="disabled"
  >
    <span x-show="!selected" class="text-gray-400" x-text="placeholder"></span>
    <span x-show="selected" class="truncate" x-text="labelSelected"></span>
    <svg class="w-4 h-4 ml-2 shrink-0 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-width="2" d="M6 9l6 6 6-6"/>
    </svg>
  </button>

  {{-- Panel dropdown --}}
  <div
    x-show="open"
    x-transition
    @click.outside="open=false"
    class="absolute z-40 mt-1 w-full rounded-xl border border-gray-200 bg-white shadow-lg"
  >
    {{-- kotak cari --}}
    <div class="p-2 border-b border-gray-100">
      <input
        x-ref="searchbox"
        type="search"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-maroon focus:ring-maroon"
        placeholder="Ketik untuk mencari…"
        x-model="q"
      />
    </div>

    {{-- daftar opsi --}}
    <ul class="max-h-56 overflow-auto py-1">
      <template x-if="filtered().length === 0">
        <li class="px-3 py-2 text-sm text-gray-500">Tidak ada hasil</li>
      </template>

      <template x-for="[val, label] in filtered()" :key="val">
        <li>
          <button
            type="button"
            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50"
            :class="{'bg-maroon/5': selected===val}"
            @click="pick(val)"
            :title="label"
          >
            <span class="truncate block" x-text="label"></span>
          </button>
        </li>
      </template>
    </ul>

    {{-- baris aksi cepat --}}
    <div class="flex justify-between gap-2 p-2 border-t border-gray-100">
      <button type="button" class="px-2 py-1 text-xs rounded border hover:bg-gray-50" @click="clear()">
        Kosongkan
      </button>
      <button type="button" class="px-2 py-1 text-xs rounded border hover:bg-gray-50" @click="open=false">
        Tutup
      </button>
    </div>
  </div>
</div>
