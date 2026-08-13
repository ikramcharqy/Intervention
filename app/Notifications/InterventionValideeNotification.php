<?php

namespace App\Notifications;

use App\Models\Intervention;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterventionValideeNotification extends Notification
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
            'type'              => 'intervention_validee',
            'titre'             => '✅ Intervention validée et clôturée',
            'message'           => "Félicitations ! L'intervention {$this->intervention->code_intervention} a été validée et clôturée par l'administrateur.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
            'date_validation'   => $this->intervention->date_validation?->toISOString(),
        ];
    }
}
