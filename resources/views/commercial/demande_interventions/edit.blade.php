<x-commercial-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier la demande : {{ $demandeIntervention->reference }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('demande-interventions.update', $demandeIntervention) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Client --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client *</label>
                                <select name="client_id" id="client_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner un client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('client_id', $demandeIntervention->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chantier --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Chantier</label>
                                <select name="chantier_id" id="chantier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Sélectionner un chantier --</option>
                                    @if($demandeIntervention->chantier)
                                        <option value="{{ $demandeIntervention->chantier->id }}" selected>
                                            {{ $demandeIntervention->chantier->nom }}
                                        </option>
                                    @endif
                                </select>
                            </div>

                            {{-- Type d'intervention --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'intervention</label>
                                <select name="type_intervention_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- Aucun --</option>
                                    @foreach($typesIntervention as $type)
                                        <option value="{{ $type->id }}"
                                            {{ old('type_intervention_id', $demandeIntervention->type_intervention_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Priorité --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priorité *</label>
                                <select name="priorite" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @foreach(['Normale', 'Faible', 'Haute', 'Urgente'] as $p)
                                        <option value="{{ $p }}" {{ old('priorite', $demandeIntervention->priorite) == $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Objet --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Objet *</label>
                                <input type="text" name="objet" required
                                    value="{{ old('objet', $demandeIntervention->objet) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea name="description" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $demandeIntervention->description) }}</textarea>
                            </div>

                            {{-- Nouvelles Photos --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ajouter des photos
                                    @if(!empty($demandeIntervention->photos))
                                        <span class="text-gray-400 font-normal">({{ count($demandeIntervention->photos) }} existante(s))</span>
                                    @endif
                                </label>
                                <input type="file" name="photos[]" multiple accept="image/*"
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                            {{-- Nouveaux Documents --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Ajouter des documents
                                    @if(!empty($demandeIntervention->documents))
                                        <span class="text-gray-400 font-normal">({{ count($demandeIntervention->documents) }} existant(s))</span>
                                    @endif
                                </label>
                                <input type="file" name="documents[]" multiple
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('demande-interventions.show', $demandeIntervention) }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-300">
                                Annuler
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-semibold">
                                Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('client_id').addEventListener('change', function() {
            const clientId = this.value;
            const chantierSelect = document.getElementById('chantier_id');
            chantierSelect.innerHTML = '<option value="">-- Chargement... --</option>';
            if (!clientId) {
                chantierSelect.innerHTML = '<option value="">-- Sélectionner un chantier --</option>';
                return;
            }
            fetch(`/clients/${clientId}/chantiers`)
                .then(res => res.json())
                .then(data => {
                    chantierSelect.innerHTML = '<option value="">-- Sélectionner un chantier --</option>';
                    data.forEach(c => {
                        const sel = c.id == {{ $demandeIntervention->chantier_id ?? 'null' }} ? 'selected' : '';
                        chantierSelect.innerHTML += `<option value="${c.id}" ${sel}>${c.nom}</option>`;
                    });
                });
        });
    </script>
    @endpush
</x-commercial-layout>
