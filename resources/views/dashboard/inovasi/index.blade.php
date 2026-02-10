@extends('layouts.app')
<style>
  /* Badge AI kecil dengan “neural orbit” */
  .ai-hdr-badge{
    position:relative;width:18px;height:18px;border-radius:9999px;
    box-shadow:inset 0 0 0 2px rgba(122,34,34,.28); /* maroon */
  }
  .ai-hdr-badge::before,.ai-hdr-badge::after{
    content:"";position:absolute;top:50%;left:50%;
    width:4px;height:4px;border-radius:9999px;background:#7a2222; /* maroon */
    transform-origin:-6px -6px;
  }
  .ai-hdr-badge::before{ animation:aihdr-spin 1.6s linear infinite; }
  .ai-hdr-badge::after{ background:#f59e0b; animation:aihdr-spin 1.6s linear infinite .4s; }
  @keyframes aihdr-spin { to { transform: rotate(360deg) } }
  /* QUILL FIX – FINAL */
.quill-wrapper {
  width: 100%;
  margin-bottom: 2rem; /* jarak ke field berikutnya */
}

.quill-wrapper .ql-container {
  min-height: 220px;
}

.quill-wrapper .ql-editor {
  min-height: 200px;
  line-height: 1.6;
}

</style>

@section('content')
  <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Inovasi</h1>
        <p class="text-sm text-gray-600 mt-1">Direktori inovasi daerah: cari, pantau tahapan, dan kelola unggahan tiap OPD.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('evidence.pedoman') }}"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                border border-maroon text-maroon
                hover:bg-maroon hover:text-white transition">
        📘 Pedoman Evidence
        </a>
        <button id="btnTambah" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 5v14M5 12h14"/></svg>
          Tambah Inovasi
        </button>
      </div>
    </div>

  </section>

  <!-- Filter & Search -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-6 gap-3" method="GET" action="{{ route('sigap-inovasi.index') }}">
        <div>
          <label class="text-sm font-semibold text-gray-700">Tahapan Inovasi</label>
          <select name="f_tahap" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Inisiatif','Uji Coba','Penerapan'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['tahap'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Jenis Urusan</label>
          <select name="f_urusan" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Kesehatan','Pendidikan','Air Bersih','Transportasi'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['urusan'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Inisiator</label>
          <select name="f_inisiator" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['OPD','Unit Kerja','Kolaborasi'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['inisiator'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>

        <div>
          <label class="text-sm font-semibold text-gray-700">Tahap Inovasi</label>
          <select name="f_tahap_inovasi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Inisiatif','Uji Coba','Penerapan'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['tahap_inovasi'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Status Tahap</label>
          <select name="f_tahap_status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach(['Belum','Berjalan','Selesai'] as $opt)
              <option value="{{ $opt }}" @selected(($filters['tahap_status'] ?? '')==$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>

        <div class="lg:col-span-6 grid md:grid-cols-3 gap-3 pt-1">
          <div class="md:col-span-2">
            <label class="text-sm font-semibold text-gray-700">Pencarian</label>
            <div class="relative mt-1.5">
              <input name="q" value="{{ $filters['q'] ?? '' }}" type="search" class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pl-9" placeholder="Judul / OPD / Kata kunci…">
              <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
            </div>
          </div>
          <div class="flex items-end gap-3">
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition w-full md:w-auto">Cari</button>
            <a href="{{ route('sigap-inovasi.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 w-full md:w-auto">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span>
          @if($items->total())
            Menampilkan {{ $items->firstItem() }}–{{ $items->lastItem() }} dari {{ $items->total() }}
          @else
            Tidak ada data
          @endif
        </span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Urutkan</label>
          <form method="GET" action="{{ route('sigap-inovasi.index') }}">
            @foreach(request()->except('sort') as $k => $v)
              <input type="hidden" name="{{ $k }}" value="{{ $v }}">
            @endforeach
            <select name="sort" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon" onchange="this.form.submit()">
              <option value="terbaru" @selected(($filters['sort'] ?? 'terbaru')==='terbaru')>Terbaru</option>
              <option value="judul"   @selected(($filters['sort'] ?? '')==='judul')>Judul (A-Z)</option>
            </select>
          </form>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm table-fixed">
          <colgroup>
          <col class="w-[28%]" />  <!-- Inovasi -->
          <col class="w-[10%]" />  <!-- Jenis Urusan -->
          <col class="w-[12%]" />  <!-- Inisiator -->
          <col class="w-[10%]" />  <!-- Tahap Inovasi -->
          <col class="w-[12%]" />  <!-- OPD/Unit -->
          <col class="w-[10%]" />  <!-- Review -->
          <col class="w-[12%]" />  <!-- Asistensi -->
          <col class="w-[16%]" />  <!-- Aksi -->
        </colgroup>
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3">Inovasi</th>
              <th class="px-4 py-3">Jenis Urusan</th>
              <th class="px-4 py-3">Inisiator</th>
              <th class="px-4 py-3">Tahap Inovasi</th>
              <th class="px-4 py-3">OPD/Unit</th>
              {{-- <th class="px-4 py-3">
                <span class="inline-flex items-center gap-2">
                  Ai Review
                  <span class="ai-hdr-badge" aria-hidden="true" title="AI ready"></span>
                </span>
              </th> --}}
              <th class="px-4 py-3">Asistensi</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse($items as $inv)
              <tr>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded bg-maroon/10 flex items-center justify-center text-maroon">
                      <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" d="M3 21h18M9 21V9h6v12M4 10h16v11H4V10zM12 3l8 6H4l8-6z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="font-medium text-gray-900">{{ $inv->judul }}</p>
                      <p class="text-xs text-gray-600 line-clamp-1">
                        {!! \Illuminate\Support\Str::limit(strip_tags($inv->rancang_bangun ?? '-'), 20) !!}
                      </p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">{{ $inv->urusan_pemerintah ?? '-' }}</td>
                <td class="px-4 py-3">{{ $inv->inisiator_nama ?? '-' }}</td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-1">
                  {{$inv->tahap_inovasi??'-'}}
                  </div>
                </td>
                <td class="px-4 py-3">{{ $inv->opd_unit ?? '-' }}</td>
                {{-- <td class="px-4 py-3"><p class="p-2 bg-yellow-100 text-black">-Coming Soon-</p></td> --}}
            <td class="px-4 py-3">
                @role('admin')
                  <form
                    action="{{ route('sigap-inovasi.asistensi', $inv->id) }}"
                    method="POST"
                    class="flex flex-col gap-2 sm:flex-row sm:items-center"
                    id="asst-form-{{ $inv->id }}"
                    data-modal-title="Asistensi: {{ $inv->judul }}"
                    data-modal-action="{{ route('sigap-inovasi.asistensi', $inv->id) }}"
                  >
                    @csrf
                    <select
                      name="status"
                      id="asst-status-{{ $inv->id }}"
                      class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon"
                    >
                      @foreach(['Pending','Disetujui','Dikembalikan','Revisi','Ditolak'] as $st)
                        <option value="{{ $st }}" @selected(($inv->asistensi_status ?? 'Menunggu Verifikasi') === $st)>{{ $st }}</option>
                      @endforeach
                    </select>

                    {{-- catatan akan diisi dari modal --}}
                    <input type="hidden" name="note" id="asst-note-hidden-{{ $inv->id }}" value="">

                    <button type="submit" class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">
                      Simpan
                    </button>
                  </form>

                  {{-- hint terakhir --}}
                  @if(!empty($inv->asistensi_status) || !empty($inv->asistensi_note))
                    <div class="mt-1 text-[11px] text-gray-600">
                      Terakhir:
                      <span class="font-medium">{{ $inv->asistensi_status ?? '—' }}</span>
                      @if(!empty($inv->asistensi_note))
                        • <span class="line-clamp-1" title="{{ $inv->asistensi_note }}">{{ \Illuminate\Support\Str::limit($inv->asistensi_note, 80) }}</span>
                      @endif
                    </div>
                  @endif
                @else
                  <span class="px-2 py-1 rounded text-xs
                    @class([
                      'bg-gray-100 text-gray-700' => ($inv->asistensi_status ?? 'Menunggu Verifikasi') === 'Menunggu Verifikasi',
                      'bg-emerald-50 text-emerald-700' => ($inv->asistensi_status ?? '') === 'Disetujui',
                      'bg-amber-50 text-amber-700' => in_array(($inv->asistensi_status ?? ''), ['Revisi','Dikembalikan']),
                      'bg-rose-50 text-rose-700' => ($inv->asistensi_status ?? '') === 'Ditolak',
                    ])"
                    @if(!empty($inv->asistensi_note)) title="{{ $inv->asistensi_note }}" @endif
                  >
                    {{ $inv->asistensi_status ?? 'Menunggu Verifikasi' }}
                  </span>
                @endrole
              </td>

                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-2">
                    <a href="{{ route('sigap-inovasi.show', $inv->id) }}" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
                    <a href="{{ route('sigap-inovasi.edit', $inv->id) }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</a>
                    <form action="{{ route('sigap-inovasi.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('Hapus inovasi ini?')" class="inline">
                      @csrf @method('DELETE')
                      <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Hapus</button>
                    </form>
                    <div class="relative group inline-block">
                      <a href="{{ route('evidence.form', $inv->id) }}">   <button
                        class="px-3 py-1.5 rounded-md border hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-maroon/30"
                        aria-describedby="tt-evidence-1"
                        aria-label="Bukti Evidence"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" d="M3 7a2 2 0 0 1 2-2h4l2 3h8a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>
                        </svg>
                    </button></a>

                       <!-- Tooltip -->
                    <div
                        id="tt-evidence-1"
                        role="tooltip"
                        class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2
                            whitespace-nowrap rounded-md bg-gray-900 text-white text-xs px-2.5 py-1
                            opacity-0 translate-y-1 group-hover:opacity-100 group-hover:translate-y-0
                            group-focus-within:opacity-100 group-focus-within:translate-y-0
                            transition duration-150 z-20"
                    >
                        Bukti Evidence
                        <!-- arrow -->
                        <span class="absolute left-1/2 top-full -translate-x-1/2 w-2 h-2 bg-gray-900 rotate-45"></span>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="px-4 py-3 flex items-center justify-between">
        <div></div>
        <div class="inline-flex">
          {{ $items->onEachSide(1)->links() }}
        </div>
      </div>
    </div>
  </section>

  <!-- Modal Tambah Inovasi -->
  <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/40" onclick="closeModal()"></div>
    <div class="relative z-10 mx-auto max-w-3xl px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
          <h2 class="text-white text-lg font-bold">Tambah Inovasi</h2>
          <p class="text-white/80 text-xs mt-0.5">Lengkapi metadata inovasi dan unggah dokumen pendukung.</p>
        </div>

        <form id="formTambah" action="{{ route('sigap-inovasi.store') }}" method="POST" enctype="multipart/form-data" class="p-5 grid sm:grid-cols-2 gap-4">
          @csrf

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul Inovasi</span>
            <input name="judul" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama inovasi">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">OPD/Unit</span>
            <input name="opd_unit" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama OPD/Unit">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tahapan Inovasi</span>
            <select name="tahap_inovasi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih…</option>
              <option>Inisiatif</option>
              <option>Uji Coba</option>
              <option>Penerapan</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Inisiator Inovasi Daerah</span>
            <select name="inisiator_daerah" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">Pilih…</option>
              <option>Kepala Daerah</option>
              <option>Anggota DPRD</option>
              <option>OPD</option>
              <option>ASN</option>
              <option>Masyrakat</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama Inisiator</span>
            <input name="inisiator_nama" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama inisiator">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Koordinat</span>
            <input name="koordinat" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Koordinat">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Klasifikasi Inovasi</span>
            <select name="klasifikasi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Perangkat Daerah</option>
              <option>Inovasi Desa dan Kelurahan</option>
              <option>Inovasi Masyarakat</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Jenis Inovasi</span>
            <select name="jenis_inovasi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option><option>Digital</option><option>Non Digital</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Bentuk Inovasi Daerah</span>
            <select name="bentuk_inovasi_daerah" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Inovasi Daerah lainnya sesuai kewenangan</option>
              <option>Inovasi Pelayanan Publik</option>
              <option>Inovasi Tata Kelolah Pemerintah Daerah</option>
            </select>
          </label>

        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Asta Cipta</span>
          <select name="asta_cipta" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">--</option>
            @foreach(config('inovasi.asta_cipta') as $code => $label)
              <option value="{{ $code }}" @selected(old('asta_cipta')===$code)>{{ $label }}</option>
            @endforeach
          </select>
        </label>

        <label class="block sm:col-span-2">
          <span class="text-sm font-semibold text-gray-700">Program Prioritas Walikota Makassar</span>
          <select name="program_prioritas" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">--</option>
            @foreach(config('inovasi.program_prioritas') as $code => $label)
              <option value="{{ $code }}" @selected(old('program_prioritas')===$code)>{{ $label }}</option>
            @endforeach
          </select>
        </label>
        <label class="block sm:col-span-2">
        <span class="text-sm font-semibold text-gray-700">
          Misi Walikota Makassar <span class="text-rose-600">*</span>
        </span>

        <select
          name="misi_walikota"
          required
          class="mt-1.5 w-full rounded-lg border p-2 border-gray-300
                focus:border-maroon focus:ring-maroon"
        >
          <option value="">— Pilih Misi —</option>
          @foreach(config('inovasi.misi_walikota') as $no => $label)
            <option value="{{ $no }}" @selected(old('misi_walikota')==$no)>
              Misi {{ $no }} — {{ \Illuminate\Support\Str::limit($label, 80) }}
            </option>
          @endforeach
        </select>

        <p class="text-xs text-gray-500 mt-1">
          Pilih misi Walikota yang paling relevan dengan inovasi ini.
        </p>
      </label>


        <label class="block">
          <span class="text-sm font-semibold text-gray-700">Urusan Pemerintah</span>
          <select name="urusan_pemerintah" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">--</option>
            @foreach(config('inovasi.urusan_pemerintah') as $code => $label)
              <option value="{{ $code }}" @selected(old('urusan_pemerintah')===$code)>{{ $label }}</option>
            @endforeach
          </select>
        </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Waktu Uji Coba</span>
            <input name="waktu_uji_coba" type="date" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Waktu Penerapan</span>
            <input name="waktu_penerapan" type="date" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
          </label>

          <!-- Tahap (opsional) -->
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Apakah sudah ada perkembangan inovasi tersebut?</span>
            <select name="perkembangan_inovasi" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option>Tidak</option>
              <option>Ya</option>
            </select>
          </label>

          <!-- Richtext (Quill -> textarea) -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Rancang bangun (Min 300 karakter)
            </label>

            <textarea
              name="rancang_bangun"
              rows="8"
              minlength="300"
              required
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Jelaskan rancang bangun inovasi secara rinci..."
            ></textarea>

            <p class="text-xs text-gray-500 mt-1">
              Minimal 300 karakter.
            </p>
          </div>


          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
              Tujuan inovasi daerah
            </label>

            <textarea
              name="tujuan"
              rows="5"
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Tuliskan tujuan inovasi..."
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
             Manfaat yang diperoleh
            </label>

            <textarea
              name="manfaat"
              rows="5"
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Tuliskan manfaat yang diperoleh..."
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">
             Hasil Inovasi
            </label>

            <textarea
              name="hasil_inovasi"
              rows="5"
              class="w-full rounded-lg border border-gray-300 p-3
                    focus:border-maroon focus:ring-maroon resize-y"
              placeholder="Tuliskan hasil inovasi..."
            ></textarea>
          </div>
          <div class="sm:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              Penelitian / Inovasi Terdahulu (Minimal 3, Maksimal 5)
            </label>

            <div id="video-wrapper" class="space-y-3">

              @for ($i = 0; $i < 3; $i++)
                <div class="video-item border rounded-lg p-3">
                  <input
                    type="text"
                    name="videos[{{ $i }}][judul]"
                    required
                    placeholder="Judul penelitian / inovasi"
                    class="w-full mb-2 rounded-lg border-black-300"
                  >

                  <textarea
                    name="videos[{{ $i }}][deskripsi]"
                    rows="2"
                    placeholder="Deskripsi singkat"
                    class="w-full mb-2 rounded-lg border-gray-300"
                  ></textarea>

                  <input
                    type="url"
                    name="videos[{{ $i }}][url]"
                    required
                    placeholder="Link video (YouTube / website)"
                    class="w-full rounded-lg border-gray-300"
                  >
                </div>
              @endfor

            </div>

            <div class="flex gap-2 mt-3">
              <button type="button" id="addVideo"
                class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                + Tambah Referensi
              </button>

              <button type="button" id="removeVideo"
                class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
                − Hapus
              </button>
            </div>
          </div>

          <!-- Files -->
          <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Anggaran (Jika diperlukan)</span>
              <input name="anggaran" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <p class="text-[12px] text-gray-500 mt-1">Utamakan PDF untuk ringkasan / TOR.</p>
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Profil Bisnis (.ppt) (Jika ada)</span>
              <input name="profil_bisnis" type="file" accept=".ppt,.pptx,.pdf" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Dokumen HAKI</span>
              <input name="haki" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Penghargaan</span>
              <input name="penghargaan" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            </label>
          </div>

          <div class="sm:col-span-2 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Data inovasi dapat diverifikasi dan diperbaharui oleh admin.</span>
            </div>
            <a href="#sop" class="text-maroon hover:underline">Lihat SOP</a>
          </div>

          <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeModal()">Batal</button>
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Modal Asistensi (global, reuse untuk semua baris) --}}
<div id="asst-modal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" data-asst-dismiss></div>
  <div class="relative z-10 mx-auto max-w-lg p-4 sm:p-6 mt-16">
    <div class="bg-white rounded-2xl shadow-2xl">
      <div class="px-5 py-3 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
        <h3 id="asst-modal-title" class="text-white text-base font-bold">Asistensi</h3>
        <p class="text-white/80 text-xs">Mohon isi catatan untuk status yang dipilih.</p>
      </div>
      <form id="asst-modal-form" method="POST" class="p-5 space-y-3">
        @csrf
        <textarea id="asst-modal-note"
          rows="6"
          maxlength="5000"
          class="w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon text-sm"
          data-template="{{ e($evidenceNoteTemplate) }}"
          placeholder="Tulis catatan… (Wajib)">
        </textarea>

        <div class="flex items-center justify-between text-[11px] text-gray-500">
          <div class="flex flex-wrap gap-2">
            @foreach(['Lengkapi dokumen','Perbaiki deskripsi manfaat','Tidak sesuai kriteria','Butuh data pendukung'] as $chip)
              <button type="button" class="px-2 py-1 rounded-md border hover:bg-gray-50"
                onclick="asstAppendChip('{{ $chip }}')">+ {{ $chip }}</button>
            @endforeach
          </div>
          <span id="asst-modal-count">0/5000</span>
        </div>
        <div class="flex items-center justify-end gap-2 pt-2">
          <button type="button" class="px-3 py-1.5 rounded-md border hover:bg-gray-50" data-asst-dismiss>Batal</button>
          <button type="submit" class="px-3 py-1.5 rounded-md bg-maroon text-white text-xs hover:bg-maroon-800">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
  <script>
    // Modal controls
    const modal = document.getElementById('modal');
    document.getElementById('btnTambah')?.addEventListener('click', () => { modal.classList.remove('hidden'); setTimeout(()=>window.dispatchEvent(new Event('resize')),50); });
    function closeModal(){ modal.classList.add('hidden'); }

 
  </script>

<script>
  // status yang butuh catatan
  const NEED_NOTE = new Set(['Dikembalikan','Revisi','Ditolak']);

  const modalEl      = document.getElementById('asst-modal');
  const modalTitleEl = document.getElementById('asst-modal-title');
  const modalFormEl  = document.getElementById('asst-modal-form');
  const noteEl       = document.getElementById('asst-modal-note');
  const countEl      = document.getElementById('asst-modal-count');

  let pendingForm = null; // form baris yang menunggu note
  let pendingHiddenNote = null; // hidden input note di baris itu

  function openAsstModal(title, status){
    modalTitleEl.textContent = title || 'Asistensi';

    const template = noteEl.dataset.template || '';
    const needTemplate = ['Dikembalikan','Revisi','Ditolak'].includes(status);

    noteEl.value = needTemplate ? template : '';
    updateCount();

    modalEl.classList.remove('hidden');
    setTimeout(()=> noteEl.focus(), 30);
  }

  function closeAsstModal(){
    modalEl.classList.add('hidden');
    pendingForm = null;
    pendingHiddenNote = null;
  }
  function updateCount(){ countEl.textContent = `${noteEl.value.length}/5000`; }
  noteEl.addEventListener('input', updateCount);
  document.querySelectorAll('[data-asst-dismiss]').forEach(el=> el.addEventListener('click', closeAsstModal));

  // chips
  window.asstAppendChip = function(text){
    noteEl.value = (noteEl.value ? (noteEl.value.trim() + (noteEl.value.trim().endsWith('.')?'':'.') + ' ') : '') + text;
    noteEl.dispatchEvent(new Event('input'));
    noteEl.focus();
  };

  // submit modal -> salin catatan ke input hidden baris -> submit form baris
  modalFormEl.addEventListener('submit', function(e){
    e.preventDefault();
    const val = noteEl.value.trim();
    if(!val){ alert('Catatan wajib diisi.'); return; }
    if(pendingHiddenNote){ pendingHiddenNote.value = val; }
    if(pendingForm){ pendingForm.submit(); }
    closeAsstModal();
  });

  // intercept semua form asistensi di tabel
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[id^="asst-form-"]').forEach(form => {
      form.addEventListener('submit', function(e){
      const statusSel = form.querySelector('select[name="status"]');
      const statusVal = statusSel?.value || '';

      if (NEED_NOTE.has(statusVal)) {
        e.preventDefault();
        pendingForm = form;
        pendingHiddenNote = form.querySelector('input[name="note"]');

        openAsstModal(form.dataset.modalTitle, statusVal);
      }

        // jika tidak perlu note -> submit langsung (biarkan default)
      });
    });
  });
