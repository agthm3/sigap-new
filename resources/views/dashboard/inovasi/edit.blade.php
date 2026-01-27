@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
<style>
  .ql-toolbar.ql-snow{border-color:#e5e7eb}
  .ql-container.ql-snow{border-color:#e5e7eb;border-bottom-left-radius:.5rem;border-bottom-right-radius:.5rem}
  .ql-editor{min-height:180px}
  .error-text{font-size:12px;color:#b91c1c}
</style>

<section class="max-w-7xl mx-auto px-4 py-6">
  <div class="flex items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-gray-900">Edit Inovasi (Metadata)</h1>
      <p class="text-sm text-gray-600 mt-1">Lengkapi informasi dasar inovasi.</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('sigap-inovasi.show',$inovasi->id) }}" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50 text-sm">Batal</a>
      <button form="formEdit" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Simpan</button>
      <a href="{{ route('evidence.form',$inovasi->id) }}" class="px-4 py-2 rounded-lg bg-maroon text-white hover:bg-maroon-800 text-sm">Lanjut ke Evidence</a>
    </div>
  </div>
</section>

<section class="max-w-7xl mx-auto px-4 pb-10">
  <div class="grid lg:grid-cols-4 gap-5">
    <form id="formEdit" class="lg:col-span-3 space-y-6"
          action="{{ route('sigap-inovasi.update',$inovasi->id) }}"
          method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      {{-- Identitas --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Identitas Inovasi</h2>
        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Judul Inovasi <span class="text-rose-600">*</span></span>
            <input name="judul" type="text" required
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                   value="{{ old('judul',$inovasi->judul) }}">
            @error('judul') <p class="error-text">{{ $message }}</p> @enderror
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">OPD/Unit</span>
            <input name="opd_unit" type="text"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                   value="{{ old('opd_unit',$inovasi->opd_unit) }}">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Inisiator (Daerah)</span>
            <select name="inisiator_daerah" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              @php $optInis = [''=>'—','OPD'=>'OPD','Unit Kerja'=>'Unit Kerja','Kolaborasi'=>'Kolaborasi']; @endphp
              @foreach($optInis as $val=>$lbl)
                <option value="{{ $val }}" @selected(old('inisiator_daerah',$inovasi->inisiator_daerah)===$val)>{{ $lbl }}</option>
              @endforeach
            </select>
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Nama Inisiator</span>
            <input name="inisiator_nama" type="text"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                   value="{{ old('inisiator_nama',$inovasi->inisiator_nama) }}">
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Koordinat</span>
            <input name="koordinat" type="text"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                   value="{{ old('koordinat',$inovasi->koordinat) }}">
          </label>
        </div>
      </div>

        {{-- Klasifikasi & Urusan --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Klasifikasi & Urusan</h2>

        @php
          $cfgAsta = config('inovasi.asta_cipta', []);
          $cfgProg = config('inovasi.program_prioritas', []);
          $cfgUrus = config('inovasi.urusan_pemerintah', []);
          $optKlas = [''=>'—','Inovasi Perangkat Daerah'=>'Inovasi Perangkat Daerah','Inovasi Desa dan Kelurahan'=>'Inovasi Desa dan Kelurahan','Inovasi Masyarakat'=>'Inovasi Masyarakat'];
          $optJenis= [''=>'—','Digital'=>'Digital','Non Digital'=>'Non Digital'];
          $optBentuk=[''=>'—','Inovasi Daerah lainnya sesuai urusan kewenangan'=>'Inovasi Daerah lainnya sesuai urusan kewenangan','Inovasi Pelayanan Publik'=>'Inovasi Pelayanan Publik','Inovasi Tata Kelola Pemerintah Daerah'=>'Inovasi Tata Kelola Pemerintah Daerah'];
        @endphp

        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Klasifikasi Inovasi</span>
            <select name="klasifikasi" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              @foreach($optKlas as $val=>$lbl)
                <option value="{{ $val }}" @selected(old('klasifikasi',$inovasi->klasifikasi)===$val)>{{ $lbl }}</option>
              @endforeach
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Jenis Inovasi</span>
            <select name="jenis_inovasi" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              @foreach($optJenis as $val=>$lbl)
                <option value="{{ $val }}" @selected(old('jenis_inovasi',$inovasi->jenis_inovasi)===$val)>{{ $lbl }}</option>
              @endforeach
            </select>
          </label>

          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Bentuk Inovasi Daerah</span>
            <select name="bentuk_inovasi_daerah" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              @foreach($optBentuk as $val=>$lbl)
                <option value="{{ $val }}" @selected(old('bentuk_inovasi_daerah',$inovasi->bentuk_inovasi_daerah)===$val)>{{ $lbl }}</option>
              @endforeach
            </select>
          </label>

          {{-- ASTA CIPTA: simpan kode (asta1, dst) tapi tampil label --}}
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Asta Cipta</span>
            <select name="asta_cipta" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              <option value="">—</option>
              @foreach($cfgAsta as $code=>$label)
                <option value="{{ $code }}" @selected(old('asta_cipta',$inovasi->asta_cipta)===$code)>{{ $label }}</option>
              @endforeach
            </select>
          </label>

          {{-- PROGRAM PRIORITAS: simpan kode (progwalkot_1, dst) --}}
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Program Prioritas Walikota</span>
            <select name="program_prioritas" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              <option value="">—</option>
              @foreach($cfgProg as $code=>$label)
                <option value="{{ $code }}" @selected(old('program_prioritas',$inovasi->program_prioritas)===$code)>{{ $label }}</option>
              @endforeach
            </select>
          </label>

          {{-- URUSAN PEMERINTAH: pakai list panjang dari config --}}
          <label class="block sm:col-span-2">
            <span class="text-sm font-semibold text-gray-700">Urusan Pemerintah</span>
            <select name="urusan_pemerintah" class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2">
              <option value="">—</option>
              @foreach($cfgUrus as $u)
                <option value="{{ $u }}" @selected(old('urusan_pemerintah',$inovasi->urusan_pemerintah)===$u)>{{ $u }}</option>
              @endforeach
            </select>
          </label>
        </div>
      </div>

      {{-- Waktu --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Waktu Pelaksanaan</h2>
        <div class="grid sm:grid-cols-2 gap-4">
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Waktu Uji Coba</span>
            <input name="waktu_uji_coba" type="date"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                   value="{{ old('waktu_uji_coba', optional($inovasi->waktu_uji_coba)->format('Y-m-d')) }}">
          </label>
          <label class="block">
            <span class="text-sm font-semibold text-gray-700">Waktu Penerapan</span>
            <input name="waktu_penerapan" type="date"
                   class="mt-1.5 w-full rounded-lg border border-gray-300 focus:border-maroon focus:ring-maroon px-3 py-2"
                   value="{{ old('waktu_penerapan', optional($inovasi->waktu_penerapan)->format('Y-m-d')) }}">
          </label>
        </div>
      </div>

      {{-- Deskripsi (Quill) --}}
      <div class="bg-white border border-gray-200 rounded-2xl p-4">
        <h2 class="font-semibold text-gray-800 mb-4">Deskripsi Detail</h2>
        <div class="space-y-6">
          <div>
            <label class="text-sm font-semibold text-gray-700">Rancang Bangun</label>
            <div id="q_rancang"></div>
            <input type="hidden" name="rancang_bangun" id="hid_rancang">
            @error('rancang_bangun') <p class="error-text">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Tujuan Inovasi</label>
            <div id="q_tujuan"></div>
            <input type="hidden" name="tujuan" id="hid_tujuan">
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Manfaat</label>
            <div id="q_manfaat"></div>
            <input type="hidden" name="manfaat" id="hid_manfaat">
          </div>
          <div>
            <label class="text-sm font-semibold text-gray-700">Hasil Inovasi</label>
            <div id="q_hasil"></div>
            <input type="hidden" name="hasil_inovasi" id="hid_hasil">
          </div>
        </div>
      </div>
{{-- Penelitian / Inovasi Terdahulu --}}
<div class="bg-white border border-gray-200 rounded-2xl p-4">
  <h2 class="font-semibold text-gray-800 mb-4">
    Penelitian / Inovasi Terdahulu
    <span class="text-xs text-gray-500 font-normal">(Minimal 3, Maksimal 5)</span>
  </h2>

  <div id="ref-wrapper" class="space-y-3">
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
      class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
      + Tambah Referensi
    </button>

    <button type="button" id="removeRef"
      class="px-3 py-1 text-sm border rounded hover:bg-gray-50">
      − Hapus Terakhir
    </button>
  </div>
</div>

{{-- Lampiran (opsional) --}}
<div class="bg-white border border-gray-200 rounded-2xl p-4">
  <h2 class="font-semibold text-gray-800 mb-4">Lampiran</h2>
  <div class="grid sm:grid-cols-2 gap-4">
    <div>
      <label class="text-sm font-semibold text-gray-700">Anggaran (PDF)</label>
      <input name="anggaran" type="file" accept=".pdf" class="mt-1.5 block w-full text-sm">
      @if(!empty($inovasi->anggaran_file))
        <p class="text-xs mt-1">
          Saat ini: <a class="text-maroon underline" target="_blank" href="{{ asset('storage/'.$inovasi->anggaran_file) }}">Lihat</a>
        </p>
      @endif
    </div>

    <div>
      <label class="text-sm font-semibold text-gray-700">Profil Bisnis (.ppt/.pdf)</label>
      <input name="profil_bisnis" type="file" accept=".ppt,.pptx,.pdf" class="mt-1.5 block w-full text-sm">
      @if(!empty($inovasi->profil_bisnis_file))
        <p class="text-xs mt-1">
          Saat ini: <a class="text-maroon underline" target="_blank" href="{{ asset('storage/'.$inovasi->profil_bisnis_file) }}">Lihat</a>
        </p>
      @endif
    </div>

    <div>
      <label class="text-sm font-semibold text-gray-700">Dokumen HAKI</label>
      <input name="haki" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 block w-full text-sm">
      @if(!empty($inovasi->haki_file))
        <p class="text-xs mt-1">
          Saat ini: <a class="text-maroon underline" target="_blank" href="{{ asset('storage/'.$inovasi->haki_file) }}">Lihat</a>
        </p>
      @endif
    </div>

    <div>
      <label class="text-sm font-semibold text-gray-700">Penghargaan</label>
      <input name="penghargaan" type="file" accept=".pdf,.jpg,.jpeg,.png" class="mt-1.5 block w-full text-sm">
      @if(!empty($inovasi->penghargaan_file))
        <p class="text-xs mt-1">
          Saat ini: <a class="text-maroon underline" target="_blank" href="{{ asset('storage/'.$inovasi->penghargaan_file) }}">Lihat</a>
        </p>
      @endif
    </div>
  </div>
</div>

    </form>

    {{-- Sidebar ringkas --}}
    <aside class="lg:col-span-1">
      <div class="lg:sticky top-24 space-y-4">
        <div class="bg-white border border-gray-200 rounded-2xl p-4">
          <h3 class="font-semibold text-gray-800">Ringkasan</h3>
          <p class="text-xs text-gray-600 mt-2">Klik “Simpan” untuk memperbarui metadata inovasi.</p>
        </div>
      </div>
    </aside>
  </div>
</section>

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

  // set initial content safely via JSON string
  qR.root.innerHTML = @json(old('rancang_bangun',$inovasi->rancang_bangun));
  qT.root.innerHTML = @json(old('tujuan',$inovasi->tujuan));
  qM.root.innerHTML = @json(old('manfaat',$inovasi->manfaat));
  qH.root.innerHTML = @json(old('hasil_inovasi',$inovasi->hasil_inovasi));

  // on submit: push HTML to hidden inputs
  document.getElementById('formEdit').addEventListener('submit', function(){
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
  if (refIndex >= maxRef) {
    alert('Maksimal 5 referensi.');
    return;
  }

  const div = document.createElement('div');
  div.className = 'ref-item border rounded-lg p-3';
  div.innerHTML = `
    <input type="text"
      name="refs[${refIndex}][judul]"
      required
      placeholder="Judul penelitian / inovasi"
      class="w-full mb-2 rounded-lg border-gray-300">

    <textarea
      name="refs[${refIndex}][deskripsi]"
      rows="2"
      placeholder="Deskripsi singkat"
      class="w-full mb-2 rounded-lg border-gray-300"></textarea>

    <input type="url"
      name="refs[${refIndex}][url]"
      required
      placeholder="Link YouTube / Website"
      class="w-full rounded-lg border-gray-300">
  `;
  refWrapper.appendChild(div);
  refIndex++;
});

document.getElementById('removeRef').addEventListener('click', () => {
  if (refIndex <= minRef) {
    alert('Minimal 3 referensi wajib ada.');
    return;
  }
  refWrapper.lastElementChild.remove();
  refIndex--;
});
</script>

@endsection
