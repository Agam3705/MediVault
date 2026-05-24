<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                    <span x-show="activeTab === 'overview'">👋 {{ __('Patient Overview') }}</span>
                    <span x-show="activeTab === 'records'">📂 {{ __('Medical Vault') }}</span>
                    <span x-show="activeTab === 'consent'">🔒 {{ __('Consent Manager') }}</span>
                    <span x-show="activeTab === 'audits'">🕵️‍♂️ {{ __('Security Access Audits') }}</span>
                    <span x-show="activeTab === 'chat'">💬 {{ __('Clinical Chats') }}</span>
                    <span x-show="activeTab === 'search'">🔍 {{ __('Search Patients') }}</span>
                </h2>
                <p class="text-sm text-[#8D6E63] mt-1">
                    <span x-show="activeTab === 'overview'">{{ __('Manage your clinical consent, review records, and check your emergency responders status.') }}</span>
                    <span x-show="activeTab === 'records'">{{ __('Upload medical summaries, prescription scans, or test reports.') }}</span>
                    <span x-show="activeTab === 'consent'">{{ __('Authorise specific doctors to view or edit specific records. Instantly revoke consent permissions.') }}</span>
                    <span x-show="activeTab === 'audits'">{{ __('Every view, download, and emergency scan of your records is logged here.') }}</span>
                    <span x-show="activeTab === 'chat'">{{ __('Start or continue a private consultation thread with any doctor under your care.') }}</span>
                    <span x-show="activeTab === 'search'">{{ __('Query verified medical practitioners in the network.') }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    {{ __('Patient Vault') }}
                </span>
                <span class="text-xs bg-green-50/80 text-green-700 font-bold px-3 py-1.5 rounded-full border border-green-200">
                    {{ __('Secure Session') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Flash Alert Toasts -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm animate-fade-in">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <span class="text-xl">⚠️</span>
                    <p class="text-sm font-semibold">Please fix the following validation errors:</p>
                </div>
                <ul class="list-disc pl-5 text-xs font-semibold flex flex-col gap-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Sleek Full Length Global Search Bar -->
        <div class="mb-8">
            <button @click="$dispatch('open-search')" class="w-full px-5 py-4 bg-white/80 dark:bg-transparent backdrop-blur-md border border-[#D7CCC8]/60 rounded-3xl flex items-center justify-between text-[#8D6E63] hover:bg-[#FDFBF7]/50 transition-all duration-300 shadow-sm group">
                <span class="flex items-center gap-3">
                    <span class="text-base shrink-0">🔍</span>
                    <span class="text-xs font-semibold text-left">{{ __('Search your clinical documents, security logs, or active consent grants instantly...') }}</span>
                </span>
                <span class="text-[10px] font-bold text-[#8D6E63] bg-[#EFEBE9] px-2 py-0.5 rounded border border-[#D7CCC8]/40 group-hover:bg-[#D7CCC8]/20">Ctrl+K</span>
            </button>
        </div>

        <!-- 1. OVERVIEW TAB -->
        <div x-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-12 gap-8" x-cloak>
            
            <!-- Left column: Onboarding and Emergency Card -->
            <div class="lg:col-span-7 flex flex-col gap-8">
                
                <!-- Onboarding Wizard -->
                @if($completeness < 100)
                <div x-data="{ step: 1 }" class="bg-gradient-to-br from-[#5D4037] to-[#3E2723] rounded-3xl p-6 text-[#FFF8E1] shadow-lg relative overflow-hidden transition duration-300">
                    <div class="absolute -right-16 -top-16 w-40 h-40 bg-white/5 rounded-full blur-2xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3 mb-4">
                            <div>
                                <span class="text-[10px] uppercase font-extrabold tracking-widest text-[#D7CCC8]">{{ __('Onboarding Wizard') }}</span>
                                <h3 class="text-lg font-bold text-white mt-0.5">{{ __("Let's complete your health profile") }}</h3>
                            </div>
                            <span class="text-xs font-bold text-[#FFF8E1]/80">{{ __('Step') }} <span x-text="step"></span> {{ __('of') }} 3</span>
                        </div>

                        <!-- Step Progress Indicator -->
                        <div class="flex gap-2 mb-6">
                            <div class="h-1 flex-1 rounded-full transition-all duration-300" :class="step >= 1 ? 'bg-[#FFF8E1]' : 'bg-white/20'"></div>
                            <div class="h-1 flex-1 rounded-full transition-all duration-300" :class="step >= 2 ? 'bg-[#FFF8E1]' : 'bg-white/20'"></div>
                            <div class="h-1 flex-1 rounded-full transition-all duration-300" :class="step >= 3 ? 'bg-[#FFF8E1]' : 'bg-white/20'"></div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <!-- Hidden default user fields for Laravel validator -->
                            <input type="hidden" name="name" value="{{ $user->name }}">
                            <input type="hidden" name="email" value="{{ $user->email }}">

                            <!-- Step 1: Basic Demographics -->
                            <div x-show="step === 1" class="flex flex-col gap-4">
                                <h4 class="text-sm font-bold">{{ __('Step 1: Your Birthday & Gender') }}</h4>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Date of Birth') }}</label>
                                    <input type="date" name="dob" value="{{ old('dob', $patient->dob) }}" required
                                           class="w-full px-4 py-2.5 bg-white/10 border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition placeholder-white/40">
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Gender Identity') }}</label>
                                    <select name="gender" required
                                            class="w-full px-4 py-2.5 bg-[#3E2723] border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition">
                                        <option value="" disabled selected>{{ __('Select Gender') }}</option>
                                        <option value="Male" @selected($patient->gender === 'Male')>{{ __('Male') }}</option>
                                        <option value="Female" @selected($patient->gender === 'Female')>{{ __('Female') }}</option>
                                        <option value="Other" @selected($patient->gender === 'Other')>{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="button" @click="step = 2" class="px-5 py-2 bg-[#FFF8E1] hover:bg-[#FDFBF7] text-[#3E2723] font-bold text-xs rounded-xl shadow transition duration-150">
                                        {!! __('Next Step &rarr;') !!}
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Contact & Blood Baseline -->
                            <div x-show="step === 2" class="flex flex-col gap-4" x-cloak>
                                <h4 class="text-sm font-bold">{{ __('Step 2: Medical Baseline & Address') }}</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Blood Group') }}</label>
                                        <select name="blood_group" required
                                                class="w-full px-4 py-2.5 bg-[#3E2723] border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition">
                                            <option value="" disabled selected>{{ __('Blood Type') }}</option>
                                            <option value="A+" @selected($patient->blood_group === 'A+')>A+</option>
                                            <option value="A-" @selected($patient->blood_group === 'A-')>A-</option>
                                            <option value="B+" @selected($patient->blood_group === 'B+')>B+</option>
                                            <option value="B-" @selected($patient->blood_group === 'B-')>B-</option>
                                            <option value="AB+" @selected($patient->blood_group === 'AB+')>AB+</option>
                                            <option value="AB-" @selected($patient->blood_group === 'AB-')>AB-</option>
                                            <option value="O+" @selected($patient->blood_group === 'O+')>O+</option>
                                            <option value="O-" @selected($patient->blood_group === 'O-')>O-</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Phone Number') }}</label>
                                        <input type="tel" name="phone" value="{{ old('phone', $patient->phone) }}" placeholder="e.g. +91 9876543210" required
                                               class="w-full px-4 py-2.5 bg-white/10 border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition placeholder-white/40">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Home Address') }}</label>
                                    <input type="text" name="address" value="{{ old('address', $patient->address) }}" placeholder="Street address, city, state" required
                                           class="w-full px-4 py-2.5 bg-white/10 border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition placeholder-white/40">
                                </div>
                                <div class="flex justify-between mt-4">
                                    <button type="button" @click="step = 1" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition">
                                        {!! __('&larr; Back') !!}
                                    </button>
                                    <button type="button" @click="step = 3" class="px-5 py-2 bg-[#FFF8E1] hover:bg-[#FDFBF7] text-[#3E2723] font-bold text-xs rounded-xl shadow transition duration-150">
                                        {!! __('Next Step &rarr;') !!}
                                    </button>
                                </div>
                            </div>

                            <!-- Step 3: Emergency Contact -->
                            <div x-show="step === 3" class="flex flex-col gap-4" x-cloak>
                                <h4 class="text-sm font-bold">{{ __('Step 3: Emergency Responder Contact') }}</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Contact Name') }}</label>
                                        <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}" placeholder="e.g. Spouse/Parent name" required
                                               class="w-full px-4 py-2.5 bg-white/10 border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition placeholder-white/40">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] uppercase font-bold text-[#D7CCC8] mb-1.5">{{ __('Contact Phone') }}</label>
                                        <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}" placeholder="Emergency number" required
                                               class="w-full px-4 py-2.5 bg-white/10 border border-white/20 focus:border-[#FFF8E1] focus:ring-[#FFF8E1] rounded-xl text-xs font-medium text-white transition placeholder-white/40">
                                    </div>
                                </div>
                                <div class="flex justify-between mt-4">
                                    <button type="button" @click="step = 2" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl transition">
                                        {!! __('&larr; Back') !!}
                                    </button>
                                    <button type="submit" class="px-6 py-2 bg-[#FFF8E1] hover:bg-[#FDFBF7] text-[#3E2723] font-bold text-xs rounded-xl shadow transition duration-150 active:scale-95">
                                        {{ __('Complete Onboarding Profile') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Emergency Card -->
                @if($emergencyCard)
                <div class="bg-gradient-to-br from-[#FDFBF7] to-[#F5F2EB] rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-md relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-[#5D4037]/10 to-transparent rounded-bl-full"></div>
                    
                    <div class="flex items-center justify-between mb-4 border-b border-[#D7CCC8]/40 pb-3">
                        <div>
                            <span class="text-[10px] uppercase tracking-widest font-extrabold text-[#8D6E63] block">{{ __('Life Saving Interface') }}</span>
                            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-1.5 mt-0.5">
                                <span>🎴</span> {{ __('QR Emergency Card') }}
                            </h3>
                        </div>
                        <span class="text-[10px] font-bold {{ $emergencyCard->is_public ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }} border px-2 py-0.5 rounded-full uppercase">
                            {{ $emergencyCard->is_public ? __('Enabled') : __('Disabled') }}
                        </span>
                    </div>

                    <div class="flex flex-col md:flex-row items-center md:items-stretch gap-5">
                        <div class="flex-grow flex flex-col justify-between gap-3 text-xs">
                            <div>
                                <p class="text-[10px] font-semibold text-[#8D6E63] uppercase tracking-wider">{{ __('Blood Type') }}</p>
                                <p class="font-extrabold text-base text-red-600 mt-0.5">{{ $emergencyCard->blood_group ?? 'Not Set' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-[#8D6E63] uppercase tracking-wider">{{ __('Allergies') }}</p>
                                <p class="font-semibold text-[#3E2723] mt-0.5 line-clamp-1" title="{{ $emergencyCard->allergies }}">{{ $emergencyCard->allergies ?? 'None Declared' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-[#8D6E63] uppercase tracking-wider">{{ __('Meds') }}</p>
                                <p class="font-semibold text-[#3E2723] mt-0.5 line-clamp-1" title="{{ $emergencyCard->medications }}">{{ $emergencyCard->medications ?? 'None Declared' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-center justify-center p-3 bg-white border border-[#D7CCC8]/60 rounded-2xl shadow-sm shrink-0">
                            <img class="w-24 h-24" src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode(route('emergency.card.view', $emergencyCard->qr_token)) }}" alt="Emergency QR Card">
                            <span class="text-[9px] font-bold text-[#8D6E63] uppercase tracking-wider mt-2">{{ __('Hospital Scan') }}</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-[#D7CCC8]/40 flex items-center justify-between gap-3">
                        <a href="{{ route('emergency.card.view', $emergencyCard->qr_token) }}" target="_blank" class="text-xs font-bold text-[#5D4037] hover:text-[#3E2723] flex items-center gap-1">
                            🔎 {{ __('Open Public View') }}
                        </a>
                        <button onclick="window.print()" class="px-3.5 py-1.5 bg-[#FFF8E1] hover:bg-[#FDFBF7] border border-[#D7CCC8]/60 text-[#5D4037] text-xs font-bold rounded-lg transition shadow-sm active:scale-95 flex items-center gap-1.5">
                            🖨️ {{ __('Print Card') }}
                        </button>
                    </div>
                </div>
                @else
                <div class="bg-white/80 rounded-3xl border border-dashed border-[#D7CCC8] p-6 text-center shadow-sm">
                    <span class="text-3xl">🎴</span>
                    <h3 class="font-bold text-[#3E2723] mt-2">{{ __('Emergency Card is not set up') }}</h3>
                    <p class="text-xs text-[#8D6E63] mt-1 max-w-xs mx-auto">{{ __('Fill in DOB and Blood Group in onboarding or profile settings to trigger emergency card compilation.') }}</p>
                </div>
                @endif

            </div>

            <!-- Right column: Summary Stats, Notifications list, and Recycle Bin Link -->
            <div class="lg:col-span-5 flex flex-col gap-8">
                
                <!-- Quick Stats Board -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4">
                    <h3 class="font-extrabold text-sm uppercase text-[#8D6E63] tracking-wider">{{ __('Health Dashboard Summary') }}</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 rounded-2xl p-4 flex flex-col justify-between">
                            <span class="text-xs text-[#8D6E63] font-bold">{{ __('Clinical Records') }}</span>
                            <span class="text-2xl font-black text-[#3E2723] mt-1">{{ $records->count() }}</span>
                        </div>
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 rounded-2xl p-4 flex flex-col justify-between">
                            <span class="text-xs text-[#8D6E63] font-bold">{{ __('Active Shares') }}</span>
                            <span class="text-2xl font-black text-[#3E2723] mt-1">{{ $activeGrants->count() }}</span>
                        </div>
                    </div>

                    <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-[#3E2723]">{{ __('Profile Completeness') }}</p>
                            <p class="text-[11px] text-[#8D6E63] mt-0.5">{{ __('Wizard progress status') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-full border-4 border-[#EFEBE9] flex items-center justify-center font-bold text-xs text-[#5D4037] relative" style="border-top-color: #5D4037;">
                            {{ $completeness }}%
                        </div>
                    </div>

                    <!-- Shortcut to Recycle Bin -->
                    <a href="{{ route('recycle-bin.index') }}" class="w-full flex items-center justify-between px-4 py-3 bg-[#EFEBE9]/30 hover:bg-[#EFEBE9]/60 border border-[#D7CCC8]/50 rounded-2xl text-xs font-bold text-[#5D4037] transition">
                        <span class="flex items-center gap-2">🗑️ {{ __('Access Recycle Bin') }}</span>
                        <span>&rarr;</span>
                    </a>
                </div>

                <!-- Recent Activity Overview list -->
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-3">
                    <h3 class="font-extrabold text-xs uppercase text-[#8D6E63] tracking-wider mb-1">{{ __('Recent Vault Documents') }}</h3>
                    
                    @forelse($records->take(3) as $rec)
                        <div class="flex items-center justify-between py-2 border-b border-[#D7CCC8]/10 last:border-0">
                            <div class="flex items-center gap-2.5 truncate">
                                <span class="text-base">
                                    @if($rec->type === 'Lab Report') 🩸
                                    @elseif($rec->type === 'Prescription') 💊
                                    @elseif($rec->type === 'Radiology') 🩻
                                    @else 📄 @endif
                                </span>
                                <div class="truncate">
                                    <p class="text-xs font-bold text-[#3E2723] truncate">{{ $rec->title }}</p>
                                    <p class="text-[10px] text-[#8D6E63]">{{ $rec->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <a href="{{ $rec->file_path }}" target="_blank" class="px-2.5 py-1 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[10px] font-bold text-[#5D4037] rounded-lg transition">
                                {{ __('Open') }}
                            </a>
                        </div>
                        @empty
                            <p class="text-xs text-[#8D6E63] py-2">{{ __('No documents stored in vault yet.') }}</p>
                        @endforelse
                </div>

            </div>
        </div>

        <!-- 2. MEDICAL VAULT TAB -->
        <div x-show="activeTab === 'records'" class="flex flex-col gap-6" x-cloak>
            <div x-data="{ viewType: localStorage.getItem('recordView') || 'grid' }" 
                 class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                
                <div class="flex items-center justify-between mb-6 pb-3 border-b border-[#D7CCC8]/40">
                    <div>
                        <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2">
                            <span>📂</span> {{ __('Medical Vault') }}
                        </h3>
                        <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('Upload medical summaries, prescription scans, or test reports.') }}</p>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Toggle Button -->
                        <button @click="viewType = (viewType === 'grid' ? 'list' : 'grid'); localStorage.setItem('recordView', viewType)"
                                class="p-1.5 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] rounded-lg transition text-xs font-bold shadow-sm">
                            <span x-show="viewType === 'grid'">{{ __('List View') }}</span>
                            <span x-show="viewType === 'list'">{{ __('Grid View') }}</span>
                        </button>

                        <a href="{{ route('records.create') }}" class="px-4 py-2 bg-gradient-to-r from-[#5D4037] to-[#3E2723] text-white text-xs font-bold rounded-lg uppercase tracking-wider shadow-sm transition active:scale-95">
                            + {{ __('Add File') }}
                        </a>
                    </div>
                </div>

                @if($records->isEmpty())
                    <div class="p-12 text-center text-sm text-[#8D6E63]">
                        <span class="text-3xl block mb-2">📁</span>
                        {{ __('No documents stored in vault yet.') }}
                    </div>
                @else
                    <!-- Layouts: Masonry Grid or List view -->
                    <div :class="viewType === 'grid' ? 'columns-1 sm:columns-2 lg:columns-3 gap-4 space-y-4' : 'flex flex-col gap-4'">
                        @foreach($records as $rec)
                            <div class="bg-[#FDFBF7]/70 border border-[#D7CCC8]/45 hover:border-[#5D4037]/35 rounded-2xl p-4 transition flex flex-col gap-3 break-inside-avoid relative"
                                 x-data="{ shareOpen: false }">
                                
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-lg p-1.5 bg-[#EFEBE9] rounded-lg border border-[#D7CCC8]/30">
                                            @if($rec->type === 'Lab Report') 🩸
                                            @elseif($rec->type === 'Prescription') 💊
                                            @elseif($rec->type === 'Radiology') 🩻
                                            @else 📄
                                            @endif
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-xs text-[#3E2723] line-clamp-1" title="{{ $rec->title }}">{{ $rec->title }}</h4>
                                            <p class="text-[9px] font-bold text-[#8D6E63] mt-0.5">
                                                {{ $rec->type }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($rec->is_critical)
                                        <span class="text-[8px] font-bold bg-red-50 text-red-700 border border-red-200 px-1.5 py-0.5 rounded-full uppercase shrink-0">
                                            {{ __('Critical') }}
                                        </span>
                                    @endif
                                </div>

                                <p class="text-[11px] text-[#5D4037] leading-relaxed line-clamp-2">{{ $rec->description }}</p>

                                <!-- Quick actions footer -->
                                <div class="border-t border-[#D7CCC8]/30 pt-3 mt-1 flex flex-col gap-2">
                                    <div class="flex justify-between items-center text-[9px] text-[#8D6E63]">
                                        <span>{{ __('Date:') }} {{ $rec->created_at->format('M d, Y') }}</span>
                                        <span>{{ __('By:') }} {{ $rec->creator->name ?? 'Self' }}</span>
                                    </div>

                                    <div class="flex flex-wrap gap-1.5 justify-end">
                                        <a href="{{ $rec->file_path }}" target="_blank" class="px-2 py-1 bg-white hover:bg-[#FDFBF7] border border-[#D7CCC8] text-[9px] font-bold text-[#5D4037] rounded-md transition shadow-sm">
                                            {{ __('Open') }}
                                        </a>
                                        <a href="{{ route('records.edit', $rec->id) }}" class="px-2 py-1 bg-white hover:bg-[#FDFBF7] border border-[#D7CCC8] text-[9px] font-bold text-[#5D4037] rounded-md transition shadow-sm">
                                            {{ __('Edit') }}
                                        </a>
                                        <a href="{{ route('records.versions', $rec->id) }}" class="px-2 py-1 bg-white hover:bg-[#FDFBF7] border border-[#D7CCC8] text-[9px] font-bold text-[#5D4037] rounded-md transition shadow-sm">
                                            {{ __('Versions') }}
                                        </a>
                                        <button @click="shareOpen = !shareOpen" class="px-2 py-1 bg-white hover:bg-[#FDFBF7] border border-[#D7CCC8] text-[9px] font-bold text-[#5D4037] rounded-md transition shadow-sm">
                                            🔗 {{ __('Share') }}
                                        </button>
                                        
                                        <!-- Soft delete -->
                                        <form method="POST" action="{{ route('records.destroy', $rec->id) }}" onsubmit="return confirm('Move record to recycle bin?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-50 hover:bg-red-100 border border-red-100 text-[9px] font-bold text-red-700 rounded-md transition active:scale-95 shadow-sm">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Expiring share link popover/box -->
                                    <div x-show="shareOpen" x-cloak class="mt-2 bg-[#FDFBF7] border border-[#D7CCC8]/60 p-3 rounded-xl flex flex-col gap-2 shadow-inner transition-all duration-300">
                                        <p class="text-[9px] font-bold text-[#8D6E63] uppercase">{{ __('Generate Expiring Link') }}</p>
                                        <form method="POST" action="{{ route('records.share', $rec->id) }}" class="flex flex-col gap-2">
                                            @csrf
                                            <div class="grid grid-cols-2 gap-2">
                                                <select name="expires_in" required class="px-2 py-1 bg-white border border-[#D7CCC8]/60 text-[10px] rounded-lg">
                                                    <option value="1h">1 Hour</option>
                                                    <option value="24h">24 Hours</option>
                                                    <option value="7d">7 Days</option>
                                                </select>
                                                <input type="password" name="password" placeholder="Passcode (Optional)" class="px-2 py-1 bg-white border border-[#D7CCC8]/60 text-[10px] rounded-lg">
                                            </div>
                                            <button type="submit" class="px-3 py-1 bg-[#3E2723] hover:bg-[#5D4037] text-white text-[9px] font-bold rounded-lg transition">
                                                {{ __('Create Link') }}
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

        <!-- 3. CONSENT MANAGER TAB -->
        <div x-show="activeTab === 'consent'" class="flex flex-col gap-6" x-cloak>
            <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-6">
                
                <div>
                    <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2">
                        <span>🔒</span> {{ __('Clinical Consent Manager') }}
                    </h3>
                    <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('Authorise specific doctors to view or edit specific records. Instantly revoke consent permissions or set timers for automatic access expiration.') }}</p>
                </div>

                <!-- A. Pending Requests Queue -->
                <div>
                    <h4 class="text-xs uppercase font-bold text-[#8D6E63] tracking-wider mb-3">{{ __('Incoming Doctor Consent Requests') }} ({{ $pendingRequests->count() }})</h4>
                    @if($pendingRequests->isEmpty())
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-4 text-center text-sm text-[#8D6E63]">
                            {{ __('No pending clinical access requests waiting.') }}
                        </div>
                    @else
                        <div class="flex flex-col gap-3">
                            @foreach($pendingRequests as $req)
                                <div class="bg-amber-50/50 border border-[#D7CCC8]/60 rounded-2xl p-4 flex flex-col gap-3 shadow-sm hover:border-[#5D4037]/40 transition duration-150">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-bold text-[#5D4037] text-sm">{{ $req->doctor->user->name }}</h5>
                                            <p class="text-xs text-[#8D6E63]">{{ $req->doctor->specialization }} &bull; {{ $req->doctor->hospital }}</p>
                                        </div>
                                        <span class="text-[10px] uppercase font-bold bg-[#5D4037]/10 text-[#5D4037] px-2 py-0.5 rounded border border-[#5D4037]/20">
                                            {{ __('Pending Action') }}
                                        </span>
                                    </div>
                                    <div class="bg-white/60 rounded-xl p-3 border border-[#D7CCC8]/30">
                                        <p class="text-xs font-semibold text-[#8D6E63] uppercase tracking-wider">{{ __('Reason for requesting access:') }}</p>
                                        <p class="text-xs text-[#3E2723] mt-1">{{ $req->reason }}</p>
                                    </div>
                                    <div class="text-[10px] text-red-700 font-medium">
                                        ⌛ {{ __('Request will auto-expire on:') }} {{ $req->expires_at->format('M d, Y h:i A') }}
                                    </div>
                                    <div class="flex gap-2 justify-end mt-1">
                                        <form method="POST" action="{{ route('consent.deny', $req->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] text-xs font-bold rounded-lg transition active:scale-95 shadow-sm">
                                                {{ __('Deny Request') }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('consent.approve', $req->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-gradient-to-r from-[#5D4037] to-[#3E2723] text-white hover:from-[#795548] text-xs font-bold rounded-lg transition active:scale-95 shadow-sm">
                                                {{ __('Grant Consent') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- B. Active Consent Grants with Granular Visibility controls -->
                <div>
                    <h4 class="text-xs uppercase font-bold text-[#8D6E63] tracking-wider mb-3">{{ __('Active Doctor Sessions') }} ({{ $activeGrants->count() }})</h4>
                    @if($activeGrants->isEmpty())
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-4 text-center text-sm text-[#8D6E63]">
                            {{ __('You have not authorized any doctors to view your records yet.') }}
                        </div>
                    @else
                        <div class="flex flex-col gap-4">
                            @foreach($activeGrants as $grant)
                                <div class="bg-white border border-[#D7CCC8]/50 rounded-2xl p-4 shadow-sm hover:border-[#D7CCC8] transition duration-150 flex flex-col gap-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h5 class="font-bold text-[#3E2723] text-sm">{{ $grant->doctor->user->name }}</h5>
                                            <p class="text-xs text-[#8D6E63]">{{ $grant->doctor->specialization }} &bull; {{ $grant->doctor->hospital }}</p>
                                            <div class="flex items-center gap-2 mt-1.5">
                                                <span class="text-[10px] uppercase font-bold {{ $grant->access_type === 'emergency-override' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-green-50 text-green-700 border-green-200' }} px-2 py-0.5 rounded border">
                                                    {{ $grant->access_type === 'emergency-override' ? __('Emergency Session') : __('Active Authorized') }}
                                                </span>
                                                <span class="text-[10px] text-[#8D6E63]">
                                                    {{ __('Expires:') }} {{ $grant->expires_at ? $grant->expires_at->diffForHumans() : __('Never') }}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Revoke Access Form -->
                                        <form method="POST" action="{{ route('consent.revoke', $grant->id) }}" onsubmit="return confirm('WARNING: Are you sure you want to revoke this doctor\'s access to your medical records immediately?')">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 text-xs font-bold rounded-lg transition active:scale-95 shadow-sm">
                                                {{ __('Revoke') }}
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Granular Visibility controls -->
                                    @if(!$records->isEmpty())
                                        <div x-data="{ openVisibility: false }" class="border-t border-[#D7CCC8]/30 pt-3">
                                            <button @click="openVisibility = !openVisibility" class="text-[11px] font-bold text-[#8D6E63] hover:text-[#3E2723] flex items-center gap-1">
                                                ⚙️ {{ __('Manage Document Permissions') }}
                                            </button>
                                            
                                            <div x-show="openVisibility" x-cloak class="mt-3 bg-[#FDFBF7] p-3 rounded-xl border border-[#D7CCC8]/40 text-xs transition-all duration-300">
                                                <p class="text-[10px] text-[#8D6E63] mb-2 font-bold uppercase">{{ __('Check documents to HIDE from this doctor:') }}</p>
                                                <form method="POST" action="{{ route('consent.visibility', $grant->id) }}" class="flex flex-col gap-2">
                                                    @csrf
                                                    <div class="flex flex-col gap-1.5 max-h-[140px] overflow-y-auto pr-1">
                                                        @foreach($records as $rec)
                                                            <label class="flex items-center gap-2 cursor-pointer py-0.5 hover:bg-[#EFEBE9]/30 rounded px-1">
                                                                <input type="checkbox" name="restricted_record_ids[]" value="{{ $rec->id }}"
                                                                       @checked($grant->isRecordRestricted($rec->id))
                                                                       class="rounded text-[#3E2723] focus:ring-[#5D4037] border-[#D7CCC8]/60">
                                                                <span class="truncate text-[#5D4037]">{{ $rec->title }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    <button type="submit" class="mt-2.5 px-3 py-1.5 bg-[#3E2723] text-white hover:bg-[#5D4037] text-[10px] font-bold rounded-lg transition active:scale-95">
                                                        {{ __('Apply Exclusions') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- 4. SECURITY AUDITS TAB -->
        <div x-show="activeTab === 'audits'" class="flex flex-col gap-6" x-cloak>

            <!-- LOGIN HISTORY (top) -->
            <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4 border-b border-[#D7CCC8]/40 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2">
                            <span>🖥️</span> {{ __('Login Session History') }}
                        </h3>
                        <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('Recent active login sessions for your account.') }}</p>
                    </div>
                    <a href="{{ route('profile.sessions') }}" class="text-xs font-bold text-[#5D4037] hover:text-[#3E2723] border border-[#D7CCC8]/60 px-3 py-1.5 rounded-xl hover:bg-[#FDFBF7] transition flex items-center gap-1">
                        {!! __('Manage Sessions &rarr;') !!}
                    </a>
                </div>

                @if($loginSessions->isEmpty())
                    <p class="text-xs text-[#8D6E63] text-center py-4">{{ __('No active sessions recorded.') }}</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($loginSessions as $session)
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

            <!-- ACCESS AUDIT LOGS (with filters) -->
            <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4">
                <div class="flex items-center justify-between border-b border-[#D7CCC8]/40 pb-3">
                    <div>
                        <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2">
                            <span>🕵️‍♂️</span> {{ __('Security Access Audits') }}
                            <span class="text-xs font-bold bg-[#EFEBE9] text-[#5D4037] px-2 py-0.5 rounded-full border border-[#D7CCC8]/60">{{ $accessLogs->count() }}</span>
                        </h3>
                        <p class="text-xs text-[#8D6E63] mt-0.5">{{ __('Every view, download, and emergency scan of your records is logged here.') }}</p>
                    </div>
                    @if($auditSearch || $auditAction || $auditUnusual)
                        <a href="{{ route('dashboard', ['tab' => 'audits']) }}" class="text-xs text-[#5D4037] hover:text-[#3E2723] font-bold border border-[#D7CCC8]/60 px-3 py-1.5 rounded-xl hover:bg-[#FDFBF7] transition shrink-0">
                            ✕ {{ __('Clear') }}
                        </a>
                    @endif
                </div>

                <!-- Filter Bar -->
                <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row gap-3">
                    <input type="hidden" name="tab" value="audits">
                    <div class="relative flex-grow">
                        <input type="text" name="audit_search" value="{{ $auditSearch ?? '' }}"
                               placeholder="{{ __('Search by action or IP...') }}"
                               class="w-full pl-9 pr-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition">
                        <span class="absolute left-3 top-3 text-[#8D6E63] text-xs">🔍</span>
                    </div>
                    <select name="audit_action" class="px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] focus:border-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-xl transition min-w-[160px]">
                        <option value="">{{ __('All Actions') }}</option>
                        @foreach($allPatientAuditActions as $actionOpt)
                            <option value="{{ $actionOpt }}" @selected($auditAction === $actionOpt)>{{ $actionOpt }}</option>
                        @endforeach
                    </select>
                    <label class="flex items-center gap-2 px-3 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] rounded-xl cursor-pointer hover:bg-red-50 hover:border-red-200 transition shrink-0">
                        <input type="checkbox" name="audit_unusual" value="1" @checked($auditUnusual === '1') class="rounded border-[#D7CCC8] text-red-600">
                        <span class="text-xs font-bold text-red-700">⚠️ {{ __('Unusual') }}</span>
                    </label>
                    <button type="submit" class="px-5 py-2.5 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold text-xs rounded-xl transition shadow-sm active:scale-95 shrink-0">
                        {{ __('Filter') }}
                    </button>
                </form>

                @if($auditSearch || $auditAction || $auditUnusual)
                    <div class="flex flex-wrap gap-2">
                        @if($auditSearch)<span class="text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-full">🔍 "{{ $auditSearch }}"</span>@endif
                        @if($auditAction)<span class="text-[10px] font-bold bg-[#EFEBE9] text-[#5D4037] border border-[#D7CCC8]/60 px-2.5 py-1 rounded-full">⚙️ {{ $auditAction }}</span>@endif
                        @if($auditUnusual === '1')<span class="text-[10px] font-bold bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-full">⚠️ Unusual Only</span>@endif
                    </div>
                @endif

                @if($accessLogs->isEmpty())
                    <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-6 text-center text-sm text-[#8D6E63]">
                        {{ $auditSearch || $auditAction || $auditUnusual
                            ? __('No log entries match your filters.')
                            : __('No log events captured yet. Logs appear when doctors access or request your records.') }}
                    </div>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-[#D7CCC8]/40">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-[#EFEBE9]/60 border-b border-[#D7CCC8] text-[#8D6E63] uppercase font-bold tracking-wider">
                                    <th class="py-3 px-4">{{ __('Action') }}</th>
                                    <th class="py-3 px-4">{{ __('By') }}</th>
                                    <th class="py-3 px-4">{{ __('IP Address') }}</th>
                                    <th class="py-3 px-4">{{ __('Time') }}</th>
                                    <th class="py-3 px-4">{{ __('Flag') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#D7CCC8]/30">
                                @foreach($accessLogs as $log)
                                    <tr class="hover:bg-[#FDFBF7] transition-colors duration-100 {{ $log->unusual_activity ? 'bg-red-50/40' : '' }}">
                                        <td class="py-3 px-4 font-bold text-[#3E2723]">{{ $log->action }}</td>
                                        <td class="py-3 px-4">
                                            <p class="font-semibold text-[#3E2723]">{{ $log->user->name ?? __('System') }}</p>
                                            @if($log->user)
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded border uppercase
                                                    {{ $log->user->role === 'Doctor' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-[#EFEBE9] text-[#5D4037] border-[#D7CCC8]/40' }}">
                                                    {{ $log->user->role }}
                                                </span>
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

        <!-- 5. CLINICAL CHAT TAB -->
        <div x-show="activeTab === 'chat'" class="flex flex-col gap-6" x-cloak>
            @php
                $chatDoctors = collect();
                foreach($activeGrants as $grant) {
                    if ($grant->doctor) {
                        $chatDoctors->put((string)$grant->doctor->id, [
                            'doctor' => $grant->doctor,
                            'status' => 'Authorized Access',
                            'status_color' => 'bg-green-50 text-green-700 border-green-200'
                        ]);
                    }
                }
                foreach($pendingRequests as $req) {
                    if ($req->doctor) {
                        $chatDoctors->put((string)$req->doctor->id, [
                            'doctor' => $req->doctor,
                            'status' => 'Pending Request',
                            'status_color' => 'bg-amber-50 text-amber-700 border-amber-200'
                        ]);
                    }
                }
            @endphp

            <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm flex flex-col gap-4">
                <div>
                    <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2">
                        <span>💬</span> {{ __('Clinical Connections & Chats') }}
                    </h3>
                    <p class="text-xs text-[#8D6E63] mt-0.5 font-medium">{{ __('Select an authorized practitioner to start a secure, encrypted clinical chat.') }}</p>
                </div>

                @if($chatDoctors->isEmpty())
                    <div class="bg-[#FDFBF7] border border-[#D7CCC8]/40 rounded-2xl p-8 text-center text-sm text-[#8D6E63]">
                        {{ __('No active clinical connections found. Authorized doctors will appear here.') }}
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($chatDoctors as $docData)
                            @php
                                $doc = $docData['doctor'];
                            @endphp
                            <div class="bg-white border border-[#D7CCC8]/50 hover:border-[#D7CCC8] rounded-2xl p-4 flex justify-between items-center gap-4 shadow-sm transition duration-150">
                                <div>
                                    <h5 class="font-bold text-[#3E2723] text-sm">{{ $doc->user->name }}</h5>
                                    <p class="text-xs text-[#8D6E63]">{{ $doc->specialization }} &bull; {{ $doc->hospital }}</p>
                                    <span class="inline-block mt-2 text-[10px] font-extrabold px-2 py-0.5 rounded border {{ $docData['status_color'] }} uppercase">
                                        {{ __($docData['status']) }}
                                    </span>
                                </div>
                                <a href="{{ route('chat.show', $doc->user->id) }}" class="px-4 py-2.5 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow active:scale-95 flex items-center gap-1.5 shrink-0">
                                    <span>💬</span> {{ __('Chat') }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-app-layout>
