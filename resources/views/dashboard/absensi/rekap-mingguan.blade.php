@extends('layouts.app')

@section('content')
<section class="mb-4">
  <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
    Rekap <span class="text-maroon">Mingguan</span>
  </h1>
  <p class="text-sm text-gray-600 mt-0.5">Ringkasan absensi per minggu.</p>
</section>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Periode</th>
          <th class="px-4 py-3 text-left">Total Absen</th>
          <th class="px-4 py-3 text-left">Terlambat</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($rekap as $r)
          <tr>
            <td class="px-4 py-3">
              {{ \Carbon\Carbon::parse($r->start_date)->format('d-m-Y') }}
              s/d
              {{ \Carbon\Carbon::parse($r->end_date)->format('d-m-Y') }}
            </td>
            <td class="px-4 py-3">{{ $r->total }}</td>
            <td class="px-4 py-3">{{ $r->terlambat }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-4 py-3 border-t bg-gray-50">
    {{ $rekap->onEachSide(1)->links() }}
  </div>
</div>
@endsection