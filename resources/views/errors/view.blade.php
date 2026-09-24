<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sertifikat — SIGAP BRIDA</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-100 py-10">

<div class="max-w-5xl mx-auto bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">
    <!-- TOP BAR -->
    <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
        <div>
            <h2 class="font-semibold text-gray-800 text-lg">Sertifikat Digital</h2>
            <p class="text-xs text-gray-500">Sistem Informasi Gabungan Arsip & Privasi (SIGAP BRIDA)</p>
        </div>

        <div class="flex items-center gap-3">
            <button id="btnDownloadPng" onclick="downloadCertificate('png')" class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition shadow-sm">
                Unduh Gambar (PNG)
            </button>
            <button id="btnDownloadPdf" onclick="downloadCertificate('pdf')" class="flex items-center gap-2 px-4 py-2 bg-[#7a2222] hover:bg-[#601a1a] text-white text-sm font-medium rounded-lg transition shadow-sm">
                Unduh Dokumen (PDF)
            </button>
        </div>
    </div>

    <!-- CANVAS PREVIEW AREA -->
    <div class="p-6 flex flex-col items-center justify-center bg-gray-200 overflow-x-auto">
        <canvas id="certificateCanvas" class="w-full max-w-4xl h-auto shadow-2xl rounded border bg-white"></canvas>
        <p class="text-xs text-gray-500 mt-3">*Dokumen dirender dengan presisi tinggi Native Canvas 300 DPI.</p>
    </div>
</div>

<!-- Hidden container untuk generate QR Code Matrix -->
<div id="hidden-qr" class="hidden"></div>

<script>
@php
    $peran = $sertifikat->kegiatan->peran_peserta ?? 'Peserta';
    if ($peran === 'Tenaga Ahli') {
        $kalimatPeran = 'telah menjadi tenaga ahli pada kegiatan';
    } elseif ($peran === 'Narasumber') {
        $kalimatPeran = 'telah menjadi narasumber pada kegiatan';
    } elseif ($peran === 'Panitia') {
        $kalimatPeran = 'telah menjadi panitia pada kegiatan';
    } else {
        $kalimatPeran = 'atas partisipasi dalam kegiatan';
    }
@endphp

const certificateData = {
    nomor: "{{ $sertifikat->nomor_sertifikat }}",
    nama: "{{ $sertifikat->nama_penerima }}",
    peranText: "{{ $kalimatPeran }}",
    kegiatan: "{{ $sertifikat->kegiatan->nama_kegiatan }}",
    tanggal: "{{ $sertifikat->kegiatan->tanggal }}",
    tempat: "{{ $sertifikat->kegiatan->tempat ?? 'Kota Makassar' }}",
    verifUrl: "{{ url('/sertifikat?no='.$sertifikat->nomor_sertifikat) }}",
    assets: {
        logoPemkot: "{{ asset('images/sertifikat/logo-pemkot.png') }}",
        logoBrida: "{{ asset('images/sertifikat/logo-brida.png') }}",
        ttd: "{{ asset('images/sertifikat/ttd-kaban.png') }}"
    }
};

const canvas = document.getElementById('certificateCanvas');
const ctx = canvas.getContext('2d');

// Standar Ukuran A4 Landscape @ 300 DPI
const CANVAS_WIDTH = 3508;
const CANVAS_HEIGHT = 2480;
canvas.width = CANVAS_WIDTH;
canvas.height = CANVAS_HEIGHT;

function loadImage(src) {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = () => resolve(img);
        img.onerror = () => resolve(null);
        img.src = src;
    });
}

function generateQrCodeDataUrl(text) {
    return new Promise((resolve) => {
        const qrContainer = document.getElementById('hidden-qr');
        qrContainer.innerHTML = '';
        new QRCode(qrContainer, {
            text: text,
            width: 400,
            height: 400,
            correctLevel: QRCode.CorrectLevel.H
        });
        setTimeout(() => {
            const qrCanvas = qrContainer.querySelector('canvas');
            if (qrCanvas) {
                resolve(qrCanvas.toDataURL("image/png"));
            } else {
                const qrImg = qrContainer.querySelector('img');
                resolve(qrImg ? qrImg.src : null);
            }
        }, 100);
    });
}

// Utility untuk render gambar dengan mempertahankan aspect ratio
function drawImageContained(ctx, img, x, y, maxW, maxH) {
    if (!img) return;
    const ratio = Math.min(maxW / img.width, maxH / img.height);
    const drawW = img.width * ratio;
    const drawH = img.height * ratio;
    const drawX = x + (maxW - drawW) / 2;
    const drawY = y + (maxH - drawH) / 2;
    ctx.drawImage(img, drawX, drawY, drawW, drawH);
}

