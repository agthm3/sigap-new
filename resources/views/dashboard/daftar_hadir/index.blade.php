@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">DAFTAR HADIR</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Daftar hadir digital dengan QR code, tanda tangan, dan export PDF resmi.
    </p>
  </div>

  @hasanyrole('admin|verif_daftarhadir')
    <a href="{{ route('sigap-daftar-hadir.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
      + Buat Kegiatan
    </a>
  @endhasanyrole
</section>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Kegiatan</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalKegiatan }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Draft</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalDraft }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Proses</p>
    <h3 class="text-2xl font-extrabold text-blue-700">{{ $totalProses }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Selesai</p>
    <h3 class="text-2xl font-extrabold text-emerald-700">{{ $totalSelesai }}</h3>
  </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50">
    <h2 class="font-semibold text-gray-900">Daftar Kegiatan</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Nama Kegiatan</th>
          <th class="px-4 py-3 text-left">Hari/Tanggal</th>
          <th class="px-4 py-3 text-left">Tempat</th>
          <th class="px-4 py-3 text-left">Waktu</th>
          <th class="px-4 py-3 text-left">Peserta</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($kegiatans as $item)
          <tr>
            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_kegiatan }}</td>
            <td class="px-4 py-3">{{ $item->hari_tanggal }}</td>
            <td class="px-4 py-3">{{ $item->tempat }}</td>
            <td class="px-4 py-3">{{ $item->waktu }}</td>
            <td class="px-4 py-3">{{ $item->peserta_count }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                {{ $item->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($item->status === 'proses' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-gray-50 border-gray-200 text-gray-700') }}">
                {{ strtoupper($item->status) }}
              </span>
            </td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <a href="{{ route('sigap-daftar-hadir.show', $item->uuid) }}"
                   class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">Buka</a>

                @hasanyrole('admin|verif_daftarhadir')
                  <a href="{{ route('sigap-daftar-hadir.edit', $item->uuid) }}"
                     class="px-3 py-1.5 rounded border border-blue-300 text-blue-700 text-xs hover:bg-blue-50">Edit</a>

                  @if($item->status === 'selesai')
                    <a href="{{ route('sigap-daftar-hadir.export-pdf', $item->uuid) }}"
                       class="px-3 py-1.5 rounded border border-emerald-500 text-emerald-700 text-xs hover:bg-emerald-50">
                      Export PDF
                    </a>
                  @endif

                  <form action="{{ route('sigap-daftar-hadir.status', $item->uuid) }}" method="POST" class="inline">
                    @csrf
                    @if($item->status === 'selesai')
                      <input type="hidden" name="status" value="proses">
                      <button type="submit" class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">
                        Tandai Proses
                      </button>
                    @else
                      <input type="hidden" name="status" value="selesai">
                      <button type="submit" class="px-3 py-1.5 rounded border border-emerald-300 text-emerald-700 text-xs hover:bg-emerald-50">
                        Tandai Selesai
                      </button>
                    @endif
                  </form>
                @endhasanyrole

                @role('admin')
                  {{-- Form hapus — id unik per baris --}}
                  <form id="form-delete-{{ $item->uuid }}"
                        action="{{ route('sigap-daftar-hadir.destroy', $item->uuid) }}"
                        method="POST"
                        class="inline">
                    @csrf
                    @method('DELETE')
                  </form>

                  <button type="button"
                          onclick="confirmDelete('{{ $item->uuid }}', '{{ addslashes($item->nama_kegiatan) }}')"
                          class="px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs hover:bg-red-600 hover:text-white transition-colors">
                    Hapus
                  </button>
                @endrole
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
              Belum ada kegiatan daftar hadir.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  {{ $kegiatans->links() }}
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(uuid, namaKegiatan) {
  Swal.fire({
    title: 'Hapus Kegiatan?',
    html: 'Kegiatan <strong>' + namaKegiatan + '</strong> beserta seluruh data pesertanya akan dihapus permanen.<br><br>Tindakan ini tidak bisa dibatalkan.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    reverseButtons: true,
  }).then(function(result) {
    if (result.isConfirmed) {
      document.getElementById('form-delete-' + uuid).submit();
    }
  });
}
</script>
@endpush