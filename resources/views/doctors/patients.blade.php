<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                    <span>🩺</span> {{ __('My Patients') }}
                </h2>
                <p class="text-sm text-[#8D6E63] mt-1">{{ __('All patients you have ever treated or accessed records for, including past and current cases.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    {{ $totalPatients }} {{ __('Total Patients') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="mb-6">
            <form method="GET" action="{{ route('doctors.my-patients') }}" class="flex flex-col md:flex-row gap-3">
                <div class="relative flex-grow">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search by patient name or email..."
                           class="w-full pl-10 pr-4 py-3 border border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-white/80 text-xs font-semibold text-[#3E2723] rounded-2xl shadow-sm transition">
                    <span class="absolute left-3.5 top-3.5 text-gray-400">🔍</span>
                </div>
                <select name="status" class="px-4 py-3 bg-white/80 border border-[#D7CCC8] focus:border-[#5D4037] text-xs font-semibold text-[#3E2723] rounded-2xl shadow-sm transition">
                    <option value="">{{ __('All Access Status') }}</option>
                    <option value="active" @selected(($status ?? '') === 'active')>{{ __('Currently Active') }}</option>
                    <option value="expired" @selected(($status ?? '') === 'expired')>{{ __('Expired / Past') }}</option>
                </select>
                <button type="submit" class="px-6 py-3 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold text-xs rounded-2xl transition shadow-md active:scale-95">
                    {{ __('Filter') }}
                </button>
                @if($search || $status)
                    <a href="{{ route('doctors.my-patients') }}" class="px-5 py-3 bg-white border border-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-2xl text-center flex items-center justify-center shadow-sm">
                        {{ __('Clear') }}
                    </a>
                @endif
            </form>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-[#5D4037] to-[#3E2723] rounded-2xl p-4 text-[#FFF8E1] shadow-md">
                <div class="text-2xl font-extrabold">{{ $totalPatients }}</div>
                <div class="text-[10px] uppercase font-bold text-[#D7CCC8] mt-0.5 tracking-wider">{{ __('Total Patients') }}</div>
            </div>
            <div class="bg-gradient-to-br from-green-700 to-green-900 rounded-2xl p-4 text-white shadow-md">
                <div class="text-2xl font-extrabold">{{ $activeCount }}</div>
                <div class="text-[10px] uppercase font-bold text-green-200 mt-0.5 tracking-wider">{{ __('Active Access') }}</div>
            </div>
            <div class="bg-gradient-to-br from-[#795548] to-[#5D4037] rounded-2xl p-4 text-[#FFF8E1] shadow-md">
                <div class="text-2xl font-extrabold">{{ $expiredCount }}</div>
                <div class="text-[10px] uppercase font-bold text-[#D7CCC8] mt-0.5 tracking-wider">{{ __('Past Cases') }}</div>
            </div>
            <div class="bg-gradient-to-br from-amber-600 to-amber-800 rounded-2xl p-4 text-white shadow-md">
                <div class="text-2xl font-extrabold">{{ $totalRecordsViewed }}</div>
                <div class="text-[10px] uppercase font-bold text-amber-200 mt-0.5 tracking-wider">{{ __('Records Accessed') }}</div>
            </div>
        </div>

        <!-- Patients Grid -->
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
            <h3 class="font-extrabold text-lg text-[#3E2723] flex items-center gap-2 mb-6 border-b border-[#D7CCC8]/40 pb-3">
                <span>📋</span> {{ __('Patient Registry') }}
            </h3>

            @if($allPatients->isEmpty())
                <div class="py-16 text-center">
                    <span class="text-4xl block mb-3">🩺</span>
                    <h4 class="font-bold text-[#3E2723] text-sm">{{ __('No patient records found') }}</h4>
                    <p class="text-xs text-[#8D6E63] mt-1">{{ __('Patients you have been granted access to will appear here.') }}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($allPatients as $entry)
                        @php
                            $pat = $entry['patient'];
                            $grant = $entry['grant'];
                            $isActive = $entry['is_active'];
                        @endphp
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 hover:border-[#5D4037]/30 hover:shadow-md rounded-2xl p-4 flex flex-col gap-3 shadow-sm transition duration-200">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-bold text-[#3E2723] text-sm">{{ $pat->user->name }}</h4>
                                    <p class="text-xs text-[#8D6E63] mt-0.5">{{ $pat->user->email }}</p>
                                </div>
                                @if($isActive)
                                    <span class="text-[9px] uppercase font-bold bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full shrink-0">
                                        {{ __('Active') }}
                                    </span>
                                @else
                                    <span class="text-[9px] uppercase font-bold bg-[#EFEBE9] text-[#8D6E63] border border-[#D7CCC8]/60 px-2 py-0.5 rounded-full shrink-0">
                                        {{ __('Past Case') }}
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 gap-x-3 gap-y-1.5 text-xs">
                                <div>
                                    <span class="text-[#8D6E63] font-semibold">{{ __('Blood Group:') }}</span>
                                    <span class="font-bold text-red-600 ml-1">{{ $pat->blood_group ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-[#8D6E63] font-semibold">{{ __('Gender:') }}</span>
                                    <span class="font-bold text-[#3E2723] ml-1">{{ $pat->gender ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-[#8D6E63] font-semibold">{{ __('Age:') }}</span>
                                    <span class="font-bold text-[#3E2723] ml-1">{{ $pat->dob ? $pat->dob->age . ' yrs' : 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-[#8D6E63] font-semibold">{{ __('Phone:') }}</span>
                                    <span class="font-bold text-[#3E2723] ml-1">{{ $pat->phone ?? 'N/A' }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-[#8D6E63] font-semibold">{{ __('Access Period:') }}</span>
                                    <span class="font-medium text-[#3E2723] ml-1">
                                        {{ $grant->granted_at ? $grant->granted_at->format('M d, Y') : 'N/A' }}
                                        @if($grant->expires_at)
                                            → {{ $grant->expires_at->format('M d, Y') }}
                                        @else
                                            → {{ __('Unlimited') }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="flex gap-2 pt-2 border-t border-[#D7CCC8]/30">
                                @if($isActive)
                                    <a href="{{ route('records.patient', $pat->id) }}" class="flex-grow text-center px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow active:scale-[0.98]">
                                        {!! __('Open Records &rarr;') !!}
                                    </a>
                                    <a href="{{ route('chat.show', $pat->user->id) }}" class="px-4 py-2 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-xl transition shadow active:scale-[0.98] flex items-center justify-center shrink-0" title="Chat">
                                        <span>💬</span>
                                    </a>
                                @else
                                    <a href="{{ route('chat.show', $pat->user->id) }}" class="flex-grow text-center px-4 py-2 bg-[#EFEBE9] hover:bg-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-xl transition shadow active:scale-[0.98]">
                                        💬 {{ __('Message Patient') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
