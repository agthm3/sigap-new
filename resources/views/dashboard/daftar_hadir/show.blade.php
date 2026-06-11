@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      {{ $kegiatan->nama_kegiatan }}
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      {{ $kegiatan->hari_tanggal }} • {{ $kegiatan->tempat }} • {{ $kegiatan->waktu }}
    </p>
  </div>

  <div class="flex flex-wrap gap-2">
    <a href="{{ route('sigap-daftar-hadir.edit', $kegiatan->uuid) }}"
       class="px-4 py-2 rounded-xl border border-blue-300 text-blue-700 text-sm font-semibold hover:bg-blue-50">
      Edit
    </a>

    @if($kegiatan->status === 'selesai')
      <a href="{{ route('sigap-daftar-hadir.export-pdf', $kegiatan->uuid) }}"
         class="px-4 py-2 rounded-xl border border-emerald-500 text-emerald-700 text-sm font-semibold hover:bg-emerald-50">
        Export PDF
      </a>
    @endif
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4">

  {{-- ===== PANEL KIRI — QR + Status + Penandatangan ===== --}}
  <div class="space-y-4 lg:col-span-1">

    {{-- QR Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-500">Status</p>
          <p class="font-semibold text-gray-900">{{ strtoupper($kegiatan->status) }}</p>
        </div>
        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
          {{ $kegiatan->status === 'selesai' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($kegiatan->status === 'proses' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-gray-50 border-gray-200 text-gray-700') }}">
          {{ strtoupper($kegiatan->status) }}
        </span>
      </div>

      <div class="mt-4 text-center">
        <div class="inline-block p-4 rounded-2xl border bg-white">
          {!! QrCode::format('svg')->size(220)->margin(1)->generate($qrUrl) !!}
        </div>
        <p class="text-xs text-gray-500 mt-3 break-all">{{ $qrUrl }}</p>
      </div>

      {{-- Tombol aksi QR Peserta --}}
      <div class="mt-4 space-y-2">
        <div class="flex gap-2">
          <a href="{{ route('sigap-daftar-hadir.print-qr', $kegiatan->uuid) }}"
             target="_blank"
             class="flex-1 px-3 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50 text-center">
            Print QR
          </a>

          @hasanyrole('admin|verif_daftarhadir')
            <form action="{{ route('sigap-daftar-hadir.status', $kegiatan->uuid) }}" method="POST" class="flex-1">
              @csrf
              @if($kegiatan->status === 'selesai')
                <input type="hidden" name="status" value="proses">
                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-gray-900 text-white text-sm">
                  Buka Lagi
                </button>
              @else
                <input type="hidden" name="status" value="selesai">
                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-emerald-600 text-white text-sm">
                  Tutup QR
                </button>
              @endif
            </form>
          @endhasanyrole
        </div>

        {{-- TOMBOL BARU: Salin Link & Share WA --}}
        <button type="button" id="btn-share-wa" 
                class="w-full px-3 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold flex items-center justify-center gap-2 shadow-sm transition-all">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.454 5.709 1.455h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
          </svg>
          Salin Link & Share ke WA
        </button>
      </div>

      {{-- Tombol QR Pejabat — muncul hanya jika ada penandatangan --}}
      @if($kegiatan->penandatangan)
        <div class="mt-2">
          <a href="{{ route('sigap-daftar-hadir.print-qr-pejabat', $kegiatan->uuid) }}"
             target="_blank"
             class="block w-full px-3 py-2 rounded-xl border border-amber-400 bg-amber-50 text-amber-800 text-sm font-medium text-center hover:bg-amber-100">
            🖊 Print QR Pejabat
          </a>
        </div>
      @endif
    </div>

    {{-- Penandatangan Card --}}
    @if($kegiatan->penandatangan)
      @php $ttd = $kegiatan->penandatangan; @endphp
      <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-semibold text-gray-900 text-sm">Penandatangan</h2>
          @if($ttd->sudah_ttd)
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-emerald-50 border border-emerald-200 text-emerald-700">
              ✔ Sudah TTD
            </span>
          @else
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-amber-50 border border-amber-200 text-amber-700">
              ⏳ Belum TTD
            </span>
          @endif
        </div>

        <div class="text-sm space-y-1 text-gray-700">
          <p class="font-medium text-gray-900">{{ $ttd->nama_lengkap }}</p>
          @if($ttd->jabatan)   <p class="text-xs">{{ $ttd->jabatan }}</p> @endif
          @if($ttd->pangkat)   <p class="text-xs text-gray-500">{{ $ttd->pangkat }}{{ $ttd->golongan ? ' / ' . $ttd->golongan : '' }}</p> @endif
          @if($ttd->nip)       <p class="text-xs text-gray-500">NIP: {{ $ttd->nip }}</p> @endif
          @if($ttd->tempat_ttd || $ttd->tanggal_ttd)
            <p class="text-xs text-gray-500 pt-1">
              {{ $ttd->tempat_ttd }}{{ ($ttd->tempat_ttd && $ttd->tanggal_ttd) ? ', ' : '' }}{{ $ttd->tanggal_ttd }}
            </p>
          @endif
        </div>

        @if($ttd->sudah_ttd && $ttd->ttd_path)
          <div class="mt-3 pt-3 border-t">
            <p class="text-xs text-gray-500 mb-1">TTD:</p>
            <img src="{{ asset('storage/' . $ttd->ttd_path) }}"
                 class="max-h-16 border rounded-lg bg-gray-50 p-1" alt="TTD Pejabat">
          </div>
        @endif
      </div>
    @endif

  </div>

  {{-- ===== PANEL KANAN — Daftar Peserta ===== --}}
  <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm lg:col-span-2">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-gray-900">Daftar Peserta</h2>
      <span class="text-sm text-gray-500">{{ $kegiatan->peserta->count() }} peserta</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-600">
          <tr>
            <th class="px-3 py-2 text-left">Urut</th>
            <th class="px-3 py-2 text-left">Nama</th>
            <th class="px-3 py-2 text-left">Instansi</th>
            <th class="px-3 py-2 text-left">Gender</th>
            <th class="px-3 py-2 text-left">No HP</th>
            <th class="px-3 py-2 text-left">Email</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($kegiatan->peserta as $p)
            <tr>
              <td class="px-3 py-2">{{ $p->urutan_absen }}</td>
              <td class="px-3 py-2 font-medium text-gray-900">{{ $p->nama }}</td>
              <td class="px-3 py-2">{{ $p->instansi }}</td>
              <td class="px-3 py-2">{{ $p->gender }}</td>
              <td class="px-3 py-2">{{ $p->no_hp }}</td>
              <td class="px-3 py-2">{{ $p->email ?: '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada peserta.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

{{-- Tambahan Script Copy Clipboard & WA Share --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btnShare = document.getElementById('btn-share-wa');

  if (btnShare) {
    btnShare.addEventListener('click', function () {
      const qrUrl = "{{ $qrUrl }}";
      const namaKegiatan = "{{ $kegiatan->nama_kegiatan }}";
      const hariTanggal = "{{ $kegiatan->hari_tanggal }}";
      const tempat = "{{ $kegiatan->tempat }}";
      const waktu = "{{ $kegiatan->waktu }}";

      // Template Teks WhatsApp
      const textMessage = `*Yth. Bapak/Ibu Peserta*

Berikut kami lampirkan link daftar hadir digital untuk kegiatan:

📌 *Kegiatan:* ${namaKegiatan}
📅 *Hari/Tanggal:* ${hariTanggal}
🕒 *Waktu:* ${waktu}
🏢 *Tempat:* ${tempat}

Silakan mengisi daftar hadir melalui link resmi di bawah ini:
🔗 ${qrUrl}

Terima kasih atas perhatian dan partisipasinya.`;

      // 1. Proses Salin Link ke Clipboard Sistem
      navigator.clipboard.writeText(qrUrl).then(() => {
        // 2. Tampilkan notifikasi toast kecil menggunakan SweetAlert bawaan sistem kamu
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Link Berhasil Disalin!',
            text: 'Membuka WhatsApp...',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
          });
        }

        // 3. Alihkan / Buka WhatsApp Web/App setelah jeda singkat
        setTimeout(() => {
          const waUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(textMessage)}`;
          window.open(waUrl, '_blank');
        }, 1000);

      }).catch(err => {
        console.error('Gagal menyalin text: ', err);
      });
    });
  }
});
</script>
@endpush