<x-super-admin-layout>
    <div class="space-y-6">

        <!-- Banner Header Super Admin Executive Light -->
        <div class="ui-card p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-rose-950 text-white border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm rounded-2xl">
            <div class="z-10 max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-rose-500/20 border border-rose-400/30 text-rose-300 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <span>Console Infrastructure & Sécurité</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Supervision Système & APIs
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 leading-relaxed">
                    Gestion globale des accès rôles Spatie, monitoring des bases de données et intégrations externes.
                </p>
            </div>
            
            <div class="flex items-center gap-3 z-10 flex-wrap shrink-0">
                <a href="{{ route('superadmin.settings') }}" class="ui-btn ui-btn-secondary text-xs py-2.5 px-4">
                    <i class="fas fa-cog text-slate-600"></i>
                    <span>Config APIs & Maps</span>
                </a>
                <form method="POST" action="{{ route('superadmin.backups.create') }}" class="inline">
                    @csrf
                    <button type="submit" class="ui-btn ui-btn-primary text-xs py-2.5 px-4 !bg-rose-600 hover:!bg-rose-700 shadow-sm">
                        <i class="fas fa-database"></i>
                        <span>Générer Dump SQL</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- KPI Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <x-soft-kpi-card
                title="Administrateurs"
                value="{{ $stats['total_admins'] }}"
                subtitle="Privilèges d'administration"
                icon="fas fa-user-shield"
                gradient="danger"
            />
            <x-soft-kpi-card
                title="Total Comptes"
                value="{{ $stats['total_users'] }}"
                subtitle="{{ $stats['active_sessions'] }} sessions actives"
                icon="fas fa-users"
                gradient="info"
            />
            <x-soft-kpi-card
                title="Interventions"
                value="{{ $stats['total_interventions'] }}"
                subtitle="{{ $stats['total_chantiers'] }} Chantiers actifs"
                icon="fas fa-building"
                gradient="warning"
            />
            <x-soft-kpi-card
                title="Taille BDD MySQL"
                value="{{ $stats['db_size'] }}"
                subtitle="Espace disque alloué"
                icon="fas fa-database"
                gradient="success"
            />
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Colonne gauche (2/3) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Répartition des rôles Spatie -->
                <div class="ui-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                        <div>
                            <h3 class="ui-card-title flex items-center gap-2 text-slate-900">
                                <i class="fas fa-sitemap text-indigo-600"></i>
                                <span>Répartition des Rôles Spatie</span>
                            </h3>
                            <p class="ui-card-subtitle">Distribution des comptes utilisateurs par périmètre</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200 text-slate-700 font-mono text-xs font-bold">
                            Total : {{ $stats['total_users'] }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @php
                            $roleColors = [
                                'Super Admin'     => 'bg-rose-500',
                                'Administrateur'  => 'bg-indigo-600',
                                'Admin'           => 'bg-indigo-600',
                                'Commercial'      => 'bg-emerald-500',
                                'Technicien'      => 'bg-amber-500',
                                'Client'          => 'bg-sky-500',
                            ];
                        @endphp

                        @foreach($usersByRole as $role => $count)
                            @php
                                $percent = $stats['total_users'] > 0 ? round(($count / $stats['total_users']) * 100) : 0;
                                $barColor = $roleColors[$role] ?? 'bg-indigo-600';
                            @endphp
                            <div>
                                <div class="flex justify-between items-center text-xs mb-1.5 font-semibold">
                                    <span class="text-slate-800">{{ $role }}</span>
                                    <span class="font-mono text-slate-500 text-[11px]">{{ $count }} compte(s) · {{ $percent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ max(4, $percent) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Journaux d'Audit Réels -->
                <div class="ui-card p-5 sm:p-6">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <h3 class="ui-card-title flex items-center gap-2 text-slate-900">
                            <i class="fas fa-shield-alt text-rose-600"></i>
                            <span>Derniers Événements d'Audit</span>
                        </h3>
                        <a href="{{ route('superadmin.logs') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                            Voir tous les logs →
                        </a>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($recentLogs as $log)
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-white border border-slate-200 text-slate-700 shrink-0 shadow-2xs">
                                        {{ $log->module }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $log->action }}</p>
                                        <p class="text-[11px] text-slate-500">Par {{ $log->user_name }} · IP: {{ $log->ip_address }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono text-slate-500 whitespace-nowrap shrink-0">
                                    {{ $log->created_at?->diffForHumans() }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-500 py-4 italic text-center">Aucun événement loggé.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Colonne droite (1/3) : Comptes Récents -->
            <div class="ui-card p-5 sm:p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <h3 class="ui-card-title flex items-center gap-2 text-slate-900">
                            <i class="fas fa-user-plus text-sky-600"></i>
                            <span>Comptes Récents</span>
                        </h3>
                        <a href="{{ route('superadmin.users') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">Tous →</a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($recentUsers as $u)
                            @php
                                $rname = $u->roles->pluck('name')->first() ?? 'User';
                            @endphp
                            <div class="py-3 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $u->name }}</p>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-slate-100 border border-slate-200 text-slate-700 shrink-0">
                                    {{ $rname }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100">
                    <a href="{{ route('superadmin.admins') }}" class="ui-btn ui-btn-secondary w-full text-xs py-2 text-center block">
                        <i class="fas fa-plus mr-1"></i> Créer un Administrateur
                    </a>
                </div>
            </div>
        </div>

    </div>
</x-super-admin-layout>
