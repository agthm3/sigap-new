@extends('layouts.app')

@section('content')
<div class="mb-6">
  <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('sigap-spj.index') }}" class="hover:text-maroon">SIGAP SPJ</a>
    <span>/</span><span class="text-gray-900 font-medium">Pengisian Berkas</span>
  </div>
  <h1 class="text-2xl font-extrabold text-gray-900">{{ $subKegiatan->nama_sub_kegiatan }}</h1>
  <p class="text-sm text-gray-600 mt-1">Bidang: <span class="font-semibold">{{ $subKegiatan->bidang->nama_bidang }}</span></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  
  <div class="lg:col-span-1">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
      <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Kerangka Acuan Kerja (KAK)</h3>
      
      @if($subKegiatan->file_kak)
        <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-200 rounded-lg mb-4">
          <a href="{{ asset('storage/' . $subKegiatan->file_kak) }}" target="_blank" class="text-sm text-emerald-700 font-medium hover:underline">Lihat KAK Saat Ini</a>
        </div>
      @endif

      <form action="{{ route('sigap-spj.upload.kak', $subKegiatan->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="block text-xs font-medium text-gray-700 mb-1">Unggah/Ganti File KAK (PDF)</label>
        <input type="file" name="file_kak" required accept="application/pdf" class="w-full text-xs border rounded p-1 mb-3">
        <button type="submit" class="w-full py-2 bg-gray-800 text-white text-xs font-semibold rounded-lg hover:bg-gray-900">Simpan KAK</button>
      </form>
    </div>
  </div>

  <div class="lg:col-span-2 space-y-4">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
      <h3 class="font-semibold text-gray-900 mb-4">Daftar Kegiatan</h3>

      @foreach($subKegiatan->kegiatans as $keg)
      <div class="border border-gray-200 rounded-xl mb-4 overflow-hidden">
        <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
          <h4 class="font-semibold text-gray-800 text-sm">{{ $keg->nama_kegiatan }}</h4>
        </div>
        <div class="p-4">
          
          <form action="{{ route('sigap-spj.upload.sk', $keg->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b pb-4 mb-4">
            @csrf
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">SK Panitia Pelaksana</label>
              @if($keg->file_sk_panpel) <a href="{{ asset('storage/'.$keg->file_sk_panpel) }}" target="_blank" class="text-xs text-emerald-600 block mb-1">Lihat SK Tersimpan</a> @endif
              <input type="file" name="file_sk_panpel" accept="application/pdf" class="text-xs border rounded p-1 w-full">
            </div>
            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">SK Tenaga Ahli</label>
              @if($keg->file_sk_tenaga_ahli) <a href="{{ asset('storage/'.$keg->file_sk_tenaga_ahli) }}" target="_blank" class="text-xs text-emerald-600 block mb-1">Lihat SK Tersimpan</a> @endif
              <input type="file" name="file_sk_tenaga_ahli" accept="application/pdf" class="text-xs border rounded p-1 w-full">
            </div>
            <div class="md:col-span-2 text-right">
              <button type="submit" class="px-3 py-1.5 bg-maroon text-white text-xs rounded hover:bg-maroon-800">Simpan SK Kegiatan</button>
            </div>
          </form>

          <h5 class="text-xs font-bold text-gray-700 uppercase mb-2">Daftar Gelombang / Angkatan</h5>
          <div class="space-y-2">
            @foreach($keg->gelombangs as $gel)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-lg border border-gray-200 hover:border-maroon/50 bg-white gap-2">
              <div>
                <span class="font-semibold text-sm text-gray-800">{{ $gel->nama_gelombang }}</span>
                <div class="text-[11px] text-gray-500 mt-1 space-y-0.5 leading-tight">
                  <div>📅 {{ \Carbon\Carbon::parse($gel->tanggal)->translatedFormat('l, d F Y') }}</div>
                  <div>⏰ {{ $gel->waktu }}</div>
                  <div>📍 {{ $gel->tempat }}</div>
                </div>
              </div>
              <div class="flex items-center">
                <a href="{{ route('sigap-spj.gelombang.berkas', $gel->id) }}" class="px-3 py-1.5 text-xs font-semibold rounded bg-gray-100 text-gray-700 hover:bg-maroon hover:text-white border border-gray-200 transition-colors whitespace-nowrap">
                  Kelola 10 Berkas
                </a>
              </div>
            </div>
            @endforeach
          </div>

        </div>
      </div>
      @endforeach

    </div>
  </div>
</div>
@endsection