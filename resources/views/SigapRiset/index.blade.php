@extends('layouts.page')
@if(isset($isPublic) && !$isPublic)
  <div id="restricted-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white max-w-md w-full mx-4 rounded-xl p-6 shadow-xl relative">
      <button type="button" id="restricted-close" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
      <h3 class="text-lg font-bold text-maroon mb-2">🔒 Akses Terbatas</h3>
      <p class="text-sm text-gray-700 mb-3">
        Dokumen atau riset ini memiliki akses terbatas, hubungi admin melalui email untuk mendapatkan akses penuh.
      </p>
      <div class="flex gap-2">
        <a href="mailto:admin@brida.makassar.go.id"
           class="inline-flex items-center px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">
           Hubungi Admin
        </a>
        <a href="{{ route('sigap-riset.index') }}"
           class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
           Kembali
        </a>
      </div>
    </div>
  </div>
  <script>
    (function(){
      function closeNow(){
        var m = document.getElementById('restricted-modal');
        if(m){ m.parentNode.removeChild(m); }
      }
      document.addEventListener('keydown', function(e){
        if(e.key === 'Escape') closeNow();
      });
      document.addEventListener('click', function(e){
        var m = document.getElementById('restricted-modal');
        if(!m) return;
        if(e.target === m || e.target.id === 'restricted-close') closeNow();
      });
    })();
  </script>
@endif

@section('content')
@php use Illuminate\Support\Str; @endphp

{{-- SECTION 1: Header ringkas --}}
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-7xl mx-auto px-4 py-10 sm:py-12 lg:py-14">
    <div class="max-w-3xl">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">SIGAP <span class="text-orange-100">RISET</span></h1>
      <p class="mt-2 text-white/90 text-sm sm:text-base">
        Direktori penelitian BRIDA Kota Makassar. Telusuri judul, baca abstrak, ekspor sitasi, dan unduh naskah lengkap sesuai hak akses.
      </p>
    </div>
  </div>
</section>

{{-- SECTION 2: Filter & Tombol --}}
<section class="py-6 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <form action="{{ route('sigap-riset.index') }}"  method="GET" class="rounded-2xl border border-gray-200 p-4 sm:p-6 shadow-sm">
      <div class="grid gap-4 sm:gap-5">
        <div class="grid sm:grid-cols-3 gap-3">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kata Kunci</span>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Judul/abstrak: PDAM, IoT, pariwisata…"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Topik/Tag</span>
            <input type="text" name="tags" value="{{ request('tags') }}" placeholder="Sains Data, Lingkungan, Tata Kelola…"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Penulis</span>
            <input type="text" name="author" value="{{ request('author') }}" placeholder="Nama penulis"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-4 gap-3">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Pihak Terkait</span>
            <input type="text" name="stakeholder" value="{{ request('stakeholder') }}" placeholder="PDAM, DPMPTSP, DLH…"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tahun</span>
            <select name="year" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Semua</option>
              @for($y = now()->year; $y >= now()->year-7; $y--)
                <option value="{{ $y }}" @selected(request('year')==$y)>{{ $y }}</option>
              @endfor
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Jenis</span>
            <select name="type" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Semua</option>
              <option value="internal" @selected(request('type')==='internal')>Riset Internal BRIDA</option>
              <option value="kolaborasi" @selected(request('type')==='kolaborasi')>Kolaborasi/OPD</option>
              <option value="eksternal" @selected(request('type')==='eksternal')>Riset Eksternal Terkait</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Sort</span>
            <select name="sort" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="latest" @selected(request('sort','latest')==='latest')>Terbaru</option>
              <option value="oldest" @selected(request('sort')==='oldest')>Terlama</option>
              <option value="az" @selected(request('sort')==='az')>Judul A–Z</option>
              <option value="za" @selected(request('sort')==='za')>Judul Z–A</option>
            </select>
          </label>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition" type="submit">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            Cari Riset
          </button>
          <a href="{{ route('sigap-riset.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</a>

          <div class="ms-auto flex items-center gap-2">
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="only_public" value="1" @checked(request('only_public')) class="rounded text-maroon focus:ring-maroon">
              Hanya yang Public
            </label>
            <label class="inline-flex items-center gap-2 text-sm">
              <input type="checkbox" name="has_doi" value="1" @checked(request('has_doi')) class="rounded text-maroon focus:ring-maroon">
              Hanya punya DOI/URL
            </label>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>

{{-- SECTION 3: Hasil Riset (inline) --}}
<section class="py-8 bg-gray-50" id="hasil">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between mb-4">
      <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-maroon">Hasil Riset</h2>
        <p class="text-sm text-gray-600">Menampilkan {{ $risets->total() }} hasil.</p>
      </div>
      <div class="text-xs text-gray-500">*Beberapa naskah mungkin terbatas (Restricted)</div>
    </div>

    <div class="grid lg:grid-cols-2 gap-5">
      @forelse($risets as $i => $r)
      @php
        $authors = collect(is_array($r->authors) ? $r->authors : (json_decode($r->authors,true) ?: []))
                    ->pluck('name')->filter()->all();
        $tags = is_array($r->tags) ? $r->tags : (json_decode($r->tags,true) ?: []);
        $stakeholders = is_array($r->stakeholders) ? $r->stakeholders : (json_decode($r->stakeholders,true) ?: []);
        $access = $r->access ?? 'Public';
        $cite = (count($authors) ? implode(', ', $authors) : 'BRIDA') . " ({$r->year}). {$r->title}. BRIDA Kota Makassar" . ($r->doi ? " https://doi.org/{$r->doi}" : '');
      @endphp
