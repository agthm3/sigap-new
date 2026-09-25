@extends('layouts.app')

@section('content')
<!-- Header & Action -->
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">NOTULENSI</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Penyusunan laporan rapat dinas: Undangan, Notula, Daftar Hadir, dan Dokumentasi.
    </p>
  </div>

  @hasanyrole('admin|verif_notulensi|employee')
    <a href="{{ route('sigap-notulensi.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 shadow-sm transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Buat Notulensi
    </a>
  @endhasanyrole
</section>

<!-- KPI Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase font-semibold text-gray-500">Total Notulensi</p>
    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $notulensis->total() }}</h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase font-semibold text-gray-500">Tersinkron Presensi</p>
    <h3 class="text-2xl font-extrabold text-blue-700 mt-1">
      {{ $notulensis->whereNotNull('daftar_hadir_kegiatan_id')->count() }}
    </h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase font-semibold text-gray-500">Presensi Manual</p>
    <h3 class="text-2xl font-extrabold text-amber-700 mt-1">
      {{ $notulensis->whereNull('daftar_hadir_kegiatan_id')->count() }}
    </h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-xs uppercase font-semibold text-gray-500">Status Selesai</p>
    <h3 class="text-2xl font-extrabold text-emerald-700 mt-1">
      {{ $notulensis->where('status', 'selesai')->count() }}
    </h3>
  </div>
</div>

<!-- Table Card -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <h2 class="font-semibold text-gray-900 text-sm">Daftar Dokumen Notulensi</h2>
    <span class="text-xs text-gray-500">Standar 4 Lembar Gabungan</span>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Nama Rapat / Agenda</th>
          <th class="px-4 py-3 text-left">No. Undangan</th>
          <th class="px-4 py-3 text-left">Waktu & Tempat</th>
          <th class="px-4 py-3 text-left">Pimpinan & Notulis</th>
          <th class="px-4 py-3 text-left">Sumber Presensi</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($notulensis as $item)
          <tr class="hover:bg-gray-50/70 transition">
            <!-- Judul & Pembuat -->
            <td class="px-4 py-3">
              <div class="font-semibold text-gray-900 leading-snug">{{ $item->judul_acara }}</div>
              <div class="text-[11px] text-gray-400 mt-0.5">Dibuat oleh: {{ $item->creator->name ?? 'Staf' }}</div>
            </td>

            <!-- Nomor Surat Undangan -->
            <td class="px-4 py-3 text-xs text-gray-600 font-mono">
              {{ $item->nomor_surat ?: '-' }}
            </td>

            <!-- Waktu & Tempat -->
            <td class="px-4 py-3">
              <div class="text-xs text-gray-800 font-medium">{{ $item->hari_tanggal }}</div>
              <div class="text-[11px] text-gray-500">{{ $item->tempat }} ({{$item->waktu_mulai_selesai }})</div>
            </td>

            <!-- Pimpinan & Notulis -->
            <td class="px-4 py-3">
              <div class="text-xs text-gray-800 font-semibold">{{ $item->pimpinan_rapat }}</div>
              <div class="text-[11px] text-gray-500">Notulis: {{ $item->notulis }}</div>
            </td>

            <!-- Sumber Presensi (Badge Status Integrasi vs Manual) -->
            <td class="px-4 py-3">
              @if($item->daftar_hadir_kegiatan_id)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 border border-blue-200 text-blue-700">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                  Sync SIGAP DH
                </span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 border border-gray-300 text-gray-700">
                  Manual
                </span>
              @endif
            </td>

            <!-- Status Laporan -->
            <td class="px-4 py-3">
              <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border
                {{ $item->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                {{ strtoupper($item->status ?? 'DRAFT') }}
              </span>
            </td>

            <!-- Aksi Tombol -->
            <td class="px-4 py-3 text-center">
              <div class="flex items-center justify-center flex-wrap gap-1.5">
                <a href="{{ route('sigap-notulensi.show', $item->id) }}"
                   class="px-2.5 py-1 rounded-md border text-xs text-gray-700 hover:bg-gray-100 transition">
                  Detail
                </a>

                <a href="{{ route('sigap-notulensi.edit', $item->id) }}"
                   class="px-2.5 py-1 rounded-md border border-gray-300 text-xs text-gray-700 hover:bg-gray-100 transition">
                  Edit
                </a>

                <a href="{{ route('sigap-notulensi.export-pdf', $item->id) }}"
                   target="_blank"
                   class="inline-flex items-center gap-1 px-3 py-1 rounded-md border border-maroon text-maroon text-xs font-semibold hover:bg-maroon hover:text-white transition">
                  <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Export PDF
                </a>

                @role('admin|verif_notulensi')
                <form action="{{ route('sigap-notulensi.destroy', $item->id) }}"
                      method="POST"
                      class="form-delete inline">
                  @csrf
                  @method('DELETE')
                  <button type="button"
                          class="btn-delete px-2.5 py-1 rounded-md border border-red-300 text-red-600 text-xs hover:bg-red-600 hover:text-white transition"
                          data-judul="{{ $item->judul_acara }}">
                    Hapus
                  </button>
                </form>
                @endrole
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
              <div class="flex flex-col items-center justify-center">
                <span class="text-3xl mb-1">📑</span>
                <p class="text-sm font-medium">Belum ada dokumen notulensi.</p>
                <p class="text-xs text-gray-400 mt-0.5">Klik tombol "Buat Notulensi" di atas untuk membuat laporan baru.</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Pagination -->
<div class="mt-4">
  {{ $notulensis->links() }}
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
        title: 'Hapus Notulensi?',
        html: `Dokumen notulensi <b>${judul}</b> akan dihapus secara permanen beserta lampirannya!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#7a2222',
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