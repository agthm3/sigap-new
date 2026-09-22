@extends('layouts.app')

@push('head')
  <!-- PDF & Image Client Processing Libraries (Stabil & Terverifikasi) -->
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
      <!-- BAGIAN 1: LAMPIRAN BERKAS DIGITAL & KOMPRESI (PALING ATAS) -->
      <!-- ======================================================== -->
      <div class="space-y-4">
        <div class="border-b pb-2 flex flex-col sm:flex-row sm:items-center justify-between gap-1">
          <div class="flex items-center gap-2">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">1. Lampiran Berkas Digital</h2>
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
            <strong>Smart Intake &amp; Privacy First:</strong> Nama file pertama akan otomatis disetel menjadi judul dan diekstrak menjadi tag pencarian. Kompresi diproses aman di RAM browser.
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
          <p class="text-xs text-gray-500 mt-1">Pilih berkas PDF atau Foto naskah dinas. Sistem akan langsung memprosesnya.</p>
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

              <!-- Progress Bar Track -->
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

      <!-- ======================================================== -->
      <!-- BAGIAN 2: IDENTITAS & LEGALITAS DOKUMEN                    -->
      <!-- ======================================================== -->
      <div class="space-y-4 pt-3 border-t">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>2. Identitas &amp; Legalitas Dokumen</span>
        </h2>

        <div class="grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <div class="flex items-center justify-between">
              <label class="block text-sm font-semibold text-gray-700">
                Judul / Perihal Dokumen <span class="text-red-500">*</span>
              </label>
              <span class="text-[11px] text-gray-400">Otomatis terisi dari nama berkas</span>
            </div>
            <input type="text" 
                   x-model="form.title" 
                   required 
                   class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon font-medium" 
                   placeholder="Contoh: DOKUMEN STB 2026">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Nomor Surat / Naskah Dinas</label>
            <input type="text" x-model="form.number" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon font-mono" placeholder="Contoh: 000.1.2/15/BRIDA/I/2026">
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700">Tanggal Penetapan / Surat</label>
            <input type="date" x-model="form.doc_date" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
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

      <!-- ======================================================== -->
      <!-- BAGIAN 3: RINGKASAN ISI & TAG PENCARIAN (AUTO-EXTRACT)   -->
      <!-- ======================================================== -->
      <div class="space-y-4 pt-3 border-t">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>3. Konteks Pencarian &amp; Metadata Tag</span>
        </h2>

        <div>
          <label class="block text-sm font-semibold text-gray-700">Ringkasan Isi / Catatan Pokok Dokumen</label>
          <textarea x-model="form.description" rows="3" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon" placeholder="Tuliskan ringkasan inti pokok bahasan naskah dinas..."></textarea>
        </div>

        <div>
          <div class="flex items-center justify-between">
            <label class="block text-sm font-semibold text-gray-700">Label / Tag Pencarian (Tekan Enter atau Koma)</label>
            <span class="text-[11px] text-gray-400">Otomatis diekstrak dari judul</span>
          </div>
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

      <!-- ======================================================== -->
      <!-- BAGIAN 4: KEAMANAN AKSES (3-LEVEL) & LOKASI FISIK       -->
      <!-- ======================================================== -->
      <!-- ======================================================== -->
      <!-- BAGIAN 4: KEAMANAN AKSES & LOKASI FISIK                  -->
      <!-- ======================================================== -->
      <div class="space-y-4 pt-3 border-t">
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 flex items-center gap-2">
          <span>4. Keamanan Akses &amp; Lokasi Fisik Arsip</span>
        </h2>

        <div>
          <label class="block text-sm font-semibold text-gray-700">
            Tingkat Kerahasiaan Dokumen (Sensitivitas) <span class="text-red-500">*</span>
          </label>

          @if($folder)
            <!-- JIKA DI DALAM FOLDER: Terkunci otomatis mengikuti folder induk -->
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
                    Dokumen ini akan tersimpan dengan status 
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
            <!-- JIKA DOKUMEN LEPAS (DI LUAR FOLDER): Pengguna bebas memilih -->
            <div class="grid sm:grid-cols-3 gap-3 mt-1.5">
              <!-- 1. Internal BRIDA -->
              <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50 has-[:checked]:shadow-xs">
                <div>
                  <div class="flex items-center justify-between">
                    <span class="text-base">🏢</span>
                    <input type="radio" name="sensitivity" value="internal" x-model="form.sensitivity" class="text-blue-600 focus:ring-blue-600">
                  </div>
                  <p class="text-xs font-bold text-gray-900 mt-2">Internal BRIDA</p>
                  <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                    Hanya dapat dilihat oleh seluruh pegawai yang login di dashboard.
                  </p>
                </div>
                <span class="mt-3 text-[10px] bg-blue-100 text-blue-700 font-extrabold px-1.5 py-0.5 rounded self-start">
                  Aman &bull; Kantor
                </span>
              </label>

              <!-- 2. Publik Terbuka -->
              <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50 has-[:checked]:shadow-xs">
                <div>
                  <div class="flex items-center justify-between">
                    <span class="text-base">🌐</span>
                    <input type="radio" name="sensitivity" value="public" x-model="form.sensitivity" class="text-emerald-600 focus:ring-emerald-600">
                  </div>
                  <p class="text-xs font-bold text-gray-900 mt-2">Publik Terbuka</p>
                  <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                    Dapat dicari dan diunduh oleh siapa saja di portal publik terbuka.
                  </p>
                </div>
                <span class="mt-3 text-[10px] bg-emerald-100 text-emerald-700 font-extrabold px-1.5 py-0.5 rounded self-start">
                  Regulasi &bull; SOP
                </span>
              </label>

              <!-- 3. Privat / Terkunci -->
              <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/50 has-[:checked]:shadow-xs">
                <div>
                  <div class="flex items-center justify-between">
                    <span class="text-base">🔒</span>
                    <input type="radio" name="sensitivity" value="private" x-model="form.sensitivity" class="text-red-600 focus:ring-red-600">
                  </div>
                  <p class="text-xs font-bold text-gray-900 mt-2">Privat / Terkunci</p>
                  <p class="text-[11px] text-gray-500 mt-1 leading-snug">
                    Hanya akun Anda yang dapat membuka (bisa di-share via link + sandi).
                  </p>
                </div>
                <span class="mt-3 text-[10px] bg-red-100 text-red-700 font-extrabold px-1.5 py-0.5 rounded self-start">
                  Keuangan &bull; Rahasia
                </span>
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
      // Otomatis mengunci visibilitas folder jika di dalam folder, default 'internal' jika berkas lepas
      sensitivity: '{{ $folder ? $folder->visibility : "internal" }}',
      tags: [],
      physical_rack: '',
      physical_row: '',
      folder_id: '{{ $folderId ?? "" }}',
      files: []
    },
    tagInput: '',
    allTags: availableTags || [],
    uploadQueue: [],
    isSubmitting: false,
    compressionPercent: '50',

    // Inisialisasi Watcher Alpine
    init() {
      // Pantau input judul untuk ekstrak tag otomatis
      this.$watch('form.title', (newVal) => {
        this.generateTagsFromTitle(newVal);
      });
    },

    // Ekstraksi kata bermakna dari judul menjadi tag
    generateTagsFromTitle(title) {
      if (!title || !title.trim()) return;

      const stopWords = [
        'dan', 'atau', 'di', 'ke', 'dari', 'yang', 'untuk', 'pada', 
        'tentang', 'oleh', 'dengan', 'atas', 'nomor', 'no', 'tahun', 'thn'
      ];

      const words = title
        .split(/[\s,./\-_()]+/)
        .map(w => w.trim().toUpperCase())
        .filter(w => w.length >= 2 && !stopWords.includes(w.toLowerCase()));

      words.forEach(word => {
        if (word && !this.form.tags.includes(word)) {
          this.form.tags.push(word);
        }
      });
    },

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
      const clean = tag.replace(/,/g, '').trim().toUpperCase();
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

      // Tarik nama berkas pertama menjadi judul jika input judul masih kosong
      const firstFile = files[0];
      if (!this.form.title || !this.form.title.trim()) {
        const cleanName = firstFile.name.replace(/\.[^/.]+$/, '').replace(/[_\-+]+/g, ' ').trim();
        this.form.title = cleanName; // Memicu $watch('form.title') untuk generate tag otomatis
      }

      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const isPdf = file.type === 'application/pdf' || file.name.toLowerCase().endsWith('.pdf');
        const isImg = file.type.startsWith('image/');

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

        const qIndex = this.uploadQueue.length - 1;
        let processedFile = file;

        try {
          if (isPdf) {
            processedFile = await this.compressPdfDirectWithTimeout(file, qIndex);
          } else if (isImg) {
            processedFile = await this.compressImageDirect(file, qIndex);
          }

          this.uploadQueue[qIndex].compressedSize = processedFile.size;
          this.uploadQueue[qIndex].blob = processedFile;

          if (this.uploadQueue[qIndex].origSize > processedFile.size) {
            this.uploadQueue[qIndex].savingsPercent = Math.round(
              ((this.uploadQueue[qIndex].origSize - processedFile.size) / this.uploadQueue[qIndex].origSize) * 100
            );
          }
        } catch (err) {
          console.warn('Kompresi dilewati (menggunakan berkas asli):', err);
          processedFile = file;
          this.uploadQueue[qIndex].compressedSize = file.size;
          this.uploadQueue[qIndex].blob = file;
        }

        this.uploadQueue[qIndex].status = 'uploading';
        await this.uploadWithXHR(processedFile, qIndex);
      }
    },

    // Kompresi PDF mandiri dengan proteksi timeout (maksimal 15 detik)
    compressPdfDirectWithTimeout(file, qIndex) {
      return new Promise(async (resolve) => {
        const timeout = setTimeout(() => {
          console.warn('Kompresi PDF melebihi batas 15 detik, memproses file asli.');
          resolve(file);
        }, 15000);

        try {
          if (typeof pdfjsLib === 'undefined' || typeof PDFLib === 'undefined') {
            clearTimeout(timeout);
            return resolve(file);
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

          this.uploadQueue[qIndex].compressMsg = 'Membaca PDF...';
          const fileBuffer = await file.arrayBuffer();
          const loadingTask = pdfjsLib.getDocument({ data: fileBuffer });
          const pdfDoc = await loadingTask.promise;
          const numPages = pdfDoc.numPages;

          if (numPages === 0) {
            clearTimeout(timeout);
            return resolve(file);
          }

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

            canvas.width = 0;
            canvas.height = 0;
          }

          this.uploadQueue[qIndex].compressMsg = 'Menyusun berkas PDF...';
          this.uploadQueue[qIndex].compressProgress = 95;

          const compressedPdfBytes = await newPdfDoc.save();
          const finalBlob = new Blob([compressedPdfBytes], { type: 'application/pdf' });

          this.uploadQueue[qIndex].compressProgress = 100;
          clearTimeout(timeout);

          if (finalBlob.size < file.size) {
            resolve(new File([finalBlob], file.name, { type: 'application/pdf', lastModified: Date.now() }));
          } else {
            resolve(file);
          }
        } catch (e) {
          clearTimeout(timeout);
          console.warn('Kompresi PDF dilewati:', e);
          resolve(file);
        }
      });
    },

    // Kompresi gambar via canvas HTML5
    compressImageDirect(file, qIndex) {
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

    // Upload asinkron via XMLHttpRequest untuk progress bar akurat
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

        xhr.open('POST', '{{ route("sigap-dokumen.temp-upload") }}', true);
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

    // Pengiriman final form payload
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
      hiddenForm.action = '{{ route("sigap-dokumen.store") }}';

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
      appendInput('folder_id', this.form.folder_id || '');

      this.form.files.forEach((f) => {
        appendInput('files[]', f);
      });

      document.body.appendChild(hiddenForm);
      hiddenForm.submit();
    }
  };
}
</script>
@endsection