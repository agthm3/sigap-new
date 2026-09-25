@extends('layouts.app')

@push('head')
<style>
    .bg-paper {
        background-color: #f7f6f2;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.08'/%3E%3C/svg%3E");
    }
    .thumb-scroll::-webkit-scrollbar { height: 6px; }
    .thumb-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@section('content')
@php
    $repo = app(\App\Repositories\KinerjaRepository::class);
    $feedData = collect($kinerjaItems)->map(function($m) use ($repo) {
        $images = [];
        if (method_exists($m, 'media') && $m->media()->count() > 0) {
            foreach ($m->media()->where('is_image', true)->orderByDesc('is_primary')->oldest()->get() as $mm) {
                $images[] = $repo->fileUrl($mm->path);
            }
        } 
        if (empty($images)) {
            if (!empty($m->thumb_path)) $images[] = $repo->fileUrl($m->thumb_path);
            elseif (!empty($m->file_path)) $images[] = $repo->fileUrl($m->file_path);
        }
        return [
            'id' => $m->id,
            'title' => $m->title,
            'description' => $m->description,
            'date' => \Carbon\Carbon::parse($m->activity_date ?? now())->locale('id')->translatedFormat('d F Y'),
            'images' => array_values(array_filter($images))
        ];
    })->values()->toJson();
@endphp

