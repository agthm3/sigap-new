@extends('layouts.app')

@section('content')
<section class="flex items-center justify-between gap-3 mb-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Edit <span class="text-maroon">Riset</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">Perbarui metadata dan berkas riset.</p>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('riset.index') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">Kembali</a>
  </div>
</section>

@if ($errors->any())
  <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 p-3 text-sm mb-4">
    <ul class="list-disc list-inside">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form id="formEdit" action="{{ route('riset.update', $r->id) }}" method="POST" enctype="multipart/form-data">
  @csrf
  @method('PUT')

  <section class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
      {{-- Informasi Utama --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Informasi Utama</h3>
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Judul</label>
            <input name="title" type="text" required value="{{ old('title',$r->title) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Tahun</label>
            <select name="year" required
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              @for($y = now()->year; $y >= now()->year-15; $y--)
                <option value="{{ $y }}" @selected(old('year',$r->year)==$y)>{{ $y }}</option>
              @endfor
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Jenis</label>
            <select name="type"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <option value="">—</option>
              <option value="internal"   @selected(old('type',$r->type)=='internal')>Riset Internal</option>
              <option value="kolaborasi" @selected(old('type',$r->type)=='kolaborasi')>Kolaborasi/OPD</option>
              <option value="eksternal"  @selected(old('type',$r->type)=='eksternal')>Eksternal Terkait</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Abstrak</label>
            <textarea name="abstract" rows="5" required
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">{{ old('abstract',$r->abstract) }}</textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Metode (opsional)</label>
            <input name="method" type="text" value="{{ old('method',$r->method) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>
        </div>
      </div>

      {{-- Penulis --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5" id="authorsBox">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-maroon">Penulis</h3>
          <button type="button" class="text-sm px-2 py-1 rounded border border-gray-300 hover:bg-gray-50"
            onclick="addAuthor()">+ Tambah</button>
        </div>

        <div id="authorsList" class="mt-3 space-y-3">
          @php $authors = old('authors', $r->authors ?? []); @endphp
          @foreach($authors as $i => $a)
            <div class="grid sm:grid-cols-12 gap-3 border-t pt-3">
              <div class="sm:col-span-4">
                <label class="text-xs font-semibold text-gray-700">Nama</label>
                <input name="authors[{{ $i }}][name]" type="text" value="{{ $a['name'] ?? '' }}" required
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-4">
                <label class="text-xs font-semibold text-gray-700">Afiliasi</label>
                <input name="authors[{{ $i }}][affiliation]" type="text" value="{{ $a['affiliation'] ?? '' }}"
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-gray-700">Peran</label>
                <input name="authors[{{ $i }}][role]" type="text" value="{{ $a['role'] ?? '' }}"
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-gray-700">ORCID</label>
                <input name="authors[{{ $i }}][orcid]" type="text" value="{{ $a['orcid'] ?? '' }}"
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-12">
                <button type="button" class="text-xs text-rose-600 hover:underline" onclick="removeBlock(this)" {{ count($authors)<=1 ? 'disabled' : '' }}>Hapus penulis</button>
              </div>
            </div>
          @endforeach
          @if(empty($authors))
            <div class="grid sm:grid-cols-12 gap-3 border-t pt-3">
              <div class="sm:col-span-4">
                <label class="text-xs font-semibold text-gray-700">Nama</label>
                <input name="authors[0][name]" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-4">
                <label class="text-xs font-semibold text-gray-700">Afiliasi</label>
                <input name="authors[0][affiliation]" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-gray-700">Peran</label>
                <input name="authors[0][role]" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
              <div class="sm:col-span-2">
                <label class="text-xs font-semibold text-gray-700">ORCID</label>
                <input name="authors[0][orcid]" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              </div>
            </div>
          @endif
        </div>

        <div class="mt-4 grid sm:grid-cols-3 gap-4 border-t pt-4">
          @php $corr = old('corresponding', $r->corresponding ?? []); @endphp
          <div class="sm:col-span-3">
            <h4 class="text-xs font-semibold text-gray-700">Kontak Korespondensi</h4>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Nama</label>
            <input name="corresponding[name]" type="text" value="{{ $corr['name'] ?? '' }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Email</label>
            <input name="corresponding[email]" type="email" value="{{ $corr['email'] ?? '' }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Telepon</label>
            <input name="corresponding[phone]" type="text" value="{{ $corr['phone'] ?? '' }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
        </div>
      </div>

      {{-- Dokumen & Lampiran --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Dokumen & Lampiran</h3>

        <div class="mt-4 grid sm:grid-cols-12 gap-4 items-start">
          <div class="sm:col-span-8">
            <label class="text-xs font-semibold text-gray-700">Ganti File Utama (PDF) — opsional</label>
            <div class="mt-1.5 border-2 border-dashed rounded-lg p-4 text-sm">
              <input name="pdf_file" type="file" accept="application/pdf"
                 class="block w-full text-sm text-gray-700 file:mr-2 file:px-3 file:py-1.5 file:rounded-md file:border-0 file:bg-maroon file:text-white">
              <p class="mt-2 text-xs text-gray-500">Saat ini: {{ $r->file_name }} • {{ $r->file_size }}</p>
            </div>
          </div>
          <div class="sm:col-span-4">
            <label class="text-xs font-semibold text-gray-700">Ganti Thumbnail (opsional)</label>
            <div class="mt-1.5 border rounded-lg p-3">
              <input name="thumbnail" type="file" accept="image/*" class="block w-full text-sm text-gray-700">
              @if($r->thumbnail_path)
                <img src="{{ Storage::disk('public')->url($r->thumbnail_path) }}" class="mt-2 w-20 h-20 object-cover rounded border">
              @endif
            </div>
          </div>
        </div>

        {{-- datasets lama --}}
        @php $datasets = old('datasets_existing', $r->datasets ?? []); @endphp
        <div class="mt-6">
          <h4 class="text-xs font-semibold text-gray-700">Dataset/Lampiran yang Ada</h4>
          <div id="datasetsExisting" class="mt-2 space-y-2">
            @foreach($datasets as $i => $d)
              <div class="grid sm:grid-cols-12 gap-2 items-center border rounded p-2">
                <div class="sm:col-span-6">
                  <input name="datasets_existing[{{ $i }}][label]" type="text"
                         value="{{ $d['label'] ?? ($d['original_name'] ?? 'Dataset') }}"
                         class="w-full rounded-lg border p-2 border-gray-300">
                </div>
                <div class="sm:col-span-5 text-xs text-gray-600">
                  {{ $d['original_name'] ?? '—' }} {{ !empty($d['size']) ? '• '.$d['size'] : '' }}
                </div>
                <div class="sm:col-span-1 text-right">
                  <label class="inline-flex items-center gap-1 text-xs">
                    <input type="checkbox" name="datasets_delete[]" value="{{ $i }}"> Hapus
                  </label>
                </div>
              </div>
            @endforeach
            @if(empty($datasets))
              <p class="text-xs text-gray-500">Belum ada dataset.</p>
            @endif
          </div>

          {{-- tambah baru --}}
          <div class="mt-4">
            <div class="flex items-center justify-between">
              <h4 class="text-xs font-semibold text-gray-700">Tambah Dataset/Lampiran Baru</h4>
              <button class="text-sm px-2 py-1 rounded border border-gray-300 hover:bg-gray-50" type="button" onclick="addDataset()">+ Tambah</button>
            </div>
            <div id="datasetsNew" class="mt-2 space-y-2"></div>
          </div>
        </div>
      </div>

      {{-- Metadata tambahan --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Metadata Tambahan</h3>
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
          {{-- tags --}}
          <div>
            <label class="text-xs font-semibold text-gray-700">Tag</label>
            <div class="mt-1.5 flex gap-2">
              <input id="tagInput" type="text" placeholder="Ketik lalu Enter"
                class="flex-1 rounded-lg border p-2 border-gray-300" onkeydown="if(event.key==='Enter'){event.preventDefault(); addTag()}">
              <button type="button" class="px-3 py-2 rounded-lg border border-gray-300 text-sm" onclick="addTag()">Tambah</button>
            </div>
            <div id="tagsWrap" class="mt-2 flex flex-wrap gap-2">
              @foreach(old('tags',$r->tags ?? []) as $i => $t)
                <span class="chip">
                  <input type="hidden" name="tags[{{ $i }}]" value="{{ $t }}">
                  <span>{{ $t }}</span>
                  <button type="button" onclick="this.parentElement.remove()">✕</button>
                </span>
              @endforeach
            </div>
          </div>

          {{-- stakeholders --}}
          <div>
            <label class="text-xs font-semibold text-gray-700">Pihak Terkait</label>
            <div class="mt-1.5 flex gap-2">
              <input id="stakeInput" type="text" placeholder="PDAM..." class="flex-1 rounded-lg border p-2 border-gray-300"
                onkeydown="if(event.key==='Enter'){event.preventDefault(); addStake()}">
              <button type="button" class="px-3 py-2 rounded-lg border border-gray-300 text-sm" onclick="addStake()">Tambah</button>
            </div>
            <div id="stakeWrap" class="mt-2 flex flex-wrap gap-2">
              @foreach(old('stakeholders',$r->stakeholders ?? []) as $i => $s)
                <span class="chip">
                  <input type="hidden" name="stakeholders[{{ $i }}]" value="{{ $s }}">
                  <span>{{ $s }}</span>
                  <button type="button" onclick="this.parentElement.remove()">✕</button>
                </span>
              @endforeach
            </div>
          </div>

          <div>
            <label class="text-xs font-semibold text-gray-700">DOI</label>
            <input name="doi" type="text" value="{{ old('doi',$r->doi) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">URL OJS</label>
            <input name="ojs_url" type="url" value="{{ old('ojs_url',$r->ojs_url) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Pendanaan</label>
            <input name="funding" type="text" value="{{ old('funding',$r->funding) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Etika</label>
            <input name="ethics" type="text" value="{{ old('ethics',$r->ethics) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
        </div>
      </div>

      {{-- Versi --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Versi</h3>
        <div class="mt-4 grid sm:grid-cols-3 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-700">Nomor Versi</label>
            <input name="version" type="text" value="{{ old('version',$r->version) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Catatan Rilis</label>
            <input name="release_note" type="text" value="{{ old('release_note',$r->release_note) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT --}}
    <div class="space-y-6">
      {{-- Akses & Lisensi --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Akses & Lisensi</h3>
        <div class="mt-3 space-y-3">
          <div>
            <label class="text-xs font-semibold text-gray-700">Status Akses</label>
            <div class="mt-1.5 flex gap-3">
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="radio" name="access" value="Public" {{ old('access',$r->access)=='Public'?'checked':'' }}> Publik
              </label>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="radio" name="access" value="Restricted" {{ old('access',$r->access)=='Restricted'?'checked':'' }}> Restricted
              </label>
            </div>
          </div>

          <div id="accessReasonWrap" style="{{ old('access',$r->access)=='Restricted' ? '' : 'display:none' }}">
            <label class="text-xs font-semibold text-gray-700">Alasan/ketentuan akses</label>
            <textarea name="access_reason" rows="3"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">{{ old('access_reason',$r->access_reason) }}</textarea>
          </div>

          <div>
            <label class="text-xs font-semibold text-gray-700">Lisensi</label>
            <select name="license" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
              <option value="">—</option>
              @foreach(['CC BY 4.0','CC BY-SA 4.0','CC BY-NC 4.0','Hak Cipta BRIDA'] as $lic)
                <option value="{{ $lic }}" @selected(old('license',$r->license)==$lic)>{{ $lic }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>

      {{-- Actions --}}
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <div class="flex gap-2">
          <button type="submit" class="px-3 py-2 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">Simpan Perubahan</button>
          <a href="{{ route('riset.index') }}" class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">Batal</a>
        </div>
      </div>
    </div>
  </section>
</form>

@push('scripts')
<script>
  // akses reason toggle
  document.querySelectorAll('input[name="access"]').forEach(r=>{
    r.addEventListener('change',()=>{
      document.getElementById('accessReasonWrap').style.display = (r.value==='Restricted')?'block':'none';
    });
  });

  // chip style
  const chip = (name, value) => {
    const span = document.createElement('span');
    span.className = 'chip inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100 border text-xs';
    span.innerHTML = `<input type="hidden" name="${name}" value="${value}"><span>${value}</span><button type="button" onclick="this.parentElement.remove()">✕</button>`;
    return span;
  };

  // TAGS
  function addTag(){
    const input = document.getElementById('tagInput');
    const v = (input.value||'').trim();
    if(!v) return;
    const wrap = document.getElementById('tagsWrap');
    const idx = wrap.querySelectorAll('input[type=hidden]').length;
    wrap.appendChild(chip(`tags[${idx}]`, v));
    input.value='';
  }

  // STAKEHOLDERS
  function addStake(){
    const input = document.getElementById('stakeInput');
    const v = (input.value||'').trim();
    if(!v) return;
    const wrap = document.getElementById('stakeWrap');
    const idx = wrap.querySelectorAll('input[type=hidden]').length;
    wrap.appendChild(chip(`stakeholders[${idx}]`, v));
    input.value='';
  }

  // AUTHORS
  function addAuthor(){
    const list = document.getElementById('authorsList');
    const idx = list.querySelectorAll('.grid.sm\\:grid-cols-12').length;
    const html = `
      <div class="grid sm:grid-cols-12 gap-3 border-t pt-3">
        <div class="sm:col-span-4">
          <label class="text-xs font-semibold text-gray-700">Nama</label>
          <input name="authors[${idx}][name]" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
        </div>
        <div class="sm:col-span-4">
          <label class="text-xs font-semibold text-gray-700">Afiliasi</label>
          <input name="authors[${idx}][affiliation]" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
        </div>
        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-700">Peran</label>
          <input name="authors[${idx}][role]" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
        </div>
        <div class="sm:col-span-2">
          <label class="text-xs font-semibold text-gray-700">ORCID</label>
          <input name="authors[${idx}][orcid]" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
        </div>
        <div class="sm:col-span-12">
          <button type="button" class="text-xs text-rose-600 hover:underline" onclick="removeBlock(this)">Hapus penulis</button>
        </div>
      </div>`;
    list.insertAdjacentHTML('beforeend', html);
  }
  function removeBlock(btn){
    const block = btn.closest('.grid');
    block.remove();
    // NOTE: kita biarkan index "bolong"; Laravel akan tetap menerima sesuai name yang ada
  }

  // DATASETS NEW
  function addDataset(){
    const wrap = document.getElementById('datasetsNew');
    const idx = wrap.querySelectorAll('.dataset-new').length;
    const node = document.createElement('div');
    node.className = 'dataset-new grid sm:grid-cols-12 gap-2 items-center border rounded p-2';
    node.innerHTML = `
      <div class="sm:col-span-5"><input name="datasets_new[${idx}][label]" type="text" placeholder="Nama lampiran/data"
        class="w-full rounded-lg border p-2 border-gray-300"></div>
      <div class="sm:col-span-6"><input name="datasets_new[${idx}][file]" type="file"
        class="w-full rounded-lg border p-2 border-gray-300 bg-white"></div>
      <div class="sm:col-span-1 text-right"><button type="button" class="text-xs text-rose-600 hover:underline" onclick="this.closest('.dataset-new').remove()">Hapus</button></div>
    `;
    wrap.appendChild(node);
  }
</script>
<style>
  .chip { display:inline-flex; align-items:center; gap:.25rem; padding:.25rem .5rem; border-radius:.5rem; border:1px solid #e5e7eb; background:#f3f4f6; font-size:.75rem }
  .chip button { margin-left:.25rem }
</style>
@endpush
@endsection
