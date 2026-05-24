<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-gradient-to-r from-[#5D4037] to-[#3E2723] border border-transparent rounded-xl font-semibold text-xs text-[#FFF8E1] uppercase tracking-widest hover:from-[#795548] hover:to-[#5D4037] active:scale-95 focus:outline-none focus:ring-2 focus:ring-[#8D6E63] focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg']) }}>
    {{ $slot }}
</button>
