@extends('layouts.app')

@section('content')
<div x-data="kumpulanIndex()" class="space-y-6">

  {{-- Header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-4">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">
        Kumpulan Laporan <span class="text-maroon">SKP Kategori</span>
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">
        Rekapitulasi kumpulan kegiatan per kategori (DIREKTIF / TUPOKSI) bulan ini.
      </p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-skp.pribadi') }}" class="px-3.5 py-2 rounded-xl border text-xs font-semibold text-gray-700 hover:bg-gray-50">
        ← Kembali ke SKP Pribadi
      </a>
      <a href="{{ route('sigap-skp.kumpulan.create') }}" class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-bold hover:bg-maroon-800 shadow-sm flex items-center gap-1.5">
        ➕ Buat Kumpulan Link
      </a>
    </div>
  </div>

  {{-- Tabel Kumpulan --}}
  <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-gray-50 text-gray-600 text-xs font-bold uppercase border-b border-gray-200">
            <th class="p-4">Bulan & Tahun</th>
            <th class="p-4">Kategori</th>
            <th class="p-4">Judul Kumpulan Rekap</th>
            <th class="p-4 text-center">Jumlah SKP</th>
            <th class="p-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
          @forelse($kumpulans as $item)
            @php $showUrl = route('sigap-skp.kumpulan.public-show', $item->slug); @endphp
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="p-4 font-semibold text-gray-900">
                📅 {{ \Carbon\Carbon::parse($item->bulan_tahun . '-01')->translatedFormat('F Y') }}
              </td>
              <td class="p-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ str_contains($item->kategori, 'DIREKTIF') ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                  {{ $item->kategori }}
                </span>
              </td>
              <td class="p-4 font-bold text-gray-800">
                {{ $item->judul_kumpulan }}
              </td>
              <td class="p-4 text-center">
                <span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-gray-100 text-gray-700">
                  {{ is_array($item->skp_ids) ? count($item->skp_ids) : 0 }} Foto/Kegiatan
                </span>
              </td>
              <td class="p-4 text-center">
                <div class="flex items-center justify-center gap-2">
                  {{-- Tombol Salin Link --}}
                  <button type="button" 
                          @click="copyLink('{{ $showUrl }}')" 
                          class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors flex items-center gap-1">
                    📋 Salin Link
                  </button>

                  {{-- Tombol Buka Link --}}
                  <a href="{{ $showUrl }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800">
                    Buka
                  </a>

                  {{-- Tombol Hapus --}}
                  <form action="{{ route('sigap-skp.kumpulan.destroy', $item->slug) }}" method="POST" onsubmit="return confirm('Hapus kumpulan rekap ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                      🗑️
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="p-8 text-center text-gray-400 italic">
                Belum ada kumpulan laporan kategori yang dibuat. Klik "Buat Kumpulan Link" untuk membuat rekap bulan ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div>
    {{ $kumpulans->links() }}
  </div>

</div>
@endsection

@push('scripts')
<script>
function kumpulanIndex() {
  return {
    copyLink(url) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
          Swal.fire({
            icon: 'success',
            title: 'Link Disalin!',
            text: 'URL kumpulan rekapitulasi telah tersalin ke clipboard.',
            timer: 1500,
            showConfirmButton: false
          });
        });
      } else {
        // Fallback jika browser tidak mendukung clipboard API
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        Swal.fire({
          icon: 'success',
          title: 'Link Disalin!',
          timer: 1500,
          showConfirmButton: false
        });
      }
    }
  }
}
</script>
@endpush