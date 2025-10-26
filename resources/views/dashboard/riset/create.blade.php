{{-- resources/views/dashboard/riset/create.blade.php --}}
@extends('layouts.app')

@section('content')
<form id="risetForm" action="{{ route('riset.store') }}" method="POST" enctype="multipart/form-data">
  @csrf

  <section class="flex items-center justify-between gap-3 mb-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        Unggah <span class="text-maroon">Riset</span> Baru
      </h1>
      <p class="text-sm text-gray-600 mt-0.5">Lengkapi metadata untuk kualitas kurasi & penemuan yang baik.</p>
    </div>
    <div class="hidden md:flex gap-2">
      <button type="button" id="btnReset" class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">
        Reset
      </button>
      <button type="button" id="btnDraft" class="px-3 py-2 rounded-lg bg-white border border-gray-200 text-sm">
        Simpan Draft (Dummy)
      </button>
      <button type="submit" id="btnSubmit" class="px-3 py-2 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">
        Publikasikan
      </button>
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

  <!-- Progress -->
  <section class="rounded-xl bg-white border border-gray-200 p-4 mb-6">
    <div class="flex items-center justify-between text-xs">
      <span class="text-gray-500">Kelengkapan Metadata</span>
      <span id="progressPct">0%</span>
    </div>
    <div class="mt-2 h-2 bg-gray-100 rounded">
      <div id="progressBar" class="h-2 bg-maroon rounded transition-all" style="width:0%"></div>
    </div>
  </section>

  <section class="grid lg:grid-cols-3 gap-6">
    <!-- LEFT -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Informasi Utama -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Informasi Utama</h3>
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Judul</label>
            <input id="f_title" name="title" type="text" required
                   placeholder="Contoh: Model Prediksi Kebocoran Air PDAM Makassar"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Tahun</label>
            <select id="f_year" name="year" required
                    class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <option value="">Pilih tahun</option>
              @for($y = now()->year; $y >= now()->year-15; $y--)
                <option value="{{ $y }}">{{ $y }}</option>
              @endfor
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Jenis</label>
            <select id="f_type" name="type"
                    class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <option value="">Pilih jenis</option>
              <option value="internal">Riset Internal</option>
              <option value="kolaborasi">Kolaborasi/OPD</option>
              <option value="eksternal">Eksternal Terkait</option>
            </select>
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Abstrak</label>
            <textarea id="f_abstract" name="abstract" rows="5" required
                      placeholder="Ringkas tujuan, metode, dan hasil utama."
                      class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon"></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Metode (opsional)</label>
            <input id="f_method" name="method" type="text"
                   placeholder="Contoh: Gradient Boosting, 5-fold CV"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>
        </div>
      </div>
            <!-- Policy Brief (opsional) -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Policy Brief (opsional)</h3>

        <div class="mt-4 grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-700">Kategori Konten</label>
            <select id="f_category" name="category"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <option value="">Bukan Policy Brief</option>
              <option value="policy_brief">Policy Brief</option>
            </select>
            <p class="text-[11px] text-gray-500 mt-1">
              Pilih <span class="font-semibold text-maroon">Policy Brief</span> jika ini produk ringkas kebijakan.
            </p>
          </div>

          <div id="youtubeWrap" class="sm:col-span-2 hidden">
            <label class="text-xs font-semibold text-gray-700">Link YouTube Policy Brief</label>
            <input id="f_youtube" name="youtube_url" type="url"
                   placeholder="https://www.youtube.com/watch?v=xxxx"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
            <p class="text-[11px] text-gray-500 mt-1">
              Link video penjelasan policy brief. Akan dipakai sebagai embed di halaman publik.
            </p>
          </div>
        </div>
      </div>


      <!-- Penulis (repeater) -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-maroon">Penulis</h3>
          <button type="button" id="btnAddAuthor"
                  class="text-sm px-2 py-1 rounded border border-gray-300 hover:bg-gray-50">
            + Tambah
          </button>
        </div>

        <div id="authorsWrap" class="mt-3 space-y-3">
          <!-- item author akan di-clone dari template -->
        </div>

        <template id="tplAuthor">
          <div class="grid sm:grid-cols-12 gap-3 border-t pt-4">
            <div class="sm:col-span-4">
              <label class="text-xs font-semibold text-gray-700">Nama</label>
              <input data-name="name" type="text" required
                     class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
            </div>
            <div class="sm:col-span-4">
              <label class="text-xs font-semibold text-gray-700">Afiliasi</label>
              <input data-name="affiliation" type="text"
                     class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
            </div>
            <div class="sm:col-span-2">
              <label class="text-xs font-semibold text-gray-700">Peran</label>
              <input data-name="role" type="text" placeholder="Penulis/Koresp."
                     class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
            </div>
            <div class="sm:col-span-2">
              <label class="text-xs font-semibold text-gray-700">ORCID</label>
              <input data-name="orcid" type="text" placeholder="0000-0000-0000-0000"
                     class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
            </div>
            <div class="sm:col-span-12">
              <button type="button" class="btnDelAuthor text-xs text-rose-600 hover:underline">
                Hapus penulis
              </button>
            </div>
          </div>
        </template>

        <div class="mt-4 grid sm:grid-cols-3 gap-4 border-t pt-4">
          <div class="sm:col-span-3">
            <h4 class="text-xs font-semibold text-gray-700">Kontak Korespondensi</h4>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Nama</label>
            <input name="corresponding[name]" type="text"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Email</label>
            <input name="corresponding[email]" type="email"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Telepon (opsional)</label>
            <input name="corresponding[phone]" type="text"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300">
          </div>
        </div>
      </div>

      <!-- Dokumen & Lampiran -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Dokumen & Lampiran</h3>

        <div class="mt-4 grid sm:grid-cols-12 gap-4 items-start">
          <div class="sm:col-span-8">
            <label class="text-xs font-semibold text-gray-700">File Utama (PDF)</label>
            <div class="mt-1.5 border-2 border-dashed rounded-lg p-4 text-center">
              <input id="inpPdf" name="pdf_file" type="file" accept="application/pdf" required
                     class="block w-full text-sm text-gray-700 file:mr-2 file:px-3 file:py-1.5 file:rounded-md file:border-0 file:bg-maroon file:text-white">
              <p id="pdfNameHelp" class="mt-2 text-xs text-gray-500">Maks. 20MB. Format PDF.</p>
            </div>
          </div>
          <div class="sm:col-span-4">
            <label class="text-xs font-semibold text-gray-700">Thumbnail (opsional)</label>
            <div class="mt-1.5 border rounded-lg p-3">
              <input id="inpThumb" name="thumbnail" type="file" accept="image/*"
                     class="block w-full text-sm text-gray-700">
              <p class="mt-2 text-[11px] text-gray-500">PNG/JPG, maks 2MB.</p>
            </div>
          </div>
        </div>

        <div class="mt-6">
          <div class="flex items-center justify-between">
            <h4 class="text-xs font-semibold text-gray-700">Dataset/Lampiran Tambahan</h4>
            <button type="button" id="btnAddDataset"
                    class="text-sm px-2 py-1 rounded border border-gray-300 hover:bg-gray-50">+ Tambah</button>
          </div>

          <div id="datasetsWrap" class="mt-3 space-y-3">
            <!-- item dataset dari template -->
          </div>

          <template id="tplDataset">
            <div class="grid sm:grid-cols-12 gap-3 items-start">
              <div class="sm:col-span-5">
                <input data-name="label" type="text" placeholder="Nama lampiran/data"
                       class="w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              </div>
              <div class="sm:col-span-6">
                <input data-name="file" type="file"
                       class="w-full rounded-lg border p-2 border-gray-300 bg-white">
              </div>
              <div class="sm:col-span-1">
                <button type="button" class="btnDelDataset text-xs text-rose-600 hover:underline mt-2">Hapus</button>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Metadata Tambahan -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Metadata Tambahan</h3>
        <div class="mt-4 grid sm:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-700">Tag / Kata Kunci</label>
            <div class="mt-1.5 flex gap-2">
              <input id="inpTag" type="text" placeholder="Ketik lalu Enter"
                     class="flex-1 rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <button type="button" id="btnAddTag" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">Tambah</button>
            </div>
            <div id="tagsWrap" class="mt-2 flex flex-wrap gap-2"></div>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">Pihak Terkait</label>
            <div class="mt-1.5 flex gap-2">
              <input id="inpStake" type="text" placeholder="Contoh: PDAM"
                     class="flex-1 rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <button type="button" id="btnAddStake" class="px-3 py-2 rounded-lg border border-gray-300 text-sm">Tambah</button>
            </div>
            <div id="stakesWrap" class="mt-2 flex flex-wrap gap-2"></div>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">DOI (opsional)</label>
            <input name="doi" type="text" placeholder="10.xxxx/xxxx"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-700">URL OJS (opsional)</label>
            <input name="ojs_url" type="url" placeholder="https://ojs..."
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>
        </div>
      </div>

      <!-- Versi Awal -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Versi Awal</h3>
        <div class="mt-4 grid sm:grid-cols-3 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-700">Nomor Versi</label>
            <input name="version" type="text" placeholder="1.0"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon" value="1.0">
          </div>
          <div class="sm:col-span-2">
            <label class="text-xs font-semibold text-gray-700">Catatan Rilis</label>
            <input name="release_note" type="text" placeholder="Rilis awal"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon" value="Rilis awal">
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT -->
    <div class="space-y-6">
      <!-- Akses & Lisensi -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Akses & Lisensi</h3>
        <div class="mt-3 space-y-3">
          <div>
            <label class="text-xs font-semibold text-gray-700">Status Akses</label>
            <div class="mt-1.5 flex gap-3">
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="radio" name="access" value="Public" checked> Publik
              </label>
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="radio" name="access" value="Restricted"> Restricted
              </label>
            </div>
          </div>

          <div id="accessReasonWrap" class="hidden">
            <label class="text-xs font-semibold text-gray-700">Alasan/ketentuan akses</label>
            <textarea id="inpAccessReason" name="access_reason" rows="3"
                      placeholder="Contoh: Memuat data sensitif pegawai/mitra"
                      class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon"></textarea>
          </div>

          <div>
            <label class="text-xs font-semibold text-gray-700">Lisensi</label>
            <select name="license"
                    class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
              <option value="">Pilih lisensi</option>
              <option>CC BY 4.0</option>
              <option>CC BY-SA 4.0</option>
              <option>CC BY-NC 4.0</option>
              <option>Hak Cipta BRIDA</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Preview dummy -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Preview Singkat</h3>
        <div class="mt-3 rounded border border-gray-200 bg-gray-50 h-48 flex items-center justify-center text-xs text-gray-500">
          Preview PDF muncul di sini setelah tersimpan (dummy).
        </div>
      </div>

      <!-- Checklist -->
      <div class="rounded-xl bg-white border border-gray-200 p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-maroon">Checklist</h3>
        <ul class="mt-3 text-sm space-y-2 text-gray-700">
          <li><input type="checkbox" id="chkJudul" class="mr-2"> Judul terisi</li>
          <li><input type="checkbox" id="chkTahun" class="mr-2"> Tahun dipilih</li>
          <li><input type="checkbox" id="chkAbstrak" class="mr-2"> Abstrak terisi</li>
          <li><input type="checkbox" id="chkPenulis" class="mr-2"> Minimal 1 penulis</li>
          <li><input type="checkbox" id="chkPdf" class="mr-2"> File PDF dipilih</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- Sticky Action (mobile) -->
  <div class="fixed inset-x-0 bottom-0 z-30 bg-white/90 backdrop-blur border-t border-gray-200 p-3 md:hidden">
    <div class="flex gap-2">
      <button type="button" id="btnDraftMobile" class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm">
        Simpan Draft
      </button>
      <button type="submit" id="btnSubmitMobile" class="flex-1 px-3 py-2 rounded-lg bg-maroon text-white text-sm">
        Publikasikan
      </button>
    </div>
  </div>
