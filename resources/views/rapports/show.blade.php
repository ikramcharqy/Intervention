<x-app-layout>
    <div class="space-y-6">
        @php
            $intervention = $rapport->intervention;
            $code = $intervention->code_intervention ?? 'N/A';
        @endphp

        <!-- Fil d'Ariane + Titre -->
        <div>
            <nav class="flex items-center gap-1.5 text-[11px] text-[#A1A7C4] mb-2">
                <a href="{{ route('rapports.index') }}" class="hover:text-[#1E5EFF] transition">Rapports & Validation</a>
                <i class="ti ti-chevron-right text-[10px]"></i>
                <span class="text-[#5A607F] font-semibold">{{ $code }}</span>
            </nav>
            <div class="flex items-center gap-3 flex-wrap">
                <a href="{{ route('rapports.index') }}" class="w-9 h-9 rounded-[4px] border border-[#E6E9F4] flex items-center justify-center text-[#5A607F] hover:bg-[#F5F6FA] transition shrink-0">
                    <i class="ti ti-arrow-left text-sm"></i>
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Rapport de l'intervention</h1>
                <span class="font-bold text-sm px-2.5 py-1 rounded-[4px] bg-[#EAF0FF] text-[#1E5EFF] border border-[#D9E4FF]">{{ $code }}</span>
            </div>
        </div>

        <!-- Bandeau de statut -->
        @php
            $theme = $statutBadge['theme'];
            $themeColors = [
                'success' => ['bg' => '#E3FBF0', 'border' => '#C4F8E2', 'text' => '#06A561'],
                'warning' => ['bg' => '#FFF3DE', 'border' => '#FFE7B8', 'text' => '#B98900'],
                'danger'  => ['bg' => '#FDE3E6', 'border' => '#F8C4CA', 'text' => '#F0142F'],
            ][$theme];
        @endphp
        <div class="ds-card-elevated p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4" style="background-color: {{ $themeColors['bg'] }}; border-color: {{ $themeColors['border'] }};">
            <div class="flex items-center gap-3.5">
                <span class="w-10 h-10 rounded-full flex items-center justify-center shrink-0" style="background-color: {{ $themeColors['border'] }}; color: {{ $themeColors['text'] }};">
                    <i class="ti {{ $statutBadge['icon'] }} text-lg"></i>
                </span>
                <div>
                    <p class="font-bold text-sm" style="color: {{ $themeColors['text'] }};">{{ $statutBadge['label'] }}</p>
                    <p class="text-xs mt-0.5" style="color: {{ $themeColors['text'] }};">{{ $statutBadge['description'] }}</p>
                </div>
            </div>
            @if (($intervention->statut ?? '') === 'Formulaire rempli' && auth()->user()->hasAnyRole(['Administrateur', 'Super Admin']))
                <form method="POST" action="{{ route('interventions.validate', $intervention) }}">
                    @csrf
                    <button type="submit" class="ds-btn ds-btn-sm" style="background-color:#06A561; color:#fff;">
                        <i class="ti ti-circle-check"></i> Valider l'Intervention & le Rapport
                    </button>
                </form>
            @endif
        </div>

        <!-- Détails de l'intervention -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-[#E6E9F4]">
                <div>
                    <h3 class="text-sm font-bold text-[#131523]">Détails de l'intervention</h3>
                    <p class="text-xs text-[#A1A7C4] mt-0.5">Rapport généré le {{ $rapport->created_at->format('d/m/Y') }}</p>
                </div>
                <a href="{{ route('rapports.pdf', $rapport) }}" class="ds-btn ds-btn-primary ds-btn-sm">
                    <i class="ti ti-file-download"></i> Télécharger PDF
                </a>
            </div>

            <div class="p-6 space-y-8">
                <!-- Informations générales -->
                <div>
                    <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Informations générales</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="flex items-start gap-3">
                            <i class="ti ti-hash text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Code Intervention</p>
                                <a href="{{ route('interventions.show', $intervention) }}" class="text-sm font-semibold text-[#1E5EFF] hover:underline">{{ $code }}</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-user text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Client</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->chantier->client->nom ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-building text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Chantier</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->chantier->nom ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-map-pin text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Emplacement</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->emplacement->nom ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suivi opérationnel -->
                <div class="pt-6 border-t border-[#E6E9F4]">
                    <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Suivi opérationnel</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="flex items-start gap-3">
                            <i class="ti ti-tool text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Technicien rédacteur</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->technicien->prenom ?? '' }} {{ $intervention->technicien->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-category text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Type d'intervention</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->typeIntervention->nom ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-flag text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Priorité</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->priorite ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-info-circle text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Statut final</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->statut ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dates & présence -->
                <div class="pt-6 border-t border-[#E6E9F4]">
                    <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Dates &amp; présence</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        <div class="flex items-start gap-3">
                            <i class="ti ti-calendar text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Début prévu</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-calendar-event text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Début réel</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->date_reelle_debut ? $intervention->date_reelle_debut->format('d/m/Y H:i') : '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-calendar text-[#A1A7C4] text-base mt-0.5"></i>
                            <div>
                                <p class="text-[11px] text-[#5A607F]">Fin prévue</p>
                                <p class="text-sm font-semibold text-[#131523]">{{ $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('d/m/Y H:i') : '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i class="ti ti-calendar-check text-[#A1A7C4] text-base mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-[11px] text-[#5A607F]">Fin réelle</p>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <p class="text-sm font-semibold text-[#131523]">{{ $intervention->date_reelle_fin ? $intervention->date_reelle_fin->format('d/m/Y H:i') : '-' }}</p>
                                    @if($intervention->date_prevue_fin && $intervention->date_reelle_fin)
                                        @php
                                            // Positif = fin réelle après la fin prévue (retard) ; négatif = avant (en avance).
                                            $ecartMinutes = intdiv($intervention->date_reelle_fin->getTimestamp() - $intervention->date_prevue_fin->getTimestamp(), 60);
                                        @endphp
                                        @if($ecartMinutes < 0)
                                            <span class="ds-badge ds-badge-sm ds-badge-light-success">
                                                <i class="ti ti-trending-down text-[10px] mr-1"></i> En avance ({{ abs($ecartMinutes) }} min)
                                            </span>
                                        @elseif($ecartMinutes > 0)
                                            <span class="ds-badge ds-badge-sm ds-badge-light-warning">
                                                <i class="ti ti-trending-up text-[10px] mr-1"></i> Retard de {{ $ecartMinutes }} min
                                            </span>
                                        @else
                                            <span class="ds-badge ds-badge-sm ds-badge-light-success">À l'heure</span>
                                        @endif
                                    @endif
                                </div>
                                @if($intervention->duree_reelle)
                                    <p class="text-[11px] text-[#A1A7C4] mt-0.5">Durée réelle : {{ $intervention->duree_reelle }} min</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @php
                        $tracking = $intervention->trackingSessions->first();
                    @endphp
                    @if($tracking)
                        <div class="mt-5 p-4 rounded-[6px] bg-[#F5F6FA] border border-[#E6E9F4] text-xs">
                            <p class="font-bold text-[#131523] mb-1.5">Suivi de présence physique</p>
                            <p class="text-[#5A607F]"><span class="font-semibold text-[#131523]">Mode :</span> {{ $tracking->mode }}</p>
                            @if($tracking->latitude && $tracking->longitude)
                                <p class="text-[#5A607F]"><span class="font-semibold text-[#131523]">GPS :</span> Latitude {{ $tracking->latitude }}, Longitude {{ $tracking->longitude }}</p>
                            @endif
                            @if($tracking->qr_code_scan)
                                <p class="text-[#5A607F]"><span class="font-semibold text-[#131523]">QR Code validé :</span> {{ $tracking->qr_code_scan }}</p>
                            @endif
                        </div>
                    @elseif($intervention->emplacement)
                        <div class="mt-5 p-4 rounded-[6px] bg-[#F5F6FA] border border-[#E6E9F4] text-xs">
                            <p class="font-bold text-[#131523] mb-1.5">Coordonnées Emplacement prévues</p>
                            @if($intervention->emplacement->latitude && $intervention->emplacement->longitude)
                                <p class="text-[#5A607F]"><span class="font-semibold text-[#131523]">GPS :</span> Latitude {{ $intervention->emplacement->latitude }}, Longitude {{ $intervention->emplacement->longitude }}</p>
                            @endif
                            @if($intervention->emplacement->qr_code)
                                <p class="text-[#5A607F]"><span class="font-semibold text-[#131523]">QR Code de l'emplacement :</span> {{ $intervention->emplacement->qr_code }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Travaux réalisés (saisis via l'app terrain) -->
                @if($rapport->travaux_effectues || $rapport->statut_equipement)
                    <div class="pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Travaux réalisés</h4>
                        @if($rapport->travaux_effectues)
                            <p class="text-sm text-[#131523] leading-relaxed">{{ $rapport->travaux_effectues }}</p>
                        @endif
                        @if($rapport->statut_equipement)
                            @php
                                $equipTheme = match($rapport->statut_equipement) {
                                    'Conforme' => 'success',
                                    'Remplacé' => 'primary',
                                    'Partiellement conforme' => 'warning',
                                    default => 'danger',
                                };
                            @endphp
                            <span class="ds-badge ds-badge-sm ds-badge-light-{{ $equipTheme }} mt-3 inline-flex">
                                <i class="ti ti-tool text-[10px] mr-1.5"></i> État de l'équipement : {{ $rapport->statut_equipement }}
                            </span>
                        @endif
                    </div>
                @endif

                <!-- Observations & Recommandations terrain -->
                @if($rapport->observations || $rapport->recommandations)
                    <div class="pt-6 border-t border-[#E6E9F4] space-y-4">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider">Observations &amp; recommandations terrain</h4>
                        @if($rapport->observations)
                            <div>
                                <p class="text-[11px] text-[#A1A7C4] mb-1">Observations terrain</p>
                                <p class="text-sm text-[#5A607F]">{{ $rapport->observations }}</p>
                            </div>
                        @endif
                        @if($rapport->recommandations)
                            <div>
                                <p class="text-[11px] text-[#A1A7C4] mb-1">Recommandations pour le suivi</p>
                                <p class="text-sm text-[#5A607F]">{{ $rapport->recommandations }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Géolocalisation & QR Code (saisis au moment du rapport) -->
                @if(($rapport->gps_latitude && $rapport->gps_longitude) || $rapport->qrcode_scanne)
                    <div class="pt-6 border-t border-[#E6E9F4] grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        @if($rapport->gps_latitude && $rapport->gps_longitude)
                            <div class="flex items-start gap-3">
                                <i class="ti ti-map-pin text-[#A1A7C4] text-base mt-0.5"></i>
                                <div>
                                    <p class="text-[11px] text-[#5A607F]">Position GPS terrain</p>
                                    <p class="text-sm font-semibold text-[#131523] font-mono">{{ $rapport->gps_latitude }}, {{ $rapport->gps_longitude }}</p>
                                    @if($rapport->gps_adresse)
                                        <p class="text-[11px] text-[#A1A7C4]">{{ $rapport->gps_adresse }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($rapport->qrcode_scanne)
                            <div class="flex items-start gap-3">
                                <i class="ti ti-qrcode text-[#A1A7C4] text-base mt-0.5"></i>
                                <div>
                                    <p class="text-[11px] text-[#5A607F]">QR Code équipement scanné</p>
                                    <p class="text-sm font-semibold text-[#131523] font-mono">{{ $rapport->qrcode_scanne }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Photos d'intervention (Avant / Après / Problème) -->
                @if($rapport->photos->isNotEmpty())
                    <div class="pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Photos d'intervention</h4>
                        @php
                            $photosParType = $rapport->photos->groupBy(fn($p) => $p->type_photo ?: 'autre');
                            $typeLabels = ['avant' => 'Avant', 'apres' => 'Après', 'probleme' => 'Problème', 'autre' => 'Autres'];
                        @endphp
                        <div class="space-y-4">
                            @foreach($photosParType as $type => $photos)
                                <div>
                                    <p class="text-[11px] font-bold text-[#5A607F] uppercase tracking-wider mb-2">{{ $typeLabels[$type] ?? $type }} ({{ $photos->count() }})</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                        @foreach($photos as $photo)
                                            <a href="{{ Storage::url($photo->chemin) }}" target="_blank">
                                                <img src="{{ Storage::url($photo->chemin) }}" class="w-full aspect-square object-cover rounded-[6px] border border-[#E6E9F4]">
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Vidéos d'intervention -->
                @if($rapport->videos->isNotEmpty())
                    <div class="pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Vidéos de démonstration</h4>
                        <div class="space-y-2">
                            @foreach($rapport->videos as $video)
                                <a href="{{ Storage::url($video->chemin) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-[6px] bg-[#F5F6FA] border border-[#E6E9F4] text-xs text-[#1E5EFF] hover:bg-[#EAF0FF] transition">
                                    <i class="ti ti-video text-base"></i>
                                    <span class="flex-1 truncate">{{ $video->nom_original ?? basename($video->chemin) }}</span>
                                    @if($video->duree)
                                        <span class="text-[#A1A7C4]">{{ gmdate('i:s', $video->duree) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Documents joints -->
                @if($rapport->documents->isNotEmpty())
                    <div class="pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Documents joints</h4>
                        <div class="space-y-2">
                            @foreach($rapport->documents as $document)
                                <a href="{{ Storage::url($document->chemin) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-[6px] bg-[#F5F6FA] border border-[#E6E9F4] text-xs text-[#1E5EFF] hover:bg-[#EAF0FF] transition">
                                    <i class="ti ti-file text-base"></i>
                                    <span class="flex-1 truncate">{{ $document->nom_original ?? basename($document->chemin) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Réponses au questionnaire (composant partagé avec le portail Client, x-rapport-formulaire) -->
                @if($rapport->reponses->isNotEmpty())
                    <div class="pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Réponses au formulaire dynamique</h4>
                        <x-rapport-formulaire :rapport="$rapport" />
                    </div>
                @endif

                <!-- Matériaux utilisés -->
                @if($intervention->materiaux->isNotEmpty())
                    <div class="pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider mb-4">Matériaux utilisés</h4>
                        <div class="overflow-x-auto">
                            <table class="ds-table">
                                <thead>
                                    <tr>
                                        <th>Matériau</th>
                                        <th>Quantité</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($intervention->materiaux as $item)
                                        <tr>
                                            <td class="text-xs font-semibold text-[#131523]">{{ $item->materiau->nom ?? '-' }}</td>
                                            <td class="text-xs text-[#5A607F]">{{ $item->quantite }} {{ $item->unite }}</td>
                                            <td class="text-xs text-[#5A607F]">{{ $item->commentaire ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Contexte de planification & commentaire interne -->
                @if($rapport->commentaire || $intervention->description || $intervention->observations)
                    <div class="pt-6 border-t border-[#E6E9F4] space-y-4">
                        <h4 class="text-xs font-bold text-[#A1A7C4] uppercase tracking-wider">Contexte &amp; commentaire interne</h4>
                        @if($intervention->description)
                            <div>
                                <p class="text-[11px] text-[#A1A7C4] mb-1">Description initiale de l'intervention</p>
                                <p class="text-sm text-[#5A607F] italic">{{ $intervention->description }}</p>
                            </div>
                        @endif
                        @if($intervention->observations)
                            <div>
                                <p class="text-[11px] text-[#A1A7C4] mb-1">Consignes de planification</p>
                                <p class="text-sm text-[#5A607F] italic">{{ $intervention->observations }}</p>
                            </div>
                        @endif
                        @if($rapport->commentaire)
                            <div>
                                <p class="text-[11px] text-[#A1A7C4] mb-1">Commentaires rédigés dans le rapport</p>
                                <p class="text-sm text-[#5A607F] whitespace-pre-wrap">{{ $rapport->commentaire }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Signatures -->
                <div class="pt-6 border-t border-[#E6E9F4] grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border border-dashed border-[#D7DBEC] rounded-[6px] p-4 text-center">
                        <h5 class="text-xs font-bold text-[#131523] mb-2">Signature Technicien</h5>
                        @if($rapport->signature_technicien)
                            @if(in_array(pathinfo($rapport->signature_technicien, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ Storage::url($rapport->signature_technicien) }}" class="max-h-24 mx-auto">
                            @else
                                <p class="text-[#A1A7C4] text-xs">{{ $rapport->signature_technicien }}</p>
                            @endif
                        @else
                            <span class="text-[#A1A7C4] text-xs">Non signée</span>
                        @endif
                    </div>
                    <div class="border border-dashed border-[#D7DBEC] rounded-[6px] p-4 text-center">
                        <h5 class="text-xs font-bold text-[#131523] mb-2">Signature Client</h5>
                        @if($rapport->signature_client)
                            @if(in_array(pathinfo($rapport->signature_client, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ Storage::url($rapport->signature_client) }}" class="max-h-24 mx-auto">
                            @else
                                <p class="text-[#A1A7C4] text-xs">{{ $rapport->signature_client }}</p>
                            @endif
                        @else
                            <span class="text-[#A1A7C4] text-xs">Non signée</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="ds-card-elevated p-5">
            <div class="flex flex-wrap gap-2">
                @can('update rapports')
                    @if($intervention->statut !== 'Terminee')
                        <a href="{{ route('rapports.edit', $rapport) }}" class="ds-btn ds-btn-sm" style="background-color:#F99600; color:#fff;">Modifier</a>
                    @endif
                @endcan

                @can('delete rapports')
                    @if($intervention->statut !== 'Terminee')
                        <form method="POST" action="{{ route('rapports.destroy', $rapport) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ds-btn ds-btn-danger ds-btn-sm">Supprimer</button>
                        </form>
                    @endif
                @endcan

                <a href="{{ route('rapports.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Retour</a>
            </div>
        </div>
    </div>
</x-app-layout>
