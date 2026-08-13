<x-client-layout>
    <x-slot name="header">Détail du Chantier</x-slot>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; border-radius:12px; background:rgba(62,151,255,0.12); color:#3e97ff; display:flex; align-items:center; justify-content:center;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <h2 style="font-size:20px; font-weight:800; color:#181c32; margin-bottom:2px;">{{ $chantier->nom }}</h2>
                <div style="font-size:12px; color:#a1a5b7;">Code : {{ $chantier->code_chantier ?? 'N/A' }} · Localisation : {{ $chantier->ville ?? 'Non précisée' }}</div>
            </div>
        </div>
        <a href="{{ route('client.chantiers.index') }}" class="kt-btn kt-btn-light">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour aux chantiers
        </a>
    </div>

    <!-- Info Grid -->
    <div style="display:grid; grid-template-columns:1fr 2fr; gap:24px; align-items:start;">

        <!-- Information Card -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Informations Générales</div>
            </div>
            <div class="kt-card-body">
                <div style="display:grid; gap:16px;">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Adresse</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $chantier->adresse ?? '—' }}</div>
                    </div>
                    <hr class="kt-separator">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Responsable</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $chantier->responsable ?? '—' }}</div>
                        @if($chantier->telephone_responsable)<div style="font-size:11px; color:#a1a5b7;">Tél : {{ $chantier->telephone_responsable }}</div>@endif
                        @if($chantier->email_responsable)<div style="font-size:11px; color:#a1a5b7;">Email : {{ $chantier->email_responsable }}</div>@endif
                    </div>
                    @if($chantier->Description)
                        <hr class="kt-separator">
                        <div>
                            <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Description / Notes</div>
                            <div style="font-size:12px; color:#3f4254; background:#f5f8fa; padding:10px 12px; border-radius:8px; margin-top:4px;">{{ $chantier->Description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Interventions Card -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Interventions sur ce chantier ({{ $chantier->interventions->count() }})</div>
            </div>
            <div style="overflow-x:auto;">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th style="padding-left:20px;">Code</th>
                            <th>Type</th>
                            <th>Date prévue</th>
                            <th>Statut</th>
                            <th style="text-align:right; padding-right:20px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($chantier->interventions as $interv)
                            <tr>
                                <td style="padding-left:20px; font-weight:700; color:#181c32;">{{ $interv->code_intervention }}</td>
                                <td>{{ $interv->typeIntervention?->nom ?? '—' }}</td>
                                <td>{{ $interv->date_prevue_debut ? $interv->date_prevue_debut->format('d/m/Y H:i') : '—' }}</td>
                                <td>
                                    @php
                                        $badgeColor = match($interv->statut) {
                                            'Planifiee' => 'kt-badge-primary',
                                            'En cours'  => 'kt-badge-warning',
                                            'Terminee'  => 'kt-badge-success',
                                            'Annulee'   => 'kt-badge-danger',
                                            default     => 'kt-badge-gray'
                                        };
                                    @endphp
                                    <span class="kt-badge {{ $badgeColor }}">{{ $interv->statut }}</span>
                                </td>
                                <td style="padding-right:20px; text-align:right;">
                                    <a href="{{ route('client.interventions.show', $interv) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                        Consulter
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center; padding:30px; color:#a1a5b7;">
                                    Aucune intervention enregistrée sur ce chantier.
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</x-client-layout>
