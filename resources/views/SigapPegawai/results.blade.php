@extends('layouts.page')

@section('content')
  <!-- Search Bar -->
  <section class="bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:py-10">
      <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
        <form class="grid sm:grid-cols-5 gap-3" action="{{ route('public.pegawai.search') }}" method="GET">
          <div class="sm:col-span-2">
            <label class="text-sm font-semibold text-gray-700">Nama / NIP</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}"
                   class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="Contoh: Andi Rahman / 1987xxxxxxxxxxx" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Unit/Bagian</label>
            <input type="text" name="unit" value="{{ $filters['unit'] ?? '' }}"
                   class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon"
                   placeholder="Kepegawaian / Bidang X" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Urutkan</label>
            <select name="sort" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="name"   @selected(($filters['sort'] ?? 'name')==='name')>Nama (A-Z)</option>
              <option value="latest" @selected(($filters['sort'] ?? '')==='latest')>Terbaru</option>
            </select>
          </div>
          <div class="sm:col-span-5 flex flex-wrap gap-3 pt-1">
            <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
              Cari Data
            </button>
            <a href="{{ route('home.pegawai') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</a>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Results header -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="py-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-maroon">Hasil Pencarian Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Menemukan {{ $results->total() }} pegawai.</p>
      </div>
    </div>
  </section>

  <!-- Results list -->
  <section class="max-w-7xl mx-auto px-4 pb-12">
    <ul class="space-y-4">
      @forelse ($results as $u)
        <li class="border border-gray-200 rounded-xl overflow-hidden p-4 sm:p-6 flex flex-col sm:flex-row gap-4 sm:gap-6">
          @php
            $photoUrl = $u->profile_photo_path
              ? asset('storage/'.$u->profile_photo_path)
              : null;
          @endphp

          <img class="w-24 h-24 rounded-lg object-cover"
              src="{{ $photoUrl ?: 'https://placehold.co/96x96?text=SP' }}"
              alt="Foto profil {{ $u->name }}">
          <div class="flex-1">
            <div class="flex flex-wrap items-center gap-2">
              <h3 class="text-lg font-semibold text-gray-900">{{ $u->name }}</h3>
              @if($u->nip)
                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">NIP: {{ $u->nip }}</span>
              @endif
              @if($u->unit)
                <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">Unit: {{ $u->unit }}</span>
              @endif
            </div>
            <p class="text-sm text-gray-600 mt-1">Jabatan: {{ $u->position ?? '—' }}</p>
          </div>
          <div class="sm:self-center">
            <a href="{{ route('public.pegawai.show', $u->id) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 transition">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
              View
            </a>
          </div>
        </li>
      @empty
        <li class="border border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-600">
          Tidak ada data pegawai yang cocok.
        </li>
      @endforelse
    </ul>

    <div class="mt-8">
      {{ $results->withQueryString()->links() }}
    </div>
  </section>
@endsection
