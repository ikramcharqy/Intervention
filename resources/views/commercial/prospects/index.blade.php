<x-commercial-layout>
    <x-slot name="header">Prospects</x-slot>

    @if(session('success'))
        <div class="kt-alert kt-alert-success" style="margin-bottom:20px;">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <div class="kt-card-title">Liste des Prospects</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">{{ $prospects->count() }} prospect(s) trouvé(s)</div>
            </div>
            <a href="{{ route('prospects.create') }}" class="kt-btn kt-btn-primary">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Nouveau Prospect
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Entreprise</th>
                        <th>Contact</th>
                        <th>Téléphone</th>
                        <th>Commercial</th>
                        <th>Statut</th>
                        <th style="text-align:right; padding-right:24px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prospects as $prospect)
                        <tr>
                            <td style="padding-left:24px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div class="kt-avatar" style="background:rgba(62,151,255,0.12); color:#3e97ff; font-size:11px;">
                                        {{ strtoupper(substr($prospect->nom_entreprise, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:#181c32; font-size:13px;">{{ $prospect->nom_entreprise }}</div>
                                        @if($prospect->adresse)
                                            <div style="font-size:11px; color:#a1a5b7; margin-top:2px;">{{ Str::limit($prospect->adresse, 40) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:13px; color:#3f4254; font-weight:500;">{{ $prospect->nom_contact ?? '—' }}</div>
                                @if($prospect->email)
                                    <div style="font-size:11px; color:#a1a5b7; margin-top:2px;">{{ $prospect->email }}</div>
                                @endif
                            </td>
                            <td>
                                <span style="font-size:13px; color:#3f4254;">{{ $prospect->telephone ?? '—' }}</span>
                            </td>
                            <td>
                                <span style="font-size:13px; color:#3f4254;">{{ $prospect->commercial ? $prospect->commercial->name : '—' }}</span>
                            </td>
                            <td>
                                @php
                                    $sColor = match($prospect->statut) {
                                        'Nouveau'     => 'kt-badge-primary',
                                        'Qualifié'    => 'kt-badge-info',
                                        'Négociation' => 'kt-badge-warning',
                                        'Converti'    => 'kt-badge-success',
                                        'Perdu'       => 'kt-badge-danger',
                                        default       => 'kt-badge-gray'
                                    };
                                @endphp
                                <span class="kt-badge {{ $sColor }}">{{ $prospect->statut }}</span>
                            </td>
                            <td style="padding-right:24px; text-align:right;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:6px;">
                                    <a href="{{ route('prospects.show', $prospect) }}" class="kt-btn kt-btn-light kt-btn-sm" title="Voir le détail">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Voir
                                    </a>
                                    <a href="{{ route('prospects.edit', $prospect) }}" class="kt-btn kt-btn-light-primary kt-btn-sm" title="Modifier">
                                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Modifier
                                    </a>
                                    @if($prospect->statut !== 'Converti')
                                        <form action="{{ route('prospects.convert', $prospect) }}" method="POST" class="inline" onsubmit="return confirm('Convertir ce prospect en client ?');">
                                            @csrf
                                            <button type="submit" class="kt-btn kt-btn-light-success kt-btn-sm">
                                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Convertir
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="kt-empty-state">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <p>Aucun prospect trouvé. <a href="{{ route('prospects.create') }}" style="color:#3e97ff; text-decoration:none; font-weight:600;">Créer votre premier prospect</a></p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-commercial-layout>
