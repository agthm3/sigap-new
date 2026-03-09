{{-- resources/views/about.blade.php --}}
@extends('layouts.page')

@push('head')
<style>
  @keyframes floaty {0%{transform:translateY(0)}50%{transform:translateY(-8px)}100%{transform:translateY(0)}}
  @keyframes wiggle {0%,100%{transform:rotate(0deg)}25%{transform:rotate(2deg)}75%{transform:rotate(-2deg)}}
  .marble {background-image: radial-gradient(transparent 1px, rgba(255,255,255,.6) 1px),radial-gradient(transparent 2px, rgba(255,255,255,.5) 2px);background-size: 24px 24px, 40px 40px;background-position: -10px -10px, 6px 6px}
  .card-pop {transition: transform .25s ease, box-shadow .25s ease}
  .card-pop:hover {transform: translateY(-4px); box-shadow: 0 12px 28px rgba(0,0,0,.08)}
</style>
@endpush

@section('content')
<!-- Hero: Playful Intro -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900 marble"></div>
  <div class="max-w-7xl mx-auto px-4 py-10 sm:py-16">
    <div class="grid lg:grid-cols-[1.25fr,1fr] items-center gap-8">
      <div class="text-white">
        <div class="inline-flex items-center gap-2 bg-white/10 rounded-full px-3 py-1 text-xs">
          <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>
          <span>Versi Publik • Aktif</span>
        </div>
        <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold leading-tight">
          Tentang <span class="underline decoration-amber-300 decoration-4 underline-offset-4">SIGAP BRIDA</span>
        </h1>
        <p class="mt-3 text-white/90 max-w-2xl">
          Sistem Informasi Gabungan Arsip & Privasi—tempat semua dokumen <em>fix</em> BRIDA Kota Makassar disatukan.
          Cari cepat, unduh aman, dan tinggalkan jejak akses demi transparansi. Santai, rapi, <strong>sigap</strong>.
        </p>

        <!-- Fun Buttons -->
        <div class="mt-5 flex flex-wrap gap-3">
          <button id="btn-acronym" class="px-4 py-2 rounded-lg bg-white text-maroon font-semibold hover:bg-amber-50 card-pop">
            Liat kepanjangan "SIGAP" 😎
          </button>
          <button id="btn-easter" class="px-4 py-2 rounded-lg bg-amber-300/90 text-maroon-900 font-semibold hover:bg-amber-200 card-pop">
            Easter Egg 🎁
          </button>
        </div>

        <!-- Acronym Reveal -->
        <div id="acronym" class="mt-4 grid grid-cols-2 sm:grid-cols-5 gap-2 hidden">
          @php
            $ac = [
              ['S','Sistem'],
              ['I','Informasi'],
              ['G','Gabungan'],
              ['A','Arsip'],
              ['P','& Privasi'],
            ];
          @endphp
          @foreach($ac as [$l,$t])
          <div class="rounded-xl bg-white/15 backdrop-blur border border-white/20 p-3 text-center card-pop">
            <div class="text-2xl font-extrabold">{{$l}}</div>
            <div class="text-xs text-white/80">{{$t}}</div>
          </div>
          @endforeach
        </div>
      </div>

      <!-- Mascot-ish Badge -->
      <div class="relative">
        <div class="rounded-2xl bg-white shadow-2xl p-6 lg:p-8 card-pop">
          <div class="flex items-center gap-3">
            <div class="h-12 w-12 rounded-2xl bg-maroon text-white grid place-content-center text-2xl font-extrabold animate-[floaty_3s_ease-in-out_infinite]">SB</div>
            <div>
              <p class="font-extrabold text-maroon">Halo, Arsipers! 👋</p>
              <p class="text-sm text-gray-600">Aku si <em>Penjaga Arsip</em>. Klik-klik aja di sini, aman kok 😉</p>
            </div>
          </div>
          <ul class="mt-4 space-y-2 text-sm">
            <li class="flex items-start gap-2"><span>✅</span><span>Dokumen resmi versi final—bukan draf nyasar.</span></li>
            <li class="flex items-start gap-2"><span>🔐</span><span>Data privat dilindungi <em>access code</em> & log akses.</span></li>
            <li class="flex items-start gap-2"><span>🕵️</span><span>Jejak unduh jelas: siapa, kapan, dari mana.</span></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
{{-- === Developer Profil (with photo support) === --}}
<section id="developer" class="max-w-7xl mx-auto px-4 py-10">
  <div class="flex items-center justify-between gap-3">
    <div>
      <h2 class="text-2xl font-extrabold text-gray-900">Orang di Balik SIGAP</h2>
      <p class="text-sm text-gray-600">Dua sekawan, kopi kental & commit mantul. ☕️💻</p>
    </div>
    <div class="hidden sm:flex items-center gap-2">
      <span class="inline-flex items-center gap-2 text-xs px-3 py-1 rounded-full bg-maroon text-white">
        <span class="h-2 w-2 rounded-full bg-emerald-300 animate-pulse"></span>Built in Makassar with 100% pure love, 45% caffeine, and 38% loneliness (Giga).
      </span>
    </div>
  </div>

  <div class="mt-6 grid md:grid-cols-2 gap-5">
    {{-- DEV 1 --}}
    <article class="rounded-2xl border border-gray-200 p-5 sm:p-6 card-pop relative overflow-hidden">
      <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-maroon/10 blur-xl"></div>
      <header class="flex items-center gap-4">
        <div class="relative">
          {{-- FOTO PROFIL: ganti src di bawah ini --}}
          <img
            src="{{ asset('storage/about/giga.jpg') }}"
            alt="Giga – Lead Dev"
            class="h-14 w-14 rounded-2xl object-cover ring-2 ring-maroon/20 shadow-sm avatar-img"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')"
          >
          {{-- Fallback inisial bila foto error/ga ada --}}
          <div class="hidden h-14 w-14 rounded-2xl bg-maroon text-white grid place-content-center text-xl font-extrabold avatar-fallback">
            GI
          </div>
          <span class="absolute -bottom-1 -right-1 h-6 w-6 grid place-content-center rounded-full bg-amber-300 text-[11px] font-bold">⚡</span>
        </div>
        <div>
          <h3 class="font-extrabold">Giga — Lead Dev</h3>
          <p class="text-xs text-gray-500">Laravel · Tailwind · Repo Pattern · Fakir Cinta</p>
        </div>
      </header>

      <p class="mt-3 text-sm text-gray-700">
        p info loker sidejob
      </p>

      <ul class="mt-4 flex flex-wrap gap-2 text-xs">
        @foreach (['Laravel','Blade','Tailwind','MySQL','n8n','GitHub Actions','Security'] as $skill)
          <li class="px-2.5 py-1 rounded-lg bg-gray-50 border border-gray-200 hover:border-maroon hover:scale-[1.03] transition [animation:wiggle_.8s_ease-in-out_0s_1]">{{$skill}}</li>
        @endforeach
      </ul>

      <div class="mt-5 flex items-center gap-2 flex-wrap">
        <a href="mailto:giga.makkasau@gmail.com" class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">Email</a>
        <a href="https://wa.me/6285173231604" target="_blank" class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">WhatsApp</a>
        <a href="https://github.com/agthm3" target="_blank" class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">GitHub</a>
      </div>

      <div class="mt-3 text-xs text-gray-500 flex items-center gap-2">
        <span id="kudos-1" class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100">Kudos: <strong>0</strong></span>
        <span id="fun-1" class="hidden text-gray-600">“Kukira keras, ternyata kertas... capek ”</span>
      </div>
    </article>

    {{-- DEV 2 --}}
    <article class="rounded-2xl border border-gray-200 p-5 sm:p-6 card-pop relative overflow-hidden">
      <div class="absolute -left-6 -bottom-6 h-24 w-24 rounded-full bg-maroon/10 blur-xl"></div>
      <header class="flex items-center gap-4">
        <div class="relative">
          {{-- FOTO PROFIL: ganti src di bawah ini --}}
          <img
            src="{{ asset('storage/about/indar.jpeg') }}"
            alt="Partner — UX & Data"
            class="h-14 w-14 rounded-2xl object-cover ring-2 ring-maroon/20 shadow-sm avatar-img"
            loading="lazy"
            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden')"
          >
          {{-- Fallback inisial --}}
          <div class="hidden h-14 w-14 rounded-2xl bg-maroon text-white grid place-content-center text-xl font-extrabold avatar-fallback">
            PN
          </div>
          <span class="absolute -bottom-1 -right-1 h-6 w-6 grid place-content-center rounded-full bg-emerald-400 text-[11px] font-bold">✔</span>
        </div>
        <div>
          <h3 class="font-extrabold">Indar — UX & Data</h3>
          <p class="text-xs text-gray-500">PHP </p>
        </div>
      </header>

      <p class="mt-3 text-sm text-gray-700">
       Bikin website ini, sekali-sekali bikin stress, sekali-sekali bikin ketawa, sekali-kali tidak jadi jadi wkwkwjwk
      </p>

      <ul class="mt-4 flex flex-wrap gap-2 text-xs">
        @foreach (['PHP','Corel','MySQL'] as $skill)
          <li class="px-2.5 py-1 rounded-lg bg-gray-50 border border-gray-200 hover:border-maroon hover:scale-[1.03] transition [animation:wiggle_.8s_ease-in-out_0s_1]">{{$skill}}</li>
        @endforeach
      </ul>

      <div class="mt-5 flex items-center gap-2 flex-wrap">
        <a href="mailto:email.partner@brida.makassar.go.id" class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">Email</a>
        <a href="https://www.linkedin.com/in/your-profile" target="_blank" class="px-3 py-1.5 rounded-lg border text-sm hover:bg-gray-50">LinkedIn</a>
      </div>

      <div class="mt-3 text-xs text-gray-500 flex items-center gap-2">
        <span id="kudos-2" class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100">Kudos: <strong>0</strong></span>
        <span id="fun-2" class="hidden text-gray-600">“Pernah menamai variabel <code>tehManisBanget</code> lalu menyesal 3 bulan.”</span>
      </div>
    </article>
  </div>

