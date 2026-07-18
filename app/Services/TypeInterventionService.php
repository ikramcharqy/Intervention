<?php

namespace App\Services;

use App\Models\TypeIntervention;

class TypeInterventionService
{
    /**
     * Enregistre un type d'intervention.
     */
    public function createType(array $data): TypeIntervention
    {
        return TypeIntervention::create($data);
    }

    /**
     * Met à jour un type d'intervention.
     */
    public function updateType(TypeIntervention $type, array $data): TypeIntervention
    {
        $type->update($data);
        return $type;
    }

    /**
     * Alterne le statut actif/inactif du type d'intervention.
     */
    public function toggleActive(TypeIntervention $type): void
    {
        $type->update(['is_active' => !$type->is_active]);
    }
}
