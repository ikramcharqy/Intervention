<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Technicien a accepté l'intervention.
 */
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
        app(PushNotificationService::class)->sendPushToUser(
            $notifiable,
            '✅ Intervention Acceptée',
            "L'intervention {$this->intervention->code_intervention} a été acceptée.",
            '/mobile'
        );

        return [
            'type'              => 'intervention_acceptee',
            'titre'             => '✅ Intervention Acceptée',
            'message'           => "L'intervention {$this->intervention->code_intervention} a été acceptée et démarrera bientôt.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
        ];
    }
}
