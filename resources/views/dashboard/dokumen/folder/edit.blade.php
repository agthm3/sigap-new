@extends('layouts.app')

@section('content')
<section class="max-w-3xl mx-auto px-4 py-6">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-900">Ubah Folder</h1>
      <p class="text-sm text-gray-600 mt-1">Perbarui nama, kode Permendagri, warna, dan status folder.</p>
    </div>
    <a href="{{ route('sigap-dokumen.saya') }}"
       class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium hover:bg-gray-50 transition">
      Kembali
    </a>
  </div>

  <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-6" x-data="editFolderForm()">
    <form action="{{ route('sigap-dokumen.folder.update', $folder) }}" method="POST" class="space-y-5">
      @csrf
      @method('PUT')

      <!-- Format Permendagri -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Format Nama Folder (Permendagri 83/2022)</label>
        <select @change="applyClassification($event.target.value)" class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
          <option value="">-- Ganti ke Klasifikasi Permendagri --</option>
          @foreach($permendagriList as $item)
            <option value="{{ $item['code'] }} - {{ $item['name'] }}">
              {{ $item['code'] }} - {{ $item['name'] }}
            </option>
          @endforeach
        </select>
      </div>

      <!-- Nama Folder -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Nama Folder <span class="text-red-500">*</span></label>
        <input type="text" 
               name="name" 
               x-model="folderName" 
               required 
               class="mt-1.5 w-full rounded-lg border border-gray-300 p-2.5 text-sm focus:border-maroon focus:ring-maroon">
        <input type="hidden" name="classification_code" x-model="classificationCode">
      </div>

      <!-- Ikon -->
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

      <!-- Warna -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Warna Aksen</label>
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

      <!-- Visibilitas -->
      <!-- Status Akses Folder (3-Level) -->
      <div>
        <label class="block text-sm font-semibold text-gray-700">Tingkat Kerahasiaan &amp; Akses Folder</label>
        <div class="grid sm:grid-cols-3 gap-3 mt-1.5">
          <!-- 1. Internal BRIDA -->
          <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer hover:bg-gray-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
            <div>
              <div class="flex items-center justify-between">
                <span class="text-base">🏢</span>
                <input type="radio" name="visibility" value="internal" @checked($folder->visibility === 'internal') class="text-blue-600 focus:ring-blue-600">
              </div>
              <p class="text-xs font-bold text-gray-900 mt-2">Internal BRIDA</p>
              <p class="text-[11px] text-gray-500 mt-1">Hanya pegawai login. Aman dari portal luar warga.</p>
            </div>
          </label>

          <!-- 2. Publik Terbuka -->
          <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer hover:bg-gray-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
            <div>
              <div class="flex items-center justify-between">
                <span class="text-base">🌐</span>
                <input type="radio" name="visibility" value="public" @checked($folder->visibility === 'public') class="text-emerald-600 focus:ring-emerald-600">
              </div>
              <p class="text-xs font-bold text-gray-900 mt-2">Publik Terbuka</p>
              <p class="text-[11px] text-gray-500 mt-1">Terbuka bebas untuk umum &amp; portal luar.</p>
            </div>
          </label>

          <!-- 3. Privat -->
          <label class="flex flex-col justify-between p-3.5 rounded-xl border cursor-pointer hover:bg-gray-50 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/50">
            <div>
              <div class="flex items-center justify-between">
                <span class="text-base">🔒</span>
                <input type="radio" name="visibility" value="private" @checked($folder->visibility === 'private') class="text-red-600 focus:ring-red-600">
              </div>
              <p class="text-xs font-bold text-gray-900 mt-2">Privat / Rahasia</p>
              <p class="text-[11px] text-gray-500 mt-1">Hanya Anda (bisa di-share via link + sandi).</p>
            </div>
          </label>
        </div>
      </div>

      <div class="pt-4 border-t flex justify-end gap-2">
        <a href="{{ route('sigap-dokumen.saya') }}" class="px-4 py-2 text-sm rounded-lg border hover:bg-gray-50">Batal</a>
        <button type="submit" class="px-5 py-2 text-sm rounded-lg bg-maroon text-white hover:bg-maroon-800 font-semibold">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</section>

<script>
function editFolderForm() {
  return {
    folderName: @js($folder->name),
    classificationCode: @js($folder->classification_code ?? ''),
    selectedIcon: @js($folder->icon ?? '📁'),
    selectedColor: @js($folder->color ?? '#7a2222'),
    icons: ['📁', '📂', '📑', '📊', '💼', '📜', '🏛️', '💰'],
    colors: ['#7a2222', '#2563eb', '#059669', '#d97706', '#7c3aed', '#db2777', '#374151'],
    applyClassification(value) {
      if (!value) return;
      this.folderName = value;
      this.classificationCode = value.split(' - ')[0] || '';
    }
  };
}
</script>
@endsection