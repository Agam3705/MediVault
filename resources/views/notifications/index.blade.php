<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-[#3E2723] leading-tight">
                🔔 Notification Center
            </h2>
            @if(\App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->exists())
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-[#3E2723] hover:bg-[#5D4037] text-white text-xs font-bold rounded-xl transition shadow-sm active:scale-95">
                        Mark All Read
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl sm:rounded-3xl p-6">
                @if($notifications->isEmpty())
                    <div class="text-center py-12 bg-[#FDFBF7] rounded-3xl border border-dashed border-[#D7CCC8] flex flex-col items-center">
                        <span class="text-4xl mb-3">🔔</span>
                        <h3 class="text-base font-bold text-[#3E2723]">No notifications yet</h3>
                        <p class="text-xs text-[#8D6E63] mt-1">Alerts regarding your profile updates, record uploads, and doctor consent triggers will appear here.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($notifications as $notif)
                            <div class="border rounded-2xl p-5 transition {{ is_null($notif->read_at) ? 'bg-[#FFF8E1]/40 border-yellow-200' : 'bg-[#FDFBF7]/60 border-[#D7CCC8]/40' }}">
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        <h4 class="font-bold text-sm text-[#3E2723]">{{ $notif->title }}</h4>
                                        <p class="text-xs text-[#5D4037] mt-1 leading-relaxed">{{ $notif->message }}</p>
                                    </div>
                                    <span class="text-[10px] text-[#8D6E63] font-semibold shrink-0">
                                        {{ $notif->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
