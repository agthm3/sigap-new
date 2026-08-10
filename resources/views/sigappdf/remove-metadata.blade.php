@extends('layouts.page')

@section('title', 'Hapus Metadata PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<!-- PDF Processing Client-Side Libraries -->
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
                Kategori Security & Utilities
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                Hapus <span class="text-white/90">Metadata PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Bersihkan jejak identitas tersembunyi seperti nama penulis, judul, perangkat lunak pembuat, dan tanggal modifikasi dari berkas PDF Anda.
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
                <strong class="text-maroon font-bold">Privacy First:</strong> Pembersihan metadata dilakukan langsung di memori browser Anda. Berkas PDF tidak pernah dikirim ke server.
            </p>
        </div>

        <!-- Drag and Drop & Workspace Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🧹
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file PDF yang ingin dibersihkan metadatanya</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Workspace Metadata Inspection Container -->
            <div id="workspace-container" class="hidden space-y-6">
                
                <!-- File Detail Summary Card -->
                <div class="rounded-xl bg-gray-50 border border-gray-200 p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center font-bold text-lg">
                            📄
                        </div>
                        <div>
                            <h4 id="file-name" class="text-sm font-bold text-gray-900">dokumen.pdf</h4>
                            <p id="file-size" class="text-xs text-gray-500">Ukuran: 0 KB</p>
                        </div>
                    </div>

                    <button type="button" id="btn-reset" class="text-xs text-red-600 font-semibold hover:underline">
                        Ganti File
                    </button>
                </div>

                <!-- Detected Metadata Table -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Metadata Terdeteksi Pada Berkas</h4>
                        <span class="text-[10px] font-bold bg-amber-100 text-amber-800 px-2.5 py-0.5 rounded-full">Akan Dihapus Permanen</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Judul Dokumen (Title)</span>
                            <span id="meta-title" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Penulis / Pemilik (Author)</span>
                            <span id="meta-author" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Subjek (Subject)</span>
                            <span id="meta-subject" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Kata Kunci (Keywords)</span>
                            <span id="meta-keywords" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Aplikasi Pembuat (Creator)</span>
                            <span id="meta-creator" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Perangkat Lunak (Producer)</span>
                            <span id="meta-producer" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Tanggal Dibuat (Creation Date)</span>
                            <span id="meta-creation-date" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-400 block text-[10px] font-bold uppercase">Tanggal Dimodifikasi (Modification Date)</span>
                            <span id="meta-mod-date" class="font-semibold text-gray-800 mt-0.5 block truncate">-</span>
                        </div>
                    </div>
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Membersihkan metadata...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button id="btn-remove-meta" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Bersihkan Metadata & Unduh</span>
                        <span>🧹</span>
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
const btnReset = document.getElementById('btn-reset');
const btnRemoveMeta = document.getElementById('btn-remove-meta');

// Metadata Value Elements
const metaTitle = document.getElementById('meta-title');
const metaAuthor = document.getElementById('meta-author');
const metaSubject = document.getElementById('meta-subject');
const metaKeywords = document.getElementById('meta-keywords');
const metaCreator = document.getElementById('meta-creator');
const metaProducer = document.getElementById('meta-producer');
const metaCreationDate = document.getElementById('meta-creation-date');
const metaModDate = document.getElementById('meta-mod-date');

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
        Swal.fire({ icon: 'error', title: 'File tidak valid', text: 'Pilih berkas berformat PDF.' });
    }
});

fileInput.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

async function handleFileSelect(file) {
    currentFile = file;
    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = `Ukuran: ${formatBytes(file.size)}`;

    dropZone.classList.add('hidden');
    workspaceContainer.classList.remove('hidden');
    progressContainer.classList.remove('hidden');
    updateProgress(30, 100, 'Memeriksa metadata tersembunyi...');

    try {
        originalArrayBuffer = await file.arrayBuffer();
        const pdfDoc = await PDFLib.PDFDocument.load(originalArrayBuffer.slice(0), { ignoreEncryption: true });

        // Inspection Metadata
        metaTitle.textContent = pdfDoc.getTitle() || '(Kosong)';
        metaAuthor.textContent = pdfDoc.getAuthor() || '(Kosong)';
        metaSubject.textContent = pdfDoc.getSubject() || '(Kosong)';
        metaKeywords.textContent = pdfDoc.getKeywords() || '(Kosong)';
        metaCreator.textContent = pdfDoc.getCreator() || '(Kosong)';
        metaProducer.textContent = pdfDoc.getProducer() || '(Kosong)';
        metaCreationDate.textContent = pdfDoc.getCreationDate() ? pdfDoc.getCreationDate().toLocaleString('id-ID') : '(Kosong)';
        metaModDate.textContent = pdfDoc.getModificationDate() ? pdfDoc.getModificationDate().toLocaleString('id-ID') : '(Kosong)';

        progressContainer.classList.add('hidden');

    } catch (err) {
        console.error(err);
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membaca PDF', text: 'Tidak dapat menginspeksi metadata dari berkas ini.' });
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

// Client-Side Remove Metadata Processing Engine
btnRemoveMeta.addEventListener('click', async () => {
    if (!originalArrayBuffer) return;

    btnRemoveMeta.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(50, 100, 'Menghapus metadata tersembunyi...');

    try {
        const pdfDoc = await PDFLib.PDFDocument.load(originalArrayBuffer.slice(0), { ignoreEncryption: true });

        // Strip Metadata Fields
        pdfDoc.setTitle('');
        pdfDoc.setAuthor('');
        pdfDoc.setSubject('');
        pdfDoc.setKeywords([]);
        pdfDoc.setCreator('');
        pdfDoc.setProducer('');

        updateProgress(80, 100, 'Menyusun ulang berkas PDF bersih...');
        const cleanPdfBytes = await pdfDoc.save();
        const finalBlob = new Blob([cleanPdfBytes], { type: 'application/pdf' });

        progressContainer.classList.add('hidden');
        btnRemoveMeta.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Metadata Berhasil Dibersihkan!',
            text: 'Berkas PDF kini bebas dari jejak metadata identitas.',
            confirmButtonText: 'Unduh Hasil PDF',
            confirmButtonColor: '#7a2222',
        }).then((result) => {
            if (result.isConfirmed) {
                saveAs(finalBlob, `clean_${currentFile.name}`);
            }
        });

    } catch (err) {
        console.error(err);
        btnRemoveMeta.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Membersihkan', text: 'Terjadi kesalahan saat mengosongkan metadata PDF.' });
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