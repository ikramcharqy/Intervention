<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.supervision.interventions') }}" class="w-9 h-9 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 flex items-center justify-center text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800 transition shrink-0">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            </a>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight font-mono">{{ $intervention->code_intervention }}</h1>
                <x-soft-badge :status="$intervention->statut" />
                <x-soft-badge :status="$intervention->priorite" />
                @if($intervention->estBloquee())
                    <span class="flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">
                        <x-icon name="warning" class="w-3 h-3" /> Bloquée depuis {{ $intervention->updated_at?->diffForHumans(null, true) }}
                    </span>
                @endif
            </div>
        </div>
        <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm -mt-4">Fiche en lecture seule (supervision transverse) — la gestion reste sur les espaces Admin/Commercial/Technicien.</p>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Client / Chantier</p>
                    <p class="text-sm font-semibold text-[#131523] dark:text-slate-100">{{ $intervention->chantier?->client?->nom ?? '—' }}</p>
                    <p class="text-xs text-[#5A607F] dark:text-slate-400">{{ $intervention->chantier?->nom ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Technicien</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->technicien ? $intervention->technicien->prenom . ' ' . $intervention->technicien->name : 'Non affecté' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Type d'Intervention</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->typeIntervention?->nom ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Prévue le</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->date_prevue_debut?->format('d/m/Y H:i') ?? '—' }} → {{ $intervention->date_prevue_fin?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Réalisée le</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->date_reelle_debut?->format('d/m/Y H:i') ?? '—' }} → {{ $intervention->date_reelle_fin?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Progression</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->pourcentage_global ?? 0 }}%</p>
                </div>
                <div class="sm:col-span-3">
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Description</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->description ?: '—' }}</p>
                </div>
            </div>
        </div>

        @if($intervention->rapport)
            <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100 mb-4">Rapport d'Intervention</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Commentaire</p>
                        <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->rapport->commentaire ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Signature Client</p>
                        <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $intervention->rapport->signature_client ?: '—' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Historique des Statuts</h3>
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($intervention->historiques as $h)
                    <div class="px-7 py-3 flex items-center justify-between gap-3">
                        <span class="text-xs text-[#131523] dark:text-slate-200">{{ $h->statut_avant ? \App\Models\Intervention::statutLabel($h->statut_avant) : '—' }} → {{ $h->statut_apres ? \App\Models\Intervention::statutLabel($h->statut_apres) : '—' }}</span>
                        <span class="text-[11px] font-mono text-[#A1A7C4] dark:text-slate-500">{{ $h->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucun historique enregistré.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-super-admin-layout>
