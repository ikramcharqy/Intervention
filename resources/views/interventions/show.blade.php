<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('interventions.index') }}" class="flex items-center justify-center w-9 h-9 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition shadow-sm text-slate-500 hover:text-slate-700 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ $intervention->code_intervention }}</h1>
                        @php
                            // Couleurs propres à cette carte (mise en avant plus large que le badge
                        // standard) — le libellé affiché, lui, vient désormais de la même source
                        // unique que <x-soft-badge> (App\Models\Intervention::statutLabel()) pour
                        // ne plus jamais afficher la valeur technique brute sans accent.
                        $statusMap = [
                                'Demande'                => ['color' => 'bg-sky-100 text-sky-800 border-sky-200', 'dot' => 'bg-sky-500'],
                                'Planifiee'              => ['color' => 'bg-amber-100 text-amber-800 border-amber-200', 'dot' => 'bg-amber-500'],
                                'Affectee'               => ['color' => 'bg-violet-100 text-violet-800 border-violet-200', 'dot' => 'bg-violet-500'],
                                'Acceptee'               => ['color' => 'bg-blue-100 text-blue-800 border-blue-200', 'dot' => 'bg-blue-500'],
                                'En cours'               => ['color' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500 animate-pulse'],
                                'Suspendue'              => ['color' => 'bg-amber-100 text-amber-900 border-amber-300', 'dot' => 'bg-amber-600'],
                                'Reportee'               => ['color' => 'bg-slate-100 text-slate-700 border-slate-300', 'dot' => 'bg-slate-500'],
                                'Partiellement realisee' => ['color' => 'bg-orange-100 text-orange-900 border-orange-300', 'dot' => 'bg-orange-600'],
                                'Client absent'          => ['color' => 'bg-rose-100 text-rose-900 border-rose-300', 'dot' => 'bg-rose-600'],
                                'Materiel manquant'      => ['color' => 'bg-orange-100 text-orange-900 border-orange-300', 'dot' => 'bg-orange-600'],
                                'Deuxieme visite requise'=> ['color' => 'bg-indigo-100 text-indigo-900 border-indigo-300', 'dot' => 'bg-indigo-600'],
                                'Formulaire rempli'      => ['color' => 'bg-teal-100 text-teal-800 border-teal-200', 'dot' => 'bg-teal-500'],
                                'En attente validation'  => ['color' => 'bg-purple-100 text-purple-800 border-purple-200', 'dot' => 'bg-purple-500'],
                                'Rejetee'                => ['color' => 'bg-rose-100 text-rose-800 border-rose-200', 'dot' => 'bg-rose-500'],
                                'Terminee'               => ['color' => 'bg-green-100 text-green-800 border-green-200', 'dot' => 'bg-green-500'],
                                'Validee'                => ['color' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'dot' => 'bg-emerald-500'],
                                'Annulee'                => ['color' => 'bg-slate-100 text-slate-500 border-slate-200', 'dot' => 'bg-slate-400'],
                                'Rouverte'               => ['color' => 'bg-purple-100 text-purple-800 border-purple-200', 'dot' => 'bg-purple-500'],
                                'En attente reafectation'=> ['color' => 'bg-rose-100 text-rose-800 border-rose-200', 'dot' => 'bg-rose-500'],
                            ];
                            $s = $statusMap[$intervention->statut] ?? ['color' => 'bg-slate-100 text-slate-600 border-slate-200', 'dot' => 'bg-slate-400'];

                            // Retard
                            $isRetard = false;
                            $retardMsg = '';
                            if ($intervention->date_reelle_debut && $intervention->date_prevue_debut && $intervention->date_reelle_debut->gt($intervention->date_prevue_debut)) {
                                $diff = $intervention->date_prevue_debut->diffInMinutes($intervention->date_reelle_debut);
                                $retardMsg = $diff >= 60 ? round($diff/60, 1).'h de retard au démarrage' : $diff.'min de retard au démarrage';
                                $isRetard = true;
                            }
                            $isDepassement = false;
                            $depassementMsg = '';
                            if (!in_array($intervention->statut, ['Terminee','Validee','Annulee']) && $intervention->date_prevue_fin && now()->gt($intervention->date_prevue_fin)) {
                                $diff = $intervention->date_prevue_fin->diffInMinutes(now());
                                $depassementMsg = $diff >= 60 ? 'Dépassement de '.round($diff/60,1).'h vs prévision' : 'Dépassement de '.$diff.' min vs prévision';
                                $isDepassement = true;
                            }
                            $isAvance = false;
                            if ($intervention->date_reelle_debut && $intervention->date_prevue_debut && $intervention->date_reelle_debut->lt($intervention->date_prevue_debut)) {
                                $isAvance = true;
                            }

                            // Infos GPS
                            $sessionGpsActive = $intervention->gpsTrackingSessions->firstWhere('ended_at', null);
                            $tousPointsGps = $intervention->gpsTrackingSessions->flatMap->points->sortBy('captured_at')->values();
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border text-xs font-bold {{ $s['color'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                            {{ \App\Models\Intervention::statutLabel($intervention->statut) }}
                        </span>
                        @if($isRetard)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 text-xs font-semibold rounded-lg">
                                ⚠ {{ $retardMsg }}
                            </span>
                        @endif
                        @if($isAvance && !$isRetard)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-sky-50 text-sky-700 border border-sky-200 text-xs font-semibold rounded-lg">
                                ↑ Démarrage avant la date prévue
                            </span>
                        @endif
                        @if($isDepassement)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-50 text-orange-700 border border-orange-200 text-xs font-semibold rounded-lg">
                                ⏱ {{ $depassementMsg }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $intervention->typeIntervention->nom ?? '-' }} · {{ $intervention->chantier->client->nom ?? '-' }} · {{ $intervention->chantier->nom ?? '-' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                @if($intervention->statut === 'Planifiee' || $intervention->statut === 'Demande')
                    @if(auth()->user()->hasAnyRole(['admin','Admin','Super Admin','superadmin','Conducteur']))
                        <a href="{{ route('interventions.planifier', $intervention) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow transition">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Planifier &amp; Affecter
                        </a>
                    @endif
                @endif
                <a href="{{ route('interventions.edit', $intervention) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold rounded-xl shadow-sm transition">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Modifier
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Alpine.js global state --}}
    <div class="space-y-5" x-data="{
        modalSuspend: false,
        modalCancel: false,
        modalClientAbsent: false,
        modalMaterielManquant: false,
        modalPartiel: false,
        modalRevisite: false,
        modalRejectValidation: false,
        modalValidate: false,
    }">

        {{-- ── Flash messages ──────────────────────────────────────────── --}}
        @if(session('success'))
            <div class="flex items-start gap-3 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-start gap-3 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-xl text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── CHECKLIST DE COMPLÉTUDE DES PREUVES OBLIGATOIRES (ADMIN) ── --}}
        @php
            $completude = app(\App\Services\InterventionService::class)->verifierCompletudePreuves($intervention);
        @endphp
        <div class="bg-white border border-slate-200 rounded-2xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-extrabold uppercase tracking-wider px-2.5 py-0.5 rounded-full {{ $completude['pret_validation'] ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                            {{ $completude['pret_validation'] ? '✓ Dossier Prêt à la Validation' : '⚠ Validation Bloquée — Preuves Incomplètes' }}
                        </span>
                        <span class="text-xs text-slate-300 font-bold">({{ $completude['stats']['validees'] }}/{{ $completude['stats']['totales'] }} Preuves Terrain Validées)</span>
                    </div>
                    <h3 class="text-base font-black text-white mt-1">Checklist de Complétude des Preuves Terrain</h3>
                    <p class="text-xs text-slate-300">L'administrateur doit contrôler l'intégralité des 7 preuves avant de valider et clôturer l'intervention.</p>
                </div>
                <div class="flex items-center gap-2 shrink-0 flex-wrap">
                    @if($intervention->rapport)
                        <a href="{{ route('rapports.pdf', $intervention->rapport) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Télécharger PDF Officiel
                        </a>
                    @endif

                    @if(auth()->user()->hasAnyRole(['admin','Admin','Super Admin','superadmin']))
                        @if(!in_array($intervention->statut, ['Validee', 'Annulee']))
                            @if($completude['pret_validation'])
                                <form method="POST" action="{{ route('interventions.validate', $intervention) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-extrabold rounded-xl shadow-lg transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        Valider &amp; Clôturer l'Intervention
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-300 text-slate-500 text-xs font-bold rounded-xl cursor-not-allowed shadow-inner" title="Remplissez les preuves obligatoires pour débloquer la validation">
                                    🔒 Validation Bloquée ({{ count($completude['manquants']) }} Manquant(s))
                                </button>
                            @endif
                        @endif
                    @endif
                </div>
            </div>

            <!-- Grille des 7 preuves -->
            <div class="p-5 bg-slate-50/50">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                    @foreach($completude['checklist'] as $key => $item)
                        <div class="p-3 rounded-xl border {{ $item['valide'] ? 'bg-emerald-50/80 border-emerald-200 text-emerald-950' : ($item['obligatoire'] ? 'bg-rose-50/90 border-rose-300 text-rose-950 shadow-sm' : 'bg-slate-100 border-slate-200 text-slate-700') }} transition flex flex-col justify-between">
                            <div class="flex items-center justify-between gap-1 mb-1.5">
                                <span class="text-[10px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded {{ $item['valide'] ? 'bg-emerald-200 text-emerald-800' : ($item['obligatoire'] ? 'bg-rose-200 text-rose-900' : 'bg-slate-200 text-slate-600') }}">
                                    {{ $item['obligatoire'] ? 'Obligatoire' : 'Optionnel' }}
                                </span>
                                <span class="text-sm">
                                    {!! $item['valide'] ? '<span class="text-emerald-600 font-bold">✅</span>' : ($item['obligatoire'] ? '<span class="text-rose-600 font-bold">❌</span>' : '<span class="text-slate-400">⚪</span>') !!}
                                </span>
                            </div>
                            <p class="text-xs font-extrabold text-slate-900 leading-tight mb-1">{{ $item['libelle'] }}</p>
                            <p class="text-[11px] font-semibold {{ $item['valide'] ? 'text-emerald-700' : ($item['obligatoire'] ? 'text-rose-700 font-bold' : 'text-slate-500') }}">
                                {{ $item['detail'] }}
                            </p>
                        </div>
                    @endforeach
                </div>

                @if(!$completude['pret_validation'])
                    <div class="mt-4 p-3.5 bg-rose-50 border border-rose-200 rounded-xl text-rose-900 text-xs font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <span>Élément(s) obligatoire(s) manquant(s) pour la validation :</span>
                            <span class="text-rose-700 underline underline-offset-2 ml-1">{{ implode(' • ', $completude['manquants']) }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── RÉSUMÉ OPÉRATIONNEL ────────────────────────────────────── --}}
        @php
            $tachesTotal = $intervention->taches->count();
            $tachesTerminees = $intervention->taches->where('statut', 'Terminee')->count();
            $progression = $intervention->pourcentage_global ?? ($tachesTotal > 0 ? round($tachesTerminees / $tachesTotal * 100) : 0);
            $nbMateriaux = $intervention->materiaux->count();
            $nbPhotos = $intervention->rapport?->photos->count() ?? 0;
            $nbVideos = $intervention->rapport?->videos->count() ?? 0;
            $nbDocs   = $intervention->rapport?->documents->count() ?? 0;

            $derniereActivite = $intervention->historiques->sortByDesc('created_at')->first();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
            {{-- Progression --}}
            <div class="col-span-2 bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-2 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Progression globale</span>
                <div class="flex items-end gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">{{ $progression }}<span class="text-lg text-slate-400">%</span></span>
                    @if($tachesTotal > 0)
                        <span class="text-xs text-slate-500 mb-1">Tâches : {{ $tachesTerminees }}/{{ $tachesTotal }}</span>
                    @endif
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full {{ $progression >= 100 ? 'bg-emerald-500' : ($progression >= 50 ? 'bg-indigo-500' : 'bg-amber-400') }} transition-all duration-500" style="width: {{ min($progression, 100) }}%"></div>
                </div>
            </div>
            {{-- Durée --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Durée réelle</span>
                <span class="text-xl font-bold text-slate-800">{{ $intervention->duree_reelle ? $intervention->duree_reelle.'min' : '—' }}</span>
                <span class="text-xs text-slate-400">Prévue : {{ $intervention->duree_prevue ? $intervention->duree_prevue.'min' : '—' }}</span>
            </div>
            {{-- Matériaux --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Matériaux</span>
                <span class="text-xl font-bold text-slate-800">{{ $nbMateriaux }}</span>
                <span class="text-xs text-slate-400">articles utilisés</span>
            </div>
            {{-- Photos --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Photos</span>
                <span class="text-xl font-bold {{ $nbPhotos > 0 ? 'text-indigo-600' : 'text-slate-400' }}">{{ $nbPhotos }}</span>
                <span class="text-xs text-slate-400">preuves visuelles</span>
            </div>
            {{-- Vidéos --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Vidéos</span>
                <span class="text-xl font-bold {{ $nbVideos > 0 ? 'text-indigo-600' : 'text-slate-400' }}">{{ $nbVideos }}</span>
                <span class="text-xs text-slate-400">enregistrements</span>
            </div>
            {{-- GPS --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">GPS</span>
                @if($sessionGpsActive)
                    <span class="inline-flex items-center gap-1 text-sm font-bold text-emerald-600"><span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>Actif</span>
                @else
                    <span class="text-sm font-bold text-slate-400">Inactif</span>
                @endif
                <span class="text-xs text-slate-400">{{ $tousPointsGps->count() }} pts</span>
            </div>
            {{-- Dernière activité --}}
            <div class="bg-white border border-slate-200 rounded-xl p-4 flex flex-col gap-1 shadow-sm">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Dernière activité</span>
                @if($derniereActivite)
                    <span class="text-xs font-bold text-slate-700">{{ $derniereActivite->created_at->diffForHumans() }}</span>
                    <span class="text-[11px] text-slate-400 truncate">{{ $derniereActivite->statut_apres }}</span>
                @else
                    <span class="text-xs text-slate-400">—</span>
                @endif
            </div>
        </div>

        {{-- ── CORPS PRINCIPAL : 2 colonnes ───────────────────────────── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- COL. GAUCHE (2/3) : Infos + Timeline + Preuves         --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="xl:col-span-2 space-y-5">

                {{-- ── INFORMATIONS GÉNÉRALES ──────────────────────── --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Informations Générales
                        </h2>
                    </div>
                    <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Type</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->typeIntervention->nom ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Client</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->chantier->client->nom ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Chantier</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->chantier->nom ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Emplacement</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->emplacement->nom ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Technicien</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->technicien ? $intervention->technicien->prenom.' '.$intervention->technicien->name : 'Non assigné' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Priorité</p>
                            @php $priMap = ['Urgente'=>'text-rose-700 bg-rose-50 border-rose-200','Haute'=>'text-orange-700 bg-orange-50 border-orange-200','Normale'=>'text-blue-700 bg-blue-50 border-blue-200','Faible'=>'text-slate-600 bg-slate-100 border-slate-200']; @endphp
                            <span class="inline-block px-2 py-0.5 rounded-md border text-xs font-bold {{ $priMap[$intervention->priorite] ?? 'text-slate-600 bg-slate-100' }}">
                                {{ $intervention->priorite }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Créé par</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->createur->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Validé par</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->validateur->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Mode suivi</p>
                            <p class="font-semibold text-slate-800">{{ $intervention->mode_suivi ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Dates planification / réalisation --}}
                    <div class="grid grid-cols-2 divide-x divide-slate-100 border-t border-slate-100 bg-slate-50/40 text-sm">
                        <div class="p-5 space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Planification prévue</p>
                            <div class="flex justify-between"><span class="text-slate-500">Début</span><span class="font-semibold text-slate-800">{{ $intervention->date_prevue_debut?->format('d/m/Y H:i') ?? '—' }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Fin</span><span class="font-semibold text-slate-800">{{ $intervention->date_prevue_fin?->format('d/m/Y H:i') ?? '—' }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Durée prévue</span><span class="font-semibold text-slate-800">{{ $intervention->duree_prevue ? $intervention->duree_prevue.'min' : '—' }}</span></div>
                        </div>
                        <div class="p-5 space-y-2">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Réalisation effective</p>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Début</span>
                                <span class="font-semibold {{ $isAvance ? 'text-sky-600' : ($isRetard ? 'text-rose-600' : 'text-slate-800') }}">
                                    {{ $intervention->date_reelle_debut?->format('d/m/Y H:i') ?? '—' }}
                                    @if($isRetard) <span class="text-[10px] font-bold">↑ Retard</span> @endif
                                    @if($isAvance) <span class="text-[10px] font-bold">↓ Avance</span> @endif
                                </span>
                            </div>
                            <div class="flex justify-between"><span class="text-slate-500">Fin</span><span class="font-semibold text-slate-800">{{ $intervention->date_reelle_fin?->format('d/m/Y H:i') ?? '—' }}</span></div>
                            <div class="flex justify-between"><span class="text-slate-500">Durée réelle</span><span class="font-semibold text-slate-800">{{ $intervention->duree_reelle ? $intervention->duree_reelle.'min' : '—' }}</span></div>
                        </div>
                    </div>

                    {{-- Description & Motifs --}}
                    @if($intervention->description || $intervention->observations || $intervention->motif_annulation || $intervention->motif_suspension || $intervention->motif_report)
                        <div class="p-5 border-t border-slate-100 space-y-3 text-sm">
                            @if($intervention->description)
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Description de la mission</p>
                                    <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $intervention->description }}</p>
                                </div>
                            @endif
                            @if($intervention->observations)
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Résultat terrain</p>
                                    <p class="text-slate-700 whitespace-pre-line leading-relaxed">{{ $intervention->observations }}</p>
                                </div>
                            @endif
                            @if($intervention->motif_annulation)
                                <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                                    <p class="font-bold uppercase tracking-wider mb-0.5 text-rose-700">Motif d'annulation</p>
                                    <p class="italic">"{{ $intervention->motif_annulation }}"</p>
                                </div>
                            @endif
                            @if($intervention->motif_suspension)
                                <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                    <p class="font-bold uppercase tracking-wider mb-0.5 text-amber-700">Motif de suspension</p>
                                    <p class="italic">"{{ $intervention->motif_suspension }}"</p>
                                </div>
                            @endif
                            @if($intervention->motif_report)
                                <div class="p-3 rounded-lg bg-sky-50 border border-sky-200 text-sky-800 text-xs">
                                    <p class="font-bold uppercase tracking-wider mb-0.5 text-sky-700">Motif / Incident terrain</p>
                                    <p class="italic">"{{ $intervention->motif_report }}"</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- ── TÂCHES ET HIÉRARCHIE (GRANDES TÂCHES & SOUS-TÂCHES) ───── --}}
                @if($intervention->taches->isNotEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Structure des Tâches &amp; Progression Réelle ({{ $intervention->pourcentage_global ?? 0 }}%)
                            </h2>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-lg">
                                Global : {{ $intervention->pourcentage_global ?? 0 }}%
                            </span>
                        </div>

                        @php
                            // Organiser les tâches en Grandes Tâches et Sous-tâches
                            $tachesPivot = $intervention->taches->load('tache.parent');
                            $grandesTaches = $tachesPivot->filter(fn($item) => is_null($item->tache?->parent_id));
                            $sousTachesParParent = $tachesPivot->filter(fn($item) => !is_null($item->tache?->parent_id))
                                ->groupBy(fn($item) => $item->tache->parent_id);
                        @endphp

                        <div class="divide-y divide-slate-100 text-sm">
                            @foreach($grandesTaches as $grandeTacheItem)
                                @php
                                    $subItems = $sousTachesParParent->get($grandeTacheItem->tache_id, collect());
                                    $hasSub = $subItems->isNotEmpty();
                                    $avgSub = $hasSub ? round($subItems->avg('pourcentage')) : $grandeTacheItem->pourcentage;
                                @endphp
                                <div class="p-4 bg-slate-50/40">
                                    {{-- En-tête Grande Tâche --}}
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                                                📌
                                            </div>
                                            <div>
                                                <h3 class="font-extrabold text-slate-800 text-sm">
                                                    {{ $grandeTacheItem->tache->nom ?? 'Grande Tâche' }}
                                                </h3>
                                                @if($grandeTacheItem->tache->description)
                                                    <p class="text-xs text-slate-500">{{ $grandeTacheItem->tache->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-slate-700">{{ $avgSub }}%</span>
                                            @php
                                                $badgeClass = match($grandeTacheItem->statut) {
                                                    'Terminee' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'En cours' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                    default    => 'bg-slate-100 text-slate-600 border-slate-200',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-0.5 rounded-md border text-xs font-bold {{ $badgeClass }}">
                                                {{ $grandeTacheItem->statut }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Barre d'avancement Grande Tâche --}}
                                    <div class="w-full bg-slate-200 rounded-full h-2 mb-3 overflow-hidden">
                                        <div class="h-2 rounded-full {{ $avgSub >= 100 ? 'bg-emerald-500' : 'bg-indigo-600' }} transition-all duration-500" style="width: {{ $avgSub }}%"></div>
                                    </div>

                                    {{-- Sous-tâches rattachées --}}
                                    @if($hasSub)
                                        <div class="ml-4 pl-3 border-l-2 border-indigo-200 space-y-2 mt-2">
                                            @foreach($subItems as $subItem)
                                                <div class="p-2.5 bg-white rounded-lg border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-slate-400">└</span>
                                                        <span class="font-bold text-slate-700">{{ $subItem->tache->nom ?? 'Sous-tâche' }}</span>
                                                        @if($subItem->tache?->is_obligatoire)
                                                            <span class="px-1.5 py-0.2 bg-rose-100 text-rose-700 text-[10px] font-bold rounded">Obligatoire</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-24 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                            <div class="h-1.5 rounded-full {{ $subItem->pourcentage >= 100 ? 'bg-emerald-500' : 'bg-indigo-500' }}" style="width: {{ $subItem->pourcentage }}%"></div>
                                                        </div>
                                                        <span class="font-bold text-slate-800 min-w-[32px] text-right">{{ $subItem->pourcentage }}%</span>
                                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $subItem->statut === 'Terminee' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($subItem->statut === 'En cours' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-50 text-slate-500 border border-slate-200') }}">
                                                            {{ $subItem->statut }}
                                                        </span>
                                                    </div>
                                                </div>
                                                @if($subItem->commentaire)
                                                    <p class="ml-6 text-[11px] text-slate-500 italic bg-amber-50/50 p-1.5 rounded border border-amber-100">
                                                        💬 Commentaire : "{{ $subItem->commentaire }}"
                                                    </p>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($grandeTacheItem->commentaire)
                                        <p class="mt-2 text-xs text-slate-600 italic bg-slate-100/70 p-2 rounded border border-slate-200">
                                            💬 Note : "{{ $grandeTacheItem->commentaire }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach

                            {{-- Sous-tâches Orphelines (sans grande tâche parente explicite) --}}
                            @php
                                $tachesOrphelines = $tachesPivot->filter(fn($item) => !is_null($item->tache?->parent_id) && !$grandesTaches->pluck('tache_id')->contains($item->tache->parent_id));
                            @endphp
                            @if($tachesOrphelines->isNotEmpty())
                                <div class="p-4 bg-white">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Autres Tâches Spécifiques</h4>
                                    @foreach($tachesOrphelines as $item)
                                        <div class="flex items-center justify-between p-2 text-xs border border-slate-100 rounded-lg mb-1">
                                            <span class="font-semibold text-slate-700">{{ $item->tache->nom }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-slate-800">{{ $item->pourcentage }}%</span>
                                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded font-semibold">{{ $item->statut }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── FORMULAIRE DYNAMIQUE TERRAIN & RÉPONSES ────── --}}
                @if($formulaire && $formulaire->questions->isNotEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-5">
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Formulaire Dynamique ({{ $formulaire->titre ?? 'Questionnaire Terrain' }})
                            </h2>
                            @php
                                $reponsesMap = $intervention->rapport?->reponses->keyBy('question_id') ?? collect();
                                $nbReponses = $reponsesMap->count();
                            @endphp
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg {{ $nbReponses > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                {{ $nbReponses > 0 ? "{$nbReponses} réponse(s) enregistrée(s)" : 'En attente de saisie' }}
                            </span>
                        </div>

                        <div class="p-5">
                            @if($reponsesMap->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    @foreach($formulaire->questions->filter(fn($q) => $q->type_reponse !== 'Materiaux') as $q)
                                        @php
                                            $rep = $reponsesMap->get($q->id);
                                            $valeurAffichage = '—';
                                            if ($rep) {
                                                if ($rep->choixQuestion) {
                                                    $valeurAffichage = $rep->choixQuestion->libelle ?? $rep->choixQuestion->valeur;
                                                } elseif ($rep->reponse_nombre !== null) {
                                                    $valeurAffichage = $rep->reponse_nombre;
                                                } elseif ($rep->reponse_texte !== null && $rep->reponse_texte !== '') {
                                                    $valeurAffichage = $rep->reponse_texte;
                                                } elseif ($rep->reponse_fichier) {
                                                    $valeurAffichage = 'Fichier joint';
                                                }
                                            }
                                        @endphp
                                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-white transition space-y-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                                    {{ $q->question }}
                                                </span>
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-200/70 text-slate-600">
                                                    {{ $q->type_reponse }}
                                                </span>
                                            </div>
                                            @if($rep)
                                                @if(in_array($q->type_reponse, ['OuiNon', 'Oui_Non']))
                                                    <div class="pt-1">
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md border text-xs font-bold {{ in_array(strtolower($valeurAffichage), ['oui','1','true']) ? 'bg-emerald-100 text-emerald-800 border-emerald-200' : 'bg-rose-100 text-rose-800 border-rose-200' }}">
                                                            {{ in_array(strtolower($valeurAffichage), ['oui','1','true']) ? '✓ Oui' : '✕ Non' }}
                                                        </span>
                                                    </div>
                                                @elseif($rep->reponse_fichier)
                                                    <div class="pt-1">
                                                        <a href="{{ Storage::url($rep->reponse_fichier) }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:underline">
                                                            📎 Consulter le fichier joint →
                                                        </a>
                                                    </div>
                                                @else
                                                    <p class="text-xs text-slate-900 font-semibold bg-white p-2 rounded-lg border border-slate-200 mt-1">
                                                        {{ $valeurAffichage }}
                                                    </p>
                                                @endif
                                            @else
                                                <p class="text-xs text-slate-400 italic">Non renseigné</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-6 text-center text-slate-400 space-y-2">
                                    <svg class="w-9 h-9 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 01-2-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <p class="text-xs font-medium text-slate-500">Aucune réponse au formulaire dynamique enregistrée.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ── RAPPORT & PREUVES DU TECHNICIEN ────────────── --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Rapport &amp; Preuves Terrain (Technicien)
                        </h2>
                        @if($intervention->rapport)
                            <a href="{{ route('rapports.show', $intervention->rapport) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                Consulter le rapport →
                            </a>
                        @endif
                    </div>
                    <div class="p-5">
                        @if($intervention->rapport)
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                                {{-- Formulaire --}}
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Formulaire</p>
                                    @if(in_array($intervention->statut, ['Formulaire rempli','Terminee','Validee']))
                                        <span class="text-xs font-bold text-emerald-600">✓ Soumis</span>
                                    @else
                                        <span class="text-xs font-bold text-slate-400">En cours</span>
                                    @endif
                                </div>
                                {{-- Photos --}}
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Photos</p>
                                    <span class="text-xl font-extrabold {{ $nbPhotos > 0 ? 'text-indigo-600' : 'text-slate-300' }}">{{ $nbPhotos }}</span>
                                </div>
                                {{-- Vidéos --}}
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Vidéos</p>
                                    <span class="text-xl font-extrabold {{ $nbVideos > 0 ? 'text-indigo-600' : 'text-slate-300' }}">{{ $nbVideos }}</span>
                                </div>
                                {{-- Documents --}}
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Documents</p>
                                    <span class="text-xl font-extrabold {{ $nbDocs > 0 ? 'text-indigo-600' : 'text-slate-300' }}">{{ $nbDocs }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                                    <span class="text-slate-500 font-medium">Signature client</span>
                                    @if($intervention->rapport->signature_client)
                                        <span class="font-bold text-emerald-600">✓ Présente</span>
                                    @else
                                        <span class="font-bold text-slate-400">—</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                                    <span class="text-slate-500 font-medium">Signature technicien</span>
                                    @if($intervention->rapport->signature_technicien)
                                        <span class="font-bold text-emerald-600">✓ Présente</span>
                                    @else
                                        <span class="font-bold text-slate-400">—</span>
                                    @endif
                                </div>
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                                    <span class="text-slate-500 font-medium">Rapport PDF</span>
                                    @if($intervention->rapport->pdf_path)
                                        <span class="font-bold text-emerald-600">✓ Disponible</span>
                                    @else
                                        <span class="font-bold text-slate-400">—</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Aperçu visuel des signatures --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-100">
                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Signature Technicien</p>
                                    @if($intervention->rapport->signature_technicien)
                                        <div class="bg-white p-2 rounded-lg border border-slate-200 text-center">
                                            <img src="{{ Storage::url($intervention->rapport->signature_technicien) }}" class="max-h-24 mx-auto object-contain">
                                            <span class="text-[9px] font-bold text-emerald-600 mt-1 inline-block">✓ Enregistrée &amp; Traçable</span>
                                        </div>
                                    @else
                                        <div class="p-4 text-center text-xs font-semibold text-rose-500 bg-rose-50 rounded-lg border border-rose-100">
                                            ❌ Signature Technicien Manquante
                                        </div>
                                    @endif
                                </div>

                                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2">Signature Client</p>
                                    @if($intervention->rapport->signature_client)
                                        <div class="bg-white p-2 rounded-lg border border-slate-200 text-center">
                                            <img src="{{ Storage::url($intervention->rapport->signature_client) }}" class="max-h-24 mx-auto object-contain">
                                            <span class="text-[9px] font-bold text-emerald-600 mt-1 inline-block">✓ Enregistrée &amp; Traçable</span>
                                        </div>
                                    @else
                                        <div class="p-4 text-center text-xs font-semibold text-slate-400 bg-slate-100 rounded-lg">
                                            ⚪ Signature Client Non Renseignée
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @if($intervention->rapport->travaux_effectues || $intervention->rapport->observations || $intervention->rapport->recommandations)
                                <div class="mt-4 space-y-3 text-sm border-t border-slate-100 pt-4">
                                    @if($intervention->rapport->travaux_effectues)
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Travaux effectués</p>
                                            <p class="text-slate-700 leading-relaxed">{{ $intervention->rapport->travaux_effectues }}</p>
                                        </div>
                                    @endif
                                    @if($intervention->rapport->observations)
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Observations technicien</p>
                                            <p class="text-slate-700 leading-relaxed">{{ $intervention->rapport->observations }}</p>
                                        </div>
                                    @endif
                                    @if($intervention->rapport->recommandations)
                                        <div>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Recommandations</p>
                                            <p class="text-slate-700 leading-relaxed">{{ $intervention->rapport->recommandations }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center text-slate-400 space-y-2">
                                <svg class="w-10 h-10 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-sm font-semibold text-slate-500">Aucun rapport soumis</p>
                                <p class="text-xs text-slate-400">Le technicien soumettra son compte-rendu depuis l'application mobile après exécution.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── MATÉRIAUX UTILISÉS ──────────────────────────── --}}
                @if($intervention->materiaux->isNotEmpty())
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Matériaux utilisés ({{ $nbMateriaux }})
                            </h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                                    <tr>
                                        <th class="px-5 py-2.5">Matériau</th>
                                        <th class="px-5 py-2.5">Qté</th>
                                        <th class="px-5 py-2.5">Prix unitaire</th>
                                        <th class="px-5 py-2.5">Coût total</th>
                                        <th class="px-5 py-2.5">Note</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($intervention->materiaux as $item)
                                        <tr class="hover:bg-slate-50/60 transition">
                                            <td class="px-5 py-3 font-semibold text-slate-800">{{ $item->materiau->nom ?? '—' }}</td>
                                            <td class="px-5 py-3 text-slate-600">{{ $item->quantite }} {{ $item->materiau->unite ?? '' }}</td>
                                            <td class="px-5 py-3 text-slate-500">{{ $item->materiau?->prix_unitaire > 0 ? number_format($item->materiau->prix_unitaire, 2, ',', ' ').' MAD' : '—' }}</td>
                                            <td class="px-5 py-3 font-bold text-slate-800">{{ ($item->materiau?->prix_unitaire > 0) ? number_format($item->quantite * $item->materiau->prix_unitaire, 2, ',', ' ').' MAD' : '—' }}</td>
                                            <td class="px-5 py-3 text-slate-400 text-xs italic">{{ $item->commentaire ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- ── TIMELINE WORKFLOW & HISTORIQUE ──────────────── --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                        <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Journal de Workflow &amp; Traçabilité ({{ $intervention->historiques->count() }} entrées)
                        </h2>
                    </div>

                    {{-- Étapes visuelles du workflow --}}
                    @php
                        $workflowSteps = [
                            'Demande' => ['label'=>'Demande','icon'=>'📋'],
                            'Planifiee' => ['label'=>'Planifiée','icon'=>'📅'],
                            'Affectee' => ['label'=>'Affectée','icon'=>'👤'],
                            'Acceptee' => ['label'=>'Acceptée','icon'=>'✓'],
                            'En cours' => ['label'=>'En cours','icon'=>'⚡'],
                            'Formulaire rempli' => ['label'=>'Rapport soumis','icon'=>'📝'],
                            'Terminee' => ['label'=>'Terminée','icon'=>'🏁'],
                            'Validee' => ['label'=>'Validée','icon'=>'✅'],
                        ];
                        $statutOrder = array_keys($workflowSteps);
                        $currentIdx = array_search($intervention->statut, $statutOrder);
                        $isTerminal = in_array($intervention->statut, ['Annulee','Validee']);
                        $isIncident = in_array($intervention->statut, ['Suspendue','Client absent','Materiel manquant','Partiellement realisee','Deuxieme visite requise','En attente reafectation','Rejetee','Reportee']);
                    @endphp

                    <div class="px-5 pt-5 pb-4 overflow-x-auto">
                        <div class="flex items-center min-w-max gap-0">
                            @foreach($workflowSteps as $stepKey => $stepData)
                                @php
                                    $stepIdx = array_search($stepKey, $statutOrder);
                                    $isDone = ($currentIdx !== false && $stepIdx < $currentIdx) || $intervention->statut === $stepKey;
                                    $isCurrent = $intervention->statut === $stepKey;
                                    $isLast = $stepKey === array_key_last($workflowSteps);
                                @endphp
                                <div class="flex items-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm border-2 transition
                                            {{ $isCurrent ? 'bg-indigo-600 border-indigo-600 text-white ring-4 ring-indigo-100' : ($isDone ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white border-slate-200 text-slate-400') }}">
                                            @if($isDone && !$isCurrent)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                <span class="text-[13px]">{{ $stepData['icon'] }}</span>
                                            @endif
                                        </div>
                                        <span class="text-[10px] font-semibold mt-1 {{ $isCurrent ? 'text-indigo-700' : ($isDone ? 'text-emerald-600' : 'text-slate-400') }} whitespace-nowrap">
                                            {{ $stepData['label'] }}
                                        </span>
                                    </div>
                                    @if(!$isLast)
                                        <div class="w-10 h-0.5 mx-1 mb-3.5 {{ ($currentIdx !== false && $stepIdx < $currentIdx) ? 'bg-emerald-400' : 'bg-slate-200' }}"></div>
                                    @endif
                                </div>
                            @endforeach
                            @if($isIncident)
                                <div class="ml-3 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm bg-amber-500 border-2 border-amber-500 text-white ring-4 ring-amber-100">
                                        ⚠
                                    </div>
                                    <span class="text-[10px] font-semibold mt-1 text-amber-700 whitespace-nowrap">{{ \App\Models\Intervention::statutLabel($intervention->statut) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Historique détaillé --}}
                    @if($intervention->historiques->isNotEmpty())
                        <div class="px-5 pb-5">
                            <div class="border border-slate-100 rounded-xl overflow-hidden">
                                @foreach($intervention->historiques->sortByDesc('created_at') as $h)
                                    <div class="flex items-start gap-4 px-4 py-3.5 {{ !$loop->last ? 'border-b border-slate-50' : '' }} hover:bg-slate-50/60 transition group text-sm">
                                        {{-- Timeline dot + connector --}}
                                        <div class="flex flex-col items-center shrink-0 mt-0.5">
                                            <div class="w-2.5 h-2.5 rounded-full border-2 border-indigo-400 bg-white ring-2 ring-indigo-50 group-hover:bg-indigo-400 transition"></div>
                                        </div>
                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-1.5 mb-0.5">
                                                <span class="px-1.5 py-0.5 bg-slate-100 text-slate-600 text-[11px] font-mono font-semibold rounded">{{ $h->statut_avant ?: 'Création' }}</span>
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-800 text-[11px] font-mono font-bold rounded">{{ $h->statut_apres }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                                <span class="font-semibold text-slate-600">{{ $h->user->name ?? 'Système' }}</span>
                                                <span>·</span>
                                                <span>{{ $h->created_at->format('d/m/Y à H:i') }}</span>
                                                <span>·</span>
                                                <span class="italic">{{ $h->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($h->commentaire)
                                                <p class="mt-1.5 text-xs text-slate-600 bg-amber-50 border border-amber-100 px-2.5 py-1.5 rounded-lg italic leading-relaxed">
                                                    "{{ $h->commentaire }}"
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="px-5 pb-5 text-center text-slate-400 text-sm py-6">Aucun historique disponible.</div>
                    @endif
                </div>

                {{-- ── LIENS REVISITES ──────────────────────────────── --}}
                @if($intervention->interventionParente || $intervention->interventionsEnfants->isNotEmpty())
                    <div class="bg-white border border-indigo-100 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-indigo-50 bg-indigo-50/50">
                            <h2 class="text-xs font-bold text-indigo-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Traçabilité des Revisites / 2ème Passage
                            </h2>
                        </div>
                        <div class="p-5 space-y-3 text-sm">
                            @if($intervention->interventionParente)
                                <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">Intervention d'origine</p>
                                        <p class="font-bold text-slate-800">#{{ $intervention->interventionParente->code_intervention }}</p>
                                    </div>
                                    <a href="{{ route('interventions.show', $intervention->interventionParente) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">Voir →</a>
                                </div>
                            @endif
                            @foreach($intervention->interventionsEnfants as $enfant)
                                <div class="flex items-center justify-between p-3 bg-indigo-50 border border-indigo-100 rounded-xl">
                                    <div>
                                        <p class="text-[10px] font-bold text-indigo-400 uppercase mb-0.5">Visite de suivi</p>
                                        <p class="font-bold text-indigo-800">#{{ $enfant->code_intervention }}</p>
                                        <p class="text-xs text-indigo-500">{{ $enfant->date_prevue_debut?->format('d/m/Y H:i') ?? '—' }} · {{ $enfant->statut }}</p>
                                    </div>
                                    <a href="{{ route('interventions.show', $enfant) }}" class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition">Voir →</a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /col-gauche --}}

            {{-- ════════════════════════════════════════════════════════ --}}
            {{-- COL. DROITE (1/3) : Statut + Actions + GPS             --}}
            {{-- ════════════════════════════════════════════════════════ --}}
            <div class="space-y-5">

                {{-- ── STATUT CARD ──────────────────────────────────── --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Statut de l'intervention</p>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-center">
                            <span class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border text-sm font-extrabold {{ $s['color'] }} shadow-sm">
                                <span class="w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                                {{ \App\Models\Intervention::statutLabel($intervention->statut) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 gap-1.5 text-xs">
                            @php
                                $dernierStatutSince = $intervention->historiques->sortByDesc('created_at')->first()?->created_at;
                            @endphp
                            <div class="flex justify-between py-1.5 border-b border-slate-50">
                                <span class="text-slate-400">Depuis</span>
                                <span class="font-semibold text-slate-700">{{ $dernierStatutSince?->format('d/m/Y H:i') ?? ($intervention->created_at->format('d/m/Y H:i')) }}</span>
                            </div>
                            <div class="flex justify-between py-1.5 border-b border-slate-50">
                                <span class="text-slate-400">Durée statut</span>
                                <span class="font-semibold text-slate-700">{{ $dernierStatutSince?->diffForHumans(['parts' => 1]) ?? $intervention->created_at->diffForHumans(['parts' => 1]) }}</span>
                            </div>
                            @if($intervention->statut === 'En cours' && $sessionGpsActive)
                                <div class="flex justify-between py-1.5 items-center">
                                    <span class="text-slate-400">GPS</span>
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                        Actif · {{ $tousPointsGps->count() }} pts
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── ACTIONS ADMIN ────────────────────────────────── --}}
                <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actions Admin</p>
                    </div>
                    <div class="p-5 space-y-2.5">

                        @php $isAdmin = auth()->user()->hasAnyRole(['admin','Admin','Super Admin','superadmin','Conducteur']); @endphp

                        {{-- ██ ACTION PRINCIPALE ██ --}}
                        {{-- Planifier --}}
                        @if(in_array($intervention->statut, ['Demande','Planifiee']) && $isAdmin)
                            <a href="{{ route('interventions.planifier', $intervention) }}" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Planifier &amp; Affecter
                            </a>
                        @endif

                        {{-- Reprendre (depuis Suspendue) --}}
                        @if($intervention->statut === 'Suspendue')
                            <form method="POST" action="{{ route('interventions.resume', $intervention) }}">
                                @csrf
                                <button type="submit" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Reprendre l'intervention
                                </button>
                            </form>
                        @endif

                        {{-- Valider (Admin, statut Formulaire rempli ou Terminee) --}}
                        @if($intervention->peutEtreValidee() && $isAdmin)
                            <button @click="modalValidate = true" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Valider &amp; Clôturer
                            </button>
                        @endif

                        {{-- Rejeter la validation (Admin, formulaire soumis) --}}
                        @if(in_array($intervention->statut, ['Formulaire rempli','En attente validation','Terminee']) && $isAdmin)
                            <button @click="modalRejectValidation = true" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                Rejeter &amp; Renvoyer au technicien
                            </button>
                        @endif

                        {{-- Réouvrir (Terminee / Validee) --}}
                        @if($intervention->peutEtreRouverte() && $isAdmin)
                            <form method="POST" action="{{ route('interventions.reopen', $intervention) }}">
                                @csrf
                                <button type="submit" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Réouvrir l'intervention
                                </button>
                            </form>
                        @endif

                        {{-- Programer 2ème visite --}}
                        @if(in_array($intervention->statut, ['Partiellement realisee','Client absent','Materiel manquant','Deuxieme visite requise','Reportee']) && $isAdmin)
                            <button @click="modalRevisite = true" class="flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                Programmer 2ème Visite
                            </button>
                        @endif

                        {{-- Consulter rapport du technicien (jamais remplir) --}}
                        @if($intervention->rapport)
                            <a href="{{ route('rapports.show', $intervention->rapport) }}" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Consulter le rapport technicien
                            </a>
                        @endif

                        {{-- ── SEPARATOR + ACTIONS SECONDAIRES ──── --}}
                        @if($intervention->peutEtreSuspendue() && $isAdmin)
                            <div class="pt-1 border-t border-slate-100">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Actions terrain</p>
                            </div>
                            <button @click="modalSuspend = true" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-semibold text-xs rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Suspendre (Motif)
                            </button>
                        @endif

                        @if($intervention->statut === 'En cours' && $isAdmin)
                            <button @click="modalClientAbsent = true" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-sky-50 hover:bg-sky-100 text-sky-800 border border-sky-200 font-semibold text-xs rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Client absent
                            </button>
                            <button @click="modalMaterielManquant = true" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-orange-50 hover:bg-orange-100 text-orange-800 border border-orange-200 font-semibold text-xs rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                Matériel manquant
                            </button>
                            <button @click="modalPartiel = true" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 font-semibold text-xs rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                                Partiellement réalisée
                            </button>
                        @endif

                        {{-- ── ACTION DESTRUCTIVE ──────────────────── --}}
                        @if($intervention->peutEtreAnnulee() && $isAdmin)
                            <div class="pt-2 border-t border-rose-50">
                                <button @click="modalCancel = true" class="flex items-center justify-center gap-2 w-full py-2 px-4 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold text-xs rounded-xl transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Annuler l'intervention
                                </button>
                            </div>
                        @endif

                        @if(!$isAdmin)
                            <div class="py-4 text-center text-xs text-slate-400 italic">
                                Vous n'avez pas les droits pour effectuer des actions sur cette intervention.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ── GPS LIVE ─────────────────────────────────────── --}}
                @if($tousPointsGps->isNotEmpty() || $sessionGpsActive)
                    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 bg-slate-50/70">
                            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Tracé GPS
                                @if($sessionGpsActive)
                                    <span class="flex items-center gap-1 text-emerald-600 font-bold text-[10px]"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>EN DIRECT</span>
                                @endif
                            </h2>
                        </div>
                        <div class="p-3">
                            <div id="carte-trajet-gps" class="w-full h-48 rounded-lg overflow-hidden border border-slate-100"></div>
                        </div>
                        @if($intervention->gpsTrackingSessions->isNotEmpty())
                            <div class="px-5 pb-4 space-y-2">
                                @foreach($intervention->gpsTrackingSessions->sortByDesc('started_at')->take(3) as $sess)
                                    @php $sec = $sess->dureeSecondes(); $durStr = sprintf('%02dh%02d', intdiv($sec, 3600), intdiv($sec % 3600, 60)); @endphp
                                    <div class="flex justify-between items-center text-xs text-slate-600 border-b border-slate-50 pb-1 last:border-0 last:pb-0">
                                        <span>{{ $sess->started_at->format('d/m H:i') }} → {{ $sess->ended_at?->format('H:i') ?? 'En cours' }}</span>
                                        <div class="flex gap-2 text-slate-400">
                                            <span>{{ $durStr }}</span>
                                            <span>{{ number_format($sess->distance_metres / 1000, 2, ',', ' ') }} km</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

            </div>{{-- /col-droite --}}

        </div>{{-- /grid --}}

        {{-- ══════════════════════════════════════════════════════════════ --}}
        {{-- MODALS                                                        --}}
        {{-- ══════════════════════════════════════════════════════════════ --}}

        {{-- Modal Valider --}}
        <div x-show="modalValidate" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-emerald-50">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Valider &amp; Clôturer l'intervention</h3>
                    <button @click="modalValidate = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <p class="text-sm text-slate-600 leading-relaxed">Vous êtes sur le point de valider et clôturer définitivement l'intervention <strong>{{ $intervention->code_intervention }}</strong>. Cette action confirmera que le travail est conforme au cahier des charges.</p>
                    <p class="text-xs text-slate-400 bg-slate-50 border border-slate-200 rounded-lg p-3">⚠ Assurez-vous d'avoir consulté le rapport et les preuves du technicien avant de valider.</p>
                    <div class="flex justify-end gap-2 pt-2">
                        <button @click="modalValidate = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button onclick="submitValidation()" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow transition">
                            Confirmer la validation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Rejeter la validation --}}
        <div x-show="modalRejectValidation" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-orange-50">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Rejeter le rapport &amp; Renvoyer au technicien</h3>
                    <button @click="modalRejectValidation = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.rejectValidation', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <p class="text-sm text-slate-600">Indiquez le motif précis du rejet pour que le technicien puisse corriger son rapport.</p>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Motif du rejet <span class="text-rose-500">*</span></label>
                        <textarea name="motif" rows="3" required minlength="10" placeholder="Ex : Photos insuffisantes, mesures non conformes, signature client manquante..." class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400 outline-none resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalRejectValidation = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow transition">Rejeter le rapport</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Suspendre --}}
        <div x-show="modalSuspend" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-amber-50">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Suspendre l'intervention</h3>
                    <button @click="modalSuspend = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.suspend', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Motif obligatoire <span class="text-rose-500">*</span></label>
                        <textarea name="motif" rows="3" required placeholder="Ex : Attente validation client, problème d'accès, intempéries..." class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalSuspend = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow transition">Confirmer la suspension</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Client Absent --}}
        <div x-show="modalClientAbsent" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-sky-50">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Déclarer Client Absent</h3>
                    <button @click="modalClientAbsent = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.client-absent', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Motif / Tentatives de contact <span class="text-rose-500">*</span></label>
                        <textarea name="motif" rows="2" required placeholder="Ex : Porte fermée, 3 appels sans réponse, interphone défaillant..." class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-400 outline-none resize-none"></textarea>
                    </div>
                    <div x-data="{ revisite: false }">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600 mb-2">
                            <input type="checkbox" x-model="revisite" class="rounded border-slate-300 text-indigo-600"> Programmer une revisite ?
                        </label>
                        <div x-show="revisite" x-cloak>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Date de revisite</label>
                            <input type="datetime-local" name="date_revisite" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-sky-400 outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalClientAbsent = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-bold rounded-xl shadow transition">Valider Client Absent</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Matériel Manquant --}}
        <div x-show="modalMaterielManquant" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-orange-50">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Déclarer Matériel Manquant</h3>
                    <button @click="modalMaterielManquant = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.materiel-manquant', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Matériel &amp; Quantités nécessaires <span class="text-rose-500">*</span></label>
                        <textarea name="motif" rows="2" required placeholder="Ex : Câble RJ45 blindé 50m, disjoncteur 32A différentiel, cosse de raccordement M6..." class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 outline-none resize-none"></textarea>
                    </div>
                    <div x-data="{ replan: false }">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600 mb-2">
                            <input type="checkbox" x-model="replan" class="rounded border-slate-300 text-indigo-600"> Replanifier après réapprovisionnement ?
                        </label>
                        <div x-show="replan" x-cloak>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Date de replanification proposée</label>
                            <input type="datetime-local" name="date_revisite" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalMaterielManquant = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold rounded-xl shadow transition">Valider Matériel Manquant</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Partiellement Réalisée --}}
        <div x-show="modalPartiel" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-purple-50">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Intervention Partiellement Réalisée</h3>
                    <button @click="modalPartiel = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.partiellement-realisee', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Travaux effectués &amp; Reste à faire <span class="text-rose-500">*</span></label>
                        <textarea name="observations" rows="3" required placeholder="Ex : 80% du câblage réalisé. Reste le raccordement au tableau principal et les tests de mise en service..." class="w-full text-xs p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-400 outline-none resize-none"></textarea>
                    </div>
                    <div x-data="{ plan2: false }">
                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600 mb-2">
                            <input type="checkbox" x-model="plan2" class="rounded border-slate-300 text-indigo-600"> Programmer une visite de finition ?
                        </label>
                        <div x-show="plan2" x-cloak>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Date de la visite de finition</label>
                            <input type="datetime-local" name="date_revisite" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-400 outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalPartiel = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow transition">Valider Intervention Partielle</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal 2ème Visite --}}
        <div x-show="modalRevisite" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-indigo-50">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-sm">Programmer une 2ème Visite / Revisite</h3>
                    <button @click="modalRevisite = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.creer-deuxieme-visite', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Date &amp; Heure prévues <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="date_prevue_debut" required value="{{ now()->addDay()->format('Y-m-d\TH:i') }}" class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Consignes pour le 2ème passage</label>
                        <input type="text" name="motif" placeholder="Ex : Finaliser le raccordement, tester la mise en service et fermer le coffret..." class="w-full text-xs p-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalRevisite = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Annuler</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow transition">Créer la 2ème visite</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Annulation --}}
        <div x-show="modalCancel" x-cloak class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-rose-200 max-w-md w-full overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-rose-100 bg-rose-50">
                    <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <h3 class="font-bold text-rose-800 text-sm">Annuler l'intervention</h3>
                    <button @click="modalCancel = false" class="ml-auto text-slate-400 hover:text-slate-600"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                <form action="{{ route('interventions.destroy', $intervention) }}" method="POST" class="px-6 py-5 space-y-4">
                    @csrf
                    @method('DELETE')
                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-lg text-xs text-rose-700 font-medium">
                        ⚠ Cette action est irréversible. L'intervention passera au statut <strong>Annulée</strong>. L'historique complet sera conservé à des fins d'audit.
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Motif d'annulation obligatoire <span class="text-rose-500">*</span></label>
                        <textarea name="motif_annulation" rows="3" required placeholder="Ex : Annulation client, doublon de dossier, travaux reportés sine die, demande hors contrat..." class="w-full text-xs p-3 border border-rose-200 rounded-xl focus:ring-2 focus:ring-rose-400 outline-none resize-none"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="modalCancel = false" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded-xl hover:bg-slate-200 transition">Fermer</button>
                        <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow transition">Confirmer l'annulation</button>
                    </div>
                </form>
            </div>
        </div>

    </div>{{-- /x-data --}}

    {{-- ── GPS MAP & VALIDATE SCRIPTS ─────────────────────────────── --}}
    @if($tousPointsGps->isNotEmpty())
        @php
            $gpsPoints = $tousPointsGps->map(fn($p) => ['lat' => (float)$p->latitude, 'lng' => (float)$p->longitude, 'at' => optional($p->captured_at)->format('H:i:s')]);
        @endphp
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const pts = @json($gpsPoints);
                if (!pts || pts.length === 0) return;
                const path = pts.map(p => [p.lat, p.lng]);
                const map = L.map('carte-trajet-gps').setView(path[0], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
                const bounds = L.latLngBounds();
                path.forEach(p => bounds.extend(p));
                L.polyline(path, { color: '#4F46E5', weight: 4, opacity: 0.9, lineCap: 'round' }).addTo(map);
                L.circleMarker(path[0], { radius: 7, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1 }).addTo(map).bindPopup('<b>Départ</b><br>' + pts[0].at);
                L.circleMarker(path[path.length - 1], { radius: 8, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1 }).addTo(map).bindPopup('<b>Dernière position</b><br>' + pts[pts.length - 1].at);
                map.fitBounds(bounds, { padding: [20, 20] });
            });
        </script>
    @endif

    <script>
        function submitValidation() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            fetch('{{ route('interventions.validate', $intervention) }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: '{}'
            }).then(r => r.json()).then(data => {
                if (data.message) {
                    window.location.reload();
                }
            }).catch(() => { window.location.reload(); });
        }
    </script>

</x-app-layout>
