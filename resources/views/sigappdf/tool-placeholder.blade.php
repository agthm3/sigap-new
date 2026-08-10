@extends('layouts.page')

@section('title', ($toolTitle ?? 'PDF Tool') . ' — SIGAP PDF')

@push('head')
<style>
    body { font-family: 'Inter', sans-serif; }
</style>
@endpush

@section('content')

<section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-maroon"></div>
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-[0.2em] mb-3">
                {{ $category ?? 'PDF Tool' }}
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">
                {{ $toolTitle ?? 'Pengolah PDF' }}
            </h1>
            <p class="mt-2 text-white/85 text-sm sm:text-base">
                {{ $toolDesc ?? 'Proses dokumen PDF secara langsung dan aman di browser Anda.' }}
            </p>
        </div>
    </div>
</section>

<section class="py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-12 shadow-sm text-center space-y-4">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto text-xl">
                ⚙️
            </div>
            <h3 class="text-base font-bold text-gray-900">Antarmuka Fitur Sedang Disiapkan</h3>
            <p class="text-xs text-gray-500">Modul pemrosesan client-side untuk fitur ini siap dikembangkan.</p>
            <div>
                <a href="{{ route('sigap-pdf.landing') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition">
                    ← Kembali ke Utama
                </a>
            </div>
        </div>
    </div>
</section>

@endsection