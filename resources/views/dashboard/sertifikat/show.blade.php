@extends('layouts.app')

@section('content')
<div x-data="{
    openModal: false,
    openModalEdit: false,
    editData: {
        id: '',
        nomor_sertifikat: '',
        nama_penerima: '',
        instansi: '',
        keterangan: '',
        status: 'Aktif'
    },
    confirmEdit(item) {
        Swal.fire({
            title: 'Peringatan Penting!',
            text: 'Tindakan mengubah data ini dapat mempengaruhi validitas dan isi sertifikat resmi milik peserta. Pastikan data yang dimasukkan benar. Lakukan dengan hati-hati!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#7a2222',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Lanjutkan Edit',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.editData = {
                    id: item.id,
                    nomor_sertifikat: item.nomor_sertifikat || '',
                    nama_penerima: item.nama_penerima || '',
                    instansi: item.instansi || '',
                    keterangan: item.keterangan || '',
                    status: item.status || 'Aktif'
                };
                this.openModalEdit = true;
            }
        });
    },
    confirmDelete(id, nomor) {
        Swal.fire({
            title: 'Hapus Sertifikat?',
            text: 'Sertifikat dengan nomor ' + nomor + ' akan dihapus secara permanen dan tidak dapat diverifikasi lagi!',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
    }
}">
<!-- ================= HEADER ================= -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col gap-2">
    <h1 class="text-2xl font-extrabold text-gray-900">
      Kelola Sertifikat
    </h1>
    <p class="text-sm text-gray-600">
      Kegiatan: <strong>{{ $kegiatan->nama_kegiatan }}</strong>
    </p>
  </div>
</section>

<!-- ================= ACTION BAR ================= -->
<section class="max-w-7xl mx-auto px-4">
  <div class="bg-white border rounded-xl p-4 flex flex-wrap gap-3 items-center justify-between">

    <div class="flex flex-wrap gap-2 items-center">
      <a href="{{ route('sertifikat.template') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border hover:bg-gray-50 text-sm">
        ⬇️ Download Template Excel
      </a>

      <!-- TOMBOL EXPORT PDF -->
      <a href="{{ route('sertifikat.exportPdf', $kegiatan->id) }}"
         target="_blank"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 text-sm font-medium">
        📄 Export List ke PDF
      </a>

      <form method="POST"
            action="{{ route('sertifikat.import') }}"
            enctype="multipart/form-data"
            class="inline">

        @csrf

        <input type="hidden"
            name="kegiatan_id"
            value="{{ $kegiatan->id }}">

        <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border hover:bg-gray-50 cursor-pointer text-sm">
          ⬆️ Upload Excel
          <input type="file"
              name="file"
              accept=".xls,.xlsx"
              class="hidden"
              onchange="this.form.submit()">
        </label>

      </form>
    </div>

    <button
      @click="openModal=true"
      class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
      ➕ Tambah Sertifikat Manual
    </button>

  </div>
</section>

<!-- ================= TABLE ================= -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">

    <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 font-semibold">
      Daftar Sertifikat
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b bg-gray-50/50">
          <tr>
            <th class="px-4 py-3 text-left">Nomor Sertifikat</th>
            <th class="px-4 py-3 text-left">Nama Penerima</th>
            <th class="px-4 py-3 text-left">Instansi</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-center">Aksi</th>
          </tr>
        </thead>

        <tbody class="divide-y">
          @forelse($sertifikat as $item)
          <tr class="hover:bg-gray-50/60 transition">
            <td class="px-4 py-3 font-medium text-maroon whitespace-nowrap">
              {{ $item->nomor_sertifikat }}
            </td>

            <td class="px-4 py-3 font-semibold text-gray-800">
              {{ $item->nama_penerima }}
            </td>

            <td class="px-4 py-3 text-gray-600">
              {{ $item->instansi ?? '-' }}
            </td>

            <td class="px-4 py-3">
              <span class="px-2 py-1 rounded text-xs font-medium
                {{ $item->status == 'Aktif'
                ? 'bg-emerald-50 text-emerald-700'
                : 'bg-gray-100 text-gray-600' }}">
                {{ $item->status ?? 'Aktif' }}
              </span>
            </td>

            <td class="px-4 py-3">
              <div class="flex items-center justify-center gap-1.5">
                <!-- VIEW -->
                <a href="{{ route('sertifikat.view', $item->id) }}"
                   target="_blank"
                   class="px-2.5 py-1.5 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-medium inline-flex items-center gap-1">
                  👁️ View
                </a>

                <!-- EDIT WITH SWEETALERT WARNING -->
                <button type="button"
                   @click="confirmEdit({{ json_encode($item) }})"
                   class="px-2.5 py-1.5 rounded-md border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 text-xs font-medium inline-flex items-center gap-1">
                  ✏️ Edit
                </button>

                <!-- HAPUS WITH SWEETALERT CONFIRMATION -->
                <button type="button"
                   @click="confirmDelete('{{ $item->id }}', '{{ $item->nomor_sertifikat }}')"
                   class="px-2.5 py-1.5 rounded-md border border-red-300 bg-red-50 text-red-700 hover:bg-red-100 text-xs font-medium inline-flex items-center gap-1">
                  🗑️ Hapus
                </button>

                <!-- Hidden Delete Form -->
                <form id="form-delete-{{ $item->id }}"
                      action="{{ route('sertifikat.destroy', $item->id) }}"
                      method="POST"
                      class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              Belum ada sertifikat
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 py-3 text-sm text-gray-500 bg-gray-50">
      Total: {{ $sertifikat->count() }} sertifikat
    </div>

  </div>
</section>

<!-- ================= MODAL TAMBAH MANUAL ================= -->
<div
  x-show="openModal"
  x-cloak
  x-transition
  class="fixed inset-0 z-50 flex items-center justify-center">

  <div class="absolute inset-0 bg-black/40" @click="openModal=false"></div>

  <div class="relative bg-white w-full max-w-lg p-5 rounded-2xl shadow-2xl overflow-hidden">
    <div class="mb-4">
      <h2 class="text-gray-900 text-lg font-bold">Tambah Sertifikat Manual</h2>
      <p class="text-gray-500 text-xs">
        Digunakan jika hanya menambahkan satu atau beberapa sertifikat.
      </p>
    </div>

    <form method="POST" action="{{ route('sertifikat.store') }}" class="space-y-3">
      @csrf

      <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">

      <div>
        <label class="text-sm font-semibold text-gray-700">Nomor Sertifikat</label>
        <input name="nomor_sertifikat" type="text" placeholder="MA-2025-XXX" required
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Nama Penerima</label>
        <input name="nama_penerima" type="text" placeholder="Nama lengkap" required
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Instansi</label>
        <input name="instansi" type="text" placeholder="Asal instansi / sekolah / OPD"
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Keterangan (Opsional)</label>
        <textarea name="keterangan" rows="2"
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon"
          placeholder="Contoh: Juara 1 kategori inovasi..."></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-3 border-t">
        <button type="button"
          @click="openModal=false"
          class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
          Batal
        </button>
        <button
          class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
          Simpan Sertifikat
        </button>
      </div>

    </form>
  </div>
</div>

<!-- ================= MODAL EDIT SERTIFIKAT ================= -->
<div
  x-show="openModalEdit"
  x-cloak
  x-transition
  class="fixed inset-0 z-50 flex items-center justify-center">

  <div class="absolute inset-0 bg-black/40" @click="openModalEdit=false"></div>

  <div class="relative bg-white w-full max-w-lg p-5 rounded-2xl shadow-2xl overflow-hidden">
    <div class="mb-4">
      <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded bg-amber-50 text-amber-800 text-xs font-medium mb-1">
        ⚠️ Mode Pengubahan Data Resmi
      </div>
      <h2 class="text-gray-900 text-lg font-bold">Edit Sertifikat Peserta</h2>
      <p class="text-gray-500 text-xs">
        Perubahan data ini langsung memperbarui tampilan sertifikat kanvas dan verifikasi portal publik.
      </p>
    </div>

    <form :action="'{{ url('/sertifikat/peserta') }}/' + editData.id" method="POST" class="space-y-3">
      @csrf
      @method('PUT')

      <div>
        <label class="text-sm font-semibold text-gray-700">Nomor Sertifikat</label>
        <input name="nomor_sertifikat" type="text" x-model="editData.nomor_sertifikat" required
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Nama Penerima</label>
        <input name="nama_penerima" type="text" x-model="editData.nama_penerima" required
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Instansi</label>
        <input name="instansi" type="text" x-model="editData.instansi"
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Status Sertifikat</label>
        <select name="status" x-model="editData.status"
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon">
          <option value="Aktif">Aktif</option>
          <option value="Nonaktif">Nonaktif</option>
        </select>
      </div>

      <div>
        <label class="text-sm font-semibold text-gray-700">Keterangan</label>
        <textarea name="keterangan" rows="2" x-model="editData.keterangan"
          class="mt-1 w-full rounded-lg border border-gray-300 p-2 text-sm focus:border-maroon focus:ring-maroon"></textarea>
      </div>

      <div class="flex items-center justify-end gap-2 pt-3 border-t">
        <button type="button"
          @click="openModalEdit=false"
          class="px-4 py-2 rounded-lg border text-sm hover:bg-gray-50">
          Batal
        </button>
        <button
          class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
          Simpan Perubahan
        </button>
      </div>

    </form>
  </div>
</div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection