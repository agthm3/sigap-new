<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $kumpulan->judul_kumpulan }} — SIGAP BRIDA</title>

  <!-- Tailwind CSS & Font Inter -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style> body { font-family: Inter, system-ui, sans-serif; } </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen p-4 sm:p-6">

  <div class="max-w-5xl mx-auto space-y-6">

    {{-- Top Bar Logo --}}
    <div class="flex items-center justify-between border-b pb-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#7a2222] text-white font-extrabold text-sm">SB</span>
        <div>
          <h1 class="text-sm font-bold text-gray-900 leading-tight">SIGAP BRIDA Kota Makassar</h1>
          <p class="text-xs text-gray-500">Rekapitulasi Evidence SKP & PPD</p>
        </div>
      </div>
      <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
        ✅ Terverifikasi
      </span>
    </div>

    {{-- Info Rekap Utama --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-3">
      <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
        {{ $kumpulan->kategori }}
      </span>
      <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">
        {{ $kumpulan->judul_kumpulan }}
      </h2>
      <div class="text-xs text-gray-500 border-t pt-3 flex flex-wrap gap-4">
        <p>👤 Pegawai: <span class="font-bold text-gray-800">{{ $kumpulan->user->name ?? '-' }}</span></p>
        <p>📅 Periode: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($kumpulan->bulan_tahun . '-01')->translatedFormat('F Y') }}</span></p>
      </div>
    </div>

    {{-- BLOK 1: LAPORAN SKP (Foto & PDF) --}}
    @if(count($skpList) > 0)
    <div class="space-y-4">
      <h3 class="text-sm font-bold text-gray-900 bg-gray-200 px-3.5 py-1.5 rounded-xl inline-block">
        📁 Evidence Laporan SKP ({{ count($skpList) }} Kegiatan)
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($skpList as $index => $skp)
          @php
            $isPdf = $skp->tipe_evidence === 'pdf';
            $foto = $skp->fotos->first();
            $pdfUrl = $isPdf && $skp->file_pdf_path ? asset('storage/' . $skp->file_pdf_path) : null;
          @endphp

          <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            
            {{-- AREA MEDIA (PDF ATAU FOTO) --}}
            <div>
              <div class="h-48 w-full bg-gray-100 relative overflow-hidden flex items-center justify-center border-b">
                @if($isPdf)
                  {{-- Tampilan Khusus PDF --}}
                  <div class="flex flex-col items-center gap-1.5 text-red-600 p-4 text-center">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-red-100 text-red-800 px-2.5 py-0.5 rounded-md border border-red-200">
                      Dokumen PDF
                    </span>
                  </div>
                @elseif($foto)
                  {{-- Tampilan Foto --}}
                  <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-full object-cover">
                @else
                  <span class="text-xs text-gray-400 italic">Tidak ada dokumentasi</span>
                @endif

                <span class="absolute top-2 left-2 bg-black/60 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-md backdrop-blur">
                  #{{ $index + 1 }}
                </span>
              </div>

              {{-- Detail Judul & Deskripsi --}}
              <div class="p-4 space-y-1.5">
                <span class="text-[10px] font-bold text-gray-400">📅 {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('d F Y') }}</span>
                <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ $skp->judul_kegiatan }}</h4>
                @if($skp->deskripsi)
                  <p class="text-xs text-gray-500 line-clamp-3 mt-1 bg-gray-50 p-2 rounded-lg border border-gray-100">
                    {{ $skp->deskripsi }}
                  </p>
                @endif
              </div>
            </div>

            {{-- TOMBOL AKSES BERKAS PDF --}}
            <div class="p-3 border-t bg-gray-50">
              @if($isPdf && $pdfUrl)
                <a href="{{ $pdfUrl }}" target="_blank" 
                   class="w-full py-2 px-3 rounded-xl bg-red-700 hover:bg-red-800 text-white text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Buka / Download PDF
                </a>
              @else
                <span class="text-[11px] text-gray-400 italic block text-center">Foto Evidence</span>
              @endif
            </div>

          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- BLOK 2: PERJALANAN DINAS (PPD) --}}
    @if(count($ppdList) > 0)
    <div class="space-y-4 mt-8">
      <h3 class="text-sm font-bold text-gray-900 bg-blue-100 text-blue-800 px-3.5 py-1.5 rounded-xl inline-block border border-blue-200">
        ✈️ Evidence Perjalanan Dinas / PPD ({{ count($ppdList) }} Kegiatan)
      </h3>
      
      <div class="space-y-6">
        @foreach($ppdList as $ppd)
          <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4">
            <div class="border-b pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <div>
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase">
                  {{ $ppd->kategori }}
                </span>
                <h4 class="font-bold text-base text-gray-900 mt-1">{{ $ppd->judul }}</h4>
                <p class="text-xs text-gray-500 mt-0.5">📍 {{ $ppd->tempat }} | 📅 {{ $ppd->hari_tanggal }}</p>
              </div>
            </div>

            <div class="space-y-4">
              @foreach($ppd->lembar as $lembar)
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200">
                  <p class="text-xs font-bold text-gray-800 mb-2">
                    Lembar {{ $lembar->lembar_ke }} : <span class="font-normal text-gray-600">{{ $lembar->deskripsi ?? 'Tanpa deskripsi' }}</span>
                  </p>
                  
                  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                    @foreach($lembar->fotos as $fotoPpd)
                      <div class="aspect-square rounded-lg border border-gray-200 overflow-hidden bg-white shadow-sm">
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

    {{-- Footer Info --}}
    <div class="text-center pt-6 text-xs text-gray-400 border-t">
      SIGAP BRIDA Kota Makassar — Sistem Informasi Pertanggungjawaban & Evidence Kegiatan
    </div>

  </div>

</body>
</html>