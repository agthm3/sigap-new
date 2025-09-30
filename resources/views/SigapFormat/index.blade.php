{{-- resources/views/SigapFormat/index.blade.php --}}
@extends('layouts.page')

@section('content')

<!-- Hero / Search -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16 lg:py-20">
    <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-stretch">
      
      <!-- Left: Search Panel -->
      <div class="bg-white/95 rounded-2xl shadow-xl p-5 sm:p-6 md:p-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-maroon tracking-tight">
          SIGAP FORMAT — Template Resmi & Siap Pakai
        </h1>
        <p class="mt-2 text-sm sm:text-base text-gray-600">
          Kumpulan <strong>template dokumen standar</strong> BRIDA: surat, nota dinas, laporan, memo, hingga <strong>stempel/TTD digital</strong>. Tinggal unduh & gunakan.
        </p>

        <form class="mt-6 grid grid-cols-1 gap-4" action="{{ route('sigap-format.index') }}" method="GET">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kata Kunci</span>
            <div class="mt-1.5 relative">
              <input type="search" name="q" value="{{ request('q') }}"
                placeholder="Contoh: Surat Tugas, Nota Dinas, Kop Surat…"
                class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pe-10" />
              <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400">🔎</span>
            </div>
          </label>

          <div class="grid sm:grid-cols-3 gap-3">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Kategori</span>
              <select name="category" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">Semua</option>
                @php $cats = ['Surat','Nota Dinas','Laporan','Memo','SK/Peraturan','Kop Surat','Stempel/TTD']; @endphp
                @foreach ($cats as $c)
                  <option value="{{ $c }}" @selected(request('category')===$c)>{{ $c }}</option>
                @endforeach
              </select>
            </label>

            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Tipe File</span>
              <select name="file_type" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">Semua</option>
                @foreach (['DOCX','XLSX','PPTX','PDF','PNG','SVG','JPG'] as $t)
                  <option value="{{ $t }}" @selected(request('file_type')===$t)>{{ $t }}</option>
                @endforeach
              </select>
            </label>

            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Orientasi</span>
              <select name="orientation" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">Semua</option>
                <option value="portrait" @selected(request('orientation')==='portrait')>Portrait</option>
                <option value="landscape" @selected(request('orientation')==='landscape')>Landscape</option>
              </select>
            </label>
          </div>

          <div class="grid sm:grid-cols-3 gap-3">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Bahasa</span>
              <select name="lang" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">Semua</option>
                <option value="id" @selected(request('lang')==='id')>Indonesia</option>
                <option value="en" @selected(request('lang')==='en')>English</option>
              </select>
            </label>

            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Tahun</span>
              <select name="year" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">Semua</option>
                @for($y = now()->year; $y >= now()->year-6; $y--)
                  <option value="{{ $y }}" @selected(request('year')==$y)>{{ $y }}</option>
                @endfor
              </select>
            </label>

            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Urutkan</span>
              @php $sorts = ['latest'=>'Terbaru','az'=>'A → Z','za'=>'Z → A']; @endphp
              <select name="sort" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                @foreach ($sorts as $key => $label)
                  <option value="{{ $key }}" @selected(request('sort','latest')===$key)>{{ $label }}</option>
                @endforeach
              </select>
            </label>
          </div>

          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">Cari Template</button>
            <a href="{{ route('sigap-format.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</a>
          </div>
        </form>
      </div>

      <!-- Right: Info Card -->
      <div class="relative">
        <div class="h-full rounded-2xl bg-white/10 border border-white/20 backdrop-blur p-1">
          <div class="h-full rounded-xl bg-white shadow-2xl p-6 sm:p-8 flex flex-col">
            <div class="flex items-center gap-3">
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-maroon text-white font-bold">SB</span>
              <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-maroon leading-tight">SIGAP FORMAT</h2>
                <p class="text-xs text-gray-500 -mt-0.5">Template Dokumen Resmi</p>
              </div>
            </div>

            <ul class="mt-6 space-y-3 text-sm">
              <li class="flex gap-3"><span>🧩</span><p><span class="font-semibold">Siap Pakai:</span> Konsisten sesuai identitas BRIDA.</p></li>
              <li class="flex gap-3"><span>🧭</span><p><span class="font-semibold">Mudah Dicari:</span> Filter kategori, tipe, bahasa, orientasi, tahun.</p></li>
              <li class="flex gap-3"><span>🔐</span><p><span class="font-semibold">Akses Terkendali:</span> Private butuh kode akses.</p></li>
              <li class="flex gap-3"><span>🛠️</span><p><span class="font-semibold">Format Beragam:</span> DOCX, XLSX, PPTX, PDF, PNG, SVG.</p></li>
            </ul>

            <div class="mt-6 rounded-lg border border-gray-200">
              <div class="px-4 py-2.5 bg-gray-50 text-xs font-semibold text-gray-600">Aktivitas Terkini (contoh)</div>
              <ul class="divide-y text-xs">
                <li class="px-4 py-2 flex items-center justify-between"><span>Unduh: Nota Dinas</span><span class="text-gray-500">22 Sep 2025 • 16:10</span></li>
                <li class="px-4 py-2 flex items-center justify-between"><span>Preview: Kop Surat (ID)</span><span class="text-gray-500">22 Sep 2025 • 15:02</span></li>
                <li class="px-4 py-2 flex items-center justify-between"><span>Unggah: Stempel (PNG)</span><span class="text-gray-500">21 Sep 2025 • 09:41</span></li>
              </ul>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</section>

