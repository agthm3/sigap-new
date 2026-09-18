@extends('layouts.app')
@section('content')
<div class="max-w-5xl">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Pilih Kegiatan untuk Narasumber</h1>
      <p class="text-xs text-gray-500 mt-1">Pilih kegiatan untuk melihat/membagikan QR code dan memantau pemateri yang telah mengisi formulir.</p>
    </div>
    <a href="{{ route('sigap-narasumber.index') }}" class="self-start sm:self-auto inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
      &larr; Data Narasumber
    </a>
  </div>

  <!-- Form Pencarian -->
  <form method="GET" class="mb-6 flex gap-2">
    <div class="relative w-full md:w-1/2">
      <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama kegiatan..." class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-300 text-sm focus:ring-maroon focus:border-maroon">
      <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
      </svg>
    </div>
    <button type="submit" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-sm font-semibold transition-colors">
      Cari
    </button>
    @if($q)
      <a href="{{ route('sigap-narasumber.pilih-kegiatan') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm transition-colors flex items-center">
        Reset
      </a>
    @endif
  </form>

  <!-- Grid Kegiatan -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($kegiatans as $kegiatan)
    @php
      $pemateriList = $kegiatan->narasumbers ?? collect();
      $sudahDiisi = $pemateriList->isNotEmpty();
    @endphp
    <div class="border {{ $sudahDiisi ? 'border-emerald-200 bg-white' : 'border-gray-200 bg-white' }} rounded-2xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow relative overflow-hidden">
      
      <!-- Strip Aksen Kiri Atas -->
      <div class="absolute top-0 left-0 right-0 h-1 {{ $sudahDiisi ? 'bg-emerald-500' : 'bg-gray-200' }}"></div>

      <div>
        <!-- Badge Status Pengisian -->
        <div class="flex items-center justify-between gap-2 mb-2 pt-1">
          @if($sudahDiisi)
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
              <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
              Sudah Diisi ({{ $pemateriList->count() }} Pemateri)
            </span>
          @else
            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
              <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              Belum Ada Pemateri
            </span>
          @endif

          @if($kegiatan->tempat)
            <span class="text-[11px] text-gray-500 truncate max-w-[140px] text-right" title="{{ $kegiatan->tempat }}">
              📍 {{ $kegiatan->tempat }}
            </span>
          @endif
        </div>

        <!-- Nama Kegiatan & Waktu -->
        <h3 class="font-bold text-gray-900 text-base leading-snug mb-1">{{ $kegiatan->nama_kegiatan }}</h3>
        <p class="text-xs text-gray-500 mb-3 flex items-center gap-1">
          <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          {{ $kegiatan->hari_tanggal ?? 'Tanggal belum diatur' }}
        </p>

        <!-- Informasi Pemateri yang Sudah Mengisi -->
        @if($sudahDiisi)
          <div class="mt-3 p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs">
            <p class="font-semibold text-gray-700 mb-1.5 flex items-center gap-1">
              <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
              Daftar Pemateri:
            </p>
            <ul class="space-y-1">
              @foreach($pemateriList as $index => $narasumber)
                <li class="flex items-center justify-between text-gray-800 bg-white px-2.5 py-1.5 rounded-lg border border-gray-100">
                  <span class="font-medium truncate mr-2">{{ $index + 1 }}. {{ $narasumber->nama_lengkap }}</span>
                  <a href="{{ route('sigap-narasumber.export-pdf', $narasumber->uuid) }}" 
                     title="Unduh PDF Kesediaan" 
                     class="text-emerald-700 hover:text-emerald-900 text-[11px] font-semibold underline shrink-0">
                    PDF
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>

      <!-- Tombol Aksi QR -->
      <div class="mt-4 pt-3 border-t border-gray-100 flex items-center gap-2">
        <a href="{{ route('sigap-narasumber.qr', $kegiatan->uuid) }}" 
           class="w-full text-center px-4 py-2 bg-maroon text-white text-xs sm:text-sm font-semibold rounded-xl hover:bg-maroon-800 transition-colors shadow-sm flex items-center justify-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
          </svg>
          Lihat QR Code & Link Form
        </a>
      </div>
    </div>
    @empty
    <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-gray-200">
      <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p class="text-sm font-medium text-gray-500">Tidak ada kegiatan yang ditemukan.</p>
    </div>
    @endforelse
  </div>

  <!-- Pagination -->
  <div class="mt-6">{{ $kegiatans->links() }}</div>
</div>
@endsection