<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion • TechniTrack</title>

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

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
    </style>
</head>
<body class="h-full antialiased text-slate-900 bg-slate-50 flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 relative selection:bg-indigo-500 selection:text-white">

    <div class="w-full max-w-[430px] space-y-6 my-auto">

        <!-- Carte de Connexion Principale Executive Light -->
        <div class="ui-card p-7 sm:p-9 space-y-6 bg-white border border-slate-200/90 shadow-sm rounded-2xl">

            <!-- Logo & En-tête -->
            <div class="text-center space-y-2.5">
                <div class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-indigo-600 text-white shadow-sm mb-1">
                    <i class="fas fa-layer-group text-base"></i>
                </div>

                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                        Techni<span class="text-indigo-600">Track</span>
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Plateforme de Gestion des Interventions & Chantiers
                    </p>
                </div>
            </div>

            <!-- Validation Status Flash -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4 pt-1">
                @csrf

                <!-- Champ Email -->
                <div class="space-y-1.5">
                    <label for="email" class="ui-label flex items-center gap-1.5 text-[10px]">
                        <i class="fas fa-envelope text-slate-400"></i>
                        <span>Email Professionnel</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fas fa-at text-xs"></i>
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
                        <i class="fas fa-lock text-slate-400"></i>
                        <span>Mot de Passe</span>
                    </label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fas fa-key text-xs"></i>
                        </span>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="ui-input pl-10 pr-10 text-xs py-2.5">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <i class="fas text-xs" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
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
                        <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </button>
                </div>
            </form>

            <!-- Accès Rapides Rôles Métier -->
            <div class="pt-5 border-t border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Accès démo certifiés</span>
                    <span class="text-[10px] text-slate-400">Clic auto-remplissage</span>
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <!-- Admin -->
                    <button type="button" onclick="fillCreds('adminShaMoh@intervention.ma')" 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition group flex flex-col items-center gap-1.5">
                        <div class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 group-hover:text-indigo-700 transition">Admin</span>
                    </button>

                    <!-- Commercial -->
                    <button type="button" onclick="fillCreds('commercial@fieldflow.test')" 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-emerald-300 hover:bg-emerald-50/40 transition group flex flex-col items-center gap-1.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 group-hover:text-emerald-700 transition">Commercial</span>
                    </button>

                    <!-- Technicien -->
                    <button type="button" onclick="fillCreds('yassine.tech@test.com')" 
                            class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-amber-300 hover:bg-amber-50/40 transition group flex flex-col items-center gap-1.5">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-xs">
                            <i class="fas fa-hard-hat"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-700 group-hover:text-amber-700 transition">Technicien</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Info & Sécurité -->
        <div class="text-center space-y-2">
            <div class="flex items-center justify-center gap-4 text-[11px] text-slate-500">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-lock text-emerald-600"></i>
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

    <script>
        function fillCreds(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</body>
</html>
