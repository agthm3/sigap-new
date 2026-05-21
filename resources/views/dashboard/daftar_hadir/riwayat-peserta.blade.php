@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">RIWAYAT PESERTA</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Cari nama peserta untuk melihat kegiatan yang pernah mereka ikuti.
    </p>
  </div>
</section>

{{-- FORM PENCARIAN --}}
<div class="rounded-2xl border border-gray-200 bg-white shadow-sm mt-4 p-4">
  <form method="GET" action="{{ route('sigap-daftar-hadir.riwayat-peserta') }}" class="flex gap-3">
    <input
      type="text"
      name="q"
      value="{{ $q }}"
      placeholder="Ketik nama peserta, contoh: Yusuf Sulaiman"
      class="flex-1 rounded-xl border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:border-maroon"
      autofocus
    >
    <button type="submit"
      class="px-5 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
      Cari
    </button>
  </form>
</div>

{{-- HASIL --}}
@if($q !== '')
  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
    <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
      <h2 class="font-semibold text-gray-900">
        Hasil pencarian: <span class="text-maroon">{{ $q }}</span>
      </h2>
      <span class="text-xs text-gray-500">{{ $results->count() }} nama ditemukan</span>
    </div>

    @if($results->isEmpty())
      <div class="px-4 py-8 text-center text-gray-500 text-sm">
        Tidak ada peserta dengan nama tersebut.
      </div>
    @else
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left">Nama</th>
              <th class="px-4 py-3 text-left">Instansi (terakhir)</th>
              <th class="px-4 py-3 text-center">Jumlah Kegiatan</th>
              <th class="px-4 py-3 text-left">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($results as $namaKey => $pesertaGroup)
              @php
                $first       = $pesertaGroup->first();
                $jumlah      = $pesertaGroup->count();
                $namaAsli    = $first->nama;
                $instansi    = $pesertaGroup->sortByDesc('created_at')->first()->instansi;
              @endphp
              <tr>
                <td class="px-4 py-3 font-medium text-gray-900">{{ $namaAsli }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $instansi }}</td>
                <td class="px-4 py-3 text-center">
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border bg-blue-50 border-blue-200 text-blue-700">
                    {{ $jumlah }} kegiatan
                  </span>
                </td>
                <td class="px-4 py-3">
                  <a href="{{ route('sigap-daftar-hadir.riwayat-peserta.detail', ['nama' => $namaAsli]) }}"
                     class="px-3 py-1.5 rounded border border-maroon text-maroon text-xs hover:bg-maroon hover:text-white transition-colors">
                    Lihat Riwayat
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endif
@endsection