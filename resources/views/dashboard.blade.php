<x-app-layout>
    <x-slot name="header">
        {{ __('Tableau de Bord Opérationnel') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Hero Banner Opérationnel -->
        <div class="ds-card-elevated p-7 flex flex-col md:flex-row items-start md:items-center justify-between gap-6" style="border-left: 4px solid #1E5EFF;">
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#EAF0FF] border border-[#D9E4FF] text-[#1E5EFF] text-xs font-semibold mb-2.5">
                    <span class="w-2 h-2 rounded-full bg-[#06A561]"></span>
                    <span>Centre de Pilotage Opérationnel</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">
                    Bonjour, {{ Auth::user()->name }}
                </h2>
                <p class="text-[#5A607F] text-xs sm:text-sm mt-1 leading-relaxed">
                    Supervision en temps réel des techniciens, planification des interventions et suivi des rapports.
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap shrink-0">
                <a href="{{ route('interventions.create') }}" class="ds-btn ds-btn-primary ds-btn-sm">
                    <i class="fas fa-plus"></i>
                    <span>Planifier Intervention</span>
                </a>

                <a href="{{ route('gps.index') }}" class="ds-btn ds-btn-white ds-btn-sm">
                    <i class="fas fa-location-crosshairs text-[#06A561]"></i>
                    <span>Tracking GPS Live</span>
                </a>
            </div>
        </div>

        <!-- KPI Stat Bar 1 -->
        <div class="ds-stat-bar">
            @foreach([
                ['label' => 'Total Interventions', 'value' => $stats['interventions'], 'subtitle' => "Volume d'activité globale", 'icon' => 'ti ti-clipboard-list', 'theme' => 'primary'],
                ['label' => 'En cours / Sur Site', 'value' => $stats['en_cours'], 'subtitle' => 'Missions actives sur le terrain', 'icon' => 'ti ti-clock-play', 'theme' => 'warning'],
                ['label' => 'Interventions Terminées', 'value' => $stats['terminees'], 'subtitle' => 'Comptes-rendus rédigés', 'icon' => 'ti ti-circle-check', 'theme' => 'success'],
                ['label' => 'En Attente Validation', 'value' => $stats['en_attente'], 'subtitle' => 'Rapports à approuver', 'icon' => 'ti ti-hourglass', 'theme' => 'secondary'],
            ] as $kpi)
                <div class="ds-stat-block">
                    <div class="min-w-0">
                        <p class="ds-stat-label">{{ $kpi['label'] }}</p>
                        <p class="ds-stat-value truncate">{{ $kpi['value'] }}</p>
                        <p class="ds-stat-subtitle truncate">{{ $kpi['subtitle'] }}</p>
                    </div>
                    <div class="ds-stat-icon ds-stat-icon-{{ $kpi['theme'] }}">
                        <i class="{{ $kpi['icon'] }}"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- KPI Stat Bar 2 -->
        <div class="ds-stat-bar">
            @foreach([
                ['label' => 'Clients Enregistrés', 'value' => $stats['clients'], 'subtitle' => 'Portefeuille actif', 'icon' => 'ti ti-users', 'theme' => 'secondary'],
                ['label' => 'Chantiers Actifs', 'value' => $stats['chantiers'], 'subtitle' => 'Sites opérationnels', 'icon' => 'ti ti-building', 'theme' => 'success'],
                ['label' => 'Techniciens Terrain', 'value' => $stats['techniciens'], 'subtitle' => 'Équipes mobilisables', 'icon' => 'ti ti-tools', 'theme' => 'primary'],
                ['label' => "Rapports d'Activité", 'value' => $stats['rapports'], 'subtitle' => 'Documents certifiés', 'icon' => 'ti ti-file-report', 'theme' => 'secondary'],
            ] as $kpi)
                <div class="ds-stat-block">
                    <div class="min-w-0">
                        <p class="ds-stat-label">{{ $kpi['label'] }}</p>
                        <p class="ds-stat-value truncate">{{ $kpi['value'] }}</p>
                        <p class="ds-stat-subtitle truncate">{{ $kpi['subtitle'] }}</p>
                    </div>
                    <div class="ds-stat-icon ds-stat-icon-{{ $kpi['theme'] }}">
                        <i class="{{ $kpi['icon'] }}"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Graphiques -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Interventions par Heure : Aujourd'hui vs Hier -->
            <div class="ds-card-elevated lg:col-span-2 p-7">
                <div class="flex items-start justify-between mb-6">
                    <h3 class="text-[16px] font-bold text-[#131523]">Interventions par Heure</h3>
                    <div class="flex items-center gap-4 text-[14px] text-[#5A607F]">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-[2px] bg-[#D9E1EC] inline-block"></span>
                            Hier
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-[2px] bg-[#1E5EFF] inline-block"></span>
                            Aujourd'hui
                        </span>
                    </div>
                </div>

                <div class="flex items-baseline gap-8 mb-6">
                    <div>
                        <p class="ds-stat-value">{{ array_sum($interventionsAujourdhui) }}</p>
                        <p class="ds-stat-subtitle">Créées aujourd'hui</p>
                    </div>
                    <div>
                        <p class="ds-stat-value">{{ array_sum($interventionsHier) }}</p>
                        <p class="ds-stat-subtitle">Créées hier</p>
                    </div>
                </div>

                <div class="relative h-64">
                    <canvas id="chartInterventionsHeure"></canvas>
                </div>
            </div>

            <!-- Interventions Terminées : 7 derniers jours -->
            <div class="ds-card-elevated p-7 flex flex-col">
                <h3 class="text-[16px] font-bold text-[#131523] mb-6">Interventions Terminées</h3>

                <div class="space-y-4 mb-6">
                    <div>
                        <p class="ds-stat-value">{{ $totalCreees7Jours }}</p>
                        <p class="ds-stat-subtitle">Créées (7 derniers jours)</p>
                    </div>
                    <div class="pt-4 border-t border-[#E6E9F4]">
                        <p class="ds-stat-value">{{ $totalTerminees7Jours }}</p>
                        <p class="ds-stat-subtitle">Validées (7 derniers jours)</p>
                    </div>
                </div>

                <div class="relative h-40 flex-1">
                    <canvas id="chartInterventionsJour"></canvas>
                </div>
            </div>
        </div>

        <!-- Pipeline & Répartition par Priorité -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Pipeline des Interventions (Funnel) -->
            <div class="ds-card-elevated lg:col-span-2 p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-6">Pipeline des Interventions</h3>
                @php
                    $funnelColors = ['#1E5EFF', '#5A607F', '#B98900', '#06A561'];
                @endphp
                @foreach($etapesLabels as $i => $label)
                    <div class="ds-funnel-row">
                        <span class="ds-funnel-label">{{ $label }}</span>
                        <div class="ds-funnel-track">
                            <div class="ds-funnel-fill" style="width: {{ $funnelPercents[$i] }}%; background-color: {{ $funnelColors[$i] }};"></div>
                        </div>
                        <span class="ds-funnel-value">{{ $funnelPercents[$i] }}%</span>
                    </div>
                @endforeach
            </div>

            <!-- Répartition par Priorité (Donut) -->
            <div class="ds-card-elevated p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-6">Répartition par Priorité</h3>
                @if($parPriorite->isNotEmpty())
                    <div class="relative h-40 mb-5">
                        <canvas id="chartPriorite"></canvas>
                    </div>
                    <div class="space-y-2">
                        @php
                            $prioColors = ['Urgente' => '#F0142F', 'Haute' => '#F5A623', 'Normale' => '#1E5EFF', 'Faible' => '#A1A7C4'];
                        @endphp
                        @foreach($parPriorite as $priorite => $count)
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2 text-[#5A607F]">
                                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $prioColors[$priorite] ?? '#A1A7C4' }};"></span>
                                    {{ $priorite }}
                                </span>
                                <span class="font-bold text-[#131523]">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-40 flex items-center justify-center text-[#A1A7C4] text-xs">Aucune donnée disponible.</div>
                @endif
            </div>
        </div>

        <!-- Techniciens les Plus Actifs & Taux -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Techniciens les Plus Actifs -->
            <div class="ds-card-elevated p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-6">Techniciens les Plus Actifs</h3>
                @if($technicienTaches->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($technicienTaches as $row)
                            @php $tech = $techniciensById[$row->technicien_id] ?? null; @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-[4px] bg-[#ECF2FF] text-[#1E5EFF] font-bold text-xs flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($tech->name ?? '?', 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-[#131523] truncate">{{ $tech->name ?? 'Technicien inconnu' }}</p>
                                    <p class="text-[11px] text-[#A1A7C4]">{{ $row->nb_taches }} tâche(s) effectuée(s)</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-[#A1A7C4] text-xs py-8">Aucune tâche effectuée pour l'instant.</div>
                @endif
            </div>

            <!-- Taux de Complétion -->
            <div class="ds-card-elevated p-7 flex flex-col items-center">
                <h3 class="text-[16px] font-bold text-[#131523] mb-6 self-start">Taux de Complétion</h3>
                <div class="ds-progress-ring">
                    <canvas id="ringCompletion"></canvas>
                    <div class="ds-progress-ring-label">
                        <span class="ds-progress-ring-value">{{ $tauxCompletionGlobal }}%</span>
                        <span class="ds-progress-ring-caption">Terminées</span>
                    </div>
                </div>
                <div class="w-full mt-6 space-y-2">
                    @forelse($completionParPriorite as $priorite => $count)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-[#5A607F]">{{ $priorite }}</span>
                            <span class="font-bold text-[#131523]">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-center text-[#A1A7C4] text-xs">Aucune intervention terminée.</p>
                    @endforelse
                </div>
            </div>

            <!-- Taux d'Annulation -->
            <div class="ds-card-elevated p-7 flex flex-col items-center">
                <h3 class="text-[16px] font-bold text-[#131523] mb-6 self-start">Taux d'Annulation</h3>
                <div class="ds-progress-ring">
                    <canvas id="ringAnnulation"></canvas>
                    <div class="ds-progress-ring-label">
                        <span class="ds-progress-ring-value">{{ $tauxAnnulationGlobal }}%</span>
                        <span class="ds-progress-ring-caption">Annulées</span>
                    </div>
                </div>
                <div class="w-full mt-6 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[#5A607F]">Ce mois</span>
                        <span class="font-bold text-[#131523]">{{ $annuleesCeMois }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-[#5A607F]">Mois dernier</span>
                        <span class="font-bold text-[#131523]">{{ $annuleesMoisDernier }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-2 border-t border-[#E6E9F4]">
                        <span class="text-[#5A607F]">Total</span>
                        <span class="font-bold text-[#131523]">{{ $totalAnnulees }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Activité Récente & Répartition par Ville -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Activité Récente (Tableau) -->
            <div class="ds-card-elevated lg:col-span-2 overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between px-7 py-5 border-b border-[#E6E9F4]">
                        <div>
                            <h3 class="flex items-center gap-2 text-[16px] font-bold text-[#131523]">
                                <i class="fas fa-clock text-[#1E5EFF]"></i>
                                <span>Dernières Interventions Créées</span>
                            </h3>
                            <p class="text-[13px] text-[#5A607F] mt-0.5">Flux chronologique des missions enregistrées</p>
                        </div>
                        <a href="{{ route('interventions.index') }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc] transition">
                            Voir tout →
                        </a>
                    </div>

                    @if($recentInterventions->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="ds-table">
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
                                                    <span class="font-bold text-xs text-[#1E5EFF] bg-[#EAF0FF] px-2 py-0.5 rounded border border-[#D9E4FF] group-hover:bg-[#D9E4FF] transition">
                                                        {{ $intervention->code_intervention }}
                                                    </span>
                                                </a>
                                            </td>
                                            <td>
                                                <div class="text-[#131523] font-semibold text-xs">{{ $intervention->chantier->nom ?? '-' }}</div>
                                                <div class="text-[11px] text-[#A1A7C4]">{{ $intervention->chantier->client->nom ?? '-' }}</div>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-6 h-6 rounded-full bg-[#F5F6FA] border border-[#E6E9F4] text-[#5A607F] text-[10px] font-bold flex items-center justify-center">
                                                        {{ strtoupper(substr($intervention->technicien->name ?? 'NA', 0, 2)) }}
                                                    </div>
                                                    <span class="text-xs text-[#5A607F] font-medium">{{ $intervention->technicien->name ?? 'Non assigné' }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $priMap = [
                                                        'Faible' => 'bg-[#F5F6FA] text-[#5A607F] border-[#E6E9F4]',
                                                        'Normale' => 'bg-[#F5F6FA] text-[#5A607F] border-[#E6E9F4]',
                                                        'Haute' => 'bg-[#FFF3DE] text-[#B98900] border-[#FFE7B8] font-semibold',
                                                        'Urgente' => 'bg-[#FDE3E6] text-[#F0142F] border-[#F8C4CA] font-bold',
                                                    ];
                                                @endphp
                                                <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold border {{ $priMap[$intervention->priorite] ?? 'bg-[#F5F6FA] text-[#5A607F] border-[#E6E9F4]' }}">
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
                        <div class="p-8 text-center text-[#A1A7C4] text-xs">
                            <i class="fas fa-clipboard-list text-2xl text-[#D7DBEC] mb-2 block"></i>
                            Aucune intervention enregistrée pour le moment.
                        </div>
                    @endif
                </div>

                <div class="p-3 bg-[#F5F6FA] border-t border-[#E6E9F4] text-right">
                    <a href="{{ route('interventions.create') }}" class="ds-btn ds-btn-primary ds-btn-sm">
                        <i class="fas fa-plus"></i> Nouvelle Intervention
                    </a>
                </div>
            </div>

            <!-- Répartition par Ville -->
            <div class="ds-card-elevated p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-5">Répartition par Ville</h3>
                @if($parVille->isNotEmpty())
                    <div class="space-y-4">
                        @foreach($parVille as $ville => $count)
                            @php $pct = round(($count / $totalParVille) * 100); @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="flex items-center gap-2 font-semibold text-[#131523] truncate">
                                        <span class="w-2 h-2 rounded-full bg-[#1E5EFF] shrink-0"></span>
                                        <span class="truncate">{{ $ville }}</span>
                                    </span>
                                    <span class="font-mono text-[#A1A7C4] text-[11px] shrink-0">{{ $count }} · {{ $pct }}%</span>
                                </div>
                                <div class="w-full bg-[#F5F6FA] rounded-full h-2 overflow-hidden border border-[#E6E9F4]">
                                    <div class="bg-[#1E5EFF] h-2 rounded-full" style="width: {{ max(4, $pct) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-[#A1A7C4] text-xs py-8">Aucune donnée de ville disponible.</div>
                @endif
            </div>
        </div>

        <!-- Tracking GPS Direct : bandeau compact -->
        <div class="ds-card-elevated p-5 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-[6px] bg-[#E3FBF0] flex items-center justify-center shrink-0">
                    <i class="fas fa-map-marked-alt text-[#06A561]"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-[#131523]">Tracking GPS Live</p>
                    <p class="text-[11px] text-[#5A607F]">Localisation en temps réel des équipes terrain</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#E3FBF0] border border-[#C4F8E2] text-[#06A561] text-[10px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#06A561]"></span>
                    Actif
                </span>
                <a href="{{ route('gps.index') }}" class="ds-btn ds-btn-primary ds-btn-sm">
                    <i class="fas fa-map-location-dot"></i>
                    <span>Ouvrir la Carte GPS Live</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const textColor = '#A1A7C4';
        const gridColor = '#E6E9F4';

        // Ligne : interventions par heure, aujourd'hui vs hier
        new Chart(document.getElementById('chartInterventionsHeure'), {
            type: 'line',
            data: {
                labels: @json($heuresLabels),
                datasets: [
                    {
                        label: 'Hier',
                        data: @json($interventionsHier),
                        borderColor: '#D9E1EC',
                        backgroundColor: 'rgba(217,225,236,0.15)',
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        tension: 0.4,
                    },
                    {
                        label: "Aujourd'hui",
                        data: @json($interventionsAujourdhui),
                        borderColor: '#1E5EFF',
                        backgroundColor: 'rgba(30,94,255,0.08)',
                        borderWidth: 2.5,
                        pointRadius: 0,
                        pointHoverRadius: 4,
                        fill: true,
                        tension: 0.4,
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#131523',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 6,
                        displayColors: false,
                    },
                },
                scales: {
                    x: {
                        ticks: { color: textColor, font: { size: 10 }, maxTicksLimit: 12 },
                        grid: { display: false },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor, font: { size: 11 }, stepSize: 1, precision: 0 },
                        grid: { color: gridColor, borderDash: [4, 4] },
                    },
                },
            }
        });

        // Barres : interventions terminées, 7 derniers jours
        new Chart(document.getElementById('chartInterventionsJour'), {
            type: 'bar',
            data: {
                labels: @json($joursLabels),
                datasets: [{
                    label: 'Terminées',
                    data: @json($interventionsTermineesParJour),
                    backgroundColor: '#1FD286',
                    borderRadius: 4,
                    borderSkipped: false,
                    maxBarThickness: 28,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: textColor, font: { size: 10 } }, grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor, font: { size: 10 }, stepSize: 1, precision: 0 },
                        grid: { color: gridColor, borderDash: [4, 4] },
                    },
                },
            }
        });

        // Donut : répartition par priorité
        @if($parPriorite->isNotEmpty())
        new Chart(document.getElementById('chartPriorite'), {
            type: 'doughnut',
            data: {
                labels: @json($parPriorite->keys()),
                datasets: [{
                    data: @json($parPriorite->values()),
                    backgroundColor: @json(collect($parPriorite->keys())->map(fn ($p) => $prioColors[$p] ?? '#A1A7C4')->values()),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } },
            }
        });
        @endif

        // Anneau : taux de complétion
        new Chart(document.getElementById('ringCompletion'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $tauxCompletionGlobal }}, {{ max(0, 100 - $tauxCompletionGlobal) }}],
                    backgroundColor: ['#06A561', '#E6E9F4'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
            }
        });

        // Anneau : taux d'annulation
        new Chart(document.getElementById('ringAnnulation'), {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $tauxAnnulationGlobal }}, {{ max(0, 100 - $tauxAnnulationGlobal) }}],
                    backgroundColor: ['#F0142F', '#E6E9F4'],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '78%',
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
            }
        });
    })();
    </script>
</x-app-layout>
