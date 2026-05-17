@extends('layouts.app')
<style>
  /* Badge AI kecil dengan “neural orbit” */
  .ai-hdr-badge{
    position:relative;width:18px;height:18px;border-radius:9999px;
    box-shadow:inset 0 0 0 2px rgba(122,34,34,.28); /* maroon */
  }
  .ai-hdr-badge::before,.ai-hdr-badge::after{
    content:"";position:absolute;top:50%;left:50%;
    width:4px;height:4px;border-radius:9999px;background:#7a2222; /* maroon */
    transform-origin:-6px -6px;
  }
  .ai-hdr-badge::before{ animation:aihdr-spin 1.6s linear infinite; }
  .ai-hdr-badge::after{ background:#f59e0b; animation:aihdr-spin 1.6s linear infinite .4s; }
  @keyframes aihdr-spin { to { transform: rotate(360deg) } }
  /* QUILL FIX – FINAL */
.quill-wrapper {
  width: 100%;
  margin-bottom: 2rem; /* jarak ke field berikutnya */
}

.quill-wrapper .ql-container {
  min-height: 220px;
}

.quill-wrapper .ql-editor {
  min-height: 200px;
  line-height: 1.6;
}

</style>
<style>
/* ========================================
   SUPER EYE CATCHING TUTORIAL BUTTON
======================================== */
.tutorial-btn{
  position: relative;
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  gap: .6rem;

  padding: 12px 20px;
  border-radius: 14px;

  background: linear-gradient(135deg,#7a2222,#a83232,#d14545);
  background-size: 200% 200%;

  color: white !important;
  font-weight: 800;
  letter-spacing: .2px;

  border: none;

  animation:
    tutorialGradient 4s ease infinite,
    tutorialBounce 1.5s infinite,
    tutorialGlow 1.2s infinite;

  box-shadow:
    0 0 0 rgba(122,34,34,0.5),
    0 10px 25px rgba(122,34,34,.35);

  transition: all .25s ease;
}

/* Hover */
.tutorial-btn:hover{
  transform: scale(1.06);
  box-shadow:
    0 0 25px rgba(220,38,38,.8),
    0 0 50px rgba(220,38,38,.4);
}

/* Shine effect */
.tutorial-btn::before{
  content:'';
  position:absolute;
  top:0;
  left:-120%;
  width:70%;
  height:100%;

  background:linear-gradient(
    120deg,
    transparent,
    rgba(255,255,255,.8),
    transparent
  );

  transform: skewX(-25deg);
  animation:tutorialShine 2s infinite;
}

/* Pulsing ring */
.tutorial-btn::after{
  content:'';
  position:absolute;
  inset:-6px;
  border-radius:18px;
  border:3px solid rgba(239,68,68,.45);

  animation:tutorialRing 1.5s infinite;
}

/* Badge */
.tutorial-badge{
  position:absolute;
  top:-10px;
  right:-10px;

  background:#facc15;
  color:#111827;

  font-size:10px;
  font-weight:900;

  padding:4px 8px;
  border-radius:999px;

  box-shadow:0 4px 10px rgba(0,0,0,.2);

  animation:tutorialBadge 1s infinite;
  z-index:2;
}

/* Floating icon */
.tutorial-icon{
  font-size:18px;
  animation:tutorialIcon 1s infinite alternate;
}

/* Animations */
@keyframes tutorialGradient{
  0%{ background-position:0% 50%; }
  50%{ background-position:100% 50%; }
  100%{ background-position:0% 50%; }
}

@keyframes tutorialBounce{
  0%,100%{ transform:translateY(0); }
  50%{ transform:translateY(-6px); }
}

@keyframes tutorialGlow{
  0%{
    box-shadow:0 0 0 rgba(239,68,68,.4);
  }
  50%{
    box-shadow:
      0 0 20px rgba(239,68,68,.9),
      0 0 40px rgba(239,68,68,.5);
  }
  100%{
    box-shadow:0 0 0 rgba(239,68,68,.4);
  }
}

@keyframes tutorialShine{
  0%{ left:-120%; }
  60%{ left:140%; }
  100%{ left:140%; }
}

@keyframes tutorialRing{
  0%{
    transform:scale(1);
    opacity:.7;
  }
  100%{
    transform:scale(1.12);
    opacity:0;
  }
}

@keyframes tutorialBadge{
  0%,100%{
    transform:scale(1);
  }
  50%{
    transform:scale(1.12);
  }
}

@keyframes tutorialIcon{
  from{
    transform:rotate(-8deg) scale(1);
  }
  to{
    transform:rotate(8deg) scale(1.15);
  }
}
</style>

@section('content')
  <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Inovasi</h1>
        <p class="text-sm text-gray-600 mt-1">Direktori inovasi daerah: cari, pantau tahapan, dan kelola unggahan tiap OPD.</p>
      </div>
      <div class="flex flex-wrap gap-2">
      <a href="{{ route('evidence.pedoman') }}"
        class="tutorial-btn">

        <span class="tutorial-badge" style="margin-top: 1em; margin-right:1em">
          WAJIB DIBACA
        </span>

        <span class="tutorial-icon">
          📘
        </span>

        <span>
          Tutorial Pengisian Metadata & Evidence
        </span>
      </a>
        <button id="btnTambah" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 5v14M5 12h14"/></svg>
          Tambah Inovasi
        </button>
      </div>
    </div>

  </section>

  <!-- Filter & Search -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-6 gap-3" method="GET" action="{{ route('sigap-inovasi.index') }}">
        <div>
          <label class="text-sm font-semibold text-gray-700">Tahapan Inovasi</label>
          <select name="tahap" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Inisiatif','Uji Coba','Penerapan'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['tahap'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Jenis Urusan</label>
          <select name="urusan" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Kesehatan','Pendidikan','Air Bersih','Transportasi'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['urusan'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Inisiator</label>
          <select name="inisiator" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['OPD','Unit Kerja','Kolaborasi'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['inisiator'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Tahap Inovasi</label>
          <select name="tahap_inovasi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Inisiatif','Uji Coba','Penerapan'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['tahap_inovasi'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Status Tahap</label>
          <select name="tahap_status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Belum','Berjalan','Selesai'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['tahap_status'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
        <label class="text-sm font-semibold text-gray-700">Status Asistensi</label>
          <select name="asistensi_status"
            class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            
            <option value="">Semua</option>
            @foreach(['Menunggu Verifikasi','Disetujui','Revisi','Dikembalikan','Ditolak'] as $opt)
              <option value="{{ $opt }}"
                @selected(($filters['asistensi_status'] ?? '')==$opt)>
                {{ $opt }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="lg:col-span-6 grid md:grid-cols-3 gap-3 pt-1">
          <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-700">Pencarian</label>
            <div class="relative mt-1.5">
              <input name="q" value="{{ $filters['q'] ?? '' }}" type="search" class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pl-9" placeholder="Judul / OPD / Kata kunci…">
              <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
          </div>
          <div class="flex items-end gap-3">
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition w-full md:w-auto">Cari</button>
            <a href="{{ route('sigap-inovasi.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 w-full md:w-auto">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </section>
  @hasanyrole('admin|verificator_inovasi')
<section class="max-w-7xl mx-auto px-4 mt-4">
  <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h3 class="text-base font-bold text-gray-900">Export Inovasi</h3>
        <p class="text-sm text-gray-600 mt-1">
          Export data inovasi beserta relasinya ke Excel. Bisa difilter per jenis urusan, tahap, status aktif, dan status revisi.
        </p>
      </div>

      <form id="exportInovasiForm"
            method="GET"
            action="{{ route('sigap-inovasi.export') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-3 w-full">

        {{-- pertahankan filter halaman yang sedang aktif --}}
        @foreach(request()->only(['q','tahap','urusan','inisiator','asistensi_status','sort']) as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ $v }}">
        @endforeach

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Jenis Urusan</span>
          <select name="urusan" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Kesehatan','Pendidikan','Air Bersih','Transportasi'] as $opt)
              <option value="{{ $opt }}" @selected(request('urusan') == $opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </label>

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Tahap Inovasi</span>
          <select name="tahap" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Inisiatif','Uji Coba','Penerapan'] as $opt)
              <option value="{{ $opt }}" @selected(request('tahap') == $opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </label>

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Status Aktif</span>
          <select name="export_status_aktif" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="semua">Semua</option>
            <option value="aktif">Aktif</option>
            <option value="nonaktif">Tidak Aktif</option>
          </select>
        </label>

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Status Revisi</span>
          <select name="export_status_revisi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="semua">Semua</option>
            <option value="ada">Ada Revisi</option>
            <option value="tidak">Tidak Ada Revisi</option>
          </select>
        </label>

        <div class="flex items-end">
          <button type="submit"
                  class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 active:bg-green-800 transition w-full shadow-md hover:shadow-lg flex items-center justify-center gap-2 font-semibold">
            
            <svg xmlns="http://www.w3.org/2000/svg"
                class="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 24 24">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zm0 1.5L18.5 8H14zM9.4 17 11 14.7 12.6 17h1.8l-2.3-3.3 2.1-3h-1.7L11 13l-1.5-2.3H7.8l2.1 3L7.6 17z"/>
            </svg>

            Export Excel
          </button>
        </div>
      </form>
    </div>
  </div>
</section>

<iframe id="download-frame" name="download-frame" class="hidden"></iframe>
@endhasanyrole

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span>
          @if($items->total())
            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }}
          @else
            Tidak ada data
          @endif
        </span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Urutkan</label>
          <form method="GET" action="{{ route('sigap-inovasi.index') }}">
            @foreach(request()->except('sort') as $k => $v)
              <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <select name="sort" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon" onchange="this.form.submit()">
              <option value="terbaru" @selected(($filters['sort'] ?? 'terbaru')==='terbaru')>Terbaru</option>
              <option value="judul"   @selected(($filters['sort'] ?? '')==='judul')>Judul (A-Z)</option>
            </select>
          </form>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-fixed">
          <colgroup>
          <col class="w-[28%]" />  <!-- Inovasi -->
          <col class="w-[10%]" />  <!-- Jenis Urusan -->
          <col class="w-[12%]" />  <!-- Inisiator -->
          <col class="w-[10%]" />  <!-- Tahap Inovasi -->
          <col class="w-[12%]" />  <!-- OPD/Unit -->
          <col class="w-[18%]" />  <!-- Asistensi/Review -->
          <col class="w-[16%]" />  <!-- Aksi -->
        </colgroup>
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3">Inovasi</th>
              <th class="px-4 py-3">Jenis Urusan</th>
              <th class="px-4 py-3">Inisiator</th>
              <th class="px-4 py-3">Tahap Inovasi</th>
              <th class="px-4 py-3">OPD/Unit</th>
              <th class="px-4 py-3">Asistensi</th>
              @hasanyrole('admin|verificator_inovasi')
              <th class="px-4 py-3">Status</th>
              @endhasanyrole
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($items as $inv)
              <tr>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded bg-maroon/10 flex items-center justify-center text-maroon">
                      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ $inv->judul }}</p>
                      <p class="text-xs text-gray-600 line-clamp-1">
                        {!! \Illuminate\Support\Str::limit(strip_tags($inv->rancang_bangun ?? '-'), 20) !!}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">{{ $inv->urusan_pemerintah ?? '-' }}</td>
                <td class="px-4 py-3">{{ $inv->inisiator_nama ?? '-' }}</td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-1">
                  {{$inv->tahap_inovasi??'-'}}
                  </div>
                </td>
                <td class="px-4 py-3">{{ $inv->opd_unit ?? '-' }}</td>
                {{-- ══ KOLOM ASISTENSI — dibedakan per role ══ --}}
                <td class="px-4 py-3">

                  @hasanyrole('admin|verificator_inovasi')
                  {{-- ── ADMIN: hanya tombol Review ── --}}
                  <a href="{{ route('inovasi.review', $inv->id) }}"
                    class="inline-block px-3 py-1.5 rounded-md border border-maroon text-maroon text-xs
                            hover:bg-maroon hover:text-white transition text-center">
                    Review
                  </a>
                  @if(!empty($inv->asistensi_note))
                    <p class="text-[10px] text-gray-500 mt-1 line-clamp-2" title="{{ $inv->asistensi_note }}">
                      💬 {{ \Illuminate\Support\Str::limit($inv->asistensi_note, 60) }}
                    </p>
                  @endif

                  @else
                  @php
                  // ── Review Metadata ──
                  $reviewItems = $inv->reviewItems ?? collect();

                  $hasRevisi = $reviewItems->contains('status', 'revisi');
                  $hasTolak  = $reviewItems->contains('status', 'tolak');
                  $hasAccept = $reviewItems->isNotEmpty()
                              && $reviewItems->every(fn($r) => $r->status === 'accept');

                  if ($hasTolak)      { $rvStatus = 'Ditolak';       $rvCss = 'bg-rose-50 text-rose-700'; }
                  elseif ($hasRevisi) { $rvStatus = 'Revisi';         $rvCss = 'bg-amber-50 text-amber-700'; }
                  elseif ($hasAccept) { $rvStatus = 'Disetujui';      $rvCss = 'bg-emerald-50 text-emerald-700'; }
                  else                { $rvStatus = 'Menunggu Review'; $rvCss = 'bg-gray-100 text-gray-600'; }

                  $acceptField = $reviewItems->groupBy('field')
                                  ->filter(fn($g) => $g->every(fn($r) => $r->status === 'accept'))->count();
                  $revisiField = $reviewItems->groupBy('field')
                                  ->filter(fn($g) => $g->contains('status','revisi'))->count();
                  $tolakField  = $reviewItems->groupBy('field')
                                  ->filter(fn($g) => $g->contains('status','tolak'))->count();

                  // ── Review Evidence ──
                  $evItems = $inv->evidenceReviewItems ?? collect();

                  $evHasTolak  = $evItems->contains('status', 'tolak');
                  $evHasRevisi = $evItems->contains('status', 'revisi');
                  $evHasAccept = $evItems->isNotEmpty()
                                && $evItems->every(fn($r) => $r->status === 'accept');

                  if ($evHasTolak)      { $evStatus = 'Ditolak';       $evCss = 'bg-rose-50 text-rose-700'; }
                  elseif ($evHasRevisi) { $evStatus = 'Revisi';         $evCss = 'bg-amber-50 text-amber-700'; }
                  elseif ($evHasAccept) { $evStatus = 'Disetujui';      $evCss = 'bg-emerald-50 text-emerald-700'; }
                  else                  { $evStatus = 'Belum Direview'; $evCss = 'bg-gray-100 text-gray-500'; }

                  $evAccept = $evItems->where('status','accept')->groupBy('no')->count();
                  $evRevisi = $evItems->where('status','revisi')->groupBy('no')->count();
                  $evTolak  = $evItems->where('status','tolak')->groupBy('no')->count();

                  // ── Status Asistensi ──
                  $astStatus = $inv->asistensi_status ?? 'Menunggu Verifikasi';
                  $astCss    = match($astStatus) {
                    'Disetujui'              => 'bg-emerald-50 text-emerald-700',
                    'Revisi', 'Dikembalikan' => 'bg-amber-50 text-amber-700',
                    'Ditolak'                => 'bg-rose-50 text-rose-700',
                    default                  => 'bg-gray-100 text-gray-600',
                  };
                @endphp

                <div class="flex flex-col gap-2">

                  {{-- ── METADATA REVIEW ── --}}
                  <div>
                    <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Metadata</p>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $rvCss }}">
                      {{ $rvStatus }}
                    </span>
                    @if($reviewItems->isNotEmpty())
                      <div class="flex flex-wrap gap-1 mt-1">
                        @if($acceptField > 0)
                          <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-50 text-emerald-700 font-medium">✅ {{ $acceptField }}</span>
                        @endif
                        @if($revisiField > 0)
                          <span class="px-1.5 py-0.5 rounded text-[9px] bg-amber-50 text-amber-700 font-medium">✏️ {{ $revisiField }}</span>
                        @endif
                        @if($tolakField > 0)
                          <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-50 text-rose-700 font-medium">❌ {{ $tolakField }}</span>
                        @endif
                      </div>
                    @endif
                  </div>

                  {{-- ── EVIDENCE REVIEW ── --}}
                  <div>
                    <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Evidence</p>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $evCss }}">
                      {{ $evStatus }}
                    </span>
                    @if($evItems->isNotEmpty())
                      <div class="flex flex-wrap gap-1 mt-1">
                        @if($evAccept > 0)
                          <span class="px-1.5 py-0.5 rounded text-[9px] bg-emerald-50 text-emerald-700 font-medium">✅ {{ $evAccept }}</span>
                        @endif
                        @if($evRevisi > 0)
                          <span class="px-1.5 py-0.5 rounded text-[9px] bg-amber-50 text-amber-700 font-medium">✏️ {{ $evRevisi }}</span>
                        @endif
                        @if($evTolak > 0)
                          <span class="px-1.5 py-0.5 rounded text-[9px] bg-rose-50 text-rose-700 font-medium">❌ {{ $evTolak }}</span>
                        @endif
                        <span class="px-1.5 py-0.5 rounded text-[9px] bg-gray-100 text-gray-500">
                          {{ $evItems->groupBy('no')->count() }}/20
                        </span>
                      </div>
                    @endif
                  </div>

                  {{-- ── STATUS ASISTENSI ── --}}
                  <div class="border-t border-gray-100 pt-1.5">
                    <p class="text-[9px] text-gray-400 uppercase tracking-wide mb-0.5">Asistensi</p>
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-medium {{ $astCss }}"
                          @if(!empty($inv->asistensi_note)) title="{{ $inv->asistensi_note }}" @endif>
                      {{ $astStatus }}
                    </span>
                    @if(!empty($inv->asistensi_note))
                      <p class="text-[10px] text-gray-400 line-clamp-1 mt-0.5" title="{{ $inv->asistensi_note }}">
                        💬 {{ \Illuminate\Support\Str::limit($inv->asistensi_note, 40) }}
                      </p>
                    @endif
                  </div>

                </div>
                  @endrole

                </td>

          @hasanyrole('admin|verificator_inovasi')
          <td class="px-4 py-3">
            @php
              $reviewItems = $inv->reviewItems ?? collect();

              if ($reviewItems->contains('status', 'tolak')) {
                $status = 'Ditolak';
              } elseif ($reviewItems->contains('status', 'revisi')) {
                $status = 'Revisi';
              } elseif ($reviewItems->isNotEmpty() && $reviewItems->every(fn($r) => $r->status === 'accept')) {
                $status = 'Disetujui';
              } else {
                $status = $inv->asistensi_status ?? 'Menunggu Verifikasi';
              }

              $css = match($status) {
                'Disetujui'              => 'bg-emerald-50 text-emerald-700',
                'Revisi', 'Dikembalikan' => 'bg-amber-50 text-amber-700',
                'Ditolak'                => 'bg-rose-50 text-rose-700',
                default                  => 'bg-gray-100 text-gray-600',
              };
            @endphp

            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $css }}">
              {{ $status }}
            </span>
          </td>
          @endhasanyrole

                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-2">
                    <a href="{{ route('sigap-inovasi.show', $inv->id) }}" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
                    <a href="{{ route('sigap-inovasi.edit', $inv->id) }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</a>
                    <form action="{{ route('sigap-inovasi.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Hapus inovasi ini?')" class="inline">
                      @csrf @method('DELETE')
                      <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Hapus</button>
                    </form>
                    <div class="relative group inline-block">
                      <a href="{{ route('evidence.form', $inv->id) }}">   <button
                        class="px-3 py-1.5 rounded-md border hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-maroon/30"
                        aria-describedby="tt-evidence-1"
                        aria-label="Bukti Evidence"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" d="M3 7a2 2 0 0 1 2-2h4l2 3h8a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>
                        </svg>
                    </button></a>


                       <!-- Tooltip -->
                    <div
                        id="tt-evidence-1"
                        role="tooltip"
                        class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                            whitespace-nowrap rounded-md bg-gray-900 text-white text-xs px-2.5 py-1
                            opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0
                            group-focus-within:opacity-100 group-focus-within:translate-y-0
                            transition duration-150 z-20"
                    >
                        Bukti Evidence
                        <!-- arrow -->
                        <span class="absolute left-1/2 top-full -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></span>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 flex items-center justify-between">
        <div></div>
        <div class="inline-flex">
          {{ $items->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Tambah Inovasi -->
  <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/40" onclick="closeModal()"></div>
    <div class="relative z-10 mx-auto max-w-3xl px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
          <h2 class="text-white text-lg font-bold">Tambah Inovasi</h2>
          <p class="text-white/80 text-xs mt-0.5">Lengkapi metadata inovasi dan unggah dokumen pendukung.</p>
        </div>

        <form id="formTambah" action="{{ route('sigap-inovasi.store') }}" method="POST" enctype="multipart/form-data" class="p-5 grid sm:grid-cols-2 gap-4">
          @csrf

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul Inovasi <span style="color:red">*</span></span>
            <input name="judul" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama inovasi">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">OPD/Unit <span style="color:red">*</span></span>
            <input name="opd_unit" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama OPD/Unit">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tahapan Inovasi <span style="color:red">*</span></span>
            <select name="tahap_inovasi" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih…</option>
              <option>Inisiatif</option>
              <option>Uji Coba</option>
              <option>Penerapan</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Inisiator Inovasi Daerah <span style="color:red">*</span></span>
            <select name="inisiator_daerah" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih…</option>
              <option>Kepala Daerah</option>
              <option>Anggota DPRD</option>
              <option>OPD</option>
              <option>ASN</option>
              <option>Masyrakat</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama Inisiator</span>
            <input name="inisiator_nama" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama inisiator">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Koordinat <span style="color:red">*</span></span>
            <input name="koordinat" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Koordinat">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Klasifikasi Inovasi <span style="color:red">*</span></span>
            <select name="klasifikasi" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Perangkat Daerah</option>
              <option>Inovasi Desa dan Kelurahan</option>
              <option>Inovasi Masyarakat</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Jenis Inovasi <span style="color:red">*</span></span>
            <select name="jenis_inovasi" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option><option>Digital</option><option>Non Digital</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Bentuk Inovasi Daerah <span style="color:red">*</span></span>
            <select name="bentuk_inovasi_daerah" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Daerah lainnya sesuai kewenangan</option>
              <option>Inovasi Pelayanan Publik</option>
              <option>Inovasi Tata Kelolah Pemerintah Daerah</option>
            </select>
          </label>

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Asta Cita <span style="color:red">*</span></span>
          <select name="asta_cipta" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">--</option>
            @foreach(config('inovasi.asta_cipta') as $code => $label)
              <option value="{{ $code }}" @selected(old('asta_cipta')===$code)>{{ $label }}</option>
            @endforeach
          </select>
        </label>

        <label class="block sm:col-span-2">
          <span class="text-sm font-semibold text-gray-700">Program Prioritas Walikota Makassar</span>
          <select name="program_prioritas" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">--</option>
            @foreach(config('inovasi.program_prioritas') as $code => $label)
              <option value="{{ $code }}" @selected(old('program_prioritas')===$code)>{{ $label }}</option>
            @endforeach
          </select>
        </label>
        <label class="block sm:col-span-2">
        <span class="text-sm font-semibold text-gray-700">
          Misi Walikota Makassar <span class="text-rose-600">*</span>
        </span>

        <select
          name="misi_walikota"
          required
          class="mt-1.5 w-full rounded-lg border p-2 border-gray-300
                focus:border-maroon focus:ring-maroon"
        >
          <option value="">— Pilih Misi —</option>
          @foreach(config('inovasi.misi_walikota') as $no => $label)
            <option value="{{ $no }}" @selected(old('misi_walikota')==$no)>
              Misi {{ $no }} — {{ \Illuminate\Support\Str::limit($label, 80) }}
            </option>
          @endforeach
        </select>

        <p class="text-xs text-gray-500 mt-1">
          Pilih misi Walikota yang paling relevan dengan inovasi ini.
        </p>
      </label>


        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Urusan Pemerintah <span style="color:red">*</span></span>
          <select name="urusan_pemerintah" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">--</option>
            @foreach(config('inovasi.urusan_pemerintah') as $code => $label)
              <option value="{{ $code }}" @selected(old('urusan_pemerintah')===$code)>{{ $label }}</option>
            @endforeach
          </select>
        </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Waktu Uji Coba <span style="color:red">*</span></span>
            <input name="waktu_uji_coba" type="date" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Waktu Penerapan <span style="color:red">*</span></span>
            <input name="waktu_penerapan" type="date" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>

          <!-- Tahap (opsional) -->
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Apakah sudah ada perkembangan inovasi tersebut? <span style="color:red">*</span></span>
            <select name="perkembangan_inovasi" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Tidak</option>
              <option>Ya</option>
            </select>
          </label>

          <!-- Richtext (Quill -> textarea) -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Rancang bangun (Min 300 karakter) <span style="color:red">*</span>
            </label>

            <textarea
              name="rancang_bangun"
              rows="8"
              minlength="300"
              required
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Jelaskan rancang bangun inovasi secara rinci..."
            ></textarea>

            <p class="text-xs text-gray-500 mt-1">
              Minimal 300 karakter.
            </p>
          </div>


          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Tujuan inovasi daerah <span style="color:red">*</span>
            </label>

            <textarea
              name="tujuan"
              rows="5"
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Tuliskan tujuan inovasi..."
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
             Manfaat yang diperoleh <span style="color:red">*</span>
            </label>

            <textarea
              name="manfaat"
              rows="5"
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Tuliskan manfaat yang diperoleh..."
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
             Hasil Inovasi <span style="color:red">*</span>
            </label>

            <textarea
              name="hasil_inovasi"
              rows="5"
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Tuliskan hasil inovasi..."
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Penelitian / Inovasi Terdahulu (Minimal 3, Maksimal 5) <span style="color:red">*</span>
              <small><br> Cari di Youtube/Tuxedovation inovasi daerah lain yang memiliki kemiripan atau pada tema yang sama dengan inovasi anda.</small>
            </label>

            <div class="flex flex-wrap gap-2 mb-3">
              <a
                href="https://www.youtube.com/results?search_query=lomba+inovasi+daerah+%22ganti+teks+ini+jadi+nama+inovasi+dan+tema+inovasi+anda%22"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition"
              >
                Cari di YouTube
              </a>

              <a
                href="https://tuxedovation.inovasi.litbang.kemendagri.go.id/"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold hover:bg-gray-50 transition"
              >
                Cari di Tuxedovation
              </a>
            </div>
            <div id="video-wrapper" class="space-y-3">

              @for ($i = 0; $i < 3; $i++)
                <div class="video-item border rounded-lg p-3">
                  <input
                    type="text"
                    name="videos[{{ $i }}][judul]"
                    required
                    placeholder="Judul penelitian / inovasi"
                    class="w-full mb-2 rounded-lg border-black-300"
                  >

                  <textarea
                    name="videos[{{ $i }}][deskripsi]"
                    rows="2"
                    placeholder="Deskripsi singkat"
                    class="w-full mb-2 rounded-lg border-gray-300"
                  ></textarea>

                  <input
                    type="url"
                    name="videos[{{ $i }}][url]"
                    required
                    placeholder="Link video (YouTube / website)"
                    class="w-full rounded-lg border-gray-300"
                  >
                </div>
              @endfor

            </div>

            <div class="flex gap-2 mt-3">
              <button type="button" id="addVideo"
                class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                + Tambah Referensi
              </button>

              <button type="button" id="removeVideo"
                class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                − Hapus
              </button>
            </div>
          </div>

          <!-- Files -->
          <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700 ">Anggaran <span style="color:red">*</span></span>
              <input 
                id="anggaranFile"
                name="anggaran" 
                type="file" 
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" 
                required
              >
              <p class="text-[12px] text-gray-500 mt-1">Pastikan file PDF, maksimal 10 MB</p>
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Profil Bisnis (.ppt) (Jika ada)</span>
              <input name="profil_bisnis" type="file" accept=".ppt,.pptx,.pdf" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Dokumen HAKI</span>
              <input name="haki" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Penghargaan</span>
              <input name="penghargaan" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>
          </div>

          <div class="sm:col-span-2 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Data inovasi dapat diverifikasi dan diperbaharui oleh admin.</span>
            </div>
            <a href="#sop" class="text-maroon hover:underline">Lihat SOP</a>
          </div>

          <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeModal()">Batal</button>
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Asistensi (global, reuse untuk semua baris) --}}
<div id="asst-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" data-asst-dismiss></div>
  <div class="relative z-10 mx-auto max-w-lg p-4 sm:p-6 mt-16">
    <div class="bg-white rounded-2xl shadow-2xl">
      <div class="px-5 py-3 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
        <h3 id="asst-modal-title" class="text-white text-base font-bold">Asistensi</h3>
        <p class="text-white/80 text-xs">Mohon isi catatan untuk status yang dipilih.</p>
      </div>
      <form id="asst-modal-form" method="POST" class="p-5 space-y-3">
        @csrf
        <textarea id="asst-modal-note"
          rows="6"
          maxlength="5000"
          class="w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon text-sm"
          data-template="{{ e($evidenceNoteTemplate) }}"
          placeholder="Tulis catatan… (Wajib)">
        </textarea>

        <div class="flex items-center justify-between text-[11px] text-gray-500">
          <div class="flex flex-wrap gap-2">
            @foreach(['Lengkapi dokumen','Perbaiki deskripsi manfaat','Tidak sesuai kriteria','Butuh data pendukung'] as $chip)
              <button type="button" class="px-2 py-1 rounded-md border hover:bg-gray-50"
                onclick="asstAppendChip('{{ $chip }}')">+ {{ $chip }}</button>
            @endforeach
          </div>
          <span id="asst-modal-count">0/5000</span>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
          <button type="button" class="px-3 py-1.5 rounded-md border hover:bg-gray-50" data-asst-dismiss>Batal</button>
          <button type="submit" class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal Pedoman / Tutorial -->
