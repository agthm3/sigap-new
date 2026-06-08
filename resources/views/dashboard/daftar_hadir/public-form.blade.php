@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
  <div class="mb-4 text-center">
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">{{ $kegiatan->nama_kegiatan }}</h1>
    <p class="text-sm text-gray-600 mt-1">
      {{ $kegiatan->hari_tanggal }} • {{ $kegiatan->tempat }} • {{ $kegiatan->waktu }}
    </p>
  </div>

  @if(session('success_name'))
    <div class="mb-4"></div>
  @endif

  @if($kegiatan->status === 'selesai')
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-center">
      <h2 class="text-lg font-bold text-amber-800">Kegiatan sudah selesai</h2>
      <p class="text-sm text-amber-700 mt-1">QR ini sudah tidak menerima peserta baru.</p>
    </div>
  @else
    <form id="absen-form" action="{{ route('sigap-daftar-hadir.store-public', $kegiatan->uuid) }}" method="POST" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm space-y-4">
      @csrf

      <div class="relative">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
        <small>Disarankan menggunakan nama lengkap sesuai dengan identitas resmi + Gelar</small>
        <input type="text" id="nama-input" name="nama" value="{{ old('nama') }}"
               autocomplete="off"
               class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
        <input type="hidden" id="existing_ttd_path" name="existing_ttd_path" value="{{ old('existing_ttd_path') }}">

        <div id="nama-suggestions" class="absolute z-20 mt-1 w-full rounded-xl border bg-white shadow hidden max-h-64 overflow-auto"></div>

        @error('nama') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Instansi</label>
          <input type="text" id="instansi" name="instansi" value="{{ old('instansi') }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
          @error('instansi') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
          <select id="gender" name="gender" class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
            <option value="">-- pilih --</option>
            <option value="L" @selected(old('gender') === 'L')>Laki-laki</option>
            <option value="P" @selected(old('gender') === 'P')>Perempuan</option>
          </select>
          @error('gender') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
          <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon" required>
          @error('no_hp') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email (opsional)</label>
          <input type="email" id="email" name="email" value="{{ old('email') }}"
                 class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon">
          @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">TTD Digital</label>
          
          <div class="rounded-2xl border p-2 relative" id="canvas-container">
            <canvas id="signature-pad" class="w-full h-40"></canvas>
            
            <!-- Overlay saat Canvas dimatikan -->
            <div id="canvas-overlay" class="absolute inset-0 bg-gray-50 bg-opacity-80 hidden flex-col items-center justify-center rounded-2xl z-10 backdrop-blur-sm transition-all">
              <span class="text-sm font-semibold text-gray-600">Menggunakan TTD Lama</span>
            </div>
          </div>

          <input type="hidden" id="ttd_data" name="ttd_data">
          <div class="mt-2 flex gap-2">
            <button type="button" id="clear-signature"
                    class="px-3 py-1.5 rounded-lg border text-xs hover:bg-gray-50">
              Hapus TTD
            </button>

            <!-- Tombol Ganti TTD -->
            <button type="button" id="ganti-signature"
                    class="hidden px-3 py-1.5 rounded-lg border border-maroon text-maroon text-xs hover:bg-maroon-50 font-medium">
              Ganti TTD Baru
            </button>
          </div>
          @error('ttd_data') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Preview TTD Lama</label>
          <div class="rounded-2xl border p-3 min-h-40 flex items-center justify-center bg-gray-50">
            <img id="ttd-preview" src="" alt="Preview TTD" class="max-h-32 hidden">
            <p id="ttd-empty" class="text-sm text-gray-500">Belum ada TTD terpilih.</p>
          </div>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 rounded-xl bg-maroon text-white font-semibold hover:bg-maroon-800">
          Save
        </button>
      </div>
    </form>
  @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('absen-form');
  const canvas = document.getElementById('signature-pad');
  const clearBtn = document.getElementById('clear-signature');
  const gantiBtn = document.getElementById('ganti-signature');
  const canvasOverlay = document.getElementById('canvas-overlay');
  
  const namaInput = document.getElementById('nama-input');
  const suggestionBox = document.getElementById('nama-suggestions');
  const instansi = document.getElementById('instansi');
  const gender = document.getElementById('gender');
  const noHp = document.getElementById('no_hp');
  const email = document.getElementById('email');
  const existingTtdInput = document.getElementById('existing_ttd_path');
  const ttdDataInput = document.getElementById('ttd_data');
  const preview = document.getElementById('ttd-preview');
  const emptyPreview = document.getElementById('ttd-empty');

  const storageBase = @json(asset('storage'));

  let signaturePad = null;
  let resizeTimer = null;
  let debounceTimer = null;
  let lastItems = [];

  if (canvas) {
    signaturePad = new SignaturePad(canvas, {
      backgroundColor: 'rgb(255,255,255)'
    });
  }

  function resizeCanvas() {
    if (!signaturePad || !canvas) return;

    const data = signaturePad.isEmpty() ? null : signaturePad.toData();

    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);

    if (data) {
      signaturePad.fromData(data);
    }
  }

  function hideSuggestions() {
    if (!suggestionBox) return;
    suggestionBox.classList.add('hidden');
    suggestionBox.innerHTML = '';
    lastItems = [];
  }

  function showPreview(url) {
    if (!preview || !emptyPreview) return;

    if (url) {
      preview.src = url;
      preview.classList.remove('hidden');
      emptyPreview.classList.add('hidden');
    } else {
      preview.src = '';
      preview.classList.add('hidden');
      emptyPreview.classList.remove('hidden');
    }
  }

  // Fungsi untuk mengaktifkan/mereset kembali canvas TTD
  function enableCanvas() {
    if (signaturePad) {
      signaturePad.on();
      signaturePad.clear();
    }
    
    if (canvasOverlay) {
      canvasOverlay.classList.remove('flex');
      canvasOverlay.classList.add('hidden');
    }
    
    if (gantiBtn) gantiBtn.classList.add('hidden');
    if (clearBtn) clearBtn.classList.remove('hidden');

    existingTtdInput.value = '';
    showPreview('');
  }

  function fillFields(item) {
    namaInput.value = item.nama || '';
    instansi.value = item.instansi || '';
    gender.value = item.gender || '';
    noHp.value = item.no_hp || '';
    email.value = item.email || '';
    existingTtdInput.value = item.ttd_path || '';

    showPreview(item.ttd_path || '');

    if (signaturePad) {
      signaturePad.clear();
      
      if (item.ttd_path) {
        // Matikan fungsi gambar pada canvas
        signaturePad.off(); 
        
        // Munculkan overlay dan tombol "Ganti TTD Baru"
        if (canvasOverlay) {
          canvasOverlay.classList.remove('hidden');
          canvasOverlay.classList.add('flex');
        }
        if (gantiBtn) gantiBtn.classList.remove('hidden');
        if (clearBtn) clearBtn.classList.add('hidden');
      } else {
        enableCanvas(); // Kalau ternyata data lamanya gak punya TTD
      }
    }

    ttdDataInput.value = '';
  }

  function renderSuggestions(items) {
    lastItems = items || [];

    if (!suggestionBox) return;

    if (!items.length) {
      hideSuggestions();
      return;
    }

    suggestionBox.innerHTML = items.map((item, index) => `
      <button type="button"
              data-index="${index}"
              class="w-full text-left px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
        <div class="font-medium text-gray-900">${item.nama ?? '-'}</div>
        <div class="text-xs text-gray-500">
          ${item.instansi ?? '-'} • ${item.no_hp ?? '-'}
        </div>
      </button>
    `).join('');

    suggestionBox.classList.remove('hidden');

    suggestionBox.querySelectorAll('button[data-index]').forEach(btn => {
      btn.addEventListener('click', function () {
        const item = lastItems[parseInt(this.dataset.index, 10)];

        Swal.fire({
          icon: 'question',
          title: 'Pakai data peserta lama?',
          text: 'Anda tercatat pernah mengikuti kegiatan BRIDA. Mau isi otomatis?',
          showCancelButton: true,
          confirmButtonText: 'Ya, isi otomatis',
          cancelButtonText: 'Tidak'
        }).then((result) => {
          if (result.isConfirmed) {
            fillFields(item);
          }
          hideSuggestions();
        });
      });
    });
  }

  if (canvas) {
    resizeCanvas();

    window.addEventListener('resize', function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        resizeCanvas();
      }, 150);
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', function () {
      if (signaturePad) {
        signaturePad.clear();
      }
      ttdDataInput.value = '';
    });
  }

  if (gantiBtn) {
    gantiBtn.addEventListener('click', function() {
      enableCanvas();
    });
  }

  if (namaInput) {
    namaInput.addEventListener('input', function () {
      const q = this.value.trim();

      // Setiap kali diketik ulang, canvas harus selalu hidup kembali
      enableCanvas();

      if (debounceTimer) clearTimeout(debounceTimer);

      if (q.length < 2) {
        hideSuggestions();
        return;
      }

      debounceTimer = setTimeout(async () => {
        try {
          const url = `{{ route('sigap-daftar-hadir.search-peserta') }}?q=${encodeURIComponent(q)}`;
          const res = await fetch(url, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          });

          if (!res.ok) {
            hideSuggestions();
            return;
          }

          const items = await res.json();
          renderSuggestions(items);
        } catch (error) {
          hideSuggestions();
        }
      }, 250);
    });
  }

  document.addEventListener('click', function (e) {
    if (!suggestionBox || !namaInput) return;

    if (!suggestionBox.contains(e.target) && e.target !== namaInput) {
      hideSuggestions();
    }
  });

  if (form) {
    form.addEventListener('submit', function (e) {
      const hasExistingTtd = (existingTtdInput?.value || '').trim() !== '';
      const hasCanvasSignature = signaturePad ? !signaturePad.isEmpty() : false;

      if (hasCanvasSignature) {
        ttdDataInput.value = signaturePad.toDataURL('image/png');
      }

      if (!hasCanvasSignature && !hasExistingTtd) {
        e.preventDefault();
        Swal.fire({
          icon: 'warning',
          title: 'TTD belum ada',
          text: 'Silakan gambar tanda tangan terlebih dahulu atau pilih peserta lama.'
        });
      }
    });
  }

  @if(session('success_name') && session('success_kegiatan'))
    Swal.fire({
      icon: 'success',
      title: 'Halo, {{ session('success_name') }}',
      text: 'Selamat datang di acara {{ session('success_kegiatan') }}',
      confirmButtonText: 'OK'
    });
  @endif

  @if(session('error'))
    Swal.fire({
      icon: 'error',
      title: 'Gagal',
      text: @json(session('error')),
      confirmButtonText: 'OK'
    });
  @endif
});
</script>
@endpush