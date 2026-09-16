<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="metronic-card p-4 text-sm font-medium bg-emerald-50 text-emerald-700 border-emerald-100">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="metronic-card p-4 text-sm font-medium bg-rose-50 text-rose-700 border-rose-100">
                {{ session('error') }}
            </div>
        @endif

        @php
            $dColor = match($devis->statut) {
                'Brouillon' => 'bg-[#F5F8FA] text-[#5E6278]',
                'Envoyé', 'En attente' => 'bg-amber-50 text-amber-600',
                'Accepté', 'Accepte', 'Validé' => 'bg-emerald-50 text-emerald-600',
                'Refusé', 'Refuse', 'Annulé' => 'bg-rose-50 text-rose-600',
                default => 'bg-[#F5F8FA] text-[#5E6278]'
            };
        @endphp

        <!-- Header Bar -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-[#181C32] font-heading">{{ $devis->reference ?? 'DEVIS-'.$devis->id }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $dColor }}">{{ $devis->statut }}</span>
                        @if($devis->date_emission)
                            <span class="text-xs text-[#A1A5B7]">Émis le {{ $devis->date_emission->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    @php $estAccepte = in_array($devis->statut, ['Accepté', 'Accepte', 'Validé'], true); @endphp
                    @if($estAccepte && $devis->demandeIntervention)
                        <div class="mt-2">
                            @if($devis->demandeIntervention->intervention)
                                <a href="{{ route('interventions.show', $devis->demandeIntervention->intervention) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 no-underline">
                                    <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                                    Voir l'intervention {{ $devis->demandeIntervention->intervention->code_intervention }}
                                </a>
                            @else
                                <a href="{{ route('demande-interventions.show', $devis->demandeIntervention) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full bg-blue-50 text-blue-600 no-underline">
                                    <i class="fas fa-inbox text-[9px]"></i>
                                    Voir la demande {{ $devis->demandeIntervention->reference }}
                                </a>
                            @endif
                        </div>
                    @elseif($estAccepte && !$devis->demandeIntervention && $devis->client_id)
                        <div class="mt-2">
                            <form action="{{ route('commercial.devis.creerDemande', $devis) }}" method="POST" onsubmit="return confirm('Créer une demande d\'intervention à partir de ce devis ?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition">
                                    <i class="fas fa-plus text-[9px]"></i>
                                    Créer la demande d'intervention
                                </button>
                            </form>
                        </div>
                    @elseif($estAccepte && !$devis->client_id)
                        <div class="mt-2">
                            <span class="text-[11px] text-[#A1A5B7]">
                                Convertissez d'abord le prospect en client pour créer une demande d'intervention.
                            </span>
                        </div>
                    @endif

                    @if($estAccepte && $devis->client_id)
                        <div class="mt-2">
                            @if($devis->factures->isNotEmpty())
                                <a href="{{ route('commercial.factures.show', $devis->factures->first()) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full bg-rose-50 text-rose-600 no-underline">
                                    <i class="fas fa-file-invoice text-[9px]"></i>
                                    Voir la facture {{ $devis->factures->first()->reference }}
                                </a>
                            @else
                                <form action="{{ route('commercial.devis.genererFacture', $devis) }}" method="POST" onsubmit="return confirm('Générer une facture à partir de ce devis ?');">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full bg-rose-600 text-white hover:bg-rose-700 transition">
                                        <i class="fas fa-plus text-[9px]"></i>
                                        Générer la facture
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('commercial.devis.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#EFF2F5] text-[#5E6278] text-xs font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                    <i class="fas fa-arrow-left text-[11px]"></i> Retour
                </a>
                <a href="{{ route('commercial.devis.edit', $devis) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#EFF2F5] text-[#5E6278] text-xs font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                    <i class="fas fa-pen text-[11px]"></i> Modifier
                </a>
                <form action="{{ route('commercial.devis.duplicate', $devis) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-100 transition">
                        <i class="fas fa-copy text-[11px]"></i> Dupliquer
                    </button>
                </form>
                <a href="{{ route('commercial.devis.pdf', $devis) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition">
                    <i class="fas fa-file-pdf text-[11px]"></i> Imprimer PDF
                </a>
                <form action="{{ route('commercial.devis.destroy', $devis) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce devis ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#EFF2F5] text-rose-600 text-xs font-bold rounded-lg hover:bg-rose-50 transition">
                        <i class="fas fa-trash text-[11px]"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>

        <!-- Content -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">

            <!-- Main Devis Card -->
            <div class="space-y-0">
                <div class="metronic-card overflow-hidden">
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 mb-6">
                            <!-- Emetteur -->
                            <div>
                                <p class="text-[10px] font-bold text-[#A1A5B7] uppercase tracking-wider mb-2.5">Émetteur</p>
                                <p class="text-sm font-extrabold text-[#181C32]">{{ $company['name'] }}</p>
                                <p class="text-xs text-[#A1A5B7] mt-1">Commercial : {{ $devis->commercial ? $devis->commercial->name : 'N/A' }}</p>
                            </div>

                            <!-- Destinataire -->
                            <div>
                                <p class="text-[10px] font-bold text-[#A1A5B7] uppercase tracking-wider mb-2.5">Destinataire</p>
                                @if($devis->prospect)
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-bold rounded-full bg-blue-50 text-blue-600 mb-1.5">Prospect</span>
                                    <p class="text-sm font-extrabold text-[#181C32]">{{ $devis->prospect->nom_entreprise }}</p>
                                    <p class="text-xs text-[#5E6278] mt-1">{{ $devis->prospect->nom_contact }}</p>
                                    <p class="text-xs text-[#A1A5B7] mt-0.5">{{ $devis->prospect->email }}</p>
                                    <p class="text-xs text-[#A1A5B7]">{{ $devis->prospect->telephone }}</p>
                                @elseif($devis->client)
                                    <span class="inline-block px-2 py-0.5 text-[9px] font-bold rounded-full bg-emerald-50 text-emerald-600 mb-1.5">Client</span>
                                    <p class="text-sm font-extrabold text-[#181C32]">{{ $devis->client->nom }}</p>
                                    <p class="text-xs text-[#5E6278] mt-1">{{ $devis->client->nom_contact ?? '' }}</p>
                                    <p class="text-xs text-[#A1A5B7] mt-0.5">{{ $devis->client->email ?? '' }}</p>
                                    <p class="text-xs text-[#A1A5B7]">{{ $devis->client->telephone ?? '' }}</p>
                                @else
                                    <p class="text-sm text-[#A1A5B7]">Non spécifié</p>
                                @endif
                            </div>
                        </div>

                        <!-- Info Strips -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-[#F5F8FA] rounded-xl p-4">
                            <div>
                                <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Statut</p>
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $dColor }}">{{ $devis->statut }}</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Date émission</p>
                                <p class="text-xs font-semibold text-[#181C32]">{{ $devis->date_emission ? $devis->date_emission->format('d/m/Y') : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Date expiration</p>
                                <p class="text-xs font-semibold text-[#181C32]">{{ $devis->date_expiration ? $devis->date_expiration->format('d/m/Y') : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Taux TVA</p>
                                <p class="text-xs font-semibold text-[#181C32]">{{ $devis->taux_tva }}%</p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-[#EFF2F5]">

                    <!-- Lignes Table -->
                    <div class="p-6">
                        <p class="text-sm font-bold text-[#181C32] mb-4">Détail des prestations</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-[#F9F9FB] text-[#A1A5B7] uppercase font-bold text-[10px]">
                                        <th class="py-3 px-3">Désignation</th>
                                        <th class="py-3 px-3">Description</th>
                                        <th class="py-3 px-3 text-center">Qté</th>
                                        <th class="py-3 px-3 text-right">Prix Unit. HT</th>
                                        <th class="py-3 px-3 text-right">Total HT</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#EFF2F5]">
                                    @foreach($devis->lignes as $ligne)
                                        <tr>
                                            <td class="py-3 px-3 font-semibold text-[#181C32]">{{ $ligne->designation }}</td>
                                            <td class="py-3 px-3 text-[#7E8299]">{{ $ligne->description ?? '—' }}</td>
                                            <td class="py-3 px-3 text-center font-medium">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                            <td class="py-3 px-3 text-right font-medium">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} {{ $currency }}</td>
                                            <td class="py-3 px-3 text-right font-bold text-[#181C32]">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} {{ $currency }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($devis->observations)
                        <hr class="border-[#EFF2F5]">
                        <div class="p-6">
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4">
                                <p class="text-[10px] font-bold text-amber-700 uppercase tracking-wide mb-1.5">Observations</p>
                                <p class="text-xs text-[#5E6278] leading-relaxed whitespace-pre-wrap">{{ $devis->observations }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar: Totaux -->
            <div class="space-y-4">
                <div class="metronic-card p-6">
                    <p class="text-sm font-bold text-[#181C32] mb-4">Récapitulatif</p>
                    <div class="space-y-3.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-[#7E8299]">Sous-total HT</span>
                            <span class="text-xs font-semibold text-[#181C32]">{{ number_format($devis->montant_ht, 2, ',', ' ') }} {{ $currency }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-[#7E8299]">TVA ({{ $devis->taux_tva }}%)</span>
                            <span class="text-xs font-semibold text-[#181C32]">{{ number_format($devis->montant_tva, 2, ',', ' ') }} {{ $currency }}</span>
                        </div>
                        <hr class="border-[#EFF2F5]">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-[#181C32]">Total TTC</span>
                            <span class="text-lg font-extrabold text-emerald-600">{{ number_format($devis->montant_ttc, 2, ',', ' ') }} {{ $currency }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="metronic-card p-6">
                    <p class="text-sm font-bold text-[#181C32] mb-4">Actions rapides</p>
                    <div class="space-y-2">
                        <a href="{{ route('commercial.devis.pdf', $devis) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition">
                            <i class="fas fa-file-pdf text-[11px]"></i> Télécharger PDF
                        </a>
                        <a href="{{ route('commercial.devis.edit', $devis) }}" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-white border border-[#EFF2F5] text-[#5E6278] text-xs font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                            <i class="fas fa-pen text-[11px]"></i> Modifier le devis
                        </a>
                        <form action="{{ route('commercial.devis.duplicate', $devis) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-100 transition">
                                <i class="fas fa-copy text-[11px]"></i> Dupliquer ce devis
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-commercial-layout>
