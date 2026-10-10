@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

  {{-- Header & Navigasi --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
        <a href="{{ route('sigap-siipid.verifikator.index') }}" class="hover:text-purple-700">Meja Reviewer</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Telaah Berkas</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug">
        Pemeriksaan Usulan: {{ $prestasi->nama_prestasi }}
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">
        Inovator: <strong class="text-gray-900">{{ $prestasi->nama_inovator }}</strong> ({{ $prestasi->opd_instansi ?: 'Masyarakat' }})
      </p>
    </div>

    <a href="{{ route('sigap-siipid.verifikator.index') }}" class="px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
      ← Kembali ke Antrian
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    {{-- KOLOM KIRI (2 KOLOM): DETAIL DATA & DOKUMEN BUKTI --}}
    <div class="lg:col-span-2 space-y-6">

      {{-- Ringkasan Prestasi & Inovasi Induk --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Data Prestasi & Inovasi</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div>
            <dt class="text-gray-400 font-medium">Nama Prestasi</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->nama_prestasi }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Ajang / Sumber Kompetisi</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->ajang_kompetisi }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Tingkat Prestasi</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->tingkat_label }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Tahun Perolehan</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->tahun_perolehan }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Peringkat / Capaian</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->peringkat_capaian ?: '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Lembaga Pemberi</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->lembaga_pemberi }}</dd>
          </div>
        </dl>

        <div class="mt-4 p-3.5 rounded-xl bg-purple-50/70 border border-purple-200 text-xs">
          <span class="font-bold text-purple-900 block mb-1">Inovasi Induk Terkait:</span>
          <div class="font-extrabold text-gray-900">{{ $prestasi->innovable->judul ?? '—' }}</div>
          <div class="text-[11px] text-purple-700 mt-0.5">
            Basis Data: <strong>{{ class_basename($prestasi->innovable_type) === 'Inovasi' ? 'SIGAP Inovasi (OPD)' : 'SIGAP IMA (Masyarakat)' }}</strong>
          </div>
        </div>

        @if($prestasi->deskripsi_prestasi)
          <div class="mt-3 text-xs">
            <span class="text-gray-400 font-medium block mb-1">Uraian Manfaat & Kontribusi:</span>
            <p class="text-gray-700 whitespace-pre-line bg-gray-50 p-3 rounded-xl border border-gray-200/60">{{ $prestasi->deskripsi_prestasi }}</p>
          </div>
        @endif
      </div>

      {{-- Identitas Inovator & Anggota --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Profil Inovator / Tim</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div>
            <dt class="text-gray-400 font-medium">Nama Lengkap</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->nama_inovator }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Kategori Sasaran</dt>
            <dd class="font-bold text-gray-900 mt-0.5 uppercase">{{ $prestasi->kategori_sasaran }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">NIP / NIK</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->nip ?: ($prestasi->nik ?: '—') }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Instansi / Unit Kerja</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->opd_instansi ?: '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">No. WhatsApp</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->no_wa }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Email</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->email }}</dd>
          </div>
        </dl>

        @if($prestasi->jenis_kepesertaan === 'kelompok' && !empty($prestasi->anggota_tim))
          <div class="mt-3 pt-3 border-t border-gray-100">
            <span class="text-xs font-bold text-gray-800 block mb-2">Anggota Tim Inovasi:</span>
            <div class="space-y-1.5">
              @foreach($prestasi->anggota_tim as $tim)
                <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 text-xs">
                  <span class="font-semibold text-gray-900">{{ $tim['nama'] ?? '—' }} ({{ $tim['nik_nip'] ?? '-' }})</span>
                  <span class="text-gray-500 font-medium text-[11px]">{{ $tim['peran'] ?? 'Anggota' }}</span>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      {{-- Berkas Bukti Fisik --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Pemeriksaan Berkas Fisik</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          
          <a href="{{ asset('storage/' . $prestasi->bukti_prestasi_file) }}" target="_blank"
             class="flex items-center justify-between p-3 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-purple-100/60 transition font-bold text-purple-900">
            <div class="flex items-center gap-2">
              <span class="text-base">📜</span>
              <span>Piagam / Sertifikat (Wajib)</span>
            </div>
            <span>Buka ↗</span>
          </a>

          @if($prestasi->bukti_penerapan_file)
            <a href="{{ asset('storage/' . $prestasi->bukti_penerapan_file) }}" target="_blank"
               class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-gray-400 transition font-semibold text-gray-800">
              <div class="flex items-center gap-2">
                <span class="text-base">📄</span>
                <span>Bukti Penerapan Inovasi</span>
              </div>
              <span class="text-maroon">Buka ↗</span>
            </a>
          @endif

          @if($prestasi->surat_keaslian_file)
            <a href="{{ asset('storage/' . $prestasi->surat_keaslian_file) }}" target="_blank"
               class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-gray-400 transition font-semibold text-gray-800">
              <div class="flex items-center gap-2">
                <span class="text-base">✍️</span>
                <span>Surat Keaslian Data</span>
              </div>
              <span class="text-maroon">Buka ↗</span>
            </a>
          @endif

          @if($prestasi->surat_kontribusi_tim_file)
            <a href="{{ asset('storage/' . $prestasi->surat_kontribusi_tim_file) }}" target="_blank"
               class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:border-gray-400 transition font-semibold text-gray-800">
              <div class="flex items-center gap-2">
                <span class="text-base">👥</span>
                <span>Surat Kontribusi Tim</span>
              </div>
              <span class="text-maroon">Buka ↗</span>
            </a>
          @endif

        </div>
      </div>

    </div>

    {{-- KOLOM KANAN (1 KOLOM): PANEL KEPUTUSAN REVIEWER --}}
    <div class="space-y-6">

      {{-- FORM AKSI REVIEWER --}}
      <div class="bg-white rounded-2xl border-2 border-purple-600/60 p-5 shadow-md space-y-4">
        <div class="border-b border-gray-100 pb-3">
          <span class="inline-block text-[10px] font-bold uppercase tracking-wider text-purple-700 bg-purple-100 px-2 py-0.5 rounded-md">
            Panel Verifikator
          </span>
          <h3 class="font-bold text-gray-900 text-sm mt-1">Keputusan & Catatan Review</h3>
        </div>

        <form action="{{ route('sigap-siipid.verifikator.action', $prestasi->id) }}" method="POST" class="space-y-4">
          @csrf

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-2">Tentukan Status Aksi <span class="text-red-500">*</span></label>
            <div class="space-y-2 text-xs">
              
              {{-- Opsi 1: Rekomendasikan --}}
              <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-emerald-200 bg-emerald-50/60 hover:bg-emerald-100/70 cursor-pointer transition">
                <input type="radio" name="action" value="direkomendasikan" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" required>
                <div>
                  <strong class="text-emerald-900 block font-bold">Rekomendasikan Penghargaan</strong>
                  <span class="text-[11px] text-emerald-700">Berkas valid, inovasi terbukti, siap diajukan ke SK Wali Kota.</span>
                </div>
              </label>

              {{-- Opsi 2: Kembalikan untuk Perbaikan --}}
              <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-amber-300 bg-amber-50/60 hover:bg-amber-100/70 cursor-pointer transition">
                <input type="radio" name="action" value="dikembalikan_perbaikan" class="mt-0.5 text-amber-600 focus:ring-amber-500" required>
                <div>
                  <strong class="text-amber-900 block font-bold">Kembalikan untuk Perbaikan</strong>
                  <span class="text-[11px] text-amber-700">Berkas kurang/salah. Inovator dapat mengedit dan mengirim ulang.</span>
                </div>
              </label>

              {{-- Opsi 3: Tolak Usulan --}}
              <label class="flex items-start gap-2.5 p-2.5 rounded-xl border border-red-200 bg-red-50/60 hover:bg-red-100/70 cursor-pointer transition">
                <input type="radio" name="action" value="ditolak" class="mt-0.5 text-red-600 focus:ring-red-500" required>
                <div>
                  <strong class="text-red-900 block font-bold">Tolak Usulan</strong>
                  <span class="text-[11px] text-red-700">Tidak memenuhi kriteria Perwali atau data tidak valid.</span>
                </div>
              </label>

            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">
              Catatan / Alasan Reviewer <span class="text-red-500">*</span>
            </label>
            <textarea name="catatan" rows="4" required placeholder="Tuliskan catatan detail untuk inovator (misal: mohon perjelas surat pernyataan keaslian, lampirkan bukti foto penerapan)..." class="w-full rounded-xl border-gray-300 text-xs focus:ring-purple-600 focus:border-purple-600"></textarea>
            <span class="text-[10px] text-gray-400">Catatan ini akan langsung tampil di dashboard inovator.</span>
          </div>

          <div class="pt-2">
            <button type="submit" class="w-full py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-sm transition">
              Simpan Keputusan Review
            </button>
          </div>
        </form>
      </div>

      {{-- PENETAPAN SK WALI KOTA (KHUSUS ADMIN JIKA SUDAH DIREKOMENDASIKAN) --}}
      @hasrole('admin|superadmin')
        <div class="bg-white rounded-2xl border border-maroon/30 p-5 shadow-2xs space-y-3">
          <div class="border-b border-gray-100 pb-2">
            <span class="text-[10px] font-bold text-maroon bg-maroon/10 px-2 py-0.5 rounded">Admin Penetapan</span>
            <h4 class="font-bold text-gray-900 text-xs mt-1">Penetapan Keputusan Wali Kota</h4>
          </div>

          <form action="{{ route('sigap-siipid.verifikator.penetapan-sk', $prestasi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3 text-xs">
            @csrf
            <div>
              <label class="block font-bold text-gray-700 mb-1">Nomor SK Wali Kota <span class="text-red-500">*</span></label>
              <input type="text" name="nomor_sk_walikota" value="{{ old('nomor_sk_walikota', $prestasi->nomor_sk_walikota) }}" required placeholder="Contoh: 100.3.3.3/SK/2026" class="w-full rounded-xl border-gray-300 text-xs py-1.5 focus:ring-maroon">
            </div>

            <div>
              <label class="block font-bold text-gray-700 mb-1">Tanggal SK <span class="text-red-500">*</span></label>
              <input type="date" name="tanggal_sk_walikota" value="{{ old('tanggal_sk_walikota', $prestasi->tanggal_sk_walikota?->format('Y-m-d')) }}" required class="w-full rounded-xl border-gray-300 text-xs py-1.5 focus:ring-maroon">
            </div>

            <div>
              <label class="block font-bold text-gray-700 mb-1">File Salinan SK (PDF)</label>
              <input type="file" name="file_sk_walikota" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-1.5 focus:ring-maroon">
            </div>

            <button type="submit" class="w-full py-2 rounded-xl bg-maroon hover:bg-maroon-700 text-white font-bold text-xs shadow-2xs transition">
              Tetapkan SK & Terbitkan ke Publik
            </button>
          </form>
        </div>
      @endhasrole

    </div>

  </div>

</div>
@endsection
