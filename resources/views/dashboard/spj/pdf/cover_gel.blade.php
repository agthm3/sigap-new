<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover Gelombang</title>
    <style>
        @page {
            size: letter;
            margin: 1.5cm;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            text-align: center; 
            padding-top: 90px; 
            background-color: #ffffff; 
            margin: 0;
        }
        .label { 
            font-size: 13px; 
            margin-bottom: 8px; 
            color: #4b5563; 
            text-transform: uppercase; 
            letter-spacing: 2px;
            font-weight: bold;
        }
        .title { 
            font-size: 20px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #7a2222; 
            margin-bottom: 25px;
            line-height: 1.3;
        }
        .info-box { 
            border: 2px solid #111827; 
            width: 85%; 
            margin: 0 auto; 
            padding: 15px 20px; 
            background-color: #fafafa;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }
        table.info-table td {
            padding: 5px 0;
            vertical-align: top;
            line-height: 1.4;
        }
        .col-label {
            width: 110px;
            font-weight: bold;
            color: #111827;
        }
        .col-colon {
            width: 15px;
            font-weight: bold;
            text-align: center;
        }
        .col-val {
            color: #1f2937;
            word-wrap: break-word;
        }
        .agency { 
            font-size: 13px; 
            font-weight: bold; 
            margin-top: 50px; 
            color: #111827; 
            line-height: 1.4;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="label">RINCIAN PELAKSANAAN</div>
    <div class="title">{{ $gel->nama_gelombang }}</div>
    
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td class="col-label">Kegiatan</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ $keg->nama_kegiatan }}</td>
            </tr>
            <tr>
                <td class="col-label">Hari/Tanggal</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ \Carbon\Carbon::parse($gel->tanggal)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <td class="col-label">Waktu</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ $gel->waktu }}</td>
            </tr>
            <tr>
                <td class="col-label">Tempat</td>
                <td class="col-colon">:</td>
                <td class="col-val">{{ $gel->tempat }}</td>
            </tr>
        </table>
    </div>

    <div class="agency">
        BADAN RISET DAN INOVASI DAERAH<br>
        KOTA MAKASSAR
    </div>
</body>
</html>