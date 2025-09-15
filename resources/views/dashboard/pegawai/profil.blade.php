@extends('layouts.app')

@section('title', 'Profil Pegawai — SIGAP BRIDA')

@section('content')
  {{-- Breadcrumb --}}
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('home.index') }}" class="hover:text-maroon">Dashboard</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Profil Pegawai</li>
    </ol>
  </nav>

  {{-- Header + Identitas --}}
  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-5">
      <div class="flex items-start gap-4">
        {{-- Avatar --}}
        <div class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-maroon/20 shrink-0">
          @if ($user->profile_photo_path)
            <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="w-full h-full object-cover" alt="Foto profil">
          @else
            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold">
              {{ strtoupper(substr($user->name,0,1)) }}
            </div>
          @endif
        </div>

        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-2">
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
            @if(($user->status ?? 'active') === 'active')
              <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Aktif</span>
            @else
              <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">Nonaktif</span>
            @endif
          </div>
          <p class="text-sm text-gray-600 mt-0.5">
            <span class="text-gray-500">Username:</span> <span class="font-medium">{{ $user->username ?: '—' }}</span>
            <span class="mx-2">•</span>
            <span class="text-gray-500">Email:</span> <span class="font-medium">{{ $user->email }}</span>
          </p>

          <div class="mt-3 flex flex-wrap gap-2 text-xs">
            @forelse ($roleNames as $r)
              <span class="px-2 py-0.5 rounded bg-maroon/5 text-maroon border border-maroon/10">{{ ucfirst($r) }}</span>
            @empty
              <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200">Tanpa role</span>
            @endforelse
          </div>
        </div>

        {{-- Aksi cepat --}}
        <div class="shrink-0">
          <a href="{{ route('pegawai.profil.edit') }}" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Edit Profil</a>
        </div>
      </div>

      {{-- Info grid --}}
      <div class="mt-5 grid sm:grid-cols-3 gap-4 text-sm">
        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
          <p class="text-gray-500">NIP</p>
          <p class="font-semibold text-gray-900">{{ $user->nip ?: '—' }}</p>
        </div>
        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
          <p class="text-gray-500">Unit</p>
          <p class="font-semibold text-gray-900">{{ $user->unit ?: '—' }}</p>
        </div>
        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
          <p class="text-gray-500">Telepon</p>
          <p class="font-semibold text-gray-900">{{ $user->nomor_hp ?: '—' }}</p>
        </div>
        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
          <p class="text-gray-500">Bergabung</p>
          <p class="font-semibold text-gray-900">{{ optional($user->created_at)->format('d M Y') }}</p>
        </div>
        <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
          <p class="text-gray-500">Terakhir Update</p>
          <p class="font-semibold text-gray-900">{{ optional($user->updated_at)->format('d M Y H:i') }}</p>
        </div>
      </div>
    </div>
  </section>

  {{-- Berkas Saya --}}
  <section class="max-w-7xl mx-auto px-4 mt-6">
    <div class="bg-white border border-gray-200 rounded-2xl">
      <div class="px-5 py-4 flex items-center justify-between">
        <div>
          <h3 class="font-semibold text-gray-800">Berkas Saya</h3>
          <p class="text-sm text-gray-500">KTP, KK, dan berkas lain yang terkait akun ini.</p>
        </div>
        <div class="flex items-center gap-2">
          {{-- nanti: tombol unggah berkas --}}
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm" disabled>Unggah (coming soon)</button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left border-y">
              <th class="px-5 py-3">Jenis Berkas</th>
              <th class="px-5 py-3">Nama File</th>
              <th class="px-5 py-3">Status</th>
              <th class="px-5 py-3">Diunggah</th>
              <th class="px-5 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($docs as $d)
              <tr>
                <td class="px-5 py-3">{{ $d['label'] }}</td>
                <td class="px-5 py-3">{{ $d['filename'] }}</td>
                <td class="px-5 py-3">
                  @if($d['status']==='Terverifikasi')
                    <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">{{ $d['status'] }}</span>
                  @elseif($d['status']==='Menunggu verifikasi')
                    <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">{{ $d['status'] }}</span>
                  @else
                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">{{ $d['status'] }}</span>
                  @endif
                </td>
                <td class="px-5 py-3">{{ $d['uploaded_at'] }}</td>
                <td class="px-5 py-3">
                  <div class="flex gap-2">
                    <a href="{{ $d['url'] }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Lihat</a>
                    <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50" disabled>Hapus</button>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-5 py-6 text-center text-gray-500">
                  Belum ada berkas yang diunggah.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
@endsection
