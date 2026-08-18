<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention validée et clôturée par l'administrateur.
 */
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
        app(PushNotificationService::class)->sendPushToUser(
            $notifiable,
            '🏆 Intervention Validée !',
            "L'intervention {$this->intervention->code_intervention} a été validée et officiellement clôturée.",
            '/mobile'
        );

        return [
            'type'              => 'intervention_validee',
            'titre'             => '🏆 Intervention Validée & Clôturée',
            'message'           => "L'intervention {$this->intervention->code_intervention} a été validée par l'administrateur.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
        ];
    }
}
