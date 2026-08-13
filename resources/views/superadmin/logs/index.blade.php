<x-super-admin-layout>
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight font-heading">Journaux d'Audit Réels (Table DB <code>audit_logs</code>)</h1>
                <p class="text-sm text-slate-400 mt-1">Historique des actions de sécurité, modifications de paramètres et opérations administrateurs enregistrées en base.</p>
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="glass-card p-4 rounded-2xl flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.logs') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Filtrer par action, utilisateur ou module..." class="w-full pl-10 pr-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                    <svg class="h-4 w-4 text-slate-500 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <select name="severity" class="w-full sm:w-48 px-4 py-2.5 bg-slate-950/80 border border-slate-800 rounded-xl text-xs text-white focus:outline-none focus:border-rose-500">
                    <option value="">Toutes les sévérités</option>
                    <option value="INFO" {{ request('severity') === 'INFO' ? 'selected' : '' }}>INFO</option>
                    <option value="SUCCESS" {{ request('severity') === 'SUCCESS' ? 'selected' : '' }}>SUCCESS</option>
                    <option value="WARNING" {{ request('severity') === 'WARNING' ? 'selected' : '' }}>WARNING</option>
                    <option value="CRITICAL" {{ request('severity') === 'CRITICAL' ? 'selected' : '' }}>CRITICAL</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-slate-200 text-xs font-semibold rounded-xl border border-slate-800">Filtrer</button>
            </form>

            <span class="text-xs text-slate-400 font-mono">{{ $logs->total() }} Événement(s) réel(s) en base DB</span>
        </div>

        <!-- Logs Table -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-950/90 text-slate-400 text-[11px] font-bold uppercase tracking-wider font-heading border-b border-slate-800/80">
                        <tr>
                            <th class="px-6 py-4">ID Log</th>
                            <th class="px-6 py-4">Sévérité</th>
                            <th class="px-6 py-4">Date & Heure</th>
                            <th class="px-6 py-4">Utilisateur Initiateur</th>
                            <th class="px-6 py-4">Action Réelle Journalisée</th>
                            <th class="px-6 py-4">Module</th>
                            <th class="px-6 py-4 text-right">Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-900/60 transition">
                                <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">#LOG-{{ $log->id }}</td>
                                <td class="px-6 py-4">
                                    @php
                                        $badge = match($log->severity) {
                                            'CRITICAL' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                            'WARNING' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'SUCCESS' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            default => 'bg-blue-500/10 text-blue-400 border-blue-500/20'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-mono font-bold rounded-lg border {{ $badge }}">
                                        {{ $log->severity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-400">
                                    {{ $log->created_at?->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 font-semibold text-white text-xs">{{ $log->user_name }}</td>
                                <td class="px-6 py-4 text-slate-300 text-xs">{{ $log->action }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 text-[10px] font-mono bg-slate-900 text-slate-400 border border-slate-800 rounded">
                                        {{ $log->module }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-400 text-right">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500 italic">Aucun journal d'audit enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-slate-950 border-t border-slate-800/80">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
