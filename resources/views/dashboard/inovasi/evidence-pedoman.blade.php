@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6">
  <h1 class="text-2xl font-extrabold text-gray-900">📘 Pedoman Evidence</h1>
  <p class="text-sm text-gray-600">Panduan & contoh resmi pengisian evidence.</p>
</section>

<form method="POST"
      enctype="multipart/form-data"
      action="{{ route('evidence.pedoman.save') }}">
@csrf

<section class="max-w-7xl mx-auto px-4 pb-10 space-y-6">
@foreach($items as $i => $it)
  <div class="bg-white border rounded-2xl p-4">
    <div class="flex items-center gap-3 mb-2">
      <span class="w-8 h-8 rounded bg-maroon text-white flex items-center justify-center">
        {{ $it['no'] }}
      </span>
      <h3 class="font-semibold">{{ $it['indikator'] }}</h3>
    </div>

    <input type="hidden" name="no[]" value="{{ $it['no'] }}">

    <p class="text-sm text-gray-700 mb-2">
      {{ $it['deskripsi'] ?? 'Belum ada deskripsi.' }}
    </p>

    @if($it['file_url'])
      <iframe src="{{ $it['file_url'] }}"
              class="w-full h-[420px] border rounded mb-2"></iframe>

      <div class="flex gap-2">
        <a href="{{ $it['file_url'] }}" download
           class="px-3 py-1 rounded bg-maroon text-white text-sm">
          Download
        </a>

        @role('admin')
        <button type="button"
                onclick="deletePedoman({{ $it['id'] }})"
                class="px-3 py-1 rounded border text-sm text-rose-600">
        Hapus File
        </button>
        @endrole

      </div>
    @else
      <p class="text-sm text-gray-400">Belum ada contoh file.</p>
    @endif

    @role('admin')
      <div class="mt-3 grid gap-2">
        <input type="text" name="indikator[]" value="{{ $it['indikator'] }}"
               class="rounded border p-2">
        <textarea name="deskripsi[]" rows="2"
                  class="rounded border p-2">{{ $it['deskripsi'] }}</textarea>
        <input type="file" name="file[{{ $i }}]" accept=".pdf">
      </div>
    @endrole
  </div>
@endforeach

@role('admin')
  <div class="text-right">
    <button class="px-5 py-2 rounded-lg bg-maroon text-white">
      Simpan Pedoman
    </button>
  </div>
@endrole
</section>
</form>
<form id="deletePedomanForm" method="POST">
  @csrf
  @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
    function deletePedoman(id) {
    if (!confirm('Hapus file pedoman ini?')) return;

    const form = document.getElementById('deletePedomanForm');
    form.action = `/sigap-inovasi/pedoman-evidence/${id}`;
    form.submit();
    }
</script>

@endpush