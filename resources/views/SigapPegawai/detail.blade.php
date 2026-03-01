@extends('layouts.page')

@section('content')
  <!-- Breadcrumb -->
  <nav class="max-w-7xl mx-auto px-4 py-4 text-sm">
    <ol class="flex flex-wrap items-center gap-1 text-gray-600">
      <li><a href="{{ route('home.pegawai') }}" class="hover:text-maroon">SIGAP Pegawai</a></li>
      <li>›</li>
      <li class="text-gray-900 font-semibold">{{ $pegawai->name }}</li>
    </ol>
  </nav>

<section class="max-w-7xl mx-auto px-4 pb-6">

    @php
        $profile = $pegawai->profile;

        $photoUrl = $pegawai->profile_photo_path
            ? asset('storage/'.$pegawai->profile_photo_path)
            : 'https://placehold.co/120x120?text=SP';
    @endphp

    <div class="rounded-2xl border border-gray-200 p-6">

        <!-- ================= IDENTITAS UTAMA ================= -->
        <div class="flex flex-col md:flex-row md:items-center gap-6">

            <img
                src="{{ $photoUrl }}"
                class="w-28 h-28 rounded-xl object-cover ring-2 ring-maroon/20"
                alt="Foto Pegawai"
            >

            <div class="flex-1">

                <h1 class="text-2xl font-extrabold text-gray-900">
                    {{ $pegawai->name }}
                </h1>

                <div class="flex flex-wrap gap-2 mt-2 text-xs">

                    @if($pegawai->nip)
                        <span class="px-2 py-1 bg-gray-100 rounded">
                            NIP: {{ $pegawai->nip }}
                        </span>
                    @endif

                    @if($pegawai->unit)
                        <span class="px-2 py-1 bg-gray-100 rounded">
                            Unit: {{ $pegawai->unit }}
                        </span>
                    @endif

                    @if($profile?->jabatan)
                        <span class="px-2 py-1 bg-maroon/10 text-maroon rounded">
                            {{ $profile->jabatan }}
                        </span>
                    @endif

                    @if($profile?->status_pegawai)
                        <span class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded">
                            {{ $profile->status_pegawai }}
                        </span>
                    @endif

                </div>

            </div>

        </div>

        @if($profile)

            <div class="mt-8 space-y-6">

                <!-- ================= IDENTITAS ================= -->
                <div class="border rounded-xl overflow-hidden">

                    <div class="px-5 py-3 font-semibold bg-gray-50">
                        Identitas
                    </div>

                    <div class="p-5 grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                        @include('partials.field', ['label'=>'NIK','value'=>$profile->nik])
                        @include('partials.field', ['label'=>'Tempat Lahir','value'=>$profile->tempat_lahir])
                        @include('partials.field', ['label'=>'Tanggal Lahir','value'=>$profile->tanggal_lahir])
                        @include('partials.field', ['label'=>'Jenis Kelamin','value'=>$profile->jenis_kelamin])
                        @include('partials.field', ['label'=>'Agama','value'=>$profile->agama])
                        @include('partials.field', ['label'=>'Status Perkawinan','value'=>$profile->status_perkawinan])
                        @include('partials.field', ['label'=>'Golongan Darah','value'=>$profile->golongan_darah])
                        @include('partials.field', ['label'=>'NIP Baru','value'=>$profile->nip_baru])
                        @include('partials.field', ['label'=>'NIP Lama','value'=>$profile->nip_lama])
                        @include('partials.field', ['label'=>'Keterangan','value'=>$profile->keterangan,'class'=>'sm:col-span-2'])

                    </div>

                </div>


                <!-- ================= KEPEGAWAIAN ================= -->
                <div class="border rounded-xl overflow-hidden">

                    <div class="px-5 py-3 font-semibold bg-gray-50">
                        Kepegawaian
                    </div>

                    <div class="p-5 grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                        @include('partials.field', ['label'=>'Status Pegawai','value'=>$profile->status_pegawai])
                        @include('partials.field', ['label'=>'Jabatan','value'=>$profile->jabatan])
                        @include('partials.field', ['label'=>'Golongan','value'=>$profile->golongan])
                        @include('partials.field', ['label'=>'TMT PNS','value'=>$profile->tmt_pns])
                        @include('partials.field', ['label'=>'Atasan Langsung','value'=>$profile->atasan_langsung])
                        @include('partials.field', ['label'=>'Golongan Ruang','value'=>$profile->golongan_ruang])
                        @include('partials.field', ['label'=>'TMT Golongan','value'=>$profile->tmt_golongan])
                        @include('partials.field', ['label'=>'Masa Kerja','value'=>$profile->masa_kerja_tahun.' Tahun '.$profile->masa_kerja_bulan.' Bulan'])
                        @include('partials.field', ['label'=>'TMT Jabatan','value'=>$profile->tmt_jabatan])
                        @include('partials.field', ['label'=>'Eselon','value'=>$profile->eselon])
                        @include('partials.field', ['label'=>'Jabatan Struktural','value'=>$profile->jabatan_struktural])
                        @include('partials.field', ['label'=>'Jabatan Fungsional','value'=>$profile->jabatan_fungsional])
                        @include('partials.field', ['label'=>'Jabatan Teknis','value'=>$profile->jabatan_teknis])
                        @include('partials.field', ['label'=>'Unit Organisasi','value'=>$profile->unor])

                    </div>

                </div>


                <!-- ================= ALAMAT & ADMINISTRASI ================= -->
                <div class="border rounded-xl overflow-hidden">

                    <div class="px-5 py-3 font-semibold bg-gray-50">
                        Alamat & Administrasi
                    </div>

                    <div class="p-5 grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                        @include('partials.field', ['label'=>'Alamat KTP','value'=>$profile->alamat_ktp,'class'=>'sm:col-span-2'])
                        @include('partials.field', ['label'=>'Alamat Domisili','value'=>$profile->alamat_domisili,'class'=>'sm:col-span-2'])
                        @include('partials.field', ['label'=>'NPWP','value'=>$profile->npwp])
                        @include('partials.field', ['label'=>'BPJS Kesehatan','value'=>$profile->bpjs_kesehatan])
                        @include('partials.field', ['label'=>'BPJS Ketenagakerjaan','value'=>$profile->bpjs_ketenagakerjaan])
                        @include('partials.field', ['label'=>'Bank','value'=>$profile->bank_nama])
                        @include('partials.field', ['label'=>'Nomor Rekening','value'=>$profile->nomor_rekening])
                        @include('partials.field', ['label'=>'Atas Nama Rekening','value'=>$profile->nama_rekening])

                    </div>

                </div>


                <!-- ================= KELUARGA ================= -->
                <div class="border rounded-xl overflow-hidden">

                    <div class="px-5 py-3 font-semibold bg-gray-50">
                        Keluarga
                    </div>

                    <div class="p-5 grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                        @include('partials.field', ['label'=>'Nama Pasangan','value'=>$profile->nama_pasangan])
                        @include('partials.field', ['label'=>'Pekerjaan Pasangan','value'=>$profile->pekerjaan_pasangan])
                        @include('partials.field', ['label'=>'Jumlah Anak','value'=>$profile->jumlah_anak])
                        @include('partials.field', ['label'=>'Kontak Darurat','value'=>$profile->kontak_darurat])

                    </div>

                </div>


                <!-- ================= PENDIDIKAN ================= -->
                <div class="border rounded-xl overflow-hidden">

                    <div class="px-5 py-3 font-semibold bg-gray-50">
                        Pendidikan
                    </div>

                    <div class="p-5 grid sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">

                        @include('partials.field', ['label'=>'Pendidikan Terakhir','value'=>$profile->pendidikan_terakhir])
                        @include('partials.field', ['label'=>'Jurusan','value'=>$profile->jurusan])
                        @include('partials.field', ['label'=>'Tahun Lulus','value'=>$profile->tahun_lulus])

                    </div>

                </div>

                {{-- ================= SERTIFIKAT ================= --}}
                @if(isset($sertifikats) && $sertifikats->count())

                <section class="max-w-7xl mx-auto px-4 pb-8">

                    <div class="rounded-2xl border border-gray-200 overflow-hidden">

                        <div class="px-5 py-3 bg-gray-50 text-sm font-semibold text-gray-700">
                            Sertifikat / Kompetensi
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

                                    @foreach($sertifikats as $s)

                                    <tr>

                                        <td class="px-5 py-3 font-medium text-gray-900">
                                            {{ $s->nama_sertifikat ?? '-' }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-700">
                                            {{ $s->bidang ?? '-' }}
                                        </td>

                                        <td class="px-5 py-3 text-gray-700">
                                            {{ $s->tahun ?? '-' }}
                                        </td>

                                        <td class="px-5 py-3">

                                            @if(!empty($s->file_path))
                                                <a
                                                    href="{{ asset('storage/'.$s->file_path) }}"
                                                    target="_blank"
                                                    class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm"
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

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </section>

                @endif

            </div>

        @endif

    </div>

</section>
  <!-- Flash messages -->
  <section class="max-w-7xl mx-auto px-4">
    @if (session('access_granted'))
      <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
        Akses diberikan untuk 30 menit. Silakan klik ulang tombol View/Download pada dokumen tujuan.
      </div>
    @endif
    @if ($errors->any())
      <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
        <ul class="list-disc list-inside">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif
  </section>

  <!-- Tabel Dokumen -->
  <section class="max-w-7xl mx-auto px-4 pb-10">
    <div class="rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-3 bg-gray-50 text-sm font-semibold text-gray-700">Dokumen Kepegawaian</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white">
            <tr class="text-left border-b">
              <th class="px-5 py-3">Dokumen</th>
              <th class="px-5 py-3">Jenis</th>
              <th class="px-5 py-3">Status</th>
              <th class="px-5 py-3">Diunggah</th>
              <th class="px-5 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($docs as $d)
            @php

            $me = Auth::user();
            $isOwner = $me && $me->id === $pegawai->id;
            $isAdmin = $me && $me->hasRole('admin');

            // sesi akses per dokumen (diset 30 menit saat verify sukses)
            $sessionKey = "doc_access_{$d->id}";
            $untilStr   = session($sessionKey);
            $hasSession = false;
            if ($untilStr) {
                try {
                    $until = \Carbon\Carbon::parse($untilStr);
                    $hasSession = now()->lte($until);
                } catch (\Throwable $e) {
                    $hasSession = false;
                }
            }

            // indikator dokumen memang punya kode (mengikuti logic dashboard)
            $docHasCode = filled($d->access_code_enc) || filled($d->access_code_set_at);
            // kalau di dashboard kamu juga pakai access_code_plain, ikutkan:
            // $docHasCode = $docHasCode || filled($d->access_code_plain ?? null);

            // HANYA butuh kode jika:
            // - bukan admin
            // - bukan owner
            // - belum punya sesi akses aktif
            // - dokumen memang punya kode
            $requires = (!$isAdmin && !$isOwner && !$hasSession && $docHasCode);

            $statusBadge = $requires
                ? ['Privasi', 'bg-amber-50 text-amber-700']
                : ['Siap Diakses', 'bg-emerald-50 text-emerald-700'];
            @endphp


              <tr>
                <td class="px-5 py-3">
                  <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-maroon/10 text-maroon">
                      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M6 2h7l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/><path d="M13 2v5h5"/></svg>
                    </span>
                    <div>
                      <p class="font-medium text-gray-900">{{ $d->title }}</p>
                      <p class="text-xs text-gray-600">{{ $d->mime ?? 'file' }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-5 py-3 uppercase text-gray-700">{{ $d->type }}</td>
                <td class="px-5 py-3">
                  <span class="px-2 py-0.5 rounded text-xs {{ $statusBadge[1] }}">{{ $statusBadge[0] }} </span> @if($hasSession)
  <span class="ml-2 text-[11px] text-emerald-700">
    Diizinkan s/d {{ \Carbon\Carbon::parse($untilStr)->format('H:i') }}
  </span>
@endif 
                </td>
                <td class="px-5 py-3 text-gray-700">{{ optional($d->created_at)->format('d M Y H:i') }}</td>
                <td class="px-5 py-3">
                  <div class="flex flex-wrap gap-2">
                   <a  href="{{ $requires ? '#' : route('public.pegawai.view', $d->id) }}"
                        class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm"
                        data-doc-btn
                        data-verify="{{ route('public.pegawai.verify', $d->id) }}"
                        data-href-view="{{ route('public.pegawai.view', $d->id) }}"
                        data-href-download="{{ route('public.pegawai.download', $d->id) }}"
                        data-requires-code="{{ $requires ? '1' : '0' }}"
                        data-action="view">
                    View
                    </a>

                    <a  href="{{ $requires ? '#' : route('public.pegawai.download', $d->id) }}"
                        class="px-3 py-1.5 rounded-md bg-maroon text-white hover:bg-maroon-800 transition text-sm"
                        data-doc-btn
                        data-verify="{{ route('public.pegawai.verify', $d->id) }}"
                        data-href-view="{{ route('public.pegawai.view', $d->id) }}"
                        data-href-download="{{ route('public.pegawai.download', $d->id) }}"
                        data-requires-code="{{ $requires ? '1' : '0' }}"
                        data-action="download">
                    Download
                    </a>

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="px-5 py-6 text-center text-gray-500">Tidak ada dokumen.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-5 py-3 text-[12px] text-amber-700 bg-amber-50 border-t border-amber-200">
        Dokumen bertanda <span class="font-semibold">Privasi</span> memerlukan <span class="font-semibold">Kode Akses</span> + <span class="font-semibold">Alasan</span>.
        Semua akses (view/unduh) dicatat di riwayat.
      </div>
    </div>
  </section>

  <!-- Modal Kode Akses -->
  <div id="accessModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative z-10 mx-auto max-w-md px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
          <h2 class="text-white text-lg font-bold">Verifikasi Akses Dokumen</h2>
          <p class="text-white/80 text-xs mt-0.5">Masukkan kode akses dan alasan untuk melanjutkan.</p>
        </div>
        <form id="accessForm" class="p-5 space-y-3" method="POST">
          @csrf
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kode Akses</span>
            <input name="access_code" id="accessCode" type="password" required
                   class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="contoh: SIGAP-1234"/>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alasan Akses</span>
            <select name="reason" id="accessReason" required
                    class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih alasan…</option>
              <option>Administrasi gaji/tunjangan</option>
              <option>Penugasan/keperluan dinas</option>
              <option>Verifikasi identitas internal</option>
              <option>Permintaan pimpinan</option>
              <option>Lainnya (SOP)</option>
            </select>
          </label>
          <div class="flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Akses tercatat untuk audit internal.</span>
            </div>
            <a href="#sop" class="text-maroon hover:underline">Lihat SOP</a>
          </div>
          <div class="pt-2 flex gap-2">
            <button class="flex-1 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">Lanjutkan</button>
            <button type="button" id="accessCancel" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</button>
          </div>
        </form>
      </div>
    </div>
  </div>

    <!-- Lightbox Preview -->
  <div id="previewModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b">
          <h3 class="font-semibold text-gray-800">Preview Dokumen</h3>
          <button id="previewClose" class="p-2 rounded-md hover:bg-gray-100" aria-label="Tutup">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="p-0">
          <div id="previewBody" class="w-full h-[80vh] bg-gray-50 flex items-center justify-center"></div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // === Elemen: modal kode ===
  const accessModal  = document.getElementById('accessModal');
  const accessForm   = document.getElementById('accessForm');
  const accessCancel = document.getElementById('accessCancel');

  let pending = { action: null, hrefView: null, hrefDownload: null };

  function openAccessModal(verifyUrl, action, hrefView, hrefDownload) {
    accessForm.setAttribute('action', verifyUrl);
    pending = { action, hrefView, hrefDownload };
    accessModal.classList.remove('hidden');
    setTimeout(() => document.getElementById('accessCode')?.focus(), 50);
  }
  function closeAccessModal() { accessModal.classList.add('hidden'); }
  accessCancel?.addEventListener('click', closeAccessModal);

  // === Elemen: lightbox preview ===
  const previewModal = document.getElementById('previewModal');
  const previewBody  = document.getElementById('previewBody');
  const previewClose = document.getElementById('previewClose');

  function openPreview(url) {
    previewBody.innerHTML = '';
    const fm = document.createElement('iframe');
    fm.src = url + (url.includes('#') ? '' : '#zoom=page-width');
    fm.title = 'Preview Dokumen';
    fm.className = 'w-full h-[80vh]';
    fm.loading = 'lazy';
    previewBody.appendChild(fm);
    previewModal.classList.remove('hidden');
  }
  function closePreview() {
    previewModal.classList.add('hidden');
    previewBody.innerHTML = '';
  }
  previewClose?.addEventListener('click', closePreview);

  // ESC tutup keduanya
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') { closeAccessModal(); closePreview(); }
  });

  // === Satu-satunya handler untuk semua tombol dokumen ===
  document.querySelectorAll('[data-doc-btn]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const requires     = btn.getAttribute('data-requires-code') === '1';
      const action       = btn.getAttribute('data-action'); // 'view' | 'download'
      const verifyUrl    = btn.getAttribute('data-verify');
      const hrefView     = btn.getAttribute('data-href-view');
      const hrefDownload = btn.getAttribute('data-href-download');

      if (requires) {
        // butuh kode -> tampilkan modal
        e.preventDefault();
        openAccessModal(verifyUrl, action, hrefView, hrefDownload);
        return;
      }

      // tidak butuh kode
      if (action === 'view') {
        // intercept -> lightbox
        e.preventDefault();
        openPreview(hrefView);
        return;
      }

      // action === 'download' -> biarkan default (biar langsung download)
      // (JANGAN set window.location.href manual agar tak nabrak handler lain)
    }, { capture: true }); // capture biar handler ini jalan duluan
  });
});
</script>
@endpush