<div id="pedomanModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
  <!-- Backdrop -->
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

  <div class="relative z-10 mx-auto flex min-h-screen max-w-5xl items-center justify-center px-4 py-8">
    <div class="relative w-full overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-black/5">

      <!-- accent line -->
      <div class="h-1 w-full bg-gradient-to-r from-maroon via-red-500 to-amber-400"></div>

      <!-- Header -->
      <div class="relative overflow-hidden bg-gradient-to-br from-maroon via-maroon-800 to-gray-900 px-6 py-5 sm:px-8">
        <div class="absolute inset-0 opacity-15">
          <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white blur-3xl"></div>
          <div class="absolute -bottom-8 -left-10 h-36 w-36 rounded-full bg-amber-300 blur-3xl"></div>
        </div>

        <div class="relative flex items-start justify-between gap-4">
          <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 ring-1 ring-white/20 backdrop-blur">
              <span class="text-2xl">📘</span>
            </div>

            <div>
              <div class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-[11px] font-semibold tracking-wide text-white/90 ring-1 ring-white/15">
                WAJIB DIBACA
              </div>
              <h2 class="mt-3 text-xl font-extrabold text-white sm:text-2xl">
                Tutorial Pengisian Metadata & Evidence
              </h2>
              <p class="mt-1 max-w-2xl text-sm leading-6 text-white/80">
                Jika ada pertanyaan, buka panduan ini terlebih dahulu agar pengisian lebih rapi, seragam, dan sesuai alur.
              </p>
            </div>
          </div>

          <button
            type="button"
            id="closePedomanModal"
            class="grid h-10 w-10 place-items-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
            aria-label="Tutup modal"
          >
            <span class="text-2xl leading-none">&times;</span>
          </button>
        </div>
      </div>

      <!-- Body -->
      <div class="grid gap-0 md:grid-cols-2">
        <!-- Image side -->
        <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 p-5 sm:p-6">
          <div class="absolute left-5 top-5 rounded-full bg-white px-3 py-1 text-[11px] font-bold text-maroon shadow">
            Tutorial Visual
          </div>

          <div class="flex h-full items-center justify-center">
            <div class="relative w-full overflow-hidden rounded-2xl bg-white p-3 shadow-xl ring-1 ring-gray-200">
              <img
                src="{{ asset('images/tutorial-inovasi.png') }}"
                alt="Pedoman SIGAP Inovasi"
                class="h-auto w-full rounded-xl object-contain"
              >
            </div>
          </div>
        </div>

        <!-- Text side -->
        <div class="flex flex-col justify-between p-6 sm:p-8">
          <div>
            <h3 class="text-lg font-bold text-gray-900 sm:text-xl">
              Baca dulu sebelum mengisi data
            </h3>

            <div class="mt-4 space-y-3 text-sm leading-6 text-gray-700">
              <p class="rounded-2xl bg-gray-50 p-4 ring-1 ring-gray-100">
                Modal ini membantu memahami cara pengisian metadata dan evidence agar data yang diinput lebih lengkap, konsisten, dan mudah diverifikasi.
              </p>

              <p class="rounded-2xl bg-amber-50 p-4 text-amber-900 ring-1 ring-amber-100">
                Jika masih bingung, silakan buka tutorial lengkap melalui tombol di bawah.
              </p>
            </div>
          </div>

          <div class="mt-6 flex flex-wrap gap-3">
            <a
              href="{{ route('evidence.pedoman') }}"
              class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-maroon to-maroon-800 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-maroon/25 transition hover:-translate-y-0.5 hover:shadow-xl hover:shadow-maroon/30"
            >
              <span>📎</span>
              Buka Tutorial Lengkap
            </a>

            <button
              type="button"
              class="rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
              data-pedoman-close
            >
              Tutup
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script>
    // Modal controls
    const modal = document.getElementById('modal');
    document.getElementById('btnTambah')?.addEventListener('click', () => { modal.classList.remove('hidden'); setTimeout(()=>window.dispatchEvent(new Event('resize')),50); });
    function closeModal(){ modal.classList.add('hidden'); }

 
  </script>

