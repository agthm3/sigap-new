@extends('layouts.page')
@section('content')

<!-- HERO — naratif, tidak ada form pencarian -->
<section class="relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900"></div>
  <div class="max-w-7xl mx-auto px-4 py-14 sm:py-16 lg:py-20">
    <div class="text-center max-w-3xl mx-auto">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-white text-xs mb-3">
        <span class="h-2 w-2 rounded-full bg-green-400"></span> Tahap Perancangan
        <span class="opacity-70">•</span> Fokus: Otomasi tugas repetitif
      </div>
      <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
        SIGAP <span class="text-white/90">AUTO</span>
      </h1>
      <p class="mt-3 text-white/80 text-sm sm:text-base">
        Biarkan sistem bekerja—pegawai cukup konfirmasi atau memantau hasilnya. Otomatiskan tugas kecil yang menyita waktu:
        dari validasi nama file hingga notifikasi lintas kanal.
      </p>

      <!-- tiga pilar -->
      <div class="mt-8 grid sm:grid-cols-3 gap-4">
        <div class="rounded-xl bg-white/95 shadow-md p-5">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" stroke-linecap="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
          </div>
          <h3 class="mt-3 font-semibold text-gray-900">Trigger</h3>
          <p class="text-sm text-gray-600">Peristiwa pemicu: unggah file, form masuk, jadwal, atau pesan dari kanal tertentu.</p>
        </div>
        <div class="rounded-xl bg-white/95 shadow-md p-5">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" stroke-linecap="round" d="M9 12h6M9 16h6M5 8h14"/>
            </svg>
          </div>
          <h3 class="mt-3 font-semibold text-gray-900">Action</h3>
          <p class="text-sm text-gray-600">Langkah otomatis: validasi, rename, konversi, kirim notifikasi, tulis log.</p>
        </div>
        <div class="rounded-xl bg-white/95 shadow-md p-5">
          <div class="w-10 h-10 rounded-lg bg-maroon/10 text-maroon flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="2" stroke-linecap="round" d="M3 7h18M3 12h18M7 17h10"/>
            </svg>
          </div>
          <h3 class="mt-3 font-semibold text-gray-900">Monitor</h3>
          <p class="text-sm text-gray-600">Panel ringkas untuk melihat status terakhir, error, dan riwayat eksekusi.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CONTOH OTOMASI — khusus 3 skenario yang diminta -->
