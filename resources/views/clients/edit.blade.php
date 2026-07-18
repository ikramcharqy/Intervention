<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Modifier le Client : {{ $client->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('clients.update', $client) }}" class="space-y-6" x-data="{ typeClient: '{{ old('type_client', $client->type_client) }}' }">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Code Client --}}
                        <div>
                            <label for="code_client" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Code Client <span class="text-red-500">*</span></label>
                            <input type="text" name="code_client" id="code_client" required value="{{ old('code_client', $client->code_client) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('code_client') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Type Client --}}
                        <div>
                            <label for="type_client" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type de Client <span class="text-red-500">*</span></label>
                            <select name="type_client" id="type_client" required x-model="typeClient"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="Entreprise">Entreprise</option>
                                <option value="Particulier">Particulier</option>
                                <option value="Administration">Administration</option>
                            </select>
                            @error('type_client') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nom / Raison sociale --}}
                        <div class="col-span-1 md:col-span-2">
                            <label for="nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom ou Raison Sociale <span class="text-red-500">*</span></label>
                            <input type="text" name="nom" id="nom" required value="{{ old('nom', $client->nom) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nom') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nom Contact --}}
                        <div>
                            <label for="nom_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom du Contact</label>
                            <input type="text" name="nom_contact" id="nom_contact" value="{{ old('nom_contact', $client->nom_contact) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('nom_contact') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- E-mail --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">E-mail</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Téléphone principal --}}
                        <div>
                            <label for="telephone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone principal <span class="text-red-500">*</span></label>
                            <input type="text" name="telephone" id="telephone" required value="{{ old('telephone', $client->telephone) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('telephone') <span class="text-red-500 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                        </div>

                        {{-- Téléphone secondaire --}}
                        <div>
                            <label for="telephone_secondaire" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone secondaire</label>
                            <input type="text" name="telephone_secondaire" id="telephone_secondaire" value="{{ old('telephone_secondaire', $client->telephone_secondaire) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('telephone_secondaire') <span class="text-red-500 text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                        </div>

                        {{-- Adresse Facturation --}}
                        <div class="col-span-1 md:col-span-2">
                            <label for="adresse_facturation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Adresse de Facturation</label>
                            <textarea name="adresse_facturation" id="adresse_facturation" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('adresse_facturation', $client->adresse_facturation) }}</textarea>
                            @error('adresse_facturation') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label for="ville" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ville <span class="text-red-500">*</span></label>
                            <input type="text" name="ville" id="ville" required value="{{ old('ville', $client->ville) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('ville') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pays --}}
                        <div>
                            <label for="pays" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pays</label>
                            <input type="text" name="pays" id="pays" value="{{ old('pays', $client->pays) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            @error('pays') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Infos Entreprise (Conditionnelles selon type_client) --}}
                    <div x-show="typeClient === 'Entreprise'" class="border-t border-gray-200 dark:border-gray-700 pt-6 space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations de l'Entreprise</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="ice" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ICE</label>
                                <input type="text" name="ice" id="ice" value="{{ old('ice', $client->clientEntreprise->ice ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('ice') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="if" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Identifiant Fiscal (IF)</label>
                                <input type="text" name="if" id="if" value="{{ old('if', $client->clientEntreprise->if ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('if') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="rc" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registre du Commerce (RC)</label>
                                <input type="text" name="rc" id="rc" value="{{ old('rc', $client->clientEntreprise->rc ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('rc') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="patente" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patente</label>
                                <input type="text" name="patente" id="patente" value="{{ old('patente', $client->clientEntreprise->patente ?? '') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                @error('patente') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Observations --}}
                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observations</label>
                        <textarea name="observations" id="observations" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('observations', $client->observations) }}</textarea>
                        @error('observations') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="flex justify-end gap-3">
                        <a href="{{ route('clients.show', $client) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-150">
                            Annuler
                        </a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition duration-150">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
