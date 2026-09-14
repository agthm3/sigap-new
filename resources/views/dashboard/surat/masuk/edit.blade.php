@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="mb-4 flex items-center justify-between">
    <div>
      <h1 class="text-xl font-extrabold text-gray-900">Edit Surat Masuk</h1>
      <p class="text-sm text-gray-600">Agenda No. <span class="font-mono font-bold text-maroon">{{ sprintf('%03d', $surat->nomor_agenda) }}</span> / Tahun {{ $surat->tahun }}</p>
    </div>
    <a href="{{ route('sigap-surat.masuk.index') }}" class="px-3 py-1.5 rounded-xl border text-xs text-gray-600 hover:bg-gray-50">
      ← Kembali
    </a>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm" x-data="suratMasukEditForm()">
    <form action="{{ route('sigap-surat.masuk.update', $surat->id) }}" method="POST" enctype="multipart/form-data" @submit="submitForm($event)">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Tanggal Terima -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Diterima Tanggal *</label>
          <input type="date" name="tanggal_terima" value="{{ old('tanggal_terima', $surat->tanggal_terima->format('Y-m-d')) }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border-gray-300" required>
        </div>

        <!-- Tingkat Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat / Sifat Surat *</label>
          <select name="tingkat_surat" class="w-full rounded-xl px-3.5 py-2.5 text-sm border-gray-300" required>
            <option value="Biasa" {{ old('tingkat_surat', $surat->tingkat_surat) === 'Biasa' ? 'selected' : '' }}>Biasa</option>
            <option value="Penting" {{ old('tingkat_surat', $surat->tingkat_surat) === 'Penting' ? 'selected' : '' }}>Penting</option>
            <option value="Segera" {{ old('tingkat_surat', $surat->tingkat_surat) === 'Segera' ? 'selected' : '' }}>Segera</option>
            <option value="Penting / Segera" {{ old('tingkat_surat', $surat->tingkat_surat) === 'Penting / Segera' ? 'selected' : '' }}>Penting / Segera</option>
            <option value="Rahasia" {{ old('tingkat_surat', $surat->tingkat_surat) === 'Rahasia' ? 'selected' : '' }}>Rahasia</option>
          </select>
        </div>

        <!-- Surat Dari -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Surat Dari (Asal Surat / Pengirim) *</label>
          <input type="text" name="asal_surat" value="{{ old('asal_surat', $surat->asal_surat) }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border-gray-300" required>
        </div>

        <!-- Nomor Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Surat Masuk *</label>
          <input type="text" name="nomor_surat_masuk" value="{{ old('nomor_surat_masuk', $surat->nomor_surat_masuk) }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border-gray-300" required>
        </div>

        <!-- Tanggal Surat -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Surat *</label>
          <input type="date" name="tanggal_surat" value="{{ old('tanggal_surat', $surat->tanggal_surat->format('Y-m-d')) }}"
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm border-gray-300" required>
        </div>

        <!-- Perihal -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Perihal / Ringkasan Isi *</label>
          <textarea name="perihal" rows="3" class="w-full rounded-xl px-3.5 py-2 text-sm border-gray-300" required>{{ old('perihal', $surat->perihal) }}</textarea>
        </div>

        <!-- Unit Pengolah -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Unit Pengolah *</label>
          <select name="unit_pengolah" class="w-full rounded-xl px-3.5 py-2.5 text-sm border-gray-300" required>
            @foreach(['Sekretariat', 'Subbagian Umum & Kepegawaian', 'Subbagian Keuangan & Perencanaan', 'Bidang Riset', 'Bidang Inovasi'] as $unit)
              <option value="{{ $unit }}" {{ old('unit_pengolah', $surat->unit_pengolah) === $unit ? 'selected' : '' }}>{{ $unit }}</option>
            @endforeach
          </select>
        </div>

        <!-- Penerima -->
        <div>
          <label class="block text-xs font-semibold text-gray-700 mb-1">Diterima Oleh</label>
          <input type="text" value="{{ $surat->penerima->name ?? '-' }}" readonly
                 class="w-full rounded-xl px-3.5 py-2.5 text-sm bg-gray-100 text-gray-600 cursor-not-allowed">
        </div>

        <!-- Berkas Surat -->
        <div class="md:col-span-2">
          <label class="block text-xs font-semibold text-gray-700 mb-1">Unggah Ganti Berkas PDF (Opsional)</label>
          @if($surat->file_surat)
            <div class="mb-2 flex items-center gap-2 text-xs">
              <span class="text-gray-500">Berkas saat ini:</span>
              <a href="{{ asset('storage/' . $surat->file_surat) }}" target="_blank" class="text-maroon font-semibold hover:underline">
                📄 Lihat File PDF
              </a>
            </div>
          @endif
          <input type="file" name="file_surat" accept="application/pdf"
                 class="w-full rounded-xl px-3 py-2 text-xs border border-gray-200">
          <p class="text-[11px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti berkas yang ada.</p>
        </div>

        <!-- Tanda Tangan Canvas -->
        <div class="md:col-span-2 pt-2 border-t border-gray-100">
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-semibold text-gray-700">Tanda Tangan Penerima</label>
            @if($surat->ttd_penerima)
              <button type="button" @click="isDrawingNew = !isDrawingNew; if(isDrawingNew) $nextTick(() => initCanvas())"
                      class="text-xs text-maroon font-semibold hover:underline"
                      x-text="isDrawingNew ? 'Batal Ganti TTD' : '✏️ Ganti Coretan TTD'">
              </button>
            @endif
          </div>

          @if($surat->ttd_penerima)
            <div x-show="!isDrawingNew" class="p-3 bg-gray-50 border rounded-xl flex items-center gap-3">
              <img src="{{ $surat->ttd_penerima }}" alt="TTD Saat Ini" class="h-14 max-w-[120px] object-contain bg-white rounded border p-1">
              <span class="text-xs text-gray-600">Tanda tangan yang tersimpan saat ini.</span>
            </div>
          @endif

          <div x-show="isDrawingNew || {{ $surat->ttd_penerima ? 'false' : 'true' }}" class="space-y-2">
            <div class="flex justify-end">
              <button type="button" @click="clearSignature()" class="text-xs text-maroon hover:underline">
                Bersihkan Coretan
              </button>
            </div>
            <div class="border-2 border-dashed border-gray-300 rounded-xl overflow-hidden bg-gray-50 flex justify-center p-2">
              <canvas id="signatureCanvasEdit" width="400" height="140" class="bg-white rounded-lg shadow-2xs border border-gray-200 touch-none"></canvas>
            </div>
          </div>

          <input type="hidden" name="ttd_penerima" id="ttd_penerima">
        </div>
      </div>

      <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t">
        <a href="{{ route('sigap-surat.masuk.index') }}" class="px-4 py-2 rounded-xl border text-sm text-gray-600 hover:bg-gray-50">
          Batal
        </a>
        <button type="submit" class="px-5 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
