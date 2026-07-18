<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Rapports d'Intervention</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <form method="GET" action="{{ route('rapports.index') }}" class="flex gap-2">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher..."
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Rechercher</button>
                    </form>
                    <a href="{{ route('rapports.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Nouveau Rapport</a>
                </div>

                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Intervention</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chantier</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Technicien</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Période de rédaction</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut Validation</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($rapports as $rapport)
                            <tr>
                                <td class="px-4 py-3 font-mono text-sm">
                                    {{ $rapport->intervention->code_intervention ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $rapport->intervention->chantier->nom ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $rapport->intervention->technicien->prenom ?? '' }} {{ $rapport->intervention->technicien->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    Du {{ $rapport->date_debut ? $rapport->date_debut->format('d/m/Y H:i') : '-' }}<br>
                                    Au {{ $rapport->date_fin ? $rapport->date_fin->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if (($rapport->intervention->statut ?? '') === 'Terminee')
                                        <span class="text-green-500 font-semibold">✓ Validé</span>
                                    @else
                                        <span class="text-orange-500 font-medium">⏳ En attente ({{ $rapport->intervention->statut ?? 'N/A' }})</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('rapports.show', $rapport) }}" class="text-blue-500 hover:text-blue-700">Voir</a>
                                    @can('update rapports')
                                        <a href="{{ route('rapports.edit', $rapport) }}" class="text-yellow-500 hover:text-yellow-700">Modifier</a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">Aucun rapport trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $rapports->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
