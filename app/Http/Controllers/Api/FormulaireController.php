<?php

namespace App\Http\Controllers\Api;

use App\Models\Formulaire;
use App\Models\Intervention;
use App\Services\InterventionService;
use App\Services\RemplissageFormulaireService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormulaireController extends BaseApiController
{
    protected RemplissageFormulaireService $remplissageFormulaireService;
    protected InterventionService $interventionService;

    public function __construct(
        RemplissageFormulaireService $remplissageFormulaireService,
        InterventionService $interventionService
    ) {
        $this->remplissageFormulaireService = $remplissageFormulaireService;
        $this->interventionService = $interventionService;
    }

    /**
     * Récupérer le formulaire dynamique associé au type d'intervention.
     */
    public function show(Request $request, Intervention $intervention): JsonResponse
    {
        if (
            $intervention->technicien_id !== $request->user()->id
            && !$request->user()->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin'])
        ) {
            return $this->errorResponse(
                'Non autorisé à accéder au formulaire de cette intervention.',
                null,
                403
            );
        }

        $formulaire = Formulaire::with(['questions.choix'])
            ->where('type_intervention_id', $intervention->type_intervention_id)
            ->where('is_active', true)
            ->first();

        if (!$formulaire) {
            return $this->errorResponse(
                'Aucun formulaire dynamique associé à cette intervention.',
                null,
                404
            );
        }

        return $this->successResponse([
            'intervention_id' => $intervention->id,
            'formulaire' => $formulaire,
        ], 'Formulaire dynamique récupéré.');
    }

    /**
     * Envoyer les réponses au formulaire dynamique (réservé aux techniciens).
     */
    public function store(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();

        // Seuls les techniciens (rôle technicien / Technicien) sont autorisés à remplir et soumettre les formulaires
        if (!$user->hasAnyRole(['technicien', 'Technicien'])) {
            return $this->errorResponse(
                'Seuls les techniciens sont autorisés à soumettre ce formulaire.',
                null,
                403
            );
        }

        $formulaire = Formulaire::with('questions')
            ->where('type_intervention_id', $intervention->type_intervention_id)
            ->where('is_active', true)
            ->first();

        if (!$formulaire) {
            return $this->errorResponse(
                'Aucun formulaire associé à cette intervention.',
                null,
                400
            );
        }

        try {
            $data = $request->input('reponses', []);
            $files = $request->allFiles()['fichiers'] ?? [];

            // 1. Sauvegarder les réponses
            $this->remplissageFormulaireService->sauvegarderReponses(
                $intervention,
                $formulaire,
                $data,
                $files
            );

            // 2. Soumettre le formulaire si l'intervention est en cours d'exécution
            if ($intervention->peutEtreSoumise()) {
                $this->interventionService->submitForm($intervention);
            }

            return $this->successResponse(
                $intervention->fresh(),
                'Formulaire enregistré et soumis à validation avec succès.'
            );

        } catch (Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                null,
                400
            );
        }
    }
}