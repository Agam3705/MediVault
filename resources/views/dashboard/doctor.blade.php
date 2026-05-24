<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                    <span x-show="activeTab === 'cases'">📂 {{ __('Clinical Case Directory') }}</span>
                    <span x-show="activeTab === 'search'">🔍 {{ __('Search Patient Vaults') }}</span>
                    <span x-show="activeTab === 'requests'">📡 {{ __('Sent Consent Requests') }}</span>
                    <span x-show="activeTab === 'chat'">💬 {{ __('Clinical Chats') }}</span>
                    <span x-show="activeTab === 'audits'">📋 {{ __('My Activity Audit Logs') }}</span>
                </h2>
                <p class="text-sm text-[#8D6E63] mt-1">
                    <span x-show="activeTab === 'cases'">{{ __('Submit patient access requests, view approved clinical histories, and manage active cases.') }}</span>
                    <span x-show="activeTab === 'search'">{{ __('Query patients by full name or email address to request access or activate emergency bypass.') }}</span>
                    <span x-show="activeTab === 'requests'">{{ __('Track active or pending access authorization requests sent to patients.') }}</span>
                    <span x-show="activeTab === 'chat'">{{ __('Start or continue a private consultation thread with any patient under your care.') }}</span>
                    <span x-show="activeTab === 'audits'">{{ __('A complete log of all clinical actions performed under your credentials.') }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    {{ $doctor->specialization ?? __('Medical Practitioner') }}
                </span>
                @if($doctor->isVerified())
                    <span class="text-xs bg-green-50/80 text-green-700 font-bold px-3 py-1.5 rounded-full border border-green-200">
                        {{ __('Verified Practitioner') }}
                    </span>
                @else
                    <span class="text-xs bg-yellow-50 text-yellow-700 font-bold px-3 py-1.5 rounded-full border border-yellow-200">
                        {{ __('Verification Pending') }}
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Success/Error Banners -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-center justify-between text-red-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⚠️</span>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        <!-- CHECK 1: License Onboarding Submission (Ring 1) -->
        @if(empty($doctor->license_number))
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl rounded-3xl p-8 max-w-2xl mx-auto my-6">
                <div class="text-center mb-6">
                    <span class="text-4xl">🏥</span>
                    <h3 class="text-xl font-extrabold text-[#3E2723] mt-3">{{ __('Clinical Credentials Registration') }}</h3>
                    <p class="text-xs text-[#8D6E63] mt-1">{{ __('Please register your medical licence to unlock clinical record search features.') }}</p>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="flex flex-col gap-5">
                    @csrf
                    @method('PATCH')

                    <!-- Hidden fields for Breeze update -->
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div>
                        <label for="license_number" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">{{ __('License Registration Number') }}</label>
                        <input type="text" name="license_number" id="license_number" required placeholder="e.g. MC-89234-A"
                               class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="specialization" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">{{ __('Specialization') }}</label>
                            <input type="text" name="specialization" id="specialization" required placeholder="e.g. Cardiology"
                                   class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">
                        </div>
                        <div>
                            <label for="hospital" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">{{ __('Hospital Affiliation') }}</label>
                            <input type="text" name="hospital" id="hospital" required placeholder="e.g. City General Hospital"
                                   class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">
                        </div>
                    </div>

                    <div>
                        <label for="bio" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">{{ __('Practitioner Brief Bio') }}</label>
                        <textarea name="bio" id="bio" rows="4" placeholder="Brief summary of your clinical practice..."
                                  class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 mt-2 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold rounded-xl transition active:scale-95 shadow-md">
                        {{ __('Submit License Registration Application') }}
                    </button>
                </form>
            </div>

        <!-- CHECK 2: Pending Approval Status Screen (Ring 2) -->
        @elseif(!$doctor->isVerified())
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl rounded-3xl p-8 max-w-2xl mx-auto my-6 text-center">
                <span class="text-4xl animate-pulse inline-block mb-3">⏳</span>
                <h3 class="text-xl font-extrabold text-[#3E2723]">{{ __('Practitioner License Verification Pending') }}</h3>
                <p class="text-sm text-[#8D6E63] mt-2 leading-relaxed">
                    {{ __('Your submitted credentials') }} (<strong class="text-[#3E2723]">{{ $doctor->license_number }}</strong>) {{ __('are currently undergoing verification by the system administrator. You will be granted clinical record access once approved.') }}
                </p>
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-100 rounded-2xl text-xs text-yellow-800 font-semibold inline-block">
                    📢 {{ __('Verification usually completes within 24 hours.') }}
                </div>
            </div>

        <!-- CHECK 3: Fully verified dashboard -->
        @else
            <div>
                
                <!-- Sleek Full Length Global Search Bar -->
                <div class="mb-8">
                    <button @click="$dispatch('open-search')" class="w-full px-5 py-4 bg-white/80 dark:bg-transparent backdrop-blur-md border border-[#D7CCC8]/60 rounded-3xl flex items-center justify-between text-[#8D6E63] hover:bg-[#FDFBF7]/50 transition-all duration-300 shadow-sm group">
                        <span class="flex items-center gap-3">
                            <span class="text-base shrink-0">🔍</span>
                            <span class="text-xs font-semibold text-left">{{ __('Search patients, clinical records, or consent logs instantly...') }}</span>
                        </span>
                        <span class="text-[10px] font-bold text-[#8D6E63] bg-[#EFEBE9] px-2 py-0.5 rounded border border-[#D7CCC8]/40 group-hover:bg-[#D7CCC8]/20">Ctrl+K</span>
                    </button>
                </div>

                <!-- 1. CASES TAB -->
                <div x-show="activeTab === 'cases'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm" x-cloak>
                    <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-4 border-b border-[#D7CCC8]/40 pb-3">
                        <span>📂</span> {{ __('Clinical Case Directory') }}
                    </h3>

                    @if($activeCases->isEmpty())
                        <div class="py-12 text-center text-sm text-[#8D6E63]">
                            <span class="text-3xl block mb-2">📁</span>
                            {{ __('No active clinical cases currently under authorization. Use the search tab to query patients and request consent.') }}
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($activeCases as $case)
                                <div class="bg-[#FDFBF7]/60 border border-[#D7CCC8]/50 hover:border-[#5D4037]/30 rounded-2xl p-4 transition duration-150 flex flex-col justify-between gap-4 shadow-sm">
                                    <div>
                                        <div class="flex items-start justify-between">
                                            <h4 class="font-bold text-sm text-[#3E2723]">{{ $case->patient->user->name }}</h4>
                                            <span class="text-[10px] font-extrabold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded uppercase">
                                                {{ $case->patient->blood_group ?? 'Not Set' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-[#8D6E63] mt-1">{{ $case->patient->user->email }}</p>
                                        <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('Gender:') }} {{ __($case->patient->gender) ?? 'Not Set' }} &bull; {{ __('Age:') }} {{ $case->patient->dob ? $case->patient->dob->age : 'Not Set' }}</p>
                                        
                                        <div class="flex items-center gap-1.5 mt-3">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                            <span class="text-[10px] uppercase font-bold text-green-700 bg-green-50/50 border border-green-200 px-2 py-0.5 rounded">
                                                {{ $case->access_type === 'emergency-override' ? __('Emergency') : __('Standard') }}
                                            </span>
                                            <span class="text-[10px] text-[#8D6E63]">
                                                {{ __('Expires:') }} {{ $case->expires_at ? $case->expires_at->diffForHumans() : __('Unlimited') }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 w-full">
                                        <a href="{{ route('records.patient', $case->patient->id) }}" class="flex-grow text-center px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow active:scale-[0.98]">
                                            {!! __('Open Records &rarr;') !!}
                                        </a>
                                        <a href="{{ route('chat.show', $case->patient->user->id) }}" class="px-4 py-2 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-xl transition shadow active:scale-[0.98] flex items-center justify-center shrink-0" title="{{ __('Chat with Patient') }}">
                                            <span>💬</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 2. PATIENT SEARCH TAB -->
                <div x-show="activeTab === 'search'" class="flex flex-col gap-6" x-cloak>
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                        <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-1.5 mb-3">
                            <span>🔍</span> {{ __('Search Patient Vaults') }}
                        </h3>
                        <p class="text-xs text-[#8D6E63] mb-4">{{ __('Query patients by full name or email address to request access or activate emergency bypass.') }}</p>

                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col gap-4">
                            <input type="hidden" name="tab" value="search">
                            <div class="flex flex-col md:flex-row gap-3">
                                <div class="relative flex-grow">
                                    <input type="text" name="search" value="{{ $searchQuery }}" placeholder="Search patient name, email, phone or address..." class="w-full pl-10 pr-4 py-3 border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-2xl shadow-sm font-medium transition duration-150">
                                    <span class="absolute left-3.5 top-3.5 text-gray-400">🔍</span>
                                </div>
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#5D4037] to-[#3E2723] hover:from-[#795548] text-white font-bold text-sm rounded-2xl transition duration-150 active:scale-95 shadow-md">
                                    {{ __('Search Patients') }}
                                </button>
                                @if($searchQuery || $bloodGroupFilter || $genderFilter || $ageRange)
                                    <a href="{{ route('dashboard', ['tab' => 'search']) }}" class="px-5 py-3 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] text-sm font-bold rounded-2xl transition duration-150 text-center flex items-center justify-center">
                                        {{ __('Clear Search') }}
                                    </a>
                                @endif
                            </div>
                            
                            <!-- Search Filters -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="flex flex-col gap-1">
                                    <label class="text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider pl-1">{{ __('Blood Group') }}</label>
                                    <select name="blood_group" class="w-full px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] text-[#3E2723] rounded-xl text-xs font-semibold transition">
                                        <option value="">{{ __('Any Blood Group') }}</option>
                                        <option value="A+" @selected($bloodGroupFilter === 'A+')>A+</option>
                                        <option value="A-" @selected($bloodGroupFilter === 'A-')>A-</option>
                                        <option value="B+" @selected($bloodGroupFilter === 'B+')>B+</option>
                                        <option value="B-" @selected($bloodGroupFilter === 'B-')>B-</option>
                                        <option value="AB+" @selected($bloodGroupFilter === 'AB+')>AB+</option>
                                        <option value="AB-" @selected($bloodGroupFilter === 'AB-')>AB-</option>
                                        <option value="O+" @selected($bloodGroupFilter === 'O+')>O+</option>
                                        <option value="O-" @selected($bloodGroupFilter === 'O-')>O-</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider pl-1">{{ __('Gender') }}</label>
                                    <select name="gender" class="w-full px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] text-[#3E2723] rounded-xl text-xs font-semibold transition">
                                        <option value="">{{ __('Any Gender') }}</option>
                                        <option value="Male" @selected($genderFilter === 'Male')>{{ __('Male') }}</option>
                                        <option value="Female" @selected($genderFilter === 'Female')>{{ __('Female') }}</option>
                                        <option value="Other" @selected($genderFilter === 'Other')>{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <label class="text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider pl-1">{{ __('Age Range') }}</label>
                                    <select name="age_range" class="w-full px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] text-[#3E2723] rounded-xl text-xs font-semibold transition">
                                        <option value="">{{ __('Any Age') }}</option>
                                        <option value="0-17" @selected($ageRange === '0-17')>{{ __('Under 18') }}</option>
                                        <option value="18-30" @selected($ageRange === '18-30')>{{ __('18 - 30') }}</option>
                                        <option value="31-50" @selected($ageRange === '31-50')>{{ __('31 - 50') }}</option>
                                        <option value="51-150" @selected($ageRange === '51-150')>{{ __('50+') }}</option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        @if($searchQuery || $bloodGroupFilter || $genderFilter || $ageRange)
                            <div class="mt-6 border-t border-[#D7CCC8]/40 pt-6">
                                <h4 class="text-xs uppercase font-bold text-[#8D6E63] tracking-wider mb-4">{{ __('Search Results') }}</h4>
                                
                                @if(count($searchResults) === 0)
                                    <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-6 text-center text-sm text-[#8D6E63]">
                                        {{ __('No matching patients found in MediVault database.') }}
                                    </div>
                                @else
                                    <div class="flex flex-col gap-4">
                                        @foreach($searchResults as $pat)
                                            @php
                                                $activeGrant = \App\Models\AccessGrant::where('doctor_id', $doctor->id)
                                                    ->where('patient_id', $pat->id)
                                                    ->where('is_active', true)
                                                    ->where(function($q) {
                                                        $q->whereNull('expires_at')
                                                          ->orWhere('expires_at', '>', now());
                                                    })
                                                    ->first();
                                                
                                                $pendingReq = \App\Models\AccessRequest::where('doctor_id', $doctor->id)
                                                    ->where('patient_id', $pat->id)
                                                    ->where('status', 'pending')
                                                    ->where(function($q) {
                                                        $q->whereNull('expires_at')
                                                          ->orWhere('expires_at', '>', now());
                                                    })
                                                    ->first();
                                            @endphp
                                            <div class="bg-white border border-[#D7CCC8]/50 hover:border-[#5D4037]/30 hover:shadow-md rounded-2xl p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm transition duration-200">
                                                <div class="flex-grow">
                                                    <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                                        <h5 class="font-extrabold text-[#3E2723] text-sm md:text-base">{{ $pat->user->name }}</h5>
                                                        <span class="text-[9px] md:text-[10px] font-bold bg-[#EFEBE9] text-[#5D4037] px-2.5 py-0.5 rounded-full border border-[#D7CCC8]/80">
                                                            {{ __('Completeness:') }} {{ $pat->completeness_score }}%
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-4 text-xs mt-3">
                                                        <div>
                                                            <span class="text-[#8D6E63] font-semibold">{{ __('Email:') }}</span>
                                                            <span class="font-bold text-[#3E2723] ml-1">{{ $pat->user->email }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[#8D6E63] font-semibold">{{ __('Phone:') }}</span>
                                                            <span class="font-bold text-[#3E2723] ml-1">{{ $pat->phone ?? __('N/A') }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[#8D6E63] font-semibold">{{ __('Age:') }}</span>
                                                            <span class="font-bold text-[#3E2723] ml-1">{{ $pat->dob ? $pat->dob->age . ' yrs' : __('N/A') }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[#8D6E63] font-semibold">{{ __('Gender:') }}</span>
                                                            <span class="font-bold text-[#3E2723] ml-1">{{ $pat->gender ?? __('N/A') }}</span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[#8D6E63] font-semibold">{{ __('Blood Group:') }}</span>
                                                            <span class="font-bold text-red-600 ml-1">{{ $pat->blood_group ?? __('N/A') }}</span>
                                                        </div>
                                                        <div class="col-span-1 sm:col-span-2 md:col-span-3">
                                                            <span class="text-[#8D6E63] font-semibold">{{ __('Address:') }}</span>
                                                            <span class="font-medium text-[#3E2723] ml-1">{{ $pat->address ?? __('N/A') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="flex items-center gap-2">
                                                    @if($activeGrant)
                                                        <span class="text-xs bg-green-50 text-green-700 font-bold px-3 py-1.5 rounded-xl border border-green-200 uppercase shrink-0">
                                                            {{ __('Authorized Access') }}
                                                        </span>
                                                        <a href="{{ route('records.patient', $pat->id) }}" class="px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                                                            {!! __('Open Vault Files &rarr;') !!}
                                                        </a>
                                                    @elseif($pendingReq)
                                                        <span class="text-xs bg-amber-50 text-amber-700 font-bold px-4 py-2 rounded-xl border border-amber-200 uppercase shrink-0">
                                                            {{ __('Request Pending') }}
                                                        </span>
                                                    @else
                                                        <!-- Request standard consent -->
                                                        <div x-data="{ open: false }" class="relative">
                                                            <button @click="open = !open" class="px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                                                                {{ __('Request Access') }}
                                                            </button>
                                                            
                                                            <!-- Simple Modal/Popup Form -->
                                                            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 p-5 bg-white border border-[#D7CCC8] shadow-xl rounded-2xl w-80 z-20 text-left" x-cloak>
                                                                <h6 class="font-bold text-[#3E2723] text-sm mb-3">{{ __('Request Patient Consent') }}</h6>
                                                                <form method="POST" action="{{ route('consent.request') }}">
                                                                    @csrf
                                                                    <input type="hidden" name="patient_id" value="{{ $pat->id }}">
                                                                    <div class="mb-3">
                                                                        <label class="block text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider mb-1">{{ __('Reason for Access') }}</label>
                                                                        <textarea name="reason" rows="3" required class="w-full p-2 border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-xs text-[#3E2723] rounded-xl" placeholder="e.g. Clinic checkup..."></textarea>
                                                                    </div>
                                                                    <div class="mb-4">
                                                                        <label class="block text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider mb-1">{{ __('Session Duration') }}</label>
                                                                        <select name="duration" class="w-full p-2 border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-xs text-[#3E2723] rounded-xl">
                                                                            <option value="1">1 Day</option>
                                                                            <option value="7">7 Days</option>
                                                                            <option value="30" selected>30 Days</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="flex gap-2 justify-end">
                                                                        <button type="button" @click="open = false" class="px-3 py-1.5 border border-[#D7CCC8] text-[#5D4037] text-[10px] font-bold rounded-lg hover:bg-gray-50">{{ __('Cancel') }}</button>
                                                                        <button type="submit" class="px-3.5 py-1.5 bg-[#3E2723] text-white text-[10px] font-bold rounded-lg transition">{{ __('Submit') }}</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>

                                                        <!-- Emergency Access Override (Ring 3: Bypasses check, logs warning) -->
                                                        <form method="POST" action="{{ route('consent.override', $pat->id) }}" onsubmit="return confirm('CRITICAL AUDIT WARNING: Emergency access override is registered as a critical security incident. Are you sure you want to bypass standard patient consent policies for immediate treatment?')">
                                                            @csrf
                                                            <button type="submit" class="px-4 py-2 border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl transition shadow-sm active:scale-95 flex items-center gap-1">
                                                                🚨 {{ __('Emergency Override') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 3. SENT REQUESTS TAB -->
                <div x-show="activeTab === 'requests'" class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm" x-cloak>
                    <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-4 border-b border-[#D7CCC8]/40 pb-3">
                        <span>📡</span> {{ __('Sent Consent Requests Log') }}
                    </h3>

                    @if($sentRequests->isEmpty())
                        <div class="py-12 text-center text-sm text-[#8D6E63]">
                            {{ __('No pending or past consent requests found. Search for a patient to request authorization.') }}
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($sentRequests as $req)
                                <div class="bg-white border border-[#D7CCC8]/50 rounded-2xl p-4 flex flex-col gap-2.5 shadow-sm">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-bold text-[#3E2723] text-sm">{{ $req->patient->user->name }}</h4>
                                            <p class="text-[10px] text-[#8D6E63]">{{ $req->created_at->diffForHumans() }}</p>
                                        </div>
                                        <span class="text-[9px] uppercase font-extrabold bg-amber-50 text-amber-600 border border-amber-200 px-2 py-0.5 rounded">
                                            {{ $req->status }}
                                        </span>
                                    </div>
                                    <div class="bg-[#FDFBF7] border border-[#D7CCC8]/30 rounded-xl p-2.5">
                                        <p class="text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider">{{ __('Justification:') }}</p>
                                        <p class="text-[11px] text-[#5D4037] mt-0.5 line-clamp-2" title="{{ $req->reason }}">{{ $req->reason }}</p>
                                    </div>
                                    <div class="text-[9px] text-red-600 font-semibold">
                                        ⚠️ {{ __('Expires:') }} {{ $req->expires_at->format('M d, Y h:i A') }}
                                    </div>
                                    <form method="POST" action="/consent/request/{{ $req->id }}/withdraw" onsubmit="return confirm('Withdraw this access request?')" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full px-3 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-[10px] font-bold rounded-xl transition active:scale-95">
                                            🗑️ Withdraw Request
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- 4. CLINICAL CHATS TAB -->
                <div x-show="activeTab === 'chat'" class="flex flex-col gap-6" x-cloak>
                    @php
                        $chatPatients = collect();
                        foreach($activeCases as $case) {
                            if ($case->patient) {
                                $chatPatients->put((string)$case->patient->id, [
                                    'patient' => $case->patient,
                                    'status' => 'Authorized Access',
                                    'status_color' => 'bg-green-50 text-green-700 border-green-200'
                                ]);
                            }
                        }
                        foreach($sentRequests as $req) {
                            if ($req->patient) {
                                $chatPatients->put((string)$req->patient->id, [
                                    'patient' => $req->patient,
                                    'status' => 'Request Pending',
                                    'status_color' => 'bg-amber-50 text-amber-700 border-amber-200'
                                ]);
                            }
                        }
                    @endphp

                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4">
                        <div>
                            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2">
                                <span>💬</span> {{ __('Clinical Chats Directory') }}
                            </h3>
                            <p class="text-xs text-[#8D6E63] mt-0.5 font-medium">{{ __('Start or continue a private consultation thread with any patient under your care.') }}</p>
                        </div>

                        @if($chatPatients->isEmpty())
                            <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-8 text-center text-sm text-[#8D6E63]">
                                {{ __('No clinical contacts available. Select patients will appear here once requested or approved.') }}
                            </div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($chatPatients as $patData)
                                    @php
                                        $pat = $patData['patient'];
                                    @endphp
                                    <div class="bg-white border border-[#D7CCC8]/50 hover:border-[#D7CCC8] rounded-2xl p-4 flex justify-between items-center gap-4 shadow-sm transition duration-150">
                                        <div>
                                            <h5 class="font-bold text-[#3E2723] text-sm">{{ $pat->user->name }}</h5>
                                            <p class="text-xs text-[#8D6E63]">{{ $pat->user->email }}</p>
                                            <span class="inline-block mt-2 text-[10px] font-extrabold px-2 py-0.5 rounded border {{ $patData['status_color'] }} uppercase">
                                                {{ __($patData['status']) }}
                                            </span>
                                        </div>
                                        <a href="{{ route('chat.show', $pat->user->id) }}" class="px-4 py-2.5 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow active:scale-95 flex items-center gap-1.5 shrink-0">
                                            <span>💬</span> {{ __('Chat') }}
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 5. AUDIT LOGS TAB (Doctor's own actions) -->
                <div x-show="activeTab === 'audits'" class="flex flex-col gap-6" x-cloak>

                    <!-- LOGIN HISTORY (top) -->
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-4 border-b border-[#D7CCC8]/40 pb-3">
                            <div>
                                <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2">
                                    <span>🖥️</span> {{ __('Login Session History') }}
                                </h3>
                                <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('Your recent login sessions across devices.') }}</p>
                            </div>
                            <a href="{{ route('profile.sessions') }}" class="text-xs font-bold text-[#5D4037] hover:text-[#3E2723] border border-[#D7CCC8]/60 px-3 py-1.5 rounded-xl hover:bg-[#FDFBF7] transition flex items-center gap-1">
                                {!! __('Manage Sessions &rarr;') !!}
                            </a>
                        </div>

                        @if($doctorLoginSessions->isEmpty())
                            <p class="text-xs text-[#8D6E63] text-center py-4">{{ __('No active sessions recorded.') }}</p>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($doctorLoginSessions as $session)
                                    <div class="flex items-center gap-3 bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-4">
                                        <span class="text-2xl p-2 bg-[#EFEBE9] rounded-xl border border-[#D7CCC8]/30 shrink-0">
                                            {{ $session->device === 'Mobile Device' ? '📱' : ($session->device === 'Tablet Device' ? '📟' : '💻') }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-xs font-bold text-[#3E2723]">{{ $session->device }}</p>
                                                @if($session->session_token === session()->getId())
                                                    <span class="text-[9px] font-bold bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full uppercase">{{ __('Current') }}</span>
                                                @elseif($session->is_active)
                                                    <span class="text-[9px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-full uppercase">{{ __('Active') }}</span>
                                                @endif
                                            </div>
                                            <p class="text-[10px] text-[#8D6E63] mt-0.5 font-mono truncate">{{ $session->ip_address }}</p>
                                            <p class="text-[10px] text-[#8D6E63]">{{ __('Last active:') }} {{ $session->last_active_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- MY AUDIT LOGS (with filters) -->
                    <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4">
                        <div class="flex items-center justify-between border-b border-[#D7CCC8]/40 pb-3">
                            <div>
                                <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2">
                                    <span>📋</span> {{ __('My Activity Audit Logs') }}
                                    <span class="text-xs font-bold bg-[#EFEBE9] text-[#5D4037] px-2 py-0.5 rounded-full border border-[#D7CCC8]/60">{{ $doctorAuditLogs->count() }}</span>
                                </h3>
                                <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('A complete log of all clinical actions performed under your credentials.') }}</p>
                            </div>
                            @if($docAuditSearch || $docAuditAction || $docAuditUnusual)
                                <a href="{{ route('dashboard', ['tab' => 'audits']) }}" class="text-xs text-[#5D4037] hover:text-[#3E2723] font-bold border border-[#D7CCC8]/60 px-3 py-1.5 rounded-xl hover:bg-[#FDFBF7] transition shrink-0">
                                    ✕ {{ __('Clear') }}
                                </a>
                            @endif
                        </div>

                        <!-- Filter Bar -->
                        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-3">
                            <input type="hidden" name="tab" value="audits">
                            <div class="relative flex-grow">
                                <input type="text" name="doc_audit_search" value="{{ $docAuditSearch ?? '' }}"
                                       placeholder="{{ __('Search by action or IP address...') }}"
                                       class="w-full pl-9 pr-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition">
                                <span class="absolute left-3 top-3 text-[#8D6E63] text-xs">🔍</span>
                            </div>
                            <select name="doc_audit_action" class="px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition min-w-[180px]">
                                <option value="">{{ __('All Actions') }}</option>
                                @foreach($allDoctorAuditActions as $actionOpt)
                                    <option value="{{ $actionOpt }}" @selected($docAuditAction === $actionOpt)>{{ $actionOpt }}</option>
                                @endforeach
                            </select>
                            <label class="flex items-center gap-2 px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] rounded-xl cursor-pointer hover:bg-red-50 hover:border-red-200 transition shrink-0">
                                <input type="checkbox" name="doc_audit_unusual" value="1" @checked($docAuditUnusual === '1') class="rounded border-[#D7CCC8] text-red-600">
                                <span class="text-xs font-bold text-red-700">⚠️ {{ __('Unusual') }}</span>
                            </label>
                            <button type="submit" class="px-5 py-2.5 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold text-xs rounded-xl transition shadow-sm active:scale-95 shrink-0">
                                {{ __('Filter') }}
                            </button>
                        </form>

                        @if($docAuditSearch || $docAuditAction || $docAuditUnusual)
                            <div class="flex flex-wrap gap-2">
                                @if($docAuditSearch)<span class="text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">🔍 "{{ $docAuditSearch }}"</span>@endif
                                @if($docAuditAction)<span class="text-[10px] font-bold bg-[#EFEBE9] text-[#5D4037] border border-[#D7CCC8]/60 px-2.5 py-1 rounded-full">⚙️ {{ $docAuditAction }}</span>@endif
                                @if($docAuditUnusual === '1')<span class="text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full">⚠️ Unusual Only</span>@endif
                            </div>
                        @endif

                        @if($doctorAuditLogs->isEmpty())
                            <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-6 text-center text-sm text-[#8D6E63]">
                                {{ $docAuditSearch || $docAuditAction || $docAuditUnusual
                                    ? __('No audit log entries match your filters.')
                                    : __('No audit logs found. Your actions will appear here as you use the platform.') }}
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-2xl border border-[#D7CCC8]/40">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-[#EFEBE9]/60 border-b border-[#D7CCC8] text-[#8D6E63] uppercase font-bold tracking-wider">
                                            <th class="py-3 px-4">{{ __('Action') }}</th>
                                            <th class="py-3 px-4">{{ __('Target') }}</th>
                                            <th class="py-3 px-4">{{ __('IP Address') }}</th>
                                            <th class="py-3 px-4">{{ __('Time') }}</th>
                                            <th class="py-3 px-4">{{ __('Flag') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#D7CCC8]/30">
                                        @foreach($doctorAuditLogs as $log)
                                            <tr class="hover:bg-[#FDFBF7] transition-colors duration-100 {{ $log->unusual_activity ? 'bg-red-50/40' : '' }}">
                                                <td class="py-3 px-4 font-bold text-[#3E2723]">{{ $log->action }}</td>
                                                <td class="py-3 px-4">
                                                    @if($log->target_type)
                                                        <span class="font-mono text-[9px] bg-[#EFEBE9] text-[#5D4037] px-1.5 py-0.5 rounded">{{ $log->target_type }}</span>
                                                    @else
                                                        <span class="text-[#8D6E63]">—</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 font-mono text-[10px] text-[#8D6E63]">{{ $log->ip_address ?? '—' }}</td>
                                                <td class="py-3 px-4">
                                                    <span class="text-[10px] text-[#8D6E63]">{{ $log->created_at->format('M d, H:i') }}</span>
                                                    <p class="text-[9px] text-[#8D6E63]/70">{{ $log->created_at->diffForHumans() }}</p>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($log->unusual_activity)
                                                        <span class="text-[9px] font-bold bg-red-100 text-red-700 border border-red-200 px-2 py-0.5 rounded uppercase animate-pulse">⚠️ Unusual</span>
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

            </div>
        @endif

    </div>
</x-app-layout>
