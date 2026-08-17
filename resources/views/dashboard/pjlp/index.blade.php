@extends('layouts.app')

@section('content')
<div x-data="pjlpLogbookComponent()">

  <!-- Header & Filter Periode + Selector PJLP -->
  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        SIGAP <span class="text-maroon">PJLP</span>
      </h1>
      <p class="text-sm text-gray-600 mt-0.5">
        @if(isset($isAdminOrVerif) && $isAdminOrVerif)
          Mengelola & Mengisikan Logbook: <b class="text-gray-900">{{ $targetUser->name }}</b>
        @else
          Pengisian Logbook Harian & Evidence Pekerjaan Kebersihan.
        @endif
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
      <!-- Selector PJLP (Hanya Tampil Jika Admin / Superadmin / Verif PJLP) & Periode Bulan -->
      <form method="GET" action="{{ route('sigap-pjlp.index') }}" class="flex flex-wrap items-center gap-2">
        @if(isset($isAdminOrVerif) && $isAdminOrVerif && isset($pjlpUsers) && $pjlpUsers->count() > 0)
          <label for="user_id" class="text-xs font-semibold text-gray-500">Pilih PJLP:</label>
          <select id="user_id" name="user_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border text-xs font-bold text-gray-800 focus:ring-0">
            @foreach($pjlpUsers as $u)
              <option value="{{ $u->id }}" {{ $targetUser->id == $u->id ? 'selected' : '' }}>
                {{ $u->name }}
              </option>
            @endforeach
          </select>
        @endif

        <label for="bulan_tahun" class="text-xs font-semibold text-gray-500">Periode:</label>
        <input type="month" id="bulan_tahun" name="bulan_tahun" value="{{ $bulanTahun }}" 
               onchange="this.form.submit()"
               class="px-3 py-1.5 rounded-xl border text-sm font-semibold focus:ring-0">
      </form>

      <!-- Tombol Export PDF -->
      @if($isSiapExport)
        <a href="{{ route('sigap-pjlp.export-pdf', $periode->id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Export PDF Laporan
        </a>
      @else
        <button type="button" 
                onclick="Swal.fire('Belum Lengkap', 'Harap lengkapi seluruh hari kerja dan unggah berkas Daftar Gaji untuk mengunduh laporan PDF.', 'info')"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-200 text-gray-400 text-sm font-semibold cursor-not-allowed">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
          Export PDF (Terkunci)
        </button>
      @endif
    </div>
  </section>

  <!-- Banner Info Jika Diisi Atas Nama (Role Verif / Admin) -->
  @if(isset($isAdminOrVerif) && $isAdminOrVerif)
    <div class="rounded-2xl border border-blue-200 bg-blue-50/60 p-3.5 mt-3 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="text-base">👤</span>
        <div class="text-xs text-blue-900">
          Sedang membuka logbook milik: <strong class="font-bold">{{ $targetUser->name }}</strong> ({{ $targetUser->email }}). Data yang disimpan akan tercatat audit pengisinya.
        </div>
      </div>
      <a href="{{ route('sigap-pjlp.monitoring', ['bulan_tahun' => $bulanTahun]) }}" class="text-xs font-bold text-blue-700 hover:underline shrink-0">
        ← Kembali ke Monitoring
      </a>
    </div>
  @endif

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Hari Kerja</p>
      <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalHariKerja }} Hari</h3>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Logbook Terisi</p>
      <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $totalTerisi }} / {{ $totalHariKerja }}</h3>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Terverifikasi</p>
      <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $totalTerverifikasi }}</h3>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Status Daftar Gaji</p>
      <div class="mt-1">
        @if($hasDaftarGaji)
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
            ✓ Terunggah
          </span>
        @else
          <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
            ! Belum Diunggah
          </span>
        @endif
      </div>
    </div>
  </div>

  <!-- BARIS PERTAMA: Dokumen Daftar Gaji Sesuai Periode -->
  <div class="rounded-2xl border-2 border-dashed {{ $hasDaftarGaji ? 'border-emerald-300 bg-emerald-50/30' : 'border-maroon/30 bg-maroon/5' }} p-5 mt-4 transition-all">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl {{ $hasDaftarGaji ? 'bg-emerald-100 text-emerald-700' : 'bg-maroon/10 text-maroon' }} flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <div>
          <h2 class="text-sm font-bold text-gray-900">Dokumen Daftar Gaji PJLP (Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanTahun)->translatedFormat('F Y') }})</h2>
          <p class="text-xs text-gray-500 mt-0.5">Unggah berkas resmi daftar gaji format PDF (maks. 5MB) untuk digabungkan ke laporan akhir.</p>
        </div>
      </div>

      <div class="flex items-center gap-2 w-full md:w-auto">
        @if($hasDaftarGaji)
          <a href="{{ asset('storage/' . $periode->file_daftar_gaji) }}" target="_blank"
             class="px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 shadow-2xs">
            📄 Lihat File PDF
          </a>
        @endif
        
        <form action="{{ route('sigap-pjlp.upload-gaji', $periode->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
          @csrf
          <label class="cursor-pointer px-4 py-2 rounded-xl {{ $hasDaftarGaji ? 'bg-gray-800 hover:bg-gray-900' : 'bg-maroon hover:bg-maroon-800' }} text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5">
            <span>{{ $hasDaftarGaji ? 'Ganti PDF Gaji' : 'Upload PDF Gaji' }}</span>
            <input type="file" name="file_daftar_gaji" accept=".pdf" class="hidden" onchange="this.form.submit()">
          </label>
        </form>
      </div>
    </div>
  </div>

  <!-- BARIS SELANJUTNYA: Tabel Seluruh Hari Kerja Bulan Terpilih -->
  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
    <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-gray-900 text-sm">Daftar Hari Kerja & Logbook Harian</h2>
        <p class="text-xs text-gray-500 mt-0.5">Semua hari kerja aktif terbuka. Klik tombol "Isi" atau "Edit" pada tanggal terkait.</p>
      </div>
      <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 border border-gray-200">
        {{ $logbooks->count() }} Hari Kerja Terdeteksi
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-600 border-b border-gray-200">
          <tr>
            <th class="px-4 py-3.5 text-left font-bold">Hari & Tanggal</th>
            <th class="px-4 py-3.5 text-center font-bold w-36">Evidence Foto (Maks 3)</th>
            <th class="px-4 py-3.5 text-left font-bold">Deskripsi Pekerjaan</th>
            <th class="px-4 py-3.5 text-center font-bold w-36">Status</th>
            <th class="px-4 py-3.5 text-left font-bold">Catatan Verifikator</th>
            <th class="px-4 py-3.5 text-center font-bold w-24">Aksi</th>
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
                           class="w-8 h-8 rounded-lg object-cover ring-1 ring-gray-200 shadow-2xs hover:scale-110 transition cursor-pointer"
                           @click="openEditModalById({{ $item->id }})">
                    @endforeach
                  </div>
                @else
                  <div class="w-10 h-10 rounded-lg border-2 border-dashed border-gray-200 flex items-center justify-center mx-auto text-gray-400 text-xs">
                    -
                  </div>
                @endif
              </td>

              <!-- Deskripsi Pekerjaan -->
              <td class="px-4 py-3.5">
                @if($item->deskripsi_pekerjaan)
                  <p class="text-xs text-gray-800 line-clamp-2 leading-relaxed font-medium">{{ $item->deskripsi_pekerjaan }}</p>
                @else
                  <span class="text-xs italic text-gray-400">Belum ada uraian pekerjaan.</span>
                @endif
              </td>

              <!-- Status Badge -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                @if($item->status === 'terverifikasi')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                    Terverifikasi
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
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium bg-gray-100 border border-gray-200 text-gray-500">
                    Belum Diisi
                  </span>
                @endif
              </td>

              <!-- Catatan Verifikator -->
              <td class="px-4 py-3.5">
                @if($item->catatan_verifikator)
                  <span class="text-xs text-red-600 font-semibold">{{ $item->catatan_verifikator }}</span>
                @else
                  <span class="text-xs text-gray-400">-</span>
                @endif
              </td>

              <!-- Tombol Aksi -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <button type="button"
                        @click="openEditModalById({{ $item->id }})"
                        class="px-3 py-1.5 rounded-lg border {{ $item->status === 'belum_diisi' ? 'border-maroon text-maroon hover:bg-maroon hover:text-white font-bold' : 'border-gray-300 text-gray-700 hover:bg-gray-100 font-medium' }} text-xs transition">
                  {{ $item->status === 'belum_diisi' ? 'Isi' : 'Edit' }}
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-xs">
                Tidak ada hari kerja pada periode yang dipilih.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL PENGISIAN EVIDENCE & MULTI-FOTO DENGAN CLIENT-SIDE AUTO COMPRESS -->
  <div x-show="modalOpen" 
       x-cloak
       class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    
    <div @click.away="modalOpen = false"
         class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
      
      <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
        <div>
          <h3 class="font-extrabold text-gray-900 text-sm">Form Evidence Logbook</h3>
          <p class="text-xs text-gray-500 mt-0.5" x-text="activeLogbook ? (activeLogbook.hari + ', ' + activeLogbook.tanggal) : ''"></p>
        </div>
        <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-700 font-bold text-lg">✕</button>
      </div>

      <form :action="getUpdateUrl()" 
            method="POST" 
            enctype="multipart/form-data" 
            class="p-5 space-y-4">
        @csrf

        <!-- Multi-Foto Evidence (Maks 3) -->
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Unggah Foto Evidence (Maksimal 3 Foto) <span class="text-red-500">*</span>
          </label>
          <div class="flex flex-col gap-3">
            <!-- Galeri Pratinjau Foto -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1" x-show="previewUrls.length > 0">
              <template x-for="(url, idx) in previewUrls" :key="idx">
                <div class="relative group shrink-0">
                  <img :src="url" class="w-16 h-16 rounded-xl object-cover ring-1 ring-gray-200 shadow-2xs">
                  <span class="absolute top-1 right-1 bg-black/60 text-white text-[9px] px-1 rounded-full font-bold" x-text="idx + 1"></span>
                </div>
              </template>
            </div>

            <!-- Input File Multi -->
            <div class="flex-1">
              <input type="file" 
                     x-ref="fileInput"
                     name="foto_evidences[]" 
                     accept="image/*"
                     multiple
                     max="3"
                     @change="handleFilesUpload($event)"
                     class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-maroon/10 file:text-maroon hover:file:bg-maroon/20">
              
              <!-- Info Kompresi -->
              <template x-if="compressInfo">
                <p class="text-[11px] text-emerald-600 font-medium mt-1 flex items-center gap-1" x-text="compressInfo"></p>
              </template>
              <template x-if="isCompressing">
                <p class="text-[11px] text-amber-600 font-medium mt-1 animate-pulse">Sedang mengompres gambar...</p>
              </template>
            </div>
          </div>
          <p class="text-[10px] text-gray-400 mt-1">Pilih 1 s.d. 3 foto sekaligus. Mengunggah foto baru akan menggantikan evidence sebelumnya.</p>
        </div>

        <!-- Deskripsi Pekerjaan -->
        <div>
          <label for="deskripsi_pekerjaan" class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
          <textarea id="deskripsi_pekerjaan" 
                    name="deskripsi_pekerjaan" 
                    rows="3" 
                    x-model="activeLogbook.deskripsi_pekerjaan" 
                    required 
                    placeholder="Contoh: Membersihkan seluruh area lobby utama, mengepel selasar, dan menyapu halaman depan." 
                    class="w-full rounded-xl text-xs p-3"></textarea>
        </div>

        <!-- Modal Footer -->
        <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
          <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50">
            Batal
          </button>
          <button type="submit" 
                  :disabled="isCompressing"
                  :class="isCompressing ? 'opacity-50 cursor-not-allowed' : 'hover:bg-maroon-800'"
                  class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-bold shadow-sm transition">
            <span x-show="!isCompressing">Simpan Evidence</span>
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
function pjlpLogbookComponent() {
  return {
    modalOpen: false,
    activeLogbook: { id: '', hari: '', tanggal: '', deskripsi_pekerjaan: '' },
    previewUrls: [],
    compressInfo: null,
    isCompressing: false,
    logbooks: {!! json_encode($logbooks) !!},

    getUpdateUrl() {
      if (!this.activeLogbook || !this.activeLogbook.id) return '#';
      const baseUrl = "{{ url('sigap-pjlp/logbook') }}";
      return `${baseUrl}/${this.activeLogbook.id}/update`;
    },

    openEditModalById(id) {
      const item = this.logbooks.find(l => l.id === id);
      if (!item) return;
      this.activeLogbook = Object.assign({}, item);
      
      let fotos = Array.isArray(item.foto_evidences) ? item.foto_evidences : [];
      if (fotos.length === 0 && item.foto_evidence) {
        fotos = [item.foto_evidence];
      }
      
      this.previewUrls = fotos.map(path => '/storage/' + path);
      this.compressInfo = null;
      this.isCompressing = false;
      this.modalOpen = true;
    },

    /**
     * Kompresi Client-Side Multi-File (Maksimal 3 Foto)
     */
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

        this.$refs.fileInput.files = dt.files;

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
    }
  };
}
</script>
@endpush