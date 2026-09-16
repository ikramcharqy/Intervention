<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ds-btn ds-btn-md ds-btn-danger']) }}>
    {{ $slot }}
</button>
