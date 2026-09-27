@extends('layouts.app')

@section('content')
<!-- Page Header -->
<section class="max-w-7xl mx-auto px-4 py-4 sm:py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Dokumen Saya</h1>
      <p class="text-sm text-gray-600 mt-1">
        Kelola folder kerja dan arsip berkas pribadi Anda secara terenkripsi &amp; terkunci.
      </p>
    </div>

    <!-- Tombol Aksi Kanan Atas -->
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

<!-- Filter & Search Sentral Dokumen Saya -->
<section class="max-w-7xl mx-auto px-4 pb-6" 
         x-data="{
           submitSearch() {
             $refs.searchForm.submit();
           }
         }">
  <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5 shadow-2xs">
    <form x-ref="searchForm" class="grid lg:grid-cols-4 gap-3" method="GET" action="{{ route('sigap-dokumen.saya') }}">
      
      <!-- Input Pencarian -->
      <div class="lg:col-span-2">
        <div class="flex items-center justify-between">
          <label class="text-xs font-bold uppercase tracking-wider text-gray-600">Pencarian Arsip Saya</label>
          <span class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            Deep Search
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
                 placeholder="Ketik judul, instansi/mitra, tagar (#), atau rak..." 
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
          <?php foreach (['Surat Keputusan', 'Laporan', 'Formulir', 'Privasi', 'Dokumen Teknis', 'Surat Masuk/Keluar'] as $item): ?>
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
          *Menampilkan folder dan dokumen pribadi milik akun Anda.
        </p>
        <div class="flex items-center gap-2">
          @if(request('q') || request('category') || request('year'))
            <a href="{{ route('sigap-dokumen.saya') }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-600 transition">
              Reset Filter
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

<!-- Section Daftar Folder Saya -->
<section class="max-w-7xl mx-auto px-4 pb-6">
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center gap-2">
      <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500">Folder Saya</h2>
      <span class="text-[11px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">{{ $folders->count() }} Folder</span>
    </div>
  </div>
  
  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 sm:gap-4">
    @if($folders->isNotEmpty())
      <?php foreach ($folders as$item): ?>
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
              
              <button type="button" 
                      onclick="openMoveFolderModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{$item->visibility }}')"
                      class="w-full flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition text-left">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                </svg>
                Pindahkan Folder
              </button>
            @endif

            <a href="{{ route('sigap-dokumen.folder.download-zip', $item) }}" 
               class="flex items-center gap-2 px-3 py-2 hover:bg-gray-50 hover:text-maroon transition">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
              </svg>
              Download ZIP
            </a>

            @if($item->user_id === auth()->id() || auth()->user()->hasRole('admin'))
              <div class="border-t border-gray-100 my-1"></div>
              <form action="{{ route('sigap-dokumen.folder.destroy', $item) }}" method="POST" onsubmit="return confirm('PERINGATAN: Menghapus folder ini akan menghapus permanen seluruh subfolder dan berkas dokumen di dalamnya! Lanjutkan?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-xs text-red-600 hover:bg-red-50 hover:text-red-700 transition rounded-md text-left font-semibold">
                  <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  <span>Hapus Folder</span>
                </button>
              </form>
            @endif
          </div>

          <!-- Judul & Keterangan Folder -->
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
      <div class="col-span-full py-8 px-4 bg-white border border-dashed border-gray-300 rounded-2xl text-center">
        <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-2">
          📁
        </div>
        <p class="text-sm font-semibold text-gray-700">Belum ada folder kerja</p>
        <p class="text-xs text-gray-500 mt-1">Gunakan tombol "Buat Folder Baru" di atas untuk mulai merapikan arsip.</p>
      </div>
    @endif
  </div>
</section>

<!-- Section Berkas -->
<section class="max-w-7xl mx-auto px-4 pb-12">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2xs">
    <div class="px-4 py-3 bg-gray-50 border-b flex items-center justify-between text-xs font-bold text-gray-500 uppercase tracking-wider">
      <span>{{ !empty($hasFilter) ? 'Hasil Pencarian Berkas (Termasuk Dalam Folder)' : 'Berkas Lepas (Tanpa Folder)' }}</span>
      <span>Total: {{ $docs->total() }}</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b bg-gray-50 text-gray-600">
            <th class="px-4 py-3">Nama Berkas</th>
            <!-- Lebar Kolom Alias/No Dikunci agar tidak mendesak nama dokumen -->
            <th class="px-4 py-3 w-32 sm:w-40">Alias / No</th>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Tahun</th>
            <th class="px-4 py-3">Lokasi Fisik</th>
            <th class="px-4 py-3">Akses</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y text-gray-700">
          @if($docs->isNotEmpty())
            <?php foreach ($docs as$doc): ?>
              <tr class="hover:bg-gray-50/70 transition">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs shrink-0">
                      PDF
                    </div>
                    <div class="min-w-0">
                      <div class="flex items-center gap-1.5 flex-wrap">
                        <a href="{{ route('sigap-dokumen.show', $doc) }}" class="font-medium text-gray-900 hover:text-maroon">
                          {{ $doc->title }}
                        </a>
                        <!-- Chip Folder jika dokumen tersimpan di dalam folder/subfolder -->
                        @if($doc->folder)
                          <a href="{{ route('sigap-dokumen.folder.show', $doc->folder) }}" 
                             class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition"
                             title="Tersimpan di dalam folder {{ $doc->folder->name }}">
                            <span>{{ $doc->folder->icon ?? '📁' }}</span>
                            <span class="font-medium truncate max-w-[120px]">{{ $doc->folder->name }}</span>
                          </a>
                        @endif
                      </div>

                      <p class="text-[11px] text-gray-500 mt-0.5 line-clamp-1" title="{{ $doc->description }}">{{ Str::limit($doc->description, 50) }}</p>
                      @if(!empty($doc->tags))
                        <?php $tagList = is_array($doc->tags) ? $doc->tags : explode(',',$doc->tags); ?>
                        <div class="flex flex-wrap gap-1 mt-1.5">
                          <?php foreach ($tagList as$t): ?>
                            <span class="text-[9px] font-bold bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded">#{{ trim($t) }}</span>
                          <?php endforeach; ?>
                        </div>
                      @endif
                    </div>
                  </div>
                </td>

                <!-- Alias & Nomor Naskah Dinas Terproteksi Truncate -->
                <td class="px-4 py-3 text-xs text-gray-600 w-32 sm:w-40 align-top">
                  <span class="block font-mono truncate max-w-[120px] sm:max-w-[150px]" title="{{ $doc->alias }}">{{ $doc->alias ?: '-' }}</span>
                  <span class="block mt-1 text-[10px] text-gray-400 truncate max-w-[120px] sm:max-w-[150px]" title="{{ $doc->number }}">{{ $doc->number ?: '' }}</span>
                </td>

                <td class="px-4 py-3 text-xs">{{ $doc->category }}</td>
                <td class="px-4 py-3 text-xs">{{ $doc->year }}</td>
                <td class="px-4 py-3 text-xs">
                  @if(filled($doc->physical_rack) || filled($doc->physical_row))
                    R: {{ $doc->physical_rack ?? '-' }}<br>B: {{ $doc->physical_row ?? '-' }}
                  @else
                    <span class="text-gray-400">-</span>
                  @endif
                </td>
                <td class="px-4 py-3 text-xs">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide 
                    {{ $doc->sensitivity === 'public' ? 'bg-emerald-50 text-emerald-700' : ($doc->sensitivity === 'internal' ? 'bg-blue-50 text-blue-700' : 'bg-red-50 text-red-700') }}">
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
            <?php endforeach; ?>
          @else
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-gray-400 text-xs">
                Tidak ada berkas yang ditemukan.
              </td>
            </tr>
          @endif
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

