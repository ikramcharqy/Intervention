<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention réaffectée à un autre technicien.
 */
class InterventionReaffecteeNotification extends Notification
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
            '🔄 Réaffectation d\'Intervention',
            "L'intervention {$this->intervention->code_intervention} vous a été réaffectée.",
            '/mobile'
        );

        return [
            'type'              => 'intervention_reaffectee',
            'titre'             => '🔄 Intervention Réaffectée',
            'message'           => "L'intervention {$this->intervention->code_intervention} vous a été réaffectée." . ($this->motif ? " Raison : {$this->motif}" : ''),
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'motif'             => $this->motif,
        ];
    }
}
