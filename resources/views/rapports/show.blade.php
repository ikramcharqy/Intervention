<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Rapport de l'Intervention : {{ $rapport->intervention->code_intervention ?? 'N/A' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Statut de validation --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                @if (($rapport->intervention->statut ?? '') === 'Terminee')
                    <div class="bg-green-50 dark:bg-green-900 border border-green-200 rounded-md p-4 flex items-center gap-3">
                        <span class="text-green-600 text-xl">✓</span>
                        <div>
                            <p class="font-semibold text-green-700 dark:text-green-300">Rapport validé (Intervention Terminée)</p>
                            <p class="text-sm text-green-600 dark:text-green-400">Le client peut maintenant consulter ce rapport.</p>
                        </div>
                    </div>
                @else
                    <div class="bg-orange-50 dark:bg-orange-900 border border-orange-200 rounded-md p-4 flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-orange-700 dark:text-orange-300">⏳ En attente de validation</p>
                            <p class="text-sm text-orange-600 dark:text-orange-400">Le statut actuel de l'intervention est : {{ $rapport->intervention->statut ?? 'N/A' }}.</p>
                        </div>
                        @if (($rapport->intervention->statut ?? '') === 'Formulaire rempli' && auth()->user()->hasAnyRole(['Administrateur', 'Super Admin']))
                            <form method="POST" action="{{ route('interventions.validate', $rapport->intervention) }}">
                                @csrf
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 text-sm">
                                    Valider l'Intervention & le Rapport
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Contenu du rapport --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 space-y-6">
                
                {{-- Entête & Téléchargement --}}
                <div class="flex justify-between items-center border-b border-gray-200 dark:border-gray-700 pb-4">
                    <div>
                        <h3 class="text-lg font-semibold">Détails de l'intervention</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Rapport généré le {{ $rapport->created_at->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('rapports.pdf', $rapport) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-semibold flex items-center gap-2">
                        📥 Télécharger PDF
                    </a>
                </div>

                {{-- 1. Informations d'intervention --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="mb-2"><strong>Code Intervention :</strong> <a href="{{ route('interventions.show', $rapport->intervention) }}" class="text-indigo-500 hover:underline">{{ $rapport->intervention->code_intervention ?? '-' }}</a></p>
                        <p class="mb-2"><strong>Client :</strong> {{ $rapport->intervention->chantier->client->nom ?? '-' }}</p>
                        <p class="mb-2"><strong>Chantier :</strong> {{ $rapport->intervention->chantier->nom ?? '-' }}</p>
                        <p class="mb-2"><strong>Emplacement :</strong> {{ $rapport->intervention->emplacement->nom ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="mb-2"><strong>Technicien rédacteur :</strong> {{ $rapport->intervention->technicien->prenom ?? '' }} {{ $rapport->intervention->technicien->name ?? '-' }}</p>
                        <p class="mb-2"><strong>Type d'intervention :</strong> {{ $rapport->intervention->typeIntervention->nom ?? '-' }}</p>
                        <p class="mb-2"><strong>Priorité :</strong> {{ $rapport->intervention->priorite ?? '-' }}</p>
                        <p class="mb-2"><strong>Statut final :</strong> {{ $rapport->intervention->statut ?? '-' }}</p>
                    </div>
                </div>

                {{-- 2. Dates et Heures --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3">Dates & Suivi de Présence</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="mb-2"><strong>Début prévu :</strong> {{ $rapport->intervention->date_prevue_debut ? $rapport->intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</p>
                            <p class="mb-2"><strong>Fin prévue :</strong> {{ $rapport->intervention->date_prevue_fin ? $rapport->intervention->date_prevue_fin->format('d/m/Y H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="mb-2"><strong>Début réel :</strong> {{ $rapport->intervention->date_reelle_debut ? $rapport->intervention->date_reelle_debut->format('d/m/Y H:i') : '-' }}</p>
                            <p class="mb-2"><strong>Fin réelle :</strong> {{ $rapport->intervention->date_reelle_fin ? $rapport->intervention->date_reelle_fin->format('d/m/Y H:i') : '-' }}</p>
                            <p class="mb-2"><strong>Durée réelle :</strong> {{ $rapport->intervention->duree_reelle ? $rapport->intervention->duree_reelle . ' min' : '-' }}</p>
                        </div>
                    </div>

                    @php
                        $tracking = $rapport->intervention->trackingSessions->first();
                    @endphp
                    @if($tracking)
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-sm">
                            <p class="font-semibold mb-1">Suivi de présence physique :</p>
                            <p><strong>Mode :</strong> {{ $tracking->mode }}</p>
                            @if($tracking->latitude && $tracking->longitude)
                                <p><strong>GPS :</strong> Latitude {{ $tracking->latitude }}, Longitude {{ $tracking->longitude }}</p>
                            @endif
                            @if($tracking->qr_code_scan)
                                <p><strong>QR Code validé :</strong> {{ $tracking->qr_code_scan }}</p>
                            @endif
                        </div>
                    @elseif($rapport->intervention->emplacement)
                        <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded-md text-sm">
                            <p class="font-semibold mb-1">Coordonnées Emplacement prévues :</p>
                            @if($rapport->intervention->emplacement->latitude && $rapport->intervention->emplacement->longitude)
                                <p><strong>GPS :</strong> Latitude {{ $rapport->intervention->emplacement->latitude }}, Longitude {{ $rapport->intervention->emplacement->longitude }}</p>
                            @endif
                            @if($rapport->intervention->emplacement->qr_code)
                                <p><strong>QR Code de l'emplacement :</strong> {{ $rapport->intervention->emplacement->qr_code }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 3. Réponses au questionnaire --}}
                @if($rapport->reponses->isNotEmpty())
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-4">Réponses au Formulaire Dynamique</h4>
                        <div class="space-y-4">
                            @foreach($rapport->reponses as $reponse)
                                @php $question = $reponse->question; @endphp
                                @if($question)
                                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                                        <p class="font-medium text-gray-800 dark:text-gray-200 mb-2">{{ $question->question }}</p>
                                        
                                        @if(in_array($question->type_reponse, ['Photo', 'Signature', 'Document']))
                                            @if($reponse->reponse_fichier)
                                                @if(in_array(pathinfo($reponse->reponse_fichier, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($reponse->reponse_fichier) }}" class="max-w-xs h-auto border rounded-md shadow-sm">
                                                    </div>
                                                @else
                                                    <a href="{{ Storage::url($reponse->reponse_fichier) }}" target="_blank" class="text-indigo-500 hover:underline">
                                                        Télécharger le fichier ({{ basename($reponse->reponse_fichier) }})
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-gray-400">Aucun fichier</span>
                                            @endif
                                        @elseif($question->type_reponse === 'OuiNon')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $reponse->reponse_texte === '1' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $reponse->reponse_texte === '1' ? 'Oui' : 'Non' }}
                                            </span>
                                        @elseif($question->type_reponse === 'Checkbox')
                                            @php
                                                $choices = \App\Models\ChoixQuestion::whereIn('id', is_array($reponse->reponse_texte) ? $reponse->reponse_texte : explode(',', $reponse->reponse_texte))->pluck('valeur')->toArray();
                                            @endphp
                                            <span class="text-gray-600 dark:text-gray-300">{{ implode(', ', $choices) }}</span>
                                        @elseif($question->type_reponse === 'Radio' || $question->type_reponse === 'Liste')
                                            <span class="text-gray-600 dark:text-gray-300">{{ $reponse->choixQuestion->valeur ?? $reponse->reponse_texte }}</span>
                                        @else
                                            <span class="text-gray-600 dark:text-gray-300">{{ $reponse->reponse_texte ?? $reponse->reponse_nombre ?? '-' }}</span>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- 3bis. Matériaux utilisés --}}
                @if($rapport->intervention->materiaux->isNotEmpty())
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-4">Matériaux utilisés</h4>
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 dark:text-gray-400">
                                    <th class="px-2 py-1">Matériau</th>
                                    <th class="px-2 py-1">Quantité</th>
                                    <th class="px-2 py-1">Commentaire</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($rapport->intervention->materiaux as $item)
                                    <tr>
                                        <td class="px-2 py-1">{{ $item->materiau->nom ?? '-' }}</td>
                                        <td class="px-2 py-1">{{ $item->quantite }} {{ $item->unite }}</td>
                                        <td class="px-2 py-1">{{ $item->commentaire ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- 4. Observations / Description --}}
                @if($rapport->commentaire || $rapport->intervention->description || $rapport->intervention->observations)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                        <h4 class="font-semibold text-gray-700 dark:text-gray-300">Observations & Notes</h4>
                        @if($rapport->intervention->description)
                            <div>
                                <p class="text-xs text-gray-400">Description initiale de l'intervention :</p>
                                <p class="text-sm italic text-gray-600 dark:text-gray-300">{{ $rapport->intervention->description }}</p>
                            </div>
                        @endif
                        @if($rapport->intervention->observations)
                            <div>
                                <p class="text-xs text-gray-400">Consignes de planification :</p>
                                <p class="text-sm italic text-gray-600 dark:text-gray-300">{{ $rapport->intervention->observations }}</p>
                            </div>
                        @endif
                        @if($rapport->commentaire)
                            <div>
                                <p class="text-xs text-gray-400">Commentaires rédigés dans le rapport :</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ $rapport->commentaire }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 5. Signatures du rapport --}}
                <div class="border-t border-gray-200 dark:border-gray-700 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-dashed border-gray-300 dark:border-gray-600 rounded-md p-4 text-center">
                        <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Signature Technicien</h5>
                        @if($rapport->signature_technicien)
                            @if(in_array(pathinfo($rapport->signature_technicien, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ Storage::url($rapport->signature_technicien) }}" class="max-h-24 mx-auto">
                            @else
                                <p class="font-mono text-gray-500 text-xs">{{ $rapport->signature_technicien }}</p>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">Non signée</span>
                        @endif
                    </div>
                    <div class="border border-dashed border-gray-300 dark:border-gray-600 rounded-md p-4 text-center">
                        <h5 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Signature Client</h5>
                        @if($rapport->signature_client)
                            @if(in_array(pathinfo($rapport->signature_client, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ Storage::url($rapport->signature_client) }}" class="max-h-24 mx-auto">
                            @else
                                <p class="font-mono text-gray-500 text-xs">{{ $rapport->signature_client }}</p>
                            @endif
                        @else
                            <span class="text-gray-400 text-sm">Non signée</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Actions --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex flex-wrap gap-3">
                    @can('update rapports')
                        @if($rapport->intervention->statut !== 'Terminee')
                            <a href="{{ route('rapports.edit', $rapport) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600 text-sm">Modifier</a>
                        @endif
                    @endcan

                    @can('delete rapports')
                        @if($rapport->intervention->statut !== 'Terminee')
                            <form method="POST" action="{{ route('rapports.destroy', $rapport) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 text-sm">Supprimer</button>
                            </form>
                        @endif
                    @endcan

                    <a href="{{ route('rapports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 text-sm">Retour</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
