@extends('layouts.app')

@section('content')
<div x-data="notulensiEdit()" class="max-w-7xl mx-auto space-y-5 pb-12">

  <!-- Action Bar Atas -->
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-200 shadow-xs">
    <div>
      <div class="flex items-center gap-2">
        <a href="{{ route('sigap-notulensi.show', $notulensi->id) }}" class="text-xs text-gray-500 hover:text-maroon flex items-center gap-1 transition">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
          Kembali ke Detail
        </a>
        <span class="text-gray-300">•</span>
        <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-bold border
          {{ $notulensi->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
          STATUS: {{ strtoupper($notulensi->status) }}
        </span>
      </div>
      <h1 class="text-lg sm:text-xl font-extrabold text-gray-900 mt-0.5">
        Edit <span class="text-maroon">Notulensi Rapat</span>
      </h1>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('sigap-notulensi.show', $notulensi->id) }}" 
         class="px-4 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
        Batal
      </a>
      
      <!-- Simpan Pembaruan -->
      <button type="button" @click="submitAs('draft')" 
              class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
        Simpan Draft
      </button>

      <button type="button" @click="submitAs('selesai')" 
              class="px-5 py-2 rounded-xl bg-maroon hover:bg-maroon-800 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        Simpan Perubahan
      </button>
    </div>
  </div>

  <form id="formNotulensi" action="{{ route('sigap-notulensi.update', $notulensi->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusField" value="{{ $notulensi->status }}">
    <input type="hidden" name="kinerja_photos" :value="JSON.stringify(selectedKinerjaPhotos)">
    <input type="hidden" name="existing_photos" :value="JSON.stringify(existingPhotos)">
    <input type="hidden" name="daftar_hadir_kegiatan_id" :value="selectedDaftarHadirId">

    <!-- BANNER SEARCHABLE INTEGRASI SIGAP DAFTAR HADIR -->
    <div class="p-4 rounded-2xl bg-gradient-to-r from-maroon-50 via-white to-maroon-50 border border-maroon/20 flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
        </div>
        <div>
          <h3 class="text-xs sm:text-sm font-bold text-gray-900">Tarik / Ganti Sinkronisasi Daftar Hadir</h3>
          <p class="text-[11px] text-gray-500">Pilih kegiatan presensi lain jika ingin menimpa data kegiatan & peserta saat ini.</p>
        </div>
      </div>
      
      <!-- Searchable Dropdown Kegiatan -->
      <div class="relative w-full md:w-96" @click.away="openDHDropdown = false">
        <div @click="openDHDropdown = !openDHDropdown" 
             class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-gray-300 bg-white shadow-2xs cursor-pointer text-xs focus:ring-2 focus:ring-maroon">
          <span class="truncate font-medium text-gray-800" x-text="selectedDHLabel || '-- Pilih / Cari Kegiatan Hadir --'"></span>
          <svg class="w-4 h-4 text-gray-400 shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>

        <div x-show="openDHDropdown" x-cloak 
             class="absolute z-30 mt-1 w-full bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
          <div class="p-2 border-b bg-gray-50">
            <input type="text" x-model="searchDHQuery" placeholder="Ketik nama kegiatan / tanggal..." 
                   class="w-full text-xs p-2 rounded-lg border border-gray-300 focus:outline-none focus:border-maroon">
          </div>
          <div class="max-h-56 overflow-y-auto p-1 text-xs divide-y divide-gray-50">
            <template x-for="item in filteredDaftarHadir" :key="item.id">
              <div @click="selectDHItem(item)" 
                   class="p-2.5 hover:bg-maroon-50 hover:text-maroon rounded-lg cursor-pointer transition">
                <p class="font-bold text-gray-900" x-text="item.nama_kegiatan"></p>
                <p class="text-[10px] text-gray-400" x-text="item.hari_tanggal + ' • ' + item.tempat"></p>
              </div>
            </template>
            <template x-if="filteredDaftarHadir.length === 0">
              <div class="p-3 text-center text-gray-400 text-[11px]">Kegiatan tidak ditemukan.</div>
            </template>
          </div>
        </div>
      </div>
    </div>

    <!-- TABS NAVIGASI -->
    <div class="border-b border-gray-200 mt-4">
      <nav class="flex space-x-2 sm:space-x-3 overflow-x-auto scrollbar-thin">
        <button type="button" @click="activeTab = 'notula'"
                :class="activeTab === 'notula' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
          <span class="w-5 h-5 rounded-full bg-maroon text-white text-[11px] flex items-center justify-center font-bold">1</span>
          <span>Notulensi Rapat</span>
        </button>

        <button type="button" @click="activeTab = 'undangan'"
                :class="activeTab === 'undangan' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
          <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 text-[11px] flex items-center justify-center font-bold">2</span>
          <span>Surat Pengantar / Undangan</span>
        </button>

        <button type="button" @click="activeTab = 'hadir'"
                :class="activeTab === 'hadir' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
          <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 text-[11px] flex items-center justify-center font-bold">3</span>
          <span>Daftar Hadir (Format Resmi)</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-gray-200 text-gray-700" x-text="pesertas.length"></span>
        </button>

        <button type="button" @click="activeTab = 'foto'"
                :class="activeTab === 'foto' ? 'border-maroon text-maroon font-bold bg-maroon/5' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="py-3 px-4 text-xs sm:text-sm border-b-2 rounded-t-xl transition flex items-center gap-2 shrink-0">
          <span class="w-5 h-5 rounded-full bg-gray-200 text-gray-700 text-[11px] flex items-center justify-center font-bold">4</span>
          <span>Dokumentasi Foto</span>
          <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-maroon text-white font-bold" x-text="totalFotoAktif()"></span>
        </button>
      </nav>
    </div>

    <!-- =================================================================== -->
    <!-- TAB 1: NOTULENSI LAPANGAN & TTD NOTULIS                           -->
    <!-- =================================================================== -->
    <div x-show="activeTab === 'notula'" class="space-y-5 pt-4">
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b pb-2">
          <h2 class="text-sm font-bold text-gray-800">A. Informasi Rapat / Acara Notulensi</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-800 mb-1">
              Nama Acara / Pembahasan Rapat <span class="text-red-500">*</span>
            </label>
            <input type="text" name="judul_acara" x-model="formData.judul" required 
                   class="w-full text-xs sm:text-sm rounded-xl p-2.5 font-semibold text-gray-900 border-gray-300 focus:border-maroon">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Hari & Tanggal</label>
            <input type="text" name="hari_tanggal" x-model="formData.hari_tanggal" class="w-full text-xs rounded-xl p-2.5">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Waktu Pelaksanaan</label>
            <input type="text" name="waktu" x-model="formData.waktu" class="w-full text-xs rounded-xl p-2.5">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tempat / Ruangan</label>
            <input type="text" name="tempat" x-model="formData.tempat" class="w-full text-xs rounded-xl p-2.5">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Pimpinan Rapat</label>
            <input type="text" name="pimpinan_rapat" x-model="formData.pimpinan_rapat" class="w-full text-xs rounded-xl p-2.5">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Notulis</label>
            <input type="text" name="notulis_nama" x-model="formData.notulis_nama" class="w-full text-xs rounded-xl p-2.5 bg-gray-50">
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan Ringkas Peserta</label>
            <input type="text" name="peserta_ringkas" x-model="formData.peserta_ringkas" class="w-full text-xs rounded-xl p-2.5">
          </div>
        </div>
      </div>

      <!-- Jalannya Rapat -->
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-3">
        <div class="flex items-center justify-between border-b pb-2">
          <div>
            <h2 class="text-sm font-bold text-gray-800">B. Catatan Notulensi / Jalannya Rapat</h2>
            <p class="text-[11px] text-gray-500">Edit poin-poin pembahasan rapat dan kesepakatan.</p>
          </div>
        </div>

        <textarea name="isi_pelaksana_kegiatan" rows="10" 
                  class="w-full text-xs sm:text-sm rounded-xl p-3 font-sans leading-relaxed focus:border-maroon">{{ old('isi_pelaksana_kegiatan', $notulensi->isi_pelaksana_kegiatan) }}</textarea>
      </div>

      <!-- Canvas Tanda Tangan Notulis -->
      @php
        $initialSig = $notulensi->notulis_ttd_image 
            ? asset('storage/' . $notulensi->notulis_ttd_image) 
            : (auth()->user()->signature_pad ? asset('storage/' . auth()->user()->signature_pad) : null);
      @endphp
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs" 
           x-data="signatureCanvas(@js($initialSig))">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b pb-2 mb-3">
          <div>
            <h2 class="text-sm font-bold text-gray-800">C. Tanda Tangan Digital Notulis</h2>
            <p class="text-[11px] text-gray-500">Tanda tangan digital notulis kegiatan.</p>
          </div>
          <template x-if="hasSavedSignature && !isRedrawing">
            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-md">
              ✓ Menggunakan TTD Tersimpan
            </span>
          </template>
        </div>

        <div class="flex flex-col sm:flex-row items-start gap-4">
          <div class="relative w-full sm:w-80 h-36 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl overflow-hidden flex items-center justify-center">
            <img x-show="hasSavedSignature && !isRedrawing" 
                 :src="savedSignatureUrl" 
                 alt="TTD Notulis" 
                 class="max-h-28 max-w-full object-contain">

            <canvas x-show="!hasSavedSignature || isRedrawing" 
                    x-ref="sigCanvas"
                    class="w-full h-full bg-white cursor-crosshair touch-none"
                    @mousedown="startDraw"
                    @mousemove="draw"
                    @mouseup="endDraw"
                    @mouseleave="endDraw"
                    @touchstart.prevent="startTouch"
                    @touchmove.prevent="drawTouch"
                    @touchend.prevent="endDraw">
            </canvas>

            <span x-show="!hasSavedSignature && !hasDrawn" class="absolute pointer-events-none text-xs text-gray-400">
              Goreskan tanda tangan di sini
            </span>
          </div>

          <div class="flex flex-col gap-2">
            <template x-if="hasSavedSignature && !isRedrawing">
              <button type="button" @click="startRedraw()" 
                      class="px-3 py-1.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 text-xs font-semibold">
                ✍ Ganti / Buat Baru
              </button>
            </template>

            <template x-if="!hasSavedSignature || isRedrawing">
              <div class="space-y-1.5">
                <button type="button" @click="clearSig()" 
                        class="w-full px-3 py-1.5 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-xs font-semibold">
                  Hapus Goresan
                </button>
                <template x-if="hasSavedSignature && isRedrawing">
                  <button type="button" @click="cancelRedraw()" 
                          class="w-full px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 text-xs">
                    Gunakan TTD Lama
                  </button>
                </template>
              </div>
            </template>
          </div>
        </div>

        <input type="hidden" name="notulis_ttd_base64" id="sigBase64Input" :value="currentBase64">
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- TAB 2: SURAT PENGANTAR / UNDANGAN                                   -->
    <!-- =================================================================== -->
    <div x-show="activeTab === 'undangan'" class="space-y-5 pt-4" style="display: none;">
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
        <h2 class="text-sm font-bold text-gray-800 border-b pb-2">Informasi Surat Pengantar / Undangan Resmi</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Surat</label>
            <input type="text" name="nomor_surat" x-model="formData.nomor_surat" class="w-full text-xs rounded-xl p-2.5">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Perihal Surat</label>
            <input type="text" name="perihal_surat" x-model="formData.perihal_surat" class="w-full text-xs rounded-xl p-2.5">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Surat</label>
            <input type="date" name="tanggal_surat" x-model="formData.tanggal_surat" class="w-full text-xs rounded-xl p-2.5">
          </div>
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tujuan Surat (Kepada Yth.)</label>
          <input type="text" name="tujuan_surat" x-model="formData.tujuan_surat" class="w-full text-xs rounded-xl p-2.5">
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Isi Paragraf Pembuka Undangan</label>
          <textarea name="isi_pembuka_surat" rows="3" class="w-full text-xs rounded-xl p-2.5">{{ old('isi_pembuka_surat', $notulensi->isi_pembuka_surat) }}</textarea>
        </div>

        <div class="border-t pt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pimpinan Penandatangan</label>
            <input type="text" name="pimpinan_nama" x-model="formData.pimpinan_nama" class="w-full text-xs rounded-xl p-2.5">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Jabatan Pimpinan</label>
            <input type="text" name="pimpinan_jabatan" x-model="formData.pimpinan_jabatan" class="w-full text-xs rounded-xl p-2.5">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Pangkat / Golongan</label>
            <input type="text" name="pimpinan_pangkat" x-model="formData.pimpinan_pangkat" class="w-full text-xs rounded-xl p-2.5">
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">NIP Pimpinan</label>
            <input type="text" name="pimpinan_nip" x-model="formData.pimpinan_nip" class="w-full text-xs rounded-xl p-2.5">
          </div>
          <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Ganti Scan Stempel & TTD Pimpinan (Biarkan kosong jika tetap menggunakan TTD bawaan Kaban)</label>
            @if($notulensi->pimpinan_ttd_image)
              <div class="mb-2">
                <img src="{{ asset('storage/' . $notulensi->pimpinan_ttd_image) }}" class="h-14 object-contain">
              </div>
            @endif
            <input type="file" name="pimpinan_ttd" accept="image/*" class="w-full text-xs rounded-xl p-2 border">
          </div>
        </div>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- TAB 3: DAFTAR HADIR (FORMAT RESMI PERSIS SIGAP DAFTAR HADIR)        -->
    <!-- =================================================================== -->
    <div x-show="activeTab === 'hadir'" class="space-y-5 pt-4" style="display: none;">
      
      <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-xs text-amber-900">
        <p class="font-bold flex items-center gap-1.5">
          <span>⚙️ Pengaturan Tampilan Lembar Presensi Notulensi</span>
        </p>
        <p class="text-[11px] text-amber-800 mt-1">
          Perubahan peserta, nama, instansi, atau kontak di tabel ini <strong>hanya berlaku untuk laporan notulensi ini</strong> dan tidak mengubah data absensi aslinya.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
        <div class="p-4 bg-gray-50 border-b flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Tabel Presensi Peserta</h3>
            <p class="text-[11px] text-gray-500">Tercatat <strong x-text="pesertas.length"></strong> peserta.</p>
          </div>
          <button type="button" @click="addPesertaRow()" class="px-3.5 py-1.5 rounded-lg bg-gray-900 text-white text-xs font-semibold hover:bg-black transition">
            + Tambah Baris Peserta
          </button>
        </div>

        <div class="overflow-x-auto p-4">
          <table class="min-w-full text-xs border border-gray-200 rounded-lg">
            <thead class="bg-gray-100 text-gray-600 uppercase font-semibold">
              <tr>
                <th class="py-2.5 px-3 text-center w-12">No</th>
                <th class="py-2.5 px-3 text-left">Nama Peserta</th>
                <th class="py-2.5 px-3 text-left">Instansi / Unit Kerja</th>
                <th class="py-2.5 px-3 text-center w-14">Gender</th>
                <th class="py-2.5 px-3 text-left">No. HP / NIP</th>
                <th class="py-2.5 px-3 text-left">Email</th>
                <th class="py-2.5 px-3 text-center w-24">Paraf / TTD</th>
                <th class="py-2.5 px-3 text-center w-12">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <template x-for="(p, index) in pesertas" :key="index">
                <tr>
                  <td class="py-2 px-3 text-center text-gray-500 font-bold" x-text="index + 1"></td>
                  <td class="py-2 px-3">
                    <input type="text" :name="'peserta['+index+'][nama]'" x-model="p.nama" placeholder="Nama Peserta" class="w-full text-xs rounded-lg p-1.5 border-gray-300">
                  </td>
                  <td class="py-2 px-3">
                    <input type="text" :name="'peserta['+index+'][instansi]'" x-model="p.instansi" placeholder="Instansi" class="w-full text-xs rounded-lg p-1.5 border-gray-300">
                  </td>
                  <td class="py-2 px-3 text-center">
                    <select :name="'peserta['+index+'][gender]'" x-model="p.gender" class="text-xs rounded-lg p-1 border-gray-300">
                      <option value="L">L</option>
                      <option value="P">P</option>
                    </select>
                  </td>
                  <td class="py-2 px-3">
                    <input type="text" :name="'peserta['+index+'][nip_nohp]'" x-model="p.nip_nohp" placeholder="No HP" class="w-full text-xs rounded-lg p-1.5 border-gray-300">
                  </td>
                  <td class="py-2 px-3">
                    <input type="email" :name="'peserta['+index+'][email]'" x-model="p.email" placeholder="Email" class="w-full text-xs rounded-lg p-1.5 border-gray-300">
                  </td>
                  <td class="py-2 px-3 text-center">
                    <template x-if="p.paraf_image">
                      <img :src="p.paraf_image" class="h-8 max-w-[80px] mx-auto object-contain">
                    </template>
                    <template x-if="!p.paraf_image">
                      <span class="text-[10px] text-gray-400 italic">Manual</span>
                    </template>
                    <input type="hidden" :name="'peserta['+index+'][paraf_image]'" :value="p.paraf_image">
                  </td>
                  <td class="py-2 px-3 text-center">
                    <button type="button" @click="removePesertaRow(index)" class="text-red-500 hover:text-red-700 font-bold text-sm">✕</button>
                  </td>
                </tr>
              </template>
              <template x-if="pesertas.length === 0">
                <tr>
                  <td colspan="8" class="py-8 text-center text-gray-400">
                    Belum ada baris peserta. Gunakan dropdown penarik di atas atau tambahkan baris manual.
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- =================================================================== -->
    <!-- TAB 4: DOKUMENTASI (FOTO TERSIMPAN + KINERJA + UPLOAD BARU)        -->
    <!-- =================================================================== -->
    <div x-show="activeTab === 'foto'" class="space-y-5 pt-4" style="display: none;">
      
      <!-- Foto Yang Sudah Tersimpan -->
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
        <div class="border-b pb-3 flex items-center justify-between">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Foto Dokumentasi Aktif</h3>
            <p class="text-[11px] text-gray-500">Foto yang saat ini terlampir pada dokumen notulensi.</p>
          </div>
          <span class="text-xs font-bold text-maroon bg-maroon-50 px-2.5 py-1 rounded-md" x-text="totalFotoAktif() + ' Foto'"></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
          <!-- List Foto Existing -->
          <template x-for="(foto, idx) in existingPhotos" :key="idx">
            <div class="relative group h-28 rounded-xl overflow-hidden border border-gray-200 shadow-2xs">
              <img :src="'/storage/' + foto" class="w-full h-full object-cover">
              <button type="button" @click="removeExistingPhoto(idx)" 
                      class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full text-xs shadow hover:bg-red-700 transition"
                      title="Hapus foto ini dari dokumen">
                ✕
              </button>
            </div>
          </template>

          <!-- List Foto Terpilih Baru dari Bukti Kinerja -->
          <template x-for="(foto, idx) in selectedKinerjaPhotos" :key="'k_'+idx">
            <div class="relative group h-28 rounded-xl overflow-hidden border-2 border-emerald-500 shadow-2xs">
              <img :src="'/storage/' + foto" class="w-full h-full object-cover">
              <button type="button" @click="removeKinerjaPhoto(idx)" 
                      class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full text-xs shadow hover:bg-red-700 transition"
                      title="Batalkan penambahan foto ini">
                ✕
              </button>
              <span class="absolute bottom-1 left-1 bg-emerald-600 text-white text-[9px] px-1.5 py-0.5 rounded font-bold">Baru</span>
            </div>
          </template>
        </div>

        <template x-if="totalFotoAktif() === 0">
          <div class="text-center py-6 text-gray-400 text-xs">
            Belum ada foto dokumentasi. Anda dapat memilih dari SIGAP Bukti Kinerja atau unggah manual di bawah.
          </div>
        </template>
      </div>

      <!-- Tambah dari Bukti Kinerja -->
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b pb-3">
          <div>
            <h3 class="text-sm font-bold text-gray-800">Pilih Tambahan dari SIGAP BUKTI KINERJA</h3>
            <p class="text-[11px] text-gray-500">Ambil foto dokumentasi tambahan dari data kinerja.</p>
          </div>
          <button type="button" @click="openKinerjaModal = true" 
                  class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-black transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            Cari di Bukti Kinerja
          </button>
        </div>
      </div>

      <!-- Tambah Manual Upload -->
      <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs space-y-4">
        <h3 class="text-sm font-bold text-gray-800 border-b pb-2">Unggah Tambahan Foto Baru (Manual)</h3>
        <p class="text-xs text-gray-500">Unggah file foto tambahan langsung dari komputer/perangkat.</p>

        <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-maroon transition bg-gray-50/50">
          <input type="file" name="dokumentasi[]" multiple accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-maroon file:text-white hover:file:bg-maroon-800">
        </div>
      </div>
    </div>
  </form>

  <!-- =================================================================== -->
  <!-- MODAL SEARCHABLE PEMILIH FOTO SIGAP BUKTI KINERJA                  -->
  <!-- =================================================================== -->
  <div x-show="openKinerjaModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div @click.away="openKinerjaModal = false" class="bg-white rounded-3xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
      <div class="px-6 py-4 bg-maroon text-white flex items-center justify-between">
        <div>
          <h3 class="font-bold text-base">Pencarian & Seleksi Bukti Kinerja</h3>
          <p class="text-white/80 text-xs mt-0.5">Saring berdasarkan kata kunci, judul kegiatan, atau tanggal pelaksanaan.</p>
        </div>
        <button type="button" @click="openKinerjaModal = false" class="text-white/70 hover:text-white font-bold text-xl">✕</button>
      </div>

      <div class="p-4 bg-gray-50 border-b grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="sm:col-span-2">
          <label class="block text-[11px] font-bold text-gray-600 mb-1">Cari Nama Kegiatan / Uraian</label>
          <div class="relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">🔍</span>
            <input type="text" x-model="searchKinerjaText" placeholder="Contoh: Rapat evaluasi, bimtek, sosialisasi..." 
                   class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-gray-300 bg-white focus:outline-none focus:border-maroon">
          </div>
        </div>

        <div>
          <label class="block text-[11px] font-bold text-gray-600 mb-1">Filter Tanggal / Bulan</label>
          <input type="month" x-model="searchKinerjaMonth" 
                 class="w-full py-2 px-3 text-xs rounded-xl border border-gray-300 bg-white focus:outline-none focus:border-maroon">
        </div>
      </div>

      <div class="p-6 overflow-y-auto space-y-5 flex-1">
        <template x-for="kinerja in filteredKinerjaList" :key="kinerja.id">
          <div class="border border-gray-200 rounded-2xl p-4 space-y-3 bg-white shadow-2xs">
            <div class="flex items-center justify-between border-b pb-2">
              <div>
                <h4 class="text-xs font-bold text-gray-900" x-text="kinerja.title"></h4>
                <p class="text-[10px] text-gray-500" x-text="'Tanggal: ' + (kinerja.date || '-')"></p>
              </div>
              <span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-semibold" 
                    x-text="kinerja.images.length + ' Foto'"></span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
              <template x-for="img in kinerja.images" :key="img.path">
                <div @click="toggleKinerjaPhoto(img.path)"
                     :class="isPhotoSelected(img.path) ? 'ring-3 ring-maroon border-transparent' : 'border-gray-200 hover:border-gray-400'"
                     class="relative h-24 rounded-xl overflow-hidden border cursor-pointer group shadow-2xs transition">
                  <img :src="img.url" class="w-full h-full object-cover">
                  
                  <div x-show="isPhotoSelected(img.path)" 
                       class="absolute inset-0 bg-maroon/30 flex items-center justify-center">
                    <span class="w-6 h-6 rounded-full bg-maroon text-white flex items-center justify-center text-xs font-bold shadow">✓</span>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </template>

        <template x-if="filteredKinerjaList.length === 0">
          <div class="text-center py-12 text-gray-400 text-xs">
            Tidak ada bukti kinerja yang sesuai dengan pencarian tersebut.
          </div>
        </template>
      </div>

      <div class="p-4 bg-gray-50 border-t flex items-center justify-between">
        <span class="text-xs text-gray-600">
          Foto Kinerja Terpilih: <strong class="text-maroon font-bold" x-text="selectedKinerjaPhotos.length"></strong>
        </span>
        <button type="button" @click="openKinerjaModal = false" class="px-5 py-2 rounded-xl bg-maroon text-white text-xs font-bold hover:bg-maroon-800 transition">
          Gunakan Foto
        </button>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
