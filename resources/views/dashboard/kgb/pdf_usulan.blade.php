<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Usulan Kenaikan Gaji Berkala</title>
  <style>
    body { font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.4; color: #000; margin: 20px 30px; }
    .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 20px; }
    .header h3, .header h2, .header p { margin: 2px 0; }
    .header h2 { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
    .header p { font-size: 10pt; }
    .table-data { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; }
    .table-data th, .table-data td { border: 1px solid #000; padding: 6px 8px; font-size: 11pt; }
    .table-data th { background-color: #f2f2f2; text-align: center; }
    .signature { margin-top: 40px; float: right; width: 45%; text-align: center; }
    .clear { clear: both; }
  </style>
</head>
<body>

  <div class="header">
    <h2>PEMERINTAH KOTA MAKASSAR</h2>
    <h3>BADAN RISET DAN INOVASI DAERAH (BRIDA)</h3>
    <p>Jl. Jenderal Ahmad Yani No. 2, Baru, Kec. Ujung Pandang, Kota Makassar</p>
  </div>

  <table style="width: 100%; margin-bottom: 20px;">
    <tr>
      <td style="width: 15%;">Nomor</td>
      <td style="width: 3%;">:</td>
      <td style="width: 47%;">800 / &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; / BRIDA / {{ now()->year }}</td>
      <td style="width: 35%; text-align: right;">Makassar, {{ now()->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
      <td>Lampiran</td>
      <td>:</td>
      <td>1 (Satu) Berkas</td>
      <td>Kepada Yth,</td>
    </tr>
    <tr>
      <td>Perihal</td>
      <td>:</td>
      <td><b>Pemberitahuan Kenaikan Gaji Berkala</b></td>
      <td><b>Kepala BKPSDM Kota Makassar</b><br>di - Makassar</td>
    </tr>
  </table>

  <p style="text-align: justify; text-indent: 30px;">
    Dengan ini diberitahukan bahwa berhubung telah dipenuhinya masa kerja dan syarat-syarat lainnya kepada Pegawai Negeri Sipil / PPPK tersebut di bawah ini:
  </p>

  <table style="width: 100%; margin-left: 20px; margin-bottom: 15px;">
    <tr>
      <td style="width: 30%;">1. Nama Pegawai</td>
      <td style="width: 3%;">:</td>
      <td><b>{{ $riwayat->user->name }}</b></td>
    </tr>
    <tr>
      <td>2. NIP</td>
      <td>:</td>
      <td>{{ $riwayat->user->nip }}</td>
    </tr>
    <tr>
      <td>3. Pangkat / Golongan</td>
      <td>:</td>
      <td>{{ $riwayat->pangkat_golongan }} ({{ strtoupper($riwayat->jenis_pegawai) }})</td>
    </tr>
    <tr>
      <td>4. Jabatan / Unit Kerja</td>
      <td>:</td>
      <td>{{ $riwayat->jabatan ?? '-' }} / {{ $riwayat->user->unit ?? 'BRIDA Kota Makassar' }}</td>
    </tr>
  </table>

  <p>Diberitahukan penyesuaian kenaikan gaji berkala dengan rincian sebagai berikut:</p>

  <table class="table-data">
    <thead>
      <tr>
        <th style="width: 30%;">Uraian</th>
        <th style="width: 35%;">Gaji / Ketetapan Lama</th>
        <th style="width: 35%;">Gaji / Ketetapan Baru</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><b>Gaji Pokok</b></td>
        <td>Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }}</td>
        <td><b>Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</b></td>
      </tr>
      <tr>
        <td><b>Masa Kerja Golongan</b></td>
        <td>{{ $riwayat->mkg_tahun_lama }} Tahun {{ $riwayat->mkg_bulan_lama }} Bulan</td>
        <td><b>{{ $riwayat->mkg_tahun_baru }} Tahun {{ $riwayat->mkg_bulan_baru }} Bulan</b></td>
      </tr>
      <tr>
        <td><b>Surat Keputusan (SK)</b></td>
        <td>Nomor: {{ $riwayat->nomor_sk_lama }}<br>Tgl: {{ $riwayat->tanggal_sk_lama->translatedFormat('d F Y') }}</td>
        <td>Diusulkan</td>
      </tr>
      <tr>
        <td><b>Terhitung Mulai Tanggal (TMT)</b></td>
        <td>{{ $riwayat->tmt_lama->translatedFormat('d F Y') }}</td>
        <td><b>{{ $riwayat->tmt_baru->translatedFormat('d F Y') }}</b></td>
      </tr>
    </tbody>
  </table>

  <p style="text-align: justify; text-indent: 30px;">
    Diharapkan agar kepada pegawai yang bersangkutan dapat dibayarkan penghasilannya berdasarkan gaji pokok baru terhitung mulai tanggal <b>{{ $riwayat->tmt_baru->translatedFormat('d F Y') }}</b>.
  </p>

  <div class="signature">
    <p>Kepala Badan Riset dan Inovasi Daerah<br>Kota Makassar,</p>
    <br><br><br><br>
    <p><b><u>( Nama Kepala Badan )</u></b><br>Pangkat / Golongan<br>NIP. .........................................</p>
  </div>
  <div class="clear"></div>

</body>
</html>