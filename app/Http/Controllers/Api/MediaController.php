<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Sert les fichiers du disque `public` à travers l'application Laravel — donc
 * soumis au middleware CORS global (`api/*`, déjà configuré) — plutôt que via
 * le lien symbolique public/storage, servi statiquement par le serveur web
 * SANS jamais passer par Laravel. Sous `php artisan serve`, le routeur
 * intégré (vendor/laravel/framework/.../server.php) court-circuite
 * entièrement l'application dès qu'un fichier existe physiquement sous
 * public/ — donc ajouter 'storage/*' à config/cors.php n'aurait aucun effet,
 * ces requêtes n'atteignant jamais le middleware CORS. Le rendu CanvasKit de
 * Flutter Web récupère les images via `fetch()`, soumis aux règles CORS du
 * navigateur — contrairement à une balise `<img>` HTML classique (utilisée
 * par les vues Blade), qui n'a jamais eu ce problème et reste inchangée.
 */
class MediaController extends BaseApiController
{
    public function show(Request $request, string $path): Response
    {
        $normalized = str_replace('\\', '/', $path);

        // Empêche toute évasion du disque public (ex: ../../.env).
        if (str_contains($normalized, '..')) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($normalized)) {
            abort(404);
        }

        $mime = Storage::disk('public')->mimeType($normalized) ?: 'application/octet-stream';

        return response(Storage::disk('public')->get($normalized), 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
