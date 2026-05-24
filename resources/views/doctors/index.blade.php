<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                    <span>🩺</span> {{ __('Doctor Directory') }}
                </h2>
                <p class="text-sm text-[#8D6E63] mt-1">{{ __('Search verified medical practitioners, initiate private chats, and manage records access.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    {{ __('Patient Access Center') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Flash Alert Toasts -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800 shadow-sm">
                <ul class="list-disc pl-5 text-xs font-semibold flex flex-col gap-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Doctor Directory & Access Manager Search Card -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4">
            <div>
                <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2">
                    <span>🔍</span> {{ __('Search Practitioner Database') }}
                </h3>
                <p class="text-xs text-[#8D6E63] mt-0.5 font-medium">{{ __('Query doctors by name, specialty, or hospital affiliation to consult or grant access.') }}</p>
            </div>

            <form method="GET" action="{{ route('doctors.index') }}" class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-grow">
                    <input type="text" name="doctor_search" value="{{ $doctorSearch ?? '' }}" placeholder="Search doctors by name, specialization, or hospital..." 
                           class="w-full pl-10 pr-4 py-3 border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-xs font-semibold text-[#3E2723] rounded-2xl shadow-sm transition">
                    <span class="absolute left-3.5 top-3.5 text-gray-400">🔍</span>
                </div>
                <button type="submit" class="px-5 py-3 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold text-xs rounded-2xl transition shadow-md active:scale-95">
                    {{ __('Search Doctors') }}
                </button>
                @if($doctorSearch)
                    <a href="{{ route('doctors.index') }}" class="px-4 py-3 bg-white border border-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-2xl text-center flex items-center justify-center">
                        {{ __('Clear') }}
                    </a>
                @endif
            </form>

            @if($doctorSearch)
                <div class="border-t border-[#D7CCC8]/40 pt-4">
                    <h4 class="text-xs uppercase font-extrabold text-[#8D6E63] mb-3">{{ __('Search Results') }} ({{ count($doctorResults) }})</h4>
                    @if(count($doctorResults) === 0)
                        <p class="text-xs text-[#8D6E63]">{{ __('No verified practitioners match your query.') }}</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($doctorResults as $doc)
                                @php
                                    $activeGrant = $activeGrants->where('doctor_id', $doc->id)->first();
                                    $pendingReq = $pendingRequestsList->where('doctor_id', $doc->id)->first();
                                @endphp
                                <div class="bg-[#FDFBF7]/60 border border-[#D7CCC8]/50 hover:border-[#D7CCC8] rounded-2xl p-4 flex flex-col justify-between gap-4 shadow-sm transition duration-150">
                                    <div>
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-bold text-[#3E2723] text-sm">{{ $doc->user->name }}</h5>
                                                <p class="text-xs text-[#8D6E63]">{{ $doc->specialization }} &bull; {{ $doc->hospital }}</p>
                                            </div>
                                            @if($activeGrant)
                                                <span class="text-[9px] uppercase font-bold bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded">
                                                    {{ __('Access Granted') }}
                                                </span>
                                            @elseif($pendingReq)
                                                <span class="text-[9px] uppercase font-bold bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded">
                                                    {{ __('Pending Request') }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($doc->bio)
                                            <p class="text-[11px] text-[#5D4037] mt-2 italic leading-relaxed">"{{ $doc->bio }}"</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0 justify-end">
                                        <a href="{{ route('chat.show', $doc->user->id) }}" class="px-4 py-2 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-xl transition shadow-sm active:scale-95 flex items-center gap-1.5">
                                            <span>💬</span> {{ __('Chat') }}
                                        </a>

                                        @if($activeGrant)
                                            <form method="POST" action="{{ route('consent.revoke', $activeGrant->id) }}" onsubmit="return confirm('WARNING: Are you sure you want to revoke this doctor\'s access immediately?')">
                                                @csrf
                                                <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                                                    {{ __('Revoke Access') }}
                                                </button>
                                            </form>
                                        @else
                                            <div x-data="{ open: false }" class="relative">
                                                <button @click="open = !open" class="px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                                                    🔒 {{ __('Grant Access') }}
                                                </button>
                                                
                                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 p-4 bg-white border border-[#D7CCC8] shadow-xl rounded-2xl w-64 z-20 text-left">
                                                    <h6 class="font-bold text-[#3E2723] text-xs mb-2">{{ __('Select Access Expiry') }}</h6>
                                                    <form method="POST" action="{{ route('consent.grant-direct') }}">
                                                        @csrf
                                                        <input type="hidden" name="doctor_id" value="{{ $doc->id }}">
                                                        <div class="mb-3">
                                                            <select name="duration" class="w-full p-2 border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-xs text-[#3E2723] rounded-xl">
                                                                <option value="1">1 Day</option>
                                                                <option value="7">7 Days</option>
                                                                <option value="30" selected>30 Days</option>
                                                                <option value="365">1 Year (Unlimited)</option>
                                                            </select>
                                                        </div>
                                                        <div class="flex gap-2 justify-end">
                                                            <button type="button" @click="open = false" class="px-2.5 py-1.5 border border-[#D7CCC8] text-[#5D4037] text-[10px] font-bold rounded-lg hover:bg-gray-50">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="px-3 py-1.5 bg-[#3E2723] text-white text-[10px] font-bold rounded-lg hover:bg-[#5D4037] transition">{{ __('Grant') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- B. Doctors in Contact Section -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4 mt-8">
            <div>
                <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2">
                    <span>👥</span> {{ __('My Medical Connections (Doctors in Contact)') }}
                </h3>
                <p class="text-xs text-[#8D6E63] mt-0.5 font-medium">{{ __('All practitioners you have authorized, received requests from, or conversed with.') }}</p>
            </div>

            @if($contactDoctors->isEmpty())
                <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-8 text-center text-sm text-[#8D6E63]">
                    {{ __('No clinical contacts found. Search verified doctors above to start a connection.') }}
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($contactDoctors as $doc)
                        @php
                            $activeGrant = $activeGrants->where('doctor_id', $doc->id)->first();
                            $pendingReq = $pendingRequestsList->where('doctor_id', $doc->id)->first();
                        @endphp
                        <div class="bg-white border border-[#D7CCC8]/50 hover:border-[#D7CCC8] rounded-2xl p-4 flex flex-col justify-between gap-4 shadow-sm transition duration-150">
                            <div>
                                <h5 class="font-bold text-[#3E2723] text-sm">{{ $doc->user->name }}</h5>
                                <p class="text-xs text-[#8D6E63]">{{ $doc->specialization }} &bull; {{ $doc->hospital }}</p>
                                
                                <div class="flex items-center gap-1.5 mt-3">
                                    @if($activeGrant)
                                        <span class="inline-block text-[9px] font-extrabold px-2 py-0.5 rounded border bg-green-50 text-green-700 border-green-200 uppercase">
                                            {{ __('Authorized') }}
                                        </span>
                                        <span class="text-[9px] text-[#8D6E63]">
                                            {{ __('Expires:') }} {{ $activeGrant->expires_at ? $activeGrant->expires_at->diffForHumans() : __('Never') }}
                                        </span>
                                    @elseif($pendingReq)
                                        <span class="inline-block text-[9px] font-extrabold px-2 py-0.5 rounded border bg-amber-50 text-amber-700 border-amber-200 uppercase">
                                            {{ __('Pending Request') }}
                                        </span>
                                    @else
                                        <span class="inline-block text-[9px] font-extrabold px-2 py-0.5 rounded border bg-[#EFEBE9] text-[#5D4037] border-[#D7CCC8]/60 uppercase">
                                            {{ __('Chat Connection Only') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 justify-end">
                                <a href="{{ route('chat.show', $doc->user->id) }}" class="px-4 py-2 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-xl transition shadow-sm active:scale-95 flex items-center gap-1">
                                    <span>💬</span> {{ __('Chat') }}
                                </a>

                                @if($activeGrant)
                                    <form method="POST" action="{{ route('consent.revoke', $activeGrant->id) }}" onsubmit="return confirm('WARNING: Are you sure you want to revoke this doctor\'s access immediately?')">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                                            {{ __('Revoke') }}
                                        </button>
                                    </form>
                                @else
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                                            🔒 {{ __('Grant Access') }}
                                        </button>
                                        
                                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 p-4 bg-white border border-[#D7CCC8] shadow-xl rounded-2xl w-64 z-20 text-left">
                                            <h6 class="font-bold text-[#3E2723] text-xs mb-2">{{ __('Select Access Expiry') }}</h6>
                                            <form method="POST" action="{{ route('consent.grant-direct') }}">
                                                @csrf
                                                <input type="hidden" name="doctor_id" value="{{ $doc->id }}">
                                                <div class="mb-3">
                                                    <select name="duration" class="w-full p-2 border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-xs text-[#3E2723] rounded-xl">
                                                        <option value="1">1 Day</option>
                                                        <option value="7">7 Days</option>
                                                        <option value="30" selected>30 Days</option>
                                                        <option value="365">1 Year (Unlimited)</option>
                                                    </select>
                                                </div>
                                                <div class="flex gap-2 justify-end">
                                                    <button type="button" @click="open = false" class="px-2.5 py-1.5 border border-[#D7CCC8] text-[#5D4037] text-[10px] font-bold rounded-lg hover:bg-gray-50">{{ __('Cancel') }}</button>
                                                    <button type="submit" class="px-3 py-1.5 bg-[#3E2723] text-white text-[10px] font-bold rounded-lg hover:bg-[#5D4037] transition">{{ __('Grant') }}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
