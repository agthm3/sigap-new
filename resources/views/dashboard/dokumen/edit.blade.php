<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Edit Dokumen — SIGAP BRIDA</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

  <header class="border-b bg-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ route('sigap-dokumen.index') }}" class="text-maroon hover:underline">&larr; Kembali ke daftar</a>
      <h1 class="text-lg font-bold">Edit Dokumen</h1>
      <div></div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6 grid lg:grid-cols-3 gap-6">
    <!-- Form -->
    <form class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl p-5 space-y-4"
          method="POST"
          action="{{ route('sigap-dokumen.update', $doc->id) }}"
          enctype="multipart/form-data">
      @csrf
      @method('PUT')

      @if ($errors->any())
        <div class="text-sm text-red-600">
          <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm font-semibold">Judul</span>
          <input type="text" name="title" value="{{ old('title',$doc->title) }}" required class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
        </label>
        <label class="block">
          <span class="text-sm font-semibold">Alias</span>
          <input type="text" name="alias" value="{{ old('alias',$doc->alias) }}" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Opsional (unik)">
        </label>

        <label class="block">
          <span class="text-sm font-semibold">Kategori</span>
          <select name="category" required class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            @foreach (['Surat Keputusan','Laporan','Formulir','Privasi'] as $opt)
              <option value="{{ $opt }}" @selected(old('category',$doc->category)===$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </label>

        <label class="block">
          <span class="text-sm font-semibold">Tahun</span>
          <select name="year" required class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            @for ($y = now()->year; $y>= now()->year-10; $y--)
              <option value="{{ $y }}" @selected(old('year',$doc->year)==$y)>{{ $y }}</option>
            @endfor
          </select>
        </label>

        <label class="block">
          <span class="text-sm font-semibold">Pihak Terkait</span>
          <input type="text" name="stakeholder" value="{{ old('stakeholder',$doc->stakeholder) }}" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
        </label>

        <label class="block">
          <span class="text-sm font-semibold">Status Akses</span>
          <select name="sensitivity" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="public"  @selected(old('sensitivity',$doc->sensitivity)==='public')>Publik</option>
            <option value="private" @selected(old('sensitivity',$doc->sensitivity)==='private')>Akses Terkendali</option>
          </select>
        </label>
      </div>

      <label class="block">
        <span class="text-sm font-semibold">Deskripsi</span>
        <textarea name="description" rows="4" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">{{ old('description',$doc->description) }}</textarea>
      </label>

      <div class="grid sm:grid-cols-2 gap-4">
        <label class="block">
          <span class="text-sm font-semibold">File saat ini</span>
          <div class="mt-1.5 text-sm">
            @if($fileUrl)
              <a href="{{ $fileUrl }}" target="_blank" class="text-maroon hover:underline">Lihat / Download</a>
            @else
              <span class="text-gray-500">Tidak ada</span>
            @endif
          </div>
          <span class="text-[12px] text-gray-500">Kosongkan jika tidak ingin mengganti.</span>
          <input type="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
        </label>

        <label class="block">
          <span class="text-sm font-semibold">Thumbnail saat ini</span>
          <div class="mt-1.5">
            @if($thumbUrl)
              <img src="{{ $thumbUrl }}" alt="thumb" class="w-20 h-20 object-cover rounded border">
            @else
              <span class="text-sm text-gray-500">Tidak ada</span>
            @endif
          </div>
          <span class="text-[12px] text-gray-500">Kosongkan jika tidak ingin mengganti.</span>
          <input type="file" name="thumb" accept=".jpg,.jpeg,.png" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon">
        </label>
      </div>

      <div class="flex items-center justify-end gap-2 pt-2">
        <a href="{{ route('sigap-dokumen.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">Batal</a>
        <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Simpan Perubahan</button>
      </div>
    </form>

    <!-- Info ringkas -->
    <aside class="bg-white border border-gray-200 rounded-2xl p-5 space-y-3 text-sm">
      <h2 class="text-lg font-bold text-gray-800">Informasi</h2>
      <p><span class="font-semibold">Dibuat:</span> {{ $doc->created_at?->format('d M Y H:i') }}</p>
      <p><span class="font-semibold">Diubah:</span> {{ $doc->updated_at?->format('d M Y H:i') }}</p>
      <p class="text-gray-600">Mengganti file/thumbnail akan menghapus file lama secara otomatis.</p>
    </aside>
  </main>

  @if(session('success'))
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json(session('success')),
        timer: 4000,
        showConfirmButton: false
      });
    </script>
  @endif

  @if($errors->any())
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: @json($errors->first()),
      });
    </script>
  @endif
</body>
</html>
