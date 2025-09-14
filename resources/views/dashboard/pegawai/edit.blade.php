@extends('layouts.app')

@section('title', 'Edit Pegawai — SIGAP BRIDA')

@section('content')
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('sigap-pegawai.index') }}" class="hover:text-maroon">SIGAP Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Edit Pegawai</li>
    </ol>
  </nav>

  <section class="max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Edit Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Ubah data dasar, role, dan foto profil.</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('sigap-pegawai.index') }}" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Kembali</a>
        <button form="fEdit" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan</button>
      </div>
    </div>
  </section>

  <main class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-3 gap-6">
    <section class="lg:col-span-2 space-y-6">
      <form id="fEdit"
            class="bg-white border border-gray-200 rounded-2xl p-5 space-y-5"
            method="POST"
            action="{{ route('sigap-pegawai.update', $user) }}"
            enctype="multipart/form-data">
        @csrf @method('PUT')

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
            <span class="text-sm font-semibold text-gray-700">Nama</span>
            <input name="name" type="text" required
                   value="{{ old('name',$user->name) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Username</span>
            <input name="username" type="text" required
                   value="{{ old('username',$user->username) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email</span>
            <input name="email" type="email" required
                   value="{{ old('email',$user->email) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Unit</span>
            <input name="unit" type="text"
                   value="{{ old('unit',$user->unit) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">NIP</span>
            <input name="nip" type="text"
                   value="{{ old('nip',$user->nip) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status</span>
            <select name="status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="active"  @selected(old('status',$user->status)==='active')>Aktif</option>
              <option value="inactive" @selected(old('status',$user->status)==='inactive')>Nonaktif</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Telepon</span>
            <input name="nomor_hp" type="text"
                   value="{{ old('nomor_hp',$user->nomor_hp) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Password Baru</span>
            <input name="password" type="password" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Kosongkan jika tidak diganti">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Konfirmasi</span>
            <input name="password_confirmation" type="password" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
        </div>

        <div>
          <span class="text-sm font-semibold text-gray-700">Role</span>
          <div class="mt-2 grid sm:grid-cols-3 gap-2">
            @foreach($roles as $r)
              <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" name="roles[]" value="{{ $r }}"
                       class="rounded border-gray-300 text-maroon focus:ring-maroon"
                       @checked(in_array($r, old('roles',$userRoleNames)))>
                <span>{{ ucfirst($r) }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Foto Profil</span>
            <input name="avatar" type="file" accept=".jpg,.jpeg,.png"
                   class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon">
            <p class="text-[11px] text-gray-500 mt-1">JPG/PNG, maks 2MB.</p>
          </label>
          <div class="flex items-end gap-3">
            @if ($user->profile_photo_path)
              <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="w-16 h-16 rounded-full object-cover" alt="">
            @else
              <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">—</div>
            @endif
          </div>
        </div>
      </form>

        <form method="POST" action="{{ route('sigap-pegawai.avatar.destroy', $user) }}">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 rounded-md border text-red-700 border-red-300 hover:bg-red-50 text-sm"
                        onclick="return confirm('Hapus foto profil?')">
                  Hapus Foto
                </button>
        </form>
    </section>

    <aside class="space-y-6">
      <div class="bg-white border border-gray-200 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-gray-700">Ringkasan</h3>
        <dl class="mt-3 space-y-2 text-sm">
          <div class="flex justify-between"><dt class="text-gray-600">Nama</dt><dd class="font-medium">{{ $user->name }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Username</dt><dd class="font-medium">{{ $user->username }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Email</dt><dd class="font-medium">{{ $user->email }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Unit</dt><dd class="font-medium">{{ $user->unit ?: '—' }}</dd></div>
          <div class="flex justify-between"><dt class="text-gray-600">Role</dt><dd class="font-medium">{{ implode(', ', $userRoleNames) ?: '—' }}</dd></div>
        </dl>
      </div>
    </aside>
  </main>
@endsection
