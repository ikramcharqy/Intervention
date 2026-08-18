<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention suspendue (en pause temporaire).
 */
class InterventionSuspenduNotification extends Notification
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
            '⏸️ Intervention Suspendue',
            "L'intervention {$this->intervention->code_intervention} a été mise en pause." . ($this->motif ? " Motif : {$this->motif}" : ''),
            '/mobile'
        );

        return [
            'type'              => 'intervention_suspendue',
            'titre'             => '⏸️ Intervention Mise en Pause',
            'message'           => "L'intervention {$this->intervention->code_intervention} a été suspendue." . ($this->motif ? " Motif : {$this->motif}" : ''),
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
            'motif'             => $this->motif,
        ];
    }
}
