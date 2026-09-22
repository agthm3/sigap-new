@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Detail Riwayat <span class="text-maroon">KGB</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Rincian kalkulasi kenaikan gaji berkala pegawai <b>{{ $riwayat->user->name ?? '' }}</b>.
    </p>
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('sigap-kgb.export-pdf', $riwayat->id) }}" target="_blank"
       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 shadow-sm transition">
      Export PDF Usulan
    </a>
    <a href="{{ route('sigap-kgb.index') }}"
       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
      &larr; Kembali
    </a>
  </div>
</section>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
  {{-- DATA LAMA --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-3 text-xs">
    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
      <h3 class="font-bold text-gray-900 text-sm">Data SK Sebelumnya</h3>
      <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 font-semibold">Lama</span>
    </div>
    <div class="flex justify-between"><span class="text-gray-500">Nomor SK:</span><span class="font-semibold text-gray-900">{{ $riwayat->nomor_sk_lama }}</span></div>
    <div class="flex justify-between"><span class="text-gray-500">Tanggal SK:</span><span class="font-semibold text-gray-900">{{ $riwayat->tanggal_sk_lama ? $riwayat->tanggal_sk_lama->translatedFormat('d F Y') : '-' }}</span></div>
    <div class="flex justify-between"><span class="text-gray-500">TMT Berlaku:</span><span class="font-semibold text-gray-900">{{ $riwayat->tmt_lama ? $riwayat->tmt_lama->translatedFormat('d F Y') : '-' }}</span></div>
    <div class="flex justify-between"><span class="text-gray-500">Masa Kerja:</span><span class="font-semibold text-gray-900">{{ $riwayat->mkg_tahun_lama }} Tahun {{ $riwayat->mkg_bulan_lama }} Bulan</span></div>
    <div class="flex justify-between items-center pt-2 border-t"><span class="text-gray-500">Gaji Pokok:</span><span class="text-base font-bold text-gray-800">Rp {{ number_format($riwayat->gaji_pokok_lama, 0, ',', '.') }}</span></div>
    @if($riwayat->file_sk_lama)
      <div class="pt-2">
        <a href="{{ asset('storage/'.$riwayat->file_sk_lama) }}" target="_blank" class="text-maroon underline font-medium">Lihat Lampiran Scan SK Lama &rarr;</a>
      </div>
    @endif
  </div>

  {{-- DATA BARU --}}
  <div class="rounded-2xl border border-maroon/20 bg-maroon-50/20 p-5 shadow-sm space-y-3 text-xs">
    <div class="flex justify-between items-center border-b border-maroon/10 pb-3">
      <h3 class="font-bold text-maroon text-sm">Hasil Kalkulasi KGB Baru</h3>
      <span class="px-2 py-0.5 rounded-md bg-maroon text-white font-semibold">+2 Tahun</span>
    </div>
    <div class="flex justify-between"><span class="text-gray-500">TMT Baru:</span><span class="font-bold text-maroon">{{ $riwayat->tmt_baru ? $riwayat->tmt_baru->translatedFormat('d F Y') : '-' }}</span></div>
    <div class="flex justify-between"><span class="text-gray-500">Masa Kerja Baru:</span><span class="font-bold text-emerald-700">{{ $riwayat->mkg_tahun_baru }} Tahun {{ $riwayat->mkg_bulan_baru }} Bulan</span></div>
    <div class="flex justify-between"><span class="text-gray-500">Status Pengusulan:</span><span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $riwayat->badge_status['color'] }}">{{ $riwayat->badge_status['label'] }}</span></div>
    <div class="flex justify-between items-center pt-2 border-t border-maroon/10"><span class="text-gray-500">Gaji Pokok Baru:</span><span class="text-base font-extrabold text-maroon">Rp {{ number_format($riwayat->gaji_pokok_baru, 0, ',', '.') }}</span></div>
  </div>
</div>
@endsection