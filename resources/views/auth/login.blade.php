<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion • TechniTrack Console</title>

    <!-- Soft UI Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <!-- Tailwind CSS Assets & Soft UI Custom Overlays -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --soft-bg: #f8f9fa;
            --soft-primary-start: #7928ca;
            --soft-primary-end: #cb0c9f;
            --soft-info-start: #2152ff;
            --soft-info-end: #21d4fd;
        }
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--soft-bg);
            color: #344767;
        }
        .soft-card {
            background-color: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 20px 27px 0 rgba(0,0,0,0.05);
        }
        .soft-gradient-primary {
            background-image: linear-gradient(310deg, var(--soft-primary-start) 0%, var(--soft-primary-end) 100%);
        }
        .soft-gradient-info {
            background-image: linear-gradient(310deg, var(--soft-info-start) 0%, var(--soft-info-end) 100%);
        }
        .soft-gradient-dark {
            background-image: linear-gradient(310deg, #141727 0%, #3a416f 100%);
        }
        .role-card {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.2s ease-in-out;
        }
        .role-card:hover {
            background: #ffffff;
            border-color: #cb0c9f;
            box-shadow: 0 10px 20px rgba(203, 12, 159, 0.1);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="h-full bg-[#f8f9fa] text-slate-700 antialiased flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 relative">

    <div class="w-full max-w-[460px] relative z-10 space-y-8 my-auto">

        <!-- Carte de Connexion Principale Soft UI -->
        <div class="soft-card p-8 sm:p-10 space-y-6">

            <!-- Logo & En-tête Soft UI -->
            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center gap-2 font-black text-lg">
                    <div class="w-9 h-9 rounded-xl soft-gradient-primary text-white flex items-center justify-center shadow-md">
                        <i class="fas fa-tools text-sm"></i>
                    </div>
                    <span class="text-slate-800 font-extrabold text-xl">TECHNITRACK <span class="text-pink-600">CONSOLE</span></span>
                </div>

                <h1 class="text-2xl font-black text-slate-800 tracking-tight pt-2">
                    Bienvenue
                </h1>
                <p class="text-xs text-slate-400 max-w-xs mx-auto leading-relaxed mb-0">
                    Connectez-vous à votre plateforme de gestion d'interventions terrain.
                </p>
            </div>

            <!-- Validation Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4 pt-2">
                @csrf

                <!-- Champ Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">EMAIL PROFESSIONNEL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i class="fas fa-envelope text-xs"></i>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="nom@entreprise.fr"
                               class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-semibold placeholder-slate-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                </div>

                <!-- Champ Mot de Passe -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">MOT DE PASSE</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i class="fas fa-lock text-xs"></i>
                        </span>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="w-full pl-11 pr-11 py-3.5 rounded-xl border border-slate-200 text-xs text-slate-800 font-semibold placeholder-slate-300 focus:ring-2 focus:ring-pink-500 focus:border-pink-500">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                </div>

                <!-- Rester Connecté & Mot de passe oublié -->
                <div class="flex items-center justify-between pt-2">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-pink-600 focus:ring-pink-500" name="remember">
                        <span class="ms-2 text-xs font-bold uppercase tracking-wider text-slate-400">SE SOUVENIR DE MOI</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs text-pink-600 hover:underline font-bold" href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Bouton de Connexion Soft UI Gradient -->
                <div class="pt-2">
                    <button type="submit" class="soft-gradient-primary w-full py-3.5 px-4 text-white text-xs font-black uppercase tracking-wider rounded-xl shadow-md flex items-center justify-center gap-2 transition hover:opacity-95">
                        <span>SE CONNECTER À LA CONSOLE</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
            </form>

            <!-- Accès Rapides Rôles Métier -->
            <div class="pt-6 border-t border-slate-100 space-y-3 text-center">
                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="flex-shrink mx-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider">ACCÈS DIRECTS RÉELS</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <div class="grid grid-cols-3 gap-3 pt-1">
                    <!-- Card Admin -->
                    <button type="button" onclick="fillCreds('adminShaMoh@intervention.ma')" class="role-card p-3 rounded-xl flex flex-col items-center justify-center gap-2">
                        <div class="w-8 h-8 rounded-lg soft-gradient-dark text-white flex items-center justify-center shadow-xs">
                            <i class="fas fa-user-shield text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-800">Admin</span>
                    </button>

                    <!-- Card Commercial -->
                    <button type="button" onclick="fillCreds('commercial@fieldflow.test')" class="role-card p-3 rounded-xl flex flex-col items-center justify-center gap-2">
                        <div class="w-8 h-8 rounded-lg soft-gradient-info text-white flex items-center justify-center shadow-xs">
                            <i class="fas fa-briefcase text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-800">Commercial</span>
                    </button>

                    <!-- Card Technicien -->
                    <button type="button" onclick="fillCreds('yassine.tech@test.com')" class="role-card p-3 rounded-xl flex flex-col items-center justify-center gap-2">
                        <div class="w-8 h-8 rounded-lg soft-gradient-primary text-white flex items-center justify-center shadow-xs">
                            <i class="fas fa-wrench text-xs"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-800">Technicien</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Links Soft UI -->
        <div class="text-center text-xs font-semibold text-slate-400 flex items-center justify-center gap-3">
            <a href="#" class="hover:text-slate-600 transition">Assistance Technique</a>
            <span>•</span>
            <a href="#" class="hover:text-slate-600 transition">Conditions Générales</a>
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
