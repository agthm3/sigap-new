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
  </style>

<!-- PWA Meta Tags -->
<link rel="manifest" href="https://sigap.brida.makassarkota.go.id/manifest.json?v=3">
<meta name="theme-color" content="#7a2222">

<!-- Apple Touch Icon -->
<link rel="apple-touch-icon" sizes="192x192" href="https://sigap.brida.makassarkota.go.id/images/icon-192.png">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex flex-col justify-between">

  <div x-data="kameraMandiri(@js($myAgendas))" class="w-full max-w-lg mx-auto flex-1 flex flex-col justify-between p-4 space-y-4">

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

    {{-- AREA KAMERA UTAMA --}}
    <div class="space-y-4 flex-1 flex flex-col justify-center">

      <div class="relative w-full aspect-[4/3] bg-black rounded-3xl overflow-hidden shadow-2xl border-2 border-gray-800 flex items-center justify-center">
        
        <!-- Stream Video Kamera -->
        <video x-ref="video" x-show="!capturedImage" autoplay playsinline class="w-full h-full object-cover"></video>
        
        <!-- Pratinjau Foto dengan Style Rotasi -->
        <img x-show="capturedImage" :src="capturedImage" 
             :style="'transform: rotate(' + rotationAngle + 'deg); transition: transform 0.3s ease;'" 
             class="w-full h-full object-cover" style="display: none;">

        <!-- TOMBOL OVERLAY KAMERA -->
        <div class="absolute bottom-4 inset-x-0 flex justify-center items-center gap-4 z-10">
          
          <!-- Tombol Switch Kamera Depan / Belakang -->
          <template x-if="!capturedImage">
            <button type="button" @click="switchCamera()" class="p-3 rounded-full bg-black/50 hover:bg-black/80 text-white backdrop-blur-md border border-white/20 transition-all active:scale-90" title="Ganti Kamera">
              <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </button>
          </template>

          <!-- Tombol Jepret Foto -->
          <template x-if="!capturedImage">
            <button type="button" @click="takeSnapshot()" class="w-16 h-16 rounded-full border-4 border-white bg-red-600 hover:bg-red-700 shadow-2xl flex items-center justify-center transition-transform active:scale-90">
              <div class="w-12 h-12 rounded-full border-2 border-white"></div>
            </button>
          </template>

          <!-- Tombol Rotate Gambar 90 Derajat -->
          <template x-if="capturedImage">
            <button type="button" @click="rotateImage()" class="px-3.5 py-2.5 rounded-full bg-black/80 hover:bg-black text-white text-xs font-bold backdrop-blur-md border border-white/20 shadow-xl flex items-center gap-1.5 transition-transform active:scale-95">
              🔄 Putar
            </button>
          </template>

          <!-- Tombol Foto Ulang -->
          <template x-if="capturedImage">
            <button type="button" @click="retakeSnapshot()" class="px-3.5 py-2.5 rounded-full bg-black/80 hover:bg-black text-white text-xs font-bold backdrop-blur-md border border-white/20 shadow-xl flex items-center gap-1.5 transition-transform active:scale-95">
              📷 Ulang
            </button>
          </template>

        </div>
      </div>

      <!-- Fallback Upload File Galeri -->
      <div class="text-center">
        <label class="text-xs text-gray-400 hover:text-white cursor-pointer font-medium underline">
          <span>Atau pilih foto dari galeri HP</span>
          <input type="file" accept="image/*" capture="environment" @change="handleFileFallback($event)" class="hidden">
        </label>
      </div>

    </div>

    {{-- FORM ISIAN DATA --}}
    <form @submit.prevent="submitForm" class="bg-gray-800 rounded-3xl p-4 space-y-3 border border-gray-700 shadow-xl">
      
      <!-- Pilihan Tab Jenis Pelaporan -->
      <div class="grid grid-cols-2 gap-2 bg-gray-900/60 p-1 rounded-2xl border border-gray-700">
        <button type="button" @click="sourceMode = 'agenda'; selectAgendaItem()"
                class="py-2 rounded-xl text-xs font-bold transition-all"
                :class="sourceMode === 'agenda' ? 'bg-maroon text-white shadow' : 'text-gray-400 hover:text-white'">
          📌 Penugasan Agenda
        </button>
        <button type="button" @click="sourceMode = 'manual'; clearFields()"
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

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-[11px] font-bold text-gray-300">Tanggal <span class="text-red-400">*</span></label>
            <input type="date" x-model="tanggalKegiatan" required :readonly="sourceMode === 'agenda'"
                   class="w-full rounded-xl text-xs bg-gray-900 border-gray-700 text-white p-2.5" :class="sourceMode === 'agenda' ? 'opacity-70 cursor-not-allowed' : ''">
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

      <!-- Submit Button -->
      <button type="submit" :disabled="isLoading || !capturedImage"
              class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl disabled:opacity-40 transition-all shadow-lg flex items-center justify-center gap-2">
        <span x-show="!isLoading">Simpan & Bagikan ke WhatsApp 🚀</span>
        <span x-show="isLoading" class="animate-pulse">Proses Menyimpan & Kompresi...</span>
      </button>

    </form>

  </div>

  <script>
  function kameraMandiri(myAgendasList) {
    return {
      stream: null,
      capturedImage: null,
      rotationAngle: 0,
      sourceMode: 'agenda',
      myAgendas: myAgendasList || [],
      selectedAgendaId: '',
      judulKegiatan: '',
      tanggalKegiatan: new Date().toISOString().split('T')[0],
      lokasiKegiatan: '',
      deskripsiKegiatan: '',
      facingMode: 'environment',
      isLoading: false,

      init() {
        this.startCamera();
        if(this.myAgendas.length > 0) {
          this.selectedAgendaId = this.myAgendas[0].id;
          this.onAgendaChange();
        } else {
          this.sourceMode = 'manual';
        }
      },

      async startCamera() {
        this.stopCamera();
        try {
          this.stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
              facingMode: this.facingMode,
              width: { ideal: 1280 }, 
              height: { ideal: 720 } 
            } 
          });
          this.$refs.video.srcObject = this.stream;
        } catch (err) {
          try {
            this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
            this.$refs.video.srcObject = this.stream;
          } catch(e) {
            console.error("Kamera tidak diizinkan atau tidak ditemukan.");
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

      rotateImage() {
        this.rotationAngle = (this.rotationAngle + 90) % 360;
      },

      takeSnapshot() {
        const video = this.$refs.video;
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 720;
        const ctx = canvas.getContext('2d');

        if (this.facingMode === 'user') {
          ctx.translate(canvas.width, 0);
          ctx.scale(-1, 1);
        }

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        this.capturedImage = canvas.toDataURL('image/jpeg', 0.75);
        this.rotationAngle = 0;
        this.stopCamera();
      },

      retakeSnapshot() {
        this.capturedImage = null;
        this.rotationAngle = 0;
        this.startCamera();
      },

      handleFileFallback(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (event) => {
          const img = new Image();
          img.src = event.target.result;
          img.onload = () => {
            const canvas = document.createElement('canvas');
            let width = img.width;
            let height = img.height;
            if (width > 1200) {
              height = Math.round((height * 1200) / width);
              width = 1200;
            }
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);
            this.capturedImage = canvas.toDataURL('image/jpeg', 0.75);
            this.rotationAngle = 0;
          };
        };
        reader.readAsDataURL(file);
      },

      getFinalRotatedBase64() {
        if (this.rotationAngle === 0) return this.capturedImage;

        return new Promise((resolve) => {
          const img = new Image();
          img.src = this.capturedImage;
          img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            if (this.rotationAngle === 90 || this.rotationAngle === 270) {
              canvas.width = img.height;
              canvas.height = img.width;
            } else {
              canvas.width = img.width;
              canvas.height = img.height;
            }

            ctx.translate(canvas.width / 2, canvas.height / 2);
            ctx.rotate((this.rotationAngle * Math.PI) / 180);
            ctx.drawImage(img, -img.width / 2, -img.height / 2);

            resolve(canvas.toDataURL('image/jpeg', 0.75));
          };
        });
      },

      onAgendaChange() {
        const selected = this.myAgendas.find(a => a.id == this.selectedAgendaId);
        if (selected) {
          this.judulKegiatan = selected.unit_title;
          this.tanggalKegiatan = selected.date;
          this.lokasiKegiatan = selected.place;
          this.deskripsiKegiatan = selected.description;
        }
      },

      clearFields() {
        this.selectedAgendaId = '';
        this.judulKegiatan = '';
        this.lokasiKegiatan = '';
        this.deskripsiKegiatan = '';
      },

      selectAgendaItem() {
        if (this.myAgendas.length > 0 && !this.selectedAgendaId) {
          this.selectedAgendaId = this.myAgendas[0].id;
          this.onAgendaChange();
        }
      },

      async submitForm() {
        if (!this.capturedImage) {
          Swal.fire('Perhatian', 'Harap jepret foto evidence terlebih dahulu.', 'warning');
          return;
        }

        this.isLoading = true;

        try {
          const finalPhoto = await this.getFinalRotatedBase64();

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
              photo_data: finalPhoto
            })
          });

          const data = await response.json();

          if (response.ok && data.status === 'success') {
            Swal.fire({
              icon: 'success',
              title: 'Laporan Berhasil Disimpan!',
              text: 'Membuka WhatsApp untuk membagikan laporan...',
              timer: 2000,
              showConfirmButton: false
            }).then(() => {
              window.open(data.wa_url, '_blank');
              window.location.href = data.redirect;
            });
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