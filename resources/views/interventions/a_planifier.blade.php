<x-app-layout>
    <div class="space-y-6">
        <!-- Titre + Badge -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Interventions à Planifier</h1>
                <p class="text-xs text-[#5A607F] mt-1">Interventions créées par le Commercial en attente d'affectation à un technicien</p>
            </div>
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-[4px] bg-[#FFF3DE] border border-[#FFE7B8] text-[#B98900] text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-[#B98900] {{ $interventions->total() > 0 ? 'animate-pulse' : '' }}"></span>
                {{ $interventions->total() }} en attente
            </span>
        </div>

        <!-- Alertes session -->
        @if(session('success'))
            <div class="p-4 rounded-[6px] bg-[#E3FBF0] border border-[#C4F8E2] text-[#06A561] text-xs font-semibold flex items-center gap-3">
                <i class="fas fa-check-circle text-[#06A561] text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Onglets rapides par priorité -->
        <div class="flex items-center gap-6 border-b border-[#E6E9F4]">
            @foreach([
                ['label' => 'Toutes', 'value' => ''],
                ['label' => 'Urgentes', 'value' => 'Urgente'],
                ['label' => 'Hautes', 'value' => 'Haute'],
                ['label' => 'Normales', 'value' => 'Normale'],
            ] as $tab)
                @php $isActiveTab = ($priorite ?? '') === $tab['value']; @endphp
                <a href="{{ route('interventions.a-planifier', array_filter(['search' => $search ?? null, 'priorite' => $tab['value'] ?: null])) }}"
                   class="pb-3 -mb-px text-sm font-semibold border-b-2 transition {{ $isActiveTab ? 'border-[#1E5EFF] text-[#1E5EFF]' : 'border-transparent text-[#5A607F] hover:text-[#131523]' }}">
                    {{ $tab['label'] }}
                </a>
            @endforeach
        </div>

        <!-- Carte : filtres + table -->
        <div class="ds-card-elevated overflow-hidden">
            <!-- Toolbar de filtres -->
            <div class="p-5 border-b border-[#E6E9F4]">
                <form method="GET" action="{{ route('interventions.a-planifier') }}" class="flex flex-col md:flex-row gap-3 items-end flex-wrap">
                    <div class="flex-1 min-w-[220px]">
                        <label class="block text-[11px] font-bold text-[#A1A7C4] uppercase tracking-wider mb-1.5">Rechercher</label>
                        <div class="flex items-center gap-2.5 px-3.5 py-2 rounded-[4px] bg-[#F5F6FA] border border-[#E6E9F4] focus-within:border-[#1E5EFF] transition">
                            <i class="fas fa-search text-[#A1A7C4] text-sm shrink-0"></i>
                            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Code, chantier, client..."
                                class="bg-transparent border-0 outline-none focus:ring-0 p-0 text-sm text-[#131523] placeholder-[#A1A7C4] w-full">
                        </div>
                    </div>

                    <div class="min-w-[200px]">
                        <label class="block text-[11px] font-bold text-[#A1A7C4] uppercase tracking-wider mb-1.5">Priorité</label>
                        <select name="priorite" class="w-full rounded-[4px] bg-white border border-[#E6E9F4] text-sm px-3.5 py-2 text-[#131523] focus:border-[#1E5EFF] focus:ring-0">
                            <option value="">Toutes priorités</option>
                            @foreach(['Urgente', 'Haute', 'Normale', 'Faible'] as $p)
                                <option value="{{ $p }}" @selected(($priorite ?? '') === $p)>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="ds-btn ds-btn-primary ds-btn-sm">Filtrer</button>
                        @if($search || $priorite)
                            <a href="{{ route('interventions.a-planifier') }}" class="ds-btn ds-btn-white ds-btn-sm">Réinitialiser</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type &amp; Chantier</th>
                            <th>Client</th>
                            <th>Priorité</th>
                            <th>Créé par</th>
                            <th>Date de création</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($interventions as $intervention)
                            @php
                                $prioriteConfig = [
                                    'Urgente' => ['badge' => 'ds-badge-light-danger', 'icon' => '#FDE3E6', 'iconText' => '#F0142F'],
                                    'Haute'   => ['badge' => 'ds-badge-light-warning', 'icon' => '#FFF3DE', 'iconText' => '#B98900'],
                                    'Normale' => ['badge' => 'ds-badge-light-primary', 'icon' => '#EAF0FF', 'iconText' => '#1E5EFF'],
                                    'Faible'  => ['badge' => 'ds-badge-light-secondary', 'icon' => '#F5F6FA', 'iconText' => '#5A607F'],
                                ];
                                $pc = $prioriteConfig[$intervention->priorite] ?? $prioriteConfig['Normale'];
                                $isUrgent = $intervention->priorite === 'Urgente';
                            @endphp
                            <tr class="{{ $isUrgent ? 'bg-[#FDE3E6]/20' : '' }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-[4px] flex items-center justify-center shrink-0" style="background-color: {{ $pc['icon'] }}; color: {{ $pc['iconText'] }};">
                                            <i class="fas fa-calendar-plus text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-[#1E5EFF]">{{ $intervention->code_intervention }}</p>
                                            <p class="text-[11px] text-[#A1A7C4]">Statut : <span class="font-semibold text-[#B98900]">{{ $intervention->statut }}</span></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-bold text-[#131523]">{{ $intervention->typeIntervention->nom ?? '—' }}</p>
                                    <p class="text-[11px] text-[#A1A7C4]">{{ $intervention->chantier->nom ?? '—' }}</p>
                                </td>
                                <td class="text-xs font-medium text-[#131523]">
                                    {{ $intervention->chantier->client->nom ?? '—' }}
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge-sm {{ $pc['badge'] }}">{{ $intervention->priorite }}</span>
                                </td>
                                <td>
                                    <p class="text-xs font-semibold text-[#131523]">{{ $intervention->createur->name ?? '—' }}</p>
                                    <p class="text-[11px] text-[#A1A7C4]">Commercial</p>
                                </td>
                                <td class="text-xs text-[#5A607F] whitespace-nowrap">
                                    {{ $intervention->created_at->format('d/m/Y') }}
                                    <p class="text-[11px] text-[#A1A7C4]">{{ $intervention->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('interventions.planifier', $intervention) }}" class="ds-btn ds-btn-primary ds-btn-sm">
                                        <i class="fas fa-calendar-plus"></i>
                                        <span>Planifier</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="text-center py-16">
                                        <div class="w-14 h-14 rounded-full bg-[#E3FBF0] flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-check text-xl text-[#06A561]"></i>
                                        </div>
                                        <p class="text-sm font-bold text-[#131523]">Aucune intervention en attente de planification</p>
                                        <p class="text-xs text-[#A1A7C4] mt-1">Toutes les interventions ont été planifiées et affectées.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($interventions->hasPages())
                <div class="px-6 py-4 border-t border-[#E6E9F4] flex items-center justify-between flex-wrap gap-3">
                    <p class="text-xs text-[#A1A7C4]">{{ $interventions->total() }} résultat(s)</p>
                    {{ $interventions->links() }}
                </div>
            @else
                <div class="px-6 py-4 border-t border-[#E6E9F4]">
                    <p class="text-xs text-[#A1A7C4]">{{ $interventions->total() }} résultat(s)</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
