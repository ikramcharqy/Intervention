<x-technicien-layout>
    <div class="space-y-6">

        <!-- Banner Welcome Technicien Executive Light -->
        <div class="ui-card p-6 bg-gradient-to-r from-slate-900 via-slate-800 to-amber-950 text-white border-slate-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-sm rounded-2xl">
            <div class="z-10 max-w-xl">
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300 text-xs font-semibold mb-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span>Console Terrain Technicien</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight">
                    Bonjour, {{ Auth::user()->name }}
                </h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 leading-relaxed">
                    Accédez à votre planning, saisissez vos formulaires d'intervention et transmettez vos comptes-rendus.
                </p>
            </div>
            
            <div class="flex items-center gap-3 z-10 flex-wrap shrink-0">
                <a href="{{ route('interventions.index') }}" class="ui-btn ui-btn-primary text-xs py-2.5 px-4 shadow-sm !bg-amber-600 hover:!bg-amber-700">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Mes Interventions</span>
                </a>
                <a href="{{ route('planning.index') }}" class="ui-btn ui-btn-secondary text-xs py-2.5 px-4">
                    <i class="fas fa-calendar-alt text-slate-600"></i>
                    <span>Planning</span>
                </a>
            </div>
        </div>

        <!-- Alerte Intervention en cours -->
        @if($enCours)
            <div class="ui-card p-5 border-l-4 border-amber-500 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-amber-50/60 border-amber-200">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 text-[11px] font-bold uppercase tracking-wide">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                        Intervention Active sur le Terrain
                    </span>
                    <h3 class="text-base font-bold text-slate-900 mt-2 mb-1 flex items-center gap-2">
                        <span class="font-mono text-amber-800 font-extrabold">{{ $enCours->code_intervention }}</span>
                        <span class="text-slate-700 font-semibold">— {{ $enCours->chantier?->nom }}</span>
                    </h3>
                    <p class="text-xs text-slate-600 line-clamp-1">{{ $enCours->description ?? 'Intervention en cours d\'exécution' }}</p>
                </div>
                <a href="{{ route('interventions.show', $enCours) }}" class="ui-btn ui-btn-primary text-xs py-2.5 px-4 !bg-amber-600 hover:!bg-amber-700 shrink-0">
                    <i class="fas fa-file-signature"></i>
                    <span>Saisir Formulaire & Clôturer</span>
                </a>
            </div>
        @endif

        <!-- KPI Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <x-soft-kpi-card
                title="Missions Assignées"
                value="{{ $stats['total'] }}"
                subtitle="Charge totale attribuée"
                icon="fas fa-clipboard-list"
                gradient="primary"
            />
            <x-soft-kpi-card
                title="À Réaliser"
                value="{{ $stats['planifiees'] }}"
                subtitle="Prochains chantiers"
                icon="fas fa-calendar-alt"
                gradient="info"
            />
            <x-soft-kpi-card
                title="En Cours"
                value="{{ $stats['en_cours'] }}"
                subtitle="Sur le terrain maintenant"
                icon="fas fa-spinner"
                gradient="warning"
                :badgeText="$stats['en_cours'] > 0 ? 'Actif' : ''"
                badgeType="warning"
            />
            <x-soft-kpi-card
                title="Terminées"
                value="{{ $stats['terminees'] }}"
                subtitle="Rapports transmis"
                icon="fas fa-check-circle"
                gradient="success"
            />
        </div>

        <!-- Planning Aujourd'hui -->
        <div class="ui-card overflow-hidden">
            <div class="ui-card-header bg-slate-50/70">
                <div>
                    <h3 class="ui-card-title flex items-center gap-2 text-slate-900">
                        <i class="fas fa-calendar-day text-amber-600"></i>
                        <span>Planning des Interventions d'Aujourd'hui</span>
                    </h3>
                    <p class="ui-card-subtitle">{{ now()->translatedFormat('l d F Y') }}</p>
                </div>
                <a href="{{ route('interventions.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-900 transition">
                    Toutes mes interventions →
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($interventionsAujourdhui as $interv)
                    <div class="p-4 sm:p-5 flex items-center justify-between gap-4 hover:bg-slate-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 flex items-center justify-center text-xs shrink-0">
                                <i class="fas fa-clock text-amber-600"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs text-amber-800 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ $interv->code_intervention }}</span>
                                    <span class="text-xs font-bold text-slate-900 truncate">{{ $interv->chantier?->nom }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Prévu : <span class="font-mono font-semibold text-slate-700">{{ $interv->date_prevue_debut?->format('H:i') ?? 'N/A' }}</span> · Emplacement : {{ $interv->emplacement?->nom ?? 'Standard' }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('interventions.show', $interv) }}" class="ui-btn ui-btn-secondary text-xs py-1.5 px-3 shrink-0">
                            <span>Fiche</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                @empty
                    <div class="p-10 text-center text-slate-500">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center text-base mx-auto mb-3">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-800">Aucune intervention planifiée pour aujourd'hui</p>
                        <p class="text-xs text-slate-500 mt-1">Consultez votre planning pour les prochains jours.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-technicien-layout>
