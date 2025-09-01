@extends('layouts.page')

@section('content')
      <!-- Search Bar -->
  <section class="bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900">
    <div class="max-w-7xl mx-auto px-4 py-8 sm:py-10">
      <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-6">
        <form class="grid sm:grid-cols-5 gap-3" onsubmit="event.preventDefault()">
          <div class="sm:col-span-2">
            <label class="text-sm font-semibold text-gray-700">Kata Kunci</label>
            <input type="search" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Contoh: SK Sekretariat A, Laporan 2024, KTP…" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Kategori</label>
            <select class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <option>Semua</option><option>Surat Keputusan</option><option>Laporan</option><option>Formulir</option><option>Privasi (KK/KTP)</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Pihak Terkait</label>
            <input type="text" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Sekretariat A, Bidang X…" />
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Tahun</label>
            <select class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
              <option>Semua</option><option>2025</option><option>2024</option><option>2023</option><option>2022</option>
            </select>
          </div>
          <div class="sm:col-span-5 flex flex-wrap gap-3 pt-1">
            <button class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/></svg>
              Cari Dokumen
            </button>
            <button type="reset" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Reset</button>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Results header -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="py-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-extrabold text-maroon">Hasil Pencarian</h1>
        <p class="text-sm text-gray-600 mt-1">Menemukan {{ $documents->count() }} dokumen.</p>
      </div>
      <div class="flex items-center gap-3">
        <label class="text-sm text-gray-600">Urutkan</label>
        <select class="rounded-md border-gray-300 focus:border-maroon focus:ring-maroon text-sm">
          <option>Terbaru</option>
          <option>Terpopuler</option>
          <option>Judul (A-Z)</option>
        </select>
      </div>
    </div>
  </section>

  <!-- Results list -->
  <section class="max-w-7xl mx-auto px-4 pb-12">
    <ul class="space-y-4">

      @foreach ($documents as $item)
              <!-- ITEM -->
        <li class="border border-gray-200 rounded-xl overflow-hidden">
            <div class="flex flex-col sm:flex-row">
            <div class="sm:w-48 shrink-0">
                <img class="w-full h-40 sm:h-full object-cover" src="https://images.unsplash.com/photo-1516387938699-a93567ec168e?q=80&w=1200&auto=format&fit=crop" alt="Thumbnail dokumen">
            </div>
            <div class="flex-1 p-4 sm:p-6">
                <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-maroon/10 text-maroon">Surat Keputusan</span>
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-700">Tahun: 2025</span>
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-700">Pihak: Sekretariat A</span>
                <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-emerald-50 text-emerald-700">Publik</span>
                </div>
                <h3 class="mt-2 text-lg font-semibold text-gray-900">
               {{$item->title}}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                <span class="font-semibold">Alias:</span> SK-TimKerja-2025-01
                </p>
                <p class="text-sm text-gray-700 mt-2 line-clamp-2">
                {{ $item->description }}
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                <a href="single-page.html" class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
                    View
                </a>
                <a href="#download" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-maroon text-white hover:bg-maroon-800 transition text-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
                    Download
                </a>
                </div>
            </div>
            </div>
        </li>
      @endforeach

      <!-- ITEM (PRIVASI) -->
      <li class="border border-gray-200 rounded-xl overflow-hidden">
        <div class="flex flex-col sm:flex-row">
          <div class="sm:w-48 shrink-0">
            <img class="w-full h-40 sm:h-full object-cover" src="https://images.unsplash.com/photo-1516542076529-1ea3854896e1?q=80&w=1200&auto=format&fit=crop" alt="Thumbnail dokumen">
          </div>
          <div class="flex-1 p-4 sm:p-6">
            <div class="flex flex-wrap items-center gap-2">
              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-maroon/10 text-maroon">Privasi (KK/KTP)</span>
              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-700">Tahun: 2024</span>
              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-700">Pihak: Kepegawaian</span>
              <span class="inline-flex items-center px-2 py-0.5 text-[11px] rounded bg-amber-50 text-amber-700">Akses Terkendali</span>
            </div>
            <h3 class="mt-2 text-lg font-semibold text-gray-900">
              KTP Pegawai — (Contoh) Andi Rahman
            </h3>
            <p class="text-sm text-gray-600 mt-1">
              <span class="font-semibold">Alias:</span> KTP-AR-2024
            </p>
            <p class="text-sm text-gray-700 mt-2 line-clamp-2">
              Dokumen identitas pegawai untuk keperluan administrasi internal. Akses membutuhkan otorisasi.
            </p>
            <div class="mt-4 flex flex-wrap gap-2">
              <a href="#view-controlled" class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-amber-400 text-amber-700 hover:bg-amber-50 transition text-sm">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 12c2.5 0 4.5-2 4.5-4.5S14.5 3 12 3 7.5 5 7.5 7.5 9.5 12 12 12z"/><path d="M19 21a7 7 0 0 0-14 0"/></svg>
                View (Minta Akses)
              </a>
              <a href="#download-controlled" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-200 text-gray-600 cursor-not-allowed text-sm" aria-disabled="true" title="Unduh membutuhkan hak akses">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
                Download
              </a>
            </div>
          </div>
        </div>
      </li>


      <!-- Tambah item lain sesuai kebutuhan... -->

    </ul>

    <!-- Pagination -->
    <div class="mt-8 flex items-center justify-between">
      <p class="text-sm text-gray-600">Menampilkan 1–10 dari 48</p>
      <nav class="inline-flex overflow-hidden rounded-md border border-gray-200">
        <a href="#prev" class="px-3 py-2 text-sm hover:bg-gray-50">Sebelumnya</a>
        <span class="px-3 py-2 text-sm bg-maroon text-white">1</span>
        <a href="#p2" class="px-3 py-2 text-sm hover:bg-gray-50">2</a>
        <a href="#p3" class="px-3 py-2 text-sm hover:bg-gray-50">3</a>
        <a href="#next" class="px-3 py-2 text-sm hover:bg-gray-50">Berikutnya</a>
      </nav>
    </div>
  </section>

@endsection