<x-app-layout>
    <x-slot name="header">
        Tracking GPS Global
    </x-slot>

    <div class="space-y-6">
        <!-- Message informatif -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Suivi Cartographique en Temps Réel</h3>
            <p class="text-sm text-gray-500">Visualisez les trajets réels effectués par les techniciens lors des interventions actives.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Liste des interventions géolocalisées réelles -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-4 text-sm uppercase tracking-wider">Interventions Géolocalisées</h4>
                    @php
                        $interventionsGps = \App\Models\Intervention::with(['technicien', 'gpsTrackingSessions.points'])
                            ->whereHas('gpsTrackingSessions')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp
                    <div class="space-y-3">
                        @forelse($interventionsGps as $item)
                            @php
                                $dernierPoint = $item->gpsTrackingSessions->flatMap->points->sortByDesc('captured_at')->first();
                            @endphp
                            <div class="p-3 bg-gray-50 dark:bg-gray-800/40 rounded-xl border border-gray-100 dark:border-gray-800 text-xs">
                                <div class="flex justify-between font-semibold mb-1">
                                    <span class="text-indigo-650 dark:text-indigo-400">#{{ $item->code_intervention }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/20 dark:text-indigo-400">
                                        {{ $item->statut }}
                                    </span>
                                </div>
                                <p class="text-gray-500 font-medium">Technicien: {{ $item->technicien->name ?? 'N/A' }}</p>
                                @if($dernierPoint)
                                    <div class="text-[10px] text-gray-400 mt-2 flex justify-between">
                                        <span>Capturé le: {{ $dernierPoint->captured_at->format('d/m H:i') }}</span>
                                        <a href="{{ route('interventions.show', $item) }}" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Voir détails</a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs py-4 text-center">Aucune intervention avec données GPS disponible.</p>
                        @endforelse
                    </div>
                </div>
                <div class="text-xs text-gray-400 pt-4 border-t border-gray-100 dark:border-gray-800 text-center">
                    Système de télémétrie GPS actif
                </div>
            </div>

            <!-- Carte Google Maps -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="border-b border-gray-150 dark:border-gray-800 px-6 py-4 bg-gray-50 dark:bg-gray-900/50">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2 text-base">
                        <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Cartographie des points GPS réels
                    </h3>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-950">
                    @php
                        $pointsCartographie = [];
                        foreach($interventionsGps as $item) {
                            $pts = $item->gpsTrackingSessions->flatMap->points->sortBy('captured_at');
                            if($pts->isNotEmpty()){
                                $pointsCartographie[] = [
                                    'code' => $item->code_intervention,
                                    'tech' => $item->technicien->name ?? 'N/A',
                                    'pts' => $pts->map(fn($p) => ['lat' => (float)$p.latitude, 'lng' => (float)$p.longitude])->toArray()
                                ];
                            }
                        }
                    @endphp
                    @if (count($pointsCartographie) > 0)
                        <div id="carte-trajet-gps" class="w-full h-80 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800"></div>
                        <script>
                            const pointsGPSReels = @json($pointsCartographie);
                            function initCarteTrajetGps() {
                                if (pointsGPSReels.length === 0) return;
                                
                                const premiereCoord = pointsGPSReels[0].pts[0];
                                const carte = new google.maps.Map(document.getElementById('carte-trajet-gps'), {
                                    zoom: 14,
                                    center: premiereCoord,
                                });

                                const limites = new google.maps.LatLngBounds();

                                pointsGPSReels.forEach(function (trajet) {
                                    const pathCoords = trajet.pts.map(p => ({ lat: p.lat, lng: p.lng }));
                                    pathCoords.forEach(p => limites.extend(p));

                                    // Tracer la ligne du trajet
                                    new google.maps.Polyline({
                                        path: pathCoords,
                                        geodesic: true,
                                        strokeColor: '#4F46E5',
                                        strokeOpacity: 0.9,
                                        strokeWeight: 4,
                                        map: carte
                                    });

                                    // Marqueur de début
                                    new google.maps.Marker({
                                        position: pathCoords[0],
                                        map: carte,
                                        label: 'D',
                                        title: 'Départ ' + trajet.code + ' (' + trajet.tech + ')'
                                    });

                                    // Marqueur de fin
                                    new google.maps.Marker({
                                        position: pathCoords[pathCoords.length - 1],
                                        map: carte,
                                        label: 'A',
                                        title: 'Dernière position ' + trajet.code + ' (' + trajet.tech + ')'
                                    });
                                });

                                carte.fitBounds(limites);
                            }
                        </script>
                        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&callback=initCarteTrajetGps" async defer></script>
                    @else
                        <div class="w-full h-80 rounded-xl border border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center text-gray-500 bg-white dark:bg-gray-900">
                            <svg class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /></svg>
                            <p class="text-sm">Aucune donnée GPS réelle à afficher sur la carte.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
