@extends('layouts.app')

@section('content')
@php
    $isVerifikator = auth()->user()->hasAnyRole(['admin', 'superadmin', 'verif_inovasi']);
    
    $statusColor = function($status) {
        return match($status) {
            'Disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'Revisi', 'Dikembalikan' => 'bg-amber-100 text-amber-700 border-amber-200',
            'Ditolak' => 'bg-red-100 text-red-700 border-red-200',
            default => 'bg-gray-100 text-gray-700 border-gray-200',
        };
    };

    $storageUrl = function($path) {
        if (blank($path)) return null;
        return asset('storage/' . ltrim($path, '/'));
    };

    // Hitung Total Skor Sementara
    $totalSkor = 0;
    foreach($indicators as $ind) {
        $ev = $evidencesMap->get($ind->id);
        if($ev && $ev->review_status !== 'Ditolak') {
            $totalSkor += ($ev->parameter_weight ?? 0) * ($ind->pengali ?? 1);
        }
    }
@endphp

<section class="max-w-6xl mx-auto px-4 py-6" x-data="{ tab: 'profil', isEditing: false }">

    <!-- 1. BANNER SAMPUL INOVASI -->
    @if(!empty($inovasi->sampul_file))
        <div class="w-full h-52 md:h-80 rounded-3xl mb-6 bg-cover bg-center shadow-lg border border-gray-200 relative overflow-hidden group" style="background-image: url('{{ $storageUrl($inovasi->sampul_file) }}');">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/25 to-transparent"></div>
            <div class="absolute bottom-6 left-6 right-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4 text-white">
                <div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider backdrop-blur-md {{ $inovasi->kategori_ima == 'PRO IMA' ? 'bg-amber-500/80 text-white' : 'bg-blue-600/80 text-white' }}">
                        {{ $inovasi->kategori_ima }}
                    </span>
                    <h2 class="text-2xl md:text-3xl font-extrabold mt-2 leading-tight drop-shadow">{{ $inovasi->judul }}</h2>
                    <p class="text-xs md:text-sm text-gray-200 mt-1 drop-shadow">{{ $inovasi->opd_unit ?? 'Perangkat Daerah belum disetel' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex flex-col items-end backdrop-blur-md bg-black/30 px-4 py-1.5 rounded-xl border border-white/20">
                        <span class="text-[10px] text-gray-300 font-bold uppercase tracking-wider">Estimasi Skor</span>
                        <span class="text-xl font-black text-amber-400">{{ $totalSkor }}</span>
                    </div>
                    <span class="px-3 py-1 rounded-xl text-xs font-bold backdrop-blur-md {{ $statusColor($inovasi->asistensi_status) }}">
                        Status: {{ $inovasi->asistensi_status }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. HEADER KONTROL & TOMBOL AKSI -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
        <div>
            @if(empty($inovasi->sampul_file))
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $inovasi->kategori_ima == 'PRO IMA' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $inovasi->kategori_ima }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusColor($inovasi->asistensi_status) }}">
                        Status: {{ $inovasi->asistensi_status }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900">{{ $inovasi->judul }}</h1>
                <p class="text-gray-500 text-sm mt-1">OPD: {{ $inovasi->opd_unit ?? 'Belum disetel' }}</p>
            @else
                <h1 class="text-xl font-bold text-gray-900">Detail & Asistensi Inovasi</h1>
                <p class="text-gray-500 text-xs mt-0.5">Kelola data profil, kelengkapan berkas evidence, dan catatan evaluasi.</p>
            @endif
        </div>
        
        <!-- TOMBOL AKSI -->
        <div class="flex flex-wrap items-center gap-2.5 w-full md:w-auto">
            <a href="{{ route('sigap-ima.index') }}" class="px-4 py-2 border border-gray-300 rounded-xl hover:bg-gray-50 text-sm font-semibold transition text-gray-700">
                &larr; Kembali
            </a>

            @if(!$isVerifikator)
                <button type="button" @click="isEditing = !isEditing; if(isEditing) tab = 'profil'" class="inline-flex items-center gap-1.5 px-4 py-2 border border-amber-500 text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-xl text-sm font-bold transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    <span x-text="isEditing ? 'Batal Edit Profil' : 'Edit Profil'"></span>
                </button>

                <a href="{{ route('sigap-ima.evidence', $inovasi->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-900 text-white rounded-xl hover:bg-black text-sm font-bold transition shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Edit Evidence
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200 p-4 rounded-xl font-medium text-sm flex items-center gap-3">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- 3. TABS NAVIGASI -->
    <div class="flex gap-2 border-b border-gray-200 mb-6">
        <button @click="tab = 'profil'" :class="tab === 'profil' ? 'border-amber-500 text-amber-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 transition outline-none">
            Profil & Metadata
        </button>
        <button @click="tab = 'evidence'; isEditing = false" :class="tab === 'evidence' ? 'border-amber-500 text-amber-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 transition outline-none">
            Penilaian Evidence
        </button>
    </div>

    <!-- ============================================== -->
    <!-- TAB 1: PROFIL & METADATA -->
    <!-- ============================================== -->
    <div x-show="tab === 'profil'" x-transition.opacity class="grid lg:grid-cols-3 gap-6">
        <!-- Konten Profil Sama Seperti Sebelumnya (Disembunyikan demi keringkasan jika mau, tapi ini full code) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- MODE EDIT -->
            <div x-show="isEditing" x-transition.opacity style="display: none;">
                <form action="{{ route('sigap-ima.update', $inovasi->id) }}" method="POST" class="bg-white border border-amber-300 rounded-2xl p-6 shadow-md space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <h3 class="text-lg font-bold text-gray-900">Perbarui Profil Inovasi</h3>
                        <button type="button" @click="isEditing = false" class="text-xs text-gray-500 hover:text-gray-800 font-bold">&times; Tutup Edit</button>
                    </div>

                    <div x-data="showInlineUploader('sampul_file')" class="border-2 border-dashed border-amber-300 rounded-xl p-4 bg-amber-50/50">
                        <span class="block text-xs font-bold text-amber-900 mb-1">Ganti Foto Sampul (Biarkan kosong jika tidak diubah)</span>
                        <div class="bg-white p-3 border border-gray-200 rounded-lg text-center cursor-pointer hover:border-amber-500" @click="$refs.fileInput.click()">
                            <span class="text-xs text-gray-500">Klik untuk memilih foto sampul baru</span>
                            <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" accept=".jpg,.jpeg,.png" class="hidden">
                        </div>
                        <template x-for="(f, i) in files" :key="f.id">
                            <div class="mt-2 text-xs p-2 bg-white border rounded flex justify-between items-center">
                                <span class="truncate w-2/3" x-text="f.name"></span>
                                <span class="text-[10px] text-emerald-600 font-bold" x-text="f.status === 'success' ? '✅ Terunggah' : 'Mengunggah...'"></span>
                                <input type="hidden" :name="inputName" :value="f.temp_path">
                            </div>
                        </template>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <label class="block sm:col-span-2">
                            <span class="text-xs font-semibold text-gray-700">Judul Inovasi *</span>
                            <input type="text" name="judul" value="{{ $inovasi->judul }}" required class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Perangkat Daerah (OPD)</span>
                            <input type="text" name="opd_unit" value="{{ $inovasi->opd_unit }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Koordinat Lokasi</span>
                            <input type="text" name="koordinat" value="{{ $inovasi->koordinat }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">
                        </label>
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 text-sm border-t pt-4 border-gray-100">
                        @foreach(['urusan_pemerintah' => 'Urusan Pemerintah', 'klasifikasi' => 'Klasifikasi', 'jenis_inovasi' => 'Jenis Inovasi', 'bentuk_inovasi_daerah' => 'Bentuk Inovasi', 'asta_cipta' => 'Asta Cita', 'program_prioritas' => 'Program Prioritas', 'misi_walikota' => 'Misi Walikota'] as $key => $label)
                            <label class="block">
                                <span class="text-xs font-semibold text-gray-700">{{ $label }}</span>
                                <select name="{{ $key }}" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">
                                    <option value="">— Pilih {{ $label }} —</option>
                                    @if(isset($dropdowns[$key]))
                                        @foreach($dropdowns[$key] as $opt)
                                            <option value="{{ $opt->label }}" @selected($inovasi->$key == $opt->label)>{{ $opt->label }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </label>
                        @endforeach
                        
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Tahapan Inovasi</span>
                            <select name="tahap_inovasi" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">
                                <option value="">— Pilih —</option>
                                @foreach(['Inisiatif', 'Uji Coba', 'Penerapan'] as $opt)
                                    <option value="{{ $opt }}" @selected($inovasi->tahap_inovasi == $opt)>{{ $opt }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>

                    <div class="space-y-4 border-t pt-4 border-gray-100">
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Rancang Bangun Inovasi</span>
                            <textarea name="rancang_bangun" rows="5" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">{{ $inovasi->rancang_bangun }}</textarea>
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Tujuan Inovasi</span>
                            <textarea name="tujuan" rows="3" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">{{ $inovasi->tujuan }}</textarea>
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Manfaat yang Diperoleh</span>
                            <textarea name="manfaat" rows="3" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">{{ $inovasi->manfaat }}</textarea>
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Hasil Inovasi</span>
                            <textarea name="hasil_inovasi" rows="3" class="mt-1 w-full rounded-lg border-gray-300 focus:border-amber-500 text-sm">{{ $inovasi->hasil_inovasi }}</textarea>
                        </label>
                    </div>

                    <!-- Pembaruan Dokumen -->
                    <div class="border-t pt-4 border-gray-100">
                        <span class="block text-sm font-bold text-gray-800 mb-3">Pembaruan Dokumen Lampiran</span>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div x-data="showInlineUploader('anggaran_file')" class="border border-gray-200 p-3 rounded-xl bg-gray-50">
                                <span class="block text-xs font-semibold text-gray-700 mb-1">Dokumen Anggaran</span>
                                <input type="file" @change="handleFileSelect($event)" accept=".pdf,.jpg,.png" class="text-xs w-full text-gray-500">
                                <template x-for="f in files" :key="f.id"><input type="hidden" :name="inputName" :value="f.temp_path"></template>
                            </div>
                            <div x-data="showInlineUploader('profil_bisnis_file')" class="border border-gray-200 p-3 rounded-xl bg-gray-50">
                                <span class="block text-xs font-semibold text-gray-700 mb-1">Profil Bisnis (Opsional)</span>
                                <input type="file" @change="handleFileSelect($event)" accept=".pdf,.ppt,.pptx" class="text-xs w-full text-gray-500">
                                <template x-for="f in files" :key="f.id"><input type="hidden" :name="inputName" :value="f.temp_path"></template>
                            </div>
                            <div x-data="showInlineUploader('haki_file')" class="border border-gray-200 p-3 rounded-xl bg-gray-50">
                                <span class="block text-xs font-semibold text-gray-700 mb-1">Dokumen HAKI (Opsional)</span>
                                <input type="file" @change="handleFileSelect($event)" accept=".pdf,.jpg,.png" class="text-xs w-full text-gray-500">
                                <template x-for="f in files" :key="f.id"><input type="hidden" :name="inputName" :value="f.temp_path"></template>
                            </div>
                            <div x-data="showInlineUploader('penghargaan_file')" class="border border-gray-200 p-3 rounded-xl bg-gray-50">
                                <span class="block text-xs font-semibold text-gray-700 mb-1">Sertifikat / Penghargaan</span>
                                <input type="file" @change="handleFileSelect($event)" accept=".pdf,.jpg,.png" class="text-xs w-full text-gray-500">
                                <template x-for="f in files" :key="f.id"><input type="hidden" :name="inputName" :value="f.temp_path"></template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t pt-5">
                        <button type="button" @click="isEditing = false" class="px-4 py-2 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-6 py-2 bg-amber-500 text-gray-900 font-bold rounded-xl hover:bg-amber-400 transition shadow">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <!-- MODE LIHAT -->
            <div x-show="!isEditing" x-transition.opacity class="space-y-6">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b pb-2 mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Metadata Inovasi</h3>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div><span class="block text-gray-500 text-xs">Urusan Pemerintah</span><span class="font-medium text-gray-900">{{ $inovasi->urusan_pemerintah ?? '—' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Klasifikasi</span><span class="font-medium text-gray-900">{{ $inovasi->klasifikasi ?? '—' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Jenis Inovasi</span><span class="font-medium text-gray-900">{{ $inovasi->jenis_inovasi ?? '—' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Bentuk Inovasi</span><span class="font-medium text-gray-900">{{ $inovasi->bentuk_inovasi_daerah ?? '—' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Asta Cita</span><span class="font-medium text-gray-900">{{ $inovasi->asta_cipta ?? '—' }}</span></div>
                        <div><span class="block text-gray-500 text-xs">Program Prioritas</span><span class="font-medium text-gray-900">{{ $inovasi->program_prioritas ?? '—' }}</span></div>
                        <div class="sm:col-span-2"><span class="block text-gray-500 text-xs">Misi Walikota</span><span class="font-medium text-gray-900">{{ $inovasi->misi_walikota ?? '—' }}</span></div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-5">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2">Deskripsi Inovasi</h3>
                    <div><span class="block text-gray-500 text-xs mb-1 font-semibold uppercase">Rancang Bangun</span><div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm leading-relaxed whitespace-pre-line">{{ $inovasi->rancang_bangun ?? 'Belum ada penjelasan.' }}</div></div>
                    <div><span class="block text-gray-500 text-xs mb-1 font-semibold uppercase">Tujuan Inovasi</span><div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm leading-relaxed whitespace-pre-line">{{ $inovasi->tujuan ?? '—' }}</div></div>
                    <div><span class="block text-gray-500 text-xs mb-1 font-semibold uppercase">Manfaat yang Diperoleh</span><div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm leading-relaxed whitespace-pre-line">{{ $inovasi->manfaat ?? '—' }}</div></div>
                    <div><span class="block text-gray-500 text-xs mb-1 font-semibold uppercase">Hasil Inovasi</span><div class="text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm leading-relaxed whitespace-pre-line">{{ $inovasi->hasil_inovasi ?? '—' }}</div></div>
                </div>

                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Lampiran Berkas Utama</h3>
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        @foreach(['Dokumen Anggaran' => $inovasi->anggaran_file, 'Profil Bisnis' => $inovasi->profil_bisnis_file, 'Dokumen HAKI' => $inovasi->haki_file, 'Sertifikat / Penghargaan' => $inovasi->penghargaan_file] as $label => $file)
                            <div class="border border-gray-200 rounded-xl p-3 flex items-center justify-between">
                                <span class="font-medium text-gray-700">{{ $label }}</span>
                                @if(!empty($file))
                                    <a href="{{ $storageUrl($file) }}" target="_blank" class="text-xs bg-amber-100 text-amber-700 px-3 py-1 rounded-md font-bold hover:bg-amber-200 transition">Lihat Berkas</a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: PANEL ASISTENSI -->
        <div class="lg:col-span-1">
            <div class="bg-gray-50 border border-amber-200 rounded-2xl p-5 shadow-sm sticky top-6">
                <h3 class="text-lg font-bold text-amber-800 mb-4">Review Profil Inovasi</h3>
                
                @if($isVerifikator)
                    <form action="{{ route('sigap-ima.review.profile', $inovasi->id) }}" method="POST">
                        @csrf
                        <label class="block mb-3">
                            <span class="text-sm font-semibold text-gray-700">Status Kelayakan</span>
                            <select name="asistensi_status" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                                <option value="Menunggu Verifikasi" @selected($inovasi->asistensi_status == 'Menunggu Verifikasi')>Menunggu Verifikasi</option>
                                <option value="Revisi" @selected($inovasi->asistensi_status == 'Revisi')>Revisi</option>
                                <option value="Disetujui" @selected($inovasi->asistensi_status == 'Disetujui')>Disetujui</option>
                                <option value="Ditolak" @selected($inovasi->asistensi_status == 'Ditolak')>Ditolak</option>
                            </select>
                        </label>
                        <label class="block mb-4">
                            <span class="text-sm font-semibold text-gray-700">Catatan Revisi / Pesan</span>
                            <textarea name="asistensi_note" rows="4" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-amber-500" placeholder="Tulis catatan perbaikan...">{{ $inovasi->asistensi_note }}</textarea>
                        </label>
                        <button type="submit" class="w-full py-2 bg-amber-500 text-gray-900 font-bold rounded-xl hover:bg-amber-400 transition shadow">
                            Simpan Review Profil
                        </button>
                    </form>
                @else
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 mb-1">Status Saat Ini:</span>
                            <span class="px-3 py-1 rounded-md text-sm font-bold border block text-center {{ $statusColor($inovasi->asistensi_status) }}">
                                {{ $inovasi->asistensi_status }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-gray-500 mb-1">Catatan Verifikator:</span>
                            <div class="bg-white p-3 rounded-lg border border-gray-200 text-sm text-gray-700 min-h-[80px]">
                                {{ $inovasi->asistensi_note ?? 'Tidak ada catatan revisi.' }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 2: EVIDENCE & PENILAIAN (SKOR DINAMIS) -->
    <!-- ============================================== -->
    <div x-show="tab === 'evidence'" x-transition.opacity style="display: none;" class="space-y-6">
        <div class="bg-amber-50 border border-amber-200 text-amber-900 p-4 rounded-xl text-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p><strong>Review Evidence:</strong> Verifikasi pemenuhan berkas pendukung pada 20 indikator penilaian IMA.</p>
            @if(!$isVerifikator)
                <a href="{{ route('sigap-ima.evidence', $inovasi->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-600 text-white font-bold rounded-lg text-xs hover:bg-amber-700 transition w-fit">
                    Edit / Lengkapi Evidence &rarr;
                </a>
            @endif
        </div>

        @foreach($indicators as $ind)
            @php 
                $ev = $evidencesMap->get($ind->id); 
                $pengali = $ind->pengali ?? 1;
                $poin = $ev->parameter_weight ?? 0;
                $skorAkhir = $poin * $pengali;
            @endphp
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row mb-6">
                
                <!-- Kiri: Bukti dari Inovator -->
                <div class="flex-1 p-6 border-b md:border-b-0 md:border-r border-gray-100 relative">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="flex gap-3">
                            <span class="w-8 h-8 flex-shrink-0 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold text-sm">{{ $ind->no_urut }}</span>
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg leading-tight">{{ $ind->nama_indikator }}</h3>
                                <div class="mt-1.5">
                                    <span class="text-[10px] font-extrabold bg-amber-100 text-amber-800 px-2 py-0.5 rounded border border-amber-200 uppercase tracking-wider">
                                        Bobot Pengali: &times;{{ $pengali }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if(!$isVerifikator)
                            <a href="{{ route('sigap-ima.evidence', $inovasi->id) }}" class="text-xs text-amber-600 hover:underline font-semibold whitespace-nowrap">
                                Edit Bukti
                            </a>
                        @endif
                    </div>
                    
                    @if($ev)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 text-sm space-y-3 relative overflow-hidden">
                            <!-- Skor Badge (Pojok Kanan Atas) -->
                            <div class="absolute top-0 right-0 bg-emerald-500 text-white pl-4 pr-3 py-1 rounded-bl-xl shadow-sm border-b border-l border-emerald-600">
                                <span class="text-[10px] uppercase font-bold opacity-80 mr-1">Skor:</span>
                                <span class="text-lg font-black">{{ $skorAkhir }}</span>
                            </div>

                            <div class="pr-24">
                                <p class="mb-2">
                                    <span class="font-semibold text-gray-500 block text-[11px] uppercase tracking-wide">Parameter yang Diklaim:</span> 
                                    <span class="font-bold text-gray-900 text-base">{{ $ev->parameter_label ?? '—' }}</span>
                                    <span class="text-xs font-bold text-amber-600 ml-1">(Poin Dasar: {{ $poin }})</span>
                                </p>
                                <p><span class="font-semibold text-gray-600">Keterangan:</span> <br> {{ $ev->deskripsi ?? '—' }}</p>
                            </div>
                            
                            @if($ev->link_url)
                                <p class="pt-2"><span class="font-semibold text-gray-600">Tautan Bukti:</span> <br> 
                                    <a href="{{ $ev->link_url }}" target="_blank" class="text-blue-600 hover:underline break-all">{{ $ev->link_url }}</a>
                                </p>
                            @endif
                            
                            @if($ev->files->count() > 0)
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <span class="font-semibold text-gray-600 block mb-2 text-xs uppercase tracking-wide">Lampiran Berkas ({{ $ev->files->count() }}):</span>
                                    <ul class="space-y-2">
                                        @foreach($ev->files as $file)
                                            <li>
                                                <a href="{{ $storageUrl($file->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-amber-700 hover:bg-amber-50 hover:border-amber-300 transition w-full shadow-sm">
                                                    📄 <span class="truncate font-medium">{{ $file->file_name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-32 bg-gray-50 border border-dashed border-gray-300 rounded-xl mt-4">
                            <span class="text-2xl mb-1">📭</span>
                            <p class="text-sm text-gray-500">Inovator belum mengisi bukti indikator ini.</p>
                        </div>
                    @endif
                </div>

                <!-- Kanan: Penilaian Verifikator -->
                <div class="w-full md:w-1/3 bg-white border-l border-gray-100 p-6">
                    <h4 class="font-bold text-gray-800 mb-4 flex items-center justify-between border-b pb-2">
                        <span>Penilaian Reviewer</span>
                        @if($ev && $ev->review_status)
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wide border {{ $statusColor($ev->review_status) }}">{{ $ev->review_status }}</span>
                        @endif
                    </h4>

                    @if($ev)
                        @if($isVerifikator)
                            <form action="{{ route('sigap-ima.review.evidence', $ev->id) }}" method="POST">
                                @csrf
                                <label class="block mb-2 text-xs font-semibold text-gray-600 uppercase">Keputusan Evaluasi</label>
                                <select name="review_status" class="w-full rounded-lg border-gray-300 text-sm focus:ring-amber-500 mb-4" required>
                                    <option value="">— Pilih Keputusan —</option>
                                    <option value="Disetujui" @selected($ev->review_status == 'Disetujui')>✅ Disetujui (Memenuhi)</option>
                                    <option value="Revisi" @selected($ev->review_status == 'Revisi')>⚠️ Perlu Revisi / Tambah Bukti</option>
                                    <option value="Ditolak" @selected($ev->review_status == 'Ditolak')>❌ Ditolak (Tidak Valid)</option>
                                </select>
                                
                                <label class="block mb-2 text-xs font-semibold text-gray-600 uppercase">Catatan</label>
                                <textarea name="review_note" rows="3" class="w-full rounded-lg border-gray-300 text-sm focus:ring-amber-500 mb-4" placeholder="Tulis instruksi perbaikan untuk indikator ini...">{{ $ev->review_note }}</textarea>
                                
                                <button type="submit" class="w-full py-2.5 bg-gray-900 text-white font-bold rounded-lg hover:bg-black transition text-sm shadow">
                                    Simpan Penilaian
                                </button>
                            </form>
                        @else
                            <div class="bg-gray-50 p-4 border border-gray-200 rounded-xl text-sm text-gray-700 min-h-[120px]">
                                <p class="text-[11px] text-gray-500 mb-1 font-bold uppercase tracking-wide">Catatan Verifikator:</p>
                                {{ $ev->review_note ?? 'Belum ada catatan evaluasi dari tim reviewer.' }}
                            </div>
                        @endif
                    @else
                        <p class="text-xs text-gray-400 italic text-center py-8">Menunggu inovator melengkapi bukti sebelum dinilai.</p>
                    @endif
                </div>
                
            </div>
        @endforeach
    </div>
</section>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
<script>
// Komponen AlpineJS untuk upload berkas baru di mode edit inline
document.addEventListener('alpine:init', () => {
    Alpine.data('showInlineUploader', (inputName) => ({
        files: [],
        inputName: inputName,
        uploadUrl: "{{ route('sigap-ima.upload-chunk') }}",
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

        async handleFileSelect(e) {
            const raw = e.target.files[0];
            if (!raw) return;

            this.files = [];
            const fileId = 'up_' + Date.now();
            this.files.push({
                id: fileId,
                name: raw.name,
                progress: 0,
                status: 'uploading',
                temp_path: ''
            });

            e.target.value = '';

            try {
                let ready = raw;
                if (raw.type.startsWith('image/')) {
                    const bitmap = await createImageBitmap(raw);
                    const canvas = document.createElement('canvas');
                    const MAX = 1600;
                    let w = bitmap.width, h = bitmap.height;
                    if (w > h && w > MAX) { h = Math.round((h * MAX) / w); w = MAX; }
                    else if (h > MAX) { w = Math.round((w * MAX) / h); h = MAX; }
                    canvas.width = w; canvas.height = h;
                    canvas.getContext('2d').drawImage(bitmap, 0, 0, w, h);
                    ready = await new Promise(res => {
                        canvas.toBlob(b => res(new File([b], raw.name.replace(/\.[^/.]+$/, "") + ".jpg", { type: 'image/jpeg' })), 'image/jpeg', 0.75);
                    });
                }

                const CHUNK = 512 * 1024;
                const total = Math.ceil(ready.size / CHUNK);
                const fId = 'inline_' + Date.now();

                for (let i = 0; i < total; i++) {
                    const blob = ready.slice(i * CHUNK, Math.min((i + 1) * CHUNK, ready.size));
                    const fd = new FormData();
                    fd.append('file', blob);
                    fd.append('file_id', fId);
                    fd.append('chunk_index', i);
                    fd.append('total_chunks', total);
                    fd.append('original_name', ready.name);

                    const res = await fetch(this.uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': this.csrfToken, 'Accept': 'application/json' },
                        body: fd
                    });

                    const data = await res.json();
                    if (data.completed) {
                        this.files[0].status = 'success';
                        this.files[0].temp_path = data.temp_path;
                        this.files = [...this.files];
                    }
                }
            } catch (err) {
                console.error(err);
                this.files[0].status = 'error';
                this.files = [...this.files];
            }
        }
    }));
});
</script>
@endpush