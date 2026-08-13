<x-client-layout>
    <x-slot name="header">Détail Intervention {{ $intervention->code_intervention }}</x-slot>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div style="width:48px; height:48px; border-radius:12px; background:rgba(114,57,234,0.12); color:#7239ea; display:flex; align-items:center; justify-content:center;">
                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
            </div>
            <div>
                <h2 style="font-size:20px; font-weight:800; color:#181c32; margin-bottom:2px;">{{ $intervention->code_intervention }}</h2>
                <div style="display:flex; align-items:center; gap:8px;">
                    @php
                        $badgeColor = match($intervention->statut) {
                            'Planifiee' => 'kt-badge-primary',
                            'En cours'  => 'kt-badge-warning',
                            'Terminee'  => 'kt-badge-success',
                            'Annulee'   => 'kt-badge-danger',
                            default     => 'kt-badge-gray'
                        };
                    @endphp
                    <span class="kt-badge {{ $badgeColor }}">{{ $intervention->statut }}</span>
                    <span style="font-size:12px; color:#a1a5b7;">Type: {{ $intervention->typeIntervention?->nom ?? 'Standard' }}</span>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="{{ route('client.interventions.index') }}" class="kt-btn kt-btn-light">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Retour
            </a>
            @if($intervention->rapport)
                <a href="{{ route('client.rapports.show', $intervention->rapport) }}" class="kt-btn kt-btn-success">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Consulter le Rapport
                </a>
            @endif
        </div>
    </div>

    <!-- Layout Grid -->
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px; align-items:start;">

        <!-- Main Info -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Informations de l'Intervention</div>
            </div>
            <div class="kt-card-body">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Chantier</div>
                        <div style="font-size:14px; font-weight:700; color:#181c32; margin-top:2px;">{{ $intervention->chantier?->nom ?? '—' }}</div>
                        <div style="font-size:11px; color:#a1a5b7;">{{ $intervention->chantier?->adresse }}</div>
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Emplacement</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $intervention->emplacement?->nom ?? 'Principal' }}</div>
                    </div>
                </div>

                <hr class="kt-separator" style="margin-bottom:20px;">

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:20px;">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Technicien Assigné</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">{{ $intervention->technicien?->name ?? 'Non assigné' }}</div>
                        @if($intervention->technicien?->telephone)
                            <div style="font-size:11px; color:#a1a5b7;">Tél: {{ $intervention->technicien->telephone }}</div>
                        @endif
                    </div>
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Priorité</div>
                        <span class="kt-badge kt-badge-primary" style="margin-top:4px;">{{ $intervention->priorite }}</span>
                    </div>
                </div>

                <hr class="kt-separator" style="margin-bottom:20px;">

                <div>
                    <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:6px;">Description</div>
                    <div style="font-size:13px; color:#3f4254; background:#f5f8fa; padding:14px 16px; border-radius:10px; line-height:1.6;">
                        {{ $intervention->description ?? 'Aucune description fournie.' }}
                    </div>
                </div>

                @if($intervention->observations)
                    <div style="margin-top:16px;">
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase; margin-bottom:6px;">Observations</div>
                        <div style="font-size:13px; color:#3f4254; background:rgba(255,199,0,0.08); border:1px solid rgba(255,199,0,0.2); padding:14px 16px; border-radius:10px; line-height:1.6;">
                            {{ $intervention->observations }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Timeline & Dates -->
        <div class="kt-card">
            <div class="kt-card-header">
                <div class="kt-card-title">Planning & Dates</div>
            </div>
            <div class="kt-card-body">
                <div style="display:grid; gap:16px;">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Date prévue début</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">
                            {{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '—' }}
                        </div>
                    </div>
                    <hr class="kt-separator">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Date prévue fin</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">
                            {{ $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('d/m/Y H:i') : '—' }}
                        </div>
                    </div>
                    <hr class="kt-separator">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Date réelle début</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">
                            {{ $intervention->date_reelle_debut ? $intervention->date_reelle_debut->format('d/m/Y H:i') : 'Non commencée' }}
                        </div>
                    </div>
                    <hr class="kt-separator">
                    <div>
                        <div style="font-size:11px; font-weight:700; color:#a1a5b7; text-transform:uppercase;">Date réelle fin</div>
                        <div style="font-size:13px; font-weight:600; color:#181c32; margin-top:2px;">
                            {{ $intervention->date_reelle_fin ? $intervention->date_reelle_fin->format('d/m/Y H:i') : 'Non terminée' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-client-layout>
