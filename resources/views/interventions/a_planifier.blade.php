<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#181C32] font-heading flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    Interventions à Planifier
                </h1>
                <p class="text-sm text-[#A1A5B7] mt-0.5">Interventions créées par le Commercial en attente d'affectation à un technicien</p>
            </div>
            <span class="px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-bold rounded-xl flex items-center gap-2">
                <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                {{ $interventions->total() }} en attente
            </span>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- Alertes session --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl text-sm font-semibold shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Barre de filtres --}}
        <div class="metronic-card p-5">
            <form method="GET" action="{{ route('interventions.a-planifier') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wider mb-1.5">Rechercher</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Code, chantier, client..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm border border-[#EFF2F5] rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-400 bg-[#F9F9FB] text-[#181C32]">
                    </div>
                </div>
                <div class="w-full sm:w-44">
                    <label class="block text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wider mb-1.5">Priorité</label>
                    <select name="priorite" class="w-full py-2.5 px-3 text-sm border border-[#EFF2F5] rounded-xl focus:ring-2 focus:ring-blue-500 bg-[#F9F9FB] text-[#181C32]">
                        <option value="">Toutes priorités</option>
                        @foreach(['Urgente', 'Haute', 'Normale', 'Faible'] as $p)
                            <option value="{{ $p }}" @selected(($priorite ?? '') === $p)>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-5 py-2.5 bg-[#3E97FF] hover:bg-blue-600 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                    Filtrer
                </button>
                @if($search || $priorite)
                    <a href="{{ route('interventions.a-planifier') }}" class="px-4 py-2.5 bg-[#F5F8FA] hover:bg-[#EEF0F8] text-[#5E6278] text-sm font-medium rounded-xl transition">
                        Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- Table des interventions --}}
        <div class="metronic-card overflow-hidden">
            {{-- Header --}}
            <div class="px-6 py-4 border-b border-[#EFF2F5] bg-gradient-to-r from-amber-50/60 to-white flex items-center gap-3">
                <svg class="w-5 h-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <h3 class="text-sm font-bold text-[#181C32]">File d'attente de planification</h3>
                <span class="ml-auto text-xs text-[#A1A5B7]">Trié par urgence &amp; ancienneté</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-[#F9F9FB] border-b border-[#EFF2F5] text-[10px] font-bold uppercase tracking-wider text-[#A1A5B7]">
                            <th class="px-6 py-4">Référence</th>
                            <th class="px-6 py-4">Type &amp; Chantier</th>
                            <th class="px-6 py-4">Client</th>
                            <th class="px-6 py-4">Priorité</th>
                            <th class="px-6 py-4">Créé par</th>
                            <th class="px-6 py-4">Date de création</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#F5F8FA]">
                        @forelse ($interventions as $intervention)
                            @php
                                $prioriteConfig = [
                                    'Urgente' => ['bg' => 'bg-red-100',    'text' => 'text-red-700',    'border' => 'border-red-300',    'dot' => 'bg-red-500'],
                                    'Haute'   => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'border' => 'border-orange-300', 'dot' => 'bg-orange-500'],
                                    'Normale' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-300',   'dot' => 'bg-blue-500'],
                                    'Faible'  => ['bg' => 'bg-slate-100',  'text' => 'text-slate-600',  'border' => 'border-slate-300',  'dot' => 'bg-slate-400'],
                                ];
                                $pc = $prioriteConfig[$intervention->priorite] ?? $prioriteConfig['Normale'];
                                $isUrgent = $intervention->priorite === 'Urgente';
                            @endphp
                            <tr class="hover:bg-[#F9F9FB] transition group {{ $isUrgent ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($isUrgent)
                                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse shrink-0"></span>
                                        @endif
                                        <span class="font-mono font-bold text-[#3E97FF] text-sm">
                                            {{ $intervention->code_intervention }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-[#A1A5B7] mt-0.5">
                                        Statut : <span class="font-semibold text-amber-600">{{ $intervention->statut }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-[#181C32]">{{ $intervention->typeIntervention->nom ?? '—' }}</div>
                                    <div class="text-xs text-[#A1A5B7]">{{ $intervention->chantier->nom ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-[#5E6278] font-medium">
                                    {{ $intervention->chantier->client->nom ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $pc['bg'] }} {{ $pc['text'] }} {{ $pc['border'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $pc['dot'] }}"></span>
                                        {{ $intervention->priorite }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[#5E6278]">
                                    <div class="font-medium text-xs">{{ $intervention->createur->name ?? '—' }}</div>
                                    <div class="text-[11px] text-[#A1A5B7]">Commercial</div>
                                </td>
                                <td class="px-6 py-4 text-[#A1A5B7] text-xs whitespace-nowrap">
                                    {{ $intervention->created_at->format('d/m/Y') }}
                                    <div class="text-[11px]">{{ $intervention->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('interventions.planifier', $intervention) }}"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#3E97FF] to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition hover:shadow-md">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Planifier
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 rounded-2xl bg-emerald-50 flex items-center justify-center">
                                            <svg class="w-8 h-8 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-[#5E6278] font-semibold">Aucune intervention en attente de planification</p>
                                        <p class="text-[#A1A5B7] text-sm">Toutes les interventions ont été planifiées et affectées. 🎉</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($interventions->hasPages())
                <div class="px-6 py-4 border-t border-[#EFF2F5]">
                    {{ $interventions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
