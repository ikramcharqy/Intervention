<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.supervision.chantiers') }}" class="w-9 h-9 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 flex items-center justify-center text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800 transition shrink-0">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">{{ $chantier->nom }}</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1 font-mono">{{ $chantier->code_chantier }} — Fiche en lecture seule (supervision transverse)</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Client</p>
                    <p class="text-sm font-semibold text-[#131523] dark:text-slate-100">{{ $chantier->client?->nom ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Ville</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $chantier->ville ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Statut</p>
                    @if($chantier->is_active)
                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                    @else
                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                    @endif
                </div>
                <div class="sm:col-span-3">
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Adresse</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $chantier->adresse ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Responsable</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $chantier->responsable ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Téléphone</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $chantier->telephone_responsable ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $chantier->email_responsable ?: '—' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800 flex items-center justify-between gap-3">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Emplacements ({{ $chantier->emplacements->count() }})</h3>
                @if($chantier->emplacements->isNotEmpty())
                    <a href="{{ route('superadmin.supervision.chantiers.qrExport', $chantier) }}" class="text-xs font-bold text-[#1E5EFF] hover:underline flex items-center gap-1.5 shrink-0">
                        <x-icon name="qrcode" class="w-3.5 h-3.5" /> Exporter les QR (PDF)
                    </a>
                @endif
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($chantier->emplacements as $e)
                    <div class="px-7 py-3 flex items-center justify-between">
                        <span class="text-xs font-semibold text-[#131523] dark:text-slate-100">{{ $e->nom }}</span>
                        <span class="text-[11px] font-mono text-[#A1A7C4] dark:text-slate-500">{{ $e->qr_code }}</span>
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucun emplacement configuré.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Interventions récentes</h3>
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($chantier->interventions as $i)
                    <div class="px-7 py-3 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <span class="block text-xs font-bold font-mono text-[#131523] dark:text-slate-100">{{ $i->code_intervention }}</span>
                            <span class="text-[11px] text-[#A1A7C4] dark:text-slate-500">{{ $i->technicien ? $i->technicien->prenom . ' ' . $i->technicien->name : 'Non affecté' }} · {{ $i->typeIntervention?->nom ?? '—' }}</span>
                        </div>
                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-[#EAF0FF] dark:bg-blue-500/10 text-[#1E5EFF] dark:text-blue-400 shrink-0">{{ $i->statut }}</span>
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucune intervention enregistrée sur ce chantier.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-super-admin-layout>
