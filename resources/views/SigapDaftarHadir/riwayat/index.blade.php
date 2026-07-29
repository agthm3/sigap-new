@extends('layouts.page')

@section('title', 'Pencarian Riwayat Kehadiran — SIGAP BRIDA Kota Makassar')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

{{-- Hero Section --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/20 text-maroon-100 text-xs font-bold uppercase tracking-[0.2em] mb-4">
                Pencarian Mandiri Kehadiran
            </span>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                SIGAP <span class="text-white/90">RIWAYAT PESERTA</span>
            </h1>

            <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
                Pusat pencarian riwayat partisipasi kegiatan. Cari nama Anda untuk melihat daftar kegiatan yang pernah Anda ikuti di lingkungan BRIDA Kota Makassar.
            </p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="#cari" class="px-6 py-3 rounded-xl bg-white text-maroon font-semibold text-base hover:bg-white/90 transition shadow-sm">
                    Cari Nama Sekarang
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-maroon-700/40 text-white border border-white/30 font-semibold text-base hover:bg-maroon-700/60 transition">
                    Masuk Dashboard
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Stats Section --}}
<section class="py-10 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl mx-auto">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm text-center">
                <p class="text-sm font-medium text-gray-500">Total Peserta Unik</p>
                <h3 class="text-3xl font-extrabold text-maroon mt-1">{{ number_format($totalPesertaTerdaftar) }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm text-center">
                <p class="text-sm font-medium text-gray-500">Total Kehadiran Terdisiplinkan</p>
                <h3 class="text-3xl font-extrabold text-gray-900 mt-1">{{ number_format($totalPartisipasi) }}</h3>
            </div>
        </div>
    </div>
</section>

{{-- Form & Result Section --}}
<section id="cari" class="py-14 bg-gray-50 min-h-[500px]">
    <div class="max-w-5xl mx-auto px-4">
        <div class="mb-6 text-center sm:text-left">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Cari Riwayat Peserta</h2>
            <p class="text-sm text-gray-600 mt-1">
                Masukkan nama lengkap atau potongan nama untuk mencari riwayat kehadiran.
            </p>
        </div>

        {{-- Form Pencarian --}}
        <form method="GET" action="{{ route('sigap-daftar-hadir.public.riwayat-peserta') }}#cari" class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm mb-8">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Peserta</label>
                    <input type="text" name="q" value="{{ $q }}" required
                           placeholder="Ketik nama Anda di sini..."
                           class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon text-sm py-2.5">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition">
                        Cari Riwayat
                    </button>
                </div>
            </div>
        </form>

        {{-- Hasil Pencarian --}}
        @if($q !== '')
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 text-sm">
                        Hasil Pencarian untuk: <span class="text-maroon">"{{ $q }}"</span>
                    </h3>
                    <span class="text-xs text-gray-500 font-medium">{{ $results->count() }} Nama Ditemukan</span>
                </div>

                @if($results->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <p class="font-semibold text-gray-700">Nama tidak ditemukan</p>
                        <p class="text-xs text-gray-500 mt-1">Pastikan ejaan nama sudah sesuai dengan yang diisikan pada formulir kehadiran.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($results as $lowerNama => $items)
                            @php 
                                $firstItem = $items->first(); 
                                $namaAsli  = $firstItem->nama;
                            @endphp
                            <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/80 transition">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">{{ $namaAsli }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Instansi Terakhir: <span class="font-medium text-gray-700">{{ $firstItem->instansi }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Telah Mengikuti <span class="font-bold text-maroon">{{ $items->count() }} Kegiatan</span>
                                    </p>
                                </div>

                                <div>
                                    <a href="{{ route('sigap-daftar-hadir.public.riwayat-peserta.detail', ['nama' => $namaAsli]) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-maroon/10 text-maroon font-semibold text-xs hover:bg-maroon hover:text-white transition">
                                        Lihat Detail Riwayat →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>

{{-- Info Section --}}
<section class="py-12 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Informasi Transparansi</h2>
            <p class="mt-2 text-gray-600 text-sm">
                Portal ini disediakan untuk memudahkan peserta mengecek rekap partisipasi dalam seluruh agenda kegiatan resmi.
            </p>
        </div>

        <div class="mt-8 grid md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">1) Cari Nama</p>
                <p class="mt-2 text-xs text-gray-600 leading-relaxed">Ketikkan nama lengkap Anda pada kolom pencarian di atas untuk menemukan kecocokan data.</p>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">2) Pilih Nama Anda</p>
                <p class="mt-2 text-xs text-gray-600 leading-relaxed">Pilih hasil pencarian yang sesuai dengan instansi dan nama lengkap Anda.</p>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">3) Unduh Sertifikat</p>
                <p class="mt-2 text-xs text-gray-600 leading-relaxed">Lihat daftar kegiatan yang pernah Anda ikuti dan unduh dokumen resmi jika sudah diterbitkan.</p>
            </div>
        </div>
    </div>
</section>

@endsection