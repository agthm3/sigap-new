@extends('layouts.app')

@section('title', 'Detail Pegawai — SIGAP BRIDA')

@section('content')
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('sigap-pegawai.index') }}" class="hover:text-maroon font-medium transition-colors">SIGAP Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">Detail: {{ $user->name }}</li>
    </ol>
  </nav>

  <section class="max-w-7xl mx-auto px-4 mt-2 mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white border border-gray-200 p-6 rounded-2xl shadow-sm">
      <div class="flex items-center gap-5">
        <div class="w-20 h-20 shrink-0 rounded-full border-2 border-maroon/20 shadow-md overflow-hidden bg-white">
          <img class="w-full h-full object-cover" src="{{ $user->profile_photo_path ? asset('storage/'.$user->profile_photo_path) : asset('images/avatar-placeholder.png') }}" alt="Avatar">
        </div>
        <div>
          <h1 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h1>
          <div class="flex flex-wrap items-center gap-2 mt-1.5 text-sm">
             <span class="text-gray-600 font-medium">NIP: {{ $user->nip ?: 'Belum diatur' }}</span>
             <span class="text-gray-300">•</span>
             <span class="text-gray-600 font-medium">Unit: {{ $user->unit ?: '—' }}</span>
             <span class="text-gray-300">•</span>
             <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $user->status==='active' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                {{ $user->status==='active' ? 'Aktif' : 'Nonaktif' }}
             </span>
          </div>
        </div>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('sigap-pegawai.index') }}" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold shadow-sm transition-all">Kembali</a>
        @hasanyrole('admin|superadmin')
            <a href="{{ route('sigap-pegawai.edit', $user->id) }}" class="px-4 py-2 rounded-xl bg-maroon text-white hover:bg-maroon-800 text-sm font-semibold shadow-sm transition-all">Edit Pegawai</a>
        @endhasanyrole
      </div>
    </div>
  </section>

  @php $profile = $user->profile; @endphp

  <section class="max-w-7xl mx-auto px-4 mb-8">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        
        {{-- TAB NAVIGASI --}}
        <div class="border-b px-6 pt-4 bg-gray-50/50">
            <nav class="flex gap-8 text-sm font-semibold overflow-x-auto whitespace-nowrap scrollbar-hide">
                <button class="tab-btn border-b-2 border-maroon text-maroon pb-3" data-tab="akun">Info Akun</button>
                <button class="tab-btn pb-3 text-gray-500 hover:text-gray-800" data-tab="identitas">Identitas</button>
                <button class="tab-btn pb-3 text-gray-500 hover:text-gray-800" data-tab="kepegawaian">Kepegawaian</button>
                <button class="tab-btn pb-3 text-gray-500 hover:text-gray-800" data-tab="alamat">Alamat & Bank</button>
                <button class="tab-btn pb-3 text-gray-500 hover:text-gray-800" data-tab="keluarga">Keluarga</button>
                <button class="tab-btn pb-3 text-gray-500 hover:text-gray-800" data-tab="pendidikan">Pendidikan</button>
                <button class="tab-btn pb-3 text-gray-500 hover:text-gray-800" data-tab="sertifikat">Sertifikat</button>
            </nav>
        </div>

        <div class="p-6">
            
            {{-- TAB: AKUN --}}
            <div id="tab-akun" class="tab-content">
                <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                    <div><span class="block text-gray-500 text-xs mb-1">Username</span><span class="font-medium text-gray-900">{{ $user->username }}</span></div>
                    <div><span class="block text-gray-500 text-xs mb-1">Email</span><span class="font-medium text-gray-900">{{ $user->email }}</span></div>
                    <div><span class="block text-gray-500 text-xs mb-1">Nomor HP</span><span class="font-medium text-gray-900">{{ $user->nomor_hp ?: '—' }}</span></div>
                    <div class="sm:col-span-2">
                        <span class="block text-gray-500 text-xs mb-1">Roles</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @forelse($userRoleNames as $role)
                                <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200 text-xs font-semibold text-gray-700">{{ $role }}</span>
                            @empty
                                <span class="text-gray-400">—</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @if($profile)
                {{-- TAB: IDENTITAS --}}
                <div id="tab-identitas" class="tab-content hidden">
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                        @include('partials.field',['label'=>'NIK','value'=>$profile->nik])
                        @include('partials.field',['label'=>'Tempat Lahir','value'=>$profile->tempat_lahir])
                        @include('partials.field',['label'=>'Tanggal Lahir','value'=>$profile->tanggal_lahir])
                        @include('partials.field',['label'=>'Jenis Kelamin','value'=>$profile->jenis_kelamin])
                        @include('partials.field',['label'=>'Agama','value'=>$profile->agama])
                        @include('partials.field',['label'=>'Status Perkawinan','value'=>$profile->status_perkawinan])
                        @include('partials.field',['label'=>'Golongan Darah','value'=>$profile->golongan_darah])
                        @include('partials.field',['label'=>'NIP Baru','value'=>$profile->nip_baru])
                        @include('partials.field',['label'=>'NIP Lama','value'=>$profile->nip_lama])
                        @include('partials.field',['label'=>'Keterangan','value'=>$profile->keterangan,'class'=>'sm:col-span-2 md:col-span-3'])
                    </div>
                </div>

                {{-- TAB: KEPEGAWAIAN --}}
                <div id="tab-kepegawaian" class="tab-content hidden">
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
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

                {{-- TAB: ALAMAT & BANK --}}
                <div id="tab-alamat" class="tab-content hidden">
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                        @include('partials.field',['label'=>'Alamat KTP','value'=>$profile->alamat_ktp,'class'=>'sm:col-span-2 md:col-span-3'])
                        @include('partials.field',['label'=>'Alamat Domisili','value'=>$profile->alamat_domisili,'class'=>'sm:col-span-2 md:col-span-3'])
                        @include('partials.field',['label'=>'NPWP','value'=>$profile->npwp])
                        @include('partials.field',['label'=>'BPJS Kesehatan','value'=>$profile->bpjs_kesehatan])
                        @include('partials.field',['label'=>'BPJS Ketenagakerjaan','value'=>$profile->bpjs_ketenagakerjaan])
                        @include('partials.field',['label'=>'Bank','value'=>$profile->bank_nama])
                        @include('partials.field',['label'=>'Nomor Rekening','value'=>$profile->nomor_rekening])
                        @include('partials.field',['label'=>'Atas Nama Rekening','value'=>$profile->nama_rekening])
                    </div>
                </div>

                {{-- TAB: KELUARGA --}}
                <div id="tab-keluarga" class="tab-content hidden">
                    <div class="grid sm:grid-cols-2 gap-6 text-sm">
                        @include('partials.field',['label'=>'Nama Pasangan','value'=>$profile->nama_pasangan])
                        @include('partials.field',['label'=>'Pekerjaan Pasangan','value'=>$profile->pekerjaan_pasangan])
                        @include('partials.field',['label'=>'Jumlah Anak','value'=>$profile->jumlah_anak])
                        @include('partials.field',['label'=>'Kontak Darurat','value'=>$profile->kontak_darurat])
                    </div>
                </div>

                {{-- TAB: PENDIDIKAN --}}
                <div id="tab-pendidikan" class="tab-content hidden">
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                        @include('partials.field',['label'=>'Pendidikan Terakhir','value'=>$profile->pendidikan_terakhir])
                        @include('partials.field',['label'=>'Jurusan','value'=>$profile->jurusan])
                        @include('partials.field',['label'=>'Tahun Lulus','value'=>$profile->tahun_lulus])
                    </div>
                </div>
            @else
                <div id="tab-identitas" class="tab-content hidden text-center py-6 text-gray-500 text-sm">Profil belum dilengkapi.</div>
                <div id="tab-kepegawaian" class="tab-content hidden text-center py-6 text-gray-500 text-sm">Profil belum dilengkapi.</div>
                <div id="tab-alamat" class="tab-content hidden text-center py-6 text-gray-500 text-sm">Profil belum dilengkapi.</div>
                <div id="tab-keluarga" class="tab-content hidden text-center py-6 text-gray-500 text-sm">Profil belum dilengkapi.</div>
                <div id="tab-pendidikan" class="tab-content hidden text-center py-6 text-gray-500 text-sm">Profil belum dilengkapi.</div>
            @endif

            {{-- TAB: SERTIFIKAT --}}
            <div id="tab-sertifikat" class="tab-content hidden">
                <div class="overflow-x-auto border rounded-xl">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama Sertifikat</th>
                                <th class="px-5 py-3 font-semibold">Bidang</th>
                                <th class="px-5 py-3 font-semibold">Tahun</th>
                                <th class="px-5 py-3 font-semibold text-right">File</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($user->kompetensis as $s)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $s->nama_sertifikat }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $s->bidang_sertifikat ?: '—' }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $s->tahun_sertifikat ?: '—' }}</td>
                                    <td class="px-5 py-3 text-right">
                                        @if($s->file_path)
                                            <a href="{{ asset('storage/'.$s->file_path) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 font-medium text-xs transition-colors">Lihat File</a>
                                        @else
                                            <span class="text-xs text-gray-400">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-gray-500 text-sm">Belum ada sertifikat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  </section>

  {{-- Arsip Dokumen Pribadi --}}
  <section class="max-w-7xl mx-auto px-4 mb-12">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
      <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-base font-bold text-gray-900">Arsip Dokumen Pribadi</h3>
        <p class="text-xs text-gray-500 mt-1">Dokumen privat (KTP, KK, Rekening, dll) dilindungi enkripsi. Akses memerlukan PIN dari pemilik.</p>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
          <thead class="bg-white border-b border-gray-100 text-gray-500">
            <tr>
              <th class="px-6 py-3 font-semibold">Jenis Berkas</th>
              <th class="px-6 py-3 font-semibold">Nama Berkas</th>
              <th class="px-6 py-3 font-semibold">Status Keamanan</th>
              <th class="px-6 py-3 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($docs as $doc)
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-6 py-4 font-bold text-gray-900 uppercase text-xs">{{ $doc->type }}</td>
                <td class="px-6 py-4 text-gray-600 max-w-xs truncate">{{ $doc->title }}</td>
                <td class="px-6 py-4">
                  @if($doc->access_code_hash)
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                      Terkunci PIN
                    </span>
                  @else
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-md border border-rose-200">
                      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                      Privat Tertutup
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="{{ route('pegawai.docs.show', $doc->id) }}" class="px-4 py-2 rounded-lg border border-gray-200 text-maroon hover:bg-maroon/5 font-semibold text-xs shadow-sm transition-colors">
                    Lihat Dokumen
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500 text-sm">
                  Pegawai ini belum mengunggah dokumen pribadi apapun.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  {{-- Script untuk Tabulasi --}}
  <script>
    document.querySelectorAll(".tab-btn").forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll(".tab-btn").forEach(b => {
                b.classList.remove("border-maroon", "text-maroon");
                b.classList.add("text-gray-500");
            });
            document.querySelectorAll(".tab-content").forEach(c => {
                c.classList.add("hidden");
            });
            btn.classList.add("border-maroon", "text-maroon");
            btn.classList.remove("text-gray-500");
            document.getElementById("tab-" + btn.dataset.tab).classList.remove("hidden");
        });
    });
  </script>
@endsection