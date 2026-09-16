<x-super-admin-layout>
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('superadmin.supervision.clients') }}" class="w-9 h-9 rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 flex items-center justify-center text-[#5A607F] dark:text-slate-300 hover:bg-[#F5F6FA] dark:hover:bg-slate-800 transition shrink-0">
                <x-icon name="chevron-right" class="w-4 h-4 rotate-180" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">{{ $client->nom }}</h1>
                <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1 font-mono">{{ $client->code_client }} — Fiche en lecture seule (supervision transverse)</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-7">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Type</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $client->type_client ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Commercial</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $client->commercial?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Statut</p>
                    @if($client->is_active)
                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                    @else
                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                    @endif
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Contact</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $client->nom_contact ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Téléphone</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $client->telephone ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Email</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $client->email ?: '—' }}</p>
                </div>
                <div class="sm:col-span-3">
                    <p class="text-[11px] font-bold text-[#A1A7C4] dark:text-slate-500 uppercase tracking-wider mb-1">Adresse de facturation</p>
                    <p class="text-sm text-[#5A607F] dark:text-slate-300">{{ $client->adresse_facturation ?: '—' }} {{ $client->ville ? '— '.$client->ville : '' }}</p>
                </div>
            </div>
        </div>

        <!-- KPI d'activité -->
        <x-supervision-kpi-header :items="[
            ['title' => 'Chantiers', 'value' => $client->chantiers_count, 'icon' => 'city', 'theme' => ['bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10', 'text' => 'text-[#1E5EFF] dark:text-blue-400']],
            ['title' => 'Interventions', 'value' => $interventionsCount, 'icon' => 'gauge', 'theme' => ['bg' => 'bg-[#FFF3DE] dark:bg-amber-500/10', 'text' => 'text-[#B98900] dark:text-amber-400']],
            ['title' => 'CA Généré', 'value' => format_montant($caGenere), 'icon' => 'gauge', 'theme' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400']],
            ['title' => 'Montant en Attente', 'value' => format_montant($montantEnAttente), 'icon' => 'warning', 'theme' => ['bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10', 'text' => 'text-[#F0142F] dark:text-rose-400']],
        ]" />

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Chantiers ({{ $client->chantiers->count() }})</h3>
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($client->chantiers as $chantier)
                    <div class="px-7 py-3 flex items-center justify-between">
                        <a href="{{ route('superadmin.supervision.chantiers.show', $chantier) }}" class="text-xs font-semibold text-[#1E5EFF] hover:underline">{{ $chantier->nom }}</a>
                        <span class="text-[11px] font-mono text-[#A1A7C4] dark:text-slate-500">{{ $chantier->code_chantier }}</span>
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucun chantier enregistré.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Devis récents ({{ $devis->count() }})</h3>
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($devis as $d)
                    <div class="px-7 py-3 flex items-center justify-between gap-3">
                        <span class="text-xs font-mono font-bold text-[#131523] dark:text-slate-100">{{ $d->reference }}</span>
                        <span class="text-xs text-[#5A607F] dark:text-slate-400">{{ $d->statut }}</span>
                        <span class="text-xs font-semibold text-[#131523] dark:text-slate-100">{{ format_montant($d->montant_ttc) }}</span>
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucun devis enregistré.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="px-7 py-4 border-b border-[#E6E9F4] dark:border-slate-800">
                <h3 class="text-sm font-bold text-[#131523] dark:text-slate-100">Factures récentes ({{ $factures->count() }})</h3>
            </div>
            <div class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                @forelse($factures as $f)
                    <div class="px-7 py-3 flex items-center justify-between gap-3">
                        <span class="text-xs font-mono font-bold text-[#131523] dark:text-slate-100">{{ $f->reference }}</span>
                        <span class="text-xs text-[#5A607F] dark:text-slate-400">{{ $f->statut }}</span>
                        <span class="text-xs font-semibold text-[#131523] dark:text-slate-100">{{ format_montant($f->montant_ttc) }}</span>
                    </div>
                @empty
                    <p class="px-7 py-6 text-xs text-[#A1A7C4] dark:text-slate-500 italic text-center">Aucune facture enregistrée.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-super-admin-layout>
