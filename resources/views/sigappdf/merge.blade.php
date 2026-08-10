@extends('layouts.page')

@section('title', 'Merge PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
    .ghost-card { opacity: 0.4; border-color: #7a2222 !important; background-color: #fdf7f7 !important; }
</style>
<!-- Library Processing Client-Side PDF & Drag-and-Drop Reorder -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

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
                Gabungkan <span class="text-white/90">PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Unggah beberapa dokumen, atur urutan posisi sesuai keinginan Anda, dan gabungkan menjadi satu berkas utuh secara instan.
            </p>
        </div>
    </div>
</section>

<!-- Content Workspace -->
<section class="py-10 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 space-y-6">

        <!-- Privacy Banner Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm flex items-center gap-3">
            <span class="text-xl">🔒</span>
            <p class="text-xs text-gray-700">
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh proses penggabungan dilakukan langsung di memori browser Anda (Client-side). Berkas PDF tidak pernah dikirim atau disimpan di server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🧩
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan Beberapa File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih satu atau banyak berkas dari komputer Anda</p>
                <input type="file" id="file-input" multiple accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Interactive File List & Reordering Container -->
            <div id="workspace-container" class="hidden space-y-5">
                
                <!-- Action Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-gray-200 pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Atur Urutan Berkas</h4>
                        <p class="text-xs text-gray-500">Geser (drag & drop) kartu di bawah atau gunakan tombol panah untuk menentukan urutan file.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('file-input').click()" class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            + Tambah File
                        </button>
                        <button type="button" id="btn-clear-all" class="px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                            Kosongkan
                        </button>
                    </div>
                </div>

                <!-- Sortable Drag & Drop Cards Grid -->
                <div id="file-list-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Cards will be dynamically injected here via JavaScript -->
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Menggabungkan berkas...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Footer Process Action -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="total-summary-text" class="text-xs font-semibold text-gray-600">Total: 0 file</span>
                    <button id="btn-merge" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Gabungkan PDF Sekarang</span>
                        <span>🧩</span>
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
let pdfFiles = []; // Array penampung file { id, file, name, size, pageCount, thumbnail }
let sortableInstance = null;

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const fileListGrid = document.getElementById('file-list-grid');
const btnClearAll = document.getElementById('btn-clear-all');
const btnMerge = document.getElementById('btn-merge');
const totalSummaryText = document.getElementById('total-summary-text');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

// Inisialisasi SortableJS untuk Drag-and-Drop Reorder
document.addEventListener('DOMContentLoaded', () => {
    sortableInstance = new Sortable(fileListGrid, {
        animation: 150,
        ghostClass: 'ghost-card',
        handle: '.drag-handle',
        onEnd: () => {
            syncArrayWithDOM();
        }
    });
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
    if (e.dataTransfer.files.length > 0) {
        handleFilesAdded(Array.from(e.dataTransfer.files));
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFilesAdded(Array.from(e.target.files));
    }
});

async function handleFilesAdded(files) {
    const validPdfs = files.filter(f => f.type === 'application/pdf' || f.name.toLowerCase().endsWith('.pdf'));

    if (validPdfs.length === 0) {
        Swal.fire({ icon: 'error', title: 'Format Tidak Sesuai', text: 'Silakan pilih berkas berformat PDF.' });
        return;
    }

    for (const file of validPdfs) {
        const fileObj = {
            id: 'pdf_' + Math.random().toString(36).substr(2, 9),
            file: file,
            name: file.name,
            size: file.size,
            pageCount: '...',
            thumbnail: null
        };
        pdfFiles.push(fileObj);
        renderCard(fileObj);
        
        // Asynchronous thumbnail generation
        generatePdfMeta(fileObj);
    }

    updateUIState();
    fileInput.value = '';
}

async function generatePdfMeta(fileObj) {
    try {
        const buffer = await fileObj.file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: buffer }).promise;
        fileObj.pageCount = pdf.numPages;

        // Render halaman pertama sebagai thumbnail
        const page = await pdf.getPage(1);
        const viewport = page.getViewport({ scale: 0.3 });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        await page.render({ canvasContext: context, viewport: viewport }).promise;
        fileObj.thumbnail = canvas.toDataURL('image/jpeg', 0.7);

        // Update card element
        const cardThumb = document.getElementById(`thumb_${fileObj.id}`);
        const cardPage = document.getElementById(`pages_${fileObj.id}`);
        if (cardThumb) cardThumb.src = fileObj.thumbnail;
        if (cardPage) cardPage.textContent = `${fileObj.pageCount} Halaman`;
    } catch (err) {
        console.error('Error reading PDF preview:', err);
    }
}