// Helper untuk auto-wrapping text agar tidak keluar garis kanvas
function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
    const words = text.split(' ');
    let line = '';
    let currentY = y;

    for (let n = 0; n < words.length; n++) {
        const testLine = line + words[n] + ' ';
        const metrics = ctx.measureText(testLine);
        const testWidth = metrics.width;
        
        if (testWidth > maxWidth && n > 0) {
            ctx.fillText(line.trim(), x, currentY);
            line = words[n] + ' ';
            currentY += lineHeight;
        } else {
            line = testLine;
        }
    }
    ctx.fillText(line.trim(), x, currentY);
    return currentY + lineHeight; // Mengembalikan posisi Y berikutnya
}

async function renderCertificate() {
    await document.fonts.ready;

    // 1. Background Putih
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

    // 2. Border Frame
    ctx.strokeStyle = "#e5e7eb";
    ctx.lineWidth = 6;
    ctx.strokeRect(60, 60, CANVAS_WIDTH - 120, CANVAS_HEIGHT - 120);

    ctx.strokeStyle = "#7a2222";
    ctx.lineWidth = 30;
    ctx.strokeRect(100, 100, CANVAS_WIDTH - 200, CANVAS_HEIGHT - 200);

    ctx.lineWidth = 4;
    ctx.strokeRect(140, 140, CANVAS_WIDTH - 280, CANVAS_HEIGHT - 280);

    // 3. Watermark Tengah
    ctx.save();
    ctx.translate(CANVAS_WIDTH / 2, CANVAS_HEIGHT / 2);
    ctx.rotate(-25 * Math.PI / 180);
    ctx.font = "800 240px Inter, sans-serif";
    ctx.fillStyle = "rgba(229, 231, 235, 0.45)";
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.fillText("SIGAP BRIDA", 0, 0);
    ctx.restore();

    // 4. Preload Semua Aset Gambar
    const qrDataUrl = await generateQrCodeDataUrl(certificateData.verifUrl);
    const [imgPemkot, imgBrida, imgTtd, imgQr] = await Promise.all([
        loadImage(certificateData.assets.logoPemkot),
        loadImage(certificateData.assets.logoBrida),
        loadImage(certificateData.assets.ttd),
        loadImage(qrDataUrl)
    ]);

    // 5. Render Logo Kiri & Kanan (Ukuran proporsional & presisi)
    const logoBoxW = 520;
    const logoBoxH = 280;
    const logoY = 200;

    drawImageContained(ctx, imgPemkot, 220, logoY, logoBoxW, logoBoxH);
    drawImageContained(ctx, imgBrida, CANVAS_WIDTH - 220 - logoBoxW, logoY, logoBoxW, logoBoxH);

    // 6. Nomor Sertifikat
    ctx.textAlign = "center";
    ctx.fillStyle = "#6B7280";
    ctx.font = "600 32px Inter, sans-serif";
    ctx.letterSpacing = "4px";
    ctx.fillText("NOMOR SERTIFIKAT", CANVAS_WIDTH / 2, 280);

    ctx.fillStyle = "#7a2222";
    ctx.font = "700 42px Inter, sans-serif";
    ctx.letterSpacing = "1px";
    ctx.fillText(certificateData.nomor, CANVAS_WIDTH / 2, 340);

    // 7. Judul Utama
    ctx.fillStyle = "#6B7280";
    ctx.font = "600 34px Inter, sans-serif";
    ctx.letterSpacing = "6px";
    ctx.fillText("SERTIFIKAT RESMI", CANVAS_WIDTH / 2, 480);

    ctx.fillStyle = "#7a2222";
    ctx.font = "700 92px 'Playfair Display', serif";
    ctx.letterSpacing = "0px";
    ctx.fillText("Sertifikat Penghargaan", CANVAS_WIDTH / 2, 600);

    // Garis Aksen Judul
    ctx.fillStyle = "#7a2222";
    ctx.fillRect(CANVAS_WIDTH / 2 - 140, 645, 280, 8);

    // 8. Nama Penerima
    ctx.fillStyle = "#4B5563";
    ctx.font = "400 44px Inter, sans-serif";
    ctx.fillText("Diberikan kepada:", CANVAS_WIDTH / 2, 780);

    ctx.fillStyle = "#111827";
    ctx.font = "700 86px 'Playfair Display', serif";
    ctx.fillText(certificateData.nama, CANVAS_WIDTH / 2, 890);

    // 9. Deskripsi & Detail Kegiatan (Dynamic Auto-Wrap & Responsive Spacing)
    ctx.fillStyle = "#374151";
    ctx.font = "400 42px Inter, sans-serif";
    ctx.fillText(certificateData.peranText, CANVAS_WIDTH / 2, 980);

    // Nama Kegiatan (Maksimal lebar 2200px agar tidak menabrak margin tepi kanvas)
    ctx.fillStyle = "#111827";
    ctx.font = "700 54px Inter, sans-serif";
    const nextYAfterKegiatan = wrapText(ctx, certificateData.kegiatan, CANVAS_WIDTH / 2, 1060, 2200, 68);

    // Teks Penyelenggara & Tanggal dihitung relatif dari baris terakhir kegiatan
    ctx.fillStyle = "#4B5563";
    ctx.font = "400 38px Inter, sans-serif";
    const organizerY = Math.max(nextYAfterKegiatan + 30, 1180);
    
    wrapText(ctx, "yang diselenggarakan oleh Badan Riset dan Inovasi Daerah Kota Makassar", CANVAS_WIDTH / 2, organizerY, 2300, 52);
    ctx.fillText(`pada tanggal ${certificateData.tanggal} di ${certificateData.tempat}`, CANVAS_WIDTH / 2, organizerY + 58);

    // 10. Footer Kiri: QR Code Verifikasi
    const qrSize = 300;
    const qrX = 360;
    const qrY = 1600;

    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(qrX, qrY, qrSize, qrSize);
    ctx.strokeStyle = "#D1D5DB";
    ctx.lineWidth = 4;
    ctx.strokeRect(qrX, qrY, qrSize, qrSize);

    if (imgQr) {
        ctx.drawImage(imgQr, qrX + 20, qrY + 20, qrSize - 40, qrSize - 40);
    }

    ctx.textAlign = "center";
    ctx.fillStyle = "#6B7280";
    ctx.font = "400 28px Inter, sans-serif";
    ctx.fillText("Scan untuk verifikasi keaslian", qrX + (qrSize / 2), qrY + qrSize + 50);

    // 11. Footer Kanan: Teks Pejabat (Layer Bawah)
    const ttdCenterX = CANVAS_WIDTH - 550;
    const ttdBaseY = 1580;

    ctx.fillStyle = "#374151";
    ctx.font = "400 36px Inter, sans-serif";
    ctx.fillText(`Makassar, ${certificateData.tanggal}`, ttdCenterX, ttdBaseY);

    ctx.fillStyle = "#111827";
    ctx.font = "700 44px Inter, sans-serif";
    ctx.fillText("Haidil Adha, S.Sos., M.M.", ttdCenterX, ttdBaseY + 260);

    ctx.fillStyle = "#4B5563";
    ctx.font = "400 32px Inter, sans-serif";
    ctx.fillText("Kepala Badan Riset dan Inovasi Daerah", ttdCenterX, ttdBaseY + 315);
    ctx.fillText("Kota Makassar", ttdCenterX, ttdBaseY + 360);

    // 12. Footer Kanan: TTD & Stempel (Layer Depan / In Front of Text)
    if (imgTtd) {
        ctx.save();
        
        const ttdW = 620;
        const ttdH = 360;
        
        const ttdX = ttdCenterX - (ttdW / 2) - 60; // Offset posisi X
        const ttdY = ttdBaseY + 15;

        drawImageContained(ctx, imgTtd, ttdX, ttdY, ttdW, ttdH);
        
        ctx.restore();
    }
}

// Handler Ekspor
async function downloadCertificate(format) {
    const filename = `Sertifikat_${certificateData.nomor.replace(/[\/\\:]/g, '_')}`;
    
    if (format === 'png') {
        const link = document.createElement('a');
        link.download = `${filename}.png`;
        link.href = canvas.toDataURL("image/png", 1.0);
        link.click();
    } else if (format === 'pdf') {
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4'
        });

        const imgData = canvas.toDataURL('image/jpeg', 0.98);
        pdf.addImage(imgData, 'JPEG', 0, 0, 297, 210, undefined, 'FAST');
        pdf.save(`${filename}.pdf`);
    }
}

// Render kanvas saat halaman selesai dimuat
window.addEventListener('load', renderCertificate);
</script>

</body>
</html>