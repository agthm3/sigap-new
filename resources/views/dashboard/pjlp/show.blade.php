@extends('layouts.app')

@section('content')
<div x-data="pjlpVerifComponent()">

  <!-- Header -->
  <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
    <div class="flex items-center gap-3">
      <a href="{{ route('sigap-pjlp.monitoring', ['bulan_tahun' => $bulanTahun]) }}" 
         class="p-2 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 transition">
        ←
      </a>
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
          Detail Logbook: <span class="text-maroon">{{ $targetUser->name }}</span>
        </h1>
        <p class="text-xs text-gray-500 mt-0.5">
          Periode: <b>{{ \Carbon\Carbon::createFromFormat('Y-m', $bulanTahun)->translatedFormat('F Y') }}</b> | NIP/NIK/Email: {{ $targetUser->email }}
        </p>
      </div>
    </div>

    <!-- Export PDF -->
    @if($isSiapExport)
      <a href="{{ route('sigap-pjlp.export-pdf', $periode->id) }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition shadow-sm">
        📄 Export PDF Laporan
      </a>
    @endif
  </section>

  <!-- Dokumen Daftar Gaji -->
  <div class="rounded-2xl border border-gray-200 bg-white p-4 mt-4 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
    <div class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl {{ $hasDaftarGaji ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center font-bold text-sm">
        PDF
      </div>
      <div>
        <p class="text-xs font-bold text-gray-900">Dokumen Daftar Gaji Periode Ini</p>
        <p class="text-[11px] text-gray-500">
          {{ $hasDaftarGaji ? 'Berkas sudah diunggah.' : 'Berkas belum diunggah oleh PJLP atau Petugas.' }}
        </p>
      </div>
    </div>
    @if($hasDaftarGaji)
      <a href="{{ asset('storage/' . $periode->file_daftar_gaji) }}" target="_blank"
         class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50">
        Lihat Dokumen Gaji
      </a>
    @endif
  </div>

  <!-- Tabel Hari Kerja & Verifikasi -->
  <div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-xs mt-4">
    <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
      <h2 class="font-bold text-gray-900 text-sm">Logbook Harian & Verifikasi Evidence</h2>
      <span class="text-xs font-bold text-gray-500">{{ $totalTerisi }} / {{ $totalHariKerja }} Hari Terisi</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-600 border-b border-gray-200">
          <tr>
            <th class="px-4 py-3.5 text-left font-bold">Hari/Tanggal</th>
            <th class="px-4 py-3.5 text-center font-bold w-24">Evidence</th>
            <th class="px-4 py-3.5 text-left font-bold">Deskripsi Pekerjaan</th>
            <th class="px-4 py-3.5 text-left font-bold">Audit Input</th>
            <th class="px-4 py-3.5 text-center font-bold w-32">Status</th>
            <th class="px-4 py-3.5 text-center font-bold w-48">Aksi Verifikasi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($logbooks as $item)
            <tr class="hover:bg-gray-50/80 transition-colors">
              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="font-bold text-gray-900">{{ $item->hari }}</div>
                <div class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</div>
              </td>

              <!-- Foto Evidence -->
              <td class="px-4 py-3.5 text-center">
                @if($item->foto_evidence)
                  <img src="{{ asset('storage/' . $item->foto_evidence) }}" 
                       alt="Evidence" 
                       class="w-12 h-12 rounded-lg object-cover mx-auto ring-1 ring-gray-200 cursor-pointer shadow-2xs hover:scale-105 transition"
                       @click="viewImage('{{ asset('storage/' . $item->foto_evidence) }}')">
                @else
                  <span class="text-xs text-gray-400 italic">Kosong</span>
                @endif
              </td>

              <!-- Deskripsi -->
              <td class="px-4 py-3.5">
                <p class="text-xs text-gray-800 leading-relaxed font-medium">
                  {{ $item->deskripsi_pekerjaan ?: '-' }}
                </p>
                @if($item->catatan_verifikator)
                  <p class="text-[11px] text-red-600 font-semibold mt-1">Catatan: {{ $item->catatan_verifikator }}</p>
                @endif
              </td>

              <!-- Audit Trail Input -->
              <td class="px-4 py-3.5 text-xs text-gray-500">
                @if($item->updatedBy)
                  <div>Diubah: <b class="text-gray-700">{{ $item->updatedBy->name }}</b></div>
                  <div class="text-[10px] text-gray-400">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
                @else
                  -
                @endif
              </td>

              <!-- Status -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                @if($item->status === 'terverifikasi')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 border border-emerald-200 text-emerald-700">
                    Disetujui
                  </span>
                @elseif($item->status === 'diajukan')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 border border-blue-200 text-blue-700">
                    Menunggu Verif
                  </span>
                @elseif($item->status === 'ditolak')
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 border border-red-200 text-red-700">
                    Ditolak
                  </span>
                @else
                  <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium bg-gray-100 border border-gray-200 text-gray-400">
                    Belum Diisi
                  </span>
                @endif
              </td>

              <!-- Aksi Verifikasi / Isi Atas Nama -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1.5">
                  @if($item->status === 'diajukan' || $item->status === 'ditolak')
                    <!-- Terima Form -->
                    <form action="{{ route('sigap-pjlp.verify', $item->id) }}" method="POST" class="inline">
                      @csrf
                      <input type="hidden" name="status" value="terverifikasi">
                      <button type="submit" class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-2xs" title="Terima">
                        ✓ Terima
                      </button>
                    </form>

                    <!-- Tolak Button Trigger Modal -->
                    <button type="button" @click="openRejectModal({{ $item->id }})" class="px-2.5 py-1 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 shadow-2xs" title="Tolak">
                      ✕ Tolak
                    </button>
                  @endif

                  <!-- Isikan / Edit Atas Nama -->
                  <button type="button" @click="openAdminEditModal({{ $item->id }})" class="px-2.5 py-1 border border-gray-300 text-gray-700 rounded-lg text-xs font-medium hover:bg-gray-100 shadow-2xs">
                    ✎ Isikan
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-xs">
                Tidak ada hari kerja.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL TOLAK EVIDENCE -->
  <div x-show="rejectModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div @click.away="rejectModalOpen = false" class="w-full max-w-md bg-white rounded-2xl p-5 shadow-2xl">
      <h3 class="font-bold text-gray-900 text-sm mb-2">Tolak Evidence PJLP</h3>
      <p class="text-xs text-gray-500 mb-3">Tuliskan alasan penolakan agar PJLP dapat memperbaiki fotonya.</p>
      
      <form :action="'/sigap-pjlp/logbook/' + activeLogbookId + '/verify'" method="POST">
        @csrf
        <input type="hidden" name="status" value="ditolak">
        <textarea name="catatan_verifikator" rows="3" required placeholder="Alasan penolakan..." class="w-full text-xs rounded-xl p-3 mb-3"></textarea>
        
        <div class="flex justify-end gap-2">
          <button type="button" @click="rejectModalOpen = false" class="px-3 py-1.5 rounded-xl border text-xs font-semibold text-gray-700">Batal</button>
          <button type="submit" class="px-4 py-1.5 rounded-xl bg-red-600 text-white text-xs font-bold hover:bg-red-700">Kirim Penolakan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL ADMIN EDIT / ISI ATAS NAMA DENGAN COMPRESS CLIENT SIDE -->
  <div x-show="adminEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div @click.away="adminEditModalOpen = false" class="w-full max-w-lg bg-white rounded-2xl overflow-hidden shadow-2xl">
      <div class="px-5 py-4 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-900 text-sm">Isi/Edit Logbook Atas Nama PJLP</h3>
        <button type="button" @click="adminEditModalOpen = false" class="text-gray-400 font-bold">✕</button>
      </div>

      <form :action="'/sigap-pjlp/logbook/' + activeLogbookId + '/admin-update'" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">Upload Evidence Foto</label>
          <input type="file" name="foto_evidence" accept="image/*" class="w-full text-xs">
        </div>
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Pekerjaan</label>
          <textarea name="deskripsi_pekerjaan" rows="3" required x-model="activeLogbookData.deskripsi_pekerjaan" class="w-full text-xs rounded-xl p-3"></textarea>
        </div>
        <div class="flex justify-end gap-2 pt-3 border-t">
          <button type="button" @click="adminEditModalOpen = false" class="px-3 py-1.5 rounded-xl border text-xs">Batal</button>
          <button type="submit" class="px-4 py-1.5 rounded-xl bg-maroon text-white text-xs font-bold hover:bg-maroon-800">Simpan (Auto-Verify)</button>
        </div>
      </form>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function pjlpVerifComponent() {
  return {
    rejectModalOpen: false,
    adminEditModalOpen: false,
    activeLogbookId: null,
    activeLogbookData: { deskripsi_pekerjaan: '' },
    logbooks: {!! json_encode($logbooks) !!},

    openRejectModal(id) {
      this.activeLogbookId = id;
      this.rejectModalOpen = true;
    },

    openAdminEditModal(id) {
      const item = this.logbooks.find(l => l.id === id);
      if (!item) return;
      this.activeLogbookId = id;
      this.activeLogbookData = Object.assign({}, item);
      this.adminEditModalOpen = true;
    },

    viewImage(url) {
      Swal.fire({
        imageUrl: url,
        imageAlt: 'Evidence Foto',
        showConfirmButton: false,
        showCloseButton: true,
      });
    }
  };
}
</script>
@endpush