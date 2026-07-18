<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Modifier l'Intervention : {{ $intervention->code_intervention }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('interventions.update', $intervention) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label for="code_intervention" class="block font-medium">Code Intervention <span class="text-red-500">*</span></label>
                            <input type="text" name="code_intervention" id="code_intervention" required
                                value="{{ old('code_intervention', $intervention->code_intervention) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('code_intervention')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        {{-- Étape 1 : Client --}}
                        <div>
                            <label for="client_id" class="block font-medium">Client <span class="text-red-500">*</span></label>
                            <select name="client_id" id="client_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        @selected(old('client_id', $intervention->chantier->client_id ?? '') == $client->id)>
                                        {{ $client->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Étape 2 : Chantier (chargé dynamiquement) --}}
                        <div>
                            <label for="chantier_id" class="block font-medium">Chantier <span class="text-red-500">*</span></label>
                            <select name="chantier_id" id="chantier_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">-- Sélectionner d'abord un client --</option>
                                @foreach ($chantiers as $chantier)
                                    <option value="{{ $chantier->id }}" @selected(old('chantier_id', $intervention->chantier_id) == $chantier->id)>
                                        {{ $chantier->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('chantier_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        {{-- Étape 3 : Emplacement (chargé dynamiquement) --}}
                        <div>
                            <label for="emplacement_id" class="block font-medium">Emplacement <span class="text-red-500">*</span></label>
                            <select name="emplacement_id" id="emplacement_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                <option value="">-- Sélectionner d'abord un chantier --</option>
                                @foreach ($emplacements as $emplacement)
                                    <option value="{{ $emplacement->id }}" @selected(old('emplacement_id', $intervention->emplacement_id) == $emplacement->id)>
                                        {{ $emplacement->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('emplacement_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="type_intervention_id" class="block font-medium">Type d'intervention <span class="text-red-500">*</span></label>
                            <select name="type_intervention_id" id="type_intervention_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach ($typesIntervention as $type)
                                    <option value="{{ $type->id }}" @selected(old('type_intervention_id', $intervention->type_intervention_id) == $type->id)>{{ $type->nom }}</option>
                                @endforeach
                            </select>
                            @error('type_intervention_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="technicien_id" class="block font-medium">Technicien <span class="text-red-500">*</span></label>
                            <select name="technicien_id" id="technicien_id" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach ($techniciens as $tech)
                                    <option value="{{ $tech->id }}" @selected(old('technicien_id', $intervention->technicien_id) == $tech->id)>{{ $tech->prenom }} {{ $tech->name }}</option>
                                @endforeach
                            </select>
                            @error('technicien_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="priorite" class="block font-medium">Priorité <span class="text-red-500">*</span></label>
                            <select name="priorite" id="priorite" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach (['Faible', 'Normale', 'Haute', 'Urgente'] as $p)
                                    <option value="{{ $p }}" @selected(old('priorite', $intervention->priorite) == $p)>{{ $p }}</option>
                                @endforeach
                            </select>
                            @error('priorite')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="statut" class="block font-medium">Statut <span class="text-red-500">*</span></label>
                            <select name="statut" id="statut" required
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                                @foreach (['Planifiee', 'En cours', 'Suspendue', 'Terminee', 'Annulee'] as $s)
                                    <option value="{{ $s }}" @selected(old('statut', $intervention->statut) == $s)>{{ $s }}</option>
                                @endforeach
                            </select>
                            @error('statut')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="duree_prevue" class="block font-medium">Durée prévue (min)</label>
                            <input type="number" name="duree_prevue" id="duree_prevue" min="1"
                                value="{{ old('duree_prevue', $intervention->duree_prevue) }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('duree_prevue')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="date_prevue_debut" class="block font-medium">Date de début prévue <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="date_prevue_debut" id="date_prevue_debut" required
                                value="{{ old('date_prevue_debut', $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('Y-m-d\TH:i') : '') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('date_prevue_debut')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="date_prevue_fin" class="block font-medium">Date de fin prévue <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="date_prevue_fin" id="date_prevue_fin" required
                                value="{{ old('date_prevue_fin', $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('Y-m-d\TH:i') : '') }}"
                                class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('date_prevue_fin')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block font-medium">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description', $intervention->description) }}</textarea>
                        @error('description')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="observations" class="block font-medium">Observations / Consignes</label>
                        <textarea name="observations" id="observations" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('observations', $intervention->observations) }}</textarea>
                        @error('observations')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Mettre à jour</button>
                        <a href="{{ route('interventions.show', $intervention) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const clientSelect      = document.getElementById('client_id');
        const chantierSelect    = document.getElementById('chantier_id');
        const emplacementSelect = document.getElementById('emplacement_id');

        const currentChantier    = "{{ $intervention->chantier_id }}";
        const currentEmplacement = "{{ $intervention->emplacement_id }}";

        clientSelect.addEventListener('change', function () {
            const clientId = this.value;
            fetch(`/clients/${clientId}/chantiers`)
                .then(r => r.json())
                .then(chantiers => {
                    chantierSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
                    emplacementSelect.innerHTML = '<option value="">-- Sélectionner d\'abord un chantier --</option>';
                    chantiers.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.nom;
                        if (c.id == currentChantier) opt.selected = true;
                        chantierSelect.appendChild(opt);
                    });
                    if (currentChantier) chantierSelect.dispatchEvent(new Event('change'));
                });
        });

        chantierSelect.addEventListener('change', function () {
            const chantierId = this.value;
            if (!chantierId) {
                emplacementSelect.innerHTML = '<option value="">-- Sélectionner d\'abord un chantier --</option>';
                return;
            }
            fetch(`/chantiers/${chantierId}/emplacements`)
                .then(r => r.json())
                .then(emplacements => {
                    emplacementSelect.innerHTML = '<option value="">-- Sélectionner --</option>';
                    emplacements.forEach(e => {
                        const opt = document.createElement('option');
                        opt.value = e.id;
                        opt.textContent = e.nom;
                        if (e.id == currentEmplacement) opt.selected = true;
                        emplacementSelect.appendChild(opt);
                    });
                });
        });

        // Déclencher le chargement initial depuis le client actuel de l'intervention
        if (clientSelect.value) {
            clientSelect.dispatchEvent(new Event('change'));
        }
    </script>
</x-app-layout>
