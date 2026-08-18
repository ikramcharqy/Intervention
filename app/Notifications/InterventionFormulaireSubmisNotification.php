<?php

namespace App\Notifications;

use App\Models\Intervention;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : Formulaire terrain soumis par le technicien.
 */
class InterventionFormulaireSubmisNotification extends Notification
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
            '📝 Formulaire Soumis',
            "Le formulaire terrain de l'intervention {$this->intervention->code_intervention} a été soumis.",
            '/interventions/' . $this->intervention->id
        );

        return [
            'type'              => 'formulaire_soumis',
            'titre'             => '📝 Formulaire Terrain Soumis',
            'message'           => "Le technicien a soumis le formulaire pour l'intervention {$this->intervention->code_intervention}.",
            'intervention_id'   => $this->intervention->id,
            'code_intervention' => $this->intervention->code_intervention,
            'statut'            => $this->intervention->statut,
        ];
    }
}
