<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Détails du Matériau : ') }} {{ $materiau->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Informations</h3>
                <div class="grid grid-cols-2 gap-4">
                    @if($materiau->reference)
                        <div><strong>Référence :</strong> <span class="font-mono">{{ $materiau->reference }}</span></div>
                    @endif
                    <div><strong>Nom :</strong> {{ $materiau->nom }}</div>
                    <div><strong>Unité de mesure :</strong> {{ $materiau->unite }}</div>
                    <div>
                        <strong>Prix unitaire HT :</strong>
                        @if($materiau->prix_unitaire > 0)
                            {{ number_format($materiau->prix_unitaire, 2, ',', ' ') }} €
                        @else
                            <span class="text-gray-400">Non renseigné</span>
                        @endif
                    </div>
                    <div class="col-span-2"><strong>Description :</strong> {{ $materiau->description ?? 'Aucune description' }}</div>
                    <div>
                        <strong>Statut :</strong>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $materiau->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $materiau->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <a href="{{ route('materiaux.edit', $materiau) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Modifier</a>
                    <a href="{{ route('materiaux.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Retour</a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Historique des utilisations</h3>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Intervention</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technicien</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coût total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($materiau->interventions as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('interventions.show', $item->intervention) }}" class="text-blue-500 hover:text-blue-700">
                                        {{ $item->intervention->code_intervention ?? '#'.$item->intervention_id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->intervention->technicien->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantite }} {{ $item->unite ?? $materiau->unite }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($materiau->prix_unitaire > 0)
                                        {{ number_format($item->quantite * $materiau->prix_unitaire, 2, ',', ' ') }} €
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">{{ $item->commentaire ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucune utilisation enregistrée pour ce matériau.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
