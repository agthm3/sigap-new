@extends('layouts.page')

@section('content')
<!-- HERO SECTION -->
<section class="relative overflow-hidden bg-linear-to-br from-maroon via-maroon-800 to-maroon-900 text-white">
  <div class="max-w-7xl mx-auto px-4 py-14 sm:py-20 relative z-10">
    <div class="text-center max-w-3xl mx-auto space-y-4">
      <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs font-semibold backdrop-blur-xs">
        <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
        Sistem Informasi Penghargaan & Insentif Inovasi Daerah
      </span>
      <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
        Direktori Prestasi Inovasi <br class="hidden sm:inline">Kota Makassar
      </h1>
      <p class="text-white/80 text-sm sm:text-base leading-relaxed">
        Pangkalan data resmi rekam jejak prestasi, penghargaan, dan insentif inovator daerah Pemerintah Kota Makassar yang telah terverifikasi oleh Badan Riset dan Inovasi Daerah (BRIDA).
      </p>

      {{-- Action Button --}}
      <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
        @auth
          <a href="{{ route('sigap-siipid.index') }}" 
             class="px-5 py-2.5 rounded-xl bg-white text-maroon font-bold text-xs hover:bg-white/90 shadow-md transition">
            Buka Dashboard Inovator →
          </a>
        @else
          <a href="{{ route('login') }}" 
             class="px-5 py-2.5 rounded-xl bg-white text-maroon font-bold text-xs hover:bg-white/90 shadow-md transition">
            Masuk untuk Mengusulkan Prestasi →
          </a>
        @endauth
        <a href="#katalog" class="px-5 py-2.5 rounded-xl bg-maroon-700/60 text-white border border-white/30 font-semibold text-xs hover:bg-maroon-700/80 transition">
          Jelajahi Galeri Prestasi ↓
        </a>
      </div>
    </div>
  </div>
</section>

<!-- STATISTIK RINGKAS -->
<section class="py-8 bg-white border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center text-xl font-bold">
          🏆
        </div>
        <div>
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Prestasi Terverifikasi</span>
          <div class="text-2xl font-extrabold text-gray-900 mt-0.5">{{ $totalPrestasi }}</div>
        </div>
      </div>

      <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl font-bold">
          🏛️
        </div>
        <div>
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Prestasi Perangkat Daerah (ASN)</span>
          <div class="text-2xl font-extrabold text-blue-800 mt-0.5">{{ $totalAsn }}</div>
        </div>
      </div>

      <div class="p-5 rounded-2xl bg-gray-50 border border-gray-100 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl font-bold">
          💡
        </div>
        <div>
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Prestasi Inovasi Masyarakat</span>
          <div class="text-2xl font-extrabold text-amber-900 mt-0.5">{{ $totalMasyarakat }}</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- KATALOG PRESTASI -->
