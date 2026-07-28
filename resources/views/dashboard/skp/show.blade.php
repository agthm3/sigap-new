@extends('layouts.app')

@section('content')
<div x-data="skpShow()" class="space-y-6">

  {{-- Navigation Back & Action --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('sigap-skp.index') }}" 
       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      Kembali ke Daftar SKP
    </a>

    <div class="flex items-center gap-2">
      <button type="button" 
              @click="copyLink()"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" stroke-width="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" stroke-width="2"/></svg>
        Salin Link Laporan
      </button>

      @hasanyrole('admin|verif_skp')
        <form action="{{ route('sigap-skp.destroy', $skp->slug) }}" method="POST" class="inline form-delete">
          @csrf
          @method('DELETE')
          <button type="button" data-judul="{{ $skp->judul_kegiatan }}"
                  class="btn-delete inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-600 hover:text-white transition-colors">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            Hapus Laporan
          </button>
        </form>
      @endhasanyrole
    </div>
  </div>

  {{-- Header Detail Kegiatan --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
    <div class="space-y-2 border-b border-gray-100 pb-5">
      <div class="flex flex-wrap items-center gap-2">
        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
          📅 {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('l, d F Y') }}
        </span>

        @if($skp->agenda_id)
          <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-maroon text-white shadow-sm">
            Terkait SIGAP AGENDA
          </span>
        @endif
      </div>

      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
        {{ $skp->judul_kegiatan }}
      </h1>
      
      <p class="text-xs text-gray-500">
        Dibuat/Diinput oleh: <span class="font-semibold text-gray-700">{{ $skp->creator->name ?? 'Sistem' }}</span> 
        • {{ $skp->created_at->diffForHumans() }}
      </p>
    </div>

    {{-- Section Pegawai Terlibat --}}
    <div>
      <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">
        Pegawai yang Terlibat ({{ $skp->pegawais->count() }})
      </h3>
      <div class="flex flex-wrap gap-2">
        @forelse($skp->pegawais as $pegawai)
          <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-gray-200 bg-gray-50 text-xs font-medium text-gray-800">
            <span class="w-6 h-6 rounded-full bg-maroon text-white font-bold flex items-center justify-center text-[10px]">
              {{ strtoupper(substr($pegawai->name, 0, 1)) }}
            </span>
            <span>{{ $pegawai->name }}</span>
          </div>
        @empty
          <p class="text-xs text-gray-400 italic">Tidak ada pegawai yang ditautkan.</p>
        @endforelse
      </div>
    </div>
  </div>

  {{-- Section Dokumentasi Foto --}}
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
        <span>📷 Dokumen Evidence & Fotos</span>
        <span class="text-xs px-2.5 py-0.5 rounded-full bg-maroon/10 text-maroon font-semibold">
          {{ $skp->fotos->count() }} Foto
        </span>
      </h2>
    </div>

    @if($skp->fotos->count() > 0)
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($skp->fotos as $index => $foto)
          @php
            $photoUrl = asset('storage/' . $foto->file_path);
          @endphp
          <div class="group relative rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-all">
            {{-- Image Thumbnail --}}
            <div class="aspect-square w-full bg-gray-100 overflow-hidden cursor-pointer" @click="openPreview('{{ $photoUrl }}')">
              <img src="{{ $photoUrl }}" 
                   alt="Dokumentasi {{ $skp->judul_kegiatan }}" 
                   class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
            </div>

            {{-- Hover Action Bar --}}
            <div class="p-3 bg-white flex items-center justify-between border-t border-gray-100 text-xs">
              <span class="text-gray-500 font-medium">Foto #{{ $index + 1 }}</span>
              <div class="flex items-center gap-1">
                <button type="button" @click="openPreview('{{ $photoUrl }}')" class="p-1.5 rounded-lg text-gray-600 hover:bg-gray-100" title="Perbesar">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M15 3h6v6M14 10l6-6M9 21H3v-6M10 14l-6 6"/></svg>
                </button>
                <a href="{{ $photoUrl }}" download class="p-1.5 rounded-lg text-maroon hover:bg-maroon/10" title="Unduh Foto">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center text-gray-500">
        Belum ada dokumentasi foto untuk kegiatan ini.
      </div>
    @endif
  </div>

  {{-- Modal Preview Foto (Lightbox Pop-up) --}}
  <div x-show="previewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
    <div x-show="previewModal" x-transition class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="previewModal = false"></div>
    <div class="flex min-h-full items-center justify-center p-4">
      <div x-show="previewModal" x-transition class="relative max-w-4xl w-full bg-transparent rounded-2xl overflow-hidden text-center">
        <button type="button" @click="previewModal = false" class="absolute top-2 right-2 text-white bg-black/50 hover:bg-black rounded-full p-2 z-10">
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img :src="activePhoto" class="max-h-[85vh] mx-auto rounded-xl shadow-2xl object-contain">
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function skpShow() {
  return {
    previewModal: false,
    activePhoto: '',

    openPreview(url) {
      this.activePhoto = url;
      this.previewModal = true;
    },

    copyLink() {
      navigator.clipboard.writeText(window.location.href).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Link Disalin!',
          text: 'URL laporan SKP berhasil disalin ke clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      });
    }
  }
}

// Konfirmasi Hapus via SweetAlert
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const judul = this.dataset.judul;

      Swal.fire({
        title: 'Hapus Laporan SKP?',
        html: `Seluruh bukti foto dan data laporan <b>${judul}</b> akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush