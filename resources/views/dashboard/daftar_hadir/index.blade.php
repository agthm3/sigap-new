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
{{-- ================= FORM FILTERING & PENCARIAN CANTIK ================= --}}
<div class="mb-4 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/>
      </svg>
      <h3 class="text-sm font-semibold text-gray-700">Filter & Pencarian Kegiatan</h3>
    </div>
    @if(request()->filled('q') || request()->filled('status'))
      <a href="{{ route('sigap-daftar-hadir.index') }}" class="text-xs font-semibold text-red-600 hover:text-red-800 transition-colors flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
        </svg>
        Reset Filter
      </a>
    @endif
  </div>

  <form action="{{ route('sigap-daftar-hadir.index') }}" method="GET" class="p-4">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
      
      <!-- Input Cari Kata Kunci (Nama Kegiatan) -->
      <div class="md:col-span-6 relative">
        <label for="q" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Nama Kegiatan</label>
        <div class="relative rounded-xl shadow-sm">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.604 10.604z"/>
            </svg>
          </div>
          <input type="text" name="q" id="q" value="{{ request('q') }}" 
                 placeholder="Ketik nama kegiatan yang dicari..." 
                 class="block w-full pl-9 pr-4 py-2 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-maroon focus:ring-4 focus:ring-maroon/10 focus:bg-white transition-all">
        </div>
      </div>

      <!-- Dropdown Filter Status -->
      <div class="md:col-span-3">
        <label for="status" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Status Kegiatan</label>
        <div class="relative">
          <select name="status" id="status" 
                  class="block w-full px-3 py-2 bg-gray-50/50 border border-gray-200 rounded-xl text-sm text-gray-800 focus:outline-none focus:border-maroon focus:ring-4 focus:ring-maroon/10 focus:bg-white transition-all appearance-none cursor-pointer">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>DRAFT</option>
            <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>PROSES</option>
            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>SELESAI</option>
          </select>
          <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
            </svg>
          </div>
        </div>
      </div>

      <!-- Tombol Submit Cari -->
      <div class="md:col-span-3">
        <button type="submit" 
                class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-maroon hover:bg-maroon-800 text-white text-sm font-semibold rounded-xl shadow-sm transition-all active:scale-[0.98] cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.604 10.604z"/>
          </svg>
          Cari Data
        </button>
      </div>

    </div>
  </form>
</div>
{{-- ===================================================================== --}}
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
          <th class="px-4 py-3 text-left">Pembuat</th> <th class="px-4 py-3 text-left">Status</th>
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
              <span class="font-medium text-gray-700">{{ $item->creator->name ?? 'Sistem' }}</span>
              <div class="text-xs text-gray-500 mt-0.5">{{ $item->created_at->format('d/m/Y H:i') }} WITA</div>
            </td>
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
  {{ $kegiatans->appends(request()->query())->links() }}
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