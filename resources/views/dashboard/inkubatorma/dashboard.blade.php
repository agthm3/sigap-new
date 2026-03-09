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
  </style>
@endpush

@section('content')
@php

  $me = auth()->user();
  $isAdmin = $me && $me->hasRole('admin');
  $isVerifikator = $me && $me->hasAnyRole(['verifikator_inkubatorma']); 
  $isUser = $me && $me->hasRole('user');

@endphp

@if(session('success'))
  <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
    {{ session('success') }}
  </div>
@endif

<!-- PAGE HEADER -->
<section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Dashboard <span class="text-maroon">SIGAP Inkubatorma</span>
    </h1>
    <p class="text-sm text-gray-500">Inspirasi Kreatif untuk Berbagi Treatment Inovasi Riset Makassar</p>
  </div>
  <a href="{{ route('sigap-inkubatorma.index') }}"
     class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:bg-maroon/90">
    + Isi Form Pengajuan
  </a>
</section>

<!-- FILTER CARD -->
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
        <option value="Menunggu">Menunggu</option>
        <option value="Akan Dijadwalkan">Akan Dijadwalkan</option>
        <option value="Terjadwal">Terjadwal</option>
        <option value="Dijadwalkan Ulang">Dijadwalkan Ulang</option>
        <option value="Ditolak">Ditolak</option>
        <option value="Selesai">Tutup/Selesai</option>
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

<!-- TABLE CARD -->
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

  <!-- WRAPPER SCROLL HORIZONTAL -->
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
            // $layananKey = (string) ($row->layanan_id ?? '');
            // $layananLabel = $layananOptions[$layananKey] ?? '—';

            // Perbaiki lainnya di dashboard
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


            $status  = $row->status ?? 'Menunggu';
            $canEdit = in_array($status, ['Menunggu', 'Akan Dijadwalkan'], true);

            // Verifikasi disable kalau sudah Selesai
            $canVerify = ($status !== 'Selesai');
          @endphp

          <tr data-row
              data-judul="{{ $row->judul_konsultasi ?? '' }}"
              data-pengaju="{{ $row->nama_pengaju ?? '' }}"
              data-opd="{{ $row->opd_unit ?? '' }}"
              data-layanan="{{ $layananLabel }}"
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

            {{-- <td class="px-5 py-4 whitespace-normal">
              <span class="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                {{ $layananLabel }}
              </span>
            </td> --}}

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

            <td class="px-5 py-4 text-right">
                <div class="inline-flex items-center gap-2">

                  {{-- DETAIL (Admin / Verifikator Inkubatorma / User) --}}
                  <a href="{{ route('sigap-inkubatorma.detail', ['id' => $row->id]) }}"
                     class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-semibold hover:bg-gray-50">
                    Detail
                  </a>

                  {{-- VERIFIKASI (Admin + Verifikator Inkubatorma) --}}
                  @if($isAdmin || $isVerifikator)
                    @if($canVerify)
                      <a href="{{ route('sigap-inkubatorma.verifikasi', ['id' => $row->id]) }}"
                         class="px-3 py-1.5 rounded-lg border border-maroon text-maroon text-xs font-semibold hover:bg-maroon/5">
                        Verifikasi
                      </a>
                    @else
                      <button type="button" disabled
                        class="px-3 py-1.5 rounded-lg bg-gray-200 text-gray-500 text-xs font-semibold cursor-not-allowed opacity-60">
                        Verifikasi
                      </button>
                    @endif
                  @endif

                  {{-- EDIT (Admin + User) --}}
                  @if($isAdmin || $isUser)
                    @if($canEdit)
                      <a href="{{ route('sigap-inkubatorma.edit', ['id' => $row->id]) }}"
                        class="px-3 py-1.5 rounded-lg bg-maroon text-white text-xs font-semibold hover:opacity-90">
                        Edit
                      </a>
                    @else
                      <button type="button" disabled
                        class="px-3 py-1.5 rounded-lg bg-gray-200 text-gray-500 text-xs font-semibold cursor-not-allowed">
                        Edit
                      </button>
                    @endif
                  @endif

                  {{-- HAPUS (Admin + User) --}}
                  @if($isAdmin || $isUser)
                    <form action="{{ route('sigap-inkubatorma.destroy', $row->id) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                      @csrf
                      @method('DELETE')

                      <button type="submit"
                              class="px-3 py-1.5 rounded-lg border border-red-600 text-red-600 text-xs hover:bg-red-600 hover:text-white transition">
                        Hapus
                      </button>
                    </form>
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

  <!-- PAGINATION  -->
  <div class="px-5 py-4 border-t flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 text-sm text-gray-500">
    <span id="pageInfo">
      Halaman {{ $inkubatormas->currentPage() }} dari {{ $inkubatormas->lastPage() }}
    </span>
  
    <div class="flex items-center gap-2 justify-end">
      {{-- Prev --}}
      @if($inkubatormas->onFirstPage())
        <button type="button" disabled
          class="px-3 py-1 rounded border border-gray-300 bg-gray-100 text-gray-400 cursor-not-allowed">‹</button>
      @else
        <a href="{{ $inkubatormas->previousPageUrl() }}"
          class="px-3 py-1 rounded border border-gray-300 hover:bg-gray-50">‹</a>
      @endif
  
      {{-- Next --}}
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
<script>
(function () {
  const $ = (id) => document.getElementById(id);

  const qSearch  = $('qSearch');
  const qStatus  = $('qStatus');
  const qLayanan = $('qLayanan');
  const qSort    = $('qSort');
  const btnApply = $('btnApply');
  const btnReset = $('btnReset');

  const tbody      = $('tbody');
  const resultInfo = $('resultInfo');

  function norm(s){ return (s || '').toString().toLowerCase().trim(); }

  // Ambil semua baris dari server-rendered table
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
      const matchStatus  = (status === 'Semua')  || (rowStatus === status);
      const matchLayanan = (layanan === 'Semua') || (rowLayanan === layanan);

      return matchText && matchStatus && matchLayanan;
    });

    filtered.sort((a,b) => {
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

  btnApply.addEventListener('click', applyFilters);
  btnReset.addEventListener('click', resetFilters);

  qSearch.addEventListener('input', applyFilters);
  qStatus.addEventListener('change', applyFilters);
  qLayanan.addEventListener('change', applyFilters);
  qSort.addEventListener('change', applyFilters);

  resetFilters();

// =========================
  // Server-side Pagination: per_page switch
  // =========================
  const pageSize = $('pageSize');

  function setQueryParam(url, key, value) {
    const u = new URL(url, window.location.origin);
    u.searchParams.set(key, value);
    u.searchParams.set('page', '1'); // reset ke page 1 kalau ganti per_page
    return u.pathname + '?' + u.searchParams.toString();
  }

  pageSize?.addEventListener('change', () => {
    const v = pageSize.value || '10';
    window.location.href = setQueryParam(window.location.href, 'per_page', v);
  });
  
});
</script>
@endpush