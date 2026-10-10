@extends('layouts.page')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10 space-y-8">

  {{-- Breadcrumb Navigasi --}}
  <div class="flex items-center gap-2 text-xs text-gray-500">
    <a href="{{ route('home') }}" class="hover:text-maroon">Beranda</a>
    <span>/</span>
    <a href="{{ route('public.siipid.index') }}" class="hover:text-maroon">Direktori SIIPID</a>
    <span>/</span>
    <span class="text-gray-900 font-medium">Detail Prestasi</span>
  </div>

  {{-- Card Header Utama --}}
  <div class="bg-white rounded-3xl border border-gray-200 p-6 sm:p-8 shadow-sm space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
          🏆 {{ $prestasi->tingkat_label }}
        </span>
        <span class="text-xs font-semibold text-gray-400">Tahun {{ $prestasi->tahun_perolehan }}</span>
      </div>

      @if($prestasi->nomor_sk_walikota)
        <span class="text-xs font-bold text-maroon bg-maroon-50 px-3 py-1 rounded-full border border-maroon/20">
          SK Wali Kota: {{ $prestasi->nomor_sk_walikota }}
        </span>
      @endif
    </div>

    <div>
      <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 leading-tight">
        {{ $prestasi->nama_prestasi }}
      </h1>
      <p class="text-sm sm:text-base text-gray-600 mt-2 font-medium">
        Ajang: {{ $prestasi->ajang_kompetisi }} • Peringkat: <strong class="text-maroon">{{ $prestasi->peringkat_capaian ?: 'Penghargaan Resmi' }}</strong>
      </p>
    </div>

    {{-- Highlight Inovasi Induk --}}
    <div class="p-4 sm:p-5 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-2">
      <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Inovasi Induk Terkait:</span>
      <h3 class="text-base sm:text-lg font-extrabold text-gray-900">
        💡 {{ $prestasi->innovable->judul ?? '—' }}
      </h3>
      <p class="text-xs text-gray-500">
        Basis Sistem: <strong>{{ class_basename($prestasi->innovable_type) === 'Inovasi' ? 'SIGAP Inovasi (Perangkat Daerah)' : 'SIGAP IMA (Inovasi Masyarakat)' }}</strong>
      </p>
    </div>

    {{-- Inovator Profile --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100 text-xs">
      <div>
        <span class="text-gray-400 font-medium block">Nama Inovator / Ketua</span>
        <p class="font-extrabold text-gray-900 text-sm mt-0.5">{{ $prestasi->nama_inovator }}</p>
      </div>

      <div>
        <span class="text-gray-400 font-medium block">Instansi / Komunitas Asal</span>
        <p class="font-extrabold text-gray-900 text-sm mt-0.5">{{ $prestasi->opd_instansi ?: 'Pemerintah Kota Makassar' }}</p>
      </div>

      <div>
        <span class="text-gray-400 font-medium block">Lembaga Pemberi Penghargaan</span>
        <p class="font-bold text-gray-800 mt-0.5">{{ $prestasi->lembaga_pemberi }}</p>
      </div>

      <div>
        <span class="text-gray-400 font-medium block">Kategori Kepesertaan</span>
        <p class="font-bold text-gray-800 mt-0.5 uppercase">{{ $prestasi->jenis_kepesertaan }}</p>
      </div>
    </div>

    {{-- Anggota Tim jika kelompok --}}
    @if($prestasi->jenis_kepesertaan === 'kelompok' && !empty($prestasi->anggota_tim))
      <div class="pt-4 border-t border-gray-100">
        <h4 class="font-bold text-gray-900 text-xs mb-2">Anggota Tim Inovator:</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
          @foreach($prestasi->anggota_tim as $tim)
            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100">
              <span class="font-bold text-gray-900 block">{{ $tim['nama'] ?? '-' }}</span>
              <span class="text-[11px] text-gray-500">{{ $tim['peran'] ?? 'Anggota' }}</span>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Deskripsi Prestasi --}}
    @if($prestasi->deskripsi_prestasi)
      <div class="pt-4 border-t border-gray-100 space-y-2">
        <h4 class="font-bold text-gray-900 text-xs uppercase tracking-wider">Uraian Prestasi & Dampak Inovasi:</h4>
        <p class="text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
          {{ $prestasi->deskripsi_prestasi }}
        </p>
      </div>
    @endif

    {{-- Bukti Fisik Piagam --}}
    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
      <span class="text-xs font-semibold text-gray-500">Bukti Fisik Sertifikat / Piagam Resmi:</span>
      <a href="{{ asset('storage/' . $prestasi->bukti_prestasi_file) }}" target="_blank"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white font-bold text-xs hover:bg-maroon-700 transition shadow-2xs">
        <span>Buka Piagam Penghargaan</span>
        <span>↗</span>
      </a>
    </div>

  </div>

  {{-- CTA Kembali --}}
  <div class="text-center">
    <a href="{{ route('public.siipid.index') }}" class="text-xs font-bold text-maroon hover:underline">
      ← Kembali ke Galeri Seluruh Prestasi Inovator
    </a>
  </div>

</div>
@endsection
