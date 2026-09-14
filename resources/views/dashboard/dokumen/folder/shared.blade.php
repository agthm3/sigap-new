<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $folder->name }} — SIGAP BRIDA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-4 sm:p-8 flex flex-col justify-between selection:bg-red-900 selection:text-white">
  
  <div class="max-w-5xl mx-auto space-y-6 w-full">
    <!-- Header Kotak Informasi Folder -->
    <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-red-100 text-red-900">
            Arsip Resmi Berbagi Tautan
          </span>
        </div>
        <h1 class="text-2xl font-extrabold text-gray-900 flex items-center gap-2">
          <span>{{ $folder->icon ?? '📁' }}</span>
          <span>{{ $folder->name }}</span>
        </h1>
        <p class="text-xs text-gray-500 mt-1">Total Berkas: {{ $documents->total() }} dokumen</p>
      </div>

      <a href="{{ route('sigap-dokumen.folder.download-zip', $folder) }}" 
         class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-900 text-white font-bold text-xs hover:bg-red-800 transition shadow-sm self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        <span>Unduh Semua (.ZIP)</span>
      </a>
    </div>

    <!-- Tabel Berkas Folder -->
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 text-gray-600 border-b text-xs uppercase tracking-wider font-bold">
            <tr>
              <th class="px-4 py-3 text-left">Nama Berkas</th>
              <th class="px-4 py-3 text-left">Kategori</th>
              <th class="px-4 py-3 text-left">Tahun</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y text-gray-700">
            @forelse($documents as $doc)
              <tr class="hover:bg-gray-50/70 transition">
                <td class="px-4 py-3">
                  <p class="font-semibold text-gray-900">{{ $doc->title }}</p>
                  @if($doc->alias)
                    <p class="text-[11px] font-mono text-gray-400">{{ $doc->alias }}</p>
                  @endif
                </td>
                <td class="px-4 py-3 text-xs">{{ $doc->category }}</td>
                <td class="px-4 py-3 text-xs">{{ $doc->year }}</td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('sigap-dokumen.download', $doc) }}" 
                     class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-900 text-white rounded-lg text-xs font-bold hover:bg-red-800 transition shadow-2xs">
                    Unduh
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-4 py-12 text-center text-xs text-gray-400">
                  Folder ini belum memiliki berkas dokumen.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      @if($documents->hasPages())
        <div class="p-3 border-t bg-gray-50">
          {{ $documents->links() }}
        </div>
      @endif
    </div>
  </div>

  <!-- WATERMARK FOOTER RESMI BRIDA -->
  <footer class="max-w-5xl mx-auto w-full mt-12 pt-6 border-t border-gray-200 text-center text-xs text-gray-500 space-y-1">
    <div class="flex items-center justify-center gap-2 font-bold text-gray-700">
      <span class="w-6 h-6 rounded-md bg-red-900 text-white flex items-center justify-center text-[10px]">SB</span>
      <span>SIGAP BRIDA &mdash; Badan Riset dan Inovasi Daerah</span>
    </div>
    <p class="text-[11px] text-gray-400">
      Dokumen ini dibagikan secara resmi melalui sistem terintegrasi &bull; &copy; {{ date('Y') }} Pemerintah Kota Makassar.
    </p>
  </footer>

</body>
</html>