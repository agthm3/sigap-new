@extends('layouts.app')

@section('content')
<section class="max-w-5xl mx-auto px-4 py-8" x-data="imaWizard()">
    <!-- Header -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-2">
            Innovative Mayor Award
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900">Pendaftaran Inovasi SIGAP IMA</h1>
        <p class="text-gray-500 mt-2 text-sm">Lengkapi seluruh metadata inovasi, kontak PIC, dan lampiran berkas pendukung.</p>
    </div>

    <!-- Stepper Indicator -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center gap-2">
            <div :class="step >= 1 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">1</div>
            <span class="text-xs font-semibold" :class="step >= 1 ? 'text-amber-600' : 'text-gray-400'">Kategori & PIC</span>
            <div :class="step >= 2 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-10 sm:w-16 transition"></div>
            
            <div :class="step >= 2 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">2</div>
            <span class="text-xs font-semibold" :class="step >= 2 ? 'text-amber-600' : 'text-gray-400'">Metadata</span>
            <div :class="step >= 3 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-10 sm:w-16 transition"></div>
            
            <div :class="step >= 3 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">3</div>
            <span class="text-xs font-semibold" :class="step >= 3 ? 'text-amber-600' : 'text-gray-400'">Deskripsi</span>
            <div :class="step >= 4 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-10 sm:w-16 transition"></div>
            
            <div :class="step >= 4 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">4</div>
            <span class="text-xs font-semibold" :class="step >= 4 ? 'text-amber-600' : 'text-gray-400'">Berkas</span>
        </div>
    </div>

    <form action="{{ route('sigap-ima.store') }}" method="POST" id="formImaCreate" novalidate class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6 sm:p-10 relative min-h-[500px]" @submit.prevent="submitForm($event)">
        @csrf
        
        <!-- ================= LANGKAH 1: KATEGORI & KONTAK OPERATOR ================= -->
        <div x-show="step === 1" x-transition.opacity.duration.300ms>
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">1. Kategori & Kontak Operator</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Tentukan kategori pendaftaran IMA dan identitas operator (PIC) aktif.</p>
                </div>
                <span class="text-xs font-bold bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200">Langkah 1 dari 4</span>
            </div>
            
            <!-- Pilihan Kategori IMA -->
            <label class="block text-sm font-semibold text-gray-800 mb-2">Kategori Pendaftaran IMA <span class="text-red-500">*</span></label>
            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                <label class="cursor-pointer">
                    <input type="radio" name="kategori_ima" value="PRO IMA" x-model="formData.kategori" class="peer hidden">
                    <div class="border-2 border-gray-200 rounded-2xl p-5 peer-checked:border-amber-500 peer-checked:bg-amber-50/50 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-extrabold text-base text-gray-900">🚀 PRO IMA</h3>
                            <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded font-semibold">Telah Mengikuti IGA</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">Untuk inovasi dari perangkat daerah/unit kerja/individu yang sebelumnya pernah dilaporkan atau mengikuti Innovative Government Award (IGA).</p>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="kategori_ima" value="PEMULA IMA" x-model="formData.kategori" class="peer hidden">
                    <div class="border-2 border-gray-200 rounded-2xl p-5 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="font-extrabold text-base text-gray-900">🌱 PEMULA IMA</h3>
                            <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-semibold">Inovasi Baru</span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1 leading-relaxed">Untuk inovasi daerah yang belum pernah dilaporkan maupun diikutsertakan dalam IGA atau kompetisi sebelumnya.</p>
                    </div>
                </label>
            </div>

            <!-- Identitas Operator PIC -->
            <div class="bg-gray-50/70 p-6 rounded-2xl border border-gray-200">
                <div class="mb-4">
                    <h4 class="text-sm font-bold text-gray-800">Kontak Person / Operator Inovasi (PIC) <span class="text-red-500">*</span></h4>
                    <p class="text-xs text-gray-500 mt-0.5">Kontak utama untuk konfirmasi verifikasi, revisi berkas, dan tindak lanjut selama proses evaluasi.</p>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Nama Operator <span class="text-red-500">*</span></span>
                        <input type="text" name="operator_nama" x-model="formData.operator_nama" placeholder="Nama lengkap operator" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Jabatan Operator</span>
                        <input type="text" name="operator_jabatan" placeholder="Contoh: Pranata Komputer / Staf Perencanaan" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Nomor WhatsApp Aktif <span class="text-red-500">*</span></span>
                        <input type="text" name="operator_wa" x-model="formData.operator_wa" placeholder="Contoh: 081234567890" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Email Operator</span>
                        <input type="email" name="operator_email" placeholder="operator@makassarkota.go.id" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                </div>
            </div>
        </div>

        <!-- ================= LANGKAH 2: METADATA & KLASIFIKASI ================= -->
        <div x-show="step === 2" x-transition.opacity.duration.300ms style="display: none;">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">2. Metadata & Klasifikasi Inovasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Lengkapi identitas umum, perangkat daerah pengusul, dan relevansi kebijakan.</p>
                </div>
                <span class="text-xs font-bold bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200">Langkah 2 dari 4</span>
            </div>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Judul Inovasi <span class="text-red-500">*</span></span>
                    <input type="text" name="judul" x-model="formData.judul" placeholder="Tuliskan nama inovasi secara singkat dan jelas" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Perangkat Daerah (OPD/Unit) <span class="text-red-500">*</span></span>
                    <input type="text" name="opd_unit" x-model="formData.opd_unit" placeholder="Nama OPD atau unit kerja" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Tahapan Inovasi <span class="text-red-500">*</span></span>
                    <select name="tahap_inovasi" x-model="formData.tahap_inovasi" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Tahapan —</option>
                        <option value="Inisiatif">Inisiatif</option>
                        <option value="Uji Coba">Uji Coba</option>
                        <option value="Penerapan">Penerapan</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Inisiator Inovasi Daerah <span class="text-red-500">*</span></span>
                    <select name="inisiator_daerah" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Inisiator —</option>
                        <option value="Kepala Daerah">Kepala Daerah</option>
                        <option value="Anggota DPRD">Anggota DPRD</option>
                        <option value="OPD">OPD</option>
                        <option value="ASN">ASN</option>
                        <option value="Masyarakat">Masyarakat</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Nama Inisiator</span>
                    <input type="text" name="inisiator_nama" placeholder="Nama penggagas/inisiator" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Koordinat Lokasi Penerapan <span class="text-red-500">*</span></span>
                    <input type="text" name="koordinat" maxlength="300" x-model="formData.koordinat" placeholder="Format: -5.147665, 119.432732" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    <div class="flex justify-between text-[11px] text-gray-500 mt-1">
                        <span>Koordinat latitude dan longitude lokasi inovasi.</span>
                        <span :class="formData.koordinat.length > 300 ? 'text-red-600 font-bold' : ''" x-text="`${formData.koordinat.length}/300`"></span>
                    </div>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Klasifikasi Inovasi <span class="text-red-500">*</span></span>
                    <select name="klasifikasi" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Klasifikasi —</option>
                        @if(isset($dropdowns['klasifikasi']))
                            @foreach($dropdowns['klasifikasi'] as $opt)
                                <option value="{{ $opt->label }}">{{ $opt->label }}</option>
                            @endforeach
                        @else
                            <option value="Inovasi Perangkat Daerah">Inovasi Perangkat Daerah</option>
                            <option value="Inovasi Desa dan Kelurahan">Inovasi Desa dan Kelurahan</option>
                            <option value="Inovasi Masyarakat">Inovasi Masyarakat</option>
                        @endif
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Jenis Inovasi <span class="text-red-500">*</span></span>
                    <select name="jenis_inovasi" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Jenis —</option>
                        <option value="Digital">Digital</option>
                        <option value="Non Digital">Non Digital</option>
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Bentuk Inovasi Daerah <span class="text-red-500">*</span></span>
                    <select name="bentuk_inovasi_daerah" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Bentuk Inovasi —</option>
                        @if(isset($dropdowns['bentuk_inovasi_daerah']))
                            @foreach($dropdowns['bentuk_inovasi_daerah'] as $opt)
                                <option value="{{ $opt->label }}">{{ $opt->label }}</option>
                            @endforeach
                        @else
                            <option value="Inovasi Daerah lainnya sesuai kewenangan">Inovasi Daerah lainnya sesuai kewenangan</option>
                            <option value="Inovasi Pelayanan Publik">Inovasi Pelayanan Publik</option>
                            <option value="Inovasi Tata Kelola Pemerintah Daerah">Inovasi Tata Kelola Pemerintah Daerah</option>
                        @endif
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Urusan Pemerintah <span class="text-red-500">*</span></span>
                    <select name="urusan_pemerintah" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Urusan Pemerintah —</option>
                        @if(isset($dropdowns['urusan_pemerintah']))
                            @foreach($dropdowns['urusan_pemerintah'] as $opt)
                                <option value="{{ $opt->label }}">{{ $opt->label }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Asta Cita <span class="text-red-500">*</span></span>
                    <select name="asta_cipta" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Asta Cita —</option>
                        @if(isset($dropdowns['asta_cipta']))
                            @foreach($dropdowns['asta_cipta'] as $opt)
                                <option value="{{ $opt->label }}">{{ $opt->label }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Program Prioritas Walikota Makassar</span>
                    <select name="program_prioritas" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Program Prioritas —</option>
                        @if(isset($dropdowns['program_prioritas']))
                            @foreach($dropdowns['program_prioritas'] as $opt)
                                <option value="{{ $opt->label }}">{{ $opt->label }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Misi Walikota Makassar <span class="text-red-500">*</span></span>
                    <select name="misi_walikota" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Misi Walikota —</option>
                        @if(isset($dropdowns['misi_walikota']))
                            @foreach($dropdowns['misi_walikota'] as $opt)
                                <option value="{{ $opt->label }}">{{ $opt->label }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Waktu Uji Coba <span class="text-red-500">*</span></span>
                    <input type="date" name="waktu_uji_coba" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                </label>

                <label class="block">
                    <span class="text-sm font-semibold text-gray-700">Waktu Penerapan <span class="text-red-500">*</span></span>
                    <input type="date" name="waktu_penerapan" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                </label>

                <label class="block sm:col-span-2">
                    <span class="text-sm font-semibold text-gray-700">Apakah sudah ada perkembangan inovasi tersebut? <span class="text-red-500">*</span></span>
                    <select name="perkembangan_inovasi" class="mt-1.5 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                        <option value="">— Pilih Status Perkembangan —</option>
                        <option value="Ya">Ya</option>
                        <option value="Tidak">Tidak</option>
                    </select>
                </label>
            </div>
        </div>

        <!-- ================= LANGKAH 3: DESKRIPSI & REFERENSI VIDEO ================= -->
        <div x-show="step === 3" x-transition.opacity.duration.300ms style="display: none;">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">3. Deskripsi & Referensi Inovasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Uraikan rancang bangun, tujuan, manfaat, hasil, serta referensi terdahulu.</p>
                </div>
                <span class="text-xs font-bold bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200">Langkah 3 dari 4</span>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Rancang Bangun Inovasi (Minimal 300 Karakter) <span class="text-red-500">*</span>
                    </label>
                    <textarea name="rancang_bangun" rows="6" x-model="formData.rancang_bangun" class="w-full rounded-xl border border-gray-300 p-3.5 focus:border-amber-500 focus:ring-amber-500 text-sm leading-relaxed" placeholder="Jelaskan latar belakang, desain, arsitektur, dan cara kerja inovasi secara detail..."></textarea>
                    <div class="flex justify-between text-[11px] text-gray-500 mt-1">
                        <span>Minimal 300 karakter untuk menjelaskan rancang bangun inovasi.</span>
                        <span :class="formData.rancang_bangun.length < 300 ? 'text-amber-600 font-semibold' : 'text-emerald-600 font-bold'" x-text="`${formData.rancang_bangun.length}/300 karakter`"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Tujuan Inovasi Daerah <span class="text-red-500">*</span>
                    </label>
                    <textarea name="tujuan" rows="3" class="w-full rounded-xl border border-gray-300 p-3 focus:border-amber-500 focus:ring-amber-500 text-sm" placeholder="Uraikan target utama dan tujuan implementasi inovasi ini..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Manfaat yang Diperoleh <span class="text-red-500">*</span>
                    </label>
                    <textarea name="manfaat" rows="3" class="w-full rounded-xl border border-gray-300 p-3 focus:border-amber-500 focus:ring-amber-500 text-sm" placeholder="Jelaskan manfaat konkret bagi masyarakat atau tata kelola pemerintah..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Hasil Inovasi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="hasil_inovasi" rows="3" class="w-full rounded-xl border border-gray-300 p-3 focus:border-amber-500 focus:ring-amber-500 text-sm" placeholder="Deskripsikan output, outcome, atau capaian nyata dari penerapan inovasi..."></textarea>
                </div>

                <!-- Referensi Video / Penelitian Terdahulu (Minimal 3, Maksimal 5) -->
                <div class="border-t border-gray-200 pt-5">
                    <label class="block text-sm font-bold text-gray-800 mb-1">
                        Penelitian / Inovasi Terdahulu (Minimal 3, Maksimal 5) <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Cantumkan referensi karya atau inovasi daerah lain yang memiliki tema serupa dari YouTube atau Tuxedovation.</p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <a href="https://www.youtube.com/results?search_query=lomba+inovasi+daerah" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold hover:bg-red-700 transition">
                            <span>▶</span> Cari di YouTube
                        </a>
                        <a href="https://tuxedovation.inovasi.litbang.kemendagri.go.id/" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
                            <span>🔍</span> Buka Tuxedovation
                        </a>
                    </div>

                    <!-- Wrapper List Video Dinamis Alpine -->
                    <div class="space-y-3">
                        <template x-for="(vid, idx) in videos" :key="idx">
                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50/70 space-y-2 relative">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-gray-700" x-text="`Referensi #${idx + 1}`"></span>
                                    <template x-if="videos.length > 3">
                                        <button type="button" @click="removeVideo(idx)" class="text-red-500 hover:text-red-700 text-xs font-bold">&times; Hapus</button>
                                    </template>
                                </div>
                                <input type="text" :name="`videos[${idx}][judul]`" x-model="vid.judul" required placeholder="Judul inovasi / penelitian terdahulu" class="w-full rounded-lg border-gray-300 text-sm">
                                <textarea :name="`videos[${idx}][deskripsi]`" x-model="vid.deskripsi" rows="2" placeholder="Deskripsi singkat keterkaitan" class="w-full rounded-lg border-gray-300 text-xs"></textarea>
                                <input type="url" :name="`videos[${idx}][url]`" x-model="vid.url" required placeholder="https://youtube.com/... atau link referensi" class="w-full rounded-lg border-gray-300 text-xs">
                            </div>
                        </template>
                    </div>

                    <div class="flex gap-2 mt-3">
                        <button type="button" @click="addVideo()" x-show="videos.length < 5" class="px-3 py-1.5 text-xs font-semibold border border-amber-500 text-amber-700 rounded-lg hover:bg-amber-50 transition">
                            + Tambah Referensi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= LANGKAH 4: SAMPUL & LAMPIRAN BERKAS ================= -->
        <div x-show="step === 4" x-transition.opacity.duration.300ms style="display: none;">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">4. Foto Sampul & Berkas Pendukung</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Unggah berkas sampul dan dokumen pendukung inovasi daerah.</p>
                </div>
                <span class="text-xs font-bold bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200">Langkah 4 dari 4</span>
            </div>

            <div class="bg-amber-50 border border-amber-200 text-amber-900 p-3.5 rounded-xl text-xs mb-6 leading-relaxed">
                <strong>Catatan Unggahan:</strong> Gambar foto sampul otomatis dikompresi di peramban dan diunggah per potongan 512 KB agar proses pengunggahan kebal terhadap batas server.
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <!-- FOTO SAMPUL INOVASI (WAJIB) -->
                <div x-data="asyncSingleUploader('sampul_file', true)" class="border-2 border-amber-300 rounded-2xl p-5 bg-amber-50/60 sm:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="block text-sm font-bold text-amber-900">Foto Sampul Inovasi <span class="text-red-600">* (Wajib)</span></span>
                        <span class="text-[11px] text-amber-700 font-medium">Format: JPG, JPEG, PNG</span>
                    </div>
                    
                    <div class="border-2 border-dashed border-amber-300 rounded-xl p-5 text-center cursor-pointer hover:border-amber-600 transition bg-white" @click="$refs.fileInput.click()">
                        <p class="text-xs text-gray-600 font-medium">Klik untuk memilih foto sampul</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Otomatis dikompresi maksimal 1600px & kualitas 75%</p>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" class="hidden" accept=".jpg,.jpeg,.png">
                    </div>

                    <div class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="f.id">
                            <div class="text-xs p-3 border border-amber-200 rounded-xl bg-white flex flex-col gap-2 shadow-sm">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2 truncate w-3/4">
                                        <span class="text-base">🖼️</span>
                                        <span class="truncate font-semibold text-gray-800" x-text="f.name"></span>
                                    </div>
                                    <button type="button" @click="removeFile(i)" class="text-red-500 hover:text-red-700 font-bold px-2 py-0.5 rounded text-sm">&times;</button>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${f.progress}%`"></div>
                                </div>
                                <div class="flex justify-between text-[11px] text-gray-500">
                                    <span x-text="f.status === 'compressing' ? '🔄 Mengompresi gambar...' : (f.status === 'uploading' ? `Mengunggah (${f.progress}%)` : (f.status === 'success' ? '✅ Siap disimpan' : '❌ Gagal'))"></span>
                                    <span x-text="f.size"></span>
                                </div>
                                <template x-if="f.status === 'success'">
                                    <input type="hidden" :name="inputName" :value="f.temp_path">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- DOKUMEN ANGGARAN (WAJIB) -->
                <div x-data="asyncSingleUploader('anggaran_file', false)" class="border border-gray-200 rounded-2xl p-4 bg-gray-50">
                    <span class="block text-sm font-semibold text-gray-700 mb-1">Dokumen Anggaran <span class="text-red-500">*</span></span>
                    <span class="text-[11px] text-gray-500 block mb-2">Pastikan berkas berformat PDF (Maks. 10MB)</span>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-amber-500 transition bg-white" @click="$refs.fileInput.click()">
                        <span class="text-xs text-gray-500">Pilih berkas anggaran</span>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    <div class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="f.id">
                            <div class="text-xs p-2.5 border rounded-xl bg-white flex flex-col gap-1.5 shadow-sm">
                                <div class="flex justify-between">
                                    <span class="truncate w-3/4 font-medium text-gray-700" x-text="f.name"></span>
                                    <button type="button" @click="removeFile(i)" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${f.progress}%`"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-gray-500">
                                    <span x-text="f.status === 'uploading' ? `Mengunggah (${f.progress}%)` : (f.status === 'success' ? '✅ Berhasil' : '❌ Gagal')"></span>
                                    <span x-text="f.size"></span>
                                </div>
                                <template x-if="f.status === 'success'">
                                    <input type="hidden" :name="inputName" :value="f.temp_path">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- PROFIL BISNIS -->
                <div x-data="asyncSingleUploader('profil_bisnis_file', false)" class="border border-gray-200 rounded-2xl p-4 bg-gray-50">
                    <span class="block text-sm font-semibold text-gray-700 mb-1">Profil Bisnis (PPT / PDF)</span>
                    <span class="text-[11px] text-gray-500 block mb-2">Opsional (Format: .ppt, .pptx, .pdf)</span>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-amber-500 transition bg-white" @click="$refs.fileInput.click()">
                        <span class="text-xs text-gray-500">Pilih berkas profil bisnis</span>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" class="hidden" accept=".pdf,.ppt,.pptx">
                    </div>
                    <div class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="f.id">
                            <div class="text-xs p-2.5 border rounded-xl bg-white flex flex-col gap-1.5 shadow-sm">
                                <div class="flex justify-between">
                                    <span class="truncate w-3/4 font-medium text-gray-700" x-text="f.name"></span>
                                    <button type="button" @click="removeFile(i)" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${f.progress}%`"></div>
                                </div>
                                <template x-if="f.status === 'success'">
                                    <input type="hidden" :name="inputName" :value="f.temp_path">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- DOKUMEN HAKI -->
                <div x-data="asyncSingleUploader('haki_file', false)" class="border border-gray-200 rounded-2xl p-4 bg-gray-50">
                    <span class="block text-sm font-semibold text-gray-700 mb-1">Dokumen HAKI</span>
                    <span class="text-[11px] text-gray-500 block mb-2">Surat pencatatan ciptaan / sertifikat (PDF/JPG)</span>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-amber-500 transition bg-white" @click="$refs.fileInput.click()">
                        <span class="text-xs text-gray-500">Pilih berkas HAKI</span>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="f.id">
                            <div class="text-xs p-2.5 border rounded-xl bg-white flex flex-col gap-1.5 shadow-sm">
                                <div class="flex justify-between">
                                    <span class="truncate w-3/4 font-medium text-gray-700" x-text="f.name"></span>
                                    <button type="button" @click="removeFile(i)" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${f.progress}%`"></div>
                                </div>
                                <template x-if="f.status === 'success'">
                                    <input type="hidden" :name="inputName" :value="f.temp_path">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- PENGHARGAAN -->
                <div x-data="asyncSingleUploader('penghargaan_file', false)" class="border border-gray-200 rounded-2xl p-4 bg-gray-50">
                    <span class="block text-sm font-semibold text-gray-700 mb-1">Piagam / Dokumen Penghargaan</span>
                    <span class="text-[11px] text-gray-500 block mb-2">Penghargaan yang pernah diterima inovasi</span>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center cursor-pointer hover:border-amber-500 transition bg-white" @click="$refs.fileInput.click()">
                        <span class="text-xs text-gray-500">Pilih piagam penghargaan</span>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                    </div>
                    <div class="mt-3 space-y-2">
                        <template x-for="(f, i) in files" :key="f.id">
                            <div class="text-xs p-2.5 border rounded-xl bg-white flex flex-col gap-1.5 shadow-sm">
                                <div class="flex justify-between">
                                    <span class="truncate w-3/4 font-medium text-gray-700" x-text="f.name"></span>
                                    <button type="button" @click="removeFile(i)" class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" :style="`width: ${f.progress}%`"></div>
                                </div>
                                <template x-if="f.status === 'success'">
                                    <input type="hidden" :name="inputName" :value="f.temp_path">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Navigasi Bawah -->
        <div class="mt-10 flex items-center justify-between border-t pt-5">
            <button type="button" x-show="step > 1" @click="step--" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                &larr; Kembali
            </button>
            <div x-show="step === 1"></div>
            
            <button type="button" x-show="step < 4" @click.prevent="nextStep()" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-md text-sm">
                Lanjut &rarr;
            </button>
            
            <button type="submit" x-show="step === 4" style="display: none;" class="px-7 py-2.5 bg-amber-500 text-gray-900 rounded-xl font-bold hover:bg-amber-400 transition shadow-lg shadow-amber-500/30 text-sm">
                Simpan Seluruh Inovasi
            </button>
        </div>
    </form>
</section>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.ImaCreateUploader = {
    CHUNK_SIZE: 512 * 1024, // 512 KB

    async compressImage(file) {
        if (!file.type.startsWith('image/')) return file;
        try {
            const bitmap = await createImageBitmap(file);
            const MAX = 1600;
            let w = bitmap.width;
            let h = bitmap.height;

            if (w > h) {
                if (w > MAX) { h = Math.round((h * MAX) / w); w = MAX; }
            } else {
                if (h > MAX) { w = Math.round((w * MAX) / h); h = MAX; }
            }

            const canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(bitmap, 0, 0, w, h);

            return await new Promise((resolve) => {
                canvas.toBlob((blob) => {
                    if (!blob) return resolve(file);
                    const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                    resolve(new File([blob], cleanName, { type: 'image/jpeg', lastModified: Date.now() }));
                }, 'image/jpeg', 0.75);
            });
        } catch (e) {
            console.warn('Fallback berkas asli:', e);
            return file;
        }
    },

    async uploadInChunks(file, onProgress, uploadUrl, csrfToken) {
        const fileId = 'create_' + Date.now() + '_' + Math.random().toString(36).substring(2, 8);
        const totalChunks = Math.ceil(file.size / this.CHUNK_SIZE);

        for (let idx = 0; idx < totalChunks; idx++) {
            const start = idx * this.CHUNK_SIZE;
            const end = Math.min(start + this.CHUNK_SIZE, file.size);
            const chunk = file.slice(start, end);

            const fd = new FormData();
            fd.append('file', chunk);
            fd.append('file_id', fileId);
            fd.append('chunk_index', idx);
            fd.append('total_chunks', totalChunks);
            fd.append('original_name', file.name);

            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd
            });

            if (!res.ok) throw new Error(`Unggahan terputus pada bagian ${idx + 1}/${totalChunks}`);
            const data = await res.json();
            const pct = Math.round(((idx + 1) / totalChunks) * 100);
            if (typeof onProgress === 'function') onProgress(pct, data);
            if (data.completed) return data;
        }
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.data('imaWizard', () => ({
        step: 1,
        formData: {
            kategori: '',
            operator_nama: '',
            operator_wa: '',
            judul: '',
            opd_unit: '',
            tahap_inovasi: '',
            koordinat: '',
            rancang_bangun: ''
        },
        videos: [
            { judul: '', deskripsi: '', url: '' },
            { judul: '', deskripsi: '', url: '' },
            { judul: '', deskripsi: '', url: '' }
        ],

        addVideo() {
            if (this.videos.length < 5) {
                this.videos.push({ judul: '', deskripsi: '', url: '' });
            }
        },

        removeVideo(idx) {
            if (this.videos.length > 3) {
                this.videos.splice(idx, 1);
            }
        },

        nextStep() {
            let valid = true;
            let msg = '';

            // Validasi Langkah 1
            if (this.step === 1) {
                if (!this.formData.kategori) { valid = false; msg = 'Silakan pilih Kategori PRO IMA atau PEMULA IMA.'; }
                else if (!this.formData.operator_nama.trim()) { valid = false; msg = 'Nama Operator (PIC) wajib diisi.'; }
                else if (!this.formData.operator_wa.trim()) { valid = false; msg = 'Nomor WhatsApp Operator wajib diisi.'; }
            }
            
            // Validasi Langkah 2
            else if (this.step === 2) {
                if (!this.formData.judul.trim()) { valid = false; msg = 'Judul Inovasi wajib diisi.'; }
                else if (!this.formData.opd_unit.trim()) { valid = false; msg = 'Nama Perangkat Daerah (OPD) wajib diisi.'; }
                else if (!this.formData.tahap_inovasi) { valid = false; msg = 'Tahapan Inovasi wajib dipilih.'; }
                else if (!this.formData.koordinat.trim()) { valid = false; msg = 'Koordinat lokasi penerapan wajib diisi.'; }
                else if (this.formData.koordinat.length > 300) { valid = false; msg = 'Koordinat tidak boleh lebih dari 300 karakter.'; }
            }

            // Validasi Langkah 3
            else if (this.step === 3) {
                if (!this.formData.rancang_bangun.trim() || this.formData.rancang_bangun.length < 300) {
                    valid = false; msg = 'Rancang bangun inovasi wajib diisi minimal 300 karakter.';
                } else {
                    const filledVideos = this.videos.filter(v => v.judul.trim() !== '' && v.url.trim() !== '');
                    if (filledVideos.length < 3) {
                        valid = false; msg = 'Minimal 3 referensi penelitian / inovasi terdahulu wajib diisi lengkap (Judul & URL).';
                    }
                }
            }

            if (!valid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Lengkapi Isian',
                    text: msg,
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            this.step++;
        },

        submitForm(e) {
            const sampulInput = document.querySelector('input[name="sampul_file"]');
            if (!sampulInput || !sampulInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Foto Sampul Wajib Diunggah',
                    text: 'Silakan unggah Gambar Sampul Inovasi terlebih dahulu sebelum menyimpan profil.',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            const anggaranInput = document.querySelector('input[name="anggaran_file"]');
            if (!anggaranInput || !anggaranInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dokumen Anggaran Wajib Diunggah',
                    text: 'Silakan unggah Dokumen Anggaran inovasi terlebih dahulu.',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            Swal.fire({
                title: 'Menyimpan Pendaftaran...',
                text: 'Memproses metadata dan menyalin berkas lampiran.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            e.target.submit();
        }
    }));

    Alpine.data('asyncSingleUploader', (inputName, isImageOnly) => ({
        files: [],
        inputName: inputName,
        isImageOnly: isImageOnly,
        uploadUrl: "{{ route('sigap-ima.upload-chunk') }}",
        csrfToken: '',

        init() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            this.csrfToken = meta ? meta.content : '';
        },

        async handleFileSelect(e) {
            const raw = e.target.files[0];
            if (!raw) return;

            this.files = [];
            const fileId = 'f_' + Date.now();
            this.files.push({
                id: fileId,
                name: raw.name,
                size: (raw.size / 1024).toFixed(1) + ' KB',
                progress: 0,
                status: 'compressing',
                temp_path: ''
            });

            e.target.value = '';
            this.processFile(fileId, raw);
        },

        async processFile(fileId, raw) {
            try {
                let ready = raw;
                if (raw.type.startsWith('image/')) {
                    ready = await window.ImaCreateUploader.compressImage(raw);
                }

                this.updateItem(fileId, {
                    status: 'uploading',
                    size: (ready.size / 1024).toFixed(1) + ' KB'
                });

                const res = await window.ImaCreateUploader.uploadInChunks(
                    ready,
                    (pct) => this.updateItem(fileId, { progress: pct }),
                    this.uploadUrl,
                    this.csrfToken
                );

                if (res && res.completed) {
                    this.updateItem(fileId, {
                        status: 'success',
                        progress: 100,
                        temp_path: res.temp_path
                    });
                } else {
                    this.updateItem(fileId, { status: 'error' });
                }
            } catch (err) {
                console.error(err);
                this.updateItem(fileId, { status: 'error' });
            }
        },

        updateItem(id, props) {
            const idx = this.files.findIndex(x => x.id === id);
            if (idx !== -1) {
                this.files[idx] = { ...this.files[idx], ...props };
                this.files = [...this.files];
            }
        },

        removeFile(i) {
            this.files.splice(i, 1);
            this.files = [...this.files];
        }
    }));
});
</script>
@endpush