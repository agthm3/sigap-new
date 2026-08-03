@extends('layouts.app')

@push('head')
<style>
  .char-correct {
    color: #059669;
    background-color: #d1fae5;
  }
  .char-incorrect {
    color: #dc2626;
    background-color: #fee2e2;
    text-decoration: underline;
  }
  .char-current {
    background-color: #fef08a;
    border-bottom: 2px solid #ca8a04;
    animation: pulse-cursor 1s infinite;
  }
  @keyframes pulse-cursor {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
  }
</style>
@endpush

@section('content')
<!-- Header & Navigasi -->
<div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
      <a href="{{ route('magang.index') }}" class="hover:text-maroon">SIGAP Magang</a>
      <span>/</span>
      <a href="{{ route('magang.logbook.index') }}" class="hover:text-maroon">Logbook Saya</a>
      <span>/</span>
      <span class="text-gray-700 font-medium">Tes Ketik 10 Jari</span>
    </div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      MINI GAME <span class="text-maroon">KETIK 10 JARI</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Syarat Kelulusan Magang: Mencapai kecepatan minimal <strong>40 WPM (Words Per Minute)</strong> dalam Bahasa Indonesia.
    </p>
  </div>

  <a href="{{ route('magang.logbook.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-100 transition-colors">
    ← Kembali ke Logbook
  </a>
</div>

<!-- Main Game Card Container -->
<div class="mt-6 max-w-4xl mx-auto">
  
  <!-- Scoreboard & Timer -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
      <p class="text-xs font-semibold text-gray-500 uppercase">Sisa Waktu</p>
      <p id="timerDisplay" class="text-3xl font-extrabold text-maroon mt-1">60s</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
      <p class="text-xs font-semibold text-gray-500 uppercase">Kecepatan (WPM)</p>
      <p id="wpmDisplay" class="text-3xl font-extrabold text-gray-900 mt-1">0</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
      <p class="text-xs font-semibold text-gray-500 uppercase">Akurasi</p>
      <p id="accuracyDisplay" class="text-3xl font-extrabold text-emerald-600 mt-1">100%</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm text-center">
      <p class="text-xs font-semibold text-gray-500 uppercase">Target Minimal</p>
      <p class="text-3xl font-extrabold text-amber-600 mt-1">40 WPM</p>
    </div>
  </div>

  <!-- Area Permainan Ketik -->
  <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm space-y-6">
    
    <!-- Box Teks Soal -->
    <div class="p-5 bg-gray-50 rounded-xl border border-gray-200 font-mono text-base sm:text-lg leading-relaxed select-none max-h-48 overflow-y-auto" id="textDisplay">
      <!-- Huruf-huruf soal di-render via JavaScript -->
    </div>

    <!-- Input Pengatikan -->
    <div>
      <textarea id="typingInput" rows="3" disabled
                placeholder="Klik tombol 'Mulai Tes' di bawah untuk memulai giliran Anda..."
                class="w-full rounded-xl p-4 border border-gray-300 focus:border-maroon focus:ring-maroon font-mono text-base resize-none disabled:bg-gray-100 disabled:cursor-not-allowed transition-all"></textarea>
    </div>

    <!-- Action Buttons & Banner Hasil -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2 border-t">
      <button type="button" id="btnStart" onclick="startGame()"
              class="w-full sm:w-auto px-6 py-3 bg-maroon text-white font-bold text-sm rounded-xl hover:bg-maroon-800 shadow-md transition-colors">
        🚀 Mulai Tes Ketik (60 Detik)
      </button>

      <div id="resultBanner" class="hidden text-center sm:text-right">
        <span id="resultBadge" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold"></span>
      </div>
    </div>

  </div>

  <!-- Petunjuk Singkat -->
  <div class="mt-6 p-4 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs leading-relaxed space-y-1">
    <p class="font-bold">💡 Tips Mengetik 10 Jari:</p>
    <ul class="list-disc pl-4 space-y-0.5">
      <li>Posisikan telunjuk kiri di tombol <strong>F</strong> dan telunjuk kanan di tombol <strong>J</strong>.</li>
      <li>Fokus pada akurasi sebelum meningkatkan kecepatan ketik.</li>
      <li>Perhitungan WPM dihitung berdasarkan total karakter benar dibagi 5 dikurangi kesalahan.</li>
    </ul>
  </div>

</div>
@endsection

@push('scripts')
<script>
// Kumpulan Paragraf Soal Bahasa Indonesia (Realistis & Bertema Kantor/Umum)
const textParagraphs = [
  "Pengembangan sistem informasi terpadu merupakan salah satu pilar utama dalam meningkatkan efisiensi dan transparansi pelayanan publik di era digital saat ini. Setiap pegawai diharapkan mampu beradaptasi dengan kemajuan teknologi demi memberikan pelayanan terbaik bagi masyarakat.",
  "Inovasi tata kelola pemerintahan berbasis elektronik bertujuan untuk mempercepat proses administrasi, meminimalisir penggunaan kertas, serta memastikan arsip dokumen daerah tersimpan dengan aman dan mudah diakses oleh pihak yang berwenang.",
  "Kedisiplinan dan ketelitian dalam mencatat setiap kegiatan harian merupakan kunci sukses pelaksanaan program magang. Dengan membiasakan diri bekerja secara terstruktur, peserta magang dapat mengasah keterampilan profesional dan kesiapan kerja di masa depan.",
  "Kolaborasi antar instansi pemerintah daerah sangat penting dalam mendorong terciptanya kebijakan publik yang berdampak positif. Penelitian dan riset yang komprehensif menjadi landasan utama dalam penyusunan perencanaan pembangunan kota yang berkelanjutan."
];

