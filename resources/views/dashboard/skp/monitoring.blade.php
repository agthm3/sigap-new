@extends('layouts.app')

@section('content')
<div x-data="{ activeTab: 'belum' }" class="space-y-6">

  {{-- Header & Title --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b pb-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        Monitoring <span class="text-maroon">Ketaatan Kumpulan SKP</span>
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">
        Pegawai dianggap <b>Sudah Mengisi</b> apabila telah membuat dan mempublikasikan Kumpulan SKP Kategori di bulan tersebut.
      </p>
    </div>

    {{-- Filter Bulan --}}
    <form method="GET" action="{{ route('sigap-skp.monitoring') }}" class="flex items-center gap-2">
      <input type="month" name="bulan" value="{{ $bulanTahun }}" class="text-xs rounded-xl border-gray-300 p-2.5 font-bold text-gray-800 focus:ring-maroon focus:border-maroon">
      <button type="submit" class="px-4 py-2.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800 transition-colors shadow">
        🔍 Filter
      </button>
    </form>
  </div>

  {{-- Stats Summary Widget --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="bg-white rounded-2xl border p-4 shadow-sm border-gray-200">
      <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pegawai</p>
      <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalPegawai }} <span class="text-xs font-normal text-gray-500">Orang</span></h3>
    </div>
    
    <div class="bg-emerald-50 rounded-2xl border border-emerald-200 p-4 shadow-sm">
      <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Sudah Buat Kumpulan SKP</p>
      <h3 class="text-2xl font-extrabold text-emerald-900 mt-1">{{ $totalSudah }} <span class="text-xs font-semibold text-emerald-700">Orang</span></h3>
    </div>

    <div class="bg-rose-50 rounded-2xl border border-rose-200 p-4 shadow-sm">
      <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Belum Buat Kumpulan SKP</p>
      <h3 class="text-2xl font-extrabold text-rose-900 mt-1">{{ $totalBelum }} <span class="text-xs font-semibold text-rose-700">Orang</span></h3>
    </div>
  </div>

  {{-- Tab Navigation --}}
  <div class="flex border-b border-gray-200 gap-4">
    <button type="button" @click="activeTab = 'belum'"
            class="pb-3 text-xs font-bold border-b-2 transition-all flex items-center gap-2"
            :class="activeTab === 'belum' ? 'border-rose-600 text-rose-600' : 'border-transparent text-gray-400 hover:text-gray-600'">
      <span>⚠️ Belum Membuat Kumpulan</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800">{{ $totalBelum }}</span>
    </button>

    <button type="button" @click="activeTab = 'sudah'"
            class="pb-3 text-xs font-bold border-b-2 transition-all flex items-center gap-2"
            :class="activeTab === 'sudah' ? 'border-emerald-600 text-emerald-600' : 'border-transparent text-gray-400 hover:text-gray-600'">
      <span>✅ Sudah Membuat Kumpulan</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800">{{ $totalSudah }}</span>
    </button>
  </div>

  {{-- TAB 1: BELUM MEMBUAT KUMPULAN --}}
  <div x-show="activeTab === 'belum'" class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-4 py-3 bg-rose-50/50 border-b border-rose-100">
      <h3 class="text-xs font-bold text-rose-900">
        Daftar Pegawai Belum Membuat Kumpulan SKP Kategori — {{ \Carbon\Carbon::parse($bulanTahun . '-01')->translatedFormat('F Y') }}
      </h3>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-gray-50 text-gray-500 font-bold uppercase border-b">
            <th class="p-3.5">#</th>
            <th class="p-3.5">Nama Pegawai</th>
            <th class="p-3.5">NIP</th>
            <th class="p-3.5 text-center">Status Rekapitulasi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($belumMengisi as $index => $item)
            <tr class="hover:bg-rose-50/30 transition-colors">
              <td class="p-3.5 text-gray-400 font-bold">{{ $index + 1 }}</td>
              <td class="p-3.5 font-bold text-gray-900">{{ $item['name'] }}</td>
              <td class="p-3.5 text-gray-500 font-mono">{{ $item['nip'] }}</td>
              <td class="p-3.5 text-center">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                  ❌ Belum Rekap Bulan Ini
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="p-8 text-center text-emerald-600 font-bold italic">
                🎉 Luar Biasa! Seluruh pegawai sudah membuat Kumpulan SKP Kategori pada bulan ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- TAB 2: SUDAH MEMBUAT KUMPULAN --}}
  <div x-show="activeTab === 'sudah'" x-cloak class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="px-4 py-3 bg-emerald-50/50 border-b border-emerald-100">
      <h3 class="text-xs font-bold text-emerald-900">
        Daftar Pegawai yang Sudah Dibuatkan Link Kumpulan — {{ \Carbon\Carbon::parse($bulanTahun . '-01')->translatedFormat('F Y') }}
      </h3>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-gray-50 text-gray-500 font-bold uppercase border-b">
            <th class="p-3.5">#</th>
            <th class="p-3.5">Nama Pegawai</th>
            <th class="p-3.5">Kategori</th>
            <th class="p-3.5">Judul Rekapitulasi SKP</th>
            <th class="p-3.5 text-center">Isi Kegiatan</th>
            <th class="p-3.5 text-center">Aksi Link</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($sudahMengisi as $index => $item)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="p-3.5 text-gray-400 font-bold">{{ $index + 1 }}</td>
              <td class="p-3.5 font-bold text-gray-900">
                {{ $item['name'] }}
                <div class="text-[10px] font-normal text-gray-400">{{ $item['nip'] }}</div>
              </td>
              <td class="p-3.5">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                  {{ $item['kategori'] }}
                </span>
              </td>
              <td class="p-3.5 font-semibold text-gray-800 max-w-xs">
                {{ $item['judul_kumpulan'] }}
                <div class="text-[10px] text-gray-400 font-normal">Dibuat: {{ $item['tgl_dibuat'] }}</div>
              </td>
              <td class="p-3.5 text-center">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                  {{ $item['total_kegiatan'] }} Items
                </span>
              </td>
              <td class="p-3.5 text-center">
                <a href="{{ route('sigap-skp.kumpulan.public-show', $item['slug']) }}" target="_blank" 
                   class="px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-[11px] font-bold transition-colors inline-block">
                  🔗 Buka Link Rekap
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-gray-400 italic">
                Belum ada pegawai yang membuat Kumpulan SKP pada bulan ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection