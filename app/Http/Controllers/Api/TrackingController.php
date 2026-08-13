<?php

namespace App\Http\Controllers\Api;

use App\Models\GpsTrackingSession;
use App\Models\Intervention;
use App\Services\GpsTrackingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrackingController extends BaseApiController
{
    public function __construct(protected GpsTrackingService $gpsTrackingService)
    {
    }

    /**
     * Démarrer une session de suivi GPS pour une intervention.
     */
    public function start(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin']);

        if ($intervention->technicien_id !== $user->id && !$isAdmin) {
            return $this->errorResponse('Non autorisé à démarrer le tracking GPS sur cette intervention.', null, 403);
        }

        try {
            $session = $this->gpsTrackingService->startSession($intervention, $user);
            return $this->successResponse($session, 'Session de suivi GPS démarrée.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Transmettre un point ou lot de positions GPS.
     */
    public function addPoint(Request $request, GpsTrackingSession $session): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin']);

        if ($session->technicien_id !== $user->id && !$isAdmin) {
            return $this->errorResponse('Non autorisé.', null, 403);
        }

        $validator = Validator::make($request->all(), [
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
            'captured_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Données GPS invalides.', $validator->errors(), 422);
        }

        try {
            $point = $this->gpsTrackingService->addPoint($session, $validator->validated());
            return $this->successResponse($point, 'Position GPS enregistrée.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Arrêter la session de suivi GPS.
     */
    public function stop(Request $request, GpsTrackingSession $session): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin']);

        if ($session->technicien_id !== $user->id && !$isAdmin) {
            return $this->errorResponse('Non autorisé.', null, 403);
        }

        try {
            $sessionCloturee = $this->gpsTrackingService->stopSession($session);
            return $this->successResponse($sessionCloturee, 'Session de suivi GPS arrêtée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Consulter l'historique du suivi GPS.
     */
    public function show(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin']);

        if ($intervention->technicien_id !== $user->id && !$isAdmin) {
            return $this->errorResponse('Non autorisé.', null, 403);
        }

        $sessions = GpsTrackingSession::with('points')
            ->where('intervention_id', $intervention->id)
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where('technicien_id', $user->id);
            })
            ->latest()
            ->get();

        return $this->successResponse(
            $sessions,
            'Historique du tracking GPS récupéré.'
        );
    }
}
