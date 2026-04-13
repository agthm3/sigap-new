{{-- resources/views/inkubatorma/partials/status-widget.blade.php --}}
@php
  /**
   * Expected variables:
   * @include('dashboard.inkubatorma.partials.status-widget', [
   *   'status' => $inkubatorma->status,
   *   'inkubatorma' => $inkubatorma
   * ])
   */

  $userEmail = optional($inkubatorma->creator)->email ?? 'email pendaftar';

  $statusMenunggu         = \App\Models\Inkubatorma::STATUS_MENUNGGU ?? 'Menunggu';
  $statusAkanDijadwalkan  = \App\Models\Inkubatorma::STATUS_AKAN_DIJADWALKAN ?? 'Akan Dijadwalkan';
  $statusTerjadwal        = \App\Models\Inkubatorma::STATUS_TERJADWAL ?? 'Terjadwal';
  $statusSesiKonsultasi   = \App\Models\Inkubatorma::STATUS_SESI_KONSULTASI ?? 'Sesi Konsultasi';
  $statusDijadwalkanUlang = \App\Models\Inkubatorma::STATUS_DIJADWALKAN_ULANG ?? 'Dijadwalkan Ulang';
  $statusDitolak          = \App\Models\Inkubatorma::STATUS_DITOLAK ?? 'Ditolak';
  $statusSelesai          = \App\Models\Inkubatorma::STATUS_SELESAI ?? 'Selesai';

  $rawStatus = $status ?? ($inkubatorma->status ?? $statusMenunggu);

  // normalisasi label lama
  $normalizedStatus = match ($rawStatus) {
    'Tutup/Selesai' => $statusSelesai,
    'Tutup'         => $statusSelesai,
    default         => $rawStatus,
  };

  // SVG icons (inline)
  $icons = [
    $statusMenunggu => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v5l3 2"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
      </svg>',

    $statusAkanDijadwalkan => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 11l2 2 4-4"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H9l-2 2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
      </svg>',

    $statusTerjadwal => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 2v3M16 2v3"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 9h18"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 5h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 15l2 2 4-4"/>
      </svg>',

    $statusSesiKonsultasi => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 10h8"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 14h5"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z"/>
      </svg>',

    $statusDijadwalkanUlang => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 0 1-9 9 9 9 0 0 1-6.36-2.64"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 9-9 9 9 0 0 1 6.36 2.64"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 21v-6h6"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 3v6h-6"/>
      </svg>',

    $statusDitolak => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/>
      </svg>',

    $statusSelesai => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 11h12a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Z"/>
      </svg>',
  ];

  $statusMap = [
    $statusMenunggu => [
      'widget'   => 'bg-yellow-50 border-yellow-200',
      'iconWrap' => 'bg-yellow-100 text-yellow-700',
      'icon'     => $icons[$statusMenunggu],
      'sub'      => "Menunggu verifikasi admin. Pembaruan status akan dikirim ke email: <strong>$userEmail</strong>. Mohon cek inbox atau folder spam Anda secara berkala.",
    ],

    $statusAkanDijadwalkan => [
      'widget'   => 'bg-indigo-50 border-indigo-200',
      'iconWrap' => 'bg-indigo-100 text-indigo-700',
      'icon'     => $icons[$statusAkanDijadwalkan],
      'sub'      => "Disetujui, menunggu admin memilih jadwal & PIC. Pembaruan status akan dikirim ke email: <strong>$userEmail</strong>. Mohon cek inbox atau folder spam Anda secara berkala.",
    ],

    $statusTerjadwal => [
      'widget'   => 'bg-blue-50 border-blue-200',
      'iconWrap' => 'bg-blue-100 text-blue-700',
      'icon'     => $icons[$statusTerjadwal],
      'sub'      => "Tanggal konsultasi & PIC sudah ditetapkan. Pembaruan status akan dikirim ke email: <strong>$userEmail</strong>. Mohon cek inbox atau folder spam Anda secara berkala.",
    ],

    $statusSesiKonsultasi => [
      'widget'   => 'bg-purple-50 border-purple-200',
      'iconWrap' => 'bg-purple-100 text-purple-700',
      'icon'     => $icons[$statusSesiKonsultasi],
      'sub'      => "Sesi konsultasi sedang berjalan / tindak lanjut revisi berlangsung. Pembaruan status akan dikirim ke email: <strong>$userEmail</strong>. Mohon cek inbox atau folder spam Anda secara berkala.",
    ],

    $statusDijadwalkanUlang => [
      'widget'   => 'bg-orange-50 border-orange-200',
      'iconWrap' => 'bg-orange-100 text-orange-700',
      'icon'     => $icons[$statusDijadwalkanUlang],
      'sub'      => "Admin mengajukan penjadwalan ulang. Pembaruan status akan dikirim ke email: <strong>$userEmail</strong>. Mohon cek inbox atau folder spam Anda secara berkala.",
    ],

    $statusDitolak => [
      'widget'   => 'bg-red-50 border-red-200',
      'iconWrap' => 'bg-red-100 text-red-700',
      'icon'     => $icons[$statusDitolak],
      'sub'      => "Pengajuan ditolak.",
    ],

    $statusSelesai => [
      'widget'   => 'bg-gray-50 border-gray-200',
      'iconWrap' => 'bg-gray-200 text-gray-700',
      'icon'     => $icons[$statusSelesai],
      'sub'      => "Konsultasi ditutup dan proses telah selesai",
    ],
  ];

  $cfg = $statusMap[$normalizedStatus] ?? $statusMap[$statusMenunggu];
@endphp

<div class="rounded-2xl px-4 py-4 flex items-center gap-3 border h-full {{ $cfg['widget'] }}">
  <div class="h-11 w-11 rounded-full flex items-center justify-center {{ $cfg['iconWrap'] }}">
    {!! $cfg['icon'] !!}
  </div>

  <div class="leading-tight">
    <p class="text-xs font-semibold text-gray-600">Status</p>
    <p class="mt-1 text-sm font-extrabold text-gray-900">{{ $normalizedStatus }}</p>
    <p class="mt-1 text-xs text-gray-600 leading-normal">{!! $cfg['sub'] !!}</p>
  </div>
</div>