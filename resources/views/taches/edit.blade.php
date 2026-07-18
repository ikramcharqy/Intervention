<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Modifier la Tâche : {{ $tache->titre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <form method="POST" action="{{ route('taches.update', $tache) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="intervention_id" class="block font-medium">Intervention <span class="text-red-500">*</span></label>
                        <select name="intervention_id" id="intervention_id" required
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach ($interventions as $intervention)
                                <option value="{{ $intervention->id }}" @selected(old('intervention_id', $tache->intervention_id) == $intervention->id)>{{ $intervention->reference }}</option>
                            @endforeach
                        </select>
                        @error('intervention_id')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="titre" class="block font-medium">Titre <span class="text-red-500">*</span></label>
                        <input type="text" name="titre" id="titre" required value="{{ old('titre', $tache->titre) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @error('titre')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="description" class="block font-medium">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description', $tache->description) }}</textarea>
                    </div>

                    <div>
                        <label for="statut" class="block font-medium">Statut</label>
                        <select name="statut" id="statut"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            @foreach (['A faire', 'En cours', 'Terminee', 'Annulee'] as $s)
                                <option value="{{ $s }}" @selected(old('statut', $tache->statut) == $s)>{{ $s }}</option>
                            @endforeach
                        </select>
                        @error('statut')<p class="text-red-500 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="ordre" class="block font-medium">Ordre d'exécution</label>
                        <input type="number" name="ordre" id="ordre" min="1" value="{{ old('ordre', $tache->ordre) }}"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>

                    <div>
                        <label for="commentaire_technicien" class="block font-medium">Commentaire technicien</label>
                        <textarea name="commentaire_technicien" id="commentaire_technicien" rows="3"
                            class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('commentaire_technicien', $tache->commentaire_technicien) }}</textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Mettre à jour</button>
                        <a href="{{ route('taches.show', $tache) }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
