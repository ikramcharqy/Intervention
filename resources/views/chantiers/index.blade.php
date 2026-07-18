<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chantiers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <form method="GET" action="{{ route('chantiers.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..." class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Rechercher</button>
                    </form>
                    <a href="{{ route('chantiers.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Nouveau Chantier</a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($chantiers as $chantier)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $chantier->code_chantier }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $chantier->nom }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $chantier->client->nom ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $chantier->ville }}</td>
                                <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                                    <a href="{{ route('chantiers.show', $chantier) }}" class="text-blue-500 hover:text-blue-700">Voir</a>
                                    <a href="{{ route('chantiers.edit', $chantier) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $chantiers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
