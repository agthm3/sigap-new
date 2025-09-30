@extends('layouts.app')

@section('content')
<section class="space-y-6">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
    <div>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-maroon">Katalog Template</h1>
      <p class="text-sm text-gray-600">Kelola template surat, nota dinas, kop, stempel/TTD, dlsb.</p>
    </div>
    @role('admin')
      <button id="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 5v14M5 12h14"/></svg>
        Tambah Template
      </button>
    @endrole
  </div>

  <!-- Toolbar Filter -->
  <div class="rounded-2xl border border-gray-200 bg-white p-4">
    <form method="GET" action="{{ route('format.index') }}" class="grid sm:grid-cols-5 gap-3">
      <div class="sm:col-span-2">
        <label class="text-xs font-semibold text-gray-600">Cari</label>
        <div class="relative mt-1">
          <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Contoh: Surat Tugas, Kop Surat…"
                 class="w-full rounded-lg border border-gray-300 pe-9 p-2 focus:border-maroon focus:ring-maroon">
          <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
        </div>
      </div>
      <div>
        <label class="text-xs font-semibold text-gray-600">Kategori</label>
        <select name="category" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
          @php $cat=$filters['category']??''; @endphp
          <option value="">Semua</option>
          @foreach(['Surat','Nota Dinas','Laporan','Kop Surat','Stempel/TTD', 'kentut Test'] as $o)
            <option value="{{ $o }}" @selected($cat===$o)>{{ $o }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-gray-600">Tipe</label>
        <select name="file_type" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
          @php $ft=$filters['file_type']??''; @endphp
          <option value="">Semua</option>
          @foreach(['DOCX','PDF','PNG','PPTX','XLSX','SVG'] as $o)
            <option value="{{ $o }}" @selected($ft===$o)>{{ $o }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-gray-600">Privasi</label>
        <select name="privacy" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
          @php $pv=$filters['privacy']??''; @endphp
          <option value="">Semua</option>
          <option value="public" @selected($pv==='public')>Publik</option>
          <option value="private" @selected($pv==='private')>Privasi</option>
        </select>
      </div>

      <div class="sm:col-span-5 grid sm:grid-cols-4 gap-3">
        <div>
          <label class="text-xs font-semibold text-gray-600">Bahasa</label>
          @php $lg=$filters['lang']??''; @endphp
          <select name="lang" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
            <option value="">Semua</option>
            <option value="id" @selected($lg==='id')>Indonesia</option>
            <option value="en" @selected($lg==='en')>English</option>
          </select>
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">Orientasi</label>
          @php $or=$filters['orientation']??''; @endphp
          <select name="orientation" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
            <option value="">Semua</option>
            <option value="portrait" @selected($or==='portrait')>Portrait</option>
            <option value="landscape" @selected($or==='landscape')>Landscape</option>
          </select>
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">Urutkan</label>
          @php $sort=$filters['sort']??'latest'; @endphp
          <select name="sort" class="mt-1 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
            <option value="latest" @selected($sort==='latest')>Terbaru</option>
            <option value="az" @selected($sort==='az')>A → Z</option>
            <option value="za" @selected($sort==='za')>Z → A</option>
          </select>
        </div>
        <div class="flex items-end gap-2 mt-1">
          <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Terapkan</button>
          <a href="{{ route('format.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</a>
        </div>
      </div>
    </form>
  </div>

  <!-- Tabel -->
  <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white">
    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
      <p class="text-sm text-gray-600">
        Menampilkan {{ $templates->count() }} dari {{ $templates->total() }} item
      </p>
      <div class="text-sm text-gray-500">Hal. {{ $templates->currentPage() }} / {{ $templates->lastPage() }}</div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
          <tr>
            <th class="text-left font-semibold px-4 py-3">Template</th>
            <th class="text-left font-semibold px-4 py-3">Kategori</th>
            <th class="text-left font-semibold px-4 py-3">Tipe</th>
            <th class="text-left font-semibold px-4 py-3">Bahasa</th>
            <th class="text-left font-semibold px-4 py-3">Orientasi</th>
            <th class="text-left font-semibold px-4 py-3">Privasi</th>
            <th class="text-left font-semibold px-4 py-3">Diupdate</th>
            <th class="text-right font-semibold px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse($templates as $t)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <div class="font-semibold text-gray-900">{{ $t->title }}</div>
                <div class="text-xs text-gray-500 line-clamp-1">{{ $t->description }}</div>
              </td>
              <td class="px-4 py-3">{{ $t->category }}</td>
              <td class="px-4 py-3">{{ $t->file_type }}</td>
              <td class="px-4 py-3 uppercase">{{ $t->lang }}</td>
              <td class="px-4 py-3 capitalize">{{ $t->orientation }}</td>
              <td class="px-4 py-3">
                @if($t->privacy==='public')
                  <span class="text-[11px] px-2 py-1 rounded-full bg-green-50 text-green-700 border border-green-200">Publik</span>
                @else
                  <span class="text-[11px] px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Privasi</span>
                @endif
              </td>
              <td class="px-4 py-3">{{ $t->updated_at?->format('d M Y') }}</td>
            <td class="px-4 py-3">
            <div class="flex items-center gap-2 justify-end">
                @role('admin')
                <a href="{{ route('format.edit', $t->id) }}"
                    class="px-2.5 py-1.5 rounded-md border border-gray-300 hover:bg-gray-50">Edit</a>
                <form action="{{ route('format.destroy', $t->id) }}" method="POST"
                        onsubmit="return confirm('Yakin hapus template ini?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-2.5 py-1.5 rounded-md border border-red-200 text-red-700 hover:bg-red-50">
                    Hapus
                    </button>
                </form>
                @endrole

                @php
                $isPrivate = $t->privacy === 'private';
                $downloadUrl = route('format.download', $t->id);
                $unlockUrl   = route('format.unlock',   $t->id);
                @endphp

                @if($isPrivate && !auth()->user()->hasRole('admin'))
                {{-- Non-admin + private → trigger modal kode --}}
                <button
                    class="px-2.5 py-1.5 rounded-md bg-maroon text-white hover:bg-maroon-800 btn-download-private"
                    data-id="{{ $t->id }}"
                    data-unlock="{{ $unlockUrl }}"
                    data-title="{{ $t->title }}">
                    Download
                </button>
                @else
                {{-- Admin atau publik → langsung download --}}
                <a href="{{ $downloadUrl }}"
                    class="px-2.5 py-1.5 rounded-md bg-maroon text-white hover:bg-maroon-800">
                    Download
                </a>
                @endif
            </div>
            </td>

            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 border-t border-gray-100">
      {{ $templates->links() }}
    </div>
  </div>
</section>

@role('admin')
  <!-- Modal: Tambah Template -->
  <div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div id="createContent" class="bg-white w-full max-w-2xl mx-4 rounded-2xl shadow-xl relative animate-fade-in">
      <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-gray-900">Tambah Template Baru</h3>
        <button id="closeCreateModal" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
      </div>

      <form class="p-5 sm:p-6" method="POST" action="{{ route('format.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul Template</span>
            <input name="title" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon" placeholder="Contoh: Surat Tugas Kegiatan A">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kategori</span>
            <select name="category" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="">- Pilih -</option>
              @foreach(['Surat','Nota Dinas','Laporan','Kop Surat','Stempel/TTD','kentut Test'] as $o)
                <option value="{{ $o }}">{{ $o }}</option>
              @endforeach
            </select>
          </label>

          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Deskripsi</span>
            <textarea name="description" rows="3" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon" placeholder="Jelaskan kegunaan singkat template ini…"></textarea>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Bahasa</span>
            <select name="lang" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="id">Indonesia</option>
              <option value="en">English</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Orientasi</span>
            <select name="orientation" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="portrait">Portrait</option>
              <option value="landscape">Landscape</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tipe File</span>
            <select name="file_type" required class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon">
              <option value="">- Pilih -</option>
              @foreach(['DOCX','PDF','PNG','PPTX','XLSX','SVG'] as $o)
                <option value="{{ $o }}">{{ $o }}</option>
              @endforeach
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Upload File</span>
            <input name="file" type="file" required class="mt-1.5 block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:bg-maroon file:text-white hover:file:bg-maroon-800" />
            <span class="text-xs text-gray-500">Maks. 10MB.</span>
          </label>

          <!-- Privasi + Kode -->
          <div class="sm:col-span-2 grid sm:grid-cols-3 gap-4 items-start">
            <div class="sm:col-span-1">
              <span class="text-sm font-semibold text-gray-700">Privasi</span>
              <div class="mt-2 flex items-center gap-3">
                <label class="inline-flex items-center gap-2">
                  <input type="radio" name="privacy" value="public" class="text-maroon" checked>
                  <span class="text-sm">Publik</span>
                </label>
                <label class="inline-flex items-center gap-2">
                  <input type="radio" name="privacy" value="private" class="text-maroon">
                  <span class="text-sm">Privasi</span>
                </label>
              </div>
              <p class="mt-1 text-xs text-gray-500">Publik: semua orang bisa akses & unduh. Privasi: butuh kode akses.</p>
            </div>

            <div class="sm:col-span-2">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Kode Akses</span>
                <div class="mt-1.5 relative">
                  <input id="accessCodeInput" name="access_code"
                         class="w-full rounded-lg border border-gray-300 p-2 pr-24 focus:border-maroon"
                         placeholder="Buat kode akses (min. 6 karakter)" disabled>
                  <button type="button" id="toggleAccessCode"
                          class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs rounded-md border border-gray-300 hover:bg-gray-50"
                          disabled>Tampilkan</button>
                </div>
              </label>
            </div>
          </div>

          <!-- Tags -->
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Tag (opsional)</span>
            <input name="tags" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2 focus:border-maroon" placeholder="Pisahkan dengan koma, contoh: BRIDA, Resmi">
          </label>
        </div>

        <div class="mt-5 flex items-center justify-end gap-2">
          <button type="button" id="cancelCreateModal" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
        </div>
      </form>
    </div>
  </div>
@endrole

<style>
  @keyframes fade-in { from { opacity:0; transform: translateY(8px);} to { opacity:1; transform: translateY(0);} }
  .animate-fade-in { animation: fade-in .25s ease-out; }
</style>
{{-- Modal: Kode Akses untuk Download Private (non-admin) --}}
<div id="accessModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
  <div id="accessContent" class="bg-white w-full max-w-md mx-4 rounded-2xl shadow-xl relative animate-fade-in">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <h3 class="text-lg font-bold text-gray-900">Masukkan Kode Akses</h3>
      <button id="closeAccessModal" class="text-gray-400 hover:text-red-500 text-xl font-bold">&times;</button>
    </div>

    <form id="accessForm" method="POST" action="#" class="p-5 sm:p-6">
      @csrf
      <p id="accessDocTitle" class="text-sm text-gray-600 mb-3"></p>
      <label class="block">
        <span class="text-sm font-semibold text-gray-700">Kode Akses</span>
        <div class="mt-1.5 relative">
          <input name="access_code" id="accessCodeField" required minlength="3"
                 class="w-full rounded-lg border border-gray-300 p-2 pr-24 focus:border-maroon"
                 placeholder="Masukkan kode akses">
          <button type="button" id="toggleAccessField"
                  class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 text-xs rounded-md border border-gray-300 hover:bg-gray-50">
            Tampilkan
          </button>
        </div>
      </label>

      <div class="mt-5 flex items-center justify-end gap-2">
        <button type="button" id="cancelAccessModal" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
        <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Download</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  // Modal open/close (admin only)
  const openBtn = document.getElementById('openCreateModal');
  const modal   = document.getElementById('createModal');
  const content = document.getElementById('createContent');
  const closeBtn= document.getElementById('closeCreateModal');
  const cancel  = document.getElementById('cancelCreateModal');

  function openModal(){ modal?.classList.remove('hidden'); modal?.classList.add('flex'); }
  function closeModal(){ modal?.classList.add('hidden'); modal?.classList.remove('flex'); }

  openBtn?.addEventListener('click', openModal);
  closeBtn?.addEventListener('click', closeModal);
  cancel?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e)=>{ if(content && !content.contains(e.target)) closeModal(); });

  // Privacy radio → enable/disable access code
  const radios = document.querySelectorAll('input[name="privacy"]');
  const accessInput = document.getElementById('accessCodeInput');
  const toggleBtn   = document.getElementById('toggleAccessCode');

  function syncPrivacy(){
    const isPrivate = [...radios].find(r=>r.checked)?.value === 'private';
    if (accessInput) accessInput.disabled = !isPrivate;
    if (toggleBtn)   toggleBtn.disabled   = !isPrivate;
    if(!isPrivate && accessInput){
      accessInput.type='password'; accessInput.value=''; if (toggleBtn) toggleBtn.textContent='Tampilkan';
    }
  }
  radios.forEach(r=> r.addEventListener('change', syncPrivacy));
  syncPrivacy();

  toggleBtn?.addEventListener('click', ()=>{
    if(accessInput?.disabled) return;
    const visible = accessInput.type === 'text';
    accessInput.type = visible ? 'password' : 'text';
    toggleBtn.textContent = visible ? 'Tampilkan' : 'Sembunyikan';
  });
