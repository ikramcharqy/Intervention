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
    if (auth()->user()->hasRole('Commercial')) {
        return redirect()->route('commercial.dashboard');
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

    return view('dashboard', compact('stats', 'recentInterventions'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes CRM Commercial
    Route::post('prospects/{prospect}/convert', [\App\Http\Controllers\ProspectController::class, 'convert'])->name('prospects.convert');
    Route::resource('prospects', \App\Http\Controllers\ProspectController::class);
    
    Route::resource('demande-interventions', \App\Http\Controllers\DemandeInterventionController::class);
    
    // Contacts liés au client
    Route::resource('clients.contacts', \App\Http\Controllers\ClientContactController::class)->shallow();

    // Routes Clients
    Route::post('clients/{client}/restore', [ClientController::class, 'restore'])->name('clients.restore');
    Route::delete('clients/{client}/force-delete', [ClientController::class, 'forceDelete'])->name('clients.forceDelete');
    Route::resource('clients', ClientController::class);

    // Routes Chantiers
    Route::post('chantiers/{chantier}/restore', [ChantierController::class, 'restore'])->name('chantiers.restore');
    Route::resource('chantiers', ChantierController::class);

    // Routes Emplacements
    Route::post('chantiers/{chantier}/emplacements', [EmplacementController::class, 'store'])->name('emplacements.store');
    Route::put('emplacements/{emplacement}', [EmplacementController::class, 'update'])->name('emplacements.update');
    Route::delete('emplacements/{emplacement}', [EmplacementController::class, 'destroy'])->name('emplacements.destroy');
    Route::post('emplacements/{emplacement}/restore', [EmplacementController::class, 'restore'])->name('emplacements.restore');

    // Endpoints JSON pour chargement dynamique
    Route::get('clients/{client}/chantiers', [ClientController::class, 'getChantiers'])->name('clients.chantiers');
    Route::get('chantiers/{chantier}/emplacements', [ChantierController::class, 'getEmplacements'])->name('chantiers.emplacements');

    // Routes Types d'Intervention
    Route::post('types-intervention/{type_intervention}/toggle-active', [TypeInterventionController::class, 'toggleActive'])->name('types-intervention.toggleActive');
    Route::resource('types-intervention', TypeInterventionController::class);

    // Routes Interventions — Transitions de statut
    Route::post('interventions/{intervention}/accept', [InterventionController::class, 'accept'])->name('interventions.accept');
    Route::post('interventions/{intervention}/start', [InterventionController::class, 'start'])->name('interventions.start');
    Route::post('interventions/{intervention}/suspend', [InterventionController::class, 'suspend'])->name('interventions.suspend');
    Route::post('interventions/{intervention}/resume', [InterventionController::class, 'resume'])->name('interventions.resume');
    Route::post('interventions/{intervention}/validate', [InterventionController::class, 'validateIntervention'])->name('interventions.validate');
    Route::post('interventions/{intervention}/reject-validation', [InterventionController::class, 'rejectValidation'])->name('interventions.rejectValidation');
    
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

    // Routes Tâches
    Route::resource('taches', TacheController::class);

    // Routes Rapports
    Route::get('rapports/{rapport}/pdf', [RapportController::class, 'generatePdf'])->name('rapports.pdf');
    Route::resource('rapports', RapportController::class);

    // Routes Matériaux
    Route::resource('materiaux', MateriauController::class);

    // Association matériaux ↔ intervention
    Route::post('interventions/{intervention}/materiaux', [\App\Http\Controllers\InterventionMateriauController::class, 'store'])->name('interventions.materiaux.store');
    Route::delete('interventions/{intervention}/materiaux/{interventionMateriau}', [\App\Http\Controllers\InterventionMateriauController::class, 'destroy'])->name('interventions.materiaux.destroy');

    // Routes Utilisateurs
    Route::post('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);

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

        $viewName = auth()->user()->hasRole('Commercial') && view()->exists('commercial.statistiques')
            ? 'commercial.statistiques'
            : 'statistiques';

        return view($viewName, compact(
            'totalInterventions', 'terminees', 'enCours', 'planifiees', 'annulees',
            'tauxCompletion', 'dureeReelleMoyenne', 'coutMoyenMateriaux',
            'parStatut', 'topTechniciens', 'parPriorite', 'parMois'
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

        Route::get('/devis', [\App\Http\Controllers\Commercial\DevisController::class, 'index'])
            ->name('devis.index');

        Route::get('/documents', [\App\Http\Controllers\Commercial\DocumentController::class, 'index'])
            ->name('documents.index');
    });

require __DIR__.'/auth.php';
