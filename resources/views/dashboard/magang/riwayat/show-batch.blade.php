@extends('layouts.app')

@section('content')
<!-- Header & Navigation -->
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <a href="{{ route('magang.riwayat.index') }}" class="hover:text-maroon">Riwayat Magang</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">Batch {{ $batch->nama_batch }}</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      DAFTAR LULUSAN <span class="text-maroon">BATCH {{ strtoupper($batch->nama_batch) }}</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      🗓 Periode: {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->isoFormat('D MMMM Y') }} – {{ \Carbon\Carbon::parse($batch->tanggal_selesai)->isoFormat('D MMMM Y') }}
    </p>
  </div>

  <a href="{{ route('magang.riwayat.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-100 transition-colors">
    ← Kembali ke Daftar Batch
  </a>
</div>

<!-- Card Daftar Peserta -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
  @foreach($batch->peserta as $p)
    @php
      $wpm = $p->pivot->typing_wpm ?? 0;
      $pdfPath = $p->pivot->file_laporan_pdf ?? null;
    @endphp
    <div class="rounded-2xl border border-emerald-200 bg-white p-5 shadow-sm space-y-4 flex flex-col justify-between">
      <div class="space-y-3">
        <div class="flex items-center justify-between border-b pb-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm">
              {{ strtoupper(substr($p->name, 0, 2)) }}
            </div>
            <div>
              <h3 class="font-bold text-gray-900 text-sm">{{ $p->name }}</h3>
              <p class="text-xs text-gray-500">{{ $p->email }}</p>
            </div>
          </div>
        </div>

        <div class="text-xs space-y-1 text-gray-700">
          <p><strong>Instansi Asal:</strong> {{ $p->pivot->instansi_asal ?: '-' }}</p>
          <p><strong>Jurusan:</strong> {{ $p->pivot->jurusan ?: '-' }}</p>
        </div>

        <!-- Badges Status -->
        <div class="pt-2 border-t flex flex-wrap items-center gap-2 text-xs">
          <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
            ⌨️ {{ $wpm }} WPM (Lulus)
          </span>

          @if($pdfPath)
            <a href="{{ asset('storage/'.$pdfPath) }}" target="_blank" class="px-2.5 py-1 rounded-full font-bold bg-blue-100 text-blue-800 border border-blue-300 hover:underline">
              📄 PDF Laporan
            </a>
          @endif
        </div>
      </div>

      <div class="pt-2">
        <a href="{{ route('magang.riwayat.show-peserta', [$batch->id, $p->id]) }}"
           class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-maroon text-white font-semibold text-xs rounded-xl hover:bg-maroon-800 transition-colors shadow-sm">
          <span>Lihat Logbook Lengkap</span>
          <span>→</span>
        </a>
      </div>
    </div>
  @endforeach
</div>
@endsection