<?php

namespace App\Services;

use App\Models\Materiau;

class MateriauService
{
    /**
     * Enregistre un matériau.
     */
    public function createMateriau(array $data): Materiau
    {
        return Materiau::create($data);
    }

    /**
     * Met à jour un matériau.
     */
    public function updateMateriau(Materiau $materiau, array $data): Materiau
    {
        $materiau->update($data);
        return $materiau;
    }

    /**
     * Gère la suppression ou la désactivation si elle est déjà utilisée.
     */
    public function deleteOrDeactivate(Materiau $materiau): bool
    {
        if ($materiau->interventions()->exists()) {
            $materiau->update(['is_active' => false]);
            return false; // Désactivée
        }

        $materiau->delete();
        return true; // Supprimée
    }
}
