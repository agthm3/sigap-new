@extends('layouts.app')

@push('head')
  <!-- Engine Client-Side PDF Processing & Downloader (Privacy First) -->
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
<section class="max-w-4xl mx-auto px-4 py-6">
  <!-- Header Form -->
  <div class="flex items-center justify-between mb-6">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-maroon-100 text-maroon">
          SIGAP DOKUMEN &amp; OPTIMIZE
        </span>
      </div>
      <h1 class="text-2xl font-extrabold text-gray-900">Upload &amp; Indeks Dokumen</h1>
      <p class="text-sm text-gray-600 mt-1">
        Lengkapi metadata naskah dinas dan manfaatkan kompresi client-side hemat ukuran sebelum berkas diarsipkan.
      </p>
    </div>
    <a href="{{ $folder ? route('sigap-dokumen.folder.show', $folder) : route('sigap-dokumen.saya') }}"
       class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
      Kembali
    </a>
  </div>

  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8" x-data="uploadManager(@js($existingTags))">
    <form @submit.prevent="submitForm()" class="space-y-6">
      @csrf

      @if($folder)
        <div class="p-4 bg-maroon/5 border border-maroon/20 rounded-xl flex items-center justify-between text-xs font-semibold text-maroon">
          <div class="flex items-center gap-2">
            <span class="text-base">{{ $folder->icon ?? '📁' }}</span>
            <span>Folder Penempatan: <strong>{{ $folder->name }}</strong></span>
          </div>
          <span class="px-2 py-0.5 rounded bg-maroon/10 text-[10px] uppercase font-bold">
            {{ $folder->visibility === 'public' ? 'Publik' : 'Privat' }}
          </span>
        </div>
      @endif

      <!-- BAGIAN 1: Identitas & Legalitas Dokumen -->
      <div class="space-y-4">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>1. Identitas &amp; Legalitas Dokumen</span>
        </h2>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700">Nomor Surat / Naskah Dinas</label>
            <input type="text" x-model="form.number" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon font-mono" placeholder="Contoh: 000.1.2/15/BRIDA/I/2026">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Tanggal Penetapan / Surat</label>
            <input type="date" x-model="form.doc_date" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700">Judul / Perihal Dokumen <span class="text-red-500">*</span></label>
            <input type="text" x-model="form.title" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon font-medium" placeholder="Contoh: Penetapan Tim Pelaksana Kajian Kelayakan Inovasi Daerah Tahun 2026">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Kategori Dokumen <span class="text-red-500">*</span></label>
            <select x-model="form.category" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
              <option value="">-- Pilih Kategori --</option>
              <option value="Surat Keputusan">Surat Keputusan (SK)</option>
              <option value="Laporan">Laporan Kegiatan / Kinerja</option>
              <option value="Formulir">Formulir / Template</option>
              <option value="Surat Masuk/Keluar">Surat Masuk / Surat Keluar</option>
              <option value="Dokumen Teknis">Dokumen Teknis / KAK / Kerangka Acuan</option>
              <option value="Privasi">Dokumen Rahasia / Personel</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Tahun Anggaran / Terbit <span class="text-red-500">*</span></label>
            <input type="number" x-model="form.year" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700">Pihak Terkait / Instansi Pengirim / Mitra</label>
            <input type="text" x-model="form.stakeholder" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon" placeholder="Contoh: Bappeda Kota Makassar, Universitas Hasanuddin">
          </div>
        </div>
      </div>

      <!-- BAGIAN 2: Ringkasan Isi & Kata Kunci -->
      <div class="space-y-4 pt-2">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>2. Konteks Pencarian &amp; Metadata Tag</span>
        </h2>

        <div>
          <label class="block text-sm font-semibold text-gray-700">Ringkasan Isi / Catatan Pokok Dokumen</label>
          <textarea x-model="form.description" rows="3" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon" placeholder="Tuliskan 1–3 kalimat inti isi surat atau kata kunci pokok bahasan..."></textarea>
        </div>

        <div>
          <label class="block text-sm font-semibold text-gray-700">Label / Tag Pencarian (Tekan Enter atau Koma)</label>
          <div class="relative mt-1.5">
            <input type="text" x-model="tagInput" @keydown.enter.prevent="addTag(tagInput)" @keydown.comma.prevent="addTag(tagInput)" placeholder="Ketik kata kunci lalu tekan Enter..." class="w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
            
            <div x-show="tagSuggestions.length > 0" class="absolute z-20 w-full bg-white border border-gray-200 rounded-lg shadow-xl mt-1 p-2 max-h-36 overflow-y-auto">
              <div class="text-[10px] uppercase font-bold text-gray-400 px-2 py-1">Pilih dari tag yang sudah ada:</div>
              <template x-for="s in tagSuggestions" :key="s">
                <button type="button" @click="addTag(s)" class="block w-full text-left px-2.5 py-1.5 text-xs text-gray-700 hover:bg-maroon/10 hover:text-maroon rounded-md transition" x-text="'# ' + s"></button>
              </template>
            </div>
          </div>

          <div class="flex flex-wrap gap-1.5 mt-2.5">
            <template x-for="(t, idx) in form.tags" :key="idx">
              <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-maroon/10 text-maroon border border-maroon/20">
                <span x-text="'#' + t"></span>
                <button type="button" @click="removeTag(idx)" class="hover:text-red-700 font-bold ml-1">&times;</button>
              </span>
            </template>
          </div>
        </div>
      </div>

      <!-- BAGIAN 3: Keamanan Akses & Lokasi Fisik -->
      <div class="space-y-4 pt-2">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>3. Keamanan Akses &amp; Lokasi Fisik Arsip</span>
        </h2>

        <div>
          <label class="block text-sm font-semibold text-gray-700">Tingkat Kerahasiaan (Sensitivitas)</label>
          <div class="grid grid-cols-2 gap-3 mt-1.5">
            <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
              <input type="radio" name="sensitivity" value="public" x-model="form.sensitivity" class="mt-0.5 text-emerald-600 focus:ring-emerald-600">
              <div>
                <p class="text-xs font-bold text-gray-900">Publik (Terbuka)</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Dapat dilihat dan dicari pada katalog Dokumen Umum oleh seluruh staf.</p>
              </div>
            </label>

            <label class="flex items-start gap-3 p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/40">
              <input type="radio" name="sensitivity" value="private" x-model="form.sensitivity" class="mt-0.5 text-red-600 focus:ring-red-600">
              <div>
                <p class="text-xs font-bold text-gray-900">Privat (Terkunci)</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Tersimpan di vault tertutup server, hanya akun Anda yang dapat membuka.</p>
              </div>
            </label>
          </div>
        </div>

        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
          <div class="flex items-center gap-2 mb-2.5">
            <span class="text-sm">🗄️</span>
            <span class="text-xs font-bold uppercase text-gray-700 tracking-wider">Lokasi Fisik Berkas Hardcopy (Opsional)</span>
          </div>
          <div class="grid sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-600">Nomor / Nama Rak / Lemari</label>
              <input type="text" x-model="form.physical_rack" placeholder="Contoh: RAK-02 KEUANGAN" class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-xs focus:border-maroon focus:ring-maroon">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600">Nomor Ordner / Baris / Boks</label>
              <input type="text" x-model="form.physical_row" placeholder="Contoh: BOKS-05 / NO. 14" class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-xs focus:border-maroon focus:ring-maroon">
            </div>
          </div>
        </div>
      </div>

      <!-- BAGIAN 4: Lampiran Berkas & Kompresi -->
      <div class="space-y-4 pt-2">
        <div class="border-b pb-2 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
          <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">4. Lampiran Berkas Digital</h2>
            <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded font-bold">
              ✓ Client-Side Compression Aktif
            </span>
          </div>
          <span class="text-[11px] text-gray-400">PDF &bull; JPG &bull; PNG (Maks 20MB)</span>
        </div>

        <!-- Privacy First Notification -->
        <div class="p-3 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-start gap-2.5 text-xs text-emerald-900">
          <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
          <div>
            <strong>Privacy First:</strong> Seluruh proses membaca dan mengecilkan file diproses di RAM browser Anda. File tidak diunggah ke server pihak ketiga.
          </div>
        </div>

        <!-- Pilihan Persentase Kompresi -->
        <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl space-y-2.5">
          <div>
            <label class="text-xs font-bold text-gray-800 block">Tingkat Kompresi Berkas</label>
            <p class="text-[11px] text-gray-500">Pilih intensitas kompresi file sebelum diunggah ke sistem.</p>
          </div>

          <div class="grid grid-cols-3 gap-2.5 pt-1">
            <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition text-center hover:bg-white has-[:checked]:border-maroon has-[:checked]:bg-white has-[:checked]:shadow-xs">
              <input type="radio" name="compressionLevel" value="30" x-model="compressionPercent" class="sr-only">
              <span class="text-sm font-black text-gray-900" :class="compressionPercent === '30' ? 'text-maroon' : ''">Kompres 30%</span>
              <span class="text-[10px] text-gray-500 mt-0.5">Ringan</span>
            </label>

            <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition text-center hover:bg-white has-[:checked]:border-maroon has-[:checked]:bg-white has-[:checked]:shadow-xs">
              <input type="radio" name="compressionLevel" value="50" x-model="compressionPercent" class="sr-only">
              <div class="flex items-center justify-center gap-1">
                <span class="text-sm font-black text-gray-900" :class="compressionPercent === '50' ? 'text-maroon' : ''">Kompres 50%</span>
                <span class="text-[9px] bg-emerald-100 text-emerald-800 font-extrabold px-1 rounded">Ideal</span>
              </div>
              <span class="text-[10px] text-gray-500 mt-0.5">Sedang</span>
            </label>

            <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition text-center hover:bg-white has-[:checked]:border-maroon has-[:checked]:bg-white has-[:checked]:shadow-xs">
              <input type="radio" name="compressionLevel" value="70" x-model="compressionPercent" class="sr-only">
              <span class="text-sm font-black text-gray-900" :class="compressionPercent === '70' ? 'text-maroon' : ''">Kompres 70%</span>
              <span class="text-[10px] text-gray-500 mt-0.5">Maksimal</span>
            </label>
          </div>
        </div>

        <!-- Dropzone Box -->
        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-maroon transition cursor-pointer bg-gray-50/50"
             @click="$refs.fileInput.click()"
             @dragover.prevent=""
             @drop.prevent="handleDrop($event)">
          <input type="file" 
                 x-ref="fileInput" 
                 @change="handleFiles($event.target.files); $event.target.value = ''" 
                 multiple 
                 class="hidden" 
                 accept=".pdf,.png,.jpg,.jpeg">
          <div class="w-12 h-12 mx-auto rounded-full bg-maroon/10 text-maroon flex items-center justify-center text-2xl mb-2">
            📄
          </div>
          <p class="text-sm font-semibold text-gray-800">Tarik berkas ke sini atau klik untuk memilih</p>
          <p class="text-xs text-gray-500 mt-1">Mendukung multi-file. Berkas PDF dan Foto akan langsung dikompresi adaptif di browser.</p>
        </div>

        <!-- Antrean Berkas & Status Bar -->
        <div class="space-y-3 mt-4" x-show="uploadQueue.length > 0">
          <template x-for="(item, index) in uploadQueue" :key="index">
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl space-y-2 shadow-2xs">
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-3">
                  <span x-text="item.isPdf ? '📕' : '🖼️'" class="text-xl"></span>
                  <div>
                    <p class="font-bold text-gray-800 truncate max-w-xs sm:max-w-md" x-text="item.name"></p>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                      Asli: <span class="font-semibold text-gray-700" x-text="formatBytes(item.origSize)"></span>
                      <template x-if="item.compressedSize">
                        <span class="text-emerald-600 font-bold ml-1">
                          &rarr; Hasil: <span x-text="formatBytes(item.compressedSize)"></span>
                          <template x-if="item.savingsPercent > 0">
                            <span>(<span x-text="item.savingsPercent + '% hemat'"></span>)</span>
                          </template>
                        </span>
                      </template>
                    </p>
                  </div>
                </div>

                <!-- Status Text & Action Buttons -->
                <div class="flex items-center gap-2">
                  <template x-if="item.status === 'compressing'">
                    <span class="text-indigo-600 font-bold flex items-center gap-1.5 animate-pulse">
                      <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                      </svg>
                      <span x-text="item.compressMsg"></span>
                    </span>
                  </template>
                  <template x-if="item.status === 'uploading'">
                    <span class="text-amber-600 font-semibold" x-text="'Mengunggah ' + item.uploadProgress + '%'"></span>
                  </template>
                  <template x-if="item.status === 'done'">
                    <div class="flex items-center gap-1.5">
                      <span class="text-emerald-600 font-bold flex items-center gap-1">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        Siap
                      </span>
                      <template x-if="item.blob">
                        <button type="button" 
                                @click="downloadCompressedFile(item)" 
                                title="Unduh hasil kompresi ke perangkat"
                                class="px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700 text-[10px] font-bold transition">
                          Unduh
                        </button>
                      </template>
                    </div>
                  </template>
                  <template x-if="item.status === 'failed'">
                    <span class="text-red-600 font-bold">Gagal</span>
                  </template>

                  <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-red-600 text-lg ml-1">&times;</button>
                </div>
              </div>

              <!-- Progress Bar -->
              <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="h-2 rounded-full transition-all duration-300"
                     :class="{
                        'bg-indigo-500': item.status === 'compressing',
                        'bg-amber-500': item.status === 'uploading',
                        'bg-emerald-500': item.status === 'done',
                        'bg-red-500': item.status === 'failed'
                     }"
                     :style="'width: ' + (item.status === 'compressing' ? item.compressProgress : (item.status === 'uploading' ? item.uploadProgress : 100)) + '%'"></div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Tombol Aksi Submit -->
      <div class="pt-6 border-t flex items-center justify-end gap-3">
        <a href="{{ $folder ? route('sigap-dokumen.folder.show', $folder) : route('sigap-dokumen.saya') }}"
           class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition">
          Batal
        </a>
        <button type="submit" 
                :disabled="isSubmitting || isAnyProcessing"
                :class="(isSubmitting || isAnyProcessing) ? 'opacity-50 cursor-not-allowed' : ''"
                class="px-7 py-2.5 rounded-lg bg-maroon text-white font-bold text-sm hover:bg-maroon-800 transition shadow-md flex items-center gap-2">
          <span x-show="!isSubmitting">Simpan &amp; Indeks Dokumen</span>
          <span x-show="isSubmitting">Menyimpan ke Sistem...</span>
        </button>
      </div>
    </form>
  </div>
