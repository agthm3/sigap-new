@extends('layouts.app')

@section('title', 'Tambah Pegawai — SIGAP BRIDA')

@section('content')
  {{-- Breadcrumb --}}
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('sigap-pegawai.index') }}" class="hover:text-maroon">SIGAP Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Tambah Pegawai</li>
    </ol>
  </nav>

  {{-- Header --}}
  <section class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Tambah Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Lengkapi data dasar untuk membuat akun pegawai dan mengelola akses dokumen.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('sigap-pegawai.index') }}" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Kembali</a>
        <button form="fPegawai" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan</button>
      </div>
    </div>
  </section>

  {{-- Form --}}
  <main class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-3 gap-6">
    {{-- Kiri --}}
    <section class="lg:col-span-2 space-y-6">
      @php($roles = $roles ?? ['admin','inovator','verificator','employee','researcher','user'])
      <form id="fPegawai"
            class="bg-white border border-gray-200 rounded-2xl p-5 space-y-5"
            method="POST"
            action="{{ route('sigap-pegawai.users.store') }}"
            enctype="multipart/form-data">
        @csrf
      <input type="hidden" name="admin_create" value="1">
        {{-- errors --}}
        @if ($errors->any())
          <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama Lengkap</span>
            <input name="name" id="name" type="text" required
                   value="{{ old('name') }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="Contoh: Andi Rahman">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Username</span>
            <div class="mt-1.5 flex">
              <input name="username" id="username" type="text" required
                     value="{{ old('username') }}"
                     class="flex-1 rounded-l-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                     placeholder="user.riset1">
              <button type="button" id="genUsername"
                      class="px-3 rounded-r-lg border border-l-0 border-gray-300 text-sm hover:bg-gray-50">
                Generate
              </button>
            </div>
            <p class="text-[11px] text-gray-500 mt-1">Huruf kecil, angka, titik. Unik.</p>
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">NIP (opsional)</span>
            <input name="nip" id="nip" type="text"
                   value="{{ old('nip') }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="19910505 201501 1 010">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Unit</span>
            <select name="unit" id="unit" required
                    class="mt-1.5 w-full rounded-lg p-2 border border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih unit</option>
              @foreach (['Sekretariat A','Bidang Riset','TI','Keuangan','Humas'] as $u)
                <option value="{{ $u }}" @selected(old('unit')===$u)>{{ $u }}</option>
              @endforeach
            </select>
          </label>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          
          <label class="block">
        <div class="mt-4">
          <span class="text-sm font-semibold text-gray-700">Role</span>
          <div class="mt-2 grid sm:grid-cols-3 gap-2">
            @foreach($roles as $r)
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="roles[]" value="{{ $r }}" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                <span>{{ ucfirst($r) }}</span>
              </label>
            @endforeach
          </div>
        </div>

          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status" id="status"
                    class="mt-1.5 w-full p-2 rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="active"  @selected(old('status','active')==='active')>Aktif</option>
              <option value="inactive" @selected(old('status')==='inactive')>Nonaktif</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Telepon</span>
            <input name="phone" id="phone" type="text"
                   value="{{ old('phone') }}"
                   class="mt-1.5 w-full rounded-lg p-2 border border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="0411-xxxxx">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email</span>
            <input name="email" id="email" type="email"
                   value="{{ old('email') }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="nama@brida.go.id">
          </label>
          <div class="grid grid-cols-5 gap-2">
            <div class="col-span-2">
              <label class="text-sm font-semibold text-gray-700">Password </label>
              <div class="mt-1.5 flex">
                <input name="password" id="password" type="password"
                       class="flex-1 rounded-l-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                       placeholder="Minimal 8 karakter">
                <button type="button" id="togglePwd"
                        class="px-3 rounded-r-lg border border-l-0 border-gray-300 text-sm hover:bg-gray-50">
                  Lihat
                </button>
              </div>
              <label class="text-sm font-semibold text-gray-700">Konfirmasi Password</label>
              <div class="mt-1.5 flex">
                <input name="password_confirmation" id="password_confirmation" type="password"
                       class="flex-1 rounded-l-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                       placeholder="Minimal 8 karakter">
                <button type="button" id="togglePwd"
                        class="px-3 rounded-r-lg border border-l-0 border-gray-300 text-sm hover:bg-gray-50">
                  Lihat
                </button>
              </div>
            </div>
          </div>
          <p class="sm:col-span-2 text-[11px] text-gray-500 -mt-2">
            Password tidak wajib bila memilih “Tidak sekarang”. Gunakan SSO/AD di produksi bila ada.
          </p>
        </div>
      </form>

      {{-- Catatan --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Catatan</h3>
        <ul class="mt-2 text-xs text-gray-600 list-disc list-inside space-y-1">
          <li>Data dapat diedit kembali di halaman profil pegawai.</li>
          <li>Dokumen privasi bisa ditambahkan setelah akun dibuat, pada tab dokumen pegawai.</li>
          <li>Semua aktivitas tambah/edit akan tercatat pada Log Aktivitas.</li>
        </ul>
      </div>
    </section>

    {{-- Kanan --}}
    <aside class="space-y-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Foto Profil</h3>
        <div class="mt-3 flex items-center gap-4">
          <div id="avatarPreview"
               class="w-20 h-20 rounded-full bg-maroon/10 text-maroon flex items-center justify-center font-bold">?</div>
          <div class="flex-1">
            <input name="avatar" id="avatar" type="file" accept=".jpg,.jpeg,.png"
                   form="fPegawai"
                   class="block w-full text-sm rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon">
            <p class="text-[11px] text-gray-500 mt-1">JPG/PNG, maks 2MB.</p>
          </div>
        </div>
      </div>

      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Ringkasan</h3>
        <dl class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between gap-3"><dt class="text-gray-600">Nama</dt><dd id="sum_name" class="font-medium">—</dd></div>
          <div class="flex justify-between gap-3"><dt class="text-gray-600">Username</dt><dd id="sum_username" class="font-medium">—</dd></div>
          <div class="flex justify-between gap-3"><dt class="text-gray-600">Unit</dt><dd id="sum_unit" class="font-medium">—</dd></div>
          <div class="flex justify-between gap-3"><dt class="text-gray-600">Role</dt><dd id="sum_role" class="font-medium">Pegawai</dd></div>
          <div class="flex justify-between gap-3"><dt class="text-gray-600">Status</dt><dd id="sum_status" class="font-medium">Aktif</dd></div>
        </dl>
        <div class="mt-4 flex flex-col gap-2">
          <button form="fPegawai" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan Pegawai</button>
          <a href="{{ route('sigap-pegawai.index') }}" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50 text-center">Batal</a>
        </div>
      </div>
    </aside>
  </main>
@endsection

@push('scripts')
<script>
  const $ = s => document.querySelector(s);
  const nameEl = $('#name'), userEl = $('#username'), genBtn = $('#genUsername');
  const unitEl = $('#unit'), roleEl = $('#role'), statusEl = $('#status');
  const sName = $('#sum_name'), sUser = $('#sum_username'), sUnit = $('#sum_unit'), sRole = $('#sum_role'), sStatus = $('#sum_status');
  const pwd = $('#password'), togglePwd = $('#togglePwd');
  const avatar = $('#avatar'), avatarPrev = $('#avatarPreview');

  genBtn?.addEventListener('click', () => {
    const base = (nameEl.value || 'pegawai').toLowerCase().replace(/[^a-z0-9\s.]/g,'').trim().replace(/\s+/g,'.');
    const suffix = Math.floor(10+Math.random()*90);
    userEl.value = base ? `${base}${base.includes('.')?'':'.'}${suffix}` : `user.${suffix}`;
    updateSummary();
  });

  togglePwd?.addEventListener('click', () => {
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
    togglePwd.textContent = pwd.type === 'password' ? 'Lihat' : 'Sembunyi';
  });

  [nameEl,userEl,unitEl,roleEl,statusEl].forEach(el => el?.addEventListener('input', updateSummary));
  function updateSummary(){
    sName.textContent = nameEl?.value || '—';
    sUser.textContent = userEl?.value || '—';
    sUnit.textContent = unitEl?.value || '—';
    sRole.textContent = roleEl?.value || 'Pegawai';
    sStatus.textContent = (statusEl?.value === 'inactive') ? 'Nonaktif' : 'Aktif';
    // avatar initial
    const ini = (nameEl?.value||'?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
    if(avatar && !avatar.dataset.set){ avatarPrev.textContent = ini || '?'; }
  }
  updateSummary();

  avatar?.addEventListener('change', () => {
    const f = avatar.files[0];
    if(!f) return;
    if(f.size > 2*1024*1024){ alert('Ukuran foto melebihi 2MB'); avatar.value=''; return; }
    const url = URL.createObjectURL(f);
    avatarPrev.innerHTML = '';
    avatarPrev.style.backgroundImage = `url('${url}')`;
    avatarPrev.style.backgroundSize = 'cover';
    avatarPrev.style.backgroundPosition = 'center';
    avatar.dataset.set = '1';
  });
</script>
@endpush
