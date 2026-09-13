@extends('layouts.app')

@section('content')
<div class="mb-6">
  <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('sigap-spj.bidang.index') }}" class="hover:text-maroon">Master Bidang</a>
    <span>/</span>
    <a href="{{ route('sigap-spj.bidang.sub.index', $subKegiatan->spj_bidang_id) }}" class="hover:text-maroon">Sub-Kegiatan</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Kegiatan</span>
  </div>
  
  <h1 class="text-2xl font-extrabold text-gray-900">Kelola Kegiatan</h1>
  <p class="text-sm text-gray-600 mt-1 leading-relaxed">
    Sub-Kegiatan Induk: <span class="font-bold text-maroon break-words">{{ $subKegiatan->nama_sub_kegiatan }}</span>
  </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  
  <div class="md:col-span-1">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
      <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Tambah Kegiatan Baru</h3>
      <form action="{{ route('sigap-spj.bidang.kegiatan.store', $subKegiatan->id) }}" method="POST">
        @csrf
        <div class="mb-4">
          <div class="flex justify-between items-center mb-1">
            <label class="block text-sm font-medium text-gray-700">Nama Kegiatan <span class="text-red-500">*</span></label>
            <span class="text-[11px] text-gray-400">Maks. 500 karakter</span>
          </div>
          <textarea name="nama_kegiatan" required rows="4" maxlength="500" placeholder="Masukkan nama kegiatan..." 
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon leading-relaxed">{{ old('nama_kegiatan') }}</textarea>
          @error('nama_kegiatan')
            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
          @enderror
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-maroon text-white font-semibold rounded-lg text-sm hover:bg-maroon-800 transition-colors shadow-sm">
          Simpan Kegiatan
        </button>
      </form>
    </div>
  </div>

  <div class="md:col-span-2">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left w-3/5">Nama Kegiatan</th>
              <th class="px-4 py-3 text-center whitespace-nowrap">Jml Gelombang</th>
              <th class="px-4 py-3 text-center whitespace-nowrap">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($subKegiatan->kegiatans as $keg)
              <tr class="hover:bg-gray-50/50 transition-colors align-top">
                <td class="px-4 py-3 font-medium text-gray-900 leading-relaxed break-words">{{ $keg->nama_kegiatan }}</td>
                <td class="px-4 py-3 text-center whitespace-nowrap">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                    {{ $keg->gelombangs_count }}
                  </span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap">
                  <div class="flex items-center justify-center gap-2">
                    <a href="{{ route('sigap-spj.bidang.gelombang.index', $keg->id) }}" 
                       class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-medium hover:bg-gray-50 text-gray-700 shadow-sm transition-colors">
                      Kelola Gelombang
                    </a>
                    
                    <form action="{{ route('sigap-spj.bidang.kegiatan.destroy', $keg->id) }}" method="POST" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn-delete px-3 py-1.5 rounded-lg border border-red-500 text-red-600 text-xs font-medium hover:bg-red-600 hover:text-white shadow-sm transition-colors" 
                              data-judul="{{ htmlspecialchars($keg->nama_kegiatan, ENT_QUOTES) }}">
                        Hapus
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada kegiatan untuk sub-kegiatan ini.</td>
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
        title: 'Hapus Kegiatan?',
        html: `Apakah Anda yakin ingin menghapus kegiatan:<br><b class="text-red-700 break-words block mt-2 px-2 text-sm text-left bg-red-50 p-2 rounded border border-red-200">${judul}</b><br><span class="text-xs text-gray-500">Seluruh data gelombang dan dokumen di dalamnya akan ikut terhapus!</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
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