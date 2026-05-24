<x-guest-layout>
    <div class="min-h-screen bg-[#FDFBF7] flex flex-col justify-center items-center px-4">
        <div class="w-full max-w-md bg-white border border-[#D7CCC8]/60 rounded-3xl p-8 shadow-xl text-center">
            <span class="text-4xl">🔒</span>
            <h2 class="text-2xl font-bold text-[#3E2723] mt-4">Secure Shared Record</h2>
            <p class="text-sm text-[#8D6E63] mt-2 mb-6">This clinical record is password-protected. Please enter the passcode to access.</p>

            @if(isset($error))
                <div class="mb-4 text-xs font-bold text-red-600 bg-red-50 border border-red-200 p-3 rounded-xl">
                    {{ $error }}
                </div>
            @endif

            <form method="POST" action="{{ route('share.view', $token) }}" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label for="password" class="sr-only">Passcode</label>
                    <input type="password" name="password" id="password" required placeholder="Enter passcode" 
                           class="w-full px-4 py-3 bg-[#F5F2EB]/50 border border-[#D7CCC8]/60 focus:border-[#5D4037] focus:ring-[#5D4037] rounded-xl text-[#3E2723] text-center font-semibold tracking-widest transition">
                </div>

                <button type="submit" class="w-full py-3 bg-[#3E2723] hover:bg-[#5D4037] text-white font-bold rounded-xl transition active:scale-95 shadow-md">
                    Access Record
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
