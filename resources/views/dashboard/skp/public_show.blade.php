<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
{{-- META TAGS UNTUK WHATSAPP THUMBNAIL --}}
@php
  $firstFoto = $skp->fotos->first();
  
  if ($firstFoto) {
      // Mengambil URL lengkap
      $imageUrl = asset('storage/' . $firstFoto->file_path);
  } else {
      $imageUrl = asset('images/logo.png');
  }

  // Paksa ubah http:// menjadi https://
  $secureImageUrl = str_replace('http://', 'https://', $imageUrl);
  $securePageUrl  = str_replace('http://', 'https://', request()->fullUrl());
@endphp

<title>{{ $skp->judul_kegiatan }} — SIGAP BRIDA</title>

<!-- Meta Tag Utama WhatsApp/Facebook -->
<meta property="og:site_name" content="SIGAP BRIDA Kota Makassar" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $skp->judul_kegiatan }}" />
<meta property="og:description" content="Kegiatan tanggal {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('d F Y') }} — Laporan Evidence SIGAP SKP." />

<!-- Meta Tag Gambar Kompatibel WhatsApp -->
<meta property="og:image" content="{{ $secureImageUrl }}" />
<meta property="og:image:secure_url" content="{{ $secureImageUrl }}" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:url" content="{{ $securePageUrl }}" />

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style> body { font-family: Inter, system-ui, sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen p-4 sm:p-6">

  <div class="max-w-3xl mx-auto space-y-6">
    
    {{-- Top Header --}}
    <div class="flex items-center justify-between border-b pb-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#7a2222] text-white font-extrabold text-sm">SB</span>
        <div>
          <h1 class="text-sm font-bold text-gray-900 leading-tight">SIGAP BRIDA</h1>
          <p class="text-xs text-gray-500">Bukti Laporan Evidence SKP</p>
        </div>
      </div>
      @auth
        <a href="{{ route('sigap-skp.index') }}" class="px-3 py-1.5 rounded-xl bg-gray-100 text-xs font-semibold text-gray-700 hover:bg-gray-200">
          Ke Dashboard
        </a>
      @else
        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-[#7a2222] text-white text-xs font-semibold hover:bg-[#661b1b]">
          Login Sistem
        </a>
      @endauth
    </div>

    {{-- Detail Kegiatan --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-4">
      <div class="space-y-1 border-b pb-3">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 mb-1">
          📅 {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('l, d F Y') }}
        </span>
        <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug">
          {{ $skp->judul_kegiatan }}
        </h2>
        <p class="text-xs text-gray-500">
          Dilaporkan oleh: <span class="font-semibold text-gray-700">{{ $skp->creator->name ?? 'Pegawai' }}</span>
        </p>
      </div>

      {{-- Pegawai Terlibat --}}
      <div>
        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Pegawai Terlibat:</p>
        <div class="flex flex-wrap gap-1.5">
          @foreach($skp->pegawais as $pegawai)
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
              👤 {{ $pegawai->name }}
            </span>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Galeri Foto Evidence --}}
    <div class="space-y-3">
      <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
        <span>📷 Foto Evidence Laporan ({{ $skp->fotos->count() }})</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($skp->fotos as $index => $foto)
          @php $photoUrl = asset('storage/' . $foto->file_path); @endphp
          <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
            <img src="{{ $photoUrl }}" alt="Foto Evidence" class="w-full h-64 object-cover">
            <div class="p-3 text-right">
              <a href="{{ $photoUrl }}" target="_blank" download class="text-xs font-semibold text-[#7a2222] hover:underline">
                ⬇ Unduh Foto Utuh
              </a>
            </div>
          </div>
        @endforeach
      </div>
    </div>

  </div>

</body>
</html>