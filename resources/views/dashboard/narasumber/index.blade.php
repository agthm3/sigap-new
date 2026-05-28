@extends('layouts.app')
@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-6">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Data <span class="text-maroon">Kesediaan Narasumber</span>
    </h1>
  </div>
  <a href="{{ route('sigap-narasumber.pilih-kegiatan') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
    + Minta Kesediaan (QR)
  </a>
</section>

<div class="rounded-2xl border border-gray-200 bg-white shadow-sm">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3">Nama Narasumber</th>
          <th class="px-4 py-3">Kegiatan</th>
          <th class="px-4 py-3">Materi</th>
          <th class="px-4 py-3">Waktu TTD</th>
          <th class="px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($narasumbers as $row)
        <tr>
          <td class="px-4 py-3 font-medium text-gray-900">{{ $row->nama_lengkap }}</td>
          <td class="px-4 py-3">{{ $row->kegiatan->nama_kegiatan }}</td>
          <td class="px-4 py-3">{{ $row->materi ?? '-' }}</td>
          <td class="px-4 py-3">{{ $row->signed_at?->format('d/m/Y H:i') ?? '-' }}</td>
          <td class="px-4 py-3 flex gap-2">
            <a href="{{ route('sigap-narasumber.export-pdf', $row->uuid) }}" class="px-3 py-1.5 rounded border border-emerald-500 text-emerald-700 text-xs hover:bg-emerald-50">Export PDF</a>
            <form action="{{ route('sigap-narasumber.destroy', $row->uuid) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
              @csrf @method('DELETE')
              <button class="px-3 py-1.5 rounded border border-red-500 text-red-600 text-xs hover:bg-red-50">Hapus</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada narasumber yang bersedia.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
<div class="mt-4">{{ $narasumbers->links() }}</div>
@endsection