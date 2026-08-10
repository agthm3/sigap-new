@extends('layouts.page')

@section('title', 'Split PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<!-- PDF Processing Client-Side Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
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
                Kategori Organize
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                Pisahkan / Split <span class="text-white/90">PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Pisahkan halaman PDF berdasarkan rentang pilihan atau ekstrak setiap lembar menjadi file terpisah secara instan.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh proses pemisahan dokumen dilakukan di RAM browser Anda. Berkas PDF tidak pernah dikirim ke server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    ✂️
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file dari komputer Anda</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Workspace Live Preview & Options -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <!-- Action Header Toolbar -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-gray-200 pb-4">
                    <div>
                        <h4 id="file-info-title" class="text-sm font-bold text-gray-900">Dokumen Terpilih</h4>
                        <p id="file-info-sub" class="text-xs text-gray-500">Pilih mode pemisahan halaman di bawah ini.</p>
                    </div>

                    <button type="button" id="btn-reset-file" class="px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                        Ganti File
                    </button>
                </div>

                <!-- Mode Selection Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Mode 1: Range Split -->
                    <label class="relative flex flex-col p-4 rounded-xl border-2 border-maroon bg-maroon/5 cursor-pointer transition shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-maroon">1. Pisahkan Berdasarkan Rentang</span>
                            <input type="radio" name="split_mode" value="range" checked class="accent-maroon">
                        </div>
                        <p class="text-xs text-gray-600 mb-3">Ekstrak rentang halaman tertentu menjadi satu atau beberapa dokumen baru.</p>
                        
                        <div class="space-y-2 mt-auto">
                            <input type="text" id="split-range-input" placeholder="Contoh: 1-3, 5, 7-10" class="w-full rounded-xl border-gray-300 text-xs py-2 px-3 focus:border-maroon focus:ring-maroon font-semibold bg-white">
                            <p class="text-[10px] text-gray-500">Gunakan tanda koma untuk memisahkan rentang halaman.</p>
                        </div>
                    </label>

                    <!-- Mode 2: Split Every Page -->
                    <label class="relative flex flex-col p-4 rounded-xl border border-gray-200 bg-white hover:border-maroon/50 cursor-pointer transition shadow-2xs">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-bold text-gray-900">2. Pisahkan Semua Halaman</span>
                            <input type="radio" name="split_mode" value="all" class="accent-maroon">
                        </div>
                        <p class="text-xs text-gray-500 mb-3">Setiap lembar halaman akan diubah menjadi berkas PDF mandiri yang dikemas dalam file ZIP.</p>
                        <div class="mt-auto pt-2 text-[11px] font-bold text-maroon">
                            Ekspor otomatis ke format .ZIP
                        </div>
                    </label>
                </div>

                <!-- Live Thumbnail Preview Grid -->
                <div>
                    <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Pratinjau Halaman</h5>
                    <div id="pages-grid" class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                        <!-- Thumbnail cards rendered here -->
                    </div>
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Memproses pemisahan...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="page-summary-text" class="text-xs font-semibold text-gray-600">Total: 0 Halaman</span>
                    <button id="btn-split-pdf" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Pisahkan PDF Sekarang</span>
                        <span>✂️</span>
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
let totalPagesCount = 0;

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const pagesGrid = document.getElementById('pages-grid');
const fileInfoTitle = document.getElementById('file-info-title');
const fileInfoSub = document.getElementById('file-info-sub');
const pageSummaryText = document.getElementById('page-summary-text');
const splitRangeInput = document.getElementById('split-range-input');

const btnResetFile = document.getElementById('btn-reset-file');
const btnSplitPdf = document.getElementById('btn-split-pdf');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

// Radio Button Style Switcher
document.querySelectorAll('input[name="split_mode"]').forEach(radio => {
    radio.addEventListener('change', (e) => {
        document.querySelectorAll('input[name="split_mode"]').forEach(r => {
            const card = r.closest('label');
            if (r.checked) {
                card.className = "relative flex flex-col p-4 rounded-xl border-2 border-maroon bg-maroon/5 cursor-pointer transition shadow-2xs";
            } else {
                card.className = "relative flex flex-col p-4 rounded-xl border border-gray-200 bg-white hover:border-maroon/50 cursor-pointer transition shadow-2xs";
            }
        });
    });
});

// Drag & Drop Handlers
dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.classList.add('border-maroon', 'bg-maroon/5'); });
dropZone.addEventListener('dragleave', () => { dropZone.classList.remove('border-maroon', 'bg-maroon/5'); });
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
    if (e.target.files.length > 0) handleFileSelect(e.target.files[0]);
});

