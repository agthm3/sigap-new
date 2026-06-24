@extends('layouts.app')

@section('content')
<div class="max-w-6xl">
  <div class="mb-4">
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Edit <span class="text-maroon">Kegiatan & Peserta</span>
    </h1>
    <p class="text-sm text-gray-600 mt-1">Di sini admin bisa ubah detail kegiatan dan urutan absen peserta.</p>
  </div>

  <form action="{{ route('sigap-daftar-hadir.update', $kegiatan->uuid) }}" enctype="multipart/form-data"  method="POST" class="space-y-4">
    @csrf
    @method('PUT')

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan</label>
          <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan', $kegiatan->nama_kegiatan) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
          <input type="text" name="waktu" value="{{ old('waktu', $kegiatan->waktu) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Hari/Tanggal</label>
          <input type="text" name="hari_tanggal" value="{{ old('hari_tanggal', $kegiatan->hari_tanggal) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
          <input type="text" name="tempat" value="{{ old('tempat', $kegiatan->tempat) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4 mt-2">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Undangan (PDF)</label>
          <input type="file" name="undangan_pdf" accept="application/pdf"
                 class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-xl file:border-0
                        file:text-sm file:font-semibold
                        file:bg-maroon-50 file:text-maroon
                        hover:file:bg-maroon-100 focus:outline-none">
          
          @if($kegiatan->undangan_path)
            <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span>Undangan sudah terupload: 
                <a href="{{ asset('storage/' . $kegiatan->undangan_path) }}" target="_blank" class="underline font-semibold hover:text-emerald-800">
                  Lihat File Dokumen
                </a>
              </span>
            </div>
          @else
            <p class="text-xs text-gray-400 mt-1">Belum ada file undangan yang dilampirkan.</p>
          @endif
          @error('undangan_pdf') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center md:mt-6">
          <input type="checkbox" name="buat_sertifikat" value="1" id="buat_sertifikat"
                 {{ old('buat_sertifikat', $kegiatan->buat_sertifikat) ? 'checked' : '' }}
                 class="h-5 w-5 rounded border-gray-300 text-maroon focus:ring-maroon cursor-pointer">
          <label for="buat_sertifikat" class="ml-2 block text-sm font-medium text-gray-900 cursor-pointer">
            Buatkan Sertifikat
          </label>
        </div>
      </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between mb-3">
        <h2 class="font-semibold text-gray-900">Edit Peserta & Urutan Absen</h2>
        <span class="text-sm text-gray-500">{{ $kegiatan->peserta->count() }} peserta</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-3 py-2 text-left">Urut</th>
              <th class="px-3 py-2 text-left">Nama</th>
              <th class="px-3 py-2 text-left">Instansi</th>
              <th class="px-3 py-2 text-left">Gender</th>
              <th class="px-3 py-2 text-left">No HP</th>
              <th class="px-3 py-2 text-left">Email</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @foreach($kegiatan->peserta as $p)
              <tr>
                <td class="px-3 py-2 w-24">
                  <input type="number" min="1"
                         name="peserta[{{ $p->id }}][urutan_absen]"
                         value="{{ old("peserta.$p->id.urutan_absen", $p->urutan_absen) }}"
                         class="w-20 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                </td>
                <td class="px-3 py-2">
                  <input type="text"
                         name="peserta[{{ $p->id }}][nama]"
                         value="{{ old("peserta.$p->id.nama", $p->nama) }}"
                         class="w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                </td>
                <td class="px-3 py-2">
                  <input type="text"
                         name="peserta[{{ $p->id }}][instansi]"
                         value="{{ old("peserta.$p->id.instansi", $p->instansi) }}"
                         class="w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                </td>
                <td class="px-3 py-2">
                  <select name="peserta[{{ $p->id }}][gender]"
                          class="w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                    <option value="L" @selected(old("peserta.$p->id.gender", $p->gender) === 'L')>L</option>
                    <option value="P" @selected(old("peserta.$p->id.gender", $p->gender) === 'P')>P</option>
                  </select>
                </td>
                <td class="px-3 py-2">
                  <input type="text"
                         name="peserta[{{ $p->id }}][no_hp]"
                         value="{{ old("peserta.$p->id.no_hp", $p->no_hp) }}"
                         class="w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                </td>
                <td class="px-3 py-2">
                  <input type="email"
                         name="peserta[{{ $p->id }}][email]"
                         value="{{ old("peserta.$p->id.email", $p->email) }}"
                         class="w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white font-semibold hover:bg-maroon-800">
        Simpan Perubahan
      </button>
      <a href="{{ route('sigap-daftar-hadir.show', $kegiatan->uuid) }}" class="px-4 py-2 rounded-xl border text-gray-700 hover:bg-gray-50">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection