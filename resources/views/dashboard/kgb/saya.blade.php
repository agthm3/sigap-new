@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      KGB <span class="text-maroon">Saya</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Informasi jadwal berkala, estimasi gaji pokok, dan pengajuan kenaikan gaji berkala pribadi.
    </p>
  </div>

  @if($kgbAktif && $kgbAktif->sisa_hari <= 60)
    <a href="{{ route('sigap-kgb.export-pdf', $kgbAktif->id) }}" target="_blank"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 shadow-sm transition">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
      </svg>
      Cetak Berkas Usulan KGB
    </a>
  @endif
</section>

@if(!$kgbAktif)
  {{-- JIKA BELUM ADA DATA SK KGB SAMA SEKALI --}}
  <div class="mt-6 rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center">
    <div class="w-12 h-12 rounded-full bg-maroon-50 text-maroon flex items-center justify-center mx-auto mb-3">
      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
    </div>
    <h3 class="text-base font-bold text-gray-900">Data SK KGB Anda Belum Terdaftar</h3>
    <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
      Data SK KGB terakhir Anda belum tercatat di sistem. Silakan laporkan atau serahkan arsip SK KGB terakhir Anda ke bagian kepegawaian BRIDA untuk diinputkan ke sistem.
    </p>
  </div>
@else
  @php
    $badge = $kgbAktif->badge_status;
    $sisaHari = $kgbAktif->sisa_hari;
  @endphp

  {{-- BANNER HERO COUNTDOWN --}}
  <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $badge['color'] }}">
          {{ $badge['label'] }} — {{ $badge['keterangan'] }}
        </span>
        <h2 class="text-2xl font-extrabold text-gray-900 mt-2">
          TMT KGB Berikutnya: <span class="text-maroon">{{ $kgbAktif->tmt_baru ? $kgbAktif->tmt_baru->translatedFormat('d F Y') : '-' }}</span>
        </h2>
        <p class="text-xs text-gray-500 mt-1">
          Dihitung otomatis 2 tahun sejak SK KGB terakhir bertanggal {{ $kgbAktif->tmt_lama ? $kgbAktif->tmt_lama->translatedFormat('d F Y') : '-' }}.
        </p>
      </div>

      {{-- COUNTDOWN DIGIT CARD --}}
      <div class="flex items-center gap-3 bg-gray-50 border border-gray-200/80 rounded-2xl p-4 shrink-0">
        <div class="text-center min-w-[70px]">
          <span class="block text-3xl font-extrabold {{ $sisaHari <= 30 ? 'text-red-600' : ($sisaHari <= 60 ? 'text-amber-600' : 'text-gray-900') }}">
            {{ max(0, $sisaHari) }}
          </span>
          <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Hari Lagi</span>
        </div>
        <div class="h-10 w-[1px] bg-gray-200"></div>
        <div class="text-xs text-gray-600 leading-relaxed">
          <p class="font-semibold text-gray-900">Kesiapan Berkas</p>
          <p class="text-[11px] text-gray-500">
            {{ $sisaHari <= 60 ? 'Sudah dapat mengajukan usulan' : 'Belum masuk periode usul' }}
          </p>
        </div>
      </div>
    </div>
  </div>

  {{-- KOMPARASI GAJI LAMA VS PROYEKSI BARU --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
    {{-- Card Ketetapan SK Lama --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between border-b border-gray-100 pb-3">
        <h3 class="font-bold text-gray-800 text-sm">Ketetapan SK Terakhir (Lama)</h3>
        <span class="text-[11px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md font-medium">SK Lama</span>
      </div>
      <div class="mt-4 space-y-3 text-xs">
        <div class="flex justify-between">
          <span class="text-gray-500">Nomor SK:</span>
          <span class="font-semibold text-gray-900">{{ $kgbAktif->nomor_sk_lama }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Golongan / Pangkat:</span>
          <span class="font-semibold text-gray-900">{{ $kgbAktif->pangkat_golongan }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Masa Kerja Golongan:</span>
          <span class="font-semibold text-gray-900">{{ $kgbAktif->mkg_tahun_lama }} Tahun {{ $kgbAktif->mkg_bulan_lama }} Bulan</span>
        </div>
        <div class="flex justify-between items-center pt-2 border-t border-gray-50">
          <span class="text-gray-500">Gaji Pokok Lama:</span>
          <span class="text-base font-bold text-gray-700">Rp {{ number_format($kgbAktif->gaji_pokok_lama, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>

    {{-- Card Proyeksi KGB Baru --}}
    <div class="rounded-2xl border border-maroon/20 bg-maroon-50/30 p-5 shadow-sm relative overflow-hidden">
      <div class="flex items-center justify-between border-b border-maroon/10 pb-3">
        <h3 class="font-bold text-maroon text-sm">Proyeksi KGB Berikutnya (Baru)</h3>
        <span class="text-[11px] bg-maroon text-white px-2 py-0.5 rounded-md font-medium">+2 Tahun</span>
      </div>
      <div class="mt-4 space-y-3 text-xs">
        <div class="flex justify-between">
          <span class="text-gray-500">TMT Baru:</span>
          <span class="font-bold text-maroon">{{ $kgbAktif->tmt_baru ? $kgbAktif->tmt_baru->translatedFormat('d F Y') : '-' }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Golongan / Pangkat:</span>
          <span class="font-semibold text-gray-900">{{ $kgbAktif->pangkat_golongan }}</span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-500">Masa Kerja Baru:</span>
          <span class="font-bold text-emerald-700">{{ $kgbAktif->mkg_tahun_baru }} Tahun {{ $kgbAktif->mkg_bulan_baru }} Bulan</span>
        </div>
        <div class="flex justify-between items-center pt-2 border-t border-maroon/10">
          <span class="text-gray-500">Estimasi Gaji Pokok Baru:</span>
          <span class="text-lg font-extrabold text-maroon">Rp {{ number_format($kgbAktif->gaji_pokok_baru, 0, ',', '.') }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- RIWAYAT & ARSIP KGB PRIBADI --}}
  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-6">
    <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
      <h3 class="font-semibold text-gray-900 text-sm">Riwayat & Arsip Kenaikan Gaji Berkala Saya</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-xs">
        <thead class="bg-gray-50 uppercase text-gray-500 font-semibold border-b">
          <tr>
            <th class="px-4 py-3 text-left">TMT Baru</th>
            <th class="px-4 py-3 text-left">Masa Kerja</th>
            <th class="px-4 py-3 text-left">Gaji Pokok Baru</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Dokumen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
          @if(isset($riwayats) && count($riwayats) > 0)
            @php foreach($riwayats as $row): @endphp
              @php $rowBadge = $row->badge_status; @endphp
              <tr>
                <td class="px-4 py-3 font-semibold text-gray-900">
                  {{ $row->tmt_baru ? $row->tmt_baru->translatedFormat('d F Y') : '-' }}
                </td>
                <td class="px-4 py-3">{{ $row->mkg_tahun_baru }} Tahun {{ $row->mkg_bulan_baru > 0 ? $row->mkg_bulan_baru . ' Bln' : '' }}</td>
                <td class="px-4 py-3 font-bold text-maroon">Rp {{ number_format($row->gaji_pokok_baru, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $rowBadge['color'] }}">
                    {{ $rowBadge['label'] }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <a href="{{ route('sigap-kgb.export-pdf', $row->id) }}" target="_blank"
                     class="px-2.5 py-1 rounded border border-maroon text-maroon hover:bg-maroon hover:text-white transition font-medium">
                    Cetak Form Usulan
                  </a>
                </td>
              </tr>
            @php endforeach; @endphp
          @else
            <tr>
              <td colspan="5" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat KGB tercatat.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
@endif
@endsection