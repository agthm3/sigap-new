@extends('layouts.app')

@section('content')

<main class="p-4 lg:p-6 space-y-6">

    <!-- Header / Greeting -->
    <section class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900">
                Selamat datang, <span class="text-maroon">{{ Auth::user()->name }}</span> 👋
            </h1>
            <p class="text-sm text-gray-600 mt-0.5">
                Portal Utama Sistem Informasi Terintegrasi (SIGAP) BRIDA Kota Makassar.
            </p>
        </div>
    </section>

    <!-- ================= REMINDER ULANG TAHUN (H-3 s/d Hari H) ================= -->
    @if(isset($upcomingBirthdays) && $upcomingBirthdays->count() > 0)
    <section class="bg-gradient-to-r from-amber-500/10 via-rose-500/10 to-amber-500/10 border border-amber-200/80 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🎂</span>
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base">Reminder Ulang Tahun Pegawai</h3>
                    <p class="text-xs text-gray-600">Pegawai BRIDA yang merayakan ulang tahun dalam 3 hari ke depan.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-amber-500 text-white font-bold text-xs shadow-xs">
                {{ $upcomingBirthdays->count() }} Pegawai
            </span>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($upcomingBirthdays as $b)
            <div class="bg-white rounded-xl p-4 border border-gray-100 shadow-xs flex items-center gap-3 relative overflow-hidden group hover:border-amber-300 transition-all">
                @if($b->diff_days === 0)
                    <div class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] font-black px-2.5 py-0.5 rounded-bl-lg shadow-2xs animate-pulse">
                        HARI INI! 🥳
                    </div>
                @else
                    <div class="absolute top-0 right-0 bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-0.5 rounded-bl-lg">
                        H-{{ $b->diff_days }}
                    </div>
                @endif

                <img src="{{ $b->photo }}" alt="{{ $b->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-amber-200 shrink-0">
                <div class="min-w-0 flex-1">
                    <h4 class="font-bold text-gray-900 text-sm truncate group-hover:text-maroon transition-colors">{{ $b->name }}</h4>
                    <p class="text-xs text-gray-500 truncate">{{ $b->jabatan }}</p>
                    <p class="text-[11px] font-semibold text-amber-700 mt-1 flex items-center gap-1">
                        <span>🗓️ {{ $b->birth_day_month }}</span>
                        <span class="text-gray-300">•</span>
                        <span>Ulang Tahun ke-{{ $b->age }}</span>
                    </p>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- ================= SMART ALERTS ================= -->
    @if(!$hasFilledSkp || $pendingPpdCount > 0)
    <section class="space-y-3">
        @if(!$hasFilledSkp)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-rose-200 bg-rose-50 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="p-2 rounded-full bg-rose-100 text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div>
                    <h3 class="font-bold text-rose-800">SKP Bulan Ini Belum Diisi!</h3>
                    <p class="text-sm text-rose-600 mt-0.5">Anda belum mengumpulkan rekapitulasi SKP untuk bulan <b>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</b>.</p>
                </div>
            </div>
            <a href="{{ route('sigap-skp.kumpulan.index') }}" class="shrink-0 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Isi SKP Sekarang
            </a>
        </div>
        @endif

        @if($pendingPpdCount > 0)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-amber-200 bg-amber-50 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="p-2 rounded-full bg-amber-100 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </span>
                <div>
                    <h3 class="font-bold text-amber-800">Tugas PPD Menunggu Diselesaikan</h3>
                    <p class="text-sm text-amber-700 mt-0.5">Anda ditugaskan pada <b>{{ $pendingPpdCount }} Kegiatan PPD</b> yang diproses/draft.</p>
                </div>
            </div>
            <a href="{{ route('sigap-ppd.index') }}" class="shrink-0 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Lihat Tugas PPD
            </a>
        </div>
        @endif
    </section>
    @endif

    <!-- ================= PORTAL CARDS ================= -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('sigap-pegawai.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
        @else
            <div class="block rounded-xl border bg-white p-5 opacity-95">
        @endif
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Pegawai</p>
                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-lg">👥</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalPegawai) }}</p>
            <p class="text-xs text-gray-400 mt-1">Aparatur Daerah Terdaftar</p>
        @if(Auth::user()->hasRole('admin')) </a> @else </div> @endif

        <a href="{{ route('sigap-dokumen.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Dokumen</p>
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">📄</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalDokumen) }}</p>
            <p class="text-xs text-gray-400 mt-1">Arsip & Dokumen Tersimpan</p>
        </a>

        <a href="{{ route('sigap-skp.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP SKP</p>
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg">🎯</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalSkp) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Laporan Kinerja</p>
        </a>

        <a href="{{ route('sigap-inovasi.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Inovasi</p>
                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-lg">💡</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalInovasi) }}</p>
            <p class="text-xs text-gray-400 mt-1">Inovasi Daerah Tercatat</p>
        </a>

        <a href="{{ route('riset.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Riset</p>
                <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-lg">🔬</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalRiset) }}</p>
            <p class="text-xs text-gray-400 mt-1">Publikasi Riset & Penelitian</p>
        </a>

        <a href="{{ route('sigap-ppd.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP PPD</p>
                <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-lg">📸</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalPpd) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Kegiatan PPD</p>
        </a>
    </section>

    <!-- ================= CHARTS SECTION ================= -->
    <section class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-xl border bg-white p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Tren Unggahan (6 Bulan Terakhir)</h3>
            <div class="relative h-64 w-full"><canvas id="trendChart"></canvas></div>
        </div>
        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Komposisi Tahapan Inovasi</h3>
            <div class="relative h-56 w-full flex justify-center">
                @if(array_sum($stageData) > 0)
                    <canvas id="stageChart"></canvas>
                @else
                    <div class="flex items-center justify-center w-full h-full text-gray-400 text-sm">Belum ada data inovasi.</div>
                @endif
            </div>
        </div>
    </section>
</main>


<!-- ================= MODAL ULANG TAHUN & ANIMASI TIRAI ================= -->
@if(isset($todayBirthdays) && $todayBirthdays->count() > 0)
<div x-data="birthdayCelebration()"
     x-init="initModal()"
     x-show="isReady"
     x-cloak
     class="fixed inset-0 z-[9999] flex items-center justify-center overflow-hidden">
    
    <!-- TIRAI KIRI -->
    <div :class="curtainOpened ? '-translate-x-full' : 'translate-x-0'" 
         class="absolute top-0 left-0 w-1/2 h-full bg-gradient-to-r from-maroon-900 to-maroon-700 shadow-2xl transition-transform duration-[7000ms] ease-in z-50 flex items-center justify-end border-r border-maroon-500/50">
    </div>

    <!-- TIRAI KANAN -->
    <div :class="curtainOpened ? 'translate-x-full' : 'translate-x-0'" 
         class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-maroon-900 to-maroon-700 shadow-2xl transition-transform duration-[7000ms] ease-in z-50 flex items-center justify-start border-l border-maroon-500/50">
    </div>

    <!-- TOMBOL BUKA TIRAI -->
    <div x-show="!buttonPressed" 
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-90"
         class="absolute z-[60] flex flex-col items-center gap-4">
         
         <div class="w-20 h-20 bg-amber-400 rounded-full flex items-center justify-center animate-bounce shadow-amber-500/50 shadow-lg border-4 border-white/20">
            <span class="text-4xl">🎁</span>
         </div>
         <button @click="triggerSurprise()" class="px-8 py-4 bg-gradient-to-b from-amber-300 to-amber-500 hover:to-amber-600 text-maroon-900 rounded-full font-black text-lg tracking-widest shadow-2xl border-2 border-white/40 transform hover:scale-105 active:scale-95 transition-all">
             ADA KEJUTAN! BUKA SEKARANG
         </button>
    </div>

    <!-- Teks "Tunggu Sebentar..." -->
    <div x-show="buttonPressed && !curtainOpened" class="absolute z-[55] flex flex-col items-center animate-pulse">
        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-widest drop-shadow-lg">TUNGGU SEBENTAR...</h2>
    </div>

    <!-- BACKDROP MODAL -->
    <div x-show="modalVisible" 
         x-transition.opacity.duration.1000ms
         class="absolute inset-0 bg-black/60 backdrop-blur-sm z-30"></div>

    <!-- KONTEN MODAL POP-UP -->
    <div x-show="modalVisible"
         x-transition:enter="transition ease-out duration-1000"
         x-transition:enter-start="opacity-0 scale-50 translate-y-10"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="relative bg-white rounded-3xl shadow-2xl border-4 border-amber-300 w-full {{ $todayBirthdays->count() > 1 ? 'max-w-3xl' : 'max-w-lg' }} z-40 text-center p-6 sm:p-8 m-4 mt-20">
        
        <!-- Hiasan Pesta -->
        <div class="absolute -top-8 -left-8 w-24 h-24 bg-rose-400/20 rounded-full blur-xl"></div>
        <div class="absolute -bottom-8 -right-8 w-24 h-24 bg-amber-400/20 rounded-full blur-xl"></div>

        <div class="relative z-10">
            <div class="text-4xl sm:text-5xl mb-2 animate-bounce">🎉 🥳 🎂</div>
            <span class="px-4 py-1 bg-amber-100 text-amber-800 text-[11px] sm:text-xs font-black uppercase tracking-wider rounded-full border border-amber-200 inline-block mb-4 shadow-xs">
                SELAMAT ULANG TAHUN!
            </span>

            {{-- Grid Pegawai Ultah --}}
            <div class="my-2 grid {{ $todayBirthdays->count() > 1 ? 'grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-16' : 'grid-cols-1' }} mt-12 mb-6">
                @foreach($todayBirthdays as $tb)
                <!-- PERBAIKAN: pt-20 untuk memberi ruang yang cukup agar nama tidak tertimpa foto -->
                <div class="pt-24 pb-6 px-4 rounded-3xl bg-gradient-to-b from-white to-amber-50/50 border border-amber-100/50 shadow-xs flex flex-col items-center justify-center relative">
                    
                    {{-- FOTO RAKSASA MELAYANG --}}
                    <!-- PERBAIKAN: Posisi -top-16 dinaikkan menjadi -top-20 (agar center) -->
                    <div class="absolute -top-20 left-1/2 -translate-x-1/2">
                        <div class="relative">
                            <div class="absolute inset-0 bg-amber-300 rounded-full blur-sm opacity-60 animate-pulse"></div>
                            <!-- Ukuran Foto Diperbesar Menjadi w-36 h-36 -->
                            <img src="{{ $tb->photo }}" alt="{{ $tb->name }}" class="relative w-36 h-36 rounded-full object-cover ring-4 ring-amber-300 border-4 border-white shadow-xl">
                        </div>
                    </div>
                    
                    <!-- Nama diletakkan di bawah foto (Aman karena pt-24 di kontainer induk) -->
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 leading-tight">{{ $tb->name }}</h2>
                    <p class="text-xs text-gray-600 font-bold truncate max-w-[16rem] mt-1">{{ $tb->jabatan }}</p>
                    
                    <div class="mt-4 px-4 py-1.5 bg-maroon text-white rounded-lg text-xs font-extrabold shadow-sm">
                        Ke-{{ $tb->age }} Tahun 🎈
                    </div>
                    <p class="text-[10px] text-gray-400 font-semibold mt-2">Lahir: {{ $tb->tanggal_lahir }}</p>
                </div>
                @endforeach
            </div>

            <!-- Ucapan -->
            <div class="text-[11px] sm:text-xs text-gray-700 italic space-y-1.5 mt-6 mb-4 bg-gray-50/80 p-4 rounded-xl border border-gray-100">
                <p class="font-medium">"Semoga panjang umur, sehat selalu, dilimpahkan rezeki, dan senantiasa diberikan keberkahan dalam pengabdian."</p>
                <p class="font-bold text-amber-600 text-[10px] not-italic mt-1">— Keluarga Besar BRIDA Kota Makassar —</p>
            </div>

            <!-- YouTube Embedded API Player (Iframe Tersembunyi) -->
            <div class="hidden">
                <div id="youtube-player"></div>
            </div>

            <!-- Tombol Tutup 10 Detik -->
            <div class="mt-4">
                <template x-if="countdown > 0">
                    <button disabled class="w-full py-3 px-6 rounded-xl bg-gray-200 text-gray-500 font-bold text-xs cursor-not-allowed flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Menikmati Momen... Lanjutkan Dalam <strong x-text="countdown" class="text-maroon">10</strong> Detik</span>
                    </button>
                </template>

                <template x-if="countdown <= 0">
                    <button @click="closeModal()" class="w-full py-3 px-6 rounded-xl bg-maroon hover:bg-maroon-800 text-white font-bold text-sm shadow-md transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span>Tutup & Lanjutkan Pekerjaan</span>
                        <span>✨</span>
                    </button>
                </template>
            </div>
        </div>

    </div>
</div>

<!-- Library YouTube IFrame API -->
<script src="https://www.youtube.com/iframe_api"></script>
@endif

<footer class="px-4 lg:px-6 py-4 text-sm text-center text-gray-400 mt-6 border-t border-gray-100">
    © {{ date('Y') }} SIGAP BRIDA • Badan Riset dan Inovasi Daerah Kota Makassar
</footer>

<!-- Memanggil Library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxTrend = document.getElementById('trendChart');
        if(ctxTrend) {
            new Chart(ctxTrend.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendLabels) !!},
                    datasets: [
                        { label: 'Inovasi', data: {!! json_encode($trendInovasi) !!}, borderColor: '#9333ea', backgroundColor: 'rgba(147, 51, 234, 0.1)', borderWidth: 2, tension: 0.3, fill: true },
                        { label: 'Dokumen', data: {!! json_encode($trendDokumen) !!}, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', borderWidth: 2, tension: 0.3, fill: true }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
            });
        }
        const ctxStage = document.getElementById('stageChart');
        if(ctxStage) {
            new Chart(ctxStage.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($stageLabels) !!},
                    datasets: [{
                        data: {!! json_encode($stageData) !!},
                        backgroundColor: ['#800000', '#f59e0b', '#3b82f6', '#10b981', '#6b7280'], borderWidth: 0, hoverOffset: 4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } } }
            });
        }
    });

    // -----------------------------------------------------
    // LOGIKA YOUTUBE & TIRAI (ALPINE JS)
    // -----------------------------------------------------
    let ytPlayer = null;

    function onYouTubeIframeAPIReady() {
        ytPlayer = new YT.Player('youtube-player', {
            height: '10',
            width: '10',
            videoId: 'raIHudGtd_Q',
            playerVars: {
                'autoplay': 0, 
                'controls': 0,
                'rel': 0,
                'fs': 0,
                'start': 8
            },
            events: {
                'onReady': function(event) {
                    console.log("YouTube Player is ready");
                }
            }
        });
    }

    function birthdayCelebration() {
        return {
            isReady: false,       
            buttonPressed: false,  
            curtainOpened: false,  
            modalVisible: false,   
            countdown: 10,
            timer: null,

            initModal() {
                const todayStr = new Date().toISOString().split('T')[0];
                const storageKey = 'sigap_bday_' + todayStr;

                if (!localStorage.getItem(storageKey)) {
                    this.isReady = true; 
                }
            },

            triggerSurprise() {
                this.buttonPressed = true;
                
                if (ytPlayer && typeof ytPlayer.playVideo === 'function') {
                    ytPlayer.unMute();
                    ytPlayer.playVideo();
                }

                setTimeout(() => {
                    this.curtainOpened = true; 
                    
                    setTimeout(() => {
                        this.modalVisible = true;
                        this.startCountdown();
                    }, 7000); 

                }, 1000); 
            },

            startCountdown() {
                this.timer = setInterval(() => {
                    if (this.countdown > 0) {
                        this.countdown--;
                    } else {
                        clearInterval(this.timer);
                    }
                }, 1000);
            },

            closeModal() {
                const todayStr = new Date().toISOString().split('T')[0];
                const storageKey = 'sigap_bday_' + todayStr;
                
                localStorage.setItem(storageKey, '1');
                this.isReady = false;

                if (ytPlayer && typeof ytPlayer.stopVideo === 'function') {
                    ytPlayer.stopVideo();
                }
            }
        }
    }
</script>

@endsection