<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notification générique du portail Client — persistée dans la table
 * `notifications` déjà utilisée par le Technicien/l'API (Illuminate\Notifications),
 * pour bénéficier nativement de read_at, markAsRead(), unreadNotifications() etc.
 * Un seul type de classe, paramétré, plutôt qu'une classe par événement (le contenu
 * varie, la structure de stockage/affichage est identique pour tous).
 */
class ClientPortalNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $titre,
        public string $message,
        public string $lienRoute,
        public array $lienParams = [],
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'titre' => $this->titre,
            'message' => $this->message,
            'lien_route' => $this->lienRoute,
            'lien_params' => $this->lienParams,
        ];
    }
}
