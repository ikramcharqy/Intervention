<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Types d'Intervention & Formulaires</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Protocoles disponibles et configuration de leur formulaire de clôture terrain.</p>
            </div>
            <a href="{{ route('types-intervention.create') }}" class="inline-flex items-center gap-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold px-4 py-2.5 transition shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] shrink-0">
                <x-icon name="plus" class="w-4 h-4" />
                <span>Nouveau Type</span>
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-4">
            <form method="GET" action="{{ route('types-intervention.index') }}" class="w-full flex flex-col md:flex-row items-center gap-3">
                <div class="relative w-full md:w-80">
                    <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#A1A7C4]" />
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par nom…"
                           class="w-full pl-10 pr-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-slate-100 placeholder-[#A1A7C4] focus:outline-none focus:border-[#1E5EFF]">
                </div>

                <div class="inline-flex rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 overflow-hidden shrink-0">
                    <a href="{{ request()->fullUrlWithQuery(['filtre' => 'actifs']) }}" class="px-4 py-2.5 text-xs font-semibold transition {{ ($filtre ?? 'actifs') === 'actifs' ? 'bg-[#1E5EFF] text-white' : 'bg-white dark:bg-slate-800 text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-700' }}">Actifs</a>
                    <a href="{{ request()->fullUrlWithQuery(['filtre' => 'tous']) }}" class="px-4 py-2.5 text-xs font-semibold transition {{ ($filtre ?? 'actifs') === 'tous' ? 'bg-[#1E5EFF] text-white' : 'bg-white dark:bg-slate-800 text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-700' }}">Tous</a>
                </div>

                <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 shrink-0">Filtrer</button>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            @php
                                $sortLink = fn (string $col) => request()->fullUrlWithQuery(['tri' => $col, 'direction' => ($tri ?? 'nom') === $col && ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc']);
                                $sortIcon = fn (string $col) => ($tri ?? 'nom') === $col ? (($direction ?? 'asc') === 'asc' ? '↑' : '↓') : '';
                            @endphp
                            <th class="px-6 py-4"><a href="{{ $sortLink('nom') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Nom {{ $sortIcon('nom') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('duree_estimee') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Durée estimée {{ $sortIcon('duree_estimee') }}</a></th>
                            <th class="px-6 py-4">Formulaire</th>
                            <th class="px-6 py-4">Interventions</th>
                            <th class="px-6 py-4">Actif</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse ($typesIntervention as $type)
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition {{ !$type->is_active ? 'opacity-60' : '' }}">
                                <td class="px-6 py-4">
                                    <span class="block font-bold text-xs text-[#131523] dark:text-slate-100">{{ $type->nom }}</span>
                                    @if($type->description)
                                        <span class="block text-[11px] text-[#A1A7C4] dark:text-slate-500 truncate max-w-xs">{{ $type->description }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300">{{ $type->duree_estimee ? $type->duree_estimee . ' min' : '—' }}</td>
                                <td class="px-6 py-4">
                                    @if(!$type->formulaire)
                                        <a href="{{ route('formulaires.create', ['type_intervention_id' => $type->id]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full hover:bg-[#F8C4CA] dark:hover:bg-rose-500/20 transition">
                                            <x-icon name="warning" class="w-3 h-3" /> Formulaire non configuré
                                        </a>
                                    @elseif($type->formulaire->questions_count === 0)
                                        <a href="{{ route('formulaires.show', $type->formulaire) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-[#B98900] dark:text-amber-400 bg-[#FFF3DE] dark:bg-amber-500/10 rounded-full hover:bg-[#FFE7B8] dark:hover:bg-amber-500/20 transition">
                                            <x-icon name="warning" class="w-3 h-3" /> Aucune question
                                        </a>
                                    @else
                                        <a href="{{ route('formulaires.show', $type->formulaire) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full hover:bg-[#C4F8E2] dark:hover:bg-emerald-500/20 transition">
                                            {{ $type->formulaire->questions_count }} question(s)
                                        </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-[#131523] dark:text-slate-200">{{ $type->interventions_count }}</td>
                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('types-intervention.toggleActive', $type) }}"
                                          onsubmit="{{ $type->is_active && $type->interventions_en_cours_count > 0 ? "return confirm('{$type->interventions_en_cours_count} intervention(s) en cours utilisent ce type — la désactivation n\'affectera pas les interventions déjà planifiées mais empêchera d\'en créer de nouvelles. Continuer ?');" : '' }}">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-[10px] font-bold rounded-full transition {{ $type->is_active ? 'text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 hover:bg-[#C4F8E2]' : 'text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 hover:bg-[#F8C4CA]' }}">
                                            {{ $type->is_active ? 'ACTIF' : 'INACTIF' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-3">
                                        <a href="{{ route('types-intervention.show', $type) }}" class="text-xs font-semibold text-[#1E5EFF] hover:underline">Voir</a>
                                        <a href="{{ route('types-intervention.edit', $type) }}" class="text-xs font-semibold text-[#5A607F] dark:text-slate-300 hover:text-[#131523] dark:hover:text-white">Modifier</a>
                                        @if($type->interventions_count > 0)
                                            <span class="text-xs text-[#A1A7C4] dark:text-slate-600 cursor-not-allowed" title="Ce type ne peut pas être supprimé, {{ $type->interventions_count }} intervention(s) y sont rattachées.">Supprimer</span>
                                        @else
                                            <form method="POST" action="{{ route('types-intervention.destroy', $type) }}" onsubmit="return confirm('Supprimer définitivement ce type d\'intervention ?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-[#F0142F] dark:text-rose-400 hover:underline">Supprimer</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun type d'intervention enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($typesIntervention->hasPages())
                <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                    {{ $typesIntervention->links() }}
                </div>
            @endif
        </div>
    </div>
</x-super-admin-layout>
