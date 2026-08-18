<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center space-x-2">
                <i class="fa-solid fa-paper-plane text-brand-600"></i>
                <span>Demander une Intervention</span>
            </h2>
            <a href="{{ route('client.interventions.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-800 flex items-center space-x-1">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Retour aux interventions</span>
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm rounded-2xl border border-slate-200 p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Formulaire de Demande d'Intervention</h3>
                <p class="text-xs text-slate-500 mt-1">Remplissez les détails ci-dessous. Votre responsable commercial étudiera votre demande et vous recontactera par téléphone ou email.</p>
            </div>

            <form action="{{ route('client.demandes.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Chantier concerné <span class="text-red-500">*</span></label>
                    <select name="chantier_id" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm">
                        <option value="">-- Sélectionnez votre chantier --</option>
                        @foreach($chantiers as $c)
                            <option value="{{ $c->id }}" {{ old('chantier_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }} ({{ $c->code_chantier }})</option>
                        @endforeach
                    </select>
                    @error('chantier_id') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Type d'intervention souhaité</label>
                        <select name="type_intervention_id" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm">
                            <option value="">-- Type d'intervention --</option>
                            @foreach($typesIntervention as $t)
                                <option value="{{ $t->id }}" {{ old('type_intervention_id') == $t->id ? 'selected' : '' }}>{{ $t->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Degré de priorité <span class="text-red-500">*</span></label>
                        <select name="priorite" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm">
                            <option value="Faible" {{ old('priorite') == 'Faible' ? 'selected' : '' }}>🟢 Faible – Pas d'urgence</option>
                            <option value="Normale" {{ old('priorite', 'Normale') == 'Normale' ? 'selected' : '' }} selected>🟡 Normale – Standard</option>
                            <option value="Haute" {{ old('priorite') == 'Haute' ? 'selected' : '' }}>🟠 Haute – Problème bloquant</option>
                            <option value="Urgente" {{ old('priorite') == 'Urgente' ? 'selected' : '' }}>🔴 Urgente – Arrêt d'activité</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Objet de la demande <span class="text-red-500">*</span></label>
                    <input type="text" name="objet" required value="{{ old('objet') }}" placeholder="Ex: Maintenance préventive réseau, Panne armoire électrique..." class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm">
                    @error('objet') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description détaillée des besoins ou symptômes <span class="text-red-500">*</span></label>
                    <textarea name="description" required rows="5" placeholder="Décrivez précisément votre besoin, la nature de la panne ou les contraintes d'accès sur votre site..." class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 shadow-sm resize-none">{{ old('description') }}</textarea>
                    @error('description') <span class="text-red-500 text-[11px]">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 flex justify-end space-x-3">
                    <a href="{{ route('client.interventions.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs border border-slate-300 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-brand-600 to-indigo-600 hover:from-brand-500 hover:to-indigo-500 text-white font-bold rounded-xl text-xs shadow-md transition flex items-center space-x-2">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Soumettre la demande au Commercial</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
