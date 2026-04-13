@extends('layouts.app')

@section('title', 'SIGAP INKUBATORMA — Dashboard')

@push('styles')
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { maroon: '#7a2222' }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, sans-serif; }
    tr[data-row] { transition: opacity .15s ease; }

    .table-col-actions {
      width: 180px;
      min-width: 180px;
    }

    .action-group {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 4px;
      flex-wrap: nowrap;
    }

    .action-item {
      position: relative;
      display: inline-flex;
      flex-shrink: 0;
    }

    .action-btn {
      width: 30px;
      height: 30px;
      min-width: 30px;
      min-height: 30px;
      padding: 0;
      border: none;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: opacity .15s ease, transform .1s ease;
      overflow: hidden;
      flex-shrink: 0;
    }

    .action-btn:hover {
      opacity: 0.85;
      transform: scale(1.07);
    }

    /* .action-btn svg {
      width: 14px !important;
      height: 14px !important;
      max-width: 14px;
      max-height: 14px;
      display: block;
      flex-shrink: 0;
      pointer-events: none;
    } */

    .action-btn-disabled {
      background: #e5e7eb !important;
      color: #9ca3af !important;
      cursor: not-allowed;
      transform: none !important;
    }

    /* Warna per aksi */
    .btn-detail   { background: #1e3a5f; color: #fff; }
    .btn-verify   { background: #7a2222; color: #fff; }
    .btn-record   { background: #0f766e; color: #fff; }
    .btn-edit     { background: #f59e0b; color: #fff; }
    .btn-delete   { background: #ef4444; color: #fff; }

    /* Tooltip */
    .action-tooltip {
      position: absolute;
      bottom: calc(100% + 7px);
      left: 50%;
      transform: translateX(-50%);
      background: #1f2937;
      color: #fff;
      font-size: 11px;
      font-weight: 600;
      padding: 5px 9px;
      border-radius: 7px;
      white-space: nowrap;
      opacity: 0;
      visibility: hidden;
      pointer-events: none;
      z-index: 99;
      box-shadow: 0 4px 12px rgba(0,0,0,.15);
      transition: opacity .15s ease, visibility .15s ease;
    }

    .action-tooltip::after {
      content: '';
      position: absolute;
      top: 100%;
      left: 50%;
      transform: translateX(-50%);
      border: 5px solid transparent;
      border-top-color: #1f2937;
    }

    .action-item:hover .action-tooltip {
      opacity: 1;
      visibility: visible;
    }
  </style>
@endpush

@section('content')
@php
  use App\Models\Inkubatorma;

  $me = auth()->user();
  $isAdmin = $me && $me->hasRole('admin');
  $isVerifikator = $me && $me->hasAnyRole(['verifikator_inkubatorma']);
  $isUser = $me && $me->hasRole('user');

  $statusMenunggu           = Inkubatorma::STATUS_MENUNGGU ?? 'Menunggu';
  $statusAkanDijadwalkan    = Inkubatorma::STATUS_AKAN_DIJADWALKAN ?? 'Akan Dijadwalkan';
  $statusTerjadwal          = Inkubatorma::STATUS_TERJADWAL ?? 'Terjadwal';
  $statusSesiKonsultasi     = Inkubatorma::STATUS_SESI_KONSULTASI ?? 'Sesi Konsultasi';
  $statusDijadwalkanUlang   = Inkubatorma::STATUS_DIJADWALKAN_ULANG ?? 'Dijadwalkan Ulang';
  $statusDitolak            = Inkubatorma::STATUS_DITOLAK ?? 'Ditolak';
  $statusSelesai            = Inkubatorma::STATUS_SELESAI ?? 'Selesai';

  $countMenunggu  = (int) ($ringkasanStatus[$statusMenunggu] ?? $ringkasanStatus['Menunggu'] ?? 0);
  $countTerjadwal = (int) ($ringkasanStatus[$statusTerjadwal] ?? $ringkasanStatus['Terjadwal'] ?? 0);
  $countSesi      = (int) ($ringkasanStatus[$statusSesiKonsultasi] ?? $ringkasanStatus['Sesi Konsultasi'] ?? 0);
  $countSelesai   = (int) ($ringkasanStatus[$statusSelesai] ?? $ringkasanStatus['Selesai'] ?? 0);

  $lineLabels = $line['labels'] ?? [];
  $lineValues = $line['values'] ?? [];

  $pieLabels = collect($pieLayanan ?? [])->pluck('label')->values()->all();
  $pieValues = collect($pieLayanan ?? [])->pluck('total')->map(fn($v)=>(int)$v)->values()->all();

  $opdLabels = collect($opdCounts ?? [])->pluck('label')->values()->all();
  $opdValues = collect($opdCounts ?? [])->pluck('total')->map(fn($v)=>(int)$v)->values()->all();

  $updatedLabel = $updatedAtLabel ?? (\Carbon\Carbon::now('Asia/Makassar')->translatedFormat('d M Y H:i') . ' WITA');
@endphp

@if(session('success'))
  <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
  </div>
@endif

<section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Dashboard <span class="text-maroon">SIGAP Inkubatorma</span>
    </h1>
    <p class="text-sm text-gray-500">Inspirasi Kreatif untuk Berbagi Treatment Inovasi Riset Makassar</p>
  </div>

  <div class="flex flex-wrap items-center gap-2">
    <a href="{{ route('sigap-inkubatorma.index') }}"
       class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:bg-maroon/90">
      + Isi Form Pengajuan
    </a>

    <a href="{{ route('sigap-inkubatorma.dashboard.print', request()->query()) }}"
        target="_blank"
        class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
      Export Laporan
    </a>
  </div>
</section>

<section class="mt-6 space-y-4">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <h2 class="text-xl font-extrabold text-gray-900">Ringkasan</h2>
      <p class="text-sm text-gray-500">Snapshot statistik pengajuan & distribusi konsultasi.</p>
    </div>

    <div class="flex flex-wrap items-center gap-2 justify-end">
      <span class="text-sm font-semibold text-gray-700 mr-1">Periode:</span>

      <div class="inline-flex rounded-xl border border-gray-200 bg-white p-1">
        <button type="button" data-period="overall"
          class="px-4 py-2 rounded-lg text-sm font-semibold {{ $period === 'overall' ? 'bg-maroon text-white' : 'text-gray-700 hover:bg-gray-50' }}">
          Overall
        </button>
        <button type="button" data-period="yearly"
          class="px-4 py-2 rounded-lg text-sm font-semibold {{ $period === 'yearly' ? 'bg-maroon text-white' : 'text-gray-700 hover:bg-gray-50' }}">
          Tahunan
        </button>
        <button type="button" data-period="monthly"
          class="px-4 py-2 rounded-lg text-sm font-semibold {{ $period === 'monthly' ? 'bg-maroon text-white' : 'text-gray-700 hover:bg-gray-50' }}">
          Bulanan
        </button>
      </div>

      <select id="periodYear"
        class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
        @php
          $yNow = (int) now('Asia/Makassar')->year;
          $years = range($yNow - 3, $yNow + 1);
        @endphp
        @foreach($years as $y)
          <option value="{{ $y }}" @selected((int)$year === (int)$y)>{{ $y }}</option>
        @endforeach
      </select>

      <select id="periodMonth"
        class="rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm {{ $period === 'monthly' ? '' : 'hidden' }}">
        @for($m=1;$m<=12;$m++)
          <option value="{{ $m }}" @selected((int)$month === (int)$m)>
            {{ \Carbon\Carbon::create(2000, $m, 1)->translatedFormat('F') }}
          </option>
        @endfor
      </select>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-stretch">

    {{-- RINGKASAN STATUS --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 h-full">
      <div class="flex items-start justify-between gap-4">
        <div>
          <h3 class="text-sm font-extrabold tracking-wide text-gray-900 uppercase">
            Ringkasan Status Konsultasi
          </h3>
          <p class="text-xs text-gray-500 mt-1">
            Total pengajuan berdasarkan status utama.
          </p>
        </div>
        <div class="h-10 w-10 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v6H2v-6zM8 3a1 1 0 011-1h2a1 1 0 011 1v14H8V3zM14 7a1 1 0 011-1h2a1 1 0 011 1v10h-4V7z"/>
          </svg>
        </div>
      </div>
  
      {{-- Mobile: grid 2x2 --}}
      <div class="mt-4 grid grid-cols-2 gap-3 lg:hidden">
        <div class="rounded-xl border border-gray-200 p-3 text-center">
          <p class="text-xs font-bold text-gray-500 uppercase">Menunggu</p>
          <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $countMenunggu }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-3 text-center">
          <p class="text-xs font-bold text-gray-500 uppercase">Terjadwal</p>
          <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $countTerjadwal }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-3 text-center">
          <p class="text-xs font-bold text-gray-500 uppercase">Sesi</p>
          <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $countSesi }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-3 text-center">
          <p class="text-xs font-bold text-gray-500 uppercase">Selesai</p>
          <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $countSelesai }}</p>
        </div>
      </div>
  
      {{-- Desktop: list rows --}}
      <div class="mt-5 rounded-xl border border-gray-200 overflow-hidden hidden lg:block">
        <div class="px-4 py-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase">Menunggu</p>
            <p class="mt-1 text-sm text-gray-600">Pengajuan menunggu proses.</p>
          </div>
          <div class="text-3xl font-extrabold text-gray-900">{{ $countMenunggu }}</div>
        </div>
        <div class="h-px bg-gray-200"></div>
  
        <div class="px-4 py-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase">Terjadwal</p>
            <p class="mt-1 text-sm text-gray-600">Pengajuan sudah punya jadwal.</p>
          </div>
          <div class="text-3xl font-extrabold text-gray-900">{{ $countTerjadwal }}</div>
        </div>
        <div class="h-px bg-gray-200"></div>
  
        <div class="px-4 py-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase">Sesi Konsultasi</p>
            <p class="mt-1 text-sm text-gray-600">Konsultasi sedang berjalan.</p>
          </div>
          <div class="text-3xl font-extrabold text-gray-900">{{ $countSesi }}</div>
        </div>
        <div class="h-px bg-gray-200"></div>
  
        <div class="px-4 py-4 flex items-center justify-between">
          <div>
            <p class="text-xs font-bold text-gray-500 uppercase">Selesai</p>
            <p class="mt-1 text-sm text-gray-600">Konsultasi telah ditutup.</p>
          </div>
          <div class="text-3xl font-extrabold text-gray-900">{{ $countSelesai }}</div>
        </div>
      </div>
    </div>
  
    {{-- CHART: JUMLAH PENGAJUAN --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:col-span-2">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm font-extrabold text-gray-900">Jumlah Pengajuan</p>
          <p class="text-xs text-gray-500">
            @if(($period ?? 'overall') === 'monthly')
              Distribusi jumlah pengajuan per hari dalam bulan terpilih.
            @elseif(($period ?? 'overall') === 'yearly')
              Distribusi jumlah pengajuan per bulan dalam tahun terpilih.
            @else
              Tren pengajuan sepanjang waktu.
            @endif
          </p>
        </div>
        <span class="text-xs text-gray-400 shrink-0">Updated: {{ $updatedLabel }}</span>
      </div>
  
      <div class="mt-4 h-[220px] lg:h-[420px]">
        <canvas id="chartSubmissions" class="w-full h-full"></canvas>
      </div>
    </div>
  
    {{-- CHART: PERSEBARAN LAYANAN --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm font-extrabold text-gray-900">Persebaran Layanan</p>
          <p class="text-xs text-gray-500">Distribusi jenis layanan.</p>
        </div>
      </div>
  
      @if(count($pieLabels))
        <div class="mt-4 h-[260px] lg:h-[420px]">
          <canvas id="chartLayanan" class="w-full h-full"></canvas>
        </div>
      @else
        <div class="mt-4 rounded-xl border border-dashed border-gray-200 p-6 text-sm text-gray-500">
          Belum ada data pada periode ini.
        </div>
      @endif
    </div>
  
    {{-- CHART: PERSEBARAN OPD --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-5 lg:col-span-4">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-sm font-extrabold text-gray-900">Persebaran OPD Pengaju</p>
          <p class="text-xs text-gray-500">Top 10 OPD/Unit yang paling sering mengajukan.</p>
        </div>
      </div>
  
      @if(count($opdLabels))
        <div class="mt-4 h-[220px] lg:h-[380px]">
          <canvas id="chartOpd" class="w-full h-full"></canvas>
        </div>
      @else
        <div class="mt-4 rounded-xl border border-dashed border-gray-200 p-6 text-sm text-gray-500">
          Belum ada data pada periode ini.
        </div>
      @endif
    </div>
  
  </div>
</section>

<section class="bg-white rounded-xl border border-gray-200 p-5 mt-6">
  <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
    <div>
      <label class="text-xs font-semibold text-gray-600">Cari</label>
      <input id="qSearch" type="text" placeholder="Judul / Pengaju / OPD"
             class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">Status</label>
      <select id="qStatus" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="Semua">Semua</option>
        <option value="{{ $statusMenunggu }}">{{ $statusMenunggu }}</option>
        <option value="{{ $statusAkanDijadwalkan }}">{{ $statusAkanDijadwalkan }}</option>
        <option value="{{ $statusTerjadwal }}">{{ $statusTerjadwal }}</option>
        <option value="{{ $statusSesiKonsultasi }}">{{ $statusSesiKonsultasi }}</option>
        <option value="{{ $statusDijadwalkanUlang }}">{{ $statusDijadwalkanUlang }}</option>
        <option value="{{ $statusDitolak }}">{{ $statusDitolak }}</option>
        <option value="{{ $statusSelesai }}">Tutup/Selesai</option>
      </select>
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">Layanan</label>
      <select id="qLayanan" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="Semua">Semua</option>
        @foreach($layananOptions as $val => $label)
          <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">Urutkan</label>
      <select id="qSort" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="Terbaru">Terbaru</option>
        <option value="Terlama">Terlama</option>
      </select>
    </div>

    <div class="flex items-end gap-2">
      <button id="btnApply"
              class="w-1/2 md:w-auto px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold">
        Terapkan
      </button>
      <button id="btnReset"
              class="w-1/2 md:w-auto px-4 py-2 rounded-lg border border-gray-300 text-sm">
        Reset
      </button>
    </div>
  </div>
</section>

<section class="bg-white rounded-xl border border-gray-200 overflow-hidden mt-6">
  <div class="px-5 py-4 border-b flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-sm text-gray-500">
    <span id="resultInfo">
      Menampilkan {{ $inkubatormas->count() }} konsultasi
    </span>
    <div class="flex items-center gap-2">
      <span class="whitespace-nowrap">Tampilkan</span>
      <select id="pageSize" class="rounded border border-gray-300 px-2 py-1 text-sm">
        @php $perPage = (int) request('per_page', 10); @endphp
        <option value="10" @selected($perPage === 10)>10</option>
        <option value="25" @selected($perPage === 25)>25</option>
        <option value="50" @selected($perPage === 50)>50</option>
      </select>
    </div>
  </div>

  <div class="w-full overflow-x-auto">
    <table class="min-w-[980px] w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="text-left px-5 py-3 font-semibold">Judul</th>
          <th class="text-left px-5 py-3 font-semibold">Pengaju</th>
          <th class="text-left px-5 py-3 font-semibold">OPD</th>
          <th class="text-left px-5 py-3 font-semibold">Layanan</th>
          <th class="text-left px-5 py-3 font-semibold">Status</th>
          <th class="text-left px-5 py-3 font-semibold whitespace-nowrap">Aksi</th>
        </tr>
      </thead>

      <tbody id="tbody" class="divide-y">
        @forelse ($inkubatormas as $row)
          @php
            $layananIds = $row->layanan_id ?? [];
            if (!is_array($layananIds)) {
                $layananIds = [$layananIds];
            }
          
            $layananLabel = collect($layananIds)
                ->map(function ($id) use ($layananOptions, $row) {
                    if ($id === 'lainnya' && !empty($row->layanan_lainnya)) {
                        return ($layananOptions[$id] ?? 'Lainnya') . ' • ' . $row->layanan_lainnya;
                    }
                    return $layananOptions[$id] ?? $id;
                })
                ->implode(', ');
          
            $layananFilterValue = implode('|', $layananIds);
          
            $status = $row->status ?? $statusMenunggu;
          
            $canEdit = in_array($status, [$statusMenunggu, $statusAkanDijadwalkan], true);
            $canVerify = ($status !== $statusSelesai);
            $canOpenRecord = in_array($status, [$statusSesiKonsultasi, $statusSelesai], true);
          @endphp

          <tr data-row
              data-judul="{{ $row->judul_konsultasi ?? '' }}"
              data-pengaju="{{ $row->nama_pengaju ?? '' }}"
              data-opd="{{ $row->opd_unit ?? '' }}"
              data-layanan="{{ $layananFilterValue }}"
              data-status="{{ $row->status ?? '' }}"
              data-created="{{ optional($row->created_at)->format('Y-m-d') ?? '' }}">

            <td class="px-5 py-4 font-semibold text-gray-800 whitespace-normal">
              {{ $row->judul_konsultasi ?? '—' }}
            </td>

            <td class="px-5 py-4 whitespace-normal">
              {{ $row->nama_pengaju ?? '—' }}
            </td>

            <td class="px-5 py-4 whitespace-normal">
              {{ $row->opd_unit ?? '—' }}
            </td>

            <td class="px-5 py-4 whitespace-normal">
              <div class="flex flex-wrap gap-1">
                @foreach ($layananIds as $id)
                  @if($id !== 'lainnya')
                    <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                      {{ $layananOptions[$id] ?? $id }}
                    </span>
                  @endif
                @endforeach

                @if(in_array('lainnya', $layananIds) && $row->layanan_lainnya)
                  <span class="px-2 py-1 rounded bg-maroon/10 text-maroon text-xs">
                    Lainnya • {{ $row->layanan_lainnya }}
                  </span>
                @endif
              </div>
            </td>

            <td class="px-5 py-4">
              <span class="px-2 py-1 rounded text-xs font-semibold {{ $row->status_badge_class }}">
                {{ $row->status }}
              </span>
            </td>

            <td class="px-5 py-4 text-right align-middle" style="width:160px; min-width:160px;">
              <div style="display:flex; align-items:center; justify-content:flex-end; gap:4px; flex-wrap:nowrap;">
            
                {{-- DETAIL --}}
                <div style="position:relative; display:inline-flex;">
                  <a href="{{ route('sigap-inkubatorma.detail', ['id' => $row->id]) }}"
                     style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#1e3a5f;color:#fff;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:opacity .15s;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                  </a>
                  <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;" class="tip">Detail</span>
                </div>
            
                {{-- VERIFIKASI --}}
                @if($isAdmin || $isVerifikator)
                  <div style="position:relative; display:inline-flex;">
                    @if($canVerify)
                      <a href="{{ route('sigap-inkubatorma.verifikasi', ['id' => $row->id]) }}"
                         style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#7a2222;color:#fff;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:opacity .15s;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/>
                        </svg>
                      </a>
                      <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;" class="tip">Verifikasi</span>
                    @else
                      <button type="button" disabled
                         style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#e5e7eb;color:#9ca3af;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:not-allowed;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 12c0 4.97-4.03 9-9 9S3 16.97 3 12 7.03 3 12 3s9 4.03 9 9z"/>
                        </svg>
                      </button>
                      <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;" class="tip">Selesai/nonaktif</span>
                    @endif
                  </div>
                @endif
            
                {{-- RECORD --}}
                @if($isAdmin || $isVerifikator || $isUser)
                <div style="position:relative; display:inline-flex;">
                  @if($canOpenRecord)
                    <a href="{{ route('sigap-inkubatorma.records', ['id' => $row->id]) }}"
                      style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#0f766e;color:#fff;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:opacity .15s;"
                      onmouseenter="this.nextElementSibling.style.opacity='1';this.nextElementSibling.style.visibility='visible';"
                      onmouseleave="this.nextElementSibling.style.opacity='0';this.nextElementSibling.style.visibility='hidden';">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h10l4 4v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                      </svg>
                    </a>
                    <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;">Record Konsultasi</span>
                  @else
                    <button type="button" disabled
                      style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#e5e7eb;color:#9ca3af;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:not-allowed;"
                      onmouseenter="this.nextElementSibling.style.opacity='1';this.nextElementSibling.style.visibility='visible';"
                      onmouseleave="this.nextElementSibling.style.opacity='0';this.nextElementSibling.style.visibility='hidden';">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6M9 16h6M9 8h6"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3h10l4 4v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                      </svg>
                    </button>
                    <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;">Belum ada record</span>
                  @endif
                </div>
                @endif
            
                {{-- EDIT --}}
                @if($isAdmin || $isUser)
                  <div style="position:relative; display:inline-flex;">
                    @if($canEdit)
                      <a href="{{ route('sigap-inkubatorma.edit', ['id' => $row->id]) }}"
                         style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#f59e0b;color:#fff;display:inline-flex;align-items:center;justify-content:center;text-decoration:none;transition:opacity .15s;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                        </svg>
                      </a>
                      <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;" class="tip">Edit</span>
                    @else
                      <button type="button" disabled
                         style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#e5e7eb;color:#9ca3af;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:not-allowed;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
                        </svg>
                      </button>
                      <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;" class="tip">Edit nonaktif</span>
                    @endif
                  </div>
                @endif
            
                {{-- HAPUS --}}
                @if($isAdmin || $isUser)
                  <div style="position:relative; display:inline-flex;">
                    <form action="{{ route('sigap-inkubatorma.destroy', $row->id) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?')" style="display:inline-flex;">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                         style="width:30px;height:30px;min-width:30px;border-radius:6px;background:#ef4444;color:#fff;border:none;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:opacity .15s;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" xmlns="http://www.w3.org/2000/svg">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M3 6h18"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M8 6V4a1 1 0 011-1h6a1 1 0 011 1v2"/>
                          <path stroke-linecap="round" stroke-linejoin="round" d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                      </button>
                    </form>
                    <span style="position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:#1f2937;color:#fff;font-size:11px;font-weight:600;padding:4px 8px;border-radius:6px;white-space:nowrap;opacity:0;visibility:hidden;pointer-events:none;z-index:99;transition:opacity .15s;" class="tip">Hapus</span>
                  </div>
                @endif
            
              </div>
            </td>
          </tr>

        @empty
          <tr>
            <td colspan="6" class="text-center py-10 text-gray-500">
              Belum ada data pengajuan.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-5 py-4 border-t flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-sm text-gray-500">
    <span id="pageInfo">
      Halaman {{ $inkubatormas->currentPage() }} dari {{ $inkubatormas->lastPage() }}
    </span>

    <div class="flex items-center gap-2 justify-end">
      @if($inkubatormas->onFirstPage())
        <button type="button" disabled
          class="px-3 py-1 rounded border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">‹</button>
      @else
        <a href="{{ $inkubatormas->previousPageUrl() }}"
          class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">‹</a>
      @endif

      @if($inkubatormas->hasMorePages())
        <a href="{{ $inkubatormas->nextPageUrl() }}"
          class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">›</a>
      @else
        <button type="button" disabled
          class="px-3 py-1 rounded border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">›</button>
      @endif
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
(function () {
  const lineLabels = @json($lineLabels);
  const lineValues = @json($lineValues);
  const pieLabels  = @json($pieLabels);
  const pieValues  = @json($pieValues);
  const opdLabels  = @json($opdLabels);
  const opdValues  = @json($opdValues);

  (function () {
    const ctx = document.getElementById('chartSubmissions');
    if (!ctx) return;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: lineLabels,
        datasets: [{
          label: 'Pengajuan',
          data: lineValues,
          tension: 0.35,
          fill: true,
          borderColor: '#7a2222',
          backgroundColor: 'rgba(122,34,34,0.12)',
          pointRadius: 3,
          pointHoverRadius: 5,
          pointBackgroundColor: '#7a2222'
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(17,24,39,.92)',
            padding: 10,
            titleColor: '#fff',
            bodyColor: '#fff'
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { color: '#6b7280', font: { size: 11 } }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(156,163,175,.25)' },
            ticks: { color: '#6b7280', font: { size: 11 }, precision: 0 }
          }
        }
      }
    });
  })();

  (function () {
    const ctx = document.getElementById('chartLayanan');
    if (!ctx || !pieLabels.length) return;

    const palette = [
      '#7a2222', '#a14a4a', '#d08a8a', '#6b7280', '#9ca3af',
      '#b45309', '#0f766e', '#1d4ed8', '#7c3aed', '#059669'
    ];

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: pieLabels,
        datasets: [{
          data: pieValues,
          backgroundColor: pieValues.map((_, i) => palette[i % palette.length]),
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 10,
              boxHeight: 10,
              color: '#374151',
              font: { size: 11 }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(17,24,39,.92)',
            titleColor: '#fff',
            bodyColor: '#fff'
          }
        }
      }
    });
  })();

  (function () {
    const ctx = document.getElementById('chartOpd');
    if (!ctx || !opdLabels.length) return;

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: opdLabels,
        datasets: [{
          label: 'Jumlah Pengajuan',
          data: opdValues,
          backgroundColor: 'rgba(122,34,34,0.18)',
          borderColor: '#7a2222',
          borderWidth: 1,
          borderRadius: 10,
          maxBarThickness: 38
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: 'rgba(17,24,39,.92)',
            titleColor: '#fff',
            bodyColor: '#fff'
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              color: '#6b7280',
              font: { size: 11 },
              maxRotation: 0,
              callback: function(value) {
                const label = this.getLabelForValue(value) || '';
                return label.length > 22 ? label.slice(0, 22) + '…' : label;
              }
            }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(156,163,175,.25)' },
            ticks: { color: '#6b7280', font: { size: 11 }, precision: 0 }
          }
        }
      }
    });
  })();

  const $ = (id) => document.getElementById(id);

  const qSearch  = $('qSearch');
  const qStatus  = $('qStatus');
  const qLayanan = $('qLayanan');
  const qSort    = $('qSort');
  const btnApply = $('btnApply');
  const btnReset = $('btnReset');
  const tbody = $('tbody');
  const resultInfo = $('resultInfo');

  function norm(s){ return (s || '').toString().toLowerCase().trim(); }

  const rows = Array.from(tbody.querySelectorAll('tr[data-row]'));

  function applyFilters() {
    const text = norm(qSearch.value);
    const status = qStatus.value;
    const layanan = qLayanan.value;
    const sort = qSort.value;

    let filtered = rows.filter(r => {
      const judul   = norm(r.dataset.judul);
      const pengaju = norm(r.dataset.pengaju);
      const opd     = norm(r.dataset.opd);

      const rowStatus  = (r.dataset.status || '');
      const rowLayanan = (r.dataset.layanan || '');

      const matchText    = !text || judul.includes(text) || pengaju.includes(text) || opd.includes(text);
      const matchStatus  = (status === 'Semua') || (rowStatus === status);
      const matchLayanan = (layanan === 'Semua') || rowLayanan.split('|').includes(layanan);

      return matchText && matchStatus && matchLayanan;
    });

    filtered.sort((a, b) => {
      const da = a.dataset.created ? new Date(a.dataset.created) : new Date(0);
      const db = b.dataset.created ? new Date(b.dataset.created) : new Date(0);
      return (sort === 'Terlama') ? (da - db) : (db - da);
    });

    rows.forEach(r => { r.style.display = 'none'; });

    filtered.forEach(r => {
      r.style.display = '';
      tbody.appendChild(r);
    });

    resultInfo.textContent = `Menampilkan ${filtered.length} konsultasi`;
  }

  function resetFilters() {
    qSearch.value  = '';
    qStatus.value  = 'Semua';
    qLayanan.value = 'Semua';
    qSort.value    = 'Terbaru';

    rows.forEach(r => {
      r.style.display = '';
      tbody.appendChild(r);
    });

    resultInfo.textContent = `Menampilkan ${rows.length} konsultasi`;
  }

  btnApply?.addEventListener('click', applyFilters);
  btnReset?.addEventListener('click', resetFilters);

  qSearch?.addEventListener('input', applyFilters);
  qStatus?.addEventListener('change', applyFilters);
  qLayanan?.addEventListener('change', applyFilters);
  qSort?.addEventListener('change', applyFilters);

  resetFilters();

  const pageSize = $('pageSize');

  function setQueryParam(url, key, value) {
    const u = new URL(url, window.location.origin);
    u.searchParams.set(key, value);
    u.searchParams.set('page', '1');
    return u.pathname + '?' + u.searchParams.toString();
  }

  pageSize?.addEventListener('change', () => {
    const v = pageSize.value || '10';
    window.location.href = setQueryParam(window.location.href, 'per_page', v);
  });
})();

