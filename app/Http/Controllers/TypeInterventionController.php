<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeInterventionRequest;
use App\Http\Requests\UpdateTypeInterventionRequest;
use App\Models\TypeIntervention;
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
     * Liste paginée des types d'interventions.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $typesIntervention = TypeIntervention::withCount('interventions')
            ->when($search, fn ($q, $s) => $q->where('nom', 'like', "%{$s}%"))
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('types-intervention.index', compact('typesIntervention', 'search'));
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
    public function store(StoreTypeInterventionRequest $request): RedirectResponse
    {
        $type = $this->typeService->createType($request->validated());

        return redirect()
            ->route('types-intervention.index')
            ->with('success', "Le type d'intervention **{$type->nom}** a été créé avec succès.");
    }

    /**
     * Détail d'un type d'intervention.
     */
    public function show(TypeIntervention $typeIntervention): View
    {
        $typeIntervention->load([
            'interventions' => fn ($q) => $q->orderBy('created_at', 'desc')->limit(10),
            'formulaires',
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
        if ($typeIntervention->interventions()->exists()) {
            return redirect()
                ->route('types-intervention.index')
                ->with('error', "Impossible de supprimer ce type : des interventions y sont associées.");
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
        $this->typeService->toggleActive($typeIntervention);
        $statut = $typeIntervention->is_active ? 'activé' : 'désactivé';

        return redirect()
            ->route('types-intervention.index')
            ->with('success', "Le type **{$typeIntervention->nom}** a été {$statut} avec succès.");
    }
}
