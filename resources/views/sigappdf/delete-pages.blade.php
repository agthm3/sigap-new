@extends('layouts.page')

@section('title', 'Hapus Halaman PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
    .page-card { transition: all 0.2s ease-in-out; }
    .page-card.selected { border-color: #dc2626 !important; background-color: #fef2f2 !important; }
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
                Hapus <span class="text-white/90">Halaman PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Pilih dan hapus halaman yang tidak diperlukan dari dokumen PDF Anda secara visual, 100% diproses langsung di browser.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh pemotongan dan penghapusan halaman dilakukan di RAM browser Anda. Berkas PDF tidak pernah dikirim ke server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🗑️
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file dari komputer Anda</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Workspace Live Preview & Page Selection -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <!-- Action Header Toolbar -->
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-gray-200 pb-4">
                    <div>
                        <h4 id="file-info-title" class="text-sm font-bold text-gray-900">Pilih Halaman yang Ingin Dihapus</h4>
                        <p id="file-info-sub" class="text-xs text-gray-500">Klik halaman di bawah untuk menandai/batal menandai halaman yang akan dibuang.</p>
                    </div>

                    <!-- Quick Range Input & Actions -->
                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1.5">
                            <input type="text" id="range-input" placeholder="Contoh: 1, 3, 5-7" class="rounded-xl border-gray-300 text-xs py-1.5 px-3 focus:border-maroon focus:ring-maroon w-36">
                            <button type="button" id="btn-select-range" class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition">
                                Pilih
                            </button>
                        </div>

                        <button type="button" id="btn-deselect-all" class="px-3 py-1.5 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
                            Batal Semua
                        </button>

                        <button type="button" id="btn-reset-file" class="px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                            Ganti File
                        </button>
                    </div>
                </div>

                <!-- Live Pages Grid Container -->
                <div id="pages-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Cards will be injected here via JavaScript -->
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Memproses penghapusan...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="summary-selected-text" class="text-xs font-semibold text-gray-600">0 Halaman Dihapus • Tersisa 0 Halaman</span>
                    <button id="btn-delete-pages" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Hapus Halaman & Unduh</span>
                        <span>🗑️</span>
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
let pagesToDelete = new Set(); // Menyimpan nomor halaman yang ditandai hapus (1-based index)

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const pagesGrid = document.getElementById('pages-grid');
const fileInfoTitle = document.getElementById('file-info-title');
const fileInfoSub = document.getElementById('file-info-sub');
const rangeInput = document.getElementById('range-input');
const btnSelectRange = document.getElementById('btn-select-range');
const btnDeselectAll = document.getElementById('btn-deselect-all');
const btnResetFile = document.getElementById('btn-reset-file');
const btnDeletePages = document.getElementById('btn-delete-pages');
const summarySelectedText = document.getElementById('summary-selected-text');

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
    if (e.target.files.length > 0) handleFileSelect(e.target.files[0]);
});

btnResetFile.addEventListener('click', () => {
    currentFile = null;
    originalArrayBuffer = null;
    totalPagesCount = 0;
    pagesToDelete.clear();
    fileInput.value = '';
    rangeInput.value = '';
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
        pagesGrid.innerHTML = '';
        pagesToDelete.clear();

        for (let i = 1; i <= totalPagesCount; i++) {
            updateProgress(i, totalPagesCount, `Memuat pratinjau halaman ${i} dari ${totalPagesCount}...`);
            
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
        updateSummaryText();

    } catch (err) {
        console.error(err);
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: 'PDF ini terlindungi kata sandi atau rusak.' });
    }
}

function renderPageCard(pageNum, imgUrl) {
    const card = document.createElement('div');
    card.className = "page-card rounded-xl border-2 border-gray-200 bg-white p-3 shadow-2xs flex flex-col items-center justify-between space-y-2 cursor-pointer relative group";
    card.setAttribute('id', `page-card-${pageNum}`);
    card.setAttribute('onclick', `togglePageSelection(${pageNum})`);

    card.innerHTML = `
        <div class="flex items-center justify-between w-full border-b border-gray-100 pb-1.5">
            <span class="text-xs font-extrabold text-gray-700">Hal ${pageNum}</span>
            <span id="badge-status-${pageNum}" class="text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Simpan</span>
        </div>

        <div class="w-full h-36 bg-gray-50 rounded-lg border border-gray-100 overflow-hidden flex items-center justify-center p-1 relative">
            <img src="${imgUrl}" class="max-h-full max-w-full object-contain">
            <div id="overlay-delete-${pageNum}" class="absolute inset-0 bg-red-500/20 backdrop-blur-[1px] flex items-center justify-center hidden">
                <span class="bg-red-600 text-white font-black text-xl w-9 h-9 rounded-full flex items-center justify-center shadow-md">✕</span>
            </div>
        </div>

        <p id="label-action-${pageNum}" class="text-[11px] font-bold text-gray-400 group-hover:text-red-600 transition">Klik untuk Hapus</p>
    `;

    pagesGrid.appendChild(card);
}

