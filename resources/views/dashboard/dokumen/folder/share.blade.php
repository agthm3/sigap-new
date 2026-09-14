@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Bagikan Tautan Folder</h1>
      <p class="text-sm text-gray-600 mt-1">
        Bagikan folder <strong>{{ $folder->name }}</strong> dengan proteksi kata sandi dan batas kedaluwarsa.
      </p>
    </div>
    <a href="{{ route('sigap-dokumen.saya') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">
      Kembali
    </a>
  </div>

  <div class="space-y-6">
    <!-- Status Tautan Aktif -->
    @if($folder->share_token)
      <div class="p-5 bg-emerald-50 border border-emerald-200 rounded-2xl space-y-3" x-data="{ copied: false }">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Tautan Berbagi Aktif</span>
          <span class="text-xs text-emerald-700">
            Masa Berlaku: 
            <strong>
              {{ $folder->share_expires_at ? $folder->share_expires_at->translatedFormat('d M Y, H:i') . ' WITA' : 'Selamanya (Unlimited)' }}
            </strong>
          </span>
        </div>

        <div class="flex items-center gap-2">
          <input type="text" 
                 readonly 
                 value="{{ route('sigap-dokumen.shared.view', $folder->share_token) }}" 
                 id="shareUrlInput"
                 class="w-full bg-white border border-emerald-300 rounded-lg p-2.5 text-xs font-mono text-gray-800">
          
          <button type="button" 
                  @click="navigator.clipboard.writeText(document.getElementById('shareUrlInput').value); copied = true; setTimeout(() => copied = false, 2000)"
                  class="px-4 py-2.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shrink-0 transition">
            <span x-show="!copied">Salin Tautan</span>
            <span x-show="copied">Tersalin! ✓</span>
          </button>
        </div>

        <div class="flex items-center justify-between pt-2 border-t border-emerald-200 text-xs">
          <span class="text-emerald-700">
            Proteksi Sandi: <strong>{{ !empty($folder->share_password) ? 'Aktif 🔒' : 'Tidak Aktif' }}</strong>
          </span>

          <form action="{{ route('sigap-dokumen.folder.share.revoke', $folder) }}" method="POST" onsubmit="return confirm('Nonaktifkan tautan berbagi?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-700 hover:underline font-bold">Nonaktifkan Tautan</button>
          </form>
        </div>
      </div>
    @endif

    <!-- Form Pengaturan Berbagi -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6">
      <h2 class="text-sm font-bold text-gray-800 mb-4">
        {{ $folder->share_token ? 'Ubah Pengaturan Tautan' : 'Buat Tautan Berbagi Baru' }}
      </h2>

      <form action="{{ route('sigap-dokumen.folder.share.update', $folder) }}" method="POST" class="space-y-4">
        @csrf

        <!-- Masa Kedaluwarsa -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Masa Kedaluwarsa Tautan</label>
          <p class="text-xs text-gray-500 mb-2">Tautan akan mati otomatis setelah durasi berikut:</p>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
            <label class="p-3 border rounded-xl cursor-pointer text-center hover:bg-gray-50 has-[:checked]:border-maroon has-[:checked]:bg-maroon/5 text-xs font-bold">
              <input type="radio" name="duration" value="1_hour" class="sr-only">
              1 Jam
            </label>
            <label class="p-3 border rounded-xl cursor-pointer text-center hover:bg-gray-50 has-[:checked]:border-maroon has-[:checked]:bg-maroon/5 text-xs font-bold">
              <input type="radio" name="duration" value="1_day" class="sr-only" checked>
              1 Hari
            </label>
            <label class="p-3 border rounded-xl cursor-pointer text-center hover:bg-gray-50 has-[:checked]:border-maroon has-[:checked]:bg-maroon/5 text-xs font-bold">
              <input type="radio" name="duration" value="7_days" class="sr-only">
              7 Hari
            </label>
            <label class="p-3 border rounded-xl cursor-pointer text-center hover:bg-gray-50 has-[:checked]:border-maroon has-[:checked]:bg-maroon/5 text-xs font-bold">
              <input type="radio" name="duration" value="unlimited" class="sr-only">
              Unlimited
            </label>
          </div>
        </div>

        <!-- Proteksi Kata Sandi -->
        <div>
          <label class="block text-sm font-semibold text-gray-700">Kata Sandi Akses (Opsional)</label>
          <input type="password" 
                 name="password" 
                 placeholder="{{ !empty($folder->share_password) ? 'Biarkan kosong jika tidak ingin mengubah sandi' : 'Masukkan sandi rahasia...' }}" 
                 class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
          <p class="text-xs text-gray-500 mt-1">Siapa pun yang membuka tautan wajib memasukkan kata sandi ini untuk melihat berkas.</p>
        </div>

        <div class="pt-4 border-t flex justify-end">
          <button type="submit" class="px-6 py-2.5 rounded-lg bg-maroon text-white font-bold text-sm hover:bg-maroon-800 transition">
            {{ $folder->share_token ? 'Perbarui Tautan' : 'Buat Tautan Berbagi' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection