{{-- resources/views/dashboard/inovasi/evidence.blade.php --}}
@extends('layouts.app')

@section('content')
  <style>
    .scrollbar-thin::-webkit-scrollbar{height:8px;width:8px}
    .scrollbar-thin::-webkit-scrollbar-thumb{background:#e5e7eb;border-radius:8px}
    details[open] .chev { transform: rotate(180deg); }
  </style>
<style>
  /* select tetap normal */
  .paramSelect {
    max-width: 100%;
  }

  /* INI YANG PENTING */
  .paramSelect option {
    display: block;
    max-width: 100vw;       /* jangan lebih lebar dari layar */
    overflow-x: auto;       /* scroll ke samping */
    white-space: nowrap;    /* tetap satu baris */
  }
</style>




  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">
          Bukti Evidence — {{ $inovasi->judul }}
        </h1>
        <p class="text-sm text-gray-600 mt-1">
          Pilih <span class="font-semibold">Parameter</span> tiap indikator & unggah
          <span class="font-semibold">Data Pendukung</span>.
          <span class="text-gray-500">“Informasi” dan “Bobot” otomatis mengikuti pilihan.</span>
        </p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('sigap-inovasi.show', $inovasi->id) }}"
           class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Kembali ke Detail</a>
        <button id="btnSubmitEvidence"
                form="evidenceForm"
                class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
          Simpan Evidence
        </button>

      </div>
    </div>

    @if(session('success'))
      <div class="mt-2 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
        {{ session('success') }}
      </div>
    @endif
  </section>

  <section class="max-w-7xl mx-auto px-4 pb-10">
    <div class="grid lg:grid-cols-4 gap-5">
      <div class="lg:col-span-3 space-y-4">
        <form id="evidenceForm"
              action="{{ route('evidence.save', $inovasi->id) }}"
              method="POST" enctype="multipart/form-data">
          @csrf

          @foreach($items as $it)
            @php
              $no = (int) $it['no'];
              $selectedLabel  = $it['selected_label'] ?? null;
              $selectedWeight = (int)($it['selected_weight'] ?? 0);
              // dipakai untuk badge kanan saat load awal
              $preInfo  = $selectedLabel ?: '—';
              $preBobot = $selectedWeight ?: 0;
              // $accept = '';
              // if (preg_match('/Upload Video/i', $it['jenis_file'] ?? '')) $accept = '.mp4,.mov';
              // elseif (preg_match('/Foto\/Gambar/i', $it['jenis_file'] ?? '')) $accept = '.jpg,.jpeg,.png,.gif,.webp,.svg';
              // elseif (preg_match('/Dokumen\/Foto\/Gambar/i', $it['jenis_file'] ?? '')) $accept = '.pdf,.jpg,.jpeg,.png';
              // elseif (preg_match('/Dokumen PDF/i', $it['jenis_file'] ?? '')) $accept = '.pdf';
              $accept = '.pdf';
            @endphp

            <details class="bg-white border border-gray-200 rounded-2xl overflow-hidden" {{ $no <= 2 ? 'open' : '' }}>
              <summary class="cursor-pointer select-none px-4 py-3 flex items-start gap-3">
                <span class="shrink-0 inline-flex items-center justify-center w-7 h-7 rounded-md bg-maroon text-white text-xs font-bold">
                  {{ $no }}
                </span>
                <div class="flex-1">
                  <h3 class="font-semibold text-gray-900">{{ $it['indikator'] }}</h3>
                  @if(!empty($it['keterangan']))
                    <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $it['keterangan'] }}</p>
                  @endif
                </div>
                <div class="flex items-center gap-3">
                  <span class="infoTag text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-700">
                    {{ $preInfo }}
                  </span>
                  <span class="weightTag text-xs px-2 py-0.5 rounded bg-maroon/10 text-maroon font-semibold">
                    Skor: {{ $preBobot }}
                  </span>
                  <svg class="chev w-4 h-4 text-gray-400 transition-transform"
                       viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M6 9l6 6 6-6" stroke-width="2" />
                  </svg>
                </div>
              </summary>

              <div class="px-4 pb-4 pt-3 border-t bg-white">
                <div class="grid md:grid-cols-2 gap-4">
                  <div class="rounded-lg border p-3">
                    <p class="text-xs text-gray-500 mb-1">
                      Jenis File yang diharapkan
                    </p>
                    <p class="text-sm font-medium text-gray-800">
                      Dokumen PDF
                    </p>
                  </div>

                  <div class="rounded-lg border p-3">
                    <label class="text-xs text-gray-500">Pilih Parameter</label>
                    <select name="param_id[{{ $no }}]"
                            class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-2 py-2 paramSelect"
                            data-no="{{ $no }}">
                      <option value="">— Pilih parameter —</option>
                      @foreach($it['params'] as $p)
                        @php
                          $sel = '';
                          if ($selectedLabel && $selectedLabel === $p['label']) $sel = 'selected';
                          elseif (!$selectedLabel && $selectedWeight && $selectedWeight == $p['weight']) $sel = 'selected';
                        @endphp
                        <option
                          value="{{ $p['id'] }}"
                          data-weight="{{ (int)$p['weight'] }}"
                          data-label="{{ $p['label'] }}"
                          title="{{ $p['label'] }}"
                          {{ $sel }}
                        >
                          {{ $p['label'] }}
                        </option>

                      @endforeach
                    </select>
                    {{-- Kalau mau support input manual, siapkan ini (opsional) --}}
                    <input type="hidden" name="parameter_label[{{ $no }}]" value="">
                    <input type="hidden" name="parameter_weight[{{ $no }}]" value="">
                  </div>

                  <div class="md:col-span-2 rounded-lg border p-3">
                    <p class="text-xs text-gray-500 mb-2">Data Pendukung</p>
                    <div class="grid sm:grid-cols-3 gap-3">
                      <div class="sm:col-span-2">
                        <input name="deskripsi[{{ $no }}]"
                               class="w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                               placeholder="Deskripsi singkat (opsional)"
                               value="{{ $it['deskripsi'] ?? '' }}">
                      </div>
                      <div>
                      <button type="button"
                        onclick="openDocModal({{ $no }})"
                        class="px-3 py-2 rounded-md border text-sm hover:bg-gray-50">
                        ➕ Tambah Dokumen
                      </button>
                      <div id="docList-{{ $no }}" class="mt-2 space-y-2"></div>
                      @if(!empty($it['files']))
                        <div class="mt-3 space-y-2">
                          @foreach($it['files'] as $file)
                            <div class="flex items-start gap-3 border rounded-lg px-3 py-2 text-xs"
                                data-file-row>

                              {{-- ICON --}}
                              <span class="mt-0.5">📄</span>

                              {{-- INFO --}}
                              <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 truncate"
                                  title="{{ $file['nomor_surat'] }}">
                                  {{ $file['nomor_surat'] ?? '—' }}
                                </p>

                                <p class="text-gray-500">
                                  {{ $file['tanggal_surat'] ?? '—' }}
                                </p>

                                <p class="text-gray-700 truncate"
                                  title="{{ $file['tentang'] }}">
                                  {{ \Illuminate\Support\Str::limit($file['tentang'] ?? '—', 20) }}
                                </p>
                              </div>

                              {{-- AKSI --}}
                              <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ $file['url'] }}"
                                  target="_blank"
                                  class="text-maroon underline">
                                  View
                                </a>

                                <button type="button"
                                        class="text-rose-600 hover:text-rose-800"
                                        onclick="markFileDelete(this)">
                                  🗑️
                                </button>

                                {{-- hidden flag delete --}}
                                <input type="hidden"
                                      name="delete_files[{{ $no }}][{{ $file['id'] }}]"
                                      value="0">
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @endif
                      </div>
                    </div>
                    @if(!empty($it['hint']))
                      <p class="text-[11px] text-gray-500 mt-2">{{ $it['hint'] }}</p>
                    @endif
                  </div>
                </div>
              </div>
            </details>
          @endforeach
        </form>
      </div>

      <aside class="lg:col-span-1">
        <div class="lg:sticky top-24 space-y-4">
          <div class="bg-white border border-gray-200 rounded-2xl p-4">
            <h3 class="font-semibold text-gray-800">Ringkasan</h3>
            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
              <div class="p-3 rounded-lg bg-maroon/5 border border-maroon/10">
                <p class="text-gray-600">Total Bobot</p>
                <p class="text-2xl font-extrabold text-maroon" id="totalBobot">{{ $totalWeight }}</p>
              </div>
              <div class="p-3 rounded-lg bg-gray-50 border border-gray-200">
                <p class="text-gray-600">Indikator Terisi</p>
                <p class="text-2xl font-extrabold text-gray-900">
                  <span id="doneCount">{{ $doneCount }}</span><span class="text-gray-500 text-base">/20</span>
                </p>
              </div>
            </div>
            @php $pct = $doneCount ? intval(round($doneCount/20*100)) : 0; @endphp
            <div class="mt-3">
              <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                <div id="progressBar" class="h-2 bg-maroon rounded-full" style="width:{{ $pct }}%"></div>
              </div>
              <p id="progressText" class="text-xs text-gray-500 mt-1">{{ $pct }}% selesai</p>
            </div>
            <button type="button" id="btnValidasi"
                    class="mt-3 w-full px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
              Validasi Isian
            </button>
          </div>

          <div class="bg-white border border-gray-200 rounded-2xl p-4">
            <h3 class="font-semibold text-gray-800 mb-2">Loncat ke</h3>
            <div class="grid grid-cols-5 gap-2 text-sm">
              @for($i=1;$i<=20;$i++)
                <a href="#"
                   onclick="document.querySelectorAll('details')[{{ $i-1 }}].open=true; document.querySelectorAll('details')[{{ $i-1 }}].scrollIntoView({behavior:'smooth',block:'start'}); return false;"
                   class="inline-flex items-center justify-center h-9 rounded-md border hover:bg-gray-50">{{ $i }}</a>
              @endfor
            </div>
          </div>
        </div>
      </aside>
    </div>


    {{-- Loading Overlay --}}
  <div id="uploadOverlay"
      class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl px-6 py-5 flex items-center gap-4 shadow-xl">
      <svg class="animate-spin h-6 w-6 text-maroon"
          xmlns="http://www.w3.org/2000/svg"
          fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10"
                stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
      </svg>
      <div>
        <p class="font-semibold text-gray-800">Mengunggah Evidence</p>
        <p class="text-sm text-gray-500">Mohon tunggu, jangan menutup halaman…</p>
      </div>
    </div>
  </div>
