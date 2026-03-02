@php
  /**
   * TIMELINE INKUBATORMA (Blade version)
   * Expected variables:
   * - $currentStatus (string) optional
   * - $submittedAt (string|Carbon|null) optional
   *
   * Status supported:
   * Menunggu | Akan Dijadwalkan | Terjadwal | Dijadwalkan Ulang | Ditolak | Tutup
   */

  $currentStatus = $currentStatus ?? ($inkubatorma->status ?? 'Menunggu');
  $normalizedStatus = in_array($currentStatus, ['Tutup/Selesai', 'Selesai', 'Tutup'], true)
    ? 'Tutup'
    : $currentStatus;

  // Optional submitted time (fallback string)
  $submittedLabel = '—';
  if (!empty($submittedAt)) {
    try {
      $submittedLabel = \Carbon\Carbon::parse($submittedAt)->timezone('Asia/Makassar')->format('d M Y • H:i');
    } catch (\Throwable $e) {
      $submittedLabel = (string) $submittedAt;
    }
  } elseif (!empty($inkubatorma->created_at)) {
    $submittedLabel = $inkubatorma->created_at->timezone('Asia/Makassar')->format('d M Y • H:i');
  } else {
    $submittedLabel = '27 Jan 2026 • 09:12';
  }

  // Build base steps
  $steps = [
    [
      'key'   => 'submitted',
      'title' => 'Pengajuan dibuat',
      'time'  => $submittedLabel,
      'desc'  => 'Pemohon mengisi form dan data pengajuan tersimpan di sistem.',
      'state' => 'done',
    ],
    [
      'key'   => 'verified',
      'title' => 'Verifikasi pengajuan',
      'time'  => 'Menunggu',
      'desc'  => 'Admin/verifikator mengecek kesesuaian konteks dan kelengkapan informasi.',
      'state' => 'next',
    ],
    [
      'key'   => 'scheduling',
      'title' => 'Penjadwalan & penunjukan PIC',
      'time'  => 'Menunggu',
      'desc'  => 'Admin menentukan tanggal inkubatorma serta menetapkan PIC pendampingan.',
      'state' => 'next',
    ],
    [
      'key'   => 'session',
      'title' => 'Sesi konsultasi berlangsung',
      'time'  => 'Menunggu',
      'desc'  => 'Konsultasi online/offline dilakukan sesuai jadwal yang ditetapkan.',
      'state' => 'next',
    ],
    [
      'key'   => 'closed',
      'title' => 'Tutup / Selesai',
      'time'  => 'Menunggu',
      'desc'  => 'Konsultasi selesai.',
      'state' => 'next',
    ],
  ];

  // Helper to mutate step quickly
  $set = function (&$steps, $i, $state=null, $time=null, $title=null, $desc=null) {
    if ($state !== null) $steps[$i]['state'] = $state;
    if ($time  !== null) $steps[$i]['time']  = $time;
    if ($title !== null) $steps[$i]['title'] = $title;
    if ($desc  !== null) $steps[$i]['desc']  = $desc;
  };

  // Apply status logic
  if ($normalizedStatus === 'Menunggu') {
    $set($steps, 1, 'current', 'Saat ini');
  }

  if ($normalizedStatus === 'Akan Dijadwalkan') {
    $set($steps, 1, 'done', 'Disetujui', null, 'Pengajuan dinyatakan sesuai dan siap diproses ke penjadwalan.');
    $set($steps, 2, 'current', 'Saat ini', null, 'Menunggu admin memilih tanggal yang tersedia dan menetapkan PIC.');
  }

  if ($normalizedStatus === 'Terjadwal') {
    $set($steps, 1, 'done', 'Terverifikasi');
    $set($steps, 2, 'done', 'Jadwal ditetapkan', null, 'Tanggal konsultasi dan PIC telah ditentukan dan diinformasikan ke pemohon.');
    $set($steps, 3, 'current', 'Saat ini', null, 'Menunggu pelaksanaan sesi konsultasi sesuai jadwal.');
  }

  if ($normalizedStatus === 'Dijadwalkan Ulang') {
    $set($steps, 1, 'done', 'Terverifikasi');
    $set($steps, 2, 'current', 'Saat ini', 'Penjadwalan ulang diminta',
      'Jadwal usulan tidak tersedia. Admin mengajukan opsi jadwal alternatif untuk disepakati.'
    );
  }

  if ($normalizedStatus === 'Ditolak') {
    $set($steps, 1, 'current', 'Keputusan', null,
      'Pengajuan ditolak karena ketidaksesuaian konteks inkubatorma atau informasi tidak memadai.'
    );
    $set($steps, 2, 'cancelled', '-', null, 'Tahap ini tidak dilanjutkan karena pengajuan ditolak.');
    $set($steps, 3, 'cancelled', '-', null, 'Tahap ini tidak dilanjutkan karena pengajuan ditolak.');
    $set($steps, 4, 'cancelled', '-', 'Berakhir (Ditolak)', 'Status berakhir pada keputusan penolakan.');
  }

  if ($normalizedStatus === 'Tutup') {
    $set($steps, 1, 'done', 'Terverifikasi');
    $set($steps, 2, 'done', 'Terjadwal');
    $set($steps, 3, 'done', 'Terlaksana');
    $set($steps, 4, 'done', 'Selesai', null,
      'Konsultasi selesai. Dokumen/notulen dan tindak lanjut dapat dilihat/diarsipkan.'
    );
  }

  // Color helpers (dot & line)
  $dotClass = function($state) use ($normalizedStatus) {
    if ($state === 'done') return 'bg-green-600';
    if ($state === 'cancelled') return 'bg-gray-300';
    if ($state === 'next') return 'bg-gray-300';

    // current
    return match ($normalizedStatus) {
      'Menunggu' => 'bg-yellow-400',
      'Akan Dijadwalkan' => 'bg-indigo-500',
      'Terjadwal' => 'bg-blue-500',
      'Dijadwalkan Ulang' => 'bg-orange-500',
      'Ditolak' => 'bg-red-500',
      'Tutup' => 'bg-gray-600',
      default => 'bg-yellow-400',
    };
  };

  $lineClass = function($state) use ($normalizedStatus) {
    if ($state === 'done') return 'bg-green-600';
    if ($state === 'cancelled') return 'bg-gray-200';
    if ($state === 'next') return 'bg-gray-200';

    // current
    return match ($normalizedStatus) {
      'Menunggu' => 'bg-yellow-300',
      'Akan Dijadwalkan' => 'bg-indigo-300',
      'Terjadwal' => 'bg-blue-300',
      'Dijadwalkan Ulang' => 'bg-orange-300',
      'Ditolak' => 'bg-red-300',
      'Tutup' => 'bg-gray-400',
      default => 'bg-yellow-300',
    };
  };

  $badgeHtml = function($state) {
    return match ($state) {
      'done' => '<span class="text-[10px] px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">Selesai</span>',
      'current' => '<span class="text-[10px] px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800 font-semibold">Saat ini</span>',
      'cancelled' => '<span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-200 text-gray-700 font-semibold">Tidak dilanjutkan</span>',
      default => '<span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 font-semibold">Berikutnya</span>',
    };
  };

  $opacity = function($state) {
    return in_array($state, ['next','cancelled'], true) ? 'opacity-60' : '';
  };
