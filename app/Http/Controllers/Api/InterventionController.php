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
        $this->authorize('view', $intervention);

        $intervention->load([
            'chantier',
            'emplacement',
            'typeIntervention',
            'rapport',
            'materiaux.materiau',
            'taches.tache',
            'historiques.user',
        ]);

        return $this->successResponse($intervention, 'Détails de l\'intervention récupérés.');
    }

    /**
     * Accepter une intervention planifiée.
     */
    public function accept(Request $request, Intervention $intervention): JsonResponse
    {
        $this->authorize('accept', $intervention);

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
        $this->authorize('start', $intervention);

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
     * Refuser une intervention et émettre une demande de réaffectation avec motif obligatoire.
     */
    public function refuse(Request $request, Intervention $intervention): JsonResponse
    {
        $this->authorize('refuse', $intervention);

        $request->validate([
            'motif' => 'required|string|min:5',
        ], [
            'motif.required' => 'Le motif de refus est obligatoire pour transmettre une demande de réaffectation.',
            'motif.min'      => 'Le motif de refus doit contenir au moins 5 caractères.',
        ]);

        try {
            $demande = $this->interventionService->refuserEtDemanderReaffectation(
                $intervention,
                $request->input('motif'),
                $request->user()
            );

            return $this->successResponse([
                'intervention' => $intervention->fresh(),
                'demande'      => $demande,
            ], 'Demande de réaffectation transmise avec succès à l\'administrateur.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Reporter une intervention.
     */
    public function reschedule(Request $request, Intervention $intervention): JsonResponse
    {
        $this->authorize('reschedule', $intervention);

        $request->validate([
            'motif'               => 'required|string|min:5',
            'date_prevue_debut'  => 'nullable|date',
            'date_prevue_fin'    => 'nullable|date|after_or_equal:date_prevue_debut',
        ]);

        try {
            $this->interventionService->reporter(
                $intervention,
                $request->input('motif'),
                $request->input('date_prevue_debut'),
                $request->input('date_prevue_fin')
            );

            return $this->successResponse($intervention->fresh(), 'Intervention reportée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Suspendre une intervention en cours.
     */
    public function suspend(Request $request, Intervention $intervention): JsonResponse
    {
        $motif = $request->input('motif', $request->input('motif_suspension', ''));

        try {
            $this->interventionService->suspendreIntervention($intervention, $motif);
            return $this->successResponse($intervention->fresh(), 'Intervention suspendue avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Reprendre une intervention suspendue.
     */
    public function resume(Request $request, Intervention $intervention): JsonResponse
    {
        try {
            $this->interventionService->reprendreIntervention($intervention);
            return $this->successResponse($intervention->fresh(), 'Intervention reprise avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Déclarer client absent.
     */
    public function marquerClientAbsent(Request $request, Intervention $intervention): JsonResponse
    {
        $request->validate([
            'motif'         => 'required|string|max:1000',
            'date_revisite' => 'nullable|date|after:now',
            'technicien_id' => 'nullable|exists:users,id',
        ]);

        try {
            $res = $this->interventionService->marquerClientAbsent(
                $intervention,
                $request->input('motif'),
                $request->input('date_revisite'),
                $request->input('technicien_id') ? (int) $request->input('technicien_id') : null
            );

            return $this->successResponse($res->fresh(), 'Statut "Client absent" enregistré avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Déclarer matériel manquant.
     */
    public function marquerMaterielManquant(Request $request, Intervention $intervention): JsonResponse
    {
        $request->validate([
            'motif'         => 'required|string|max:1000',
            'date_revisite' => 'nullable|date|after:now',
            'technicien_id' => 'nullable|exists:users,id',
        ]);

        try {
            $res = $this->interventionService->marquerMaterielManquant(
                $intervention,
                $request->input('motif'),
                $request->input('date_revisite'),
                $request->input('technicien_id') ? (int) $request->input('technicien_id') : null
            );

            return $this->successResponse($res->fresh(), 'Statut "Matériel manquant" enregistré avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Déclarer partiellement réalisée.
     */
    public function marquerPartiellementRealisee(Request $request, Intervention $intervention): JsonResponse
    {
        $request->validate([
            'observations'  => 'required|string|max:1000',
            'date_revisite' => 'nullable|date|after:now',
            'technicien_id' => 'nullable|exists:users,id',
        ]);

        try {
            $res = $this->interventionService->marquerPartiellementRealisee(
                $intervention,
                $request->input('observations'),
                $request->input('date_revisite'),
                $request->input('technicien_id') ? (int) $request->input('technicien_id') : null
            );

            return $this->successResponse($res->fresh(), 'Statut "Partiellement réalisée" enregistré avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Générer une 2ème visite liée.
     */
    public function creerDeuxiemeVisite(Request $request, Intervention $intervention): JsonResponse
    {
        $request->validate([
            'date_prevue_debut' => 'required|date',
            'technicien_id'     => 'nullable|exists:users,id',
            'motif'             => 'nullable|string|max:500',
        ]);

        try {
            $revisite = $this->interventionService->creerDeuxiemeVisite($intervention, [
                'date_prevue_debut' => $request->input('date_prevue_debut'),
                'technicien_id'     => $request->input('technicien_id') ? (int) $request->input('technicien_id') : null,
                'motif'             => $request->input('motif', 'Deuxième visite programmée'),
            ]);

            return $this->successResponse($revisite, 'Deuxième visite créée avec succès et liée à l\'intervention initiale.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Rejeter le rapport/formulaire soumis (Admin / Manager).
     */
    public function reject(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin', 'Planificateur']);

        if (!$isAdmin) {
            return $this->errorResponse('Seul un administrateur peut rejeter une soumission de rapport.', null, 403);
        }

        $request->validate([
            'motif' => 'required|string|min:5',
        ]);

        try {
            $this->interventionService->rejeterValidation($intervention, $request->input('motif'));
            return $this->successResponse($intervention->fresh(), 'Soumission rejetée. Intervention renvoyée au technicien sur le terrain.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Réouvrir une intervention validée / clôturée (Admin).
     */
    public function reopen(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin', 'Planificateur']);

        if (!$isAdmin) {
            return $this->errorResponse('Seul un administrateur peut rouvrir une intervention clôturée.', null, 403);
        }

        $request->validate([
            'motif' => 'nullable|string',
        ]);

        try {
            $this->interventionService->rouvrir($intervention, $request->input('motif', ''));
            return $this->successResponse($intervention->fresh(), 'Intervention rouverte avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Terminer définitivement l'intervention (Technicien).
     */
    public function finish(Request $request, Intervention $intervention): JsonResponse
    {
        $this->authorize('submitForm', $intervention);

        try {
            $res = $this->interventionService->finishIntervention($intervention, $request->input('commentaire'));
            return $this->successResponse($res->fresh(), 'Intervention marquée comme terminée et transmise à l\'administration avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Valider et clôturer une intervention.
     */
    public function validate(Request $request, Intervention $intervention): JsonResponse
    {
        $this->authorize('validate', $intervention);

        try {
            $this->interventionService->validateIntervention($intervention);
            return $this->successResponse($intervention->fresh(), 'Intervention validée et clôturée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Mettre à jour l'avancement/progression d'une tâche d'intervention (API).
     */
    public function updateTacheProgress(Request $request, Intervention $intervention, int $tachePivotId): JsonResponse
    {
        $request->validate([
            'pourcentage' => 'required|integer|min:0|max:100',
            'statut'      => 'nullable|string',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        try {
            $tachePivot = $this->interventionService->updateTacheProgress(
                $intervention,
                $tachePivotId,
                (int) $request->input('pourcentage'),
                $request->input('statut'),
                $request->input('commentaire')
            );

            $interventionFresh = $intervention->fresh();

            return $this->successResponse([
                'tache_pivot' => $tachePivot,
                'pourcentage_global' => $interventionFresh->pourcentage_global,
            ], 'Progression enregistrée. Pourcentage global recalculé.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Démarre une tâche d'intervention (API).
     */
    public function startTache(Request $request, Intervention $intervention, int $tachePivotId): JsonResponse
    {
        try {
            $tachePivot = $this->interventionService->demarrerTache(
                $intervention,
                $tachePivotId,
                $request->input('commentaire')
            );

            return $this->successResponse([
                'tache_pivot' => $tachePivot,
                'pourcentage_global' => $intervention->fresh()->pourcentage_global,
            ], 'Tâche démarrée avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Termine une tâche d'intervention (API).
     */
    public function finishTache(Request $request, Intervention $intervention, int $tachePivotId): JsonResponse
    {
        try {
            $tachePivot = $this->interventionService->terminerTache(
                $intervention,
                $tachePivotId,
                $request->input('commentaire')
            );

            return $this->successResponse([
                'tache_pivot' => $tachePivot,
                'pourcentage_global' => $intervention->fresh()->pourcentage_global,
            ], 'Tâche marquée comme terminée.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Enregistrer un matériau utilisé depuis l'application mobile (API).
     */
    public function addMateriau(Request $request, Intervention $intervention): JsonResponse
    {
        $request->validate([
            'materiau_id' => 'required|exists:materiaus,id',
            'quantite'    => 'required|numeric|min:0.01',
            'commentaire' => 'nullable|string|max:500',
        ]);

        try {
            $materiau = \App\Models\Materiau::find($request->input('materiau_id'));

            $materiauUsed = \App\Models\InterventionMateriau::updateOrCreate(
                [
                    'intervention_id' => $intervention->id,
                    'materiau_id'     => $request->input('materiau_id'),
                ],
                [
                    'quantite'    => $request->input('quantite'),
                    'unite'       => $materiau ? $materiau->unite : null,
                    'commentaire' => $request->input('commentaire'),
                ]
            );

            return $this->successResponse($materiauUsed->load('materiau'), 'Matériau enregistré avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Supprimer un matériau d'une intervention (application mobile).
     */
    public function removeMateriau(Request $request, Intervention $intervention, int $pivot): JsonResponse
    {
        // Si c'est son intervention, il peut ajouter/supprimer du matériel.
        $this->authorize('view', $intervention);

        try {
            \App\Models\InterventionMateriau::where('id', $pivot)
                ->where('intervention_id', $intervention->id)
                ->delete();

            return $this->successResponse(null, 'Matériau supprimé de l\'intervention.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Lister le catalogue complet des matériaux disponibles.
     */
    public function catalogueMateriaux(Request $request): JsonResponse
    {
        $materiaux = \App\Models\Materiau::query()
            ->when($request->input('q'), fn($query, $q) =>
                $query->where('nom', 'like', "%{$q}%")
                    ->orWhere('reference', 'like', "%{$q}%")
            )
            ->orderBy('nom')
            ->get(['id', 'nom', 'reference', 'unite', 'description']);

        return $this->successResponse($materiaux, 'Catalogue matériaux.');
    }
}
