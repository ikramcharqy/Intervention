<x-app-layout>
    <div class="space-y-6">
        <!-- Titre + retour -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <a href="{{ route('commercial-suivi.devis') }}" class="text-xs font-semibold text-[#5A607F] hover:text-[#1E5EFF] transition inline-flex items-center gap-1.5 mb-2">
                    <i class="fas fa-arrow-left text-[10px]"></i> Retour au suivi
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">{{ $devis->reference }}</h1>
            </div>
            @php
                $dColor = match(true) {
                    in_array($devis->statut, ['Accepté', 'Accepte', 'Validé']) => 'ds-badge-light-success',
                    in_array($devis->statut, ['Envoyé', 'En attente']) => 'ds-badge-light-warning',
                    in_array($devis->statut, ['Refusé', 'Refuse', 'Annulé']) => 'ds-badge-light-danger',
                    default => 'ds-badge-light-secondary',
                };
            @endphp
            <span class="ds-badge ds-badge-md {{ $dColor }}">{{ $devis->statut }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Infos -->
            <div class="ds-card-elevated p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-5">Informations</h3>
                <dl class="space-y-4 text-xs">
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Client / Prospect</dt>
                        <dd class="text-[#131523] font-semibold">{{ $devis->client->nom ?? $devis->prospect->nom_entreprise ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Commercial responsable</dt>
                        <dd class="text-[#131523] font-semibold">{{ $devis->commercial->name ?? 'Non assigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Date d'émission</dt>
                        <dd class="text-[#131523]">{{ $devis->date_emission?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Date d'expiration</dt>
                        <dd class="text-[#131523]">{{ $devis->date_expiration?->format('d/m/Y') ?? '—' }}</dd>
                    </div>
                    <div class="pt-4 border-t border-[#E6E9F4]">
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Créé le</dt>
                        <dd class="text-[#131523]">{{ $devis->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Dernière modification</dt>
                        <dd class="text-[#131523]">{{ $devis->updated_at?->format('d/m/Y H:i') ?? '—' }} ({{ $devis->updated_at?->diffForHumans() }})</dd>
                    </div>
                    @if($devis->observations)
                        <div class="pt-4 border-t border-[#E6E9F4]">
                            <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Observations</dt>
                            <dd class="text-[#131523] leading-relaxed">{{ $devis->observations }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Lignes du devis -->
            <div class="ds-card-elevated lg:col-span-2 overflow-hidden">
                <div class="p-7 pb-0">
                    <h3 class="text-[16px] font-bold text-[#131523] mb-5">Lignes du Devis</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="ds-table">
                        <thead>
                            <tr>
                                <th>Désignation</th>
                                <th class="text-right">Qté</th>
                                <th class="text-right">Prix Unitaire</th>
                                <th class="text-right">Montant HT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($devis->lignes as $ligne)
                                <tr>
                                    <td>
                                        <p class="text-xs font-semibold text-[#131523]">{{ $ligne->designation }}</p>
                                        @if($ligne->description)
                                            <p class="text-[11px] text-[#A1A7C4]">{{ $ligne->description }}</p>
                                        @endif
                                    </td>
                                    <td class="text-right text-xs text-[#131523]">{{ $ligne->quantite }}</td>
                                    <td class="text-right text-xs font-mono text-[#131523]">{{ number_format($ligne->prix_unitaire, 2) }} DH</td>
                                    <td class="text-right text-xs font-mono font-bold text-[#131523]">{{ number_format($ligne->montant_ht, 2) }} DH</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-xs text-[#A1A7C4]">Aucune ligne enregistrée.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-[#E6E9F4] flex justify-end gap-8">
                    <div class="text-right">
                        <p class="text-[11px] text-[#A1A7C4] uppercase tracking-wider font-bold">Total HT</p>
                        <p class="text-sm font-bold text-[#131523]">{{ number_format($devis->montant_ht, 2) }} DH</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-[#A1A7C4] uppercase tracking-wider font-bold">TVA ({{ $devis->taux_tva }}%)</p>
                        <p class="text-sm font-bold text-[#131523]">{{ number_format($devis->montant_tva, 2) }} DH</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] text-[#1E5EFF] uppercase tracking-wider font-bold">Total TTC</p>
                        <p class="text-base font-bold text-[#1E5EFF]">{{ number_format($devis->montant_ttc, 2) }} DH</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
