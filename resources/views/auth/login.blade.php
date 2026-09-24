<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion • TechniTrack</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-technitrack-icon.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Assets Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .login-visual {
            background-image: linear-gradient(160deg, rgba(15, 23, 42, 0.88) 0%, rgba(15, 23, 42, 0.65) 45%, rgba(79, 70, 229, 0.55) 100%), url('https://images.pexels.com/photos/35072822/pexels-photo-35072822/free-photo-of-technician-working-inside-industrial-electrical-panel.jpeg?auto=compress&w=1260&h=750&dpr=1');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="h-full antialiased text-slate-900 bg-slate-50 flex flex-col min-h-screen selection:bg-indigo-500 selection:text-white">

    <!-- ===================== HEADER MINIMAL ===================== -->
    <header class="h-20 shrink-0 flex items-center justify-between px-4 sm:px-8 border-b border-slate-200 bg-white">
        <a href="{{ route('landing') }}">
            <img src="{{ asset('images/logo-technitrack-full.png') }}" alt="TechniTrack" class="h-11 sm:h-12 w-auto object-contain">
        </a>
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition">
            <x-icon name="arrow-right" class="w-3.5 h-3.5 rotate-180" />
            <span>Retour à l'accueil</span>
        </a>
    </header>

    <!-- ===================== CORPS DEUX COLONNES ===================== -->
    <div class="flex-1 grid lg:grid-cols-2">

        <!-- Colonne visuelle (masquée sur mobile) -->
        <div class="hidden lg:flex login-visual relative flex-col justify-end p-12 text-white">
            <span class="inline-flex items-center gap-1.5 self-start px-3 py-1 rounded-full bg-white/10 border border-white/20 text-[11px] font-bold uppercase tracking-wider mb-5">
                <x-icon name="bolt-solid" class="w-3 h-3 text-amber-300" />
                Plateforme marocaine de gestion technique
            </span>
            <h2 class="text-3xl font-extrabold tracking-tight leading-tight max-w-md">
                La gestion d'interventions, simplifiée
            </h2>
            <p class="mt-4 text-sm text-slate-200 max-w-sm leading-relaxed">
                TechniTrack accompagne les entreprises marocaines dans le suivi de leurs interventions techniques et chantiers, en temps réel.
            </p>
        </div>

        <!-- Colonne formulaire -->
        <div class="flex items-center justify-center p-4 sm:p-8 py-10">
            <div class="w-full max-w-[430px] space-y-6">

                <!-- Carte de Connexion -->
                <div class="ui-card p-7 sm:p-9 space-y-6 bg-white border border-slate-200/90 shadow-sm rounded-2xl"
                     x-data="{ space: '{{ request('type') === 'client' ? 'client' : 'employe' }}' }">

                    <!-- Sélecteur Espace Client / Espace Employé -->
                    <div class="grid grid-cols-2 gap-1 p-1 bg-slate-100 rounded-xl">
                        <button type="button" @click="space = 'client'"
                                :class="space === 'client' ? 'bg-white shadow-sm text-emerald-700' : 'text-slate-500 hover:text-slate-700'"
                                class="inline-flex items-center justify-center gap-1.5 py-2 rounded-lg text-xs font-bold transition">
                            <x-icon name="user" class="w-3.5 h-3.5" />
                            <span>Espace Client</span>
                        </button>
                        <button type="button" @click="space = 'employe'"
                                :class="space === 'employe' ? 'bg-white shadow-sm text-indigo-700' : 'text-slate-500 hover:text-slate-700'"
                                class="inline-flex items-center justify-center gap-1.5 py-2 rounded-lg text-xs font-bold transition">
                            <x-icon name="briefcase" class="w-3.5 h-3.5" />
                            <span>Espace Employé</span>
                        </button>
                    </div>

                    <!-- En-tête -->
                    <div class="text-center space-y-1.5">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                            <span x-show="space === 'client'" x-cloak>Connexion Espace Client</span>
                            <span x-show="space === 'employe'" x-cloak>Connexion Espace Employé</span>
                        </h1>
                        <p class="text-xs text-slate-500">
                            <span x-show="space === 'client'" x-cloak>Suivez vos chantiers et interventions en temps réel</span>
                            <span x-show="space === 'employe'" x-cloak>Accédez à votre espace de gestion TechniTrack</span>
                        </p>
                    </div>

                    <!-- Validation Status Flash -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-4 pt-1">
                        @csrf

                        <!-- Champ Email -->
                        <div class="space-y-1.5">
                            <label for="email" class="ui-label flex items-center gap-1.5 text-[10px]">
                                <x-icon name="mail" class="w-3 h-3 text-slate-400" />
                                <span>Email Professionnel</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <x-icon name="mail" class="w-3.5 h-3.5" />
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                       placeholder="nom@entreprise.com"
                                       class="ui-input pl-10 text-xs py-2.5">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-600" />
                        </div>

                        <!-- Champ Mot de Passe -->
                        <div class="space-y-1.5">
                            <label for="password" class="ui-label flex items-center gap-1.5 text-[10px]">
                                <x-icon name="lock-closed" class="w-3 h-3 text-slate-400" />
                                <span>Mot de Passe</span>
                            </label>
                            <div class="relative" x-data="{ show: false }">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <x-icon name="lock-closed" class="w-3.5 h-3.5" />
                                </span>
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="ui-input pl-10 pr-10 text-xs py-2.5">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                                    <x-icon name="eye" class="w-3.5 h-3.5" x-show="!show" />
                                    <x-icon name="eye-slash" class="w-3.5 h-3.5" x-show="show" x-cloak />
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-600" />
                        </div>

                        <!-- Rester Connecté & Mot de passe oublié -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                                <input id="remember_me" type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" name="remember">
                                <span class="ms-2 text-xs text-slate-600 group-hover:text-slate-900 transition">Se souvenir de moi</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold transition" href="{{ route('password.request') }}">
                                    Mot de passe oublié ?
                                </a>
                            @endif
                        </div>

                        <!-- Bouton de Connexion -->
                        <div class="pt-2">
                            <button type="submit" class="ui-btn ui-btn-primary w-full py-2.5 text-xs font-bold uppercase tracking-wider shadow-sm">
                                <span>Se connecter</span>
                                <x-icon name="arrow-right" class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer Info & Sécurité -->
                <div class="text-center space-y-2">
                    <div class="flex items-center justify-center gap-4 text-[11px] text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <x-icon name="lock-closed" class="w-3 h-3 text-emerald-600" />
                            Connexion Sécurisée SSL
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Serveur Opérationnel
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-400">
                        © {{ date('Y') }} {{ config('app.name', 'TechniTrack') }} — Tous droits réservés.
                    </p>
                </div>

            </div>
        </div>
    </div>

</body>
</html>
