<x-client-layout>
    <x-slot name="header">Mes Documents</x-slot>

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <div class="kt-card-title">Documents de vos Interventions</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">Consultation et téléchargement des pièces jointes</div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Nom du Document</th>
                        <th>Intervention / Origine</th>
                        <th>Type / Extension</th>
                        <th>Date d'ajout</th>
                        <th style="text-align:right; padding-right:24px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td style="padding-left:24px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(62,151,255,0.12); color:#3e97ff; display:flex; align-items:center; justify-content:center; font-weight:700;">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:#181c32; font-size:13px;">{{ $doc->nom_original }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($doc->rapport && $doc->rapport->intervention)
                                    <div style="font-size:12px; font-weight:600; color:#181c32;">{{ $doc->rapport->intervention->code_intervention }}</div>
                                    <div style="font-size:11px; color:#a1a5b7;">Chantier : {{ $doc->rapport->intervention->chantier?->nom }}</div>
                                @else
                                    <span style="font-size:12px; color:#a1a5b7;">Document client</span>
                                @endif
                            </td>
                            <td>
                                <span class="kt-badge kt-badge-gray" style="text-transform:uppercase;">
                                    {{ pathinfo($doc->nom_original, PATHINFO_EXTENSION) ?: 'Fichier' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size:12px; color:#a1a5b7;">{{ $doc->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td style="padding-right:24px; text-align:right;">
                                <a href="{{ route('client.documents.download', $doc) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Télécharger
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="kt-empty-state">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    <p>Aucun document disponible pour le moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($documents, 'hasPages') && $documents->hasPages())
            <div style="padding:16px 24px; border-top:1px dashed #eff2f5;">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

</x-client-layout>
