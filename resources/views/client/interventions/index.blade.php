<x-client-layout>
    <x-slot name="header">Mes Interventions</x-slot>

    <div class="space-y-6">
        <!-- Filtres par catégorie (vocabulaire client — les statuts internes du workflow
             ne sont jamais exposés tels quels ici, voir InterventionService::GROUPES_STATUT_CLIENT) -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                @foreach(\App\Services\InterventionService::LIBELLES_GROUPE_CLIENT as $value => $label)
                    <a href="{{ route('client.interventions.index', array_filter(['statut' => $value, 'chantier_id' => $chantierFiltre, 'q' => $recherche, 'avec_rapport' => $avecRapport ? 1 : null])) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $statutFiltre === $value ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 cursor-pointer">
                <input type="checkbox" onchange="window.location='{{ route('client.interventions.index', array_filter(['statut' => $statutFiltre, 'chantier_id' => $chantierFiltre, 'q' => $recherche])) }}' + (this.checked ? '&avec_rapport=1' : '')"
                       {{ $avecRapport ? 'checked' : '' }}
                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/40">
                Avec rapport disponible uniquement
            </label>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-6 pb-4">
                <div>
                    <h1 class="text-base font-bold text-slate-900">Interventions</h1>
                    <p class="text-xs text-slate-400 mt-1">Consultation et suivi de vos interventions, rapports compris</p>
                </div>

                <form method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="statut" value="{{ $statutFiltre }}">
                    @if($avecRapport)<input type="hidden" name="avec_rapport" value="1">@endif
                    <select name="chantier_id" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <option value="">Tous les chantiers</option>
                        @foreach($chantiersDuClient as $c)
                            <option value="{{ $c->id }}" {{ (string) $chantierFiltre === (string) $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                        @endforeach
                    </select>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="q" value="{{ $recherche }}" placeholder="Code, chantier…"
                               class="pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition w-48">
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Code</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Chantier</th>
                            <th class="py-3 px-4">Priorité</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 px-4">Technicien</th>
                            <th class="py-3 pr-6 pl-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($interventions as $interv)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-slate-900">{{ $interv->code_intervention }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $interv->typeIntervention?->nom ?? '—' }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $interv->chantier?->nom ?? '—' }}</div>
                                    @if($interv->chantier?->ville)<div class="text-[11px] text-slate-400">{{ $interv->chantier->ville }}</div>@endif
                                </td>
                                <td class="py-3.5 px-4"><x-soft-badge :status="$interv->priorite" /></td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $interv->date_prevue_debut ? $interv->date_prevue_debut->format('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <x-soft-badge :status="$interv->statut" />
                                        @if($interv->rapport)
                                            <a href="{{ route('client.rapports.show', $interv->rapport) }}" title="Rapport disponible pour cette intervention" class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
                                                <i class="fas fa-file-pdf text-[10px]"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $interv->technicien?->name ?? 'Non assigné' }}</td>
                                <td class="py-3.5 pr-6 pl-4 text-right">
                                    <a href="{{ route('client.interventions.show', $interv) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                        Consulter <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 italic">Aucune intervention trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($interventions, 'hasPages') && $interventions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $interventions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
