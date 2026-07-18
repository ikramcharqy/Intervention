<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmplacementRequest;
use App\Http\Requests\UpdateEmplacementRequest;
use App\Models\Chantier;
use App\Models\Emplacement;
use App\Services\EmplacementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Gestion des emplacements physiques (QR Code / NFC).
 * Ce contrôleur est désormais allégé et gère les actions depuis la vue du Chantier (sans vues propres).
 */
class EmplacementController extends Controller
{
    protected EmplacementService $emplacementService;

    public function __construct(EmplacementService $emplacementService)
    {
        $this->emplacementService = $emplacementService;
    }

    /**
     * Enregistre un nouvel emplacement.
     */
    public function store(Chantier $chantier, StoreEmplacementRequest $request): RedirectResponse
    {
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
        $this->emplacementService->updateEmplacement($emplacement, $request->validated());

        return redirect()->back()->with('success', "L'emplacement **{$emplacement->nom}** a été mis à jour avec succès.");
    }

    /**
     * Désactive logiquement un emplacement.
     */
    public function destroy(Emplacement $emplacement): RedirectResponse
    {
        $this->emplacementService->deactivate($emplacement);

        return redirect()->back()->with('success', "L'emplacement **{$emplacement->nom}** a été désactivé avec succès.");
    }

    /**
     * Réactive un emplacement.
     */
    public function restore(Emplacement $emplacement): RedirectResponse
    {
        $this->emplacementService->activate($emplacement);

        return redirect()->back()->with('success', "L'emplacement **{$emplacement->nom}** a été réactivé avec succès.");
    }
}