<section class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center max-w-2xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Contoh Otomasi yang Relevan</h2>
      <p class="mt-2 text-gray-600 text-sm">Tiga skenario prioritas ide untuk dieksplor saat tahap perancangan.</p>
    </div>

    <div class="mt-8 grid md:grid-cols-3 gap-6">
      <!-- 1. Pengisian otomatis Coretax -->
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
          Integrasi Sistem
        </div>
        <h3 class="mt-3 font-semibold text-gray-900">Pengisian Otomatis Coretax</h3>
        <p class="mt-1 text-sm text-gray-600">
          Mengambil data sumber (mis: rekap gaji/TPP, honor, atau CSV/Excel) → memetakan ke kolom yang dibutuhkan Coretax →
          menyiapkan draft input secara otomatis untuk dikonfirmasi petugas.
        </p>
        <ul class="mt-3 text-xs text-gray-600 space-y-1 list-disc list-inside">
          <li><strong>Trigger:</strong> unggah file rekap / jadwal bulanan</li>
          <li><strong>Action:</strong> validasi format → mapping kolom → hasilkan pre-fill</li>
          <li><strong>Monitor:</strong> log sukses/error & file hasil</li>
        </ul>
        <div class="mt-3 rounded-lg bg-gray-50 border border-gray-200 p-3 text-[12px] text-gray-700">
          <strong>Catatan:</strong> kredensial tidak disimpan di SIGAP. Eksekusi pakai akun layanan/SSO resmi atau ekspor
          berkas siap-unggah (sandbox dulu, uji pada data dummy).
        </div>
      </div>

      <!-- 2. Validasi dokumen otomatis menggunakan AI -->
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded-full bg-purple-50 text-purple-700 border border-purple-200">
          AI Review
        </div>
        <h3 class="mt-3 font-semibold text-gray-900">Validasi Dokumen Otomatis (AI)</h3>
        <p class="mt-1 text-sm text-gray-600">
          AI memeriksa kelengkapan & konsistensi dokumen (PDF/DOC): deteksi nomor/ tanggal, pihak terkait, masa berlaku,
          tanda tangan/stempel, hingga potensi PII—lalu memberi skor & saran perbaikan.
        </p>
        <ul class="mt-3 text-xs text-gray-600 space-y-1 list-disc list-inside">
          <li><strong>Trigger:</strong> dokumen baru diunggah/diubah</li>
          <li><strong>Action:</strong> ekstraksi metadata → cek kelengkapan → skor & rekomendasi</li>
          <li><strong>Monitor:</strong> panel temuan, tombol “Kirim ke Verifikator”</li>
        </ul>
        <div class="mt-3 rounded-lg bg-gray-50 border border-gray-200 p-3 text-[12px] text-gray-700">
          <strong>Catatan:</strong> AI bersifat asisten—keputusan akhir tetap pada verifikator. Data sensitif diproses sesuai
          kebijakan privasi & perizinan internal.
        </div>
      </div>

      <!-- 3. Pengisian otomatis RENJA/RENSTRA -->
      <div class="p-6 rounded-xl border border-gray-200 hover:shadow-lg transition">
        <div class="inline-flex items-center gap-2 text-xs px-2 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
          Dokumen Perencanaan
        </div>
        <h3 class="mt-3 font-semibold text-gray-900">Auto-Isi Dokumen RENJA, RENSTRA, dll.</h3>
        <p class="mt-1 text-sm text-gray-600">
          Menggabungkan data master (program, indikator, pagu, realisasi, OPD, sumber pendanaan) ke template resmi RENJA/RENSTRA
          untuk menghasilkan draf dokumen (DOCX/PDF) yang siap ditinjau.
        </p>
        <ul class="mt-3 text-xs text-gray-600 space-y-1 list-disc list-inside">
          <li><strong>Trigger:</strong> pilih tahun/periode & template</li>
          <li><strong>Action:</strong> tarik data → isi tabel/rumus → buat draf</li>
          <li><strong>Monitor:</strong> versi draf, perubahan, & riwayat unduh</li>
        </ul>
        <div class="mt-3 rounded-lg bg-gray-50 border border-gray-200 p-3 text-[12px] text-gray-700">
          <strong>Catatan:</strong> tetap menyisakan kolom manual (narasi, analisis) agar fleksibel. Versi draf diberi watermark.
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STATUS PRIORITAS PENGEMBANGAN -->
<section class="py-14 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center max-w-3xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Status Prioritas Pengembangan</h2>
      <p class="mt-2 text-gray-600 text-sm">
        Agar stabil dan minim error, tim developer memfokuskan kapasitas pada modul inti terlebih dahulu.
      </p>
    </div>

    <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      <!-- Prioritas Tinggi -->
      <div class="p-5 rounded-xl border border-green-200 bg-green-50">
        <div class="text-xs inline-flex items-center gap-2 px-2 py-1 rounded-full bg-white text-green-700 border border-green-200">
          <span class="h-2 w-2 rounded-full bg-green-500"></span> Prioritas Tinggi — Fokus Saat Ini
        </div>
        <ul class="mt-3 text-sm text-gray-700 space-y-1 list-disc list-inside">
          <li>SIGAP Dokumen (stabilitas search, preview, versi)</li>
          <li>SIGAP Pegawai (kontrol akses & log sensitif)</li>
          <li>SIGAP Inovasi (dashboard, leaderboard OPD)</li>
          <li>SIGAP Riset (index publik & preview)</li>
          <li>SIGAP Kinerja / Format (katalog & template)</li>
        </ul>
      </div>

      <!-- Prioritas Rendah -->
      <div class="p-5 rounded-xl border border-gray-200">
        <div class="text-xs inline-flex items-center gap-2 px-2 py-1 rounded-full bg-gray-100 text-gray-700 border border-gray-200">
          <span class="h-2 w-2 rounded-full bg-gray-400"></span> Prioritas Rendah — Belum Mendesak
        </div>
        <ul class="mt-3 text-sm text-gray-700 space-y-1 list-disc list-inside">
          <li><strong>SIGAP Auto</strong> — konsep & UI eksploratif, implementasi bertahap</li>
          <li><strong>SIGAP SKPRD</strong> — menunggu definisi kebutuhan & data kasus</li>
        </ul>
        <p class="mt-3 text-xs text-gray-600">
          Penjadwalan implementasi akan mengikuti kesiapan data & hasil uji modul inti, supaya kualitas tetap terjaga.
        </p>
      </div>

      <!-- Info Tambahan -->
      <div class="p-5 rounded-xl border border-blue-200 bg-blue-50">
        <div class="text-xs inline-flex items-center gap-2 px-2 py-1 rounded-full bg-white text-blue-700 border border-blue-200">
          <span class="h-2 w-2 rounded-full bg-blue-500"></span> Kenapa demikian?
        </div>
        <p class="mt-3 text-sm text-gray-700">
          Modul inti langsung dipakai banyak pegawai & publik, sehingga stabilitas, keamanan, dan konsistensi data jadi
          pertimbangan utama sebelum memperluas ke otomasi & dashboard kebijakan.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- SIMULASI ALUR — stepper horizontal -->
