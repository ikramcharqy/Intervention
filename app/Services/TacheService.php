<?php

namespace App\Services;

use App\Models\Tache;

class TacheService
{
    /**
     * Enregistre une tâche.
     */
    public function createTache(array $data): Tache
    {
        return Tache::create($data);
    }

    /**
     * Met à jour une tâche et gère la désactivation en cascade.
     */
    public function updateTache(Tache $tache, array $data): Tache
    {
        $tache->update($data);

        if (!$tache->is_active) {
            $tache->enfants()->update(['is_active' => false]);
        }

        return $tache;
    }

    /**
     * Gère la suppression ou la désactivation si elle est déjà utilisée.
     */
    public function deleteOrDeactivate(Tache $tache): bool
    {
        if ($tache->interventions()->exists()) {
            $tache->update(['is_active' => false]);
            $tache->enfants()->update(['is_active' => false]);
            return false; // Désactivée
        }

        $tache->enfants()->update(['parent_id' => null]);
        $tache->delete();
        return true; // Supprimée
    }
}
