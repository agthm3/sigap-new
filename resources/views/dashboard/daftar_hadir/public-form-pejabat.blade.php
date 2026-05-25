<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TTD Pejabat – {{ $penandatangan->kegiatan->nama_kegiatan }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-lg mx-auto px-4 py-8">

  {{-- Header --}}
  <div class="text-center mb-6">
    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-amber-100 mb-3">
      <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213l-4.5 1.125 1.125-4.5 12.737-12.35z"/>
      </svg>
    </div>
    <h1 class="text-xl font-extrabold text-gray-900">Tanda Tangan Pejabat</h1>
    <p class="text-sm text-gray-600 mt-1">{{ $penandatangan->kegiatan->nama_kegiatan }}</p>
    <p class="text-xs text-gray-500 mt-0.5">
      {{ $penandatangan->kegiatan->hari_tanggal }}
      • {{ $penandatangan->kegiatan->tempat }}
    </p>
  </div>

  {{-- Sudah TTD --}}
  @if($penandatangan->sudah_ttd)
    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-center mb-4">
      <p class="font-bold text-emerald-800 text-lg">✔ Tanda Tangan Tersimpan</p>
      <p class="text-sm text-emerald-700 mt-1">
        Ditandatangani pada {{ $penandatangan->signed_at?->format('d M Y, H:i') }} WITA
      </p>
      @if($penandatangan->ttd_path)
        <div class="mt-4 flex justify-center">
          <img src="{{ asset('storage/' . $penandatangan->ttd_path) }}"
               class="max-h-24 rounded-xl border bg-white p-2" alt="TTD">
        </div>
      @endif
    </div>
  @endif

  {{-- Data Pejabat --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm mb-4">
    <h2 class="text-sm font-semibold text-gray-700 mb-3">Data Penandatangan</h2>
    <div class="space-y-2 text-sm text-gray-800">
      <div class="flex gap-2">
        <span class="w-28 text-gray-500 shrink-0">Nama</span>
        <span class="font-semibold">{{ $penandatangan->nama_lengkap }}</span>
      </div>
      @if($penandatangan->jabatan)
        <div class="flex gap-2">
          <span class="w-28 text-gray-500 shrink-0">Jabatan</span>
          <span>{{ $penandatangan->jabatan }}</span>
        </div>
      @endif
      @if($penandatangan->pangkat)
        <div class="flex gap-2">
          <span class="w-28 text-gray-500 shrink-0">Pangkat</span>
          <span>{{ $penandatangan->pangkat }}</span>
        </div>
      @endif
      @if($penandatangan->golongan)
        <div class="flex gap-2">
          <span class="w-28 text-gray-500 shrink-0">Golongan</span>
          <span>{{ $penandatangan->golongan }}</span>
        </div>
      @endif
      @if($penandatangan->nip)
        <div class="flex gap-2">
          <span class="w-28 text-gray-500 shrink-0">NIP</span>
          <span>{{ $penandatangan->nip }}</span>
        </div>
      @endif
      @if($penandatangan->tempat_ttd || $penandatangan->tanggal_ttd)
        <div class="flex gap-2">
          <span class="w-28 text-gray-500 shrink-0">Tempat/Tgl</span>
          <span>{{ $penandatangan->tempat_ttd }}{{ ($penandatangan->tempat_ttd && $penandatangan->tanggal_ttd) ? ', ' : '' }}{{ $penandatangan->tanggal_ttd }}</span>
        </div>
      @endif
    </div>
  </div>

  {{-- Form TTD --}}
  @if(!$penandatangan->sudah_ttd)
    <form id="ttd-form"
          action="{{ route('sigap-daftar-hadir.pejabat-store', $penandatangan->uuid) }}"
          method="POST"
          class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
      @csrf

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanda Tangan Digital</label>
        <div class="rounded-2xl border-2 border-dashed border-gray-300 p-2 bg-gray-50">
          <canvas id="signature-pad" class="w-full" style="height:180px;"></canvas>
        </div>
        <input type="hidden" id="ttd_data" name="ttd_data">
        <div class="mt-2 flex gap-2">
          <button type="button" id="clear-btn"
                  class="px-3 py-1.5 rounded-lg border text-xs text-gray-600 hover:bg-gray-100">
            Hapus & Ulangi
          </button>
        </div>
      </div>

      <button type="submit"
              class="w-full px-4 py-3 rounded-xl font-semibold text-white"
              style="background-color:#7a2222;">
        Simpan Tanda Tangan
      </button>
    </form>
  @endif

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const canvas = document.getElementById('signature-pad');
  const ttdDataInput = document.getElementById('ttd_data');
  const form = document.getElementById('ttd-form');

  if (!canvas) return;

  const ratio = Math.max(window.devicePixelRatio || 1, 1);

  function resizeCanvas() {
    const data = signaturePad.isEmpty() ? null : signaturePad.toData();
    canvas.width  = canvas.offsetWidth  * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    if (data) signaturePad.fromData(data);
  }

  const signaturePad = new SignaturePad(canvas, {
    backgroundColor: 'rgb(255,255,255)',
  });

  resizeCanvas();
  window.addEventListener('resize', () => resizeCanvas());

  document.getElementById('clear-btn').addEventListener('click', function () {
    signaturePad.clear();
    ttdDataInput.value = '';
  });

  if (form) {
    form.addEventListener('submit', function (e) {
      if (signaturePad.isEmpty()) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'TTD Kosong',
          text: 'Silakan gambar tanda tangan terlebih dahulu.',
        });
        return;
      }
      ttdDataInput.value = signaturePad.toDataURL('image/png');
    });
  }

  @if(session('success'))
    Swal.fire({
      icon: 'success',
      title: 'Berhasil',
      text: @json(session('success')),
      confirmButtonText: 'OK',
      confirmButtonColor: '#7a2222',
    });
  @endif

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: @json(session('error')),
    });
  @endif
});
</script>
</body>
</html>