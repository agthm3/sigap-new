<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cover Kegiatan</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; text-align: center; padding-top: 300px; background-color: #fdfdfd; }
        .label { font-size: 16px; margin-bottom: 15px; color: #555; }
        .title { font-size: 22px; font-weight: bold; text-transform: uppercase; border: 2px solid #000; display: inline-block; padding: 20px 40px; }
    </style>
</head>
<body>
    <div class="label">BAGIAN DOKUMEN KEGIATAN:</div>
    <div class="title">
        {{ $keg->nama_kegiatan }}
    </div>
</body>
</html>