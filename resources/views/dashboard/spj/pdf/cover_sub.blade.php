<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover Sub-Kegiatan</title>
    <style>
        @page {
            size: letter;
            margin: 1.2cm 1.5cm;
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
            padding-top: 30px;
        }
        .header { 
            font-size: 15px; 
            font-weight: bold; 
            letter-spacing: 1px; 
            text-transform: uppercase; 
        }
        .line { 
            border-bottom: 3px double #000000; 
            width: 70%; 
            margin: 12px auto 25px auto; 
        }
        .label-sub {
            font-size: 12px;
            font-weight: bold;
            color: #4b5563;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .title { 
            /* Ukuran font disesuaikan agar teks 500 karakter muat rapi dalam 1 lembar */
            font-size: 15px; 
            font-weight: 800; 
            line-height: 1.35;
            text-transform: uppercase; 
            color: #7a2222; /* Maroon Khas SIGAP */
            padding: 0 15px;
            word-wrap: break-word;
        }

        /* Blok Bawah (Kop Instansi & QR Code) */
        .bottom-block {
            position: absolute;
            bottom: 115px; 
            width: 100%;
            left: 0;
            text-align: center;
        }
        
        .qr-container {
            margin-bottom: 8px;
        }
        .qr-img {
            width: 85px;
            height: 85px;
        }
        .qr-text {
            font-size: 8px;
            color: #4b5563;
            margin-top: 4px;
            font-style: italic;
            line-height: 1.25;
        }

        .agency { 
            font-size: 13px; 
            font-weight: bold; 
            line-height: 1.35;
            color: #000000;
            text-transform: uppercase;
            margin-top: 10px;
            letter-spacing: 0.5px;
        }

        /* Footer Watermark */
        .footer-watermark {
            position: absolute;
            bottom: 0px; 
            left: 0;
            width: 100%;
            border-top: 2px solid #7a2222;
            padding-top: 6px;
            text-align: left;
        }
        .watermark-content {
            font-size: 7.5px;
            color: #4b5563;
            line-height: 1.35;
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
            margin-bottom: 2px;
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