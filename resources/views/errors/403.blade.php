@extends('errors.layout')

@section('title', 'Akses Ditolak')
@section('code', '403')
@section('heading', 'Forbidden / Akses Ditolak')

@section('message')
    Anda tidak memiliki hak akses atau izin yang memadai untuk membuka halaman atau memproses dokumen SPJ ini. Silakan hubungi administrator jika Anda merasa ini adalah sebuah kekeliruan.
@endsection

@section('details')
    @if(isset($exception) && $exception->getMessage())
        <details class="text-left bg-gray-50 border border-gray-200 rounded-xl p-3 mb-8 group transition-all">
            <summary class="cursor-pointer text-xs font-semibold text-gray-600 hover:text-maroon flex items-center justify-between select-none list-none">
                <span class="inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Lihat Detail Log Error Teknis
                </span>
                <svg class="w-4 h-4 text-gray-400 transform group-open:rotate-180 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </summary>
            
            <div class="mt-3 pt-3 border-t border-gray-200">
                <pre class="text-xs text-red-600 font-mono bg-white p-3 rounded-lg border border-gray-200 overflow-x-auto whitespace-pre-wrap break-words leading-relaxed max-h-48 overflow-y-auto">{{ $exception->getMessage() }}</pre>
            </div>
        </details>
    @endif
@endsection