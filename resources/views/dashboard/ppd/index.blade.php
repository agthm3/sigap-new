@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">PPD</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pertanggungjawaban perjalanan dinas dan kegiatan lapangan.
    </p>
  </div>

  @hasanyrole('admin|verif_ppd')
    <a href="{{ route('sigap-ppd.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
      + Buat Kegiatan
    </a>
  @endhasanyrole
</section>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Kegiatan</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $kegiatans->total() }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Kategori Bimtek</p>
    <h3 class="text-2xl font-extrabold text-maroon">
      {{ $kegiatans->where('kategori', 'bimtek')->count() }}
    </h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Kategori Koordinasi</p>
    <h3 class="text-2xl font-extrabold text-maroon">
      {{ $kegiatans->where('kategori', 'koordinasi')->count() }}
    </h3>
  </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50">
    <h2 class="font-semibold text-gray-900">Daftar Kegiatan</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Judul</th>
          <th class="px-4 py-3 text-left">Kategori</th>
          <th class="px-4 py-3 text-left">Hari/Tanggal</th>
          <th class="px-4 py-3 text-left">Tempat</th>
          <th class="px-4 py-3 text-left">Pegawai</th>
          <th class="px-4 py-3 text-left">Lembar</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($kegiatans as $item)
          <tr>
            <td class="px-4 py-3 font-medium text-gray-900">
              {{ $item->judul }}
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border
                {{ $item->kategori === 'bimtek' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                {{ strtoupper($item->kategori) }}
              </span>
            </td>
            <td class="px-4 py-3">{{ $item->hari_tanggal }}</td>
            <td class="px-4 py-3">{{ $item->tempat }}</td>
            <td class="px-4 py-3">
              <div class="text-xs text-gray-600">
                {{ $item->pegawai->pluck('name')->join(', ') ?: '-' }}
              </div>
            </td>
            <td class="px-4 py-3">{{ $item->jumlah_lembar }}</td>
                        <td class="px-4 py-3">
              <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                {{ $item->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($item->status === 'proses' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-gray-50 border-gray-200 text-gray-700') }}">
                {{ strtoupper($item->status) }}
              </span>
            </td>

            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-2">
                <a href="{{ route('sigap-ppd.show', $item->id) }}"
                  class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">
                  Buka
                </a>

                <a href="{{ route('sigap-ppd.export-pdf', $item->id) }}"
                  class="px-3 py-1.5 rounded border border-maroon text-maroon text-xs hover:bg-maroon hover:text-white">
                  Export PDF
                </a>

                @hasanyrole('admin|verif_ppd')
                  <form action="{{ route('sigap-ppd.status', $item->id) }}" method="POST">
                    @csrf
                    @if($item->status === 'selesai')
                      <input type="hidden" name="status" value="proses">
                      <button type="submit"
                              class="px-3 py-1.5 rounded border text-xs hover:bg-gray-50">
                        Tandai Proses
                      </button>
                    @else
                      <input type="hidden" name="status" value="selesai">
                      <button type="submit"
                              class="px-3 py-1.5 rounded border border-emerald-300 text-emerald-700 text-xs hover:bg-emerald-50">
                        Tandai Selesai
                      </button>
                    @endif
                  </form>
                @endhasanyrole
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
              Belum ada kegiatan PPD.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  {{ $kegiatans->links() }}
</div>
@endsection