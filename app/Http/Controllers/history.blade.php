@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      History <span class="text-maroon">Laporan PJLP</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      {{ $isAdminOrVerif ? 'Arsip seluruh logbook, rekapitulasi, dan dokumen gaji bulanan PJLP.' : 'Arsip riwayat logbook pekerjaan dan dokumen gaji Anda per periode bulan.' }}
    </p>
  </div>

  <!-- Filter Tahun & PJLP -->
  <form method="GET" action="{{ route('sigap-pjlp.history') }}" class="flex flex-wrap items-center gap-2">
    <!-- Filter PJLP (Khusus Admin/Verifikator) -->
    @if($isAdminOrVerif && $pjlpUsers->count() > 0)
      <select name="user_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border text-xs font-semibold focus:ring-0">
        <option value="">-- Semua PJLP --</option>
        @foreach($pjlpUsers as $u)
          <option value="{{ $u->id }}" {{ $filterUser == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
        @endforeach
      </select>
    @endif

    <!-- Filter Tahun -->
    <select name="tahun" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border text-xs font-semibold focus:ring-0">
      @for($y = date('Y'); $y >= 2024; $y--)
        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
      @endfor
    </select>
  </form>
</section>

<!-- Tabel History Riwayat Periode -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-xs mt-4">
  <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
    <h2 class="font-bold text-gray-900 text-sm">Riwayat Arsip Periode (Tahun {{ $tahun }})</h2>
    <span class="text-xs font-bold text-gray-500">{{ $periodes->count() }} Data Ditemukan</span>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-600 border-b border-gray-200">
        <tr>
          <th class="px-4 py-3.5 text-left font-bold">Bulan / Periode</th>
          @if($isAdminOrVerif)
            <th class="px-4 py-3.5 text-left font-bold">Nama PJLP</th>
          @endif
          <th class="px-4 py-3.5 text-center font-bold">Hari Kerja</th>
          <th class="px-4 py-3.5 text-center font-bold text-emerald-600">Disetujui</th>
          <th class="px-4 py-3.5 text-center font-bold text-red-600">Ditolak</th>
          <th class="px-4 py-3.5 text-center font-bold">Daftar Gaji</th>
          <th class="px-4 py-3.5 text-center font-bold">Status Laporan</th>
          <th class="px-4 py-3.5 text-center font-bold w-44">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($periodes as $item)
          <tr class="hover:bg-gray-50/80 transition-colors">
            <!-- Periode Bulan -->
            <td class="px-4 py-3.5 whitespace-nowrap">
              <div class="font-bold text-gray-900">
                {{ \Carbon\Carbon::createFromFormat('Y-m', $item->bulan_tahun)->translatedFormat('F Y') }}
              </div>
              <div class="text-[11px] text-gray-400 font-medium">Periode: {{ $item->bulan_tahun }}</div>
            </td>

            <!-- Nama PJLP (Hanya jika Admin/Verifikator) -->
            @if($isAdminOrVerif)
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-2.5">
                  @if($item->user && $item->user->profile_photo_path)
                    <img src="{{ asset('storage/' . $item->user->profile_photo_path) }}" alt="Foto" class="w-7 h-7 rounded-full object-cover shrink-0 ring-1 ring-gray-200">
                  @else
                    <div class="w-7 h-7 rounded-full bg-maroon/10 text-maroon font-bold flex items-center justify-center text-[10px] shrink-0">
                      {{ $item->user ? substr($item->user->name, 0, 1) : '-' }}
                    </div>
                  @endif
                  <div>
                    <div class="font-bold text-gray-800">{{ $item->user->name ?? '-' }}</div>
                    <div class="text-[11px] text-gray-400">{{ $item->user->email ?? '' }}</div>
                  </div>
                </div>
              </td>
            @endif

            <!-- Hari Kerja & Terisi -->
            <td class="px-4 py-3.5 text-center whitespace-nowrap font-medium text-gray-700">
              <b>{{ $item->terisi }}</b> / {{ $item->total_hari }} Hari
            </td>

            <!-- Terverifikasi -->
            <td class="px-4 py-3.5 text-center font-bold text-emerald-600">
              {{ $item->terverifikasi }}
            </td>

            <!-- Ditolak -->
            <td class="px-4 py-3.5 text-center font-bold text-red-600">
              {{ $item->ditolak }}
            </td>

            <!-- Dokumen Gaji -->
            <td class="px-4 py-3.5 text-center whitespace-nowrap">
              @if($item->has_gaji)
                <a href="{{ asset('storage/' . $item->file_daftar_gaji) }}" target="_blank"
                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700 hover:bg-emerald-100">
                  📄 Ada PDF
                </a>
              @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-400">
                  Belum Ada
                </span>
              @endif
            </td>

            <!-- Status Laporan -->
            <td class="px-4 py-3.5 text-center whitespace-nowrap">
              @if($item->is_lengkap)
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                  Siap Export
                </span>
              @else
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium bg-amber-50 border border-amber-200 text-amber-700">
                  Belum Lengkap ({{ $item->persen }}%)
                </span>
              @endif
            </td>

            <!-- Tombol Aksi -->
            <td class="px-4 py-3.5 text-center whitespace-nowrap">
              <div class="flex items-center justify-center gap-1.5">
                <!-- Tombol Buka Detail/Logbook -->
                @if($isAdminOrVerif)
                  <a href="{{ route('sigap-pjlp.show-user', ['userId' => $item->user->id, 'bulan_tahun' => $item->bulan_tahun]) }}"
                     class="px-2.5 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold transition">
                    Lihat
                  </a>
                @else
                  <a href="{{ route('sigap-pjlp.index', ['bulan_tahun' => $item->bulan_tahun]) }}"
                     class="px-2.5 py-1 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold transition">
                    Buka
                  </a>
                @endif

                <!-- Tombol Export PDF -->
                @if($item->is_lengkap)
                  <a href="{{ route('sigap-pjlp.export-pdf', $item->id) }}"
                     class="px-2.5 py-1 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-xs font-bold transition shadow-2xs">
                    Export PDF
                  </a>
                @else
                  <button type="button"
                          onclick="Swal.fire('Belum Lengkap', 'Periode ini belum melengkapi seluruh logbook dan dokumen gaji.', 'info')"
                          class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-400 text-xs font-medium cursor-not-allowed">
                    Export PDF
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ $isAdminOrVerif ? 8 : 7 }}" class="px-4 py-8 text-center text-gray-500 text-xs">
              Belum ada riwayat laporan pada tahun {{ $tahun }}.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection