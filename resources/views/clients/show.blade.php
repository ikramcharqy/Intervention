<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Client : {{ $client->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Notifications --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:text-green-300 dark:border-green-800">
                    {!! session('success') !!}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Fiche d'information client --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 lg:col-span-2 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold border-b border-gray-100 dark:border-gray-700 pb-2 mb-4">Informations Générales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div><strong>Code Client :</strong> <span class="font-mono">{{ $client->code_client }}</span></div>
                            <div><strong>Type de Client :</strong> {{ $client->type_client }}</div>
                            <div><strong>Nom / Raison Sociale :</strong> {{ $client->nom }}</div>
                            <div><strong>Nom du Contact :</strong> {{ $client->nom_contact ?? '-' }}</div>
                            <div><strong>Téléphone :</strong> {{ $client->telephone }}</div>
                            <div><strong>Téléphone secondaire :</strong> {{ $client->telephone_secondaire ?? '-' }}</div>
                            <div><strong>E-mail :</strong> {{ $client->email ?? '-' }}</div>
                            <div><strong>Ville :</strong> {{ $client->ville }}</div>
                            <div><strong>Pays :</strong> {{ $client->pays ?? '-' }}</div>
                            <div><strong>Adresse de facturation :</strong> {{ $client->adresse_facturation ?? '-' }}</div>
                            <div><strong>Commercial Responsable :</strong> 
                                @if($client->commercial)
                                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ $client->commercial->prenom }} {{ $client->commercial->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">Non assigné</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($client->type_client === 'Entreprise' && $client->clientEntreprise)
                        <div>
                            <h3 class="text-lg font-semibold border-b border-gray-100 dark:border-gray-700 pb-2 mb-4">Informations de l'Entreprise</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><strong>ICE :</strong> {{ $client->clientEntreprise->ice ?? '-' }}</div>
                                <div><strong>Identifiant Fiscal (IF) :</strong> {{ $client->clientEntreprise->if ?? '-' }}</div>
                                <div><strong>Registre du Commerce (RC) :</strong> {{ $client->clientEntreprise->rc ?? '-' }}</div>
                                <div><strong>Patente :</strong> {{ $client->clientEntreprise->patente ?? '-' }}</div>
                            </div>
                        </div>
                    @endif

                    @if ($client->observations)
                        <div>
                            <h3 class="text-lg font-semibold border-b border-gray-100 dark:border-gray-700 pb-2 mb-2">Observations</h3>
                            <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $client->observations }}</p>
                        </div>
                    @endif
                </div>

                {{-- Barre d'actions & Statut --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold border-b border-gray-100 dark:border-gray-700 pb-2 mb-4 font-medium">Statut du Compte</h3>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                                {{ $client->is_active ? 'Compte Actif' : 'Compte Désactivé' }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('clients.edit', $client) }}" class="w-full text-center bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition duration-150 font-medium">
                            Modifier le profil
                        </a>

                        @if ($client->is_active)
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Désactiver ce client ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-150 font-medium">
                                    Désactiver le compte
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('clients.restore', $client) }}">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition duration-150 font-medium">
                                    Réactiver le compte
                                </button>
                            </form>
                        @endif

                        @role('Super Admin')
                            <form method="POST" action="{{ route('clients.forceDelete', $client) }}" onsubmit="return confirm('ATTENTION : Supprimer DEFINITIVEMENT ce client et toutes ses données associées (chantiers, interventions...) ? Cette action est irréversible.');" class="mt-4 pt-4 border-t border-red-200 dark:border-red-800">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded-md transition duration-150 font-medium">
                                    Supprimer définitivement
                                </button>
                            </form>
                        @endrole

                        <a href="{{ route('clients.index') }}" class="w-full text-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-150 font-medium">
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>

            {{-- Liste des chantiers associés --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    <h3 class="text-lg font-semibold">Chantiers du Client ({{ $client->chantiers->count() }})</h3>
                    <a href="{{ route('chantiers.create', ['client_id' => $client->id]) }}" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md transition duration-150">
                        Ajouter un chantier
                    </a>
                </div>

                @if ($client->chantiers->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Code</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nom</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type Local</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ville</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($client->chantiers as $chantier)
                                    <tr>
                                        <td class="px-4 py-2 font-mono text-sm">{{ $chantier->code_chantier }}</td>
                                        <td class="px-4 py-2">{{ $chantier->nom }}</td>
                                        <td class="px-4 py-2">{{ $chantier->type_local }}</td>
                                        <td class="px-4 py-2">{{ $chantier->ville }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('chantiers.show', $chantier) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                Consulter
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun chantier enregistré pour ce client.</p>
                @endif
            </div>

            {{-- Contacts du client --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100" x-data="{ showContactForm: false }">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                    <h3 class="text-lg font-semibold">Contacts ({{ $client->contacts->count() }})</h3>
                    <button @click="showContactForm = !showContactForm" class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md transition duration-150">
                        + Ajouter un contact
                    </button>
                </div>

                {{-- Formulaire d'ajout de contact --}}
                <div x-show="showContactForm" x-collapse class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                    <form action="{{ route('contacts.store', $client) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nom *</label>
                                <input type="text" name="nom" required class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm" placeholder="Nom">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Prénom</label>
                                <input type="text" name="prenom" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm" placeholder="Prénom">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fonction</label>
                                <input type="text" name="fonction" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm" placeholder="Ex: Directeur technique">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm" placeholder="email@example.com">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Téléphone</label>
                                <input type="text" name="telephone" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm" placeholder="+212...">
                            </div>
                            <div class="flex items-center gap-2 mt-5">
                                <input type="checkbox" name="is_principal" value="1" id="is_principal" class="rounded">
                                <label for="is_principal" class="text-xs font-medium text-gray-700 dark:text-gray-300">Contact principal</label>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-2">
                            <button type="button" @click="showContactForm = false" class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">Annuler</button>
                            <button type="submit" class="px-3 py-1.5 text-sm bg-emerald-600 text-white rounded-md hover:bg-emerald-700 font-semibold">Enregistrer</button>
                        </div>
                    </form>
                </div>

                @if($client->contacts->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fonction</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Principal</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($client->contacts as $contact)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $contact->prenom }} {{ $contact->nom }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $contact->fonction ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $contact->email ?? '-' }}</td>
                                        <td class="px-4 py-2">{{ $contact->telephone ?? '-' }}</td>
                                        <td class="px-4 py-2">
                                            @if($contact->is_principal)
                                                <span class="px-2 py-0.5 text-xs bg-emerald-100 text-emerald-700 rounded-full font-semibold">Principal</span>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <form action="{{ route('contacts.destroy', $contact) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer ce contact ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline text-xs">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucun contact enregistré pour ce client.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
