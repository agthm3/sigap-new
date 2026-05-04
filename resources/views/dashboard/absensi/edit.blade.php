@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
  <section>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Ubah <span class="text-maroon">Absensi</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Admin / verificator dapat mengubah jam, tanggal, status, dan lokasi absensi.
    </p>
  </section>

  @if ($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
      <ul class="list-disc pl-5 space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="mb-4">
      <div class="text-sm font-semibold text-gray-900">
        {{ $absensi->user->name ?? '-' }}
      </div>
      <div class="text-xs text-gray-500">
        Rekam absensi tanggal {{ $absensi->absen_date?->format('d-m-Y') ?? $absensi->absen_date }}
      </div>
    </div>

    <form action="{{ route('sigap-absensi.update', $absensi->id) }}" method="POST" class="space-y-4">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Absen</label>
          <input type="date" name="absen_date" value="{{ old('absen_date', optional($absensi->absen_date)->format('Y-m-d') ?? $absensi->absen_date) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jam Absen</label>
          <input type="time" name="absen_time" value="{{ old('absen_time', \Carbon\Carbon::parse($absensi->absen_time)->format('H:i')) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
          <select name="keterangan" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @php $ket = old('keterangan', $absensi->keterangan); @endphp
            <option value="HADIR" {{ $ket === 'HADIR' ? 'selected' : '' }}>HADIR</option>
            <option value="TERLAMBAT" {{ $ket === 'TERLAMBAT' ? 'selected' : '' }}>TERLAMBAT</option>
            <option value="IZIN" {{ $ket === 'IZIN' ? 'selected' : '' }}>IZIN</option>
            <option value="ALFA" {{ $ket === 'ALFA' ? 'selected' : '' }}>ALFA</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
          <input type="text" name="location_text" value="{{ old('location_text', $absensi->location_text ?? 'Balaikota Makassar') }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
          <input type="text" name="latitude" value="{{ old('latitude', $absensi->latitude) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
          <input type="text" name="longitude" value="{{ old('longitude', $absensi->longitude) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Jarak Meter</label>
          <input type="number" step="0.01" name="distance_meter" value="{{ old('distance_meter', $absensi->distance_meter) }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
        </div>

        <div class="flex items-center gap-2 pt-7">
          <input type="checkbox" name="is_outside_radius" value="1"
                 class="rounded border-gray-300 text-maroon focus:ring-maroon"
                 {{ old('is_outside_radius', $absensi->is_outside_radius) ? 'checked' : '' }}>
          <label class="text-sm text-gray-700">Tandai sebagai di luar radius</label>
        </div>
      </div>

      <div class="flex items-center gap-2 pt-2">
        <button type="submit"
                class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
          Simpan Perubahan
        </button>

        <a href="{{ route('sigap-absensi.dashboard') }}"
           class="px-4 py-2 rounded-xl border text-sm font-semibold hover:bg-gray-50">
          Kembali
        </a>
      </div>
    </form>
  </div>
</div>
@endsection