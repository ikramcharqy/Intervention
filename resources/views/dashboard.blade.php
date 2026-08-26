<x-app-layout>
    <x-slot name="header">
        {{ __('Tableau de Bord Opérationnel') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Hero Banner Opérationnel -->
        <div class="ui-card p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-indigo-950 text-white border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-md rounded-2xl">
            <div class="z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-indigo-500/20 border border-indigo-400/30 text-indigo-300 text-xs font-semibold mb-2.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Centre de Pilotage Opérationnel</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Bonjour, {{ Auth::user()->name }}
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 leading-relaxed">
                    Supervision en temps réel des techniciens, planification des interventions et suivi des rapports.
                </p>
            </div>
            
            <div class="flex items-center gap-3 z-10 flex-wrap shrink-0">
                <a href="{{ route('interventions.create') }}" class="ui-btn ui-btn-primary text-xs py-2.5 px-4 shadow-sm">
                    <i class="fas fa-plus"></i>
                    <span>Planifier Intervention</span>
                </a>

                <form id="admin-gps-seed-form" action="{{ route('interventions.quickGpsSeed') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="latitude" id="admin-gps-lat" value="33.5731">
                    <input type="hidden" name="longitude" id="admin-gps-lng" value="-7.5898">
                    <button type="button" onclick="declencherAdminGpsSeed()" class="ui-btn ui-btn-secondary text-xs py-2.5 px-4">
                        <i class="fas fa-location-crosshairs text-emerald-600"></i>
                        <span>Intervention GPS Direct</span>
                    </button>
                </form>
            </div>
            <script>
                function declencherAdminGpsSeed() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                document.getElementById('admin-gps-lat').value = position.coords.latitude;
                                document.getElementById('admin-gps-lng').value = position.coords.longitude;
                                document.getElementById('admin-gps-seed-form').submit();
                            },
                            (error) => {
                                document.getElementById('admin-gps-seed-form').submit();
                            }
                        );
                    } else {
                        document.getElementById('admin-gps-seed-form').submit();
                    }
                }
            </script>
        </div>

        <!-- KPIs Cards Grid 1 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <x-soft-kpi-card 
                title="Total Interventions" 
                value="{{ $stats['interventions'] }}" 
                subtitle="Volume d'activité globale"
                icon="fas fa-clipboard-list"
                gradient="primary"
            />

            <x-soft-kpi-card 
                title="En cours / Sur Site" 
                value="{{ $stats['en_cours'] }}" 
                subtitle="Missions actives sur le terrain"
                icon="fas fa-spinner"
                gradient="warning"
                :badgeText="$stats['en_cours'] > 0 ? 'Actif' : ''"
                badgeType="warning"
            />

            <x-soft-kpi-card 
                title="Interventions Terminées" 
                value="{{ $stats['terminees'] }}" 
                subtitle="Comptes-rendus rédigés"
                icon="fas fa-check-circle"
                gradient="success"
            />

            <x-soft-kpi-card 
                title="En Attente Validation" 
                value="{{ $stats['en_attente'] }}" 
                subtitle="Rapports à approuver"
                icon="fas fa-hourglass-half"
                gradient="info"
            />
        </div>

        <!-- KPIs Cards Grid 2 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <x-soft-kpi-card 
                title="Clients Enregistrés" 
                value="{{ $stats['clients'] }}" 
                subtitle="Portefeuille actif"
                icon="fas fa-users"
                gradient="dark"
            />

            <x-soft-kpi-card 
                title="Chantiers Actifs" 
                value="{{ $stats['chantiers'] }}" 
                subtitle="Sites opérationnels"
                icon="fas fa-building"
                gradient="success"
            />

            <x-soft-kpi-card 
                title="Techniciens Terrain" 
                value="{{ $stats['techniciens'] }}" 
                subtitle="Équipes mobilisables"
                icon="fas fa-user-cog"
                gradient="primary"
            />

            <x-soft-kpi-card 
                title="Rapports d'Activité" 
                value="{{ $stats['rapports'] }}" 
                subtitle="Documents certifiés"
                icon="fas fa-file-pdf"
                gradient="info"
            />
        </div>

        <!-- Section Activité Récente & GPS Widget -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Activité Récente (Tableau) -->
            <div class="ui-card lg:col-span-2 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="ui-card-header bg-slate-50/70">
                        <div>
                            <h3 class="ui-card-title flex items-center gap-2 text-slate-900">
                                <i class="fas fa-clock text-indigo-600"></i>
                                <span>Dernières Interventions Créées</span>
                            </h3>
                            <p class="ui-card-subtitle">Flux chronologique des missions enregistrées</p>
                        </div>
                        <a href="{{ route('interventions.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition">
                            Voir tout →
                        </a>
                    </div>

                    @if($recentInterventions->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="ui-table">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Chantier / Client</th>
                                        <th>Technicien</th>
                                        <th>Priorité</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentInterventions as $intervention)
                                        <tr>
                                            <td>
                                                <a href="{{ route('interventions.show', $intervention) }}" class="inline-flex items-center gap-2 group">
                                                    <span class="font-mono font-bold text-xs text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 group-hover:bg-indigo-100 transition">
                                                        {{ $intervention->code_intervention }}
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="text-slate-900 font-semibold text-xs">{{ $intervention->chantier->nom ?? '-' }}</div>
                                                <div class="text-[11px] text-slate-500">{{ $intervention->chantier->client->nom ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">
                                                        {{ strtoupper(substr($intervention->technicien->name ?? 'NA', 0, 2)) }}
                                                    </div>
                                                    <span class="text-xs text-slate-700 font-medium">{{ $intervention->technicien->name ?? 'Non assigné' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $priMap = [
                                                        'Faible' => 'bg-slate-100 text-slate-600 border-slate-200',
                                                        'Normale' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                        'Haute' => 'bg-amber-50 text-amber-700 border-amber-200 font-semibold',
                                                        'Urgente' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold border {{ $priMap[$intervention->priorite] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                                    {{ $intervention->priorite }}
                                                </span>
                                            </td>
                                            <td>
                                                <x-soft-badge :status="$intervention->statut" />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-8 text-center text-slate-500 text-xs">
                            <i class="fas fa-clipboard-list text-2xl text-slate-400 mb-2 block"></i>
                            Aucune intervention enregistrée pour le moment.
                        </div>
                    @endif
                </div>
                
                <div class="p-3 bg-slate-50/70 border-t border-slate-100 text-right">
                    <a href="{{ route('interventions.create') }}" class="ui-btn ui-btn-primary text-xs py-1.5 px-3">
                        <i class="fas fa-plus"></i> Nouvelle Intervention
                    </a>
                </div>
            </div>

            <!-- Tracking GPS Direct Widget -->
            <div class="ui-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <h3 class="ui-card-title flex items-center gap-2 text-slate-900">
                            <i class="fas fa-map-marked-alt text-emerald-600"></i>
                            <span>Tracking GPS Live</span>
                        </h3>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px] font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Actif
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        Suivez en temps réel la localisation des interventions, les trajets des équipes terrain et l'historique des déplacements.
                    </p>

                    <div class="p-4 rounded-xl bg-indigo-50/60 border border-indigo-100 text-xs space-y-3 mb-4">
                        <div class="flex items-center gap-2 text-indigo-900 font-bold">
                            <i class="fas fa-satellite-dish text-indigo-600"></i>
                            <span>Télémétrie Instantanée</span>
                        </div>
                        <p class="text-slate-600 text-[11px] leading-normal">
                            Capture automatique des coordonnées GPS au départ, en cours et à la clôture de chaque mission.
                        </p>
                        <a href="{{ route('gps.index') }}" class="ui-btn ui-btn-primary w-full text-xs py-2">
                            <i class="fas fa-map-location-dot"></i>
                            <span>Ouvrir la Carte GPS Live</span>
                        </a>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 text-center">
                    <span class="text-[11px] text-slate-500 flex items-center justify-center gap-1.5">
                        <i class="fas fa-shield-alt text-slate-400"></i>
                        Données de géolocalisation certifiées
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
