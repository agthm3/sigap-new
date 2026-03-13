@extends('layouts.page')

@section('content')
    


<!-- ================= HERO ================= -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-4xl mx-auto px-4 py-16 text-center">
    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
      Verifikasi Sertifikat SIGAP BRIDA
    </h1>
    <p class="mt-3 text-white/80 text-sm sm:text-base">
      Pastikan keaslian sertifikat yang diterbitkan oleh BRIDA Kota Makassar
    </p>
  </div>
</section>

<!-- ================= MAIN ================= -->
<section class="py-16 bg-gray-50">
  <div class="max-w-3xl mx-auto px-4"
       x-data="{
         nomor: '',
         result: null,
         check() {
           if (this.nomor === 'BRIDA-2025-001') {
             this.result = {
               status: 'valid',
               nama: 'Andi Gigatera Halil',
               kegiatan: 'Program Magang Mandiri BRIDA',
               tanggal: '12 Januari – 6 Februari 2026',
               instansi: 'Universitas Bosowa'
             }
           } else {
             this.result = { status: 'invalid' }
           }
         }
       }">

    <div class="bg-white rounded-2xl shadow-xl p-6 sm:p-8">

    <form method="POST" action="{{ route('sigap-sertifikat.verifikasi') }}">
    @csrf

    <div class="mt-6">
    <label class="block text-sm font-semibold text-gray-700 mb-1">
    Nomor Sertifikat
    </label>

    <input
    name="nomor"
    value="{{ $nomor ?? '' }}"
    type="text"
    placeholder="Contoh: BRIDA-2025-001"
    class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-maroon focus:border-maroon"
    />

    </div>

    <div class="mt-4 text-center">
    <button
    class="inline-flex items-center justify-center rounded-lg bg-maroon text-white px-6 py-2.5 hover:bg-maroon-800 transition">
    Verifikasi Sertifikat
    </button>
    </div>

    </form>
    @if(isset($sertifikat) && $sertifikat)
    <div class="mt-8">
        <div class="rounded-xl border border-green-200 bg-green-50 p-5">
            <p class="font-semibold text-green-700 mb-3">
            ✅ Sertifikat Terverifikasi
            </p>
            <ul class="text-sm text-gray-700 space-y-1">
                <li>
                <strong>Nama:</strong>
                {{ $sertifikat->nama_penerima }}
                </li>
                <li>
                <strong>Kegiatan:</strong>
                {{ $sertifikat->kegiatan->nama_kegiatan }}
                </li>
                <li>
                <strong>Periode:</strong>
                {{ $sertifikat->kegiatan->tanggal }}
                </li>
                <li>
                <strong>Instansi:</strong>
                {{ $sertifikat->instansi }}
                </li>
            </ul>
            <div class="mt-6 text-center">
              <a href="{{ route('sigap-sertifikat.view',$sertifikat->id) }}"
              class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">

              Lihat Sertifikat

              </a>
            </div>
        </div>
    </div>

    @endif

    @if(isset($searched) && !$sertifikat)

    <div class="mt-8">
        <div class="rounded-xl border border-red-200 bg-red-50 p-5">
            <p class="font-semibold text-red-700">
            ❌ Nomor sertifikat tidak ditemukan atau tidak valid
            </p>
        </div>
    </div>

    @endif

    </div>

    <!-- Info -->
    <div class="mt-6 text-center text-xs text-gray-500">
      Semua sertifikat yang valid diterbitkan secara resmi oleh BRIDA Kota Makassar
    </div>

  </div>
</section>
<!-- ================= INFO SIGAP SERTIFIKAT ================= -->
<section class="py-16 bg-white">
  <div class="max-w-5xl mx-auto px-4">

    <div class="text-center max-w-2xl mx-auto">
      <h3 class="text-2xl sm:text-3xl font-extrabold text-maroon">
        Apa itu SIGAP Sertifikat?
      </h3>
      <p class="mt-3 text-gray-600">
        SIGAP Sertifikat adalah layanan verifikasi sertifikat resmi yang diterbitkan oleh
        Badan Riset dan Inovasi Daerah Kota Makassar melalui platform SIGAP BRIDA.
      </p>
    </div>

    <div class="mt-10 grid md:grid-cols-3 gap-6">

      <!-- Card 1 -->
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 2L3 7v7c0 5 4 9 9 9s9-4 9-9V7l-9-5z"/>
            <path stroke-width="2" d="M9 14l2 2 4-4"/>
          </svg>
        </div>
        <h4 class="mt-4 font-semibold">Verifikasi Keaslian</h4>
        <p class="mt-1 text-sm text-gray-600">
          Memastikan sertifikat benar-benar diterbitkan oleh BRIDA dan bukan hasil pemalsuan
          atau perubahan data.
        </p>
      </div>

      <!-- Card 2 -->
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-width="2"
                  d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
          </svg>
        </div>
        <h4 class="mt-4 font-semibold">Akses Publik & Transparan</h4>
        <p class="mt-1 text-sm text-gray-600">
          Dapat diakses oleh siapa saja melalui nomor sertifikat, tanpa perlu login,
          untuk keperluan validasi administrasi.
        </p>
      </div>

      <!-- Card 3 -->
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M3 6h18M7 6v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V6"/>
          </svg>
        </div>
        <h4 class="mt-4 font-semibold">Tercatat & Terstandar</h4>
        <p class="mt-1 text-sm text-gray-600">
          Setiap sertifikat memiliki nomor unik, metadata kegiatan,
          serta tercatat dalam sistem resmi SIGAP BRIDA.
        </p>
      </div>

    </div>

    <!-- Note -->
    <div class="mt-10 flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 max-w-3xl mx-auto">
      <svg class="w-5 h-5 mt-0.5 text-amber-600" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
      </svg>
      <p class="text-sm text-amber-800">
        Jika nomor sertifikat tidak ditemukan, berarti sertifikat tersebut
        <strong>tidak terdaftar</strong> atau <strong>bukan diterbitkan secara resmi</strong>
        oleh BRIDA Kota Makassar.
      </p>
    </div>

  </div>
</section>


@endsection