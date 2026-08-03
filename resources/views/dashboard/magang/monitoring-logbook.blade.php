@extends('layouts.app')

@section('content')
<!-- Header Section -->
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">Monitoring Logbook</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      MONITORING <span class="text-maroon">MAGANG & LOGBOOK</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pemantauan keaktifan harian, konfirmasi izin susulan, dan rekapitulasi laporan harian mingguan.
    </p>
  </div>
</div>

<!-- Filter Bar -->
<div class="mt-4 p-4 bg-white rounded-2xl border border-gray-200 shadow-sm">
  <form action="{{ route('magang.monitoring-logbook') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
    <div>
      <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Batch Magang</label>
      <select name="batch_id" onchange="this.form.submit()" class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
        @forelse($batches as $b)
          <option value="{{ $b->id }}" {{ $selectedBatchId == $b->id ? 'selected' : '' }}>
            {{ $b->nama_batch }} ({{ strtoupper($b->status) }})
          </option>
        @empty
          <option value="">Belum ada batch</option>
        @endforelse
      </select>
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Tinjauan Spesifik</label>
      <input type="date" name="tanggal" value="{{ $selectedDate }}" onchange="this.form.submit()" class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
    </div>

    <div class="flex items-end gap-2">
      <button type="submit" class="w-full px-4 py-2 bg-maroon text-white text-xs font-semibold rounded-xl hover:bg-maroon-800 transition-colors">
        Terapkan Filter
      </button>
      <a href="{{ route('magang.monitoring-logbook') }}" class="px-3 py-2 border border-gray-300 text-xs font-semibold rounded-xl text-gray-700 hover:bg-gray-50">
        Reset
      </a>
    </div>
  </form>
</div>

@if(!$batch)
  <div class="mt-6 p-8 text-center bg-white rounded-2xl border border-gray-200 shadow-sm text-gray-500">
    Silakan pilih Batch Magang terlebih dahulu untuk menampilkan data monitoring.
  </div>
@else

  <!-- Summary Cards / Angka Statistik -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-medium text-gray-500">Total Peserta Batch</p>
      <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalPeserta }} Orang</h3>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-medium text-gray-500">Sudah Mengisi Logbook ({{ \Carbon\Carbon::parse($selectedDate)->isoFormat('D MMM Y') }})</p>
      <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $terisiHariIni }} Orang</h3>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-xs font-medium text-gray-500">Belum Mengisi / Terlewat</p>
      <h3 class="text-2xl font-extrabold text-red-600 mt-1">{{ $belumIsiHariIni }} Orang</h3>
    </div>
  </div>

  <!-- SECTION 1: ANTREAN HARI TERLEWAT (DIBUTUHKAN KONFIRMASI DITERIMA / DITOLAK) -->
  <div class="rounded-2xl border border-amber-200 bg-amber-50/30 overflow-hidden shadow-sm mt-6">
    <div class="px-5 py-4 border-b border-amber-200 bg-amber-100/50 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-amber-900 flex items-center gap-2">
          <span>⚠️</span> Daftar Hari Kerja Terlewat (Butuh Konfirmasi Waktu Tambahan)
        </h2>
        <p class="text-xs text-amber-800 mt-0.5">
          Berikut adalah daftar hari kerja yang dilewati anak magang tanpa mengisi logbook. Terima untuk memberikan akses pengisian susulan.
        </p>
      </div>
      <span class="px-3 py-1 bg-amber-200 text-amber-900 text-xs font-extrabold rounded-full">
        {{ $pendingApprovals->count() }} Pengajuan
      </span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm bg-white">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">Mahasiswa</th>
            <th class="px-4 py-3 text-left">Instansi / Asal</th>
            <th class="px-4 py-3 text-left">Hari / Tanggal Terlewat</th>
            <th class="px-4 py-3 text-center">Status Akses</th>
            <th class="px-4 py-3 text-right">Keputusan Verifikator</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($pendingApprovals as $item)
            <tr class="hover:bg-amber-50/20">
              <td class="px-4 py-3 font-semibold text-gray-900">
                {{ $item['user_name'] }}
              </td>
              <td class="px-4 py-3 text-gray-600">
                {{ $item['instansi'] ?: '-' }}
              </td>
              <td class="px-4 py-3 font-medium text-amber-900">
                {{ $item['formatted_date'] }}
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                  Terkunci
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <form action="{{ route('magang.izin-susulan.store') }}" method="POST" class="inline-flex gap-2">
                  @csrf
                  <input type="hidden" name="magang_batch_id" value="{{ $batch->id }}">
                  <input type="hidden" name="user_id" value="{{ $item['user_id'] }}">
                  <input type="hidden" name="tanggal" value="{{ $item['tanggal'] }}">
                  
                  <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-semibold shadow-sm transition-colors">
                    ✓ Buka Akses (Terima)
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-gray-500 font-medium">
                🎉 Tidak ada pengisian logbook terlewat yang membutuhkan konfirmasi.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- SECTION 2: LAPORAN MINGGU INI (GRID VIEW WITH THUMBNAILS) -->
  <!-- SECTION: DAFTAR MAHASISWA & REKAP KELULUSAN BATCH -->
