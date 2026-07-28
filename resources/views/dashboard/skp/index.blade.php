@extends('layouts.app')

@section('content')
<div x-data="skpData()" class="space-y-6">

  {{-- Header --}}
  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        SIGAP <span class="text-maroon">SKP Umum</span>
      </h1>
      <p class="text-sm text-gray-600 mt-0.5">
        Laporan evidence dan dokumentasi kegiatan pegawai.
      </p>
    </div>

    <div class="flex items-center gap-2">
    <a href="{{ route('sigap-skp.upload-mandiri') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3" stroke-width="2"/></svg>
        Upload Mandiri
    </a>

    @hasanyrole('admin|verif_skp')
        <button @click="openModal = true"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition-colors shadow-sm">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
        Tambah SKP
        </button>
    @endhasanyrole
    </div>
  </section>

  {{-- Stats --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-sm text-gray-500">Total Kegiatan SKP</p>
      <h3 class="text-2xl font-extrabold text-gray-900">{{ $skps->total() ?? 0 }}</h3> 
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-sm text-gray-500">Total Dokumentasi Foto</p>
      <h3 class="text-2xl font-extrabold text-maroon">
        {{ $total_dokumentasi ?? 0 }}
      </h3>
    </div>
  </div>

  {{-- Filter Section --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <form action="{{ route('sigap-skp.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
      
      <div class="flex-1">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Cari nama kegiatan..." 
               class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
      </div>
      
      <div class="w-full sm:w-64">
        <select name="pegawai_id" class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
          <option value="">-- Semua Pegawai --</option>
          @foreach($employees as $emp)
            <option value="{{ $emp->id }}" {{ request('pegawai_id') == $emp->id ? 'selected' : '' }}>
              {{ $emp->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="w-full sm:w-48">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
               class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
      </div>

      <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition-colors">
        Filter
      </button>
      
      @if(request()->anyFilled(['search', 'pegawai_id', 'tanggal']))
        <a href="{{ route('sigap-skp.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 text-center">
          Reset
        </a>
      @endif
    </form>
  </div>

  {{-- GRID CARD SKP --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($skps as $item)
      @php
        $firstFoto = $item->fotos->first();
        $thumbUrl = $firstFoto ? asset('storage/' . $firstFoto->file_path) : null;
        $showUrl = route('sigap-skp.show', $item->slug);
      @endphp
      <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
        <div>
          {{-- Thumbnail Foto --}}
          <div class="relative h-48 w-full bg-gray-100 border-b overflow-hidden">
            @if($thumbUrl)
              <img src="{{ $thumbUrl }}" alt="{{ $item->judul_kegiatan }}" class="w-full h-full object-cover">
            @else
              <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <svg class="w-10 h-10 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21" stroke-width="2"/></svg>
                <span class="text-xs">Tidak ada gambar</span>
              </div>
            @endif

            {{-- Badges --}}
            <div class="absolute top-3 right-3 flex gap-1.5">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-black/60 text-white backdrop-blur-sm">
                📷 {{ $item->fotos_count }} Foto
              </span>
            </div>

            @if($item->agenda_id)
              <div class="absolute top-3 left-3">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-maroon text-white shadow">
                  Agenda
                </span>
              </div>
            @endif
          </div>

          {{-- Konten Card --}}
          <div class="p-4 space-y-3">
            <div>
              <p class="text-xs font-semibold text-gray-500 mb-0.5">
                📅 {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
              </p>
              <h3 class="font-bold text-gray-900 text-base line-clamp-2 hover:text-maroon transition-colors">
                <a href="{{ $showUrl }}">{{ $item->judul_kegiatan }}</a>
              </h3>
            </div>

            {{-- Pegawai Terlibat --}}
            <div>
              <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Pegawai Terlibat:</p>
              <div class="flex flex-wrap gap-1">
                @forelse($item->pegawais as $pegawai)
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-800 border border-gray-200">
                    👤 {{ $pegawai->name }}
                  </span>
                @empty
                  <span class="text-xs text-gray-400 italic">Tidak ada pegawai</span>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        {{-- Footer Card & Action Buttons --}}
        <div class="p-4 pt-0 border-t border-gray-100 mt-3 flex items-center justify-between gap-2">
          {{-- TOMBOL SALIN LINK --}}
          <button type="button" 
                  @click="copyToClipboard('{{ $showUrl }}')" 
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" stroke-width="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" stroke-width="2"/></svg>
            Salin Link
          </button>

          <div class="flex items-center gap-1.5">
            <a href="{{ $showUrl }}" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors">
              Buka
            </a>

            @hasanyrole('admin|verif_skp')
              <form action="{{ route('sigap-skp.destroy', $item->slug) }}" method="POST" class="inline form-delete">
                @csrf
                @method('DELETE')
                <button type="button" data-judul="{{ $item->judul_kegiatan }}"
                        class="btn-delete p-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-600 hover:text-white transition-colors">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </form>
            @endhasanyrole
          </div>
        </div>
      </div>
    @empty
      <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-12 text-center text-gray-500">
        Belum ada data laporan SKP.
      </div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $skps->links() ?? '' }}
  </div>

  {{-- MODAL TAMBAH SKP --}}
  <div x-show="openModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
    <div x-show="openModal" x-transition class="fixed inset-0 bg-gray-900/50 transition-opacity" @click="openModal = false"></div>

    <div class="flex min-h-full items-center justify-center p-4">
      <div x-show="openModal" x-transition class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:w-full sm:max-w-2xl">
        
        <form action="{{ route('sigap-skp.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="bg-white p-6 space-y-4">
            <h3 class="text-lg font-bold text-gray-900 border-b pb-2">Tambah Laporan SKP</h3>
            
            <!-- Pilihan Sumber Kegiatan -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Sumber Kegiatan</label>
              <div class="flex gap-4">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="source_mode" value="agenda" x-model="sourceMode" @change="resetFields" class="text-maroon">
                  <span class="ml-2 text-sm">Dari SIGAP AGENDA</span>
                </label>
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" name="source_mode" value="manual" x-model="sourceMode" @change="resetFields" class="text-maroon">
                  <span class="ml-2 text-sm">Ketik Manual</span>
                </label>
              </div>
            </div>

            <!-- Select Agenda -->
            <div x-show="sourceMode === 'agenda'" class="p-3 bg-gray-50 rounded-lg border border-gray-200">
              <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Agenda <span class="text-red-500">*</span></label>
              <select name="agenda_id" x-model="selectedAgendaId" @change="fillFromAgenda" class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
                <option value="">-- Pilih Kegiatan Agenda --</option>
                @foreach($agendas as $agenda)
                  <option value="{{ $agenda['id'] }}" 
                          data-title="{{ $agenda['unit_title'] }}" 
                          data-date="{{ $agenda['date'] }}"
                          data-assignees="{{ $agenda['assignees'] }}">
                    {{ \Carbon\Carbon::parse($agenda['date'])->format('d/m/Y') }} - {{ $agenda['unit_title'] }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Searchable Multi-Select Pegawai -->
            <div x-data="employeeSelect(@js($employees))" 
                 x-ref="empSelectComp"
                 @click.outside="open = false" 
                 class="relative">
              
              <label class="block text-sm font-semibold text-gray-700 mb-1">
                Pegawai yang Terlibat <span class="text-red-500">*</span>
              </label>
              
              <div class="min-h-[42px] p-1.5 border border-gray-300 rounded-lg bg-white flex flex-wrap items-center gap-1.5 cursor-text" @click="openDropdown()">
                <template x-for="emp in selectedEmployees" :key="emp.id">
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-maroon/10 text-maroon text-xs font-semibold border border-maroon/20">
                    <span x-text="emp.name"></span>
                    <button type="button" @click.stop="removeEmployee(emp.id)" class="text-maroon/70 hover:text-maroon">&times;</button>
                    <input type="hidden" name="pegawai_ids[]" :value="emp.id">
                  </span>
                </template>

                <input x-ref="searchInput" type="text" x-model="search" @focus="open = true" @input="open = true"
                       placeholder="Cari & pilih pegawai..." class="flex-1 min-w-[120px] outline-none border-none !p-1 text-sm bg-transparent !border-0 focus:!ring-0">
              </div>

              <div x-show="open" x-cloak class="absolute z-[100] left-0 right-0 mt-1 max-h-48 overflow-y-auto bg-white border border-gray-200 rounded-lg shadow-xl scrollbar-thin py-1">
                <template x-for="emp in filteredEmployees" :key="emp.id">
                  <div @click="toggleEmployee(emp)" class="px-3 py-2 text-sm text-gray-800 hover:bg-maroon/10 hover:text-maroon cursor-pointer flex items-center justify-between" :class="{'bg-gray-50 font-semibold text-maroon': isSelected(emp.id)}">
                    <span x-text="emp.name"></span>
                    <svg x-show="isSelected(emp.id)" class="w-4 h-4 text-maroon" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline stroke-width="3" points="20 6 9 17 4 12"></polyline></svg>
                  </div>
                </template>
                <div x-show="filteredEmployees.length === 0" class="px-3 py-2 text-xs text-gray-400 text-center italic">
                  Pegawai tidak ditemukan.
                </div>
              </div>
            </div>

            <!-- Field Judul & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="judul_kegiatan" x-model="judulKegiatan" required :readonly="sourceMode === 'agenda'"
                       class="w-full rounded-lg px-3 py-2 text-sm border-gray-300" :class="sourceMode === 'agenda' ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'">
              </div>
              <div class="col-span-1">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal" x-model="tanggalKegiatan" required :readonly="sourceMode === 'agenda'"
                       class="w-full rounded-lg px-3 py-2 text-sm border-gray-300" :class="sourceMode === 'agenda' ? 'bg-gray-100 cursor-not-allowed' : 'bg-white'">
              </div>
            </div>

            <!-- Upload Dokumentasi Foto dengan Indikator Kompresi -->
        <!-- Upload Dokumentasi Foto Multi-File dengan Live Preview -->
            <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                Upload Foto Dokumentasi <span class="text-red-500">*</span>
            </label>
            
            <input x-ref="fileInput"
                    type="file" 
                    name="dokumentasi[]" 
                    multiple 
                    required 
                    accept="image/*" 
                    @change="handleImageUpload($event)"
                    class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-maroon focus:border-maroon">

            <!-- Indikator Kompresi Client-side -->
            <p x-show="isCompressing" class="text-xs text-amber-600 font-semibold mt-1.5 animate-pulse flex items-center gap-1" style="display: none;">
                <svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"/><path d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"/></svg>
                Mengompresi beberapa foto otomatis... Silakan tunggu sejenak.
            </p>

            <!-- Live Preview Grid Foto yang Siap Diunggah -->
            <div x-show="imagePreviews.length > 0" class="mt-3 space-y-1.5" style="display: none;">
                <p class="text-xs font-semibold text-gray-600">Pratinjau Foto Terpilih (<span x-text="imagePreviews.length"></span> foto):</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5 max-h-48 overflow-y-auto p-2 border border-gray-200 bg-gray-50 rounded-xl scrollbar-thin">
                <template x-for="(img, index) in imagePreviews" :key="index">
                    <div class="relative group aspect-square rounded-lg overflow-hidden border border-gray-300 bg-white shadow-sm">
                    <img :src="img.url" class="w-full h-full object-cover">
                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 bg-black/60 text-white text-[9px] rounded font-mono" x-text="img.size"></span>
                    <button type="button" 
                            @click="removeSelectedImage(index)" 
                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-1 opacity-90 hover:opacity-100 hover:scale-110 transition-all shadow" 
                            title="Hapus foto ini">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                    </div>
                </template>
                </div>
            </div>
            </div>

          </div>
          
          <div class="bg-gray-50 px-6 py-3 sm:flex sm:flex-row-reverse rounded-b-2xl border-t border-gray-200">
            <button type="submit" :disabled="isCompressing" class="inline-flex w-full justify-center rounded-lg bg-maroon px-5 py-2 text-sm font-semibold text-white hover:bg-maroon-800 disabled:opacity-50 sm:ml-3 sm:w-auto">Simpan SKP</button>
            <button type="button" @click="openModal = false" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-5 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function skpData() {
  return {
    openModal: false,
    sourceMode: 'agenda',
    selectedAgendaId: '',
    judulKegiatan: '',
    tanggalKegiatan: '',
    isCompressing: false,
    
    resetFields() {
      this.selectedAgendaId = '';
      this.judulKegiatan = '';
      this.tanggalKegiatan = '';
      const empComp = this.getEmpComp();
      if(empComp) empComp.selectedIds = [];
    },

    getEmpComp() {
      const el = document.querySelector('[x-ref="empSelectComp"]');
      return el ? Alpine.$data(el) : null;
    },
    
    fillFromAgenda(e) {
      if(this.sourceMode !== 'agenda') return;
      
      const select = e.target;
      const option = select.options[select.selectedIndex];
      
      if(option && option.value) {
        this.judulKegiatan = option.getAttribute('data-title');
        this.tanggalKegiatan = option.getAttribute('data-date');

        const assigneesStr = (option.getAttribute('data-assignees') || '').toLowerCase();
        const empComp = this.getEmpComp();

        if (empComp && assigneesStr) {
          const matchedIds = empComp.employees
            .filter(emp => assigneesStr.includes(emp.name.toLowerCase()))
            .map(emp => emp.id);

          empComp.selectedIds = matchedIds;
        }
      } else {
        this.resetFields();
      }
    },

    // FITUR KOMPRESI CLIENT-SIDE VIA CANVAS
    async handleImageUpload(e) {
      const files = Array.from(e.target.files);
      if (files.length === 0) return;

      this.isCompressing = true;
      const dataTransfer = new DataTransfer();

      for (let file of files) {
        if (file.type.startsWith('image/')) {
          try {
            // Kompresi ke max-width 1200px dan quality 0.75
            const compressedBlob = await this.compressImageCanvas(file, 1200, 0.75);
            const compressedFile = new File([compressedBlob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
              type: 'image/jpeg',
              lastModified: Date.now()
            });
            dataTransfer.items.add(compressedFile);
          } catch (err) {
            dataTransfer.items.add(file); // Fallback jika browser gagal mengompres
          }
        } else {
          dataTransfer.items.add(file);
        }
      }

      e.target.files = dataTransfer.files;
      this.isCompressing = false;
    },

    compressImageCanvas(file, maxWidth, quality) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (event) => {
          const img = new Image();
          img.src = event.target.result;
          img.onload = () => {
            let width = img.width;
            let height = img.height;

            if (width > maxWidth) {
              height = Math.round((height * maxWidth) / width);
              width = maxWidth;
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob((blob) => {
              resolve(blob);
            }, 'image/jpeg', quality);
          };
          img.onerror = (error) => reject(error);
        };
        reader.onerror = (error) => reject(error);
      });
    },

    copyToClipboard(url) {
      navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Link Disalin!',
          text: 'URL detail SKP telah tersalin ke clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      });
    }
  }
}

function employeeSelect(employeesList) {
  return {
    open: false,
    search: '',
    employees: employeesList || [],
    selectedIds: [],

    openDropdown() {
      this.open = true;
      this.$nextTick(() => { this.$refs.searchInput.focus(); });
    },

    get selectedEmployees() {
      return this.employees.filter(e => this.selectedIds.includes(e.id));
    },

    get filteredEmployees() {
      if (!this.search || this.search.trim() === '') return this.employees;
      return this.employees.filter(e => e.name.toLowerCase().includes(this.search.toLowerCase()));
    },

    isSelected(id) { return this.selectedIds.includes(id); },

    toggleEmployee(emp) {
      if (this.isSelected(emp.id)) {
        this.removeEmployee(emp.id);
      } else {
        this.selectedIds.push(emp.id);
      }
      this.search = '';
      this.open = true;
    },

    removeEmployee(id) {
      this.selectedIds = this.selectedIds.filter(i => i !== id);
    }
  }
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const judul = this.dataset.judul;

      Swal.fire({
        title: 'Hapus SKP?',
        html: `Laporan kegiatan <b>${judul}</b> beserta dokumentasinya akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush