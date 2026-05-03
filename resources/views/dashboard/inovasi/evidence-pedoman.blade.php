@extends('layouts.app')

@section('content')
<style>
  details[open] .chev { transform: rotate(180deg); }
  .yt-embed { aspect-ratio: 16/9; }
</style>

{{-- ─── HEADER ─── --}}
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">📘 Pedoman Pengisian Inovasi</h1>
      <p class="text-sm text-gray-600 mt-1">
        Panduan lengkap pengisian metadata inovasi dan 20 indikator evidence beserta video tutorial.
      </p>
    </div>
    <div class="flex gap-2 flex-wrap">
      <a href="#section-metadata"
         class="px-4 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm">
        📋 Metadata
      </a>
      <a href="#section-evidence"
         class="px-4 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm">
        📊 Evidence
      </a>
    </div>
  </div>
</section>

{{-- ════════════════════════════════════════════
     SECTION 0: SAMBUTAN & ARAHAN
════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 pb-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <div class="grid md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">

      {{-- KIRI: Sambutan --}}
      <div class="p-6 flex flex-col justify-center">

        {{-- Badge --}}
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                     bg-maroon/10 text-maroon text-xs font-semibold w-fit mb-4">
          🎙️ Sambutan & Arahan
        </span>

        @php
          $sambutan = \App\Models\InovasiPedomanMeta::where('field_key','sambutan')->first();
        @endphp

        {{-- Teks sambutan --}}
        <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed">
          {!! $sambutan?->deskripsi
              ? nl2br(e($sambutan->deskripsi))
              : '<p class="text-gray-400 italic">Sambutan belum diisi.</p>' !!}
        </div>

        {{-- Nama & jabatan --}}
        @if($sambutan?->video_url)
          {{-- Gunakan field tambahan untuk nama/jabatan, atau hardcode dulu --}}
        @endif

        @role('admin')
        <form method="POST" action="{{ route('evidence.pedoman.meta.save') }}"
              class="mt-5 pt-4 border-t border-gray-100 space-y-2">
          @csrf
          <p class="text-[11px] text-gray-400 uppercase tracking-wide font-medium">Edit Sambutan</p>
          <input type="hidden" name="meta[sambutan][key]" value="sambutan">
          <textarea name="meta[sambutan][deskripsi]" rows="5"
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                           focus:border-maroon focus:ring-maroon resize-none"
                    placeholder="Tulis sambutan/arahan Kepala Badan di sini…">{{ $sambutan?->deskripsi }}</textarea>
          <input type="url" name="meta[sambutan][video_url]"
                 value="{{ $sambutan?->video_url ?? '' }}"
                 class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                        focus:border-maroon focus:ring-maroon"
                 placeholder="https://youtube.com/watch?v=... (video sambutan)">
          <div class="flex justify-end">
            <button type="submit"
                    class="px-4 py-1.5 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">
              💾 Simpan
            </button>
          </div>
        </form>
        @endrole

      </div>

      {{-- KANAN: Video Sambutan --}}
      <div class="p-6 flex flex-col items-center justify-center bg-gray-50/40">
        @php
          $sambutanVideo = $sambutan?->video_url ?? null;
          $sambutanYtId  = null;
          if ($sambutanVideo && preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $sambutanVideo, $m)) {
            $sambutanYtId = $m[1];
          }
        @endphp

        @if($sambutanYtId)
          <div class="w-full rounded-xl overflow-hidden border border-gray-200 shadow-sm"
               style="aspect-ratio:16/9">
            <iframe
              src="https://www.youtube.com/embed/{{ $sambutanYtId }}"
              class="w-full h-full"
              frameborder="0"
              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
              allowfullscreen>
            </iframe>
          </div>
          <p class="text-[11px] text-gray-400 mt-3 text-center">
            Sambutan & Arahan Kepala Badan
          </p>
        @else
          <div class="flex flex-col items-center justify-center gap-3 py-12 w-full
                      rounded-xl border-2 border-dashed border-gray-200">
            <svg class="w-14 h-14 text-gray-200" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="1.5"
                    d="M15 10l4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14
                       M3 8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8z"/>
            </svg>
            <div class="text-center">
              <p class="text-sm font-medium text-gray-400">Video sambutan belum tersedia</p>
              @role('admin')
                <p class="text-xs text-gray-400 mt-1">
                  Tambahkan link YouTube di panel edit samping kiri
                </p>
              @endrole
            </div>
          </div>
        @endif
      </div>

    </div>
  </div>
</section>
{{-- ════ BANNER: BUTUH BANTUAN ════ --}}
<section class="max-w-7xl mx-auto px-4 pb-6">
  <a href="https://sigap.brida.makassarkota.go.id/sigap-inkubatorma"
     target="_blank"
     class="group flex flex-col sm:flex-row items-center justify-between gap-4
            bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900
            rounded-2xl px-6 py-5 shadow-lg hover:shadow-xl transition-shadow">

    {{-- Kiri: ikon + teks --}}
    <div class="flex items-center gap-4">
      <div class="shrink-0 w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center text-2xl">
        🤝
      </div>
      <div>
        <p class="text-white font-bold text-base leading-snug">
          Butuh bantuan pengisian inovasi?
        </p>
        <p class="text-white/75 text-sm mt-0.5">
          Minta pendampingan langsung dari staff BRIDA melalui
          <span class="text-white font-semibold underline underline-offset-2">SIGAP Inkubatorma</span>
        </p>
      </div>
    </div>

    {{-- Kanan: tombol --}}
    <div class="shrink-0">
      <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   bg-white text-maroon font-semibold text-sm
                   group-hover:bg-white/90 transition">
        Buka Inkubatorma
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4M14 4h6m0 0v6m0-6L10 14"/>
        </svg>
      </span>
    </div>

  </a>
</section>
{{-- ════════════════════════════════════════════
     SECTION 1: METADATA
════════════════════════════════════════════ --}}
<section id="section-metadata" class="max-w-7xl mx-auto px-4 pb-6">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-8 h-8 rounded-lg bg-maroon flex items-center justify-center">
      <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" d="M9 12h6M9 16h6M7 3H4a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8l-5-5H7z"/>
      </svg>
    </div>
    <div>
      <h2 class="text-lg font-bold text-gray-900">Metadata Inovasi</h2>
      <p class="text-xs text-gray-500">{{ count($metadataItems) }} field yang perlu diisi</p>
    </div>
  </div>

  <form method="POST" action="{{ route('evidence.pedoman.meta.save') }}" class="space-y-3">
    @csrf

    @foreach($metadataItems as $i => $meta)
      @php
        $pm = $pedomanMeta[$meta['key']] ?? null;
        $ytUrl = $pm?->video_url ?? null;
        $ytId  = null;
        if ($ytUrl && preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $ytUrl, $m)) {
          $ytId = $m[1];
        }
      @endphp

      <details class="bg-white border border-gray-200 rounded-2xl overflow-hidden"
               id="meta-card-{{ $meta['key'] }}">
        <summary class="cursor-pointer select-none px-4 py-3 flex items-center gap-3">
          <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md
                       bg-maroon/10 text-maroon text-xs font-bold">
            {{ $i + 1 }}
          </span>
          <div class="flex-1">
            <h3 class="font-semibold text-gray-900 text-sm">{{ $meta['label'] }}</h3>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            @if($ytId)
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium">
                ▶ Video
              </span>
            @endif
            <svg class="chev w-4 h-4 text-gray-400 transition-transform"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M6 9l6 6 6-6" stroke-width="2"/>
            </svg>
          </div>
        </summary>

        <div class="border-t">
          <div class="grid md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-gray-100">

            {{-- KIRI: info + PDF --}}
            <div class="p-4 space-y-3">
              <div>
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">Deskripsi</p>
                <p class="text-sm text-gray-700 leading-relaxed">
                  {{ $pm?->deskripsi ?? $meta['deskripsi'] }}
                </p>
              </div>

              @role('admin')
              <div class="pt-2 border-t border-gray-100 space-y-2">
                <p class="text-[11px] text-gray-400 uppercase tracking-wide">Edit Pedoman</p>
                <input type="hidden" name="meta[{{ $meta['key'] }}][key]" value="{{ $meta['key'] }}">
                <textarea name="meta[{{ $meta['key'] }}][deskripsi]" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                 focus:border-maroon focus:ring-maroon resize-none"
                          placeholder="Deskripsi panduan pengisian…">{{ $pm?->deskripsi ?? $meta['deskripsi'] }}</textarea>
                <input type="url"
                       name="meta[{{ $meta['key'] }}][video_url]"
                       value="{{ $pm?->video_url ?? '' }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:border-maroon focus:ring-maroon"
                       placeholder="https://youtube.com/watch?v=... (link video tutorial)">
              </div>
              @endrole
            </div>

            {{-- KANAN: video YouTube --}}
            <div class="p-4 flex flex-col items-center justify-center bg-gray-50/50">
              @if($ytId)
                <div class="w-full yt-embed rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                  <iframe
                    src="https://www.youtube.com/embed/{{ $ytId }}"
                    class="w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                  </iframe>
                </div>
                <p class="text-[11px] text-gray-400 mt-2">Video tutorial pengisian {{ $meta['label'] }}</p>
              @else
                <div class="flex flex-col items-center justify-center gap-2 py-8 text-gray-300">
                  <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14M3 8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8z"/>
                  </svg>
                  <p class="text-sm text-gray-400">Video tutorial belum tersedia</p>
                  @role('admin')
                    <p class="text-xs text-gray-400">Tambahkan link YouTube di panel edit</p>
                  @endrole
                </div>
              @endif
            </div>

          </div>
        </div>
      </details>
    @endforeach

    @role('admin')
    <div class="flex justify-end pt-2">
      <button type="submit"
              class="px-5 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 font-medium">
        💾 Simpan Pedoman Metadata
      </button>
    </div>
    @endrole
  </form>
