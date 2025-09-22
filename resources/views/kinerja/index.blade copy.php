@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Kinerja</h1>
      <p class="text-sm text-gray-600 mt-1">
        Pilih mode <b>Bulanan</b> untuk kirim bukti harian, atau <b>Tahunan</b> untuk 1 tautan berisi semua bukti setahun.
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
            <input name="category" list="dl_kategori" value="{{ request('category') }}"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Contoh: KINERJA A">
          </label>

          <label class="block lg:col-span-2">
            <span class="block text-sm font-semibold text-gray-700">RHK</span>
            <input name="rhk" list="dl_rhk" value="{{ request('rhk') }}"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Contoh: H">
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
          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Tahun</span>
            <input x-model="annual.year" type="number" min="2020" max="2100"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
            <p class="text-xs text-gray-500 mt-1">Contoh: 2025</p>
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">Kategori (opsional)</span>
            <input x-model="annual.category" list="dl_kategori"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Kosongkan untuk semua">
          </label>

          <label class="block">
            <span class="block text-sm font-semibold text-gray-700">RHK (opsional)</span>
            <input x-model="annual.rhk" list="dl_rhk"
                   class="mt-1.5 w-full rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon"
                   placeholder="Kosongkan untuk semua">
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
             class="px-5 py-2.5 rounded-xl bg-gray-900 text-white hover:bg-black">
            Lihat Laporan Tahunan
          </a>
          <button type="button" @click="copyAnnualLink()"
                  class="px-5 py-2.5 rounded-xl bg-maroon text-white hover:bg-maroon-800">
            Salin Link Laporan Tahunan
          </button>
        </div>

        <p class="text-xs text-gray-500 mt-3">
          Tautan ini menampilkan <b>semua bukti setahun penuh</b> sesuai filter lalu bisa dibagikan ke pemeriksa.
        </p>
      </div>
    </form>

    <!-- Shared datalist (dipakai di kedua mode) -->
    <datalist id="dl_kategori">
      @foreach(($categories ?? ['KINERJA A','Rapat','Sosialisasi','Bimtek','Kunjungan','Pelatihan','Lainnya']) as $kat)
        <option value="{{ $kat }}"></option>
      @endforeach
    </datalist>
    <datalist id="dl_rhk">
      @foreach(($rhks ?? ['H','RHK-1: Inovasi','RHK-2: Penelitian','RHK-3: Diseminasi','RHK-4: Kemitraan']) as $r)
        <option value="{{ $r }}"></option>
      @endforeach
    </datalist>
  </div>
</section>

<!-- GRID BUKTI hanya muncul di mode Bulan -->
@if(request('mode', 'bulan') === 'bulan')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse ($items as $it)
      @php $tgl = \Carbon\Carbon::parse($it['date'] ?? now())->locale('id')->translatedFormat('d F Y'); @endphp
      <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col">
        <div class="relative">
          <img src="{{ $it['thumb_url'] ?? asset('images/thumb/photo-placeholder.jpg') }}"
               alt="{{ $it['title'] }}" class="w-full h-44 object-cover">
          <div class="absolute top-2 left-2 flex gap-1">
            <span class="px-2 py-0.5 rounded text-xs bg-maroon text-white">{{ $it['category'] }}</span>
            @if(!empty($it['rhk']))
              <span class="px-2 py-0.5 rounded text-[11px] bg-gray-900/80 text-white">{{ $it['rhk'] }}</span>
            @endif
          </div>
        </div>
        <div class="p-3 flex-1 flex flex-col gap-1">
          <h3 class="font-semibold text-gray-900 leading-snug line-clamp-2">{{ $it['title'] }}</h3>
          <p class="text-xs text-gray-600">{{ $tgl }}</p>
          @if(!empty($it['description']))
            <p class="text-sm text-gray-700 line-clamp-2 mt-1">{{ $it['description'] }}</p>
          @endif
        </div>
        <div class="p-3 pt-0 grid grid-cols-3 gap-2">
          <a href="{{ route('sigap-kinerja.public', $it['id']) }}" target="_blank"
             class="col-span-1 px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm text-center">Lihat</a>
          <button type="button"
                  class="col-span-2 px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm"
                  onclick="copyLink(@js(route('sigap-kinerja.public', $it['id'])))">Salin Link</button>
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
</section>
@endif

@if($isAdminDemo)
<!-- MODAL UPLOAD (DUMMY) -->
<div id="modalKinerja" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeKinerjaModal()"></div>
  <div class="relative z-10 mx-auto max-w-3xl px-4 py-8">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
        <h2 class="text-white text-lg font-bold">Upload Bukti Kinerja</h2>
        <p class="text-white/80 text-xs mt-0.5">Dummy: belum menyimpan ke database.</p>
      </div>

      <form method="POST" action="{{ route('sigap-kinerja.store') }}" enctype="multipart/form-data" class="p-5 grid sm:grid-cols-2 gap-4">
        @csrf
        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Kategori</span>
          <input name="category" list="dl_kategori" required class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Pilih/ketik kategori…">
        </label>
        <label class="block">
          <span class="text-sm font-semibold text-gray-700">RHK</span>
          <input name="rhk" list="dl_rhk" class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Pilih/ketik RHK…">
        </label>
        <label class="block sm:col-span-2">
          <span class="text-sm font-semibold text-gray-700">Judul / Nama Kegiatan</span>
          <input name="title" type="text" required class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Contoh: Sosialisasi SIGAP BRIDA di Balaikota">
        </label>
        <label class="block sm:col-span-2">
          <span class="text-sm font-semibold text-gray-700">Deskripsi (opsional)</span>
          <textarea name="description" rows="3" class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Ringkas, agar mudah dicari."></textarea>
        </label>
        <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">File Bukti</span>
            <input name="file" type="file" accept=".jpg,.jpeg,.png,.pdf" required class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
            <p class="text-[12px] text-gray-500 mt-1">Utamakan foto JPG/PNG. PDF juga didukung.</p>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Thumbnail (opsional)</span>
            <input name="thumb" type="file" accept=".jpg,.jpeg,.png" class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
          </label>
        </div>
        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Tanggal Kegiatan</span>
          <input name="date" type="date" required class="mt-1.5 w-full rounded border border-gray-300 p-2 focus:border-maroon focus:ring-maroon">
        </label>
        <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
          <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeKinerjaModal()">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan (Dummy)</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection

@push('scripts')
<script>
  // Alpine state
  function kinerjaFilter(){
    return {
      mode: @json(request('mode','bulan')),
      annual: {
        year: (new Date()).getFullYear().toString(),
        category: '',
        rhk: '',
        q: ''
      },
      init(){
        // kalau user sudah isi month di query, set default year di tahunan
        const month = @json(request('month'));
        if(month && /^\d{4}-\d{2}$/.test(month)) this.annual.year = month.slice(0,4);
      },
      setMode(m){
        this.mode = m;
        // update query 'mode' tanpa reload (biar share state)
        const url = new URL(window.location);
        url.searchParams.set('mode', m);
        window.history.replaceState({}, '', url);
      },
      annualLink(){
        const base = @js(route('sigap-kinerja.annual-public', ['year' => 'YEAR_MARK'])).replace('YEAR_MARK', this.annual.year || (new Date()).getFullYear());
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

  // Modal Upload
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
