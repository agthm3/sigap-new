@extends('layouts.app')

@section('content')
<!-- Header Section -->
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">Logbook & Syarat Kelulusan</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      LOGBOOK <span class="text-maroon">KEGIATAN MAGANG</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pelaporan presensi, tes kecakapan ketik 10 jari, dan penutupan magang.
    </p>
  </div>

  @if($activeBatch)
    <div class="bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm flex items-center gap-3">
      <div class="w-2.5 h-2.5 rounded-full {{ $pesertaPivot->status === 'selesai' ? 'bg-blue-500' : 'bg-emerald-500' }} animate-pulse"></div>
      <div>
        <p class="text-[11px] text-gray-500 font-medium">Status Magang Anda</p>
        <p class="text-xs font-bold text-gray-900 uppercase">
          {{ $pesertaPivot->status === 'selesai' ? '🎓 LULUS / SELESAI' : 'AKTIF (BATCH '.$activeBatch->nama_batch.')' }}
        </p>
      </div>
    </div>
  @endif
</div>

@if(!$activeBatch)
  <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50/50 p-6 text-center shadow-sm">
    <h3 class="text-base font-bold text-amber-900">Anda Belum Terdaftar di Batch Magang</h3>
    <p class="text-sm text-amber-700 mt-1">Silakan bergabung dengan batch magang terlebih dahulu.</p>
  </div>
