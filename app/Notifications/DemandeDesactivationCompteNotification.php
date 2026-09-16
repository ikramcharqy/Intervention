<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification : un employé (Commercial/Technicien) demande la désactivation
 * de son propre compte depuis sa page Profil (pas d'auto-suppression en
 * self-service pour ces rôles — seul un Admin/Super Admin peut désactiver
 * un compte employé, via la gestion des utilisateurs).
 */
class DemandeDesactivationCompteNotification extends Notification
{
    use Queueable;

    public function __construct(public User $demandeur) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'demande_desactivation_compte',
            'titre' => '⚠️ Demande de désactivation de compte',
            'message' => "{$this->demandeur->prenom} {$this->demandeur->name} ({$this->demandeur->email}) a demandé la désactivation de son compte.",
            'user_id' => $this->demandeur->id,
        ];
    }
}