function togglePageSelection(pageNum) {
    if (pagesToDelete.has(pageNum)) {
        pagesToDelete.delete(pageNum);
    } else {
        pagesToDelete.add(pageNum);
    }
    updateCardUI(pageNum);
    updateSummaryText();
}

function updateCardUI(pageNum) {
    const card = document.getElementById(`page-card-${pageNum}`);
    const badge = document.getElementById(`badge-status-${pageNum}`);
    const overlay = document.getElementById(`overlay-delete-${pageNum}`);
    const label = document.getElementById(`label-action-${pageNum}`);

    if (pagesToDelete.has(pageNum)) {
        card.classList.add('selected');
        badge.textContent = 'Dihapus';
        badge.className = "text-[10px] font-bold bg-red-600 text-white px-2 py-0.5 rounded-full";
        overlay.classList.remove('hidden');
        label.textContent = 'Akan Dihapus';
        label.className = "text-[11px] font-bold text-red-600";
    } else {
        card.classList.remove('selected');
        badge.textContent = 'Simpan';
        badge.className = "text-[10px] font-bold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full";
        overlay.classList.add('hidden');
        label.textContent = 'Klik untuk Hapus';
        label.className = "text-[11px] font-bold text-gray-400 group-hover:text-red-600";
    }
}

// Select via Text Range (e.g. 1, 3, 5-7)
btnSelectRange.addEventListener('click', () => {
    const rawVal = rangeInput.value.trim();
    if (!rawVal) return;

    const parts = rawVal.split(',');
    parts.forEach(part => {
        part = part.trim();
        if (part.includes('-')) {
            const [start, end] = part.split('-').map(Number);
            if (start && end && start <= end) {
                for (let i = start; i <= end; i++) {
                    if (i >= 1 && i <= totalPagesCount) pagesToDelete.add(i);
                }
            }
        } else {
            const num = Number(part);
            if (num >= 1 && num <= totalPagesCount) pagesToDelete.add(num);
        }
    });

    for (let i = 1; i <= totalPagesCount; i++) {
        updateCardUI(i);
    }
    updateSummaryText();
});

btnDeselectAll.addEventListener('click', () => {
    pagesToDelete.clear();
    rangeInput.value = '';
    for (let i = 1; i <= totalPagesCount; i++) {
        updateCardUI(i);
    }
    updateSummaryText();
});

function updateSummaryText() {
    const deleteCount = pagesToDelete.size;
    const remainingCount = totalPagesCount - deleteCount;
    summarySelectedText.textContent = `${deleteCount} Halaman Dihapus • Tersisa ${remainingCount} Halaman`;
}

// Client-Side Delete Processing Engine (pdf-lib)
btnDeletePages.addEventListener('click', async () => {
    if (!originalArrayBuffer) return;

    if (pagesToDelete.size === 0) {
        Swal.fire({ icon: 'warning', title: 'Belum Ada Halaman Terpilih', text: 'Klik halaman yang ingin dihapus terlebih dahulu.' });
        return;
    }

    if (pagesToDelete.size >= totalPagesCount) {
        Swal.fire({ icon: 'error', title: 'Tidak Dapat Menghapus Semua', text: 'Dokumen PDF harus menyisakan setidaknya 1 halaman.' });
        return;
    }

    btnDeletePages.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(30, 100, 'Menyusun halaman sisa...');

    try {
        const srcPdfDoc = await PDFLib.PDFDocument.load(originalArrayBuffer.slice(0));
        const newPdfDoc = await PDFLib.PDFDocument.create();

        // Tentukan indeks halaman yang akan DIPERTAHANKAN (0-based)
        const keepIndices = [];
        for (let i = 1; i <= totalPagesCount; i++) {
            if (!pagesToDelete.has(i)) {
                keepIndices.push(i - 1);
            }
        }

        updateProgress(60, 100, 'Penyalinan halaman...');
        const copiedPages = await newPdfDoc.copyPages(srcPdfDoc, keepIndices);
        copiedPages.forEach(page => newPdfDoc.addPage(page));

        updateProgress(90, 100, 'Menyusun dokumen PDF baru...');
        const finalPdfBytes = await newPdfDoc.save();
        const finalBlob = new Blob([finalPdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnDeletePages.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Penghapusan Berhasil!',
            text: `${pagesToDelete.size} halaman berhasil dibuang.`,
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `edited_${currentFile.name}`);
            }
        });

    } catch (err) {
        console.error(err);
        btnDeletePages.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Memproses', text: 'Terjadi kesalahan saat memotong halaman PDF.' });
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