<section id="katalog" class="py-12 bg-gray-50/60 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 space-y-8">

    {{-- Filter & Search Form --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-2xs">
      <form action="{{ route('public.siipid.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
        
        {{-- Input Search --}}
        <div class="lg:col-span-2">
          <label class="block font-bold text-gray-700 mb-1">Cari Inovator / Prestasi</label>
          <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama inovator, judul inovasi..." class="w-full rounded-xl border-gray-300 text-xs py-2 focus:ring-maroon focus:border-maroon">
        </div>

        {{-- Filter Tingkat --}}
        <div>
          <label class="block font-bold text-gray-700 mb-1">Tingkat Prestasi</label>
          <select name="tingkat" class="w-full rounded-xl border-gray-300 text-xs py-2 focus:ring-maroon focus:border-maroon">
            <option value="">Semua Tingkat</option>
            <option value="kota" @selected(request('tingkat') === 'kota')>Tingkat Kota</option>
            <option value="provinsi" @selected(request('tingkat') === 'provinsi')>Tingkat Provinsi</option>
            <option value="nasional" @selected(request('tingkat') === 'nasional')>Tingkat Nasional</option>
            <option value="internasional" @selected(request('tingkat') === 'internasional')>Tingkat Internasional</option>
            <option value="khusus" @selected(request('tingkat') === 'khusus')>Kategori Khusus</option>
          </select>
        </div>

        {{-- Filter Kategori Sasaran --}}
        <div>
          <label class="block font-bold text-gray-700 mb-1">Kategori Inovator</label>
          <select name="sasaran" class="w-full rounded-xl border-gray-300 text-xs py-2 focus:ring-maroon focus:border-maroon">
            <option value="">Semua Kategori</option>
            <option value="asn" @selected(request('sasaran') === 'asn')>ASN / Perangkat Daerah</option>
            <option value="masyarakat" @selected(request('sasaran') === 'masyarakat')>Masyarakat / Komunitas</option>
          </select>
        </div>

        {{-- Tombol Filter --}}
        <div class="flex items-end gap-2">
          <button type="submit" class="w-full py-2 bg-maroon text-white font-bold rounded-xl hover:bg-maroon-700 transition shadow-2xs">
            Terapkan Filter
          </button>
          @if(request()->hasAny(['q', 'tingkat', 'sasaran', 'tahun']))
            <a href="{{ route('public.siipid.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-semibold transition" title="Reset">
              ✕
            </a>
          @endif
        </div>

      </form>
    </div>

    {{-- Grid Kartu Prestasi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($prestasis as $item)
        <div class="bg-white rounded-3xl border border-gray-200/80 shadow-2xs hover:shadow-md hover:border-maroon/30 transition-all flex flex-col justify-between overflow-hidden">
          
          <div class="p-6 space-y-4">
            {{-- Badge Tingkat & Tahun --}}
            <div class="flex items-center justify-between gap-2">
              <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                🏆 {{ $item->tingkat_label }}
              </span>
              <span class="text-xs font-bold text-gray-400">Tahun {{ $item->tahun_perolehan }}</span>
            </div>

            {{-- Judul Prestasi --}}
            <div>
              <h3 class="font-extrabold text-gray-900 text-base leading-snug line-clamp-2">
                {{ $item->nama_prestasi }}
              </h3>
              <p class="text-xs text-gray-500 mt-1 font-medium line-clamp-1">
                Ajang: {{ $item->ajang_kompetisi }} • <strong class="text-maroon">{{ $item->peringkat_capaian ?: 'Penghargaan Resmi' }}</strong>
              </p>
            </div>

            {{-- Inovasi Induk --}}
            <div class="p-3.5 rounded-2xl bg-gray-50 border border-gray-100 space-y-1">
              <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-wider">Inovasi Induk:</span>
              <div class="text-xs font-bold text-gray-800 line-clamp-2">
                💡 {{ $item->innovable->judul ?? 'Inovasi Daerah' }}
              </div>
            </div>

            {{-- Inovator Profile --}}
            <div class="flex items-center gap-3 pt-1">
              <div class="w-9 h-9 rounded-full bg-maroon/10 text-maroon font-extrabold flex items-center justify-center text-xs shrink-0">
                {{ strtoupper(substr($item->nama_inovator, 0, 2)) }}
              </div>
              <div class="truncate">
                <p class="text-xs font-bold text-gray-900 truncate">{{ $item->nama_inovator }}</p>
                <p class="text-[11px] text-gray-400 truncate">{{ $item->opd_instansi ?: 'Kota Makassar' }}</p>
              </div>
            </div>
          </div>

          {{-- Footer Card --}}
          <div class="px-6 py-3.5 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between">
            <span class="text-[11px] font-semibold text-gray-500">{{ $item->lembaga_pemberi }}</span>
            <a href="{{ route('public.siipid.show', $item->uuid) }}" class="text-xs font-bold text-maroon hover:underline flex items-center gap-1">
              <span>Detail Piagam</span>
              <span>→</span>
            </a>
          </div>

        </div>
      @empty
        <div class="col-span-full py-16 text-center text-gray-400 bg-white rounded-3xl border border-gray-200">
          <p class="text-4xl">🏛️</p>
          <h4 class="font-bold text-gray-700 text-base mt-2">Belum ada data prestasi yang terpublikasi</h4>
          <p class="text-xs text-gray-500 mt-1">Data prestasi terverifikasi akan otomatis dimunculkan di halaman ini.</p>
        </div>
      @endforelse
    </div>

    {{-- Pagination --}}
    @if($prestasis->hasPages())
      <div class="pt-4">
        {{ $prestasis->links() }}
      </div>
    @endif

  </div>
</section>
@endsection
