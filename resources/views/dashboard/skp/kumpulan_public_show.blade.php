<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $kumpulan->judul_kumpulan }} — SIGAP BRIDA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style> body { font-family: Inter, system-ui, sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen p-4 sm:p-6">

  <div class="max-w-4xl mx-auto space-y-6">

    {{-- Info Rekap --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-2">
      <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
        {{ $kumpulan->kategori }}
      </span>
      <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">{{ $kumpulan->judul_kumpulan }}</h2>
      <p class="text-xs text-gray-500 mt-2 border-t pt-2">
        👤 Pegawai: <span class="font-bold text-gray-800">{{ $kumpulan->user->name ?? '-' }}</span><br>
        📅 Periode: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($kumpulan->bulan_tahun . '-01')->translatedFormat('F Y') }}</span>
      </p>
    </div>

    {{-- BLOK 1: SKP --}}
    @if(count($skpList) > 0)
    <div class="space-y-3">
      <h3 class="text-sm font-bold text-gray-900 bg-gray-200 px-3 py-1.5 rounded inline-block">📁 Laporan SKP ({{ count($skpList) }} Kegiatan)</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($skpList as $index => $skp)
          @php $foto = $skp->fotos->first(); @endphp
          <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm flex flex-col justify-between">
            <div class="h-48 w-full bg-gray-100 overflow-hidden border-b relative">
              @if($foto)
                <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-full object-cover">
              @else
                <div class="flex items-center justify-center h-full text-xs text-gray-400">Tidak ada gambar</div>
              @endif
              <div class="absolute top-2 left-2 bg-black/60 text-white text-[10px] font-bold px-2 py-1 rounded backdrop-blur">SKP #{{ $index + 1 }}</div>
            </div>
            <div class="p-4 space-y-1">
              <span class="text-[10px] font-bold text-gray-400">📅 {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('d F Y') }}</span>
              <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ $skp->judul_kegiatan }}</h4>
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- BLOK 2: PPD --}}
    @if(count($ppdList) > 0)
    <div class="space-y-4 mt-8">
      <h3 class="text-sm font-bold text-gray-900 bg-blue-100 text-blue-800 px-3 py-1.5 rounded inline-block border border-blue-200">
        ✈️ Perjalanan Dinas / PPD ({{ count($ppdList) }} Kegiatan)
      </h3>
      
      <div class="space-y-6">
        @foreach($ppdList as $ppd)
          <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <div class="border-b pb-3 mb-3">
              <h4 class="font-bold text-base text-gray-900">{{ $ppd->judul }}</h4>
              <p class="text-[11px] text-gray-500 mt-1">📍 {{ $ppd->tempat }} | 📅 {{ $ppd->hari_tanggal }}</p>
            </div>

            <div class="space-y-4">
              @foreach($ppd->lembar as $lembar)
                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                  <p class="text-xs font-semibold text-gray-700 mb-2">Lembar {{ $lembar->lembar_ke }} : <span class="font-normal">{{ $lembar->deskripsi }}</span></p>
                  
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($lembar->fotos as $fotoPpd)
                      <div class="aspect-square rounded border border-gray-200 overflow-hidden bg-white">
                        <img src="{{ asset('storage/' . $fotoPpd->foto_path) }}" class="w-full h-full object-cover">
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

  </div>
</body>
</html>