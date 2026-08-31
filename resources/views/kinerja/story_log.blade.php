@extends('layouts.app')

@section('content')
<div x-data="storyLogManager()">
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Riwayat SIGAP Story</h1>
        <p class="text-sm text-gray-600 mt-1">Daftar semua infografis story yang pernah Anda buat sebelumnya.</p>
      </div>
      <div class="flex items-center gap-3">
        <!-- Tombol Hapus Terpilih (Muncul jika ada item yang dicentang) -->
        <button type="button" 
                x-show="selectedIds.length > 0" 
                x-cloak
                @click="confirmBulkDelete()" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition shadow-sm text-sm font-semibold">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          Hapus Terpilih (<span x-text="selectedIds.length"></span>)
        </button>

        <a href="{{ route('sigap-story.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition text-sm font-semibold shadow-sm">
          + Buat Story Baru
        </a>
      </div>
    </div>

    <!-- Opsi Select All -->
    @if($logs->count() > 0)
    <div class="mt-6 flex items-center justify-between bg-white px-4 py-3 rounded-xl border border-gray-200">
      <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-semibold text-gray-700 select-none">
        <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected()" class="w-4 h-4 rounded text-maroon focus:ring-maroon border-gray-300">
        Pilih Semua di Halaman Ini
      </label>
      <span class="text-xs text-gray-500" x-text="selectedIds.length + ' dipilih dari {{ $logs->count() }} item'"></span>
    </div>
    @endif
  </section>

  <section class="max-w-7xl mx-auto px-4">
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($logs as $log)
          <article class="bg-white border border-gray-200 rounded-2xl overflow-hidden flex flex-col shadow-sm relative group transition-all hover:shadow-md">
              
              <!-- Checkbox Item -->
              <div class="absolute top-3 left-3 z-20">
                <input type="checkbox" 
                       value="{{ $log->id }}" 
                       x-model="selectedIds" 
                       class="w-5 h-5 rounded text-maroon focus:ring-maroon border-gray-300 shadow-md cursor-pointer bg-white/90">
              </div>

              <!-- Preview Gambar Story -->
              <div class="relative w-full aspect-[9/16] bg-gray-100 border-b overflow-hidden">
                  <img src="{{ Storage::url($log->image_path) }}" alt="{{ $log->title }}" class="w-full h-full object-contain">
              </div>

              <!-- Detail Card -->
              <div class="p-4 flex-1 flex flex-col justify-between gap-3">
                  <div>
                    <h3 class="font-bold text-gray-900 leading-snug line-clamp-2 text-sm" title="{{ $log->title }}">
                        {{ $log->title }}
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $log->created_at->translatedFormat('d F Y - H:i') }}</p>
                  </div>

                  <div class="flex flex-col gap-2 pt-2 border-t border-gray-100">
                      <div class="flex gap-2">
                        <a href="{{ Storage::url($log->image_path) }}" download class="flex-1 text-center bg-gray-900 text-white text-xs py-2 rounded-lg hover:bg-black font-semibold transition">
                          Download
                        </a>
                        <a href="{{ Storage::url($log->image_path) }}" target="_blank" class="flex-1 text-center border border-gray-300 text-gray-700 text-xs py-2 rounded-lg hover:bg-gray-50 font-semibold transition">
                          Lihat
                        </a>
                      </div>
                      
                      <!-- Tombol Hapus Satuan -->
                      <button type="button" 
                              @click="confirmSingleDelete({{ $log->id }}, @js($log->title))" 
                              class="w-full text-center text-xs py-1.5 rounded-lg text-red-600 hover:bg-red-50 font-semibold border border-red-200 transition">
                        Hapus Story
                      </button>
                  </div>
              </div>
          </article>
        @empty
          <div class="col-span-full bg-white border border-gray-200 rounded-2xl p-12 text-center text-gray-500">
              Belum ada riwayat SIGAP Story yang tersimpan.
          </div>
        @endforelse
    </div>
    
    @if($logs->hasPages())
      <div class="mt-8 mb-6">
          {{ $logs->links() }}
      </div>
    @endif
  </section>

  <!-- Form Single Delete (Hidden) -->
  <form id="formSingleDelete" method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>

  <!-- Form Bulk Delete (Hidden) -->
  <form id="formBulkDelete" method="POST" action="{{ route('sigap-story.bulk-destroy') }}" class="hidden">
    @csrf
    <template x-for="id in selectedIds" :key="id">
      <input type="hidden" name="ids[]" :value="id">
    </template>
  </form>
</div>
@endsection

@push('scripts')
<script>
function storyLogManager() {
  return {
    selectedIds: [],
    pageItemIds: @json($logs->pluck('id')->all()),

    isAllSelected() {
      if (this.pageItemIds.length === 0) return false;
      return this.pageItemIds.every(id => this.selectedIds.includes(id));
    },

    toggleSelectAll(e) {
      if (e.target.checked) {
        this.selectedIds = Array.from(new Set([...this.selectedIds, ...this.pageItemIds]));
      } else {
        this.selectedIds = this.selectedIds.filter(id => !this.pageItemIds.includes(id));
      }
    },

    confirmSingleDelete(id, title) {
      Swal.fire({
        title: 'Hapus Story ini?',
        html: `<div class="text-left text-sm text-gray-600">Judul: <b>${title || 'Tanpa Judul'}</b><br><span class="text-xs text-red-500">File gambar akan dihapus permanen.</span></div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          const form = document.getElementById('formSingleDelete');
          form.action = "{{ url('/sigap-story') }}/" + id;
          form.submit();
        }
      });
    },

    confirmBulkDelete() {
      Swal.fire({
        title: `Hapus ${this.selectedIds.length} Story terpilih?`,
        text: 'Semua file gambar yang dipilih akan dihapus secara permanen.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          document.getElementById('formBulkDelete').submit();
        }
      });
    }
  }
}
</script>
@endpush