</script>
@endpush
@push('scripts')
<script>
// ====== Access Code Modal (non-admin + private) ======
const accessModal   = document.getElementById('accessModal');
const accessContent = document.getElementById('accessContent');
const accessForm    = document.getElementById('accessForm');
const closeAccess   = document.getElementById('closeAccessModal');
const cancelAccess  = document.getElementById('cancelAccessModal');
const codeField     = document.getElementById('accessCodeField');
const toggleField   = document.getElementById('toggleAccessField');
const docTitleEl    = document.getElementById('accessDocTitle');

function openAccessModal(actionUrl, docTitle){
  accessForm.setAttribute('action', actionUrl);
  docTitleEl.textContent = 'Dokumen: ' + (docTitle || '');
  codeField.type = 'password';
  codeField.value = '';
  toggleField.textContent = 'Tampilkan';
  accessModal.classList.remove('hidden');
  accessModal.classList.add('flex');
  setTimeout(()=> codeField.focus(), 100);
}
function closeAccessModal(){
  accessModal.classList.add('hidden');
  accessModal.classList.remove('flex');
}

document.querySelectorAll('.btn-download-private').forEach(btn=>{
  btn.addEventListener('click', ()=>{
    const url = btn.getAttribute('data-unlock');
    const title = btn.getAttribute('data-title');
    openAccessModal(url, title);
  });
});

closeAccess?.addEventListener('click', closeAccessModal);
cancelAccess?.addEventListener('click', closeAccessModal);
accessModal?.addEventListener('click', (e)=>{ if(accessContent && !accessContent.contains(e.target)) closeAccessModal(); });

// show/hide
toggleField?.addEventListener('click', ()=>{
  const visible = codeField.type === 'text';
  codeField.type = visible ? 'password' : 'text';
  toggleField.textContent = visible ? 'Tampilkan' : 'Sembunyikan';
});

// ====== SweetAlert error dari server (kode salah) ======
@if(session('format_error_msg'))
  Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: @json(session('format_error_msg')),
    confirmButtonColor: '#7a2222'
  });
@endif
</script>
@endpush

@endsection
