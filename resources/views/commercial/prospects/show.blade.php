<x-commercial-layout>
    <x-slot name="header"></x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="metronic-card p-4 text-sm font-medium bg-emerald-50 text-emerald-700 border-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        <!-- Header Bar -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg font-extrabold shrink-0">
                    {{ strtoupper(substr($prospect->nom_entreprise, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-[#181C32] font-heading">{{ $prospect->nom_entreprise }}</h1>
                    <div class="flex items-center gap-2 mt-1">
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
                        @if($prospect->commercial)
                            <span class="text-xs text-[#A1A5B7]">Commercial : {{ $prospect->commercial->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('prospects.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#EFF2F5] text-[#5E6278] text-xs font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                    <i class="fas fa-arrow-left text-[11px]"></i> Retour
                </a>
                <a href="{{ route('prospects.edit', $prospect) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-white border border-[#EFF2F5] text-[#5E6278] text-xs font-bold rounded-lg hover:bg-[#F9F9FB] transition">
                    <i class="fas fa-pen text-[11px]"></i> Modifier
                </a>
                @if($prospect->statut !== 'Converti')
                    <a href="{{ route('commercial.devis.create', ['prospect_id' => $prospect->id]) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                        <i class="fas fa-plus text-[11px]"></i> Créer un Devis
                    </a>
                    <form action="{{ route('prospects.convert', $prospect) }}" method="POST" onsubmit="return confirm('Convertir ce prospect en client ?');">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-[#181C32] hover:bg-black text-white text-xs font-bold rounded-lg transition">
                            <i class="fas fa-check text-[11px]"></i> Convertir en Client
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Alerte prochaine action -->
        @if($prospect->prochaine_action_date)
            @php
                $alerteColor = $prospect->prochaine_action_en_retard ? 'bg-rose-50 border-rose-100 text-rose-700'
                    : ($prospect->prochaine_action_imminente ? 'bg-amber-50 border-amber-100 text-amber-700'
                    : 'bg-[#F5F8FA] border-[#EFF2F5] text-[#5E6278]');
                $alerteIcon = $prospect->prochaine_action_en_retard ? 'fa-triangle-exclamation' : 'fa-calendar-check';
            @endphp
            <div class="metronic-card p-4 flex items-center gap-3 {{ $alerteColor }}">
                <i class="fas {{ $alerteIcon }} text-sm shrink-0"></i>
                <div class="text-xs font-semibold">
                    Prochaine action : {{ $prospect->prochaine_action_date->format('d/m/Y') }}
                    @if($prospect->prochaine_action_description)
                        — {{ $prospect->prochaine_action_description }}
                    @endif
                    @if($prospect->prochaine_action_en_retard)
                        <span class="font-bold">(en retard)</span>
                    @elseif($prospect->prochaine_action_imminente)
                        <span class="font-bold">(imminente)</span>
                    @endif
                </div>
            </div>
        @endif

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.6fr] gap-6 items-start">

            <!-- Left: Info Card -->
            <div class="space-y-6">
                <div class="metronic-card p-6">
                    <h2 class="text-sm font-bold text-[#181C32] font-heading mb-4">Informations générales</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-1">{{ $prospect->type_prospect === 'Particulier' ? 'Nom complet' : 'Entreprise' }}</p>
                            <p class="text-sm font-semibold text-[#181C32]">{{ $prospect->nom_entreprise }} <span class="text-[11px] font-normal text-[#A1A5B7]">({{ $prospect->type_prospect }})</span></p>
                        </div>
                        <hr class="border-[#EFF2F5]">
                        <div>
                            <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-1">Contact principal</p>
                            <p class="text-sm text-[#3F4254] font-medium">{{ $prospect->nom_contact ?? '—' }}</p>
                        </div>
                        <hr class="border-[#EFF2F5]">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-1">Email</p>
                                <p class="text-sm text-emerald-600 font-medium break-all">{{ $prospect->email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-1">Téléphone</p>
                                <p class="text-sm text-[#3F4254] font-medium">{{ $prospect->telephone ?? '—' }}</p>
                            </div>
                        </div>
                        <hr class="border-[#EFF2F5]">
                        <div>
                            <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-1">Adresse</p>
                            <p class="text-sm text-[#3F4254]">{{ $prospect->adresse ?? '—' }}</p>
                        </div>
                        @if($prospect->typesIntervention->isNotEmpty())
                            <hr class="border-[#EFF2F5]">
                            <div>
                                <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-2">Type(s) de besoin</p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($prospect->typesIntervention as $type)
                                        <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-[#F5F8FA] text-[#5E6278]">{{ $type->nom }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if($prospect->observations)
                            <hr class="border-[#EFF2F5]">
                            <div>
                                <p class="text-[11px] font-bold text-[#A1A5B7] uppercase tracking-wide mb-1.5">Observations</p>
                                <p class="text-sm text-[#3F4254] bg-[#F5F8FA] rounded-lg px-3.5 py-3 leading-relaxed">{{ $prospect->observations }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Historique -->
                <div class="metronic-card p-6">
                    <h2 class="text-sm font-bold text-[#181C32] font-heading mb-4">Historique des actions</h2>
                    <div class="max-h-80 overflow-y-auto">
                        @if($prospect->activites->isNotEmpty())
                            <div class="relative pl-5">
                                <div class="absolute left-[7px] top-0 bottom-0 w-px bg-[#EFF2F5]"></div>
                                @foreach($prospect->activites as $activite)
                                    <div class="relative mb-4 pb-4 border-b border-dashed border-[#F5F5F5] last:border-0">
                                        <div class="absolute -left-[17px] top-1 w-2.5 h-2.5 rounded-full bg-white border-2 border-emerald-500"></div>
                                        <p class="text-sm font-semibold text-[#181C32]">{{ $activite->description }}</p>
                                        <p class="text-[11px] text-[#A1A5B7] mt-0.5">Par {{ $activite->user?->name ?? 'Système' }} · {{ $activite->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-[#A1A5B7] italic text-center py-8">Aucun historique disponible</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: Notes -->
            <div class="metronic-card overflow-hidden">
                <div class="p-6 pb-0">
                    <h2 class="text-sm font-bold text-[#181C32] font-heading">Notes & Échanges</h2>
                </div>

                <div class="p-6">
                    <form action="{{ route('prospects.notes.store', $prospect) }}" method="POST" class="mb-6 pb-6 border-b border-dashed border-[#EFF2F5]">
                        @csrf
                        <textarea name="content" required rows="3" placeholder="Ajouter un commentaire, échange ou rappel..." class="w-full text-sm border border-[#EFF2F5] rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-emerald-500 resize-none"></textarea>
                        <div class="flex justify-end mt-2.5">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition">
                                <i class="fas fa-plus text-[10px]"></i> Ajouter la note
                            </button>
                        </div>
                    </form>

                    <div class="max-h-[500px] overflow-y-auto space-y-3">
                        @if(!empty($prospect->notes))
                            @foreach(array_reverse($prospect->notes) as $note)
                                <div class="bg-[#F5F8FA] rounded-xl px-4 py-3.5 border-l-[3px] border-emerald-500">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                                                {{ strtoupper(substr($note['user'], 0, 2)) }}
                                            </div>
                                            <span class="text-xs font-bold text-[#181C32]">{{ $note['user'] }}</span>
                                        </div>
                                        <span class="text-[11px] text-[#A1A5B7]">{{ \Carbon\Carbon::parse($note['date'])->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-[#3F4254] leading-relaxed">{{ $note['content'] }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-[#A1A5B7] italic text-center py-10">Aucune note pour le moment</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-commercial-layout>
