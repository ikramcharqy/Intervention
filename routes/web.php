<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ChantierController;
use App\Http\Controllers\EmplacementController;
use App\Http\Controllers\FormulaireController;
use App\Http\Controllers\InterventionFormulaireController;
use App\Http\Controllers\TypeInterventionController;
use App\Http\Controllers\InterventionController;
use App\Http\Controllers\TacheController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\MateriauController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('Super Admin')) {
        return redirect()->route('superadmin.dashboard');
    }

    if (auth()->user()->hasRole('Commercial')) {
        return redirect()->route('commercial.dashboard');
    }

    if (auth()->user()->hasRole('Client')) {
        return redirect()->route('client.dashboard');
    }

    if (auth()->user()->hasRole('technicien') || auth()->user()->hasRole('Technicien')) {
        return redirect()->route('technicien.dashboard');
    }

    $stats = [
        'interventions' => \App\Models\Intervention::count(),
        'en_cours' => \App\Models\Intervention::where('statut', 'En cours')->count(),
        'terminees' => \App\Models\Intervention::where('statut', 'Terminee')->count(),
        'en_attente' => \App\Models\Intervention::where('statut', 'Formulaire rempli')->count(),
        'clients' => \App\Models\Client::count(),
        'chantiers' => \App\Models\Chantier::count(),
        'techniciens' => \App\Models\User::role('Technicien')->count(),
        'rapports' => \App\Models\Rapport::count(),
    ];

    // Dernières interventions
    $recentInterventions = \App\Models\Intervention::with(['chantier', 'technicien', 'typeIntervention'])
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // Interventions créées par heure : aujourd'hui vs hier (graphique ligne)
    $today = now()->startOfDay();
    $yesterday = now()->subDay()->startOfDay();

    $parHeureAujourdhui = \App\Models\Intervention::selectRaw('HOUR(created_at) as heure, COUNT(*) as total')
        ->whereDate('created_at', $today->toDateString())
        ->groupBy('heure')
        ->pluck('total', 'heure');

    $parHeureHier = \App\Models\Intervention::selectRaw('HOUR(created_at) as heure, COUNT(*) as total')
        ->whereDate('created_at', $yesterday->toDateString())
        ->groupBy('heure')
        ->pluck('total', 'heure');

    $heuresLabels = [];
    $interventionsAujourdhui = [];
    $interventionsHier = [];
    for ($h = 0; $h < 24; $h++) {
        $heuresLabels[] = sprintf('%02dh', $h);
        $interventionsAujourdhui[] = (int) ($parHeureAujourdhui[$h] ?? 0);
        $interventionsHier[] = (int) ($parHeureHier[$h] ?? 0);
    }

    // Interventions terminées (validées) par jour sur les 7 derniers jours (graphique barres)
    $depuis7Jours = now()->subDays(6)->startOfDay();

    $parJourTerminees = \App\Models\Intervention::selectRaw('DATE(date_validation) as jour, COUNT(*) as total')
        ->whereNotNull('date_validation')
        ->where('date_validation', '>=', $depuis7Jours)
        ->groupBy('jour')
        ->pluck('total', 'jour');

    $joursLabels = [];
    $interventionsTermineesParJour = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $joursLabels[] = ucfirst($date->locale('fr')->translatedFormat('D d/m'));
        $interventionsTermineesParJour[] = (int) ($parJourTerminees[$date->toDateString()] ?? 0);
    }

    $totalCreees7Jours = \App\Models\Intervention::where('created_at', '>=', $depuis7Jours)->count();
    $totalTerminees7Jours = array_sum($interventionsTermineesParJour);

    // Pipeline des interventions (funnel) : comptage cumulatif par étape, basé sur le statut courant
    $etapeParStatut = [
        \App\Models\Intervention::STATUT_DEMANDE => 0,
        \App\Models\Intervention::STATUT_PLANIFIEE => 0,
        \App\Models\Intervention::STATUT_AFFECTEE => 1,
        \App\Models\Intervention::STATUT_ACCEPTEE => 1,
        \App\Models\Intervention::STATUT_REFUSEE => 1,
        \App\Models\Intervention::STATUT_REPORTEE => 1,
        \App\Models\Intervention::STATUT_EN_ATTENTE_REAFFECTATION => 1,
        \App\Models\Intervention::STATUT_EN_COURS => 2,
        \App\Models\Intervention::STATUT_SUSPENDUE => 2,
        \App\Models\Intervention::STATUT_PARTIELLEMENT_REALISEE => 2,
        \App\Models\Intervention::STATUT_CLIENT_ABSENT => 2,
        \App\Models\Intervention::STATUT_MATERIEL_MANQUANT => 2,
        \App\Models\Intervention::STATUT_DEUXIEME_VISITE => 2,
        \App\Models\Intervention::STATUT_FORM_REMPLI => 2,
        \App\Models\Intervention::STATUT_EN_ATTENTE_VALID => 2,
        \App\Models\Intervention::STATUT_REJETEE => 2,
        \App\Models\Intervention::STATUT_ROUVERTE => 2,
        \App\Models\Intervention::STATUT_TERMINEE => 3,
        \App\Models\Intervention::STATUT_VALIDEE => 3,
    ];

    $etapesLabels = ['Planifiée', 'Affectée / Acceptée', 'En Cours', 'Terminée'];
    $funnelCounts = [0, 0, 0, 0];

    \App\Models\Intervention::where('statut', '!=', \App\Models\Intervention::STATUT_ANNULEE)
        ->pluck('statut')
        ->each(function ($statut) use ($etapeParStatut, &$funnelCounts) {
            $etape = $etapeParStatut[$statut] ?? 0;
            for ($i = 0; $i <= $etape; $i++) {
                $funnelCounts[$i]++;
            }
        });

    $funnelBaseCount = max($funnelCounts[0], 1);
    $funnelPercents = array_map(fn ($c) => round(($c / $funnelBaseCount) * 100), $funnelCounts);

    // Répartition par priorité (donut)
    $parPriorite = \App\Models\Intervention::selectRaw('priorite, COUNT(*) as total')
        ->groupBy('priorite')
        ->pluck('total', 'priorite');

    // Techniciens les plus actifs, classés par nombre de tâches effectuées
    $technicienTaches = \App\Models\InterventionTache::query()
        ->join('interventions', 'interventions.id', '=', 'intervention_taches.intervention_id')
        ->whereNotNull('interventions.technicien_id')
        ->where('intervention_taches.statut', 'Terminee')
        ->selectRaw('interventions.technicien_id, COUNT(*) as nb_taches')
        ->groupBy('interventions.technicien_id')
        ->orderByDesc('nb_taches')
        ->limit(5)
        ->get();

    $techniciensById = \App\Models\User::whereIn('id', $technicienTaches->pluck('technicien_id'))
        ->get()
        ->keyBy('id');

    // Taux de complétion global + répartition par priorité parmi les interventions terminées
    $totalInterventions = $stats['interventions'];
    $totalTermineesEtValidees = \App\Models\Intervention::whereIn('statut', ['Terminee', 'Validee'])->count();
    $tauxCompletionGlobal = $totalInterventions > 0 ? round(($totalTermineesEtValidees / $totalInterventions) * 100) : 0;

    $completionParPriorite = \App\Models\Intervention::whereIn('statut', ['Terminee', 'Validee'])
        ->selectRaw('priorite, COUNT(*) as total')
        ->groupBy('priorite')
        ->pluck('total', 'priorite');

    // Taux d'annulation global + répartition par période
    $totalAnnulees = \App\Models\Intervention::where('statut', 'Annulee')->count();
    $tauxAnnulationGlobal = $totalInterventions > 0 ? round(($totalAnnulees / $totalInterventions) * 100) : 0;

    $annuleesCeMois = \App\Models\Intervention::where('statut', 'Annulee')
        ->whereMonth('updated_at', now()->month)
        ->whereYear('updated_at', now()->year)
        ->count();
    $annuleesMoisDernier = \App\Models\Intervention::where('statut', 'Annulee')
        ->whereMonth('updated_at', now()->subMonth()->month)
        ->whereYear('updated_at', now()->subMonth()->year)
        ->count();

    // Répartition des interventions par ville (top 5 chantiers)
    $parVille = \App\Models\Intervention::join('chantiers', 'chantiers.id', '=', 'interventions.chantier_id')
        ->whereNotNull('chantiers.ville')
        ->selectRaw('chantiers.ville, COUNT(*) as total')
        ->groupBy('chantiers.ville')
        ->orderByDesc('total')
        ->limit(5)
        ->pluck('total', 'ville');

    $totalParVille = max($parVille->sum(), 1);

    return view('dashboard', compact(
        'stats', 'recentInterventions',
        'heuresLabels', 'interventionsAujourdhui', 'interventionsHier',
        'joursLabels', 'interventionsTermineesParJour',
        'totalCreees7Jours', 'totalTerminees7Jours',
        'etapesLabels', 'funnelCounts', 'funnelPercents',
        'parPriorite',
        'technicienTaches', 'techniciensById',
        'tauxCompletionGlobal', 'completionParPriorite',
        'tauxAnnulationGlobal', 'totalAnnulees', 'annuleesCeMois', 'annuleesMoisDernier',
        'parVille', 'totalParVille'
    ));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/request-deactivation', [ProfileController::class, 'requestDeactivation'])->name('profile.requestDeactivation');

    // Notifications temps réel (navbar) — réutilise le contrôleur de l'API mobile,
    // $request->user() fonctionne indifféremment avec le guard web ou sanctum.
    Route::get('/notifications/unread', [\App\Http\Controllers\Api\NotificationController::class, 'unread'])
        ->name('notifications.unread');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])
        ->name('notifications.read-all');

    // Suivi lecture-seule des actions Commercial (Admin) — distinct du CRUD
    // ci-dessous, réservé aux commerciaux via leur propre layout.
    Route::get('suivi-commercial/prospects', [\App\Http\Controllers\CommercialSuiviController::class, 'prospects'])
        ->name('commercial-suivi.prospects');
    Route::get('suivi-commercial/prospects/{prospect}', [\App\Http\Controllers\CommercialSuiviController::class, 'prospectShow'])
        ->name('commercial-suivi.prospects.show');
    Route::get('suivi-commercial/devis', [\App\Http\Controllers\CommercialSuiviController::class, 'devis'])
        ->name('commercial-suivi.devis');
    Route::get('suivi-commercial/devis/{devis}', [\App\Http\Controllers\CommercialSuiviController::class, 'devisShow'])
        ->name('commercial-suivi.devis.show');

    // Routes CRM Commercial
    Route::post('prospects/{prospect}/convert', [\App\Http\Controllers\ProspectController::class, 'convert'])->name('prospects.convert');
    Route::post('prospects/{prospect}/notes', [\App\Http\Controllers\ProspectController::class, 'addNote'])->name('prospects.notes.store');
    Route::resource('prospects', \App\Http\Controllers\ProspectController::class);
    
    Route::post('demande-interventions/{demandeIntervention}/valider-convertir', [\App\Http\Controllers\DemandeInterventionController::class, 'validerEtConvertir'])->name('demande-interventions.validerConvertir');
    Route::post('demande-interventions/{demandeIntervention}/refuser', [\App\Http\Controllers\DemandeInterventionController::class, 'refuser'])->name('demande-interventions.refuser');
    Route::resource('demande-interventions', \App\Http\Controllers\DemandeInterventionController::class);
    
    // Contacts liés au client
    Route::resource('clients.contacts', \App\Http\Controllers\ClientContactController::class)->shallow();

    // Routes Clients
    Route::get('clients/a-assigner', [ClientController::class, 'nonAssignes'])->name('clients.non-assignes');
    Route::post('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::post('clients/{client}/reassign', [ClientController::class, 'reassign'])->name('clients.reassign');
    Route::delete('clients/{client}/force-delete', [ClientController::class, 'forceDelete'])->name('clients.forceDelete');
    Route::resource('clients', ClientController::class);

    // Routes Chantiers
    Route::post('chantiers/{chantier}/restore', [ChantierController::class, 'restore'])->name('chantiers.restore');
    Route::resource('chantiers', ChantierController::class);

    // Routes Emplacements
    Route::get('emplacements/{emplacement}', [EmplacementController::class, 'show'])->name('emplacements.show');
    Route::post('chantiers/{chantier}/emplacements', [EmplacementController::class, 'store'])->name('emplacements.store');
    Route::put('emplacements/{emplacement}', [EmplacementController::class, 'update'])->name('emplacements.update');
    Route::delete('emplacements/{emplacement}', [EmplacementController::class, 'destroy'])->name('emplacements.destroy');
    Route::post('emplacements/{emplacement}/restore', [EmplacementController::class, 'restore'])->name('emplacements.restore');
    Route::post('emplacements/{emplacement}/move-up', [EmplacementController::class, 'moveUp'])->name('emplacements.moveUp');
    Route::post('emplacements/{emplacement}/move-down', [EmplacementController::class, 'moveDown'])->name('emplacements.moveDown');

    // Endpoints JSON pour chargement dynamique
    Route::get('clients/{client}/chantiers', [ClientController::class, 'getChantiers'])->name('clients.chantiers');
    Route::get('chantiers/{chantier}/emplacements', [ChantierController::class, 'getEmplacements'])->name('chantiers.emplacements');

    // Routes Types d'Intervention
    // Bug corrigé : le nom de paramètre auto-dérivé par Route::resource() pour
    // "types-intervention" ("types_intervention", pluriel non-singularisé) ne
    // correspondait jamais à l'argument du contrôleur ($typeIntervention) — Laravel liait
    // alors un modèle vide (id=null) au lieu du vrai type, cassant show/edit/update/destroy
    // silencieusement (aucune 404, juste des données vides). ->parameters() force le nom
    // de wildcard à correspondre exactement à la signature du contrôleur.
    Route::post('types-intervention/{typeIntervention}/toggle-active', [TypeInterventionController::class, 'toggleActive'])->name('types-intervention.toggleActive');
    Route::resource('types-intervention', TypeInterventionController::class)->parameters(['types-intervention' => 'typeIntervention']);

    // Routes Interventions — Transitions de statut
    Route::post('interventions/{intervention}/accept', [InterventionController::class, 'accept'])->name('interventions.accept');
    Route::post('interventions/{intervention}/refuse', [InterventionController::class, 'refuse'])->name('interventions.refuse');
    Route::post('interventions/{intervention}/start', [InterventionController::class, 'start'])->name('interventions.start');
    Route::post('interventions/{intervention}/suspend', [InterventionController::class, 'suspend'])->name('interventions.suspend');
    Route::post('interventions/{intervention}/resume', [InterventionController::class, 'resume'])->name('interventions.resume');
    Route::post('interventions/{intervention}/reschedule', [InterventionController::class, 'reschedule'])->name('interventions.reschedule');
    Route::post('interventions/{intervention}/validate', [InterventionController::class, 'validateIntervention'])->name('interventions.validate');
    Route::post('interventions/{intervention}/reject-validation', [InterventionController::class, 'rejectValidation'])->name('interventions.rejectValidation');
    Route::post('interventions/{intervention}/reopen', [InterventionController::class, 'reopen'])->name('interventions.reopen');

    // Nouvelles actions de statut terrain et deuxième visite
    Route::post('interventions/{intervention}/client-absent', [InterventionController::class, 'marquerClientAbsent'])->name('interventions.client-absent');
    Route::post('interventions/{intervention}/materiel-manquant', [InterventionController::class, 'marquerMaterielManquant'])->name('interventions.materiel-manquant');
    Route::post('interventions/{intervention}/partiellement-realisee', [InterventionController::class, 'marquerPartiellementRealisee'])->name('interventions.partiellement-realisee');
    Route::post('interventions/{intervention}/creer-deuxieme-visite', [InterventionController::class, 'creerDeuxiemeVisite'])->name('interventions.creer-deuxieme-visite');

    // Gestion des Tâches d'intervention & progression
    Route::post('interventions/{intervention}/taches/{tachePivot}/progress', [InterventionController::class, 'updateTacheProgress'])->name('interventions.taches.progress');
    Route::post('interventions/{intervention}/taches/{tachePivot}/start', [InterventionController::class, 'startTache'])->name('interventions.taches.start');
    Route::post('interventions/{intervention}/taches/{tachePivot}/finish', [InterventionController::class, 'finishTache'])->name('interventions.taches.finish');

    // ——— Admin : Planification & Affectation ———
    Route::get('interventions/a-planifier', [InterventionController::class, 'aPlanifier'])->name('interventions.a-planifier');
    Route::get('interventions/{intervention}/planifier', [InterventionController::class, 'planifier'])->name('interventions.planifier');
    Route::post('interventions/{intervention}/planifier', [InterventionController::class, 'savePlanification'])->name('interventions.savePlanification');
    
    // Demandes de réaffectation (Arbitrage Admin)
    Route::get('demandes-reaffectation', [\App\Http\Controllers\DemandeReaffectationController::class, 'index'])->name('demandes-reaffectation.index');
    Route::post('demandes-reaffectation/{demande}/traiter', [\App\Http\Controllers\DemandeReaffectationController::class, 'traiter'])->name('demandes-reaffectation.traiter');
    
    // Remplissage Formulaire Dynamique
    Route::get('interventions/{intervention}/formulaire', [InterventionFormulaireController::class, 'create'])->name('interventions.formulaire.create');
    Route::post('interventions/{intervention}/formulaire', [InterventionFormulaireController::class, 'store'])->name('interventions.formulaire.store');

    Route::resource('interventions', InterventionController::class);

    // Suivi GPS continu (module indépendant du pointage TrackingSession)
    Route::post('interventions/{intervention}/gps-tracking/start', [\App\Http\Controllers\GpsTrackingController::class, 'start'])->name('interventions.gpsTracking.start');
    Route::post('interventions/{intervention}/gps-tracking/{gpsTrackingSession}/stop', [\App\Http\Controllers\GpsTrackingController::class, 'stop'])->name('interventions.gpsTracking.stop');
    Route::post('interventions/{intervention}/gps-tracking/{gpsTrackingSession}/points', [\App\Http\Controllers\GpsTrackingController::class, 'storePoint'])->name('interventions.gpsTracking.points.store');

    // Routes Formulaires Dynamiques
    Route::resource('formulaires', FormulaireController::class);
    Route::post('formulaires/{formulaire}/questions', [FormulaireController::class, 'storeQuestion'])->name('formulaires.questions.store');
    Route::put('formulaires/{formulaire}/questions/{question}', [FormulaireController::class, 'updateQuestion'])->name('formulaires.questions.update');
    Route::delete('formulaires/{formulaire}/questions/{question}', [FormulaireController::class, 'destroyQuestion'])->name('formulaires.questions.destroy');
    Route::post('formulaires/{formulaire}/questions/reorder', [FormulaireController::class, 'reorderQuestions'])->name('formulaires.questions.reorder');
    Route::put('formulaires/{formulaire}/questions/{question}/choix', [FormulaireController::class, 'updateQuestionChoix'])->name('formulaires.questions.choix.update');

    // Routes Tâches
    Route::resource('taches', TacheController::class);

    // Routes Rapports
    Route::get('rapports/{rapport}/pdf', [RapportController::class, 'generatePdf'])->name('rapports.pdf');
    Route::resource('rapports', RapportController::class);

    // Routes Matériaux
    Route::get('materiaux/{materiau}/mouvements', [MateriauController::class, 'mouvements'])->name('materiaux.mouvements');
    Route::post('materiaux/{materiau}/reapprovisionner', [MateriauController::class, 'reapprovisionner'])->name('materiaux.reapprovisionner');
    Route::resource('materiaux', MateriauController::class)->parameters(['materiaux' => 'materiau']);

    // Association matériaux ↔ intervention
    Route::post('interventions/{intervention}/materiaux', [\App\Http\Controllers\InterventionMateriauController::class, 'store'])->name('interventions.materiaux.store');
    Route::delete('interventions/{intervention}/materiaux/{interventionMateriau}', [\App\Http\Controllers\InterventionMateriauController::class, 'destroy'])->name('interventions.materiaux.destroy');

    // Routes Utilisateurs
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);
    // Routes Paramètres Système — restreintes au Super Admin (confirmé) : la route
    // n'avait auparavant aucun contrôle de rôle malgré des paramètres sensibles
    // (branding, GPS, facturation).
    Route::middleware('role:Super Admin')->group(function () {
        Route::get('settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        // Étape 3 : une action de sauvegarde par sous-section (remplace l'ancien
        // 'settings.update' unique, désormais scindé).
        Route::post('settings/branding', [\App\Http\Controllers\SettingController::class, 'updateBranding'])->name('settings.updateBranding');
        Route::post('settings/gps', [\App\Http\Controllers\SettingController::class, 'updateGps'])->name('settings.updateGps');
        Route::post('settings/billing', [\App\Http\Controllers\SettingController::class, 'updateBilling'])->name('settings.updateBilling');
        Route::post('settings/preview-pdf', [\App\Http\Controllers\SettingController::class, 'previewPdf'])->name('settings.previewPdf');
    });

    // Vues simples de reporting et tracking pour la sidebar
    Route::get('/gps-tracking', function() {
        return view('gps');
    })->name('gps.index');

    Route::get('/planning', function() {
        // Interventions du mois courant (ou filtré par month GET param)
        $month = request('month', now()->month);
        $year  = request('year',  now()->year);

        $interventions = \App\Models\Intervention::with(['technicien', 'chantier', 'typeIntervention'])
            ->whereYear('date_prevue_debut', $year)
            ->whereMonth('date_prevue_debut', $month)
            ->orderBy('date_prevue_debut')
            ->get();

        // Techniciens pour le filtre
        $techniciens = \App\Models\User::role('Technicien')->orderBy('name')->get();

        // Premier jour du mois (0=dimanche, 1=lundi, ...) converti en index lundi=0
        $firstDayOfMonth = Carbon::create($year, $month, 1)->dayOfWeek; // 0=dim
        $firstDayOffset  = ($firstDayOfMonth === 0) ? 6 : $firstDayOfMonth - 1;
        $daysInMonth     = Carbon::create($year, $month)->daysInMonth;
        $monthLabel      = Carbon::create($year, $month)->locale('fr')->isoFormat('MMMM YYYY');

        // Grouper par jour
        $interventionsByDay = $interventions->groupBy(function($i) {
            return $i->date_prevue_debut->day;
        });

        return view('planning', compact(
            'interventions', 'techniciens',
            'firstDayOffset', 'daysInMonth', 'monthLabel',
            'interventionsByDay', 'month', 'year'
        ));
    })->name('planning.index');

    Route::get('/statistiques', function() {
        // Comptages globaux
        $totalInterventions  = \App\Models\Intervention::count();
        $terminees           = \App\Models\Intervention::where('statut', 'Terminee')->count();
        $enCours             = \App\Models\Intervention::where('statut', 'En cours')->count();
        $planifiees          = \App\Models\Intervention::where('statut', 'Planifiee')->count();
        $annulees            = \App\Models\Intervention::where('statut', 'Annulee')->count();

        // Taux de complétion
        $tauxCompletion = $totalInterventions > 0
            ? round(($terminees / $totalInterventions) * 100, 1)
            : 0;

        // Durée réelle moyenne en minutes
        $dureeReelleMoyenne = \App\Models\Intervention::whereNotNull('duree_reelle')
            ->avg('duree_reelle');

        // Coût matériaux moyen par intervention
        $coutMoyenMateriaux = \App\Models\InterventionMateriau::selectRaw(
            'AVG(sub.total) as avg_cout'
        )->fromSub(function($query) {
            $query->from('intervention_materiaus')
                  ->selectRaw('intervention_id, SUM(quantite) as total')
                  ->groupBy('intervention_id');
        }, 'sub')->value('avg_cout');

        // Interventions par statut (pour graphique)
        $parStatut = \App\Models\Intervention::selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Top techniciens (10 premiers)
        $topTechniciens = \App\Models\Intervention::with('technicien')
            ->whereNotNull('technicien_id')
            ->where('statut', 'Terminee')
            ->selectRaw('technicien_id, COUNT(*) as nb_interventions')
            ->groupBy('technicien_id')
            ->orderByDesc('nb_interventions')
            ->limit(10)
            ->get();

        // Interventions par priorité
        $parPriorite = \App\Models\Intervention::selectRaw('priorite, COUNT(*) as total')
            ->groupBy('priorite')
            ->pluck('total', 'priorite');

        // Interventions par mois sur les 6 derniers mois
        $parMois = \App\Models\Intervention::selectRaw(
            "DATE_FORMAT(created_at, '%Y-%m') as mois, COUNT(*) as total"
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('mois')
        ->orderBy('mois')
        ->pluck('total', 'mois');

        // Vue Commercial (portefeuille personnel) vs vue Admin (vision globale toutes
        // équipes confondues) : mêmes règles de confidentialité déjà appliquées au reste
        // du module Commercial (Portefeuille Clients, Documents, Devis).
        $isCommercialView = auth()->user()->hasRole('Commercial') && view()->exists('commercial.statistiques');
        $commercialId = auth()->id();

        $prospectsQuery = \App\Models\Prospect::query();
        if ($isCommercialView) {
            $prospectsQuery->where('commercial_id', $commercialId);
        }
        $nbProspects = (clone $prospectsQuery)->count();
        $prospectsConvertis = (clone $prospectsQuery)->where('statut', 'Converti')->count();
        $tauxConversionProspects = $nbProspects > 0 ? round(($prospectsConvertis / $nbProspects) * 100, 1) : 0;

        // Seule 'Accepté' (avec accent) peut réellement être écrite en base : StoreDevisRequest
        // valide strictement 'Brouillon,Envoyé,Accepté,Refusé', les autres variantes ('Accepte',
        // 'Validé'...) ne correspondent à aucune donnée existante.
        $devisQuery = \App\Models\Devis::query();
        if ($isCommercialView) {
            $devisQuery->where('commercial_id', $commercialId);
        }
        $nbDevis = (clone $devisQuery)->count();
        $devisAcceptes = (clone $devisQuery)->where('statut', 'Accepté')->count();
        $devisAttente = (clone $devisQuery)->whereIn('statut', ['Envoyé', 'Brouillon'])->count();
        $devisRefuses = (clone $devisQuery)->where('statut', 'Refusé')->count();
        $montantDevisAcceptes = (clone $devisQuery)->where('statut', 'Accepté')->sum('montant_ttc');
        $montantDevisTotal = (clone $devisQuery)->sum('montant_ttc');

        $parStatutDevis = (clone $devisQuery)->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // "Interventions générées par le commercial" = vraies Demandes d'Intervention
        // (même source/filtre que le module Demandes d'Intervention), pas la table
        // Intervention globale qui n'a aucun lien avec le commercial connecté.
        $demandesQuery = \App\Models\DemandeIntervention::query();
        if ($isCommercialView) {
            $demandesQuery->where('commercial_id', $commercialId);
        }
        $parStatutDemandes = (clone $demandesQuery)->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Répartition des prospects par statut (même principe que $parStatutDevis).
        $parStatutProspects = (clone $prospectsQuery)->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        // Évolution mensuelle (6 derniers mois) : nombre de devis émis et montant
        // accepté, pour visualiser la tendance plutôt qu'un simple instantané.
        $parMoisDevis = (clone $devisQuery)
            ->selectRaw(
                "DATE_FORMAT(date_emission, '%Y-%m') as mois, COUNT(*) as nb_devis, ".
                "SUM(CASE WHEN statut = 'Accepté' THEN montant_ttc ELSE 0 END) as montant_accepte"
            )
            ->where('date_emission', '>=', now()->subMonths(6))
            ->groupBy('mois')
            ->orderBy('mois')
            ->get();

        // Entonnoir de conversion : où le pipeline perd le plus de volume, du
        // premier contact prospect jusqu'à la conversion effective en client.
        $entonnoirConversion = [
            'Prospects' => $nbProspects,
            'Devis émis' => $nbDevis,
            'Devis acceptés' => $devisAcceptes,
            'Clients convertis' => $prospectsConvertis,
        ];

        $viewName = $isCommercialView ? 'commercial.statistiques' : 'statistiques';

        return view($viewName, compact(
            'totalInterventions', 'terminees', 'enCours', 'planifiees', 'annulees',
            'tauxCompletion', 'dureeReelleMoyenne', 'coutMoyenMateriaux',
            'parStatut', 'topTechniciens', 'parPriorite', 'parMois',
            'nbProspects', 'prospectsConvertis', 'tauxConversionProspects',
            'nbDevis', 'devisAcceptes', 'devisAttente', 'devisRefuses',
            'montantDevisAcceptes', 'montantDevisTotal', 'parStatutDevis',
            'parStatutDemandes', 'parStatutProspects', 'parMoisDevis', 'entonnoirConversion'
        ));
    })->name('statistiques.index');
});

