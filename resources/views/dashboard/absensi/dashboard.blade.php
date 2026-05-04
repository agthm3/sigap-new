@extends('layouts.app')

@section('content')
<section class="mb-4">
  <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
    Dashboard <span class="text-maroon">Absensi</span>
  </h1>
  <p class="text-sm text-gray-600 mt-0.5">
    Rekap absensi hari ini, mingguan, dan keterlambatan.
  </p>
</section>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Absen Hari Ini</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalToday }}</h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Terlambat Hari Ini</p>
    <h3 class="text-2xl font-extrabold text-rose-600">{{ $totalLate }}</h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Minggu Ini</p>
    <h3 class="text-2xl font-extrabold text-maroon">{{ $weeklyTotal }}</h3>
  </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Bulan Ini</p>
    <h3 class="text-2xl font-extrabold text-maroon">{{ $monthlyTotal }}</h3>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <div>
      <h2 class="font-semibold text-gray-900">Detail Absensi Hari Ini</h2>
      <p class="text-xs text-gray-500">{{ now()->format('d F Y') }}</p>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Nama</th>
          <th class="px-4 py-3 text-left">Jam</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Terlambat</th>
          <th class="px-4 py-3 text-left">Lokasi</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($todayRecords as $row)
          <tr>
            <td class="px-4 py-3 font-medium text-gray-900">
              {{ $row->user->name ?? '-' }}
            </td>
            <td class="px-4 py-3">{{ $row->absen_time }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border bg-emerald-50 border-emerald-200 text-emerald-700">
                {{ $row->keterangan }}
              </span>
            </td>
            <td class="px-4 py-3">
              {{ $row->late_minutes > 0 ? $row->late_minutes . ' menit' : '-' }}
            </td>
            <td class="px-4 py-3 text-xs text-gray-600">
              {{ $row->location_text ?? '-' }}
            </td>
            <td class="px-4 py-3 text-right">
                @hasanyrole('admin|verificator_absensi')
                    <a href="{{ route('sigap-absensi.edit', $row->id) }}"
                    class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">
                    Ubah
                    </a>
                @endhasanyrole
            </td>
          </tr>
      
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada absensi hari ini.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection