<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-[#3E2723] leading-tight">
            🛡️ Security: Active Login Sessions
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-[#D7CCC8]/60 overflow-hidden shadow-xl sm:rounded-3xl p-6">
                <p class="text-sm text-[#8D6E63] mb-6">Manage and audit your active logins across multiple web browsers and mobile devices. You can immediately terminate any session remotely.</p>

                <div class="flex flex-col gap-4">
                    @foreach($sessions as $session)
                        <div class="bg-[#FDFBF7] border border-[#D7CCC8]/60 rounded-2xl p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl p-2 bg-[#EFEBE9] rounded-xl border border-[#D7CCC8]/30">
                                    {{ $session->device === 'Mobile Device' ? '📱' : ($session->device === 'Tablet Device' ? '📟' : '💻') }}
                                </span>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-sm text-[#3E2723]">{{ $session->device }}</h4>
                                        @if($session->session_token === $currentSessionToken)
                                            <span class="text-[9px] font-bold bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full uppercase">
                                                This Session
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#8D6E63] mt-0.5">
                                        IP Address: {{ $session->ip_address }} &bull; Last Active: {{ $session->last_active_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('profile.sessions.revoke') }}" onsubmit="return confirm('Are you sure you want to revoke and terminate this session? Devices using it will be logged out.')">
                                @csrf
                                <input type="hidden" name="session_id" value="{{ $session->id }}">
                                <button type="submit" class="px-4 py-2 border border-red-200 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-xl transition active:scale-95 shadow-sm">
                                    Terminate Session
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
