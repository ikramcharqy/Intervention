@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'ui-input text-xs']) }}>
