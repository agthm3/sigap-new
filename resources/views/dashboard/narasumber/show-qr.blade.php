@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto text-center">
  <div class="bg-white p-6 rounded-2xl shadow-sm border">
    <h2 class="font-bold text-lg text-gray-900">QR Code Form Kesediaan Narasumber</h2>
    <p class="text-sm text-gray-500 mt-1 mb-6">{{ $kegiatan->nama_kegiatan }}</p>
    
    <div class="inline-block p-4 rounded-xl border bg-gray-50">
      {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(220)->margin(1)->generate($qrUrl) !!}
    </div>
    <p class="text-xs text-blue-600 mt-4 break-all bg-blue-50 p-2 rounded-lg"><a href="{{ $qrUrl }}" target="_blank">{{ $qrUrl }}</a></p>
    
    <div class="mt-6 flex justify-center gap-3">
      <a href="{{ route('sigap-narasumber.pilih-kegiatan') }}" class="px-4 py-2 rounded-xl border text-sm hover:bg-gray-50">Kembali</a>
    </div>
  </div>
</div>
@endsection