{{-- resources/views/inkubatorma/partials/status-widget.blade.php --}}
@php
  /**
   * Expected variables (pilih salah satu cara pemanggilan):
   * 1) Kirim langsung:  @include('inkubatorma.partials.status-widget', ['status' => $inkubatorma->status])
   * 2) Atau kalau view sudah punya $inkubatorma: status otomatis ambil dari $inkubatorma->status
   */

  $rawStatus = $status ?? ($inkubatorma->status ?? 'Menunggu');

  // Normalisasi label yang mungkin beda di DB
  $normalizedStatus = ($rawStatus === 'Tutup/Selesai') ? 'Tutup' : $rawStatus;

  // SVG icons (inline)
  $icons = [
    'Menunggu' => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 8v5l3 2"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
      </svg>',
    'Akan Dijadwalkan' => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 11l2 2 4-4"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H9l-2 2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2Z"/>
      </svg>',
    'Terjadwal' => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M8 2v3M16 2v3"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 9h18"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M5 5h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M9 15l2 2 4-4"/>
      </svg>',
    'Dijadwalkan Ulang' => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 0 1-9 9 9 9 0 0 1-6.36-2.64"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 12a9 9 0 0 1 9-9 9 9 0 0 1 6.36 2.64"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 21v-6h6"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M21 3v6h-6"/>
      </svg>',
    'Ditolak' => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/>
      </svg>',
    'Tutup' => '
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4"/>
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 11h12a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Z"/>
      </svg>',
  ];

  // Map UI sesuai detail.html
  $statusMap = [
    'Menunggu' => [
      'widget'   => 'bg-yellow-50 border-yellow-200',
      'iconWrap' => 'bg-yellow-100 text-yellow-700',
      'icon'     => $icons['Menunggu'],
      'sub'      => 'Menunggu verifikasi admin/verifikator',
    ],
    'Akan Dijadwalkan' => [
      'widget'   => 'bg-indigo-50 border-indigo-200',
      'iconWrap' => 'bg-indigo-100 text-indigo-700',
      'icon'     => $icons['Akan Dijadwalkan'],
      'sub'      => 'Disetujui, menunggu admin memilih jadwal & PIC',
    ],
    'Terjadwal' => [
      'widget'   => 'bg-blue-50 border-blue-200',
      'iconWrap' => 'bg-blue-100 text-blue-700',
      'icon'     => $icons['Terjadwal'],
      'sub'      => 'Tanggal konsultasi & PIC sudah ditetapkan',
    ],
    'Dijadwalkan Ulang' => [
      'widget'   => 'bg-orange-50 border-orange-200',
      'iconWrap' => 'bg-orange-100 text-orange-700',
      'icon'     => $icons['Dijadwalkan Ulang'],
      'sub'      => 'Admin mengajukan penjadwalan ulang',
    ],
    'Ditolak' => [
      'widget'   => 'bg-red-50 border-red-200',
      'iconWrap' => 'bg-red-100 text-red-700',
      'icon'     => $icons['Ditolak'],
      'sub'      => 'Pengajuan ditolak (ketidaksesuaian konteks)',
    ],
    'Tutup' => [
      'widget'   => 'bg-gray-50 border-gray-200',
      'iconWrap' => 'bg-gray-200 text-gray-700',
      'icon'     => $icons['Tutup'],
      'sub'      => 'Konsultasi ditutup (selesai/diarsipkan)',
    ],
  ];

  $cfg = $statusMap[$normalizedStatus] ?? $statusMap['Menunggu'];
@endphp

<div class="rounded-2xl px-4 py-4 flex items-center gap-3 border h-full {{ $cfg['widget'] }}">
  <div class="h-11 w-11 rounded-full flex items-center justify-center {{ $cfg['iconWrap'] }}">
    {!! $cfg['icon'] !!}
  </div>

  <div class="leading-tight">
    <p class="text-xs font-semibold text-gray-600">Status</p>
    <p class="mt-1 text-sm font-extrabold text-gray-900">{{ $normalizedStatus }}</p>
    <p class="mt-1 text-[11px] text-gray-700">{{ $cfg['sub'] }}</p>
  </div>
</div>
