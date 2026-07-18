<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tâches</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <form method="GET" action="{{ route('taches.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..."
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <select name="statut" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <option value="">Tous les statuts</option>
                            @foreach (['A faire', 'En cours', 'Terminee', 'Annulee'] as $s)
                                <option value="{{ $s }}" @selected(($statut ?? '') == $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Filtrer</button>
                    </form>
                    <a href="{{ route('taches.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Nouvelle Tâche</a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intervention</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($taches as $tache)
                            <tr>
                                <td class="px-4 py-3">{{ $tache->titre }}</td>
                                <td class="px-4 py-3">{{ $tache->intervention->reference ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @php $statusColors = ['A faire' => 'bg-gray-100 text-gray-800', 'En cours' => 'bg-yellow-100 text-yellow-800', 'Terminee' => 'bg-green-100 text-green-800', 'Annulee' => 'bg-red-100 text-red-800']; @endphp
                                    <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$tache->statut] ?? '' }}">{{ $tache->statut }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $tache->ordre }}</td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('taches.show', $tache) }}" class="text-blue-500 hover:text-blue-700">Voir</a>
                                    <a href="{{ route('taches.edit', $tache) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Aucune tâche trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $taches->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
