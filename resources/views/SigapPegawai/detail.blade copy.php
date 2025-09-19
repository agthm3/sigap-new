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

  <!-- Header -->
  <section class="max-w-7xl mx-auto px-4 pb-6">
    <div class="rounded-2xl border border-gray-200 p-4 sm:p-6">
      <div class="flex flex-col md:flex-row md:items-center gap-6">
        @php
        $photoUrl = $pegawai->profile_photo_path
            ? asset('storage/'.$pegawai->profile_photo_path)
            : null;
        @endphp

        <img class="w-28 h-28 rounded-xl object-cover ring-2 ring-maroon/20"
            src="{{ $photoUrl ?: 'https://placehold.co/112x112?text=SP' }}"
            alt="Foto profil pegawai">

        <div class="flex-1">
          <div class="flex flex-wrap items-center gap-2">
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $pegawai->name }}</h1>
            @if($pegawai->nip)
              <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs">NIP: {{ $pegawai->nip }}</span>
            @endif
            @if($pegawai->unit)
              <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-xs">Unit: {{ $pegawai->unit }}</span>
            @endif
          </div>
          <p class="text-sm text-gray-600 mt-1">Jabatan: {{ $pegawai->position ?? '—' }}</p>
        </div>
      </div>
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
@endsection

@push('scripts')
<script>
/**
 * Tidak auto-buka modal. Modal hanya muncul saat klik View/Download
 * pada dokumen yang butuh kode. Aksi verifikasi POST ke URL pada
 * data-verify masing-masing dokumen.
 *
 * Setelah verifikasi sukses, controller me-return back dengan flash
 * 'access_granted'. Pengguna tinggal klik ulang View/Download.
 */
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('accessModal');
  const accessForm = document.getElementById('accessForm');
  const cancelBtn = document.getElementById('accessCancel');

  let pending = { action: null, hrefView: null, hrefDownload: null };

  const openModal = (verifyUrl, action, hrefView, hrefDownload) => {
    accessForm.setAttribute('action', verifyUrl);
    pending = { action, hrefView, hrefDownload };
    modal.classList.remove('hidden');
    setTimeout(() => document.getElementById('accessCode')?.focus(), 50);
  };
  const closeModal = () => modal.classList.add('hidden');

  cancelBtn?.addEventListener('click', closeModal);
  window.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

  // Delegasi untuk semua tombol dokumen
  document.querySelectorAll('[data-doc-btn]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const requires = btn.getAttribute('data-requires-code') === '1';
      const action = btn.getAttribute('data-action'); // 'view' | 'download'
      const verifyUrl = btn.getAttribute('data-verify');
      const hrefView = btn.getAttribute('data-href-view');
      const hrefDownload = btn.getAttribute('data-href-download');

      if (requires) {
        e.preventDefault();
        openModal(verifyUrl, action, hrefView, hrefDownload);
      } else {
        // tidak butuh kode -> langsung
        if (action === 'view') window.location.href = hrefView;
        else window.location.href = hrefDownload;
      }
    });
  });
});
</script>
@endpush
