{{-- resources/views/SigapFormat/preview.blade.php --}}
@extends('layouts.page')

@section('content')

<!-- Breadcrumb -->
<nav class="max-w-7xl mx-auto px-4 mt-6 mb-3 text-sm text-gray-600">
  <ol class="flex items-center gap-2">
    <li><a href="{{ route('sigap-format.index') }}" class="hover:text-maroon">SIGAP FORMAT</a></li>
    <li class="text-gray-400">/</li>
    <li class="text-gray-800 font-semibold truncate" title="{{ $template->title }}">Preview</li>
  </ol>
</nav>

<section class="max-w-7xl mx-auto px-4 pb-12">
  <div class="grid lg:grid-cols-3 gap-6">

    <!-- Viewer -->
    <div class="lg:col-span-2">
      <div class="rounded-xl border border-gray-200 overflow-hidden bg-white">
        <div class="px-4 sm:px-5 py-3 border-b border-gray-100 flex items-center justify-between">
          <div class="min-w-0">
            <h1 class="text-lg sm:text-xl font-extrabold text-gray-900 leading-tight truncate" title="{{ $template->title }}">
              {{ $template->title }}
            </h1>
            <p class="mt-0.5 text-xs text-gray-500">
              {{ $template->category }} • {{ strtoupper($template->file_type) }} • {{ strtoupper($template->lang) }} • {{ ucfirst($template->orientation) }}
            </p>
          </div>
          <div class="hidden sm:flex items-center gap-2">
            <button 
              class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm"
              data-open-download="true"
              data-id="{{ $template->id }}"
              data-title="{{ $template->title }}"
            >Download</button>

            <button id="copy-link" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Salin Link</button>
          </div>
        </div>

        <div class="bg-gray-50 aspect-[16/10] sm:aspect-[16/9]">
          @php
            $type = strtoupper($template->file_type);
            $isImage = in_array($type, ['PNG','JPG','JPEG','SVG','WEBP']);
            $isPdf   = $type === 'PDF';
            $isOffice = in_array($type, ['DOC','DOCX','XLS','XLSX','PPT','PPTX']);
          @endphp

          @if ($isPdf)
            {{-- PDF: tampilkan langsung --}}
            <iframe 
              src="{{ $previewUrl }}" 
              class="w-full h-full"
              title="Preview PDF"
              frameborder="0">
            </iframe>

          @elseif ($isImage)
            {{-- Image: tampilkan img --}}
            <div class="w-full h-full flex items-center justify-center bg-white">
              <img src="{{ $previewUrl }}" alt="Preview Image" class="max-h-full max-w-full object-contain">
            </div>

          @elseif ($isOffice)
            {{-- Office: coba Office Online viewer (butuh URL publik) --}}
            @php
              $encoded = urlencode($previewUrl);
            @endphp
            <iframe 
              src="https://view.officeapps.live.com/op/embed.aspx?src={{ $encoded }}" 
              class="w-full h-full bg-white"
              title="Preview Office"
              frameborder="0">
            </iframe>

            <div class="px-4 py-3 border-t text-xs text-gray-500 bg-white">
              Jika pratinjau tidak tampil, kemungkinan URL berkas tidak publik. 
              Silakan klik <button 
                class="text-maroon underline" 
                data-open-download="true"
                data-id="{{ $template->id }}"
                data-title="{{ $template->title }}"
              >Download</button> untuk mengunduh.
            </div>

          @else
            {{-- Fallback: tipe lain --}}
            <div class="w-full h-full flex items-center justify-center">
              <div class="text-center px-6">
                <div class="mx-auto w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold">{{ $type }}</div>
                <p class="mt-3 text-sm text-gray-600">Pratinjau belum didukung untuk tipe berkas ini.</p>
                <div class="mt-3">
                  <button 
                    class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm"
                    data-open-download="true"
                    data-id="{{ $template->id }}"
                    data-title="{{ $template->title }}"
                  >Download</button>
                </div>
              </div>
            </div>
          @endif
        </div>

        <div class="px-4 sm:px-5 py-4 bg-white">
          <h2 class="text-sm font-semibold text-gray-800">Deskripsi</h2>
          <p class="mt-1 text-sm text-gray-700">{{ $template->description }}</p>

          @if (!empty($template->tags))
            <div class="mt-3 flex flex-wrap gap-2">
              @foreach ($template->tags as $tag)
                <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-200">#{{ $tag }}</span>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>

    <!-- Sidebar -->
    <aside>
      <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="text-base font-bold text-gray-900">Detail Template</h3>
        <dl class="mt-3 space-y-2 text-sm">
          <div class="flex items-start justify-between gap-4">
            <dt class="text-gray-600">Kategori</dt>
            <dd class="text-gray-900 font-medium text-right">{{ $template->category }}</dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="text-gray-600">Tipe</dt>
            <dd class="text-gray-900 font-medium text-right">{{ strtoupper($template->file_type) }}</dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="text-gray-600">Bahasa</dt>
            <dd class="text-gray-900 font-medium text-right">{{ strtoupper($template->lang) }}</dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="text-gray-600">Orientasi</dt>
            <dd class="text-gray-900 font-medium text-right">{{ ucfirst($template->orientation) }}</dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="text-gray-600">Ukuran</dt>
            <dd class="text-gray-900 font-medium text-right">{{ $template->size ?? '-' }}</dd>
          </div>
          <div class="flex items-start justify-between gap-4">
            <dt class="text-gray-600">Diupdate</dt>
            <dd class="text-gray-900 font-medium text-right">
              {{ \Illuminate\Support\Carbon::parse($template->updated_at)->format('d M Y') }}
            </dd>
          </div>
        </dl>

        <div class="mt-5 grid gap-2">
          <button 
            class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm"
            data-open-download="true"
            data-id="{{ $template->id }}"
            data-title="{{ $template->title }}"
          >Download</button>

          <a href="{{ route('sigap-format.index', request()->except('page')) }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm text-center">
            Kembali ke Daftar
          </a>

          <a href="https://wa.me/6285173231604?text=Halo%20Admin%2C%20ada%20masalah%20pada%20template%20{{ urlencode($template->title) }}" 
             target="_blank"
             class="px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 text-sm text-center">
            Laporkan Masalah (WA)
          </a>
        </div>
      </div>

      <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="text-base font-bold text-gray-900">Catatan Penggunaan</h3>
        <ul class="mt-2 text-sm text-gray-700 space-y-2 list-disc list-inside">
          <li>Perbarui nomor & tanggal surat sesuai kebutuhan.</li>
          <li>Sesuaikan nama pejabat/penandatangan.</li>
          <li>Jika mengubah desain kop/stempel, pastikan sesuai pedoman identitas BRIDA.</li>
        </ul>
      </div>
    </aside>
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
      <input type="hidden" name="template_id" id="dl-template-id" value="{{ $template->id }}">
      <div class="mb-3">
        <label class="text-sm font-semibold text-gray-700">Judul Template</label>
        <input id="dl-template-title" type="text" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 bg-gray-50" value="{{ $template->title }}" readonly>
      </div>
      <div class="mb-3">
        <label class="text-sm font-semibold text-gray-700">Tujuan Penggunaan</label>
        <input name="purpose" required placeholder="Contoh: Nota Dinas Kegiatan X" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
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
  // Copy link
  const copyBtn = document.getElementById('copy-link');
  if (copyBtn) {
    copyBtn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(window.location.href);
        copyBtn.textContent = 'Tersalin ✓';
        setTimeout(() => copyBtn.textContent = 'Salin Link', 1500);
      } catch (e) {}
    });
  }

  // Modal Download
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
    form.setAttribute('action', "{{ route('sigap-format.download', '__ID__') }}".replace('__ID__', id));
    modal.classList.remove('hidden');
    modal.classList.add('flex');

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

  document.body.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-open-download="true"]');
    if (btn) {
      const id = btn.getAttribute('data-id');
      const title = btn.getAttribute('data-title') || '{{ $template->title }}';
      openModal(id, title);
    }
  });

  closeBtn.addEventListener('click', closeModal);
  if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (!content.contains(e.target)) closeModal(); });
});
</script>
@endpush
