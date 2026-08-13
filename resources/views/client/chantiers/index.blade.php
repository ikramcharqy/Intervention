<x-client-layout>
    <x-slot name="header">Mes Chantiers</x-slot>

    <div class="kt-card">
        <div class="kt-card-header">
            <div>
                <div class="kt-card-title">Liste de vos Chantiers</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">{{ $chantiers->count() }} chantier(s) associés à votre compte</div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Chantier</th>
                        <th>Adresse / Ville</th>
                        <th>Responsable</th>
                        <th style="text-align:center;">Interventions</th>
                        <th>Dernière intervention</th>
                        <th style="text-align:right; padding-right:24px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($chantiers as $chantier)
                        @php
                            $derniereInterv = $chantier->interventions->first();
                        @endphp
                        <tr>
                            <td style="padding-left:24px;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(62,151,255,0.12); color:#3e97ff; display:flex; align-items:center; justify-content:center; font-weight:700;">
                                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <div>
                                        <div style="font-weight:700; color:#181c32; font-size:13px;">{{ $chantier->nom }}</div>
                                        <div style="font-size:11px; color:#a1a5b7;">Code: {{ $chantier->code_chantier ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:13px; color:#3f4254;">{{ $chantier->adresse ?? '—' }}</div>
                                @if($chantier->ville)<div style="font-size:11px; color:#a1a5b7;">{{ $chantier->ville }}</div>@endif
                            </td>
                            <td>
                                <div style="font-size:13px; color:#3f4254; font-weight:500;">{{ $chantier->responsable ?? '—' }}</div>
                                @if($chantier->telephone_responsable)<div style="font-size:11px; color:#a1a5b7;">{{ $chantier->telephone_responsable }}</div>@endif
                            </td>
                            <td style="text-align:center;">
                                <span class="kt-badge kt-badge-primary">{{ $chantier->interventions_count }}</span>
                            </td>
                            <td>
                                @if($derniereInterv)
                                    <div style="font-size:12px; font-weight:600; color:#181c32;">{{ $derniereInterv->code_intervention }}</div>
                                    <div style="font-size:11px; color:#a1a5b7;">{{ $derniereInterv->updated_at->format('d/m/Y') }}</div>
                                @else
                                    <span style="font-size:12px; color:#a1a5b7;">Aucune</span>
                                @endif
                            </td>
                            <td style="padding-right:24px; text-align:right;">
                                <a href="{{ route('client.chantiers.show', $chantier) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                    Consulter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="kt-empty-state">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <p>Aucun chantier trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-client-layout>
