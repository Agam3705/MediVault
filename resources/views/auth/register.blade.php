<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ role: '{{ old('role', 'Patient') }}' }">
            <!-- Role Selection -->
            <div class="mt-4">
                <x-input-label for="role" :value="__('Register As')" />
                <select id="role" name="role" x-model="role" class="border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm mt-1 block w-full py-2.5 px-3.5 font-medium transition duration-150" required>
                    <option value="Patient">Patient (Own & control your clinical history)</option>
                    <option value="Doctor">Medical Doctor (Request access & manage records)</option>
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

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-[#8D6E63] hover:text-[#5D4037] rounded-md focus:outline-none focus:ring-2 focus:ring-[#8D6E63] transition duration-150" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <!-- Social Login Separator & Google Button -->
        <div class="mt-6">
            <div class="flex items-center w-full my-2">
                <div class="flex-grow border-t border-[#D7CCC8]/60"></div>
                <span class="mx-3 text-xs text-[#8D6E63] font-semibold uppercase tracking-wider">or</span>
                <div class="flex-grow border-t border-[#D7CCC8]/60"></div>
            </div>

            <a href="{{ route('auth.google') }}" class="w-full mt-4 flex items-center justify-center gap-3 px-5 py-2.5 bg-[#FDFBF7] border border-[#D7CCC8] hover:bg-[#F5F2EB] active:scale-[0.99] text-[#5D4037] font-semibold text-sm rounded-xl transition duration-150 shadow-sm">
                <svg class="w-5 h-5" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span>Continue with Google</span>
            </a>
        </div>
    </form>
</x-guest-layout>
