<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-btn ui-btn-primary w-full py-2.5 text-xs font-bold uppercase tracking-wider shadow-lg shadow-indigo-600/30']) }}>
    {{ $slot }}
</button>
