<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cover & Data Diri PJLP</title>
  <style>
    @page {
      margin: 20mm 15mm 20mm 15mm;
      size: letter portrait;
    }
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      color: #111827;
      line-height: 1.35;
      font-size: 10px;
    }
    .header-table {
      width: 100%;
      border-bottom: 2.5px solid #7a2222;
      padding-bottom: 8px;
      margin-bottom: 20px;
      text-align: center;
    }
    .instansi-title {
      font-size: 14px;
      font-weight: bold;
      color: #7a2222;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }
    .doc-main-title {
      font-size: 12px;
      font-weight: bold;
      margin-top: 3px;
      color: #1f2937;
    }
    .doc-periode {
      font-size: 10px;
      color: #4b5563;
      margin-top: 2px;
    }

    /* Profil Card */
    .section-title {
      font-size: 10.5px;
      font-weight: bold;
      color: #7a2222;
      border-bottom: 1.5px solid #e5e7eb;
      padding-bottom: 4px;
      margin-bottom: 12px;
      text-transform: uppercase;
    }
    .profile-card {
      width: 100%;
      border: 1px solid #d1d5db;
      background-color: #fcfcfc;
      border-radius: 6px;
      margin-bottom: 20px;
    }
    .profile-card td {
      padding: 12px;
      vertical-align: top;
    }
    .profile-photo {
      width: 90px;
      height: 115px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid #9ca3af;
    }
    .profile-photo-placeholder {
      width: 90px;
      height: 115px;
      background-color: #e5e7eb;
      border: 1px solid #9ca3af;
      border-radius: 4px;
      text-align: center;
      line-height: 115px;
      color: #6b7280;
      font-size: 9px;
      font-weight: bold;
    }
    .data-table {
      width: 100%;
      font-size: 9.5px;
    }
    .data-table td {
      padding: 3px 4px !important;
      border: none !important;
    }
    .label {
      width: 28%;
      font-weight: bold;
      color: #374151;
    }

    /* Summary Ringkasan */
    .summary-box {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .summary-box th, .summary-box td {
      border: 1px solid #d1d5db;
      padding: 6px 8px;
      text-align: center;
      font-size: 9px;
    }
    .summary-box th {
      background-color: #f3f4f6;
      font-weight: bold;
      color: #374151;
    }

    /* Footer Verifikasi */
    .footer-table {
      position: fixed;
      bottom: 0px;
      left: 0px;
      right: 0px;
      width: 100%;
      border-top: 1px solid #e5e7eb;
      padding-top: 6px;
    }
    .footer-table td {
      vertical-align: middle;
      border: none;
    }
    .footer-text {
      font-size: 8px;
      color: #4b5563;
      line-height: 1.2;
    }
  </style>
</head>
<body>

  <!-- KOP SURAT / HEADER RESMI -->
  <table class="header-table">
    <tr>
      <td>
        <div class="instansi-title">{{ strtoupper($user->unit ?: 'BADAN RISET DAN INOVASI DAERAH') }}</div>
        <div class="doc-main-title">LAPORAN PERTANGGUNGJAWABAN BULANAN PJLP</div>
        <div class="doc-periode">Periode Kerja: {{ $namaBulanTahun }} ({{ $firstDate }} s.d. {{ $lastDate }})</div>
      </td>
    </tr>
  </table>

  <!-- BAGIAN DATA DIRI DASAR PEGAWAI -->
  <div class="section-title">I. Identitas Diri Pegawai (PJLP)</div>
  <table class="profile-card">
    <tr>
      <td style="width: 100px; text-align: center;">
        @if($fotoProfilBase64)
          <img src="{{ $fotoProfilBase64 }}" class="profile-photo" alt="Pas Foto">
        @else
          <div class="profile-photo-placeholder">PAS FOTO</div>
        @endif
      </td>
      <td>
        <table class="data-table">
          <tr>
            <td class="label">Nama Lengkap</td>
            <td style="width: 5px;">:</td>
            <td><b>{{ strtoupper($user->name) }}</b></td>
          </tr>
          <tr>
            <td class="label">NIP / ID PJLP</td>
            <td>:</td>
            <td>{{ $user->nip ?: '-' }}</td>
          </tr>
          <tr>
            <td class="label">NIK (KTP)</td>
            <td>:</td>
            <td>{{ $profile->nik ?? '-' }}</td>
          </tr>
          <tr>
            <td class="label">Jabatan / Tugas Pokok</td>
            <td>:</td>
            <td>{{ $profile->jabatan ?? 'Tenaga Kebersihan Lingkungan Kantor' }}</td>
          </tr>
          <tr>
            <td class="label">Unit Kerja / OPD</td>
            <td>:</td>
            <td>{{ $user->unit ?: 'Badan Riset dan Inovasi Daerah (BRIDA)' }}</td>
          </tr>
          <tr>
            <td class="label">Kontak / No. WhatsApp</td>
            <td>:</td>
            <td>{{ $user->nomor_hp ?: ($profile->kontak_darurat ?? '-') }}</td>
          </tr>
          <tr>
            <td class="label">Email Terdaftar</td>
            <td>:</td>
            <td>{{ $user->email }}</td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- BAGIAN RINGKASAN CAPAIAN KERJA -->
  <div class="section-title">II. Rekapitulasi Capaian Logbook Bulanan</div>
  <table class="summary-box">
    <thead>
      <tr>
        <th>Total Hari Kerja</th>
        <th>Logbook Terisi</th>
        <th>Disetujui / Terverifikasi</th>
        <th>Dokumen Daftar Gaji</th>
        <th>Status Laporan</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><b>{{ $totalHariKerja }} Hari</b></td>
        <td>{{ $totalHariKerja }} Hari</td>
        <td style="color: #065f46; font-weight: bold;">{{ $totalTerverifikasi }} Hari</td>
        <td style="color: #065f46; font-weight: bold;">
          {{ $periode->file_daftar_gaji ? 'Terlampir (Hal. Berikutnya)' : 'Belum Terlampir' }}
        </td>
        <td style="color: #7a2222; font-weight: bold;">LENGKAP & FINAL</td>
      </tr>
    </tbody>
  </table>

  <p style="font-size: 8.5px; color: #4b5563; margin-top: 15px;">
    <i>* Dokumen resmi Daftar Gaji disisipkan pada halaman berikutnya setelah identitas diri ini, diikuti dengan lampiran seluruh bukti/evidence foto pekerjaan harian.</i>
  </p>

  <!-- FOOTER COVER -->
  <table class="footer-table">
    <tr>
      <td style="width: 55px;">
        <img src="{{ $qrCodeBase64 }}" style="width: 45px; height: 45px;" alt="QR Validasi">
      </td>
      <td class="footer-text">
        Dokumen Laporan Pertanggungjawaban Bulanan PJLP diterbitkan secara sah melalui <b>SIGAP PJLP — {{ strtoupper($user->unit ?: 'BRIDA KOTA MAKASSAR') }}</b>.<br>
        Scan QR Code untuk memverifikasi keabsahan data logbook & dokumen pertanggungjawaban.
      </td>
      <td style="text-align: right; width: 120px; font-size: 8px; color: #6b7280;">
        Halaman 1 (Cover)<br>
        Status: <b>VERIFIED</b>
      </td>
    </tr>
  </table>

</body>
</html>