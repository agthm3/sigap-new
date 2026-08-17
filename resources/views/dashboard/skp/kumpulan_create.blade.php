@extends('layouts.app')

@section('content')
<div x-data="kumpulanForm()" class="max-w-5xl mx-auto space-y-6">

  {{-- Header & Filter --}}
  <div class="flex items-center justify-between border-b pb-4">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">Buat Kumpulan <span class="text-maroon">Rekap SKP & PPD</span></h1>
      <p class="text-xs text-gray-500 mt-0.5">Pilih kegiatan bulan ini yang ingin digabungkan dalam satu link rekap.</p>
    </div>
    <a href="{{ route('sigap-skp.kumpulan.index') }}" class="px-3 py-1.5 rounded-lg border text-xs font-semibold text-gray-600 hover:bg-gray-50">Batal</a>
  </div>

  <form method="GET" action="{{ route('sigap-skp.kumpulan.create') }}" class="bg-gray-50 p-4 rounded-2xl border border-gray-200 grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
      <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Bulan & Tahun</label>
      <input type="month" name="bulan" value="{{ $bulanTahun }}" class="w-full text-xs rounded-xl border-gray-300 p-2 focus:ring-maroon focus:border-maroon">
    </div>
    <div>
      <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Kategori</label>
      <select x-model="kategoriOption" @change="handleKategoriSelect($event)" class="w-full text-xs rounded-xl border-gray-300 p-2 focus:ring-maroon focus:border-maroon">
        <option value="DIREKTIF (TUGAS TAMBAHAN)">DIREKTIF (TUGAS TAMBAHAN)</option>
        <option value="TUPOKSI">TUPOKSI</option>
        <option value="MANUAL">✏️ Ketik Manual Lainnya...</option>
      </select>
      <div x-show="isManualKategori" class="mt-2" x-cloak>
        <input type="text" x-model="kategoriManual" @input="updateKategoriManual" placeholder="Tuliskan nama kategori khusus..." class="w-full text-xs rounded-xl border-amber-400 bg-amber-50 p-2 text-amber-900 font-semibold focus:ring-amber-500 focus:border-amber-500">
      </div>
      <input type="hidden" name="kategori" :value="kategori">
    </div>
    <div class="flex items-end">
      <button type="submit" class="w-full py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
        🔍 Tampilkan Kegiatan
      </button>
    </div>
  </form>

  <form @submit.prevent="submitKumpulan" class="space-y-6">
    
    <div>
      <label class="block text-xs font-bold text-gray-700 mb-1">Judul Kumpulan Rekap <span class="text-red-500">*</span></label>
      <input type="text" x-model="judulKumpulan" required class="w-full text-sm rounded-xl border-gray-300 p-2.5 font-semibold focus:ring-maroon focus:border-maroon">
    </div>

    {{-- BAGIAN 1: SIGAP SKP --}}
    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <label class="block text-xs font-bold text-gray-700">
          📁 Kinerja Laporan SKP (<span x-text="selectedSkpIds.length" class="text-maroon font-extrabold"></span> terpilih)
        </label>
        <button type="button" @click="toggleSelectAllSkp()" class="text-xs text-maroon font-semibold hover:underline">Pilih Semua SKP</button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[400px] overflow-y-auto p-2.5 border border-gray-200 rounded-2xl bg-white shadow-inner scrollbar-thin">
        @forelse($skpList as $skp)
          @php 
            $foto = $skp->fotos->first();
            $assignedKats = $usedSkpCategories[$skp->id] ?? [];
            $hasUsed = count($assignedKats) > 0;
            $katLabel = implode(', ', array_unique($assignedKats));
          @endphp
          
          <div @click="toggleSkp({{ $skp->id }})" 
               class="relative rounded-2xl border-2 p-2.5 cursor-pointer transition-all flex flex-col justify-between select-none
               {{ $hasUsed ? 'bg-amber-50/30' : 'bg-white' }}"
               :class="selectedSkpIds.includes({{ $skp->id }}) 
                  ? 'border-maroon bg-maroon/5 ring-2 ring-maroon/20 shadow-md' 
                  : '{{ $hasUsed ? 'border-amber-300 hover:border-amber-400' : 'border-gray-200 hover:border-gray-300' }}'">
            
            <div class="aspect-video w-full rounded-xl bg-gray-100 overflow-hidden mb-2 relative border border-gray-200">
              @if($skp->tipe_evidence === 'pdf')
                <div class="flex flex-col items-center justify-center h-full text-red-600 bg-red-50">
                  <span class="text-xl">📄</span>
                  <span class="text-[9px] font-bold mt-0.5">PDF DOC</span>
                </div>
              @elseif($foto)
                <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-full object-cover">
              @else
                <div class="flex items-center justify-center h-full text-[10px] text-gray-400">Tidak ada gambar</div>
              @endif

              {{-- Checkbox Centang Terpilih --}}
              <div class="absolute top-1.5 right-1.5 z-10">
                <span x-show="selectedSkpIds.includes({{ $skp->id }})" class="w-5 h-5 rounded-full bg-maroon text-white flex items-center justify-center font-bold text-xs shadow-lg">✓</span>
              </div>

              {{-- Badge Status Terpakai di Kategori Lain --}}
              @if($hasUsed)
                <div class="absolute bottom-1 left-1 max-w-[90%]">
                  <span class="px-1.5 py-0.5 rounded bg-amber-500/90 text-white text-[8px] font-extrabold backdrop-blur-sm shadow truncate block" title="Sudah masuk: {{ $katLabel }}">
                    Sudah: {{ Str::limit($katLabel, 18) }}
                  </span>
                </div>
              @endif
            </div>

            <div>
              <div class="flex items-center justify-between mb-0.5">
                <span class="text-[10px] text-gray-500 font-semibold">📅 {{ \Carbon\Carbon::parse($skp->tanggal)->format('d/m/Y') }}</span>
                @if($hasUsed)
                  <span class="text-[9px] font-bold text-amber-600">✓ Terarsip</span>
                @endif
              </div>
              <h4 class="text-[11px] font-bold text-gray-800 line-clamp-2 leading-snug">{{ $skp->judul_kegiatan }}</h4>
            </div>
          </div>
        @empty
          <div class="col-span-full p-6 text-center text-xs text-gray-400 italic">Tidak ada laporan SKP di bulan ini.</div>
        @endforelse
      </div>
    </div>

    {{-- BAGIAN 2: SIGAP PPD --}}
    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <label class="block text-xs font-bold text-gray-700">
          ✈️ Perjalanan Dinas / PPD (<span x-text="selectedPpdIds.length" class="text-blue-600 font-extrabold"></span> terpilih)
        </label>
        <button type="button" @click="toggleSelectAllPpd()" class="text-xs text-blue-600 font-semibold hover:underline">Pilih Semua PPD</button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto p-2.5 border border-gray-200 rounded-2xl bg-white shadow-inner scrollbar-thin">
        @forelse($ppdList as $ppd)
          @php 
            $firstLembar = $ppd->lembar->first();
            $fotoPpd = $firstLembar ? $firstLembar->fotos->first() : null;
            $assignedPpdKats = $usedPpdCategories[$ppd->id] ?? [];
            $hasUsedPpd = count($assignedPpdKats) > 0;
            $katPpdLabel = implode(', ', array_unique($assignedPpdKats));
          @endphp

          <div @click="togglePpd({{ $ppd->id }})" 
               class="relative rounded-2xl border-2 p-3 cursor-pointer transition-all flex flex-col justify-between select-none
               {{ $hasUsedPpd ? 'bg-amber-50/30' : 'bg-white' }}" 
               :class="selectedPpdIds.includes({{ $ppd->id }}) 
                  ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20 shadow-md' 
                  : '{{ $hasUsedPpd ? 'border-amber-300 hover:border-amber-400' : 'border-gray-200 hover:border-gray-300' }}'">
            
            <div class="flex gap-3">
              <div class="w-20 h-20 shrink-0 rounded-xl bg-gray-100 overflow-hidden relative border border-gray-200">
                @if($fotoPpd) 
                  <img src="{{ asset('storage/' . $fotoPpd->foto_path) }}" class="w-full h-full object-cover"> 
                @else 
                  <div class="flex items-center justify-center h-full text-[9px] text-gray-400 text-center px-1">Tanpa Foto</div> 
                @endif

                <div class="absolute top-1 right-1">
                  <span x-show="selectedPpdIds.includes({{ $ppd->id }})" class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-lg">✓</span>
                </div>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1 flex-wrap">
                  <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 text-[9px] font-bold uppercase">{{ $ppd->kategori }}</span>
                  @if($hasUsedPpd)
                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 text-[9px] font-bold border border-amber-200 truncate max-w-full" title="Sudah masuk: {{ $katPpdLabel }}">
                      ✓ Masuk: {{ Str::limit($katPpdLabel, 15) }}
                    </span>
                  @endif
                </div>
                
                <h4 class="text-xs font-bold text-gray-800 line-clamp-2 mt-1 leading-snug">{{ $ppd->judul }}</h4>
                <p class="text-[10px] text-gray-500 font-medium mt-1 truncate">📍 {{ $ppd->tempat }}</p>
                <p class="text-[10px] text-gray-500">📅 {{ $ppd->hari_tanggal }}</p>
              </div>
            </div>
          </div>
        @empty
          <div class="col-span-full p-6 text-center text-xs text-gray-400 italic">Tidak ada kegiatan PPD Anda di bulan ini.</div>
        @endforelse
      </div>
    </div>

    <button type="submit" :disabled="isLoading || (selectedSkpIds.length === 0 && selectedPpdIds.length === 0)" class="w-full py-3 bg-emerald-600 text-white font-bold text-xs rounded-xl hover:bg-emerald-700 disabled:opacity-40 transition-all shadow-md flex items-center justify-center gap-2">
      <span x-show="!isLoading">Buat Kumpulan Link & Salin 🚀</span>
      <span x-show="isLoading" class="animate-pulse">Menyimpan Kumpulan...</span>
    </button>
  </form>
