@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between" x-data="{ modalBuku: false, tahunPilih: '{{ date('Y') }}' }">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">SURAT MASUK</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pencatatan buku agenda surat masuk, tanda terima digital, dan penerbitan lembar disposisi.
    </p>
  </div>

  @hasanyrole('admin|verif_surat')
    <div class="flex items-center gap-2">
      <!-- Tombol Mode Buku Agenda Tahunan -->
      <button type="button" @click="modalBuku = true"
              class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 shadow-2xs transition">
        <svg class="w-4 h-4 text-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        Buku Agenda Tahunan
      </button>

      <!-- Tombol Tambah Surat Masuk -->
      <a href="{{ route('sigap-surat.masuk.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 shadow-sm transition">
        + Catat Surat Masuk
      </a>
    </div>

    <!-- Modal Dialog Pemilihan Tahun -->
    <div x-show="modalBuku" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
      <div @click.away="modalBuku = false"
           class="relative w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl border border-gray-100 transform transition-all">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center font-bold text-lg">
            📖
          </div>
          <div>
            <h3 class="text-base font-bold text-gray-900">Buku Register Surat Masuk</h3>
            <p class="text-xs text-gray-500">Pilih tahun takwim agenda yang ingin dibuka/dicetak</p>
          </div>
        </div>

        <form method="GET" action="{{ route('sigap-surat.masuk.buku-agenda') }}" target="_blank">
          <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tahun Takwim Agenda</label>
            <select name="tahun" x-model="tahunPilih" class="w-full rounded-xl px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-300">
              @for($y = date('Y'); $y >= 2024; $y--)
                <option value="{{ $y }}">Tahun Takwim {{ $y }}</option>
              @endfor
            </select>
          </div>

          <div class="flex items-center justify-end gap-2">
            <button type="button" @click="modalBuku = false"
                    class="px-4 py-2 rounded-xl border text-xs font-medium text-gray-600 hover:bg-gray-100">
              Batal
            </button>
            <button type="submit" @click="modalBuku = false"
                    class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-bold hover:bg-maroon-800">
              Buka Buku Agenda →
            </button>
          </div>
        </form>
      </div>
    </div>
  @endhasanyrole
</section>

<!-- Kartu Ringkasan -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Surat Masuk (Tahun {{ $tahunSekarang }})</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalSemua }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Sifat Penting / Segera</p>
    <h3 class="text-2xl font-extrabold text-maroon">{{ $totalPenting }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Surat Masuk Terakhir</p>
    <h3 class="text-base font-bold text-gray-800 truncate mt-1">
      {{ $surats->first()?->asal_surat ?: '-' }}
    </h3>
  </div>
</div>

<!-- Tabel Agenda Surat Masuk -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-3">
    <h2 class="font-semibold text-gray-900">Daftar Agenda Surat Masuk</h2>

    <form method="GET" action="{{ route('sigap-surat.masuk.index') }}" class="flex flex-wrap items-center gap-2">
      <input type="date" name="tanggal_terima" value="{{ request('tanggal_terima') }}"
             class="text-xs px-3 py-1.5 rounded-lg border border-gray-300">

      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari asal/nomor/perihal..."
             class="text-xs px-3 py-1.5 rounded-lg border border-gray-300">

      <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-xs font-semibold">
        Saring
      </button>

      @if(request()->anyFilled(['tanggal_terima', 'q']))
        <a href="{{ route('sigap-surat.masuk.index') }}" class="text-xs text-maroon hover:underline">Reset</a>
      @endif
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-center w-16">No. Agenda</th>
          <th class="px-4 py-3 text-left">Tgl Terima</th>
          <th class="px-4 py-3 text-left">Surat Dari (Pengirim)</th>
          <th class="px-4 py-3 text-left">Nomor & Tgl Surat</th>
          <th class="px-4 py-3 text-left">Perihal</th>
          <th class="px-4 py-3 text-left">Unit Pengolah</th>
          <th class="px-4 py-3 text-center">Penerima & TTD</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($surats as $item)
          <tr>
            <td class="px-4 py-3 text-center font-extrabold text-maroon font-mono">
              {{ sprintf('%03d', $item->nomor_agenda) }}
            </td>
            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
              {{ $item->tanggal_terima->format('d/m/Y') }}
            </td>
            <td class="px-4 py-3 font-semibold text-gray-900 max-w-xs">
              {{ $item->asal_surat }}
              @if(in_array($item->tingkat_surat, ['Penting', 'Segera', 'Penting / Segera']))
                <span class="inline-block text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-bold ml-1">
                  {{ $item->tingkat_surat }}
                </span>
              @endif
            </td>
            <td class="px-4 py-3 text-gray-600 font-mono text-xs whitespace-nowrap">
              <div>{{ $item->nomor_surat_masuk }}</div>
              <div class="text-[11px] text-gray-400 font-sans">Tgl: {{ $item->tanggal_surat->format('d/m/Y') }}</div>
            </td>
            <td class="px-4 py-3 text-gray-700 max-w-sm truncate">
              {{ $item->perihal }}
            </td>
            <td class="px-4 py-3 text-xs text-gray-600 font-medium">
              {{ $item->unit_pengolah }}
            </td>
            <td class="px-4 py-3 text-center">
              <div class="flex flex-col items-center gap-1">
                @if($item->ttd_penerima)
                  <img src="{{ $item->ttd_penerima }}" alt="TTD" class="h-7 max-w-[80px] object-contain border border-gray-100 rounded bg-white p-0.5">
                @else
                  <span class="text-[10px] text-gray-400 italic">Tanpa TTD</span>
                @endif
                <span class="text-[11px] font-semibold text-gray-700">{{ $item->penerima->name ?? '-' }}</span>
              </div>
            </td>
            <td class="px-4 py-3 text-center whitespace-nowrap">
              <div class="flex items-center justify-center gap-1.5">
                <a href="{{ route('sigap-surat.masuk.disposisi', $item->id) }}" target="_blank"
                   class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow-2xs transition"
                   title="Cetak Lembar Disposisi 1/2 HVS A4">
                  🖨️ Disposisi
                </a>

                <a href="{{ route('sigap-surat.masuk.show', $item->id) }}"
                   class="px-2.5 py-1 rounded border text-xs hover:bg-gray-50 text-gray-700">
                  Detail
                </a>

                @hasanyrole('admin|verif_surat')
                  <a href="{{ route('sigap-surat.masuk.edit', $item->id) }}"
                     class="px-2.5 py-1 rounded border text-xs text-blue-600 border-blue-200 hover:bg-blue-50">
                    Edit
                  </a>
                <form action="{{ route('sigap-surat.masuk.destroy', $item->id) }}" method="POST" class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat masuk Agenda No. {{ sprintf('%03d', $item->nomor_agenda) }}?')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="px-2 py-1 rounded border border-red-300 text-red-600 text-xs hover:bg-red-600 hover:text-white transition">
                    Hapus
                </button>
                </form>
                @endhasanyrole
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
              Belum ada surat masuk yang tercatat pada tahun {{ $tahunSekarang }}.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  {{ $surats->links() }}
</div>
@endsection