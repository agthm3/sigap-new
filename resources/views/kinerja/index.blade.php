@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Kinerja</h1>
      <p class="text-sm text-gray-600 mt-1">
        Gunakan <b>Bulanan</b> untuk kirim bukti harian, atau <b>Tahunan</b> untuk 1 tautan berisi semua bukti setahun.
      </p>
    </div>

  @hasanyrole('admin|verif_kinerja')
  <button id="btnTambahKinerja"
    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-maroon text-white font-medium hover:bg-maroon-800 shadow-sm transition">
    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
    Upload Bukti
  </button>
  @endhasanyrole
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 -mt-3">
  <div x-data="kinerjaFilter()" x-init="init()" class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 shadow-sm">

    <div class="inline-flex rounded-xl overflow-hidden border border-gray-300 p-1 bg-gray-50">
      <button type="button" @click="setMode('bulan')"
        :class="mode==='bulan' ? 'bg-maroon text-white shadow' : 'bg-transparent text-gray-700 hover:text-gray-900'"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 w-36">Bulanan</button>
      <button type="button" @click="setMode('tahun')"
        :class="mode==='tahun' ? 'bg-maroon text-white shadow' : 'bg-transparent text-gray-700 hover:text-gray-900'"
        class="px-4 py-2 text-sm font-semibold rounded-lg transition duration-150 w-36">Tahunan (1 Link)</button>
    </div>

    <form method="GET" action="{{ route('sigap-kinerja.index') }}" class="mt-5">
      <input type="hidden" name="mode" :value="mode">

      <div x-show="mode==='bulan'" x-cloak>
        <div class="grid lg:grid-cols-4 gap-4">
          <label class="block lg:col-span-2">
            <span class="block text-sm font-semibold text-gray-700">Cari cepat</span>
            <input name="q" type="search" value="{{ request('q') }}"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Nama kegiatan / keterangan…">
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Bulan</span>
            <input name="month" type="month" value="{{ request('month') }}"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Kinerja (Kategori)</span>
            <div
              x-data="combo({
                name:'category',
                placeholder:'Pilih/ketik kategori…',
                options: __KINERJA.catOptions,
                initial: filter.category,
                disabled: false
              })"
              @changed="
                filter.category = $event.detail;
                onCategoryChange();
              "
              class="mt-1.5"
            >
              @include('partials.combo')
            </div>
          </label>

          <label class="block lg:col-span-2">
            <span class="block text-sm font-semibold text-gray-700">RHK</span>
            <div
              x-data="combo({
                name:'rhk',
                placeholder: filter.category ? 'Pilih/ketik RHK…' : 'Pilih kategori dulu',
                options: __KINERJA.rhkMap(filter.category),
                initial: filter.rhk,
                disabled: true
              })"
              x-effect="
                $data.disabled = !filter.category;
                $data.setOptions(__KINERJA.rhkMap(filter.category));
                if(!filter.category){ $data.clear(false); }
              "
              @changed="filter.rhk = $event.detail"
              class="mt-1.5"
            >
              @include('partials.combo')
            </div>
            <p class="text-[12px] text-gray-500 mt-1" x-show="!filter.category">Pilih kategori dulu untuk menampilkan RHK.</p>
          </label>
        </div>

        <div class="flex flex-wrap gap-3 mt-5">
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-maroon text-white font-medium hover:bg-maroon-800 transition">
            Terapkan Filter
          </button>
          <a href="{{ route('sigap-kinerja.index', ['mode'=>'bulan']) }}"
             class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50 font-medium transition">
            Bersihkan
          </a>
        </div>

        <p class="text-xs text-gray-500 mt-3">
          Setelah ketemu buktinya, klik <b>Salin Link</b> lalu tempel di sistem SKP PRO.
        </p>
      </div>

      <div x-show="mode==='tahun'" x-cloak>
        <div class="grid md:grid-cols-4 gap-4">
          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Tahun</span>
            <input x-model="annual.year" type="number" min="2020" max="2100"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
            <p class="text-xs text-gray-500 mt-1">Contoh: 2025</p>
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Kategori (opsional)</span>
            <div
              x-data="combo({
                name:'category_annual',
                placeholder:'Semua / ketik untuk cari…',
                options: __KINERJA.catOptions,
                initial: annual.category,
                disabled: false
              })"
              @changed="annual.category = $event.detail; annual.rhk='';"
              class="mt-1.5"
            >
              @include('partials.combo')
            </div>
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">RHK (opsional)</span>
            <div
              x-data="combo({
                name:'rhk_annual',
                placeholder: annual.category ? 'Semua / ketik untuk cari…' : 'Pilih kategori dulu',
                options: __KINERJA.rhkMap(annual.category),
                initial: annual.rhk,
                disabled: true
              })"
              x-effect="
                $data.disabled = !annual.category;
                $data.setOptions(__KINERJA.rhkMap(annual.category));
                if(!annual.category){ $data.clear(false); }
              "
              @changed="annual.rhk = $event.detail"
              class="mt-1.5"
            >
              @include('partials.combo')
            </div>
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Cari cepat (opsional)</span>
            <input x-model="annual.q" type="search"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Nama kegiatan / keterangan…">
          </label>
        </div>

        <div class="flex flex-wrap gap-3 mt-5">
          <a :href="annualLink()" target="_blank"
             class="px-5 py-2.5 rounded-xl bg-gray-900 text-white font-medium hover:bg-black transition">
            Lihat Laporan Tahunan
          </a>
          <button type="button" @click="copyAnnualLink()"
                  class="px-5 py-2.5 rounded-xl bg-maroon text-white font-medium hover:bg-maroon-800 transition">
            Salin Link Laporan Tahunan
          </button>
        </div>

        <p class="text-xs text-gray-500 mt-3">
          Tautan ini menampilkan <b>semua bukti setahun penuh</b> (sesuai filter) dan bisa dibagikan ke pemeriksa.
        </p>
      </div>
    </form>
  </div>
