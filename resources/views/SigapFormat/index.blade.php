{{-- resources/views/SigapFormat/home/index.blade.php --}}
@extends('layouts.page')

@section('content')

<!-- Hero -->
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
                @php
                  $cats = ['Surat','Nota Dinas','Laporan','Memo','SK/Peraturan','Kop Surat','Stempel/TTD'];
                @endphp
                @foreach ($cats as $c)
                  <option value="{{ $c }}" @selected(request('category')===$c)>{{ $c }}</option>
                @endforeach
              </select>
            </label>

            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Tipe File</span>
              <select name="file_type" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="">Semua</option>
                @foreach (['DOCX','XLSX','PPTX','PDF','PNG','SVG'] as $t)
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
              <select name="sort" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                @php $sorts = ['latest'=>'Terbaru','popular'=>'Terpopuler','az'=>'A → Z','za'=>'Z → A']; @endphp
                @foreach ($sorts as $key => $label)
                  <option value="{{ $key }}" @selected(request('sort', 'latest')===$key)>{{ $label }}</option>
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

      <!-- Right: System Card -->
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

            <ul class="mt-6 space-y-4 text-sm">
              <li class="flex gap-3">
                <span class="shrink-0 mt-0.5">🧩</span>
                <p><span class="font-semibold">Siap Pakai:</span> Struktur & gaya konsisten sesuai identitas BRIDA.</p>
              </li>
              <li class="flex gap-3">
                <span class="shrink-0 mt-0.5">🧭</span>
                <p><span class="font-semibold">Cepat Ditemukan:</span> Filter kategori, tipe file, bahasa, orientasi, tahun.</p>
              </li>
              <li class="flex gap-3">
                <span class="shrink-0 mt-0.5">🔐</span>
                <p><span class="font-semibold">Akses Terkendali:</span> Log unduh & pratinjau untuk audit internal.</p>
              </li>
              <li class="flex gap-3">
                <span class="shrink-0 mt-0.5">🛠️</span>
                <p><span class="font-semibold">Format Beragam:</span> DOCX, XLSX, PPTX, PDF, PNG/SVG (stempel/TTD).</p>
              </li>
            </ul>

            <div class="mt-6 grid grid-cols-2 gap-3">
              <a href="#kategori" class="inline-flex items-center justify-center rounded-lg border border-maroon text-maroon px-4 py-2.5 hover:bg-maroon hover:text-white transition">Kategori Populer</a>
              <a href="#cara-pakai" class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-4 py-2.5 hover:bg-maroon-800 transition">Cara Pakai</a>
            </div>

            <!-- mini activity preview -->
            <div class="mt-6 rounded-lg border border-gray-200">
              <div class="px-4 py-2.5 bg-gray-50 text-xs font-semibold text-gray-600">Aktivitas Terkini (contoh)</div>
              <ul class="divide-y text-xs">
                <li class="px-4 py-2 flex items-center justify-between">
                  <span>Unduh: Template Nota Dinas</span><span class="text-gray-500">22 Sep 2025 • 16:10</span>
                </li>
                <li class="px-4 py-2 flex items-center justify-between">
                  <span>Preview: Kop Surat (ID)</span><span class="text-gray-500">22 Sep 2025 • 15:02</span>
                </li>
                <li class="px-4 py-2 flex items-center justify-between">
                  <span>Unggah: Stempel Kepala BRIDA (PNG)</span><span class="text-gray-500">21 Sep 2025 • 09:41</span>
                </li>
              </ul>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Hasil -->
@php
  // Contoh struktur data. Di produksi, ganti dari controller (LengthAwarePaginator).
  $templates = $templates ?? collect([
    (object)[
      'id'=>1,'title'=>'Template Surat Tugas (DOCX)','description'=>'Format surat tugas resmi dengan header BRIDA, tanggal & nomor otomatis.',
      'category'=>'Surat','file_type'=>'DOCX','lang'=>'id','orientation'=>'portrait','size'=>'42 KB','updated_at'=>'2025-09-10',
      'tags'=>['BRIDA','Resmi','Header'],'is_pdf'=>false,'thumbnail'=>null
    ],
    (object)[
      'id'=>2,'title'=>'Nota Dinas - Internal (PDF)','description'=>'Nota dinas siap cetak, struktur paragraf rapi & tanda tangan pejabat.',
      'category'=>'Nota Dinas','file_type'=>'PDF','lang'=>'id','orientation'=>'portrait','size'=>'118 KB','updated_at'=>'2025-08-31',
      'tags'=>['Internal','Siap Cetak'],'is_pdf'=>true,'thumbnail'=>null
    ],
    (object)[
      'id'=>3,'title'=>'Kop Surat BRIDA (PNG & SVG)','description'=>'Kop surat resolusi tinggi untuk integrasi ke Word/Google Docs.',
      'category'=>'Kop Surat','file_type'=>'PNG','lang'=>'id','orientation'=>'landscape','size'=>'256 KB','updated_at'=>'2025-07-26',
      'tags'=>['Logo','Identitas','Visual'],'is_pdf'=>false,'thumbnail'=>null
    ],
    (object)[
      'id'=>4,'title'=>'Stempel Kepala BRIDA (PNG)','description'=>'Stempel transparan, siap overlay, ukuran 1200px.',
      'category'=>'Stempel/TTD','file_type'=>'PNG','lang'=>'id','orientation'=>'landscape','size'=>'350 KB','updated_at'=>'2025-09-12',
      'tags'=>['Stempel','Transparan'],'is_pdf'=>false,'thumbnail'=>null
    ],
  ]);
