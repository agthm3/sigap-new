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

@php
  // Helper badge sederhana
  $badgeClass = function($text){
    $map = [
      'Selesai'  => 'bg-emerald-50 text-emerald-700',
      'Berjalan' => 'bg-amber-50 text-amber-700',
      'Belum'    => 'bg-gray-100 text-gray-700',
    ];
    return $map[$text] ?? $map['Belum'];
  };
@endphp

<!-- Header -->
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
          Urusan: {{ $inovasi->urusan_pemerintah ?? '—' }}
        </span>
        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
          Inisiator: {{ $inovasi->inisiator_daerah ?? '—' }}
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
      <a href="{{ route('evidence.form', $inovasi->id) }}"
         class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
         Isi/Update Evidence
      </a>
      <button onclick="window.print()" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Cetak</button>
    </div>
  </div>
</section>

<!-- KPI ringkas -->
<section class="max-w-7xl mx-auto px-4">
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Skor Evidence</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $evTotal }}</p>
      <p class="text-xs text-gray-500 mt-1">{{ $evFilled }}/20 indikator terisi</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">
        {{ $inovasi->updated_at? $inovasi->updated_at->timezone('Asia/Makassar')->format('d M Y • H:i') : '—' }}
      </p>
      <p class="text-xs text-gray-500 mt-1">Metadata</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Berkas Evidence</p>
      <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $evFiles }}</p>
      <p class="text-xs text-gray-500 mt-1">File terunggah</p>
    </div>
    <div class="card rounded-2xl border bg-white p-4">
      <p class="text-xs text-gray-500">Koordinat</p>
      <p class="mt-1 text-lg font-semibold text-gray-900">{{ $inovasi->koordinat ?? '—' }}</p>
      <p class="text-xs text-gray-500 mt-1">Lokasi (jika diisi)</p>
    </div>
  </div>
</section>
<!-- Ringkasan Evidence -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="card rounded-2xl border bg-white overflow-hidden">
    <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
      <span>Ringkasan Evidence (20 indikator)</span>
      <a href="{{ route('evidence.form',$inovasi->id) }}" class="text-maroon hover:underline">Ubah Evidence</a>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b">
            <th class="px-4 py-3 w-12">No</th>
            <th class="px-4 py-3">Indikator</th>
            <th class="px-4 py-3">Parameter</th>
            <th class="px-4 py-3">Bobot</th>
            <th class="px-4 py-3">File</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($evItems as $row)
            @php
              $done  = !empty($row['selected_label']) && (($row['selected_weight'] ?? 0) > 0);
              $tone  = $done ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700';
              $label = $done ? $row['selected_label'] : 'Belum dipilih';
              $bobot = (int)($row['selected_weight'] ?? 0);
              $fname = $row['file_name'] ?? '';
              $furl  = $row['file_url'] ?? '';
            @endphp
            <tr>
              <td class="px-4 py-3 text-gray-600">{{ $row['no'] }}</td>
              <td class="px-4 py-3">{{ $row['indikator'] }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-0.5 rounded {{ $tone }}">{{ $label }}</span>
              </td>
              <td class="px-4 py-3 font-semibold {{ $bobot>0?'text-maroon':'' }}">{{ $bobot }}</td>
              <td class="px-4 py-3">
                @if($fname && $furl)
                  <a href="{{ $furl }}" target="_blank" class="text-maroon underline">{{ $fname }}</a>
                @else
                  <span class="text-gray-500">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada data template evidence.</td></tr>
          @endforelse
        </tbody>
        <tfoot class="border-t bg-gray-50">
          <tr>
            <td class="px-4 py-3 font-semibold" colspan="3">Total Bobot</td>
            <td class="px-4 py-3 font-extrabold text-maroon">{{ $evTotal }}</td>
            <td></td>
          </tr>
        </tfoot>
      </table>
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


<!-- Lampiran terkumpul -->
<section class="max-w-7xl mx-auto px-4">
  <div class="card rounded-2xl border bg-white p-4">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-gray-800">Lampiran Terkumpul</h3>
      {{-- opsional tombol unduh semua --}}
    </div>

    @php
      $fileRows = $evItems->filter(fn($r)=> !empty($r['file_name']) && !empty($r['file_url']))->values();
    @endphp

    @if($fileRows->isEmpty())
      <p class="mt-3 text-sm text-gray-500">Belum ada lampiran.</p>
    @else
      <div class="mt-3 grid md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
        @foreach($fileRows as $f)
          <div class="rounded-xl border p-3 flex items-center gap-3">
            <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-gray-100 text-gray-700">FILE</span>
            <div class="min-w-0">
              <p class="font-medium text-gray-800 truncate">{{ $f['file_name'] }}</p>
              <p class="text-xs text-gray-500">Indikator #{{ $f['no'] }} • {{ $f['indikator'] }}</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
              <a href="{{ $f['file_url'] }}" target="_blank" class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50">View</a>
              <a href="{{ $f['file_url'] }}" download class="px-2.5 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Download</a>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection
