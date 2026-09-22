@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-4 sm:py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
        <a href="{{ route('sigap-dokumen.saya') }}" class="hover:underline">Dokumen Saya</a>
        @if($folder->parent)
          <span>/</span>
          <a href="{{ route('sigap-dokumen.folder.show', $folder->parent) }}" class="hover:underline">{{ $folder->parent->name }}</a>
        @endif
        <span>/</span>
        <span class="font-semibold text-gray-700">{{ $folder->name }}</span>
      </div>
      <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-2">
        <span>{{ $folder->icon ?? '📁' }}</span>
        <span>{{ $folder->name }}</span>
      </h1>
    </div>

    <!-- 2 Tombol di Ujung Kanan Atas Sesuai Permintaan -->
    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-dokumen.folder.create', ['parent_id' => $folder->id]) }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 transition text-sm font-semibold shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Folder Baru
      </a>
      <a href="{{ route('sigap-dokumen.upload', ['folder_id' => $folder->id]) }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition text-sm font-semibold shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
        </svg>
        Upload Dokumen
      </a>
    </div>
  </div>
</section>

<!-- Section Subfolder -->
@if($subfolders->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 pb-6">
  <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3">Subfolder</h2>
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
    @foreach($subfolders as $sub)
      <a href="{{ route('sigap-dokumen.folder.show', $sub) }}" 
         class="p-3.5 bg-white border border-gray-200 rounded-xl hover:border-maroon/50 hover:shadow-sm transition flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-white shrink-0" style="background-color: {{ $sub->color ?? '#7a2222' }};">
          <span class="text-sm">{{ $sub->icon ?? '📁' }}</span>
        </div>
        <div class="truncate">
          <p class="text-xs font-semibold text-gray-800 truncate">{{ $sub->name }}</p>
          <p class="text-[10px] text-gray-400">{{ $sub->documents()->count() }} berkas</p>
        </div>
      </a>
    @endforeach
  </div>
</section>
@endif

<!-- Section Berkas dalam Folder -->
<section class="max-w-7xl mx-auto px-4 pb-12">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
    <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
      <span>Daftar Berkas</span>
      <span>Total: {{ $documents->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b text-gray-600 bg-gray-50/50">
            <th class="px-4 py-3">Nama Berkas</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Tahun</th>
            <th class="px-4 py-3">Lokasi Fisik</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y text-gray-700">
          @forelse ($documents as $doc)
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
                      @foreach($tagList as $t)
                        <span class="text-[10px] bg-gray-100 text-gray-600 px-1.5 py-0.2 rounded font-medium">#{{ trim($t) }}</span>
                      @endforeach
                    @endif
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-xs">{{ $doc->category }}</td>
              <td class="px-4 py-3 text-xs">{{ $doc->year }}</td>
              <td class="px-4 py-3 text-xs">
                @if($doc->physical_rack || $doc->physical_row)
                  Rak {{ $doc->physical_rack ?? '-' }}, No. {{ $doc->physical_row ?? '-' }}
                @else
                  -
                @endif
              </td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded text-xs font-semibold
                  {{ $doc->sensitivity === 'public' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 
                    ($doc->sensitivity === 'internal' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-red-50 text-red-700 border border-red-200') }}">
                  {{ $doc->sensitivity === 'public' ? 'Publik' : ($doc->sensitivity === 'internal' ? 'Internal' : 'Privat') }}
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
              <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-xs">
                Folder ini masih kosong. Silakan unggah dokumen baru.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($documents->hasPages())
      <div class="p-3 border-t bg-gray-50">
        {{ $documents->links() }}
      </div>
    @endif
  </div>
</section>
@endsection