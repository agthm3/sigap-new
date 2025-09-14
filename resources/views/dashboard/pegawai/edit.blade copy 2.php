@extends('layouts.app')

@section('content')
  {{-- Header --}}
  <section class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Ubah data dasar akun pegawai & informasi kontak.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('sigap-pegawai.index') }}" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Kembali</a>
        <button form="fPegawai" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan</button>
      </div>
    </div>
  </section>

  {{-- Alert errors --}}
  @if ($errors->any())
    <section class="max-w-7xl mx-auto px-4 mt-3">
      <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    </section>
  @endif
{{-- @dd($users) --}}
  {{-- Form + Sidebar --}}
  <main class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-3 gap-6">
    {{-- Kiri: Form data dasar --}}
    <section class="lg:col-span-2 space-y-6">
      <form id="fPegawai" method="POST"
            action="{{ route('sigap-pegawai.update', $users->id) }}"
            enctype="multipart/form-data"
            class="bg-white border border-gray-200 rounded-2xl p-5 space-y-5">
        @csrf
        @method('PUT')

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama Lengkap</span>
            <input name="name" type="text" required
              value="{{ old('name', $users->name) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
              placeholder="Contoh: Andi Rahman">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Username</span>
            <input name="username" type="text" required
              value="{{ old('username', $users->username) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
              placeholder="user.riset1">
            <p class="text-[11px] text-gray-500 mt-1">Huruf kecil, angka, titik. Unik.</p>
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">NIP </span>
            <input name="nip" type="text"
              value="{{ old('nip', $users->nip) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
              placeholder="19910505 201501 1 010">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Unit</span>
            <select name="unit" required
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              @php $units = ['Sekretariat A','Bidang Riset','TI','Keuangan','Humas']; @endphp
              <option value="">Pilih unit</option>
              @foreach ($units as $u)
                <option value="{{ $u }}" @selected(old('unit', $users->unit)===$u)>{{ $u }}</option>
              @endforeach
            </select>
          </label>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Role</span>
            <select name="role" required
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              @foreach (['pegawai','verifikator','admin'] as $r)
                <option value="{{ $r }}" @selected(old('role', $users->role)===$r)>{{ ucfirst($r) }}</option>
              @endforeach
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="active" @selected(old('status',$users->status)==='active')>Aktif</option>
              <option value="inactive" @selected(old('status',$users->status)==='inactive')>Nonaktif</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Telepon</span>
            <input name="phone" type="text"
              value="{{ old('phone', $users->phone) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
              placeholder="0411-xxxxx">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email</span>
            <input name="email" type="email"
              value="{{ old('email', $users->email) }}"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
              placeholder="nama@brida.go.id">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Password Awal (opsional)</span>
            <input name="password" type="password"
              class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
              placeholder="Minimal 8 karakter">
            <p class="text-[11px] text-gray-500 mt-1">Kosongkan bila tidak ingin mengubah password.</p>
          </label>
        </div>
      </form>

      {{-- Catatan --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Catatan</h3>
        <ul class="mt-2 text-xs text-gray-600 list-disc list-inside space-y-1">
          <li>Perubahan akan dicatat pada log aktivitas internal.</li>
          <li>Dokumen privasi (KK/KTP, dsb.) diatur pada modul dokumen pegawai.</li>
        </ul>
      </div>
    </section>

    {{-- Kanan: Foto & Aksi terpisah (di luar box form) --}}
    <aside class="space-y-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Foto Profil</h3>
        <div class="mt-3 flex items-center gap-4">
          @php
            $avatar = $users->avatar_path ? asset('storage/'.$users->avatar_path) : null;
            $initial = collect(explode(' ', $users->name))->map(fn($w)=>mb_substr($w,0,1))->take(2)->implode('');
          @endphp

          <div class="w-20 h-20 rounded-full bg-maroon/10 text-maroon flex items-center justify-center font-bold overflow-hidden">
            @if($avatar)
              <img src="{{ $avatar }}" class="w-full h-full object-cover" alt="Avatar">
            @else
              {{ $initial ?: '?' }}
            @endif
          </div>

          <div class="flex-1">
            <form id="avatarForm" method="POST" action="{{ route('sigap-pegawai.update', $users->id) }}" enctype="multipart/form-data" class="space-y-2">
              @csrf
              @method('PUT')
              <input name="avatar" type="file" accept=".jpg,.jpeg,.png"
                     class="block w-full text-sm rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <button class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Update Foto</button>
            </form>
            <p class="text-[11px] text-gray-500 mt-1">JPG/PNG, maks 2MB.</p>
          </div>
        </div>
      </div>

      {{-- Tombol Tambah Dokumen: DI LUAR box form utama --}}
      {{-- <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Dokumen Pegawai</h3>
        <p class="text-xs text-gray-600 mt-1">KTP/KK dan dokumen sensitif lain dikelola terpisah.</p>
        <div class="mt-3 flex flex-col gap-2">
          <a href="{{ route('users-documents.create', $users->id) }}"
             class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm text-center">
            Tambah Dokumen
          </a>
          <a href="#" class="px-3 py-2 rounded-lg border text-sm hover:bg-gray-50 text-center">
            Lihat Semua Dokumen
          </a>
        </div>
      </div> --}}
    </aside>
  </main>

  @if(session('success'))
    <section class="max-w-7xl mx-auto px-4 -mt-2">
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
        {{ session('success') }}
      </div>
    </section>
  @endif
@endsection
