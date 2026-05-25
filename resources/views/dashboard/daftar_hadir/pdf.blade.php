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

        /* ---- KOP ---- */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .kop-table td { vertical-align: middle; }
        .logo-pemkot  { width: 70px;  height: auto; }
        .logo-brida   { width: 150px; height: auto; }
        .center       { text-align: center; }
        .judul        { font-size: 14px; font-weight: bold; line-height: 1.4; }
        .subjudul     { font-size: 11px; margin-top: 2px; }
        .line         { border-top: 1px solid #000; margin-top: 8px; margin-bottom: 10px; }

        /* ---- TABEL PESERTA ---- */
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
        .ttd-img { width: 80px; height: 35px; object-fit: contain; }

        /* ---- PENANDATANGAN ---- */
        .ttd-pejabat-section {
            margin-top: 22px;
            width: 100%;
        }
        .ttd-pejabat-box {
            display: inline-block;
            width: 220px;
            text-align: left;
            float: right;
            font-size: 10px;
        }
        .ttd-pejabat-box .ttd-tempat {
            margin-bottom: 2px;
            font-weight: bold;
        }
        .ttd-pejabat-box .ttd-jabatan {
            margin-bottom: 4px;
            font-weight: bold;
        }
        .ttd-pejabat-img {
            width: 140px;
            height: 60px;
            object-fit: contain;
            display: block;
            margin: 0;
        }
        .ttd-pejabat-box .garis-ttd {
            border-bottom: 1px solid #000;
            margin: 0 0 4px 0;
            height: 60px;
            width: 140px;
        }
        .ttd-pejabat-box .nama-pejabat {
            font-weight: bold;
            font-size: 10.5px;
            text-decoration: underline;
            color: #000;
        }
        .ttd-pejabat-box .info-pejabat {
            font-size: 9.5px;
            margin-top: 1px;
            font-weight: bold;
            color: #000;
        }
        .clearfix::after { content: ""; display: table; clear: both; }

        /* ---- FOOTER WATERMARK ---- */
        .footer-watermark {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
            width: 100%;
        }
        .footer-inner {
            width: 100%;
        }
        .footer-inner td {
            vertical-align: middle;
        }
        .watermark-text {
            font-size: 8px;
            color: #6b7280;
            line-height: 1.5;
        }
        .watermark-text strong {
            color: #374151;
            font-size: 8.5px;
        }
        .qr-verifikasi {
            width: 60px;
            height: 60px;
        }
        .sigap-badge {
            display: inline-block;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 8px;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    {{-- ===== KOP ===== --}}
    <table class="kop-table">
        <tr>
            <td width="15%" align="left">
                @if($logoPemkot)
                    <img src="{{ $logoPemkot }}" class="logo-pemkot">
                @endif
            </td>
            <td width="70%" class="center">
                <div class="judul">{{ $kegiatan->nama_kegiatan }}</div>
                <div class="subjudul">{{ $kegiatan->hari_tanggal }}</div>
                <div class="subjudul">{{ $kegiatan->tempat }}</div>
                <div class="subjudul">{{ $kegiatan->waktu }}</div>
            </td>
            <td width="15%" align="right">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo-brida">
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- ===== TABEL PESERTA ===== --}}
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
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->instansi }}</td>
                    <td align="center">{{ $item->gender }}</td>
                    <td>{{ $item->no_hp }}</td>
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
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center">Belum ada peserta.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== PENANDATANGAN (muncul hanya jika ada data) ===== --}}
    @if($kegiatan->penandatangan)
        @php $pejabat = $kegiatan->penandatangan; @endphp
        <div class="ttd-pejabat-section clearfix">
            <div class="ttd-pejabat-box">

                {{-- 1. Tempat & Tanggal --}}
                @if($pejabat->tempat_ttd || $pejabat->tanggal_ttd)
                    <div class="ttd-tempat">
                        {{ $pejabat->tempat_ttd }}{{ ($pejabat->tempat_ttd && $pejabat->tanggal_ttd) ? ', ' : '' }}{{ $pejabat->tanggal_ttd }}
                    </div>
                @endif

                {{-- 2. Jabatan --}}
                @if($pejabat->jabatan)
                    <div class="ttd-jabatan">{{ $pejabat->jabatan }}</div>
                @endif

                {{-- 3. Area TTD --}}
                @if($pejabat->ttd_path && file_exists(storage_path('app/public/' . $pejabat->ttd_path)))
                    @php
                        $pejabatTtdPath = storage_path('app/public/' . $pejabat->ttd_path);
                        $pejabatTtdExt  = pathinfo($pejabatTtdPath, PATHINFO_EXTENSION);
                        $pejabatTtdData = file_get_contents($pejabatTtdPath);
                        $pejabatTtdB64  = 'data:image/' . $pejabatTtdExt . ';base64,' . base64_encode($pejabatTtdData);
                    @endphp
                    <img src="{{ $pejabatTtdB64 }}" class="ttd-pejabat-img">
                @else

                @endif

                {{-- 4. Nama --}}
                <div class="nama-pejabat">{{ $pejabat->nama_lengkap }}</div>

                {{-- 5. Pangkat --}}
                @if($pejabat->pangkat)
                    <div class="info-pejabat">
                        Pangkat : {{ $pejabat->pangkat }}{{ $pejabat->golongan ? ' / ' . $pejabat->golongan : '' }}
                    </div>
                @endif

                {{-- 6. NIP --}}
                @if($pejabat->nip)
                    <div class="info-pejabat">NIP. {{ $pejabat->nip }}</div>
                @endif

            </div>
        </div>
    @endif

    {{-- ===== FOOTER: WATERMARK + QR VERIFIKASI ===== --}}
    <div class="footer-watermark">
        <table class="footer-inner">
            <tr>
                <td width="75%">
                    <div class="sigap-badge">✔ SIGAP — TERVERIFIKASI</div>
                    <div class="watermark-text">
                        <strong>Daftar hadir ini digenerate dan diverifikasi secara digital oleh SIGAP.</strong><br>
                        Sistem Informasi dan Pengelolaan Administrasi – BRIDA Kota Makassar.<br>
                        Scan QR di samping untuk memverifikasi keaslian dokumen ini secara online.
                    </div>
                    <div class="watermark-text" style="margin-top:4px;font-size:7.5px;color:#9ca3af;">
                        {{ $verifikasiUrl }}
                    </div>
                </td>
                <td width="25%" align="right">
                    @if($qrVerifikasi)
                        <img src="data:image/png;base64,{{ $qrVerifikasi }}"
                             class="qr-verifikasi" alt="QR Verifikasi">
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>