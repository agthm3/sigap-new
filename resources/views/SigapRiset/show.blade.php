@extends('layouts.page')

@section('content')
@php
  use Illuminate\Support\Str;

  // Normalisasi akses & penyiapan variabel aman
  $accessRaw   = $research->access ?? 'Public';
  $isPublic    = strtoupper(trim($accessRaw)) === 'PUBLIC';

  // Pastikan authors berbentuk array of ['name'=>..]
  $authorsArr  = is_array($research->authors) ? $research->authors : (json_decode($research->authors, true) ?: []);
  $authorsText = collect($authorsArr)->pluck('name')->filter()->implode(', ');

  // Keywords, tags, stakeholders
  $keywords    = is_array($research->keywords) ? $research->keywords : (json_decode($research->keywords, true) ?: []);
  $tags        = is_array($research->tags) ? $research->tags : (json_decode($research->tags, true) ?: []);
  $stakeholders= is_array($research->stakeholders) ? $research->stakeholders : (json_decode($research->stakeholders, true) ?: []);

  // Sitasi ringkas (APA-ish)
  $citeApa     = trim(($authorsText ?: 'BRIDA') .' ('. ($research->year ?? 'n.d.') .'). '. ($research->title ?? '-') .'. BRIDA Kota Makassar'. (!empty($research->doi) ? '. https://doi.org/'.$research->doi : ''));

  // Versi, datasets, funding, ethics
  $datasets    = is_array($research->datasets ?? null) ? $research->datasets : [];
  $versions    = is_array($research->versions ?? null) ? $research->versions : [];
  $funding     = (array)($research->funding ?? []);
  $ethics      = $research->ethics ?? '—';

  // PDF control dari controller:
  // - $pdfUrl hanya terisi untuk Public
  // - $canPreview true hanya jika Public & file ada
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
      {{ $authorsText ?: '—' }} • {{ $research->year ?: '—' }}
    </p>
  </div>
</section>

{{-- PESAN RESTRICTED --}}
@if(!$isPublic)
  <div class="max-w-7xl mx-auto px-4 mt-4">
    <div class="rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-800 p-4 text-sm">
      🔒 Dokumen atau riset ini memiliki akses terbatas.
      Silakan hubungi admin untuk mendapatkan akses penuh:
      <a href="mailto:balitbangdamks@gmail.com" class="underline font-semibold">
        balitbangdamks@gmail.com
      </a>.
    </div>
  </div>
@endif