<script>
  // status yang butuh catatan
  const NEED_NOTE = new Set(['Dikembalikan','Revisi','Ditolak']);

  const modalEl      = document.getElementById('asst-modal');
  const modalTitleEl = document.getElementById('asst-modal-title');
  const modalFormEl  = document.getElementById('asst-modal-form');
  const noteEl       = document.getElementById('asst-modal-note');
  const countEl      = document.getElementById('asst-modal-count');

  let pendingForm = null; // form baris yang menunggu note
  let pendingHiddenNote = null; // hidden input note di baris itu

  function openAsstModal(title, status){
    modalTitleEl.textContent = title || 'Asistensi';

    const template = noteEl.dataset.template || '';
    const needTemplate = ['Dikembalikan','Revisi','Ditolak'].includes(status);

    noteEl.value = needTemplate ? template : '';
    updateCount();

    modalEl.classList.remove('hidden');
    setTimeout(()=> noteEl.focus(), 30);
  }

  function closeAsstModal(){
    modalEl.classList.add('hidden');
    pendingForm = null;
    pendingHiddenNote = null;
  }
  function updateCount(){ countEl.textContent = `${noteEl.value.length}/5000`; }
  noteEl.addEventListener('input', updateCount);
  document.querySelectorAll('[data-asst-dismiss]').forEach(el=> el.addEventListener('click', closeAsstModal));

  // chips
  window.asstAppendChip = function(text){
    noteEl.value = (noteEl.value ? (noteEl.value.trim() + (noteEl.value.trim().endsWith('.')?'':'.') + ' ') : '') + text;
    noteEl.dispatchEvent(new Event('input'));
    noteEl.focus();
  };

  // submit modal -> salin catatan ke input hidden baris -> submit form baris
  modalFormEl.addEventListener('submit', function(e){
    e.preventDefault();
    const val = noteEl.value.trim();
    if(!val){ alert('Catatan wajib diisi.'); return; }
    if(pendingHiddenNote){ pendingHiddenNote.value = val; }
    if(pendingForm){ pendingForm.submit(); }
    closeAsstModal();
  });

  // intercept semua form asistensi di tabel
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[id^="asst-form-"]').forEach(form => {
      form.addEventListener('submit', function(e){
      const statusSel = form.querySelector('select[name="status"]');
      const statusVal = statusSel?.value || '';

      if (NEED_NOTE.has(statusVal)) {
        e.preventDefault();
        pendingForm = form;
        pendingHiddenNote = form.querySelector('input[name="note"]');

        openAsstModal(form.dataset.modalTitle, statusVal);
      }

        // jika tidak perlu note -> submit langsung (biarkan default)
      });
    });
  });
