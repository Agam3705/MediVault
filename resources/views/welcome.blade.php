<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MediVault | Secure Clinical Records Portal</title>
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
            .glow-effect {
                box-shadow: 0 0 40px rgba(93, 64, 55, 0.08);
            }
            .card-hover:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 24px -10px rgba(62, 39, 35, 0.15);
            }
        </style>
    </head>
    <body class="antialiased text-[#3E2723] min-h-screen flex flex-col justify-between selection:bg-[#5D4037] selection:text-[#FFF8E1]">
        
        <!-- Header / Navigation -->
        <header class="w-full max-w-7xl mx-auto px-6 py-5 flex items-center justify-between z-10">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#5D4037] flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-[#FFF8E1]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.599-3.75A11.959 11.959 0 0112 2.714z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold text-[#3E2723] tracking-tight">MediVault</h1>
                    <p class="text-[9px] font-bold text-[#8D6E63] uppercase tracking-wider -mt-1">Secure Health Records</p>
                </div>
            </div>
            
            <nav class="flex items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" 
                       class="px-5 py-2 bg-[#5D4037] text-[#FFF8E1] hover:bg-[#3E2723] font-bold text-xs rounded-xl shadow-sm transition duration-150 active:scale-[0.98]">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" 
                       class="px-4 py-2 text-[#5D4037] hover:text-[#3E2723] font-extrabold text-xs transition duration-150">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-5 py-2 bg-[#5D4037] text-[#FFF8E1] hover:bg-[#3E2723] font-bold text-xs rounded-xl shadow-sm transition duration-150 active:scale-[0.98]">
                        Register
                    </a>
                @endauth
            </nav>
        </header>

        <!-- Main Hero Section -->
        <main class="flex-grow flex items-center justify-center px-6 py-12 md:py-20 relative">
            <!-- Background Decorative Gradients -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-[#D7CCC8]/30 rounded-full blur-3xl -z-10"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-[#8D6E63]/10 rounded-full blur-3xl -z-10"></div>

            <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Left Content -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#D7CCC8]/40 border border-[#D7CCC8]/60 rounded-full">
                        <span class="w-2 h-2 rounded-full bg-[#5D4037] animate-pulse"></span>
                        <span class="text-[10px] font-extrabold text-[#5D4037] uppercase tracking-wider">HIPAA and GDPR Compliant Architecture</span>
                    </div>

                    <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-[#3E2723] leading-tight tracking-tight">
                        Your Clinical History.<br>
                        <span class="text-[#5D4037]">Fully Encrypted</span> & Under Your Control.
                    </h2>

                    <p class="text-sm md:text-base text-[#8D6E63] max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                        MediVault is a secure decentralized health record ecosystem. Maintain ownership of your documents, configure practitioner consent dynamically, and secure emergency situations with instant-scan medical QR cards.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" 
                               class="w-full sm:w-auto px-8 py-3.5 bg-[#5D4037] hover:bg-[#3E2723] text-[#FFF8E1] font-bold text-sm rounded-2xl shadow-lg hover:shadow-xl transition duration-150 text-center active:scale-[0.98]">
                                Access Dashboard &rarr;
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="w-full sm:w-auto px-8 py-3.5 bg-[#5D4037] hover:bg-[#3E2723] text-[#FFF8E1] font-bold text-sm rounded-2xl shadow-lg hover:shadow-xl transition duration-150 text-center active:scale-[0.98]">
                                Get Started Free &rarr;
                            </a>
                            <a href="{{ route('auth.google') }}" 
                               class="w-full sm:w-auto flex items-center justify-center gap-3 px-6 py-3.5 bg-[#FDFBF7] border border-[#D7CCC8] hover:bg-[#F5F2EB] text-[#5D4037] font-extrabold text-sm rounded-2xl transition duration-150 shadow-sm active:scale-[0.98]">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                <span>Continue with Google</span>
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Visual / Feature Highlight Cards -->
                <div class="lg:col-span-5 grid grid-cols-1 gap-6">
                    
                    <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-[#D7CCC8]/60 shadow-md card-hover transition duration-200 flex items-start gap-4">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-[#FFF8E1] border border-[#FFE082] flex items-center justify-center text-xl">
                            🔐
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-[#3E2723] uppercase tracking-wider">Granular Consent Control</h4>
                            <p class="text-xs text-[#8D6E63] mt-1 font-medium leading-relaxed">
                                Authorize specific doctors to view or edit specific records. Instantly revoke consent permissions or set timers for automatic access expiration.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-[#D7CCC8]/60 shadow-md card-hover transition duration-200 flex items-start gap-4">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-[#E8F5E9] border border-[#A5D6A7] flex items-center justify-center text-xl">
                            📟
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-[#3E2723] uppercase tracking-wider">Emergency QR Card</h4>
                            <p class="text-xs text-[#8D6E63] mt-1 font-medium leading-relaxed">
                                Generate an emergency card with critical information (blood group, allergies, medications). Emergency responders can access this in one tap by scanning your card.
                            </p>
                        </div>
                    </div>

                    <div class="bg-white/95 backdrop-blur-md p-6 rounded-3xl border border-[#D7CCC8]/60 shadow-md card-hover transition duration-200 flex items-start gap-4">
                        <div class="w-12 h-12 shrink-0 rounded-2xl bg-[#E1F5FE] border border-[#90CAF9] flex items-center justify-center text-xl">
                            📑
                        </div>
                        <div>
                            <h4 class="font-extrabold text-sm text-[#3E2723] uppercase tracking-wider">Audit Log Traceability</h4>
                            <p class="text-xs text-[#8D6E63] mt-1 font-medium leading-relaxed">
                                Every action—views, shares, file uploads, scans—is logged inside an immutable security audit database to prevent unauthorized access.
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full border-t border-[#D7CCC8]/40 bg-[#FDFBF7] py-6 text-center text-xs text-[#8D6E63] font-semibold">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
                <p>&copy; 2026 MediVault Secure Clinical Systems. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="#" class="hover:text-[#5D4037] transition">Privacy Policy</a>
                    <a href="#" class="hover:text-[#5D4037] transition">Terms of Service</a>
                    <a href="#" class="hover:text-[#5D4037] transition">Developer API</a>
                </div>
            </div>
        </footer>

    </body>
</html>
