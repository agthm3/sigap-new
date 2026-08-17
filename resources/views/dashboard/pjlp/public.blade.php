@extends('layouts.page')

@section('title', 'SIGAP PJLP — Publikasi Logbook BRIDA')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')

<!-- Hero Section -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/20 text-maroon-100 text-xs font-bold uppercase tracking-[0.2em] mb-4">
                Sistem Pengelolaan Logbook PJLP
            </span>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                SIGAP <span class="text-white/90">PJLP</span>
            </h1>

            <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
                Pusat publikasi dokumentasi kinerja Penyedia Jasa Lainnya Perorangan (PJLP) BRIDA Kota Makassar. Menampilkan evidence harian yang transparan dan terverifikasi.
            </p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="#daftar" class="px-6 py-3 rounded-xl bg-white text-maroon font-semibold text-base hover:bg-white/90 transition">
                    Lihat Galeri Kinerja
                </a>
                
                {{-- Tombol Khusus Akses Internal (Berubah warna & bentuk agar berbeda) --}}
                @auth
                    @hasanyrole('admin|superadmin|verif_pjlp|pjlp')
                        <a href="{{ route('sigap-pjlp.index') }}" class="px-6 py-3 rounded-xl bg-amber-500 text-amber-950 font-bold text-base hover:bg-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.4)] transition border border-amber-300">
                            ⚙️ Masuk Dashboard PJLP
                        </a>
                    @endhasanyrole
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-maroon-700/40 text-white border border-white/30 font-semibold text-base hover:bg-maroon-700/60 transition">
                        Login Petugas
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Statistik -->
<section class="py-14 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm text-center">
                <p class="text-sm font-semibold text-gray-500">Total Tenaga PJLP</p>
                <h3 class="text-3xl font-extrabold text-maroon mt-1">{{ $totalPjlp }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm text-center">
                <p class="text-sm font-semibold text-gray-500">Total Laporan Bulanan</p>
                <h3 class="text-3xl font-extrabold text-maroon mt-1">{{ $totalPeriode }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm text-center">
                <p class="text-sm font-semibold text-gray-500">Evidence Terverifikasi</p>
                <h3 class="text-3xl font-extrabold text-maroon mt-1">{{ $totalEvidence }}</h3>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section id="daftar" class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Filter Pencarian -->
        <form method="GET" action="{{ route('sigap-pjlp.public') }}" class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Nama PJLP</label>
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Masukkan nama PJLP..."
                           class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Bulan/Tahun</label>
                    <input type="month" name="bulan_tahun" value="{{ $kategoriBulan }}" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-maroon text-white text-sm font-bold rounded-xl hover:bg-maroon-800 transition shadow-sm h-[42px]">
                        Tampilkan
                    </button>
                </div>
            </div>
        </form>

        <!-- Grid Cards -->
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($periodes as $item)
            
            <!-- ALPINE.JS Carousel Logic -->
            <article x-data="{
                         currentIndex: 0,
                         slides: {{ json_encode($item->slides) }},
                         init() {
                             if(this.slides.length > 1) {
                                 setInterval(() => {
                                     this.currentIndex = (this.currentIndex + 1) % this.slides.length;
                                 }, 3500); // Ganti foto setiap 3.5 detik
                             }
                         }
                     }" 
                     class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 flex flex-col relative group">
                
                <!-- Badge Bulan & Nama -->
                <div class="absolute top-3 left-3 right-3 z-20 flex justify-between items-start pointer-events-none">
                    <span class="px-3 py-1 rounded-full bg-white/90 backdrop-blur text-maroon text-xs font-extrabold shadow-sm border border-white/40">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $item->bulan_tahun)->translatedFormat('F Y') }}
                    </span>
                    <span class="px-2 py-1 bg-black/60 backdrop-blur text-white text-[10px] font-bold rounded-lg shadow-sm">
                        <span x-text="currentIndex + 1"></span> / <span x-text="slides.length"></span> Foto
                    </span>
                </div>

                <!-- Bagian Gambar (Rotating) -->
                <div class="h-64 w-full bg-gray-100 relative overflow-hidden">
                    <template x-for="(slide, index) in slides" :key="index">
                        <img :src="slide.foto" 
                             x-show="currentIndex === index"
                             x-transition.opacity.duration.700ms
                             alt="Evidence" 
                             class="absolute inset-0 w-full h-full object-cover">
                    </template>
                    <!-- Fallback jika array kosong -->
                    <template x-if="slides.length === 0">
                        <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100 text-sm font-semibold">
                            Tidak ada foto evidence
                        </div>
                    </template>
                </div>

                <!-- Bagian Informasi (Rotating Text) -->
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-100">
                        @if($item->user->profile_photo_path)
                            <img src="{{ asset('storage/' . $item->user->profile_photo_path) }}" alt="Foto" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100">
                        @else
                            <div class="w-10 h-10 rounded-full bg-maroon/10 text-maroon font-bold flex items-center justify-center text-sm">
                                {{ substr($item->user->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="font-extrabold text-gray-900 leading-none">{{ $item->user->name }}</h3>
                            <p class="text-[11px] text-gray-500 font-medium mt-1">{{ $item->user->profile->jabatan ?? 'Tenaga Kebersihan' }}</p>
                        </div>
                    </div>

                    <!-- Detail Kegiatan Rotating -->
                    <template x-if="slides.length > 0">
                        <div class="flex-1">
                            <p class="text-xs font-bold text-maroon mb-1.5" x-text="slides[currentIndex].hari + ', ' + slides[currentIndex].tanggal"></p>
                            <p class="text-sm text-gray-700 leading-relaxed font-medium line-clamp-3" x-text="slides[currentIndex].deskripsi"></p>
                        </div>
                    </template>
                </div>
            </article>
            
            @empty
                <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-12 text-center">
                    <span class="text-4xl mb-3 block">📸</span>
                    <h3 class="text-lg font-bold text-gray-900">Belum ada publikasi logbook</h3>
                    <p class="text-sm text-gray-500 mt-1">Laporan harian PJLP yang telah diverifikasi akan otomatis tampil di sini.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $periodes->links() }}
        </div>
    </div>
</section>

@endsection