@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">Detail Surat Masuk</h1>
      <p class="text-xs text-gray-500">Agenda No. {{ sprintf('%03d', $surat->nomor_agenda) }} / Tahun {{ $surat->tahun }}</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-surat.masuk.disposisi', $surat->id) }}" target="_blank"
         class="px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-bold hover:bg-amber-600 shadow-2xs transition">
        🖨️ Cetak Lembar Disposisi
      </a>
      <a href="{{ route('sigap-surat.masuk.index') }}" class="px-3 py-1.5 rounded-lg border text-xs text-gray-600 hover:bg-gray-50">
        ← Kembali
      </a>
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4 text-xs">
    <div class="grid grid-cols-2 gap-4 pb-3 border-b">
      <div>
        <p class="text-gray-400">Surat Dari (Pengirim)</p>
        <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $surat->asal_surat }}</p>
      </div>
      <div>
        <p class="text-gray-400">Tingkat Surat</p>
        <p class="font-bold text-maroon mt-0.5">{{ $surat->tingkat_surat }}</p>
      </div>
      <div>
        <p class="text-gray-400">Nomor Surat Masuk</p>
        <p class="font-mono font-semibold text-gray-800 mt-0.5">{{ $surat->nomor_surat_masuk }}</p>
      </div>
      <div>
        <p class="text-gray-400">Tanggal Surat Fisik</p>
        <p class="font-semibold text-gray-800 mt-0.5">{{ $surat->tanggal_surat->format('d/m/Y') }}</p>
      </div>
      <div>
        <p class="text-gray-400">Diterima Tanggal</p>
        <p class="font-semibold text-gray-800 mt-0.5">{{ $surat->tanggal_terima->format('d/m/Y') }}</p>
      </div>
      <div>
        <p class="text-gray-400">Unit Pengolah</p>
        <p class="font-semibold text-gray-800 mt-0.5">{{ $surat->unit_pengolah }}</p>
      </div>
    </div>

    <div>
      <p class="text-gray-400 mb-1">Perihal</p>
      <p class="font-normal text-gray-800 leading-relaxed text-sm bg-gray-50 p-3 rounded-xl border border-gray-100">
        {{ $surat->perihal }}
      </p>
    </div>

    <div class="pt-3 border-t flex items-center justify-between">
      <div>
        <p class="text-gray-400 mb-1">Diterima Oleh:</p>
        <p class="font-bold text-gray-800">{{ $surat->penerima->name ?? '-' }}</p>
      </div>

      @if($surat->ttd_penerima)
        <div class="text-right">
          <p class="text-gray-400 mb-1">Tanda Tangan Penerima:</p>
          <img src="{{ $surat->ttd_penerima }}" alt="TTD" class="h-14 max-w-[120px] object-contain border border-gray-200 rounded-lg p-1 bg-white inline-block">
        </div>
      @endif
    </div>

    @if($surat->file_surat)
      <div class="pt-3 border-t">
        <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-maroon font-semibold hover:underline">
          📄 Buka Berkas Scan PDF Surat Masuk
        </a>
      </div>
    @endif
  </div>
</div>
@endsection