</section>

{{-- ════════════════════════════════════════════
     SECTION 2: EVIDENCE (20 Indikator)
════════════════════════════════════════════ --}}
<section id="section-evidence" class="max-w-7xl mx-auto px-4 pb-10">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-8 h-8 rounded-lg bg-maroon flex items-center justify-center">
      <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/>
      </svg>
    </div>
    <div>
      <h2 class="text-lg font-bold text-gray-900">Evidence — 20 Indikator</h2>
      <p class="text-xs text-gray-500">Panduan pengisian tiap indikator evidence beserta contoh dokumen</p>
    </div>
  </div>

  <form method="POST"
        enctype="multipart/form-data"
        action="{{ route('evidence.pedoman.save') }}"
        class="space-y-3">
    @csrf

    @foreach($evidenceItems as $i => $it)
      @php
        $ytUrl = $it['video_url'] ?? null;
        $ytId  = null;
        if ($ytUrl && preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $ytUrl, $m)) {
          $ytId = $m[1];
        }
      @endphp

      <details class="bg-white border border-gray-200 rounded-2xl overflow-hidden"
               id="ev-pedoman-{{ $it['no'] }}">
        <summary class="cursor-pointer select-none px-4 py-3 flex items-center gap-3">
          <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md
                       bg-maroon text-white text-xs font-bold">
            {{ $it['no'] }}
          </span>
          <div class="flex-1">
            <h3 class="font-semibold text-gray-900 text-sm leading-snug">{{ $it['indikator'] }}</h3>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            @if($it['file_url'])
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 font-medium">
                📄 PDF
              </span>
            @endif
            @if($ytId)
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-medium">
                ▶ Video
              </span>
            @endif
            <svg class="chev w-4 h-4 text-gray-400 transition-transform"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M6 9l6 6 6-6" stroke-width="2"/>
            </svg>
          </div>
        </summary>

        <div class="border-t">
          <div class="grid md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-gray-100">

            {{-- KIRI: deskripsi + PDF --}}
            <div class="p-4 space-y-3">

              {{-- Deskripsi --}}
              <div>
                <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">Panduan</p>
                <p class="text-sm text-gray-700 leading-relaxed">
                  {{ $it['deskripsi'] ?? 'Belum ada panduan.' }}
                </p>
              </div>

              {{-- PDF Preview --}}
              @if($it['file_url'])
                <div>
                  <p class="text-[11px] text-gray-400 uppercase tracking-wide mb-2">Contoh Dokumen</p>
                  <iframe src="{{ $it['file_url'] }}#toolbar=0"
                          class="w-full rounded-lg border"
                          style="height: 320px;">
                  </iframe>
                  <div class="flex gap-2 mt-2">
                    <a href="{{ $it['file_url'] }}" target="_blank" download
                       class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">
                      ⬇ Download
                    </a>
                    @role('admin')
                    <button type="button"
                            onclick="deletePedoman({{ $it['id'] }})"
                            class="px-3 py-1.5 rounded-md border border-rose-300 text-rose-600 text-xs hover:bg-rose-50">
                      🗑 Hapus File
                    </button>
                    @endrole
                  </div>
                </div>
              @else
                <div class="rounded-lg border border-dashed border-gray-300 p-4 text-center">
                  <p class="text-sm text-gray-400">Belum ada contoh dokumen PDF</p>
                </div>
              @endif

              {{-- Admin: edit form --}}
              @role('admin')
              <div class="pt-3 border-t border-gray-100 space-y-2">
                <p class="text-[11px] text-gray-400 uppercase tracking-wide">Edit Pedoman</p>
                <input type="hidden" name="no[]" value="{{ $it['no'] }}">
                <input type="text" name="indikator[]" value="{{ $it['indikator'] }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                              focus:border-maroon focus:ring-maroon"
                       placeholder="Nama indikator">
                <textarea name="deskripsi[]" rows="2"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                 focus:border-maroon focus:ring-maroon resize-none"
                          placeholder="Panduan pengisian…">{{ $it['deskripsi'] }}</textarea>
                <div>
                  <label class="text-xs text-gray-500 mb-1 block">Upload PDF Contoh</label>
                  <input type="file" name="file[{{ $i }}]" accept=".pdf"
                         class="text-sm">
                </div>
                <div>
                  <label class="text-xs text-gray-500 mb-1 block">Link Video Tutorial (YouTube)</label>
                  <input type="url" name="video_url[{{ $i }}]"
                         value="{{ $it['video_url'] ?? '' }}"
                         class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                focus:border-maroon focus:ring-maroon"
                         placeholder="https://youtube.com/watch?v=...">
                </div>
              </div>
              @endrole
            </div>

            {{-- KANAN: video YouTube --}}
            <div class="p-4 flex flex-col items-center justify-start bg-gray-50/50">
              @if($ytId)
                <div class="w-full yt-embed rounded-xl overflow-hidden border border-gray-200 shadow-sm">
                  <iframe
                    src="https://www.youtube.com/embed/{{ $ytId }}"
                    class="w-full h-full"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen>
                  </iframe>
                </div>
                <p class="text-[11px] text-gray-400 mt-2 text-center">
                  Video tutorial indikator {{ $it['no'] }}
                </p>
              @else
                <div class="flex flex-col items-center justify-center gap-2 py-10 w-full
                            rounded-xl border border-dashed border-gray-200 text-gray-300">
                  <svg class="w-12 h-12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0 1 21 8.618v6.764a1 1 0 0 1-1.447.894L15 14M3 8a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8z"/>
                  </svg>
                  <p class="text-sm text-gray-400">Video tutorial belum tersedia</p>
                </div>
              @endif
            </div>

          </div>
        </div>
      </details>
    @endforeach

    @role('admin')
    <div class="flex justify-end pt-2">
      <button type="submit"
              class="px-5 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 font-medium">
        💾 Simpan Pedoman Evidence
      </button>
    </div>
    @endrole
  </form>
</section>

{{-- Form delete tersembunyi --}}
<form id="deletePedomanForm" method="POST">
  @csrf @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
function deletePedoman(id) {
  if (!confirm('Hapus file contoh dokumen ini?')) return;
  const form = document.getElementById('deletePedomanForm');
  form.action = `/sigap-inovasi/pedoman-evidence/${id}`;
  form.submit();
}
</script>
@endpush