<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $kumpulan->judul_kumpulan }} — SIGAP BRIDA</title>

  <!-- Tailwind CSS & Font Inter -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: Inter, system-ui, sans-serif; }
    [x-cloak] { display: none !important; }
    .scrollbar-thin::-webkit-scrollbar { width: 4px; height: 4px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen p-4 sm:p-6" x-data="kumpulanViewer()">

  <div class="max-w-5xl mx-auto space-y-6">

    {{-- Top Bar Logo --}}
    <div class="flex items-center justify-between border-b pb-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
      <div class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-[#7a2222] text-white font-extrabold text-sm">SB</span>
        <div>
          <h1 class="text-sm font-bold text-gray-900 leading-tight">SIGAP BRIDA Kota Makassar</h1>
          <p class="text-xs text-gray-500">Rekapitulasi Evidence SKP & PPD</p>
        </div>
      </div>
      <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
        ✅ Terverifikasi
      </span>
    </div>

    {{-- Info Rekap Utama --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm space-y-3">
      <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
        {{ $kumpulan->kategori }}
      </span>
      <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">
        {{ $kumpulan->judul_kumpulan }}
      </h2>
      <div class="text-xs text-gray-500 border-t pt-3 flex flex-wrap gap-4">
        <p>👤 Pegawai: <span class="font-bold text-gray-800">{{ $kumpulan->user->name ?? '-' }}</span></p>
        <p>📅 Periode: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($kumpulan->bulan_tahun . '-01')->translatedFormat('F Y') }}</span></p>
      </div>
    </div>

    {{-- BLOK 1: LAPORAN SKP (Foto & PDF) --}}
    @if(count($skpList) > 0)
    <div class="space-y-4">
      <h3 class="text-sm font-bold text-gray-900 bg-gray-200 px-3.5 py-1.5 rounded-xl inline-block">
        📁 Evidence Laporan SKP ({{ count($skpList) }} Kegiatan)
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($skpList as $index => $skp)
          @php
            $isPdf = $skp->tipe_evidence === 'pdf';
            $allFotoUrls = $skp->fotos->map(fn($f) => asset('storage/' . $f->file_path))->values()->toArray();
            $coverFotoUrl = count($allFotoUrls) > 0 ? $allFotoUrls[0] : null;
            $pdfUrl = $isPdf && $skp->file_pdf_path ? asset('storage/' . $skp->file_pdf_path) : null;
          @endphp

          <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
            
            <div>
              {{-- AREA MEDIA THUMBNAIL (BISA DIKLIK BUKA POPUP) --}}
              <div class="h-48 w-full bg-gray-100 relative overflow-hidden flex items-center justify-center border-b cursor-pointer group"
                   @click="{{ $isPdf ? 'openPdfModal(\''.addslashes($skp->judul_kegiatan).'\', \''.$pdfUrl.'\')' : 'openPhotoListModal(\''.addslashes($skp->judul_kegiatan).'\', '.json_encode($allFotoUrls).')' }}">
                
                @if($isPdf)
                  {{-- Tampilan PDF --}}
                  <div class="flex flex-col items-center gap-1.5 text-red-600 p-4 text-center group-hover:scale-105 transition-transform">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider bg-red-100 text-red-800 px-2.5 py-0.5 rounded-md border border-red-200">
                      📄 Klik Buka PDF
                    </span>
                  </div>
                @elseif($coverFotoUrl)
                  {{-- Tampilan Foto --}}
                  <img src="{{ $coverFotoUrl }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                  
                  @if(count($allFotoUrls) > 1)
                    <span class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-lg backdrop-blur">
                      📷 +{{ count($allFotoUrls) }} Foto
                    </span>
                  @endif
                @else
                  <span class="text-xs text-gray-400 italic">Tidak ada dokumentasi</span>
                @endif

                <span class="absolute top-2 left-2 bg-black/60 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-md backdrop-blur">
                  #{{ $index + 1 }}
                </span>

                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                  <span class="bg-white/90 text-gray-800 px-3 py-1.5 rounded-xl text-xs font-bold shadow-lg">🔍 Perbesar</span>
                </div>
              </div>

              {{-- Detail Judul & Deskripsi --}}
              <div class="p-4 space-y-1.5">
                <span class="text-[10px] font-bold text-gray-400">📅 {{ \Carbon\Carbon::parse($skp->tanggal)->translatedFormat('d F Y') }}</span>
                <h4 class="font-bold text-sm text-gray-900 leading-snug">{{ $skp->judul_kegiatan }}</h4>
                @if($skp->deskripsi)
                  <p class="text-xs text-gray-500 line-clamp-3 mt-1 bg-gray-50 p-2 rounded-lg border border-gray-100">
                    {{ $skp->deskripsi }}
                  </p>
                @endif
              </div>
            </div>

            {{-- TOMBOL LIHAT SEMUA --}}
            <div class="p-3 border-t bg-gray-50">
              @if($isPdf && $pdfUrl)
                <button type="button" 
                        @click="openPdfModal('{{ addslashes($skp->judul_kegiatan) }}', '{{ $pdfUrl }}')"
                        class="w-full py-2 px-3 rounded-xl bg-red-700 hover:bg-red-800 text-white text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                  Buka Dokumen PDF
                </button>
              @else
                <button type="button" 
                        @click="openPhotoListModal('{{ addslashes($skp->judul_kegiatan) }}', {{ json_encode($allFotoUrls) }})"
                        class="w-full py-2 px-3 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold transition-colors flex items-center justify-center gap-1.5 shadow-sm">
                  🖼️ Lihat Semua Foto ({{ count($allFotoUrls) }})
                </button>
              @endif
            </div>

          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- BLOK 2: PERJALANAN DINAS (PPD) --}}
    @if(count($ppdList) > 0)
    <div class="space-y-4 mt-8">
      <h3 class="text-sm font-bold text-gray-900 bg-blue-100 text-blue-800 px-3.5 py-1.5 rounded-xl inline-block border border-blue-200">
        ✈️ Evidence Perjalanan Dinas / PPD ({{ count($ppdList) }} Kegiatan)
      </h3>
      
      <div class="space-y-6">
        @foreach($ppdList as $ppd)
          <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4">
            <div class="border-b pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <div>
                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase">
                  {{ $ppd->kategori }}
                </span>
                <h4 class="font-bold text-base text-gray-900 mt-1">{{ $ppd->judul }}</h4>
                <p class="text-xs text-gray-500 mt-0.5">📍 {{ $ppd->tempat }} | 📅 {{ $ppd->hari_tanggal }}</p>
              </div>
            </div>

            <div class="space-y-4">
              @foreach($ppd->lembar as $lembar)
                @php
                  $ppdFotos = $lembar->fotos->map(fn($f) => asset('storage/' . $f->foto_path))->values()->toArray();
                @endphp
                <div class="bg-gray-50 rounded-xl p-3.5 border border-gray-200">
                  <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-bold text-gray-800">
                      Lembar {{ $lembar->lembar_ke }} : <span class="font-normal text-gray-600">{{ $lembar->deskripsi ?? 'Tanpa deskripsi' }}</span>
                    </p>
                    <button type="button" 
                            @click="openPhotoListModal('{{ addslashes($ppd->judul) }} — Lembar {{ $lembar->lembar_ke }}', {{ json_encode($ppdFotos) }})" 
                            class="text-[11px] font-bold text-blue-600 hover:underline">
                      Lihat Semua ({{ count($ppdFotos) }})
                    </button>
                  </div>
                  
                  <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-2">
                    @foreach($lembar->fotos as $fotoPpd)
                      @php $srcFoto = asset('storage/' . $fotoPpd->foto_path); @endphp
                      <div class="aspect-square rounded-lg border border-gray-200 overflow-hidden bg-white shadow-sm cursor-pointer hover:opacity-80 transition-opacity"
                           @click="openSinglePhotoModal('{{ addslashes($ppd->judul) }}', '{{ $srcFoto }}')">
                        <img src="{{ $srcFoto }}" class="w-full h-full object-cover">
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Footer Info --}}
    <div class="text-center pt-6 text-xs text-gray-400 border-t">
      SIGAP BRIDA Kota Makassar — Sistem Informasi Pertanggungjawaban & Evidence Kegiatan
    </div>

  </div>

  {{-- ================= MODAL LIGHTBOX FOTO & DOKUMEN ================= --}}

  {{-- 1. MODAL GALERI FOTO (Melihat Seluruh Foto Suatu Kegiatan) --}}
  <div x-show="showPhotoModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" @click="closeModals()"></div>

    <div class="flex min-h-screen items-center justify-center p-4">
      <div class="relative w-full max-w-4xl rounded-2xl bg-gray-900 border border-gray-800 p-4 sm:p-6 text-white shadow-2xl space-y-4">
        
        <div class="flex items-center justify-between border-b border-gray-800 pb-3">
          <h3 class="text-sm sm:text-base font-bold text-white truncate max-w-[85%]" x-text="modalTitle"></h3>
          <button type="button" @click="closeModals()" class="text-gray-400 hover:text-white text-lg font-bold">✕</button>
        </div>

        {{-- Foto Besar Aktif --}}
        <div class="relative w-full aspect-[4/3] sm:aspect-video bg-black rounded-xl overflow-hidden flex items-center justify-center border border-gray-800">
          <template x-if="modalPhotos.length > 0">
            <img :src="modalPhotos[activePhotoIndex]" class="w-full h-full object-contain">
          </template>
          <template x-if="modalPhotos.length === 0">
            <p class="text-xs text-gray-500">Tidak ada foto dokumentasi.</p>
          </template>
        </div>

        {{-- Baris Thumbnail Foto yang Ada --}}
        <div x-show="modalPhotos.length > 1" class="flex gap-2 overflow-x-auto pb-2 scrollbar-thin">
          <template x-for="(fotoUrl, idx) in modalPhotos" :key="idx">
            <div class="relative shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 cursor-pointer transition-all"
                 :class="activePhotoIndex === idx ? 'border-amber-400 ring-2 ring-amber-400/40' : 'border-gray-700 opacity-60 hover:opacity-100'"
                 @click="activePhotoIndex = idx">
              <img :src="fotoUrl" class="w-full h-full object-cover">
            </div>
          </template>
        </div>

        <div class="flex justify-between items-center text-xs text-gray-400 pt-2 border-t border-gray-800">
          <span>Foto <span x-text="activePhotoIndex + 1"></span> dari <span x-text="modalPhotos.length"></span></span>
          <a :href="modalPhotos[activePhotoIndex]" target="_blank" download class="text-amber-400 hover:underline font-bold">
            ⬇️ Download Foto Asli
          </a>
        </div>

      </div>
    </div>
  </div>

  {{-- 2. MODAL VIEWER PDF (Membuka PDF Langsung di Halaman Tanpa Download Dulu) --}}
  <div x-show="showPdfModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" @click="closeModals()"></div>

    <div class="flex min-h-screen items-center justify-center p-2 sm:p-4">
      <div class="relative w-full max-w-4xl h-[90vh] rounded-2xl bg-white p-4 flex flex-col justify-between shadow-2xl space-y-3">
        
        <div class="flex items-center justify-between border-b pb-2">
          <div class="flex items-center gap-2 truncate max-w-[85%]">
            <span class="p-1.5 bg-red-100 text-red-700 rounded-lg text-sm">📄</span>
            <h3 class="text-sm font-bold text-gray-900 truncate" x-text="modalTitle"></h3>
          </div>
          <button type="button" @click="closeModals()" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
        </div>

        {{-- Frame PDF --}}
        <div class="flex-1 w-full bg-gray-100 rounded-xl overflow-hidden border border-gray-200">
          <iframe :src="modalPdfUrl" class="w-full h-full" frameborder="0"></iframe>
        </div>

        <div class="flex justify-end gap-2 pt-2 border-t">
          <a :href="modalPdfUrl" target="_blank" download 
             class="px-4 py-2 bg-red-700 hover:bg-red-800 text-white font-bold text-xs rounded-xl shadow-sm flex items-center gap-1.5">
            📥 Download Berkas PDF
          </a>
          <button type="button" @click="closeModals()" class="px-4 py-2 border rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-50">
            Tutup
          </button>
        </div>

      </div>
    </div>
  </div>

<script>
function kumpulanViewer() {
  return {
    showPhotoModal: false,
    showPdfModal: false,
    modalTitle: '',
    modalPhotos: [],
    activePhotoIndex: 0,
    modalPdfUrl: '',

    openPhotoListModal(title, photos) {
      if (!photos || photos.length === 0) return;
      this.modalTitle = title;
      this.modalPhotos = photos;
      this.activePhotoIndex = 0;
      this.showPhotoModal = true;
    },

    openSinglePhotoModal(title, photoUrl) {
      this.modalTitle = title;
      this.modalPhotos = [photoUrl];
      this.activePhotoIndex = 0;
      this.showPhotoModal = true;
    },

    openPdfModal(title, pdfUrl) {
      this.modalTitle = title;
      this.modalPdfUrl = pdfUrl;
      this.showPdfModal = true;
    },

    closeModals() {
      this.showPhotoModal = false;
      this.showPdfModal = false;
      this.modalPhotos = [];
      this.modalPdfUrl = '';
    }
  }
}
</script>

</body>
</html>