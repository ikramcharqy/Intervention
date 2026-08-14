<?php

namespace App\Http\Controllers\Api;

use App\Models\Intervention;
use App\Services\InterventionService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));

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
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));

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

    public function refuse(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return $this->errorResponse('Non autorisé à refuser cette intervention.', null, 403);
        }

        $motif = $request->input('motif', '');
        if (empty(trim($motif))) {
            return $this->errorResponse('Le motif de refus est obligatoire.', null, 422);
        }

        try {
            $this->interventionService->refuseIntervention($intervention, $motif);
            return $this->successResponse($intervention->fresh(), 'Intervention refusée et signalée au planificateur.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function suspend(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return $this->errorResponse('Non autorisé à suspendre cette intervention.', null, 403);
        }

        $motif = $request->input('motif', '');

        try {
            $this->interventionService->suspendreIntervention($intervention, $motif);
            return $this->successResponse($intervention->fresh(), 'Intervention mise en pause.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function resume(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return $this->errorResponse('Non autorisé à reprendre cette intervention.', null, 403);
        }

        try {
            $this->interventionService->reprendreIntervention($intervention);
            return $this->successResponse($intervention->fresh(), 'Intervention reprise avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function submitForm(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id) {
            return $this->errorResponse('Non autorisé à soumettre le formulaire de cette intervention.', null, 403);
        }

        try {
            $this->interventionService->submitForm($intervention);
            return $this->successResponse($intervention->fresh(), 'Formulaire soumis avec succès.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function reassign(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));
        if (!$isAdmin) {
            return $this->errorResponse('Non autorisé à réaffecter cette intervention.', null, 403);
        }

        $nouveau = $request->input('technicien_id');
        if (!$nouveau) {
            return $this->errorResponse('technicien_id requis', null, 422);
        }

        try {
            $this->interventionService->reassignIntervention($intervention, (int)$nouveau, $request->input('motif', ''));
            return $this->successResponse($intervention->fresh(), 'Intervention réaffectée.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function report(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));
        // Seuls les admins appliquent directement le report ; les techniciens peuvent générer une demande de report.
        $canReschedule = $isAdmin;

        $nouvelleDate = $request->input('date');
        $motif = $request->input('motif', '');

        if (empty($nouvelleDate)) {
            return $this->errorResponse('date requise', null, 422);
        }

        try {
            // For simplicity, admins directly apply the report; technicians create a report request which is stored as history.
            if ($isAdmin) {
                $this->interventionService->reporterIntervention($intervention, $nouvelleDate, $motif);
            } else {
                // Store as historique demande de report
                $this->interventionService->enregistrerHistorique($intervention, $intervention->statut, Intervention::STATUT_REPORTEE, "Demande de report: {$nouvelleDate} - {$motif}");
            }

            return $this->successResponse($intervention->fresh(), 'Report traité (ou demande enregistrée).');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function reopen(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));
        if (!$isAdmin) {
            return $this->errorResponse('Non autorisé à rouvrir cette intervention.', null, 403);
        }

        $motif = $request->input('motif', '');

        try {
            $this->interventionService->rouvrirIntervention($intervention, $motif);
            return $this->successResponse($intervention->fresh(), 'Intervention rouverte.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function close(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));
        if (!$isAdmin) {
            return $this->errorResponse('Non autorisé à clôturer cette intervention.', null, 403);
        }

        try {
            $this->interventionService->cloturerIntervention($intervention);
            return $this->successResponse($intervention->fresh(), 'Intervention clôturée administrativement.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    public function cancel(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));
        if (!$isAdmin) {
            return $this->errorResponse('Non autorisé à annuler cette intervention.', null, 403);
        }

        $motif = $request->input('motif', '');
        try {
            $this->interventionService->cancelOrDelete($intervention, $motif);
            return $this->successResponse(null, 'Intervention annulée.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Upload de la signature client (image ou base64). Accessible au technicien assigné ou à l'admin.
     */
    public function uploadSignature(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasAnyRole(array_merge(config('roles.ADMIN'), config('roles.SUPER_ADMIN')));

        if (!$isAdmin && $intervention->technicien_id !== $user->id) {
            return $this->errorResponse('Non autorisé à upload la signature pour cette intervention.', null, 403);
        }

        // Accept either a file upload 'signature' or a base64 field 'signature_data'
        try {
            $path = null;

            if ($request->hasFile('signature')) {
                $file = $request->file('signature');
                $path = $file->storePubliclyAs('signatures/interventions/' . $intervention->id, time() . '_' . $file->getClientOriginalName(), 'public');
            } elseif ($request->input('signature_data')) {
                $data = $request->input('signature_data');
                // data like data:image/png;base64,.....
                if (preg_match('/^data:(.*);base64,(.*)$/', $data, $matches)) {
                    $mime = $matches[1];
                    $content = base64_decode($matches[2]);
                    $ext = explode('/', $mime)[1] ?? 'png';
                    $filename = 'signature_' . time() . '.' . $ext;
                    $path = 'signatures/interventions/' . $intervention->id . '/' . $filename;
                    Storage::disk('public')->put($path, $content);
                } else {
                    return $this->errorResponse('signature_data mal formée', null, 422);
                }
            } else {
                return $this->errorResponse('Aucune signature fournie', null, 422);
            }

            // Persister le chemin et la date
            $intervention->update([
                'signature_client_path' => $path,
                'signed_at' => now(),
            ]);

            $this->interventionService->enregistrerHistorique($intervention, $intervention->statut, $intervention->statut, 'Signature uploadée');

            return $this->successResponse($intervention->fresh(), 'Signature enregistrée.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 500);
        }
    }
}


