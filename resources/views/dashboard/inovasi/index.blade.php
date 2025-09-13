@extends('layouts.app')

@section('content')
  <!-- Page header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Inovasi</h1>
        <p class="text-sm text-gray-600 mt-1">Direktori inovasi daerah: cari, pantau tahapan, dan kelola unggahan tiap OPD.</p>
      </div>
      <div class="flex flex-wrap gap-2">
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
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3">Inovasi</th>
              <th class="px-4 py-3">Jenis Urusan</th>
              <th class="px-4 py-3">Inisiator</th>
              <th class="px-4 py-3">Tahap Inovasi</th>
              <th class="px-4 py-3">OPD/Unit</th>
              <th class="px-4 py-3">Review</th>
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
                        {!! \Illuminate\Support\Str::limit(strip_tags($inv->rancang_bangun ?? '-'), 120) !!}
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
                <td class="px-4 py-3"><p class="p-2 bg-yellow-100 text-black">Belum Direview</p></td>
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
              <option value="asta1">Memperkokoh ideologi Pancasila, demokrasi, dan hak asasi manusia (HAM).</option>
              <option value="asta2">Memantapkan sistem pertahanan keamanan negara dan mendorong kemandirian bangsa melalui swasembada pangan, energi, air, ekonomi kreatif, ekonomi hijau, dan ekonomi biru.</option>
              <option value="asta3">Meningkatkan lapangan kerja yang berkualitas, mendorong kewirausahaan, mengembangkan industri kreatif, dan melanjutkan pengembangan infrastruktur.</option>
              <option value="asta4">Memperkuat pembangunan sumber daya manusia (SDM), sains, teknologi, pendidikan, kesehatan, prestasi olahraga, kesetaraan gender, serta penguatan peran perempuan, pemuda, dan penyandang disabilitas.</option>
              <option value="asta5">Melanjutkan hilirisasi dan industrialisasi untuk meningkatkan nilai tambah di dalam negeri.</option>
              <option value="asta6">Membangun dari desa dan dari bawah untuk pemerataan ekonomi dan pemberantasan kemiskinan.</option>
              <option value="asta7">Memperkuat reformasi politik, hukum, dan birokrasi, serta memperkuat pencegahan dan pemberantasan korupsi dan narkoba.</option>
              <option value="asta8">Memperkuat penyelarasan kehidupan yang harmonis dengan lingkungan, alam, dan budaya, serta peningkatan toleransi antarumat beragama untuk mencapai masyarakat yang adil dan Makmur</option>
            </select>
          </label>
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Program Prioritas Walikota Makassar</span>
            <select name="program_prioritas" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option value="progwalkot_1">Makassar SuperApps: Platform digital terpadu yang menyatukan seluruh layanan publik dalam satu genggaman.</option>
              <option value="progwalkot_2">Makassar Creative Hub (MCH): Fasilitas pengembangan keterampilan dan inovasi bagi warga, khususnya generasi muda.</option>
              <option value="progwalkot_3">Penyediaan Air Bersih: Fasilitas air bersih berkualitas tinggi yang dijadwalkan diluncurkan bulan depan.</option>
              <option value="progwalkot_4">Seragam Gratis: Bantuan seragam sekolah bagi siswa SD dan SMP dari keluarga kurang mampu.</option>
              <option value="progwalkot_5">Iuran Sampah Gratis: Program pembebasan iuran kebersihan untuk sebagian masyarakat sebagai bentuk keadilan sosial.</option>
              <option value="progwalkot_6">Makassar Social Assistant: Program bantuan sosial terintegrasi dalam platform SuperApps.</option>
              <option value="progwalkot_7">Pembangunan Stadion Internasional: Infrastruktur olahraga modern guna mendukung budaya sepak bola lokal.</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Urusan Pemerintah</span>
            <select name="urusan_pemerintah" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
              <option value="">--</option>
              <option value="urusan_pemerintah_1">Pendidikan</option>
              <option value="urusan_pemerintah_2">Kesehatan</option>
              <option value="urusan_pemerintah_3">Infrastruktur</option>
              <option value="urusan_pemerintah_4">Lingkungan Hidup</option>
              <option value="urusan_pemerintah_5">Sosial</option>
              <option value="urusan_pemerintah_6">Ekonomi</option>
              <option value="urusan_pemerintah_7">Budaya</option>
              <option value="urusan_pemerintah_8">Pendidikan</option>
              <option value="urusan_pemerintah_9">Pendidikan</option>
              <option value="urusan_pemerintah_10">Pendidikan</option>
              <option value="urusan_pemerintah_11">Pendidikan</option>
              <option value="urusan_pemerintah_12">Pendidikan</option>
              <option value="urusan_pemerintah_13">Pendidikan</option>
              <option value="urusan_pemerintah_14">Pendidikan</option>
              <option value="urusan_pemerintah_15">Pendidikan</option>
              <option value="urusan_pemerintah_16">Pendidikan</option>
              <option value="urusan_pemerintah_17">Pendidikan</option>
              <option value="urusan_pemerintah_18">Pendidikan</option>
              <option value="urusan_pemerintah_19">Pendidikan</option>
              <option value="urusan_pemerintah_20">Pendidikan</option>
              <option value="urusan_pemerintah_21">Pendidikan</option>
              <option value="urusan_pemerintah_22">Pendidikan</option>
              <option value="urusan_pemerintah_23">Pendidikan</option>
              <option value="urusan_pemerintah_24">Pendidikan</option>
              <option value="urusan_pemerintah_25">Pendidikan</option>
              <option value="urusan_pemerintah_26">Pendidikan</option>
              <option value="urusan_pemerintah_27">Pendidikan</option>
              <option value="urusan_pemerintah_28">Pendidikan</option>
              <option value="urusan_pemerintah_29">Pendidikan</option>
              <option value="urusan_pemerintah_30">Pendidikan</option>
              <option value="urusan_pemerintah_31">Pendidikan</option>
              <option value="urusan_pemerintah_32">Pendidikan</option>
              <option value="urusan_pemerintah_33">Pendidikan</option>
              <option value="urusan_pemerintah_34">Pendidikan</option>
              <option value="urusan_pemerintah_35">Pendidikan</option>
              <option value="urusan_pemerintah_36">Pendidikan</option>
              <option value="urusan_pemerintah_37">Pendidikan</option>
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
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Rancang bangun (Min 300 karakter)</span>
            <textarea name="rancang_bangun" class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300" data-minlength="300" data-short="true"></textarea>
          </label>
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Tujuan inovasi daerah</span>
            <textarea name="tujuan" class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"></textarea>
          </label>
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Manfaat yang diperoleh</span>
            <textarea name="manfaat" class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"></textarea>
          </label>
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Hasil Inovasi</span>
            <textarea name="hasil_inovasi" class="richtext mt-1.5 w-full rounded-lg border p-2 border-gray-300"></textarea>
          </label>

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
@endsection

