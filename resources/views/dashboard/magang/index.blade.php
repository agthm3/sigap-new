@extends('layouts.app')

@section('content')
<!-- Header Section -->
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">MAGANG</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pengelolaan batch magang, peserta mahasiswa, dan logbook harian.
    </p>
  </div>

  @hasanyrole('admin|verif_magang')
    <button type="button"
            onclick="openCreateModal()"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition-colors">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      + Buat Batch Magang
    </button>
  @endhasanyrole
</section>

<!-- Cards / Summary Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm font-medium text-gray-500">Total Batch Magang</p>
    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $batches->total() }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm font-medium text-gray-500">Batch Aktif</p>
    <h3 class="text-2xl font-extrabold text-maroon mt-1">
      {{ $batches->where('status', 'aktif')->count() }}
    </h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm font-medium text-gray-500">Total Peserta Magang</p>
    <h3 class="text-2xl font-extrabold text-maroon mt-1">
      {{ $totalMahasiswa ?? 0 }}
    </h3>
  </div>
</div>

<!-- Table Container -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <h2 class="font-semibold text-gray-900">Daftar Batch Magang</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Nama Batch</th>
          <th class="px-4 py-3 text-left">Periode</th>
          <th class="px-4 py-3 text-left">Kuota / Peserta</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($batches as $item)
          <tr class="hover:bg-gray-50/50">
            <td class="px-4 py-3 font-medium text-gray-900">
              <div>{{ $item->nama_batch }}</div>
              <div class="text-xs text-gray-500 font-normal">{{ $item->deskripsi ?? '-' }}</div>
            </td>
            <td class="px-4 py-3 text-gray-600">
              {{ \Carbon\Carbon::parse($item->tanggal_mulai)->isoFormat('D MMM Y') }} - 
              {{ \Carbon\Carbon::parse($item->tanggal_selesai)->isoFormat('D MMM Y') }}
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center gap-1 font-semibold text-gray-700">
                {{ $item->peserta_count ?? 0 }} / {{ $item->kuota ?? '-' }}
              </span>
            </td>
            <td class="px-4 py-3">
              @if($item->status === 'aktif')
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium border bg-emerald-50 border-emerald-200 text-emerald-700">
                  AKTIF
                </span>
              @elseif($item->status === 'mendatang')
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium border bg-blue-50 border-blue-200 text-blue-700">
                  MENDATANG
                </span>
              @else
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium border bg-gray-50 border-gray-200 text-gray-700">
                  SELESAI
                </span>
              @endif
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap items-center gap-2">
                @role('magang')
                  @if($item->status === 'aktif')
                    <form action="{{ route('magang.batch.join', $item->id) }}" method="POST">
                      @csrf
                      <button type="submit"
                              class="px-3 py-1.5 rounded-lg border border-emerald-500 text-emerald-600 text-xs font-semibold hover:bg-emerald-600 hover:text-white transition-colors">
                        Join Batch
                      </button>
                    </form>
                  @endif
                @endrole

                <a href="{{ route('magang.batch.show', $item->id) }}"
                   class="px-3 py-1.5 rounded border border-gray-300 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                  Buka
                </a>

                @hasanyrole('admin|verif_magang')
                  <form action="{{ route('magang.batch.destroy', $item->id) }}"
                        method="POST"
                        class="form-delete inline">
                    @csrf
                    @method('DELETE')
                    <button type="button"
                            class="btn-delete px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs hover:bg-red-600 hover:text-white transition-colors"
                            data-judul="{{ $item->nama_batch }}">
                      Hapus
                    </button>
                  </form>
                @endhasanyrole
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              Belum ada data Batch Magang.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  {{ $batches->links() }}
</div>

<!-- Modal Pop-Up Buat Batch Magang -->
@hasanyrole('admin|verif_magang')
<div id="modalCreateBatch" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
    <!-- Modal Header -->
    <div class="flex items-center justify-between border-b pb-3">
      <h3 class="text-lg font-bold text-gray-900">Tambah Batch Magang Baru</h3>
      <button type="button" onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>

    <!-- Modal Form -->
    <form action="{{ route('magang.batch.store') }}" method="POST" class="mt-4 space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Batch <span class="text-red-500">*</span></label>
        <input type="text" name="nama_batch" required placeholder="Contoh: Batch 1 - Semester Genap 2026"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="3" placeholder="Keterangan atau info tambahan..."
                  class="w-full rounded-lg px-3 py-2 text-sm"></textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_mulai" required class="w-full rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
          <input type="date" name="tanggal_selesai" required class="w-full rounded-lg px-3 py-2 text-sm">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Kuota Peserta <span class="text-red-500">*</span></label>
          <input type="number" name="kuota" min="1" required placeholder="10" class="w-full rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status <span class="text-red-500">*</span></label>
          <select name="status" required class="w-full rounded-lg px-3 py-2 text-sm">
            <option value="mendatang">MENDATANG</option>
            <option value="aktif" selected>AKTIF</option>
            <option value="selesai">SELESAI</option>
          </select>
        </div>
      </div>

      <!-- Modal Footer / Buttons -->
      <div class="flex justify-end gap-2 pt-4 border-t">
        <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800">
          Simpan Batch
        </button>
      </div>
    </form>
  </div>
</div>
@endhasanyrole
@endsection

@push('scripts')
<script>
function openCreateModal() {
  document.getElementById('modalCreateBatch').classList.remove('hidden');
}

function closeCreateModal() {
  document.getElementById('modalCreateBatch').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function () {
  // SwatAlert Delete Confirmation
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const judul = this.dataset.judul;

      Swal.fire({
        title: 'Hapus Batch Magang?',
        html: `Batch <b>${judul}</b> akan dihapus secara permanen!`,
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