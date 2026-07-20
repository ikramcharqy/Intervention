<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="space-y-8">
        <!-- Message de bienvenue -->
        <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-2xl shadow-sm border border-slate-800 p-6 text-white relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-indigo-500/10 blur-2xl"></div>
            <h3 class="text-xl font-bold mb-1">Espace de Travail : {{ Auth::user()->name }}</h3>
            <p class="text-slate-400 text-xs">Field Service Hub — Suivi en temps réel de vos techniciens et de l'état des interventions planifiées.</p>
        </div>

        <!-- KPIs Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Interventions -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Interventions</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['interventions'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-650 dark:text-indigo-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>

            <!-- Interventions En Cours -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">En cours / Sur Site</p>
                    <h4 class="text-3xl font-extrabold text-yellow-600 dark:text-yellow-400 mt-1">{{ $stats['en_cours'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-yellow-50 dark:bg-yellow-950/40 text-yellow-600 dark:text-yellow-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Terminées -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Interventions Terminées</p>
                    <h4 class="text-3xl font-extrabold text-green-600 dark:text-green-400 mt-1">{{ $stats['terminees'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <!-- Validation -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">En attente validation</p>
                    <h4 class="text-3xl font-extrabold text-blue-600 dark:text-blue-400 mt-1">{{ $stats['en_attente'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Clients -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Clients</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['clients'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-purple-50 dark:bg-purple-950/40 text-purple-650 dark:text-purple-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            <!-- Chantiers -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Chantiers Actifs</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['chantiers'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-650 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>

            <!-- Techniciens -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Techniciens actifs</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['techniciens'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-pink-50 dark:bg-pink-950/40 text-pink-655 dark:text-pink-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>

            <!-- Rapports -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Rapports</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['rapports'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-teal-50 dark:bg-teal-950/40 text-teal-650 dark:text-teal-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Section Activité Récente & Graphiques (placeholders) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Activité Récente (Tableau) -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 lg:col-span-2">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4">Dernières interventions créées</h3>
                @if($recentInterventions->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-slate-400 uppercase bg-slate-50 dark:bg-slate-800/40 border-b border-gray-150 dark:border-gray-800">
                                <tr>
                                    <th class="px-4 py-3">Référence</th>
                                    <th class="px-4 py-3">Client / Chantier</th>
                                    <th class="px-4 py-3">Technicien</th>
                                    <th class="px-4 py-3">Priorité</th>
                                    <th class="px-4 py-3">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($recentInterventions as $intervention)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                                        <td class="px-4 py-3 font-mono font-bold text-indigo-650 dark:text-indigo-400">{{ $intervention->code_intervention }}</td>
                                        <td class="px-4 py-3">
                                            <div class="text-gray-900 dark:text-white font-semibold">{{ $intervention->chantier->nom ?? '-' }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $intervention->chantier->client->nom ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $intervention->technicien->name ?? 'Non assigné' }}</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $priColors = [
                                                    'Faible' => 'bg-gray-105 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                                                    'Normale' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                                    'Haute' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-400',
                                                    'Urgente' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400 font-bold',
                                                ];
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full font-medium {{ $priColors[$intervention->priorite] ?? '' }}">
                                                {{ $intervention->priorite }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $stColors = [
                                                    'Planifiee' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400',
                                                    'Acceptee' => 'bg-indigo-50 text-indigo-750 dark:bg-indigo-900/20 dark:text-indigo-400',
                                                    'En cours' => 'bg-yellow-50 text-yellow-750 dark:bg-yellow-900/20 dark:text-yellow-400',
                                                    'Formulaire rempli' => 'bg-emerald-50 text-emerald-750 dark:bg-emerald-900/20 dark:text-emerald-400',
                                                    'Suspendue' => 'bg-orange-50 text-orange-750 dark:bg-orange-900/20 dark:text-orange-400',
                                                    'Terminee' => 'bg-green-50 text-green-755 dark:bg-green-900/20 dark:text-green-400',
                                                    'Annulee' => 'bg-red-50 text-red-750 dark:bg-red-900/20 dark:text-red-400',
                                                ];
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full font-bold {{ $stColors[$intervention->statut] ?? '' }}">
                                                {{ $intervention->statut }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-xs py-4">Aucune intervention enregistrée.</p>
                @endif
            </div>

            <!-- Graphiques / Statistiques de charges (placeholder) -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 flex flex-col justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-1">Performance & Charge</h3>
                    <p class="text-xs text-gray-400 mb-4">Statistiques d'exécution de la semaine en cours.</p>
                    
                    <!-- Graphique simulé -->
                    <div class="h-44 border border-dashed border-gray-200 dark:border-gray-800 rounded-xl flex items-center justify-center bg-gray-50 dark:bg-gray-950">
                        <div class="text-center p-4">
                            <svg class="h-8 w-8 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                            <span class="text-xs text-gray-450 font-medium">Graphique d'activité</span>
                        </div>
                    </div>
                </div>

                <div class="text-center text-xs text-gray-400 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    Prochaine mise à jour à minuit
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
