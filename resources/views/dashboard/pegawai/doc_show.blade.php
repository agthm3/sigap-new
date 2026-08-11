@extends('layouts.app')

@section('title','Detail Dokumen — SIGAP BRIDA')

@section('content')
  @php use Illuminate\Support\Str; @endphp

  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="javascript:history.back()" class="hover:text-maroon transition-colors font-medium">Kembali</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Arsip Dokumen Pribadi</li>
    </ol>
  </nav>

  <section class="max-w-7xl mx-auto px-4 mb-10">
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        
      {{-- Notifikasi --}}
      @if (session('success')) <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div> @endif
      @if (session('warning')) <div class="mb-4 rounded border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('warning') }}</div> @endif
      @if ($errors->any())
        <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
          <ul class="list-disc list-inside">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        </div>
      @endif

      @if(!$isUnlocked)
        {{-- ================= TAMPILAN TERKUNCI (Hanya Untuk Employee Biasa yg Buka Punya Temannya) ================= --}}
        <div class="max-w-md mx-auto py-12 text-center">
            
            @if($doc->access_code_hash)
                {{-- DOKUMEN ADA PIN-NYA --}}
                <div class="mx-auto w-16 h-16 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900">Dokumen Terkunci</h2>
                <p class="text-sm text-gray-500 mt-2">File <b>{{ $doc->title }}</b> dilindungi oleh PIN. Masukkan Kode Akses (PIN) untuk melihat atau mengunduh isinya.</p>
                
                @if($doc->access_code_hint)
                    <p class="mt-4 text-xs font-semibold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg inline-block border border-amber-200">
                        💡 Petunjuk PIN: {{ $doc->access_code_hint }}
                    </p>
                @endif

                <form action="{{ route('pegawai.docs.unlock', $doc->id) }}" method="POST" class="mt-6 flex gap-2">
                    @csrf
                    <input type="password" name="access_code" placeholder="Masukkan PIN..." required
                           class="flex-1 rounded-xl border border-gray-300 px-4 py-3 focus:border-maroon focus:ring-maroon text-center tracking-widest text-lg">
                    <button type="submit" class="px-6 py-3 bg-maroon hover:bg-maroon-800 text-white font-bold rounded-xl transition-colors shadow-sm">Buka</button>
                </form>
            @else
                {{-- DOKUMEN TIDAK ADA PIN-NYA (MUTLAK PRIVAT, EMPLOYEE TIDAK BISA BUKA SAMA SEKALI) --}}
                <div class="mx-auto w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                </div>
                
                <h2 class="text-xl font-bold text-gray-900">Akses Ditolak</h2>
                <p class="text-sm text-gray-500 mt-2">File <b>{{ $doc->title }}</b> ini bersifat sangat privat. Pemilik tidak mengatur PIN akses untuk dibagikan.</p>
                
                <div class="mt-6">
                    <a href="javascript:history.back()" class="px-6 py-3 rounded-xl bg-gray-900 text-white hover:bg-black font-semibold text-sm transition-colors shadow-sm">Kembali</a>
                </div>
            @endif
        </div>

      @else
        {{-- ================= TAMPILAN TERBUKA (Untuk Admin, Pemilik, atau Employee yg sudah ketik PIN benar) ================= --}}
        
        {{-- AREA PREVIEW --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-gray-900 text-lg">Pratinjau Dokumen</h3>
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    Akses Terbuka
                </span>
            </div>
            
            @php $mime = $doc->mime; @endphp
            @if (Str::startsWith($mime ?? '', 'image/'))
                <div class="border rounded-2xl overflow-hidden bg-gray-50 shadow-inner">
                    <img src="{{ route('pegawai.docs.preview', $doc->id) }}" alt="Preview" class="w-full h-auto max-h-[70vh] object-contain">
                </div>
            @elseif (($mime ?? '') === 'application/pdf')
                <div class="border rounded-2xl overflow-hidden bg-gray-50 shadow-inner">
                    <iframe src="{{ route('pegawai.docs.preview', $doc->id) }}#zoom=page-width" class="w-full h-[70vh]"></iframe>
                </div>
            @else
                <div class="rounded-xl border border-gray-200 p-6 bg-gray-50 text-gray-600 text-sm text-center">
                    Format file tidak mendukung pratinjau (Preview). Silakan gunakan tombol unduh di bawah.
                </div>
            @endif
        </div>

        {{-- AREA INFO & TOMBOL --}}
        <div class="grid md:grid-cols-2 gap-6 items-start border-t border-gray-100 pt-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $doc->title }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Jenis Berkas: <span class="font-bold uppercase text-maroon">{{ $doc->type }}</span>
                </p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('pegawai.docs.download', $doc->id) }}" class="px-5 py-2.5 rounded-xl bg-gray-900 text-white hover:bg-black font-semibold text-sm shadow-sm transition-colors">⬇ Unduh Dokumen</a>
                    <a href="javascript:history.back()" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 font-semibold text-sm transition-colors">Kembali</a>
                </div>
            </div>

            {{-- Form Lihat Kode PIN (HANYA BAGI PEMILIK ATAU ADMIN) --}}
            @if($isAdmin || $isOwner)
            <div class="border border-amber-200 bg-amber-50/30 rounded-2xl p-5">
                <h3 class="font-bold text-gray-900">Manajemen PIN (Admin/Pemilik)</h3>
                <p class="text-xs text-gray-500 mt-1">Hanya Anda yang dapat melihat PIN asli dokumen ini.</p>
                
                @if (session('revealed_code'))
                <div class="mt-4">
                    <label class="block text-xs font-bold text-gray-600 mb-1">PIN Asli:</label>
                    <div class="flex gap-2">
                        <input type="text" readonly value="{{ session('revealed_code') }}" class="w-full rounded-lg border-gray-300 font-mono font-bold text-lg px-3 py-2 select-all">
                    </div>
                    <p class="text-[11px] text-amber-700 mt-1">Simpan/Copy PIN di atas.</p>
                </div>
                @else
                <form action="{{ route('pegawai.docs.reveal', $doc->id) }}" method="POST" class="mt-4">
                    @csrf
                    {{-- Owner biasa butuh confirm password akun, Superadmin bebas --}}
                    @if ($isOwner && !$isAdmin)
                    <input type="password" name="password" required placeholder="Verifikasi Password Akun Anda..." class="w-full rounded-lg border-gray-300 px-3 py-2 text-sm mb-2 focus:border-maroon focus:ring-maroon">
                    @endif
                    <button class="w-full py-2 rounded-lg bg-white border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 text-sm">Tampilkan PIN</button>
                </form>
                @endif
            </div>
            @endif
        </div>
      @endif

    </div>
  </section>
@endsection