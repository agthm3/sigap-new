<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kesediaan Narasumber - {{ $kegiatan->nama_kegiatan }}</title>
    <style>
        @page { size: A4 portrait; margin: 2.5cm 2cm; }
        body { font-family: Arial, sans-serif; font-size: 12pt; line-height: 1.5; color: #000; position: relative; }
        h3 { text-align: center; text-transform: uppercase; margin: 0; padding: 0; }
        .header-text { text-align: center; margin: 3px 0; font-size: 11pt; }
        .line { border-bottom: 2px solid #000; margin-top: 15px; margin-bottom: 20px; }
        table.biodata td { vertical-align: top; padding: 4px 0; }
        .td-label { width: 220px; }
        .td-colon { width: 20px; text-align: center; }
        .ttd-box { width: 250px; float: right; margin-top: 40px; text-align: center; }
        .ttd-img { height: 70px; margin: 5px 0; }
        .page-break { page-break-after: always; }
        
        /* Watermark SIGAP */
        .watermark {
            position: fixed;
            top: 25%;
            left: 10%;
            width: 80%;
            opacity: 0.08;
            z-index: -1000;
        }
    </style>
</head>
<body>

    @if($logoBrida)
        <img src="{{ $logoBrida }}" class="watermark" alt="Watermark SIGAP">
    @endif

    <h3 style="font-size: 14pt; margin-bottom: 10px;">{{ strtoupper($kegiatan->nama_kegiatan) }}</h3>
    <p class="header-text">Hari/Tanggal : {{ $kegiatan->hari_tanggal }}</p>
    <p class="header-text">Waktu : {{ $kegiatan->waktu }}</p>
    <p class="header-text">Tempat : {{ $kegiatan->tempat }}</p>
    
    <div class="line"></div>

    <p style="margin-top: 20px;">Yang bertanda tangan di bawah ini :</p>
    
    <table class="biodata" style="width: 100%;">
        <tr>
            <td class="td-label">Nama Lengkap</td>
            <td class="td-colon">:</td>
            <td>{{ $data->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="td-label">Alamat Kantor</td>
            <td class="td-colon">:</td>
            <td>{{ $data->alamat_kantor ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">Alamat Rumah</td>
            <td class="td-colon">:</td>
            <td>{{ $data->alamat_rumah ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">HP</td>
            <td class="td-colon">:</td>
            <td>{{ $data->no_hp ?? '-' }}</td>
        </tr>
        <tr>
            <td class="td-label">Email</td>
            <td class="td-colon">:</td>
            <td>{{ $data->email ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin-top: 25px; text-align: justify;">
        Dengan ini menyatakan <b>BERSEDIA</b> menjadi <b>NARASUMBER</b> pada kegiatan <b>{{ $kegiatan->nama_kegiatan }}</b>.
    </p>

    <div class="ttd-box">
        <p style="margin: 0;">{{ $data->tempat_ttd }}, {{ $data->signed_at ? $data->signed_at->translatedFormat('d F Y') : '-' }}</p>
        <p style="margin: 0;">Hormat saya,</p>
        @if($data->ttd_path)
            <img src="{{ public_path('storage/' . $data->ttd_path) }}" class="ttd-img" alt="TTD">
        @else
            <div style="height: 70px;"></div>
        @endif
        <p style="margin: 0; text-decoration: underline; font-weight: bold;">{{ $data->nama_lengkap }}</p>
    </div>

    <div class="page-break" style="clear: both;"></div>

    <h3>BIODATA</h3>
    <br>

    <table class="biodata" style="width: 100%;">
        <tr><td class="td-label">NAMA LENGKAP</td><td class="td-colon">:</td><td>{{ $data->nama_lengkap }}</td></tr>
        <tr><td class="td-label">NOMOR INDUK PEGAWAI (NIP)</td><td class="td-colon">:</td><td>{{ $data->nip ?? '-' }}</td></tr>
        <tr><td class="td-label">TEMPAT/TANGGAL LAHIR</td><td class="td-colon">:</td><td>{{ $data->tempat_tanggal_lahir ?? '-' }}</td></tr>
        <tr><td class="td-label">PANGKAT/GOL.RUANG</td><td class="td-colon">:</td><td>{{ $data->pangkat_golongan ?? '-' }}</td></tr>
        <tr><td class="td-label">JABATAN</td><td class="td-colon">:</td><td>{{ $data->jabatan ?? '-' }}</td></tr>
        <tr><td class="td-label">INSTANSI/UNIT KERJA</td><td class="td-colon">:</td><td>{{ $data->instansi_unit_kerja ?? '-' }}</td></tr>
        <tr><td class="td-label">AGAMA</td><td class="td-colon">:</td><td>{{ $data->agama ?? '-' }}</td></tr>
        <tr><td class="td-label">ALAMAT KANTOR/NO. TELP</td><td class="td-colon">:</td><td>{{ $data->alamat_kantor ?? '-' }}</td></tr>
        <tr><td class="td-label">ALAMAT RUMAH/NO. TELP</td><td class="td-colon">:</td><td>{{ $data->alamat_rumah ?? '-' }} {{ $data->no_hp ? '/ '.$data->no_hp : '' }}</td></tr>
        <tr><td class="td-label">STATUS KELUARGA</td><td class="td-colon">:</td><td>{{ $data->status_keluarga ?? '-' }}</td></tr>
        <tr><td class="td-label">H O B B Y</td><td class="td-colon">:</td><td>{{ $data->hobby ?? '-' }}</td></tr>
        <tr><td class="td-label">MATERI</td><td class="td-colon">:</td><td>{{ $data->materi ?? '-' }}</td></tr>
        <tr><td class="td-label">NO. TELEPON</td><td class="td-colon">:</td><td>{{ $data->no_hp ?? '-' }}</td></tr>
        <tr><td class="td-label">NO. NPWP</td><td class="td-colon">:</td><td>{{ $data->npwp ?? '-' }}</td></tr>
        <tr><td class="td-label">NO. REKENING</td><td class="td-colon">:</td><td>{{ $data->no_rekening ?? '-' }}</td></tr>
    </table>

    <div class="ttd-box">
        <p style="margin: 0;">{{ $data->tempat_ttd }}, {{ $data->signed_at ? $data->signed_at->translatedFormat('d F Y') : '-' }}</p>
        <p style="margin: 0;">Hormat saya,</p>
        @if($data->ttd_path)
            <img src="{{ public_path('storage/' . $data->ttd_path) }}" class="ttd-img" alt="TTD">
        @else
            <div style="height: 70px;"></div>
        @endif
        <p style="margin: 0; text-decoration: underline; font-weight: bold;">{{ $data->nama_lengkap }}</p>
    </div>

</body>
</html>