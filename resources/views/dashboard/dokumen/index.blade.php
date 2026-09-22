@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="max-w-7xl mx-auto px-4 py-4 sm:py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Dokumen Umum</h1>
      <p class="text-sm text-gray-600 mt-1">
        Katalog arsip dokumen terbuka dan folder publik BRIDA.
      </p>
    </div>

    <!-- Tombol Aksi Kanan Atas -->
    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-dokumen.folder.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm font-semibold shadow-2xs">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Folder Publik
      </a>
      <a href="{{ route('sigap-dokumen.upload') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition text-sm font-semibold shadow-sm">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
        </svg>
        Tambah Dokumen
      </a>
    </div>
  </div>
</section>

<!-- Filter & Search Sentral -->
<section class="max-w-7xl mx-auto px-4 pb-6" 
         x-data="{
           submitSearch() {
             $refs.searchForm.submit();
           }
         }">
  <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-2xs">
    <form x-ref="searchForm" class="grid lg:grid-cols-4 gap-3" method="GET" action="{{ route('sigap-dokumen.index') }}">
      
      <!-- Input Pencarian -->
      <div class="lg:col-span-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold uppercase tracking-wider text-gray-600">Pencarian Cerdas &amp; Komprehensif</label>
          <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Live auto-filter
          </span>
        </div>
        <div class="relative mt-1">
          <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </span>
          <input id="q" 
                 name="q" 
                 type="search" 
                 @input.debounce.450ms="submitSearch()"
                 class="w-full rounded-lg border border-gray-300 pl-9 pr-3 p-2 text-sm focus:border-maroon focus:ring-maroon" 
                 placeholder="Ketik judul, no. surat, instansi/mitra, tagar (#), atau rak..." 
                 value="{{ request('q') }}">
        </div>
      </div>

      <!-- Filter Kategori -->
      <div>
        <label class="text-xs font-bold uppercase tracking-wider text-gray-600">Kategori Dokumen</label>
        <select name="category" 
                id="f_kat" 
                @change="submitSearch()"
                class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
          <option value="">Semua Kategori</option>
          <?php foreach (['Surat Keputusan', 'Laporan', 'Formulir', 'Privasi', 'Dokumen Teknis'] as $item): ?>
            <option value="{{ $item }}" @selected(request('category') == $item)>{{$item }}</option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Filter Tahun -->
      <div>
        <label class="text-xs font-bold uppercase tracking-wider text-gray-600">Tahun Arsip</label>
        <select name="year" 
                id="f_th" 
                @change="submitSearch()"
                class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
          <option value="">Semua Tahun</option>
          <?php for ($y = now()->year + 1; $y >= now()->year - 10; $y--): ?>
            <option value="{{ $y }}" @selected(request('year') == $y)>{{$y }}</option>
          <?php endfor; ?>
        </select>
      </div>

      <!-- Action Button Status -->
      <div class="lg:col-span-4 flex items-center justify-between pt-1 border-t border-gray-100">
        <p class="text-[11px] text-gray-400">
          *Mengetik kata kunci atau mengubah filter akan otomatis memfilter folder dan berkas.
        </p>
        <div class="flex items-center gap-2">
          @if(request('q') || request('category') || request('year'))
            <a href="{{ route('sigap-dokumen.index') }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-600 transition">
              Reset Filter x
            </a>
          @endif
          <button type="submit" class="px-4 py-1.5 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition text-xs font-semibold shadow-2xs">
            Cari Manual
          </button>
        </div>
      </div>
    </form>
  </div>
</section>

