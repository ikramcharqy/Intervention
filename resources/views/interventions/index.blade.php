<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">Interventions</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Barre d'outils et filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <form method="GET" action="{{ route('interventions.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Recherche</label>
                    <input type="text" name="search" id="search" value="{{ $search ?? '' }}" placeholder="Code, Client, Chantier, Technicien..."
                        class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                
                <div class="w-full md:w-48">
                    <label for="statut" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Statut</label>
                    <select name="statut" id="statut" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                        <option value="">Tous les statuts</option>
                        @foreach (['Planifiee', 'Acceptee', 'En cours', 'Formulaire rempli', 'Suspendue', 'Terminee', 'Annulee'] as $s)
                            <option value="{{ $s }}" @selected(($statut ?? '') == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-48">
                    <label for="priorite" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Priorité</label>
                    <select name="priorite" id="priorite" class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                        <option value="">Toutes les priorités</option>
                        @foreach (['Faible', 'Normale', 'Haute', 'Urgente'] as $p)
                            <option value="{{ $p }}" @selected(($priorite ?? '') == $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit" class="bg-indigo-650 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm flex-1 md:flex-none text-center shadow-sm">
                        Filtrer
                    </button>
                    @if($search || $statut || $priorite)
                        <a href="{{ route('interventions.index') }}" class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-750 transition text-sm text-center">
                            Réinitialiser
                        </a>
                    @endif
                </div>

                @can('create interventions')
                    <div class="w-full md:w-auto md:ml-auto">
                        <a href="{{ route('interventions.create') }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-4 rounded-xl transition text-sm shadow-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Créer une intervention
                        </a>
                    </div>
                @endcan
            </form>
        </div>

        <!-- Table des interventions -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Référence</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4">Chantier / Client</th>
                            <th class="px-6 py-4">Technicien</th>
                            <th class="px-6 py-4">Priorité</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4">Date planifiée</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-855">
                        @forelse ($interventions as $intervention)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-6 py-4 font-mono font-bold text-indigo-650 dark:text-indigo-400">
                                    {{ $intervention->code_intervention }}
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    {{ $intervention->typeIntervention->nom ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-medium">{{ $intervention->chantier->nom ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $intervention->chantier->client->nom ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    {{ $intervention->technicien->prenom ?? '' }} {{ $intervention->technicien->name ?? 'Non assigné' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $priColors = [
                                            'Faible' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                            'Normale' => 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400',
                                            'Haute' => 'bg-orange-50 text-orange-700 dark:bg-orange-950/20 dark:text-orange-400',
                                            'Urgente' => 'bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-400 font-bold',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $priColors[$intervention->priorite] ?? '' }}">
                                        {{ $intervention->priorite }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'Planifiee' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900',
                                            'Acceptee' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900',
                                            'En cours' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-950/20 dark:text-yellow-400 dark:border-yellow-900',
                                            'Formulaire rempli' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900',
                                            'Suspendue' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/20 dark:text-orange-400 dark:border-orange-900',
                                            'Terminee' => 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900',
                                            'Annulee' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900'
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusColors[$intervention->statut] ?? '' }}">
                                        {{ $intervention->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('interventions.show', $intervention) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 font-semibold text-sm">Voir</a>
                                        @can('update interventions')
                                            <a href="{{ route('interventions.edit', $intervention) }}" class="text-gray-500 hover:text-gray-900 dark:hover:text-white font-medium text-sm">Modifier</a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-500">Aucune intervention trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($interventions->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $interventions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
