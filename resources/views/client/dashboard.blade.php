<x-client-layout>
    <x-slot name="header">Tableau de bord</x-slot>

    @php
        // Palette du donut : couleurs sémantiques fixes pour les statuts connus,
        // rotation neutre pour tout statut additionnel — jamais de couleur codée
        // en dur par libellé exact (les libellés viennent tous du modèle Intervention).
        $donutColors = [
            'Planifiee' => '#38bdf8', 'Planifiée' => '#38bdf8',
            'Acceptee' => '#6366f1', 'Acceptée' => '#6366f1',
            'En cours' => '#f59e0b',
            'Formulaire rempli' => '#a855f7',
            'Terminee' => '#10b981', 'Terminée' => '#10b981',
            'Validee' => '#059669', 'Validée' => '#059669',
            'Annulee' => '#94a3b8', 'Annulée' => '#94a3b8',
        ];
        $fallbackPalette = ['#38bdf8', '#f59e0b', '#a855f7', '#10b981', '#94a3b8', '#f472b6'];
        $donutLabels = $parStatut->keys()->values();
        $donutValues = $parStatut->values();
        $donutBg = $donutLabels->map(function ($label, $i) use ($donutColors, $fallbackPalette) {
            return $donutColors[$label] ?? $fallbackPalette[$i % count($fallbackPalette)];
        });
        $currency = currency_symbol();
    @endphp

    <div class="space-y-6">

        <!-- HERO : bannière + carte compte -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-5">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500 p-7 md:p-8 text-white">
                <div class="relative z-10 max-w-md">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-emerald-50/80">
                        <i class="fas fa-circle-check"></i> Espace Client Protégé
                    </span>
                    <h2 class="text-2xl md:text-[26px] font-extrabold mt-2 leading-snug">Bienvenue, {{ Auth::user()->prenom ?: Auth::user()->name }}</h2>
                    <p class="text-sm text-emerald-50/90 mt-2 leading-relaxed">
                        Retrouvez ici le suivi de vos chantiers, l'avancement de vos interventions et vos documents.
                    </p>
                    <a href="{{ route('client.demandes.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-full bg-white text-emerald-600 text-xs font-extrabold hover:bg-emerald-50 transition">
                        <i class="fas fa-plus"></i> Nouvelle Demande
                    </a>
                </div>
                <div class="absolute -right-10 -bottom-16 w-64 h-64 rounded-full bg-white/10"></div>
                <div class="absolute right-16 -top-10 w-32 h-32 rounded-full bg-white/10"></div>
            </div>

            <div class="rounded-3xl bg-[#1e2530] text-white p-6 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400">Compte Client</span>
                    <p class="text-base font-extrabold mt-1">{{ $client->nom ?? Auth::user()->name }}</p>
                </div>
                <div class="mt-6 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Chantiers suivis</span>
                        <span class="font-bold">{{ $stats['chantiers'] }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-400">Demandes en attente</span>
                        <span class="font-bold">{{ $stats['demandes_en_attente'] }}</span>
                    </div>
                </div>
                <a href="{{ route('client.support.index') }}" class="mt-5 inline-flex items-center justify-center gap-2 py-2.5 rounded-full bg-white/10 hover:bg-white/20 text-xs font-bold transition">
                    <i class="fas fa-headset"></i> Contacter le support
                </a>
            </div>
        </div>

        <!-- KPI ROW -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @php
                $kpiCards = [
                    ['label' => 'Interventions', 'value' => $stats['interventions'], 'trend' => $stats['trend_interventions'], 'sparkline' => $evolution['creees'], 'color' => '#10b981', 'href' => route('client.interventions.index')],
                    ['label' => 'Mes Chantiers', 'value' => $stats['chantiers'], 'trend' => null, 'sparkline' => null, 'color' => '#38bdf8', 'href' => route('client.chantiers.index')],
                    ['label' => 'En cours', 'value' => $stats['en_cours'], 'trend' => null, 'sparkline' => null, 'color' => '#f59e0b', 'href' => route('client.interventions.index', ['statut' => 'En cours'])],
                    ['label' => 'Terminées', 'value' => $stats['terminees'], 'trend' => $stats['trend_terminees'], 'sparkline' => $evolution['terminees'], 'color' => '#059669', 'href' => route('client.interventions.index', ['statut' => 'Terminee'])],
                ];
            @endphp
            @foreach($kpiCards as $i => $kpi)
                <a href="{{ $kpi['href'] }}" class="block min-w-0 bg-white rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $kpi['label'] }}</span>
                        @if(!is_null($kpi['trend']))
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold {{ $kpi['trend'] >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                <i class="fas fa-arrow-{{ $kpi['trend'] >= 0 ? 'up' : 'down' }} text-[8px]"></i> {{ abs($kpi['trend']) }}%
                            </span>
                        @endif
                    </div>
                    <div class="text-3xl font-extrabold text-[#1e2530] mt-2 font-mono">{{ $kpi['value'] }}</div>
                    <div class="relative mt-3 h-8 overflow-hidden">
                        @if($kpi['sparkline'])
                            <canvas id="spark-{{ $i }}"></canvas>
                        @else
                            <span class="text-[11px] text-slate-300">Total actuel</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <!-- DONUT + EVOLUTION -->
        <div class="grid grid-cols-1 lg:grid-cols-[340px_1fr] gap-5">
            <div class="min-w-0 bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1e2530] mb-1">Répartition par statut</h3>
                <p class="text-[11px] text-slate-400 mb-4">{{ $stats['interventions'] }} interventions au total</p>
                <div class="relative w-44 h-44 mx-auto overflow-hidden">
                    <canvas id="donutStatuts"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-2xl font-extrabold text-[#1e2530] font-mono">{{ $stats['interventions'] }}</span>
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Total</span>
                    </div>
                </div>
                <div class="mt-5 space-y-2">
                    @foreach($donutLabels as $i => $label)
                        <a href="{{ route('client.interventions.index', ['statut' => $label]) }}" class="flex items-center justify-between text-xs hover:bg-slate-50 rounded-lg px-2 py-1.5 transition">
                            <span class="flex items-center gap-2 text-slate-600 font-medium">
                                <span class="w-2 h-2 rounded-full" style="background:{{ $donutBg[$i] }}"></span>
                                {{ $label }}
                            </span>
                            <span class="font-bold text-[#1e2530] font-mono">{{ $donutValues[$i] }}</span>
                        </a>
                    @endforeach
                    @if($donutLabels->isEmpty())
                        <p class="text-xs text-slate-400 italic text-center py-4">Aucune donnée pour le moment.</p>
                    @endif
                </div>
            </div>

            <div class="min-w-0 bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-extrabold text-[#1e2530]">Évolution des interventions</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Créées vs terminées — 6 derniers mois</p>
                    </div>
                    <div class="flex items-center gap-3 text-[11px] font-semibold">
                        <span class="flex items-center gap-1.5 text-slate-500"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Créées</span>
                        <span class="flex items-center gap-1.5 text-slate-500"><span class="w-2 h-2 rounded-full bg-amber-400"></span> Terminées</span>
                    </div>
                </div>
                <div class="relative h-64 overflow-hidden">
                    <canvas id="evolutionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- PROGRESS BARS + FACTURATION -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1e2530] mb-5">Suivi des statuts</h3>
                <div class="space-y-5">
                    @foreach($donutLabels as $i => $label)
                        @php $pct = $stats['interventions'] > 0 ? round(($donutValues[$i] / $stats['interventions']) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-semibold text-slate-600">{{ $label }}</span>
                                <span class="font-bold text-[#1e2530]">{{ $donutValues[$i] }} <span class="text-slate-400 font-medium">({{ $pct }}%)</span></span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $donutBg[$i] }}"></div>
                            </div>
                        </div>
                    @endforeach
                    @if($donutLabels->isEmpty())
                        <p class="text-xs text-slate-400 italic">Aucune intervention pour le moment.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm flex flex-col">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Solde restant dû</span>
                <div class="text-3xl font-extrabold text-[#1e2530] mt-2 font-mono">{{ number_format($facturation['solde'], 2, ',', ' ') }} {{ $currency }}</div>
                <p class="text-[11px] text-slate-400 mt-1">sur {{ number_format($facturation['total'], 2, ',', ' ') }} {{ $currency }} facturés</p>
                <div class="mt-5 flex gap-2">
                    <a href="{{ route('client.factures.index') }}" class="flex-1 text-center py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition">
                        Voir mes factures
                    </a>
                    <a href="{{ route('client.support.index') }}" class="flex-1 text-center py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                        Support
                    </a>
                </div>
            </div>
        </div>

        <!-- TABLE + À SUIVRE -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-5">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-extrabold text-[#1e2530]">Mes Dernières Interventions</h3>
                    <a href="{{ route('client.interventions.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">Voir tout →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-slate-400 uppercase font-bold text-[10px]">
                                <th class="py-2.5 pr-4">Technicien</th>
                                <th class="py-2.5 px-4">Chantier</th>
                                <th class="py-2.5 px-4">Code</th>
                                <th class="py-2.5 px-4 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentInterventions as $interv)
                                <tr class="hover:bg-slate-50 transition cursor-pointer" onclick="window.location='{{ route('client.interventions.show', $interv) }}'">
                                    <td class="py-3 pr-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-600 font-bold text-[10px] flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($interv->technicien?->name ?? '??', 0, 2)) }}
                                            </div>
                                            <span class="font-semibold text-[#1e2530]">{{ $interv->technicien?->name ?? 'Non assigné' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-slate-500">{{ $interv->chantier?->nom ?? '—' }}</td>
                                    <td class="py-3 px-4 font-mono text-slate-500">{{ $interv->code_intervention }}</td>
                                    <td class="py-3 px-4 text-right"><x-soft-badge :status="$interv->statut" /></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 text-center text-slate-400 italic">Aucune intervention enregistrée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1e2530] mb-4">À suivre prochainement</h3>
                <div class="space-y-3">
                    @forelse($prochaines as $interv)
                        <a href="{{ route('client.interventions.show', $interv) }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-calendar-days text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-[#1e2530] truncate">{{ $interv->chantier?->nom ?? $interv->code_intervention }}</p>
                                <p class="text-[11px] text-slate-400">{{ $interv->date_prevue_debut ? $interv->date_prevue_debut->format('d/m/Y H:i') : 'Date à confirmer' }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 italic">Aucune intervention planifiée à venir.</p>
                    @endforelse

                    @if($dernierRapport)
                        <a href="{{ route('client.rapports.show', $dernierRapport) }}" class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-slate-50 transition border-t border-slate-100 pt-4 mt-1">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-file-pdf text-sm"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-[#1e2530] truncate">Dernier rapport disponible</p>
                                <p class="text-[11px] text-slate-400">{{ $dernierRapport->created_at->format('d/m/Y') }} · {{ $dernierRapport->estValide() ? 'Validé' : 'En attente de validation' }}</p>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const sparklineOptions = (color) => ({
            type: 'line',
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: { x: { display: false }, y: { display: false } },
                elements: { point: { radius: 0 }, line: { tension: 0.4, borderWidth: 2 } },
            },
        });

        @foreach($kpiCards as $i => $kpi)
            @if($kpi['sparkline'])
                new Chart(document.getElementById('spark-{{ $i }}'), {
                    ...sparklineOptions('{{ $kpi['color'] }}'),
                    data: {
                        labels: {!! json_encode($evolution['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($kpi['sparkline']) !!},
                            borderColor: '{{ $kpi['color'] }}',
                            backgroundColor: '{{ $kpi['color'] }}22',
                            fill: true,
                        }],
                    },
                });
            @endif
        @endforeach

        new Chart(document.getElementById('donutStatuts'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($donutLabels) !!},
                datasets: [{
                    data: {!! json_encode($donutValues) !!},
                    backgroundColor: {!! json_encode($donutBg) !!},
                    borderWidth: 0,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '72%',
                plugins: { legend: { display: false } },
            },
        });

        new Chart(document.getElementById('evolutionChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($evolution['labels']) !!},
                datasets: [
                    {
                        label: 'Créées',
                        data: {!! json_encode($evolution['creees']) !!},
                        borderColor: '#10b981',
                        backgroundColor: '#10b98122',
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Terminées',
                        data: {!! json_encode($evolution['terminees']) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b11',
                        fill: true,
                        tension: 0.4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, precision: 0 } },
                },
            },
        });
    </script>
</x-client-layout>