function renderCard(fileObj) {
    const card = document.createElement('div');
    card.setAttribute('data-id', fileObj.id);
    card.className = "rounded-xl border border-gray-200 bg-white p-3 shadow-2xs flex flex-col justify-between space-y-3 hover:border-maroon/50 transition relative group";

    card.innerHTML = `
        <div class="flex items-start gap-3">
            <!-- Drag Handle Icon -->
            <div class="drag-handle cursor-grab active:cursor-grabbing text-gray-400 hover:text-maroon p-1 rounded-md hover:bg-gray-100 shrink-0 mt-1" title="Geser untuk memindahkan urutan">
                ☰
            </div>

            <!-- Thumbnail Preview -->
            <div class="w-12 h-16 bg-gray-100 rounded-md border border-gray-200 overflow-hidden shrink-0 flex items-center justify-center">
                <img id="thumb_${fileObj.id}" src="${fileObj.thumbnail || ''}" class="w-full h-full object-cover ${!fileObj.thumbnail ? 'hidden' : ''}">
                <span class="text-xs text-gray-400 ${fileObj.thumbnail ? 'hidden' : ''}">PDF</span>
            </div>

            <!-- Info File -->
            <div class="min-w-0 flex-1">
                <h5 class="text-xs font-bold text-gray-900 truncate" title="${fileObj.name}">${fileObj.name}</h5>
                <p id="pages_${fileObj.id}" class="text-[11px] text-gray-500 mt-0.5">${fileObj.pageCount} Halaman</p>
                <p class="text-[10px] font-medium text-maroon mt-0.5">${formatBytes(fileObj.size)}</p>
            </div>
        </div>

        <!-- Order Control & Delete Buttons -->
        <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-[11px]">
            <div class="flex items-center gap-1">
                <button type="button" onclick="moveCard('${fileObj.id}', -1)" class="px-2 py-0.5 rounded border border-gray-200 bg-gray-50 hover:bg-maroon hover:text-white transition font-bold" title="Naikkan Urutan">▲</button>
                <button type="button" onclick="moveCard('${fileObj.id}', 1)" class="px-2 py-0.5 rounded border border-gray-200 bg-gray-50 hover:bg-maroon hover:text-white transition font-bold" title="Turunkan Urutan">▼</button>
                <span class="badge-index text-[10px] font-bold text-gray-400 ml-1">#1</span>
            </div>

            <button type="button" onclick="removeFile('${fileObj.id}')" class="text-red-500 hover:text-red-700 font-semibold px-2 py-0.5 rounded hover:bg-red-50 transition">
                Hapus
            </button>
        </div>
    `;

    fileListGrid.appendChild(card);
    updateIndexes();
}

function moveCard(id, direction) {
    const card = fileListGrid.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    if (direction === -1 && card.previousElementSibling) {
        fileListGrid.insertBefore(card, card.previousElementSibling);
    } else if (direction === 1 && card.nextElementSibling) {
        fileListGrid.insertBefore(card.nextElementSibling, card);
    }

    syncArrayWithDOM();
}

function removeFile(id) {
    pdfFiles = pdfFiles.filter(f => f.id !== id);
    const card = fileListGrid.querySelector(`[data-id="${id}"]`);
    if (card) card.remove();
    updateUIState();
    updateIndexes();
}

btnClearAll.addEventListener('click', () => {
    pdfFiles = [];
    fileListGrid.innerHTML = '';
    updateUIState();
});

function syncArrayWithDOM() {
    const cardElements = Array.from(fileListGrid.querySelectorAll('[data-id]'));
    const newOrderedFiles = [];
    
    cardElements.forEach(el => {
        const id = el.getAttribute('data-id');
        const found = pdfFiles.find(f => f.id === id);
        if (found) newOrderedFiles.push(found);
    });

    pdfFiles = newOrderedFiles;
    updateIndexes();
}

function updateIndexes() {
    const cardElements = Array.from(fileListGrid.querySelectorAll('[data-id]'));
    cardElements.forEach((el, idx) => {
        const indexSpan = el.querySelector('.badge-index');
        if (indexSpan) indexSpan.textContent = `#${idx + 1}`;
    });
}

function updateUIState() {
    if (pdfFiles.length > 0) {
        dropZone.classList.add('hidden');
        workspaceContainer.classList.remove('hidden');
        totalSummaryText.textContent = `Total: ${pdfFiles.length} File terdaftar`;
    } else {
        dropZone.classList.remove('hidden');
        workspaceContainer.classList.add('hidden');
    }
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Client-Side Merge Processing Engine
btnMerge.addEventListener('click', async () => {
    if (pdfFiles.length < 2) {
        Swal.fire({
            icon: 'warning',
            title: 'Minimal 2 Berkas',
            text: 'Tambahkan sekurang-kurangnya 2 file PDF untuk digabungkan.'
        });
        return;
    }

    btnMerge.disabled = true;
    progressContainer.classList.remove('hidden');

    try {
        const mergedPdf = await PDFLib.PDFDocument.create();
        const totalFiles = pdfFiles.length;

        for (let i = 0; i < totalFiles; i++) {
            const item = pdfFiles[i];
            updateProgress(i + 1, totalFiles, `Memproses file ${i + 1} dari ${totalFiles}: ${item.name}`);

            const arrayBuffer = await item.file.arrayBuffer();
            const srcPdf = await PDFLib.PDFDocument.load(arrayBuffer);
            const copiedPages = await mergedPdf.copyPages(srcPdf, srcPdf.getPageIndices());
            
            copiedPages.forEach((page) => mergedPdf.addPage(page));
        }

        updateProgress(totalFiles, totalFiles, 'Menyusun dokumen gabungan final...');
        const mergedPdfBytes = await mergedPdf.save();
        const finalBlob = new Blob([mergedPdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnMerge.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Penggabungan Berhasil!',
            html: `
                <div class="text-left text-xs space-y-2 mt-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                    <p>Total File Digabung: <b>${totalFiles} File</b></p>
                    <p>Ukuran Berkas Akhir: <b class="text-emerald-700">${formatBytes(finalBlob.size)}</b></p>
                </div>
            `,
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `sigap_merged_${Date.now()}.pdf`);
            }
        });

    } catch (error) {
        console.error('Error merging PDFs:', error);
        btnMerge.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Menggabungkan', text: 'Terjadi kesalahan saat memproses berkas PDF.' });
    }
});

function updateProgress(current, total, statusText) {
    const percent = Math.round((current / total) * 100);
    progressBar.style.width = `${percent}%`;
    progressPercent.textContent = `${percent}%`;
    progressStatus.textContent = statusText;
}
</script>
@endpush