@php
  $authors = collect(is_array($r->authors) ? $r->authors : (json_decode($r->authors,true) ?: []))
              ->pluck('name')->filter()->all();

  $tags = is_array($r->tags) ? $r->tags : (json_decode($r->tags,true) ?: []);
  $stakeholders = is_array($r->stakeholders) ? $r->stakeholders : (json_decode($r->stakeholders,true) ?: []);

  $access = $r->access ?? 'Public';
  $isPublicDoc = strtoupper(trim($access ?? 'Public')) === 'PUBLIC';

  $isPolicyBrief = isset($r->category) && $r->category === 'policy_brief';
  $hasYoutube    = $isPolicyBrief && !empty($r->youtube_url);

  $cite = (count($authors) ? implode(', ', $authors) : 'BRIDA')
          . " ({$r->year}). {$r->title}. BRIDA Kota Makassar"
          . ($r->doi ? " https://doi.org/{$r->doi}" : '');
@endphp

<article class="
  bg-white rounded-xl border p-5 shadow-sm hover:shadow-md transition
  {{ $isPolicyBrief ? 'border-yellow-300 ring-1 ring-yellow-100' : 'border-gray-200' }}
">
  <div class="flex items-start justify-between gap-3 flex-wrap">
    <div class="min-w-0">
      <h3 class="text-base sm:text-lg font-extrabold text-gray-900 leading-snug flex items-start flex-wrap gap-2">
        <span class="break-words">{{ $r->title }}</span>

        @if($isPolicyBrief)
          <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold bg-yellow-50 text-yellow-700 border border-yellow-300">
            Policy Brief
          </span>
        @endif
      </h3>
    </div>

    <span class="shrink-0 inline-flex items-center rounded-full px-2 py-1 text-xs
      {{ $access==='Public' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
      {{ $access }}
    </span>
  </div>

  <p class="mt-2 text-sm text-gray-700">
    {{ \Illuminate\Support\Str::limit($r->abstract, 240) }}
  </p>

  <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 border">{{ $r->year }}</span>

    @foreach($tags as $t)
      <span class="inline-flex px-2 py-1 rounded bg-maroon/10 text-maroon border border-maroon/20">#{{ $t }}</span>
    @endforeach
  </div>

  <dl class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
    <div>
      <dt class="text-gray-500">Penulis</dt>
      <dd class="font-medium text-gray-800">{{ $authors ? implode(', ', $authors) : '—' }}</dd>
    </div>
    <div>
      <dt class="text-gray-500">Pihak Terkait</dt>
      <dd class="font-medium text-gray-800">{{ $stakeholders ? implode(', ', $stakeholders) : '—' }}</dd>
    </div>
    <div>
      <dt class="text-gray-500">Lisensi</dt>
      <dd class="font-medium text-gray-800">{{ $r->license ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-gray-500">Berkas</dt>
      <dd class="font-medium text-gray-800">
        {{ $r->file_name }} {{ $r->file_size ? '• '.$r->file_size : '' }}
      </dd>
    </div>
    <div class="sm:col-span-2">
      <dt class="text-gray-500">DOI/URL</dt>
      <dd class="font-medium text-gray-800">{{ $r->doi ?: '—' }}</dd>
    </div>
  </dl>

  <div class="mt-4 flex flex-wrap gap-2">
    {{-- tombol utama --}}
    @if($hasYoutube)
      <a href="{{ route('sigap-riset.show', $r->id) }}#video"
         class="px-3 py-2 rounded-lg bg-yellow-400 text-gray-900 text-sm font-semibold hover:bg-yellow-300 transition">
         Tonton Video
      </a>
    @endif

    <a href="{{ route('sigap-riset.show', $r->id) }}"
       class="px-3 py-2 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">
       Lihat Detail
    </a>

    @if($isPublicDoc)
      <a href="{{ route('sigap-riset.download', $r->id) }}"
         class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
         Unduh PDF
      </a>
    @endif

    <button type="button"
      onclick="navigator.clipboard.writeText(this.nextElementSibling.textContent); this.innerText='Disalin!'; setTimeout(()=>this.innerText='Salin Sitasi',1400);"
      class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
      Salin Sitasi
    </button>
    <pre class="sr-only">{{ $cite }}</pre>

    @if($r->doi)
      <a href="https://doi.org/{{ $r->doi }}" target="_blank"
         class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
         Buka DOI
      </a>
    @endif

    @unless($isPublicDoc)
      <span class="text-xs text-yellow-700 bg-yellow-50 border border-yellow-200 px-2 py-1 rounded">
        Akses terbatas (Restricted)
      </span>
    @endunless
  </div>
</article>

      @empty
        <div class="lg:col-span-2">
          <div class="p-6 rounded-xl border border-dashed text-center text-gray-500 bg-white">Tidak ada hasil.</div>
        </div>
      @endforelse
    </div>

    <div class="mt-6">{{ $risets->onEachSide(1)->links() }}</div>
  </div>
</section>

{{-- SECTION 4–6 biarkan seperti punyamu atau pakai versi dummy sebelumnya --}}
@endsection
