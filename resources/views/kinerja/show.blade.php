@extends('layouts.page') {{-- atau layouts.app kalau mau pakai sidebar. Untuk publik biasanya tanpa sidebar --}}
@section('content')
@php
  use Carbon\Carbon;
  $tgl = Carbon::parse($item['date'] ?? now())->locale('id')->translatedFormat('d F Y');
  $isImage = preg_match('/\.(png|jpg|jpeg|gif|webp)$/i', $item['file_url'] ?? '') === 1;
@endphp

<!—- Meta untuk preview WA/Telegram —->
@section('head')
  <meta property="og:title" content="{{ $item['title'] }} — SIGAP Kinerja">
  <meta property="og:description" content="{{ $item['description'] ?? 'Bukti kegiatan BRIDA' }}">
  <meta property="og:image" content="{{ $item['thumb_url'] ?? $item['file_url'] }}">
  <meta property="og:type" content="article">
  <meta name="twitter:card" content="summary_large_image">
@endsection

<section class="max-w-5xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <!-- Header sederhana -->
    <div class="px-4 sm:px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
      <div class="min-w-0">
        <h1 class="text-lg sm:text-xl font-extrabold text-gray-900 leading-tight line-clamp-2">
          {{ $item['title'] }}
        </h1>
        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-600">
          <span class="px-2 py-0.5 rounded bg-maroon text-white">{{ $item['category'] }}</span>
          @if(!empty($item['rhk']))
            <span class="px-2 py-0.5 rounded bg-gray-900/80 text-white">{{ $item['rhk'] }}</span>
          @endif
          <span>• {{ $tgl }}</span>
        </div>
      </div>

      <div class="flex-none flex gap-2">
        <button id="btnCopy" class="px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm">
          Salin Link
        </button>
        <a href="{{ $item['file_url'] }}" target="_blank"
           class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          Buka File
        </a>
      </div>
    </div>

    <!-- Media -->
    <div class="p-0">
      @if($isImage)
        <img
          src="{{ $item['file_url'] }}"
          alt="{{ $item['title'] }}"
          class="w-full max-h-[80vh] object-contain bg-black"
        >
      @else
        <!-- PDF / lainnya -->
        <div class="aspect-[4/3] sm:aspect-[16/9] bg-gray-100">
          <iframe src="{{ $item['file_url'] }}" class="w-full h-full" allowfullscreen></iframe>
        </div>
      @endif
    </div>

    <!-- Detail singkat -->
    <div class="px-4 sm:px-6 py-4">
      @if(!empty($item['description']))
        <p class="text-sm sm:text-base text-gray-800">{{ $item['description'] }}</p>
      @else
        <p class="text-sm text-gray-500">Tidak ada deskripsi tambahan.</p>
      @endif

      <div class="mt-4 grid sm:grid-cols-3 gap-3 text-sm">
        <div class="p-3 rounded-lg bg-gray-50 border">
          <div class="text-gray-500">Kategori</div>
          <div class="font-medium text-gray-900">{{ $item['category'] }}</div>
        </div>
        <div class="p-3 rounded-lg bg-gray-50 border">
          <div class="text-gray-500">RHK</div>
          <div class="font-medium text-gray-900">{{ $item['rhk'] ?? '-' }}</div>
        </div>
        <div class="p-3 rounded-lg bg-gray-50 border">
          <div class="text-gray-500">Tanggal</div>
          <div class="font-medium text-gray-900">{{ $tgl }}</div>
        </div>
      </div>

      <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('sigap-kinerja.index') }}"
           class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          ← Kembali ke Kinerja
        </a>
        <button id="btnCopy2" class="px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm">
          Salin Link
        </button>
        <a href="{{ $item['file_url'] }}" target="_blank"
           class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          Buka File
        </a>
      </div>
    </div>

    <!-- Footer kecil -->
    <div class="px-4 sm:px-6 py-3 border-t text-xs text-gray-500 bg-gray-50">
      Dokumen ini ditayangkan oleh <b>BRIDA Kota Makassar</b>. Penggunaan tautan ini tercatat untuk audit (fitur nanti).
    </div>
  </div>
</section>

{{-- Script salin tautan + notifikasi SweetAlert --}}
@push('scripts')
<script>
  function copyCurrentUrl(){
    const link = window.location.href;
    navigator.clipboard.writeText(link).then(() => {
      Swal.fire({ icon:'success', title:'Tautan disalin', text:'Tempel di sistem pelaporan Pemkot.', timer: 1800, showConfirmButton:false });
      // TODO: panggil endpoint log salin untuk audit
    }).catch(() => {
      Swal.fire({ icon:'error', title:'Gagal menyalin', text:'Silakan salin manual dari bilah alamat.' });
    });
  }
  document.getElementById('btnCopy')?.addEventListener('click', copyCurrentUrl);
  document.getElementById('btnCopy2')?.addEventListener('click', copyCurrentUrl);
</script>
@endpush
@endsection
