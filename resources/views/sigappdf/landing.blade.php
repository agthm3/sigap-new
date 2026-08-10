@extends('layouts.page')

@section('title', 'SIGAP PDF — Pengolah PDF Gratis & Diproses di Browser')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

<!-- Hero Header Maroon -->
<section class="relative overflow-hidden bg-maroon text-white py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 text-center">
        
        <!-- Privacy Badge Header -->
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-semibold mb-4">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>🔒 100% Client-Side Engine — File Tidak Pernah Diunggah</span>
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
            SIGAP <span class="text-white/90">PDF</span>
        </h1>
        <p class="mt-2 text-sm sm:text-base text-white/85 max-w-xl mx-auto font-normal">
            Pilih alat pengolah PDF di bawah ini. Semua dokumen diproses cepat & aman di browser Anda.
        </p>

        <!-- Search Bar Cepat Alat PDF -->
        <div class="mt-6 max-w-md mx-auto" x-data="{ search: '' }">
            <div class="relative">
                <input type="text" 
                       x-model="search"
                       @input="$dispatch('filter-tools', search)"
                       placeholder="🔍 Cari alat (misal: merge, compress, versi, watermark)..." 
                       class="w-full py-3 pl-4 pr-10 rounded-2xl bg-white text-gray-800 text-sm font-medium shadow-lg border-0 focus:ring-2 focus:ring-white/50 focus:outline-none placeholder-gray-400">
            </div>
        </div>

    </div>
</section>

<!-- Cara Kerja (3 Langkah Cepat) -->
<section class="py-8 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
            
            <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-200/60">
                <div class="w-10 h-10 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center font-extrabold shrink-0">
                    1
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-gray-900">Pilih Tools</h4>
                    <p class="text-[11px] text-gray-500">Klik alat yang ingin digunakan.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-200/60">
                <div class="w-10 h-10 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center font-extrabold shrink-0">
                    2
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-gray-900">Drag & Drop</h4>
                    <p class="text-[11px] text-gray-500">Tarik file PDF dari komputer Anda.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 p-3 rounded-2xl bg-gray-50 border border-gray-200/60">
                <div class="w-10 h-10 rounded-xl bg-maroon/10 text-maroon flex items-center justify-center font-extrabold shrink-0">
                    3
                </div>
                <div class="text-left">
                    <h4 class="text-xs font-bold text-gray-900">Unduh Hasil</h4>
                    <p class="text-[11px] text-gray-500">File langsung diolah & siap diunduh.</p>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Katalog Alat Utama -->
