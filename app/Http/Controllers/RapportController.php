<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRapportRequest;
use App\Http\Requests\UpdateRapportRequest;
use App\Models\Rapport;
use App\Models\Intervention;
use App\Services\RapportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Gestion des rapports d'interventions.
 */
class RapportController extends Controller
{
    protected RapportService $rapportService;

    public function __construct(RapportService $rapportService)
    {
        $this->rapportService = $rapportService;
    }

    /**
     * Liste des rapports.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $rapports = Rapport::with(['intervention.chantier.client', 'intervention.technicien'])
            ->when($search, function ($query, $search) {
                $query->where('commentaire', 'like', "%{$search}%")
                      ->orWhereHas('intervention', function ($q) use ($search) {
                          $q->where('code_intervention', 'like', "%{$search}%")
                            ->orWhereHas('chantier', fn($qc) => $qc->where('nom', 'like', "%{$search}%"));
                      });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('rapports.index', compact('rapports', 'search'));
    }

    /**
     * Formulaire de création.
     */
    public function create(Request $request): View
    {
        $interventionId = $request->input('intervention_id');
        
        $interventions = Intervention::whereDoesntHave('rapport')
            ->when($interventionId, fn($q) => $q->where('id', $interventionId))
            ->orderBy('code_intervention', 'desc')
            ->get(['id', 'code_intervention']);

        return view('rapports.create', compact('interventions', 'interventionId'));
    }

    /**
     * Enregistre un rapport.
     */
    public function store(StoreRapportRequest $request): RedirectResponse
    {
        $rapport = $this->rapportService->createRapport($request->validated());

        return redirect()
            ->route('interventions.show', $rapport->intervention_id)
            ->with('success', "Le rapport a été soumis. L'intervention est en attente de validation par l'administration.");
    }

    /**
     * Affiche un rapport d'intervention avec vérification de la validation pour le client.
     */
    public function show(Rapport $rapport): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user && !$this->rapportService->canClientView($rapport, $user)) {
            return redirect()
                ->route('interventions.index')
                ->with('error', "Ce rapport est en cours de validation par l'administration et n'est pas encore consultable.");
        }

        $rapport->load(['intervention.technicien', 'intervention.chantier.client', 'intervention.materiaux.materiau', 'photos', 'videos', 'documents', 'reponses.question']);

        return view('rapports.show', compact('rapport'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Rapport $rapport): View
    {
        $interventions = Intervention::where('id', $rapport->intervention_id)->get(['id', 'code_intervention']);

        return view('rapports.edit', compact('rapport', 'interventions'));
    }

    /**
     * Met à jour le rapport.
     */
    public function update(UpdateRapportRequest $request, Rapport $rapport): RedirectResponse
    {
        $this->rapportService->updateRapport($rapport, $request->validated());

        return redirect()
            ->route('interventions.show', $rapport->intervention_id)
            ->with('success', "Le rapport a été mis à jour avec succès.");
    }

    /**
     * Supprime le rapport.
     */
    public function destroy(Rapport $rapport): RedirectResponse
    {
        $codeIntervention = $rapport->intervention->code_intervention;

        $this->rapportService->deleteRapport($rapport);

        return redirect()
            ->route('rapports.index')
            ->with('success', "Le rapport de l'intervention **{$codeIntervention}** a été supprimé. L'intervention est repassée en cours.");
    }

    /**
     * Génère et télécharge le rapport au format PDF.
     */
    public function generatePdf(Rapport $rapport)
    {
        $user = auth()->user();

        if ($user && !$this->rapportService->canClientView($rapport, $user)) {
            return redirect()
                ->route('interventions.index')
                ->with('error', "Ce rapport est en cours de validation par l'administration.");
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
            'reponses.question.choix'
        ]);

        $pdf = Pdf::loadView('rapports.pdf', compact('rapport'));

        return $pdf->download('rapport_' . ($rapport->intervention->code_intervention ?? $rapport->id) . '.pdf');
    }
}
