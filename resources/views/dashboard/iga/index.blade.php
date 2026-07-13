@extends('layouts.app')

@section('content')
<!-- BUNGKUS DENGAN ALPINE.JS UNTUK MODAL -->
<div x-data="{ showModal: false }">

    <!-- Header Section -->
    <section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
      <div>
        <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
          Kredensial Akun <span class="text-[#002B4C]">IGA KEMENDAGRI</span>
        </h1>
        <p class="text-sm text-gray-600 mt-0.5">
          Manajemen data akses Innovative Government Award untuk SKPD dan UPT Kota Makassar.
        </p>
      </div>

      <div class="flex items-center gap-2 flex-wrap">
        <a href="https://indeks.inovasi.bskdn.kemendagri.go.id/login" 
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#002B4C] text-white text-sm font-semibold hover:bg-[#001f38] transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
          </svg>
          Buka Portal IGA
        </a>

        <!-- TOMBOL TAMBAH AKUN (HANYA ADMIN & VERIFICATOR) -->
        @hasanyrole('admin|verificator')
        <button type="button" @click="showModal = true"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border-2 border-[#002B4C] text-[#002B4C] text-sm font-semibold hover:bg-[#002B4C] hover:text-white transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Tambah Akun
        </button>
        @endhasanyrole
      </div>
    </section>

    <!-- Banner Edukasi Perbedaan & Alur SIGAP vs IGA -->
    <div class="mt-4 p-5 rounded-2xl bg-gradient-to-r from-slate-50 to-blue-50 border border-blue-100 shadow-sm flex flex-col md:flex-row gap-4 items-start">
      <div class="p-3 bg-[#002B4C] text-white rounded-xl shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
      </div>
      <div class="space-y-2">
        <h3 class="text-base font-bold text-slate-900">Alur Penginputan & Pusat Backup Inovasi</h3>
        <p class="text-sm text-slate-600 leading-relaxed">
          <strong>Alur Sistem:</strong> Setiap Dinas, Badan, UPT, maupun Personal diwajibkan melakukan pengisian melalui platform <strong>SIGAP INOVASI</strong> terlebih dahulu untuk proses reviu, kurasi, dan verifikasi oleh tim internal Kota Makassar. Hanya inovasi yang berstatus aman dan lolos verifikasi yang akan diunggah ke sistem nasional <strong>IGA BSKDN Kemendagri</strong>.
        </p>
        <p class="text-sm text-slate-600 leading-relaxed">
          <strong>Fungsi Backup Permanen:</strong> Mengingat server pusat IGA terkadang mengalami kendala teknis serta menerapkan kebijakan retensi pembersihan otomatis data inovasi lama, <strong>SIGAP INOVASI</strong> bertindak sebagai pangkalan data sekunder permanen Pemerintah Kota Makassar untuk memastikan tidak ada rekam jejak inovasi daerah yang hilang.
        </p>
      </div>
    </div>

    <!-- SECTION BARU: KONTAK BANTUAN WHATSAPP (DENGAN TEMPLATE PESAN DEFAULT) -->
    <div class="mt-4 p-5 rounded-2xl border border-amber-200 bg-amber-50/60 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex gap-3 items-start sm:items-center">
        <div class="p-2.5 bg-amber-100 text-amber-900 rounded-xl shrink-0">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <h4 class="text-sm font-bold text-gray-900">Akun IGA Dinas atau UPT Anda Tidak Ditemukan?</h4>
          <p class="text-xs text-gray-600 mt-0.5">Jika instansi Anda belum terdaftar dalam pangkalan data, silakan hubungi tim fasilitator BRIDA Makassar melalui jalur koordinasi berikut:</p>
        </div>
      </div>

      <!-- Tombol WhatsApp Akses Langsung -->
      <div class="flex flex-wrap items-center gap-2 shrink-0">
        <!-- Pak Ikrom -->
        <a href="https://wa.me/6285255245231?text=tabe%20pak%2C%20saya%20dari%20%5BISI%20NAMA%20DINAS%20ANDA%5D%0A%0Auntuk%20kantor%2Fdinas%20saya%20belum%20ada%20akun%20IGAnya%2C%20mohon%20untuk%20dibuatkan%20atau%20dicekkan%20apakah%20ada%20akun%20untuk%20dinas%2/kantor%20saya%20pak%20%F0%9F%99%8F" 
           target="_blank" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397 0 11.966 0c3.178.001 6.165 1.24 8.413 3.491 2.247 2.253 3.484 5.244 3.481 8.425-.003 6.616-5.34 11.965-11.909 11.965-2.005-.001-3.973-.507-5.729-1.476L0 24zm6.59-4.846c1.62.962 3.204 1.47 4.778 1.471 5.42 0 9.83-4.414 9.833-9.84.002-2.628-1.02-5.101-2.877-6.958C16.466 1.97 13.999.95 11.379.95c-5.424 0-9.832 4.412-9.835 9.84-.001 1.77.477 3.497 1.385 5.085L1.892 22.18l6.305-1.654z"/>
          </svg>
          Pak Ikrom (Fasilitator)
        </a>

        <!-- Pak Budi -->
        <a href="https://wa.me/6282343447786?text=tabe%20pak%2C%20saya%20dari%20%5BISI%20NAMA%20DINAS%20ANDA%5D%0A%0Auntuk%20kantor%2Fdinas%20saya%20belum%20ada%20akun%20IGAnya%2C%20mohon%20untuk%20dibuatkan%20atau%20dicekkan%20apakah%20ada%20akun%20untuk%20dinas%2/kantor%20saya%20pak%20%F0%9F%99%8F" 
           target="_blank" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition-colors shadow-sm">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397 0 11.966 0c3.178.001 6.165 1.24 8.413 3.491 2.247 2.253 3.484 5.244 3.481 8.425-.003 6.616-5.34 11.965-11.909 11.965-2.005-.001-3.973-.507-5.729-1.476L0 24zm6.59-4.846c1.62.962 3.204 1.47 4.778 1.471 5.42 0 9.83-4.414 9.833-9.84.002-2.628-1.02-5.101-2.877-6.958C16.466 1.97 13.999.95 11.379.95c-5.424 0-9.832 4.412-9.835 9.84-.001 1.77.477 3.497 1.385 5.085L1.892 22.18l6.305-1.654z"/>
          </svg>
          Pak Budi (Bidang Inovasi)
        </a>
      </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
      <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Total Akun Terdaftar</p>
        <h3 class="text-2xl font-extrabold text-gray-900">{{ $akuns->total() }}</h3>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Tipe Organisasi (OPD)</p>
        <h3 class="text-2xl font-extrabold text-[#002B4C]">
          {{ $totalOpd }}
        </h3>
      </div>
      <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Tipe Unit Pelaksana (UPT)</p>
        <h3 class="text-2xl font-extrabold text-[#002B4C]">
          {{ $totalUpt }}
        </h3>
      </div>
    </div>

    <!-- Tabel Kredensial Akun -->
<div class="rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm mt-4">
      <div class="px-4 py-3 border-b bg-gray-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
          <h2 class="font-semibold text-gray-900">Daftar Akun IGA Kemendagri</h2>
          <span class="text-[11px] px-2 py-0.5 bg-amber-50 text-amber-800 border border-amber-200 rounded-full font-medium inline-block mt-0.5">
            Internal Pemerintah Kota Makassar
          </span>
        </div>
        
        <form action="{{ route('sigap-iga.index') }}" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
          <div class="w-full sm:w-40">
            <select name="role" onchange="this.form.submit()" class="w-full rounded-xl border-gray-300 text-xs focus:ring-[#002B4C] focus:border-[#002B4C] py-2">
              <option value="">Semua Kategori</option>
              <option value="opd" {{ request('role') == 'opd' ? 'selected' : '' }}>OPD</option>
              <option value="upt" {{ request('role') == 'upt' ? 'selected' : '' }}>UPT</option>
            </select>
          </div>

          <div class="relative w-full sm:w-64">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama OPD atau username..." 
                   class="w-full rounded-xl border-gray-300 text-xs focus:ring-[#002B4C] focus:border-[#002B4C] py-2 pl-8 pr-4">
            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
              </svg>
            </div>
          </div>

          @if(request('search') || request('role'))
            <a href="{{ route('sigap-iga.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-medium transition-colors">
              Reset
            </a>
          @endif
        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-600">
            <tr>
              <th class="px-4 py-3 text-left w-16">#</th>
              <th class="px-4 py-3 text-left">Role</th>
              <th class="px-4 py-3 text-left">Daerah</th>
              <th class="px-4 py-3 text-left">OPD / Instansi</th>
              <th class="px-4 py-3 text-left">Username IGA</th>
              <th class="px-4 py-3 text-left w-48">Aksi Keamanan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse($akuns as $index => $item)
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-4 py-3 font-medium text-gray-900">
                  {{ $akuns->firstItem() + $index }}
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold border uppercase
                    {{ $item->role === 'opd' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-purple-50 border-purple-200 text-purple-700' }}">
                    {{ $item->role }}
                  </span>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $item->daerah }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $item->opd }}</td>
                <td class="px-4 py-3">
                  <code class="px-2 py-1 bg-slate-100 rounded text-xs text-slate-700 border border-slate-200">
                    {{ $item->username }}
                  </code>
                </td>
                <td class="px-4 py-3">
                  <button type="button"
                          class="btn-view-password inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#002B4C] text-[#002B4C] text-xs font-semibold hover:bg-[#002B4C] hover:text-white transition-all"
                          data-opd="{{ $item->opd }}"
                          data-username="{{ $item->username }}"
                          data-password="{{ $item->password_raw }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Lihat Password
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                  Data tidak ditemukan atau kata kunci pencarian tidak cocok.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-4">
      {{ $akuns->links() }}
    </div>

    <!-- ============================================== -->
    <!-- MODAL TAMBAH AKUN (HANYA MUNCUL JIKA showModal = true) -->
    <!-- ============================================== -->
    <div x-show="showModal" 
         x-transition.opacity
         style="display: none;" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
         
        <!-- Latar belakang gelap -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showModal" 
                 @click="showModal = false"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                 aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Box Modal -->
            <div x-show="showModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-gray-200">
                
                <form action="{{ route('sigap-iga.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-center gap-3 mb-5 border-b pb-3">
                            <div class="w-10 h-10 rounded-full bg-[#002B4C]/10 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-[#002B4C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                                Tambah Data Akun IGA
                            </h3>
                        </div>

                        <div class="space-y-4">
                            <!-- Role -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Role / Kategori</label>
                                <select name="role" required class="w-full rounded-lg border-gray-300 focus:ring-[#002B4C] focus:border-[#002B4C] sm:text-sm p-2.5">
                                    <option value="opd">OPD (Organisasi Perangkat Daerah)</option>
                                    <option value="upt">UPT (Unit Pelaksana Teknis)</option>
                                </select>
                            </div>

                            <!-- Daerah -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Daerah</label>
                                <input type="text" name="daerah" value="Kota Makassar" required class="w-full rounded-lg border-gray-300 focus:ring-[#002B4C] focus:border-[#002B4C] sm:text-sm p-2.5" placeholder="Contoh: Kota Makassar">
                            </div>

                            <!-- OPD -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama OPD / Instansi</label>
                                <input type="text" name="opd" required class="w-full rounded-lg border-gray-300 focus:ring-[#002B4C] focus:border-[#002B4C] sm:text-sm p-2.5" placeholder="Contoh: Dinas Pendidikan">
                            </div>

                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Username Akun IGA</label>
                                <input type="text" name="username" required class="w-full rounded-lg border-gray-300 focus:ring-[#002B4C] focus:border-[#002B4C] sm:text-sm p-2.5" placeholder="Username resmi IGA">
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Password Asli (Raw)</label>
                                <input type="text" name="password_raw" required class="w-full rounded-lg border-gray-300 focus:ring-[#002B4C] focus:border-[#002B4C] sm:text-sm p-2.5 font-mono" placeholder="Password tanpa enkripsi">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2.5 bg-[#002B4C] text-sm font-semibold text-white hover:bg-[#001f38] focus:outline-none sm:w-auto">
                            Simpan Akun
                        </button>
                        <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-4 py-2.5 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  
  document.querySelectorAll('.btn-view-password').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const opd = this.dataset.opd;
      const username = this.dataset.username;
      const password = this.dataset.password;

      Swal.fire({
        title: 'Pernyataan Batasan Akses',
        html: `<div class="text-left p-2 border border-red-100 bg-red-50 rounded-lg text-xs text-red-800 space-y-2">
                <p><strong>PERINGATAN:</strong> Anda akan melihat kredensial resmi milik <b>${opd}</b>.</p>
                <p>Dilarang keras menyalahgunakan akun ini, mengubah kredensial tanpa koordinasi dengan BRIDA, atau mendistribusikannya kepada pihak eksternal yang tidak berwenang!</p>
               </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#002B4C',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Saya Paham, Tampilkan',
        cancelButtonText: 'Batal',
        focusCancel: true
      }).then((result) => {
        if (result.isConfirmed) {
          Swal.fire({
            title: 'Kredensial Akun IGA',
            html: `<div class="text-left space-y-3 p-3 bg-slate-50 border rounded-lg font-mono text-sm">
                    <div><span class="text-slate-500 block text-xs">Username:</span><b>${username}</b></div>
                    <div><span class="text-slate-500 block text-xs">Password:</span><span class="text-emerald-700 font-bold tracking-wider">${password}</span></div>
                   </div>`,
            icon: 'success',
            confirmButtonColor: '#002B4C',
            confirmButtonText: 'Tutup'
          });
        }
      });
    });
  });

});
</script>
@endpush