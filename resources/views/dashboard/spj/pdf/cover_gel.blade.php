<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover Gelombang</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; text-align: center; padding-top: 200px; background-color: #ffffff; }
        .label { font-size: 14px; margin-bottom: 10px; color: #555; text-transform: uppercase; letter-spacing: 2px;}
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; color: #7a2222; margin-bottom: 30px;}
        .info-box { border: 2px solid #000; width: 70%; margin: 0 auto; padding: 20px; text-align: left; background-color: #fafafa;}
        .info-row { margin-bottom: 10px; font-size: 14px; line-height: 1.5; }
        .info-label { font-weight: bold; display: inline-block; width: 100px; }
        .agency { font-size: 14px; font-weight: bold; margin-top: 60px; color: #333; }
    </style>
</head>
<body>
    <div class="label">RINCIAN PELAKSANAAN</div>
    <div class="title">{{ $gel->nama_gelombang }}</div>
    
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Kegiatan</span> : {{ $keg->nama_kegiatan }}
        </div>
        <div class="info-row">
            <span class="info-label">Hari/Tanggal</span> : {{ \Carbon\Carbon::parse($gel->tanggal)->translatedFormat('l, d F Y') }}
        </div>
        <div class="info-row">
            <span class="info-label">Waktu</span> : {{ $gel->waktu }}
        </div>
        <div class="info-row">
            <span class="info-label">Tempat</span> : {{ $gel->tempat }}
        </div>
    </div>

    <div class="agency">
        BADAN RISET DAN INOVASI DAERAH<br>
        KOTA MAKASSAR
    </div>
</body>
</html>