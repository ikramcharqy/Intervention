<x-commercial-layout>
    <x-slot name="header">Tableau de bord Commercial</x-slot>

    <div class="space-y-8">
        <!-- Welcome Banner Metronic Style -->
        <div class="p-6 md:p-8 rounded-2xl bg-gradient-to-r from-[#1E1E2D] to-[#2B2B40] text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md relative overflow-hidden">
            <div class="space-y-2 z-10">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold rounded-full uppercase tracking-wider">
                    Espace Commercial & Ventes
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold font-heading text-white">Bonjour, {{ Auth::user()->name }} 👋</h2>
                <p class="text-xs md:text-sm text-[#A1A5B7]">Suivez vos prospects, opportunités et devis en temps réel.</p>
            </div>
            <div class="flex items-center gap-3 z-10">
                <a href="{{ route('prospects.create') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg shadow-sm transition flex items-center gap-2">
                    <svg width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    + Nouveau Prospect
                </a>
                <a href="{{ route('commercial.devis.create') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold rounded-lg transition flex items-center gap-2">
                    <svg width="16" height="16" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    + Nouveau Devis
                </a>
            </div>
        </div>

        <!-- Metronic KPI Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('prospects.index') }}" class="metronic-card p-6 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Prospects</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['prospects'] }}</div>
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded mt-2 inline-block">Pistes d'affaires</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </a>

            <a href="{{ route('clients.index') }}" class="metronic-card p-6 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Clients</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['clients'] }}</div>
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-2 inline-block">Comptes Signés</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </a>

            <a href="{{ route('commercial.devis.index') }}" class="metronic-card p-6 flex items-center justify-between hover:shadow-md transition">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Total Devis</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['devis'] }}</div>
                    <span class="text-[11px] font-semibold text-purple-600 bg-purple-50 px-2 py-0.5 rounded mt-2 inline-block">Propositions</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </a>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Taux Conversion</span>
                    <div class="text-2xl font-extrabold text-amber-600 mt-1 font-heading">{{ $stats['taux_conversion'] }}%</div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded mt-2 inline-block">Performance Ventes</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>

        <!-- Devis Status Row Metronic Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="metronic-card p-6 border-l-4 border-l-amber-400 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Devis en Attente</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['devis_attente'] }}</div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="metronic-card p-6 border-l-4 border-l-emerald-500 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Devis Acceptés</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['devis_acceptes'] }}</div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="metronic-card p-6 border-l-4 border-l-rose-500 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Devis Refusés</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['devis_refuses'] }}</div>
                </div>
                <div class="h-10 w-10 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center shrink-0">
                    <svg width="20" height="20" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Main Content Grids -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Derniers Devis -->
            <div class="metronic-card p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#EFF2F5]">
                    <h3 class="text-base font-bold text-[#181C32] font-heading">Derniers Devis Émis</h3>
                    <a href="{{ route('commercial.devis.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">Voir tout →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#EFF2F5] text-[#A1A5B7] uppercase font-bold text-[10px]">
                                <th class="pb-3">Référence</th>
                                <th class="pb-3">Client / Prospect</th>
                                <th class="pb-3">Montant HT</th>
                                <th class="pb-3 text-right">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFF2F5]">
                            @forelse($recentDevis as $d)
                                <tr class="hover:bg-[#F9F9FB] transition">
                                    <td class="py-3 font-mono font-bold text-[#181C32]">{{ $d->reference }}</td>
                                    <td class="py-3 font-semibold text-[#3F4254]">{{ $d->client?->nom_complet ?? $d->prospect?->nom_complet ?? 'N/A' }}</td>
                                    <td class="py-3 font-mono font-bold text-[#181C32]">{{ number_format($d->montant_ht, 2) }} DH</td>
                                    <td class="py-3 text-right">
                                        @php
                                            $badge = match($d->statut) {
                                                'Accepte' => 'bg-emerald-50 text-emerald-600',
                                                'Refuse' => 'bg-rose-50 text-rose-600',
                                                default => 'bg-amber-50 text-amber-600'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $badge }}">
                                            {{ $d->statut }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-[#A1A5B7] italic">Aucun devis récent.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Derniers Prospects -->
            <div class="metronic-card p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#EFF2F5]">
                    <h3 class="text-base font-bold text-[#181C32] font-heading">Pistes & Prospects Récents</h3>
                    <a href="{{ route('prospects.index') }}" class="text-xs font-bold text-emerald-600 hover:underline">Voir tout →</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-[#EFF2F5] text-[#A1A5B7] uppercase font-bold text-[10px]">
                                <th class="pb-3">Prospect</th>
                                <th class="pb-3">Entreprise</th>
                                <th class="pb-3">Statut</th>
                                <th class="pb-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EFF2F5]">
                            @forelse($recentProspects as $p)
                                <tr class="hover:bg-[#F9F9FB] transition">
                                    <td class="py-3 font-semibold text-[#181C32]">{{ $p->nom_complet }}</td>
                                    <td class="py-3 text-[#5E6278]">{{ $p->nom_entreprise ?? 'Particulier' }}</td>
                                    <td class="py-3">
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-600">
                                            {{ $p->statut }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('prospects.show', $p) }}" class="px-2.5 py-1 bg-[#F5F8FA] hover:bg-[#EEF0F8] text-[#3F4254] font-bold rounded-md text-[10px]">Voir</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-[#A1A5B7] italic">Aucun prospect récent.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-commercial-layout>
