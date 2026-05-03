@extends('layouts.app')

@section('content')

<style>
.review-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
    background: white;
    transition: 0.2s;
}
</style>

<div class="max-w-7xl mx-auto px-4 py-6">

    <!-- HEADER -->
    <div class="sticky top-0 z-10 bg-white border-b px-4 py-3 flex justify-between items-center mb-4">
        <div>
            <h1 class="font-bold text-lg">Review Inovasi</h1>
            <p class="text-xs text-gray-500">{{ $inovasi->judul }}</p>
        </div>

        <div class="text-right">
            <p class="text-xs text-gray-500">Total Poin</p>
            <p id="totalPoint" class="text-xl font-bold text-maroon">0</p>
        </div>
    </div>

    <!-- REVIEWER -->
    <div class="bg-white p-4 rounded-xl shadow mb-4">
        <h3 class="font-semibold mb-2">Reviewer</h3>
        <div class="flex flex-wrap gap-2">
            @forelse($reviewers as $revId => $items)
                @php $user = $items->first()->reviewer; @endphp
                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">
                    👤 {{ $user->name ?? 'Unknown' }}
                </span>
            @empty
                <span class="text-sm text-gray-500">Belum ada reviewer</span>
            @endforelse
        </div>
    </div>

    <!-- FORM -->
    <form action="{{ route('inovasi.review.store', $inovasi->id) }}" method="POST">
        @csrf

        <div class="grid md:grid-cols-2 gap-4">

            @foreach($templates as $tpl)

           @php
                $field = $tpl->field;

                // mapping field file
                $fileMapping = [
                    'anggaran' => 'anggaran_file',
                    'profil_bisnis' => 'profil_bisnis_file',
                    'haki' => 'haki_file',
                    'penghargaan' => 'penghargaan_file',
                ];

                if(isset($fileMapping[$field])) {
                    $value = $inovasi->{$fileMapping[$field]} ?? null;
                } else {
                    $value = $inovasi->$field ?? null;
                }

                $review = $existing[$field] ?? null;
            @endphp

            <div class="review-card">

                <!-- TITLE -->
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold text-sm">
                        {{ $tpl->label }}
                    </h3>

                    <div class="flex items-center gap-2">
                        <span id="badge-{{ $field }}"
                            class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-500">
                            Belum
                        </span>

                        <span class="text-xs text-gray-400">
                            {{ $tpl->point }} poin
                        </span>
                    </div>
                </div>

                <!-- VALUE -->
                <div class="mb-3 text-sm bg-gray-50 p-3 rounded">

                    {{-- CONFIG --}}
                    @if(in_array($field, ['asta_cipta','program_prioritas','urusan_pemerintah']))
                        {{ config("inovasi.$field")[$value] ?? '-' }}

                    @elseif($field == 'misi_walikota')
                        {{ config('inovasi.misi_walikota')[$value] ?? '-' }}

                    {{-- FILE (PDF PREVIEW) --}}
                    @elseif(in_array($field, ['anggaran','profil_bisnis','haki','penghargaan']))

                        @php
                            $fileUrl = $value ? \Storage::disk('public')->url($value) : null;
                        @endphp

                        @if($fileUrl)
                            <iframe 
                                src="{{ $fileUrl }}#toolbar=0"
                                class="w-full h-64 rounded border bg-white">
                            </iframe>
                        @else
                            <span class="text-gray-400 italic">Tidak ada file</span>
                        @endif

                        {{-- REFERENSI / VIDEO --}}
                        @elseif(in_array($field, ['videos', 'referensi', 'referensi_video']))
                            @php
                                $refs = $inovasi->referensiVideos ?? collect();
                            @endphp

                            @if($refs->isEmpty())
                                <span class="italic text-gray-400">Belum ada referensi</span>
                            @else
                                <div class="space-y-3">
                                    @foreach($refs as $i => $ref)
                                        <div class="bg-white border border-gray-200 rounded-lg p-3">
                                            {{-- Nomor & Judul --}}
                                            <div class="flex items-start gap-2 mb-1">
                                                <span class="flex-shrink-0 w-5 h-5 rounded-full bg-maroon/10 text-maroon
                                                            text-[10px] font-bold flex items-center justify-center mt-0.5">
                                                    {{ $i + 1 }}
                                                </span>
                                                <p class="font-semibold text-sm text-gray-800 leading-snug">
                                                    {{ $ref->judul ?? '-' }}
                                                </p>
                                            </div>

                                            {{-- Deskripsi --}}
                                            @if(!empty($ref->deskripsi))
                                                <p class="text-xs text-gray-600 ml-7 mb-2 leading-relaxed">
                                                    {{ $ref->deskripsi }}
                                                </p>
                                            @endif

                                            {{-- Link --}}
                                            @if(!empty($ref->video_url))
                                                @php
                                                    $url = $ref->video_url;
                                                    // Deteksi YouTube
                                                    $isYoutube = str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be');
                                                    // Ambil embed ID jika youtube
                                                    $ytId = null;
                                                    if ($isYoutube) {
                                                        if (preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
                                                            $ytId = $m[1];
                                                        }
                                                    }
                                                @endphp

                                                @if($isYoutube && $ytId)
                                                    {{-- YouTube embed --}}
                                                    <div class="ml-7 mt-2">
                                                        <iframe
                                                            src="https://www.youtube.com/embed/{{ $ytId }}"
                                                            class="w-full rounded border"
                                                            style="height:160px"
                                                            frameborder="0"
                                                            allowfullscreen>
                                                        </iframe>
                                                    </div>
                                                @else
                                                    {{-- Link biasa --}}
                                                    <a href="{{ $url }}" target="_blank"
                                                    class="ml-7 inline-flex items-center gap-1 text-xs text-maroon
                                                            hover:underline break-all">
                                                        <svg class="w-3 h-3 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                            <path stroke-width="2" d="M10 6H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                        </svg>
                                                        {{ \Illuminate\Support\Str::limit($url, 55) }}
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach

                                    {{-- Ringkasan jumlah --}}
                                    <p class="text-[11px] text-gray-400 text-right">
                                        {{ $refs->count() }} referensi &bull;
                                        {{ $refs->filter(fn($r) => !empty($r->video_url))->count() }} punya link
                                    </p>
                                </div>
                            @endif
                    {{-- TEXT --}}
                    @else
                        {!! $value ?? '-' !!}
                    @endif

                </div>

                <!-- ACTION -->
                <div class="flex gap-2 mb-2">

                    <button type="button"
                        data-field="{{ $field }}"
                        data-status="accept"
                        class="btn-review px-3 py-1 text-xs bg-green-100 text-green-700 rounded">
                        ✅ Accept
                    </button>

                    <button type="button"
                        data-field="{{ $field }}"
                        data-status="revisi"
                        class="btn-review px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded">
                        ⚠️ Revisi
                    </button>

                    <button type="button"
                        data-field="{{ $field }}"
                        data-status="tolak"
                        class="btn-review px-3 py-1 text-xs bg-red-100 text-red-700 rounded">
                        ❌ Tolak
                    </button>

                </div>

                <!-- HIDDEN -->
                <input type="hidden"
                    name="status[{{ $field }}]"
                    id="input-{{ $field }}"
                    value="{{ $review->status ?? '' }}">

                <!-- COMMENT -->
                <textarea
                    name="comment[{{ $field }}]"
                    class="w-full border rounded p-2 text-sm"
                    placeholder="Komentar...">{{ $review->comment ?? '' }}</textarea>

            </div>

            @endforeach

        </div>

        <!-- SUBMIT -->
        <div class="mt-6 flex justify-end">
            <button class="px-4 py-2 bg-maroon text-white rounded-lg">
                Simpan Review
            </button>
        </div>

       
    </form>

     {{-- ═══════════════════════════════════════════════
            SECTION: REVIEW EVIDENCE (20 Indikator)
        ═══════════════════════════════════════════════ --}}
        <div class="max-w-7xl mx-auto px-4 mt-8 pb-10">

        <div class="flex items-center gap-3 mb-4">
            <h2 class="text-lg font-bold text-gray-900">Review Evidence</h2>
            <span class="text-xs px-2 py-0.5 rounded-full bg-maroon/10 text-maroon font-medium">
            20 Indikator
            </span>
        </div>

        @if(session('success_evidence'))
            <div class="mb-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
            {{ session('success_evidence') }}
            </div>
        @endif

        @if(session('error_evidence'))
        <div class="mb-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-800 border border-rose-200">
            {{ session('error_evidence') }}
        </div>
        @endif

        <form action="{{ route('inovasi.review.evidence.store', $inovasi->id) }}" method="POST">
            @csrf

            <div class="space-y-3">
            @foreach($evidenceItems as $it)
                @php
                $no       = (int) $it['no'];
                $evRev    = $existingEvRev[$no] ?? null;   // review yang sudah ada
                $hasParam = !empty($it['selected_label']) || ($it['selected_weight'] ?? 0) > 0;

                // File yang sudah diupload inovator
                $files = $it['files'] ?? [];
                @endphp

                <details class="bg-white border border-gray-200 rounded-2xl overflow-hidden"
                        id="ev-card-{{ $no }}">
                <summary class="cursor-pointer select-none px-4 py-3 flex items-start gap-3">

                    {{-- Nomor --}}
                    <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md
                                bg-maroon text-white text-xs font-bold mt-0.5">
                    {{ $no }}
                    </span>

                    {{-- Judul & keterangan --}}
                    <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm leading-snug">
                        {{ $it['indikator'] }}
                    </h3>
                    @if(!empty($it['keterangan']))
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">
                        {{ $it['keterangan'] }}
                        </p>
                    @endif
                    </div>

                    {{-- Badge parameter inovator --}}
                    <div class="flex items-center gap-2 flex-shrink-0">
                    @if($hasParam)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium">
                        Skor: {{ $it['selected_weight'] ?? 0 }}
                        </span>
                    @else
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">
                        Belum diisi
                        </span>
                    @endif

                    {{-- Badge status review (jika sudah direview) --}}
                    @if($evRev)
                        @php
                        $badgeCss = match($evRev->status) {
                            'accept' => 'bg-emerald-100 text-emerald-700',
                            'revisi' => 'bg-amber-100 text-amber-700',
                            'tolak'  => 'bg-rose-100 text-rose-700',
                        };
                        $badgeTxt = match($evRev->status) {
                            'accept' => '✅ Accept',
                            'revisi' => '✏️ Revisi',
                            'tolak'  => '❌ Tolak',
                        };
                        @endphp
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-medium {{ $badgeCss }}">
                        {{ $badgeTxt }}
                        </span>
                    @endif

                    <svg class="chev w-4 h-4 text-gray-400 transition-transform"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M6 9l6 6 6-6" stroke-width="2"/>
                    </svg>
                    </div>
                </summary>

                {{-- Isi detail --}}
                <div class="px-4 pb-4 pt-3 border-t bg-gray-50/40">
                    <div class="grid md:grid-cols-2 gap-4">

                    {{-- KIRI: apa yang diisi inovator --}}
                    <div class="space-y-3">
                        <div class="rounded-lg border bg-white p-3">
                        <p class="text-[11px] text-gray-400 mb-1 uppercase tracking-wide">
                            Parameter dipilih inovator
                        </p>
                        @if($hasParam)
                            <p class="text-sm font-medium text-gray-800">
                            {{ $it['selected_label'] ?? '—' }}
                            </p>
                            <p class="text-xs text-maroon font-semibold mt-0.5">
                            Skor: {{ $it['selected_weight'] ?? 0 }}
                            </p>
                        @else
                            <p class="text-sm text-gray-400 italic">Belum memilih parameter</p>
                        @endif
                        </div>

                        @if(!empty($it['deskripsi']))
                        <div class="rounded-lg border bg-white p-3">
                            <p class="text-[11px] text-gray-400 mb-1 uppercase tracking-wide">Deskripsi</p>
                            <p class="text-sm text-gray-700">{{ $it['deskripsi'] }}</p>
                        </div>
                        @endif

                        {{-- File/dokumen yang diupload --}}
                        @if(!empty($files))
                        <div class="rounded-lg border bg-white p-3">
                            <p class="text-[11px] text-gray-400 mb-2 uppercase tracking-wide">
                            Dokumen ({{ count($files) }})
                            </p>
                            <div class="space-y-2">
                            @foreach($files as $file)
                                <div class="flex items-center gap-2 text-xs">
                                <span>📄</span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate">
                                    {{ $file['nomor_surat'] ?? '—' }}
                                    </p>
                                    <p class="text-gray-500">
                                    {{ $file['tanggal_surat'] ?? '' }}
                                    @if(!empty($file['tentang']))
                                        · {{ \Str::limit($file['tentang'], 30) }}
                                    @endif
                                    </p>
                                </div>
                                @if(!empty($file['url']))
                                    <a href="{{ $file['url'] }}" target="_blank"
                                    class="text-maroon underline flex-shrink-0">
                                    Lihat
                                    </a>
                                @endif
                                </div>
                            @endforeach
                            </div>
                        </div>
                        @else
                        <div class="rounded-lg border bg-white p-3">
                            <p class="text-[11px] text-gray-400 mb-1 uppercase tracking-wide">Dokumen</p>
                            <p class="text-sm text-gray-400 italic">Belum ada dokumen diunggah</p>
                        </div>
                        @endif

                        {{-- Hint dari template --}}
                        @if(!empty($it['hint']))
                        <div class="rounded-lg border border-amber-100 bg-amber-50 p-3">
                            <p class="text-[11px] text-amber-700 font-semibold mb-1">💡 Pedoman</p>
                            <p class="text-xs text-amber-800 leading-relaxed">{{ $it['hint'] }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- KANAN: form review dari reviewer --}}
                    <div class="space-y-3">
                        <div class="rounded-lg border bg-white p-3">
                        <p class="text-[11px] text-gray-400 mb-2 uppercase tracking-wide">
                            Keputusan Review
                        </p>

                        {{-- Tombol Accept/Revisi/Tolak --}}
                        <div class="flex gap-2 mb-3" id="ev-btn-group-{{ $no }}">
                            @foreach([
                            ['accept', '✅ Accept',  'bg-emerald-100 text-emerald-700 border-emerald-300'],
                            ['revisi', '✏️ Revisi',  'bg-amber-100  text-amber-700  border-amber-300'],
                            ['tolak',  '❌ Tolak',   'bg-rose-100   text-rose-700   border-rose-300'],
                            ] as [$val, $lbl, $css])
                            <button type="button"
                                    data-evno="{{ $no }}"
                                    data-evstatus="{{ $val }}"
                                    class="ev-btn-review flex-1 px-2 py-1.5 text-xs rounded-lg border
                                            {{ $css }}
                                            {{ ($evRev?->status === $val) ? 'ring-2 ring-maroon ring-offset-1 font-bold' : 'opacity-70' }}">
                                {{ $lbl }}
                            </button>
                            @endforeach
                        </div>

                        {{-- Hidden input status --}}
                        <input type="hidden"
                                name="ev_status[{{ $no }}]"
                                id="ev-input-status-{{ $no }}"
                                value="{{ $evRev?->status ?? '' }}">

                        {{-- Komentar --}}
                        <label class="block">
                            <span class="text-xs text-gray-500">Komentar untuk inovator</span>
                            <textarea name="ev_comment[{{ $no }}]"
                                    rows="3"
                                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                                            focus:border-maroon focus:ring-maroon resize-none"
                                    placeholder="Tulis komentar (opsional)…">{{ $evRev?->comment ?? '' }}</textarea>
                        </label>
                        </div>

                        {{-- Review dari reviewer lain --}}
                        @php
                        $otherEvRevs = $inovasi->evidenceReviewItems
                            ->where('no', $no)
                            ->where('reviewer_id', '!=', Auth::id())
                            ->values();
                        @endphp
                        @if($otherEvRevs->isNotEmpty())
                        <div class="rounded-lg border bg-gray-50 p-3 mt-2">
                            <p class="text-[11px] text-gray-400 mb-2 uppercase tracking-wide">
                            Review Reviewer Lain
                            </p>
                            <div class="space-y-2">
                            @foreach($otherEvRevs as $other)
                                @php
                                $otherCss = match($other->status) {
                                    'accept' => 'bg-emerald-100 text-emerald-700',
                                    'revisi' => 'bg-amber-100 text-amber-700',
                                    'tolak'  => 'bg-rose-100 text-rose-700',
                                    default  => 'bg-gray-100 text-gray-600',
                                };
                                $otherTxt = match($other->status) {
                                    'accept' => '✅ Accept',
                                    'revisi' => '✏️ Revisi',
                                    'tolak'  => '❌ Tolak',
                                    default  => '—',
                                };
                                @endphp
                                <div class="flex items-start gap-2 text-xs">
                                <span class="shrink-0 font-semibold text-gray-600">
                                    👤 {{ $other->reviewer?->name ?? 'Reviewer' }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded-full text-[9px] font-medium {{ $otherCss }}">
                                    {{ $otherTxt }}
                                </span>
                                @if(!empty($other->comment))
                                    <span class="text-gray-600 flex-1">{{ $other->comment }}</span>
                                @endif
                                </div>
                            @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    </div>
                </div>
                </details>
            @endforeach
            </div>

            {{-- Tombol simpan --}}
            <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Review akan disimpan per indikator yang dipilih statusnya.
            </p>
            <button type="submit"
                    class="px-5 py-2 bg-maroon text-white rounded-lg hover:bg-maroon-800 font-medium">
                💾 Simpan Review Evidence
            </button>
            </div>

        </form>
        </div>

        <script>
        // Handler tombol review evidence
        document.querySelectorAll('.ev-btn-review').forEach(btn => {
        btn.addEventListener('click', function () {
            const no     = this.dataset.evno;
            const status = this.dataset.evstatus;

            // Set hidden input
            document.getElementById('ev-input-status-' + no).value = status;

            // Update tampilan tombol dalam grup
            document.querySelectorAll(`#ev-btn-group-${no} .ev-btn-review`).forEach(b => {
            b.classList.remove('ring-2','ring-maroon','ring-offset-1','font-bold');
            b.classList.add('opacity-70');
            });
            this.classList.add('ring-2','ring-maroon','ring-offset-1','font-bold');
            this.classList.remove('opacity-70');

            // Update badge di summary
            const card   = document.getElementById('ev-card-' + no);
            let badge    = card.querySelector('.ev-status-badge');
            if (!badge) {
            badge = document.createElement('span');
            badge.className = 'ev-status-badge text-[10px] px-2 py-0.5 rounded-full font-medium';
            card.querySelector('summary .flex.items-center.gap-2').insertBefore(
                badge,
                card.querySelector('summary .chev')
            );
            }
            const cfg = {
            accept: { css: 'bg-emerald-100 text-emerald-700', txt: '✅ Accept' },
            revisi: { css: 'bg-amber-100 text-amber-700',     txt: '✏️ Revisi' },
            tolak:  { css: 'bg-rose-100 text-rose-700',       txt: '❌ Tolak' },
            };
            badge.className = 'ev-status-badge text-[10px] px-2 py-0.5 rounded-full font-medium ' + cfg[status].css;
            badge.textContent = cfg[status].txt;
        });
        });
        </script>


    <!-- FLOATING SCORE -->
    <div id="floatingScore"
        class="fixed bottom-6 right-6 bg-maroon text-white px-4 py-3 rounded-xl shadow-lg">

        <p class="text-xs opacity-80">Total Poin</p>
        <p id="totalPointFloating" class="text-lg font-bold">0</p>
    </div>

</div>

<script>
const reviewPoints = @json($templates->map(fn($t) => [
    'field' => $t->field,
    'point' => $t->point
]));

function calculateTotal() {
    let total = 0;

    reviewPoints.forEach(item => {
        let el = document.getElementById('input-' + item.field);
        if(el && el.value === 'accept') {
            total += item.point;
        }
    });

    document.getElementById('totalPoint').innerText = total;
    document.getElementById('totalPointFloating').innerText = total;
}

function setUI(field, status) {
    const badge = document.getElementById('badge-' + field);
    const card  = document.getElementById('input-' + field)?.closest('.review-card');

    if(!badge || !card) return;

    if(status === 'accept') {
        badge.innerText = 'Accepted';
        badge.className = 'text-xs px-2 py-1 rounded bg-green-100 text-green-700';
        card.style.borderLeft = '4px solid #16a34a';
    }

    if(status === 'revisi') {
        badge.innerText = 'Revisi';
        badge.className = 'text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-700';
        card.style.borderLeft = '4px solid #f59e0b';
    }

    if(status === 'tolak') {
        badge.innerText = 'Ditolak';
        badge.className = 'text-xs px-2 py-1 rounded bg-red-100 text-red-700';
        card.style.borderLeft = '4px solid #dc2626';
    }
}

document.querySelectorAll('.btn-review').forEach(btn => {
    btn.addEventListener('click', function () {

        const field = this.dataset.field;
        const status = this.dataset.status;

        const input = document.getElementById('input-' + field);
        if(!input) return;

        input.value = status;

        this.parentElement.querySelectorAll('button')
            .forEach(b => b.classList.remove('ring-2','ring-maroon'));

        this.classList.add('ring-2','ring-maroon');

        setUI(field, status);
        calculateTotal();
    });
});

// INIT LOAD
reviewPoints.forEach(item => {
    let el = document.getElementById('input-' + item.field);
    if(el && el.value) {
        setUI(item.field, el.value);
    }
});

calculateTotal();
</script>

@endsection