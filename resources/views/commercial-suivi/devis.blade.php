<x-app-layout>
    <div class="space-y-6">
        <!-- Titre -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Suivi Devis &amp; Offres</h1>
            <p class="text-xs text-[#5A607F] mt-1">Vue lecture seule des devis émis par l'équipe commerciale.</p>
        </div>

        <!-- Table -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client / Prospect</th>
                            <th>Commercial</th>
                            <th>Statut</th>
                            <th>Montant TTC</th>
                            <th>Émis le</th>
                            <th>Dernière modif.</th>
                            <th class="text-right">Détail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($devis as $d)
                            @php
                                $dColor = match(true) {
                                    in_array($d->statut, ['Accepté', 'Accepte', 'Validé']) => 'ds-badge-light-success',
                                    in_array($d->statut, ['Envoyé', 'En attente']) => 'ds-badge-light-warning',
                                    in_array($d->statut, ['Refusé', 'Refuse', 'Annulé']) => 'ds-badge-light-danger',
                                    default => 'ds-badge-light-secondary',
                                };
                            @endphp
                            <tr>
                                <td class="font-bold text-xs text-[#5A607F]">{{ $d->reference }}</td>
                                <td>
                                    <p class="text-xs font-bold text-[#131523]">{{ $d->client->nom ?? $d->prospect->nom_entreprise ?? '—' }}</p>
                                    <p class="text-[11px] text-[#A1A7C4]">{{ $d->client ? 'Client' : ($d->prospect ? 'Prospect' : '—') }}</p>
                                </td>
                                <td class="text-xs font-medium text-[#131523]">{{ $d->commercial->name ?? 'Non assigné' }}</td>
                                <td>
                                    <span class="ds-badge ds-badge-sm {{ $dColor }}">{{ $d->statut }}</span>
                                </td>
                                <td class="text-xs font-mono text-[#131523]">{{ number_format($d->montant_ttc, 2) }} DH</td>
                                <td class="text-xs text-[#5A607F]">{{ $d->date_emission?->format('d/m/Y') ?? '—' }}</td>
                                <td class="text-xs text-[#A1A7C4]">{{ $d->updated_at?->diffForHumans() ?? '—' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('commercial-suivi.devis.show', $d) }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc] transition">
                                        Voir →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="text-center py-14">
                                        <i class="fas fa-file-invoice-dollar text-3xl text-[#D7DBEC] mb-3 block"></i>
                                        <p class="text-sm text-[#A1A7C4] font-medium">Aucun devis enregistré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[#E6E9F4]">
                <p class="text-xs text-[#A1A7C4]">{{ $devis->count() }} devis</p>
            </div>
        </div>
    </div>
</x-app-layout>