</section>


<!-- Modules: Interactive Cards -->
<section class="max-w-7xl mx-auto px-4 py-10" id="fitur">
  <h2 class="text-2xl font-extrabold text-gray-900">Apa saja di dalamnya?</h2>
  <p class="text-gray-600 mt-1">Tiga modul inti, gaya perpustakaan modern.</p>

  <div class="mt-6 grid md:grid-cols-3 gap-5">
    <!-- Dokumen -->
    <article class="rounded-2xl border border-gray-200 p-5 card-pop">
      <header class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-maroon text-white grid place-content-center text-lg font-bold">D</div>
        <div>
          <h3 class="font-extrabold">SIGAP Dokumen</h3>
          <p class="text-xs text-gray-500">Repositori dokumen resmi</p>
        </div>
      </header>
      <p class="mt-3 text-sm text-gray-700">Cari Perwali, SOP, surat edaran, hingga berkas rapat—semua versi final.</p>
      <div class="mt-4 flex items-center justify-between">
        <button data-demo="dokumen" class="px-3 py-1.5 text-sm rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white">Lihat Demo</button>
        <span class="text-xs text-gray-500">Indexed • Tertata</span>
      </div>
    </article>

    <!-- Pegawai -->
    <article class="rounded-2xl border border-gray-200 p-5 card-pop">
      <header class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-maroon text-white grid place-content-center text-lg font-bold">P</div>
        <div>
          <h3 class="font-extrabold">SIGAP Pegawai</h3>
          <p class="text-xs text-gray-500">Data & dokumen privat</p>
        </div>
      </header>
      <p class="mt-3 text-sm text-gray-700">KTP/KK, SK, ijazah, dsb. Akses pakai kode & tercatat rapi di <em>history akses</em>.</p>
      <div class="mt-4 flex items-center justify-between">
        <button data-demo="pegawai" class="px-3 py-1.5 text-sm rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white">Lihat Demo</button>
        <span class="text-xs text-gray-500">Aman • Terkontrol</span>
      </div>
    </article>

    <!-- Inovasi -->
    <article class="rounded-2xl border border-gray-200 p-5 card-pop">
      <header class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-maroon text-white grid place-content-center text-lg font-bold">I</div>
        <div>
          <h3 class="font-extrabold">SIGAP Inovasi</h3>
          <p class="text-xs text-gray-500">Direktori & evidensi</p>
        </div>
      </header>
      <p class="mt-3 text-sm text-gray-700">Pantau pengajuan, review bukti, dan leaderboard OPD. Seru tapi serius.</p>
      <div class="mt-4 flex items-center justify-between">
        <button data-demo="inovasi" class="px-3 py-1.5 text-sm rounded-lg border border-maroon text-maroon hover:bg-maroon hover:text-white">Lihat Demo</button>
        <span class="text-xs text-gray-500">Terukur • Transparan</span>
      </div>
    </article>
  </div>
