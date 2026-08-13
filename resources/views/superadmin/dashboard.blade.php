<x-super-admin-layout>
    <div class="space-y-8">
        <!-- Header Page Title & Actions Metronic style -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#181C32] tracking-tight font-heading">Supervision Infrastructure & APIs</h2>
                <p class="text-xs text-[#A1A5B7] mt-1">Console globale Metronic v8.2 — Données certifiées de la BDD MySQL.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('superadmin.settings') }}" class="px-4 py-2.5 bg-white border border-[#EFF2F5] hover:bg-[#F9F9FB] text-[#3F4254] text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-2">
                    <svg class="h-4 w-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                    Config Google Maps & Caméras
                </a>
                <form method="POST" action="{{ route('superadmin.backups.create') }}" class="inline-block">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold rounded-lg shadow-sm transition">
                        + Générer Dump SQL
                    </button>
                </form>
            </div>
        </div>

        <!-- Metronic Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Administrateurs</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-2 font-heading">{{ $stats['total_admins'] }}</div>
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-2 inline-block">Privilèges Métier</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
            </div>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Total Comptes</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-2 font-heading">{{ $stats['total_users'] }}</div>
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded mt-2 inline-block">{{ $stats['active_sessions'] }} Comptes Actifs</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
            </div>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Interventions & Chantiers</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-2 font-heading">{{ $stats['total_interventions'] }} Int.</div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded mt-2 inline-block">{{ $stats['total_chantiers'] }} Chantiers</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
            </div>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Taille Base MySQL</span>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-heading">{{ $stats['db_size'] }}</div>
                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded mt-2 inline-block">information_schema</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 2.21 8 4" /></svg>
                </div>
            </div>
        </div>

        <!-- Middle Metronic Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Role Breakdown -->
                <div class="metronic-card p-6">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#EFF2F5]">
                        <h3 class="text-base font-bold text-[#181C32] font-heading">Répartition des Rôles Spatie</h3>
                        <span class="px-3 py-1 bg-[#F5F8FA] text-[#5E6278] text-xs font-bold rounded-lg font-mono">Total: {{ $stats['total_users'] }}</span>
                    </div>

                    <div class="space-y-4">
                        @foreach($usersByRole as $role => $count)
                            @php
                                $percent = $stats['total_users'] > 0 ? round(($count / $stats['total_users']) * 100) : 0;
                                $color = match($role) {
                                    'Super Admin' => 'bg-rose-500',
                                    'Administrateur' => 'bg-blue-500',
                                    'Commercial' => 'bg-emerald-500',
                                    'Technicien' => 'bg-amber-500',
                                    'Client' => 'bg-purple-500',
                                    default => 'bg-slate-500'
                                };
                            @endphp
                            <div>
                                <div class="flex justify-between text-xs font-semibold text-[#3F4254] mb-1">
                                    <span>Espace {{ $role }}</span>
                                    <span class="font-mono text-[#A1A5B7]">{{ $count }} compte(s) ({{ $percent }}%)</span>
                                </div>
                                <div class="w-full bg-[#F5F8FA] h-2.5 rounded-full overflow-hidden">
                                    <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ max(4, $percent) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Audit Log Stream -->
                <div class="metronic-card p-6">
                    <div class="flex items-center justify-between mb-4 pb-4 border-b border-[#EFF2F5]">
                        <h3 class="text-base font-bold text-[#181C32] font-heading">Journaux d'Audit Réels</h3>
                        <a href="{{ route('superadmin.logs') }}" class="text-xs font-bold text-rose-600 hover:underline">Voir les logs →</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentLogs as $log)
                            <div class="p-3.5 rounded-lg bg-[#F9F9FB] border border-[#EFF2F5] flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-white text-[#5E6278] border border-[#EFF2F5]">
                                        {{ $log->module }}
                                    </span>
                                    <div>
                                        <span class="text-xs font-bold text-[#181C32] block">{{ $log->action }}</span>
                                        <span class="text-[11px] text-[#A1A5B7]">Par {{ $log->user_name }} • IP: {{ $log->ip_address }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-mono text-[#A1A5B7]">{{ $log->created_at?->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-[#A1A5B7] italic">Aucun événement loggé.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="metronic-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#EFF2F5]">
                        <h3 class="text-base font-bold text-[#181C32] font-heading">Comptes Récent</h3>
                        <a href="{{ route('superadmin.users') }}" class="text-xs font-bold text-rose-600 hover:underline">Tous →</a>
                    </div>

                    <div class="divide-y divide-[#EFF2F5]">
                        @foreach($recentUsers as $u)
                            <div class="py-3 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-lg bg-[#F5F8FA] text-[#181C32] font-bold text-xs flex items-center justify-center border border-[#EFF2F5]">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-[#181C32] block leading-tight">{{ $u->name }}</span>
                                        <span class="text-[11px] text-[#A1A5B7]">{{ $u->email }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-[#F5F8FA] text-[#5E6278]">
                                    {{ $u->roles->pluck('name')->first() ?? 'User' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-[#EFF2F5]">
                    <a href="{{ route('superadmin.admins') }}" class="w-full py-2.5 bg-[#F5F8FA] hover:bg-[#EEF0F8] text-[#181C32] font-bold text-xs rounded-lg transition text-center block">
                        + Ajouter un Administrateur
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-super-admin-layout>
