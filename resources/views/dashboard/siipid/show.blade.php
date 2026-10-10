@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- Header & Navigasi --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
        <a href="{{ route('sigap-siipid.index') }}" class="hover:text-maroon">SIGAP SIIPID</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Detail Prestasi</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug">
        {{ $prestasi->nama_prestasi }}
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">
        {{ $prestasi->ajang_kompetisi }} • Tahun {{ $prestasi->tahun_perolehan }} • {{ $prestasi->tingkat_label }}
      </p>
    </div>

    <div class="flex items-center gap-2 shrink-0">
      <a href="{{ route('sigap-siipid.index') }}" class="px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
        Kembali
      </a>
      @if($prestasi->is_editable)
        <a href="{{ route('sigap-siipid.edit', $prestasi->uuid) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-2xs">
          Perbaiki Usulan
        </a>
      @endif
    </div>
  </div>

  {{-- Status Bar --}}
  @php $badge = $prestasi->status_badge; @endphp
  <div class="p-4 rounded-2xl border {{ $badge['class'] }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
    <div class="flex items-center gap-3">
      <span class="text-xl">
        @if($prestasi->status === 'dikembalikan_perbaikan') ⚠️
        @elseif($prestasi->status === 'direkomendasikan' || $prestasi->status === 'ditetapkan_sk') 🎖️
        @else 📋 @endif
      </span>
      <div>
        <div class="text-xs font-bold uppercase tracking-wider">Status Pengajuan: {{ $badge['label'] }}</div>
        @if($prestasi->reviewer)
          <div class="text-[11px] opacity-80">Ditinjau oleh: <strong>{{ $prestasi->reviewer->name }}</strong> ({{ $prestasi->reviewed_at?->diffForHumans() }})</div>
        @endif
      </div>
    </div>

    @if($prestasi->nomor_sk_walikota)
      <div class="text-xs text-right">
        <span class="font-bold">SK Wali Kota:</span> {{ $prestasi->nomor_sk_walikota }}
      </div>
    @endif
  </div>

  {{-- CATATAN REVIEWER TERAKHIR JIKA ADA --}}
  @if($prestasi->catatan_review_terakhir)
    <div class="rounded-2xl border border-amber-300 bg-amber-50/80 p-5 space-y-2">
      <h4 class="font-bold text-amber-900 text-xs uppercase tracking-wider flex items-center gap-1.5">
        <span>💬</span>
        <span>Catatan dari Tim Verifikator:</span>
      </h4>
      <p class="text-xs text-amber-950 leading-relaxed bg-white/80 p-3 rounded-xl border border-amber-200">
        {{ $prestasi->catatan_review_terakhir }}
      </p>
    </div>
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- KOLOM KIRI: DATA LENGKAP --}}
    <div class="lg:col-span-2 space-y-6">

      {{-- Inovasi Induk Terkait --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Inovasi Induk Terkait</h3>
        <div class="space-y-1.5">
          <div class="text-base font-extrabold text-gray-900">{{ $prestasi->innovable->judul ?? '—' }}</div>
          <div class="text-xs text-gray-500">
            Sumber Sistem: <span class="font-bold text-maroon">{{ class_basename($prestasi->innovable_type) === 'Inovasi' ? 'SIGAP Inovasi (OPD)' : 'SIGAP IMA (Masyarakat)' }}</span>
          </div>
          @if(!empty($prestasi->innovable->tahap_inovasi))
            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
              Tahap: {{ ucfirst($prestasi->innovable->tahap_inovasi) }}
            </span>
          @endif
        </div>
      </div>

      {{-- Data Inovator & Tim --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Identitas Inovator</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
          <div>
            <dt class="text-gray-400 font-medium">Nama Lengkap</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->nama_inovator }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Kategori Sasaran</dt>
            <dd class="font-bold text-gray-900 mt-0.5 uppercase">{{ $prestasi->kategori_sasaran }}</dd>
          </div>
          @if($prestasi->nip)
            <div>
              <dt class="text-gray-400 font-medium">NIP</dt>
              <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->nip }}</dd>
            </div>
          @endif
          @if($prestasi->nik)
            <div>
              <dt class="text-gray-400 font-medium">NIK</dt>
              <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->nik }}</dd>
            </div>
          @endif
          <div>
            <dt class="text-gray-400 font-medium">Instansi / Unit Kerja</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->opd_instansi ?: '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Jabatan / Profesi</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->jabatan ?: '—' }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Kontak WhatsApp</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->no_wa }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-medium">Email</dt>
            <dd class="font-bold text-gray-900 mt-0.5">{{ $prestasi->email }}</dd>
          </div>
        </dl>

        {{-- Anggota Tim jika beregu --}}
        @if($prestasi->jenis_kepesertaan === 'kelompok' && !empty($prestasi->anggota_tim))
          <div class="mt-4 pt-3 border-t border-gray-100">
            <h4 class="font-bold text-gray-800 text-xs mb-2">Anggota Tim Inovasi:</h4>
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

      {{-- Rincian Deskripsi Prestasi --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Deskripsi Capaian Prestasi</h3>
        <p class="text-xs text-gray-700 leading-relaxed whitespace-pre-line">
          {{ $prestasi->deskripsi_prestasi ?: 'Tidak ada deskripsi tambahan.' }}
        </p>
      </div>

    </div>

    {{-- KOLOM KANAN: BERKAS & TIMELINE LOG --}}
    <div class="space-y-6">

      {{-- Berkas Lampiran Dokumen --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Berkas & Bukti Fisik</h3>
        
        <div class="space-y-2 text-xs">
          {{-- Piagam --}}
          <a href="{{ asset('storage/' . $prestasi->bukti_prestasi_file) }}" target="_blank"
             class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:border-maroon hover:bg-maroon-50/40 text-gray-800 transition">
            <div class="flex items-center gap-2">
              <span class="text-base">📜</span>
              <span class="font-semibold text-[11px]">Piagam / Sertifikat SK</span>
            </div>
            <span class="text-maroon font-bold text-[10px]">Lihat ↗</span>
          </a>

          @if($prestasi->bukti_penerapan_file)
            <a href="{{ asset('storage/' . $prestasi->bukti_penerapan_file) }}" target="_blank"
               class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:border-maroon hover:bg-maroon-50/40 text-gray-800 transition">
              <div class="flex items-center gap-2">
                <span class="text-base">📄</span>
                <span class="font-semibold text-[11px]">Bukti Penerapan</span>
              </div>
              <span class="text-maroon font-bold text-[10px]">Lihat ↗</span>
            </a>
          @endif

          @if($prestasi->surat_keaslian_file)
            <a href="{{ asset('storage/' . $prestasi->surat_keaslian_file) }}" target="_blank"
               class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:border-maroon hover:bg-maroon-50/40 text-gray-800 transition">
              <div class="flex items-center gap-2">
                <span class="text-base">✍️</span>
                <span class="font-semibold text-[11px]">Surat Keaslian Data</span>
              </div>
              <span class="text-maroon font-bold text-[10px]">Lihat ↗</span>
            </a>
          @endif

          @if($prestasi->surat_kontribusi_tim_file)
            <a href="{{ asset('storage/' . $prestasi->surat_kontribusi_tim_file) }}" target="_blank"
               class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:border-maroon hover:bg-maroon-50/40 text-gray-800 transition">
              <div class="flex items-center gap-2">
                <span class="text-base">👥</span>
                <span class="font-semibold text-[11px]">Surat Tim Inovator</span>
              </div>
              <span class="text-maroon font-bold text-[10px]">Lihat ↗</span>
            </a>
          @endif

          @if($prestasi->file_sk_walikota)
            <a href="{{ asset('storage/' . $prestasi->file_sk_walikota) }}" target="_blank"
               class="flex items-center justify-between p-2.5 rounded-xl border border-maroon bg-maroon-50 text-maroon font-bold transition">
              <div class="flex items-center gap-2">
                <span class="text-base">🏛️</span>
                <span class="text-[11px]">Salinan SK Wali Kota</span>
              </div>
              <span class="text-[10px]">Unduh ↗</span>
            </a>
          @endif
        </div>
      </div>

      {{-- Riwayat Catatan Review (Audit Logs) --}}
      <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-3">
        <h3 class="font-bold text-gray-900 text-sm border-b border-gray-100 pb-2.5">Histori Review</h3>
        
        <div class="space-y-3">
          @forelse($prestasi->reviewLogs as $log)
            <div class="text-xs p-3 rounded-xl bg-gray-50 border border-gray-200/70 space-y-1">
              <div class="flex items-center justify-between text-[11px]">
                <span class="font-bold text-gray-800">{{ $log->reviewer->name ?? 'Reviewer' }}</span>
                <span class="text-gray-400">{{ $log->created_at->format('d M Y, H:i') }}</span>
              </div>
              <div class="text-[11px] font-semibold text-gray-600">
                Status: <span class="capitalize">{{ str_replace('_', ' ', $log->status_baru) }}</span>
              </div>
              <p class="text-gray-700 italic bg-white p-2 rounded border border-gray-200 mt-1">
                "{{ $log->catatan }}"
              </p>
            </div>
          @empty
            <p class="text-xs text-gray-400 italic">Belum ada riwayat catatan review.</p>
          @endforelse
        </div>
      </div>

    </div>

  </div>

</div>
@endsection
