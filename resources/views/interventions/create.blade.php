<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">Créer une Intervention</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Soft UI Card Container -->
            <div class="soft-card p-6 md:p-8">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-6 mb-6 border-b border-slate-100">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 mb-0">Nouvelle Mission d'Intervention</h3>
                        <p class="text-xs text-slate-400 mb-0">Saisissez les paramètres de la mission terrain pour l'affecter à un technicien</p>
                    </div>
                    <span class="px-3 py-1 soft-gradient-primary text-white text-xs font-bold rounded-xl shadow-xs">
                        Édition Admin
                    </span>
                </div>

                <form method="POST" action="{{ route('interventions.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Code Intervention -->
                        <div class="col-span-1 md:col-span-2">
                            <label for="code_intervention" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Code Intervention <span class="text-pink-600">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="code_intervention" id="code_intervention" required
                                    value="{{ old('code_intervention') }}"
                                    placeholder="Ex: INT-2026-0001"
                                    class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500 font-mono">
                            </div>
                            @error('code_intervention')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Étape 1 : Client -->
                        <div>
                            <label for="client_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Client <span class="text-pink-600">*</span>
                            </label>
                            <select name="client_id" id="client_id" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>
                                        {{ $client->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Étape 2 : Chantier -->
                        <div>
                            <label for="chantier_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Chantier <span class="text-pink-600">*</span>
                            </label>
                            <select name="chantier_id" id="chantier_id" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">-- Sélectionner d'abord un client --</option>
                                @foreach ($chantiers as $chantier)
                                    <option value="{{ $chantier->id }}" @selected(old('chantier_id') == $chantier->id)>
                                        {{ $chantier->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chantier_id')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Étape 3 : Emplacement -->
                        <div>
                            <label for="emplacement_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Emplacement <span class="text-pink-600">*</span>
                            </label>
                            <select name="emplacement_id" id="emplacement_id" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">-- Sélectionner d'abord un chantier --</option>
                                @foreach ($emplacements as $emplacement)
                                    <option value="{{ $emplacement->id }}" @selected(old('emplacement_id') == $emplacement->id)>
                                        {{ $emplacement->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('emplacement_id')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Type d'intervention -->
                        <div>
                            <label for="type_intervention_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Type d'intervention <span class="text-pink-600">*</span>
                            </label>
                            <select name="type_intervention_id" id="type_intervention_id" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($typesIntervention as $type)
                                    <option value="{{ $type->id }}" @selected(old('type_intervention_id') == $type->id)>{{ $type->nom }}</option>
                                @endforeach
                            </select>
                            @error('type_intervention_id')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Technicien -->
                        <div>
                            <label for="technicien_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Technicien Assigné <span class="text-pink-600">*</span>
                            </label>
                            <select name="technicien_id" id="technicien_id" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($techniciens as $tech)
                                    <option value="{{ $tech->id }}" @selected(old('technicien_id') == $tech->id)>{{ $tech->prenom }} {{ $tech->name }}</option>
                                @endforeach
                            </select>
                            @error('technicien_id')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Priorité -->
                        <div>
                            <label for="priorite" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Priorité <span class="text-pink-600">*</span>
                            </label>
                            <select name="priorite" id="priorite" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                                @foreach (['Faible', 'Normale', 'Haute', 'Urgente'] as $p)
                                    <option value="{{ $p }}" @selected(old('priorite', 'Normale') == $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('priorite')<p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Dates Prévues -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <div>
                            <label for="date_prevue_debut" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Date & Heure Début Prévue
                            </label>
                            <input type="datetime-local" name="date_prevue_debut" id="date_prevue_debut"
                                value="{{ old('date_prevue_debut') }}"
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500">
                        </div>
                        <div>
                            <label for="date_prevue_fin" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Date & Heure Fin Prévue
                            </label>
                            <input type="datetime-local" name="date_prevue_fin" id="date_prevue_fin"
                                value="{{ old('date_prevue_fin') }}"
                                class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="pt-4 border-t border-slate-100">
                        <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                            Description des Travaux
                        </label>
                        <textarea name="description" id="description" rows="4"
                            placeholder="Détaillez les consignes et la nature des travaux..."
                            class="w-full rounded-xl border-slate-200 text-sm focus:ring-2 focus:ring-pink-500">{{ old('description') }}</textarea>
                    </div>

                    <!-- Actions Formulaire -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('interventions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2.5 soft-gradient-primary text-white font-bold text-xs rounded-xl shadow-md transition hover:opacity-90">
                            <i class="fas fa-save me-1"></i> Enregistrer l'Intervention
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
