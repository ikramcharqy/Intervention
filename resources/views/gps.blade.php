<x-app-layout>
    <x-slot name="header">
        Tracking GPS Techniciens en Temps Réel
    </x-slot>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @php
        $etapes = [
            \App\Models\Intervention::STATUT_PLANIFIEE => ['Planifiée', 1],
            \App\Models\Intervention::STATUT_AFFECTEE   => ['Affectée', 2],
            \App\Models\Intervention::STATUT_ACCEPTEE   => ['Acceptée', 3],
            \App\Models\Intervention::STATUT_EN_COURS   => ['En cours', 4],
            \App\Models\Intervention::STATUT_TERMINEE   => ['Terminée', 5],
        ];

        $techniciensList = \App\Models\User::role('technicien')->with(['interventions' => function($q) {
            $q->whereHas('gpsTrackingSessions')->with('gpsTrackingSessions.points');
        }])->get();

        $interventionsGpsAll = \App\Models\Intervention::with(['technicien', 'chantier.client', 'gpsTrackingSessions.points'])
            ->whereHas('gpsTrackingSessions')
            ->orderBy('created_at', 'desc')
            ->get();

        $earthRadius = 6371000;
        $haversine = function ($lat1, $lon1, $lat2, $lon2) use ($earthRadius) {
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
            return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
        };

        $itemsData = $interventionsGpsAll->map(function ($item) use ($haversine, $etapes) {
            $pointsTries = $item->gpsTrackingSessions->flatMap->points->sortBy('captured_at')->values();
            $dernierPoint = $pointsTries->last();
            $avantDernierPoint = $pointsTries->count() > 1 ? $pointsTries[$pointsTries->count() - 2] : null;

            $vitesseKmh = null;
            if ($dernierPoint && $avantDernierPoint) {
                $secondes = $avantDernierPoint->captured_at->diffInSeconds($dernierPoint->captured_at, false);
                if ($secondes > 0) {
                    $distanceSegment = $haversine(
                        (float) $avantDernierPoint->latitude, (float) $avantDernierPoint->longitude,
                        (float) $dernierPoint->latitude, (float) $dernierPoint->longitude
                    );
                    $vitesseKmh = ($distanceSegment / $secondes) * 3.6;
                }
            }

            $distanceRestanteM = null;
            $etaMinutes = null;
            if ($dernierPoint && $item->chantier && $item->chantier->latitude && $item->chantier->longitude) {
                $distanceRestanteM = $haversine(
                    (float) $dernierPoint->latitude, (float) $dernierPoint->longitude,
                    (float) $item->chantier->latitude, (float) $item->chantier->longitude
                );
                if ($vitesseKmh && $vitesseKmh > 1) {
                    $etaMinutes = round(($distanceRestanteM / 1000) / $vitesseKmh * 60);
                }
            }

            $distanceParcourueM = (float) $item->gpsTrackingSessions->sum('distance_metres');

            return [
                'item' => $item,
                'dernierPoint' => $dernierPoint,
                'vitesseKmh' => $vitesseKmh,
                'distanceRestanteM' => $distanceRestanteM,
                'etaMinutes' => $etaMinutes,
                'distanceParcourueM' => $distanceParcourueM,
                'etapeActuelle' => $etapes[$item->statut][1] ?? null,
            ];
        });

        $itemsInitialJs = $itemsData->map(function ($d) {
            $item = $d['item'];
            $pts = $item->gpsTrackingSessions->flatMap->points->sortBy('captured_at')->values();
            $destination = null;
            if ($item->chantier && $item->chantier->latitude && $item->chantier->longitude) {
                $destination = [
                    'lat' => (float) $item->chantier->latitude,
                    'lng' => (float) $item->chantier->longitude,
                    'nom' => $item->chantier->nom,
                ];
            }
            return [
                'id' => $item->id,
                'code' => $item->code_intervention,
                'tech_id' => (string) $item->technicien_id,
                'tech' => $item->technicien->name ?? 'N/A',
                'url' => route('interventions.show', $item),
                'destination' => $destination,
                'pts' => $pts->map(fn ($p) => ['lat' => (float) $p->latitude, 'lng' => (float) $p->longitude])->toArray(),
            ];
        })->values();
    @endphp

    <div class="space-y-6" x-data="{ selectedTechId: 'all' }">
        <!-- Hero -->
        <div class="ds-card-elevated p-7 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6" style="border-left: 4px solid #1E5EFF;">
            <div class="max-w-2xl space-y-2">
                <div id="ws-status-badge" class="ds-badge ds-badge-md ds-badge-light-warning inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" style="background-color:#F99600;"></span>
                    <span id="ws-status-label">Connexion au flux temps réel...</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Poste de Contrôle & Tracking GPS</h2>
                <p class="text-[#5A607F] text-xs sm:text-sm leading-relaxed">
                    Suivez la position exacte de chaque technicien sur le terrain, leurs trajets parcourus, et filtrez les cartes en temps réel par intervenant.
                </p>
            </div>
        </div>

        <!-- Sélecteur de Technicien -->
        <div class="ds-card-elevated p-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="text-xs font-bold uppercase tracking-wider text-[#A1A7C4] flex items-center gap-1.5">
                    <i class="ti ti-users" style="color:#1E5EFF;"></i>
                    Sélectionner un Technicien :
                </span>
                <div class="flex flex-wrap gap-2">
                    <button @click="selectedTechId = 'all'"
                            :class="selectedTechId === 'all' ? 'text-white' : 'text-[#5A607F]'"
                            :style="selectedTechId === 'all' ? 'background-color:#1E5EFF' : 'background-color:#F1F4FA'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                        Tous les Techniciens ({{ $interventionsGpsAll->count() }})
                    </button>
                    @foreach($techniciensList as $tech)
                        @php
                            $techIntCount = $interventionsGpsAll->where('technicien_id', $tech->id)->count();
                        @endphp
                        <button @click="selectedTechId = '{{ $tech->id }}'"
                                :class="selectedTechId === '{{ $tech->id }}' ? 'text-white' : 'text-[#5A607F]'"
                                :style="selectedTechId === '{{ $tech->id }}' ? 'background-color:#1E5EFF' : 'background-color:#F1F4FA'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                            <span class="tech-live-dot w-2 h-2 rounded-full" style="background-color:#D7DBEC;" data-tech-id="{{ $tech->id }}"></span>
                            {{ $tech->name }} ({{ $techIntCount }})
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Liste des interventions -->
            <div class="ds-card-elevated p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-[#131523] mb-4 text-xs uppercase tracking-wider flex items-center justify-between">
                        <span>Interventions Suivies</span>
                        <span class="ds-badge ds-badge-sm ds-badge-light-primary">Temps Réel</span>
                    </h4>

                    <div class="space-y-3 max-h-[640px] overflow-y-auto pr-1">
                        @forelse($itemsData as $data)
                            @php
                                $item = $data['item'];
                                $statutTheme = match(true) {
                                    $item->statut === \App\Models\Intervention::STATUT_TERMINEE => 'success',
                                    $item->statut === \App\Models\Intervention::STATUT_EN_COURS => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <div x-show="selectedTechId === 'all' || selectedTechId === '{{ $item->technicien_id }}'"
                                 id="intervention-card-{{ $item->id }}"
                                 class="ds-card p-4 text-xs transition hover:shadow-md">
                                <div class="flex justify-between items-center font-bold mb-2">
                                    <span class="text-sm" style="color:#1E5EFF;">#{{ $item->code_intervention }}</span>
                                    <span class="ds-badge ds-badge-sm ds-badge-light-{{ $statutTheme }}">
                                        {{ $item->statut }}
                                    </span>
                                </div>

                                <!-- Timeline des statuts réels -->
                                @if($data['etapeActuelle'])
                                    <div class="flex items-center gap-1 mb-3">
                                        @foreach($etapes as $label => $step)
                                            <div class="flex-1 h-1.5 rounded-full" style="background-color: {{ $step[1] <= $data['etapeActuelle'] ? '#1E5EFF' : '#E6E9F4' }};" title="{{ $step[0] }}"></div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded-full text-white flex items-center justify-center font-bold text-[10px] shrink-0" style="background-color:#1E5EFF;">
                                        {{ strtoupper(substr($item->technicien->name ?? '?', 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-[#131523] truncate">{{ $item->technicien->name ?? 'Technicien N/A' }}</p>
                                        <p class="text-[11px] text-[#A1A7C4] truncate">Chantier: {{ $item->chantier->nom ?? 'Standard' }}</p>
                                    </div>
                                    @if($item->technicien->telephone ?? null)
                                        <a href="tel:{{ $item->technicien->telephone }}" class="ml-auto shrink-0 w-7 h-7 rounded-full flex items-center justify-center transition" style="background-color:#C4F8E2; color:#06A561;">
                                            <i class="ti ti-phone text-sm"></i>
                                        </a>
                                    @endif
                                </div>

                                <div class="grid grid-cols-2 gap-2 mb-3">
                                    <div class="rounded-lg p-2 border" style="background-color:#F1F4FA; border-color:#E6E9F4;">
                                        <p class="text-[9px] uppercase font-bold flex items-center gap-1" style="color:#A1A7C4;"><i class="ti ti-route"></i> Parcourue</p>
                                        <p class="font-bold text-[#131523] gps-distance-parcourue" data-item-id="{{ $item->id }}">{{ number_format($data['distanceParcourueM'] / 1000, 2) }} km</p>
                                    </div>
                                    <div class="rounded-lg p-2 border" style="background-color:#F1F4FA; border-color:#E6E9F4;">
                                        <p class="text-[9px] uppercase font-bold flex items-center gap-1" style="color:#A1A7C4;"><i class="ti ti-clock"></i> ETA</p>
                                        <p class="font-bold text-[#131523] gps-eta" data-item-id="{{ $item->id }}">
                                            {{ $data['etaMinutes'] !== null ? $data['etaMinutes'] . ' min' : (($item->chantier->latitude ?? null) ? 'Calcul...' : 'N/A') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="pt-2 border-t flex items-center justify-between" style="border-color:#E6E9F4;">
                                    <span class="text-[10px] gps-last-update" style="color:#A1A7C4;" data-item-id="{{ $item->id }}" data-captured-at="{{ $data['dernierPoint']?->captured_at?->toIso8601String() }}">
                                        {{ $data['dernierPoint'] ? $data['dernierPoint']->captured_at->diffForHumans() : 'N/A' }}
                                    </span>
                                    <a href="{{ route('interventions.show', $item) }}" class="text-[11px] font-bold transition flex items-center gap-1" style="color:#1E5EFF;">
                                        Voir Détails
                                        <i class="ti ti-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-14">
                                <i class="ti ti-map-pin-off text-3xl mb-3 block" style="color:#D7DBEC;"></i>
                                <p class="text-sm font-medium" style="color:#A1A7C4;">
                                    Aucune intervention avec tracking GPS actif. Le suivi démarre automatiquement lorsqu'un technicien lance une intervention en mode GPS depuis l'application mobile.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Carte -->
            <div class="lg:col-span-2 space-y-4">
                <div class="ds-card-elevated overflow-hidden">
                    <div class="border-b px-6 py-4 flex items-center justify-between" style="border-color:#E6E9F4;">
                        <div>
                            <h3 class="text-[16px] font-bold text-[#131523] flex items-center gap-2">
                                <i class="ti ti-map-pin text-lg" style="color:#1E5EFF;"></i>
                                Cartographie GPS Dédiée & Trajet Actif
                            </h3>
                            <p class="text-[11px] text-[#A1A7C4]" id="carte-update-label">Mise à jour en temps réel des coordonnées et du parcours</p>
                        </div>
                        <span class="ds-badge ds-badge-sm ds-badge-light-primary">Leaflet GIS</span>
                    </div>

                    <div class="p-4" style="background-color:#F5F6FA;">
                        <div id="carte-trajet-gps" class="w-full h-[520px] rounded-xl overflow-hidden border shadow-inner z-10" style="border-color:#E6E9F4;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const itemsInitial = @json($itemsInitialJs);

        document.addEventListener("DOMContentLoaded", function() {
            if (!itemsInitial || itemsInitial.length === 0) {
                document.getElementById('carte-trajet-gps').innerHTML = '<div class="w-full h-full flex items-center justify-center text-xs" style="color:#A1A7C4;">Aucune position GPS à afficher pour le moment.</div>';
                setupWebSocket();
                return;
            }

            let map = L.map('carte-trajet-gps').setView([itemsInitial[0].pts[0]?.lat ?? 33.5731, itemsInitial[0].pts[0]?.lng ?? -7.5898], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let layerGroup = L.layerGroup().addTo(map);
            const polylines = {};
            const markers = {};
            const lastUpdateAt = {};

            function techInitials(name) {
                return (name || '?').trim().split(/\s+/).map(w => w[0]).join('').substring(0, 2).toUpperCase();
            }

            function techDivIcon(name) {
                return L.divIcon({
                    className: '',
                    html: `<div style="width:34px;height:34px;border-radius:9999px;background:#F0142F;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);">${techInitials(name)}</div>`,
                    iconSize: [34, 34],
                    iconAnchor: [17, 17],
                });
            }

            function destDivIcon() {
                return L.divIcon({
                    className: '',
                    html: `<div style="width:26px;height:26px;border-radius:6px;background:#1E5EFF;color:#fff;display:flex;align-items:center;justify-content:center;border:2px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"><i class="ti ti-building" style="font-size:13px;"></i></div>`,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13],
                });
            }

            function renderTrajet(trajet) {
                const latLngs = trajet.pts.map(p => [p.lat, p.lng]);
                if (latLngs.length === 0) return;

                polylines[trajet.id] = L.polyline(latLngs, {
                    color: '#1E5EFF', weight: 6, opacity: 0.85, lineCap: 'round'
                }).addTo(layerGroup);

                const endPt = latLngs[latLngs.length - 1];
                markers[trajet.id] = L.marker(endPt, { icon: techDivIcon(trajet.tech) }).addTo(layerGroup);
                markers[trajet.id].bindPopup(popupHtml(trajet, endPt));
                lastUpdateAt[trajet.id] = Date.now();

                if (trajet.destination) {
                    L.marker([trajet.destination.lat, trajet.destination.lng], { icon: destDivIcon() })
                        .addTo(layerGroup)
                        .bindPopup(`<b>${trajet.destination.nom ?? 'Chantier'}</b><br>Destination`);
                }
            }

            function popupHtml(trajet, pt) {
                const secsAgo = Math.max(0, Math.round((Date.now() - (lastUpdateAt[trajet.id] || Date.now())) / 1000));
                return `<div class="p-1 font-sans">
                    <b style="color:#1E5EFF;">${trajet.tech}</b><br>
                    Intervention #${trajet.code}<br>
                    <span class="text-xs" style="color:#A1A7C4;">Mis à jour il y a ${secsAgo}s</span><br>
                    <a href="${trajet.url}" class="text-xs font-bold hover:underline" style="color:#1E5EFF;">Voir Détails Intervention</a>
                </div>`;
            }

            function renderMapForTech(techId) {
                layerGroup.clearLayers();
                Object.keys(polylines).forEach(k => delete polylines[k]);
                Object.keys(markers).forEach(k => delete markers[k]);

                const filtered = (techId === 'all') ? itemsInitial : itemsInitial.filter(d => d.tech_id === techId);
                if (filtered.length === 0) return;

                const bounds = L.latLngBounds();
                filtered.forEach(trajet => {
                    renderTrajet(trajet);
                    trajet.pts.forEach(p => bounds.extend([p.lat, p.lng]));
                    if (trajet.destination) bounds.extend([trajet.destination.lat, trajet.destination.lng]);
                });

                if (bounds.isValid()) map.fitBounds(bounds, { padding: [50, 50] });
            }

            renderMapForTech('all');

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

            // Refresh "mis à jour il y a Xs" every second
            setInterval(() => {
                document.querySelectorAll('.gps-last-update').forEach(el => {
                    const iso = el.dataset.capturedAt;
                    if (!iso) return;
                    const secs = Math.max(0, Math.round((Date.now() - new Date(iso).getTime()) / 1000));
                    el.textContent = secs < 60 ? `Il y a ${secs}s` : `Il y a ${Math.floor(secs / 60)} min`;
                });
            }, 1000);

            function applyPositionUpdate(data) {
                const trajet = itemsInitial.find(t => t.id === data.intervention_id);
                if (!trajet) return;

                trajet.pts.push({ lat: data.latitude, lng: data.longitude });
                lastUpdateAt[data.intervention_id] = Date.now();

                if (polylines[data.intervention_id]) {
                    polylines[data.intervention_id].addLatLng([data.latitude, data.longitude]);
                }
                if (markers[data.intervention_id]) {
                    markers[data.intervention_id].setLatLng([data.latitude, data.longitude]);
                    markers[data.intervention_id].setPopupContent(popupHtml(trajet, [data.latitude, data.longitude]));
                }

                const distEl = document.querySelector(`.gps-distance-parcourue[data-item-id="${data.intervention_id}"]`);
                if (distEl) distEl.textContent = (data.distance_metres / 1000).toFixed(2) + ' km';

                const lastUpdateEl = document.querySelector(`.gps-last-update[data-item-id="${data.intervention_id}"]`);
                if (lastUpdateEl) lastUpdateEl.dataset.capturedAt = data.captured_at;
            }

            window.__gpsApplyPositionUpdate = applyPositionUpdate;
            setupWebSocket();
        });

        function setWsStatus(state) {
            const badge = document.getElementById('ws-status-badge');
            const label = document.getElementById('ws-status-label');
            if (!badge || !label) return;
            const dot = badge.querySelector('span');
            if (state === 'connected') {
                badge.className = 'ds-badge ds-badge-md ds-badge-light-success inline-flex items-center gap-2';
                dot.style.backgroundColor = '#1FD286';
                dot.className = 'w-2 h-2 rounded-full animate-pulse';
                label.textContent = 'Système de Géolocalisation en Direct — Connecté';
            } else if (state === 'lost') {
                badge.className = 'ds-badge ds-badge-md ds-badge-light-danger inline-flex items-center gap-2';
                dot.style.backgroundColor = '#F0142F';
                dot.className = 'w-2 h-2 rounded-full';
                label.textContent = 'Connexion perdue — les positions ne sont plus actualisées';
            } else {
                badge.className = 'ds-badge ds-badge-md ds-badge-light-warning inline-flex items-center gap-2';
                dot.style.backgroundColor = '#F99600';
                dot.className = 'w-2 h-2 rounded-full animate-pulse';
                label.textContent = 'Connexion au flux temps réel...';
            }
        }

        function setupWebSocket() {
            if (typeof window.initEcho !== 'function') {
                setWsStatus('lost');
                return;
            }

            const echo = window.initEcho();

            echo.connector.pusher.connection.bind('connected', () => setWsStatus('connected'));
            echo.connector.pusher.connection.bind('unavailable', () => setWsStatus('lost'));
            echo.connector.pusher.connection.bind('failed', () => setWsStatus('lost'));
            echo.connector.pusher.connection.bind('disconnected', () => setWsStatus('lost'));

            itemsInitial.forEach(trajet => {
                echo.private(`intervention.${trajet.id}.gps`).listen('.position.updated', (data) => {
                    document.querySelectorAll('.tech-live-dot[data-tech-id="' + data.technicien_id + '"]').forEach(dot => {
                        dot.style.backgroundColor = '#1FD286';
                    });
                    if (window.__gpsApplyPositionUpdate) window.__gpsApplyPositionUpdate(data);
                });
            });
        }
    </script>
</x-app-layout>
