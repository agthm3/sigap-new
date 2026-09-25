@extends('layouts.app')

@section('content')
<div x-data="{ activeTab: 'preview' }" class="max-w-7xl mx-auto space-y-6 pb-12">

  <!-- Header & Navigasi Aksi -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
    <div>
      <div class="flex items-center gap-2">
        <a href="{{ route('sigap-notulensi.index') }}" class="text-xs text-gray-500 hover:text-maroon flex items-center gap-1 transition">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Kembali ke Daftar
        </a>
        <span class="text-gray-300">•</span>
        <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold border
          {{ $notulensi->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
          STATUS: {{ strtoupper($notulensi->status) }}
        </span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-1 leading-snug">
        {{ $notulensi->judul_acara }}
      </h1>
      <p class="text-xs text-gray-500 mt-0.5">
        Dibuat oleh: <span class="font-semibold text-gray-700">{{ $notulensi->creator->name ?? 'Staf' }}</span> 
        • Pelaksanaan: <span class="font-semibold text-gray-700">{{ $notulensi->hari_tanggal ?: '-' }}</span>
      </p>
    </div>

    <!-- Tombol Aksi Cepat -->
    <div class="flex items-center flex-wrap gap-2">
      <a href="{{ route('sigap-notulensi.edit', $notulensi->id) }}"
         class="px-4 py-2.5 rounded-xl border border-gray-300 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 transition flex items-center gap-1.5 shadow-2xs">
        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Edit Dokumen
      </a>

      @hasanyrole('admin|verif_notulensi')
        <form action="{{ route('sigap-notulensi.status', $notulensi->id) }}" method="POST">
          @csrf
          @if($notulensi->status === 'selesai')
            <input type="hidden" name="status" value="proses">
            <button type="submit" class="px-3.5 py-2.5 rounded-xl border border-amber-300 text-amber-700 hover:bg-amber-50 text-xs font-bold transition">
              Tandai Draft/Proses
            </button>
          @else
            <input type="hidden" name="status" value="selesai">
            <button type="submit" class="px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold transition">
              Tandai Selesai
            </button>
          @endif
        </form>
      @endhasanyrole

      <a href="{{ route('sigap-notulensi.export-pdf', $notulensi->id) }}"
         target="_blank"
         class="px-5 py-2.5 rounded-xl bg-maroon hover:bg-maroon-800 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Cetak / Export PDF (4 Lembar)
      </a>
    </div>
  </div>

  <!-- Navigasi Tab Pratinjau Dokumen -->
  <div class="border-b border-gray-200">
    <nav class="flex space-x-2 sm:space-x-4 overflow-x-auto scrollbar-thin">
      <button type="button" @click="activeTab = 'notula'"
              :class="activeTab === 'notula' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
              class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
        <span>1. Lembar Notula Rapat</span>
      </button>

      <button type="button" @click="activeTab = 'undangan'"
              :class="activeTab === 'undangan' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
              class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
        <span>2. Surat Undangan</span>
      </button>

      <button type="button" @click="activeTab = 'hadir'"
              :class="activeTab === 'hadir' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
              class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
        <span>3. Daftar Hadir Peserta</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] bg-gray-200 text-gray-700" x-text="{{ $notulensi->pesertas->count() }}"></span>
      </button>

      <button type="button" @click="activeTab = 'foto'"
              :class="activeTab === 'foto' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
              class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
        <span>4. Foto Dokumentasi</span>
        <span class="px-2 py-0.5 rounded-full text-[10px] bg-maroon text-white font-bold">{{ count($notulensi->dokumentasi_foto ?? []) }}</span>
      </button>
    </nav>
  </div>

  <!-- =================================================================== -->
  <!-- TAB 1: LEMBAR NOTULA RAPAT                                          -->
  <!-- =================================================================== -->
  <div x-show="activeTab === 'notula'" class="space-y-5">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-6">
      
      <!-- Metadata Kegiatan Notula -->
      <div>
        <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-4">A. Informasi Rapat / Agenda</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <span class="text-gray-400 block mb-0.5">Hari / Tanggal</span>
            <span class="font-bold text-gray-800">{{ $notulensi->hari_tanggal ?: '-' }}</span>
          </div>
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <span class="text-gray-400 block mb-0.5">Waktu Pelaksanaan</span>
            <span class="font-bold text-gray-800">{{ $notulensi->waktu ?: '-' }}</span>
          </div>
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <span class="text-gray-400 block mb-0.5">Tempat / Ruangan</span>
            <span class="font-bold text-gray-800">{{ $notulensi->tempat ?: '-' }}</span>
          </div>
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <span class="text-gray-400 block mb-0.5">Pimpinan Rapat</span>
            <span class="font-bold text-gray-800">{{ $notulensi->pimpinan_rapat ?: ($notulensi->pimpinan_nama ?: 'Kepala BRIDA Kota Makassar') }}</span>
          </div>
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <span class="text-gray-400 block mb-0.5">Notulis Kegiatan</span>
            <span class="font-bold text-gray-800">{{ $notulensi->notulis_nama ?: ($notulensi->creator->name ?? 'Staf') }}</span>
          </div>
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
            <span class="text-gray-400 block mb-0.5">Ringkasan Peserta</span>
            <span class="font-bold text-gray-800">{{ $notulensi->peserta_ringkas ?: 'Seluruh Pejabat Struktural & Tim Fungsional' }}</span>
          </div>
        </div>
      </div>

      <!-- Jalannya Rapat (Isi Notulensi) -->
      <div>
        <h3 class="text-sm font-bold text-gray-900 border-b pb-2 mb-3">B. Catatan Jalannya Rapat & Kesepakatan</h3>
        <div class="p-4 rounded-xl bg-gray-50/70 border border-gray-200 text-xs sm:text-sm text-gray-800 leading-relaxed font-sans whitespace-pre-line">
          {{ $notulensi->isi_pelaksana_kegiatan ?: 'Belum ada catatan pembahasan rapat yang dimasukkan.' }}
        </div>
      </div>

      <!-- Tanda Tangan Notulis -->
      <div class="pt-4 border-t flex justify-end">
        <div class="text-center w-64 space-y-1">
          <p class="text-xs text-gray-500">Notulis Kegiatan,</p>
          <div class="h-20 flex items-center justify-center">
            @if($notulensi->notulis_ttd_image && file_exists(storage_path('app/public/' . $notulensi->notulis_ttd_image)))
              <img src="{{ asset('storage/' . $notulensi->notulis_ttd_image) }}" alt="TTD Notulis" class="max-h-16 max-w-full object-contain">
            @else
              <span class="text-xs text-gray-300 italic">(Belum ada tanda tangan)</span>
            @endif
          </div>
          <p class="text-xs font-bold text-gray-900 underline">{{ $notulensi->notulis_nama ?: ($notulensi->creator->name ?? 'Notulis') }}</p>
          <p class="text-[11px] text-gray-400">Pegawai BRIDA Kota Makassar</p>
        </div>
      </div>

    </div>
  </div>

  <!-- =================================================================== -->
  <!-- TAB 2: SURAT PENGANTAR / UNDANGAN                                   -->
  <!-- =================================================================== -->
  <div x-show="activeTab === 'undangan'" class="space-y-5" style="display: none;">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-5 max-w-4xl mx-auto">
      
      <!-- Preview Surat Resmi -->
      <div class="border-b pb-4">
        <table class="w-full text-xs text-gray-800">
          <tr>
            <td class="w-20 font-semibold py-1">Nomor</td>
            <td class="w-3">:</td>
            <td>{{ $notulensi->nomor_surat ?: '-' }}</td>
            <td class="text-right text-gray-500">Makassar, {{ optional($notulensi->tanggal_surat)->translatedFormat('d F Y') ?: date('d F Y') }}</td>
          </tr>
          <tr>
            <td class="font-semibold py-1">Lampiran</td>
            <td>:</td>
            <td>-</td>
            <td></td>
          </tr>
          <tr>
            <td class="font-semibold py-1 align-top">Perihal</td>
            <td class="align-top">:</td>
            <td class="font-bold text-gray-900 align-top">{{ $notulensi->perihal_surat ?: 'NOTULA DAN DOKUMENTASI RAPAT' }}</td>
            <td class="align-top text-right">
              <strong>Kepada Yth.</strong><br>
              {{ $notulensi->tujuan_surat ?: 'Pejabat Eselon dan Staf BRIDA Kota Makassar' }}<br>
              di - Makassar
            </td>
          </tr>
        </table>
      </div>

      <div class="text-xs text-gray-700 leading-relaxed space-y-3 pt-2">
        <p>{{ $notulensi->isi_pembuka_surat ?: 'Sehubungan dengan telah dilaksanakannya rapat koordinasi, bersama ini kami sampaikan laporan notula dan dokumentasi pelaksanaan kegiatan dimaksud sebagai bahan evaluasi dan tindak lanjut program kerja.' }}</p>
        <p>Rapat Koordinasi Pimpinan ini dilaksanakan pada:</p>
        <div class="pl-4 border-l-2 border-maroon space-y-1 font-medium">
          <p><span class="text-gray-500 inline-block w-28">Hari / Tanggal</span>: <strong>{{ $notulensi->hari_tanggal }}</strong></p>
          <p><span class="text-gray-500 inline-block w-28">Waktu</span>: {{ $notulensi->waktu }}</p>
          <p><span class="text-gray-500 inline-block w-28">Tempat</span>: {{ $notulensi->tempat }}</p>
          <p><span class="text-gray-500 inline-block w-28">Acara</span>: {{ $notulensi->judul_acara }}</p>
        </div>
        <p>Demikian surat ini disampaikan untuk menjadi perhatian dan bahan evaluasi.</p>
      </div>

      <!-- TTD Pimpinan -->
      <div class="pt-6 border-t flex justify-end">
        <div class="text-left w-72 space-y-1">
          <p class="text-xs font-semibold text-gray-800">{{ $notulensi->pimpinan_jabatan ?: 'KEPALA BADAN RISET DAN INOVASI DAERAH' }},</p>
          <div class="h-20 flex items-center justify-start">
            @if($notulensi->pimpinan_ttd_image && file_exists(storage_path('app/public/' . $notulensi->pimpinan_ttd_image)))
              <img src="{{ asset('storage/' . $notulensi->pimpinan_ttd_image) }}" alt="TTD Pimpinan" class="max-h-16 max-w-full object-contain">
            @else
              <span class="text-xs text-gray-300 italic">(Belum ada stempel / scan TTD)</span>
            @endif
          </div>
          <p class="text-xs font-bold text-gray-900 underline">{{ $notulensi->pimpinan_nama ?: 'H. ANDI RAMA, S.Sos., M.Si.' }}</p>
          <p class="text-[11px] text-gray-500">{{ $notulensi->pimpinan_pangkat ?: 'Pembina Utama Muda' }}</p>
          <p class="text-[11px] text-gray-500">NIP. {{ $notulensi->pimpinan_nip ?: '19700216 199803 1 004' }}</p>
        </div>
      </div>

    </div>
  </div>

  <!-- =================================================================== -->
  <!-- TAB 3: DAFTAR HADIR (TAMPILAN PERSIS MODEL PRESENSI RESMI)         -->
  <!-- =================================================================== -->
  <div x-show="activeTab === 'hadir'" class="space-y-5" style="display: none;">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
      
      <div class="p-4 bg-gray-50 border-b flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-gray-900">Daftar Kehadiran Peserta Rapat</h3>
          <p class="text-xs text-gray-500">Total terdata: <b>{{ $notulensi->pesertas->count() }}</b> peserta hadir</p>
        </div>
        @if($notulensi->daftar_hadir_kegiatan_id)
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            Tersinkron SIGAP DH
          </span>
        @endif
      </div>

      <div class="overflow-x-auto p-4">
        <table class="min-w-full text-xs border border-gray-200 rounded-lg">
          <thead class="bg-gray-100 text-gray-700 uppercase font-semibold">
            <tr>
              <th class="py-2.5 px-3 text-center w-12 border">No</th>
              <th class="py-2.5 px-3 text-left border">Nama Peserta</th>
              <th class="py-2.5 px-3 text-left border">Instansi / Unit Kerja</th>
              <th class="py-2.5 px-3 text-center border w-16">Gender</th>
              <th class="py-2.5 px-3 text-left border">NIP / Kontak</th>
              <th class="py-2.5 px-3 text-left border">Email</th>
              <th class="py-2.5 px-3 text-center border w-28">Paraf / TTD</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($notulensi->pesertas as $p)
              <tr class="hover:bg-gray-50/50">
                <td class="py-2.5 px-3 text-center font-bold text-gray-500 border">{{ $loop->iteration }}</td>
                <td class="py-2.5 px-3 font-semibold text-gray-900 border">{{ $p->nama }}</td>
                <td class="py-2.5 px-3 text-gray-600 border">{{ $p->instansi ?: '-' }}</td>
                <td class="py-2.5 px-3 text-center text-gray-600 border">{{ $p->gender ?: 'L' }}</td>
                <td class="py-2.5 px-3 text-gray-600 border font-mono">{{ $p->nip_nohp ?: '-' }}</td>
                <td class="py-2.5 px-3 text-gray-500 border">{{ $p->email ?: '-' }}</td>
                <td class="py-2.5 px-3 text-center border">
                  @if($p->paraf_image)
                    <img src="{{ $p->paraf_image }}" alt="Paraf" class="h-7 mx-auto object-contain">
                  @else
                    <span class="text-gray-300 italic text-[11px]">-</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">
                  Belum ada peserta yang didaftarkan pada laporan ini.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- =================================================================== -->
  <!-- TAB 4: DOKUMENTASI FOTO RAPAT                                       -->
  <!-- =================================================================== -->
  <div x-show="activeTab === 'foto'" class="space-y-5" style="display: none;">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xs space-y-4">
      <div class="border-b pb-3 flex items-center justify-between">
        <div>
          <h3 class="text-sm font-bold text-gray-900">Foto Dokumentasi Pelaksanaan Kegiatan</h3>
          <p class="text-xs text-gray-500">Dokumentasi yang akan dilampirkan pada halaman ke-4 PDF laporan.</p>
        </div>
        <span class="text-xs font-bold text-maroon bg-maroon-50 px-2.5 py-1 rounded-md">
          {{ count($notulensi->dokumentasi_foto ?? []) }} Foto Tersimpan
        </span>
      </div>

      @if(!empty($notulensi->dokumentasi_foto) && count($notulensi->dokumentasi_foto) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-2">
          @foreach($notulensi->dokumentasi_foto as $foto)
            <div class="group relative rounded-xl overflow-hidden border border-gray-200 shadow-2xs aspect-4/3 bg-gray-100">
              <img src="{{ asset('storage/' . $foto) }}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
              <a href="{{ asset('storage/' . $foto) }}" target="_blank" 
                 class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold transition">
                Lihat Foto Penuh ↗
              </a>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-12 text-gray-400 text-xs">
          Belum ada foto dokumentasi yang dilampirkan. Klik tombol <b>Edit Dokumen</b> untuk menambahkan foto.
        </div>
      @endif
    </div>
  </div>

</div>
@endsection