</section>

@if(request('mode', 'bulan') === 'bulan')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse ($items as $it)
      @php $tgl = \Carbon\Carbon::parse($it['date'] ?? now())->locale('id')->translatedFormat('d F Y'); @endphp
      <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col shadow-sm hover:shadow transition">
        <div class="relative">
          <img src="{{ $it['thumb_url'] ?? asset('images/thumb/photo-placeholder.jpg') }}"
               alt="{{ $it['title'] }}" class="w-full h-44 object-cover">
          <div class="absolute top-2 left-2 flex flex-wrap gap-1 max-w-[90%]">
            <span class="px-2 py-0.5 rounded text-xs bg-maroon text-white truncate" title="{{ $it['category'] }}">
              {{ \Illuminate\Support\Str::limit($it['category'], 40) }}
            </span>
            @if(!empty($it['rhk']))
              <span class="px-2 py-0.5 rounded text-[11px] bg-gray-900/80 text-white truncate" title="{{ $it['rhk'] }}">
                {{ \Illuminate\Support\Str::limit($it['rhk'], 40) }}
              </span>
            @endif
          </div>
        </div>

        <div class="p-3.5 flex-1 flex flex-col gap-1">
          <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2" title="{{ $it['title'] }}">
            {{ \Illuminate\Support\Str::limit($it['title'], 90) }}
          </h3>
          <p class="text-xs text-gray-500">{{ $tgl }}</p>
          @if(!empty($it['description']))
            <p class="text-sm text-gray-600 line-clamp-2 mt-1" title="{{ $it['description'] }}">
              {{ \Illuminate\Support\Str::limit($it['description'], 120) }}
            </p>
          @endif
        </div>

        <div class="p-3 pt-0 grid grid-cols-3 gap-2">
          <a href="{{ route('sigap-kinerja.public', $it['id']) }}" target="_blank"
             class="col-span-1 px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm font-medium text-center transition">
            Lihat
          </a>
          <button type="button"
                  class="col-span-2 px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm font-medium transition"
                  onclick="copyLink(@js(route('sigap-kinerja.public', $it['id'])))">
            Salin Link
          </button>
        @hasanyrole('admin|verif_kinerja')
          <button type="button"
                  class="col-span-3 px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-medium transition text-center"
                  onclick="confirmHapus({{ $it['id'] }}, @js($it['title']))">
            Hapus Bukti
          </button>
        @endhasanyrole
        </div>
      </article>
    @empty
      <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center text-gray-500">
          Tidak ada bukti kinerja untuk filter ini.
        </div>
      </div>
    @endforelse
  </div>

  @isset($itemsPage)
    @if($itemsPage->hasPages())
    <div class="mt-6">
      {{ $itemsPage->onEachSide(1)->links() }}
    </div>
    @endif
  @endisset
