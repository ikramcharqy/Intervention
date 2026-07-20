<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Profil de {{ $user->prenom }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-col md:flex-row gap-6">
                    @if ($user->photo)
                        <div class="w-32 h-32 rounded-full overflow-hidden flex-shrink-0 bg-gray-100">
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="flex-grow space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div><strong>Nom :</strong> {{ $user->name }}</div>
                            <div><strong>Prénom :</strong> {{ $user->prenom }}</div>
                            <div><strong>E-mail :</strong> {{ $user->email }}</div>
                            <div><strong>Téléphone :</strong> {{ $user->telephone }}</div>
                            <div class="col-span-2"><strong>Adresse :</strong> {{ $user->adresse ?? '-' }}</div>
                            <div>
                                <strong>Rôles :</strong>
                                @foreach ($user->roles as $roleObj)
                                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full mr-1">{{ $roleObj->name }}</span>
                                @endforeach
                            </div>
                            <div>
                                <strong>Statut :</strong>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-4">
                            <a href="{{ route('users.edit', $user) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Modifier</a>
                            <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Retour</a>
                        </div>
                    </div>
                </div>
            </div>

            @if ($user->interventions->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Dernières interventions assignées</h3>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Chantier</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Priorité</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Début prévu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($user->interventions as $intervention)
                                <tr>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('interventions.show', $intervention) }}" class="text-blue-500 hover:text-blue-700">
                                            {{ $intervention->code_intervention }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">{{ $intervention->chantier->nom ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $intervention->priorite }}</td>
                                    <td class="px-4 py-2">{{ $intervention->statut }}</td>
                                    <td class="px-4 py-2">{{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if ($user->hasRole('Commercial'))
                @php
                    $user->load('clientsGeres');
                @endphp
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Clients enregistrés / gérés par ce commercial ({{ $user->clientsGeres->count() }})</h3>
                    @if($user->clientsGeres->isNotEmpty())
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code Client</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom / Raison Sociale</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ville</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($user->clientsGeres as $clt)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-sm">{{ $clt->code_client }}</td>
                                        <td class="px-4 py-2">{{ $clt->nom }}</td>
                                        <td class="px-4 py-2">{{ $clt->telephone }}</td>
                                        <td class="px-4 py-2">{{ $clt->ville }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('clients.show', $clt) }}" class="text-blue-500 hover:text-blue-700">
                                                Voir Fiche
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-sm text-gray-500">Aucun client lié pour le moment.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
