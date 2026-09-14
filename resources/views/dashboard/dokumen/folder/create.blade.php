@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Buat Folder Baru</h1>
      <p class="text-sm text-gray-600 mt-1">Konfigurasi nama, kode Permendagri, warna, dan ikon folder.</p>
    </div>
    <a href="{{ $parentId ? route('sigap-dokumen.folder.show', $parentId) : route('sigap-dokumen.saya') }}"
       class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">
      Kembali
    </a>
  </div>

  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6" x-data="folderForm()">
    <form action="{{ route('sigap-dokumen.folder.store') }}" method="POST" class="space-y-5">
      @csrf
      @if($parentId)
        <input type="hidden" name="parent_id" value="{{ $parentId }}">
      @endif

      <!-- 1. Format Nama Permendagri 83/2022 -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Format Nama Folder (Permendagri 83/2022)</label>
        <select @change="applyClassification($event.target.value)" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
          <option value="">-- Pilih Klasifikasi Permendagri --</option>
          @foreach($permendagriList as $item)
            <option value="{{ $item['code'] }} - {{ $item['name'] }}" data-code="{{ $item['code'] }}">
              {{ $item['code'] }} - {{ $item['name'] }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Memilih kode ini akan otomatis mengisi kolom nama folder di bawah.</p>
      </div>

      <!-- 2. Nama Folder -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">
          Nama Folder <span class="text-red-500">*</span>
        </label>
        <input type="text" 
               name="name" 
               x-model="folderName"
               required 
               class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon" 
               placeholder="Contoh: 900 - KEUANGAN atau Arsip Surat Masuk 2026">
        <input type="hidden" name="classification_code" x-model="classificationCode">
      </div>

      <!-- 3. Simbol / Icon Folder -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Simbol / Ikon</label>
        <div class="grid grid-cols-5 sm:grid-cols-8 gap-2 mt-2">
          <template x-for="ic in icons" :key="ic">
            <button type="button" 
                    @click="selectedIcon = ic"
                    :class="selectedIcon === ic ? 'border-maroon ring-2 ring-maroon/20 bg-maroon/5' : 'border-gray-200 hover:bg-gray-50'"
                    class="h-10 rounded-lg border flex items-center justify-center text-base transition">
              <span x-text="ic"></span>
            </button>
          </template>
        </div>
        <input type="hidden" name="icon" x-model="selectedIcon">
      </div>

      <!-- 4. Warna Folder -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Warna Aksen Folder</label>
        <div class="flex items-center gap-3 mt-2">
          <template x-for="c in colors" :key="c">
            <button type="button" 
                    @click="selectedColor = c"
                    :style="'background-color:' + c"
                    :class="selectedColor === c ? 'ring-2 ring-offset-2 ring-gray-600 scale-110' : ''"
                    class="w-7 h-7 rounded-full transition-transform"></button>
          </template>
        </div>
        <input type="hidden" name="color" x-model="selectedColor">
      </div>

      <!-- Status Akses Folder -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Status Akses Folder <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 gap-3 mt-1.5">
          <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-maroon has-[:checked]:bg-maroon/5">
            <input type="radio" name="visibility" value="public" checked class="text-maroon focus:ring-maroon">
            <div>
              <p class="text-sm font-semibold text-gray-800">Folder Publik</p>
              <p class="text-[11px] text-gray-500">Tampil di Dokumen Umum & Dokumen Saya.</p>
            </div>
          </label>

          <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition hover:bg-gray-50 has-[:checked]:border-maroon has-[:checked]:bg-maroon/5">
            <input type="radio" name="visibility" value="private" class="text-maroon focus:ring-maroon">
            <div>
              <p class="text-sm font-semibold text-gray-800">Folder Privat</p>
              <p class="text-[11px] text-gray-500">Hanya tampil di Dokumen Saya (Terkunci).</p>
            </div>
          </label>
        </div>
      </div>

      <div class="pt-4 border-t flex justify-end gap-2">
        <a href="{{ $parentId ? route('sigap-dokumen.folder.show', $parentId) : route('sigap-dokumen.saya') }}" 
           class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50 transition">
          Batal
        </a>
        <button type="submit" class="px-5 py-2 text-sm rounded-lg bg-maroon text-white hover:bg-maroon-800 transition font-semibold">
          Simpan Folder
        </button>
      </div>
    </form>
  </div>
</section>

<script>
function folderForm() {
  return {
    folderName: '',
    classificationCode: '',
    selectedIcon: '📁',
    selectedColor: '#7a2222',
    icons: ['📁', '📂', '📑', '📊', '💼', '📜', '🏛️', '💰'],
    colors: ['#7a2222', '#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#374151'],
    applyClassification(value) {
      if(!value) return;
      this.folderName = value;
      const parts = value.split(' - ');
      this.classificationCode = parts[0] || '';
    }
  }
}
</script>
@endsection