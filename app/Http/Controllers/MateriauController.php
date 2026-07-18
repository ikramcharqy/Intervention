<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMateriauRequest;
use App\Http\Requests\UpdateMateriauRequest;
use App\Models\Materiau;
use App\Services\MateriauService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion du catalogue des matériaux.
 */
class MateriauController extends Controller
{
    protected MateriauService $materiauService;

    public function __construct(MateriauService $materiauService)
    {
        $this->materiauService = $materiauService;
    }

    /**
     * Liste paginée des matériaux.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $materiaux = Materiau::query()
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('materiaux.index', compact('materiaux', 'search'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        return view('materiaux.create');
    }

    /**
     * Enregistre un matériau.
     */
    public function store(StoreMateriauRequest $request): RedirectResponse
    {
        $materiau = $this->materiauService->createMateriau($request->validated());

        return redirect()
            ->route('materiaux.index')
            ->with('success', "Le matériau **{$materiau->nom}** a été ajouté au catalogue.");
    }

    /**
     * Détails d'un matériau.
     */
    public function show(Materiau $materiau): View
    {
        $materiau->load(['interventions.intervention.technicien']);

        return view('materiaux.show', compact('materiau'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Materiau $materiau): View
    {
        return view('materiaux.edit', compact('materiau'));
    }

    /**
     * Met à jour le matériau.
     */
    public function update(UpdateMateriauRequest $request, Materiau $materiau): RedirectResponse
    {
        $this->materiauService->updateMateriau($materiau, $request->validated());

        return redirect()
            ->route('materiaux.index')
            ->with('success', "Le matériau **{$materiau->nom}** a été mis à jour avec succès.");
    }

    /**
     * Supprime ou désactive le matériau.
     */
    public function destroy(Materiau $materiau): RedirectResponse
    {
        $nom = $materiau->nom;
        $deleted = $this->materiauService->deleteOrDeactivate($materiau);

        $message = $deleted 
            ? "Le matériau **{$nom}** a été définitivement supprimé du catalogue." 
            : "Le matériau **{$nom}** étant associé à des interventions, il a été désactivé pour empêcher toute nouvelle utilisation.";

        return redirect()
            ->route('materiaux.index')
            ->with('success', $message);
    }
}
