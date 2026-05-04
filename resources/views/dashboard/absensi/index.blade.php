@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto space-y-4 pb-24">
  <section class="space-y-1">
    <h1 class="text-2xl font-extrabold text-gray-900">
      SIGAP <span class="text-maroon">ABSENSI</span>
    </h1>
    <p class="text-sm text-gray-600 leading-relaxed">
      Absensi dilakukan dari kamera depan. Lokasi absensi adalah Balaikota Makassar.
    </p>
  </section>

  @if(session('success'))
    <div class="rounded-xl border border-green-200 bg-green-50 text-green-800 p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 p-3 text-sm">
      {{ session('error') }}
    </div>
  @endif

  @if($myTodayAbsensi)
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
      Anda sudah absen hari ini pada jam <strong>{{ $myTodayAbsensi->absen_time }}</strong>.
    </div>
  @endif

  <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
    <div class="flex items-center justify-between mb-3">
      <div>
        <h2 class="font-semibold text-gray-900">Kamera Depan</h2>
        <p class="text-xs text-gray-500">Pastikan wajah terlihat di frame</p>
      </div>
      <span class="text-[11px] px-2 py-1 rounded-full bg-maroon-50 text-maroon border border-maroon-100">
        Front Camera
      </span>
    </div>

    <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-black">
      <video
        id="cameraPreview"
        autoplay
        playsinline
        class="w-full aspect-[3/4] object-cover"
      ></video>

      <div class="pointer-events-none absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 via-transparent to-black/25"></div>

        <div class="absolute inset-x-0 top-3 flex justify-center">
          <div class="px-3 py-1.5 rounded-full bg-maroon/90 text-white text-[11px] font-semibold">
            Posisikan wajah di tengah frame
          </div>
        </div>

        <div class="absolute inset-0 flex items-center justify-center">
          <div class="w-[72%] max-w-[260px] aspect-[3/4] rounded-[2rem] border-[3px] border-maroon/90 shadow-[0_0_0_9999px_rgba(0,0,0,0.18)]"></div>
        </div>

        <div class="absolute inset-x-0 bottom-3 flex justify-center">
          <div class="px-3 py-1 rounded-full bg-white/90 text-maroon text-[11px] font-bold">
            SIGAP ABSENSI
          </div>
        </div>
      </div>
    </div>

    <canvas id="captureCanvas" class="hidden"></canvas>

    <div class="grid grid-cols-2 gap-2 mt-3">
      <button
        id="captureBtn"
        type="button"
        class="w-full px-4 py-3 rounded-xl bg-maroon text-white text-sm font-semibold active:scale-[0.99]"
        {{ $myTodayAbsensi ? 'disabled' : '' }}
      >
        Ambil Foto
      </button>

      <button
        id="retakeBtn"
        type="button"
        class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm font-semibold hidden"
      >
        Ulangi
      </button>
    </div>

    <p class="text-[11px] text-gray-500 mt-2 leading-relaxed">
      Kamera depan wajib aktif. Tidak ada upload file dari galeri.
    </p>
  </div>

  <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-gray-900">Detail Absensi</h2>
      <span class="text-[11px] px-2 py-1 rounded-full bg-gray-100 text-gray-700">
        HADIR
      </span>
    </div>

    <form id="absensiForm" action="{{ route('sigap-absensi.store') }}" method="POST" class="space-y-3">
      @csrf

      <input type="hidden" name="photo_base64" id="photo_base64">
      <input type="hidden" name="latitude" id="latitude">
      <input type="hidden" name="longitude" id="longitude">

      <div>
        <label class="block mb-1 text-xs font-semibold text-gray-600">Lokasi</label>
        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm">
          <div class="font-semibold text-gray-800">Balaikota Makassar</div>
          <div id="locationStatus" class="text-xs text-gray-500 mt-1">
            Menunggu GPS...
          </div>
          <div id="radiusInfo" class="text-xs mt-1 font-semibold hidden"></div>
        </div>
      </div>

      <div>
        <label class="block mb-1 text-xs font-semibold text-gray-600">Keterangan</label>
        <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-3 text-sm font-semibold text-maroon">
          HADIR
        </div>
      </div>

      <button
        type="submit"
        id="submitBtn"
        class="w-full px-4 py-3 rounded-xl bg-maroon text-white text-sm font-semibold disabled:opacity-50"
        {{ $myTodayAbsensi ? 'disabled' : '' }}
      >
        Simpan Absensi
      </button>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', async () => {
  const video = document.getElementById('cameraPreview');
  const canvas = document.getElementById('captureCanvas');
  const captureBtn = document.getElementById('captureBtn');
  const retakeBtn = document.getElementById('retakeBtn');
  const form = document.getElementById('absensiForm');
  const photoInput = document.getElementById('photo_base64');
  const latInput = document.getElementById('latitude');
  const lngInput = document.getElementById('longitude');
  const locationStatus = document.getElementById('locationStatus');
  const radiusInfo = document.getElementById('radiusInfo');

  const centerLat = {{ config('sigap_absensi.center_lat') }};
  const centerLng = {{ config('sigap_absensi.center_lng') }};
  const radiusMeter = {{ config('sigap_absensi.radius_meter') }};

  function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) *
      Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: { ideal: 'user' }
      },
      audio: false
    });

    video.srcObject = stream;
  } catch (e) {
    locationStatus.innerText = 'Kamera tidak dapat diakses.';
    captureBtn.disabled = true;
  }

  navigator.geolocation.getCurrentPosition(
    (position) => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      latInput.value = lat;
      lngInput.value = lng;

      const distance = calculateDistance(lat, lng, centerLat, centerLng);

      if (distance > radiusMeter) {
        radiusInfo.classList.remove('hidden');
        radiusInfo.className = 'text-xs mt-1 font-semibold text-rose-700';
        radiusInfo.innerText = 'Anda berada di luar radius Balaikota Makassar';
      } else {
        radiusInfo.classList.remove('hidden');
        radiusInfo.className = 'text-xs mt-1 font-semibold text-emerald-700';
        radiusInfo.innerText = 'Anda berada di dalam radius Balaikota Makassar';
      }

      locationStatus.innerText = `GPS aktif: ${lat}, ${lng}`;
    },
    () => {
      locationStatus.innerText = 'GPS tidak aktif. Izinkan lokasi agar absensi bisa disimpan.';
    },
    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
  );

  function drawMaroonFrame(ctx, width, height) {
    ctx.fillStyle = 'rgba(122, 34, 34, 0.12)';
    ctx.fillRect(0, 0, width, height);

    ctx.strokeStyle = '#7a2222';
    ctx.lineWidth = 18;
    ctx.strokeRect(9, 9, width - 18, height - 18);

    const frameW = width * 0.68;
    const frameH = height * 0.80;
    const frameX = (width - frameW) / 2;
    const frameY = (height - frameH) / 2;

    ctx.strokeStyle = '#7a2222';
    ctx.lineWidth = 6;
    ctx.setLineDash([18, 12]);
    ctx.strokeRect(frameX, frameY, frameW, frameH);
    ctx.setLineDash([]);

    ctx.fillStyle = 'rgba(122, 34, 34, 0.92)';
    ctx.fillRect(18, 18, 250, 42);

    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 20px Arial';
    ctx.fillText('SIGAP ABSENSI', 34, 46);

    ctx.fillStyle = 'rgba(255,255,255,0.90)';
    ctx.fillRect(18, height - 62, 390, 40);

    ctx.fillStyle = '#7a2222';
    ctx.font = 'bold 16px Arial';
    ctx.fillText('Wajah harus berada di dalam frame', 34, height - 36);
  }

  captureBtn.addEventListener('click', () => {
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    drawMaroonFrame(context, canvas.width, canvas.height);

    const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
    photoInput.value = dataUrl;

    captureBtn.textContent = 'Foto Tersimpan';
    retakeBtn.classList.remove('hidden');
  });

  retakeBtn.addEventListener('click', () => {
    photoInput.value = '';
    captureBtn.textContent = 'Ambil Foto';
    retakeBtn.classList.add('hidden');
  });

  form.addEventListener('submit', (e) => {
    if (!photoInput.value) {
      e.preventDefault();
      alert('Silakan ambil foto terlebih dahulu.');
      return;
    }

    if (!latInput.value || !lngInput.value) {
      e.preventDefault();
      alert('GPS belum aktif.');
      return;
    }
  });
});
</script>
@endpush