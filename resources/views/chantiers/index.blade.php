<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">
            {{ __('Chantiers') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Outils et filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('chantiers.index') }}" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher un chantier..."
                        class="w-full md:w-80 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-650 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm shadow-sm">
                        Rechercher
                    </button>
                    @if(isset($search) && $search)
                        <a href="{{ route('chantiers.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-750 transition text-sm">
                            Réinitialiser
                        </a>
                    @endif
                </form>
                <a href="{{ route('chantiers.create') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouveau Chantier
                </a>
            </div>
        </div>

        <!-- Table des chantiers -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Code</th>
                            <th class="px-6 py-4">Nom</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Ville</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-855">
                        @forelse ($chantiers as $chantier)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4 font-mono font-semibold text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/20 w-32">
                                    {{ $chantier->code_chantier }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $chantier->nom }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $chantier->client->nom ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $chantier->ville }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('chantiers.show', $chantier) }}" class="text-indigo-650 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm font-medium">Voir</a>
                                        <a href="{{ route('chantiers.edit', $chantier) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">Aucun chantier enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($chantiers->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $chantiers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
