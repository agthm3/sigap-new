@extends('layouts.page')

@section('title', 'Add Watermark PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<!-- PDF Processing Client-Side Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
</script>
@endpush

@section('content')

<!-- Header Maroon Section -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-[0.2em] mb-3">
                Kategori Security & Utilities
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                Tambah <span class="text-white/90">Watermark PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Sematkan stempel teks proteksi pada dokumen PDF Anda secara langsung dengan pratinjau live di browser.
            </p>
        </div>
    </div>
</section>

<!-- Content Workspace -->
<section class="py-10 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 space-y-6">

        <!-- Privacy Banner Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm flex items-center gap-3">
            <span class="text-xl">🔒</span>
            <p class="text-xs text-gray-700">
                <strong class="text-maroon font-bold">Privacy First:</strong> Penambahan watermark diproses 100% pada browser Anda. Dokumen tidak pernah dikirim atau disimpan di server SIGAP.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🏷️
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file dari komputer Anda</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Main Interactive Workspace -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                    <div>
                        <h4 id="file-title" class="text-sm font-bold text-gray-900">Pengaturan Watermark</h4>
                        <p id="file-subtitle" class="text-xs text-gray-500">Sesuaikan teks dan tata letak watermark pada panel di bawah.</p>
                    </div>

                    <button type="button" id="btn-reset-file" class="text-xs text-red-600 font-semibold hover:underline">
                        Ganti File
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    
                    <!-- Left Column: Controls Form (5 Cols) -->
                    <div class="lg:col-span-5 space-y-4">
                        
                        <!-- Input Text -->
                        <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                            <label class="block text-xs font-bold text-gray-800">Teks Watermark</label>
                            <input type="text" id="wm-text" value="RAHASIA / CONFIDENTIAL" placeholder="Ketik teks watermark..." 
                                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                        </div>

                        <!-- Typography & Style Options -->
                        <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-4">
                            <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Gaya & Tampilan</h5>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Ukuran Font (<span id="wm-size-val">40</span>px)</label>
                                    <input type="range" id="wm-size" min="10" max="100" value="40" class="w-full accent-maroon">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Transparansi (<span id="wm-opacity-val">40</span>%)</label>
                                    <input type="range" id="wm-opacity" min="10" max="100" value="40" class="w-full accent-maroon">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Rotasi (<span id="wm-rotation-val">45</span>°)</label>
                                    <input type="range" id="wm-rotation" min="0" max="360" value="45" step="15" class="w-full accent-maroon">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Warna Teks</label>
                                    <select id="wm-color" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                                        <option value="#7a2222" selected>Maroon SIGAP</option>
                                        <option value="#000000">Hitam (Black)</option>
                                        <option value="#dc2626">Merah (Red)</option>
                                        <option value="#4b5563">Abu-abu (Gray)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Target Page Selection -->
                        <div class="rounded-xl border border-gray-200 bg-white p-4 space-y-3">
                            <label class="block text-xs font-bold text-gray-800">Terapkan Pada Halaman</label>
                            <select id="wm-pages-option" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                                <option value="all" selected>Semua Halaman</option>
                                <option value="odd">Halaman Ganjil Saja</option>
                                <option value="even">Halaman Genap Saja</option>
                            </select>
                        </div>

                    </div>

                    <!-- Right Column: Live Interactive Preview Canvas (7 Cols) -->
                    <div class="lg:col-span-7 flex flex-col items-center justify-center bg-gray-100/70 border border-gray-200 rounded-2xl p-4 min-h-[420px] relative overflow-hidden">
                        
                        <div class="flex items-center justify-between w-full mb-3 px-2 text-xs font-semibold text-gray-600">
                            <span>Pratinjau Live (Halaman 1)</span>
                            <span id="preview-zoom-label" class="text-[11px] bg-white px-2 py-0.5 rounded border border-gray-200">Scale: Auto</span>
                        </div>

                        <!-- Canvas Stacking Container -->
                        <div class="relative shadow-md rounded border border-gray-300 bg-white overflow-hidden max-w-full">
                            <!-- Base PDF Render Canvas -->
                            <canvas id="pdf-preview-canvas" class="block max-w-full h-auto"></canvas>
                            <!-- Live Overlay Canvas for Watermark -->
                            <canvas id="wm-overlay-canvas" class="absolute inset-0 pointer-events-none"></canvas>
                        </div>

                    </div>

                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Menambahkan watermark...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="page-count-summary" class="text-xs font-semibold text-gray-600">Total: 0 Halaman</span>
                    <button id="btn-process-wm" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Terapkan & Unduh PDF</span>
                        <span>🏷️</span>
                    </button>
                </div>

            </div>

        </div>

        <div class="flex justify-between items-center text-xs text-gray-500">
            <a href="{{ route('sigap-pdf.landing') }}" class="hover:text-maroon font-semibold">← Kembali ke Katalog Tools</a>
            <span>SIGAP PDF Client-Side Engine</span>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
let currentFile = null;
let originalArrayBuffer = null;
let pdfDocJs = null;
let renderViewport = null;

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const fileTitle = document.getElementById('file-title');
const fileSubtitle = document.getElementById('file-subtitle');
const pageCountSummary = document.getElementById('page-count-summary');
const btnResetFile = document.getElementById('btn-reset-file');
const btnProcessWm = document.getElementById('btn-process-wm');

// Form Controls
const wmTextInput = document.getElementById('wm-text');
const wmSizeInput = document.getElementById('wm-size');
const wmSizeVal = document.getElementById('wm-size-val');
const wmOpacityInput = document.getElementById('wm-opacity');
const wmOpacityVal = document.getElementById('wm-opacity-val');
const wmRotationInput = document.getElementById('wm-rotation');
const wmRotationVal = document.getElementById('wm-rotation-val');
const wmColorInput = document.getElementById('wm-color');
const wmPagesOption = document.getElementById('wm-pages-option');

// Canvas Elements
const pdfCanvas = document.getElementById('pdf-preview-canvas');
const overlayCanvas = document.getElementById('wm-overlay-canvas');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

// Event Listeners for Live Controls
wmTextInput.addEventListener('input', drawLiveWatermarkOverlay);
wmColorInput.addEventListener('change', drawLiveWatermarkOverlay);
wmPagesOption.addEventListener('change', drawLiveWatermarkOverlay);

wmSizeInput.addEventListener('input', (e) => {
    wmSizeVal.textContent = e.target.value;
    drawLiveWatermarkOverlay();
});

wmOpacityInput.addEventListener('input', (e) => {
    wmOpacityVal.textContent = e.target.value;
    drawLiveWatermarkOverlay();
});

wmRotationInput.addEventListener('input', (e) => {
    wmRotationVal.textContent = e.target.value;
    drawLiveWatermarkOverlay();
});

// Drag & Drop Handlers
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-maroon', 'bg-maroon/5');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-maroon', 'bg-maroon/5');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-maroon', 'bg-maroon/5');
    if (e.dataTransfer.files.length > 0 && e.dataTransfer.files[0].type === 'application/pdf') {
        handleFileSelect(e.dataTransfer.files[0]);
    } else {
        Swal.fire({ icon: 'error', title: 'File tidak valid', text: 'Pilih berkas berformat PDF.' });
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

btnResetFile.addEventListener('click', () => {
    currentFile = null;
    originalArrayBuffer = null;
    pdfDocJs = null;
    fileInput.value = '';
    dropZone.classList.remove('hidden');
    workspaceContainer.classList.add('hidden');
    progressContainer.classList.add('hidden');
});

async function handleFileSelect(file) {
    currentFile = file;
    fileTitle.textContent = file.name;
    
    dropZone.classList.add('hidden');
    workspaceContainer.classList.remove('hidden');
    progressContainer.classList.remove('hidden');
    updateProgress(10, 100, 'Membaca struktur PDF...');

    try {
        originalArrayBuffer = await file.arrayBuffer();
        pdfDocJs = await pdfjsLib.getDocument({ data: originalArrayBuffer.slice(0) }).promise;
        
        fileSubtitle.textContent = `Ukuran: ${formatBytes(file.size)} • Total ${pdfDocJs.numPages} Halaman`;
        pageCountSummary.textContent = `Total: ${pdfDocJs.numPages} Halaman`;

        // Render Halaman 1 ke Base Canvas
        const page = await pdfDocJs.getPage(1);
        renderViewport = page.getViewport({ scale: 0.8 });

        pdfCanvas.height = renderViewport.height;
        pdfCanvas.width = renderViewport.width;
        overlayCanvas.height = renderViewport.height;
        overlayCanvas.width = renderViewport.width;

        const ctx = pdfCanvas.getContext('2d');
        await page.render({ canvasContext: ctx, viewport: renderViewport }).promise;

        progressContainer.classList.add('hidden');
        drawLiveWatermarkOverlay();

    } catch (err) {
        console.error(err);
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: 'Terjadi kesalahan saat memproses PDF.' });
    }
}

