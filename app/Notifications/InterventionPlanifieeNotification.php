<?php

namespace App\Notifications;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterventionPlanifieeNotification extends Notification
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
            'type'                => 'intervention_planifiee',
            'titre'               => 'Nouvelle intervention planifiée',
            'message'             => "L'intervention {$this->intervention->code_intervention} vous a été assignée.",
            'intervention_id'     => $this->intervention->id,
            'code_intervention'   => $this->intervention->code_intervention,
            'statut'              => $this->intervention->statut,
            'priorite'            => $this->intervention->priorite,
            'date_prevue_debut'   => $this->intervention->date_prevue_debut?->toISOString(),
            'date_prevue_fin'     => $this->intervention->date_prevue_fin?->toISOString(),
        ];
    }
}
