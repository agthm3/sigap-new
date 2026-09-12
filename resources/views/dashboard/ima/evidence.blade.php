@extends('layouts.app')

@section('content')
<section class="max-w-5xl mx-auto px-4 py-6" x-data="imaEvidenceManager()">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold uppercase tracking-wider mb-2">
                {{ $inovasi->kategori_ima }}
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900">Pengisian Evidence IMA</h1>
            <p class="text-gray-500 text-sm mt-1">Inovasi: <strong class="text-gray-800">{{ $inovasi->judul }}</strong></p>
        </div>
        <a href="{{ route('sigap-ima.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-semibold transition text-gray-700">
            &larr; Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-700 border border-emerald-200 p-4 rounded-xl font-medium text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- BANNER TOTAL SKOR REALTIME -->
    <div class="bg-gray-900 rounded-3xl p-6 md:p-8 mb-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6 border border-gray-800">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500 rounded-full blur-3xl opacity-20"></div>
        <div class="relative z-10 text-center md:text-left">
            <h2 class="text-2xl font-black mb-1">Estimasi Skor Inovasi</h2>
            <p class="text-sm text-gray-400">Total poin dari seluruh parameter indikator yang telah dipilih (Otomatis & Real-Time).</p>
        </div>
        <div class="relative z-10 flex flex-col items-center md:items-end">
            <div class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-amber-500 tabular-nums tracking-tighter" x-text="totalSkor">
                0
            </div>
            <span class="text-[10px] uppercase font-bold tracking-widest text-amber-500/80 mt-1">Poin Terkumpul</span>
        </div>
    </div>

    <!-- Daftar 20 Indikator (Accordion) -->
    <div class="space-y-4">
        @foreach($indicators as $ind)
            @php
                $existing = $evidences->get($ind->id);
                $expected = $ind->parameter_options ?? [];
                
                // Ambil Pilihan Parameter (Dropdown) & Bobot Pengali
                $paramsList = is_array($ind->pilihan_parameter) ? $ind->pilihan_parameter : json_decode($ind->pilihan_parameter, true) ?? [];
                $pengali = $ind->pengali ?? 1;
                $savedLabel = $existing->parameter_label ?? '';
                $savedWeight = $existing->parameter_weight ?? 0;

                $acceptTypes = [];
                if(in_array('pdf', $expected)) $acceptTypes[] = '.pdf';
                if(in_array('image', $expected)) array_push($acceptTypes, '.jpg', '.jpeg', '.png');
                $acceptStr = implode(',', $acceptTypes) ?: '.pdf,.jpg,.jpeg,.png';
            @endphp

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden" 
                 x-data="imaEvidenceItem({{ $ind->id }})"
                 x-init="initScore({{ $ind->id }}, {{ $savedWeight }}, {{ $pengali }}); registerItem({{ $ind->id }}, {{ $ind->no_urut }}, $data)">
                 
                <!-- Accordion Header -->
                <button type="button" @click="toggleAccordion({{ $ind->no_urut }})" class="w-full px-5 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition focus:outline-none">
                    <div class="flex items-center gap-4 text-left">
                        <span class="flex-shrink-0 w-10 h-10 rounded-full {{ $existing ? 'bg-emerald-500' : 'bg-gray-800' }} text-white font-black text-sm flex items-center justify-center shadow-md border-2 border-white">
                            {{ $ind->no_urut }}
                        </span>
                        <div>
                            <h3 class="font-bold text-gray-900 text-[15px]">{{ $ind->nama_indikator }}</h3>
                            <div class="flex flex-wrap items-center gap-2 mt-1">
                                @if($existing)
                                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200">✅ {{ $existing->files->count() }} Lampiran</span>
                                @else
                                    <span class="text-[10px] text-gray-500 font-semibold bg-gray-200 px-2 py-0.5 rounded">Belum diisi</span>
                                @endif
                                <!-- Indikator Skor Kecil di Header -->
                                <span class="text-[10px] text-amber-800 font-bold bg-amber-100 px-2 py-0.5 rounded border border-amber-300 shadow-sm">
                                    Skor: <span x-text="(scores[{{ $ind->id }}]?.poin || 0) * {{ $pengali }}"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200" :class="activeAccordion === {{ $ind->no_urut }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <!-- Accordion Body -->
                <div x-show="activeAccordion === {{ $ind->no_urut }}" x-collapse class="px-6 py-5 border-t border-gray-200 bg-white">
                    
                    <!-- Panduan Indikator -->
                    @if($ind->deskripsi_panduan || $ind->file_panduan_path || $ind->video_url)
                        <div class="mb-6 bg-amber-50 p-4 rounded-xl border border-amber-200 text-sm text-amber-900">
                            @if($ind->deskripsi_panduan)
                                <p class="mb-2 text-[13px] leading-relaxed"><strong>Panduan:</strong> {{ $ind->deskripsi_panduan }}</p>
                            @endif
                            <div class="flex flex-wrap gap-3 mt-2">
                                @if($ind->file_panduan_path)
                                    <a href="{{ Storage::url($ind->file_panduan_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] px-3 py-1 bg-amber-600 text-white rounded-md font-bold hover:bg-amber-700 transition">📄 Lihat Pedoman PDF</a>
                                @endif
                                @if($ind->video_url)
                                    <a href="{{ $ind->video_url }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] px-3 py-1 bg-red-600 text-white rounded-md font-bold hover:bg-red-700 transition">📺 Tonton Penjelasan</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Kolom Kiri: Form Metadata & Pilihan Parameter -->
                        <div class="space-y-5">
                            
                            <!-- Kalkulator & Dropdown Parameter -->
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-inner">
                                <label class="block mb-3">
                                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wide block mb-2">Parameter Dokumen Bukti <span class="text-red-500">*</span></span>
                                    
                                    @if(count($paramsList) > 0)
                                        <select id="param_select_{{ $ind->id }}" @change="updateScore({{ $ind->id }}, $event.target.options[$event.target.selectedIndex].dataset.poin)" class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 font-semibold text-gray-700 bg-white">
                                            <option value="" data-poin="0">— Pilih Opsi Dokumen —</option>
                                            @foreach($paramsList as $p)
                                                <option value="{{ $p['label'] }}" data-poin="{{ $p['poin'] }}" @selected($savedLabel == $p['label'])>
                                                    {{ $p['label'] }} (⭐️ {{ $p['poin'] }} Poin)
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <!-- Fallback jika admin belum setting parameter -->
                                        <input type="text" id="param_label_{{ $ind->id }}" value="{{ $savedLabel }}" placeholder="Ketik parameter bukti manual..." class="w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                                    @endif
                                </label>

                                <!-- Kalkulator Mini -->
                                <div class="bg-amber-100 border border-amber-300 p-3 rounded-lg flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-amber-700 font-bold uppercase tracking-wider mb-0.5">Kalkulasi Skor Indikator</p>
                                        <p class="text-xs font-medium text-amber-900">
                                            <span x-text="scores[{{ $ind->id }}]?.poin || 0" class="font-black text-sm"></span> Poin Dasar &times; 
                                            <span class="font-black text-sm">{{ $pengali }}</span> Bobot Pengali
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black text-amber-600" x-text="(scores[{{ $ind->id }}]?.poin || 0) * {{ $pengali }}">0</span>
                                    </div>
                                </div>
                            </div>

                            <label class="block">
                                <span class="text-sm font-semibold text-gray-700">Keterangan / Penjelasan Bukti</span>
                                <textarea id="desc_{{ $ind->id }}" rows="3" class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500" placeholder="Jelaskan bagian spesifik dari dokumen yang menjadi bukti...">{{ $existing->deskripsi ?? '' }}</textarea>
                            </label>

                            @if(in_array('link', $expected))
                                <label class="block">
                                    <span class="text-sm font-semibold text-gray-700">Tautan Berkas (Link Google Drive / Youtube)</span>
                                    <input type="url" id="link_{{ $ind->id }}" value="{{ $existing->link_url ?? '' }}" placeholder="https://..." class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500">
                                </label>
                            @endif
                        </div>

                        <!-- Kolom Kanan: Uploader & File Tersimpan -->
                        <div>
                            @if(in_array('pdf', $expected) || in_array('image', $expected) || empty($expected))
                                <span class="text-sm font-bold text-gray-800 block mb-2">Unggah Berkas Fisik (PDF / Foto)</span>
                                
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-gray-50 hover:bg-amber-50 hover:border-amber-400 transition cursor-pointer" @click="$refs.fileInput.click()">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-gray-200">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-700">Pilih Berkas atau Letakkan di Sini</p>
                                    <p class="text-[10px] text-gray-500 mt-1">Sistem akan otomatis mengecilkan resolusi gambar jika terlalu besar.</p>
                                    
                                    <input type="file" x-ref="fileInput" @change="handleFileInput($event)" multiple accept="{{ $acceptStr }}" class="hidden">
                                    
                                    <!-- Daftar Berkas Baru yang Diunggah -->
                                    <div class="mt-5 space-y-2 text-left" @click.stop>
                                        <template x-for="(f, i) in files" :key="f.id">
                                            <div class="text-xs p-3 border border-gray-200 rounded-lg bg-white flex flex-col gap-1.5 shadow-sm">
                                                <div class="flex justify-between items-center">
                                                    <span class="truncate w-3/4 font-bold text-gray-800" x-text="f.name"></span>
                                                    <button type="button" @click="removeFile(i)" class="text-red-500 hover:text-red-700 font-bold px-2 py-0.5 rounded bg-red-50">&times;</button>
                                                </div>

                                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mt-1">
                                                    <div class="bg-amber-500 h-2 rounded-full transition-all duration-300" :style="`width: ${f.progress}%`"></div>
                                                </div>

                                                <div class="flex justify-between items-center text-[10px] text-gray-500 mt-1">
                                                    <span class="font-semibold" x-text="f.status === 'compressing' ? '🔄 Mengompres...' : (f.status === 'uploading' ? `Mengunggah (${f.progress}%)` : (f.status === 'success' ? '✅ Siap disimpan' : '❌ Gagal'))" :class="f.status === 'success' ? 'text-emerald-600' : ''"></span>
                                                    <span x-text="f.size"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @endif

                            <!-- Lampiran yang Sudah Tersimpan di Database -->
                            @if($existing && $existing->files->count() > 0)
                                <div class="mt-5 pt-4 border-t border-gray-200">
                                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-3">Berkas Telah Tersimpan:</span>
                                    <ul class="space-y-2">
                                        @foreach($existing->files as $file)
                                            <li class="flex items-center justify-between p-3 rounded-lg bg-white border border-gray-200 text-xs shadow-sm hover:border-emerald-300 transition">
                                                <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="flex items-center gap-2 truncate w-3/4 text-gray-700 hover:text-emerald-600 font-semibold">
                                                    <span class="text-base">📄</span> <span class="truncate">{{ $file->file_name }}</span>
                                                </a>
                                                <button type="button" onclick="if(confirm('Yakin ingin menghapus dokumen bukti ini?')) document.getElementById('delete-file-{{ $file->id }}').submit();" class="text-red-500 font-bold hover:bg-red-50 px-2 py-1 rounded">Hapus</button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Hidden Form Hapus Lampiran -->
            @if($existing)
                @foreach($existing->files as $file)
                    <form id="delete-file-{{ $file->id }}" action="{{ route('sigap-ima.evidence.file.destroy', $file->id) }}" method="POST" class="hidden">
                        @csrf @method('DELETE')
                    </form>
                @endforeach
            @endif
        @endforeach
    </div>

    <!-- Tombol Simpan Global -->
    <div class="sticky bottom-6 flex justify-end mt-10 z-10">
        <button type="button" @click="submitAllData()" class="px-8 py-4 bg-gradient-to-r from-gray-900 to-black text-white rounded-2xl font-bold shadow-[0_10px_25px_-5px_rgba(0,0,0,0.5)] hover:shadow-[0_15px_35px_-5px_rgba(0,0,0,0.6)] hover:-translate-y-1 transition transform flex items-center gap-3 border border-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Simpan Seluruh Evidence
        </button>
    </div>
</section>

<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ==========================================
// 1. ENGINE KOMPRESI & CHUNK UPLOADER MODERN
// ==========================================
window.SigapImaUploader = {
    CHUNK_SIZE: 512 * 1024,

    async compressImage(file) {
        if (!file.type.startsWith('image/')) return file;
        try {
            const bitmap = await createImageBitmap(file);
            const MAX = 1600;
            let w = bitmap.width, h = bitmap.height;
            if (w > h && w > MAX) { h = Math.round((h * MAX) / w); w = MAX; }
            else if (h > MAX) { w = Math.round((w * MAX) / h); h = MAX; }

            const canvas = document.createElement('canvas');
            canvas.width = w; canvas.height = h;
            canvas.getContext('2d').drawImage(bitmap, 0, 0, w, h);

            return await new Promise((resolve) => {
                canvas.toBlob((blob) => {
                    if (!blob) return resolve(file);
                    const cleanName = file.name.replace(/\.[^/.]+$/, "") + ".jpg";
                    resolve(new File([blob], cleanName, { type: 'image/jpeg', lastModified: Date.now() }));
                }, 'image/jpeg', 0.75);
            });
        } catch (e) { return file; }
    },

    async uploadFileInChunks(file, onProgress, uploadUrl, csrfToken) {
        const fileId = 'file_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
        const totalChunks = Math.ceil(file.size / this.CHUNK_SIZE);

        for (let idx = 0; idx < totalChunks; idx++) {
            const start = idx * this.CHUNK_SIZE;
            const chunk = file.slice(start, Math.min(start + this.CHUNK_SIZE, file.size));

            const fd = new FormData();
            fd.append('file', chunk);
            fd.append('file_id', fileId);
            fd.append('chunk_index', idx);
            fd.append('total_chunks', totalChunks);
            fd.append('original_name', file.name);

            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: fd
            });

            if (!res.ok) throw new Error(`Unggahan chunk ${idx + 1} gagal.`);
            const data = await res.json();
            if (typeof onProgress === 'function') onProgress(Math.round(((idx + 1) / totalChunks) * 100), data);
            if (data.completed) return data;
        }
    }
};

