<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
  <title>Form Kesediaan Narasumber</title>
  
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

  <!-- Library Client-Side Compression -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
  <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
  <script>
    if (typeof pdfjsLib !== 'undefined') {
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    }
  </script>

  <style>
    /* Cegah gesture scroll bawaan browser saat narasumber menandatangani kanvas di HP */
    #signature-pad {
      touch-action: none;
    }
    /* Cegah Safari iOS auto zoom pada form input */
    @media screen and (max-width: 768px) {
      input, textarea, select {
        font-size: 16px !important;
      }
    }
  </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased py-4 sm:py-8 px-3 sm:px-6">
<div class="max-w-2xl mx-auto">

  <!-- Header Branding -->
  <div class="text-center mb-5 sm:mb-8 px-2">
    <span class="inline-block px-3 py-1 bg-red-100 text-red-800 text-[11px] sm:text-xs font-bold rounded-full uppercase tracking-wider mb-2">
      SIGAP NARASUMBER
    </span>
    <h1 class="text-xl sm:text-2xl font-black text-slate-900 leading-tight">Form Kesediaan Narasumber</h1>
    <div class="mt-2 p-3 bg-white/70 backdrop-blur-sm rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-600 shadow-sm">
      <p class="font-semibold text-slate-800">{{ $kegiatan->nama_kegiatan }}</p>
      @if($kegiatan->hari_tanggal || $kegiatan->tempat)
        <p class="text-slate-500 mt-0.5 text-[11px] sm:text-xs">
          {{ $kegiatan->hari_tanggal }} {{ $kegiatan->tempat ? '• ' . $kegiatan->tempat : '' }}
        </p>
      @endif
    </div>
  </div>

  @if(session('success_name'))
    <div class="bg-white border-2 border-emerald-500/20 text-emerald-900 p-6 rounded-2xl mb-6 text-center shadow-sm">
      <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
      </div>
      <h2 class="font-extrabold text-lg sm:text-xl text-slate-900">Terima Kasih, {{ session('success_name') }}!</h2>
      <p class="text-xs sm:text-sm text-slate-600 mt-1">Data biodata dan surat kesediaan Anda telah berhasil terverifikasi dalam sistem.</p>
    </div>
  @else
    @if (isset($errors) && $errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-5 text-xs sm:text-sm">
        <strong class="font-bold flex items-center gap-1.5 mb-1 text-red-800">
          <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          Mohon periksa data berikut:
        </strong>
        <ul class="list-disc list-inside space-y-0.5 pl-1">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="narasumber-form" action="{{ route('sigap-narasumber.store-public', $kegiatan->uuid) }}" method="POST" enctype="multipart/form-data" class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-6 shadow-sm space-y-6">
      @csrf
      
      <!-- SEKSI 1: IDENTITAS UTAMA -->
      <div>
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2.5 mb-4">
          <div class="w-6 h-6 rounded-lg bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center">1</div>
          <h2 class="font-bold text-sm sm:text-base text-slate-900">Identitas & Kontak Dasar</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
              Nama Lengkap + Gelar <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" placeholder="Contoh: Dr. H. Fulan, M.Si" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition" required>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
              No. HP / WhatsApp <span class="text-red-500">*</span>
            </label>
            <input type="tel" name="no_hp" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx" inputmode="numeric" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition" required>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
              Alamat Email <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition" required>
          </div>

          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Alamat Kantor</label>
            <textarea name="alamat_kantor" rows="2" placeholder="Nama gedung, jalan, kota..." class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">{{ old('alamat_kantor') }}</textarea>
          </div>

          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Alamat Rumah</label>
            <textarea name="alamat_rumah" rows="2" placeholder="Alamat domisili narasumber..." class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">{{ old('alamat_rumah') }}</textarea>
          </div>

          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Materi yang akan dibawakan</label>
            <input type="text" name="materi" value="{{ old('materi') }}" placeholder="Judul / topik materi narasumber" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>
        </div>
      </div>

      <!-- SEKSI 2: KELENGKAPAN ADMINISTRASI -->
      <div>
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2.5 mb-4">
          <div class="w-6 h-6 rounded-lg bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center">2</div>
          <h2 class="font-bold text-sm sm:text-base text-slate-900">Biodata Lengkap (Administrasi)</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
              NIK (16 Digit) <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" pattern="\d{16}" inputmode="numeric" placeholder="7371xxxxxxxxxxxx" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition" required>
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">No. NPWP</label>
            <input type="text" name="npwp" value="{{ old('npwp') }}" placeholder="Contoh: 12.345.678.9-012.000" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">NIP (Jika ASN)</label>
            <input type="text" name="nip" value="{{ old('nip') }}" placeholder="19xxxxxxxxxxxxxxxx" inputmode="numeric" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm font-mono focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Tempat / Tgl Lahir</label>
            <input type="text" name="tempat_tanggal_lahir" value="{{ old('tempat_tanggal_lahir') }}" placeholder="Makassar, 01 Januari 1980" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Pangkat / Gol. Ruang</label>
            <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan') }}" placeholder="Contoh: Pembina / IV.a" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Jabatan</label>
            <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Kepala Pusat / Dosen" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Instansi / Unit Kerja</label>
            <input type="text" name="instansi_unit_kerja" value="{{ old('instansi_unit_kerja') }}" placeholder="Contoh: Universitas Hasanuddin" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Agama</label>
            <input type="text" name="agama" value="{{ old('agama') }}" placeholder="Islam / Kristen / dll" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Status Keluarga</label>
            <input type="text" name="status_keluarga" value="{{ old('status_keluarga') }}" placeholder="Kawin / Belum Kawin" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">Hobby</label>
            <input type="text" name="hobby" value="{{ old('hobby') }}" placeholder="Membaca, Olahraga, dll" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <div class="col-span-1 md:col-span-2">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">No. Rekening & Nama Bank</label>
            <input type="text" name="no_rekening" value="{{ old('no_rekening') }}" placeholder="Contoh: 123456789 - Bank Sulselbar a.n Fulan" class="w-full rounded-xl border-slate-300 border p-2.5 sm:p-2 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-600 transition">
          </div>

          <!-- UPLOAD SCAN / FOTO KTP -->
          <div class="col-span-1 md:col-span-2 mt-1">
            <label class="block text-xs sm:text-sm font-semibold text-slate-700 mb-1">
              Foto / Scan Berkas KTP <span class="text-red-500">*</span>
            </label>
            
            <div class="border-2 border-dashed border-slate-300 rounded-2xl p-3 sm:p-4 bg-slate-50/70 hover:bg-slate-50 transition">
              <input type="file" id="ktp_raw_input" accept="image/png, image/jpeg, image/jpg, application/pdf" 
                     class="block w-full text-xs sm:text-sm text-slate-500
                            file:mr-3 file:py-2 file:px-4 file:rounded-xl
                            file:border-0 file:text-xs file:font-semibold
                            file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer" required>
              
              <input type="file" name="file_ktp" id="ktp_compressed_input" class="hidden">

              <!-- Status Kompresi Mobile Responsive -->
              <div id="compression-status" class="hidden mt-3 p-3 rounded-xl border border-slate-200 bg-white shadow-sm text-xs">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                  <span id="compression-text" class="text-blue-600 font-semibold truncate">Memproses berkas...</span>
                  <span id="compression-size" class="text-slate-500 font-mono text-[11px]"></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 mt-2 overflow-hidden">
                  <div id="compression-bar" class="bg-red-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
              </div>

              <p class="text-[11px] text-slate-400 mt-2">Mendukung format JPG, PNG, atau PDF. Berkas otomatis dioptimasi di browser agar hemat kuota.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- SEKSI 3: TANDA TANGAN DIGITAL -->
      <div>
        <div class="flex items-center justify-between border-b border-slate-200 pb-2.5 mb-3">
          <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center">3</div>
            <h2 class="font-bold text-sm sm:text-base text-slate-900">Tanda Tangan Digital <span class="text-red-500">*</span></h2>
          </div>
          <button type="button" id="clear-signature" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-medium bg-white text-slate-600 hover:bg-slate-50 shadow-sm active:scale-95 transition">
            Reset TTD
          </button>
        </div>

        <p class="text-[11px] sm:text-xs text-slate-500 mb-2">Gunakan jari atau stylus Anda pada kotak putih di bawah ini:</p>
        
        <div class="w-full rounded-2xl border-2 border-dashed border-slate-300 p-1.5 bg-slate-50 shadow-inner">
          <div class="relative w-full h-44 sm:h-52 bg-white rounded-xl overflow-hidden">
            <canvas id="signature-pad" class="w-full h-full block cursor-crosshair"></canvas>
          </div>
        </div>
        <input type="hidden" id="ttd_data" name="ttd_data">
      </div>

      <!-- TOMBOL SUBMIT -->
      <div class="pt-2">
        <button type="submit" id="btn-submit" class="w-full py-3.5 px-4 rounded-xl bg-red-800 text-white font-bold text-sm sm:text-base hover:bg-red-900 active:scale-[0.99] transition shadow-md flex items-center justify-center gap-2">
          <span>Kirim Kesediaan Sekarang</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
          </svg>
        </button>
        <p class="text-[11px] text-center text-slate-400 mt-2">Data Anda terlindungi dan hanya dipergunakan untuk keperluan administrasi kegiatan.</p>
      </div>

    </form>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // === 1. TANDA TANGAN DIGITAL (RESPONSIF MOBILE DENGAN TOUCH SCALING) ===
  const canvas = document.getElementById('signature-pad');
  const ttdDataInput = document.getElementById('ttd_data');
  const form = document.getElementById('narasumber-form');
  let signaturePad = null;

  if (canvas) {
    signaturePad = new SignaturePad(canvas, { 
      backgroundColor: 'rgb(255, 255, 255)',
      penColor: 'rgb(15, 23, 42)',
      minWidth: 1.5,
      maxWidth: 3.5
    });

    function resizeCanvas() {
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      // Simpan data ttd sementara jika user me-rotate HP
      const data = signaturePad.toData();
      
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext('2d').scale(ratio, ratio);
      
      signaturePad.clear();
      if (data && data.length > 0) {
        signaturePad.fromData(data);
      }
    }

    // Panggil saat load & orientasi layar HP berubah
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);
    window.addEventListener('orientationchange', resizeCanvas);

    document.getElementById('clear-signature').addEventListener('click', () => {
      signaturePad.clear();
      ttdDataInput.value = '';
    });
  }

  // === 2. KOMPRESI BERKAS CLIENT-SIDE ===
  const rawInput = document.getElementById('ktp_raw_input');
  const compressedInput = document.getElementById('ktp_compressed_input');
  const statusBox = document.getElementById('compression-status');
  const statusText = document.getElementById('compression-text');
  const statusSize = document.getElementById('compression-size');
  const statusBar = document.getElementById('compression-bar');
  const btnSubmit = document.getElementById('btn-submit');

  let isCompressing = false;
  const formatSize = (bytes) => (bytes / 1024).toFixed(1) + ' KB';

  async function compressImage(file) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = (e) => {
        const img = new Image();
        img.src = e.target.result;
        img.onload = () => {
          const canvas = document.createElement('canvas');
          const MAX_DIM = 1600;
          let w = img.width;
          let h = img.height;

          if (w > h && w > MAX_DIM) {
            h = Math.round((h * MAX_DIM) / w);
            w = MAX_DIM;
          } else if (h > MAX_DIM) {
            w = Math.round((w * MAX_DIM) / h);
            h = MAX_DIM;
          }

          canvas.width = w;
          canvas.height = h;
          const ctx = canvas.getContext('2d');
          ctx.drawImage(img, 0, 0, w, h);

          canvas.toBlob((blob) => {
            canvas.width = 0; canvas.height = 0;
            if (!blob || blob.size >= file.size) return resolve(file);
            const compressed = new File([blob], file.name.replace(/\.[^/.]+$/, ".jpg"), {
              type: 'image/jpeg',
              lastModified: Date.now()
            });
            resolve(compressed);
          }, 'image/jpeg', 0.65);
        };
        img.onerror = () => resolve(file);
      };
      reader.onerror = () => resolve(file);
    });
  }

  async function compressPdf(file) {
    try {
      const arrayBuffer = await file.arrayBuffer();
      const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
      const newPdfDoc = await PDFLib.PDFDocument.create();

      for (let i = 1; i <= pdf.numPages; i++) {
        statusBar.style.width = Math.round((i / pdf.numPages) * 100) + '%';
        statusText.textContent = 'Mengompresi PDF hal ' + i + '/' + pdf.numPages + '...';

        const page = await pdf.getPage(i);
        const viewport = page.getViewport({ scale: 1.0 });

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        const ctx = canvas.getContext('2d');

        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

        const imgDataUrl = canvas.toDataURL('image/jpeg', 0.60);
        canvas.width = 0; canvas.height = 0;

        const jpgImage = await newPdfDoc.embedJpg(imgDataUrl);
        const newPage = newPdfDoc.addPage([viewport.width, viewport.height]);
        newPage.drawImage(jpgImage, {
          x: 0,
          y: 0,
          width: viewport.width,
          height: viewport.height,
        });
      }

      const pdfBytes = await newPdfDoc.save();
      if (pdfBytes.byteLength >= file.size) return file;

      return new File([pdfBytes], file.name, { type: 'application/pdf', lastModified: Date.now() });
    } catch (err) {
      console.warn('Gagal kompresi PDF, memakai file asli.', err);
      return file;
    }
  }

  if (rawInput) {
    rawInput.addEventListener('change', async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      isCompressing = true;
      btnSubmit.disabled = true;
      statusBox.classList.remove('hidden');
      statusBar.style.width = '30%';
      statusText.className = 'text-blue-600 font-semibold';
      statusText.textContent = 'Memulai proses berkas...';
      statusSize.textContent = formatSize(file.size);

      try {
        let finalFile = file;
        if (file.type.startsWith('image/')) {
          statusText.textContent = 'Mengompresi foto KTP...';
          statusBar.style.width = '60%';
          finalFile = await compressImage(file);
        } else if (file.type === 'application/pdf') {
          finalFile = await compressPdf(file);
        }

        const dt = new DataTransfer();
        dt.items.add(finalFile);
        compressedInput.files = dt.files;

        statusBar.style.width = '100%';
        statusText.className = 'text-emerald-600 font-semibold';
        statusText.textContent = 'Selesai dioptimasi ✓';
        statusSize.textContent = formatSize(file.size) + ' → ' + formatSize(finalFile.size);
      } catch (err) {
        console.error(err);
        statusText.className = 'text-amber-600 font-semibold';
        statusText.textContent = 'Menggunakan berkas asli';
        const dt = new DataTransfer();
        dt.items.add(file);
        compressedInput.files = dt.files;
      } finally {
        isCompressing = false;
        btnSubmit.disabled = false;
      }
    });
  }

  // === 3. SUBMIT FORM VALIDATION ===
  if (form) {
    form.addEventListener('submit', function (e) {
      if (isCompressing) {
        e.preventDefault();
        Swal.fire({ icon: 'info', title: 'Mohon Tunggu', text: 'Proses optimasi berkas sedang berjalan.' });
        return;
      }

      if (!compressedInput.files || compressedInput.files.length === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'KTP Belum Dipilih', text: 'Silakan pilih berkas KTP narasumber terlebih dahulu.' });
        return;
      }

      if (!signaturePad || signaturePad.isEmpty()) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'Tanda Tangan Kosong', text: 'Mohon tanda tangan pada kotak yang telah disediakan.' });
        return;
      }

      ttdDataInput.value = signaturePad.toDataURL('image/png');
    });
  }
});
</script>
</body>
</html>