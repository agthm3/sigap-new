<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Verifikasi Keaslian Dokumen — SIGAP PJLP</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50: '#fdf7f7', 100: '#faeeee', 200: '#f0d1d1', 300: '#e2a8a8',
              400: '#c86f6f', 500: '#a64040', 600: '#8f2f2f', 700: '#7a2222',
              800: '#661b1b', 900: '#4a1313', DEFAULT: '#7a2222'
            }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col justify-between p-4 sm:p-6">

  <div class="max-w-lg w-full mx-auto my-auto">
    <!-- Header Branding -->
    <div class="text-center mb-5">
      <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-maroon text-white font-extrabold text-lg shadow-md mb-2">
        SB
      </div>
      <h1 class="text-lg font-extrabold text-gray-900 leading-tight">SIGAP BRIDA MAKASSAR</h1>
      <p class="text-xs text-gray-500 font-medium">Sistem Informasi Pengelolaan Evidence & Logbook PJLP</p>
    </div>

    @if($isValid)
      <!-- KARTU VALIDASI RESMI -->
      <div class="bg-white rounded-3xl border border-gray-200/80 shadow-xl overflow-hidden">
        
        <!-- Status Header Banner -->
        <div class="bg-emerald-500 px-5 py-4 text-white text-center">
          <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-1.5 backdrop-blur-xs">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h2 class="text-base font-extrabold tracking-wide uppercase">Dokumen Terverifikasi Sah</h2>
          <p class="text-[11px] text-white/90">Laporan pertanggungjawaban resmi terdaftar pada database sistem SIGAP.</p>
        </div>

        <div class="p-5 space-y-4">
          
          <!-- Identitas Pegawai PJLP -->
          <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-gray-50 border border-gray-200/60">
            @if($user->profile_photo_path)
              <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                   alt="{{ $user->name }}" 
                   class="w-14 h-16 rounded-xl object-cover ring-2 ring-maroon/20 shrink-0">
            @else
              <div class="w-14 h-16 rounded-xl bg-gray-200 text-gray-400 flex items-center justify-center text-xs font-bold shrink-0">
                FOTO
              </div>
            @endif

            <div class="min-w-0">
              <div class="text-[10px] font-bold uppercase tracking-wider text-maroon">Penyedia Jasa Lainnya (PJLP)</div>
              <h3 class="text-sm font-extrabold text-gray-900 truncate">{{ $user->name }}</h3>
            <p class="text-xs text-gray-600 truncate">
                {{ $profile->jabatan ?? 'Penyedia Jasa Lainnya Perorangan (PJLP)' }}
              </p>
              <p class="text-[11px] text-gray-500 font-medium truncate">{{ $user->unit ?: 'BRIDA Kota Makassar' }}</p>
            </div>
          </div>

          <!-- Detail Informasi Rinci -->
          <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1.5 border-b border-gray-100">
              <span class="text-gray-500 font-medium">Periode Kerja</span>
              <span class="font-bold text-gray-900">{{ $namaBulanTahun }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-100">
              <span class="text-gray-500 font-medium">NIP / ID PJLP</span>
              <span class="font-semibold text-gray-800">{{ $user->nip ?: '-' }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-100">
              <span class="text-gray-500 font-medium">NIK (KTP)</span>
              <span class="font-semibold text-gray-800">{{ $profile->nik ? substr($profile->nik, 0, 6) . '******' . substr($profile->nik, -4) : '-' }}</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-100">
              <span class="text-gray-500 font-medium">Total Hari Kerja</span>
              <span class="font-bold text-gray-900">{{ $totalHariKerja }} Hari Kerja</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-100">
              <span class="text-gray-500 font-medium">Evidence Logbook</span>
              <span class="font-bold text-emerald-600">{{ $totalDisetujui }} / {{ $totalHariKerja }} Hari Disetujui</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-100">
              <span class="text-gray-500 font-medium">Dokumen Daftar Gaji</span>
              @if($hasDaftarGaji)
                <span class="inline-flex items-center gap-1 font-bold text-emerald-600">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                  Tervalidasi & Terlampir
                </span>
              @else
                <span class="font-semibold text-amber-600">Belum Terlampir</span>
              @endif
            </div>
            <div class="flex justify-between py-1.5">
              <span class="text-gray-500 font-medium">Status Pengesahan</span>
              <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                FINAL & SAH
              </span>
            </div>
          </div>

          <!-- Informasi Keamanan / Integritas -->
          <div class="p-3 rounded-xl bg-gray-50 border border-gray-200/50 text-[10px] text-gray-500 flex items-start gap-2">
            <span class="text-base shrink-0">🔒</span>
            <p class="leading-relaxed">
              Dokumen ini diverifikasi secara elektronik melalui modul resmi SIGAP PJLP. Integritas data evidence pekerjaan dan daftar gaji dijamin oleh instansi Badan Riset dan Inovasi Daerah (BRIDA).
            </p>
          </div>

        </div>
      </div>
    @else
      <!-- KARTU DOKUMEN TIDAK VALID / PALSU -->
      <div class="bg-white rounded-3xl border border-red-200 shadow-xl overflow-hidden">
        <div class="bg-red-600 px-5 py-6 text-white text-center">
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-2">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
          <h2 class="text-base font-extrabold tracking-wide uppercase">Dokumen Tidak Ditemukan / Tidak Sah</h2>
          <p class="text-xs text-white/90 mt-1">Data verifikasi atau kode token keamanan QR Code tidak sesuai dengan arsip resmi SIGAP.</p>
        </div>

        <div class="p-6 text-center text-xs text-gray-600 space-y-3">
          <p>Pastikan Anda memindai kode QR asli yang diterbitkan langsung oleh sistem resmi <b>SIGAP BRIDA</b>.</p>
          <div class="pt-2">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-800 text-white text-xs font-semibold hover:bg-gray-900 transition">
              ← Kembali ke Beranda
            </a>
          </div>
        </div>
      </div>
    @endif

    <!-- Footer Copyright -->
    <div class="text-center text-[11px] text-gray-400 mt-6">
      © {{ date('Y') }} Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar.<br>
      Hak Cipta Dilindungi Undang-Undang.
    </div>
  </div>

</body>
</html>