<!-- Section Folder Publik dengan Toggle Tampilan (Grid Card vs List Row) -->
<section class="max-w-7xl mx-auto px-4 pb-6" 
         x-data="{ 
           folderView: localStorage.getItem('sigap_folder_view') || 'grid',
           setView(v) { 
             this.folderView = v; 
             localStorage.setItem('sigap_folder_view', v); 
           } 
         }">
  
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center gap-2">
      <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500">Folder Publik</h2>
      <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">{{ $folders->count() }} Folder</span>
    </div>
    
    <!-- Tombol Sakelar View Mode (Grid vs List) -->
    <div class="inline-flex items-center p-0.5 bg-gray-200/80 rounded-lg border border-gray-300">
      <button type="button" 
              @click="setView('grid')" 
              :class="folderView === 'grid' ? 'bg-white text-maroon shadow-2xs font-bold' : 'text-gray-500 hover:text-gray-800'"
              title="Tampilan Grid / Ikon Kartu"
              class="flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs transition">
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
          <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        <span class="hidden sm:inline">Grid</span>
      </button>

      <button type="button" 
              @click="setView('list')" 
              :class="folderView === 'list' ? 'bg-white text-maroon shadow-2xs font-bold' : 'text-gray-500 hover:text-gray-800'"
              title="Tampilan Baris Judul"
              class="flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs transition">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        <span class="hidden sm:inline">Baris</span>
      </button>
    </div>
  </div>

  <!-- MODE 1: GRID VIEW -->
  <div x-show="folderView === 'grid'" 
       x-transition.opacity.duration.200ms
       class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
    @if($folders->isNotEmpty())
      <?php foreach ($folders as$item): ?>
        <div x-data="{ openMenu: false }" 
             class="relative group p-4 bg-white border border-gray-200 rounded-xl hover:border-maroon/50 hover:shadow-md transition flex flex-col justify-between">
          
          <div class="flex items-start justify-between">
            <a href="{{ route('sigap-dokumen.folder.show', $item) }}" class="block">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $item->color ?? '#7a2222' }};">
                <span class="text-lg">{{ $item->icon ?? '📁' }}</span>
              </div>
            </a>

            <div class="flex items-center gap-1">
              <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">
                {{ $item->classification_code ?? 'DIR' }}
              </span>

              <button type="button" 
                      @click.stop="openMenu = !openMenu" 
                      class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Dropdown Aksi Folder Grid (z-50) -->
          <div x-show="openMenu" 
               @click.away="openMenu = false" 
               x-transition 
               class="absolute right-2 top-10 z-50 w-44 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 text-xs font-semibold text-gray-700">
            @if($item->user_id === auth()->id() || auth()->user()->hasRole('admin'))
              <a href="{{ route('sigap-dokumen.folder.edit', $item) }}" 
                 class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit Folder
              </a>

              <a href="{{ route('sigap-dokumen.folder.share', $item) }}" 
                 class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                Bagikan Tautan
              </a>
            @endif

            <a href="{{ route('sigap-dokumen.folder.download-zip', $item) }}" 
               class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download ZIP
            </a>

            <!-- Tombol Hapus Folder Grid di Dalam Dropdown -->
            @if($item->user_id === auth()->id() || auth()->user()->hasRole('admin'))
              <div class="border-t border-gray-100 my-1"></div>
              <form action="{{ route('sigap-dokumen.folder.destroy', $item) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus folder ini akan menghapus permanen seluruh subfolder dan berkas dokumen di dalamnya! Lanjutkan?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 hover:text-red-700 transition rounded-md text-left font-semibold">
                  <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  <span>Hapus Folder</span>
                </button>
              </form>
            @endif
          </div>

          <a href="{{ route('sigap-dokumen.folder.show', $item) }}" class="block mt-3">
            <h3 class="text-sm font-semibold text-gray-800 group-hover:text-maroon truncate" title="{{ $item->name }}">
              {{ $item->name }}
            </h3>
            <p class="text-[11px] text-gray-500 mt-0.5">
              {{ $item->documents_count }} Berkas &bull; {{$item->subfolders_count }} Folder
            </p>
          </a>
        </div>
      <?php endforeach; ?>
    @else
      <div class="col-span-full py-6 px-4 bg-white border border-dashed border-gray-300 rounded-2xl text-center">
        <p class="text-xs text-gray-500">Belum ada folder publik yang dibuat.</p>
      </div>
    @endif
  </div>

  <!-- MODE 2: LIST VIEW -->
  <div x-show="folderView === 'list'" 
       x-transition.opacity.duration.200ms
       class="bg-white border border-gray-200 rounded-2xl shadow-2xs divide-y divide-gray-100">
    @if($folders->isNotEmpty())
      <?php foreach ($folders as $index =>$item): ?>
        <div x-data="{ openMenu: false }" 
             :class="openMenu ? 'z-40 relative bg-gray-50/90' : 'relative'"
             class="flex items-center justify-between p-3.5 hover:bg-gray-50/80 transition {{ $index === 0 ? 'rounded-t-2xl' : '' }} {{$loop->last ?? false ? 'rounded-b-2xl' : '' }}">
          
          <a href="{{ route('sigap-dokumen.folder.show', $item) }}" class="flex items-center gap-3 min-w-0 flex-1 pr-4">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white shrink-0 text-sm" 
                 style="background-color: {{ $item->color ?? '#7a2222' }};">
              <span>{{ $item->icon ?? '📁' }}</span>
            </div>
            <div class="truncate">
              <h3 class="text-xs font-bold text-gray-800 hover:text-maroon truncate" title="{{ $item->name }}">
                {{ $item->name }}
              </h3>
              <p class="text-[10px] text-gray-400 mt-0.5">
                {{ $item->documents_count }} Berkas &bull; {{$item->subfolders_count }} Subfolder
              </p>
            </div>
          </a>

          <div class="flex items-center gap-2 shrink-0">
            <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
              {{ $item->classification_code ?? 'DIR' }}
            </span>

            <div class="relative">
              <button type="button" 
                      @click.stop="openMenu = !openMenu" 
                      class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-200 transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
              </button>

              <!-- Dropdown Aksi Folder (List View) -->
              <div x-show="openMenu" 
                   @click.away="openMenu = false" 
                   x-transition 
                   class="absolute right-0 top-8 z-50 w-48 bg-white rounded-xl shadow-2xl border border-gray-200 py-1.5 text-xs font-semibold text-gray-700">
                @if($item->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                  <a href="{{ route('sigap-dokumen.folder.edit', $item) }}" 
                     class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Edit Folder
                  </a>

                  <a href="{{ route('sigap-dokumen.folder.share', $item) }}" 
                     class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                    </svg>
                    Bagikan Tautan
                  </a>
                @endif

                <a href="{{ route('sigap-dokumen.folder.download-zip', $item) }}" 
                   class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                  </svg>
                  Download ZIP
                </a>

                <!-- Tombol Hapus Folder List di Dalam Dropdown -->
                @if($item->user_id === auth()->id() || auth()->user()->hasRole('admin'))
                  <div class="border-t border-gray-100 my-1"></div>
                  <form action="{{ route('sigap-dokumen.folder.destroy', $item) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus folder ini akan menghapus permanen seluruh subfolder dan berkas dokumen di dalamnya! Lanjutkan?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 hover:text-red-700 transition rounded-md text-left font-semibold">
                      <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      <span>Hapus Folder</span>
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    @else
      <div class="py-6 px-4 text-center text-xs text-gray-500">
        Belum ada folder publik yang dibuat.
      </div>
    @endif
  </div>
</section>

<!-- Table Dokumen Lepas (Publik) -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2xs">
    <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
      <span>Berkas Publik Lepas (Tanpa Folder)</span>
      <span>Total: {{ $docs->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b bg-gray-50 text-gray-600">
            <th class="px-4 py-3">Dokumen</th>
            <th class="px-4 py-3">Alias</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Tahun</th>
            <th class="px-4 py-3">Lokasi Fisik</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y text-gray-700">
          @if($docs->isNotEmpty())
            <?php foreach ($docs as$item): ?>
              <tr class="hover:bg-gray-50/70 transition">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    @if (!empty($item->thumb_path))
                      <img class="w-11 h-11 rounded-lg object-cover ring-1 ring-gray-200" src="{{ asset('storage/'.$item->thumb_path) }}" alt="">
                    @else
                      <div class="w-11 h-11 rounded-lg bg-maroon/10 text-maroon font-bold flex items-center justify-center text-xs shrink-0">
                        PDF
                      </div>
                    @endif
                    <div>
                      <a href="{{ route('sigap-dokumen.show', $item) }}" class="font-medium text-gray-900 hover:text-maroon">
                        {{ $item->title }}
                      </a>
                      <p class="text-xs text-gray-500 line-clamp-1">{{ Str::limit($item->description, 40) }}</p>
                      @if(!empty($item->tags))
                        <?php $tagList = is_array($item->tags) ? $item->tags : explode(',',$item->tags); ?>
                        <div class="flex flex-wrap gap-1 mt-1">
                          <?php foreach ($tagList as$t): ?>
                            <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.2 rounded font-medium">#{{ trim($t) }}</span>
                          <?php endforeach; ?>
                        </div>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item->alias }}</td>
                <td class="px-4 py-3 text-xs">{{ $item->category }}</td>
                <td class="px-4 py-3 text-xs">{{ $item->year }}</td>
                <td class="px-4 py-3 text-xs">
                  @if(filled($item->physical_rack) or filled($item->physical_row))
                    Rak {{ $item->physical_rack ?? '-' }}, No. {{ $item->physical_row ?? '-' }}
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('sigap-dokumen.show', $item) }}" target="_blank" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-xs font-semibold">View</a>
                    <a href="{{ route('sigap-dokumen.download', $item) }}" target="_blank" class="px-3 py-1.5 rounded-md bg-maroon text-white hover:bg-maroon-800 transition text-xs font-semibold">Download</a>
                    
                    @if($item->created_by === auth()->id() || auth()->user()->hasRole('admin'))
                    <a href="{{ route('sigap-dokumen.edit', $item->id) }}" class="px-3 py-1.5 rounded-md border border-gray-300 hover:bg-gray-50 text-xs">Edit</a>
                    <button type="button"
                            class="px-3 py-1.5 rounded-md border border-red-200 text-red-700 hover:bg-red-50 text-xs"
                            onclick="confirmHapus({{ $item->id }}, @js($item->title))">
                      Hapus
                    </button>
                    <form id="form-delete-{{ $item->id }}" action="{{ route('sigap-dokumen.destroy', $item->id) }}" method="POST" class="hidden">
                      @csrf
                      @method('DELETE')
                    </form>
                    @endif
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          @else
            <tr>
              <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                Tidak ada dokumen lepas ditemukan.
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    @if($docs->hasPages())
    <div class="px-4 py-3 border-t bg-gray-50">
      {{ $docs->links() }}
    </div>
    @endif
  </div>
</section>

@push('scripts')
<script>
function confirmHapus(id, title) {
  Swal.fire({
    title: 'Hapus Dokumen?',
    text: `Apakah Anda yakin ingin menghapus dokumen "${title}"?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#7a2222',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('form-delete-' + id).submit();
    }
  });
}
</script>
@endpush
@endsection