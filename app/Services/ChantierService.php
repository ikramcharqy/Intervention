<?php

namespace App\Services;

use App\Models\Chantier;
use Illuminate\Support\Str;

class ChantierService
{
    /**
     * Enregistre un nouveau chantier.
     */
    public function createChantier(array $data): Chantier
    {
        return Chantier::create($data);
    }

    /**
     * Met à jour un chantier existant.
     */
    public function updateChantier(Chantier $chantier, array $data): Chantier
    {
        $chantier->update($data);
        return $chantier;
    }

    /**
     * Désactive logiquement un chantier.
     */
    public function deactivate(Chantier $chantier): void
    {
        $chantier->update(['is_active' => false]);
    }

    /**
     * Réactive un chantier.
     */
    public function activate(Chantier $chantier): void
    {
        $chantier->update(['is_active' => true]);
    }
}