@endphp

<section class="py-12">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-xl sm:text-2xl font-extrabold text-gray-900">Hasil Template</h3>
      <p class="text-sm text-gray-600">{{ method_exists($templates,'total') ? $templates->total() : $templates->count() }} item</p>
    </div>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse ($templates as $t)
        <div class="p-5 rounded-xl border border-gray-200 hover:shadow-lg transition flex flex-col">
          <div class="flex items-start gap-4">
            <div class="shrink-0 w-12 h-12 rounded-lg bg-maroon/10 flex items-center justify-center text-maroon text-sm font-bold">
              {{ $t->file_type }}
            </div>
            <div class="min-w-0">
              <h4 class="font-semibold text-gray-900 line-clamp-2">{{ $t->title }}</h4>
              <p class="mt-1 text-xs text-gray-500">{{ $t->category }} • {{ strtoupper($t->lang) }} • {{ ucfirst($t->orientation) }}</p>
            </div>
          </div>

          <p class="mt-3 text-sm text-gray-700 line-clamp-3">{{ $t->description }}</p>

          @if (!empty($t->tags))
            <div class="mt-3 flex flex-wrap gap-2">
              @foreach ($t->tags as $tag)
                <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-200">#{{ $tag }}</span>
              @endforeach
            </div>
          @endif

          <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
            <span>Ukuran: {{ $t->size }}</span>
            <span>Diupdate: {{ \Illuminate\Support\Carbon::parse($t->updated_at)->format('d M Y') }}</span>
          </div>

          <div class="mt-4 grid grid-cols-3 gap-2">
            @if ($t->is_pdf)
              <a href="{{ route('sigap-format.preview', $t->id) }}" class="px-3 py-2 text-center rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Preview</a>
            @else
              <a href="{{ route('sigap-format.show', $t->id) }}" class="px-3 py-2 text-center rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Detail</a>
            @endif

            <button 
              class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm"
              data-open-download="true"
              data-id="{{ $t->id }}"
              data-title="{{ $t->title }}"
            >Download</button>

            <a href="{{ route('sigap-format.show', $t->id) }}" class="px-3 py-2 text-center rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Info</a>
          </div>
        </div>
      @empty
        <div class="col-span-full">
          <div class="p-6 rounded-xl border-2 border-dashed border-gray-300 text-center text-gray-600">
            Tidak ditemukan template sesuai filter. Coba ubah kata kunci/penyaring.
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

<!-- Kategori Populer -->
<section id="kategori" class="py-12 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center max-w-2xl mx-auto">
      <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Kategori Populer</h3>
      <p class="mt-3 text-gray-600">Langsung pilih kategori yang paling sering digunakan pegawai.</p>
    </div>

    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach (['Surat','Nota Dinas','Laporan','Memo','SK/Peraturan','Kop Surat','Stempel/TTD'] as $kc)
        <a href="{{ route('sigap-format.index', array_merge(request()->except('page'), ['category'=>$kc])) }}" class="p-5 rounded-xl border border-gray-200 hover:shadow-lg transition flex items-center justify-between">
          <span class="font-semibold text-gray-900">{{ $kc }}</span>
          <span class="text-maroon">→</span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<!-- Cara Pakai -->
