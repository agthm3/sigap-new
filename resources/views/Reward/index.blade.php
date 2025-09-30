@extends('layouts.page')

@section('content')
<!-- HERO -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-7xl mx-auto px-4 py-12 sm:py-16">
    <div class="text-center max-w-3xl mx-auto">
      <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs mb-3">
        <span class="h-2 w-2 rounded-full bg-green-400"></span> Penghargaan Kontributor
      </span>
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white">Halaman <span class="text-white/90">Reward</span></h1>
      <p class="mt-3 text-white/80 text-sm sm:text-base">
        Terima kasih kepada rekan-rekan yang telah berkontribusi menjaga keamanan, stabilitas, dan pengembangan <strong>SIGAP BRIDA</strong>.
        Nama Anda adalah bagian dari pondasi kualitas sistem ini. 🙏
      </p>

      <div class="mt-6 flex items-center justify-center gap-3">
        <a href="https://wa.me/6285173231604" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">
          Laporkan Temuan / Usulan
        </a>
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">
          Kembali ke Beranda
        </a>
      </div>
    </div>
  </div>
</section>

<!-- RINGKASAN -->
<section class="py-10 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="grid md:grid-cols-3 gap-4">
      <div class="rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase">Kategori Kontribusi</p>
        <ul class="mt-2 text-sm text-gray-700 space-y-1">
          <li><span class="inline-block w-2 h-2 rounded-full bg-rose-500 mr-2"></span>Keamanan (Vulnerability/Bug) </li>
          <li><span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-2"></span>Fitur/UX</li>
          <li><span class="inline-block w-2 h-2 rounded-full bg-amber-500 mr-2"></span>Data/Validasi</li>
          <li><span class="inline-block w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>Dokumentasi</li>
        </ul>
      </div>
      <div class="rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase">Kriteria Penayangan</p>
        <p class="mt-2 text-sm text-gray-700">
          Temuan/usulan yang valid, bermanfaat, dan telah diverifikasi tim akan kami tampilkan di halaman ini.
          Tingkat keparahan keamanan tidak ditampilkan secara detail demi menjaga privasi & mitigasi risiko.
        </p>
      </div>
      <div class="rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase">Bagaimana Mengirim?</p>
        <p class="mt-2 text-sm text-gray-700">
          Kirim detail singkat (judul, deskripsi, bukti) ke WhatsApp
          <a class="text-maroon font-semibold hover:underline" href="https://wa.me/6285173231604" target="_blank">0851-7323-1604</a>.
          Jika disetujui, nama Anda akan tercantum di sini sebagai bentuk apresiasi.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- DAFTAR KONTRIBUTOR -->