@else

  <!-- Syarat Kelulusan Progress Tracker -->
  <div class="mt-4 p-4 bg-white rounded-2xl border border-gray-200 shadow-sm">
    <h3 class="text-xs font-bold uppercase text-gray-700 mb-3">Syarat & Tahapan Kelulusan Program Magang:</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
      <div class="p-3 rounded-xl border flex items-center gap-3 {{ $hasPenerimaan ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-gray-50 border-gray-200 text-gray-500' }}">
        <span class="text-lg font-extrabold">{{ $hasPenerimaan ? '✓' : '1' }}</span>
        <div>
          <p class="text-xs font-bold">1. Penerimaan Magang</p>
          <p class="text-[11px]">{{ $hasPenerimaan ? 'Sudah Dilaporkan' : 'Wajib diisi di awal' }}</p>
        </div>
      </div>

      <div class="p-3 rounded-xl border flex items-center gap-3 {{ $hasTypingPass ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-amber-50 border-amber-200 text-amber-800' }}">
        <span class="text-lg font-extrabold">{{ $hasTypingPass ? '✓' : '2' }}</span>
        <div>
          <p class="text-xs font-bold">2. Tes Ketik 10 Jari</p>
          <p class="text-[11px]">{{ $hasTypingPass ? 'Lulus (Score: '.$pesertaPivot->typing_wpm.' WPM)' : 'Belum Lulus (Min. 40 WPM)' }}</p>
        </div>
      </div>

      <div class="p-3 rounded-xl border flex items-center gap-3 {{ $hasPenutupan ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-gray-50 border-gray-200 text-gray-500' }}">
        <span class="text-lg font-extrabold">{{ $hasPenutupan ? '✓' : '3' }}</span>
        <div>
          <p class="text-xs font-bold">3. Penutupan & Laporan PDF</p>
          <p class="text-[11px]">{{ $hasPenutupan ? 'Laporan PDF Terunggah' : 'Kunci Terbuka Jika (1) & (2) Selesai' }}</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Table List Logbook & Syarat -->
  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-6">
    <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
      <div>
        <h2 class="font-bold text-gray-900">Jadwal Logbook & Evaluasi Akhir</h2>
        <p class="text-xs text-gray-500 mt-0.5">Selesaikan seluruh tahapan untuk memproses kelulusan magang.</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">Tahap / Hari</th>
            <th class="px-4 py-3 text-left">Tanggal</th>
            <th class="px-4 py-3 text-left">Detail Kegiatan / Hasil</th>
            <th class="px-4 py-3 text-center">Status</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <!-- 1. BARIS PENERIMAAN MAGANG -->
            @php 
            $penerimaanLog = $specialLogs->get('penerimaan');
            $penerimaanArray = $penerimaanLog ? [$penerimaanLog] : [];
            $penerimaanTanggal = $penerimaanLog ? \Carbon\Carbon::parse($penerimaanLog->tanggal)->format('Y-m-d') : now()->format('Y-m-d');
            @endphp
            <tr class="bg-blue-50/40">
            <td class="px-4 py-3.5 font-bold text-blue-900">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 text-xs font-extrabold">
                📌 Penerimaan Magang
                </span>
            </td>
            <td class="px-4 py-3.5 text-xs text-gray-600">Awal Magang</td>
            <td class="px-4 py-3.5 text-gray-800">
                {{ $penerimaanLog ? '1 Laporan Penerimaan Diunggah' : 'Unggah foto & bukti penerimaan resmi' }}
            </td>
            <td class="px-4 py-3.5 text-center">
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $hasPenerimaan ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $hasPenerimaan ? 'Selesai' : 'Wajib' }}
                </span>
            </td>
            <td class="px-4 py-3.5 text-right">
                <button type="button"
                        onclick="openLogbookModal('penerimaan', 'Penerimaan Magang', '{{ $penerimaanTanggal }}', {{ json_encode($penerimaanArray) }})"
                        class="px-3 py-1.5 rounded-lg border border-blue-600 text-blue-700 hover:bg-blue-600 hover:text-white text-xs font-semibold transition-colors">
                {{ $penerimaanLog ? 'Edit Bukti' : '+ Isi Bukti' }}
                </button>
            </td>
            </tr>

          <!-- 2. HARI KERJA REGULER (SENIN-JUMAT) -->
          @foreach($scheduleDays as $index => $day)
            @php
              $dateStr   = $day['date'];
              $rawLogs   = $filledLogs->get($dateStr);
              $dayLogs   = is_a($rawLogs, 'Illuminate\Support\Collection') ? $rawLogs : collect($rawLogs ? [$rawLogs] : []);
              $isToday   = $day['is_today'];
              $isPast    = $day['is_past'];
              $hasExtra  = in_array($dateStr, $extraTimeAllowedDates);
              $canFill   = $isToday || ($isPast && $hasExtra);
              $logsArray = $dayLogs->values()->toArray();
            @endphp
            <tr class="{{ $isToday ? 'bg-amber-50/40 font-medium' : '' }} hover:bg-gray-50/60 transition-colors">
              <td class="px-4 py-3.5 text-gray-900 font-bold">Hari ke-{{ $index + 1 }}</td>
              <td class="px-4 py-3.5 text-gray-800">
                <div class="font-semibold">{{ $day['formatted_date'] }}</div>
                @if($isToday)
                  <span class="text-[10px] uppercase font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Hari Ini</span>
                @endif
              </td>
              <td class="px-4 py-3.5 text-gray-700">
                @if(count($logsArray) > 0)
                  <span class="font-bold text-emerald-700">{{ count($logsArray) }} Kegiatan Dilaporkan</span>
                  <p class="text-xs text-gray-500 truncate max-w-xs mt-0.5">{{ $dayLogs->pluck('kegiatan')->join('; ') }}</p>
                @else
                  <span class="text-gray-400 text-xs italic">Belum ada laporan</span>
                @endif
              </td>
              <td class="px-4 py-3.5 text-center">
                @if(count($logsArray) > 0)
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 border border-emerald-200 text-emerald-700">Selesai</span>
                @elseif($canFill)
                  @if($isPast && $hasExtra)
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-50 border border-purple-200 text-purple-700">Izin Susulan</span>
                  @else
                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 border border-amber-200 text-amber-700">Terbuka Hari Ini</span>
                  @endif
                @elseif($isPast)
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-red-50 border border-red-200 text-red-600">Terlewat</span>
                @else
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500">Terkunci</span>
                @endif
              </td>
              <td class="px-4 py-3.5 text-right">
                @if($canFill)
                  <button type="button"
                          onclick="openLogbookModal('reguler', 'Logbook Hari {{ $day['formatted_date'] }}', '{{ $dateStr }}', {{ json_encode($logsArray) }})"
                          class="px-3 py-1.5 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-xs font-semibold shadow-sm transition-colors">
                    {{ count($logsArray) > 0 ? 'Edit Logbook ('.count($logsArray).')' : 'Isi Logbook' }}
                  </button>
                @elseif(count($logsArray) > 0)
                  <button type="button"
                          onclick="openDetailModal('{{ $day['formatted_date'] }}', {{ json_encode($logsArray) }})"
                          class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold transition-colors">
                    Lihat
                  </button>
                @else
                  <button disabled class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-300 text-xs font-semibold cursor-not-allowed">
                    Terkunci
                  </button>
                @endif
              </td>
            </tr>
          @endforeach

          <!-- 3. BARIS SYARAT TES KETIK 10 JARI (MIN. 40 WPM) -->
          <tr class="bg-amber-50/50 border-t-2 border-amber-200">
            <td class="px-4 py-3.5 font-bold text-amber-900">
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-100 text-amber-900 text-xs font-extrabold">
                ⌨️ Tes Ketik 10 Jari
              </span>
            </td>
            <td class="px-4 py-3.5 text-xs text-gray-600">Evaluasi Kecakapan</td>
            <td class="px-4 py-3.5 text-gray-800">
              @if($hasTypingPass)
                <span class="font-bold text-emerald-700">✓ Lulus Mini Game (Skor: {{ $pesertaPivot->typing_wpm }} WPM)</span>
              @else
                <span class="text-amber-800 font-medium">Syarat Minimal: 40 WPM (Bahasa Indonesia). Skor Anda: {{ $pesertaPivot->typing_wpm ?? 0 }} WPM</span>
              @endif
            </td>
            <td class="px-4 py-3.5 text-center">
              @if($hasTypingPass)
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                  LULUS
                </span>
              @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                  BELUM LULUS
                </span>
              @endif
            </td>
            <td class="px-4 py-3.5 text-right">
              <!-- DILENGKAPI EVENT ONCLICK PENGECEKAN PERANGKAT LAPTOP / HP -->
              <a href="{{ route('magang.typing-game') }}"
                 onclick="return checkDeviceForTypingGame(event)"
                 class="inline-block px-3 py-1.5 rounded-lg bg-amber-600 text-white hover:bg-amber-700 text-xs font-semibold shadow-sm transition-colors">
                {{ $hasTypingPass ? 'Ulangi Tes' : '🎮 Mainkan Mini Game Ketik' }}
              </a>
            </td>
          </tr>

        <!-- 4. BARIS PENUTUPAN MAGANG -->
        @php 
        $penutupanLog = $specialLogs->get('penutupan');
        $penutupanArray = $penutupanLog ? [$penutupanLog] : [];
        $penutupanTanggal = $penutupanLog ? \Carbon\Carbon::parse($penutupanLog->tanggal)->format('Y-m-d') : now()->format('Y-m-d');
        $canCloseMagang = $hasPenerimaan && $hasTypingPass;
        @endphp
        <tr class="bg-emerald-50/40 border-t">
        <td class="px-4 py-3.5 font-bold text-emerald-900">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-extrabold">
            🏁 Penutupan & Laporan Akhir
            </span>
        </td>
        <td class="px-4 py-3.5 text-xs text-gray-600">Akhir Magang</td>
        <td class="px-4 py-3.5 text-gray-800">
            @if($hasPenutupan)
            <span class="font-bold text-emerald-700">✓ Laporan PDF & Dokumen Penutupan Lengkap</span>
            @if($pesertaPivot->file_laporan_pdf)
                <br><a href="{{ asset('storage/'.$pesertaPivot->file_laporan_pdf) }}" target="_blank" class="text-xs text-maroon hover:underline font-semibold">📄 Lihat Laporan PDF</a>
            @endif
            @else
            <span>Upload laporan PDF akhir & foto bukti acara penutupan</span>
            @endif
        </td>
        <td class="px-4 py-3.5 text-center">
            @if($hasPenutupan)
            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                MAGANG SELESAI
            </span>
            @elseif($canCloseMagang)
            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                Siap Diisi
            </span>
            @else
            <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold bg-gray-200 text-gray-500">
                🔒 Terkunci
            </span>
            @endif
        </td>
        <td class="px-4 py-3.5 text-right">
            @if($canCloseMagang)
            <button type="button"
                    onclick="openLogbookModal('penutupan', 'Penutupan Magang & Upload Laporan PDF', '{{ $penutupanTanggal }}', {{ json_encode($penutupanArray) }}, true)"
                    class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-semibold shadow-sm transition-colors">
                {{ $hasPenutupan ? 'Edit Laporan Final' : '+ Isi Penutupan & Upload PDF' }}
            </button>
            @else
            <button disabled class="px-3 py-1.5 rounded-lg border border-gray-300 bg-gray-100 text-gray-400 text-xs font-semibold cursor-not-allowed">
                🔒 Terkunci
            </button>
            @endif
        </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
