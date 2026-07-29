@extends('layouts.page')

@section('title', 'Detail Riwayat: ' . $nama . ' — SIGAP BRIDA')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

<section class="bg-maroon py-12 text-white">
    <div class="max-w-6xl mx-auto px-4">
        <a href="{{ route('sigap-daftar-hadir.public.riwayat-peserta', ['q' => $nama]) }}#cari"
           class="inline-flex items-center gap-1.5 text-xs text-white/80 hover:text-white transition mb-3">
          ← Kembali ke Pencarian
        </a>
        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
          Riwayat Kehadiran: <span class="text-white/90">{{ $nama }}</span>
        </h1>
        <p class="text-sm text-white/80 mt-1">
          Daftar seluruh agenda kegiatan resmi yang terdaftar atas nama ini.
        </p>
    </div>
</section>

<section class="py-12 bg-gray-50 min-h-[500px]">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <h2 class="font-bold text-gray-900 text-sm">Daftar Kegiatan Diikuti</h2>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-maroon/10 text-maroon">
                    {{ $pesertaList->count() }} Kegiatan
                </span>
            </div>

            @if($pesertaList->isEmpty())
                <div class="p-8 text-center text-gray-500 text-sm">
                    Tidak ada riwayat kegiatan ditemukan.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                            <tr>
                                <th class="px-5 py-3.5 text-center w-12">No</th>
                                <th class="px-5 py-3.5">Nama Kegiatan</th>
                                <th class="px-5 py-3.5">Hari / Tanggal</th>
                                <th class="px-5 py-3.5">Tempat</th>
                                <th class="px-5 py-3.5">Instansi Terdaftar</th>
                                <th class="px-5 py-3.5 text-center">Status</th>
                                <th class="px-5 py-3.5 text-center">Dokumen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($pesertaList as $i => $peserta)
                                @php $kegiatan = $peserta->kegiatan; @endphp
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="px-5 py-4 text-center font-medium text-gray-400">{{ $i + 1 }}</td>
                                    <td class="px-5 py-4 font-bold text-gray-900">
                                        {{ $kegiatan->nama_kegiatan ?? '(Kegiatan telah dihapus)' }}
                                    </td>
                                    <td class="px-5 py-4 text-xs font-medium text-gray-700 whitespace-nowrap">
                                        {{ $kegiatan->hari_tanggal ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-600">
                                        {{ $kegiatan->tempat ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-xs text-gray-600">
                                        {{ $peserta->instansi }}
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        @if($kegiatan)
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border
                                                {{ $kegiatan->status === 'selesai'
                                                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                                                    : ($kegiatan->status === 'proses'
                                                        ? 'bg-blue-50 border-blue-200 text-blue-700'
                                                        : 'bg-gray-50 border-gray-200 text-gray-600') }}">
                                                {{ strtoupper($kegiatan->status) }}
                                            </span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold border bg-red-50 border-red-200 text-red-600">
                                                DIHAPUS
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center whitespace-nowrap">
                                        @if($kegiatan && $kegiatan->status === 'selesai')
                                            <a href="{{ route('sigap-daftar-hadir.public.export-pdf', $kegiatan->uuid) }}"
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-emerald-600 bg-emerald-50 text-emerald-700 font-semibold text-xs hover:bg-emerald-600 hover:text-white transition shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Download PDF
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Belum Tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>

@endsection