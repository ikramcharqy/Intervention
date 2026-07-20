<x-commercial-layout>
    <x-slot name="header">
        {{ __('Tableau de bord Commercial') }}
    </x-slot>

    <div class="space-y-8">
        <!-- Message de bienvenue -->
        <div class="bg-gradient-to-r from-emerald-950 to-emerald-800 rounded-2xl shadow-sm border border-emerald-900 p-6 text-white relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-emerald-500/10 blur-2xl"></div>
            <h3 class="text-xl font-bold mb-1">Bienvenue, {{ Auth::user()->name }}</h3>
            <p class="text-emerald-200 text-xs">Suivi de vos prospects, clients et demandes d'intervention.</p>
        </div>

        <!-- KPIs Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <a href="{{ route('prospects.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Prospects</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['prospects'] }}</h4>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['prospects_a_relancer'] }} à relancer</p>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('clients.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Clients</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['clients'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('chantiers.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Chantiers</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['chantiers'] }}</h4>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('demande-interventions.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Demandes d'intervention</p>
                    <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $stats['demandes'] }}</h4>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['demandes_en_attente'] }} en attente</p>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('commercial.devis.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Devis</p>
                    <h4 class="text-sm font-medium text-gray-400 mt-1">À venir</h4>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </a>

            <a href="{{ route('commercial.documents.index') }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Documents</p>
                    <h4 class="text-sm font-medium text-gray-400 mt-1">À venir</h4>
                </div>
                <div class="h-10 w-10 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Prospects récents -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Prospects récents</h3>
                    <a href="{{ route('prospects.index') }}" class="text-xs text-emerald-600 hover:underline">Voir tout</a>
                </div>
                <div class="space-y-2">
                    @forelse($prospectsRecents as $prospect)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800 last:border-0 text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $prospect->nom_entreprise }}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400">{{ $prospect->statut }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Aucun prospect pour le moment.</p>
                    @endforelse
                </div>
            </div>

            <!-- Demandes récentes -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200">Demandes d'intervention récentes</h3>
                    <a href="{{ route('demande-interventions.index') }}" class="text-xs text-emerald-600 hover:underline">Voir tout</a>
                </div>
                <div class="space-y-2">
                    @forelse($demandesRecentes as $demande)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-800 last:border-0 text-sm">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $demande->objet }}</span>
                            <span class="text-xs px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400">{{ $demande->statut }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Aucune demande pour le moment.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-commercial-layout>
