<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Daftar Akun — SIGAP BRIDA</title>
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
        Sudah punya akun?
        <a href="pegawai-hasil.html" class="font-semibold text-maroon hover:underline">Login</a>
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
            <h1 class="text-3xl font-extrabold">Buat Akun SIGAP</h1>
            <p class="mt-2 text-white/80">Akses arsip dokumen & data pegawai dengan kontrol dan audit ketat.</p>

            <ul class="mt-8 space-y-4 text-sm">
              <li class="flex gap-3">
                <span class="mt-0.5 inline-flex w-6 h-6 items-center justify-center rounded-full bg-white/10">✓</span>
                Hak akses berbasis peran (RBAC)
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 inline-flex w-6 h-6 items-center justify-center rounded-full bg-white/10">✓</span>
                Log aktivitas lengkap (view/unduh)
              </li>
              <li class="flex gap-3">
                <span class="mt-0.5 inline-flex w-6 h-6 items-center justify-center rounded-full bg-white/10">✓</span>
                Single UI untuk Dokumen & Pegawai
              </li>
            </ul>

            <div class="absolute inset-x-0 bottom-0">
              <svg viewBox="0 0 1440 200" class="w-full opacity-20"><path fill="white" d="M0,160L120,149.3C240,139,480,117,720,106.7C960,96,1200,96,1320,96L1440,96L1440,200L1320,200C1200,200,960,200,720,200C480,200,240,200,120,200L0,200Z"></path></svg>
            </div>
          </div>
        </section>

        <!-- Form panel -->
        <section class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-8 shadow-sm">
          <h2 class="text-xl sm:text-2xl font-extrabold text-maroon">Form Registrasi</h2>
          <p class="text-sm text-gray-600 mt-1">Gunakan email dinas bila tersedia. Data akan diverifikasi admin.</p>
          @if ($errors->any())
              <div class="alert alert-danger">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
 
          <form id="regForm" class="mt-6 grid grid-cols-1 gap-4" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Nama Lengkap</span>
                <input required name="name" id="nama" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama sesuai identitas">
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">NIP <span class="text-red-500 font-normal">*</span></span>
                <input required name="nip" id="nip" type="text" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="18xxxxxxxxxxxxxxx" inputmode="numeric">
              </label>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Email / Username</span>
                <input required name="email" id="email" type="email" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="nama@brida.mks.go.id">
              </label>
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Unit/Bagian</span>
                <input required name="unit" id="unit" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Kepegawaian / Bidang X">
              </label>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
              <label class="block">
                <span class="text-sm font-semibold text-gray-700">Nomor Whatsapp</span>
                <input required name="nomor_hp" id="nomor_hp" type="text" required class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nomor Whatsapp">
              </label>
              <div class="grid grid-cols-2 gap-4">
                <label class="block col-span-2 sm:col-span-1">
                  <span class="text-sm font-semibold text-gray-700">Kata Sandi</span>
                  <div class="relative mt-1.5">
                    <input required id="pwd" name="password" type="password" required minlength="8" class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pr-10" placeholder="Min. 8 karakter">
                    <button type="button" class="absolute inset-y-0 right-0 px-3 text-gray-500" onclick="togglePwd('pwd')">👁️</button>
                  </div>
                </label>
                <label class="block col-span-2 sm:col-span-1">
                  <span class="text-sm font-semibold text-gray-700">Konfirmasi</span>
                  <div class="relative mt-1.5">
                    <input required id="pwd2" name="password_confirmation" type="password" required minlength="8" class="w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon pr-10" placeholder="Ulangi kata sandi">
                    <button type="button" class="absolute inset-y-0 right-0 px-3 text-gray-500" onclick="togglePwd('pwd2')">👁️</button>
                  </div>
                </label>
              </div>
            </div>

            <!-- Password strength -->
            <div>
              <div class="flex items-center justify-between text-xs">
                <span class="text-gray-600">Kekuatan Kata Sandi</span>
                <span id="pwdLabel" class="font-semibold text-gray-700">-</span>
              </div>
              <div class="mt-1 h-2 w-full rounded bg-gray-100 overflow-hidden">
                <div id="pwdBar" class="h-full w-0 bg-maroon transition-all"></div>
              </div>
              <ul class="mt-2 text-[12px] text-gray-600 list-disc list-inside">
                <li>Minimal 8 karakter</li>
                <li>Campuran huruf besar, kecil, angka</li>
              </ul>
            </div>

            <!-- Terms -->
            <label class="inline-flex items-start gap-3 text-sm">
              <input id="agree" type="checkbox" class="mt-1 rounded border-gray-300 text-maroon focus:ring-maroon">
              <span>Saya menyetujui <a href="#kebijakan" class="text-maroon hover:underline">Kebijakan Privasi</a> & <a href="#tos" class="text-maroon hover:underline">Syarat Penggunaan</a>.</span>
            </label>

            <div id="formError" class="hidden text-sm text-rose-700 bg-rose-50 border border-rose-200 rounded-md p-3"></div>

            <div class="pt-2 flex items-center gap-3">
              <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">Daftar</button>
              <a href="index.html" class="px-4 py-2.5 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</a>
            </div>
          </form>

          <!-- Info keamanan -->
          <div class="mt-6 text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Registrasi dapat memerlukan persetujuan admin. Aktivitas login/akses akan tercatat untuk audit.</span>
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

  <!-- Success Modal (demo) -->
  <div id="successModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative z-10 max-w-md mx-auto px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
          <h3 class="text-white font-bold">Registrasi Berhasil</h3>
          <p class="text-white/80 text-xs mt-0.5">Akun berhasil dibuat (demo). Silakan login.</p>
        </div>
        <div class="p-5 space-y-3 text-sm">
          <p>Anda akan diarahkan ke halaman login.</p>
          <div class="flex gap-2">
            <a href="pegawai-hasil.html" class="flex-1 px-4 py-2 rounded-lg bg-maroon text-white text-center hover:bg-maroon-800">Ke Login</a>
            <button class="flex-1 px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeSuccess()">Tutup</button>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
  // Show/Hide password
  function togglePwd(id){
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
  }

  // Password strength (sederhana)
  const pwd = document.getElementById('pwd');
  const pwdBar = document.getElementById('pwdBar');
  const pwdLabel = document.getElementById('pwdLabel');
  function scorePassword(p){
    let s = 0;
    if(!p) return 0;
    if(p.length >= 8) s += 1;
    if(/[A-Z]/.test(p)) s += 1;
    if(/[a-z]/.test(p)) s += 1;
    if(/[0-9]/.test(p)) s += 1;
    return s; // 0..4
  }
  function renderStrength(){
    const s = scorePassword(pwd.value);
    const pct = (s/4)*100;
    pwdBar.style.width = pct + '%';
    let label = '-', col = 'bg-gray-300';
    if(s<=1){ label='Lemah'; col='bg-rose-500'; }
    else if(s===2){ label='Sedang'; col='bg-amber-500'; }
    else if(s>=3){ label='Kuat'; col='bg-emerald-600'; }
    pwdBar.className = 'h-full transition-all ' + col;
    pwdLabel.textContent = label;
  }
  pwd.addEventListener('input', renderStrength);
  renderStrength();

  // Submit register (demo)
  function submitRegister(){
    const err = document.getElementById('formError');
    err.classList.add('hidden');
    err.textContent = '';

    const nama = document.getElementById('nama').value.trim();
    const email = document.getElementById('email').value.trim();
    const unit = document.getElementById('unit').value.trim();
    const role = document.getElementById('role').value;
    const p1 = document.getElementById('pwd').value;
    const p2 = document.getElementById('pwd2').value;
    const agree = document.getElementById('agree').checked;

    // Validasi dasar
    if(!nama || !email || !unit || !role || !p1 || !p2){
      showError('Mohon lengkapi semua field wajib.');
      return;
    }
    if(p1 !== p2){
      showError('Konfirmasi kata sandi tidak sama.');
      return;
    }
    if(scorePassword(p1) < 2){
      showError('Kata sandi terlalu lemah. Gunakan huruf besar, kecil, dan angka.');
      return;
    }
    if(!agree){
      showError('Anda harus menyetujui Kebijakan & Syarat.');
      return;
    }

    // DEMO: simpan ke localStorage (di produksi: kirim ke API)
    const users = JSON.parse(localStorage.getItem('sb_users') || '[]');
    if(users.some(u => u.email === email)){
      showError('Email/username sudah terdaftar.');
      return;
    }
    users.push({ nama, email, unit, role, createdAt: new Date().toISOString() });
    localStorage.setItem('sb_users', JSON.stringify(users));

    // Tandai sudah punya akun (opsional)
    localStorage.setItem('sb_auth', 'false');
    localStorage.setItem('sb_user', email);

    openSuccess();
  }

  function showError(msg){
    const err = document.getElementById('formError');
    err.textContent = msg;
    err.classList.remove('hidden');
  }

  function openSuccess(){
    document.getElementById('successModal').classList.remove('hidden');
  }
  function closeSuccess(){
    document.getElementById('successModal').classList.add('hidden');
  }

  // Esc untuk tutup modal sukses
  window.addEventListener('keydown', (e)=> {
    if(e.key === 'Escape'){
      closeSuccess();
    }
  });
</script>

<noscript>
  <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-md mt-4">
    JavaScript dinonaktifkan. Registrasi memerlukan JavaScript untuk validasi.
  </div>
</noscript>

</body>
</html>
