<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        @page { size: letter; margin: 1.2cm 1cm 1cm 1cm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; }
        .kop { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .kop td { vertical-align: middle; }
        .logo { width: 70px; height: 70px; object-fit: contain; }
        .center { text-align: center; }
        .judul { font-size: 14px; font-weight: bold; }
        .subjudul { font-size: 11px; margin-top: 2px; }
        .line { border-top: 1px solid #000; margin: 6px 0 10px 0; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #000; padding: 4px 5px; vertical-align: middle; }
        table.data th { text-align: center; font-weight: bold; }
        .ttd-img { width: 80px; height: 35px; object-fit: contain; }
        .muted { color: #000; }
    </style>
</head>
<body>
    <table class="kop">
        <tr>
            <td style="width: 15%; text-align:left;">
                @if($logoPemkot)
                    <img src="{{ $logoPemkot }}" class="logo" alt="Pemkot Makassar">
                @endif
            </td>
            <td style="width: 70%; text-align:center;">
                <div class="judul">{{ $kegiatan->nama_kegiatan }}</div>
                <div class="subjudul">{{ $kegiatan->hari_tanggal }}</div>
                <div class="subjudul">{{ $kegiatan->tempat }} • {{ $kegiatan->waktu }}</div>
            </td>
            <td style="width: 15%; text-align:right;">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo" alt="BRIDA">
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 18%;">Nama</th>
                <th style="width: 20%;">Instansi</th>
                <th style="width: 8%;">Gender</th>
                <th style="width: 16%;">No. HP</th>
                <th style="width: 18%;">Email</th>
                <th style="width: 16%;">TTD</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kegiatan->peserta as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->instansi }}</td>
                    <td class="center">{{ $item->gender }}</td>
                    <td>{{ $item->no_hp }}</td>
                    <td>{{ $item->email ?: '-' }}</td>
                    <td class="center">
                        @if($item->ttd_path && file_exists(storage_path('app/public/' . $item->ttd_path)))
                            <img src="data:image/{{ pathinfo($item->ttd_path, PATHINFO_EXTENSION) === 'jpg' ? 'jpeg' : 'png' }};base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $item->ttd_path))) }}" class="ttd-img" alt="TTD">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">Belum ada peserta.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>