@extends('layouts.app')

@section('content')
<section class="flex items-center justify-between gap-3 mb-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Daftar <span class="text-maroon">Riset</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">Semua riset yang telah diunggah.</p>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('riset.create') }}"
       class="px-3 py-2 rounded-lg bg-maroon text-white text-sm hover:bg-maroon-800">+ Unggah Riset</a>
  </div>
</section>

@if (session('success'))
  <div class="rounded-xl border border-green-200 bg-green-50 text-green-800 p-3 text-sm mb-4">
    {{ session('success') }}
  </div>
@endif

<div class="rounded-xl border border-gray-200 bg-white overflow-hidden">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Dokumen</th>
          <th class="px-4 py-3 text-left">Tahun</th>
          <th class="px-4 py-3 text-left">Jenis</th>
          <th class="px-4 py-3 text-left">Tag</th>
          <th class="px-4 py-3 text-left">Akses</th>
          <th class="px-4 py-3 text-left">Ukuran</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($risets as $r)
          <tr>
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                @if ($r->thumbnail_path)
                  <img src="{{ Storage::disk('public')->url($r->thumbnail_path) }}"
                       class="w-12 h-12 rounded object-cover border" alt="thumb">
                @else
                  <div class="w-12 h-12 rounded bg-gray-100 border flex items-center justify-center text-xs text-gray-500">
                    PDF
                  </div>
                @endif
                <div>
                  <div class="font-semibold text-gray-900">{{ $r->title }}</div>
                  <div class="text-xs text-gray-500">
                    @php
                      $authors = is_array($r->authors) ? $r->authors : [];
                      $names = collect($authors)->pluck('name')->filter()->toArray();
                    @endphp
                    {{ $names ? implode(', ', array_slice($names, 0, 3)) : '—' }}
                  </div>
                </div>
              </div>
            </td>
            <td class="px-4 py-3">{{ $r->year }}</td>
            <td class="px-4 py-3">
              @if($r->type)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border
                  @class([
                    'bg-emerald-50 border-emerald-200 text-emerald-700' => $r->type==='internal',
                    'bg-amber-50 border-amber-200 text-amber-700' => $r->type==='kolaborasi',
                    'bg-sky-50 border-sky-200 text-sky-700' => $r->type==='eksternal',
                  ])">
                  {{ ucfirst($r->type) }}
                </span>
              @else
                —
              @endif
            </td>
            <td class="px-4 py-3">
              @php $tags = is_array($r->tags) ? $r->tags : []; @endphp
              <div class="flex flex-wrap gap-1.5">
                @forelse($tags as $t)
                  <span class="inline-block px-2 py-0.5 rounded bg-gray-100 border text-[11px]">{{ $t }}</span>
                @empty
                  <span class="text-gray-400">—</span>
                @endforelse
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] border
                @class([
                  'bg-gray-50 border-gray-200 text-gray-700' => $r->access==='Public',
                  'bg-rose-50 border-rose-200 text-rose-700' => $r->access==='Restricted',
                ])">
                {{ $r->access }}
              </span>
            </td>
            <td class="px-4 py-3">{{ $r->file_size ?? '—' }}</td>
            <td class="px-4 py-3">
              <div class="flex items-center gap-2 justify-end">
                @php $pdfUrl = Storage::disk('public')->url($r->file_path); @endphp
                <a href="{{ route('riset.edit', $r->id) }}"
                  class="px-2.5 py-1.5 rounded border text-xs hover:bg-gray-50">Edit</a>
                <a href="{{ $pdfUrl }}" target="_blank"
                  class="px-2.5 py-1.5 rounded border text-xs hover:bg-gray-50">View</a>
                <a href="{{ $pdfUrl }}" download
                  class="px-2.5 py-1.5 rounded bg-maroon text-white text-xs hover:bg-maroon-800">Download</a>
              </div>
            </td>

            
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada riset.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-4 py-3 border-t bg-gray-50">
    {{ $risets->onEachSide(1)->links() }}
  </div>
</div>
@endsection
