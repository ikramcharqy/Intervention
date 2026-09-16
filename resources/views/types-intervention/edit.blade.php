<x-super-admin-layout>
    <div class="space-y-6" x-data="{ wasActive: {{ $typeIntervention->is_active ? 'true' : 'false' }}, interventionsEnCours: {{ $typeIntervention->interventions()->actives()->count() }} }">
        <div class="flex items-center gap-3">
            <a href="{{ route('types-intervention.show', $typeIntervention) }}" class="w-9 h-9 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 flex items-center justify-center text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800 transition shrink-0">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Modifier : {{ $typeIntervention->nom }}</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Les champs marqués d'un * sont obligatoires.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7 max-w-2xl">
            <form method="POST" action="{{ route('types-intervention.update', $typeIntervention) }}" class="space-y-5"
                  @submit="if (wasActive && document.getElementById('is_active') && !document.getElementById('is_active').checked && interventionsEnCours > 0) { if (!confirm(interventionsEnCours + ' intervention(s) en cours utilisent ce type — la désactivation n\'affectera pas les interventions déjà planifiées mais empêchera d\'en créer de nouvelles. Continuer ?')) { $event.preventDefault(); } }">
                @csrf
                @method('PUT')

                <div>
                    <label for="nom" class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Nom *</label>
                    <input type="text" name="nom" id="nom" required value="{{ old('nom', $typeIntervention->nom) }}"
                           class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    @error('nom')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Description</label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">{{ old('description', $typeIntervention->description) }}</textarea>
                    @error('description')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="duree_estimee" class="block text-xs font-semibold text-[#5A607F] dark:text-slate-400 uppercase mb-1">Durée estimée (minutes)</label>
                    <input type="number" name="duree_estimee" id="duree_estimee" min="1"
                           value="{{ old('duree_estimee', $typeIntervention->duree_estimee) }}"
                           class="w-full px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:border-[#1E5EFF] focus:outline-none">
                    @error('duree_estimee')<p class="text-[#F0142F] text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer w-fit">
                    <input type="checkbox" name="is_active" id="is_active" value="1"
                           @checked(old('is_active', $typeIntervention->is_active))
                           class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#1E5EFF] focus:ring-[#1E5EFF]">
                    <span class="text-sm font-medium text-[#131523] dark:text-slate-200">Actif</span>
                </label>
                @if($typeIntervention->is_active && $typeIntervention->interventions()->actives()->count() > 0)
                    <p class="text-[11px] text-[#B98900] dark:text-amber-400 -mt-3">
                        {{ $typeIntervention->interventions()->actives()->count() }} intervention(s) en cours utilisent ce type.
                    </p>
                @endif

                <div class="pt-4 flex gap-3 border-t border-[#E6E9F4] dark:border-slate-800">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-semibold px-4 py-2.5 transition">
                        Mettre à jour
                    </button>
                    <a href="{{ route('types-intervention.show', $typeIntervention) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-super-admin-layout>
