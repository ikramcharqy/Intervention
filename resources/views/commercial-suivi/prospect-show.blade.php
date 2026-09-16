<x-app-layout>
    <div class="space-y-6">
        <!-- Titre + retour -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <a href="{{ route('commercial-suivi.prospects') }}" class="text-xs font-semibold text-[#5A607F] hover:text-[#1E5EFF] transition inline-flex items-center gap-1.5 mb-2">
                    <i class="fas fa-arrow-left text-[10px]"></i> Retour au suivi
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-[#131523] tracking-tight">{{ $prospect->nom_entreprise }}</h1>
            </div>
            @php
                $pColor = match($prospect->statut) {
                    'Nouveau' => 'ds-badge-light-primary',
                    'En cours' => 'ds-badge-light-warning',
                    'Converti' => 'ds-badge-light-success',
                    'Perdu' => 'ds-badge-light-danger',
                    default => 'ds-badge-light-secondary',
                };
            @endphp
            <span class="ds-badge ds-badge-md {{ $pColor }}">{{ $prospect->statut }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Infos -->
            <div class="ds-card-elevated p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-5">Informations</h3>
                <dl class="space-y-4 text-xs">
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Contact</dt>
                        <dd class="text-[#131523] font-semibold">{{ $prospect->nom_contact ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Email</dt>
                        <dd class="text-[#131523] font-mono">{{ $prospect->email ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Téléphone</dt>
                        <dd class="text-[#131523] font-mono">{{ $prospect->telephone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Adresse</dt>
                        <dd class="text-[#131523]">{{ $prospect->adresse ?? '—' }}</dd>
                    </div>
                    <div class="pt-4 border-t border-[#E6E9F4]">
                        <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Commercial responsable</dt>
                        <dd class="text-[#131523] font-semibold">{{ $prospect->commercial->name ?? 'Non assigné' }}</dd>
                    </div>
                    @if($prospect->observations)
                        <div class="pt-4 border-t border-[#E6E9F4]">
                            <dt class="text-[#A1A7C4] uppercase tracking-wider text-[11px] font-bold mb-1">Observations</dt>
                            <dd class="text-[#131523] leading-relaxed">{{ $prospect->observations }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Timeline historique -->
            <div class="ds-card-elevated lg:col-span-2 p-7">
                <h3 class="text-[16px] font-bold text-[#131523] mb-5">Historique des Actions</h3>
                @php $historique = collect($prospect->historique ?? [])->reverse(); @endphp
                @if($historique->isNotEmpty())
                    <div class="space-y-0">
                        @foreach($historique as $entry)
                            <div class="flex gap-3 pb-5 last:pb-0">
                                <div class="flex flex-col items-center">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#1E5EFF] shrink-0 mt-1"></span>
                                    @if(!$loop->last)
                                        <span class="w-px flex-1 bg-[#E6E9F4] mt-1"></span>
                                    @endif
                                </div>
                                <div class="min-w-0 pb-1">
                                    <p class="text-xs font-bold text-[#131523]">{{ $entry['action'] ?? '—' }}</p>
                                    <p class="text-[11px] text-[#A1A7C4] mt-0.5">
                                        Par {{ $entry['user'] ?? 'Inconnu' }} · {{ isset($entry['date']) ? \Illuminate\Support\Carbon::parse($entry['date'])->format('d/m/Y H:i') : '—' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-[#A1A7C4] text-xs py-8">Aucune action enregistrée pour l'instant.</p>
                @endif

                @php $notes = collect($prospect->notes ?? [])->reverse(); @endphp
                @if($notes->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-[#E6E9F4]">
                        <h4 class="text-xs font-bold text-[#131523] uppercase tracking-wider mb-3">Notes</h4>
                        <div class="space-y-3">
                            @foreach($notes as $note)
                                <div class="p-3 rounded-[6px] bg-[#F5F6FA] border border-[#E6E9F4]">
                                    <p class="text-xs text-[#131523]">{{ $note['content'] ?? '' }}</p>
                                    <p class="text-[10px] text-[#A1A7C4] mt-1.5">
                                        {{ $note['user'] ?? 'Inconnu' }} · {{ isset($note['date']) ? \Illuminate\Support\Carbon::parse($note['date'])->diffForHumans() : '' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