<section class="py-6 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between gap-3">
      <div>
        <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Daftar Kontributor</h2>
        <p class="text-sm text-gray-600 mt-0.5">Urutan terbaru ke terlama.</p>
      </div>
      <!-- Optional filter (non-functional UI placeholder, bisa dihubungkan nanti) -->
      <div class="hidden sm:flex gap-2">
        <button class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-100">Semua</button>
        <button class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-100">Keamanan</button>
        <button class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-100">Fitur/UX</button>
        <button class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-100">Data</button>
        <button class="px-3 py-2 rounded-lg border border-gray-300 text-sm hover:bg-gray-100">Dokumentasi</button>
      </div>
    </div>

    @php
      // Contoh struktur data (hapus saat sudah pakai dari Controller)
      // $contributors = [
      //   [
      //     'name' => 'Andi Pratama',
      //     'photo' => null, // atau URL foto
      //     'category' => 'Keamanan',
      //     'title' => 'Validasi akses kode pada dokumen privasi',
      //     'summary' => 'Menemukan potensi bypass saat input kode akses di endpoint unduh.',
      //     'date' => '2025-09-28',
      //     'severity' => 'Medium', // untuk kategori Keamanan (opsional)
      //   ],
      //   [
      //     'name' => 'Siti Rahma',
      //     'photo' => null,
      //     'category' => 'Fitur/UX',
      //     'title' => 'Perbaikan struktur kartu hasil pencarian',
      //     'summary' => 'Usulan judul/alias dipisah agar mudah dipindai, tambah tombol “salin link”.',
      //     'date' => '2025-09-26',
      //   ],
      // ];
    @endphp

    <div class="mt-6">
      @if (empty($contributors) || count($contributors) === 0)
        <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
          <p class="text-sm text-gray-600">Belum ada kontributor yang ditampilkan.</p>
          <p class="text-sm text-gray-600">Jadilah yang pertama! Laporkan temuan/usulan ke <a class="text-maroon font-semibold hover:underline" href="https://wa.me/6285173231604" target="_blank">0851-7323-1604</a>.</p>
        </div>
      @else
        <ul class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach($contributors as $c)
            <li class="rounded-xl bg-white border border-gray-200 p-5 hover:shadow-lg transition">
              <div class="flex items-start gap-3">
                {{-- Avatar / Inisial --}}
                @php
                  $initials = collect(explode(' ', $c['name'] ?? ''))
                      ->map(fn($p) => mb_substr($p,0,1))
                      ->take(2)->implode('');
                @endphp
                @if(!empty($c['photo']))
                  <img src="{{ $c['photo'] }}" alt="{{ $c['name'] }}" class="h-10 w-10 rounded-lg object-cover">
                @else
                  <div class="h-10 w-10 rounded-lg bg-maroon/10 text-maroon grid place-content-center font-bold">
                    {{ $initials }}
                  </div>
                @endif

                <div class="flex-1 min-w-0">
                  <div class="flex flex-wrap items-center gap-2">
                    <h3 class="font-semibold text-gray-900 truncate">{{ $c['name'] ?? '-' }}</h3>
                    @if(($c['category'] ?? '') === 'Keamanan')
                      <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">Keamanan</span>
                      @if(!empty($c['severity']))
                        <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">{{ $c['severity'] }}</span>
                      @endif
                    @elseif(($c['category'] ?? '') === 'Fitur/UX')
                      <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">Fitur/UX</span>
                    @elseif(($c['category'] ?? '') === 'Data')
                      <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Data</span>
                    @elseif(($c['category'] ?? '') === 'Dokumentasi')
                      <span class="inline-flex items-center gap-1 text-[11px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">Dokumentasi</span>
                    @endif
                  </div>

                  <p class="text-sm text-gray-700 mt-0.5">{{ $c['title'] ?? '-' }}</p>
                  @if(!empty($c['summary']))
                    <p class="text-sm text-gray-600 mt-1">{{ $c['summary'] }}</p>
                  @endif

                  <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                    <span>
                      @php
                        $d = !empty($c['date']) ? \Carbon\Carbon::parse($c['date'])->locale('id')->translatedFormat('d M Y') : '-';
                      @endphp
                      {{ $d }}
                    </span>
                    {{-- Opsional: tautan bukti internal atau PR --}}
                    @if(!empty($c['link']))
                      <a href="{{ $c['link'] }}" class="text-maroon hover:underline" target="_blank">Detail</a>
                    @endif
                  </div>
                </div>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
</section>

<!-- CATATAN PRIVASI -->
<section class="py-10 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="rounded-xl border border-gray-200 p-5">
      <h3 class="font-semibold text-gray-900">Catatan Privasi & Etika</h3>
      <p class="text-sm text-gray-700 mt-1">
        Kami hanya menampilkan <em>nama</em> dan <em>judul kontribusi</em>. Rincian teknis temuan keamanan tidak dipublikasikan
        demi mencegah penyalahgunaan. Jika Anda ingin tidak ditampilkan, silakan hubungi nomor yang sama.
      </p>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-12">
  <div class="max-w-7xl mx-auto px-4">
    <div class="rounded-2xl overflow-hidden">
      <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 sm:px-10 py-10 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
          <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Ingin namamu tercantum di sini?</h3>
          <p class="text-white/80 mt-1 text-sm">Kirim temuan atau usulan perbaikan—kontribusimu berarti bagi kualitas SIGAP.</p>
        </div>
        <a href="https://wa.me/6285173231604" target="_blank"
           class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">Hubungi Kami</a>
      </div>
    </div>
  </div>
</section>
@endsection
