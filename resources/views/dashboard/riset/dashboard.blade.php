@extends('layouts.app')

@section('content')
@php
  $tahunSekarang = now()->year;
@endphp

<!-- Heading & Quick Actions -->
<section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Dashboard <span class="text-maroon">SIGAP Riset</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">Ringkasan portofolio riset BRIDA, progres metadata, dan aktivitas terbaru.</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="{{ route('riset.create') }}"
    class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm
            {{ request()->routeIs('riset.create') ? 'bg-maroon text-white' : 'hover:bg-gray-100' }}">
    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="2" d="M12 4v16M4 12h16"/>
    </svg>
    Unggah Riset
    </a>
    <a href="#" class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">Kelola Kategori/Tag</a>
    <a href="#" class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">Export CSV</a>
    <a href="#" class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">Kunjungi OJS</a>
  </div>
</section>

<!-- KPI Cards -->
<section class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <div class="p-4 rounded-xl bg-white border border-gray-200">
    <p class="text-xs text-gray-500">Total Riset</p>
    <div class="mt-1 flex items-end gap-2">
      <h3 class="text-2xl font-extrabold text-gray-900">248</h3>
      <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">+12% YoY</span>
    </div>
  </div>
  <div class="p-4 rounded-xl bg-white border border-gray-200">
    <p class="text-xs text-gray-500">Riset Tahun {{ $tahunSekarang }}</p>
    <h3 class="mt-1 text-2xl font-extrabold text-gray-900">37</h3>
    <div class="mt-2 h-2 bg-gray-100 rounded">
      <div class="h-2 bg-maroon rounded" style="width:58%"></div>
    </div>
  </div>
  <div class="p-4 rounded-xl bg-white border border-gray-200">
    <p class="text-xs text-gray-500">Akses Publik vs Restricted</p>
    <div class="mt-1 flex items-baseline gap-3">
      <h3 class="text-2xl font-extrabold text-gray-900">68%</h3>
      <span class="text-xs text-gray-500">publik</span>
    </div>
    <div class="mt-2 flex gap-1">
      <span class="px-2 py-0.5 text-[11px] rounded bg-green-50 text-green-700 border border-green-200">Publik: 169</span>
      <span class="px-2 py-0.5 text-[11px] rounded bg-yellow-50 text-yellow-700 border border-yellow-200">Restricted: 79</span>
    </div>
  </div>
  <div class="p-4 rounded-xl bg-white border border-gray-200">
    <p class="text-xs text-gray-500">Unduhan Bulan Ini</p>
    <h3 class="mt-1 text-2xl font-extrabold text-gray-900">1.482</h3>
    <p class="text-xs text-green-700 mt-1">↑ 7% vs bulan lalu</p>
  </div>
</section>

<!-- Filter Bar -->
<section class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5">
  <form class="grid lg:grid-cols-12 gap-3">
    <div class="lg:col-span-3">
      <label class="text-xs font-semibold text-gray-700">Kata Kunci</label>
      <input type="search" placeholder="Judul / abstrak / penulis…" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
    </div>
    <div class="lg:col-span-2">
      <label class="text-xs font-semibold text-gray-700">Tahun</label>
      <select class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
        <option value="">Semua</option>
        @for($y=$tahunSekarang; $y>=$tahunSekarang-7; $y--)
          <option value="{{ $y }}">{{ $y }}</option>
        @endfor
      </select>
    </div>
    <div class="lg:col-span-2">
      <label class="text-xs font-semibold text-gray-700">Jenis</label>
      <select class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
        <option value="">Semua</option>
        <option value="internal">Riset Internal</option>
        <option value="kolaborasi">Kolaborasi/OPD</option>
        <option value="eksternal">Eksternal Terkait</option>
      </select>
    </div>
    <div class="lg:col-span-2">
      <label class="text-xs font-semibold text-gray-700">Pihak Terkait</label>
      <input type="text" placeholder="PDAM, DLH, DPMPTSP…" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
    </div>
    <div class="lg:col-span-2">
      <label class="text-xs font-semibold text-gray-700">Sort</label>
      <select class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:ring-maroon focus:border-maroon">
        <option value="latest">Terbaru</option>
        <option value="downloads">Unduhan Terbanyak</option>
        <option value="az">Judul A–Z</option>
        <option value="za">Judul Z–A</option>
      </select>
    </div>
    <div class="lg:col-span-1 flex items-end gap-2">
      <button type="button" class="w-full px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Terapkan</button>
    </div>
  </form>
