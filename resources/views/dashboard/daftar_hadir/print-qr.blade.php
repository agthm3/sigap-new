<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print QR - {{ $kegiatan->nama_kegiatan }}</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @page {
            size: 21.5cm 33cm;
            margin: 0;
        }

        html, body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            background: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
        }

        .sheet {
            width: calc(100% - 1.2rem);
            height: calc(100vh - 1.2rem);
            margin: 0.6rem;
            box-sizing: border-box;
            background: #ffffff;
            border: 8px solid #7a2222;
            overflow: hidden;
        }

        .inner-border {
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            border: 2px solid #c9a3a3;
            padding: 1rem 1.1rem;
            overflow: hidden;
        }

        .avoid-break {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        svg {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .sheet {
                width: 100%;
                height: 100vh;
                margin: 0;
                border: 8px solid #7a2222;
                overflow: hidden;
            }

            .inner-border {
                height: calc(100vh - 16px);
                overflow: hidden;
            }
        }
    </style>
</head>
<body>

    <div class="no-print max-w-4xl mx-auto px-4 pt-4 flex justify-end">
        <button onclick="window.print()"
                class="px-4 py-2 rounded-xl bg-[#7a2222] text-white font-semibold hover:bg-[#5f1a1a]">
            Print
        </button>
    </div>

    <div class="sheet">
        <div class="inner-border flex flex-col">

            <!-- JUDUL -->
            <div class="avoid-break text-center">
                <h1 class="text-2xl font-extrabold text-gray-900 leading-tight">
                    {{ $kegiatan->nama_kegiatan }}
                </h1>

                <div class="mt-2 text-sm text-gray-700 space-y-0.5">
                    <p>
                        <span class="font-semibold">Hari/Tanggal:</span>
                        {{ $kegiatan->hari_tanggal }}
                    </p>
                    <p>
                        <span class="font-semibold">Tempat:</span>
                        {{ $kegiatan->tempat }}
                    </p>
                    <p>
                        <span class="font-semibold">Waktu:</span>
                        {{ $kegiatan->waktu }}
                    </p>
                </div>
            </div>

            <!-- QR UTAMA -->
            <div class="avoid-break mt-4 flex justify-center">
                <div class="text-center">
                    <p class="text-sm font-semibold text-gray-600 mb-2">
                        Scan QR Code Daftar Hadir
                    </p>

                    <div class="p-4 border-4 border-gray-200 rounded-3xl inline-block bg-white">
                        {!! QrCode::format('svg')
                            ->size(300)
                            ->margin(1)
                            ->generate($qrUrl) !!}
                    </div>

                    <p class="text-[11px] text-gray-500 mt-2 break-all max-w-lg mx-auto leading-snug">
                        {{ $qrUrl }}
                    </p>
                </div>
            </div>

            <!-- INSTRUKSI -->
            <div class="avoid-break mt-4">
                <h2 class="text-base font-bold text-gray-900 mb-2 text-center">
                    Cara Mengisi Daftar Hadir
                </h2>

                <div class="space-y-1.5 text-sm text-gray-700 leading-snug max-w-2xl mx-auto">
                    <div class="flex gap-2">
                        <span class="font-bold w-4">1.</span>
                        <span>Scan QR Code menggunakan kamera HP.</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold w-4">2.</span>
                        <span>Isi data diri dengan lengkap dan benar.</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold w-4">3.</span>
                        <span>Gambar tanda tangan digital pada kolom TTD.</span>
                    </div>
                    <div class="flex gap-2">
                        <span class="font-bold w-4">4.</span>
                        <span>Tekan tombol Save untuk menyelesaikan daftar hadir.</span>
                    </div>
                </div>
            </div>

            <!-- INSTAGRAM -->
            <div class="avoid-break mt-auto pt-3 border-t text-center">
                <h3 class="text-sm font-bold text-gray-900">
                    Follow Instagram BRIDA Kota Makassar
                </h3>
                <p class="text-[11px] text-gray-500 mt-0.5">
                    Scan QR berikut untuk mengikuti informasi terbaru.
                </p>

                <div class="mt-2 flex justify-center">
                    <div class="p-2 border rounded-xl bg-white">
                        {!! QrCode::format('svg')
                            ->size(95)
                            ->margin(1)
                            ->generate('https://www.instagram.com/bridakotamakassar/') !!}
                    </div>
                </div>

                <p class="text-[11px] text-blue-600 mt-1">
                    @bridakotamakassar
                </p>
            </div>

        </div>
    </div>

</body>
</html>