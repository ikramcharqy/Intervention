<x-app-layout>
    <x-slot name="header">
        Centre des Rapports d'Intervention
    </x-slot>

    <div class="space-y-6">
        <!-- Header Banner & KPIs -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-2xl p-6 shadow-xl border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-indigo-300 text-xs font-semibold mb-2">
                    📄 Module de Reporting & Certifications Client
                </span>
                <h3 class="text-2xl font-black text-white tracking-tight">Rapports Techniques & Procès-Verbaux</h3>
                <p class="text-xs text-slate-300 mt-1 max-w-xl">
                    Consultez, validez et exportez en PDF haute définition tous les comptes-rendus d'interventions certifiés et signés par vos techniciens.
                </p>
            </div>
            @php
                $totalRapports = \App\Models\Rapport::count();
                $rapportsValides = \App\Models\Rapport::whereHas('intervention', fn($q) => $q->where('statut', 'Terminee'))->count();
            @endphp
            <div class="flex items-center gap-4 bg-white/5 border border-white/10 p-4 rounded-xl backdrop-blur-md">
                <div class="text-center px-3 border-r border-white/10">
                    <span class="block text-2xl font-black text-white">{{ $totalRapports }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Total Générés</span>
                </div>
                <div class="text-center px-3">
                    <span class="block text-2xl font-black text-emerald-400">{{ $rapportsValides }}</span>
                    <span class="text-[10px] uppercase tracking-wider text-emerald-300 font-bold">Certifiés PDF</span>
                </div>
            </div>
        </div>

        <!-- Barres de Recherche & Filtres -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 p-5">
            <form method="GET" action="{{ route('rapports.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="relative w-full md:w-96">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Rechercher par code, client, technicien..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 text-xs focus:ring-2 focus:ring-indigo-500 shadow-sm">
                </div>
                
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition text-xs shadow-md flex items-center gap-2">
                        <span>Filtrer</span>
                    </button>
                    @if(isset($search) && $search)
                        <a href="{{ route('rapports.index') }}" class="px-4 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-200 transition text-xs">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Metronic 8 des rapports -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-gray-600 dark:text-gray-300">
                    <thead class="text-[11px] font-black uppercase tracking-wider text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-800/80 border-b border-gray-200 dark:border-gray-800">
                        <tr>
                            <th class="px-6 py-4">Code / Intervention</th>
                            <th class="px-6 py-4">Client & Chantier</th>
                            <th class="px-6 py-4">Technicien</th>
                            <th class="px-6 py-4">Horodatage</th>
                            <th class="px-6 py-4">Conformité</th>
                            <th class="px-6 py-4 text-right">Actions Export PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium">
                        @forelse ($rapports as $rapport)
                            <tr class="hover:bg-indigo-50/30 dark:hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400 text-sm">
                                        {{ $rapport->intervention->code_intervention ?? '-' }}
                                    </span>
                                    <div class="text-[10px] text-gray-400 font-normal">
                                        Type: {{ $rapport->intervention->typeIntervention->nom ?? 'Technique' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 dark:text-white font-bold">{{ $rapport->intervention->chantier->client->nom ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $rapport->intervention->chantier->nom ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $rapport->intervention->technicien->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-[11px]">
                                    <div class="text-gray-700 dark:text-gray-300">
                                        <span class="font-semibold text-gray-400">Date :</span> 
                                        {{ $rapport->created_at ? $rapport->created_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                    <div class="text-gray-400 text-[10px]">
                                        Durée : {{ $rapport->intervention->duree_reelle ? $rapport->intervention->duree_reelle.' min' : 'Standard' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if (($rapport->intervention->statut ?? '') === 'Terminee')
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                                            ✓ Certifié
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300">
                                            ⏳ En Révision
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('rapports.show', $rapport) }}" class="px-3 py-1.5 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 text-gray-700 dark:text-gray-300 font-bold rounded-lg text-xs transition">
                                            Consulter
                                        </a>
                                        <a href="{{ route('rapports.pdf', $rapport) }}" target="_blank" class="px-3 py-1.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold rounded-lg text-xs shadow transition flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Export PDF Pro
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">
                                    Aucun rapport d'intervention trouvé dans la base de données.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rapports->hasPages())
                <div class="px-6 py-4 border-t border-gray-150 dark:border-gray-800">
                    {{ $rapports->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
