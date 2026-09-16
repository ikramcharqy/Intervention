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

        <div class="metronic-card overflow-hidden">
            <div class="flex items-center justify-between p-6 pb-0">
                <div>
                    <h1 class="text-base font-bold text-[#181C32] font-heading">Gestion des Factures</h1>
                    <p class="text-xs text-[#A1A5B7] mt-1">{{ $factures->count() }} facture(s) au total. Générées depuis un devis accepté.</p>
                </div>
            </div>

            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#F9F9FB] text-[#A1A5B7] uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Référence</th>
                            <th class="py-3 px-4">Client</th>
                            <th class="py-3 px-4">Devis d'origine</th>
                            <th class="py-3 px-4 text-right">Montant TTC</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 pr-6 pl-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFF2F5]">
                        @forelse($factures as $facture)
                            <tr class="hover:bg-[#F9F9FB] transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-[#181C32]">{{ $facture->reference }}</td>
                                <td class="py-3.5 px-4 text-[#3F4254] font-medium">{{ $facture->client->nom ?? '—' }}</td>
                                <td class="py-3.5 px-4">
                                    @if($facture->devis)
                                        <a href="{{ route('commercial.devis.show', $facture->devis) }}" class="text-emerald-600 font-bold hover:underline">{{ $facture->devis->reference }}</a>
                                    @else
                                        <span class="text-[#A1A5B7]">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-[#181C32]">{{ number_format($facture->montant_ttc, 2, ',', ' ') }} {{ $currency }}</td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $fColor = match($facture->statut) {
                                            'Payée' => 'bg-emerald-50 text-emerald-600',
                                            'Partiellement payée' => 'bg-amber-50 text-amber-600',
                                            'En retard' => 'bg-rose-50 text-rose-600',
                                            'Annulée' => 'bg-[#F5F8FA] text-[#5E6278]',
                                            default => 'bg-blue-50 text-blue-600',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $fColor }}">{{ $facture->statut }}</span>
                                </td>
                                <td class="py-3.5 pr-6 pl-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('commercial.factures.show', $facture) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white border border-[#EFF2F5] text-[#5E6278] text-[11px] font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                                            <i class="fas fa-eye text-[10px]"></i> Voir
                                        </a>
                                        <a href="{{ route('commercial.factures.pdf', $facture) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-rose-50 text-rose-700 text-[11px] font-bold rounded-lg hover:bg-rose-100 transition">
                                            <i class="fas fa-file-pdf text-[10px]"></i> PDF
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-[#A1A5B7] italic">
                                    Aucune facture. Générez-en une depuis un devis accepté.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-commercial-layout>
