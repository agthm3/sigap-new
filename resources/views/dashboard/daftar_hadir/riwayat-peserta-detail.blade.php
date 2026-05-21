@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <a href="{{ route('sigap-daftar-hadir.riwayat-peserta') }}"
       class="inline-flex items-center gap-1 text-xs text-gray-500 hover:text-maroon mb-1">
      ← Kembali ke pencarian
    </a>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Riwayat: <span class="text-maroon">{{ $nama }}</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Semua kegiatan yang pernah dihadiri oleh peserta ini.
    </p>
  </div>
</section>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <h2 class="font-semibold text-gray-900">Daftar Kegiatan</h2>
    <span class="text-xs text-gray-500">{{ $pesertaList->count() }} kegiatan</span>
  </div>

  @if($pesertaList->isEmpty())
    <div class="px-4 py-8 text-center text-gray-500 text-sm">
      Tidak ada riwayat kegiatan ditemukan.
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="px-4 py-3 text-left">No</th>
            <th class="px-4 py-3 text-left">Nama Kegiatan</th>
            <th class="px-4 py-3 text-left">Hari/Tanggal</th>
            <th class="px-4 py-3 text-left">Tempat</th>
            <th class="px-4 py-3 text-left">Instansi (saat itu)</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($pesertaList as $i => $peserta)
            @php $kegiatan = $peserta->kegiatan; @endphp
            <tr>
              <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>
              <td class="px-4 py-3 font-medium text-gray-900">
                {{ $kegiatan->nama_kegiatan ?? '(kegiatan dihapus)' }}
              </td>
              <td class="px-4 py-3">{{ $kegiatan->hari_tanggal ?? '-' }}</td>
              <td class="px-4 py-3">{{ $kegiatan->tempat ?? '-' }}</td>
              <td class="px-4 py-3 text-gray-600">{{ $peserta->instansi }}</td>
              <td class="px-4 py-3">
                @if($kegiatan)
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                    {{ $kegiatan->status === 'selesai'
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                        : ($kegiatan->status === 'proses'
                            ? 'bg-blue-50 border-blue-200 text-blue-700'
                            : 'bg-gray-50 border-gray-200 text-gray-700') }}">
                    {{ strtoupper($kegiatan->status) }}
                  </span>
                @else
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border bg-red-50 border-red-200 text-red-600">
                    DIHAPUS
                  </span>
                @endif
              </td>
              <td class="px-4 py-3">
                <div class="flex gap-2 flex-wrap">
                  @if($kegiatan)
                    <a href="{{ route('sigap-daftar-hadir.show', $kegiatan->uuid) }}"
                       class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">
                      Buka
                    </a>

                    @if($kegiatan->status === 'selesai')
                      <a href="{{ route('sigap-daftar-hadir.export-pdf', $kegiatan->uuid) }}"
                         class="px-3 py-1.5 rounded border border-emerald-500 text-emerald-700 text-xs hover:bg-emerald-50">
                        Download PDF
                      </a>
                    @endif
                  @else
                    <span class="text-xs text-gray-400 italic">Kegiatan sudah dihapus</span>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection