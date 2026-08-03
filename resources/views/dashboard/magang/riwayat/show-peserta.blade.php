@extends('layouts.app')

@section('content')
<!-- Header & Navigasi -->
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <a href="{{ route('magang.riwayat.index') }}" class="hover:text-maroon">Riwayat</a>
      <span>/</span>
      <a href="{{ route('magang.riwayat.show-batch', $batch->id) }}" class="hover:text-maroon">Batch {{ $batch->nama_batch }}</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">{{ $peserta->name }}</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      LOGBOOK & ARSIP <span class="text-maroon">{{ strtoupper($peserta->name) }}</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Dokumen rekapitulasi kegiatan harian, tes ketik 10 jari, dan Laporan Akhir PDF.
    </p>
  </div>

  <a href="{{ route('magang.riwayat.show-batch', $batch->id) }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-100 transition-colors">
    ← Kembali ke Daftar Mahasiswa
  </a>
</div>

<!-- Profile Card & Ringkasan Lulus -->
<div class="mt-6 p-5 bg-white rounded-2xl border border-gray-200 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
  <div>
    <p class="text-xs font-semibold text-gray-500 uppercase">Nama Mahasiswa</p>
    <h3 class="text-lg font-bold text-gray-900 mt-0.5">{{ $peserta->name }}</h3>
    <p class="text-xs text-gray-600">{{ $pesertaPivot->instansi_asal }} — {{ $pesertaPivot->jurusan }}</p>
  </div>

  <div>
    <p class="text-xs font-semibold text-gray-500 uppercase">Hasil Tes Ketik 10 Jari</p>
    <p class="text-xl font-extrabold text-emerald-700 mt-0.5">⌨️ {{ $pesertaPivot->typing_wpm }} WPM (LULUS)</p>
  </div>

  <div>
    <p class="text-xs font-semibold text-gray-500 uppercase">Dokumen Laporan Akhir PDF</p>
    @if($pesertaPivot->file_laporan_pdf)
      <a href="{{ asset('storage/'.$pesertaPivot->file_laporan_pdf) }}" target="_blank" class="inline-flex items-center gap-2 mt-1 px-3 py-1.5 rounded-lg bg-red-600 text-white font-bold text-xs hover:bg-red-700 transition-colors shadow-sm">
        📄 Download Laporan PDF
      </a>
    @else
      <span class="text-xs text-gray-400 italic">Tidak ada file</span>
    @endif
  </div>
</div>

<!-- Timeline & Logbook Lengkap -->
<div class="mt-6 space-y-6">

  <!-- 1. Penerimaan Magang -->
  <div class="p-5 bg-blue-50/50 rounded-2xl border border-blue-200 shadow-sm">
    <h3 class="text-sm font-bold text-blue-900 mb-3 flex items-center gap-2">
      <span>📌</span> Tahap 1: Laporan Penerimaan Magang
    </h3>
    @if($penerimaanLog)
      <div class="bg-white p-4 rounded-xl border border-blue-100 space-y-2">
        <p class="text-xs font-semibold text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($penerimaanLog->tanggal)->isoFormat('D MMMM Y') }}</p>
        <p class="text-sm text-gray-800">{{ $penerimaanLog->kegiatan }}</p>
        @if($penerimaanLog->file_lampiran)
          <a href="{{ asset('storage/'.$penerimaanLog->file_lampiran) }}" target="_blank" class="inline-block mt-2">
            <img src="{{ asset('storage/'.$penerimaanLog->file_lampiran) }}" class="h-32 rounded-lg border object-cover">
          </a>
        @endif
      </div>
    @else
      <p class="text-xs text-gray-500 italic">Tidak ada catatan penerimaan.</p>
    @endif
  </div>

  <!-- 2. Logbook Harian Reguler -->
  <div class="p-5 bg-white rounded-2xl border border-gray-200 shadow-sm space-y-4">
    <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2 border-b pb-3">
      <span>📅</span> Tahap 2: Logbook Kegiatan Harian
    </h3>

    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
      @forelse($regulerLogs as $dateStr => $dayLogs)
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3">
          <div class="flex items-center justify-between border-b pb-2">
            <span class="text-xs font-bold text-maroon">
              {{ \Carbon\Carbon::parse($dateStr)->isoFormat('dddd, D MMMM Y') }}
            </span>
            <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">
              {{ $dayLogs->count() }} Kegiatan
            </span>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($dayLogs as $idx => $log)
              <div class="p-3 bg-white rounded-lg border border-gray-100 space-y-2">
                <span class="text-[10px] font-extrabold uppercase text-gray-400">Kegiatan #{{ $idx + 1 }}</span>
                <p class="text-xs text-gray-800 leading-relaxed">{{ $log->kegiatan }}</p>
                @if($log->file_lampiran)
                  <a href="{{ asset('storage/'.$log->file_lampiran) }}" target="_blank" class="inline-block text-[11px] text-maroon font-bold hover:underline">
                    📷 Lihat Foto Bukti
                  </a>
                @endif
              </div>
            @endforeach
          </div>
        </div>
      @empty
        <p class="text-xs text-gray-500 italic text-center py-4">Tidak ada entri logbook harian.</p>
      @endforelse
    </div>
  </div>

  <!-- 3. Penutupan Magang -->
  <div class="p-5 bg-emerald-50/50 rounded-2xl border border-emerald-200 shadow-sm">
    <h3 class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2">
      <span>🏁</span> Tahap 3: Penutupan & Pelepasan Magang
    </h3>
    @if($penutupanLog)
      <div class="bg-white p-4 rounded-xl border border-emerald-100 space-y-2">
        <p class="text-xs font-semibold text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($penutupanLog->tanggal)->isoFormat('D MMMM Y') }}</p>
        <p class="text-sm text-gray-800">{{ $penutupanLog->kegiatan }}</p>
        @if($penutupanLog->file_lampiran)
          <a href="{{ asset('storage/'.$penutupanLog->file_lampiran) }}" target="_blank" class="inline-block mt-2">
            <img src="{{ asset('storage/'.$penutupanLog->file_lampiran) }}" class="h-32 rounded-lg border object-cover">
          </a>
        @endif
      </div>
    @else
      <p class="text-xs text-gray-500 italic">Tidak ada catatan penutupan.</p>
    @endif
  </div>

</div>
@endsection