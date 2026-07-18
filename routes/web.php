<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
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
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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
});

require __DIR__.'/auth.php';
