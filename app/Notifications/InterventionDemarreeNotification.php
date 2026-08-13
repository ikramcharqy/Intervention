<?php

namespace App\Notifications;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterventionDemarreeNotification extends Notification
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
            'type'              => 'intervention_demarree',
            'titre'             => 'Intervention démarrée',
            'message'           => "L'intervention {$this->intervention->code_intervention} a démarré en mode {$this->intervention->mode_suivi}.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
            'mode_suivi'        => $this->intervention->mode_suivi,
            'date_reelle_debut' => $this->intervention->date_reelle_debut?->toISOString(),
        ];
    }
}
