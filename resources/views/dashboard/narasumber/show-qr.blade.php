@extends('layouts.app')
@section('content')
<style>
  @media print {
    body * {
      visibility: hidden;
    }
    #printable-qr-card, #printable-qr-card * {
      visibility: visible;
    }
    #printable-qr-card {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
    }
    .no-print {
      display: none !important;
    }
  }
</style>

<div class="max-w-xl mx-auto text-center">
  <div id="printable-qr-card" class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-200">
    <span class="inline-block px-3 py-1 bg-red-50 text-red-700 text-xs font-semibold rounded-full uppercase tracking-wider mb-2">
      SIGAP NARASUMBER
    </span>
    <h2 class="font-extrabold text-xl text-gray-900">QR Code Kesediaan Narasumber</h2>
    <p class="text-sm text-gray-600 mt-1 font-medium">{{ $kegiatan->nama_kegiatan }}</p>
    
    @if($kegiatan->hari_tanggal || $kegiatan->tempat)
      <p class="text-xs text-gray-400 mt-0.5">
        {{ $kegiatan->hari_tanggal }} {{ $kegiatan->tempat ? '• ' . $kegiatan->tempat : '' }}
      </p>
    @endif
    
    <!-- QR Code SVG Container -->
    <div class="mt-6">
      <div id="qr-container" class="inline-block p-4 rounded-2xl border border-gray-200 bg-white shadow-sm">
        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(240)->margin(1)->generate($qrUrl) !!}
      </div>
    </div>

    <!-- Tautan Publik -->
    <div class="mt-4 p-2.5 rounded-xl bg-blue-50/80 border border-blue-100 flex items-center justify-between text-xs text-blue-700 gap-2">
      <a href="{{ $qrUrl }}" target="_blank" class="truncate hover:underline font-mono text-left">{{ $qrUrl }}</a>
      <button type="button" onclick="copyLink('{{ $qrUrl }}')" class="shrink-0 px-2.5 py-1 bg-white rounded-lg border border-blue-200 text-blue-800 font-medium hover:bg-blue-50 transition-colors">
        Salin
      </button>
    </div>

    <!-- Tombol Aksi (Tidak Tampil Saat Print) -->
    <div class="mt-6 pt-5 border-t border-gray-100 no-print space-y-3">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
        <!-- 1. Share ke WhatsApp -->
        @php
          $waLines = [
              "📋 *FORMULIR KESEDIAAN NARASUMBER*",
              "Sistem Informasi & Pengelolaan Administrasi (SIGAP)",
              "━━━━━━━━━━━━━━━━━━━━━━",
              "",
              "Yth. Bapak/Ibu Narasumber,",
              "",
              "Sehubungan dengan pelaksanaan kegiatan:",
              "📌 *Kegiatan:* " . $kegiatan->nama_kegiatan,
          ];

          if ($kegiatan->hari_tanggal) {
              $waLines[] = "🗓️ *Waktu/Tanggal:* " . $kegiatan->hari_tanggal;
          }
          if ($kegiatan->tempat) {
              $waLines[] = "📍 *Tempat:* " . $kegiatan->tempat;
          }

          $waLines[] = "";
          $waLines[] = "Kami memohon kesediaan Bapak/Ibu untuk mengisi formulir biodata dan kesediaan narasumber secara digital melalui tautan di bawah ini:";
          $waLines[] = "👉 " . $qrUrl;
          $waLines[] = "";
          $waLines[] = "📝 *Mohon siapkan:*";
          $waLines[] = "• NIK (16 digit) & NPWP";
          $waLines[] = "• Foto/Scan Berkas KTP";
          $waLines[] = "• Tanda Tangan Digital pada form";
          $waLines[] = "";
          $waLines[] = "Atas kesediaan dan kerja samanya, kami ucapkan terima kasih. 🙏";

          $waText = implode("\n", $waLines);
          $waUrl = 'https://api.whatsapp.com/send?text=' . rawurlencode($waText);
        @endphp
        <a href="{{ $waUrl }}" target="_blank" 
           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
          <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
          </svg>
          Share ke WA
        </a>

        <!-- 2. Simpan Gambar Poster Cantik -->
        <button type="button" onclick="downloadBeautifiedQrImage()" 
                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Simpan Gambar
        </button>

        <!-- 3. Simpan PDF / Cetak -->
        <button type="button" onclick="window.print()" 
                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-gray-800 text-white text-xs font-semibold hover:bg-gray-900 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
          </svg>
          Cetak / PDF
        </button>
      </div>

      <div class="pt-2">
        <a href="{{ route('sigap-narasumber.pilih-kegiatan') }}" class="inline-flex items-center gap-1 px-4 py-2 rounded-xl border border-gray-300 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors">
          &larr; Kembali ke Daftar Kegiatan
        </a>
      </div>
    </div>
  </div>
