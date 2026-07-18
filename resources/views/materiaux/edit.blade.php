<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Modifier le Matériau : ') }} {{ $materiau->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('materiaux.update', $materiau) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="reference" class="block font-medium">Référence (optionnel)</label>
                            <input type="text" name="reference" id="reference" value="{{ old('reference', $materiau->reference) }}" placeholder="Ex: REF-001, SKU..." class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('reference') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="nom" class="block font-medium">Nom du matériau <span class="text-red-500">*</span></label>
                            <input type="text" name="nom" id="nom" required value="{{ old('nom', $materiau->nom) }}" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="unite" class="block font-medium">Unité de mesure <span class="text-red-500">*</span></label>
                            <input type="text" name="unite" id="unite" required value="{{ old('unite', $materiau->unite) }}" placeholder="Ex: kg, litre, mètre, pièce" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('unite') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="prix_unitaire" class="block font-medium">Prix unitaire HT (€)</label>
                            <input type="number" name="prix_unitaire" id="prix_unitaire" value="{{ old('prix_unitaire', $materiau->prix_unitaire) }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @error('prix_unitaire') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block font-medium">Description</label>
                            <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description', $materiau->description) }}</textarea>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $materiau->is_active)) class="rounded border-gray-300 dark:border-gray-700">
                            <label for="is_active" class="font-medium">Actif</label>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer les modifications</button>
                        <a href="{{ route('materiaux.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
