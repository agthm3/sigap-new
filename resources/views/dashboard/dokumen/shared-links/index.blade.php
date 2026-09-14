@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Manajemen Tautan Berbagi</h1>
      <p class="text-sm text-gray-600 mt-1">
        Daftar seluruh tautan folder aktif yang dibagikan ke publik, dilengkapi status keamanan sandi dan masa kedaluwarsa.
      </p>
    </div>

    <!-- Pencarian Cepat Tautan -->
    <form method="GET" action="{{ route('sigap-dokumen.shared-links.index') }}" class="flex items-center gap-2">
      <input type="search" 
             name="q" 
             value="{{ request('q') }}" 
             placeholder="Cari nama folder..." 
             class="rounded-lg border border-gray-300 p-2 text-xs focus:border-maroon focus:ring-maroon w-48 sm:w-64">
      <button type="submit" class="px-3.5 py-2 rounded-lg bg-maroon text-white text-xs font-semibold hover:bg-maroon-800 transition">
        Cari
      </button>
    </form>
  </div>
</section>

<!-- Tabel Daftar Tautan Aktif -->
<section class="max-w-7xl mx-auto px-4 pb-12">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-2xs">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b bg-gray-50 text-gray-600 text-xs font-bold uppercase tracking-wider">
            <th class="px-4 py-3">Nama Folder</th>
            <th class="px-4 py-3">Pemilik</th>
            <th class="px-4 py-3">Proteksi Sandi</th>
            <th class="px-4 py-3">Masa Berlaku</th>
            <th class="px-4 py-3">Tautan Publik</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y text-gray-700">
          @forelse ($sharedFolders as $folder)
            <tr class="hover:bg-gray-50/70 transition" x-data="{ copied: false }">
              <td class="px-4 py-3">
                <div class="flex items-center gap-2.5">
                  <span class="text-lg">{{ $folder->icon ?? '📁' }}</span>
                  <div>
                    <p class="font-bold text-gray-900">{{ $folder->name }}</p>
                    <p class="text-[10px] text-gray-400">Dibuat: {{ $folder->created_at->format('d M Y') }}</p>
                  </div>
                </div>
              </td>
              <td class="px-4 py-3 text-xs font-semibold text-gray-600">
                {{ $folder->user->name ?? 'Sistem' }}
              </td>
              <td class="px-4 py-3">
                @if(!empty($folder->share_password))
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-amber-50 text-amber-800 font-bold border border-amber-200">
                    🔒 Terproteksi Sandi
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">
                    🔓 Tanpa Sandi
                  </span>
                @endif
              </td>
              <td class="px-4 py-3 text-xs">
                @if($folder->share_expires_at)
                  @if($folder->share_expires_at->isPast())
                    <span class="text-red-600 font-bold">Kedaluwarsa</span>
                  @else
                    <span class="text-emerald-700 font-semibold">{{ $folder->share_expires_at->format('d M Y, H:i') }}</span>
                  @endif
                @else
                  <span class="text-indigo-700 font-semibold">Selamanya (Unlimited)</span>
                @endif
              </td>
              <td class="px-4 py-3 font-mono text-xs text-gray-500">
                <span class="bg-gray-50 border px-2 py-1 rounded truncate max-w-xs inline-block" title="{{ route('sigap-dokumen.shared.view', $folder->share_token) }}">
                  {{ route('sigap-dokumen.shared.view', $folder->share_token) }}
                </span>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <!-- Tombol Salin Tautan -->
                  <button type="button" 
                          @click="navigator.clipboard.writeText('{{ route('sigap-dokumen.shared.view', $folder->share_token) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                          class="px-2.5 py-1 text-xs rounded border border-gray-300 hover:bg-gray-100 font-medium transition">
                    <span x-show="!copied">Salin Link</span>
                    <span x-show="copied" class="text-emerald-600 font-bold">Tersalin!</span>
                  </button>

                  <a href="{{ route('sigap-dokumen.shared.view', $folder->share_token) }}" 
                     target="_blank" 
                     class="px-2.5 py-1 text-xs rounded bg-maroon text-white hover:bg-maroon-800 font-semibold transition">
                    Buka ↗
                  </a>

                  <!-- Hapus/Nonaktifkan Tautan (Hanya Admin) -->
                  @role('admin')
                  <form action="{{ route('sigap-dokumen.shared-links.revoke', $folder) }}" 
                        method="POST" 
                        onsubmit="return confirm('Nonaktifkan tautan berbagi untuk folder {{ $folder->name }} ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-2.5 py-1 text-xs rounded border border-red-200 text-red-700 hover:bg-red-50 font-semibold transition">
                      Cabut Link
                    </button>
                  </form>
                  @endrole
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-12 text-center text-gray-400 text-xs">
                Belum ada tautan folder aktif yang dibagikan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($sharedFolders->hasPages())
      <div class="p-3 border-t bg-gray-50">
        {{ $sharedFolders->links() }}
      </div>
    @endif
  </div>
</section>
@endsection