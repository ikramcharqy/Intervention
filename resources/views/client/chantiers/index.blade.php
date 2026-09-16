<x-client-layout>
    <x-slot name="header">Mes Chantiers</x-slot>

    <div class="space-y-6">
        <div class="ui-card overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-6 pb-4">
                <div>
                    <h1 class="text-base font-bold text-slate-900">Liste de vos Chantiers</h1>
                    <p class="text-xs text-slate-400 mt-1">
                        {{ method_exists($chantiers, 'total') ? $chantiers->total() : $chantiers->count() }} chantier(s) associés à votre compte
                    </p>
                </div>
                <form method="GET" class="flex items-center">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="q" value="{{ $recherche }}" placeholder="Rechercher un chantier, une ville…"
                               class="pl-9 pr-3 py-2 text-xs rounded-lg border border-slate-200 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-400 transition w-64">
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Chantier</th>
                            <th class="py-3 px-4">Adresse / Ville</th>
                            <th class="py-3 px-4">Responsable</th>
                            <th class="py-3 px-4 text-center">Interventions</th>
                            <th class="py-3 px-4">Dernière intervention</th>
                            <th class="py-3 pr-6 pl-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($chantiers as $chantier)
                            @php $derniereInterv = $chantier->interventions->first(); @endphp
                            <tr class="hover:bg-slate-50 transition cursor-pointer" onclick="window.location='{{ route('client.chantiers.show', $chantier) }}'">
                                <td class="py-3.5 pl-6 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-sky-50 border border-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-city text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900 truncate">{{ $chantier->nom }}</p>
                                            <p class="text-[11px] text-slate-400">Code : {{ $chantier->code_chantier ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="text-slate-700">{{ $chantier->adresse ?? '—' }}</div>
                                    @if($chantier->ville)<div class="text-[11px] text-slate-400">{{ $chantier->ville }}</div>@endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="text-slate-700 font-medium">{{ $chantier->responsable ?? '—' }}</div>
                                    @if($chantier->telephone_responsable)
                                        <a href="tel:{{ $chantier->telephone_responsable }}" onclick="event.stopPropagation()" class="text-[11px] text-emerald-600 hover:text-emerald-700 font-semibold">{{ $chantier->telephone_responsable }}</a>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[24px] px-2 py-1 rounded-md text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $chantier->interventions_count }}</span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($derniereInterv)
                                        <div class="font-semibold text-slate-900">{{ $derniereInterv->code_intervention }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $derniereInterv->updated_at->format('d/m/Y') }}</div>
                                    @else
                                        <span class="text-slate-400">Aucune</span>
                                    @endif
                                </td>
                                <td class="py-3.5 pr-6 pl-4 text-right">
                                    <a href="{{ route('client.chantiers.show', $chantier) }}" class="text-xs font-bold text-sky-600 hover:text-sky-800">
                                        Consulter <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400 italic">Aucun chantier trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($chantiers, 'hasPages') && $chantiers->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $chantiers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-client-layout>
