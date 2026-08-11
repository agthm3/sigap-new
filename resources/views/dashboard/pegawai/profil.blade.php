@extends('layouts.app')

@section('title', 'Profil Pegawai — SIGAP BRIDA')

@section('content')
  {{-- Breadcrumb --}}
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('home.index') }}" class="hover:text-maroon">Dashboard</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Profil Pegawai</li>
    </ol>
  </nav>
{{-- ================= PROFIL PEGAWAI TAB ================= --}}
@if($user->profile)

<section class="max-w-7xl mx-auto px-4 mt-6">

    @php
        $profile = $user->profile;
    @endphp

    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">

        {{-- ================= HEADER PROFIL ================= --}}
        <div class="p-6 border-b">

            <div class="flex items-center gap-4">

                {{-- Foto --}}
                <div class="relative w-20 h-20 shrink-0">

                    <div class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-maroon/20">

                        @if ($user->profile_photo_path)
                            <img
                                src="{{ asset('storage/'.$user->profile_photo_path) }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold">
                                {{ strtoupper(substr($user->name,0,1)) }}
                            </div>
                        @endif

                    </div>

                    <!-- Upload Form -->
                    <form
                        action="{{ route('pegawai.profil.avatar') }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="absolute -bottom-2 -right-2"
                    >
                        @csrf

                        <label class="cursor-pointer">

                            <input
                                type="file"
                                name="photo"
                                class="hidden"
                                onchange="this.form.submit()"
                            >

                            <div class="w-8 h-8 rounded-full bg-maroon text-white flex items-center justify-center shadow hover:bg-maroon-800">
                                ✎
                            </div>

                        </label>

                    </form>

                </div>

                

                <div>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ $user->name }}
                    </h2>

                    <div class="flex flex-wrap gap-2 mt-2 text-xs">

                        @if($user->nip)
                            <span class="px-2 py-1 bg-gray-100 rounded">
                                NIP: {{ $user->nip }}
                            </span>
                        @endif

                        @if($user->unit)
                            <span class="px-2 py-1 bg-gray-100 rounded">
                                Unit: {{ $user->unit }}
                            </span>
                        @endif

                        @if($profile->jabatan)
                            <span class="px-2 py-1 bg-maroon/10 text-maroon rounded">
                                {{ $profile->jabatan }}
                            </span>
                        @endif

                    </div>
                </div>

                 {{-- TOMBOL EDIT --}}
                  <div class="shrink-0">
                      <a
                          href="{{ route('pegawai.profil.edit') }}"
                          class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm shadow"
                      >
                          Edit Profil
                      </a>
                  </div>
            </div>

            
        </div>


        {{-- ================= TAB NAV ================= --}}
        <div class="border-b px-6">

            <nav class="flex gap-8 text-sm font-semibold">

                <button class="tab-btn border-b-2 border-maroon pb-3" data-tab="identitas">
                    Identitas
                </button>

                <button class="tab-btn pb-3" data-tab="kepegawaian">
                    Kepegawaian
                </button>

                <button class="tab-btn pb-3" data-tab="alamat">
                    Alamat & Administrasi
                </button>

                <button class="tab-btn pb-3" data-tab="keluarga">
                    Keluarga
                </button>

                <button class="tab-btn pb-3" data-tab="pendidikan">
                    Pendidikan
                </button>

                <button class="tab-btn pb-3" data-tab="sertifikat">
                    Sertifikat
                </button>

            </nav>

        </div>


        {{-- ================= TAB CONTENT ================= --}}
        <div class="p-6">

            {{-- IDENTITAS --}}
            <div id="tab-identitas" class="tab-content">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                    @include('partials.field',['label'=>'NIK','value'=>$profile->nik])
                    @include('partials.field',['label'=>'Tempat Lahir','value'=>$profile->tempat_lahir])
                    @include('partials.field',['label'=>'Tanggal Lahir','value'=>$profile->tanggal_lahir])
                    @include('partials.field',['label'=>'Jenis Kelamin','value'=>$profile->jenis_kelamin])
                    @include('partials.field',['label'=>'Agama','value'=>$profile->agama])
                    @include('partials.field',['label'=>'Status Perkawinan','value'=>$profile->status_perkawinan])
                    @include('partials.field',['label'=>'Golongan Darah','value'=>$profile->golongan_darah])
                    @include('partials.field',['label'=>'NIP Baru','value'=>$profile->nip_baru])
                    @include('partials.field',['label'=>'NIP Lama','value'=>$profile->nip_lama])
                    @include('partials.field',['label'=>'Keterangan','value'=>$profile->keterangan,'class'=>'sm:col-span-2'])

                </div>
            </div>


            {{-- KEPEGAWAIAN --}}
            <div id="tab-kepegawaian" class="tab-content hidden">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                    @include('partials.field',['label'=>'Status Pegawai','value'=>$profile->status_pegawai])
                    @include('partials.field',['label'=>'Jabatan','value'=>$profile->jabatan])
                    @include('partials.field',['label'=>'Golongan','value'=>$profile->golongan])
                    @include('partials.field',['label'=>'TMT PNS','value'=>$profile->tmt_pns])
                    @include('partials.field',['label'=>'Atasan Langsung','value'=>$profile->atasan_langsung])
                    @include('partials.field',['label'=>'Golongan Ruang','value'=>$profile->golongan_ruang])
                    @include('partials.field',['label'=>'TMT Golongan','value'=>$profile->tmt_golongan])
                    @include('partials.field',['label'=>'Masa Kerja','value'=>$profile->masa_kerja_tahun.' Tahun '.$profile->masa_kerja_bulan.' Bulan'])
                    @include('partials.field',['label'=>'TMT Jabatan','value'=>$profile->tmt_jabatan])
                    @include('partials.field',['label'=>'Eselon','value'=>$profile->eselon])
                    @include('partials.field',['label'=>'Jabatan Struktural','value'=>$profile->jabatan_struktural])
                    @include('partials.field',['label'=>'Jabatan Fungsional','value'=>$profile->jabatan_fungsional])
                    @include('partials.field',['label'=>'Jabatan Teknis','value'=>$profile->jabatan_teknis])
                    @include('partials.field',['label'=>'Unit Organisasi','value'=>$profile->unor])

                </div>
            </div>


            {{-- ALAMAT --}}
            <div id="tab-alamat" class="tab-content hidden">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                    @include('partials.field',['label'=>'Alamat KTP','value'=>$profile->alamat_ktp,'class'=>'sm:col-span-2'])
                    @include('partials.field',['label'=>'Alamat Domisili','value'=>$profile->alamat_domisili,'class'=>'sm:col-span-2'])
                    @include('partials.field',['label'=>'NPWP','value'=>$profile->npwp])
                    @include('partials.field',['label'=>'BPJS Kesehatan','value'=>$profile->bpjs_kesehatan])
                    @include('partials.field',['label'=>'BPJS Ketenagakerjaan','value'=>$profile->bpjs_ketenagakerjaan])
                    @include('partials.field',['label'=>'Bank','value'=>$profile->bank_nama])
                    @include('partials.field',['label'=>'Nomor Rekening','value'=>$profile->nomor_rekening])
                    @include('partials.field',['label'=>'Atas Nama Rekening','value'=>$profile->nama_rekening])

                </div>
            </div>


            {{-- KELUARGA --}}
            <div id="tab-keluarga" class="tab-content hidden">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                    @include('partials.field',['label'=>'Nama Pasangan','value'=>$profile->nama_pasangan])
                    @include('partials.field',['label'=>'Pekerjaan Pasangan','value'=>$profile->pekerjaan_pasangan])
                    @include('partials.field',['label'=>'Jumlah Anak','value'=>$profile->jumlah_anak])
                    @include('partials.field',['label'=>'Kontak Darurat','value'=>$profile->kontak_darurat])

                </div>
            </div>


            {{-- PENDIDIKAN --}}
            <div id="tab-pendidikan" class="tab-content hidden">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                    @include('partials.field',['label'=>'Pendidikan Terakhir','value'=>$profile->pendidikan_terakhir])
                    @include('partials.field',['label'=>'Jurusan','value'=>$profile->jurusan])
                    @include('partials.field',['label'=>'Tahun Lulus','value'=>$profile->tahun_lulus])

                </div>
            </div>


            <div id="tab-sertifikat" class="tab-content hidden">

                <div class="border border-gray-200 rounded-xl overflow-hidden">

                    <div class="px-5 py-3 bg-gray-50 text-sm font-semibold text-gray-700">
                        Sertifikat Kompetensi
                    </div>

                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm">

                            <thead class="bg-white">
                                <tr class="text-left border-b">
                                    <th class="px-5 py-3">Nama Sertifikat</th>
                                    <th class="px-5 py-3">Bidang</th>
                                    <th class="px-5 py-3">Tahun</th>
                                    <th class="px-5 py-3">File</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">

                                @forelse($sertifikats as $s)

                                    <tr>

                                        <td class="px-5 py-3 font-medium text-gray-900">
                                            {{ $s->nama_sertifikat }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-700">
                                            {{ $s->bidang }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-700">
                                            {{ $s->tahun }}
                                        </td>

                                        <td class="px-5 py-3">

                                            @if($s->file_path)
                                                <a
                                                    href="{{ asset('storage/'.$s->file_path) }}"
                                                    target="_blank"
                                                    class="px-3 py-1.5 rounded-md border hover:bg-gray-50 text-sm"
                                                >
                                                    Lihat
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">
                                                    Tidak ada file
                                                </span>
                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-gray-500">
                                            Belum ada sertifikat yang ditambahkan.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>

    </div>

</section>

@endif

  {{-- Berkas Saya --}}
{{-- ================= BERKAS PRIBADI (PRIVATE) ================= --}}
  <section class="max-w-7xl mx-auto px-4 mt-8 mb-12">
    <div x-data="{ openUpload: false }" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
      
      {{-- Header --}}
      <div class="px-6 py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 bg-gray-50/30">
        <div>
          <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            Berkas Pribadi
          </h3>
          <p class="text-sm text-gray-500 mt-1">
            Unggah dokumen sensitif seperti KTP, KK, dan NPWP. File Anda disimpan secara <span class="font-semibold text-gray-700">Privat & Tertutup</span> dari publik.
          </p>
        </div>
        <div class="shrink-0">
          <button @click="openUpload = true" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-maroon text-white hover:bg-maroon-800 text-sm font-semibold shadow-sm transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Unggah Berkas Baru
          </button>
        </div>
      </div>

      {{-- MODAL UNGGAH BERKAS (Alpine.js Teleport) --}}
      <template x-teleport="body">
        <div x-show="openUpload" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
          
          <!-- Backdrop -->
          <div x-show="openUpload" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
               class="absolute inset-0 bg-black/50 backdrop-blur-sm" 
               @click="openUpload = false"></div>

          <!-- Modal Panel -->
          <div x-show="openUpload"
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="opacity-0 translate-y-8 scale-95"
               x-transition:enter-end="opacity-100 translate-y-0 scale-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="opacity-100 translate-y-0 scale-100"
               x-transition:leave-end="opacity-0 translate-y-8 scale-95"
               class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
              <div>
                <h4 class="font-bold text-gray-900 text-lg">Unggah Dokumen Baru</h4>
                <p class="text-xs text-gray-500 mt-0.5">Maksimal 4MB. Format: PDF, JPG, PNG.</p>
              </div>
              <button @click="openUpload = false" class="text-gray-400 hover:text-gray-700 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
              </button>
            </div>

            <form action="{{ route('pegawai.docs.storeSelf') }}" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-6 space-y-5">
              @csrf
              
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Dokumen <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-maroon focus:ring-maroon bg-gray-50">
                  <option value="" disabled selected>-- Pilih Jenis Berkas --</option>
                  <option value="ktp">Kartu Tanda Penduduk (KTP)</option>
                  <option value="kk">Kartu Keluarga (KK)</option>
                  <option value="npwp">NPWP</option>
                  <option value="bpjs">BPJS Kesehatan / Ketenagakerjaan</option>
                  <option value="ijazah">Ijazah</option>
                  <option value="sk">SK Pegawai</option>
                  <option value="buku_rekening">Buku Rekening</option>
                  <option value="other">Lainnya</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan / Nama Dokumen <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: KTP a.n. {{ $user->name }}"
                       class="w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-maroon focus:ring-maroon">
              </div>

              {{-- PENGAMANAN EKSTRA (PIN) --}}
              <div class="p-4 bg-amber-50/50 border border-amber-200/60 rounded-xl space-y-4">
                <div class="flex gap-2 text-amber-800 items-center">
                  <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                  <span class="font-semibold text-sm">Kode Akses / PIN (Opsional)</span>
                </div>
                <p class="text-[11px] text-amber-700/80 leading-relaxed -mt-2">
                  Tambahkan lapisan keamanan ganda. Jika diatur, Admin/Verifikator wajib memasukkan PIN ini untuk bisa membuka file Anda.
                </p>
                
                <div class="grid sm:grid-cols-2 gap-4">
                  <div>
                    <input type="password" name="access_code"
                           class="w-full rounded-lg border-amber-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500 placeholder-gray-400"
                           placeholder="Buat PIN (Min. 4 char)">
                  </div>
                  <div>
                    <input type="text" name="access_code_hint" maxlength="100"
                           class="w-full rounded-lg border-amber-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500 placeholder-gray-400"
                           placeholder="Petunjuk PIN (Contoh: NIK)">
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">File Berkas <span class="text-red-500">*</span></label>
                <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-maroon/10 file:text-maroon hover:file:bg-maroon/20 border border-gray-200 rounded-xl cursor-pointer">
              </div>

              <div class="pt-4 flex items-center justify-end gap-3">
                <button type="button" @click="openUpload = false" class="px-5 py-2.5 rounded-xl font-semibold text-gray-600 hover:bg-gray-100 text-sm transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-maroon text-white hover:bg-maroon-800 font-semibold text-sm shadow-sm transition-colors">Simpan Dokumen</button>
              </div>
            </form>
          </div>
        </div>
      </template>

      {{-- TABLE DOKUMEN --}}
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-gray-50 border-b border-gray-100 text-gray-600">
            <tr>
              <th class="px-6 py-4 font-semibold whitespace-nowrap">Jenis</th>
              <th class="px-6 py-4 font-semibold">Nama File</th>
              <th class="px-6 py-4 font-semibold">Status</th>
              <th class="px-6 py-4 font-semibold whitespace-nowrap">Diupload Pada</th>
              <th class="px-6 py-4 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($docs as $d)
              <tr x-data="{ openCfg: false }" class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-6 py-4 font-medium text-gray-900">{{ $d['label'] }}</td>
                <td class="px-6 py-4 text-gray-600 truncate max-w-[200px]" title="{{ $d['filename'] }}">{{ $d['filename'] }}</td>
                <td class="px-6 py-4">
                  @if($d['status'] === 'Terverifikasi')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ $d['status'] }}
                    </span>
                  @elseif($d['status'] === 'Menunggu verifikasi')
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">
                      <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>{{ $d['status'] }}
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $d['uploaded_at'] }}</td>
                <td class="px-6 py-4 text-right">
                  
                  {{-- Tombol Aksi Kanan --}}
                  <div class="flex items-center justify-end gap-2 relative">
                    <a href="{{ route('pegawai.docs.show', $d['id']) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 font-medium transition-colors">Detail</a>
                    <a href="{{ route('pegawai.docs.download', $d['id']) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-maroon hover:bg-maroon/5 font-medium transition-colors">Unduh</a>
                    
                    {{-- Tombol Setting PIN --}}
                    <button type="button" @click="openCfg = !openCfg" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors" title="Pengaturan Keamanan">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </button>

                    {{-- Popover Ganti PIN (Tampil saat icon gembok di klik) --}}
                    <div x-show="openCfg" x-cloak @click.away="openCfg = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute right-0 top-10 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 z-50 text-left p-4">
                         
                      <h5 class="text-sm font-bold text-gray-900 mb-3">Keamanan Dokumen</h5>
                      <form action="{{ route('pegawai.docs.access.set', $d['id']) }}" method="POST" class="space-y-3" onsubmit="return confirm('Simpan perubahan PIN?')">
                        @csrf
                        <input type="password" name="access_code" required minlength="4" maxlength="50" class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-maroon focus:ring-maroon" placeholder="PIN Baru"/>
                        <input type="password" name="access_code_confirmation" required minlength="4" maxlength="50" class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-maroon focus:ring-maroon" placeholder="Konfirmasi PIN Baru"/>
                        <input type="text" name="access_code_hint" maxlength="100" class="w-full rounded-lg border-gray-200 px-3 py-2 text-sm focus:border-maroon focus:ring-maroon" placeholder="Hint PIN (Opsional)"/>
                        <button type="submit" class="w-full py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-black">Simpan PIN</button>
                      </form>
                      
                      <hr class="my-3 border-gray-100">
                      
                      <form action="{{ route('pegawai.docs.access.clear', $d['id']) }}" method="POST" onsubmit="return confirm('Hapus PIN perlindungan dokumen ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full py-2 bg-rose-50 text-rose-700 rounded-lg text-sm font-semibold hover:bg-rose-100">Hapus PIN</button>
                      </form>
                    </div>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-6 py-10 text-center">
                  <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                  </div>
                  <p class="text-gray-500 text-sm">Anda belum mengunggah dokumen pribadi apapun.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
    {{-- ================= SCRIPT TAB ================= --}}
<script>
document.querySelectorAll(".tab-btn").forEach(btn => {
    btn.onclick = () => {

        document.querySelectorAll(".tab-btn").forEach(b => {
            b.classList.remove("border-maroon");
        });

        document.querySelectorAll(".tab-content").forEach(c => {
            c.classList.add("hidden");
        });

        btn.classList.add("border-maroon");
        document.getElementById("tab-" + btn.dataset.tab).classList.remove("hidden");
    };
});
</script>
@endpush
