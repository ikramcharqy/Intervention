<x-commercial-layout>
    <x-slot name="header">Devis</x-slot>

    @if(session('success'))
        <div class="kt-alert kt-alert-success" style="margin-bottom:20px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <div class="kt-card-title">Gestion des Devis</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">{{ $devis->count() }} devis au total</div>
            </div>
            <a href="{{ route('commercial.devis.create') }}" class="kt-btn kt-btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouveau Devis
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Référence</th>
                        <th>Destinataire</th>
                        <th>Date émission</th>
                        <th style="text-align:right;">Montant TTC</th>
                        <th>Statut</th>
                        <th style="text-align:right; padding-right:24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devis as $item)
                        <tr>
                            <td style="padding-left:24px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="kt-avatar" style="background:rgba(114,57,234,0.12); color:#7239ea;">
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:#181c32; font-size:13px;">{{ $item->reference ?? 'DEVIS-'.$item->id }}</div>
                                        @if($item->date_expiration && $item->date_expiration->isPast() && $item->statut === 'Envoyé')
                                            <div style="font-size:10px; color:#f1416c; font-weight:600; margin-top:2px;">⚠ Expiré</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($item->prospect)
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <span class="kt-badge kt-badge-primary" style="font-size:9px; padding:2px 6px;">Prospect</span>
                                        <span style="font-size:13px; color:#3f4254; font-weight:500;">{{ $item->prospect->nom_entreprise }}</span>
                                    </div>
                                @elseif($item->client)
                                    <div style="display:flex; align-items:center; gap:6px;">
                                        <span class="kt-badge kt-badge-success" style="font-size:9px; padding:2px 6px;">Client</span>
                                        <span style="font-size:13px; color:#3f4254; font-weight:500;">{{ $item->client->nom }}</span>
                                    </div>
                                @else
                                    <span style="color:#a1a5b7; font-size:13px;">—</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:13px; color:#3f4254;">
                                    {{ $item->date_emission ? $item->date_emission->format('d/m/Y') : '—' }}
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <span style="font-size:14px; font-weight:700; color:#181c32;">
                                    {{ number_format($item->montant_ttc, 2, ',', ' ') }} €
                                </span>
                            </td>
                            <td>
                                @php
                                    $dColor = match($item->statut) {
                                        'Brouillon' => 'kt-badge-gray',
                                        'Envoyé'    => 'kt-badge-warning',
                                        'Accepté'   => 'kt-badge-success',
                                        'Refusé'    => 'kt-badge-danger',
                                        default     => 'kt-badge-gray'
                                    };
                                @endphp
                                <span class="kt-badge {{ $dColor }}">{{ $item->statut }}</span>
                            </td>
                            <td style="padding-right:24px; text-align:right;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                                    <a href="{{ route('commercial.devis.show', $item) }}" class="kt-btn kt-btn-light kt-btn-sm">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir
                                    </a>
                                    <a href="{{ route('commercial.devis.edit', $item) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Modifier
                                    </a>
                                    <a href="{{ route('commercial.devis.pdf', $item) }}" target="_blank" class="kt-btn kt-btn-light-danger kt-btn-sm">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        PDF
                                    </a>
                                    <form action="{{ route('commercial.devis.duplicate', $item) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="kt-btn kt-btn-light-success kt-btn-sm">
                                            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            Dupliquer
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="kt-empty-state">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p>Aucun devis trouvé. <a href="{{ route('commercial.devis.create') }}" style="color:#3e97ff; text-decoration:none; font-weight:600;">Créer votre premier devis</a></p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-commercial-layout>
