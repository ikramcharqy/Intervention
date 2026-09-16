<x-app-layout>
    <x-slot name="header">
        {{ __('Historique des mouvements — ') }} {{ $materiau->nom }}
    </x-slot>

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <a href="{{ route('materiaux.show', $materiau) }}" class="text-xs font-semibold inline-flex items-center gap-1.5" style="color:#5A607F;">
                <i class="ti ti-arrow-left"></i> Retour au matériau
            </a>
        </div>

        <div class="ds-card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Quantité</th>
                            <th>Stock après</th>
                            <th>Intervention</th>
                            <th>Technicien</th>
                            <th>Effectué par</th>
                            <th>Commentaire</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mouvements as $mvt)
                            <tr>
                                <td>
                                    <span class="ds-badge ds-badge-sm {{ $mvt->type_mouvement === 'entree' ? 'ds-badge-light-success' : 'ds-badge-light-secondary' }}">
                                        {{ $mvt->type_mouvement === 'entree' ? 'Entrée' : 'Sortie' }}
                                    </span>
                                </td>
                                <td class="font-semibold text-[#131523]">{{ number_format($mvt->quantite, 2) }} {{ $materiau->unite }}</td>
                                <td class="text-[#5A607F]">{{ number_format($mvt->stock_apres, 2) }}</td>
                                <td>
                                    @if($mvt->intervention)
                                        <a href="{{ route('interventions.show', $mvt->intervention) }}" class="text-xs font-semibold" style="color:#1E5EFF;">{{ $mvt->intervention->code_intervention }}</a>
                                    @else
                                        <span class="text-[#A1A7C4]">-</span>
                                    @endif
                                </td>
                                <td class="text-[#5A607F]">{{ $mvt->technicien->name ?? '-' }}</td>
                                <td class="text-[#5A607F]">{{ $mvt->user->name ?? '-' }}</td>
                                <td class="text-[#5A607F] text-xs">{{ $mvt->commentaire ?? '-' }}</td>
                                <td class="text-[#A1A7C4] text-xs">{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-14">
                                    <i class="ti ti-history text-3xl mb-3 block" style="color:#D7DBEC;"></i>
                                    <p class="text-sm font-medium" style="color:#A1A7C4;">Aucun mouvement de stock enregistré pour ce matériau.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($mouvements->hasPages())
                <div class="px-6 py-4 border-t" style="border-color:#E6E9F4;">
                    {{ $mouvements->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
