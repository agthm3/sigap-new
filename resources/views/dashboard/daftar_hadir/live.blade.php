<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Preview - {{ $kegiatan->nama_kegiatan }}</title>
    
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Smooth transition untuk pergantian layar */
        .layer-view {
            transition: opacity 1s ease-in-out;
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100vh;
        }

        /* Animasi Card Peserta */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.5s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-100 overflow-hidden relative font-sans">

    <div id="start-overlay" class="absolute inset-0 z-50 bg-black/80 flex flex-col items-center justify-center text-white backdrop-blur-sm">
        <h1 class="text-3xl font-bold mb-4 text-center">Live Preview Daftar Hadir</h1>
        <p class="mb-8 text-gray-300">Menampilkan Video Edukasi & Real-time Peserta Hadir (Auto-Pagination)</p>
        <button onclick="startLive()" class="px-8 py-3 bg-red-600 hover:bg-red-700 rounded-full font-bold text-lg shadow-lg shadow-red-600/30 transition-transform transform hover:scale-105">
            ▶ Mulai Putar Sekarang
        </button>
    </div>

    <div id="video-container" class="layer-view z-10 bg-black opacity-0 pointer-events-none">
        <div id="youtube-player"></div>
    </div>

    <div id="list-container" class="layer-view z-20 bg-gray-50 opacity-0 overflow-y-auto pb-10">
        <div class="sticky top-0 bg-white/90 backdrop-blur-md border-b shadow-sm p-6 z-30 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 uppercase">DAFTAR HADIR PESERTA</h1>
                <p class="text-gray-600 mt-1 font-medium text-lg">{{ $kegiatan->nama_kegiatan }}</p>
            </div>
            <div class="flex items-center gap-4">
                <div id="page-indicator" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-xl font-bold border border-gray-200 shadow-inner">
                    Hal. 1 / 1
                </div>
                
                <div class="bg-emerald-100 text-emerald-700 px-4 py-2 rounded-xl font-bold flex items-center gap-2 border border-emerald-200">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                    LIVE REALTIME
                </div>
            </div>
        </div>

        <div class="p-8">
            <div id="attendees-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                </div>
        </div>
    </div>

    <script>
        let player;
        let playCount = 0;
        let isLongPause = false;
        let showVideoPhase = true; 
        let phaseTimer;

        // Data & Pagination Absensi
        let allAttendees = [];
        let currentPage = 0;
        const ITEMS_PER_PAGE = 16; // Maksimal 16 Card (4x4 Grid)
        
        // Waktu konfigurasi (dalam milidetik)
        const TIME_SHOW_VIDEO = 30000; // Tampilkan video 30 detik
        const TIME_PER_PAGE   = 10000; // Tampilkan tiap halaman daftar hadir 10 detik
        const TIME_LONG_PAUSE = 10 * 60 * 1000; // Pause konstan daftar hadir 10 menit

        // 1. Load YouTube Iframe API Asinkronus
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-player', {
                height: '100%',
                width: '100%',
                videoId: 'Pf8pK3lU_s8', // ID Video Anda
                playerVars: {
                    'autoplay': 0, 
                    'controls': 0,
                    'disablekb': 1,
                    'modestbranding': 1,
                    'rel': 0
                },
                events: {
                    'onStateChange': onPlayerStateChange
                }
            });
        }

        // 2. Fungsi saat tombol "Mulai" ditekan
        function startLive() {
            document.getElementById('start-overlay').style.display = 'none';
            player.playVideo();
            startLoopingPhase();
        }

        // 3. Deteksi saat video selesai
        function onPlayerStateChange(event) {
            if (event.data === YT.PlayerState.ENDED) {
                playCount++;
                
                if (playCount >= 2) {
                    // Masuk fase istirahat 10 Menit (Hanya menampilkan daftar hadir berputar)
                    isLongPause = true;
                    clearTimeout(phaseTimer);
                    
                    showListView(); 
                    currentPage = 0;
                    renderCurrentPage();
                    scheduleNextPage(); 
                    
                    // Setelah 10 Menit, reset & ulangi video dari awal
                    setTimeout(() => {
                        isLongPause = false;
                        playCount = 0;
                        player.seekTo(0);
                        player.playVideo();
                        showVideoPhase = true;
                        startLoopingPhase();
                    }, TIME_LONG_PAUSE);
                    
                } else {
                    // Belum 2x putar, ulangi video lagi langsung
                    player.seekTo(0);
                    player.playVideo();
                }
            }
        }

        // 4. Logika Perpindahan Layar & Siklus Halaman
        function startLoopingPhase() {
            if (isLongPause) return; // Hentikan siklus flip jika sedang masuk sesi 10 menit
            
            if (showVideoPhase) {
                showVideoView();
                phaseTimer = setTimeout(() => {
                    showVideoPhase = false;
                    startLoopingPhase();
                }, TIME_SHOW_VIDEO);
            } else {
                showListView();
                currentPage = 0;
                renderCurrentPage();
                scheduleNextPage();
            }
        }

        // Fungsi penjadwal halaman berikutnya
        function scheduleNextPage() {
            clearTimeout(phaseTimer);
            phaseTimer = setTimeout(() => {
                let totalPages = Math.max(1, Math.ceil(allAttendees.length / ITEMS_PER_PAGE));
                currentPage++;
                
                // Jika sudah melewati halaman terakhir
                if (currentPage >= totalPages) {
                    if (!isLongPause) {
                        // Jika fase normal, kembali ke Video setelah halaman terakhir selesai
                        showVideoPhase = true;
                        startLoopingPhase();
                    } else {
                        // Jika dalam masa pause 10 menit, ulang lagi halaman absensi dari halaman 1 (Carousel effect)
                        currentPage = 0;
                        renderCurrentPage();
                        scheduleNextPage();
                    }
                } else {
                    // Pindah ke halaman selanjutnya
                    renderCurrentPage();
                    scheduleNextPage();
                }
            }, TIME_PER_PAGE);
        }

        // 5. Fungsi Render Card (Hanya memotong 16 data sesuai halaman)
        function renderCurrentPage() {
            const container = document.getElementById('attendees-grid');
            container.innerHTML = ''; 

            let totalPages = Math.max(1, Math.ceil(allAttendees.length / ITEMS_PER_PAGE));
            if (currentPage >= totalPages && totalPages > 0) {
                currentPage = 0; 
            }

            // Update teks indikator halaman
            document.getElementById('page-indicator').innerText = `Hal. ${currentPage + 1} / ${totalPages}`;

            // Potong array data sesuai halaman yang aktif (Pagination)
            let startIdx = currentPage * ITEMS_PER_PAGE;
            let endIdx = startIdx + ITEMS_PER_PAGE;
            let pageData = allAttendees.slice(startIdx, endIdx);

            pageData.forEach(p => {
                let bgHex   = p.gender === 'L' ? 'bfdbfe' : 'fbcfe8';
                let textHex = p.gender === 'L' ? '1d4ed8' : 'be185d';
                let avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(p.nama)}&background=${bgHex}&color=${textHex}&bold=true&rounded=true&size=128`;
                let cardBg = p.gender === 'L' ? 'bg-blue-50 border-blue-100' : 'bg-pink-50 border-pink-100';

                container.innerHTML += `
                    <div class="flex items-center p-4 rounded-2xl border ${cardBg} shadow-sm transform transition duration-500 hover:-translate-y-1 hover:shadow-md animate-fade-in-up">
                        <img src="${avatarUrl}" class="w-14 h-14 rounded-full shadow-sm mr-4" alt="Profil">
                        <div class="overflow-hidden">
                            <h4 class="font-bold text-gray-900 text-base truncate">${p.nama}</h4>
                            <p class="text-xs text-gray-500 truncate mt-0.5">${p.instansi}</p>
                        </div>
                    </div>
                `;
            });
        }

        // Helper Pengubah CSS Tampilan (Transisi halus)
        function showVideoView() {
            document.getElementById('video-container').style.opacity = '1';
            document.getElementById('video-container').style.pointerEvents = 'auto';
            
            document.getElementById('list-container').style.opacity = '0';
            document.getElementById('list-container').style.pointerEvents = 'none';
        }

        function showListView() {
            document.getElementById('list-container').style.opacity = '1';
            document.getElementById('list-container').style.pointerEvents = 'auto';
            
            document.getElementById('video-container').style.opacity = '0';
            document.getElementById('video-container').style.pointerEvents = 'none';
        }

        // 6. Fetch Data Realtime via AJAX (Setiap 5 detik)
        function fetchAttendees() {
            fetch('{{ route("sigap-daftar-hadir.live-data", $kegiatan->uuid) }}')
                .then(res => res.json())
                .then(data => {
                    allAttendees = data;
                    
                    // Jika sedang menampilkan layer daftar hadir, re-render halaman yang aktif agar nama baru langsung masuk tanpa menunggu ganti halaman
                    if (document.getElementById('list-container').style.opacity === '1') {
                        renderCurrentPage();
                    }
                })
                .catch(err => console.error("Gagal menarik data:", err));
        }

        // Jalankan penarik data realtime setiap 5 detik
        setInterval(fetchAttendees, 5000);
        fetchAttendees(); // Fetch pertama kali saat load
    </script>
</body>
</html>