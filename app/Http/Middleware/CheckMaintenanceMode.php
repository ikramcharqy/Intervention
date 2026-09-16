<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Étape 5 du prompt "Corrections critiques Paramètres" : le bascule "Mode Maintenance"
 * de la page Paramètres était jusqu'ici purement décoratif (persisté en base mais jamais
 * lu par aucun middleware) — ce middleware lui donne un effet réel : verrouille l'accès
 * de tout utilisateur qui n'est pas Super Admin. Le compte Super Admin reste toujours en
 * mesure de se connecter et d'atteindre la page Paramètres pour désactiver le mode, quelle
 * que soit la configuration, pour ne jamais bloquer totalement la plateforme (Étape 5.3).
 */
class CheckMaintenanceMode
{
    private const ROUTES_TOUJOURS_ACCESSIBLES = [
        'login',
        'logout',
        // Le Super Admin doit pouvoir s'authentifier normalement (login + 2FA) pour
        // atteindre la page Paramètres et désactiver le mode.
        'two-factor.setup',
        'two-factor.confirm',
        'two-factor.challenge',
        'two-factor.verify',
        'two-factor.recoveryCodes',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (!Setting::get('maintenance_mode', false)) {
            return $next($request);
        }

        $user = auth()->user();

        if ($user && $user->hasRole('Super Admin')) {
            return $next($request);
        }

        if ($request->routeIs(self::ROUTES_TOUJOURS_ACCESSIBLES)) {
            return $next($request);
        }

        $message = Setting::get('maintenance_message') ?: "La plateforme est actuellement en maintenance. Merci de réessayer ultérieurement.";

        return response()->view('maintenance', ['message' => $message], 503);
    }
}
