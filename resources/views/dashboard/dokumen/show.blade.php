@extends('layouts.app')

@section('content')
<!-- Breadcrumb Dinamis -->
<nav class="max-w-7xl mx-auto px-4 pt-4 pb-2 text-xs" aria-label="Breadcrumb">
  <ol class="flex items-center flex-wrap gap-1.5 text-gray-500 font-medium">
    <li>
      <a href="{{ route('home') }}" class="hover:text-maroon transition">Beranda</a>
    </li>
    <li><span>/</span></li>
    <li>
      <a href="{{ route('sigap-dokumen.index') }}" class="hover:text-maroon transition">Dokumen</a>
    </li>
    @if($document->folder)
      <li><span>/</span></li>
      <li>
        <a href="{{ route('sigap-dokumen.folder.show', $document->folder) }}" class="hover:text-maroon transition flex items-center gap-1">
          <span>{{ $document->folder->icon ?? '📁' }}</span>
          <span>{{ $document->folder->name }}</span>
        </a>
      </li>
    @endif
    <li><span>/</span></li>
    <li class="text-maroon font-bold truncate max-w-xs sm:max-w-md" aria-current="page">
      {{ $document->title }}
    </li>
  </ol>
</nav>

<!-- Header Halaman -->
<section class="max-w-7xl mx-auto px-4 py-4">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-maroon/10 text-maroon">
          ARSIP NASKAH DINAS
        </span>
        <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase
          {{ $document->sensitivity === 'public' ? 'bg-emerald-100 text-emerald-800' :              ($document->sensitivity === 'internal' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
          {{ $document->sensitivity === 'public' ? 'Publik Terbuka' : ($document->sensitivity === 'internal' ? 'Internal BRIDA' : 'Privat Terkunci') }}
        </span>
      </div>
      <h1 class="text-2xl font-extrabold text-gray-900 leading-tight">
        {{ $document->title }}
      </h1>
      <p class="text-xs text-gray-500 mt-1">
        Diunggah oleh <strong class="text-gray-700">{{ $document->creator->name ?? 'Staf BRIDA' }}</strong> &bull; {{ $document->created_at ? $document->created_at->timezone('Asia/Makassar')->format('d M Y, H:i') . ' WITA' : '-' }}
      </p>
    </div>

    <!-- Tombol Aksi Kanan Atas -->
    <div class="flex items-center gap-2">
      @if($document->folder_id)
        <a href="{{ route('sigap-dokumen.folder.show', $document->folder_id) }}" 
           class="px-3.5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs font-semibold shadow-2xs">
          &larr; Ke Folder
        </a>
      @else
        <a href="{{ $document->sensitivity === 'private' ? route('sigap-dokumen.saya') : route('sigap-dokumen.index') }}" 
           class="px-3.5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs font-semibold shadow-2xs">
          &larr; Kembali
        </a>
      @endif

      @if($document->created_by === auth()->id() || auth()->user()->hasRole('admin'))
        <a href="{{ route('sigap-dokumen.edit', $document->id) }}" 
           class="px-3.5 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition text-xs font-semibold shadow-2xs">
          Edit
        </a>
        <button type="button" 
                onclick="confirmHapusDokumen({{ $document->id }}, '{{ addslashes($document->title) }}')"
                class="px-3.5 py-2 rounded-lg border border-red-200 text-red-700 hover:bg-red-50 transition text-xs font-semibold shadow-2xs">
          Hapus
        </button>
        <form id="form-delete-{{ $document->id }}" action="{{ route('sigap-dokumen.destroy', $document->id) }}" method="POST" class="hidden">
          @csrf
          @method('DELETE')
        </form>
      @endif

      <a href="{{ route('sigap-dokumen.download', $document) }}" 
         class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition text-xs font-bold flex items-center gap-1.5 shadow-sm">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
        </svg>
        <span>Unduh Berkas</span>
      </a>
    </div>
  </div>
</section>

<!-- Konten Utama (Preview & Metadata Grid) -->
<section class="max-w-7xl mx-auto px-4 pb-8 grid lg:grid-cols-3 gap-6">
  <!-- Sisi Kiri: Pratinjau Dokumen Inline -->
  <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2xs flex flex-col">
    <div class="px-5 py-3.5 border-b bg-gray-50 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-sm">👁️</span>
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Pratinjau Berkas</h2>
      </div>
      <a href="{{ $fileUrl }}" target="_blank" class="text-xs text-maroon hover:underline font-semibold flex items-center gap-1">
        Buka Tab Baru &nearr;
      </a>
    </div>

    <div class="flex-1 bg-gray-900 min-h-[500px] flex items-center justify-center">
      @if($isPdf)
        <iframe src="{{ $fileUrl }}#toolbar=1" class="w-full h-[650px]" frameborder="0"></iframe>
      @elseif($isImage)
        <div class="p-4 flex items-center justify-center">
          <img src="{{ $fileUrl }}" class="max-h-[650px] w-auto object-contain rounded shadow-lg" alt="{{ $document->title }}">
        </div>
      @else
        <div class="p-8 text-center text-gray-400">
          <div class="w-12 h-12 mx-auto rounded-full bg-gray-800 flex items-center justify-center text-xl mb-2">📄</div>
          <p class="text-sm font-semibold text-gray-200">Pratinjau langsung tidak didukung untuk format ini.</p>
          <a href="{{ route('sigap-dokumen.download', $document) }}" class="mt-3 inline-block px-4 py-1.5 rounded bg-maroon text-white text-xs font-semibold">
            Unduh Berkas untuk Membaca
          </a>
        </div>
      @endif
    </div>
  </div>

  <!-- Sisi Kanan: Spesifikasi & Lokasi Fisik Berkas -->
  <div class="space-y-6">
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs">
      <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2.5 mb-3 flex items-center gap-2">
        <span>📋 Spesifikasi &amp; Legalitas</span>
      </h2>

      <dl class="space-y-3 text-xs">
        <div>
          <dt class="text-gray-400 font-semibold">Nomor Surat / Naskah Dinas:</dt>
          <dd class="text-gray-800 font-mono font-bold mt-0.5">{{ $document->number ?: '—' }}</dd>
        </div>

        <div>
          <dt class="text-gray-400 font-semibold">Tanggal Penetapan / Surat:</dt>
          <dd class="text-gray-800 font-semibold mt-0.5">
            {{ $document->doc_date ? \Carbon\Carbon::parse($document->doc_date)->isoFormat('D MMMM Y') : '—' }}
          </dd>
        </div>

        <div>
          <dt class="text-gray-400 font-semibold">Alias Berkas:</dt>
          <dd class="text-gray-700 font-mono mt-0.5">{{ $document->alias ?: '—' }}</dd>
        </div>

        <div class="grid grid-cols-2 gap-2 pt-1 border-t border-gray-100">
          <div>
            <dt class="text-gray-400 font-semibold">Kategori:</dt>
            <dd class="text-gray-800 font-bold mt-0.5">{{ $document->category }}</dd>
          </div>
          <div>
            <dt class="text-gray-400 font-semibold">Tahun Anggaran:</dt>
            <dd class="text-gray-800 font-bold mt-0.5">{{ $document->year }}</dd>
          </div>
        </div>

        @if($document->folder)
          <div class="pt-1 border-t border-gray-100">
            <dt class="text-gray-400 font-semibold">Folder Penempatan:</dt>
            <dd class="mt-1">
              <a href="{{ route('sigap-dokumen.folder.show', $document->folder) }}" 
                 class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold transition">
                <span>{{ $document->folder->icon ?? '📁' }}</span>
                <span>{{ $document->folder->name }}</span>
              </a>
            </dd>
          </div>
        @endif

        <div class="pt-1 border-t border-gray-100">
          <dt class="text-gray-400 font-semibold">Pihak Terkait / Instansi / Mitra:</dt>
          <dd class="text-gray-800 font-semibold mt-0.5">{{ $document->stakeholder ?: '—' }}</dd>
        </div>

        <div class="pt-1 border-t border-gray-100">
          <dt class="text-gray-400 font-semibold">Ringkasan / Pokok Bahasan:</dt>
          <dd class="text-gray-700 leading-relaxed mt-1 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
            {{ $document->description ?: 'Tidak ada ringkasan catatan pokok untuk dokumen ini.' }}
          </dd>
        </div>

        @if(!empty($document->tags))
          <div class="pt-1 border-t border-gray-100">
            <dt class="text-gray-400 font-semibold mb-1">Tag Pencarian:</dt>
            <dd class="flex flex-wrap gap-1">
              @php
                $tagList = is_array($document->tags) ? $document->tags : explode(',',$document->tags);
              @endphp
              @foreach ($tagList as $t)
                @if(trim($t))
                  <span class="text-[10px] bg-maroon/10 text-maroon border border-maroon/20 px-2 py-0.5 rounded-full font-bold">
                    #{{ trim($t) }}
                  </span>
                @endif
              @endforeach
            </dd>
          </div>
        @endif
      </dl>
    </div>

    <!-- Lokasi Fisik Arsip Hardcopy -->
    <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-2xs">
      <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 border-b pb-2 mb-3 flex items-center gap-2">
        <span>🗄️ Lokasi Fisik Lemari Arsip</span>
      </h2>

      <div class="grid grid-cols-2 gap-3 text-xs">
        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
          <p class="text-[10px] text-gray-400 font-bold uppercase">Rak / Lemari</p>
          <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $document->physical_rack ?: '—' }}</p>
        </div>
        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200">
          <p class="text-[10px] text-gray-400 font-bold uppercase">Boks / Ordner</p>
          <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $document->physical_row ?: '—' }}</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Riwayat Aktivitas & Akses Dokumen -->
