@extends('layouts.page')

@section('title', 'Proteksi Password PDF — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
<!-- Core Client-Side Engine (PDF.js untuk render, jsPDF untuk Native Encryption) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

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
                Proteksi Password <span class="text-white/90">PDF</span>
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                Enkripsi berkas PDF Anda dengan kata sandi, 100% diproses langsung di RAM browser tanpa file menyentuh server.
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
                <strong class="text-maroon font-bold">Privacy First (Zero Upload):</strong> Penguncian PDF dilakukan langsung di browser Anda. SIGAP tidak pernah menerima, membaca, atau menyimpan dokumen dan kata sandi Anda.
            </p>
        </div>

        <!-- Drag and Drop Container -->
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            
            <!-- Dropzone Area -->
            <div id="drop-zone" class="border-2 border-dashed border-gray-300 hover:border-maroon rounded-2xl p-8 sm:p-12 text-center bg-gray-50 hover:bg-maroon/5 transition cursor-pointer">
                <div class="w-12 h-12 rounded-2xl bg-white border border-gray-200 text-maroon flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                    🔑
                </div>
                <h3 class="text-base font-bold text-gray-900">Tarik & Lepaskan File PDF di Sini</h3>
                <p class="text-xs text-gray-500 mt-1">atau klik tombol di bawah untuk memilih file PDF yang ingin diproteksi</p>
                <input type="file" id="file-input" accept=".pdf" class="hidden">
                
                <button type="button" onclick="document.getElementById('file-input').click()" class="mt-4 px-5 py-2.5 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition shadow-sm">
                    Pilih File PDF
                </button>
            </div>

            <!-- Password Form Workspace -->
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

                <!-- Password Input Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                    <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Atur Kata Sandi Pembuka</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">Kata Sandi (Password)</label>
                            <div class="relative">
                                <input type="password" id="user-password" placeholder="Masukkan password pembuka..." 
                                       class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold pr-10">
                                <button type="button" onclick="togglePassVisibility('user-password', this)" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-xs">
                                    👁️
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-500 mt-1">Dibutuhkan saat seseorang membuka & melihat file PDF ini.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-800 mb-1">Konfirmasi Kata Sandi</label>
                            <div class="relative">
                                <input type="password" id="confirm-password" placeholder="Ketik ulang password..." 
                                       class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-xs font-semibold pr-10">
                                <button type="button" onclick="togglePassVisibility('confirm-password', this)" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600 text-xs">
                                    👁️
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress & Status Indicator -->
                <div id="progress-container" class="hidden space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span id="progress-status" class="font-bold text-maroon">Mengenkripsi berkas...</span>
                        <span id="progress-percent" class="font-semibold text-gray-600">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div id="progress-bar" class="bg-maroon h-2.5 rounded-full transition-all duration-200" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Process Action -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button id="btn-protect" class="px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm inline-flex items-center gap-2">
                        <span>Proteksi & Unduh PDF</span>
                        <span>🔒</span>
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
const workspaceContainer = document.getElementById('workspace-container');
const fileNameEl = document.getElementById('file-name');
const fileSizeEl = document.getElementById('file-size');
const btnReset = document.getElementById('btn-reset');
const btnProtect = document.getElementById('btn-protect');

const userPasswordInput = document.getElementById('user-password');
const confirmPasswordInput = document.getElementById('confirm-password');

const progressContainer = document.getElementById('progress-container');
const progressStatus = document.getElementById('progress-status');
const progressPercent = document.getElementById('progress-percent');
const progressBar = document.getElementById('progress-bar');

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

function handleFileSelect(file) {
    currentFile = file;
    fileNameEl.textContent = file.name;
    fileSizeEl.textContent = `Ukuran: ${formatBytes(file.size)}`;
    dropZone.classList.add('hidden');
    workspaceContainer.classList.remove('hidden');
}

btnReset.addEventListener('click', () => {
    currentFile = null;
    fileInput.value = '';
    userPasswordInput.value = '';
    confirmPasswordInput.value = '';
    dropZone.classList.remove('hidden');
    workspaceContainer.classList.add('hidden');
    progressContainer.classList.add('hidden');
});

function togglePassVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.textContent = '🙈';
    } else {
        input.type = 'password';
        btn.textContent = '👁️';
    }
}

// ==========================================
// 100% CLIENT-SIDE ENCRYPTION ENGINE (Zero Upload)
// ==========================================
btnProtect.addEventListener('click', async () => {
    if (!currentFile) return;

    const pass = userPasswordInput.value;
    const confirm = confirmPasswordInput.value;

    if (!pass) {
        Swal.fire({ icon: 'warning', title: 'Password Kosong', text: 'Masukkan kata sandi terlebih dahulu.' });
        return;
    }
    if (pass !== confirm) {
        Swal.fire({ icon: 'error', title: 'Password Tidak Cocok', text: 'Konfirmasi password tidak sesuai.' });
        return;
    }

    btnProtect.disabled = true;
    progressContainer.classList.remove('hidden');
    updateProgress(10, 100, 'Memuat dokumen ke memori...');

    try {
        const fileBuffer = await currentFile.arrayBuffer();
        const pdfjsDoc = await pdfjsLib.getDocument({ data: fileBuffer }).promise;
        const totalPages = pdfjsDoc.numPages;

        const { jsPDF } = window.jspdf;
        let finalPdf = null;

        for (let i = 1; i <= totalPages; i++) {
            updateProgress(i, totalPages, `Mengenksripsi halaman ${i} dari ${totalPages}...`);
            
            const page = await pdfjsDoc.getPage(i);
            const viewport = page.getViewport({ scale: 2.0 }); // Skala tinggi untuk menjaga ketajaman
            
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            await page.render({ canvasContext: ctx, viewport: viewport }).promise;
            const imgData = canvas.toDataURL('image/jpeg', 0.95);

            const widthPx = viewport.width;
            const heightPx = viewport.height;
            const orientation = widthPx > heightPx ? 'l' : 'p';

            if (i === 1) {
                // Inisialisasi PDF Baru DENGAN ENKRIPSI AKTIF
                finalPdf = new jsPDF({
                    orientation: orientation,
                    unit: 'px',
                    format: [widthPx, heightPx],
                    encryption: {
                        userPassword: pass,
                        ownerPassword: pass,
                        userPermissions: ["print"]
                    }
                });
            } else {
                finalPdf.addPage([widthPx, heightPx], orientation);
            }

            finalPdf.addImage(imgData, 'JPEG', 0, 0, widthPx, heightPx);
        }

        updateProgress(100, 100, 'Menyelesaikan berkas terkunci...');
        
        // Simpan langsung di browser
        finalPdf.save(`protected_${currentFile.name}`);

        progressContainer.classList.add('hidden');
        btnProtect.disabled = false;

        Swal.fire({
            icon: 'success',
            title: 'Dokumen Berhasil Dikunci!',
            text: 'PDF Anda telah diamankan dan akan meminta kata sandi saat dibuka.',
            confirmButtonText: 'Selesai',
            confirmButtonColor: '#7a2222',
        });

    } catch (error) {
        console.error(error);
        btnProtect.disabled = false;
        progressContainer.classList.add('hidden');
        Swal.fire({ icon: 'error', title: 'Gagal Memproteksi', text: 'Terjadi kesalahan saat memproses enkripsi file.' });
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