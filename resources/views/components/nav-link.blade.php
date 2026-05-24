@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-[#5D4037] text-sm font-bold leading-5 text-[#3E2723] focus:outline-none focus:border-[#3E2723] transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-semibold leading-5 text-[#8D6E63] hover:text-[#5D4037] hover:border-[#D7CCC8] focus:outline-none focus:text-[#5D4037] focus:border-[#D7CCC8] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
