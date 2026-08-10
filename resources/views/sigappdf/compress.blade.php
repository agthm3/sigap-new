@extends('layouts.page')

@section('title', 'Compress PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<!-- Load Library Browser-Based PDF Processing secara lokal/standard CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
    // Set worker src untuk PDF.js
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
                Kategori Optimize
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                Kompres <span class="text-white/90">PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Atur kustomisasi target ukuran berkas PDF (KB/MB) secara presisi, 100% diproses langsung di browser Anda tanpa unggah ke server.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh proses membaca dan mengecilkan file PDF mengeksekusi memori lokal RAM browser. SIGAP tidak menerima atau menyimpan dokumen Anda.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    📉
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file PDF yang ingin dikompresi</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- File Selected Info & Compression Target Settings -->
            <div id="compression-options-container" class="hidden space-y-6">
                
                <!-- File Detail Summary Card -->
                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center font-bold text-lg">
                            📄
                        </div>
                        <div>
                            <h4 id="file-name" class="text-sm font-bold text-gray-900">dokumen.pdf</h4>
                            <p id="file-size" class="text-xs text-gray-500">Ukuran Asli: 0 KB</p>
                        </div>
                    </div>

                    <button type="button" id="btn-reset" class="text-xs text-red-600 font-semibold hover:underline">
                        Ganti File
                    </button>
                </div>

                <!-- Custom Target Size Settings -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Pengaturan Target Ukuran File</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Input Custom Target -->
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-2xs">
                            <label class="block text-xs font-bold text-gray-800 mb-1">Target Maksimal Ukuran</label>
                            <div class="flex gap-2">
                                <input type="number" id="target-size" placeholder="Contoh: 500" min="10" step="1"
                                       class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-sm font-semibold">
                                <select id="target-unit" class="rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-sm font-semibold bg-gray-50">
                                    <option value="KB" selected>KB</option>
                                    <option value="MB">MB</option>
                                </select>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-2">Sistem akan menyesuaikan kompresi gambar agar mendekati target ukuran file yang Anda tentukan.</p>
                        </div>

                        <!-- Presets Quick Select -->
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-2xs">
                            <label class="block text-xs font-bold text-gray-800 mb-1">Atau Pilih Preset Cepat</label>
                            <div class="grid grid-cols-3 gap-2 mt-2">
                                <button type="button" onclick="setPresetTarget(200, 'KB')" class="px-2 py-1.5 rounded-lg border border-gray-200 hover:border-maroon hover:bg-maroon/5 text-xs font-semibold text-gray-700 transition">
                                    200 KB
                                </button>
                                <button type="button" onclick="setPresetTarget(500, 'KB')" class="px-2 py-1.5 rounded-lg border border-gray-200 hover:border-maroon hover:bg-maroon/5 text-xs font-semibold text-gray-700 transition">
                                    500 KB
                                </button>
                                <button type="button" onclick="setPresetTarget(1, 'MB')" class="px-2 py-1.5 rounded-lg border border-gray-200 hover:border-maroon hover:bg-maroon/5 text-xs font-semibold text-gray-700 transition">
                                    1 MB
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Memproses kompresi...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button id="btn-compress" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Kompres PDF Sekarang</span>
                        <span>⚡</span>
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

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const compressionContainer = document.getElementById('compression-options-container');
const fileNameEl = document.getElementById('file-name');
const fileSizeEl = document.getElementById('file-size');
const btnReset = document.getElementById('btn-reset');
const btnCompress = document.getElementById('btn-compress');
const targetSizeInput = document.getElementById('target-size');
const targetUnitSelect = document.getElementById('target-unit');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

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
        Swal.fire({ icon: 'error', title: 'File tidak valid', text: 'Pilih berkas dengan format PDF.' });
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

function handleFileSelect(file) {
    currentFile = file;
    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = `Ukuran Asli: ${formatBytes(file.size)}`;

    // Set default target size (misal 50% dari ukuran asli dalam KB/MB)
    const sizeInKB = Math.round(file.size / 1024);
    if (sizeInKB > 1024) {
        targetSizeInput.value = Math.max(1, Math.round((file.size / (1024 * 1024)) * 0.6));
        targetUnitSelect.value = 'MB';
    } else {
        targetSizeInput.value = Math.max(50, Math.round(sizeInKB * 0.6));
        targetUnitSelect.value = 'KB';
    }

    dropZone.classList.add('hidden');
    compressionContainer.classList.remove('hidden');
}

btnReset.addEventListener('click', () => {
    currentFile = null;
    fileInput.value = '';
    dropZone.classList.remove('hidden');
    compressionContainer.classList.add('hidden');
    progressContainer.classList.add('hidden');
});

function setPresetTarget(size, unit) {
    targetSizeInput.value = size;
    targetUnitSelect.value = unit;
}

function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Client-Side PDF Compression Engine
// Client-Side Adaptive PDF Compression Engine
btnCompress.addEventListener('click', async () => {
    if (!currentFile) return;

    const targetVal = parseFloat(targetSizeInput.value);
    if (!targetVal || targetVal <= 0) {
        Swal.fire({ icon: 'warning', title: 'Target Tidak Valid', text: 'Masukkan nilai target ukuran berkas.' });
        return;
    }

    // Hitung Target Bytes
    const targetBytes = targetUnitSelect.value === 'MB' ? targetVal * 1024 * 1024 : targetVal * 1024;

    if (targetBytes >= currentFile.size) {
        Swal.fire({
            icon: 'info',
            title: 'Target Lebih Besar Dari Ukuran Asli',
            text: 'Target ukuran yang ditentukan lebih besar atau sama dengan ukuran berkas saat ini.'
        });
        return;
    }

    btnCompress.disabled = true;
    progressContainer.classList.remove('hidden');

    try {
        const fileBuffer = await currentFile.arrayBuffer();
        const pdfDoc = await pdfjsLib.getDocument({ data: fileBuffer }).promise;
        const numPages = pdfDoc.numPages;

        // Hitung batas target maksimal per halaman
        const targetBytesPerPage = targetBytes / numPages;

        // Tentukan Scale & Quality berdasarkan target per halaman
        let scale = 1.0;
        let quality = 0.6;

        if (targetBytesPerPage < 30 * 1024) {        // < 30 KB per halaman (Target Sangat Ekstrem)
            scale = 0.5;
            quality = 0.25;
        } else if (targetBytesPerPage < 60 * 1024) { // < 60 KB per halaman
            scale = 0.65;
            quality = 0.35;
        } else if (targetBytesPerPage < 100 * 1024) { // < 100 KB per halaman
            scale = 0.85;
            quality = 0.5;
        } else {
            scale = 1.0;
            quality = 0.65;
        }

        const newPdfDoc = await PDFLib.PDFDocument.create();

        for (let i = 1; i <= numPages; i++) {
            updateProgress(i, numPages, `Mekompresi halaman ${i} dari ${numPages} (Res: ${Math.round(scale * 100)}%)...`);

            const page = await pdfDoc.getPage(i);
            const viewport = page.getViewport({ scale: scale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;

            // Render ke JPEG dengan quality adaptif
            let imgDataUrl = canvas.toDataURL('image/jpeg', quality);
            let imgBytes = await fetch(imgDataUrl).then(res => res.arrayBuffer());

            // Jika ukuran halaman tunggal masih melebihi target batas per halaman, lakukan re-compress darurat
            if (imgBytes.byteLength > targetBytesPerPage && quality > 0.15) {
                const adaptiveQuality = Math.max(0.12, quality * (targetBytesPerPage / imgBytes.byteLength));
                imgDataUrl = canvas.toDataURL('image/jpeg', adaptiveQuality);
                imgBytes = await fetch(imgDataUrl).then(res => res.arrayBuffer());
            }

            const embeddedImg = await newPdfDoc.embedJpg(imgBytes);
            const newPage = newPdfDoc.addPage([viewport.width, viewport.height]);
            newPage.drawImage(embeddedImg, {
                x: 0,
                y: 0,
                width: viewport.width,
                height: viewport.height,
            });
        }

        updateProgress(numPages, numPages, 'Menyusun berkas PDF hasil kompresi...');
        const compressedPdfBytes = await newPdfDoc.save();
        const finalBlob = new Blob([compressedPdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnCompress.disabled = false;

        // Tampilkan Hasil Akhir
        Swal.fire({
            icon: 'success',
            title: 'Kompresi Berhasil!',
            html: `
                <div class="text-left text-xs space-y-2 mt-2 bg-gray-50 p-3 rounded-xl border border-gray-200">
                    <p>Ukuran Asli: <b>${formatBytes(currentFile.size)}</b></p>
                    <p>Ukuran Target: <b>${targetVal} ${targetUnitSelect.value}</b></p>
                    <p>Ukuran Hasil: <b class="text-emerald-700">${formatBytes(finalBlob.size)}</b></p>
                    <p class="text-[10px] text-gray-500 mt-1">*Jika target tidak tercapai 100%, ini adalah batas minimum pembacaan teks halaman.</p>
                </div>
            `,
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `compressed_${currentFile.name}`);
            }
        });

    } catch (error) {
        console.error(error);
        btnCompress.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Kompresi Gagal', text: 'Terjadi kesalahan saat memproses berkas PDF.' });
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