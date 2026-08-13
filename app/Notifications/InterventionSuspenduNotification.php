<?php

namespace App\Notifications;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterventionSuspenduNotification extends Notification
{
    use Queueable;

    public function __construct(public Intervention $intervention, public string $motif = '') {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type'              => 'intervention_suspendue',
            'titre'             => 'Intervention mise en pause',
            'message'           => "L'intervention {$this->intervention->code_intervention} a été mise en pause." . ($this->motif ? " Motif : {$this->motif}" : ''),
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
            'motif'             => $this->motif,
        ];
    }
}