<div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 items-start" x-data="feedGenerator()" x-init="initCanvas()">
    
    <!-- Bagian Form Control (Kiri) -->
    <div class="w-full lg:w-1/3 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm sticky top-24 max-h-[88vh] overflow-y-auto thumb-scroll">
        <div class="flex items-center justify-between border-b pb-2 mb-4">
            <h2 class="text-xl font-bold text-gray-900">SIGAP Feed</h2>
            <span class="text-xs font-bold px-2 py-1 bg-maroon/10 text-maroon rounded-md">Rasio 4:5 Portrait</span>
        </div>
        
        <label class="block mb-4">
            <span class="text-sm font-semibold text-gray-700">1. Pilih Kegiatan Kinerja</span>
            <select x-model="selectedId" @change="onSelectKinerja" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 shadow-sm focus:ring-maroon focus:border-maroon">
                <option value="">-- Silakan Pilih Kegiatan --</option>
                @foreach($kinerjaItems as $item)
                    <option value="{{ $item->id }}">{{ $item->title }}</option>
                @endforeach
            </select>
        </label>

        <div x-show="selectedId" x-cloak class="space-y-4">
            <label class="block">
                <span class="text-sm font-semibold text-gray-700">Tanggal Tampil</span>
                <input type="text" x-model="dateText" @input="debounceRender" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 uppercase font-bold shadow-sm">
            </label>

            <!-- Judul (Max 140 karakter untuk 3 baris fleksibel) -->
            <label class="block">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Judul Feed (Slide 1 - Max 3 Baris)</span>
                    <span class="text-xs font-semibold text-gray-400" x-text="(selectedTitle || '').length + '/140'"></span>
                </div>
                <textarea x-model="selectedTitle" @input="debounceRender" rows="3" maxlength="140" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 font-semibold shadow-sm focus:ring-maroon focus:border-maroon" placeholder="Judul kegiatan bisa sampai 3 baris..."></textarea>
            </label>

            <label class="block">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Deskripsi Cover</span>
                    <span class="text-xs font-semibold text-gray-400" x-text="(description || '').length + '/250'"></span>
                </div>
                <textarea x-model="description" @input="debounceRender" rows="3" maxlength="250" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 text-sm shadow-sm focus:ring-maroon focus:border-maroon"></textarea>
            </label>

            <!-- Box Preview & Copy Caption Instagram -->
            <div class="border-t pt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-semibold text-gray-700">Caption Instagram</span>
                    <button type="button" @click="copyCaption()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-900 text-white hover:bg-black rounded-lg text-xs font-semibold transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Salin Caption
                    </button>
                </div>
                <div class="p-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-600 space-y-2 select-all font-mono leading-relaxed max-h-36 overflow-y-auto">
                    <p class="font-bold text-gray-900" x-text="selectedTitle || 'Judul Kegiatan'"></p>
                    <p x-text="description || 'Deskripsi kegiatan...'"></p>
                    <p class="text-blue-600 break-words font-semibold">#BRIDAMakassar #MakassarMULIA #MunafriArifuddin #Riset #KelompokRiset #EvaluasiRiset #InovasiDaerah #PembangunanBerbasisRiset</p>
                </div>
            </div>

            <!-- Pilih Cover -->
            <div class="border-t pt-4">
                <span class="text-sm font-semibold text-gray-700 block mb-2">2. Pilih Foto Utama (Cover - Slide 1)</span>
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="(img, idx) in availableImages" :key="idx">
                        <div @click="setCover(img)" class="relative h-16 bg-gray-200 rounded-lg cursor-pointer overflow-hidden border-2 transition-all" :class="coverImage === img ? 'border-maroon shadow-md' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                            <template x-if="coverImage === img">
                                <div class="absolute inset-0 bg-maroon/50 flex items-center justify-center backdrop-blur-[1px]">
                                    <span class="text-white text-[10px] font-bold">COVER</span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Pilih Foto Extra Unlimited -->
            <div class="border-t pt-4">
                <span class="text-sm font-semibold text-gray-700 block mb-1">3. Tambah Slide Dokumentasi (<span x-text="extraImages.length"></span> Foto)</span>
                <p class="text-[10px] text-gray-500 mb-2 leading-tight">Setiap 2 foto akan otomatis membuat 1 slide tambahan 4:5.</p>
                <div class="grid grid-cols-4 gap-2">
                    <template x-for="(img, idx) in availableImages" :key="'ex'+idx">
                        <div x-show="img !== coverImage" @click="toggleExtra(img)" class="relative h-16 bg-gray-200 rounded-lg cursor-pointer overflow-hidden border-2 transition-all" :class="extraImages.includes(img) ? 'border-[#002B4C] shadow-md' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                            <template x-if="extraImages.includes(img)">
                                <div class="absolute inset-0 bg-[#002B4C]/60 flex flex-col items-center justify-center backdrop-blur-[1px]">
                                    <span class="text-white text-[10px] font-bold" x-text="'SLIDE ' + (Math.floor(extraImages.indexOf(img) / 2) + 2)"></span>
                                    <span class="text-white/80 text-[8px] mt-0.5" x-text="'Foto ' + ((extraImages.indexOf(img) % 2) + 1)"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- 4. ATUR POSISI FOTO (GESER KIRI, KANAN, ATAS, BAWAH) -->
            <div x-show="coverImage" class="border-t pt-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-bold text-gray-800">4. Sesuaikan Posisi Foto Frame</span>
                    <button type="button" @click="resetAllOffsets()" class="text-xs text-maroon hover:underline font-semibold">Reset Semua</button>
                </div>
                <p class="text-[11px] text-gray-500 mb-3">Gunakan tombol panah untuk memposisikan subjek foto agar tidak terpotong.</p>

                <div class="space-y-3">
                    <!-- Frame 1: Cover Photo -->
                    <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="relative w-10 h-10 rounded-lg overflow-hidden bg-gray-200 shrink-0 border border-gray-300">
                                <img :src="coverImage" class="w-full h-full object-cover">
                                <span class="absolute bottom-0 right-0 bg-maroon text-white text-[8px] px-1 font-bold rounded-tl">S1</span>
                            </div>
                            <div class="truncate">
                                <span class="text-xs font-bold text-gray-800 block">Cover (Slide 1)</span>
                                <span class="text-[10px] text-gray-500" x-text="'X: ' + (coverOffset.x > 0 ? '+' : '') + coverOffset.x + '% | Y: ' + (coverOffset.y > 0 ? '+' : '') + coverOffset.y + '%'"></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button" @click="shiftCover(-5, 0)" title="Geser Kiri" class="w-7 h-7 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg flex items-center justify-center text-gray-700 text-xs font-bold active:scale-95 transition">◀</button>
                            <div class="flex flex-col gap-1">
                                <button type="button" @click="shiftCover(0, -5)" title="Geser Atas" class="w-7 h-3.5 bg-white hover:bg-gray-100 border border-gray-300 rounded flex items-center justify-center text-gray-700 text-[8px] active:scale-95 transition">▲</button>
                                <button type="button" @click="shiftCover(0, 5)" title="Geser Bawah" class="w-7 h-3.5 bg-white hover:bg-gray-100 border border-gray-300 rounded flex items-center justify-center text-gray-700 text-[8px] active:scale-95 transition">▼</button>
                            </div>
                            <button type="button" @click="shiftCover(5, 0)" title="Geser Kanan" class="w-7 h-7 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg flex items-center justify-center text-gray-700 text-xs font-bold active:scale-95 transition">▶</button>
                            <button type="button" @click="resetCoverOffset()" title="Reset Cover" class="w-7 h-7 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg flex items-center justify-center text-xs ml-1 active:scale-95 transition">↺</button>
                        </div>
                    </div>

                    <!-- Frames: Extra Slides Documentation -->
                    <template x-for="(img, idx) in extraImages" :key="'ctrl'+idx">
                        <div class="p-2.5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="relative w-10 h-10 rounded-lg overflow-hidden bg-gray-200 shrink-0 border border-gray-300">
                                    <img :src="img" class="w-full h-full object-cover">
                                    <span class="absolute bottom-0 right-0 bg-[#002B4C] text-white text-[8px] px-1 font-bold rounded-tl" x-text="'S' + (Math.floor(idx / 2) + 2)"></span>
                                </div>
                                <div class="truncate">
                                    <span class="text-xs font-bold text-gray-800 block" x-text="'Slide ' + (Math.floor(idx / 2) + 2) + ' (Foto ' + ((idx % 2) + 1) + ')'"></span>
                                    <span class="text-[10px] text-gray-500" x-text="'X: ' + ((extraOffsets[idx]?.x || 0) > 0 ? '+' : '') + (extraOffsets[idx]?.x || 0) + '% | Y: ' + ((extraOffsets[idx]?.y || 0) > 0 ? '+' : '') + (extraOffsets[idx]?.y || 0) + '%'"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 shrink-0">
                                <button type="button" @click="shiftExtra(idx, -5, 0)" title="Geser Kiri" class="w-7 h-7 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg flex items-center justify-center text-gray-700 text-xs font-bold active:scale-95 transition">◀</button>
                                <div class="flex flex-col gap-1">
                                    <button type="button" @click="shiftExtra(idx, 0, -5)" title="Geser Atas" class="w-7 h-3.5 bg-white hover:bg-gray-100 border border-gray-300 rounded flex items-center justify-center text-gray-700 text-[8px] active:scale-95 transition">▲</button>
                                    <button type="button" @click="shiftExtra(idx, 0, 5)" title="Geser Bawah" class="w-7 h-3.5 bg-white hover:bg-gray-100 border border-gray-300 rounded flex items-center justify-center text-gray-700 text-[8px] active:scale-95 transition">▼</button>
                                </div>
                                <button type="button" @click="shiftExtra(idx, 5, 0)" title="Geser Kanan" class="w-7 h-7 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg flex items-center justify-center text-gray-700 text-xs font-bold active:scale-95 transition">▶</button>
                                <button type="button" @click="resetExtraOffset(idx)" title="Reset Foto Ini" class="w-7 h-7 bg-gray-200 hover:bg-gray-300 text-gray-600 rounded-lg flex items-center justify-center text-xs ml-1 active:scale-95 transition">↺</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <div class="mt-6 flex flex-col gap-2">
            <button @click="downloadFeed()" :disabled="!selectedId || !coverImage" 
                :class="(!selectedId || !coverImage) ? 'bg-gray-300 cursor-not-allowed' : 'bg-maroon hover:bg-maroon-800'"
                class="w-full text-white font-bold py-3 rounded-xl transition-colors shadow-lg">
                <span x-text="coverImage ? '⬇️ Export ' + getTotalSlides() + ' Slide (.JPG)' : 'Pilih Cover Terlebih Dahulu'"></span>
            </button>
            
            <button type="button" x-show="selectedId" @click="copyCaption()" class="w-full py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-800 text-xs font-bold transition flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-maroon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                Salin Caption Instagram
            </button>
        </div>
    </div>

    <!-- Bagian Preview Canvas (Kanan) -->
    <div class="w-full lg:w-2/3 flex flex-col items-center gap-8 bg-gray-200 p-6 rounded-2xl overflow-hidden border border-gray-300 shadow-inner">
        
        <!-- Preview Slide 1 (Cover) -->
        <div class="w-full flex flex-col items-center">
            <div class="flex items-center justify-between w-full max-w-[420px] mb-2 px-1">
                <span class="font-bold text-gray-700 text-sm">SLIDE 1 (COVER)</span>
                <span class="text-xs bg-maroon text-white font-semibold px-2 py-0.5 rounded">Desain Pola A</span>
            </div>
            <canvas id="feedSlide1" width="1080" height="1350" class="w-full max-w-[420px] h-auto rounded-xl shadow-2xl bg-white"></canvas>
        </div>

        <!-- Preview Unlimited Extra Slides Dinamis -->
        <template x-for="i in Math.ceil(extraImages.length / 2)" :key="i">
            <div class="w-full flex flex-col items-center border-t border-gray-300 pt-6">
                <div class="flex items-center justify-between w-full max-w-[420px] mb-2 px-1">
                    <span class="font-bold text-gray-700 text-sm" x-text="'SLIDE ' + (i + 1) + ' (DOKUMENTASI)'"></span>
                    <span class="text-xs bg-[#002B4C] text-white font-semibold px-2 py-0.5 rounded" x-text="'Desain Pola ' + String.fromCharCode(65 + (i % 4))"></span>
                </div>
                <canvas :id="'feedSlide' + (i + 1)" width="1080" height="1350" class="w-full max-w-[420px] h-auto rounded-xl shadow-2xl bg-white"></canvas>
            </div>
        </template>

    </div>
