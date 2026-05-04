@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    $isPrivileged = $user->hasAnyRole(['admin', 'verif_ppd']);

    $lembarItems = $isPrivileged
        ? $kegiatan->lembar
        : $kegiatan->lembar->where('user_id', $user->id);

    $groupedLembar = $lembarItems->groupBy('user_id');
@endphp

<section class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between mb-4">
  <div>
    <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
      Detail <span class="text-maroon">Kegiatan PPD</span>
    </h1>
    <p class="text-sm text-gray-600 mt-0.5">
      Kelola deskripsi per lembar dan upload 6 foto untuk tiap lembar laporan.
    </p>
  </div>

  <div class="flex flex-wrap gap-2">
    <a href="{{ route('sigap-ppd.index') }}"
       class="px-4 py-2 rounded-xl border border-gray-300 text-sm font-semibold hover:bg-gray-50">
      Kembali
    </a>

    <a href="{{ route('sigap-ppd.export-pdf', $kegiatan->id) }}"
       class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
      Export PDF
    </a>
  </div>
</section>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
  <div class="xl:col-span-1 space-y-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <div class="flex items-start justify-between gap-3">
        <div>
          <p class="text-xs uppercase tracking-wide text-gray-500">Judul Kegiatan</p>
          <h2 class="text-lg font-extrabold text-gray-900 leading-snug mt-1">
            {{ $kegiatan->judul }}
          </h2>
        </div>

        <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border
          {{ $kegiatan->kategori === 'bimtek' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
          {{ strtoupper($kegiatan->kategori) }}
        </span>
      </div>

      <div class="mt-4 space-y-3 text-sm">
        <div class="flex justify-between gap-3">
          <span class="text-gray-500">Hari / Tanggal</span>
          <span class="font-medium text-gray-900 text-right">{{ $kegiatan->hari_tanggal }}</span>
        </div>

        <div class="flex justify-between gap-3">
          <span class="text-gray-500">Tempat</span>
          <span class="font-medium text-gray-900 text-right">{{ $kegiatan->tempat }}</span>
        </div>

        <div class="flex justify-between gap-3">
          <span class="text-gray-500">Jumlah Lembar</span>
          <span class="font-medium text-gray-900">{{ $kegiatan->jumlah_lembar }}</span>
        </div>

        <div class="flex justify-between gap-3">
          <span class="text-gray-500">Status</span>
          <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] border bg-gray-50 border-gray-200 text-gray-700">
            {{ strtoupper($kegiatan->status) }}
          </span>
        </div>

        <div class="flex justify-between gap-3">
          <span class="text-gray-500">Dibuat Oleh</span>
          <span class="font-medium text-gray-900 text-right">
            {{ $kegiatan->creator->name ?? '-' }}
          </span>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
      <h3 class="font-semibold text-gray-900 mb-3">Pegawai Terlibat</h3>

      <div class="flex flex-wrap gap-2">
        @forelse($kegiatan->pegawai as $pegawai)
          <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-sm text-gray-700">
            <span class="w-2 h-2 rounded-full bg-maroon"></span>
            {{ $pegawai->name }}
            @if($pegawai->nip)
              <span class="text-xs text-gray-500">({{ $pegawai->nip }})</span>
            @endif
          </span>
        @empty
          <span class="text-sm text-gray-500">Belum ada pegawai yang ditugaskan.</span>
        @endforelse
      </div>
    </div>
  </div>

  <div class="xl:col-span-2 space-y-4">
    @forelse($groupedLembar as $pegawaiId => $lembarGroup)
      @php
        $pegawai = $lembarGroup->first()->user;
      @endphp

      <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b bg-gray-50 flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
          <div>
            <h3 class="font-semibold text-gray-900">
              {{ $pegawai->name ?? 'Pegawai' }}
            </h3>
            <p class="text-xs text-gray-500">
              {{ $pegawai->nip ?? '-' }}
            </p>
          </div>

          <div class="text-xs text-gray-500">
            Total lembar: <span class="font-semibold text-gray-900">{{ $lembarGroup->count() }}</span>
          </div>
        </div>

        <div class="p-4 space-y-4">
          @foreach($lembarGroup->sortBy('lembar_ke') as $lembar)
            <div class="rounded-2xl border border-gray-200 overflow-hidden">
              <div class="px-4 py-3 border-b bg-white flex items-center justify-between gap-3">
                <div>
                  <h4 class="font-semibold text-gray-900">
                    Lembar {{ $lembar->lembar_ke }}
                  </h4>
                  <p class="text-xs text-gray-500">
                    Isi deskripsi dan upload 6 foto untuk lembar ini.
                  </p>
                </div>

                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] border bg-gray-50 border-gray-200 text-gray-700">
                  {{ $lembar->fotos->count() }}/6 Foto
                </span>
              </div>

              <div class="p-4">
                @if($isPrivileged || $pegawaiId == $user->id)
                  <form action="{{ route('sigap-ppd.lembar.store', $lembar->id) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-4">
                    @csrf

                    <div>
                      <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Deskripsi Lembar
                      </label>
                      <textarea name="deskripsi"
                                rows="3"
                                class="w-full rounded-xl border-gray-300 focus:border-maroon focus:ring-maroon"
                                placeholder="Jelaskan isi 6 foto pada lembar ini...">{{ old('deskripsi', $lembar->deskripsi) }}</textarea>
                      @error('deskripsi')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                      @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                      @for($i = 1; $i <= 6; $i++)
                        @php
                            $foto = $lembar->fotos->firstWhere('urutan', $i);
                            $previewId = 'preview-' . $lembar->id . '-' . $i;
                            $placeholderId = 'placeholder-' . $lembar->id . '-' . $i;
                        @endphp

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-3">
                          <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Foto {{ $i }}
                          </label>

                          <input type="file"
                                name="foto_{{ $i }}"
                                accept="image/*"
                                data-preview-target="{{ $previewId }}"
                                data-placeholder-target="{{ $placeholderId }}"
                                class="ppd-photo-input block w-full text-sm text-gray-700 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-maroon file:text-white hover:file:bg-maroon-800">

                          <div class="mt-3">
                            <img id="{{ $previewId }}"
                                src="{{ $foto ? asset('storage/' . $foto->foto_path) : '' }}"
                                class="w-full h-32 object-cover rounded-xl border border-gray-200 bg-white {{ $foto ? '' : 'hidden' }}"
                                alt="preview foto {{ $i }}">

                            <div id="{{ $placeholderId }}"
                                class="w-full h-32 rounded-xl border border-dashed border-gray-300 bg-white flex items-center justify-center text-xs text-gray-400 {{ $foto ? 'hidden' : '' }}">
                              Belum ada foto
                            </div>
                          </div>
                        </div>
                      @endfor
                    </div>

                    <div class="flex items-center justify-end">
                      <button type="submit"
                              class="px-4 py-2 rounded-xl bg-maroon text-white text-sm font-semibold hover:bg-maroon-800">
                        Simpan Lembar
                      </button>
                    </div>
                  </form>
                @else
                  <div class="text-sm text-gray-500">
                    Lembar ini hanya bisa dilihat oleh pegawai yang ditugaskan.
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center text-gray-500 shadow-sm">
        Belum ada lembar yang bisa ditampilkan.
      </div>
    @endforelse
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    @if(session('success'))
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json(session('success')),
        confirmButtonText: 'OK'
      });
    @endif

    @if(session('warning_duplicate'))
      Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: @json(session('warning_duplicate')),
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
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.ppd-photo-input').forEach(function (input) {
      input.addEventListener('change', function () {
        const file = this.files && this.files[0];
        const previewId = this.dataset.previewTarget;
        const placeholderId = this.dataset.placeholderTarget;

        const previewImg = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);

        if (!previewImg || !placeholder) return;

        if (file) {
          const url = URL.createObjectURL(file);
          previewImg.src = url;
          previewImg.classList.remove('hidden');
          placeholder.classList.add('hidden');
        } else {
          previewImg.src = '';
          previewImg.classList.add('hidden');
          placeholder.classList.remove('hidden');
        }
      });
    });
  });
</script>
@endpush