</section>

<!-- Timeline singkat & Counter -->
<section class="max-w-7xl mx-auto px-4 py-10" id="bagaimana">
  <div class="grid lg:grid-cols-[1.1fr,.9fr] gap-8">
    <div>
      <h2 class="text-2xl font-extrabold text-gray-900">Perjalanan Singkat</h2>
      <ol class="mt-4 relative border-s border-gray-200 ps-4 space-y-5">
        <li>
          <div class="absolute -start-1.5 mt-1.5 h-3 w-3 rounded-full bg-maroon"></div>
          <p class="text-sm font-semibold">2024 — Ide Muncul</p>
          <p class="text-sm text-gray-600">Masih idealis banget, apalagi masih CPNS</p>
        </li>
        <li>
          <div class="absolute -start-1.5 mt-1.5 h-3 w-3 rounded-full bg-maroon"></div>
          <p class="text-sm font-semibold">2024 — Eksekusi</p>
          <p class="text-sm text-gray-600">Baru masuk, langsung buat roadmap, ERD dll wkwk</p>
        </li>
        <li>
          <div class="absolute -start-1.5 mt-1.5 h-3 w-3 rounded-full bg-emerald-600 animate-pulse"></div>
          <p class="text-sm font-semibold">2026 — Full Launching</p>
          <p class="text-sm text-gray-600">Target Januari 2026 sudah full launching, doakan ya.</p>
        </li>
      </ol>
    </div>

  </div>
