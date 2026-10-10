@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header Meja Review --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
          ⚖️ MEJA REVIEWER SIIPID
        </span>
        <span class="text-xs text-gray-400">•</span>
        <span class="text-xs text-gray-500 font-medium">Verifikasi Penghargaan & Insentif</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-1">
        Monitoring & Verifikasi Usulan Prestasi
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
        Pemeriksaan kelengkapan administrasi, validasi penerapan inovasi, dan perumusan rekomendasi penghargaan.
      </p>
    </div>
  </div>

  {{-- Statistik Status Antrian --}}
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
    <a href="{{ route('sigap-siipid.verifikator.index', ['status' => 'diajukan']) }}" 
       class="bg-white p-3.5 rounded-2xl border {{ $statusFilter === 'diajukan' ? 'border-blue-500 ring-2 ring-blue-500/20' : 'border-gray-200/80' }} shadow-2xs hover:border-blue-400 transition">
      <span class="text-[11px] font-bold text-blue-600 block">Menunggu Review</span>
      <p class="text-xl font-extrabold text-blue-700 mt-1">{{ $countDiajukan }}</p>
      <span class="text-[10px] text-gray-400">Usulan baru masuk</span>
    </a>

    <a href="{{ route('sigap-siipid.verifikator.index', ['status' => 'dikembalikan_perbaikan']) }}" 
       class="bg-white p-3.5 rounded-2xl border {{ $statusFilter === 'dikembalikan_perbaikan' ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-gray-200/80' }} shadow-2xs hover:border-amber-400 transition">
      <span class="text-[11px] font-bold text-amber-600 block">Perbaikan Peserta</span>
      <p class="text-xl font-extrabold text-amber-700 mt-1">{{ $countPerbaikan }}</p>
      <span class="text-[10px] text-gray-400">Dikembalikan ke inovator</span>
    </a>

    <a href="{{ route('sigap-siipid.verifikator.index', ['status' => 'direkomendasikan']) }}" 
       class="bg-white p-3.5 rounded-2xl border {{ $statusFilter === 'direkomendasikan' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-200/80' }} shadow-2xs hover:border-emerald-400 transition">
      <span class="text-[11px] font-bold text-emerald-600 block">Direkomendasikan</span>
      <p class="text-xl font-extrabold text-emerald-700 mt-1">{{ $countDirekomendasikan }}</p>
      <span class="text-[10px] text-gray-400">Lolos telaah reviewer</span>
    </a>

    <a href="{{ route('sigap-siipid.verifikator.index', ['status' => 'ditetapkan_sk']) }}" 
       class="bg-white p-3.5 rounded-2xl border {{ $statusFilter === 'ditetapkan_sk' ? 'border-maroon ring-2 ring-maroon/20' : 'border-gray-200/80' }} shadow-2xs hover:border-maroon transition">
      <span class="text-[11px] font-bold text-maroon block">Ditetapkan SK</span>
      <p class="text-xl font-extrabold text-maroon mt-1">{{ $countDitetapkan }}</p>
      <span class="text-[10px] text-gray-400">Keputusan Wali Kota</span>
    </a>

    <a href="{{ route('sigap-siipid.verifikator.index', ['status' => 'ditolak']) }}" 
       class="bg-white p-3.5 rounded-2xl border {{ $statusFilter === 'ditolak' ? 'border-red-500 ring-2 ring-red-500/20' : 'border-gray-200/80' }} shadow-2xs hover:border-red-400 transition">
      <span class="text-[11px] font-bold text-red-600 block">Ditolak</span>
      <p class="text-xl font-extrabold text-red-700 mt-1">{{ $countDitolak }}</p>
      <span class="text-[10px] text-gray-400">Tidak memenuhi syarat</span>
    </a>
  </div>

  {{-- Tabel Usulan untuk Reviewer --}}
  <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h3 class="font-bold text-gray-900 text-sm sm:text-base">Antrian Usulan Masuk</h3>
        <p class="text-xs text-gray-500">Klik "Periksa & Review" untuk melakukan telaah berkas dan menentukan status.</p>
      </div>

      {{-- Search Filter --}}
      <form action="{{ route('sigap-siipid.verifikator.index') }}" method="GET" class="flex items-center gap-2">
        @if($statusFilter)
          <input type="hidden" name="status" value="{{ $statusFilter }}">
        @endif
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari inovator / prestasi..." class="rounded-xl border-gray-300 text-xs py-1.5 focus:ring-maroon focus:border-maroon">
        <button type="submit" class="px-3 py-1.5 bg-gray-800 text-white rounded-xl text-xs font-semibold hover:bg-gray-900 transition">
          Cari
        </button>
        @if(request('q') || $statusFilter)
          <a href="{{ route('sigap-siipid.verifikator.index') }}" class="text-xs text-gray-500 hover:text-gray-800 px-1">Reset</a>
        @endif
      </form>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50/80 text-gray-600 font-bold border-b border-gray-200/60 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-4">Nama Inovator & Sasaran</th>
            <th class="py-3 px-4">Prestasi & Inovasi</th>
            <th class="py-3 px-4">Tingkat & Tahun</th>
            <th class="py-3 px-4">Status Usulan</th>
            <th class="py-3 px-4 text-center">Aksi Review</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($prestasis as $item)
            @php $badge = $item->status_badge; @endphp
            <tr class="hover:bg-gray-50/60 transition-colors">
              <td class="py-3.5 px-4">
                <div class="font-bold text-gray-900 text-sm leading-snug">{{ $item->nama_inovator }}</div>
                <div class="text-gray-500 text-[11px] mt-0.5">
                  <span class="uppercase font-semibold text-gray-700">[{{ $item->kategori_sasaran }}]</span>
                  {{ $item->opd_instansi ?: 'Masyarakat Kota Makassar' }}
                </div>
                <div class="text-gray-400 text-[10px]">WA: {{ $item->no_wa }}</div>
              </td>

              <td class="py-3.5 px-4">
                <div class="font-bold text-gray-900 leading-snug">{{ $item->nama_prestasi }}</div>
                <div class="text-gray-500 text-[11px]">{{ $item->ajang_kompetisi }}</div>
                <div class="text-maroon font-semibold text-[11px] mt-0.5">
                  💡 Inovasi: {{ $item->innovable->judul ?? '—' }}
                </div>
              </td>

              <td class="py-3.5 px-4">
                <span class="font-semibold text-gray-800">{{ $item->tingkat_label }}</span>
                <div class="text-gray-400 text-[11px]">Tahun {{ $item->tahun_perolehan }}</div>
              </td>

              <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                  {{ $badge['label'] }}
                </span>
                @if($item->reviewer)
                  <div class="text-[10px] text-gray-400 mt-1">Oleh: {{ $item->reviewer->name }}</div>
                @endif
              </td>

              <td class="py-3.5 px-4 text-center">
                <a href="{{ route('sigap-siipid.verifikator.review', $item->uuid) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-2xs transition">
                  <span>Periksa & Review</span>
                  <span>→</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-12 text-center text-gray-400">
                <p class="font-semibold text-gray-600">Tidak ada usulan dalam kategori ini.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($prestasis->hasPages())
      <div class="p-4 border-t border-gray-100">
        {{ $prestasis->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
