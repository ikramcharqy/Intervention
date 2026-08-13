<?php

namespace App\Http\Controllers\Api;

use App\Models\Intervention;
use App\Services\InterventionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterventionController extends BaseApiController
{
    protected InterventionService $interventionService;

    public function __construct(InterventionService $interventionService)
    {
        $this->interventionService = $interventionService;
    }

    /**
     * Liste des interventions assignées au technicien connecté.
     */
    public function index(Request $request): JsonResponse
    {
        $technicienId = $request->user()->id;

        $interventions = Intervention::with(['chantier', 'emplacement', 'typeIntervention'])
            ->where('technicien_id', $technicienId)
            ->orderBy('date_prevue_debut', 'asc')
            ->get();

        return $this->successResponse($interventions, 'Liste des interventions récupérée.');
    }

    /**
     * Détails d'une intervention spécifique.
     */
    public function show(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin']);

        if ($intervention->technicien_id !== $user->id && !$isAdmin) {
            return $this->errorResponse('Non autorisé à consulter cette intervention.', null, 403);
        }

        $intervention->load([
            'chantier',
            'emplacement',
            'typeIntervention',
            'rapport',
            'materiaux',
            'taches',
            'historiques',
        ]);

        return $this->successResponse($intervention, 'Détails de l\'intervention récupérés.');
    }

    /**
     * Accepter une intervention planifiée.
     */
    public function accept(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return $this->errorResponse('Non autorisé à accepter cette intervention.', null, 403);
        }

        try {
            $this->interventionService->accept($intervention);
            return $this->successResponse($intervention->fresh(), 'Intervention acceptée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Démarrer une intervention.
     */
    public function start(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return $this->errorResponse('Non autorisé à démarrer cette intervention.', null, 403);
        }

        $mode = $request->input('mode', Intervention::MODE_MANUEL);
        $params = $request->only(['latitude', 'longitude', 'qr_code', 'nfc_tag']);

        try {
            $this->interventionService->startIntervention($intervention, $mode, $params);
            return $this->successResponse($intervention->fresh(), 'Intervention démarrée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Valider et clôturer une intervention.
     */
    public function validate(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin']);

        if ($intervention->technicien_id !== $user->id && !$isAdmin) {
            return $this->errorResponse('Non autorisé à valider cette intervention.', null, 403);
        }

        try {
            $this->interventionService->validateIntervention($intervention);
            return $this->successResponse($intervention->fresh(), 'Intervention validée et clôturée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }
}
