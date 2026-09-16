<x-commercial-layout>
    <x-slot name="header">Statistiques Commerciales & Performance Ventes</x-slot>

    <div class="space-y-6">

        {{-- En-tête Commercial Metronic 8 --}}
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl shadow-xl border border-slate-800 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-indigo-300 text-xs font-semibold mb-2">
                    📈 Dashboard Analytique CRM & Ventes
                </span>
                <h3 class="text-2xl font-black text-white tracking-tight">Performances Commerciales & Conversion</h3>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Indicateurs réels de vos prospects, propositions de devis, taux de transformation client et chiffre d'affaires signé.
                </p>
            </div>
            
            <div class="flex items-center gap-3 bg-white/5 border border-white/10 p-4 rounded-xl backdrop-blur-md">
                <div class="text-center px-4 border-r border-white/10">
                    <span class="block text-2xl font-black text-emerald-400">{{ format_montant($montantDevisAcceptes ?? 0, 0) }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-slate-300 font-bold">CA Signé (Devis Acceptés)</span>
                </div>
                <div class="text-center px-4">
                    <span class="block text-2xl font-black text-indigo-300">{{ $tauxConversionProspects ?? 0 }}%</span>
                    <span class="text-[10px] uppercase tracking-wider text-slate-300 font-bold">Taux Conversion Prospects</span>
                </div>
            </div>
        </div>

        {{-- KPIs Commercial Réels --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center gap-4">
                <div class="p-3 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $nbProspects ?? 0 }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Prospects Qualifiés</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center gap-4">
                <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $nbDevis ?? 0 }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Devis Émis (Total)</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center gap-4">
                <div class="p-3 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">{{ $devisAcceptes ?? 0 }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Devis Signés / Gagnés</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center gap-4">
                <div class="p-3 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-extrabold text-amber-600 dark:text-amber-400">{{ $devisAttente ?? 0 }}</p>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Devis En Négociation</p>
                </div>
            </div>
        </div>

        {{-- Métriques Avancées Commerciales --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Taux de conversion prospects -> clients --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Transformation Prospects</h4>
                <p class="text-xs text-gray-400 mb-4">Pourcentage de prospects devenus clients</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $tauxConversionProspects ?? 0 }}%</span>
                    <span class="text-xs text-gray-400">({{ $prospectsConvertis ?? 0 }} converti(s))</span>
                </div>
                <div class="mt-4 h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-600 rounded-full" style="width: {{ min(100, $tauxConversionProspects ?? 0) }}%"></div>
                </div>
            </div>

            {{-- Valeur du Pipeline Commercial --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Volume Total du Pipeline</h4>
                <p class="text-xs text-gray-400 mb-4">Montant TTC cumulé des offres de devis émanant du service</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ format_montant($montantDevisTotal ?? 0) }}
                    </span>
                </div>
                <div class="mt-4 h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 100%"></div>
                </div>
            </div>

            {{-- Taux d'Acceptation Devis --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Taux de Succès Devis</h4>
                <p class="text-xs text-gray-400 mb-4">Devis signés par rapport aux offres émises</p>
                @php 
                    $tauxSuccesDevis = ($nbDevis ?? 0) > 0 ? round((($devisAcceptes ?? 0) / $nbDevis) * 100, 1) : 0;
                @endphp
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-blue-600 dark:text-blue-400">{{ $tauxSuccesDevis }}%</span>
                    <span class="text-xs text-gray-400">{{ $devisAcceptes ?? 0 }} sur {{ $nbDevis ?? 0 }}</span>
                </div>
                <div class="mt-4 h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ $tauxSuccesDevis }}%"></div>
                </div>
            </div>
        </div>

        {{-- Section Graphiques Répartition Devis & Interventions --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Graphique Répartition des Devis --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Statut des Devis Commercial</h4>
                <p class="text-xs text-gray-400 mb-4">Répartition des propositions commerciales par état.</p>
                @if(isset($parStatutDevis) && $parStatutDevis->isNotEmpty())
                    {{-- Volume faible : les valeurs absolues priment sur le pourcentage du
                    donut (qui donnerait une fausse impression de significativité statistique). --}}
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach($parStatutDevis as $statutDevis => $totalStatut)
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                                {{ $totalStatut }} {{ $statutDevis }}
                            </span>
                        @endforeach
                    </div>
                    <div class="relative h-56">
                        <canvas id="chartDevisStatut"></canvas>
                    </div>
                    @if(($nbDevis ?? 0) < 20)
                        <p class="text-[10px] text-gray-400 mt-2 text-center">Basé sur {{ $nbDevis }} devis — volume encore faible, à interpréter avec prudence.</p>
                    @endif
                @else
                    <div class="h-56 flex items-center justify-center text-gray-400 text-xs">Aucun devis créé pour l'instant.</div>
                @endif
            </div>

            {{-- Graphique Répartition des Interventions --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Interventions Générées par le Commercial</h4>
                <p class="text-xs text-gray-400 mb-4">Suivi des demandes d'intervention issues de vos devis acceptés.</p>
                @if(isset($parStatutDemandes) && $parStatutDemandes->isNotEmpty())
                    <div class="relative h-56">
                        <canvas id="chartStatut"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-gray-400 text-xs">Aucune demande d'intervention générée pour l'instant.</div>
                @endif
            </div>

        </div>

        {{-- Section Évolution & Entonnoir --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Évolution mensuelle Devis --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Évolution Mensuelle (6 derniers mois)</h4>
                <p class="text-xs text-gray-400 mb-4">Nombre de devis émis et montant accepté par mois.</p>
                @if(isset($parMoisDevis) && $parMoisDevis->isNotEmpty())
                    <div class="relative h-64">
                        <canvas id="chartEvolution"></canvas>
                    </div>
                @else
                    <div class="h-64 flex items-center justify-center text-gray-400 text-xs">Aucun devis émis sur les 6 derniers mois.</div>
                @endif
            </div>

            {{-- Répartition des Prospects par Statut --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Prospects par Statut</h4>
                <p class="text-xs text-gray-400 mb-4">Répartition de votre portefeuille de prospects.</p>
                @if(isset($parStatutProspects) && $parStatutProspects->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach($parStatutProspects as $statutProspect => $totalStatutP)
                            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200">
                                {{ $totalStatutP }} {{ $statutProspect }}
                            </span>
                        @endforeach
                    </div>
                    <div class="relative h-56">
                        <canvas id="chartProspectsStatut"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-gray-400 text-xs">Aucun prospect enregistré.</div>
                @endif
            </div>

        </div>

        {{-- Entonnoir de Conversion --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
            <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Entonnoir de Conversion</h4>
            <p class="text-xs text-gray-400 mb-4">Du premier contact prospect jusqu'à la conversion effective en client — repère les étapes où le pipeline perd le plus de volume.</p>
            @if(isset($entonnoirConversion) && array_sum($entonnoirConversion) > 0)
                <div class="relative h-64">
                    <canvas id="chartEntonnoir"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center text-gray-400 text-xs">Aucune donnée de pipeline pour l'instant.</div>
            @endif
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#6b7280';

        // Doughnut Devis Statut
        @if(isset($parStatutDevis) && $parStatutDevis->isNotEmpty())
        new Chart(document.getElementById('chartDevisStatut'), {
            type: 'doughnut',
            data: {
                labels: @json($parStatutDevis->keys()),
                datasets: [{
                    data: @json($parStatutDevis->values()),
                    backgroundColor: ['#10b981','#f59e0b','#ef4444','#6366f1','#64748b'],
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

        // Doughnut Interventions Statut (Demandes d'Intervention du commercial)
        @if(isset($parStatutDemandes) && $parStatutDemandes->isNotEmpty())
        new Chart(document.getElementById('chartStatut'), {
            type: 'doughnut',
            data: {
                labels: @json($parStatutDemandes->keys()),
                datasets: [{
                    data: @json($parStatutDemandes->values()),
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

        // Doughnut Prospects Statut
        @if(isset($parStatutProspects) && $parStatutProspects->isNotEmpty())
        new Chart(document.getElementById('chartProspectsStatut'), {
            type: 'doughnut',
            data: {
                labels: @json($parStatutProspects->keys()),
                datasets: [{
                    data: @json($parStatutProspects->values()),
                    backgroundColor: ['#64748b','#f59e0b','#6366f1','#10b981','#ef4444'],
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

        // Évolution mensuelle : barres (nb devis) + ligne (montant accepté)
        @if(isset($parMoisDevis) && $parMoisDevis->isNotEmpty())
        new Chart(document.getElementById('chartEvolution'), {
            data: {
                labels: @json($parMoisDevis->pluck('mois')),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Devis émis',
                        data: @json($parMoisDevis->pluck('nb_devis')),
                        backgroundColor: '#6366f1',
                        borderRadius: 4,
                        yAxisID: 'y',
                    },
                    {
                        type: 'line',
                        label: 'Montant accepté ({{ currency_symbol() }})',
                        data: @json($parMoisDevis->pluck('montant_accepte')),
                        borderColor: '#10b981',
                        backgroundColor: '#10b981',
                        tension: 0.3,
                        yAxisID: 'y1',
                    },
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: { size: 11 }, boxWidth: 12 } }
                },
                scales: {
                    x: { ticks: { color: textColor } },
                    y: { position: 'left', ticks: { color: textColor, precision: 0 }, title: { display: true, text: 'Devis émis', color: textColor } },
                    y1: { position: 'right', ticks: { color: textColor }, grid: { drawOnChartArea: false }, title: { display: true, text: 'Montant ({{ currency_symbol() }})', color: textColor } },
                }
            }
        });
        @endif

        // Entonnoir de conversion (barres horizontales décroissantes)
        @if(isset($entonnoirConversion) && array_sum($entonnoirConversion) > 0)
        new Chart(document.getElementById('chartEntonnoir'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($entonnoirConversion)),
                datasets: [{
                    data: @json(array_values($entonnoirConversion)),
                    backgroundColor: ['#6366f1','#3e97ff','#f59e0b','#10b981'],
                    borderRadius: 6,
                    barThickness: 32,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => ctx.raw + ' ' + ctx.label } }
                },
                scales: {
                    x: { ticks: { color: textColor, precision: 0 }, beginAtZero: true },
                    y: { ticks: { color: textColor, font: { size: 12, weight: 'bold' } } }
                }
            }
        });
        @endif
    })();
    </script>
</x-commercial-layout>
