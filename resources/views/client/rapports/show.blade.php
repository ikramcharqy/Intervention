<x-client-layout>
    <x-slot name="header">Rapport {{ $rapport->intervention?->code_intervention }}</x-slot>

    <div class="space-y-6">

        <!-- Fil d'Ariane local (niveau intermédiaire manquant, même souci déjà signalé sur la fiche Chantier) -->
        <nav class="flex items-center gap-1.5 text-[11px] text-slate-400">
            <a href="{{ route('client.interventions.index') }}" class="hover:text-emerald-600 transition">Mes Interventions</a>
            <i class="fas fa-chevron-right text-[8px]"></i>
            @if($rapport->intervention)
                <a href="{{ route('client.interventions.show', $rapport->intervention) }}" class="hover:text-emerald-600 transition">{{ $rapport->intervention->code_intervention }}</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
            @endif
            <span class="text-slate-600 font-semibold">Rapport</span>
        </nav>

        <!-- Header -->
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-file-lines text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-extrabold text-[#1e2530]">Rapport {{ $rapport->intervention?->code_intervention ?? '#'.$rapport->id }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Généré le {{ $rapport->created_at->format('d/m/Y H:i') }} pour le chantier "{{ $rapport->intervention?->chantier?->nom }}"
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($rapport->intervention)
                    <a href="{{ route('client.interventions.show', $rapport->intervention) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white shadow-sm hover:shadow text-slate-600 text-xs font-bold transition">
                        <i class="fas fa-screwdriver-wrench"></i> Voir l'intervention
                    </a>
                @endif
                <a href="{{ route('client.rapports.pdf', $rapport) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </a>
            </div>
        </div>

        <!-- Statut de validation persistant -->
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between gap-4 flex-wrap">
            @if($rapport->estValide())
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i class="fas fa-circle-check"></i> Validé
                    </span>
                    <span class="text-xs text-slate-500">
                        le {{ $rapport->validated_at?->format('d/m/Y à H:i') }}
                        @if($rapport->validateur) par {{ $rapport->validateur->name }} @endif
                    </span>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                        <i class="fas fa-clock"></i> En attente de validation
                    </span>
                    <span class="text-xs text-slate-400">Confirmez que ce rapport correspond aux travaux réalisés.</span>
                </div>
                <form action="{{ route('client.rapports.valider', $rapport) }}" method="POST" onsubmit="return confirm('Confirmer la validation de ce rapport ?');">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition">
                        <i class="fas fa-check"></i> Valider le rapport
                    </button>
                </form>
            @endif
        </div>

        <!-- Résumé -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Résumé de l'intervention</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-5">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Technicien</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $rapport->intervention?->technicien?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Durée réelle</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $rapport->intervention?->duree_reelle ? $rapport->intervention->duree_reelle.' min' : '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date de clôture</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $rapport->intervention?->date_reelle_fin?->format('d/m/Y H:i') ?? $rapport->created_at->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Type d'intervention</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $rapport->intervention?->typeIntervention?->nom ?? '—' }}</p>
                </div>
            </div>
        </div>

        <!-- Travaux réalisés / Observations terrain / Recommandations / État équipement
             (uniquement les champs de niveau Rapport — les consignes internes de
             planification (Intervention::description/observations) ne sont pas des
             données destinées au Client et restent exclues de cette fiche). -->
        @if($rapport->travaux_effectues || $rapport->observations || $rapport->recommandations || $rapport->statut_equipement)
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h2 class="text-sm font-extrabold text-[#1e2530]">Travaux réalisés & observations</h2>

                @if($rapport->travaux_effectues)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Travaux réalisés</p>
                        <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 leading-relaxed">{{ $rapport->travaux_effectues }}</p>
                    </div>
                @endif

                @if($rapport->statut_equipement)
                    @php
                        $equipColor = match($rapport->statut_equipement) {
                            'Conforme' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'Remplacé' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'Partiellement conforme' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-rose-50 text-rose-700 border-rose-200',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $equipColor }}">
                        <i class="fas fa-toolbox"></i> État de l'équipement : {{ $rapport->statut_equipement }}
                    </span>
                @endif

                @if($rapport->observations)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Observations terrain</p>
                        <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 leading-relaxed">{{ $rapport->observations }}</p>
                    </div>
                @endif

                @if($rapport->recommandations)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Recommandations</p>
                        <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 leading-relaxed">{{ $rapport->recommandations }}</p>
                    </div>
                @endif

                @if($rapport->commentaire)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Commentaire</p>
                        <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 leading-relaxed whitespace-pre-wrap">{{ $rapport->commentaire }}</p>
                    </div>
                @endif
            </div>
        @endif

        <!-- Géolocalisation & QR Code terrain -->
        @if(($rapport->gps_latitude && $rapport->gps_longitude) || $rapport->qrcode_scanne || $rapport->intervention?->trackingSessions?->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Localisation & présence sur site</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @if($rapport->gps_latitude && $rapport->gps_longitude)
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Position GPS terrain</p>
                            <p class="text-sm font-semibold text-[#1e2530] mt-1 font-mono">{{ $rapport->gps_latitude }}, {{ $rapport->gps_longitude }}</p>
                            @if($rapport->gps_adresse)<p class="text-[11px] text-slate-400">{{ $rapport->gps_adresse }}</p>@endif
                        </div>
                    @endif
                    @if($rapport->qrcode_scanne)
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">QR Code équipement scanné</p>
                            <p class="text-sm font-semibold text-[#1e2530] mt-1 font-mono">{{ $rapport->qrcode_scanne }}</p>
                        </div>
                    @endif
                    @if($tracking = $rapport->intervention?->trackingSessions?->first())
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mode de suivi de présence</p>
                            <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $tracking->mode }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Réponses au formulaire dynamique (composant partagé avec Admin/Technicien) -->
        @if($rapport->reponses->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Réponses au formulaire dynamique</h2>
                <x-rapport-formulaire :rapport="$rapport" />
            </div>
        @endif

        <!-- Photos, groupées par type comme côté Admin/Technicien -->
        @if($rapport->photos->isNotEmpty())
            @php
                $photosParType = $rapport->photos->groupBy(fn($p) => $p->type_photo ?: 'autre');
                $typeLabels = ['avant' => 'Avant', 'apres' => 'Après', 'probleme' => 'Problème', 'autre' => 'Autres'];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Photos ({{ $rapport->photos->count() }})</h2>
                <div class="space-y-4">
                    @foreach($photosParType as $type => $photos)
                        <div>
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">{{ $typeLabels[$type] ?? $type }} ({{ $photos->count() }})</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($photos as $photo)
                                    <a href="{{ Storage::url($photo->chemin) }}" target="_blank" class="block aspect-square rounded-lg overflow-hidden border border-slate-100 hover:opacity-90 transition">
                                        <img src="{{ Storage::url($photo->chemin) }}" class="w-full h-full object-cover">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Vidéos -->
        @if($rapport->videos->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Vidéos ({{ $rapport->videos->count() }})</h2>
                <div class="space-y-2">
                    @foreach($rapport->videos as $video)
                        <a href="{{ Storage::url($video->chemin) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition text-xs">
                            <i class="fas fa-video text-slate-400"></i>
                            <span class="flex-1 truncate font-semibold text-slate-700">{{ $video->nom_original ?? basename($video->chemin) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Documents -->
        @if($rapport->documents->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Documents joints ({{ $rapport->documents->count() }})</h2>
                <div class="space-y-2">
                    @foreach($rapport->documents as $doc)
                        <a href="{{ route('client.documents.download', $doc) }}" class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 hover:bg-slate-100 transition text-xs">
                            <i class="fas fa-file text-slate-400"></i>
                            <span class="flex-1 truncate font-semibold text-slate-700">{{ $doc->nom_original }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Matériaux utilisés -->
        @if($rapport->intervention?->materiaux?->isNotEmpty())
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 pb-4">
                    <h2 class="text-sm font-extrabold text-[#1e2530]">Matériaux utilisés</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                                <th class="py-3 pl-6 pr-4">Matériau</th>
                                <th class="py-3 px-4">Quantité</th>
                                <th class="py-3 pr-6 pl-4">Commentaire</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rapport->intervention->materiaux as $item)
                                <tr>
                                    <td class="py-3 pl-6 pr-4 font-semibold text-slate-800">{{ $item->materiau->nom ?? '-' }}</td>
                                    <td class="py-3 px-4 text-slate-600">{{ $item->quantite }} {{ $item->unite }}</td>
                                    <td class="py-3 pr-6 pl-4 text-slate-600">{{ $item->commentaire ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Signatures -->
        <div class="bg-white rounded-2xl shadow-sm p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-bold text-slate-700 mb-2">Signature Technicien</p>
                @if($rapport->signature_technicien)
                    @if(in_array(pathinfo($rapport->signature_technicien, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($rapport->signature_technicien) }}" class="max-h-24 mx-auto">
                    @else
                        <p class="text-slate-400 text-xs">{{ $rapport->signature_technicien }}</p>
                    @endif
                @else
                    <span class="text-slate-400 text-xs italic">Non signée</span>
                @endif
            </div>
            <div class="border-2 border-dashed border-slate-200 rounded-xl p-4 text-center">
                <p class="text-xs font-bold text-slate-700 mb-2">Signature Client</p>
                @if($rapport->signature_client)
                    @if(in_array(pathinfo($rapport->signature_client, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                        <img src="{{ Storage::url($rapport->signature_client) }}" class="max-h-24 mx-auto">
                    @else
                        <p class="text-slate-400 text-xs">{{ $rapport->signature_client }}</p>
                    @endif
                @else
                    <span class="text-slate-400 text-xs italic">Non signée</span>
                @endif
            </div>
        </div>

    </div>
</x-client-layout>