</section>
@endif

@if($isAdminDemo)
<div id="modalKinerja" class="fixed inset-0 z-50 hidden overflow-y-auto" x-data="kinerjaModal()">
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeKinerjaModal()"></div>
  
  <div class="relative min-h-screen flex items-center justify-center p-4">
    <div class="relative w-full max-w-3xl bg-white rounded-3xl shadow-2xl overflow-hidden my-8">
      
      <div class="px-6 py-5 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 text-white flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold">Unggah Bukti Kinerja</h2>
          <p class="text-white/80 text-xs mt-0.5">Unggah hingga 30 foto/PDF. Foto dikompres otomatis & tajam.</p>
        </div>
        <button type="button" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10 transition" onclick="closeKinerjaModal()">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <form @submit.prevent="submitForm()" class="p-6 space-y-5">
        
        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kategori <span class="text-red-500">*</span></span>
            <div
              x-data="combo({
                name:'category',
                placeholder:'Pilih/ketik kategori…',
                options: __KINERJA.catOptions,
                initial: form.category,
                disabled: false
              })"
              @changed="form.category=$event.detail; form.rhk='';"
              class="mt-1.5"
            >
              @include('partials.combo')
            </div>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">RHK</span>
            <div
              x-data="combo({
                name:'rhk',
                placeholder: form.category ? 'Pilih/ketik RHK…' : 'Pilih kategori dulu',
                options: __KINERJA.rhkMap(form.category),
                initial: form.rhk,
                disabled: true
              })"
              x-effect="
                $data.disabled = !form.category;
                $data.setOptions(__KINERJA.rhkMap(form.category));
                if(!form.category){ $data.clear(false); }
              "
              @changed="form.rhk=$event.detail"
              class="mt-1.5"
            >
              @include('partials.combo')
            </div>
          </label>

          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Judul / Nama Kegiatan <span class="text-red-500">*</span></span>
            <input x-model="form.title" type="text" required
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-maroon focus:ring-maroon"
                   placeholder="Contoh: Sosialisasi SIGAP BRIDA di Balaikota">
          </label>

          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Deskripsi (opsional)</span>
            <textarea x-model="form.description" rows="2"
                      class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-maroon focus:ring-maroon"
                      placeholder="Ringkasan singkat kegiatan..."></textarea>
          </label>

          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Tanggal Kegiatan <span class="text-red-500">*</span></span>
            <input x-model="form.date" type="date" required
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="space-y-3 pt-2">
          <div class="flex items-center justify-between">
            <label class="block text-sm font-semibold text-gray-700">
              Lampiran File Bukti <span class="text-red-500">*</span>
            </label>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium" 
                  :class="filesList.length >= 30 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600'">
              <span x-text="filesList.length"></span> / 30 Maks. Foto/File
            </span>
          </div>

          <div 
            @dragover.prevent="dragover = true"
            @dragleave.prevent="dragover = false"
            @drop.prevent="dragover = false; handleFilesSelect($event.dataTransfer.files)"
            :class="dragover ? 'border-maroon bg-maroon/5' : 'border-gray-300 hover:border-maroon/60 bg-gray-50/50'"
            class="border-2 border-dashed rounded-2xl p-6 transition text-center cursor-pointer relative"
            @click="$refs.dummyFileInput.click()"
          >
            <input type="file" x-ref="dummyFileInput" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" 
                   @change="handleFilesSelect($event.target.files); $event.target.value = ''">
            
            <div class="space-y-2 pointer-events-none">
              <div class="w-12 h-12 mx-auto rounded-full bg-maroon/10 text-maroon flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              <div class="text-sm font-medium text-gray-800">
                Tarik & Lepas foto di sini, atau <span class="text-maroon underline">Pilih File</span>
              </div>
              <p class="text-xs text-gray-500">
                Format: JPG, PNG, WEBP, PDF • Maks 20MB per foto (otomatis dikompres ke 200-400KB)
              </p>
            </div>
          </div>

          <div x-show="isCompressing" x-cloak class="flex items-center gap-2 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-xl text-xs">
            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>Memproses dan mengompres gambar... Harap tunggu sebentar.</span>
          </div>

          <div x-show="isSubmitting" x-cloak class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs">
            <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span x-text="uploadStatusText"></span>
          </div>

          <div x-show="filesList.length > 0" x-cloak class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-64 overflow-y-auto p-1 border border-gray-100 rounded-xl">
            <template x-for="(item, idx) in filesList" :key="idx">
              <div class="relative group bg-gray-900 rounded-xl overflow-hidden border border-gray-200 text-white flex flex-col justify-between h-32">
                <template x-if="item.isImage">
                  <img :src="item.previewUrl" class="absolute inset-0 w-full h-full object-cover opacity-80 group-hover:opacity-100 transition">
                </template>
                <template x-if="!item.isImage">
                  <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-800 text-gray-300 p-2 text-center">
                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-width="2" d="M7 21h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v15a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-[10px] truncate w-full mt-1" x-text="item.file.name"></span>
                  </div>
                </template>

                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/40"></div>

                <div class="relative z-10 flex justify-end p-1.5">
                  <button type="button" @click="removeFile(idx)" :disabled="isSubmitting"
                          class="bg-red-600/90 hover:bg-red-600 text-white p-1 rounded-full text-xs shadow transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                </div>

                <div class="relative z-10 p-2 text-[10px] leading-tight space-y-0.5">
                  <p class="font-semibold truncate" x-text="item.file.name"></p>
                  <template x-if="item.isImage">
                    <p class="text-emerald-300 font-mono">
                      <span class="line-through text-gray-400" x-text="formatSize(item.originalSize)"></span>
                      <span x-text="' → ' + formatSize(item.compressedSize)"></span>
                    </p>
                  </template>
                  <template x-if="!item.isImage">
                    <p class="text-gray-300 font-mono" x-text="formatSize(item.originalSize)"></p>
                  </template>
                </div>
              </div>
            </template>
          </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
          <button type="button" :disabled="isSubmitting" 
                  class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium transition text-sm" 
                  onclick="closeKinerjaModal()">
            Batal
          </button>
          <button type="submit" 
                  :disabled="isCompressing || isSubmitting || filesList.length === 0"
                  :class="(isCompressing || isSubmitting || filesList.length === 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-maroon hover:bg-maroon-800'"
                  class="px-6 py-2.5 rounded-xl text-white font-medium transition text-sm flex items-center gap-2">
            <span x-text="isSubmitting ? 'Mengunggah...' : 'Simpan Bukti'"></span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection

