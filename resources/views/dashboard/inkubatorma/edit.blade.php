@extends('layouts.app')

@section('content')
@php
  // Status final: Menunggu, Akan Dijadwalkan, Terjadwal, Dijadwalkan Ulang, Ditolak, Selesai
  $status = $inkubatorma->status ?? 'Menunggu';

  // format date aman untuk tampilan
  $fmtDate = function ($date, $format = 'd M Y') {
    if (empty($date)) return '—';
    try {
      return \Carbon\Carbon::parse($date)->timezone('Asia/Makassar')->format($format);
    } catch (\Throwable $e) {
      return '—';
    }
  };

  // value untuk input type="time" (harus HH:MM)
  $timeValue = function ($time) {
    if (empty($time)) return '';
    try {
      return \Carbon\Carbon::parse($time)->format('H:i');
    } catch (\Throwable $e) {
      return (string) $time;
    }
  };

  // value untuk input type="date" (harus Y-m-d)
  $dateValue = function ($date) {
    if (empty($date)) return '';
    try {
      return \Carbon\Carbon::parse($date)->timezone('Asia/Makassar')->format('Y-m-d');
    } catch (\Throwable $e) {
      return (string) $date;
    }
  };

  // nilai terpilih (ambil dari old dulu, lalu DB)
  // supaya layanan bisa tersimpan 2
  $selectedLayanan = old('layanan_id', $inkubatorma->layanan_id ?? []);

  if (!is_array($selectedLayanan)) {
      $selectedLayanan = [$selectedLayanan];
  }

  // input layanan lainnya (ambil dari old dulu, lalu DB)
  $selectedLayananLainnya = (string) old('layanan_lainnya', $inkubatorma->layanan_lainnya);

  // label dropdown awal (mengikuti detail: "Lainnya • (input)")
  $selectedLabel = 'Pilih…';
  if (!empty($selectedLayanan)) {
      $labels = collect($selectedLayanan)
          ->map(fn($id) => $layananOptions[$id] ?? $id)
          ->toArray();
      $selectedLabel = implode(', ', $labels);

      if (in_array('lainnya', $selectedLayanan) && !empty($selectedLayananLainnya)) {
          $selectedLabel .= ' • ' . $selectedLayananLainnya;
      }
  }
@endphp

