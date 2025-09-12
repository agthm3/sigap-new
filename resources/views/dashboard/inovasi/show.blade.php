@extends('layouts.app')

@section('content')
<style>
@media print{
  .no-print{ display:none !important; }
  header, footer{ display:none !important; }
  .card{ box-shadow:none !important; border-color:#e5e7eb !important; }
  body{ background:#fff; }
  body::before{
    content: "Dicetak via SIGAP INOVASI - BRIDA MKS";
    position: fixed; top: 50%; left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-weight: 800; font-size: 30px; letter-spacing: .06em;
    color: #7a2222; opacity: .08; z-index: 9999; pointer-events: none;
    text-transform: uppercase; white-space: nowrap;
  }
  .card, section, .rounded-2xl, .border, .bg-white { background: transparent !important; }
}
</style>

<!-- Page header -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
    <div class="flex-1 min-w-0">
      <div class="flex items-center gap-2">
        <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-maroon text-white">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
          </svg>
        </span>
        <p class="text-xs text-gray-600">Detail Inovasi</p>
      </div>
      <h1 class="mt-1 text-2xl font-extrabold text-gray-900">
        {{ $inovasi->judul ?? '—' }}
      </h1>

      <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
          Klasifikasi: {{ $inovasi->klasifikasi ?? '—' }}
        </span>
        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
          Jenis: {{ $inovasi->jenis_inovasi ?? '—' }}
        </span>
        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
          Urusan: {{ $inovasi->urusan ?? '—' }}
        </span>
        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
          Inisiator: {{ $inovasi->inisiator ?? '—' }}
        </span>
        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
          OPD: {{ $inovasi->opd_unit ?? '—' }}
        </span>
      </div>
    </div>

    <div class="no-print flex flex-wrap gap-2">
      <a href="{{ route('sigap-inovasi.edit', $inovasi->id) }}"
         class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm">
         Edit Metadata
      </a>
      {{-- tombol evidence disembunyikan sementara --}}
      <button onclick="window.print()" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Cetak</button>
    </div>
  </div>
</section>

<!-- Summary cards (tanpa evidence) -->
<section class="max-w-7xl mx-auto px-4">
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Progres Tahapan</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $progressPct }}%</p>
      <p class="text-xs text-gray-500 mt-1">Inisiatif • Uji Coba • Penerapan</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">
        {{ $inovasi->updated_at? $inovasi->updated_at->timezone('Asia/Makassar')->format('d M Y • H:i') : '—' }}
      </p>
      <p class="text-xs text-gray-500 mt-1">Metadata</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">OPD/Unit</p>
      <p class="mt-1 text-lg font-semibold text-gray-900">{{ $inovasi->opd_unit ?? '—' }}</p>
      <p class="text-xs text-gray-500 mt-1">Penanggung jawab</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Koordinat</p>
      <p class="mt-1 text-lg font-semibold text-gray-900">{{ $inovasi->koordinat ?? '—' }}</p>
      <p class="text-xs text-gray-500 mt-1">Lokasi (jika diisi)</p>
    </div>
  </div>
</section>

<!-- Tahapan -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="card rounded-2xl border bg-white p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="font-semibold text-gray-800">Tahapan Inovasi</h3>
      <div class="text-xs text-gray-500">Status ringkas</div>
    </div>

    @php
      $badge = function($text){
        $map = [
          'Selesai'  => 'bg-emerald-50 text-emerald-700',
          'Berjalan' => 'bg-amber-50 text-amber-700',
          'Belum'    => 'bg-gray-100 text-gray-700',
        ];
        $key = $text ?: 'Belum';
        return $map[$key] ?? $map['Belum'];
      };
    @endphp

    <div class="grid md:grid-cols-3 gap-3 text-sm">
      <div class="rounded-xl border p-3">
        <p class="text-gray-600">Inisiatif</p>
        <p class="mt-1 font-semibold">
          <span class="px-2 py-0.5 rounded {{ $badge($tInis) }}">{{ $tInis }}</span>
        </p>
      </div>
      <div class="rounded-xl border p-3">
        <p class="text-gray-600">Uji Coba</p>
        <p class="mt-1 font-semibold">
          <span class="px-2 py-0.5 rounded {{ $badge($tUji) }}">{{ $tUji }}</span>
        </p>
      </div>
      <div class="rounded-xl border p-3">
        <p class="text-gray-600">Penerapan</p>
        <p class="mt-1 font-semibold">
          <span class="px-2 py-0.5 rounded {{ $badge($tTerap) }}">{{ $tTerap }}</span>
        </p>
      </div>
    </div>

    <div class="mt-4">
      <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
        <div class="h-2 bg-maroon rounded-full" style="width: {{ $progressPct }}%"></div>
      </div>
      <p class="text-xs text-gray-500 mt-1">{{ $progressPct }}% selesai</p>
    </div>
  </div>
</section>

<!-- Deskripsi detail -->
<section class="max-w-7xl mx-auto px-4">
  <div class="grid lg:grid-cols-2 gap-4">
    <div class="card rounded-2xl border bg-white p-4">
      <h3 class="font-semibold text-gray-800">Rancang Bangun</h3>
      <div class="prose prose-sm max-w-none mt-2 text-gray-800">
        {!! $inovasi->rancang_bangun ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
      </div>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <h3 class="font-semibold text-gray-800">Tujuan Inovasi</h3>
      <div class="prose prose-sm max-w-none mt-2 text-gray-800">
        {!! $inovasi->tujuan ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
      </div>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <h3 class="font-semibold text-gray-800">Manfaat yang Diperoleh</h3>
      <div class="prose prose-sm max-w-none mt-2 text-gray-800">
        {!! $inovasi->manfaat ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
      </div>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <h3 class="font-semibold text-gray-800">Hasil Inovasi</h3>
      <div class="prose prose-sm max-w-none mt-2 text-gray-800">
        {!! $inovasi->hasil_inovasi ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
      </div>
    </div>
  </div>
</section>
@endsection
