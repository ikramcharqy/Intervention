<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Demandes d\'Intervention') }}
            </h2>
            <a href="{{ route('demande-interventions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Nouvelle Demande
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr>
                                <th class="border-b py-2 px-4">Référence</th>
                                <th class="border-b py-2 px-4">Client</th>
                                <th class="border-b py-2 px-4">Objet</th>
                                <th class="border-b py-2 px-4">Priorité</th>
                                <th class="border-b py-2 px-4">Statut</th>
                                <th class="border-b py-2 px-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($demandes as $demande)
                                <tr>
                                    <td class="border-b py-2 px-4 font-medium">{{ $demande->reference }}</td>
                                    <td class="border-b py-2 px-4">{{ $demande->client->nom }}</td>
                                    <td class="border-b py-2 px-4">{{ $demande->objet }}</td>
                                    <td class="border-b py-2 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $demande->priorite == 'Urgente' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $demande->priorite }}
                                        </span>
                                    </td>
                                    <td class="border-b py-2 px-4">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $demande->statut == 'Planifiée' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $demande->statut }}
                                        </span>
                                    </td>
                                    <td class="border-b py-2 px-4 text-right space-x-2">
                                        <a href="{{ route('demande-interventions.show', $demande) }}" class="text-indigo-600 hover:underline text-sm">Détails</a>
                                        @if($demande->statut == 'En attente')
                                            <a href="{{ route('demande-interventions.edit', $demande) }}" class="text-blue-600 hover:underline text-sm">Modifier</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($demandes->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-500">Aucune demande d'intervention trouvée.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
