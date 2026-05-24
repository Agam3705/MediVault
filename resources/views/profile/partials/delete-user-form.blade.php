<section class="space-y-6">
    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="w-full text-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition duration-150 active:scale-95 shadow-sm"
    >
        {{ __('Permanently Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-white rounded-3xl border border-[#D7CCC8]/60">
            @csrf
            @method('delete')

            <h2 class="text-base font-extrabold text-[#3E2723]">
                {{ __('Confirm Account Deletion') }}
            </h2>

            <p class="mt-2 text-xs text-[#8D6E63]">
                {{ __('Once your account is deleted, all resources (including clinical uploads, active consent sessions, and emergency card QR configurations) will be permanently erased. Enter your current password to proceed.') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Current Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-full"
                    placeholder="{{ __('Enter password to confirm...') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 bg-white border border-[#D7CCC8] text-[#5D4037] text-xs font-bold rounded-xl hover:bg-[#FDFBF7] transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition">
                    {{ __('Confirm Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