// Espace dédié au rôle Commercial : routes protégées par le rôle Spatie "Commercial".
Route::middleware(['auth', 'verified', 'role:Commercial'])
    ->prefix('commercial')
    ->name('commercial.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Commercial\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::post('devis/{devis}/duplicate', [\App\Http\Controllers\Commercial\DevisController::class, 'duplicate'])
            ->name('devis.duplicate');
        Route::post('devis/{devis}/generer-facture', [\App\Http\Controllers\Commercial\DevisController::class, 'genererFacture'])
            ->name('devis.genererFacture');
        Route::get('devis/{devis}/pdf', [\App\Http\Controllers\Commercial\DevisController::class, 'generatePdf'])
            ->name('devis.pdf');
        Route::post('devis/{devis}/creer-demande', [\App\Http\Controllers\Commercial\DevisController::class, 'creerDemandeIntervention'])
            ->name('devis.creerDemande');
        // Paramètre forcé à "devis" (invariant en français) : Laravel singularise sinon
        // "devis" → "devi", ce qui désynchronise ce paramètre des routes manuelles
        // ci-dessus ({devis}) et casse la génération d'URL (route()) pour edit/update/destroy.
        Route::resource('devis', \App\Http\Controllers\Commercial\DevisController::class)
            ->parameters(['devis' => 'devis']);

        Route::get('factures', [\App\Http\Controllers\Commercial\FactureController::class, 'index'])
            ->name('factures.index');
        Route::get('factures/{facture}', [\App\Http\Controllers\Commercial\FactureController::class, 'show'])
            ->name('factures.show');
        Route::get('factures/{facture}/pdf', [\App\Http\Controllers\Commercial\FactureController::class, 'generatePdf'])
            ->name('factures.pdf');
        Route::post('factures/{facture}/marquer-payee', [\App\Http\Controllers\Commercial\FactureController::class, 'marquerPayee'])
            ->name('factures.marquerPayee');

        Route::get('/documents', [\App\Http\Controllers\Commercial\DocumentController::class, 'index'])
            ->name('documents.index');
        Route::post('/documents', [\App\Http\Controllers\Commercial\DocumentController::class, 'store'])
            ->name('documents.store');
        Route::get('/documents/{document}/download', [\App\Http\Controllers\Commercial\DocumentController::class, 'download'])
            ->name('documents.download');
        Route::delete('/documents/{document}', [\App\Http\Controllers\Commercial\DocumentController::class, 'destroy'])
            ->name('documents.destroy');
    });

