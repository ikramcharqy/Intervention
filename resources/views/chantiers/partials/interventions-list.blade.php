<div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
    <h3 class="text-lg font-semibold mb-4">Interventions ({{ $chantier->interventions->count() }})</h3>

    @if($chantier->interventions->isEmpty())
        <p class="text-sm text-gray-400">Aucune intervention enregistrée pour ce chantier.</p>
    @elseif($chantier->emplacements->isNotEmpty())
        {{-- Chantier avec emplacements définis : interventions groupées par emplacement --}}
        @php $groupes = $chantier->interventions->groupBy('emplacement_id'); @endphp

        @foreach($chantier->emplacements as $emplacement)
            @continue(!$groupes->has($emplacement->id))
            <div class="mb-6">
                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">{{ $emplacement->nom }}</h4>
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
                            @foreach($groupes->get($emplacement->id) as $intervention)
                                @include('chantiers.partials.intervention-ligne')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        @if($groupes->has(null))
            <div class="mb-2">
                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Sans emplacement précis</h4>
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
                            @foreach($groupes->get(null) as $intervention)
                                @include('chantiers.partials.intervention-ligne')
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @else
        {{-- Chantier sans emplacement (site simple) : liste plate --}}
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
                    @foreach($chantier->interventions as $intervention)
                        @include('chantiers.partials.intervention-ligne')
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
