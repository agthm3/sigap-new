@extends('layouts.app')

@section('content')
<section class="mb-4">
  <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
    Tambah <span class="text-maroon">Role Baru</span>
  </h1>
  <p class="text-sm text-gray-600">Buat role baru untuk menentukan hak akses pengguna.</p>
</section>

<div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm max-w-2xl">
  <form action="{{ route('roles.store') }}" method="POST" class="space-y-4">
    @csrf

    <div>
      <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Role</label>
      <input type="text" name="name" id="name" value="{{ old('name') }}" required
             placeholder="Contoh: verifikator_dokumen"
             class="w-full px-3 py-2 rounded-lg border text-sm focus:ring-maroon focus:border-maroon @error('name') border-red-500 @enderror">
      @error('name')
        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
      @enderror
    </div>

    @if($permissions->count() > 0)
    <div>
      <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Permission (Opsional)</label>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 border rounded-lg scrollbar-thin">
        @foreach($permissions as $perm)
          <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
            <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="rounded text-maroon focus:ring-maroon">
            {{ $perm->name }}
          </label>
        @endforeach
      </div>
    </div>
    @endif

    <div class="flex gap-2 pt-2">
      <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
        Simpan Role
      </button>
      <a href="{{ route('roles.index') }}" class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold text-gray-600 hover:bg-gray-100">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection