<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Empêche toute mise en cache (HTTP standard ET bfcache navigateur) des pages
 * d'authentification/2FA — leur contenu dépend d'un état serveur qui change
 * (secret généré, statut de confirmation, session active) ; sans ces
 * en-têtes, Chrome peut réafficher une version figée après une veille ou un
 * retour arrière sans jamais recontacter le serveur pour connaître l'état
 * réel (setup vs challenge).
 *
 * `no-store` est l'en-tête précis qui désactive le bfcache — `no-cache` seul
 * ne le fait pas (il autorise toujours la mise en bfcache, juste avec
 * revalidation). Les trois en-têtes sont posés ensemble pour la compatibilité
 * navigateurs anciens/proxys intermédiaires.
 *
 * Appliqué uniquement aux routes login/2FA/logout (cf. bootstrap/app.php) —
 * jamais globalement, pour ne pas dégrader le cache des pages non sensibles.
 */
class PreventBackHistoryCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
