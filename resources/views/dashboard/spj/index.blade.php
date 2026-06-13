@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">SPJ</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">Manajemen, Pengisian Berkas, dan Pembuatan Laporan Dokumen SPJ.</p>
  </div>
  @hasrole('admin')
    <a href="{{ route('sigap-spj.bidang.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800 transition-colors shadow-sm">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
      Kelola Struktur Master
    </a>
  @endhasrole
</section>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mt-6">
  <form action="{{ route('sigap-spj.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
    
    <div>
      <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Sub-Kegiatan</label>
      <div class="relative">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik nama sub-kegiatan..." 
               class="w-full pl-8 pr-3 py-2 rounded-xl text-sm border-gray-300 focus:ring-maroon focus:border-maroon">
        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"></path></svg>
      </div>
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Bidang Kantor</label>
      <select name="bidang_id" class="w-full rounded-xl text-sm border-gray-300 focus:ring-maroon focus:border-maroon">
        <option value="">-- Semua Bidang --</option>
        @foreach($allBidangs as $b)
          <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama_bidang }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-xs font-semibold text-gray-600 mb-1">Status Berkas KAK</label>
      <select name="status_kak" class="w-full rounded-xl text-sm border-gray-300 focus:ring-maroon focus:border-maroon">
        <option value="">-- Semua Status --</option>
        <option value="terisi" {{ request('status_kak') === 'terisi' ? 'selected' : '' }}>TERISI</option>
        <option value="kosong" {{ request('status_kak') === 'kosong' ? 'selected' : '' }}>KOSONG / BELUM ADA</option>
      </select>
    </div>

    <div class="flex gap-2">
      <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-gray-800 text-white font-semibold text-sm hover:bg-gray-950 transition-colors flex items-center justify-center gap-1.5 shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
        Filter
      </button>
      
      @if(request()->anyFilled(['search', 'bidang_id', 'status_kak']))
        <a href="{{ route('sigap-spj.index') }}" class="py-2 px-3 rounded-xl bg-gray-100 text-gray-700 font-semibold text-sm hover:bg-gray-200 border border-gray-300 transition-colors flex items-center justify-center" title="Reset Filter">
          Reset
        </a>
      @endif
    </div>

  </form>
</div>