</section>


<!-- FAQ Ringkas -->
<section class="max-w-7xl mx-auto px-4 py-10">
  <h2 class="text-2xl font-extrabold text-gray-900">FAQ</h2>
  <div class="mt-4 grid md:grid-cols-2 gap-4">
    <details class="rounded-xl border border-gray-200 p-4">
      <summary class="font-semibold cursor-pointer">Apakah SIGAP bisa diakses publik?</summary>
      <p class="mt-2 text-sm text-gray-600">Halaman publik tersedia untuk dokumen non-privat. Data pegawai tetap terbatas & ber-<em>access code</em>.</p>
    </details>
    <details class="rounded-xl border border-gray-200 p-4">
      <summary class="font-semibold cursor-pointer">Apakah ada log siapa yang unduh?</summary>
      <p class="mt-2 text-sm text-gray-600">Ada. Tercatat waktu, pengguna, dan dokumen—demi akuntabilitas.</p>
    </details>
    <details class="rounded-xl border border-gray-200 p-4">
      <summary class="font-semibold cursor-pointer">Bagaimana jika menemukan bug?</summary>
      <p class="mt-2 text-sm text-gray-600">Laporkan via WhatsApp 0851-7323-1604. Terima kasih sudah bantu! 🙏</p>
    </details>
    <details class="rounded-xl border border-gray-200 p-4">
      <summary class="font-semibold cursor-pointer">Apakah mendukung review evidensi inovasi?</summary>
      <p class="mt-2 text-sm text-gray-600">Ya, termasuk form evidensi & rencana AI-assisted review.</p>
    </details>
  </div>
</section>

<!-- CTA -->
<section class="max-w-7xl mx-auto px-4 pb-14">
  <div class="rounded-2xl border-2 border-dashed border-maroon/30 p-6 sm:p-8 text-center">
    <h3 class="text-xl sm:text-2xl font-extrabold text-gray-900">Siap jadi lebih SIGAP?</h3>
    <p class="text-gray-600 mt-1">Mulai dari kebiasaan kecil: unggah dokumen final, kasih metadata rapi, dan pakai kode akses.</p>
    <div class="mt-4 flex items-center justify-center gap-3">
      @guest
      <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-maroon text-white font-semibold hover:bg-maroon-800">Masuk</a>
      @endguest
      <button id="btn-copy-mail" class="px-4 py-2 rounded-lg border border-maroon text-maroon font-semibold hover:bg-maroon hover:text-white">Salin Email Admin</button>
    </div>
    <p id="copy-status" class="text-xs text-gray-500 mt-2"></p>
  </div>
</section>

@endsection

