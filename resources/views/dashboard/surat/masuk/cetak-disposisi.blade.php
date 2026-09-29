<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Lembar Disposisi - Agenda No. {{ sprintf('%03d', $surat->nomor_agenda) }}</title>
  <style>
    @page {
      size: A4 portrait;
      margin: 12mm 15mm;
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
      padding: 12px 16px 10px 16px;
      box-sizing: border-box;
      display: block;
      position: relative;
      overflow: hidden;
      background: #fff;
    }

    /* Watermark Diagonal Tengah Lembar */
    .watermark-bg {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-25deg);
      font-size: 26pt;
      font-weight: 800;
      color: #000;
      opacity: 0.05;
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
      padding-bottom: 6px;
      margin-bottom: 8px;
      position: relative;
      z-index: 1;
    }
    .logo-pemkot {
      width: 48px;
      height: 54px;
      object-fit: contain;
      flex-shrink: 0;
    }
    .logo-brida {
      width: 88px;
      height: 52px;
      object-fit: contain;
      flex-shrink: 0;
    }
    .header-text {
      flex: 1;
      text-align: center;
      padding: 0 8px;
    }
    .header-text h2 {
      margin: 0;
      font-size: 10pt;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .header-text h1 {
      margin: 2px 0;
      font-size: 11.5pt;
      font-weight: bold;
    }
    .header-text .title-lembar {
      margin-top: 3px;
      font-size: 11pt;
      font-weight: bold;
      text-decoration: underline;
      letter-spacing: 1px;
    }

    table.disposisi-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 9pt;
      position: relative;
      z-index: 1;
      background: transparent;
    }
    table.disposisi-table td {
      border: 1px solid #000;
      padding: 6px 8px;
      vertical-align: top;
    }
    .w-50 { width: 50%; }
    .field-label {
      font-weight: bold;
      font-size: 8.5pt;
      text-transform: uppercase;
    }
    .bullet-list {
      margin: 6px 0 0 0;
      padding-left: 16px;
      list-style-type: disc;
    }
    .bullet-list li {
      margin-bottom: 8px;
    }
    .footer-sub {
      width: 100%;
      margin-top: 8px;
      font-size: 8.5pt;
      position: relative;
      z-index: 1;
    }
    .footer-sub table {
      width: 100%;
      border-collapse: collapse;
    }
    .footer-sub td {
      vertical-align: top;
      padding: 0 6px;
    }
    .bottom-bar {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      margin-top: 10px;
      padding-top: 6px;
      border-top: 1px dotted #999;
      position: relative;
      z-index: 1;
    }
    .catatan-box {
      font-size: 7.5pt;
      line-height: 1.3;
      color: #222;
    }
    .watermark-footer {
      font-size: 7pt;
      font-family: 'Courier New', Courier, monospace;
      color: #333;
      text-align: right;
      line-height: 1.3;
    }
    @media print {
      .no-print { display: none !important; }
      body { background: transparent !important; }
      .disposisi-card { border: 1.5px solid #000 !important; }
    }
  </style>
</head>
<body>

  <!-- Tombol Cetak Layar -->
  <div class="no-print" style="position: fixed; top: 12px; right: 16px; background: #fff; border: 1px solid #ccc; padding: 8px 14px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 999;">
    <button onclick="window.print()" style="background: #7a2222; color: #fff; font-weight: bold; border: none; padding: 7px 16px; border-radius: 6px; cursor: pointer;">
      🖨️ Cetak Disposisi (Ctrl+P)
    </button>
  </div>

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
        <td class="w-50"><span class="field-label">Diterima Tanggal :</span> {{ $surat->tanggal_terima ? \Carbon\Carbon::parse($surat->tanggal_terima)->format('d/m/Y') : '-' }}</td>
      </tr>
      <tr>
        <td><span class="field-label">Tanggal Surat :</span> {{ $surat->tanggal_surat ? \Carbon\Carbon::parse($surat->tanggal_surat)->format('d/m/Y') : '-' }}</td>
        <td><span class="field-label">Nomor Agenda :</span> <b>{{ sprintf('%03d', $surat->nomor_agenda) }}</b></td>
      </tr>
      <tr>
        <td><span class="field-label">Nomor Surat :</span> {{ $surat->nomor_surat_masuk }}</td>
        <td><span class="field-label">Tingkat Surat :</span> {{ $surat->tingkat_surat }}</td>
      </tr>
      <tr>
        <td colspan="2"><span class="field-label">Perihal :</span> {{ $surat->perihal }}</td>
      </tr>
      <tr>
        <td style="background-color: rgba(245, 245, 245, 0.7);"><span class="field-label">Diteruskan Kpd :</span></td>
        <td style="background-color: rgba(245, 245, 245, 0.7);"><span class="field-label">Segera Untuk :</span></td>
      </tr>
      <tr style="height: 52mm;">
        <td>
          <ul class="bullet-list">
            <li>Sekretariat</li>
            <li>Ketua Riset</li>
            <li>Ketua Inovasi</li>
            <li>Unit Pengolah: <b>{{ $surat->unit_pengolah ?? '-' }}</b></li>
          </ul>
        </td>
        <td>
          <div style="font-weight:bold; font-size: 8.5pt; margin-bottom: 4px;">Isi Disposisi Kepala BRIDA:</div>
          <div style="height: 42mm;"></div>
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

    <!-- Bottom Bar & Identitas Generate Sistem -->
    <div class="bottom-bar">
      <div class="catatan-box">
        <b>Catatan Disposisi:</b><br>
        1. Sekretaris: Untuk diketahui dan diarsipkan<br>
        2. Koordinator: Arsip / Koordinator Ybs.
      </div>

      <div class="watermark-footer">
        Dokumen ini diterbitkan resmi melalui <b>SIGAP SURAT</b> (Fitur Lembar Disposisi)<br>
        Dicetak oleh: {{ auth()->user()->name ?? 'Petugas Arsip' }} | Waktu: {{ now()->translatedFormat('d/m/Y H:i') }} WITA
      </div>
    </div>

  </div>

</body>
</html>