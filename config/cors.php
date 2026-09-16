<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    // Domaines de production explicites, séparés par des virgules (ex: CORS_ALLOWED_ORIGINS=https://app.technitrack.com).
    // Vide par défaut : seuls les patterns ci-dessous (dev local) s'appliquent tant que rien n'est configuré.
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', '')))),

    // localhost/127.0.0.1 sur n'importe quel port : couvre les ports dynamiques de
    // `flutter run -d chrome`, sans ouvrir le CORS à '*' de façon permanente.
    'allowed_origins_patterns' => [
        '#^http://localhost:[0-9]+$#',
        '#^http://127\.0\.0\.1:[0-9]+$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
