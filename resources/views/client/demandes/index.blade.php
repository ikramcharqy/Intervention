<x-client-layout>
    <x-slot name="header">Mes Demandes</x-slot>

    <div class="space-y-6">
        <!-- Aide à l'orientation : Mes Demandes vs Support -->
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 text-xs text-indigo-800 flex items-start gap-3">
            <i class="fas fa-circle-info mt-0.5"></i>
            <p>
                <strong>Mes Demandes</strong> sert à solliciter une nouvelle intervention technique sur un chantier.
                Pour toute question administrative, de facturation ou d'accès à votre compte, utilisez plutôt
                <a href="{{ route('client.support.index') }}" class="font-bold underline hover:text-indigo-900">Support</a>.
            </p>
        </div>

        <!-- Filtres -->
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 flex-wrap">
                @php
                    $statutOptions = [
                        'tous' => 'Toutes',
                        'soumise' => 'Soumise',
                        'en_attente_validation' => 'En attente de validation',
                        'convertie' => 'Convertie',
                        'refusee' => 'Refusée',
                    ];
                @endphp
                @foreach($statutOptions as $value => $label)
                    <a href="{{ route('client.demandes.index', array_filter(['statut' => $value, 'periode' => $periode])) }}"
                       class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $statutFiltre === $value ? 'bg-emerald-500 text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <select name="periode" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                <option value="">Toute période</option>
                <option value="30" {{ $periode === '30' ? 'selected' : '' }}>30 derniers jours</option>
                <option value="90" {{ $periode === '90' ? 'selected' : '' }}>90 derniers jours</option>
                <option value="365" {{ $periode === '365' ? 'selected' : '' }}>12 derniers mois</option>
            </select>
            <input type="hidden" name="statut" value="{{ $statutFiltre }}">
        </form>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 pb-4">
                <h1 class="text-base font-bold text-slate-900">Mes Demandes d'Intervention</h1>
                <p class="text-xs text-slate-400 mt-1">Suivi de vos demandes, distinctes des interventions déjà planifiées.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Référence</th>
                            <th class="py-3 px-4">Chantier</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Priorité</th>
                            <th class="py-3 px-4">Soumise le</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 pr-6 pl-4 text-right">Suivi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($demandes as $demande)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-slate-900">{{ $demande->reference }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $demande->chantier?->nom ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $demande->typeIntervention?->nom ?? '—' }}</td>
                                <td class="py-3.5 px-4"><x-soft-badge :status="$demande->priorite" /></td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $demande->created_at->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-4">
                                    @php $libelle = $demande->libelleStatutClient(); @endphp
                                    @php
                                        $badgeColor = match(true) {
                                            $libelle === 'Convertie en intervention' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            $libelle === 'Refusée' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            $libelle === 'En attente de validation' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                            default => 'bg-amber-50 text-amber-700 border-amber-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold rounded-full border {{ $badgeColor }}">{{ $libelle }}</span>
                                    @if($libelle === 'Refusée' && $demande->motifRefus())
                                        <p class="text-[11px] text-rose-600 mt-1 max-w-xs" title="{{ $demande->motifRefus() }}">
                                            <i class="fas fa-comment-dots"></i> {{ Str::limit($demande->motifRefus(), 60) }}
                                        </p>
                                    @endif
                                </td>
                                <td class="py-3.5 pr-6 pl-4 text-right">
                                    @if($demande->intervention)
                                        <a href="{{ route('client.interventions.show', $demande->intervention) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                            Voir l'intervention <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    @elseif($libelle === 'Refusée')
                                        <a href="{{ route('client.demandes.create', ['depuis' => $demande->id]) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                            Nouvelle demande liée <i class="fas fa-rotate-right text-[10px]"></i>
                                        </a>
                                    @elseif($libelle === 'En attente de validation')
                                        <span class="text-xs text-slate-400 italic">En cours de traitement</span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">En attente de traitement commercial</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 italic">
                                    Aucune demande trouvée pour ces filtres.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($demandes, 'hasPages') && $demandes->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $demandes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
