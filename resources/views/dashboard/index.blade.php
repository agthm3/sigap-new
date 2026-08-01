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

    <!-- ================= SMART ALERTS ================= -->
    @if(!$hasFilledSkp || $pendingPpdCount > 0)
    <section class="space-y-3">
        
        <!-- Alert SKP -->
        @if(!$hasFilledSkp)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-rose-200 bg-rose-50 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="p-2 rounded-full bg-rose-100 text-rose-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div>
                    <h3 class="font-bold text-rose-800">SKP Bulan Ini Belum Diisi!</h3>
                    <p class="text-sm text-rose-600 mt-0.5">Anda belum mengumpulkan rekapitulasi SKP untuk bulan <b>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</b>. Hal ini wajib dilakukan setiap bulan.</p>
                </div>
            </div>
            <a href="{{ route('sigap-skp.kumpulan.index') }}" class="shrink-0 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Isi SKP Sekarang
            </a>
        </div>
        @endif

        <!-- Alert PPD -->
        @if($pendingPpdCount > 0)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl border border-amber-200 bg-amber-50 shadow-sm">
            <div class="flex items-start gap-3">
                <span class="p-2 rounded-full bg-amber-100 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </span>
                <div>
                    <h3 class="font-bold text-amber-800">Tugas PPD Menunggu Diselesaikan</h3>
                    <p class="text-sm text-amber-700 mt-0.5">Anda ditugaskan pada <b>{{ $pendingPpdCount }} Kegiatan PPD</b> yang saat ini statusnya masih diproses/draft.</p>
                </div>
            </div>
            <a href="{{ route('sigap-ppd.index') }}" class="shrink-0 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Lihat Tugas PPD
            </a>
        </div>
        @endif
        
    </section>
    @endif

    <!-- ================= PORTAL CARDS (Metrik Global) ================= -->
    <section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        
        <!-- 1. PEGAWAI (Disable click untuk employee) -->
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

        <!-- 2. DOKUMEN -->
        <a href="{{ route('sigap-dokumen.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Dokumen</p>
                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">📄</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalDokumen) }}</p>
            <p class="text-xs text-gray-400 mt-1">Arsip & Dokumen Tersimpan</p>
        </a>

        <!-- 3. SKP -->
        <a href="{{ route('sigap-skp.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP SKP</p>
                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg">🎯</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalSkp) }}</p>
            <p class="text-xs text-gray-400 mt-1">Total Laporan Kinerja</p>
        </a>

        <!-- 4. INOVASI -->
        <a href="{{ route('sigap-inovasi.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Inovasi</p>
                <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-lg">💡</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalInovasi) }}</p>
            <p class="text-xs text-gray-400 mt-1">Inovasi Daerah Tercatat</p>
        </a>

        <!-- 5. RISET -->
        <a href="{{ route('riset.index') }}" class="group block rounded-xl border bg-white p-5 hover:shadow-lg hover:border-maroon transition duration-300">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm font-semibold text-gray-500 group-hover:text-maroon transition">SIGAP Riset</p>
                <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center text-lg">🔬</div>
            </div>
            <p class="text-3xl font-extrabold text-gray-900 group-hover:text-maroon transition">{{ number_format($totalRiset) }}</p>
            <p class="text-xs text-gray-400 mt-1">Publikasi Riset & Penelitian</p>
        </a>

        <!-- 6. PPD -->
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
        
        <!-- Line Chart: Tren Unggahan -->
        <div class="lg:col-span-2 rounded-xl border bg-white p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Tren Unggahan (6 Bulan Terakhir)</h3>
            <div class="relative h-64 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Doughnut Chart: Komposisi Inovasi -->
        <div class="rounded-xl border bg-white p-5 shadow-sm">
            <h3 class="font-bold text-gray-800 mb-4">Komposisi Tahapan Inovasi</h3>
            <div class="relative h-56 w-full flex justify-center">
                @if(array_sum($stageData) > 0)
                    <canvas id="stageChart"></canvas>
                @else
                    <div class="flex items-center justify-center w-full h-full text-gray-400 text-sm">
                        Belum ada data inovasi.
                    </div>
                @endif
            </div>
        </div>

    </section>

</main>

<!-- Footer -->
<footer class="px-4 lg:px-6 py-4 text-sm text-center text-gray-400 mt-6 border-t border-gray-100">
    © {{ date('Y') }} SIGAP BRIDA • Badan Riset dan Inovasi Daerah Kota Makassar
</footer>

<!-- Memanggil Library Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Inisialisasi Trend Chart (Line)
        const ctxTrend = document.getElementById('trendChart');
        if(ctxTrend) {
            new Chart(ctxTrend.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($trendLabels) !!},
                    datasets: [
                        {
                            label: 'Inovasi',
                            data: {!! json_encode($trendInovasi) !!},
                            borderColor: '#9333ea', // Purple
                            backgroundColor: 'rgba(147, 51, 234, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                        },
                        {
                            label: 'Dokumen',
                            data: {!! json_encode($trendDokumen) !!},
                            borderColor: '#10b981', // Emerald
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        // 2. Inisialisasi Stage Chart (Doughnut)
        const ctxStage = document.getElementById('stageChart');
        if(ctxStage) {
            new Chart(ctxStage.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($stageLabels) !!},
                    datasets: [{
                        data: {!! json_encode($stageData) !!},
                        backgroundColor: [
                            '#800000', // Maroon
                            '#f59e0b', // Amber
                            '#3b82f6', // Blue
                            '#10b981', // Emerald
                            '#6b7280'  // Gray
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });
        }
    });
</script>

@endsection