@push('scripts')
<script>
  // Reveal SIGAP acronym
  document.getElementById('btn-acronym')?.addEventListener('click', ()=> {
    const el = document.getElementById('acronym');
    if (!el) return;
    el.classList.toggle('hidden');
  });

  // Easter egg with SweetAlert2 (already loaded by layout)
  document.getElementById('btn-easter')?.addEventListener('click', ()=> {
    Swal.fire({
      title: 'SURPRISE 🎉',
      html: '<div style="font-size:14px;color:#374151">Terima kasih sudah pakai SIGAP. Bonus pantun:<br><br><em>Dua tiga simpan arsip,</em><br><em>Dokumen rapi hati pun mantap.</em><br><em>Kalau butuh akses cepat,</em><br><em>Ingat SIGAP, selalu sigap!</em><br>Apakah :")</div>',
      confirmButtonText: 'Mantap!',
      confirmButtonColor: '#7a2222',
      backdrop: true
    });
  });

  // Module demo buttons -> playful info
  document.querySelectorAll('[data-demo]')?.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const type = btn.getAttribute('data-demo');
      const info = {
        dokumen: 'Cari & unduh dokumen final lengkap dengan metadata dan versi.',
        pegawai: 'Akses dokumen privat pegawai dengan kode & history akses.',
        inovasi: 'Kelola pengajuan, evidensi, dan leaderboard OPD.'
      }[type] || 'Demo modul';
      Swal.fire({title: `Demo ${type.toUpperCase()}`, text: info, confirmButtonColor:'#7a2222'});
    });
  });

  // Counters
  const counters = document.querySelectorAll('[data-counter]');
  const animateCount = (el, target)=>{
    let cur=0, step=Math.max(1, Math.floor(target/60));
    const tick=()=>{cur+=step; if(cur>=target){cur=target; el.textContent=cur; return;}
      el.textContent=cur; requestAnimationFrame(tick);}
    tick();
  };
  const startCounters = ()=>{
    counters.forEach(el=> animateCount(el, parseInt(el.getAttribute('data-counter')||'0',10)));
  };
  // Start on visible
  let started=false;
  const onScroll=()=>{
    if(started) return;
    const rect = document.body.getBoundingClientRect();
    // start once user scrolled a bit
    if (window.scrollY > 150 || rect.top < -150) { started=true; startCounters(); window.removeEventListener('scroll', onScroll); }
  };
  window.addEventListener('scroll', onScroll);
  // also try after load
  setTimeout(onScroll, 800);

  // Quiz
  const btnStart = document.getElementById('btn-start-quiz');
  const quizArea = document.getElementById('quiz-area');
  const quizForm = document.getElementById('quiz-form');
  btnStart?.addEventListener('click', ()=>{
    quizArea.classList.remove('hidden');
    btnStart.disabled = true;
    btnStart.classList.add('opacity-60','cursor-not-allowed');
  });
  quizForm?.addEventListener('submit',(e)=>{
    e.preventDefault();
    const data = new FormData(quizForm);
    const score = ['q1','q2','q3'].reduce((s,k)=> s + (data.get(k)==='b'?1:0),0);
    const msg = score===3 ? 'Master Arsip! 🏆' : score===2 ? 'Nyaris! 👍' : 'Semangat, belajar lagi! 💪';
    Swal.fire({title: `Nilai: ${score}/3`, text: msg, confirmButtonColor:'#7a2222'});
  });

  // Copy email
  document.getElementById('btn-copy-mail')?.addEventListener('click', async ()=>{
    try {
      await navigator.clipboard.writeText('email@brida.makassar.go.id');
      const s = document.getElementById('copy-status');
      s.textContent = 'Email admin tersalin ✔';
      setTimeout(()=> s.textContent='', 2000);
    } catch(e) {
      alert('Gagal menyalin email.');
    }
  });
</script>
@endpush
@push('scripts')
<script>
  // Kudos counter
  document.querySelectorAll('.dev-kudos').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.getAttribute('data-target');
      const box = document.querySelector(id);
      if(!box) return;
      const strong = box.querySelector('strong');
      const cur = parseInt(strong.textContent || '0', 10) + 1;
      strong.textContent = cur;
      Swal.fire({
        title: 'Terima kasih! 🙌',
        text: `Kudos terkirim. Total: ${cur}`,
        confirmButtonColor: '#7a2222',
        timer: 1300,
        showConfirmButton: false
      });
    });
  });

  // Fun facts toggle
  document.querySelectorAll('.dev-fun').forEach(btn=>{
    btn.addEventListener('click', ()=>{
      const id = btn.getAttribute('data-target');
      const el = document.querySelector(id);
      if(!el) return;
      el.classList.toggle('hidden');
      if(!el.classList.contains('hidden')) {
        el.classList.add('animate-[floaty_3s_ease-in-out_infinite]');
      } else {
        el.classList.remove('animate-[floaty_3s_ease-in-out_infinite]');
      }
    });
  });
</script>
@endpush
