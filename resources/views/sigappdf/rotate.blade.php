@extends('layouts.page')

@section('title', 'Rotate PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
    .page-card { transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out; }
    .page-card:hover { transform: translateY(-2px); }
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
                Kategori Organize
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                Putar / Rotate <span class="text-white/90">PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Lihat pratinjau halaman secara live dan sesuaikan arah rotasi per-halaman atau seluruh dokumen dengan presisi.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh pratinjau dan rotasi PDF dilakukan langsung di browser Anda (Client-side). Berkas PDF tidak pernah diunggah atau disimpan di server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🔄
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file dari komputer Anda</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Workspace Live Preview & Controls -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <!-- Action Header Toolbar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
                    <div>
                        <h4 id="file-info-title" class="text-sm font-bold text-gray-900">Pratinjau Halaman Live</h4>
                        <p id="file-info-sub" class="text-xs text-gray-500">Klik tombol putar pada halaman individual atau gunakan kontrol global di kanan.</p>
                    </div>

                    <!-- Global Controls -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" onclick="rotateAllPages(-90)" class="px-3 py-1.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition inline-flex items-center gap-1.5 shadow-2xs">
                            <span>↩</span> Putar Semua Kiri
                        </button>
                        <button type="button" onclick="rotateAllPages(90)" class="px-3 py-1.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition inline-flex items-center gap-1.5 shadow-2xs">
                            <span>↪</span> Putar Semua Kanan
                        </button>
                        <button type="button" id="btn-reset-file" class="px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                            Ganti File
                        </button>
                    </div>
                </div>

                <!-- Live Pages Grid -->
                <div id="pages-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Page preview cards will be injected here -->
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Memproses rotasi...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="page-summary-count" class="text-xs font-semibold text-gray-600">Total: 0 Halaman</span>
                    <button id="btn-save-rotate" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Simpan Hasil PDF</span>
                        <span>💾</span>
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
let pageRotations = []; // Menyimpan rotasi relatif per halaman [0, 90, 180, 270]

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const pagesGrid = document.getElementById('pages-grid');
const fileInfoTitle = document.getElementById('file-info-title');
const fileInfoSub = document.getElementById('file-info-sub');
const btnResetFile = document.getElementById('btn-reset-file');
const btnSaveRotate = document.getElementById('btn-save-rotate');
const pageSummaryCount = document.getElementById('page-summary-count');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

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
    pageRotations = [];
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
    progressContainer.classList.remove('hidden');
    
    try {
        originalArrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: originalArrayBuffer.slice(0) }).promise;
        const numPages = pdf.numPages;
        
        pageRotations = new Array(numPages).fill(0);
        pageSummaryCount.textContent = `Total: ${numPages} Halaman`;
        fileInfoSub.textContent = `Ukuran: ${formatBytes(file.size)} • ${numPages} Halaman`;

        pagesGrid.innerHTML = '';

        for (let i = 1; i <= numPages; i++) {
            updateProgress(i, numPages, `Meresolusi visual halaman ${i} dari ${numPages}...`);
            
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 0.35 });
            
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;
            const imgUrl = canvas.toDataURL('image/jpeg', 0.8);

            renderPageCard(i, imgUrl);
        }

        progressContainer.classList.add('hidden');
    } catch (err) {
        console.error(err);
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: 'PDF ini terlindungi kata sandi atau tidak valid.' });
    }
}

function renderPageCard(pageNum, imgUrl) {
    const card = document.createElement('div');
    card.className = "page-card rounded-xl border border-gray-200 bg-white p-3 shadow-2xs flex flex-col items-center justify-between space-y-3 relative group";
    card.setAttribute('id', `page-card-${pageNum}`);

    card.innerHTML = `
        <!-- Page Badge -->
        <div class="flex items-center justify-between w-full border-b border-gray-100 pb-2">
            <span class="text-xs font-extrabold text-gray-700">Hal ${pageNum}</span>
            <span id="rotation-badge-${pageNum}" class="text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">0°</span>
        </div>

        <!-- Thumbnail Image Canvas Wrap -->
        <div class="w-full h-40 bg-gray-50 rounded-lg border border-gray-100 overflow-hidden flex items-center justify-center p-2">
            <img id="thumb-img-${pageNum}" src="${imgUrl}" class="max-h-full max-w-full object-contain transition-transform duration-300" style="transform: rotate(0deg);">
        </div>

        <!-- Rotate Action Buttons per Page -->
        <div class="flex items-center justify-center gap-2 w-full pt-1">
            <button type="button" onclick="rotateSinglePage(${pageNum}, -90)" class="flex-1 py-1 px-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-maroon hover:text-white text-xs font-semibold transition" title="Putar 90° Kiri">
                ↩ Kiri
            </button>
            <button type="button" onclick="rotateSinglePage(${pageNum}, 90)" class="flex-1 py-1 px-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-maroon hover:text-white text-xs font-semibold transition" title="Putar 90° Kanan">
                ↪ Kanan
            </button>
        </div>
    `;

    pagesGrid.appendChild(card);
}

function rotateSinglePage(pageNum, angle) {
    const index = pageNum - 1;
    pageRotations[index] = (pageRotations[index] + angle) % 360;
    if (pageRotations[index] < 0) pageRotations[index] += 360;

    // Apply Live Rotate CSS Transformation
    const imgEl = document.getElementById(`thumb-img-${pageNum}`);
    const badgeEl = document.getElementById(`rotation-badge-${pageNum}`);

    if (imgEl) {
        imgEl.style.transform = `rotate(${pageRotations[index]}deg)`;
    }
    if (badgeEl) {
        badgeEl.textContent = `${pageRotations[index]}°`;
        if (pageRotations[index] !== 0) {
            badgeEl.className = "text-[10px] font-bold bg-maroon text-white px-2 py-0.5 rounded-full";
        } else {
            badgeEl.className = "text-[10px] font-bold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full";
        }
    }
}

function rotateAllPages(angle) {
    for (let i = 1; i <= pageRotations.length; i++) {
        rotateSinglePage(i, angle);
    }
}

// Client-Side Save PDF Engine
btnSaveRotate.addEventListener('click', async () => {
    if (!originalArrayBuffer) return;

    btnSaveRotate.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(0, 100, 'Memproses orientasi PDF...');

    try {
        const pdfDoc = await PDFLib.PDFDocument.load(originalArrayBuffer.slice(0));
        const pages = pdfDoc.getPages();

        for (let i = 0; i < pages.length; i++) {
            const page = pages[i];
            const addedRotation = pageRotations[i] || 0;
            
            if (addedRotation !== 0) {
                const currentAngle = page.getRotation().angle;
                const newAngle = (currentAngle + addedRotation) % 360;
                page.setRotation(PDFLib.degrees(newAngle));
            }
        }

        updateProgress(80, 100, 'Menyusun file PDF...');
        const pdfBytes = await pdfDoc.save();
        const finalBlob = new Blob([pdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnSaveRotate.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Rotasi Selesai!',
            text: 'Berkas PDF Anda telah berhasil dirotasi.',
            confirmButtonText: 'Unduh PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `rotated_${currentFile.name}`);
            }
        });

    } catch (err) {
        console.error(err);
        btnSaveRotate.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: 'Terjadi kesalahan saat memproses rotasi PDF.' });
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