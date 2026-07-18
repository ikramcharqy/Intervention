<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Type d'Intervention : {{ $typeIntervention->nom }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Nom :</strong> {{ $typeIntervention->nom }}</div>
                    <div><strong>Durée estimée :</strong> {{ $typeIntervention->duree_estimee ? $typeIntervention->duree_estimee . ' min' : '-' }}</div>
                    <div class="col-span-2"><strong>Description :</strong> {{ $typeIntervention->description ?? '-' }}</div>
                    <div>
                        <strong>Statut :</strong>
                        <span class="{{ $typeIntervention->is_active ? 'text-green-500' : 'text-red-500' }}">
                            {{ $typeIntervention->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <a href="{{ route('types-intervention.edit', $typeIntervention) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Modifier</a>
                    <a href="{{ route('types-intervention.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Retour</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
