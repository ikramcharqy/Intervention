<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Créer un Type d'Intervention</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('types-intervention.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="nom" class="block font-medium">Nom</label>
                        <input type="text" name="nom" id="nom" required value="{{ old('nom') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @error('nom')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block font-medium">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="duree_estimee" class="block font-medium">Durée estimée (minutes)</label>
                        <input type="number" name="duree_estimee" id="duree_estimee" min="1" value="{{ old('duree_estimee') }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @error('duree_estimee')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', true))
                            class="rounded border-gray-300 dark:border-gray-700">
                        <label for="is_active" class="font-medium">Actif</label>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Enregistrer</button>
                        <a href="{{ route('types-intervention.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
