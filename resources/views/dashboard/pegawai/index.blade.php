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
        <a href="{{ route('sigap-pegawai.create') }}" class="px-3 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
        <button id="btnExport" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Export CSV</button>
      </div>
    </div>
  </section>

  <!-- Filters -->
  <section class="max-w-7xl mx-auto px-4 mt-4">
    <div class="bg-white border border-gray-200 rounded-2xl p-4 sm:p-5">
      <form class="grid lg:grid-cols-7 gap-3" action="{{ route('sigap-pegawai.index') }}" method="GET">
        @csrf
        <div class="lg:col-span-2">
          <label class="text-sm font-semibold text-gray-700">Cari</label>
          <input id="f_q" name="q" type="search" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama / Username / NIP / Unit">
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Unit</label>
          <select id="f_unit" name="unit" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>Sekretariat A</option>
            <option>Bidang Riset</option>
            <option>TI</option>
            <option>Keuangan</option>
            <option>Humas</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Role</label>
          <select id="f_role" name="role" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option>admin</option>
            <option>verifikator</option>
            <option>pegawai</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Status</label>
          <select id="f_status" name="status" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="">Semua</option>
            <option value="active">Aktif</option>
            <option value="inactive">Nonaktif</option>
          </select>
        </div>
        <div>
          <label class="text-sm font-semibold text-gray-700">Urutkan</label>
          <select id="sort" name="sort" class="mt-1.5 w-full rounded-lg border p-2 border-gray-300 focus:border-maroon focus:ring-maroon">
            <option value="latest">Terbaru</option>
            <option value="name">Nama (A-Z)</option>
            <option value="unit">Unit (A-Z)</option>
          </select>
        </div>
        <div class="col-span-full flex items-end gap-2">
          <button class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">Terapkan</button>
          <a href="{{ route('sigap-pegawai.index') }}" type="reset" class="px-4 py-2 rounded-lg  border border-gray-300 hover:bg-gray-50" >Reset</a>
        </div>
    </form>
    </div>
  </section>

  <!-- Table -->
  <section class="max-w-7xl mx-auto px-4 py-6">
    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
      <div class="px-4 py-3 bg-gray-50 text-sm text-gray-700 flex items-center justify-between">
        <span id="countInfo">Menampilkan {{ $employees->count() }} pegawai</span>
        <div class="flex items-center gap-2">
          <label class="text-sm text-gray-600">Tampilkan</label>
          <select id="pageSize" class="text-sm rounded-md border-gray-300 focus:border-maroon focus:ring-maroon">
            <option>10</option>
            <option selected>25</option>
            <option>50</option>
          </select>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-white">
            <tr class="text-left border-b">
              <th class="px-4 py-3">Pegawai</th>
              <th class="px-4 py-3">NIP</th>
              <th class="px-4 py-3">Unit</th>
              <th class="px-4 py-3">Role</th>
              <th class="px-4 py-3">Kontak</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            @forelse ($employees as $e)
              <tr>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-3">
                    <img class="w-10 h-10 rounded-full object-cover"
                        src="{{ $e->avatar_path ? asset('storage/'.$e->avatar_path) : asset('images/avatar-placeholder.png') }}"
                        alt="">
                    <div>
                      <p class="font-medium text-gray-900">{{ $e->name }}</p>
                      <p class="text-xs text-gray-600">{{ '@'.$e->username }}</p>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-3">{{ $e->nip ?: '—' }}</td>
                <td class="px-4 py-3">{{ $e->unit }}</td>
                <td class="px-4 py-3 capitalize">{{ $e->role }}</td>
                <td class="px-4 py-3 text-xs">
                  <div>{{ $e->email ?: '—' }}</div>
                  <div class="text-gray-500">{{ $e->phone ?: '—' }}</div>
                </td>
                <td class="px-4 py-3">
                  <span class="px-2 py-0.5 rounded text-xs {{ $e->status==='active' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $e->status==='active' ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex flex-wrap gap-2">
                    <a href="{{ route('sigap-pegawai.edit', $e->id) }}" class="px-3 py-1.5 rounded-md border hover:bg-gray-50">Edit</a>
                    <form action="{{ route('sigap-pegawai.destroy', $e) }}" method="POST"
                          onsubmit="return confirm('Hapus pegawai ini?')">
                      @csrf @method('DELETE')
                      <button class="px-3 py-1.5 rounded-md border text-red-700 border-red-300 hover:bg-red-50">Hapus</button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada data pegawai.</td>
              </tr>
            @endforelse
          </tbody>

        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 flex items-center justify-between">
        <p class="text-sm text-gray-600">
          Halaman {{ $employees->currentPage() }} dari {{ $employees->lastPage() }}
        </p>
        <div>
          {{ $employees->links() }}
        </div>
      </div>


    <!-- Empty -->
    <div id="empty" class="mt-6 hidden">
      <div class="rounded-xl border border-dashed border-gray-300 bg-white p-8 text-center">
        <p class="text-sm text-gray-700">Belum ada data pegawai.</p>
        <a href="{{ route('sigap-pegawai.create') }}" class="inline-flex mt-3 px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Tambah Pegawai</a>
      </div>
    </div>
  </section>
@endsection