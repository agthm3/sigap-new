@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      {{ $kegiatan->nama_kegiatan }}
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      {{ $kegiatan->hari_tanggal }} • {{ $kegiatan->tempat }} • {{ $kegiatan->waktu }}
    </p>
  </div>

  <div class="flex flex-wrap gap-2">
    <a href="{{ route('sigap-daftar-hadir.edit', $kegiatan->uuid) }}"
       class="px-4 py-2 rounded-xl border border-blue-300 text-blue-700 text-sm font-semibold hover:bg-blue-50">
      Edit
    </a>

    @if($kegiatan->status === 'selesai')
      <a href="{{ route('sigap-daftar-hadir.export-pdf', $kegiatan->uuid) }}"
         class="px-4 py-2 rounded-xl border border-emerald-500 text-emerald-700 text-sm font-semibold hover:bg-emerald-50">
        Export PDF
      </a>
    @endif
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-1">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm text-gray-500">Status</p>
        <p class="font-semibold text-gray-900">{{ strtoupper($kegiatan->status) }}</p>
      </div>
      <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
        {{ $kegiatan->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($kegiatan->status === 'proses' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-gray-50 border-gray-200 text-gray-700') }}">
        {{ strtoupper($kegiatan->status) }}
      </span>
    </div>

    <div class="mt-4 text-center">
      <div class="inline-block p-4 rounded-2xl border bg-white">
        {!! QrCode::format('svg')->size(220)->margin(1)->generate($qrUrl) !!}
      </div>
      <p class="text-xs text-gray-500 mt-3 break-all">{{ $qrUrl }}</p>
    </div>

    <div class="mt-4 flex gap-2">
      <a href="{{ route('sigap-daftar-hadir.print-qr', $kegiatan->uuid) }}"
        target="_blank"
        class="flex-1 px-4 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50 text-center">
        Print QR
      </a>

      @hasanyrole('admin|verif_daftarhadir')
        <form action="{{ route('sigap-daftar-hadir.status', $kegiatan->uuid) }}" method="POST" class="flex-1">
          @csrf
          @if($kegiatan->status === 'selesai')
            <input type="hidden" name="status" value="proses">
            <button type="submit" class="w-full px-4 py-2 rounded-xl bg-gray-900 text-white text-sm">
              Buka Lagi
            </button>
          @else
            <input type="hidden" name="status" value="selesai">
            <button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm">
              Tutup QR
            </button>
          @endif
        </form>
      @endhasanyrole
    </div>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-gray-900">Daftar Peserta</h2>
      <span class="text-sm text-gray-500">{{ $kegiatan->peserta->count() }} peserta</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="px-3 py-2 text-left">Urut</th>
            <th class="px-3 py-2 text-left">Nama</th>
            <th class="px-3 py-2 text-left">Instansi</th>
            <th class="px-3 py-2 text-left">Gender</th>
            <th class="px-3 py-2 text-left">No HP</th>
            <th class="px-3 py-2 text-left">Email</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($kegiatan->peserta as $p)
            <tr>
              <td class="px-3 py-2">{{ $p->urutan_absen }}</td>
              <td class="px-3 py-2 font-medium text-gray-900">{{ $p->nama }}</td>
              <td class="px-3 py-2">{{ $p->instansi }}</td>
              <td class="px-3 py-2">{{ $p->gender }}</td>
              <td class="px-3 py-2">{{ $p->no_hp }}</td>
              <td class="px-3 py-2">{{ $p->email ?: '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada peserta.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection