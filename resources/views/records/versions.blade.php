<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ auth()->user()->role === 'Doctor' ? route('records.patient', $record->patient_id) : route('dashboard') }}" class="p-2.5 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] rounded-xl transition font-extrabold flex items-center justify-center shrink-0 w-10 h-10" title="{{ __('Back') }}">
                &larr;
            </a>
            <h2 class="font-bold text-xl text-[#3E2723] leading-tight">
                📜 Version History: {{ $record->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl sm:rounded-3xl p-6">
                <p class="text-sm text-[#8D6E63] mb-6">Each time you edit this record, a snapshot of its previous state is captured for audit trails and restoration. Here is the historical version log.</p>

                @if($versions->isEmpty())
                    <div class="text-center py-12 bg-[#FDFBF7] rounded-3xl border border-dashed border-[#D7CCC8] flex flex-col items-center">
                        <span class="text-4xl mb-3">📄</span>
                        <h3 class="text-base font-bold text-[#3E2723]">No previous versions found</h3>
                        <p class="text-xs text-[#8D6E63] mt-1">This record is in its original state and has not been modified.</p>
                    </div>
                @else
                    <div class="relative border-l-2 border-[#D7CCC8] ml-4 md:ml-6 flex flex-col gap-6">
                        @foreach($versions as $index => $ver)
                            <div class="mb-4 ml-6 relative">
                                <!-- Timeline Bullet -->
                                <span class="absolute -left-[31px] top-1.5 flex items-center justify-center w-4.5 h-4.5 rounded-full bg-[#3E2723] border-4 border-white text-white"></span>
                                
                                <div class="bg-[#FDFBF7] border border-[#D7CCC8]/60 rounded-2xl p-5 shadow-sm">
                                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2 mb-3">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#8D6E63] uppercase tracking-wider bg-[#EFEBE9] px-2 py-0.5 rounded border border-[#D7CCC8]/30">
                                                Snapshot #{{ $versions->count() - $index }}
                                            </span>
                                            <h4 class="font-bold text-sm text-[#3E2723] mt-1">{{ $ver->snapshot_json['title'] ?? 'Untitled Record' }}</h4>
                                        </div>
                                        <p class="text-[10px] font-semibold text-[#8D6E63] shrink-0">
                                            {{ $ver->created_at->format('M d, Y \a\t H:i:s') }}
                                        </p>
                                    </div>

                                    <p class="text-xs text-[#5D4037] leading-relaxed mb-4">
                                        {{ $ver->snapshot_json['description'] ?? 'No description snapshot' }}
                                    </p>

                                    <div class="text-[10px] border-t border-[#D7CCC8]/30 pt-3 mt-1 text-[#8D6E63] flex justify-between items-center">
                                        <span>Change Event: <strong class="text-[#3E2723]">{{ $ver->change_note }}</strong></span>
                                        <span>Editor: {{ $ver->editor->name ?? 'Self' }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
