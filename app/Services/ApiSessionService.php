<?php

namespace App\Services;

use App\Models\LoginHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Historique de connexion et gestion des jetons Sanctum ("sessions actives")
 * pour les clients authentifiés par jeton (app mobile Technicien) — distinct
 * de SessionSecurityService, qui opère sur la table `sessions` (cookies web)
 * et n'a aucun rapport avec les jetons Sanctum utilisés par l'app mobile
 * (confirmé : aucune des deux implémentations ne peut être réutilisée pour
 * l'autre canal d'authentification).
 */
class ApiSessionService
{
    /**
     * Journalise une connexion API — l'événement `Illuminate\Auth\Events\Login`
     * (qui alimente LoginHistory côté web, cf. AppServiceProvider) ne se
     * déclenche jamais pour l'authentification par jeton
     * (Api\AuthController::login ne passe jamais par Auth::attempt()) ; sans
     * cet appel explicite, LoginHistory resterait vide pour tout compte
     * n'utilisant que l'app mobile.
     */
    public function enregistrerConnexion(User $user, Request $request): void
    {
        LoginHistory::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_in_at' => now(),
        ]);
    }

    public function historiqueConnexions(User $user, int $limit = 20): Collection
    {
        return LoginHistory::where('user_id', $user->id)
            ->orderByDesc('logged_in_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Jetons Sanctum actifs de l'utilisateur ("appareils connectés") — chacun
     * correspond à une connexion `POST /api/login` distincte (un jeton par
     * appel à `createToken()`, aucune notion de session partagée entre
     * appareils).
     */
    public function appareilsActifs(User $user, ?int $currentTokenId): Collection
    {
        return $user->tokens()
            ->orderByDesc('last_used_at')
            ->get()
            ->map(fn ($token) => [
                'id' => $token->id,
                'nom' => $token->name,
                'derniere_utilisation' => $token->last_used_at,
                'cree_le' => $token->created_at,
                'est_actuel' => $token->id === $currentTokenId,
            ]);
    }

    /**
     * Révoque un jeton précis appartenant à l'utilisateur — jamais celui d'un
     * autre utilisateur (vérifié via la relation `tokens()`, pas une requête
     * directe sur `personal_access_tokens`).
     */
    public function revoquerAppareil(User $user, int $tokenId): bool
    {
        $token = $user->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return false;
        }

        $token->delete();

        return true;
    }
}
