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
<section class="max-w-7xl mx-auto px-4 pb-4">
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

{{-- ════ BANNER: AI TEXT GENERATOR (NEW) ════ --}}
<section class="max-w-7xl mx-auto px-4 pb-6">
  <div onclick="openAiModal()"
       class="cursor-pointer group flex flex-col sm:flex-row items-center justify-between gap-4
              bg-gradient-to-r from-purple-700 via-purple-800 to-indigo-900
              rounded-2xl px-6 py-5 shadow-lg hover:shadow-xl transition-all border border-purple-500/30">

    {{-- Kiri: ikon + teks --}}
    <div class="flex items-center gap-4">
      <div class="shrink-0 w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl shadow-inner">
        ✨
      </div>
      <div>
        <p class="text-white font-bold text-base leading-snug">
          AI Text Generator Inovasi
        </p>
        <p class="text-purple-200 text-sm mt-0.5">
          Bantu buat narasi Rancang Bangun, Tujuan & Hasil otomatis dengan standar resmi.
        </p>
      </div>
    </div>

    {{-- Kanan: tombol --}}
    <div class="shrink-0">
      <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                   bg-white text-purple-800 font-bold text-sm
                   group-hover:scale-105 transition-transform shadow-md">
        Mulai Generate Text
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
      </span>
    </div>
  </div>
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

{{-- ════════════════════════════════════════════
     MODAL AI TEXT GENERATOR
════════════════════════════════════════════ --}}
<div id="aiModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
    <div class="relative bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl w-full border border-gray-100">
      
      {{-- Modal Header --}}
      <div class="bg-gradient-to-r from-purple-700 to-indigo-800 px-6 py-4 flex justify-between items-center">
        <div>
          <h3 class="text-lg leading-6 font-bold text-white flex items-center gap-2" id="modal-title">
            ✨ AI Prompt Generator Inovasi
          </h3>
          <p class="text-xs text-purple-200 mt-1">Isi data di bawah untuk menghasilkan kerangka perintah AI sesuai pedoman resmi</p>
        </div>
        <button onclick="closeAiModal()" class="text-white hover:text-red-300 transition bg-white/10 hover:bg-white/20 p-2 rounded-lg">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>

      {{-- Modal Body --}}
      <div class="px-6 py-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Inovasi</label>
            <input type="text" id="ai_nama" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: BANK SAMPAH DIGITAL">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SKPD / Instansi</label>
            <input type="text" id="ai_skpd" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: Dinas Lingkungan Hidup">
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">
              Topik Utama Masalah <span class="text-rose-500">*</span>
            </label>
            <textarea type="text" id="ai_topik" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: Penumpukan sampah di tempat pembuangan sementara akibat armada pengangkut yang terbatas" rows="3"></textarea>
            <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
              💡 **Panduan:** Tuliskan kondisi negatif/kendala utama yang terjadi di lapangan atau SKPD Anda saat ini (Minimal 30 karakter).
            </p>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">
              Target / Harapan Inovasi <span class="text-rose-500">*</span>
            </label>
            <textarea type="text" id="ai_target" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm" placeholder="Contoh: Mempercepat waktu pengangkutan sampah dan meminimalisir penumpukan bau di area pemukiman" rows="3"></textarea>
            <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">
              💡 **Panduan:** Tuliskan kondisi ideal/solusi yang ingin dicapai setelah inovasi ini berjalan (Minimal 30 karakter). Jangan gunakan angka persentase fiktif.
            </p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Poin / Manfaat</label>
            <input type="number" id="ai_jumlah" value="4" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm">
          </div>
        </div>

        <div class="mt-5 flex justify-end">
          <button onclick="generatePrompt()" type="button" class="px-5 py-2.5 bg-purple-600 text-white font-semibold rounded-xl shadow hover:bg-purple-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            Generate Prompt
          </button>
        </div>

        {{-- Result Area --}}
        <div id="ai_result_area" class="hidden mt-5 pt-5 border-t border-gray-200">
          <label class="block text-sm font-bold text-gray-800 mb-2">Hasil Prompt (Salin teks di bawah ini ke AI)</label>
          <textarea id="ai_prompt_text" rows="12" class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm bg-gray-50 font-mono text-gray-700 leading-relaxed" readonly></textarea>
          
          <div class="mt-4 flex flex-wrap gap-3 justify-end">
            <button onclick="copyPrompt()" class="px-4 py-2 border border-purple-600 text-purple-700 font-medium rounded-lg hover:bg-purple-50 transition flex items-center gap-2 text-sm">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
              Copy Teks
            </button>
            <a href="https://chatgpt.com/" target="_blank" class="px-4 py-2 bg-[#10a37f] text-white font-medium rounded-lg hover:bg-[#0e906f] transition flex items-center gap-2 text-sm">
              🤖 Buka ChatGPT
            </a>
            <a href="https://gemini.google.com/app" target="_blank" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-lg hover:opacity-90 transition flex items-center gap-2 text-sm">
              ✨ Buka Gemini
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function deletePedoman(id) {
  if (!confirm('Hapus file contoh dokumen ini?')) return;
  const form = document.getElementById('deletePedomanForm');
  form.action = `/sigap-inovasi/pedoman-evidence/${id}`;
  form.submit();
}