<div id="docModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeDocModal()"></div>

  <div class="relative bg-white rounded-xl max-w-md mx-auto mt-24 p-4">
    <h3 class="font-semibold text-gray-800 mb-3">Tambah Dokumen Evidence</h3>

    <input type="hidden" id="doc_no">

    <label class="block mb-2">
      <span class="text-sm">Nomor Surat / Dokumen</span>
      <input id="doc_nomor" class="w-full border rounded px-2 py-1">
    </label>

    <label class="block mb-2">
      <span class="text-sm">Tanggal Surat / Dokumen</span>
      <input type="date" id="doc_tanggal" class="w-full border rounded px-2 py-1">
    </label>

    <label class="block mb-2">
      <span class="text-sm">Tentang</span>
      <input id="doc_tentang" class="w-full border rounded px-2 py-1">
    </label>

    <label class="block mb-3">
      <span class="text-sm">File (PDF)</span>
      <input type="file" id="doc_file" accept=".pdf">
    </label>

    <div class="flex justify-end gap-2">
      <button onclick="closeDocModal()" class="px-3 py-1 border rounded">
        Batal
      </button>
      <button onclick="addDoc()" class="px-3 py-1 bg-maroon text-white rounded">
        Tambah
      </button>
    </div>
  </div>
</div>

  </section>
