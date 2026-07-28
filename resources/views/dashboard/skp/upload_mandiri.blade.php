@extends('layouts.app')

@section('content')
<div x-data="kameraMandiri(@js($myAgendas))" class="max-w-3xl mx-auto space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between border-b pb-4">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">
        Upload Mandiri <span class="text-maroon">SKP</span>
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">Ambil foto foto kegiatan langsung via kamera & laporkan ke WhatsApp.</p>
    </div>
    <a href="{{ route('sigap-skp.pribadi') }}" class="px-3 py-1.5 rounded-lg border text-xs font-semibold text-gray-600 hover:bg-gray-50">
      Batal
    </a>
  </div>

  <form @submit.prevent="submitForm">
    <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-5 shadow-sm">
      
      {{-- AREA KAMERA & FOTO --}}
      <div class="space-y-2">
        <label class="block text-sm font-semibold text-gray-700">Foto Evidence Kegiatan <span class="text-red-500">*</span></label>
        
        <div class="relative w-full aspect-[4/3] bg-black rounded-2xl overflow-hidden flex items-center justify-center shadow-inner">
          
          {{-- Video Stream Kamera --}}
          <video x-ref="video" x-show="!capturedImage" autoplay playsinline class="w-full h-full object-cover"></video>
          
          {{-- Image Preview Setelah Jepret --}}
          <img x-show="capturedImage" :src="capturedImage" class="w-full h-full object-cover" style="display: none;">

          {{-- Overlay Tombol Kamera --}}
          <div class="absolute bottom-4 inset-x-0 flex justify-center items-center gap-3">
            <template x-if="!capturedImage">
              <button type="button" @click="takeSnapshot()" class="w-14 h-14 rounded-full border-4 border-white bg-red-600 hover:bg-red-700 shadow-lg flex items-center justify-center transition-transform active:scale-95">
                <div class="w-10 h-10 rounded-full border-2 border-white"></div>
              </button>
            </template>
            <template x-if="capturedImage">
              <button type="button" @click="retakeSnapshot()" class="px-4 py-2 rounded-xl bg-black/70 hover:bg-black text-white text-xs font-bold backdrop-blur-sm shadow flex items-center gap-1.5">
                🔄 Foto Ulang
              </button>
            </template>
          </div>
        </div>

        {{-- Fallback Input File HP jika Kamera Browser Tidak Aktif --}}
        <div class="text-center pt-1">
          <label class="text-xs text-maroon hover:underline cursor-pointer font-semibold">
            <span>Atau upload foto dari galeri HP</span>
            <input type="file" accept="image/*" capture="environment" @change="handleFileFallback($event)" class="hidden">
          </label>
        </div>
      </div>

      {{-- SUMBER KEGIATAN --}}
      <div class="space-y-3 border-t pt-4">
        <label class="block text-sm font-semibold text-gray-700">Jenis Pelaporan</label>
        <div class="grid grid-cols-2 gap-3">
          <button type="button" @click="sourceMode = 'agenda'; selectAgendaItem()"
                  class="p-3 rounded-xl border text-left text-xs font-bold transition-all"
                  :class="sourceMode === 'agenda' ? 'border-maroon bg-maroon/5 text-maroon ring-2 ring-maroon/20' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
            📌 Penugasan Agenda
          </button>
          <button type="button" @click="sourceMode = 'manual'; clearFields()"
                  class="p-3 rounded-xl border text-left text-xs font-bold transition-all"
                  :class="sourceMode === 'manual' ? 'border-maroon bg-maroon/5 text-maroon ring-2 ring-maroon/20' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
            ✍️ Lapor Mandiri
          </button>
        </div>
      </div>

      {{-- DARI SIGAP AGENDA --}}
      <div x-show="sourceMode === 'agenda'" class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
        <label class="block text-xs font-bold text-gray-700 uppercase">Pilih Penugasan Saya</label>
        <select x-model="selectedAgendaId" @change="onAgendaChange" class="w-full rounded-lg text-sm border-gray-300">
          <option value="">-- Pilih Agenda Kegiatan --</option>
          <template x-for="agenda in myAgendas" :key="agenda.id">
            <option :value="agenda.id" x-text="agenda.date + ' - ' + agenda.unit_title"></option>
          </template>
        </select>
      </div>

      {{-- FORM DETAIL --}}
      <div class="space-y-4 border-t pt-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
          <input type="text" x-model="judulKegiatan" required :readonly="sourceMode === 'agenda'"
                 class="w-full rounded-lg text-sm border-gray-300" :class="sourceMode === 'agenda' ? 'bg-gray-100' : 'bg-white'">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
            <input type="date" x-model="tanggalKegiatan" required :readonly="sourceMode === 'agenda'"
                   class="w-full rounded-lg text-sm border-gray-300" :class="sourceMode === 'agenda' ? 'bg-gray-100' : 'bg-white'">
          </div>
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Lokasi</label>
            <input type="text" x-model="lokasiKegiatan" placeholder="Contoh: Ruang Rapat Lt.2"
                   class="w-full rounded-lg text-sm border-gray-300">
          </div>
        </div>

        <div x-show="sourceMode === 'manual'">
          <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Singkat</label>
          <textarea x-model="deskripsiKegiatan" rows="2" placeholder="Tuliskan ringkasan kegiatan yang dilakukan..."
                    class="w-full rounded-lg text-sm border-gray-300"></textarea>
        </div>
      </div>

      {{-- SUBMIT BUTTON --}}
      <button type="submit" :disabled="isLoading || !capturedImage"
              class="w-full py-3 bg-maroon text-white font-bold text-sm rounded-xl hover:bg-maroon-800 disabled:opacity-50 transition-colors shadow flex items-center justify-center gap-2">
        <span x-show="!isLoading">Simpan & Bagikan ke WhatsApp 🚀</span>
        <span x-show="isLoading" class="animate-pulse">Menyimpan Laporan...</span>
      </button>

    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
