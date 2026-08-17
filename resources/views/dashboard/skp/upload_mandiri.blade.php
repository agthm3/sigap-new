<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
  <title>Upload Mandiri — SIGAP BRIDA</title>

  <!-- Tailwind CSS & Alpine.js -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50:'#fdf7f7',100:'#faeeee',200:'#f0d1d1',300:'#e2a8a8',
              400:'#c86f6f',500:'#a64040',600:'#8f2f2f',700:'#7a2222',
              800:'#661b1b',900:'#4a1313', DEFAULT:'#7a2222'
            }
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

  <style>
    body { font-family: Inter, system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    .scrollbar-thin::-webkit-scrollbar { width: 4px; height: 4px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
  </style>

  <!-- PWA Meta Tags -->
  <link rel="manifest" href="https://sigap.brida.makassarkota.go.id/manifest.json?v=3">
  <meta name="theme-color" content="#7a2222">
  <link rel="apple-touch-icon" sizes="192x192" href="https://sigap.brida.makassarkota.go.id/images/icon-192.png">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col justify-between">

  <div x-data="kameraMandiri(@js($myAgendas), {{ auth()->id() }})" class="w-full max-w-lg mx-auto flex-1 flex flex-col justify-between p-4 space-y-4">

    {{-- TOP BAR --}}
    <div class="flex items-center justify-between py-2 border-b border-gray-800">
      <div class="flex items-center gap-2">
        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-maroon text-white font-extrabold text-xs">SB</span>
        <div>
          <h1 class="text-sm font-bold text-white leading-tight">Upload Mandiri SKP</h1>
          <p class="text-[10px] text-gray-400">SIGAP BRIDA Kota Makassar</p>
        </div>
      </div>
      <a href="{{ route('sigap-skp.pribadi') }}" class="px-3 py-1.5 rounded-lg bg-gray-800 text-gray-300 text-xs font-semibold hover:bg-gray-700">
        ✕ Tutup
      </a>
    </div>

    {{-- AREA KAMERA & FOTO UTAMA --}}
    <div class="space-y-3 flex-1 flex flex-col justify-center">

      {{-- Jendela Kamera Web / Pratinjau Terakhir --}}
      <div class="relative w-full aspect-[4/3] bg-black rounded-3xl overflow-hidden shadow-2xl border-2 border-gray-800 flex items-center justify-center">
        
        <!-- Video Stream Kamera Web -->
        <video x-ref="video" x-show="isCameraActive" autoplay playsinline class="w-full h-full object-cover"></video>
        
        <!-- Pratinjau Foto Preview Terakhir / Standby -->
        <div x-show="!isCameraActive" class="w-full h-full flex items-center justify-center bg-gray-950">
          <template x-if="capturedPhotos.length > 0">
            <img :src="capturedPhotos[activePreviewIndex !== null ? activePreviewIndex : (capturedPhotos.length - 1)]" 
                 class="w-full h-full object-cover">
          </template>
          <template x-if="capturedPhotos.length === 0">
            <div class="text-center p-4">
              <span class="text-3xl">📷</span>
              <p class="text-xs text-gray-400 mt-1">Gunakan tombol kamera browser, kamera bawaan HP, atau galeri.</p>
            </div>
          </template>
        </div>

        <!-- Indikator Proses Kompresi Browser -->
        <div x-show="isProcessingPhoto" class="absolute inset-0 bg-black/70 flex flex-col items-center justify-center z-20" style="display: none;">
          <svg class="w-8 h-8 text-amber-400 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"/></svg>
          <p class="text-xs text-white font-semibold mt-2">Mengompresi Foto...</p>
        </div>

        <!-- TOMBOL OVERLAY KAMERA -->
        <div class="absolute bottom-4 inset-x-0 flex justify-center items-center gap-3 z-10">
          
          <!-- Tombol Switch Kamera Depan/Belakang -->
          <template x-if="isCameraActive">
            <button type="button" @click="switchCamera()" class="p-3 rounded-full bg-black/60 hover:bg-black/80 text-white backdrop-blur-md border border-white/20 transition-all active:scale-90" title="Ganti Kamera">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </template>

          <!-- Tombol Jepret Foto Web -->
          <template x-if="isCameraActive">
            <button type="button" @click="takeSnapshot()" :disabled="isProcessingPhoto" class="w-16 h-16 rounded-full border-4 border-white bg-red-600 hover:bg-red-700 shadow-2xl flex items-center justify-center transition-transform active:scale-90 disabled:opacity-50">
              <div class="w-12 h-12 rounded-full border-2 border-white"></div>
            </button>
          </template>

          <!-- Tombol Buka Kembali Kamera Web Jika Dimatikan -->
          <template x-if="!isCameraActive">
            <button type="button" @click="startCamera()" class="px-4 py-2.5 rounded-full bg-maroon hover:bg-maroon-800 text-white text-xs font-bold shadow-xl flex items-center gap-1.5 transition-transform active:scale-95">
              📷 Kamera Web
            </button>
          </template>

          <!-- Tombol Putar Foto Terpilih -->
          <template x-if="capturedPhotos.length > 0">
            <button type="button" @click="rotateActivePhoto()" class="p-3 rounded-full bg-black/60 hover:bg-black/80 text-white backdrop-blur-md border border-white/20 transition-all active:scale-90" title="Putar Foto 90°">
              🔄
            </button>
          </template>

        </div>
      </div>

      <!-- Live Thumbnail Bar Foto Terpilih (Multiple) -->
      <div x-show="capturedPhotos.length > 0" class="space-y-1.5">
        <div class="flex items-center justify-between text-[11px] px-1 text-gray-400">
          <span>Foto Terpilih (<strong class="text-white" x-text="capturedPhotos.length"></strong> foto):</span>
          <button type="button" @click="clearAllPhotos()" class="text-red-400 hover:underline">Hapus Semua</button>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto p-1.5 bg-gray-950/60 rounded-2xl border border-gray-800 scrollbar-thin">
          <template x-for="(photo, index) in capturedPhotos" :key="index">
            <div class="relative shrink-0 w-16 h-16 rounded-xl overflow-hidden border-2 transition-all cursor-pointer group"
                 :class="activePreviewIndex === index ? 'border-amber-400 ring-2 ring-amber-400/30' : 'border-gray-700'"
                 @click="activePreviewIndex = index; isCameraActive = false">
              <img :src="photo" class="w-full h-full object-cover">
              
              <button type="button" @click.stop="removePhoto(index)" class="absolute top-0.5 right-0.5 w-4 h-4 bg-red-600/90 text-white text-[10px] rounded-full flex items-center justify-center shadow hover:bg-red-700">
                ✕
              </button>
            </div>
          </template>
        </div>
      </div>

      <!-- TOMBOL SUMBER FOTO EKSTERNAL: KAMERA NATIVE & GALERI -->
      <div class="grid grid-cols-2 gap-2 pt-1">
        
        <!-- Tombol 1: Kamera Bawaan HP (Native Camera) -->
        <label class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 border border-gray-700 text-xs text-amber-400 cursor-pointer font-bold shadow transition-colors text-center">
          <span>📸 Kamera Bawaan HP</span>
          <input type="file" accept="image/*" capture="environment" @change="handleNativeCameraFile($event)" class="hidden">
        </label>

        <!-- Tombol 2: Galeri HP / File (Multiple Selection) -->
        <label class="flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-gray-800 hover:bg-gray-700 border border-gray-700 text-xs text-emerald-400 cursor-pointer font-bold shadow transition-colors text-center">
          <span>📁 Galeri (Banyak Foto)</span>
          <input type="file" accept="image/*" multiple @change="handleMultipleGalleryFiles($event)" class="hidden">
        </label>

      </div>

    </div>

    {{-- FORM ISIAN DATA --}}
    <form class="bg-gray-800 rounded-3xl p-4 space-y-3 border border-gray-700 shadow-xl">
      
      <!-- Pilihan Tab Jenis Pelaporan -->
      <div class="grid grid-cols-2 gap-2 bg-gray-900/60 p-1 rounded-2xl border border-gray-700">
        <button type="button" @click="sourceMode = 'agenda'; selectAgendaItem()"
                class="py-2 rounded-xl text-xs font-bold transition-all"
                :class="sourceMode === 'agenda' ? 'bg-maroon text-white shadow' : 'text-gray-400 hover:text-white'">
          📌 Penugasan Agenda
        </button>
        <button type="button" @click="sourceMode = 'manual'; switchToManual()"
                class="py-2 rounded-xl text-xs font-bold transition-all"
                :class="sourceMode === 'manual' ? 'bg-maroon text-white shadow' : 'text-gray-400 hover:text-white'">
          ✍️ Lapor Mandiri
        </button>
      </div>

      <!-- Select Penugasan Agenda -->
      <div x-show="sourceMode === 'agenda'" class="space-y-1">
        <label class="block text-[11px] font-bold text-gray-300 uppercase">Pilih Penugasan Saya</label>
        <select x-model="selectedAgendaId" @change="onAgendaChange" class="w-full rounded-xl text-xs bg-gray-900 border-gray-700 text-white p-2.5 focus:ring-maroon focus:border-maroon">
          <option value="">-- Pilih Penugasan Saya --</option>
          <template x-for="(agenda, index) in myAgendas" :key="index">
            <option :value="agenda.id" x-text="'[' + agenda.date + '] ' + agenda.unit_title"></option>
          </template>
        </select>
      </div>

      <!-- Fields Detail Kegiatan -->
      <div class="space-y-2.5">
        <div>
          <label class="block text-[11px] font-bold text-gray-300">Nama Kegiatan <span class="text-red-400">*</span></label>
          <input type="text" x-model="judulKegiatan" required :readonly="sourceMode === 'agenda'"
                 placeholder="Tulis nama kegiatan..."
                 class="w-full rounded-xl text-xs bg-gray-900 border-gray-700 text-white p-2.5" :class="sourceMode === 'agenda' ? 'opacity-70 cursor-not-allowed' : ''">
        </div>

        {{-- INPUT CENTANG REKAN SE-TIM --}}
        <div x-show="sourceMode === 'agenda' && anggotaLain.length > 0" class="space-y-1.5 p-3 rounded-2xl bg-gray-900/90 border border-amber-500/30">
          <div class="flex items-center justify-between">
            <label class="text-[11px] font-bold text-amber-400">👥 Rekan Se-tim yang Hadir / Ikut Foto:</label>
            <span class="text-[10px] text-gray-400 font-normal" x-text="selectedPegawaiIds.length + ' Dipilih'"></span>
          </div>

          <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1 scrollbar-thin mt-1">
            <template x-for="pegawai in anggotaLain" :key="pegawai.id">
              <label class="flex items-center justify-between p-2 rounded-xl bg-gray-800 hover:bg-gray-700/80 border border-gray-700 cursor-pointer transition-colors">
                <span class="text-xs text-gray-200 font-semibold" x-text="pegawai.name"></span>
                <input type="checkbox" :value="pegawai.id" x-model="selectedPegawaiIds" class="rounded text-maroon focus:ring-maroon w-4 h-4 bg-gray-900 border-gray-600">
              </label>
            </template>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-[11px] font-bold text-gray-300">Tanggal Kegiatan <span class="text-red-400">*</span></label>
            <input type="date" x-model="tanggalKegiatan" required :readonly="sourceMode === 'agenda'"
                   class="w-full rounded-xl text-xs bg-gray-900 border-gray-700 text-white p-2.5" :class="sourceMode === 'agenda' ? 'opacity-70 cursor-not-allowed' : 'cursor-pointer'">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-gray-300">Lokasi</label>
            <input type="text" x-model="lokasiKegiatan" placeholder="Contoh: Kantor BRIDA"
                   class="w-full rounded-xl text-xs bg-gray-900 border-gray-700 text-white p-2.5">
          </div>
        </div>

        <div x-show="sourceMode === 'manual'">
          <label class="block text-[11px] font-bold text-gray-300">Deskripsi Singkat</label>
          <textarea x-model="deskripsiKegiatan" rows="2" placeholder="Ringkasan singkat kegiatan..."
                    class="w-full rounded-xl text-xs bg-gray-900 border-gray-700 text-white p-2.5"></textarea>
        </div>
      </div>

      <!-- DUA TOMBOL AKSI SIMPAN -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
        
        <!-- Tombol 1: Simpan Saja -->
        <button type="button" @click="submitForm(false)" :disabled="isLoading || capturedPhotos.length === 0 || isProcessingPhoto"
                class="w-full py-3 bg-gray-700 hover:bg-gray-600 text-white font-bold text-xs rounded-xl disabled:opacity-40 transition-all shadow-md flex items-center justify-center gap-1.5 border border-gray-600">
          <span x-show="!isLoading">💾 Simpan Saja</span>
          <span x-show="isLoading && submitMode === 'save_only'" class="animate-pulse">Menyimpan...</span>
        </button>

        <!-- Tombol 2: Simpan & Bagikan ke WhatsApp -->
        <button type="button" @click="submitForm(true)" :disabled="isLoading || capturedPhotos.length === 0 || isProcessingPhoto"
                class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl disabled:opacity-40 transition-all shadow-md flex items-center justify-center gap-1.5">
          <span x-show="!isLoading">🚀 Simpan & Kirim WA</span>
          <span x-show="isLoading && submitMode === 'save_and_share'" class="animate-pulse">Menyimpan...</span>
        </button>

      </div>

    </form>
  </div>

  <script>
  function kameraMandiri(myAgendasList, currentUserId) {
    return {
      stream: null,
      isCameraActive: true,
      isProcessingPhoto: false,
      capturedPhotos: [],
      activePreviewIndex: null,
      sourceMode: 'agenda',
      myAgendas: myAgendasList || [],
      selectedAgendaId: '',
      judulKegiatan: '',
      tanggalKegiatan: new Date().toISOString().split('T')[0],
      lokasiKegiatan: '',
      deskripsiKegiatan: '',
      facingMode: 'user',
      isLoading: false,
      submitMode: 'save_and_share',

      anggotaLain: [],        
      selectedPegawaiIds: [], 
      currentUserId: currentUserId,

      init() {
        this.startCamera();
        if(this.myAgendas.length > 0) {
          this.selectedAgendaId = this.myAgendas[0].id;
          this.onAgendaChange();
        } else {
          this.sourceMode = 'manual';
          this.switchToManual();
        }
      },

      // ================== FUNGSI KAMERA BROWSER ==================
      async startCamera() {
        this.stopCamera();
        this.isCameraActive = true;
        try {
          this.stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
              facingMode: this.facingMode,
              width: { ideal: 1280 }, 
              height: { ideal: 720 } 
            } 
          });
          if (this.$refs.video) {
            this.$refs.video.srcObject = this.stream;
          }
        } catch (err) {
          try {
            this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
            if (this.$refs.video) {
              this.$refs.video.srcObject = this.stream;
            }
          } catch(e) {
            console.error("Kamera browser tidak aktif:", e);
            this.isCameraActive = false;
          }
        }
      },

      switchCamera() {
        this.facingMode = this.facingMode === 'environment' ? 'user' : 'environment';
        this.startCamera();
      },

      stopCamera() {
        if (this.stream) {
          this.stream.getTracks().forEach(track => track.stop());
          this.stream = null;
        }
      },

      takeSnapshot() {
        const video = this.$refs.video;
        if (!video) return;

        this.isProcessingPhoto = true;

        const canvas = document.createElement('canvas');
        let width = video.videoWidth || 1280;
        let height = video.videoHeight || 720;

        if (width > 1200) {
          height = Math.round((height * 1200) / width);
          width = 1200;
        }

        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');

        if (this.facingMode === 'user') {
          ctx.translate(canvas.width, 0);
          ctx.scale(-1, 1);
        }

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const compressedBase64 = canvas.toDataURL('image/jpeg', 0.80);
        this.capturedPhotos.push(compressedBase64);
        this.activePreviewIndex = this.capturedPhotos.length - 1;

        if (navigator.vibrate) navigator.vibrate(50);
        this.isProcessingPhoto = false;
      },

      removePhoto(index) {
        this.capturedPhotos.splice(index, 1);
        if (this.capturedPhotos.length > 0) {
          this.activePreviewIndex = this.capturedPhotos.length - 1;
        } else {
          this.activePreviewIndex = null;
          if (!this.isCameraActive) this.startCamera();
        }
      },

      clearAllPhotos() {
        this.capturedPhotos = [];
        this.activePreviewIndex = null;
        this.startCamera();
      },

      rotateActivePhoto() {
        const targetIdx = this.activePreviewIndex !== null ? this.activePreviewIndex : (this.capturedPhotos.length - 1);
        if (targetIdx < 0 || !this.capturedPhotos[targetIdx]) return;

        this.isProcessingPhoto = true;
        const img = new Image();
        img.src = this.capturedPhotos[targetIdx];
        img.onload = () => {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          canvas.width = img.height;
          canvas.height = img.width;

          ctx.translate(canvas.width / 2, canvas.height / 2);
          ctx.rotate((90 * Math.PI) / 180);
          ctx.drawImage(img, -img.width / 2, -img.height / 2);

          this.capturedPhotos[targetIdx] = canvas.toDataURL('image/jpeg', 0.80);
          this.isProcessingPhoto = false;
        };
      },

      // ================== KAMERA BAWAAN HP (NATIVE CAMERA) ==================
      async handleNativeCameraFile(e) {
        const file = e.target.files[0];
        if (!file) return;

        this.isProcessingPhoto = true;
        try {
          const compressedBase64 = await this.compressFileToDataUrl(file, 1200, 0.80);
          this.capturedPhotos.push(compressedBase64);
          this.activePreviewIndex = this.capturedPhotos.length - 1;
          this.isCameraActive = false; // Matikan kamera browser, tampilkan foto hasil jepret
        } catch(err) {
          console.error('Gagal memproses foto kamera HP:', err);
        } finally {
          this.isProcessingPhoto = false;
          e.target.value = '';
        }
      },

      // ================== MULTIPLE UPLOAD DARI GALERI ==================
      async handleMultipleGalleryFiles(e) {
        const files = Array.from(e.target.files);
        if (files.length === 0) return;

        this.isProcessingPhoto = true;

        for (const file of files) {
          if (!file.type.startsWith('image/')) continue;

          try {
            const compressedBase64 = await this.compressFileToDataUrl(file, 1200, 0.80);
            this.capturedPhotos.push(compressedBase64);
          } catch(err) {
            console.error('Gagal mengompresi foto galeri:', err);
          }
        }

        this.activePreviewIndex = this.capturedPhotos.length - 1;
        this.isCameraActive = false;
        this.isProcessingPhoto = false;
        e.target.value = '';
      },

      compressFileToDataUrl(file, maxWidth = 1200, quality = 0.80) {
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onload = (event) => {
            const img = new Image();
            img.src = event.target.result;
            img.onload = () => {
              const canvas = document.createElement('canvas');
              let width = img.width;
              let height = img.height;

              if (width > maxWidth) {
                height = Math.round((height * maxWidth) / width);
                width = maxWidth;
              }

              canvas.width = width;
              canvas.height = height;
              const ctx = canvas.getContext('2d');
              ctx.drawImage(img, 0, 0, width, height);

              resolve(canvas.toDataURL('image/jpeg', quality));
            };
            img.onerror = reject;
          };
          reader.onerror = reject;
          reader.readAsDataURL(file);
        });
      },

      // ================== MODE FORM & TANGGAL ==================
      switchToManual() {
        this.selectedAgendaId = '';
        this.judulKegiatan = '';
        this.tanggalKegiatan = new Date().toISOString().split('T')[0];
        this.lokasiKegiatan = '';
        this.deskripsiKegiatan = '';
        this.anggotaLain = [];
        this.selectedPegawaiIds = [];
      },

      onAgendaChange() {
        const selected = this.myAgendas.find(a => a.id == this.selectedAgendaId);
        if (selected) {
          this.judulKegiatan = selected.unit_title;
          this.tanggalKegiatan = selected.date;
          this.lokasiKegiatan = selected.place;
          this.deskripsiKegiatan = selected.description;

          if (selected.pegawais && Array.isArray(selected.pegawais)) {
            this.anggotaLain = selected.pegawais;
            this.selectedPegawaiIds = selected.pegawais.map(p => p.id);
          } else {
            this.anggotaLain = [];
            this.selectedPegawaiIds = [];
          }
        } else {
          this.anggotaLain = [];
          this.selectedPegawaiIds = [];
        }
      },

      selectAgendaItem() {
        if (this.myAgendas.length > 0 && !this.selectedAgendaId) {
          this.selectedAgendaId = this.myAgendas[0].id;
          this.onAgendaChange();
        }
      },

      // ================== SUBMIT FORM ==================
      async submitForm(shareToWa = true) {
        if (this.capturedPhotos.length === 0) {
          Swal.fire('Perhatian', 'Harap ambil foto lewat kamera atau pilih minimal 1 foto dari galeri.', 'warning');
          return;
        }

        if (!this.judulKegiatan || this.judulKegiatan.trim() === '') {
          Swal.fire('Perhatian', 'Nama kegiatan wajib diisi.', 'warning');
          return;
        }

        this.submitMode = shareToWa ? 'save_and_share' : 'save_only';
        this.isLoading = true;

        try {
          const response = await fetch("{{ route('sigap-skp.store-mandiri') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json",
              "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
              source_mode: this.sourceMode,
              agenda_id: this.selectedAgendaId,
              judul_kegiatan: this.judulKegiatan,
              tanggal: this.tanggalKegiatan,
              lokasi: this.lokasiKegiatan,
              deskripsi: this.deskripsiKegiatan,
              photo_data: this.capturedPhotos,
              pegawai_ids: this.selectedPegawaiIds
            })
          });

          const data = await response.json();

          if (response.ok && (data.status === 'success' || data.success)) {
            
            if (!shareToWa) {
              Swal.fire({
                icon: 'success',
                title: 'Tersimpan!',
                text: 'Laporan SKP berhasil disimpan. Halaman siap untuk input berikutnya.',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                window.location.href = "{{ route('sigap-skp.upload-mandiri') }}";
              });
              return;
            }

            const redirectUrl = data.redirect || "{{ route('sigap-skp.pribadi') }}";
            const origWaUrl = data.wa_url || data.wa_message;
            
            let waText = '';
            try {
              const urlObj = new URL(origWaUrl);
              waText = urlObj.searchParams.get('text') || '';
            } catch(e) {
              waText = origWaUrl;
            }

            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) || 
                          (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

            if (isIOS) {
              const iosWaScheme = 'whatsapp://send?text=' + encodeURIComponent(waText);

              Swal.fire({
                icon: 'success',
                title: 'Laporan Berhasil Disimpan!',
                text: 'Tekan tombol di bawah untuk membuka aplikasi WhatsApp.',
                showCancelButton: true,
                confirmButtonText: '📲 Buka WhatsApp',
                cancelButtonText: 'Ke SKP Pribadi',
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#4b5563',
                allowOutsideClick: false
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = iosWaScheme;
                  setTimeout(() => {
                    window.location.href = redirectUrl;
                  }, 1500);
                } else {
                  window.location.href = redirectUrl;
                }
              });

            } else {
              Swal.fire({
                icon: 'success',
                title: 'Laporan Berhasil Disimpan!',
                text: 'Membuka WhatsApp...',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                window.open(origWaUrl, '_blank');
                window.location.href = redirectUrl;
              });
            }

          } else {
            const errorMsg = data.message || 'Gagal menyimpan data laporan.';
            Swal.fire('Gagal Menyimpan', errorMsg, 'error');
          }
        } catch (err) {
          Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan laporan.', 'error');
        } finally {
          this.isLoading = false;
        }
      }
    }
  }
  </script>
</body>
</html>