<section class="py-10 bg-gray-50" x-data="{ query: '' }" @filter-tools.window="query = $event.detail.toLowerCase()">
    <div class="max-w-7xl mx-auto px-4 space-y-8">

        <!-- Favorit / Paling Sering Digunakan -->
        <div>
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">🔥 Alat Populer</h2>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                
                <a href="{{ route('sigap-pdf.merge') }}" 
                   x-show="!query || 'merge gabung PDF'.includes(query)"
                   class="flex flex-col items-center p-5 rounded-2xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-md transition text-center group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition">🧩</span>
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-maroon">Merge PDF</h3>
                    <span class="mt-2 px-2.5 py-1 rounded-lg bg-maroon text-white text-[10px] font-semibold">Klik di sini →</span>
                </a>

                <a href="{{ route('sigap-pdf.compress') }}" 
                   x-show="!query || 'compress kompres kecilkan ukuran PDF'.includes(query)"
                   class="flex flex-col items-center p-5 rounded-2xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-md transition text-center group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition">📉</span>
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-maroon">Compress PDF</h3>
                    <span class="mt-2 px-2.5 py-1 rounded-lg bg-maroon text-white text-[10px] font-semibold">Klik di sini →</span>
                </a>

                <a href="{{ route('sigap-pdf.split') }}" 
                   x-show="!query || 'split pisah halaman PDF'.includes(query)"
                   class="flex flex-col items-center p-5 rounded-2xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-md transition text-center group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition">✂️</span>
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-maroon">Split PDF</h3>
                    <span class="mt-2 px-2.5 py-1 rounded-lg bg-maroon text-white text-[10px] font-semibold">Klik di sini →</span>
                </a>

                <a href="{{ route('sigap-pdf.jpg-to-pdf') }}" 
                   x-show="!query || 'convert foto gambar jpg png ke pdf'.includes(query)"
                   class="flex flex-col items-center p-5 rounded-2xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-md transition text-center group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition">🖼️</span>
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-maroon">Gambar → PDF</h3>
                    <span class="mt-2 px-2.5 py-1 rounded-lg bg-maroon text-white text-[10px] font-semibold">Klik di sini →</span>
                </a>

                <!-- FITUR BARU: PDF VERSION CONVERTER -->
                <a href="{{ route('sigap-pdf.change-version') }}" 
                   x-show="!query || 'versi version ubah pdf 1.4 1.5 1.7 2.0'.includes(query)"
                   class="flex flex-col items-center p-5 rounded-2xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-md transition text-center group">
                    <span class="text-3xl mb-2 group-hover:scale-110 transition">⚙️</span>
                    <h3 class="text-sm font-bold text-gray-900 group-hover:text-maroon">Ubah Versi PDF</h3>
                    <span class="mt-2 px-2.5 py-1 rounded-lg bg-maroon text-white text-[10px] font-semibold">Klik di sini →</span>
                </a>

            </div>
        </div>

        <!-- Kategori Semua Fitur -->
        <div class="space-y-6">
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider">🛠️ Semua Alat Pengolah PDF</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                <a href="{{ route('sigap-pdf.change-version') }}" 
                   x-show="!query || 'versi version ubah pdf 1.4 1.5 1.7 2.0'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚙️</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">Ubah Versi PDF</h4>
                            <p class="text-[11px] text-gray-500">Konversi versi spesifikasi PDF (1.4, 1.5, 1.7, 2.0).</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

                <a href="{{ route('sigap-pdf.add-password') }}" 
                   x-show="!query || 'password sandi kunci proteksi PDF'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔑</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">Proteksi Password</h4>
                            <p class="text-[11px] text-gray-500">Kunci dokumen PDF.</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

                <a href="{{ route('sigap-pdf.rotate') }}" 
                   x-show="!query || 'rotate putar orientasi PDF'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🔄</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">Rotate PDF</h4>
                            <p class="text-[11px] text-gray-500">Putar posisi halaman.</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

                <a href="{{ route('sigap-pdf.delete-pages') }}" 
                   x-show="!query || 'delete hapus halaman PDF'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🗑️</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">Hapus Halaman</h4>
                            <p class="text-[11px] text-gray-500">Hapus lembar tertentu.</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

                <a href="{{ route('sigap-pdf.watermark') }}" 
                   x-show="!query || 'watermark stempel teks PDF'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🏷️</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">Add Watermark</h4>
                            <p class="text-[11px] text-gray-500">Tambah stempel / teks.</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

                <a href="{{ route('sigap-pdf.pdf-to-image') }}" 
                   x-show="!query || 'pdf to image jpg png ubah ke gambar'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">📸</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">PDF → Gambar</h4>
                            <p class="text-[11px] text-gray-500">Simpan PDF ke JPG / PNG.</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

                <a href="{{ route('sigap-pdf.remove-metadata') }}" 
                   x-show="!query || 'metadata hapus jejak berkas PDF'.includes(query)"
                   class="p-4 rounded-xl bg-white border border-gray-200 hover:border-maroon shadow-2xs hover:shadow-sm transition flex items-center justify-between group">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">🧹</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 group-hover:text-maroon">Hapus Metadata</h4>
                            <p class="text-[11px] text-gray-500">Bersihkan identitas berkas.</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-400 group-hover:text-maroon font-bold">Buka →</span>
                </a>

            </div>
        </div>

    </div>
</section>

<!-- Banner Keamanan Ringkas -->
<section class="py-6 bg-white border-t border-gray-100 text-center">
    <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-600">
        <div class="flex items-center gap-2">
            <span class="text-lg">🔒</span>
            <span class="font-semibold text-gray-800">Keamanan Terjamin:</span>
            <span>Dokumen tidak pernah keluar dari memori browser Anda.</span>
        </div>
        <span class="font-extrabold text-maroon bg-maroon/5 px-3 py-1 rounded-full border border-maroon/20">
            SIGAP PDF Client-Side Engine
        </span>
    </div>
</section>

@endsection