<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Tâche : {{ $tache->titre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><strong>Titre :</strong> {{ $tache->titre }}</div>
                    <div><strong>Intervention :</strong>
                        <a href="{{ route('interventions.show', $tache->intervention) }}" class="text-blue-500 hover:text-blue-700">
                            {{ $tache->intervention->reference ?? '-' }}
                        </a>
                    </div>
                    <div>
                        <strong>Statut :</strong>
                        @php $statusColors = ['A faire' => 'bg-gray-100 text-gray-800', 'En cours' => 'bg-yellow-100 text-yellow-800', 'Terminee' => 'bg-green-100 text-green-800', 'Annulee' => 'bg-red-100 text-red-800']; @endphp
                        <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$tache->statut] ?? '' }}">{{ $tache->statut }}</span>
                    </div>
                    <div><strong>Ordre :</strong> {{ $tache->ordre }}</div>
                    @if ($tache->date_debut)
                        <div><strong>Début :</strong> {{ \Carbon\Carbon::parse($tache->date_debut)->format('d/m/Y H:i') }}</div>
                    @endif
                    @if ($tache->date_fin)
                        <div><strong>Fin :</strong> {{ \Carbon\Carbon::parse($tache->date_fin)->format('d/m/Y H:i') }}</div>
                    @endif
                </div>

                @if ($tache->description)
                    <div><strong>Description :</strong><br>{{ $tache->description }}</div>
                @endif

                @if ($tache->commentaire_technicien)
                    <div class="bg-blue-50 dark:bg-blue-900 p-3 rounded-md">
                        <strong>Commentaire technicien :</strong><br>{{ $tache->commentaire_technicien }}
                    </div>
                @endif

                <div class="flex gap-3 mt-4">
                    <a href="{{ route('taches.edit', $tache) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">Modifier</a>
                    <a href="{{ route('taches.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Retour</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
