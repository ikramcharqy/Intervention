<x-commercial-layout>
    <x-slot name="header">
        {{ __('Devis') }}
    </x-slot>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-10 text-center">
        <div class="mx-auto h-14 w-14 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-4">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">Module Devis</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
            Cet espace est réservé à la gestion des devis. La fonctionnalité arrive prochainement.
        </p>
    </div>
</x-commercial-layout>
