<x-app-layout>
    <x-slot name="header">Planning des Interventions</x-slot>

    <div class="space-y-6">

        {{-- En-tête --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Planning de Maintenance & Dispatch</h3>
                <p class="text-sm text-gray-500">{{ $interventions->count() }} intervention(s) planifiée(s) ce mois • {{ $techniciens->count() }} technicien(s) actif(s)</p>
            </div>

            {{-- Navigation mois --}}
            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $prevMonth = \Carbon\Carbon::create($year, $month, 1)->subMonth();
                    $nextMonth = \Carbon\Carbon::create($year, $month, 1)->addMonth();
                @endphp
                <a href="{{ route('planning.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300 text-sm font-bold rounded-xl border border-indigo-100 dark:border-indigo-900 capitalize min-w-[140px] text-center">
                    {{ $monthLabel }}
                </span>
                <a href="{{ route('planning.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                   class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('planning.index') }}"
                   class="px-3 py-1.5 text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg hover:bg-slate-200 transition">
                    Aujourd'hui
                </a>
            </div>
        </div>

        {{-- Légende priorités --}}
        <div class="flex flex-wrap gap-3 text-xs font-medium">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-red-500 inline-block"></span> Urgente</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-orange-400 inline-block"></span> Haute</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-indigo-500 inline-block"></span> Normale</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-slate-400 inline-block"></span> Faible</span>
            <span class="ml-auto text-gray-400">Total ce mois : <strong class="text-gray-700 dark:text-gray-200">{{ $interventions->count() }}</strong></span>
        </div>

        {{-- Calendrier --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            {{-- Header jours --}}
            <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-900/60 border-b border-gray-200 dark:border-gray-800">
                @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $jour)
                    <div class="py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $jour }}</div>
                @endforeach
            </div>

            {{-- Grille --}}
            <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-800">
                {{-- Cellules vides avant le 1er --}}
                @for($pad = 0; $pad < $firstDayOffset; $pad++)
                    <div class="bg-gray-50 dark:bg-gray-900/30 min-h-28 p-2"></div>
                @endfor

                {{-- Jours du mois --}}
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $isToday = ($day == now()->day && $month == now()->month && $year == now()->year);
                        $dayInterventions = $interventionsByDay->get($day, collect());
                    @endphp
                    <div class="bg-white dark:bg-gray-900 min-h-28 p-2 flex flex-col gap-1 relative {{ $isToday ? 'ring-2 ring-inset ring-indigo-400' : '' }}">
                        <span class="text-xs font-bold {{ $isToday ? 'bg-indigo-600 text-white rounded-full w-5 h-5 flex items-center justify-center' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $day }}
                        </span>

                        @foreach($dayInterventions as $intervention)
                            @php
                                $colors = match($intervention->priorite) {
                                    'Urgente' => 'bg-red-50 border-red-300 text-red-700 dark:bg-red-950/30 dark:border-red-800 dark:text-red-400',
                                    'Haute'   => 'bg-orange-50 border-orange-300 text-orange-700 dark:bg-orange-950/30 dark:border-orange-800 dark:text-orange-400',
                                    'Normale' => 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-950/30 dark:border-indigo-800 dark:text-indigo-400',
                                    default   => 'bg-slate-50 border-slate-200 text-slate-600 dark:bg-slate-800/30 dark:border-slate-700 dark:text-slate-400',
                                };
                            @endphp
                            <a href="{{ route('interventions.show', $intervention->id) }}"
                               class="block p-1.5 border rounded-md text-[10px] font-semibold truncate leading-tight {{ $colors }} hover:opacity-80 transition"
                               title="{{ $intervention->code_intervention }} — {{ $intervention->typeIntervention?->nom ?? 'N/A' }}">
                                {{ $intervention->code_intervention }}
                                @if($intervention->technicien)
                                    <span class="block font-normal opacity-75">{{ $intervention->technicien->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endfor

                {{-- Cellules vides après le dernier jour (compléter la dernière ligne à 7) --}}
                @php $trailing = (7 - (($firstDayOffset + $daysInMonth) % 7)) % 7; @endphp
                @for($pad = 0; $pad < $trailing; $pad++)
                    <div class="bg-gray-50 dark:bg-gray-900/30 min-h-28 p-2"></div>
                @endfor
            </div>
        </div>

        {{-- Liste des interventions du mois --}}
        @if($interventions->count() > 0)
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h4 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                    <svg class="h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Liste détaillée — {{ $monthLabel }}
                </h4>
                <span class="text-xs text-gray-400">{{ $interventions->count() }} entrée(s)</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/40 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Code</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Technicien</th>
                            <th class="px-4 py-3 text-left">Chantier</th>
                            <th class="px-4 py-3 text-left">Date prévue</th>
                            <th class="px-4 py-3 text-left">Priorité</th>
                            <th class="px-4 py-3 text-left">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($interventions as $intervention)
                            @php
                                $priorityBadge = match($intervention->priorite) {
                                    'Urgente' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                    'Haute'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                    'Normale' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                    default   => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                };
                                $statutBadge = match($intervention->statut) {
                                    'Planifiee'        => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                    'Acceptee'         => 'bg-blue-100 text-blue-700',
                                    'En cours'         => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'Formulaire rempli'=> 'bg-purple-100 text-purple-700',
                                    'Terminee'         => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                    'Annulee'          => 'bg-red-100 text-red-700',
                                    default            => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                <td class="px-4 py-3">
                                    <a href="{{ route('interventions.show', $intervention->id) }}"
                                       class="font-mono text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                                        {{ $intervention->code_intervention }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ $intervention->typeIntervention?->nom ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $intervention->technicien?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs truncate max-w-[150px]">{{ $intervention->chantier?->nom ?? '—' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                    {{ $intervention->date_prevue_debut?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $priorityBadge }}">
                                        {{ $intervention->priorite }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $statutBadge }}">
                                        {{ $intervention->statut }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-12 text-center">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-400 font-medium">Aucune intervention planifiée pour <span class="capitalize">{{ $monthLabel }}</span>.</p>
                @can('create interventions')
                    <a href="{{ route('interventions.create') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Créer une intervention
                    </a>
                @endcan
            </div>
        @endif
    </div>
</x-app-layout>
