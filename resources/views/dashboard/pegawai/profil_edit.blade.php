@extends('layouts.app')

@section('title','Edit Profil — SIGAP BRIDA')

@section('content')
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('pegawai.profil') }}" class="hover:text-maroon">Profil Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Edit Profil</li>
    </ol>
  </nav>

  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
      <form id="fSelfEdit" action="{{ route('pegawai.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        @if (session('success'))
          <div class="rounded-md border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
            {{ session('success') }}
          </div>
        @endif

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
            <input name="name" type="text" required
                   value="{{ old('name',$user->name) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Email</span>
            <input name="email" type="email" required
                   value="{{ old('email',$user->email) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <p class="text-[11px] text-gray-500 mt-1">Perubahan email dapat memerlukan verifikasi ulang.</p>
          </label>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nomor HP</span>
            <input name="nomor_hp" type="text"
                   value="{{ old('nomor_hp',$user->nomor_hp) }}"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <div></div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Password Baru</span>
            <input name="password" type="password"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="Kosongkan jika tidak diganti">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Konfirmasi Password</span>
            <input name="password_confirmation" type="password"
                   class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
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
              <form method="POST" action="{{ route('pegawai.profil.avatar.destroy') }}">
                @csrf @method('DELETE')
                <button class="px-3 py-1.5 rounded-md border text-red-700 border-red-300 hover:bg-red-50 text-sm"
                        onclick="return confirm('Hapus foto profil?')">
                  Hapus Foto
                </button>
              </form>
            @else
              <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-500">—</div>
            @endif
          </div>
        </div>

        <div class="flex items-center gap-2 pt-2">
          <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan Perubahan</button>
          <a href="{{ route('pegawai.profil') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Batal</a>
        </div>
      </form>
    </div>
  </section>
@endsection