@push('head')
<script>
  // Seed dari server ke GLOBAL
  (function(){
    const RAW = @json($rhksByCategory ?? []);
    const catOptions = {};
    const catIndex   = {};
    (RAW || []).forEach(c => {
      catOptions[c.code] = c.label;
      catIndex[c.code]   = c.rhks || {};
    });
    window.__KINERJA = {
      catOptions,
      rhkMap: function(code){ return code ? (catIndex[code] || {}) : {}; }
    };
  })();
</script>
@endpush

@push('scripts')
@hasanyrole('admin|verif_kinerja')
  <form id="formDeleteKinerja" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>
@endhasanyrole
<script>
  @if($isAdminDemo)
  const DELETE_BASE = @js(route('sigap-kinerja.destroy', ['id' => '__ID__']));

  function confirmHapus(id, title){
    Swal.fire({
      icon: 'warning',
      title: 'Hapus bukti ini?',
      html: `<div class="text-left"><b>${title || 'Tanpa judul'}</b><br><span class="text-xs text-gray-500">Tindakan ini tidak bisa dibatalkan.</span></div>`,
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#7a2222'
    }).then(res => {
      if(res.isConfirmed){
        const form = document.getElementById('formDeleteKinerja');
        form.action = DELETE_BASE.replace('__ID__', id);
        form.submit();
      }
    });
  }
  @endif
</script>

