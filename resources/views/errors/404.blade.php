<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Page Not Found - MediVault</title>
        <!-- Google Fonts: Outfit -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css?family=Outfit:300,400,500,600,700,800&display=swap" rel="stylesheet">
        <!-- Tailwind CSS (via Vite) or Fallback styling -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
        <style>
            body {
                font-family: 'Outfit', sans-serif;
                background-color: #FDFBF7;
            }
        </style>
    </head>
    <body class="antialiased text-[#3E2723] min-h-screen flex flex-col justify-between selection:bg-[#5D4037] selection:text-[#FFF8E1]">
        
        <!-- Header / Logo -->
        <header class="w-full max-w-7xl mx-auto px-6 py-5 flex items-center justify-start">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#5D4037] flex items-center justify-center shadow-md">
                    <span class="text-lg">🏥</span>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-[#3E2723] tracking-tight">MediVault</h1>
                    <p class="text-[9px] font-bold text-[#8D6E63] uppercase tracking-wider -mt-1">Secure Health Records</p>
                </div>
            </div>
        </header>

        <!-- Centered 404 message -->
        <main class="flex-grow flex flex-col items-center justify-center px-6 text-center space-y-6 max-w-xl mx-auto">
            <div class="w-24 h-24 rounded-3xl bg-[#FFF8E1] border border-[#FFE082] flex items-center justify-center text-4xl shadow-sm animate-bounce">
                🔍
            </div>
            
            <h2 class="text-3xl md:text-4xl font-extrabold text-[#3E2723] tracking-tight">
                Oops! Page Not Found
            </h2>

            <p class="text-sm text-[#8D6E63] font-medium leading-relaxed">
                The clinical path or record file you are looking for does not exist, has been archived in the recycle bin, or you lack the necessary consent credentials to view it.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3 w-full">
                <a href="{{ url('/') }}" 
                   class="w-full sm:w-auto px-6 py-3 bg-[#FDFBF7] border border-[#D7CCC8] hover:bg-[#F5F2EB] text-[#5D4037] font-extrabold text-xs rounded-xl shadow-xs transition duration-150 text-center active:scale-[0.98]">
                    Home Page
                </a>
                <a href="{{ url('/dashboard') }}" 
                   class="w-full sm:w-auto px-6 py-3 bg-[#5D4037] hover:bg-[#3E2723] text-[#FFF8E1] font-bold text-xs rounded-xl shadow-sm transition duration-150 text-center active:scale-[0.98]">
                    Go to Dashboard &rarr;
                </a>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full border-t border-[#D7CCC8]/40 bg-[#FDFBF7] py-6 text-center text-xs text-[#8D6E63] font-semibold">
            <p>&copy; 2026 MediVault Secure Clinical Systems. All rights reserved.</p>
        </footer>

    </body>
</html>
