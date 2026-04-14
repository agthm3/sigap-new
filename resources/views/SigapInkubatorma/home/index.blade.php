@extends('layouts.page') {{-- Mengambil Header & Footer SIGAP --}}

@section('title', 'SIGAP INKUBATORMA — BRIDA Kota Makassar')

@push('styles')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

@php
    use App\Models\Inkubatorma;

    $selectedLayanan = old('layanan', $formData['layanan'] ?? []);
    if (!is_array($selectedLayanan)) {
        $selectedLayanan = [$selectedLayanan];
    }

    $selectedLabel = '— Pilih maksimal 2 layanan —';
    if (!empty($selectedLayanan)) {
        $labels = collect($selectedLayanan)
            ->map(fn($id) => $layananOptions[$id] ?? $id)
            ->toArray();

        $selectedLabel = implode(', ', $labels);
    }

    $statusSelesai = defined(\App\Models\Inkubatorma::class . '::STATUS_SELESAI')
        ? \App\Models\Inkubatorma::STATUS_SELESAI
        : 'Selesai';

    $statusTerjadwal = defined(\App\Models\Inkubatorma::class . '::STATUS_TERJADWAL')
        ? \App\Models\Inkubatorma::STATUS_TERJADWAL
        : 'Terjadwal';

    $statusDijadwalkanUlang = defined(\App\Models\Inkubatorma::class . '::STATUS_DIJADWALKAN_ULANG')
        ? \App\Models\Inkubatorma::STATUS_DIJADWALKAN_ULANG
        : 'Dijadwalkan Ulang';

    $statusSesiKonsultasi = defined(\App\Models\Inkubatorma::class . '::STATUS_SESI_KONSULTASI')
        ? \App\Models\Inkubatorma::STATUS_SESI_KONSULTASI
        : 'Sesi Konsultasi';
@endphp