</script>
<script>
let videoIndex = 3;
const minVideo = 3;
const maxVideo = 5;

const wrapper = document.getElementById('video-wrapper');

document.getElementById('addVideo').addEventListener('click', () => {
  if (videoIndex >= maxVideo) {
    alert('Maksimal 5 referensi video.');
    return;
  }

  const div = document.createElement('div');
  div.className = 'video-item border rounded-lg p-3';
  div.innerHTML = `
    <input type="text" name="videos[${videoIndex}][judul]"
      required placeholder="Judul penelitian / inovasi"
      class="w-full mb-2 rounded-lg border-gray-300">

    <textarea name="videos[${videoIndex}][deskripsi]"
      rows="2" placeholder="Deskripsi singkat"
      class="w-full mb-2 rounded-lg border-gray-300"></textarea>

    <input type="url" name="videos[${videoIndex}][url]"
      required placeholder="Link video"
      class="w-full rounded-lg border-gray-300">
  `;

  wrapper.appendChild(div);
  videoIndex++;
});

document.getElementById('removeVideo').addEventListener('click', () => {
  if (videoIndex <= minVideo) {
    alert('Minimal 3 referensi wajib diisi.');
    return;
  }

  wrapper.lastElementChild.remove();
  videoIndex--;
});
</script>

@endpush
