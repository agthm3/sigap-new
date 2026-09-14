@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">Detail Surat Keluar</h1>
      <p class="text-xs text-gray-500">Informasi lengkap pencatatan nomor di buku agenda.</p>
    </div>
    <a href="{{ route('sigap-surat.keluar.index') }}" class="px-3 py-1.5 rounded-lg border text-xs text-gray-600 hover:bg-gray-50">
      ← Kembali
    </a>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4">
    <div class="p-4 rounded-xl bg-maroon-50/60 border border-maroon/20 flex items-center justify-between">
      <div>
        <p class="text-xs text-gray-500">Nomor Surat Lengkap</p>
        <p class="text-lg font-extrabold text-maroon font-mono">{{ $surat->nomor_surat_lengkap }}</p>
      </div>
      <button onclick="navigator.clipboard.writeText('{{ $surat->nomor_surat_lengkap }}'); Swal.fire({title: 'Tersalin!', text: 'Nomor surat berhasil disalin', icon: 'success', timer: 1200, showConfirmButton: false});"
              class="px-3 py-1.5 rounded-lg bg-maroon text-white text-xs font-semibold hover:bg-maroon-800">
        Salin
      </button>
    </div>

    <div class="grid grid-cols-2 gap-4 text-xs pt-2">
      <div>
        <p class="text-gray-400">Nomor Urut Buku</p>
        <p class="text-base font-bold text-gray-800">{{ $surat->nomor_urut }}</p>
      </div>
      <div>
        <p class="text-gray-400">Tanggal Surat</p>
        <p class="text-base font-bold text-gray-800">{{ $surat->tanggal->format('d/m/Y') }}</p>
      </div>
      <div>
        <p class="text-gray-400">Kode Berkas / Klasifikasi</p>
        <p class="font-semibold text-gray-800">{{ $surat->nomor_berkas }}</p>
      </div>
      <div>
        <p class="text-gray-400">Pembuat / Akun Pengambil</p>
        <p class="font-semibold text-gray-800">{{ $surat->creator->name ?? '-' }}</p>
      </div>
    </div>

    <div class="border-t pt-3 text-xs">
      <p class="text-gray-400 mb-1">Alamat Penerima</p>
      <p class="font-semibold text-gray-800">{{ $surat->alamat_penerima }}</p>
    </div>

    <div class="border-t pt-3 text-xs">
      <p class="text-gray-400 mb-1">Perihal</p>
      <p class="font-normal text-gray-800 leading-relaxed">{{ $surat->perihal }}</p>
    </div>

    @if($surat->file_surat)
      <div class="border-t pt-3 text-xs">
        <p class="text-gray-400 mb-1">Pindaian Fisik (PDF)</p>
        <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank"
           class="inline-flex items-center gap-1.5 text-maroon font-semibold hover:underline">
          📄 Buka Dokumen PDF
        </a>
      </div>
    @endif
  </div>
</div>
@endsection