<?php

namespace App\Http\Controllers\Api;

use App\Models\Intervention;
use App\Models\Rapport;
use App\Services\RapportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RapportController extends BaseApiController
{
    public function __construct(protected RapportService $rapportService)
    {
    }

    /**
     * Liste de tous les rapports d'intervention.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $search = $request->input('search');

        $rapports = Rapport::with([
            'intervention.chantier.client',
            'intervention.technicien',
            'intervention.typeIntervention',
        ])
        ->when($user->hasRole('Client'), function ($q) {
            $q->whereHas('intervention', fn($qi) => $qi->where('statut', 'Terminee'));
        })
        ->when($user->hasRole('Technicien') || $user->hasRole('technicien'), function ($q) use ($user) {
            $q->whereHas('intervention', fn($qi) => $qi->where('technicien_id', $user->id));
        })
        ->when($search, function ($query, $search) {
            $query->where('commentaire', 'like', "%{$search}%")
                ->orWhere('travaux_effectues', 'like', "%{$search}%")
                ->orWhereHas('intervention', function ($q) use ($search) {
                    $q->where('code_intervention', 'like', "%{$search}%")
                        ->orWhereHas('chantier', fn($qc) => $qc->where('nom', 'like', "%{$search}%"));
                });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return $this->successResponse($rapports, 'Liste des rapports récupérée.');
    }

    /**
     * Récupérer le rapport d'une intervention spécifique.
     */
    public function showByIntervention(Request $request, Intervention $intervention): JsonResponse
    {
        $user = $request->user();

        if (
            $intervention->technicien_id !== $user->id
            && !$user->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin'])
        ) {
            return $this->errorResponse('Non autorisé à consulter ce rapport.', null, 403);
        }

        $rapport = $intervention->rapport;

        if (!$rapport) {
            return $this->errorResponse('Aucun rapport enregistré pour cette intervention.', null, 404);
        }

        if (!$this->rapportService->canClientView($rapport, $user)) {
            return $this->errorResponse('Ce rapport est en cours de validation et n\'est pas encore disponible.', null, 403);
        }

        $rapport->load([
            'intervention.technicien',
            'intervention.chantier.client',
            'intervention.typeIntervention',
            'intervention.emplacement',
            'intervention.materiaux.materiau',
            'photos',
            'videos',
            'documents',
            'reponses.question',
        ]);

        return $this->successResponse($rapport, 'Rapport d\'intervention récupéré.');
    }

    /**
     * Récupérer les détails d'un rapport par son ID.
     */
    public function show(Request $request, Rapport $rapport): JsonResponse
    {
        $user = $request->user();

        if (!$this->rapportService->canClientView($rapport, $user)) {
            return $this->errorResponse('Ce rapport est en cours de validation.', null, 403);
        }

        $rapport->load([
            'intervention.technicien',
            'intervention.chantier.client',
            'intervention.typeIntervention',
            'intervention.emplacement',
            'intervention.materiaux.materiau',
            'photos',
            'videos',
            'documents',
            'reponses.question',
        ]);

        return $this->successResponse($rapport, 'Détails du rapport récupérés.');
    }

    /**
     * Créer ou mettre à jour le rapport d'intervention.
     */
    public function storeOrUpdate(Request $request, Intervention $intervention): JsonResponse
    {
        if ($intervention->technicien_id !== $request->user()->id && !$request->user()->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin'])) {
            return $this->errorResponse('Non autorisé à gérer ce rapport.', null, 403);
        }

        $validator = Validator::make($request->all(), [
            'travaux_effectues' => 'nullable|string',
            'observations'      => 'nullable|string',
            'recommandations'   => 'nullable|string',
            'statut_equipement' => 'nullable|string',
            'qrcode_scanne'     => 'nullable|string',
            'commentaire'       => 'nullable|string',
            'date_debut'        => 'nullable|date',
            'date_fin'          => 'nullable|date',
            'photos.*'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'videos.*'          => 'nullable|file|mimes:mp4,mov,avi,webm|max:51200',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Données du rapport invalides.', $validator->errors(), 422);
        }

        try {
            $data = $validator->validated();
            unset($data['photos'], $data['videos']);

            $data['intervention_id'] = $intervention->id;
            if (empty($data['date_debut'])) {
                $data['date_debut'] = $intervention->date_reelle_debut ?? now();
            }
            if (empty($data['date_fin'])) {
                $data['date_fin'] = now();
            }

            $rapport = $intervention->rapport;

            if ($rapport) {
                $rapport = $this->rapportService->updateRapport($rapport, $data);
                $message = 'Rapport d\'intervention mis à jour avec succès.';
            } else {
                $rapport = $this->rapportService->createRapport($data);
                $message = 'Rapport d\'intervention créé avec succès.';
            }

            // Gestion de l'upload des photos du rapport
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $file) {
                    $path = $file->store('photos/rapports', 'public');
                    $rapport->photos()->create(['chemin' => $path]);
                }
            }

            // Gestion de l'upload des vidéos du rapport
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $file) {
                    $path = $file->store('videos/rapports', 'public');
                    $rapport->videos()->create(['chemin' => $path]);
                }
            }

            return $this->successResponse($rapport->fresh([
                'intervention',
                'photos',
                'videos',
                'documents',
            ]), $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Télécharger le rapport en PDF.
     */
    public function downloadPdf(Request $request, Rapport $rapport)
    {
        $user = $request->user();

        if (!$this->rapportService->canClientView($rapport, $user)) {
            return $this->errorResponse('Ce rapport est en cours de validation par l\'administration.', null, 403);
        }

        $rapport->load([
            'intervention.technicien',
            'intervention.chantier.client',
            'intervention.typeIntervention',
            'intervention.emplacement',
            'intervention.trackingSessions',
            'intervention.materiaux.materiau',
            'photos',
            'videos',
            'documents',
            'reponses.question.choix',
        ]);

        $pdf = Pdf::loadView('rapports.pdf', compact('rapport'));

        return $pdf->download('rapport_' . ($rapport->intervention->code_intervention ?? $rapport->id) . '.pdf');
    }

    /**
     * Supprimer un rapport d'intervention.
     */
    public function destroy(Request $request, Rapport $rapport): JsonResponse
    {
        if (!$request->user()->hasAnyRole(['Admin', 'admin', 'Super Admin', 'superadmin'])) {
            return $this->errorResponse('Seul un administrateur peut supprimer un rapport.', null, 403);
        }

        try {
            $this->rapportService->deleteRapport($rapport);
            return $this->successResponse(null, 'Rapport supprimé avec succès. L\'intervention est repassée en cours.');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }
}
