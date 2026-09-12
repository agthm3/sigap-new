@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <div class="flex items-center gap-2">
        <a href="{{ route('sigap-agenda.index') }}" class="text-maroon hover:text-maroon-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-2xl font-extrabold text-gray-900">Pencarian Detail Kegiatan</h1>
      </div>
      <p class="text-sm text-gray-600 mt-1 ml-8">
        Cari rekam jejak kegiatan berdasarkan nama pegawai (yang ditugaskan), tempat, atau rincian kegiatan.
      </p>
    </div>
  </div>
</section>

<!-- FORM PENCARIAN -->
<section class="max-w-7xl mx-auto px-4 -mt-3">
  <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 mb-6">
    <form action="{{ route('sigap-agenda.search-items') }}" method="GET" class="flex flex-col sm:flex-row items-end gap-3">
      <div class="flex-1 w-full">
        <label class="block text-sm font-semibold text-gray-700">Kata Kunci Pencarian</label>
        <div class="relative mt-1.5">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" name="keyword" value="{{ request('keyword') }}" 
                   placeholder="Masukkan nama pegawai, tempat, atau deskripsi..." 
                   class="w-full pl-10 rounded-xl border border-gray-300 p-3 focus:border-maroon focus:ring-maroon">
        </div>
      </div>
      <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 text-white font-medium hover:bg-blue-700">Temukan Data</button>
      @if(request('keyword'))
        <a href="{{ route('sigap-agenda.search-items') }}" class="px-5 py-3 rounded-xl border border-gray-300 text-gray-700 font-medium hover:bg-gray-50">Reset</a>
      @endif
    </form>
  </div>
</section>

<!-- HASIL PENCARIAN -->
<section class="max-w-7xl mx-auto px-4 pb-10">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="text-left px-4 py-3 min-w-[150px]">Tanggal & Agenda</th>
            <th class="text-left px-4 py-3 min-w-[250px]">Deskripsi Kegiatan</th>
            <th class="text-left px-4 py-3 min-w-[150px]">Tempat & Waktu</th>
            <th class="text-left px-4 py-3 min-w-[200px]">Penugasan</th>
            <th class="text-left px-4 py-3 min-w-[100px]">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($items as $it)
                @php
                    $assignees = [];
                    if ($it->assignees) {
                        try {
                            $json = json_decode($it->assignees, true);
                            if (is_array($json)) {
                                foreach ($json['users'] ?? [] as $u) { $assignees[] = $u['name']; }
                                foreach ($json['manual'] ?? [] as $m) { $assignees[] = $m; }
                            }
                        } catch (\Throwable $e) {}
                    }
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 align-top">
                        <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($it->agenda_date)->locale('id')->translatedFormat('d M Y') }}</div>
                        <div class="text-xs text-gray-500 mt-1">{{ $it->unit_title }}</div>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="text-gray-800 line-clamp-3" title="{{ $it->description }}">{{ $it->description }}</div>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="font-medium text-gray-900">{{ $it->place }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">Pukul: {{ $it->time_text }}</div>
                    </td>
                    <td class="px-4 py-3 align-top">
                        @if(!empty($assignees))
                            <div class="flex flex-wrap gap-1">
                                @foreach($assignees as $name)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-maroon/10 text-maroon font-medium border border-maroon/20">
                                        {{ $name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 align-top">
                        <a href="{{ route('sigap-agenda.show', ['id' => $it->sigap_agenda_id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-medium">
                            Lihat Agenda
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-10 text-gray-500">
                        @if(request('keyword'))
                            Tidak ada data kegiatan/pegawai yang ditemukan untuk "<b>{{ request('keyword') }}</b>".
                        @else
                            Silakan masukkan kata kunci pencarian.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
      </table>
    </div>
    
    <!-- PAGINASI -->
    @if($items->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $items->links() }}
        </div>
    @endif
  </div>
</section>
@endsection