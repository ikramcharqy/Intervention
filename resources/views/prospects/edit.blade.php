<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier Prospect') }} - {{ $prospect->nom_entreprise }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('prospects.update', $prospect) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Entreprise -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                                <input type="text" name="nom_entreprise" required value="{{ old('nom_entreprise', $prospect->nom_entreprise) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('nom_entreprise')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <!-- Nom Contact -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nom du contact</label>
                                <input type="text" name="nom_contact" value="{{ old('nom_contact', $prospect->nom_contact) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" name="email" value="{{ old('email', $prospect->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <!-- Téléphone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                                <input type="text" name="telephone" value="{{ old('telephone', $prospect->telephone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <!-- Adresse -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Adresse</label>
                                <input type="text" name="adresse" value="{{ old('adresse', $prospect->adresse) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </div>

                            <!-- Statut -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Statut</label>
                                <select name="statut" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="Nouveau" {{ old('statut', $prospect->statut) == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                                    <option value="Qualifié" {{ old('statut', $prospect->statut) == 'Qualifié' ? 'selected' : '' }}>Qualifié</option>
                                    <option value="Négociation" {{ old('statut', $prospect->statut) == 'Négociation' ? 'selected' : '' }}>Négociation</option>
                                    <option value="Converti" {{ old('statut', $prospect->statut) == 'Converti' ? 'selected' : '' }}>Converti</option>
                                    <option value="Perdu" {{ old('statut', $prospect->statut) == 'Perdu' ? 'selected' : '' }}>Perdu</option>
                                </select>
                            </div>

                            <!-- Observations -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Observations</label>
                                <textarea name="observations" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('observations', $prospect->observations) }}</textarea>
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('prospects.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 mr-2">Annuler</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
