<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Pejabat - {{ $kegiatan->nama_kegiatan }}</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    @page { size: 21.5cm 33cm; margin: 0; }

    html, body {
      width: 100%; height: 100%;
      margin: 0; padding: 0;
      background: #f3f4f6;
      font-family: Arial, Helvetica, sans-serif;
    }

    .sheet {
      width: calc(100% - 1.2rem);
      height: calc(100vh - 1.2rem);
      margin: 0.6rem;
      box-sizing: border-box;
      background: #ffffff;
      border: 8px solid #7a2222;
      overflow: hidden;
    }

    .inner-border {
      width: 100%; height: 100%;
      box-sizing: border-box;
      border: 2px solid #c9a3a3;
      padding: 1.2rem 1.1rem;
      overflow: hidden;
    }

    svg { display: block; }

    @media print {
      .no-print { display: none !important; }
      body { background: #fff; }
      .sheet { width: 100%; height: 100vh; margin: 0; }
      .inner-border { height: calc(100vh - 16px); }
    }
  </style>
</head>
<body>

  <div class="no-print max-w-4xl mx-auto px-4 pt-4 flex justify-end">
    <button onclick="window.print()"
            class="px-4 py-2 rounded-xl text-white font-semibold"
            style="background-color:#7a2222;">
      Print
    </button>
  </div>

  <div class="sheet">
    <div class="inner-border flex flex-col">

      {{-- BADGE --}}
      <div class="text-center mb-3">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-widest uppercase"
              style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;">
          Khusus Pejabat Penandatangan
        </span>
      </div>

      {{-- JUDUL KEGIATAN --}}
      <div class="text-center">
        <h1 class="text-2xl font-extrabold text-gray-900 leading-tight">
          {{ $kegiatan->nama_kegiatan }}
        </h1>
        <div class="mt-2 text-sm text-gray-700 space-y-0.5">
          <p><span class="font-semibold">Hari/Tanggal:</span> {{ $kegiatan->hari_tanggal }}</p>
          <p><span class="font-semibold">Tempat:</span> {{ $kegiatan->tempat }}</p>
          <p><span class="font-semibold">Waktu:</span> {{ $kegiatan->waktu }}</p>
        </div>
      </div>

      {{-- DATA PEJABAT --}}
      <div class="mt-4 mx-auto w-full max-w-sm bg-amber-50 border border-amber-200 rounded-2xl p-4">
        <p class="text-xs font-bold text-amber-800 uppercase tracking-widest mb-2">Data Penandatangan</p>
        <div class="space-y-1 text-sm text-gray-800">
          <p class="font-semibold text-base">{{ $penandatangan->nama_lengkap }}</p>
          @if($penandatangan->jabatan)   <p>{{ $penandatangan->jabatan }}</p> @endif
          @if($penandatangan->pangkat)   <p class="text-gray-600">{{ $penandatangan->pangkat }}{{ $penandatangan->golongan ? ' / Gol. ' . $penandatangan->golongan : '' }}</p> @endif
          @if($penandatangan->nip)       <p class="text-gray-600">NIP: {{ $penandatangan->nip }}</p> @endif
        </div>
      </div>

      {{-- QR CODE --}}
      <div class="mt-4 flex justify-center">
        <div class="text-center">
          <p class="text-sm font-semibold text-gray-600 mb-2">Scan untuk membubuhkan tanda tangan</p>
          <div class="p-4 border-4 border-amber-200 rounded-3xl inline-block bg-white">
            {!! QrCode::format('svg')->size(260)->margin(1)->generate($qrPejabatUrl) !!}
          </div>
          <p class="text-[11px] text-gray-500 mt-2 break-all max-w-xs mx-auto leading-snug">
            {{ $qrPejabatUrl }}
          </p>
        </div>
      </div>

      {{-- INSTRUKSI --}}
      <div class="mt-4 max-w-sm mx-auto w-full">
        <h2 class="text-sm font-bold text-gray-900 mb-2 text-center">Cara Menandatangani</h2>
        <div class="space-y-1.5 text-sm text-gray-700">
          <div class="flex gap-2"><span class="font-bold w-4">1.</span><span>Scan QR Code dengan kamera HP.</span></div>
          <div class="flex gap-2"><span class="font-bold w-4">2.</span><span>Verifikasi data diri yang tampil di layar.</span></div>
          <div class="flex gap-2"><span class="font-bold w-4">3.</span><span>Gambar tanda tangan digital pada kolom yang tersedia.</span></div>
          <div class="flex gap-2"><span class="font-bold w-4">4.</span><span>Tekan Simpan untuk menyelesaikan penandatanganan.</span></div>
        </div>
      </div>

      {{-- FOOTER --}}
      <div class="mt-auto pt-3 border-t text-center">
        <p class="text-xs text-gray-400">
          Dikelola oleh <span class="font-semibold text-gray-600">SIGAP — BRIDA Kota Makassar</span>
        </p>
      </div>

    </div>
  </div>

</body>
</html>