<x-guest-layout>
    <div class="flex flex-col items-center text-center mb-6">
        @if(!empty($socialUser['avatar']))
            <img class="w-20 h-20 rounded-full border-4 border-[#D7CCC8] shadow-md object-cover mb-3" src="{{ $socialUser['avatar'] }}" alt="{{ $socialUser['name'] }}">
        @endif
        <h2 class="text-xl font-extrabold text-[#3E2723] tracking-tight">Finalize Your Profile</h2>
        <p class="text-sm text-[#8D6E63] mt-1">Welcome, <span class="font-bold text-[#5D4037]">{{ $socialUser['name'] }}</span>. Let's set up your clinical account role.</p>
    </div>

    <form method="POST" action="{{ route('auth.social-register.store') }}">
        @csrf

        <div x-data="{ role: '{{ old('role', 'Patient') }}' }">
            <!-- Role Selection -->
            <div class="mt-4">
                <x-input-label for="role" :value="__('Select Your Role')" />
                <p class="text-xs text-[#8D6E63] mt-0.5 mb-2">This setting is permanent and determines your clinical dashboard capabilities.</p>
                
                <select id="role" name="role" x-model="role" class="border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm mt-1 block w-full py-2.5 px-3.5 font-medium transition duration-150" required>
                    <option value="Patient">Patient (Own and control your clinical history)</option>
                    <option value="Doctor">Medical Doctor (Request access and manage patient records)</option>
                </select>
                <x-input-error :messages="$errors->get('role')" class="mt-2" />
            </div>

            <!-- Phone Number (Required for Doctor) -->
            <div class="mt-4 animate-fade-in" x-show="role === 'Doctor'" x-cloak>
                <x-input-label for="phone" :value="__('Mobile Number')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" x-bind:required="role === 'Doctor'" placeholder="+91 99999 99999" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between mt-8">
            <a class="underline text-sm text-[#8D6E63] hover:text-[#5D4037] rounded-md focus:outline-none focus:ring-2 focus:ring-[#8D6E63] transition duration-150" href="{{ route('login') }}">
                {{ __('Cancel') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Complete Setup') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
