<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Technicien a refusé l'intervention.
 */
class InterventionRefuseeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Intervention $intervention,
        public string $motif = ''
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        app(PushNotificationService::class)->sendPushToUser(
            $notifiable,
            '❌ Intervention Refusée',
            "Le technicien a refusé l'intervention {$this->intervention->code_intervention}." . ($this->motif ? " Motif : {$this->motif}" : ''),
            '/interventions/' . $this->intervention->id
        );

        return [
            'type'              => 'intervention_refusee',
            'titre'             => '❌ Intervention Refusée par le Technicien',
            'message'           => "L'intervention {$this->intervention->code_intervention} a été refusée." . ($this->motif ? " Motif : {$this->motif}" : ''),
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'motif'             => $this->motif,
        ];
    }
}
