<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Interventions</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <!-- Filtres -->
                <form method="GET" action="{{ route('interventions.index') }}" class="flex flex-wrap gap-2 mb-6">
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..."
                        class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    <select name="statut" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Tous les statuts</option>
                        @foreach (['Planifiee', 'En cours', 'Suspendue', 'Terminee', 'Annulee'] as $s)
                            <option value="{{ $s }}" @selected(($statut ?? '') == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                    <select name="priorite" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Toutes les priorités</option>
                        @foreach (['Faible', 'Normale', 'Haute', 'Urgente'] as $p)
                            <option value="{{ $p }}" @selected(($priorite ?? '') == $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filtrer</button>
                    <a href="{{ route('interventions.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">Réinitialiser</a>
                    @can('create interventions')
                        <a href="{{ route('interventions.create') }}" class="ml-auto bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Nouvelle Intervention</a>
                    @endcan
                </form>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chantier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technicien</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date planifiée</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($interventions as $intervention)
                            <tr>
                                <td class="px-4 py-3 font-mono text-sm">{{ $intervention->code_intervention }}</td>
                                <td class="px-4 py-3">{{ $intervention->typeIntervention->nom ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $intervention->chantier->nom ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $intervention->technicien->name ?? 'Non assigné' }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $colors = ['Faible' => 'text-gray-500', 'Normale' => 'text-blue-500', 'Haute' => 'text-orange-500', 'Urgente' => 'text-red-600 font-bold'];
                                    @endphp
                                    <span class="{{ $colors[$intervention->priorite] ?? '' }}">{{ $intervention->priorite }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'Planifiee' => 'bg-blue-100 text-blue-800',
                                            'En cours' => 'bg-yellow-100 text-yellow-800',
                                            'Suspendue' => 'bg-orange-100 text-orange-800',
                                            'Terminee' => 'bg-green-100 text-green-800',
                                            'Annulee' => 'bg-red-100 text-red-800',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$intervention->statut] ?? '' }}">
                                        {{ $intervention->statut }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('interventions.show', $intervention) }}" class="text-blue-500 hover:text-blue-700 mr-2">Voir</a>
                                    @can('update interventions')
                                        <a href="{{ route('interventions.edit', $intervention) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500">Aucune intervention trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $interventions->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
