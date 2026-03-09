@extends('layouts.app')

@section('content')
@php
  $status = $inkubatorma->status ?? 'Menunggu';

  // Jika sudah selesai, verifikasi dikunci
  $isClosed = ($status === 'Selesai');

  $metodeLabel = function ($val) {
    return match ($val) {
      'online'  => 'Online (Zoom/Meet)',
      'offline' => 'Tatap Muka (Offline)',
      default   => '—',
    };
  };

  $fmtDate = function ($date, $format = 'd M Y') {
    if (empty($date)) return '—';
    try {
      return \Carbon\Carbon::parse($date)->timezone('Asia/Makassar')->format($format);
    } catch (\Throwable $e) {
      return '—';
    }
  };

  $timeValue = function ($time) {
    if (empty($time)) return '';
    try {
      return \Carbon\Carbon::parse($time)->format('H:i');
    } catch (\Throwable $e) {
      return (string) $time;
    }
  };

  $fmtTime = function ($time) {
    if (empty($time)) return '—';
    try {
      return \Carbon\Carbon::parse($time)->format('H:i') . ' WITA';
    } catch (\Throwable $e) {
      return (string) $time;
    }
  };

  $layananKey   = (string) ($inkubatorma->layanan_id ?? '');
  $layananBase  = $layananOptions[$layananKey] ?? '—';

  // ✅ tampil seperti detail: "Lainnya • (input user)"
  $layananLainnya = trim((string) ($inkubatorma->layanan_lainnya ?? ''));

  if ($layananKey === 'lainnya' && $layananLainnya !== '') {
    $layananLabel = $layananBase . ' • ' . $layananLainnya;
  } else {
    $layananLabel = $layananBase;
  }
  // Untuk dropdown search PIC: tampilkan nama awal jika sudah ada PIC
  $initialPicId = old('pic_employee_id', $inkubatorma->pic_employee_id);

  // Ambil nama dari relasi yang benar (sesuaikan dengan controller: picUser)
  $defaultPicName = $inkubatorma->picUser?->name;

  // Fallback: kalau relasi belum ada / tidak ke-load, cari dari list employees yang sudah dikirim controller
  if (empty($defaultPicName) && !empty($initialPicId)) {
    $defaultPicName = collect($employees ?? [])
      ->firstWhere('id', (int) $initialPicId)
      ->name ?? null;
  }

  $initialPicName = old('pic_employee_name', $defaultPicName);
@endphp