</script>
<script>
let videoIndex = 3;
const minVideo = 3;
const maxVideo = 5;

const wrapper = document.getElementById('video-wrapper');

document.getElementById('addVideo').addEventListener('click', () => {
  if (videoIndex >= maxVideo) {
    alert('Maksimal 5 referensi video.');
    return;
  }

  const div = document.createElement('div');
  div.className = 'video-item border rounded-lg p-3';
  div.innerHTML = `
    <input type="text" name="videos[${videoIndex}][judul]"
      required placeholder="Judul penelitian / inovasi"
      class="w-full mb-2 rounded-lg border-gray-300">

    <textarea name="videos[${videoIndex}][deskripsi]"
      rows="2" placeholder="Deskripsi singkat"
      class="w-full mb-2 rounded-lg border-gray-300"></textarea>

    <input type="url" name="videos[${videoIndex}][url]"
      required placeholder="Link video"
      class="w-full rounded-lg border-gray-300">
  `;

  wrapper.appendChild(div);
  videoIndex++;
});

document.getElementById('removeVideo').addEventListener('click', () => {
  if (videoIndex <= minVideo) {
    alert('Minimal 3 referensi wajib diisi.');
    return;
  }

  wrapper.lastElementChild.remove();
  videoIndex--;
});
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const pedomanModal = document.getElementById('pedomanModal');
    const closeBtn = document.getElementById('closePedomanModal');

    if (!pedomanModal) return;

    setTimeout(() => {
      pedomanModal.classList.remove('hidden');
    }, 150);

    setTimeout(() => {
      pedomanModal.classList.add('hidden');
    }, 10000);

    closeBtn?.addEventListener('click', () => {
      pedomanModal.classList.add('hidden');
    });

    document.querySelectorAll('[data-pedoman-close]').forEach(el => {
      el.addEventListener('click', () => {
        pedomanModal.classList.add('hidden');
      });
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        pedomanModal.classList.add('hidden');
      }
    });
  });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const anggaranInput = document.getElementById('anggaranFile');

    anggaranInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) return;

        // 10 MB
        const maxSize = 10 * 1024 * 1024;

        if (file.size > maxSize) {

            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar',
                text: 'Ukuran file maksimal 10 MB.',
                confirmButtonColor: '#7a2222'
            });

            // reset input
            this.value = '';
        }
    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('exportInovasiForm');
  if (!form || typeof Swal === 'undefined') return;

  form.addEventListener('submit', async function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Sedang memproses export',
      text: 'File Excel sedang disiapkan. Mohon tunggu.',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const formData = new FormData(form);
      const params = new URLSearchParams();

      for (const pair of formData.entries()) {
        if (pair[1] !== '') params.append(pair[0], pair[1]);
      }

      const response = await fetch(`${form.action}?${params.toString()}`, {
        method: 'GET',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      if (!response.ok) {
        throw new Error('Gagal memproses export.');
      }

      const blob = await response.blob();

      const disposition = response.headers.get('Content-Disposition');
      let filename = 'export-inovasi.xlsx';

      if (disposition && disposition.includes('filename=')) {
        const match = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/i);
        if (match && match[1]) {
          filename = match[1].replace(/['"]/g, '');
        }
      }

      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);

      Swal.close();
      Swal.fire({
        icon: 'success',
        title: 'Export selesai',
        text: 'File Excel berhasil diunduh.'
      });

    } catch (error) {
      Swal.close();
      Swal.fire({
        icon: 'error',
        title: 'Export gagal',
        text: error.message || 'Terjadi kesalahan saat export.'
      });
    }
  });
});
</script>
@endpush
