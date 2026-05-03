@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
  .ql-toolbar.ql-snow{border-color:#e5e7eb}
  .ql-container.ql-snow{border-color:#e5e7eb;border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem}
  .ql-editor{min-height:180px}
  .error-text{font-size:12px;color:#b91c1c}

  /* Review status border helpers */
  .field-accept  { border-color: #16a34a !important; }
  .field-revisi  { border-color: #d97706 !important; }
  .field-tolak   { border-color: #dc2626 !important; }

  /* Quill wrapper borders */
  .ql-accept .ql-toolbar.ql-snow,
  .ql-accept .ql-container.ql-snow { border-color: #16a34a !important; }
  .ql-revisi .ql-toolbar.ql-snow,
  .ql-revisi .ql-container.ql-snow { border-color: #d97706 !important; }
  .ql-tolak  .ql-toolbar.ql-snow,
  .ql-tolak  .ql-container.ql-snow { border-color: #dc2626 !important; }
</style>

{{-- ── Helper macro: reviewMeta ── --}}
@php
/**
 * Ambil status & komentar review untuk satu field.
 * Prioritas: tolak > revisi > accept
 */
function reviewMeta(string $field, $reviewByField): array
{
    if (empty($reviewByField[$field])) {
        return ['status' => null, 'comments' => collect()];
    }

    $items    = $reviewByField[$field];
    $statuses = $items->pluck('status');

    if ($statuses->contains('tolak'))                          $status = 'tolak';
    elseif ($statuses->contains('revisi'))                     $status = 'revisi';
    elseif ($statuses->every(fn($s) => $s === 'accept'))       $status = 'accept';
    else                                                       $status = null;

    return [
        'status'   => $status,
        'comments' => $items->pluck('comment')->filter()->values(),
    ];
}

/**
 * Kelas border input berdasarkan status.
 */
function reviewBorderClass(?string $status): string
{
    return match($status) {
        'accept' => 'field-accept',
        'revisi' => 'field-revisi',
        'tolak'  => 'field-tolak',
        default  => '',
    };
}
@endphp

{{-- ─────────────────── HEADER ─────────────────── --}}
<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Edit Inovasi (Metadata)</h1>
      <p class="text-sm text-gray-600 mt-1">Lengkapi informasi dasar inovasi.</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('sigap-inovasi.show', $inovasi->id) }}"
         class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Batal</a>
      <button form="formEdit"
              class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan</button>
      <a href="{{ route('evidence.form', $inovasi->id) }}"
         class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Lanjut ke Evidence</a>
    </div>
  </div>
</section>

{{-- ─────────────────── REVIEWER SUMMARY ─────────────────── --}}
@if(isset($reviewers) && $reviewers->count())
<div class="max-w-7xl mx-auto px-4 mb-4">
  <div class="bg-white border rounded-2xl p-4 shadow">
    <h3 class="font-semibold text-gray-800 mb-2">Yang Telah Mereview</h3>
    <div class="flex flex-wrap gap-2">
      @foreach($reviewers as $items)
        @php $user = $items->first()->reviewer; @endphp
        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">
          👤 {{ $user->name }}
        </span>
      @endforeach
    </div>
  </div>
</div>
@endif

{{-- ─────────────────── BODY ─────────────────── --}}
<section class="max-w-7xl mx-auto px-4 pb-10">
  <div class="grid lg:grid-cols-4 gap-5">

    {{-- ══════════════ FORM ══════════════ --}}
    <form id="formEdit" class="lg:col-span-3 space-y-6"
          action="{{ route('sigap-inovasi.update', $inovasi->id) }}"
          method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      {{-- ── IDENTITAS ── --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Identitas Inovasi</h2>
        <div class="grid sm:grid-cols-2 gap-4">

          {{-- Judul --}}
          @php $meta = reviewMeta('judul', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Judul Inovasi
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <input name="judul" type="text" required
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}"
                   value="{{ old('judul', $inovasi->judul) }}">
            @include('components.review-comment', ['comments' => $meta['comments']])
            @error('judul') <p class="error-text">{{ $message }}</p> @enderror
          </label>

          {{-- OPD/Unit --}}
          @php $meta = reviewMeta('opd_unit', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              OPD/Unit
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <input name="opd_unit" type="text"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}"
                   value="{{ old('opd_unit', $inovasi->opd_unit) }}">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Inisiator Daerah --}}
          @php
            $meta    = reviewMeta('inisiator_daerah', $reviewByField ?? []);
            $optInis = [''=>'—','OPD'=>'OPD','Unit Kerja'=>'Unit Kerja','Kolaborasi'=>'Kolaborasi'];
          @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Inisiator (Daerah)
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="inisiator_daerah"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              @foreach($optInis as $val => $lbl)
                <option value="{{ $val }}" @selected(old('inisiator_daerah', $inovasi->inisiator_daerah) === $val)>{{ $lbl }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Nama Inisiator --}}
          @php $meta = reviewMeta('inisiator_nama', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Nama Inisiator
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <input name="inisiator_nama" type="text"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}"
                   value="{{ old('inisiator_nama', $inovasi->inisiator_nama) }}">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Koordinat --}}
          @php $meta = reviewMeta('koordinat', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Koordinat
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <input name="koordinat" type="text"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}"
                   value="{{ old('koordinat', $inovasi->koordinat) }}">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

        </div>
      </div>

      {{-- ── KLASIFIKASI & URUSAN ── --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Klasifikasi & Urusan</h2>
        @php
          $cfgAsta  = config('inovasi.asta_cipta', []);
          $cfgProg  = config('inovasi.program_prioritas', []);
          $cfgUrus  = config('inovasi.urusan_pemerintah', []);
          $optKlas  = [''=>'—','Inovasi Perangkat Daerah'=>'Inovasi Perangkat Daerah','Inovasi Desa dan Kelurahan'=>'Inovasi Desa dan Kelurahan','Inovasi Masyarakat'=>'Inovasi Masyarakat'];
          $optJenis = [''=>'—','Digital'=>'Digital','Non Digital'=>'Non Digital'];
          $optBentuk= [''=>'—','Inovasi Daerah lainnya sesuai urusan kewenangan'=>'Inovasi Daerah lainnya sesuai urusan kewenangan','Inovasi Pelayanan Publik'=>'Inovasi Pelayanan Publik','Inovasi Tata Kelola Pemerintah Daerah'=>'Inovasi Tata Kelola Pemerintah Daerah'];
        @endphp
        <div class="grid sm:grid-cols-2 gap-4">

          {{-- Klasifikasi --}}
          @php $meta = reviewMeta('klasifikasi', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Klasifikasi Inovasi
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="klasifikasi"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              @foreach($optKlas as $val => $lbl)
                <option value="{{ $val }}" @selected(old('klasifikasi', $inovasi->klasifikasi) === $val)>{{ $lbl }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Jenis Inovasi --}}
          @php $meta = reviewMeta('jenis_inovasi', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Jenis Inovasi
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="jenis_inovasi"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              @foreach($optJenis as $val => $lbl)
                <option value="{{ $val }}" @selected(old('jenis_inovasi', $inovasi->jenis_inovasi) === $val)>{{ $lbl }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Bentuk Inovasi Daerah --}}
          @php $meta = reviewMeta('bentuk_inovasi_daerah', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Bentuk Inovasi Daerah
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="bentuk_inovasi_daerah"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              @foreach($optBentuk as $val => $lbl)
                <option value="{{ $val }}" @selected(old('bentuk_inovasi_daerah', $inovasi->bentuk_inovasi_daerah) === $val)>{{ $lbl }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Asta Cipta --}}
          @php $meta = reviewMeta('asta_cipta', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Asta Cipta
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="asta_cipta"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              <option value="">—</option>
              @foreach($cfgAsta as $code => $label)
                <option value="{{ $code }}" @selected(old('asta_cipta', $inovasi->asta_cipta) === $code)>{{ $label }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Program Prioritas --}}
          @php $meta = reviewMeta('program_prioritas', $reviewByField ?? []); @endphp
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Program Prioritas Walikota
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="program_prioritas"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              <option value="">—</option>
              @foreach($cfgProg as $code => $label)
                <option value="{{ $code }}" @selected(old('program_prioritas', $inovasi->program_prioritas) === $code)>{{ $label }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Urusan Pemerintah --}}
          @php $meta = reviewMeta('urusan_pemerintah', $reviewByField ?? []); @endphp
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Urusan Pemerintah
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <select name="urusan_pemerintah"
                    class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}">
              <option value="">—</option>
              @foreach($cfgUrus as $u)
                <option value="{{ $u }}" @selected(old('urusan_pemerintah', $inovasi->urusan_pemerintah) === $u)>{{ $u }}</option>
              @endforeach
            </select>
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

        </div>
      </div>

      {{-- ── WAKTU PELAKSANAAN ── --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Waktu Pelaksanaan</h2>
        <div class="grid sm:grid-cols-2 gap-4">

          {{-- Waktu Uji Coba --}}
          @php $meta = reviewMeta('waktu_uji_coba', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Waktu Uji Coba
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <input name="waktu_uji_coba" type="date"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}"
                   value="{{ old('waktu_uji_coba', optional($inovasi->waktu_uji_coba)->format('Y-m-d')) }}">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

          {{-- Waktu Penerapan --}}
          @php $meta = reviewMeta('waktu_penerapan', $reviewByField ?? []); @endphp
          <label class="block">
            <span class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Waktu Penerapan
              @include('components.review-badge', ['status' => $meta['status']])
            </span>
            <input name="waktu_penerapan" type="date"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2 {{ reviewBorderClass($meta['status']) }}"
                   value="{{ old('waktu_penerapan', optional($inovasi->waktu_penerapan)->format('Y-m-d')) }}">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </label>

        </div>
      </div>

      {{-- ── DESKRIPSI DETAIL (Quill) ── --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Deskripsi Detail</h2>
        <div class="space-y-6">

          {{-- Rancang Bangun --}}
          @php $meta = reviewMeta('rancang_bangun', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-1.5">
              Rancang Bangun
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <div id="q_rancang" class="ql-{{ $meta['status'] ?? 'default' }}"></div>
            <input type="hidden" name="rancang_bangun" id="hid_rancang">
            @include('components.review-comment', ['comments' => $meta['comments']])
            @error('rancang_bangun') <p class="error-text">{{ $message }}</p> @enderror
          </div>

          {{-- Tujuan Inovasi --}}
          @php $meta = reviewMeta('tujuan', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-1.5">
              Tujuan Inovasi
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <div id="q_tujuan" class="ql-{{ $meta['status'] ?? 'default' }}"></div>
            <input type="hidden" name="tujuan" id="hid_tujuan">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

          {{-- Manfaat --}}
          @php $meta = reviewMeta('manfaat', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-1.5">
              Manfaat
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <div id="q_manfaat" class="ql-{{ $meta['status'] ?? 'default' }}"></div>
            <input type="hidden" name="manfaat" id="hid_manfaat">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

          {{-- Hasil Inovasi --}}
          @php $meta = reviewMeta('hasil_inovasi', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2 mb-1.5">
              Hasil Inovasi
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <div id="q_hasil" class="ql-{{ $meta['status'] ?? 'default' }}"></div>
            <input type="hidden" name="hasil_inovasi" id="hid_hasil">
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

        </div>
      </div>

      {{-- ── PENELITIAN / INOVASI TERDAHULU ── --}}
      @php $meta = reviewMeta('videos', $reviewByField ?? []); @endphp
      @php
        $refBorder = match($meta['status']) {
          'accept' => 'border-2 border-green-400',
          'revisi' => 'border-2 border-yellow-400',
          'tolak'  => 'border-2 border-red-400',
          default  => 'border border-gray-200',
        };
      @endphp
      <div class="bg-white rounded-2xl p-4 {{ $refBorder }}"> 
        <h2 class="font-semibold text-gray-800 mb-1 flex items-center gap-2">
          Penelitian / Inovasi Terdahulu
          <span class="text-xs text-gray-500 font-normal">(Minimal 3, Maksimal 5)</span>
          @include('components.review-badge', ['status' => $meta['status']])
        </h2>
        @include('components.review-comment', ['comments' => $meta['comments']])

        <div id="ref-wrapper" class="space-y-3 mt-3">
          @foreach($inovasi->referensiVideos as $i => $ref)
            <div class="ref-item border rounded-lg p-3">
              <input type="hidden" name="refs[{{ $i }}][id]" value="{{ $ref->id }}">
              <input type="text"
                name="refs[{{ $i }}][judul]"
                value="{{ old("refs.$i.judul", $ref->judul) }}"
                placeholder="Judul penelitian / inovasi"
                class="w-full mb-2 rounded-lg border-gray-300">
              <textarea
                name="refs[{{ $i }}][deskripsi]"
                rows="2"
                placeholder="Deskripsi singkat"
                class="w-full mb-2 rounded-lg border-gray-300"
              >{{ old("refs.$i.deskripsi", $ref->deskripsi) }}</textarea>
              <input type="url"
                name="refs[{{ $i }}][url]"
                value="{{ old("refs.$i.url", $ref->video_url) }}"
                placeholder="Link YouTube / Website"
                class="w-full rounded-lg border-gray-300">
            </div>
          @endforeach
        </div>

        <div class="flex gap-2 mt-3">
          <button type="button" id="addRef"
            class="px-3 py-1 text-sm border rounded hover:bg-gray-50">+ Tambah Referensi</button>
          <button type="button" id="removeRef"
            class="px-3 py-1 text-sm border rounded hover:bg-gray-50">− Hapus Terakhir</button>
        </div>
      </div>

      {{-- ── LAMPIRAN ── --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Lampiran</h2>
        <div class="grid sm:grid-cols-2 gap-4">

          {{-- Anggaran --}}
          @php $meta = reviewMeta('anggaran', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Anggaran (PDF)
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <input name="anggaran" type="file" accept=".pdf"
                   class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 px-2 py-1.5 {{ reviewBorderClass($meta['status']) }}">
            @if(!empty($inovasi->anggaran_file))
              <p class="text-xs mt-1">Saat ini:
                <a class="text-maroon underline" target="_blank"
                   href="{{ asset('storage/'.$inovasi->anggaran_file) }}">Lihat</a>
              </p>
            @endif
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

          {{-- Profil Bisnis --}}
          @php $meta = reviewMeta('profil_bisnis', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Profil Bisnis (.ppt/.pdf)
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <input name="profil_bisnis" type="file" accept=".ppt,.pptx,.pdf"
                   class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 px-2 py-1.5 {{ reviewBorderClass($meta['status']) }}">
            @if(!empty($inovasi->profil_bisnis_file))
              <p class="text-xs mt-1">Saat ini:
                <a class="text-maroon underline" target="_blank"
                   href="{{ asset('storage/'.$inovasi->profil_bisnis_file) }}">Lihat</a>
              </p>
            @endif
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

          {{-- HAKI --}}
          @php $meta = reviewMeta('haki', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Dokumen HAKI
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <input name="haki" type="file" accept=".pdf,.jpg,.jpeg,.png"
                   class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 px-2 py-1.5 {{ reviewBorderClass($meta['status']) }}">
            @if(!empty($inovasi->haki_file))
              <p class="text-xs mt-1">Saat ini:
                <a class="text-maroon underline" target="_blank"
                   href="{{ asset('storage/'.$inovasi->haki_file) }}">Lihat</a>
              </p>
            @endif
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

          {{-- Penghargaan --}}
          @php $meta = reviewMeta('penghargaan', $reviewByField ?? []); @endphp
          <div>
            <label class="text-sm font-semibold text-gray-700 flex items-center gap-2">
              Penghargaan
              @include('components.review-badge', ['status' => $meta['status']])
            </label>
            <input name="penghargaan" type="file" accept=".pdf,.jpg,.jpeg,.png"
                   class="mt-1.5 block w-full text-sm rounded-lg border border-gray-300 px-2 py-1.5 {{ reviewBorderClass($meta['status']) }}">
            @if(!empty($inovasi->penghargaan_file))
              <p class="text-xs mt-1">Saat ini:
                <a class="text-maroon underline" target="_blank"
                   href="{{ asset('storage/'.$inovasi->penghargaan_file) }}">Lihat</a>
              </p>
            @endif
            @include('components.review-comment', ['comments' => $meta['comments']])
          </div>

        </div>
      </div>

    </form>

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside class="lg:col-span-1">
      <div class="lg:sticky top-24 space-y-4">

        {{-- Ringkasan status review --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-4">
          <h3 class="font-semibold text-gray-800 mb-3">Ringkasan Review</h3>

          @php
            $allFields = [
              'judul','opd_unit','inisiator_daerah','inisiator_nama','koordinat',
              'klasifikasi','jenis_inovasi','bentuk_inovasi_daerah','asta_cipta',
              'program_prioritas','misi_walikota','urusan_pemerintah',
              'waktu_uji_coba','waktu_penerapan','perkembangan_inovasi',
              'rancang_bangun','tujuan','manfaat','hasil_inovasi',
              'videos','anggaran','profil_bisnis','haki','penghargaan',
            ];
            $countAccept = 0; $countRevisi = 0; $countTolak = 0;
            foreach($allFields as $f){
              $s = reviewMeta($f, $reviewByField ?? [])['status'];
              if($s === 'accept') $countAccept++;
              elseif($s === 'revisi') $countRevisi++;
              elseif($s === 'tolak') $countTolak++;
            }
          @endphp

          <div class="space-y-2 text-sm">
            <div class="flex items-center justify-between">
              <span class="text-green-700 font-medium">✅ Disetujui</span>
              <span class="font-bold text-green-700">{{ $countAccept }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-yellow-700 font-medium">✏️ Perlu Revisi</span>
              <span class="font-bold text-yellow-700">{{ $countRevisi }}</span>
            </div>
            <div class="flex items-center justify-between">
              <span class="text-red-700 font-medium">❌ Ditolak</span>
              <span class="font-bold text-red-700">{{ $countTolak }}</span>
            </div>
          </div>

          @if($countRevisi > 0 || $countTolak > 0)
            <p class="text-xs text-gray-500 mt-3">Perhatikan field yang ditandai merah/kuning dan perbaiki sesuai komentar reviewer.</p>
          @else
            <p class="text-xs text-gray-500 mt-3">Klik "Simpan" untuk memperbarui metadata inovasi.</p>
          @endif
        </div>

        {{-- Legend warna --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-4">
          <h3 class="font-semibold text-gray-800 mb-3 text-sm">Keterangan Warna</h3>
          <ul class="space-y-1.5 text-xs">
            <li class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
              <span>Hijau = Disetujui reviewer</span>
            </li>
            <li class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-yellow-500 inline-block"></span>
              <span>Kuning = Perlu revisi</span>
            </li>
            <li class="flex items-center gap-2">
              <span class="w-3 h-3 rounded-full bg-red-500 inline-block"></span>
              <span>Merah = Ditolak reviewer</span>
            </li>
          </ul>
        </div>

      </div>
    </aside>

  </div>
</section>

{{-- ─────────────────── SCRIPTS ─────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
  const quillModules = {
    toolbar: [
      [{ header: [1,2,false] }],
      ['bold','italic','underline'],
      [{ list:'ordered' }, { list:'bullet' }],
      ['link','clean']
    ]
  };

  const qR = new Quill('#q_rancang', { theme:'snow', modules: quillModules });
  const qT = new Quill('#q_tujuan',  { theme:'snow', modules: quillModules });
  const qM = new Quill('#q_manfaat', { theme:'snow', modules: quillModules });
  const qH = new Quill('#q_hasil',   { theme:'snow', modules: quillModules });

  qR.root.innerHTML = @json(old('rancang_bangun', $inovasi->rancang_bangun));
  qT.root.innerHTML = @json(old('tujuan',         $inovasi->tujuan));
  qM.root.innerHTML = @json(old('manfaat',        $inovasi->manfaat));
  qH.root.innerHTML = @json(old('hasil_inovasi',  $inovasi->hasil_inovasi));

  document.getElementById('formEdit').addEventListener('submit', function () {
    document.getElementById('hid_rancang').value = qR.root.innerHTML;
    document.getElementById('hid_tujuan').value  = qT.root.innerHTML;
    document.getElementById('hid_manfaat').value = qM.root.innerHTML;
    document.getElementById('hid_hasil').value   = qH.root.innerHTML;
  });
</script>

<script>
  let refIndex = {{ $inovasi->referensiVideos->count() }};
  const minRef = 3;
  const maxRef = 5;
  const refWrapper = document.getElementById('ref-wrapper');

  document.getElementById('addRef').addEventListener('click', () => {
    if (refIndex >= maxRef) { alert('Maksimal 5 referensi.'); return; }
    const div = document.createElement('div');
    div.className = 'ref-item border rounded-lg p-3';
    div.innerHTML = `
      <input type="text"
        name="refs[${refIndex}][judul]" required
        placeholder="Judul penelitian / inovasi"
        class="w-full mb-2 rounded-lg border-gray-300">
      <textarea
        name="refs[${refIndex}][deskripsi]" rows="2"
        placeholder="Deskripsi singkat"
        class="w-full mb-2 rounded-lg border-gray-300"></textarea>
      <input type="url"
        name="refs[${refIndex}][url]" required
        placeholder="Link YouTube / Website"
        class="w-full rounded-lg border-gray-300">
    `;
    refWrapper.appendChild(div);
    refIndex++;
  });

  document.getElementById('removeRef').addEventListener('click', () => {
    if (refIndex <= minRef) { alert('Minimal 3 referensi wajib ada.'); return; }
    refWrapper.lastElementChild.remove();
    refIndex--;
  });
</script>
@endsection