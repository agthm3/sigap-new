@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
  <div>
    <div class="flex items-center gap-2 flex-wrap">
      <h1 class="text-2xl font-extrabold text-gray-900">
        {{ $system->nama_sistem }}
      </h1>

      <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
        {{ $system->status === 'aktif'
            ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
            : ($system->status === 'maintenance'
                ? 'bg-amber-50 border-amber-200 text-amber-700'
                : 'bg-gray-50 border-gray-200 text-gray-700') }}">
        {{ strtoupper($system->status) }}
      </span>

      @if($system->kategori)
        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border bg-blue-50 border-blue-200 text-blue-700">
          {{ strtoupper($system->kategori) }}
        </span>
      @endif

      @if($system->level_kritis)
        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
          {{ $system->level_kritis === 'tinggi'
              ? 'bg-red-50 border-red-200 text-red-700'
              : ($system->level_kritis === 'sedang'
                  ? 'bg-yellow-50 border-yellow-200 text-yellow-700'
                  : 'bg-emerald-50 border-emerald-200 text-emerald-700') }}">
          KRITIS: {{ strtoupper($system->level_kritis) }}
        </span>
      @endif
    </div>

    <p class="text-sm text-gray-600 mt-2 max-w-3xl">
      {{ $system->deskripsi ?: 'Tidak ada deskripsi sistem.' }}
    </p>
  </div>

  <div class="flex gap-2 flex-wrap">
    @if($system->url)
      <a href="{{ $system->url }}"
         target="_blank"
         class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
        Buka Website
      </a>
    @endif

    @hasanyrole('admin|verif_pic')
      <a href="{{ route('sigap-pic.edit', $system->id) }}"
         class="px-4 py-2 rounded-xl border border-blue-300 text-blue-700 text-sm hover:bg-blue-50">
        Edit
      </a>
    @endhasanyrole

    <a href="{{ route('sigap-pic.index') }}"
       class="px-4 py-2 rounded-xl border border-gray-300 text-sm hover:bg-gray-50">
      Kembali
    </a>
  </div>
</div>

{{-- INFO CARD --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-5">
  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">PIC Utama</p>
    <h3 class="text-lg font-extrabold text-gray-900">
      {{ optional($system->assignments->where('is_primary', true)->first())->user->name
         ?? optional($system->assignments->where('is_primary', true)->first())->nama_pic
         ?? '-' }}
    </h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Jumlah PIC</p>
    <h3 class="text-lg font-extrabold text-maroon">
      {{ $system->assignments->count() }}
    </h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Jumlah Akun</p>
    <h3 class="text-lg font-extrabold text-blue-700">
      {{ $system->credentials->count() }}
    </h3>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-gray-500">Update Terakhir</p>
    <h3 class="text-lg font-extrabold text-gray-900">
      {{ $system->updated_at?->format('d M Y H:i') }}
    </h3>
  </div>
</div>

{{-- WEBSITE PREVIEW --}}
<div class="mt-5 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <h2 class="font-bold text-gray-900">Preview Website Sistem</h2>

    @if($system->url)
      <a href="{{ $system->url }}"
         target="_blank"
         class="text-sm text-maroon hover:underline">
        Buka Website
      </a>
    @endif
  </div>

  @if($system->url)
    <iframe
      src="{{ $system->url }}"
      class="w-full h-[500px] bg-white"
      loading="lazy"
      referrerpolicy="no-referrer"
      sandbox="allow-scripts allow-forms allow-same-origin allow-popups">
    </iframe>
  @else
    <div class="p-8 text-center text-gray-500">
      Belum ada URL website yang dimasukkan.
    </div>
  @endif
</div>

{{-- YOUTUBE --}}
@if($system->youtube_url)
<div class="mt-5 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  <div class="px-4 py-3 border-b bg-gray-50">
    <h2 class="font-bold text-gray-900">Tutorial Sistem</h2>
  </div>

  @php
    $youtubeUrl = $system->youtube_url;

    if (str_contains($youtubeUrl, 'watch?v=')) {
        preg_match('/v=([^&]+)/', $youtubeUrl, $matches);
        $videoId = $matches[1] ?? null;
        $embedUrl = $videoId ? 'https://www.youtube.com/embed/' . $videoId : null;
    } elseif (str_contains($youtubeUrl, 'youtu.be/')) {
        $videoId = last(explode('/', $youtubeUrl));
        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
    } else {
        $embedUrl = $youtubeUrl;
    }
  @endphp

  @if($embedUrl)
    <div class="aspect-video">
      <iframe
        src="{{ $embedUrl }}"
        class="w-full h-full"
        frameborder="0"
        allowfullscreen>
      </iframe>
    </div>
  @endif
</div>
@endif

{{-- PIC --}}
<div class="mt-5 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  <div class="px-4 py-3 border-b bg-gray-50">
    <h2 class="font-bold text-gray-900">PIC / Penanggung Jawab</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">No</th>
          <th class="px-4 py-3 text-left">Nama</th>
          <th class="px-4 py-3 text-left">NIP</th>
          <th class="px-4 py-3 text-left">Unit</th>
          <th class="px-4 py-3 text-left">Status</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">
        @forelse($system->assignments->sortBy('urutan') as $i => $pic)
          <tr>
            <td class="px-4 py-3 text-gray-500">{{ $i + 1 }}</td>

            <td class="px-4 py-3 font-medium text-gray-900">
              {{ $pic->user->name ?? $pic->nama_pic }}
            </td>

            <td class="px-4 py-3">
              {{ $pic->user->nip ?? $pic->pegawai_nik ?? '-' }}
            </td>

            <td class="px-4 py-3">
              {{ $pic->user->unit ?? $pic->bidang ?? '-' }}
            </td>

            <td class="px-4 py-3">
              @if($pic->is_primary)
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] bg-maroon text-white">
                  PIC UTAMA
                </span>
              @else
                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border border-gray-300 text-gray-700">
                  PIC
                </span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              Belum ada PIC terdaftar.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- CREDENTIAL --}}
