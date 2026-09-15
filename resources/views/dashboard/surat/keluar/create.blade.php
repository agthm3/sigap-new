@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="mb-4">
    <h1 class="text-xl font-extrabold text-gray-900">Ambil Nomor Surat Keluar</h1>
    <p class="text-sm text-gray-600">Pilih mode penomoran otomatis untuk operasional berjalan atau mode manual untuk migrasi buku fisik.</p>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm" x-data="suratKeluarForm()">
    <form action="{{ route('sigap-surat.keluar.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <!-- ========================================================================= -->
      <!-- PILIHAN MODE PENOMORAN (OTOMATIS VS MIGRASI BUKU FISIK) -->
      <!-- ========================================================================= -->
      <div class="mb-5 p-4 rounded-xl bg-amber-50/80 border border-amber-200">
        <label class="block text-xs font-bold text-amber-900 uppercase tracking-wide mb-2">
          Metode Penomoran Surat
        </label>
        <div class="flex flex-col sm:flex-row gap-4">
          <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer text-gray-800">
            <input type="radio" name="mode_penomoran" value="otomatis" x-model="modePenomoran" class="text-maroon focus:ring-maroon">
            <span>Otomatis (Antrean / Slot Berjalan)</span>
          </label>
          
          <label class="flex items-center gap-2 text-xs font-semibold cursor-pointer text-gray-800">
            <input type="radio" name="mode_penomoran" value="manual" x-model="modePenomoran" class="text-maroon focus:ring-maroon">
            <span class="text-maroon font-bold">📖 Migrasi dari Buku Fisik (Ketik Nomor Urut Sendiri)</span>
          </label>
        </div>

        <!-- Input Nomor Urut Manual jika Mode Migrasi Buku Fisik Aktif -->
        <div x-show="modePenomoran === 'manual'" class="mt-3 pt-3 border-t border-amber-200/60" x-cloak>
          <label class="block text-xs font-semibold text-gray-700 mb-1">
            Nomor Urut Buku Fisik *
          </label>
          <div class="flex items-center gap-2 max-w-xs">
            <input type="number" name="nomor_urut_manual" placeholder="Contoh: 15" min="1"
                   :required="modePenomoran === 'manual'"
                   class="w-full rounded-xl px-3.5 py-2 text-sm border border-gray-300 font-mono font-bold text-gray-900 bg-white focus:border-maroon focus:ring-1 focus:ring-maroon">
            <span class="text-xs text-gray-500 whitespace-nowrap">Sesuaikan persis isi buku</span>
          </div>
          <p class="text-[11px] text-amber-800 mt-1">
            Sistem tidak akan membuat lompatan baris cadangan dan langsung menggunakan nomor urut yang Anda isi.
          </p>
          @error('nomor_urut_manual')
            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="space-y-4">
        <!-- Tanggal Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Surat *</label>
          <input type="date" name="tanggal" x-model="tanggal" @change="cekSlot()"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
        </div>

        <!-- Banner Deteksi Slot Cadangan Tanggal Terpilih (Hanya Tampil di Mode Otomatis) -->
        <template x-if="modePenomoran === 'otomatis'">
          <div>
            <div x-show="loadingSlot" class="text-xs text-gray-500">Memeriksa slot ketersediaan nomor...</div>
            
            <template x-if="slots.length > 0">
              <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl space-y-2">
                <div class="flex items-center justify-between">
                  <span class="text-xs font-bold text-amber-900">
                    ✨ Ditemukan <span x-text="slots.length"></span> Slot Cadangan di Tanggal Ini
                  </span>
                  <span class="text-[10px] text-amber-700">(Bisa pilih slot mundur)</span>
                </div>
                <select name="slot_id" class="w-full rounded-lg text-xs py-2 px-3 bg-white border border-gray-300">
                  <option value="">-- Ambil Slot Otomatis (No. Urut Paling Awal) --</option>
                  <template x-for="slot in slots" :key="slot.id">
                    <option :value="slot.id" x-text="'Gunakan Nomor Urut ' + slot.nomor_urut"></option>
                  </template>
                </select>
              </div>
            </template>

            <template x-if="!loadingSlot && slots.length === 0 && tanggal">
              <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800">
                ℹ️ Belum ada blok di tanggal ini. Sistem akan otomatis menyiapkan nomor sesuai antrean takwim.
              </div>
            </template>
          </div>
        </template>

        <!-- Kode Klasifikasi Paten Permendagri 83/2022 (Searchable Dropdown) -->
        <div x-data="searchableSelect({
            options: [
                @foreach($daftarKlasifikasi as $kategori => $items)
                @foreach($items as $kode => $label)
                    { kode: '{{ $kode }}', label: '{{ $label }}', group: '{{ $kategori }}' },
                @endforeach
                @endforeach
            ],
            selected: '{{ old('nomor_berkas') }}'
            })" 
            class="relative">

          <label class="block text-xs font-semibold text-gray-700 mb-1">
            Kode Klasifikasi Surat (Permendagri 83/2022) *
          </label>

          <!-- Input Hidden yang dikirim ke Backend -->
          <input type="hidden" name="nomor_berkas" :value="selected" required>

          <!-- Tombol Trigger Dropdown -->
          <button type="button" 
                  @click="toggleDropdown()"
                  class="w-full flex items-center justify-between rounded-xl px-3.5 py-2.5 text-sm bg-white border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon text-left">
            <span x-text="selectedLabel || '-- Cari / Pilih Kode Klasifikasi --'"
                  :class="selected ? 'text-gray-900 font-semibold' : 'text-gray-400'"></span>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" 
                 :class="isOpen ? 'rotate-180 text-maroon' : ''" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>

          <!-- Floating Menu Panel -->
          <div x-show="isOpen" 
               x-transition:enter="transition ease-out duration-100"
               x-transition:enter-start="opacity-0 scale-95"
               x-transition:enter-end="opacity-100 scale-100"
               x-transition:leave="transition ease-in duration-75"
               x-transition:leave-start="opacity-100 scale-100"
               x-transition:leave-end="opacity-0 scale-95"
               @click.outside="isOpen = false"
               class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden"
               x-cloak>

            <!-- Search Input Bar -->
            <div class="p-2 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"></line>
              </svg>
              <input type="text" 
                     x-ref="searchInput"
                     x-model="search" 
                     placeholder="Ketik kode (misal: 070) atau kata kunci (misal: riset, cuti)..."
                     class="w-full text-xs bg-transparent border-0 focus:ring-0 focus:outline-none p-1 placeholder-gray-400">
              <span x-show="search" @click="search = ''" class="cursor-pointer text-xs text-gray-400 hover:text-gray-600 px-1">✕</span>
            </div>

            <!-- Items List -->
            <div class="max-h-60 overflow-y-auto p-1.5 space-y-1 scrollbar-thin text-xs">
              <template x-for="item in filteredOptions" :key="item.kode">
                <div @click="selectOption(item)" 
                     :class="selected === item.kode ? 'bg-maroon text-white font-semibold' : 'text-gray-700 hover:bg-gray-100'"
                     class="px-3 py-2 rounded-lg cursor-pointer flex items-center justify-between transition-colors">
                  <div>
                    <span class="font-mono font-bold mr-1.5" 
                          :class="selected === item.kode ? 'text-amber-200' : 'text-maroon'"
                          x-text="item.kode"></span>
                    <span x-text="item.label.replace(item.kode + ' - ', '')"></span>
                  </div>
                  <span class="text-[10px] px-1.5 py-0.5 rounded"
                        :class="selected === item.kode ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'"
                        x-text="item.group.includes('POPULER') ? '⭐ Rekomendasi' : ''"></span>
                </div>
              </template>

              <!-- Empty State -->
              <div x-show="filteredOptions.length === 0" class="py-4 text-center text-xs text-gray-400">
                Kode atau nama klasifikasi tidak ditemukan.
              </div>
            </div>
          </div>

          <p class="text-[11px] text-gray-400 mt-1">
            Ketik langsung untuk menyaring kode klasifikasi yang diinginkan.
          </p>
        </div>

        <!-- Alamat Penerima -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Penerima / Tujuan *</label>
          <input type="text" name="alamat_penerima" value="{{ old('alamat_penerima') }}" placeholder="Contoh: Kepala Badan Perencanaan Pembangunan Daerah"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>
        </div>

        <!-- Perihal -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Perihal *</label>
          <textarea name="perihal" rows="3" placeholder="Uraian perihal surat dinas..."
                    class="w-full rounded-xl px-3.5 py-2 text-sm border border-gray-300 focus:border-maroon focus:ring-1 focus:ring-maroon" required>{{ old('perihal') }}</textarea>
        </div>

        <!-- File Scan PDF -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Upload Berkas PDF (Opsional)</label>
          <input type="file" name="file_surat" accept="application/pdf"
                 class="w-full rounded-xl px-3 py-2 text-xs border border-gray-200">
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('sigap-surat.keluar.index') }}" class="px-4 py-2 rounded-xl border text-sm text-gray-600 hover:bg-gray-50">
          Batal
        </a>
        <button type="submit" class="px-5 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 shadow-sm transition">
          Terbitkan Nomor
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function suratKeluarForm() {
  return {
    modePenomoran: 'otomatis',
    tanggal: '{{ date('Y-m-d') }}',
    slots: [],
    loadingSlot: false,
    init() {
      this.cekSlot();
    },
    cekSlot() {
      if (!this.tanggal || this.modePenomoran === 'manual') return;
      this.loadingSlot = true;
      fetch(`{{ route('sigap-surat.keluar.check-slots') }}?tanggal=${this.tanggal}`)
        .then(res => res.json())
        .then(data => {
          this.slots = data.slots || [];
          this.loadingSlot = false;
        })
        .catch(() => {
          this.loadingSlot = false;
        });
    }
  }
}
</script>

<script>
function searchableSelect(config) {
  return {
    options: config.options || [],
    selected: config.selected || '',
    search: '',
    isOpen: false,

    get selectedLabel() {
      const found = this.options.find(opt => opt.kode === this.selected);
      return found ? found.label : '';
    },

    get filteredOptions() {
      if (!this.search.trim()) {
        return this.options;
      }
      const q = this.search.toLowerCase();
      return this.options.filter(item => 
        item.kode.toLowerCase().includes(q) || item.label.toLowerCase().includes(q)
      );
    },

    toggleDropdown() {
      this.isOpen = !this.isOpen;
      if (this.isOpen) {
        this.search = '';
        this.$nextTick(() => {
          if (this.$refs.searchInput) this.$refs.searchInput.focus();
        });
      }
    },

    selectOption(item) {
      this.selected = item.kode;
      this.isOpen = false;
      this.search = '';
    }
  }
}
</script>
@endpush