</form>

@push('scripts')
<script>
(function(){
  // ===== Utilities =====
  const $ = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));
  const makeHiddenInput = (name, value) => {
    const i = document.createElement('input');
    i.type = 'hidden'; i.name = name; i.value = value;
    return i;
  };

  // ===== Elements =====
  const form = $('#risetForm');

  // progress & checklist
  const progressBar = $('#progressBar');
  const progressPct = $('#progressPct');
  const chkJudul = $('#chkJudul'), chkTahun = $('#chkTahun'),
        chkAbstrak = $('#chkAbstrak'), chkPenulis = $('#chkPenulis'), chkPdf = $('#chkPdf');

  // inputs utama
  // const fTitle = $('#f_title'), fYear = $('#f_year'), fAbstract = $('#f_abstract');
  // inputs utama
  const fTitle = $('#f_title'),
        fYear = $('#f_year'),
        fAbstract = $('#f_abstract');

  // policy brief
  const fCategory   = $('#f_category');
  const youtubeWrap = $('#youtubeWrap');
  const fYoutube    = $('#f_youtube');


  // PDF
  const inpPdf = $('#inpPdf'), pdfNameHelp = $('#pdfNameHelp');
  const inpThumb = $('#inpThumb');

  // access
  const accessRadios = $$('input[name="access"]');
  const accessReasonWrap = $('#accessReasonWrap'), inpAccessReason = $('#inpAccessReason');

  // authors
  const authorsWrap = $('#authorsWrap'), tplAuthor = $('#tplAuthor');
  const btnAddAuthor = $('#btnAddAuthor');

  // datasets
  const datasetsWrap = $('#datasetsWrap'), tplDataset = $('#tplDataset');
  const btnAddDataset = $('#btnAddDataset');

  // tags & stakeholders
  const tagsWrap = $('#tagsWrap'), inpTag = $('#inpTag'), btnAddTag = $('#btnAddTag');
  const stakesWrap = $('#stakesWrap'), inpStake = $('#inpStake'), btnAddStake = $('#btnAddStake');

  // actions
  const btnDraft = $('#btnDraft'), btnDraftMobile = $('#btnDraftMobile');
  const btnReset = $('#btnReset');

  // ===== Authors Repeater =====
  function addAuthorRow(data = {name:'',affiliation:'',role:'',orcid:''}) {
    const node = tplAuthor.content.cloneNode(true);
    const el = node.querySelector('.grid');

    // map data-name attr to input names later in reindexAuthors()
    el.querySelector('[data-name="name"]').value = data.name || '';
    el.querySelector('[data-name="affiliation"]').value = data.affiliation || '';
    el.querySelector('[data-name="role"]').value = data.role || '';
    el.querySelector('[data-name="orcid"]').value = data.orcid || '';

    el.querySelector('.btnDelAuthor').addEventListener('click', () => {
      if ($$('.grid', authorsWrap).length > 1) {
        el.remove();
        reindexAuthors();
        updateChecklistAndProgress();
      }
    });

    // update checklist/progress on input change
    $$('input', el).forEach(inp => inp.addEventListener('input', updateChecklistAndProgress));

    authorsWrap.appendChild(node);
    reindexAuthors();
    updateChecklistAndProgress();
  }

  function reindexAuthors() {
    $$('.grid', authorsWrap).forEach((row, idx) => {
      row.querySelector('[data-name="name"]').setAttribute('name', `authors[${idx}][name]`);
      row.querySelector('[data-name="affiliation"]').setAttribute('name', `authors[${idx}][affiliation]`);
      row.querySelector('[data-name="role"]').setAttribute('name', `authors[${idx}][role]`);
      row.querySelector('[data-name="orcid"]').setAttribute('name', `authors[${idx}][orcid]`);
    });
  }

  btnAddAuthor.addEventListener('click', () => addAuthorRow());

  // seed 1 row
  addAuthorRow();

  // ===== Datasets Repeater =====
  function addDatasetRow(data = {label:''}) {
    const node = tplDataset.content.cloneNode(true);
    const el = node.querySelector('.grid');

    el.querySelector('[data-name="label"]').value = data.label || '';

    el.querySelector('.btnDelDataset').addEventListener('click', () => {
      if ($$('.grid', datasetsWrap).length > 1) {
        el.remove();
        reindexDatasets();
      }
    });

    datasetsWrap.appendChild(node);
    reindexDatasets();
  }

  function reindexDatasets() {
    $$('.grid', datasetsWrap).forEach((row, idx) => {
      row.querySelector('[data-name="label"]').setAttribute('name', `datasets[${idx}][label]`);
      row.querySelector('[data-name="file"]').setAttribute('name', `datasets[${idx}][file]`);
    });
  }

  btnAddDataset.addEventListener('click', () => addDatasetRow());
  // seed 1 dataset row
  addDatasetRow();

  // ===== Tags =====
  function addTagChip(val) {
    if (!val) return;
    // prevent duplicate (case-insensitive)
    const exists = $$('.tag-chip', tagsWrap).some(x => x.dataset.val?.toLowerCase() === val.toLowerCase());
    if (exists) return;

    const chip = document.createElement('span');
    chip.className = 'tag-chip inline-flex items-center gap-1 px-2 py-1 rounded bg-maroon/10 text-maroon border border-maroon/20 text-xs';
    chip.dataset.val = val;
    chip.appendChild(document.createTextNode(val));

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ml-1';
    btn.textContent = '✕';
    btn.addEventListener('click', () => chip.remove());

    chip.appendChild(btn);
    chip.appendChild(makeHiddenInput(`tags[]`, val));
    tagsWrap.appendChild(chip);
  }

  btnAddTag.addEventListener('click', () => { addTagChip(inpTag.value.trim()); inpTag.value=''; });
  inpTag.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); btnAddTag.click(); }
  });

  // ===== Stakeholders =====
  function addStakeChip(val) {
    if (!val) return;
    const chip = document.createElement('span');
    chip.className = 'inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100 border text-xs';
    chip.appendChild(document.createTextNode(val));

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ml-1';
    btn.textContent = '✕';
    btn.addEventListener('click', () => chip.remove());

    chip.appendChild(btn);
    chip.appendChild(makeHiddenInput(`stakeholders[]`, val));
    stakesWrap.appendChild(chip);
  }

  btnAddStake.addEventListener('click', () => { addStakeChip(inpStake.value.trim()); inpStake.value=''; });
  inpStake.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') { e.preventDefault(); btnAddStake.click(); }
  });

  // ===== Access reason toggle =====
  function updateAccessVisibility(){
    const val = (accessRadios.find(r => r.checked) || {}).value;
    const restricted = (val === 'Restricted');
    accessReasonWrap.classList.toggle('hidden', !restricted);
    if (!restricted) inpAccessReason.value = '';
  }
  accessRadios.forEach(r => r.addEventListener('change', updateAccessVisibility));
  updateAccessVisibility();
 function updatePolicyBriefVisibility() {
    const val = fCategory.value;
    const isPB = (val === 'policy_brief');

    youtubeWrap.classList.toggle('hidden', !isPB);

    if (!isPB) {
      // kalau bukan policy brief, kosongkan youtube_url biar ga nyasar
      fYoutube.value = '';
    }
  }

  fCategory && fCategory.addEventListener('change', updatePolicyBriefVisibility);
  updatePolicyBriefVisibility();
  // ===== PDF name & checklist =====
  inpPdf.addEventListener('change', () => {
    pdfNameHelp.textContent = inpPdf.files?.[0]?.name || 'Maks. 20MB. Format PDF.';
    updateChecklistAndProgress();
  });

  // ===== Progress & checklist =====
  function updateChecklistAndProgress(){
    chkJudul.checked  = !!fTitle.value.trim();
    chkTahun.checked  = !!fYear.value;
    chkAbstrak.checked= !!fAbstract.value.trim();
    chkPdf.checked    = (inpPdf.files && inpPdf.files.length > 0);
    chkPenulis.checked= $$('.grid', authorsWrap).some(row => {
      const name = row.querySelector('[data-name="name"]').value.trim();
      return !!name;
    });

    let p = 0;
    if (chkJudul.checked) p+=20;
    if (chkTahun.checked) p+=15;
    if (chkAbstrak.checked && fAbstract.value.trim().length > 40) p+=20;
    if (chkPenulis.checked) p+=20;
    if (chkPdf.checked) p+=25;
    if (p > 100) p = 100;

    progressBar.style.width = p + '%';
    progressPct.textContent = p + '%';
  }
  [fTitle, fYear, fAbstract].forEach(el => el.addEventListener('input', updateChecklistAndProgress));

  // ===== Draft/Reset/Submit =====
  function validateBeforeSubmit(){
    const missing = [];
    if (!fTitle.value.trim()) missing.push('Judul');
    if (!fYear.value) missing.push('Tahun');
    if (!fAbstract.value.trim()) missing.push('Abstrak');

    const hasAuthor = $$('.grid', authorsWrap).some(row => row.querySelector('[data-name="name"]').value.trim());
    if (!hasAuthor) missing.push('Penulis');

    if (!(inpPdf.files && inpPdf.files.length)) missing.push('File PDF');

    const valAccess = (accessRadios.find(r => r.checked) || {}).value;
    if (valAccess === 'Restricted' && !inpAccessReason.value.trim()) missing.push('Alasan Akses');

    if (missing.length) {
      if (window.Swal) Swal.fire('Lengkapi dulu', 'Bidang wajib: ' + missing.join(', '), 'warning');
      else alert('Lengkapi: ' + missing.join(', '));
      return false;
    }
    return true;
  }

  btnDraft && btnDraft.addEventListener('click', () => {
    updateChecklistAndProgress();
    if (window.Swal) Swal.fire('Tersimpan', 'Draft (dummy)—belum ke server.', 'success');
    else alert('Draft (dummy) tersimpan.');
  });
  btnDraftMobile && btnDraftMobile.addEventListener('click', () => {
    updateChecklistAndProgress();
    if (window.Swal) Swal.fire('Tersimpan', 'Draft (dummy)—belum ke server.', 'success');
    else alert('Draft (dummy) tersimpan.');
  });

  btnReset && btnReset.addEventListener('click', () => resetForm());

  function resetForm(){
    form.reset();
    // clear dynamic chips
    tagsWrap.innerHTML = '';
    stakesWrap.innerHTML = '';
    // authors: sisakan 1
    authorsWrap.innerHTML = '';
    addAuthorRow();
    // datasets: sisakan 1
    datasetsWrap.innerHTML = '';
    addDatasetRow();

    pdfNameHelp.textContent = 'Maks. 20MB. Format PDF.';
    updateAccessVisibility();
    updateChecklistAndProgress();
  }

  form.addEventListener('submit', (e) => {
    if (!validateBeforeSubmit()) {
      e.preventDefault();
      return;
    }
    // pastikan index name authors/datasets up-to-date
    reindexAuthors();
    reindexDatasets();
  });

  // initial compute
  updateChecklistAndProgress();
})();
</script>
@endpush
@endsection