@endsection

@push('scripts')
<script>
    const MULTIPLIER = {
    1:3, 2:2, 3:2, 4:1, 5:2,
    6:1, 7:1, 8:1, 9:1, 10:1,
    11:1, 12:2, 13:2, 14:3, 15:2,
    16:3, 17:2, 18:1, 19:2, 20:4
  };
  // Hitung total bobot + indikator terisi secara lokal (tanpa API)
 function recompute() {
    let total = 0, done = 0;

    document.querySelectorAll('select.paramSelect').forEach(sel => {
      const opt    = sel.selectedOptions[0];
      const raw    = opt ? parseInt(opt.dataset.weight || '0', 10) : 0; // bobot dasar (1/2/3/...)
      const label  = opt ? (opt.dataset.label || '') : '';
      const no     = parseInt(sel.dataset.no || '0', 10);
      const mult   = MULTIPLIER[no] || 1;
      const score  = raw * mult; // skor akhir = raw × multiplier

      // set hidden fallback (kalau backend butuh)
      const hiddenLabel  = document.querySelector(`input[name="parameter_label[${no}]"]`);
      const hiddenWeight = document.querySelector(`input[name="parameter_weight[${no}]"]`);
      if (hiddenLabel)  hiddenLabel.value  = label || '';
      if (hiddenWeight) hiddenWeight.value = String(raw || 0); // tetap raw yg disubmit (server akan kalikan)

      const wrap = sel.closest('details');
      const infoTag   = wrap.querySelector('.infoTag');
      const weightTag = wrap.querySelector('.weightTag');

      infoTag.textContent   = label || '—';
      infoTag.className = 'infoTag text-xs px-2 py-0.5 rounded ' + (
        !opt || !sel.value ? 'bg-gray-100 text-gray-700' :
        score >= 20 ? 'bg-emerald-50 text-emerald-700' :
        score >= 10 ? 'bg-blue-50 text-blue-700' :
        score >   0 ? 'bg-amber-50 text-amber-700' :
                      'bg-gray-100 text-gray-700'
      );
      weightTag.textContent = 'Skor: ' + (score || 0);

      if (sel.value) done++;
      total += (score || 0);
    });

    document.getElementById('totalBobot').textContent = String(total);
    document.getElementById('doneCount').textContent  = String(done);
    const pct = Math.round((done/20)*100);
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressText').textContent = pct + '% selesai';
  }

  document.querySelectorAll('select.paramSelect').forEach(sel => {
    sel.addEventListener('change', recompute);
  });

  document.getElementById('btnValidasi')?.addEventListener('click', () => {
    const missing = [];
    document.querySelectorAll('select.paramSelect').forEach((sel, idx) => {
      if (!sel.value) missing.push(idx + 1);
    });
    if (missing.length) {
      alert('Mohon pilih parameter untuk indikator: ' + missing.join(', '));
    } else {
      alert('Semua indikator sudah punya parameter. Silakan simpan.');
    }
  });

  // inisialisasi pertama (update badge sesuai preselect)
  recompute();
