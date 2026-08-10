@extends('layouts.app')

@push('head')
<!-- TomSelect CSS untuk Searchable Dropdown -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<style>
  .ts-control {
    border-radius: 0.5rem !important;
    padding: 0.5rem 0.75rem !important;
    border-color: #d1d5db !important;
    font-size: 0.875rem !important;
  }
  .ts-control:focus-within {
    border-color: #7a2222 !important;
    box-shadow: 0 0 0 2px rgba(122, 34, 34, 0.15) !important;
  }
</style>
@endpush

@section('content')
<!-- Header & Navigation -->
<div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">Detail Batch</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 flex items-center gap-3">
      {{ $batch->nama_batch }}
      @if($batch->status === 'aktif')
        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-emerald-50 border-emerald-200 text-emerald-700">
          AKTIF
        </span>
      @elseif($batch->status === 'mendatang')
        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-blue-50 border-blue-200 text-blue-700">
          MENDATANG
        </span>
      @else
        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-gray-50 border-gray-200 text-gray-700">
          SELESAI
        </span>
      @endif
    </h1>
  </div>

  <div class="flex items-center gap-2">
    <a href="{{ route('magang.index') }}"
       class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
      &larr; Kembali
    </a>

    <!-- Action Tambah Mahasiswa khusus Admin & Verif Magang -->
    @hasanyrole('admin|verif_magang')
      <button type="button"
              onclick="openAddPesertaModal()"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition-colors">
        + Tambah Mahasiswa
      </button>
    @endhasanyrole

    <!-- Action Join Batch khusus Peserta Magang -->
    @role('magang')
      @php
        $alreadyJoined = $batch->peserta->contains(auth()->id());
      @endphp

      @if($alreadyJoined)
        <span class="px-3.5 py-2 rounded-xl bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-300">
          ✓ Sudah Terdaftar
        </span>
      @elseif($batch->status === 'aktif')
        <button type="button"
                onclick="openJoinModal()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition-colors">
          + Join Batch Ini
        </button>
      @endif
    @endrole
  </div>
</div>

