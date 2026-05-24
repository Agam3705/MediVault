<x-guest-layout>
    <div class="min-h-screen bg-[#FDFBF7] flex flex-col justify-center items-center px-4">
        <div class="w-full max-w-2xl bg-white border border-[#D7CCC8]/60 rounded-3xl p-8 shadow-xl">
            <div class="flex justify-between items-start gap-4 border-b border-[#D7CCC8]/30 pb-4 mb-6">
                <div>
                    <span class="text-xs font-bold text-[#8D6E63] uppercase tracking-wider bg-[#EFEBE9] px-2.5 py-1 rounded-full border border-[#D7CCC8]/30">
                        {{ $record->type }}
                    </span>
                    <h2 class="text-2xl font-bold text-[#3E2723] mt-2">{{ $record->title }}</h2>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-red-600 bg-red-50 border border-red-100 px-2 py-0.5 rounded-full uppercase">
                        Expiring Link
                    </p>
                    <p class="text-[9px] text-[#8D6E63] mt-1">Expires: {{ $share->expires_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <h4 class="text-xs font-bold text-[#8D6E63] uppercase mb-2">Description / Notes</h4>
                <p class="text-sm text-[#5D4037] leading-relaxed bg-[#FDFBF7] p-4 rounded-2xl border border-[#D7CCC8]/20">
                    {{ $record->description }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 items-center justify-between border-t border-[#D7CCC8]/30 pt-6 mt-6">
                <span class="text-xs text-[#8D6E63]">
                    Accessed {{ $share->access_count }} times. Shared by verified MediVault user.
                </span>

                <div class="flex gap-3">
                    @if($record->file_path)
                        <a href="{{ $record->file_path }}" target="_blank" 
                           class="px-5 py-2.5 bg-[#3E2723] hover:bg-[#5D4037] text-white text-sm font-bold rounded-xl transition active:scale-95 shadow-md flex items-center gap-2">
                            <span>📄</span> View Clinical Document
                        </a>
                    @else
                        <span class="text-xs font-semibold text-[#8D6E63] italic">No document file attached</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
