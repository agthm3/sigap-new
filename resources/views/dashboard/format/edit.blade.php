@extends('layouts.app')

@section('content')
<section class="space-y-6">

  <!-- Breadcrumb + Header -->
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <nav class="text-xs text-gray-500 mb-1">
        <a href="{{ route('format.index') }}" class="hover:text-maroon">Katalog Template</a>
        <span class="mx-1">/</span>
        <span class="text-gray-700 font-medium">Edit</span>
      </nav>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-maroon">Edit Template</h1>
      <p class="text-sm text-gray-600">Perbarui metadata, privasi, atau ganti file.</p>
    </div>

    <div class="flex items-center gap-2">
      <form method="POST" action="{{ route('format.destroy', $template->id) }}"
            onsubmit="return confirm('Yakin hapus template ini? Tindakan tidak bisa dibatalkan.');">
        @csrf @method('DELETE')
        <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-red-700 hover:bg-red-50">
          Hapus
        </button>
      </form>
    </div>
  </div>

  <div class="grid lg:grid-cols-3 gap-6">
    <!-- Form Utama -->
    <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white">
      <form class="p-5 sm:p-6" method="POST" action="{{ route('format.update', $template->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul Template</span>
            <input name="title" required
                   value="{{ old('title', $template->title) }}"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kategori</span>
            @php $cat = old('category', $template->category); @endphp
            <select name="category" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="">- Pilih -</option>
              @foreach (['Surat','Nota Dinas','Laporan','Kop Surat','Stempel/TTD'] as $opt)
                <option value="{{ $opt }}" {{ $cat === $opt ? 'selected' : '' }}>{{ $opt }}</option>
              @endforeach
            </select>
          </label>

          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Deskripsi</span>
            <textarea name="description" rows="3"
                      class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">{{ old('description', $template->description) }}</textarea>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Bahasa</span>
            @php $lang = old('lang', $template->lang); @endphp
            <select name="lang" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="id" {{ $lang==='id' ? 'selected' : '' }}>Indonesia</option>
              <option value="en" {{ $lang==='en' ? 'selected' : '' }}>English</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Orientasi</span>
            @php $ori = old('orientation', $template->orientation); @endphp
            <select name="orientation" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="portrait" {{ $ori==='portrait' ? 'selected' : '' }}>Portrait</option>
              <option value="landscape" {{ $ori==='landscape' ? 'selected' : '' }}>Landscape</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tipe File</span>
            @php $ft = old('file_type', $template->file_type); @endphp
            <select name="file_type" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="">- Pilih -</option>
              @foreach (['DOCX','PDF','PNG','PPTX','XLSX','SVG'] as $opt)
                <option value="{{ $opt }}" {{ $ft===$opt ? 'selected' : '' }}>{{ $opt }}</option>
              @endforeach
            </select>
          </label>

          <!-- Ganti File (opsional) -->
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Ganti File (opsional)</span>
            <input name="file" type="file"
                   class="mt-1.5 block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-maroon file:text-white hover:file:bg-maroon-800" />
            <span class="text-xs text-gray-500">Kosongkan jika tidak ingin mengganti file. Maks. 10MB.</span>
          </label>
        </div>

        <!-- Privasi -->
        @php $privacy = old('privacy', $template->privacy); @endphp
        <div class="mt-5 grid sm:grid-cols-3 gap-4 items-start">
          <div class="sm:col-span-1">
            <span class="text-sm font-semibold text-gray-700">Privasi</span>
            <div class="mt-2 flex items-center gap-3">
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="privacy" value="public" class="text-maroon" {{ $privacy==='public' ? 'checked' : '' }}>
                <span class="text-sm">Publik</span>
              </label>
              <label class="inline-flex items-center gap-2">
                <input type="radio" name="privacy" value="private" class="text-maroon" {{ $privacy==='private' ? 'checked' : '' }}>
                <span class="text-sm">Privasi</span>
              </label>
            </div>
            <p class="mt-1 text-xs text-gray-500">Publik: semua orang bisa akses & unduh. Privasi: butuh kode akses.</p>
          </div>

          <!-- Kode Akses -->
          <div class="sm:col-span-2">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Kode Akses</span>
              <div class="mt-1.5 flex gap-2">
                <div class="relative flex-1">
                  <input id="accessCodeInput" name="access_code"
                         class="w-full rounded-lg border border-gray-300 p-2 pr-24 focus:border-maroon"
                         placeholder="Kosongkan untuk tetap pakai kode lama"
                         value=""
                         {{ $privacy==='private' ? '' : 'disabled' }}>
                  <button type="button" id="toggleAccessCode"
                          class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs rounded-md border border-gray-300 hover:bg-gray-50"
                          {{ $privacy==='private' ? '' : 'disabled' }}>
                    Tampilkan
                  </button>
                </div>
                <button type="button" id="genAccessCode"
                        class="px-3 py-2 text-xs rounded-md border border-gray-300 hover:bg-gray-50"
                        {{ $privacy==='private' ? '' : 'disabled' }}>
                  Generate
                </button>
              </div>
              <p class="mt-1 text-xs text-gray-500">
                Demi keamanan, kode lama tidak ditampilkan. Isi untuk mengganti, atau biarkan kosong untuk mempertahankan kode lama.
              </p>
            </label>
          </div>
        </div>

        <!-- Tag -->
        <div class="mt-5">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tag (opsional)</span>
            <input name="tags"
                   value="{{ old('tags', isset($template->tags)?(is_array($template->tags)?implode(', ', $template->tags):$template->tags):'') }}"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon"
                   placeholder="Pisahkan dengan koma, contoh: BRIDA, Resmi">
          </label>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex items-center justify-end gap-2">
          <a href="{{ route('format.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</a>
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan Perubahan</button>
        </div>
      </form>
    </div>

    <!-- Sidebar Info -->
    <aside class="space-y-6">
      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h3 class="text-base font-bold text-gray-900">Info File Saat Ini</h3>
        <dl class="mt-3 text-sm space-y-2">
          <div class="flex justify-between"><dt class="text-gray-600">Nama File</dt><dd class="font-medium text-right truncate">{{ $template->original_name }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Tipe</dt><dd class="font-medium">{{ strtoupper($template->file_type) }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Ukuran</dt><dd class="font-medium">{{ number_format(($template->size ?? 0)/1024,1) }} KB</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Privasi</dt><dd class="font-medium">{{ $template->privacy==='private'?'Privasi':'Publik' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Diunggah</dt><dd class="font-medium">{{ $template->created_at?->format('d M Y') }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Diupdate</dt><dd class="font-medium">{{ $template->updated_at?->format('d M Y') }}</dd></div>
        </dl>
        <div class="mt-4">
          <a href="{{ route('format.download', $template->id) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
            Download
          </a>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-200 bg-white p-5">
        <h3 class="text-base font-bold text-gray-900">Riwayat Singkat</h3>
        <ul class="mt-3 text-sm text-gray-700 space-y-2">
          <li>{{ $template->updated_at?->format('d M Y') }} — Diperbarui</li>
          <li>{{ $template->created_at?->format('d M Y') }} — Dibuat</li>
        </ul>
      </div>
    </aside>
  </div>
</section>

@push('scripts')
<script>
  // Privacy radio → enable/disable access code controls
  const radios = document.querySelectorAll('input[name="privacy"]');
  const accessInput = document.getElementById('accessCodeInput');
  const toggleBtn   = document.getElementById('toggleAccessCode');
  const genBtn      = document.getElementById('genAccessCode');

  function syncPrivacy(){
    const val = [...radios].find(r=>r.checked)?.value;
    const isPrivate = val === 'private';
    [accessInput, toggleBtn, genBtn].forEach(el=>{
      if(!el) return;
      el.disabled = !isPrivate;
    });
    if(!isPrivate && accessInput){
      accessInput.type='password';
      accessInput.value='';
      if (toggleBtn) toggleBtn.textContent='Tampilkan';
    }
  }
  radios.forEach(r=> r.addEventListener('change', syncPrivacy));
  syncPrivacy();

  // Show/Hide access code
  toggleBtn?.addEventListener('click', ()=>{
    if(accessInput?.disabled) return;
    const visible = accessInput.type === 'text';
    accessInput.type = visible ? 'password' : 'text';
    toggleBtn.textContent = visible ? 'Tampilkan' : 'Sembunyikan';
  });

  // Generate dummy code (front-end only; pada server tetap validasi & hash)
  genBtn?.addEventListener('click', ()=>{
    if(accessInput?.disabled) return;
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    let out = '';
    for(let i=0;i<8;i++){ out += chars[Math.floor(Math.random()*chars.length)]; }
    accessInput.value = out;
    accessInput.type = 'text';
    if (toggleBtn) toggleBtn.textContent = 'Sembunyikan';
  });
</script>
@endpush
@endsection
