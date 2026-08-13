<x-client-layout>
    <x-slot name="header">Dashboard Client</x-slot>

    <div class="space-y-6">

        <!-- WELCOME BANNER (METRONIC 8 PREMIUM STYLE) -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#181C32] via-[#1E1E2D] to-[#2B2B40] p-6 md:p-8 text-white shadow-xl border border-slate-800/80">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-[11px] font-bold tracking-wider uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        Espace Client Protégé
                    </div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-snug">
                        Bienvenue, {{ Auth::user()->name }} 👋
                    </h2>
                    <p class="text-xs md:text-sm text-slate-300 leading-relaxed font-medium">
                        Suivez l'avancement en temps réel de vos chantiers, le statut des interventions et vos comptes-rendus d'interventions validés.
                    </p>
                </div>

                @if(isset($client))
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-xl p-4 text-left md:text-right shrink-0 shadow-inner">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Compte Client</span>
                        <span class="text-sm font-extrabold text-white block mt-1">{{ $client->nom }}</span>
                    </div>
                @endif
            </div>

            <!-- Subtle background glow decoration -->
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
        </div>

        <!-- METRONIC KPI STATS GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- Chantiers -->
            <a href="{{ route('client.chantiers.index') }}" class="kt-card p-5 flex items-center justify-between hover:shadow-lg transition-all duration-200 group border border-slate-100 hover:border-indigo-200">
                <div class="space-y-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Mes Chantiers</span>
                    <div class="text-3xl font-extrabold text-slate-900 group-hover:text-indigo-600 transition">{{ $stats['chantiers'] }}</div>
                    <span class="inline-block text-[11px] font-bold text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-md">Chantiers suivis</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </a>

            <!-- Total Interventions -->
            <a href="{{ route('client.interventions.index') }}" class="kt-card p-5 flex items-center justify-between hover:shadow-lg transition-all duration-200 group border border-slate-100 hover:border-purple-200">
                <div class="space-y-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Interventions</span>
                    <div class="text-3xl font-extrabold text-slate-900 group-hover:text-purple-600 transition">{{ $stats['interventions'] }}</div>
                    <span class="inline-block text-[11px] font-bold text-purple-600 bg-purple-50 px-2.5 py-0.5 rounded-md">Total demandes</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shrink-0 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 022 2h2a2 2 0 022-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </a>

            <!-- En cours sur site -->
            <a href="{{ route('client.interventions.index', ['statut' => 'En cours']) }}" class="kt-card p-5 flex items-center justify-between hover:shadow-lg transition-all duration-200 group border border-slate-100 hover:border-amber-200">
                <div class="space-y-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">En cours sur site</span>
                    <div class="text-3xl font-extrabold text-amber-600 group-hover:scale-105 transition">{{ $stats['en_cours'] }}</div>
                    <span class="inline-block text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-md">Activement traitées</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shrink-0 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>

            <!-- Terminées -->
            <a href="{{ route('client.interventions.index', ['statut' => 'Terminee']) }}" class="kt-card p-5 flex items-center justify-between hover:shadow-lg transition-all duration-200 group border border-slate-100 hover:border-emerald-200">
                <div class="space-y-1">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Terminées</span>
                    <div class="text-3xl font-extrabold text-emerald-600 group-hover:scale-105 transition">{{ $stats['terminees'] }}</div>
                    <span class="inline-block text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-md">Rapports validés</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 shrink-0 group-hover:scale-110 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </a>
        </div>

        <!-- LOWER SECTION (2 COLS TABLEAU + 1 COL DERNIER RAPPORT) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- TABLEAU DES INTERVENTIONS RÉCENTES (2/3 width) -->
            <div class="lg:col-span-2 kt-card p-6 flex flex-col justify-between border border-slate-100 shadow-sm">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Mes Dernières Interventions</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Suivi synthétique de l'exécution sur vos chantiers</p>
                        </div>
                        <a href="{{ route('client.interventions.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                            Voir tout <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 uppercase font-extrabold text-[10px] bg-slate-50/70">
                                    <th class="py-3 px-3.5 rounded-l-lg">Code Ref.</th>
                                    <th class="py-3 px-3.5">Chantier</th>
                                    <th class="py-3 px-3.5">Technicien</th>
                                    <th class="py-3 px-3.5 text-right rounded-r-lg">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($recentInterventions as $interv)
                                    <tr class="hover:bg-indigo-50/40 transition">
                                        <td class="py-3.5 px-3.5 font-mono font-bold text-indigo-600">{{ $interv->code_intervention }}</td>
                                        <td class="py-3.5 px-3.5 font-semibold text-slate-800">{{ $interv->chantier?->nom ?? '—' }}</td>
                                        <td class="py-3.5 px-3.5 text-slate-600 font-medium">{{ $interv->technicien?->name ?? 'Attribution en cours' }}</td>
                                        <td class="py-3.5 px-3.5 text-right">
                                            @php
                                                $badge = match($interv->statut) {
                                                    'Terminee' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'En cours' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'Annulee' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    default => 'bg-blue-50 text-blue-700 border-blue-200'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-1 text-[10px] font-extrabold rounded-md border {{ $badge }}">
                                                {{ $interv->statut }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-slate-400 italic">Aucune intervention enregistrée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- CARTE DERNIER RAPPORT VALIDÉ (1/3 width) -->
            <div class="kt-card p-6 flex flex-col justify-between border border-slate-100 shadow-sm">
                <div>
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                        <h3 class="text-base font-extrabold text-slate-900">Dernier Rapport Validé</h3>
                        <a href="{{ route('client.rapports.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Tous les rapports →</a>
                    </div>

                    @if($dernierRapport)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 font-extrabold text-xs shadow-md shadow-indigo-600/20">
                                    PDF
                                </div>
                                <div class="min-w-0">
                                    <span class="text-xs font-extrabold text-slate-900 truncate block">{{ $dernierRapport->intervention?->code_intervention ?? 'Rapport #'.$dernierRapport->id }}</span>
                                    <span class="text-[11px] font-semibold text-slate-400 block mt-0.5">Généré le {{ $dernierRapport->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="border-t border-slate-200/60 pt-3">
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    {{ Str::limit($dernierRapport->travaux_effectues ?? 'Compte-rendu d\'intervention validé et disponible au téléchargement.', 110) }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="py-12 text-center text-slate-400 italic">
                            Aucun rapport disponible actuellement.
                        </div>
                    @endif
                </div>

                @if($dernierRapport)
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a href="{{ route('client.rapports.show', $dernierRapport) }}" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs rounded-xl transition text-center block shadow-lg shadow-indigo-600/25">
                            Consulter & Télécharger PDF
                        </a>
                    </div>
                @endif
            </div>

        </div>

    </div>
</x-client-layout>