</div>

@push('scripts')
<script>
function kumpulanForm() {
  const initialKat = @js($kategori) || 'DIREKTIF (TUGAS TAMBAHAN)';
  const isStandar = ['DIREKTIF (TUGAS TAMBAHAN)', 'TUPOKSI'].includes(initialKat);

  return {
    kategori: initialKat,
    kategoriOption: isStandar ? initialKat : 'MANUAL',
    kategoriManual: isStandar ? '' : initialKat,
    isManualKategori: !isStandar,
    bulanTahun: '{{ $bulanTahun }}',
    judulKumpulan: '',
    selectedSkpIds: [],
    selectedPpdIds: [],
    allSkpIds: @js($skpList->pluck('id')),
    allPpdIds: @js($ppdList->pluck('id')),
    isLoading: false,

    init() { this.updateJudulAuto(); },

    handleKategoriSelect(e) {
      if (e.target.value === 'MANUAL') {
        this.isManualKategori = true;
        this.kategori = this.kategoriManual || 'KATEGORI KHUSUS';
      } else {
        this.isManualKategori = false;
        this.kategori = e.target.value;
      }
      this.updateJudulAuto();
    },

    updateKategoriManual() {
      this.kategori = this.kategoriManual;
      this.updateJudulAuto();
    },

    updateJudulAuto() {
      this.judulKumpulan = 'KUMPULAN SKP ' + (this.kategori || 'KATEGORI').toUpperCase();
    },

    toggleSkp(id) {
      this.selectedSkpIds.includes(id) ? this.selectedSkpIds = this.selectedSkpIds.filter(i => i !== id) : this.selectedSkpIds.push(id);
    },
    
    togglePpd(id) {
      this.selectedPpdIds.includes(id) ? this.selectedPpdIds = this.selectedPpdIds.filter(i => i !== id) : this.selectedPpdIds.push(id);
    },

    toggleSelectAllSkp() {
      this.selectedSkpIds = this.selectedSkpIds.length === this.allSkpIds.length ? [] : [...this.allSkpIds];
    },

    toggleSelectAllPpd() {
      this.selectedPpdIds = this.selectedPpdIds.length === this.allPpdIds.length ? [] : [...this.allPpdIds];
    },

    async submitKumpulan() {
      if (this.selectedSkpIds.length === 0 && this.selectedPpdIds.length === 0) {
        Swal.fire('Perhatian', 'Pilih minimal 1 kegiatan SKP atau PPD.', 'warning');
        return;
      }

      this.isLoading = true;
      try {
        const res = await fetch("{{ route('sigap-skp.kumpulan.store') }}", {
          method: "POST",
          headers: { "Content-Type": "application/json", "Accept": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
          body: JSON.stringify({
            kategori: this.kategori, bulan_tahun: this.bulanTahun, judul_kumpulan: this.judulKumpulan,
            skp_ids: this.selectedSkpIds, ppd_ids: this.selectedPpdIds
          })
        });

        const data = await res.json();
        if (res.ok && data.status === 'success') {
          if (navigator.clipboard) await navigator.clipboard.writeText(data.public_url);
          Swal.fire({
            icon: 'success', title: 'Berhasil Dibuat!',
            html: `Link rekap otomatis disalin:<br><br><input type="text" readonly value="${data.public_url}" class="w-full text-xs p-2 border rounded bg-gray-50 text-center select-all">`,
            confirmButtonText: 'Kembali ke Daftar', confirmButtonColor: '#7a2222',
          }).then(() => window.location.href = data.redirect);
        } else {
          Swal.fire('Gagal', data.message || 'Gagal menyimpan.', 'error');
        }
      } catch (err) {
        Swal.fire('Error', 'Kesalahan sistem saat menyimpan.', 'error');
      } finally { this.isLoading = false; }
    }
  }
}
</script>
@endpush
@endsection