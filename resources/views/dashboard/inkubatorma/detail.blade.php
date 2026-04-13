@extends('layouts.app')

@section('content')
@php
  use App\Models\Inkubatorma;

  // Status final:
  // Menunggu, Akan Dijadwalkan, Terjadwal, Sesi Konsultasi, Dijadwalkan Ulang, Ditolak, Selesai
  $statusMenunggu         = Inkubatorma::STATUS_MENUNGGU ?? 'Menunggu';
  $statusAkanJadwal       = Inkubatorma::STATUS_AKAN_DIJADWALKAN ?? 'Akan Dijadwalkan';
  $statusTerjadwal        = Inkubatorma::STATUS_TERJADWAL ?? 'Terjadwal';
  $statusSesiKonsultasi   = Inkubatorma::STATUS_SESI_KONSULTASI ?? 'Sesi Konsultasi';
  $statusJadwalUlang      = Inkubatorma::STATUS_DIJADWALKAN_ULANG ?? 'Dijadwalkan Ulang';
  $statusDitolak          = Inkubatorma::STATUS_DITOLAK ?? 'Ditolak';
  $statusSelesai          = Inkubatorma::STATUS_SELESAI ?? 'Selesai';

  $status = $inkubatorma->status ?? $statusMenunggu;

  // helper tampilkan metode (enum online/offline)
  $metodeLabel = function ($val) {
    return match ($val) {
      'online'  => 'Online (Zoom/Meet)',
      'offline' => 'Tatap Muka (Offline)',
      default   => '—',
    };
  };

  // Helper aman format tanggal
  $fmtDate = function ($date, $format = 'd M Y') {
    if (empty($date)) return '—';
    try {
      return \Carbon\Carbon::parse($date)->timezone('Asia/Makassar')->format($format);
    } catch (\Throwable $e) {
      return '—';
    }
  };

  // Helper aman format jam (time)
  $fmtTime = function ($time) {
    if (empty($time)) return '—';
    try {
      return \Carbon\Carbon::parse($time)->format('H:i') . ' WITA';
    } catch (\Throwable $e) {
      return (string) $time;
    }
  };

  // Helper format datetime (created_at / updated_at)
  $fmtDateTime = function ($dt, $format = 'd M Y • H:i') {
    if (empty($dt)) return '—';
    try {
      return \Carbon\Carbon::parse($dt)->timezone('Asia/Makassar')->format($format) . ' WITA';
    } catch (\Throwable $e) {
      return '—';
    }
  };

  // Supaya bisa multi layanan
  $layananLabel = $inkubatorma->layanan_nama;

  if (!empty($inkubatorma->layanan_lainnya)) {
    $layananLabel .= ' • ' . $inkubatorma->layanan_lainnya;
  }

  // Modal "Hubungi Verifikator" (khusus sisi user)
  $isUser = auth()->check() && auth()->user()->hasRole('user');

  // Tampilkan blok jadwal final jika memang sudah pernah dijadwalkan / sedang sesi / selesai
  $showFinalSchedule = (
    $inkubatorma->tanggal_final ||
    $inkubatorma->jam_final ||
    $inkubatorma->metode_final ||
    $inkubatorma->pic_employee_id ||
    in_array($status, [$statusTerjadwal, $statusSesiKonsultasi, $statusJadwalUlang, $statusSelesai], true)
  );
@endphp

<style>
  @media print {
    @page { size: A4 portrait; margin: 14mm; }

    /* sembunyikan elemen navigasi & tombol */
    nav, header, footer, aside,
    .no-print,
    #btnHubungiVerif,
    #modalVerif,
    a[href*="dashboard"] { display: none !important; }

    /* reset layout jadi full width */
    main { max-width: 100% !important; padding: 0 !important; }
    .lg\:col-span-2 { grid-column: span 3 !important; }
    .lg\:grid-cols-3 { grid-template-columns: 1fr !important; }

    /* munculkan aside (timeline) yang biasanya di kanan */
    aside { display: block !important; }

    /* pastikan card tidak terpotong antar halaman */
    .rounded-xl, .bg-white { break-inside: avoid; page-break-inside: avoid; }
  }
