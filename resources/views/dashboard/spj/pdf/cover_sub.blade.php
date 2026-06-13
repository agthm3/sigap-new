<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover Sub-Kegiatan</title>
    <style>
        @page {
            size: letter;
            margin: 1.5cm;
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            text-align: center; 
            color: #111827;
            margin: 0;
            padding: 0;
        }

        /* Konten Atas & Tengah */
        .container {
            padding-top: 80px;
        }
        .header { 
            font-size: 16px; 
            font-weight: bold; 
            letter-spacing: 1px; 
            text-transform: uppercase; 
        }
        .line { 
            border-bottom: 3px double #000000; 
            width: 70%; 
            margin: 15px auto 35px auto; 
        }
        .label-sub {
            font-size: 13px;
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .title { 
            font-size: 20px; 
            font-weight: 800; 
            line-height: 1.4;
            text-transform: uppercase; 
            color: #7a2222; /* Maroon Khas SIGAP */
            padding: 0 30px;
        }

        /* Blok Bawah (Kop Instansi & QR Code) - Sedikit dinaikkan agar tidak tabrakan dengan footer */
        .bottom-block {
            position: absolute;
            bottom: 140px; 
            width: 100%;
            left: 0;
            text-align: center;
        }
        
        .qr-container {
            margin-bottom: 12px;
        }
        .qr-img {
            width: 95px;
            height: 95px;
        }
        .qr-text {
            font-size: 8.5px;
            color: #4b5563;
            margin-top: 5px;
            font-style: italic;
            line-height: 1.3;
        }

        .agency { 
            font-size: 14px; 
            font-weight: bold; 
            line-height: 1.4;
            color: #000000;
            text-transform: uppercase;
            margin-top: 15px;
            letter-spacing: 0.5px;
        }

        /* PERBAIKAN: Dipatok langsung ke bottom body (0), terpisah dari bottom-block */
        .footer-watermark {
            position: absolute;
            bottom: 0px; 
            left: 0;
            width: 100%;
            border-top: 2px solid #7a2222; /* Garis Pembatas Maroon */
            padding-top: 8px;
            text-align: left;
        }
        .watermark-content {
            font-size: 8px;
            color: #4b5563;
            line-height: 1.4;
            margin-top: 2px;
        }
        .badge-verified {
            display: inline-block;
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
            font-size: 7px;
            font-weight: bold;
            padding: 1px 5px;
            border-radius: 4px;
            margin-bottom: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">Dokumen Surat Pertanggungjawaban (SPJ)</div>
        <div class="line"></div>
        
        <div class="label-sub">Sub-Kegiatan:</div>
        <div class="title">{{ $subKegiatan->nama_sub_kegiatan }}</div>
    </div>

    <div class="bottom-block">
        <div class="qr-container">
            <img src="data:image/svg+xml;base64,{{ $qrCodeCover }}" class="qr-img" alt="QR Verifikasi SPJ">
            <div class="qr-text">
                Scan QR ini untuk memverifikasi keaslian berkas & lampiran secara online<br>
                <span style="color: #9ca3af; font-size: 7.5px;">{{ $shareUrl }}</span>
            </div>
        </div>
        
        <div class="agency">
            BADAN RISET DAN INOVASI DAERAH<br>
            KOTA MAKASSAR<br>
            TAHUN 2026
        </div>
    </div>

    <div class="footer-watermark">
        <div class="badge-verified">✔ Terverifikasi Ekosistem Digital SIGAP SPJ</div>
        <div class="watermark-content">
            Dokumen ini merupakan bundel laporan resmi yang <strong>dihasilkan secara otomatis (Auto-Generated)</strong> melalui modul internal <strong>SIGAP SPJ</strong> milik Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar.<br>
            Kompilasi lembar halaman, nomor surat keputusan, daftar hadir elektronik, beserta dokumentasi harian di dalamnya telah tersinkronisasi secara aman dan sah oleh sistem.
        </div>
    </div>

</body>
</html>