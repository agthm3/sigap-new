@extends('layouts.page')

@section('content')
<section class="bg-gradient-to-br from-maroon via-maroon-800 to-maroon-900">
  <div class="max-w-7xl mx-auto px-4 py-8">
    <div class="bg-white rounded-2xl shadow-xl p-5 sm:p-6">
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
          <h1 class="text-2xl font-extrabold text-maroon">{{ $document->title }}</h1>
          <p class="text-sm text-gray-600 mt-1">Detail dokumen publik SIGAP DOKUMEN.</p>
        </div>
        <a href="{{ route('home.show') }}" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">
          Kembali ke Hasil
        </a>
      </div>

      <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl overflow-hidden">
          <div class="px-5 py-4 border-b">
            <h2 class="text-lg font-bold text-gray-800">Preview Dokumen</h2>
          </div>
          <div class="aspect-[4/3] bg-gray-100 flex items-center justify-center">
            @if($isPdf)
              <iframe src="{{ $fileUrl }}" class="w-full h-full" frameborder="0"></iframe>
            @elseif($isImage)
              <img src="{{ $fileUrl }}" class="w-full h-full object-cover" alt="Preview dokumen">
            @else
              <p class="text-sm text-gray-500">Preview tidak tersedia.</p>
            @endif
          </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
          <div class="px-5 py-4 border-b">
            <h2 class="text-lg font-bold text-gray-800">Informasi Dokumen</h2>
          </div>
          <div class="p-5 space-y-3 text-sm">
            <p><span class="font-semibold">Judul:</span> {{ $document->title }}</p>
            <p><span class="font-semibold">Alias:</span> {{ $document->alias }}</p>
            <p><span class="font-semibold">Kategori:</span> {{ $document->category }}</p>
            <p><span class="font-semibold">Tahun:</span> {{ $document->year }}</p>
            <p><span class="font-semibold">Pihak Terkait:</span> {{ $document->stakeholder }}</p>
            <p><span class="font-semibold">Status:</span> {{ $document->sensitivity }}</p>
            <p><span class="font-semibold">Deskripsi:</span> {{ $document->description }}</p>
          </div>

          <div class="px-5 pb-5" id="download">
            @auth
              <button type="button" onclick="startDownload('')"
                 class="block w-full px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
                Download Dokumen
              </button>
            @else
              <button type="button" onclick="openGuestModal()"
                 class="block w-full px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 transition">
                Download Dokumen
              </button>
            @endauth
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b">
      <h2 class="text-lg font-bold text-gray-800">Preview Log Akses</h2>
      <p class="text-sm text-gray-500 mt-1">Menampilkan 5 aktivitas terakhir pada dokumen ini.</p>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b">
          <tr class="text-left">
            <th class="px-4 py-2">Nama Pengguna</th>
            <th class="px-4 py-2">Tanggal Akses</th>
            <th class="px-4 py-2">Jenis Akses</th>
            <th class="px-4 py-2">Alasan</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse ($logs as $log)
            <tr>
              <td class="px-4 py-2">
                {{ $log->user_name ?? 'Tamu' }}
                @if($log->user_role)
                  <span class="ml-1 text-xs text-gray-500">({{ $log->user_role }})</span>
                @endif
              </td>
              <td class="px-4 py-2">
                {{ $log->created_at->timezone('Asia/Makassar')->format('d M Y • H:i') }}
              </td>
              <td class="px-4 py-2">
                @php
                  $label = [
                    'view' => 'View',
                    'download' => 'Download',
                    'create' => 'Create',
                    'update' => 'Update',
                    'delete' => 'Delete',
                    'access_denied' => 'Access Denied',
                  ][$log->action] ?? $log->action;
                @endphp
                {{ $label }}
                @if($log->success === false)
                  <span class="ml-2 px-2 py-0.5 rounded text-xs bg-red-50 text-red-700">Gagal</span>
                @endif
              </td>
              <td class="px-4 py-2">{{ $log->reason ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat akses.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</section>

@if(!auth()->check())
<div id="guestModal" class="fixed inset-0 z-50 hidden">
  <div class="absolute inset-0 bg-black/40" onclick="closeGuestModal()"></div>
  <div class="relative z-10 mx-auto max-w-md px-4 py-10">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
      <div class="px-5 py-4 bg-maroon text-white">
        <h2 class="text-lg font-bold">Masukkan Nama</h2>
        <p class="text-sm text-white/80">Nama ini akan dicatat pada log unduhan.</p>
      </div>

      <form onsubmit="event.preventDefault(); startDownload(document.getElementById('guest_name').value.trim());" class="p-5 space-y-4">
        <div>
          <label class="text-sm font-semibold text-gray-700">Nama</label>
          <input type="text" id="guest_name" class="mt-1.5 w-full rounded-lg border-gray-300 focus:border-maroon focus:ring-maroon" placeholder="Nama Anda" required>
        </div>

        <div class="flex justify-end gap-2">
          <button type="button" onclick="closeGuestModal()" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">
            Batal
          </button>
          <button type="submit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800">
            Lanjut Download
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  const isGuest = @json(!auth()->check());

  function openGuestModal() {
    document.getElementById('guestModal').classList.remove('hidden');
  }

  function closeGuestModal() {
    const modal = document.getElementById('guestModal');
    if (modal) modal.classList.add('hidden');
  }

  async function startDownload(guestName = '') {
    if (isGuest && !guestName) {
      Swal.fire({
        icon: 'warning',
        title: 'Nama belum diisi',
        text: 'Silakan isi nama terlebih dahulu.'
      });
      return;
    }

    Swal.fire({
      title: 'Memproses unduhan',
      text: 'File sedang di download...',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const response = await fetch(@json(route('home.document.download', $document)), {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': @json(csrf_token()),
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/octet-stream'
        },
        body: new URLSearchParams({
          guest_name: guestName
        })
      });

      if (!response.ok) {
        throw new Error('Gagal mengunduh file.');
      }

      const blob = await response.blob();
      const disposition = response.headers.get('Content-Disposition') || '';
      let filename = @json(($document->alias ?? $document->title) . '.' . pathinfo($document->file_path, PATHINFO_EXTENSION));

      const match = disposition.match(/filename\*=UTF-8''([^;]+)|filename="?([^"]+)"?/i);
      if (match) {
        filename = decodeURIComponent(match[1] || match[2]);
      }

      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      document.body.appendChild(a);
      a.click();
      a.remove();
      window.URL.revokeObjectURL(url);

      closeGuestModal();

      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'File sedang diunduh.',
        timer: 1400,
        showConfirmButton: false
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'File tidak dapat diunduh.'
      });
    }
  }
</script>
@endsection