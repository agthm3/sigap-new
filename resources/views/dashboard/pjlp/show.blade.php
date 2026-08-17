@extends('layouts.app')

@section('content')
<div x-data="pjlpVerifComponent()">

  <!-- Header -->
  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ route('sigap-pjlp.monitoring', ['bulan_tahun' => $bulanTahun]) }}" 
         class="p-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 transition">
        ←
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
          Detail Logbook: <span class="text-maroon">{{ $targetUser->name }}</span>
        </h1>
        <p class="text-xs text-gray-500 mt-0.5">
          Periode: <b>{{ \Carbon\Carbon::createFromFormat('Y-m', $bulanTahun)->translatedFormat('F Y') }}</b> | NIP: {{ $targetUser->nip ?: '-' }} | Email: {{ $targetUser->email }}
        </p>
      </div>
    </div>

    <!-- Export PDF -->
    @if($isSiapExport)
      <a href="{{ route('sigap-pjlp.export-pdf', $periode->id) }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm">
        📄 Export PDF Laporan
      </a>
    @endif
  </section>

  <!-- Dokumen Daftar Gaji -->
  <div class="rounded-2xl border border-gray-200 bg-white p-4 mt-4 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl {{ $hasDaftarGaji ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center font-bold text-sm">
        PDF
      </div>
      <div>
        <p class="text-xs font-bold text-gray-900">Dokumen Daftar Gaji Periode Ini</p>
        <p class="text-[11px] text-gray-500">
          {{ $hasDaftarGaji ? 'Berkas daftar gaji resmi sudah diunggah.' : 'Berkas belum diunggah oleh PJLP atau Petugas.' }}
        </p>
      </div>
    </div>
    @if($hasDaftarGaji)
      <a href="{{ asset('storage/' . $periode->file_daftar_gaji) }}" target="_blank"
         class="px-3.5 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 shadow-2xs">
        📄 Lihat Dokumen Gaji
      </a>
    @endif
  </div>

  <!-- Tabel Hari Kerja & Verifikasi -->
  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-xs mt-4">
    <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-gray-900 text-sm">Logbook Harian & Verifikasi Evidence</h2>
        <p class="text-xs text-gray-500 mt-0.5">Periksa kesesuaian deskripsi dan foto evidence sebelum memverifikasi.</p>
      </div>
      <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 border border-gray-200">
        {{ $totalTerisi }} / {{ $totalHariKerja }} Hari Terisi
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-600 border-b border-gray-200">
          <tr>
            <th class="px-4 py-3.5 text-left font-bold">Hari/Tanggal</th>
            <th class="px-4 py-3.5 text-center font-bold w-36">Evidence Foto (Maks 3)</th>
            <th class="px-4 py-3.5 text-left font-bold">Deskripsi Pekerjaan</th>
            <th class="px-4 py-3.5 text-left font-bold w-40">Audit Input</th>
            <th class="px-4 py-3.5 text-center font-bold w-32">Status</th>
            <th class="px-4 py-3.5 text-center font-bold w-48">Aksi Verifikasi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($logbooks as $item)
            @php
              $fotos = is_array($item->foto_evidences) ? $item->foto_evidences : ($item->foto_evidence ? [$item->foto_evidence] : []);
            @endphp
            <tr class="hover:bg-gray-50/80 transition-colors">
              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="font-bold text-gray-900">{{ $item->hari }}</div>
                <div class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
              </td>

              <!-- Foto Evidence (Multi Thumbnail) -->
              <td class="px-4 py-3.5 text-center">
                @if(count($fotos) > 0)
                  <div class="flex items-center justify-center gap-1.5 flex-wrap max-w-[130px] mx-auto">
                    @foreach($fotos as $foto)
                      <img src="{{ asset('storage/' . $foto) }}" 
                           alt="Evidence" 
                           class="w-8 h-8 rounded-lg object-cover ring-1 ring-gray-200 cursor-pointer shadow-2xs hover:scale-110 transition"
                           @click="viewImage('{{ asset('storage/' . $foto) }}')">
                    @endforeach
                  </div>
                @else
                  <span class="text-xs text-gray-400 italic">Kosong</span>
                @endif
              </td>

              <!-- Deskripsi -->
              <td class="px-4 py-3.5">
                <p class="text-xs text-gray-800 leading-relaxed font-medium">
                  {{ $item->deskripsi_pekerjaan ?: '-' }}
                </p>
                @if($item->catatan_verifikator)
                  <p class="text-[11px] text-red-600 font-semibold mt-1">Catatan: {{ $item->catatan_verifikator }}</p>
                @endif
              </td>

              <!-- Audit Trail Input -->
              <td class="px-4 py-3.5 text-xs text-gray-500">
                @if($item->updatedBy)
                  <div>Petugas: <b class="text-gray-800">{{ $item->updatedBy->name }}</b></div>
                  <div class="text-[10px] text-gray-400">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
                @else
                  -
                @endif
              </td>

              <!-- Status -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                @if($item->status === 'terverifikasi')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                    Disetujui
                  </span>
                @elseif($item->status === 'diajukan')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 border border-blue-200 text-blue-700">
                    Menunggu Verif
                  </span>
                @elseif($item->status === 'ditolak')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 border border-red-200 text-red-700">
                    Ditolak
                  </span>
                @else
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium bg-gray-100 border border-gray-200 text-gray-400">
                    Belum Diisi
                  </span>
                @endif
              </td>

              <!-- Aksi Verifikasi / Isi Atas Nama -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1.5">
                  @if($item->status === 'diajukan' || $item->status === 'ditolak')
                    <!-- Terima Form -->
                    <form action="{{ route('sigap-pjlp.verify', $item->id) }}" method="POST" class="inline">
                      @csrf
                      <input type="hidden" name="status" value="terverifikasi">
                      <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-2xs transition" title="Setujui Evidence">
                        ✓ Terima
                      </button>
                    </form>

                    <!-- Tolak Button Trigger Modal -->
                    <button type="button" @click="openRejectModal({{ $item->id }})" class="px-2.5 py-1 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 shadow-2xs transition" title="Tolak Evidence">
                      ✕ Tolak
                    </button>
                  @endif

                  <!-- Isikan / Edit Atas Nama -->
                  <button type="button" @click="openAdminEditModal({{ $item->id }})" class="px-2.5 py-1 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-100 shadow-2xs transition">
                    ✎ Isikan
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-xs">
                Tidak ada hari kerja.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL TOLAK EVIDENCE -->
  <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div @click.away="rejectModalOpen = false" class="w-full max-w-md bg-white rounded-2xl p-5 shadow-2xl border border-gray-100">
      <h3 class="font-extrabold text-gray-900 text-sm mb-1">Tolak Evidence PJLP</h3>
      <p class="text-xs text-gray-500 mb-3">Tuliskan alasan penolakan agar PJLP dapat memperbaiki foto atau uraian pekerjaannya.</p>
      
      <form :action="getRejectUrl()" method="POST">
        @csrf
        <input type="hidden" name="status" value="ditolak">
        <textarea name="catatan_verifikator" rows="3" required placeholder="Tuliskan catatan perbaikan..." class="w-full text-xs rounded-xl p-3 mb-4"></textarea>
        
        <div class="flex justify-end gap-2">
          <button type="button" @click="rejectModalOpen = false" class="px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700 shadow-sm">Kirim Penolakan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL ADMIN EDIT / ISI ATAS NAMA (MULTI-FOTO & AUTO COMPRESS) -->
  <div x-show="adminEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div @click.away="adminEditModalOpen = false" class="w-full max-w-lg bg-white rounded-2xl overflow-hidden shadow-2xl border border-gray-100">
      <div class="px-5 py-4 border-b bg-gray-50 flex justify-between items-center">
        <div>
          <h3 class="font-extrabold text-gray-900 text-sm">Isi/Edit Logbook Atas Nama PJLP</h3>
          <p class="text-xs text-gray-500 mt-0.5" x-text="activeLogbookData ? (activeLogbookData.hari + ', ' + activeLogbookData.tanggal) : ''"></p>
        </div>
        <button type="button" @click="adminEditModalOpen = false" class="text-gray-400 hover:text-gray-700 font-bold text-lg">✕</button>
      </div>

      <form :action="getAdminUpdateUrl()" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
        @csrf

        <!-- Multi-Foto Evidence (Maks 3) -->
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Unggah Foto Evidence (Maksimal 3 Foto) <span class="text-red-500">*</span>
          </label>
          <div class="flex flex-col gap-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1" x-show="previewUrls.length > 0">
              <template x-for="(url, idx) in previewUrls" :key="idx">
                <div class="relative group shrink-0">
                  <img :src="url" class="w-16 h-16 rounded-xl object-cover ring-1 ring-gray-200 shadow-2xs">
                  <span class="absolute top-1 right-1 bg-black/60 text-white text-[9px] px-1 rounded-full font-bold" x-text="idx + 1"></span>
                </div>
              </template>
            </div>

            <div class="flex-1">
              <input type="file" 
                     x-ref="adminFileInput"
                     name="foto_evidences[]" 
                     accept="image/*"
                     multiple
                     max="3"
                     @change="handleFilesUpload($event)"
                     class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-maroon/10 file:text-maroon hover:file:bg-maroon/20">
              
              <template x-if="compressInfo">
                <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1" x-text="compressInfo"></p>
              </template>
              <template x-if="isCompressing">
                <p class="text-[11px] text-amber-600 font-medium mt-1 animate-pulse">Sedang mengompres gambar...</p>
              </template>
            </div>
          </div>
          <p class="text-[10px] text-gray-400 mt-1">Pilih 1 s.d. 3 foto. Status akan otomatis menjadi <b>Terverifikasi</b> saat disimpan oleh petugas.</p>
        </div>

        <!-- Deskripsi Pekerjaan -->
        <div>
          <label for="admin_deskripsi" class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
          <textarea id="admin_deskripsi" 
                    name="deskripsi_pekerjaan" 
                    rows="3" 
                    required 
                    x-model="activeLogbookData.deskripsi_pekerjaan" 
                    placeholder="Uraian pekerjaan..." 
                    class="w-full text-xs rounded-xl p-3"></textarea>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
          <button type="button" @click="adminEditModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50">Batal</button>
          <button type="submit" 
                  :disabled="isCompressing"
                  :class="isCompressing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-maroon-800'"
                  class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-bold shadow-sm transition">
            <span x-show="!isCompressing">Simpan & Setujui</span>
            <span x-show="isCompressing">Memproses Foto...</span>
          </button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function pjlpVerifComponent() {
  return {
    rejectModalOpen: false,
    adminEditModalOpen: false,
    activeLogbookId: null,
    activeLogbookData: { deskripsi_pekerjaan: '', hari: '', tanggal: '' },
    previewUrls: [],
    compressInfo: null,
    isCompressing: false,
    logbooks: {!! json_encode($logbooks) !!},

    getRejectUrl() {
      if (!this.activeLogbookId) return '#';
      const baseUrl = "{{ url('sigap-pjlp/logbook') }}";
      return `${baseUrl}/${this.activeLogbookId}/verify`;
    },

    getAdminUpdateUrl() {
      if (!this.activeLogbookId) return '#';
      const baseUrl = "{{ url('sigap-pjlp/logbook') }}";
      return `${baseUrl}/${this.activeLogbookId}/admin-update`;
    },

    openRejectModal(id) {
      this.activeLogbookId = id;
      this.rejectModalOpen = true;
    },

    openAdminEditModal(id) {
      const item = this.logbooks.find(l => l.id === id);
      if (!item) return;
      this.activeLogbookId = id;
      this.activeLogbookData = Object.assign({}, item);
      
      let fotos = Array.isArray(item.foto_evidences) ? item.foto_evidences : [];
      if (fotos.length === 0 && item.foto_evidence) {
        fotos = [item.foto_evidence];
      }
      
      this.previewUrls = fotos.map(path => '/storage/' + path);
      this.compressInfo = null;
      this.isCompressing = false;
      this.adminEditModalOpen = true;
    },

    async handleFilesUpload(event) {
      const files = Array.from(event.target.files);
      if (files.length === 0) return;

      if (files.length > 3) {
        Swal.fire('Batas Maksimal', 'Anda hanya dapat memilih maksimal 3 foto dalam satu hari kerja.', 'warning');
        event.target.value = '';
        return;
      }

      this.isCompressing = true;
      this.compressInfo = null;
      this.previewUrls = [];
      const dt = new DataTransfer();

      try {
        let totalOriginal = 0;
        let totalCompressed = 0;

        for (let file of files) {
          if (!file.type.match(/image.*/)) continue;
          totalOriginal += file.size;

          const compressed = await this.compressImage(file, 1280, 0.75);
          totalCompressed += compressed.size;

          dt.items.add(compressed);
          this.previewUrls.push(URL.createObjectURL(compressed));
        }

        this.$refs.adminFileInput.files = dt.files;

        const origKB = (totalOriginal / 1024).toFixed(1);
        const compKB = (totalCompressed / 1024).toFixed(1);
        this.compressInfo = `✓ ${dt.files.length} Foto Terkompresi: ${origKB} KB → ${compKB} KB`;
      } catch (err) {
        console.error("Gagal mengompresi gambar:", err);
      } finally {
        this.isCompressing = false;
      }
    },

    compressImage(file, maxDimension, quality) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          const img = new Image();
          img.src = e.target.result;
          img.onload = () => {
            let width = img.width;
            let height = img.height;

            if (width > height) {
              if (width > maxDimension) {
                height = Math.round((height * maxDimension) / width);
                width = maxDimension;
              }
            } else {
              if (height > maxDimension) {
                width = Math.round((width * maxDimension) / height);
                height = maxDimension;
              }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob((blob) => {
              if (!blob) return reject(new Error('Canvas blob failed'));
              const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                type: 'image/jpeg',
                lastModified: Date.now()
              });
              resolve(compressedFile);
            }, 'image/jpeg', quality);
          };
          img.onerror = (error) => reject(error);
        };
        reader.onerror = (error) => reject(error);
      });
    },

    viewImage(url) {
      Swal.fire({
        imageUrl: url,
        imageAlt: 'Evidence Foto',
        showConfirmButton: false,
        showCloseButton: true,
      });
    }
  };
}
</script>
@endpush