function kameraMandiri(myAgendasList) {
  return {
    stream: null,
    capturedImage: null,
    sourceMode: 'agenda',
    myAgendas: myAgendasList || [],
    selectedAgendaId: '',
    judulKegiatan: '',
    tanggalKegiatan: new Date().toISOString().split('T')[0],
    lokasiKegiatan: '',
    deskripsiKegiatan: '',
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
      try {
        this.stream = await navigator.mediaDevices.getUserMedia({ 
          video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } 
        });
        this.$refs.video.srcObject = this.stream;
      } catch (err) {
        console.warn("Kamera tidak dapat diakses langsung:", err);
      }
    },

    stopCamera() {
      if (this.stream) {
        this.stream.getTracks().forEach(track => track.stop());
      }
    },

    takeSnapshot() {
      const video = this.$refs.video;
      const canvas = document.createElement('canvas');
      canvas.width = video.videoWidth || 640;
      canvas.height = video.videoHeight || 480;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      
      // Kompres snapshot ke JPEG 0.75
      this.capturedImage = canvas.toDataURL('image/jpeg', 0.75);
      this.stopCamera();
    },

    retakeSnapshot() {
      this.capturedImage = null;
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
        };
      };
      reader.readAsDataURL(file);
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

    async submitForm() {
    if (!this.capturedImage) {
        Swal.fire('Perhatian', 'Harap jepret foto evidence terlebih dahulu.', 'warning');
        return;
    }

    this.isLoading = true;

    try {
        // 1. Salin Gambar yang Dijepret ke Clipboard HP/Komputer
        try {
        const responseImg = await fetch(this.capturedImage);
        const blob = await responseImg.blob();
        await navigator.clipboard.write([
            new ClipboardItem({ [blob.type]: blob })
        ]);
        } catch (err) {
        console.log("Clipboard API tidak didukung di browser ini:", err);
        }

        // 2. Kirim Data Form ke Server
        const response = await fetch("{{ route('sigap-skp.store-mandiri') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            source_mode: this.sourceMode,
            agenda_id: this.selectedAgendaId,
            judul_kegiatan: this.judulKegiatan,
            tanggal: this.tanggalKegiatan,
            lokasi: this.lokasiKegiatan,
            deskripsi: this.deskripsiKegiatan,
            photo_data: this.capturedImage
        })
        });

        const data = await response.json();

        if (data.status === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Laporan Berhasil Disimpan!',
            html: 'Foto telah <b>disalin ke clipboard</b>!<br>Silakan tekan <b>PASTE (TEMPEL)</b> di kolom chat WhatsApp.',
            confirmButtonText: 'Buka WhatsApp & Tempel',
            confirmButtonColor: '#059669',
        }).then(() => {
            window.open(data.wa_url, '_blank');
            window.location.href = data.redirect;
        });
        }
    } catch (err) {
        Swal.fire('Error', 'Gagal menyimpan laporan mandiri.', 'error');
    } finally {
        this.isLoading = false;
    }
    }
  }
}
</script>
@endpush