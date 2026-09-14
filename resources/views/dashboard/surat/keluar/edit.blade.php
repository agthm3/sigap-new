@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="mb-4">
    <h1 class="text-xl font-extrabold text-gray-900">
      {{ $surat->status === 'slot_kosong' ? 'Gunakan Slot Cadangan' : 'Edit Surat Keluar' }}
    </h1>
    <p class="text-sm text-gray-600">
      Nomor Urut: <span class="font-bold text-maroon">{{ $surat->nomor_urut }}</span> | 
      Tanggal Agenda: <span class="font-semibold">{{ $surat->tanggal->format('d/m/Y') }}</span>
    </p>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
    <form action="{{ route('sigap-surat.keluar.update', $surat->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="space-y-4">
        <!-- Kode Klasifikasi -->
        <!-- Kode Klasifikasi Paten Permendagri 83/2022 (Searchable Dropdown) -->
        <div x-data="searchableSelect({
            options: [
                @foreach($daftarKlasifikasi as $kategori => $items)
                @foreach($items as $kode => $label)
                    { kode: '{{ $kode }}', label: '{{ $label }}', group: '{{ $kategori }}' },
                @endforeach
                @endforeach
            ],
            selected: '{{ old('nomor_berkas', $surat->nomor_berkas) }}'
            })" 
            class="relative">

        <label class="block text-xs font-semibold text-gray-700 mb-1">
            Kode Klasifikasi Surat (Permendagri 83/2022) *
        </label>

        <input type="hidden" name="nomor_berkas" :value="selected" required>

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

        <div x-show="isOpen" 
            x-transition
            @click.outside="isOpen = false"
            class="absolute left-0 right-0 mt-1.5 bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">

            <div class="p-2 border-b border-gray-100 bg-gray-50 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8" stroke-width="2"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-width="2"></line>
            </svg>
            <input type="text" 
                    x-ref="searchInput"
                    x-model="search" 
                    placeholder="Cari kode atau nama urusan..."
                    class="w-full text-xs bg-transparent border-0 focus:ring-0 focus:outline-none p-1 placeholder-gray-400">
            <span x-show="search" @click="search = ''" class="cursor-pointer text-xs text-gray-400 hover:text-gray-600 px-1">✕</span>
            </div>

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
                        x-text="item.group.includes('POPULER') ? '⭐ Populer' : ''"></span>
                </div>
            </template>

            <div x-show="filteredOptions.length === 0" class="py-4 text-center text-xs text-gray-400">
                Kode atau nama klasifikasi tidak ditemukan.
            </div>
            </div>
        </div>
        </div>
        <!-- Alamat Penerima -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Penerima *</label>
          <input type="text" name="alamat_penerima" value="{{ old('alamat_penerima', $surat->alamat_penerima) }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm" required>
        </div>

        <!-- Perihal -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Perihal *</label>
          <textarea name="perihal" rows="3" class="w-full rounded-xl px-3.5 py-2 text-sm" required>{{ old('perihal', $surat->perihal) }}</textarea>
        </div>

        <!-- File PDF -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Berkas PDF</label>
          @if($surat->file_surat)
            <div class="mb-2">
              <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank" class="text-xs text-maroon hover:underline">
                📄 Lihat Berkas Terunggah
              </a>
            </div>
          @endif
          <input type="file" name="file_surat" accept="application/pdf"
                 class="w-full rounded-xl px-3 py-2 text-xs border border-gray-200">
        </div>
      </div>

      <div class="mt-6 flex items-center justify-between">
        @if($surat->status === 'terbit')
          <button type="button" onclick="batalSurat()" class="text-xs text-red-600 hover:underline">
            Batalkan Nomor Ini (Void)
          </button>
        @else
          <div></div>
        @endif

        <div class="flex items-center gap-2">
          <a href="{{ route('sigap-surat.keluar.index') }}" class="px-4 py-2 rounded-xl border text-sm text-gray-600 hover:bg-gray-50">
            Batal
          </a>
          <button type="submit" class="px-5 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
            Simpan Perubahan
          </button>
        </div>
      </div>
    </form>

    @if($surat->status === 'terbit')
      <form id="formBatal" action="{{ route('sigap-surat.keluar.batal', $surat->id) }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="catatan" id="catatanBatal">
      </form>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
function batalSurat() {
  Swal.fire({
    title: 'Batalkan Nomor Surat?',
    text: 'Nomor urut ini akan diberi label BATAL di buku agenda.',
    input: 'text',
    inputPlaceholder: 'Tuliskan alasan pembatalan...',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#b91c1c',
    confirmButtonText: 'Ya, Batalkan',
    cancelButtonText: 'Kembali',
    preConfirm: (catatan) => {
      if (!catatan) {
        Swal.showValidationMessage('Alasan pembatalan wajib diisi');
      }
      return catatan;
    }
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('catatanBatal').value = result.value;
      document.getElementById('formBatal').submit();
    }
  });
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