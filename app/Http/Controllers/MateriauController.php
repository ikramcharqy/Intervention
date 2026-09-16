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
     * Autorise uniquement Super Admin et Admin (rôle stocké en minuscule 'admin').
     * Bloque toute autre personne — notamment les Techniciens — avec un 403 réel.
     */
    private function ensureAdminOuSuperAdmin(): void
    {
        if (!auth()->user()?->hasAnyRole(['Super Admin', 'admin'])) {
            abort(403, "Cette action est réservée aux administrateurs.");
        }
    }

    /**
     * Autorise uniquement le Super Admin.
     */
    private function ensureSuperAdmin(): void
    {
        if (!auth()->user()?->hasRole('Super Admin')) {
            abort(403, "Cette action est réservée au Super Admin.");
        }
    }

    /**
     * Liste paginée des matériaux.
     * Accessible à tout utilisateur authentifié : Admin/Super Admin voient tout
     * (prix, stock exact), Technicien voit une version limitée (nom, référence,
     * unité, catégorie, disponibilité) — filtrage appliqué côté vue via
     * $canSeePrix, calculé ici.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $materiaux = Materiau::query()
            ->when($search, function ($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('reference', 'like', "%{$search}%");
            })
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        $canSeePrix = (bool) auth()->user()?->hasAnyRole(['Super Admin', 'admin']);

        return view('materiaux.index', compact('materiaux', 'search', 'canSeePrix'));
    }

    /**
     * Formulaire de création. Admin/Super Admin uniquement.
     */
    public function create(): View
    {
        $this->ensureAdminOuSuperAdmin();

        return view('materiaux.create');
    }

    /**
     * Historique des mouvements de stock d'un matériau. Admin/Super Admin uniquement.
     */
    public function mouvements(Materiau $materiau): View
    {
        $this->ensureAdminOuSuperAdmin();

        $mouvements = $materiau->mouvements()
            ->with(['intervention', 'technicien', 'user'])
            ->latest()
            ->paginate(25);

        return view('materiaux.mouvements', compact('materiau', 'mouvements'));
    }

    /**
     * Réapprovisionne le stock d'un matériau. Admin/Super Admin uniquement.
     * Passe systématiquement par StockService::reapprovisionner() — jamais
     * de modification directe du champ stock.
     */
    public function reapprovisionner(Request $request, Materiau $materiau): RedirectResponse
    {
        $this->ensureAdminOuSuperAdmin();

        $validated = $request->validate([
            'quantite'    => ['required', 'numeric', 'min:0.01'],
            'commentaire' => ['nullable', 'string', 'max:255'],
        ], [
            'quantite.required' => 'La quantité à réapprovisionner est obligatoire.',
            'quantite.min'      => 'La quantité doit être supérieure à 0.',
        ]);

        $this->materiauService->reapprovisionner(
            $materiau,
            (float) $validated['quantite'],
            $validated['commentaire'] ?? null,
            $request->user()
        );

        return redirect()
            ->route('materiaux.show', $materiau)
            ->with('success', "Stock de **{$materiau->nom}** réapprovisionné de {$validated['quantite']} {$materiau->unite}.");
    }

    /**
     * Enregistre un matériau. Admin/Super Admin uniquement.
     */
    public function store(StoreMateriauRequest $request): RedirectResponse
    {
        $this->ensureAdminOuSuperAdmin();

        $materiau = $this->materiauService->createMateriau($request->validated());

        return redirect()
            ->route('materiaux.index')
            ->with('success', "Le matériau **{$materiau->nom}** a été ajouté au catalogue.");
    }

    /**
     * Détails d'un matériau.
     * Accessible à tout utilisateur authentifié — la vue masque prix/stock
     * exact et les actions de gestion pour un Technicien.
     */
    public function show(Materiau $materiau): View
    {
        $materiau->load(['interventions.intervention.technicien']);

        $canSeePrix = (bool) auth()->user()?->hasAnyRole(['Super Admin', 'admin']);

        return view('materiaux.show', compact('materiau', 'canSeePrix'));
    }

    /**
     * Formulaire d'édition. Admin/Super Admin uniquement.
     */
    public function edit(Materiau $materiau): View
    {
        $this->ensureAdminOuSuperAdmin();

        return view('materiaux.edit', compact('materiau'));
    }

    /**
     * Met à jour le matériau (infos générales — jamais le stock). Admin/Super Admin uniquement.
     */
    public function update(UpdateMateriauRequest $request, Materiau $materiau): RedirectResponse
    {
        $this->ensureAdminOuSuperAdmin();

        $this->materiauService->updateMateriau($materiau, $request->validated());

        return redirect()
            ->route('materiaux.index')
            ->with('success', "Le matériau **{$materiau->nom}** a été mis à jour avec succès.");
    }

    /**
     * Supprime (soft delete) ou désactive le matériau. Super Admin UNIQUEMENT.
     * Le SoftDeletes du modèle Materiau garantit que l'historique des
     * mouvements de stock liés reste intact (deleted_at, pas de suppression
     * physique).
     */
    public function destroy(Materiau $materiau): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $nom = $materiau->nom;
        $deleted = $this->materiauService->deleteOrDeactivate($materiau);

        $message = $deleted
            ? "Le matériau **{$nom}** a été archivé (suppression réversible, historique des mouvements conservé)."
            : "Le matériau **{$nom}** étant associé à des interventions, il a été désactivé pour empêcher toute nouvelle utilisation.";

        return redirect()
            ->route('materiaux.index')
            ->with('success', $message);
    }
}
