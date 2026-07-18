<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGpsTrackingPointRequest;
use App\Models\GpsTrackingSession;
use App\Models\Intervention;
use App\Services\GpsTrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Suivi GPS continu du déplacement d'un technicien pendant une intervention.
 * Module indépendant du pointage de présence (TrackingSession).
 */
class GpsTrackingController extends Controller
{
    public function __construct(private GpsTrackingService $gpsTrackingService)
    {
    }

    /**
     * Démarre une session de suivi GPS pour l'intervention.
     */
    public function start(Request $request, Intervention $intervention): RedirectResponse
    {
        try {
            $this->gpsTrackingService->startSession($intervention, $request->user());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Session de suivi GPS démarrée.');
    }

    /**
     * Arrête une session de suivi GPS en cours.
     */
    public function stop(Intervention $intervention, GpsTrackingSession $gpsTrackingSession): RedirectResponse
    {
        $this->assertSessionBelongsToIntervention($intervention, $gpsTrackingSession);

        try {
            $this->gpsTrackingService->stopSession($gpsTrackingSession);
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Session de suivi GPS arrêtée.');
    }

    /**
     * Enregistre un point GPS pour une session en cours.
     * Utilisé par l'application mobile du technicien pendant le déplacement.
     */
    public function storePoint(StoreGpsTrackingPointRequest $request, Intervention $intervention, GpsTrackingSession $gpsTrackingSession): JsonResponse
    {
        $this->assertSessionBelongsToIntervention($intervention, $gpsTrackingSession);

        try {
            $point = $this->gpsTrackingService->addPoint($gpsTrackingSession, $request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($point, 201);
    }

    private function assertSessionBelongsToIntervention(Intervention $intervention, GpsTrackingSession $gpsTrackingSession): void
    {
        if ($gpsTrackingSession->intervention_id !== $intervention->id) {
            abort(403, 'Cette session de suivi GPS n\'appartient pas à cette intervention.');
        }
    }
}
