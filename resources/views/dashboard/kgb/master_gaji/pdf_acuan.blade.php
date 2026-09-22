<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tabel Acuan Gaji Pokok ASN & PPPK</title>
  <style>
    @page {
      margin: 1.5cm 1.5cm 1.5cm 1.5cm;
    }
    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 10pt;
      color: #1f2937;
      line-height: 1.3;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #800000;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }
    .header h2 {
      margin: 0;
      font-size: 14pt;
      color: #800000;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .header p {
      margin: 3px 0 0;
      font-size: 9pt;
      color: #4b5563;
    }
    .meta-filter {
      margin-bottom: 12px;
      font-size: 8.5pt;
      background-color: #f9fafb;
      padding: 6px 10px;
      border: 1px solid #e5e7eb;
      border-radius: 4px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }
    th {
      background-color: #800000;
      color: #ffffff;
      font-size: 8.5pt;
      font-weight: bold;
      text-transform: uppercase;
      padding: 6px 8px;
      border: 1px solid #6b0000;
      text-align: center;
    }
    td {
      padding: 5px 8px;
      font-size: 8.5pt;
      border: 1px solid #d1d5db;
    }
    tr:nth-child(even) {
      background-color: #f9fafb;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .font-bold { font-weight: bold; }
    .badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 3px;
      font-size: 7.5pt;
      font-weight: bold;
      text-transform: uppercase;
    }
    .badge-pns {
      background-color: #eff6ff;
      color: #1d4ed8;
      border: 1px solid #bfdbfe;
    }
    .badge-pppk {
      background-color: #faf5ff;
      color: #7e22ce;
      border: 1px solid #e9d5ff;
    }
    .footer {
      margin-top: 20px;
      font-size: 8pt;
      color: #6b7280;
      text-align: right;
    }
  </style>
</head>
<body>

  <div class="header">
    <h2>Daftar Tabel Acuan Gaji Pokok Pegawai ASN & PPPK</h2>
    <p>Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar • Dasar: PP No. 5 Tahun 2024 & Perpres No. 11 Tahun 2024</p>
  </div>

  <div class="meta-filter">
    <strong>Filter Data:</strong> Jenis: <u>{{ $filterJenis }}</u> | Kategori Golongan: <u>{{ $filterGolongan }}</u> | Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WITA
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 5%;">No.</th>
        <th style="width: 12%;">Jenis Pegawai</th>
        <th style="width: 15%;">Golongan / Ruang</th>
        <th style="width: 18%;">Masa Kerja Golongan (MKG)</th>
        <th style="width: 25%;">Nominal Gaji Pokok</th>
        <th style="width: 25%;">Dasar Regulasi</th>
      </tr>
    </thead>
    <tbody>
      @php $no = 1; @endphp
      @forelse($gajis as $row)
        <tr>
          <td class="text-center">{{ $no++ }}</td>
          <td class="text-center">
            <span class="badge {{ $row->jenis_pegawai === 'pns' ? 'badge-pns' : 'badge-pppk' }}">
              {{ strtoupper($row->jenis_pegawai) }}
            </span>
          </td>
          <td class="text-center font-bold">{{ $row->golongan }}</td>
          <td class="text-center">{{ $row->masa_kerja }} Tahun</td>
          <td class="text-right font-bold" style="color: #800000;">
            Rp {{ number_format($row->nominal, 0, ',', '.') }}
          </td>
          <td class="text-center">{{ $row->regulasi }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="text-center" style="padding: 20px; color: #9ca3af;">
            Tidak ada data acuan gaji pokok yang sesuai filter.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Dokumen dicetak otomatis melalui Modul SIGAP KGB BRIDA Kota Makassar
  </div>

</body>
</html>