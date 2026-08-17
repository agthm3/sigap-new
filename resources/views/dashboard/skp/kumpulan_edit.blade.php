@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="kumpulanEditApp()">

  {{-- Header --}}
  <div class="flex items-center justify-between border-b pb-4">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">✏️ Edit Kumpulan SKP</h1>
      <p class="text-xs text-gray-500 mt-0.5">Edit judul atau ubah centang kegiatan yang ingin dilampirkan.</p>
    </div>
    <a href="{{ route('sigap-skp.kumpulan.index') }}" class="px-4 py-2 rounded-xl border text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors">
      Batal & Kembali
    </a>
  </div>

  <form @submit.prevent="submitEditForm" class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm space-y-5">
    
    {{-- Info Kategori & Bulan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-xs font-bold text-gray-700 mb-1">Kategori SKP</label>
        <input type="text" value="{{ $kategori }}" readonly class="w-full text-xs rounded-xl border-gray-300 p-2.5 bg-gray-100 text-gray-500 cursor-not-allowed">
      </div>
      
      <div>
        <label class="block text-xs font-bold text-gray-700 mb-1">Bulan & Tahun</label>
        <input type="month" value="{{ $bulanTahun }}" readonly class="w-full text-xs rounded-xl border-gray-300 p-2.5 bg-gray-100 text-gray-500 cursor-not-allowed">
      </div>
    </div>

    {{-- Judul Kumpulan --}}
    <div>
      <label class="block text-xs font-bold text-gray-700 mb-1">Judul Kumpulan Laporan <span class="text-red-500">*</span></label>
      <input type="text" x-model="form.judul_kumpulan" required placeholder="Contoh: Rekap Kinerja Tupoksi Bulan Ini..." class="w-full text-xs rounded-xl border-gray-300 p-2.5 focus:border-maroon focus:ring-maroon">
    </div>

    <hr class="border-gray-100">

    {{-- PILIH DOKUMENTASI SKP DENGAN FOTO & TANDA STATUS --}}
    <div>
      <div class="flex items-center justify-between mb-2.5">
        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
          <span>📸</span> Bukti Kegiatan SKP ({{ count($skpList) }})
        </h3>
        <span class="text-[11px] text-gray-500" x-text="form.skp_ids.length + ' Dipilih'"></span>
      </div>

      @if(count($skpList) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto pr-2 scrollbar-thin">
          @foreach($skpList as $skp)
            @php
              $firstFoto = $skp->fotos->first();
              $fotoUrl = $firstFoto ? asset('storage/' . $firstFoto->file_path) : null;
              $assignedKats = $usedSkpCategories[$skp->id] ?? [];
              $hasUsedOther = count($assignedKats) > 0;
              $katLabel = implode(', ', array_unique($assignedKats));
            @endphp
            <label class="relative flex flex-col p-2.5 border-2 rounded-2xl cursor-pointer transition-all hover:shadow-md {{ $hasUsedOther ? 'bg-amber-50/30' : 'bg-white' }}"
                   :class="form.skp_ids.includes({{ $skp->id }}) 
                      ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20' 
                      : '{{ $hasUsedOther ? 'border-amber-300 hover:border-amber-400' : 'border-gray-200 hover:border-gray-300' }}'">
              
              {{-- Thumbnail Foto --}}
              <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 mb-2 border border-gray-100">
                @if($fotoUrl)
                  <img src="{{ $fotoUrl }}" alt="{{ $skp->judul_kegiatan }}" class="w-full h-full object-cover">
                @elseif($skp->tipe_evidence === 'pdf')
                  <div class="w-full h-full flex flex-col items-center justify-center text-red-500 bg-red-50">
                    <span class="text-2xl">📄</span>
                    <span class="text-[10px] font-bold mt-1">DOKUMEN PDF</span>
                  </div>
                @else
                  <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                    No Image
                  </div>
                @endif

                {{-- Badge Tanggal --}}
                <span class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-black/60 text-white text-[9px] font-semibold backdrop-blur-sm">
                  {{ \Carbon\Carbon::parse($skp->tanggal)->format('d/m/Y') }}
                </span>

                {{-- Checkbox Floating --}}
                <div class="absolute top-1.5 right-1.5 z-10">
                  <input type="checkbox" value="{{ $skp->id }}" x-model="form.skp_ids" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 bg-white/90 shadow-sm border-gray-300">
                </div>

                {{-- Badge Status Terdaftar di Kumpulan Lain --}}
                @if($hasUsedOther)
                  <div class="absolute top-1.5 left-1.5 max-w-[75%]">
                    <span class="px-1.5 py-0.5 rounded bg-amber-500/90 text-white text-[8px] font-extrabold backdrop-blur-sm shadow truncate block" title="Sudah masuk kumpulan: {{ $katLabel }}">
                      Kumpulan: {{ Str::limit($katLabel, 14) }}
                    </span>
                  </div>
                @endif
              </div>

              {{-- Deskripsi Judul & Keterangan --}}
              <div class="flex items-center justify-between mb-0.5">
                <span class="text-[10px] text-gray-500 font-semibold">{{ \Carbon\Carbon::parse($skp->tanggal)->format('d/m/Y') }}</span>
                @if($hasUsedOther)
                  <span class="text-[9px] font-bold text-amber-600">✓ Kategori Lain</span>
                @endif
              </div>
              <p class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight">{{ $skp->judul_kegiatan }}</p>
            </label>
          @endforeach
        </div>
      @else
        <p class="text-xs text-gray-400 italic bg-gray-50 p-4 rounded-xl text-center">
          Tidak ada kegiatan SKP di bulan ini.
        </p>
      @endif
    </div>

    {{-- PILIH DOKUMENTASI PPD DENGAN FOTO & TANDA STATUS --}}
    @if(count($ppdList) > 0)
    <div>
      <div class="flex items-center justify-between mb-2.5">
        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-1.5">
          <span>📝</span> Laporan PPD ({{ count($ppdList) }})
        </h3>
        <span class="text-[11px] text-gray-500" x-text="form.ppd_ids.length + ' Dipilih'"></span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-80 overflow-y-auto pr-2 scrollbar-thin">
        @foreach($ppdList as $ppd)
          @php
            $firstPpdFoto = $ppd->lembar?->fotos?->first();
            $ppdFotoUrl = $firstPpdFoto ? asset('storage/' . $firstPpdFoto->file_path) : null;
            $assignedPpdKats = $usedPpdCategories[$ppd->id] ?? [];
            $hasUsedPpdOther = count($assignedPpdKats) > 0;
            $katPpdLabel = implode(', ', array_unique($assignedPpdKats));
          @endphp
          <label class="relative flex flex-col p-2.5 border-2 rounded-2xl cursor-pointer transition-all hover:shadow-md {{ $hasUsedPpdOther ? 'bg-amber-50/30' : 'bg-white' }}"
                 :class="form.ppd_ids.includes({{ $ppd->id }}) 
                    ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20' 
                    : '{{ $hasUsedPpdOther ? 'border-amber-300 hover:border-amber-400' : 'border-gray-200 hover:border-gray-300' }}'">
            
            {{-- Thumbnail Foto PPD --}}
            <div class="relative w-full aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 mb-2 border border-gray-100">
              @if($ppdFotoUrl)
                <img src="{{ $ppdFotoUrl }}" alt="{{ $ppd->judul_kegiatan }}" class="w-full h-full object-cover">
              @else
                <div class="w-full h-full flex flex-col items-center justify-center text-blue-500 bg-blue-50">
                  <span class="text-2xl">📋</span>
                  <span class="text-[10px] font-bold mt-1">LEMBAR PPD</span>
                </div>
              @endif

              <span class="absolute bottom-1.5 left-1.5 px-2 py-0.5 rounded-md bg-black/60 text-white text-[9px] font-semibold backdrop-blur-sm">
                {{ \Carbon\Carbon::parse($ppd->created_at)->format('d/m/Y') }}
              </span>

              <div class="absolute top-1.5 right-1.5 z-10">
                <input type="checkbox" value="{{ $ppd->id }}" x-model="form.ppd_ids" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 bg-white/90 shadow-sm border-gray-300">
              </div>

              @if($hasUsedPpdOther)
                <div class="absolute top-1.5 left-1.5 max-w-[75%]">
                  <span class="px-1.5 py-0.5 rounded bg-amber-500/90 text-white text-[8px] font-extrabold backdrop-blur-sm shadow truncate block" title="Sudah masuk kumpulan: {{ $katPpdLabel }}">
                    Kumpulan: {{ Str::limit($katPpdLabel, 14) }}
                  </span>
                </div>
              @endif
            </div>

            <div class="flex items-center justify-between mb-0.5">
              <span class="text-[10px] text-gray-500 font-semibold">{{ \Carbon\Carbon::parse($ppd->created_at)->format('d/m/Y') }}</span>
              @if($hasUsedPpdOther)
                <span class="text-[9px] font-bold text-amber-600">✓ Kategori Lain</span>
              @endif
            </div>
            <p class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight">{{ $ppd->judul_kegiatan ?? 'Kegiatan PPD' }}</p>
          </label>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Tombol Aksi --}}
    <div class="pt-3 border-t flex justify-end gap-2">
      <a href="{{ route('sigap-skp.kumpulan.index') }}" class="px-4 py-2.5 border rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-50">
        Batal
      </a>
      <button type="submit" :disabled="isLoading" class="px-6 py-2.5 bg-emerald-600 text-white font-bold text-xs rounded-xl shadow hover:bg-emerald-700 disabled:opacity-50 transition-colors">
        <span x-show="!isLoading">💾 Simpan Perubahan</span>
        <span x-show="isLoading">Menyimpan...</span>
      </button>
    </div>

  </form>
</div>
@endsection

@push('scripts')
<script>
function kumpulanEditApp() {
  return {
    isLoading: false,
    form: {
      kategori: '{{ $kumpulan->kategori }}',
      bulan_tahun: '{{ $kumpulan->bulan_tahun }}',
      judul_kumpulan: '{{ $kumpulan->judul_kumpulan }}',
      skp_ids: @json(array_map('intval', (array)($kumpulan->skp_ids ?? []))),
      ppd_ids: @json(array_map('intval', (array)($kumpulan->ppd_ids ?? [])))
    },
    
    async submitEditForm() {
      if (this.form.skp_ids.length === 0 && this.form.ppd_ids.length === 0) {
        Swal.fire('Perhatian', 'Pilih minimal 1 kegiatan SKP atau PPD!', 'warning');
        return;
      }

      this.isLoading = true;

      try {
        const response = await fetch("{{ route('sigap-skp.kumpulan.update', $kumpulan->slug) }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-HTTP-Method-Override": "PUT"
          },
          body: JSON.stringify({
            _method: 'PUT',
            ...this.form
          })
        });

        const rawText = await response.text();
        let data;
        try {
          data = JSON.parse(rawText);
        } catch (e) {
          throw new Error('Server mengembalikan respons non-JSON: ' + rawText.substring(0, 150));
        }

        if (response.ok && data.status === 'success') {
          Swal.fire({
            icon: 'success', 
            title: 'Berhasil!', 
            text: data.message, 
            timer: 1500, 
            showConfirmButton: false
          }).then(() => {
            window.location.href = data.redirect || "{{ route('sigap-skp.kumpulan.index') }}";
          });
        } else {
          const errorMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('<br>') : 'Gagal memperbarui data.');
          Swal.fire('Gagal Menyimpan', errorMsg, 'error');
        }
      } catch (err) {
        console.error(err);
        Swal.fire('Error Server', err.message || 'Terjadi kesalahan sistem saat menyimpan data.', 'error');
      } finally {
        this.isLoading = false;
      }
    }
  }
}
</script>
@endpush