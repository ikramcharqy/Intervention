<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('types-intervention.index') }}" class="w-9 h-9 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 flex items-center justify-center text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800 transition shrink-0">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">{{ $typeIntervention->nom }}</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Détail du type d'intervention</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Nom</p>
                    <p class="text-sm font-semibold text-[#131523] dark:text-slate-100">{{ $typeIntervention->nom }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Durée estimée</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $typeIntervention->duree_estimee ? $typeIntervention->duree_estimee . ' min' : '—' }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Description</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $typeIntervention->description ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Statut</p>
                    @if($typeIntervention->is_active)
                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                    @else
                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                    @endif
                </div>
            </div>

            <div class="pt-4 border-t border-[#E6E9F4] dark:border-slate-800">
                <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-2">Formulaire associé</p>
                @if($typeIntervention->formulaire)
                    <a href="{{ route('formulaires.show', $typeIntervention->formulaire) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#1E5EFF] bg-[#EAF0FF] dark:bg-blue-500/10 rounded-[4px] hover:bg-[#D9E4FF] dark:hover:bg-blue-500/20 transition">
                        {{ $typeIntervention->formulaire->nom }} ({{ $typeIntervention->formulaire->questions->count() }} question(s)) →
                    </a>
                @else
                    <a href="{{ route('formulaires.create', ['type_intervention_id' => $typeIntervention->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-[#F0142F] bg-[#FDE3E6] dark:bg-rose-500/10 rounded-[4px] hover:bg-[#F8C4CA] dark:hover:bg-rose-500/20 transition">
                        <x-icon name="warning" class="w-3.5 h-3.5" /> Formulaire non configuré — Configurer maintenant
                    </a>
                @endif
            </div>

            <div class="flex gap-3 pt-4 border-t border-[#E6E9F4] dark:border-slate-800">
                <a href="{{ route('types-intervention.edit', $typeIntervention) }}" class="inline-flex items-center gap-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold px-4 py-2.5 transition">Modifier</a>
                <a href="{{ route('types-intervention.index') }}" class="inline-flex items-center px-4 py-2.5 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Retour à la liste</a>
            </div>
        </div>
    </div>
</x-super-admin-layout>