<main class="max-w-7xl mx-auto px-4 py-6 space-y-6">

  {{-- PAGE HEADER --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">SIGAP Inkubatorma</h1>
      <p class="text-sm text-gray-500">Edit data pengajuan</p>
    </div>

    <div class="flex flex-wrap gap-2">
      <a href="{{ route('sigap-inkubatorma.dashboard') }}"
         class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50">
        ← Kembali
      </a>

      <button type="submit" form="formInkEdit"
        class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90">
        Simpan Perubahan
      </button>
    </div>
  </div>

  {{-- CARD STATUS --}}
  <div class="w-full">
    @include('dashboard.inkubatorma.partials.status-widget', [
      'status' => $status,
      'inkubatorma' => $inkubatorma
    ])
  </div>

  {{-- SUMMARY (4 cards) --}}
  <div class="grid grid-cols-1 gap-6 items-start">
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
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    {{-- FORM --}}
    <section class="lg:col-span-2 space-y-6">

      {{-- Global validation error summary --}}
      @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
          <p class="font-semibold">Ada input yang belum valid:</p>
          <ul class="list-disc ml-5 mt-2 space-y-1">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form id="formInkEdit"
            method="POST"
            action="{{ route('sigap-inkubatorma.update', $inkubatorma->id) }}"
            enctype="multipart/form-data"
            class="space-y-6">
        @csrf
        @method('PUT')

        {{-- DATA PENGAJU --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
          <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Data Pengaju</h3>
            <span class="text-xs text-gray-500">Wajib diisi</span>
          </div>

          <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
              <label class="text-xs font-semibold text-gray-600">Nama Pengaju <span class="text-red-600">*</span></label>
              <input name="nama_pengaju" id="nama_pengaju" type="text"
                     value="{{ old('nama_pengaju', $inkubatorma->nama_pengaju) }}" required
                     class="mt-1 w-full rounded-lg border {{ $errors->has('nama_pengaju') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
              @error('nama_pengaju') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Nomor HP / WA <span class="text-red-600">*</span></label>
              <input name="hp_pengaju" id="hp_pengaju" type="tel"
                     value="{{ old('hp_pengaju', $inkubatorma->hp_pengaju) }}" required
                     placeholder="08xxxxxxxxxx"
                     class="mt-1 w-full rounded-lg border {{ $errors->has('hp_pengaju') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
              @error('hp_pengaju') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
              <label class="text-xs font-semibold text-gray-600">Nama OPD / Unit <span class="text-red-600">*</span></label>
              <input name="opd_unit" id="opd_unit" type="text"
                     value="{{ old('opd_unit', $inkubatorma->opd_unit) }}" required
                     class="mt-1 w-full rounded-lg border {{ $errors->has('opd_unit') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
              @error('opd_unit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
          </div>
        </div>

        {{-- RINCIAN PERMINTAAN --}}
        <div class="bg-white rounded-xl border border-gray-200">
          <div class="px-5 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Rincian Permintaan</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pastikan judul & kebutuhan sudah jelas</p>
          </div>

          <div class="p-5 grid grid-cols-1 gap-4 text-sm">

            {{-- LAYANAN (CUSTOM DROPDOWN seperti INDEX + divider + field lainnya) --}}
            <div class="relative" id="layananDropdownWrap">
              <label class="text-xs font-semibold text-gray-600">Layanan <span class="text-red-600">*</span></label>

              {{-- Native select hidden untuk submit --}}
              <select name="layanan_id[]" id="layananSelectNative" multiple required class="hidden">
                <option value="">Pilih…</option>
                @foreach(($layananOptions ?? []) as $id => $nama)
                  <option value="{{ $id }}" @selected(in_array($id, $selectedLayanan ?? []))>{{ $nama }}</option>
                @endforeach
              </select>

              {{-- Button trigger --}}
              <button type="button"
                      id="layananBtn"
                      class="mt-1 w-full rounded-lg border {{ $errors->has('layanan_id') ? 'border-red-400' : 'border-gray-300' }} bg-white px-3 py-2 text-left text-sm
                             focus:outline-none focus:ring-2 focus:ring-maroon/30 focus:border-maroon
                             flex items-center justify-between gap-3">
                <span id="layananBtnLabel" class="{{ $selectedLayanan ? 'text-gray-900' : 'text-gray-500' }} truncate">
                  {{ $selectedLabel }}
                </span>

                <svg class="w-5 h-5 text-gray-500 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.24a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08z" clip-rule="evenodd"/>
                </svg>
              </button>

              {{-- Chip untuk tampilkan 2 layanan --}}
              <div id="selectedLayananContainer" class="flex flex-wrap gap-2 mt-2"></div>

              {{-- Dropdown panel --}}
              <div id="layananPanel"
                   class="hidden absolute z-30 mt-2 w-full rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden">
                <div class="max-h-72 overflow-y-auto py-2">

                  <div class="px-3 py-2 text-xs font-semibold text-gray-500">Layanan</div>

                  @foreach(($layananOptions ?? []) as $id => $nama)
                    @continue($id === 'lainnya')
                    <button type="button"
                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-maroon/10 flex items-center justify-between gap-3"
                            data-value="{{ $id }}"
                            data-label="{{ $nama }}">
                      <span class="truncate">{{ $nama }}</span>
                      <span class="text-maroon font-semibold {{ in_array($id, $selectedLayanan ?? []) ? '' : 'hidden' }}" data-check="{{ $id }}">✓</span>
                    </button>
                  @endforeach

                  {{-- Divider --}}
                  <div class="px-4 py-2"><hr class="border-gray-200"></div>

                  <div class="px-3 py-2 text-xs font-semibold text-gray-500">Lainnya</div>

                  <button type="button"
                          class="w-full text-left px-4 py-2.5 text-sm hover:bg-maroon/10 flex items-center justify-between gap-3"
                          data-value="lainnya"
                          data-label="{{ $layananOptions['lainnya'] ?? 'Lainnya' }}">
                    <span class="truncate">{{ $layananOptions['lainnya'] ?? 'Lainnya' }}</span>
                    <span class="text-maroon font-semibold {{ in_array('lainnya', $selectedLayanan ?? []) ? '' : 'hidden' }}" data-check="lainnya">✓</span>
                  </button>

                </div>
              </div>

              @error('layanan_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

              {{-- Input tambahan kalau pilih "lainnya" --}}
              <div id="layananLainnyaWrap" class="mt-3 {{ in_array('lainnya', $selectedLayanan ?? []) ? '' : 'hidden' }}">
                <label class="text-xs font-semibold text-gray-600">Spesifikasi Lainnya <span class="text-red-600">*</span></label>
                <input type="text"
                       name="layanan_lainnya"
                       id="layanan_lainnya"
                       value="{{ $selectedLayananLainnya }}"
                       placeholder="Contoh: Pendampingan Penyusunan Dokumen"
                       class="mt-1 w-full rounded-lg border {{ $errors->has('layanan_lainnya') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
                @error('layanan_lainnya') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                <p class="mt-1 text-xs text-gray-500">Akan tampil sebagai: <b>Lainnya • (input)</b></p>
              </div>
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Judul Konsultasi / Program <span class="text-red-600">*</span></label>
              <input name="judul_konsultasi" id="judul_konsultasi" type="text"
                     value="{{ old('judul_konsultasi', $inkubatorma->judul_konsultasi) }}" required
                     class="mt-1 w-full rounded-lg border {{ $errors->has('judul_konsultasi') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
              @error('judul_konsultasi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Keluhan / Permasalahan <span class="text-red-600">*</span></label>
              <textarea name="keluhan" id="keluhan" rows="4" required
                        class="mt-1 w-full rounded-lg border {{ $errors->has('keluhan') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">{{ old('keluhan', $inkubatorma->keluhan) }}</textarea>
              @error('keluhan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="text-xs font-semibold text-gray-600">Poin Asistensi yang Dibutuhkan <span class="text-red-600">*</span></label>
              <textarea name="poin_asistensi" id="poin_asistensi" rows="4" required
                        class="mt-1 w-full rounded-lg border {{ $errors->has('poin_asistensi') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">{{ old('poin_asistensi', $inkubatorma->poin_asistensi) }}</textarea>
              @error('poin_asistensi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- LAMPIRAN --}}
            <div>
                <label class="text-xs font-semibold text-gray-600">Lampiran Dokumen</label>
                <p class="mt-0.5 text-xs text-gray-400">Maks. 3 file total (PDF/DOC/DOCX). Hapus file lama atau tambah file baru.</p>

                {{-- CHIP FILE YANG SUDAH ADA --}}
                <div id="lampiranLamaContainer" class="flex flex-wrap gap-2 mt-2">
                    @if(!empty($inkubatorma->lampiran))
                        @foreach($inkubatorma->lampiran as $filePath)
                            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 border border-gray-300 text-xs text-gray-700"
                                id="chip-lama-{{ $loop->index }}">
                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank"
                                  class="max-w-[160px] truncate hover:underline">
                                    📄 {{ basename($filePath) }}
                                </a>
                                {{-- hidden input agar path lama ikut terkirim, bisa di-disable saat hapus --}}
                                <input type="hidden"
                                      name="lampiran_lama[]"
                                      value="{{ $filePath }}"
                                      id="input-lama-{{ $loop->index }}">
                                <button type="button"
                                        onclick="hapusLampiran({{ $loop->index }}, '{{ $filePath }}')"
                                        class="ml-1 text-gray-400 hover:text-red-500 font-bold text-base leading-none">×</button>
                            </div>
                        @endforeach
                    @else
                        <p class="text-xs text-gray-400 italic">Belum ada lampiran.</p>
                    @endif
                </div>

                {{-- INPUT HAPUS (diisi via JS saat klik ×) --}}
                <div id="hapusLampiranContainer"></div>

                {{-- UPLOAD FILE BARU --}}
                <input type="file"
                      name="lampiran[]"
                      id="lampiranEditInput"
                      multiple
                      accept=".pdf,.doc,.docx"
                      class="mt-3 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                              file:text-xs file:font-semibold file:bg-maroon file:text-white
                              hover:file:bg-maroon-800">

                {{-- Chip file baru yang dipilih --}}
                <div id="lampiranBaruChips" class="flex flex-wrap gap-2 mt-2"></div>
                <p id="lampiranEditError" class="mt-1 text-xs text-red-600 hidden">⚠ Total lampiran tidak boleh lebih dari 3 file.</p>
                <p id="lampiranEditErrorFormat" class="mt-1 text-xs text-red-600 hidden">⚠ Hanya file PDF, DOC, atau DOCX yang diperbolehkan.</p>
<p id="lampiranEditErrorSize" class="mt-1 text-xs text-red-600 hidden">⚠ Ukuran file tidak boleh melebihi 100MB.</p>

                @error('lampiran')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="text-xs font-semibold text-gray-600">Tanggal Usulan <span class="text-red-600">*</span></label>
                <input name="tanggal_usulan" id="tanggal_usulan" type="date"
                       value="{{ old('tanggal_usulan', $dateValue($inkubatorma->tanggal_usulan)) }}"
                       required
                       class="mt-1 w-full rounded-lg border {{ $errors->has('tanggal_usulan') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
                @error('tanggal_usulan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>

              <div>
                <label class="text-xs font-semibold text-gray-600">Jam Usulan (WITA) <span class="text-red-600">*</span></label>
                <input name="jam_usulan" id="jam_usulan" type="time"
                       value="{{ old('jam_usulan', $timeValue($inkubatorma->jam_usulan)) }}"
                       required
                       class="mt-1 w-full rounded-lg border {{ $errors->has('jam_usulan') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
                @error('jam_usulan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="text-xs font-semibold text-gray-600">Metode Pertemuan <span class="text-red-600">*</span></label>
                <select name="metode_usulan" id="metode_usulan" required
                        class="mt-1 w-full rounded-lg border {{ $errors->has('metode_usulan') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">
                  <option value="online"  @selected(old('metode_usulan', $inkubatorma->metode_usulan) === 'online')>Online (Zoom/Meet)</option>
                  <option value="offline" @selected(old('metode_usulan', $inkubatorma->metode_usulan) === 'offline')>Tatap Muka (Offline)</option>
                </select>
                @error('metode_usulan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
              </div>

              <div class="relative">
                <label class="text-xs font-semibold text-gray-600">Target Personil (opsional)</label>

                <input id="pegawaiInput" type="text"
                       autocomplete="off"
                       placeholder="Ketik nama pegawai..."
                       value="{{ old('target_personil_usulan', $inkubatorma->target_personil_usulan) }}"
                       class="mt-1 w-full rounded-lg border {{ $errors->has('pegawai_id') ? 'border-red-400' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-maroon focus:border-maroon">

                <input type="hidden" name="pegawai_id" id="pegawai_id"
                  value="{{ old('pegawai_id', $inkubatorma->pegawai_id) }}">
                <input type="hidden" name="target_personil_usulan" id="target_personil_usulan"
                  value="{{ old('target_personil_usulan', $inkubatorma->target_personil_usulan) }}">

                <div id="pegawaiDropdown"
                     class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-sm hidden max-h-48 overflow-y-auto">
                </div>

                @error('pegawai_id')
                  <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
              </div>
            </div>

          </div>
        </div>

        {{-- ACTIONS BOTTOM --}}
        <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
          <a href="{{ route('sigap-inkubatorma.dashboard') }}"
             class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-semibold hover:bg-gray-50 text-center">
            Batal
          </a>

          <button type="submit"
            class="px-4 py-2 rounded-lg bg-maroon text-white text-sm font-semibold hover:opacity-90">
            Simpan Perubahan
          </button>
        </div>

      </form>
    </section>

    {{-- RIGHT SIDEBAR: TIMELINE --}}
    <aside class="space-y-6">
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
  // ===== PEGAWAI AUTOCOMPLETE =====
  const pegawaiList = @json($employees);

  const pegawaiInput = document.getElementById("pegawaiInput");
  const pegawaiDropdown = document.getElementById("pegawaiDropdown");
  const pegawaiIdInput = document.getElementById("pegawai_id");

  function renderPegawai(list){
    pegawaiDropdown.innerHTML = "";

    if(!list || list.length === 0){
      pegawaiDropdown.classList.add("hidden");
      return;
    }

    list.forEach(emp => {
      const div = document.createElement("div");
      div.className = "px-3 py-2 text-sm cursor-pointer hover:bg-maroon/10";
      div.textContent = emp.name;

      div.onclick = () => {
        pegawaiInput.value = emp.name;
        pegawaiIdInput.value = emp.id;
        document.getElementById("target_personil_usulan").value = emp.name;
        pegawaiDropdown.classList.add("hidden");
      };

      pegawaiDropdown.appendChild(div);
    });

    pegawaiDropdown.classList.remove("hidden");
  }

  pegawaiInput?.addEventListener("focus", () => renderPegawai(pegawaiList));

  pegawaiInput?.addEventListener("keyup", () => {
    const keyword = (pegawaiInput.value || '').toLowerCase();
    const filtered = (pegawaiList || []).filter(emp =>
      (emp.name || '').toLowerCase().includes(keyword)
    );
    renderPegawai(filtered);
  });

  document.addEventListener("click", (e) => {
    if(!pegawaiInput || !pegawaiDropdown) return;
    if(!pegawaiInput.contains(e.target) && !pegawaiDropdown.contains(e.target)){
      pegawaiDropdown.classList.add("hidden");
    }
  });

  // ===== DROPDOWN LAYANAN (CUSTOM) =====
  const wrap   = document.getElementById('layananDropdownWrap');
  const btn    = document.getElementById('layananBtn');
  const panel  = document.getElementById('layananPanel');
  const label  = document.getElementById('layananBtnLabel');
  const native = document.getElementById('layananSelectNative');

  const lainnyaWrap = document.getElementById('layananLainnyaWrap');
  const lainnyaInput = document.getElementById('layanan_lainnya');

  const layananOptions = @json($layananOptions);

  if (!wrap || !btn || !panel || !label || !native) return;

  function openPanel()  { panel.classList.remove('hidden'); }
  function closePanel() { panel.classList.add('hidden'); }

  function updateLainnyaVisibility(val) {
    const isLainnya = val === 'lainnya';
    if (!lainnyaWrap) return;

    lainnyaWrap.classList.toggle('hidden', !isLainnya);

    // kalau bukan lainnya, kosongkan input biar tidak nyangkut (opsional)
    if (!isLainnya && lainnyaInput) {
      lainnyaInput.value = '';
    }
  }

  function setSelected(value, text) {
    native.value = value;

    // label display
    label.textContent = text || 'Pilih…';
    label.classList.toggle('text-gray-500', !value);
    label.classList.toggle('text-gray-900', !!value);

    // update checkmarks
    panel.querySelectorAll('[data-check]').forEach(el => {
      el.classList.toggle('hidden', el.getAttribute('data-check') !== value);
    });

    updateLainnyaVisibility(value);
  }

  // Toggle panel
  btn.addEventListener('click', () => {
    if (panel.classList.contains('hidden')) openPanel();
    else closePanel();
  });

  // Click option
  panel.addEventListener('click', (e) => {
    const opt = e.target.closest('[data-value]');
    if (!opt) return;

    const val = opt.getAttribute('data-value') || '';
    const txt = opt.getAttribute('data-label') || '';

    setSelected(val, txt);
    closePanel();
  });

  // Close on outside click
  document.addEventListener('click', (e) => {
    if (!wrap.contains(e.target)) closePanel();
  });

  // Close on escape
  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closePanel();
  });

  // Inisialisasi awal: sync checkmark + visibility
  // const initVal = native.value || '';
  // updateLainnyaVisibility(initVal);

  // if (initVal && initVal !== 'lainnya') {
  //   const initText = native.options[native.selectedIndex]?.textContent || initVal;
  //   setSelected(initVal, initText);
  // } else if (!initVal) {
  //   setSelected('', 'Pilih…');
  // } else {
  //   native.value = 'lainnya';
  //   panel.querySelectorAll('[data-check]').forEach(el => {
  //     el.classList.toggle('hidden', el.getAttribute('data-check') !== 'lainnya');
  //   });
  // }

  // Supaya muncul 2 layanan di halaman edit
  let selectedValues = @json($selectedLayanan ?? []);

  function toggleLainnya(){
    const show = selectedValues.includes('lainnya');

    if(lainnyaWrap){
        lainnyaWrap.classList.toggle('hidden', !show);
    }

    if(lainnyaInput){
        lainnyaInput.required = show;

        if(!show){
            lainnyaInput.value = '';
        }
    }
  }

  const container = document.getElementById('selectedLayananContainer');

  function updateUI(){

      // update native select
      Array.from(native.options).forEach(opt => {
          opt.selected = selectedValues.includes(opt.value);
      });

      // update label
      label.textContent = selectedValues.length
          ? selectedValues.map(v => layananOptions[v]).join(', ')
          : 'Pilih maksimal 2 layanan';

      // render chips
      container.innerHTML = '';

      selectedValues.forEach(val => {

          const option = native.querySelector(`option[value="${val}"]`);
          if(!option) return;

          const chip = document.createElement('div');

          chip.className =
          "flex items-center gap-2 px-3 py-1 rounded-full bg-maroon/10 text-maroon text-sm font-semibold";

          chip.innerHTML = `
              ${option.textContent}
              <button type="button" class="font-bold">×</button>
          `;

          chip.querySelector('button').onclick = () => {
              selectedValues = selectedValues.filter(v => v !== val);
              updateUI();
          };

          container.appendChild(chip);

      });

      toggleLainnya();
  }

  panel.addEventListener('click', (e)=>{

      const opt = e.target.closest('[data-value]');
      if(!opt) return;

      const value = opt.dataset.value;

      if(!selectedValues.includes(value)){

          if(selectedValues.length >= 2){
              alert('Maksimal 2 layanan');
              return;
          }

          selectedValues.push(value);

      }else{

          selectedValues = selectedValues.filter(v => v !== value);

      }

      toggleLainnya();
      updateUI();

  });

  updateUI();
  toggleLainnya();

  // ===== LAMPIRAN EDIT — FILE LAMA + FILE BARU =====
  const hapusContainer = document.getElementById('hapusLampiranContainer');
  const errorMsg       = document.getElementById('lampiranEditError');
  const inputBaru      = document.getElementById('lampiranEditInput');
  const chipsBaru      = document.getElementById('lampiranBaruChips');
  const MAX            = 3;

  const errorFormat   = document.getElementById('lampiranEditErrorFormat');
  const errorSize     = document.getElementById('lampiranEditErrorSize');
  const formatAllowed = ['application/pdf', 'application/msword',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
  const MAX_MB = 100;

  // hitung file lama yang masih aktif (belum dihapus)
  let jumlahLama = {{ count($inkubatorma->lampiran ?? []) }};
  let selectedFiles = [];

  // fungsi hapus file lama (dipanggil dari onclick di blade)
  window.hapusLampiran = function (index, path) {
    const chip = document.getElementById('chip-lama-' + index);
    if (chip) chip.style.display = 'none';

    const inputLama = document.getElementById('input-lama-' + index);
    if (inputLama) inputLama.disabled = true;

    const hidden = document.createElement('input');
    hidden.type  = 'hidden';
    hidden.name  = 'hapus_lampiran[]';
    hidden.value = path;
    hapusContainer.appendChild(hidden);

    jumlahLama--;
    errorMsg.classList.add('hidden');
  };

  if (inputBaru) {
      inputBaru.addEventListener('change', function () {
          errorMsg.classList.add('hidden');
          errorFormat.classList.add('hidden');
          errorSize.classList.add('hidden');

          const incoming = Array.from(this.files);
          let adaFormatSalah = false;
          let adaMelebihi    = false;

          incoming.forEach(f => {
              if (!formatAllowed.includes(f.type)) {
                  adaFormatSalah = true;
                  return;
              }
              if (f.size > MAX_MB * 1024 * 1024) {
                  adaMelebihi = true;
                  return;
              }
              if (!selectedFiles.find(x => x.name === f.name)) {
                  selectedFiles.push(f);
              }
          });

          if (adaFormatSalah) errorFormat.classList.remove('hidden');
          if (adaMelebihi)    errorSize.classList.remove('hidden');

          const total = jumlahLama + selectedFiles.length;
          if (total > MAX) {
              const boleh = Math.max(0, MAX - jumlahLama);
              selectedFiles = selectedFiles.slice(0, boleh);
              errorMsg.classList.remove('hidden');
          }

          syncInput();
          renderChipsBaru();
      });
  }

  function removeNewFile(name) {
    selectedFiles = selectedFiles.filter(f => f.name !== name);
    syncInput();
    renderChipsBaru();
    errorMsg.classList.add('hidden');
  }

  function syncInput() {
    if (!inputBaru) return;
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    inputBaru.files = dt.files;
  }

  function renderChipsBaru() {
    if (!chipsBaru) return;
    chipsBaru.innerHTML = '';
    selectedFiles.forEach(file => {
      const sizeMB = (file.size / 1024 / 1024).toFixed(2);
      const chip   = document.createElement('div');
      chip.className = 'flex items-center gap-2 px-3 py-1.5 rounded-full bg-maroon/10 border border-maroon/20 text-xs text-maroon';
      chip.innerHTML = `
        <span class="max-w-[160px] truncate font-medium">📄 ${file.name}</span>
        <span class="text-maroon/60">${sizeMB}MB</span>
        <button type="button" class="ml-1 text-maroon/50 hover:text-red-500 font-bold text-base leading-none">×</button>
      `;
      chip.querySelector('button').addEventListener('click', () => removeNewFile(file.name));
      chipsBaru.appendChild(chip);
    });
  }
})();
</script>
@endsection