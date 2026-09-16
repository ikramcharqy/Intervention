<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Gestion des sessions actives (table `sessions`, driver déjà configuré en
 * `database` — confirmé avant implémentation, aucune migration de driver
 * nécessaire) pour la page Sécurité du portail Client.
 */
class SessionSecurityService
{
    /**
     * Étape 4 du prompt "Vérification du contournement 2FA" : clients HTTP non-
     * navigateur les plus courants — une connexion authentifiée avec l'un de ces
     * user-agents mérite un signal visuel distinct (souvent un test/script, parfois un
     * signe d'automatisation non désirée).
     */
    private const CLIENTS_AUTOMATISES = ['curl', 'Postman', 'python-requests', 'HTTPie', 'okhttp', 'insomnia', 'Wget', 'axios'];

    public function sessionsActives(User $user, string $sessionCouranteId)
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($sessionCouranteId) {
                return (object) [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'appareil' => $this->analyserUserAgent($session->user_agent),
                    'est_client_automatise' => self::estClientAutomatise($session->user_agent),
                    'derniere_activite' => Carbon::createFromTimestamp($session->last_activity),
                    'est_courante' => $session->id === $sessionCouranteId,
                ];
            });
    }

    /**
     * Détection d'un client HTTP automatisé (curl, Postman, script...) plutôt qu'un
     * navigateur — statique et pure, réutilisable directement depuis les vues pour
     * l'historique de connexion (qui ne passe pas par sessionsActives()).
     */
    public static function estClientAutomatise(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }

        foreach (self::CLIENTS_AUTOMATISES as $client) {
            if (stripos($userAgent, $client) !== false) {
                return true;
            }
        }

        return false;
    }

    public function revoquerSession(User $user, string $sessionId): void
    {
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', $sessionId)
            ->delete();
    }

    /**
     * Déconnecte toutes les sessions de l'utilisateur sauf la session courante.
     */
    public function revoquerAutresSessions(User $user, string $sessionCouranteId): int
    {
        return DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('id', '!=', $sessionCouranteId)
            ->delete();
    }

    /**
     * Détection basique navigateur/OS à partir du user-agent — suffisant pour un
     * affichage lisible, pas besoin d'une librairie dédiée pour cet usage.
     */
    private function analyserUserAgent(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Appareil inconnu';
        }

        foreach (self::CLIENTS_AUTOMATISES as $client) {
            if (stripos($userAgent, $client) !== false) {
                return "Client API ({$client})";
            }
        }

        $navigateur = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'OPR/') => 'Opera',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') && !str_contains($userAgent, 'Chrome') => 'Safari',
            default => 'Navigateur inconnu',
        };

        $os = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => null,
        };

        return trim($navigateur . ($os ? " · {$os}" : ''));
    }
}
