@extends('layouts.page')

@section('title', 'SIGAP PIC — BRIDA Kota Makassar')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-16">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 border border-white/20 text-white/80 text-xs font-bold uppercase tracking-[0.2em] mb-4">
                Pusat Informasi Penanggung Jawab
            </span>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">
                SIGAP <span class="text-white/90">PIC</span>
            </h1>

            <p class="mt-4 text-white/85 text-base sm:text-lg leading-relaxed">
                Direktori publik sistem, aplikasi, dan tanggung jawab kerja di lingkungan BRIDA Kota Makassar — lengkap dengan penanggung jawab setiap sistem.
            </p>

            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <a href="#daftar" class="px-6 py-3 rounded-xl bg-white text-maroon font-semibold text-base hover:bg-white/90 transition">
                    Lihat Daftar Sistem
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3 rounded-xl bg-maroon-700/40 text-white border border-white/30 font-semibold text-base hover:bg-maroon-700/60 transition">
                    Masuk Dashboard
                </a>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="py-14 bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total Sistem</p>
                <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalSystems }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Sistem Aktif</p>
                <h3 class="text-2xl font-extrabold text-emerald-600">{{ $activeSystems }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Total PIC</p>
                <h3 class="text-2xl font-extrabold text-maroon">{{ $totalPic }}</h3>
            </div>
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm text-gray-500">Kategori Sistem</p>
                <h3 class="text-2xl font-extrabold text-blue-600">{{ $totalKategori }}</h3>
            </div>
        </div>
    </div>
</section>

{{-- DAFTAR SISTEM --}}
<section id="daftar" class="py-14 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">

        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Daftar Sistem & PIC</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Cari sistem berdasarkan nama, PIC, atau kategori.
                </p>
            </div>
        </div>

        {{-- FILTER --}}
        <form method="GET" action="{{ route('sigap-pic.public') }}"
              class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Cari</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ $q }}"
                               placeholder="Cari sistem, nama PIC, atau unit..."
                               class="w-full pl-9 rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
                        </svg>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Kategori</label>
                    <select name="kategori" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
                        <option value="">Semua Kategori</option>
                        @foreach(['internal' => 'Internal', 'publik' => 'Publik', 'khusus' => 'Khusus', 'lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}" @selected($kategori === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
                        Cari
                    </button>
                </div>
            </div>
        </form>

        {{-- GRID SISTEM --}}
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($systems as $item)
            <article class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition flex flex-col">

                {{-- Header warna berdasarkan status --}}
                <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-maroon text-white flex items-center justify-center font-extrabold text-lg shrink-0">
                            {{ strtoupper(substr($item->nama_sistem ?? 'S', 0, 2)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-gray-900 leading-snug truncate">
                                {{ $item->nama_sistem }}
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">
                                {{ $item->deskripsi ?: 'Tidak ada deskripsi.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                            {{ $item->status === 'aktif'
                                ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                                : ($item->status === 'maintenance'
                                    ? 'bg-amber-50 border-amber-200 text-amber-700'
                                    : 'bg-gray-50 border-gray-200 text-gray-700') }}">
                            {{ strtoupper($item->status) }}
                        </span>

                        @if($item->kategori)
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border bg-blue-50 border-blue-200 text-blue-700">
                                {{ strtoupper($item->kategori) }}
                            </span>
                        @endif

                        @if($item->level_kritis)
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                                {{ $item->level_kritis === 'tinggi'
                                    ? 'bg-red-50 border-red-200 text-red-700'
                                    : ($item->level_kritis === 'sedang'
                                        ? 'bg-yellow-50 border-yellow-200 text-yellow-700'
                                        : 'bg-emerald-50 border-emerald-200 text-emerald-700') }}">
                                KRITIS: {{ strtoupper($item->level_kritis) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-5 flex-1 flex flex-col gap-4">

                    {{-- URL --}}
                    @if($item->url)
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer"
                               class="text-maroon hover:underline truncate">
                                {{ $item->url }}
                            </a>
                        </div>
                    @endif

                    {{-- PIC List --}}
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 mb-2">
                            Penanggung Jawab
                        </p>

                        <div class="space-y-1.5">
                            @forelse($item->assignments->sortBy('urutan') as $pic)
                                <div class="flex items-center gap-2">
                                    <div class="h-7 w-7 rounded-full bg-maroon/10 text-maroon flex items-center justify-center text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($pic->user->name ?? $pic->nama_pic ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $pic->user->name ?? $pic->nama_pic ?? '-' }}
                                        </span>
                                        @if($pic->user->unit ?? $pic->bidang ?? null)
                                            <span class="text-xs text-gray-400 ml-1">
                                                — {{ $pic->user->unit ?? $pic->bidang }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($pic->is_primary)
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-maroon text-white shrink-0">UTAMA</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 italic">Belum ada PIC terdaftar.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Footer meta --}}
                    <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-400">
                        <span>Update: {{ $item->updated_at?->format('d M Y') ?? '-' }}</span>
                        <span>{{ $item->pic_count }} PIC</span>
                    </div>
                </div>
            </article>
            @empty
                <div class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
                    Belum ada sistem yang dipublikasikan.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $systems->links() }}
        </div>
    </div>
</section>

{{-- INFO PUBLIK --}}
<section class="py-12 bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Informasi Publik</h2>
            <p class="mt-2 text-gray-600">
                Halaman ini hanya menampilkan nama sistem, status, kategori, URL, dan penanggung jawab. Data sensitif seperti akun dan password hanya dapat diakses melalui dashboard internal.
            </p>
        </div>

        <div class="mt-8 grid md:grid-cols-3 gap-4">
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">1) Cari Sistem</p>
                <p class="mt-2 text-sm text-gray-700">Gunakan nama sistem, nama PIC, atau unit untuk menemukan sistem yang dibutuhkan.</p>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">2) Lihat Penanggung Jawab</p>
                <p class="mt-2 text-sm text-gray-700">Setiap sistem mencantumkan nama PIC utama dan anggota tim yang bertanggung jawab.</p>
            </div>
            <div class="rounded-2xl bg-gray-50 border border-gray-200 p-6">
                <p class="text-sm font-semibold text-maroon">3) Akses Penuh via Dashboard</p>
                <p class="mt-2 text-sm text-gray-700">Informasi lengkap termasuk akun dan credential hanya tersedia untuk petugas berwenang melalui login.</p>
            </div>
        </div>
    </div>
</section>

@endsection