<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
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
</head>
<body class="bg-gray-50 text-gray-800 py-8">
<div class="max-w-3xl mx-auto px-4">

  <div class="text-center mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900">Form Kesediaan Narasumber</h1>
    <p class="text-sm text-gray-600 mt-1">{{ $kegiatan->nama_kegiatan }} <br> {{$kegiatan->hari_tanggal }}</p>
  </div>

  @if(session('success_name'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl mb-6 text-center">
      <h2 class="font-bold">Terima Kasih, {{ session('success_name') }}!</h2>
      <p class="text-sm">Data kesediaan Anda telah kami terima.</p>
    </div>
  @else
    @if (isset($errors) && $errors->any())
      <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-6 text-sm">
        <strong class="font-semibold">Mohon periksa kembali form Anda:</strong>
        <ul class="list-disc list-inside mt-1">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="narasumber-form" action="{{ route('sigap-narasumber.store-public', $kegiatan->uuid) }}" method="POST" enctype="multipart/form-data" class="bg-white border rounded-2xl p-6 shadow-sm space-y-5">
      @csrf
      
      <h3 class="font-bold text-gray-900 border-b pb-2">Identitas & Kontak Dasar</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="text-sm font-medium">Nama Lengkap + Gelar <span class="text-red-500">*</span></label>
          <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500" required>
        </div>
        <div>
          <label class="text-sm font-medium">No. HP / WhatsApp <span class="text-red-500">*</span></label>
          <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500" required>
        </div>
        <div>
          <label class="text-sm font-medium">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" value="{{ old('email') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500" required>
        </div>
        <div>
          <label class="text-sm font-medium">Alamat Kantor</label>
          <textarea name="alamat_kantor" rows="2" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">{{ old('alamat_kantor') }}</textarea>
        </div>
        <div>
          <label class="text-sm font-medium">Alamat Rumah</label>
          <textarea name="alamat_rumah" rows="2" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">{{ old('alamat_rumah') }}</textarea>
        </div>
        <div class="col-span-2">
          <label class="text-sm font-medium">Materi yang akan dibawakan</label>
          <input type="text" name="materi" value="{{ old('materi') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
      </div>

      <h3 class="font-bold text-gray-900 border-b pb-2 pt-4">Biodata Lengkap (Administrasi)</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">NIK (Nomor Induk Kependudukan) <span class="text-red-500">*</span></label>
          <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" pattern="\d{16}" inputmode="numeric" placeholder="16 digit NIK" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500" required>
        </div>
        <div>
          <label class="text-sm font-medium">No. NPWP</label>
          <input type="text" name="npwp" value="{{ old('npwp') }}" placeholder="Contoh: 12.345.678.9-012.000" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">NIP</label>
          <input type="text" name="nip" value="{{ old('nip') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">Tempat / Tanggal Lahir</label>
          <input type="text" name="tempat_tanggal_lahir" value="{{ old('tempat_tanggal_lahir') }}" placeholder="Makassar, 01 Januari 1980" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">Pangkat / Gol. Ruang</label>
          <input type="text" name="pangkat_golongan" value="{{ old('pangkat_golongan') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">Jabatan</label>
          <input type="text" name="jabatan" value="{{ old('jabatan') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div class="col-span-2">
          <label class="text-sm font-medium">Instansi / Unit Kerja</label>
          <input type="text" name="instansi_unit_kerja" value="{{ old('instansi_unit_kerja') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">Agama</label>
          <input type="text" name="agama" value="{{ old('agama') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">Status Keluarga</label>
          <input type="text" name="status_keluarga" value="{{ old('status_keluarga') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">Hobby</label>
          <input type="text" name="hobby" value="{{ old('hobby') }}" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="text-sm font-medium">No. Rekening & Nama Bank</label>
          <input type="text" name="no_rekening" value="{{ old('no_rekening') }}" placeholder="Misal: 123456789 - Bank Sulselbar" class="w-full mt-1 rounded-xl border-gray-300 border p-2 focus:ring-red-500 focus:border-red-500">
        </div>

        <!-- Bagian Upload & Kompres KTP -->
        <div class="col-span-2">
          <label class="text-sm font-medium">Upload Scan / Foto KTP <span class="text-red-500">*</span></label>
          <input type="file" id="ktp_raw_input" accept="image/png, image/jpeg, image/jpg, application/pdf" class="w-full mt-1 rounded-xl border-gray-300 border p-2 file:mr-4 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100" required>
          
          <!-- File input sesungguhnya yang akan dikirim ke Laravel -->
          <input type="file" name="file_ktp" id="ktp_compressed_input" class="hidden">

          <!-- Status Kompresi -->
          <div id="compression-status" class="hidden mt-2 p-3 rounded-xl border bg-gray-50 text-xs">
            <div class="flex items-center justify-between font-semibold">
              <span id="compression-text" class="text-blue-600">Mengompres berkas...</span>
              <span id="compression-size" class="text-gray-500"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
              <div id="compression-bar" class="bg-red-600 h-1.5 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
          </div>
          <p class="text-xs text-gray-500 mt-1">Mendukung format JPG, PNG, atau PDF. Berkas otomatis dioptimasi di browser sebelum dikirim.</p>
        </div>
      </div>

      <h3 class="font-bold text-gray-900 border-b pb-2 pt-4">Tanda Tangan Digital <span class="text-red-500">*</span></h3>
      <div>
        <div class="rounded-2xl border-2 border-dashed border-gray-300 p-2 bg-gray-50">
          <canvas id="signature-pad" class="w-full h-48"></canvas>
        </div>
        <input type="hidden" id="ttd_data" name="ttd_data">
        <button type="button" id="clear-signature" class="mt-2 px-3 py-1.5 rounded-lg border text-xs bg-white shadow-sm hover:bg-gray-50">Hapus TTD</button>
      </div>

      <button type="submit" id="btn-submit" class="w-full mt-6 py-3 rounded-xl bg-red-800 text-white font-bold hover:bg-red-900 transition-colors">
        Kirim Kesediaan
      </button>
    </form>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // === 1. TANDA TANGAN DIGITAL ===
  const canvas = document.getElementById('signature-pad');
  const ttdDataInput = document.getElementById('ttd_data');
  const form = document.getElementById('narasumber-form');
  let signaturePad = null;

  if (canvas) {
    signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });

    function resizeCanvas() {
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext('2d').scale(ratio, ratio);
    }
    resizeCanvas();

    document.getElementById('clear-signature').addEventListener('click', () => {
      signaturePad.clear();
      ttdDataInput.value = '';
    });
  }

  // === 2. KOMPRESI CLIENT-SIDE (GAMBAR & PDF) ===
  const rawInput = document.getElementById('ktp_raw_input');
  const compressedInput = document.getElementById('ktp_compressed_input');
  const statusBox = document.getElementById('compression-status');
  const statusText = document.getElementById('compression-text');
  const statusSize = document.getElementById('compression-size');
  const statusBar = document.getElementById('compression-bar');
  const btnSubmit = document.getElementById('btn-submit');

  let isCompressing = false;

  const formatSize = (bytes) => (bytes / 1024).toFixed(1) + ' KB';

  // Kompres Gambar via Canvas
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
            canvas.width = 0; canvas.height = 0; // memory cleanup
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

  // Kompres PDF (pdf.js -> Canvas -> pdf-lib)
  async function compressPdf(file) {
    try {
      const arrayBuffer = await file.arrayBuffer();
      const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
      const newPdfDoc = await PDFLib.PDFDocument.create();

      for (let i = 1; i <= pdf.numPages; i++) {
        statusBar.style.width = Math.round((i / pdf.numPages) * 100) + '%';
        statusText.textContent = `Mengompresi PDF hal ${i} dari ${pdf.numPages}...`;

        const page = await pdf.getPage(i);
        const viewport = page.getViewport({ scale: 1.0 });

        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        const ctx = canvas.getContext('2d');

        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

        const imgDataUrl = canvas.toDataURL('image/jpeg', 0.60);
        canvas.width = 0; canvas.height = 0; // memory cleanup

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
      console.warn('Kompresi PDF gagal, menggunakan file asli.', err);
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
      statusText.className = 'text-blue-600';
      statusText.textContent = 'Memulai kompresi berkas...';
      statusSize.textContent = formatSize(file.size);

      try {
        let finalFile = file;
        if (file.type.startsWith('image/')) {
          statusText.textContent = 'Mengompresi gambar...';
          statusBar.style.width = '60%';
          finalFile = await compressImage(file);
        } else if (file.type === 'application/pdf') {
          finalFile = await compressPdf(file);
        }

        // Masukkan file hasil kompresi ke input hidden yang akan disubmit
        const dt = new DataTransfer();
        dt.items.add(finalFile);
        compressedInput.files = dt.files;

        statusBar.style.width = '100%';
        statusText.className = 'text-emerald-600';
        statusText.textContent = 'Selesai dioptimasi ✓';
        statusSize.textContent = `${formatSize(file.size)} → ${formatSize(finalFile.size)}`;
      } catch (err) {
        console.error(err);
        statusText.className = 'text-amber-600';
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

  // === 3. VALIDASI FORM SUBMIT ===
  if (form) {
    form.addEventListener('submit', function (e) {
      if (isCompressing) {
        e.preventDefault();
        Swal.fire({ icon: 'info', title: 'Mohon Tunggu', text: 'Proses kompresi berkas KTP sedang berjalan.' });
        return;
      }

      if (!compressedInput.files || compressedInput.files.length === 0) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'KTP Belum Dipilih', text: 'Silakan pilih berkas KTP terlebih dahulu.' });
        return;
      }

      if (!signaturePad || signaturePad.isEmpty()) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'TTD Kosong', text: 'Tanda tangan digital wajib diisi.' });
        return;
      }

      ttdDataInput.value = signaturePad.toDataURL('image/png');
    });
  }
});
</script>
</body>
</html>