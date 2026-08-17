@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Monitoring <span class="text-maroon">SIGAP PJLP</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pantau progres pengisian logbook harian dan dokumen gaji seluruh PJLP.
    </p>
  </div>

  <div class="flex flex-wrap items-center gap-3">
    <!-- Filter Periode Bulan -->
    <form method="GET" action="{{ route('sigap-pjlp.monitoring') }}" class="flex items-center gap-2">
      <input type="hidden" name="search" value="{{ $search }}">
      <label for="bulan_tahun" class="text-xs font-semibold text-gray-500">Periode:</label>
      <input type="month" id="bulan_tahun" name="bulan_tahun" value="{{ $bulanTahun }}" 
             onchange="this.form.submit()"
             class="px-3 py-1.5 rounded-xl border text-sm font-semibold focus:ring-0">
    </form>
  </div>
</section>

<!-- Ringkasan Kartu Atas -->
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Total PJLP</p>
    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $summary['total_pjlp'] }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Sudah Lengkap</p>
    <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $summary['lengkap'] }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-amber-600">Belum Lengkap</p>
    <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ $summary['belum_lengkap'] }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Menunggu Verif</p>
    <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $summary['total_menunggu'] }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-red-600">Ditolak</p>
    <h3 class="text-2xl font-extrabold text-red-600 mt-1">{{ $summary['total_ditolak'] }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-xs">
    <p class="text-[11px] font-bold uppercase tracking-wider text-purple-600">Terverifikasi</p>
    <h3 class="text-2xl font-extrabold text-purple-600 mt-1">{{ $summary['total_terverifikasi'] }}</h3>
  </div>
</div>

<!-- Tabel Monitoring PJLP -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-xs mt-4">
  <div class="px-5 py-4 border-b bg-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
      <h2 class="font-bold text-gray-900 text-sm">Daftar PJLP & Capaian Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulanTahun)->translatedFormat('F Y') }}</h2>
      <p class="text-xs text-gray-500 mt-0.5">Klik tombol <b>"Periksa Logbook"</b> untuk memverifikasi atau mengisi atas nama.</p>
    </div>

    <!-- Search PJLP -->
    <form method="GET" action="{{ route('sigap-pjlp.monitoring') }}" class="flex items-center gap-2">
      <input type="hidden" name="bulan_tahun" value="{{ $bulanTahun }}">
      <div class="relative">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama PJLP..." 
               class="pl-8 pr-3 py-1.5 rounded-xl border text-xs focus:ring-0 w-48 sm:w-60">
        <span class="absolute left-2.5 top-2 text-gray-400 text-xs">🔍</span>
      </div>
      <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white rounded-xl text-xs font-semibold">Cari</button>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-600 border-b border-gray-200">
        <tr>
          <th class="px-4 py-3.5 text-left font-bold">Nama PJLP</th>
          <th class="px-4 py-3.5 text-center font-bold">Hari Kerja</th>
          <th class="px-4 py-3.5 text-center font-bold">Terisi</th>
          <th class="px-4 py-3.5 text-center font-bold text-emerald-600">Disetujui</th>
          <th class="px-4 py-3.5 text-center font-bold text-blue-600">Menunggu</th>
          <th class="px-4 py-3.5 text-center font-bold text-red-600">Ditolak</th>
          <th class="px-4 py-3.5 text-center font-bold">Daftar Gaji</th>
          <th class="px-4 py-3.5 text-left font-bold w-40">Progres</th>
          <th class="px-4 py-3.5 text-center font-bold w-28">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($monitoringData as $item)
          <tr class="hover:bg-gray-50/80 transition-colors">
            <!-- Nama PJLP -->
            <td class="px-4 py-3.5">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-maroon/10 text-maroon font-bold flex items-center justify-center text-xs shrink-0">
                  {{ substr($item->user->name, 0, 1) }}
                </div>
                <div>
                  <div class="font-bold text-gray-900">{{ $item->user->name }}</div>
                  <div class="text-[11px] text-gray-500">{{ $item->user->email }}</div>
                </div>
              </div>
            </td>

            <td class="px-4 py-3.5 text-center font-semibold">{{ $item->total_hari }}</td>
            <td class="px-4 py-3.5 text-center font-semibold text-gray-800">{{ $item->terisi }}</td>
            <td class="px-4 py-3.5 text-center font-bold text-emerald-600">{{ $item->terverifikasi }}</td>
            <td class="px-4 py-3.5 text-center font-bold text-blue-600">{{ $item->menunggu }}</td>
            <td class="px-4 py-3.5 text-center font-bold text-red-600">{{ $item->ditolak }}</td>

            <!-- Status Dokumen Gaji -->
            <td class="px-4 py-3.5 text-center">
              @if($item->has_gaji)
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                  ✓ PDF Ada
                </span>
              @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-400">
                  Belum Ada
                </span>
              @endif
            </td>

            <!-- Progres Bar -->
            <td class="px-4 py-3.5">
              <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div class="bg-maroon h-2 rounded-full transition-all duration-300" style="width: {{ $item->persen_progress }}%"></div>
              </div>
              <div class="flex justify-between items-center text-[10px] font-bold text-gray-500 mt-1">
                <span>{{ $item->persen_progress }}% Selesai</span>
                @if($item->is_lengkap)
                  <span class="text-emerald-600 font-extrabold">Lengkap</span>
                @endif
              </div>
            </td>

            <!-- Tombol Buka Detail & Verifikasi -->
            <td class="px-4 py-3.5 text-center">
              <a href="{{ route('sigap-pjlp.show-user', ['userId' => $item->user->id, 'bulan_tahun' => $bulanTahun]) }}"
                 class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-maroon text-maroon hover:bg-maroon hover:text-white text-xs font-bold transition shadow-2xs">
                Periksa
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="px-4 py-8 text-center text-gray-500 text-xs">
              Tidak ditemukan data PJLP pada sistem.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection