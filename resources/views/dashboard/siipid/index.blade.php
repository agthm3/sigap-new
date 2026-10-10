@extends('layouts.app')

@section('content')
<div class="space-y-6">

  {{-- Header & Aksi --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-maroon/10 text-maroon">
          🏆 SIGAP SIIPID
        </span>
        <span class="text-xs text-gray-400">•</span>
        <span class="text-xs text-gray-500 font-medium">Perwali Makassar 2026</span>
      </div>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-1">
        Database Prestasi & Rekam Jejak Inovator
      </h1>
      <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
        Pencatatan dan pengusulan penghargaan serta insentif prestasi inovasi daerah Kota Makassar.
      </p>
    </div>

    <div class="flex items-center gap-2.5 shrink-0">
      <a href="{{ route('public.siipid.index') }}" target="_blank"
         class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-gray-300 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 shadow-2xs transition">
        <span>Galeri Publik</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
      </a>

      <a href="{{ route('sigap-siipid.create') }}" 
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon text-white text-xs font-bold hover:bg-maroon-700 shadow-sm transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        <span>Usulkan Prestasi Baru</span>
      </a>
    </div>
  </div>

  {{-- Alert jika ada usulan butuh perbaikan --}}
  @if($perluPerbaikan > 0)
    <div class="rounded-2xl border border-amber-300 bg-amber-50/90 p-4 flex items-start gap-3 shadow-2xs">
      <div class="p-2 rounded-xl bg-amber-200 text-amber-900 shrink-0">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
      </div>
      <div class="text-xs">
        <h4 class="font-bold text-amber-900 text-sm">Perhatian: Ada Usulan yang Memerlukan Perbaikan!</h4>
        <p class="text-amber-800 mt-0.5">
          Terdapat <strong>{{ $perluPerbaikan }} usulan</strong> yang telah diperiksa oleh Reviewer dan dikembalikan untuk dilengkapi. Silakan klik tombol <em>Perbaiki</em> pada tabel di bawah.
        </p>
      </div>
    </div>
  @endif

  {{-- Statistik Cards --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-gray-500">Total Usulan Saya</span>
        <span class="p-2 rounded-xl bg-gray-100 text-gray-700 text-xs">📋</span>
      </div>
      <p class="text-2xl font-extrabold text-gray-900 mt-2">{{ $totalUsulan }}</p>
      <span class="text-[11px] text-gray-400">Prestasi terinput</span>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-blue-600">Menunggu Review</span>
        <span class="p-2 rounded-xl bg-blue-50 text-blue-600 text-xs">⏳</span>
      </div>
      <p class="text-2xl font-extrabold text-blue-700 mt-2">{{ $menungguReview }}</p>
      <span class="text-[11px] text-gray-400">Sedang diproses reviewer</span>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-amber-600">Perlu Perbaikan</span>
        <span class="p-2 rounded-xl bg-amber-50 text-amber-600 text-xs">✍️</span>
      </div>
      <p class="text-2xl font-extrabold text-amber-700 mt-2">{{ $perluPerbaikan }}</p>
      <span class="text-[11px] text-gray-400">Catatan dari tim verifikator</span>
    </div>

    <div class="bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-semibold text-emerald-600">Disetujui / SK</span>
        <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600 text-xs">🎖️</span>
      </div>
      <p class="text-2xl font-extrabold text-emerald-700 mt-2">{{ $disetujui }}</p>
      <span class="text-[11px] text-gray-400">Rekomendasi penghargaan</span>
    </div>
  </div>

  {{-- Tabel Usulan Prestasi --}}
  <div class="bg-white rounded-2xl border border-gray-200/80 shadow-2xs overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between">
      <div>
        <h3 class="font-bold text-gray-900 text-sm sm:text-base">Daftar Usulan Prestasi Saya</h3>
        <p class="text-xs text-gray-500">Seluruh riwayat pengajuan prestasi dan status persetujuannya.</p>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-gray-50/80 text-gray-600 font-bold border-b border-gray-200/60 uppercase tracking-wider text-[10px]">
          <tr>
            <th class="py-3 px-4">Prestasi & Kompetisi</th>
            <th class="py-3 px-4">Inovasi Terkait</th>
            <th class="py-3 px-4">Tingkat & Tahun</th>
            <th class="py-3 px-4">Status Review</th>
            <th class="py-3 px-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse($prestasis as $item)
            @php $badge = $item->status_badge; @endphp
            <tr class="hover:bg-gray-50/60 transition-colors">
              <td class="py-3.5 px-4">
                <div class="font-bold text-gray-900 text-sm leading-snug">{{ $item->nama_prestasi }}</div>
                <div class="text-gray-500 text-[11px] mt-0.5">
                  {{ $item->ajang_kompetisi }} • <span class="font-semibold text-gray-700">{{ $item->peringkat_capaian ?: 'Peserta/Finalis' }}</span>
                </div>
              </td>

              <td class="py-3.5 px-4">
                <div class="font-semibold text-gray-800">{{ $item->innovable->judul ?? '—' }}</div>
                <span class="inline-block mt-0.5 text-[10px] font-medium text-gray-400">
                  {{ class_basename($item->innovable_type) === 'Inovasi' ? '🏛️ SIGAP Inovasi (OPD)' : '💡 SIGAP IMA (Masyarakat)' }}
                </span>
              </td>

              <td class="py-3.5 px-4">
                <span class="font-semibold text-gray-800">{{ $item->tingkat_label }}</span>
                <div class="text-gray-400 text-[11px]">Tahun {{ $item->tahun_perolehan }}</div>
              </td>

              <td class="py-3.5 px-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $badge['class'] }}">
                  {{ $badge['label'] }}
                </span>
                @if($item->status === 'dikembalikan_perbaikan' && $item->catatan_review_terakhir)
                  <div class="mt-1 text-[11px] text-amber-700 line-clamp-1 max-w-xs" title="{{ $item->catatan_review_terakhir }}">
                    ⚠️ Catatan: {{ $item->catatan_review_terakhir }}
                  </div>
                @endif
              </td>

              <td class="py-3.5 px-4 text-center">
                <div class="inline-flex items-center gap-1.5">
                  <a href="{{ route('sigap-siipid.show', $item->uuid) }}" 
                     class="px-2.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 font-semibold transition" title="Lihat Detail">
                    Detail
                  </a>

                  @if($item->is_editable)
                    <a href="{{ route('sigap-siipid.edit', $item->uuid) }}" 
                       class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold transition shadow-2xs" title="Edit / Perbaiki">
                      Perbaiki
                    </a>
                  @endif

                  @if($item->status === 'draft')
                    <form action="{{ route('sigap-siipid.destroy', $item->uuid) }}" method="POST" onsubmit="return confirm('Hapus draft ini?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition" title="Hapus Draft">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-12 text-center text-gray-400">
                <div class="space-y-2">
                  <p class="text-3xl">📭</p>
                  <p class="font-semibold text-gray-600">Belum ada data prestasi yang diusulkan.</p>
                  <p class="text-xs">Klik tombol <strong>"Usulkan Prestasi Baru"</strong> untuk mulai mendata capaian inovasi Anda.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($prestasis->hasPages())
      <div class="p-4 border-t border-gray-100">
        {{ $prestasis->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