</section>

<!-- Content Grid -->
<section class="grid lg:grid-cols-3 gap-6">
  <!-- Left: Charts + Table -->
  <div class="lg:col-span-2 space-y-6">
    <!-- Charts (placeholders tanpa Chart.js) -->
    <div class="grid md:grid-cols-2 gap-6">
      <div class="p-4 rounded-xl bg-white border border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-800">Tren Publikasi (12 Bulan)</h3>
          <span class="text-[11px] text-gray-500">Dummy</span>
        </div>
        <div class="mt-4 h-36 grid grid-cols-12 items-end gap-1">
          @foreach([20,35,28,40,52,60,38,44,70,64,58,76] as $h)
            <div class="bg-maroon/70 rounded" style="height:{{ $h }}%"></div>
          @endforeach
        </div>
        <div class="mt-2 text-[11px] text-gray-500">Jumlah artikel per bulan (dummy).</div>
      </div>
      <div class="p-4 rounded-xl bg-white border border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-sm font-semibold text-gray-800">Top 5 Tag</h3>
          <span class="text-[11px] text-gray-500">Dummy</span>
        </div>
        <div class="mt-3 space-y-3">
          @foreach([
            ['Sains Data',82],
            ['Lingkungan',74],
            ['Tata Kelola',66],
            ['Pelayanan Publik',54],
            ['Ekonomi Pembangunan',41]
          ] as [$nama,$val])
            <div>
              <div class="flex justify-between text-xs">
                <span class="font-medium text-gray-700">{{ $nama }}</span>
                <span class="text-gray-500">{{ $val }}</span>
              </div>
              <div class="mt-1 h-2 rounded bg-gray-100">
                <div class="h-2 rounded bg-maroon" style="width: {{ $val }}%"></div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Tabel Riset Terbaru -->
    <div class="rounded-xl bg-white border border-gray-200 overflow-hidden">
      <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Riset Terbaru</h3>
        <div class="text-xs text-gray-500">8 item (dummy)</div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left font-semibold">Judul</th>
              <th class="px-4 py-3 text-left font-semibold">Penulis</th>
              <th class="px-4 py-3 text-left font-semibold">Tahun</th>
              <th class="px-4 py-3 text-left font-semibold">Tag</th>
              <th class="px-4 py-3 text-left font-semibold">Akses</th>
              <th class="px-4 py-3 text-left font-semibold">Unduhan</th>
              <th class="px-4 py-3 text-right font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @php
              $rows = [
                ['t'=>'Model Prediksi Kebocoran Air PDAM Makassar','a'=>'R. Akbar, N. Sari','y'=>2024,'tag'=>'Sains Data','acc'=>'Publik','dl'=>212],
                ['t'=>'Peta Jalan Perizinan Berbasis AI','a'=>'M. Fadli','y'=>2025,'tag'=>'Tata Kelola','acc'=>'Restricted','dl'=>88],
                ['t'=>'Dampak Ekonomi Wisata Kota Lama','a'=>'A. Rahman, L. Nur','y'=>2023,'tag'=>'Ekonomi','acc'=>'Publik','dl'=>304],
                ['t'=>'Monitoring Kualitas Udara (IoT)','a'=>'Tim BRIDA','y'=>2024,'tag'=>'Lingkungan','acc'=>'Publik','dl'=>156],
                ['t'=>'Evaluasi Layanan UPT XYZ','a'=>'BRIDA Policy Lab','y'=>2025,'tag'=>'Pelayanan Publik','acc'=>'Restricted','dl'=>41],
                ['t'=>'Analisis Data Pengaduan Publik','a'=>'N. Sari','y'=>2025,'tag'=>'Sains Data','acc'=>'Publik','dl'=>97],
                ['t'=>'Kesiapan Open Data Daerah','a'=>'Tim Data BRIDA','y'=>2024,'tag'=>'Open Data','acc'=>'Publik','dl'=>120],
                ['t'=>'Blueprint Command Center','a'=>'Tim Strategi','y'=>2023,'tag'=>'Tata Kelola','acc'=>'Restricted','dl'=>19],
              ];
            @endphp
            @foreach($rows as $r)
              <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-900">{{ $r['t'] }}</td>
                <td class="px-4 py-3 text-gray-700">{{ $r['a'] }}</td>
                <td class="px-4 py-3">{{ $r['y'] }}</td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 text-xs rounded bg-maroon/10 text-maroon border border-maroon/20">#{{ $r['tag'] }}</span>
                </td>
                <td class="px-4 py-3">
                  @if($r['acc']==='Publik')
                    <span class="px-2 py-0.5 text-xs rounded bg-green-50 text-green-700 border border-green-200">Publik</span>
                  @else
                    <span class="px-2 py-0.5 text-xs rounded bg-yellow-50 text-yellow-700 border border-yellow-200">Restricted</span>
                  @endif
                </td>
                <td class="px-4 py-3">{{ number_format($r['dl']) }}</td>
                <td class="px-4 py-3 text-right">
                  <div class="inline-flex gap-1">
                    <button class="px-2 py-1.5 text-xs rounded border border-gray-300 hover:bg-gray-50">Detail</button>
                    <button class="px-2 py-1.5 text-xs rounded border border-gray-300 hover:bg-gray-50">Edit</button>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3 border-t bg-white flex items-center justify-between text-xs text-gray-600">
        <div>Menampilkan 1–8 dari 248</div>
        <div class="inline-flex -space-x-px rounded-lg overflow-hidden border border-gray-200">
          <button class="px-3 py-1.5 bg-white hover:bg-gray-50 border-r">Sebelumnya</button>
          <span class="px-3 py-1.5 bg-gray-50">1</span>
          <button class="px-3 py-1.5 bg-white hover:bg-gray-50 border-l">Berikutnya</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Right: Side widgets -->
  <div class="space-y-6">
    <!-- Butuh Tindak Lanjut -->
    <div class="rounded-xl bg-white border border-gray-200">
      <div class="px-4 py-3 border-b bg-gray-50">
        <h3 class="text-sm font-semibold text-gray-800">Butuh Tindak Lanjut</h3>
      </div>
      <ul class="divide-y text-sm">
        <li class="px-4 py-3">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="font-medium text-gray-900">Perizinan Berbasis AI</p>
              <p class="text-xs text-gray-600 mt-0.5">Metadata belum lengkap: DOI kosong.</p>
            </div>
            <span class="px-2 py-0.5 text-[11px] rounded bg-amber-100 text-amber-700">Perlu Revisi</span>
          </div>
        </li>
        <li class="px-4 py-3">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="font-medium text-gray-900">Evaluasi Layanan UPT XYZ</p>
              <p class="text-xs text-gray-600 mt-0.5">Lampiran dataset tidak tersedia.</p>
            </div>
            <span class="px-2 py-0.5 text-[11px] rounded bg-amber-100 text-amber-700">Kurang Data</span>
          </div>
        </li>
        <li class="px-4 py-3">
          <div class="flex items-start justify-between gap-3">
            <div>
              <p class="font-medium text-gray-900">Blueprint Command Center</p>
              <p class="text-xs text-gray-600 mt-0.5">Status Restricted tanpa alasan akses.</p>
            </div>
            <span class="px-2 py-0.5 text-[11px] rounded bg-rose-100 text-rose-700">Validasi Akses</span>
          </div>
        </li>
      </ul>
      <div class="px-4 py-3 border-t bg-white">
        <button class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-50">Lihat Semua</button>
      </div>
    </div>

    <!-- Top Tag -->
    <div class="rounded-xl bg-white border border-gray-200 p-4">
      <h3 class="text-sm font-semibold text-gray-800">Tag Teratas</h3>
      <div class="mt-3 flex flex-wrap gap-2 text-xs">
        @foreach(['Sains Data','Lingkungan','Tata Kelola','Pelayanan Publik','Ekonomi'] as $t)
          <span class="px-2 py-1 rounded bg-maroon/10 text-maroon border border-maroon/20">#{{ $t }}</span>
        @endforeach
      </div>
    </div>

    <!-- Pihak Terkait Teraktif -->
    <div class="rounded-xl bg-white border border-gray-200 p-4">
      <h3 class="text-sm font-semibold text-gray-800">Pihak Terkait Teraktif</h3>
      <ul class="mt-3 space-y-2 text-sm">
        <li class="flex items-center justify-between"><span>PDAM Kota Makassar</span><span class="text-gray-500">26 riset</span></li>
        <li class="flex items-center justify-between"><span>Dinas Lingkungan Hidup</span><span class="text-gray-500">18 riset</span></li>
        <li class="flex items-center justify-between"><span>DPMPTSP</span><span class="text-gray-500">14 riset</span></li>
        <li class="flex items-center justify-between"><span>Diskominfo</span><span class="text-gray-500">12 riset</span></li>
      </ul>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="rounded-xl bg-white border border-gray-200">
      <div class="px-4 py-3 border-b bg-gray-50">
        <h3 class="text-sm font-semibold text-gray-800">Aktivitas Terbaru</h3>
      </div>
      <ul class="divide-y text-xs">
        <li class="px-4 py-2 flex items-center justify-between">
          <span>Unduh: Monitoring Kualitas Udara</span><span class="text-gray-500">22 Sep 2025 • 10:14</span>
        </li>
        <li class="px-4 py-2 flex items-center justify-between">
          <span>Tambah: Analisis Data Pengaduan Publik</span><span class="text-gray-500">21 Sep 2025 • 16:03</span>
        </li>
        <li class="px-4 py-2 flex items-center justify-between">
          <span>Edit: Peta Jalan Perizinan AI</span><span class="text-gray-500">21 Sep 2025 • 09:27</span>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- How-to / Help -->
