<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-250 leading-tight">
            Intervention : {{ $intervention->code_intervention }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Grid principal : Informations à gauche (large), Actions à droite (étroit) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Section Informations (2/3 de large) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Carte Informations Générales -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informations Générales
                        </h3>
                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">
                            {{ $intervention->code_intervention }}
                        </span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                            <div>
                                <span class="text-gray-500 block">Type d'intervention</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $intervention->typeIntervention->nom ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Client</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $intervention->chantier->client->nom ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Chantier</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $intervention->chantier->nom ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Emplacement</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $intervention->emplacement->nom ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Technicien</span>
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $intervention->technicien->prenom ?? '' }} {{ $intervention->technicien->name ?? 'Non assigné' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Priorité</span>
                                @php 
                                    $priColors = [
                                        'Faible' => 'text-gray-600 bg-gray-100 dark:bg-gray-800 dark:text-gray-300', 
                                        'Normale' => 'text-blue-600 bg-blue-50 dark:bg-blue-950/30 dark:text-blue-400', 
                                        'Haute' => 'text-orange-600 bg-orange-50 dark:bg-orange-950/30 dark:text-orange-400', 
                                        'Urgente' => 'text-red-650 bg-red-50 dark:bg-red-950/30 dark:text-red-400 font-bold'
                                    ]; 
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs inline-block mt-1 font-medium {{ $priColors[$intervention->priorite] ?? '' }}">
                                    {{ $intervention->priorite }}
                                </span>
                            </div>
                        </div>

                        @if ($intervention->description)
                            <div class="mt-6 border-t border-gray-100 dark:border-gray-800 pt-4 text-sm">
                                <span class="text-gray-500 block mb-1">Description</span>
                                <p class="text-gray-850 dark:text-gray-350 whitespace-pre-line">{{ $intervention->description }}</p>
                            </div>
                        @endif

                        @if ($intervention->observations)
                            <div class="mt-4 border-t border-gray-100 dark:border-gray-800 pt-4 text-sm">
                                <span class="text-gray-500 block mb-1">Observations / Consignes</span>
                                <p class="text-gray-850 dark:text-gray-350 whitespace-pre-line">{{ $intervention->observations }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Carte Workflow / Planification & Suivi -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Dates & Planification
                        </h3>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <!-- Dates prévues -->
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-950 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-1">Planification</h4>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Début prévu :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Fin prévue :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Durée prévue :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $intervention->duree_prevue ? $intervention->duree_prevue . ' min' : '-' }}</span>
                            </div>
                        </div>

                        <!-- Dates réelles -->
                        <div class="space-y-3">
                            <h4 class="font-semibold text-gray-950 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-1">Réalisation</h4>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Début réel :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $intervention->date_reelle_debut ? $intervention->date_reelle_debut->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Fin réelle :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $intervention->date_reelle_fin ? $intervention->date_reelle_fin->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Durée réelle :</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $intervention->duree_reelle ? $intervention->duree_reelle . ' min' : '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Col droite : Actions & Statuts (1/3 de large) -->
            <div class="space-y-6">
                <!-- Carte Statut & Actions -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 space-y-6">
                    <div>
                        <span class="text-sm text-gray-500 block mb-1">Statut actuel</span>
                        @php 
                            $statusColors = [
                                'Planifiee' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900',
                                'Acceptee' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900',
                                'En cours' => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-950/20 dark:text-yellow-400 dark:border-yellow-900',
                                'Formulaire rempli' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900',
                                'Suspendue' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/20 dark:text-orange-400 dark:border-orange-900',
                                'Terminee' => 'bg-green-50 text-green-700 border-green-200 dark:bg-green-950/20 dark:text-green-400 dark:border-green-900',
                                'Annulee' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900'
                            ]; 
                        @endphp
                        <span class="px-4 py-2 rounded-xl text-sm font-bold border block text-center {{ $statusColors[$intervention->statut] ?? '' }}">
                            {{ $intervention->statut }}
                        </span>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 pt-4 space-y-3">
                        <h4 class="font-bold text-sm text-gray-900 dark:text-white">Actions disponibles</h4>

                        {{-- Use the shared actions partial which relies on policies for availability --}}
                        @include('interventions._actions', ['intervention' => $intervention])

                        <a href="{{ route('interventions.index') }}" class="w-full bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 font-medium py-2 px-4 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-750 transition flex items-center justify-center gap-2 text-sm">
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Tâches et Matériaux sur la même ligne ou empilés -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Tâches -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Tâches associées
                    </h3>
                </div>
                <div class="p-6">
                    @if ($intervention->taches->isNotEmpty())
                        <ul class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($intervention->taches as $item)
                                <li class="py-3 flex justify-between items-center text-sm">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $item->tache->nom ?? '-' }}</span>
                                    @php 
                                        $tacheColors = [
                                            'Planifiee' => 'bg-gray-100 text-gray-600',
                                            'Terminee' => 'bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-400'
                                        ]; 
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $tacheColors[$item->statut] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $item->statut }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-sm">Aucune tâche assignée à cette intervention.</p>
                    @endif
                </div>
            </div>

            <!-- Rapport d'Intervention -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                            <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Rapport d'intervention
                        </h3>
                    </div>
                    <div class="p-6 text-sm text-gray-600 dark:text-gray-400">
                        @if ($intervention->rapport)
                            <p class="mb-4 text-green-600 dark:text-green-400 font-medium">Un rapport a été soumis pour cette intervention.</p>
                            <div class="space-y-1">
                                <div><strong>Date début rédaction :</strong> {{ $intervention->rapport->date_debut?->format('d/m/Y H:i') ?? '-' }}</div>
                                <div><strong>Date fin rédaction :</strong> {{ $intervention->rapport->date_fin?->format('d/m/Y H:i') ?? '-' }}</div>
                            </div>
                        @else
                            <p>Aucun rapport rédigé pour le moment. Une fois l'intervention achevée, le technicien doit soumettre son rapport final.</p>
                        @endif
                    </div>
                </div>
                <div class="p-6 border-t border-gray-100 dark:border-gray-800 flex gap-3 bg-gray-50/50 dark:bg-gray-900/10">
                    @if ($intervention->rapport)
                        <a href="{{ route('rapports.show', $intervention->rapport) }}" class="flex-1 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-xl text-center hover:bg-indigo-700 transition text-sm">
                            Consulter le rapport
                        </a>
                        @if(auth()->user()->hasRole('Technicien'))
                            <a href="{{ route('rapports.edit', $intervention->rapport) }}" class="flex-1 bg-gray-600 text-white font-semibold py-2 px-4 rounded-xl text-center hover:bg-gray-700 transition text-sm">
                                Modifier le rapport
                            </a>
                        @endif
                    @else
                        @if(auth()->user()->hasRole('Technicien'))
                            <a href="{{ route('rapports.create', ['intervention_id' => $intervention->id]) }}" class="w-full bg-indigo-600 text-white font-semibold py-2.5 px-4 rounded-xl text-center hover:bg-indigo-700 transition text-sm shadow-sm">
                                Rédiger le rapport (Technicien)
                            </a>
                        @else
                            <div class="w-full text-center py-2 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-xl text-xs font-semibold">
                                En attente de rédaction par le technicien
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Matériaux consommés -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Matériaux consommés ({{ $intervention->materiaux->count() }})
                </h3>
                @if(isset($coutTotal) && $coutTotal > 0)
                    <span class="text-sm font-bold text-green-700 dark:text-green-400 bg-green-50 dark:bg-green-950/20 px-3 py-1 rounded-full">
                        Coût total : {{ number_format($coutTotal, 2, ',', ' ') }} MAD
                    </span>
                @endif
            </div>
            <div class="p-6 space-y-6">
                <!-- Formulaire d'ajout rapide de matériaux (Reservé aux Techniciens) -->
                @if(!in_array($intervention->statut, ['Terminee', 'Annulee']) && auth()->user()->hasRole('Technicien'))
                    <form method="POST" action="{{ route('interventions.materiaux.store', $intervention) }}" class="p-4 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-150 dark:border-gray-800">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label for="materiau_id" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Matériau</label>
                                <select name="materiau_id" id="materiau_id" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($materiaux as $mat)
                                        <option value="{{ $mat->id }}" {{ old('materiau_id') == $mat->id ? 'selected' : '' }}>
                                            {{ $mat->nom }} ({{ $mat->unite }}){{ $mat->prix_unitaire > 0 ? ' — ' . number_format($mat->prix_unitaire, 2, ',', ' ') . ' MAD/' . $mat->unite : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('materiau_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="quantite" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Quantité</label>
                                <input type="number" name="quantite" id="quantite" step="0.01" min="0.01" value="{{ old('quantite', 1) }}" required class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                                @error('quantite') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="commentaire" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Notes / Commentaire</label>
                                <input type="text" name="commentaire" id="commentaire" value="{{ old('commentaire') }}" placeholder="Ex: Tuyauterie..." class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-sm">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-indigo-600 text-white font-medium py-2 px-4 rounded-xl hover:bg-indigo-700 transition text-sm shadow-sm">
                                + Saisir Matériau Utilisé
                            </button>
                        </div>
                    </form>
                @endif

                <!-- Liste des matériaux -->
                @if($intervention->materiaux->isNotEmpty())
                    <div class="overflow-x-auto border border-gray-150 dark:border-gray-800 rounded-xl">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-800 border-b border-gray-150 dark:border-gray-850">
                                <tr>
                                    <th class="px-6 py-3">Matériau</th>
                                    <th class="px-6 py-3">Quantité</th>
                                    <th class="px-6 py-3">Prix unitaire</th>
                                    <th class="px-6 py-3">Coût total</th>
                                    <th class="px-6 py-3">Commentaire</th>
                                    @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                                        <th class="px-6 py-3">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($intervention->materiaux as $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $item->materiau->nom ?? 'Inconnu' }}</div>
                                            @if($item->materiau && $item->materiau->reference)
                                                <span class="text-xs text-gray-400 font-mono">{{ $item->materiau->reference }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->quantite }} {{ $item->unite ?? ($item->materiau->unite ?? '') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->materiau && $item->materiau->prix_unitaire > 0)
                                                {{ number_format($item->materiau->prix_unitaire, 2, ',', ' ') }} €
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                            @if($item->materiau && $item->materiau->prix_unitaire > 0)
                                                {{ number_format($item->quantite * $item->materiau->prix_unitaire, 2, ',', ' ') }} €
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{{ $item->commentaire ?? '-' }}</td>
                                        @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                                            <td class="px-6 py-4">
                                                <form method="POST" action="{{ route('interventions.materiaux.destroy', [$intervention, $item]) }}" onsubmit="return confirm('Retirer ce matériau ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-750 font-semibold transition text-sm">
                                                        Retirer
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Aucun matériau associé à cette intervention.</p>
                @endif
            </div>
        </div>

        <!-- Section Suivi GPS (lecture seule) et Carte du trajet -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sessions GPS -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Sessions GPS
                    </h3>
                    @if(!in_array($intervention->statut, ['Terminee', 'Annulee']))
                        @php $sessionGpsActive = $intervention->gpsTrackingSessions->firstWhere('ended_at', null); @endphp
                        @if($sessionGpsActive)
                            <form method="POST" action="{{ route('interventions.gpsTracking.stop', [$intervention, $sessionGpsActive]) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-red-500 text-white font-medium px-2 py-1 rounded hover:bg-red-650 transition">Arrêter</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('interventions.gpsTracking.start', $intervention) }}">
                                @csrf
                                <button type="submit" class="text-xs bg-indigo-600 text-white font-medium px-2 py-1 rounded hover:bg-indigo-700 transition">Démarrer</button>
                            </form>
                        @endif
                    @endif
                </div>
                <div class="p-6 overflow-y-auto max-h-96">
                    @if($intervention->gpsTrackingSessions->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($intervention->gpsTrackingSessions->sortByDesc('started_at') as $sessionGps)
                                @php
                                    $secondes = $sessionGps->dureeSecondes();
                                    $duree = sprintf('%02dh%02d', intdiv($secondes, 3600), intdiv($secondes % 3600, 60));
                                @endphp
                                <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-100 dark:border-gray-800 text-xs">
                                    <div class="flex justify-between font-semibold mb-1">
                                        <span class="text-gray-900 dark:text-white">Début : {{ $sessionGps->started_at->format('d/m H:i') }}</span>
                                        <span class="text-indigo-600 dark:text-indigo-400">{{ $duree }}</span>
                                    </div>
                                    <div class="text-gray-500 flex justify-between">
                                        <span>Fin : {{ $sessionGps->ended_at?->format('d/m H:i') ?? 'En cours' }}</span>
                                        <span>{{ number_format($sessionGps->distance_metres / 1000, 2, ',', ' ') }} km</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">Aucune session de suivi GPS enregistrée.</p>
                    @endif
                </div>
            </div>

            <!-- Carte Google Maps -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Tracé cartographique
                    </h3>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-950">
                    @php
                        $tousPointsGps = $intervention->gpsTrackingSessions->flatMap->points->sortBy('captured_at')->values();
                    @endphp
                    @if ($tousPointsGps->isNotEmpty())
                        <div id="carte-trajet-gps" class="w-full h-80 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800"></div>
                    @else
                        <div class="w-full h-80 rounded-xl border border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center text-gray-500 bg-white dark:bg-gray-900">
                            <svg class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            <p class="text-sm">Aucun point GPS enregistré pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historique des transitions -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                    <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Historique de l'intervention ({{ $intervention->historiques->count() }})
                </h3>
            </div>
            <div class="p-6">
                @if($intervention->historiques->isNotEmpty())
                    <div class="relative border-l border-gray-200 dark:border-gray-800 ml-4 space-y-6">
                        @foreach($intervention->historiques->sortByDesc('created_at') as $historique)
                            <div class="relative pl-6">
                                <!-- Point chronologique -->
                                <div class="absolute -left-1.5 top-1.5 h-3 w-3 rounded-full bg-indigo-600 ring-4 ring-white dark:ring-gray-900"></div>
                                <div class="text-sm">
                                    <span class="font-semibold text-gray-950 dark:text-white capitalize">{{ $historique->statut_depart }}</span>
                                    <span class="text-gray-500">→</span>
                                    <span class="font-semibold text-gray-950 dark:text-white capitalize">{{ $historique->statut_arrivee }}</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    Le {{ $historique->created_at->format('d/m/Y \à H:i') }} par {{ $historique->user->name ?? 'Système' }}
                                </div>
                                @if($historique->commentaire)
                                    <p class="text-xs text-gray-650 bg-gray-50 dark:bg-gray-800/40 p-2 rounded-lg border border-gray-100 dark:border-gray-800 mt-2 italic">
                                        "{{ $historique->commentaire }}"
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Aucun historique d'état disponible.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Scripts & CSS pour la carte Leaflet OpenStreetMap -->
    @if ($tousPointsGps->isNotEmpty())
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        @php
            $gpsPointsTrajet = $tousPointsGps->map(function ($point) {
                return [
                    'lat' => (float) $point->latitude,
                    'lng' => (float) $point->longitude,
                    'capturedAt' => optional($point->captured_at)->format('d/m/Y H:i:s'),
                ];
            });
        @endphp
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const gpsPointsTrajet = @json($gpsPointsTrajet);
                if (!gpsPointsTrajet || gpsPointsTrajet.length === 0) return;

                const chemin = gpsPointsTrajet.map(function (p) { return [p.lat, p.lng]; });
                const map = L.map('carte-trajet-gps').setView(chemin[0], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                const bounds = L.latLngBounds();
                chemin.forEach(function (p) { bounds.extend(p); });

                L.polyline(chemin, {
                    color: '#4F46E5',
                    weight: 5,
                    opacity: 0.9,
                    lineCap: 'round'
                }).addTo(map);

                L.circleMarker(chemin[0], {
                    radius: 9,
                    fillColor: '#10B981',
                    color: '#FFFFFF',
                    weight: 2,
                    fillOpacity: 1
                }).addTo(map).bindPopup("<b>Départ</b><br>" + gpsPointsTrajet[0].capturedAt);

                L.circleMarker(chemin[chemin.length - 1], {
                    radius: 10,
                    fillColor: '#EF4444',
                    color: '#FFFFFF',
                    weight: 3,
                    fillOpacity: 1
                }).addTo(map).bindPopup("<b>Dernière Position</b><br>" + gpsPointsTrajet[gpsPointsTrajet.length - 1].capturedAt);

                map.fitBounds(bounds, { padding: [30, 30] });
            });
        </script>
    @endif

    <!-- Suivi GPS automatique périodique en tâche de fond -->
    @if(isset($sessionGpsActive) && $sessionGpsActive && auth()->id() === $intervention->technicien_id && $intervention->statut === 'En cours')
        <script>
            (function () {
                const pointUrl = "{{ route('interventions.gpsTracking.points.store', [$intervention, $sessionGpsActive]) }}";
                const csrfToken = "{{ csrf_token() }}";

                function envoyerPosition(position) {
                    fetch(pointUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                        }),
                    });
                }

                if (navigator.geolocation) {
                    setInterval(function () {
                        navigator.geolocation.getCurrentPosition(envoyerPosition, function () {}, { enableHighAccuracy: true });
                    }, 30000);
                }
            })();
        </script>
    @endif
</x-app-layout>
