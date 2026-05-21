<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir</title>

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

        .kop-table td {
            vertical-align: middle;
        }

        .logo-pemkot {
            width: 70px;
            height: auto;
        }

        .logo-brida {
            width: 150px;
            height: auto;
        }

        .center {
            text-align: center;
        }

        .judul {
            font-size: 14px;
            font-weight: bold;
            line-height: 1.4;
        }

        .subjudul {
            font-size: 11px;
            margin-top: 2px;
        }

        .line {
            border-top: 1px solid #000;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

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

        .ttd-img {
            width: 80px;
            height: 35px;
            object-fit: contain;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <table class="kop-table">
        <tr>

            {{-- LOGO PEMKOT --}}
            <td width="15%" align="left">
                @if($logoPemkot)
                    <img src="{{ $logoPemkot }}" class="logo-pemkot">
                @endif
            </td>

            {{-- JUDUL --}}
            <td width="70%" class="center">

                <div class="judul">
                    {{ $kegiatan->nama_kegiatan }}
                </div>

                <div class="subjudul">
                    {{ $kegiatan->hari_tanggal }}
                </div>

                <div class="subjudul">
                    {{ $kegiatan->tempat }}
                </div>

                <div class="subjudul">
                    {{ $kegiatan->waktu }}
                </div>

            </td>

            {{-- LOGO BRIDA --}}
            <td width="15%" align="right">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo-brida">
                @endif
            </td>

        </tr>
    </table>

    <div class="line"></div>

    {{-- TABEL --}}
    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="22%">Nama</th>
                <th width="22%">Instansi</th>
                <th width="8%">Gender</th>
                <th width="16%">No. HP</th>
                <th width="17%">Email</th>
                <th width="10%">TTD</th>
            </tr>
        </thead>

        <tbody>

            @forelse($kegiatan->peserta as $item)

                <tr>

                    <td align="center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $item->nama }}
                    </td>

                    <td>
                        {{ $item->instansi }}
                    </td>

                    <td align="center">
                        {{ $item->gender }}
                    </td>

                    <td>
                        {{ $item->no_hp }}
                    </td>

                    <td>
                        {{ $item->email ?: '-' }}
                    </td>

                    <td align="center">

                        @if(
                            $item->ttd_path &&
                            file_exists(storage_path('app/public/' . $item->ttd_path))
                        )

                            @php
                                $ttdPath = storage_path('app/public/' . $item->ttd_path);

                                $type = pathinfo($ttdPath, PATHINFO_EXTENSION);

                                $data = file_get_contents($ttdPath);

                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                            @endphp

                            <img src="{{ $base64 }}" class="ttd-img">

                        @else
                            -
                        @endif

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" align="center">
                        Belum ada peserta.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

</body>
</html>