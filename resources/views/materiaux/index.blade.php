<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">
            {{ __('Catalogue des Matériaux') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Outils et filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('materiaux.index') }}" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom ou description..."
                        class="w-full md:w-80 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-650 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm shadow-sm">
                        Rechercher
                    </button>
                    @if($search)
                        <a href="{{ route('materiaux.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-750 transition text-sm">
                            Réinitialiser
                        </a>
                    @endif
                </form>
                <a href="{{ route('materiaux.create') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Nouveau Matériau
                </a>
            </div>
        </div>

        <!-- Table des matériaux -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Référence</th>
                            <th class="px-6 py-4">Nom</th>
                            <th class="px-6 py-4">Unité</th>
                            <th class="px-6 py-4">Prix unitaire</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-855">
                        @forelse ($materiaux as $materiau)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition {{ !$materiau->is_active ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4 font-mono font-semibold text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/20 w-32">
                                    {{ $materiau->reference ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    {{ $materiau->nom }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $materiau->unite }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    @if ($materiau->prix_unitaire !== null && $materiau->prix_unitaire > 0)
                                        {{ number_format($materiau->prix_unitaire, 2, ',', ' ') }} €
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $materiau->is_active ? 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400' : 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400' }}">
                                        {{ $materiau->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('materiaux.show', $materiau) }}" class="text-indigo-650 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm">Voir</a>
                                        <a href="{{ route('materiaux.edit', $materiau) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                        <form method="POST" action="{{ route('materiaux.destroy', $materiau) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir désactiver ou supprimer ce matériau ?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-755 font-semibold text-sm">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">Aucun matériau enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($materiaux->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $materiaux->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
