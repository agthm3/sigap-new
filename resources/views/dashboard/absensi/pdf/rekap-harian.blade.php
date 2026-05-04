<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Harian Absensi</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 10px;
      color: #1f2937;
    }
    .header {
      text-align: center;
      margin-bottom: 14px;
    }
    .title {
      font-size: 16px;
      font-weight: bold;
      color: #7a2222;
      margin-bottom: 4px;
    }
    .subtitle {
      font-size: 11px;
      color: #6b7280;
    }
    .meta {
      margin: 10px 0 14px;
      font-size: 10px;
      color: #374151;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th {
      background: #f3f4f6;
      color: #4b5563;
      font-size: 9px;
      text-transform: uppercase;
      padding: 7px 6px;
      border: 1px solid #d1d5db;
      text-align: left;
    }
    td {
      border: 1px solid #d1d5db;
      padding: 6px;
      vertical-align: top;
    }
    .badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 9999px;
      font-size: 9px;
      border: 1px solid #d1d5db;
    }
    .badge-green {
      background: #ecfdf5;
      color: #047857;
      border-color: #a7f3d0;
    }
    .badge-red {
      background: #fef2f2;
      color: #b91c1c;
      border-color: #fecaca;
    }
    .photo {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    }
    .small {
      font-size: 8px;
      color: #6b7280;
      line-height: 1.2;
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="title">REKAP HARIAN ABSENSI SIGAP</div>
    <div class="subtitle">Badan Riset dan Inovasi Daerah Kota Makassar</div>
  </div>

  <div class="meta">
    Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
  </div>

  <table>
    <thead>
      <tr>
        <th style="width: 20px;">No</th>
        <th>Nama</th>
        <th>NIP</th>
        <th style="width: 85px;">Status</th>
        <th style="width: 110px;">Koordinat</th>
        <th style="width: 85px;">Absensi</th>
        <th>Keterangan</th>
        <th style="width: 150px;">Foto</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rekap as $i => $row)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $row->user->name ?? '-' }}</td>
          <td>{{ $row->user->nip ?? '-' }}</td>
          <td>
            <span class="badge {{ $row->is_outside_radius ? 'badge-red' : 'badge-green' }}">
              {{ $row->is_outside_radius ? 'DI LUAR RADIUS BALAIKOTA MAKASSAR' : 'DALAM RADIUS BALAIKOTA MAKASSAR' }}
            </span>
          </td>
          <td>{{ $row->latitude }}, {{ $row->longitude }}</td>
          <td>
            <strong>{{ $row->keterangan }}</strong><br>
            <span class="small">{{ \Carbon\Carbon::parse($row->absen_time)->format('H:i') }}</span>
          </td>
          <td>
            <div class="small">Absen Terverifikasi oleh SIGAP ABSENSI</div>
          </td>
          <td>
              @if(!empty($row->photo_base64))
                <img src="{{ $row->photo_base64 }}"
                    class="photo"
                    alt="foto absensi">
              @else
                <div class="small">No Photo</div>
              @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" style="text-align:center; padding:14px;">
            Belum ada data absensi.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>