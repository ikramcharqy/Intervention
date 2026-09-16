<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Étape 5 : rappel manuel envoyé par un Super Admin à un administrateur dont
 * l'invitation est acceptée (compte utilisable) mais dont la 2FA obligatoire n'est
 * toujours pas activée. Réutilise le canal 'mail' déjà configuré pour l'application
 * (même Mailer que Password::sendResetLink() utilisé par AdminInvitationService) —
 * aucune nouvelle infrastructure d'envoi, seulement un nouveau message.
 */
class RappelActivation2FANotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rappel : activez votre authentification à deux facteurs')
            ->greeting("Bonjour {$notifiable->prenom},")
            ->line("Votre compte administrateur TechniTrack ({$notifiable->email}) n'a pas encore activé l'authentification à deux facteurs (2FA), pourtant obligatoire pour votre rôle.")
            ->line("Merci de vous connecter dès que possible pour finaliser cette étape et sécuriser votre accès.")
            ->action('Se connecter', route('login'))
            ->line("Si vous rencontrez une difficulté, contactez votre Super Admin.");
    }
}
