<?php

namespace App\Observers;

use App\Models\Prospect;

class ProspectObserver
{
    private const CHAMPS_CONTACT = [
        'nom_entreprise',
        'nom_contact',
        'telephone',
        'email',
        'adresse',
    ];

    public function created(Prospect $prospect): void
    {
        $prospect->activites()->create([
            'user_id' => auth()->id(),
            'type_action' => 'creation',
            'description' => "Prospect créé : {$prospect->nom_entreprise}",
            'donnees_apres' => [
                'nom_entreprise' => $prospect->nom_entreprise,
                'statut' => $prospect->statut,
                'commercial_id' => $prospect->commercial_id,
            ],
        ]);
    }

    public function updated(Prospect $prospect): void
    {
        $changes = $prospect->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        if (array_key_exists('statut', $changes)) {
            $this->logChangementStatut($prospect);
        }

        if (array_key_exists('notes', $changes)) {
            $this->logNouvelleNote($prospect);
        }

        if (array_key_exists('prochaine_action_date', $changes) || array_key_exists('prochaine_action_description', $changes)) {
            $this->logProchaineAction($prospect);
        }

        $changementsContact = array_intersect_key($changes, array_flip(self::CHAMPS_CONTACT));
        if (! empty($changementsContact)) {
            $this->logModificationContact($prospect, $changementsContact);
        }
    }

    private function logChangementStatut(Prospect $prospect): void
    {
        $ancien = $prospect->getOriginal('statut');
        $nouveau = $prospect->statut;

        $prospect->activites()->create([
            'user_id' => auth()->id(),
            'type_action' => $nouveau === 'Converti' ? 'conversion' : 'changement_statut',
            'description' => "Changement de statut : {$ancien} → {$nouveau}",
            'donnees_avant' => ['statut' => $ancien],
            'donnees_apres' => ['statut' => $nouveau],
        ]);
    }

    private function logNouvelleNote(Prospect $prospect): void
    {
        $notes = $prospect->notes ?? [];
        $derniere = end($notes) ?: null;

        $prospect->activites()->create([
            'user_id' => auth()->id(),
            'type_action' => 'note',
            'description' => 'Note ajoutée' . ($derniere ? ' : ' . \Illuminate\Support\Str::limit($derniere['content'] ?? '', 80) : ''),
            'donnees_apres' => $derniere,
        ]);
    }

    private function logProchaineAction(Prospect $prospect): void
    {
        if (! $prospect->prochaine_action_date) {
            return;
        }

        $prospect->activites()->create([
            'user_id' => auth()->id(),
            'type_action' => 'prochaine_action',
            'description' => sprintf(
                'Prochaine action programmée le %s%s',
                $prospect->prochaine_action_date->format('d/m/Y'),
                $prospect->prochaine_action_description ? ' — ' . $prospect->prochaine_action_description : ''
            ),
            'donnees_apres' => [
                'date' => $prospect->prochaine_action_date->format('Y-m-d'),
                'description' => $prospect->prochaine_action_description,
            ],
        ]);
    }

    private function logModificationContact(Prospect $prospect, array $changements): void
    {
        $avant = [];
        $apres = [];

        foreach (array_keys($changements) as $champ) {
            $avant[$champ] = $prospect->getOriginal($champ);
            $apres[$champ] = $prospect->{$champ};
        }

        $prospect->activites()->create([
            'user_id' => auth()->id(),
            'type_action' => 'modification',
            'description' => 'Informations modifiées : ' . implode(', ', array_keys($changements)),
            'donnees_avant' => $avant,
            'donnees_apres' => $apres,
        ]);
    }
}
