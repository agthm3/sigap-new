@extends('layouts.app')

@section('content')
@php
    $repo = app(\App\Repositories\KinerjaRepository::class);
    $storyData = collect($kinerjaItems)->map(function($m) use ($repo) {
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

<div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6 items-start" x-data="storyGenerator()" x-init="initCanvas()">
    
    <!-- Form Control (Kiri) -->
    <div class="w-full lg:w-1/3 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm sticky top-24 max-h-[85vh] overflow-y-auto">
        <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Pengaturan SIGAP Story</h2>
        
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
                <input type="text" x-model="dateText" @input="renderCanvas" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 uppercase font-bold shadow-sm">
            </label>

            <label class="block">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Judul Story</span>
                    <span class="text-xs font-semibold text-gray-400" x-text="selectedTitle.length + '/90'"></span>
                </div>
                <textarea x-model="selectedTitle" @input="renderCanvas" rows="2" maxlength="90" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 font-semibold shadow-sm focus:ring-maroon focus:border-maroon"></textarea>
            </label>

            <label class="block">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-700">Deskripsi</span>
                    <span class="text-xs font-semibold text-gray-400" x-text="description.length + '/250'"></span>
                </div>
                <textarea x-model="description" @input="renderCanvas" rows="4" maxlength="250" class="mt-1 w-full rounded-lg border-gray-300 p-2.5 text-sm shadow-sm focus:ring-maroon focus:border-maroon"></textarea>
            </label>

            <div class="border-t pt-4">
                <span class="text-sm font-semibold text-gray-700 block mb-2">2. Pilih Layout Foto</span>
                <div class="flex gap-2">
                    <button @click="setLayout(2)" :class="layoutMode === 2 ? 'bg-maroon text-white ring-2 ring-maroon' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" type="button" class="flex-1 py-2 rounded-lg font-bold text-sm transition-all">2 Foto</button>
                    <button @click="setLayout(4)" :class="layoutMode === 4 ? 'bg-maroon text-white ring-2 ring-maroon' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'" type="button" class="flex-1 py-2 rounded-lg font-bold text-sm transition-all">4 Foto</button>
                </div>
            </div>

            <div x-show="availableImages.length > 0" class="pt-2">
                <span class="text-sm font-semibold text-gray-700 block mb-2">
                    3. Pilih <span x-text="layoutMode"></span> Foto (<span x-text="selectedImages.length"></span>/<span x-text="layoutMode"></span>)
                </span>
                <div class="grid grid-cols-3 gap-2">
                    <template x-for="(img, idx) in availableImages" :key="idx">
                        <div @click="toggleImage(img)" class="relative h-20 bg-gray-200 rounded-lg cursor-pointer overflow-hidden border-2 transition-all" :class="selectedImages.includes(img) ? 'border-maroon shadow-md' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img :src="img" class="w-full h-full object-cover">
                            <template x-if="selectedImages.includes(img)">
                                <div class="absolute inset-0 bg-maroon/30 flex items-center justify-center">
                                    <span class="bg-maroon text-white text-xs font-bold w-6 h-6 rounded-full flex items-center justify-center" x-text="selectedImages.indexOf(img) + 1"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <button @click="downloadStory()" :disabled="!selectedId || selectedImages.length !== layoutMode" 
            :class="(!selectedId || selectedImages.length !== layoutMode) ? 'bg-gray-300 cursor-not-allowed' : 'bg-maroon hover:bg-maroon-800'"
            class="w-full text-white font-bold py-3 rounded-xl mt-6 transition-colors shadow-lg">
            <span x-text="selectedImages.length === layoutMode ? '⬇️ Download & Simpan Story' : 'Pilih Foto Terlebih Dahulu'"></span>
        </button>
    </div>

    <!-- Preview Canvas Area (Kanan) -->
    <div class="w-full lg:w-2/3 flex justify-center bg-gray-200 p-6 rounded-2xl overflow-hidden border border-gray-300 shadow-inner">
        <canvas id="storyCanvas" width="1080" height="1920" class="w-[360px] md:w-[450px] lg:w-[480px] h-auto rounded-2xl shadow-2xl bg-white"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
function storyGenerator() {
    return {
        kinerjaList: {!! $storyData !!},
        selectedId: '',
        dateText: '',
        selectedTitle: '',
        description: '',
        layoutMode: 2, 
        availableImages: [],
        selectedImages: [], 
        canvas: null,
        ctx: null,

        logoPemkot: null,
        logoBrida: null,

        initCanvas() {
            this.canvas = document.getElementById('storyCanvas');
            this.ctx = this.canvas.getContext('2d');
            
            this.logoPemkot = new Image();
            this.logoPemkot.crossOrigin = 'anonymous';
            this.logoPemkot.src = 'https://i.ibb.co.com/CXfMzQc/images.png';

            this.logoBrida = new Image();
            this.logoBrida.crossOrigin = 'anonymous';
            this.logoBrida.src = 'https://i.ibb.co.com/1JwDK8qG/LOGO-BRIDA-KOTA-MAKASSAR.png';

            Promise.all([
                new Promise(r => this.logoPemkot.onload = r),
                new Promise(r => this.logoBrida.onload = r)
            ]).then(() => {
                this.renderCanvas();
            }).catch(() => {
                this.renderCanvas();
            });
        },

        onSelectKinerja() {
            const selected = this.kinerjaList.find(k => k.id == this.selectedId);
            if (selected) {
                this.dateText = selected.date.toUpperCase();
                const rawTitle = selected.title || '';
                this.selectedTitle = rawTitle.length > 90 ? rawTitle.substring(0, 87) + '...' : rawTitle;

                const rawDesc = selected.description || '';
                this.description = rawDesc.length > 250 ? rawDesc.substring(0, 247) + '...' : rawDesc;

                this.availableImages = selected.images;
                this.selectedImages = [];
                
                if (this.availableImages.length >= this.layoutMode) {
                    this.selectedImages = this.availableImages.slice(0, this.layoutMode);
                }
            } else {
                this.dateText = '';
                this.selectedTitle = '';
                this.description = '';
                this.availableImages = [];
                this.selectedImages = [];
            }
            this.renderCanvas();
        },

        setLayout(mode) {
            this.layoutMode = mode;
            if (this.selectedImages.length > mode) {
                this.selectedImages = this.selectedImages.slice(0, mode);
            }
            this.renderCanvas();
        },

        toggleImage(imgUrl) {
            const index = this.selectedImages.indexOf(imgUrl);
            if (index > -1) {
                this.selectedImages.splice(index, 1);
            } else {
                if (this.selectedImages.length < this.layoutMode) {
                    this.selectedImages.push(imgUrl);
                }
            }
            this.renderCanvas();
        },

        async renderCanvas() {
            if (!this.ctx) return;
            const ctx = this.ctx;
            const W = 1080;
            const H = 1920;

            // 1. Background Kertas
            ctx.fillStyle = '#f6f5f0';
            ctx.fillRect(0, 0, W, H);

            // 2. Ornamen Sudut Atas (Navy & Maroon)
            ctx.save();
            ctx.fillStyle = '#002B4C';
            ctx.beginPath();
            ctx.arc(-50, -50, 480, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = '#7a2222';
            ctx.beginPath();
            ctx.arc(-120, -120, 440, 0, Math.PI * 2);
            ctx.fill();
            ctx.lineWidth = 14;
            ctx.strokeStyle = '#ffffff';
            ctx.stroke();

            // Ornamen Sudut Bawah
            ctx.fillStyle = '#002B4C';
            ctx.beginPath();
            ctx.arc(W + 50, H + 50, 520, 0, Math.PI * 2);
            ctx.fill();

            ctx.fillStyle = '#7a2222';
            ctx.beginPath();
            ctx.arc(W + 120, H + 120, 460, 0, Math.PI * 2);
            ctx.fill();
            ctx.stroke();
            ctx.restore();

            // 3. Header Logo Box (Center Otomatis dengan Proporsi Asli)
            const headerBoxH = 100;
            const targetLogoH = 64; // Tinggi patokan logo

            // Hitung lebar logo BRIDA berdasarkan aspek rasio aslinya
            let bridaW = 240;
            if (this.logoBrida && this.logoBrida.naturalHeight) {
                const bridaRatio = this.logoBrida.naturalWidth / this.logoBrida.naturalHeight;
                bridaW = targetLogoH * bridaRatio;
            }

            // Hitung lebar logo Pemkot berdasarkan rasio aslinya
            let pemkotW = 55;
            if (this.logoPemkot && this.logoPemkot.naturalHeight) {
                const pemkotRatio = this.logoPemkot.naturalWidth / this.logoPemkot.naturalHeight;
                pemkotW = targetLogoH * pemkotRatio;
            }

            // Hitung total lebar box agar presisi di tengah
            const textWidth = 190;
            const paddingHorizontal = 40;
            const dividerGap = 25;
            
            const headerBoxW = paddingHorizontal * 2 + pemkotW + dividerGap + textWidth + dividerGap + bridaW;
            const headerBoxX = (W - headerBoxW) / 2;
            const headerBoxY = 70;

            this.drawRoundedRect(ctx, headerBoxX, headerBoxY, headerBoxW, headerBoxH, 20, '#ffffff');
            
            // Draw Logo Pemkot (Proporsional)
            if (this.logoPemkot && this.logoPemkot.complete) {
                ctx.drawImage(this.logoPemkot, headerBoxX + paddingHorizontal, headerBoxY + (headerBoxH - targetLogoH) / 2, pemkotW, targetLogoH);
            }

            // Divider 1
            const div1X = headerBoxX + paddingHorizontal + pemkotW + dividerGap / 2;
            ctx.strokeStyle = '#d1d5db';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(div1X, headerBoxY + 22);
            ctx.lineTo(div1X, headerBoxY + 78);
            ctx.stroke();

            // Text Pemkot
            ctx.fillStyle = '#111827';
            ctx.font = '900 20px Arial, Helvetica, sans-serif';
            ctx.fillText('PEMERINTAH', div1X + dividerGap / 2, headerBoxY + 44);
            ctx.fillText('KOTA MAKASSAR', div1X + dividerGap / 2, headerBoxY + 68);

            // Divider 2
            const div2X = div1X + dividerGap / 2 + textWidth + dividerGap / 2;
            ctx.beginPath();
            ctx.moveTo(div2X, headerBoxY + 22);
            ctx.lineTo(div2X, headerBoxY + 78);
            ctx.stroke();

            // Logo BRIDA (Terkunci Sesuai Aspek Rasio Asli / Tidak Gepeng)
            if (this.logoBrida && this.logoBrida.complete) {
                ctx.drawImage(this.logoBrida, div2X + dividerGap / 2, headerBoxY + (headerBoxH - targetLogoH) / 2, bridaW, targetLogoH);
            }

            // 4. Box Informasi Kegiatan
            const infoBoxW = 940;
            const infoBoxH = 340;
            const infoBoxX = 70;
            const infoBoxY = 200;

            this.drawRoundedRect(ctx, infoBoxX, infoBoxY, infoBoxW, infoBoxH, 30, '#ffffff', 'rgba(0,0,0,0.05)', 20);

            // Badge Tanggal
            const dateStr = this.dateText || 'TANGGAL KEGIATAN';
            ctx.font = 'bold 20px Arial, Helvetica, sans-serif';
            const dateWidth = ctx.measureText(dateStr).width + 30;
            this.drawRoundedRect(ctx, infoBoxX + 40, infoBoxY + 30, dateWidth, 42, 10, '#7a2222');
            
            ctx.fillStyle = '#ffffff';
            ctx.fillText(dateStr, infoBoxX + 55, infoBoxY + 58);

            // Judul Kegiatan
            ctx.fillStyle = '#002B4C';
            ctx.font = '900 34px Arial, Helvetica, sans-serif';
            const titleStr = this.selectedTitle || 'JUDUL / NAMA KEGIATAN AKAN TAMPIL DISINI';
            this.wrapText(ctx, titleStr, infoBoxX + 40, infoBoxY + 115, infoBoxW - 80, 42, 2);

            // Deskripsi Kegiatan
            ctx.fillStyle = '#374151';
            ctx.font = '500 22px Arial, Helvetica, sans-serif';
            const descStr = this.description || 'Deskripsi kegiatan akan ditampilkan di area ini. Pilih kegiatan di sebelah kiri untuk mengisi teks secara otomatis.';
            this.wrapText(ctx, descStr, infoBoxX + 40, infoBoxY + 215, infoBoxW - 80, 30, 3);

            // 5. Grid Foto Kolase
            const photoY = 570;
            const photoW = 940;
            const photoH = 1140;

            if (this.layoutMode === 2) {
                const singleH = (photoH - 30) / 2;
                await this.drawPhoto(ctx, this.selectedImages[0], 70, photoY, photoW, singleH);
                await this.drawPhoto(ctx, this.selectedImages[1], 70, photoY + singleH + 30, photoW, singleH);
            } else {
                const bigH = 430;
                const midH = 220;
                await this.drawPhoto(ctx, this.selectedImages[0], 70, photoY, photoW, bigH);
                
                const halfW = (photoW - 20) / 2;
                await this.drawPhoto(ctx, this.selectedImages[1], 70, photoY + bigH + 20, halfW, midH);
                await this.drawPhoto(ctx, this.selectedImages[2], 70 + halfW + 20, photoY + bigH + 20, halfW, midH);
                
                await this.drawPhoto(ctx, this.selectedImages[3], 70, photoY + bigH + midH + 40, photoW, bigH);
            }

            // 6. Footer Social Media
            const footerW = 940;
            const footerH = 75;
            const footerX = 70;
            const footerY = 1740;

            this.drawRoundedRect(ctx, footerX, footerY, footerW, footerH, 40, '#ffffff');

            ctx.fillStyle = '#002B4C';
            ctx.font = 'bold 22px Arial, Helvetica, sans-serif';
            ctx.fillText('📷  @bridakotamakassar', footerX + 50, footerY + 46);
            ctx.fillText('🌐  sigap.brida.kotamakassar.go.id', footerX + footerW - 400, footerY + 46);

            // 7. Watermark
            ctx.fillStyle = '#6b7280';
            ctx.font = 'bold 16px Arial, Helvetica, sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('DESAIN INI DIGENERATE SECARA OTOMATIS MENGGUNAKAN SIGAP STORY', W / 2, 1860);
            ctx.textAlign = 'left';
        },

        drawRoundedRect(ctx, x, y, width, height, radius, fill, shadowColor = null, shadowBlur = 0) {
            ctx.save();
            if (shadowColor) {
                ctx.shadowColor = shadowColor;
                ctx.shadowBlur = shadowBlur;
                ctx.shadowOffsetY = 6;
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
            const words = (text || '').split(' ');
            let line = '';
            let lineCount = 0;

            for (let n = 0; n < words.length; n++) {
                const testLine = line + words[n] + ' ';
                const metrics = ctx.measureText(testLine);
                const testWidth = metrics.width;
                if (testWidth > maxWidth && n > 0) {
                    ctx.fillText(line.trim(), x, y);
                    line = words[n] + ' ';
                    y += lineHeight;
                    lineCount++;
                    if (lineCount >= maxLines - 1) {
                        break;
                    }
                } else {
                    line = testLine;
                }
            }
            if (lineCount < maxLines) {
                ctx.fillText(line.trim(), x, y);
            }
        },

        async drawPhoto(ctx, imgUrl, x, y, w, h) {
            ctx.save();
            this.drawRoundedRect(ctx, x, y, w, h, 28, '#e5e7eb');
            ctx.clip();

            if (imgUrl) {
                try {
                    const img = await this.loadImage(imgUrl);
                    const imgRatio = img.width / img.height;
                    const containerRatio = w / h;
                    let renderW, renderH, offsetX, offsetY;

                    if (imgRatio > containerRatio) {
                        renderH = h;
                        renderW = h * imgRatio;
                        offsetX = x - (renderW - w) / 2;
                        offsetY = y;
                    } else {
                        renderW = w;
                        renderH = w / imgRatio;
                        offsetX = x;
                        offsetY = y - (renderH - h) * 0.15;
                    }

                    ctx.drawImage(img, offsetX, offsetY, renderW, renderH);
                } catch(e) {
                    ctx.fillStyle = '#cbd5e1';
                    ctx.fillRect(x, y, w, h);
                }
            } else {
                ctx.fillStyle = '#cbd5e1';
                ctx.fillRect(x, y, w, h);
            }

            ctx.restore();
            ctx.save();
            ctx.lineWidth = 10;
            ctx.strokeStyle = '#ffffff';
            this.drawRoundedRectStroke(ctx, x, y, w, h, 28);
            ctx.restore();
        },

        drawRoundedRectStroke(ctx, x, y, width, height, radius) {
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
            ctx.stroke();
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

        downloadStory() {
            Swal.fire({
                title: 'Menyimpan Story...',
                text: 'Memproses gambar dan menyimpan ke riwayat...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const dataUrl = this.canvas.toDataURL('image/jpeg', 0.85);

            fetch('{{ route("sigap-story.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    kinerja_id: this.selectedId,
                    title: this.selectedTitle,
                    image: dataUrl
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const link = document.createElement('a');
                    link.download = 'SIGAP_Story_' + Date.now() + '.jpg';
                    link.href = dataUrl;
                    link.click();
                    Swal.fire('Berhasil!', 'Story telah diunduh dan tersimpan ke riwayat log.', 'success');
                } else {
                    throw new Error('Gagal simpan');
                }
            })
            .catch(err => {
                const link = document.createElement('a');
                link.download = 'SIGAP_Story_' + Date.now() + '.jpg';
                link.href = dataUrl;
                link.click();
                Swal.fire('Info', 'Story berhasil di-download.', 'info');
            });
        }
    }
}
</script>
@endpush