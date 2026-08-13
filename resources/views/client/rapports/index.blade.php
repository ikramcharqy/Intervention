<x-client-layout>
    <x-slot name="header">Rapports d'Intervention</x-slot>

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <div class="kt-card-title">Mes Rapports</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">Consultation et téléchargement PDF</div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Intervention</th>
                        <th>Chantier</th>
                        <th>Technicien</th>
                        <th>Date de création</th>
                        <th style="text-align:right; padding-right:24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rapports as $rapport)
                        <tr>
                            <td style="padding-left:24px; font-weight:700; color:#181c32;">
                                {{ $rapport->intervention?->code_intervention ?? 'Rapport #'.$rapport->id }}
                            </td>
                            <td>
                                <div style="font-weight:600; color:#181c32;">{{ $rapport->intervention?->chantier?->nom ?? '—' }}</div>
                            </td>
                            <td>
                                <span style="font-size:13px; color:#3f4254;">{{ $rapport->intervention?->technicien?->name ?? '—' }}</span>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#a1a5b7;">{{ $rapport->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td style="padding-right:24px; text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:6px;">
                                    <a href="{{ route('client.rapports.show', $rapport) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                        Consulter
                                    </a>
                                    <a href="{{ route('client.rapports.pdf', $rapport) }}" target="_blank" class="kt-btn kt-btn-success kt-btn-sm">
                                        Télécharger PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="kt-empty-state">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2 2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p>Aucun rapport disponible pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($rapports, 'hasPages') && $rapports->hasPages())
            <div style="padding:16px 24px; border-top:1px dashed #eff2f5;">
                {{ $rapports->links() }}
            </div>
        @endif
    </div>

</x-client-layout>
