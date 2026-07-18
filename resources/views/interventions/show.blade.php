<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Intervention : {{ $intervention->code_intervention }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Informations générales --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Informations générales</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Code Intervention :</strong> {{ $intervention->code_intervention }}</div>
                    <div><strong>Type :</strong> {{ $intervention->typeIntervention->nom ?? '-' }}</div>
                    <div><strong>Chantier :</strong> {{ $intervention->chantier->nom ?? '-' }}</div>
                    <div><strong>Client :</strong> {{ $intervention->chantier->client->nom ?? '-' }}</div>
                    <div><strong>Emplacement :</strong> {{ $intervention->emplacement->nom ?? '-' }}</div>
                    <div><strong>Technicien :</strong> {{ $intervention->technicien->prenom ?? '' }} {{ $intervention->technicien->name ?? 'Non assigné' }}</div>
                    <div>
                        <strong>Priorité :</strong>
                        @php $colors = ['Faible' => 'text-gray-500', 'Normale' => 'text-blue-500', 'Haute' => 'text-orange-500', 'Urgente' => 'text-red-600 font-bold']; @endphp
                        <span class="{{ $colors[$intervention->priorite] ?? '' }}">{{ $intervention->priorite }}</span>
                    </div>
                    <div>
                        <strong>Statut :</strong>
                        @php $statusColors = ['Planifiee' => 'bg-blue-100 text-blue-800','En cours' => 'bg-yellow-100 text-yellow-800','Suspendue' => 'bg-orange-100 text-orange-800','Terminee' => 'bg-green-100 text-green-800','Annulee' => 'bg-red-100 text-red-800']; @endphp
                        <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$intervention->statut] ?? '' }}">{{ $intervention->statut }}</span>
                    </div>
                    <div><strong>Début prévu :</strong> {{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</div>
                    <div><strong>Fin prévue :</strong> {{ $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('d/m/Y H:i') : '-' }}</div>
                    <div><strong>Début réel :</strong> {{ $intervention->date_reelle_debut ? $intervention->date_reelle_debut->format('d/m/Y H:i') : '-' }}</div>
                    <div><strong>Fin réelle :</strong> {{ $intervention->date_reelle_fin ? $intervention->date_reelle_fin->format('d/m/Y H:i') : '-' }}</div>
                    <div><strong>Durée prévue :</strong> {{ $intervention->duree_prevue ? $intervention->duree_prevue . ' min' : '-' }}</div>
                    <div><strong>Durée réelle :</strong> {{ $intervention->duree_reelle ? $intervention->duree_reelle . ' min' : '-' }}</div>
                </div>

                @if ($intervention->description)
                    <div class="mt-4"><strong>Description :</strong><br>{{ $intervention->description }}</div>
                @endif
                @if ($intervention->observations)
                    <div class="mt-2"><strong>Observations / Consignes :</strong><br>{{ $intervention->observations }}</div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Actions</h3>
                <div class="flex flex-wrap gap-3">
                    @can('update interventions')
                        @if($intervention->statut !== 'Terminee' && $intervention->statut !== 'Annulee')
                            <a href="{{ route('interventions.edit', $intervention) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Modifier</a>
                        @endif
                    @endcan

                    @if ($intervention->statut === 'Planifiee' && auth()->user()->hasRole('Technicien'))
                        <form method="POST" action="{{ route('interventions.accept', $intervention) }}">
                            @csrf
                            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600">Accepter l'intervention</button>
                        </form>
                    @endif

                    @if ($intervention->statut === 'Acceptee')
                        <form method="POST" action="{{ route('interventions.start', $intervention) }}">
                            @csrf
                            <input type="hidden" name="mode" value="Manuel">
                            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">Démarrer</button>
                        </form>
                    @endif

                    @if (in_array($intervention->statut, ['En cours', 'Formulaire rempli']) && $intervention->typeIntervention && $intervention->typeIntervention->formulaire && $intervention->typeIntervention->formulaire->is_active)
                        <a href="{{ route('interventions.formulaire.create', $intervention) }}" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600">
                            {{ $intervention->statut === 'Formulaire rempli' ? 'Modifier le formulaire' : 'Remplir le formulaire' }}
                        </a>
                    @endif

                    @if ($intervention->statut === 'Formulaire rempli' && auth()->user()->hasAnyRole(['Administrateur', 'Super Admin']))
                        <form method="POST" action="{{ route('interventions.validate', $intervention) }}">
                            @csrf
                            <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-md hover:bg-green-800">Valider l'intervention</button>
                        </form>
                    @endif

                    @if ($intervention->statut === 'Terminee' && $intervention->typeIntervention && $intervention->typeIntervention->formulaire)
                        <a href="{{ route('interventions.formulaire.create', $intervention) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                            Consulter le formulaire
                        </a>
                    @endif

                    <a href="{{ route('interventions.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Retour</a>
                </div>
            </div>

            {{-- Tâches --}}
            @if ($intervention->taches->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Tâches ({{ $intervention->taches->count() }})</h3>
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($intervention->taches as $item)
                            <li class="py-2 flex justify-between">
                                <span>{{ $item->tache->nom ?? '-' }}</span>
                                <span class="text-sm {{ $item->statut === 'Terminee' ? 'text-green-500' : 'text-gray-400' }}">{{ $item->statut }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Matériaux utilisés --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Matériaux utilisés ({{ $intervention->materiaux->count() }})</h3>
                    @php
                        $coutTotal = $intervention->materiaux->sum(function($m) {
                            return $m->quantite * ($m->materiau->prix_unitaire ?? 0);
                        });
                    @endphp
                    @if($coutTotal > 0)
                        <span class="text-sm font-semibold text-green-700 dark:text-green-400">Coût total : {{ number_format($coutTotal, 2, ',', ' ') }} €</span>
                    @endif
                </div>

                {{-- Formulaire d'ajout (visible si l'intervention n'est pas terminée/annulée) --}}
                @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                    <form method="POST" action="{{ route('interventions.materiaux.store', $intervention) }}" class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div class="md:col-span-2">
                                <label for="materiau_id" class="block text-sm font-medium mb-1">Matériau</label>
                                <select name="materiau_id" id="materiau_id" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                    <option value="">-- Sélectionner un matériau --</option>
                                    @foreach($materiaux as $mat)
                                        <option value="{{ $mat->id }}" {{ old('materiau_id') == $mat->id ? 'selected' : '' }}>
                                            {{ $mat->nom }} ({{ $mat->unite }}){{ $mat->prix_unitaire > 0 ? ' — ' . number_format($mat->prix_unitaire, 2, ',', ' ') . '€/' . $mat->unite : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('materiau_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="quantite" class="block text-sm font-medium mb-1">Quantité</label>
                                <input type="number" name="quantite" id="quantite" step="0.01" min="0.01" value="{{ old('quantite', 1) }}" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                @error('quantite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="commentaire" class="block text-sm font-medium mb-1">Commentaire</label>
                                <input type="text" name="commentaire" id="commentaire" value="{{ old('commentaire') }}" placeholder="Optionnel..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 text-sm">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 text-sm">+ Ajouter ce matériau</button>
                        </div>
                    </form>
                @endif

                {{-- Liste des matériaux --}}
                @if($intervention->materiaux->isNotEmpty())
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Matériau</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix unit.</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Coût total</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Commentaire</th>
                                @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($intervention->materiaux as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-medium">{{ $item->materiau->nom ?? 'Inconnu' }}</span>
                                        @if($item->materiau && $item->materiau->reference)
                                            <br><span class="text-xs text-gray-400 font-mono">{{ $item->materiau->reference }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $item->quantite }} {{ $item->unite ?? ($item->materiau->unite ?? '') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if($item->materiau && $item->materiau->prix_unitaire > 0)
                                            {{ number_format($item->materiau->prix_unitaire, 2, ',', ' ') }} €
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-medium">
                                        @if($item->materiau && $item->materiau->prix_unitaire > 0)
                                            {{ number_format($item->quantite * $item->materiau->prix_unitaire, 2, ',', ' ') }} €
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->commentaire ?? '-' }}</td>
                                    @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                                        <td class="px-4 py-3">
                                            <form method="POST" action="{{ route('interventions.materiaux.destroy', [$intervention, $item]) }}" onsubmit="return confirm('Retirer ce matériau ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Retirer</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-sm">Aucun matériau associé à cette intervention.</p>
                @endif
            </div>

            {{-- Suivi GPS (module indépendant du pointage de présence) --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Suivi GPS ({{ $intervention->gpsTrackingSessions->count() }})</h3>

                    @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                        @php $sessionGpsActive = $intervention->gpsTrackingSessions->firstWhere('ended_at', null); @endphp
                        @if($sessionGpsActive)
                            <form method="POST" action="{{ route('interventions.gpsTracking.stop', [$intervention, $sessionGpsActive]) }}">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Arrêter la session GPS</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('interventions.gpsTracking.start', $intervention) }}">
                                @csrf
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">Démarrer une session GPS</button>
                            </form>
                        @endif
                    @endif
                </div>

                @if($intervention->gpsTrackingSessions->isNotEmpty())
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-2">Début</th>
                                <th class="px-4 py-2">Fin</th>
                                <th class="px-4 py-2">Durée</th>
                                <th class="px-4 py-2">Distance</th>
                                <th class="px-4 py-2">Points GPS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($intervention->gpsTrackingSessions->sortByDesc('started_at') as $sessionGps)
                                @php
                                    $secondes = $sessionGps->dureeSecondes();
                                    $duree = sprintf('%02dh%02d', intdiv($secondes, 3600), intdiv($secondes % 3600, 60));
                                @endphp
                                <tr>
                                    <td class="px-4 py-2">{{ $sessionGps->started_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-2">{{ $sessionGps->ended_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="px-4 py-2">{{ $duree }}</td>
                                    <td class="px-4 py-2">{{ number_format($sessionGps->distance_metres / 1000, 2, ',', ' ') }} km</td>
                                    <td class="px-4 py-2">{{ $sessionGps->points->count() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-sm">Aucune session de suivi GPS pour cette intervention.</p>
                @endif
            </div>

            {{-- Rapport --}}
            @if ($intervention->rapport)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">Rapport</h3>
                    <a href="{{ route('rapports.show', $intervention->rapport) }}" class="text-blue-500 hover:text-blue-700">Voir le rapport</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
