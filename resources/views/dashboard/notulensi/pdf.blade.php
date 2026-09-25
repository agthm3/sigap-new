<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Notulensi Rapat</title>
    <style>
        @page {
            size: letter;
            margin: 1.2cm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            line-height: 1.4;
        }

        .page-break {
            page-break-after: always;
        }

        /* Container per halaman agar posisi absolut terkunci di halaman bersangkutan */
        .page-container {
            position: relative;
            min-height: 940px;
            height: 100%;
            width: 100%;
        }

        /* ---- KOP UMUM ---- */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .kop-table td { vertical-align: middle; }
        .logo-pemkot  { width: 65px; height: auto; }
        .logo-brida   { width: 140px; height: auto; }
        .center       { text-align: center; }
        .judul-kop    { font-size: 13px; font-weight: bold; line-height: 1.3; }
        .subjudul-kop { font-size: 10px; margin-top: 2px; }
        .line-kop     { border-top: 2px solid #000; border-bottom: 1px solid #000; height: 2px; margin-top: 6px; margin-bottom: 12px; }

        /* ---- FORMAT NOTULA RATA KIRI ---- */
        .notula-wrapper {
            text-align: left !important;
            direction: ltr !important;
            clear: both;
            width: 100%;
        }
        .notula-content {
            text-align: left !important;
            direction: ltr !important;
            line-height: 1.6;
            margin-left: 10px;
            margin-right: 10px;
            clear: both;
            white-space: pre-line;
            word-wrap: break-word;
        }

        /* ---- TABEL DATA PRESENSI ---- */
        table.data-presensi { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 6px;
        }
        table.data-presensi th,
        table.data-presensi td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        table.data-presensi th {
            text-align: center;
            font-weight: bold;
            background: #f3f4f6;
        }
        .ttd-img { width: 75px; height: 32px; object-fit: contain; }

        /* ---- AREA PENANDATANGAN KANAN ---- */
        .ttd-pejabat-section {
            margin-top: 15px;
            width: 100%;
            clear: both;
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
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        .ttd-kaban-img {
            width: 170px;
            height: 75px;
            object-fit: contain;
            display: block;
            margin-top: -6px;
            margin-bottom: -6px;
            margin-left: -10px;
        }
        
        .ttd-notulis-img {
            width: 130px;
            height: 55px;
            object-fit: contain;
            display: block;
            margin: 0;
        }

        .ttd-pejabat-box .nama-pejabat {
            font-weight: bold;
            font-size: 10.5px;
            text-decoration: underline;
            color: #000;
        }
        .ttd-pejabat-box .info-pejabat {
            font-size: 9px;
            margin-top: 1px;
            font-weight: bold;
            color: #000;
        }
        .clearfix::after { content: ""; display: table; clear: both; }

        /* ---- FOOTER WATERMARK STICKY BAWAH KERTAS ---- */
        .footer-watermark-fixed {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding-top: 6px;
            border-top: 1px solid #ccc;
        }
        .footer-inner { width: 100%; border-collapse: collapse; }
        .footer-inner td { vertical-align: middle; }
        .watermark-text {
            font-size: 8px;
            color: #6b7280;
            line-height: 1.4;
            text-align: left !important;
        }
        .watermark-text strong {
            color: #374151;
            font-size: 8.5px;
        }
        .sigap-badge {
            display: inline-block;
            background: #f0fdf4;
            border: 1px solid #86efac;
            color: #166534;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 6px;
            margin-bottom: 3px;
        }

        /* ---- DOKUMENTASI GRID ---- */
        .grid-foto { width: 100%; margin-top: 12px; }
        .foto-item { width: 48%; float: left; margin: 1%; text-align: center; }
        .foto-item img { width: 100%; height: 260px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    @php
        $namaKaban = $notulensi->pimpinan_nama ?: 'Haidil Adha, S.Sos., M.M.';
        $jabatanKaban = $notulensi->pimpinan_jabatan ?: 'Kepala Badan Riset dan Inovasi Daerah Kota Makassar';
    @endphp

    <!-- ======================================================== -->
    <!-- HALAMAN 1: SURAT PENGANTAR / UNDANGAN KEGIATAN            -->
    <!-- ======================================================== -->
    <table class="kop-table">
        <tr>
            <td width="15%" align="left">
                @if($logoPemkot)
                    <img src="{{ $logoPemkot }}" class="logo-pemkot">
                @endif
            </td>
            <td width="70%" class="center">
                <div style="font-size: 11px; font-weight: bold;">PEMERINTAH KOTA MAKASSAR</div>
                <div class="judul-kop">BADAN RISET DAN INOVASI DAERAH</div>
                <div class="subjudul-kop">Jalan Jenderal Achmad Yani No.2 Makassar 90111</div>
                <div class="subjudul-kop">Email: sekretariatbridamks@gmail.com | Website: brida.makassarkota.go.id</div>
            </td>
            <td width="15%" align="right">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo-brida">
                @endif
            </td>
        </tr>
    </table>
    <div class="line-kop"></div>

    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td width="12%">Nomor</td>
            <td width="2%">:</td>
            <td width="46%">{{ $notulensi->nomor_surat ?: '-' }}</td>
            <td width="40%" align="right">Makassar, {{ optional($notulensi->tanggal_surat)->translatedFormat('d F Y') ?: date('d F Y') }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
            <td>Kepada Yth.</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Perihal</td>
            <td style="vertical-align: top;">:</td>
            <td style="vertical-align: top; font-weight: bold;">{{ $notulensi->perihal_surat ?: 'NOTULA DAN DOKUMENTASI RAPAT' }}</td>
            <td style="vertical-align: top;">
                <strong>{{ $notulensi->tujuan_surat ?: 'Pejabat Eselon dan Staf BRIDA Kota Makassar' }}</strong><br>di -<br>&nbsp;&nbsp;&nbsp;&nbsp;Makassar
            </td>
        </tr>
    </table>

    <div style="line-height: 1.6; text-align: justify; margin-top: 15px;">
        <p>{{ $notulensi->isi_pembuka_surat ?: 'Sehubungan dengan telah dilaksanakannya rapat koordinasi, bersama ini kami sampaikan laporan notula dan dokumentasi pelaksanaan kegiatan dimaksud sebagai bahan evaluasi dan tindak lanjut program kerja.' }}</p>
        
        <p style="margin-top: 10px;">Adapun kegiatan rapat tersebut telah dilaksanakan pada:</p>
        <table style="margin-left: 20px; line-height: 1.6;">
            <tr>
                <td width="120">Hari / Tanggal</td>
                <td width="10">:</td>
                <td><strong>{{ $notulensi->hari_tanggal }}</strong></td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ $notulensi->waktu }}</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>{{ $notulensi->tempat }}</td>
            </tr>
            <tr>
                <td>Acara</td>
                <td>:</td>
                <td><strong>{{ $notulensi->judul_acara }}</strong></td>
            </tr>
        </table>

        <p style="margin-top: 15px;">Demikian surat ini disampaikan untuk menjadi perhatian dan dapat dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- Tanda Tangan Kaban (Halaman 1) -->
    <div class="ttd-pejabat-section clearfix" style="margin-top: 25px;">
        <div class="ttd-pejabat-box">
            <div class="ttd-jabatan">{{ strtoupper($jabatanKaban) }},</div>
            
            @if($ttdKabanBase64)
                <img src="{{ $ttdKabanBase64 }}" class="ttd-kaban-img">
            @else
                <div style="height: 60px;"></div>
            @endif

            <div class="nama-pejabat">{{ $namaKaban }}</div>
            @if($notulensi->pimpinan_pangkat)
                <div class="info-pejabat">{{ $notulensi->pimpinan_pangkat }}</div>
            @endif
            @if($notulensi->pimpinan_nip)
                <div class="info-pejabat">NIP. {{ $notulensi->pimpinan_nip }}</div>
            @endif
        </div>
    </div>

    <div class="page-break"></div>

    <!-- ======================================================== -->
    <!-- HALAMAN 2: LEMBAR NOTULA RAPAT (DENGAN FOOTER STICKY)     -->
    <!-- ======================================================== -->
    <div class="page-container">
        
        <div style="text-align: center; margin-bottom: 15px;">
            <div style="font-size: 13px; font-weight: bold; text-decoration: underline;">NOTULA RAPAT</div>
            <div style="font-size: 10.5px; font-weight: bold; margin-top: 3px;">{{ strtoupper($notulensi->judul_acara) }}</div>
        </div>

        <div class="notula-wrapper">
            <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px;">A. KEGIATAN RAPAT</div>
            <table style="width: 100%; line-height: 1.6; margin-left: 10px; margin-bottom: 15px;">
                <tr>
                    <td width="130">Hari / Tanggal</td>
                    <td width="10">:</td>
                    <td>{{ $notulensi->hari_tanggal }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>:</td>
                    <td>{{ $notulensi->waktu }}</td>
                </tr>
                <tr>
                    <td>Tempat</td>
                    <td>:</td>
                    <td>{{ $notulensi->tempat }}</td>
                </tr>
                <tr>
                    <td>Acara / Pembahasan</td>
                    <td>:</td>
                    <td>{{ $notulensi->judul_acara }}</td>
                </tr>
                <tr>
                    <td>Pimpinan Rapat</td>
                    <td>:</td>
                    <td>{{ $notulensi->pimpinan_rapat ?: $namaKaban }}</td>
                </tr>
                <tr>
                    <td>Peserta Rapat</td>
                    <td>:</td>
                    <td>{{ $notulensi->peserta_ringkas ?: 'Seluruh Pejabat Struktural & Tim Fungsional' }}</td>
                </tr>
                <tr>
                    <td>Notulis</td>
                    <td>:</td>
                    <td>{{ $notulensi->notulis_nama ?: ($notulensi->creator->name ?? 'Staf') }}</td>
                </tr>
            </table>

            <!-- B. PELAKSANA KEGIATAN (TERKUNCI RATA KIRI) -->
            <div style="font-weight: bold; font-size: 11px; margin-bottom: 5px; clear: both;">
                B. PELAKSANA KEGIATAN
            </div>
            
            <div class="notula-content">
{{ $notulensi->isi_pelaksana_kegiatan ?: 'Rapat berlangsung dengan pembahasan agenda pokok program kerja, evaluasi penyerapan realisasi kegiatan, dan perumusan langkah tindak lanjut bersama.' }}
            </div>
        </div>

        <!-- Tanda Tangan Notulis (Halaman 2) -->
        <div class="ttd-pejabat-section clearfix" style="margin-top: 25px;">
            <div class="ttd-pejabat-box">
                <div class="ttd-tempat">Makassar, {{ optional($notulensi->tanggal_surat)->translatedFormat('d F Y') ?: date('d F Y') }}</div>
                <div class="ttd-jabatan">Notulis Kegiatan,</div>

                @if($ttdNotulisBase64)
                    <img src="{{ $ttdNotulisBase64 }}" class="ttd-notulis-img">
                @else
                    <div style="height: 50px;"></div>
                @endif

                <div class="nama-pejabat">{{ $notulensi->notulis_nama ?: ($notulensi->creator->name ?? 'Notulis') }}</div>
                <div class="info-pejabat">Pegawai BRIDA Kota Makassar</div>
            </div>
        </div>

        <!-- FOOTER WATERMARK RESMI STICKY DI BAWAH KERTAS -->
        <div class="footer-watermark-fixed">
            <table class="footer-inner">
                <tr>
                    <td width="80%">
                        <div class="sigap-badge">✔ SIGAP NOTULENSI — DOKUMEN RESMI</div>
                        <div class="watermark-text">
                            <strong>Notula rapat ini dibuat dan diverifikasi secara digital melalui SIGAP NOTULENSI.</strong><br>
                            Sistem Informasi dan Pengelolaan Administrasi – BRIDA Kota Makassar.<br>
                            Dokumen ini merupakan rekaman sah jalannya rapat dinas beserta seluruh tindak lanjutnya.
                        </div>
                        <div class="watermark-text" style="margin-top: 3px; font-size: 7.5px; color: #9ca3af;">
                            ID Dokumen: NOT-{{ str_pad($notulensi->id, 5, '0', STR_PAD_LEFT) }} • Dicetak pada: {{ date('d/m/Y H:i') }} WITA
                        </div>
                    </td>
                    <td width="20%" align="right">
                        @if(!empty($qrVerifikasi))
                            <img src="data:image/svg+xml;base64,{{ $qrVerifikasi }}" style="width: 48px; height: 48px;" alt="QR Validasi">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>

    <div class="page-break"></div>

    <!-- ======================================================== -->
    <!-- HALAMAN 3: LEMBAR DAFTAR HADIR (PERSIS FORMAT ASLI)       -->
    <!-- ======================================================== -->
    <table class="kop-table">
        <tr>
            <td width="15%" align="left">
                @if($logoPemkot)
                    <img src="{{ $logoPemkot }}" class="logo-pemkot">
                @endif
            </td>
            <td width="70%" class="center">
                <div class="judul-kop">{{ $notulensi->judul_acara }}</div>
                <div class="subjudul-kop">{{ $notulensi->hari_tanggal }}</div>
                <div class="subjudul-kop">{{ $notulensi->tempat }}</div>
                <div class="subjudul-kop">{{ $notulensi->waktu }}</div>
            </td>
            <td width="15%" align="right">
                @if($logoBrida)
                    <img src="{{ $logoBrida }}" class="logo-brida">
                @endif
            </td>
        </tr>
    </table>

    <div class="line-kop"></div>

    <table class="data-presensi">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="22%">Nama</th>
                <th width="22%">Instansi</th>
                <th width="8%">Gender</th>
                <th width="16%">No. HP</th>
                <th width="17%">Email</th>
                <th width="10%">TTD</th>
            </tr>
        </thead>
        <tbody>
            @forelse($notulensi->pesertas as $item)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->instansi }}</td>
                    <td align="center">{{ $item->gender ?: 'L' }}</td>
                    <td>{{ $item->nip_nohp ?: '-' }}</td>
                    <td>{{ $item->email ?: '-' }}</td>
                    <td align="center">
                        @php
                            $parafRel = str_replace(asset('storage/'), '', $item->paraf_image ?? '');
                            $parafFull = storage_path('app/public/' . ltrim($parafRel, '/'));
                        @endphp
                        @if($item->paraf_image && file_exists($parafFull))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($parafFull)) }}" class="ttd-img">
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" align="center">Belum ada data peserta daftar hadir.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan Kaban (Halaman 3) -->
    <div class="ttd-pejabat-section clearfix">
        <div class="ttd-pejabat-box">
            <div class="ttd-tempat">Makassar, {{ optional($notulensi->tanggal_surat)->translatedFormat('d F Y') ?: date('d F Y') }}</div>
            <div class="ttd-jabatan">{{ $jabatanKaban }}</div>

            @if($ttdKabanBase64)
                <img src="{{ $ttdKabanBase64 }}" class="ttd-kaban-img">
            @else
                <div style="height: 60px;"></div>
            @endif

            <div class="nama-pejabat">{{ $namaKaban }}</div>
            @if($notulensi->pimpinan_pangkat)
                <div class="info-pejabat">Pangkat : {{ $notulensi->pimpinan_pangkat }}</div>
            @endif
            @if($notulensi->pimpinan_nip)
                <div class="info-pejabat">NIP. {{ $notulensi->pimpinan_nip }}</div>
            @endif
        </div>
    </div>

    <!-- Footer Watermark SIGAP -->
    <div class="footer-watermark">
        <table class="footer-inner">
            <tr>
                <td width="80%">
                    <div class="sigap-badge">✔ SIGAP — TERVERIFIKASI</div>
                    <div class="watermark-text">
                        <strong>Daftar hadir ini digenerate dan diverifikasi secara digital oleh SIGAP.</strong><br>
                        Sistem Informasi dan Pengelolaan Administrasi – BRIDA Kota Makassar.
                    </div>
                </td>
                <td width="20%" align="right">
                    @if(!empty($qrVerifikasi))
                        <img src="data:image/svg+xml;base64,{{ $qrVerifikasi }}" style="width: 50px; height: 50px;">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- ======================================================== -->
    <!-- HALAMAN 4: DOKUMENTASI FOTO RAPAT                         -->
    <!-- ======================================================== -->
    <div style="text-align: center; margin-bottom: 20px;">
        <div style="font-size: 13px; font-weight: bold; text-decoration: underline;">DOKUMENTASI KEGIATAN</div>
        <div style="font-size: 10.5px; font-weight: bold; margin-top: 3px;">{{ strtoupper($notulensi->judul_acara) }}</div>
    </div>

    <div class="grid-foto clearfix">
        @if(!empty($notulensi->dokumentasi_foto))
            @foreach($notulensi->dokumentasi_foto as $foto)
                @php
                    $fotoPath = storage_path('app/public/' . $foto);
                @endphp
                @if(file_exists($fotoPath))
                    <div class="foto-item">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($fotoPath)) }}">
                    </div>
                @endif
            @endforeach
        @else
            <div style="text-align: center; color: #999; padding-top: 80px;">
                Belum ada foto dokumentasi yang dilampirkan.
            </div>
        @endif
    </div>

</body>
</html>