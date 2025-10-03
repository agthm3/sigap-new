@extends('layouts.page')

@section('content')

<!-- Breadcrumb -->
<nav class="max-w-7xl mx-auto px-4 mt-6 mb-3 text-sm text-gray-600">
  <ol class="flex items-center gap-2">
    <li><a href="{{ route('sigap-format.index') }}" class="hover:text-maroon">SIGAP FORMAT</a></li>
    <li class="text-gray-400">/</li>
    <li class="text-gray-800 font-semibold truncate" title="{{ $template->title }}">Detail</li>
  </ol>
</nav>

<section class="max-w-7xl mx-auto px-4 pb-12">
  <div class="grid lg:grid-cols-3 gap-6">

    <!-- Main Detail + Preview -->
    <div class="lg:col-span-2">
      <div class="rounded-xl border border-gray-200 overflow-hidden bg-white">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-100">
          <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $template->title }}</h1>
          <p class="mt-1 text-sm text-gray-500">
            {{ $template->category }} • {{ strtoupper($template->file_type) }} • {{ strtoupper($template->lang) }} • {{ ucfirst($template->orientation) }}
          </p>
        </div>

        <!-- Preview -->
        <div class="bg-gray-50 aspect-[16/10] sm:aspect-[16/9]">
          @php
            $type = strtoupper($template->file_type);
            $isImage  = in_array($type, ['PNG','JPG','JPEG','SVG','WEBP']);
            $isPdf    = $type === 'PDF';
            $isOffice = in_array($type, ['DOC','DOCX','XLS','XLSX','PPT','PPTX']);
          @endphp

          @if ($isPdf)
            <iframe src="{{ $previewUrl }}" class="w-full h-full" frameborder="0"></iframe>
          @elseif ($isImage)
            <div class="w-full h-full flex items-center justify-center bg-white">
              <img src="{{ $previewUrl }}" alt="Preview Image" class="max-h-full max-w-full object-contain">
            </div>
          @elseif ($isOffice)
            @php $encoded = urlencode($previewUrl); @endphp
            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ $encoded }}" class="w-full h-full bg-white" frameborder="0"></iframe>
            <div class="px-4 py-2 text-xs text-gray-500 bg-white">
              Jika pratinjau tidak tampil, kemungkinan file bukan dalam format PDF atau tidak publik. Silakan unduh langsung.
            </div>
          @else
            <div class="w-full h-full flex items-center justify-center">
              <div class="text-center px-6">
                <div class="mx-auto w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold">{{ $type }}</div>
                <p class="mt-3 text-sm text-gray-600">Pratinjau tidak tersedia untuk tipe file ini.</p>
              </div>
            </div>
          @endif
        </div>

        <!-- Deskripsi -->
        <div class="px-6 py-4">
          <h2 class="text-sm font-semibold text-gray-800">Deskripsi</h2>
          <p class="mt-1 text-sm text-gray-700">{{ $template->description ?? 'Belum ada deskripsi.' }}</p>

          @if (!empty($template->tags))
            <div class="mt-3 flex flex-wrap gap-2">
              @foreach ($template->tags as $tag)
                <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700 border">#{{ $tag }}</span>
              @endforeach
            </div>
          @endif

          <div class="mt-6 flex flex-wrap gap-3">
            <button 
              class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm"
              data-open-download="true"
              data-id="{{ $template->id }}"
              data-title="{{ $template->title }}"
            >Download</button>

            <a href="{{ route('sigap-format.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
              Kembali ke Daftar
            </a>
          </div>
        </div>
      </div>

      <!-- Catatan Penggunaan -->
      <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="text-base font-bold text-gray-900">Catatan Penggunaan</h2>
        <ul class="mt-2 text-sm text-gray-700 list-disc list-inside space-y-2">
          <li>Nomor & tanggal surat wajib disesuaikan.</li>
          <li>Nama pejabat/penandatangan perlu diganti sesuai kebutuhan.</li>
          <li>Gunakan template ini untuk kepentingan resmi BRIDA.</li>
        </ul>
      </div>
    </div>

    <!-- Sidebar -->
    <aside>
      <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h3 class="text-base font-bold text-gray-900">Detail Informasi</h3>
        <dl class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between"><dt class="text-gray-600">Kategori</dt><dd class="font-medium">{{ $template->category }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Tipe File</dt><dd class="font-medium">{{ strtoupper($template->file_type) }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Ukuran</dt><dd class="font-medium">{{ $template->size ?? '-' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Orientasi</dt><dd class="font-medium">{{ ucfirst($template->orientation) }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Bahasa</dt><dd class="font-medium">{{ strtoupper($template->lang) }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Diupdate</dt><dd class="font-medium">{{ \Carbon\Carbon::parse($template->updated_at)->format('d M Y') }}</dd></div>
        </dl>
      </div>
    </aside>
  </div>
</section>

<!-- Modal Download -->
<div id="download-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div id="download-content" class="bg-white max-w-md w-full mx-4 rounded-xl p-6 shadow-xl relative animate-fade-in">
    <button id="dl-close" class="absolute top-3 right-3 text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
    <h3 class="text-lg font-bold text-maroon">Unduh Template</h3>
    <form id="download-form" class="mt-4" method="POST" action="{{ route('sigap-format.download', $template->id) }}">
      @csrf
      <input type="hidden" name="template_id" value="{{ $template->id }}">
      <div class="mb-3">
        <label class="text-sm font-semibold text-gray-700">Tujuan Penggunaan</label>
        <input name="purpose" required placeholder="Contoh: Nota Dinas Kegiatan X" class="mt-1.5 w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
      </div>
      <div class="mt-4 flex gap-3">
        <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Unduh Sekarang</button>
        <button type="button" id="dl-cancel" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
      </div>
    </form>
  </div>
</div>

<style>
  @keyframes fade-in { from { opacity: 0; transform: translateY(10px);} to { opacity: 1; transform: translateY(0);} }
  .animate-fade-in { animation: fade-in 0.35s ease-out; }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('download-modal');
  const content = document.getElementById('download-content');
  const closeBtn = document.getElementById('dl-close');
  const cancelBtn = document.getElementById('dl-cancel');

  function openModal() { modal.classList.remove('hidden'); modal.classList.add('flex'); }
  function closeModal() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

  document.body.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-open-download="true"]');
    if (btn) { openModal(); }
  });
  closeBtn.addEventListener('click', closeModal);
  cancelBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (!content.contains(e.target)) closeModal(); });
});
</script>
@endpush