// ==========================================
// 2. KOMPONEN REAKTIF ALPINE.JS
// ==========================================
document.addEventListener('alpine:init', () => {

    Alpine.data('imaEvidenceItem', (indicatorId) => ({
        indicatorId: indicatorId,
        files: [],
        uploadUrl: "{{ route('sigap-ima.upload-chunk') }}",
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',

        async handleFileInput(e) {
            const rawFiles = Array.from(e.target.files);
            if (!rawFiles.length) return;

            for (const file of rawFiles) {
                const fileId = 'doc_' + Date.now();
                this.files.push({ id: fileId, name: file.name, size: (file.size / 1024).toFixed(1) + ' KB', progress: 0, status: 'compressing', temp_path: '', original_name: file.name });
                this.processSingleFile(fileId, file);
            }
            e.target.value = '';
        },

        async processSingleFile(fileId, rawFile) {
            try {
                const readyFile = await window.SigapImaUploader.compressImage(rawFile);
                this.updateFile(fileId, { status: 'uploading', size: (readyFile.size / 1024).toFixed(1) + ' KB' });

                const result = await window.SigapImaUploader.uploadFileInChunks(
                    readyFile,
                    (pct) => this.updateFile(fileId, { progress: pct }),
                    this.uploadUrl,
                    this.csrfToken
                );

                if (result && result.completed) {
                    this.updateFile(fileId, { status: 'success', progress: 100, temp_path: result.temp_path, original_name: result.original_name });
                } else {
                    this.updateFile(fileId, { status: 'error' });
                }
            } catch (err) {
                this.updateFile(fileId, { status: 'error' });
            }
        },

        updateFile(id, props) {
            const i = this.files.findIndex(f => f.id === id);
            if (i !== -1) { this.files[i] = { ...this.files[i], ...props }; this.files = [...this.files]; }
        },

        removeFile(i) {
            this.files.splice(i, 1);
            this.files = [...this.files];
        },

        getUploadedPaths() {
            return this.files.filter(f => f.status === 'success' && f.temp_path).map(f => ({ temp_path: f.temp_path, original_name: f.original_name }));
        }
    }));

    Alpine.data('imaEvidenceManager', () => ({
        activeAccordion: 1,
        registeredItems: {},
        scores: {}, // State global untuk kalkulator: { id: { poin: X, pengali: Y } }

        // Kalkulasi Skor Total Keseluruhan
        get totalSkor() {
            return Object.values(this.scores).reduce((sum, item) => sum + ((item.poin || 0) * (item.pengali || 1)), 0);
        },

        // Inisialisasi poin saat halaman dimuat (dari database)
        initScore(id, poin, pengali) {
            this.scores[id] = { poin: parseInt(poin) || 0, pengali: parseInt(pengali) || 1 };
        },

        // Update poin saat dropdown parameter dipilih
        updateScore(id, poin) {
            if (this.scores[id]) {
                this.scores[id].poin = parseInt(poin) || 0;
            } else {
                this.scores[id] = { poin: parseInt(poin) || 0, pengali: 1 };
            }
        },

        toggleAccordion(no) {
            this.activeAccordion = this.activeAccordion === no ? null : no;
        },

        registerItem(id, noUrut, alpineInstance) {
            this.registeredItems[id] = { id: id, noUrut: noUrut, instance: alpineInstance };
        },

        async submitAllData() {
            let isStillUploading = false;
            Object.values(this.registeredItems).forEach(item => {
                if (item.instance.files.some(f => f.status === 'uploading' || f.status === 'compressing')) isStillUploading = true;
            });

            if (isStillUploading) {
                Swal.fire({ icon: 'warning', title: 'Tunggu Sebentar', text: 'Masih ada berkas yang sedang diunggah.' });
                return;
            }

            const payloadItems = [];
            Object.values(this.registeredItems).forEach(item => {
                const id = item.id;
                
                // Cek dari Dropdown ATAU Input Teks Biasa
                const paramSelect = document.getElementById(`param_select_${id}`);
                const paramText   = document.getElementById(`param_label_${id}`);
                
                let paramLabel = '';
                let paramWeight = 0;

                if (paramSelect) {
                    paramLabel = paramSelect.value;
                    paramWeight = parseInt(paramSelect.options[paramSelect.selectedIndex]?.dataset?.poin || 0);
                } else if (paramText) {
                    paramLabel = paramText.value.trim();
                }

                const descVal  = document.getElementById(`desc_${id}`)?.value.trim() || '';
                const linkVal  = document.getElementById(`link_${id}`)?.value.trim() || '';
                const uploaded = item.instance.getUploadedPaths();

                if (paramLabel || descVal || linkVal || uploaded.length > 0) {
                    payloadItems.push({
                        indicator_id: id,
                        no_urut: item.noUrut,
                        parameter_label: paramLabel,
                        parameter_weight: paramWeight, // Kirim bobot Poin untuk disimpan ke DB
                        deskripsi: descVal,
                        link_url: linkVal,
                        temp_files: uploaded
                    });
                }
            });

            if (payloadItems.length === 0) {
                Swal.fire({ icon: 'info', title: 'Tidak Ada Perubahan', text: 'Silakan isi parameter atau unggah berkas terlebih dahulu.' });
                return;
            }

            Swal.fire({ title: 'Menyimpan Evidence...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch("{{ route('sigap-ima.evidence.store.json', $inovasi->id) }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ items: payloadItems })
                });

                const res = await response.json();
                if (response.ok && res.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: res.message }).then(() => window.location.reload());
                } else {
                    throw new Error(res.message);
                }
            } catch (err) {
                Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: err.message });
            }
        }
    }));
});
</script>
@endpush