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
      Pemantauan keaktifan harian, konfirmasi izin susulan, dan audit seluruh riwayat logbook mahasiswa.
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

  <!-- SECTION 2: DAFTAR MAHASISWA & SELURUH LOGBOOK (DENGAN THUMBNAIL) -->
  <div class="mt-8">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h2 class="text-lg font-bold text-gray-900">
          Status & Rekap Seluruh Peserta Magang — Batch {{ $batch->nama_batch }}
        </h2>
        <p class="text-xs text-gray-500">
          Pemantauan kelulusan, seluruh riwayat logbook (scroll internal), dan eksekusi kelulusan mahasiswa.
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      @forelse($pesertaList as $p)
        @php
          // Ambil seluruh entri logbook peserta, urutkan dari yang paling baru
          $userLogs = $logbooksMap->get($p->id, collect())->sortByDesc('tanggal');
          $isSelesai = $p->pivot->status === 'selesai';
          $wpm = $p->pivot->typing_wpm ?? 0;
          $pdfPath = $p->pivot->file_laporan_pdf ?? null;
        @endphp
        
        <div class="rounded-2xl border {{ $isSelesai ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-200 bg-white' }} p-4 shadow-sm flex flex-col h-[30rem]">
          
          <!-- Header Profile & Badge Status / Tombol Selesaikan Paksa -->
          <div class="flex items-start justify-between border-b pb-3 mb-3">
            <div class="flex items-center gap-3 min-w-0 pr-2">
              <div class="w-10 h-10 rounded-full {{ $isSelesai ? 'bg-emerald-100 text-emerald-800' : 'bg-maroon/10 text-maroon' }} font-bold flex items-center justify-center text-sm shrink-0">
                {{ strtoupper(substr($p->name, 0, 2)) }}
              </div>
              <div class="min-w-0">
                <h3 class="font-bold text-gray-900 text-sm truncate" title="{{ $p->name }}">{{ $p->name }}</h3>
                <p class="text-[11px] text-gray-500 truncate" title="{{ $p->pivot->instansi_asal }} — {{ $p->pivot->jurusan }}">
                  {{ $p->pivot->instansi_asal }}
                </p>
              </div>
            </div>

            <!-- Lencana Status & Opsi Selesaikan Paksa -->
            <div class="flex flex-col items-end gap-1 shrink-0">
              @if($isSelesai)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-100 border border-emerald-300 text-emerald-800">
                  🎓 LULUS / SELESAI
                </span>
              @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-100 border border-amber-300 text-amber-900">
                  ● PROSES MAGANG
                </span>
                <!-- Tombol Selesaikan Paksa -->
                <form action="{{ route('magang.batch.force-complete', [$batch->id, $p->id]) }}" 
                      method="POST" 
                      onsubmit="return confirmForceComplete(event, '{{ addslashes($p->name) }}')">
                  @csrf
                  <button type="submit" class="text-[10px] font-bold text-red-600 hover:text-red-800 hover:underline">
                    ⚡ Selesaikan Paksa
                  </button>
                </form>
              @endif
            </div>
          </div>

          <!-- Ringkasan Evaluasi & Syarat -->
          <div class="grid grid-cols-2 gap-2 text-[11px] p-2.5 bg-white/80 rounded-xl border border-gray-100 mb-3">
            <div>
              <span class="text-gray-500 block">Ketik 10 Jari:</span>
              <span class="font-bold {{ $wpm >= 40 ? 'text-emerald-700' : 'text-amber-700' }}">
                ⌨️ {{ $wpm }} WPM {{ $wpm >= 40 ? '(Lulus)' : '' }}
              </span>
            </div>
            <div>
              <span class="text-gray-500 block">Laporan PDF:</span>
              @if($pdfPath)
                <a href="{{ asset('storage/'.$pdfPath) }}" target="_blank" class="font-bold text-maroon hover:underline inline-flex items-center gap-1">
                  📄 Unduh PDF
                </a>
              @else
                <span class="text-gray-400 italic">Belum Ada</span>
              @endif
            </div>
          </div>

          <!-- AREA SELURUH LOGBOOK (SCROLLABLE DENGAN THUMBNAIL LAZY LOAD) -->
          <div class="flex-1 overflow-y-auto pr-1 space-y-2 scrollbar-thin">
            <div class="sticky top-0 bg-white/95 backdrop-blur-xs py-1 z-10 flex items-center justify-between border-b border-gray-100 mb-1">
              <span class="text-[10px] font-bold uppercase text-gray-500">
                Seluruh Logbook ({{ $userLogs->count() }} Entri):
              </span>
              <span class="text-[10px] text-gray-400">Terbaru &darr;</span>
            </div>
            
            @forelse($userLogs as $log)
              <div class="p-2 bg-gray-50/80 rounded-lg border border-gray-100 flex items-start gap-2.5 hover:bg-gray-100/70 transition-colors">
                <!-- Thumbnail Gambar dengan Lazy Load & Preview -->
                @if($log->file_lampiran)
                  <a href="{{ asset('storage/'.$log->file_lampiran) }}" target="_blank" class="shrink-0 group relative" title="Klik untuk memperbesar">
                    <img src="{{ asset('storage/'.$log->file_lampiran) }}" 
                         loading="lazy" 
                         alt="Foto Kegiatan" 
                         class="w-12 h-12 rounded-lg object-cover border border-gray-200 group-hover:opacity-80 transition-opacity">
                    <span class="absolute inset-0 flex items-center justify-center bg-black/30 opacity-0 group-hover:opacity-100 rounded-lg text-white text-[9px] font-bold transition-opacity">
                      🔍
                    </span>
                  </a>
                @else
                  <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center shrink-0 border border-gray-200 text-gray-400" title="Tidak ada foto lampiran">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                  </div>
                @endif
                
                <!-- Deskripsi Kegiatan & Kategori -->
                <div class="space-y-0.5 min-w-0 flex-1">
                  <div class="flex justify-between items-center">
                    <span class="font-bold text-maroon text-[10px] uppercase">
                      {{ \Carbon\Carbon::parse($log->tanggal)->isoFormat('D MMM Y') }}
                    </span>
                    @if($log->kategori && $log->kategori !== 'reguler')
                      <span class="text-[8px] font-extrabold uppercase px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded">
                        {{ $log->kategori }}
                      </span>
                    @endif
                  </div>
                  <p class="text-gray-700 text-[11px] leading-tight line-clamp-2" title="{{ $log->kegiatan }}">
                    {{ $log->kegiatan }}
                  </p>
                </div>
              </div>
            @empty
              <div class="p-6 text-center text-xs text-gray-400 bg-gray-50 rounded-lg">
                Belum ada entri aktivitas logbook.
              </div>
            @endforelse
          </div>

        </div>
      @empty
        <div class="col-span-full p-8 text-center bg-white rounded-2xl border text-gray-500">
          Belum ada peserta pada batch ini.
        </div>
      @endforelse
    </div>
  </div>

@endif
@endsection

@push('scripts')
<script>
  // Konfirmasi sebelum Selesaikan Paksa Peserta
  function confirmForceComplete(event, userName) {
    if (typeof Swal !== 'undefined') {
      event.preventDefault();
      const form = event.target;
      Swal.fire({
        title: 'Selesaikan Paksa Peserta?',
        html: `Apakah Anda yakin ingin menyelesaikan program magang atas nama <strong>${userName}</strong> secara paksa?<br><br><span class="text-xs text-gray-500">Status peserta akan langsung ditandai <strong>LULUS / SELESAI</strong> meskipun belum mengunggah seluruh laporan atau tes ketik.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#059669', // Emerald
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Selesaikan Sekarang',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
      return false;
    }
    return confirm(`Apakah Anda yakin ingin menyelesaikan magang atas nama ${userName} secara paksa? Status akan menjadi LULUS/SELESAI.`);
  }
</script>
@endpush