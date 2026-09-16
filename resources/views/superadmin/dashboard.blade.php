<x-super-admin-layout>
    <div class="space-y-6" x-data="{ showDumpModal: false, showIncidentPanel: false }">

        <!-- Banner Header Super Admin -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border-l-4 border-[#1E5EFF] p-7 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#FDE3E6] dark:bg-rose-500/10 border border-[#F8C4CA] dark:border-rose-500/30 text-[#F0142F] dark:text-rose-400 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-[#F0142F] dark:bg-rose-400"></span>
                    <span>Console Infrastructure &amp; Sécurité</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">
                    Supervision Système &amp; APIs
                </h2>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1 leading-relaxed">
                    Gestion globale des accès rôles Spatie, monitoring des bases de données et intégrations externes.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap shrink-0">
                <a href="{{ route('superadmin.settings') }}" class="inline-flex items-center gap-2 rounded-[4px] bg-white dark:bg-slate-800 border border-[#D7DBEC] dark:border-slate-700 text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 hover:text-[#131523] dark:hover:text-white text-xs font-semibold px-4 py-2.5 transition">
                    <x-icon name="settings" class="w-4 h-4" />
                    <span>Config APIs &amp; Maps</span>
                </a>
                <button type="button" @click="showDumpModal = true" class="inline-flex items-center gap-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold px-4 py-2.5 transition shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)]">
                    <x-icon name="circle-stack" class="w-4 h-4" />
                    <span>Générer Dump SQL</span>
                </button>
                <button type="button" @click="showIncidentPanel = true" class="inline-flex items-center gap-2 rounded-[4px] bg-white dark:bg-slate-800 border border-[#F8C4CA] dark:border-rose-500/30 text-[#F0142F] dark:text-rose-400 hover:bg-[#FDE3E6] dark:hover:bg-rose-500/10 text-xs font-semibold px-4 py-2.5 transition">
                    <x-icon name="warning" class="w-4 h-4" />
                    <span>Action Incident</span>
                </button>
            </div>
        </div>

        <!-- Étape 4.1 — Modale de confirmation explicite avant génération du dump SQL -->
        <div x-show="showDumpModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4">
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-base font-bold text-[#131523] dark:text-slate-100 flex items-center gap-2"><x-icon name="warning" class="w-4 h-4 text-[#F0142F] dark:text-rose-400" /> Confirmer la génération du dump SQL</h3>
                <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed">
                    Ce fichier contiendra <strong>l'intégralité des données de production</strong>, y compris les données personnelles des clients/utilisateurs et les mots de passe hashés. Il sera stocké sur le serveur (<code>storage/app/backups</code>) sans chiffrement au repos — traitez-le avec le même niveau de confidentialité que la base de données elle-même, et supprimez-le dès qu'il n'est plus nécessaire.
                </p>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showDumpModal = false" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                    <form method="POST" action="{{ route('superadmin.backups.create') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-[4px] bg-[#F0142F] hover:bg-[#c9102a] text-white text-xs font-bold">Confirmer &amp; Générer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Étape 7.1 — Actions d'incident immédiates, accessibles directement depuis le dashboard racine -->
        <div x-show="showIncidentPanel" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4">
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-xl w-full max-w-md p-6 space-y-4">
                <h3 class="text-base font-bold text-[#131523] dark:text-slate-100 flex items-center gap-2"><x-icon name="warning" class="w-4 h-4 text-[#F0142F] dark:text-rose-400" /> Action Incident Immédiate</h3>
                <p class="text-xs text-[#5A607F] dark:text-slate-400">Désactive un compte ou révoque toutes ses sessions actives instantanément. Nécessite une re-confirmation du mot de passe.</p>
                <form method="POST" action="{{ route('superadmin.incidents.disableAccount') }}" class="flex gap-2">
                    @csrf
                    <input type="email" name="email" required placeholder="email@compte.ma" class="flex-1 px-3 py-2 border border-[#D7DBEC] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-[4px] text-xs">
                    <button type="submit" class="px-3 py-2 bg-[#F0142F] hover:bg-[#c9102a] text-white text-xs font-bold rounded-[4px] whitespace-nowrap">Désactiver</button>
                </form>
                <form method="POST" action="{{ route('superadmin.incidents.revokeSessions') }}" class="flex gap-2">
                    @csrf
                    <input type="email" name="email" required placeholder="email@compte.ma" class="flex-1 px-3 py-2 border border-[#D7DBEC] dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 rounded-[4px] text-xs">
                    <button type="submit" class="px-3 py-2 bg-[#131523] hover:bg-black text-white text-xs font-bold rounded-[4px] whitespace-nowrap">Révoquer sessions</button>
                </form>
                <div class="flex justify-end pt-2">
                    <button type="button" @click="showIncidentPanel = false" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Fermer</button>
                </div>
            </div>
        </div>

        <!-- Alerte comptes sans rôle Spatie : priorité absolue, visible avant tout le reste -->
        @if($stats['unassigned_accounts'] > 0)
            <div class="bg-[#FDE3E6] dark:bg-rose-500/10 border border-[#F8C4CA] dark:border-rose-500/30 rounded-[6px] p-4 flex items-center gap-3">
                <x-icon name="warning" class="w-4 h-4 text-[#F0142F] dark:text-rose-400" />
                <p class="text-xs sm:text-sm text-[#8A0E20] dark:text-rose-300 font-semibold">
                    {{ $stats['unassigned_accounts'] }} compte(s) détecté(s) sans rôle Spatie assigné —
                    <a href="{{ route('superadmin.users', ['role' => 'unassigned']) }}" class="underline hover:no-underline">voir les comptes concernés</a>.
                </p>
            </div>
        @endif

        <!-- KPI Stats Bar -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 lg:divide-x divide-[#E6E9F4] dark:divide-slate-800">
                @php
                    $kpiThemes = [
                        'danger'  => ['bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10', 'text' => 'text-[#F0142F] dark:text-rose-400'],
                        'info'    => ['bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10', 'text' => 'text-[#1E5EFF] dark:text-blue-400'],
                        'warning' => ['bg' => 'bg-[#FFF3DE] dark:bg-amber-500/10', 'text' => 'text-[#B98900] dark:text-amber-400'],
                        'success' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400'],
                    ];
                @endphp

                @foreach([
                    ['title' => 'Administrateurs', 'value' => $stats['total_admins'], 'subtitle' => "Privilèges d'administration", 'icon' => 'shield-check', 'theme' => 'danger'],
                    ['title' => 'Total Comptes', 'value' => $stats['total_users'], 'subtitle' => $stats['active_sessions'] . ' sessions actives', 'icon' => 'users', 'theme' => 'info'],
                    ['title' => 'Comptes Sans Rôle', 'value' => $stats['unassigned_accounts'], 'subtitle' => $stats['unassigned_accounts'] > 0 ? 'Arbitrage requis' : 'Aucune anomalie', 'icon' => 'user-off', 'theme' => $stats['unassigned_accounts'] > 0 ? 'danger' : 'success'],
                    ['title' => 'Taille BDD MySQL', 'value' => $stats['db_size'], 'subtitle' => 'Espace disque alloué', 'icon' => 'circle-stack', 'theme' => 'success'],
                ] as $kpi)
                    @php $theme = $kpiThemes[$kpi['theme']]; @endphp
                    <div class="flex items-center justify-between gap-4 p-7">
                        <div class="min-w-0">
                            <p class="text-[14px] text-[#5A607F] dark:text-slate-400 mb-1">{{ $kpi['title'] }}</p>
                            <p class="text-[20px] font-bold text-[#131523] dark:text-slate-100 leading-[28px] truncate">{{ $kpi['value'] }}</p>
                            <p class="text-[12px] text-[#A1A7C4] dark:text-slate-500 mt-1 truncate">{{ $kpi['subtitle'] }}</p>
                        </div>
                        <div class="w-14 h-14 rounded-full flex items-center justify-center shrink-0 {{ $theme['bg'] }} {{ $theme['text'] }}">
                            <x-icon :name="$kpi['icon']" class="w-6 h-6" />
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Colonne gauche (2/3) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Répartition des rôles Spatie -->
                <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-[#E6E9F4] dark:border-slate-800">
                        <div>
                            <h3 class="flex items-center gap-2 text-[16px] font-bold text-[#131523] dark:text-slate-100">
                                <x-icon name="sitemap" class="w-4 h-4 text-[#1E5EFF]" />
                                <span>Répartition des Rôles Spatie</span>
                            </h3>
                            <p class="text-[13px] text-[#5A607F] dark:text-slate-400 mt-0.5">Distribution des comptes utilisateurs par périmètre</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700 text-[#5A607F] dark:text-slate-300 font-mono text-xs font-bold">
                            Total : {{ $stats['total_users'] }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        @php
                            $roleColors = [
                                'Super Admin'     => 'bg-[#F0142F]',
                                'Administrateur'  => 'bg-[#1E5EFF]',
                                'Admin'           => 'bg-[#1E5EFF]',
                                'Commercial'      => 'bg-[#06A561]',
                                'Technicien'      => 'bg-[#F5A623]',
                                'Client'          => 'bg-[#1FD286]',
                                'Sans rôle'       => 'bg-[#F0142F]',
                            ];
                        @endphp

                        @foreach($usersByRole as $role => $count)
                            @php
                                $percent = $stats['total_users'] > 0 ? round(($count / $stats['total_users']) * 100) : 0;
                                $barColor = $roleColors[$role] ?? 'bg-[#1E5EFF]';
                            @endphp
                            <div>
                                <div class="flex justify-between items-center text-xs mb-1.5 font-semibold">
                                    <span class="text-[#131523] dark:text-slate-200">{{ $role }}</span>
                                    <span class="font-mono text-[#A1A7C4] dark:text-slate-500 text-[11px]">{{ $count }} compte(s) · {{ $percent }}%</span>
                                </div>
                                <div class="w-full bg-[#F5F6FA] dark:bg-slate-800 rounded-full h-2 overflow-hidden border border-[#E6E9F4] dark:border-slate-700">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-500" style="width: {{ max(4, $percent) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Journaux d'Audit Réels -->
                <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#E6E9F4] dark:border-slate-800">
                        <h3 class="flex items-center gap-2 text-[16px] font-bold text-[#131523] dark:text-slate-100">
                            <x-icon name="shield-half" class="w-4 h-4 text-[#F0142F] dark:text-rose-400" />
                            <span>Journal de Sécurité &amp; Gouvernance</span>
                        </h3>
                        <a href="{{ route('superadmin.logs') }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc] dark:hover:text-blue-300 transition">
                            Voir tous les logs →
                        </a>
                    </div>

                    <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($recentLogs as $log)
                            <div class="py-3 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700 text-[#5A607F] dark:text-slate-300 shrink-0">
                                        {{ $log->module }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-[#131523] dark:text-slate-100 truncate">{{ $log->action }}</p>
                                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500">Par {{ $log->user_name }} · IP: {{ $log->ip_address }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono text-[#A1A7C4] dark:text-slate-500 whitespace-nowrap shrink-0">
                                    {{ $log->created_at?->diffForHumans() }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-[#A1A7C4] dark:text-slate-500 py-4 italic text-center">Aucun événement loggé.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Colonne droite (1/3) -->
            <div class="space-y-6">

            <!-- Étape 3.2 — Sécurité de mon compte Super Admin -->
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
                <h3 class="flex items-center justify-between gap-2 text-[16px] font-bold text-[#131523] dark:text-slate-100 pb-4 mb-4 border-b border-[#E6E9F4] dark:border-slate-800">
                    <span class="flex items-center gap-2">
                        <x-icon name="shield-half" class="w-4 h-4 text-[#F0142F] dark:text-rose-400" />
                        <span>Sécurité de Mon Compte</span>
                    </span>
                    <a href="{{ route('superadmin.securite.index') }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc] dark:hover:text-blue-300 transition">Détails →</a>
                </h3>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs text-[#5A607F] dark:text-slate-400">Authentification à deux facteurs</span>
                    @if(auth()->user()->two_factor_confirmed_at)
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#E3FBF0] dark:bg-emerald-500/10 text-[#06A561] dark:text-emerald-400">ACTIVÉE</span>
                            <form method="POST" action="{{ route('two-factor.reset') }}" onsubmit="return confirm('Réinitialiser la 2FA ? Un nouvel enrôlement sera nécessaire.');">
                                @csrf
                                <button type="submit" class="text-[10px] font-semibold text-[#A1A7C4] dark:text-slate-500 hover:text-[#F0142F] dark:hover:text-rose-400 underline">Réinitialiser</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('two-factor.setup') }}" class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#FDE3E6] dark:bg-rose-500/10 text-[#F0142F] dark:text-rose-400 hover:bg-[#F8C4CA] dark:hover:bg-rose-500/20">OBLIGATOIRE — ACTIVER</a>
                    @endif
                </div>
                <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-2">Dernières connexions</p>
                <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                    @forelse($myLoginHistory as $h)
                        <div class="py-2 flex items-center justify-between gap-2">
                            <span class="text-[11px] font-mono text-[#5A607F] dark:text-slate-400">{{ $h->ip_address }}</span>
                            <span class="text-[10px] text-[#A1A7C4] dark:text-slate-500">{{ $h->logged_in_at?->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 italic py-2">Aucune connexion enregistrée.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#E6E9F4] dark:border-slate-800">
                        <h3 class="flex items-center gap-2 text-[16px] font-bold text-[#131523] dark:text-slate-100">
                            <x-icon name="user-plus" class="w-4 h-4 text-[#1E5EFF]" />
                            <span>Comptes Récents</span>
                        </h3>
                        <a href="{{ route('superadmin.users') }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc] dark:hover:text-blue-300 transition">Tous →</a>
                    </div>

                    <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @foreach($recentUsers as $u)
                            @php
                                $rname = $u->roles->pluck('name')->first() ?? 'User';
                            @endphp
                            <div class="py-3 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-9 h-9 rounded-[4px] bg-[#ECF2FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400 font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-[#131523] dark:text-slate-100 truncate">{{ $u->name }}</p>
                                        <p class="text-[11px] text-[#A1A7C4] dark:text-slate-500 truncate">{{ $u->email }}</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-[4px] bg-[#F5F6FA] dark:bg-slate-800 border border-[#E6E9F4] dark:border-slate-700 text-[#5A607F] dark:text-slate-300 shrink-0">
                                    {{ $rname }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-[#E6E9F4] dark:border-slate-800">
                    <a href="{{ route('superadmin.admins') }}" class="inline-flex items-center justify-center gap-2 rounded-[4px] bg-white dark:bg-slate-800 border border-[#D7DBEC] dark:border-slate-700 text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 hover:text-[#131523] dark:hover:text-white w-full text-xs font-semibold py-2.5 transition">
                        <x-icon name="plus" class="w-4 h-4" /> Créer un Administrateur
                    </a>
                </div>
            </div>
            </div>
        </div>

    </div>
</x-super-admin-layout>
