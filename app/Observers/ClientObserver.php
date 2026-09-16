<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\User;

class ClientObserver
{
    private const CHAMPS_CONTACT = [
        'nom',
        'nom_contact',
        'telephone',
        'telephone_secondaire',
        'email',
        'adresse_facturation',
        'ville',
        'pays',
    ];

    public function created(Client $client): void
    {
        $client->activites()->create([
            'user_id'       => auth()->id(),
            'type_action'   => 'creation',
            'description'   => "Client créé : {$client->nom}",
            'donnees_apres' => [
                'nom'           => $client->nom,
                'type_client'   => $client->type_client,
                'ville'         => $client->ville,
                'commercial_id' => $client->commercial_id,
            ],
        ]);
    }

    public function updated(Client $client): void
    {
        $changes = $client->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        if (array_key_exists('commercial_id', $changes)) {
            $this->logChangementCommercial($client);
        }

        if (array_key_exists('is_active', $changes)) {
            $this->logChangementStatut($client);
        }

        $changementsContact = array_intersect_key($changes, array_flip(self::CHAMPS_CONTACT));
        if (! empty($changementsContact)) {
            $this->logModificationContact($client, $changementsContact);
        }
    }

    private function logChangementCommercial(Client $client): void
    {
        $ancienId = $client->getOriginal('commercial_id');
        $nouveauId = $client->commercial_id;

        $ancien = $ancienId ? User::find($ancienId) : null;
        $nouveau = $nouveauId ? User::find($nouveauId) : null;

        $description = sprintf(
            'Commercial changé : %s → %s',
            $ancien ? trim($ancien->prenom.' '.$ancien->name) : 'Non assigné',
            $nouveau ? trim($nouveau->prenom.' '.$nouveau->name) : 'Non assigné'
        );

        if ($client->reassignment_comment) {
            $description .= ' — '.$client->reassignment_comment;
        }

        $client->activites()->create([
            'user_id'        => auth()->id(),
            'type_action'    => 'changement_commercial',
            'description'    => $description,
            'donnees_avant'  => ['commercial_id' => $ancienId, 'commercial_nom' => $ancien ? trim($ancien->prenom.' '.$ancien->name) : null],
            'donnees_apres'  => ['commercial_id' => $nouveauId, 'commercial_nom' => $nouveau ? trim($nouveau->prenom.' '.$nouveau->name) : null],
        ]);

        $client->reassignment_comment = null;
    }

    private function logChangementStatut(Client $client): void
    {
        $client->activites()->create([
            'user_id'       => auth()->id(),
            'type_action'   => 'changement_statut',
            'description'   => $client->is_active ? 'Client réactivé' : 'Client désactivé',
            'donnees_avant' => ['is_active' => $client->getOriginal('is_active')],
            'donnees_apres' => ['is_active' => $client->is_active],
        ]);
    }

    private function logModificationContact(Client $client, array $changements): void
    {
        $avant = [];
        $apres = [];

        foreach (array_keys($changements) as $champ) {
            $avant[$champ] = $client->getOriginal($champ);
            $apres[$champ] = $client->{$champ};
        }

        $client->activites()->create([
            'user_id'       => auth()->id(),
            'type_action'   => 'modification',
            'description'   => 'Informations modifiées : '.implode(', ', array_keys($changements)),
            'donnees_avant' => $avant,
            'donnees_apres' => $apres,
        ]);
    }
}
