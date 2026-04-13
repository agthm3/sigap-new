<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Sertifikat — SIGAP BRIDA</title>

<script src="https://cdn.tailwindcss.com"></script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

<style>

#sertifikat-area{
width:100%;
margin:auto;
background:white;
}

/* hanya saat print dipaksa ukuran A4 */
@media print{

@page{
size:A4 landscape;
margin:0;
}

body *{
visibility:hidden;
}

#sertifikat-area,
#sertifikat-area *{
visibility:visible;
}

#sertifikat-area{
position:absolute;
top:0;
left:0;
width:297mm;
height:210mm;
}

button{
display:none;
}

.logo{
width:56px;
height:56px;
object-fit:contain;
}
}
</style>
</head>

<body class="bg-gray-100 py-10">

<div class="max-w-5xl mx-auto bg-white shadow-xl rounded-xl border border-gray-200 overflow-hidden">

<div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">

<div>
<h2 class="font-semibold text-gray-800">
Sertifikat Digital
</h2>

<p class="text-xs text-gray-500">
Sistem Informasi Gabungan Arsip & Privasi
</p>
</div>

<!-- BUTTON -->
<button onclick="window.print()"
class="flex items-center gap-2 px-4 py-2 bg-[#7a2222] text-white rounded-lg">

Download Sertifikat

</button>

</div>


<!-- CERTIFICATE -->

<div id="sertifikat-area" class="flex items-center justify-center">

<div class="w-full h-full border border-gray-300">

<div class="border-[8px] border-[#7a2222] h-full">

<div class="border border-[#7a2222] p-8 h-full relative">

<!-- LOGOS -->

<div class="flex items-center justify-between mb-6">

<div class="flex items-center gap-2">

<div class="w-40 h-20 flex items-center justify-center">
<img src="{{ asset('images/sertifikat/logo-pemkot.png') }}" class="logo">
</div>


</div>

<div class="flex items-center gap-2">



<div class="w-40 h-20 flex items-center justify-center">
<img src="{{ asset('images/sertifikat/logo-brida.png') }}" class="logo">
</div>

</div>

</div>


<div class="text-center mb-3">

<p class="text-xs text-gray-500 uppercase tracking-widest">
Nomor Sertifikat
</p>

<p class="text-[#7a2222] font-semibold">
{{ $sertifikat->nomor_sertifikat }}
</p>

</div>


<div class="text-center">

<p class="text-sm tracking-widest uppercase text-gray-500">
Sertifikat Resmi
</p>

<h1 class="mt-2 text-4xl font-extrabold text-[#7a2222] font-sertifikat">
Sertifikat Penghargaan
</h1>

<div class="w-24 h-1 bg-[#7a2222] mx-auto mt-4"></div>

</div>


<div class="mt-10 text-center">

<p class="text-lg text-gray-700">
Diberikan kepada:
</p>

<h2 class="mt-4 text-3xl font-bold text-gray-900 font-sertifikat">
{{ $sertifikat->nama_penerima }}
</h2>

<p class="mt-4 text-gray-700 max-w-2xl mx-auto">
atas partisipasi dalam kegiatan
</p>

<p class="mt-2 text-xl font-semibold text-gray-900 font-sertifikat">
{{ $sertifikat->kegiatan->nama_kegiatan }}
</p>

<p class="mt-4 text-gray-600 max-w-2xl mx-auto">
yang diselenggarakan oleh
<strong>Badan Riset dan Inovasi Daerah Kota Makassar</strong>
</p>

</div>


<div class="mt-10 grid grid-cols-2 items-end">

<div class="text-center">

<div class="mx-auto w-fit p-2 border rounded bg-white">

  <img
    src="{{ asset('images/sertifikat/qrcode.png') }}"
    alt="QR Verifikasi Sertifikat"
    class="w-24 h-24 object-contain">

</div>

<p class="mt-2 text-[11px] text-gray-500">
Scan untuk verifikasi sertifikat ini secara online
</p>

</div>

<div class="text-center">

<p class="text-sm text-gray-600">
Makassar, {{ $sertifikat->kegiatan->tanggal }}
</p>

<img
src="{{ asset('images/sertifikat/ttd.png') }}"
class="mx-auto h-20 mt-4">

<p class="mt-2 font-semibold text-gray-900">
Haidil Adha, S.Sos., M.M.
</p>

<p class="text-sm text-gray-600">
Kepala Badan Riset dan Inovasi Daerah Kota Makassar
</p>

</div>

</div>


<div class="absolute inset-0 flex items-center justify-center pointer-events-none">

<p class="text-7xl font-extrabold text-gray-200 rotate-[-30deg] opacity-10 select-none">
SIGAP BRIDA
</p>

</div>

</div>
</div>
</div>

</div>

</div>

</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>

new QRCode(document.getElementById("qrcode"), {
text: "{{ url('/sertifikat?no='.$sertifikat->nomor_sertifikat) }}",
width:90,
height:90
});

</script>

</body>
</html>