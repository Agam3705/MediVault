<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ activeTab: '{{ $activeTab ?? (request()->query('search') ? 'search' : (request()->query('tab') ?? (auth()->check() && auth()->user()->role === 'Doctor' ? 'cases' : 'overview'))) }}', dark: localStorage.getItem('theme') === 'dark' }" :class="{ 'dark': dark }">
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
            /* Custom premium scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            ::-webkit-scrollbar-track {
                background: #FDFBF7;
            }
            ::-webkit-scrollbar-thumb {
                background: #D7CCC8;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #BCAAA4;
            }
        </style>
    </head>
    <body class="antialiased text-[#3E2723]">
        <div class="min-h-screen bg-gradient-to-br from-[#FDFBF7] via-[#F5F2EB] to-[#EFEBE9]">
            @include('layouts.navigation')

            <x-command-palette />

            <!-- Page Heading -->
            @isset($header)
                <header class="pt-8 pb-2">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="py-8 pb-24 sm:pb-8">
                {{ $slot }}
            </main>

            <!-- Mobile FAB (Floating Action Button) -->
            @auth
                @if(auth()->user()->role === 'Patient')
                    <a href="{{ route('records.create') }}" class="fixed bottom-20 right-4 z-40 sm:hidden w-14 h-14 rounded-full bg-[#3E2723] text-white flex items-center justify-center shadow-xl hover:scale-105 active:scale-95 transition duration-150">
                        <span class="text-xl">➕</span>
                    </a>
                @elseif(auth()->user()->role === 'Doctor')
                    <a href="{{ route('dashboard') }}" class="fixed bottom-20 right-4 z-40 sm:hidden w-14 h-14 rounded-full bg-[#3E2723] text-white flex items-center justify-center shadow-xl hover:scale-105 active:scale-95 transition duration-150">
                        <span class="text-xl">🔍</span>
                    </a>
                @endif
            @endauth

            <!-- Mobile-first App Bottom Navigation Bar -->
            @auth
                <div class="fixed bottom-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-t border-[#D7CCC8]/60 flex items-center justify-around z-40 sm:hidden shadow-[0_-4px_20px_rgba(62,39,35,0.04)] px-2">
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center text-[#5D4037] hover:text-[#3E2723] transition flex-1 py-1">
                        <span class="text-lg">🏠</span>
                        <span class="text-[9px] font-bold mt-0.5">Home</span>
                    </a>
                    
                    @if(auth()->user()->role === 'Patient')
                        <a href="{{ route('doctors.index') }}" class="flex flex-col items-center justify-center text-[#5D4037] hover:text-[#3E2723] transition flex-1 py-1">
                            <span class="text-lg">🩺</span>
                            <span class="text-[9px] font-bold mt-0.5">Doctors</span>
                        </a>
                        <a href="{{ route('recycle-bin.index') }}" class="flex flex-col items-center justify-center text-[#5D4037] hover:text-[#3E2723] transition flex-1 py-1">
                            <span class="text-lg">🗑️</span>
                            <span class="text-[9px] font-bold mt-0.5">Archive</span>
                        </a>
                    @endif

                    <a href="{{ route('notifications.index') }}" class="flex flex-col items-center justify-center text-[#5D4037] hover:text-[#3E2723] relative transition flex-1 py-1">
                        <span class="text-lg">🔔</span>
                        @php
                            $unreadNotifCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadNotifCount > 0)
                            <span class="absolute top-1 right-5 w-2 h-2 rounded-full bg-red-600"></span>
                        @endif
                        <span class="text-[9px] font-bold mt-0.5">Alerts</span>
                    </a>

                    <a href="{{ route('profile.sessions') }}" class="flex flex-col items-center justify-center text-[#5D4037] hover:text-[#3E2723] transition flex-1 py-1">
                        <span class="text-lg">🛡️</span>
                        <span class="text-[9px] font-bold mt-0.5">Security</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center text-[#5D4037] hover:text-[#3E2723] transition flex-1 py-1">
                        <span class="text-lg">👤</span>
                        <span class="text-[9px] font-bold mt-0.5">Profile</span>
                    </a>
                </div>
            @endauth
        </div>
    </body>
</html>
