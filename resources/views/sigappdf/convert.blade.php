@extends('layouts.page')

@section('title', ($title ?? 'Convert Gambar ke PDF') . ' — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
    .ghost-card { opacity: 0.4; border-color: #7a2222 !important; background-color: #fdf7f7 !important; }
</style>
<!-- PDF Processing Client-Side Libraries -->
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
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
                Gambar <span class="text-white/90">(JPG/PNG) → PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Ubah foto atau dokumen berkas gambar menjadi satu dokumen PDF rapi, 100% diproses langsung di browser Anda.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh proses penggabungan dan konversi gambar dilakukan langsung pada RAM browser Anda. Foto tidak pernah diunggah atau disimpan di server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🖼️
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File Gambar di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">Mendukung format JPG, JPEG, PNG, dan WebP (Bisa pilih banyak sekaligus)</p>
                <input type="file" id="file-input" multiple accept="image/jpeg, image/jpg, image/png, image/webp" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File Gambar
                </button>
            </div>

            <!-- Workspace Container -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 border-b border-gray-200 pb-3">
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">Atur Gambar & Layout PDF</h4>
                        <p class="text-xs text-gray-500">Geser urutan gambar untuk mengatur posisi halaman dalam PDF.</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('file-input').click()" class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            + Tambah Gambar
                        </button>
                        <button type="button" id="btn-clear-all" class="px-3 py-1.5 rounded-xl border border-red-200 bg-red-50 text-xs font-semibold text-red-600 hover:bg-red-100 transition">
                            Kosongkan
                        </button>
                    </div>
                </div>

                <!-- Page Layout Options -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-gray-50/70 p-4 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Ukuran Halaman</label>
                        <select id="page-size" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                            <option value="A4" selected>A4 (210 x 297 mm)</option>
                            <option value="LETTER">Letter</option>
                            <option value="LEGAL">Legal</option>
                            <option value="FIT">Sesuai Ukuran Gambar</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Orientasi Halaman</label>
                        <select id="page-orientation" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                            <option value="auto" selected>Otomatis (Sesuai Gambar)</option>
                            <option value="portrait">Potret (Portrait)</option>
                            <option value="landscape">Lanskap (Landscape)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Margin Halaman</label>
                        <select id="page-margin" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold">
                            <option value="none" selected>Tanpa Margin (Full Bleed)</option>
                            <option value="small">Margin Kecil (10pt)</option>
                            <option value="big">Margin Besar (30pt)</option>
                        </select>
                    </div>
                </div>

                <!-- Sortable Drag & Drop Images Grid -->
                <div id="image-list-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Image Cards will be injected here via JavaScript -->
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2 pt-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Mengkonversi gambar...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action Footer -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <span id="total-summary-text" class="text-xs font-semibold text-gray-600">Total: 0 Gambar</span>
                    <button id="btn-convert" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Konversi ke PDF Sekarang</span>
                        <span>📄</span>
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
let imageItems = []; // Array penampung { id, file, name, size, dataUrl }
let sortableInstance = null;

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const imageListGrid = document.getElementById('image-list-grid');
const btnClearAll = document.getElementById('btn-clear-all');
const btnConvert = document.getElementById('btn-convert');
const totalSummaryText = document.getElementById('total-summary-text');

// Form Selects
const pageSizeSelect = document.getElementById('page-size');
const pageOrientationSelect = document.getElementById('page-orientation');
const pageMarginSelect = document.getElementById('page-margin');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

// Initialize SortableJS for Drag-and-Drop Reordering
document.addEventListener('DOMContentLoaded', () => {
    sortableInstance = new Sortable(imageListGrid, {
        animation: 150,
        ghostClass: 'ghost-card',
        handle: '.drag-handle',
        onEnd: () => {
            syncArrayWithDOM();
        }
    });
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
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    const validImages = files.filter(f => validTypes.includes(f.type) || /\.(jpe?g|png|webp)$/i.test(f.name));

    if (validImages.length === 0) {
        Swal.fire({ icon: 'error', title: 'Format Tidak Sesuai', text: 'Pilih file berkas gambar (JPG, PNG, WebP).' });
        return;
    }

    for (const file of validImages) {
        const dataUrl = await readFileAsDataURL(file);
        const itemObj = {
            id: 'img_' + Math.random().toString(36).substr(2, 9),
            file: file,
            name: file.name,
            size: file.size,
            dataUrl: dataUrl
        };
        imageItems.push(itemObj);
        renderCard(itemObj);
    }

    updateUIState();
    fileInput.value = '';
}

function readFileAsDataURL(file) {
    return new Promise((resolve) => {
        const reader = new FileReader();
        reader.onload = (e) => resolve(e.target.result);
        reader.readAsDataURL(file);
    });
}

function renderCard(itemObj) {
    const card = document.createElement('div');
    card.setAttribute('data-id', itemObj.id);
    card.className = "rounded-xl border border-gray-200 bg-white p-3 shadow-2xs flex flex-col justify-between space-y-2 relative group hover:border-maroon/50 transition";

    card.innerHTML = `
        <div class="relative w-full h-32 bg-gray-50 rounded-lg border border-gray-100 overflow-hidden flex items-center justify-center">
            <img src="${itemObj.dataUrl}" class="max-h-full max-w-full object-contain">
            
            <!-- Drag Handle Overlay -->
            <div class="drag-handle absolute top-1 left-1 bg-white/90 hover:bg-maroon hover:text-white text-gray-600 p-1 rounded cursor-grab active:cursor-grabbing shadow-xs transition" title="Geser posisi">
                ☰
            </div>

            <!-- Page Index Badge -->
            <span class="badge-index absolute bottom-1 right-1 bg-gray-900/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">
                #1
            </span>
        </div>

        <div class="min-w-0">
            <p class="text-xs font-bold text-gray-900 truncate" title="${itemObj.name}">${itemObj.name}</p>
            <p class="text-[10px] font-medium text-gray-500">${formatBytes(itemObj.size)}</p>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-2 text-[11px]">
            <div class="flex items-center gap-1">
                <button type="button" onclick="moveCard('${itemObj.id}', -1)" class="px-2 py-0.5 rounded border border-gray-200 bg-gray-50 hover:bg-maroon hover:text-white transition font-bold" title="Geser Kiri">◀</button>
                <button type="button" onclick="moveCard('${itemObj.id}', 1)" class="px-2 py-0.5 rounded border border-gray-200 bg-gray-50 hover:bg-maroon hover:text-white transition font-bold" title="Geser Kanan">▶</button>
            </div>

            <button type="button" onclick="removeItem('${itemObj.id}')" class="text-red-500 hover:text-red-700 font-semibold px-2 py-0.5 rounded hover:bg-red-50 transition">
                Hapus
            </button>
        </div>
    `;

    imageListGrid.appendChild(card);
    updateIndexes();
}

function moveCard(id, direction) {
    const card = imageListGrid.querySelector(`[data-id="${id}"]`);
    if (!card) return;

    if (direction === -1 && card.previousElementSibling) {
        imageListGrid.insertBefore(card, card.previousElementSibling);
    } else if (direction === 1 && card.nextElementSibling) {
        imageListGrid.insertBefore(card.nextElementSibling, card);
    }

    syncArrayWithDOM();
}

function removeItem(id) {
    imageItems = imageItems.filter(i => i.id !== id);
    const card = imageListGrid.querySelector(`[data-id="${id}"]`);
    if (card) card.remove();
    updateUIState();
    updateIndexes();
}

btnClearAll.addEventListener('click', () => {
    imageItems = [];
    imageListGrid.innerHTML = '';
    updateUIState();
});

function syncArrayWithDOM() {
    const cardElements = Array.from(imageListGrid.querySelectorAll('[data-id]'));
    const newOrdered = [];
    
    cardElements.forEach(el => {
        const id = el.getAttribute('data-id');
        const found = imageItems.find(i => i.id === id);
        if (found) newOrdered.push(found);
    });

    imageItems = newOrdered;
    updateIndexes();
}

function updateIndexes() {
    const cardElements = Array.from(imageListGrid.querySelectorAll('[data-id]'));
    cardElements.forEach((el, idx) => {
        const badge = el.querySelector('.badge-index');
        if (badge) badge.textContent = `#${idx + 1}`;
    });
}

function updateUIState() {
    if (imageItems.length > 0) {
        dropZone.classList.add('hidden');
        workspaceContainer.classList.remove('hidden');
        totalSummaryText.textContent = `Total: ${imageItems.length} Gambar`;
    } else {
        dropZone.classList.remove('hidden');
        workspaceContainer.classList.add('hidden');
    }
}

// Client-Side Image to PDF Conversion Engine
btnConvert.addEventListener('click', async () => {
    if (imageItems.length === 0) return;

    btnConvert.disabled = true;
    progressContainer.classList.remove('hidden');

    try {
        const pdfDoc = await PDFLib.PDFDocument.create();
        const total = imageItems.length;

        const selectedSize = pageSizeSelect.value;
        const selectedOrientation = pageOrientationSelect.value;
        const selectedMarginStr = pageMarginSelect.value;
        const margin = selectedMarginStr === 'small' ? 10 : (selectedMarginStr === 'big' ? 30 : 0);

        for (let i = 0; i < total; i++) {
            const item = imageItems[i];
            updateProgress(i + 1, total, `Memproses gambar ${i + 1} dari ${total}...`);

            // Convert image DataURL to ArrayBuffer
            const imgBuffer = await fetch(item.dataUrl).then(res => res.arrayBuffer());
            
            let embeddedImg;
            if (item.file.type === 'image/png') {
                embeddedImg = await pdfDoc.embedPng(imgBuffer);
            } else {
                // Default JPG / WebP via Canvas Re-encode
                embeddedImg = await embedJpgFromCanvas(item.dataUrl, pdfDoc);
            }

            const imgWidth = embeddedImg.width;
            const imgHeight = embeddedImg.height;

            // Determine Page Dimensions
            let pageWidth, pageHeight;

            if (selectedSize === 'FIT') {
                pageWidth = imgWidth + margin * 2;
                pageHeight = imgHeight + margin * 2;
            } else {
                // Standard Dimensions (A4: 595.28 x 841.89 pt)
                let dims = [595.28, 841.89];
                if (selectedSize === 'LETTER') dims = [612, 792];
                if (selectedSize === 'LEGAL') dims = [612, 1008];

                if (selectedOrientation === 'landscape' || (selectedOrientation === 'auto' && imgWidth > imgHeight)) {
                    pageWidth = Math.max(dims[0], dims[1]);
                    pageHeight = Math.min(dims[0], dims[1]);
                } else {
                    pageWidth = Math.min(dims[0], dims[1]);
                    pageHeight = Math.max(dims[0], dims[1]);
                }
            }

            const page = pdfDoc.addPage([pageWidth, pageHeight]);

            // Calculate Scaled Dimensions inside Margins
            const maxWidth = pageWidth - margin * 2;
            const maxHeight = pageHeight - margin * 2;

            const scale = Math.min(maxWidth / imgWidth, maxHeight / imgHeight);
            const drawWidth = imgWidth * scale;
            const drawHeight = imgHeight * scale;

            // Center image on page
            const x = margin + (maxWidth - drawWidth) / 2;
            const y = margin + (maxHeight - drawHeight) / 2;

            page.drawImage(embeddedImg, {
                x: x,
                y: y,
                width: drawWidth,
                height: drawHeight,
            });
        }

        updateProgress(total, total, 'Menyusun berkas PDF final...');
        const pdfBytes = await pdfDoc.save();
        const finalBlob = new Blob([pdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnConvert.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Konversi Berhasil!',
            html: `
                <div class="text-left text-xs space-y-2 mt-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                    <p>Total Gambar: <b>${total} Gambar</b></p>
                    <p>Ukuran File PDF: <b class="text-emerald-700">${formatBytes(finalBlob.size)}</b></p>
                </div>
            `,
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `images_converted_${Date.now()}.pdf`);
            }
        });

    } catch (err) {
        console.error(err);
        btnConvert.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Konversi Gagal', text: 'Terjadi kesalahan saat mengolah gambar.' });
    }
});

async function embedJpgFromCanvas(dataUrl, pdfDoc) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = async () => {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            const jpegUrl = canvas.toDataURL('image/jpeg', 0.9);
            const jpegBuffer = await fetch(jpegUrl).then(r => r.arrayBuffer());
            const embedded = await pdfDoc.embedJpg(jpegBuffer);
            resolve(embedded);
        };
        img.onerror = reject;
        img.src = dataUrl;
    });
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