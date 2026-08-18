<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Envoie une notification Push PWA native au téléphone/navigateur de l'utilisateur.
     */
    public function sendPushToUser(User $user, string $title, string $body, string $actionUrl = '/mobile'): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        if ($subscriptions->isEmpty()) {
            Log::info("PushNotification: Aucun abonnement Push trouvé pour l'utilisateur #{$user->id} ({$user->name})");
            return;
        }

        $payload = json_encode([
            'title'     => $title,
            'body'      => $body,
            'url'       => $actionUrl,
            'timestamp' => now()->toIso8601String(),
            'icon'      => '/icon-192.png',
            'badge'     => '/badge.png',
        ]);

        foreach ($subscriptions as $sub) {
            $this->dispatchWebPush($sub, $payload);
        }
    }

    /**
     * Transmet le payload Web Push vers l'endpoint de la souscription.
     */
    protected function dispatchWebPush(PushSubscription $sub, string $payload): void
    {
        try {
            // Standard WebPush payload delivery or HTTP POST to subscription endpoint
            $ch = curl_init($sub->endpoint);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'TTL: 86400',
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // Si l'endpoint renvoie 410 (Gone) ou 404 (Not Found), la souscription a expiré
            if (in_array($statusCode, [404, 410])) {
                $sub->delete();
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de la Web Push Notification : " . $e->getMessage());
        }
    }
}
