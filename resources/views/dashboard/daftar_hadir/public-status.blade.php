@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
  <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 sm:p-8 text-center">

    @if(session('status_type') === 'already_registered')
      <!-- KONDISI NAMA SUDAH TERDAFTAR -->
      <div class="w-20 h-20 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>

      <span class="inline-block px-3 py-1 text-xs font-semibold tracking-wider text-amber-800 uppercase bg-amber-100 rounded-full mb-3">
        Data Sudah Ada
      </span>
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Anda Sudah Terdaftar!</h1>
      
      <p class="text-gray-600 text-sm leading-relaxed mb-6">
        Nama <strong class="text-gray-900 font-semibold">{{ session('peserta_nama') }}</strong> sudah tercatat pada absensi kegiatan ini. Anda tidak perlu melakukan pengisian ulang.
      </p>

    @else
      <!-- KONDISI BERHASIL MENDAFTAR -->
      <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
        </svg>
      </div>

      <span class="inline-block px-3 py-1 text-xs font-semibold tracking-wider text-emerald-800 uppercase bg-emerald-100 rounded-full mb-3">
        Berhasil Tersimpan
      </span>
      <h1 class="text-2xl font-bold text-gray-900 mb-2">Kehadiran Berhasil Dicatat!</h1>

      <p class="text-gray-600 text-sm leading-relaxed mb-6">
        Terima kasih, <strong class="text-gray-900 font-semibold">{{ session('peserta_nama') }}</strong>. Data kehadiran Anda telah tersimpan ke dalam sistem.
      </p>
    @endif

    <!-- DETAIL INFORMASI KEGIATAN -->
    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 text-left mb-6 space-y-2">
      <div class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Kegiatan</div>
      <div class="text-sm font-bold text-gray-800 leading-snug">{{ $kegiatan->nama_kegiatan }}</div>
      <div class="text-xs text-gray-600 pt-1 border-t border-gray-200">
        {{ $kegiatan->hari_tanggal }} &bull; {{ $kegiatan->tempat }}
      </div>
    </div>

    <!-- TOMBOL AKSI -->
    <div class="space-y-3">
      <a href="{{ route('sigap-daftar-hadir.public', $kegiatan->uuid) }}" 
         class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-maroon text-white font-medium hover:bg-maroon-800 transition shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
        </svg>
        Kembali ke Daftar Hadir
      </a>
      <p class="text-xs text-gray-400">Gunakan tombol di atas jika Anda ingin mengisi kehadiran untuk nama/peserta lain.</p>
    </div>

  </div>
</div>
@endsection