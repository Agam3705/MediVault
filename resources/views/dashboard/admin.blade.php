<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                    <span x-show="activeTab === 'overview'">🛡️ {{ __('Administrator Control Panel') }}</span>
                    <span x-show="activeTab === 'verifications'">🏥 {{ __('Doctor Verification Queue') }}</span>
                    <span x-show="activeTab === 'doctors'">🩺 {{ __('Verified Practitioners Registry') }}</span>
                    <span x-show="activeTab === 'users'">👥 {{ __('Global Users Directory') }}</span>
                    <span x-show="activeTab === 'tickets'">🎫 {{ __('Support Tickets Resolution Queue') }}</span>
                    <span x-show="activeTab === 'audits'">📋 {{ __('Security Operations Audit Logs') }}</span>
                </h2>
                <p class="text-sm text-[#8D6E63] mt-1">
                    <span x-show="activeTab === 'overview'">{{ __('Manage users, verified medical practitioners, support requests, and security audit logs.') }}</span>
                    <span x-show="activeTab === 'verifications'">{{ __('Review doctor license submissions for verification.') }}</span>
                    <span x-show="activeTab === 'doctors'">{{ __('Registry of medical practitioners verified by administrators.') }}</span>
                    <span x-show="activeTab === 'users'">{{ __('Global list of users registered on the platform.') }}</span>
                    <span x-show="activeTab === 'tickets'">{{ __('Incoming user queries and support tickets.') }}</span>
                    <span x-show="activeTab === 'audits'">{{ __('Platform-wide security audit and operations logs.') }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-red-50 text-red-700 font-bold px-3 py-1.5 rounded-full border border-red-200 uppercase">
                    {{ __('Admin Access') }}
                </span>
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    {{ now()->format('M d, Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center justify-between text-red-800 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        <!-- 1. OVERVIEW TAB -->
        <div x-show="activeTab === 'overview'" class="flex flex-col gap-6" x-cloak>

            <!-- Stats Grid - 8 cards in 2 rows -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-[#5D4037] to-[#3E2723] rounded-2xl p-5 text-[#FFF8E1] shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">👥</div>
                    <div class="text-3xl font-extrabold">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-[10px] text-[#D7CCC8] font-bold mt-1 uppercase tracking-wider">{{ __('Total Users') }}</div>
                </div>
                <div class="bg-gradient-to-br from-[#795548] to-[#5D4037] rounded-2xl p-5 text-[#FFF8E1] shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">🧑‍⚕️</div>
                    <div class="text-3xl font-extrabold">{{ number_format($stats['total_patients']) }}</div>
                    <div class="text-[10px] text-[#D7CCC8] font-bold mt-1 uppercase tracking-wider">{{ __('Patients') }}</div>
                </div>
                <div class="bg-gradient-to-br from-green-700 to-green-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">✅</div>
                    <div class="text-3xl font-extrabold">{{ number_format($stats['verified_doctors']) }}</div>
                    <div class="text-[10px] text-green-200 font-bold mt-1 uppercase tracking-wider">{{ __('Verified Doctors') }}</div>
                </div>
                <div class="bg-gradient-to-br from-amber-600 to-amber-800 rounded-2xl p-5 text-white shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">⏳</div>
                    <div class="text-3xl font-extrabold">{{ number_format($stats['pending_doctors']) }}</div>
                    <div class="text-[10px] text-amber-200 font-bold mt-1 uppercase tracking-wider">{{ __('Awaiting Review') }}</div>
                    @if($stats['pending_doctors'] > 0)
                        <div class="mt-2 w-2 h-2 rounded-full bg-amber-300 animate-ping absolute top-3 right-3"></div>
                    @endif
                </div>
                <div class="bg-gradient-to-br from-[#4E342E] to-[#3E2723] rounded-2xl p-5 text-[#FFF8E1] shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">📂</div>
                    <div class="text-3xl font-extrabold">{{ number_format($stats['total_records']) }}</div>
                    <div class="text-[10px] text-[#D7CCC8] font-bold mt-1 uppercase tracking-wider">{{ __('Medical Records') }}</div>
                </div>
                <div class="bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">🔑</div>
                    <div class="text-3xl font-extrabold">{{ number_format($activeGrantsCount) }}</div>
                    <div class="text-[10px] text-blue-200 font-bold mt-1 uppercase tracking-wider">{{ __('Active Access Grants') }}</div>
                </div>
                <div class="bg-gradient-to-br from-purple-700 to-purple-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">🎫</div>
                    <div class="text-3xl font-extrabold">{{ number_format($pendingTicketsCount) }}</div>
                    <div class="text-[10px] text-purple-200 font-bold mt-1 uppercase tracking-wider">{{ __('Open Tickets') }}</div>
                    @if($pendingTicketsCount > 0)
                        <div class="mt-2 w-2 h-2 rounded-full bg-purple-300 animate-ping absolute top-3 right-3"></div>
                    @endif
                </div>
                <div class="bg-gradient-to-br from-red-700 to-red-900 rounded-2xl p-5 text-white shadow-md relative overflow-hidden group hover:shadow-xl transition-all duration-300">
                    <div class="absolute -right-4 -top-4 w-20 h-20 bg-white/5 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
                    <div class="text-2xl mb-1">⚠️</div>
                    <div class="text-3xl font-extrabold">{{ number_format($unusualActivityCount) }}</div>
                    <div class="text-[10px] text-red-200 font-bold mt-1 uppercase tracking-wider">{{ __('Unusual Activities') }}</div>
                </div>
            </div>

            <!-- Analytics Row -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                <!-- Doughnut Chart -->
                <div class="md:col-span-4 bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col items-center">
                    <h3 class="font-extrabold text-sm text-[#3E2723] self-start flex items-center gap-1.5 mb-4">
                        <span>📊</span> {{ __('User Base Distribution') }}
                    </h3>
                    <div class="w-full max-w-[200px] h-[200px]">
                        <canvas id="usersDistributionChart"></canvas>
                    </div>
                    <div class="mt-4 w-full grid grid-cols-3 gap-1 text-center">
                        <div class="text-[10px] font-bold text-[#8D6E63]">
                            <span class="block w-2 h-2 rounded-full bg-[#795548] mx-auto mb-1"></span>Patients
                        </div>
                        <div class="text-[10px] font-bold text-[#8D6E63]">
                            <span class="block w-2 h-2 rounded-full bg-green-700 mx-auto mb-1"></span>Doctors
                        </div>
                        <div class="text-[10px] font-bold text-[#8D6E63]">
                            <span class="block w-2 h-2 rounded-full bg-amber-600 mx-auto mb-1"></span>Pending
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="md:col-span-5 bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col">
                    <h3 class="font-extrabold text-sm text-[#3E2723] flex items-center gap-1.5 mb-4">
                        <span>📈</span> {{ __('Platform Activity Breakdown') }}
                    </h3>
                    <div class="w-full flex-grow h-[200px]">
                        <canvas id="auditActivitiesChart"></canvas>
                    </div>
                </div>

                <!-- Quick Actions + Kill Switch -->
                <div class="md:col-span-3 flex flex-col gap-3">
                    <!-- Quick Actions -->
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-4 shadow-sm flex flex-col gap-2">
                        <h4 class="font-extrabold text-xs text-[#3E2723] uppercase tracking-wider mb-1">⚡ {{ __('Quick Actions') }}</h4>
                        <button @click="activeTab = 'verifications'" class="w-full text-left px-3 py-2.5 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-800 text-xs font-bold rounded-xl transition flex items-center justify-between">
                            <span>⏳ {{ __('Review Verifications') }}</span>
                            <span class="bg-amber-200 text-amber-800 text-[9px] px-1.5 py-0.5 rounded-full">{{ $stats['pending_doctors'] }}</span>
                        </button>
                        <button @click="activeTab = 'tickets'" class="w-full text-left px-3 py-2.5 bg-purple-50 hover:bg-purple-100 border border-purple-200 text-purple-800 text-xs font-bold rounded-xl transition flex items-center justify-between">
                            <span>🎫 {{ __('Resolve Tickets') }}</span>
                            <span class="bg-purple-200 text-purple-800 text-[9px] px-1.5 py-0.5 rounded-full">{{ $pendingTicketsCount }}</span>
                        </button>
                        <button @click="activeTab = 'audits'" class="w-full text-left px-3 py-2.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-800 text-xs font-bold rounded-xl transition flex items-center justify-between">
                            <span>🔍 {{ __('Review Unusual Activity') }}</span>
                            <span class="bg-red-200 text-red-800 text-[9px] px-1.5 py-0.5 rounded-full">{{ $unusualActivityCount }}</span>
                        </button>
                    </div>

                    <!-- Kill Switch -->
                    <div class="bg-red-50 border border-red-200 rounded-3xl p-4 shadow-sm flex flex-col gap-3">
                        <div>
                            <h4 class="font-extrabold text-xs text-red-900 flex items-center gap-1.5 mb-1">
                                <span>🚨</span> {{ __('Security Kill-Switch') }}
                            </h4>
                            <p class="text-[10px] text-red-700 leading-relaxed">
                                {{ __('Immediately revoke ALL active access grants platform-wide.') }}
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.force-revoke') }}" onsubmit="return confirm('⚠️ CRITICAL: This will revoke ALL active grants immediately. Confirm?')">
                            @csrf
                            <button type="submit" class="w-full py-2.5 bg-red-700 hover:bg-red-800 text-white font-bold text-xs rounded-xl shadow-md transition active:scale-95 flex items-center justify-center gap-2">
                                💥 {{ __('Force Revoke All') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Platform Health + Recent Registrations -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Platform Health Panel -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                    <h3 class="font-extrabold text-sm text-[#3E2723] flex items-center gap-2 mb-5 border-b border-[#D7CCC8]/40 pb-3">
                        <span>💚</span> {{ __('Platform Health Monitor') }}
                    </h3>
                    <div class="flex flex-col gap-4">
                        @php
                            $verifiedPct = $stats['total_doctors'] > 0
                                ? round(($stats['verified_doctors'] / $stats['total_doctors']) * 100)
                                : 0;
                            $profilePct  = $stats['total_patients'] > 0
                                ? round((App\Models\Patient::whereNotNull('blood_group')->count() / $stats['total_patients']) * 100)
                                : 0;
                            $grantRatio  = $stats['total_patients'] > 0
                                ? round(($activeGrantsCount / max($stats['total_patients'], 1)) * 100)
                                : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-[#5D4037]">{{ __('Doctor Verification Rate') }}</span>
                                <span class="font-extrabold text-[#3E2723]">{{ $verifiedPct }}%</span>
                            </div>
                            <div class="w-full bg-[#EFEBE9] rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-600 to-green-800 h-2 rounded-full transition-all duration-700" style="width: {{ $verifiedPct }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-[#5D4037]">{{ __('Patient Profile Completion') }}</span>
                                <span class="font-extrabold text-[#3E2723]">{{ $profilePct }}%</span>
                            </div>
                            <div class="w-full bg-[#EFEBE9] rounded-full h-2">
                                <div class="bg-gradient-to-r from-[#5D4037] to-[#3E2723] h-2 rounded-full transition-all duration-700" style="width: {{ $profilePct }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-[#5D4037]">{{ __('Active Consent Coverage') }}</span>
                                <span class="font-extrabold text-[#3E2723]">{{ min($grantRatio, 100) }}%</span>
                            </div>
                            <div class="w-full bg-[#EFEBE9] rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-600 to-blue-800 h-2 rounded-full transition-all duration-700" style="width: {{ min($grantRatio, 100) }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-[#5D4037]">{{ __('Ticket Resolution Rate') }}</span>
                                @php
                                    $totalTickets = $pendingTickets->count() + $resolvedTickets->count();
                                    $resolutionPct = $totalTickets > 0 ? round(($resolvedTickets->count() / $totalTickets) * 100) : 100;
                                @endphp
                                <span class="font-extrabold text-[#3E2723]">{{ $resolutionPct }}%</span>
                            </div>
                            <div class="w-full bg-[#EFEBE9] rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-600 to-purple-800 h-2 rounded-full transition-all duration-700" style="width: {{ $resolutionPct }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Registrations -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                    <h3 class="font-extrabold text-sm text-[#3E2723] flex items-center gap-2 mb-5 border-b border-[#D7CCC8]/40 pb-3">
                        <span>🆕</span> {{ __('Recent User Registrations') }}
                    </h3>
                    @if($recentRegistrations->isEmpty())
                        <p class="text-xs text-[#8D6E63] py-6 text-center">{{ __('No users registered yet.') }}</p>
                    @else
                        <div class="flex flex-col gap-3">
                            @foreach($recentRegistrations as $reg)
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-[#EFEBE9] border border-[#D7CCC8]/60 flex items-center justify-center font-bold text-xs text-[#5D4037] shrink-0">
                                            {{ substr($reg->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold text-[#3E2723] leading-tight">{{ $reg->name }}</p>
                                            <p class="text-[9px] text-[#8D6E63]">{{ $reg->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full border uppercase
                                            {{ $reg->role === 'Doctor' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($reg->role === 'Admin' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-[#EFEBE9] text-[#5D4037] border-[#D7CCC8]/60') }}">
                                            {{ $reg->role }}
                                        </span>
                                        <p class="text-[9px] text-[#8D6E63] mt-0.5">{{ $reg->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 2. VERIFICATIONS TAB -->
        <div x-show="activeTab === 'verifications'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4" x-cloak>
            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-2 border-b border-[#D7CCC8]/40 pb-3">
                <span>🏥</span> {{ __('Doctor Verification Queue') }}
                @if($pendingDoctors->count() > 0)
                    <span class="ml-2 text-xs font-bold bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full border border-amber-200 animate-pulse">
                        {{ $pendingDoctors->count() }} {{ __('Pending') }}
                    </span>
                @endif
            </h3>

            @if($pendingDoctors->isEmpty())
                <div class="py-12 text-center text-sm text-[#8D6E63]">
                    <span class="text-3xl block mb-2">✅</span>
                    {{ __('All doctor license submissions have been reviewed. No pending verifications.') }}
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($pendingDoctors as $doc)
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 hover:border-amber-300 rounded-2xl p-5 transition duration-150 shadow-sm flex flex-col justify-between gap-4">
                            <div>
                                <div class="flex items-center justify-between border-b border-[#D7CCC8]/20 pb-2 mb-2">
                                    <h4 class="font-bold text-[#3E2723] text-sm">{{ $doc->user->name }}</h4>
                                    <span class="text-[9px] uppercase font-extrabold bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full">
                                        {{ __('Pending') }}
                                    </span>
                                </div>
                                <p class="text-xs text-[#8D6E63]">{{ $doc->user->email }}</p>
                                
                                <div class="mt-3 grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                                    <div>
                                        <span class="text-[#8D6E63] font-semibold">{{ __('License No:') }}</span>
                                        <span class="font-bold text-[#3E2723] ml-1 font-mono text-[10px] bg-[#EFEBE9] px-1.5 py-0.5 rounded">{{ $doc->license_number ?? 'Not provided' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-[#8D6E63] font-semibold">{{ __('Specialty:') }}</span>
                                        <span class="font-bold text-[#3E2723] ml-1">{{ $doc->specialization ?? 'Not provided' }}</span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-[#8D6E63] font-semibold">{{ __('Hospital:') }}</span>
                                        <span class="font-bold text-[#3E2723] ml-1">{{ $doc->hospital ?? 'Not provided' }}</span>
                                    </div>
                                    @if($doc->bio)
                                        <div class="col-span-2 mt-1">
                                            <span class="text-[#8D6E63] font-semibold">{{ __('Bio:') }}</span>
                                            <p class="text-[#5D4037] mt-0.5 line-clamp-2 italic">"{{ $doc->bio }}"</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2 justify-end border-t border-[#D7CCC8]/20 pt-3">
                                <!-- Reject -->
                                <form method="POST" action="{{ route('admin.doctors.reject', $doc->id) }}">
                                    @csrf
                                    <button type="submit" onclick="return confirm('Are you sure you want to reject this doctor application?')" 
                                            class="px-4 py-2 bg-white border border-red-200 hover:bg-red-50 text-red-700 text-xs font-bold rounded-xl shadow-sm transition active:scale-95">
                                        ❌ {{ __('Reject') }}
                                    </button>
                                </form>
                                <!-- Approve -->
                                <form method="POST" action="{{ route('admin.doctors.verify', $doc->id) }}">
                                    @csrf
                                    <button type="submit" class="px-5 py-2 bg-gradient-to-r from-green-700 to-green-900 hover:from-green-600 text-white text-xs font-bold rounded-xl shadow-sm transition active:scale-95">
                                        ✅ {{ __('Approve') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 3. APPROVED DOCTORS TAB -->
        <div x-show="activeTab === 'doctors'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4" x-cloak>
            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-2 border-b border-[#D7CCC8]/40 pb-3">
                <span>🩺</span> {{ __('Verified Practitioners Registry') }}
            </h3>

            @if($approvedDoctors->isEmpty())
                <div class="py-12 text-center text-sm text-[#8D6E63]">
                    <span class="text-3xl block mb-2">🩺</span>
                    {{ __('No verified doctors registered yet.') }}
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($approvedDoctors as $doc)
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 hover:border-[#5D4037]/20 rounded-2xl p-4 flex flex-col justify-between gap-4 shadow-sm">
                            <div>
                                <div class="flex items-center justify-between border-b border-[#D7CCC8]/20 pb-2 mb-2">
                                    <h4 class="font-bold text-[#3E2723] text-sm">{{ $doc->user->name }}</h4>
                                    <span class="text-[9px] uppercase font-bold bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full">
                                        {{ __('Verified') }}
                                    </span>
                                </div>
                                <p class="text-xs text-[#8D6E63]">{{ $doc->user->email }}</p>

                                <div class="mt-2 text-xs flex flex-col gap-1 text-[#5D4037]">
                                    <p><span class="font-semibold text-[#8D6E63]">{{ __('License:') }}</span> <code class="font-mono bg-[#EFEBE9] px-1 rounded text-[10px]">{{ $doc->license_number }}</code></p>
                                    <p><span class="font-semibold text-[#8D6E63]">{{ __('Specialty:') }}</span> {{ $doc->specialization }}</p>
                                    <p><span class="font-semibold text-[#8D6E63]">{{ __('Hospital:') }}</span> {{ $doc->hospital }}</p>
                                    @if($doc->verified_at)
                                        <p class="text-[10px] text-[#8D6E63] mt-2">{{ __('Approved:') }} {{ $doc->verified_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex justify-end pt-2 border-t border-[#D7CCC8]/20">
                                <form method="POST" action="{{ route('admin.doctors.revoke-verification', $doc->id) }}" onsubmit="return confirm('Are you sure you want to revoke this doctor\'s verification status? They will lose access to record search immediately.')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-bold rounded-xl transition active:scale-95">
                                        🚫 {{ __('Revoke Verification') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- 4. USERS DIRECTORY TAB -->
        <div x-show="activeTab === 'users'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4" x-cloak>
            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-2 border-b border-[#D7CCC8]/40 pb-3">
                <span>👥</span> {{ __('Global Users Directory') }}
            </h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-[#D7CCC8] text-[#8D6E63] uppercase font-bold tracking-wider">
                            <th class="py-3.5 px-4">{{ __('Name') }}</th>
                            <th class="py-3.5 px-4">{{ __('Email') }}</th>
                            <th class="py-3.5 px-4">{{ __('Role') }}</th>
                            <th class="py-3.5 px-4">{{ __('Status') }}</th>
                            <th class="py-3.5 px-4 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#D7CCC8]/30">
                        @foreach($allUsers as $u)
                            <tr class="hover:bg-[#FDFBF7]/50 transition-colors">
                                <td class="py-3 px-4 font-bold text-[#3E2723]">{{ $u->name }}</td>
                                <td class="py-3 px-4 text-[#5D4037] font-medium">{{ $u->email }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[9px] uppercase border
                                        @if($u->role === 'Admin') bg-red-50 text-red-700 border-red-200
                                        @elseif($u->role === 'Doctor') bg-blue-50 text-blue-700 border-blue-200
                                        @else bg-green-50 text-green-700 border-green-200 @endif">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded font-bold text-[9px] uppercase
                                        {{ $u->is_active !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $u->is_active !== false ? __('Active') : __('Suspended') }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right flex justify-end gap-2">
                                    @if(auth()->id() !== $u->id)
                                        <!-- Suspend / Unsuspend -->
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $u->id) }}">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 border rounded-lg text-[10px] font-bold shadow-xs transition active:scale-95
                                                {{ $u->is_active !== false ? 'bg-amber-50 border-amber-200 hover:bg-amber-100 text-amber-700' : 'bg-green-50 border-green-200 hover:bg-green-100 text-green-700' }}">
                                                {{ $u->is_active !== false ? __('Suspend') : __('Unsuspend') }}
                                            </button>
                                        </form>

                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.users.delete', $u->id) }}" onsubmit="return confirm('⚠️ WARNING: This will permanently delete user {{ $u->name }} and all their related vaults/clinical files. This action is irreversible. Proceed?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 bg-red-700 hover:bg-red-800 text-white rounded-lg text-[10px] font-bold shadow-xs transition active:scale-95">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-[#8D6E63] font-semibold italic">{{ __('Logged In Admin') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. SUPPORT TICKETS TAB -->
        <div x-show="activeTab === 'tickets'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4" x-cloak>
            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-2 border-b border-[#D7CCC8]/40 pb-3">
                <span>🎫</span> {{ __('Support Tickets Resolution Queue') }}
                @if($pendingTickets->count() > 0)
                    <span class="ml-2 text-xs font-bold bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full border border-amber-200">
                        {{ $pendingTickets->count() }} {{ __('Pending') }}
                    </span>
                @endif
            </h3>

            @if($pendingTickets->isEmpty())
                <div class="py-6 text-center text-sm text-[#8D6E63]">
                    <span class="text-2xl block mb-1">📥</span>
                    {{ __('No pending support tickets.') }}
                </div>
            @else
                <div class="flex flex-col gap-4 max-h-[350px] overflow-y-auto pr-1">
                    @foreach($pendingTickets as $ticket)
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 rounded-2xl p-4 shadow-xs">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-xs text-[#3E2723]">{{ $ticket->user->name }}</span>
                                    <span class="text-[9px] font-extrabold px-2 py-0.5 rounded bg-[#EFEBE9] text-[#5D4037] uppercase tracking-wider">{{ $ticket->user->role }}</span>
                                </div>
                                <span class="text-[9px] text-[#8D6E63] font-semibold">{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                            <h4 class="font-bold text-xs text-[#5D4037] mb-1">{{ __('Subject:') }} {{ $ticket->subject }}</h4>
                            <p class="text-xs text-[#3E2723] leading-relaxed mb-4 whitespace-pre-wrap">{{ $ticket->message }}</p>
                            
                            <!-- Admin reply form -->
                            <form method="POST" action="{{ route('support.reply', $ticket->id) }}" class="border-t border-[#D7CCC8]/30 pt-3 flex flex-col gap-2">
                                @csrf
                                <textarea name="reply" rows="2" placeholder="Write administrative reply & resolve ticket..." required
                                          class="w-full px-3 py-2 bg-white border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-xs text-[#3E2723] transition"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="px-4 py-1.5 bg-[#3E2723] hover:bg-[#5D4037] text-white text-[10px] font-bold rounded-lg transition active:scale-95 shadow-sm uppercase tracking-wider">
                                        {{ __('Resolve & Send Reply') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Resolved Tickets Collapsible History -->
            @if(!$resolvedTickets->isEmpty())
                <div x-data="{ openHistory: false }" class="border-t border-[#D7CCC8]/30 pt-3">
                    <button @click="openHistory = !openHistory" class="text-xs font-bold text-[#8D6E63] hover:text-[#3E2723] flex items-center gap-1.5 transition-colors">
                        <span>📜</span> {{ __('View Resolved Ticket History') }} (<span x-text="openHistory ? 'Hide' : 'Show'"></span>)
                    </button>
                    
                    <div x-show="openHistory" class="flex flex-col gap-3 mt-3 max-h-[300px] overflow-y-auto pr-1">
                        @foreach($resolvedTickets as $ticket)
                            <div class="bg-[#FDFBF7]/40 border border-[#D7CCC8]/30 rounded-xl p-3 text-xs">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-[#3E2723]">{{ $ticket->subject }} ({{ $ticket->user->name }})</span>
                                    <span class="text-[9px] text-[#8D6E63]">{{ $ticket->replied_at ? $ticket->replied_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-[#8D6E63] italic line-clamp-1 mb-2">Q: "{{ $ticket->message }}"</p>
                                <div class="p-2.5 bg-white border border-[#D7CCC8]/40 rounded-lg text-[#3E2723]">
                                    <p class="text-[9px] uppercase font-extrabold text-green-700 mb-0.5">Resolved Response:</p>
                                    <p class="leading-relaxed whitespace-pre-wrap">{{ $ticket->reply }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- 6. AUDIT LOGS TAB -->
        <div x-show="activeTab === 'audits'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4" x-cloak>
            <div class="flex items-center justify-between border-b border-[#D7CCC8]/40 pb-3 mb-2">
                <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2">
                    <span>📋</span> {{ __('Security Operations Audit Logs') }}
                    <span class="text-xs font-bold bg-[#EFEBE9] text-[#5D4037] px-2 py-0.5 rounded-full border border-[#D7CCC8]/60">{{ $recentLogs->count() }} {{ __('entries') }}</span>
                </h3>
                @if($auditSearch || $auditAction || $auditRole || $auditUnusual)
                    <a href="{{ route('dashboard', ['tab' => 'audits']) }}" class="text-xs text-[#5D4037] hover:text-[#3E2723] font-bold flex items-center gap-1 border border-[#D7CCC8]/60 px-3 py-1.5 rounded-xl hover:bg-[#FDFBF7] transition">
                        ✕ {{ __('Clear Filters') }}
                    </a>
                @endif
            </div>

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <input type="hidden" name="tab" value="audits">

                <div class="relative">
                    <input type="text" name="audit_search" value="{{ $auditSearch ?? '' }}"
                           placeholder="{{ __('Search by user, action, or IP...') }}"
                           class="w-full pl-9 pr-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition">
                    <span class="absolute left-3 top-3 text-[#8D6E63] text-xs">🔍</span>
                </div>

                <select name="audit_action" class="w-full px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition">
                    <option value="">{{ __('All Actions') }}</option>
                    @foreach($allAuditActions as $action)
                        <option value="{{ $action }}" @selected($auditAction === $action)>{{ $action }}</option>
                    @endforeach
                </select>

                <select name="audit_role" class="w-full px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition">
                    <option value="">{{ __('All User Roles') }}</option>
                    <option value="Patient" @selected($auditRole === 'Patient')>{{ __('Patients') }}</option>
                    <option value="Doctor" @selected($auditRole === 'Doctor')>{{ __('Doctors') }}</option>
                    <option value="Admin" @selected($auditRole === 'Admin')>{{ __('Admins') }}</option>
                </select>

                <div class="flex gap-2">
                    <label class="flex items-center gap-2 flex-grow px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] rounded-xl cursor-pointer hover:bg-red-50 hover:border-red-200 transition">
                        <input type="checkbox" name="audit_unusual" value="1" @checked($auditUnusual === '1') class="rounded border-[#D7CCC8] text-red-600">
                        <span class="text-xs font-bold text-red-700">⚠️ {{ __('Unusual Only') }}</span>
                    </label>
                    <button type="submit" class="px-4 py-2.5 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold text-xs rounded-xl transition shadow-sm active:scale-95 shrink-0">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>

            <!-- Active Filter Tags -->
            @if($auditSearch || $auditAction || $auditRole || $auditUnusual)
                <div class="flex flex-wrap gap-2">
                    @if($auditSearch)
                        <span class="text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">
                            🔍 "{{ $auditSearch }}"
                        </span>
                    @endif
                    @if($auditAction)
                        <span class="text-[10px] font-bold bg-[#EFEBE9] text-[#5D4037] border border-[#D7CCC8]/60 px-2.5 py-1 rounded-full">
                            ⚙️ {{ $auditAction }}
                        </span>
                    @endif
                    @if($auditRole)
                        <span class="text-[10px] font-bold bg-[#EFEBE9] text-[#5D4037] border border-[#D7CCC8]/60 px-2.5 py-1 rounded-full">
                            👤 {{ $auditRole }}
                        </span>
                    @endif
                    @if($auditUnusual === '1')
                        <span class="text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full">
                            ⚠️ Unusual Only
                        </span>
                    @endif
                </div>
            @endif

            <!-- Logs Table -->
            @if($recentLogs->isEmpty())
                <div class="py-12 text-center text-sm text-[#8D6E63]">
                    <span class="text-3xl block mb-2">📭</span>
                    {{ __('No audit log entries match your filters.') }}
                </div>
            @else
                <div class="overflow-x-auto rounded-2xl border border-[#D7CCC8]/40">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#EFEBE9]/60 border-b border-[#D7CCC8] text-[#8D6E63] uppercase font-bold tracking-wider">
                                <th class="py-3 px-4">{{ __('Action') }}</th>
                                <th class="py-3 px-4">{{ __('User') }}</th>
                                <th class="py-3 px-4">{{ __('Target') }}</th>
                                <th class="py-3 px-4">{{ __('IP Address') }}</th>
                                <th class="py-3 px-4">{{ __('Time') }}</th>
                                <th class="py-3 px-4">{{ __('Flag') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#D7CCC8]/30">
                            @foreach($recentLogs as $log)
                                <tr class="hover:bg-[#FDFBF7] transition-colors duration-100 {{ $log->unusual_activity ? 'bg-red-50/40' : '' }}">
                                    <td class="py-3 px-4">
                                        <span class="font-bold text-[#3E2723]">{{ $log->action }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($log->user)
                                            <div>
                                                <p class="font-semibold text-[#3E2723]">{{ $log->user->name }}</p>
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded border uppercase
                                                    {{ $log->user->role === 'Doctor' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($log->user->role === 'Admin' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-[#EFEBE9] text-[#5D4037] border-[#D7CCC8]/40') }}">
                                                    {{ $log->user->role }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-[#8D6E63] italic">{{ __('System') }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($log->target_type)
                                            <span class="font-mono text-[9px] bg-[#EFEBE9] text-[#5D4037] px-1.5 py-0.5 rounded">
                                                {{ $log->target_type }}
                                            </span>
                                        @else
                                            <span class="text-[#8D6E63]">—</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="font-mono text-[10px] text-[#8D6E63]">{{ $log->ip_address ?? '—' }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-[10px] text-[#8D6E63]">{{ $log->created_at->format('M d, H:i') }}</span>
                                        <p class="text-[9px] text-[#8D6E63]/70">{{ $log->created_at->diffForHumans() }}</p>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($log->unusual_activity)
                                            <span class="text-[9px] font-bold bg-red-100 text-red-700 border border-red-200 px-2 py-0.5 rounded uppercase animate-pulse">
                                                ⚠️ Unusual
                                            </span>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    @php
        $actionCounts = [];
        foreach($recentLogs as $log) {
            $actionCounts[$log->action] = ($actionCounts[$log->action] ?? 0) + 1;
        }
        arsort($actionCounts);
        $actionCounts = array_slice($actionCounts, 0, 5, true);
        $chartLabels = array_keys($actionCounts);
        $chartData = array_values($actionCounts);
    @endphp

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Users Doughnut Chart
            const ctxUsers = document.getElementById('usersDistributionChart').getContext('2d');
            new Chart(ctxUsers, {
                type: 'doughnut',
                data: {
                    labels: ['Patients', 'Verified Doctors', 'Pending Doctors'],
                    datasets: [{
                        data: [
                            {{ $stats['total_patients'] }},
                            {{ $stats['verified_doctors'] }},
                            {{ $stats['pending_doctors'] }}
                        ],
                        backgroundColor: ['#795548', '#2E7D32', '#EF6C00'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#3E2723',
                                font: {
                                    family: 'Outfit',
                                    weight: 'bold',
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Audit Traffic Bar Chart
            const ctxAudit = document.getElementById('auditActivitiesChart').getContext('2d');
            new Chart(ctxAudit, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Trigger Frequency',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: '#5D4037',
                        borderRadius: 8,
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: '#8D6E63',
                                font: {
                                    family: 'Outfit',
                                    size: 9
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#8D6E63',
                                stepSize: 1,
                                font: {
                                    family: 'Outfit',
                                    size: 10
                                }
                            },
                            grid: {
                                color: '#EFEBE9'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
