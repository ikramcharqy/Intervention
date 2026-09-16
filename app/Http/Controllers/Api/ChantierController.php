<?php

namespace App\Http\Controllers\Api;

use App\Models\Chantier;
use App\Services\ChantierService;
use App\Services\InterventionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Détail chantier pour l'app Technicien — réutilise les mêmes champs et la
 * même logique documents que ClientModule\ChantierController::show() (pas de
 * fiche différente), simplement exposée à un technicien assigné plutôt qu'au
 * client propriétaire.
 */
class ChantierController extends BaseApiController
{
    public function __construct(
        private ChantierService $chantierService,
        private InterventionService $interventionService,
    ) {
    }

    public function show(Request $request, Chantier $chantier): JsonResponse
    {
        if (!$this->chantierService->estAccessiblePourTechnicien($chantier, $request->user()->id)) {
            return $this->errorResponse('Accès non autorisé à ce chantier.', null, 403);
        }

        $chantier->load('emplacements');

        // /api/media/{path} (Api\MediaController), pas Storage::url() — ce
        // dernier pointe vers le lien symbolique public/storage, servi
        // statiquement en dehors de l'application et donc jamais soumis au
        // middleware CORS (cf. Api\MediaController pour le détail).
        $documents = $this->chantierService->documentsPourChantier($chantier)->map(fn ($document) => [
            'id' => $document->id,
            'nom_original' => $document->nom_original,
            'type_document' => $document->type_document,
            'url' => url('/api/media/' . $document->chemin),
        ]);

        // Étape 6 du prompt "design de référence" : mêmes interventions
        // Terminée/Validée que la fiche chantier web (InterventionService::
        // pourChantiers, déjà utilisé par ClientModule\ChantierController),
        // pas une requête reconstruite différemment.
        $interventionsTerminees = $this->interventionService
            ->pourChantiers(collect([$chantier->id]))
            ->whereIn('statut', ['Terminee', 'Validee'])
            ->with('typeIntervention')
            ->latest('updated_at')
            ->take(10)
            ->get()
            ->map(fn ($intervention) => [
                'id' => $intervention->id,
                'code_intervention' => $intervention->code_intervention,
                'statut' => $intervention->statut,
                'type_intervention_nom' => $intervention->typeIntervention?->nom,
                'date_fin' => $intervention->date_reelle_fin,
            ]);

        return $this->successResponse([
            'chantier' => $chantier,
            'documents' => $documents,
            'interventions_terminees' => $interventionsTerminees,
        ], 'Détails du chantier récupérés.');
    }
}