@php
  // helper thumbnail — gunakan Storage dengan prefix global namespace
  function sigap_thumb($t) {
    $type = strtoupper($t->file_type);
    $isImage = in_array($type, ['PNG','JPG','JPEG','SVG','WEBP']);
    if ($isImage && !empty($t->file_path ?? null)) {
      try {
        return \Storage::disk('public')->url($t->file_path);
      } catch (\Throwable $e) {}
    }
    // private non-image → dummy locked; selainnya → dummy doc
    if (($t->privacy ?? 'public') === 'private') {
      return 'https://picsum.photos/seed/locked'.$t->id.'/600/360';
    }
    return 'https://picsum.photos/seed/doc'.$t->id.'/600/360';
  }
@endphp

<!-- Hasil -->
<section class="py-12">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl sm:text-2xl font-extrabold text-gray-900">Hasil Template</h3>
      <p class="text-sm text-gray-600">
        {{ method_exists($templates,'total') ? $templates->total() : $templates->count() }} item
      </p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse ($templates as $t)
        @php
          $type = strtoupper($t->file_type);
          $thumb = sigap_thumb($t);
          $isPrivate = ($t->privacy ?? 'public') === 'private';
          $detailUrl = route('sigap-format.show', $t->id);
          $previewUrl = route('sigap-format.preview', $t->id);
          $downloadUrl = route('sigap-format.download', $t->id);
          $unlockUrl = route('sigap-format.unlock', $t->id);
        @endphp

        <div class="group rounded-xl border border-gray-200 overflow-hidden bg-white hover:shadow-lg transition flex flex-col">
          <div class="aspect-[16/9] bg-gray-100 relative">
            <img src="{{ $thumb }}" alt="thumbnail" class="w-full h-full object-cover">
            @if($isPrivate)
              <div class="absolute inset-0 bg-black/30 flex items-center justify-center">
                <span class="inline-flex items-center gap-2 text-white text-xs font-semibold bg-black/40 px-3 py-1.5 rounded">
                  🔐 Private
                </span>
              </div>
            @endif
          </div>

          <div class="p-5 flex-1 flex flex-col">
            <div class="flex items-start gap-3">
              <div class="shrink-0 w-10 h-10 rounded-lg bg-maroon/10 text-maroon text-xs font-bold flex items-center justify-center">
                {{ $type }}
              </div>
              <div class="min-w-0">
                <h4 class="font-semibold text-gray-900 line-clamp-2">{{ $t->title }}</h4>
                <p class="mt-0.5 text-xs text-gray-500">
                  {{ $t->category }} • {{ strtoupper($t->lang) }} • {{ ucfirst($t->orientation) }}
                </p>
              </div>
            </div>

            <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ $t->description }}</p>

            <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
              <span>Ukuran: {{ number_format((($t->size ?? 0)/1024),1) }} KB</span>
              <span>Diupdate: {{ \Illuminate\Support\Carbon::parse($t->updated_at)->format('d M Y') }}</span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2">
              {{-- Aksi kiri: Preview untuk PDF, Detail untuk selain PDF --}}
              @if($type === 'PDF')
                @if($isPrivate)
                  <button
                    class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm btn-unlock"
                    data-action="detail"  {{-- intent: show --}}
                    data-title="{{ $t->title }}"
                    data-unlock="{{ $unlockUrl }}">
                    Preview
                  </button>
                @else
                  <a href="{{ $previewUrl }}" class="px-3 py-2 text-center rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Preview</a>
                @endif
              @else
                @if($isPrivate)
                  <button
                    class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm btn-unlock"
                    data-action="detail"  {{-- intent: show --}}
                    data-title="{{ $t->title }}"
                    data-unlock="{{ $unlockUrl }}">
                    Detail
                  </button>
                @else
                  <a href="{{ $detailUrl }}" class="px-3 py-2 text-center rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Detail</a>
                @endif
              @endif

              {{-- Aksi kanan: Download --}}
              @if($isPrivate)
                <button
                  class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm btn-unlock"
                  data-action="download" {{-- intent: download --}}
                  data-title="{{ $t->title }}"
                  data-unlock="{{ $unlockUrl }}">
                  Download
                </button>
              @else
                <form method="POST" action="{{ $downloadUrl }}">
                  @csrf
                  <button class="w-full px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Download</button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="col-span-full">
          <div class="p-6 rounded-xl border-2 border-dashed border-gray-300 text-center text-gray-600">
            Tidak ditemukan template sesuai filter.
          </div>
        </div>
      @endforelse
    </div>

    @if (method_exists($templates,'links'))
      <div class="mt-8">
        {{ $templates->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
</section>

<!-- Modal: Kode Akses -->
<div id="unlockModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div id="unlockContent" class="bg-white w-full max-w-md mx-4 rounded-2xl shadow-xl relative animate-fade-in">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="text-lg font-bold text-gray-900">Masukkan Kode Akses</h3>
      <button id="unlockClose" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
    </div>

    <form id="unlockForm" class="p-5" method="POST" action="#">
      @csrf
      {{-- penting: intent dipakai controller (show|download) --}}
      <input type="hidden" name="intent" id="unlockIntent" value="show">
      <p id="unlockDocTitle" class="text-sm text-gray-600 mb-3"></p>

      <label class="block">
        <span class="text-sm font-semibold text-gray-700">Kode Akses</span>
        <div class="mt-1.5 relative">
          <input name="access_code" id="unlockCode"
                 class="w-full rounded-lg border border-gray-300 p-2 pr-24 focus:border-maroon"
                 placeholder="Masukkan kode akses" type="password" required>
          <button type="button" id="unlockToggle"
                  class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs rounded-md border border-gray-300 hover:bg-gray-50">
            Tampilkan
          </button>
        </div>
      </label>

      <div class="mt-5 flex items-center justify-end gap-2">
        <button type="button" id="unlockCancel" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Lanjut</button>
      </div>
    </form>
  </div>
</div>

<style>
  @keyframes fade-in { from { opacity:.0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
  .animate-fade-in { animation: fade-in .25s ease-out; }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', ()=>{
  // ===== Unlock Modal =====
  const m = document.getElementById('unlockModal');
  const c = document.getElementById('unlockContent');
  const f = document.getElementById('unlockForm');
  const closeBtn = document.getElementById('unlockClose');
  const cancelBtn= document.getElementById('unlockCancel');
  const code     = document.getElementById('unlockCode');
  const toggle   = document.getElementById('unlockToggle');
  const titleEl  = document.getElementById('unlockDocTitle');
  const intentEl = document.getElementById('unlockIntent');

  function openUnlock(action, title, url){
    f.setAttribute('action', url);
    // mapping: 'detail' -> 'show', 'download' -> 'download'
    const intent = (action === 'download') ? 'download' : 'show';
    intentEl.value = intent;

    code.type = 'password'; code.value = '';
    toggle.textContent = 'Tampilkan';
    titleEl.textContent = 'Dokumen: ' + (title || '');
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(()=>code.focus(), 80);
  }
  function closeUnlock(){ m.classList.add('hidden'); m.classList.remove('flex'); }

  document.body.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-unlock');
    if(!btn) return;
    const action = btn.getAttribute('data-action');
    const title  = btn.getAttribute('data-title');
    const url    = btn.getAttribute('data-unlock');
    openUnlock(action, title, url);
  });

  // toggle show/hide
  toggle.addEventListener('click', ()=>{
    const visible = code.type === 'text';
    code.type = visible ? 'password' : 'text';
    toggle.textContent = visible ? 'Tampilkan' : 'Sembunyikan';
  });

  closeBtn.addEventListener('click', closeUnlock);
  cancelBtn.addEventListener('click', closeUnlock);
  m.addEventListener('click', (e)=>{ if(!c.contains(e.target)) closeUnlock(); });

  // SweetAlert error (dari session)
  @if(session('format_error_msg'))
    Swal.fire({ icon:'error', title:'Gagal', text:@json(session('format_error_msg')), confirmButtonColor:'#7a2222' });
  @endif

  // Auto open modal jika diarahkan dengan ?need={id}
  @if(request('need'))
    const needId = @json((int)request('need'));
    const unlockUrl = @json(route('sigap-format.unlock', 0)).replace('/0/unlock','/'+needId+'/unlock');
    openUnlock('detail','', unlockUrl);
  @endif
});
</script>
@endpush
