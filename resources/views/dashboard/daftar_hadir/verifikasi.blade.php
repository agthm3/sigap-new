<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Daftar Hadir – {{ $kegiatan->nama_kegiatan }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-4xl mx-auto px-4 py-8">

  {{-- Header SIGAP --}}
  <div class="text-center mb-6">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold tracking-widest uppercase mb-3"
         style="background:#f0fdf4;color:#166534;border:1px solid #86efac;">
      ✔ Terverifikasi oleh SIGAP
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      {{ $kegiatan->nama_kegiatan }}
    </h1>
    <p class="text-sm text-gray-600 mt-1">
      {{ $kegiatan->hari_tanggal }} • {{ $kegiatan->tempat }} • {{ $kegiatan->waktu }}
    </p>
    <p class="text-xs text-gray-400 mt-1">
      BRIDA Kota Makassar — Sistem Informasi dan Pengelolaan Administrasi (SIGAP)
    </p>
  </div>

  {{-- Ringkasan --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    @php
      $jmlPeserta = $kegiatan->peserta->count();
      $jmlL = $kegiatan->peserta->where('gender','L')->count();
      $jmlP = $kegiatan->peserta->where('gender','P')->count();
    @endphp
    <div class="rounded-2xl border bg-white p-4 text-center shadow-sm">
      <p class="text-2xl font-extrabold text-gray-900">{{ $jmlPeserta }}</p>
      <p class="text-xs text-gray-500 mt-1">Total Peserta</p>
    </div>
    <div class="rounded-2xl border bg-white p-4 text-center shadow-sm">
      <p class="text-2xl font-extrabold text-blue-700">{{ $jmlL }}</p>
      <p class="text-xs text-gray-500 mt-1">Laki-laki</p>
    </div>
    <div class="rounded-2xl border bg-white p-4 text-center shadow-sm">
      <p class="text-2xl font-extrabold text-pink-600">{{ $jmlP }}</p>
      <p class="text-xs text-gray-500 mt-1">Perempuan</p>
    </div>
    <div class="rounded-2xl border bg-white p-4 text-center shadow-sm">
      <p class="text-2xl font-extrabold {{ $kegiatan->penandatangan?->sudah_ttd ? 'text-emerald-700' : 'text-amber-600' }}">
        {{ $kegiatan->penandatangan?->sudah_ttd ? '✔' : '—' }}
      </p>
      <p class="text-xs text-gray-500 mt-1">TTD Pejabat</p>
    </div>
  </div>

  {{-- Tabel Peserta --}}
  <div class="rounded-2xl border border-gray-200 bg-white shadow-sm mb-6 overflow-hidden">
    <div class="px-5 py-4 border-b">
      <h2 class="font-semibold text-gray-900">Daftar Peserta</h2>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
          <tr>
            <th class="px-4 py-3 text-left w-10">No</th>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-left">Instansi</th>
            <th class="px-4 py-3 text-left w-16">Gender</th>
            <th class="px-4 py-3 text-left w-24">TTD</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($kegiatan->peserta as $p)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-2 text-gray-500">{{ $loop->iteration }}</td>
              <td class="px-4 py-2 font-medium text-gray-900">{{ $p->nama }}</td>
              <td class="px-4 py-2 text-gray-700">{{ $p->instansi }}</td>
              <td class="px-4 py-2">
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-medium
                  {{ $p->gender === 'L' ? 'bg-blue-50 text-blue-700' : 'bg-pink-50 text-pink-700' }}">
                  {{ $p->gender === 'L' ? 'L' : 'P' }}
                </span>
              </td>
              <td class="px-4 py-2">
                @if($p->ttd_path && file_exists(storage_path('app/public/' . $p->ttd_path)))
                  <img src="{{ asset('storage/' . $p->ttd_path) }}"
                       class="h-8 object-contain rounded border bg-white p-0.5" alt="TTD">
                @else
                  <span class="text-gray-300 text-xs">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada peserta.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Penandatangan --}}
  @if($kegiatan->penandatangan)
    @php $ttd = $kegiatan->penandatangan; @endphp
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-5">
      <h2 class="font-semibold text-gray-900 mb-4">Penandatangan</h2>
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div class="text-sm space-y-1 text-gray-700">
          <p class="font-semibold text-gray-900 text-base">{{ $ttd->nama_lengkap }}</p>
          @if($ttd->jabatan)   <p>{{ $ttd->jabatan }}</p> @endif
          @if($ttd->pangkat)   <p class="text-gray-500">{{ $ttd->pangkat }}{{ $ttd->golongan ? ' / ' . $ttd->golongan : '' }}</p> @endif
          @if($ttd->nip)       <p class="text-gray-500">NIP: {{ $ttd->nip }}</p> @endif
          @if($ttd->tempat_ttd || $ttd->tanggal_ttd)
            <p class="text-gray-500">{{ $ttd->tempat_ttd }}{{ ($ttd->tempat_ttd && $ttd->tanggal_ttd) ? ', ' : '' }}{{ $ttd->tanggal_ttd }}</p>
          @endif
        </div>
        <div class="text-center min-w-[120px]">
          @if($ttd->sudah_ttd && $ttd->ttd_path)
            <img src="{{ asset('storage/' . $ttd->ttd_path) }}"
                 class="max-h-20 border rounded-xl bg-gray-50 p-2 mx-auto" alt="TTD Pejabat">
            <p class="text-[11px] text-gray-400 mt-1">
              {{ $ttd->signed_at?->format('d M Y, H:i') }}
            </p>
          @else
            <div class="h-20 w-32 rounded-xl border-2 border-dashed border-gray-200 flex items-center justify-center bg-gray-50">
              <span class="text-xs text-gray-400">Belum TTD</span>
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif

  {{-- Footer --}}
  <div class="mt-8 text-center text-xs text-gray-400">
    <p>
      Dokumen ini digenerate dan diverifikasi secara digital oleh
      <span class="font-semibold text-gray-600">SIGAP — BRIDA Kota Makassar</span>.
    </p>
    <p class="mt-1">Scan QR pada dokumen PDF untuk memverifikasi keaslian daftar hadir ini.</p>
  </div>

</div>
</body>
</html>