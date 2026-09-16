<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.supervision.emplacements') }}" class="w-9 h-9 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 flex items-center justify-center text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800 transition shrink-0">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            </a>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">{{ $emplacement->nom }}</h1>
                @if($emplacement->is_active)
                    <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                @else
                    <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                @endif
            </div>
        </div>
        <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm -mt-4">Fiche en lecture seule (supervision transverse) — la gestion reste sur les espaces Admin/Commercial.</p>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Chantier</p>
                    <p class="text-sm font-semibold text-[#131523] dark:text-slate-100">{{ $emplacement->chantier?->nom ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Client</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $emplacement->chantier?->client?->nom ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Code QR</p>
                    <p class="text-sm font-mono text-[#5A607F] dark:text-slate-300">{{ $emplacement->qr_code }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Tag NFC</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $emplacement->nfc_uid ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Généré le</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $emplacement->created_at?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div class="sm:col-span-3">
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Description</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $emplacement->description ?: '—' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Interventions récentes ({{ $emplacement->interventions->count() }})</h3>
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($emplacement->interventions as $i)
                    <div class="px-7 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <a href="{{ route('superadmin.supervision.interventions.show', $i) }}" class="block text-xs font-bold font-mono text-[#1E5EFF] hover:underline">{{ $i->code_intervention }}</a>
                            <span class="text-[11px] text-[#A1A7C4] dark:text-slate-500">{{ $i->technicien ? $i->technicien->prenom . ' ' . $i->technicien->name : 'Non affecté' }} · {{ $i->typeIntervention?->nom ?? '—' }}</span>
                        </div>
                        <x-soft-badge :status="$i->statut" />
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucune intervention enregistrée sur cet emplacement.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-super-admin-layout>
