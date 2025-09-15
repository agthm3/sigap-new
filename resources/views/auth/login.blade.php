<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login — SIGAP BRIDA</title>
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

  <!-- Navbar mini -->
  <header class="border-b border-maroon/10 bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="index.html" class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500">Sistem Informasi Gabungan Arsip & Privasi</p>
        </div>
      </a>
      <div class="text-sm">
        Belum punya akun?
        <a href="register.html" class="font-semibold text-maroon hover:underline">Daftar</a>
      </div>
    </div>
  </header>

  <!-- Wrapper -->
  <main class="min-h-[calc(100vh-160px)] flex items-center">
    <div class="max-w-7xl mx-auto px-4 w-full">
      <div class="grid lg:grid-cols-2 gap-8 items-stretch">

        <!-- Info panel -->
        <section class="hidden lg:flex rounded-2xl overflow-hidden">
          <div class="relative flex-1 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900 p-10 text-white">
            <h1 class="text-3xl font-extrabold">Masuk ke SIGAP</h1>
            <p class="mt-2 text-white/80">Akses arsip dokumen & data pegawai secara terpusat, aman, dan teraudit.</p>

            <ul class="mt-8 space-y-4 text-sm">
              <li class="flex gap-3">
                <span class="mt-0.5 inline-flex w-6 h-6 items-center justify-center rounded-full bg-white/10">✓</span>
                UI terpadu untuk Dokumen & Pegawai
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 inline-flex w-6 h-6 items-center justify-center rounded-full bg-white/10">✓</span>
                Hak akses berbasis peran (RBAC)
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 inline-flex w-6 h-6 items-center justify-center rounded-full bg-white/10">✓</span>
                Pencatatan lengkap aktivitas view/unduh
              </li>
            </ul>

            <div class="absolute inset-x-0 bottom-0">
              <svg viewBox="0 0 1440 200" class="w-full opacity-20"><path fill="white" d="M0,160L120,149.3C240,139,480,117,720,106.7C960,96,1200,96,1320,96L1440,96L1440,200L1320,200C1200,200,960,200,720,200C480,200,240,200,120,200L0,200Z"></path></svg>
            </div>
          </div>
        </section>

        <!-- Form panel -->
        <section class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-8 shadow-sm">
          <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-extrabold text-maroon">Login</h2>
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-maroon">← Kembali</a>
          </div>
          <p class="text-sm text-gray-600 mt-1">Gunakan email dinas/username dan kata sandi Anda.</p>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
          <form id="loginForm" class="mt-6 grid grid-cols-1 gap-4" action="{{ route('login') }}" method="POST" >
            @csrf
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Email</span>
              <input id="email" name="email" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="nama@brida.mks.go.id / user.unit">
            </label>

            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Kata Sandi</span>
              <div class="relative mt-1.5">
                <input id="password" name="password" type="password" required class="w-full border p-2 rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon pr-10" placeholder="••••••••" autocomplete="current-password">
                <button type="button" class="absolute inset-y-0 right-0 px-3 text-gray-500" onclick="togglePwd()">👁️</button>
              </div>
              <div id="caps" class="hidden mt-1 text-[12px] text-amber-700">Caps Lock aktif</div>
            </label>

            <div class="flex items-center justify-between">
              <label class="inline-flex items-center gap-2 text-sm text-gray-600">
                <input id="remember" type="checkbox" class="rounded border-gray-300 text-maroon focus:ring-maroon">
                Ingat saya
              </label>
              <a href="reset.html" class="text-sm text-maroon hover:underline">Lupa sandi?</a>
            </div>

            <div id="error" class="hidden text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-md p-3"></div>

            <button class="mt-1 px-4 py-2.5 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">Masuk</button>

            <!-- Garis pemisah -->
            {{-- <div class="flex items-center gap-3 my-2">
              <div class="h-px bg-gray-200 flex-1"></div>
              <span class="text-xs text-gray-500">atau</span>
              <div class="h-px bg-gray-200 flex-1"></div>
            </div> --}}

            <!-- SSO placeholder -->
            {{-- <button type="button" class="px-4 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition flex items-center justify-center gap-2" onclick="alert('SSO BRIDA (placeholder). Integrasikan dengan IdP internal.')">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M12 2l2.4 7.4H22l-6.2 4.5L18.3 21 12 16.8 5.7 21l2.5-7.1L2 9.4h7.6L12 2z"/></svg>
              Masuk dengan SSO BRIDA
            </button> --}}
          </form>

          <!-- Catatan keamanan -->
          <div class="mt-6 text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Aktivitas login terekam untuk audit internal. Jangan bagikan kredensial Anda.</span>
            </div>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-8 text-sm text-gray-600">
      © 2025 SIGAP BRIDA • BRIDA Kota Makassar
    </div>
  </footer>

<script>
  // Toggle show/hide password
  function togglePwd(){
    const el = document.getElementById('password');
    el.type = el.type === 'password' ? 'text' : 'password';
  }

  // CapsLock indicator
  const pwdEl = document.getElementById('password');
  const caps = document.getElementById('caps');
  ['keydown','keyup'].forEach(evt => {
    pwdEl.addEventListener(evt, e => {
      const isCaps = e.getModifierState && e.getModifierState('CapsLock');
      caps.classList.toggle('hidden', !isCaps);
    });
  });

  // Helper: ambil query param next
  function getNext(){
    const u = new URL(window.location.href);
    return u.searchParams.get('next') || 'pegawai-hasil.html';
    // contoh: login.html?next=pegawai-detail.html
  }

  // Demo login handler (ganti dengan fetch API di produksi)
  function doLogin(){
    const email = document.getElementById('email').value.trim();
    const pass  = document.getElementById('password').value.trim();
    const remember = document.getElementById('remember').checked;
    const err = document.getElementById('error');

    err.classList.add('hidden');
    err.textContent = '';

    if(!email || !pass){
      err.textContent = 'Mohon isi email/username dan kata sandi.';
      err.classList.remove('hidden');
      return;
    }

    // DEMO: anggap valid. Di produksi: kirim ke server dan verifikasi.
    try{
      if(remember){
        localStorage.setItem('sb_auth','true');
        localStorage.setItem('sb_user', email);
      }else{
        sessionStorage.setItem('sb_auth','true');
        sessionStorage.setItem('sb_user', email);
      }
      // opsional: salin ke localStorage juga agar halaman lain tetap membaca
      localStorage.setItem('sb_auth','true');
      localStorage.setItem('sb_user', email);

      // redirect
      window.location.href = getNext();
    }catch(e){
      err.textContent = 'Terjadi kesalahan saat menyimpan sesi.';
      err.classList.remove('hidden');
    }
  }
</script>

<noscript>
  <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md mt-4">
    JavaScript dinonaktifkan. Login memerlukan JavaScript untuk pemrosesan.
  </div>
</noscript>

</body>
</html>
