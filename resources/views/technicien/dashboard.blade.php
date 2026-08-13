<x-technicien-layout>
    <div class="space-y-8">
        <!-- Welcome Banner Metronic Style -->
        <div class="p-6 md:p-8 rounded-2xl bg-gradient-to-r from-[#1E1E2D] to-[#2B2B40] text-white flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-md relative overflow-hidden">
            <div class="space-y-2 z-10">
                <span class="px-3 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs font-bold rounded-full uppercase tracking-wider">
                    Console Terrain Technicien
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold font-heading text-white">Bonjour, {{ Auth::user()->name }} 🛠️</h2>
                <p class="text-xs md:text-sm text-[#A1A5B7]">Suivez votre planning d'intervention et vos comptes-rendus terrain.</p>
            </div>
        </div>

        <!-- Metronic KPI Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Missions Assignées</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['total'] }}</div>
                    <span class="text-[11px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded mt-2 inline-block">Charge totale</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">À Réaliser</span>
                    <div class="text-2xl font-extrabold text-[#181C32] mt-1 font-heading">{{ $stats['planifiees'] }}</div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded mt-2 inline-block">Prochains chantiers</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">En Cours</span>
                    <div class="text-2xl font-extrabold text-amber-600 mt-1 font-heading">{{ $stats['en_cours'] }}</div>
                    <span class="text-[11px] font-semibold text-amber-600 bg-amber-50 px-2 py-0.5 rounded mt-2 inline-block">Sur le terrain</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>

            <div class="metronic-card p-6 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#A1A5B7]">Terminées</span>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-1 font-heading">{{ $stats['terminees'] }}</div>
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded mt-2 inline-block">Rapports envoyés</span>
                </div>
                <div class="h-12 w-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-500 shrink-0">
                    <svg width="24" height="24" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- Intervention Active -->
        @if($enCours)
            <div class="p-6 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded bg-amber-500 text-white">Intervention Active en cours</span>
                    <h3 class="text-lg font-bold text-[#181C32] font-heading mt-2">{{ $enCours->code_intervention }} — {{ $enCours->chantier?->nom }}</h3>
                    <p class="text-xs text-[#5E6278] mt-1">{{ $enCours->description }}</p>
                </div>
                <a href="{{ route('interventions.show', $enCours) }}" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-lg transition shadow-sm shrink-0">
                    Saisir Formulaire & Clôturer →
                </a>
            </div>
        @endif

        <!-- Planning d'Aujourd'hui -->
        <div class="metronic-card p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#EFF2F5]">
                <h3 class="text-base font-bold text-[#181C32] font-heading">Planning des Interventions d'Aujourd'hui</h3>
                <a href="{{ route('interventions.index') }}" class="text-xs font-bold text-amber-600 hover:underline">Toutes les interventions →</a>
            </div>

            <div class="divide-y divide-[#EFF2F5]">
                @forelse($interventionsAujourdhui as $interv)
                    <div class="py-3.5 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-[#181C32] block">{{ $interv->code_intervention }} — {{ $interv->chantier?->nom }}</span>
                            <span class="text-[11px] text-[#A1A5B7]">Date prévue : {{ $interv->date_prevue_debut?->format('H:i') }} | Emplacement: {{ $interv->emplacement?->nom ?? 'N/A' }}</span>
                        </div>
                        <a href="{{ route('interventions.show', $interv) }}" class="px-3 py-1.5 bg-[#F5F8FA] hover:bg-[#EEF0F8] text-[#181C32] font-bold rounded-lg text-xs transition">
                            Fiche Intervention →
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-[#A1A5B7] py-4 italic">Aucune intervention planifiée pour aujourd'hui.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-technicien-layout>
