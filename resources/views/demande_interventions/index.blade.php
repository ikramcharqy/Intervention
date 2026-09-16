<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-extrabold text-lg text-slate-900 leading-tight">
                    Demandes d'Intervention Clients
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Registre des demandes émises par les clients à qualifier ou planifier</p>
            </div>
            <a href="{{ route('demande-interventions.create') }}" class="ui-btn ui-btn-primary text-xs py-2 px-3.5 shadow-sm">
                <i class="fas fa-plus"></i>
                <span>Nouvelle Demande</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-3 shadow-xs">
                <i class="fas fa-check-circle text-emerald-600 text-sm shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="ui-table-container">
            <div class="p-4 sm:p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/70">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Demandes Reçues</h3>
                        <p class="text-[11px] text-slate-500">{{ $demandes->count() }} demande(s) enregistrée(s)</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="ui-table">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Objet</th>
                            <th>Priorité</th>
                            <th>Statut</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $demande)
                            <tr>
                                <!-- Référence -->
                                <td>
                                    <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-100 inline-block">
                                        {{ $demande->reference }}
                                    </span>
                                </td>

                                <!-- Client -->
                                <td>
                                    <div class="font-semibold text-slate-900 text-xs">{{ $demande->client->nom ?? '-' }}</div>
                                    @if($demande->chantier)
                                        <div class="text-[11px] text-slate-500">{{ $demande->chantier->nom }}</div>
                                    @endif
                                </td>

                                <!-- Objet -->
                                <td>
                                    <span class="text-slate-800 font-medium text-xs">{{ $demande->objet }}</span>
                                </td>

                                <!-- Priorité -->
                                <td>
                                    @php
                                        $priConfig = [
                                            'Urgente' => 'bg-rose-50 text-rose-700 border-rose-200 font-bold',
                                            'Haute'   => 'bg-amber-50 text-amber-700 border-amber-200 font-semibold',
                                            'Normale' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'Faible'  => 'bg-slate-100 text-slate-600 border-slate-200',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs border {{ $priConfig[$demande->priorite] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                        {{ $demande->priorite }}
                                    </span>
                                </td>

                                <!-- Statut -->
                                <td>
                                    <x-soft-badge :status="$demande->statut" />
                                </td>

                                <!-- Actions -->
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('demande-interventions.show', $demande) }}" 
                                           class="ui-btn {{ $demande->statut == 'En attente' ? 'ui-btn-primary' : 'ui-btn-secondary' }} text-xs py-1 px-3">
                                            @if($demande->statut == 'En attente')
                                                <i class="fas fa-check-circle text-xs"></i>
                                                <span>Qualifier / Traiter</span>
                                            @else
                                                <i class="fas fa-eye text-xs"></i>
                                                <span>Consulter</span>
                                            @endif
                                        </a>

                                        @if($demande->statut == 'En attente')
                                            <a href="{{ route('demande-interventions.edit', $demande) }}" class="ui-btn ui-btn-ghost text-xs py-1 px-2 text-slate-500 hover:text-slate-800" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($demande->devis->isNotEmpty())
                                            <a href="{{ route('commercial-suivi.devis.show', $demande->devis->first()) }}" class="ui-btn ui-btn-ghost text-xs py-1 px-2 text-slate-500 hover:text-slate-800" title="Voir le devis d'origine">
                                                <i class="fas fa-file-invoice-dollar"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-12 text-center text-slate-500">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center text-base mx-auto mb-3">
                                        <i class="fas fa-inbox"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">Aucune demande d'intervention</p>
                                    <p class="text-xs text-slate-500 mt-1">Toutes les demandes clients ont été traitées ou planifiées.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
