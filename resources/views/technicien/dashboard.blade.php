<x-technicien-layout>
@php
    /* ── Styles inline Soft UI (indépendants du layout parent) ── */
    $styleCard = 'background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,0.05);';
    $styleDark = 'background-image:linear-gradient(310deg,#141727 0%,#3a416f 100%);';
    $stylePrimary = 'background-image:linear-gradient(310deg,#7928ca 0%,#cb0c9f 100%);';
    $styleWarning = 'background-image:linear-gradient(310deg,#f53939 0%,#fbcf33 100%);';
@endphp

<div style="font-family:'Open Sans',sans-serif; padding:0 1.25rem 1.25rem;" class="space-y-6">

    {{-- ── WELCOME BANNER ── --}}
    <div style="{{ $styleDark }} border-radius:1.25rem; padding:2rem; position:relative; overflow:hidden;" class="text-white">
        {{-- Cercles décoratifs --}}
        <div style="position:absolute; top:-40px; right:-40px; width:180px; height:180px; border-radius:50%;
                    background:rgba(255,255,255,0.04);"></div>
        <div style="position:absolute; bottom:-60px; right:80px; width:240px; height:240px; border-radius:50%;
                    background:rgba(255,255,255,0.03);"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative">
            <div class="space-y-2">
                <span style="padding:0.25rem 0.875rem; border-radius:9999px; font-size:0.65rem; font-weight:800;
                             text-transform:uppercase; letter-spacing:0.1em; border:1px solid rgba(203,12,159,0.4);
                             background:rgba(203,12,159,0.15); color:#f9a8d4;">
                    Console Terrain Technicien
                </span>
                <h2 style="font-size:1.75rem; font-weight:900; color:#fff; margin:0.5rem 0 0.25rem;">
                    Bonjour, {{ Auth::user()->name }} 🛠️
                </h2>
                <p style="font-size:0.8rem; color:rgba(255,255,255,0.65); margin:0;">
                    Suivez votre planning et vos comptes-rendus terrain en temps réel.
                </p>
            </div>
            <div class="flex gap-3 shrink-0 flex-wrap">
                <a href="{{ route('interventions.index') }}"
                   style="{{ $stylePrimary }} border-radius:0.75rem; padding:0.625rem 1.25rem; font-size:0.75rem;
                          font-weight:700; color:#fff; text-decoration:none; display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="fas fa-clipboard-list"></i> Mes Interventions
                </a>
                <a href="{{ route('planning.index') }}"
                   style="background:rgba(255,255,255,0.12); border:1px solid rgba(255,255,255,0.2); border-radius:0.75rem;
                          padding:0.625rem 1.25rem; font-size:0.75rem; font-weight:700; color:#fff; text-decoration:none;
                          display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="fas fa-calendar-alt"></i> Planning
                </a>
            </div>
        </div>
    </div>

    {{-- ── ALERTE INTERVENTION EN COURS ── --}}
    @if($enCours)
        <div style="{{ $styleCard }} border-left:4px solid #f59e0b; padding:1.25rem 1.5rem;"
             class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span style="{{ $styleWarning }} border-radius:0.5rem; padding:0.2rem 0.75rem; font-size:0.65rem;
                             font-weight:800; text-transform:uppercase; color:#fff; letter-spacing:0.06em; display:inline-block;">
                    ⚡ Intervention Active
                </span>
                <h3 style="font-size:1.05rem; font-weight:900; color:#344767; margin:0.5rem 0 0.25rem;">
                    {{ $enCours->code_intervention }}
                    <span style="font-weight:500; color:#67748e;"> — {{ $enCours->chantier?->nom }}</span>
                </h3>
                <p style="font-size:0.75rem; color:#8392ab; margin:0;">{{ $enCours->description }}</p>
            </div>
            <a href="{{ route('interventions.show', $enCours) }}"
               style="{{ $stylePrimary }} border-radius:0.875rem; padding:0.75rem 1.5rem; font-size:0.75rem;
                      font-weight:800; color:#fff; text-decoration:none; display:inline-flex; align-items:center;
                      gap:0.5rem; white-space:nowrap; box-shadow:0 4px 15px rgba(203,12,159,0.35);">
                <i class="fas fa-file-signature"></i> Saisir Formulaire & Clôturer
            </a>
        </div>
    @endif

    {{-- ── KPI GRID ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-soft-kpi-card
            title="Missions Assignées"
            value="{{ $stats['total'] }}"
            subtitle="Charge totale attribuée"
            icon="fas fa-clipboard-list"
            gradient="primary"
        />
        <x-soft-kpi-card
            title="À Réaliser"
            value="{{ $stats['planifiees'] }}"
            subtitle="Prochains chantiers"
            icon="fas fa-calendar-alt"
            gradient="info"
        />
        <x-soft-kpi-card
            title="En Cours"
            value="{{ $stats['en_cours'] }}"
            subtitle="Sur le terrain maintenant"
            icon="fas fa-spinner"
            gradient="warning"
            :badgeText="$stats['en_cours'] > 0 ? 'Actif' : ''"
            badgeType="warning"
        />
        <x-soft-kpi-card
            title="Terminées"
            value="{{ $stats['terminees'] }}"
            subtitle="Rapports transmis"
            icon="fas fa-check-circle"
            gradient="success"
        />
    </div>

    {{-- ── PLANNING AUJOURD'HUI ── --}}
    <div style="{{ $styleCard }} padding:1.5rem;">
        <div class="flex items-center justify-between pb-4 mb-4" style="border-bottom:1px solid #f1f3f5;">
            <div>
                <h3 style="font-size:0.95rem; font-weight:800; color:#344767; margin:0 0 0.125rem;">
                    <i class="fas fa-calendar-day me-2 text-pink-500"></i>Planning d'Aujourd'hui
                </h3>
                <p style="font-size:0.7rem; color:#8392ab; margin:0;">
                    {{ now()->format('l d F Y') }}
                </p>
            </div>
            <a href="{{ route('interventions.index') }}"
               style="font-size:0.75rem; font-weight:700; color:#cb0c9f; text-decoration:none;">
                Toutes les interventions →
            </a>
        </div>

        <div class="divide-y" style="border-color:#f8f9fa;">
            @forelse($interventionsAujourdhui as $interv)
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        {{-- Icône heure --}}
                        <div style="width:2.25rem; height:2.25rem; border-radius:0.625rem; background:#f8f9fa;
                                    display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-clock" style="font-size:0.75rem; color:#67748e;"></i>
                        </div>
                        <div class="min-w-0">
                            <span style="font-size:0.8rem; font-weight:700; color:#344767; display:block;"
                                  class="truncate">
                                {{ $interv->code_intervention }} — {{ $interv->chantier?->nom }}
                            </span>
                            <span style="font-size:0.7rem; color:#8392ab;">
                                {{ $interv->date_prevue_debut?->format('H:i') }} · {{ $interv->emplacement?->nom ?? 'Emplacement N/A' }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('interventions.show', $interv) }}"
                       style="background:#f8f9fa; border:1px solid #e9ecef; border-radius:0.625rem; padding:0.4rem 0.875rem;
                              font-size:0.7rem; font-weight:700; color:#344767; text-decoration:none; white-space:nowrap;
                              transition:all 0.2s;" class="hover:bg-white">
                        Voir fiche →
                    </a>
                </div>
            @empty
                <div style="text-align:center; padding:2.5rem 1rem;">
                    <div style="width:3rem; height:3rem; border-radius:0.875rem; background:#f8f9fa;
                                display:flex; align-items:center; justify-content:center; margin:0 auto 0.75rem;">
                        <i class="fas fa-calendar-check" style="font-size:1.25rem; color:#8392ab;"></i>
                    </div>
                    <p style="font-size:0.8rem; color:#8392ab; font-weight:600; margin:0;">
                        Aucune intervention planifiée pour aujourd'hui.
                    </p>
                </div>
            @endforelse
        </div>
    </div>

</div>
</x-technicien-layout>