// Espace dédié au rôle Client : routes protégées par le rôle Spatie "Client".
Route::middleware(['auth', 'verified', 'role:Client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ClientModule\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/profile', [\App\Http\Controllers\ClientModule\ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::put('/profile', [\App\Http\Controllers\ClientModule\ProfileController::class, 'update'])
            ->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\ClientModule\ProfileController::class, 'updatePassword'])
            ->name('profile.password');

        Route::get('/chantiers', [\App\Http\Controllers\ClientModule\ChantierController::class, 'index'])
            ->name('chantiers.index');
        Route::get('/chantiers/{chantier}', [\App\Http\Controllers\ClientModule\ChantierController::class, 'show'])
            ->name('chantiers.show');
        Route::get('/chantiers/{chantier}/pdf', [\App\Http\Controllers\ClientModule\ChantierController::class, 'pdf'])
            ->name('chantiers.pdf');

        Route::get('/interventions', [\App\Http\Controllers\ClientModule\InterventionController::class, 'index'])
            ->name('interventions.index');
        Route::get('/demandes/creer', [\App\Http\Controllers\ClientModule\InterventionController::class, 'createDemande'])
            ->name('demandes.create');
        Route::post('/demandes/creer', [\App\Http\Controllers\ClientModule\InterventionController::class, 'storeDemande'])
            ->name('demandes.store');
        Route::get('/interventions/{intervention}', [\App\Http\Controllers\ClientModule\InterventionController::class, 'show'])
            ->name('interventions.show');

        Route::get('/demandes', [\App\Http\Controllers\ClientModule\DemandeController::class, 'index'])
            ->name('demandes.index');

        Route::get('/rapports', [\App\Http\Controllers\ClientModule\RapportController::class, 'index'])
            ->name('rapports.index');
        Route::get('/rapports/{rapport}', [\App\Http\Controllers\ClientModule\RapportController::class, 'show'])
            ->name('rapports.show');
        Route::get('/rapports/{rapport}/pdf', [\App\Http\Controllers\ClientModule\RapportController::class, 'pdf'])
            ->name('rapports.pdf');
        Route::post('/rapports/{rapport}/valider', [\App\Http\Controllers\ClientModule\RapportController::class, 'valider'])
            ->name('rapports.valider');

        Route::get('/documents', [\App\Http\Controllers\ClientModule\DocumentController::class, 'index'])
            ->name('documents.index');
        Route::get('/documents/{document}/download', [\App\Http\Controllers\ClientModule\DocumentController::class, 'download'])
            ->name('documents.download');
        Route::get('/documents/{document}/preview', [\App\Http\Controllers\ClientModule\DocumentController::class, 'preview'])
            ->name('documents.preview');
        Route::get('/documents/{document}/apercu', [\App\Http\Controllers\ClientModule\DocumentController::class, 'viewer'])
            ->name('documents.viewer');

        Route::get('/facturation', [\App\Http\Controllers\ClientModule\FactureController::class, 'index'])
            ->name('factures.index');
        Route::get('/facturation/{facture}/pdf', [\App\Http\Controllers\ClientModule\FactureController::class, 'pdf'])
            ->name('factures.pdf');

        Route::get('/notifications', [\App\Http\Controllers\ClientModule\NotificationController::class, 'index'])
            ->name('notifications.index');
        Route::get('/notifications/{id}/ouvrir', [\App\Http\Controllers\ClientModule\NotificationController::class, 'open'])
            ->name('notifications.open');
        Route::post('/notifications/tout-marquer-lu', [\App\Http\Controllers\ClientModule\NotificationController::class, 'markAllRead'])
            ->name('notifications.markAllRead');

        Route::get('/support', [\App\Http\Controllers\ClientModule\SupportController::class, 'index'])
            ->name('support.index');
        Route::post('/support', [\App\Http\Controllers\ClientModule\SupportController::class, 'store'])
            ->name('support.store');

        Route::get('/securite', [\App\Http\Controllers\ClientModule\ProfileController::class, 'securite'])
            ->name('securite.index');
        Route::post('/securite/sessions/{id}/revoquer', [\App\Http\Controllers\ClientModule\ProfileController::class, 'revoquerSession'])
            ->name('securite.sessions.revoquer');
        Route::post('/securite/sessions/revoquer-autres', [\App\Http\Controllers\ClientModule\ProfileController::class, 'revoquerAutresSessions'])
            ->name('securite.sessions.revoquerAutres');
    });

