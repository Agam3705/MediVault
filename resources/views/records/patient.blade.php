<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="p-2.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] rounded-xl transition font-extrabold flex items-center justify-center shrink-0 w-10 h-10" title="{{ __('Back') }}">
                    &larr;
                </a>
                <div>
                    <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                        <span>📂</span> Patient Clinical Records
                    </h2>
                    <p class="text-sm text-[#8D6E63] mt-1">Viewing records for <span class="font-bold text-[#5D4037]">{{ $patient->user->name }}</span> — {{ $patient->user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs bg-green-50 text-green-700 font-bold px-3 py-1.5 rounded-full border border-green-200 uppercase">
                    {{ $grant->access_type ?? 'Read-Only' }} Access
                </span>
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    Exp: {{ $grant->expires_at ? $grant->expires_at->format('M d, Y') : 'Unlimited' }}
                </span>
                @if(auth()->user()->role === 'Doctor')
                    <a href="{{ route('records.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-1.5 bg-gradient-to-r from-[#5D4037] to-[#3E2723] hover:from-[#795548] text-white text-xs font-bold rounded-xl transition shadow active:scale-95">
                        + {{ __('Add New Report') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Patient Summary Card -->
        <div class="bg-gradient-to-br from-[#5D4037] to-[#3E2723] rounded-3xl p-6 text-[#FFF8E1] shadow-lg mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase font-extrabold tracking-widest text-[#D7CCC8]">Patient Profile</span>
                    <h3 class="text-xl font-bold mt-1">{{ $patient->user->name }}</h3>
                    <p class="text-sm text-[#D7CCC8] mt-0.5">
                        Age: {{ $patient->dob ? $patient->dob->age : 'N/A' }} &bull;
                        Gender: {{ $patient->gender ?? 'N/A' }} &bull;
                        Blood: <span class="font-bold text-[#FFF8E1]">{{ $patient->blood_group ?? 'N/A' }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[#D7CCC8]">Phone: {{ $patient->phone ?? 'Not provided' }}</p>
                    <p class="text-xs text-[#D7CCC8] mt-0.5">Emergency Contact: {{ $patient->emergency_contact_name ?? 'Not set' }}</p>
                </div>
            </div>
        </div>

        <!-- Records List -->
        @if($records->isEmpty())
            <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-12 text-center shadow-sm">
                <span class="text-4xl block mb-3">📁</span>
                <h3 class="font-bold text-[#3E2723] text-lg">No Records Available</h3>
                <p class="text-sm text-[#8D6E63] mt-1 max-w-md mx-auto">This patient has not uploaded any clinical documents yet, or all records have been restricted from your access.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($records as $rec)
                    <div class="bg-white/80 backdrop-blur-md border border-[#D7CCC8]/50 hover:border-[#5D4037]/30 rounded-2xl p-5 transition duration-150 shadow-sm flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-3">
                                <span class="text-xl p-2 bg-[#EFEBE9] rounded-xl border border-[#D7CCC8]/30 shrink-0">
                                    @if($rec->type === 'Lab Report') 🩸
                                    @elseif($rec->type === 'Prescription') 💊
                                    @elseif($rec->type === 'Radiology') 🩻
                                    @elseif($rec->type === 'Vaccination') 💉
                                    @else 📄
                                    @endif
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-[#3E2723]">{{ $rec->title }}</h4>
                                    <p class="text-[10px] font-semibold text-[#8D6E63] mt-0.5">{{ $rec->type }} &bull; {{ $rec->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @if($rec->is_critical)
                                <span class="text-[9px] font-bold bg-red-50 text-red-700 border border-red-200 px-2 py-0.5 rounded-full uppercase shrink-0">Critical</span>
                            @endif
                        </div>

                        <p class="text-xs text-[#5D4037]">{{ $rec->description }}</p>

                        <div class="flex justify-between items-center border-t border-[#D7CCC8]/30 pt-3 mt-auto">
                            <span class="text-[10px] text-[#8D6E63]">Added by: {{ $rec->creator->name ?? 'Patient' }} &bull; {{ $rec->created_at->diffForHumans() }}</span>
                            <div class="flex items-center gap-1.5">
                                @if(auth()->user()->role === 'Doctor')
                                    <a href="{{ route('records.edit', $rec->id) }}" class="px-2.5 py-1.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] text-[10px] font-bold rounded-lg transition shadow-sm active:scale-95">
                                        {{ __('Edit') }}
                                    </a>
                                @endif
                                @if($rec->file_path)
                                    <a href="{{ $rec->file_path }}" target="_blank" class="px-3 py-1.5 bg-gradient-to-r from-[#5D4037] to-[#3E2723] hover:from-[#795548] text-white text-[10px] font-bold rounded-lg transition shadow-sm active:scale-95">
                                        {{ __('Open File ↗') }}
                                    </a>
                                @else
                                    <span class="text-[10px] text-[#BCAAA4]">{{ __('No file') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
