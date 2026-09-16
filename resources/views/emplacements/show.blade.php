<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Emplacement : {{ $emplacement->nom }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg dark:bg-green-900 dark:text-green-300 dark:border-green-800">
                    {!! session('success') !!}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <a href="{{ route('chantiers.show', $emplacement->chantier) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    ← Retour au chantier {{ $emplacement->chantier->nom }}
                </a>
                <a href="{{ route('interventions.create', ['client_id' => $emplacement->chantier->client_id, 'chantier_id' => $emplacement->chantier_id, 'emplacement_id' => $emplacement->id]) }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Planifier une intervention ici
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">{{ $emplacement->nom }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $emplacement->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $emplacement->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><strong>Chantier :</strong> {{ $emplacement->chantier->nom }}</div>
                    <div><strong>Client :</strong> {{ $emplacement->chantier->client->nom ?? '-' }}</div>
                    <div><strong>QR Code :</strong> <span class="font-mono">{{ $emplacement->qr_code }}</span></div>
                    <div>
                        <strong>Tag NFC :</strong>
                        @if($emplacement->nfc_uid)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Associé</span>
                            <span class="font-mono text-xs text-gray-500">{{ $emplacement->nfc_uid }}</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Non associé</span>
                        @endif
                    </div>
                    @if($emplacement->latitude && $emplacement->longitude)
                        <div><strong>Coordonnées GPS :</strong> {{ $emplacement->latitude }}, {{ $emplacement->longitude }}</div>
                    @endif
                </div>

                @if($emplacement->description)
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <strong class="block mb-1">Description :</strong>
                        <p class="text-gray-600 dark:text-gray-400 whitespace-pre-wrap">{{ $emplacement->description }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-semibold mb-4">Historique des interventions ({{ $emplacement->interventions->count() }})</h3>

                @if($emplacement->interventions->isEmpty())
                    <p class="text-sm text-gray-400">Aucune intervention réalisée à cet emplacement.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 uppercase">
                                    <th>Code</th>
                                    <th>Type</th>
                                    <th>Technicien</th>
                                    <th>Statut</th>
                                    <th>Date prévue</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($emplacement->interventions as $intervention)
                                    @include('chantiers.partials.intervention-ligne')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
