<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="metronic-card p-4 text-sm font-medium bg-emerald-50 text-emerald-700 border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <div class="metronic-card overflow-hidden">
            <div class="flex items-center justify-between p-6 pb-0">
                <div>
                    <h1 class="text-base font-bold text-[#181C32] font-heading">Liste des Prospects</h1>
                    <p class="text-xs text-[#A1A5B7] mt-1">{{ $prospects->count() }} prospect(s) trouvé(s)</p>
                </div>
                <a href="{{ route('prospects.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                    <i class="fas fa-plus text-[11px]"></i> Nouveau Prospect
                </a>
            </div>

            <div class="overflow-x-auto mt-6">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-[#F9F9FB] text-[#A1A5B7] uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Entreprise</th>
                            <th class="py-3 px-4">Contact</th>
                            <th class="py-3 px-4">Téléphone</th>
                            <th class="py-3 px-4">Commercial</th>
                            <th class="py-3 px-4">Prochaine action</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 pr-6 pl-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#EFF2F5]">
                        @forelse($prospects as $prospect)
                            <tr class="hover:bg-[#F9F9FB] transition">
                                <td class="py-3.5 pl-6 pr-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-[11px] font-bold shrink-0">
                                            {{ strtoupper(substr($prospect->nom_entreprise, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-[#181C32] truncate">{{ $prospect->nom_entreprise }}</p>
                                            @if($prospect->typesIntervention->isNotEmpty())
                                                <p class="text-[11px] text-[#A1A5B7] truncate">{{ $prospect->typesIntervention->pluck('nom')->join(', ') }}</p>
                                            @elseif($prospect->adresse)
                                                <p class="text-[11px] text-[#A1A5B7] truncate">{{ Str::limit($prospect->adresse, 40) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <p class="text-[#3F4254] font-medium">{{ $prospect->nom_contact ?? '—' }}</p>
                                    @if($prospect->email)
                                        <p class="text-[11px] text-[#A1A5B7] mt-0.5">{{ $prospect->email }}</p>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-[#3F4254]">{{ $prospect->telephone ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-[#3F4254]">{{ $prospect->commercial?->name ?? '—' }}</td>
                                <td class="py-3.5 px-4">
                                    @if($prospect->prochaine_action_date)
                                        @php
                                            $paColor = $prospect->prochaine_action_en_retard ? 'bg-rose-50 text-rose-600'
                                                : ($prospect->prochaine_action_imminente ? 'bg-amber-50 text-amber-600' : 'bg-[#F5F8FA] text-[#5E6278]');
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full {{ $paColor }}">
                                            @if($prospect->prochaine_action_en_retard)
                                                <i class="fas fa-triangle-exclamation"></i>
                                            @endif
                                            {{ $prospect->prochaine_action_date->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-[#A1A5B7]">—</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    @php
                                        $sColor = match($prospect->statut) {
                                            'Nouveau' => 'bg-blue-50 text-blue-600',
                                            'Qualifié' => 'bg-emerald-50 text-emerald-600',
                                            'Négociation' => 'bg-amber-50 text-amber-600',
                                            'Converti' => 'bg-emerald-600 text-white',
                                            'Perdu' => 'bg-rose-50 text-rose-600',
                                            default => 'bg-[#F5F8FA] text-[#5E6278]'
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $sColor }}">{{ $prospect->statut }}</span>
                                </td>
                                <td class="py-3.5 pr-6 pl-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('prospects.show', $prospect) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white border border-[#EFF2F5] text-[#5E6278] text-[11px] font-bold rounded-lg hover:bg-[#F9F9FB] transition" title="Voir le détail">
                                            <i class="fas fa-eye text-[10px]"></i> Voir
                                        </a>
                                        <a href="{{ route('prospects.edit', $prospect) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white border border-[#EFF2F5] text-[#5E6278] text-[11px] font-bold rounded-lg hover:bg-[#F9F9FB] transition" title="Modifier">
                                            <i class="fas fa-pen text-[10px]"></i> Modifier
                                        </a>
                                        @if($prospect->statut !== 'Converti')
                                            <form action="{{ route('prospects.convert', $prospect) }}" method="POST" onsubmit="return confirm('Convertir ce prospect en client ?');">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 text-[11px] font-bold rounded-lg hover:bg-emerald-100 transition">
                                                    <i class="fas fa-check text-[10px]"></i> Convertir
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-[#A1A5B7] italic">
                                    Aucun prospect trouvé. <a href="{{ route('prospects.create') }}" class="text-emerald-600 font-bold hover:underline">Créer votre premier prospect</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-commercial-layout>
