<?php

namespace App\Http\Controllers\Api;

use App\Models\Intervention;
use App\Models\Rapport;
use App\Services\RapportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRapportRequest;
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
        $this->authorize('view', $intervention);

        $user = $request->user();

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
            'reponses.question.choix',
            'reponses.choixQuestion',
        ]);

        return $this->successResponse($rapport, 'Rapport d\'intervention récupéré.');
    }

    /**
     * Récupérer les détails d'un rapport par son ID.
     */
    public function show(Request $request, Rapport $rapport): JsonResponse
    {
        $this->authorize('view', $rapport);

        if (!$this->rapportService->canClientView($rapport, $request->user())) {
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
            'reponses.question.choix',
            'reponses.choixQuestion',
        ]);

        return $this->successResponse($rapport, 'Détails du rapport récupérés.');
    }

    /**
     * Créer ou mettre à jour le rapport d'intervention.
     */
    public function storeOrUpdate(StoreRapportRequest $request, Intervention $intervention): JsonResponse
    {
        // Authorization handled by FormRequest

        if (in_array($intervention->statut, ['Terminee', 'Validee', 'Annulee'])) {
            return $this->errorResponse('Cette intervention est clôturée et ne peut plus être modifiée.', null, 400);
        }

        // ── Idempotence : éviter les doublons sur retry réseau ──
        $idempotencyKey = $request->header('X-Idempotency-Key');
        if ($idempotencyKey) {
            $cacheKey = 'rapport_idempotency_' . $idempotencyKey;
            $cached   = \Illuminate\Support\Facades\Cache::get($cacheKey);
            if ($cached) {
                return $this->successResponse($cached, 'Rapport déjà enregistré (idempotent).');
            }
        }

        /** @var \App\Services\SecureFileUploadService $uploader */
        $uploader = app(\App\Services\SecureFileUploadService::class);
        $userId   = $request->user()->id;

        // ── Validation sécurisée des fichiers (MIME réel + extension + taille) ──
        try {
            if ($request->hasFile('photos')) {
                $uploader->validateBatch($request->file('photos'), 'image', $userId);
            }
            if ($request->hasFile('videos')) {
                $uploader->validateBatch($request->file('videos'), 'video', $userId);
            }
            if ($request->hasFile('documents')) {
                $uploader->validateBatch($request->file('documents'), 'document', $userId);
            }
            if ($request->hasFile('signature_technicien_file')) {
                $uploader->validate($request->file('signature_technicien_file'), 'signature', $userId);
            }
            if ($request->hasFile('signature_client_file')) {
                $uploader->validate($request->file('signature_client_file'), 'signature', $userId);
            }
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), null, 422);
        }

        try {
            $data = $request->validated();
            unset($data['photos'], $data['photos_types'], $data['videos'], $data['documents'],
                  $data['signature_technicien_file'], $data['signature_client_file']);

            // Traitement des signatures Base64 (canvas numérique)
            foreach (['signature_technicien', 'signature_client'] as $sigKey) {
                if (!empty($data[$sigKey]) && str_starts_with($data[$sigKey], 'data:image')) {
                    $imageParts   = explode(';base64,', $data[$sigKey]);
                    $imageTypeAux = explode('image/', $imageParts[0]);
                    $imageType    = $imageTypeAux[1] ?? 'png';
                    $imageBase64  = base64_decode($imageParts[1]);
                    $fileName     = 'signatures/' . $sigKey . '_' . $intervention->id . '_' . time() . '.' . $imageType;
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageBase64);
                    $data[$sigKey] = $fileName;
                }
            }

            // Gestion de la photo papier de signature si uploadée
            if ($request->hasFile('signature_technicien_file')) {
                $path = $request->file('signature_technicien_file')->store('signatures', 'public');
                $data['signature_technicien'] = $path;
            }
            if ($request->hasFile('signature_client_file')) {
                $path = $request->file('signature_client_file')->store('signatures', 'public');
                $data['signature_client'] = $path;
            }

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

            // Upload des photos avec type (avant, apres, probleme)
            if ($request->hasFile('photos')) {
                $types = $request->input('photos_types', []);
                foreach ($request->file('photos') as $index => $file) {
                    $path = $file->store('photos/rapports', 'public');
                    $type = $types[$index] ?? 'avant';
                    $rapport->photos()->create([
                        'chemin'     => $path,
                        'type_photo' => $type,
                        'date_prise' => now(),
                    ]);
                }
            }

            // Upload des vidéos
            if ($request->hasFile('videos')) {
                foreach ($request->file('videos') as $file) {
                    $path = $file->store('videos/rapports', 'public');
                    $rapport->videos()->create(['chemin' => $path]);
                }
            }

            // Upload des documents justificatifs
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('documents/rapports', 'public');
                    $rapport->documents()->create([
                        'nom_original' => $file->getClientOriginalName(),
                        'chemin'       => $path,
                        'type'         => $file->getClientMimeType(),
                        'taille'       => $file->getSize(),
                    ]);
                }
            }

            // Traçabilité dans l'historique de l'intervention
            app(\App\Services\InterventionService::class)->enregistrerHistorique(
                $intervention,
                statut_avant: $intervention->statut,
                statut_apres: $intervention->statut,
                commentaire: 'Saisie / Mise à jour du compte-rendu & preuves terrain par le technicien'
            );

            $result = $rapport->fresh(['intervention', 'photos', 'videos', 'documents', 'reponses.question']);

            // Stocker en cache pour idempotence (5 min)
            if ($idempotencyKey) {
                \Illuminate\Support\Facades\Cache::put($cacheKey, $result, now()->addMinutes(5));
            }

            return $this->successResponse($result, $message);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), null, 400);
        }
    }

    /**
     * Obtenir la checklist de complétude des preuves de l'intervention (Admin/Tech).
     */
    public function checkCompletude(Request $request, Intervention $intervention): JsonResponse
    {
        $completude = app(\App\Services\InterventionService::class)->verifierCompletudePreuves($intervention);
        return $this->successResponse($completude, 'Checklist de complétude des preuves calculée.');
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
