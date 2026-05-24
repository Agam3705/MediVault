<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-[#3E2723] leading-tight">
                🗑️ Deleted Records Archive (Recycle Bin)
            </h2>
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-[#EFEBE9] hover:bg-[#D7CCC8]/60 text-xs font-bold text-[#5D4037] border border-[#D7CCC8] rounded-xl transition">
                &larr; Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl sm:rounded-3xl p-6">
                <p class="text-sm text-[#8D6E63] mb-6">Here are the records you have soft-deleted. You can restore them to your active list at any time, along with their full version history intact.</p>

                @if($records->isEmpty())
                    <div class="text-center py-12 bg-[#FDFBF7] rounded-3xl border border-dashed border-[#D7CCC8] flex flex-col items-center">
                        <span class="text-4xl mb-3">🍃</span>
                        <h3 class="text-base font-bold text-[#3E2723]">Recycle Bin is empty</h3>
                        <p class="text-xs text-[#8D6E63] mt-1">Deleted records will appear here.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($records as $rec)
                            <div class="bg-[#FDFBF7] border border-[#D7CCC8]/60 hover:border-[#5D4037]/50 rounded-2xl p-5 transition flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl p-2 bg-[#EFEBE9] rounded-xl border border-[#D7CCC8]/30">
                                                @if($rec->type === 'Lab Report') 🩸
                                                @elseif($rec->type === 'Prescription') 💊
                                                @elseif($rec->type === 'Radiology') 🩻
                                                @else 📄
                                                @endif
                                            </span>
                                            <div>
                                                <h4 class="font-bold text-sm text-[#3E2723]">{{ $rec->title }}</h4>
                                                <p class="text-[10px] text-[#8D6E63] mt-0.5">{{ $rec->type }} &bull; Deleted on {{ $rec->deleted_at->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-[#5D4037] mt-3 leading-relaxed">{{ Str::limit($rec->description, 150) }}</p>
                                </div>

                                <div class="border-t border-[#D7CCC8]/30 pt-4 mt-4 flex justify-between items-center">
                                    <span class="text-[10px] text-[#8D6E63]">Deleted by: Self</span>
                                    
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('recycle-bin.restore', $rec->id) }}">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition active:scale-95 shadow-sm">
                                                Restore Record
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('recycle-bin.force-delete', $rec->id) }}" onsubmit="return confirm('⚠️ WARNING: This action cannot be undone. This record and all its version history will be permanently deleted. Do you want to proceed?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3.5 py-1.5 bg-red-700 hover:bg-red-800 text-white text-xs font-bold rounded-xl transition active:scale-95 shadow-sm">
                                                Permanently Delete
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
    </div>
</x-app-layout>
