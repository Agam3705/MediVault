@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-[#5D4037]']) }}>
    {{ $value ?? $slot }}
</label>