@push('scripts')
<script>
const globalFolders = @json($allFolders);

function openMoveFolderModal(folderId, folderName, currentVis) {
  let options = `<option value="">-- Letakkan di Luar Folder (Root Utama) --</option>`;
  
  globalFolders.forEach(f => {
    if (f.id !== folderId) {
      options += `<option value="${f.id}" data-vis="${f.visibility}">
                    ${f.name} (Akses: ${f.visibility.toUpperCase()})
                  </option>`;
    }
  });

  Swal.fire({
    title: 'Pindahkan Folder',
    html: `
      <div class="text-left space-y-3 mt-3">
        <p class="text-sm text-gray-600 mb-2">Pilih lokasi tujuan untuk <strong>${folderName}</strong>:</p>
        <select id="destFolderSelect" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm focus:border-maroon focus:ring-maroon" onchange="checkVisibilityMismatch(this, '${currentVis}')">
          ${options}
        </select>
        
        <div id="visWarningBox" class="hidden mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs">
          <p class="font-bold text-amber-800 mb-1">⚠️ Perbedaan Privasi Terdeteksi</p>
          <p class="text-amber-700 mb-3 leading-relaxed">Folder tujuan memiliki level hak akses yang berbeda dengan folder ini. Sesuaikan hak akses agar selaras?</p>
          
          <div class="space-y-2">
            <label class="flex items-start gap-2 cursor-pointer p-2 rounded hover:bg-amber-100/50 transition">
              <input type="radio" name="vis_action" value="adapt" checked class="mt-0.5 text-maroon focus:ring-maroon">
              <div>
                <span class="font-bold text-gray-800 block">Selaraskan (Sangat Disarankan)</span>
                <span class="text-gray-500 text-[10px]">Folder ini (dan isinya) akan mengikuti hak akses folder induk yang baru.</span>
              </div>
            </label>
            <label class="flex items-start gap-2 cursor-pointer p-2 rounded hover:bg-amber-100/50 transition">
              <input type="radio" name="vis_action" value="keep" class="mt-0.5 text-maroon focus:ring-maroon">
              <div>
                <span class="font-bold text-gray-800 block">Biarkan Berbeda</span>
                <span class="text-gray-500 text-[10px]">Folder ini akan mempertahankan hak akses lamanya.</span>
              </div>
            </label>
          </div>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Simpan Pemindahan',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#7a2222',
    preConfirm: () => {
      return {
        dest_id: document.getElementById('destFolderSelect').value,
        vis_action: document.querySelector('input[name="vis_action"]:checked')?.value || 'keep'
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const form = document.createElement('form');
      form.method = 'POST';
      let actionUrl = "{{ route('sigap-dokumen.folder.move', ':id') }}";
      form.action = actionUrl.replace(':id', folderId);
      
      const csrf = document.createElement('input');
      csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
      form.appendChild(csrf);

      const destInput = document.createElement('input');
      destInput.type = 'hidden'; destInput.name = 'dest_id'; destInput.value = result.value.dest_id;
      form.appendChild(destInput);

      const actionInput = document.createElement('input');
      actionInput.type = 'hidden'; actionInput.name = 'vis_action'; actionInput.value = result.value.vis_action;
      form.appendChild(actionInput);

      document.body.appendChild(form);
      form.submit();
    }
  });
}

function checkVisibilityMismatch(selectElement, currentVisibility) {
  const selectedOpt = selectElement.options[selectElement.selectedIndex];
  const destVis = selectedOpt.getAttribute('data-vis');
  const warningBox = document.getElementById('visWarningBox');
  
  if (destVis && destVis !== currentVisibility) {
    warningBox.classList.remove('hidden');
  } else {
    warningBox.classList.add('hidden');
  }
}
</script>
@endpush
@endsection