function drawLiveWatermarkOverlay() {
    if (!renderViewport) return;

    const ctx = overlayCanvas.getContext('2d');
    ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);

    const text = wmTextInput.value || '';
    if (!text) return;

    const size = parseInt(wmSizeInput.value) * 0.8;
    const opacity = parseInt(wmOpacityInput.value) / 100;
    const rotationDeg = parseInt(wmRotationInput.value);
    const color = wmColorInput.value;

    ctx.save();
    
    // Posisikan ke tengah canvas
    const centerX = overlayCanvas.width / 2;
    const centerY = overlayCanvas.height / 2;
    ctx.translate(centerX, centerY);

    // Rotasi dalam Radian
    ctx.rotate((rotationDeg * Math.PI) / 180);

    // Text Attributes
    ctx.font = `bold ${size}px Inter, sans-serif`;
    ctx.fillStyle = color;
    ctx.globalAlpha = opacity;
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    ctx.fillText(text, 0, 0);
    ctx.restore();
}

// Client-Side Watermark Processing Engine (pdf-lib)
btnProcessWm.addEventListener('click', async () => {
    if (!originalArrayBuffer) return;

    const text = wmTextInput.value.trim();
    if (!text) {
        Swal.fire({ icon: 'warning', title: 'Teks Kosong', text: 'Masukkan teks watermark terlebih dahulu.' });
        return;
    }

    btnProcessWm.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(0, 100, 'Menyiapkan dokumen...');

    try {
        const pdfDoc = await PDFLib.PDFDocument.load(originalArrayBuffer.slice(0));
        const font = await pdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
        const pages = pdfDoc.getPages();
        const totalPages = pages.length;

        const fontSize = parseInt(wmSizeInput.value);
        const opacity = parseInt(wmOpacityInput.value) / 100;
        const rotationAngle = parseInt(wmRotationInput.value);
        const hexColor = wmColorInput.value;
        const colorRgb = hexToRgbNormalized(hexColor);
        const targetOption = wmPagesOption.value;

        for (let i = 0; i < totalPages; i++) {
            const pageNum = i + 1;

            // Filter Halaman
            if (targetOption === 'odd' && pageNum % 2 === 0) continue;
            if (targetOption === 'even' && pageNum % 2 !== 0) continue;

            updateProgress(pageNum, totalPages, `Menambahkan watermark pada halaman ${pageNum} dari ${totalPages}...`);

            const page = pages[i];
            const { width, height } = page.getSize();

            const textWidth = font.widthOfTextAtSize(text, fontSize);
            const textHeight = font.heightAtSize(fontSize);

            page.drawText(text, {
                x: width / 2 - textWidth / 2,
                y: height / 2 - textHeight / 2,
                size: fontSize,
                font: font,
                color: PDFLib.rgb(colorRgb.r, colorRgb.g, colorRgb.b),
                opacity: opacity,
                rotate: PDFLib.degrees(rotationAngle),
            });
        }

        updateProgress(totalPages, totalPages, 'Menyusun berkas PDF akhir...');
        const pdfBytes = await pdfDoc.save();
        const finalBlob = new Blob([pdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnProcessWm.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Watermark Berhasil Ditambahkan!',
            text: 'Dokumen Anda siap diunduh.',
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `watermarked_${currentFile.name}`);
            }
        });

    } catch (err) {
        console.error(err);
        btnProcessWm.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Menambahkan Watermark', text: 'Terjadi kesalahan saat memproses dokumen.' });
    }
});

function hexToRgbNormalized(hex) {
    hex = hex.replace('#', '');
    const r = parseInt(hex.substring(0, 2), 16) / 255;
    const g = parseInt(hex.substring(2, 4), 16) / 255;
    const b = parseInt(hex.substring(4, 6), 16) / 255;
    return { r, g, b };
}

function updateProgress(current, total, statusText) {
    const percent = Math.round((current / total) * 100);
    progressBar.style.width = `${percent}%`;
    progressPercent.textContent = `${percent}%`;
    progressStatus.textContent = statusText;
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}
</script>
@endpush