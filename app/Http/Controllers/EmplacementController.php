<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmplacementRequest;
use App\Http\Requests\UpdateEmplacementRequest;
use App\Models\Chantier;
use App\Models\Emplacement;
use App\Services\EmplacementService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

/**
 * Gestion des emplacements physiques (QR Code / NFC).
 * La création/modification/suppression se pilote depuis la vue du Chantier ;
 * `show()` est la première vue propre à ce contrôleur (fiche détail emplacement).
 */
class EmplacementController extends Controller
{
    use AuthorizesRequests;

    protected EmplacementService $emplacementService;

    public function __construct(EmplacementService $emplacementService)
    {
        $this->emplacementService = $emplacementService;
    }

    /**
     * Affiche la fiche détail d'un emplacement (infos + historique des interventions).
     */
    public function show(Emplacement $emplacement): View
    {
        $emplacement->load([
            'chantier.client',
            'interventions' => fn ($q) => $q->orderBy('date_prevue_debut', 'desc'),
            'interventions.typeIntervention',
            'interventions.technicien',
        ]);

        return view('emplacements.show', compact('emplacement'));
    }

    /**
     * Enregistre un nouvel emplacement.
     */
    public function store(Chantier $chantier, StoreEmplacementRequest $request): RedirectResponse
    {
        $this->authorize('create', Emplacement::class);

        // On s'assure que le chantier_id correspond à la route, bien qu'il soit validé.
        $data = $request->validated();
        $data['chantier_id'] = $chantier->id;

        $this->emplacementService->createEmplacement($data);

        return redirect()->back()->with('success', "L'emplacement a été créé avec succès.");
    }

    /**
     * Met à jour un emplacement.
     */
    public function update(UpdateEmplacementRequest $request, Emplacement $emplacement): RedirectResponse
    {
        $this->authorize('update', $emplacement);

        $this->emplacementService->updateEmplacement($emplacement, $request->validated());

        return redirect()->back()->with('success', "L'emplacement **{$emplacement->nom}** a été mis à jour avec succès.");
    }

    /**
     * Désactive logiquement un emplacement (bloqué si des interventions actives y sont rattachées).
     */
    public function destroy(Emplacement $emplacement): RedirectResponse
    {
        $this->authorize('delete', $emplacement);

        try {
            $this->emplacementService->deactivate($emplacement);
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->back()->with('success', "L'emplacement **{$emplacement->nom}** a été désactivé avec succès.");
    }

    /**
     * Réactive un emplacement.
     */
    public function restore(Emplacement $emplacement): RedirectResponse
    {
        $this->authorize('delete', $emplacement);

        $this->emplacementService->activate($emplacement);

        return redirect()->back()->with('success', "L'emplacement **{$emplacement->nom}** a été réactivé avec succès.");
    }

    /**
     * Remonte un emplacement dans l'ordre d'affichage du chantier.
     */
    public function moveUp(Emplacement $emplacement): RedirectResponse
    {
        $this->authorize('update', $emplacement);

        $this->emplacementService->moveUp($emplacement);

        return redirect()->back();
    }

    /**
     * Descend un emplacement dans l'ordre d'affichage du chantier.
     */
    public function moveDown(Emplacement $emplacement): RedirectResponse
    {
        $this->authorize('update', $emplacement);

        $this->emplacementService->moveDown($emplacement);

        return redirect()->back();
    }
}
