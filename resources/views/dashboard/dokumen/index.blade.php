@extends('layouts.app')

@section('content')
      <!-- Page header -->
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <!-- Kiri -->
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">SIGAP Dokumen</h1>
      <p class="text-sm text-gray-600 mt-1">
        Kelola arsip dokumen resmi BRIDA: SK, Laporan, Formulir, dan Privasi.
      </p>
    </div>

    <!-- Kanan -->
    <div>
      <button id="btnTambah"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 5v14M5 12h14"/>
        </svg>
        Tambah Dokumen
      </button>
    </div>
  </div>
</section>


  @if ($errors->any())
  <div class="text-sm text-red-600">
    <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
  </div>
  @endif

  <!-- Filter & Search -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-5 gap-3" method="GET" action="{{ route('sigap-dokumen.index') }}">
        <div class="lg:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Kata Kunci</label>
          <input id="q" name="q" type="search" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Judul / Alias / Kata kunci…" value="{{ request('q') }}">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Kategori</label>
          <select name="category" id="f_kat" class="mt-1.5 w-full rounded-lg border border-black-300 p-2 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach (['Surat Keputusan', 'Laporan', 'Formulir', 'Privasi'] as $item)
              <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Tahun</label>
          <select name="year" id="f_th" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @for ($y = now()->year; $y>= now()->year-10; $y--) 
              <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
            @endfor
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Akses</label>
          <select name="sensitivity" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option value="public"  @selected(request('sensitivity')==='public')>Publik</option>
            <option value="private" @selected(request('sensitivity')==='private')>Akses Terkendali</option>
          </select>
      </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Pihak Terkait</label>
          <input name="stakeholder" id="f_pihak" type="text" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Sekretariat A / Bidang X">
        </div>
        <div class="lg:col-span-5 flex gap-3 pt-1">
          <button id="btnCari" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">Cari</button>
          <a href='{{ route('sigap-dokumen.index') }}' class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" >Reset</a>
        </div>
      </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Urutkan</label>
          <select id="sort" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="terbaru">Terbaru</option>
            <option value="judul">Judul (A-Z)</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead>
            <tr class="text-left border-b">
              <th class="px-4 py-3">Dokumen</th>
              <th class="px-4 py-3">Alias</th>
              <th class="px-4 py-3">Kategori</th>
              <th class="px-4 py-3">Tahun</th>
              <th class="px-4 py-3">Pihak Terkait</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody id="tbody" class="divide-y">
            <!-- Baris contoh (akan ditimpa JS saat tambah dokumen) -->
            @forelse ($docs as $item)
            {{-- @dd($item) --}}
            <tr>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <!-- ganti dummy image menjadi gambar dokumen -->
                  @if (!empty($item->thumb_path))
                    <img class="w-12 h-12 rounded object-cover" src="{{ asset('storage/'.$item->thumb_path) }}" alt="">
                  @else
                    <img class="w-12 h-12 rounded object-cover" src="{{ asset('images/thumb/document-icon.png') }}" alt="">
                  @endif
                  <div>
                    <p class="font-medium text-gray-900">{{ $item->title }}</p>
                    <p class="text-xs text-gray-600 line-clamp-1">{{ Str::limit($item->description, 30) }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3">{{ $item->alias }}</td>
              <td class="px-4 py-3">{{ $item->category }}</td>
              <td class="px-4 py-3">{{ $item->year }}</td>
              <td class="px-4 py-3">{{ $item->stakeholder ?? '-' }}</td>
              <td>
                @if($item->sensitivity === 'public')
                  <span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Publik</span>
                @else
                  <span class="px-2 py-0.5 rounded text-xs bg-red-50 text-red-700">Privat</span>
                @endif
              </td>
              {{-- <td class="px-4 py-3"><span class="px-2 py-0.5 rounded text-xs bg-emerald-50 text-emerald-700">Publik</span></td> --}}
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-2">
                  <a href="{{ route('sigap-dokumen.show', $item->id) }}" target="_blank" class="px-3 py-1.5 rounded-md border border-maroon text-maroon hover:bg-maroon hover:text-white transition">View</a>
                  <a href="{{ route('sigap-dokumen.download', $item->id) }}" target="_blank" class="px-3 py-1.5 rounded-md bg-maroon text-white hover:bg-maroon-800 transition">Download</a>
                  @hasrole('admin')
                  <a href="{{ route('sigap-dokumen.edit', $item->id) }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</a>
                  <button type="button"
                          class="px-3 py-1.5 rounded-md border hover:bg-gray-50 text-red-900 border-red-900"
                          onclick="confirmHapus({{ $item->id }}, @js($item->title))">
                    Hapus
                  </button>
                  @endhasrole
                  {{-- <button class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Hapus</button> --}}
                  <form id="form-delete-{{ $item->id }}" action="{{ route('sigap-dokumen.destroy', $item->id) }}" method="POST" >
                    @csrf
                    @method('DELETE')
                  </form>
                </div>
              </td>
            </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                  Tidak ada berkas, Ganteng 😔
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 flex items-center justify-between">
        <p class="text-sm text-gray-600">Menampilkan 1–3 dari 3</p>
        <nav class="inline-flex overflow-hidden rounded-md border border-gray-200">
          <a href="#prev" class="px-3 py-2 text-sm hover:bg-gray-50">Sebelumnya</a>
          <span class="px-3 py-2 text-sm bg-maroon text-white">1</span>
          <a href="#next" class="px-3 py-2 text-sm hover:bg-gray-50">Berikutnya</a>
        </nav>
      </div>
    </div>
  </section>

  <!-- Modal Tambah Dokumen -->
  <div id="modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="fixed inset-0 bg-black/40" onclick="closeModal()"></div>
    <div class="relative z-10 mx-auto max-w-3xl px-4 py-8">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-maroon via-maroon-800 to-maroon-900">
          <h2 class="text-white text-lg font-bold">Tambah Dokumen 📃</h2>
          <p class="text-white/80 text-xs mt-0.5">Lengkapi metadata dan unggah file.</p>
        </div>

        <form id="formTambah" 
        class="p-5 grid sm:grid-cols-2 gap-4" 
        {{-- onsubmit="event.preventDefault(); tambahDokumen();" --}}
        method="POST"
        action="{{ route('sigap-dokumen.store') }}"
        enctype="multipart/form-data"
        >
        @csrf
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul</span>
            <input id="d_judul" name="title" type="text" required class="mt-1.5 w-full rounded border  border-black-300 p-2 focus:border-maroon focus:ring-maroon" placeholder="Judul dokumen">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Alias</span>
            <input id="d_alias" name="alias" type="text" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon" placeholder="SK-TimKerja-2025-01">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Kategori</span>
            <select id="d_kat" name="category" required class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon">
              <option value="">Pilih…</option>
              <option>Surat Keputusan</option>
              <option>Laporan</option>
              <option>Formulir</option>
              <option>Privasi</option>
              <option>Dokumen</option>
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Tahun</span>
            <select id="d_th" name="year" required class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon">
              <option value="2025">2025</option>
              <option value="2024">2024</option>
              <option value="2023">2023</option>
              <option value="2023">2022</option>
              <option value="2023">2021</option>
              <option value="2023">2020</option>
              <option value="2023">2019</option>
              <option value="2023">2018</option>
              <option value="2023">2017</option>
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Pihak Terkait</span>
            <input id="d_pihak" name="stakeholder" type="text" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon" placeholder="Sekretariat A / Bidang X">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Status Akses</span>
            <select id="d_status" name="sensitivity" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon">
              <option value="public">Publik</option>
              <option value="private">Akses Terkendali</option>
            </select>
          </label>

          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Deskripsi</span>
             <div class="sm:col-span-2 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Ketik deskripsi atau latar belakang dari dokumen yang anda masukkan agar lebih mudah ditemukan.</span>
            </div>
          </div>
            <textarea id="d_desc" name="description" rows="3" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon" placeholder="Ringkasan singkat isi dokumen…"></textarea>
          </label>

          <div class="sm:col-span-2 grid sm:grid-cols-2 gap-4">
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">File (Maksimal 20 Mb)</span>
              <input id="d_file" name="file" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon"   onchange="validateFileSize(this)">
              <p class="text-[12px] text-gray-500 mt-1">Utamakan PDF. Dokumen privasi wajib disimpan sebagai private.</p>
            </label>
            <label class="block">
              <span class="text-sm font-semibold text-gray-700">Thumbnail (opsional)</span>
              <input id="d_thumb" name="thumb" type="file" accept=".jpg,.jpeg,.png" class="mt-1.5 w-full rounded border border-black-300 p-2 focus:ring-maroon">
            </label>
          </div>

          <div class="sm:col-span-2 flex items-center justify-between text-xs text-gray-600">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-amber-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
              <span>Dokumen privasi akan meminta kode/alasan saat diakses.</span>
            </div>
            <a href="#sop" class="text-maroon hover:underline">Lihat SOP</a>
          </div>

          <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50" onclick="closeModal()">Batal</button>
            <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection


@push('scripts')
  {{-- CDN SweetAlert cukup di sini (khusus halaman ini) --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    // Modal controls
    const modal = document.getElementById('modal');
    const btnTambah = document.getElementById('btnTambah');
    if (btnTambah && modal) {
      btnTambah.addEventListener('click', () => { modal.classList.remove('hidden'); });
    }
    function closeModal(){ modal?.classList.add('hidden'); }

    // SweetAlert delete
    function confirmHapus(id, title){
      Swal.fire({
        title: 'Hapus dokumen?',
        html: 'Dokumen: <b>'+title+'</b>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
      }).then((res) => {
        if(res.isConfirmed){
          document.getElementById('form-delete-'+id).submit();
        }
      });
    }
  </script>

  {{-- Flash message via SweetAlert --}}
  @if(session('success'))
    <script>
      Swal.fire({ icon:'success', title:'Berhasil', text:@json(session('success')), timer: 3000, showConfirmButton:false });
    </script>
  @endif
  @if($errors->any())
    <script>
      Swal.fire({ icon:'error', title:'Gagal', text:@json($errors->first()) });
    </script>
  @endif
  <script>
    function validateFileSize(input) {
      const file = input.files[0];
      if (file && file.size > 20 * 1024 * 1024) { // 20 MB
        alert("Ukuran file maksimal 5 MB!");
        input.value = ""; // reset input
      }
    }
</script>

@endpush