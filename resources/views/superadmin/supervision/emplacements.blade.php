<x-super-admin-layout>
    <div class="space-y-6" x-data="{ confirmRegen: null, preview: null }">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] dark:text-slate-100 tracking-tight">Emplacements & QR Codes</h1>
            <p class="text-[#5A607F] dark:text-slate-400 text-xs sm:text-sm mt-1">Supervision transverse des emplacements — la gestion courante reste sur les espaces Admin/Commercial. Seule la régénération de QR Code est disponible ici, en tant qu'action exceptionnelle réservée au Super Admin (perte ou dégradation d'un support imprimé).</p>
        </div>

        <!-- KPI Stats Bar -->
        <x-supervision-kpi-header :items="[
            ['title' => 'Total Emplacements', 'value' => $kpis['total'], 'icon' => 'qrcode', 'theme' => ['bg' => 'bg-[#EAF0FF] dark:bg-blue-500/10', 'text' => 'text-[#1E5EFF] dark:text-blue-400']],
            ['title' => 'Actifs', 'value' => $kpis['actifs'], 'icon' => 'circle-check', 'theme' => ['bg' => 'bg-[#E3FBF0] dark:bg-emerald-500/10', 'text' => 'text-[#06A561] dark:text-emerald-400']],
            ['title' => 'Chantiers Équipés', 'value' => $kpis['total_chantiers'], 'icon' => 'city', 'theme' => ['bg' => 'bg-[#FFF3DE] dark:bg-amber-500/10', 'text' => 'text-[#B98900] dark:text-amber-400']],
            ['title' => 'Jamais Utilisés', 'value' => $kpis['jamais_utilises'], 'icon' => 'warning', 'theme' => ['bg' => 'bg-[#FDE3E6] dark:bg-rose-500/10', 'text' => 'text-[#F0142F] dark:text-rose-400']],
        ]" />

        <!-- Filtres : selects/case auto-soumis au changement ; le champ recherche garde un
             bouton dédié ("Rechercher"), seul mécanisme cohérent pour ce champ texte. -->
        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none p-4">
            <form method="GET" action="{{ route('superadmin.supervision.emplacements') }}" class="w-full flex flex-col lg:flex-row items-center gap-3">
                <div class="relative w-full lg:w-72">
                    <x-icon name="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-[#A1A7C4]" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un emplacement ou un code QR…"
                           class="w-full pl-10 pr-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-full text-xs text-[#131523] dark:text-slate-100 placeholder-[#A1A7C4] focus:outline-none focus:border-[#1E5EFF]">
                </div>
                <select name="chantier_id" onchange="this.form.submit()" class="w-full lg:w-56 px-4 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 border border-transparent rounded-[4px] text-xs text-[#131523] dark:text-slate-100 focus:outline-none focus:border-[#1E5EFF]">
                    <option value="">Tous les chantiers</option>
                    @foreach($chantiers as $c)
                        <option value="{{ $c->id }}" {{ request('chantier_id') == $c->id ? 'selected' : '' }}>{{ $c->nom }}</option>
                    @endforeach
                </select>
                <label class="inline-flex items-center gap-2 px-3 py-2.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] cursor-pointer shrink-0 w-full lg:w-auto">
                    <input type="checkbox" name="jamais_utilise" value="1" onchange="this.form.submit()" {{ request()->boolean('jamais_utilise') ? 'checked' : '' }} class="w-4 h-4 rounded border-[#D7DBEC] dark:border-slate-700 text-[#F0142F] focus:ring-[#F0142F]">
                    <span class="text-xs font-semibold text-[#F0142F] dark:text-rose-400 whitespace-nowrap">Jamais utilisés uniquement</span>
                </label>
                <button type="submit" class="px-4 py-2.5 bg-white dark:bg-slate-800 hover:bg-[#F5F6FA] dark:hover:bg-slate-700 text-[#5A607F] dark:text-slate-300 text-xs font-semibold rounded-[4px] border border-[#D7DBEC] dark:border-slate-700 shrink-0">Rechercher</button>
                @if(request('chantier_id'))
                    <a href="{{ route('superadmin.supervision.chantiers.qrExport', request('chantier_id')) }}" class="px-4 py-2.5 bg-[#EAF0FF] dark:bg-blue-500/10 hover:bg-[#D7E3FF] text-[#1E5EFF] dark:text-blue-400 text-xs font-bold rounded-[4px] shrink-0 whitespace-nowrap flex items-center gap-1.5">
                        <x-icon name="qrcode" class="w-3.5 h-3.5" /> Exporter les QR (PDF)
                    </a>
                @endif
            </form>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[6px] shadow-[0px_1px_4px_0px_rgba(21,34,50,0.08)] dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F5F6FA] dark:bg-slate-800 text-[#A1A7C4] dark:text-slate-500 text-[11px] font-bold uppercase tracking-wider border-b border-[#E6E9F4] dark:border-slate-700">
                        <tr>
                            @php
                                $sortLink = fn (string $col) => request()->fullUrlWithQuery(['tri' => $col, 'direction' => ($tri ?? null) === $col && ($direction ?? 'asc') === 'asc' ? 'desc' : 'asc']);
                                $sortIcon = fn (string $col) => ($tri ?? null) === $col ? (($direction ?? 'asc') === 'asc' ? '↑' : '↓') : '';
                            @endphp
                            <th class="px-6 py-4">QR</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('nom') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Emplacement {{ $sortIcon('nom') }}</a></th>
                            <th class="px-6 py-4">Chantier / Client</th>
                            <th class="px-6 py-4">Code QR</th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('created_at') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Généré le {{ $sortIcon('created_at') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('interventions_count') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Interventions {{ $sortIcon('interventions_count') }}</a></th>
                            <th class="px-6 py-4"><a href="{{ $sortLink('is_active') }}" class="hover:text-[#131523] dark:hover:text-slate-200">Statut {{ $sortIcon('is_active') }}</a></th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#E6E9F4] dark:divide-slate-800">
                        @forelse($emplacements as $e)
                            @php $jamaisUtilise = $e->interventions_count === 0; @endphp
                            <tr class="hover:bg-[#F5F6FA] dark:hover:bg-slate-800/60 transition">
                                <td class="px-6 py-4">
                                    <button type="button" @click="preview = {{ $e->id }}" class="block w-12 h-12 rounded-[4px] border border-[#E6E9F4] dark:border-slate-700 p-1 bg-white hover:border-[#1E5EFF] transition">
                                        <img src="data:image/svg+xml;base64,{{ $qrThumbs[$e->id]['thumb'] }}" alt="QR {{ $e->qr_code }}" class="w-full h-full">
                                    </button>
                                    <div x-show="preview === {{ $e->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="preview = null">
                                        <div class="bg-white dark:bg-slate-900 p-6 rounded-[6px] shadow-xl space-y-3 text-center">
                                            <img src="data:image/svg+xml;base64,{{ $qrThumbs[$e->id]['full'] }}" alt="QR {{ $e->qr_code }}" class="w-72 h-72 mx-auto">
                                            <p class="text-xs font-mono text-[#5A607F] dark:text-slate-400">{{ $e->qr_code }}</p>
                                            <div class="flex justify-center gap-3">
                                                <a href="{{ route('superadmin.supervision.emplacements.qrDownload', $e) }}" class="px-4 py-2 rounded-[4px] bg-[#1E5EFF] hover:bg-[#174ecc] text-white text-xs font-bold">Télécharger (SVG)</a>
                                                <button type="button" @click="preview = null" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-xs">
                                    <a href="{{ route('superadmin.supervision.emplacements.show', $e) }}" class="text-[#1E5EFF] hover:underline">{{ $e->nom }}</a>
                                    @if($jamaisUtilise)
                                        <x-supervision-alert-badge label="Jamais utilisé" />
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-[#5A607F] dark:text-slate-300">
                                    {{ $e->chantier?->nom ?? '—' }}
                                    <span class="block text-[11px] text-[#A1A7C4] dark:text-slate-500">{{ $e->chantier?->client?->nom }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-[#5A607F] dark:text-slate-300">{{ $e->qr_code }}</td>
                                <td class="px-6 py-4 text-[11px] font-mono text-[#A1A7C4] dark:text-slate-500">{{ $e->created_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs">
                                    <a href="{{ route('superadmin.supervision.interventions', ['emplacement_id' => $e->id]) }}" class="font-mono font-bold text-[#1E5EFF] hover:underline">{{ $e->interventions_count }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    @if($e->is_active)
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#06A561] dark:text-emerald-400 bg-[#E3FBF0] dark:bg-emerald-500/10 rounded-full">ACTIF</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] font-bold text-[#F0142F] dark:text-rose-400 bg-[#FDE3E6] dark:bg-rose-500/10 rounded-full">INACTIF</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" @click="confirmRegen = {{ $e->id }}" class="text-xs font-semibold text-[#1E5EFF] hover:underline px-3 py-1.5 bg-[#F5F6FA] dark:bg-slate-800 rounded-[4px] transition whitespace-nowrap">
                                        Régénérer le QR
                                    </button>
                                    <div x-show="confirmRegen === {{ $e->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4" @click.self="confirmRegen = null">
                                        <div class="bg-white dark:bg-slate-900 w-full max-w-md p-6 rounded-[6px] shadow-xl space-y-4 text-left">
                                            <h3 class="text-base font-bold text-[#131523] dark:text-slate-100">Régénérer le QR Code de « {{ $e->nom }} » ?</h3>
                                            <p class="text-xs text-[#5A607F] dark:text-slate-400 leading-relaxed">Le code actuel (<span class="font-mono">{{ $e->qr_code }}</span>) sera immédiatement invalidé — tout support imprimé existant ne fonctionnera plus. À utiliser uniquement en cas de perte ou de dégradation physique du support.</p>
                                            <div class="flex justify-end gap-3 pt-2">
                                                <button type="button" @click="confirmRegen = null" class="px-4 py-2 text-xs font-semibold text-[#5A607F] dark:text-slate-400 hover:text-[#131523] dark:hover:text-slate-100">Annuler</button>
                                                <form method="POST" action="{{ route('superadmin.supervision.emplacements.regenerateQr', $e) }}">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 rounded-[4px] bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold">Confirmer la régénération</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-10 text-center text-[#A1A7C4] dark:text-slate-500 italic">Aucun emplacement trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-[#F5F6FA] dark:bg-slate-800 border-t border-[#E6E9F4] dark:border-slate-700">
                {{ $emplacements->links() }}
            </div>
        </div>
    </div>
</x-super-admin-layout>
