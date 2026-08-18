<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention modifiée par l'administrateur (date, priorité, etc.).
 */
class InterventionModifieeNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Intervention $intervention,
        public array $modifications = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $changesText = empty($this->modifications)
            ? 'Des informations ont été mises à jour.'
            : implode(', ', $this->modifications);

        app(PushNotificationService::class)->sendPushToUser(
            $notifiable,
            '✏️ Intervention Modifiée',
            "L'intervention {$this->intervention->code_intervention} a été mise à jour : {$changesText}",
            '/mobile'
        );

        return [
            'type'              => 'intervention_modifiee',
            'titre'             => '✏️ Intervention Mise à Jour',
            'message'           => "L'intervention {$this->intervention->code_intervention} a été modifiée : {$changesText}",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'modifications'     => $this->modifications,
        ];
    }
}