<section id="cara-pakai" class="py-12">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid lg:grid-cols-3 gap-8">
      <div>
        <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">Cara Pakai Cepat</h3>
        <p class="mt-3 text-gray-600">Cukup 4 langkah untuk pakai template standar di unit kerjamu.</p>
      </div>
      <ol class="lg:col-span-2 grid sm:grid-cols-2 gap-6">
        <li class="p-5 bg-white rounded-xl border border-gray-200">
          <p class="text-sm font-semibold text-maroon">1) Pilih Template</p>
          <p class="mt-1 text-sm text-gray-600">Telusuri berdasarkan kategori/jenis file & pratinjau jika perlu.</p>
        </li>
        <li class="p-5 bg-white rounded-xl border border-gray-200">
          <p class="text-sm font-semibold text-maroon">2) Unduh</p>
          <p class="mt-1 text-sm text-gray-600">Isi tujuan penggunaan pada dialog download (untuk audit).</p>
        </li>
        <li class="p-5 bg-white rounded-xl border border-gray-200">
          <p class="text-sm font-semibold text-maroon">3) Sesuaikan</p>
          <p class="mt-1 text-sm text-gray-600">Edit nama, nomor surat, tanggal, pejabat, dan isi sesuai kebutuhan.</p>
        </li>
        <li class="p-5 bg-white rounded-xl border border-gray-200">
          <p class="text-sm font-semibold text-maroon">4) Simpan Arsip</p>
          <p class="mt-1 text-sm text-gray-600">Unggah balik versi final ke SIGAP Dokumen agar menjadi rujukan fix.</p>
        </li>
      </ol>
    </div>
  </div>
</section>

<!-- Modal Download (Konfirmasi & Alasan) -->
<div id="download-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div id="download-content" class="bg-white max-w-md w-full mx-4 rounded-xl p-6 shadow-xl relative animate-fade-in">
    <button id="dl-close" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
    <h3 class="text-lg font-bold text-maroon">Unduh Template</h3>
    <p class="mt-1 text-sm text-gray-700">Mohon isi tujuan penggunaan untuk keperluan <em>log</em> & audit internal.</p>

    <form id="download-form" class="mt-4" method="POST" action="#">
      @csrf
      <input type="hidden" name="template_id" id="dl-template-id">
      <div class="mb-3">
        <label class="text-sm font-semibold text-gray-700">Judul Template</label>
        <input id="dl-template-title" type="text" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 bg-gray-50" readonly>
      </div>
      <div class="mb-3">
        <label class="text-sm font-semibold text-gray-700">Tujuan Penggunaan</label>
        <input name="purpose" required placeholder="Contoh: Surat Tugas Kegiatan X" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
      </div>
      <div class="grid sm:grid-cols-2 gap-3">
        <div>
          <label class="text-sm font-semibold text-gray-700">Unit/OPD</label>
          <input name="unit" placeholder="Contoh: Sekretariat" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Nama Peminta</label>
          <input name="requester" placeholder="Nama lengkap" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>
      </div>

      <p class="mt-3 text-[12px] text-gray-500">Dengan menekan “Unduh Sekarang”, Anda menyetujui penggunaan sesuai ketentuan internal BRIDA & pencatatan log akses.</p>

      <div class="mt-4 flex items-center gap-3">
        <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Unduh Sekarang</button>
        <button type="button" id="dl-cancel" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
      </div>
    </form>

    <p id="dl-timer" class="mt-3 text-xs text-gray-500 hidden">Dialog ini akan tertutup otomatis dalam <span id="dl-count">15</span> detik…</p>
  </div>
</div>

<style>
  @keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
  .animate-fade-in { animation: fade-in 0.35s ease-out; }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // --- Modal Download ---
  const modal = document.getElementById('download-modal');
  const content = document.getElementById('download-content');
  const closeBtn = document.getElementById('dl-close');
  const cancelBtn = document.getElementById('dl-cancel');
  const form = document.getElementById('download-form');
  const inputId = document.getElementById('dl-template-id');
  const inputTitle = document.getElementById('dl-template-title');
  const timerText = document.getElementById('dl-timer');
  const countSpan = document.getElementById('dl-count');
  let timer = null, counter = 15;

  function openModal(id, title) {
    inputId.value = id;
    inputTitle.value = title;
    // Set action route dinamis:
    form.setAttribute('action', "{{ route('sigap-format.download', '__ID__') }}".replace('__ID__', id));
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Auto close 15 detik
    counter = 15;
    countSpan.textContent = counter;
    timerText.classList.remove('hidden');
    timer = setInterval(() => {
      counter--;
      countSpan.textContent = counter;
      if (counter <= 0) closeModal();
    }, 1000);
  }
  function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    if (timer) { clearInterval(timer); timer = null; }
  }

  // Delegasi tombol "Download"
  document.body.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-open-download="true"]');
    if (btn) {
      const id = btn.getAttribute('data-id');
      const title = btn.getAttribute('data-title') || 'Template';
      openModal(id, title);
    }
  });

  // Tutup manual
  closeBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  // Klik di luar konten = tutup
  modal.addEventListener('click', (e) => {
    if (!content.contains(e.target)) closeModal();
  });
});
</script>
@endpush