<div class="mt-5 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
    <h2 class="font-bold text-gray-900">Akun / Credential</h2>
    @hasanyrole('admin|verif_pic')
      <span class="text-xs text-gray-500">Klik ikon mata untuk lihat password</span>
    @endhasanyrole
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">Nama Akun</th>
          <th class="px-4 py-3 text-left">Username</th>
          @hasanyrole('admin|verif_pic')
            <th class="px-4 py-3 text-left">Password</th>
          @endhasanyrole
          <th class="px-4 py-3 text-left">Email</th>
          <th class="px-4 py-3 text-left">Access</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Login</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">
        @forelse($system->credentials as $credential)
          <tr>
            <td class="px-4 py-3 font-medium text-gray-900">
              {{ $credential->nama_akun }}
              @if($credential->catatan)
                <p class="text-xs text-gray-400 mt-0.5">{{ $credential->catatan }}</p>
              @endif
            </td>

            <td class="px-4 py-3">
              {{ $credential->username ?: '-' }}
            </td>

            {{-- FIX: Password hanya tampil untuk admin/verif_pic, dengan toggle show/hide --}}
            @hasanyrole('admin|verif_pic')
              <td class="px-4 py-3">
                @if($credential->password_encrypted)
                  <div class="flex items-center gap-2">
                    <span class="password-mask font-mono text-xs tracking-widest">••••••••</span>
                    <span class="password-plain font-mono text-xs hidden">{{ $credential->password_encrypted }}</span>
                    <button type="button"
                            class="toggle-show-password text-gray-400 hover:text-maroon"
                            title="Tampilkan password">
                      <svg class="eye-open w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      <svg class="eye-close w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                      </svg>
                    </button>
                  </div>
                @else
                  <span class="text-gray-400">-</span>
                @endif
              </td>
            @endhasanyrole

            <td class="px-4 py-3">
              {{ $credential->email ?: '-' }}
            </td>

            <td class="px-4 py-3">
              {{ $credential->access_level ?: '-' }}
            </td>

            <td class="px-4 py-3">
              @if($credential->is_sensitive)
                <span class="inline-flex px-2 py-1 rounded-full text-[11px] bg-red-50 border border-red-200 text-red-700">
                  Sensitif
                </span>
              @else
                <span class="inline-flex px-2 py-1 rounded-full text-[11px] bg-emerald-50 border border-emerald-200 text-emerald-700">
                  Umum
                </span>
              @endif
            </td>

            <td class="px-4 py-3">
              @if($credential->url_login)
                <a href="{{ $credential->url_login }}"
                   target="_blank"
                   class="text-maroon hover:underline">
                  Buka
                </a>
              @else
                -
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
              Belum ada credential terdaftar.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- LOG AKTIVITAS --}}
<div class="mt-5 rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
  <div class="px-4 py-3 border-b bg-gray-50">
    <h2 class="font-bold text-gray-900">Log Aktivitas</h2>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">User</th>
          <th class="px-4 py-3 text-left">Aksi</th>
          <th class="px-4 py-3 text-left">Detail</th>
          <th class="px-4 py-3 text-left">IP</th>
          <th class="px-4 py-3 text-left">Waktu</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">
        @forelse($system->logs->sortByDesc('created_at') as $log)
          <tr>
            <td class="px-4 py-3">
              {{ $log->user->name ?? 'System' }}
            </td>

            <td class="px-4 py-3">
              <span class="inline-flex px-2 py-1 rounded-full text-[11px] border border-gray-300 text-gray-700">
                {{ $log->aksi }}
              </span>
            </td>

            <td class="px-4 py-3">
              {{ $log->detail }}
            </td>

            <td class="px-4 py-3">
              {{ $log->ip_address ?? '-' }}
            </td>

            <td class="px-4 py-3">
              {{ $log->created_at?->format('d M Y H:i') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-gray-500">
              Belum ada log aktivitas.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Toggle password visibility di tabel credential
  document.querySelectorAll('.toggle-show-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const cell     = this.closest('div');
      const mask     = cell.querySelector('.password-mask');
      const plain    = cell.querySelector('.password-plain');
      const eyeOpen  = this.querySelector('.eye-open');
      const eyeClose = this.querySelector('.eye-close');

      const isHidden = plain.classList.contains('hidden');
      plain.classList.toggle('hidden', !isHidden);
      mask.classList.toggle('hidden', isHidden);
      eyeOpen.classList.toggle('hidden', isHidden);
      eyeClose.classList.toggle('hidden', !isHidden);
    });
  });
});
</script>
@endpush
