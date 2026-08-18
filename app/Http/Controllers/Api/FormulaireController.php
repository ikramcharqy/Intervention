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
        $this->authorize('view', $intervention);

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
        $this->authorize('submitForm', $intervention);

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

        if (in_array($intervention->statut, ['Terminee', 'Validee', 'Annulee'])) {
            return $this->errorResponse('Cette intervention est clôturée et ne peut plus être modifiée.', null, 400);
        }

        // ── Idempotence : éviter la double soumission sur retry réseau ──
        $idempotencyKey = $request->header('X-Idempotency-Key');
        if ($idempotencyKey) {
            $cacheKey = 'formulaire_idempotency_' . $idempotencyKey;
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                return $this->successResponse(
                    $intervention->fresh(),
                    'Formulaire déjà enregistré (idempotent).'
                );
            }
        }

        // ── Validation sécurisée des fichiers joints au formulaire ──
        $files = $request->allFiles()['fichiers'] ?? [];
        if (!empty($files)) {
            $uploader = app(\App\Services\SecureFileUploadService::class);
            foreach ($files as $file) {
                if ($file instanceof \Illuminate\Http\UploadedFile) {
                    try {
                        $uploader->validate($file, 'document', $request->user()->id);
                    } catch (\InvalidArgumentException $e) {
                        return $this->errorResponse($e->getMessage(), null, 422);
                    }
                }
            }
        }

        try {
            $data = $request->input('reponses', []);

            // 1. Sauvegarder les réponses
            $this->remplissageFormulaireService->sauvegarderReponses(
                $intervention,
                $formulaire,
                $data,
                $files
            );

            // 2. Traçabilité & Mise à jour du statut
            $statutAvant = $intervention->statut;
            $statutApres = in_array($statutAvant, ['En cours', 'Acceptee']) ? 'Formulaire rempli' : $statutAvant;

            if ($statutAvant !== $statutApres) {
                $intervention->statut = $statutApres;
                $intervention->save();
            }

            $this->interventionService->enregistrerHistorique(
                $intervention,
                statut_avant: $statutAvant,
                statut_apres: $statutApres,
                commentaire: 'Saisie / Mise à jour du formulaire terrain par le technicien'
            );

            // Stocker en cache pour idempotence (5 minutes)
            if ($idempotencyKey) {
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addMinutes(5));
            }

            return $this->successResponse(
                $intervention->fresh(),
                'Formulaire enregistré avec succès.'
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