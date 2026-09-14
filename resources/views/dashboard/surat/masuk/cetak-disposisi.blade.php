<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Lembar Disposisi - Agenda No. {{ sprintf('%03d', $surat->nomor_agenda) }}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 8mm 12mm;
    }
    * { box-sizing: border-box; }
    body {
      font-family: Arial, Helvetica, sans-serif;
      margin: 0;
      padding: 0;
      color: #000;
      font-size: 9.5pt;
    }
    .disposisi-card {
      border: 1.5px solid #000;
      padding: 8px 12px 6px 12px;
      height: 134mm;
      box-sizing: border-box;
      display: block;
      position: relative;
      overflow: hidden; /* Mengunci watermark tetap di dalam kartu */
    }

    /* Watermark Diagonal Tengah Lembar */
    .watermark-bg {
      position: absolute;
      top: 55%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-25deg);
      font-size: 24pt;
      font-weight: 800;
      color: #000;
      opacity: 0.06;
      white-space: nowrap;
      pointer-events: none;
      user-select: none;
      z-index: 0;
      text-transform: uppercase;
      letter-spacing: 2px;
    }
    
    /* Kop dengan 2 Logo Mengapit */
    .header-kop {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #000;
      padding-bottom: 4px;
      margin-bottom: 6px;
      position: relative;
      z-index: 1;
    }
    .logo-pemkot {
      width: 44px;
      height: 50px;
      object-fit: contain;
      flex-shrink: 0;
    }
    .logo-brida {
      width: 82px;           /* Logo BRIDA proporsional diperbesar */
      height: 48px;
      object-fit: contain;
      flex-shrink: 0;
    }
    .header-text {
      flex: 1;
      text-align: center;
      padding: 0 6px;
    }
    .header-text h2 {
      margin: 0;
      font-size: 9.5pt;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .header-text h1 {
      margin: 1px 0;
      font-size: 10.5pt;
      font-weight: bold;
    }
    .header-text .title-lembar {
      margin-top: 2px;
      font-size: 10.5pt;
      font-weight: bold;
      text-decoration: underline;
      letter-spacing: 1px;
    }

    table.disposisi-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 8.5pt;
      position: relative;
      z-index: 1;
      background: transparent;
    }
    table.disposisi-table td {
      border: 1px solid #000;
      padding: 4px 6px;
      vertical-align: top;
    }
    .w-50 { width: 50%; }
    .field-label {
      font-weight: bold;
      font-size: 8pt;
      text-transform: uppercase;
    }
    .bullet-list {
      margin: 4px 0 0 0;
      padding-left: 14px;
      list-style-type: disc;
    }
    .bullet-list li {
      margin-bottom: 5px;
    }
    .footer-sub {
      width: 100%;
      margin-top: 5px;
      font-size: 8pt;
      position: relative;
      z-index: 1;
    }
    .footer-sub table {
      width: 100%;
      border-collapse: collapse;
    }
    .footer-sub td {
      vertical-align: top;
      padding: 0 4px;
    }
    .bottom-bar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-top: 5px;
      position: relative;
      z-index: 1;
    }
    .catatan-box {
      font-size: 7pt;
      line-height: 1.2;
    }
    .watermark-footer {
      font-size: 6.5pt;
      font-family: monospace;
      color: #555;
      text-align: right;
      letter-spacing: 0.3px;
    }
    .cut-line {
      text-align: center;
      margin: 8mm 0;
      border-top: 1px dashed #666;
      position: relative;
    }
    .cut-line span {
      position: relative;
      top: -9px;
      background: #fff;
      padding: 0 8px;
      font-size: 7.5pt;
      color: #666;
    }
    @media print {
      .no-print { display: none !important; }
      body { background: transparent !important; }
    }
  </style>
