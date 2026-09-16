<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('interventions.a-planifier') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-[#181C32] font-heading flex items-center gap-3">
                        Planification &amp; Affectation
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-xs font-bold rounded-lg border border-blue-200">
                            {{ $intervention->code_intervention }}
                        </span>
                    </h1>
                    <p class="text-sm text-[#A1A5B7]">Attribuez les dates prévues et le technicien responsable de l'intervention</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Récapitulatif Intervention --}}
        <div class="metronic-card p-6 bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-2xl shadow-xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-indigo-800/60 pb-4 mb-4 gap-4">
                <div>
                    <div class="text-xs text-indigo-300 font-bold uppercase tracking-wider">Chantier &amp; Emplacement</div>
                    <div class="text-lg font-bold text-white font-heading mt-0.5">
                        {{ $intervention->chantier->nom ?? '—' }}
                    </div>
                    <div class="text-xs text-slate-300 mt-0.5 flex items-center gap-2">
                        <span>Client : <strong class="text-indigo-200">{{ $intervention->chantier->client->nom ?? '—' }}</strong></span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1.5 bg-white/10 backdrop-blur-md rounded-xl text-xs font-semibold text-indigo-200 border border-white/10">
                        Type : {{ $intervention->typeIntervention->nom ?? 'Standard' }}
                    </span>
                    <span class="px-3 py-1.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-xl text-xs font-bold uppercase">
                        Priorité {{ $intervention->priorite }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block mb-1">Créé par</span>
                    <span class="font-semibold text-slate-200">{{ $intervention->createur->name ?? 'Commercial' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Date de demande</span>
                    <span class="font-semibold text-slate-200">{{ $intervention->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Statut actuel</span>
                    <span class="font-bold text-amber-400">{{ $intervention->statut }}</span>
                </div>
            </div>
        </div>

        {{-- Formulaire de Planification & Affectation --}}
        <div class="metronic-card p-6 md:p-8 bg-white shadow-sm border border-slate-200 rounded-2xl">
            <form action="{{ route('interventions.savePlanification', $intervention) }}" method="POST" class="space-y-6">
                @csrf

                <div class="border-b border-slate-100 pb-4">
                    <h3 class="text-base font-bold text-[#181C32] font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        1. Affectation du Technicien
                    </h3>
                    <p class="text-xs text-[#A1A5B7] mt-0.5">Choisissez le technicien qualifié pour exécuter cette mission</p>
                </div>

                <div>
                    <label for="technicien_id" class="block text-xs font-bold text-[#181C32] uppercase tracking-wider mb-2">
                        Technicien Responsable <span class="text-rose-500">*</span>
                    </label>
                    <select name="technicien_id" id="technicien_id" required
                        class="w-full py-3 px-4 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-[#181C32]">
                        <option value="">-- Sélectionner un technicien --</option>
                        @foreach($techniciens as $t)
                            <option value="{{ $t->id }}" @selected(old('technicien_id', $intervention->technicien_id) == $t->id)>
                                👨‍🔧 {{ $t->name }} {{ $t->prenom }}
                            </option>
                        @endforeach
                    </select>
                    @error('technicien_id')
                        <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-b border-slate-100 pb-4 pt-4">
                    <h3 class="text-base font-bold text-[#181C32] font-heading flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        2. Dates &amp; Priorité
                    </h3>
                    <p class="text-xs text-[#A1A5B7] mt-0.5">Fixez le créneau horaire prévu pour le début et la fin de l'intervention</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="date_prevue_debut" class="block text-xs font-bold text-[#181C32] uppercase tracking-wider mb-2">
                            Date &amp; Heure de Début <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="date_prevue_debut" id="date_prevue_debut" required
                            value="{{ old('date_prevue_debut', $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                            class="w-full py-3 px-4 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-[#181C32]">
                        @error('date_prevue_debut')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="date_prevue_fin" class="block text-xs font-bold text-[#181C32] uppercase tracking-wider mb-2">
                            Date &amp; Heure de Fin Estimée
                        </label>
                        <input type="datetime-local" name="date_prevue_fin" id="date_prevue_fin"
                            value="{{ old('date_prevue_fin', $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('Y-m-d\TH:i') : '') }}"
                            class="w-full py-3 px-4 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-[#181C32]">
                        @error('date_prevue_fin')
                            <p class="text-xs text-rose-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="priorite" class="block text-xs font-bold text-[#181C32] uppercase tracking-wider mb-2">
                            Priorité Métier
                        </label>
                        <select name="priorite" id="priorite"
                            class="w-full py-3 px-4 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-[#181C32]">
                            @foreach(['Faible', 'Normale', 'Haute', 'Urgente'] as $p)
                                <option value="{{ $p }}" @selected(old('priorite', $intervention->priorite) === $p)>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="commentaire" class="block text-xs font-bold text-[#181C32] uppercase tracking-wider mb-2">
                            Consignes Administrateur
                        </label>
                        <input type="text" name="commentaire" id="commentaire" placeholder="Ex: Matériel spécifique requis..."
                            value="{{ old('commentaire') }}"
                            class="w-full py-3 px-4 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-[#181C32]">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('interventions.a-planifier') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Valider la Planification &amp; Affecter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
