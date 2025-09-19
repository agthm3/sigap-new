@extends('layouts.app')

@section('title','Detail Dokumen — SIGAP BRIDA')

@section('content')
  @php use Illuminate\Support\Str; @endphp

  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('pegawai.profil') }}" class="hover:text-maroon">Profil Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Detail Dokumen</li>
    </ol>
  </nav>

  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-5">

      {{-- Flash & errors --}}
      @if (session('success'))
        <div class="mb-3 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
      @endif
      @if (session('warning'))
        <div class="mb-3 rounded border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">{{ session('warning') }}</div>
      @endif
      @if ($errors->any())
        <div class="mb-3 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
          <ul class="list-disc list-inside">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- PREVIEW --}}
      <div class="mt-2">
        <h3 class="font-semibold text-gray-800 mb-2">Preview Dokumen</h3>
        @php $mime = $doc->mime; @endphp

        @if (Str::startsWith($mime ?? '', 'image/'))
          {{-- Preview gambar --}}
          <div class="border rounded-xl overflow-hidden">
            <img src="{{ route('pegawai.docs.preview', $doc->id) }}"
                 alt="Preview {{ $doc->title }}"
                 class="w-full h-auto max-h-[70vh] object-contain bg-gray-50">
          </div>
        @elseif (($mime ?? '') === 'application/pdf')
          {{-- Preview PDF --}}
          <div class="border rounded-xl overflow-hidden bg-gray-50">
            <iframe
              src="{{ route('pegawai.docs.preview', $doc->id) }}#zoom=page-width"
              class="w-full h-[70vh]"
              title="Preview PDF"
              loading="lazy">
            </iframe>
          </div>
        @else
          {{-- Fallback --}}
          <div class="rounded-lg border p-3 bg-amber-50 border-amber-200 text-amber-800 text-sm">
            Format file <span class="font-mono">{{ $mime ?: 'unknown' }}</span> tidak mendukung preview.
            Silakan gunakan tombol <span class="font-semibold">Unduh Dokumen</span>.
          </div>
        @endif
      </div>

      {{-- BODY: kiri-kanan --}}
      <div class="mt-6 grid md:grid-cols-3 gap-6 items-start">

        {{-- KIRI (info dokumen) --}}
        <div class="md:col-span-2">
          <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $doc->title }}</h1>
          @php
            $status = $doc->status === 'verified'
              ? 'Terverifikasi'
              : ($doc->status === 'pending' ? 'Menunggu verifikasi' : 'Ditolak');
          @endphp
          <p class="text-sm text-gray-600 mt-1">
            Jenis: <span class="font-medium uppercase">{{ $doc->type }}</span>
            <span class="mx-2">•</span>
            Status: <span class="font-medium">{{ $status }}</span>
          </p>

          <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
            <div class="p-3 border rounded-lg bg-gray-50">
              <p class="text-gray-500">Ukuran</p>
              <p class="font-medium text-gray-900">{{ number_format(($doc->size ?? 0)/1024,1) }} KB</p>
            </div>
            <div class="p-3 border rounded-lg bg-gray-50">
              <p class="text-gray-500">Diupload</p>
              <p class="font-medium text-gray-900">{{ optional($doc->created_at)->format('d M Y H:i') }}</p>
            </div>
            <div class="p-3 border rounded-lg bg-gray-50">
              <p class="text-gray-500">Hint Kode</p>
              <p class="font-medium text-gray-900">{{ $doc->access_code_hint ?: '—' }}</p>
            </div>
            <div class="p-3 border rounded-lg bg-gray-50">
              <p class="text-gray-500">Kode Diatur</p>
              <p class="font-medium text-gray-900">
                @if($doc->access_code_set_at && is_object($doc->access_code_set_at))
                  {{ $doc->access_code_set_at->format('d M Y H:i') }}
                @elseif($doc->access_code_set_at && is_string($doc->access_code_set_at))
                  {{ \Carbon\Carbon::parse($doc->access_code_set_at)->format('d M Y H:i') }}
                @else
                  —
                @endif
              </p>
            </div>
          </div>

          <div class="mt-6 flex flex-wrap gap-2">
            <a href="{{ route('pegawai.docs.download', $doc->id) }}" class="px-4 py-2 rounded-md border hover:bg-gray-50 text-sm">Unduh Dokumen</a>
            <a href="{{ route('pegawai.profil') }}" class="px-4 py-2 rounded-md border hover:bg-gray-50 text-sm">Kembali ke Profil</a>
            <a href="{{ route('pegawai.docs.preview', $doc->id) }}" target="_blank" rel="noopener"
               class="px-4 py-2 rounded-md border hover:bg-gray-50 text-sm">Buka di Tab Baru</a>
          </div>
        </div>

        {{-- KANAN (2 kartu sejajar) --}}
        <div class="w-full">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Lihat Kode Akses --}}
            <div class="border rounded-2xl p-4">
              <h3 class="font-semibold text-gray-800">Lihat Kode Akses</h3>
              <p class="text-xs text-gray-500 mt-0.5">Hanya admin atau pemilik dokumen yang dapat melihat kode. Owner wajib memasukkan password akun.</p>

              @if (session('revealed_code'))
                <div class="mt-3">
                  <label class="block text-sm text-gray-600 mb-1">Kode Akses</label>
                  <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ session('revealed_code') }}"
                           class="w-full rounded-md border px-2 py-2 font-mono text-sm select-all">
                    <button type="button" onclick="navigator.clipboard.writeText('{{ session('revealed_code') }}')"
                            class="px-3 py-2 rounded-md border hover:bg-gray-50 text-sm">Copy</button>
                  </div>
                  <p class="text-[11px] text-amber-700 mt-1">Jaga kerahasiaan kode. Semua tampilan kode terekam audit.</p>
                </div>
              @else
                <form action="{{ route('pegawai.docs.reveal', $doc->id) }}" method="POST" class="mt-3 space-y-3">
                  @csrf
                  @if ($isOwner && !$isAdmin)
                    <label class="block">
                      <span class="text-sm font-medium text-gray-700">Password Akun Anda</span>
                      <input type="password" name="password" required
                             class="mt-1.5 w-full rounded-md border px-3 py-2 focus:border-maroon focus:ring-maroon">
                    </label>
                  @endif
                  <button class="px-4 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm">
                    Lihat Kode
                  </button>
                </form>
              @endif
            </div>

            {{-- Deskripsi & Catatan --}}
            <div class="border rounded-2xl p-4">
              <h3 class="font-semibold text-gray-800">Deskripsi & Catatan</h3>
              <ul class="mt-2 text-sm text-gray-700 space-y-1">
                <li><span class="text-gray-500">Judul:</span> <span class="font-medium">{{ $doc->title }}</span></li>
                <li><span class="text-gray-500">Jenis:</span> <span class="font-medium uppercase">{{ $doc->type }}</span></li>
                <li><span class="text-gray-500">Status:</span> <span class="font-medium">{{ $status }}</span></li>
                <li><span class="text-gray-500">Hint:</span> <span class="font-medium">{{ $doc->access_code_hint ?: '—' }}</span></li>
                <li><span class="text-gray-500">Catatan Verifikator:</span> <span class="font-medium">{{ $doc->notes ?: '—' }}</span></li>
              </ul>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>
@endsection
