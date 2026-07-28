@extends('layouts.app')

@section('content')
<div x-data="{ openModalPdf: false }" class="space-y-6">

  {{-- Header & Akses Tombol --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b pb-4">
    <div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
        SKP <span class="text-maroon">Pribadi Saya</span>
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">
        Kelola riwayat bukti kegiatan kinerja (Foto Evidence & Dokumen PDF).
      </p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      {{-- Tombol Modal PDF --}}
      <button type="button" @click="openModalPdf = true"
              class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 transition-colors shadow-sm">
        📄 Upload Dokumen (PDF)
      </button>

      {{-- Tombol Rekap Link --}}
      <a href="{{ route('sigap-skp.kumpulan.index') }}"
         class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-gray-900 text-white text-xs font-semibold hover:bg-gray-800 transition-colors shadow-sm">
        📂 Kategori & Rekap Link
      </a>

      {{-- Tombol Kamera Mandiri --}}
      <a href="{{ route('sigap-skp.upload-mandiri') }}"
         class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
        📷 Kamera Mandiri
      </a>
    </div>
  </div>

  {{-- Stats Mini --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="bg-white rounded-2xl border p-4 shadow-sm">
      <p class="text-xs font-semibold text-gray-500">Total Kegiatan Saya</p>
      <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ $skps->total() }}</h3>
    </div>
    <div class="bg-white rounded-2xl border p-4 shadow-sm">
      <p class="text-xs font-semibold text-gray-500">Total File Dokumentasi Foto</p>
      <h3 class="text-2xl font-extrabold text-maroon mt-1">{{ $total_dokumentasi }}</h3>
    </div>
  </div>

  {{-- BARIS FILTER PENCARIAN & KATEGORI --}}
  <form method="GET" action="{{ route('sigap-skp.pribadi') }}" class="bg-white p-4 rounded-2xl border border-gray-200 shadow-sm grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
    
    {{-- Search Judul --}}
    <div>
      <label class="block text-[11px] font-bold text-gray-600 mb-1">Cari Kegiatan</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Kata kunci judul..." class="w-full text-xs rounded-xl border-gray-300 p-2 focus:ring-maroon focus:border-maroon">
    </div>

    {{-- Filter Tanggal --}}
    <div>
      <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal</label>
      <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="w-full text-xs rounded-xl border-gray-300 p-2 focus:ring-maroon focus:border-maroon">
    </div>

    {{-- Filter Kategori --}}
    <div>
      <label class="block text-[11px] font-bold text-gray-600 mb-1">Kategori SKP</label>
      <select name="kategori" class="w-full text-xs rounded-xl border-gray-300 p-2 focus:ring-maroon focus:border-maroon">
        <option value="">-- Semua Kategori --</option>
        <option value="TUPOKSI" {{ request('kategori') === 'TUPOKSI' ? 'selected' : '' }}>TUPOKSI</option>
        <option value="DIREKTIF (TUGAS TAMBAHAN)" {{ request('kategori') === 'DIREKTIF (TUGAS TAMBAHAN)' ? 'selected' : '' }}>DIREKTIF (TUGAS TAMBAHAN)</option>
      </select>
    </div>

    {{-- Filter Tipe Evidence --}}
    <div>
      <label class="block text-[11px] font-bold text-gray-600 mb-1">Tipe Berkas</label>
      <select name="tipe_evidence" class="w-full text-xs rounded-xl border-gray-300 p-2 focus:ring-maroon focus:border-maroon">
        <option value="">-- Semua Tipe --</option>
        <option value="foto" {{ request('tipe_evidence') === 'foto' ? 'selected' : '' }}>📷 Foto Evidence</option>
        <option value="pdf" {{ request('tipe_evidence') === 'pdf' ? 'selected' : '' }}>📄 Dokumen PDF</option>
      </select>
    </div>

    {{-- Tombol Filter & Reset --}}
    <div class="flex items-end gap-1.5">
      <button type="submit" class="flex-1 py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-colors shadow-sm">
        🔍 Filter
      </button>
      @if(request()->anyFilled(['search', 'tanggal', 'kategori', 'tipe_evidence']))
        <a href="{{ route('sigap-skp.pribadi') }}" class="px-3 py-2 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-200 transition-colors">
          ↺ Reset
        </a>
      @endif
    </div>
  </form>

  {{-- GRID DAFTAR SKP (Foto & PDF) --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @forelse($skps as $skp)
      @php
        $isPdf = $skp->tipe_evidence === 'pdf';
        $foto = $skp->fotos->first();
      @endphp
      <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
        <div>
          {{-- Preview Box --}}
          <div class="h-44 w-full bg-gray-100 relative overflow-hidden flex items-center justify-center border-b">
            @if($isPdf)
              <div class="flex flex-col items-center gap-1 text-red-600 p-4 text-center">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="text-[10px] font-bold uppercase tracking-wider bg-red-100 px-2 py-0.5 rounded text-red-800">Dokumen PDF</span>
              </div>
            @elseif($foto)
              <img src="{{ asset('storage/' . $foto->file_path) }}" class="w-full h-full object-cover">
            @else
              <span class="text-xs text-gray-400">Tidak ada gambar</span>
            @endif

            <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[10px] font-bold bg-black/60 text-white backdrop-blur">
              {{ $skp->kategori ?? 'TUPOKSI' }}
            </span>
          </div>

          {{-- Detail Info --}}
          <div class="p-4 space-y-1">
            <span class="text-[10px] font-bold text-gray-400">📅 {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('d F Y') }}</span>
            <h4 class="font-bold text-sm text-gray-900 leading-snug line-clamp-2">{{ $skp->judul_kegiatan }}</h4>
            @if($skp->deskripsi)
              <p class="text-xs text-gray-500 line-clamp-2 mt-1">{{ $skp->deskripsi }}</p>
            @endif
          </div>
        </div>

        {{-- Action Button --}}
        <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
          @if($isPdf)
            <a href="{{ asset('storage/' . $skp->file_pdf_path) }}" target="_blank" class="text-xs font-bold text-red-700 hover:underline flex items-center gap-1">
              📥 Buka Dokumen PDF
            </a>
          @else
            <a href="{{ route('sigap-skp.show', $skp->slug) }}" class="text-xs font-bold text-maroon hover:underline">
              Lihat Foto
            </a>
          @endif

          <form action="{{ route('sigap-skp.destroy', $skp->slug) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">Hapus</button>
          </form>
        </div>
      </div>
    @empty
      <div class="col-span-full p-8 text-center text-gray-400 italic bg-white rounded-2xl border">
        Tidak ada laporan SKP yang sesuai dengan filter pencarian.
      </div>
    @endforelse
  </div>

  <div>
    {{ $skps->links() }}
  </div>

  {{-- MODAL POP-UP UPLOAD DOKUMEN PDF --}}
  <div x-show="openModalPdf" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
      
      {{-- Backdrop --}}
      <div x-show="openModalPdf" x-transition.opacity class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="openModalPdf = false"></div>

      {{-- Modal Box --}}
      <div x-show="openModalPdf" x-transition class="relative bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100 p-6 space-y-4">
        
        <div class="flex items-center justify-between border-b pb-3">
          <div class="flex items-center gap-2">
            <span class="p-2 bg-red-100 text-red-700 rounded-xl">📄</span>
            <h3 class="text-base font-bold text-gray-900">Upload Dokumen SKP (PDF)</h3>
          </div>
          <button type="button" @click="openModalPdf = false" class="text-gray-400 hover:text-gray-600 font-bold">✕</button>
        </div>

        <form action="{{ route('sigap-skp.store-pdf') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
          @csrf

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Judul Dokumen / Kegiatan <span class="text-red-500">*</span></label>
            <input type="text" name="judul_kegiatan" required placeholder="Tulis judul berkas/kegiatan SKP..." class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-maroon focus:border-maroon">
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Kegiatan <span class="text-red-500">*</span></label>
              <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-maroon focus:border-maroon">
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-700 mb-1">Kategori SKP</label>
              <select name="kategori" class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-maroon focus:border-maroon">
                <option value="TUPOKSI">TUPOKSI</option>
                <option value="DIREKTIF (TUGAS TAMBAHAN)">DIREKTIF (TUGAS TAMBAHAN)</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Deskripsi Ringkas</label>
            <textarea name="deskripsi" rows="2" placeholder="Catatan/deskripsi singkat terkait dokumen..." class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:ring-maroon focus:border-maroon"></textarea>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Berkas PDF <span class="text-red-500">* (Max 10 MB)</span></label>
            <input type="file" name="dokumen_pdf" accept="application/pdf" required class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
          </div>

          <div class="pt-2 flex items-center justify-end gap-2">
            <button type="button" @click="openModalPdf = false" class="px-4 py-2 rounded-xl border text-xs font-bold text-gray-600 hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 shadow">Unggah Dokumen 🚀</button>
          </div>
        </form>

      </div>
    </div>
  </div>

</div>
@endsection