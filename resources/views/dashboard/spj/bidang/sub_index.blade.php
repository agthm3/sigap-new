@extends('layouts.app')

@section('content')
<div class="mb-6">
  <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('sigap-spj.bidang.index') }}" class="hover:text-maroon">Master Bidang</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Sub-Kegiatan</span>
  </div>
  
  <h1 class="text-2xl font-extrabold text-gray-900">Kelola Sub-Kegiatan</h1>
  <p class="text-sm text-gray-600 mt-1">Bidang Induk: <span class="font-bold text-maroon">{{ $bidang->nama_bidang }}</span></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  
  <!-- Form Tambah Sub-Kegiatan -->
  <div class="md:col-span-1">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
      <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Tambah Sub Baru</h3>
      <form action="{{ route('sigap-spj.bidang.sub.store', $bidang->id) }}" method="POST">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Sub-Kegiatan <span class="text-red-500">*</span></label>
          <textarea name="nama_sub_kegiatan" required rows="3" placeholder="Contoh: Fasilitasi Riset Kebijakan Utama" 
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon"></textarea>
          @error('nama_sub_kegiatan')
            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-maroon text-white font-semibold rounded-lg text-sm hover:bg-maroon-800 transition-colors">
          Simpan Sub-Kegiatan
        </button>
      </form>
    </div>
  </div>

  <!-- Tabel Daftar Sub-Kegiatan -->
  <div class="md:col-span-2">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Nama Sub-Kegiatan</th>
              <th class="px-4 py-3 text-center">Jml Kegiatan</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($bidang->subKegiatans as $sub)
              <tr>
                <td class="px-4 py-3 font-medium text-gray-900 whitespace-pre-line">{{ $sub->nama_sub_kegiatan }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                    {{ $sub->kegiatans_count }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center justify-center gap-2">
                    <!-- Tombol Kelola Kegiatan level di bawahnya -->
                    <a href="{{ route('sigap-spj.bidang.kegiatan.index', $sub->id) }}" 
                    class="px-3 py-1.5 rounded border border-gray-300 text-xs font-medium hover:bg-gray-50 text-gray-700">
                    Kelola Kegiatan
                    </a>
                    
                    <form action="{{ route('sigap-spj.bidang.sub.destroy', $sub->id) }}" method="POST" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn-delete px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs font-medium hover:bg-red-600 hover:text-white" data-judul="{{ $sub->nama_sub_kegiatan }}">
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada sub-kegiatan di bidang ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
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
        title: 'Hapus Sub-Kegiatan?',
        html: `Sub-kegiatan ini beserta struktur kegiatan/gelombang di bawahnya akan dihapus permanen!`,
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