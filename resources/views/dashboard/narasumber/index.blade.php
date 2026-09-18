@extends('layouts.app')
@section('content')
<div x-data="{
    showModal: false,
    previewUrl: '',
    previewTitle: '',
    isPdf: false,
    openPreview(url, title, isPdfFile) {
        this.previewUrl = url;
        this.previewTitle = title;
        this.isPdf = isPdfFile;
        this.showModal = true;
    },
    closeModal() {
        this.showModal = false;
        this.previewUrl = '';
        this.previewTitle = '';
    }
}" @keydown.escape.window="closeModal()">

  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-6">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        Data <span class="text-maroon">Kesediaan Narasumber</span>
      </h1>
      <p class="text-xs text-gray-500 mt-1">Daftar narasumber yang telah mengisi formulir kesediaan kegiatan.</p>
    </div>
    <a href="{{ route('sigap-narasumber.pilih-kegiatan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition-colors shadow-sm">
      + Minta Kesediaan (QR)
    </a>
  </section>

  @if(session('success'))
    <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
      <span>{{ session('success') }}</span>
      <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600 border-b border-gray-100">
          <tr>
            <th class="px-4 py-3.5">Nama Narasumber</th>
            <th class="px-4 py-3.5">NIK & NPWP</th>
            <th class="px-4 py-3.5">Kegiatan</th>
            <th class="px-4 py-3.5">Waktu TTD</th>
            <th class="px-4 py-3.5">Berkas KTP</th>
            <th class="px-4 py-3.5 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($narasumbers as $row)
          @php
            $isPdf = $row->ktp_path && str_ends_with(strtolower($row->ktp_path), '.pdf');
          @endphp
          <tr class="hover:bg-gray-50/75 transition-colors">
            <td class="px-4 py-3">
              <div class="font-semibold text-gray-900">{{ $row->nama_lengkap }}</div>
              <div class="text-xs text-gray-500 truncate max-w-xs">{{ $row->materi ?? 'Materi: -' }}</div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-xs">
              <div><span class="font-medium text-gray-500">NIK:</span> {{ $row->nik ?? '-' }}</div>
              <div><span class="font-medium text-gray-500">NPWP:</span> {{ $row->npwp ?? '-' }}</div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-block max-w-xs text-gray-700 truncate" title="{{ $row->kegiatan->nama_kegiatan ?? '-' }}">
                {{ $row->kegiatan->nama_kegiatan ?? '-' }}
              </span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">
              {{ $row->signed_at ? \Carbon\Carbon::parse($row->signed_at)->format('d/m/Y H:i') : '-' }}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              @if($row->ktp_path)
                <button type="button" 
                        @click="openPreview('{{ route('sigap-narasumber.ktp', $row->uuid) }}', '{{ addslashes($row->nama_lengkap) }}', {{ $isPdf ? 'true' : 'false' }})"
                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-blue-200 bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100 transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  Lihat KTP
                </button>
              @else
                <span class="text-xs text-gray-400 italic">Tidak ada</span>
              @endif
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center">
              <div class="inline-flex items-center gap-1.5">
                <a href="{{ route('sigap-narasumber.export-pdf', $row->uuid) }}" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-500 text-emerald-700 text-xs font-medium hover:bg-emerald-50 transition-colors">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  PDF
                </a>
                <form action="{{ route('sigap-narasumber.destroy', $row->uuid) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data kesediaan ini?');" class="inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg border border-red-300 text-red-600 text-xs font-medium hover:bg-red-50 transition-colors">
                    Hapus
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
              Belum ada data narasumber yang bersedia.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-4">{{ $narasumbers->links() }}</div>

  <!-- === MODAL POPUP PREVIEW KTP (AMAN & PRIVAT) === -->
  <div x-show="showModal" 
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm"
       style="display: none;">
       
    <div @click.away="closeModal()" 
         x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col max-h-[90vh]">
      
      <!-- Modal Header -->
      <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div>
          <h3 class="font-bold text-gray-800 text-sm">Verifikasi Berkas KTP</h3>
          <p class="text-xs text-gray-500" x-text="'Narasumber: ' + previewTitle"></p>
        </div>
        <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-700 text-lg p-1 rounded-lg hover:bg-gray-100 transition-colors">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Modal Body (Preview Area) -->
      <div class="p-4 overflow-auto flex-1 flex items-center justify-center bg-gray-100 min-h-[320px]">
        <!-- Jika PDF -->
        <template x-if="isPdf && previewUrl">
          <iframe :src="previewUrl + '#toolbar=0&navpanes=0'" class="w-full h-[65vh] rounded-xl border border-gray-200 bg-white" frameborder="0"></iframe>
        </template>
        
        <!-- Jika Gambar (JPG/PNG) -->
        <template x-if="!isPdf && previewUrl">
          <img :src="previewUrl" alt="KTP Preview" class="max-h-[65vh] max-w-full rounded-xl object-contain shadow-sm border border-gray-200 bg-white select-none">
        </template>
      </div>

      <!-- Modal Footer -->
      <div class="px-5 py-3 bg-white border-t border-gray-100 flex items-center justify-between">
        <span class="text-xs text-gray-400 italic">Mode pratinjau baca-saja (privat).</span>
        <button type="button" @click="closeModal()" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200 transition-colors">
          Tutup
        </button>
      </div>
    </div>
  </div>

</div>
@endsection