function notulensiEdit() {
  @php
    $initialPesertas = $notulensi->pesertas->map(function($p) {
        return [
            'nama'        => $p->nama,
            'instansi'    => $p->instansi,
            'gender'      => $p->gender ?? 'L',
            'nip_nohp'    => $p->nip_nohp,
            'email'       => $p->email,
            'paraf_image' => $p->paraf_image
        ];
    })->values()->all();

    $initialExistingPhotos = $notulensi->dokumentasi_foto ?? [];
    $initialKegiatanDH     = $kegiatanDaftarHadir ?? [];
    $initialBuktiKinerja   = $buktiKinerjaList ?? [];
  @endphp

  return {
    activeTab: 'notula',

    formData: {
      judul: @js($notulensi->judul_acara),
      tempat: @js($notulensi->tempat ?? ''),
      hari_tanggal: @js($notulensi->hari_tanggal ?? ''),
      waktu: @js($notulensi->waktu ?? ''),
      pimpinan_rapat: @js($notulensi->pimpinan_rapat ?? ''),
      notulis_nama: @js($notulensi->notulis_nama ?? ''),
      peserta_ringkas: @js($notulensi->peserta_ringkas ?? ''),
      nomor_surat: @js($notulensi->nomor_surat ?? ''),
      perihal_surat: @js($notulensi->perihal_surat ?? ''),
      tanggal_surat: @js(optional($notulensi->tanggal_surat)->format('Y-m-d') ?? date('Y-m-d')),
      tujuan_surat: @js($notulensi->tujuan_surat ?? ''),
      pimpinan_nama: @js($notulensi->pimpinan_nama ?? ''),
      pimpinan_jabatan: @js($notulensi->pimpinan_jabatan ?? ''),
      pimpinan_pangkat: @js($notulensi->pimpinan_pangkat ?? ''),
      pimpinan_nip: @js($notulensi->pimpinan_nip ?? '')
    },

    pesertas: @js($initialPesertas),
    existingPhotos: @js($initialExistingPhotos),
    selectedKinerjaPhotos: [],

    openDHDropdown: false,
    searchDHQuery: '',
    selectedDaftarHadirId: @js($notulensi->daftar_hadir_kegiatan_id ?? ''),
    selectedDHLabel: '',
    rawDaftarHadirList: @js($initialKegiatanDH),

    openKinerjaModal: false,
    searchKinerjaText: '',
    searchKinerjaMonth: '',
    rawKinerjaList: @js($initialBuktiKinerja),

    init() {
      if (this.selectedDaftarHadirId) {
        const found = this.rawDaftarHadirList.find(i => i.id == this.selectedDaftarHadirId);
        if (found) this.selectedDHLabel = found.nama_kegiatan;
      }
    },

    get filteredDaftarHadir() {
      if (!this.searchDHQuery.trim()) return this.rawDaftarHadirList;
      const q = this.searchDHQuery.toLowerCase();
      return this.rawDaftarHadirList.filter(item => 
        (item.nama_kegiatan && item.nama_kegiatan.toLowerCase().includes(q)) ||
        (item.hari_tanggal && item.hari_tanggal.toLowerCase().includes(q)) ||
        (item.tempat && item.tempat.toLowerCase().includes(q))
      );
    },

    selectDHItem(item) {
      this.selectedDaftarHadirId = item.id;
      this.selectedDHLabel = item.nama_kegiatan;
      this.openDHDropdown = false;
      this.fetchDaftarHadirData();
    },

    fetchDaftarHadirData() {
      if (!this.selectedDaftarHadirId) return;

      fetch(`/sigap-notulensi/api/daftar-hadir/${this.selectedDaftarHadirId}`)
        .then(res => res.json())
        .then(res => {
          if (res.judul) this.formData.judul = res.judul;
          if (res.tempat) this.formData.tempat = res.tempat;
          if (res.hari_tanggal) this.formData.hari_tanggal = res.hari_tanggal;
          if (res.waktu) this.formData.waktu = res.waktu;
          if (res.nomor_surat) this.formData.nomor_surat = res.nomor_surat;
          if (res.pimpinan_nama) {
            this.formData.pimpinan_nama = res.pimpinan_nama;
            this.formData.pimpinan_rapat = res.pimpinan_nama;
          }
          if (res.pimpinan_jabatan) this.formData.pimpinan_jabatan = res.pimpinan_jabatan;
          if (res.pimpinan_pangkat) this.formData.pimpinan_pangkat = res.pimpinan_pangkat;
          if (res.pimpinan_nip) this.formData.pimpinan_nip = res.pimpinan_nip;

          if (res.pesertas && res.pesertas.length > 0) {
            this.pesertas = res.pesertas.map(p => ({
              nama: p.nama,
              instansi: p.instansi,
              gender: p.gender || 'L',
              nip_nohp: p.nip_nohp || '',
              email: p.email || '',
              paraf_image: p.paraf_image || null
            }));
          }

          Swal.fire({
            title: 'Data Berhasil Disinkronkan',
            text: 'Data agenda dan peserta telah diperbarui. Silakan sesuaikan kembali jika diperlukan.',
            icon: 'success',
            confirmButtonColor: '#7a2222',
            timer: 2000
          });
        })
        .catch(err => console.error(err));
    },

    get filteredKinerjaList() {
      return this.rawKinerjaList.filter(k => {
        let matchText = true;
        let matchMonth = true;

        if (this.searchKinerjaText.trim()) {
          const q = this.searchKinerjaText.toLowerCase();
          matchText = (k.title && k.title.toLowerCase().includes(q)) ||
                      (k.description && k.description.toLowerCase().includes(q));
        }

        if (this.searchKinerjaMonth && k.date) {
          matchMonth = k.date.startsWith(this.searchKinerjaMonth);
        }

        return matchText && matchMonth && k.images && k.images.length > 0;
      });
    },

    toggleKinerjaPhoto(path) {
      const idx = this.selectedKinerjaPhotos.indexOf(path);
      if (idx > -1) {
        this.selectedKinerjaPhotos.splice(idx, 1);
      } else {
        this.selectedKinerjaPhotos.push(path);
      }
    },

    isPhotoSelected(path) {
      return this.selectedKinerjaPhotos.includes(path) || this.existingPhotos.includes(path);
    },

    removeKinerjaPhoto(idx) {
      this.selectedKinerjaPhotos.splice(idx, 1);
    },

    removeExistingPhoto(idx) {
      this.existingPhotos.splice(idx, 1);
    },

    totalFotoAktif() {
      return this.existingPhotos.length + this.selectedKinerjaPhotos.length;
    },

    addPesertaRow() {
      this.pesertas.push({
        nama: '',
        instansi: 'BRIDA Kota Makassar',
        gender: 'L',
        no_hp: '',
        email: '',
        paraf_image: null
      });
    },

    removePesertaRow(idx) {
      this.pesertas.splice(idx, 1);
    },

    submitAs(status) {
      if (!this.formData.judul.trim()) {
        this.activeTab = 'notula';
        Swal.fire({
          icon: 'warning',
          title: 'Nama Rapat Diperlukan',
          text: 'Harap isi minimal Nama Acara / Pembahasan Rapat.',
          confirmButtonColor: '#7a2222'
        });
        return;
      }

      document.getElementById('statusField').value = status;
      document.getElementById('formNotulensi').submit();
    }
  }
}

