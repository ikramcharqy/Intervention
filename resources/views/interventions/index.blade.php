<x-app-layout>
    <x-slot name="header">
        <h2 style="font-weight:800; font-size:1.1rem; color:#e2e8f0;">Interventions</h2>
    </x-slot>

@php
    $S = '#16162a'; $B = 'rgba(139,92,246,0.12)';
    $statusConfig = [
        'Demande'                 => ['bg'=>'rgba(96,165,250,0.15)',  'color'=>'#93c5fd', 'dot'=>'#60a5fa'],
        'Planifiee'               => ['bg'=>'rgba(251,191,36,0.15)',  'color'=>'#fde68a', 'dot'=>'#fbbf24'],
        'Affectee'                => ['bg'=>'rgba(167,139,250,0.15)', 'color'=>'#c4b5fd', 'dot'=>'#a78bfa'],
        'Acceptee'                => ['bg'=>'rgba(59,130,246,0.15)',  'color'=>'#93c5fd', 'dot'=>'#3b82f6'],
        'En cours'                => ['bg'=>'rgba(52,211,153,0.15)',  'color'=>'#6ee7b7', 'dot'=>'#34d399'],
        'Suspendue'               => ['bg'=>'rgba(245,158,11,0.15)',  'color'=>'#fcd34d', 'dot'=>'#f59e0b'],
        'Reportee'                => ['bg'=>'rgba(148,163,184,0.12)', 'color'=>'#94a3b8', 'dot'=>'#64748b'],
        'Partiellement realisee'  => ['bg'=>'rgba(251,146,60,0.15)',  'color'=>'#fdba74', 'dot'=>'#fb923c'],
        'Client absent'           => ['bg'=>'rgba(248,113,113,0.15)', 'color'=>'#fca5a5', 'dot'=>'#f87171'],
        'Materiel manquant'       => ['bg'=>'rgba(251,146,60,0.15)',  'color'=>'#fdba74', 'dot'=>'#fb923c'],
        'Deuxieme visite requise' => ['bg'=>'rgba(129,140,248,0.15)', 'color'=>'#a5b4fc', 'dot'=>'#818cf8'],
        'Formulaire rempli'       => ['bg'=>'rgba(52,211,153,0.12)',  'color'=>'#6ee7b7', 'dot'=>'#10b981'],
        'En attente validation'   => ['bg'=>'rgba(167,139,250,0.15)', 'color'=>'#c4b5fd', 'dot'=>'#8b5cf6'],
        'Rejetee'                 => ['bg'=>'rgba(239,68,68,0.15)',   'color'=>'#fca5a5', 'dot'=>'#ef4444'],
        'Terminee'                => ['bg'=>'rgba(16,185,129,0.15)',  'color'=>'#6ee7b7', 'dot'=>'#10b981'],
        'Validee'                 => ['bg'=>'rgba(52,211,153,0.2)',   'color'=>'#34d399', 'dot'=>'#34d399'],
        'Annulee'                 => ['bg'=>'rgba(100,116,139,0.12)', 'color'=>'#64748b', 'dot'=>'#475569'],
        'Rouverte'                => ['bg'=>'rgba(167,139,250,0.15)', 'color'=>'#c4b5fd', 'dot'=>'#a78bfa'],
        'En attente reafectation' => ['bg'=>'rgba(248,113,113,0.15)', 'color'=>'#fca5a5', 'dot'=>'#f87171'],
    ];
    $priConfig = [
        'Faible'  => ['bg'=>'rgba(100,116,139,0.15)', 'color'=>'#94a3b8'],
        'Normale' => ['bg'=>'rgba(59,130,246,0.15)',  'color'=>'#93c5fd'],
        'Haute'   => ['bg'=>'rgba(245,158,11,0.15)',  'color'=>'#fcd34d'],
        'Urgente' => ['bg'=>'rgba(239,68,68,0.2)',    'color'=>'#fca5a5'],
    ];
@endphp