@endif

<!-- MODAL FORM MULTI-ENTRY LOGBOOK WITH CLIENT-SIDE IMAGE COMPRESSION & PDF UPLOAD -->
<div id="modalLogbook" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl border border-gray-100 my-8">
    <div class="flex items-center justify-between border-b pb-3">
      <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Isi Logbook</h3>
      <button type="button" onclick="closeLogbookModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>

    <form id="formLogbook" action="{{ route('magang.logbook.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
      @csrf
      <input type="hidden" name="magang_batch_id" value="{{ $activeBatch->id ?? '' }}">
      <input type="hidden" id="inputTanggal" name="tanggal" value="">
      <input type="hidden" id="inputKategori" name="kategori" value="reguler">

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Laporan</label>
        <input type="text" id="displayTanggal" disabled class="w-full bg-gray-100 rounded-lg px-3 py-2 text-sm font-semibold text-gray-700">
      </div>

      <!-- Khusus Penutupan: Field Input File PDF Laporan Akhir -->
      <div id="pdfUploadContainer" class="hidden p-4 bg-emerald-50 rounded-xl border border-emerald-200 space-y-2">
        <label class="block text-xs font-bold text-emerald-900 uppercase">
          Upload File Laporan Akhir Magang (Format PDF, Max 10MB) <span class="text-red-500">*</span>
        </label>
        <input type="file" name="laporan_pdf" accept="application/pdf" class="w-full text-xs text-gray-700">
        <p class="text-[11px] text-emerald-700">
          File PDF laporan resmi magang ini akan tersimpan permanen sebagai syarat kelulusan Anda.
        </p>
      </div>

      <!-- Container Kegiatan Multi-Entry -->
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="block text-xs font-semibold text-gray-700 uppercase">
            Daftar Kegiatan (Maksimal 5) <span class="text-red-500">*</span>
          </label>
          <button type="button" id="btnAddActivity" onclick="addActivityRow()" class="text-xs font-bold text-maroon hover:underline">
            + Tambah Kegiatan
          </button>
        </div>

        <div id="activitiesContainer" class="space-y-4 max-h-[50vh] overflow-y-auto pr-1">
          <!-- Filled via JS -->
        </div>
      </div>

      <canvas id="compressCanvas" class="hidden"></canvas>

      <div class="flex justify-end gap-2 pt-4 border-t">
        <button type="button" onclick="closeLogbookModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
          Batal
        </button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition-colors">
          Simpan Laporan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL DETAIL MULTI LOGBOOK -->
