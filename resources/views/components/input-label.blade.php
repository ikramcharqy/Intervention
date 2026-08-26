@props(['value'])

<label {{ $attributes->merge(['class' => 'ui-label text-[10px]']) }}>
    {{ $value ?? $slot }}
</label>