<div style="display:flex; flex-direction:column; gap:1.25rem;">

    {{-- ── BARRE D'OUTILS ET FILTRES ── --}}
    <div style="background:{{ $S }}; border:1px solid {{ $B }}; border-radius:1rem;
                box-shadow:0 4px 24px rgba(0,0,0,0.45); padding:1.25rem;">
        <form method="GET" action="{{ route('interventions.index') }}"
              style="display:flex; flex-wrap:wrap; gap:0.875rem; align-items:flex-end;">

            {{-- Recherche --}}
            <div style="flex:1; min-width:220px;">
                <label style="display:block; font-size:0.58rem; font-weight:800; text-transform:uppercase;
                              letter-spacing:0.12em; color:#64748b; margin-bottom:0.35rem;">
                    <i class="fas fa-search" style="color:#8b5cf6; margin-right:0.25rem;"></i> Recherche
                </label>
                <input type="text" name="search" id="search" value="{{ $search ?? '' }}"
                       placeholder="Code, Client, Chantier, Technicien..."
                       style="width:100%; background:#1a1a2e; border:1px solid rgba(139,92,246,0.15);
                              border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.78rem;
                              color:#e2e8f0; outline:none; transition:border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='#8b5cf6'; this.style.boxShadow='0 0 0 3px rgba(139,92,246,0.2)';"
                       onblur="this.style.borderColor='rgba(139,92,246,0.15)'; this.style.boxShadow='none';">
            </div>

            {{-- Statut --}}
            <div style="min-width:180px;">
                <label style="display:block; font-size:0.58rem; font-weight:800; text-transform:uppercase;
                              letter-spacing:0.12em; color:#64748b; margin-bottom:0.35rem;">Statut</label>
                <select name="statut" id="statut"
                        style="width:100%; background:#1a1a2e; border:1px solid rgba(139,92,246,0.15);
                               border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.78rem;
                               color:#e2e8f0; outline:none;">
                    <option value="">Tous les statuts</option>
                    @foreach(['Planifiee','Acceptee','En cours','Formulaire rempli','Suspendue','Terminee','Validee','Annulee'] as $s)
                        <option value="{{ $s }}" @selected(($statut ?? '') == $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Priorité --}}
            <div style="min-width:160px;">
                <label style="display:block; font-size:0.58rem; font-weight:800; text-transform:uppercase;
                              letter-spacing:0.12em; color:#64748b; margin-bottom:0.35rem;">Priorité</label>
                <select name="priorite" id="priorite"
                        style="width:100%; background:#1a1a2e; border:1px solid rgba(139,92,246,0.15);
                               border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.78rem;
                               color:#e2e8f0; outline:none;">
                    <option value="">Toutes les priorités</option>
                    @foreach(['Faible','Normale','Haute','Urgente'] as $p)
                        <option value="{{ $p }}" @selected(($priorite ?? '') == $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Boutons --}}
            <div style="display:flex; gap:0.5rem; align-items:flex-end; flex-wrap:wrap; margin-left:auto;">
                @if($search || $statut || ($priorite ?? ''))
                    <a href="{{ route('interventions.index') }}"
                       style="background:rgba(100,116,139,0.15); border:1px solid rgba(100,116,139,0.2);
                              border-radius:0.75rem; padding:0.6rem 1rem; font-size:0.72rem; font-weight:700;
                              color:#94a3b8; text-decoration:none; display:inline-flex; align-items:center; gap:0.35rem;">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                @endif
                <button type="submit"
                        style="background-image:linear-gradient(310deg,#4c1d95 0%,#7c3aed 50%,#a855f7 100%);
                               border-radius:0.75rem; padding:0.6rem 1.25rem; font-size:0.72rem; font-weight:800;
                               color:#fff; border:none; cursor:pointer; display:inline-flex; align-items:center;
                               gap:0.35rem; text-transform:uppercase; letter-spacing:0.06em;
                               box-shadow:0 4px 15px rgba(124,58,237,0.4);">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                @can('create interventions')
                    <a href="{{ route('interventions.create') }}"
                       style="background-image:linear-gradient(310deg,#7c3aed 0%,#ec4899 100%);
                              border-radius:0.75rem; padding:0.6rem 1.25rem; font-size:0.72rem; font-weight:800;
                              color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:0.35rem;
                              text-transform:uppercase; letter-spacing:0.06em;
                              box-shadow:0 4px 15px rgba(236,72,153,0.35);">
                        <i class="fas fa-plus"></i> Nouvelle Intervention
                    </a>
                @endcan
            </div>
        </form>
    </div>

    {{-- ── TABLE DES INTERVENTIONS ── --}}
    <div style="background:{{ $S }}; border:1px solid {{ $B }}; border-radius:1rem;
                box-shadow:0 4px 24px rgba(0,0,0,0.45); overflow:hidden;">

        {{-- En-tête de la table --}}
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid rgba(139,92,246,0.1);
                    display:flex; align-items:center; justify-content:space-between; gap:1rem;">
            <div>
                <h3 style="font-size:0.95rem; font-weight:800; color:#e2e8f0; margin:0 0 0.2rem;">
                    <i class="fas fa-clipboard-list me-2" style="color:#8b5cf6;"></i>Registre des Interventions
                </h3>
                <p style="font-size:0.7rem; color:#64748b; margin:0;">
                    Liste temps réel · {{ $interventions->total() }} enregistrement(s)
                </p>
            </div>
            <span style="font-size:0.7rem; font-weight:700; font-family:monospace;
                         background:rgba(139,92,246,0.1); border:1px solid rgba(139,92,246,0.2);
                         border-radius:0.5rem; padding:0.3rem 0.75rem; color:#a78bfa;">
                Page {{ $interventions->currentPage() }} / {{ $interventions->lastPage() }}
            </span>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#13131f;">
                        @foreach(['Référence','Type','Chantier / Client','Technicien','Priorité','Statut','Date prévue','Actions'] as $col)
                            <th style="font-size:0.58rem; font-weight:800; text-transform:uppercase; letter-spacing:0.12em;
                                       color:#475569; padding:0.75rem 1rem; text-align:{{ $col==='Actions'?'right':'left' }};
                                       white-space:nowrap; border-bottom:1px solid rgba(139,92,246,0.1);">
                                {{ $col }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($interventions as $i)
                        @php
                            $sc = $statusConfig[$i->statut] ?? ['bg'=>'rgba(100,116,139,0.12)','color'=>'#94a3b8','dot'=>'#64748b'];
                            $pc = $priConfig[$i->priorite]   ?? ['bg'=>'rgba(100,116,139,0.12)','color'=>'#94a3b8'];
                            $isUrgent = $i->priorite === 'Urgente';
                        @endphp
                        <tr style="border-bottom:1px solid rgba(139,92,246,0.06); transition:background 0.15s;"
                            onmouseover="this.style.background='#1a1a2e';"
                            onmouseout="this.style.background='transparent';">

                            {{-- Référence --}}
                            <td style="padding:0.875rem 1rem;">
                                <div style="display:flex; align-items:center; gap:0.625rem;">
                                    <div style="width:1.875rem; height:1.875rem; border-radius:0.5rem; flex-shrink:0;
                                                background-image:linear-gradient(310deg,#4c1d95,#7c3aed);
                                                display:flex; align-items:center; justify-content:center;
                                                color:#fff; font-size:0.6rem;
                                                box-shadow:0 2px 8px rgba(124,58,237,0.4);">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <span style="font-family:monospace; font-weight:800; font-size:0.75rem; color:#a78bfa;">
                                        {{ $i->code_intervention }}
                                    </span>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td style="padding:0.875rem 1rem; font-size:0.78rem; color:#cbd5e1; font-weight:600;">
                                {{ $i->typeIntervention->nom ?? '—' }}
                            </td>

                            {{-- Chantier / Client --}}
                            <td style="padding:0.875rem 1rem;">
                                <div style="font-size:0.78rem; font-weight:700; color:#e2e8f0;">{{ $i->chantier->nom ?? '—' }}</div>
                                <div style="font-size:0.68rem; color:#64748b; margin-top:0.1rem;">{{ $i->chantier->client->nom ?? '—' }}</div>
                            </td>

                            {{-- Technicien --}}
                            <td style="padding:0.875rem 1rem;">
                                <div style="display:flex; align-items:center; gap:0.5rem;">
                                    <div style="width:1.75rem; height:1.75rem; border-radius:50%; flex-shrink:0;
                                                background:rgba(139,92,246,0.2); border:1px solid rgba(139,92,246,0.3);
                                                display:flex; align-items:center; justify-content:center;
                                                font-size:0.6rem; font-weight:900; color:#a78bfa;">
                                        {{ strtoupper(substr($i->technicien->name ?? 'NA', 0, 2)) }}
                                    </div>
                                    <span style="font-size:0.75rem; font-weight:600; color:{{ $i->technicien ? '#cbd5e1' : '#475569' }};">
                                        {{ $i->technicien ? ($i->technicien->prenom ?? '') . ' ' . $i->technicien->name : 'Non assigné' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Priorité --}}
                            <td style="padding:0.875rem 1rem;">
                                <span style="background:{{ $pc['bg'] }}; color:{{ $pc['color'] }};
                                             font-size:0.6rem; font-weight:800; text-transform:uppercase;
                                             letter-spacing:0.08em; padding:0.25rem 0.625rem; border-radius:0.5rem;
                                             {{ $isUrgent ? 'animation:pulse 2s infinite;' : '' }}">
                                    {{ $i->priorite }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td style="padding:0.875rem 1rem;">
                                <span style="background:{{ $sc['bg'] }}; display:inline-flex; align-items:center;
                                             gap:0.3rem; padding:0.25rem 0.625rem; border-radius:9999px;
                                             font-size:0.6rem; font-weight:800; text-transform:uppercase;
                                             letter-spacing:0.06em; color:{{ $sc['color'] }}; white-space:nowrap;">
                                    <span style="width:0.375rem; height:0.375rem; border-radius:50%;
                                                 background:{{ $sc['dot'] }};
                                                 {{ $i->statut === 'En cours' ? 'animation:pulse 2s infinite;' : '' }}"></span>
                                    {{ $i->statut }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td style="padding:0.875rem 1rem; font-size:0.72rem; font-family:monospace; color:#64748b; white-space:nowrap;">
                                {{ $i->date_prevue_debut ? $i->date_prevue_debut->format('d/m/Y H:i') : '—' }}
                            </td>

                            {{-- Actions --}}
                            <td style="padding:0.875rem 1rem; text-align:right;">
                                <div style="display:flex; align-items:center; justify-content:flex-end; gap:0.4rem;">
                                    <a href="{{ route('interventions.show', $i) }}"
                                       style="background:rgba(139,92,246,0.12); border:1px solid rgba(139,92,246,0.2);
                                              border-radius:0.5rem; padding:0.35rem 0.75rem; font-size:0.7rem;
                                              font-weight:700; color:#a78bfa; text-decoration:none; display:inline-flex;
                                              align-items:center; gap:0.3rem; transition:all 0.15s;"
                                       onmouseover="this.style.background='rgba(139,92,246,0.25)';"
                                       onmouseout="this.style.background='rgba(139,92,246,0.12)';">
                                        <i class="fas fa-eye"></i> Voir
                                    </a>
                                    @can('edit interventions')
                                        <a href="{{ route('interventions.edit', $i) }}"
                                           style="background-image:linear-gradient(310deg,#4c1d95,#7c3aed);
                                                  border-radius:0.5rem; padding:0.35rem 0.75rem; font-size:0.7rem;
                                                  font-weight:700; color:#fff; text-decoration:none; display:inline-flex;
                                                  align-items:center; gap:0.3rem; transition:opacity 0.15s;
                                                  box-shadow:0 2px 8px rgba(124,58,237,0.35);"
                                           onmouseover="this.style.opacity='0.85';"
                                           onmouseout="this.style.opacity='1';">
                                            <i class="fas fa-edit"></i> Éditer
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:3.5rem 1.5rem; text-align:center;">
                                <div style="width:3.5rem; height:3.5rem; border-radius:1rem; background:rgba(139,92,246,0.1);
                                            border:1px solid rgba(139,92,246,0.15); display:flex; align-items:center;
                                            justify-content:center; margin:0 auto 1rem; font-size:1.25rem; color:#8b5cf6;">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <p style="font-size:0.875rem; font-weight:700; color:#94a3b8; margin:0 0 0.5rem;">
                                    Aucune intervention trouvée
                                </p>
                                <p style="font-size:0.75rem; color:#475569; margin:0;">
                                    Essayez de modifier vos filtres de recherche.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($interventions->hasPages())
            <div style="padding:1rem 1.5rem; border-top:1px solid rgba(139,92,246,0.1);">
                {{ $interventions->links() }}
            </div>
        @endif
    </div>

</div>
</x-app-layout>
