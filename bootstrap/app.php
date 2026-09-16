<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use App\Http\Middleware\CheckMaintenanceMode;
use App\Http\Middleware\EnforceSuperAdminSessionTimeout;
use App\Http\Middleware\EnsureTwoFactorForSuperAdmin;
use App\Http\Middleware\PreventBackHistoryCache;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        channels: __DIR__.'/../routes/channels.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Étape 5 : Mode Maintenance — sur le groupe 'web' entier (pas seulement
        // /superadmin/*) puisque la maintenance doit verrouiller TOUS les accès non-admin,
        // pas uniquement la console Super Admin.
        //
        // Correction (bug 2FA non appliquée pour le rôle 'admin') : EnsureTwoFactorForSuperAdmin
        // n'était attachée qu'au groupe de routes /superadmin/* (role:Super Admin uniquement).
        // Le rôle 'admin' — pourtant dans ENFORCED_ROLES — n'a aucune route protégée par ce
        // préfixe (AuthenticatedSessionController::store le redirige vers le /dashboard générique,
        // seulement ['auth','verified']) : son 2FA n'était donc JAMAIS vérifiée après connexion.
        // Déplacée ici, sur tout le groupe 'web', pour que le contrôle s'applique quelle que soit
        // la page où atterrit un compte Super Admin/admin — le middleware est déjà un no-op pour
        // tout autre rôle (hasAnyRole(ENFORCED_ROLES) sinon $next() immédiat), donc sans impact sur
        // les autres rôles ni sur les pages invité (login/register : $user est null à ce stade).
        $middleware->web(append: [CheckMaintenanceMode::class, EnsureTwoFactorForSuperAdmin::class]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'superadmin.timeout' => EnforceSuperAdminSessionTimeout::class,
            'superadmin.2fa' => EnsureTwoFactorForSuperAdmin::class,
            'no-back-cache' => PreventBackHistoryCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
