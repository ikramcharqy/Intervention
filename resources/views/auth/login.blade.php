<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Connexion • InterventionPRO</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
        }
        .login-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        }
        .soft-input {
            background: #f1f3f9;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .soft-input:focus {
            background: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .btn-primary-blue {
            background: #0256d0;
            transition: all 0.2s ease;
        }
        .btn-primary-blue:hover {
            background: #0143a5;
            box-shadow: 0 8px 20px rgba(2, 86, 208, 0.3);
        }
        .role-card {
            background: #fafafc;
            border: 1px solid #f0f1f6;
            transition: all 0.2s ease;
        }
        .role-card:hover {
            background: #ffffff;
            border-color: #0256d0;
            box-shadow: 0 10px 25px rgba(2, 86, 208, 0.08);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased flex flex-col items-center justify-center min-h-screen p-4 sm:p-6 relative">

    <div class="w-full max-w-[460px] relative z-10 space-y-8 my-auto">

        <!-- Carte de Connexion Principale -->
        <div class="login-card p-8 sm:p-10 space-y-6">

            <!-- Logo & En-tête -->
            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center gap-2 text-[#0256d0] font-black text-lg">
                    <div class="w-7 h-7 rounded-lg bg-[#0256d0] text-white flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span>Intervention<strong class="text-slate-900">PRO</strong></span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight pt-2">
                    Connexion
                </h1>
                <p class="text-xs text-slate-500 max-w-xs mx-auto leading-relaxed">
                    Accédez à votre plateforme de gestion d'interventions professionnelles.
                </p>
            </div>

            <!-- Validation Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-4 pt-2">
                @csrf

                <!-- Champ Email -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400">EMAIL PROFESSIONNEL</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        </span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               placeholder="nom@entreprise.fr"
                               class="soft-input w-full pl-11 pr-4 py-3.5 rounded-2xl text-xs text-slate-900 placeholder-slate-400 font-medium focus:outline-none">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1 text-xs text-rose-500" />
                </div>

                <!-- Champ Mot de Passe -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-[11px] font-extrabold uppercase tracking-wider text-slate-400">MOT DE PASSE</label>
                    <div class="relative" x-data="{ show: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        </span>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="current-password"
                               placeholder="••••••••"
                               class="soft-input w-full pl-11 pr-11 py-3.5 rounded-2xl text-xs text-slate-900 placeholder-slate-400 font-medium focus:outline-none">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-xs text-rose-500" />
                </div>

                <!-- Rester Connecté & Mot de passe oublié -->
                <div class="flex items-center justify-between pt-2">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="w-4 h-4 rounded border-slate-300 text-[#0256d0] focus:ring-[#0256d0]" name="remember">
                        <span class="ms-2 text-xs font-bold uppercase tracking-wider text-slate-500">RESTER CONNECTÉ</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs text-[#0256d0] hover:underline font-bold" href="{{ route('password.request') }}">
                            Mot de passe oublié ?
                        </a>
                    @endif
                </div>

                <!-- Bouton de Connexion BLEU -->
                <div class="pt-2">
                    <button type="submit" class="btn-primary-blue w-full py-4 px-4 text-white text-xs font-extrabold uppercase tracking-wider rounded-2xl shadow-lg flex items-center justify-center gap-2">
                        <span>ACCÉDER À MON ESPACE</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </button>
                </div>
            </form>

            <!-- Accès Rapides (3 Cartes Rôles comme la photo) -->
            <div class="pt-6 border-t border-slate-100 space-y-3 text-center">
                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="flex-shrink mx-4 text-[10px] uppercase font-bold text-slate-400 tracking-wider">ACCÈS RAPIDE</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <div class="grid grid-cols-3 gap-3 pt-1">
                    <!-- Card Admin -->
                    <button type="button" onclick="fillCreds('adminShaMoh@intervention.ma')" class="role-card p-3 rounded-2xl flex flex-col items-center justify-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Admin</span>
                    </button>

                    <!-- Card Commercial -->
                    <button type="button" onclick="fillCreds('commercial@fieldflow.test')" class="role-card p-3 rounded-2xl flex flex-col items-center justify-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Commercial</span>
                    </button>

                    <!-- Card Technicien -->
                    <button type="button" onclick="fillCreds('yassine.tech@test.com')" class="role-card p-3 rounded-2xl flex flex-col items-center justify-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Technicien</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="text-center text-xs font-semibold text-slate-400 flex items-center justify-center gap-3">
            <a href="#" class="hover:text-slate-600 transition">Assistance technique</a>
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
