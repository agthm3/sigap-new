@extends('layouts.page')

@section('content')
@php
  use Illuminate\Support\Str;

  $isPublic = ($research->access ?? 'Public') === 'Public';
  $authorsText = collect($research->authors ?? [])->pluck('name')->implode(', ');
  $citeApa = trim(($authorsText ?: 'BRIDA') .' ('. ($research->year ?? 'n.d.') .'). '. ($research->title ?? '-') .'. BRIDA Kota Makassar'. (isset($research->doi) ? '. https://doi.org/'.$research->doi : ''));
  $bibtexKey = Str::slug((($research->authors[0]['name'] ?? 'brida').($research->year ?? 'n.d.')), '');
@endphp

{{-- HERO --}}
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-7xl mx-auto px-4 py-8 sm:py-10">
    <nav class="text-white/80 text-xs sm:text-sm mb-3">
      <a href="{{ route('sigap-riset.index') }}" class="hover:underline">SIGAP Riset</a>
      <span class="px-2">/</span>
      <span class="opacity-90">Detail</span>
    </nav>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-white leading-tight">
      {{ $research->title }}
    </h1>
    <p class="mt-2 text-white/90 text-sm">
      {{ $authorsText }} • {{ $research->year }}
    </p>
  </div>
</section>

<section class="py-6 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid lg:grid-cols-3 gap-6">
      {{-- KONTEN UTAMA --}}
      <div class="lg:col-span-2 space-y-6">
        {{-- Meta ringkas + tombol --}}
        <div class="rounded-xl border border-gray-200 p-4 sm:p-6">
          <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs border
              {{ $isPublic ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
              {{ $research->access }}
            </span>
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs border bg-gray-50 text-gray-700">
              Lisensi: {{ $research->license }}
            </span>
            @if(!empty($research->doi))
              <a href="https://doi.org/{{ $research->doi }}" target="_blank"
                 class="inline-flex items-center rounded-full px-2.5 py-1 text-xs border bg-blue-50 text-blue-700 border-blue-200">
                DOI
              </a>
            @endif
            @if(!empty($research->ojs_url))
              <a href="{{ $research->ojs_url }}" target="_blank"
                 class="inline-flex items-center rounded-full px-2.5 py-1 text-xs border bg-purple-50 text-purple-700 border-purple-200">
                OJS
              </a>
            @endif
            <span class="ms-auto inline-flex gap-3 text-xs text-gray-600">
              <span>👁️ {{ number_format($research->stats['views'] ?? 0) }}</span>
              <span>⬇️ {{ number_format($research->stats['downloads'] ?? 0) }}</span>
            </span>
          </div>

          {{-- Abstrak --}}
          <div class="mt-4">
            <h2 class="text-lg font-extrabold text-gray-900">Abstrak</h2>
            <p class="mt-2 text-sm text-gray-700">{{ $research->abstract }}</p>
          </div>

          {{-- Kata kunci & Metode --}}
          <div class="mt-4 grid sm:grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-semibold text-maroon">Kata Kunci</h3>
              <div class="mt-2 flex flex-wrap gap-2">
                @foreach(($research->keywords ?? []) as $kw)
                  <span class="inline-flex px-2 py-1 rounded bg-gray-100 border text-xs">{{ $kw }}</span>
                @endforeach
              </div>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-maroon">Metode</h3>
              <p class="mt-2 text-sm text-gray-700">{{ $research->method ?? '—' }}</p>
            </div>
          </div>

          {{-- Tombol aksi --}}
          <div class="mt-5 flex flex-wrap gap-2">
            @if(!empty($pdfUrl))
              <a href="{{ $pdfUrl }}" class="px-4 py-2 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800" download>
                Unduh PDF ({{ $research->file['size'] ?? '' }})
              </a>
              <a href="{{ $pdfUrl }}" target="_blank" class="px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
                Buka PDF Penuh
              </a>
            @endif

            <button type="button"
              onclick="navigator.clipboard.writeText(document.getElementById('cite-apa').textContent); this.innerText='Sitasi Disalin!'; setTimeout(()=>this.innerText='Salin Sitasi (APA)',1400);"
              class="px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
              Salin Sitasi (APA)
            </button>
          </div>

          {{-- Hidden text for copy --}}
          <pre id="cite-apa" class="sr-only">{{ $citeApa }}</pre>

          {{-- Preview PDF --}}
          <div class="mt-6">
            @if(!empty($pdfUrl))
              <iframe src="{{ $pdfUrl }}#view=FitH&toolbar=1"
                      class="w-full h-[520px] rounded-lg border"
                      title="Preview PDF"></iframe>
            @else
              <p class="text-sm text-gray-500">Tidak ada file PDF tersedia.</p>
            @endif
          </div>
        </div>

        {{-- Lampiran / Dataset --}}
        @if(!empty($research->datasets))
          <div class="rounded-xl border border-gray-200 p-5 bg-white">
            <h3 class="text-sm font-semibold text-maroon">Lampiran & Dataset</h3>
            <ul class="mt-3 space-y-2 text-sm">
              @foreach($research->datasets as $d)
                <li>
                  <a href="{{ $d['url'] ?? '#' }}" class="text-maroon hover:underline">{{ $d['label'] }}</a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Riwayat Versi --}}
        @if(!empty($research->versions))
          <div class="rounded-xl border border-gray-200 p-5 bg-white">
            <h3 class="text-sm font-semibold text-maroon">Riwayat Versi</h3>
            <ul class="mt-3 space-y-3">
              @foreach($research->versions as $v)
                <li class="text-sm">
                  <div class="flex items-center justify-between">
                    <span class="font-semibold">v{{ $v['v'] }}</span>
                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($v['date'])->format('d M Y') }}</span>
                  </div>
                  <p class="text-gray-700">{{ $v['note'] }}</p>
                </li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Pendanaan & Etika --}}
        <div class="rounded-xl border border-gray-200 p-5 bg-white">
          <h3 class="text-sm font-semibold text-maroon">Pendanaan & Etika</h3>
          <dl class="mt-3 text-sm">
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Sumber Pendanaan</dt>
              <dd class="font-medium text-gray-800 text-right">{{ implode(', ', (array)$research->funding) }}</dd>
            </div>
            <div class="flex justify-between py-1">
              <dt class="text-gray-500">Pernyataan Etika</dt>
              <dd class="font-medium text-gray-800 text-right">{{ $research->ethics }}</dd>
            </div>
          </dl>
        </div>
      </div>

      {{-- SIDEBAR --}}
      <aside class="space-y-6">
        {{-- Profil Peneliti --}}
        <div class="rounded-xl border border-gray-200 p-5 bg-white">
          <h3 class="text-sm font-semibold text-maroon">Profil Peneliti</h3>
          <ul class="mt-3 space-y-4">
            @foreach($research->authors as $a)
              @php
                $initials = collect(explode(' ', $a['name']))->map(fn($w)=>Str::substr($w,0,1))->implode('');
              @endphp
              <li class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-full bg-maroon/10 text-maroon flex items-center justify-center font-bold">
                  {{ $initials }}
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-800">{{ $a['name'] }}</p>
                  <p class="text-xs text-gray-600">{{ $a['affiliation'] ?? '-' }}</p>
                  <div class="mt-1 flex flex-wrap gap-2 text-xs">
                    @if(!empty($a['orcid']))
                      <a href="https://orcid.org/{{ $a['orcid'] }}" target="_blank" class="underline text-gray-700">ORCID</a>
                    @endif
                    @if(!empty($a['scholar']))
                      <a href="{{ $a['scholar'] }}" target="_blank" class="underline text-gray-700">Google Scholar</a>
                    @endif
                  </div>
                </div>
              </li>
            @endforeach
          </ul>

          {{-- Korespondensi --}}
          @if(!empty($research->corresponding))
          <div class="mt-4 border-t pt-3">
            <h4 class="text-xs font-semibold text-gray-700">Kontak Korespondensi</h4>
            <p class="text-xs text-gray-700">
              {{ $research->corresponding['name'] ?? '-' }} —
              <a href="mailto:{{ $research->corresponding['email'] ?? '' }}" class="underline">{{ $research->corresponding['email'] ?? '-' }}</a>
            </p>
          </div>
          @endif
        </div>

        {{-- Metadata ringkas --}}
        <div class="rounded-xl border border-gray-200 p-5 bg-white">
          <h3 class="text-sm font-semibold text-maroon">Metadata Ringkas</h3>
          <dl class="mt-3 text-sm">
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Tahun</dt><dd class="font-medium text-gray-800">{{ $research->year }}</dd>
            </div>
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Pihak Terkait</dt>
              <dd class="font-medium text-gray-800 text-right">{{ implode(', ', (array)$research->stakeholders) }}</dd>
            </div>
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Tag</dt>
              <dd class="font-medium text-gray-800 text-right">{{ implode(', ', (array)$research->tags) }}</dd>
            </div>
            <div class="flex justify-between py-1">
              <dt class="text-gray-500">Ukuran Dokumen</dt><dd class="font-medium text-gray-800">{{ $research->file['size'] ?? '-' }}</dd>
            </div>
          </dl>
        </div>

        {{-- Riset Terkait --}}
        @if(!empty($related))
        <div class="rounded-xl border border-gray-200 p-5 bg-white">
          <h3 class="text-sm font-semibold text-maroon">Riset Terkait</h3>
          <ul class="mt-3 space-y-3 text-sm">
            @foreach($related as $rel)
              <li>
                <a href="{{ route('sigap-riset.show', $rel->id) }}" class="font-medium text-maroon hover:underline">
                  {{ Str::limit($rel->title, 80) }}
                </a>
                <div class="text-xs text-gray-500">{{ $rel->year }} • {{ implode(', ', array_slice((array)$rel->tags,0,2)) }}</div>
              </li>
            @endforeach
          </ul>
        </div>
        @endif
      </aside>
    </div>
  </div>
</section>
@endsection
