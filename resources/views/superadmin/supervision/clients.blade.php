<x-super-admin-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Comptes Clients — Activité Métier</h1>
            <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Distinct de "Tous les Comptes" : centré sur l'activité métier (chantiers, interventions, devis, factures) plutôt que la gestion de compte.</p>
        </div>

        <!-- KPI Stats Bar -->
        <x-supervision-kpi-header :items="[
            ['title' => 'Total Clients', 'value' => $kpis['total'], 'icon' => 'city', 'theme' => ['bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10', 'text' => 'text-[#1E5EFF] dark:text-blue-400']],
            ['title' => 'CA Généré', 'value' => format_montant($kpis['ca_genere']), 'icon' => 'gauge', 'theme' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400']],
            ['title' => 'Montant en Attente', 'value' => format_montant($kpis['montant_en_attente']), 'icon' => 'warning', 'theme' => ['bg' => 'bg-[#FFF3DE] dark:bg-amber-500/10', 'text' => 'text-[#B98900] dark:text-amber-400']],
            ['title' => 'Clients Sans Activité', 'value' => $kpis['sans_activite'], 'icon' => 'warning', 'theme' => ['bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10', 'text' => 'text-[#F0142F] dark:text-rose-400']],
        ]" />

        <!-- Filtres : la case "sans activité" auto-soumet au changement ; le champ recherche
             garde un bouton dédié ("Rechercher"), même pattern que les 3 autres vues. -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-4">
            <form method="GET" action="{{ route('superadmin.supervision.clients') }}" class="w-full flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#A1A7C4]" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un client…"
                           class="w-full pl-10 pr-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-slate-100 placeholder-[#A1A7C4] focus:outline-none focus:border-[#1E5EFF]">
                </div>
                <label class="inline-flex items-center gap-2 px-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] cursor-pointer shrink-0 w-full sm:w-auto">
                    <input type="checkbox" name="sans_activite" value="1" onchange="this.form.submit()" {{ request()->boolean('sans_activite') ? 'checked' : '' }} class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#F0142F] focus:ring-[#F0142F]">
                    <span class="text-xs font-semibold text-[#F0142F] dark:text-rose-400 whitespace-nowrap">Sans activité uniquement</span>
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
                            <th class="px-6 py-4"><a href="{{ $sortLink('nom') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Client {{ $sortIcon('nom') }}</a></th>
                            <th class="px-6 py-4">Commercial</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('chantiers_count') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Chantiers {{ $sortIcon('chantiers_count') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('interventions_count') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Interventions {{ $sortIcon('interventions_count') }}</a></th>
                            <th class="px-6 py-4">Devis</th>
                            <th class="px-6 py-4">Factures</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('ca_genere') }}" class="hover:text-[#131523] dark:hover:text-slate-200">CA Généré {{ $sortIcon('ca_genere') }}</a></th>
                            <th class="px-6 py-4">En Attente</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($clients as $client)
                            @php $sansActivite = $client->chantiers_count === 0; @endphp
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition">
                                <td class="px-6 py-4">
                                    <a href="{{ route('superadmin.supervision.clients.show', $client) }}" class="block font-bold text-xs text-[#1E5EFF] hover:underline">{{ $client->nom }}</a>
                                    <span class="text-[11px] text-[#A1A7C4] dark:text-slate-500 font-mono">{{ $client->code_client }}</span>
                                    @if($sansActivite)
                                        <x-supervision-alert-badge label="Sans activité" />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300">{{ $client->commercial?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $client->chantiers_count }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $client->interventions_count }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $client->devis_count }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $client->factures_count }}</td>
                                <td class="px-6 py-4 text-xs font-semibold text-[#06A561] dark:text-emerald-400 whitespace-nowrap">{{ format_montant($client->ca_genere) }}</td>
                                <td class="px-6 py-4 text-xs font-semibold {{ $client->montant_en_attente > 0 ? 'text-[#F0142F] dark:text-rose-400' : 'text-[#5A607F] dark:text-slate-400' }} whitespace-nowrap">{{ format_montant($client->montant_en_attente) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun client trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
