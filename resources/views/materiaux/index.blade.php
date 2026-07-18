<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Catalogue des Matériaux') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <form method="GET" action="{{ route('materiaux.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom ou description..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 w-64">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Rechercher</button>
                        @if($search)
                            <a href="{{ route('materiaux.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500">Réinitialiser</a>
                        @endif
                    </form>
                    <a href="{{ route('materiaux.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">+ Nouveau Matériau</a>
                </div>

                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-md">{{ session('success') }}</div>
                @endif

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unité</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($materiaux as $materiau)
                            <tr class="{{ !$materiau->is_active ? 'opacity-60' : '' }}">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-mono">{{ $materiau->reference ?? '-' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap font-medium">{{ $materiau->nom }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $materiau->unite }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if ($materiau->prix_unitaire !== null && $materiau->prix_unitaire > 0)
                                        {{ number_format($materiau->prix_unitaire, 2, ',', ' ') }} €
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $materiau->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $materiau->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap flex gap-2">
                                    <a href="{{ route('materiaux.show', $materiau) }}" class="text-blue-500 hover:text-blue-700">Voir</a>
                                    <a href="{{ route('materiaux.edit', $materiau) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                    <form method="POST" action="{{ route('materiaux.destroy', $materiau) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer/désactiver ce matériau ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">Aucun matériau enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $materiaux->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
