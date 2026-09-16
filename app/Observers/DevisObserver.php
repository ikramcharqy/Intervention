<?php

namespace App\Observers;

use App\Models\Devis;
use App\Services\ProspectConversionService;

class DevisObserver
{
    /**
     * Conversion automatique Prospect → Client quand un devis lié à un prospect
     * (pas encore rattaché à un client) passe au statut "Accepté" : sans ce
     * déclencheur, le prospect restait indéfiniment "non converti" malgré une
     * vente conclue, et le devis restait bloqué sans client_id (empêchant la
     * génération de la demande d'intervention).
     */
    public function updated(Devis $devis): void
    {
        if (! $devis->wasChanged('statut') || $devis->statut !== 'Accepté') {
            return;
        }

        if ($devis->client_id || ! $devis->prospect_id) {
            return;
        }

        $prospect = $devis->prospect;
        if (! $prospect) {
            return;
        }

        $client = app(ProspectConversionService::class)->convert($prospect, 'automatique');

        // updateQuietly : évite de redéclencher cet observer (la mise à jour ne
        // porte que sur client_id, pas sur statut, donc le garde ci-dessus suffirait
        // déjà, mais quiet reste plus explicite et évite tout coût d'événement inutile).
        $devis->updateQuietly(['client_id' => $client->id]);
    }
}