(function () {
  function setParam(url, k, v) {
    const u = new URL(url, window.location.origin);
    if (v === null || v === undefined || v === '') u.searchParams.delete(k);
    else u.searchParams.set(k, v);
    return u.pathname + '?' + u.searchParams.toString();
  }

  function applyPeriod(nextPeriod) {
    const year  = document.getElementById('periodYear')?.value || '';
    const month = document.getElementById('periodMonth')?.value || '';

    let url = window.location.href;
    url = setParam(url, 'period', nextPeriod);
    url = setParam(url, 'year', year);

    if (nextPeriod === 'overall' || nextPeriod === 'yearly') {
      url = setParam(url, 'month', null);
    }

    if (nextPeriod === 'monthly') {
      url = setParam(url, 'month', month || '1');
    }

    url = setParam(url, 'page', '1');
    window.location.href = url;
  }

  document.querySelectorAll('[data-period]').forEach(btn => {
    btn.addEventListener('click', () => applyPeriod(btn.dataset.period));
  });

  document.getElementById('periodYear')?.addEventListener('change', () => {
    const active = document.querySelector('[data-period].bg-maroon')?.dataset.period || '{{ $period }}';
    applyPeriod(active);
  });

  document.getElementById('periodMonth')?.addEventListener('change', () => {
    const active = document.querySelector('[data-period].bg-maroon')?.dataset.period || '{{ $period }}';
    applyPeriod(active);
  });
})();

// Tooltip hover untuk .tip
document.querySelectorAll('[style*="position:relative"]').forEach(wrap => {
  const tip = wrap.querySelector('.tip');
  if (!tip) return;
  const btn = wrap.querySelector('a, button');
  if (!btn) return;
  btn.addEventListener('mouseenter', () => { tip.style.opacity='1'; tip.style.visibility='visible'; });
  btn.addEventListener('mouseleave', () => { tip.style.opacity='0'; tip.style.visibility='hidden'; });
});
</script>
@endpush