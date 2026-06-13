<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - SIGAP SPJ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafb;
        }
        .maroon-gradient {
            background: linear-gradient(135deg, #7a2222 0%, #4a1111 100%);
        }
        .text-maroon {
            color: #7a2222;
        }
        .border-maroon {
            border-color: #7a2222;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-6">

    <div class="max-w-xl w-full text-center bg-white rounded-2xl shadow-xl border border-gray-100 p-8 md:p-12 transition-all transform hover:scale-[1.01]">
        
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 rounded-2xl maroon-gradient flex items-center justify-center shadow-lg shadow-red-900/20">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>

        <h1 class="text-7xl font-extrabold tracking-tight text-gray-300 mb-2">@yield('code')</h1>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-4 uppercase tracking-wide">@yield('heading')</h2>
        
        <p class="text-gray-500 text-base leading-relaxed mb-8">
            @yield('message')
        </p>

        <div class="w-1/4 h-1 maroon-gradient mx-auto rounded-full mb-8"></div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ url()->previous() }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-sm font-semibold rounded-xl text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Halaman Sebelumnya
            </a>
            <a href="{{ url('/dashboard') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-semibold rounded-xl text-white maroon-gradient hover:opacity-95 transition-opacity shadow-md shadow-red-900/10 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Masuk ke Dashboard
            </a>
        </div>

        <div class="mt-12 pt-6 border-t border-gray-100 text-xs text-gray-400">
            Sistem Informasi Gabungan Arsip & Pegawai (<span class="font-semibold text-maroon">SIGAP BRIDA</span>)<br>
            <strong>Badan Riset dan Inovasi Daerah (BRIDA) Kota Makassar</strong>
        </div>

    </div>

</body>
</html>