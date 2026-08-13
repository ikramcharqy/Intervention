<?php

namespace App\Notifications;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterventionFormulaireSubmisNotification extends Notification
{
    use Queueable;

    public function __construct(public Intervention $intervention) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'                    => 'formulaire_soumis',
            'titre'                   => 'Formulaire soumis — En attente de validation',
            'message'                 => "Le formulaire de l'intervention {$this->intervention->code_intervention} a été soumis avec succès. En attente de validation par l'administrateur.",
            'intervention_id'         => $this->intervention->id,
            'code_intervention'       => $this->intervention->code_intervention,
            'statut'                  => $this->intervention->statut,
            'date_soumission'         => $this->intervention->date_soumission_validation?->toISOString(),
        ];
    }
}
