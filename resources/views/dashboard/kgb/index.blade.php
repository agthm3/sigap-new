@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">KGB</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Monitoring, countdown jatuh tempo, dan otomatisasi usulan Kenaikan Gaji Berkala (PNS & PPPK).
    </p>
  </div>

  @hasanyrole('admin|superadmin|verif_kgb')
    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-kgb.master-gaji.index') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-sm transition">
        ⚙️ Atur Tabel Acuan Gaji
      </a>
      <a href="{{ route('sigap-kgb.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 shadow-sm transition">
        + Input SK KGB
      </a>
    </div>
  @endhasanyrole
</section>

{{-- STAT BOXES (KONSISTEN DENGAN LAYOUT SIGAP) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500 font-medium">Total Pegawai Terdata</p>
    <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $totalPegawai ?? 0 }}</h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500 font-medium">Segera Jatuh Tempo (&le; 60 Hari)</p>
    <h3 class="text-2xl font-extrabold text-amber-600 mt-1">
      {{ $jatuhTempo ?? 0 }}
    </h3>
  </div>
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500 font-medium">KGB Selesai (Tahun Ini)</p>
    <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">
      {{ $selesaiTahunIni ?? 0 }}
    </h3>
  </div>
</div>

{{-- TABEL MONITORING & COUNTDOWN --}}
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-2">
    <h2 class="font-semibold text-gray-900 text-sm">Daftar Jadwal & Usulan Kenaikan Gaji Berkala</h2>
    
    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-kgb.index') }}" 
         class="text-xs px-3 py-1.5 rounded-lg font-medium transition {{ !request('filter') ? 'bg-maroon text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-100' }}">
        Semua
      </a>
      <a href="{{ route('sigap-kgb.index', ['filter' => 'segera']) }}" 
         class="text-xs px-3 py-1.5 rounded-lg font-medium transition {{ request('filter') === 'segera' ? 'bg-amber-500 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-100' }}">
        Segera Jatuh Tempo (&le; 60 Hari)
      </a>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-xs">
      <thead class="bg-gray-50 uppercase text-gray-600 border-b font-semibold">
        <tr>
          <th class="px-4 py-3 text-left">Pegawai</th>
          <th class="px-4 py-3 text-left">Golongan</th>
          <th class="px-4 py-3 text-left">Masa Kerja (Lama &rarr; Baru)</th>
          <th class="px-4 py-3 text-left">Gaji Pokok Baru</th>
          <th class="px-4 py-3 text-left">TMT Berikutnya</th>
          <th class="px-4 py-3 text-left">Status / Countdown</th>
          <th class="px-4 py-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @if(isset($riwayats) && count($riwayats) > 0)
          @php foreach($riwayats as$item): @endphp
            @php $badge =$item->badge_status; @endphp
            <tr class="hover:bg-gray-50/70 transition-colors">
              <td class="px-4 py-3">
                <a href="{{ route('sigap-kgb.show', $item->id) }}" class="font-bold text-gray-900 hover:text-maroon transition-colors block">
                  {{ $item->user->name ?? '-' }}
                </a>
                <div class="text-[11px] text-gray-500 font-mono">NIP: {{ $item->user->nip ?? '-' }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-0.5 rounded-full font-semibold uppercase border {{ $item->jenis_pegawai === 'pns' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-purple-50 border-purple-200 text-purple-700' }}">
                  {{ $item->jenis_pegawai }} - {{$item->pangkat_golongan }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-700">
                <div>
                  <span class="text-gray-400">{{ $item->mkg_tahun_lama }} Thn</span>
                  <span class="mx-1 font-bold text-gray-400">&rarr;</span>
                  <span class="font-bold text-gray-900">{{ $item->mkg_tahun_baru }} Thn {{ $item->mkg_bulan_baru > 0 ?$item->mkg_bulan_baru . ' Bln' : '' }}</span>
                </div>
              </td>
              <td class="px-4 py-3 font-extrabold text-maroon text-sm">
                Rp {{ number_format($item->gaji_pokok_baru, 0, ',', '.') }}
              </td>
              <td class="px-4 py-3">
                <div class="font-semibold text-gray-900">{{ $item->tmt_baru ? $item->tmt_baru->translatedFormat('d M Y') : '-' }}</div>
                <div class="text-[11px] text-gray-400">SK: {{ $item->nomor_sk_lama }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $badge['color'] }}">
                  {{ $badge['label'] }} ({{$badge['keterangan'] }})
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap items-center gap-1.5">
                  {{-- TOMBOL DETAIL (SHOW) --}}
                  <a href="{{ route('sigap-kgb.show', $item->id) }}"
                     class="px-2.5 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition font-medium shadow-2xs"
                     title="Lihat Rincian KGB">
                    Detail
                  </a>

                  {{-- TOMBOL EDIT --}}
                  @hasanyrole('admin|superadmin|verif_kgb')
                    <a href="{{ route('sigap-kgb.edit', $item->id) }}"
                       class="px-2.5 py-1 rounded border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 transition font-medium shadow-2xs"
                       title="Edit Data SK">
                      Edit
                    </a>
                  @endhasanyrole

                  {{-- TOMBOL EXPORT PDF --}}
                  <a href="{{ route('sigap-kgb.export-pdf', $item->id) }}" target="_blank"
                     class="px-2.5 py-1 rounded border border-maroon text-maroon hover:bg-maroon hover:text-white transition font-medium shadow-2xs"
                     title="Cetak Berkas Usulan">
                    PDF
                  </a>

                  {{-- AKSI STATUS & HAPUS --}}
                  @hasanyrole('admin|superadmin|verif_kgb')
                    @if($item->status !== 'selesai')
                      <form action="{{ route('sigap-kgb.status', $item->id) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="status" value="selesai">
                        <button type="submit" class="px-2.5 py-1 rounded border border-emerald-300 text-emerald-700 hover:bg-emerald-50 transition shadow-2xs" title="Tandai SK Baru Sudah Terbit">
                          Selesai
                        </button>
                      </form>
                    @endif

                    <form action="{{ route('sigap-kgb.destroy', $item->id) }}" method="POST" class="inline form-delete">
                      @csrf
                      @method('DELETE')
                      <button type="button" class="btn-delete px-2.5 py-1 rounded border border-red-500 text-red-600 hover:bg-red-600 hover:text-white transition shadow-2xs"
                              data-judul="KGB {{ $item->user->name ?? '' }}">
                        Hapus
                      </button>
                    </form>
                  @endhasanyrole
                </div>
              </td>
            </tr>
          @php endforeach; @endphp
        @else
          <tr>
            <td colspan="7" class="px-4 py-8 text-center text-gray-400">
              Belum ada data riwayat KGB. Klik <b>+ Input SK KGB</b> untuk mulai merekam data pegawai.
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>
</div>

<div class="mt-4">
  @if(isset($riwayats) && method_exists($riwayats, 'links'))
    {{ $riwayats->links() }}
  @endif
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
        title: 'Hapus Data KGB?',
        html: `Data <b>${judul}</b> akan dihapus permanen!`,
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