@extends('layouts.app')

@section('content')
<div class="max-w-3xl">
  <div class="mb-4">
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Buat <span class="text-maroon">Kegiatan Daftar Hadir</span>
    </h1>
    <p class="text-sm text-gray-600 mt-1">Setelah disimpan, QR code akan muncul di halaman detail kegiatan.</p>
  </div>

  <form action="{{ route('sigap-daftar-hadir.store') }}" method="POST"
        class="space-y-6" enctype="multipart/form-data">
    @csrf

    {{-- ===== INFO KEGIATAN ===== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
      <h2 class="font-semibold text-gray-800">Informasi Kegiatan</h2>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kegiatan</label>
        <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        @error('nama_kegiatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Hari/Tanggal</label>
          <input type="text" name="hari_tanggal" value="{{ old('hari_tanggal') }}"
                 placeholder="Senin, 26 Mei 2026"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
          @error('hari_tanggal') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Tempat</label>
          <input type="text" name="tempat" value="{{ old('tempat') }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
          @error('tempat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
          <input type="text" name="waktu" value="{{ old('waktu') }}"
                 placeholder="09.00 – selesai"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
          @error('waktu') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    {{-- ===== UPLOAD UNDANGAN & SERTIFIKAT ===== --}}
    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Undangan (PDF)</label>
            <input type="file" name="undangan_pdf" accept="application/pdf"
                   class="block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-xl file:border-0
                          file:text-sm file:font-semibold
                          file:bg-maroon-50 file:text-maroon
                          hover:file:bg-maroon-100 focus:outline-none">
            <p class="text-xs text-gray-500 mt-1">Opsional. Jika diisi, akan otomatis digabung dengan Daftar Hadir saat export.</p>
            @error('undangan_pdf') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

    {{-- ===== PENANDATANGAN (OPSIONAL) ===== --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-gray-800">Penandatangan <span class="text-gray-400 font-normal text-sm">(opsional)</span></h2>
          <p class="text-xs text-gray-500 mt-0.5">
            Jika diisi, kolom TTD pejabat akan muncul di PDF dan QR khusus pejabat akan tersedia.
          </p>
        </div>
        <button type="button" id="toggle-pejabat"
                class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-600">
          Tambah
        </button>
      </div>

      <div id="pejabat-section" class="{{ old('pejabat.nama_lengkap') ? '' : 'hidden' }} space-y-4">

        {{-- Autocomplete search pejabat --}}
        <div class="relative">
          <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pejabat (dari data lama)</label>
          <input type="text" id="search-pejabat-input"
                 placeholder="Ketik nama / NIP / jabatan..."
                 autocomplete="off"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
          <div id="pejabat-suggestions"
               class="absolute z-20 mt-1 w-full rounded-xl border bg-white shadow hidden max-h-64 overflow-auto">
          </div>
          <p class="text-xs text-gray-400 mt-1">Pilih dari daftar untuk isi otomatis, atau isi manual di bawah.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap + Gelar</label>
            <input type="text" name="pejabat[nama_lengkap]" id="pejabat_nama"
                   value="{{ old('pejabat.nama_lengkap') }}"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.nama_lengkap') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
            <input type="text" name="pejabat[jabatan]" id="pejabat_jabatan"
                   value="{{ old('pejabat.jabatan') }}"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.jabatan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
            <input type="text" name="pejabat[nip]" id="pejabat_nip"
                   value="{{ old('pejabat.nip') }}"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.nip') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pangkat</label>
            <input type="text" name="pejabat[pangkat]" id="pejabat_pangkat"
                   value="{{ old('pejabat.pangkat') }}"
                   placeholder="Pembina Tk. I"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.pangkat') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Golongan</label>
            <input type="text" name="pejabat[golongan]" id="pejabat_golongan"
                   value="{{ old('pejabat.golongan') }}"
                   placeholder="IV/b"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.golongan') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Tempat & Tanggal TTD --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat TTD</label>
            <input type="text" name="pejabat[tempat_ttd]" id="pejabat_tempat_ttd"
                   value="{{ old('pejabat.tempat_ttd') }}"
                   placeholder="Makassar"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.tempat_ttd') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal TTD</label>
            <input type="text" name="pejabat[tanggal_ttd]" id="pejabat_tanggal_ttd"
                   value="{{ old('pejabat.tanggal_ttd') }}"
                   placeholder="26 Mei 2026"
                   class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
            @error('pejabat.tanggal_ttd') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>

        <div class="pt-1">
          <button type="button" id="clear-pejabat"
                  class="text-xs text-red-500 hover:underline">
            Kosongkan data penandatangan
          </button>
        </div>
      </div>
    </div>

    {{-- ===== ACTIONS ===== --}}
    <div class="flex items-center gap-3">
      <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white font-semibold hover:bg-maroon-800">
        Simpan
      </button>
      <a href="{{ route('sigap-daftar-hadir.index') }}" class="px-4 py-2 rounded-xl border text-gray-700 hover:bg-gray-50">
        Kembali
      </a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // ---- Toggle section pejabat ----
  const toggleBtn     = document.getElementById('toggle-pejabat');
  const pejabatSection = document.getElementById('pejabat-section');

  function isSectionVisible() {
    return !pejabatSection.classList.contains('hidden');
  }

  toggleBtn.addEventListener('click', function () {
    pejabatSection.classList.toggle('hidden');
    toggleBtn.textContent = isSectionVisible() ? 'Sembunyikan' : 'Tambah';
  });

  // Jika section sudah terisi (old input), tampilkan & ubah label tombol
  if (isSectionVisible()) {
    toggleBtn.textContent = 'Sembunyikan';
  }

  // ---- Clear pejabat ----
  document.getElementById('clear-pejabat').addEventListener('click', function () {
    ['pejabat_nama','pejabat_jabatan','pejabat_nip','pejabat_pangkat',
     'pejabat_golongan','pejabat_tempat_ttd','pejabat_tanggal_ttd'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    pejabatSection.classList.add('hidden');
    toggleBtn.textContent = 'Tambah';
  });

  // ---- Autocomplete pejabat ----
  const searchInput   = document.getElementById('search-pejabat-input');
  const suggestionBox = document.getElementById('pejabat-suggestions');
  let debounce = null;
  let lastItems = [];

  const fields = {
    nama_lengkap : document.getElementById('pejabat_nama'),
    jabatan      : document.getElementById('pejabat_jabatan'),
    nip          : document.getElementById('pejabat_nip'),
    pangkat      : document.getElementById('pejabat_pangkat'),
    golongan     : document.getElementById('pejabat_golongan'),
  };

  function hideSuggestions() {
    suggestionBox.classList.add('hidden');
    suggestionBox.innerHTML = '';
    lastItems = [];
  }

  function fillPejabat(item) {
    fields.nama_lengkap.value = item.nama_lengkap || '';
    fields.jabatan.value      = item.jabatan      || '';
    fields.nip.value          = item.nip          || '';
    fields.pangkat.value      = item.pangkat      || '';
    fields.golongan.value     = item.golongan     || '';
    searchInput.value         = '';
    hideSuggestions();

    // Tampilkan section jika tersembunyi
    if (!isSectionVisible()) {
      pejabatSection.classList.remove('hidden');
      toggleBtn.textContent = 'Sembunyikan';
    }
  }

  searchInput.addEventListener('input', function () {
    const q = this.value.trim();
    clearTimeout(debounce);
    if (q.length < 2) { hideSuggestions(); return; }

    debounce = setTimeout(async () => {
      try {
        const url = `{{ route('sigap-daftar-hadir.search-pejabat') }}?q=${encodeURIComponent(q)}`;
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) { hideSuggestions(); return; }

        const items = await res.json();
        lastItems = items;

        if (!items.length) { hideSuggestions(); return; }

        suggestionBox.innerHTML = items.map((item, i) => `
          <button type="button" data-index="${i}"
                  class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
            <div class="font-medium text-gray-900">${item.nama_lengkap}</div>
            <div class="text-xs text-gray-500">${item.jabatan ?? '-'} • NIP: ${item.nip ?? '-'}</div>
          </button>
        `).join('');

        suggestionBox.classList.remove('hidden');

        suggestionBox.querySelectorAll('button[data-index]').forEach(btn => {
          btn.addEventListener('click', function () {
            fillPejabat(lastItems[parseInt(this.dataset.index, 10)]);
          });
        });
      } catch (e) {
        hideSuggestions();
      }
    }, 250);
  });

  document.addEventListener('click', function (e) {
    if (!suggestionBox.contains(e.target) && e.target !== searchInput) {
      hideSuggestions();
    }
  });
});
</script>
@endpush