<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="metronic-card p-4 text-sm font-medium bg-emerald-50 text-emerald-700 border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <div class="metronic-card overflow-hidden">
            <div class="flex items-center justify-between p-6 pb-0">
                <div>
                    <h1 class="text-base font-bold text-[#181C32] font-heading">Gestion des Devis</h1>
                    <p class="text-xs text-[#A1A5B7] mt-1">{{ $devis->count() }} devis au total</p>
                </div>
                <a href="{{ route('commercial.devis.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                    <i class="fas fa-plus text-[11px]"></i> Nouveau Devis
                </a>
            </div>

            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#F9F9FB] text-[#A1A5B7] uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Référence</th>
                            <th class="py-3 px-4">Destinataire</th>
                            <th class="py-3 px-4">Date émission</th>
                            <th class="py-3 px-4 text-right">Montant TTC</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 pr-6 pl-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFF2F5]">
                        @forelse($devis as $item)
                            <tr class="hover:bg-[#F9F9FB] transition">
                                <td class="py-3.5 pl-6 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                            <i class="fas fa-file-invoice-dollar text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[#181C32] truncate">{{ $item->reference ?? 'DEVIS-'.$item->id }}</p>
                                            @if($item->date_expiration && $item->date_expiration->isPast() && $item->statut === 'Envoyé')
                                                <p class="text-[10px] text-rose-600 font-bold mt-0.5">
                                                    <i class="fas fa-triangle-exclamation"></i> Expiré
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($item->prospect)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-blue-50 text-blue-600">Prospect</span>
                                            <span class="text-[#3F4254] font-medium">{{ $item->prospect->nom_entreprise }}</span>
                                        </div>
                                    @elseif($item->client)
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 text-[9px] font-bold rounded-full bg-emerald-50 text-emerald-600">Client</span>
                                            <span class="text-[#3F4254] font-medium">{{ $item->client->nom }}</span>
                                        </div>
                                    @else
                                        <span class="text-[#A1A5B7]">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[#3F4254]">
                                    {{ $item->date_emission ? $item->date_emission->format('d/m/Y') : '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-[#181C32]">
                                    {{ number_format($item->montant_ttc, 2, ',', ' ') }} {{ $currency }}
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $dColor = match($item->statut) {
                                            'Brouillon' => 'bg-[#F5F8FA] text-[#5E6278]',
                                            'Envoyé', 'En attente' => 'bg-amber-50 text-amber-600',
                                            'Accepté', 'Accepte', 'Validé' => 'bg-emerald-50 text-emerald-600',
                                            'Refusé', 'Refuse', 'Annulé' => 'bg-rose-50 text-rose-600',
                                            default => 'bg-[#F5F8FA] text-[#5E6278]'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $dColor }}">{{ $item->statut }}</span>
                                </td>
                                <td class="py-3.5 pr-6 pl-4">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('commercial.devis.show', $item) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white border border-[#EFF2F5] text-[#5E6278] text-[11px] font-bold rounded-lg hover:bg-[#F9F9FB] transition" title="Voir le détail">
                                            <i class="fas fa-eye text-[10px]"></i> Voir
                                        </a>
                                        <a href="{{ route('commercial.devis.edit', $item) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white border border-[#EFF2F5] text-[#5E6278] text-[11px] font-bold rounded-lg hover:bg-[#F9F9FB] transition" title="Modifier">
                                            <i class="fas fa-pen text-[10px]"></i> Modifier
                                        </a>
                                        <a href="{{ route('commercial.devis.pdf', $item) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-rose-50 text-rose-700 text-[11px] font-bold rounded-lg hover:bg-rose-100 transition" title="PDF">
                                            <i class="fas fa-file-pdf text-[10px]"></i> PDF
                                        </a>
                                        <form action="{{ route('commercial.devis.duplicate', $item) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-lg hover:bg-emerald-100 transition" title="Dupliquer">
                                                <i class="fas fa-copy text-[10px]"></i> Dupliquer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-[#A1A5B7] italic">
                                    Aucun devis trouvé. <a href="{{ route('commercial.devis.create') }}" class="text-emerald-600 font-bold hover:underline">Créer votre premier devis</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-commercial-layout>
