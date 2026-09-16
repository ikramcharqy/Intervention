<x-client-layout>
    <x-slot name="header">Détail du Chantier</x-slot>

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-city text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-extrabold text-[#1e2530] truncate">{{ $chantier->nom }}</h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Code : {{ $chantier->code_chantier ?? 'N/A' }} · {{ $chantier->ville ?? 'Ville non précisée' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('client.chantiers.pdf', $chantier) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="{{ route('client.chantiers.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white shadow-sm hover:shadow text-slate-600 text-xs font-bold transition">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.4fr] gap-5 items-start">

            <!-- Informations générales -->
            <div class="min-w-0 bg-white rounded-2xl p-6 shadow-sm space-y-5">
                <h2 class="text-sm font-extrabold text-[#1e2530]">Informations Générales</h2>

                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Adresse</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $chantier->adresse ?? '—' }}</p>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Responsable</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $chantier->responsable ?? '—' }}</p>
                    <div class="flex flex-col gap-1 mt-1.5">
                        @if($chantier->telephone_responsable)
                            <a href="tel:{{ $chantier->telephone_responsable }}" class="inline-flex items-center gap-1.5 text-xs text-emerald-600 hover:text-emerald-700 font-semibold">
                                <i class="fas fa-phone text-[10px]"></i> {{ $chantier->telephone_responsable }}
                            </a>
                        @endif
                        @if($chantier->email_responsable)
                            <a href="mailto:{{ $chantier->email_responsable }}" class="inline-flex items-center gap-1.5 text-xs text-emerald-600 hover:text-emerald-700 font-semibold">
                                <i class="fas fa-envelope text-[10px]"></i> {{ $chantier->email_responsable }}
                            </a>
                        @endif
                    </div>
                </div>

                @if($chantier->client && $chantier->client->contacts->isNotEmpty())
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Autres contacts de l'entreprise</p>
                        <div class="space-y-2.5">
                            @foreach($chantier->client->contacts as $contact)
                                <div class="text-xs">
                                    <p class="font-semibold text-[#1e2530]">{{ trim($contact->prenom . ' ' . $contact->nom) }} @if($contact->fonction)<span class="text-slate-400 font-normal">— {{ $contact->fonction }}</span>@endif</p>
                                    <div class="flex items-center gap-3 mt-0.5">
                                        @if($contact->telephone)
                                            <a href="tel:{{ $contact->telephone }}" class="text-emerald-600 hover:text-emerald-700"><i class="fas fa-phone text-[10px]"></i> {{ $contact->telephone }}</a>
                                        @endif
                                        @if($contact->email)
                                            <a href="mailto:{{ $contact->email }}" class="text-emerald-600 hover:text-emerald-700"><i class="fas fa-envelope text-[10px]"></i> {{ $contact->email }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($chantier->description)
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Description / Notes</p>
                        <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3 mt-1.5 leading-relaxed">{{ $chantier->description }}</p>
                    </div>
                @endif

                <div class="border-t border-slate-100 pt-4">
                    <a href="{{ route('client.documents.index', ['chantier_id' => $chantier->id]) }}" class="inline-flex items-center justify-between w-full gap-2 px-3.5 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold transition">
                        <span class="flex items-center gap-2"><i class="fas fa-folder-open text-slate-400"></i> Documents liés à ce chantier</span>
                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full bg-white text-slate-500 text-[10px] font-extrabold">{{ $documentsCount }}</span>
                    </a>
                </div>
            </div>

            <!-- Mini carte -->
            <div class="min-w-0 bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-sm font-extrabold text-[#1e2530] mb-4">Localisation</h2>
                @if($chantier->latitude && $chantier->longitude)
                    <div id="carteChantier" class="rounded-xl overflow-hidden" style="height: 280px;"></div>
                @else
                    <div class="h-[280px] rounded-xl bg-slate-50 flex flex-col items-center justify-center text-slate-400">
                        <i class="fas fa-map-location-dot text-2xl mb-2"></i>
                        <p class="text-xs">Coordonnées GPS non renseignées pour ce chantier.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Interventions -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 p-6 pb-4">
                <div>
                    <h2 class="text-sm font-extrabold text-[#1e2530]">Interventions sur ce chantier</h2>
                    <p class="text-xs text-slate-400 mt-1">{{ $interventions->total() }} intervention(s) au total</p>
                </div>
                <form method="GET" class="flex items-center gap-2">
                    <select name="statut" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="tous" {{ $statutFiltre === 'tous' ? 'selected' : '' }}>Tous les statuts</option>
                        <option value="Planifiee" {{ $statutFiltre === 'Planifiee' ? 'selected' : '' }}>Planifiée</option>
                        <option value="En cours" {{ $statutFiltre === 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminee" {{ $statutFiltre === 'Terminee' ? 'selected' : '' }}>Terminée</option>
                        <option value="Annulee" {{ $statutFiltre === 'Annulee' ? 'selected' : '' }}>Annulée</option>
                    </select>
                    <select name="tri" onchange="this.form.submit()" class="text-xs rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/30">
                        <option value="date_desc" {{ $tri === 'date_desc' ? 'selected' : '' }}>Date (récent → ancien)</option>
                        <option value="date_asc" {{ $tri === 'date_asc' ? 'selected' : '' }}>Date (ancien → récent)</option>
                        <option value="statut" {{ $tri === 'statut' ? 'selected' : '' }}>Statut</option>
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 uppercase font-bold text-[10px]">
                            <th class="py-3 pl-6 pr-4">Code</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Date prévue</th>
                            <th class="py-3 px-4">Statut</th>
                            <th class="py-3 pr-6 pl-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($interventions as $interv)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 pl-6 pr-4 font-bold text-[#1e2530]">{{ $interv->code_intervention }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $interv->typeIntervention?->nom ?? '—' }}</td>
                                <td class="py-3.5 px-4 text-slate-500">{{ $interv->date_prevue_debut ? $interv->date_prevue_debut->format('d/m/Y H:i') : '—' }}</td>
                                <td class="py-3.5 px-4"><x-soft-badge :status="$interv->statut" /></td>
                                <td class="py-3.5 pr-6 pl-4 text-right">
                                    <a href="{{ route('client.interventions.show', $interv) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                                        Consulter <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-400 italic">Aucune intervention enregistrée sur ce chantier.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($interventions->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $interventions->links() }}
                </div>
            @endif
        </div>

    </div>

    @if($chantier->latitude && $chantier->longitude)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            const map = L.map('carteChantier').setView([{{ $chantier->latitude }}, {{ $chantier->longitude }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap',
            }).addTo(map);
            L.marker([{{ $chantier->latitude }}, {{ $chantier->longitude }}]).addTo(map)
                .bindPopup(@json($chantier->nom));
        </script>
    @endif
</x-client-layout>
