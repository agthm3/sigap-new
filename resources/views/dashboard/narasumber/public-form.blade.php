<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Form Kesediaan Narasumber</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 py-8">
<div class="max-w-3xl mx-auto px-4">

  <div class="text-center mb-6">
    <h1 class="text-2xl font-extrabold text-gray-900">Form Kesediaan Narasumber</h1>
    <p class="text-sm text-gray-600 mt-1">{{ $kegiatan->nama_kegiatan }} <br> {{ $kegiatan->hari_tanggal }}</p>
  </div>

  @if(session('success_name'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl mb-6 text-center">
      <h2 class="font-bold">Terima Kasih, {{ session('success_name') }}!</h2>
      <p class="text-sm">Data kesediaan Anda telah kami terima.</p>
    </div>
  @else
    <form id="narasumber-form" action="{{ route('sigap-narasumber.store-public', $kegiatan->uuid) }}" method="POST" class="bg-white border rounded-2xl p-6 shadow-sm space-y-5">
      @csrf
      
      <h3 class="font-bold text-gray-900 border-b pb-2">Identitas & Kontak Dasar</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="col-span-2">
          <label class="text-sm font-medium">Nama Lengkap + Gelar <span class="text-red-500">*</span></label>
          <input type="text" name="nama_lengkap" class="w-full mt-1 rounded-xl border-gray-300 border p-2" required>
        </div>
        <div>
          <label class="text-sm font-medium">No. HP / WhatsApp <span class="text-red-500">*</span></label>
          <input type="text" name="no_hp" class="w-full mt-1 rounded-xl border-gray-300 border p-2" required>
        </div>
        <div>
          <label class="text-sm font-medium">Email <span class="text-red-500">*</span></label>
          <input type="email" name="email" class="w-full mt-1 rounded-xl border-gray-300 border p-2" required>
        </div>
        <div>
          <label class="text-sm font-medium">Alamat Kantor</label>
          <textarea name="alamat_kantor" class="w-full mt-1 rounded-xl border-gray-300 border p-2"></textarea>
        </div>
        <div>
          <label class="text-sm font-medium">Alamat Rumah</label>
          <textarea name="alamat_rumah" class="w-full mt-1 rounded-xl border-gray-300 border p-2"></textarea>
        </div>
        <div class="col-span-2">
          <label class="text-sm font-medium">Materi yang akan dibawakan</label>
          <input type="text" name="materi" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
      </div>

      <h3 class="font-bold text-gray-900 border-b pb-2 pt-4">Biodata Lengkap (Administrasi)</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">NIP</label>
          <input type="text" name="nip" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Tempat / Tanggal Lahir</label>
          <input type="text" name="tempat_tanggal_lahir" placeholder="Makassar, 01 Januari 1980" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Pangkat / Gol. Ruang</label>
          <input type="text" name="pangkat_golongan" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Jabatan</label>
          <input type="text" name="jabatan" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div class="col-span-2">
          <label class="text-sm font-medium">Instansi / Unit Kerja</label>
          <input type="text" name="instansi_unit_kerja" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Agama</label>
          <input type="text" name="agama" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Status Keluarga</label>
          <input type="text" name="status_keluarga" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">Hobby</label>
          <input type="text" name="hobby" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div>
          <label class="text-sm font-medium">No. NPWP</label>
          <input type="text" name="npwp" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
        <div class="col-span-2">
          <label class="text-sm font-medium">No. Rekening & Nama Bank</label>
          <input type="text" name="no_rekening" placeholder="Misal: 123456789 - Bank Sulselbar" class="w-full mt-1 rounded-xl border-gray-300 border p-2">
        </div>
      </div>

      <h3 class="font-bold text-gray-900 border-b pb-2 pt-4">Tanda Tangan Digital <span class="text-red-500">*</span></h3>
      <div>
        <div class="rounded-2xl border-2 border-dashed border-gray-300 p-2 bg-gray-50">
          <canvas id="signature-pad" class="w-full h-48"></canvas>
        </div>
        <input type="hidden" id="ttd_data" name="ttd_data">
        <button type="button" id="clear-signature" class="mt-2 px-3 py-1.5 rounded-lg border text-xs bg-white shadow-sm">Hapus TTD</button>
      </div>

      <button type="submit" class="w-full mt-6 py-3 rounded-xl bg-red-800 text-white font-bold hover:bg-red-900">
        Kirim Kesediaan
      </button>
    </form>
  @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const canvas = document.getElementById('signature-pad');
  const ttdDataInput = document.getElementById('ttd_data');
  const form = document.getElementById('narasumber-form');
  if(!canvas) return;

  const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255,255,255)' });

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

  if(form) {
    form.addEventListener('submit', function (e) {
      if (signaturePad.isEmpty()) {
        e.preventDefault();
        Swal.fire({ icon: 'warning', title: 'TTD Kosong', text: 'Tanda tangan digital wajib diisi.' });
      } else {
        ttdDataInput.value = signaturePad.toDataURL('image/png');
      }
    });
  }
});
</script>
</body>
</html>