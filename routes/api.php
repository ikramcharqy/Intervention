<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FormulaireController;
use App\Http\Controllers\Api\InterventionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RapportController;
use App\Http\Controllers\Api\TrackingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Technicien Mobile
|--------------------------------------------------------------------------
*/

// Public Auth routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Auth endpoints
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // 1. Module Interventions
    Route::get('/interventions', [InterventionController::class, 'index']);
    Route::get('/interventions/{intervention}', [InterventionController::class, 'show']);

    // Actions technicien
    Route::post('/interventions/{intervention}/accept', [InterventionController::class, 'accept'])->name('api.interventions.accept');
    Route::post('/interventions/{intervention}/refuse', [InterventionController::class, 'refuse'])->name('api.interventions.refuse');
    Route::post('/interventions/{intervention}/start', [InterventionController::class, 'start'])->name('api.interventions.start');
    Route::post('/interventions/{intervention}/suspend', [InterventionController::class, 'suspend'])->name('api.interventions.suspend');
    Route::post('/interventions/{intervention}/resume', [InterventionController::class, 'resume'])->name('api.interventions.resume');
    Route::post('/interventions/{intervention}/submit-form', [InterventionController::class, 'submitForm'])->name('api.interventions.submitForm');

    // Actions admin / planificateur
    Route::post('/interventions/{intervention}/reassign', [InterventionController::class, 'reassign'])->name('api.interventions.reassign');
    Route::post('/interventions/{intervention}/report', [InterventionController::class, 'report'])->name('api.interventions.report');
    Route::post('/interventions/{intervention}/signature', [InterventionController::class, 'uploadSignature'])->name('api.interventions.signature');
    Route::post('/interventions/{intervention}/reopen', [InterventionController::class, 'reopen'])->name('api.interventions.reopen');
    Route::post('/interventions/{intervention}/close', [InterventionController::class, 'close'])->name('api.interventions.close');
    Route::post('/interventions/{intervention}/cancel', [InterventionController::class, 'cancel'])->name('api.interventions.cancel');
    Route::post('/interventions/{intervention}/validate', [InterventionController::class, 'validate'])->name('api.interventions.validate');

    // 2. Module Formulaires Dynamiques
    Route::get('/interventions/{intervention}/formulaire', [FormulaireController::class, 'show']);
    Route::post('/interventions/{intervention}/formulaire', [FormulaireController::class, 'store']);

    // 3. Module Tracking GPS
    Route::post('/interventions/{intervention}/tracking/start', [TrackingController::class, 'start']);
    Route::post('/tracking/sessions/{session}/points', [TrackingController::class, 'addPoint']);
    Route::post('/tracking/sessions/{session}/stop', [TrackingController::class, 'stop']);

    // 4. Module Rapports d'intervention
    Route::get('/rapports', [RapportController::class, 'index']);
    Route::get('/rapports/{rapport}', [RapportController::class, 'show']);
    Route::get('/rapports/{rapport}/pdf', [RapportController::class, 'downloadPdf']);
    Route::delete('/rapports/{rapport}', [RapportController::class, 'destroy']);
    Route::get('/interventions/{intervention}/rapport', [RapportController::class, 'showByIntervention']);
    Route::post('/interventions/{intervention}/rapport', [RapportController::class, 'storeOrUpdate']);

    // 5. Module Notifications
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
    });

    Route::get(
    '/interventions/{intervention}/tracking',
    [TrackingController::class, 'show']
    );
});
