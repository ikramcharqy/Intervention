<x-app-layout>
    <div class="space-y-6">
        <!-- Titre -->
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">Suivi Prospects &amp; Leads</h1>
            <p class="text-xs text-[#5A607F] mt-1">Vue lecture seule des prospects gérés par l'équipe commerciale.</p>
        </div>

        <!-- Table -->
        <div class="ds-card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="ds-table">
                    <thead>
                        <tr>
                            <th>Entreprise</th>
                            <th>Contact</th>
                            <th>Commercial</th>
                            <th>Statut</th>
                            <th>Dernière action</th>
                            <th class="text-right">Historique</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($prospects as $prospect)
                            @php
                                $pColor = match($prospect->statut) {
                                    'Nouveau' => 'ds-badge-light-primary',
                                    'En cours' => 'ds-badge-light-warning',
                                    'Converti' => 'ds-badge-light-success',
                                    'Perdu' => 'ds-badge-light-danger',
                                    default => 'ds-badge-light-secondary',
                                };
                                $derniereAction = collect($prospect->historique ?? [])->last();
                            @endphp
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-[4px] bg-[#ECF2FF] text-[#1E5EFF] flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($prospect->nom_entreprise, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-[#131523] truncate">{{ $prospect->nom_entreprise }}</p>
                                            <p class="text-[11px] text-[#A1A7C4] truncate">{{ $prospect->adresse ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="text-xs font-semibold text-[#131523]">{{ $prospect->nom_contact ?? '—' }}</p>
                                    <p class="text-[11px] text-[#A1A7C4] font-mono">{{ $prospect->email ?? '—' }}</p>
                                </td>
                                <td class="text-xs font-medium text-[#131523]">
                                    {{ $prospect->commercial->name ?? 'Non assigné' }}
                                </td>
                                <td>
                                    <span class="ds-badge ds-badge-sm {{ $pColor }}">{{ $prospect->statut }}</span>
                                </td>
                                <td class="text-xs text-[#5A607F]">
                                    @if($derniereAction)
                                        <span class="block font-semibold text-[#131523]">{{ $derniereAction['action'] }}</span>
                                        <span class="block text-[11px] text-[#A1A7C4]">{{ \Illuminate\Support\Carbon::parse($derniereAction['date'])->diffForHumans() }}</span>
                                    @else
                                        <span class="text-[#A1A7C4] italic">Aucune action enregistrée</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('commercial-suivi.prospects.show', $prospect) }}" class="text-xs font-semibold text-[#1E5EFF] hover:text-[#174ecc] transition">
                                        Voir l'historique →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="text-center py-14">
                                        <i class="fas fa-user-tag text-3xl text-[#D7DBEC] mb-3 block"></i>
                                        <p class="text-sm text-[#A1A7C4] font-medium">Aucun prospect enregistré.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-[#E6E9F4]">
                <p class="text-xs text-[#A1A7C4]">{{ $prospects->count() }} prospect(s)</p>
            </div>
        </div>
    </div>
</x-app-layout>
