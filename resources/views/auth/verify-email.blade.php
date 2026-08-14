<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Verifikasi OTP Email — SIGAP BRIDA</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            maroon: {
              50:'#fdf7f7',100:'#faeeee',200:'#f0d1d1',300:'#e2a8a8',
              400:'#c86f6f',500:'#a64040',600:'#8f2f2f',700:'#7a2222',
              800:'#661b1b',900:'#4a1313', DEFAULT:'#7a2222'
            }
          }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>body{font-family:Inter,system-ui,Segoe UI,Roboto,Helvetica,Arial,sans-serif}</style>
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Header -->
  <header class="border-b border-maroon/10 bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="/" class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500">Sistem Informasi Gabungan Arsip & Privasi</p>
        </div>
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="min-h-[calc(100vh-160px)] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm text-center">
      
      <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-maroon/10 text-maroon mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
      </div>

      <h2 class="text-2xl font-extrabold text-maroon">Verifikasi Email Anda</h2>
      <p class="text-sm text-gray-600 mt-2">
        Kode 6-digit OTP telah dikirimkan ke email <span class="font-semibold text-gray-800">{{ auth()->user()->email }}</span>. Masukkan kode di bawah ini untuk melanjutkan.
      </p>

      @if (session('status'))
        <div class="mt-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs text-left">
          {{ session('status') }}
        </div>
      @endif

      @if ($errors->any())
        <div class="mt-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs text-left">
          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('verification.otp.verify') }}" class="mt-6 space-y-4">
        @csrf
        <div>
          <label for="otp" class="sr-only">Kode OTP</label>
          <input 
            id="otp" 
            name="otp" 
            type="text" 
            inputmode="numeric" 
            maxlength="6" 
            required 
            autofocus 
            placeholder="000000"
            class="w-full text-center text-3xl font-bold tracking-[0.5em] py-3 rounded-xl border border-gray-300 focus:border-maroon focus:ring-maroon text-maroon uppercase"
          >
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-maroon text-white font-semibold hover:bg-maroon-800 transition">
          Verifikasi Akun
        </button>
      </form>

      <div class="mt-6 flex items-center justify-between text-xs text-gray-500 border-t border-gray-100 pt-4">
        <form method="POST" action="{{ route('verification.otp.resend') }}">
          @csrf
          <button type="submit" class="font-medium text-maroon hover:underline">
            Kirim Ulang Kode OTP
          </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="hover:underline">
            Keluar / Logout
          </button>
        </form>
      </div>

    </div>
  </main>

  <footer class="border-t border-gray-200 text-center py-4 text-xs text-gray-500">
    © 2025 SIGAP BRIDA • BRIDA Kota Makassar
  </footer>

</body>
</html>