</section>

<script>
function uploadManager(availableTags) {
  return {
    form: {
      number: '',
      doc_date: '',
      title: '',
      category: '',
      year: new Date().getFullYear(),
      stakeholder: '',
      description: '',
      sensitivity: 'public',
      tags: [],
      physical_rack: '',
      physical_row: '',
      folder_id: '{{ $folderId ?? '' }}',
      files: []
    },
    tagInput: '',
    allTags: availableTags || [],
    uploadQueue: [],
    isSubmitting: false,

    // Pilihan Tingkat Kompresi
    compressionPercent: '50',

    get isAnyProcessing() {
      return this.uploadQueue.some(item => item.status === 'compressing' || item.status === 'uploading');
    },

    get tagSuggestions() {
      if (!this.tagInput.trim()) return [];
      const q = this.tagInput.toLowerCase();
      return this.allTags.filter(t => t.toLowerCase().includes(q) && !this.form.tags.includes(t));
    },

    formatBytes(bytes) {
      if (!bytes || bytes === 0) return '0 B';
      const k = 1024;
      const sizes = ['B', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    },

    addTag(tag) {
      const clean = tag.replace(/,/g, '').trim();
      if (clean && !this.form.tags.includes(clean)) {
        this.form.tags.push(clean);
      }
      this.tagInput = '';
    },

    removeTag(index) {
      this.form.tags.splice(index, 1);
    },

    handleDrop(e) {
      const files = e.dataTransfer.files;
      if (files && files.length > 0) this.handleFiles(files);
    },

    async handleFiles(files) {
      if (!files || files.length === 0) return;

      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        const isImg = file.type.startsWith('image/');

        // Push Objek Mentah (Raw) ke dalam Proxy Array Alpine
        this.uploadQueue.push({
          file: file,
          name: file.name,
          origSize: file.size,
          compressedSize: null,
          savingsPercent: 0,
          isPdf: isPdf,
          isImage: isImg,
          compressProgress: 10,
          uploadProgress: 0,
          compressMsg: 'Menyiapkan berkas...',
          status: 'compressing',
          blob: null
        });

        // AMBIL REFERENSI PROXY AGAR REAKTIVITAS UI BERJALAN (INI YANG MEMPERBAIKI MASALAH "STUCK")
        const qIndex = this.uploadQueue.length - 1;
        let processedFile = file;

        try {
          if (isPdf) {
            processedFile = await this.compressPdfAdaptive(file, qIndex);
          } else if (isImg) {
            processedFile = await this.compressImageAdaptive(file, qIndex);
          }
          
          this.uploadQueue[qIndex].compressedSize = processedFile.size;
          this.uploadQueue[qIndex].blob = processedFile;
          
          if (this.uploadQueue[qIndex].origSize > processedFile.size) {
            this.uploadQueue[qIndex].savingsPercent = Math.round(((this.uploadQueue[qIndex].origSize - processedFile.size) / this.uploadQueue[qIndex].origSize) * 100);
          }
        } catch (err) {
          console.error('Kompresi error/dilewati:', err);
          processedFile = file;
          this.uploadQueue[qIndex].compressedSize = file.size;
          this.uploadQueue[qIndex].blob = file;
        }

        this.uploadQueue[qIndex].status = 'uploading';
        await this.uploadWithXHR(processedFile, qIndex);
      }
    },

    // 1. ENGINE KOMPRESI PDF BERBASIS GAMBAR (PDF.js + Canvas + PDFLib)
    async compressPdfAdaptive(currentFile, qIndex) {
      if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
        return currentFile;
      }

      let scale = 1.0;
      let quality = 0.55;

      if (this.compressionPercent === '30') {
        scale = 1.2;
        quality = 0.75;
      } else if (this.compressionPercent === '70') {
        scale = 0.75;
        quality = 0.35;
      }

      const fileBuffer = await currentFile.arrayBuffer();
      const loadingTask = pdfjsLib.getDocument({ data: fileBuffer });
      const pdfDoc = await loadingTask.promise;
      const numPages = pdfDoc.numPages;

      if (numPages === 0) return currentFile;

      const newPdfDoc = await PDFLib.PDFDocument.create();

      for (let i = 1; i <= numPages; i++) {
        this.uploadQueue[qIndex].compressMsg = `Mengompresi hal ${i} dari ${numPages}...`;
        this.uploadQueue[qIndex].compressProgress = Math.round(((i - 1) / numPages) * 100);

        const page = await pdfDoc.getPage(i);
        const viewport = page.getViewport({ scale: scale });

        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        await page.render({
          canvasContext: context,
          viewport: viewport
        }).promise;

        const dataUrl = canvas.toDataURL('image/jpeg', quality);
        const imgBytes = this.dataURLtoUint8Array(dataUrl);

        const embeddedImg = await newPdfDoc.embedJpg(imgBytes);
        const newPage = newPdfDoc.addPage([viewport.width, viewport.height]);

        newPage.drawImage(embeddedImg, {
          x: 0,
          y: 0,
          width: viewport.width,
          height: viewport.height
        });

        // Bersihkan memori canvas
        canvas.width = 0;
        canvas.height = 0;
      }

      this.uploadQueue[qIndex].compressMsg = 'Menyusun hasil PDF...';
      this.uploadQueue[qIndex].compressProgress = 95;

      const compressedPdfBytes = await newPdfDoc.save();
      const finalBlob = new Blob([compressedPdfBytes], { type: 'application/pdf' });

      this.uploadQueue[qIndex].compressProgress = 100;

      if (finalBlob.size < currentFile.size) {
        return new File([finalBlob], currentFile.name, {
          type: 'application/pdf',
          lastModified: Date.now()
        });
      }

      return currentFile;
    },

    // 2. ENGINE KOMPRESI GAMBAR
    compressImageAdaptive(file, qIndex) {
      return new Promise((resolve) => {
        this.uploadQueue[qIndex].compressMsg = 'Mengompres gambar...';
        this.uploadQueue[qIndex].compressProgress = 50;

        let maxDim = 1600;
        let quality = 0.65;

        if (this.compressionPercent === '30') {
          maxDim = 1920;
          quality = 0.80;
        } else if (this.compressionPercent === '70') {
          maxDim = 1200;
          quality = 0.45;
        }

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
              this.uploadQueue[qIndex].compressProgress = 100;
              if (blob && blob.size < file.size) {
                resolve(new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }));
              } else {
                resolve(file);
              }
              canvas.width = 0;
              canvas.height = 0;
            }, 'image/jpeg', quality);
          };
          img.onerror = () => resolve(file);
        };
        reader.onerror = () => resolve(file);
      });
    },

    dataURLtoUint8Array(dataurl) {
      const arr = dataurl.split(',');
      const bstr = atob(arr[1]);
      let n = bstr.length;
      const u8arr = new Uint8Array(n);
      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }
      return u8arr;
    },

    // 3. Upload Asinkron per-file dengan XHR
    uploadWithXHR(file, qIndex) {
      return new Promise((resolve) => {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        xhr.upload.addEventListener('progress', (e) => {
          if (e.lengthComputable) {
            this.uploadQueue[qIndex].uploadProgress = Math.round((e.loaded / e.total) * 100);
          }
        });

        xhr.onreadystatechange = () => {
          if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
              try {
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                  this.uploadQueue[qIndex].status = 'done';
                  this.uploadQueue[qIndex].uploadProgress = 100;
                  this.form.files.push(res.temp_path);
                } else {
                  this.uploadQueue[qIndex].status = 'failed';
                }
              } catch (e) {
                this.uploadQueue[qIndex].status = 'failed';
              }
            } else {
              this.uploadQueue[qIndex].status = 'failed';
            }
            resolve();
          }
        };

        xhr.onerror = () => {
          this.uploadQueue[qIndex].status = 'failed';
          resolve();
        };

        xhr.open('POST', '{{ route('sigap-dokumen.temp-upload') }}', true);
        xhr.send(formData);
      });
    },

    downloadCompressedFile(item) {
      if (typeof saveAs !== 'undefined' && item.blob) {
        saveAs(item.blob, `compressed_${item.name}`);
      }
    },

    removeFile(idx) {
      this.uploadQueue.splice(idx, 1);
      this.form.files.splice(idx, 1);
    },

    async submitForm() {
      if (this.form.files.length === 0) {
        Swal.fire({ 
          icon: 'warning', 
          title: 'Lampiran Kosong', 
          text: 'Harap pilih dan unggah minimal satu berkas dokumen.' 
        });
        return;
      }

      if (this.isAnyProcessing) {
        Swal.fire({ 
          icon: 'info', 
          title: 'Berkas Masih Diproses', 
          text: 'Harap tunggu hingga seluruh berkas selesai dikompresi dan diunggah.' 
        });
        return;
      }

      this.isSubmitting = true;

      const hiddenForm = document.createElement('form');
      hiddenForm.method = 'POST';
      hiddenForm.action = '{{ route('sigap-dokumen.store') }}';

      const appendInput = (name, val) => {
        if (val !== null && val !== undefined) {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = name;
          input.value = val;
          hiddenForm.appendChild(input);
        }
      };

      appendInput('_token', '{{ csrf_token() }}');
      appendInput('number', this.form.number);
      appendInput('doc_date', this.form.doc_date);
      appendInput('title', this.form.title);
      appendInput('category', this.form.category);
      appendInput('year', this.form.year);
      appendInput('stakeholder', this.form.stakeholder);
      appendInput('description', this.form.description);
      appendInput('sensitivity', this.form.sensitivity);
      appendInput('tags', this.form.tags.join(','));
      appendInput('physical_rack', this.form.physical_rack);
      appendInput('physical_row', this.form.physical_row);
      if (this.form.folder_id) {
        appendInput('folder_id', this.form.folder_id);
      } else {
        appendInput('folder_id', '');
      }

      this.form.files.forEach(f => {
        appendInput('files[]', f);
      });

      document.body.appendChild(hiddenForm);
      hiddenForm.submit();
    }
  };
}
</script>
@endsection