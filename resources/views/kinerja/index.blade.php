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

    @if($isAdminDemo)
    <button id="btnTambahKinerja"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 5v14M5 12h14"/></svg>
      Upload Bukti
    </button>
    @endif
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 -mt-3">
  <div x-data="kinerjaFilter()" x-init="init()" class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6">

    <!-- Saklar Mode -->
    <div class="inline-flex rounded-xl overflow-hidden border border-gray-300">
      <button type="button" @click="setMode('bulan')"
        :class="mode==='bulan' ? 'bg-maroon text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
        class="px-4 py-2 text-sm font-semibold w-36">Bulanan</button>
      <button type="button" @click="setMode('tahun')"
        :class="mode==='tahun' ? 'bg-maroon text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
        class="px-4 py-2 text-sm font-semibold w-36">Tahunan (1 Link)</button>
    </div>

    <!-- Form Satu Pintu -->
    <form method="GET" action="{{ route('sigap-kinerja.index') }}" class="mt-5">
      <input type="hidden" name="mode" :value="mode">

      <!-- BULANAN -->
      <div x-show="mode==='bulan'" x-cloak>
        <div class="grid lg:grid-cols-4 gap-4">
          <!-- Pencarian -->
          <label class="block lg:col-span-2">
            <span class="block text-sm font-semibold text-gray-700">Cari cepat</span>
            <input name="q" type="search" value="{{ request('q') }}"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Nama kegiatan / keterangan…">
          </label>

          <!-- Bulan -->
          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Bulan</span>
            <input name="month" type="month" value="{{ request('month') }}"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
          </label>

          <!-- Kategori (kode) — Combobox -->
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

          <!-- RHK (kode) — Combobox -->
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
          <button type="submit" class="px-5 py-2.5 rounded-xl bg-maroon text-white hover:bg-maroon-800">
            Terapkan Filter
          </button>
          <a href="{{ route('sigap-kinerja.index', ['mode'=>'bulan']) }}"
             class="px-5 py-2.5 rounded-xl border border-gray-300 hover:bg-gray-50">
            Bersihkan
          </a>
        </div>

        <p class="text-xs text-gray-500 mt-3">
          Setelah ketemu buktinya, klik <b>Salin Link</b> lalu tempel di sistem SKP PRO.
        </p>
      </div>

      <!-- TAHUNAN -->
      <div x-show="mode==='tahun'" x-cloak>
        <div class="grid md:grid-cols-4 gap-4">
          <!-- Tahun -->
          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Tahun</span>
            <input x-model="annual.year" type="number" min="2020" max="2100"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
            <p class="text-xs text-gray-500 mt-1">Contoh: 2025</p>
          </label>

          <!-- Kategori (opsional) — Combobox -->
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

          <!-- RHK (opsional) — Combobox -->
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

          <!-- Cari cepat (opsional) -->
          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Cari cepat (opsional)</span>
            <input x-model="annual.q" type="search"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Nama kegiatan / keterangan…">
          </label>
        </div>

        <div class="flex flex-wrap gap-3 mt-5">
          <a :href="annualLink()" target="_blank"
             class="px-5 py-2.5 rounded-xl bg-gray-900 text-white hover:bg-black">
            Lihat Laporan Tahunan
          </a>
          <button type="button" @click="copyAnnualLink()"
                  class="px-5 py-2.5 rounded-xl bg-maroon text-white hover:bg-maroon-800">
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

<!-- GRID BUKTI: muncul saat mode Bulan -->
@if(request('mode', 'bulan') === 'bulan')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse ($items as $it)
      @php $tgl = \Carbon\Carbon::parse($it['date'] ?? now())->locale('id')->translatedFormat('d F Y'); @endphp
      <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col">
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

        <div class="p-3 flex-1 flex flex-col gap-1">
          <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2" title="{{ $it['title'] }}">
            {{ \Illuminate\Support\Str::limit($it['title'], 90) }}
          </h3>
          <p class="text-xs text-gray-600">{{ $tgl }}</p>
          @if(!empty($it['description']))
            <p class="text-sm text-gray-700 line-clamp-2 mt-1" title="{{ $it['description'] }}">
              {{ \Illuminate\Support\Str::limit($it['description'], 120) }}
            </p>
          @endif
        </div>

        <div class="p-3 pt-0 grid grid-cols-3 gap-2">
          <a href="{{ route('sigap-kinerja.public', $it['id']) }}" target="_blank"
             class="col-span-1 px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm text-center">
            Lihat
          </a>
          <button type="button"
                  class="col-span-2 px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm"
                  onclick="copyLink(@js(route('sigap-kinerja.public', $it['id'])))">
            Salin Link
          </button>
        </div>
      </article>
    @empty
      <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center text-gray-600">
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
<!-- MODAL UPLOAD -->
<div id="modalKinerja" class="fixed inset-0 z-50 hidden" x-data="kinerjaModal()">
  <div class="absolute inset-0 bg-black/40" onclick="closeKinerjaModal()"></div>
  <div class="relative z-10 mx-auto max-w-3xl px-4 py-8">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
        <h2 class="text-white text-lg font-bold">Upload Bukti Kinerja</h2>
        <p class="text-white/80 text-xs mt-0.5">Unggah foto/PDF. Jika foto, otomatis jadi thumbnail.</p>
      </div>

      <form method="POST" action="{{ route('sigap-kinerja.store') }}" enctype="multipart/form-data" class="p-5 grid sm:grid-cols-2 gap-4">
        @csrf

        <!-- Kategori (Combobox) -->
        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Kategori</span>
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

        <!-- RHK (Combobox) -->
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
          <span class="text-sm font-semibold text-gray-700">Judul / Nama Kegiatan</span>
          <input name="title" type="text" required
                 class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon"
                 placeholder="Contoh: Sosialisasi SIGAP BRIDA di Balaikota">
        </label>

        <label class="block sm:col-span-2">
          <span class="text-sm font-semibold text-gray-700">Deskripsi (opsional)</span>
          <textarea name="description" rows="3"
                    class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon"
                    placeholder="Ringkas, agar mudah dicari."></textarea>
        </label>

        <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">File Bukti</span>
            <input name="file" type="file" accept=".jpg,.jpeg,.png,.pdf" required
                   class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
            <p class="text-[12px] text-gray-500 mt-1">Jika foto (jpg/png), otomatis jadi thumbnail.</p>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Thumbnail (opsional)</span>
            <input name="thumb" type="file" accept=".jpg,.jpeg,.png"
                   class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
            <p class="text-[12px] text-gray-500 mt-1">Kosongkan jika file bukti berupa foto.</p>
          </label>
        </div>

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Tanggal Kegiatan</span>
          <input name="date" type="date" required
                 class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>

        <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
          <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeKinerjaModal()">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection

@push('head')
<script>
  // Seed dari server ke GLOBAL (harus ada sebelum Alpine init)
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
<script>
  // ====== Reusable combobox Alpine component (searchable) ======
  // props: name, options(map {code:label}), initial, placeholder, disabled
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

      form: { category:'', rhk:'' },

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

  // ====== State modal upload ======
  function kinerjaModal(){
    return { form: { category:'', rhk:'' } }
  }

  // Modal Upload
  const mk = document.getElementById('modalKinerja');
  const bt = document.getElementById('btnTambahKinerja');
  if (bt && mk) bt.addEventListener('click', () => mk.classList.remove('hidden'));
  function closeKinerjaModal(){ mk?.classList.add('hidden'); }

  // Copy Link per item (kartu)
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
