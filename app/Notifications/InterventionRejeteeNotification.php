<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention rejetée par l'administrateur — le technicien retourne sur le terrain.
 */
class InterventionRejeteeNotification extends Notification
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
            '🔁 Rapport Rejeté — Action Requise',
            "L'administrateur a rejeté le rapport de l'intervention {$this->intervention->code_intervention}." . ($this->motif ? " Motif : {$this->motif}" : ''),
            '/mobile'
        );

        return [
            'type'              => 'intervention_rejetee',
            'titre'             => '🔁 Rapport Rejeté par l\'Administrateur',
            'message'           => "L'intervention {$this->intervention->code_intervention} nécessite des corrections." . ($this->motif ? " Motif : {$this->motif}" : ''),
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'motif'             => $this->motif,
        ];
    }
}
