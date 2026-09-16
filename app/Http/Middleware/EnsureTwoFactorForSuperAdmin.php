<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 2FA obligatoire pour Super Admin et Admin (priorité sur les autres rôles — cf. audit
 * de sécurité de la Console Super Admin). Un utilisateur de ces rôles sans secret
 * confirmé est redirigé vers l'enrôlement ; un utilisateur enrôlé mais n'ayant pas
 * encore validé de code TOTP pour la session courante est redirigé vers le challenge.
 *
 * Commercial/Technicien/Client ne sont pas concernés par ce blocage dans cette passe —
 * la 2FA reste optionnelle pour eux (route two-factor.setup reste accessible à tous).
 */
class EnsureTwoFactorForSuperAdmin
{
    private const ENFORCED_ROLES = ['Super Admin', 'admin', 'Administrateur'];

    private const EXEMPT_ROUTES = [
        'two-factor.setup',
        'two-factor.confirm',
        'two-factor.recoveryCodes',
        'two-factor.challenge',
        'two-factor.verify',
        'two-factor.reset',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !$user->hasAnyRole(self::ENFORCED_ROLES)) {
            return $next($request);
        }

        if ($request->routeIs(self::EXEMPT_ROUTES)) {
            return $next($request);
        }

        if (!$user->two_factor_confirmed_at) {
            return redirect()->route('two-factor.setup');
        }

        if (!$request->session()->get('2fa_passed_at')) {
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
