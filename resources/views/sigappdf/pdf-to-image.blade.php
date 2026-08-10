@extends('layouts.page')

@section('title', 'PDF ke Gambar (JPG/PNG) — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<!-- Client-Side PDF Rendering & Zip Archive Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
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
                Kategori Convert
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                PDF → Gambar <span class="text-white/90">(JPG/PNG)</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Ekstrak dan ubah setiap halaman PDF menjadi gambar resolusi tinggi, 100% diproses langsung di browser Anda tanpa upload.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Konversi PDF ke gambar diproses sepenuhnya oleh engine rendering di perangkat Anda. Berkas PDF tidak pernah dikirim atau disimpan di server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    📸
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file dari komputer Anda</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Workspace Live Pages Grid & Options -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <!-- Action Header Toolbar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
                    <div>
                        <h4 id="file-info-title" class="text-sm font-bold text-gray-900">PDF Terpilih</h4>
                        <p id="file-info-sub" class="text-xs text-gray-500">Pilih format ekspor dan opsi resolusi gambar di bawah.</p>
                    </div>

                    <button type="button" id="btn-reset-file" class="px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                        Ganti File
                    </button>
                </div>

                <!-- Export Options Controls -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Format Gambar Output</label>
                        <select id="export-format" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                            <option value="jpeg" selected>JPG (Ukuran Ringan)</option>
                            <option value="png">PNG (Kualitas Tinggi / Tanpa Kompresi)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Kualitas / Skala Resolusi</label>
                        <select id="export-scale" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                            <option value="1.0">Standar (100% - Cepat)</option>
                            <option value="1.5" selected>Sedang (150% - Direkomendasikan)</option>
                            <option value="2.0">Tinggi (200% - Tajam / HD)</option>
                        </select>
                    </div>
                </div>

                <!-- Live Rendered Pages Grid -->
                <div id="pages-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Page preview cards will be injected here via JavaScript -->
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Mengekstrak gambar...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="page-summary-count" class="text-xs font-semibold text-gray-600">Total: 0 Halaman</span>
                    <button id="btn-download-all" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Unduh Semua Gambar (ZIP)</span>
                        <span>📦</span>
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
let renderedImages = []; // Array object { pageNum, dataUrl }

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const pagesGrid = document.getElementById('pages-grid');
const fileInfoTitle = document.getElementById('file-info-title');
const fileInfoSub = document.getElementById('file-info-sub');
const pageSummaryCount = document.getElementById('page-summary-count');
const btnResetFile = document.getElementById('btn-reset-file');
const btnDownloadAll = document.getElementById('btn-download-all');

const exportFormatSelect = document.getElementById('export-format');
const exportScaleSelect = document.getElementById('export-scale');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

// Re-render preview saat opsi skala/format diubah
exportScaleSelect.addEventListener('change', () => {
    if (pdfDocJs) renderAllPages();
});

// Drag & Drop Event Listeners
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
    renderedImages = [];
    fileInput.value = '';
    pagesGrid.innerHTML = '';
    dropZone.classList.remove('hidden');
    workspaceContainer.classList.add('hidden');
    progressContainer.classList.add('hidden');
});

async function handleFileSelect(file) {
    currentFile = file;
    fileInfoTitle.textContent = file.name;
    
    dropZone.classList.add('hidden');
    workspaceContainer.classList.remove('hidden');
    
    try {
        originalArrayBuffer = await file.arrayBuffer();
        pdfDocJs = await pdfjsLib.getDocument({ data: originalArrayBuffer.slice(0) }).promise;
        
        fileInfoSub.textContent = `Ukuran: ${formatBytes(file.size)} • Total ${pdfDocJs.numPages} Halaman`;
        pageSummaryCount.textContent = `Total: ${pdfDocJs.numPages} Halaman`;

        await renderAllPages();

    } catch (err) {
        console.error(err);
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: 'PDF ini terlindungi kata sandi atau tidak valid.' });
    }
}

async function renderAllPages() {
    if (!pdfDocJs) return;

    progressContainer.classList.remove('hidden');
    pagesGrid.innerHTML = '';
    renderedImages = [];

    const numPages = pdfDocJs.numPages;
    const scale = parseFloat(exportScaleSelect.value);
    const format = exportFormatSelect.value;
    const mimeType = format === 'png' ? 'image/png' : 'image/jpeg';

    for (let i = 1; i <= numPages; i++) {
        updateProgress(i, numPages, `Mengekstrak halaman ${i} dari ${numPages}...`);

        const page = await pdfDocJs.getPage(i);
        const viewport = page.getViewport({ scale: scale });

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        await page.render({ canvasContext: context, viewport: viewport }).promise;

        const dataUrl = canvas.toDataURL(mimeType, 0.92);
        renderedImages.push({ pageNum: i, dataUrl: dataUrl });

        renderPageCard(i, dataUrl, format);
    }

    progressContainer.classList.add('hidden');
}

function renderPageCard(pageNum, dataUrl, format) {
    const ext = format === 'png' ? 'PNG' : 'JPG';
    const card = document.createElement('div');
    card.className = "rounded-xl border border-gray-200 bg-white p-3 shadow-2xs flex flex-col items-center justify-between space-y-3 relative group";

    card.innerHTML = `
        <div class="flex items-center justify-between w-full border-b border-gray-100 pb-2">
            <span class="text-xs font-extrabold text-gray-700">Halaman ${pageNum}</span>
            <span class="text-[10px] font-bold bg-maroon/10 text-maroon px-2 py-0.5 rounded-full">${ext}</span>
        </div>

        <div class="w-full h-36 bg-gray-50 rounded-lg border border-gray-100 overflow-hidden flex items-center justify-center p-1">
            <img src="${dataUrl}" class="max-h-full max-w-full object-contain">
        </div>

        <button type="button" onclick="downloadSinglePage(${pageNum})" class="w-full py-1.5 px-2 rounded-lg bg-gray-100 hover:bg-maroon hover:text-white text-gray-700 text-xs font-semibold transition">
            Unduh Gambar
        </button>
    `;

    pagesGrid.appendChild(card);
}

function downloadSinglePage(pageNum) {
    const item = renderedImages.find(img => img.pageNum === pageNum);
    if (!item) return;

    const format = exportFormatSelect.value;
    const ext = format === 'png' ? 'png' : 'jpg';
    const baseName = currentFile.name.replace(/\.pdf$/i, '');
    
    saveAs(item.dataUrl, `${baseName}_hal_${pageNum}.${ext}`);
}

// Download All Pages as ZIP Package
btnDownloadAll.addEventListener('click', async () => {
    if (renderedImages.length === 0) return;

    btnDownloadAll.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(0, 100, 'Membuat arsip ZIP...');

    try {
        const zip = new JSZip();
        const format = exportFormatSelect.value;
        const ext = format === 'png' ? 'png' : 'jpg';
        const baseName = currentFile.name.replace(/\.pdf$/i, '');

        renderedImages.forEach((img) => {
            // Unpack Base64 dataURL
            const base64Data = img.dataUrl.split(',')[1];
            zip.file(`${baseName}_hal_${img.pageNum}.${ext}`, base64Data, { base64: true });
        });

        updateProgress(80, 100, 'Mengompresi berkas ZIP...');
        const content = await zip.generateAsync({ type: 'blob' });

        progressContainer.classList.add('hidden');
        btnDownloadAll.disabled = false;

        saveAs(content, `${baseName}_images.zip`);

    } catch (err) {
        console.error(err);
        btnDownloadAll.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membuat ZIP', text: 'Terjadi kesalahan saat mengekspor gambar.' });
    }
});

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