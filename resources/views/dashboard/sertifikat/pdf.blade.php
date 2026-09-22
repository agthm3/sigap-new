<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Sertifikat - SIGAP BRIDA</title>

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
        .logo-pemkot  { width: 70px; height: auto; }
        .logo-brida   { width: 140px; height: auto; }
        .center       { text-align: center; }
        .judul-instansi { font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        .judul        { font-size: 13px; font-weight: bold; line-height: 1.3; }
        .subjudul     { font-size: 10px; margin-top: 2px; color: #374151; }
        .line         { border-top: 2px solid #7a2222; margin-top: 8px; margin-bottom: 12px; }

        /* ---- TABEL SERTIFIKAT ---- */
        table.data { width: 100%; border-collapse: collapse; }
        table.data th,
        table.data td {
            border: 1px solid #333;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data th {
            text-align: center;
            font-weight: bold;
            background: #f3f4f6;
            color: #111;
        }
        .nomor-sertif {
            font-family: monospace;
            font-size: 9.5px;
            font-weight: bold;
            color: #7a2222;
        }

        /* ---- PENANDATANGAN ---- */
        .ttd-pejabat-section {
            margin-top: 25px;
            width: 100%;
        }
        .ttd-pejabat-box {
            display: inline-block;
            width: 250px;
            text-align: left;
            float: right;
            font-size: 10px;
        }
        .ttd-pejabat-box .ttd-tempat {
            margin-bottom: 3px;
        }
        .ttd-pejabat-box .ttd-jabatan {
            margin-bottom: 45px;
            font-weight: bold;
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
            color: #111;
        }
        .clearfix::after { content: ""; display: table; clear: both; }

        /* ---- FOOTER WATERMARK ---- */
        .footer-watermark {
            margin-top: 24px;
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
            line-height: 1.4;
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
            background: #fdf2f2;
            border: 1px solid #f87171;
            color: #991b1b;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 6px;
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
                <div class="judul-instansi">DAFTAR PENERBITAN SERTIFIKAT DIGITAL</div>
                <div class="judul">{{ $kegiatan->nama_kegiatan }}</div>
                <div class="subjudul">Tanggal: {{ $kegiatan->tanggal }} | Tempat: {{ $kegiatan->tempat ?? 'Kota Makassar' }}</div>
            </td>
            <td width="15%" align="right">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo-brida">
                @endif
            </td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- ===== TABEL SERTIFIKAT ===== --}}
    <table class="data">
        <thead>
            <tr>
                <th width="6%">No</th>
                <th width="28%">Nomor Sertifikat</th>
                <th width="32%">Nama Penerima</th>
                <th width="24%">Instansi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kegiatan->sertifikat as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td class="nomor-sertif">{{ $item->nomor_sertifikat }}</td>
                    <td><strong>{{ $item->nama_penerima }}</strong></td>
                    <td>{{ $item->instansi ?? '-' }}</td>
                    <td align="center">{{ $item->status ?? 'Aktif' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" align="center">Belum ada penerbitan sertifikat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    
    {{-- ===== FOOTER WATERMARK + QR VERIFIKASI ===== --}}
    <div class="footer-watermark">
        <table class="footer-inner">
            <tr>
                <td width="75%">
                    <div class="sigap-badge">✔ SIGAP SERTIFIKAT — TERVERIFIKASI</div>
                    <div class="watermark-text">
                        <strong>Daftar sertifikat ini digenerate secara resmi melalui modul SIGAP SERTIFIKAT.</strong><br>
                        Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar.<br>
                        Gunakan tautan atau scan QR untuk memverifikasi keabsahan data penerbitan.
                    </div>
                    <div class="watermark-text" style="margin-top:4px;font-size:7.5px;color:#9ca3af;">
                        {{ $verifikasiUrl }}
                    </div>
                </td>
                <td width="25%" align="right">
                    @if($qrVerifikasi)
                        <img src="data:image/png;base64,{{ $qrVerifikasi }}" class="qr-verifikasi" alt="QR Verifikasi">
                    @endif
                </td>
            </tr>
        </table>
    </div>

</body>
</html>