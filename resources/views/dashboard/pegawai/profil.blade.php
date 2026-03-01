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
  <section class="max-w-7xl mx-auto px-4 mt-6">
<div x-data="{ open:false }" class="px-5 py-4 flex items-center justify-between">
  <div>
    <h3 class="font-semibold text-gray-800">Berkas Saya</h3>
    <p class="text-sm text-gray-500">KTP, KK, dan berkas lain yang terkait akun ini.</p>
  </div>
  <div class="flex items-center gap-2">
    <button @click="open=true" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
      Unggah Berkas
    </button>
  </div>

  {{-- Modal --}}
  <div x-show="open" x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
        <div @click.away="open=false"
            class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-gray-200">
          <div class="px-5 py-4 border-b">
            <h4 class="font-semibold text-gray-800">Unggah Dokumen Pribadi</h4>
            <p class="text-xs text-gray-500">File disimpan privat. Maks 4MB, PDF/JPG/PNG.</p>
          </div>
          <form action="{{ route('pegawai.docs.storeSelf') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            <label class="block">
              <span class="text-sm font-medium text-gray-700">Jenis Dokumen</span>
              <select name="type" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
                <option value="ktp">KTP</option>
                <option value="kk">Kartu Keluarga (KK)</option>
                <option value="npwp">NPWP</option>
                <option value="bpjs">BPJS</option>
                <option value="ijazah">Ijazah</option>
                <option value="sk">SK</option>
                <option value="other">Lainnya</option>
              </select>
            </label>
            <label class="block">
            <span class="text-sm font-medium text-gray-700">Kode Akses (opsional)</span>
            <input type="password" name="access_code"
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                  placeholder="Contoh: SIGAP-1234 (min 4 karakter)">
            <p class="text-[11px] text-gray-500 mt-1">Jika diisi, siapapun yang ingin mengakses harus memasukkan kode ini.</p>
          </label>

          <label class="block">
            <span class="text-sm font-medium text-gray-700">Hint Kode (opsional)</span>
            <input type="text" name="access_code_hint" maxlength="100"
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon"
                  placeholder="Contoh: 4 huruf terakhir NIK">
          </label>

            <label class="block">
              <span class="text-sm font-medium text-gray-700">Judul / Keterangan</span>
              <input type="text" name="title" required placeholder="contoh: KTP a.n. {{ $user->name }}"
                    class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>

            <label class="block">
              <span class="text-sm font-medium text-gray-700">File</span>
              <input type="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"
                    class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>

            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" @click="open=false" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Batal</button>
              <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Unggah</button>
            </div>
          </form>
        </div>
      </div>
    </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50">
            <tr class="text-left border-y">
              <th class="px-5 py-3">Jenis Berkas</th>
              <th class="px-5 py-3">Nama File (Tergenerate Otomatis)</th>
              <th class="px-5 py-3">Status</th>
              <th class="px-5 py-3">Diunggah</th>
              <th class="px-5 py-3">Aksi</th>
            </tr>
          </thead>
  <tbody class="divide-y">
  @forelse($docs as $d)
    <tr x-data="{ openCfg:false }">
      <td class="px-5 py-3">{{ $d['label'] }}</td>
      <td class="px-5 py-3">{{ $d['filename'] }}</td>
      <td class="px-5 py-3">
        @if($d['status']==='Terverifikasi')
          <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">{{ $d['status'] }}</span>
        @elseif($d['status']==='Menunggu verifikasi')
          <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">{{ $d['status'] }}</span>
        @else
          <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">{{ $d['status'] }}</span>
        @endif
      </td>
      <td class="px-5 py-3">{{ $d['uploaded_at'] }}</td>
      <td class="px-5 py-3">
        <div class="flex flex-wrap items-center gap-2">
          <a href="{{ route('pegawai.docs.show', $d['id']) }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Lihat</a>
          <a href="{{ route('pegawai.docs.download', $d['id']) }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Unduh</a>

          <!-- Toggle form atur/hapus kode -->
          <button type="button" @click="openCfg=!openCfg"
                  class="px-3 py-1.5 rounded-md border hover:bg-gray-50 text-sm">
            Atur Kode
          </button>
        </div>

        <!-- Panel form (di dalam kolom Aksi) -->
        <div x-show="openCfg" x-cloak class="mt-3 border rounded-lg p-3 space-y-2">
          <form action="{{ route('pegawai.docs.access.set', $d['id']) }}" method="POST"
                class="flex flex-wrap items-center gap-2"
                onsubmit="return confirm('Setel / ganti kode akses untuk dokumen ini?')">
            @csrf
            <input type="password" name="access_code" required minlength="4" maxlength="50"
                   class="w-32 rounded-md border px-2 py-1 text-sm" placeholder="Kode"/>
            <input type="password" name="access_code_confirmation" required minlength="4" maxlength="50"
                   class="w-32 rounded-md border px-2 py-1 text-sm" placeholder="Ulangi"/>
            <input type="text" name="access_code_hint" maxlength="100"
                   class="w-40 rounded-md border px-2 py-1 text-sm" placeholder="Hint (opsional)"/>
            <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50 text-sm">Simpan</button>
          </form>

          <form action="{{ route('pegawai.docs.access.clear', $d['id']) }}" method="POST"
                onsubmit="return confirm('Hapus kode akses untuk dokumen ini?')">
            @csrf @method('DELETE')
            <button class="px-3 py-1.5 rounded-md border text-red-700 border-red-300 hover:bg-red-50 text-sm">
              Hapus Kode
            </button>
          </form>
        </div>
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="5" class="px-5 py-6 text-center text-gray-500">
        Belum ada berkas yang diunggah.
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
