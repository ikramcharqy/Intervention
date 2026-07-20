<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">Rapports d'Intervention</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Outils et filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" action="{{ route('rapports.index') }}" class="flex gap-2 w-full md:w-auto">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par référence, chantier, technicien..."
                        class="w-full md:w-80 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-650 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm shadow-sm">
                        Rechercher
                    </button>
                    @if(isset($search) && $search)
                        <a href="{{ route('rapports.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-750 transition text-sm">
                            Réinitialiser
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Table des rapports -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Intervention</th>
                            <th class="px-6 py-4">Chantier</th>
                            <th class="px-6 py-4">Technicien</th>
                            <th class="px-6 py-4">Période de rédaction</th>
                            <th class="px-6 py-4">Statut Validation</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-855">
                        @forelse ($rapports as $rapport)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-650 dark:text-indigo-400">
                                    {{ $rapport->intervention->code_intervention ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-medium">{{ $rapport->intervention->chantier->nom ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $rapport->intervention->chantier->client->nom ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $rapport->intervention->technicien->prenom ?? '' }} {{ $rapport->intervention->technicien->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-400">Début :</span> {{ $rapport->date_debut ? $rapport->date_debut->format('d/m/Y H:i') : '-' }}</div>
                                    <div class="text-gray-700 dark:text-gray-300"><span class="font-medium text-gray-400">Fin :</span> {{ $rapport->date_fin ? $rapport->date_fin->format('d/m/Y H:i') : '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if (($rapport->intervention->statut ?? '') === 'Terminee')
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400">✓ Validé</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-50 text-orange-700 dark:bg-orange-950/20 dark:text-orange-400">⏳ En cours ({{ $rapport->intervention->statut ?? 'N/A' }})</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('rapports.show', $rapport) }}" class="text-indigo-650 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm">Voir</a>
                                        @can('update rapports')
                                            <a href="{{ route('rapports.edit', $rapport) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">Aucun rapport trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rapports->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $rapports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