</div>
@endsection

@push('scripts')
<script>
function feedGenerator() {
    return {
        kinerjaList: {!! $feedData !!},
        selectedId: '',
        dateText: '',
        selectedTitle: '',
        description: '',
        
        availableImages: [],
        coverImage: null,
        extraImages: [], 
        
        coverOffset: { x: 0, y: -15 },
        extraOffsets: [],

        logoPemkot: null,
        logoBrida: null,
        renderTimer: null,

        initCanvas() {
            this.logoPemkot = new Image();
            this.logoPemkot.crossOrigin = 'anonymous';
            this.logoPemkot.src = 'https://i.ibb.co.com/CXfMzQc/images.png';

            this.logoBrida = new Image();
            this.logoBrida.crossOrigin = 'anonymous';
            this.logoBrida.src = 'https://i.ibb.co.com/1JwDK8qG/LOGO-BRIDA-KOTA-MAKASSAR.png';

            const checkLoad = () => {
                this.renderAll();
            };

            this.logoPemkot.onload = checkLoad;
            this.logoBrida.onload = checkLoad;
        },

        getTotalSlides() {
            return 1 + Math.ceil(this.extraImages.length / 2);
        },

        shiftCover(deltaX, deltaY) {
            this.coverOffset.x = Math.max(-50, Math.min(50, this.coverOffset.x + deltaX));
            this.coverOffset.y = Math.max(-50, Math.min(50, this.coverOffset.y + deltaY));
            this.renderSlide1();
        },

        resetCoverOffset() {
            this.coverOffset = { x: 0, y: -15 };
            this.renderSlide1();
        },

        shiftExtra(idx, deltaX, deltaY) {
            if (!this.extraOffsets[idx]) {
                this.extraOffsets[idx] = { x: 0, y: 0 };
            }
            this.extraOffsets[idx].x = Math.max(-50, Math.min(50, this.extraOffsets[idx].x + deltaX));
            this.extraOffsets[idx].y = Math.max(-50, Math.min(50, this.extraOffsets[idx].y + deltaY));
            
            const slideIdx = Math.floor(idx / 2) + 1;
            this.renderExtraSlide(slideIdx);
        },

        resetExtraOffset(idx) {
            if (this.extraOffsets[idx]) {
                this.extraOffsets[idx] = { x: 0, y: 0 };
            }
            const slideIdx = Math.floor(idx / 2) + 1;
            this.renderExtraSlide(slideIdx);
        },

        resetAllOffsets() {
            this.coverOffset = { x: 0, y: -15 };
            this.extraOffsets = this.extraImages.map(() => ({ x: 0, y: 0 }));
            this.renderAll();
        },

        copyCaption() {
            const hashtags = '#BRIDAMakassar #MakassarMULIA #MunafriArifuddin #Riset #KelompokRiset #EvaluasiRiset #InovasiDaerah #PembangunanBerbasisRiset';
            const title = (this.selectedTitle || '').trim();
            const desc = (this.description || '').trim();
            const date = (this.dateText || '').trim();

            const fullCaption = `${title}\n\n${desc}\n\n🗓️ Tanggal: ${date}\n📍 Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar\n\n.\n.\n${hashtags}`;

            navigator.clipboard.writeText(fullCaption).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Caption Tersalin!',
                    text: 'Caption Instagram lengkap dengan hashtag berhasil disalin ke clipboard.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(() => {
                Swal.fire('Gagal', 'Gagal menyalin teks ke clipboard.', 'error');
            });
        },

        onSelectKinerja() {
            const selected = this.kinerjaList.find(k => k.id == this.selectedId);
            if (selected) {
                this.dateText = (selected.date || '').toUpperCase();
                
                const rawTitle = selected.title || '';
                this.selectedTitle = rawTitle.length > 140 ? rawTitle.substring(0, 137) + '...' : rawTitle;

                const rawDesc = selected.description || '';
                this.description = rawDesc.length > 250 ? rawDesc.substring(0, 247) + '...' : rawDesc;

                this.availableImages = selected.images || [];
                this.extraImages = [];
                this.coverOffset = { x: 0, y: -15 };
                
                if (this.availableImages.length > 0) {
                    this.coverImage = this.availableImages[0];
                    for (let i = 1; i < this.availableImages.length; i++) {
                        this.extraImages.push(this.availableImages[i]);
                    }
                    this.extraOffsets = this.extraImages.map(() => ({ x: 0, y: 0 }));
                } else {
                    this.coverImage = null;
                    this.extraOffsets = [];
                    Swal.fire({ toast:true, position:'top-end', icon:'warning', title:'Tidak ada foto di kinerja ini', showConfirmButton:false, timer:3000 });
                }
            } else {
                this.dateText = '';
                this.selectedTitle = '';
                this.description = '';
                this.availableImages = [];
                this.coverImage = null;
                this.extraImages = [];
                this.extraOffsets = [];
            }
            
            this.$nextTick(() => { this.renderAll(); });
        },

        setCover(imgUrl) {
            this.coverImage = imgUrl;
            this.coverOffset = { x: 0, y: -15 };
            const idx = this.extraImages.indexOf(imgUrl);
            if (idx > -1) {
                this.extraImages.splice(idx, 1);
                this.extraOffsets.splice(idx, 1);
            }
            this.$nextTick(() => { this.renderAll(); });
        },

        toggleExtra(imgUrl) {
            const index = this.extraImages.indexOf(imgUrl);
            if (index > -1) {
                this.extraImages.splice(index, 1);
                this.extraOffsets.splice(index, 1);
            } else {
                this.extraImages.push(imgUrl);
                this.extraOffsets.push({ x: 0, y: 0 });
            }
            this.$nextTick(() => { this.renderAll(); });
        },

        debounceRender() {
            clearTimeout(this.renderTimer);
            this.renderTimer = setTimeout(() => {
                this.renderAll();
            }, 300);
        },

        renderAll() {
            this.renderSlide1();
            
            const extraSlideCount = Math.ceil(this.extraImages.length / 2);
            for (let i = 1; i <= extraSlideCount; i++) {
                this.renderExtraSlide(i);
            }
        },

        drawPattern(ctx, slideNumber) {
            const W = 1080;
            const H = 1350;

            ctx.fillStyle = '#f6f5f0';
            ctx.fillRect(0, 0, W, H);

            ctx.save();
            const patternType = slideNumber % 4;

            if (patternType === 1) {
                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.arc(-40, -40, 320, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.arc(-100, -100, 290, 0, Math.PI * 2);
                ctx.fill();
                ctx.lineWidth = 12;
                ctx.strokeStyle = '#ffffff';
                ctx.stroke();

                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.arc(W + 40, H + 40, 340, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.arc(W + 100, H + 100, 290, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();

            } else if (patternType === 2) {
                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.arc(W + 50, -50, 340, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.arc(W + 100, -100, 290, 0, Math.PI * 2);
                ctx.fill();
                ctx.lineWidth = 12;
                ctx.strokeStyle = '#ffffff';
                ctx.stroke();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.arc(-50, H + 50, 340, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.arc(-100, H + 100, 290, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();

            } else if (patternType === 3) {
                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.arc(-60, 0, 260, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.arc(-100, 0, 220, 0, Math.PI * 2);
                ctx.fill();
                ctx.lineWidth = 10;
                ctx.strokeStyle = '#ffffff';
                ctx.stroke();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.arc(W + 50, 675, 290, 0, Math.PI * 2);
                ctx.fill();

                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.arc(W + 100, 675, 240, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();

            } else {
                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(400, 0);
                ctx.lineTo(0, 260);
                ctx.closePath();
                ctx.fill();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.moveTo(0, 0);
                ctx.lineTo(290, 0);
                ctx.lineTo(0, 190);
                ctx.closePath();
                ctx.fill();

                ctx.fillStyle = '#7a2222';
                ctx.beginPath();
                ctx.moveTo(W, H);
                ctx.lineTo(W - 400, H);
                ctx.lineTo(W, H - 260);
                ctx.closePath();
                ctx.fill();

                ctx.fillStyle = '#002B4C';
                ctx.beginPath();
                ctx.moveTo(W, H);
                ctx.lineTo(W - 290, H);
                ctx.lineTo(W, H - 190);
                ctx.closePath();
                ctx.fill();
            }

            ctx.restore();
        },

        drawHeaderLogo(ctx, yPosition) {
            const W = 1080;
            const targetLogoH = 50;
            
            let bridaW = 190;
            if (this.logoBrida && this.logoBrida.naturalHeight) {
                bridaW = targetLogoH * (this.logoBrida.naturalWidth / this.logoBrida.naturalHeight);
            }

            let pemkotW = 44;
            if (this.logoPemkot && this.logoPemkot.naturalHeight) {
                pemkotW = targetLogoH * (this.logoPemkot.naturalWidth / this.logoPemkot.naturalHeight);
            }

            const textWidth = 150;
            const paddingHorizontal = 32;
            const dividerGap = 22;
            
            const headerBoxW = paddingHorizontal * 2 + pemkotW + dividerGap + textWidth + dividerGap + bridaW;
            const headerBoxH = 84;
            const headerBoxX = (W - headerBoxW) / 2;
            const headerBoxY = yPosition;

            this.drawRoundedRect(ctx, headerBoxX, headerBoxY, headerBoxW, headerBoxH, 18, '#ffffff', 'rgba(0,0,0,0.05)', 10);
            
            if (this.logoPemkot && this.logoPemkot.complete) {
                ctx.drawImage(this.logoPemkot, headerBoxX + paddingHorizontal, headerBoxY + (headerBoxH - targetLogoH) / 2, pemkotW, targetLogoH);
            }

            const div1X = headerBoxX + paddingHorizontal + pemkotW + dividerGap / 2;
            ctx.strokeStyle = '#d1d5db';
            ctx.lineWidth = 1.5;
            ctx.beginPath();
            ctx.moveTo(div1X, headerBoxY + 22);
            ctx.lineTo(div1X, headerBoxY + 62);
            ctx.stroke();

            ctx.fillStyle = '#111827';
            ctx.font = '900 16px Arial, Helvetica, sans-serif';
            ctx.fillText('PEMERINTAH', div1X + dividerGap / 2, headerBoxY + 38);
            ctx.fillText('KOTA MAKASSAR', div1X + dividerGap / 2, headerBoxY + 58);

            const div2X = div1X + dividerGap / 2 + textWidth + dividerGap / 2;
            ctx.beginPath();
            ctx.moveTo(div2X, headerBoxY + 22);
            ctx.lineTo(div2X, headerBoxY + 62);
            ctx.stroke();

            if (this.logoBrida && this.logoBrida.complete) {
                ctx.drawImage(this.logoBrida, div2X + dividerGap / 2, headerBoxY + (headerBoxH - targetLogoH) / 2, bridaW, targetLogoH);
            }
        },

        drawInstagramIcon(ctx, x, y, size, color) {
            ctx.save();
            ctx.strokeStyle = color;
            ctx.fillStyle = color;
            ctx.lineWidth = size * 0.09;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';

            const radius = size * 0.28;
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + size - radius, y);
            ctx.quadraticCurveTo(x + size, y, x + size, y + radius);
            ctx.lineTo(x + size, y + size - radius);
            ctx.quadraticCurveTo(x + size, y + size, x + size - radius, y + size);
            ctx.lineTo(x + radius, y + size);
            ctx.quadraticCurveTo(x, y + size, x, y + size - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            ctx.stroke();

            ctx.beginPath();
            ctx.arc(x + size / 2, y + size / 2, size * 0.24, 0, Math.PI * 2);
            ctx.stroke();

            ctx.beginPath();
            ctx.arc(x + size * 0.77, y + size * 0.23, size * 0.055, 0, Math.PI * 2);
            ctx.fill();

            ctx.restore();
        },

        drawFooter(ctx, yPosition, isLastSlide = false) {
            const footerW = 960;
            const footerH = 64;
            const footerX = 60;
            const footerY = yPosition;

            this.drawRoundedRect(ctx, footerX, footerY, footerW, footerH, 32, '#ffffff');

            this.drawInstagramIcon(ctx, footerX + 45, footerY + 20, 24, '#7a2222');

            ctx.fillStyle = '#002B4C';
            ctx.font = 'bold 19px Arial, Helvetica, sans-serif';
            ctx.fillText('@bridakotamakassar', footerX + 78, footerY + 39);

            ctx.fillText('🌐  sigap.brida.kotamakassar.go.id', footerX + footerW - 365, footerY + 39);

            if (isLastSlide) {
                ctx.fillStyle = '#6b7280';
                ctx.font = 'bold 14px Arial, Helvetica, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('DESAIN INI DIGENERATE SECARA OTOMATIS MENGGUNAKAN SIGAP FEED', 1080 / 2, footerY + 85);
                ctx.textAlign = 'left';
            }
        },

        async renderSlide1() {
            const canvas = document.getElementById('feedSlide1');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            
            this.drawPattern(ctx, 1);
            this.drawHeaderLogo(ctx, 45);

            // Foto Cover (Tinggi 650px)
            await this.drawPhoto(ctx, this.coverImage, 60, 145, 960, 650, this.coverOffset);

            // Box Informasi (Tinggi 320px)
            const infoBoxW = 960;
            const infoBoxH = 320;
            const infoBoxX = 60;
            const infoBoxY = 820;

            this.drawRoundedRect(ctx, infoBoxX, infoBoxY, infoBoxW, infoBoxH, 26, '#ffffff', 'rgba(0,0,0,0.05)', 15);

            // 1. Badge Tanggal
            const dateStr = this.dateText || 'TANGGAL KEGIATAN';
            ctx.font = 'bold 18px Arial, Helvetica, sans-serif';
            const dateWidth = ctx.measureText(dateStr).width + 32;
            this.drawRoundedRect(ctx, infoBoxX + 32, infoBoxY + 22, dateWidth, 36, 8, '#7a2222');
            ctx.fillStyle = '#ffffff';
            ctx.fillText(dateStr, infoBoxX + 48, infoBoxY + 46);

            // 2. Judul Kegiatan (Tidak CAPSLOCK, font 28px, max 3 baris)
            ctx.fillStyle = '#002B4C';
            ctx.font = '900 28px Arial, Helvetica, sans-serif';
            const titleStr = (this.selectedTitle || '').trim() || 'Judul / Nama Kegiatan Akan Tampil Disini';
            const titleResult = this.wrapText(ctx, titleStr, infoBoxX + 32, infoBoxY + 95, infoBoxW - 64, 36, 3);

            // 3. Deskripsi Kegiatan (Jarak dinamis adaptif mengikuti jumlah baris judul)
            const descStartY = titleResult.lastY + 28;
            const descMaxLines = titleResult.lineCount >= 3 ? 2 : 3;

            ctx.fillStyle = '#374151';
            ctx.font = '500 20px Arial, Helvetica, sans-serif';
            const descStr = (this.description || '').trim() || 'Deskripsi kegiatan akan ditampilkan di area ini.';
            this.wrapText(ctx, descStr, infoBoxX + 32, descStartY, infoBoxW - 64, 26, descMaxLines);

            const isLast = (this.extraImages.length === 0);
            this.drawFooter(ctx, 1180, isLast);
        },

        async renderExtraSlide(index) {
            const slideNumber = index + 1;
            const canvasId = 'feedSlide' + slideNumber;
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            
            this.drawPattern(ctx, slideNumber);
            this.drawHeaderLogo(ctx, 45);

            const imgIndex1 = (index - 1) * 2;
            const imgIndex2 = imgIndex1 + 1;
            
            const img1 = this.extraImages[imgIndex1];
            const img2 = this.extraImages[imgIndex2];

            const offset1 = this.extraOffsets[imgIndex1] || { x: 0, y: 0 };
            const offset2 = this.extraOffsets[imgIndex2] || { x: 0, y: 0 };

            if (img1 && !img2) {
                await this.drawPhoto(ctx, img1, 60, 145, 960, 995, offset1);
            } else if (img1 && img2) {
                await this.drawPhoto(ctx, img1, 60, 145, 960, 485, offset1);
                await this.drawPhoto(ctx, img2, 60, 655, 960, 485, offset2);
            }

            const totalSlides = this.getTotalSlides();
            const isLast = (slideNumber === totalSlides);

            this.drawFooter(ctx, 1180, isLast);
        },

        drawRoundedRect(ctx, x, y, width, height, radius, fill, shadowColor = null, shadowBlur = 0) {
            ctx.save();
            if (shadowColor) {
                ctx.shadowColor = shadowColor;
                ctx.shadowBlur = shadowBlur;
                ctx.shadowOffsetY = 4;
            }
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
            ctx.fillStyle = fill;
            ctx.fill();
            ctx.restore();
        },

        wrapText(ctx, text, x, y, maxWidth, lineHeight, maxLines) {
            if (!text) return { lastY: y, lineCount: 0 };
            const words = text.split(/\s+/);
            let line = '';
            let currentLine = 1;
            let currentY = y;

            for (let n = 0; n < words.length; n++) {
                const testLine = line ? (line + ' ' + words[n]) : words[n];
                const metrics = ctx.measureText(testLine);
                const testWidth = metrics.width;

                if (testWidth > maxWidth && n > 0) {
                    if (currentLine >= maxLines) {
                        let truncated = line;
                        while (ctx.measureText(truncated + '...').width > maxWidth && truncated.length > 0) {
                            truncated = truncated.slice(0, -1);
                        }
                        ctx.fillText(truncated + '...', x, currentY);
                        return { lastY: currentY, lineCount: currentLine };
                    }
                    ctx.fillText(line, x, currentY);
                    line = words[n];
                    currentY += lineHeight;
                    currentLine++;
                } else {
                    line = testLine;
                }
            }
            if (line) {
                ctx.fillText(line, x, currentY);
            }
            return { lastY: currentY, lineCount: currentLine };
        },

        async drawPhoto(ctx, imgUrl, x, y, w, h, offset = { x: 0, y: 0 }) {
            ctx.save();
            this.drawRoundedRect(ctx, x, y, w, h, 24, '#e5e7eb');
            ctx.clip();

            if (imgUrl) {
                try {
                    const img = await this.loadImage(imgUrl);
                    const imgRatio = img.width / img.height;
                    const containerRatio = w / h;
                    let renderW, renderH, baseOffsetX, baseOffsetY;

                    if (imgRatio > containerRatio) {
                        renderH = h;
                        renderW = h * imgRatio;
                        baseOffsetX = x - (renderW - w) / 2;
                        baseOffsetY = y;
                    } else {
                        renderW = w;
                        renderH = w / imgRatio;
                        baseOffsetX = x;
                        baseOffsetY = y - (renderH - h) / 2;
                    }

                    const extraShiftX = (offset && offset.x) ? (w * (offset.x / 100)) : 0;
                    const extraShiftY = (offset && offset.y) ? (h * (offset.y / 100)) : 0;

                    const finalX = baseOffsetX + extraShiftX;
                    const finalY = baseOffsetY + extraShiftY;

                    ctx.drawImage(img, finalX, finalY, renderW, renderH);
                } catch(e) {
                    ctx.fillStyle = '#cbd5e1';
                    ctx.fillRect(x, y, w, h);
                }
            }

            ctx.restore();
            ctx.save();
            ctx.lineWidth = 8;
            ctx.strokeStyle = '#ffffff';
            ctx.beginPath();
            ctx.moveTo(x + 24, y);
            ctx.lineTo(x + w - 24, y);
            ctx.quadraticCurveTo(x + w, y, x + w, y + 24);
            ctx.lineTo(x + w, y + h - 24);
            ctx.quadraticCurveTo(x + w, y + h, x + w - 24, y + h);
            ctx.lineTo(x + 24, y + h);
            ctx.quadraticCurveTo(x, y + h, x, y + h - 24);
            ctx.lineTo(x, y + 24);
            ctx.quadraticCurveTo(x, y, x + 24, y);
            ctx.closePath();
            ctx.stroke();
            ctx.restore();
        },

        loadImage(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });
        },

        downloadFeed() {
            Swal.fire({
                title: 'Mengekspor Feed 4:5...',
                text: 'Menyimpan slide carousel per slide...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const downloadImage = (dataUrl, filename) => {
                const link = document.createElement('a');
                link.download = filename;
                link.href = dataUrl;
                link.click();
            };

            const canvas1 = document.getElementById('feedSlide1');
            const dataUrl1 = canvas1.toDataURL('image/jpeg', 0.85);
            downloadImage(dataUrl1, 'SIGAP_Feed_Slide_1_' + Date.now() + '.jpg');

            fetch('{{ route("sigap-story.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    kinerja_id: this.selectedId,
                    title: '(FEED 4:5) ' + this.selectedTitle,
                    image: dataUrl1
                })
            }).catch(() => {});

            const extraSlideCount = Math.ceil(this.extraImages.length / 2);
            let delay = 600;
            
            for (let i = 1; i <= extraSlideCount; i++) {
                setTimeout(() => {
                    const canvasN = document.getElementById('feedSlide' + (i + 1));
                    if (canvasN) {
                        const dataUrlN = canvasN.toDataURL('image/jpeg', 0.85);
                        downloadImage(dataUrlN, 'SIGAP_Feed_Slide_' + (i + 1) + '_' + Date.now() + '.jpg');
                    }
                }, delay);
                delay += 600;
            }

            setTimeout(() => {
                Swal.fire('Selesai!', 'Semua slide feed (4:5 Portrait) berhasil diunduh.', 'success');
            }, delay + 500);
        }
    }
}
</script>


<!-- ========================================================================= -->
<!-- [START] FITUR SEMENTARA: POPUP SEMANGAT (Hapus blok ini jika sudah tidak dipakai) -->
<!-- ========================================================================= -->
@auth
  @php
      $currentUser = auth()->user();$canSeeCheer = false;

      // 1. Role Spatie "admin"
      if (method_exists($currentUser, 'hasRole') && $currentUser->hasRole('admin')) {$canSeeCheer = true;
      }
      
      // 2. Role Spatie "magang" dengan NIP "4523023038"
      if (method_exists($currentUser, 'hasRole') &&$currentUser->hasRole('magang')) {
          $nip = (string) ($currentUser->nip ?? $currentUser->nik ?? $currentUser->username ?? '');
          if ($nip === '4523023038') {$canSeeCheer = true;
          }
      }
  @endphp

  @if($canSeeCheer)
  <script>
  document.addEventListener('DOMContentLoaded', function () {
      const today = new Date().toISOString().slice(0, 10);
      const userId = "{{ $currentUser->id }}";
      const storageKey = 'sigap_feed_cheer_' + userId + '_' + today;
      let accessCount = parseInt(localStorage.getItem(storageKey) || '0', 10);

      // Maksimal muncul 2 kali per hari
      if (accessCount < 2) {
          localStorage.setItem(storageKey, (accessCount + 1).toString());

          // Variasi nama panggilan (kadang tanpa nama)
          const names = ['', 'Dew', 'Dewi', 'Dewinda Djaledje', 'bro','pegawai brida'];
          const chosenName = names[Math.floor(Math.random() * names.length)];

         const messages = [
            'Semangat wkwk ✨',
            'Jangan lupa istirahat dan minum air putih yang cukup (ini ai) 🌸',
            'Kamu hebat dan sudah bekerja luar biasa hari ini, tetap senyum! (ini kata-kata ai) 😊',
            'Semoga lancar hari ini🌟 ',
            'Pelan-pelan tapi pasti, hasil karya dan usahamu selalu membanggakan (ini kata-kata ai) 💪'
        ];
          const chosenMessage = messages[Math.floor(Math.random() * messages.length)];

          const titleText = chosenName ? `Semangat, ${chosenName}! ✨` : 'Semangat Hari Ini! ✨';

          setTimeout(() => {
              if (typeof Swal !== 'undefined') {
                  Swal.fire({
                      title: titleText,
                      text: chosenMessage,
                      icon: 'info',
                      confirmButtonText: 'Siap, Terima Kasih bos',
                      confirmButtonColor: '#7a2222',
                      backdrop: `rgba(0,43,76,0.25)`
                  });
              }
          }, 600);
      }
  });
  </script>
  @endif
@endauth
<!-- ========================================================================= -->
<!-- [END] FITUR SEMENTARA: POPUP SEMANGAT                                      -->
<!-- ========================================================================= -->
@endpush