</script>
<script>
  const form = document.getElementById('evidenceForm');
  const overlay = document.getElementById('uploadOverlay');
  const btnSubmit = document.getElementById('btnSubmitEvidence');

  if (form) {
    form.addEventListener('submit', function () {
      // tampilkan overlay
      overlay.classList.remove('hidden');
      overlay.classList.add('flex');

      // disable tombol supaya tidak double submit
      if (btnSubmit) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-70', 'cursor-not-allowed');
        btnSubmit.textContent = 'Mengunggah...';
      }
    });
  }
</script>
<script>
  function markFileDelete(btn) {
    const row = btn.closest('[data-file-row]');
    const input = row.querySelector('input[type="hidden"]');

    if (!row || !input) return;

    const isDeleted = input.value === '1';

    if (isDeleted) {
      // UNDO
      input.value = '0';
      row.classList.remove('opacity-50', 'bg-rose-50');
      btn.textContent = '🗑️';
    } else {
      // MARK DELETE
      input.value = '1';
      row.classList.add('opacity-50', 'bg-rose-50');
      btn.textContent = '↩️';
    }
  }
</script>

<script>
  let docCounter = 0;

function openDocModal(no){
  document.getElementById('doc_no').value = no;
  document.getElementById('docModal').classList.remove('hidden');
}

function closeDocModal(){
  document.getElementById('docModal').classList.add('hidden');
}

function addDoc(){
  const no = document.getElementById('doc_no').value;

  const nomor   = document.getElementById('doc_nomor').value;
  const tanggal = document.getElementById('doc_tanggal').value;
  const tentang = document.getElementById('doc_tentang').value;
  const file    = document.getElementById('doc_file').files[0];

  if(!nomor || !tanggal || !tentang || !file){
    alert('Semua field wajib diisi');
    return;
  }

  const idx = docCounter++;

  const row = document.createElement('div');
  row.className = 'border rounded p-2 text-sm flex justify-between items-center';

  row.innerHTML = `
    <div>
      <p class="font-medium">${nomor}</p>
      <p class="text-xs text-gray-600">${tentang} • ${tanggal}</p>
    </div>

    <button type="button" onclick="this.closest('div').remove()">🗑️</button>

    <input type="hidden" name="docs[${no}][${idx}][nomor]" value="${nomor}">
    <input type="hidden" name="docs[${no}][${idx}][tanggal]" value="${tanggal}">
    <input type="hidden" name="docs[${no}][${idx}][tentang]" value="${tentang}">
  `;

  const fileInput = document.createElement('input');
  fileInput.type = 'file';
  fileInput.name = `docs[${no}][${idx}][file]`;
  fileInput.files = document.getElementById('doc_file').files;
  fileInput.hidden = true;

  row.appendChild(fileInput);

  document.getElementById(`docList-${no}`).appendChild(row);
  closeDocModal();
}

</script>
@endpush
