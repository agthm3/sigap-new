@extends('layouts.app')

@section('content')
<div class="space-y-4">
  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        Rekap <span class="text-maroon">Harian</span>
      </h1>
      <p class="text-sm text-gray-600 mt-0.5">
        Data absensi per tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}.
      </p>
    </div>

    <form method="GET" action="{{ route('sigap-absensi.rekap-harian') }}" class="flex flex-col sm:flex-row gap-2">
      <input type="date"
             name="tanggal"
             value="{{ $tanggal }}"
             class="rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">

      <button type="submit"
              class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold hover:bg-gray-50">
        Filter
      </button>

      <a href="{{ route('sigap-absensi.rekap-harian.pdf', ['tanggal' => $tanggal]) }}"
         class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
        Export PDF
      </a>
    </form>
  </section>

  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">No</th>
            <th class="px-4 py-3 text-left">Nama</th>
            <th class="px-4 py-3 text-left">NIP</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Koordinat</th>
            <th class="px-4 py-3 text-left">Absensi</th>
            <th class="px-4 py-3 text-left">Keterangan</th>
            <th class="px-4 py-3 text-left">Foto</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($rekap as $i => $row)
            <tr>
              <td class="px-4 py-3">{{ $i + 1 }}</td>
              <td class="px-4 py-3 font-medium text-gray-900">
                {{ $row->user->name ?? '-' }}
              </td>
              <td class="px-4 py-3">
                {{ $row->user->nip ?? '-' }}
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border
                  {{ $row->is_outside_radius ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                  {{ $row->is_outside_radius ? 'DI LUAR RADIUS' : 'DALAM RADIUS' }}
                </span>
              </td>
              <td class="px-4 py-3 text-xs text-gray-600">
                {{ $row->latitude }}, {{ $row->longitude }}
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border bg-gray-50 border-gray-200 text-gray-700">
                  {{ $row->keterangan }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="text-[11px] leading-snug text-gray-500">
                  Absen Terverifikasi oleh SIGAP ABSENSI
                </div>
                <div class="text-[11px] text-gray-400 mt-0.5">
                  {{ \Carbon\Carbon::parse($row->absen_time)->format('H:i') }}
                </div>
              </td>
              <td class="px-4 py-3">
                @php
                  $photoPath = storage_path('app/public/' . $row->photo_path);
                @endphp

                @if(file_exists($photoPath))
                  <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($photoPath)) }}"
                       class="w-16 h-16 object-cover rounded-lg border border-gray-200"
                       alt="foto absensi">
                @else
                  <div class="w-16 h-16 rounded-lg border border-gray-200 bg-gray-100 flex items-center justify-center text-[10px] text-gray-500">
                    No Photo
                  </div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                Belum ada data absensi pada tanggal ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection