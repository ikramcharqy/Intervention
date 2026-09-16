<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="space-y-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-[28px] font-extrabold text-[#181C32] font-heading">Bonjour, {{ Auth::user()->name }}</h1>
                <p class="text-sm text-[#A1A5B7] mt-1">Suivez vos performances commerciales en un coup d'œil.</p>
            </div>

            <!-- Sélecteur de période -->
            <form method="GET" action="{{ route('commercial.dashboard') }}" id="period-form" class="flex items-center gap-2.5">
                <select name="period" onchange="document.getElementById('custom-range').classList.toggle('hidden', this.value !== 'custom'); this.value !== 'custom' && this.form.submit();"
                        class="text-xs font-semibold text-[#181C32] border border-[#EFF2F5] rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <option value="week" {{ $period === 'week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Ce mois</option>
                    <option value="quarter" {{ $period === 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Personnalisé</option>
                </select>
                <div id="custom-range" class="flex items-center gap-1.5 {{ $period === 'custom' ? '' : 'hidden' }}">
                    <input type="date" name="from" value="{{ request('from') }}" class="text-xs border border-[#EFF2F5] rounded-lg px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <span class="text-[#A1A5B7] text-xs">→</span>
                    <input type="date" name="to" value="{{ request('to') }}" class="text-xs border border-[#EFF2F5] rounded-lg px-2.5 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    <button type="submit" class="text-xs font-bold text-emerald-600 px-2">OK</button>
                </div>
            </form>
        </div>

        <!-- Carte KPI unique, 3 colonnes séparées par un trait -->
        <div class="metronic-card divide-y sm:divide-y-0 sm:divide-x divide-[#EFF2F5] flex flex-col sm:flex-row">
            <div class="p-6 flex-1 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-[#5E6278]">CA de la Période</p>
                    <p class="text-2xl font-extrabold text-[#181C32] mt-2 font-heading">{{ format_montant($revenueTile['value'], 0) }}</p>
                    @include('commercial.partials.kpi-variation', ['tile' => $revenueTile, 'periodLabel' => $periodLabel])
                </div>
                <div class="w-24 h-14 shrink-0">
                    <canvas id="sparkRevenue"></canvas>
                </div>
            </div>

            <div class="p-6 flex-1">
                <p class="text-xs font-semibold text-[#5E6278]">Devis Acceptés</p>
                <p class="text-2xl font-extrabold text-[#181C32] mt-2 font-heading">{{ number_format($salesTile['value']) }}</p>
                @include('commercial.partials.kpi-variation', ['tile' => $salesTile, 'periodLabel' => $periodLabel])
            </div>

            <div class="p-6 flex-1">
                <p class="text-xs font-semibold text-[#5E6278]">Nouveaux Prospects</p>
                <p class="text-2xl font-extrabold text-[#181C32] mt-2 font-heading">{{ number_format($prospectsTile['value']) }}</p>
                @include('commercial.partials.kpi-variation', ['tile' => $prospectsTile, 'periodLabel' => $periodLabel])
            </div>
        </div>

        <!-- Actions Requises -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <a href="{{ route('commercial.devis.index', ['relance' => 1]) }}" class="metronic-card p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-[#5E6278]">Devis à relancer</p>
                    <p class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $devisARelancer }}</p>
                    <p class="text-[11px] text-[#A1A5B7] mt-1">Envoyés depuis plus de {{ \App\Http\Controllers\Commercial\DashboardController::DEVIS_RELANCE_JOURS }} jours, sans réponse</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-[#F5F8FA] text-[#5E6278] flex items-center justify-center shrink-0">
                    <i class="fas fa-clock-rotate-left text-sm"></i>
                </div>
            </a>

            <a href="{{ route('prospects.index', ['inactifs' => 1]) }}" class="metronic-card p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-[#5E6278]">Prospects inactifs</p>
                    <p class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $prospectsInactifs }}</p>
                    <p class="text-[11px] text-[#A1A5B7] mt-1">Sans activité depuis plus de {{ \App\Http\Controllers\Commercial\DashboardController::PROSPECT_INACTIVITE_JOURS }} jours</p>
                </div>
                <div class="h-10 w-10 rounded-lg bg-[#F5F8FA] text-[#5E6278] flex items-center justify-center shrink-0">
                    <i class="fas fa-user-clock text-sm"></i>
                </div>
            </a>
        </div>

        <!-- Derniers Devis (équivalent "Recent Transactions") -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-extrabold text-[#181C32] font-heading">Derniers Devis</h2>
                <div class="flex items-center gap-5">
                    <button type="button" class="flex items-center gap-1.5 text-xs font-semibold text-[#5E6278] hover:text-[#181C32]">
                        <i class="fas fa-search text-[11px]"></i> Rechercher
                    </button>
                    <a href="{{ route('commercial.devis.index') }}" class="flex items-center gap-1.5 text-xs font-semibold text-[#5E6278] hover:text-[#181C32]">
                        <i class="fas fa-arrow-up-from-bracket text-[11px]"></i> Exporter
                    </a>
                    <button type="button" class="flex items-center gap-1.5 text-xs font-semibold text-[#5E6278] hover:text-[#181C32]">
                        <i class="fas fa-filter text-[11px]"></i> Filtrer
                    </button>
                </div>
            </div>

            <div class="metronic-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-[#F9F9FB] text-[#A1A5B7] uppercase font-bold text-[10px]">
                                <th class="py-3 pl-6 w-10"><input type="checkbox" class="rounded border-[#D9D9D9]"></th>
                                <th class="py-3">Devis</th>
                                <th class="py-3">Date</th>
                                <th class="py-3">Montant</th>
                                <th class="py-3">Statut</th>
                                <th class="py-3 pr-6 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFF2F5]">
                            @forelse($recentDevis as $i => $d)
                                @php
                                    $name = $d->client?->nom ?? $d->prospect?->nom_contact ?? 'N/A';
                                    $badge = match($d->statut) {
                                        'Accepté', 'Accepte', 'Validé' => 'bg-emerald-50 text-emerald-600',
                                        'Refusé', 'Refuse', 'Annulé' => 'bg-rose-50 text-rose-600',
                                        default => 'bg-[#F5F8FA] text-[#5E6278]'
                                    };
                                @endphp
                                <tr class="hover:bg-[#F9F9FB] transition">
                                    <td class="py-3.5 pl-6"><input type="checkbox" class="rounded border-[#D9D9D9]"></td>
                                    <td class="py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-[11px] shrink-0 bg-emerald-50 text-emerald-700">
                                                {{ strtoupper(substr($name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-[#181C32] truncate">{{ $d->reference }}</p>
                                                <p class="text-[11px] text-[#A1A5B7] truncate">{{ $name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3.5 text-[#5E6278]">{{ $d->date_emission?->format('d/m/Y H:i') }}</td>
                                    <td class="py-3.5 font-semibold text-[#181C32]">{{ format_montant($d->montant_ttc, 0) }}</td>
                                    <td class="py-3.5">
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $badge }}">{{ $d->statut }}</span>
                                    </td>
                                    <td class="py-3.5 pr-6 text-right">
                                        <a href="{{ route('commercial.devis.show', $d) }}" class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[#A1A5B7] hover:bg-[#F5F8FA] hover:text-[#181C32] transition">
                                            <i class="fas fa-chevron-right text-[10px]"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-8 text-center text-[#A1A5B7] italic">Aucun devis pour l'instant.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistiques complémentaires -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Évolution des Devis Émis -->
            <div class="metronic-card p-6 lg:col-span-2">
                <h2 class="text-sm font-bold text-[#181C32] font-heading mb-1">Évolution des Devis Émis</h2>
                <p class="text-xs text-[#A1A5B7] mb-4">Nombre de devis créés par mois, 6 derniers mois</p>
                <div class="relative h-52">
                    <canvas id="chartDevisTendance"></canvas>
                </div>
            </div>

            <!-- Répartition par statut -->
            <div class="metronic-card p-6">
                <h2 class="text-sm font-bold text-[#181C32] font-heading mb-1">Répartition des Devis</h2>
                <p class="text-xs text-[#A1A5B7] mb-4">Par statut</p>
                @if($parStatutDevis->isNotEmpty())
                    <div class="relative h-36">
                        <canvas id="chartDevisStatut"></canvas>
                    </div>
                    <div class="mt-5 space-y-2.5">
                        @php
                            $statutColors = ['Brouillon' => '#94A3B8', 'Envoyé' => '#94A3B8', 'En attente' => '#94A3B8', 'Accepté' => '#10B981', 'Accepte' => '#10B981', 'Validé' => '#10B981', 'Refusé' => '#F43F5E', 'Refuse' => '#F43F5E', 'Annulé' => '#F43F5E'];
                            $totalDevisStatut = max(1, $parStatutDevis->sum());
                        @endphp
                        @foreach($parStatutDevis as $label => $value)
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-2 text-[#5E6278] font-semibold">
                                    <span class="w-2 h-2 rounded-full" style="background-color: {{ $statutColors[$label] ?? '#94A3B8' }};"></span>
                                    {{ $label }}
                                </span>
                                <span class="font-bold text-[#181C32]">{{ round(($value / $totalDevisStatut) * 100) }}%</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="h-36 flex items-center justify-center text-[#A1A5B7] text-xs">Aucun devis pour l'instant.</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <!-- Top Clients -->
            <div class="metronic-card p-6">
                <h2 class="text-sm font-bold text-[#181C32] font-heading mb-4">Top Clients</h2>
                <div class="space-y-4">
                    @php $rankColors = ['#059669', '#10B981', '#34D399', '#6EE7B7', '#A7F3D0']; @endphp
                    @forelse($topClients as $tc)
                        <a href="{{ $tc->client ? route('clients.show', $tc->client) : '#' }}" class="block {{ $tc->client ? 'hover:opacity-80' : 'pointer-events-none' }} transition">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-semibold text-[#3F4254]">{{ $tc->client?->nom ?? 'Client supprimé' }}</span>
                                <span class="text-[#A1A5B7]">{{ format_montant($tc->total, 0) }}</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-[#F5F8FA] overflow-hidden">
                                <div class="h-full rounded-full" style="width: {{ ($tc->total / $topClientMax) * 100 }}%; background-color: {{ $rankColors[$loop->index % 5] }};"></div>
                            </div>
                        </a>
                    @empty
                        <p class="text-xs text-[#A1A5B7] italic text-center py-6">Aucun devis accepté pour l'instant.</p>
                    @endforelse
                </div>
            </div>

            <!-- Prospects Récents -->
            <div class="metronic-card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-[#181C32] font-heading">Prospects Récents</h2>
                    <a href="{{ route('prospects.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">Voir tout →</a>
                </div>
                <div class="space-y-3.5">
                    @forelse($recentProspects as $p)
                        <a href="{{ route('prospects.show', $p) }}" class="flex items-center justify-between gap-2 hover:opacity-80 transition">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-[#181C32] truncate">{{ $p->nom_contact }}</p>
                                <p class="text-[11px] text-[#5E6278] truncate">{{ $p->nom_entreprise ?? 'Particulier' }}</p>
                            </div>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#F5F8FA] text-[#5E6278] shrink-0">{{ $p->statut }}</span>
                        </a>
                    @empty
                        <p class="text-xs text-[#A1A5B7] italic text-center py-6">Aucun prospect récent.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function() {
        new Chart(document.getElementById('chartDevisTendance'), {
            type: 'bar',
            data: {
                labels: @json($moisLabelsFr),
                datasets: [{
                    data: @json($devisTendanceSeries->values()),
                    backgroundColor: '#10B981',
                    borderRadius: 6,
                    maxBarThickness: 40,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#A1A5B7', font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { color: '#A1A5B7', font: { size: 11 }, precision: 0 }, grid: { color: '#F1F4F9' } }
                }
            }
        });

        @if($parStatutDevis->isNotEmpty())
        new Chart(document.getElementById('chartDevisStatut'), {
            type: 'doughnut',
            data: {
                labels: @json($parStatutDevis->keys()),
                datasets: [{
                    data: @json($parStatutDevis->values()),
                    backgroundColor: @json($parStatutDevis->keys()).map(label => ({
                        'Brouillon': '#94A3B8', 'Envoyé': '#94A3B8', 'En attente': '#94A3B8',
                        'Accepté': '#10B981', 'Accepte': '#10B981', 'Validé': '#10B981',
                        'Refusé': '#F43F5E', 'Refuse': '#F43F5E', 'Annulé': '#F43F5E',
                    }[label] || '#94A3B8')),
                    borderWidth: 3,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: { legend: { display: false } }
            }
        });
        @endif

        new Chart(document.getElementById('sparkRevenue'), {
            type: 'bar',
            data: {
                labels: @json($revenueSparkline->values()),
                datasets: [{
                    data: @json($revenueSparkline->values()),
                    backgroundColor: @json($revenueSparkline->values()).map((v, i, arr) => i === arr.length - 1 ? '#059669' : '#A7F3D0'),
                    borderRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: { x: { display: false }, y: { display: false } }
            }
        });
    })();
    </script>
</x-commercial-layout>