btnResetFile.addEventListener('click', () => {
    currentFile = null;
    originalArrayBuffer = null;
    totalPagesCount = 0;
    fileInput.value = '';
    splitRangeInput.value = '';
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
        totalPagesCount = pdf.numPages;
        
        fileInfoSub.textContent = `Ukuran: ${formatBytes(file.size)} • Total ${totalPagesCount} Halaman`;
        pageSummaryText.textContent = `Total: ${totalPagesCount} Halaman`;
        splitRangeInput.value = `1-${totalPagesCount}`;

        pagesGrid.innerHTML = '';

        for (let i = 1; i <= totalPagesCount; i++) {
            updateProgress(i, totalPagesCount, `Memuat pratinjau halaman ${i} dari ${totalPagesCount}...`);
            
            const page = await pdf.getPage(i);
            const viewport = page.getViewport({ scale: 0.3 });
            
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
        Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: 'PDF ini terlindungi kata sandi atau rusak.' });
    }
}

function renderPageCard(pageNum, imgUrl) {
    const card = document.createElement('div');
    card.className = "rounded-lg border border-gray-200 bg-white p-2 shadow-2xs flex flex-col items-center text-center space-y-1.5";

    card.innerHTML = `
        <div class="w-full h-28 bg-gray-50 rounded border border-gray-100 overflow-hidden flex items-center justify-center p-1">
            <img src="${imgUrl}" class="max-h-full max-w-full object-contain">
        </div>
        <span class="text-[11px] font-extrabold text-gray-700">Hal ${pageNum}</span>
    `;

    pagesGrid.appendChild(card);
}

// Client-Side Split Processing Engine
btnSplitPdf.addEventListener('click', async () => {
    if (!originalArrayBuffer) return;

    const mode = document.querySelector('input[name="split_mode"]:checked').value;
    btnSplitPdf.disabled = true;
    progressContainer.classList.remove('hidden');

    try {
        const srcPdfDoc = await PDFLib.PDFDocument.load(originalArrayBuffer.slice(0));

        if (mode === 'all') {
            // MODE: Split Setiap Halaman ke ZIP
            const zip = new JSZip();
            const baseName = currentFile.name.replace(/\.pdf$/i, '');

            for (let i = 0; i < totalPagesCount; i++) {
                updateProgress(i + 1, totalPagesCount, `Mengekstrak halaman ${i + 1} dari ${totalPagesCount}...`);
                
                const newDoc = await PDFLib.PDFDocument.create();
                const [copiedPage] = await newDoc.copyPages(srcPdfDoc, [i]);
                newDoc.addPage(copiedPage);

                const pdfBytes = await newDoc.save();
                zip.file(`${baseName}_hal_${i + 1}.pdf`, pdfBytes);
            }

            updateProgress(totalPagesCount, totalPagesCount, 'Mengompresi ke file ZIP...');
            const zipBlob = await zip.generateAsync({ type: 'blob' });

            progressContainer.classList.add('hidden');
            btnSplitPdf.disabled = false;

            saveAs(zipBlob, `${baseName}_split_pages.zip`);

        } else {
            // MODE: Split Berdasarkan Rentang Teks
            const rawRange = splitRangeInput.value.trim();
            if (!rawRange) {
                Swal.fire({ icon: 'warning', title: 'Rentang Kosong', text: 'Masukkan rentang halaman terlebih dahulu.' });
                btnSplitPdf.disabled = false;
                progressContainer.classList.add('hidden');
                return;
            }

            const targetIndices = parsePageRanges(rawRange, totalPagesCount);
            if (targetIndices.length === 0) {
                Swal.fire({ icon: 'error', title: 'Rentang Tidak Valid', text: 'Masukkan nomor halaman yang sesuai dengan dokumen.' });
                btnSplitPdf.disabled = false;
                progressContainer.classList.add('hidden');
                return;
            }

            updateProgress(50, 100, 'Menyusun berkas PDF terpisah...');
            const newDoc = await PDFLib.PDFDocument.create();
            const copiedPages = await newDoc.copyPages(srcPdfDoc, targetIndices);
            copiedPages.forEach(p => newDoc.addPage(p));

            const finalBytes = await newDoc.save();
            const finalBlob = new Blob([finalBytes], { type: 'application/pdf' });

            progressContainer.classList.add('hidden');
            btnSplitPdf.disabled = false;

            const baseName = currentFile.name.replace(/\.pdf$/i, '');
            saveAs(finalBlob, `${baseName}_split.pdf`);
        }

        Swal.fire({
            icon: 'success',
            title: 'Pemisahan Berhasil!',
            text: 'Dokumen PDF telah dipisahkan.',
            confirmButtonText: 'Selesai',
            confirmButtonColor: '#7a2222',
        });

    } catch (err) {
        console.error(err);
        btnSplitPdf.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Memisah PDF', text: 'Terjadi kesalahan saat memproses pemisahan halaman.' });
    }
});

function parsePageRanges(rangeStr, maxPages) {
    const indices = new Set();
    const parts = rangeStr.split(',');

    parts.forEach(part => {
        part = part.trim();
        if (part.includes('-')) {
            const [start, end] = part.split('-').map(Number);
            if (start && end && start <= end) {
                for (let i = start; i <= end; i++) {
                    if (i >= 1 && i <= maxPages) indices.add(i - 1);
                }
            }
        } else {
            const num = Number(part);
            if (num >= 1 && num <= maxPages) indices.add(num - 1);
        }
    });

    return Array.from(indices);
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