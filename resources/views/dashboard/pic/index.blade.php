@extends('layouts.app')

@section('content')
<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">PIC</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Pusat Informasi Penanggung Jawab sistem, akun, dan tanggung jawab kerja di lingkungan BRIDA.
    </p>
  </div>

  @hasanyrole('admin|verif_pic')
    <a href="{{ route('sigap-pic.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
      + Tambah Sistem
    </a>
  @endhasanyrole
</section>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total Sistem</p>
    <h3 class="text-2xl font-extrabold text-gray-900">{{ $totalSystems }}</h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Sistem Aktif</p>
    <h3 class="text-2xl font-extrabold text-emerald-600">{{ $activeSystems }}</h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Total PIC</p>
    <h3 class="text-2xl font-extrabold text-maroon">{{ $totalPic }}</h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Akun Terdokumentasi</p>
    <h3 class="text-2xl font-extrabold text-blue-600">{{ $totalAccounts }}</h3>
  </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm mt-4">
  <form method="GET" action="{{ route('sigap-pic.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
    <div class="md:col-span-2 relative">
      <input type="text"
             name="q"
             value="{{ request('q') }}"
             placeholder="Cari sistem, PIC, akun, jabatan, atau kata kunci..."
             class="w-full pl-10 pr-3 py-2.5 rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
      <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
           viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.3-4.3M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z"/>
      </svg>
    </div>

    <select name="kategori"
            class="w-full px-3 py-2.5 rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
      <option value="">Semua Kategori</option>
      <option value="internal" @selected(request('kategori') === 'internal')>Internal</option>
      <option value="publik" @selected(request('kategori') === 'publik')>Publik</option>
      <option value="khusus" @selected(request('kategori') === 'khusus')>Khusus</option>
      <option value="lainnya" @selected(request('kategori') === 'lainnya')>Lainnya</option>
    </select>

    <div class="flex gap-2">
      <select name="status"
              class="w-full px-3 py-2.5 rounded-xl border border-gray-300 focus:ring-maroon focus:border-maroon">
        <option value="">Semua Status</option>
        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
        <option value="maintenance" @selected(request('status') === 'maintenance')>Maintenance</option>
        <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
      </select>

      <button type="submit"
              class="px-4 py-2.5 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
        Filter
      </button>
    </div>
  </form>
</div>

<div class="mt-4 grid grid-cols-1 lg:grid-cols-2 gap-4">
  @forelse($systems as $item)
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
      <div class="p-4 border-b bg-gray-50 flex items-start gap-4">
        <div class="h-14 w-14 rounded-2xl bg-maroon text-white flex items-center justify-center font-extrabold text-lg shrink-0">
          {{ strtoupper(substr($item->nama_sistem ?? 'S', 0, 2)) }}
        </div>

        <div class="flex-1 min-w-0">
          <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
            <div>
              <h2 class="font-bold text-gray-900 text-lg leading-tight">
                {{ $item->nama_sistem }}
              </h2>
              <p class="text-sm text-gray-600 mt-1">
                {{ $item->deskripsi ?: 'Tidak ada deskripsi.' }}
              </p>
            </div>

            <div class="flex flex-wrap gap-2">
              <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
                {{ $item->status === 'aktif'
                    ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                    : ($item->status === 'maintenance'
                        ? 'bg-amber-50 border-amber-200 text-amber-700'
                        : 'bg-gray-50 border-gray-200 text-gray-700') }}">
                {{ strtoupper($item->status) }}
              </span>

              <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border bg-blue-50 border-blue-200 text-blue-700">
                {{ strtoupper($item->kategori ?? 'UMUM') }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="p-4 space-y-4">
        <div class="mt-1 rounded-xl overflow-hidden border border-gray-200 bg-gray-50">
          <div class="px-3 py-2 flex items-center justify-between text-xs text-gray-500 border-b border-gray-200">
            <span>Preview Website</span>
            @if($item->url)
              <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="text-maroon hover:underline">Buka</a>
            @endif
          </div>

          @if($item->url)
            <iframe
              src="{{ $item->url }}"
              class="w-full h-36 bg-white"
              loading="lazy"
              referrerpolicy="no-referrer"
              sandbox="allow-scripts allow-forms allow-same-origin allow-popups">
            </iframe>
          @else
            <div class="p-4 text-sm text-gray-500">
              Belum ada link website.
            </div>
          @endif
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[11px] text-gray-500">PIC Utama</p>
            <p class="text-sm font-semibold text-gray-900">
              {{ $item->primary_pic_name ?? '-' }}
            </p>
          </div>

          <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[11px] text-gray-500">Jumlah PIC</p>
            <p class="text-sm font-semibold text-gray-900">{{ $item->pic_count ?? 0 }}</p>
          </div>

          <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[11px] text-gray-500">Akun Terkait</p>
            <p class="text-sm font-semibold text-gray-900">{{ $item->account_count ?? 0 }}</p>
          </div>

          <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
            <p class="text-[11px] text-gray-500">Update Terakhir</p>
            <p class="text-sm font-semibold text-gray-900">
              {{ $item->updated_at ? $item->updated_at->format('d M Y') : '-' }}
            </p>
          </div>
        </div>

        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-2">
            PIC / Penanggung Jawab
          </p>

          <div class="flex flex-wrap gap-2">
            @forelse($item->assignments as $pic)
              <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-maroon/5 border border-maroon/15 text-sm text-gray-800">
                <span class="font-semibold">{{ $pic->user->name ?? $pic->nama_pic }}</span>
                @if($pic->is_primary)
                  <span class="text-[10px] px-2 py-0.5 rounded-full bg-maroon text-white">UTAMA</span>
                @endif
              </span>
            @empty
              <span class="text-sm text-gray-500">Belum ada PIC terdaftar.</span>
            @endforelse
          </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-1">
          <a href="{{ route('sigap-pic.show', $item->id) }}"
             class="px-3 py-1.5 rounded-xl border border-gray-300 text-xs hover:bg-gray-50">
            Buka
          </a>

          @hasanyrole('admin|verif_pic')
            <a href="{{ route('sigap-pic.edit', $item->id) }}"
               class="px-3 py-1.5 rounded-xl border border-blue-300 text-blue-700 text-xs hover:bg-blue-50">
              Edit
            </a>

            <form action="{{ route('sigap-pic.destroy', $item->id) }}"
                  method="POST"
                  class="form-delete inline">
              @csrf
              @method('DELETE')

              <button type="button"
                      class="btn-delete px-3 py-1.5 rounded-xl border border-red-500 text-red-600 text-xs hover:bg-red-600 hover:text-white"
                      data-nama="{{ $item->nama_sistem }}">
                Hapus
              </button>
            </form>
          @endhasanyrole
        </div>
      </div>
    </div>
  @empty
    <div class="lg:col-span-2 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center text-gray-500">
      Belum ada sistem PIC yang terdaftar.
    </div>
  @endforelse
</div>

<div class="mt-4">
  {{ $systems->links() }}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.btn-delete').forEach(function(btn) {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      const nama = this.dataset.nama;

      Swal.fire({
        title: 'Hapus Sistem?',
        html: `Sistem <b>${nama}</b> akan dihapus permanen!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#b91c1c',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
});
</script>
@endpush