<div id="modalDetailLogbook" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
    <div class="flex items-center justify-between border-b pb-3">
      <h3 id="detailTitle" class="text-lg font-bold text-gray-900">Detail Laporan</h3>
      <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>
    
    <div id="detailLogsContainer" class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto">
      <!-- Filled via JS -->
    </div>

    <div class="flex justify-end pt-4 border-t mt-4">
      <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200">
        Tutup
      </button>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let activityCount = 0;

// FUNGSI PENGECEKAN PERANGKAT PERAMBAT (HP / LAPTOP)
function checkDeviceForTypingGame(event) {
  // Mengecek User Agent HP / Tablet atau ukuran lebar layar < 1024px
  const isMobileUserAgent = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
  const isMobileScreen = window.innerWidth < 1024;

  if (isMobileUserAgent || isMobileScreen) {
    event.preventDefault(); // Mencegah membuka tautan/route game

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'warning',
        title: 'Akses Ditolak',
        html: 'Anda tidak dapat mengerjakan tes ketik 10 jari melalui <strong>Handphone / Perangkat Seluler</strong>.<br><br>Silakan buka halaman ini menggunakan <strong>Laptop / Komputer (PC)</strong> dengan Keyboard fisik.',
        confirmButtonColor: '#7a2222',
        confirmButtonText: 'Saya Mengerti'
      });
    } else {
      alert('Akses Ditolak!\n\nAnda tidak dapat mengerjakan tes ketik 10 jari melalui Handphone.\nSilakan buka halaman ini melalui Laptop / Komputer menggunakan Keyboard fisik.');
    }
    return false;
  }
  return true;
}

function openLogbookModal(kategori, title, tanggal, existingLogs, isPenutupan = false) {
  document.getElementById('modalTitle').innerText = title;
  document.getElementById('inputTanggal').value = tanggal;
  document.getElementById('displayTanggal').value = tanggal;
  document.getElementById('inputKategori').value = kategori;

  const pdfContainer = document.getElementById('pdfUploadContainer');
  if (isPenutupan) {
    pdfContainer.classList.remove('hidden');
  } else {
    pdfContainer.classList.add('hidden');
  }

  const container = document.getElementById('activitiesContainer');
  container.innerHTML = '';
  activityCount = 0;

  if (existingLogs && existingLogs.length > 0) {
    existingLogs.forEach(log => {
      addActivityRow(log.kegiatan, log.file_lampiran ? '/storage/' + log.file_lampiran : '');
    });
  } else {
    addActivityRow();
  }

  document.getElementById('modalLogbook').classList.remove('hidden');
}

function closeLogbookModal() {
  document.getElementById('modalLogbook').classList.add('hidden');
}

