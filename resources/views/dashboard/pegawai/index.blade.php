@extends('layouts.app')

@section('content')
  <!-- Header -->
  <section class="max-w-7xl mx-auto px-4">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-extrabold text-gray-900">Daftar Pegawai</h1>
        <p class="text-sm text-gray-600 mt-1">Kelola data pegawai untuk akses dokumen dan arsip privasi.</p>
      </div>
      <div class="flex flex-wrap gap-2">
        @hasrole('admin')
        <a href="{{ route('sigap-pegawai.create') }}" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
        @endhasrole
        <button id="btnExport" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Export CSV</button>
      </div>
    </div>
  </section>

  <!-- Modal Export -->
  <div id="exportModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 w-full max-w-lg">
      <h2 class="text-lg font-bold mb-4">Export Data Pegawai</h2>

      <form method="GET" action="{{ route('sigap-pegawai.export') }}">
        
        <div class="mb-3">
          <label class="text-sm">Role</label>
          <select name="role" class="w-full border rounded p-2">
            <option value="">Semua</option>
            @foreach($roles as $role)
              <option value="{{ $role }}">{{ $role }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="text-sm">Unit</label>
          <select name="unit" class="w-full border rounded p-2">
          <option value="">Semua</option>
          @foreach ($unitCategories as $kategori)
            <option value="{{ $kategori }}">{{ $kategori }}</option>
          @endforeach
        </select>
        </div>

        <div class="mb-3">
          <label class="text-sm">Status</label>
          <select name="status" class="w-full border rounded p-2">
            <option value="">Semua</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <button type="button" id="closeModal" class="px-3 py-2 border rounded">Batal</button>
          <button type="submit" class="px-3 py-2 bg-maroon text-white rounded">Export</button>
        </div>

      </form>
    </div>
  </div>

  <!-- Filters -->
  <section class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-7 gap-3" action="{{ route('sigap-pegawai.index') }}" method="GET">
        @csrf
        <div class="lg:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Cari</label>
          <input id="f_q" name="q" value="{{ request('q') }}" type="search" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama / Username / NIP / Unit">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Unit</label>
          <select id="f_unit" name="unit"
                  class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            @foreach ($unitCategories as $kategori)
              <option value="{{ $kategori }}" @selected(request('unit') == $kategori)>
                {{ $kategori }}
              </option>
            @endforeach
          </select>
        </div>
        
        {{-- Hanya Admin yang bisa melihat/memfilter Role, Verif Pegawai tidak perlu --}}
        @hasrole('admin')
        <div>
          <label class="text-sm font-semibold text-gray-700">Role</label>
          <select id="f_role" name="role" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
              @foreach($roles as $role)
                <option value="{{ $role }}" @selected(request('role') == $role)>{{ $role }}</option>
              @endforeach
          </select>
        </div>
        @endhasrole

        <div>
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select id="f_status" name="status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option value="active" @selected(request('status') == 'active')>Aktif</option>
            <option value="inactive" @selected(request('status') == 'inactive')>Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Urutkan</label>
          <select id="sort" name="sort" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="latest" @selected(request('sort') == 'latest')>Terbaru</option>
            <option value="name" @selected(request('sort') == 'name')>Nama (A-Z)</option>
            <option value="unit" @selected(request('sort') == 'unit')>Unit (A-Z)</option>
          </select>
        </div>
        <div class="col-span-full flex items-end gap-2">
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Terapkan</button>
          <a href="{{ route('sigap-pegawai.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-gray-700">Reset</a>
        </div>
      </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
      <div class="px-4 py-3 bg-gray-50/50 text-sm text-gray-700 flex flex-wrap items-center justify-between border-b border-gray-100">
        <span id="countInfo" class="font-medium">Menampilkan {{ $users->count() }} pegawai</span>
        <form method="GET" action="{{ route('sigap-pegawai.index') }}" class="flex items-center gap-2 mt-2 sm:mt-0">
          <!-- Pertahankan filter lain saat mengganti page size -->
          @if(request('q')) <input type="hidden" name="q" value="{{ request('q') }}"> @endif
          @if(request('unit')) <input type="hidden" name="unit" value="{{ request('unit') }}"> @endif
          @if(request('role')) <input type="hidden" name="role" value="{{ request('role') }}"> @endif
          @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
          @if(request('sort')) <input type="hidden" name="sort" value="{{ request('sort') }}"> @endif

          <label class="text-sm text-gray-600 font-medium">Tampilkan</label>
          <select name="per_page" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-1.5 cursor-pointer">
            <option value="10" @selected(request('per_page') == '10')>10</option>
            <option value="25" @selected(request('per_page', '25') == '25')>25</option>
            <option value="50" @selected(request('per_page') == '50')>50</option>
          </select>
        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white">
            <tr class="text-left border-b border-gray-200 text-gray-600">
              <th class="px-4 py-3 font-semibold">Pegawai</th>
              <th class="px-4 py-3 font-semibold">NIP</th>
              <th class="px-4 py-3 font-semibold">Unit</th>
              <th class="px-4 py-3 font-semibold">Role</th>
              <th class="px-4 py-3 font-semibold">Kontak</th>
              <th class="px-4 py-3 font-semibold">Status</th>
              <th class="px-4 py-3 font-semibold text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            @forelse ($users as $e)
              <tr class="hover:bg-gray-50/50 transition-colors">
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm"
                    src="{{ $e->profile_photo_path ? asset('storage/'.$e->profile_photo_path) : asset('images/avatar-placeholder.png') }}"
                    alt="">
                    <div>
                      <p class="font-bold text-gray-900">{{ $e->name }}</p>
                      <p class="text-xs text-gray-500">{{ '@'.$e->username }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3 font-medium text-gray-700">{{ $e->nip ?: '—' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $e->unit ?: '—' }}</td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-1">
                    @php($roleNames = $e->getRoleNames())
                    @forelse($roleNames as $rn)
                      <span class="px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 border border-gray-200 text-gray-700">{{ $rn }}</span>
                    @empty
                      <span class="text-xs text-gray-400">—</span>
                    @endforelse
                  </div>
                </td>
                <td class="px-4 py-3 text-xs">
                  <div class="font-medium text-gray-800">{{ $e->email ?: '—' }}</div>
                  <div class="text-gray-500 mt-0.5">{{ $e->nomor_hp ?: '—' }}</div>
                </td>
                <td class="px-4 py-3">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold tracking-wide {{ $e->status==='active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $e->status==='active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                    {{ $e->status==='active' ? 'AKTIF' : 'NONAKTIF' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap items-center justify-end gap-2">
                    
                    {{-- Tombol Lihat (Tampil untuk Admin & Verif Pegawai) --}}
                    <a href="{{ route('sigap-pegawai.show', $e->id) }}" class="px-3 py-1.5 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-100 font-semibold text-xs shadow-sm transition-colors">
                      Lihat
                    </a>
                    
                    {{-- Tombol Edit & Hapus (Hanya tampil untuk Admin) --}}
                    @hasrole('admin')
                    <a href="{{ route('sigap-pegawai.edit', $e->id) }}" class="px-3 py-1.5 rounded-lg border border-blue-200 text-blue-700 bg-blue-50/50 hover:bg-blue-100 font-semibold text-xs shadow-sm transition-colors">
                      Edit
                    </a>
                    
                    <form action="{{ route('sigap-pegawai.destroy', $e) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pegawai ini secara permanen?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-200 text-red-700 bg-red-50/50 hover:bg-red-100 font-semibold text-xs shadow-sm transition-colors">
                        Hapus
                      </button>
                    </form>
                    @endhasrole

                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-10 text-center">
                  <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  </div>
                  <p class="text-sm font-medium text-gray-900">Belum ada data pegawai.</p>
                  <p class="text-xs text-gray-500 mt-1">Gunakan tombol 'Tambah Pegawai' untuk memasukkan data baru.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      @if($users->hasPages())
      <div class="px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-gray-100 bg-white">
        <p class="text-sm text-gray-500 font-medium">
          Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}
        </p>
        <div class="overflow-x-auto w-full sm:w-auto">
          {{ $users->withQueryString()->links() }}
        </div>
      </div>
      @endif
    </div>

  </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('btnExport');
    const close = document.getElementById('closeModal');
    const modal = document.getElementById('exportModal');

    if (btn) {
        btn.onclick = function () {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
    }

    if (close) {
        close.onclick = function () {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };
    }

});
</script>
@endpush