<section class="rounded-2xl border border-gray-200 bg-white p-5">
  <div class="grid md:grid-cols-3 gap-6">
    <div>
      <h3 class="text-sm font-semibold text-maroon">Alur Cepat</h3>
      <ol class="mt-3 text-sm space-y-2 text-gray-700 list-decimal list-inside">
        <li>Unggah naskah, isi metadata (judul, abstrak, penulis, tag, lisensi).</li>
        <li>Tandai akses (Publik/Restricted) dan lampirkan dataset (opsional).</li>
        <li>Review internal → publikasi ke SIGAP Riset (dan OJS bila relevan).</li>
      </ol>
    </div>
    <div>
      <h3 class="text-sm font-semibold text-maroon">Tips Kurasi</h3>
      <ul class="mt-3 text-sm space-y-2 text-gray-700 list-disc list-inside">
        <li>Gunakan tag konsisten (kamus tag) untuk kualitas pencarian.</li>
        <li>Lengkapi pihak terkait untuk konteks kebijakan/OPD.</li>
        <li>Pastikan lisensi jelas (CC-BY, CC-BY-NC, dll.).</li>
      </ul>
    </div>
    <div>
      <h3 class="text-sm font-semibold text-maroon">Keamanan & Audit</h3>
      <p class="mt-3 text-sm text-gray-700">
        Setiap aksi lihat/unduh tercatat. Gunakan akses Restricted untuk naskah sensitif dan tulis alasan akses saat dibagikan.
      </p>
    </div>
  </div>
</section>
@endsection
