<?php

namespace App\Notifications;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterventionAccepteeNotification extends Notification
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
            'type'              => 'intervention_acceptee',
            'titre'             => 'Intervention acceptée',
            'message'           => "Vous avez accepté l'intervention {$this->intervention->code_intervention}. Elle est prête à démarrer.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
        ];
    }
}