</div>

<script>
  function copyLink(text) {
    navigator.clipboard.writeText(text).then(() => {
      alert('Tautan formulir berhasil disalin!');
    }).catch(() => {
      prompt('Salin link berikut secara manual:', text);
    });
  }

  // Generate Poster Card Beresolusi Tinggi (HD) & Indah
  function downloadBeautifiedQrImage() {
    const svgElement = document.querySelector('#qr-container svg');
    if (!svgElement) return;

    const svgData = new XMLSerializer().serializeToString(svgElement);
    const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
    const DOMURL = window.URL || window.webkitURL || window;
    const url = DOMURL.createObjectURL(svgBlob);

    const img = new Image();
    img.onload = function () {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      // Dimensi Poster HD
      const width = 800;
      const height = 1060;
      canvas.width = width;
      canvas.height = height;

      // Helper Fungsi Menggambar Rounded Rect
      function roundRect(x, y, w, h, radius, fill, stroke) {
        ctx.beginPath();
        ctx.moveTo(x + radius, y);
        ctx.lineTo(x + w - radius, y);
        ctx.quadraticCurveTo(x + w, y, x + w, y + radius);
        ctx.lineTo(x + w, y + h - radius);
        ctx.quadraticCurveTo(x + w, y + h, x + w - radius, y + h);
        ctx.lineTo(x + radius, y + h);
        ctx.quadraticCurveTo(x, y + h, x, y + h - radius);
        ctx.lineTo(x, y + radius);
        ctx.quadraticCurveTo(x, y, x + radius, y);
        ctx.closePath();
        if (fill) ctx.fill();
        if (stroke) ctx.stroke();
      }

      // Helper Text Wrap
      function wrapText(text, x, y, maxWidth, lineHeight, maxLines = 3) {
        const words = text.split(' ');
        let line = '';
        let lines = [];

        for (let n = 0; n < words.length; n++) {
          const testLine = line + words[n] + ' ';
          const metrics = ctx.measureText(testLine);
          if (metrics.width > maxWidth && n > 0) {
            lines.push(line.trim());
            line = words[n] + ' ';
          } else {
            line = testLine;
          }
        }
        lines.push(line.trim());

        if (lines.length > maxLines) {
          lines = lines.slice(0, maxLines);
          lines[maxLines - 1] += '...';
        }

        const startY = y - ((lines.length - 1) * lineHeight) / 2;
        for (let i = 0; i < lines.length; i++) {
          ctx.fillText(lines[i], x, startY + i * lineHeight);
        }
        return lines.length;
      }

      // 1. Background Luar (Soft Grayish Gradient)
      const bgGrad = ctx.createLinearGradient(0, 0, width, height);
      bgGrad.addColorStop(0, '#f8fafc');
      bgGrad.addColorStop(1, '#e2e8f0');
      ctx.fillStyle = bgGrad;
      ctx.fillRect(0, 0, width, height);

      // 2. Main White Card
      ctx.shadowColor = 'rgba(0, 0, 0, 0.08)';
      ctx.shadowBlur = 30;
      ctx.shadowOffsetY = 15;
      ctx.fillStyle = '#ffffff';
      roundRect(45, 45, width - 90, height - 90, 28, true, false);

      // Reset Shadow
      ctx.shadowColor = 'transparent';

      // 3. Header Accent Bar (Merah Maroon)
      const maroonGrad = ctx.createLinearGradient(45, 45, width - 45, 45);
      maroonGrad.addColorStop(0, '#881337');
      maroonGrad.addColorStop(1, '#9f1239');
      ctx.fillStyle = maroonGrad;
      roundRect(45, 45, width - 90, 16, { tl: 28, tr: 28, bl: 0, br: 0 }, true, false);

      // 4. Pill Badge: SIGAP NARASUMBER
      ctx.fillStyle = '#fff1f2';
      ctx.strokeStyle = '#fecdd3';
      ctx.lineWidth = 1.5;
      roundRect(width / 2 - 120, 95, 240, 36, 18, true, true);

      ctx.font = 'bold 13px Arial, sans-serif';
      ctx.fillStyle = '#9f1239';
      ctx.textAlign = 'center';
      ctx.letterSpacing = '1px';
      ctx.fillText('SIGAP NARASUMBER', width / 2, 118);

      // 5. Title & Activity Name
      ctx.font = 'bold 24px Arial, sans-serif';
      ctx.fillStyle = '#0f172a';
      ctx.fillText('Form Kesediaan Narasumber', width / 2, 175);

      // Nama Kegiatan (Auto-wrapped)
      ctx.font = '600 18px Arial, sans-serif';
      ctx.fillStyle = '#334155';
      const namaKegiatan = @json($kegiatan->nama_kegiatan);
      wrapText(namaKegiatan, width / 2, 225, width - 180, 26, 3);

      // Info Tanggal / Tempat jika ada
      const subInfo = @json(($kegiatan->hari_tanggal ? $kegiatan->hari_tanggal : '') . ($kegiatan->tempat ? ' • ' . $kegiatan->tempat : ''));
      if (subInfo.trim()) {
        ctx.font = '13px Arial, sans-serif';
        ctx.fillStyle = '#64748b';
        ctx.fillText(subInfo, width / 2, 280);
      }

      // 6. QR Box Container
      const qrBoxSize = 390;
      const qrBoxX = (width - qrBoxSize) / 2;
      const qrBoxY = 320;

      ctx.fillStyle = '#f8fafc';
      ctx.strokeStyle = '#e2e8f0';
      ctx.lineWidth = 2;
      roundRect(qrBoxX, qrBoxY, qrBoxSize, qrBoxSize, 24, true, true);

      // Draw QR Image
      const qrImgPadding = 30;
      ctx.drawImage(
        img,
        qrBoxX + qrImgPadding,
        qrBoxY + qrImgPadding,
        qrBoxSize - qrImgPadding * 2,
        qrBoxSize - qrImgPadding * 2
      );

      // 7. Footer Instructions
      ctx.font = 'bold 15px Arial, sans-serif';
      ctx.fillStyle = '#0f172a';
      ctx.fillText('Pindai QR Code untuk Konfirmasi Kesediaan', width / 2, 755);

      ctx.font = '13px Arial, sans-serif';
      ctx.fillStyle = '#64748b';
      ctx.fillText('Siapkan NIK, NPWP, Berkas KTP, dan Tanda Tangan Digital', width / 2, 782);

      // Footer Pill URL
      ctx.fillStyle = '#f1f5f9';
      ctx.strokeStyle = '#e2e8f0';
      ctx.lineWidth = 1;
      roundRect(width / 2 - 270, 815, 540, 42, 14, true, true);

      ctx.font = '13px monospace';
      ctx.fillStyle = '#2563eb';
      const shortUrl = @json($qrUrl);
      ctx.fillText(shortUrl, width / 2, 841);

      // Watermark Text Bawah
      ctx.font = '11px Arial, sans-serif';
      ctx.fillStyle = '#94a3b8';
      ctx.fillText('Badan Riset dan Inovasi Daerah • SIGAP', width / 2, 940);

      // Download file
      const pngFile = canvas.toDataURL('image/png');
      const downloadLink = document.createElement('a');
      downloadLink.download = 'Poster_QR_{{ \Illuminate\Support\Str::slug($kegiatan->nama_kegiatan) }}.png';
      downloadLink.href = pngFile;
      downloadLink.click();
      DOMURL.revokeObjectURL(url);
    };
    img.src = url;
  }
</script>
@endsection