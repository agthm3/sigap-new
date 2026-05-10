@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <h2 class="font-bold text-gray-900 mb-4">Informasi Sistem</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="text-sm font-medium text-gray-700">Nama Sistem</label>
        <input type="text" name="nama_sistem" value="{{ old('nama_sistem', $system->nama_sistem ?? '') }}"
               class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
        @error('nama_sistem') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- FIX: Kategori pakai <select> agar konsisten dengan filter di index --}}
      <div>
        <label class="text-sm font-medium text-gray-700">Kategori</label>
        <select name="kategori" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          <option value="">-- Pilih Kategori --</option>
          @foreach(['internal' => 'Internal', 'publik' => 'Publik', 'khusus' => 'Khusus', 'lainnya' => 'Lainnya'] as $val => $label)
            <option value="{{ $val }}" @selected(old('kategori', $system->kategori ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('kategori') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">URL Sistem</label>
        <input type="text" name="url" value="{{ old('url', $system->url ?? '') }}"
               placeholder="https://..."
               class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
        @error('url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">Link YouTube Tutorial</label>
        <input type="text" name="youtube_url" value="{{ old('youtube_url', $system->youtube_url ?? '') }}"
               placeholder="https://youtube.com/..."
               class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
        @error('youtube_url') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">Thumbnail / Path Gambar</label>
        <input type="text" name="thumbnail_path" value="{{ old('thumbnail_path', $system->thumbnail_path ?? '') }}"
               placeholder="opsional"
               class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
        @error('thumbnail_path') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">Status</label>
        <select name="status" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          @foreach(['aktif' => 'Aktif', 'maintenance' => 'Maintenance', 'nonaktif' => 'Nonaktif'] as $val => $label)
            <option value="{{ $val }}" @selected(old('status', $system->status ?? 'aktif') === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="text-sm font-medium text-gray-700">Level Kritis</label>
        <select name="level_kritis" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          <option value="">-</option>
          @foreach(['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'] as $val => $label)
            <option value="{{ $val }}" @selected(old('level_kritis', $system->level_kritis ?? '') === $val)>{{ $label }}</option>
          @endforeach
        </select>
        @error('level_kritis') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="mt-4">
      <label class="text-sm font-medium text-gray-700">Deskripsi</label>
      <textarea name="deskripsi" rows="4"
                class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">{{ old('deskripsi', $system->deskripsi ?? '') }}</textarea>
      @error('deskripsi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <h2 class="font-bold text-gray-900 mb-2">Catatan</h2>
    <p class="text-sm text-gray-600 leading-relaxed">
      Satu sistem bisa punya beberapa PIC dan beberapa akun. Password disimpan terenkripsi di database.
    </p>
    <div class="mt-4 rounded-xl bg-yellow-50 border border-yellow-200 p-3 text-sm text-yellow-800">
      Untuk tahap awal, kolom akun sensitif sebaiknya hanya dibuka oleh role admin.
    </div>
    <div class="mt-3 rounded-xl bg-blue-50 border border-blue-200 p-3 text-sm text-blue-800">
      PIC pertama yang dipilih otomatis menjadi <strong>PIC Utama</strong>. Urutan dapat diatur dengan drag &amp; drop.
    </div>
  </div>
</div>

{{-- ================================================================
     PIC / PENANGGUNG JAWAB — Searchable multi-select dengan drag & drop
     ================================================================ --}}
@php
  $selectedPicIds = old(
      'pic_user_ids',
      isset($system) ? $system->assignments->sortBy('urutan')->pluck('user_id')->filter()->map(fn($id) => (string)$id)->toArray() : []
  );
@endphp

<div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
  <h2 class="font-bold text-gray-900 mb-3">PIC / Penanggung Jawab</h2>

  <p class="text-xs text-gray-500 mb-3">
    Cari dan klik pegawai untuk menambahkan sebagai PIC. PIC pertama otomatis menjadi <span class="font-semibold text-maroon">PIC Utama</span>. Drag untuk mengubah urutan.
  </p>

  @error('pic_user_ids')
    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
  @enderror
  @error('pic_user_ids.*')
    <p class="text-xs text-red-600 mb-2">{{ $message }}</p>
  @enderror

  {{-- Search Box --}}
  <div class="relative mb-2">
    <input type="text"
           id="picSearch"
           placeholder="Cari nama, NIP, atau unit pegawai..."
           class="w-full pl-9 pr-3 py-2.5 rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon text-sm">
    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
    </svg>
  </div>

  {{-- Dropdown list pegawai --}}
  <div id="picDropdown"
       class="hidden max-h-52 overflow-y-auto rounded-xl border border-gray-200 bg-white shadow-md divide-y divide-gray-100 mb-3 z-10">
    @foreach($employees as $emp)
      <div class="pic-option px-3 py-2.5 hover:bg-maroon/5 cursor-pointer text-sm flex items-center justify-between gap-2"
           data-id="{{ $emp->id }}"
           data-name="{{ $emp->name }}"
           data-nip="{{ $emp->nip }}"
           data-unit="{{ $emp->unit ?? '-' }}"
           data-search="{{ strtolower($emp->name . ' ' . $emp->nip . ' ' . ($emp->unit ?? '')) }}">
        <div>
          <span class="font-medium text-gray-900">{{ $emp->name }}</span>
          <span class="ml-2 text-xs text-gray-500">NIP: {{ $emp->nip ?? '-' }}</span>
          <span class="ml-1 text-xs text-gray-400">| {{ $emp->unit ?? '-' }}</span>
        </div>
        <span class="pic-check hidden text-maroon">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
          </svg>
        </span>
      </div>
    @endforeach
  </div>

  {{-- Selected PICs (sortable) --}}
  <div id="selectedPicList" class="space-y-2 min-h-[48px]">
    {{-- Diisi oleh JS --}}
  </div>

  {{-- Hidden inputs — diisi JS sebelum submit --}}
  <div id="picHiddenInputs"></div>

  {{-- Data pegawai untuk JS --}}
  @php
    $employeesJson = $employees->map(function ($e) {
        return [
            'id'   => (string) $e->id,
            'name' => $e->name,
            'nip'  => $e->nip ?? '',
            'unit' => $e->unit ?? '-',
        ];
    })->values();
  @endphp
  <script id="employeesData" type="application/json">
    {!! json_encode($employeesJson) !!}
  </script>

  {{-- Pre-selected (old / edit) --}}
  <script id="selectedPicIdsData" type="application/json">
    @json($selectedPicIds)
  </script>
</div>

{{-- ================================================================
     AKUN / CREDENTIAL
     ================================================================ --}}
@php
  $credentialRows = old('credentials')
      ?? (
          isset($system)
          ? $system->credentials->map(fn ($c) => $c->toArray())->toArray()
          : [['nama_akun' => '', 'username' => '', 'password_encrypted' => '', 'email' => '', 'url_login' => '', 'access_level' => '', 'is_sensitive' => true, 'catatan' => '']]
      );
@endphp

<div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
  <div class="flex items-center justify-between gap-3 mb-4">
    <h2 class="font-bold text-gray-900">Akun / Credential</h2>
    <button type="button" id="addCredential"
            class="px-3 py-2 rounded-xl border border-maroon text-maroon text-sm hover:bg-maroon hover:text-white">
      + Tambah Akun
    </button>
  </div>

  <div id="credentialsWrap" class="space-y-4">
    @foreach($credentialRows as $i => $row)
      <div class="credential-row rounded-2xl border border-gray-200 bg-gray-50 p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-sm font-semibold text-gray-800">Akun {{ $i + 1 }}</h3>
          <button type="button" class="removeCredential text-xs text-red-600 hover:underline">Hapus</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="text-sm font-medium text-gray-700">Nama Akun</label>
            <input type="text" name="credentials[{{ $i }}][nama_akun]"
                   value="{{ data_get($row, 'nama_akun') }}"
                   class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="credentials[{{ $i }}][username]"
                   value="{{ data_get($row, 'username') }}"
                   class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>

          {{-- FIX: Password field dengan toggle show/hide --}}
          <div>
            <label class="text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1 relative">
              <input type="password" name="credentials[{{ $i }}][password_encrypted]"
                     value="{{ data_get($row, 'password_encrypted') }}"
                     class="password-field w-full pr-10 rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
              <button type="button"
                      class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
                <svg class="eye-open w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg class="eye-close w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="credentials[{{ $i }}][email]"
                   value="{{ data_get($row, 'email') }}"
                   class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">URL Login</label>
            <input type="text" name="credentials[{{ $i }}][url_login]"
                   value="{{ data_get($row, 'url_login') }}"
                   class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>

          <div>
            <label class="text-sm font-medium text-gray-700">Level Akses</label>
            <input type="text" name="credentials[{{ $i }}][access_level]"
                   value="{{ data_get($row, 'access_level') }}"
                   class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          </div>

          <div class="md:col-span-2">
            <label class="text-sm font-medium text-gray-700">Catatan</label>
            <textarea name="credentials[{{ $i }}][catatan]" rows="3"
                      class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">{{ data_get($row, 'catatan') }}</textarea>
          </div>

          <div class="flex items-center gap-2">
            <input type="checkbox" name="credentials[{{ $i }}][is_sensitive]" value="1"
                   @checked((bool) data_get($row, 'is_sensitive', true))
                   class="rounded border-gray-300 text-maroon focus:ring-maroon">
            <span class="text-sm text-gray-700">Akun sensitif</span>
          </div>
        </div>
      </div>
    @endforeach
  </div>
</div>

<div class="flex items-center gap-3 mt-4">
  <button type="submit"
          class="px-4 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
    Simpan
  </button>
  <a href="{{ route('sigap-pic.index') }}"
     class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm hover:bg-gray-50">
    Batal
  </a>
</div>

{{-- Template credential baru --}}
<template id="credentialTemplate">
  <div class="credential-row rounded-2xl border border-gray-200 bg-gray-50 p-4">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-sm font-semibold text-gray-800">Akun Baru</h3>
      <button type="button" class="removeCredential text-xs text-red-600 hover:underline">Hapus</button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <div><label class="text-sm font-medium text-gray-700">Nama Akun</label><input type="text" name="credentials[__INDEX__][nama_akun]" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon"></div>
      <div><label class="text-sm font-medium text-gray-700">Username</label><input type="text" name="credentials[__INDEX__][username]" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon"></div>
      <div><label class="text-sm font-medium text-gray-700">Password</label>
        <div class="mt-1 relative">
          <input type="password" name="credentials[__INDEX__][password_encrypted]" class="password-field w-full pr-10 rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
          <button type="button" class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700">
            <svg class="eye-open w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg class="eye-close w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
          </button>
        </div>
      </div>
      <div><label class="text-sm font-medium text-gray-700">Email</label><input type="email" name="credentials[__INDEX__][email]" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon"></div>
      <div><label class="text-sm font-medium text-gray-700">URL Login</label><input type="text" name="credentials[__INDEX__][url_login]" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon"></div>
      <div><label class="text-sm font-medium text-gray-700">Level Akses</label><input type="text" name="credentials[__INDEX__][access_level]" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon"></div>
      <div class="md:col-span-2"><label class="text-sm font-medium text-gray-700">Catatan</label><textarea name="credentials[__INDEX__][catatan]" rows="3" class="mt-1 w-full rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon"></textarea></div>
      <div class="flex items-center gap-2"><input type="checkbox" name="credentials[__INDEX__][is_sensitive]" value="1" checked class="rounded border-gray-300 text-maroon focus:ring-maroon"><span class="text-sm text-gray-700">Akun sensitif</span></div>
    </div>
  </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

  // ============================================================
  // 1. CREDENTIAL: Tambah / Hapus
  // ============================================================
  const credentialsWrap = document.getElementById('credentialsWrap');
  const addCredential   = document.getElementById('addCredential');

  function nextIndex() {
    return Date.now() + '_' + Math.floor(Math.random() * 1000);
  }

  addCredential?.addEventListener('click', function () {
    const template = document.getElementById('credentialTemplate').innerHTML;
    credentialsWrap.insertAdjacentHTML('beforeend', template.replaceAll('__INDEX__', nextIndex()));
  });

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('removeCredential')) {
      const rows = document.querySelectorAll('.credential-row');
      if (rows.length > 1) e.target.closest('.credential-row').remove();
    }

    // Toggle password visibility
    if (e.target.closest('.toggle-password')) {
      const btn   = e.target.closest('.toggle-password');
      const wrap  = btn.closest('div.relative');
      const input = wrap.querySelector('.password-field');
      const eyeOpen  = btn.querySelector('.eye-open');
      const eyeClose = btn.querySelector('.eye-close');

      if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClose.classList.remove('hidden');
      } else {
        input.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClose.classList.add('hidden');
      }
    }
  });

  // ============================================================
  // 2. PIC: Searchable Multi-select + Drag & Drop
  // ============================================================
  const employees       = JSON.parse(document.getElementById('employeesData').textContent);
  const preSelectedIds  = JSON.parse(document.getElementById('selectedPicIdsData').textContent);

  const picSearch       = document.getElementById('picSearch');
  const picDropdown     = document.getElementById('picDropdown');
  const selectedPicList = document.getElementById('selectedPicList');
  const picHiddenInputs = document.getElementById('picHiddenInputs');

  // State: array of employee objects in order
  let selectedPics = [];

  // Init from pre-selected (edit mode / old input)
  preSelectedIds.forEach(id => {
    const emp = employees.find(e => String(e.id) === String(id));
    if (emp && !selectedPics.find(p => p.id === emp.id)) {
      selectedPics.push(emp);
    }
  });

  renderSelectedPics();
  updateDropdownChecks();

  // Search input
  picSearch.addEventListener('focus', () => {
    picDropdown.classList.remove('hidden');
    filterDropdown('');
  });

  picSearch.addEventListener('input', function () {
    picDropdown.classList.remove('hidden');
    filterDropdown(this.value.trim().toLowerCase());
  });

  document.addEventListener('click', function (e) {
    if (!picSearch.contains(e.target) && !picDropdown.contains(e.target)) {
      picDropdown.classList.add('hidden');
    }
  });

  function filterDropdown(q) {
    document.querySelectorAll('.pic-option').forEach(opt => {
      const match = !q || opt.dataset.search.includes(q);
      opt.style.display = match ? '' : 'none';
    });
  }

  // Klik opsi di dropdown
  picDropdown.addEventListener('click', function (e) {
    const opt = e.target.closest('.pic-option');
    if (!opt) return;

    const id = opt.dataset.id;
    const emp = employees.find(e => String(e.id) === String(id));
    if (!emp) return;

    const idx = selectedPics.findIndex(p => String(p.id) === String(id));
    if (idx === -1) {
      selectedPics.push(emp);
    } else {
      selectedPics.splice(idx, 1);
    }

    renderSelectedPics();
    updateDropdownChecks();
  });

  function updateDropdownChecks() {
    document.querySelectorAll('.pic-option').forEach(opt => {
      const isSelected = selectedPics.some(p => String(p.id) === String(opt.dataset.id));
      opt.querySelector('.pic-check').classList.toggle('hidden', !isSelected);
      opt.classList.toggle('bg-maroon/5', isSelected);
    });
  }

  function renderSelectedPics() {
    selectedPicList.innerHTML = '';
    picHiddenInputs.innerHTML = '';

    if (selectedPics.length === 0) {
      selectedPicList.innerHTML = '<p class="text-sm text-gray-400 italic py-2">Belum ada PIC dipilih.</p>';
      return;
    }

    selectedPics.forEach((emp, index) => {
      // Hidden input
      const input = document.createElement('input');
      input.type  = 'hidden';
      input.name  = 'pic_user_ids[]';
      input.value = emp.id;
      picHiddenInputs.appendChild(input);

      // Visible chip
      const chip = document.createElement('div');
      chip.className = 'flex items-center gap-3 px-3 py-2 rounded-xl border ' +
        (index === 0
          ? 'border-maroon/30 bg-maroon/5'
          : 'border-gray-200 bg-gray-50');
      chip.setAttribute('draggable', 'true');
      chip.dataset.index = index;

      chip.innerHTML = `
        <span class="cursor-grab text-gray-400 drag-handle">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
          </svg>
        </span>
        <div class="flex-1 min-w-0">
          <span class="text-sm font-semibold text-gray-900">${emp.name}</span>
          <span class="ml-2 text-xs text-gray-500">NIP: ${emp.nip || '-'}</span>
          <span class="ml-1 text-xs text-gray-400">| ${emp.unit || '-'}</span>
        </div>
        ${index === 0
          ? '<span class="text-[10px] px-2 py-0.5 rounded-full bg-maroon text-white shrink-0">UTAMA</span>'
          : ''}
        <button type="button" class="remove-pic shrink-0 text-gray-400 hover:text-red-600" data-id="${emp.id}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      `;

      // Remove PIC
      chip.querySelector('.remove-pic').addEventListener('click', function () {
        const removeId = this.dataset.id;
        selectedPics = selectedPics.filter(p => String(p.id) !== String(removeId));
        renderSelectedPics();
        updateDropdownChecks();
      });

      // Drag & Drop
      chip.addEventListener('dragstart', onDragStart);
      chip.addEventListener('dragover',  onDragOver);
      chip.addEventListener('drop',      onDrop);
      chip.addEventListener('dragend',   onDragEnd);

      selectedPicList.appendChild(chip);
    });
  }

  // Drag & Drop state
  let dragSrcIndex = null;

  function onDragStart(e) {
    dragSrcIndex = parseInt(this.dataset.index);
    this.classList.add('opacity-50');
    e.dataTransfer.effectAllowed = 'move';
  }

  function onDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    this.classList.add('ring-2', 'ring-maroon/30');
  }

  function onDrop(e) {
    e.preventDefault();
    const destIndex = parseInt(this.dataset.index);
    if (dragSrcIndex === null || dragSrcIndex === destIndex) return;

    const moved = selectedPics.splice(dragSrcIndex, 1)[0];
    selectedPics.splice(destIndex, 0, moved);
    renderSelectedPics();
    updateDropdownChecks();
  }

  function onDragEnd() {
    document.querySelectorAll('#selectedPicList > div').forEach(el => {
      el.classList.remove('opacity-50', 'ring-2', 'ring-maroon/30');
    });
    dragSrcIndex = null;
  }
});
</script>
@endpush