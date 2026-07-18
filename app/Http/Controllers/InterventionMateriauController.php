<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use App\Models\InterventionMateriau;
use App\Models\Materiau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Gestion des matériaux associés à une intervention.
 */
class InterventionMateriauController extends Controller
{
    /**
     * Associe un matériau à une intervention avec une quantité.
     */
    public function store(Request $request, Intervention $intervention): RedirectResponse
    {
        $validated = $request->validate([
            'materiau_id' => ['required', 'exists:materiaus,id'],
            'quantite'    => ['required', 'numeric', 'min:0.01'],
            'unite'       => ['nullable', 'string', 'max:50'],
            'commentaire' => ['nullable', 'string', 'max:500'],
        ], [
            'materiau_id.required' => 'Veuillez sélectionner un matériau.',
            'materiau_id.exists'   => 'Ce matériau n\'existe pas.',
            'quantite.required'    => 'La quantité est obligatoire.',
            'quantite.min'         => 'La quantité doit être supérieure à 0.',
        ]);

        $materiau = Materiau::findOrFail($validated['materiau_id']);

        InterventionMateriau::create([
            'intervention_id' => $intervention->id,
            'materiau_id'     => $materiau->id,
            'quantite'        => $validated['quantite'],
            'unite'           => $validated['unite'] ?? $materiau->unite,
            'commentaire'     => $validated['commentaire'] ?? null,
        ]);

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', "Le matériau **{$materiau->nom}** a été ajouté à l'intervention.");
    }

    /**
     * Retire un matériau d'une intervention.
     */
    public function destroy(Intervention $intervention, InterventionMateriau $interventionMateriau): RedirectResponse
    {
        // Vérifier que la ligne appartient bien à cette intervention
        if ($interventionMateriau->intervention_id !== $intervention->id) {
            abort(403, 'Ce matériau n\'appartient pas à cette intervention.');
        }

        $nom = $interventionMateriau->materiau->nom ?? 'Inconnu';
        $interventionMateriau->delete();

        return redirect()
            ->route('interventions.show', $intervention)
            ->with('success', "Le matériau **{$nom}** a été retiré de l'intervention.");
    }
}