function suratMasukEditForm() {
  return {
    isDrawingNew: false,
    canvas: null,
    ctx: null,
    isDrawing: false,
    hasDrawn: false,

    init() {
      @if(!$surat->ttd_penerima)
        this.isDrawingNew = true;
        this.$nextTick(() => this.initCanvas());
      @endif
    },

    initCanvas() {
      this.canvas = document.getElementById('signatureCanvasEdit');
      if (!this.canvas) return;
      this.ctx = this.canvas.getContext('2d');
      this.ctx.lineWidth = 2;
      this.ctx.lineCap = 'round';
      this.ctx.strokeStyle = '#1e293b';

      const startDraw = (e) => {
        this.isDrawing = true;
        this.hasDrawn = true;
        const rect = this.canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        this.ctx.beginPath();
        this.ctx.moveTo(clientX - rect.left, clientY - rect.top);
      };

      const draw = (e) => {
        if (!this.isDrawing) return;
        e.preventDefault();
        const rect = this.canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        this.ctx.lineTo(clientX - rect.left, clientY - rect.top);
        this.ctx.stroke();
      };

      const stopDraw = () => { this.isDrawing = false; };

      this.canvas.addEventListener('mousedown', startDraw);
      this.canvas.addEventListener('mousemove', draw);
      window.addEventListener('mouseup', stopDraw);

      this.canvas.addEventListener('touchstart', startDraw, { passive: false });
      this.canvas.addEventListener('touchmove', draw, { passive: false });
      this.canvas.addEventListener('touchend', stopDraw);
    },

    clearSignature() {
      if (!this.ctx) return;
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      this.hasDrawn = false;
      document.getElementById('ttd_penerima').value = '';
    },

    submitForm(e) {
      if (this.isDrawingNew && this.hasDrawn && this.canvas) {
        document.getElementById('ttd_penerima').value = this.canvas.toDataURL('image/png');
      }
    }
  }
}
</script>
@endpush