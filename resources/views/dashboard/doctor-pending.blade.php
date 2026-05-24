<x-app-layout>
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white/80 backdrop-blur-md rounded-3xl border border-[#D7CCC8]/60 p-8 shadow-md text-center relative overflow-hidden">
            <!-- Ambient top color banner -->
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 to-[#5D4037]"></div>

            <div class="w-20 h-20 bg-amber-50 border border-amber-200 text-amber-500 rounded-2xl flex items-center justify-center text-4xl mx-auto mb-6 shadow-sm animate-pulse">
                ⏳
            </div>

            <span class="text-xs uppercase font-extrabold tracking-widest text-[#8D6E63] block">Credentials Holding Area</span>
            <h2 class="font-extrabold text-2xl text-[#3E2723] mt-2">Professional License Verification Pending</h2>
            <p class="text-sm text-[#8D6E63] mt-2 max-w-lg mx-auto">
                Welcome, <span class="font-bold text-[#5D4037]">{{ $user->name }}</span>. To protect patient privacy and comply with clinical security policies, your professional license must be verified before you can access clinical search and submit consent requests.
            </p>

            <!-- Current credentials summary box -->
            <div class="mt-8 bg-[#FDFBF7] border border-[#D7CCC8]/50 rounded-2xl p-6 text-left max-w-md mx-auto">
                <h3 class="text-xs uppercase font-bold text-[#8D6E63] tracking-wider mb-4 border-b border-[#D7CCC8]/40 pb-2">Submitted Profile Details</h3>
                
                <div class="flex flex-col gap-3.5 text-xs text-[#5D4037]">
                    <div class="flex justify-between">
                        <span class="font-semibold text-[#8D6E63]">Medical License No:</span>
                        <span class="font-bold text-[#3E2723] bg-[#EFEBE9] px-2 py-0.5 rounded font-mono">{{ $doctor->license_number ?? 'Not provided yet' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-[#8D6E63]">Specialization:</span>
                        <span class="font-bold text-[#3E2723]">{{ $doctor->specialization ?? 'Not provided yet' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-[#8D6E63]">Affiliated Hospital:</span>
                        <span class="font-bold text-[#3E2723] text-right max-w-[220px] line-clamp-1">{{ $doctor->hospital ?? 'Not provided yet' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-[#8D6E63]">Verification Status:</span>
                        <span class="font-extrabold text-amber-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending Review
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-[#D7CCC8]/40 flex flex-col md:flex-row items-center justify-between gap-4 max-w-lg mx-auto">
                <div class="text-left text-xs text-[#8D6E63]">
                    <p class="font-semibold">Need to modify credentials?</p>
                    <p class="mt-0.5">Click the profile settings page to make updates.</p>
                </div>
                <div class="flex gap-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-white border border-[#D7CCC8] hover:bg-[#FDFBF7] text-[#5D4037] text-xs font-bold rounded-xl transition duration-150 active:scale-95 shadow-sm">
                            Log Out
                        </button>
                    </form>
                    <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-gradient-to-r from-[#5D4037] to-[#3E2723] hover:from-[#795548] text-white text-xs font-bold rounded-xl transition duration-150 active:scale-95 shadow-md hover:shadow-lg">
                        Edit Profile
                    </a>
                </div>
            </div>

            <!-- Notice card -->
            <p class="text-[11px] text-[#8D6E63] mt-8 opacity-75">
                Verification checks typically complete within 1-2 business hours. If you need immediate administrative clearance, please contact support at <span class="underline font-semibold">verification@medivault.com</span>.
            </p>
        </div>
    </div>
</x-app-layout>