@endphp

<ol class="relative space-y-5">
  @foreach($steps as $idx => $step)
    @php
      $isLast = $idx === count($steps) - 1;
      $dotCls  = $dotClass($step['state']);
      $lineCls = $lineClass($step['state']);
      $opCls   = $opacity($step['state']);
    @endphp

    <li class="relative {{ $opCls }}">
      <!-- LEFT RAIL: dot + line -->
      <div class="absolute left-2 top-0 bottom-0 flex flex-col items-center">
        <span class="mt-1.5 h-3 w-3 rounded-full {{ $dotCls }}"></span>
        @if(!$isLast)
          <span class="mt-1 w-0.5 flex-1 {{ $lineCls }} rounded"></span>
        @endif
      </div>

      <!-- CONTENT -->
      <div class="pl-10">
        <div class="flex items-start justify-between gap-3">
          <p class="text-sm font-semibold text-gray-800">{{ $step['title'] }}</p>
          {!! $badgeHtml($step['state']) !!}
        </div>
        <p class="text-xs text-gray-500 mt-0.5">{{ $step['time'] }}</p>
        <p class="text-sm text-gray-700 mt-1 leading-relaxed">{{ $step['desc'] }}</p>
      </div>
    </li>
  @endforeach
</ol>

<div class="mt-4 rounded-lg bg-gray-50 border border-gray-200 p-3 text-xs text-gray-600">
  <span class="font-semibold text-gray-700">Keterangan:</span>
  garis & titik hijau = selesai,
  garis & titik kuning (atau warna status) = tahap saat ini,
  garis & titik abu = berikutnya.
</div>