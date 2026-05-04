@extends('layouts.page')

@section('title', 'SIGAP PPD — BRIDA Kota Makassar')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/20 text-maroon-100 text-xs font-bold uppercase tracking-[0.2em] mb-4">
                Sistem Pertanggungjawaban Perjalanan Dinas
            </span>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                SIGAP <span class="text-white/90">PPD</span>
            </h1>

            <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
                Pusat publik untuk melihat kegiatan PPD yang telah terverifikasi, lengkap dengan judul, tanggal, tempat, pegawai terlibat, dan jumlah lembar laporan.
            </p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="#daftar" class="px-6 py-3 rounded-xl bg-white text-maroon font-semibold text-base hover:bg-white/90 transition">
                    Lihat Daftar Kegiatan
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-maroon-700/40 text-white border border-white/30 font-semibold text-base hover:bg-maroon-700/60 transition">
                    Masuk Dashboard
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-14 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Kegiatan</p>
                <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalKegiatan }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Bimtek</p>
                <h3 class="text-2xl font-extrabold text-maroon">{{ $totalBimtek }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Koordinasi</p>
                <h3 class="text-2xl font-extrabold text-maroon">{{ $totalKoordinasi }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Pegawai Terlibat</p>
                <h3 class="text-2xl font-extrabold text-maroon">{{ $totalPegawai }}</h3>
            </div>
        </div>
    </div>
</section>

<section id="daftar" class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Daftar Kegiatan PPD</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Gunakan pencarian untuk mencari kegiatan berdasarkan judul, tempat, atau hari/tanggal.
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('sigap-ppd.public') }}" class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label>
                    <input type="text" name="q" value="{{ $q }}"
                           placeholder="Cari judul, tempat, atau tanggal..."
                           class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
                    <select name="kategori" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
                        <option value="">Semua</option>
                        <option value="bimtek" @selected($kategori === 'bimtek')>Bimtek</option>
                        <option value="koordinasi" @selected($kategori === 'koordinasi')>Koordinasi</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
                        Cari
                    </button>
                </div>
            </div>
        </form>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($kegiatans as $item)
            <article class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="h-48 bg-gray-100 overflow-hidden">
                    @if($item->random_photo)
                        <img src="{{ $item->random_photo }}" alt="{{ $item->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">
                            Belum ada foto
                        </div>
                    @endif
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 leading-snug">
                                {{ $item->judul }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $item->tempat }}
                            </p>
                        </div>

                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                            {{ $item->kategori === 'bimtek' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                            {{ strtoupper($item->kategori) }}
                        </span>
                    </div>

                    <div class="mt-4 text-sm text-gray-700 flex justify-between gap-3">
                        <span class="text-gray-500">Tanggal</span>
                        <span class="font-medium text-gray-900 text-right">{{ $item->hari_tanggal }}</span>
                    </div>
                </div>
            </article>
            @empty
                <div class="col-span-full rounded-2xl border border-gray-200 bg-white p-8 text-center text-gray-500">
                    Belum ada kegiatan PPD yang dipublikasikan.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $kegiatans->links() }}
        </div>
    </div>
</section>

<section class="py-12 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Informasi Publik</h2>
            <p class="mt-2 text-gray-600">
                Halaman ini menampilkan kegiatan PPD yang telah dinyatakan selesai dan siap ditampilkan ke publik.
            </p>
        </div>

        <div class="mt-8 grid md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">1) Cari Kegiatan</p>
                <p class="mt-2 text-sm text-gray-700">Gunakan judul, tempat, atau tanggal untuk menemukan kegiatan yang dibutuhkan.</p>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">2) Lihat Ringkasan</p>
                <p class="mt-2 text-sm text-gray-700">Setiap kartu menampilkan kategori, tanggal, jumlah lembar, dan pegawai yang terlibat.</p>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">3) Verifikasi Dokumen</p>
                <p class="mt-2 text-sm text-gray-700">Dokumen resmi dapat dicek melalui sistem dashboard internal oleh petugas berwenang.</p>
            </div>
        </div>
    </div>
</section>

@endsection