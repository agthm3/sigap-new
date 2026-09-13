@extends('layouts.app')

@push('head')
<!-- Include Quill Stylesheet -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<style>
    .ql-container { font-family: inherit; font-size: 14px; min-height: 150px; border-bottom-left-radius: 0.75rem; border-bottom-right-radius: 0.75rem; background-color: white; }
    .ql-toolbar { border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem; background-color: #f9fafb; }
    .ql-editor { min-height: 150px; }
</style>
@endpush

@section('content')
<section class="max-w-5xl mx-auto px-4 py-8" x-data="imaWizard()">
    <!-- Header -->
    <div class="mb-8 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-2">
            Innovative Mayor Award
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900">Pendaftaran Inovasi SIGAP IMA</h1>
        <p class="text-gray-500 mt-2 text-sm">Lengkapi seluruh metadata inovasi, kontak PIC, uraian deskripsi, dan lampiran berkas pendukung.</p>
    </div>

    <!-- Stepper Indicator (5 Langkah) -->
    <div class="flex items-center justify-center mb-8 overflow-x-auto pb-4">
        <div class="flex items-center gap-2 min-w-max">
            <div :class="step >= 1 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">1</div>
            <span class="text-[11px] font-semibold" :class="step >= 1 ? 'text-amber-600' : 'text-gray-400'">Kategori & PIC</span>
            <div :class="step >= 2 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-6 sm:w-10 transition"></div>
            
            <div :class="step >= 2 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">2</div>
            <span class="text-[11px] font-semibold" :class="step >= 2 ? 'text-amber-600' : 'text-gray-400'">Metadata</span>
            <div :class="step >= 3 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-6 sm:w-10 transition"></div>
            
            <div :class="step >= 3 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">3</div>
            <span class="text-[11px] font-semibold" :class="step >= 3 ? 'text-amber-600' : 'text-gray-400'">Deskripsi</span>
            <div :class="step >= 4 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-6 sm:w-10 transition"></div>
            
            <div :class="step >= 4 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">4</div>
            <span class="text-[11px] font-semibold" :class="step >= 4 ? 'text-amber-600' : 'text-gray-400'">SDGs</span>
            <div :class="step >= 5 ? 'bg-amber-500' : 'bg-gray-200'" class="h-1 w-6 sm:w-10 transition"></div>

            <div :class="step >= 5 ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition">5</div>
            <span class="text-[11px] font-semibold" :class="step >= 5 ? 'text-amber-600' : 'text-gray-400'">Upload Berkas</span>
        </div>
    </div>

    <form action="{{ route('sigap-ima.store') }}" method="POST" id="formImaCreate" novalidate class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6 sm:p-10 relative min-h-[500px]" @submit.prevent="submitForm($event)">
        @csrf
        
        <!-- HIDDEN INPUTS UNTUK EDITOR TEKS -->
        <input type="hidden" name="rancang_bangun" id="input_rancang_bangun">
        <input type="hidden" name="tujuan" id="input_tujuan">
        <input type="hidden" name="manfaat" id="input_manfaat">
        <input type="hidden" name="hasil_inovasi" id="input_hasil">
        <input type="hidden" name="sdgs_keterkaitan" id="input_sdgs">

        <!-- ================= LANGKAH 1: KATEGORI & KONTAK OPERATOR ================= -->
        <div x-show="step === 1" x-transition.opacity.duration.300ms>
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">1. Kategori & Kontak Operator</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Tentukan kategori pendaftaran IMA dan identitas operator (PIC) aktif.</p>
                </div>
            </div>
            
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

            <div class="bg-gray-50/70 p-6 rounded-2xl border border-gray-200">
                <div class="mb-4">
                    <h4 class="text-sm font-bold text-gray-800">Kontak Person / Operator Inovasi (PIC) <span class="text-red-500">*</span></h4>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Nama Operator <span class="text-red-500">*</span></span>
                        <input type="text" name="operator_nama" x-model="formData.operator_nama" placeholder="Nama lengkap operator" class="mt-1 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Jabatan Operator</span>
                        <input type="text" name="operator_jabatan" placeholder="Contoh: Pranata Komputer / Staf Perencanaan" class="mt-1 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Nomor WhatsApp Aktif <span class="text-red-500">*</span></span>
                        <input type="text" name="operator_wa" x-model="formData.operator_wa" placeholder="Contoh: 081234567890" class="mt-1 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
                    </label>
                    <label class="block">
                        <span class="text-sm font-semibold text-gray-700">Email Operator</span>
                        <input type="email" name="operator_email" placeholder="operator@makassarkota.go.id" class="mt-1 w-full rounded-xl border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-sm">
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

        <!-- ================= LANGKAH 3: DESKRIPSI LENGKAP (QUILL EDITOR) ================= -->
        <div x-show="step === 3" x-transition.opacity.duration.300ms style="display: none;">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">3. Uraian Deskripsi Inovasi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Uraikan rancang bangun (min. 300 kata), tujuan, manfaat, dan hasil secara rapi.</p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Rancang Bangun -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">
                        Rancang Bangun Inovasi (Minimal 300 KATA) <span class="text-red-500">*</span>
                    </label>
                    <div id="editor-rancang" class="rounded-xl"></div>
                    <div class="flex justify-between items-center text-[11px] mt-1.5 px-1">
                        <span class="text-gray-500">Jelaskan latar belakang, desain, arsitektur, dan cara kerja.</span>
                        <span class="font-extrabold px-2 py-0.5 rounded" :class="rancangWordCount < 300 ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700'" x-text="`${rancangWordCount} / 300 Kata`"></span>
                    </div>
                </div>

                <!-- Tujuan -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">
                        Tujuan Inovasi Daerah <span class="text-red-500">*</span>
                    </label>
                    <div id="editor-tujuan" class="rounded-xl"></div>
                </div>

                <!-- Manfaat -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">
                        Manfaat yang Diperoleh <span class="text-red-500">*</span>
                    </label>
                    <div id="editor-manfaat" class="rounded-xl"></div>
                </div>

                <!-- Hasil -->
                <div>
                    <label class="block text-sm font-bold text-gray-800 mb-1">
                        Hasil Inovasi <span class="text-red-500">*</span>
                    </label>
                    <div id="editor-hasil" class="rounded-xl"></div>
                </div>
            </div>
        </div>

        <!-- ================= LANGKAH 4: KETERKAITAN SDGs (OPSIONAL DENGAN IKON & TEMA WARNA) ================= -->
        <div x-show="step === 4" x-transition.opacity.duration.300ms style="display: none;">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">4. Keterkaitan SDGs (Opsional)</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih pilar Sustainable Development Goals yang sesuai dengan dampak inovasi Anda.</p>
                </div>
                <span class="text-xs font-bold bg-amber-50 text-amber-700 px-3 py-1 rounded-full border border-amber-200">Langkah 4 dari 5</span>
            </div>

            <div class="bg-gray-50/60 border border-gray-200 rounded-3xl p-6 sm:p-8">
                <div class="mb-4">
                    <h3 class="text-sm font-bold text-gray-900">A. Pilih Pilar SDGs yang Relevan</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Klik satu atau beberapa kartu pilar SDGs di bawah ini untuk menghubungkannya.</p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
                    @if(isset($dropdowns['sdgs']) && count($dropdowns['sdgs']) > 0)
                        @foreach($dropdowns['sdgs'] as $sdg)
                            @php
                                $color = $sdg->warna ?: '#E5243B';
                            @endphp
                            <div @click="toggleSdg('{{ $sdg->label }}')"
                                 :style="selectedSdgs.includes('{{ $sdg->label }}') 
                                    ? 'border-color: {{ $color }}; background-color: {{ $color }}15; box-shadow: 0 4px 14px {{ $color }}25;' 
                                    : 'border-top: 4px solid {{ $color }};'"
                                 class="border rounded-2xl p-4 cursor-pointer transition-all duration-200 bg-white hover:-translate-y-0.5 relative group flex flex-col justify-between">
                                
                                <div>
                                    <!-- Ikon Gambar / Badge Nomor -->
                                    <div class="flex items-center justify-between mb-3">
                                        @if(!empty($sdg->icon_path))
                                            <img src="{{ asset('storage/' . $sdg->icon_path) }}" alt="{{ $sdg->label }}" class="w-12 h-12 object-contain rounded-xl p-1 bg-white border border-gray-100 shadow-2xs">
                                        @else
                                            <div class="w-11 h-11 rounded-xl flex items-center justify-center font-black text-white text-sm shadow-xs" style="background-color: {{ $color }};">
                                                {{ $sdg->kode ?? 'SDG' }}
                                            </div>
                                        @endif

                                        <!-- Centang Aktif -->
                                        <div x-show="selectedSdgs.includes('{{ $sdg->label }}')" 
                                             class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs font-black shadow-xs"
                                             style="background-color: {{ $color }};">
                                            ✓
                                        </div>
                                    </div>

                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded text-white inline-block mb-1" style="background-color: {{ $color }};">
                                        {{ $sdg->kode ?? 'Pilar' }}
                                    </span>
                                    <h4 class="font-bold text-gray-900 text-xs sm:text-sm leading-tight mb-1.5">{{ $sdg->label }}</h4>
                                </div>

                                @if($sdg->deskripsi)
                                    <p class="text-[10px] text-gray-500 leading-relaxed mt-2 pt-2 border-t border-gray-100">
                                        {{ $sdg->deskripsi }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-gray-300">
                            <span class="text-3xl block mb-2 opacity-50">🌱</span>
                            <p class="text-sm font-semibold text-gray-600">Data Master SDGs belum diatur oleh admin.</p>
                            <p class="text-xs text-gray-400 mt-1">Admin dapat mengunggah gambar ikon dan warna tema melalui menu Pengaturan IMA.</p>
                        </div>
                    @endif
                </div>

                <!-- Input Hidden Array untuk dikirim ke Backend -->
                <template x-for="s in selectedSdgs">
                    <input type="hidden" name="sdgs_pilihan[]" :value="s">
                </template>

                <!-- Box Quill Editor yang Terbuka Otomatis Jika Ada SDGs Dipilih -->
                <div x-show="selectedSdgs.length > 0" x-transition.duration.300ms class="mt-6 pt-6 border-t border-gray-200">
                    <div class="mb-3">
                        <label class="block text-sm font-bold text-gray-900">
                            B. Uraian Keterkaitan Inovasi dengan SDGs Terpilih
                        </label>
                        <p class="text-xs text-gray-500">Jelaskan bagaimana inovasi ini secara konkret berkontribusi pada pencapaian target SDGs yang telah dipilih.</p>
                    </div>
                    <div id="editor-sdgs" class="rounded-xl bg-white border border-gray-300 shadow-2xs"></div>
                </div>
            </div>
        </div>

        <!-- ================= LANGKAH 5: SAMPUL & LAMPIRAN BERKAS ================= -->
        <div x-show="step === 5" x-transition.opacity.duration.300ms style="display: none;">
            <div class="flex items-center justify-between border-b pb-3 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">5. Foto Sampul & Berkas Pendukung</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Unggah berkas sampul dan dokumen pendukung inovasi daerah.</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 text-amber-900 p-3.5 rounded-xl text-xs mb-6 leading-relaxed">
                <strong>Catatan Unggahan:</strong> Berkas akan diproses dan diunggah per potongan 512 KB ke server penyimpanan sementara agar aman dari batas memori server.
            </div>

            <div class="grid sm:grid-cols-2 gap-6">
                <!-- FOTO SAMPUL INOVASI (WAJIB) -->
                <div x-data="asyncSingleUploader('sampul_file', true)" class="border-2 border-amber-300 rounded-2xl p-5 bg-amber-50/60 sm:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="block text-sm font-bold text-amber-900">Foto Sampul Inovasi <span class="text-red-600">* (Wajib)</span></span>
                        <span class="text-[11px] text-amber-700 font-medium">Format: JPG, JPEG, PNG, WEBP</span>
                    </div>
                    
                    <div class="border-2 border-dashed border-amber-300 rounded-xl p-5 text-center cursor-pointer hover:border-amber-600 transition bg-white" @click="$refs.fileInput.click()">
                        <p class="text-xs text-gray-600 font-medium">Klik untuk memilih foto sampul</p>
                        <p class="text-[11px] text-gray-400 mt-0.5">Otomatis disesuaikan maksimal 1600px & kompresi 80%</p>
                        <input type="file" x-ref="fileInput" @change="handleFileSelect($event)" class="hidden" accept="image/jpeg,image/png,image/webp,image/jpg">
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
                                    <span x-text="f.status === 'compressing' ? '🔄 Menyiapkan gambar...' : (f.status === 'uploading' ? `Mengunggah (${f.progress}%)` : (f.status === 'success' ? '✅ Siap disimpan' : '❌ Gagal'))"></span>
                                    <span x-text="f.size"></span>
                                </div>
                                <template x-if="f.status === 'success'">
                                    <input type="hidden" :name="inputName" :value="f.temp_path">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- DOKUMEN ANGGARAN (TIDAK WAJIB LAGI) -->
                <div x-data="asyncSingleUploader('anggaran_file', false)" class="border border-gray-200 rounded-2xl p-4 bg-gray-50">
                    <span class="block text-sm font-semibold text-gray-700 mb-1">Dokumen Anggaran</span>
                    <span class="text-[11px] text-gray-500 block mb-2">Opsional (PDF, JPG, PNG - Maks. 15MB)</span>
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
            
            <button type="button" x-show="step < 5" @click.prevent="nextStep()" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-md text-sm">
                Lanjut &rarr;
            </button>
            
            <button type="submit" x-show="step === 5" style="display: none;" class="px-7 py-2.5 bg-amber-500 text-gray-900 rounded-xl font-bold hover:bg-amber-400 transition shadow-lg shadow-amber-500/30 text-sm">
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
    // Konfigurasi Toolbar Quill Editor
    const toolbarOptions = [
        ['bold', 'italic', 'underline'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'indent': '-1'}, { 'indent': '+1' }],
        ['clean']
    ];

    const quillRancang = new Quill('#editor-rancang', { theme: 'snow', modules: { toolbar: toolbarOptions }});
    const quillTujuan = new Quill('#editor-tujuan', { theme: 'snow', modules: { toolbar: toolbarOptions }});
    const quillManfaat = new Quill('#editor-manfaat', { theme: 'snow', modules: { toolbar: toolbarOptions }});
    const quillHasil = new Quill('#editor-hasil', { theme: 'snow', modules: { toolbar: toolbarOptions }});
    const quillSdgs = new Quill('#editor-sdgs', { theme: 'snow', modules: { toolbar: toolbarOptions }});

    // ENGINE KOMPRESI & CHUNK ROBUST (ANTI-CRASH)
    window.ImaCreateUploader = {
        CHUNK_SIZE: 512 * 1024, // 512 KB per potongan

        // Kompresi Gambar menggunakan HTMLImageElement (Stabil di Safari, Chrome, Firefox)
        async compressImage(file) {
            if (!file || !file.type.startsWith('image/')) return file;
            
            // Format SVG atau GIF dilewati langsung
            if (file.type === 'image/svg+xml' || file.type === 'image/gif') return file;

            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onerror = () => resolve(file); // Fallback jika gagal baca
                reader.onload = (e) => {
                    const img = new Image();
                    img.onerror = () => resolve(file); // Fallback ke file asli
                    img.onload = () => {
                        try {
                            const MAX_WIDTH = 1600;
                            const MAX_HEIGHT = 1600;
                            let width = img.width;
                            let height = img.height;

                            if (width > height) {
                                if (width > MAX_WIDTH) {
                                    height = Math.round((height * MAX_WIDTH) / width);
                                    width = MAX_WIDTH;
                                }
                            } else {
                                if (height > MAX_HEIGHT) {
                                    width = Math.round((width * MAX_HEIGHT) / height);
                                    height = MAX_HEIGHT;
                                }
                            }

                            const canvas = document.createElement('canvas');
                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            // Beri latar belakang putih agar transparan tidak menjadi hitam pekat
                            ctx.fillStyle = '#FFFFFF';
                            ctx.fillRect(0, 0, width, height);
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (!blob) {
                                    resolve(file);
                                    return;
                                }
                                const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                                const compressedFile = new File([blob], cleanName, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                });
                                resolve(compressedFile);
                            }, 'image/jpeg', 0.80);
                        } catch (err) {
                            console.warn('Kompresi canvas gagal, menggunakan file asli:', err);
                            resolve(file);
                        }
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        },

        // Upload Chunk dengan pengecekan token CSRF & response JSON yang ketat
        async uploadInChunks(file, onProgress, uploadUrl, csrfToken) {
            const fileId = 'create_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7);
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
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: fd
                });

                if (!res.ok) {
                    const textErr = await res.text();
                    console.error('Server error upload:', textErr);
                    throw new Error(`Gagal pada bagian ${idx + 1}/${totalChunks}`);
                }

                const data = await res.json();
                const pct = Math.round(((idx + 1) / totalChunks) * 100);
                if (typeof onProgress === 'function') onProgress(pct, data);

                if (data.completed) {
                    return data;
                }
            }
        }
    };

    document.addEventListener('alpine:init', () => {
        Alpine.data('imaWizard', () => ({
            step: 1,
            rancangWordCount: 0,
            selectedSdgs: [],
            formData: {
                kategori: '', 
                operator_nama: '', 
                operator_wa: '',
                judul: '', 
                opd_unit: '', 
                tahap_inovasi: '', 
                koordinat: ''
            },

            init() {
                // Perhitungan kata secara akurat di Quill
                quillRancang.on('text-change', () => {
                    const text = quillRancang.getText().trim();
                    this.rancangWordCount = text.length > 0 ? text.split(/\s+/).filter(Boolean).length : 0;
                });
            },

            toggleSdg(label) {
                if (this.selectedSdgs.includes(label)) {
                    this.selectedSdgs = this.selectedSdgs.filter(item => item !== label);
                } else {
                    this.selectedSdgs.push(label);
                }
            },

            nextStep() {
                let valid = true;
                let msg = '';

                // VALIDASI LANGKAH 1
                if (this.step === 1) {
                    if (!this.formData.kategori) { valid = false; msg = 'Kategori PRO IMA atau PEMULA IMA wajib dipilih.'; }
                    else if (!this.formData.operator_nama.trim()) { valid = false; msg = 'Nama Operator (PIC) wajib diisi.'; }
                    else if (!this.formData.operator_wa.trim()) { valid = false; msg = 'Nomor WhatsApp Operator wajib diisi.'; }
                } 
                // VALIDASI LANGKAH 2
                else if (this.step === 2) {
                    if (!this.formData.judul.trim()) { valid = false; msg = 'Judul Inovasi wajib diisi.'; }
                    else if (!this.formData.opd_unit.trim()) { valid = false; msg = 'Perangkat Daerah (OPD) wajib diisi.'; }
                    else if (!this.formData.tahap_inovasi) { valid = false; msg = 'Tahapan Inovasi wajib dipilih.'; }
                    else if (!this.formData.koordinat.trim()) { valid = false; msg = 'Koordinat Lokasi penerapan wajib diisi.'; }
                } 
                // VALIDASI LANGKAH 3 (Minimal 300 Kata)
                else if (this.step === 3) {
                    if (this.rancangWordCount < 300) {
                        valid = false; msg = `Rancang Bangun Inovasi wajib minimal 300 KATA. Saat ini baru ${this.rancangWordCount} kata.`;
                    } else if (quillTujuan.getText().trim().length === 0) {
                        valid = false; msg = 'Tujuan Inovasi Daerah wajib diisi.';
                    } else if (quillManfaat.getText().trim().length === 0) {
                        valid = false; msg = 'Manfaat yang Diperoleh wajib diisi.';
                    } else if (quillHasil.getText().trim().length === 0) {
                        valid = false; msg = 'Hasil Inovasi wajib diisi.';
                    }
                }
                // LANGKAH 4 (SDGs) bersifat Opsional, jadi bebas lanjut ke Langkah 5

                if (!valid) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Periksa Kembali Isian', 
                        text: msg, 
                        confirmButtonColor: '#f59e0b' 
                    });
                    return;
                }
                this.step++;
            },

            submitForm(e) {
                // 1. SINKRONISASI ISI EDITOR KE HIDDEN INPUT
                document.getElementById('input_rancang_bangun').value = quillRancang.root.innerHTML;
                document.getElementById('input_tujuan').value = quillTujuan.root.innerHTML;
                document.getElementById('input_manfaat').value = quillManfaat.root.innerHTML;
                document.getElementById('input_hasil').value = quillHasil.root.innerHTML;
                document.getElementById('input_sdgs').value = quillSdgs.root.innerHTML;

                // 2. VALIDASI FOTO SAMPUL (WAJIB)
                const sampulVal = document.querySelector('input[name="sampul_file"]')?.value;
                if (!sampulVal) {
                    Swal.fire({ 
                        icon: 'warning', 
                        title: 'Foto Sampul Wajib Diunggah', 
                        text: 'Silakan pilih foto sampul dan tunggu hingga proses unggah selesai.', 
                        confirmButtonColor: '#f59e0b' 
                    });
                    return;
                }

                Swal.fire({ 
                    title: 'Menyimpan Pendaftaran...', 
                    text: 'Memproses seluruh data inovasi dan memindahkan berkas.',
                    allowOutsideClick: false, 
                    didOpen: () => Swal.showLoading() 
                });

                document.getElementById('formImaCreate').submit();
            }
        }));

        // KOMPONEN UPLOADER FILE SINGLE ASINKRON
        Alpine.data('asyncSingleUploader', (inputName, isImageOnly = false) => ({
            files: [], 
            inputName: inputName,
            isImageOnly: isImageOnly,
            uploadUrl: "{{ route('sigap-ima.upload-chunk') }}",
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

            async handleFileSelect(e) {
                const raw = e.target.files[0];
                if (!raw) return;

                const fileId = 'f_' + Date.now();
                this.files = [{
                    id: fileId,
                    name: raw.name,
                    size: (raw.size / 1024).toFixed(1) + ' KB',
                    progress: 0,
                    status: 'compressing',
                    temp_path: ''
                }];

                e.target.value = ''; // Reset input agar bisa re-select file yang sama

                try {
                    let ready = raw;
                    if (this.isImageOnly) {
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
                            temp_path: res.temp_path,
                            progress: 100
                        });
                    } else {
                        this.updateItem(fileId, { status: 'error' });
                    }
                } catch (err) {
                    console.error('Upload gagal:', err);
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