// Canvas Tanda Tangan Digital
function signatureCanvas(initialSignatureUrl) {
  return {
    savedSignatureUrl: initialSignatureUrl,
    hasSavedSignature: !!initialSignatureUrl,
    isRedrawing: false,
    hasDrawn: false,
    currentBase64: initialSignatureUrl || '',
    isDrawing: false,
    ctx: null,

    init() {
      const canvas = this.$refs.sigCanvas;
      if (canvas) {
        canvas.width = canvas.parentElement.offsetWidth || 320;
        canvas.height = canvas.parentElement.offsetHeight || 144;
        this.ctx = canvas.getContext('2d');
        this.ctx.lineWidth = 2.5;
        this.ctx.lineCap = 'round';
        this.ctx.lineJoin = 'round';
        this.ctx.strokeStyle = '#0f172a';
      }
    },

    startRedraw() {
      this.isRedrawing = true;
      this.hasDrawn = false;
      this.$nextTick(() => this.clearSig());
    },

    cancelRedraw() {
      this.isRedrawing = false;
      this.currentBase64 = this.savedSignatureUrl;
    },

    clearSig() {
      const canvas = this.$refs.sigCanvas;
      if (this.ctx && canvas) {
        this.ctx.clearRect(0, 0, canvas.width, canvas.height);
        this.hasDrawn = false;
        if (!this.hasSavedSignature || this.isRedrawing) {
          this.currentBase64 = '';
        }
      }
    },

    getPos(e, canvas) {
      const rect = canvas.getBoundingClientRect();
      return { x: e.clientX - rect.left, y: e.clientY - rect.top };
    },

    startDraw(e) {
      this.isDrawing = true;
      this.hasDrawn = true;
      const pos = this.getPos(e, this.$refs.sigCanvas);
      this.ctx.beginPath();
      this.ctx.moveTo(pos.x, pos.y);
    },

    draw(e) {
      if (!this.isDrawing) return;
      const pos = this.getPos(e, this.$refs.sigCanvas);
      this.ctx.lineTo(pos.x, pos.y);
      this.ctx.stroke();
      this.saveState();
    },

    endDraw() {
      if (this.isDrawing) {
        this.isDrawing = false;
        this.saveState();
      }
    },

    startTouch(e) { this.startDraw(e.touches[0]); },
    drawTouch(e) { this.draw(e.touches[0]); },

    saveState() {
      const canvas = this.$refs.sigCanvas;
      this.currentBase64 = canvas.toDataURL('image/png');
    }
  }
}
</script>
@endpush