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
{{-- letakkan di bagian atas detail (mis. setelah header) --}}
@if(in_array($inovasi->asistensi_status, ['Dikembalikan','Revisi','Ditolak']))
  <section class="max-w-7xl mx-auto px-4">
    <div class="rounded-2xl border p-4
      @if($inovasi->asistensi_status==='Ditolak') bg-rose-50 border-rose-200 text-rose-800
      @elseif($inovasi->asistensi_status==='Revisi' || $inovasi->asistensi_status==='Dikembalikan') bg-amber-50 border-amber-200 text-amber-800
      @else bg-gray-50 border-gray-200 text-gray-800 @endif">
      <div class="flex items-start gap-3">
        <div class="font-semibold">
          Status Asistensi: {{ $inovasi->asistensi_status }}
          @if($inovasi->asistensi_at)
            <span class="text-xs text-gray-600 block">
              {{ $inovasi->asistensi_at->timezone('Asia/Makassar')->format('d M Y • H:i') }}
            </span>
          @endif
        </div>
      </div>
      @if(!empty($inovasi->asistensi_note))
        <div class="mt-2 text-sm leading-relaxed">{!! nl2br(e($inovasi->asistensi_note)) !!}</div>
      @endif
    </div>
  </section>
@endif

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
            <th class="px-4 py-3">Deskripsi</th>
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
              <td class="px-4 py-3 text-sm text-gray-700">
                @php
                  $desc = $row['deskripsi'] ?? null;
                  $isUrl = $desc && preg_match('/^https?:\/\/\S+/i', $desc);
                @endphp

                @if($desc)
                  @if($isUrl)
                    <a href="{{ $desc }}"
                      target="_blank"
                      rel="noopener noreferrer"
                      title="{{ $desc }}"
                      class="text-maroon underline block truncate">
                      {{ \Illuminate\Support\Str::limit($desc, 40) }}
                    </a>
                  @else
                    <span title="{{ $desc }}">
                      {{ \Illuminate\Support\Str::limit($desc, 40) }}
                    </span>
                  @endif
                @else
                  <span class="text-gray-400">—</span>
                @endif
              </td>


              <td class="px-4 py-3 space-y-1">
                  @forelse(($row['files'] ?? []) as $file)
                    <a href="{{ $file['url'] }}"
                      target="_blank"
                      title="{{ $file['name'] }}"
                      class="block text-maroon underline truncate">
                      {{ \Illuminate\Support\Str::limit($file['name'], 40) }}
                    </a>

                  @empty
                    <span class="text-gray-500">—</span>
                  @endforelse
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
{{-- Penelitian / Inovasi Terdahulu --}}
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="card rounded-2xl border bg-white p-4">
    <h3 class="font-semibold text-gray-800 mb-3">
      Penelitian / Inovasi Terdahulu
    </h3>

    @if($referensiVideos->isEmpty())
      <p class="text-sm text-gray-500">
        Belum ada referensi penelitian atau inovasi terdahulu.
      </p>
    @else
      <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($referensiVideos as $ref)
          <div class="rounded-xl border p-3 flex flex-col gap-2">

            {{-- Judul --}}
            <p class="font-semibold text-gray-900 leading-snug">
              {{ $ref->judul }}
            </p>

            {{-- Deskripsi --}}
            @if($ref->deskripsi)
              <p class="text-sm text-gray-600 line-clamp-3">
                {{ $ref->deskripsi }}
              </p>
            @endif

            {{-- Video / Link --}}
            @php
              $isYoutube = preg_match('/(youtube\.com|youtu\.be)/i', $ref->video_url);
            @endphp

            @if($isYoutube)
              <div class="aspect-video mt-2 rounded overflow-hidden border">
                <iframe
                  src="https://www.youtube.com/embed/{{ \Illuminate\Support\Str::afterLast($ref->video_url, '/') }}"
                  class="w-full h-full"
                  frameborder="0"
                  allowfullscreen>
                </iframe>
              </div>
            @else
              <a href="{{ $ref->video_url }}" target="_blank"
                 class="mt-2 inline-flex items-center gap-2 text-sm text-maroon hover:underline">
                🔗 Buka Referensi
              </a>
            @endif

          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>


<!-- Lampiran Utama + Evidence -->
<section class="max-w-7xl mx-auto px-4">
  <div class="card rounded-2xl border bg-white p-4">
    <div class="flex items-center justify-between">
      <h3 class="font-semibold text-gray-800">Lampiran</h3>
    </div>

    {{-- LAMPIRAN UTAMA --}}
    <div class="mt-3">
      <h4 class="text-sm font-semibold text-gray-700">Lampiran Utama</h4>
      @if(($mainFiles ?? collect())->isEmpty())
        <p class="mt-2 text-sm text-gray-500">Belum ada lampiran utama.</p>
      @else
        <div class="mt-2 grid md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
          @foreach($mainFiles as $mf)
            <div class="rounded-xl border p-3 flex items-center gap-3">
              <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-gray-100 text-gray-700">FILE</span>
              <div class="min-w-0">
                <p class="font-medium text-gray-800 truncate">{{ $mf['name'] ?? '—' }}</p>
                <p class="text-xs text-gray-500">{{ $mf['label'] }}</p>
              </div>
              <div class="ml-auto flex items-center gap-2">
                @if(!empty($mf['url']))
                  <a href="{{ $mf['url'] }}" target="_blank" class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50">View</a>
                  <a href="{{ $mf['url'] }}" download class="px-2.5 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Download</a>
                @else
                  <span class="text-xs text-gray-400">tidak ditemukan</span>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- LAMPIRAN EVIDENCE --}}
    @php
      $evidenceFiles = collect($evItems ?? [])
        ->flatMap(fn($r) =>
          collect($r['files'] ?? [])
            ->map(fn($f) => array_merge($f, [
              'no' => $r['no'],
              'indikator' => $r['indikator'],
            ]))
        );
    @endphp


    <div class="mt-6">
      <h4 class="text-sm font-semibold text-gray-700">Lampiran Evidence</h4>
@if($evidenceFiles->isEmpty())
  <p class="mt-2 text-sm text-gray-500">Belum ada lampiran evidence.</p>
@else
  <div class="mt-2 grid md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
    @foreach($evidenceFiles as $f)
      <div class="rounded-xl border p-3 flex items-center gap-3">
        <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-gray-100 text-gray-700">
          PDF
        </span>

        <div class="min-w-0">
          <p class="font-medium text-gray-800 truncate">
            {{ $f['name'] }}
          </p>
          <p class="text-xs text-gray-500">
            Indikator #{{ $f['no'] }} • {{ $f['indikator'] }}
          </p>
        </div>

        <div class="ml-auto flex items-center gap-2">
          <a href="{{ $f['url'] }}"
             target="_blank"
             class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50">
            View
          </a>
          <a href="{{ $f['url'] }}"
             download
             class="px-2.5 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">
            Download
          </a>
        </div>
      </div>
    @endforeach
  </div>
@endif

    </div>
  </div>
</section>

@endsection