</head>
<body>

  <!-- Tombol Cetak Layar -->
  <div class="no-print" style="position: fixed; top: 10px; right: 15px; background: #fff; border: 1px solid #ccc; padding: 8px 14px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 999;">
    <button onclick="window.print()" style="background: #7a2222; color: #fff; font-weight: bold; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer;">
      🖨️ Cetak Disposisi (Ctrl+P)
    </button>
  </div>

  @for($copy = 1; $copy <= 2; $copy++)
    <div class="disposisi-card">
      
      <!-- Watermark Transparan Latar Belakang -->
      <div class="watermark-bg">
        SIGAP SURAT • BRIDA MAKASSAR
      </div>

      <!-- Header Kop dengan Logo Pemkot dan Logo BRIDA -->
      <div class="header-kop">
        <img src="{{ asset('images/logo-pemkot.png') }}" alt="Logo Pemkot" class="logo-pemkot">
        
        <div class="header-text">
          <h2>PEMERINTAH KOTA MAKASSAR</h2>
          <h1>BADAN RISET DAN INOVASI DAERAH</h1>
          <div class="title-lembar">LEMBAR DISPOSISI</div>
        </div>

        <img src="{{ asset('images/logo-brida.png') }}" alt="Logo BRIDA" class="logo-brida">
      </div>

      <table class="disposisi-table">
        <tr>
          <td class="w-50"><span class="field-label">SURAT DARI :</span> {{ $surat->asal_surat }}</td>
          <td class="w-50"><span class="field-label">Diterima Tanggal :</span> {{ $surat->tanggal_terima->format('d/m/Y') }}</td>
        </tr>
        <tr>
          <td><span class="field-label">Tanggal Surat :</span> {{ $surat->tanggal_surat->format('d/m/Y') }}</td>
          <td><span class="field-label">Nomor Agenda :</span> <b>{{ sprintf('%03d', $surat->nomor_agenda) }}</b></td>
        </tr>
        <tr>
          <td><span class="field-label">Nomor Surat :</span> {{ $surat->nomor_surat_masuk }}</td>
          <td><span class="field-label">Tingkat Surat :</span> {{ $surat->tingkat_surat }}</td>
        </tr>
        <tr>
          <td style="background-color: rgba(245, 245, 245, 0.6);"><span class="field-label">Diteruskan Kpd :</span></td>
          <td style="background-color: rgba(245, 245, 245, 0.6);"><span class="field-label">Segera Untuk :</span></td>
        </tr>
        <tr style="height: 48mm;">
          <td>
            <ul class="bullet-list">
              <li>Sekretariat</li>
              <li>Ketua Riset</li>
              <li>Ketua Inovasi</li>
            </ul>
          </td>
          <td>
            <div style="font-weight:bold; font-size: 8.5pt; margin-bottom: 4px;">Isi Disposisi Kepala BRIDA:</div>
            <div style="height: 38mm;"></div>
          </td>
        </tr>
      </table>

      <div class="footer-sub">
        <table>
          <tr>
            <td style="width: 50%;"><b>Dari Sekretaris :</b></td>
            <td style="width: 50%;">
              <b>Kasubag :</b><br>
              <b>Koordinator :</b>
            </td>
          </tr>
        </table>
      </div>

      <div class="bottom-bar">
        <div class="catatan-box">
          <b>Catatan:</b><br>
          1. Sekretaris: Untuk diketahui dan diarsipkan<br>
          2. Koordinator: Arsip. Koordinator Ybs
        </div>

        <!-- Tanda Cetak Resmi Sigap Surat -->
        <div class="watermark-footer">
          Digenerate resmi oleh <b>SIGAP SURAT</b><br>
          Waktu: {{ now()->translatedFormat('d/m/Y H:i') }} WITA
        </div>
      </div>

    </div>

    @if($copy === 1)
      <div class="cut-line">
        <span>✂ Gunting / Potong di sini (Ukuran 1/2 HVS A4)</span>[cite: 1, 2]
      </div>
    @endif
  @endfor

</body>
</html>