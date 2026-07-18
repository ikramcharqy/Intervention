<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTacheRequest;
use App\Http\Requests\UpdateTacheRequest;
use App\Models\Tache;
use App\Services\TacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestion du catalogue de tâches.
 */
class TacheController extends Controller
{
    protected TacheService $tacheService;

    public function __construct(TacheService $tacheService)
    {
        $this->tacheService = $tacheService;
    }

    /**
     * Liste des tâches.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $taches = Tache::with(['parent', 'enfants'])
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('parent_id')
            ->orderBy('ordre')
            ->paginate(15)
            ->withQueryString();

        return view('taches.index', compact('taches', 'search'));
    }

    /**
     * Formulaire de création.
     */
    public function create(): View
    {
        $tachesParentes = Tache::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('taches.create', compact('tachesParentes'));
    }

    /**
     * Enregistre une tâche.
     */
    public function store(StoreTacheRequest $request): RedirectResponse
    {
        $tache = $this->tacheService->createTache($request->validated());

        return redirect()
            ->route('taches.index')
            ->with('success', "La tâche **{$tache->nom}** a été ajoutée avec succès.");
    }

    /**
     * Détails de la tâche.
     */
    public function show(Tache $tache): View
    {
        $tache->load(['parent', 'enfants' => fn($q) => $q->orderBy('ordre')]);

        return view('taches.show', compact('tache'));
    }

    /**
     * Formulaire d'édition.
     */
    public function edit(Tache $tache): View
    {
        $tachesParentes = Tache::whereNull('parent_id')
            ->where('is_active', true)
            ->where('id', '!=', $tache->id)
            ->orderBy('nom')
            ->get(['id', 'nom']);

        return view('taches.edit', compact('tache', 'tachesParentes'));
    }

    /**
     * Met à jour une tâche.
     */
    public function update(UpdateTacheRequest $request, Tache $tache): RedirectResponse
    {
        $this->tacheService->updateTache($tache, $request->validated());

        return redirect()
            ->route('taches.index')
            ->with('success', "La tâche **{$tache->nom}** a été mise à jour.");
    }

    /**
     * Supprime ou désactive une tâche.
     */
    public function destroy(Tache $tache): RedirectResponse
    {
        $nom = $tache->nom;
        $deleted = $this->tacheService->deleteOrDeactivate($tache);

        $message = $deleted 
            ? "La tâche **{$nom}** a été définitivement supprimée." 
            : "La tâche **{$nom}** étant liée à des interventions, elle a été désactivée ainsi que ses enfants.";

        return redirect()
            ->route('taches.index')
            ->with('success', $message);
    }
}