<script>
  // ====== Reusable combobox Alpine component (searchable) ======
  function combo({name, options, initial = '', placeholder = 'Pilih…', disabled = false}){
    return {
      name, placeholder,
      open: false,
      q: '',
      disabled,
      selected: initial || '',
      optionsMap: {...(options || {})},

      setOptions(newMap){ this.optionsMap = {...(newMap || {})}; },

      get labelSelected(){
        return this.selected && this.optionsMap[this.selected]
          ? this.optionsMap[this.selected] : '';
      },

      filtered(){
        const term = (this.q || '').toLowerCase();
        const arr = Object.entries(this.optionsMap);
        if(!term) return arr;
        return arr.filter(([code, label]) => (label||'').toLowerCase().includes(term));
      },

      pick(code){
        this.selected = code;
        this.open = false;
        this.q = '';
        this.$dispatch('changed', code);
      },

      clear(emit=true){
        this.selected = '';
        this.q = '';
        if(emit) this.$dispatch('changed', '');
      },

      toggle(){
        if(this.disabled) return;
        this.open = !this.open;
        if(this.open){ this.$nextTick(() => this.$refs.searchbox?.focus()); }
      }
    }
  }

  // ====== State halaman utama ======
  function kinerjaFilter(){
    return {
      mode: @json(request('mode','bulan')),
      annual: { year: (new Date()).getFullYear().toString(), category:'', rhk:'', q:'' },
      filter: {
        category: @json(request('category') ?? ''),
        rhk: @json(request('rhk') ?? ''),
      },
      init(){
        const month = @json(request('month'));
        if(month && /^\d{4}-\d{2}$/.test(month)) this.annual.year = month.slice(0,4);
        this.onCategoryChange(true);
      },
      setMode(m){
        this.mode = m;
        const url = new URL(window.location);
        url.searchParams.set('mode', m);
        window.history.replaceState({}, '', url);
      },
      onCategoryChange(initial=false){
        const allowed = Object.keys(__KINERJA.rhkMap(this.filter.category));
        if(!allowed.includes(this.filter.rhk)) this.filter.rhk = '';
      },
      annualLink(){
        const base = @js(route('sigap-kinerja.annual-public', ['year' => 'YEAR_MARK']))
                      .replace('YEAR_MARK', this.annual.year || (new Date()).getFullYear());
        const p = new URLSearchParams();
        if(this.annual.category) p.set('category', this.annual.category);
        if(this.annual.rhk)      p.set('rhk', this.annual.rhk);
        if(this.annual.q)        p.set('q', this.annual.q);
        const qs = p.toString();
        return qs ? `${base}?${qs}` : base;
      },
      async copyAnnualLink(){
        const link = this.annualLink();
        try{
          await navigator.clipboard.writeText(link);
          Swal.fire({ icon:'success', title:'Link Tahunan tersalin', text: link, timer: 1800, showConfirmButton:false });
        }catch(e){
          Swal.fire({ icon:'error', title:'Gagal menyalin', html:`<div class="text-left break-all">${link}</div>` });
        }
      }
    }
  }

  // ====== State modal upload modern & Async Upload ======
  function kinerjaModal(){
    return {
      form: { 
        category: '', 
        rhk: '', 
        title: '', 
        description: '', 
        date: new Date().toISOString().split('T')[0] 
      },
      filesList: [],
      dragover: false,
      isCompressing: false,
      isSubmitting: false,
      uploadStatusText: '',

      formatSize(bytes){
        if(!bytes) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
      },

      async handleFilesSelect(files){
        if(!files || files.length === 0) return;
        const fileArr = Array.from(files);

        if(this.filesList.length + fileArr.length > 30){
          Swal.fire({ icon: 'warning', title: 'Maksimal 30 File', text: 'Maksimal 30 foto/file bukti.' });
          return;
        }

        this.isCompressing = true;

        for(const file of fileArr){
          if(file.size > 20 * 1024 * 1024){
            Swal.fire({ icon: 'error', title: 'File Terlalu Besar', text: `File "${file.name}" lebih dari 20 MB.` });
            continue;
          }

          const isImage = file.type.startsWith('image/');
          if(isImage){
            try {
              const compressedFile = await this.compressImage(file);
              this.filesList.push({
                file: compressedFile,
                originalSize: file.size,
                compressedSize: compressedFile.size,
                previewUrl: URL.createObjectURL(compressedFile),
                isImage: true
              });
            } catch(e) {
              console.error(e);
              this.filesList.push({
                file: file,
                originalSize: file.size,
                compressedSize: file.size,
                previewUrl: URL.createObjectURL(file),
                isImage: true
              });
            }
          } else {
            this.filesList.push({
              file: file,
              originalSize: file.size,
              compressedSize: file.size,
              previewUrl: null,
              isImage: false
            });
          }
        }
        this.isCompressing = false;
      },

      compressImage(file){
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = (e) => {
            const img = new Image();
            img.src = e.target.result;
            img.onload = () => {
              const canvas = document.createElement('canvas');
              let width = img.width;
              let height = img.height;
              const maxDim = 1600;

              if (width > maxDim || height > maxDim) {
                if (width > height) {
                  height = Math.round((height * maxDim) / width);
                  width = maxDim;
                } else {
                  width = Math.round((width * maxDim) / height);
                  height = maxDim;
                }
              }

              canvas.width = width;
              canvas.height = height;
              const ctx = canvas.getContext('2d');
              ctx.drawImage(img, 0, 0, width, height);

              canvas.toBlob((blob) => {
                if(!blob) return reject(new Error("Blob error"));
                resolve(new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
                  type: 'image/jpeg',
                  lastModified: Date.now()
                }));
              }, 'image/jpeg', 0.75); // Kualitas 0.75 (hasil 200KB-400KB), jernih & tajam
            };
            img.onerror = reject;
          };
          reader.onerror = reject;
        });
      },

      removeFile(idx){
        if(this.filesList[idx]?.previewUrl) URL.revokeObjectURL(this.filesList[idx].previewUrl);
        this.filesList.splice(idx, 1);
      },

      async submitForm(){
        if(this.filesList.length === 0){
          Swal.fire({ icon: 'warning', title: 'File Kosong', text: 'Pilih minimal 1 file bukti.' });
          return;
        }
        if(!this.form.category || !this.form.title || !this.form.date){
          Swal.fire({ icon: 'warning', title: 'Form Belum Lengkap', text: 'Mohon isi Kategori, Judul, dan Tanggal.' });
          return;
        }

        this.isSubmitting = true;
        const uploadedPaths = [];
        const total = this.filesList.length;

        // 1. Upload file satu-persatu (ukuran ~300KB/file aman dari limit 2MB)
        for(let i = 0; i < total; i++){
          this.uploadStatusText = `Mengunggah file ${i + 1} dari ${total}...`;
          const item = this.filesList[i];
          
          const fd = new FormData();
          fd.append('file', item.file);
          fd.append('_token', '{{ csrf_token() }}');

          try {
            const res = await fetch('{{ route("sigap-kinerja.upload-media") }}', {
              method: 'POST',
              body: fd,
              headers: {
                'Accept': 'application/json'
              }
            });
            const json = await res.json();
            if(!res.ok) throw new Error(json.message || 'Gagal mengunggah file.');
            uploadedPaths.push(json.path);
          } catch(err) {
            this.isSubmitting = false;
            Swal.fire({ icon: 'error', title: 'Gagal Upload File', text: err.message });
            return;
          }
        }

        // 2. Simpan form data utama ke store
        this.uploadStatusText = 'Menyimpan data kegiatan...';
        try {
          const resFinal = await fetch('{{ route("sigap-kinerja.store") }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}',
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              category: this.form.category,
              rhk: this.form.rhk,
              title: this.form.title,
              description: this.form.description,
              date: this.form.date,
              uploaded_paths: uploadedPaths
            })
          });
          const finalJson = await resFinal.json();
          
          if(!resFinal.ok) throw new Error(finalJson.message || 'Gagal menyimpan data.');

          Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Bukti kinerja berhasil disimpan!',
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            window.location.reload();
          });

        } catch(err) {
          this.isSubmitting = false;
          Swal.fire({ icon: 'error', title: 'Gagal', text: err.message });
        }
      }
    }
  }

  // Controls Modal
  const mk = document.getElementById('modalKinerja');
  const bt = document.getElementById('btnTambahKinerja');
  if (bt && mk) bt.addEventListener('click', () => mk.classList.remove('hidden'));
  function closeKinerjaModal(){ mk?.classList.add('hidden'); }

  // Copy Link per item
  async function copyLink(link){
    try{
      await navigator.clipboard.writeText(link);
      Swal.fire({ icon:'success', title:'Tautan tersalin', text:'Tempel di sistem SKP PRO.', timer: 1800, showConfirmButton:false });
    }catch(e){
      Swal.fire({ icon:'error', title:'Gagal menyalin', text:'Coba lagi atau klik kanan salin tautan.' });
    }
  }
</script>

@if(session('success'))
<script>Swal.fire({ icon:'success', title:'Berhasil', text:@json(session('success')), timer:2200, showConfirmButton:false });</script>
@endif
@if($errors->any())
<script>Swal.fire({ icon:'error', title:'Gagal', text:@json($errors->first()) });</script>
@endif
@endpush