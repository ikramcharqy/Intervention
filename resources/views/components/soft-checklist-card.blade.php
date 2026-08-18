@props([
    'checklist' => [],
    'pretValidation' => false,
    'stats' => ['validees' => 0, 'totales' => 7],
    'manquants' => [],
    'intervention' => null,
])

<div class="soft-card overflow-hidden">
    <!-- Soft UI Gradient Banner Header -->
    <div class="p-6 soft-gradient-dark text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 flex-wrap mb-1">
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $pretValidation ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                    {{ $pretValidation ? '✓ Dossier Prêt à la Validation' : '⚠ Validation Bloquée — Preuves Incomplètes' }}
                </span>
                <span class="text-xs text-slate-300 font-bold">({{ $stats['validees'] }}/{{ $stats['totales'] }} Preuves Validées)</span>
            </div>
            <h3 class="text-base font-extrabold text-white mb-0">Checklist de Complétude des Preuves Terrain</h3>
            <p class="text-xs text-slate-300 mb-0">Contrôle d'intégrité avant validation et clôture de l'intervention</p>
        </div>

        <div class="flex items-center gap-2 shrink-0 flex-wrap">
            @if($intervention && $intervention->rapport)
                <a href="{{ route('rapports.pdf', $intervention->rapport) }}" target="_blank" class="px-4 py-2.5 soft-gradient-info text-white text-xs font-bold rounded-xl shadow-md transition hover:opacity-90">
                    <i class="fas fa-file-pdf me-1"></i> Télécharger PDF Officiel
                </a>
            @endif

            @if(auth()->user()->hasAnyRole(['admin','Admin','Super Admin','superadmin']))
                @if($intervention && !in_array($intervention->statut, ['Validee', 'Annulee']))
                    @if($pretValidation)
                        <form method="POST" action="{{ route('interventions.validate', $intervention) }}">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 soft-gradient-success text-white text-xs font-black rounded-xl shadow-lg transition hover:opacity-90">
                                <i class="fas fa-check-circle me-1"></i> Valider & Clôturer
                            </button>
                        </form>
                    @else
                        <button type="button" disabled class="px-4 py-2.5 bg-slate-200 text-slate-400 text-xs font-bold rounded-xl cursor-not-allowed shadow-inner" title="Preuves obligatoires manquantes">
                            🔒 Validation Bloquée ({{ count($manquants) }} Manquant(s))
                        </button>
                    @endif
                @endif
            @endif
        </div>
    </div>

    <!-- Grille des 7 preuves aux couleurs et cartes Soft UI -->
    <div class="p-5 bg-slate-50/50">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
            @foreach($checklist as $key => $item)
                <div class="p-3 rounded-xl border {{ $item['valide'] ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : ($item['obligatoire'] ? 'bg-rose-50/90 border-rose-200 text-rose-900 shadow-2xs' : 'bg-white border-slate-200 text-slate-700') }} transition flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-1 mb-1.5">
                        <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded {{ $item['valide'] ? 'bg-emerald-200 text-emerald-800' : ($item['obligatoire'] ? 'bg-rose-200 text-rose-900' : 'bg-slate-200 text-slate-600') }}">
                            {{ $item['obligatoire'] ? 'Obligatoire' : 'Optionnel' }}
                        </span>
                        <span class="text-sm">
                            {{ $item['valide'] ? '✅' : ($item['obligatoire'] ? '❌' : '⚪') }}
                        </span>
                    </div>

                    <div>
                        <span class="text-xs font-bold block leading-tight mb-1">{{ $item['libelle'] }}</span>
                        <p class="text-[10px] opacity-80 mb-0 leading-tight">{{ $item['details'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
