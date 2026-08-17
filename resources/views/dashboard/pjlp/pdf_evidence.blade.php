<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Lampiran Evidence Logbook PJLP</title>
  <style>
    @page {
      margin: 14mm 12mm 16mm 12mm;
      size: letter portrait;
    }
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #111827;
      line-height: 1.25;
      font-size: 9px;
      margin: 0;
      padding: 0;
    }
    .page-break {
      page-break-after: always;
    }

    /* TITLE PEMISAH KHUSUS LOGBOOK */
    .logbook-title-header {
      width: 100%;
      border-bottom: 2px solid #7a2222;
      padding-bottom: 5px;
      margin-bottom: 8px;
    }
    .instansi-name {
      font-size: 11px;
      font-weight: bold;
      color: #7a2222;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }
    .logbook-main-title {
      font-size: 12px;
      font-weight: bold;
      color: #111827;
      margin: 2px 0;
      text-transform: uppercase;
    }
    .logbook-meta-info {
      font-size: 8.5px;
      color: #4b5563;
    }

    /* Grid Evidence 2 Kolom (3 Kiri, 3 Kanan per Halaman Letter) */
    .grid-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 8px 6px;
      margin-top: 0px;
    }
    .grid-cell {
      width: 50%;
      vertical-align: top;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 6px;
      background-color: #ffffff;
    }

    /* Wrapper Frame Foto untuk Mencegah Gambar Gepeng & Memaksimalkan Tinggi */
    .img-container {
      width: 100%;
      height: 175px;
      background-color: #f8fafc;
      border: 1px solid #f1f5f9;
      border-radius: 4px;
      text-align: center;
      vertical-align: middle;
      display: block;
      margin-bottom: 4px;
      overflow: hidden;
    }

    /* Solusi Anti-Gepeng DomPDF: Batasi max dimensi & biarkan rasio asli */
    .evidence-img {
      max-width: 100%;
      max-height: 175px;
      width: auto;
      height: auto;
      display: inline-block;
      vertical-align: middle;
      border-radius: 3px;
    }

    .no-img-box {
      width: 100%;
      height: 175px;
      line-height: 175px;
      text-align: center;
      color: #94a3b8;
      font-size: 9px;
      font-style: italic;
    }

    .evidence-meta {
      font-size: 8.5px;
      font-weight: bold;
      color: #7a2222;
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 3px;
      margin-bottom: 4px;
    }

    .evidence-desc {
      font-size: 8.5px;
      color: #334155;
      min-height: 38px;
      max-height: 48px;
      overflow: hidden;
      line-height: 1.25;
      padding-top: 2px;
    }

    .status-badge {
      display: inline-block;
      float: right;
      font-size: 7.5px;
      font-weight: bold;
      padding: 1px 4px;
      border-radius: 3px;
      background-color: #ecfdf5;
      color: #065f46;
      border: 0.5px solid #a7f3d0;
    }

    /* Footer Verifikasi & QR Code */
    .footer-table {
      position: fixed;
      bottom: 0px;
      left: 0px;
      right: 0px;
      width: 100%;
      border-top: 1px solid #e2e8f0;
      padding-top: 4px;
      background-color: #ffffff;
    }
    .footer-table td {
      vertical-align: middle;
      border: none;
    }
    .footer-text {
      font-size: 7.5px;
      color: #475569;
      line-height: 1.2;
    }
  </style>
</head>
<body>

  <!-- FOOTER DI SETIAP LEMBAR EVIDENCE -->
  <table class="footer-table">
    <tr>
      <td style="width: 50px;">
        <img src="{{ $qrCodeBase64 }}" style="width: 40px; height: 40px;" alt="QR Validasi">
      </td>
      <td class="footer-text">
        Lampiran Evidence Pekerjaan Harian PJLP diterbitkan secara sah melalui <b>SIGAP PJLP — {{ strtoupper($user->unit ?: 'BRIDA KOTA MAKASSAR') }}</b>.<br>
        Scan QR Code untuk memverifikasi keaslian rekaman logbook pekerjaan.
      </td>
      <td style="text-align: right; width: 110px; font-size: 7.5px; color: #64748b;">
        PJLP: <b>{{ $user->name }}</b><br>
        Periode: {{ $periode->bulan_tahun }}
      </td>
    </tr>
  </table>

  @foreach($logbookPages as $pageIndex => $chunk)
    <!-- TITLE PEMISAH KHUSUS LOGBOOK -->
    <table class="logbook-title-header">
      <tr>
        <td>
          <div class="instansi-name">{{ strtoupper($user->unit ?: 'BADAN RISET DAN INOVASI DAERAH') }}</div>
          <div class="logbook-main-title">LAMPIRAN EVIDENCE DAN LOGBOOK PEKERJAAN HARIAN</div>
          <div class="logbook-meta-info">
            PJLP: <b>{{ strtoupper($user->name) }}</b> | Periode: <b>{{ $namaBulanTahun }}</b> ({{ $firstDate }} s.d. {{ $lastDate }})
          </div>
        </td>
      </tr>
    </table>

    <!-- GRID EVIDENCE: 2 KOLOM (Kiri & Kanan), 3 BARIS PER LEMBAR -->
    <table class="grid-table">
      @foreach($chunk->chunk(2) as $row)
        <tr>
          @foreach($row as $item)
            <td class="grid-cell">
              <div class="evidence-meta">
                {{ $item->hari }}, {{ $item->tanggal->format('d/m/Y') }}
                <span class="status-badge">{{ strtoupper($item->status) }}</span>
              </div>
              
              <!-- Container Frame Foto -->
              <div class="img-container">
                @if($item->foto_base64)
                  <img src="{{ $item->foto_base64 }}" class="evidence-img" alt="Foto Evidence">
                @else
                  <div class="no-img-box">Evidence Tidak Tersedia</div>
                @endif
              </div>

              <!-- Uraian Pekerjaan -->
              <div class="evidence-desc">
                <b>Uraian:</b> {{ $item->deskripsi_pekerjaan ?: '-' }}
              </div>
            </td>
          @endforeach

          {{-- Kolom dummy penyeimbang jika jumlah ganjil --}}
          @if($row->count() === 1)
            <td class="grid-cell" style="border: none; background: transparent;"></td>
          @endif
        </tr>
      @endforeach
    </table>

    {{-- Page break jika masih ada lembar halaman evidence berikutnya --}}
    @if(!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

</body>
</html>