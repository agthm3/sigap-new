<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Laporan SPJ - {{ $subKegiatan->nama_sub_kegiatan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f3f4f6; }
        /* Memastikan tinggi iframe kokoh dan proporsional */
        .iframe-container { height: 75vh; min-height: 600px; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 shadow-sm h-[80px] flex items-center justify-between px-4 sm:px-6 lg:px-12 sticky top-0 z-50">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">DOKUMEN SPJ</h1>
                <p class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wide">SIGAP BRIDA MAKASSAR</p>
            </div>
        </div>

        <form action="{{ route('spj.public.download', $subKegiatan->uuid) }}" method="POST" id="publicDownloadForm">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl bg-[#7a2222] text-white text-xs sm:text-sm font-semibold hover:bg-[#5c1919] transition-all shadow-md hover:shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Unduh Dokumen
            </button>
        </form>
    </header>

    <!-- Info Banner -->
    <div class="bg-[#fdf7f7] border-b border-[#f0d1d1] px-4 sm:px-6 lg:px-12 py-3 flex items-center gap-3">
        <svg class="w-5 h-5 text-[#7a2222] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-xs sm:text-sm text-gray-700 leading-tight">Sub-Kegiatan: <strong>{{ $subKegiatan->nama_sub_kegiatan }}</strong> ({{ $subKegiatan->bidang->nama_bidang }})</span>
    </div>

    <!-- Container Utama Preview (Dibuat max-w-7xl agar jauh lebih lebar) -->
    <main class="w-full max-w-7xl mx-auto p-4 lg:p-6">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-300">
            <!-- Source mengarah ke route stream menggunakan UUID -->
            <iframe src="{{ route('spj.public.stream', $subKegiatan->uuid) }}" class="w-full iframe-container border-0" title="Preview SPJ"></iframe>
        </div>
    </main>

    <!-- SECTION INFORMASI SIGAP SPJ (MENGALIR DI BAWAH PREVIEW) -->
    <section class="w-full max-w-7xl mx-auto p-4 lg:p-6 pb-12">
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            
            <div class="bg-[#7a2222] text-white px-6 py-4 flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="font-bold text-sm tracking-wide uppercase">Informasi Sistem - SIGAP SPJ</h3>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-700">
                
                <div class="space-y-2">
                    <h4 class="font-extrabold text-gray-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-[#7a2222] rounded-full"></span>
                        Mengenai Dokumen Ini
                    </h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Dokumen ini merupakan Kumpulan Berkas Surat Pertanggungjawaban (SPJ) Administratif yang di-generate secara otomatis melalui modul <strong>SIGAP SPJ</strong> pada platform internal BRIDA Kota Makassar.
                    </p>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Sistem mengompilasi seluruh dokumen cover pembatas, Kerangka Acuan Kerja (KAK), Surat Keputusan (SK), Surat Perintah (SP), hingga lampiran teknis per gelombang kegiatan secara digital.
                    </p>
                </div>

                <div class="space-y-2">
                    <h4 class="font-extrabold text-gray-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-[#7a2222] rounded-full"></span>
                        Struktur & Validitas Berkas
                    </h4>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Kompilasi berkas disusun secara berjenjang berdasarkan regulasi pelaporan keuangan daerah:
                    </p>
                    <ul class="list-disc pl-4 text-[11px] text-gray-500 space-y-1">
                        <li>Level Sub-Kegiatan (Sampul Induk & KAK)</li>
                        <li>Level Kegiatan (SK Panitia / SK Tenaga Ahli)</li>
                        <li>Level Gelombang (Undangan, Daftar Hadir, Notulensi, Dokumentasi, & Materi)</li>
                    </ul>
                </div>

                <div class="space-y-2">
                    <h4 class="font-extrabold text-gray-900 flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-[#7a2222] rounded-full"></span>
                        Catatan Untuk Pemeriksa
                    </h4>
                    <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg text-xs">
                        <p class="leading-relaxed text-gray-600 mb-2">
                            Apabila Anda memerlukan berkas fisik asli bertandatangan basah/elektronik secara terpisah, Anda dapat berkoordinasi dengan Admin SPJ BRIDA Makassar atau mengklik tombol <strong>"Unduh Dokumen"</strong> di atas untuk menyimpan master PDF.
                        </p>
                        <div class="text-[10px] text-gray-400 italic flex items-center gap-1">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            Terverifikasi secara Sistem Digital SIGAP
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-gray-50 px-6 py-2.5 border-t border-gray-200 text-[11px] text-gray-400 text-center">
                Sistem Informasi Pertanggungjawaban Perjalanan Dinas & Kegiatan Lapangan (SIGAP) — BRIDA Kota Makassar © {{ date('Y') }}
            </div>

        </div>
    </section>

    <!-- Script Fetch API Download -->
    <script>
        document.getElementById('publicDownloadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = this;
            const url = form.action;
            const csrfToken = form.querySelector('input[name="_token"]').value;

            Swal.fire({
                title: 'Mempersiapkan Unduhan...',
                text: 'Sistem sedang menyatukan dan merapikan halaman PDF.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });

                if (!response.ok) throw new Error('Gagal mengunduh laporan.');

                let filename = 'Laporan_SPJ.pdf';
                const disposition = response.headers.get('Content-Disposition');
                if (disposition && disposition.indexOf('filename=') !== -1) {
                    const matches = /filename="([^"]*)"/.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1];
                }

                const blob = await response.blob();
                const downloadUrl = window.URL.createObjectURL(blob);
                
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();

                window.URL.revokeObjectURL(downloadUrl);
                document.body.removeChild(a);

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Dokumen SPJ siap diperiksa.',
                    timer: 2000,
                    showConfirmButton: false
                });

            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: error.message });
            }
        });
    </script>
</body>
</html>