// Étape 3.1 — 2FA obligatoire pour le rôle Super Admin : enrôlement/challenge, accessibles
// dès l'authentification (avant que `superadmin.2fa` n'autorise le reste de la console).
// no-back-cache : ces routes exposent un état serveur qui change (secret TOTP
// généré, statut de confirmation, session 2fa_passed_at) — sans ces en-têtes,
// un retour au setup après veille/retour arrière navigateur peut réafficher
// un QR code obsolète depuis le bfcache sans recontacter le serveur.
Route::middleware(['auth', 'no-back-cache'])->group(function () {
    Route::get('/2fa/setup', [\App\Http\Controllers\Auth\TwoFactorController::class, 'setup'])->name('two-factor.setup');
    Route::post('/2fa/confirm', [\App\Http\Controllers\Auth\TwoFactorController::class, 'confirm'])->name('two-factor.confirm');
    Route::get('/2fa/recovery-codes', [\App\Http\Controllers\Auth\TwoFactorController::class, 'recoveryCodes'])->name('two-factor.recoveryCodes');
    Route::get('/2fa/challenge', [\App\Http\Controllers\Auth\TwoFactorController::class, 'challenge'])->name('two-factor.challenge');
    Route::post('/2fa/verify', [\App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('two-factor.verify');

    // Réinitialisation : step-up auth (re-confirmation du mot de passe) obligatoire.
    Route::middleware('password.confirm')->group(function () {
        Route::post('/2fa/reset', [\App\Http\Controllers\Auth\TwoFactorController::class, 'reset'])->name('two-factor.reset');
    });
});

// Espace dédié au rôle Super Admin : routes protégées par le rôle Spatie "Super Admin".
// `superadmin.timeout` : timeout d'inactivité réduit (15 min) spécifique à ce rôle (Étape 3.3).
// 2FA obligatoire (EnsureTwoFactorForSuperAdmin) : appliquée globalement au groupe 'web' dans
// bootstrap/app.php depuis la correction du bug d'enforcement pour le rôle 'admin' — plus besoin
// de la répéter ici, elle a déjà tourné avant même d'atteindre ce groupe de routes.
Route::middleware(['auth', 'verified', 'role:Super Admin', 'superadmin.timeout'])
    ->prefix('superadmin')
    ->name('superadmin.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('/admins', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'admins'])->name('admins');
        Route::post('/admins', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'storeAdmin'])->name('admins.store');
        // Étape 5 : rappel 2FA — action non destructive/réversible (email), pas de step-up.
        Route::post('/admins/{user}/resend-2fa-reminder', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'resend2FAReminder'])->name('admins.resend2FAReminder');

        Route::get('/roles', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'roles'])->name('roles');
        Route::post('/roles', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'storeRole'])->name('roles.store');
        // Étape 3 : vue d'audit consolidée des permissions individuelles actives (lecture).
        Route::get('/roles/exceptions', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'permissionExceptions'])->name('roles.exceptions');

        Route::get('/users', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'users'])->name('users');
        Route::get('/users/{user}', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'showUser'])->name('users.show');
        Route::post('/users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'toggleUserStatus'])->name('users.toggleStatus');

        Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'updateSettings'])->name('settings.update');

        Route::get('/logs', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'logs'])->name('logs');

        // Étape 3 — point d'entrée découvrable pour la 2FA/historique de connexion du
        // compte Super Admin connecté (les routes /2fa/* existaient déjà mais sans lien
        // de navigation).
        Route::get('/securite', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'securite'])->name('securite.index');
        Route::post('/securite/sessions/{id}/revoquer', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'securiteRevoquerSession'])->name('securite.sessions.revoquer');
        Route::post('/securite/sessions/revoquer-autres', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'securiteRevoquerAutresSessions'])->name('securite.sessions.revoquerAutres');

        Route::get('/backups', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'backups'])->name('backups');
        Route::get('/backups/{filename}/download', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'downloadBackup'])->name('backups.download');

        // Étape 3 — Supervision Métier transverse (lecture) : ne touche à aucune route
        // Admin/Commercial existante sur ces mêmes entités.
        Route::get('/supervision/chantiers', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'chantiers'])->name('supervision.chantiers');
        Route::get('/supervision/chantiers/{chantier}', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'chantierShow'])->name('supervision.chantiers.show');
        Route::get('/supervision/interventions', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'interventions'])->name('supervision.interventions');
        Route::get('/supervision/interventions/{intervention}', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'interventionShow'])->name('supervision.interventions.show');
        Route::get('/supervision/emplacements', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'emplacements'])->name('supervision.emplacements');
        Route::get('/supervision/emplacements/{emplacement}', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'emplacementShow'])->name('supervision.emplacements.show');
        Route::get('/supervision/clients', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'clients'])->name('supervision.clients');
        Route::get('/supervision/clients/{client}', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'clientShow'])->name('supervision.clients.show');
        // Régénération d'un QR Code (support imprimé perdu/dégradé) : action ponctuelle,
        // réversible par réimpression — confirmation JS suffisante, pas de step-up
        // généralisé ici (cf. règle : à évaluer au cas par cas, pas systématiquement).
        Route::post('/supervision/emplacements/{emplacement}/regenerate-qr', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'regenerateQrCode'])->name('supervision.emplacements.regenerateQr');
        Route::get('/supervision/emplacements/{emplacement}/qr-code/download', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'emplacementQrDownload'])->name('supervision.emplacements.qrDownload');
        Route::get('/supervision/chantiers/{chantier}/qr-codes/export', [\App\Http\Controllers\SuperAdmin\SupervisionController::class, 'emplacementsQrExport'])->name('supervision.chantiers.qrExport');

        // Étape 3.4 — Step-up auth (re-confirmation du mot de passe) avant toute action
        // destructive/sensible : dump SQL, changement de rôle, révélation de clé API,
        // désactivation de compte, révocation de sessions.
        Route::middleware('password.confirm')->group(function () {
            Route::post('/users/{user}/role', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'updateUserRole'])->name('users.updateRole');
            // Étape 1 : matrice de permissions par rôle. Étape 2 : permissions
            // individuelles (exceptions, justification obligatoire) — même exigence de
            // step-up auth que tout changement de privilège.
            Route::post('/roles/{role}/permissions', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'updateRolePermissions'])->name('roles.permissions.update');
            Route::post('/users/{user}/permissions/grant', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'grantIndividualPermission'])->name('users.permissions.grant');
            Route::post('/users/{user}/permissions/revoke', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'revokeIndividualPermission'])->name('users.permissions.revoke');
            Route::post('/backups', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'createBackup'])->name('backups.create');
            Route::delete('/backups/{filename}', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'deleteBackup'])->name('backups.delete');
            Route::post('/settings/reveal-key', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'revealApiKey'])->name('settings.revealKey');
            Route::post('/settings/reveal-rtsp', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'revealRtspUrl'])->name('settings.revealRtsp');
            Route::post('/incidents/disable-account', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'quickDisableAccount'])->name('incidents.disableAccount');
            Route::post('/incidents/revoke-sessions', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'quickRevokeSessions'])->name('incidents.revokeSessions');

            // Étape 5 — Désactivation d'un administrateur + réinitialisation forcée de sa
            // 2FA : actions destructives/sensibles, step-up auth obligatoire.
            Route::post('/admins/{user}/deactivate', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'deactivateAdmin'])->name('admins.deactivate');
            Route::post('/admins/{user}/force-2fa-reset', [\App\Http\Controllers\SuperAdmin\SystemController::class, 'forceTwoFactorReset'])->name('admins.forceTwoFactorReset');
        });
    });

// Espace dédié au rôle Technicien : routes protégées par le rôle Spatie "technicien" ou "Technicien".
Route::middleware(['auth', 'verified', 'role:technicien|Technicien'])
    ->prefix('technicien')
    ->name('technicien.')
    ->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TechnicienModule\DashboardController::class, 'index'])->name('dashboard');
    });

// Application Mobile Technicien (Mobile Web SPA / PWA)
Route::get('/mobile/{any?}', function () {
    return view('mobile.app');
})->where('any', '.*')->name('mobile.app');

require __DIR__.'/auth.php';
