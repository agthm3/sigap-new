@extends('layouts.app')
{{-- Tambahkan di atas, kalau belum ada --}}
@push('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
  .select2-container .select2-selection--single,
  .select2-container .select2-selection--multiple{
    min-height: 42px;
    border: 1px solid #d1d5db !important;
    border-radius: 0.75rem !important;
    padding: 0.25rem 0.5rem;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice{
    background: #7a2222;
    border: none;
    color: white;
    border-radius: 9999px;
    padding: 2px 8px;
    font-size: 12px;
  }
</style>
@endpush
@section('content')

<section class="mb-4">
  <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
    Buat <span class="text-maroon">Kegiatan PPD</span>
  </h1>
  <p class="text-sm text-gray-600 mt-0.5">
    Isi data kegiatan, kategori, pegawai, lalu sistem akan membuat lembar laporan otomatis.
  </p>
</section>

<div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
  <form action="{{ route('sigap-ppd.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block text-sm font-semibold text-gray-700 mb-1">Judul Kegiatan</label>
        <input type="text" name="judul" value="{{ old('judul') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon"
               placeholder="Masukkan judul kegiatan">
        @error('judul') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori Laporan</label>
        <select name="kategori" id="kategori"
                class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
          <option value="bimtek" {{ old('kategori') === 'bimtek' ? 'selected' : '' }}>Bimtek</option>
          <option value="koordinasi" {{ old('kategori') === 'koordinasi' ? 'selected' : '' }}>Koordinasi</option>
        </select>
        @error('kategori') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Jumlah Lembar</label>
        <input type="number" name="jumlah_lembar" id="jumlah_lembar"
               value="{{ old('jumlah_lembar') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon"
               min="1" max="20">
        <p class="text-xs text-gray-500 mt-1">Default: bimtek = 4, koordinasi = 1.</p>
        @error('jumlah_lembar') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Hari / Tanggal</label>
        <input type="text" name="hari_tanggal" value="{{ old('hari_tanggal') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon"
               placeholder="Rabu – Sabtu / 08 s.d 11 April 2026">
        @error('hari_tanggal') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Tempat</label>
        <input type="text" name="tempat" value="{{ old('tempat') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon"
               placeholder="Masukkan tempat kegiatan">
        @error('tempat') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

        <div class="md:col-span-2">
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-semibold text-gray-700">Pegawai yang Terlibat</label>

            <button type="button" id="addPegawaiBtn"
                    class="px-3 py-1.5 rounded-lg bg-maroon text-white text-xs font-semibold hover:bg-maroon-800">
            + Tambah Pegawai
            </button>
        </div>

        <div id="pegawaiWrapper" class="space-y-3">
            @php
            $oldPegawai = old('pegawai_ids', []);
            if (empty($oldPegawai)) $oldPegawai = [''];
            @endphp

            @foreach($oldPegawai as $index => $selectedId)
            <div class="pegawai-row flex gap-2 items-start">
                <div class="flex-1">
                <select name="pegawai_ids[]"
                        class="pegawai-select w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
                    <option value="">-- Pilih pegawai --</option>
                    @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" @selected((string)$selectedId === (string)$employee->id)>
                        {{ $employee->name }}{{ $employee->nip ? ' - '.$employee->nip : '' }}
                    </option>
                    @endforeach
                </select>
                </div>

                <button type="button"
                        class="removePegawaiBtn px-3 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50">
                Hapus
                </button>
            </div>
            @endforeach
        </div>

        @error('pegawai_ids') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        @error('pegawai_ids.*') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="mt-5 flex items-center justify-end gap-2">
      <a href="{{ route('sigap-ppd.index') }}"
         class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold hover:bg-gray-50">
        Batal
      </a>
      <button type="submit"
              class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
        Simpan Kegiatan
      </button>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const kategori = document.getElementById('kategori');
  const jumlah   = document.getElementById('jumlah_lembar');

  function syncDefault() {
    if (!jumlah.value) {
      jumlah.value = kategori.value === 'bimtek' ? 4 : 1;
    }
  }

  kategori.addEventListener('change', () => {
    jumlah.value = kategori.value === 'bimtek' ? 4 : 1;
  });

  syncDefault();
</script>
@endpush
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  function initPegawaiSelect() {
    $('.pegawai-select').select2({
      width: '100%',
      placeholder: '-- Pilih pegawai --',
      allowClear: true
    });
  }

  function bindRemoveButtons() {
    document.querySelectorAll('.removePegawaiBtn').forEach(btn => {
      btn.onclick = function () {
        const row = this.closest('.pegawai-row');
        const wrapper = document.getElementById('pegawaiWrapper');

        if (wrapper.querySelectorAll('.pegawai-row').length > 1) {
          if ($(row).find('.pegawai-select').data('select2')) {
            $(row).find('.pegawai-select').select2('destroy');
          }
          row.remove();
        } else {
          $(row).find('.pegawai-select').val(null).trigger('change');
        }
      };
    });
  }

  document.getElementById('addPegawaiBtn').addEventListener('click', function () {
    const wrapper = document.getElementById('pegawaiWrapper');

    const row = document.createElement('div');
    row.className = 'pegawai-row flex gap-2 items-start';
    row.innerHTML = `
      <div class="flex-1">
        <select name="pegawai_ids[]" class="pegawai-select w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
          <option value="">-- Pilih pegawai --</option>
          @foreach($employees as $employee)
            <option value="{{ $employee->id }}">
              {{ $employee->name }}{{ $employee->nip ? ' - '.$employee->nip : '' }}
            </option>
          @endforeach
        </select>
      </div>
      <button type="button"
              class="removePegawaiBtn px-3 py-2 rounded-xl border border-gray-300 text-gray-600 hover:bg-gray-50">
        Hapus
      </button>
    `;

    wrapper.appendChild(row);
    $(row).find('.pegawai-select').select2({
      width: '100%',
      placeholder: '-- Pilih pegawai --',
      allowClear: true
    });

    bindRemoveButtons();
  });

  document.addEventListener('DOMContentLoaded', function () {
    initPegawaiSelect();
    bindRemoveButtons();
  });
</script>
@endpush