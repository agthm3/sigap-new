<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Bulanan PJLP - {{ $user->name }}</title>
  <style>
    @page {
      margin: 18mm 14mm 18mm 14mm;
      size: letter portrait;
    }
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #111827;
      line-height: 1.25;
      font-size: 9.5px;
      margin: 0;
      padding: 0;
    }
    .page-break {
      page-break-after: always;
    }
    
    /* Header Utama */
    .header-table {
      width: 100%;
      border-bottom: 2px solid #7a2222;
      padding-bottom: 6px;
      margin-bottom: 10px;
    }
    .instansi-title {
      font-size: 13px;
      font-weight: bold;
      color: #7a2222;
      text-transform: uppercase;
      margin: 0;
      letter-spacing: 0.5px;
    }
    .doc-title {
      font-size: 11px;
      font-weight: bold;
      color: #1f2937;
      margin: 2px 0 0 0;
    }
    .doc-subtitle {
      font-size: 9px;
      color: #4b5563;
      margin: 1px 0 0 0;
    }

    /* Profil Card */
    .profile-card {
      width: 100%;
      border: 1px solid #e5e7eb;
      background-color: #f9fafb;
      border-radius: 6px;
      margin-bottom: 12px;
    }
    .profile-card td {
      padding: 6px 8px;
      vertical-align: middle;
    }
    .profile-photo {
      width: 60px;
      height: 72px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #d1d5db;
    }
    .profile-photo-placeholder {
      width: 60px;
      height: 72px;
      background-color: #e5e7eb;
      border: 1px solid #d1d5db;
      border-radius: 4px;
      text-align: center;
      line-height: 72px;
      color: #6b7280;
      font-size: 8px;
      font-weight: bold;
    }
    .info-table {
      width: 100%;
      font-size: 9px;
    }
    .info-table td {
      padding: 1.5px 3px !important;
      border: none !important;
    }
    .info-label {
      width: 22%;
      font-weight: bold;
      color: #374151;
    }

    /* Grid Evidence 2 Kolom (3 Kiri, 3 Kanan) */
    .grid-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 6px;
      margin-top: 0px;
    }
    .grid-cell {
      width: 50%;
      vertical-align: top;
      border: 1px solid #e5e7eb;
      border-radius: 5px;
      padding: 5px;
      background-color: #ffffff;
    }
    .evidence-img {
      width: 100%;
      height: 110px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #f3f4f6;
      margin-bottom: 3px;
    }
    .no-img-box {
      width: 100%;
      height: 110px;
      background-color: #f9fafb;
      border: 1px dashed #d1d5db;
      border-radius: 4px;
      text-align: center;
      line-height: 110px;
      color: #9ca3af;
      font-size: 8.5px;
      margin-bottom: 3px;
    }
    .evidence-meta {
      font-size: 8.5px;
      font-weight: bold;
      color: #7a2222;
      border-bottom: 1px solid #f3f4f6;
      padding-bottom: 2px;
      margin-bottom: 3px;
    }
    .evidence-desc {
      font-size: 8px;
      color: #374151;
      height: 32px;
      overflow: hidden;
      line-height: 1.2;
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
      border-top: 1px solid #e5e7eb;
      padding-top: 4px;
      background-color: #ffffff;
    }
    .footer-table td {
      vertical-align: middle;
      border: none;
    }
    .footer-text {
      font-size: 7.5px;
      color: #4b5563;
      line-height: 1.2;
    }
  </style>
