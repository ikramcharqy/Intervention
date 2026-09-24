<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TechniTrack') }} — Authentification</title>

        <link rel="icon" type="image/png" href="{{ asset('images/logo-technitrack-icon.png') }}">

        <!-- Fonts & Icons -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #0f172a; }
        </style>
    </head>
    <body class="h-full antialiased text-slate-900 bg-slate-50 flex flex-col items-center justify-center min-h-screen p-4 sm:p-6">
        <div class="w-full max-w-[430px] space-y-6 my-auto">
            <div class="text-center">
                <a href="/" class="inline-flex items-center justify-center">
                    <img src="{{ asset('images/logo-technitrack-full.png') }}" alt="TechniTrack" class="h-9 w-auto object-contain">
                </a>
            </div>

            <div class="ui-card p-6 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-2xl">
                {{ $slot }}
            </div>

            <div class="text-center text-xs text-slate-500">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 transition font-semibold">
                    ← Retour à la connexion
                </a>
            </div>
        </div>
    </body>
</html>