<section class="py-14 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Simulasi Alur</h2>
        <p class="text-sm text-gray-600 mt-1">Contoh: “Unggah Dokumen Fix → Notifikasi Pihak Terkait”.</p>
      </div>
      <div class="text-xs text-gray-500">Semua contoh bersifat dummy (Coming Soon)</div>
    </div>

    <div class="mt-6 grid lg:grid-cols-4 gap-4">
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">Langkah 1 — Trigger</p>
        <h4 class="mt-1 font-semibold">Unggah “SK Sekretariat A”</h4>
        <p class="text-sm text-gray-600 mt-1">File masuk ke antrean otomatis.</p>
      </div>
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">Langkah 2 — Validasi</p>
        <h4 class="mt-1 font-semibold">Cek Pola Nama + Metadata</h4>
        <p class="text-sm text-gray-600 mt-1">Perbaiki nama, isi alias & pihak terkait.</p>
      </div>
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">Langkah 3 — Action</p>
        <h4 class="mt-1 font-semibold">Kirim Notifikasi</h4>
        <p class="text-sm text-gray-600 mt-1">WA/Email ke stakeholder berisi tautan dokumen.</p>
      </div>
      <div class="rounded-xl bg-white border border-gray-200 p-5">
        <p class="text-xs font-semibold text-maroon">Langkah 4 — Monitor</p>
        <h4 class="mt-1 font-semibold">Catat Log</h4>
        <p class="text-sm text-gray-600 mt-1">Siapa, kapan, dan hasil eksekusi disimpan.</p>
      </div>
    </div>

    <!-- Mini riwayat -->
    <div class="mt-6 rounded-xl border border-gray-200 bg-white overflow-hidden">
      <div class="px-4 py-2.5 bg-gray-50 text-xs font-semibold text-gray-600">Riwayat Eksekusi (contoh)</div>
      <ul class="divide-y text-xs">
        <li class="px-4 py-2 flex items-center justify-between">
          <span>✔️ Notifikasi terkirim ke Bidang X (email)</span><span class="text-gray-500">30 Sep 2025 • 10:22</span>
        </li>
        <li class="px-4 py-2 flex items-center justify-between">
          <span>✔️ Metadata diperbarui: alias=“SK-SekA-2025-v1”</span><span class="text-gray-500">30 Sep 2025 • 10:20</span>
        </li>
        <li class="px-4 py-2 flex items-center justify-between">
          <span>⏳ Menunggu unggah file berikutnya</span><span class="text-gray-500">—</span>
        </li>
      </ul>
    </div>
  </div>
</section>

