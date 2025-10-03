@extends('layouts.page')

@section('content')
<section class="bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900">
  <div class="max-w-7xl mx-auto px-4 py-8 sm:py-10">
    <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
      <form class="grid sm:grid-cols-5 gap-3" action="{{ route('home.show') }}" method="GET">
        <div class="sm:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Kata Kunci</label>
          <input name="q" value="{{ request('q') }}" type="search" class="mt-1.5 w-full rounded-lg p-2 border border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Contoh: SK Sekretariat A, Laporan 2024, KTP…" />
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Kategori</label>
          <select name="category" class="mt-1.5 w-full rounded-lg p-2 border border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option value="Surat Keputusan" @selected(request('category')==='Surat Keputusan')>Surat Keputusan</option>
            <option value="Laporan" @selected(request('category')==='Laporan')>Laporan</option>
            <option value="Formulir" @selected(request('category')==='Formulir')>Formulir</option>
            <option value="Privasi" @selected(request('category')==='Privasi')>Privasi (KK/KTP)</option>
            <option value="Dokumen" @selected(request('category')==='Dokumen')>Dokumen</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Pihak Terkait</label>
          <input name="stakeholder" value="{{ request('stakeholder') }}" type="text" class="mt-1.5 w-full rounded-lg p-2 border border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Sekretariat A, Bidang X…" />
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Tahun</label>
          <select name="year" class="mt-1.5 w-full rounded-lg p-2 border border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @for($y = now()->year; $y >= now()->year-10; $y--)
              <option value="{{ $y }}" @selected(request('year')==$y)>{{ $y }}</option>
            @endfor
          </select>
        </div>

        <input type="hidden" name="sort" value="{{ request('sort','latest') }}">
        <div class="sm:col-span-5 flex flex-wrap gap-3 pt-1">
          <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">… Cari Dokumen</button>
          <a href="{{ route('home.show') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</a>
        </div>
      </form>
    </div>
  </div>
</section>

{{-- Results header --}}
<section class="max-w-7xl mx-auto px-4">
  <div class="py-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl font-extrabold text-maroon">Hasil Pencarian</h1>
      <p class="text-sm text-gray-600 mt-1">Menemukan {{ $documents->total() }} dokumen.</p>
    </div>
    <div class="flex items-center gap-3">
      <label class="text-sm text-gray-600">Urutkan</label>
      <form action="{{ route('home.show') }}" method="GET">
        @foreach(request()->except('sort') as $k=>$v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach
        <select name="sort" class="rounded-md border-gray-300 focus:border-maroon focus:ring-maroon text-sm" onchange="this.form.submit()">
          <option value="latest" @selected(request('sort','latest')==='latest')>Terbaru</option>
          <option value="title_asc" @selected(request('sort')==='title_asc')>Judul (A-Z)</option>
          <option value="popular" @selected(request('sort')==='popular')>Terpopuler</option>
        </select>
      </form>
    </div>
  </div>
</section>

{{-- Results list --}}
<section class="max-w-7xl mx-auto px-4 pb-12">
  <ul class="space-y-4">
    @forelse ($documents as $item)
      <li class="border border-gray-200 rounded-xl overflow-hidden">
        <div class="flex flex-col sm:flex-row">
          <div class="sm:w-48 shrink-0">
            <img class="w-full h-40 sm:h-full object-cover"
                 src="{{ $item->thumb_path ? asset('storage/'.$item->thumb_path) : 'https://images.unsplash.com/photo-1516387938699-a93567ec168e?q=80&w=1200&auto=format&fit=crop' }}"
                 alt="Thumbnail dokumen">
          </div>
          <div class="flex-1 p-4 sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
              @if($item->category)
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-maroon/10 text-maroon">{{ $item->category }}</span>
              @endif
              @if($item->year)
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-700">Tahun: {{ $item->year }}</span>
              @endif
              @if($item->stakeholder)
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-700">Pihak: {{ $item->stakeholder }}</span>
              @endif
              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded
                {{ $item->sensitivity==='private' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                {{ $item->sensitivity==='private' ? 'Akses Terkendali' : 'Publik' }}
              </span>
            </div>

            <h3 class="mt-2 text-lg font-semibold text-gray-900">{{ $item->title }}</h3>

            @if($item->alias)
              <p class="text-sm text-gray-600 mt-1"><span class="font-semibold">Alias:</span> {{ $item->alias }}</p>
            @endif

            @if($item->description)
              <p class="text-sm text-gray-700 mt-2 line-clamp-2">{{ $item->description }}</p>
            @endif

            <div class="mt-4 flex flex-wrap gap-2">
              <a href="{{ route('sigap-dokumen.show', $item->id) }}"
                 class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm">
                Lihat
              </a>
              @if($item->sensitivity==='public')
                <a href="{{ route('sigap-dokumen.download', $item->id) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 transition text-sm">
               Download
                </a>
              @else
                <span class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-200 text-gray-600 cursor-not-allowed text-sm" title="Unduh membutuhkan hak akses">
                 Download
                </span>
              @endif
            </div>
          </div>
        </div>
      </li>
    @empty
      <li class="text-sm text-gray-600">Tidak ada dokumen yang cocok.</li>
    @endforelse
  </ul>

  <div class="mt-8">
    {{ $documents->withQueryString()->links() }}
  </div>
</section>


@endsection