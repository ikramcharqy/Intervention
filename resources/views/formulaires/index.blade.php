<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Formulaires Dynamiques</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <form method="GET" action="{{ route('formulaires.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..."
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Rechercher</button>
                    </form>
                    <a href="{{ route('formulaires.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Créer un formulaire</a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type d'intervention</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Champs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actif</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($formulaires as $formulaire)
                            <tr>
                                <td class="px-6 py-4">{{ $formulaire->nom }}</td>
                                <td class="px-6 py-4">{{ $formulaire->typeIntervention->nom ?? '-' }}</td>
                                <td class="px-6 py-4">{{ $formulaire->questions_count }}</td>
                                <td class="px-6 py-4">
                                    <span class="{{ $formulaire->is_active ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $formulaire->is_active ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 flex gap-2">
                                    <a href="{{ route('formulaires.show', $formulaire) }}" class="text-blue-500 hover:text-blue-700">Gérer les champs</a>
                                    <a href="{{ route('formulaires.edit', $formulaire) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun formulaire trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $formulaires->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
