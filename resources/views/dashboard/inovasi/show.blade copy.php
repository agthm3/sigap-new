@extends('layouts.app')

@section('content')

<style>
/* Print + Watermark */
@media print{
  .no-print{ display:none !important; }
  header, footer{ display:none !important; }
  .card{ box-shadow:none !important; border-color:#e5e7eb !important; }
  body{ background:#fff; }

  body::before{
    content: "Dicetak via SIGAP INOVASI - BRIDA MKS";
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-30deg);
    font-weight: 800;
    font-size: 30px;
    letter-spacing: .06em;
    color: #7a2222;
    opacity: .08;
    z-index: 9999;
    pointer-events: none;
    text-transform: uppercase;
    white-space: nowrap;
  }

  .card, section, .rounded-2xl, .border, .bg-white { background: transparent !important; }
}
</style>

  <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
          <span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-maroon text-white">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
            </svg>
          </span>
          <p class="text-xs text-gray-600">Detail Inovasi</p>
        </div>
        <h1 class="mt-1 text-2xl font-extrabold text-gray-900">
          {{ $inovasi->judul ?? '—' }}
        </h1>

        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
          <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
            Klasifikasi: {{ $inovasi->klasifikasi ?? '—' }}
          </span>
          <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
            Jenis: {{ $inovasi->jenis ?? '—' }}
          </span>
          <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
            Urusan: {{ $inovasi->urusan ?? '—' }}
          </span>
          <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
            Inisiator: {{ $inovasi->inisiator ?? '—' }}
          </span>
          <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700">
            OPD: {{ $inovasi->opd ?? '—' }}
          </span>
        </div>
      </div>

      <div class="no-print flex flex-wrap gap-2">
        <a href="{{ route('inovasi.edit', $inovasi->id) }}"
           class="px-3 py-2 rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white text-sm">
           Edit Metadata
        </a>
        <a href="{{ route('inovasi.evidence.index', $inovasi->id) }}"
           class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">
           Isi Evidence
        </a>
        <button onclick="window.print()" class="px-3 py-2 rounded-lg border hover:bg-gray-50 text-sm">Cetak</button>
      </div>
    </div>
  </section>

  <!-- Summary cards -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="card rounded-2xl border bg-white p-4">
        <p class="text-xs text-gray-500">Skor Evidence (Dummy)</p>
        <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $totalBobot }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $terisiCount }}/20 indikator terisi</p>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <p class="text-xs text-gray-500">Terakhir Diperbarui</p>
        <p class="mt-1 text-2xl font-extrabold text-maroon">
          {{ $inovasi->updated_at? $inovasi->updated_at->timezone('Asia/Makassar')->format('d M Y • H:i') : '—' }}
        </p>
        <p class="text-xs text-gray-500 mt-1">Metadata/Evidence</p>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <p class="text-xs text-gray-500">Berkas Evidence</p>
        <p class="mt-1 text-2xl font-extrabold text-maroon">{{ $fileCount }}</p>
        <p class="text-xs text-gray-500 mt-1">File terunggah</p>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <p class="text-xs text-gray-500">Koordinat</p>
        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $inovasi->koordinat ?? '—' }}</p>
        <p class="text-xs text-gray-500 mt-1">Lokasi (jika diisi)</p>
      </div>
    </div>
  </section>

  <!-- Tahapan -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="card rounded-2xl border bg-white p-4">
      <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-800">Tahapan Inovasi</h3>
        <div class="text-xs text-gray-500">Status ringkas</div>
      </div>

      @php
        // Contoh mapping sederhana dari total bobot → status
        $tInis = $totalBobot > 0 ? 'Selesai' : 'Belum';
        $tUji  = $totalBobot >= 50 ? 'Berjalan' : 'Belum';
        $tTerap= $totalBobot >= 100 ? 'Berjalan' : 'Belum';

        $badge = function($text){
          $map = [
            'Selesai'  => 'bg-emerald-50 text-emerald-700',
            'Berjalan' => 'bg-amber-50 text-amber-700',
            'Belum'    => 'bg-gray-100 text-gray-700',
          ];
          return $map[$text] ?? $map['Belum'];
        };
      @endphp

      <div class="grid md:grid-cols-3 gap-3 text-sm">
        <div class="rounded-xl border p-3">
          <p class="text-gray-600">Inisiatif</p>
          <p class="mt-1 font-semibold">
            <span class="px-2 py-0.5 rounded {{ $badge($tInis) }}">{{ $tInis }}</span>
          </p>
        </div>
        <div class="rounded-xl border p-3">
          <p class="text-gray-600">Uji Coba</p>
          <p class="mt-1 font-semibold">
            <span class="px-2 py-0.5 rounded {{ $badge($tUji) }}">{{ $tUji }}</span>
          </p>
        </div>
        <div class="rounded-xl border p-3">
          <p class="text-gray-600">Penerapan</p>
          <p class="mt-1 font-semibold">
            <span class="px-2 py-0.5 rounded {{ $badge($tTerap) }}">{{ $tTerap }}</span>
          </p>
        </div>
      </div>

      <div class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
          <div class="h-2 bg-maroon rounded-full" style="width: {{ $progressPct }}%"></div>
        </div>
        <p class="text-xs text-gray-500 mt-1">{{ $progressPct }}% selesai</p>
      </div>
    </div>
  </section>

  <!-- Deskripsi detail -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="grid lg:grid-cols-2 gap-4">
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Rancang Bangun</h3>
        <div class="prose prose-sm max-w-none mt-2 text-gray-800">
          {!! $inovasi->rancang_bangun_html ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
        </div>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Tujuan Inovasi</h3>
        <div class="prose prose-sm max-w-none mt-2 text-gray-800">
          {!! $inovasi->tujuan_html ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
        </div>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Manfaat yang Diperoleh</h3>
        <div class="prose prose-sm max-w-none mt-2 text-gray-800">
          {!! $inovasi->manfaat_html ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
        </div>
      </div>
      <div class="card rounded-2xl border bg-white p-4">
        <h3 class="font-semibold text-gray-800">Hasil Inovasi</h3>
        <div class="prose prose-sm max-w-none mt-2 text-gray-800">
          {!! $inovasi->hasil_inovasi_html ?? '<p class="text-gray-500">Belum ada isi.</p>' !!}
        </div>
      </div>
    </div>
  </section>

  <!-- Ringkasan Evidence -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="card rounded-2xl border bg-white overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span>Ringkasan Evidence (20 indikator)</span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Tampilkan</label>
          <select id="filterEv" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="all">Semua</option>
            <option value="incomplete">Yang belum lengkap</option>
            <option value="complete">Yang sudah lengkap</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3 w-12">No</th>
              <th class="px-4 py-3">Indikator</th>
              <th class="px-4 py-3">Parameter</th>
              <th class="px-4 py-3">Bobot</th>
              <th class="px-4 py-3">File</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbodyEv" class="divide-y">
            @php
              $badgeTone = fn($ok) => $ok ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700';
            @endphp
            @forelse($evidences as $ev)
              @php
                $complete = ($ev->parameter_label && (int)$ev->bobot > 0 && $ev->file_path);
                $fileName = $ev->original_name ?: basename((string)$ev->file_path);
              @endphp
              <tr data-complete="{{ $complete ? '1' : '0' }}">
                <td class="px-4 py-3 text-gray-600">{{ $ev->nomor }}</td>
                <td class="px-4 py-3">{{ $ev->indikator ?? '—' }}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 rounded {{ $badgeTone($complete) }}">
                    {{ $ev->parameter_label ?: 'Belum dipilih' }}
                  </span>
                </td>
                <td class="px-4 py-3 font-semibold {{ (int)$ev->bobot>0 ? 'text-maroon' : '' }}">
                  {{ (int) $ev->bobot }}
                </td>
                <td class="px-4 py-3">
                  @if($ev->file_path)
                    <a href="{{ Storage::disk('public')->url($ev->file_path) }}" target="_blank"
                       class="text-maroon hover:underline">
                       {{ $fileName }}
                    </a>
                  @else
                    —
                  @endif
                </td>
                <td class="px-4 py-3">
                  <a href="{{ route('inovasi.evidence.index', $inovasi->id) }}#ind-{{ $ev->nomor }}"
                     class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-xs">
                     Lengkapi
                  </a>
                </td>
              </tr>
            @empty
              {{-- jika belum ada evidence tersimpan, render placeholder 1..20 --}}
              @for($i=1;$i<=20;$i++)
                <tr data-complete="0">
                  <td class="px-4 py-3 text-gray-600">{{ $i }}</td>
                  <td class="px-4 py-3">—</td>
                  <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700">Belum dipilih</span></td>
                  <td class="px-4 py-3">0</td>
                  <td class="px-4 py-3">—</td>
                  <td class="px-4 py-3">
                    <a href="{{ route('inovasi.evidence.index', $inovasi->id) }}#ind-{{ $i }}"
                       class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-xs">
                       Lengkapi
                    </a>
                  </td>
                </tr>
              @endfor
            @endforelse
          </tbody>
          <tfoot class="border-t bg-gray-50">
            <tr>
              <td class="px-4 py-3 font-semibold" colspan="3">Total Bobot</td>
              <td class="px-4 py-3 font-extrabold text-maroon">{{ $totalBobot }}</td>
              <td class="px-4 py-3" colspan="2">
                <a href="{{ route('inovasi.evidence.index', $inovasi->id) }}" class="text-maroon hover:underline">
                  Lengkapi / ubah evidence
                </a>
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </section>

  <!-- Lampiran terkumpul -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="card rounded-2xl border bg-white p-4">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-gray-800">Lampiran Terkumpul</h3>
        {{-- TODO: route zip-all kalau sudah siap --}}
        <a href="#" class="text-sm text-maroon hover:underline">Unduh semua (zip)</a>
      </div>

      <div class="mt-3 grid md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
        @php
          $fileIcon = function($path){
            $ext = strtolower(pathinfo((string)$path, PATHINFO_EXTENSION));
            return match(true){
              in_array($ext, ['jpg','jpeg','png','gif','webp','svg']) => '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-blue-100 text-blue-700">IMG</span>',
              in_array($ext, ['mp4','mov']) => '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-purple-100 text-purple-700">VID</span>',
              $ext === 'pdf' => '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-rose-100 text-rose-700">PDF</span>',
              default => '<span class="inline-flex w-8 h-8 items-center justify-center rounded-md bg-gray-100 text-gray-700">FILE</span>',
            };
          };
        @endphp

        @forelse($evidences->whereNotNull('file_path') as $f)
          @php
            $url  = Storage::disk('public')->url($f->file_path);
            $name = $f->original_name ?: basename($f->file_path);
          @endphp
          <div class="rounded-xl border p-3 flex items-center gap-3">
            {!! $fileIcon($f->file_path) !!}
            <div class="min-w-0">
              <p class="font-medium text-gray-800 truncate">{{ $name }}</p>
              <p class="text-xs text-gray-500">Indikator #{{ $f->nomor }} • {{ Str::limit($f->indikator ?? '—', 60) }}</p>
            </div>
            <div class="ml-auto flex items-center gap-2">
              <a href="{{ $url }}" target="_blank" class="px-2.5 py-1.5 rounded-md border text-xs hover:bg-gray-50">View</a>
              <a href="{{ $url }}" download class="px-2.5 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Download</a>
            </div>
          </div>
        @empty
          <p class="text-sm text-gray-500">Belum ada lampiran.</p>
        @endforelse
      </div>
    </div>
  </section>

@endsection

@push('scripts')
<script>
  // Filter ringkasan evidence (client-side)
  document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('tbodyEv');
    const filter = document.getElementById('filterEv');
    if(!tbody || !filter) return;

    function applyFilter(){
      const mode = filter.value; // all | complete | incomplete
      tbody.querySelectorAll('tr').forEach(tr => {
        const done = tr.getAttribute('data-complete') === '1';
        let show = true;
        if (mode === 'complete') show = done;
        if (mode === 'incomplete') show = !done;
        tr.style.display = show ? '' : 'none';
      });
    }

    filter.addEventListener('change', applyFilter);
    applyFilter();
  });
</script>
@endpush