</head>
<body>

  <!-- FOOTER DI SETIAP LEMBAR REKAP -->
  <table class="footer-table">
    <tr>
      <td style="width: 55px;">
        <img src="{{ $qrCodeBase64 }}" style="width: 45px; height: 45px;" alt="QR Validasi">
      </td>
      <td class="footer-text">
        Dokumen Laporan Pertanggungjawaban Bulanan PJLP ini diterbitkan & diverifikasi secara digital melalui <b>SIGAP PJLP — {{ strtoupper($user->unit ?: 'Badan Riset dan Inovasi Daerah') }}</b>.<br>
        Scan QR Code untuk memverifikasi keabsahan logbook & bukti dokumentasi pekerjaan harian.
      </td>
      <td style="text-align: right; width: 110px; font-size: 7.5px; color: #6b7280;">
        Periode: {{ $periode->bulan_tahun }}<br>
        Status: <b>VERIFIED</b>
      </td>
    </tr>
  </table>

  @foreach($logbookPages as $pageIndex => $chunk)
    <!-- HEADER RESMI -->
    <table class="header-table">
      <tr>
        <td>
          <div class="instansi-title">{{ strtoupper($user->unit ?: 'BADAN RISET DAN INOVASI DAERAH') }}</div>
          <div class="doc-title">LAPORAN EVIDENCE & LOGBOOK PEKERJAAN PJLP</div>
          <div class="doc-subtitle">Periode: {{ $namaBulanTahun }} ({{ $firstDate }} s.d. {{ $lastDate }})</div>
        </td>
      </tr>
    </table>

    <!-- KARTU IDENTITAS PJLP (Halaman Pertama) -->
    @if($pageIndex === 0)
      <table class="profile-card">
        <tr>
          <td style="width: 65px; text-align: center;">
            @if($fotoProfilBase64)
              <img src="{{ $fotoProfilBase64 }}" class="profile-photo" alt="Foto">
            @else
              <div class="profile-photo-placeholder">PAS FOTO</div>
            @endif
          </td>
          <td>
            <table class="info-table">
              <tr>
                <td class="info-label">Nama PJLP</td>
                <td style="width: 4px;">:</td>
                <td><b>{{ strtoupper($user->name) }}</b></td>
                <td class="info-label" style="width: 18%;">Jabatan / Tugas</td>
                <td style="width: 4px;">:</td>
                <td>{{ $profile->jabatan ?? 'Tenaga Kebersihan' }}</td>
              </tr>
              <tr>
                <td class="info-label">NIP / ID</td>
                <td>:</td>
                <td>{{ $user->nip ?: '-' }}</td>
                <td class="info-label">Unit Kerja</td>
                <td>:</td>
                <td>{{ $user->unit ?: 'BRIDA Kota Makassar' }}</td>
              </tr>
              <tr>
                <td class="info-label">NIK (KTP)</td>
                <td>:</td>
                <td>{{ $profile->nik ?? '-' }}</td>
                <td class="info-label">Kontak / HP</td>
                <td>:</td>
                <td>{{ $user->nomor_hp ?: ($profile->kontak_darurat ?? '-') }}</td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    @endif

    <!-- GRID EVIDENCE: 2 KOLOM (Kiri & Kanan), MAKS 3 BARIS PER LEMBAR -->
    <table class="grid-table">
      @foreach($chunk->chunk(2) as $row)
        <tr>
          @foreach($row as $item)
            <td class="grid-cell">
              <div class="evidence-meta">
                {{ $item->hari }}, {{ $item->tanggal->format('d/m/Y') }}
                <span class="status-badge">{{ strtoupper($item->status) }}</span>
              </div>
              
              @if($item->foto_base64)
                <img src="{{ $item->foto_base64 }}" class="evidence-img" alt="Foto Evidence">
              @else
                <div class="no-img-box">Evidence Tidak Tersedia</div>
              @endif

              <div class="evidence-desc">
                <b>Uraian:</b> {{ $item->deskripsi_pekerjaan ?: '-' }}
              </div>
            </td>
          @endforeach

          {{-- Kolom dummy penyeimbang jika item ganjil --}}
          @if($row->count() === 1)
            <td class="grid-cell" style="border: none; background: transparent;"></td>
          @endif
        </tr>
      @endforeach
    </table>

    {{-- Page break antar halaman evidence --}}
    @if(!$loop->last)
      <div class="page-break"></div>
    @endif
  @endforeach

</body>
</html>