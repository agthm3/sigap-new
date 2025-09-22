@extends('layouts.page') {{-- publik ringan --}}

@section('head')
  <meta property="og:title" content="Bukti Kinerja Tahun {{ $year }} — SIGAP BRIDA">
  <meta property="og:description" content="Daftar tautan bukti kinerja untuk tahun {{ $year }}.">
  <meta property="og:type" content="article">
  <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
<section class="max-w-6xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <div class="px-4 sm:px-6 py-4 border-b bg-gray-50 flex items-center justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
          Bukti Kinerja — Tahun {{ $year }}
        </h1>
        <div class="mt-1 flex flex-wrap items-center gap-2 text-xs sm:text-sm text-gray-600">
          <span class="px-2 py-0.5 rounded-full bg-gray-100 border">Tahun: {{ $year }}</span>

          @php
            $catLabel = $meta['category'] ?? '';
            $rhkLabel = $meta['rhk'] ?? '';
            $qLabel   = $meta['q'] ?? '';
          @endphp

          @if($catLabel !== '')
            <span class="px-2 py-0.5 rounded-full bg-gray-100 border">Kategori: {{ $catLabel }}</span>
          @endif
          @if($rhkLabel !== '')
            <span class="px-2 py-0.5 rounded-full bg-gray-100 border">RHK: {{ $rhkLabel }}</span>
          @endif
          @if($qLabel !== '')
            <span class="px-2 py-0.5 rounded-full bg-gray-100 border">Cari: "{{ $qLabel }}"</span>
          @endif
        </div>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
          Bagikan satu tautan ini; siapa pun yang membuka akan melihat daftar sesuai filter di atas.
        </p>
      </div>
      <div class="flex gap-2">
        <button id="btnCopyPageLink"
                class="px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 text-sm">
          Salin Link Halaman
        </button>
        <button id="btnCopyAll"
                class="px-3 py-2 rounded-md border border-gray-300 hover:bg-gray-50 text-sm">
          Salin Semua Link (Teks)
        </button>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="border-b bg-white">
          <tr class="text-left">
            <th class="px-4 py-3 w-40">Kategori</th>
            <th class="px-4 py-3 w-28">RHK</th>
            <th class="px-4 py-3 w-40">Tanggal</th>
            <th class="px-4 py-3">Judul</th>
            <th class="px-4 py-3 w-[420px]">Link</th>
            <th class="px-4 py-3 w-28 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse ($items as $it)
            @php
              $tgl  = \Carbon\Carbon::parse($it['date'] ?? now())->locale('id')->translatedFormat('d F Y');
              $link = $it['link'] ?? ''; // controller mengirim key 'link'
            @endphp
            <tr>
              <td class="px-4 py-3">{{ $it['category'] }}</td>
              <td class="px-4 py-3">{{ $it['rhk'] ?? '-' }}</td>
              <td class="px-4 py-3 whitespace-nowrap">{{ $tgl }}</td>
              <td class="px-4 py-3">
                <a href="{{ $link }}" target="_blank" class="text-maroon hover:underline">{{ $it['title'] }}</a>
              </td>
              <td class="px-4 py-3">
                <div class="truncate">
                  <a href="{{ $link }}" target="_blank" class="hover:underline">{{ $link }}</a>
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <button type="button" class="px-3 py-1.5 rounded-md bg-maroon text-white hover:bg-maroon-800"
                        onclick="copyText(@js($link))">Salin</button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                Tidak ada data untuk filter ini pada tahun {{ $year }}.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-4 sm:px-6 py-3 border-t text-xs text-gray-500 bg-gray-50">
      Ditayangkan oleh <b>BRIDA Kota Makassar</b>. Akses ke halaman ini dapat dicatat untuk audit (fitur nanti).
    </div>
  </div>
</section>

@push('scripts')
<script>
  function toLines() {
    const rows = Array.from(document.querySelectorAll('tbody tr'));
    return rows.map(r => {
      const tds = r.querySelectorAll('td');
      if(!tds.length) return '';
      const kategori = tds[0].innerText.trim();
      const rhk      = tds[1].innerText.trim();
      const tanggal  = tds[2].innerText.trim();
      const judul    = tds[3].innerText.trim();
      const link     = tds[4].querySelector('a')?.href || '';
      return `• [${tanggal}] ${kategori} — ${rhk} — ${judul}\n  ${link}`;
    }).filter(Boolean).join('\n');
  }

  async function copyText(txt){
    try{
      await navigator.clipboard.writeText(txt);
      Swal.fire({ icon:'success', title:'Tersalin', timer:1400, showConfirmButton:false });
    }catch(e){
      Swal.fire({ icon:'error', title:'Gagal menyalin' });
    }
  }

  document.getElementById('btnCopyAll')?.addEventListener('click', async () => {
    const all = toLines();
    if(!all) return Swal.fire({ icon:'info', title:'Tidak ada data untuk disalin' });
    copyText(all);
  });

  document.getElementById('btnCopyPageLink')?.addEventListener('click', async () => {
    copyText(window.location.href); // URL + query filter
  });
</script>

<script>
  function toastOK(msg='Tersalin'){
    Swal.fire({ icon:'success', title: msg, timer: 1400, showConfirmButton: false });
  }
  function toastErr(msg='Gagal menyalin'){
    Swal.fire({ icon:'error', title: msg });
  }

  function toLines() {
    const rows = Array.from(document.querySelectorAll('tbody tr'));
    return rows.map(r => {
      const tds = r.querySelectorAll('td');
      if(!tds.length) return '';
      const kategori = tds[0].innerText.trim();
      const rhk      = tds[1].innerText.trim();
      const tanggal  = tds[2].innerText.trim();
      const judul    = tds[3].innerText.trim();
      const link     = tds[4].querySelector('a')?.href || '';
      return `• [${tanggal}] ${kategori} — ${rhk} — ${judul}\n  ${link}`;
    }).filter(Boolean).join('\n');
  }

  async function copyText(txt){
    try{
      await navigator.clipboard.writeText(txt);
      toastOK();
    }catch(e){
      toastErr();
    }
  }

  document.getElementById('btnCopyAll')?.addEventListener('click', async () => {
    const all = toLines();
    if(!all) return Swal.fire({ icon:'info', title:'Tidak ada data untuk disalin' });
    copyText(all);
  });

  document.getElementById('btnCopyPageLink')?.addEventListener('click', async () => {
    copyText(window.location.href); // URL + query filter
  });
</script>

@endpush
@endsection
