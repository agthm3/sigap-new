@extends('layouts.app')

@section('content')
<div x-data="siipidForm()" class="max-w-5xl mx-auto space-y-6">

  {{-- Header & Breadcrumb --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
        <a href="{{ route('sigap-siipid.index') }}" class="hover:text-maroon">SIGAP SIIPID</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Usulkan Prestasi Baru</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Form Pendaftaran Prestasi Inovator</h1>
      <p class="text-xs text-gray-500">Isi data capaian prestasi, identitas inovator, serta lampirkan bukti fisik sertifikat/piagam.</p>
    </div>

    <a href="{{ route('sigap-siipid.index') }}" class="px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
      Kembali
    </a>
  </div>

  <form action="{{ route('sigap-siipid.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf

    {{-- CARD 1: PILIH INOVASI INDUK --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">1</span>
        <h3 class="font-bold text-gray-900 text-sm">Pilih Inovasi yang Pernah Terdaftar di BRIDA</h3>
      </div>
      
      <p class="text-xs text-gray-500">
        Pilih inovasi induk yang menjadi dasar pencapaian prestasi ini. Sistem mendeteksi karya inovasi Anda dari <strong>SIGAP Inovasi (OPD)</strong> dan <strong>SIGAP IMA (Masyarakat)</strong>.
      </p>

      <div x-data="searchableInovasi({
          options: [
            @foreach($daftarInovasiOpd as $in)
              {
                value: 'Inovasi:{{ $in->id }}',
                title: '{{ addslashes($in->judul) }}',
                source: 'SIGAP Inovasi (OPD)',
                sub: 'OPD: {{ addslashes($in->opd_unit ?: 'Umum') }} • Tahap: {{ ucfirst($in->tahap_inovasi ?: 'Penerapan') }}',
                badge: '🏛️ OPD'
              },
            @endforeach
            @foreach($daftarInovasiIma as $ima)
              {
                value: 'ImaInovasi:{{ $ima->id }}',
                title: '{{ addslashes($ima->judul) }}',
                source: 'SIGAP IMA (Masyarakat)',
                sub: 'Inisiator: {{ addslashes($ima->opd_unit ?: 'Masyarakat') }} • Tahap: {{ ucfirst($ima->tahap_inovasi ?: 'Penerapan') }}',
                badge: '💡 IMA'
              },
            @endforeach
          ],
          selected: '{{ old('innovable_selection') }}'
      })" class="relative">

        <label class="block text-xs font-bold text-gray-700 mb-1.5">
          Inovasi Induk <span class="text-red-500">*</span>
        </label>

        <!-- Hidden Input Pengirim Nilai Form -->
        <input type="hidden" name="innovable_selection" :value="selected" required>

        <!-- Trigger Button -->
        <button type="button" 
                @click="toggleDropdown()"
                class="w-full flex items-center justify-between rounded-xl px-4 py-2.5 text-xs bg-white border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon text-left shadow-2xs transition">
          <div class="truncate pr-2">
            <template x-if="selectedItem">
              <div>
                <span class="font-extrabold text-gray-900" x-text="selectedItem.title"></span>
                <span class="text-[11px] text-gray-500 block mt-0.5" x-text="selectedItem.source + ' • ' + selectedItem.sub"></span>
              </div>
            </template>
            <template x-if="!selectedItem">
              <span class="text-gray-400 font-medium">-- Ketik atau Cari Inovasi Terdaftar Anda --</span>
            </template>
          </div>
          <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" 
               :class="isOpen ? 'rotate-180 text-maroon' : ''" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>

        <!-- Floating Searchable Panel -->
        <div x-show="isOpen" 
             @click.outside="isOpen = false"
             x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-98"
             x-transition:enter-end="transform opacity-100 scale-100"
             class="absolute left-0 right-0 z-30 mt-1.5 bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden p-2 space-y-1.5">
          
          <!-- Search Input -->
          <div class="relative px-1 pt-1 pb-1.5 border-b border-gray-100">
            <input type="text" 
                   x-ref="searchInputRef"
                   x-model="search" 
                   placeholder="Ketik nama inovasi atau OPD..." 
                   class="w-full rounded-xl bg-gray-50 border border-gray-200 text-xs py-2 pl-3 pr-8 focus:bg-white focus:border-maroon focus:ring-1 focus:ring-maroon">
            <template x-if="search">
              <button type="button" @click="search = ''" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 text-xs">✕</button>
            </template>
          </div>

          <!-- List Inovasi -->
          <div class="max-h-60 overflow-y-auto space-y-1 scrollbar-thin">
            <template x-for="item in filteredOptions" :key="item.value">
              <div @click="selectItem(item)" 
                   class="p-2.5 rounded-xl cursor-pointer hover:bg-maroon-50 hover:text-maroon transition-colors flex items-start justify-between gap-2 text-xs"
                   :class="selected === item.value ? 'bg-maroon-50 border border-maroon/20' : ''">
                <div class="truncate">
                  <span class="font-bold text-gray-900 block truncate" x-text="item.title"></span>
                  <span class="text-[11px] text-gray-400 block mt-0.5" x-text="item.sub"></span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold shrink-0 bg-gray-100 text-gray-700" x-text="item.badge"></span>
              </div>
            </template>

            <template x-if="filteredOptions.length === 0">
              <div class="py-6 text-center text-xs text-gray-400">
                Inovasi tidak ditemukan. Coba kata kunci lain.
              </div>
            </template>
          </div>
        </div>

        @error('innovable_selection') <p class="text-red-600 text-[11px] mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- CARD 2: KATEGORI SASARAN & BIODATA INOVATOR --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">2</span>
        <h3 class="font-bold text-gray-900 text-sm">Identitas Inovator & Kategori Sasaran</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori Sasaran <span class="text-red-500">*</span></label>
          <select name="kategori_sasaran" x-model="kategoriSasaran" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
            <option value="asn">ASN (Aparatur Sipil Negara)</option>
            <option value="perangkat_daerah">Perangkat Daerah / OPD</option>
            <option value="masyarakat">Anggota Masyarakat / Umum</option>
            <option value="dprd">Anggota DPRD</option>
            <option value="kelompok">Kelompok / Tim Inovator</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Jenis Kepesertaan <span class="text-red-500">*</span></label>
          <select name="jenis_kepesertaan" x-model="jenisKepesertaan" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
            <option value="perorangan">Perorangan (Individu)</option>
            <option value="kelompok">Kelompok / Tim</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Lengkap Inovator / Ketua Tim <span class="text-red-500">*</span></label>
          <input type="text" name="nama_inovator" value="{{ old('nama_inovator', $user->name) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor WhatsApp (Aktif) <span class="text-red-500">*</span></label>
          <input type="text" name="no_wa" value="{{ old('no_wa', $user->phone ?? '') }}" required placeholder="Contoh: 081234567890" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <template x-if="kategoriSasaran === 'asn'">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">NIP (Nomor Induk Pegawai)</label>
            <input type="text" name="nip" value="{{ old('nip') }}" placeholder="19XXXXXXXXXXXXXX" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
          </div>
        </template>

        <template x-if="kategoriSasaran === 'masyarakat'">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">NIK (KTP)</label>
            <input type="text" name="nik" value="{{ old('nik') }}" placeholder="7371XXXXXXXXXXXX" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
          </div>
        </template>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Email Inovator <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Instansi / Unit Kerja / Komunitas</label>
          <input type="text" name="opd_instansi" value="{{ old('opd_instansi') }}" placeholder="Contoh: Dinas Kesehatan / Komunitas Riset" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Jabatan / Profesi</label>
          <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Pranata Komputer / Mahasiswa" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>
      </div>

      {{-- REPEATER ANGGOTA TIM (JIKA KELOMPOK) --}}
      <div x-show="jenisKepesertaan === 'kelompok'" x-transition class="border-t border-gray-100 pt-4 space-y-3">
        <div class="flex items-center justify-between">
          <label class="block text-xs font-bold text-gray-700">Daftar Anggota Tim & Peran</label>
          <button type="button" @click="tambahAnggota()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-[11px] transition">
            + Tambah Anggota
          </button>
        </div>

        <template x-for="(anggota, idx) in anggotaList" :key="idx">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 rounded-xl bg-gray-50 border border-gray-200/60 relative">
            <input type="text" :name="'anggota_tim['+idx+'][nama]'" x-model="anggota.nama" placeholder="Nama Anggota" class="rounded-lg border-gray-300 text-xs py-1.5 focus:ring-maroon" required>
            <input type="text" :name="'anggota_tim['+idx+'][nik_nip]'" x-model="anggota.nik_nip" placeholder="NIP / NIK" class="rounded-lg border-gray-300 text-xs py-1.5 focus:ring-maroon">
            <div class="flex items-center gap-1.5">
              <input type="text" :name="'anggota_tim['+idx+'][peran]'" x-model="anggota.peran" placeholder="Peran (misal: Programmer/Analisis)" class="w-full rounded-lg border-gray-300 text-xs py-1.5 focus:ring-maroon" required>
              <button type="button" @click="hapusAnggota(idx)" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg shrink-0" title="Hapus">✕</button>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- CARD 3: DATA PRESTASI & PENGHARGAAN --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">3</span>
        <h3 class="font-bold text-gray-900 text-sm">Rincian Prestasi & Capaian</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Prestasi / Penghargaan <span class="text-red-500">*</span></label>
          <input type="text" name="nama_prestasi" value="{{ old('nama_prestasi') }}" required placeholder="Contoh: Juara 1 Inovasi Daerah / Top 45 KIPP Sinovik" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Ajang Kompetisi / Sumber Prestasi <span class="text-red-500">*</span></label>
          <input type="text" name="ajang_kompetisi" value="{{ old('ajang_kompetisi') }}" required placeholder="Contoh: Lomba IMA 2025 / IGA Award Kemendagri" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Tingkat Prestasi <span class="text-red-500">*</span></label>
          <select name="tingkat_prestasi" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
            <option value="kota">Tingkat Kota Makassar</option>
            <option value="provinsi">Tingkat Provinsi Sulawesi Selatan</option>
            <option value="nasional">Tingkat Nasional</option>
            <option value="internasional">Tingkat Internasional</option>
            <option value="khusus">Kategori Khusus Pemerintah Kota</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Tahun Perolehan <span class="text-red-500">*</span></label>
          <input type="number" name="tahun_perolehan" value="{{ old('tahun_perolehan', date('Y')) }}" min="2000" max="{{ date('Y')+1 }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Peringkat / Capaian</label>
          <input type="text" name="peringkat_capaian" value="{{ old('peringkat_capaian') }}" placeholder="Contoh: Juara 1 / Terbaik 1 / Finalis" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Lembaga Pemberi Penghargaan <span class="text-red-500">*</span></label>
          <input type="text" name="lembaga_pemberi" value="{{ old('lembaga_pemberi') }}" required placeholder="Contoh: BRIDA Kota Makassar / Kementerian PANRB" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi Singkat Capaian & Dampak Prestasi</label>
          <textarea name="deskripsi_prestasi" rows="3" placeholder="Jelaskan ringkasan kontribusi dan dampak inovasi sehingga meraih prestasi ini..." class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">{{ old('deskripsi_prestasi') }}</textarea>
        </div>
      </div>
    </div>

    {{-- CARD 4: UNGGAH DOKUMEN BUKTI --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">4</span>
        <h3 class="font-bold text-gray-900 text-sm">Unggah Dokumen Persyaratan & Bukti Fisik</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Piagam / Sertifikat / SK Prestasi <span class="text-red-500">*</span>
          </label>
          <p class="text-[11px] text-gray-400 mb-2">Format: PDF atau Gambar (JPG/PNG). Maks 5 MB.</p>
          <input type="file" name="bukti_prestasi_file" required accept=".pdf,image/*" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Bukti Penerapan & Kemanfaatan Inovasi
          </label>
          <p class="text-[11px] text-gray-400 mb-2">Laporan evaluasi / foto penerapan lapangan (PDF, Maks 10 MB).</p>
          <input type="file" name="bukti_penerapan_file" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Surat Pernyataan Keaslian Data
          </label>
          <p class="text-[11px] text-gray-400 mb-2">Surat pernyataan kebenaran data bermaterai (PDF, Maks 5 MB).</p>
          <input type="file" name="surat_keaslian_file" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Surat Kontribusi Tim (Bila Kelompok)
          </label>
          <p class="text-[11px] text-gray-400 mb-2">Surat pembagian peran anggota tim inovator (PDF, Maks 5 MB).</p>
          <input type="file" name="surat_kontribusi_tim_file" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>
      </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <button type="submit" name="action_type" value="draft" class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs shadow-2xs transition">
        Simpan sebagai Draft
      </button>

      <button type="submit" name="action_type" value="submit" class="px-6 py-2.5 rounded-xl bg-maroon hover:bg-maroon-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
        <span>Ajukan Usulan Prestasi</span>
        <span>→</span>
      </button>
    </div>

  </form>
</div>

<script>
function searchableInovasi(config) {
  return {
    isOpen: false,
    search: '',
    options: config.options || [],
    selected: config.selected || '',

    get selectedItem() {
      return this.options.find(item => item.value === this.selected) || null;
    },

    get filteredOptions() {
      if (!this.search.trim()) {
        return this.options;
      }
      const q = this.search.toLowerCase();
      return this.options.filter(item => 
        (item.title && item.title.toLowerCase().includes(q)) ||
        (item.sub && item.sub.toLowerCase().includes(q)) ||
        (item.source && item.source.toLowerCase().includes(q))
      );
    },

    toggleDropdown() {
      this.isOpen = !this.isOpen;
      if (this.isOpen) {
        this.$nextTick(() => {
          if (this.$refs.searchInputRef) {
            this.$refs.searchInputRef.focus();
          }
        });
      }
    },

    selectItem(item) {
      this.selected = item.value;
      this.isOpen = false;
      this.search = '';
    }
  }
}

function siipidForm() {
  return {
    kategoriSasaran: '{{ old('kategori_sasaran', 'asn') }}',
    jenisKepesertaan: '{{ old('jenis_kepesertaan', 'perorangan') }}',
    anggotaList: [],
    tambahAnggota() {
      this.anggotaList.push({ nama: '', nik_nip: '', peran: '' });
    },
    hapusAnggota(idx) {
      this.anggotaList.splice(idx, 1);
    }
  }
}
</script>
@endsection
