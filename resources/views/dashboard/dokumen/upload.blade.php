@extends('layouts.app')

@push('head')
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
        Pilih berkas terlebih dahulu untuk ekstraksi otomatis judul &amp; tag, lalu sesuaikan metadata arsip dinas.
      </p>
    </div>
    <a href="{{ $folder ? route('sigap-dokumen.folder.show', $folder) : route('sigap-dokumen.saya') }}"
       class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
      Kembali
    </a>
  </div>

  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6 sm:p-8" x-data="uploadManager(@js($existingTags))">
    
    <!-- Datalist untuk Autocomplete Tag Native HTML5 -->
    <datalist id="existing-tags">
      <template x-for="tag in allTags" :key="tag">
        <option :value="tag"></option>
      </template>
    </datalist>

    <form @submit.prevent="submitForm()" class="space-y-7">
      @csrf

      @if($folder)
        <div class="p-4 bg-maroon/5 border border-maroon/20 rounded-xl flex items-center justify-between text-xs font-semibold text-maroon">
          <div class="flex items-center gap-2">
            <span class="text-base">{{ $folder->icon ?? '📁' }}</span>
            <span>Folder Penempatan: <strong>{{ $folder->name }}</strong></span>
          </div>
          <span class="px-2 py-0.5 rounded bg-maroon/10 text-[10px] uppercase font-bold">
            {{ $folder->visibility === 'public' ? 'Publik' : ($folder->visibility === 'internal' ? 'Internal' : 'Privat') }}
          </span>
        </div>
      @endif

      <!-- ======================================================== -->
      <!-- BAGIAN 1: LAMPIRAN & METADATA INDIVIDUAL BERKAS          -->
      <!-- ======================================================== -->
      <div class="space-y-4">
        <div class="border-b pb-2 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
          <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">1. Lampiran Berkas Digital</h2>
            <span class="text-[10px] text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded font-bold">
              ✓ Smart Extraction Aktif
            </span>
          </div>
          <span class="text-[11px] text-gray-400">Tarik banyak file sekaligus. Judul &amp; Tag otomatis menyesuaikan masing-masing file.</span>
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
            </label>
            <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition text-center hover:bg-white has-[:checked]:border-maroon has-[:checked]:bg-white has-[:checked]:shadow-xs">
              <input type="radio" name="compressionLevel" value="50" x-model="compressionPercent" class="sr-only">
              <span class="text-sm font-black text-gray-900" :class="compressionPercent === '50' ? 'text-maroon' : ''">Kompres 50%</span>
            </label>
            <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition text-center hover:bg-white has-[:checked]:border-maroon has-[:checked]:bg-white has-[:checked]:shadow-xs">
              <input type="radio" name="compressionLevel" value="70" x-model="compressionPercent" class="sr-only">
              <span class="text-sm font-black text-gray-900" :class="compressionPercent === '70' ? 'text-maroon' : ''">Kompres 70%</span>
            </label>
          </div>
        </div>

        <!-- Dropzone Box -->
        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-7 text-center hover:border-maroon transition cursor-pointer bg-gray-50/50"
             @click="$refs.fileInput.click()"
             @dragover.prevent=""
             @drop.prevent="handleDrop($event)">
          <input type="file" 
                 x-ref="fileInput" 
                 @change="handleFiles($event.target.files); $event.target.value = ''" 
                 multiple 
                 class="hidden" 
                 accept=".pdf,.png,.jpg,.jpeg">
          <div class="w-14 h-14 mx-auto rounded-full bg-maroon/10 text-maroon flex items-center justify-center text-3xl mb-3">
            📁
          </div>
          <p class="text-sm font-bold text-gray-800">Tarik berkas ke sini atau klik untuk memilih</p>
          <p class="text-xs text-gray-500 mt-1">Setiap berkas akan dibuatkan form judul dan tag secara terpisah.</p>
        </div>

        <!-- Antrean Berkas & Metadata Individual -->
        <div class="space-y-4 mt-4" x-show="uploadQueue.length > 0">
          <template x-for="(item, index) in uploadQueue" :key="index">
            <div class="p-3.5 bg-white border border-gray-200 rounded-xl space-y-2 shadow-2xs">
              <!-- Header Baris File -->
              <div class="flex items-center justify-between text-xs">
                <div class="flex items-center gap-3">
                  <span x-text="item.isPdf ? '📕' : '🖼️'" class="text-xl"></span>
                  <div>
                    <p class="font-bold text-gray-800 truncate max-w-xs sm:max-w-md" x-text="item.name"></p>
                    <p class="text-[11px] text-gray-500 mt-0.5">
                      Ukuran Asli: <span class="font-semibold text-gray-700" x-text="formatBytes(item.origSize)"></span>
                    </p>
                  </div>
                </div>

                <!-- Status Progress & Actions -->
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
                    <span class="text-amber-600 font-semibold" x-text="'Mempersiapkan ' + item.uploadProgress + '%'"></span>
                  </template>
                  <template x-if="item.status === 'done'">
                    <span class="text-emerald-600 font-bold flex items-center gap-1">✓ Siap Disimpan</span>
                  </template>
                  <template x-if="item.status === 'saving'">
                    <span class="text-indigo-600 font-bold animate-pulse">Sedang Menyimpan...</span>
                  </template>
                  <template x-if="item.status === 'saved'">
                    <span class="text-emerald-600 font-extrabold flex items-center gap-1">✓ Tersimpan</span>
                  </template>
                  <template x-if="item.status === 'failed'">
                    <span class="text-red-600 font-bold">Gagal</span>
                  </template>

                  <button type="button" @click="removeFile(index)" class="text-gray-400 hover:text-red-600 text-lg ml-1" :disabled="isSubmitting">&times;</button>
                </div>
              </div>

              <!-- Progress Bar Track -->
              <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                <div class="h-1.5 rounded-full transition-all duration-300"
                     :class="{
                        'bg-indigo-500': item.status === 'compressing',
                        'bg-amber-500': item.status === 'uploading',
                        'bg-emerald-500': item.status === 'done' || item.status === 'saved',
                        'bg-blue-600': item.status === 'saving',
                        'bg-red-500': item.status === 'failed'
                     }"
                     :style="'width: ' + (item.status === 'compressing' ? item.compressProgress : (item.status === 'uploading' ? item.uploadProgress : 100)) + '%'"></div>
              </div>

              <!-- Metadata Khusus Per-Berkas (Tampil Setelah Kompresi & Upload Selesai) -->
              <div class="mt-3 pt-3 border-t border-gray-100 bg-gray-50/50 -mx-3.5 -mb-3.5 p-3.5 rounded-b-xl" x-show="['done', 'saving', 'saved', 'failed'].includes(item.status)">
                <div class="grid sm:grid-cols-2 gap-3">
                  <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider">Judul Dokumen (Bisa Diubah) <span class="text-red-500">*</span></label>
                    <input type="text" x-model="item.title" class="mt-1 w-full rounded-md border border-gray-300 p-2 text-xs font-semibold text-gray-800 focus:border-maroon focus:ring-maroon" required :disabled="isSubmitting">
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider">Kategori Khusus Berkas Ini</label>
                    <select x-model="item.category" class="mt-1 w-full rounded-md border border-gray-300 p-2 text-xs focus:border-maroon focus:ring-maroon" :disabled="isSubmitting">
                      <option value="">-- Ikuti Kategori Utama (Default) --</option>
                      <option value="Surat Keputusan">Surat Keputusan (SK)</option>
                      <option value="Laporan">Laporan Kegiatan / Kinerja</option>
                      <option value="Formulir">Formulir / Template</option>
                      <option value="Surat Masuk/Keluar">Surat Masuk / Surat Keluar</option>
                      <option value="Dokumen Teknis">Dokumen Teknis / KAK / Kerangka Acuan</option>
                      <option value="Privasi">Dokumen Rahasia / Personel</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-[11px] font-bold text-gray-600 uppercase tracking-wider flex items-center justify-between">
                      <span>Tambah Tag (Enter)</span>
                      <span class="text-maroon">Ekstraksi Otomatis</span>
                    </label>
                    <input type="text" x-model="item.tagInput" @keydown.enter.prevent="addItemTag(item)" @keydown.comma.prevent="addItemTag(item)" list="existing-tags" placeholder="Ketik tag..." class="mt-1 w-full rounded-md border border-gray-300 p-2 text-xs focus:border-maroon focus:ring-maroon" :disabled="isSubmitting">
                  </div>
                  <div class="sm:col-span-2 flex flex-wrap gap-1">
                     <template x-for="(t, tIdx) in item.tags" :key="tIdx">
                       <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold bg-white text-maroon border border-maroon/20 shadow-xs">
                         <span x-text="'#' + t"></span>
                         <button type="button" @click="item.tags.splice(tIdx, 1)" class="text-red-500 hover:text-red-800" :disabled="isSubmitting">&times;</button>
                       </span>
                     </template>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- BAGIAN 2: METADATA GLOBAL (BERLAKU UNTUK SEMUA BERKAS)     -->
      <!-- ======================================================== -->
      <div class="space-y-4 pt-3 border-t">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>2. Metadata Global (Berlaku untuk Semua Dokumen)</span>
        </h2>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700">Kategori Utama (Default) <span class="text-red-500">*</span></label>
            <select x-model="form.category" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
              <option value="">-- Pilih Kategori Utama --</option>
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

          <div>
            <label class="block text-sm font-semibold text-gray-700">Nomor Surat / Naskah Dinas</label>
            <input type="text" x-model="form.number" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon font-mono" placeholder="Contoh: 000.1.2/15/BRIDA/I/2026">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Tanggal Penetapan / Surat</label>
            <input type="date" x-model="form.doc_date" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
          </div>

          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700">Pihak Terkait / Instansi Pengirim / Mitra</label>
            <input type="text" x-model="form.stakeholder" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon" placeholder="Contoh: Bappeda Kota Makassar, Universitas Hasanuddin">
          </div>
          
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700">Ringkasan Isi / Pokok Bahasan</label>
            <textarea x-model="form.description" rows="3" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon" placeholder="Tuliskan ringkasan inti naskah dinas untuk seluruh file..."></textarea>
          </div>
        </div>
      </div>

      <!-- ======================================================== -->
      <!-- BAGIAN 3: KEAMANAN AKSES & LOKASI FISIK                  -->
      <!-- ======================================================== -->
      <div class="space-y-4 pt-3 border-t">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>3. Keamanan Akses &amp; Lokasi Fisik Arsip</span>
        </h2>

        <div>
          <label class="block text-sm font-semibold text-gray-700">
            Tingkat Kerahasiaan Dokumen (Sensitivitas) <span class="text-red-500">*</span>
          </label>

          @if($folder)
            <div class="mt-2 p-3.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
              <div class="flex items-center gap-2.5">
                <span class="text-lg">
                  {{ $folder->visibility === 'public' ? '🌐' : ($folder->visibility === 'internal' ? '🏢' : '🔒') }}
                </span>
                <div>
                  <p class="text-xs font-bold text-gray-800">
                    Otomatis mengikuti folder "{{ $folder->name }}"
                  </p>
                  <p class="text-[11px] text-gray-500">
                    Semua dokumen akan tersimpan dengan status 
                    <strong class="uppercase font-mono text-gray-700">{{ $folder->visibility }}</strong>.
                  </p>
                </div>
              </div>
              <span class="px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase
                {{ $folder->visibility === 'public' ? 'bg-emerald-100 text-emerald-800' : 
                  ($folder->visibility === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                {{ $folder->visibility === 'public' ? 'Publik' : ($folder->visibility === 'internal' ? 'Internal' : 'Privat') }}
              </span>
            </div>
          @else
            <div class="grid sm:grid-cols-3 gap-3 mt-1.5">
              <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 has-[:checked]:shadow-xs">
                <div>
                  <div class="flex items-center justify-between">
                    <span class="text-base">🏢</span>
                    <input type="radio" name="sensitivity" value="internal" x-model="form.sensitivity" class="text-blue-600 focus:ring-blue-600">
                  </div>
                  <p class="text-xs font-bold text-gray-900 mt-2">Internal BRIDA</p>
                  <p class="text-[11px] text-gray-500 mt-1 leading-snug">Hanya dapat dilihat oleh seluruh pegawai yang login.</p>
                </div>
              </label>

              <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 has-[:checked]:shadow-xs">
                <div>
                  <div class="flex items-center justify-between">
                    <span class="text-base">🌐</span>
                    <input type="radio" name="sensitivity" value="public" x-model="form.sensitivity" class="text-emerald-600 focus:ring-emerald-600">
                  </div>
                  <p class="text-xs font-bold text-gray-900 mt-2">Publik Terbuka</p>
                  <p class="text-[11px] text-gray-500 mt-1 leading-snug">Dapat dicari dan diunduh oleh siapa saja di portal publik.</p>
                </div>
              </label>

              <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/50 has-[:checked]:shadow-xs">
                <div>
                  <div class="flex items-center justify-between">
                    <span class="text-base">🔒</span>
                    <input type="radio" name="sensitivity" value="private" x-model="form.sensitivity" class="text-red-600 focus:ring-red-600">
                  </div>
                  <p class="text-xs font-bold text-gray-900 mt-2">Privat / Terkunci</p>
                  <p class="text-[11px] text-gray-500 mt-1 leading-snug">Hanya akun Anda yang dapat membuka dan mengelola.</p>
                </div>
              </label>
            </div>
          @endif
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
          <span x-show="!isSubmitting">Simpan Semua Dokumen</span>
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
      category: '', // Kategori Default (Global)
      year: new Date().getFullYear(),
      stakeholder: '',
      description: '',
      sensitivity: '{{ $folder ? $folder->visibility : "internal" }}',
      physical_rack: '',
      physical_row: '',
      folder_id: '{{ $folderId ?? "" }}'
    },
    allTags: availableTags || [],
    uploadQueue: [],
    isSubmitting: false,
    compressionPercent: '50',

    get isAnyProcessing() {
      return this.uploadQueue.some(item => item.status === 'compressing' || item.status === 'uploading');
    },

    formatBytes(bytes) {
      if (!bytes || bytes === 0) return '0 B';
      const k = 1024;
      const sizes = ['B', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    },

    // Fungsi ekstraksi tag otomatis dari nama file
    generateTagsFromFilename(filename) {
      const stopWords = ['dan', 'atau', 'di', 'ke', 'dari', 'yang', 'untuk', 'pada', 'tentang', 'oleh', 'dengan', 'atas', 'nomor', 'no', 'tahun', 'thn', 'revisi', 'final', 'copy', 'salinan'];
      const cleanName = filename.replace(/\.[^/.]+$/, '').replace(/[_\-+]+/g, ' ').trim();
      const words = cleanName.split(/[\s,()]+/).map(w => w.trim().toUpperCase()).filter(w => w.length >= 3 && !stopWords.includes(w.toLowerCase()));
      return [...new Set(words)]; // Hanya tag unik
    },

    addItemTag(item) {
      if (!item.tagInput) return;
      const clean = item.tagInput.replace(/,/g, '').trim().toUpperCase();
      if (clean && !item.tags.includes(clean)) {
        item.tags.push(clean);
      }
      item.tagInput = '';
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

        // Judul otomatis bersih tanpa ekstensi
        const cleanTitle = file.name.replace(/\.[^/.]+$/, '').replace(/[_\-+]+/g, ' ').trim();
        const autoTags = this.generateTagsFromFilename(file.name);

        this.uploadQueue.push({
          file: file,
          name: file.name,
          title: cleanTitle,
          category: '', // Akan fallback ke Kategori Utama jika dibiarkan kosong
          tags: autoTags,
          tagInput: '',
          origSize: file.size,
          compressedSize: null,
          isPdf: isPdf,
          isImage: isImg,
          compressProgress: 10,
          uploadProgress: 0,
          compressMsg: 'Menyiapkan berkas...',
          status: 'compressing',
          tempPath: null // Path sementara setelah diunggah ke temp storage
        });

        const qIndex = this.uploadQueue.length - 1;
        let processedFile = file;

        try {
          if (isPdf) {
            processedFile = await this.compressPdfDirectWithTimeout(file, qIndex);
          } else if (isImg) {
            processedFile = await this.compressImageDirect(file, qIndex);
          }
          this.uploadQueue[qIndex].compressedSize = processedFile.size;
        } catch (err) {
          console.warn('Kompresi dilewati:', err);
          processedFile = file;
          this.uploadQueue[qIndex].compressedSize = file.size;
        }

        this.uploadQueue[qIndex].status = 'uploading';
        await this.uploadWithXHR(processedFile, qIndex);
      }
    },

    compressPdfDirectWithTimeout(file, qIndex) {
      return new Promise(async (resolve) => {
        const timeout = setTimeout(() => resolve(file), 15000);
        try {
          if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
            clearTimeout(timeout); return resolve(file);
          }
          let scale = this.compressionPercent === '30' ? 1.2 : (this.compressionPercent === '70' ? 0.75 : 1.0);
          let quality = this.compressionPercent === '30' ? 0.75 : (this.compressionPercent === '70' ? 0.35 : 0.55);

          this.uploadQueue[qIndex].compressMsg = 'Membaca PDF...';
          const fileBuffer = await file.arrayBuffer();
          const pdfDoc = await pdfjsLib.getDocument({ data: fileBuffer }).promise;
          const numPages = pdfDoc.numPages;

          if (numPages === 0) { clearTimeout(timeout); return resolve(file); }

          const newPdfDoc = await PDFLib.PDFDocument.create();
          for (let p = 1; p <= numPages; p++) {
            this.uploadQueue[qIndex].compressMsg = `Mengompresi hal ${p} dari ${numPages}...`;
            this.uploadQueue[qIndex].compressProgress = Math.round(((p - 1) / numPages) * 100);
            
            const page = await pdfDoc.getPage(p);
            const viewport = page.getViewport({ scale: scale });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            await page.render({ canvasContext: context, viewport: viewport }).promise;

            const imgBytes = this.dataURLtoUint8Array(canvas.toDataURL('image/jpeg', quality));
            const embeddedImg = await newPdfDoc.embedJpg(imgBytes);
            const newPage = newPdfDoc.addPage([viewport.width, viewport.height]);
            newPage.drawImage(embeddedImg, { x: 0, y: 0, width: viewport.width, height: viewport.height });
            canvas.width = 0; canvas.height = 0;
          }

          this.uploadQueue[qIndex].compressMsg = 'Menyusun berkas PDF...';
          this.uploadQueue[qIndex].compressProgress = 95;
          const compressedPdfBytes = await newPdfDoc.save();
          const finalBlob = new Blob([compressedPdfBytes], { type: 'application/pdf' });
          this.uploadQueue[qIndex].compressProgress = 100;
          clearTimeout(timeout);

          resolve(finalBlob.size < file.size ? new File([finalBlob], file.name, { type: 'application/pdf', lastModified: Date.now() }) : file);
        } catch (e) {
          clearTimeout(timeout); resolve(file);
        }
      });
    },

    compressImageDirect(file, qIndex) {
      return new Promise((resolve) => {
        this.uploadQueue[qIndex].compressMsg = 'Mengompres gambar...';
        this.uploadQueue[qIndex].compressProgress = 50;
        let maxDim = this.compressionPercent === '30' ? 1920 : (this.compressionPercent === '70' ? 1200 : 1600);
        let quality = this.compressionPercent === '30' ? 0.80 : (this.compressionPercent === '70' ? 0.45 : 0.65);

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
          const img = new Image();
          img.src = event.target.result;
          img.onload = () => {
            const canvas = document.createElement('canvas');
            let w = img.width, h = img.height;
            if (w > h && w > maxDim) { h = Math.round((h * maxDim) / w); w = maxDim; }
            else if (h > maxDim) { w = Math.round((w * maxDim) / h); h = maxDim; }

            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(img, 0, 0, w, h);
            canvas.toBlob((blob) => {
              this.uploadQueue[qIndex].compressProgress = 100;
              resolve((blob && blob.size < file.size) ? new File([blob], file.name, { type: 'image/jpeg', lastModified: Date.now() }) : file);
              canvas.width = 0; canvas.height = 0;
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
      while (n--) { u8arr[n] = bstr.charCodeAt(n); }
      return u8arr;
    },

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
                  this.uploadQueue[qIndex].tempPath = res.temp_path; // Simpan path sementara untuk antrean ini
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
        xhr.onerror = () => { this.uploadQueue[qIndex].status = 'failed'; resolve(); };
        xhr.open('POST', '{{ route("sigap-dokumen.temp-upload") }}', true);
        xhr.send(formData);
      });
    },

    removeFile(idx) {
      this.uploadQueue.splice(idx, 1);
    },

    // Pengiriman Final (Multiple AJAX Requests)
    async submitForm() {
      const readyFiles = this.uploadQueue.filter(i => i.status === 'done' && i.tempPath);
      
      if (readyFiles.length === 0) {
        Swal.fire('Lampiran Kosong', 'Harap lampirkan minimal satu berkas dokumen.', 'warning');
        return;
      }

      // Validasi: Cek apakah ada file yang tidak punya kategori DAN form global juga tidak punya kategori
      if (!this.form.category && readyFiles.some(i => !i.category)) {
        Swal.fire('Kategori Belum Dipilih', 'Pilih "Kategori Utama (Default)" pada Bagian 2, atau pastikan tiap berkas memiliki Kategorinya masing-masing.', 'warning');
        return;
      }

      this.isSubmitting = true;
      let successCount = 0;

      // Kirim satu per satu via AJAX ke backend Store method Anda. 
      // Backend akan menerima request layaknya form di-submit satu kali per file.
      for (let i = 0; i < this.uploadQueue.length; i++) {
        let item = this.uploadQueue[i];
        
        if (item.status === 'done' && item.tempPath) {
          item.status = 'saving'; // Ubah UI baris ini jadi "Menyimpan..."
          
          let formData = new FormData();
          formData.append('_token', '{{ csrf_token() }}');
          
          // Data File Individual
          formData.append('files[]', item.tempPath); // Backend expects array of temp paths (ini diisi 1 path)
          formData.append('title', item.title);
          formData.append('category', item.category || this.form.category);
          formData.append('tags', item.tags.join(','));

          // Data Global
          formData.append('number', this.form.number);
          formData.append('doc_date', this.form.doc_date);
          formData.append('year', this.form.year);
          formData.append('stakeholder', this.form.stakeholder);
          formData.append('description', this.form.description);
          formData.append('sensitivity', this.form.sensitivity);
          formData.append('physical_rack', this.form.physical_rack);
          formData.append('physical_row', this.form.physical_row);
          formData.append('folder_id', this.form.folder_id || '');

          try {
            const res = await fetch('{{ route("sigap-dokumen.store") }}', {
              method: 'POST',
              body: formData,
              headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (res.ok) {
              item.status = 'saved';
              successCount++;
            } else {
              item.status = 'failed';
            }
          } catch (e) {
            item.status = 'failed';
          }
        }
      }

      // Setelah seluruh form tersubmit (berhasil atau sebagian gagal)
      if (successCount === readyFiles.length) {
         window.location.href = "{{ $folder ? route('sigap-dokumen.folder.show', $folder) : route('sigap-dokumen.saya') }}";
      } else if (successCount > 0) {
         Swal.fire('Selesai Sebagian', 'Beberapa dokumen berhasil disimpan, namun ada yang gagal.', 'warning').then(() => {
           window.location.href = "{{ $folder ? route('sigap-dokumen.folder.show', $folder) : route('sigap-dokumen.saya') }}";
         });
      } else {
         Swal.fire('Gagal Menyimpan', 'Tidak ada dokumen yang berhasil tersimpan ke sistem.', 'error');
         this.isSubmitting = false;
      }
    }
  };
}
</script>
@endsection