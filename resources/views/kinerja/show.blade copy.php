{{-- resources/views/kinerja/show.blade.php --}}
@extends('layouts.page') {{-- untuk publik tanpa sidebar --}}
@section('content')
@php
  use Carbon\Carbon;

  $tgl   = Carbon::parse($item['date'] ?? now())->locale('id')->translatedFormat('d F Y');
  $media = $item['media'] ?? []; // [{url,mime,is_image,is_primary}]
  // Tentukan primary viewer:
  $primary = null;

  if (!empty($media)) {
    // Prioritas: is_primary image → image pertama → file pertama apapun
    $primary = collect($media)->firstWhere('is_primary', true)
            ?? collect($media)->firstWhere('is_image', true)
            ?? $media[0];
  } else {
    // fallback single file (legacy)
    $url = $item['file_url'] ?? null;
    $mime = $item['file_mime'] ?? null;
    $isImage = $url && preg_match('/\.(png|jpe?g|gif|webp)$/i', $url);
    if ($url) {
      $primary = [
        'url'       => $url,
        'mime'      => $mime ?: ($isImage ? 'image/*' : 'application/octet-stream'),
        'is_image'  => (bool) $isImage,
        'is_primary'=> true,
      ];
      $media = [$primary]; // supaya galeri tetap jalan
    }
  }

  $isImagePrimary = $primary && (!empty($primary['is_image']));
@endphp

{{-- Meta untuk preview --}}
@section('head')
  <meta property="og:title" content="{{ $item['title'] }} — SIGAP Kinerja">
  <meta property="og:description" content="{{ $item['description'] ?? 'Bukti kegiatan BRIDA' }}">
  <meta property="og:image" content="{{ $item['thumb_url'] ?? ($primary['url'] ?? '') }}">
  <meta property="og:type" content="article">
  <meta name="twitter:card" content="summary_large_image">
  {{-- SweetAlert (jaga-jaga kalau layouts.page belum load) --}}
  <script>if(typeof Swal==='undefined'){document.write('<scr'+'ipt src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/scr'+'ipt>')}</script>
@endsection

<section class="max-w-5xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <!-- Header -->
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
        @if(!empty($primary['url']))
          <a href="{{ $primary['url'] }}" target="_blank"
            class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
            Buka File
          </a>
        @endif
      </div>
    </div>

    <!-- Viewer utama -->
    <div class="p-0">
      @if($primary && $isImagePrimary)
        <img
          src="{{ $primary['url'] }}"
          alt="{{ $item['title'] }}"
          class="w-full max-h-[80vh] object-contain bg-black"
        >
      @elseif($primary && str_starts_with(strtolower($primary['mime'] ?? ''), 'application/pdf'))
        <div class="aspect-[4/3] sm:aspect-[16/9] bg-gray-100">
          <iframe src="{{ $primary['url'] }}" class="w-full h-full" allowfullscreen></iframe>
        </div>
      @elseif($primary && !empty($primary['url']))
        <div class="p-6">
          <a href="{{ $primary['url'] }}" target="_blank"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
              <path stroke-width="2" d="M14 2v6h6"/>
            </svg>
            Buka Berkas
          </a>
        </div>
      @else
        <div class="p-6 text-center text-gray-500">Tidak ada berkas untuk ditampilkan.</div>
      @endif
    </div>

    <!-- Deskripsi & info -->
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

      {{-- Galeri semua media --}}
      @if(!empty($media) && count($media) > 1)
        <div class="mt-6">
          <h3 class="text-sm font-semibold text-gray-700 mb-2">Media Lainnya</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach($media as $m)
              @php $isImg = !empty($m['is_image']); @endphp
              @if($isImg)
                <a href="{{ $m['url'] }}" target="_blank"
                   class="block border rounded-lg overflow-hidden group">
                  <img src="{{ $m['url'] }}" alt="media"
                       class="w-full h-36 object-cover group-hover:opacity-90">
                </a>
              @else
                <a href="{{ $m['url'] }}" target="_blank"
                   class="flex items-center gap-2 px-3 py-2 border rounded-lg hover:bg-gray-50">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                    <path stroke-width="2" d="M14 2v6h6"/>
                  </svg>
                  <span class="text-xs truncate">Berkas</span>
                </a>
              @endif
            @endforeach
          </div>
        </div>
      @endif

      <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('sigap-kinerja.index') }}"
           class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          ← Kembali ke Kinerja
        </a>
        <button id="btnCopy2" class="px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm">
          Salin Link
        </button>
        @if(!empty($primary['url']))
          <a href="{{ $primary['url'] }}" target="_blank"
            class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
            Buka File
          </a>
        @endif
      </div>
    </div>

    <!-- Footer -->
    <div class="px-4 sm:px-6 py-3 border-t text-xs text-gray-500 bg-gray-50">
      Dokumen ini ditayangkan oleh <b>BRIDA Kota Makassar</b>. Penggunaan tautan ini dapat tercatat untuk audit (fitur nanti).
    </div>
  </div>
</section>

@push('scripts')
<script>
  function copyCurrentUrl(){
    const link = window.location.href;
    navigator.clipboard.writeText(link).then(() => {
      Swal.fire({ icon:'success', title:'Tautan disalin', text:'Tempel di sistem pelaporan Pemkot.', timer: 1800, showConfirmButton:false });
    }).catch(() => {
      Swal.fire({ icon:'error', title:'Gagal menyalin', text:'Silakan salin manual dari bilah alamat.' });
    });
  }
  document.getElementById('btnCopy')?.addEventListener('click', copyCurrentUrl);
  document.getElementById('btnCopy2')?.addEventListener('click', copyCurrentUrl);
</script>
@endpush
@endsection
