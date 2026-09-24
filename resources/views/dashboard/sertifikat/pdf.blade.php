<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Sertifikat</title>

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
            margin-bottom: 8px;
        }
        .kop-table td { vertical-align: middle; }
        .center       { text-align: center; }
        .judul        { font-size: 13px; font-weight: bold; line-height: 1.4; }
        .subjudul     { font-size: 10.5px; margin-top: 2px; color: #333; }
        .line         { border-top: 1.5px solid #000; margin-top: 8px; margin-bottom: 12px; }

        /* ---- KETERANGAN INFO BOX ---- */
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-left: 3.5px solid #7a2222;
            padding: 7px 10px;
            margin-bottom: 12px;
            border-radius: 4px;
            font-size: 9px;
            line-height: 1.45;
            color: #1e293b;
        }
        .info-box strong {
            color: #7a2222;
        }
        .info-box a {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* ---- TABEL DATA ---- */
        table.data { width: 100%; border-collapse: collapse; }
        table.data th,
        table.data td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        table.data th {
            text-align: center;
            font-weight: bold;
            background: #f3f4f6;
            font-size: 9.5px;
        }

        /* Memastikan nomor sertifikat tetap 1 baris */
        .no-wrap-cell {
            white-space: nowrap !important;
            font-size: 9px;
            font-weight: bold;
            color: #111;
        }

        /* ---- PENANDATANGAN ---- */
        .ttd-pejabat-section {
            margin-top: 22px;
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
            margin-bottom: 2px;
            font-weight: bold;
        }
        .ttd-pejabat-box .ttd-jabatan {
            margin-bottom: 50px;
            font-weight: bold;
        }
        .ttd-pejabat-box .nama-pejabat {
            font-weight: bold;
            font-size: 10.5px;
            text-decoration: underline;
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
        .watermark-text {
            font-size: 8px;
            color: #6b7280;
            line-height: 1.5;
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
            <td class="center">
                <div class="judul">DAFTAR PENERIMA SERTIFIKAT</div>
                <div class="judul">{{ $kegiatan->nama_kegiatan }}</div>
                <div class="subjudul">Tanggal: {{ $kegiatan->tanggal }} | Tempat: {{ $kegiatan->tempat ?? 'Kota Makassar' }}</div>
            </td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- ===== PETUNJUK AKSES SERTIFIKAT ===== --}}
    <div class="info-box">
        <strong>Petunjuk Akses Sertifikat Digital:</strong><br>
        Untuk melihat dan mengunduh sertifikat digital resmi, silakan salin <strong>Nomor Sertifikat</strong> yang tertera pada tabel di bawah ini, kemudian masukkan pada menu verifikasi portal SIGAP di:  
        <a href="https://sigap.brida.makassarkota.go.id/sertifikat">https://sigap.brida.makassarkota.go.id/sertifikat</a>
    </div>

    {{-- ===== TABEL SERTIFIKAT ===== --}}
    <table class="data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="33%">Nomor Sertifikat</th>
                <th width="33%">Nama Penerima</th>
                <th width="20%">Instansi</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kegiatan->sertifikat as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td class="no-wrap-cell" align="center">{{ $item->nomor_sertifikat }}</td>
                    <td><strong>{{ $item->nama_penerima }}</strong></td>
                    <td>{{ $item->instansi ?? '-' }}</td>
                    <td align="center">{{ $item->status ?? 'Aktif' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" align="center">Belum ada penerima sertifikat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== PENANDATANGAN ===== --}}
    {{-- <div class="ttd-pejabat-section clearfix">
        <div class="ttd-pejabat-box">
            <div class="ttd-tempat">{{ $kegiatan->tempat ?? 'Makassar' }}, {{ $kegiatan->tanggal }}</div>
            <div class="ttd-jabatan">Kepala Badan Riset dan Inovasi Daerah<br>Kota Makassar</div>
            <div class="nama-pejabat">Haidil Adha, S.Sos., M.M.</div>
        </div>
    </div> --}}

    {{-- ===== FOOTER WATERMARK ===== --}}
    <div class="footer-watermark">
        <div class="sigap-badge">✔ SIGAP SERTIFIKAT — TERVERIFIKASI</div>
        <div class="watermark-text">
            Dokumen daftar sertifikat ini digenerate secara digital oleh SIGAP BRIDA Kota Makassar.
        </div>
    </div>

</body>
</html>