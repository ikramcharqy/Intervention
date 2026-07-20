<x-commercial-layout>
    <x-slot name="header">Statistiques Analytiques</x-slot>

    <div class="space-y-6">

        {{-- En-tête --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Centre de Statistiques & Performance</h3>
            <p class="text-sm text-gray-500">Données réelles basées sur {{ $totalInterventions }} intervention(s) enregistrée(s) dans le système.</p>
        </div>

        {{-- KPIs principaux --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $kpis = [
                    ['label' => 'Total Interventions', 'value' => $totalInterventions,  'color' => 'indigo',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['label' => 'Terminées',            'value' => $terminees,           'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'En Cours',             'value' => $enCours,             'color' => 'yellow',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label' => 'Planifiées',           'value' => $planifiees,          'color' => 'blue',    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ];
            @endphp
            @foreach($kpis as $kpi)
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center gap-4">
                <div class="p-3 rounded-xl bg-{{ $kpi['color'] }}-100 dark:bg-{{ $kpi['color'] }}-900/30 shrink-0">
                    <svg class="h-5 w-5 text-{{ $kpi['color'] }}-600 dark:text-{{ $kpi['color'] }}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $kpi['icon'] }}"/>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">{{ $kpi['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Métriques calculées --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Taux de complétion --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Taux de Completion</h4>
                <p class="text-xs text-gray-400 mb-4">Interventions terminées / total</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $tauxCompletion }}%</span>
                    <span class="text-xs text-gray-400">sur {{ $totalInterventions }} intervention(s)</span>
                </div>
                <div class="mt-4 h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-600 rounded-full transition-all duration-700" style="width: {{ $tauxCompletion }}%"></div>
                </div>
            </div>

            {{-- Durée moyenne --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Durée Moyenne Réelle</h4>
                <p class="text-xs text-gray-400 mb-4">Durée effective en minutes (interventions clôturées)</p>
                <div class="flex items-baseline gap-2">
                    @if($dureeReelleMoyenne)
                        @php
                            $h = intdiv((int)$dureeReelleMoyenne, 60);
                            $m = (int)$dureeReelleMoyenne % 60;
                        @endphp
                        <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">
                            {{ $h > 0 ? $h.'h ' : '' }}{{ $m }}m
                        </span>
                    @else
                        <span class="text-2xl font-extrabold text-gray-400">N/A</span>
                        <span class="text-xs text-gray-400">Pas encore de données</span>
                    @endif
                </div>
                <div class="mt-4 h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, ($dureeReelleMoyenne ?? 0) / 4) }}%"></div>
                </div>
            </div>

            {{-- Annulées --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Taux d'Annulation</h4>
                <p class="text-xs text-gray-400 mb-4">Interventions annulées / total</p>
                @php $tauxAnnulation = $totalInterventions > 0 ? round(($annulees / $totalInterventions) * 100, 1) : 0; @endphp
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-red-500 dark:text-red-400">{{ $tauxAnnulation }}%</span>
                    <span class="text-xs text-gray-400">{{ $annulees }} annulée(s)</span>
                </div>
                <div class="mt-4 h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-red-400 rounded-full" style="width: {{ $tauxAnnulation }}%"></div>
                </div>
            </div>
        </div>

        {{-- Graphiques --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Graphique par statut (Doughnut) --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Répartition par Statut</h4>
                <p class="text-xs text-gray-400 mb-4">Distribution réelle de toutes les interventions.</p>
                @if($parStatut->isNotEmpty())
                    <div class="relative h-52">
                        <canvas id="chartStatut"></canvas>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach($parStatut as $statut => $count)
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                {{ $statut }}: {{ $count }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <div class="h-52 flex items-center justify-center text-gray-400 text-sm">Aucune donnée disponible.</div>
                @endif
            </div>

            {{-- Graphique par priorité (Bar) --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Répartition par Priorité</h4>
                <p class="text-xs text-gray-400 mb-4">Volume d'interventions selon le niveau d'urgence.</p>
                @if($parPriorite->isNotEmpty())
                    <div class="relative h-52">
                        <canvas id="chartPriorite"></canvas>
                    </div>
                @else
                    <div class="h-52 flex items-center justify-center text-gray-400 text-sm">Aucune donnée disponible.</div>
                @endif
            </div>

            {{-- Évolution mensuelle (Line) --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Évolution sur 6 Mois</h4>
                <p class="text-xs text-gray-400 mb-4">Nombre d'interventions créées par mois.</p>
                @if($parMois->isNotEmpty())
                    <div class="relative h-52">
                        <canvas id="chartMois"></canvas>
                    </div>
                @else
                    <div class="h-52 flex items-center justify-center text-gray-400 text-sm">Aucune donnée disponible.</div>
                @endif
            </div>

            {{-- Top Techniciens --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-sm uppercase tracking-wider">Top Techniciens</h4>
                <p class="text-xs text-gray-400 mb-4">Nombre d'interventions terminées par technicien.</p>
                @if($topTechniciens->count() > 0)
                    @php $maxNb = $topTechniciens->max('nb_interventions'); @endphp
                    <div class="space-y-3">
                        @foreach($topTechniciens as $tech)
                            <div class="flex items-center gap-3">
                                <div class="h-7 w-7 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                                    {{ strtoupper(substr($tech->technicien?->name ?? '?', 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-center mb-0.5">
                                        <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $tech->technicien?->name ?? 'Inconnu' }}</span>
                                        <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 ml-2 shrink-0">{{ $tech->nb_interventions }}</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $maxNb > 0 ? round(($tech->nb_interventions / $maxNb) * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-40 flex items-center justify-center text-gray-400 text-sm">Aucune intervention terminée pour l'instant.</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#6b7280';
        const gridColor = isDark ? '#1e293b' : '#f3f4f6';

        // Doughnut statut
        @if($parStatut->isNotEmpty())
        new Chart(document.getElementById('chartStatut'), {
            type: 'doughnut',
            data: {
                labels: @json($parStatut->keys()),
                datasets: [{
                    data: @json($parStatut->values()),
                    backgroundColor: ['#6366f1','#f59e0b','#10b981','#8b5cf6','#ef4444','#64748b'],
                    borderWidth: 2,
                    borderColor: isDark ? '#111827' : '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { color: textColor, font: { size: 11 }, boxWidth: 12 } }
                }
            }
        });
        @endif

        // Bar priorité
        @if($parPriorite->isNotEmpty())
        new Chart(document.getElementById('chartPriorite'), {
            type: 'bar',
            data: {
                labels: @json($parPriorite->keys()),
                datasets: [{
                    label: 'Interventions',
                    data: @json($parPriorite->values()),
                    backgroundColor: ['#64748b', '#6366f1', '#f59e0b', '#ef4444'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor, font: { size: 11 }, stepSize: 1 }, grid: { color: gridColor } }
                }
            }
        });
        @endif

        // Line mois
        @if($parMois->isNotEmpty())
        new Chart(document.getElementById('chartMois'), {
            type: 'line',
            data: {
                labels: @json($parMois->keys()),
                datasets: [{
                    label: 'Créées',
                    data: @json($parMois->values()),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: textColor, font: { size: 10 } }, grid: { color: gridColor } },
                    y: { ticks: { color: textColor, font: { size: 10 }, stepSize: 1 }, grid: { color: gridColor } }
                }
            }
        });
        @endif
    })();
    </script>
</x-commercial-layout>
