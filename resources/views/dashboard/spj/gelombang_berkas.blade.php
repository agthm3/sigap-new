@extends('layouts.app')

@section('content')
<div class="mb-6">
  <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
    <a href="{{ route('sigap-spj.show', $gelombang->kegiatan->spj_sub_kegiatan_id) }}" class="hover:text-maroon flex items-center gap-1 font-medium transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
      Kembali ke Detail Sub-Kegiatan
    </a>
  </div>
  
  <!-- Header Info (Responsif & Aman untuk Teks 500 Karakter) -->
  <div class="flex flex-col lg:flex-row lg:items-start justify-between bg-white border border-gray-200 p-5 rounded-2xl shadow-sm gap-5">
    <div class="min-w-0 flex-1">
      <span class="inline-block text-[11px] font-bold text-maroon uppercase tracking-wider bg-red-50 px-2.5 py-0.5 rounded-md mb-2 border border-red-100">
        {{ $gelombang->nama_gelombang }}
      </span>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-snug break-words">
        {{ $gelombang->kegiatan->nama_kegiatan }}
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 mt-1.5 leading-relaxed break-words">
        Sub-Kegiatan: <span class="font-semibold text-gray-700">{{ $gelombang->kegiatan->subKegiatan->nama_sub_kegiatan }}</span>
      </p>
    </div>
    
    <div class="text-xs text-gray-700 bg-gray-50 border border-gray-200 p-3.5 rounded-xl space-y-1.5 w-full lg:w-72 shrink-0">
      <div class="font-bold text-maroon uppercase tracking-wider text-[10px] pb-1 border-b border-gray-200">Rincian Pelaksanaan</div>
      <div class="flex items-center gap-1.5"><span>📅</span> <span>{{ \Carbon\Carbon::parse($gelombang->tanggal)->translatedFormat('l, d F Y') }}</span></div>
      <div class="flex items-center gap-1.5"><span>⏰</span> <span>{{ $gelombang->waktu }}</span></div>
      <div class="flex items-start gap-1.5"><span class="shrink-0">📍</span> <span class="break-words">{{ $gelombang->tempat }}</span></div>
    </div>
  </div>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 max-w-5xl">
  <form action="{{ route('sigap-spj.upload.berkas', $gelombang->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf

    @php
      $fields = [
          'file_sk_narasumber' => 'SK Narasumber',
          'file_sk_moderator'  => 'SK Moderator',
          'file_sp_narasumber' => 'SP Narasumber',
          'file_sp_moderator'  => 'SP Moderator',
          'file_sp_panitia'    => 'SP Panitia',
          'file_surat_undangan'=> 'Surat Undangan',
          'file_daftar_hadir'  => 'Daftar Hadir',
          'file_notulensi'     => 'Notulensi',
          'file_dokumentasi'   => 'Dokumentasi',
          'file_materi'        => 'Materi / PPT'
      ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      @foreach($fields as $name => $label)
      <div class="border border-gray-200 p-4 rounded-xl bg-gray-50/70 flex flex-col justify-between hover:border-gray-300 transition-colors">
        <div>
            <label class="block text-sm font-semibold text-gray-800 mb-2">{{ $label }}</label>
            
            @if($gelombang->$name)
            <div class="mb-3 flex items-center gap-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">SUDAH ADA FILE</span>
                <a href="{{ asset('storage/'.$gelombang->$name) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-medium inline-flex items-center gap-1">
                  Lihat PDF
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
            @endif

            <input type="file" name="{{ $name }}" accept="application/pdf" class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:font-semibold file:bg-white file:text-gray-700 hover:file:bg-gray-100 border border-gray-300 rounded-lg cursor-pointer bg-white">
        </div>
        
        @if($name === 'file_daftar_hadir')
            <div class="mt-4 pt-3 border-t border-gray-200">
                <button type="button" onclick="openModalDH()" class="w-full justify-center px-3 py-2 bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold rounded-lg hover:bg-blue-100 transition-colors flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Tarik dari SIGAP Daftar Hadir
                </button>
            </div>
        @endif
        @if($name === 'file_dokumentasi')
            <div class="mt-4 pt-3 border-t border-gray-200">
                <button type="button" onclick="openModalKinerja()" class="w-full justify-center px-3 py-2 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-lg hover:bg-amber-100 transition-colors flex items-center gap-2 shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Tarik Foto dari SIGAP Kinerja
                </button>
            </div>
        @endif
      </div>
      @endforeach
    </div>

    <div class="pt-4 border-t mt-6 text-right">
      <button type="submit" class="px-6 py-2.5 bg-maroon text-white font-semibold rounded-xl hover:bg-maroon-800 shadow-sm transition-colors">
        Simpan Semua Berkas
      </button>
    </div>
  </form>

    {{-- Modal SIGAP Daftar Hadir --}}
    <div id="modalDH" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModalDH()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900" id="modal-title">Tarik Daftar Hadir</h3>
                        <button onclick="closeModalDH()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        <div class="relative mb-4">
                            <input type="text" id="searchDH" placeholder="Ketik nama kegiatan daftar hadir..." 
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-maroon focus:border-maroon shadow-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"></path></svg>
                        </div>

                        <div id="resultsDH" class="space-y-3 max-h-80 overflow-y-auto scrollbar-thin pr-1">
                            <div class="text-center text-sm text-gray-500 py-4">Ketik minimal 3 karakter untuk mencari...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal SIGAP Kinerja --}}
    <div id="modalKinerja" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" onclick="closeModalKinerja()"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-base font-bold text-gray-900">Tarik Dokumentasi Kinerja</h3>
                        <button onclick="closeModalKinerja()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <div class="relative mb-4">
                            <input type="text" id="searchKinerja" placeholder="Ketik judul kegiatan kinerja..." 
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-maroon focus:border-maroon shadow-sm">
                            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"></path></svg>
                        </div>
                        <div id="resultsKinerja" class="space-y-3 max-h-80 overflow-y-auto scrollbar-thin pr-1"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModalDH() {
        const modalDH = document.getElementById('modalDH');
        const inputSearch = document.getElementById('searchDH');
        const resultsContainer = document.getElementById('resultsDH');

        if (!modalDH) return;

        modalDH.classList.remove('hidden');
        inputSearch.value = '';
        resultsContainer.innerHTML = '<div class="text-center text-sm text-gray-500 py-4">Memuat 5 daftar hadir terbaru...</div>';
        
        setTimeout(() => { inputSearch.focus(); }, 100);

        fetch(`{{ route('sigap-spj.search.daftar-hadir', $gelombang->id) }}`)
            .then(res => res.json())
            .then(data => renderDHResults(data, resultsContainer, true))
            .catch(() => {
                resultsContainer.innerHTML = '<div class="text-center text-sm text-red-500 py-4">Gagal memuat daftar hadir terbaru.</div>';
            });
    }

    function closeModalDH() {
        const modalDH = document.getElementById('modalDH');
        if (modalDH) modalDH.classList.add('hidden');
    }

    function renderDHResults(data, container, isRecent) {
        if (data.length === 0) {
            container.innerHTML = '<div class="text-center text-sm text-amber-600 py-4">Belum ada kegiatan daftar hadir dengan status "Selesai".</div>';
            return;
        }

        let html = isRecent ? '<div class="text-[11px] font-bold text-maroon uppercase tracking-wider mb-2 px-1">✨ 5 Daftar Hadir Terbaru:</div>' : '';
        data.forEach(item => {
            html += `
            <div class="flex items-start justify-between p-3.5 border border-gray-200 rounded-xl hover:border-maroon/50 hover:bg-gray-50 transition-colors text-left bg-white gap-3">
                <div class="min-w-0 flex-1">
                    <h4 class="font-bold text-gray-900 text-sm mb-1.5 leading-snug break-words">${item.nama_kegiatan}</h4>
                    <div class="text-xs text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>📅 ${item.hari_tanggal}</span>
                        <span>👥 ${item.peserta_count} Peserta</span>
                    </div>
                </div>
                <button type="button" onclick="importDH('${item.uuid}')" class="px-3.5 py-1.5 bg-maroon text-white text-xs font-bold rounded-lg hover:bg-maroon-800 shadow-sm shrink-0">
                    Pilih & Tarik
                </button>
            </div>`;
        });
        container.innerHTML = html;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const inputSearch = document.getElementById('searchDH');
        const resultsContainer = document.getElementById('resultsDH');
        let searchTimeout = null;

        if (inputSearch) {
            inputSearch.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value;

                if (query.length < 3) {
                    resultsContainer.innerHTML = '<div class="text-center text-sm text-gray-500 py-4">Ketik minimal 3 karakter untuk mencari...</div>';
                    return;
                }

                resultsContainer.innerHTML = '<div class="text-center text-sm text-gray-500 py-4">Mencari data...</div>';

                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('sigap-spj.search.daftar-hadir', $gelombang->id) }}?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => renderDHResults(data, resultsContainer, false))
                        .catch(() => {
                            resultsContainer.innerHTML = '<div class="text-center text-sm text-red-500 py-4">Gagal mengambil data.</div>';
                        });
                }, 500);
            });
        }
    });

    function importDH(uuid) {
        closeModalDH();
        
        Swal.fire({
            title: 'Sedang Menarik Data...',
            html: 'Sistem sedang memproses dokumen PDF daftar hadir, mohon tunggu.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`{{ route('sigap-spj.import.daftar-hadir', $gelombang->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ dh_uuid: uuid })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
                });
            } else {
                throw new Error(res.message);
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: err.message });
        });
    }

    // ==========================================
    // LOGIKA MODAL SIGAP KINERJA
    // ==========================================
    function openModalKinerja() {
        const modal = document.getElementById('modalKinerja');
        const input = document.getElementById('searchKinerja');
        const results = document.getElementById('resultsKinerja');

        if (!modal) return;
        modal.classList.remove('hidden');
        input.value = '';
        results.innerHTML = '<div class="text-center text-sm text-gray-500 py-4">Memuat 5 data kinerja terbaru...</div>';
        
        setTimeout(() => { input.focus(); }, 100);

        fetch(`{{ route('sigap-spj.search.kinerja', $gelombang->id) }}`)
            .then(res => res.json())
            .then(data => renderKinerjaResults(data, results, true))
            .catch(() => results.innerHTML = '<div class="text-center text-sm text-red-500 py-4">Gagal memuat data.</div>');
    }

    function closeModalKinerja() {
        const modal = document.getElementById('modalKinerja');
        if (modal) modal.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchKinerja');
        const results = document.getElementById('resultsKinerja');
        let timer = null;

        if (!input) return;

        input.addEventListener('input', function() {
            clearTimeout(timer);
            const query = this.value;

            if (query.length < 3) {
                results.innerHTML = '<div class="text-center text-sm text-gray-500 py-4">Ketik minimal 3 karakter untuk mencari...</div>';
                return;
            }

            results.innerHTML = '<div class="text-center text-sm text-gray-500 py-4">Mencari data kinerja...</div>';

            timer = setTimeout(() => {
                fetch(`{{ route('sigap-spj.search.kinerja', $gelombang->id) }}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => renderKinerjaResults(data, results, false))
                    .catch(() => results.innerHTML = '<div class="text-center text-sm text-red-500 py-4">Gagal mengambil data.</div>');
            }, 500);
        });
    });

    function renderKinerjaResults(data, container, isRecent) {
        if (data.length === 0) {
            container.innerHTML = '<div class="text-center text-sm text-amber-600 py-4">Data kinerja tidak ditemukan.</div>';
            return;
        }

        let html = isRecent ? '<div class="text-[11px] font-bold text-amber-700 uppercase tracking-wider mb-2 px-1">✨ 5 Kinerja Terbaru:</div>' : '';
        data.forEach(item => {
            html += `
            <div class="flex items-start justify-between p-3.5 border border-gray-200 rounded-xl hover:border-amber-500/50 hover:bg-amber-50 transition-colors text-left bg-white gap-3">
                <div class="min-w-0 flex-1">
                    <h4 class="font-bold text-gray-900 text-sm mb-1.5 leading-snug break-words" title="${item.title}">${item.title}</h4>
                    <div class="text-xs text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span>📅 ${item.activity_date}</span>
                        <span class="px-2 py-0.5 bg-gray-100 rounded text-[10px]">${item.category}</span>
                    </div>
                </div>
                <button type="button" onclick="importKinerja('${item.id}')" class="px-3.5 py-1.5 bg-amber-600 text-white text-xs font-bold rounded-lg hover:bg-amber-700 shadow-sm shrink-0">
                    Jadikan PDF
                </button>
            </div>`;
        });
        container.innerHTML = html;
    }

    function importKinerja(id) {
        closeModalKinerja();
        
        Swal.fire({
            title: 'Menyusun Dokumentasi...',
            html: 'Sistem sedang menarik foto dan menatanya ke dalam lembar PDF F4.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`{{ route('sigap-spj.import.kinerja', $gelombang->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ kinerja_id: id })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                Swal.fire({ icon: 'success', title: 'Selesai!', text: res.message, timer: 2000, showConfirmButton: false })
                .then(() => window.location.reload());
            } else {
                throw new Error(res.message);
            }
        })
        .catch(err => Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: err.message }));
    }
</script>
@endpush