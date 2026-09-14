<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Buku Register Surat Keluar Tahun {{ $tahun }} — BRIDA Kota Makassar</title>
  
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50:'#fdf7f7', 100:'#faeeee', 700:'#7a2222', 800:'#661b1b', DEFAULT:'#7a2222'
            }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Cinzel:wght@700&display=swap" rel="stylesheet">
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #525659;
      margin: 0;
      padding: 20px 0;
      color: #111827;
    }

    /* Format Kertas A4 Landscape untuk Cetak */
    @page {
      size: A4 landscape;
      margin: 12mm 10mm 15mm 10mm;
    }

    .book-page {
      background: white;
      width: 297mm;
      min-height: 210mm;
      margin: 0 auto 30px auto;
      padding: 15mm;
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
      box-sizing: border-box;
      position: relative;
    }

    .page-break {
      page-break-after: always;
      break-after: page;
    }

    @media print {
      body {
        background: transparent !important;
        padding: 0 !important;
      }
      .no-print {
        display: none !important;
      }
      .book-page {
        width: 100% !important;
        min-height: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
      }
    }
  </style>
</head>
<body>

<!-- Floating Print & Filter Control Bar -->
  <div class="no-print fixed top-4 right-6 z-50 flex items-center gap-3 bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-xl border border-gray-200">
    
    <!-- Form Pilih Tahun Publik -->
    <form method="GET" action="{{ route('sigap-surat.keluar.buku-agenda') }}" class="flex items-center gap-1.5 text-xs">
      <span class="font-bold text-gray-700">Tahun:</span>
      <select name="tahun" onchange="this.form.submit()" class="rounded-lg border border-gray-300 py-1 px-2 text-xs font-semibold bg-gray-50 focus:ring-0">
        @for($y = date('Y'); $y >= 2024; $y--)
          <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
    </form>

    <span class="text-gray-300">|</span>

    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-maroon text-white text-xs font-bold hover:bg-maroon-800 shadow transition">
      🖨️ Cetak / PDF
    </button>

    @auth
      <a href="{{ route('sigap-surat.keluar.index') }}" class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
        Ke Dashboard
      </a>
    @endauth
  </div>
  <!-- ========================================================================= -->
  <!-- HALAMAN 1: SAMPUL DEPAN (COVER BUKU REGISTER AGENDA FISIK) -->
  <!-- ========================================================================= -->
  <div class="book-page page-break flex flex-col justify-between items-center text-center border-[6px] border-double border-gray-900 p-16">
    
    <!-- Header Sampul -->
    <div class="pt-8 space-y-2">
      <h3 class="text-lg font-bold tracking-widest uppercase text-gray-700">Pemerintah Kota Makassar</h3>
      <h2 class="text-2xl font-black tracking-wider uppercase text-gray-900">Badan Riset dan Inovasi Daerah</h2>
      <div class="w-32 h-1 bg-maroon mx-auto mt-2"></div>
    </div>

    <!-- Judul Buku Utama -->
    <div class="my-auto space-y-6">
      <div class="inline-block p-6 border-2 border-gray-800 rounded-2xl">
        <h1 class="text-4xl font-black uppercase tracking-widest text-gray-900" style="font-family: 'Cinzel', serif;">
          Buku Register Agenda
        </h1>
        <p class="text-2xl font-extrabold text-maroon tracking-widest uppercase mt-2">
          Surat Keluar
        </p>
      </div>

      <div class="text-sm font-semibold text-gray-600 tracking-wide">
        Klasifikasi Berdasarkan Format Permendagri No. 83 Tahun 2022
      </div>

      <div class="text-6xl font-black text-gray-900 tracking-wider pt-2">
        TAHUN {{ $tahun }}
      </div>
    </div>

    <!-- Footer Sampul (Tanda Pengesahan Sistem) -->
    <div class="w-full flex items-center justify-between border-t border-gray-300 pt-4 px-4 text-xs text-gray-500">
      <span>Sistem Informasi Tata Naskah Dinas & Persuratan</span>
      <span class="font-bold text-gray-700">SIGAP SURAT — BRIDA KOTA MAKASSAR</span>
    </div>

  </div>

  <!-- ========================================================================= -->
  <!-- HALAMAN 2 DST: ISI TABEL REGISTER AGENDA (Buku Manual Digital) -->
  <!-- ========================================================================= -->
  <div class="book-page">
    
    <!-- Kop Halaman Buku Agenda -->
    <div class="flex items-center justify-between border-b-2 border-gray-900 pb-3 mb-4">
      <div>
        <h2 class="text-base font-black uppercase text-gray-900">Buku Register Surat Keluar</h2>
        <p class="text-xs text-gray-600">Badan Riset dan Inovasi Daerah Kota Makassar — Tahun Takwim {{ $tahun }}</p>
      </div>
      <div class="text-right text-xs">
        <span class="px-2.5 py-1 rounded bg-gray-100 font-bold text-gray-700">KODE OPD: BRIDA</span>
      </div>
    </div>

    <!-- Tabel Register 5 Kolom Klasik -->
    <table class="w-full text-xs border-collapse border border-gray-900">
      <thead>
        <tr class="bg-gray-100 text-gray-900 text-center font-bold uppercase">
          <th class="border border-gray-900 px-2 py-2.5 w-12">No. Urut</th>
          <th class="border border-gray-900 px-3 py-2.5 w-24">Tanggal</th>
          <th class="border border-gray-900 px-2 py-2.5 w-20">No. Berkas</th>
          <th class="border border-gray-900 px-3 py-2.5 w-60">Nomor Surat Lengkap</th>
          <th class="border border-gray-900 px-3 py-2.5">Alamat Penerima / Tujuan</th>
          <th class="border border-gray-900 px-3 py-2.5">Perihal / Isi Ringkas Surat</th>
          <th class="border border-gray-900 px-3 py-2.5 w-32">Pengolah / Pembuat</th>
          <th class="border border-gray-900 px-2 py-2.5 w-16">Ket.</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-900">
        @forelse($surats as $item)
          <tr class="{{ $item->status === 'slot_kosong' ? 'bg-amber-50/20' : ($item->status === 'batal' ? 'bg-gray-100/70' : '') }}">
            <td class="border border-gray-900 px-2 py-2 text-center font-bold text-gray-900">
              {{ $item->nomor_urut }}
            </td>
            <td class="border border-gray-900 px-3 py-2 text-center whitespace-nowrap">
              {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
            </td>
            <td class="border border-gray-900 px-2 py-2 text-center font-mono">
              {{ $item->nomor_berkas ?: '-' }}
            </td>
            <td class="border border-gray-900 px-3 py-2 font-semibold">
              @if($item->status === 'slot_kosong')
                <span class="text-gray-400 font-normal">-</span>
              @elseif($item->status === 'batal')
                <span class="line-through text-red-600">{{ $item->nomor_surat_lengkap }}</span>
              @else
                {{ $item->nomor_surat_lengkap }}
              @endif
            </td>
            <td class="border border-gray-900 px-3 py-2">
              {{ $item->alamat_penerima ?: '-' }}
            </td>
            <td class="border border-gray-900 px-3 py-2">
              {{ $item->perihal ?: '-' }}
            </td>
            <td class="border border-gray-900 px-3 py-2 text-[11px] text-gray-700">
              {{ $item->creator->name ?? '-' }}
            </td>
            <td class="border border-gray-900 px-2 py-2 text-center text-[10px] font-bold">
              @if($item->status === 'terbit')
                <span class="text-emerald-700">TERBIT</span>
              @elseif($item->status === 'batal')
                <span class="text-red-600">BATAL</span>
              @else
                <span class="text-gray-400 font-normal">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="border border-gray-900 px-4 py-8 text-center text-gray-500 italic">
              Tidak ada data buku register surat keluar yang tercatat untuk tahun {{ $tahun }}.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Tanda Verifikasi Resmi SIGAP SURAT di Bagian Footer Halaman -->
    <div class="mt-8 pt-4 border-t border-dashed border-gray-400 flex items-center justify-between text-[10px] text-gray-600">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6 rounded bg-maroon text-white flex items-center justify-center font-bold text-xs">
          ✓
        </div>
        <div>
          <p class="font-bold text-gray-800">DIVERIFIKASI DAN DICETAK SECARA RESMI OLEH SIGAP SURAT</p>
          <p>Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar • Dokumen ini sah dan diakui secara digital.</p>
        </div>
      </div>
      <div class="text-right">
        <p>Waktu Cetak: {{ now()->translatedFormat('d F Y, H:i') }} WITA</p>
        <p class="font-mono text-gray-400">UUID: {{ strtoupper(substr(md5($tahun . now()), 0, 16)) }}</p>
      </div>
    </div>

  </div>

</body>
</html>