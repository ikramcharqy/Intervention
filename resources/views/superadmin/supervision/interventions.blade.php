<x-super-admin-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Interventions — Vue Globale</h1>
            <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Suivi transverse du workflow (Planifiée → Acceptée → En cours → Formulaire rempli → Terminée), lecture seule.</p>
        </div>

        <!-- KPI Stats Bar -->
        <x-supervision-kpi-header :items="[
            ['title' => 'Total Interventions', 'value' => $kpis['total'], 'icon' => 'gauge', 'theme' => ['bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10', 'text' => 'text-[#1E5EFF] dark:text-blue-400']],
            ['title' => 'En Cours', 'value' => $kpis['par_statut']['En cours'] ?? 0, 'icon' => 'bolt', 'theme' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400']],
            ['title' => 'Terminées', 'value' => $kpis['par_statut']['Terminee'] ?? 0, 'icon' => 'circle-check', 'theme' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400']],
            ['title' => 'Interventions Bloquées', 'value' => $kpis['bloquees'], 'icon' => 'warning', 'theme' => ['bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10', 'text' => 'text-[#F0142F] dark:text-rose-400']],
        ]" />

        <!-- Filtres : selects auto-soumis au changement, pas de bouton "Filtrer" redondant -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-4">
            <form method="GET" action="{{ route('superadmin.supervision.interventions') }}" class="w-full flex flex-col lg:flex-row items-center gap-3">
                <select name="statut" onchange="this.form.submit()" class="w-full lg:w-48 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $s)
                        <option value="{{ $s }}" {{ request('statut') === $s ? 'selected' : '' }}>{{ \App\Models\Intervention::statutLabel($s) }}</option>
                    @endforeach
                </select>
                <select name="priorite" onchange="this.form.submit()" class="w-full lg:w-40 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Toutes priorités</option>
                    @foreach($priorites as $p)
                        <option value="{{ $p }}" {{ request('priorite') === $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
                <select name="client_id" onchange="this.form.submit()" class="w-full lg:w-48 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Tous les clients</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
                <select name="technicien_id" onchange="this.form.submit()" class="w-full lg:w-48 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Tous les techniciens</option>
                    @foreach($techniciens as $t)
                        <option value="{{ $t->id }}" {{ request('technicien_id') == $t->id ? 'selected' : '' }}>{{ $t->prenom }} {{ $t->name }}</option>
                    @endforeach
                </select>
                <label class="inline-flex items-center gap-2 px-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] cursor-pointer shrink-0 w-full lg:w-auto">
                    <input type="checkbox" name="bloquees" value="1" onchange="this.form.submit()" {{ request()->boolean('bloquees') ? 'checked' : '' }} class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#F0142F] focus:ring-[#F0142F]">
                    <span class="text-xs font-semibold text-[#F0142F] dark:text-rose-400 whitespace-nowrap">Interventions bloquées</span>
                </label>
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
                            <th class="px-6 py-4">Intervention</th>
                            <th class="px-6 py-4">Client / Chantier</th>
                            <th class="px-6 py-4">Technicien</th>
                            <th class="px-6 py-4">Type</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('priorite') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Priorité {{ $sortIcon('priorite') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('statut') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Statut {{ $sortIcon('statut') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('date_prevue_debut') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Prévue le {{ $sortIcon('date_prevue_debut') }}</a></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($interventions as $i)
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition {{ $i->estBloquee() ? 'bg-[#FDE3E6]/40 dark:bg-rose-500/5' : '' }}">
                                <td class="px-6 py-4">
                                    <a href="{{ route('superadmin.supervision.interventions.show', $i) }}" class="font-mono text-xs text-[#1E5EFF] font-bold hover:underline">{{ $i->code_intervention }}</a>
                                    @if($i->estBloquee())
                                        <span class="flex items-center gap-1 mt-1 text-[9px] font-bold text-[#F0142F] dark:text-rose-400">
                                            <x-icon name="warning" class="w-3 h-3" /> Bloquée depuis {{ $i->updated_at?->diffForHumans(null, true) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300">
                                    {{ $i->chantier?->client?->nom ?? '—' }}
                                    <span class="block text-[11px] text-[#A1A7C4] dark:text-slate-500">{{ $i->chantier?->nom }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300">{{ $i->technicien ? $i->technicien->prenom . ' ' . $i->technicien->name : '—' }}</td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-400">{{ $i->typeIntervention?->nom ?? '—' }}</td>
                                <td class="px-6 py-4"><x-soft-badge :status="$i->priorite" /></td>
                                <td class="px-6 py-4"><x-soft-badge :status="$i->statut" /></td>
                                <td class="px-6 py-4 text-[11px] font-mono text-[#A1A7C4] dark:text-slate-500">{{ $i->date_prevue_debut?->format('d/m/Y H:i') ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucune intervention trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $interventions->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
