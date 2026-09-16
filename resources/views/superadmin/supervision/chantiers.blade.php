<x-super-admin-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Chantiers — Vue Globale</h1>
            <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Supervision transverse tous clients confondus — lecture seule, la gestion reste sur les espaces Admin/Commercial.</p>
        </div>

        <!-- KPI Stats Bar -->
        <x-supervision-kpi-header :items="[
            ['title' => 'Chantiers Actifs', 'value' => $kpis['chantiers_actifs'], 'icon' => 'city', 'theme' => ['bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10', 'text' => 'text-[#1E5EFF] dark:text-blue-400']],
            ['title' => 'Total Emplacements', 'value' => $kpis['total_emplacements'], 'icon' => 'qrcode', 'theme' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400']],
            ['title' => 'Total Interventions', 'value' => $kpis['total_interventions'], 'icon' => 'gauge', 'theme' => ['bg' => 'bg-[#FFF3DE] dark:bg-amber-500/10', 'text' => 'text-[#B98900] dark:text-amber-400']],
            ['title' => 'Chantiers Sans Activité', 'value' => $kpis['sans_activite'], 'icon' => 'warning', 'theme' => ['bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10', 'text' => 'text-[#F0142F] dark:text-rose-400']],
        ]" />

        <!-- Filtres -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-4 flex flex-col md:flex-row items-center gap-3">
            <form method="GET" action="{{ route('superadmin.supervision.chantiers') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#A1A7C4]" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un chantier…"
                           class="w-full pl-10 pr-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-slate-100 placeholder-[#A1A7C4] focus:outline-none focus:border-[#1E5EFF]">
                </div>
                <!-- Filtre client : auto-soumission au changement, pas de bouton redondant -->
                <select name="client_id" onchange="this.form.submit()" class="w-full sm:w-56 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
                <label class="inline-flex items-center gap-2 px-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] cursor-pointer shrink-0">
                    <input type="checkbox" name="sans_activite" value="1" onchange="this.form.submit()" {{ request()->boolean('sans_activite') ? 'checked' : '' }} class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#F0142F] focus:ring-[#F0142F]">
                    <span class="text-xs font-semibold text-[#F0142F] dark:text-rose-400">Chantiers sans activité uniquement</span>
                </label>
                <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 shrink-0">Rechercher</button>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            @php
                                $sortLink = fn (string $col) => request()->fullUrlWithQuery(['tri' => $col, 'direction' => ($tri ?? null) === $col && ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc']);
                                $sortIcon = fn (string $col) => ($tri ?? null) === $col ? (($direction ?? 'asc') === 'asc' ? '↑' : '↓') : '';
                            @endphp
                            <th class="px-6 py-4"><a href="{{ $sortLink('nom') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Chantier {{ $sortIcon('nom') }}</a></th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('ville') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Ville {{ $sortIcon('ville') }}</a></th>
                            <th class="px-6 py-4">Emplacements</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('interventions_count') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Interventions {{ $sortIcon('interventions_count') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('is_active') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Statut {{ $sortIcon('is_active') }}</a></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($chantiers as $chantier)
                            @php $dormant = $chantier->emplacements_count > 0 && $chantier->interventions_count === 0; @endphp
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition {{ $dormant ? 'bg-[#FDE3E6]/40 dark:bg-rose-500/5' : '' }}">
                                <td class="px-6 py-4">
                                    <a href="{{ route('superadmin.supervision.chantiers.show', $chantier) }}" class="block font-bold text-xs text-[#1E5EFF] hover:underline">{{ $chantier->nom }}</a>
                                    <span class="text-[11px] text-[#A1A7C4] dark:text-slate-500 font-mono">{{ $chantier->code_chantier }}</span>
                                    @if($dormant)
                                        <x-supervision-alert-badge label="Sans activité" />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300">{{ $chantier->client?->nom ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    @if($chantier->client)
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-[4px] {{ $chantier->client->type_client === 'Entreprise' ? 'bg-[#EAF0FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400' : 'bg-[#F5F6FA] dark:bg-slate-800 text-[#5A607F] dark:text-slate-400' }}">
                                            {{ $chantier->client->type_client ?? '—' }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-400">{{ $chantier->ville ?: '—' }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $chantier->emplacements_count }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $chantier->interventions_count }}</td>
                                <td class="px-6 py-4">
                                    @if($chantier->is_active)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun chantier trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $chantiers->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
