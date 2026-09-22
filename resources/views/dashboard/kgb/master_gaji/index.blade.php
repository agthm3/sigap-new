@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Tabel Acuan <span class="text-maroon">Gaji Pokok</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Master data referensi nominal kenaikan gaji berkala resmi PNS & PPPK.
    </p>
  </div>

<div class="flex items-center gap-2">
    {{-- Tombol Cetak PDF Landscape Mengikuti Filter Aktif --}}
    <a href="{{ route('sigap-kgb.master-gaji.cetak-pdf', request()->query()) }}" target="_blank"
       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 shadow-sm transition">
      <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
      </svg>
      Cetak Tabel (Landscape PDF)
    </a>

    <a href="{{ route('sigap-kgb.index') }}"
       class="px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
      &larr; Kembali ke Monitoring KGB
    </a>
  </div>
</section>

{{-- FORM TAMBAH / UPDATE NOMINAL CEPAT --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm mt-4">
  <h2 class="font-bold text-sm text-gray-900 mb-3">+ Tambah / Sesuaikan Aturan Gaji</h2>
  <form action="{{ route('sigap-kgb.master-gaji.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
    @csrf
    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Pegawai</label>
      <select name="jenis_pegawai" class="w-full rounded-lg text-xs px-3 py-2 border">
        <option value="pns">PNS</option>
        <option value="pppk">PPPK</option>
      </select>
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1">Golongan</label>
      <input type="text" name="golongan" placeholder="misal: III/a atau IX" required
             class="w-full rounded-lg text-xs px-3 py-2 border">
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1">Masa Kerja (Tahun)</label>
      <input type="number" name="masa_kerja" placeholder="0" min="0" max="40" required
             class="w-full rounded-lg text-xs px-3 py-2 border">
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1">Gaji Pokok (Rp)</label>
      <input type="number" name="nominal" placeholder="3000000" min="0" required
             class="w-full rounded-lg text-xs px-3 py-2 border">
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-700 mb-1">Dasar Regulasi</label>
      <input type="text" name="regulasi" value="PP 5/2024" required
             class="w-full rounded-lg text-xs px-3 py-2 border">
    </div>

    <div>
      <button type="submit" class="w-full px-4 py-2 bg-maroon text-white text-xs font-semibold rounded-lg hover:bg-maroon-800 transition">
        Simpan Aturan
      </button>
    </div>
  </form>
</div>

{{-- DAFTAR TABEL GAJI POKOK --}}
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-3">
    <h2 class="font-semibold text-gray-900 text-sm">Daftar Nominal Gaji Pokok</h2>
    
    <form method="GET" class="flex items-center gap-2">
      <select name="jenis_pegawai" onchange="this.form.submit()" class="text-xs rounded-lg px-2.5 py-1.5 border">
        <option value="">Semua Jenis</option>
        <option value="pns" {{ request('jenis_pegawai') === 'pns' ? 'selected' : '' }}>PNS</option>
        <option value="pppk" {{ request('jenis_pegawai') === 'pppk' ? 'selected' : '' }}>PPPK</option>
      </select>

      <select name="golongan" onchange="this.form.submit()" class="text-xs rounded-lg px-2.5 py-1.5 border">
        <option value="">Semua Golongan</option>
        @if(!empty($daftarGolongan))
          @php foreach($daftarGolongan as$gol): @endphp
            <option value="{{ $gol }}" {{ request('golongan') === $gol ? 'selected' : '' }}>{{ $gol }}</option>
          @php endforeach; @endphp
        @endif
      </select>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-xs">
      <thead class="bg-gray-50 uppercase text-gray-600 border-b font-semibold">
        <tr>
          <th class="px-4 py-3 text-left">Jenis</th>
          <th class="px-4 py-3 text-left">Golongan</th>
          <th class="px-4 py-3 text-left">Masa Kerja (MKG)</th>
          <th class="px-4 py-3 text-left">Nominal Gaji Pokok</th>
          <th class="px-4 py-3 text-left">Dasar Regulasi</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @if(isset($gajis) && count($gajis) > 0)
          @php foreach($gajis as$item): @endphp
            <tr>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-0.5 rounded-full font-bold uppercase border {{ $item->jenis_pegawai === 'pns' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-purple-50 border-purple-200 text-purple-700' }}">
                  {{ $item->jenis_pegawai }}
                </span>
              </td>
              <td class="px-4 py-3 font-bold text-gray-900">{{ $item->golongan }}</td>
              <td class="px-4 py-3 font-semibold text-gray-700">{{ $item->masa_kerja }} Tahun</td>
              <td class="px-4 py-3 font-extrabold text-maroon text-sm">
                Rp {{ number_format($item->nominal, 0, ',', '.') }}
              </td>
              <td class="px-4 py-3 text-gray-500">{{ $item->regulasi }}</td>
              <td class="px-4 py-3">
                <form action="{{ route('sigap-kgb.master-gaji.destroy', $item->id) }}" method="POST" class="inline form-delete">
                  @csrf
                  @method('DELETE')
                  <button type="button" class="btn-delete px-2 py-1 rounded border border-red-500 text-red-600 hover:bg-red-600 hover:text-white transition"
                          data-judul="{{ $item->jenis_pegawai }} {{ $item->golongan }} MKG {{$item->masa_kerja }} Thn">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          @php endforeach; @endphp
        @else
          <tr>
            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
              Belum ada data tabel gaji. Jalankan seeder atau isi melalui form di atas.
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  {{ $gajis->links() }}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const judul = this.dataset.judul;

      Swal.fire({
        title: 'Hapus Aturan Gaji?',
        html: `Aturan <b>${judul}</b> akan dihapus!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush