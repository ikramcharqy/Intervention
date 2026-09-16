<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Étape 3.3 : timeout d'inactivité réduit spécifiquement pour le rôle Super Admin.
 * `SESSION_LIFETIME` est global à l'application (config/session.php) et ne peut pas
 * être scopé par rôle nativement — ce middleware applique donc un délai plus court
 * (15 min) en comparant `last_activity` de la table `sessions` (driver `database`)
 * à l'horloge courante, indépendamment du GC probabiliste de Laravel.
 */
class EnforceSuperAdminSessionTimeout
{
    private const TIMEOUT_MINUTES = 15;

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->hasRole('Super Admin')) {
            $lastActivity = DB::table('sessions')->where('id', $request->session()->getId())->value('last_activity');

            if ($lastActivity && (time() - (int) $lastActivity) > self::TIMEOUT_MINUTES * 60) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Session expirée par inactivité (délai de 15 minutes appliqué au rôle Super Admin).');
            }
        }

        return $next($request);
    }
}
