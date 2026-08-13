<x-app-layout>
    <x-slot name="header">
        Tracking GPS Techniciens en Temps Réel
    </x-slot>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @php
        $techniciensList = \App\Models\User::role('technicien')->with(['interventions' => function($q) {
            $q->whereHas('gpsTrackingSessions')->with('gpsTrackingSessions.points');
        }])->get();

        $interventionsGpsAll = \App\Models\Intervention::with(['technicien', 'chantier.client', 'gpsTrackingSessions.points'])
            ->whereHas('gpsTrackingSessions')
            ->orderBy('created_at', 'desc')
            ->get();
    @endphp

    <div class="space-y-6" x-data="{ selectedTechId: 'all' }">
        <!-- Banner header & action -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl shadow-xl p-6 border border-slate-800 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-indigo-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Système de Géolocalisation V3 & Télémétrie en Direct
                </div>
                <h3 class="text-2xl font-black text-white tracking-tight">Poste de Contrôle & Tracking GPS</h3>
                <p class="text-xs text-slate-300 max-w-2xl">
                    Suivez la position exacte de chaque technicien sur le terrain, leurs trajets parcourus, et filtrez les cartes en temps réel par intervenant.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <form id="gps-quick-seed-form" action="{{ route('interventions.quickGpsSeed') }}" method="POST">
                    @csrf
                    <input type="hidden" name="latitude" id="gps-lat" value="33.5731">
                    <input type="hidden" name="longitude" id="gps-lng" value="-7.5898">
                    <button type="button" onclick="declencherQuickGpsSeed()" class="px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-bold rounded-xl shadow-lg transition flex items-center gap-2 transform hover:-translate-y-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        ⚡ Simuler / Tracker Ma Position Actuelle
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 rounded-xl text-xs font-bold flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {!! session('success') !!}
            </div>
        @endif

        <!-- Selecteur de Technicien (Filtre dynamique) -->
        <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Sélectionner un Technicien :
                </span>
                <div class="flex flex-wrap gap-2">
                    <button @click="selectedTechId = 'all'" 
                            :class="selectedTechId === 'all' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                        Tous les Techniciens ({{ $interventionsGpsAll->count() }})
                    </button>
                    @foreach($techniciensList as $tech)
                        @php
                            $techIntCount = $interventionsGpsAll->where('technicien_id', $tech->id)->count();
                        @endphp
                        <button @click="selectedTechId = '{{ $tech->id }}'" 
                                :class="selectedTechId === '{{ $tech->id }}' ? 'bg-indigo-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200'"
                                class="px-3 py-1.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full {{ $techIntCount > 0 ? 'bg-emerald-400' : 'bg-gray-400' }}"></span>
                            {{ $tech->name }} ({{ $techIntCount }})
                        </button>
                    @endforeach
                </div>
            </div>
            <div class="text-xs font-semibold text-gray-400">
                Cartographie isolée & indépendante
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Liste des interventions avec sélection & actions directes -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-900 dark:text-white mb-4 text-xs uppercase tracking-wider flex items-center justify-between">
                        <span>Interventions Suivies</span>
                        <span class="text-[10px] text-indigo-500 font-bold bg-indigo-50 dark:bg-indigo-950/40 px-2 py-0.5 rounded-full">Temps Réel</span>
                    </h4>
                    
                    <div class="space-y-3 max-h-[500px] overflow-y-auto pr-1">
                        @forelse($interventionsGpsAll as $item)
                            @php
                                $dernierPoint = $item->gpsTrackingSessions->flatMap->points->sortByDesc('captured_at')->first();
                            @endphp
                            <div x-show="selectedTechId === 'all' || selectedTechId === '{{ $item->technicien_id }}'"
                                 class="p-4 bg-slate-50 dark:bg-gray-800/60 hover:bg-white dark:hover:bg-gray-800 transition rounded-xl border border-gray-200 dark:border-gray-700/80 text-xs shadow-sm hover:shadow-md">
                                <div class="flex justify-between items-center font-bold mb-2">
                                    <span class="text-indigo-600 dark:text-indigo-400 text-sm">#{{ $item->code_intervention }}</span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                        {{ $item->statut }}
                                    </span>
                                </div>
                                <div class="space-y-1 text-gray-600 dark:text-gray-300 mb-3">
                                    <p class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $item->technicien->name ?? 'Technicien N/A' }}
                                    </p>
                                    <p class="text-[11px] text-gray-500">Chantier: {{ $item->chantier->nom ?? 'Standard' }}</p>
                                    @if($dernierPoint)
                                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono">
                                            GPS: {{ number_format($dernierPoint->latitude, 5) }}, {{ number_format($dernierPoint->longitude, 5) }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="pt-2 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                    <span class="text-[10px] text-gray-400">
                                        {{ $dernierPoint ? $dernierPoint->captured_at->format('H:i:s') : 'N/A' }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('interventions.show', $item) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[11px] font-bold transition flex items-center gap-1 shadow-sm">
                                            <span>Voir Détails</span>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-xs py-8 text-center bg-gray-50 dark:bg-gray-800/20 rounded-xl">
                                Aucune intervention avec tracking GPS enregistré.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Carte OpenStreetMap Interactive & Dynamique -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="border-b border-gray-200 dark:border-gray-800 px-6 py-4 bg-slate-50 dark:bg-gray-900/80 flex items-center justify-between">
                        <div>
                            <h3 class="font-black text-gray-900 dark:text-white flex items-center gap-2 text-base">
                                <svg class="h-5 w-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                                Cartographie GPS Dédiée & Trajet Actif
                            </h3>
                            <p class="text-[11px] text-gray-500">Mise à jour en temps réel des coordonnées et du parcours</p>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 rounded-lg">Leaflet GIS Telemetry</span>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-950">
                        @php
                            $pointsCartographie = [];
                            foreach($interventionsGpsAll as $item) {
                                $pts = $item->gpsTrackingSessions->flatMap->points->sortBy('captured_at');
                                if($pts->isNotEmpty()){
                                    $pointsCartographie[] = [
                                        'code' => $item->code_intervention,
                                        'tech_id' => (string) $item->technicien_id,
                                        'tech' => $item->technicien->name ?? 'N/A',
                                        'url' => route('interventions.show', $item),
                                        'pts' => $pts->map(fn($p) => ['lat' => (float)$p->latitude, 'lng' => (float)$p->longitude])->toArray()
                                    ];
                                }
                            }
                        @endphp

                        <div id="carte-trajet-gps" class="w-full h-[520px] rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-inner z-10"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function declencherQuickGpsSeed() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        document.getElementById('gps-lat').value = position.coords.latitude;
                        document.getElementById('gps-lng').value = position.coords.longitude;
                        document.getElementById('gps-quick-seed-form').submit();
                    },
                    (error) => {
                        document.getElementById('gps-quick-seed-form').submit();
                    }
                );
            } else {
                document.getElementById('gps-quick-seed-form').submit();
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const allData = @json($pointsCartographie);
            if (!allData || allData.length === 0) return;

            let map = L.map('carte-trajet-gps').setView([allData[0].pts[0].lat, allData[0].pts[0].lng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let layerGroup = L.layerGroup().addTo(map);

            function renderMapForTech(techId) {
                layerGroup.clearLayers();

                const filtered = (techId === 'all') 
                    ? allData 
                    : allData.filter(d => d.tech_id === techId);

                if (filtered.length === 0) {
                    return;
                }

                const bounds = L.latLngBounds();

                filtered.forEach(function (trajet) {
                    const latLngs = trajet.pts.map(p => [p.lat, p.lng]);
                    latLngs.forEach(pt => bounds.extend(pt));

                    // Trace la ligne de trajet
                    L.polyline(latLngs, {
                        color: '#4F46E5',
                        weight: 6,
                        opacity: 0.85,
                        lineCap: 'round'
                    }).addTo(layerGroup);

                    const startPt = latLngs[0];
                    const endPt = latLngs[latLngs.length - 1];

                    // Point de Départ
                    L.circleMarker(startPt, {
                        radius: 8,
                        fillColor: '#10B981',
                        color: '#FFFFFF',
                        weight: 2,
                        fillOpacity: 1
                    }).addTo(layerGroup).bindPopup(
                        `<div class="p-1 font-sans">
                            <b class="text-indigo-600">Départ #${trajet.code}</b><br>
                            Technicien: ${trajet.tech}<br>
                            <a href="${trajet.url}" class="text-xs font-bold text-indigo-600 hover:underline">Voir Détails Intervention</a>
                        </div>`
                    );

                    // Point Actuel (Rouge + Effet pulse)
                    L.circleMarker(endPt, {
                        radius: 10,
                        fillColor: '#EF4444',
                        color: '#FFFFFF',
                        weight: 3,
                        fillOpacity: 1
                    }).addTo(layerGroup).bindPopup(
                        `<div class="p-1 font-sans">
                            <b class="text-red-600">Position En Direct #${trajet.code}</b><br>
                            Technicien: ${trajet.tech}<br>
                            <a href="${trajet.url}" class="inline-block mt-2 px-2 py-1 bg-indigo-600 text-white rounded text-xs font-bold">Voir Détails</a>
                        </div>`
                    );
                });

                map.fitBounds(bounds, { padding: [50, 50] });
            }

            // Render initial
            renderMapForTech('all');

            // Écouter le changement AlpineJS
            document.addEventListener('alpine:initialized', () => {
                Alpine.effect(() => {
                    const data = Alpine.raw(Alpine.store('selectedTechId')) || document.querySelector('[x-data]').__x.$data.selectedTechId;
                    renderMapForTech(data);
                });
            });

            // Fallback interval listener for x-data variable
            let currentSelected = 'all';
            setInterval(() => {
                const el = document.querySelector('[x-data]');
                if (el && el.__x) {
                    const val = el.__x.$data.selectedTechId;
                    if (val !== currentSelected) {
                        currentSelected = val;
                        renderMapForTech(val);
                    }
                }
            }, 300);
        });
    </script>
</x-app-layout>
