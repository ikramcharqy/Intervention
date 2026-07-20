<?php

namespace App\Services;

use App\Models\Rapport;
use Illuminate\Support\Facades\DB;

class RapportService
{
    /**
     * Enregistre un rapport d'intervention.
     * Le statut de l'intervention n'est pas modifié ici — il est géré
     * exclusivement par InterventionService (workflow centralisé).
     */
    public function createRapport(array $data): Rapport
    {
        return Rapport::create($data);
    }

    /**
     * Met à jour le rapport d'intervention.
     */
    public function updateRapport(Rapport $rapport, array $data): Rapport
    {
        $rapport->update($data);
        return $rapport;
    }

    /**
     * Supprime le rapport et replace l'intervention en cours.
     */
    public function deleteRapport(Rapport $rapport): void
    {
        DB::transaction(function () use ($rapport) {
            // Remet l'intervention en cours
            $rapport->intervention->update([
                'statut' => 'En cours',
                'date_reelle_fin' => null,
            ]);

            $rapport->delete();
        });
    }

    /**
     * Vérifie si le client a le droit de consulter le rapport (seulement s'il est validé / Terminé).
     */
    public function canClientView(Rapport $rapport, $user): bool
    {
        if ($user->hasRole('Client')) {
            // Le client ne peut voir le rapport que si l'intervention est officiellement validée (Terminee)
            return $rapport->intervention->statut === 'Terminee';
        }

        return true; // Les admins, commerciaux et techniciens peuvent toujours le voir
    }
}