</style>

<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">

  {{-- HEADER --}}
  <div class="flex items-start justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 text-sm text-gray-500">
        <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg bg-maroon text-white">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-8 9 8v10a2 2 0 0 1-2 2h-4v-7H9v7H5a2 2 0 0 1-2-2V11z"/>
          </svg>
        </span>
        <span class="text-xs text-gray-600">Detail Pengajuan</span>
      </div>

      <h1 class="mt-1 text-2xl font-extrabold text-gray-900">
        {{ $inkubatorma->judul_konsultasi ?? '—' }}
      </h1>
    </div>

    <div class="flex flex-wrap items-center gap-2">
      <a href="{{ route('sigap-inkubatorma.dashboard') }}"
         class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
        ← Kembali
      </a>
    
      <button onclick="window.print()"
              class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90 flex items-center gap-2">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M6 9V2h12v7"/><rect x="6" y="17" width="12" height="5" rx="1"/>
          <path d="M6 13H4a2 2 0 0 0-2 2v4h4v-4h12v4h4v-4a2 2 0 0 0-2-2h-2"/>
        </svg>
        Print Detail
      </button>
    </div>
  </div>

  {{-- CARD STATUS --}}
  <div>
    @include('dashboard.inkubatorma.partials.status-widget', [
      'status' => $status,
      'inkubatorma' => $inkubatorma
    ])
  </div>

  {{-- SUMMARY --}}
  {{-- Desktop: 4 card grid | Mobile: 1 card ringkas --}}

  {{-- Mobile only --}}
  <div class="sm:hidden bg-white rounded-xl border border-gray-200 px-4 py-3 space-y-2 text-sm">
    <div class="flex items-center justify-between">
      <span class="text-gray-500">Kode</span>
      <span class="font-extrabold text-maroon">{{ $inkubatorma->kode ?? '—' }}</span>
    </div>
    <div class="h-px bg-gray-100"></div>
    <div class="flex items-center justify-between">
      <span class="text-gray-500">Pengaju</span>
      <span class="font-semibold text-gray-800 text-right max-w-[60%] truncate">{{ $inkubatorma->nama_pengaju ?? '—' }}</span>
    </div>
    <div class="h-px bg-gray-100"></div>
    <div class="flex items-center justify-between">
      <span class="text-gray-500">Diajukan</span>
      <span class="font-semibold text-gray-800">
        {{ $inkubatorma->created_at ? \Carbon\Carbon::parse($inkubatorma->created_at)->timezone('Asia/Makassar')->format('d M Y • H:i') . ' WITA' : '—' }}
      </span>
    </div>
    <div class="h-px bg-gray-100"></div>
    <div class="flex items-center justify-between">
      <span class="text-gray-500">Update</span>
      <span class="font-semibold text-gray-800">
        {{ $inkubatorma->updated_at ? \Carbon\Carbon::parse($inkubatorma->updated_at)->timezone('Asia/Makassar')->format('d M Y • H:i') . ' WITA' : '—' }}
      </span>
    </div>
  </div>

  {{-- Desktop only --}}
  <div class="hidden sm:grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl border border-gray-200 p-5">
      <p class="text-sm text-gray-500">Kode Pengajuan</p>
      <p class="mt-1 text-xl font-extrabold text-maroon">{{ $inkubatorma->kode ?? '—' }}</p>
      <p class="mt-1 text-xs text-gray-500">Identitas pengajuan</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
      <p class="text-sm text-gray-500">Nama Pengaju</p>
      <p class="mt-1 text-xl font-extrabold text-maroon">{{ $inkubatorma->nama_pengaju ?? '—' }}</p>
      <p class="mt-1 text-xs text-gray-500">Pemohon/instansi</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
      <p class="text-sm text-gray-500">Diajukan</p>
      <p class="mt-1 text-xl font-extrabold text-maroon">
        {{ $inkubatorma->created_at ? \Carbon\Carbon::parse($inkubatorma->created_at)->timezone('Asia/Makassar')->format('d M Y') : '—' }}
      </p>
      <p class="mt-1 text-xs text-maroon">
        {{ $inkubatorma->created_at ? \Carbon\Carbon::parse($inkubatorma->created_at)->timezone('Asia/Makassar')->format('H:i') . ' WITA' : '—' }}
      </p>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5">
      <p class="text-sm text-gray-500">Terakhir Update</p>
      <p class="mt-1 text-xl font-extrabold text-maroon">
        {{ $inkubatorma->updated_at ? \Carbon\Carbon::parse($inkubatorma->updated_at)->timezone('Asia/Makassar')->format('d M Y') : '—' }}
      </p>
      <p class="mt-1 text-xs text-maroon">
        {{ $inkubatorma->updated_at ? \Carbon\Carbon::parse($inkubatorma->updated_at)->timezone('Asia/Makassar')->format('H:i') . ' WITA' : '—' }}
      </p>
    </div>
  </div>

  {{-- FEEDBACK VERIFIKATOR --}}
  <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-gray-800">Feedback Verifikator</h3>
        <p class="text-xs text-gray-500 mt-0.5">Catatan setelah proses verifikasi</p>
      </div>

      <span class="text-xs px-2 py-1 rounded-full font-semibold
        {{ !empty($inkubatorma->catatan_verifikator) ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
        {{ !empty($inkubatorma->catatan_verifikator) ? 'Ada catatan' : 'Belum ada' }}
      </span>
    </div>

    <div class="p-5 text-sm">
      @if(empty($inkubatorma->catatan_verifikator))
        <div class="rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4 text-sm text-gray-600">
          <p class="font-semibold text-gray-800">Belum ada feedback dari verifikator</p>
          <p class="mt-1 text-xs text-gray-500">
            Jika status pengajuan sudah ditinjau, catatan verifikator akan muncul di sini.
          </p>
        </div>
      @else
        <div class="rounded-lg border border-gray-200 bg-white p-4">
          <p class="text-xs font-semibold text-gray-500">Catatan</p>
          <p class="mt-2 text-sm text-gray-800 leading-relaxed whitespace-pre-line">
            {{ $inkubatorma->catatan_verifikator }}
          </p>

          <div class="mt-3 text-xs text-gray-500">
            {{ $inkubatorma->verifikatorUser?->name ?? 'Verifikator' }}
            • {{ $inkubatorma->verifikasi_at ? \Carbon\Carbon::parse($inkubatorma->verifikasi_at)->timezone('Asia/Makassar')->format('d M Y • H:i') . ' WITA' : '—' }}
          </div>
        </div>
      @endif
    </div>
  </div>

  {{-- MAIN GRID --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT --}}
    <section class="lg:col-span-2 space-y-6">

      {{-- Hubungi verifikator (khusus USER) --}}
      @if($isUser)
        <div>
          <div class="rounded-xl border border-gray-200 bg-white p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-gray-700">
              <span class="font-semibold">Ada kendala? Silahkan hubungi verifikator</span>
            </p>

            <button type="button" id="btnHubungiVerif"
                    class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90">
              Hubungi Verifikator
            </button>
          </div>
        </div>

        <div id="modalVerif" class="hidden" style="position:fixed; inset:0; z-index:9999;">
          <div id="modalBackdrop"
               style="position:fixed; inset:0;"
               class="bg-black/50 backdrop-blur-sm">
          </div>

          <div class="absolute inset-0 flex items-start sm:items-center justify-center p-4 sm:p-6">
            <div class="relative w-full max-w-3xl lg:max-w-5xl rounded-[28px] border-4 border-gray-300 bg-white shadow-2xl overflow-hidden"
                 style="max-height:85vh;">
              <div class="relative px-6 sm:px-8 py-6 sm:py-7 text-white"
                   style="background: radial-gradient(1200px 500px at 20% -10%, rgba(255,255,255,.18), transparent 55%),
                          linear-gradient(135deg, #7a2222 0%, #5f1717 60%, #3f0f0f 100%);">
                <p class="text-sm opacity-90">Layanan Bantuan</p>
                <h3 class="text-2xl sm:text-4xl font-extrabold leading-tight">Informasi Kontak Verifikator</h3>

                <button type="button" id="btnCloseVerif"
                        class="absolute right-4 top-4 sm:right-6 sm:top-6 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 grid place-items-center">
                  <span class="text-2xl leading-none">×</span>
                </button>
              </div>

              <div class="overflow-y-auto px-6 sm:px-8 py-6" style="max-height:calc(85vh - 92px);">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  @forelse(($verifikators ?? []) as $v)
                    @php
                      $avatar = $v->profile_photo_path
                        ? asset('storage/'.$v->profile_photo_path)
                        : asset('images/avatar-placeholder.png');

                      $digits = preg_replace('/\D+/', '', (string) $v->nomor_hp);
                      if ($digits) {
                        if (!str_starts_with($digits, '62')) $digits = '62' . ltrim($digits, '0');
                      } else {
                        $digits = null;
                      }
                    @endphp

                    <div class="rounded-2xl border border-gray-200 bg-white p-5">
                      <div class="flex items-center gap-4">
                        <img src="{{ $avatar }}" alt="{{ $v->name }}"
                             class="w-14 h-14 rounded-full object-cover border border-gray-200"
                             onerror="this.onerror=null;this.src='{{ asset('images/avatar-placeholder.png') }}';">

                        <div class="min-w-0">
                          <p class="text-xs text-gray-500">Verifikator Inkubatorma</p>
                          <p class="font-extrabold text-gray-900 truncate">{{ $v->name }}</p>
                          <p class="text-sm text-gray-600">{{ $v->nomor_hp ?: 'Nomor belum diisi' }}</p>
                        </div>
                      </div>

                      <div class="mt-4 flex items-center justify-end gap-2">
                        @if($digits)
                          <a href="https://wa.me/{{ $digits }}" target="_blank"
                             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-semibold hover:opacity-90">
                            Hubungi WhatsApp
                          </a>
                        @else
                          <button type="button" disabled
                                  class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-200 text-gray-500 text-sm font-semibold cursor-not-allowed">
                            Hubungi WhatsApp
                          </button>
                        @endif
                      </div>
                    </div>
                  @empty
                    <div class="sm:col-span-2 rounded-2xl border border-gray-200 bg-gray-50 p-6 text-gray-600">
                      Belum ada verifikator yang terdaftar.
                    </div>
                  @endforelse
                </div>
                <div class="h-2"></div>
              </div>
            </div>
          </div>
        </div>

        <script>
        (function () {
          const btn = document.getElementById('btnHubungiVerif');
          const modal = document.getElementById('modalVerif');
          const closeBtn = document.getElementById('btnCloseVerif');
          const backdrop = document.getElementById('modalBackdrop');

          function lockScroll() {
            document.documentElement.classList.add('overflow-hidden');
            document.body.classList.add('overflow-hidden');
          }

          function unlockScroll() {
            document.documentElement.classList.remove('overflow-hidden');
            document.body.classList.remove('overflow-hidden');
          }

          function openModal() {
            if (!modal) return;
            modal.classList.remove('hidden');
            lockScroll();
          }

          function closeModal() {
            if (!modal) return;
            modal.classList.add('hidden');
            unlockScroll();
          }

          btn?.addEventListener('click', openModal);
          closeBtn?.addEventListener('click', closeModal);
          backdrop?.addEventListener('click', closeModal);

          window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeModal();
          });
        })();
        </script>
      @endif

      {{-- DATA PENGAJU --}}
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b">
          <h3 class="font-semibold text-gray-800">Data Pengaju</h3>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
          <div>
            <p class="text-xs font-semibold text-gray-500">Nama Pengaju</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->nama_pengaju ?? '—' }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-500">Nomor HP / WA</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->hp_pengaju ?? '—' }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-500">Nama OPD / Unit</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->opd_unit ?? '—' }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-500">Layanan</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $layananLabel }}</p>
          </div>
        </div>
      </div>

      {{-- RINCIAN --}}
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b">
          <h3 class="font-semibold text-gray-800">Rincian Permintaan</h3>
          <p class="text-xs text-gray-500 mt-0.5">Ringkasan kebutuhan, masalah, dan target output</p>
        </div>

        <div class="p-5 space-y-4 text-sm">

          <div>
            <p class="text-xs font-semibold text-gray-500">Judul Pengajuan</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->judul_konsultasi ?? '—' }}</p>
          </div>

          <div>
            <p class="text-xs font-semibold text-gray-500">Keluhan / Permasalahan</p>
            <p class="mt-1 text-gray-700 leading-relaxed whitespace-pre-line">{{ $inkubatorma->keluhan ?? '—' }}</p>
          </div>

          <div>
            <p class="text-xs font-semibold text-gray-500">Poin Asistensi yang Dibutuhkan</p>
            <p class="mt-1 text-gray-700 leading-relaxed whitespace-pre-line">{{ $inkubatorma->poin_asistensi ?? '—' }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
              <p class="text-xs font-semibold text-gray-500">Tanggal Usulan</p>
              <p class="mt-1 font-semibold text-gray-800">{{ $fmtDate($inkubatorma->tanggal_usulan) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
              <p class="text-xs font-semibold text-gray-500">Jam Usulan</p>
              <p class="mt-1 font-semibold text-gray-800">{{ $fmtTime($inkubatorma->jam_usulan) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
              <p class="text-xs font-semibold text-gray-500">Metode Usulan</p>
              <p class="mt-1 font-semibold text-gray-800">{{ $metodeLabel($inkubatorma->metode_usulan) }}</p>
            </div>
            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
              <p class="text-xs font-semibold text-gray-500">Target Personil Asistensi (opsional)</p>
              <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->target_personil_usulan ?? '—' }}</p>
            </div>
          </div>

          {{-- Jika sudah ada jadwal final / sudah masuk fase jadwal --}}
          @if($showFinalSchedule)
            <div class="pt-2">
              <h4 class="font-semibold text-gray-800">Jadwal Final</h4>

              <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="rounded-xl border border-gray-200 p-5 bg-white">
                  <p class="text-xs font-semibold text-gray-500">Tanggal & Jam</p>
                  <p class="mt-1 font-semibold text-gray-900">
                    {{ $inkubatorma->tanggal_final ? $fmtDate($inkubatorma->tanggal_final) : '—' }}
                    {{ $inkubatorma->jam_final ? '• ' . $fmtTime($inkubatorma->jam_final) : '' }}
                  </p>
                </div>

                <div class="rounded-xl border border-gray-200 p-5 bg-white">
                  <p class="text-xs font-semibold text-gray-500">Status tersimpan</p>
                  <p class="mt-1 font-semibold text-gray-900">{{ $status }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 p-5 bg-white">
                  <p class="text-xs font-semibold text-gray-500">Metode & Lokasi/Link</p>
                  <p class="mt-1 font-semibold text-gray-900">
                    {{ $metodeLabel($inkubatorma->metode_final) }} • {{ $inkubatorma->lokasi_link_final ?? '—' }}
                  </p>
                </div>

                <div class="rounded-xl border border-gray-200 p-5 bg-white">
                  <p class="text-xs font-semibold text-gray-500">Personil Asistensi</p>
                  <p class="mt-1 font-semibold text-gray-900">{{ $inkubatorma->verifikatorUser?->name ?? '—' }}</p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

    </section>

    {{-- RIGHT --}}
    <aside class="space-y-6">

      {{-- TIMELINE CARD --}}
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b">
          <h3 class="font-semibold text-gray-800">Timeline</h3>
          <p class="text-xs text-gray-500 mt-0.5">Pantau progres dari pengajuan hingga selesai</p>
        </div>

        <div class="p-5">
          @include('dashboard.inkubatorma.partials.timeline', [
            'status' => $status,
            'inkubatorma' => $inkubatorma
          ])
        </div>
      </div>

    </aside>

  </div>

</main>
@endsection