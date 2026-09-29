@extends('layouts.app')

@push('head')
<!-- Library Client-Side PDF & Image Compression (Versi Stabil Anti-Worker Hang) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
  if (typeof pdfjsLib !== 'undefined') {
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
  }
</script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto" x-data="suratMasukForm()">
  <div class="mb-4">
    <h1 class="text-xl font-extrabold text-gray-900">Catat Surat Masuk</h1>
    <p class="text-sm text-gray-600">Lengkapi data surat masuk fisik, kompresi berkas di browser, dan bubuhkan tanda tangan penerimaan.</p>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
    <form id="formSuratMasuk" action="{{ route('sigap-surat.masuk.store') }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitFormViaXhr()">
      @csrf

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Tanggal Terima -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Diterima Tanggal *</label>
          <input type="date" name="tanggal_terima" value="{{ date('Y-m-d') }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
        </div>

        <!-- Tingkat Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat / Sifat Surat *</label>
          <select name="tingkat_surat" class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
            <option value="Biasa" selected>Biasa</option>
            <option value="Penting">Penting</option>
            <option value="Segera">Segera</option>
            <option value="Penting / Segera">Penting / Segera</option>
            <option value="Rahasia">Rahasia</option>
          </select>
        </div>

        <!-- Surat Dari -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Surat Dari (Asal Surat / Instansi Pengirim) *</label>
          <input type="text" name="asal_surat" placeholder="Contoh: Bappeda Kota Makassar / Kementerian Dalam Negeri"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
        </div>

        <!-- Nomor Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Surat Masuk *</label>
          <input type="text" name="nomor_surat_masuk" placeholder="Contoh: 005/789/Bappeda/IX/2026"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
        </div>

        <!-- Tanggal Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Surat *</label>
          <input type="date" name="tanggal_surat" value="{{ date('Y-m-d') }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
        </div>

        <!-- Perihal -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Perihal / Ringkasan Isi Surat *</label>
          <textarea name="perihal" rows="3" placeholder="Perihal yang tertera di naskah surat..."
                    class="w-full rounded-xl px-3.5 py-2 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required></textarea>
        </div>

        <!-- Unit Pengolah -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Unit Pengolah *</label>
          <select name="unit_pengolah" class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
            <option value="Sekretariat">Sekretariat</option>
            <option value="Subbagian Umum & Kepegawaian">Subbagian Umum & Kepegawaian</option>
            <option value="Subbagian Keuangan & Perencanaan">Subbagian Keuangan & Perencanaan</option>
            <option value="Bidang Riset">Bidang Riset</option>
            <option value="Bidang Inovasi">Bidang Inovasi</option>
          </select>
        </div>

        <!-- Nama Penerima -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Penerima (Akun Login)</label>
          <input type="text" value="{{ auth()->user()->name }}" readonly
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm bg-gray-100 text-gray-600 cursor-not-allowed border border-gray-200">
        </div>

        <!-- ========================================================================= -->
        <!-- FITUR CLIENT-SIDE COMPRESSION & UNGGAH BERKAS -->
        <!-- ========================================================================= -->
        <div class="md:col-span-2 pt-2 border-t border-gray-100 space-y-3">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
              <label class="block text-xs font-semibold text-gray-800">
                Unggah Scan Surat Fisik (PDF / Gambar)
              </label>
              <p class="text-[11px] text-gray-500">100% diproses di browser (RAM) sebelum dikirim ke server.</p>
            </div>

            <!-- Pilihan Tingkat Kompresi (Tailwind UI) -->
            <div class="flex items-center gap-2 bg-gray-50 p-1.5 rounded-xl border border-gray-200">
              <span class="text-[10px] font-bold text-gray-500 uppercase px-1">Kompresi:</span>
              
              <label class="flex items-center gap-1 text-xs cursor-pointer px-2 py-1 rounded-lg transition"
                     :class="compressionLevel === '30' ? 'bg-white font-bold text-maroon shadow-2xs' : 'text-gray-600'">
                <input type="radio" name="compression_opt" value="30" x-model="compressionLevel" class="sr-only">
                30% (Ringan)
              </label>

              <label class="flex items-center gap-1 text-xs cursor-pointer px-2 py-1 rounded-lg transition"
                     :class="compressionLevel === '50' ? 'bg-white font-bold text-maroon shadow-2xs' : 'text-gray-600'">
                <input type="radio" name="compression_opt" value="50" x-model="compressionLevel" class="sr-only">
                50% (Sedang)
              </label>

              <label class="flex items-center gap-1 text-xs cursor-pointer px-2 py-1 rounded-lg transition"
                     :class="compressionLevel === '70' ? 'bg-white font-bold text-maroon shadow-2xs' : 'text-gray-600'">
                <input type="radio" name="compression_opt" value="70" x-model="compressionLevel" class="sr-only">
                70% (Maksimal)
              </label>
            </div>
          </div>

          <!-- Multi-file Dropzone -->
          <div class="border-2 border-dashed rounded-2xl p-6 text-center transition-colors cursor-pointer"
               :class="isDragging ? 'border-maroon bg-maroon-50/30' : 'border-gray-300 hover:border-maroon/60 bg-gray-50/50'"
               @dragover.prevent="isDragging = true"
               @dragleave.prevent="isDragging = false"
               @drop.prevent="handleFileDrop($event)"
               @click="$refs.fileInput.click()">
            <input type="file" x-ref="fileInput" class="hidden" accept=".pdf,image/jpeg,image/png"
                   @change="handleFileSelect($event)">
            
            <div class="flex flex-col items-center justify-center gap-1 text-gray-600">
              <svg class="w-8 h-8 text-maroon/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <p class="text-xs font-semibold text-gray-800 mt-1">
                Tarik & lepaskan berkas ke sini, atau <span class="text-maroon underline">pilih dari komputer</span>
              </p>
              <p class="text-[11px] text-gray-400">Mendukung dokumen .PDF, foto naskah .JPG, dan .PNG</p>
            </div>
          </div>

          <!-- Queue & Progress UI -->
          <template x-if="uploadQueue.length > 0">
            <div class="space-y-2 pt-1">
              <template x-for="(item, index) in uploadQueue" :key="index">
                <div class="p-3 bg-white border border-gray-200 rounded-xl shadow-2xs space-y-2">
                  <div class="flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2 truncate max-w-md">
                      <span class="font-bold text-gray-800 truncate" x-text="item.name"></span>
                      <span class="text-[10px] text-gray-400" x-text="'(' + formatBytes(item.originalSize) + ')'"></span>
                    </div>

                    <div class="flex items-center gap-3">
                      <!-- Status Teks -->
                      <span class="text-[11px] font-semibold"
                            :class="{
                              'text-amber-600': item.status === 'compressing',
                              'text-blue-600': item.status === 'uploading',
                              'text-emerald-600': item.status === 'ready' || item.status === 'done',
                              'text-red-600': item.status === 'error'
                            }"
                            x-text="item.statusText">
                      </span>

                      <!-- Tombol Unduh Lokal Menggunakan FileSaver.js -->
                      <template x-if="item.compressedBlob">
                        <button type="button" @click="downloadLocal(index)"
                                class="inline-flex items-center gap-1 text-[11px] font-semibold text-maroon hover:underline cursor-pointer">
                          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                          </svg>
                          Unduh (<span x-text="formatBytes(item.compressedSize)"></span>)
                        </button>
                      </template>

                      <!-- Hapus Berkas dari Antrean -->
                      <button type="button" @click="removeFromQueue(index)" class="text-gray-400 hover:text-red-600 text-xs">✕</button>
                    </div>
                  </div>

                  <!-- Progress Bar Visual (0 - 100%) -->
                  <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                    <div class="h-1.5 rounded-full transition-all duration-200"
                         :class="item.status === 'error' ? 'bg-red-500' : 'bg-maroon'"
                         :style="'width: ' + item.progress + '%'"></div>
                  </div>
                </div>
              </template>
            </div>
          </template>
        </div>

        <!-- ========================================================================= -->
        <!-- TANDA TANGAN PENERIMA -->
        <!-- ========================================================================= -->
        <div class="md:col-span-2 pt-2 border-t border-gray-100">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-semibold text-gray-700">
              Tanda Tangan Penerima
            </label>

            @if($lastSignature)
              <div class="flex items-center gap-2">
                <button type="button" 
                        @click="useSavedSignature = true; hasDrawn = false;"
                        class="text-xs px-2.5 py-1 rounded-lg border transition"
                        :class="useSavedSignature ? 'bg-maroon text-white border-maroon font-semibold' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'">
                  ✓ Gunakan TTD Tersimpan
                </button>
                <button type="button" 
                        @click="useSavedSignature = false; $nextTick(() => initSignatureCanvas())"
                        class="text-xs px-2.5 py-1 rounded-lg border transition"
                        :class="!useSavedSignature ? 'bg-maroon text-white border-maroon font-semibold' : 'bg-gray-50 text-gray-600 hover:bg-gray-100'">
                  ✏️ Gambar Baru
                </button>
              </div>
            @endif
          </div>

          @if($lastSignature)
            <div x-show="useSavedSignature" class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img src="{{ $lastSignature }}" alt="TTD Tersimpan" class="h-14 max-w-[120px] object-contain bg-white rounded-lg border border-emerald-300 p-1 shadow-2xs">
                <div>
                  <p class="text-xs font-bold text-emerald-900">Tanda Tangan Tersimpan Terdeteksi</p>
                  <p class="text-[11px] text-emerald-700">Tanda tangan akun Anda otomatis digunakan tanpa perlu menggambar ulang.</p>
                </div>
              </div>
              <button type="button" @click="useSavedSignature = false; $nextTick(() => initSignatureCanvas())" 
                      class="text-xs text-maroon font-semibold hover:underline">
                Ubah Tanda Tangan
              </button>
            </div>
          @endif

          <div x-show="!useSavedSignature" class="space-y-2">
            <div class="flex items-center justify-between">
              <span class="text-[11px] text-gray-500">Silakan buat paraf / tanda tangan pada kotak di bawah ini:</span>
              <button type="button" @click="clearSignature()" class="text-xs text-maroon hover:underline font-medium">
                Bersihkan Coretan
              </button>
            </div>

            <div class="border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 flex justify-center p-2">
              <canvas id="signatureCanvas" width="400" height="140" class="bg-white rounded-lg shadow-2xs border border-gray-200 touch-none"></canvas>
            </div>
          </div>

          <input type="hidden" name="ttd_penerima" id="ttd_penerima" :value="useSavedSignature ? savedSignatureData : ''">
        </div>
      </div>

      <!-- Action Buttons & Global Upload Status -->
      <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-gray-100">
        <div class="text-xs text-gray-500" x-text="globalStatusText"></div>

        <div class="flex items-center justify-end gap-3">
          <a href="{{ route('sigap-surat.masuk.index') }}" class="px-4 py-2 rounded-xl border text-sm text-gray-600 hover:bg-gray-50">
            Batal
          </a>
          <button type="submit" 
                  :disabled="isProcessing"
                  class="px-5 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-sm transition">
            <span x-show="isProcessing" class="animate-spin text-xs">⚪</span>
            <span x-text="isProcessing ? 'Memproses Berkas...' : 'Simpan Surat Masuk'"></span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function suratMasukForm() {
  return {
    compressionLevel: '50',
    isDragging: false,
    uploadQueue: [],
    isProcessing: false,
    globalStatusText: '',

    canvas: null,
    ctx: null,
    isDrawing: false,
    hasDrawn: false,
    useSavedSignature: {{ $lastSignature ? 'true' : 'false' }},
    savedSignatureData: @json($lastSignature ?? ''),

    init() {
      if (!this.useSavedSignature) {
        this.$nextTick(() => this.initSignatureCanvas());
      }
    },

    formatBytes(bytes) {
      if (!bytes || bytes === 0) return '0 B';
      const k = 1024;
      const sizes = ['B', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    },

    handleFileDrop(event) {
      this.isDragging = false;
      const files = event.dataTransfer.files;
      if (files.length > 0) {
        this.processInputFile(files[0]);
      }
    },

    handleFileSelect(event) {
      const files = event.target.files;
      if (files.length > 0) {
        this.processInputFile(files[0]);
      }
    },

    removeFromQueue(index) {
      this.uploadQueue.splice(index, 1);
      if (this.$refs.fileInput) this.$refs.fileInput.value = '';
    },

    // Tombol unduh lokal menggunakan FileSaver.js (saveAs)
    downloadLocal(index) {
      const item = this.uploadQueue[index];
      if (item && item.compressedBlob) {
        const ext = item.compressedBlob.type === 'application/pdf' ? '.pdf' : '.jpg';
        const cleanName = item.name.replace(/\.[^/.]+$/, "") + '_compressed' + ext;
        if (typeof saveAs !== 'undefined') {
          saveAs(item.compressedBlob, cleanName);
        } else {
          const link = document.createElement('a');
          link.href = URL.createObjectURL(item.compressedBlob);
          link.download = cleanName;
          link.click();
        }
      }
    },

    async processInputFile(file) {
      // Masukkan item awal ke antrean
      this.uploadQueue = [{
        name: file.name,
        originalSize: file.size,
        compressedSize: null,
        compressedBlob: null,
        status: 'compressing',
        statusText: 'Membaca dokumen...',
        progress: 10
      }];

      // KRITIS: Selalu gunakan indeks array langsung untuk reaktivitas Alpine.js
      const qIdx = 0;

      try {
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        const isImage = file.type.startsWith('image/');

        if (isPdf) {
          await this.compressPdfAdaptive(file, qIdx);
        } else if (isImage) {
          await this.compressImageAdaptive(file, qIdx);
        } else {
          this.setOriginal(qIdx, file, 'Format tidak dikompresi');
        }
      } catch (err) {
        console.error('Kompresi gagal, beralih ke file asli:', err);
        this.setOriginal(qIdx, file, 'Kompresi dilewati (Asli)');
      }
    },

    setOriginal(index, file, statusText) {
      if (!this.uploadQueue[index]) return;
      this.uploadQueue[index].compressedBlob = file;
      this.uploadQueue[index].compressedSize = file.size;
      this.uploadQueue[index].status = 'ready';
      this.uploadQueue[index].statusText = statusText || 'Siap diunggah (Asli)';
      this.uploadQueue[index].progress = 100;
    },

    // ALGORITMA 1: KOMPRESI PDF (PDF.js + Canvas + pdf-lib)
    async compressPdfAdaptive(file, index) {
      if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
        this.setOriginal(index, file, 'Library tidak tersedia');
        return;
      }

      let scale = 1.0;
      let quality = 0.55;

      if (this.compressionLevel === '30') {
        scale = 1.2;
        quality = 0.75;
      } else if (this.compressionLevel === '70') {
        scale = 0.75;
        quality = 0.35;
      }

      this.uploadQueue[index].statusText = 'Membaca struktur PDF...';
      this.uploadQueue[index].progress = 15;

      const fileBuffer = await file.arrayBuffer();
      const loadingTask = pdfjsLib.getDocument({ data: new Uint8Array(fileBuffer) });
      const pdfDoc = await loadingTask.promise;
      const numPages = pdfDoc.numPages;

      if (numPages === 0) {
        this.setOriginal(index, file);
        return;
      }

      const newPdfDoc = await PDFLib.PDFDocument.create();

      for (let i = 1; i <= numPages; i++) {
        // Update state reaktif via indeks langsung
        this.uploadQueue[index].progress = 15 + Math.round(((i - 1) / numPages) * 75);
        this.uploadQueue[index].statusText = `Mengompresi hal ${i} dari ${numPages}...`;

        const page = await pdfDoc.getPage(i);
        const viewport = page.getViewport({ scale: scale });

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        await page.render({ canvasContext: context, viewport: viewport }).promise;

        // Ekspor canvas ke JPEG
        const dataUrl = canvas.toDataURL('image/jpeg', quality);
        
        // Konversi Data URL ke Uint8Array secara aman
        const base64Data = dataUrl.split(',')[1];
        const binaryStr = atob(base64Data);
        const imgBytes = new Uint8Array(binaryStr.length);
        for (let k = 0; k < binaryStr.length; k++) {
          imgBytes[k] = binaryStr.charCodeAt(k);
        }

        const embeddedImg = await newPdfDoc.embedJpg(imgBytes);
        const newPage = newPdfDoc.addPage([viewport.width, viewport.height]);

        newPage.drawImage(embeddedImg, {
          x: 0,
          y: 0,
          width: viewport.width,
          height: viewport.height
        });

        // Manajemen RAM: Hancurkan referensi ukuran canvas pada setiap iterasi
        canvas.width = 0;
        canvas.height = 0;
      }

      this.uploadQueue[index].statusText = 'Menyusun ulang dokumen...';
      this.uploadQueue[index].progress = 95;

      const compressedPdfBytes = await newPdfDoc.save();
      const finalBlob = new Blob([compressedPdfBytes], { type: 'application/pdf' });

      // Jika hasil kompresi ternyata lebih besar, tetap pertahankan file asli
      if (finalBlob.size < file.size) {
        this.uploadQueue[index].compressedBlob = finalBlob;
        this.uploadQueue[index].compressedSize = finalBlob.size;
        this.uploadQueue[index].statusText = `Selesai (${this.formatBytes(finalBlob.size)})`;
      } else {
        this.uploadQueue[index].compressedBlob = file;
        this.uploadQueue[index].compressedSize = file.size;
        this.uploadQueue[index].statusText = 'Ukuran asli sudah optimal';
      }

      this.uploadQueue[index].status = 'ready';
      this.uploadQueue[index].progress = 100;
    },

    // ALGORITMA 2: KOMPRESI GAMBAR (Canvas + maxDim + quality)
    compressImageAdaptive(file, index) {
      return new Promise((resolve) => {
        let maxDim = 1600;
        let quality = 0.55;

        if (this.compressionLevel === '30') {
          maxDim = 1920;
          quality = 0.75;
        } else if (this.compressionLevel === '70') {
          maxDim = 1200;
          quality = 0.35;
        }

        this.uploadQueue[index].statusText = 'Mengompresi gambar...';
        this.uploadQueue[index].progress = 40;

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
          const img = new Image();
          img.src = event.target.result;
          img.onload = () => {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;

            if (width > height && width > maxDim) {
              height = Math.round((height * maxDim) / width);
              width = maxDim;
            } else if (height > maxDim) {
              width = Math.round((width * maxDim) / height);
              height = maxDim;
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob((blob) => {
              if (blob && blob.size < file.size) {
                this.uploadQueue[index].compressedBlob = blob;
                this.uploadQueue[index].compressedSize = blob.size;
                this.uploadQueue[index].statusText = `Selesai (${this.formatBytes(blob.size)})`;
              } else {
                this.uploadQueue[index].compressedBlob = file;
                this.uploadQueue[index].compressedSize = file.size;
                this.uploadQueue[index].statusText = 'Ukuran asli sudah optimal';
              }
              this.uploadQueue[index].status = 'ready';
              this.uploadQueue[index].progress = 100;

              canvas.width = 0;
              canvas.height = 0;
              resolve();
            }, 'image/jpeg', quality);
          };
          img.onerror = () => {
            this.setOriginal(index, file);
            resolve();
          };
        };
        reader.onerror = () => {
          this.setOriginal(index, file);
          resolve();
        };
      });
    },

    // ALGORITMA 3: UPLOAD ASINKRON VIA XHR (DENGAN PROGRESS BAR)
    submitFormViaXhr() {
      const form = document.getElementById('formSuratMasuk');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }

      const ttdInput = document.getElementById('ttd_penerima');
      if (this.useSavedSignature && this.savedSignatureData) {
        if (ttdInput) ttdInput.value = this.savedSignatureData;
      } else if (this.hasDrawn && this.canvas) {
        if (ttdInput) ttdInput.value = this.canvas.toDataURL('image/png');
      }

      const formData = new FormData(form);

      // Lampirkan berkas hasil kompresi ke FormData
      if (this.uploadQueue.length > 0 && this.uploadQueue[0].compressedBlob) {
        const item = this.uploadQueue[0];
        const ext = item.compressedBlob.type === 'application/pdf' ? '.pdf' : '.jpg';
        const finalName = item.name.replace(/\.[^/.]+$/, "") + ext;
        formData.set('file_surat', item.compressedBlob, finalName);
      }

      this.isProcessing = true;
      this.globalStatusText = 'Mempersiapkan pengunggahan...';

      const xhr = new XMLHttpRequest();
      xhr.open('POST', form.action, true);
      xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

      xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
          const percent = Math.round((e.loaded / e.total) * 100);
          this.globalStatusText = `Mengunggah berkas: ${percent}%`;
          if (this.uploadQueue.length > 0) {
            this.uploadQueue[0].status = 'uploading';
            this.uploadQueue[0].progress = percent;
            this.uploadQueue[0].statusText = `Mengunggah ${percent}%`;
          }
        }
      });

      xhr.onload = () => {
        if (xhr.status >= 200 && xhr.status < 300) {
          this.globalStatusText = 'Selesai! Mengalihkan halaman...';
          window.location.href = "{{ route('sigap-surat.masuk.index') }}";
        } else {
          this.isProcessing = false;
          this.globalStatusText = 'Gagal menyimpan surat masuk.';
          alert('Terjadi kesalahan pada server saat menyimpan surat masuk. Periksa kembali form.');
        }
      };

      xhr.onerror = () => {
        this.isProcessing = false;
        this.globalStatusText = 'Koneksi terputus saat mengunggah.';
        alert('Gagal terhubung ke server.');
      };

      xhr.send(formData);
    },

    // Inisialisasi Canvas TTD
    initSignatureCanvas() {
      this.canvas = document.getElementById('signatureCanvas');
      if (!this.canvas) return;
      this.ctx = this.canvas.getContext('2d');
      this.ctx.lineWidth = 2;
      this.ctx.lineCap = 'round';
      this.ctx.strokeStyle = '#1e293b';

      const startDraw = (e) => {
        this.isDrawing = true;
        this.hasDrawn = true;
        const rect = this.canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        this.ctx.beginPath();
        this.ctx.moveTo(clientX - rect.left, clientY - rect.top);
      };

      const draw = (e) => {
        if (!this.isDrawing) return;
        e.preventDefault();
        const rect = this.canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        this.ctx.lineTo(clientX - rect.left, clientY - rect.top);
        this.ctx.stroke();
      };

      const stopDraw = () => { this.isDrawing = false; };

      this.canvas.addEventListener('mousedown', startDraw);
      this.canvas.addEventListener('mousemove', draw);
      window.addEventListener('mouseup', stopDraw);

      this.canvas.addEventListener('touchstart', startDraw, { passive: false });
      this.canvas.addEventListener('touchmove', draw, { passive: false });
      this.canvas.addEventListener('touchend', stopDraw);
    },

    clearSignature() {
      if (!this.ctx) return;
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      this.hasDrawn = false;
      const hiddenInput = document.getElementById('ttd_penerima');
      if (hiddenInput) hiddenInput.value = '';
    }
  };
}
</script>
@endpush