<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
@page {
    size: 210mm 330mm;
    margin: 8mm 10mm 10mm 10mm;
}

body {
    font-family: DejaVu Sans, sans-serif;
    margin: 0;
    padding: 0;
    color: #000;
    font-size: 10px;
}

.page {
    position: relative;
    page-break-after: always;
    height: 312mm;
    overflow: hidden;
}

.page:last-child {
    page-break-after: auto;
}

.header {
    text-align: center;
    margin-bottom: 3mm;
}

.title {
    font-size: 13px;
    font-weight: bold;
    color: #000;
    line-height: 1.35;
    text-transform: uppercase;
}

.meta {
    font-size: 9.5px;
    margin-bottom: 2.5mm;
    line-height: 1.35;
    color: #000;
}

.desc {
    font-size: 9.5px;
    margin-bottom: 2.5mm;
    line-height: 1.35;
    max-height: 16mm;
    overflow: hidden;
    color: #000;
}

.grid {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.grid td {
    width: 50%;
    padding: 1.5mm;
    vertical-align: top;
}

.photo-box {
    width: 100%;
    height: 78mm;
    border: 1px solid #cfcfcf;
    overflow: hidden;
    box-sizing: border-box;
}

.photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 9px;
    background: #fafafa;
}

.page.first .photo-box {
    height: 75mm;
}

.footer {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    text-align: center;
    font-size: 8px;
    line-height: 1.35;
    color: #6b7280;
    border-top: 1px solid #e5e7eb;
    padding-top: 2mm;
    background: #fff;
}

.footer a {
    color: #000;
    text-decoration: none;
}
</style>
</head>
<body>

@foreach($lembarPages as $index => $lembar)
    <div class="page {{ $index === 0 ? 'first' : '' }}">

        @if($index === 0)
            <div class="header">
                <div class="title">{{ $kegiatan->judul }}</div>
                <div class="title">HARI/TANGGAL: {{ strtoupper($kegiatan->hari_tanggal) }}</div>
                <div class="title">TEMPAT: {{ strtoupper($kegiatan->tempat) }}</div>
            </div>
        @endif

        <div class="meta">
            <strong>Pegawai:</strong> {{ $lembar->user->name ?? '-' }}<br>
            <strong>Lembar:</strong> {{ $lembar->lembar_ke }}
        </div>

        <div class="desc">
            <strong>Deskripsi:</strong><br>
            {!! nl2br(e($lembar->deskripsi ?? '-')) !!}
        </div>

        <table class="grid">
            @for($r = 0; $r < 3; $r++)
                <tr>
                    @for($c = 0; $c < 2; $c++)
                        @php
                            $i = $r * 2 + $c;
                            $img = $lembar->foto_base64[$i] ?? null;
                        @endphp
                        <td>
                            <div class="photo-box">
                                @if($img)
                                    <img src="{{ $img }}" class="photo" alt="foto">
                                @else
                                    <div class="placeholder">Belum ada foto</div>
                                @endif
                            </div>
                        </td>
                    @endfor
                </tr>
            @endfor
        </table>

        <div class="footer">
            Dokumen ini telah terdaftar dan terverifikasi oleh SIGAP PPD melalui link berikut<br>
            <a href="https://sigap.brida.makassarkota.go.id/sigap-ppd">
                https://sigap.brida.makassarkota.go.id/sigap-ppd
            </a>
        </div>
    </div>
@endforeach

</body>
</html>