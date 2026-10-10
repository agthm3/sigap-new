@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
  <div class="rounded-3xl border border-amber-200 bg-linear-to-b from-amber-50/80 to-white p-8 md:p-12 shadow-sm text-center space-y-6">
    
    {{-- Ikon Ilustrasi --}}
    <div class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 mx-auto shadow-inner">
      <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
    </div>

    {{-- Judul & Deskripsi Edukatif --}}
    <div class="space-y-3 max-w-2xl mx-auto">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-200/80 text-amber-900 uppercase tracking-wider">
        Syarat Kelayakan Pengusulan SIIPID
      </span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">
        Belum Memiliki Inovasi yang Terdaftar
      </h1>
      <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
        Sesuai <strong>Peraturan Wali Kota Makassar tentang Tata Cara Pemberian Penghargaan dan/atau Insentif Inovasi Daerah</strong>, untuk mencatatkan rekam jejak prestasi di <strong>SIGAP SIIPID</strong>, Anda wajib memiliki minimal 1 (satu) Inovasi Daerah yang telah terdaftar di database BRIDA.
      </p>
    </div>

    {{-- Kartu Pilihan Mendaftarkan Inovasi --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left max-w-2xl mx-auto pt-2">
      
      {{-- Opsi 1: SIGAP Inovasi (OPD / ASN) --}}
      <a href="{{ route('sigap-inovasi.index') }}" 
         class="group p-5 rounded-2xl border border-gray-200 bg-white hover:border-maroon/40 hover:shadow-md transition-all flex flex-col justify-between">
        <div class="space-y-2">
          <div class="w-8 h-8 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center font-bold">
            🏢
          </div>
          <h3 class="font-bold text-gray-900 group-hover:text-maroon transition-colors text-sm">
            Inovasi Perangkat Daerah (OPD)
          </h3>
          <p class="text-xs text-gray-500">
            Daftarkan inovasi unit kerja/dinas Anda melalui modul <strong>SIGAP Inovasi</strong>.
          </p>
        </div>
        <div class="mt-4 flex items-center text-xs font-bold text-maroon gap-1">
          <span>Buka SIGAP Inovasi</span>
          <span>→</span>
        </div>
      </a>

      {{-- Opsi 2: SIGAP IMA (Masyarakat / Umum) --}}
      <a href="{{ route('sigap-ima.index') }}" 
         class="group p-5 rounded-2xl border border-gray-200 bg-white hover:border-amber-400 hover:shadow-md transition-all flex flex-col justify-between">
        <div class="space-y-2">
          <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center font-bold">
            💡
          </div>
          <h3 class="font-bold text-gray-900 group-hover:text-amber-700 transition-colors text-sm">
            Inovasi Masyarakat (IMA)
          </h3>
          <p class="text-xs text-gray-500">
            Daftarkan karya inovasi perorangan / kelompok masyarakat melalui <strong>SIGAP IMA</strong>.
          </p>
        </div>
        <div class="mt-4 flex items-center text-xs font-bold text-amber-700 gap-1">
          <span>Buka SIGAP IMA</span>
          <span>→</span>
        </div>
      </a>

    </div>

    {{-- Navigasi Kembali --}}
    <div class="pt-4 border-t border-amber-200/60 max-w-md mx-auto">
      <a href="{{ route('home.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition">
        ← Kembali ke Dashboard Utama
      </a>
    </div>

  </div>
</div>
@endsection
