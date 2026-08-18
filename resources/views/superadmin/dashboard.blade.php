<x-super-admin-layout>
@php
    $styleCard = 'background:#fff; border-radius:1rem; box-shadow:0 20px 27px 0 rgba(0,0,0,0.05);';
    $styleDark = 'background-image:linear-gradient(310deg,#141727 0%,#3a416f 100%);';
    $stylePrimary = 'background-image:linear-gradient(310deg,#7928ca 0%,#cb0c9f 100%);';

    $roleGradients = [
        'Super Admin'     => 'linear-gradient(310deg,#7928ca 0%,#cb0c9f 100%)',
        'Administrateur'  => 'linear-gradient(310deg,#2152ff 0%,#21d4fd 100%)',
        'Admin'           => 'linear-gradient(310deg,#2152ff 0%,#21d4fd 100%)',
        'Commercial'      => 'linear-gradient(310deg,#17ad37 0%,#98ec2d 100%)',
        'Technicien'      => 'linear-gradient(310deg,#f53939 0%,#fbcf33 100%)',
        'Client'          => 'linear-gradient(310deg,#627594 0%,#a8b8d0 100%)',
    ];
@endphp

<div style="font-family:'Open Sans',sans-serif; padding:0 1.25rem 1.25rem;" class="space-y-6">

    {{-- ── BANNIÈRE HEADER ── --}}
    <div style="{{ $styleDark }} border-radius:1.25rem; padding:1.75rem 2rem; position:relative; overflow:hidden;" class="text-white">
        <div style="position:absolute; top:-50px; right:-50px; width:200px; height:200px; border-radius:50%;
                    background:rgba(255,255,255,0.04);"></div>
        <div style="position:absolute; bottom:-70px; right:100px; width:260px; height:260px; border-radius:50%;
                    background:rgba(255,255,255,0.03);"></div>

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative">
            <div class="space-y-1">
                <span style="font-size:0.625rem; font-weight:800; text-transform:uppercase; letter-spacing:0.1em;
                             color:#a78bfa; background:rgba(167,139,250,0.12); border:1px solid rgba(167,139,250,0.25);
                             padding:0.2rem 0.75rem; border-radius:9999px;">
                    Console Supervision Infrastructure
                </span>
                <h2 style="font-size:1.5rem; font-weight:900; color:#fff; margin:0.375rem 0 0.25rem;">
                    Supervision Infrastructure & APIs
                </h2>
                <p style="font-size:0.75rem; color:rgba(255,255,255,0.55); margin:0;">
                    Données certifiées BDD MySQL · {{ now()->format('d/m/Y H:i') }}
                </p>
            </div>
            <div class="flex gap-2 flex-wrap shrink-0">
                <a href="{{ route('superadmin.settings') }}"
                   style="background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15); border-radius:0.75rem;
                          padding:0.6rem 1.1rem; font-size:0.7rem; font-weight:700; color:#fff; text-decoration:none;
                          display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="fas fa-cog"></i> Config Maps & Caméras
                </a>
                <form method="POST" action="{{ route('superadmin.backups.create') }}" class="inline">
                    @csrf
                    <button type="submit"
                            style="{{ $stylePrimary }} border-radius:0.75rem; padding:0.6rem 1.1rem; font-size:0.7rem;
                                   font-weight:800; color:#fff; border:none; cursor:pointer; display:inline-flex;
                                   align-items:center; gap:0.4rem; box-shadow:0 4px 12px rgba(203,12,159,0.35);">
                        <i class="fas fa-database"></i> Générer Dump SQL
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── KPI GRID ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <x-soft-kpi-card
            title="Administrateurs"
            value="{{ $stats['total_admins'] }}"
            subtitle="Privilèges métier actifs"
            icon="fas fa-user-shield"
            gradient="danger"
        />
        <x-soft-kpi-card
            title="Total Comptes"
            value="{{ $stats['total_users'] }}"
            subtitle="{{ $stats['active_sessions'] }} comptes actifs"
            icon="fas fa-users"
            gradient="info"
        />
        <x-soft-kpi-card
            title="Interventions"
            value="{{ $stats['total_interventions'] }}"
            subtitle="{{ $stats['total_chantiers'] }} Chantiers"
            icon="fas fa-building"
            gradient="warning"
        />
        <x-soft-kpi-card
            title="Taille Base MySQL"
            value="{{ $stats['db_size'] }}"
            subtitle="information_schema"
            icon="fas fa-database"
            gradient="success"
        />
    </div>

    {{-- ── CONTENU PRINCIPAL ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne gauche (2/3) --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Répartition des rôles --}}
            <div style="{{ $styleCard }} padding:1.5rem;">
                <div class="flex items-center justify-between pb-4 mb-5" style="border-bottom:1px solid #f1f3f5;">
                    <div>
                        <h3 style="font-size:0.95rem; font-weight:800; color:#344767; margin:0 0 0.125rem;">
                            <i class="fas fa-sitemap me-2" style="color:#7928ca;"></i>Répartition des Rôles Spatie
                        </h3>
                        <p style="font-size:0.7rem; color:#8392ab; margin:0;">Distribution des comptes par périmètre</p>
                    </div>
                    <span style="font-size:0.7rem; font-weight:700; font-family:monospace; padding:0.25rem 0.75rem;
                                 background:#f8f9fa; border-radius:0.5rem; color:#344767;">
                        Total: {{ $stats['total_users'] }}
                    </span>
                </div>
                <div class="space-y-4">
                    @foreach($usersByRole as $role => $count)
                        @php
                            $percent = $stats['total_users'] > 0 ? round(($count / $stats['total_users']) * 100) : 0;
                            $bg = $roleGradients[$role] ?? 'linear-gradient(310deg,#627594 0%,#a8b8d0 100%)';
                        @endphp
                        <div>
                            <div class="flex justify-between mb-1.5">
                                <span style="font-size:0.78rem; font-weight:700; color:#344767;">{{ $role }}</span>
                                <span style="font-size:0.7rem; font-family:monospace; color:#8392ab; font-weight:600;">
                                    {{ $count }} compte(s) · {{ $percent }}%
                                </span>
                            </div>
                            <div style="height:0.375rem; background:#f1f3f5; border-radius:9999px; overflow:hidden;">
                                <div style="height:100%; width:{{ max(4, $percent) }}%; background-image:{{ $bg }};
                                            border-radius:9999px; transition:width 0.5s ease;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Journaux d'Audit --}}
            <div style="{{ $styleCard }} padding:1.5rem;">
                <div class="flex items-center justify-between pb-4 mb-4" style="border-bottom:1px solid #f1f3f5;">
                    <h3 style="font-size:0.95rem; font-weight:800; color:#344767; margin:0;">
                        <i class="fas fa-shield-alt me-2" style="color:#ea0606;"></i>Journaux d'Audit Réels
                    </h3>
                    <a href="{{ route('superadmin.logs') }}"
                       style="font-size:0.75rem; font-weight:700; color:#cb0c9f; text-decoration:none;">
                        Voir les logs →
                    </a>
                </div>
                <div class="space-y-2">
                    @forelse($recentLogs as $log)
                        <div style="background:#f8f9fa; border:1px solid #f1f3f5; border-radius:0.75rem; padding:0.75rem 1rem;"
                             class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span style="font-size:0.6rem; font-family:monospace; font-weight:800; padding:0.2rem 0.5rem;
                                             background:#fff; border:1px solid #dee2e6; border-radius:0.375rem;
                                             color:#344767; flex-shrink:0;">
                                    {{ $log->module }}
                                </span>
                                <div class="min-w-0">
                                    <span style="font-size:0.78rem; font-weight:700; color:#344767; display:block;"
                                          class="truncate">{{ $log->action }}</span>
                                    <span style="font-size:0.65rem; color:#8392ab;">
                                        {{ $log->user_name }} · IP: {{ $log->ip_address }}
                                    </span>
                                </div>
                            </div>
                            <span style="font-size:0.65rem; font-family:monospace; color:#8392ab; white-space:nowrap; flex-shrink:0;">
                                {{ $log->created_at?->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <div style="text-align:center; padding:2rem; color:#8392ab; font-size:0.8rem;">
                            Aucun événement loggé.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne droite (1/3) — Comptes récents --}}
        <div style="{{ $styleCard }} padding:1.5rem;" class="flex flex-col">
            <div class="flex items-center justify-between pb-4 mb-4" style="border-bottom:1px solid #f1f3f5;">
                <h3 style="font-size:0.95rem; font-weight:800; color:#344767; margin:0;">
                    <i class="fas fa-user-plus me-2" style="color:#2152ff;"></i>Comptes Récents
                </h3>
                <a href="{{ route('superadmin.users') }}"
                   style="font-size:0.75rem; font-weight:700; color:#cb0c9f; text-decoration:none;">Tous →</a>
            </div>

            <div class="flex-1 divide-y" style="border-color:#f8f9fa;">
                @foreach($recentUsers as $u)
                    @php
                        $rname = $u->roles->pluck('name')->first() ?? 'User';
                        $rbg = $roleGradients[$rname] ?? 'linear-gradient(310deg,#627594 0%,#a8b8d0 100%)';
                    @endphp
                    <div class="py-3 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            <div style="width:2.25rem; height:2.25rem; border-radius:0.625rem; flex-shrink:0;
                                        background-image:{{ $rbg }}; display:flex; align-items:center;
                                        justify-content:center; color:#fff; font-size:0.65rem; font-weight:900;
                                        box-shadow:0 4px 8px rgba(0,0,0,0.12);">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <span style="font-size:0.78rem; font-weight:700; color:#344767; display:block;"
                                      class="truncate">{{ $u->name }}</span>
                                <span style="font-size:0.65rem; color:#8392ab;" class="truncate block">{{ $u->email }}</span>
                            </div>
                        </div>
                        <span style="font-size:0.6rem; font-weight:800; text-transform:uppercase; letter-spacing:0.06em;
                                     padding:0.2rem 0.5rem; background:#f8f9fa; border:1px solid #e9ecef;
                                     border-radius:0.5rem; color:#344767; white-space:nowrap; flex-shrink:0;">
                            {{ $rname }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 pt-4" style="border-top:1px solid #f1f3f5;">
                <a href="{{ route('superadmin.admins') }}"
                   style="display:block; text-align:center; background:#f8f9fa; border:1px solid #e9ecef;
                          border-radius:0.75rem; padding:0.7rem; font-size:0.75rem; font-weight:800;
                          color:#344767; text-decoration:none; transition:all 0.2s;"
                   class="hover:bg-white">
                    <i class="fas fa-plus me-1" style="color:#7928ca;"></i> Ajouter un Administrateur
                </a>
            </div>
        </div>
    </div>

</div>
</x-super-admin-layout>
