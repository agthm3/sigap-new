<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumentasi Kegiatan</title>
    <style>
        @page { margin: 1.5cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #000; }
        
        .header { text-align: center; margin-bottom: 25px; }
        .judul { font-size: 16px; font-weight: bold; text-transform: uppercase; line-height: 1.3; color: #7a2222; }
        .subjudul { font-size: 12px; font-weight: normal; margin-top: 5px; color: #333; }
        
        .page-break { page-break-after: always; }
        
        /* Tabel untuk Grid 2 Kolom */
        .grid-table { width: 100%; border-collapse: separate; border-spacing: 10px; }
        .grid-table td {
            width: 50%;
            height: 250px; /* Tinggi konstan untuk tiap foto */
            border: 2px solid #555;
            padding: 5px;
            vertical-align: middle;
            text-align: center;
            background-color: #fafafa;
        }
        
        /* Memaksa foto proporsional di dalam kotak */
        .img-container img {
            max-width: 100%;
            max-height: 240px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    {{-- Pecah gambar menjadi potongan per 6 gambar (1 halaman = 6 gambar) --}}
    @foreach(array_chunk($images, 6) as $pageIndex => $pageImages)
        
        <div class="header">
            <div class="judul">DOKUMENTASI {{ $kegiatan->nama_kegiatan }}</div>
            <div class="subjudul">
                {{ $gelombang->nama_gelombang }} — {{ \Carbon\Carbon::parse($gelombang->tanggal)->translatedFormat('d F Y') }}
            </div>
        </div>

        <table class="grid-table">
            {{-- Pecah lagi 6 gambar tersebut menjadi baris-baris (1 baris = 2 gambar) --}}
            @foreach(array_chunk($pageImages, 2) as $rowImages)
                <tr>
                    @foreach($rowImages as $img)
                        <td>
                            <div class="img-container">
                                <img src="{{ $img }}" alt="Dokumentasi">
                            </div>
                        </td>
                    @endforeach
                    
                    {{-- Jika gambar ganjil di baris terakhir, buat 1 kolom kosong agar layout tidak rusak --}}
                    @if(count($rowImages) == 1)
                        <td style="border: 2px dashed #ccc; background-color: transparent;"></td>
                    @endif
                </tr>
            @endforeach
        </table>

        {{-- Jangan beri page-break di halaman terakhir --}}
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>