<div class="mt-8">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h2 class="text-lg font-bold text-gray-900">
        Status & Rekap Peserta Magang — Batch {{ $batch->nama_batch }}
      </h2>
      <p class="text-xs text-gray-500">
        Pemantauan kelulusan, skor tes ketik 10 jari, dan akses Laporan PDF Akhir.
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @forelse($pesertaList as $p)
      @php
        $userLogs = $weeklyReportsMap->get($p->id, collect());
        $isSelesai = $p->pivot->status === 'selesai';
        $wpm = $p->pivot->typing_wpm ?? 0;
        $pdfPath = $p->pivot->file_laporan_pdf ?? null;
      @endphp
      
      <div class="rounded-2xl border {{ $isSelesai ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-200 bg-white' }} p-5 shadow-sm space-y-4">
        
        <!-- Header Profile & Badge Kelulusan -->
        <div class="flex items-start justify-between border-b pb-3">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full {{ $isSelesai ? 'bg-emerald-100 text-emerald-800' : 'bg-maroon/10 text-maroon' }} font-bold flex items-center justify-center text-sm">
              {{ strtoupper(substr($p->name, 0, 2)) }}
            </div>
            <div>
              <h3 class="font-bold text-gray-900 text-sm">{{ $p->name }}</h3>
              <p class="text-xs text-gray-500">{{ $p->pivot->instansi_asal }} — {{ $p->pivot->jurusan }}</p>
            </div>
          </div>

          <!-- Lencana Status -->
          <div>
            @if($isSelesai)
              <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 border border-emerald-300 text-emerald-800">
                🎓 LULUS / SELESAI
              </span>
            @else
              <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 border border-amber-300 text-amber-900">
                ● PROSES MAGANG
              </span>
            @endif
          </div>
        </div>

        <!-- Ringkasan Evaluasi & Syarat -->
        <div class="grid grid-cols-2 gap-2 text-xs p-3 bg-white/80 rounded-xl border border-gray-100">
          <div>
            <span class="text-gray-500 block">Tes Ketik 10 Jari:</span>
            <span class="font-bold {{ $wpm >= 40 ? 'text-emerald-700' : 'text-amber-700' }}">
              ⌨️ {{ $wpm }} WPM {{ $wpm >= 40 ? '(Lulus)' : '(Belum Lulus)' }}
            </span>
          </div>

          <div>
            <span class="text-gray-500 block">Laporan PDF Akhir:</span>
            @if($pdfPath)
              <a href="{{ asset('storage/'.$pdfPath) }}" target="_blank" class="font-bold text-maroon hover:underline inline-flex items-center gap-1">
                📄 Unduh PDF
              </a>
            @else
              <span class="text-gray-400 italic">Belum Diunggah</span>
            @endif
          </div>
        </div>

        <!-- Logs Minggu Ini / Kegiatan -->
        <div class="space-y-2">
          <p class="text-[11px] font-bold uppercase text-gray-500">Ringkasan Laporan Terakhir:</p>
          @forelse($userLogs->take(3) as $log)
            <div class="p-2.5 bg-gray-50/80 rounded-lg border border-gray-100 flex items-center justify-between text-xs">
              <div class="space-y-0.5 truncate pr-2">
                <span class="font-bold text-maroon text-[11px]">
                  {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('dddd, D MMM') }}
                </span>
                <p class="text-gray-700 truncate">{{ $log->kegiatan }}</p>
              </div>
              @if($log->file_lampiran)
                <a href="{{ asset('storage/'.$log->file_lampiran) }}" target="_blank" class="shrink-0 text-maroon font-semibold hover:underline text-[11px]">
                  📷 Foto
                </a>
              @endif
            </div>
          @empty
            <div class="p-3 text-center text-xs text-gray-400 bg-gray-50 rounded-lg">
              Belum ada aktivitas logbook pada periode ini.
            </div>
          @endforelse
        </div>

      </div>
    @empty
      <div class="col-span-2 p-8 text-center bg-white rounded-2xl border text-gray-500">
        Belum ada peserta pada batch ini.
      </div>
    @endforelse
  </div>
</div>

@endif
@endsection