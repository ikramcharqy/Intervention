<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention en retard (dépasse la date prévue de fin).
 */
class InterventionRetardNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Intervention $intervention,
        public int $minutesDeRetard = 0
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $delai = $this->minutesDeRetard > 60
            ? round($this->minutesDeRetard / 60, 1) . 'h'
            : $this->minutesDeRetard . ' min';

        app(PushNotificationService::class)->sendPushToUser(
            $notifiable,
            '⚠️ Intervention en Retard',
            "L'intervention {$this->intervention->code_intervention} dépasse le délai prévu de {$delai}.",
            '/interventions/' . $this->intervention->id
        );

        return [
            'type'              => 'intervention_retard',
            'titre'             => '⚠️ Intervention en Retard',
            'message'           => "L'intervention {$this->intervention->code_intervention} est en retard de {$delai}.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'minutes_de_retard' => $this->minutesDeRetard,
        ];
    }
}
