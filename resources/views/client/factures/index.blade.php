<x-client-layout>
    <x-slot name="header">Facturation & Devis</x-slot>

    <div class="space-y-6">

        <!-- DEVIS -->
        <div class="ui-card overflow-hidden">
            <div class="p-6 pb-4">
                <h2 class="text-base font-bold text-slate-900">Mes Devis</h2>
                <p class="text-xs text-slate-400 mt-1">Propositions commerciales émises pour votre compte.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Référence</th>
                            <th class="py-3 px-4">Date émission</th>
                            <th class="py-3 px-4">Validité</th>
                            <th class="py-3 px-4 text-right">Montant TTC</th>
                            <th class="py-3 px-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($devis as $item)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-slate-900">{{ $item->reference }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $item->date_emission?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $item->date_expiration?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">{{ number_format($item->montant_ttc, 2, ',', ' ') }} {{ $currency }}</td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $color = match($item->statut) {
                                            'Accepté' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Refusé' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'Envoyé' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            default => 'bg-slate-100 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold rounded-full border {{ $color }}">{{ $item->statut }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 italic">Aucun devis pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FACTURES -->
        <div class="ui-card overflow-hidden">
            <div class="p-6 pb-4">
                <h2 class="text-base font-bold text-slate-900">Mes Factures</h2>
                <p class="text-xs text-slate-400 mt-1">Factures émises et suivi de leur règlement.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Référence</th>
                            <th class="py-3 px-4">Émise le</th>
                            <th class="py-3 px-4">Échéance</th>
                            <th class="py-3 px-4 text-right">Montant TTC</th>
                            <th class="py-3 px-4 text-right">Solde restant</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 pr-6 pl-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($factures as $facture)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-slate-900">{{ $facture->reference }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $facture->date_emission?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $facture->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-right font-bold text-slate-900">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} {{ $currency }}</td>
                                <td class="py-3.5 px-4 text-right font-semibold {{ $facture->soldeRestant() > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ number_format($facture->soldeRestant(), 2, ',', ' ') }} {{ $currency }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $color = match($facture->statut) {
                                            'Payée' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'Partiellement payée' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'En retard' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            'Annulée' => 'bg-slate-100 text-slate-500 border-slate-200',
                                            default => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold rounded-full border {{ $color }}">{{ $facture->statut }}</span>
                                </td>
                                <td class="py-3.5 pr-6 pl-4 text-right">
                                    <a href="{{ route('client.factures.pdf', $facture) }}" target="_blank" class="text-xs font-bold text-sky-600 hover:text-sky-800">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 italic">Aucune facture pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-client-layout>
