<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier le Chantier : {{ $chantier->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('chantiers.update', $chantier) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="client_id" class="block font-medium">Client</label>
                        <select name="client_id" id="client_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(old('client_id', $chantier->client_id) == $client->id)>
                                    {{ $client->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="code_chantier" class="block font-medium">Code Chantier</label>
                        <input type="text" name="code_chantier" id="code_chantier" required
                            value="{{ old('code_chantier', $chantier->code_chantier) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="nom" class="block font-medium">Nom du chantier</label>
                        <input type="text" name="nom" id="nom" required
                            value="{{ old('nom', $chantier->nom) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="type_local" class="block font-medium">Type de local</label>
                        <select name="type_local" id="type_local" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach (\App\Models\Chantier::TYPES_LOCAL as $type)
                                <option value="{{ $type }}" @selected(old('type_local', $chantier->type_local) == $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="adresse" class="block font-medium">Adresse</label>
                        <textarea name="adresse" id="adresse" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('adresse', $chantier->adresse) }}</textarea>
                    </div>

                    <div>
                        <label for="ville" class="block font-medium">Ville</label>
                        <input type="text" name="ville" id="ville" required
                            value="{{ old('ville', $chantier->ville) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="responsable" class="block font-medium">Responsable</label>
                        <input type="text" name="responsable" id="responsable" required
                            value="{{ old('responsable', $chantier->responsable) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="telephone_responsable" class="block font-medium">Téléphone responsable</label>
                        <input type="text" name="telephone_responsable" id="telephone_responsable" required
                            value="{{ old('telephone_responsable', $chantier->telephone_responsable) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="email_responsable" class="block font-medium">E-mail responsable</label>
                        <input type="email" name="email_responsable" id="email_responsable" required
                            value="{{ old('email_responsable', $chantier->email_responsable) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Mettre à jour</button>
                        <a href="{{ route('chantiers.show', $chantier) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
