@extends('layouts.page')

@section('title', 'Ubah Versi PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
@endpush

@section('content')

<!-- Header Maroon Section -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-[0.2em] mb-3">
                Kategori Utilities
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                Ubah Versi <span class="text-white/90">PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Deteksi dan ubah versi header dokumen PDF (Acrobat 1.4, 1.5, 1.6, 1.7, hingga 2.0) sesuai kebutuhan kompatibilitas aplikasi Anda.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Seluruh proses pengubahan versi spesifikasi PDF dilakukan di memori browser Anda tanpa dikirim ke server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    ⚙️
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file PDF dari komputer Anda</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Workspace Options -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <!-- File Detail Summary Card -->
                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center font-bold text-lg">
                            📄
                        </div>
                        <div>
                            <h4 id="file-name" class="text-sm font-bold text-gray-900">dokumen.pdf</h4>
                            <p id="file-size" class="text-xs text-gray-500">Ukuran: 0 KB • Versi Saat Ini: <b id="current-version" class="text-maroon">PDF 1.7</b></p>
                        </div>
                    </div>

                    <button type="button" id="btn-reset" class="text-xs text-red-600 font-semibold hover:underline">
                        Ganti File
                    </button>
                </div>

                <!-- Version Target Selection -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Pilih Versi PDF Target</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        
                        <label class="relative flex flex-col p-3.5 rounded-xl border border-gray-200 bg-white hover:border-maroon/50 cursor-pointer transition shadow-2xs">
                            <input type="radio" name="pdf_version" value="1.4" class="accent-maroon">
                            <span class="text-sm font-bold text-gray-900 mt-1">PDF 1.4</span>
                            <span class="text-[11px] text-gray-500 mt-0.5">Acrobat 5.0 (Kompatibilitas Lama)</span>
                        </label>

                        <label class="relative flex flex-col p-3.5 rounded-xl border border-gray-200 bg-white hover:border-maroon/50 cursor-pointer transition shadow-2xs">
                            <input type="radio" name="pdf_version" value="1.5" class="accent-maroon">
                            <span class="text-sm font-bold text-gray-900 mt-1">PDF 1.5</span>
                            <span class="text-[11px] text-gray-500 mt-0.5">Acrobat 6.0</span>
                        </label>

                        <label class="relative flex flex-col p-3.5 rounded-xl border-2 border-maroon bg-maroon/5 cursor-pointer transition shadow-2xs">
                            <input type="radio" name="pdf_version" value="1.7" checked class="accent-maroon">
                            <span class="text-sm font-bold text-maroon mt-1">PDF 1.7 (ISO 32000-1)</span>
                            <span class="text-[11px] text-gray-600 mt-0.5">Standar Umum Modern</span>
                        </label>

                        <label class="relative flex flex-col p-3.5 rounded-xl border border-gray-200 bg-white hover:border-maroon/50 cursor-pointer transition shadow-2xs">
                            <input type="radio" name="pdf_version" value="2.0" class="accent-maroon">
                            <span class="text-sm font-bold text-gray-900 mt-1">PDF 2.0 (ISO 32000-2)</span>
                            <span class="text-[11px] text-gray-500 mt-0.5">Spesifikasi Terbaru</span>
                        </label>

                    </div>
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Mengubah header versi...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button id="btn-convert-version" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Ubah Versi & Unduh</span>
                        <span>⚙️</span>
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

const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');
const workspaceContainer = document.getElementById('workspace-container');
const fileNameEl = document.getElementById('file-name');
const fileSizeEl = document.getElementById('file-size');
const currentVersionEl = document.getElementById('current-version');
const btnReset = document.getElementById('btn-reset');
const btnConvertVersion = document.getElementById('btn-convert-version');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

// Radio Selection UI Styling
document.querySelectorAll('input[name="pdf_version"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('input[name="pdf_version"]').forEach(r => {
            const card = r.closest('label');
            if (r.checked) {
                card.className = "relative flex flex-col p-3.5 rounded-xl border-2 border-maroon bg-maroon/5 cursor-pointer transition shadow-2xs";
            } else {
                card.className = "relative flex flex-col p-3.5 rounded-xl border border-gray-200 bg-white hover:border-maroon/50 cursor-pointer transition shadow-2xs";
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

async function handleFileSelect(file) {
    currentFile = file;
    fileNameEl.textContent = file.name;

    dropZone.classList.add('hidden');
    workspaceContainer.classList.remove('hidden');
    progressContainer.classList.remove('hidden');
    updateProgress(30, 100, 'Memeriksa versi header PDF...');

    try {
        originalArrayBuffer = await file.arrayBuffer();
        
        // Inspect Header Version (%PDF-1.x)
        const headerStr = new TextDecoder('latin1').decode(originalArrayBuffer.slice(0, 20));
        const match = headerStr.match(/%PDF-(\d\.\d)/);
        const detectedVer = match ? match[1] : '1.7';

        currentVersionEl.textContent = `PDF ${detectedVer}`;
        fileSizeEl.innerHTML = `Ukuran: ${formatBytes(file.size)} • Versi Terdeteksi: <b class="text-maroon">PDF ${detectedVer}</b>`;

        progressContainer.classList.add('hidden');

    } catch (err) {
        console.error(err);
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membaca File', text: 'Berkas PDF ini rusak atau tidak valid.' });
    }
}

btnReset.addEventListener('click', () => {
    currentFile = null;
    originalArrayBuffer = null;
    fileInput.value = '';
    dropZone.classList.remove('hidden');
    workspaceContainer.classList.add('hidden');
    progressContainer.classList.add('hidden');
});

// Client-Side Version Modification Engine
btnConvertVersion.addEventListener('click', async () => {
    if (!originalArrayBuffer) return;

    const targetVer = document.querySelector('input[name="pdf_version"]:checked').value;

    btnConvertVersion.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(50, 100, `Mengubah versi ke PDF ${targetVer}...`);

    try {
        // Clone ArrayBuffer
        const bytes = new Uint8Array(originalArrayBuffer.slice(0));
        const textDecoder = new TextDecoder('latin1');
        const textEncoder = new TextEncoder();
        
        let pdfStr = textDecoder.decode(bytes);

        // Replace Header %PDF-1.X -> %PDF-target
        pdfStr = pdfStr.replace(/%PDF-\d\.\d/, `%PDF-${targetVer}`);

        const finalBytes = textEncoder.encode(pdfStr);
        const finalBlob = new Blob([finalBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnConvertVersion.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Pengubahan Versi Berhasil!',
            text: `Versi berkas berhasil diperbarui ke PDF ${targetVer}.`,
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `v${targetVer}_${currentFile.name}`);
            }
        });

    } catch (err) {
        console.error(err);
        btnConvertVersion.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Mengubah Versi', text: 'Terjadi kesalahan saat memperbarui spesifikasi versi PDF.' });
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