function addActivityRow(kegiatanVal = '', fotoUrl = '') {
  if (activityCount >= 5) {
    alert('Maksimal 5 kegiatan.');
    return;
  }

  activityCount++;
  const index = activityCount - 1;

  const card = document.createElement('div');
  card.className = 'p-4 bg-gray-50 rounded-xl border border-gray-200 relative space-y-3';
  card.id = `activityRow_${index}`;

  card.innerHTML = `
    <div class="flex items-center justify-between border-b pb-2">
      <span class="text-xs font-extrabold text-maroon uppercase">Kegiatan #${activityCount}</span>
      ${index > 0 ? `<button type="button" onclick="removeActivityRow(${index})" class="text-xs text-red-600 hover:underline font-semibold">Hapus</button>` : ''}
    </div>

    <div>
      <textarea name="items[${index}][kegiatan]" rows="2" required placeholder="Tuliskan uraian kegiatan #${activityCount}..."
                class="w-full rounded-lg px-3 py-2 text-sm">${kegiatanVal}</textarea>
    </div>

    <div>
      <label class="block text-[11px] font-semibold text-gray-600 uppercase mb-1">Foto Bukti (Kompresi Otomatis Browser)</label>
      <input type="file" accept="image/jpeg,image/png,image/jpg" onchange="compressImageForIndex(event, ${index})" class="w-full text-xs text-gray-500">
      <input type="hidden" id="compressedInput_${index}" name="items[${index}][compressed_image]" value="">
      
      <div id="previewContainer_${index}" class="${fotoUrl ? '' : 'hidden'} mt-2 relative">
        <img id="previewImg_${index}" src="${fotoUrl}" class="h-28 w-full object-cover rounded-lg border">
        <span id="previewSize_${index}" class="absolute bottom-1 right-1 bg-black/70 text-white text-[9px] px-1.5 py-0.5 rounded"></span>
      </div>
    </div>
  `;

  document.getElementById('activitiesContainer').appendChild(card);
  updateAddButtonState(); 
}

function removeActivityRow(index) {
  const row = document.getElementById(`activityRow_${index}`);
  if (row) {
    row.remove();
    activityCount--;
    updateAddButtonState();
  }
}

function updateAddButtonState() {
  document.getElementById('btnAddActivity').style.display = activityCount >= 5 ? 'none' : 'inline-block';
}

function compressImageForIndex(event, index) {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.readAsDataURL(file);

  reader.onload = function (e) {
    const img = new Image();
    img.src = e.target.result;

    img.onload = function () {
      const canvas = document.getElementById('compressCanvas');
      const ctx = canvas.getContext('2d');

      const MAX_WIDTH = 1024;
      const MAX_HEIGHT = 1024;
      let width = img.width;
      let height = img.height;

      if (width > height) {
        if (width > MAX_WIDTH) {
          height *= MAX_WIDTH / width;
          width = MAX_WIDTH;
        }
      } else {
        if (height > MAX_HEIGHT) {
          width *= MAX_HEIGHT / height;
          height = MAX_HEIGHT;
        }
      }

      canvas.width = width;
      canvas.height = height;
      ctx.drawImage(img, 0, 0, width, height);

      const compressedBase64 = canvas.toDataURL('image/jpeg', 0.7);

      document.getElementById(`compressedInput_${index}`).value = compressedBase64;
      document.getElementById(`previewImg_${index}`).src = compressedBase64;
      document.getElementById(`previewContainer_${index}`).classList.remove('hidden');

      const approxSizeKB = Math.round((compressedBase64.length * 3 / 4) / 1024);
      document.getElementById(`previewSize_${index}`).innerText = `~${approxSizeKB} KB (Terkompresi)`;
    };
  };
}

function openDetailModal(tanggal, logs) {
  document.getElementById('detailTitle').innerText = 'Laporan Hari ' + tanggal;
  const container = document.getElementById('detailLogsContainer');
  container.innerHTML = '';

  if (logs && logs.length > 0) {
    logs.forEach((log, i) => {
      const div = document.createElement('div');
      div.className = 'p-3 bg-gray-50 rounded-xl border space-y-2';
      const imgHtml = log.file_lampiran 
        ? `<img src="/storage/${log.file_lampiran}" class="h-36 w-full object-cover rounded-lg border">` 
        : '<p class="text-xs text-gray-400 italic">Tidak ada foto bukti.</p>';

      div.innerHTML = `
        <span class="text-xs font-bold text-maroon uppercase">Kegiatan #${i+1}</span>
        <p class="text-sm text-gray-800">${log.kegiatan}</p>
        ${imgHtml}
      `;
      container.appendChild(div);
    });
  }

  document.getElementById('modalDetailLogbook').classList.remove('hidden');
}

function closeDetailModal() {
  document.getElementById('modalDetailLogbook').classList.add('hidden');
}
</script>
@endpush