<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="font-extrabold text-2xl text-[#3E2723] leading-tight flex items-center gap-2">
                    <span>🎫</span> {{ __('Contact Support & Admin') }}
                </h2>
                <p class="text-sm text-[#8D6E63] mt-1">{{ __('Submit queries, report system issues, or request help directly from the platform administrator.') }}</p>
            </div>
            <div>
                <span class="text-xs bg-[#EFEBE9] text-[#5D4037] font-bold px-3 py-1.5 rounded-full border border-[#D7CCC8]/80">
                    {{ __('Help Desk') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Status Toast Alert -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-center justify-between text-green-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-800 font-bold text-sm">Dismiss</button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- LEFT: Ticket Submission Form -->
            <div class="lg:col-span-5">
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                    <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2 mb-4 border-b border-[#D7CCC8]/40 pb-3">
                        <span>✉️</span> {{ __('New Ticket Submission') }}
                    </h3>

                    <form method="POST" action="{{ route('support.store') }}" class="flex flex-col gap-4">
                        @csrf

                        <div>
                            <label for="subject" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">{{ __('Subject / Topic') }}</label>
                            <input type="text" name="subject" id="subject" required placeholder="{{ __('e.g., File load latency, Consent error') }}"
                                   class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition">
                            @error('subject')
                                <span class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-xs font-bold text-[#8D6E63] uppercase tracking-wider mb-2">{{ __('Detailed Description') }}</label>
                            <textarea name="message" id="message" rows="6" required placeholder="{{ __('Provide as much detail as possible to help the administrator troubleshoot...') }}"
                                      class="w-full px-4 py-3 bg-[#FDFBF7] border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-sm font-medium text-[#3E2723] transition"></textarea>
                            @error('message')
                                <span class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-3 mt-2 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold rounded-xl transition active:scale-95 shadow-md uppercase tracking-wider text-xs">
                            {{ __('Submit Ticket') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- RIGHT: Ticket History Log -->
            <div class="lg:col-span-7">
                <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-6 shadow-sm">
                    <h3 class="font-extrabold text-base text-[#3E2723] flex items-center gap-2 mb-4 border-b border-[#D7CCC8]/40 pb-3">
                        <span>📜</span> {{ __('Your Past Tickets & Responses') }}
                    </h3>

                    @if($tickets->isEmpty())
                        <div class="py-12 text-center text-sm text-[#8D6E63]">
                            <span class="text-3xl block mb-2">📥</span>
                            {{ __('No support tickets submitted yet. Fill out the form on the left to reach out.') }}
                        </div>
                    @else
                        <div class="flex flex-col gap-4 max-h-[600px] overflow-y-auto pr-1">
                            @foreach($tickets as $ticket)
                                <div class="bg-[#FDFBF7] border border-[#D7CCC8]/50 rounded-2xl p-5 shadow-xs transition duration-150 hover:border-[#D7CCC8]">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="font-bold text-[#3E2723] text-sm">{{ $ticket->subject }}</h4>
                                        <span class="text-[10px] uppercase font-extrabold px-2.5 py-0.5 rounded-full border {{ $ticket->status === 'resolved' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                                            {{ __($ticket->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-[#5D4037] whitespace-pre-wrap leading-relaxed">{{ $ticket->message }}</p>
                                    
                                    <div class="text-[10px] text-[#8D6E63] mt-3 font-semibold">
                                        {{ __('Submitted:') }} {{ $ticket->created_at->diffForHumans() }}
                                    </div>

                                    <!-- Admin response wrapper -->
                                    @if($ticket->reply)
                                        <div class="mt-4 p-4 bg-white border border-green-100 rounded-xl relative">
                                            <div class="absolute right-3 top-3 text-lg">🛡️</div>
                                            <h5 class="text-[10px] uppercase tracking-wider font-extrabold text-green-700 mb-1.5">{{ __('Administrator Response') }}</h5>
                                            <p class="text-xs text-[#3E2723] whitespace-pre-wrap leading-relaxed">{{ $ticket->reply }}</p>
                                            <div class="text-[9px] text-[#8D6E63] mt-2 font-medium">
                                                {{ __('Replied:') }} {{ $ticket->replied_at ? $ticket->replied_at->diffForHumans() : '' }}
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
    </div>
</x-app-layout>
