@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#D7CCC8] focus:border-[#5D4037] focus:ring-[#5D4037] bg-[#FDFBF7] text-[#3E2723] rounded-xl shadow-sm']) }}>
