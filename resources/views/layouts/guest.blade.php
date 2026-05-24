<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body {
                font-family: 'Outfit', sans-serif !important;
            }
        </style>
    </head>
    <body class="text-[#3E2723] antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-[#FDFBF7] via-[#F5F2EB] to-[#EFEBE9]">
            <div class="mb-4">
                <a href="/" class="flex flex-col items-center gap-1 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-[#5D4037] to-[#3E2723] flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-8 h-8 text-[#FFF8E1]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.599-3.75A11.959 11.959 0 0112 2.714z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-tight bg-gradient-to-r from-[#3E2723] to-[#5D4037] bg-clip-text text-transparent">MediVault</span>
                    <span class="text-xs font-medium text-[#8D6E63] tracking-widest uppercase">Secure Records System</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-4 px-8 py-8 bg-white/80 backdrop-blur-md border border-[#D7CCC8]/60 shadow-[0_12px_40px_rgba(62,39,35,0.06)] overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