// === FUNGSI MODAL AI TEXT GENERATOR ===
function openAiModal() {
  document.getElementById('aiModal').classList.remove('hidden');
}

function closeAiModal() {
  document.getElementById('aiModal').classList.add('hidden');
  // Sembunyikan kembali area hasil jika modal ditutup
  document.getElementById('ai_result_area').classList.add('hidden');
}

function generatePrompt() {
  const nama = document.getElementById('ai_nama').value.trim();
  const skpd = document.getElementById('ai_skpd').value.trim();
  const topik = document.getElementById('ai_topik').value.trim();
  const target = document.getElementById('ai_target').value.trim();
  const jumlah = document.getElementById('ai_jumlah').value || '4';

  // --- VALIDASI INPUT OPERATOR ---
  if (!nama || !skpd) {
    Swal.fire({
      icon: 'warning',
      title: 'Isian Belum Lengkap',
      text: 'Mohon isi Nama Inovasi dan SKPD/Instansi terlebih dahulu!',
      confirmButtonColor: '#6B21A8' // Warna ungu menyesuaikan tema AI
    });
    return;
  }

  if (topik.length < 30) {
    Swal.fire({
      icon: 'warning',
      title: 'Topik Masalah Terlalu Pendek',
      text: 'Mohon uraikan Topik Utama Masalah dengan lebih jelas (Minimal 30 karakter). Saat ini baru ' + topik.length + ' karakter.',
      confirmButtonColor: '#6B21A8'
    });
    return;
  }

  if (target.length < 30) {
    Swal.fire({
      icon: 'warning',
      title: 'Target Inovasi Terlalu Pendek',
      text: 'Mohon uraikan Target / Harapan Inovasi secara detail (Minimal 30 karakter). Saat ini baru ' + target.length + ' karakter.',
      confirmButtonColor: '#6B21A8'
    });
    return;
  }

  // --- JIKA VALIDASI LOLOS, GENERATE PROMPT ---
  const promptText = `Halo! Bertindaklah sebagai Konsultan Inovasi Pemerintahan Daerah Kota Makassar. 
Buatkan saya narasi Inovasi Daerah dengan format, pola, dan struktur penomoran yang SANGAT SPESIFIK seperti di bawah ini. JANGAN mengubah struktur hirarkinya.

DATA INOVASI:
- Nama Inovasi: ${nama}
- SKPD: ${skpd} Kota Makassar
- Topik / Fokus Masalah: ${topik}
- Target / Harapan Inovasi: ${target}

INSTRUKSI KETAT:
1. JANGAN mengarang atau berhalusinasi membuat angka persentase/statistik yang tidak disebutkan di atas.
2. Gunakan bahasa kualitatif yang rasional dan terukur secara logis (misalnya: "Meningkatnya...", "Menurunnya...", "Terwujudnya...").
3. Untuk poin "Dasar Hukum", Anda WAJIB menyebutkan Nomor dan Tahun peraturannya secara spesifik (Contoh: Undang-Undang Nomor ... Tahun ..., Peraturan Pemerintah Nomor ... Tahun ..., dll) yang relevan dengan topik.
4. Total kata untuk bagian "Rancang Bangun" minimal 300 kata.

TULISLAH DENGAN FORMAT DAN URUTAN BERIKUT INI:

**Rancang Bangun**
1. Dasar Hukum Inovasi
(Sebutkan minimal 4 aturan: UU, PP, Permendagri, dan Perda/Perwali Kota Makassar terkait topik inovasi beserta nomor dan tahunnya secara spesifik).

2. Latar Belakang dan Permasalahan
2.1 Latar Belakang
(Uraikan alasan inovasi ini dibentuk sebagai perbaikan atau pengembangan dari layanan sebelumnya berdasarkan fokus masalah: ${topik}).
2.2 Permasalahan Makro (Nasional/Global)
(Uraikan masalah makro terkait tata kelola/tuntutan nasional).
2.3 Permasalahan Mikro (Daerah/Inovasi)
(Uraikan permasalahan spesifik yang dihadapi oleh ${skpd} Kota Makassar saat ini berdasarkan masalah: ${topik}).

3. Isu Strategis
3.1 Isu Global (Berbasis SDGs)
(Sebutkan pilar SDGs yang sangat relevan dan jelaskan peran inovasi ini terhadap pilar tersebut).
3.2 Isu Nasional
(Sebutkan isu nasional terkait topik).
3.3 Isu Lokal (Makassar)
(Sebutkan isu lokal peningkatan kualitas layanan atau capaian SPM di Makassar terkait topik ini).

4. Metode Pembaharuan
(Uraikan perbandingan menjadi dua sub-poin: "Sebelum Inovasi" dan "Sesudah Inovasi").

5. Keunggulan dan Kebaruan
5.1 Keunggulan
(Sebutkan minimal 3 poin keunggulan).
5.2 Kebaruan
(Sebutkan kebaruannya, serta jelaskan bahwa inovasi ini memiliki SOP layanan cepat dengan estimasi waktu tertentu yang tidak lebih dari 24 jam).

**Tujuan dan Manfaat**

Tujuan Inovasi
(Sebutkan ${jumlah} poin tujuan dengan kata kerja awalan: Meningkatkan, Menurunkan, Mempercepat, atau Mengoptimalkan).

Manfaat Inovasi
(Uraikan manfaat inovasi ini dengan mengelompokkannya ke dalam 4 sub-judul berikut:)
1. Bagi Pemerintah Daerah
2. Bagi Perangkat Daerah (${skpd})
3. Bagi Inovator dan SDM
4. Bagi Masyarakat

**Hasil Inovasi**
(Sebutkan pencapaian inovasi ini tanpa menyebutkan persentase fiktif. Jelaskan bagaimana inovasi ini berhasil mewujudkan target: ${target}. Gunakan poin-poin yang diawali dengan kata kerja kualitatif positif).`;

  document.getElementById('ai_prompt_text').value = promptText;
  document.getElementById('ai_result_area').classList.remove('hidden');

  // Notifikasi sukses menggunakan SweetAlert
  Swal.fire({
    icon: 'success',
    title: 'Prompt Berhasil Dibuat!',
    text: 'Silakan salin teks di bawah untuk ditempel ke ChatGPT atau Gemini.',
    confirmButtonColor: '#6B21A8'
  });
}

function copyPrompt() {
  const textarea = document.getElementById('ai_prompt_text');
  textarea.select();
  textarea.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(textarea.value).then(() => {
    Swal.fire({
      icon: 'success',
      title: 'Teks Berhasil Disalin!',
      text: 'Sekarang Anda tinggal melakukan Paste (Ctrl+V) pada ChatGPT atau Gemini.',
      timer: 2000,
      showConfirmButton: false
    });
  });
}
</script>
@endpush