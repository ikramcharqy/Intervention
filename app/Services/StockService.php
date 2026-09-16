<?php

namespace App\Services;

use App\Models\Intervention;
use App\Models\Materiau;
use App\Models\MouvementStock;
use App\Models\User;
use RuntimeException;

class StockService
{
    /**
     * Décrémente le stock d'un matériau suite à sa consommation réelle
     * sur une intervention, et trace le mouvement.
     * Doit toujours être appelé à l'intérieur d'une transaction DB par
     * l'appelant lorsque plusieurs consommations doivent réussir ensemble.
     *
     * @throws RuntimeException si le stock disponible est insuffisant
     */
    public function consommerMateriau(
        Materiau $materiau,
        float $quantite,
        ?Intervention $intervention = null,
        ?User $technicien = null,
        ?User $auteur = null
    ): MouvementStock {
        if ((float) $materiau->stock < $quantite) {
            throw new RuntimeException(
                "Stock insuffisant pour \"{$materiau->nom}\" ({$materiau->reference}) : "
                . "disponible {$materiau->stock} {$materiau->unite}, demandé {$quantite} {$materiau->unite}."
            );
        }

        $materiau->decrement('stock', $quantite);
        $materiau->refresh();

        return MouvementStock::create([
            'materiau_id'     => $materiau->id,
            'intervention_id' => $intervention?->id,
            'technicien_id'   => $technicien?->id,
            'user_id'         => $auteur?->id,
            'type_mouvement'  => 'sortie',
            'quantite'        => $quantite,
            'stock_apres'     => $materiau->stock,
        ]);
    }

    /**
     * Réapprovisionne le stock d'un matériau (achat, retour) et trace le mouvement.
     */
    public function reapprovisionner(
        Materiau $materiau,
        float $quantite,
        ?string $commentaire = null,
        ?User $auteur = null
    ): MouvementStock {
        if ($quantite <= 0) {
            throw new RuntimeException('La quantité à réapprovisionner doit être supérieure à 0.');
        }

        $materiau->increment('stock', $quantite);
        $materiau->refresh();

        return MouvementStock::create([
            'materiau_id'    => $materiau->id,
            'user_id'        => $auteur?->id,
            'type_mouvement' => 'entree',
            'quantite'       => $quantite,
            'stock_apres'    => $materiau->stock,
            'commentaire'    => $commentaire,
        ]);
    }
}
