@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-4 sm:py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Dokumen Saya</h1>
      <p class="text-sm text-gray-600 mt-1">
        Kelola folder kerja dan arsip berkas pribadi Anda secara terenkripsi &amp; terkunci.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-dokumen.folder.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition text-sm font-semibold shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Folder Baru
      </a>
      <a href="{{ route('sigap-dokumen.upload') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm font-semibold">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Upload Dokumen
      </a>
    </div>
  </div>
</section>

<!-- Section Daftar Folder -->
<section class="max-w-7xl mx-auto px-4 pb-6">
  <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Folder Saya</h2>
  
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
    @forelse ($folders as $item)
      <div x-data="{ openMenu: false }" 
           class="relative group p-4 bg-white border border-gray-200 rounded-xl hover:border-maroon/50 hover:shadow-md transition flex flex-col justify-between">
        
        <!-- Header Folder (Ikon + Tiga Titik) -->
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

            <!-- Tombol Titik Tiga -->
            <button type="button" 
                    @click.stop="openMenu = !openMenu" 
                    class="p-1 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
              <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Dropdown Aksi Folder -->
        <div x-show="openMenu" 
             @click.away="openMenu = false"
             x-transition
             class="absolute right-2 top-10 z-30 w-44 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 text-xs font-semibold text-gray-700">
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

          <a href="{{ route('sigap-dokumen.folder.download-zip', $item) }}" 
             class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            Download ZIP
          </a>
        </div>

        <!-- Judul & Keterangan Folder -->
        <a href="{{ route('sigap-dokumen.folder.show', $item) }}" class="block mt-3">
          <h3 class="text-sm font-semibold text-gray-800 group-hover:text-maroon truncate" title="{{ $item->name }}">
            {{ $item->name }}
          </h3>
          <p class="text-[11px] text-gray-500 mt-0.5">
            {{ $item->documents_count }} Berkas &bull; {{ $item->subfolders_count }} Folder
          </p>
        </a>
      </div>
    @empty
      <div class="col-span-full py-8 px-4 bg-white border border-dashed border-gray-300 rounded-2xl text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
          📁
        </div>
        <p class="text-sm font-semibold text-gray-700">Belum ada folder kerja</p>
        <p class="text-xs text-gray-500 mt-1">Gunakan tombol "Buat Folder Baru" di atas untuk mulai merapikan arsip.</p>
      </div>
    @endforelse
  </div>
</section>

<!-- Section Berkas Root (Tanpa Folder) -->
<section class="max-w-7xl mx-auto px-4 pb-12">
  <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Berkas Lepas (Tanpa Folder)</h2>

  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b bg-gray-50 text-gray-600">
            <th class="px-4 py-3">Nama Berkas</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Tahun</th>
            <th class="px-4 py-3">Lokasi Fisik</th>
            <th class="px-4 py-3">Akses</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y text-gray-700">
          @forelse ($docs as $doc)
            <tr class="hover:bg-gray-50/70 transition">
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs shrink-0">
                    PDF
                  </div>
                  <div>
                    <a href="{{ route('sigap-dokumen.show', $doc) }}" class="font-medium text-gray-900 hover:text-maroon">
                      {{ $doc->title }}
                    </a>
                    @if(!empty($doc->tags))
                      @php
                        $tagList = is_array($doc->tags) ? $doc->tags : explode(',', $doc->tags);
                      @endphp
                      <div class="flex flex-wrap gap-1 mt-1">
                        @foreach($tagList as $t)
                          <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.2 rounded font-medium">#{{ trim($t) }}</span>
                        @endforeach
                      </div>
                    @endif
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-xs">{{ $doc->category }}</td>
              <td class="px-4 py-3 text-xs">{{ $doc->year }}</td>
              <td class="px-4 py-3 text-xs">
                @if($doc->physical_rack || $doc->physical_row)
                  Rak: {{ $doc->physical_rack ?? '-' }}, Baris: {{ $doc->physical_row ?? '-' }}
                @else
                  -
                @endif
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded text-xs {{ $doc->sensitivity === 'public' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                  {{ $doc->sensitivity === 'public' ? 'Publik' : 'Privat' }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('sigap-dokumen.show', $doc) }}" class="px-2.5 py-1 text-xs rounded border border-gray-300 hover:bg-gray-100">Buka</a>
                  <a href="{{ route('sigap-dokumen.download', $doc) }}" class="px-2.5 py-1 text-xs rounded bg-maroon text-white hover:bg-maroon-800">Unduh</a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-xs">
                Tidak ada berkas lepas di luar folder.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($docs->hasPages())
      <div class="p-3 border-t bg-gray-50">
        {{ $docs->links() }}
      </div>
    @endif
  </div>
</section>
@endsection