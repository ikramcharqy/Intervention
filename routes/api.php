<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChantierController;
use App\Http\Controllers\Api\FormulaireController;
use App\Http\Controllers\Api\InterventionController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\Api\RapportController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Technicien Mobile
|--------------------------------------------------------------------------
*/

// Public Auth routes
Route::middleware('throttle:6,1')->post('/login', [AuthController::class, 'login']);

// Fichiers du disque public (photos/signatures/documents) — non authentifié,
// même niveau d'accès que le lien symbolique public/storage qu'il remplace
// pour l'app Flutter Web (cf. Api\MediaController). Volontairement en dehors
// du groupe auth:sanctum : ces fichiers étaient déjà accessibles sans jeton
// via /storage/*.
Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*');

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // 1. Module Interventions (Workflow complet)
    Route::get('/interventions', [InterventionController::class, 'index']);
    Route::get('/interventions/{intervention}', [InterventionController::class, 'show']);
    Route::post('/interventions/{intervention}/accept', [InterventionController::class, 'accept']);
    Route::post('/interventions/{intervention}/refuse', [InterventionController::class, 'refuse']);
    Route::post('/interventions/{intervention}/start', [InterventionController::class, 'start']);
    Route::post('/interventions/{intervention}/suspend', [InterventionController::class, 'suspend']);
    Route::post('/interventions/{intervention}/resume', [InterventionController::class, 'resume']);
    Route::post('/interventions/{intervention}/client-absent', [InterventionController::class, 'marquerClientAbsent']);
    Route::post('/interventions/{intervention}/materiel-manquant', [InterventionController::class, 'marquerMaterielManquant']);
    Route::post('/interventions/{intervention}/partiellement-realisee', [InterventionController::class, 'marquerPartiellementRealisee']);
    Route::post('/interventions/{intervention}/creer-deuxieme-visite', [InterventionController::class, 'creerDeuxiemeVisite']);
    Route::post('/interventions/{intervention}/reschedule', [InterventionController::class, 'reschedule']);
    Route::post('/interventions/{intervention}/reject', [InterventionController::class, 'reject']);
    Route::post('/interventions/{intervention}/reopen', [InterventionController::class, 'reopen']);
    Route::post('/interventions/{intervention}/finish', [InterventionController::class, 'finish']);
    Route::post('/interventions/{intervention}/validate', [InterventionController::class, 'validate']);

    // Routes Tâches & Matériaux (Technicien Mobile)
    Route::post('/interventions/{intervention}/taches/{tachePivot}/progress', [InterventionController::class, 'updateTacheProgress']);
    Route::post('/interventions/{intervention}/taches/{tachePivot}/start', [InterventionController::class, 'startTache']);
    Route::post('/interventions/{intervention}/taches/{tachePivot}/finish', [InterventionController::class, 'finishTache']);
    Route::post('/interventions/{intervention}/materiaux', [InterventionController::class, 'addMateriau']);
    Route::delete('/interventions/{intervention}/materiaux/{pivot}', [InterventionController::class, 'removeMateriau']);
    Route::get('/materiaux', [InterventionController::class, 'catalogueMateriaux']);

    // Fiche chantier (Technicien) — même donnée que ClientModule\ChantierController::show(),
    // restreinte aux chantiers où le technicien connecté a au moins une intervention.
    Route::get('/chantiers/{chantier}', [ChantierController::class, 'show']);

    // 2. Module Formulaires Dynamiques — throttle 30/min (évite soumissions multiples)
    Route::get('/interventions/{intervention}/formulaire', [FormulaireController::class, 'show']);
    Route::middleware('throttle:api-upload')->post('/interventions/{intervention}/formulaire', [FormulaireController::class, 'store']);

    // 3. Module Tracking GPS
    Route::post('/interventions/{intervention}/tracking/start', [TrackingController::class, 'start']);
    Route::post('/tracking/sessions/{session}/points', [TrackingController::class, 'addPoint']);
    Route::post('/tracking/sessions/{session}/stop', [TrackingController::class, 'stop']);
    Route::get('/interventions/{intervention}/tracking', [TrackingController::class, 'show']);

    // 4. Module Rapports d'intervention
    Route::get('/rapports', [RapportController::class, 'index']);
    Route::get('/rapports/{rapport}', [RapportController::class, 'show']);
    Route::get('/rapports/{rapport}/pdf', [RapportController::class, 'downloadPdf']);
    Route::delete('/rapports/{rapport}', [RapportController::class, 'destroy']);
    Route::get('/interventions/{intervention}/rapport', [RapportController::class, 'showByIntervention']);
    Route::get('/interventions/{intervention}/rapport/completude', [RapportController::class, 'checkCompletude']);
    Route::middleware('throttle:api-upload')->post('/interventions/{intervention}/rapport', [RapportController::class, 'storeOrUpdate']);

    // 5. Module Notifications (Base de données)
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // 6. Module Profil Technicien
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('/photo', [ProfileController::class, 'updatePhoto']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
        Route::put('/presence', [ProfileController::class, 'updatePresence']);
        Route::put('/notification-preferences', [ProfileController::class, 'updateNotificationPreferences']);
        Route::get('/login-history', [ProfileController::class, 'loginHistory']);
        Route::get('/sessions', [ProfileController::class, 'sessions']);
        Route::delete('/sessions/{tokenId}', [ProfileController::class, 'revokeSession']);
    });

    // 7. Module Push Notifications PWA (Web Push Subscriptions)
    Route::prefix('push')->group(function () {
        Route::get('/status', [PushSubscriptionController::class, 'status']);
        Route::post('/subscribe', [PushSubscriptionController::class, 'store']);
        Route::post('/unsubscribe', [PushSubscriptionController::class, 'destroy']);
    });
});
