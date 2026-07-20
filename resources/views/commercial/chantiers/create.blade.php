<x-commercial-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Créer un Chantier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('chantiers.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="client_id" class="block font-medium">Client</label>
                        <select name="client_id" id="client_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="code_chantier" class="block font-medium">Code Chantier</label>
                        <input type="text" name="code_chantier" id="code_chantier" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="nom" class="block font-medium">Nom du chantier</label>
                        <input type="text" name="nom" id="nom" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="type_local" class="block font-medium">Type de local</label>
                        <select name="type_local" id="type_local" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach (['Maison', 'Appartement', 'Magasin', 'Bureau', 'Usine', 'Restaurant', 'Hotel', 'Hopital', 'Ecole', 'Administration', 'Autre'] as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="adresse" class="block font-medium">Adresse</label>
                        <textarea name="adresse" id="adresse" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                    </div>
                    <div>
                        <label for="ville" class="block font-medium">Ville</label>
                        <input type="text" name="ville" id="ville" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="responsable" class="block font-medium">Responsable</label>
                        <input type="text" name="responsable" id="responsable" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="telephone_responsable" class="block font-medium">Téléphone responsable</label>
                        <input type="text" name="telephone_responsable" id="telephone_responsable" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="email_responsable" class="block font-medium">E-mail responsable</label>
                        <input type="email" name="email_responsable" id="email_responsable" required class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</x-commercial-layout>