@push('scripts')
  {{-- Quill CDN (kalau belum dimuat global) --}}
  <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
  <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

  <script>
    // Modal controls
    const modal = document.getElementById('modal');
    document.getElementById('btnTambah')?.addEventListener('click', () => { modal.classList.remove('hidden'); setTimeout(()=>window.dispatchEvent(new Event('resize')),50); });
    function closeModal(){ modal.classList.add('hidden'); }

    // Quill init
    const QUILL_EDITORS=[];
    function initQuillEditors(){
      document.querySelectorAll('textarea.richtext').forEach((ta, idx) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'mt-1.5';
        const editor = document.createElement('div');
        wrapper.appendChild(editor);
        ta.insertAdjacentElement('afterend', wrapper);
        ta.style.display='none';

        const quill = new Quill(editor, {
          theme: 'snow',
          modules: { toolbar: [['bold','italic','underline'], [{'list':'ordered'},{'list':'bullet'}], ['link','clean']] }
        });
        if (ta.value && ta.value.trim()!=='') quill.root.innerHTML = ta.value;

        QUILL_EDITORS.push({
          ta, quill,
          min: parseInt(ta.dataset.minlength || '0',10) || 0,
          label: ta.previousElementSibling?.querySelector('span')?.innerText || ta.name || `Field ${idx+1}`
        });
      });
    }
    function syncQuillToTextareas(){
      for(const {ta,quill,min,label} of QUILL_EDITORS){
        const html = quill.root.innerHTML.trim();
        const plainLen = quill.getText().trim().length;
        if(min && plainLen < min){ alert(`${label}: minimal ${min} karakter. Saat ini ${plainLen}.`); throw new Error('VALIDATION_FAIL'); }
        ta.value = html;
      }
    }
    initQuillEditors();

    // Sync sebelum submit form create
    document.getElementById('formTambah')?.addEventListener('submit', (e)=>{
      try { syncQuillToTextareas(); } catch(err){ e.preventDefault(); }
    });
  </script>
@endpush
