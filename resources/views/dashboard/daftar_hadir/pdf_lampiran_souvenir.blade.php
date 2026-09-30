<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Penerima Souvenir</title>
    <style>
        @page {
            size: letter;
            margin: 1.2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .kop-table td { vertical-align: middle; }
        .logo-pemkot  { width: 70px;  height: auto; }
        .logo-brida   { width: 150px; height: auto; }
        .center       { text-align: center; }
        .judul        { font-size: 13px; font-weight: bold; line-height: 1.4; text-transform: uppercase; }
        .subjudul     { font-size: 10.5px; margin-top: 2px; }
        .line         { border-top: 1px solid #000; margin-top: 8px; margin-bottom: 12px; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        table.data th {
            text-align: center;
            font-weight: bold;
            background: #f3f4f6;
        }
        .ttd-img { width: 75px; height: 32px; object-fit: contain; }

        .status-dapat {
            font-weight: bold;
            color: #065f46;
        }
        .status-tidak {
            font-weight: bold;
            color: #991b1b;
        }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td width="15%" align="left">
                @if($logoPemkot)
                    <img src="{{ $logoPemkot }}" class="logo-pemkot">
                @endif
            </td>
            <td width="70%" class="center">
                <div class="judul">DAFTAR PENERIMA SOUVENIR</div>
                <div class="subjudul" style="font-weight: bold; margin-top: 3px;">{{ $kegiatan->nama_kegiatan }}</div>
                <div class="subjudul">{{ $kegiatan->hari_tanggal }} • {{ $kegiatan->tempat }}</div>
            </td>
            <td width="15%" align="right">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo-brida">
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="23%">NAMA</th>
                <th width="23%">INSTANSI</th>
                <th width="20%">EMAIL</th>
                <th width="14%">TTD</th>
                <th width="15%">KONFIRMASI PENERIMAAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kegiatan->peserta as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->instansi }}</td>
                    <td>{{ $item->email ?: '-' }}</td>
                    <td align="center">
                        @if($item->ttd_path && file_exists(storage_path('app/public/' . $item->ttd_path)))
                            @php
                                $ttdPath = storage_path('app/public/' . $item->ttd_path);
                                $type    = pathinfo($ttdPath, PATHINFO_EXTENSION);
                                $data    = file_get_contents($ttdPath);
                                $base64  = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            @endphp
                            <img src="{{ $base64 }}" class="ttd-img">
                        @else
                            -
                        @endif
                    </td>
                    <td align="center">
                        @if($item->terima_souvenir == 1)
                            <span class="status-dapat">Dapat</span>
                        @else
                            <span class="status-tidak">Tidak</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" align="center">Belum ada peserta terdaftar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>