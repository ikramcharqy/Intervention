<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Demande : {{ $demandeIntervention->reference }}
            </h2>
            @if($demandeIntervention->statut === 'En attente')
                <a href="{{ route('demande-interventions.edit', $demandeIntervention) }}"
                   class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm font-medium">
                    Modifier
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Statut Badge --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest font-semibold mb-1">Statut de la demande</p>
                        @php
                            $statutColors = [
                                'En attente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                                'Acceptée' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'Refusée' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                'Planifiée' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                            ];
                            $color = $statutColors[$demandeIntervention->statut] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-3 py-1.5 text-sm font-bold rounded-full {{ $color }}">
                            {{ $demandeIntervention->statut }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Soumise le</p>
                        <p class="text-sm font-semibold">{{ $demandeIntervention->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Informations principales --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Informations de la demande</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500 font-medium">Référence</dt>
                        <dd class="font-mono text-gray-900 dark:text-gray-100">{{ $demandeIntervention->reference }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Commercial</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->commercial->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Client</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-semibold">{{ $demandeIntervention->client->nom ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Chantier</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->chantier->nom ?? 'Non spécifié' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Type d'intervention</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $demandeIntervention->typeIntervention->nom ?? 'Non spécifié' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500 font-medium">Priorité</dt>
                        <dd>
                            @php
                                $prioColors = ['Urgente' => 'text-red-600 font-bold', 'Haute' => 'text-orange-600 font-semibold', 'Normale' => 'text-gray-900', 'Faible' => 'text-gray-500'];
                            @endphp
                            <span class="{{ $prioColors[$demandeIntervention->priorite] ?? 'text-gray-900' }}">
                                {{ $demandeIntervention->priorite }}
                            </span>
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 font-medium">Objet</dt>
                        <dd class="text-gray-900 dark:text-gray-100 font-semibold text-base">{{ $demandeIntervention->objet }}</dd>
                    </div>
                    @if($demandeIntervention->description)
                    <div class="sm:col-span-2">
                        <dt class="text-gray-500 font-medium">Description</dt>
                        <dd class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $demandeIntervention->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Photos --}}
            @if(!empty($demandeIntervention->photos))
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Photos ({{ count($demandeIntervention->photos) }})</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($demandeIntervention->photos as $photo)
                        <a href="{{ Storage::url($photo) }}" target="_blank">
                            <img src="{{ Storage::url($photo) }}" alt="Photo" class="w-full h-32 object-cover rounded-lg hover:opacity-80 transition">
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Documents --}}
            @if(!empty($demandeIntervention->documents))
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <h3 class="text-base font-semibold border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Documents ({{ count($demandeIntervention->documents) }})</h3>
                <ul class="space-y-2">
                    @foreach($demandeIntervention->documents as $doc)
                        <li>
                            <a href="{{ Storage::url($doc) }}" target="_blank"
                               class="flex items-center gap-2 text-sm text-indigo-600 hover:underline dark:text-indigo-400">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                {{ basename($doc) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Action Admin : Planifier --}}
            @role('Admin')
            @if($demandeIntervention->statut === 'En attente')
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-indigo-800 dark:text-indigo-300">Action Administrateur</p>
                    <p class="text-sm text-indigo-600 dark:text-indigo-400 mt-1">Vous pouvez accepter et planifier cette demande ou la refuser.</p>
                </div>
                <div class="flex gap-3">
                    {{-- Bouton Planifier → redirige vers créer une intervention avec données pré-remplies --}}
                    <a href="{{ route('interventions.create', [
                            'client_id' => $demandeIntervention->client_id,
                            'chantier_id' => $demandeIntervention->chantier_id,
                            'type_intervention_id' => $demandeIntervention->type_intervention_id,
                            'priorite' => $demandeIntervention->priorite,
                            'description' => $demandeIntervention->description,
                            'demande_id' => $demandeIntervention->id
                        ]) }}"
                       class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-md hover:bg-green-700">
                        Planifier l'intervention
                    </a>

                    <form action="{{ route('demande-interventions.update', $demandeIntervention) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="client_id" value="{{ $demandeIntervention->client_id }}">
                        <input type="hidden" name="chantier_id" value="{{ $demandeIntervention->chantier_id }}">
                        <input type="hidden" name="type_intervention_id" value="{{ $demandeIntervention->type_intervention_id }}">
                        <input type="hidden" name="priorite" value="{{ $demandeIntervention->priorite }}">
                        <input type="hidden" name="objet" value="{{ $demandeIntervention->objet }}">
                        <input type="hidden" name="description" value="{{ $demandeIntervention->description }}">
                        <input type="hidden" name="statut" value="Refusée">
                        <button type="submit" onclick="return confirm('Refuser cette demande ?')"
                            class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-md hover:bg-red-700">
                            Refuser
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endrole

            <div class="text-right">
                <a href="{{ route('demande-interventions.index') }}" class="text-sm text-gray-500 hover:underline">
                    ← Retour à la liste des demandes
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
