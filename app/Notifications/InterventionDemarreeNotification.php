<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Intervention démarrée sur le terrain.
 */
class InterventionDemarreeNotification extends Notification
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
            '🚀 Intervention Démarrée',
            "Le technicien a démarré l'intervention {$this->intervention->code_intervention}.",
            '/interventions/' . $this->intervention->id
        );

        return [
            'type'              => 'intervention_demarree',
            'titre'             => '🚀 Intervention En Cours',
            'message'           => "L'intervention {$this->intervention->code_intervention} vient de démarrer.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
            'date_reelle_debut' => $this->intervention->date_reelle_debut?->toISOString(),
        ];
    }
}
