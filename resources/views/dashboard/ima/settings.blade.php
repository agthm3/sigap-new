@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-4 py-6" x-data="{ activeTab: 'jadwal' }">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-gray-900">Pengaturan SIGAP IMA</h1>
        <p class="text-gray-500 text-sm mt-1">Kelola jadwal lomba, status kunci pengisian inovator, skema poin 20 indikator, dan opsi dropdown.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200 text-sm font-medium">
            ✅ {{ session('success') }}
        </div>
    @endif

    <!-- Tabs Navigasi -->
    <div class="flex flex-wrap gap-2 border-b border-gray-200 mb-6">
        <button @click="activeTab = 'jadwal'" :class="activeTab === 'jadwal' ? 'border-amber-500 text-amber-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 transition outline-none">
            🗓️ Jadwal Lomba & Kunci Pengisian
        </button>
        <button @click="activeTab = 'indikator'" :class="activeTab === 'indikator' ? 'border-amber-500 text-amber-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 transition outline-none">
            ⭐ Pengaturan 20 Indikator & Poin
        </button>
        <button @click="activeTab = 'dropdown'" :class="activeTab === 'dropdown' ? 'border-amber-500 text-amber-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-2 transition outline-none">
            📋 Opsi Dropdown Profil
        </button>
    </div>

    <!-- ============================================== -->
    <!-- TAB 1: JADWAL LOMBA & KUNCI PENGISIAN -->
    <!-- ============================================== -->
    <div x-show="activeTab === 'jadwal'" x-transition.opacity>
        <div class="grid lg:grid-cols-3 gap-6">
            
            <!-- PANEL KIRI: SAKELAR KUNCI & FORM TAMBAH JADWAL -->
            <div class="lg:col-span-1 space-y-6">
                
                <!-- CARD TOGGLE KUNCI PENGISIAN -->
                <div class="bg-white p-6 rounded-2xl border {{ $isLocked ? 'border-rose-300 bg-rose-50/30' : 'border-emerald-300 bg-emerald-50/30' }} shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-extrabold text-gray-900 text-base">Status Pengisian</h3>
                        <span class="px-2.5 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $isLocked ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                            {{ $isLocked ? '🔒 DIKUNCI' : '🔓 DIBUKA' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                        Jika dikunci, inovator (OPD) tidak dapat menambah inovasi baru, mengubah profil, atau mengunggah berkas evidence.
                    </p>

                    <form action="{{ route('sigap-ima.settings.toggle-lock') }}" method="POST">
                        @csrf
                        <input type="hidden" name="is_submission_locked" value="{{ $isLocked ? '0' : '1' }}">

                        <label class="block mb-3">
                            <span class="text-xs font-semibold text-gray-700">Pesan Pengumuman untuk Inovator</span>
                            <textarea name="lock_notice_message" rows="3" class="mt-1 w-full rounded-xl border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500" placeholder="Pesan yang tampil saat inovator membuka halaman pendaftaran...">{{ $lockNotice }}</textarea>
                        </label>

                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengubah status kunci pengisian?')" class="w-full py-2.5 rounded-xl font-bold text-xs transition shadow-sm {{ $isLocked ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'bg-rose-600 text-white hover:bg-rose-700' }}">
                            {{ $isLocked ? '🔓 Buka Kembali Pengisian' : '🔒 Kunci Seluruh Pengisian Sekarang' }}
                        </button>
                    </form>
                </div>

                <!-- CARD TAMBAH FASE / AGENDA -->
                <form action="{{ route('sigap-ima.settings.schedule.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    @csrf
                    <h3 class="font-bold text-gray-900 mb-4 border-b pb-2 text-sm">Tambah Fase / Jadwal Lomba</h3>

                    <label class="block mb-3">
                        <span class="text-xs font-semibold text-gray-700">Nama Fase / Tahapan *</span>
                        <input type="text" name="fase_nama" required placeholder="Contoh: Pendaftaran & Unggah Evidence" class="mt-1 w-full rounded-lg border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                    </label>

                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Tanggal Mulai *</span>
                            <input type="date" name="tanggal_mulai" required class="mt-1 w-full rounded-lg border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-gray-700">Tanggal Selesai *</span>
                            <input type="date" name="tanggal_selesai" required class="mt-1 w-full rounded-lg border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                        </label>
                    </div>

                    <label class="block mb-3">
                        <span class="text-xs font-semibold text-gray-700">Urutan Fase *</span>
                        <input type="number" name="urutan" value="{{ $schedules->count() + 1 }}" min="1" required class="mt-1 w-full rounded-lg border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500">
                    </label>

                    <label class="block mb-4">
                        <span class="text-xs font-semibold text-gray-700">Deskripsi Ringkas</span>
                        <textarea name="deskripsi" rows="2" placeholder="Catatan instruksi untuk inovator..." class="mt-1 w-full rounded-lg border-gray-300 text-xs focus:ring-amber-500 focus:border-amber-500"></textarea>
                    </label>

                    <button type="submit" class="w-full py-2 bg-gray-900 text-white font-bold rounded-lg hover:bg-black transition text-xs">
                        + Tambah ke Jadwal Lomba
                    </button>
                </form>
            </div>

            <!-- PANEL KANAN: LIST AGENDA TAHAPAN / JADWAL -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-50/70 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-900 text-sm">Linimasa & Tahapan Pelaksanaan IMA</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Jadwal ini akan muncul di dashboard dan formulir pengisian inovator.</p>
                        </div>
                        <span class="text-xs font-bold text-amber-700 bg-amber-100 px-3 py-1 rounded-full">{{ $schedules->count() }} Fase</span>
                    </div>

                    <div class="p-6">
                        @if($schedules->isEmpty())
                            <div class="text-center py-12 text-gray-400">
                                <span class="text-3xl block mb-2">🗓️</span>
                                <p class="text-sm font-medium">Belum ada agenda jadwal lomba yang ditambahkan.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($schedules as $sch)
                                    @php
                                        $now = now();
                                        $isOngoing = $now->between($sch->tanggal_mulai->startOfDay(), $sch->tanggal_selesai->endOfDay());
                                        $isPassed  = $now->isAfter($sch->tanggal_selesai->endOfDay());
                                    @endphp
                                    <div class="p-4 rounded-xl border {{ $isOngoing ? 'border-amber-400 bg-amber-50/40 shadow-sm' : ($isPassed ? 'border-gray-200 bg-gray-50/60 opacity-80' : 'border-gray-200 bg-white') }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="flex items-start gap-3.5">
                                            <span class="w-7 h-7 rounded-lg {{ $isOngoing ? 'bg-amber-500 text-white' : 'bg-gray-800 text-white' }} flex items-center justify-center font-bold text-xs flex-shrink-0">
                                                {{ $sch->urutan }}
                                            </span>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-gray-900 text-sm">{{ $sch->fase_nama }}</h4>
                                                    @if($isOngoing)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-200 text-amber-900 animate-pulse">BERLANGSUNG</span>
                                                    @elseif($isPassed)
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-200 text-gray-600">SELESAI</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-100 text-blue-700">MENDATANG</span>
                                                    @endif
                                                </div>
                                                <p class="text-xs text-amber-700 font-semibold mt-0.5">
                                                    📅 {{ $sch->tanggal_mulai->format('d M Y') }} s.d. {{ $sch->tanggal_selesai->format('d M Y') }}
                                                </p>
                                                @if($sch->deskripsi)
                                                    <p class="text-xs text-gray-600 mt-1.5">{{ $sch->deskripsi }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <form action="{{ route('sigap-ima.settings.schedule.destroy', $sch->id) }}" method="POST" onsubmit="return confirm('Hapus tahapan jadwal ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 text-xs font-bold transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 2: INDIKATOR SETTINGS (DENGAN SKEMA POIN) -->
    <!-- ============================================== -->
    <div x-show="activeTab === 'indikator'" x-transition.opacity style="display: none;">
        <form action="{{ route('sigap-ima.settings.indicator') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="flex justify-between items-center bg-amber-50 border border-amber-200 p-4 rounded-xl mb-4 sticky top-4 z-10 shadow-sm">
                <p class="text-sm text-amber-800">Tentukan nama indikator, pengali nilai, dan opsi parameternya (beserta bintang/poin).</p>
                <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-md whitespace-nowrap">
                    Simpan 20 Indikator
                </button>
            </div>

            <div class="grid xl:grid-cols-2 gap-6">
                @foreach($indicators as $ind)
                    @php
                        $expected = $ind->parameter_options ?? [];
                        $pilihanParams = json_encode($ind->pilihan_parameter ?? []);
                    @endphp
                    
                    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm relative" x-data="indicatorItem({{ $ind->id }}, {{ $pilihanParams }})">
                        <div class="absolute -top-3 -left-3 w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center font-bold text-sm shadow">
                            {{ $ind->no_urut }}
                        </div>
                        
                        <div class="grid md:grid-cols-4 gap-3 mb-3">
                            <label class="block md:col-span-3">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Nama Indikator</span>
                                <input type="text" name="indikator[{{ $ind->id }}][nama]" value="{{ $ind->nama_indikator }}" required class="mt-1 w-full rounded-lg border-gray-300 text-sm font-semibold focus:border-amber-500">
                            </label>
                            
                            <label class="block md:col-span-1">
                                <span class="text-xs font-semibold text-amber-600 uppercase">Bobot Pengali</span>
                                <input type="number" name="indikator[{{ $ind->id }}][pengali]" value="{{ $ind->pengali ?? 1 }}" min="1" required class="mt-1 w-full rounded-lg border-amber-300 text-sm focus:ring-amber-500 focus:border-amber-500 bg-amber-50 font-bold text-center">
                            </label>
                        </div>
                        
                        <!-- MANAJEMEN PARAMETER & POIN DINAMIS -->
                        <div class="mb-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                            <span class="text-sm font-bold text-gray-800 block mb-2">Opsi Parameter & Poin Bintang</span>
                            <div class="space-y-2">
                                <template x-for="(param, index) in params" :key="index">
                                    <div class="flex items-start gap-2">
                                        <div class="flex-1">
                                            <input type="text" :name="`indikator[${id}][params][${index}][label]`" x-model="param.label" placeholder="Contoh: SK WALI KOTA" required class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500">
                                        </div>
                                        <div class="w-24">
                                            <input type="number" :name="`indikator[${id}][params][${index}][poin]`" x-model="param.poin" placeholder="Poin" required min="0" class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500">
                                        </div>
                                        <button type="button" @click="removeParam(index)" class="px-2.5 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 font-bold">
                                            &times;
                                        </button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addParam()" class="mt-3 text-xs text-amber-600 font-bold hover:text-amber-800 inline-flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Parameter Penilaian
                            </button>
                        </div>

                        <label class="block mb-3">
                            <span class="text-xs font-semibold text-gray-500 uppercase">Deskripsi / Panduan Singkat</span>
                            <textarea name="indikator[{{ $ind->id }}][deskripsi]" rows="2" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-amber-500">{{ $ind->deskripsi_panduan }}</textarea>
                        </label>
                        
                        <div class="mb-4 bg-white p-3 rounded-lg border border-gray-200 shadow-sm">
                            <span class="text-xs font-semibold text-gray-700 block mb-2">Jenis Bukti Upload yang Diizinkan</span>
                            <div class="flex flex-wrap gap-4 text-sm">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="indikator[{{ $ind->id }}][expected_files][]" value="pdf" class="rounded text-amber-500 focus:ring-amber-500" {{ in_array('pdf', $expected) ? 'checked' : '' }}>
                                    <span>📄 PDF</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="indikator[{{ $ind->id }}][expected_files][]" value="image" class="rounded text-amber-500 focus:ring-amber-500" {{ in_array('image', $expected) ? 'checked' : '' }}>
                                    <span>🖼️ Gambar (JPG/PNG)</span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="indikator[{{ $ind->id }}][expected_files][]" value="link" class="rounded text-amber-500 focus:ring-amber-500" {{ in_array('link', $expected) ? 'checked' : '' }}>
                                    <span>🔗 Tautan/Link</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <label class="block">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Link Video Youtube</span>
                                <input type="url" name="indikator[{{ $ind->id }}][video_url]" value="{{ $ind->video_url }}" placeholder="https://youtube.com/..." class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-amber-500">
                            </label>
                            <label class="block">
                                <span class="text-xs font-semibold text-gray-500 uppercase">File Pedoman (PDF)</span>
                                <input type="file" name="indikator[{{ $ind->id }}][file]" accept=".pdf" class="mt-1 w-full text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-gray-100 hover:file:bg-gray-200 border border-gray-200 rounded-lg">
                                @if($ind->file_panduan_path)
                                    <a href="{{ Storage::url($ind->file_panduan_path) }}" target="_blank" class="text-[10px] text-amber-600 hover:underline mt-1 block font-semibold">📄 Lihat Pedoman Aktif</a>
                                @endif
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold hover:bg-black transition shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Simpan Pengaturan Indikator
                </button>
            </div>
        </form>
    </div>

    <!-- ============================================== -->
    <!-- TAB 3: DROPDOWN SETTINGS -->
    <!-- ============================================== -->
    <div x-show="activeTab === 'dropdown'" x-transition.opacity style="display: none;">
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <form action="{{ route('sigap-ima.settings.dropdown') }}" method="POST" class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm sticky top-4">
                    @csrf
                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2">Tambah Opsi Dropdown</h3>
                    <label class="block mb-3">
                        <span class="text-sm font-semibold text-gray-700">Pilih Kategori</span>
                        <select name="kategori" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500">
                            <option value="">-- Pilih --</option>
                            @foreach($kategoriList as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block mb-4">
                        <span class="text-sm font-semibold text-gray-700">Opsi (Bisa isi banyak sekaligus)</span>
                        <textarea name="label" rows="6" required class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:ring-amber-500 focus:border-amber-500" placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3&#10;&#10;(Pisahkan dengan Enter)"></textarea>
                    </label>
                    <button type="submit" class="w-full py-2 bg-amber-500 text-gray-900 font-bold rounded-lg hover:bg-amber-400 transition">Tambah Opsi</button>
                </form>
            </div>
            <div class="lg:col-span-2 space-y-6">
                @foreach($kategoriList as $key => $title)
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b font-bold text-gray-800">{{ $title }}</div>
                        <ul class="divide-y divide-gray-100">
                            @forelse($dropdowns[$key] ?? [] as $opt)
                                <li class="p-4 flex items-start justify-between gap-4 hover:bg-gray-50">
                                    <span class="text-sm text-gray-700">{{ $opt->label }}</span>
                                    <form action="{{ route('sigap-ima.settings.dropdown.destroy', $opt->id) }}" method="POST" onsubmit="return confirm('Hapus opsi ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 p-1 bg-red-50 rounded">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li class="p-4 text-sm text-gray-500 text-center">Belum ada data.</li>
                            @endforelse
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('indicatorItem', (id, existingParams) => ({
        id: id,
        params: [],

        init() {
            if (existingParams && existingParams.length > 0) {
                this.params = existingParams;
            } else {
                this.addParam();
            }
        },

        addParam() {
            this.params.push({ label: '', poin: '' });
        },

        removeParam(index) {
            this.params.splice(index, 1);
        }
    }));
});
</script>
@endpush