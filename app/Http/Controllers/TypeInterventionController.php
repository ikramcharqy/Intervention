<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeInterventionRequest;
use App\Http\Requests\UpdateTypeInterventionRequest;
use App\Models\Intervention;
use App\Models\TypeIntervention;
use App\Services\FormulaireService;
use App\Services\TypeInterventionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion des types d'interventions.
 */
class TypeInterventionController extends Controller
{
    protected TypeInterventionService $typeService;

    public function __construct(TypeInterventionService $typeService)
    {
        $this->typeService = $typeService;
    }

    /**
     * Seul le Super Admin peut créer/modifier/supprimer les Types d'Intervention
     * (même règle que FormulaireController::ensureSuperAdmin() pour les Formulaires) —
     * cette route n'était protégée par aucun contrôle avant ce correctif.
     */
    private function ensureSuperAdmin(): void
    {
        if (!auth()->user()?->hasRole('Super Admin')) {
            abort(403, "Cette action est réservée au Super Admin.");
        }
    }

    /**
     * Liste paginée des types d'interventions.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $filtre = $request->input('filtre', 'actifs'); // 'actifs' | 'tous'
        $tri = in_array($request->input('tri'), ['nom', 'duree_estimee'], true) ? $request->input('tri') : 'nom';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $typesIntervention = TypeIntervention::withCount('interventions')
            ->withCount(['interventions as interventions_en_cours_count' => fn ($q) => $q->actives()])
            ->with(['formulaire' => fn ($q) => $q->withCount('questions')])
            ->when($filtre === 'actifs', fn ($q) => $q->where('is_active', true))
            ->when($search, fn ($q, $s) => $q->where('nom', 'like', "%{$s}%"))
            ->orderBy($tri, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('types-intervention.index', compact('typesIntervention', 'search', 'filtre', 'tri', 'direction'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        return view('types-intervention.create');
    }

    /**
     * Enregistre un type d'intervention.
     */
    public function store(StoreTypeInterventionRequest $request, FormulaireService $formulaireService): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $type = $this->typeService->createType($request->validated());

        // Étape 3.1 : "Enregistrer et configurer le formulaire" crée immédiatement le
        // Formulaire associé (réutilise FormulaireService::createFormulaire() déjà
        // implémenté) et amène directement à l'éditeur de Questions.
        if ($request->input('action') === 'save_and_configure') {
            $formulaire = $formulaireService->createFormulaire([
                'type_intervention_id' => $type->id,
                'nom' => $type->nom,
            ]);

            return redirect()
                ->route('formulaires.show', $formulaire)
                ->with('success', "Le type d'intervention **{$type->nom}** a été créé. Configurez maintenant les questions du protocole.");
        }

        return redirect()
            ->route('types-intervention.index')
            ->with('success', "Le type d'intervention **{$type->nom}** a été créé avec succès.");
    }

    /**
     * Détail d'un type d'intervention.
     */
    public function show(TypeIntervention $typeIntervention): View
    {
        // Bug corrigé : ->load(['formulaires']) référençait une relation inexistante
        // (le hasOne du modèle s'appelle 'formulaire', singulier) — provoquait une
        // BadMethodCallException à chaque visite de cette page.
        $typeIntervention->load([
            'interventions' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(10),
            'formulaire.questions',
        ]);

        return view('types-intervention.show', compact('typeIntervention'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(TypeIntervention $typeIntervention): View
    {
        return view('types-intervention.edit', compact('typeIntervention'));
    }

    /**
     * Met à jour un type d'intervention.
     */
    public function update(UpdateTypeInterventionRequest $request, TypeIntervention $typeIntervention): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $this->typeService->updateType($typeIntervention, $request->validated());

        return redirect()
            ->route('types-intervention.index')
            ->with('success', "Le type d'intervention **{$typeIntervention->nom}** a été mis à jour avec succès.");
    }

    /**
     * Supprime ou bloque la suppression si utilisé.
     */
    public function destroy(TypeIntervention $typeIntervention): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $nbInterventions = $typeIntervention->interventions()->count();
        if ($nbInterventions > 0) {
            return redirect()
                ->route('types-intervention.index')
                ->with('error', "Ce type ne peut pas être supprimé : {$nbInterventions} intervention(s) y sont rattachées.");
        }

        $nom = $typeIntervention->nom;
        $typeIntervention->delete();

        return redirect()
            ->route('types-intervention.index')
            ->with('success', "Le type d'intervention **{$nom}** a été supprimé avec succès.");
    }

    /**
     * Active ou désactive un type d'intervention.
     */
    public function toggleActive(TypeIntervention $typeIntervention): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $this->typeService->toggleActive($typeIntervention);
        $statut = $typeIntervention->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->route('types-intervention.index')
            ->with('success', "Le type **{$typeIntervention->nom}** a été {$statut} avec succès.");
    }
}