let targetText = "";
let timer = 60;
let timerInterval = null;
let isPlaying = false;
let totalTypedChars = 0;
let correctCharsCount = 0;
let mistakesCount = 0;

const textDisplay = document.getElementById('textDisplay');
const typingInput = document.getElementById('typingInput');
const timerDisplay = document.getElementById('timerDisplay');
const wpmDisplay = document.getElementById('wpmDisplay');
const accuracyDisplay = document.getElementById('accuracyDisplay');
const btnStart = document.getElementById('btnStart');
const resultBanner = document.getElementById('resultBanner');
const resultBadge = document.getElementById('resultBadge');

function renderTextDisplay() {
  textDisplay.innerHTML = '';
  targetText.split('').forEach((char, index) => {
    const charSpan = document.createElement('span');
    charSpan.innerText = char;
    charSpan.id = `char_${index}`;
    textDisplay.appendChild(charSpan);
  });
  if (textDisplay.firstChild) {
    textDisplay.firstChild.classList.add('char-current');
  }
}

function startGame() {
  // Pilih paragraf acak
  targetText = textParagraphs[Math.floor(Math.random() * textParagraphs.length)];
  renderTextDisplay();

  // Reset State
  timer = 60;
  isPlaying = true;
  totalTypedChars = 0;
  correctCharsCount = 0;
  mistakesCount = 0;

  timerDisplay.innerText = '60s';
  wpmDisplay.innerText = '0';
  accuracyDisplay.innerText = '100%';
  resultBanner.classList.add('hidden');

  typingInput.value = '';
  typingInput.disabled = false;
  typingInput.focus();

  btnStart.innerText = '🔄 Ulangi Tes';

  if (timerInterval) clearInterval(timerInterval);

  timerInterval = setInterval(() => {
    timer--;
    timerDisplay.innerText = timer + 's';

    calculateLiveStats();

    if (timer <= 0) {
      endGame();
    }
  }, 1000);
}

typingInput.addEventListener('input', () => {
  if (!isPlaying) return;

  const typedText = typingInput.value;
  const typedChars = typedText.split('');
  const targetChars = targetText.split('');

  correctCharsCount = 0;
  mistakesCount = 0;

  targetChars.forEach((char, index) => {
    const charSpan = document.getElementById(`char_${index}`);
    if (!charSpan) return;

    const typedChar = typedChars[index];

    // Reset kelas
    charSpan.className = '';

    if (typedChar == null) {
      if (index === typedChars.length) {
        charSpan.classList.add('char-current');
      }
    } else if (typedChar === char) {
      charSpan.classList.add('char-correct');
      correctCharsCount++;
    } else {
      charSpan.classList.add('char-incorrect');
      mistakesCount++;
    }
  });

  totalTypedChars = typedText.length;
  calculateLiveStats();

  // Selesai jika semua teks berhasil diketik sebelum waktu habis
  if (typedText.length >= targetText.length) {
    endGame();
  }
});

function calculateLiveStats() {
  const timeElapsed = (60 - timer) || 1;
  // Rumus Net WPM: ((Karakter Benar / 5) / Menit Terpakai)
  const grossWPM = (correctCharsCount / 5) / (timeElapsed / 60);
  const netWPM = Math.max(0, Math.round(grossWPM));
  
  const accuracy = totalTypedChars > 0 ? Math.round((correctCharsCount / totalTypedChars) * 100) : 100;

  wpmDisplay.innerText = netWPM;
  accuracyDisplay.innerText = accuracy + '%';

  return { netWPM, accuracy };
}

function endGame() {
  clearInterval(timerInterval);
  isPlaying = false;
  typingInput.disabled = true;

  const { netWPM, accuracy } = calculateLiveStats();
  const isPassed = netWPM >= 40;

  resultBanner.classList.remove('hidden');

  if (isPassed) {
    resultBadge.className = "inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300";
    resultBadge.innerHTML = `🎉 LULUS! Hasil: ${netWPM} WPM (${accuracy}% Akurasi)`;
    
    // Simpan skor otomatis ke Database via AJAX
    saveScoreToDatabase(netWPM);
  } else {
    resultBadge.className = "inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300";
    resultBadge.innerHTML = `⚠️ BELUM LULUS! Hasil: ${netWPM} WPM (Target Min. 40 WPM)`;
    
    // Tetap simpan skor percobaan tertinggi
    saveScoreToDatabase(netWPM);
  }
}

function saveScoreToDatabase(wpmScore) {
  fetch("{{ route('magang.typing-game.save-score') }}", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-CSRF-TOKEN": "{{ csrf_token() }}"
    },
    body: JSON.stringify({ wpm: wpmScore })
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      if (data.passed) {
        Swal.fire({
          icon: 'success',
          title: 'Selamat! Anda Lulus Tes Ketik',
          text: `Kecepatan Anda ${wpmScore} WPM. Syarat Penutupan Magang sekarang telah terbuka!`,
          confirmButtonColor: '#7a2222',
          confirmButtonText: 'Kembali ke Logbook'
        }).then(() => {
          window.location.href = "{{ route('magang.logbook.index') }}";
        });
      }
    }
  })
  .catch(err => console.error("Gagal menyimpan skor:", err));
}
</script>
@endpush