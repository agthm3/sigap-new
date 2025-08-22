<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Detail Dokumen — SIGAP BRIDA</title>

  <!-- Tailwind -->
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

  <!-- Navbar -->
  <header class="border-b border-maroon/10 bg-white sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="index.html" class="flex items-center gap-3">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-md bg-maroon text-white font-extrabold">SB</span>
        <div>
          <p class="text-sm font-semibold text-maroon leading-4">SIGAP BRIDA</p>
          <p class="text-[11px] text-gray-500 -mt-0.5">Sistem Informasi Gabungan Arsip & Privasi</p>
        </div>
      </a>
      <nav class="hidden md:flex items-center gap-6 text-sm">
        <a href="sigap-dokumen.html" class="hover:text-maroon">Dokumen</a>
        <a href="pegawai.html" class="hover:text-maroon">Pegawai</a>
        <a href="admin-dashboard.html" class="hover:text-maroon">Admin</a>
        <a href="login.html" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">Login</a>
      </nav>
    </div>
  </header>

  <!-- Header -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <h1 class="text-2xl font-extrabold text-gray-900">Detail Dokumen</h1>
    <p class="text-sm text-gray-600 mt-1">Informasi lengkap dan riwayat akses dokumen.</p>
  </section>

  <!-- Konten -->
  <section class="max-w-7xl mx-auto px-4 grid lg:grid-cols-3 gap-6">
    <!-- Kiri: Preview -->
    <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-5 py-4 border-b">
        <h2 class="text-lg font-bold text-gray-800">Preview Dokumen</h2>
      </div>
      <div class="aspect-[4/3] bg-gray-100 flex items-center justify-center">
        <!-- contoh PDF preview -->
        @if($isPdf)
        <iframe src="{{ $fileUrl }}" class="w-full h-full" frameborder="0"></iframe>
        @elseif($isImage)
        <img src="{{ $fileUrl }}" class="w-full h-full object-cover" alt="Preview Image">
        @else
          <p>Preview tidak tersedia.</p>
        @endif
      </div>
    </div>

    <!-- Kanan: Detail Info -->
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-5 py-4 border-b">
        <h2 class="text-lg font-bold text-gray-800">Informasi Dokumen</h2>
      </div>
      <div class="p-5 space-y-3 text-sm">
        <p><span class="font-semibold">Judul:</span> {{ $doc->title }}</p>
        <p><span class="font-semibold">Alias:</span> {{ $doc->alias }}</p>
        <p><span class="font-semibold">Kategori:</span> {{ $doc->category }}</p>
        <p><span class="font-semibold">Tahun:</span> {{ $doc->year }}</p>
        <p><span class="font-semibold">Pihak Terkait:</span> {{ $doc->stakeholder }}</p>
        <p><span class="font-semibold">Status Akses:</span> <span class="px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-700">{{ $doc->sensitivity }}</span></p>
        <p><span class="font-semibold">Deskripsi:</span> {{ $doc->description }}</p>
      </div>
      <div class="px-5 pb-5">
        <a href="{{ $fileUrl }}" target="_blank" class="w-full px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
            Download Dokumen
        </a>
        {{-- <button id="btnDownload" class="w-full px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">Download Dokumen</button> --}}
      </div>
    </div>
  </section>

  <!-- Riwayat Akses -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-5 py-4 border-b">
        <h2 class="text-lg font-bold text-gray-800">Riwayat Akses</h2>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 border-b">
            <tr class="text-left">
              <th class="px-4 py-2">Nama Pengguna</th>
              <th class="px-4 py-2">Tanggal Akses</th>
              <th class="px-4 py-2">Jenis Akses</th>
              <th class="px-4 py-2">Alasan</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr>
              <td class="px-4 py-2">Andi Rahman</td>
              <td class="px-4 py-2">10 Aug 2025 • 14:05</td>
              <td class="px-4 py-2">Download</td>
              <td class="px-4 py-2">Administrasi internal</td>
            </tr>
            <tr>
              <td class="px-4 py-2">Budi Santoso</td>
              <td class="px-4 py-2">09 Aug 2025 • 10:21</td>
              <td class="px-4 py-2">View</td>
              <td class="px-4 py-2">Verifikasi data pegawai</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Modal Kode Akses -->
  <div id="modalKode" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeModalKode()"></div>
    <div class="relative z-10 mx-auto max-w-md px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-maroon text-white">
          <h2 class="text-lg font-bold">Masukkan Kode Akses</h2>
          <p class="text-sm text-white/80">Dokumen ini memerlukan kode atau alasan akses.</p>
        </div>
        <form onsubmit="event.preventDefault(); verifikasiKode();" class="p-5 space-y-4">
          <div>
            <label class="text-sm font-semibold text-gray-700">Kode Akses</label>
            <input type="password" id="kodeAkses" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Masukkan kode...">
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Alasan Akses</label>
            <textarea id="alasanAkses" rows="3" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Jelaskan alasan Anda..."></textarea>
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeModalKode()">Batal</button>
            <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 py-8 text-sm text-gray-600">
      © 2025 SIGAP BRIDA • BRIDA Kota Makassar
    </div>
  </footer>

  <script>
    document.getElementById('btnDownload').addEventListener('click', () => {
      // Simulasi: kalau statusnya terkendali, minta kode akses
      openModalKode();
    });

    function openModalKode(){
      document.getElementById('modalKode').classList.remove('hidden');
    }
    function closeModalKode(){
      document.getElementById('modalKode').classList.add('hidden');
    }
    function verifikasiKode(){
      const kode = document.getElementById('kodeAkses').value.trim();
      const alasan = document.getElementById('alasanAkses').value.trim();
      if(!kode || !alasan){
        alert('Mohon isi kode dan alasan akses.');
        return;
      }
      closeModalKode();
      alert('Kode diterima. Dokumen akan diunduh...');
      // window.location.href = 'file.pdf'; // proses unduh asli
    }
  </script>

</body>
</html>
