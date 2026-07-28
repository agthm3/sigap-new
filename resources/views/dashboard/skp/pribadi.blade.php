@extends('layouts.app')

@section('content')
<div x-data="skpPribadi()" class="space-y-6">

  {{-- Header --}}
  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        SIGAP <span class="text-maroon">SKP Pribadi</span>
      </h1>
      <p class="text-sm text-gray-600 mt-0.5">
        Daftar laporan kegiatan dan evidence di mana Anda ditautkan/terlibat.
      </p>
    </div>

    <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-100 border border-gray-200 text-xs font-semibold text-gray-700">
      <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
      Pegawai: {{ auth()->user()->name }}
    </div>
  </section>

  {{-- Stats Ringkas --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-sm text-gray-500">Total Kegiatan Saya</p>
      <h3 class="text-2xl font-extrabold text-gray-900">{{ $skps->total() ?? 0 }}</h3> 
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
      <p class="text-sm text-gray-500">Total Foto Dokumentasi Terkait</p>
      <h3 class="text-2xl font-extrabold text-maroon">
        {{ $total_dokumentasi ?? 0 }}
      </h3>
    </div>
  </div>
<div class="flex items-center gap-2">
  <a href="{{ route('sigap-skp.kumpulan.index') }}"
     class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors shadow-sm">
    📂 Kategori & Rekap Link
  </a>

  <a href="{{ route('sigap-skp.upload-mandiri') }}"
     class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
    📷 Upload Mandiri
  </a>
</div>
  {{-- Filter Section --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <form action="{{ route('sigap-skp.pribadi') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
      
      <div class="flex-1">
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Cari nama kegiatan saya..." 
               class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
      </div>

      <div class="w-full sm:w-48">
        <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
               class="w-full rounded-lg px-3 py-2 text-sm border-gray-300">
      </div>

      <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition-colors">
        Filter
      </button>
      
      @if(request()->anyFilled(['search', 'tanggal']))
        <a href="{{ route('sigap-skp.pribadi') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-200 text-center">
          Reset
        </a>
      @endif
    </form>
  </div>

  {{-- GRID CARD SKP PRIBADI --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($skps as $item)
      @php
        $firstFoto = $item->fotos->first();
        $thumbUrl = $firstFoto ? asset('storage/' . $firstFoto->file_path) : null;
        $showUrl = route('sigap-skp.show', $item->slug);
      @endphp
      <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
        <div>
          {{-- Thumbnail Foto --}}
          <div class="relative h-48 w-full bg-gray-100 border-b overflow-hidden">
            @if($thumbUrl)
              <img src="{{ $thumbUrl }}" alt="{{ $item->judul_kegiatan }}" class="w-full h-full object-cover">
            @else
              <div class="flex flex-col items-center justify-center h-full text-gray-400">
                <svg class="w-10 h-10 mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21" stroke-width="2"/></svg>
                <span class="text-xs">Tidak ada gambar</span>
              </div>
            @endif

            {{-- Badges --}}
            <div class="absolute top-3 right-3 flex gap-1.5">
              <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-black/60 text-white backdrop-blur-sm">
                📷 {{ $item->fotos_count }} Foto
              </span>
            </div>

            @if($item->agenda_id)
              <div class="absolute top-3 left-3">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-maroon text-white shadow">
                  Agenda
                </span>
              </div>
            @endif
          </div>

          {{-- Konten Card --}}
          <div class="p-4 space-y-3">
            <div>
              <p class="text-xs font-semibold text-gray-500 mb-0.5">
                📅 {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M Y') }}
              </p>
              <h3 class="font-bold text-gray-900 text-base line-clamp-2 hover:text-maroon transition-colors">
                <a href="{{ $showUrl }}">{{ $item->judul_kegiatan }}</a>
              </h3>
            </div>

            {{-- Pegawai Terlibat Lainnya --}}
            <div>
              <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Rekan Tim Terlibat:</p>
              <div class="flex flex-wrap gap-1">
                @forelse($item->pegawais as $pegawai)
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium border {{ $pegawai->id === auth()->id() ? 'bg-maroon/10 text-maroon border-maroon/30 font-semibold' : 'bg-gray-100 text-gray-800 border-gray-200' }}">
                    👤 {{ $pegawai->id === auth()->id() ? 'Saya' : $pegawai->name }}
                  </span>
                @empty
                  <span class="text-xs text-gray-400 italic">Tidak ada pegawai lain</span>
                @endforelse
              </div>
            </div>
          </div>
        </div>

        {{-- Footer Card & Action Buttons --}}
        <div class="p-4 pt-0 border-t border-gray-100 mt-3 flex items-center justify-between gap-2">
          {{-- TOMBOL SALIN LINK --}}
          <button type="button" 
                  @click="copyToClipboard('{{ $showUrl }}')" 
                  class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2" stroke-width="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" stroke-width="2"/></svg>
            Salin Link
          </button>

          <a href="{{ $showUrl }}" class="px-3.5 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors">
            Lihat Detail
          </a>
        </div>
      </div>
    @empty
      <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-12 text-center text-gray-500 space-y-2">
        <svg class="w-12 h-12 mx-auto text-gray-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 8v4m0 4h.01" stroke-width="2"/></svg>
        <p class="font-medium text-gray-700">Belum ada laporan SKP tempat Anda ditautkan.</p>
        <p class="text-xs text-gray-400">Laporan kegiatan yang melibatkan nama Anda akan muncul secara otomatis di sini.</p>
      </div>
    @endforelse
  </div>

  <div class="mt-4">
    {{ $skps->links() ?? '' }}
  </div>

</div>
@endsection

@push('scripts')
<script>
function skpPribadi() {
  return {
    copyToClipboard(url) {
      navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Link Disalin!',
          text: 'URL detail SKP telah tersalin ke clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      });
    }
  }
}
</script>
@endpush