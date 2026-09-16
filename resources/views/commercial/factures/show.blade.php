<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="metronic-card p-4 text-sm font-medium bg-emerald-50 text-emerald-700 border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @php
            $fColor = match($facture->statut) {
                'Payée' => 'bg-emerald-50 text-emerald-600',
                'Partiellement payée' => 'bg-amber-50 text-amber-600',
                'En retard' => 'bg-rose-50 text-rose-600',
                'Annulée' => 'bg-[#F5F8FA] text-[#5E6278]',
                default => 'bg-blue-50 text-blue-600',
            };
        @endphp

        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-700 flex items-center justify-center shrink-0">
                    <i class="fas fa-file-invoice text-lg"></i>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-[#181C32] font-heading">{{ $facture->reference }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $fColor }}">{{ $facture->statut }}</span>
                        @if($facture->date_emission)
                            <span class="text-xs text-[#A1A5B7]">Émise le {{ $facture->date_emission->format('d/m/Y') }}</span>
                        @endif
                    </div>
                    @if($facture->devis)
                        <div class="mt-2">
                            <a href="{{ route('commercial.devis.show', $facture->devis) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 no-underline">
                                <i class="fas fa-arrow-up-right-from-square text-[9px]"></i> Voir le devis {{ $facture->devis->reference }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('commercial.factures.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#EFF2F5] text-[#5E6278] text-xs font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                    <i class="fas fa-arrow-left text-[11px]"></i> Retour
                </a>
                @if($facture->soldeRestant() > 0)
                    <form action="{{ route('commercial.factures.marquerPayee', $facture) }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                            <i class="fas fa-check text-[11px]"></i> Marquer comme payée
                        </button>
                    </form>
                @endif
                <a href="{{ route('commercial.factures.pdf', $facture) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition">
                    <i class="fas fa-file-pdf text-[11px]"></i> Imprimer PDF
                </a>
            </div>
        </div>

        <div class="metronic-card p-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-[#F5F8FA] rounded-xl p-4 mb-6">
                <div>
                    <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Total HT</p>
                    <p class="text-xs font-semibold text-[#181C32]">{{ number_format($facture->montant_ht, 2, ',', ' ') }} {{ $currency }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">TVA ({{ $facture->taux_tva }}%)</p>
                    <p class="text-xs font-semibold text-[#181C32]">{{ number_format($facture->montant_tva, 2, ',', ' ') }} {{ $currency }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Total TTC</p>
                    <p class="text-sm font-extrabold text-[#181C32]">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} {{ $currency }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-[#A1A5B7] uppercase mb-1">Solde restant</p>
                    <p class="text-sm font-extrabold {{ $facture->soldeRestant() > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($facture->soldeRestant(), 2, ',', ' ') }} {{ $currency }}</p>
                </div>
            </div>

            @if($facture->devis)
                <p class="text-sm font-bold text-[#181C32] mb-4">Détail des prestations (devis {{ $facture->devis->reference }})</p>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-[#F9F9FB] text-[#A1A5B7] uppercase font-bold text-[10px]">
                                <th class="py-3 px-3">Désignation</th>
                                <th class="py-3 px-3 text-center">Qté</th>
                                <th class="py-3 px-3 text-right">Prix Unit. HT</th>
                                <th class="py-3 px-3 text-right">Total HT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFF2F5]">
                            @foreach($facture->devis->lignes as $ligne)
                                <tr>
                                    <td class="py-3 px-3 font-semibold text-[#181C32]">{{ $ligne->designation }}</td>
                                    <td class="py-3 px-3 text-center font-medium">{{ number_format($ligne->quantite, 2, ',', ' ') }}</td>
                                    <td class="py-3 px-3 text-right font-medium">{{ number_format($ligne->prix_unitaire, 2, ',', ' ') }} {{ $currency }}</td>
                                    <td class="py-3 px-3 text-right font-bold text-[#181C32]">{{ number_format($ligne->montant_ht, 2, ',', ' ') }} {{ $currency }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-commercial-layout>