<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">

  {{-- HEADER --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-2xl font-bold text-gray-800">SIGAP Inkubatorma</h1>
        <span class="px-2 py-1 rounded bg-maroon/10 text-maroon text-xs font-semibold">Panel Verifikasi</span>
      </div>
      <p class="text-sm text-gray-500">Review pengajuan, catatan verifikator, dan ubah status</p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="{{ route('sigap-inkubatorma.dashboard') }}"
         class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
        ← Kembali
      </a>

      {{-- tombol submit form (atas) --}}
      <button type="submit" form="formVerifikasi"
        @disabled($isClosed)
        class="px-4 py-2 rounded-lg text-sm font-semibold
               {{ $isClosed ? 'bg-gray-200 text-gray-500 cursor-not-allowed opacity-60' : 'bg-maroon text-white hover:opacity-90' }}">
        Simpan Verifikasi
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

  {{-- SUMMARY (4 cards) --}}
  <div class="grid grid-cols-1 gap-6 items-start">

    {{-- 4 Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Kode Pengajuan</p>
        <p class="mt-1 text-xl font-extrabold text-maroon">
          {{ $inkubatorma->kode ?? '—' }}
        </p>
        <p class="mt-1 text-xs text-gray-500">Identitas pengajuan</p>
      </div>

      <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-sm text-gray-500">Nama Pengaju</p>
        <p class="mt-1 text-xl font-extrabold text-maroon">
          {{ $inkubatorma->nama_pengaju ?? '—' }}
        </p>
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
  </div>

  {{-- MAIN GRID --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT --}}
    <section class="lg:col-span-2 space-y-6">

      {{-- DATA PENGAJU --}}
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b">
          <h3 class="font-semibold text-gray-800">Data Pengaju</h3>
          <p class="text-xs text-gray-500 mt-0.5">Pastikan kontak valid untuk konfirmasi</p>
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
          <div class="sm:col-span-2">
            <p class="text-xs font-semibold text-gray-500">Instansi / OPD / Unit</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->opd_unit ?? '—' }}</p>
          </div>
        </div>
      </div>

      {{-- RINCIAN PERMINTAAN --}}
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b">
          <h3 class="font-semibold text-gray-800">Rincian Permintaan</h3>
          <p class="text-xs text-gray-500 mt-0.5">Ringkasan kebutuhan, masalah, dan target output</p>
        </div>

        <div class="p-5 space-y-4 text-sm">
          <div>
            <p class="text-xs font-semibold text-gray-500">Layanan yang Diminta</p>
            <p class="mt-1 font-semibold text-gray-800">{{ $layananLabel }}</p>
          </div>

          <div>
            <p class="text-xs font-semibold text-gray-500">Judul Konsultasi</p>
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
              <p class="text-xs font-semibold text-gray-500">Target Personil (opsional)</p>
              <p class="mt-1 font-semibold text-gray-800">{{ $inkubatorma->target_personil_usulan ?? '—' }}</p>
            </div>
          </div>

          {{-- DATA FINAL (READ-ONLY) --}}
          <div class="pt-2">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-base font-semibold text-gray-800">Jadwal & PIC (Final)</p>
                <p class="text-xs text-gray-500 mt-0.5">Terisi jika sudah diset oleh verifikator</p>
              </div>
              @php
                $finalReady = ($inkubatorma->verifikator_employee_id || $inkubatorma->tanggal_final || $inkubatorma->jam_final);
              @endphp
            </div>

            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                <p class="text-xs font-semibold text-gray-500">PIC</p>
                <p class="mt-1 font-semibold text-gray-900">{{ $inkubatorma->verifikatorUser?->name ?? '—' }}</p>
              </div>
            </div>
          </div>

        </div>
      </div>

      {{-- AKSI VERIFIKASI (FORM) --}}
      <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b">
          <h3 class="font-semibold text-gray-800">Aksi Verifikasi</h3>
          <p class="text-xs text-gray-500 mt-0.5">Perubahan resmi setelah menekan Simpan</p>
        </div>

        <div class="p-5 text-sm space-y-4">

          @if ($isClosed)
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-xs text-gray-700">
              <p class="font-semibold">Konsultasi sudah ditutup (Selesai).</p>
              <p class="mt-1">Verifikasi dikunci dan tidak dapat diubah lagi.</p>
            </div>
          @endif

          @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-800">
              <p class="font-semibold">Ada input yang belum valid.</p>
              <ul class="list-disc ml-4 mt-1 space-y-1">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form id="formVerifikasi" method="POST" action="{{ route('sigap-inkubatorma.verifikasi.update', $inkubatorma->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
              <label class="text-xs font-semibold text-gray-600">Ubah Status</label>
              <select name="status" id="statusSelect"
                @disabled($isClosed)
                class="mt-1 w-full rounded-lg border {{ $errors->has('status') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
                @php
                  $opts = ['Menunggu','Akan Dijadwalkan','Terjadwal','Dijadwalkan Ulang','Ditolak','Selesai'];
                  $selectedStatus = old('status', $inkubatorma->status ?? 'Menunggu');
                @endphp
                @foreach($opts as $opt)
                  <option value="{{ $opt }}" @selected($selectedStatus === $opt)>{{ $opt }}</option>
                @endforeach
              </select>
              @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              <p class="mt-1 text-[11px] text-gray-500">
                Catatan: status ini adalah <b>status tersimpan</b> setelah Simpan.
              </p>
            </div>

            {{-- Konfirmasi penutupan (muncul jika pilih Selesai) --}}
            <div id="closeConfirmBox" class="rounded-lg border border-amber-200 bg-amber-50 p-4 space-y-2 hidden">
              <p class="text-xs font-semibold text-amber-800">Konfirmasi Penutupan</p>
              <p class="text-[11px] text-amber-800/90">
                Menutup konsultasi akan mengunci verifikasi dan tidak bisa diubah lagi.
                Untuk konfirmasi, ketik <b>TUTUP</b> di bawah ini.
              </p>
              <input type="text" name="close_confirm" id="closeConfirmInput"
                     value="{{ old('close_confirm') }}"
                     placeholder="Ketik TUTUP"
                     @disabled($isClosed)
                     class="mt-1 w-full rounded-lg border border-amber-300 px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
              <p class="text-[11px] text-amber-700">Case-insensitive. Contoh: tutup / TUTUP</p>
            </div>

            {{-- Jadwal Final --}}
            <div id="scheduleBox" class="rounded-lg border border-gray-200 p-4 bg-gray-50 space-y-3">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <p class="text-xs font-semibold text-gray-700">Jadwal Final</p>
                  <p class="text-[11px] text-gray-500 mt-0.5">Isi tanggal, jam, metode, dan lokasi/link.</p>
                </div>
              </div>

              {{-- ✅ PIC SEARCH DROPDOWN --}}
              <div class="relative">
                <label class="text-xs font-semibold text-gray-600">PIC / Penanggung Jawab (Verifikator)</label>

                <input type="hidden" name="pic_employee_id" id="pic_employee_id" value="{{ $initialPicId }}">
                <input type="hidden" name="pic_employee_name" id="pic_employee_name" value="{{ $initialPicName }}">

                <input id="picInput" type="text"
                  autocomplete="off"
                  placeholder="Ketik nama verifikator..."
                  value="{{ $initialPicName }}"
                  @disabled($isClosed)
                  class="mt-1 w-full rounded-lg border {{ $errors->has('pic_employee_id') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">

                <button type="button" id="picClear"
                  class="hidden absolute right-2 top-9 text-gray-400 hover:text-gray-700"
                  aria-label="Hapus PIC">✕</button>

                <div id="picDropdown"
                  class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-sm hidden max-h-48 overflow-y-auto">
                </div>

                @error('pic_employee_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>

              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="text-xs font-semibold text-gray-600">Tanggal</label>
                  <input name="tanggal_final" id="finalDate" type="date"
                    value="{{ old('tanggal_final', optional($inkubatorma->tanggal_final)->format('Y-m-d')) }}"
                    @disabled($isClosed)
                    class="mt-1 w-full rounded-lg border {{ $errors->has('tanggal_final') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm">
                  @error('tanggal_final') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                  <label class="text-xs font-semibold text-gray-600">Jam</label>
                  <input name="jam_final" id="finalTime" type="time"
                    value="{{ old('jam_final', $timeValue($inkubatorma->jam_final)) }}"
                    @disabled($isClosed)
                    class="mt-1 w-full rounded-lg border {{ $errors->has('jam_final') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm">
                  @error('jam_final') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
              </div>

              <div>
                <label class="text-xs font-semibold text-gray-600">Metode</label>
                <select name="metode_final" id="finalMode"
                  @disabled($isClosed)
                  class="mt-1 w-full rounded-lg border {{ $errors->has('metode_final') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm">
                  <option value="online"  @selected(old('metode_final', $inkubatorma->metode_final) === 'online')>Online</option>
                  <option value="offline" @selected(old('metode_final', $inkubatorma->metode_final) === 'offline')>Tatap Muka (Offline)</option>
                </select>
                @error('metode_final') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="text-xs font-semibold text-gray-600">Lokasi / Link</label>
                <input name="lokasi_link_final" id="finalPlace" type="text"
                  value="{{ old('lokasi_link_final', $inkubatorma->lokasi_link_final) }}"
                  placeholder="Contoh: Ruang Rapat BRIDA / Link Zoom"
                  @disabled($isClosed)
                  class="mt-1 w-full rounded-lg border {{ $errors->has('lokasi_link_final') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm">
                @error('lokasi_link_final') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Catatan Verifikator</label>
              <textarea name="catatan_verifikator" id="noteToUser" rows="3"
                placeholder="Contoh: Mohon lengkapi deskripsi inovasi 1 halaman dan lampiran pendukung."
                @disabled($isClosed)
                class="mt-1 w-full rounded-lg border {{ $errors->has('catatan_verifikator') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">{{ old('catatan_verifikator', '') }}</textarea>
              @error('catatan_verifikator') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2">
              <a href="{{ route('sigap-inkubatorma.dashboard') }}"
                class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
                Batal
              </a>
              <button type="submit"
                @disabled($isClosed)
                class="px-4 py-2 rounded-lg text-sm font-semibold
                       {{ $isClosed ? 'bg-gray-200 text-gray-500 cursor-not-allowed opacity-60' : 'bg-maroon text-white hover:opacity-90' }}">
                Simpan
              </button>
            </div>

          </form>
        </div>
      </div>

    </section>

    {{-- RIGHT --}}
    <aside class="space-y-6">

      {{-- TIMELINE (CARD) --}}
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

<script>
(function () {
  // =========================
  // 1) Toggle Jadwal Final + Konfirmasi Tutup
  // =========================
  const statusSelect = document.getElementById('statusSelect');
  const noteToUser   = document.getElementById('noteToUser');
  const scheduleBox  = document.getElementById('scheduleBox');
  const closeBox     = document.getElementById('closeConfirmBox');
  const closeInput   = document.getElementById('closeConfirmInput');

  function needSchedule(status) {
    return ['Terjadwal', 'Dijadwalkan Ulang'].includes(status);
  }

  function needCloseConfirm(status) {
    return status === 'Selesai';
  }

  function applyBoxes() {
    const st = statusSelect ? statusSelect.value : 'Menunggu';

    if (scheduleBox) {
      scheduleBox.style.display = needSchedule(st) ? '' : 'none';
    }

    if (closeBox) {
      const show = needCloseConfirm(st);
      closeBox.classList.toggle('hidden', !show);
      if (!show && closeInput) closeInput.value = '';
    }
  }

  if (statusSelect) {
    statusSelect.addEventListener('change', applyBoxes);
    applyBoxes();
  }

  // =========================
  // 2) PIC Dropdown (SAMA seperti index: pakai list dari server)
  // =========================
  const picList = @json($employees); // expected: [{id,name}, ...]
  const picInput = document.getElementById('picInput');
  const picDropdown = document.getElementById('picDropdown');
  const picId = document.getElementById('pic_employee_id');
  const picName = document.getElementById('pic_employee_name');
  const picClear = document.getElementById('picClear');

  function setClearVisible() {
    const has = (picId?.value || '').trim() !== '' || (picInput?.value || '').trim() !== '';
    picClear?.classList.toggle('hidden', !has);
  }

  function closePic() {
    picDropdown.classList.add('hidden');
    picDropdown.innerHTML = '';
  }

  function openPic() {
    picDropdown.classList.remove('hidden');
  }

  function renderPic(items) {
    picDropdown.innerHTML = '';
    if (!items.length) {
      closePic();
      return;
    }

    items.forEach(emp => {
      const item = document.createElement('div');
      item.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-maroon/10';
      item.textContent = emp.name;

      item.onclick = () => {
        picInput.value = emp.name;
        picId.value = emp.id;
        picName.value = emp.name;
        setClearVisible();
        closePic();
      };

      picDropdown.appendChild(item);
    });

    openPic();
  }

  picInput?.addEventListener('input', () => {
    const keyword = (picInput.value || '').toLowerCase().trim();

    // reset dulu setiap user mengetik
    picId.value = '';
    picName.value = '';
    setClearVisible();

    const filtered = (picList || []).filter(emp =>
      (emp.name || '').toLowerCase().includes(keyword)
    );

    renderPic(filtered);
  });

  picInput?.addEventListener('focus', () => {
    setClearVisible();
    renderPic(picList);
  });

  document.addEventListener('click', (e) => {
    if (!picInput || !picDropdown) return;
    if (picInput.contains(e.target) || picDropdown.contains(e.target)) return;
    closePic();
  });

  picClear?.addEventListener('click', () => {
    picInput.value = '';
    picId.value = '';
    picName.value = '';
    setClearVisible();
    closePic();
    picInput.focus();
  });

  setClearVisible();

  let lastStatus = statusSelect ? statusSelect.value : '';

  statusSelect?.addEventListener('change', () => {
    const current = statusSelect.value;

    // kalau status berubah, kosongkan catatan (biar catatan "per status")
    if (noteToUser && current !== lastStatus) {
      noteToUser.value = '';
    }

    lastStatus = current;
  });
})();
</script>
@endsection