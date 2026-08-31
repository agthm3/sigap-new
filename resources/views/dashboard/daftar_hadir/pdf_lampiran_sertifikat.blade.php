<!DOCTYPE html>
<html>
<head>
    <title>Lampiran Sertifikat</title>
    <style>
        /* Mengatur margin global kertas agar tabel tidak menabrak footer di bawah */
        @page {
            margin: 40px 40px 120px 40px; /* Top, Right, Bottom, Left */
        }
        body { 
            font-family: sans-serif; 
            font-size: 12px; 
            color: #333;
        }
        
        /* Pengaturan Footer Fixed DOMPDF */
        footer {
            position: fixed; 
            bottom: -90px; /* Menarik posisi relatif dari margin bottom @page */
            left: 0px; 
            right: 0px;
            height: 90px;
        }

        /* Styling elemen layout */
        .text-center { text-align: center; }
        .maroon-line {
            border-top: 3px solid #800000;
            margin-bottom: 8px;
            margin-top: 5px;
        }
        
        /* Styling Tabel */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
        }
        th, td { 
            border: 1px solid #333; 
            padding: 7px; 
            vertical-align: middle;
        }
        th { 
            background-color: #f3f4f6; 
            font-weight: bold;
        }
        
        /* Kotak instruksi */
        .instruction-box {
            background-color: #fdfdfd;
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 10px;
            margin-bottom: 15px;
            font-size: 11px;
            line-height: 1.5;
        }
        .text-maroon {
            color: #800000;
            font-weight: bold;
        }
        
        /* Text URL Styling */
        .url-text {
            color: #0056b3;
            font-size: 9.5px;
            font-family: sans-serif;
            word-break: break-all;
        }
    </style>
</head>
<body>

    <footer>
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="border: none; padding: 0; vertical-align: top; width: 85%;">
                    <div class="maroon-line"></div>
                    <p style="font-size: 10px; font-style: italic; color: #555; margin: 0; line-height: 1.4;">
                        Dokumen lampiran ini digenerate secara otomatis dan terintegrasi melalui sistem <strong>SIGAP SERTIFIKAT</strong>.<br>
                        Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar.
                    </p>
                </td>
                <td style="border: none; padding: 0; vertical-align: top; text-align: right; width: 15%;">
                    <img src="data:image/svg+xml;base64,{{ $qrSertifikat }}" width="65" style="margin-top: -5px;">
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <div class="text-center">
            <h3 style="margin-bottom: 5px; font-size: 16px;">LAMPIRAN PENERBITAN SERTIFIKAT</h3>
            <p style="margin-top:0; font-size: 12px;">
                <strong>Kegiatan:</strong> {{ $kegiatan->nama_kegiatan }}<br>
                <strong>Tanggal:</strong> {{ $kegiatan->hari_tanggal }}
            </p>
        </div>

        <div class="instruction-box">
            <strong>Informasi Pengambilan Sertifikat:</strong><br>
            Bagi seluruh peserta yang namanya tercantum pada tabel di bawah ini, E-Sertifikat resmi sudah dapat diunduh. 
            <br><br>
            Anda memiliki 2 opsi untuk mengunduhnya:
            <ul style="margin: 3px 0; padding-left: 20px;">
                <li><strong>Opsi Langsung:</strong> Klik tautan (URL) berwarna biru yang tertera di bawah nomor sertifikat Anda.</li>
                <li><strong>Opsi Pencarian:</strong> Pindai QR Code di pojok kanan bawah dokumen ini, lalu masukkan Nomor Sertifikat Anda.</li>
            </ul>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%" class="text-center">No</th>
                    <th style="width: 35%">Nomor & Tautan Sertifikat</th>
                    <th style="width: 30%">Nama Peserta</th>
                    <th style="width: 30%">Asal Dinas / Instansi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan->peserta as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-family: monospace; font-size: 11px; font-weight: bold; margin-bottom: 3px;">
                            {{ $p->nomor_sertifikat_dinamis }}
                        </div>
                        
                        {{-- Menampilkan URL eksplisit agar dideteksi otomatis oleh pembaca PDF (Chrome/Edge/Acrobat) --}}
                        @if($p->sertifikat_id)
                            <div class="url-text">
                                https://sigap.brida.makassarkota.go.id/sertifikat/view/{{ $p->sertifikat_id }}
                            </div>
                        @endif
                    </td>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->instansi }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>

</body>
</html>