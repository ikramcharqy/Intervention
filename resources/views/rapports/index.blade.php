<x-app-layout>
    <div class="space-y-6">
        <!-- Titre -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Rapports & Validation</h1>
            <p class="text-xs text-[#5A607F] mt-1">Consultez, validez et exportez en PDF les comptes-rendus d'interventions certifiés par vos techniciens</p>
        </div>

        <!-- KPI bar -->
        <div class="ds-stat-bar">
            <div class="ds-stat-block">
                <div>
                    <p class="ds-stat-label">Total générés</p>
                    <p class="ds-stat-value">{{ $stats['total'] }}</p>
                </div>
                <span class="ds-stat-icon ds-stat-icon-primary"><i class="ti ti-file-text"></i></span>
            </div>
            <div class="ds-stat-block">
                <div>
                    <p class="ds-stat-label">Certifiés PDF</p>
                    <p class="ds-stat-value">{{ $stats['certifies'] }}</p>
                </div>
                <span class="ds-stat-icon ds-stat-icon-success"><i class="ti ti-file-check"></i></span>
            </div>
            <div class="ds-stat-block">
                <div>
                    <p class="ds-stat-label">En révision</p>
                    <p class="ds-stat-value">{{ $stats['enRevision'] }}</p>
                </div>
                <span class="ds-stat-icon ds-stat-icon-warning"><i class="ti ti-clock-edit"></i></span>
            </div>
            <div class="ds-stat-block">
                <div>
                    <p class="ds-stat-label">Taux de certification</p>
                    <p class="ds-stat-value">{{ $stats['taux'] }}%</p>
                </div>
                <span class="ds-stat-icon ds-stat-icon-secondary"><i class="ti ti-chart-pie"></i></span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Colonne principale : recherche + table -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recherche & filtres -->
                <div class="ds-card-elevated p-5">
                    <form method="GET" action="{{ route('rapports.index') }}" class="flex flex-col md:flex-row gap-3 items-center">
                        <div class="flex-1 w-full flex items-center gap-2.5 px-3.5 py-2 rounded-[4px] bg-[#F5F6FA] border border-[#E6E9F4] focus-within:border-[#1E5EFF] transition">
                            <i class="fas fa-search text-[#A1A7C4] text-sm shrink-0"></i>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par code, client, technicien..."
                                class="bg-transparent border-0 outline-none focus:ring-0 p-0 text-sm text-[#131523] placeholder-[#A1A7C4] w-full">
                        </div>
                        <div class="flex items-center gap-2 w-full md:w-auto">
                            <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Filtrer</button>
                            @if(isset($search) && $search)
                                <a href="{{ route('rapports.index') }}" class="ds-btn ds-btn-white ds-btn-sm">Réinitialiser</a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Table des rapports -->
                <div class="ds-card-elevated overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="ds-table">
                            <thead>
                                <tr>
                                    <th>Code / Intervention</th>
                                    <th>Client &amp; Chantier</th>
                                    <th>Technicien</th>
                                    <th>Horodatage</th>
                                    <th>Conformité</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rapports as $rapport)
                                    <tr>
                                        <td>
                                            <span class="font-bold text-xs text-[#1E5EFF]">{{ $rapport->intervention->code_intervention ?? '-' }}</span>
                                            <p class="text-[11px] text-[#A1A7C4] mt-0.5">{{ $rapport->intervention->typeIntervention->nom ?? 'Technique' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs font-bold text-[#131523]">{{ $rapport->intervention->chantier->client->nom ?? '-' }}</p>
                                            <p class="text-[11px] text-[#A1A7C4]">{{ $rapport->intervention->chantier->nom ?? '-' }}</p>
                                        </td>
                                        <td class="text-xs font-semibold text-[#131523]">
                                            {{ $rapport->intervention->technicien->name ?? '-' }}
                                        </td>
                                        <td class="text-xs whitespace-nowrap">
                                            <p class="text-[#5A607F]">{{ $rapport->created_at ? $rapport->created_at->format('d/m/Y H:i') : '-' }}</p>
                                            <p class="text-[11px] text-[#A1A7C4]">{{ $rapport->intervention->duree_reelle ? $rapport->intervention->duree_reelle.' min' : 'Standard' }}</p>
                                        </td>
                                        <td>
                                            <span class="ds-badge ds-badge-sm ds-badge-light-{{ $rapport->statutBadge['theme'] }}">
                                                {{ $rapport->statutBadge['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('rapports.show', $rapport) }}" class="ds-btn ds-btn-white ds-btn-sm">Consulter</a>
                                                <a href="{{ route('rapports.pdf', $rapport) }}" target="_blank" class="ds-btn ds-btn-danger ds-btn-sm">
                                                    <i class="fas fa-file-pdf"></i> Export PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="text-center py-16">
                                                <div class="w-14 h-14 rounded-full bg-[#F5F6FA] flex items-center justify-center mx-auto mb-4">
                                                    <i class="fas fa-file-lines text-xl text-[#A1A7C4]"></i>
                                                </div>
                                                <p class="text-sm font-bold text-[#131523]">Aucun rapport d'intervention trouvé</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($rapports->hasPages())
                        <div class="px-6 py-4 border-t border-[#E6E9F4] flex items-center justify-between flex-wrap gap-3">
                            <p class="text-xs text-[#A1A7C4]">{{ $rapports->total() }} résultat(s)</p>
                            {{ $rapports->links() }}
                        </div>
                    @else
                        <div class="px-6 py-4 border-t border-[#E6E9F4]">
                            <p class="text-xs text-[#A1A7C4]">{{ $rapports->total() }} résultat(s)</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Colonne latérale : Taux de certification -->
            <div class="ds-card-elevated overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E6E9F4]">
                    <h3 class="text-sm font-bold text-[#131523]">Taux de certification</h3>
                </div>
                <div class="p-6 flex flex-col items-center">
                    <div class="ds-progress-ring">
                        <canvas id="chart-certification-ring" width="140" height="140"></canvas>
                        <div class="ds-progress-ring-label">
                            <span class="ds-progress-ring-value">{{ $stats['taux'] }}%</span>
                            <span class="ds-progress-ring-caption">Certifiés</span>
                        </div>
                    </div>
                    <div class="w-full mt-6 space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-[#5A607F]"><span class="w-2.5 h-2.5 rounded-full bg-[#1FD286]"></span> Certifiés</span>
                            <span class="font-bold text-[#131523]">{{ $stats['certifies'] }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 text-[#5A607F]"><span class="w-2.5 h-2.5 rounded-full bg-[#F99600]"></span> En révision</span>
                            <span class="font-bold text-[#131523]">{{ $stats['enRevision'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($stats['total'] > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const ctx = document.getElementById('chart-certification-ring');
                if (!ctx) return;
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Certifiés', 'En révision'],
                        datasets: [{
                            data: [{{ $stats['certifies'] }}, {{ $stats['enRevision'] }}],
                            backgroundColor: ['#1FD286', '#F99600'],
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        cutout: '75%',
                        plugins: { legend: { display: false }, tooltip: { enabled: true } },
                    },
                });
            });
        </script>
    @endif
</x-app-layout>
