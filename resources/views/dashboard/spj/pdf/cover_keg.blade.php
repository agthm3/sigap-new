<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover Kegiatan</title>
    <style>
        @page {
            size: letter;
            margin: 1.5cm;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            text-align: center; 
            padding-top: 140px; 
            background-color: #ffffff;
            margin: 0;
        }
        .label { 
            font-size: 14px; 
            margin-bottom: 20px; 
            color: #4b5563; 
            letter-spacing: 1.5px;
            font-weight: bold;
        }
        .box-wrapper {
            width: 85%;
            margin: 0 auto;
        }
        .title { 
            font-size: 17px; 
            font-weight: bold; 
            text-transform: uppercase; 
            border: 2.5px solid #111827; 
            padding: 25px 30px; 
            line-height: 1.4;
            color: #111827;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="label">BAGIAN DOKUMEN KEGIATAN:</div>
    <div class="box-wrapper">
        <div class="title">
            {{ $keg->nama_kegiatan }}
        </div>
    </div>
</body>
</html>