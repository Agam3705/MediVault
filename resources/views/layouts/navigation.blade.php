<nav x-data="{ open: false }" class="bg-white/70 backdrop-blur-md border-b border-[#D7CCC8]/60 shadow-[0_2px_15px_rgba(62,39,35,0.02)] sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-[#5D4037] to-[#3E2723] flex items-center justify-center shadow-md group-hover:scale-105 transition-transform duration-300">
                            <svg class="w-5 h-5 text-[#FFF8E1]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.599-3.75A11.959 11.959 0 0112 2.714z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight bg-gradient-to-r from-[#3E2723] to-[#5D4037] bg-clip-text text-transparent">MediVault</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    @auth
                        @if(auth()->user()->role === 'Patient')
                            @if(request()->routeIs('dashboard'))
                                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🏠 {{ __('Overview') }}
                                </button>
                                <button @click="activeTab = 'records'" :class="activeTab === 'records' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    📂 {{ __('Medical Vault') }}
                                </button>
                                <button @click="activeTab = 'consent'" :class="activeTab === 'consent' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🔒 {{ __('Consent') }}
                                </button>
                                <button @click="activeTab = 'audits'" :class="activeTab === 'audits' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🕵️‍♂️ {{ __('Audits') }}
                                </button>
                                <button @click="activeTab = 'chat'" :class="activeTab === 'chat' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    💬 {{ __('Chat') }}
                                </button>
                                <a href="{{ route('doctors.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🩺 {{ __('Doctors') }}
                                </a>
                            @else
                                <a href="{{ route('dashboard', ['tab' => 'overview']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🏠 {{ __('Overview') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'records']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    📂 {{ __('Medical Vault') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'consent']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🔒 {{ __('Consent') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'audits']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🕵️‍♂️ {{ __('Audits') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'chat']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    💬 {{ __('Chat') }}
                                </a>
                                <a href="{{ route('doctors.index') }}" :class="request()->routeIs('doctors.index') ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out">
                                    🩺 {{ __('Doctors') }}
                                </a>
                            @endif
                        @elseif(auth()->user()->role === 'Doctor')
                            @if(request()->routeIs('dashboard'))
                                <button @click="activeTab = 'cases'" :class="activeTab === 'cases' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    📂 {{ __('Active Cases') }}
                                </button>
                                <button @click="activeTab = 'search'" :class="activeTab === 'search' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🔍 {{ __('Patient Search') }}
                                </button>
                                <button @click="activeTab = 'requests'" :class="activeTab === 'requests' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    📡 {{ __('Requests Log') }}
                                </button>
                                <button @click="activeTab = 'chat'" :class="activeTab === 'chat' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    💬 {{ __('Clinical Chats') }}
                                </button>
                                <button @click="activeTab = 'audits'" :class="activeTab === 'audits' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🕵️‍♂️ {{ __('Audit Logs') }}
                                </button>
                                <a href="{{ route('doctors.my-patients') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🩺 {{ __('My Patients') }}
                                </a>
                            @else
                                <a href="{{ route('dashboard', ['tab' => 'cases']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    📂 {{ __('Active Cases') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'search']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🔍 {{ __('Patient Search') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'requests']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    📡 {{ __('Requests Log') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'chat']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    💬 {{ __('Clinical Chats') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'audits']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🕵️‍♂️ {{ __('Audit Logs') }}
                                </a>
                                <a href="{{ route('doctors.my-patients') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🩺 {{ __('My Patients') }}
                                </a>
                            @endif
                        @elseif(auth()->user()->role === 'Admin')
                            @if(request()->routeIs('dashboard'))
                                <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    📊 {{ __('Overview') }}
                                </button>
                                <button @click="activeTab = 'verifications'" :class="activeTab === 'verifications' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    ⏳ {{ __('Verifications') }}
                                </button>
                                <button @click="activeTab = 'doctors'" :class="activeTab === 'doctors' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🩺 {{ __('Approved Doctors') }}
                                </button>
                                <button @click="activeTab = 'users'" :class="activeTab === 'users' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    👥 {{ __('Users Directory') }}
                                </button>
                                <button @click="activeTab = 'tickets'" :class="activeTab === 'tickets' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🎫 {{ __('Support Tickets') }}
                                </button>
                                <button @click="activeTab = 'audits'" :class="activeTab === 'audits' ? 'border-[#5D4037] text-[#3E2723]' : 'border-transparent text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8]/40'" class="inline-flex items-center px-1 pt-1 border-b-2 text-xs font-bold leading-5 transition duration-150 ease-in-out cursor-pointer">
                                    🕵️‍♂️ {{ __('Audit Logs') }}
                                </button>
                            @else
                                <a href="{{ route('dashboard', ['tab' => 'overview']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    📊 {{ __('Overview') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'verifications']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    ⏳ {{ __('Verifications') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'doctors']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🩺 {{ __('Approved Doctors') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'users']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    👥 {{ __('Users Directory') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'tickets']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🎫 {{ __('Support Tickets') }}
                                </a>
                                <a href="{{ route('dashboard', ['tab' => 'audits']) }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-xs font-bold leading-5 text-[#8D6E63] hover:text-[#5D4037] transition duration-150 ease-in-out">
                                    🕵️‍♂️ {{ __('Audit Logs') }}
                                </a>
                            @endif
                        @endif
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">

                @php
                    $notifCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
                    $recentNotifs = \App\Models\Notification::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            class="p-2 bg-[#FDFBF7] hover:bg-[#F5F2EB] border border-[#D7CCC8]/60 text-[#5D4037] hover:text-[#3E2723] rounded-xl relative shadow-sm transition active:scale-95">
                        <span class="text-sm">🔔</span>
                        @if($notifCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[8px] font-bold px-1.5 py-0.5 rounded-full flex items-center justify-center border border-white min-w-[16px] h-[16px]">
                                {{ $notifCount }}
                            </span>
                        @endif
                    </button>

                    <div x-show="open" 
                         @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-72 bg-white/95 backdrop-blur-md border border-[#D7CCC8]/60 rounded-2xl shadow-lg py-3 z-50" 
                         style="display: none;">
                        <div class="px-4 pb-2 border-b border-[#D7CCC8]/40 flex items-center justify-between">
                            <span class="text-xs font-extrabold text-[#3E2723]">{{ __('Notifications') }}</span>
                            @if($notifCount > 0)
                                <form method="POST" action="{{ route('notifications.read-all') }}">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-[#8D6E63] hover:text-[#3E2723] font-bold underline">{{ __('Mark all read') }}</button>
                                </form>
                            @endif
                        </div>
                        <div class="max-h-60 overflow-y-auto mt-2">
                            @forelse($recentNotifs as $notif)
                                <div class="px-4 py-2 hover:bg-[#FDFBF7] transition-colors duration-100 flex items-start gap-2.5 border-b border-[#D7CCC8]/10 last:border-0">
                                    <span class="text-sm shrink-0">
                                        @if(str_contains(strtolower($notif->message), 'access')) 🔐 
                                        @elseif(str_contains(strtolower($notif->message), 'record')) 📄 
                                        @elseif(str_contains(strtolower($notif->message), 'session')) 💻 
                                        @else 🔔 @endif
                                    </span>
                                    <div class="flex-grow">
                                        <p class="text-[11px] font-semibold text-[#3E2723] leading-snug">{{ $notif->message }}</p>
                                        <span class="text-[9px] text-[#8D6E63]">{{ $notif->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if(is_null($notif->read_at))
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 shrink-0 mt-1"></span>
                                    @endif
                                </div>
                            @empty
                                <div class="py-6 text-center text-xs text-[#8D6E63]">{{ __('No notifications yet.') }}</div>
                            @endforelse
                        </div>
                        <div class="px-4 pt-2 border-t border-[#D7CCC8]/40 text-center">
                            <a href="{{ route('notifications.index') }}" class="text-[10px] font-bold text-[#5D4037] hover:text-[#3E2723] tracking-wide uppercase">{{ __('View All Notifications') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Language Toggle -->
                <div class="flex items-center gap-1 border border-[#D7CCC8]/60 rounded-xl p-1 bg-[#FDFBF7] shadow-sm shrink-0">
                    <a href="{{ route('lang.switch', 'en') }}" class="px-2 py-1 text-[10px] font-bold rounded-lg transition-all {{ app()->getLocale() === 'en' ? 'bg-[#5D4037] text-[#FFF8E1] shadow-xs' : 'text-[#8D6E63] hover:text-[#5D4037]' }}">
                        EN
                    </a>
                    <a href="{{ route('lang.switch', 'hi') }}" class="px-2 py-1 text-[10px] font-bold rounded-lg transition-all {{ app()->getLocale() === 'hi' ? 'bg-[#5D4037] text-[#FFF8E1] shadow-xs' : 'text-[#8D6E63] hover:text-[#5D4037]' }}">
                        HI
                    </a>
                </div>

                <!-- Dark Mode Toggle -->
                <button @click="dark = !dark; localStorage.setItem('theme', dark ? 'dark' : 'light')" 
                        class="p-2 bg-[#FDFBF7] hover:bg-[#F5F2EB] border border-[#D7CCC8]/60 text-[#5D4037] hover:text-[#3E2723] rounded-xl shadow-sm transition active:scale-95 flex items-center justify-center shrink-0 w-8 h-8" title="{{ __('Toggle Dark Mode') }}">
                    <span x-show="!dark">☀️</span>
                    <span x-show="dark">🌙</span>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3.5 py-2 border border-[#D7CCC8]/60 text-sm leading-4 font-semibold rounded-xl text-[#5D4037] bg-[#FDFBF7] hover:bg-[#F5F2EB] hover:text-[#3E2723] focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                <div>{{ Auth::user()->name }}</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('support.create')">
                            {{ __('Contact Support') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('support.create')">
                    {{ __('Contact Support') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
