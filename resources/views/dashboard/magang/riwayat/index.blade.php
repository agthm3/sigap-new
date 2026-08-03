@extends('layouts.app')

@section('content')
<!-- Header Section -->
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">Riwayat Magang</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      RIWAYAT & ARSIP <span class="text-maroon">BATCH MAGANG</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Daftar batch magang resmi yang seluruh pesertanya telah menyelesaikan seluruh program & dinyatakan lulus.
    </p>
  </div>
</div>

<!-- Grid Daftar Batch Selesai -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
  @forelse($completedBatches as $batch)
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow space-y-4 flex flex-col justify-between">
      <div>
        <div class="flex items-center justify-between border-b pb-3 mb-3">
          <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
            ✓ LULUS 100%
          </span>
          <span class="text-xs text-gray-400 font-medium">
            ID Batch: #{{ $batch->id }}
          </span>
        </div>

        <h3 class="text-lg font-bold text-gray-900">{{ $batch->nama_batch }}</h3>
        <p class="text-xs text-gray-500 mt-1">
          🗓 Periode: {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->isoFormat('D MMM Y') }} – {{ \Carbon\Carbon::parse($batch->tanggal_selesai)->isoFormat('D MMM Y') }}
        </p>

        <div class="mt-4 p-3 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between text-xs">
          <span class="text-gray-600 font-medium">Total Mahasiswa Lulus:</span>
          <span class="font-bold text-gray-900 bg-white px-2.5 py-1 rounded-lg border border-gray-200">
            🎓 {{ $batch->peserta_count }} Orang
          </span>
        </div>
      </div>

      <div class="pt-2">
        <a href="{{ route('magang.riwayat.show-batch', $batch->id) }}" 
           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-maroon text-white font-semibold text-xs rounded-xl hover:bg-maroon-800 transition-colors shadow-sm">
          <span>Lihat Mahasiswa & Laporan</span>
          <span>→</span>
        </a>
      </div>
    </div>
  @empty
    <div class="col-span-full p-12 text-center bg-white rounded-2xl border border-gray-200 shadow-sm text-gray-500 space-y-2">
      <div class="text-3xl">📁</div>
      <h3 class="text-base font-bold text-gray-800">Belum Ada Riwayat Batch Selesai</h3>
      <p class="text-xs text-gray-500 max-w-md mx-auto">
        Batch akan muncul di halaman ini secara otomatis jika <strong>semua mahasiswa</strong> di dalamnya telah menyelesaikan seluruh tahapan magang & lulus.
      </p>
    </div>
  @endforelse
</div>
@endsection