<x-super-admin-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="flex items-center gap-2 text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">
                    <x-icon name="shield-half" class="w-5 h-5 text-[#F0142F] dark:text-rose-400" />
                    <span>Journaux d'Audit</span>
                </h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Historique des actions de sécurité, modifications de paramètres et opérations administrateurs.</p>
            </div>
        </div>

        @php
            $severityThemes = [
                'CRITICAL' => ['label' => 'Critique',       'icon' => 'circle-exclamation', 'bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10',  'text' => 'text-[#F0142F] dark:text-rose-400'],
                'WARNING'  => ['label' => 'Avertissement',  'icon' => 'warning',             'bg' => 'bg-[#FFF3DE] dark:bg-amber-500/10', 'text' => 'text-[#B98900] dark:text-amber-400'],
                'SUCCESS'  => ['label' => 'Succès',          'icon' => 'circle-check',        'bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10','text' => 'text-[#06A561] dark:text-emerald-400'],
                'INFO'     => ['label' => 'Info',            'icon' => 'circle-exclamation',  'bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10',   'text' => 'text-[#1E5EFF] dark:text-blue-400'],
            ];
        @endphp

        <!-- Synthèse par sévérité -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 lg:divide-x divide-[#E6E9F4] dark:divide-slate-800">
                @foreach($severityThemes as $key => $theme)
                    <a href="{{ route('superadmin.logs', array_filter(['severity' => $key, 'search' => request('search')])) }}"
                       class="flex items-center justify-between gap-4 p-6 transition {{ request('severity') === $key ? 'bg-[#F5F6FA] dark:bg-slate-800/60' : 'hover:bg-[#F5F6FA] dark:hover:bg-slate-800/40' }}">
                        <div class="min-w-0">
                            <p class="text-[13px] text-[#5A607F] dark:text-slate-400 mb-1">{{ $theme['label'] }}</p>
                            <p class="text-[20px] font-bold text-[#131523] dark:text-slate-100 leading-[28px]">{{ $severityCounts[$key] ?? 0 }}</p>
                        </div>
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 {{ $theme['bg'] }} {{ $theme['text'] }}">
                            <x-icon :name="$theme['icon']" class="w-5 h-5" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Filters Bar -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <form method="GET" action="{{ route('superadmin.logs') }}" class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Filtrer par action, utilisateur ou module..." class="w-full pl-10 pr-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-white placeholder-[#A1A7C4] dark:placeholder-slate-500 focus:outline-none focus:border-[#1E5EFF]">
                    <svg class="h-4 w-4 text-[#A1A7C4] dark:text-slate-500 absolute left-3.5 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>

                <select name="severity" onchange="this.form.submit()" class="w-full sm:w-48 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-white focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Toutes les sévérités</option>
                    <option value="INFO" {{ request('severity') === 'INFO' ? 'selected' : '' }}>Info</option>
                    <option value="SUCCESS" {{ request('severity') === 'SUCCESS' ? 'selected' : '' }}>Succès</option>
                    <option value="WARNING" {{ request('severity') === 'WARNING' ? 'selected' : '' }}>Avertissement</option>
                    <option value="CRITICAL" {{ request('severity') === 'CRITICAL' ? 'selected' : '' }}>Critique</option>
                </select>

                <button type="submit" class="w-full sm:w-auto px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px] border border-[#D7DBEC] dark:border-slate-700">Rechercher</button>

                @if(request('search') || request('severity'))
                    <a href="{{ route('superadmin.logs') }}" class="w-full sm:w-auto text-center px-5 py-2.5 text-[#5A607F] dark:text-slate-400 hover:text-[#F0142F] dark:hover:text-rose-400 text-xs font-semibold transition">Réinitialiser</a>
                @endif
            </form>

            <span class="text-xs text-[#A1A7C4] dark:text-slate-400 font-mono whitespace-nowrap shrink-0">{{ $logs->total() }} événement(s) enregistré(s)</span>
        </div>

        <!-- Logs Table -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none border border-[#E6E9F4] dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Sévérité</th>
                            <th class="px-6 py-4">Date &amp; Heure</th>
                            <th class="px-6 py-4">Utilisateur</th>
                            <th class="px-6 py-4">Action</th>
                            <th class="px-6 py-4">Module</th>
                            <th class="px-6 py-4 text-right">Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($logs as $log)
                            @php $theme = $severityThemes[$log->severity] ?? $severityThemes['INFO']; @endphp
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition">
                                <td class="px-6 py-4 font-mono text-xs text-[#A1A7C4] dark:text-slate-500 font-semibold">#LOG-{{ $log->id }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-lg border {{ $theme['bg'] }} {{ $theme['text'] }} border-transparent">
                                        <x-icon :name="$theme['icon']" class="w-3 h-3" />
                                        {{ $theme['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-400">
                                    {{ $log->created_at?->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-[#131523] dark:text-white text-xs">{{ $log->user_name }}</td>
                                <td class="px-6 py-4 text-[#5A607F] dark:text-slate-300 text-xs">{{ $log->action }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 text-[10px] font-mono bg-[#F5F6FA] dark:bg-slate-900 text-[#5A607F] dark:text-slate-400 border border-[#E6E9F4] dark:border-slate-800 rounded">
                                        {{ $log->module }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-400 text-right">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun journal d'audit enregistré.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
