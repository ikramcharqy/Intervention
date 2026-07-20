<x-commercial-layout>
    <x-slot name="header">
        {{ __('Documents') }}
    </x-slot>

    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-10 text-center">
        <div class="mx-auto h-14 w-14 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center mb-4">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h3 class="text-base font-bold text-gray-800 dark:text-gray-200">Espace Documents</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 max-w-md mx-auto">
            Cet espace regroupera vos documents commerciaux. La fonctionnalité arrive prochainement.
        </p>
    </div>
</x-commercial-layout>
