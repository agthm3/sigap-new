@extends('layouts.app')

@section('content')
  <!-- Heading & Quick Actions -->
  <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Dashboard <span class="text-maroon">SIGAP Inovasi</span></h1>
      <p class="text-sm text-gray-600 mt-0.5">Ringkasan portofolio inovasi daerah, progres tahapan, dan aktivitas terbaru.</p>
    </div>
    <div class="flex flex-wrap gap-2">
      <a href="{{ route('sigap-inovasi.index') }}" class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm transition">Tambah Inovasi</a>
      <a href="{{ route('sigap-inovasi.konfigurasi') }}" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Konfigurasi</a>
    </div>
  </section>

  <!-- KPI Cards -->
  <section class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <div class="rounded-xl border bg-white p-4">
      <p class="text-xs text-gray-500">Total Inovasi</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $kpi['total'] }}</p>
      <p class="text-xs text-emerald-600 mt-1">+{{ $kpi['addedThisMonth'] }} inovasi bulan ini</p>
    </div>
    <div class="rounded-xl border bg-white p-4">
      <p class="text-xs text-gray-500">OPD Terlibat</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $kpi['opd'] }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $kpi['activeOpdWeek'] }} OPD aktif minggu ini</p>
    </div>
    <div class="rounded-xl border bg-white p-4">
      <p class="text-xs text-gray-500">Tahap Uji Coba</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $kpi['uji'] }}</p>
      <p class="text-xs text-gray-500 mt-1">+{{ $kpi['ujiDeltaWeek'] }} minggu ini</p>
    </div>
    <div class="rounded-xl border bg-white p-4">
      <p class="text-xs text-gray-500">Tahap Penerapan</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $kpi['terap'] }}</p>
      <p class="text-xs text-gray-500 mt-1">&nbsp;</p>
    </div>
  </section>

  <!-- Leaderboard OPD -->
  <section class="grid xl:grid-cols-3 gap-4 mt-4">
    <div class="rounded-xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Leaderboard OPD (Jumlah Inovasi)</div>
      <ul class="divide-y text-sm">
        @forelse($leaderboard as $row)
          <li class="p-4 flex items-center justify-between">
            <span>{{ $row->opd_unit }}</span>
            <span class="font-semibold text-maroon">{{ $row->total }}</span>
          </li>
        @empty
          <li class="p-4 text-gray-500">Belum ada data.</li>
        @endforelse
      </ul>
    </div>
  </section>

  <!-- Activity & Pending -->
  <section class="grid xl:grid-cols-3 gap-4 mt-4">
    <!-- Pengajuan Terbaru -->
    <div class="xl:col-span-2 rounded-xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700 flex items-center justify-between">
        <span>Pengajuan Terbaru</span>
        <a href="{{ route('sigap-inovasi.index') }}" class="text-maroon hover:underline">Lihat semua</a>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-fixed">
          <colgroup>
            <col class="w-[22%]" />
            <col class="w-[32%]" />
            <col class="w-[18%]" />
            <col class="w-[12%]" />
            <col class="w-[16%]" />
          </colgroup>
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-2">Waktu</th>
              <th class="px-4 py-2">Inovasi</th>
              <th class="px-4 py-2">OPD</th>
              <th class="px-4 py-2">Tahapan</th>
              <th class="px-4 py-2">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($recent as $r)
              <tr>
                <td class="px-4 py-2 text-gray-600">{{ $r->created_at->isoFormat('DD MMM YYYY • HH:mm') }}</td>
                <td class="px-4 py-2">
                  <a href="{{ route('sigap-inovasi.show', $r->id) }}" class="text-maroon hover:underline font-medium">{{ $r->judul }}</a>
                </td>
                <td class="px-4 py-2">{{ $r->opd_unit ?? '—' }}</td>
                <td class="px-4 py-2">{{ $r->tahap_inovasi ?? '—' }}</td>
                <td class="px-4 py-2">
                  @php $st = $r->asistensi_status ?? 'Menunggu Verifikasi'; @endphp
                  <span class="px-2 py-0.5 rounded text-xs
                    @class([
                      'bg-gray-100 text-gray-700' => $st==='Menunggu Verifikasi',
                      'bg-emerald-50 text-emerald-700' => $st==='Disetujui',
                      'bg-amber-50 text-amber-700' => in_array($st,['Revisi','Dikembalikan']),
                      'bg-rose-50 text-rose-700' => $st==='Ditolak',
                    ])">
                    {{ $st }}
                  </span>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="px-4 py-4 text-gray-500">Belum ada pengajuan.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <!-- Butuh Tindak Lanjut -->
    <div class="rounded-xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Butuh Tindak Lanjut</div>
      <ul class="divide-y text-sm">
        @forelse($needsFollowup as $nf)
          <li class="p-4">
            <div class="flex items-start gap-3">
              <span class="inline-flex w-8 h-8 items-center justify-center rounded-full
                @class([
                  'bg-amber-100 text-amber-700' => in_array($nf->asistensi_status,['Revisi','Dikembalikan']),
                  'bg-rose-100 text-rose-700' => $nf->asistensi_status==='Ditolak',
                ])">!</span>
              <div class="flex-1">
                <p class="font-semibold">
                  {{ $nf->asistensi_status==='Ditolak' ? 'Perbaiki & Ajukan Ulang – ' : 'Lengkapi Revisi – ' }}
                  <a href="{{ route('sigap-inovasi.show', $nf->id) }}" class="text-maroon hover:underline">{{ $nf->judul }}</a>
                </p>
                <p class="text-gray-600 text-xs mt-0.5">
                  Update: {{ optional($nf->asistensi_at)->isoFormat('DD MMM YYYY • HH:mm') ?: '—' }}
                  • OPD: {{ $nf->opd_unit ?: '—' }}
                </p>
                @if(!empty($nf->asistensi_note))
                  <p class="text-xs mt-1 p-2 rounded bg-gray-50 border">{{ \Illuminate\Support\Str::limit($nf->asistensi_note, 160) }}</p>
                @endif
                <div class="mt-2 flex gap-2">
                  <a href="{{ route('sigap-inovasi.show', $nf->id) }}" class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Buka</a>
                  @role('admin')
                    <a href="{{ route('sigap-inovasi.edit', $nf->id) }}" class="px-3 py-1.5 rounded-md border text-xs hover:bg-gray-50">Edit</a>
                  @endrole
                </div>
              </div>
            </div>
          </li>
        @empty
          <li class="p-4 text-gray-500">Tidak ada item yang butuh tindak lanjut.</li>
        @endforelse
      </ul>
    </div>
  </section>
@endsection