<section class="max-w-7xl mx-auto px-4 pb-12">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2xs">
    <div class="px-5 py-3.5 bg-gray-50 border-b flex items-center justify-between">
      <div class="flex items-center gap-2">
        <span class="text-sm">📜</span>
        <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Audit Trail / Riwayat Akses Dokumen</h2>
      </div>
      <span class="text-[11px] text-gray-500 font-semibold">{{ $logs->total() }} Catatan</span>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-xs">
        <thead>
          <tr class="text-left border-b bg-gray-50 text-gray-500 uppercase tracking-wider">
            <th class="px-4 py-3">Nama Pegawai / Pengguna</th>
            <th class="px-4 py-3">Waktu Akses</th>
            <th class="px-4 py-3">Aksi</th>
            <th class="px-4 py-3">Keterangan</th>
          </tr>
        </thead>
        <tbody class="divide-y text-gray-700">
          @if($logs->isNotEmpty())
            @foreach ($logs as $log)
              <tr class="hover:bg-gray-50/70 transition">
                <td class="px-4 py-2.5">
                  <span class="font-bold text-gray-900">{{ $log->user_name ?? 'Sistem' }}</span>
                  @if($log->user_role)
                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] bg-gray-100 text-gray-600 font-mono">{{ $log->user_role }}</span>
                  @endif
                </td>
                <td class="px-4 py-2.5 font-mono text-gray-500">
                  {{ $log->created_at->timezone('Asia/Makassar')->format('d M Y &bull; H:i') }}
                </td>
                <td class="px-4 py-2.5">
                  @php
                    $label = [
                      'view'          => 'Melihat Dokumen',
                      'download'      => 'Mengunduh Berkas',
                      'create'        => 'Membuat Arsip',
                      'update'        => 'Memperbarui Data',
                      'delete'        => 'Menghapus',
                      'access_denied' => 'Akses Ditolak',
                    ][$log->action] ?? $log->action;
                  @endphp
                  <span class="font-semibold">{{ $label }}</span>
                  @if($log->success === false)
                    <span class="ml-1.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Gagal</span>
                  @endif
                </td>
                <td class="px-4 py-2.5 text-gray-500">
                  {{ $log->reason ?: '—' }}
                </td>
              </tr>
            @endforeach
          @else
            <tr>
              <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                Belum ada catatan aktivitas untuk berkas ini.
              </td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>

    @if($logs->hasPages())
      <div class="px-5 py-3 border-t bg-gray-50">
        {{ $logs->links() }}
      </div>
    @endif
  </div>
</section>

@push('scripts')
<script>
function confirmHapusDokumen(id, title) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Hapus Dokumen?',
      text: `Apakah Anda yakin ingin menghapus dokumen "${title}"?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#7a2222',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Hapus',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('form-delete-' + id).submit();
      }
    });
  } else {
    if (confirm(`Apakah Anda yakin ingin menghapus dokumen "${title}"?`)) {
      document.getElementById('form-delete-' + id).submit();
    }
  }
}
</script>
@endpush
@endsection