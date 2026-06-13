@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
  <div>
    <h1 class="text-2xl font-extrabold text-gray-900">Master Struktur Bidang</h1>
    <p class="text-sm text-gray-600 mt-1">Kelola nama bidang sebagai hierarki tertinggi SPJ.</p>
  </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  
  <!-- Kolom Kiri: Form Tambah Bidang -->
  <div class="md:col-span-1">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
      <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Tambah Bidang Baru</h3>
      <form action="{{ route('sigap-spj.bidang.store') }}" method="POST">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bidang <span class="text-red-500">*</span></label>
          <input type="text" name="nama_bidang" required placeholder="Contoh: Bidang Inovasi" 
                 class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon">
          @error('nama_bidang')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-maroon text-white font-semibold rounded-lg text-sm hover:bg-maroon-800 transition-colors">
          Simpan Bidang
        </button>
      </form>
    </div>
  </div>

  <!-- Kolom Kanan: Tabel Daftar Bidang -->
  <div class="md:col-span-2">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Nama Bidang</th>
              <th class="px-4 py-3 text-center">Jml Sub-Kegiatan</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($bidangs as $item)
              <tr>
                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_bidang }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                    {{ $item->sub_kegiatans_count }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-2">
                    <!-- Tombol Kelola Sub-Kegiatan -->
                    <a href="{{ route('sigap-spj.bidang.sub.index', $item->id) }}" 
                    class="px-3 py-1.5 rounded border border-gray-300 text-xs font-medium hover:bg-gray-50 text-gray-700">
                    Kelola Sub
                    </a>
                    
                    <!-- Form Delete -->
                    <form action="{{ route('sigap-spj.bidang.destroy', $item->id) }}" method="POST" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn-delete px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs font-medium hover:bg-red-600 hover:text-white" data-judul="{{ $item->nama_bidang }}">
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada data bidang.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @if($bidangs->hasPages())
        <div class="px-4 py-3 border-t">
          {{ $bidangs->links() }}
        </div>
      @endif
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const judul = this.dataset.judul;

      Swal.fire({
        title: 'Hapus Bidang?',
        html: `Bidang <b>${judul}</b> dan semua data di dalamnya akan terhapus permanen!`,
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