{{-- HERO --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="max-w-3xl mx-auto text-center">
            <div class="mb-4">
                <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/20 block text-maroon-200 text-xs font-bold uppercase tracking-[0.2em] mb-2">
                    Inspirasi Kreatif untuk Berbagi Treatment Inovasi Riset Makassar
                </span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                SIGAP <span class="text-white/90">INKUBATORMA</span>
            </h1>
            <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
                Ajukan <b>jadwal pertemuan</b> dengan BRIDA Kota Makassar untuk diskusi inovasi, riset, atau pemecahan masalah.
            </p>
            <div class="mt-7 flex justify-center gap-3">
                <a href="#form" class="px-6 py-3 rounded-xl bg-white text-maroon font-semibold text-base hover:bg-white/90 transition">
                    Isi Form Sekarang
                </a>
                <a href="{{ route('sigap-inkubatorma.dashboard') }}"
                    class="px-6 py-3 rounded-xl bg-maroon-700/40 text-white border border-white/30 
                            font-semibold text-base hover:bg-maroon-700/60 transition">
                    Lihat Dashboard
                </a>
            </div>
        </div>
    </div>
</section>

<!-- FITUR -->
<section id="fitur" class="py-14 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid lg:grid-cols-2 gap-12 items-stretch">
            {{-- Fitur Utama --}}
            <div class="flex flex-col justify-center">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon tracking-tight">Fitur Utama</h3>
                <p class="mt-3 text-gray-600">Ekosistem pendampingan riset yang terukur untuk mencetak inovator unggul di Kota Makassar.</p>

                <div class="mt-8 space-y-6">
                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9l-4 4v-4H3a2 2 0 01-2-2V10a2 2 0 012-2h2M9 21V5a2 2 0 012-2h2a2 2 0 012 2v16"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Konsultasi Eksklusif</h4>
                            <p class="text-sm text-gray-600">Ajukan pertemuan tatap muka atau daring dengan tim ahli BRIDA untuk bedah ide inovasi Anda.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Penjadwalan Fleksibel</h4>
                            <p class="text-sm text-gray-600">Pilih waktu dan personil pendamping yang sesuai dengan ketersediaan jadwal Anda secara real-time.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="shrink-0 w-12 h-12 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">Log Aktivitas</h4>
                            <p class="text-sm text-gray-600">Setiap riwayat konsultasi dan akses data privasi dicatat secara ketat demi keamanan data bersama.</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Video --}}
            <div class="relative">
                <div class="aspect-w-16 aspect-h-9 rounded-2xl overflow-hidden shadow-2xl border-4 border-white">
                    <iframe 
                            class="w-full h-[300px] sm:h-[380px]"
                            src="https://www.youtube.com/embed/DOUC9yEuskg" 
                            title="Penjelasan SIGAP Inovasi" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
                <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-maroon/10 rounded-full -z-10"></div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 bg-white" id="form">
    <div class="max-w-7xl mx-auto px-4 grid lg:grid-cols-5 gap-10">
        <!-- FORM -->
        <div class="lg:col-span-2">
            <div class="border border-gray-200 rounded-2xl p-7 shadow-sm">
                <h2 class="text-2xl font-extrabold text-maroon">Form Pengajuan Jadwal</h2>

                <div class="mt-4 rounded-xl bg-maroon/5 border border-maroon/15 p-4">
                    <p class="text-base text-gray-800">
                        <b>Butuh bantuan?</b>
                    </p>
                    <a href="https://wa.me/6285173231604?text=Halo%20Admin%20BRIDA,%20saya%20ingin%20bertanya%20terkait%20layanan%20SIGAP%20Inkubatorma."
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-3 py-1 rounded bg-green-600 text-white text-sm font-semibold hover:opacity-90">
                        Hubungi Admin BRIDA
                    </a>
                </div>

                <form action="{{ route('sigap-inkubatorma.store') }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="mt-6 space-y-5">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div>
                        <label class="text-base font-semibold">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" name="nama_pengaju" required
                            value="{{ old('nama_pengaju', $formData['nama_pengaju'] ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30"
                            placeholder="Contoh: Ahmad Fauzi">
                    </div>

                    {{-- Nomor Whatsapp --}}
                    <div>
                        <label class="text-base font-semibold">Nomor WhatsApp / HP <span class="text-red-600">*</span></label>
                        <input type="tel" name="no_hp" required inputmode="tel"
                            value="{{ old('no_hp', $formData['no_hp'] ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30"
                            placeholder="Contoh: 08xxxxxxxxxx">
                        <p class="mt-1 text-sm text-gray-600">Nomor ini dipakai untuk konfirmasi jadwal.</p>
                    </div>

                    {{-- Nama OPD/Unit --}}
                    <div>
                        <label class="text-base font-semibold">Instansi / OPD / Komunitas <span class="text-red-600">*</span></label>
                        <p class="mt-1 text-sm text-gray-500">Nama dinas, perusahaan, komunitas, atau instansi asal Anda</p>
                        <input type="text" name="nama_opd" required
                            value="{{ old('nama_opd', $formData['nama_opd'] ?? '') }}"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30"
                            placeholder="Contoh: Dinas … / Komunitas … / Perusahaan …">
                    </div>

                    {{-- Layanan --}}
                    <div class="relative" id="layananDropdownWrap">
                        <label class="text-base font-semibold">
                            Pilih Layanan <span class="text-red-600">*</span>
                        </label>

                       <select name="layanan[]" id="layananSelectNative" multiple required
                            style="position:absolute; left:0; top:40px; width:100%; height:1px; opacity:0; pointer-events:none; overflow:hidden;">
                            @foreach (($layananOptions ?? []) as $id => $nama)
                                <option value="{{ $id }}" @selected(in_array($id, $selectedLayanan ?? []))>{{ $nama }}</option>
                            @endforeach
                        </select>

                        <button type="button"
                                id="layananBtn"
                                class="mt-2 w-full max-w-full truncate rounded-xl border border-gray-300 bg-white px-4 py-3 text-left text-base
                                       focus:outline-none focus:ring-2 focus:ring-maroon/30 focus:border-maroon flex items-center justify-between gap-3">
                            <span id="layananBtnLabel" class="{{ !empty($selectedLayanan) ? 'text-gray-900' : 'text-gray-500' }}">
                                {{ $selectedLabel }}
                            </span>

                            <svg class="w-5 h-5 text-gray-500 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <div id="selectedLayananContainer" class="flex flex-wrap gap-2 mt-2"></div>

                        <div id="layananPanel"
                             class="hidden absolute z-30 mt-2 w-full rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden">
                            <div class="max-h-72 overflow-y-auto py-2">
                                <div class="px-3 py-2 text-xs font-semibold text-gray-500">Layanan</div>

                                @foreach (($layananOptions ?? []) as $id => $nama)
                                    @continue($id === 'lainnya')
                                    <button type="button"
                                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-maroon/10 flex items-center justify-between gap-3"
                                            data-value="{{ $id }}"
                                            data-label="{{ $nama }}">
                                        <span class="truncate">{{ $nama }}</span>
                                        <span class="text-maroon font-semibold {{ in_array($id, $selectedLayanan ?? []) ? '' : 'hidden' }}" data-check="{{ $id }}">✓</span>
                                    </button>
                                @endforeach

                                <div class="px-4 py-2">
                                    <hr class="border-gray-200">
                                </div>

                                <div class="px-3 py-2 text-xs font-semibold text-gray-500">Lainnya</div>

                                <button type="button"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-maroon/10 flex items-center justify-between gap-3"
                                        data-value="lainnya"
                                        data-label="{{ $layananOptions['lainnya'] ?? 'Lainnya' }}">
                                    <span class="truncate">{{ $layananOptions['lainnya'] ?? 'Lainnya' }}</span>
                                    <span class="text-maroon font-semibold {{ in_array('lainnya', $selectedLayanan ?? []) ? '' : 'hidden' }}" data-check="lainnya">✓</span>
                                </button>
                            </div>
                        </div>

                        @error('layanan')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input tambahan kalau pilih "lainnya" --}}
                    <div id="layananLainnyaWrap" class="hidden">
                        <label class="text-base font-semibold">
                            Tuliskan Layanan Lainnya <span class="text-red-600">*</span>
                        </label>
                        <input type="text"
                            name="layanan_lainnya"
                            id="layanan_lainnya"
                            value="{{ old('layanan_lainnya', $formData['layanan_lainnya'] ?? '') }}"
                            placeholder="Contoh: Pendampingan penyusunan proposal riset"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base
                                   focus:border-maroon focus:ring-2 focus:ring-maroon/30">
                        @error('layanan_lainnya')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Judul Konsultasi -->
                    <div>
                        <label class="text-base font-semibold">Judul Konsultasi <span class="text-red-600">*</span></label>
                        <input type="text" name="judul_konsultasi" required
                            value="{{ old('judul_konsultasi', $formData['judul_konsultasi'] ?? '') }}"
                            class="mt-2 w-full rounded-xl border {{ $errors->has('judul_konsultasi') ? 'border-red-500' : 'border-gray-300' }} bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30"
                            placeholder="Contoh: Konsultasi Pengelolaan Air Bersih">
                    </div>

                    <!-- Keluhan -->
                    <div>
                        <label class="text-base font-semibold">Keluhan / Permasalahan <span class="text-red-600">*</span></label>
                        <p class="mt-1 text-sm text-gray-600">Ceritakan latar belakang inovasi atau masalah yang sedang dihadapi</p>
                        <textarea name="keluhan" rows="3"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30"
                            placeholder="Contoh: Tahun Inovasi = 2024&#10;Inovasi pengelolaan sampah berbasis digital mengalami kendala pada tahap implementasi di lapangan..."
                            required>{{ old('keluhan', $formData['keluhan'] ?? '') }}</textarea>
                        <p id="catatanTahunInovasi" class="text-sm text-maroon font-semibold hidden">*Cantumkan Tahun Inovasi Anda</p>
                    </div>

                    <!-- Poin Asistensi -->
                    <div>
                        <label class="text-base font-semibold">Poin Asistensi yang Dibutuhkan <span class="text-red-600">*</span></label>
                        <p class="mt-1 text-sm text-gray-600">Tuliskan poin-poin yang ingin didiskusikan atau dibantu dalam sesi konsultasi ini</p>
                        <textarea name="poin_asistensi" rows="3"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30"
                            placeholder="Contoh:&#10;- Review kelayakan inovasi&#10;- Penyusunan indikator inovasi daerah&#10;- Pendampingan pendaftaran HAKI"
                            required>{{ old('poin_asistensi', $formData['poin_asistensi'] ?? '') }}</textarea>
                    </div>

                    {{-- Tanggal dan Jam Usulan --}}
                    <div>
                        <p class="text-base font-semibold">Kapan Anda ingin bertemu? <span class="text-red-600">*</span></p>
                        <p class="mt-1 text-sm text-gray-500">
                            Pilih tanggal dan jam yang Anda inginkan. Tim BRIDA akan konfirmasi atau menyesuaikan jadwal dengan ketersediaan.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Tanggal</label>
                                <input type="date" name="tanggal" id="tanggal" required
                                    value="{{ old('tanggal', $formData['tanggal'] ?? '') }}"
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30">
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-700">Jam (WITA)</label>
                                <input name="jam" id="jam" type="time" required
                                    min="08:00"
                                    max="16:00"
                                    value="{{ old('jam', $formData['jam'] ?? '') }}"
                                    class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30">
                            </div>
                        </div>
                    </div>

                    {{-- Cara Pertemuan --}}
                    <div>
                        <p class="text-base font-semibold">Bagaimana cara bertemu? <span class="text-red-600">*</span></p>
                        <p class="mt-1 text-sm text-gray-500">Pilih sesuai kenyamanan Anda. Bisa disesuaikan kembali saat konfirmasi.</p>
                        <div class="mt-2 grid sm:grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:border-maroon transition">
                                <input type="radio" name="mode" value="offline" required class="h-5 w-5 accent-maroon"
                                    {{ old('mode', $formData['mode'] ?? '') == 'offline' ? 'checked' : '' }}>
                                <div>
                                    <span class="text-base font-semibold block">Tatap Muka</span>
                                    <span class="text-xs text-gray-500">Langsung di kantor BRIDA</span>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 border border-gray-300 rounded-xl px-4 py-3 cursor-pointer hover:border-maroon transition">
                                <input type="radio" name="mode" value="online" required class="h-5 w-5 accent-maroon"
                                    {{ old('mode', $formData['mode'] ?? '') == 'online' ? 'checked' : '' }}>
                                <div>
                                    <span class="text-base font-semibold block">Online</span>
                                    <span class="text-xs text-gray-500">Via Zoom atau Google Meet</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Target Personil -->
                    <div class="relative">
                        <label class="text-base font-semibold">
                            Pilih Personil BRIDA yang Ingin Ditemui (Opsional)
                        </label>
                        <p class="mt-1 text-sm text-gray-600">Ketersediaan pegawai yang dipilih akan disesuaikan dan dikonfirmasi oleh tim BRIDA</p>

                        <div class="relative">
                            <input id="pegawaiInput" type="text"
                                autocomplete="off"
                                placeholder="Ketik atau pilih pegawai..."
                                class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 pr-10 text-base focus:border-maroon focus:ring-2 focus:ring-maroon/30">

                            <button type="button"
                                id="pegawaiToggle"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-maroon">
                                ▼
                            </button>
                        </div>

                        <input type="hidden" name="pegawai_id" id="pegawai_id">

                        <div id="pegawaiDropdown"
                            class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-sm hidden max-h-52 overflow-y-auto">
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    <div>
                        <label class="text-base font-semibold">
                            Lampiran Surat / Permohonan <span class="text-red-600">*</span>
                        </label>
                        <p class="mt-1 text-sm text-gray-600">
                            Upload surat permohonan (PDF/DOC/DOCX, maks. <strong>3 file</strong>, maks. 10MB/file)
                        </p>
                        <p class="mt-1 text-sm text-gray-600">
                            Belum punya surat?
                            <a href="https://drive.google.com/drive/folders/19yJ2sLh1Y5Eck4g6CMYRVmdWPr-tcQiw?usp=sharing"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="text-maroon font-semibold underline underline-offset-2 hover:opacity-75">
                                Download template surat di sini
                            </a>
                        </p>

                        <input type="file"
                            name="lampiran[]"
                            id="lampiranInput"
                            multiple
                            accept=".pdf,.doc,.docx"
                            class="mt-2 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-base
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-maroon file:text-white
                                hover:file:bg-maroon-800">
                        
                        {{-- Dummy input untuk trigger validasi browser --}}
                        <input type="text"
                            id="lampiranDummy"
                            tabindex="-1"
                            aria-hidden="true"
                            style="position:absolute; left:0; width:100%; height:1px; opacity:0; pointer-events:none; overflow:hidden;">

                        {{-- Chip nama file --}}
                        <div id="lampiranChips" class="flex flex-wrap gap-2 mt-3"></div>
                        <p id="lampiranError" class="mt-1 text-xs text-red-600 hidden">⚠ Maksimal 3 file diperbolehkan.</p>
                        <p id="lampiranErrorFormat" class="mt-1 text-xs text-red-600 hidden">⚠ Hanya file PDF, DOC, atau DOCX yang diperbolehkan.</p>
                        <p id="lampiranErrorSize" class="mt-1 text-xs text-red-600 hidden">⚠ Ukuran file tidak boleh melebihi 10MB.</p>

                        @error('lampiran')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @error('lampiran.*')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Persetujuan --}}
                    <label class="flex items-start gap-3 text-sm text-gray-700">
                        <input type="checkbox" required class="mt-1 h-5 w-5">
                        <span class="text-base leading-relaxed">
                            Saya setuju data di atas digunakan hanya untuk keperluan penjadwalan & administrasi BRIDA.
                        </span>
                    </label>

                    {{-- Submit --}}
                    <button type="submit"
                        class="w-full mt-2 px-6 py-3 rounded-xl bg-maroon text-white font-extrabold text-base hover:bg-maroon-800 transition">
                        Kirim Pengajuan
                    </button>
                    <p class="text-sm text-gray-600 text-center leading-relaxed">
                        Setelah kirim, admin akan memeriksa dan menghubungi Anda untuk konfirmasi jadwal.
                    </p>
                </form>
            </div>
        </div>

        <div class="lg:col-span-3" id="jadwal">
            <!-- KALENDER -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-3">
                    <div>
                        <h2 class="text-2xl font-extrabold text-maroon">Kalender Janji Temu</h2>
                        <p class="text-base text-gray-700 mt-1">
                            Tanggal yang ada titik berarti ada jadwal. Klik tanggal untuk lihat rincian.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-2 text-sm text-gray-600">
                            <span class="h-2.5 w-2.5 rounded-full bg-maroon"></span> Ada jadwal
                        </span>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-2xl bg-white shadow-sm">
                    <div class="px-4 py-4 border-b border-gray-200 flex items-center justify-center relative">
                        <button id="calPrev"
                            class="absolute left-4 w-9 h-9 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center focus:ring-4 focus:ring-maroon/20">
                            ←
                        </button>

                        <div class="text-center">
                            <p id="calMonthLabel" class="text-base font-extrabold text-gray-900"></p>
                            <p class="text-xs text-gray-500">Kalender jadwal yang disetujui</p>
                        </div>

                        <button id="calNext"
                            class="absolute right-4 w-9 h-9 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 flex items-center justify-center focus:ring-4 focus:ring-maroon/20">
                            →
                        </button>
                    </div>

                    <div class="p-4">
                        <div class="grid grid-cols-7 gap-2 text-center text-[11px] font-bold text-gray-400 mb-3 uppercase">
                            <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                        </div>
                        <div id="calGrid" class="grid grid-cols-7 gap-2"></div>
                    </div>

                    <div class="px-4 pb-4">
                        <button id="calToday"
                            class="w-full sm:w-auto mx-auto block px-6 py-2 rounded-xl bg-maroon/10 text-maroon font-bold text-xs uppercase hover:bg-maroon/20 transition">
                            Bulan Ini
                        </button>
                    </div>
                </div>
            </div>

            <!-- JADWAL -->
            <div class="mt-10 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-maroon">Jadwal Konsultasi</h2>
                    <p class="text-base text-gray-700 mt-1">
                        Klik salah satu jadwal untuk melihat rincian.
                    </p>
                </div>
            </div>

            <!-- FILTER -->
            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 mb-5">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                        <select id="filterStatus"
                            class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm">
                            <option value="">Semua Status</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="akan">Akan Dijadwalkan</option>
                            <option value="terjadwal">Terjadwal</option>
                            <option value="sesi">Sesi Konsultasi</option>
                            <option value="ulang">Dijadwalkan Ulang</option>
                            <option value="tolak">Ditolak</option>
                            <option value="selesai">Tutup/Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                        <input type="date" id="filterFrom"
                            class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                        <input type="date" id="filterTo"
                            class="w-full h-10 rounded-lg border border-gray-300 px-3 text-sm">
                    </div>

                    <div>
                        <button id="btnFilter"
                            class="w-full h-10 rounded-lg bg-maroon text-white text-sm font-bold">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>

            <!-- LIST JADWAL -->
            <div class="space-y-4 h-[1300px] overflow-y-auto pr-2">
                @forelse ($jadwals as $j)
                    @php
                        $pakaiFinal = in_array($j->status, [
                            $statusTerjadwal,
                            $statusSesiKonsultasi,
                            $statusDijadwalkanUlang,
                            $statusSelesai,
                        ], true);

                        $tanggalTampil = $pakaiFinal && $j->tanggal_final
                            ? $j->tanggal_final
                            : $j->tanggal_usulan;

                        $jamTampil = $pakaiFinal && $j->jam_final
                            ? $j->jam_final
                            : $j->jam_usulan;

                        $modeTampil = $pakaiFinal && $j->metode_final
                            ? $j->metode_final
                            : $j->metode_usulan;

                        $lokasiTampil = $pakaiFinal && $j->lokasi_link_final
                            ? $j->lokasi_link_final
                            : ($j->lokasi_link_usulan ?? '—');
                    @endphp

                    <button type="button"
                        class="w-full text-left p-6 border border-gray-200 rounded-2xl hover:shadow-md transition"
                        data-modal="schedule"
                        data-title="{{ $j->judul_konsultasi }}"
                        data-instansi="{{ $j->opd_unit }}"
                        data-layanan="{{ $j->layanan_nama }}"
                        data-tanggal="{{ \Carbon\Carbon::parse($tanggalTampil)->translatedFormat('d M Y') }}"
                        data-jam="{{ \Carbon\Carbon::parse($jamTampil)->format('H:i') }} WITA"
                        data-mode="{{ $modeTampil }}"
                        data-lokasi="{{ $lokasiTampil }}"
                        data-deskripsi="{{ $j->keluhan }}"
                        data-dateiso="{{ \Carbon\Carbon::parse($tanggalTampil)->format('Y-m-d') }}"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $j->judul_konsultasi }}
                                </h3>
                                <p class="text-base text-gray-700 mt-1">
                                    Instansi: {{ $j->opd_unit }}
                                </p>
                            </div>

                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $j->status_badge_class }}">
                                {{ $j->status }}
                            </span>
                        </div>

                        <div class="mt-4 text-base text-gray-700 flex flex-wrap gap-4">
                            <span>
                                📅
                                @if($j->tanggal_final)
                                    {{ \Carbon\Carbon::parse($j->tanggal_final)->translatedFormat('d M Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($j->tanggal_usulan)->translatedFormat('d M Y') }}
                                @endif
                            </span>

                            <span>
                                ⏰
                                @if($j->jam_final)
                                    {{ \Carbon\Carbon::parse($j->jam_final)->format('H:i') }} WITA
                                @else
                                    {{ \Carbon\Carbon::parse($j->jam_usulan)->format('H:i') }} WITA
                                @endif
                            </span>

                            <span>
                                📍
                                @if($j->metode_final)
                                    {{ $j->metode_final == 'online' ? 'Online' : 'Tatap Muka' }}
                                @else
                                    {{ $j->metode_usulan == 'online' ? 'Online' : 'Tatap Muka' }}
                                @endif
                            </span>
                        </div>

                        <p class="mt-3 text-sm text-gray-600">Klik untuk rincian lengkap</p>
                    </button>
                @empty
                    <p class="text-center text-gray-500">
                        Belum ada jadwal tersedia.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- 3 LANGKAH MUDAH -->
<section id="cara" class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">3 Langkah Mudah</h2>
        </div>

        <div class="mt-8 grid md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-white border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">1) Isi Data Singkat</p>
                <p class="mt-2 text-base text-gray-700">Nama, instansi, keperluan, dan pilih tanggal & jam.</p>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">2) Klik “Kirim”</p>
                <p class="mt-2 text-base text-gray-700">Pengajuan masuk ke admin BRIDA untuk diperiksa.</p>
            </div>
            <div class="rounded-2xl bg-white border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">3) Tunggu Konfirmasi</p>
                <p class="mt-2 text-base text-gray-700">Admin menghubungi Anda (WhatsApp/telepon) untuk jadwal final.</p>
            </div>
        </div>
    </div>
</section>

{{-- PANDUAN PENGGUNAAN — tambahkan ini setelahnya --}}
<section class="py-12 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Panduan Penggunaan</h2>
            <p class="mt-2 text-gray-600 text-base">
                Baca panduan lengkap sebelum mengajukan konsultasi ke BRIDA.
            </p>
        </div>

        {{-- PDF Viewer --}}
        <div class="max-w-4xl mx-auto">
            <div class="rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                {{-- Header card --}}
                <div class="flex items-center justify-between px-5 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Panduan Penggunaan SIGAP Inkubatorma</p>
                            <p class="text-xs text-gray-500">Dokumen PDF</p>
                        </div>
                    </div>

                    <a href="{{ asset('storage/inkubatorma/panduan/panduan-sigap-inkubatorma.pdf') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>

                {{-- PDF embed — tampil di desktop --}}
                <div class="hidden sm:block bg-gray-100">
                    <iframe
                        src="{{ asset('storage/inkubatorma/panduan/panduan-sigap-inkubatorma.pdf') }}"
                        class="w-full"
                        style="height: 600px;"
                        type="application/pdf">
                    </iframe>
                </div>

                {{-- Mobile: tidak embed, cukup tombol buka/download --}}
                <div class="sm:hidden px-5 py-6 text-center bg-gray-50">
                    <p class="text-sm text-gray-600 mb-4">
                        Buka panduan di tab baru untuk membaca atau mengunduh dokumen PDF.
                    </p>
                    <a href="{{ asset('storage/panduan/panduan-sigap-inkubatorma.pdf') }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-maroon text-white text-sm font-semibold hover:opacity-90">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Buka Panduan PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL -->
<div id="scheduleModal"
    class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/40">
    <div class="bg-white w-[min(720px,92vw)] h-[85vh] rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden">
        <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 py-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-white/80 text-sm">Rincian Jadwal</p>
                    <h3 id="mTitle"
                        class="text-white text-xl sm:text-2xl font-extrabold leading-tight">—</h3>
                    <div id="multiScheduleWrapper" class="mt-3 hidden">
                        <label class="text-xs text-white/80">Pilih Jam</label>
                        <select id="multiScheduleSelect"
                            class="mt-1 rounded-lg px-3 py-2 text-sm text-gray-900 w-full">
                        </select>
                    </div>
                </div>
                <button id="closeModalX"
                    class="text-white/90 hover:text-white rounded-lg px-2 py-1">
                    ✕
                </button>
            </div>
        </div>

        <div class="px-6 py-6 bg-white overflow-y-auto flex-1">
            <div class="grid sm:grid-cols-2 gap-4 text-base">
                <div class="rounded-xl border p-4">
                    <p class="text-sm text-gray-500">Instansi</p>
                    <p id="mInstansi" class="font-semibold">—</p>
                </div>
                <div class="rounded-xl border p-4">
                    <p class="text-sm text-gray-500">Layanan</p>
                    <p id="mLayanan" class="font-semibold">—</p>
                </div>
                <div class="rounded-xl border p-4">
                    <p class="text-sm text-gray-500">Tanggal & Jam</p>
                    <p class="font-semibold">
                        <span id="mTanggal">—</span> • <span id="mJam">—</span>
                    </p>
                </div>
                <div class="rounded-xl border p-4">
                    <p class="text-sm text-gray-500">Metode</p>
                    <p id="mMode" class="font-semibold">—</p>
                </div>
                <div class="sm:col-span-2 rounded-xl border p-4">
                    <p class="text-sm text-gray-500">Lokasi / Link</p>
                    <p id="mLokasi" class="font-semibold">—</p>
                </div>
                <div class="sm:col-span-2 rounded-xl border p-4">
                    <p class="text-sm text-gray-500">Keluhan</p>
                    <p id="mDeskripsi">—</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button id="closeModalBtn"
                    class="px-6 py-3 rounded-xl border font-semibold">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    // ====== MODAL LOGIC ======
    const modal = document.getElementById('scheduleModal');
    const mTitle   = document.getElementById('mTitle');
    const mInstansi= document.getElementById('mInstansi');
    const mLayanan = document.getElementById('mLayanan');
    const mTanggal = document.getElementById('mTanggal');
    const mJam     = document.getElementById('mJam');
    const mMode    = document.getElementById('mMode');
    const mLokasi  = document.getElementById('mLokasi');
    const mDeskripsi = document.getElementById('mDeskripsi');
    const multiWrapper = document.getElementById('multiScheduleWrapper');
    const multiSelect  = document.getElementById('multiScheduleSelect');

    let activeMultiList = [];
    const closeX   = document.getElementById('closeModalX');
    const closeBtn = document.getElementById('closeModalBtn');

    const wrap   = document.getElementById('layananDropdownWrap');
    const btn    = document.getElementById('layananBtn');
    const panel  = document.getElementById('layananPanel');
    const label  = document.getElementById('layananBtnLabel');
    const native = document.getElementById('layananSelectNative');
    const container = document.getElementById('selectedLayananContainer');

    // Set tanggal minimum = hari ini
    const tanggalInput = document.getElementById('tanggal');
    if (tanggalInput) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        tanggalInput.min = `${yyyy}-${mm}-${dd}`;
    }

    // bisa 3 file
    const input       = document.getElementById('lampiranInput');
    const chips       = document.getElementById('lampiranChips');
    const errorMsg    = document.getElementById('lampiranError');
    const errorFormat = document.getElementById('lampiranErrorFormat');
    const errorSize   = document.getElementById('lampiranErrorSize');
    const MAX         = 3;
    const formatAllowed = ['application/pdf', 'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    const MAX_MB = 10;
    let selectedFiles = [];
    // input.setCustomValidity('Upload minimal 1 file lampiran.'); 
    // const lampiranDummy = document.getElementById('lampiranDummy');
    // lampiranDummy.setCustomValidity('Upload minimal 1 file lampiran.');

    input.addEventListener('change', function () {
        errorMsg.classList.add('hidden');
        errorFormat.classList.add('hidden');
        errorSize.classList.add('hidden');

        const incoming = Array.from(this.files);
        let adaFormatSalah = false;
        let adaMelebihi    = false;

        incoming.forEach(f => {
            if (!formatAllowed.includes(f.type)) {
                adaFormatSalah = true;
                return;
            }
            if (f.size > MAX_MB * 1024 * 1024) {
                adaMelebihi = true;
                return;
            }
            if (!selectedFiles.find(x => x.name === f.name)) {
                selectedFiles.push(f);
            }
        });

        if (adaFormatSalah) errorFormat.classList.remove('hidden');
        if (adaMelebihi)    errorSize.classList.remove('hidden');

        if (selectedFiles.length > MAX) {
            selectedFiles = selectedFiles.slice(0, MAX);
            errorMsg.classList.remove('hidden');
        }

        syncInput();
        renderChips();
    });

    function openModalWithData(data, list = null) {
        function render(d) {
            mTitle.textContent     = d.title || '—';
            mInstansi.textContent  = d.instansi || '—';
            mLayanan.textContent   = d.layanan || '—';
            mTanggal.textContent   = d.tanggal || '—';
            mJam.textContent       = d.jam || '—';
            mMode.textContent =
                d.mode === 'online'
                    ? 'Online'
                    : d.mode === 'offline'
                        ? 'Tatap Muka'
                        : '—';
            mLokasi.textContent    = d.lokasi || '—';
            mDeskripsi.textContent = d.deskripsi || '—';
        }

        render(data);

        if (Array.isArray(list) && list.length > 1) {
            activeMultiList = list;
            multiSelect.innerHTML = '';

            list.forEach((e, i) => {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = `${e.jam} — ${e.title}`;
                multiSelect.appendChild(opt);
            });

            multiWrapper.classList.remove('hidden');
            multiSelect.onchange = () => {
                const idx = multiSelect.value;
                render(activeMultiList[idx]);
            };
        } else {
            multiWrapper.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function openModalFromCard(cardBtn) {
        openModalWithData({
            title: cardBtn.dataset.title,
            instansi: cardBtn.dataset.instansi,
            layanan: cardBtn.dataset.layanan,
            tanggal: cardBtn.dataset.tanggal,
            jam: cardBtn.dataset.jam,
            mode: cardBtn.dataset.mode,
            lokasi: cardBtn.dataset.lokasi,
            deskripsi: cardBtn.dataset.deskripsi,
        });
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    document.querySelectorAll('[data-modal="schedule"]').forEach(btnCard => {
        btnCard.addEventListener('click', () => openModalFromCard(btnCard));
        btnCard.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openModalFromCard(btnCard);
            }
        });
    });

    closeX.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);

    // ====== KALENDER ======
    const monthNames = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    const calendarEvents = @json($calendarJadwals);
    const layananOptions = @json($layananOptions);

    const events = calendarEvents.map(j => ({
        dateISO: (j.tanggal_final || j.tanggal_usulan).slice(0,10),
        title: j.judul_konsultasi,
        instansi: j.opd_unit,
        layanan: j.layanan_nama ?? (layananOptions[j.layanan_id] ?? '-'),
        tanggal: (j.tanggal_final || j.tanggal_usulan).slice(0,10),
        jam: j.jam_final || j.jam_usulan,
        mode: j.metode_final || j.metode_usulan,
        lokasi: j.lokasi_link_final || '—',
        deskripsi: j.keluhan
    }));

    const eventsByDate = events.reduce((acc, e) => {
        (acc[e.dateISO] ||= []).push(e);
        return acc;
    }, {});

    const calGrid = document.getElementById('calGrid');
    const calMonthLabel = document.getElementById('calMonthLabel');
    const calPrev = document.getElementById('calPrev');
    const calNext = document.getElementById('calNext');
    const calToday = document.getElementById('calToday');

    const now = new Date();
    let viewYear = now.getFullYear();
    let viewMonth = now.getMonth();

    function pad2(n){ return String(n).padStart(2,'0'); }
    function toISODate(y,m,d){ return `${y}-${pad2(m+1)}-${pad2(d)}`; }

    function renderCalendar() {
        calGrid.innerHTML = '';
        calMonthLabel.textContent = `${monthNames[viewMonth]} ${viewYear}`;

        const firstDay = new Date(viewYear, viewMonth, 1);
        const startDow = firstDay.getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        for (let i = 0; i < startDow; i++) {
            const cell = document.createElement('div');
            cell.className = 'h-12 md:h-14 lg:h-16 rounded-xl bg-gray-50 border border-gray-100';
            calGrid.appendChild(cell);
        }

        const todayISO = toISODate(now.getFullYear(), now.getMonth(), now.getDate());

        for (let d = 1; d <= daysInMonth; d++) {
            const iso = toISODate(viewYear, viewMonth, d);
            const hasEvent = !!eventsByDate[iso];
            const isToday = iso === todayISO;

            const btnDay = document.createElement('button');
            btnDay.type = 'button';
            btnDay.className =
                'relative h-12 md:h-14 lg:h-16 rounded-xl border text-left px-2 py-2 overflow-hidden ' +
                'focus:outline-none focus:ring-4 focus:ring-maroon/20 ' +
                (hasEvent
                    ? 'border-maroon/30 bg-maroon/5 hover:bg-maroon/10'
                    : 'border-gray-200 bg-white hover:bg-gray-50'
                );

            btnDay.setAttribute('aria-label', hasEvent ? `Tanggal ${d}, ada jadwal` : `Tanggal ${d}`);
            btnDay.innerHTML = `
                <span class="absolute top-2 left-2 text-sm md:text-lg font-bold ${isToday ? 'text-maroon' : 'text-gray-900'}">
                    ${d}
                </span>
                ${hasEvent ? `
                    <div class="absolute bottom-1.5 left-2 right-2 flex justify-center">
                        <span class="h-2 w-2 rounded-full bg-maroon sm:hidden"></span>
                        <div class="hidden sm:flex items-center gap-1">
                            <span class="h-2 w-2 rounded-full bg-maroon"></span>
                            <span class="text-[10px] text-gray-600 font-semibold">
                                ${eventsByDate[iso].length} jadwal
                            </span>
                        </div>
                    </div>` : ''}
            `;

            if (hasEvent) {
                btnDay.addEventListener('click', () => {
                    const list = eventsByDate[iso];
                    openModalWithData(list[0], list);
                });
            }
            calGrid.appendChild(btnDay);
        }
    }

    function goPrevMonth() {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    }
    function goNextMonth() {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    }
    function goThisMonth() {
        viewYear = now.getFullYear();
        viewMonth = now.getMonth();
        renderCalendar();
    }

    calPrev.addEventListener('click', goPrevMonth);
    calNext.addEventListener('click', goNextMonth);
    calToday.addEventListener('click', goThisMonth);

    renderCalendar();

    // ====== DROPDOWN PEGAWAI ======
    const pegawaiList = @json($employees);
    const pegawaiInput   = document.getElementById("pegawaiInput");
    const pegawaiDropdown = document.getElementById("pegawaiDropdown");
    const pegawaiIdInput = document.getElementById("pegawai_id");
    const pegawaiToggle = document.getElementById("pegawaiToggle");

    function renderPegawai(list){
        pegawaiDropdown.innerHTML = "";

        if(list.length === 0){
            pegawaiDropdown.classList.add("hidden");
            return;
        }

        list.forEach(emp => {
            const div = document.createElement("div");
            div.className = "px-3 py-2 text-sm cursor-pointer hover:bg-maroon/10";
            div.textContent = emp.name;

            div.onclick = () => {
                pegawaiInput.value = emp.name;
                pegawaiIdInput.value = emp.id;
                pegawaiDropdown.classList.add("hidden");
            };

            pegawaiDropdown.appendChild(div);
        });

        pegawaiDropdown.classList.remove("hidden");
    }

    pegawaiInput?.addEventListener("focus", () => renderPegawai(pegawaiList));

    pegawaiInput?.addEventListener("keyup", () => {
        const keyword = pegawaiInput.value.toLowerCase();
        const filtered = pegawaiList.filter(emp =>
            emp.name.toLowerCase().includes(keyword)
        );
        renderPegawai(filtered);
    });

    pegawaiToggle?.addEventListener('click', () => {
        if (pegawaiDropdown.classList.contains('hidden')) {
            renderPegawai(pegawaiList);
        } else {
            pegawaiDropdown.classList.add('hidden');
        }
    });

    document.addEventListener("click", (e) => {
        if (
            pegawaiInput &&
            pegawaiDropdown &&
            !pegawaiInput.contains(e.target) &&
            !pegawaiDropdown.contains(e.target) &&
            !pegawaiToggle?.contains(e.target)
        ) {
            pegawaiDropdown.classList.add("hidden");
        }
    });

    // ====== FILTER STATUS & TANGGAL ======
    const btnFilter = document.getElementById('btnFilter');
    const filterStatus = document.getElementById('filterStatus');
    const filterFrom = document.getElementById('filterFrom');
    const filterTo = document.getElementById('filterTo');

    const scheduleCards = Array.from(
        document.querySelectorAll('[data-modal="schedule"]')
    );

    function normalizeStatus(text) {
        text = (text || '').toLowerCase();
        if (text.includes('menunggu')) return 'menunggu';
        if (text.includes('akan')) return 'akan';
        if (text.includes('sesi')) return 'sesi';
        if (text.includes('terjadwal')) return 'terjadwal';
        if (text.includes('ulang')) return 'ulang';
        if (text.includes('tolak') || text.includes('tidak')) return 'tolak';
        if (text.includes('selesai')) return 'selesai';
        return '';
    }

    btnFilter?.addEventListener('click', () => {
        const selectedStatus = filterStatus.value;
        const fromDate = filterFrom.value ? new Date(filterFrom.value) : null;
        const toDate   = filterTo.value ? new Date(filterTo.value) : null;

        scheduleCards.forEach(card => {
            let show = true;

            if (selectedStatus) {
                const badge = card.querySelector('span.rounded-full');
                const statusText = badge ? badge.innerText : '';
                const statusValue = normalizeStatus(statusText);

                if (statusValue !== selectedStatus) {
                    show = false;
                }
            }

            const dateISO = card.dataset.dateiso;
            if (dateISO && (fromDate || toDate)) {
                const cardDate = new Date(dateISO);

                if (fromDate && cardDate < fromDate) show = false;
                if (toDate && cardDate > toDate) show = false;
            }

            card.style.display = show ? 'block' : 'none';
        });
    });

    // ====== DROPDOWN LAYANAN ======
    const lainnyaWrap  = document.getElementById('layananLainnyaWrap');
    const lainnyaInput = document.getElementById('layanan_lainnya');
    const catatanTahun = document.getElementById('catatanTahunInovasi');

    if (!wrap || !btn || !panel || !label || !native) return;

    let selectedValues = Array.from(native.selectedOptions).map(opt => opt.value);

    btn.addEventListener('click', () => {
        panel.classList.toggle('hidden');
    });

    panel.addEventListener('click', (e) => {
        const opt = e.target.closest('[data-value]');
        if (!opt) return;

        const value = opt.dataset.value;

        if (!selectedValues.includes(value)) {
            if (selectedValues.length >= 2) {
                alert('Maksimal 2 layanan dapat dipilih');
                return;
            }
            selectedValues.push(value);
        } else {
            selectedValues = selectedValues.filter(v => v !== value);
        }

        updateUI();
        panel.classList.add('hidden');
    });

    function cekCatatanTahun(){
        if(!catatanTahun) return;

        const adaSelainLainnya =
            selectedValues.length &&
            !selectedValues.every(v => v === 'lainnya');

        catatanTahun.classList.toggle('hidden', !adaSelainLainnya);
    }

    function toggleLainnya(){
        const show = selectedValues.includes('lainnya');

        if (lainnyaWrap) {
            lainnyaWrap.classList.toggle('hidden', !show);
        }

        if (lainnyaInput) {
            lainnyaInput.required = show;
            if (!show) {
                lainnyaInput.value = '';
            }
        }
    }

    function updateUI() {
        Array.from(native.options).forEach(opt => {
            opt.selected = selectedValues.includes(opt.value);
        });

        // Beritahu browser valid/tidak
        native.setCustomValidity(
            selectedValues.length === 0 ? 'Pilih minimal 1 layanan.' : ''
        );

        label.textContent = selectedValues.length
            ? selectedValues.map(v => layananOptions[v] ?? v).join(', ')
            : '— Pilih maksimal 2 layanan —';

        label.classList.toggle('text-gray-500', selectedValues.length === 0);
        label.classList.toggle('text-gray-900', selectedValues.length > 0);

        container.innerHTML = '';

        selectedValues.forEach(val => {
            const option = native.querySelector(`option[value="${val}"]`);
            if (!option) return;

            const chip = document.createElement('div');
            chip.className = "flex items-center gap-2 px-3 py-1 rounded-full bg-maroon/10 text-maroon text-sm font-semibold";
            chip.innerHTML = `
                ${option.textContent}
                <button type="button" class="font-bold">×</button>
            `;

            chip.querySelector('button').onclick = () => {
                selectedValues = selectedValues.filter(v => v !== val);
                updateUI();
            };

            container.appendChild(chip);
        });

        panel.querySelectorAll('[data-check]').forEach(el => {
            el.classList.toggle('hidden', !selectedValues.includes(el.getAttribute('data-check')));
        });

        cekCatatanTahun();
        toggleLainnya();
    }

    document.addEventListener('click', (e) => {
        if (!wrap.contains(e.target)) {
            panel.classList.add('hidden');
        }
    });

    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') panel.classList.add('hidden');
    });

    updateUI();

    // Validasi max 3 file di frontend
    function removeFile(name) {
        selectedFiles = selectedFiles.filter(f => f.name !== name);
        syncInput();
        renderChips();
    }

    function syncInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(f => dt.items.add(f));
        input.files = dt.files;

        // Untuk alert input
        input.setCustomValidity(
            selectedFiles.length === 0 ? 'Upload minimal 1 file lampiran.' : ''
        );
        // lampiranDummy.setCustomValidity(
        //     selectedFiles.length === 0 ? 'Upload minimal 1 file lampiran.' : ''
        // );
    }

    function renderChips() {
        chips.innerHTML = '';
        selectedFiles.forEach(file => {
            const sizeMB = (file.size / 1024 / 1024).toFixed(2);
            const chip = document.createElement('div');
            chip.className = 'flex items-center gap-2 px-3 py-1.5 rounded-full bg-maroon/10 border border-maroon/20 text-sm text-maroon';
            chip.innerHTML = `
                <span class="max-w-[180px] truncate font-medium">📄 ${file.name}</span>
                <span class="text-xs text-maroon/60">${sizeMB}MB</span>
                <button type="button" data-name="${file.name}"
                    class="ml-1 text-maroon/50 hover:text-red-500 font-bold text-lg leading-none">×</button>
            `;
            chip.querySelector('button').addEventListener('click', () => removeFile(file.name));
            chips.appendChild(chip);
        });
    }

    // ====== VALIDASI SUBMIT ======
    const form = document.querySelector('form[enctype="multipart/form-data"]');

    form?.addEventListener('submit', function (e) {
        syncInput(); // WAJIB biar file masuk ke input
        
        let firstError = null;

        // --- CEK LAYANAN ---
        const layananError = document.getElementById('layananValidasiError');
        if (selectedValues.length === 0) {
            e.preventDefault();
            if (!layananError) {
                const p = document.createElement('p');
                p.id = 'layananValidasiError';
                p.className = 'mt-1 text-xs text-red-600';
                p.textContent = '⚠ Pilih minimal 1 layanan.';
                document.getElementById('layananDropdownWrap')?.appendChild(p);
            }
            firstError = firstError ?? document.getElementById('layananDropdownWrap');
        } else {
            layananError?.remove();
        }

        // --- CEK LAYANAN LAINNYA ---
        const lainnyaError = document.getElementById('lainnyaValidasiError');
        if (selectedValues.includes('lainnya')) {
            const lainnyaVal = document.getElementById('layanan_lainnya')?.value?.trim();
            if (!lainnyaVal) {
                e.preventDefault();
                if (!lainnyaError) {
                    const p = document.createElement('p');
                    p.id = 'lainnyaValidasiError';
                    p.className = 'mt-1 text-xs text-red-600';
                    p.textContent = '⚠ Tuliskan spesifikasi layanan lainnya.';
                    document.getElementById('layananLainnyaWrap')?.appendChild(p);
                }
                firstError = firstError ?? document.getElementById('layananLainnyaWrap');
            } else {
                lainnyaError?.remove();
            }
        } else {
            lainnyaError?.remove();
        }

        // --- CEK LAMPIRAN ---
        // const lampiranError2 = document.getElementById('lampiranValidasiError');
        // if (selectedFiles.length === 0) {
        //     e.preventDefault();
        //     if (!lampiranError2) {
        //         const p = document.createElement('p');
        //         p.id = 'lampiranValidasiError';
        //         p.className = 'mt-1 text-xs text-red-600';
        //         p.textContent = '⚠ Upload minimal 1 file lampiran.';
        //         document.getElementById('lampiranInput')?.parentElement.appendChild(p);
        //     }
        //     firstError = firstError ?? document.getElementById('lampiranInput');
        // } else {
        //     lampiranError2?.remove();
        // }

        // --- SCROLL KE ERROR PERTAMA ---
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        // TRIGGER VALIDASI BROWSER
        if (!this.checkValidity()) {
            e.preventDefault();
            this.reportValidity();
        }
    });
})();
</script>
@endpush