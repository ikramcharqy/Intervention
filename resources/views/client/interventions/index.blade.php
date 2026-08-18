<x-client-layout>
    <x-slot name="header">Mes Interventions</x-slot>

    <!-- Filter Buttons -->
    <div style="display:flex; align-items:center; gap:8px; margin-bottom:20px; flex-wrap:wrap;">
        <a href="{{ route('client.interventions.index', ['statut' => 'tous']) }}" class="kt-btn {{ $statutFiltre === 'tous' ? 'kt-btn-primary' : 'kt-btn-light' }}">
            Toutes
        </a>
        <a href="{{ route('client.interventions.index', ['statut' => 'Planifiee']) }}" class="kt-btn {{ $statutFiltre === 'Planifiee' ? 'kt-btn-primary' : 'kt-btn-light' }}">
            Planifiées
        </a>
        <a href="{{ route('client.interventions.index', ['statut' => 'En cours']) }}" class="kt-btn {{ $statutFiltre === 'En cours' ? 'kt-btn-primary' : 'kt-btn-light' }}">
            En cours
        </a>
        <a href="{{ route('client.interventions.index', ['statut' => 'Terminee']) }}" class="kt-btn {{ $statutFiltre === 'Terminee' ? 'kt-btn-primary' : 'kt-btn-light' }}">
            Terminées
        </a>
        <a href="{{ route('client.interventions.index', ['statut' => 'Annulee']) }}" class="kt-btn {{ $statutFiltre === 'Annulee' ? 'kt-btn-primary' : 'kt-btn-light' }}">
            Annulées
        </a>
    </div>

    <!-- Table Card -->
    <div class="kt-card">
        <div class="kt-card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div class="kt-card-title">Interventions</div>
                <div style="font-size:12px; color:#a1a5b7; margin-top:3px;">Consultation et suivi de vos interventions</div>
            </div>
            <div>
                <a href="{{ route('client.demandes.create') }}" class="kt-btn kt-btn-primary" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Demander une Intervention</span>
                </a>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="kt-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Code</th>
                        <th>Type</th>
                        <th>Chantier</th>
                        <th>Priorité</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Technicien</th>
                        <th style="text-align:right; padding-right:24px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interventions as $interv)
                        <tr>
                            <td style="padding-left:24px; font-weight:700; color:#181c32;">
                                {{ $interv->code_intervention }}
                            </td>
                            <td>{{ $interv->typeIntervention?->nom ?? '—' }}</td>
                            <td>
                                <div style="font-weight:600; color:#181c32;">{{ $interv->chantier?->nom ?? '—' }}</div>
                                @if($interv->chantier?->ville)<div style="font-size:11px; color:#a1a5b7;">{{ $interv->chantier->ville }}</div>@endif
                            </td>
                            <td>
                                @php
                                    $prioColor = match($interv->priorite) {
                                        'Urgente' => 'kt-badge-danger',
                                        'Haute'   => 'kt-badge-warning',
                                        'Normale' => 'kt-badge-primary',
                                        'Faible'  => 'kt-badge-gray',
                                        default   => 'kt-badge-gray'
                                    };
                                @endphp
                                <span class="kt-badge {{ $prioColor }}">{{ $interv->priorite }}</span>
                            </td>
                            <td>
                                <div style="font-size:12px; color:#181c32; font-weight:500;">
                                    {{ $interv->date_prevue_debut ? $interv->date_prevue_debut->format('d/m/Y H:i') : '—' }}
                                </div>
                            </td>
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
                            <td>
                                <span style="font-size:13px; color:#3f4254;">{{ $interv->technicien?->name ?? 'Non assigné' }}</span>
                            </td>
                            <td style="padding-right:24px; text-align:right;">
                                <a href="{{ route('client.interventions.show', $interv) }}" class="kt-btn kt-btn-light-primary kt-btn-sm">
                                    Consulter
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="kt-empty-state">
                                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                                    <p>Aucune intervention trouvée.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($interventions, 'hasPages') && $interventions->hasPages())
            <div style="padding:16px 24px; border-top:1px dashed #eff2f5;">
                {{ $interventions->links() }}
            </div>
        @endif
    </div>

</x-client-layout>
