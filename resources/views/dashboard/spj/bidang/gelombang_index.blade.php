@extends('layouts.app')

@section('content')
<div class="mb-6">
  <div class="flex flex-wrap items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('sigap-spj.bidang.index') }}" class="hover:text-maroon">Master Bidang</a>
    <span>/</span>
    <a href="{{ route('sigap-spj.bidang.sub.index', $kegiatan->subKegiatan->spj_bidang_id) }}" class="hover:text-maroon">Sub-Kegiatan</a>
    <span>/</span>
    <a href="{{ route('sigap-spj.bidang.kegiatan.index', $kegiatan->spj_sub_kegiatan_id) }}" class="hover:text-maroon">Kegiatan</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Gelombang</span>
  </div>
  
  <h1 class="text-2xl font-extrabold text-gray-900">Kelola Gelombang / Angkatan</h1>
  <p class="text-sm text-gray-600 mt-1">
    Kegiatan Induk: <span class="font-bold text-maroon whitespace-pre-line">{{ $kegiatan->nama_kegiatan }}</span>
  </p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  
  <div class="md:col-span-1">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
      <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Tambah Gelombang</h3>
      <form action="{{ route('sigap-spj.bidang.gelombang.store', $kegiatan->id) }}" method="POST">
        @csrf
        <div class="mb-4 space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Gelombang/Angkatan <span class="text-red-500">*</span></label>
            <input type="text" name="nama_gelombang" required placeholder="Contoh: Angkatan I" 
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kegiatan <span class="text-red-500">*</span></label>
            <input type="date" name="tanggal" required 
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu <span class="text-red-500">*</span></label>
            <input type="text" name="waktu" required placeholder="Contoh: 08:00 WITA - Selesai" 
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon">
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat <span class="text-red-500">*</span></label>
            <input type="text" name="tempat" required placeholder="Contoh: Hotel Claro Makassar" 
                  class="w-full rounded-lg border-gray-300 text-sm focus:border-maroon focus:ring-maroon">
          </div>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-maroon text-white font-semibold rounded-lg text-sm hover:bg-maroon-800 transition-colors">
          Simpan Gelombang
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
              <th class="px-4 py-3 text-left">Nama Gelombang / Angkatan</th>
              <th class="px-4 py-3 text-center">Wadah Dokumen</th>
              <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($kegiatan->gelombangs as $gel)
              <tr>
                <td class="px-4 py-3 font-medium text-gray-900">{{ $gel->nama_gelombang }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    Siap Diisi Operator
                  </span>
                </td>
                <td class="px-4 py-3 font-medium text-gray-900">
                {{ $gel->nama_gelombang }}
                <div class="text-[11px] text-gray-500 mt-1 font-normal leading-tight">
                  📅 {{ \Carbon\Carbon::parse($gel->tanggal)->translatedFormat('d M Y') }} <br>
                  ⏰ {{ $gel->waktu }} <br>
                  📍 {{ $gel->tempat }}
                </div>
              </td>
                <td class="px-4 py-3 text-center">
                  <form action="{{ route('sigap-spj.bidang.gelombang.destroy', $gel->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn-delete px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs font-medium hover:bg-red-600 hover:text-white" data-judul="{{ $gel->nama_gelombang }}">
                      Hapus
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada gelombang untuk kegiatan ini.</td>
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
        title: 'Hapus Gelombang?',
        html: `Gelombang <b>${judul}</b> dan seluruh file PDF di dalamnya (jika ada) akan terhapus permanen!`,
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