<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-extrabold text-lg text-slate-900 leading-tight">Registre des Interventions</h2>
                <p class="text-xs text-slate-500 mt-0.5">Supervision et gestion complète de toutes les missions terrain</p>
            </div>
            @can('create interventions')
                <a href="{{ route('interventions.create') }}" class="ui-btn ui-btn-primary text-xs py-2 px-3.5 shadow-sm">
                    <i class="fas fa-plus"></i>
                    <span>Nouvelle Intervention</span>
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Barre d'outils et filtres Enterprise -->
        <div class="ui-card p-4 sm:p-5">
            <form method="GET" action="{{ route('interventions.index') }}" class="flex flex-col md:flex-row md:items-end gap-3 sm:gap-4">
                
                <!-- Recherche libre -->
                <div class="flex-1 min-w-[200px]">
                    <label for="search" class="ui-label flex items-center gap-1.5">
                        <i class="fas fa-search text-indigo-600"></i>
                        <span>Recherche</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ $search ?? '' }}" 
                               placeholder="Code, Client, Chantier, Technicien..."
                               class="ui-input pl-9 text-xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-search text-xs"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Filtre Statut -->
                <div class="w-full md:w-48">
                    <label for="statut" class="ui-label">Statut</label>
                    <select name="statut" id="statut" class="ui-input text-xs">
                        <option value="">Tous les statuts</option>
                        @foreach (['Planifiee', 'Acceptee', 'En cours', 'Formulaire rempli', 'Suspendue', 'Terminee', 'Validee', 'Annulee'] as $s)
                            <option value="{{ $s }}" @selected(($statut ?? '') == $s)>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Priorité -->
                <div class="w-full md:w-44">
                    <label for="priorite" class="ui-label">Priorité</label>
                    <select name="priorite" id="priorite" class="ui-input text-xs">
                        <option value="">Toutes les priorités</option>
                        @foreach (['Faible', 'Normale', 'Haute', 'Urgente'] as $p)
                            <option value="{{ $p }}" @selected(($priorite ?? '') == $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Boutons d'action des filtres -->
                <div class="flex items-center gap-2 pt-1 md:pt-0">
                    <button type="submit" class="ui-btn ui-btn-primary text-xs py-2 px-4 flex-1 md:flex-none">
                        <i class="fas fa-filter"></i>
                        <span>Filtrer</span>
                    </button>
                    @if($search || $statut || $priorite)
                        <a href="{{ route('interventions.index') }}" class="ui-btn ui-btn-secondary text-xs py-2 px-3 text-center" title="Réinitialiser les filtres">
                            <i class="fas fa-times"></i>
                            <span class="hidden sm:inline">Effacer</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Container Enterprise -->
        <div class="ui-table-container">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                        <i class="fas fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Missions Répertoriées</h3>
                        <p class="text-[11px] text-slate-500">{{ $interventions->total() }} intervention(s) au total</p>
                    </div>
                </div>

                @if($interventions->hasPages())
                    <div class="text-[11px] font-mono text-slate-500">
                        Page {{ $interventions->currentPage() }} sur {{ $interventions->lastPage() }}
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Type Mission</th>
                            <th>Chantier / Client</th>
                            <th>Technicien</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th>Date Planifiée</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($interventions as $intervention)
                            <tr>
                                <!-- Référence -->
                                <td>
                                    <a href="{{ route('interventions.show', $intervention) }}" class="inline-flex items-center gap-2 group">
                                        <span class="font-mono font-bold text-xs text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-100 group-hover:bg-indigo-100 transition">
                                            {{ $intervention->code_intervention }}
                                        </span>
                                    </a>
                                </td>

                                <!-- Type -->
                                <td>
                                    <span class="text-xs font-semibold text-slate-800">
                                        {{ $intervention->typeIntervention->nom ?? 'Standard' }}
                                    </span>
                                </td>

                                <!-- Chantier / Client -->
                                <td>
                                    <div class="text-xs font-semibold text-slate-900">{{ $intervention->chantier->nom ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $intervention->chantier->client->nom ?? '-' }}</div>
                                </td>

                                <!-- Technicien -->
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($intervention->technicien->name ?? 'NA', 0, 2)) }}
                                        </div>
                                        <span class="text-xs {{ $intervention->technicien ? 'text-slate-800 font-medium' : 'text-slate-400 italic' }}">
                                            {{ $intervention->technicien ? ($intervention->technicien->prenom ?? '') . ' ' . $intervention->technicien->name : 'Non assigné' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Priorité -->
                                <td>
                                    @php
                                        $priMap = [
                                            'Faible' => 'bg-slate-100 text-slate-600 border-slate-200',
                                            'Normale' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'Haute' => 'bg-amber-50 text-amber-700 border-amber-200 font-semibold',
                                            'Urgente' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $priMap[$intervention->priorite] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $intervention->priorite }}
                                    </span>
                                </td>

                                <!-- Statut -->
                                <td>
                                    <x-soft-badge :status="$intervention->statut" />
                                </td>

                                <!-- Date planifiée -->
                                <td class="font-mono text-xs text-slate-600 whitespace-nowrap">
                                    {{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '—' }}
                                </td>

                                <!-- Actions -->
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('interventions.show', $intervention) }}" class="ui-btn ui-btn-secondary text-xs py-1 px-2.5" title="Consulter la fiche">
                                            <i class="fas fa-eye text-indigo-600"></i>
                                            <span>Fiche</span>
                                        </a>
                                        @can('edit interventions')
                                            <a href="{{ route('interventions.edit', $intervention) }}" class="ui-btn ui-btn-ghost text-xs py-1 px-2 text-slate-500 hover:text-slate-900" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center text-base mx-auto mb-3">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">Aucune intervention trouvée</p>
                                    <p class="text-xs text-slate-500 mt-1">Ajustez vos filtres de recherche ou créez une nouvelle mission.</p>
                                    @can('create interventions')
                                        <a href="{{ route('interventions.create') }}" class="ui-btn ui-btn-primary text-xs py-2 px-4 mt-4 inline-flex">
                                            <i class="fas fa-plus"></i>
                                            <span>Créer une intervention</span>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($interventions->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50/70">
                    {{ $interventions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
