<x-client-layout>
    <x-slot name="header">Détail Intervention {{ $intervention->code_intervention }}</x-slot>

    <div class="space-y-6">

        <!-- Header -->
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-screwdriver-wrench text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-lg font-extrabold text-[#1e2530]">{{ $intervention->code_intervention }}</h1>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <x-soft-badge :status="$intervention->statut" />
                        <span class="text-xs text-slate-400">Type : {{ $intervention->typeIntervention?->nom ?? 'Standard' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                @if($intervention->rapport)
                    <a href="{{ route('client.rapports.show', $intervention->rapport) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition">
                        <i class="fas fa-file-pdf"></i> Consulter le rapport
                    </a>
                @endif
                <a href="{{ route('client.interventions.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full bg-white shadow-sm hover:shadow text-slate-600 text-xs font-bold transition">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <!-- Stepper de progression -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <x-intervention-stepper :status="$intervention->statut" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1.4fr_1fr] gap-5 items-start">

            <!-- Informations -->
            <div class="min-w-0 bg-white rounded-2xl shadow-sm p-6 space-y-5">
                <h2 class="text-sm font-extrabold text-[#1e2530]">Informations de l'Intervention</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Chantier</p>
                        @if($intervention->chantier)
                            <a href="{{ route('client.chantiers.show', $intervention->chantier) }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700 mt-1 block">
                                {{ $intervention->chantier->nom }} <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                            <p class="text-xs text-slate-400">{{ $intervention->chantier->adresse }}</p>
                        @else
                            <p class="text-sm font-semibold text-[#1e2530] mt-1">—</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Emplacement</p>
                        <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $intervention->emplacement?->nom ?? 'Principal' }}</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Technicien assigné</p>
                        <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $intervention->technicien?->name ?? 'Non assigné' }}</p>
                        @if($intervention->technicien?->telephone)
                            <a href="tel:{{ $intervention->technicien->telephone }}" class="inline-flex items-center gap-1.5 text-xs text-emerald-600 hover:text-emerald-700 font-semibold mt-0.5">
                                <i class="fas fa-phone text-[10px]"></i> {{ $intervention->technicien->telephone }}
                            </a>
                        @endif
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Priorité</p>
                        <div class="mt-1"><x-soft-badge :status="$intervention->priorite" /></div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Description</p>
                    <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 leading-relaxed">{{ $intervention->description ?? 'Aucune description fournie.' }}</p>
                </div>

                @if($intervention->observations)
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Observations</p>
                        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-xl p-3.5 leading-relaxed">{{ $intervention->observations }}</p>
                    </div>
                @endif
            </div>

            <!-- Planning -->
            <div class="min-w-0 bg-white rounded-2xl shadow-sm p-6 space-y-4">
                <h2 class="text-sm font-extrabold text-[#1e2530]">Planning & Dates</h2>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date prévue début</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $intervention->date_prevue_debut ? $intervention->date_prevue_debut->format('d/m/Y H:i') : '—' }}</p>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date prévue fin</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $intervention->date_prevue_fin ? $intervention->date_prevue_fin->format('d/m/Y H:i') : '—' }}</p>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date réelle début</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $intervention->date_reelle_debut ? $intervention->date_reelle_debut->format('d/m/Y H:i') : 'Non commencée' }}</p>
                </div>
                <div class="border-t border-slate-100 pt-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date réelle fin</p>
                    <p class="text-sm font-semibold text-[#1e2530] mt-1">{{ $intervention->date_reelle_fin ? $intervention->date_reelle_fin->format('d/m/Y H:i') : 'Non terminée' }}</p>
                </div>
            </div>
        </div>

        <!-- Rapport, photos, documents rattachés -->
        @if($intervention->rapport)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-extrabold text-[#1e2530]">Rapport & pièces jointes</h2>
                    <a href="{{ route('client.rapports.show', $intervention->rapport) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700">
                        Voir le rapport complet <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                @if($intervention->rapport->travaux_effectues || $intervention->rapport->commentaire)
                    <p class="text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 leading-relaxed mb-4">
                        {{ $intervention->rapport->travaux_effectues ?? $intervention->rapport->commentaire }}
                    </p>
                @endif

                @if($intervention->rapport->photos->isNotEmpty())
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Photos ({{ $intervention->rapport->photos->count() }})</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2 mb-4">
                        @foreach($intervention->rapport->photos as $photo)
                            <a href="{{ Storage::url($photo->chemin) }}" target="_blank" class="block aspect-square rounded-lg overflow-hidden border border-slate-100 hover:opacity-90 transition">
                                <img src="{{ Storage::url($photo->chemin) }}" alt="Photo" class="w-full h-full object-cover">
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($intervention->rapport->documents->isNotEmpty())
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Documents ({{ $intervention->rapport->documents->count() }})</p>
                    <div class="space-y-1.5">
                        @foreach($intervention->rapport->documents as $doc)
                            <a href="{{ route('client.documents.download', $doc) }}" class="flex items-center gap-2.5 p-2.5 rounded-lg hover:bg-slate-50 transition text-xs">
                                <i class="fas fa-file text-slate-400"></i>
                                <span class="font-semibold text-slate-700">{{ $doc->nom_original }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($intervention->rapport->photos->isEmpty() && $intervention->rapport->documents->isEmpty() && !$intervention->rapport->travaux_effectues && !$intervention->rapport->commentaire)
                    <p class="text-xs text-slate-400 italic">Aucune photo ni document joint à ce rapport pour le moment.</p>
                @endif
            </div>
        @endif

    </div>
</x-client-layout>