<!-- Grid Ringkasan / Overview Card -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">
  <!-- Informasi Batch -->
  <div class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
    <h2 class="font-bold text-gray-900 border-b pb-2">Informasi Batch Magang</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
      <div>
        <p class="text-xs text-gray-500 font-medium">Periode Pelaksanaan</p>
        <p class="font-semibold text-gray-800 mt-0.5">
          {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->isoFormat('D MMMM Y') }} — 
          {{ \Carbon\Carbon::parse($batch->tanggal_selesai)->isoFormat('D MMMM Y') }}
        </p>
      </div>

      <div>
        <p class="text-xs text-gray-500 font-medium">Durasi Magang</p>
        <p class="font-semibold text-gray-800 mt-0.5">
          {{ \Carbon\Carbon::parse($batch->tanggal_mulai)->diffInDays($batch->tanggal_selesai) }} Hari
        </p>
      </div>
    </div>

    <div>
      <p class="text-xs text-gray-500 font-medium mb-1">Deskripsi & Catatan</p>
      <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm text-gray-700 leading-relaxed">
        {{ $batch->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
      </div>
    </div>
  </div>

  <!-- Statistik Kuota & Progress -->
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm flex flex-col justify-between">
    <div>
      <h2 class="font-bold text-gray-900 border-b pb-2">Keterisian Kuota</h2>
      
      @php
        $totalPeserta = $batch->peserta->count();
        $persentase = $batch->kuota > 0 ? min(100, round(($totalPeserta / $batch->kuota) * 100)) : 0;
      @endphp

      <div class="mt-4 text-center">
        <span class="text-4xl font-extrabold text-maroon">{{ $totalPeserta }}</span>
        <span class="text-lg font-semibold text-gray-500">/ {{ $batch->kuota }} Peserta</span>
      </div>

      <!-- Progress Bar -->
      <div class="w-full bg-gray-200 rounded-full h-2.5 mt-4 overflow-hidden">
        <div class="bg-maroon h-2.5 rounded-full transition-all duration-300" style="width: {{ $persentase }}%"></div>
      </div>
      <p class="text-right text-xs text-gray-500 mt-1.5 font-medium">{{ $persentase }}% terisi</p>
    </div>

    <div class="mt-4 pt-3 border-t text-xs text-gray-500">
      * Mahasiswa yang telah bergabung dapat mulai mengisi logbook kegiatan harian.
    </div>
  </div>
</div>

<!-- Daftar Peserta Magang -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-6">
  <div class="px-5 py-4 border-b bg-gray-50 flex items-center justify-between">
    <div>
      <h2 class="font-bold text-gray-900">Daftar Peserta Mahasiswa</h2>
      <p class="text-xs text-gray-500 mt-0.5">Daftar mahasiswa yang terdaftar pada batch magang ini.</p>
    </div>
    <span class="text-xs font-semibold px-3 py-1 bg-white border rounded-lg text-gray-700">
      Total: {{ $batch->peserta->count() }} Orang
    </span>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-5 py-3 text-left">Nama Mahasiswa</th>
          <th class="px-5 py-3 text-left">Instansi / Universitas</th>
          <th class="px-5 py-3 text-left">Jurusan</th>
          <th class="px-5 py-3 text-left">Tanggal Bergabung</th>
          <th class="px-5 py-3 text-left">Status</th>
          @hasanyrole('admin|verif_magang')
            <th class="px-5 py-3 text-right">Aksi</th>
          @endhasanyrole
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($batch->peserta as $peserta)
          <tr class="hover:bg-gray-50/50">
            <td class="px-5 py-3 font-medium text-gray-900">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-maroon/10 text-maroon font-bold flex items-center justify-center text-xs">
                  {{ strtoupper(substr($peserta->name, 0, 2)) }}
                </div>
                <div>
                  <div class="font-semibold text-gray-900">{{ $peserta->name }}</div>
                  <div class="text-xs text-gray-500 font-normal">{{ $peserta->email }}</div>
                </div>
              </div>
            </td>
            <td class="px-5 py-3 text-gray-700 font-medium">
              {{ $peserta->pivot->instansi_asal ?: '-' }}
            </td>
            <td class="px-5 py-3 text-gray-600">
              {{ $peserta->pivot->jurusan ?: '-' }}
            </td>
            <td class="px-5 py-3 text-gray-600">
              {{ \Carbon\Carbon::parse($peserta->pivot->created_at)->isoFormat('D MMM Y, HH:mm') }}
            </td>
            <td class="px-5 py-3">
              <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-medium border bg-emerald-50 border-emerald-200 text-emerald-700">
                {{ strtoupper($peserta->pivot->status) }}
              </span>
            </td>

            <!-- Kolom Edit & Hapus Peserta khusus Admin & Verif Magang -->
            @hasanyrole('admin|verif_magang')
              <td class="px-5 py-3 text-right">
                <div class="inline-flex items-center gap-1.5">
                  <!-- Tombol Edit -->
                  <button type="button"
                          onclick="openEditPesertaModal({{ $peserta->id }}, '{{ addslashes($peserta->name) }}', '{{ addslashes($peserta->pivot->instansi_asal) }}', '{{ addslashes($peserta->pivot->jurusan) }}')"
                          class="px-2.5 py-1 rounded-lg border border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-600 hover:text-white text-xs font-semibold transition-colors">
                    Edit
                  </button>

                  <!-- Tombol Hapus -->
                  <form action="{{ route('magang.batch.remove-peserta', [$batch->id, $peserta->id]) }}" 
                        method="POST" 
                        onsubmit="return confirmRemovePeserta(event, '{{ $peserta->name }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-2.5 py-1 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white text-xs font-semibold transition-colors">
                      Hapus
                    </button>
                  </form>
                </div>
              </td>
            @endhasanyrole
          </tr>
        @empty
          <tr>
            <td colspan="{{ auth()->user()->hasAnyRole(['admin', 'verif_magang']) ? 6 : 5 }}" class="px-5 py-8 text-center text-gray-500">
              Belum ada peserta yang bergabung pada batch magang ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Pop-Up Tambah Mahasiswa (Searchable Dropdown) -->
@hasanyrole('admin|verif_magang')
<div id="modalAddPeserta" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
    <div class="flex items-center justify-between border-b pb-3">
      <h3 class="text-lg font-bold text-gray-900">Tambah Mahasiswa Magang</h3>
      <button type="button" onclick="closeAddPesertaModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>

    <form action="{{ route('magang.batch.add-peserta', $batch->id) }}" method="POST" class="mt-4 space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
          Cari Mahasiswa (Role Magang) <span class="text-red-500">*</span>
        </label>
        <select id="userSelect" name="user_id" required placeholder="Cari nama atau email mahasiswa...">
          <option value="">Cari nama atau email mahasiswa...</option>
          @foreach($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
          @endforeach
        </select>
        @if($users->isEmpty())
          <p class="text-[11px] text-amber-600 mt-1">* Tidak ada mahasiswa ber-role magang yang tersedia untuk ditambahkan.</p>
        @endif
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Instansi / Asal Kampus <span class="text-red-500">*</span></label>
        <input type="text" name="instansi_asal" required placeholder="Contoh: Universitas Hasanuddin / UPN"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jurusan / Program Studi <span class="text-red-500">*</span></label>
        <input type="text" name="jurusan" required placeholder="Contoh: Teknik Geologi / Ilmu Komputer"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div class="flex justify-end gap-2 pt-4 border-t">
        <button type="button" onclick="closeAddPesertaModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800" {{ $users->isEmpty() ? 'disabled' : '' }}>
          Simpan Peserta
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Pop-Up Edit Data Peserta Magang -->
<div id="modalEditPeserta" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
    <div class="flex items-center justify-between border-b pb-3">
      <h3 class="text-lg font-bold text-gray-900">Edit Data Peserta Magang</h3>
      <button type="button" onclick="closeEditPesertaModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>

    <form id="formEditPeserta" method="POST" class="mt-4 space-y-4">
      @csrf
      @method('PUT')

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Mahasiswa</label>
        <input type="text" id="editNamaMahasiswa" disabled class="w-full bg-gray-100 rounded-lg px-3 py-2 text-sm font-semibold text-gray-700">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Instansi / Asal Kampus <span class="text-red-500">*</span></label>
        <input type="text" id="editInstansiAsal" name="instansi_asal" required placeholder="Contoh: Universitas Hasanuddin / UPN"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jurusan / Program Studi <span class="text-red-500">*</span></label>
        <input type="text" id="editJurusan" name="jurusan" required placeholder="Contoh: Teknik Geologi / Ilmu Komputer"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div class="flex justify-end gap-2 pt-4 border-t">
        <button type="button" onclick="closeEditPesertaModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
@endhasanyrole

<!-- Modal Pop-Up Join Batch (Khusus Mahasiswa) -->
@role('magang')
<div id="modalJoinBatch" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
  <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl border border-gray-100">
    <div class="flex items-center justify-between border-b pb-3">
      <h3 class="text-lg font-bold text-gray-900">Konfirmasi Join Batch</h3>
      <button type="button" onclick="closeJoinModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
    </div>

    <form action="{{ route('magang.batch.join', $batch->id) }}" method="POST" class="mt-4 space-y-4">
      @csrf

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Instansi / Asal Kampus <span class="text-red-500">*</span></label>
        <input type="text" name="instansi_asal" required placeholder="Contoh: Universitas Hasanuddin / UPN"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div>
        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jurusan / Program Studi <span class="text-red-500">*</span></label>
        <input type="text" name="jurusan" required placeholder="Contoh: Teknik Geologi / Ilmu Komputer"
               class="w-full rounded-lg px-3 py-2 text-sm">
      </div>

      <div class="flex justify-end gap-2 pt-4 border-t">
        <button type="button" onclick="closeJoinModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-100">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800">
          Bergabung Sekarang
        </button>
      </div>
    </form>
  </div>
</div>
@endrole
@endsection

@push('scripts')
<!-- TomSelect JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
let tomSelectInstance = null;

document.addEventListener('DOMContentLoaded', function() {
  const userSelectEl = document.getElementById('userSelect');
  if (userSelectEl) {
    tomSelectInstance = new TomSelect('#userSelect', {
      create: false,
      sortField: { field: "text", direction: "asc" }
    });
  }
});

function openEditPesertaModal(userId, userName, instansi, jurusan) {
  const form = document.getElementById('formEditPeserta');
  form.action = `/dashboard/magang/batch/{{ $batch->id }}/peserta/${userId}`;
  
  document.getElementById('editNamaMahasiswa').value = userName;
  document.getElementById('editInstansiAsal').value = instansi;
  document.getElementById('editJurusan').value = jurusan;

  document.getElementById('modalEditPeserta')?.classList.remove('hidden');
}

function closeEditPesertaModal() {
  document.getElementById('modalEditPeserta')?.classList.add('hidden');
}

function confirmRemovePeserta(event, userName) {
  if (typeof Swal !== 'undefined') {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
      title: 'Keluarkan Peserta?',
      text: `Apakah Anda yakin ingin mengeluarkan ${userName} dari batch magang ini? Seluruh logbook terkait juga akan dibersihkan.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#7a2222',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Keluarkan',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    });
    return false;
  }
  return confirm(`Apakah Anda yakin ingin mengeluarkan ${userName} dari batch magang ini?`);
}

function openAddPesertaModal() {
  document.getElementById('modalAddPeserta')?.classList.remove('hidden');
}

function closeAddPesertaModal() {
  document.getElementById('modalAddPeserta')?.classList.add('hidden');
}

function openJoinModal() {
  document.getElementById('modalJoinBatch')?.classList.remove('hidden');
}

function closeJoinModal() {
  document.getElementById('modalJoinBatch')?.classList.add('hidden');
}
</script>
@endpush