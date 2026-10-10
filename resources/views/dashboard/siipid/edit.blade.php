@extends('layouts.app')

@section('content')
<div x-data="siipidEditForm()" class="max-w-5xl mx-auto space-y-6">

  {{-- Header & Breadcrumb --}}
  <div class="flex items-center justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
        <a href="{{ route('sigap-siipid.index') }}" class="hover:text-maroon">SIGAP SIIPID</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">Perbaiki Usulan</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">Perbaikan Data Prestasi Inovator</h1>
      <p class="text-xs text-gray-500">Perbarui data atau berkas dokumen sesuai catatan reviewer.</p>
    </div>

    <a href="{{ route('sigap-siipid.show', $prestasi->uuid) }}" class="px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition shadow-2xs">
      Kembali
    </a>
  </div>

  {{-- BANNER CATATAN REVIEWER (JIKA DIKEMBALIKAN PERBAIKAN) --}}
  @if($prestasi->status === 'dikembalikan_perbaikan' && $prestasi->catatan_review_terakhir)
    <div class="rounded-2xl border-2 border-amber-400 bg-amber-50 p-5 shadow-sm space-y-2">
      <div class="flex items-center gap-2 text-amber-900 font-extrabold text-sm">
        <span class="text-base">⚠️</span>
        <span>CATATAN REVIEWER YANG HARUS DIPERBAIKI:</span>
      </div>
      <div class="text-xs text-amber-900 bg-white/90 p-3.5 rounded-xl border border-amber-200/80 leading-relaxed font-medium">
        {{ $prestasi->catatan_review_terakhir }}
      </div>
      <p class="text-[11px] text-amber-700 italic">
        Silakan lengkapi atau ubah formulir di bawah ini, lalu klik tombol <strong>"Kirim Ulang Usulan"</strong> di bagian bawah.
      </p>
    </div>
  @endif

  <form action="{{ route('sigap-siipid.update', $prestasi->uuid) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- CARD 1: INOVASI INDUK (READONLY/INFO) --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-3">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">1</span>
        <h3 class="font-bold text-gray-900 text-sm">Inovasi Induk Terpilih</h3>
      </div>
      <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center justify-between">
        <div>
          <div class="font-bold text-gray-900 text-sm">{{ $prestasi->innovable->judul ?? 'Inovasi' }}</div>
          <div class="text-xs text-gray-500 mt-0.5">
            Sumber: <strong>{{ class_basename($prestasi->innovable_type) === 'Inovasi' ? 'SIGAP Inovasi (OPD)' : 'SIGAP IMA (Masyarakat)' }}</strong>
          </div>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-white border border-gray-200 text-gray-700">Terkunci</span>
      </div>
    </div>

    {{-- CARD 2: KATEGORI & BIODATA INOVATOR --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">2</span>
        <h3 class="font-bold text-gray-900 text-sm">Identitas Inovator & Kategori Sasaran</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Kategori Sasaran <span class="text-red-500">*</span></label>
          <select name="kategori_sasaran" x-model="kategoriSasaran" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
            <option value="asn">ASN (Aparatur Sipil Negara)</option>
            <option value="perangkat_daerah">Perangkat Daerah / OPD</option>
            <option value="masyarakat">Anggota Masyarakat / Umum</option>
            <option value="dprd">Anggota DPRD</option>
            <option value="kelompok">Kelompok / Tim Inovator</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Jenis Kepesertaan <span class="text-red-500">*</span></label>
          <select name="jenis_kepesertaan" x-model="jenisKepesertaan" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
            <option value="perorangan">Perorangan (Individu)</option>
            <option value="kelompok">Kelompok / Tim</option>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Lengkap Inovator <span class="text-red-500">*</span></label>
          <input type="text" name="nama_inovator" value="{{ old('nama_inovator', $prestasi->nama_inovator) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Nomor WhatsApp <span class="text-red-500">*</span></label>
          <input type="text" name="no_wa" value="{{ old('no_wa', $prestasi->no_wa) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <template x-if="kategoriSasaran === 'asn'">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">NIP (Nomor Induk Pegawai)</label>
            <input type="text" name="nip" value="{{ old('nip', $prestasi->nip) }}" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
          </div>
        </template>

        <template x-if="kategoriSasaran === 'masyarakat'">
          <div>
            <label class="block text-xs font-bold text-gray-700 mb-1.5">NIK (KTP)</label>
            <input type="text" name="nik" value="{{ old('nik', $prestasi->nik) }}" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
          </div>
        </template>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Email Inovator <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="{{ old('email', $prestasi->email) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Instansi / Unit Kerja / Komunitas</label>
          <input type="text" name="opd_instansi" value="{{ old('opd_instansi', $prestasi->opd_instansi) }}" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Jabatan / Profesi</label>
          <input type="text" name="jabatan" value="{{ old('jabatan', $prestasi->jabatan) }}" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>
      </div>

      {{-- REPEATER ANGGOTA TIM --}}
      <div x-show="jenisKepesertaan === 'kelompok'" x-transition class="border-t border-gray-100 pt-4 space-y-3">
        <div class="flex items-center justify-between">
          <label class="block text-xs font-bold text-gray-700">Daftar Anggota Tim & Peran</label>
          <button type="button" @click="tambahAnggota()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-[11px] transition">
            + Tambah Anggota
          </button>
        </div>

        <template x-for="(anggota, idx) in anggotaList" :key="idx">
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 p-3 rounded-xl bg-gray-50 border border-gray-200/60 relative">
            <input type="text" :name="'anggota_tim['+idx+'][nama]'" x-model="anggota.nama" placeholder="Nama Anggota" class="rounded-lg border-gray-300 text-xs py-1.5 focus:ring-maroon" required>
            <input type="text" :name="'anggota_tim['+idx+'][nik_nip]'" x-model="anggota.nik_nip" placeholder="NIP / NIK" class="rounded-lg border-gray-300 text-xs py-1.5 focus:ring-maroon">
            <div class="flex items-center gap-1.5">
              <input type="text" :name="'anggota_tim['+idx+'][peran]'" x-model="anggota.peran" placeholder="Peran Tim" class="w-full rounded-lg border-gray-300 text-xs py-1.5 focus:ring-maroon" required>
              <button type="button" @click="hapusAnggota(idx)" class="p-1.5 text-red-500 hover:bg-red-100 rounded-lg shrink-0">✕</button>
            </div>
          </div>
        </template>
      </div>
    </div>

    {{-- CARD 3: DATA PRESTASI & PENGHARGAAN --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">3</span>
        <h3 class="font-bold text-gray-900 text-sm">Rincian Prestasi & Capaian</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Nama Prestasi / Penghargaan <span class="text-red-500">*</span></label>
          <input type="text" name="nama_prestasi" value="{{ old('nama_prestasi', $prestasi->nama_prestasi) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Ajang Kompetisi <span class="text-red-500">*</span></label>
          <input type="text" name="ajang_kompetisi" value="{{ old('ajang_kompetisi', $prestasi->ajang_kompetisi) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Tingkat Prestasi <span class="text-red-500">*</span></label>
          <select name="tingkat_prestasi" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
            <option value="kota" @selected($prestasi->tingkat_prestasi === 'kota')>Tingkat Kota Makassar</option>
            <option value="provinsi" @selected($prestasi->tingkat_prestasi === 'provinsi')>Tingkat Provinsi Sulawesi Selatan</option>
            <option value="nasional" @selected($prestasi->tingkat_prestasi === 'nasional')>Tingkat Nasional</option>
            <option value="internasional" @selected($prestasi->tingkat_prestasi === 'internasional')>Tingkat Internasional</option>
            <option value="khusus" @selected($prestasi->tingkat_prestasi === 'khusus')>Kategori Khusus Pemerintah Kota</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Tahun Perolehan <span class="text-red-500">*</span></label>
          <input type="number" name="tahun_perolehan" value="{{ old('tahun_perolehan', $prestasi->tahun_perolehan) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Peringkat / Capaian</label>
          <input type="text" name="peringkat_capaian" value="{{ old('peringkat_capaian', $prestasi->peringkat_capaian) }}" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Lembaga Pemberi Penghargaan <span class="text-red-500">*</span></label>
          <input type="text" name="lembaga_pemberi" value="{{ old('lembaga_pemberi', $prestasi->lembaga_pemberi) }}" required class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-gray-700 mb-1.5">Deskripsi Singkat Capaian & Dampak Prestasi</label>
          <textarea name="deskripsi_prestasi" rows="3" class="w-full rounded-xl border-gray-300 text-xs focus:border-maroon focus:ring-maroon">{{ old('deskripsi_prestasi', $prestasi->deskripsi_prestasi) }}</textarea>
        </div>
      </div>
    </div>

    {{-- CARD 4: UNGGAH/GANTI DOKUMEN BUKTI --}}
    <div class="bg-white rounded-2xl border border-gray-200/80 p-5 sm:p-6 shadow-2xs space-y-4">
      <div class="flex items-center gap-2.5 border-b border-gray-100 pb-3">
        <span class="w-6 h-6 rounded-md bg-maroon/10 text-maroon flex items-center justify-center font-bold text-xs">4</span>
        <h3 class="font-bold text-gray-900 text-sm">Dokumen & Berkas Pendukung</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Piagam / Sertifikat / SK Prestasi
          </label>
          @if($prestasi->bukti_prestasi_file)
            <div class="text-[11px] text-emerald-700 mb-1">
              ✓ File tersimpan: <a href="{{ asset('storage/'.$prestasi->bukti_prestasi_file) }}" target="_blank" class="underline font-bold">Lihat Berkas Saat Ini</a>
            </div>
          @endif
          <input type="file" name="bukti_prestasi_file" accept=".pdf,image/*" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
          <span class="text-[10px] text-gray-400">Kosongkan jika tidak ingin mengubah file lama.</span>
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Bukti Penerapan & Kemanfaatan Inovasi
          </label>
          @if($prestasi->bukti_penerapan_file)
            <div class="text-[11px] text-emerald-700 mb-1">
              ✓ File tersimpan: <a href="{{ asset('storage/'.$prestasi->bukti_penerapan_file) }}" target="_blank" class="underline font-bold">Lihat Berkas</a>
            </div>
          @endif
          <input type="file" name="bukti_penerapan_file" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Surat Pernyataan Keaslian Data
          </label>
          @if($prestasi->surat_keaslian_file)
            <div class="text-[11px] text-emerald-700 mb-1">
              ✓ File tersimpan: <a href="{{ asset('storage/'.$prestasi->surat_keaslian_file) }}" target="_blank" class="underline font-bold">Lihat Berkas</a>
            </div>
          @endif
          <input type="file" name="surat_keaslian_file" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>

        <div>
          <label class="block text-xs font-bold text-gray-700 mb-1">
            Surat Kontribusi Tim (Bila Kelompok)
          </label>
          @if($prestasi->surat_kontribusi_tim_file)
            <div class="text-[11px] text-emerald-700 mb-1">
              ✓ File tersimpan: <a href="{{ asset('storage/'.$prestasi->surat_kontribusi_tim_file) }}" target="_blank" class="underline font-bold">Lihat Berkas</a>
            </div>
          @endif
          <input type="file" name="surat_kontribusi_tim_file" accept=".pdf" class="w-full text-xs border border-gray-300 rounded-xl p-2 focus:ring-maroon">
        </div>
      </div>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="flex items-center justify-end gap-3 pt-2">
      <button type="submit" name="action_type" value="draft" class="px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-bold text-xs shadow-2xs transition">
        Simpan sebagai Draft
      </button>

      <button type="submit" name="action_type" value="submit" class="px-6 py-2.5 rounded-xl bg-maroon hover:bg-maroon-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-2">
        <span>Kirim Ulang Usulan (Resubmit)</span>
        <span>→</span>
      </button>
    </div>

  </form>
</div>

<script>
function siipidEditForm() {
  return {
    kategoriSasaran: '{{ old('kategori_sasaran', $prestasi->kategori_sasaran) }}',
    jenisKepesertaan: '{{ old('jenis_kepesertaan', $prestasi->jenis_kepesertaan) }}',
    anggotaList: {!! json_encode(old('anggota_tim', $prestasi->anggota_tim ?: [])) !!},
    tambahAnggota() {
      this.anggotaList.push({ nama: '', nik_nip: '', peran: '' });
    },
    hapusAnggota(idx) {
      this.anggotaList.splice(idx, 1);
    }
  }
}
</script>
@endsection