<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
  <div class="px-4 py-3 border-b bg-gray-50 flex justify-between items-center">
    <h2 class="font-semibold text-gray-900">Daftar Sub-Kegiatan (Laporan Induk)</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left w-[25%]">Nama Bidang</th>
          <th class="px-4 py-3 text-left w-[40%]">Nama Sub-Kegiatan</th>
          <th class="px-4 py-3 text-center">Struktur Data</th>
          <th class="px-4 py-3 text-center">Status KAK</th>
          <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($subKegiatans as $sub)
        <tr class="hover:bg-gray-50/50 transition-colors">
          <td class="px-4 py-3 font-medium text-gray-900">{{ $sub->bidang->nama_bidang }}</td>
          <td class="px-4 py-3 whitespace-pre-line text-gray-700">{{ $sub->nama_sub_kegiatan }}</td>
          <td class="px-4 py-3 text-center">
            <div class="inline-flex flex-col gap-1 items-center bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1.5">
              <span class="text-xs font-bold text-gray-800">{{ $sub->kegiatans->count() }} Kegiatan</span>
              <span class="text-[10px] text-gray-500 font-medium">Total: {{ $sub->kegiatans->sum(fn($k) => $k->gelombangs->count()) }} Gelombang</span>
            </div>
          </td>
          <td class="px-4 py-3 text-center">
            @if($sub->file_kak)
              <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border bg-emerald-50 border-emerald-200 text-emerald-700 shadow-sm">
                <svg class="w-3 h-3 mr-1 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                TERISI
              </span>
            @else
              <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border bg-red-50 border-red-200 text-red-700 shadow-sm animate-pulse">
                ⚠️ KOSONG
              </span>
            @endif
          </td>
          <td class="px-4 py-3">
            <div class="flex items-center justify-center gap-1.5">
              
              <a href="{{ route('sigap-spj.show', $sub->id) }}" class="px-3 py-1.5 rounded-lg border border-gray-300 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition-all flex items-center gap-1">
                Isi Berkas
              </a>

              @php
                $missing = [];
                if (!$sub->file_kak) {
                    $missing[] = 'KAK (Kerangka Acuan Kerja) belum diunggah';
                }
                foreach($sub->kegiatans as $keg) {
                    if (!$keg->file_sk_panpel && !$keg->file_sk_tenaga_ahli) {
                        $missing[] = 'SK Panpel / SK Tenaga Ahli pada Kegiatan: <b>' . $keg->nama_kegiatan . '</b> masih kosong';
                    }
                }
                $missingJson = json_encode($missing);
              @endphp

              <form action="{{ route('sigap-spj.generate', $sub->id) }}" method="POST" class="form-generate" data-missing="{{ $missingJson }}">
                  @csrf
                  <button type="submit" class="px-3 py-1.5 rounded-lg border border-maroon text-maroon text-xs font-bold hover:bg-maroon hover:text-white flex items-center gap-1 shadow-sm transition-all">
                    Generate
                  </button>
              </form>

              <button type="button" 
                      onclick="copyShareLink('{{ route('spj.public.share', $sub->uuid) }}')" 
                      class="px-2.5 py-1.5 rounded-lg border border-gray-300 text-gray-600 bg-white hover:bg-gray-100 transition-colors shadow-sm" title="Salin Link Inspektorat">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
              </button>

            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-4 py-8 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center gap-2">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <span>Tidak ada data sub-kegiatan yang memenuhi kriteria pencarian / filter.</span>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  
  @if($subKegiatans->hasPages())
    <div class="p-4 border-t border-gray-200 bg-gray-50/50">
      {{ $subKegiatans->links() }}
    </div>
  @endif
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {

  // Tangkap semua form dengan class form-generate
  document.querySelectorAll('.form-generate').forEach(function(form) {
    form.addEventListener('submit', async function(e) {
      e.preventDefault(); // Hentikan submit bawaan browser

      const url = this.action;
      const csrfToken = this.querySelector('input[name="_token"]').value;
      const missingFields = JSON.parse(this.dataset.missing || '[]');

      // Fungsi eksekusi download menggunakan Fetch API
      const processDownload = async () => {
        // Tampilkan animasi loading
        Swal.fire({
          title: 'Membangun SPJ...',
          html: 'Mohon tunggu, sistem sedang menyatukan file PDF. <br>Jangan tutup halaman ini.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        try {
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken
            }
          });

          if (!response.ok) {
            throw new Error('Gagal melakukan generate laporan.');
          }

          // Dapatkan nama file dari header Content-Disposition (jika dikirim backend)
          let filename = 'Laporan_SPJ_Gabungan.pdf';
          const disposition = response.headers.get('Content-Disposition');
          if (disposition && disposition.indexOf('filename=') !== -1) {
            const matches = /filename="([^"]*)"/.exec(disposition);
            if (matches != null && matches[1]) filename = matches[1];
          }

          // Ubah response menjadi Blob PDF
          const blob = await response.blob();
          const downloadUrl = window.URL.createObjectURL(blob);
          
          // Buat elemen <a> bayangan untuk memicu download paksa
          const a = document.createElement('a');
          a.style.display = 'none';
          a.href = downloadUrl;
          a.download = filename;
          document.body.appendChild(a);
          a.click(); // Klik paksa

          // Bersihkan elemen & URL untuk menghemat memori
          window.URL.revokeObjectURL(downloadUrl);
          document.body.removeChild(a);

          // Tutup loading dan tampilkan animasi sukses
          Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Dokumen SPJ Anda berhasil diunduh.',
            timer: 2500,
            showConfirmButton: false
          });

        } catch (error) {
          Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: error.message
          });
        }
      };

      // Cek apakah ada field wajib yang belum diisi
      if (missingFields.length > 0) {
        
        // Buat list HTML untuk item yang kosong
        let htmlList = '<div class="text-left bg-red-50 p-3 rounded-lg border border-red-100 text-sm mb-3">';
        htmlList += '<p class="font-bold text-red-800 mb-2">Dokumen Wajib Berikut Belum Terisi:</p>';
        htmlList += '<ul class="list-disc pl-5 text-red-700 space-y-1">';
        missingFields.forEach(item => {
          htmlList += `<li>${item}</li>`;
        });
        htmlList += '</ul></div>';
        htmlList += '<p class="text-sm text-gray-700">Apakah Anda yakin tetap ingin meng-generate SPJ dalam keadaan tidak lengkap?</p>';

        // Tampilkan SWAL Konfirmasi
        Swal.fire({
          title: 'Berkas Belum Lengkap!',
          html: htmlList,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#7a2222', // maroon
          cancelButtonColor: '#6b7280', // gray
          confirmButtonText: 'Ya, Tetap Generate',
          cancelButtonText: 'Batal',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            processDownload();
          }
        });

      } else {
        // Jika lengkap, langsung download tanpa peringatan
        processDownload();
      }

    });
  });

});
</script>
<script>
function copyShareLink(url) {
    navigator.clipboard.writeText(url).then(function() {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Link Publik berhasil disalin!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }, function(err) {
        console.error('Async: Could not copy text: ', err);
    });
}
</script>