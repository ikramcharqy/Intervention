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
                    <span class="block text-2xl font-black text-emerald-400">{{ number_format($montantDevisAcceptes ?? 0, 0, ',', ' ') }} MAD</span>
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
                        {{ number_format($montantDevisTotal ?? 0, 2, ',', ' ') }} MAD
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
                    <div class="relative h-56">
                        <canvas id="chartDevisStatut"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-gray-400 text-xs">Aucun devis créé pour l'instant.</div>
                @endif
            </div>

            {{-- Graphique Répartition des Interventions --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6">
                <h4 class="font-bold text-gray-900 dark:text-white mb-1 text-xs uppercase tracking-wider">Interventions Générées par le Commercial</h4>
                <p class="text-xs text-gray-400 mb-4">Suivi de l'exécution terrain des demandes clients.</p>
                @if(isset($parStatut) && $parStatut->isNotEmpty())
                    <div class="relative h-56">
                        <canvas id="chartStatut"></canvas>
                    </div>
                @else
                    <div class="h-56 flex items-center justify-center text-gray-400 text-xs">Aucune intervention enregistrée.</div>
                @endif
            </div>

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

        // Doughnut Interventions Statut
        @if(isset($parStatut) && $parStatut->isNotEmpty())
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
    })();
    </script>
</x-commercial-layout>
