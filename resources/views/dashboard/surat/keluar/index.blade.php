@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between" x-data="{ modalBuku: false, tahunPilih: '{{ date('Y') }}' }">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">SURAT KELUAR</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Buku register penomoran surat keluar dan ketersediaan slot cadangan tanggal mundur.
    </p>
  </div>

  @hasanyrole('admin|verif_surat')
    <div class="flex items-center gap-2">
      <!-- Tombol Cetak / Mode Buku Agenda -->
      <button type="button" @click="modalBuku = true"
              class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 shadow-2xs transition">
        <svg class="w-4 h-4 text-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        Buku Agenda Tahunan
      </button>

      <!-- Tombol Ambil Nomor Baru -->
      <a href="{{ route('sigap-surat.keluar.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 shadow-sm transition">
        + Ambil / Catat Nomor
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
            <h3 class="text-base font-bold text-gray-900">Buku Register Tahunan</h3>
            <p class="text-xs text-gray-500">Pilih tahun takwim agenda yang ingin dibuka/dicetak</p>
          </div>
        </div>

        <form method="GET" action="{{ route('sigap-surat.keluar.buku-agenda') }}" target="_blank">
          <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Tahun Takwim Surat</label>
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
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Baris Agenda</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalSemua }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Surat Terbit</p>
    <h3 class="text-2xl font-extrabold text-emerald-600">{{ $totalTerbit }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Slot Cadangan (Tersedia)</p>
    <h3 class="text-2xl font-extrabold text-maroon">{{ $totalSlot }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Nomor Dibatalkan</p>
    <h3 class="text-2xl font-extrabold text-gray-400">{{ $totalBatal }}</h3>
  </div>
</div>

<!-- Filter & Tabel -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-3">
    <h2 class="font-semibold text-gray-900">Buku Register Agenda (Tahun {{ $tahunSekarang }})</h2>

    <form method="GET" action="{{ route('sigap-surat.keluar.index') }}" class="flex flex-wrap items-center gap-2">
      <input type="date" name="tanggal" value="{{ request('tanggal') }}"
             class="text-xs px-3 py-1.5 rounded-lg border border-gray-300">
      
      <select name="status" class="text-xs px-3 py-1.5 rounded-lg border border-gray-300">
        <option value="">-- Semua Status --</option>
        <option value="terbit" {{ request('status') === 'terbit' ? 'selected' : '' }}>Terbit</option>
        <option value="slot_kosong" {{ request('status') === 'slot_kosong' ? 'selected' : '' }}>Slot Kosong</option>
        <option value="batal" {{ request('status') === 'batal' ? 'selected' : '' }}>Batal</option>
      </select>

      <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no/perihal..."
             class="text-xs px-3 py-1.5 rounded-lg border border-gray-300">

      <button type="submit" class="px-3 py-1.5 rounded-lg bg-gray-200 hover:bg-gray-300 text-xs font-semibold">
        Saring
      </button>
      @if(request()->anyFilled(['tanggal', 'status', 'q']))
        <a href="{{ route('sigap-surat.keluar.index') }}" class="text-xs text-maroon hover:underline">Reset</a>
      @endif
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-center w-16">No. Urut</th>
          <th class="px-4 py-3 text-left">Tanggal</th>
          <th class="px-4 py-3 text-left">No. Berkas</th>
          <th class="px-4 py-3 text-left">Nomor Surat Lengkap</th>
          <th class="px-4 py-3 text-left">Alamat Penerima</th>
          <th class="px-4 py-3 text-left">Perihal</th>
          <th class="px-4 py-3 text-left">Pembuat</th>
          <th class="px-4 py-3 text-center">Status</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($surats as $item)
          <tr class="{{ $item->status === 'slot_kosong' ? 'bg-amber-50/50' : ($item->status === 'batal' ? 'bg-gray-100/60' : '') }}">
            <td class="px-4 py-3 text-center font-extrabold text-gray-900">
              {{ $item->nomor_urut }}
            </td>
            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
              {{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}
            </td>
            <td class="px-4 py-3 font-mono text-xs text-gray-600">
              {{ $item->nomor_berkas ?: '-' }}
            </td>
            <td class="px-4 py-3 font-semibold {{ $item->status === 'terbit' ? 'text-maroon' : 'text-gray-400' }}">
              @if($item->status === 'slot_kosong')
                <span class="italic font-normal text-xs text-amber-800">[Slot Cadangan Kosong]</span>
              @elseif($item->status === 'batal')
                <span class="line-through text-gray-400">{{ $item->nomor_surat_lengkap ?: 'Dibatalkan' }}</span>
              @else
                {{ $item->nomor_surat_lengkap }}
              @endif
            </td>
            <td class="px-4 py-3 text-gray-700 max-w-xs truncate">
              {{ $item->alamat_penerima ?: '-' }}
            </td>
            <td class="px-4 py-3 text-gray-700 max-w-sm truncate">
              {{ $item->perihal ?: '-' }}
            </td>
            <td class="px-4 py-3 text-xs text-gray-600">
              {{ $item->creator->name ?? '-' }}
            </td>
            <td class="px-4 py-3 text-center">
              @if($item->status === 'terbit')
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border bg-emerald-50 border-emerald-200 text-emerald-700">TERBIT</span>
              @elseif($item->status === 'slot_kosong')
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border bg-amber-50 border-amber-200 text-amber-700">CADANGAN</span>
              @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border bg-gray-200 border-gray-300 text-gray-700">BATAL</span>
              @endif
            </td>
            <td class="px-4 py-3 text-center whitespace-nowrap">
              <div class="flex items-center justify-center gap-1.5">
                @if($item->status === 'slot_kosong')
                  <a href="{{ route('sigap-surat.keluar.edit', $item->id) }}"
                     class="px-2.5 py-1 rounded-lg bg-maroon text-white text-xs hover:bg-maroon-800 transition">
                    Gunakan Slot
                  </a>
                @elseif($item->status === 'terbit')
                  <a href="{{ route('sigap-surat.keluar.show', $item->id) }}"
                     class="px-2.5 py-1 rounded border text-xs hover:bg-gray-50">
                    Buka
                  </a>
                  <button type="button"
                          onclick="navigator.clipboard.writeText('{{ $item->nomor_surat_lengkap }}'); Swal.fire({title: 'Tersalin!', text: '{{ $item->nomor_surat_lengkap }}', icon: 'success', timer: 1200, showConfirmButton: false});"
                          class="px-2 py-1 rounded border border-gray-300 text-xs hover:bg-gray-100" title="Salin Nomor">
                    📋
                  </button>
                  <a href="{{ route('sigap-surat.keluar.edit', $item->id) }}"
                     class="px-2 py-1 rounded border text-xs hover:bg-gray-50">
                    Edit
                  </a>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="px-4 py-8 text-center text-gray-500">
              Belum ada baris agenda di buku surat keluar tahun ini.
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