<!-- MINI BUILDER — UI dummy untuk “rancang otomasi” -->
<section class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="text-center max-w-2xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Rancang Otomasi (Preview)</h2>
      <p class="mt-2 text-gray-600 text-sm">Belum terhubung backend—sekadar pratinjau pengalaman pengguna.</p>
    </div>

    <div class="mt-8 grid lg:grid-cols-3 gap-6">
      <!-- Trigger -->
      <div class="rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase">Pilih Trigger</p>
        <div class="mt-3 space-y-2">
          <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="trigger" class="text-maroon" checked>
            Unggah file ke “SIGAP Dokumen”
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="trigger" class="text-maroon">
            Pesan masuk di Channel Telegram
          </label>
          <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="trigger" class="text-maroon">
            Jadwal (harian/mingguan)
          </label>
        </div>
      </div>

      <!-- Action -->
      <div class="rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase">Tambah Action</p>
        <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Validasi nama</button>
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Rename file</button>
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Kirim WA</button>
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Kirim Email</button>
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Konversi PDF</button>
          <button class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Tulis Log</button>
        </div>
      </div>

      <!-- Preview -->
      <div class="rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase">Preview Workflow</p>
        <ol class="mt-3 space-y-2 text-sm text-gray-700">
          <li>1) Trigger: Unggah file ke “SIGAP Dokumen”</li>
          <li>2) Action: Validasi → Rename → Konversi PDF</li>
          <li>3) Action: Kirim WA + Email</li>
          <li>4) Action: Tulis Log Eksekusi</li>
        </ol>
        <button class="mt-4 w-full px-4 py-2.5 rounded-lg bg-maroon text-white hover:bg-maroon-800">
          Simpan (Coming Soon)
        </button>
      </div>
    </div>
  </div>
</section>

<!-- FAQ — details/summary tanpa JS -->
<section class="py-14 bg-gray-50">
  <div class="max-w-5xl mx-auto px-4">
    <div class="text-center max-w-2xl mx-auto">
      <h2 class="text-2xl sm:text-3xl font-extrabold text-maroon">Pertanyaan Umum</h2>
      <p class="mt-2 text-gray-600 text-sm">Hal yang sering ditanyakan sebelum implementasi otomasi.</p>
    </div>

    <div class="mt-8 space-y-3">
      <details class="group rounded-xl bg-white border border-gray-200 p-4">
        <summary class="flex cursor-pointer items-center justify-between">
          <span class="font-semibold text-gray-900">Apakah SIGAP Auto menggantikan pekerjaan pegawai?</span>
          <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="mt-3 text-sm text-gray-600">Tidak. Fokusnya mengurangi tugas repetitif agar pegawai fokus pada penilaian & pengambilan keputusan.</p>
      </details>
      <details class="group rounded-xl bg-white border border-gray-200 p-4">
        <summary class="flex cursor-pointer items-center justify-between">
          <span class="font-semibold text-gray-900">Bagaimana dengan keamanan & log akses?</span>
          <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="mt-3 text-sm text-gray-600">Setiap eksekusi menulis jejak (siapa/kapan/apa). Untuk data sensitif, tetap melalui persetujuan & kode akses.</p>
      </details>
      <details class="group rounded-xl bg-white border border-gray-200 p-4">
        <summary class="flex cursor-pointer items-center justify-between">
          <span class="font-semibold text-gray-900">Apakah butuh aplikasi tambahan?</span>
          <span class="text-gray-400 group-open:rotate-180 transition">⌄</span>
        </summary>
        <p class="mt-3 text-sm text-gray-600">Integrasi bisa bertahap: mulai dari modul internal SIGAP, lalu sambungkan ke WA/Email/Telegram sesuai kebutuhan.</p>
      </details>
    </div>
  </div>
</section>

<!-- CTA — ajukan ide otomasi -->
<section class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4">
    <div class="rounded-2xl overflow-hidden">
      <div class="bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900 px-6 sm:px-10 py-10 sm:py-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
          <h3 class="text-white text-2xl sm:text-3xl font-extrabold">Punya tugas repetitif yang mau di-AUTO?</h3>
          <p class="text-white/80 mt-1 text-sm">Kirimkan usulan alur—kami bantu susun workflow & prioritasnya.</p>
        </div>
        <div class="flex gap-3">
          <a href="#" class="px-5 py-2.5 rounded-lg bg-white text-maroon font-semibold hover:bg-white/90">Ajukan Ide (Coming Soon)</a>
          <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-lg bg-maroon-700/40 text-white border border-white/30 hover:bg-maroon-700/60">Masuk</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