<section class="py-6 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid lg:grid-cols-3 gap-6">

      {{-- KONTEN UTAMA --}}
      @php
        $isPolicyBrief = isset($research->category) && $research->category === 'policy_brief';
        $hasYoutube    = $isPolicyBrief && !empty($research->youtube_url);

        // extract video id dari youtube_url secara sederhana
        // kita coba ambil parameter v=... atau path /embed/... atau youtu.be/...
        $youtubeId = null;
        if (!empty($research->youtube_url)) {
            $urlRaw = $research->youtube_url;
            // pola umum ?v=XXXXXXXX
            if (preg_match('/[?&]v=([^&]+)/', $urlRaw, $m)) {
                $youtubeId = $m[1];
            }
            // pola youtu.be/XXXXXXXX
            elseif (preg_match('#youtu\.be/([^?&/]+)#', $urlRaw, $m)) {
                $youtubeId = $m[1];
            }
            // pola /embed/XXXXXXXX
            elseif (preg_match('#/embed/([^?&/]+)#', $urlRaw, $m)) {
                $youtubeId = $m[1];
            }
        }

        // siapkan embed src kalau ada id
        $youtubeEmbedSrc = $youtubeId
          ? 'https://www.youtube.com/embed/'.$youtubeId.'?autoplay=1&mute=1&rel=0&modestbranding=1'
          : null;
      @endphp

      <div class="lg:col-span-2 space-y-6">

        {{-- BLOK VIDEO POLICY BRIEF (PALING ATAS) --}}
        @if($hasYoutube && $youtubeEmbedSrc)
          <section id="video" class="rounded-xl border border-yellow-300 bg-black shadow ring-1 ring-yellow-100 overflow-hidden">
            <div class="aspect-video w-full bg-black">
              <iframe
                src="{{ $youtubeEmbedSrc }}"
                allow="autoplay; encrypted-media; picture-in-picture"
                allowfullscreen
                class="w-full h-full border-0">
              </iframe>
            </div>

            <div class="p-4 sm:p-5 bg-white">
              <div class="flex items-start flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-yellow-50 text-yellow-700 border border-yellow-300">
                  Policy Brief
                </span>

                <div class="text-xs text-gray-500 flex items-center gap-3 ms-auto">
                  <span class="inline-flex items-center gap-1">
                    <span>👁️</span>
                    <span>{{ number_format($research->stats['views'] ?? 0) }}</span>
                  </span>
                  <span class="inline-flex items-center gap-1">
                    <span>⬇️</span>
                    <span>{{ number_format($research->stats['downloads'] ?? 0) }}</span>
                  </span>
                </div>
              </div>

              <h2 class="mt-3 text-lg font-extrabold text-gray-900 leading-snug">
                {{ $research->title }}
              </h2>
              <p class="mt-1 text-sm text-gray-700">
                {{ $authorsText ?: '—' }} • {{ $research->year ?: '—' }}
              </p>
            </div>
          </section>
        @endif

        {{-- KONTEN UTAMA: ABSTRAK, AKSI, PREVIEW PDF --}}
        <div class="rounded-xl border border-gray-200 p-4 sm:p-6 bg-white">
          {{-- Badge & meta atas --}}
          <div class="flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs border
              {{ $isPublic ? 'bg-green-50 text-green-700 border-green-200' : 'bg-yellow-50 text-yellow-700 border-yellow-200' }}">
              {{ $accessRaw }}
            </span>

            @if($isPolicyBrief)
              <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold bg-yellow-50 text-yellow-700 border border-yellow-300">
                Policy Brief
              </span>
            @endif

            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs border bg-gray-50 text-gray-700">
              Lisensi: {{ $research->license ?? '—' }}
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
            <p class="mt-2 text-sm text-gray-700">{{ $research->abstract ?? '—' }}</p>
          </div>

          {{-- Kata kunci & Metode --}}
          <div class="mt-4 grid sm:grid-cols-2 gap-4">
            <div>
              <h3 class="text-sm font-semibold text-maroon">Kata Kunci</h3>
              <div class="mt-2 flex flex-wrap gap-2">
                @forelse($keywords as $kw)
                  <span class="inline-flex px-2 py-1 rounded bg-gray-100 border text-xs">{{ $kw }}</span>
                @empty
                  <span class="text-xs text-gray-500">—</span>
                @endforelse
              </div>
            </div>
            <div>
              <h3 class="text-sm font-semibold text-maroon">Metode</h3>
              <p class="mt-2 text-sm text-gray-700">{{ $research->method ?? '—' }}</p>
            </div>
          </div>

          {{-- Tombol aksi --}}
          <div class="mt-5 flex flex-wrap gap-2">
            {{-- Kalau policy brief & ada video, tawarkan tombol lompat ke video (atas) --}}
            @if($hasYoutube && $youtubeEmbedSrc)
              <a href="#video"
                class="px-4 py-2 rounded-lg bg-yellow-400 text-gray-900 text-sm font-semibold hover:bg-yellow-300 transition">
                Tonton Video
              </a>
            @endif

            {{-- Unduh/Buka PDF hanya jika Public --}}
            @if(isset($isPublic, $pdfUrl) && $isPublic && !empty($pdfUrl))
              <a href="{{ $pdfUrl }}" class="px-4 py-2 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800" download>
                Unduh PDF {{ isset($research->file['size']) ? '(' . $research->file['size'] . ')' : '' }}
              </a>
              <a href="{{ $pdfUrl }}" target="_blank"
                class="px-4 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
                Buka PDF Penuh
              </a>
            @endif

            {{-- Salin sitasi selalu --}}
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
            @if(isset($canPreview) && $canPreview && !empty($pdfUrl))
              <iframe src="{{ $pdfUrl }}" class="w-full h-[70vh] rounded-xl border"></iframe>
            @else
              <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 text-gray-700 p-4 text-sm">
                File tidak dapat dipreview di sini.
                @if(!$isPublic)
                  Silakan hubungi admin untuk akses penuh.
                @endif
              </div>
            @endif
          </div>
        </div>

        {{-- Lampiran / Dataset --}}
        @if(!empty($datasets))
          <div class="rounded-xl border border-gray-200 p-5 bg-white">
            <h3 class="text-sm font-semibold text-maroon">Lampiran & Dataset</h3>
            <ul class="mt-3 space-y-2 text-sm">
              @foreach($datasets as $d)
                <li>
                  <a href="{{ $d['url'] ?? '#' }}" class="text-maroon hover:underline">
                    {{ $d['label'] ?? ($d['url'] ?? 'Dataset') }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Riwayat Versi --}}
        @if(!empty($versions))
          <div class="rounded-xl border border-gray-200 p-5 bg-white">
            <h3 class="text-sm font-semibold text-maroon">Riwayat Versi</h3>
            <ul class="mt-3 space-y-3">
              @foreach($versions as $v)
                <li class="text-sm">
                  <div class="flex items-center justify-between">
                    <span class="font-semibold">v{{ $v['v'] ?? '?' }}</span>
                    <span class="text-xs text-gray-500">
                      {{ !empty($v['date']) ? \Carbon\Carbon::parse($v['date'])->format('d M Y') : '—' }}
                    </span>
                  </div>
                  <p class="text-gray-700">{{ $v['note'] ?? '—' }}</p>
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
              <dd class="font-medium text-gray-800 text-right">
                {{ count($funding) ? implode(', ', $funding) : '—' }}
              </dd>
            </div>
            <div class="flex justify-between py-1">
              <dt class="text-gray-500">Pernyataan Etika</dt>
              <dd class="font-medium text-gray-800 text-right">{{ $ethics }}</dd>
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
            @forelse($authorsArr as $a)
              @php
                $nm = $a['name'] ?? '-';
                $initials = collect(explode(' ', $nm))->map(fn($w)=>Str::substr($w,0,1))->implode('');
              @endphp
              <li class="flex items-start gap-3">
                <div class="h-10 w-10 rounded-full bg-maroon/10 text-maroon flex items-center justify-center font-bold">
                  {{ $initials }}
                </div>
                <div class="flex-1">
                  <p class="font-semibold text-gray-800">{{ $nm }}</p>
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
            @empty
              <li class="text-xs text-gray-500">—</li>
            @endforelse
          </ul>

          {{-- Korespondensi --}}
          @if(!empty($research->corresponding))
          <div class="mt-4 border-t pt-3">
            <h4 class="text-xs font-semibold text-gray-700">Kontak Korespondensi</h4>
            <p class="text-xs text-gray-700">
              {{ $research->corresponding['name'] ?? '-' }} —
              <a href="mailto:{{ $research->corresponding['email'] ?? '' }}" class="underline">
                {{ $research->corresponding['email'] ?? '-' }}
              </a>
            </p>
          </div>
          @endif
        </div>

        {{-- Metadata Ringkas --}}
        <div class="rounded-xl border border-gray-200 p-5 bg-white">
          <h3 class="text-sm font-semibold text-maroon">Metadata Ringkas</h3>
          <dl class="mt-3 text-sm">
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Tahun</dt><dd class="font-medium text-gray-800">{{ $research->year ?? '—' }}</dd>
            </div>
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Pihak Terkait</dt>
              <dd class="font-medium text-gray-800 text-right">
                {{ count($stakeholders) ? implode(', ', $stakeholders) : '—' }}
              </dd>
            </div>
            <div class="flex justify-between py-1 border-b">
              <dt class="text-gray-500">Tag</dt>
              <dd class="font-medium text-gray-800 text-right">
                {{ count($tags) ? implode(', ', $tags) : '—' }}
              </dd>
            </div>
            <div class="flex justify-between py-1">
              <dt class="text-gray-500">Ukuran Dokumen</dt>
              <dd class="font-medium text-gray-800">{{ $research->file['size'] ?? '-' }}</dd>
            </div>
          </dl>
        </div>

        {{-- Riset Terkait --}}
        @if(!empty($related))
        <div class="rounded-xl border border-gray-200 p-5 bg-white">
          <h3 class="text-sm font-semibold text-maroon">Riset Terkait</h3>
          <ul class="mt-3 space-y-3 text-sm">
            @foreach($related as $rel)
              @php
                $relTags = is_array($rel->tags) ? $rel->tags : (json_decode($rel->tags, true) ?: []);
              @endphp
              <li>
                <a href="{{ route('sigap-riset.show', $rel->id) }}" class="font-medium text-maroon hover:underline">
                  {{ Str::limit($rel->title, 80) }}
                </a>
                <div class="text-xs text-gray-500">
                  {{ $rel->year ?? '—' }} • {{ implode(', ', array_slice($relTags, 0, 2)) }}
                </div>
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
